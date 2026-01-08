<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Repositories\AuthorRepository;
use App\Repositories\BookRepository;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Services\Notifications\BookDueNotificationStrategy;
use App\Services\Notifications\MailBookDueNotificationStrategy;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
        $this->app->bind(AuthorRepositoryInterface::class, AuthorRepository::class);
        $this->app->bind(BookDueNotificationStrategy::class, MailBookDueNotificationStrategy::class);
    }
    public function boot(): void
    {
        app()->setLocale('pt');
        app()->setFallbackLocale('pt');
    }
}
