<?php

namespace CaueSantos\LaravelModelUtils;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/laravel-model-utils.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('laravel-model-utils.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'laravel-model-utils'
        );

        $this->app->bind('laravel-model-utils', function () {
            return new LaravelModelUtils();
        });
    }
}
