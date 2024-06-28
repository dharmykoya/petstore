<?php

namespace Tests\Unit\Admin;

use App\Http\Services\OrderStatusService;
use App\Models\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderStatusUnitTest extends TestCase
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

    public function test_edit_status_success()
    {
        $status = OrderStatus::factory()->create([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'title' => 'Processing'
        ]);

        $requestData = ['title' => 'Shipped'];
        $uuid = $status->uuid;

        $service = new OrderStatusService();
        $result = $service->editStatus($requestData, $uuid);

        $this->assertTrue($result['status']);
        $this->assertEquals('Update successful.', $result['message']);
        $this->assertEquals('Shipped', $result['data']->title);
    }

    public function test_edit_status_not_found()
    {
        $requestData = ['title' => 'Shipped'];
        $uuid = 'non-existent-uuid';

        $service = new OrderStatusService();
        $result = $service->editStatus($requestData, $uuid);

        $this->assertFalse($result['status']);
        $this->assertEquals('Status not found.', $result['message']);
    }
}
