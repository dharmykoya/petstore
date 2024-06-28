<?php

namespace Tests\Feature\Admin;

use App\Http\Services\JwtService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_registration()
    {
        // Create a mock user with admin privileges
        $adminUser = User::factory()->create(['is_admin' => true]);
        $token = (new JwtService())->createToken($adminUser->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/admin/create', [
            'first_name' => fake()->firstNameMale(),
            'last_name' => fake()->lastName(),
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => 'lagos, Nigeria',
            'phone_number' => '08037145164',
        ]);

        // Assert HTTP status code
        $response->assertStatus(Response::HTTP_CREATED);

        // Assert response structure
        $response->assertJson([
            'status' => true,
            'message' => 'Admin created successfully, please login to continue.'
        ]);

        // Optionally, assert the database state
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'is_admin' => true,
        ]);
    }

    public function test_unauthorized_admin_registration()
    {
        // regular user without admin privileges
        $regularUser = User::factory()->create(['is_admin' => false]);
        $token = (new JwtService())->createToken($regularUser->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/admin/create', [
            'first_name' => fake()->firstNameMale(),
            'last_name' => fake()->lastName(),
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => 'lagos, Nigeria',
            'phone_number' => '08037145164',
        ]);

        // Assert HTTP status code 403 (Forbidden) due to middleware
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        // Assert error message returned by the middleware
        $response->assertJson([
            'message' => "You don't have permission to operate this route."
        ]);
    }
}
