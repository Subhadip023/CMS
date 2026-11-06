<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DomainController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\WebsiteController;
use App\Http\Middleware\EnsureTenantAccess;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Auth
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Public Templates & Themes
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/themes', [TemplateController::class, 'themes']);

    // Authenticated Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Current user & session
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

        // Dashboard Telemetry
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        // Website CRUD & Actions
        Route::get('/websites', [WebsiteController::class, 'index']);
        Route::post('/websites', [WebsiteController::class, 'store']);
        
        Route::middleware(EnsureTenantAccess::class)->group(function () {
            Route::get('/websites/{website}', [WebsiteController::class, 'show']);
            Route::put('/websites/{website}', [WebsiteController::class, 'update']);
            Route::delete('/websites/{website}', [WebsiteController::class, 'destroy']);
            Route::post('/websites/{website}/publish', [WebsiteController::class, 'publish']);
            Route::post('/websites/{website}/unpublish', [WebsiteController::class, 'unpublish']);
            Route::post('/websites/{website}/duplicate', [WebsiteController::class, 'duplicate']);

            // Custom Domains
            Route::get('/websites/{website}/domains', [DomainController::class, 'index']);
            Route::post('/websites/{website}/domains', [DomainController::class, 'store']);
            Route::post('/websites/{website}/domains/{domain}/verify', [DomainController::class, 'verify']);
            Route::delete('/websites/{website}/domains/{domain}', [DomainController::class, 'destroy']);
        });
    });
});
