<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NamecheapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('namecheap', function() {
            return new \App\Api\Namecheap\NamecheapApi();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
