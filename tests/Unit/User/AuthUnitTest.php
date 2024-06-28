<?php

namespace Tests\Unit\User;

use App\Http\Services\AuthService;
use App\Http\Services\JwtService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class AuthUnitTest extends TestCase
{
    use RefreshDatabase;
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_user()
    {
        $jwtService = Mockery::mock(JwtService::class);
        $authService = new AuthService($jwtService);

        $requestData = [
            'first_name' => 'John Doe',
            'last_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'address' => 'lagos, Nigeria',
            'phone_number' => '08037145164',
        ];
        $createdUser = $authService->createUser($requestData);

        $this->assertInstanceOf(User::class, $createdUser);
        $this->assertEquals($requestData['email'], $createdUser->email);
    }

    public function test_login_user_with_invalid_credentials()
    {
        $jwtService = Mockery::mock(JwtService::class);
        $authService = new AuthService($jwtService);

        $requestData = [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $authService->loginUser($requestData);

        $this->assertFalse($response['status']);
        $this->assertEquals('Email and/or Password does not match.', $response['message']);
    }

    public function testLogout()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')
            ->andReturn('mocked_token');

        DB::shouldReceive('table')
            ->once()
            ->with('token_blacklists')
            ->andReturnSelf();
        DB::shouldReceive('insert')
            ->once()
            ->with(['token' => 'mocked_token']);

        $jwtService = Mockery::mock(JwtService::class);
        $authService = new AuthService($jwtService);
        $authService->logout($request);
    }
}
