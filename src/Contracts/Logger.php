<?php namespace SamagTech\CoreLumen\Contracts;

use SamagTech\CoreLumen\Core\BaseService;

/**
 * Definizione di un interfaccia per il logger dell'applicazione
 *
 * @interface
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v1.1
 */
interface Logger {

    //-----------------------------------------------------------------------

    /**
     * Imposta il service da loggare
     *
     * @param BaseService $service
     *
     * @return void
     */
    public function setService (BaseService $service) : void;

    //-----------------------------------------------------------------------

    /**
     * Funzione di scrittura del log
     *
     * @param string $type      Tipo di log
     * @param int|string $rowId Identificativo riga da loggare
     * @param mixed $old_data   Dati di creazione/Vecchi dati
     * @param mixed $new_data   Dati se la riga viene modificata (Default null)
     *
     * @return void
     */
    public function write (string $type, int|string $rowId, $old_data, $new_data = null) : void;

    //-----------------------------------------------------------------------

}
