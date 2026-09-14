<?php

namespace App\Services;

use App\Models\OtpChallenge;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\OtpCodeNotification;
use RuntimeException;
use Throwable;

class AuthOtpService
{
    public function __construct(private readonly JbsSmsService $sms)
    {
    }

    /** @return array{challenge: OtpChallenge, code: string} */
    public function issue(User $user, string $channel, string $purpose = OtpChallenge::PURPOSE_LOGIN, ?string $ip = null): array
    {
        $destination = $channel === OtpChallenge::CHANNEL_SMS
            ? (string) $user->phone
            : strtolower((string) $user->email);

        if ($destination === '') {
            throw new RuntimeException('The requested contact method is not configured for this account.');
        }

        OtpChallenge::query()
            ->where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $code = (string) random_int(100000, 999999);
        $challenge = OtpChallenge::create([
            'user_id' => $user->id,
            'purpose' => $purpose,
            'channel' => $channel,
            'destination' => $destination,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes((int) config('auth_otp.ttl_minutes', 10)),
            'requested_ip' => $ip,
        ]);

        try {
            $this->deliver($user, $channel, $destination, $code, $purpose);
        } catch (Throwable $exception) {
            $challenge->forceFill(['consumed_at' => now()])->save();
            throw $exception;
        }

        return compact('challenge', 'code');
    }

    public function verify(OtpChallenge $challenge, string $code): bool
    {
        if ($challenge->isConsumed() || $challenge->isExpired() || $challenge->attempts >= (int) config('auth_otp.max_attempts', 5)) {
            return false;
        }

        if (! Hash::check($code, $challenge->code_hash)) {
            $challenge->increment('attempts');
            return false;
        }

        $challenge->forceFill(['consumed_at' => now()])->save();

        return true;
    }

    private function deliver(User $user, string $channel, string $destination, string $code, string $purpose): void
    {
        $message = $purpose === OtpChallenge::PURPOSE_LOGIN
            ? "Your Ochoka Heritage sign-in code is {$code}. It expires in ".config('auth_otp.ttl_minutes', 10).' minutes.'
            : "Your Ochoka Heritage verification code is {$code}. It expires in ".config('auth_otp.ttl_minutes', 10).' minutes.';

        if ($channel === OtpChallenge::CHANNEL_SMS) {
            $this->sms->send($destination, $message);
            return;
        }

        $user->notify(new OtpCodeNotification($code, $purpose));
    }
}
