<?php

namespace Microservices;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Event;
use Microservices\models\Microservices;
use Microservices\Facade\Microservices as MicroservicesFacade;

class MicroservicesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // User Provider
        Event::listen(
            \Microservices\Events\BusEvent::class,
            [\Microservices\Listeners\BusListener::class, 'handle']
        );
        $loader = AliasLoader::getInstance();
        $loader->alias('Microservices', MicroservicesFacade::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Facades
        $this->app->singleton('Microservices', function ($app) {
            return $app->make(Microservices::class);
        });
        //$this->app->alias('Microservices',MicroservicesFacade::class);
    }
}
