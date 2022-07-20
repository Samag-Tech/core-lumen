<?php namespace SamagTech\CoreLumen\Providers;

use SamagTech\CoreLumen\Console\AddServiceKeyCommand;
use SamagTech\CoreLumen\Console\ServiceKeyTableCommand;

/**
 * Implemetazione del generatore
 *
 * @extends BaseGeneratorProvider
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class GeneratorProvider extends BaseGeneratorProvider {

    protected array $commands = [
        'ServiceKeyTable'   => 'servicekeytable',
        'AddServiceKey'     => 'addservicekey'
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

    /**
     * Registra il comando per la creazione di una nuova chiave della tabella
     * 'services_keys'
     *
     * @return void
     */
    public function registerAddServiceKeyCommand() : void {

        app()->singleton($this->getPrefixBinding().'addservicekey', function ($app) {
            return new AddServiceKeyCommand();
        });

    }

    //-----------------------------------------------------------------------
}