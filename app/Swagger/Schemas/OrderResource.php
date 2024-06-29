<?php


namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     type="object",
 *     title="Order",
 *     description="Order details",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="The unique identifier of the order"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         example=1,
 *         description="The unique identifier of the user"
 *     ),
 *     @OA\Property(
 *         property="order_status_id",
 *         type="integer",
 *         example=1,
 *         description="The unique identifier of the order status"
 *     ),
 *     @OA\Property(
 *         property="payment_id",
 *         type="integer",
 *         nullable=true,
 *         example=1,
 *         description="The unique identifier of the payment"
 *     ),
 *     @OA\Property(
 *         property="uuid",
 *         type="string",
 *         format="uuid",
 *         example="123e4567-e89b-12d3-a456-426614174000",
 *         description="The UUID of the order"
 *     ),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(type="object"),
 *         description="The products associated with the order"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="object",
 *         description="The address associated with the order"
 *     ),
 *     @OA\Property(
 *         property="delivery_fee",
 *         type="number",
 *         format="float",
 *         example=5.99,
 *         description="The delivery fee for the order"
 *     ),
 *     @OA\Property(
 *         property="amount",
 *         type="number",
 *         format="float",
 *         example=100.99,
 *         description="The total amount for the order"
 *     ),
 *     @OA\Property(
 *         property="shipped_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         example="2024-07-01T12:00:00Z",
 *         description="The timestamp when the order was shipped"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-06-28T12:00:00Z",
 *         description="The timestamp when the order was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-06-28T12:00:00Z",
 *         description="The timestamp when the order was last updated"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         example="2024-07-01T12:00:00Z",
 *         description="The timestamp when the order was deleted, if applicable"
 *     )
 * )
 */
class OrderResource {
}
