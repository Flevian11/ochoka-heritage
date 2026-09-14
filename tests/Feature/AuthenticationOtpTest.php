<?php

namespace Tests\Feature;

use App\Models\OtpChallenge;
use App\Models\User;
use App\Services\JbsSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_accepts_phone_and_sends_sms_verification_code(): void
    {
        $this->mock(JbsSmsService::class, function ($mock): void {
            $mock->shouldReceive('send')
                ->once()
                ->with('+254712345678', \Mockery::on(fn (string $message): bool => preg_match('/\b\d{6}\b/', $message) === 1));
        });

        $response = $this->post('/register', [
            'name' => 'Phone Account',
            'email' => '',
            'phone' => '0712345678',
            'password' => 'correct-horse-battery-staple',
            'password_confirmation' => 'correct-horse-battery-staple',
        ]);

        $user = User::where('phone', '+254712345678')->firstOrFail();

        $response->assertRedirect('/login/otp');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('otp_challenges', [
            'user_id' => $user->id,
            'purpose' => OtpChallenge::PURPOSE_PHONE_VERIFICATION,
            'channel' => OtpChallenge::CHANNEL_SMS,
            'destination' => '+254712345678',
        ]);
    }

    public function test_otp_login_can_be_requested_by_email(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'otp@example.com']);

        $this->post('/login/otp', [
            'identifier' => 'otp@example.com',
            'channel' => 'email',
        ])->assertRedirect('/login/otp');

        $this->assertGuest();
        $this->assertDatabaseHas('otp_challenges', [
            'user_id' => $user->id,
            'purpose' => OtpChallenge::PURPOSE_LOGIN,
            'channel' => OtpChallenge::CHANNEL_EMAIL,
            'destination' => 'otp@example.com',
        ]);
        Notification::assertSentTo($user, \App\Notifications\OtpCodeNotification::class);
    }

    public function test_otp_login_can_be_verified_and_marks_contact_verified(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'verify-otp@example.com',
        ]);

        $code = '123456';
        $challenge = OtpChallenge::create([
            'user_id' => $user->id,
            'purpose' => OtpChallenge::PURPOSE_LOGIN,
            'channel' => OtpChallenge::CHANNEL_EMAIL,
            'destination' => $user->email,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->withSession([
            'otp_challenge_user_id' => $user->id,
            'otp_challenge_purpose' => OtpChallenge::PURPOSE_LOGIN,
            'otp_channel' => OtpChallenge::CHANNEL_EMAIL,
        ])->post('/login/otp/verify', ['code' => $code])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNotNull($challenge->fresh()->consumed_at);
    }

    public function test_otp_login_by_phone_can_verify_and_mark_phone_verified(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => null,
            'phone' => '+254712345679',
        ]);

        $code = '654321';
        $challenge = OtpChallenge::create([
            'user_id' => $user->id,
            'purpose' => OtpChallenge::PURPOSE_LOGIN,
            'channel' => OtpChallenge::CHANNEL_SMS,
            'destination' => $user->phone,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->withSession([
            'otp_challenge_user_id' => $user->id,
            'otp_challenge_purpose' => OtpChallenge::PURPOSE_LOGIN,
            'otp_channel' => OtpChallenge::CHANNEL_SMS,
        ])->post('/login/otp/verify', ['code' => $code])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->phone_verified_at);
        $this->assertNotNull($challenge->fresh()->consumed_at);
    }

    public function test_otp_rejects_invalid_code_and_increments_attempts(): void
    {
        $user = User::factory()->create(['email' => 'invalid-otp@example.com']);
        $challenge = OtpChallenge::create([
            'user_id' => $user->id,
            'purpose' => OtpChallenge::PURPOSE_LOGIN,
            'channel' => OtpChallenge::CHANNEL_EMAIL,
            'destination' => $user->email,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->withSession([
            'otp_challenge_user_id' => $user->id,
            'otp_challenge_purpose' => OtpChallenge::PURPOSE_LOGIN,
        ])->post('/login/otp/verify', ['code' => '000000'])->assertSessionHasErrors('code');

        $this->assertSame(1, $challenge->fresh()->attempts);
        $this->assertGuest();
    }
    public function test_otp_login_does_not_redirect_to_code_page_for_an_unregistered_email(): void
    {
        Notification::fake();

        $this->post('/login/otp', [
            'identifier' => 'missing@example.com',
            'channel' => 'email',
        ])->assertSessionHasErrors(['identifier' => 'No account is registered with that email address. Please create an account first.'])
          ->assertRedirect('/login');

        $this->assertGuest();
        $this->assertDatabaseCount('otp_challenges', 0);
    }

    public function test_otp_page_cannot_be_opened_without_a_pending_challenge(): void
    {
        $this->get('/login/otp')
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['identifier' => 'Request a one-time code first.']);
    }

    public function test_otp_login_rejects_an_unregistered_phone(): void
    {
        $this->post('/login/otp', [
            'identifier' => '+254700000001',
            'channel' => 'sms',
        ])->assertSessionHasErrors(['identifier' => 'No account is registered with that phone number. Please create an account first.'])
          ->assertRedirect('/login');

        $this->assertDatabaseCount('otp_challenges', 0);
        $this->assertGuest();
    }

    public function test_otp_login_finds_registered_email_case_insensitively(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'yalitechsystems@gmail.com']);

        $this->post('/login/otp', [
            'identifier' => 'YALITECHSYSTEMS@GMAIL.COM',
            'channel' => 'email',
        ])->assertRedirect('/login/otp');

        $this->assertDatabaseHas('otp_challenges', [
            'user_id' => $user->id,
            'destination' => 'yalitechsystems@gmail.com',
            'channel' => OtpChallenge::CHANNEL_EMAIL,
        ]);
    }

}
