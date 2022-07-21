<?php namespace SamagTech\CoreLumen\Console;

use Illuminate\Console\Command;
use SamagTech\CoreLumen\Models\System;

/**
 * Modifica il valore di un impostazione di sistema
 *
 * @extends BaseGeneratorCommand
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 *
 * @since v1.1
 */
class UpdateOptionSystemCommand extends Command {

    protected $signature = 'core:update-option-system {--option=} {--value=}';

    protected $description = 'Modifica un opzione di sistema';

    //-----------------------------------------------------------------------

    public function handle(System $system) {

        $option = $this->option('option');
        $value = $this->option('value');

        if ( is_null($option)) {
            $this->error('Deve essere impostata la chiave con l\'opzione da modificare');
            return;
        }

        if ( is_null($value)) {
            $this->error('Deve essere impostato il valore dell\'opzione');
            return;
        }

        $row = $system->find($option);

        if ( is_null($row) ) {
            $this->error('L\'opzione non esiste');
            return;
        }

        $row->value = $value;

        $row->save();

        $this->info('Opzione modificata');

    }

}