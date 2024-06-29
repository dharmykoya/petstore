<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     title="User Response",
 *     description="User data response",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the user",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="uuid",
 *         type="string",
 *         format="uuid",
 *         description="UUID of the user",
 *         example="550e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="first_name",
 *         type="string",
 *         description="First name of the user",
 *         example="John"
 *     ),
 *     @OA\Property(
 *         property="last_name",
 *         type="string",
 *         description="Last name of the user",
 *         example="Doe"
 *     ),
 *     @OA\Property(
 *         property="is_admin",
 *         type="boolean",
 *         description="Indicates if the user is an admin",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email address of the user",
 *         example="john.doe@example.com"
 *     ),
 *     @OA\Property(
 *         property="email_verified_at",
 *         type="string",
 *         format="date-time",
 *         description="Email verified timestamp",
 *         example="2024-06-29T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="avatar",
 *         type="string",
 *         description="Avatar URL of the user",
 *         example="https://example.com/avatar.jpg"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Address of the user",
 *         example="123 Main St"
 *     ),
 *     @OA\Property(
 *         property="phone_number",
 *         type="string",
 *         description="Phone number of the user",
 *         example="123-456-7890"
 *     ),
 *     @OA\Property(
 *         property="is_marketing",
 *         type="boolean",
 *         description="Indicates if the user has opted in for marketing emails",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="last_login_at",
 *         type="string",
 *         format="date-time",
 *         description="Last login timestamp",
 *         example="2024-06-29T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp",
 *         example="2024-06-29T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Update timestamp",
 *         example="2024-06-29T12:00:00Z"
 *     )
 * )
 */
class UserResource {}
