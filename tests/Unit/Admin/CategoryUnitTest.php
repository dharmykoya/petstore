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

    public function test_it_creates_a_category()
    {
        $data = [
            'title' => 'New Category',
        ];

        $category = $this->categoryService->createCategory($data);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', [
            'title' => 'New Category',
            'slug' => 'new-category'
        ]);
    }

    public function test_it_generates_unique_slug_for_duplicate_titles()
    {
        // Create first category
        $data1 = [
            'title' => 'New Category',
        ];
        $category1 = $this->categoryService->createCategory($data1);

        // Create second category with the same title
        $data2 = [
            'title' => 'New Category',
        ];
        $category2 = $this->categoryService->createCategory($data2);

        $this->assertNotEquals($category1->slug, $category2->slug);
        $this->assertDatabaseHas('categories', ['slug' => 'new-category-1']);
    }

    public function test_it_updates_a_category()
    {
        $category = Category::factory()->create([
            'title' => 'Original Title',
        ]);

        $data = [
            'title' => 'Updated Category Title'
        ];

        $result = $this->categoryService->updateCategory($data, $category->uuid);

        $this->assertTrue($result['status']);
        $this->assertEquals('Update successful.', $result['message']);
        $this->assertEquals('Updated Category Title', $result['data']->title);

        $this->assertDatabaseHas('categories', [
            'uuid' => $category->uuid,
            'title' => 'Updated Category Title',
        ]);
    }

    public function test_it_returns_error_if_category_not_found()
    {
        $data = [
            'title' => 'Updated Category Title',
        ];

        $result = $this->categoryService->updateCategory($data, 'non-existing-uuid');

        $this->assertFalse($result['status']);
        $this->assertEquals('Category not found.', $result['message']);
    }

    public function test_delete_category()
    {
        $category = Category::factory()->create();
        $result = $this->categoryService->deleteCategory($category->uuid);

        $this->assertTrue($result['status']);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_non_existing_category()
    {
        $result = $this->categoryService->deleteCategory('non-existing-uuid');

        $this->assertFalse($result['status']);
        $this->assertEquals('Category not found.', $result['message']);
    }
}
