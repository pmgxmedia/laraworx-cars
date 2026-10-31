<?php

namespace App\Providers;

use App\Events\PurchaseClaimStatusChanged;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureUserIsActive;
use App\Listeners\NotifyClaimInterestedParties;
use App\Models\User;
use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\GatewayManager;
use App\Services\Payments\MockPayoutGateway;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentGateway::class, function ($app) {
            return new GatewayManager([
                'manual' => new MockPayoutGateway,
                'mock' => new MockPayoutGateway,
            ]);
        });
    }

    public function boot(): void
    {
        Event::listen(
            PurchaseClaimStatusChanged::class,
            NotifyClaimInterestedParties::class
        );

        Gate::before(function (User $user, string $permission) {
            return $user->hasPermission($permission);
        });

        $this->app['router']->aliasMiddleware('active', EnsureUserIsActive::class);
        $this->app['router']->aliasMiddleware('admin', EnsureAdmin::class);
        $this->app['router']->aliasMiddleware('role', EnsureRole::class);
    }
}