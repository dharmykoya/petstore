<?php

namespace Tests\Feature\User;

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

    public function test_edit_user_details_success()
    {
        // Create a user
        $user = User::factory()->create();

        // Generate JWT token for the user
        $token = (new JwtService())->createToken($user->toArray());

        $newUserData = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/v1/user/edit', $newUserData);

        // Assert the response status and structure
        $response->assertStatus(200);

        // Assert the user data was updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ]);
    }
    public function test_edit_user_details_unauthorized()
    {
        // Define the new user data
        $newUserData = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ];

        $response = $this->putJson('/api/v1/user/edit', $newUserData);

        // Assert the response status
        $response->assertStatus(401);
    }

    public function test_edit_user_details_user_not_found()
    {
        // Create a user
        $user = User::factory()->create();

        // Generate JWT token for the user
        $token = (new JwtService())->createToken($user->toArray());

        // Define the new user data
        $newUserData = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ];

        // Delete the user to simulate "User not found"
        $user->delete();

        // Make a PUT request to the endpoint with the Bearer token and new user data
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/v1/user/edit', $newUserData);

        // Assert the response status and message
        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'User not found.'
            ]);
    }

    public function test_edit_user_details_server_error()
     {
         // Mock the user service to throw an exception
         $this->mock(UserService::class, function ($mock) {
             $mock->shouldReceive('editUser')
                 ->andThrow(new \Exception('Server Error'));
         });

         // Create a user
         $user = User::factory()->create();

         // Generate JWT token for the user
         $token = (new JwtService())->createToken($user->toArray());

         $newUserData = [
             'first_name' => 'First Name',
             'last_name' => 'Last Name',
         ];

         // Make a PUT request to the endpoint with the Bearer token and new user data
         $response = $this->withHeaders([
             'Authorization' => 'Bearer ' . $token,
         ])->putJson('/api/v1/user/edit', $newUserData);

         // Assert the response status and message
         $response->assertStatus(500)
             ->assertJson([
                 'message' => 'Server Error'
             ]);
     }

    public function test_delete_user_success()
    {
        // Create a user
        $user = User::factory()->create();

        // Generate JWT token for the user
        $token = (new JwtService())->createToken($user->toArray());

        // Make a DELETE request to the endpoint with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/v1/user');

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        // Assert the user was soft deleted
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_delete_user_not_found()
    {
        // Create a user
        $user = User::factory()->create();

        // Generate JWT token for the user
        $token = (new JwtService())->createToken($user->toArray());

        // Delete the user to simulate "User not found"
        $user->delete();

        // Make a DELETE request to the endpoint with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/v1/user');

        // Assert the response status and structure
        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
            ]);
    }

    public function test_delete_user_unauthorized()
    {
        // Make a DELETE request to the endpoint without authentication
        $response = $this->deleteJson('/api/v1/user');

        // Assert the response status
        $response->assertStatus(401);
    }
}
