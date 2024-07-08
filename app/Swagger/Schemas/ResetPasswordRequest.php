<?php

namespace App\Swagger\Schemas;
/**
 * @OA\Schema(
 *     schema="ResetPasswordRequest",
 *     title="Reset Password Request",
 *     required={"token", "email", "password"},
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         description="Token received for password reset",
 *         example="your-reset-token"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="User's email address",
 *         example="user@example.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="New password for the user",
 *         example="newPassword123"
 *     ),
 *     @OA\Property(
 *         property="password_confirmation",
 *         type="string",
 *         format="password",
 *         description="Confirmation of the new password",
 *         example="newPassword123"
 *     )
 * )
 */

class ResetPasswordRequest {}
