<?php namespace SamagTech\CoreLumen\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Classe astratta per la creazione di un generatore di provider
 *
 * @abstract
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
abstract class BaseGeneratorProvider extends ServiceProvider {

    /**
     * Prefisso da posizionare davanti ai binding
     *
     * @var string
     *
     * @access private
     */
    private string $prefixBinding = 'command.core.';

    /**
     * Lista di comandi da abilitare
     *
     * Es. [
     *  'ComandoDaLancia' => 'binding'
     * ]
     *
     * @var array
     */
    protected array $commands = [];

    //-----------------------------------------------------------------------

    /**
     * Registrazione del provider
     *
     * @override
     */
    public function register() {
        $this->registerCommands($this->commands);
    }

    //-----------------------------------------------------------------------

    /**
     * Funzione per la registrazione dei comandi
     *
     * @param array $commands   Array dei comandi
     *
     * @return void
     */
    public function registerCommands(array $commands) : void {

        foreach ($commands as &$value) {
            $value = $this->prefixBinding.$value;
        }

        // Gestisce i comandi
        foreach ( array_keys($commands) as $command) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], []);
        }

        // Registra i comandi
        $this->commands(array_values($commands));
    }

    //-----------------------------------------------------------------------

    /**
     * Restituisce il prefisso del binding
     *
     * @return string
     */
    protected function getPrefixBinding() : string {
        return $this->prefixBinding;
    }

    //-----------------------------------------------------------------------

}