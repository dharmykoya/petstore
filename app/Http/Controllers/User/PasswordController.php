<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordResetLinkRequest;
use App\Http\Services\AuthService;

class PasswordController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/user/forgot-password",
     *     summary="Send password reset link",
     *     description="Send a password reset link to the user's email.",
     *     operationId="sendPasswordResetLink",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="The email address to send the password reset link")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reset link sent to your email.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error message.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error message.")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function sendPasswordResetLink(SendPasswordResetLinkRequest $request)
    {
        try {
            $this->authService->sendPasswordResetLink($request->validated());
            return $this->successResponse('Reset link sent to your email.');
        }  catch (\Exception $exception ) {
            return $this->serverErrorResponse('Server error', exception:$exception );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/reset-password-token",
     *     summary="Reset password",
     *     description="Reset user's password using a password reset token.",
     *     operationId="resetPassword",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error message.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error message.")
     *         )
     *     )
     * )
     */
    public function resetPassword(ResetPasswordRequest $request) {

        try {
            $resetPassword = $this->authService->resetPassword($request->validated());
            if (!$resetPassword['status']) {
                return $this->failedResponse($resetPassword['message']);
            }
            return $this->successResponse($resetPassword['message']);
        }  catch (\Exception $exception ) {
            return $this->serverErrorResponse('Server error', exception:$exception );
        }
    }
}
