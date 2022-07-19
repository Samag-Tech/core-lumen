<?php namespace SamagTech\CoreLumen\Providers;

use Illuminate\Support\ServiceProvider;
use SamagTech\CoreLumen\Console\ServiceKeyTableCommand;

class Generator extends ServiceProvider {

    protected $commands = [
        'ServiceKeyTable'   => 'command.core.servicekeytable'
    ];

    public function register() {
        $this->registerCommands($this->commands);
    }

    public function registerCommands(array $commands) {

        foreach ( array_keys($commands) as $command) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], []);
        }

        $this->commands(array_values($command));
    }

    public function registerServiceKeyTableCommand() {

        app()->singleton('command.core.servicekeytable', function ($app) {
            return new ServiceKeyTableCommand($app['files']);
        });
    }

}