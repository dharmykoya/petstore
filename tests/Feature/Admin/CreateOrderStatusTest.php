<?php

namespace Tests\Feature\Admin;

use App\Http\Services\JwtService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_order_status()
    {
        // Create an admin user
        $admin = User::factory()->create(['is_admin' => true]);

        $token = (new JwtService())->createToken($admin->toArray());

        $payload = [
            'title' => 'Processing',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/order-status/create', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'created successfully.',
                'data' => [
                    'title' => 'Processing',
                ],
            ]);

        $this->assertDatabaseHas('order_statuses', ['title' => 'Processing']);
    }

   public function test_non_admin_cannot_create_order_status()
    {
        // Create a non-admin user
        $user = User::factory()->create(['is_admin' => false]);

        $token = (new JwtService())->createToken($user->toArray());

        $payload = [
            'title' => 'Processing',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/order-status/create', $payload);

        $response->assertStatus(403)
            ->assertJson([
                'message' => "You don't have permission to operate this route.",
            ]);

        $this->assertDatabaseMissing('order_statuses', ['title' => 'Processing']);
    }

    public function test_unauthenticated_user_cannot_create_order_status()
    {
        $payload = [
            'title' => 'Processing',
        ];

        $response = $this->postJson('/api/v1/order-status/create', $payload);

        $response->assertStatus(401);
    }
}
