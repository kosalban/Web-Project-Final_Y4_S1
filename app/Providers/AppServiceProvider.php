<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-records', static fn (User $user): bool => $user->isAdmin());
        Gate::define('create-tickets', static fn (User $user): bool => $user->isAdmin() || $user->isStaff());
        Gate::define('manage-tickets', static fn (User $user): bool => $user->isAdmin() || $user->isStaff());

        View::composer('layouts.navbar', static function (ViewContract $view): void {
            $user = Auth::user();

            if (! $user instanceof User) {
                $view->with('latestNotifications', collect());
                $view->with('unreadNotificationCount', 0);

                return;
            }

            $view->with('latestNotifications', $user->unreadNotifications()->latest()->limit(5)->get());
            $view->with('unreadNotificationCount', $user->unreadNotifications()->count());
        });
    }
}
