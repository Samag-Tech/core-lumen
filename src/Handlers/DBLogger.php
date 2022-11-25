<?php namespace SamagTech\CoreLumen\Handlers;

use SamagTech\CoreLumen\Models\Log;
use SamagTech\CoreLumen\Models\System;
use SamagTech\CoreLumen\Contracts\Logger;
use SamagTech\CoreLumen\Core\BaseService;

/**
 * Implementazione del logger nel database.
 *
 * @implements Logger
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 *
 * @since v1.1
 */
class DBLogger implements Logger {

    /**
     * Modello per la gestione dei log
     *
     * @access private
     *
     * @var Log
     */
    private Log $log;

    /**
     * Servizio che genera i log
     *
     * @access private
     *
     * @var BaseService
     */
    private BaseService $service;

    /**
     * Utente corrente che genera il log
     *
     * @access private
     *
     * @var mixed
     */
    private $user;

    //-----------------------------------------------------------------------

    /**
     * Modello per la gestione delle opzioni di sistema
     *
     * @var System
     *
     * @access private
     */
    private System $system;

    //-----------------------------------------------------------------------

    /**
     * Costruttore
     *
     * @param Log $log  Modello
     * @param mixed $user   Utente loggato
     */
    public function __construct(Log $log, $user = null) {
        $this->log = $log;
        $this->user = $user;

        $this->system = app()->make(System::class);
    }

    //-----------------------------------------------------------------------

    /**
     * Imposta l'utente nei log
     *
     * @param object|array|null $user
     */
    public function setUser (object|array|null $user) : void {
        $this->user = $user;
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     *
     */
    public function setService ( BaseService $service) : void {
        $this->service = $service;
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     *
     */
    public function write (string $type, int|string $rowId, $old_data, $new_data = null) : void {

        if ( ! $this->system->find('logger')?->value ) {
            return;
        }

        $this->log->create([
            'table'      => $this->service->getRepository()->getTable(),
            'row_id'     => $rowId,
            'service'    => get_class($this->service),
            'old_data'   => json_encode($old_data),
            'new_data'   => is_null($new_data) ? null : json_encode($new_data),
            'type'       => $type,
            'user'       => is_null($this->user) ? null : json_encode($this->user)
        ]);

    }

    //-----------------------------------------------------------------------
}