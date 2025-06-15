<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     */
    protected $middleware = [
        // Trusts proxies (e.g. for load balancers)
        \App\Http\Middleware\TrustProxies::class,
        // Handles CORS
        \Fruitcake\Cors\HandleCors::class,
        // Prevents your app from running when in maintenance mode
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        // Validates the size of POST bodies
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        // Trims all request strings
        \App\Http\Middleware\TrimStrings::class,
        // Converts empty strings to null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // Encrypt cookies on outgoing responses
            \App\Http\Middleware\EncryptCookies::class,
            // Add queued cookies to the response
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            // Start the session
            \Illuminate\Session\Middleware\StartSession::class,
            // Share errors from the session with views
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // Verify CSRF tokens on POST/PUT/PATCH/DELETE
            \App\Http\Middleware\VerifyCsrfToken::class,
            // Substitute route bindings
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // For SPA authentication (stateful cookies). If you're only using token-based API, you can remove this.
            EnsureFrontendRequestsAreStateful::class,

            // Rate limiting
            'throttle:api',

            // Substitute route-model bindings
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Route middleware.
     *
     * These can be assigned to groups or used individually.
     */
    protected $routeMiddleware = [
        // Authentication (session-based)
        'auth' => \App\Http\Middleware\Authenticate::class,
        // Basic HTTP auth
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        // Authorize actions via policies
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        // Redirect if authenticated
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        // Signature validation for URLs
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        // Throttle requests
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        // Substitute route-model bindings
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        // Ensure email is verified
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Sanctum token authentication (for api routes)
       'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\Authenticate::class,
        'admin'=> \App\Http\Middleware\AdminMiddleware::class,

    ];
}
