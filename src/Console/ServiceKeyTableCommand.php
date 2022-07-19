<?php namespace SamagTech\CoreLumen\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ServiceKeyTableCommand extends Command {

    protected $name = 'core:service-key-table';

    protected $description = 'Crea la tabella con le chiavi dei vari servizi';

    protected Filesystem $filesystem;

    public function __construct(Filesystem $filesystem) {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function handle() {

        $stub = $this->filesystem->get(__DIR__.'stubs/servicekeytable.stub');

        $path = app()->basePath('database/migrations/').date('Y_m_d_His').'_create_services_keys_table.php';

        $this->filesystem->put($path, $stub);
    }
}