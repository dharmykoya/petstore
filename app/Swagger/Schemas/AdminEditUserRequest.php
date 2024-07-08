<?php
namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="AdminEditUserRequest",
 *     type="object",
 *     title="Update User Request",
 *     description="Request object for updating a user",
 *     @OA\Property(
 *         property="first_name",
 *         type="string",
 *         description="First name of the user",
 *         example="John",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="last_name",
 *         type="string",
 *         description="Last name of the user",
 *         example="Doe",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Address of the user",
 *         example="123 Main St",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email address of the user",
 *         example="john.doe@example.com",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="phone_number",
 *         type="string",
 *         description="Phone number of the user",
 *         example="123-456-7890",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="is_marketing",
 *         type="boolean",
 *         description="Indicates if the user has opted in for marketing emails",
 *         example=false,
 *         nullable=true
 *     )
 * )
 */
class AdminEditUserRequest {}
