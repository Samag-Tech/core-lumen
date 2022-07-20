<?php namespace SamagTech\CoreLumen\Console;


/**
 * Comando per la generazione della tabella dei logs
 *
 * @extends BaseGeneratorCommand
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class LogsTableCommand extends BaseGeneratorCommand {

    protected $name = 'core:logs-table';

    protected $description = 'Crea la tabella dei log';

    //-----------------------------------------------------------------------

    public function handle() {
        $this->buildFile($this->filesystem->get($this->getStub()));
    }

    //-----------------------------------------------------------------------

    public function getStub () : string {
        return __DIR__.'/stubs/logtable.stub';
    }

    //-----------------------------------------------------------------------

    public function getFilename () : string {
        return date('Y_m_d_His').'_create_logs_table.php';
    }

    //-----------------------------------------------------------------------

    public function getFilePath () : string {
        return app()->basePath('database/migrations/');
    }

    //-----------------------------------------------------------------------


}