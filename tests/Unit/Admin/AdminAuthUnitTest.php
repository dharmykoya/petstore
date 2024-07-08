<?php

namespace Tests\Unit\Admin;

use App\Http\Middleware\IsAdminMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminAuthUnitTest extends TestCase
{
    public function test_admin_middleware_allows_access()
    {
        // Create a mock admin user
        $adminUser = User::factory()->create(['is_admin' => true]);

        // Create a mock request
        $request = Request::create('/api/v1/admin/create', 'POST');

        // Set the authenticated user for the request
        Auth::setUser($adminUser);

        // Create a mock closure for the next middleware
        $next = function ($request) {
            return new Response('Passed admin middleware');
        };

        // Create an instance of the middleware
        $middleware = new IsAdminMiddleware();

        // Assert that middleware allows access for admin user
        $response = $middleware->handle($request, $next);
        $this->assertEquals('Passed admin middleware', $response->getContent());
    }
}
