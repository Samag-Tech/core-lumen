<?php namespace SamagTech\CoreLumen\Providers;

use SamagTech\CoreLumen\Models\Log;
use SamagTech\CoreLumen\Models\System;
use Illuminate\Support\ServiceProvider;
use SamagTech\CoreLumen\Contracts\Logger;
use SamagTech\CoreLumen\Handlers\DBLogger;
use SamagTech\CoreLumen\Models\ServiceKey;

/**
 * Provider per caricamenti generici
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class GenericProvider extends ServiceProvider {


    public function register() {

        app()->bind(ServiceKey::class, function ($app) {
            return new ServiceKey();
        });

        app()->bind(System::class, function ($app) {
            return new System();
        });

        // Implementazione Logger
        app()->singleton(Logger::class, function ($app) {
            return new DBLogger(new Log);
        });
    }

}