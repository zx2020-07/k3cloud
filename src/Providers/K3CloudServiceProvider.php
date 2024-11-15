<?php

namespace Glitter\K3Cloud\Providers;

use Glitter\K3Cloud\K3CloudApiSdk;
use Illuminate\Support\ServiceProvider;

class K3CloudServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/k3cloud.php', 'k3cloud');

        $this->app->singleton('k3cloud', function () {
            return new K3CloudApiSdk(config('k3cloud'));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/k3cloud.php' => config_path('k3cloud.php'),
        ], 'config');
    }
}
