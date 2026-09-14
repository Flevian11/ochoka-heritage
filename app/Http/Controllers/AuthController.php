<?php

namespace App\Http\Controllers;

use App\Models\OtpChallenge;
use App\Models\User;
use App\Services\AuthOtpService;
use App\Services\JbsSmsService;
use App\Services\PhoneNumberNormalizer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function register(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function storeRegistration(Request $request, AuthOtpService $otpService, JbsSmsService $sms): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if (blank($data['email'] ?? null) && blank($data['phone'] ?? null)) {
            throw ValidationException::withMessages([
                'email' => 'Provide an email address or phone number.',
                'phone' => 'Provide an email address or phone number.',
            ]);
        }

        $phone = null;
        if (! blank($data['phone'] ?? null)) {
            try {
                $phone = PhoneNumberNormalizer::normalize($data['phone']);
            } catch (Throwable) {
                throw ValidationException::withMessages(['phone' => 'Enter a valid international phone number.']);
            }

            if (User::where('phone', $phone)->exists()) {
                throw ValidationException::withMessages(['phone' => 'That phone number is already registered.']);
            }
        }

        $email = blank($data['email'] ?? null) ? null : Str::lower($data['email']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'phone' => $phone,
            'password' => $data['password'],
            'status' => User::STATUS_ACTIVE,
        ]);

        $emailSent = false;
        if ($email !== null) {
            try {
                event(new Registered($user));
                $emailSent = true;
            } catch (Throwable) {
                // Account creation must not be rolled back because an email provider is temporarily unavailable.
            }
        }

        $smsSent = false;
        if ($phone !== null) {
            try {
                if ($email === null) {
                    $otpService->issue($user, OtpChallenge::CHANNEL_SMS, OtpChallenge::PURPOSE_PHONE_VERIFICATION, $request->ip());
                    $request->session()->put('otp_challenge_user_id', $user->id);
                    $request->session()->put('otp_challenge_purpose', OtpChallenge::PURPOSE_PHONE_VERIFICATION);
                    $request->session()->put('otp_channel', OtpChallenge::CHANNEL_SMS);
                } else {
                    $sms->send($phone, 'Welcome to Ochoka Heritage. Your account has been created successfully.');
                }
                $smsSent = true;
            } catch (Throwable) {
                // Registration remains successful; the verification/resend path can be used after login.
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($email !== null) {
            return redirect()->route('verification.notice')->with('success', $emailSent
                ? ($smsSent
                    ? 'Your account has been created. Check your email for verification and your phone for a welcome message.'
                    : 'Your account has been created. Check your email for verification.')
                : ($smsSent
                    ? 'Your account has been created. Email delivery is temporarily unavailable; your phone welcome message was sent.'
                    : 'Your account has been created. Email delivery is temporarily unavailable; you can retry verification later.'));
        }

        return redirect()->route('otp.verify')->with('success', $smsSent
            ? 'Your account has been created. Enter the code sent to your phone.'
            : 'Your account has been created, but the verification code could not be sent. Request a new code.');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $email = Str::lower($credentials['email']);
        $throttleKey = Str::transliterate($email).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages(['email' => 'Too many sign-in attempts. Please try again later.']);
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! Auth::validate(['email' => $email, 'password' => $credentials['password']])) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages(['email' => 'The provided credentials are incorrect.']);
        }

        if (! $user->isLoginAllowed()) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages(['email' => 'This account is not currently allowed to sign in.']);
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, (bool) ($credentials['remember'] ?? false));
        $user->forceFill(['last_login_at' => now()])->save();
        $request->session()->regenerate();

        if (! $user->hasVerifiedContact()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function requestOtp(Request $request, AuthOtpService $otpService): RedirectResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'channel' => ['required', 'in:email,sms'],
        ]);

        $identifier = trim($data['identifier']);
        $channel = $data['channel'];
        $normalized = $channel === OtpChallenge::CHANNEL_SMS
            ? $this->normalizeOtpPhone($identifier)
            : Str::lower($identifier);

        $rateKey = 'otp:'.$channel.':'.Str::transliterate($normalized).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            return redirect()->route('login')->withErrors([
                'identifier' => 'Too many code requests. Please wait before requesting another code.',
            ]);
        }
        RateLimiter::hit($rateKey, (int) config('auth_otp.send_cooldown_seconds', 60));

        $user = $channel === OtpChallenge::CHANNEL_SMS
            ? User::where('phone', $normalized)->first()
            : User::where('email', $normalized)->first();

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'identifier' => $channel === OtpChallenge::CHANNEL_SMS
                    ? 'No account is registered with that phone number. Please create an account first.'
                    : 'No account is registered with that email address. Please create an account first.',
            ]);
        }

        if (! $user->isLoginAllowed()) {
            return redirect()->route('login')->withErrors([
                'identifier' => 'This account is not currently allowed to sign in. Please contact the association administrator.',
            ]);
        }

        if ($channel === OtpChallenge::CHANNEL_EMAIL && blank($user->email)) {
            return redirect()->route('login')->withErrors([
                'identifier' => 'This account does not have an email address configured. Choose Phone / SMS instead.',
            ]);
        }

        if ($channel === OtpChallenge::CHANNEL_SMS && blank($user->phone)) {
            return redirect()->route('login')->withErrors([
                'identifier' => 'This account does not have a phone number configured. Choose Email instead.',
            ]);
        }

        try {
            $otpService->issue($user, $channel, OtpChallenge::PURPOSE_LOGIN, $request->ip());
        } catch (Throwable) {
            return redirect()->route('login')->withErrors([
                'identifier' => 'We could not send the one-time code right now. Please check the contact details and try again.',
            ]);
        }

        $request->session()->put('otp_challenge_user_id', $user->id);
        $request->session()->put('otp_challenge_purpose', OtpChallenge::PURPOSE_LOGIN);
        $request->session()->put('otp_channel', $channel);

        return redirect()->route('otp.verify')->with('success', 'A one-time code has been sent.');
    }

    public function showOtp(Request $request): Response|RedirectResponse
    {
        if (! $request->session()->has('otp_challenge_user_id')) {
            return redirect()->route('login')->withErrors([
                'identifier' => 'Request a one-time code first.',
            ]);
        }

        return Inertia::render('Auth/Otp', [
            'channel' => $request->session()->get('otp_channel'),
        ]);
    }

    public function verifyOtp(Request $request, AuthOtpService $otpService): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $userId = $request->session()->get('otp_challenge_user_id');
        $purpose = $request->session()->get('otp_challenge_purpose', OtpChallenge::PURPOSE_LOGIN);

        $challenge = OtpChallenge::query()
            ->where('user_id', $userId)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $challenge || ! $otpService->verify($challenge, $data['code'])) {
            throw ValidationException::withMessages(['code' => 'The code is invalid, expired, or has reached its attempt limit.']);
        }

        $user = $challenge->user;
        if (! $user->isLoginAllowed()) {
            throw ValidationException::withMessages(['code' => 'This account is not currently allowed to sign in.']);
        }

        if ($challenge->channel === OtpChallenge::CHANNEL_EMAIL) {
            if (! $user->isEmailVerified()) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }
        } else {
            if (! $user->isPhoneVerified()) {
                $user->forceFill(['phone_verified_at' => now()])->save();
            }
        }

        Auth::login($user);
        $user->forceFill(['last_login_at' => now()])->save();
        $request->session()->forget(['otp_challenge_user_id', 'otp_challenge_purpose', 'otp_channel']);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function resendOtp(Request $request, AuthOtpService $otpService): RedirectResponse
    {
        $userId = $request->session()->get('otp_challenge_user_id');
        $purpose = $request->session()->get('otp_challenge_purpose', OtpChallenge::PURPOSE_LOGIN);
        $challenge = OtpChallenge::query()->where('user_id', $userId)->where('purpose', $purpose)->latest('id')->first();

        if (! $challenge || ! $challenge->user->isLoginAllowed()) {
            return back()->with('success', 'If a valid verification request exists, a new code has been sent.');
        }

        $rateKey = 'otp:resend:'.$challenge->user_id.'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            throw ValidationException::withMessages(['code' => 'Too many code requests. Please wait before requesting another code.']);
        }
        RateLimiter::hit($rateKey, (int) config('auth_otp.send_cooldown_seconds', 60));

        try {
            $otpService->issue($challenge->user, $challenge->channel, $purpose, $request->ip());
        } catch (Throwable) {
            // Keep the response generic.
        }

        return back()->with('success', 'If the verification request is valid, a new code has been sent.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function forgotPassword(): Response { return Inertia::render('Auth/ForgotPassword'); }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink(['email' => Str::lower($data['email'])]);
        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }
        return back()->with('success', 'If an account exists for that email, a password reset link has been sent.');
    }

    public function resetPassword(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->string('email')->toString(),
            'token' => $request->string('token')->toString(),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'], 'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);
        $status = Password::reset([
            'token' => $data['token'], 'email' => Str::lower($data['email']),
            'password' => $data['password'], 'password_confirmation' => $request->input('password_confirmation'),
        ], function (User $user, string $password): void {
            $user->forceFill(['password' => $password])->save();
        });
        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }
        return redirect()->route('login')->with('success', 'Your password has been reset. You can now sign in.');
    }

    public function verificationNotice(Request $request): Response
    {
        return Inertia::render('Auth/VerifyEmail', [
            'email' => $request->user()->email,
            'phone' => $request->user()->phone,
            'email_verified' => $request->user()->isEmailVerified(),
            'phone_verified' => $request->user()->isPhoneVerified(),
        ]);
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->email && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
        return back()->with('success', 'If your account requires email verification, a verification email has been sent.');
    }

    public function verifyEmail(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) abort(403);
        if (! URL::hasValidSignature($request)) abort(403);
        if (! $user->hasVerifiedEmail()) $user->markEmailAsVerified();
        return redirect()->route('login')->with('success', 'Your email address has been verified. You can now sign in.');
    }

    private function normalizeOtpPhone(string $phone): string
    {
        try {
            return PhoneNumberNormalizer::normalize($phone);
        } catch (Throwable) {
            throw ValidationException::withMessages(['identifier' => 'Enter a valid international phone number.']);
        }
    }
}
