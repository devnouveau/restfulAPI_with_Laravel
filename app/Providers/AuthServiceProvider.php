<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addDays(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

        Passport::enableImplicitGrant();

        Passport::tokensCan([
            'purchase-product' => 'Create a new transaction for a specific product',
            'manage-products' =>  'Create, reade, update, and delete products',
            'manage-account' => 'Read your account data, id, name, email, if verified and admin. Modify your account data. Cannot delete your account',
            'read-general' => 'Read general information like purchased products, selling products, your transactions (purchases and sales)',
        ]);
    }
}
