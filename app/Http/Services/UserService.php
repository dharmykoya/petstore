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

    public function editUser($requestData)
    {
        $authUser = auth()->user();
        $user = User::query()->where('uuid', $authUser->uuid)->first();

        if (!$user) {
            return ['status' => false, 'message' => 'User not found.'];
        }

        $user->update($requestData);

        return ['status' => true, 'message' => 'Update successful.', 'data' => $user];
    }

    public function deleteUser($uuid) {
        $user = User::query()->where('uuid', $uuid)->first();
        if (!$user) {
            return ['status' => false];
        }

        $user->delete();

        return ['status' => true];
    }
}
