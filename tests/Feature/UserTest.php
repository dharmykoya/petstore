<?php

namespace Tests\Feature;

use App\Http\Services\JwtService;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_details_success()
    {
        // Create a user
        $user = User::factory()->create();

        // Generate JWT token for the user
        $token = (new JwtService())->createToken($user->toArray());

        // Make a GET request to the endpoint with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user');

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'uuid',
                    'first_name',
                    'last_name',
                    'email',
                    'email_verified_at',
                    'address',
                    'phone_number',
                    'is_marketing',
                ]
            ]);
    }

    public function test_get_user_details_unauthorized()
    {
        $response = $this->getJson('/api/v1/user');

        $response->assertStatus(401);
    }

    public function test_get_user_details_server_error()
    {
        // Mock the user service to throw an exception
        $this->mock(UserService::class, function ($mock) {
            $mock->shouldReceive('getUser')
                ->andThrow(new \Exception('Server Error'));
        });

        // Create a user
        $user = User::factory()->create();

        // Generate JWT token for the user
        $token = (new JwtService())->createToken($user->toArray());

        // Make a GET request to the endpoint with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user');

        // Assert the response status and message
        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Server Error'
            ]);
    }
}
