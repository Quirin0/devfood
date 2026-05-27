<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private function secret(): string
    {
        // Fallback pra evitar depender de um env específico.
        return (string) (env('JWT_SECRET') ?: env('APP_KEY'));
    }

    public function issueToken(User $user): string
    {
        $now = time();

        $payload = [
            'iss' => 'devfood',
            'aud' => 'devfood',
            'sub' => $user->id,
            'iat' => $now,
            'exp' => $now + (int) (env('JWT_TTL_SECONDS', 3600)),
        ];

        return JWT::encode($payload, $this->secret(), 'HS256');
    }

    public function decode(string $token): object
    {
        return JWT::decode($token, new Key($this->secret(), 'HS256'));
    }
}

