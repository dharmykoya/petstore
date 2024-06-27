<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user()
    {
        $response = $this->postJson('/api/v1/user/create', [
            'first_name' => fake()->firstNameMale(),
            'last_name' => fake()->lastName(),
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => 'lagos, Nigeria',
            'phone_number' => '08037145164',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
            ]);
    }

    public function test_login()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'address' => 'lagos, Nigeria',
            'phone_number' => '08037145164',
        ]);

        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

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
                    'token',
                ],
            ]);
    }

    public function test_invalid_credentials_for_creating_user() {
        $response = $this->postJson('/api/v1/user/create', [
            'first_name' => fake()->firstNameMale(),
            'last_name' => fake()->lastName(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone_number' => '08037145164',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    "email",
                    "address"
                ],
            ]);
    }

    public function test_invalid_login_credentials() {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'address' => 'lagos, Nigeria',
            'phone_number' => '08037145164',
        ]);

        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'john@example.com',
            'password' => 'password2',
        ]);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'status',
                'message',
            ]);
    }
}
