<?php

namespace Tests\Feature;

use App\Http\Services\JwtService;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_their_orders()
    {
        $user = User::factory()->create();
        OrderStatus::factory()->create();
        Order::factory()->count(5)->create(['user_id' => $user->id]);

        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
            ->get('/api/v1/user/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'order_status_id',
                        'payment_id',
                        'uuid',
                        'products',
                        'address',
                        'delivery_fee',
                        'amount',
                        'shipped_at',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'status',
                'message'
            ]);
    }

   public function test_user_can_paginate_their_orders()
    {
        $user = User::factory()->create();
        OrderStatus::factory()->create();
        Order::factory()->count(50)->create(['user_id' => $user->id]);

        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
            ->get('/api/v1/user/orders?page=2&limit=10');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'orders fetched successfully.'
            ])
            ->assertJsonCount(10, 'data');
    }

    public function test_user_can_sort_their_orders()
    {
        $user = User::factory()->create();
        OrderStatus::factory()->create();
        Order::factory()->count(5)->create(['user_id' => $user->id]);

        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
            ->get('/api/v1/user/orders?sort_by=created_at&desc=false');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'orders fetched successfully.'
            ]);
    }
}
