<?php

namespace Tests\Unit\Admin;

use App\Http\Services\OrderStatusService;
use App\Models\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateOrderStatusUnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_status()
    {
        $service = new OrderStatusService();

        $requestData = [
            'title' => 'Processing',
        ];

        $status = $service->createStatus($requestData);

        $this->assertInstanceOf(OrderStatus::class, $status);
        $this->assertEquals('Processing', $status->title);
        $this->assertDatabaseHas('order_statuses', ['title' => 'Processing']);
    }
}
