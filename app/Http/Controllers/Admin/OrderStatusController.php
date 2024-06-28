<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateOrderStatusRequest;
use App\Http\Resources\Admin\OrderStatusResource;
use App\Http\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderStatusController extends Controller
{
    protected OrderStatusService $orderStatusService;

    public function __construct(OrderStatusService $orderStatusService) {
        $this->orderStatusService = $orderStatusService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/order-status/create",
     *     tags={"Order Status"},
     *     summary="Create a new order status",
     *     description="This endpoint allows you to create a new order status.",
     *     operationId="createOrderStatus",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateOrderStatusRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order status created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderStatusResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Server Error"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function create(CreateOrderStatusRequest $request) {
        try {
            $status = $this->orderStatusService->createStatus($request->validated());
            return  $this->successResponse("created successfully.", new OrderStatusResource($status), Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }


}
