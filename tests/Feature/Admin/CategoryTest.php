<?php

namespace Tests\Feature\Admin;

use App\Http\Services\JwtService;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_get_all_categories()
    {
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

    public function test_non_admin_cannot_get_all_categories()
    {
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
}
