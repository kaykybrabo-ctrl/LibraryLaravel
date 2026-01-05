<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Services\Notifications\BookDueNotificationStrategy;
use App\Services\Notifications\MailBookDueNotificationStrategy;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookDueNotificationStrategy::class, MailBookDueNotificationStrategy::class);
    }
    public function boot(): void
    {
    }
}
