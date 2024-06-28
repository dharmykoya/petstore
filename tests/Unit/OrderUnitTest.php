<?php

namespace Tests\Unit;

use App\Http\Services\OrderService;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class OrderUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_orders_with_pagination()
    {
        $user = User::factory()->create();
        OrderStatus::factory()->create();
        Order::factory()->count(50)->create(['user_id' => $user->id]);

        Auth::shouldReceive('user')->andReturn($user);
        $request = Request::create('/api/v1/user/orders', 'GET', [
            'page' => 2,
            'limit' => 10
        ]);

        $service = new OrderService();
        $orders = $service->getOrders($request);

        $this->assertEquals(10, $orders->count());
        $this->assertEquals(2, $orders->currentPage());
    }

    public function test_get_orders_with_default_values()
    {
        $user = User::factory()->create();
        OrderStatus::factory()->create();
        Order::factory()->count(5)->create(['user_id' => $user->id]);

        Auth::shouldReceive('user')->andReturn($user);
        $request = Request::create('/api/v1/user/orders', 'GET');

        $service = new OrderService();
        $orders = $service->getOrders($request);

        $this->assertEquals(5, $orders->count());
        $this->assertEquals(1, $orders->currentPage());
    }
}
