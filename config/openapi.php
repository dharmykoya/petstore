<?php

/**
 * @OA\Info(
 *     title="My API",
 *     version="1.0.0",
 *     description="This is a sample API documentation for my Laravel application."
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"first_name", "last_name", "email", "password", "address", "phone_number"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string", format="password"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="phone_number", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
