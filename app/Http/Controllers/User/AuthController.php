<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Services\AuthService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;


/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel API Documentation",
 *      description="Swagger OpenAPI description for Laravel API",
 *      @OA\Contact(
 *          email="admin@example.com"
 *      ),
 * )
 */

/**
 * @OA\Tag(
 *     name="User",
 *     description="API Endpoints of User"
 * )
 */
class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/create",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Create a new user account with the provided details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation","address","phone_number"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="address", type="string", example="123 Main St"),
     *             @OA\Property(property="phone_number", type="string", example="1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User created successfully, please login to continue.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully, please login to continue.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     )
     * )
     */
    public function register(RegisterUserRequest $request) {
        try {
            $this->authService->createUser($request->validated());
            return  $this->successResponse("User created successfully, please login to continue.", [], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/login",
     *     tags={"User"},
     *     summary="Login user",
     *     description="Returns user data",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function login(LoginRequest $request) {
        try {
            $user = $this->authService->loginUser($request->validated());
            if (!$user['status']) {
                return $this->failedResponse($user['message']);
            }
            return  $this->successResponse("login successful.", new UserResource($user['data']));
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/logout",
     *     tags={"Auth"},
     *     summary="Logout the current user",
     *     description="Invalidate the current user's JWT token.",
     *     operationId="logout",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         description="Logout request",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             example={}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="logout successful"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Server Error"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
    public function logout(Request $request) {
        try {
            $this->authService->logout($request);
            return  $this->successResponse("logout successful");
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }
}
