<?php


namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     title="Login Request",
 *     description="Request payload for login",
 *     required={"email", "password"},
 *
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
 *     )
 * )
 */
class LoginRequest {
}
