<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Events\OrderShipped;
use App\Listeners\SendOrderNotification;
use App\Listeners\TrackOrderAnalytics;
use App\Listeners\UpdateInventory;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations (Repository Pattern)
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Event Listeners (Observer Pattern)
        Event::listen(OrderCreated::class, [SendOrderNotification::class, 'handle']);
        Event::listen(OrderCreated::class, [TrackOrderAnalytics::class, 'handle']);

        Event::listen(OrderPaid::class, [SendOrderNotification::class, 'handle']);
        Event::listen(OrderPaid::class, [UpdateInventory::class, 'handle']);
        Event::listen(OrderPaid::class, [TrackOrderAnalytics::class, 'handle']);

        Event::listen(OrderShipped::class, [SendOrderNotification::class, 'handle']);
    }
}
