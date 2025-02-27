<?php

namespace App\Providers;

use App\Mail\UserCreated;
use App\Models\Product;
use App\Models\User;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        Schema::defaultStringLength(191); // 5.6이하의 MySQL과 utf8mb4 인코딩을 사용할 때 발생하는 문제를 해결 -> mysql8을 사용하게 되어 필요하지 않음

        Product::observe(ProductObserver::class);
        User::observe(UserObserver::class);
    }
}
