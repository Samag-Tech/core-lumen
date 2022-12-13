<?php namespace SamagTech\CoreLumen\Console;

use Illuminate\Console\Command;
use SamagTech\CoreLumen\Models\ServiceKey;

/**
 * Comando per la generazione di una nuova chiave all'interno
 * della tabella delle chiavi dei servizi
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class AddServiceKeyCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:add-service-key {suffix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggiunge una nuova chiave per un servizio';

    /**
     * Execute the console command.
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle(ServiceKey $serviceKey) {

        $suffix = $this->argument('suffix');

        $confirm = $this->confirm('Vuoi generare una chiave randominca?', true);

        if ( $confirm ) {
            $key = $serviceKey->create(['suffix' => $suffix]);
        }
        else {

            $id = $this->ask('Inserisci la chiave: ');

            $key = $serviceKey->create([
                'id'        => $id,
                'suffix'    => $suffix
            ]);
        }


        $this->info("Chiave per il suffisso $suffix : $key->id");
    }
}