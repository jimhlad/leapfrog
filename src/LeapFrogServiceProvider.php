<?php

namespace JimHlad\LeapFrog;

use Illuminate\Support\ServiceProvider;

class LeapFrogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Publishes/Config/leapfrog.php' => config_path('leapfrog.php'),
        ]);

        $this->publishes([
            __DIR__.'/Publishes/Routes/leapfrog.php' => base_path('routes/leapfrog.php'),
        ]);

        $this->publishes([
            __DIR__.'/Publishes/Assets' => public_path('jimhlad/leapfrog'),
        ], 'public');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('JimHlad\LeapFrog\Controllers\DashboardController');
        $this->app->make('JimHlad\LeapFrog\Controllers\CrudController');
        $this->loadViewsFrom(__DIR__.'/Views', 'leapfrog');
    }
}
