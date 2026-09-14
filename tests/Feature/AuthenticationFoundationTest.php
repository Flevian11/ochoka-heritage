<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserIdentity;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_without_becoming_a_member(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'New Account',
            'email' => 'new@example.com',
            'password' => 'correct-horse-battery-staple',
            'password_confirmation' => 'correct-horse-battery-staple',
        ]);

        $user = User::where('email', 'new@example.com')->firstOrFail();

        $response->assertRedirect('/email/verify');
        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->member);
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_can_sign_in_but_is_sent_to_verification(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/email/verify');

        $this->assertAuthenticatedAs($user);
    }

    public function test_unverified_user_cannot_access_the_admin_portal(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get('/admin')->assertRedirect('/email/verify');
    }

    public function test_suspended_user_cannot_sign_in(): void
    {
        $user = User::factory()->create([
            'status' => User::STATUS_SUSPENDED,
            'password' => Hash::make('password'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_verified_active_user_can_sign_in_and_login_timestamp_is_recorded(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->create([
            'organization_id' => $organization->id,
        ]);
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $member->update(['user_id' => $user->id]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_verification_link_marks_email_verified(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(10),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())],
        );

        $this->actingAs($user)->get($url)->assertRedirect('/login');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_password_reset_can_update_password(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-correct-password',
            'password_confirmation' => 'new-correct-password',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('new-correct-password', $user->fresh()->password));
    }

    public function test_user_identity_can_store_a_future_external_provider_identity(): void
    {
        $user = User::factory()->create();

        $identity = $user->identities()->create([
            'provider' => 'github',
            'provider_user_id' => 'github-123',
            'provider_email' => $user->email,
            'provider_name' => $user->name,
            'metadata' => ['future_login' => true],
        ]);

        $this->assertInstanceOf(UserIdentity::class, $identity);
        $this->assertSame('github', $identity->provider);
        $this->assertSame($user->id, $identity->user_id);
    }
}
