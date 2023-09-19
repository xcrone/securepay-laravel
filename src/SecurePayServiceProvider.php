<?php

namespace Glsb\SecurePay;

use Illuminate\Support\ServiceProvider;

class SecurePayServiceProvider extends ServiceProvider
{
    /**
     * Register the service.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/securepay.php', 'securepay');
    }

    /**
     * Bootstrap the service.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/securepay.php' => config_path('securepay.php'),
        ]);
    }
}
