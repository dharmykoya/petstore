<?php

namespace Tests\Unit\Admin;

use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    public function test_get_users_pagination()
    {
        $user = User::factory()->create();
        $this->mockJwtAuthentication($user);

        User::factory()->count(50)->create(['is_admin' => false]);
        User::factory()->count(10)->create(['is_admin' => true]);

        $request = new Request(['page' => 2, 'limit' => 10]);
        $result = $this->userService->getUsers($request);

        $this->assertCount(10, $result);
        $this->assertEquals(2, $result->currentPage());
    }

    public function test_get_users_search_and_sort()
    {
        $user = User::factory()->create();
        $this->mockJwtAuthentication($user);

        User::factory()->create(['is_admin' => false, 'first_name' => 'John', 'last_name' => 'Doe']);
        User::factory()->create(['is_admin' => false, 'first_name' => 'Jane', 'last_name' => 'Smith']);

        $request = new Request(['first_name' => 'John', 'sort_by' => 'last_name', 'desc' => 'false']);
        $result = $this->userService->getUsers($request);

        $this->assertCount(1, $result);
        $this->assertEquals('John', $result->first()->first_name);
    }

    public function test_get_users_exclude_admins()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->mockJwtAuthentication($user);

        User::factory()->count(5)->create(['is_admin' => false]);
        User::factory()->count(5)->create(['is_admin' => true]);

        $request = new Request();
        $result = $this->userService->getUsers($request);

        $this->assertCount(5, $result);
        $this->assertFalse($result->contains(function ($user) {
            return $user->is_admin;
        }));
    }

    protected function mockJwtAuthentication($user)
    {
        Auth::shouldReceive('user')->andReturn($user);
    }

    public function test_edit_user_by_admin()
    {
        $user = User::factory()->create();

        $updatedData = [
            'first_name' => 'Updated First Name',
        ];

        $userService = new UserService();
        $result = $userService->editUser($updatedData, $user->uuid);

        $this->assertTrue($result['status']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated First Name',
        ]);
    }

    public function test_it_deletes_a_user_successfully()
    {

        $user = User::factory()->create();

        $response = $this->userService->deleteUser($user->uuid);

        $this->assertTrue($response['status']);

        $this->assertNull(User::find($user->id));
    }


    public function test_it_returns_false_when_deleting_non_existing_user()
    {
        $response = $this->userService->deleteUser('non_existing_uuid');

        $this->assertFalse($response['status']);
    }

    public function test_it_returns_false_when_deleting_admin_user()
    {
        $adminUser = User::factory()->create(['is_admin' => true]);

        $response = $this->userService->deleteUser($adminUser->uuid);

        $this->assertFalse($response['status']);

        $this->assertNotNull(User::find($adminUser->id));
    }
}
