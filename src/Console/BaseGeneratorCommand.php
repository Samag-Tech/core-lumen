<?php namespace SamagTech\CoreLumen\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Classe astratta per implementare un comando di generazione di file
 *
 * @abstract
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
abstract class BaseGeneratorCommand extends Command {

    /**
     * Classe per la gestione del filesystem
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    //-----------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * @param Illuminate\Filesystem\Filesystem $filesystem
     *
     */
    public function __construct(Filesystem $filesystem) {
        parent::__construct();
        $this->filesystem = $filesystem;
    }


    //-----------------------------------------------------------------------

    /**
     * Restituisce il path dello stub
     *
     * @abstract
     *
     * @return string
     */
    abstract protected function getStub () : string;

    //-----------------------------------------------------------------------

    /**
     * Restituisce il nome del file
     *
     * @abstract
     *
     * @return string
     */
    abstract protected function getFilename () : string;

    //-----------------------------------------------------------------------

    /**
     * Restituisce il path dove deve essere posizionato il file
     *
     * @abstract
     *
     * @return string
     */
    abstract protected function getFilePath () : string;

    //-----------------------------------------------------------------------

    /**
     * Crea il file
     *
     * @param string $contents  Contenuto del file
     *
     * @return void
     */
    protected function buildFile ( string $contents) : void {

        if ( ! $this->filesystem->exists($this->getFilePath()) ) {
            $this->filesystem->makeDirectory($this->getFilePath(),0755, true, true);
        }

        $this->filesystem->put($this->getFilePath().$this->getFilename(), $contents);
    }

    //-----------------------------------------------------------------------



}