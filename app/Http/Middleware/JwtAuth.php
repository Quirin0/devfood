<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    public function __construct(private readonly JwtService $jwt) {}

    public function handle(Request $request, Closure $next): Response
    {
        $authorization = $request->header('Authorization');

        if (! $authorization || ! str_starts_with($authorization, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authorization, 7);

        try {
            $payload = $this->jwt->decode($token);
            $userId = isset($payload->sub) ? (int) $payload->sub : null;

            if (! $userId) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            /** @var User|null $user */
            $user = User::find($userId);
            if (! $user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $request->attributes->set('jwt_user', $user);
        } catch (\Throwable) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

