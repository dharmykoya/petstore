<?php

namespace Tests\Unit\Admin;

use App\Http\Services\CategoryService;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class CategoryUnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected CategoryService $categoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = new CategoryService();
    }

    public function test_it_can_get_all_categories_with_pagination_and_sorting()
    {
        // Create some categories
        Category::factory()->count(20)->create();

        // Create a request instance with query parameters
        $request = Request::create('/api/v1/categories', 'GET', [
            'page' => 1,
            'limit' => 10,
            'sort_by' => 'created_at',
            'desc' => 'true',
        ]);

        $categories = $this->categoryService->getAllCategories($request);

        $this->assertEquals(10, $categories->count());
        $this->assertEquals(1, $categories->currentPage());
    }

    public function test_it_can_search_categories_by_title()
    {
        Category::factory()->create(['title' => 'First Category']);
        Category::factory()->create(['title' => 'Second Category']);

        $request = Request::create('/api/v1/categories', 'GET', [
            'page' => 1,
            'limit' => 10,
            'title' => 'First',
        ]);

        $categories = $this->categoryService->getAllCategories($request);

        $this->assertCount(1, $categories);
        $this->assertEquals('First Category', $categories->items()[0]->title);
    }
}
