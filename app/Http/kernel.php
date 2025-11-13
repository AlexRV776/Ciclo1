<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // Middleware globales
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        // ...
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\TrustProxies::class,
            //\App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            //\App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    // 👇 Aquí agregas el tuyo
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'isAdmin' => \App\Http\Middleware\IsAdmin::class, // ✅ <-- ESTE
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
