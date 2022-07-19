<?php namespace SamagTech\CoreLumen\Providers;

use SamagTech\CoreLumen\Console\ServiceKeyTableCommand;

/**
 * Implemetazione del generatore
 *
 * @extends BaseGeneratorProvider
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class GeneratorProvider extends BaseGeneratorProvider {

    protected $commands = [
        'ServiceKeyTable'   => 'servicekeytable'
    ];

    //-----------------------------------------------------------------------

    /**
     * Registra il comando per la creazione della migrazione della tabella
     * 'services_keys'
     *
     * @return void
     */
    public function registerServiceKeyTableCommand() : void {

        app()->singleton($this->getPrefixBinding().'servicekeytable', function ($app) {
            return new ServiceKeyTableCommand($app['files']);
        });

    }

    //-----------------------------------------------------------------------
}