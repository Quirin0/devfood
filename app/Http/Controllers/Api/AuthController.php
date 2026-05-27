<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct(private readonly JwtService $jwt) {}

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->string('name') ?: 'Usuário DevFood',
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
            'google_id' => null,
        ]);

        $token = $this->jwt->issueToken($user);

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $this->jwt->issueToken($user);

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ]);
    }

    public function me(Request $request)
    {
        /** @var User $user */
        $user = $request->attributes->get('jwt_user');

        return response()->json(['data' => $user]);
    }

    public function googleRedirect()
    {
        $redirectUrl = $this->googleCallbackUrl();

        return Socialite::driver('google')
            ->stateless()
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    public function googleCallback()
    {
        $redirectUrl = $this->googleCallbackUrl();

        $googleUser = Socialite::driver('google')
            ->stateless()
            ->redirectUrl($redirectUrl)
            ->user();

        $email = $googleUser->getEmail();
        $googleId = (string) $googleUser->getId();
        $name = $googleUser->getName() ?: 'Usuário DevFood';

        if (! $email) {
            return response()->json(['message' => 'Google did not provide email'], 422);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)), // usuário não precisa senha pra login via Google
                'google_id' => $googleId,
            ]);
        } else {
            $user->forceFill([
                'name' => $name,
                'google_id' => $googleId,
            ])->save();
        }

        $token = $this->jwt->issueToken($user);

        // Callback simples para SPA: salva o token e redireciona.
        $escapedToken = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');

        return response(
            '<!doctype html>
<html>
  <head><meta charset="utf-8" /></head>
  <body>
    <script>
      try {
        localStorage.setItem("devfood_token", "' . $escapedToken . '");
      } catch (e) {}
      window.location = "/perfil/";
    </script>
  </body>
</html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    private function googleCallbackUrl(): string
    {
        $redirect = config('services.google.redirect');

        if (is_string($redirect) && $redirect !== '') {
            return rtrim($redirect, '/');
        }

        $base = rtrim((string) config('app.url'), '/');
        if (! $base) {
            $base = rtrim((string) env('APP_URL', ''), '/');
        }

        return $base . '/api/v1/auth/google/callback';
    }
}

