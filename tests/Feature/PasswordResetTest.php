<?php

namespace Tests\Feature;

use App\Http\Services\JwtService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;
use App\Http\Services\AuthService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup if needed
    }

    public function test_send_password_reset_link()
    {
        Mail::fake();
        $user = User::factory()->create();

        // Mock the URL facade
        URL::shouldReceive('to')->andReturn('https://example.com/reset-password');

        // Mock the Mail send method
        Mail::shouldReceive('send')->once()->andReturnUsing(function ($view, $data) use ($user) {
            $this->assertEquals('emails.userResetPassword', $view);
            $this->assertArrayHasKey('link', $data);
            $this->assertStringContainsString('https://example.com/reset-password', $data['link']);
        });

        $jwtService = Mockery::mock(JwtService::class);
        $authService = new AuthService($jwtService);
        $response = $authService->sendPasswordResetLink(['email' => $user->email]);

        // Assert the response
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Reset link sent to your email.', $response['message']);
    }

    public function test_reset_password()
    {
        // Create a user and insert a password reset token into the database
        $user = User::factory()->create();
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Simulate the reset password request
        $response = $this->postJson('/api/v1/user/reset-password-token', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Password has been reset successfully.',
            ]);

        // Assert the password has been updated in the database
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));

        // Assert the reset token has been deleted from the database
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_send_password_reset_link_user_not_found()
    {
        Mail::fake();

        $jwtService = Mockery::mock(JwtService::class);
        $authService = new AuthService($jwtService);
        $response = $authService->sendPasswordResetLink(['email' => 'nonexistent@example.com']);

        // Assert the response
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Reset link sent to your email.', $response['message']);
    }

    public function test_send_password_reset_link_mail_failure()
    {
        Mail::fake();
        $user = User::factory()->create();
        URL::shouldReceive('to')->andReturn('https://example.com/reset');

        // Mock the Mail send method to fail
        Mail::shouldReceive('send')->once()->andThrow(new \Exception('Mail sending failed'));

        $jwtService = Mockery::mock(JwtService::class);
        $authService = new AuthService($jwtService);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Mail sending failed');

        $authService->sendPasswordResetLink(['email' => $user->email]);
    }

    public function test_reset_password_invalid_token()
    {
        $user = User::factory()->create();
        $invalidToken = Str::random(60);

        $response = $this->postJson('/api/v1/user/reset-password-token', [
            'email' => $user->email,
            'token' => $invalidToken,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Invalid token.',
            ]);
    }

    public function test_reset_password_expired_token()
    {
        $user = User::factory()->create();
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()->subHours(2), // Set the token creation time to 2 hours ago
        ]);

        $response = $this->postJson('/api/v1/user/reset-password-token', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Invalid token or expired token.',
            ]);
    }

    public function test_reset_password_user_not_found()
    {
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => 'nonexistent@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/user/reset-password-token', [
            'email' => 'nonexistent@example.com',
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);


        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Invalid token.',
            ]);
    }
}
