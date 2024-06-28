<?php

namespace Tests\Feature\Admin;

use App\Http\Services\JwtService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;
    protected $admin;
    protected $adminToken;
    protected function setUp(): void
    {
        parent::setUp();

        // Create a regular user and set it as the authenticated user
        $this->admin = User::factory()->create([
            'is_admin' => 1
        ]);
        $this->adminToken = (new JwtService())->createToken($this->admin->toArray());
    }

    public function test_get_users_pagination()
    {
        User::factory()->count(50)->create(['is_admin' => false]);
        User::factory()->count(10)->create(['is_admin' => true]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/v1/admin/user-listing?page=2&limit=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['uuid', 'first_name', 'last_name', 'email', 'phone_number']
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(10, 'data');
    }

    public function test_get_users_search_and_sort()
    {
        User::factory()->create(['is_admin' => false, 'first_name' => 'John', 'last_name' => 'Doe']);
        User::factory()->create(['is_admin' => false, 'first_name' => 'Jane', 'last_name' => 'Smith']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/v1/admin/user-listing?first_name=John&sort_by=last_name&desc=false');

        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'John'])
            ->assertJsonMissing(['first_name' => 'Jane']);
    }

    public function test_get_users_exclude_admins()
    {
        User::factory()->count(5)->create(['is_admin' => false]);
        User::factory()->count(5)->create(['is_admin' => true]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/v1/admin/user-listing');


        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_get_users_can_only_be_accessed_by_admin()
    {
        User::factory()->count(5)->create();

        $user = User::factory()->create();
        $token = (new JwtService())->createToken($user->toArray());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/admin/user-listing');


        $response->assertStatus(403);
    }

    public function test_admin_can_edit_user()
    {
        $user = User::factory()->create();

        $updatedData = [
            'first_name' => 'Updated First Name',
            'last_name'  => 'New Last'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson("/api/v1/admin/user-edit/{$user->uuid}", $updatedData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated First Name',
        ]);
    }

    public function test_admin_account_can_not_be_deleted()
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson("/api/v1/admin/user-delete/{$user->uuid}");

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
            ]);
    }
}
