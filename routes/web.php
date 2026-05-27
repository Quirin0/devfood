<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\GoogleOAuthController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/google-oauth', [GoogleOAuthController::class, 'edit'])->name('admin.google.edit');
        Route::post('/google-oauth', [GoogleOAuthController::class, 'update'])->name('admin.google.update');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});

Route::get('/_next/{path}', [FrontendController::class, 'nextAsset'])
    ->where('path', '.*')
    ->name('frontend.next');

Route::get('/frontend/{path}', [FrontendController::class, 'asset'])
    ->where('path', '.*')
    ->name('frontend.asset');

Route::get('/{path?}', [FrontendController::class, 'index'])
    ->where('path', '^(?!api|up).*$')
    ->name('frontend.index');
