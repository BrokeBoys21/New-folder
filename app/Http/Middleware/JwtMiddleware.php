<?php
// app/Http/Middleware/JwtMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => 'error', 'message' => 'Token is invalid'], 401);
            } else if ($e instanceof TokenExpiredException) {
                return response()->json(['status' => 'error', 'message' => 'Token is expired'], 401);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Authorization token not found'], 401);
            }
        }
        return $next($request);
    }
}