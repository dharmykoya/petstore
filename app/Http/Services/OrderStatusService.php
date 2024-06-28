<?php

namespace App\Http\Services;

use App\Models\OrderStatus;

class OrderStatusService {
    public function createStatus($requestData) {
        return OrderStatus::create($requestData);
    }
}
