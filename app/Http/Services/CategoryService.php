<?php

namespace App\Http\Services;

use App\Models\Category;
use Illuminate\Support\Str;

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

    public function createCategory($requestData) {
        if (empty($requestData['slug'])) {
            $requestData['slug'] = $this->generateUniqueSlug($requestData['title']);
        }

        return Category::create($requestData);
    }

    protected function generateUniqueSlug($title, $id = 0)
    {
        $slug = Str::slug($title);
        $count = Category::where('slug', 'like', "{$slug}%")->where('id', '!=', $id)->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function updateCategory($requestData, $uuid)
    {
        $category = Category::query()->where('uuid', $uuid)->first();

        if (!$category) {
            return ['status' => false, 'message' => 'Category not found.'];
        }

        if (empty($requestData['slug'])) {
            $requestData['slug'] = $this->generateUniqueSlug($requestData['title'], $uuid);
        }

        $category->update($requestData);

        return ['status' => true, 'message' => 'Update successful.', 'data' => $category];
    }

    public function deleteCategory($uuid) {
        $category = Category::query()->where('uuid', $uuid)->first();

        if (!$category) {
            return ['status' => false, 'message' => 'Category not found.'];
        }

        $category->delete();

        return ['status' => true];
    }
}
