<?php

namespace App\Http\Services;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService {
    /**
     * Get all categories with pagination.
     *
     * @param Request $request
     * @return LengthAwarePaginator<Category>
     */
    public function getAllCategories(Request $request): LengthAwarePaginator {
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

    /**
     * update Category.
     *
     * @param array<string, mixed> $requestData
     * @return Category
     */
    public function createCategory(array $requestData): Category {
        if (empty($requestData['slug'])) {
            $requestData['slug'] = $this->generateUniqueSlug($requestData['title']);
        }

        return Category::create($requestData);
    }

    protected function generateUniqueSlug(string $title, int $id = 0): string {
        $slug = Str::slug($title);
        $count = Category::where('slug', 'like', "{$slug}%")->where('uuid', '!=', $id)->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * update Category.
     *
     * @param array<string, mixed> $requestData
     * @param string $uuid
     * @return array<string, mixed>
     */
    public function updateCategory(array $requestData, string $uuid): array {
        $category = Category::query()->where('uuid', $uuid)->first();

        if (!$category) {
            return ['status' => false, 'message' => 'Category not found.'];
        }

        if (empty($requestData['slug'])) {
            $requestData['slug'] = $this->generateUniqueSlug($requestData['title'], $category->id);
        }

        $category->update($requestData);

        return ['status' => true, 'message' => 'Update successful.', 'data' => $category];
    }


    /**
     * delete Category.
     *
     * @param string $uuid
     * @return array<string, mixed>
     */
    public function deleteCategory(string $uuid): array {
        $category = Category::query()->where('uuid', $uuid)->first();

        if (!$category) {
            return ['status' => false, 'message' => 'Category not found.'];
        }

        $category->delete();

        return ['status' => true];
    }

    /**
     * get Category.
     *
     * @param string $uuid
     * @return array<string, mixed>
     */
    public function getCategory(string $uuid): array {
        $category = Category::query()->where('uuid', $uuid)->first();

        if (!$category) {
            return ['status' => false, 'message' => 'Category not found.'];
        }


        return ['status' => true, 'data' => $category];
    }
}
