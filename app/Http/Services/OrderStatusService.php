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
}
