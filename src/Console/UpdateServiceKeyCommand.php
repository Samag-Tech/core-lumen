<?php namespace SamagTech\CoreLumen\Console;

use Illuminate\Console\Command;
use SamagTech\CoreLumen\Models\ServiceKey;

/**
 * Comando per la modifica di un suffisso di una chiave all'interno
 * della tabella delle chiavi dei servizi
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class UpdateServiceKeyCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:update-service-key {suffix} {--key=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modifica il suffisso di una chiave';

    /**
     * Execute the console command.
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle(ServiceKey $serviceKey) {

        $suffix = $this->argument('suffix');

        $key = $this->option('key');

        if ( is_null($key)) {
            $this->error('Deve essere impostata la chiave con l\'opzione --key=XXX');
        }

        $service = $serviceKey->find($key);

        $service->suffix = $suffix;

        $service->save();

        $this->info("Chiave per il suffisso $suffix : $service->id");
    }
}