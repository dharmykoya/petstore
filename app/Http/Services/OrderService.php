<?php

namespace App\Http\Services;

use App\Models\Order;

class OrderService {
    public function getOrders($request)
    {
        $authUser = auth()->user();

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 15);
        $sortBy = $request->input('sort_by', 'created_at');
        $desc = $request->input('desc', 'true');

        $query = Order::query()->where('user_id', $authUser->id);

        if ($desc === 'true') {
            $query->orderByDesc($sortBy);
        } else {
            $query->orderBy($sortBy);
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }

}
