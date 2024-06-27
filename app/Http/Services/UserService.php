<?php

namespace App\Http\Services;

use App\Models\User;

class UserService {
    public function getUser($uuid) {
        $user = User::query()->select([
            'uuid',
            'first_name',
            'last_name',
            'email',
            'avatar',
            'address',
            'phone_number',
            'is_marketing',
        ])->where('uuid', $uuid)->first();

        if (!$user) {
            return ['status' => false, 'message' => 'User not found.'];
        }

        return ['status' => true, 'message' => 'User found.', 'data' => $user];
    }
}
