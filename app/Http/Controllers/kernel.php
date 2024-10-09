<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        // Middleware applied to all requests
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        // You can add more global middleware here if necessary
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'auth' => \App\Http\Middleware\Authenticate::class, // For route authentication
        'api' => \App\Http\Middleware\EnsureTokenIsValid::class, // If you are using API tokens for authentication
        // Add your custom middleware here as needed
    ];

    /**
     * The application's middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // Middleware applied to web routes
            // You can add more middleware specific to web routes here
        ],

        'api' => [
<<<<<<< HEAD
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
=======
            'throttle:api', // Rate limiting for API routes
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Enables route model binding
            // You can add more middleware specific to API here
>>>>>>> 3e5061f (commit onspot)
        ],
    ];
}
