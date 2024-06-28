<?php

namespace App\Http\Middleware;

use App\Http\Services\JwtService;
use App\Models\TokenBlacklist;
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
        $token = $request->bearerToken();

        if (!$token) {
            abort(Response::HTTP_UNAUTHORIZED, "Please login to complete this request");
        }

        $blackList = TokenBlacklist::query()->where('token', $token)->exists();

        // Check if the token is blacklisted
        if ($blackList) {
            abort(Response::HTTP_UNAUTHORIZED, "Please login to complete this request");
        }

        try {
            $jwtService = new JwtService();
            $decodedUser = $jwtService->getUserFromToken($token);

            if (!$decodedUser) {
                abort(Response::HTTP_UNAUTHORIZED, "Please login to complete this request");
            }

            // set Auth user;
            $user = new GenericUser((array) $decodedUser);
            Auth::setUser($user);

            return $next($request);
        } catch (\Exception $e) {
            abort(Response::HTTP_UNAUTHORIZED, "Invalid Token");
        }
    }
}
