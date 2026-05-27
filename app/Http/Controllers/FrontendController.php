<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class FrontendController extends Controller
{
    private function frontendPath(string $relative = ''): string
    {
        return public_path('frontend'.($relative ? '/'.$relative : ''));
    }

    public function index(?string $path = null): Response
    {
        if ($path && $this->tryServeStatic($path)) {
            return $this->serveFile($this->frontendPath($path));
        }

        $indexPath = $this->frontendPath('index.html');

        if (! File::exists($indexPath)) {
            return response(
                '<html><body style="font-family:sans-serif;padding:2rem;max-width:600px"><h1 style="color:#ea1d2c">DevFood</h1><p>Frontend ainda não foi buildado.</p><pre style="background:#f5f5f5;padding:1rem;border-radius:8px">cd frontend\nnpm install\nnpm run build</pre></body></html>',
                200,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        $html = File::get($indexPath);

        if ($path && ! str_contains($path, '.')) {
            $subIndex = $this->frontendPath(trim($path, '/').'/index.html');
            if (File::exists($subIndex)) {
                return response(File::get($subIndex), 200, ['Content-Type' => 'text/html; charset=UTF-8']);
            }
        }

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function nextAsset(string $path): Response
    {
        // Robust: some deploys copy `_next` to `public/_next` (not `public/frontend/_next`).
        // Serve from `public/frontend/_next` first, then fallback to `public/_next`.
        $candidateInFrontend = public_path('frontend/_next/'.$path);
        if (File::exists($candidateInFrontend) && File::isFile($candidateInFrontend)) {
            return $this->serveFile($candidateInFrontend);
        }

        $candidateInRootNext = public_path('_next/'.$path);
        if (File::exists($candidateInRootNext) && File::isFile($candidateInRootNext)) {
            return $this->serveFile($candidateInRootNext);
        }

        abort(404);
    }

    public function asset(string $path): Response
    {
        return $this->serveAsset($path);
    }

    private function serveAsset(string $relativePath): Response
    {
        $filePath = $this->frontendPath($relativePath);

        if (! File::exists($filePath) || ! File::isFile($filePath)) {
            abort(404);
        }

        return $this->serveFile($filePath);
    }

    private function tryServeStatic(string $path): bool
    {
        $filePath = $this->frontendPath($path);

        return File::exists($filePath) && File::isFile($filePath);
    }

    private function serveFile(string $filePath): Response
    {
        $mime = match (strtolower(pathinfo($filePath, PATHINFO_EXTENSION))) {
            'js' => 'application/javascript',
            'css' => 'text/css',
            'json' => 'application/json',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'woff2' => 'font/woff2',
            'woff' => 'font/woff',
            'ico' => 'image/x-icon',
            'txt' => 'text/plain',
            'html' => 'text/html; charset=UTF-8',
            default => File::mimeType($filePath) ?: 'application/octet-stream',
        };

        return response(File::get($filePath), 200, ['Content-Type' => $mime]);
    }
}
