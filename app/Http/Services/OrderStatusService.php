<?php

namespace App\Http\Services;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderStatusService {
    /**
     * Get all orders with pagination.
     *
     * @param array<string, mixed> $requestData
     * @return OrderStatus
     */
    public function createStatus(array $requestData): OrderStatus {
        return OrderStatus::create($requestData);
    }

    /**
     * update Status.
     *
     * @param array<string, mixed> $requestData
     * @param string $uuid
     * @return array<string, mixed>
     */
    public function editStatus(array $requestData, string $uuid): array
    {
        $status = OrderStatus::query()->where('uuid', $uuid)->first();

        if (!$status) {
            return ['status' => false, 'message' => 'Status not found.'];
        }

        $status->update($requestData);

        return ['status' => true, 'message' => 'Update successful.', 'data' => $status];
    }

    /**
     * delete Status.
     *
     * @param string $uuid
     * @return array<string, mixed>
     */
    public function deleteStatus(string $uuid): array {
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

    /**
     * get Status.
     *
     * @param string $uuid
     * @return array<string, mixed>
     */
    public function getStatus(string $uuid): array {
        $status = OrderStatus::query()->where('uuid', $uuid)->first();

        if (!$status) {
            return ['status' => false, 'message' => 'Status not found.'];
        }


        return ['status' => true, 'data' => $status];
    }

    /**
     * Get all order status with pagination.
     *
     * @param Request $request
     * @return LengthAwarePaginator<OrderStatus>
     */
    public function getAllStatus(Request $request): LengthAwarePaginator {
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
