<?php

namespace App\Http\Services;

use App\Models\User;

class AuthService {
    public function createUser($requestData) {
        return User::create($requestData);
    }
}
