<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }
    public function register(RegisterUserRequest $request) {
        try {
            $this->authService->createUser($request->validated());
            return  $this->successResponse("User created successfully, please login to continue.");
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

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
}
