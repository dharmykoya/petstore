<?php

namespace App\Http\Services;

use App\Models\OrderStatus;

class OrderStatusService {
    public function createStatus($requestData) {
        return OrderStatus::create($requestData);
    }

    public function editStatus($requestData, $uuid)
    {
        $status = OrderStatus::query()->where('uuid', $uuid)->first();

        if (!$status) {
            return ['status' => false, 'message' => 'Status not found.'];
        }

        $status->update($requestData);

        return ['status' => true, 'message' => 'Update successful.', 'data' => $status];
    }

    public function deleteStatus($uuid) {
        $status = OrderStatus::query()->with('orders')->where('uuid', $uuid)->first();

        if (!$status) {
            return ['status' => false, 'message' => 'Status not found.'];
        }

        if ($status->orders->count()) {
            return ['status' => false, 'message' => 'There are orders attached to this status.'];
        }

        $status->delete();

        return ['status' => true];
    }

    public function getStatus($uuid) {
        $status = OrderStatus::query()->where('uuid', $uuid)->first();

        if (!$status) {
            return ['status' => false, 'message' => 'Status not found.'];
        }


        return ['status' => true, 'data' => $status];
    }

    public function getAllStatus($request) {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 15);
        $sortBy = $request->input('sort_by', 'created_at');
        $desc = $request->input('desc', 'true');

        $query = OrderStatus::query();

        $query->when($request->filled('title'), function ($q) use ($request) {
            $q->orWhere('title', 'like', '%' . $request->input('title') . '%');
        });

        if ($desc === 'true') {
            $query->orderByDesc($sortBy);
        } else {
            $query->orderBy($sortBy);
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }
}
