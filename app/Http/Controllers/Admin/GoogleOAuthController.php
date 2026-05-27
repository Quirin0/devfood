<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GoogleOAuthController extends Controller
{
    public function edit()
    {
        return view('admin.google-oauth', [
            'clientId' => env('GOOGLE_CLIENT_ID', ''),
            'clientSecret' => env('GOOGLE_CLIENT_SECRET', ''),
            'callbackUrl' => config('services.google.redirect'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'client_id' => ['required', 'string', 'max:255'],
            'client_secret' => ['required', 'string', 'max:255'],
            'callback_url' => ['required', 'url', 'max:500'],
        ]);

        $this->setEnvValue('GOOGLE_CLIENT_ID', $data['client_id']);
        $this->setEnvValue('GOOGLE_CLIENT_SECRET', $data['client_secret']);
        $this->setEnvValue('GOOGLE_CALLBACK_URL', $data['callback_url']);

        config([
            'services.google.client_id' => $data['client_id'],
            'services.google.client_secret' => $data['client_secret'],
            'services.google.redirect' => $data['callback_url'],
        ]);

        return back()->with('success', 'Configuração do Google OAuth salva com sucesso.');
    }

    private function setEnvValue(string $key, string $value): void
    {
        $envPath = base_path('.env');
        $escaped = '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';

        if (! File::exists($envPath)) {
            File::put($envPath, "{$key}={$escaped}\n");
            return;
        }

        $content = File::get($envPath);
        $pattern = "/^{$key}=.*$/m";
        $line = "{$key}={$escaped}";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $line, $content) ?? $content;
        } else {
            $content = rtrim($content)."\n".$line."\n";
        }

        File::put($envPath, $content);
    }
}

