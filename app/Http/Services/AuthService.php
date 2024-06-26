<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService {
    private JwtService $jwtService;
    public function __construct(JwtService $jwtService) {
        $this->jwtService = $jwtService;
    }

    public function createUser($requestData) {
        return User::create($requestData);
    }

    public function loginUser($requestData) {
        $user = User::query()->where('email', $requestData['email'])->first();

        if (!$user || !Hash::check($requestData['password'], $user->password)) {
            return ['status' => false, 'message' => 'Email and/or Password does not match.'];
        }

        $token = $this->jwtService->createToken($user->toArray());
        $user['token'] = $token;
        return ['status' => true, 'data' => $user];
    }
}
