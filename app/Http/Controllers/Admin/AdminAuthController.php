<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Resources\Admin\AdminLoginResource;
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
     *      path="/api/v1/admin/create",
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

    /**
     * @OA\Post(
     *     path="/api/v1/admin/login",
     *     summary="Login",
     *     description="Authenticate a user and return a JWT token.",
     *     operationId="login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="login successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="login successful."),
     *             @OA\Property(property="data", ref="#/components/schemas/AdminLoginResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Email and/or Password does not match.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function login(LoginRequest $request) {
        try {
            $user = $this->authService->loginUser($request->validated());
            if (!$user['status']) {
                return $this->failedResponse($user['message']);
            }
            return  $this->successResponse("login successful.", new AdminLoginResource($user['data']));
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }
}
