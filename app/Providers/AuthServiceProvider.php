<?php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Book::class => \App\Policies\BookPolicy::class,
        \App\Models\Author::class => \App\Policies\AuthorPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
    ];
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('admin', function ($user) {
            return (bool) ($user->is_admin ?? false);
        });
    }
}
