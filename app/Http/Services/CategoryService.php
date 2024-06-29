<?php

namespace App\Http\Services;

use App\Models\Category;

class CategoryService {
    public function getAllCategories($request) {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 15);
        $sortBy = $request->input('sort_by', 'created_at');
        $desc = $request->input('desc', 'true');

        $query = Category::query();

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
