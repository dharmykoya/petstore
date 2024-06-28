<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Services\AuthService;
use Illuminate\Http\Response;

class AdminAuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="registerUser",
     *      tags={"Authentication"},
     *      summary="Register a new user (Admin)",
     *      description="Registers a new admin user.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User registration details",
     *          @OA\JsonContent(ref="#/components/schemas/RegisterUserRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful registration response",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Admin created successfully, please login to continue.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Server Error")
     *          )
     *      )
     * )
     */
    public function register(RegisterUserRequest $request) {
        try {
            $this->authService->createUser($request->validated(), true);
            return  $this->successResponse("Admin created successfully, please login to continue.", [], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }
}
