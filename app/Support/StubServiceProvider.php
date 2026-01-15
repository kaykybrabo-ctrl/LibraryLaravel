<?php

namespace App\Support;

use Illuminate\Support\ServiceProvider;

class StubServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // no-op
    }

    public function boot(): void
    {
        // no-op
    }
}
