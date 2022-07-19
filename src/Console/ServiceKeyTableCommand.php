<?php namespace SamagTech\CoreLumen\Console;


/**
 * Comando per la generazione della tabella dei servizi
 *
 * @extends BaseGeneratorCommand
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class ServiceKeyTableCommand extends BaseGeneratorCommand {

    protected $name = 'core:service-key-table';

    protected $description = 'Crea la tabella con le chiavi dei vari servizi';

    //-----------------------------------------------------------------------

    public function handle() {
        $this->buildFile($this->filesystem->get($this->getStub()));
    }

    //-----------------------------------------------------------------------

    public function getStub () : string {
        return __DIR__.'/stubs/servicekeytable.stub';
    }

    //-----------------------------------------------------------------------

    public function getFilename () : string {
        return date('Y_m_d_His').'_create_services_keys_table.php';
    }

    //-----------------------------------------------------------------------

    public function getFilePath () : string {
        return app()->basePath('database/migrations/');
    }

    //-----------------------------------------------------------------------


}