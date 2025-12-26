<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Notifications\BookDueNotificationStrategy;
use App\Services\Notifications\LogBookDueNotificationStrategy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookDueNotificationStrategy::class, LogBookDueNotificationStrategy::class);
    }

    public function boot(): void
    {
    }
}
