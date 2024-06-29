<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="RegisterUserRequest",
 *     type="object",
 *     title="Create User Request",
 *     description="Request payload for creating a new user",
 *     required={"first_name", "last_name", "email", "password", "address", "phone_number"},
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
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email address of the user",
 *         example="john.doe@example.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="Password for the user",
 *         example="password123"
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
 *     )
 * )
 */
class RegisterUserRequest {}
