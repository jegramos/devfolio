<?php

namespace App\Providers;

use App\Actions\AddSoftDeleteMarkerAction;
use App\Enums\Role;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * Load IDE helper for non-production environment
         *
         * @see https://github.com/barryvdh/laravel-ide-helper
         */
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->bind(AddSoftDeleteMarkerAction::class, function () {
            return new AddSoftDeleteMarkerAction;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Implicitly grant "super_user" role all permissions.
         * This works in the app by using gate-related functions like auth()->user->can() and @can()
         *
         * @see https://spatie.be/docs/laravel-permission/v6/basic-usage/super-admin
         */
        Gate::after(function ($user) {
            return $user->hasRole(Role::SUPER_USER) ? true : null;
        });
    }
}
