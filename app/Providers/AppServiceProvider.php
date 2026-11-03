<?php

namespace App\Providers;

use App\Support\FakeGeocoder;
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
        // Tests und Dusk: Geocoding ohne Google (Fassade, Container und Helper::getGeocoder)
        if (config('geocoder.fake')) {
            $this->app->bind('geocoder', fn () => new FakeGeocoder);
        }

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
    public function boot() {}
}
