<?php namespace SamagTech\CoreLumen\Console;

use Illuminate\Support\Facades\Artisan;
use SamagTech\CoreLumen\Models\System;

/**
 * Comando per il setup iniziale della gestione delle
 * configurazioni di sistema.
 *
 * @extends BaseGeneratorCommand
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 *
 * @since v1.1
 */
class SetupSystemCommand extends BaseGeneratorCommand {

    protected $name = 'core:setup-system';

    protected $description = 'Crea ed inizializza la tabella di sistema';

    //-----------------------------------------------------------------------

    public function handle(System $system) {

        $filename = $this->getFilename();

        $this->filesystem->put($this->getFilePath().$filename, $this->filesystem->get($this->getStub()));

        $this->info('Migrazione system creata');

        Artisan::call('migrate', ['--path' => $this->getFilePath().$filename]);

        $this->info('Migrazione lanciata');

        $system->insert([
            ['option'   => 'maintenance', 'value' => 0],
            ['option'   => 'logger', 'value' => 1]
        ]);

        $this->info('Creazione configurazione effettuata');


    }

    //-----------------------------------------------------------------------

    public function getStub () : string {
        return __DIR__.'/stubs/systemtable.stub';
    }

    //-----------------------------------------------------------------------

    public function getFilename () : string {
        return date('Y_m_d_His').'_create_system_table.php';
    }

    //-----------------------------------------------------------------------

    public function getFilePath () : string {
        return app()->basePath('database/migrations/');
    }

    //-----------------------------------------------------------------------


}