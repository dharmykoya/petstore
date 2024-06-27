<?php

namespace App\Http\Middleware;

use App\Http\Services\JwtService;
use Closure;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (!$token || !preg_match('/^Bearer\s(\S+)$/', $token, $matches)) {
            abort(Response::HTTP_UNAUTHORIZED, "Please login to complete this request");
        }

        $token = $matches[1];

        $jwtService = new JwtService();
        $decodedUser = $jwtService->getUserFromToken($token);

        if (!$decodedUser) {
            abort(Response::HTTP_UNAUTHORIZED, "Please login to complete this request");
        }

        // set Auth user;
        $user = new GenericUser((array) $decodedUser);
        Auth::setUser($user);

        return $next($request);
    }
}
