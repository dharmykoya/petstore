<?php

namespace Tests\Feature\Admin;

use App\Http\Services\JwtService;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase {
    use RefreshDatabase;

    public function test_admin_can_get_all_categories() {
        $admin = User::factory()->create(['is_admin' => true]);

        Category::factory()->count(20)->create();

        $token = (new JwtService())->createToken($admin->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/categories?limit=10&page=1");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'created_at', 'updated_at'],
                ],
                'status',
                'message',
            ]);

        $this->assertCount(10, $response->json('data'));
        $this->assertEquals('Categories fetched successfully.', $response->json('message'));
    }

    public function test_non_admin_cannot_get_all_categories() {
        // Create a non-admin user
        $user = User::factory()->create(['is_admin' => false]);
        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/categories?limit=10&page=1");

        $response->assertStatus(403)
            ->assertJson([
                'message' => "You don't have permission to operate this route.",
            ]);
    }

    public function test_admin_can_create_category() {
        $admin = User::factory()->create(['is_admin' => 1]);

        $data = [
            'title' => 'New Category',
            'description' => 'This is a description of the new category.',
        ];

        $token = (new JwtService())->createToken($admin->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/v1/category/create", $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'category created successfully.',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'uuid',
                    'title',
                    'slug',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'title' => 'New Category',
            'slug' => 'new-category',
        ]);
    }

    public function test_non_admin_cannot_create_category() {
        $user = User::factory()->create(['is_admin' => 0]);

        $data = [
            'title' => 'New Category'
        ];

        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/v1/category/create", $data);


        $response->assertStatus(403)
            ->assertJson([
                'message' => "You don't have permission to operate this route."
            ]);
    }

    public function test_unauthenticated_user_cannot_create_category() {
        $data = [
            'title' => 'New Category',
        ];
        $response = $this->postJson('/api/v1/category/create', $data);

        $response->assertStatus(401);
    }

    public function test_category_creation_fails_with_invalid_data() {
        $admin = User::factory()->create(['is_admin' => 1]);

        $data = [
            'title' => '',
            'slug' => '',
        ];

        $token = (new JwtService())->createToken($admin->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/v1/category/create", $data);


        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'slug']);
    }

    public function test_admin_can_update_category()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create();
        $token = (new JwtService())->createToken($admin->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/v1/category/{$category->uuid}", ['title' => 'Updated Title',]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'Category edited successfully',
            'data' => [
                'title' => 'Updated Title',
                'slug' => 'updated-title',
            ],
        ]);

        $this->assertDatabaseHas('categories', [
            'uuid' => $category->uuid,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
        ]);
    }

    public function test_non_admin_cannot_update_category()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $category = Category::factory()->create();

        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/v1/category/{$category->uuid}", ['title' => 'Updated Title',]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => "You don't have permission to operate this route.",
        ]);
    }

    public function test_cannot_update_non_existent_category()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $token = (new JwtService())->createToken($admin->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/v1/category/ffrtt343345", ['title' => 'Updated Title',]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => false,
            'message' => 'Category not found.',
        ]);
    }

    public function test_admin_can_delete_category()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $token = (new JwtService())->createToken($admin->toArray());

        $category = Category::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/category/{$category->uuid}");

        $response->assertStatus(200)
            ->assertJson(['status' => true]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_non_admin_cannot_delete_category()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $category = Category::factory()->create();

        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/category/{$category->uuid}");

        // Assert response
        $response->assertStatus(403)
            ->assertJson([
                'message' => "You don't have permission to operate this route.",
            ]);
    }
}
