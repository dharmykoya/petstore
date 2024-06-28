<?php

namespace Tests\Feature\Admin;

use App\Http\Services\JwtService;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class OrderStatusTest extends TestCase
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

    public function test_admin_can_edit_status()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $status = OrderStatus::factory()->create([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'title' => 'Processing'
        ]);

        $token = (new JwtService())->createToken($admin->toArray());


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/v1/order-status/{$status->uuid}", ['title' => 'Shipped']);


        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Status edited successfully',
                'data' => [
                    'uuid' => '123e4567-e89b-12d3-a456-426614174000',
                    'title' => 'Shipped'
                ]
            ]);

        $this->assertDatabaseHas('order_statuses', [
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'title' => 'Shipped'
        ]);
    }

    public function test_non_admin_cannot_edit_status()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $token = (new JwtService())->createToken($user->toArray());

        $status = OrderStatus::factory()->create([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'title' => 'Processing'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/v1/order-status/{$status->uuid}", ['title' => 'Shipped']);

        $response->assertStatus(403)
            ->assertJson([
                'message' => "You don't have permission to operate this route."
            ]);

        $this->assertDatabaseHas('order_statuses', [
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'title' => 'Processing'
        ]);
    }

    public function test_admin_can_delete_status()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $token = (new JwtService())->createToken($admin->toArray());

        $status = OrderStatus::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/order-status/{$status->uuid}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Status deleted successfully.'
            ]);

        $this->assertDatabaseMissing('order_statuses', ['uuid' => $status->uuid]);
    }

    public function test_non_admin_cannot_delete_status()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $token = (new JwtService())->createToken($user->toArray());

        $status = OrderStatus::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/order-status/{$status->uuid}");

        $response->assertStatus(403)
            ->assertJson([
                'message' => "You don't have permission to operate this route."
            ]);

        $this->assertDatabaseHas('order_statuses', ['uuid' => $status->uuid]);
    }

    public function test_admin_cannot_delete_non_existing_status()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $token = (new JwtService())->createToken($admin->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/order-status/non-existing-uuid");

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Status not found.'
            ]);
    }

    public function test_admin_cannot_delete_status_with_orders()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $token = (new JwtService())->createToken($admin->toArray());

        $status = OrderStatus::factory()->create();
        Order::factory()->create(['order_status_id' => $status->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/order-status/{$status->uuid}");

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'There are orders attached to this status.'
            ]);

        $this->assertDatabaseHas('order_statuses', ['uuid' => $status->uuid]);
    }

    public function test_get_status_as_admin()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $orderStatus = OrderStatus::factory()->create();

        $token = (new JwtService())->createToken($admin->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/order-status/{$orderStatus->uuid}");


        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'data' => [
                    'uuid',
                    'title'
                ]
            ]);
    }

    public function test_get_status_as_non_admin()
    {
        // Create a mock non-admin user
        $user = User::factory()->create(['is_admin' => false]);

        // Create a mock order status
        $orderStatus = OrderStatus::factory()->create();

        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/order-status/{$orderStatus->uuid}");

        $response->assertForbidden();
        $response->assertStatus(403)
            ->assertJson([
                'message' => "You don't have permission to operate this route."
            ]);
    }

    public function test_get_all_statuses()
    {
        $user = User::factory()->create(['is_admin' => true]);

        OrderStatus::factory()->count(20)->create();
        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/order-statuses?limit=10");

        // Assert HTTP status and response structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'uuid',
                        'title',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta'
            ]);

        $response->assertJsonCount(10, 'data');
    }

    public function test_get_all_statuses_with_pagination()
    {
        $user = User::factory()->create(['is_admin' => true]);
        OrderStatus::factory()->count(30)->create();

        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/order-statuses?limit=10&page=2");

        // Assert HTTP status and response structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'uuid',
                        'title',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta'
            ]);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data')
                ->has('links')
                ->has('meta.per_page')
                ->has('status')
                ->has('meta')
                ->has('message');
        });
    }

    public function test_unauthorized_access_to_statuses_endpoint()
    {
        $response = $this->getJson("/api/v1/order-statuses?limit=10&page=2");

        $response->assertStatus(401);
    }
}