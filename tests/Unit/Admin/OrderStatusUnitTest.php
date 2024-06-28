<?php

namespace Tests\Unit\Admin;

use App\Http\Services\OrderStatusService;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class OrderStatusUnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $orderStatusService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderStatusService = new OrderStatusService();
    }
    public function test_create_status()
    {
        $requestData = [
            'title' => 'Processing',
        ];

        $status = $this->orderStatusService->createStatus($requestData);

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

        $result = $this->orderStatusService->editStatus($requestData, $uuid);

        $this->assertTrue($result['status']);
        $this->assertEquals('Update successful.', $result['message']);
        $this->assertEquals('Shipped', $result['data']->title);
    }

    public function test_edit_status_not_found()
    {
        $requestData = ['title' => 'Shipped'];
        $uuid = 'non-existent-uuid';

        $result = $this->orderStatusService->editStatus($requestData, $uuid);

        $this->assertFalse($result['status']);
        $this->assertEquals('Status not found.', $result['message']);
    }

    public function test_delete_status_successful()
    {
        $status = OrderStatus::factory()->create();

        $result = $this->orderStatusService->deleteStatus($status->uuid);

        $this->assertTrue($result['status']);
        $this->assertDatabaseMissing('order_statuses', ['uuid' => $status->uuid]);
    }

    public function test_delete_non_existing_status()
    {
        $result = $this->orderStatusService->deleteStatus('non-existing-uuid');

        $this->assertFalse($result['status']);
    }

    public function test_delete_status_with_orders()
    {
        User::factory()->create();
        $orderStatus = OrderStatus::factory()->create();
        Order::factory()->create(['order_status_id' => $orderStatus->id]);

        $result = $this->orderStatusService->deleteStatus($orderStatus->uuid);

        $this->assertFalse($result['status']);
        $this->assertEquals('There are orders attached to this status.', $result['message']);
        $this->assertDatabaseHas('order_statuses', ['uuid' => $orderStatus->uuid]);
    }
    public function test_get_status_existing()
    {
        $orderStatus = OrderStatus::factory()->create();

        $result = $this->orderStatusService->getStatus($orderStatus->uuid);

        $this->assertTrue($result['status']);
        $this->assertEquals($orderStatus->uuid, $result['data']->uuid); // Adjust as per your model fields
    }

    public function test_get_status_not_found()
    {
        $result = $this->orderStatusService->getStatus('non_existing_uuid');

        $this->assertFalse($result['status']);
        $this->assertEquals('Status not found.', $result['message']);
    }


    public function test_get_all_statuses_without_filters()
    {
        OrderStatus::factory()->count(10)->create();

        $request = new Request();

        // Call the service method
        $statuses = $this->orderStatusService->getAllStatus($request);

        // Assertions
        $this->assertEquals(15, $statuses->perPage()); // Default limit is 15
        $this->assertEquals(1, $statuses->currentPage()); // Default page is 1
        $this->assertCount(10, $statuses); // Should retrieve all 10 statuses
    }

    public function test_get_all_statuses_with_title_filter()
    {
        OrderStatus::factory()->create(['title' => 'In Progress']);
        OrderStatus::factory()->create(['title' => 'Completed']);

        $request = new Request(['title' => 'Progress']);

        // Call the service method
        $statuses = $this->orderStatusService->getAllStatus($request);

        // Assertions
        $this->assertCount(1, $statuses); // Should retrieve only 1 status matching 'Progress'
        $this->assertEquals('In Progress', $statuses->first()->title);
    }
}
