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

    public function editUser($requestData, $uuid)
    {
        $user = User::query()->where('uuid', $uuid)->first();

        if (!$user) {
            return ['status' => false, 'message' => 'User not found.'];
        }

        if ($user->is_admin) {
            return ['status' => false, 'message' => 'Admin details can not be edited.'];
        }

        $user->update($requestData);

        return ['status' => true, 'message' => 'Update successful.', 'data' => $user];
    }

    public function deleteUser($uuid) {
        $user = User::query()->where('uuid', $uuid)->first();
        if (!$user) {
            return ['status' => false];
        }

        if ($user->is_admin) {
            return ['status' => false];
        }

        $user->delete();

        return ['status' => true];
    }

    public function getUsers($request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 15);
        $sortBy = $request->input('sort_by', 'created_at');
        $desc = $request->input('desc', 'true');

        $query = User::query();

        $query->when($request->filled('first_name'), function ($q) use ($request) {
            $q->orWhere('first_name', 'like', '%' . $request->input('first_name') . '%');
        });

        $query->when($request->filled('last_name'), function ($q) use ($request) {
            $q->orWhere('last_name', 'like', '%' . $request->input('last_name') . '%');
        });

        $query->when($request->filled('email'), function ($q) use ($request) {
            $q->orWhere('email', 'like', '%' . $request->input('email') . '%');
        });

        $query->when($request->filled('phone_number'), function ($q) use ($request) {
            $q->orWhere('phone_number', 'like', '%' . $request->input('phone_number') . '%');
        });

        $query->where('is_admin', 0);

        if ($desc === 'true') {
            $query->orderByDesc($sortBy);
        } else {
            $query->orderBy($sortBy);
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }

}
