<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SedoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->bind('namecheapapi', function() {
//            return new \App\Api\Namecheap\NamecheapApi();
//        });
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
