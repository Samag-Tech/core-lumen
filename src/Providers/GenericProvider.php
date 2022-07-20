<?php namespace SamagTech\CoreLumen\Providers;

use Illuminate\Support\ServiceProvider;
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
    }

}