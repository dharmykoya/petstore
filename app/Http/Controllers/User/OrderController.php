<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\OrderResource;
use App\Http\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService) {
        $this->orderService = $orderService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user/orders",
     *     summary="Get User Orders",
     *     description="Fetches the orders of the authenticated user with pagination, sorting, and filtering options.",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", example="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         description="Sort in descending order if true",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orders fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="orders fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/OrderResource"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     )
     * )
     */
    public function getUserOrders(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection {
        try {
            $orders = $this->orderService->getOrders($request);
            return OrderResource::collection($orders)->additional([
                'status' => true,
                'message' => 'orders fetched successfully.'
            ]);
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }
}
