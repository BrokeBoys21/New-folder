<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Exception;

class Authenticate extends Middleware
{
    /**
     * Redirect unauthenticated users to the login page.
     *
     * @param Request $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $token = $this->extractToken($request);
            if (!$token) {
                return response()->json(['error' => 'Token not provided'], Response::HTTP_UNAUTHORIZED);
            }

            $decoded = $this->decodeJwt($token);
            if (!$decoded) {
                return response()->json(['error' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
            }

            $companyIdFromToken = $decoded->company_id ?? null;
            if (!$this->isCompanyValid($companyIdFromToken)) {
                return response()->json(['error' => 'Unauthorized company'], Response::HTTP_UNAUTHORIZED);
            }

            // Attach the decoded token to the request for further use
            $request->attributes->add(['decoded_token' => $decoded]);

            return $next($request);
        } catch (Exception $e) {
            Log::error('Authentication Error: ' . $e->getMessage());
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Extract the JWT token from the Authorization header.
     *
     * @param Request $request
     * @return string|null
     */
    private function extractToken(Request $request): ?string
    {
        $authorizationHeader = $request->header('Authorization');
        if ($authorizationHeader && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Decode the JWT token.
     *
     * @param string $token
     * @return object|null
     */
    private function decodeJwt(string $token): ?object
    {
        try {
            $secretKey = env('JWT_SECRET', "a-string-secret-at-least-256-bits-long");
            if (!$secretKey) {
                throw new Exception('JWT_SECRET is not set in the environment.');
            }

            return JWT::decode($token, new Key($secretKey, 'HS256'));
        } catch (Exception $e) {
            Log::error('JWT Decode Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate if the company ID exists in the database.
     *
     * @param int|null $companyIdFromToken
     * @return bool
     */
    private function isCompanyValid(?int $companyIdFromToken): bool
    {
        if (!$companyIdFromToken) {
            return false;
        }

        return DB::table('deposits')->where('id', $companyIdFromToken)->exists();
    }
}

