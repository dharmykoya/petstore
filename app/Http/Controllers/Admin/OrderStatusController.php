<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateOrderStatusRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Resources\Admin\OrderStatusResource;
use App\Http\Services\OrderStatusService;
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


    /**
     * @OA\Put(
     *     path="/api/v1/order-status/{uuid}",
     *     tags={"Order Status"},
     *     summary="Edit an existing order status",
     *     description="Allows an admin to edit an existing order status.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the order status to be edited",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Shipped")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status edited successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status edited successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="uuid", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *                 @OA\Property(property="title", type="string", example="Shipped"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You don't have permission to operate this route.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order status not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     )
     * )
     */
    public function editStatus(UpdateOrderStatusRequest $request, $uuid) {
        try {
            $status = $this->orderStatusService->editStatus($request->validated(), $uuid);
            if (!$status['status']) {
                return $this->failedResponse($status['message']);
            }
            return  $this->successResponse("Status edited successfully", new OrderStatusResource($status['data']));
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/order-status/{uuid}",
     *     summary="Delete an order status",
     *     description="Deletes an order status. Only accessible by admin users.",
     *     tags={"Order Status"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID of the order status to delete",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You don't have permission to operate this route.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Server Error"),
     *             @OA\Property(property="error", type="string", example="Exception message")
     *         )
     *     )
     * )
     */
    public function deleteStatus($uuid) {
        try {
            $status = $this->orderStatusService->deleteStatus($uuid);
            if (!$status['status']) {
                return $this->failedResponse($status['message']);
            }
            return  $this->successResponse("Status deleted successfully.");
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/order-status/{uuid}",
     *     summary="Get order status by UUID",
     *     tags={"Order Status"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the order status to retrieve",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/OrderStatusResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order status not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order status not found."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Server Error"
     *             )
     *         )
     *     )
     * )
     */
    public function getStatus($uuid) {
        try {
            $status = $this->orderStatusService->getStatus($uuid);
            if (!$status['status']) {
                return $this->failedResponse($status['message'], Response::HTTP_NOT_FOUND);
            }
            return  $this->successResponse("", new OrderStatusResource($status['data']));
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }
}
