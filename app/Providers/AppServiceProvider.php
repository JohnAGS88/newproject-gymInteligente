<?php

namespace App\Providers;

use App\Models\User;
use App\Services\DynamicQrService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DynamicQrService::class, static fn (): DynamicQrService => DynamicQrService::withAppKey());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // superadmin tiene paso libre; el resto pasa por las Policies.
        Gate::before(static function (User $user, string $ability): ?bool {
            return $user->hasRole('superadmin') ? true : null;
        });
    }
}