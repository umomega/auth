<?php

namespace Umomega\Auth\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/auth.php', 'auth'
        );
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->publishes([__DIR__ . '/../../config/auth.php' => config_path('auth.php')], 'config');

        $this->publishes([__DIR__ . '/../../resources/lang' => resource_path('lang/vendor/auth')], 'lang');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'auth');

        $this->publishes([__DIR__ . '/../../resources/views' => resource_path('views/vendor/auth')], 'views');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'auth');

        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
    }

}
