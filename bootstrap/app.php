<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use App\Providers\AuthServiceProvider;
use App\Providers\AppServiceProvider;
use PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider as JWTServiceProvider;
use Laravel\Horizon\HorizonServiceProvider;

if (!class_exists(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class)) {
    class_alias(\App\Support\StubServiceProvider::class, \NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class);
}

if (!class_exists(\Laravel\Sail\SailServiceProvider::class)) {
    class_alias(\App\Support\StubServiceProvider::class, \Laravel\Sail\SailServiceProvider::class);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withEvents()
    ->withProviders([
        AuthServiceProvider::class,
        AppServiceProvider::class,
        JWTServiceProvider::class,
        HorizonServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'jwt.auth' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\RefreshToken::class,
        ]);
    })
    ->withBindings([
        ConsoleKernelContract::class => \App\Console\Kernel::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
    })
    ->create();
