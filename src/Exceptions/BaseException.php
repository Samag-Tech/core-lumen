<?php namespace SamagTech\CoreLumen\Exceptions;

use Exception;

/**
 * Eccezione di base da utilizzare per automatizzare le
 * eccezioni tramite configurazioni.
 *
 * Per utilizzare questa classe bisogna estenderla ed impostare
 * le variabili $messageCustom e $httpCode
 *
 * @abstract
 *
 * @property string $messageCustom  Messaggio custom mostrato se non viene passato il messaggio al costruttore
 * @property int    $httpCode       Codice di risposta, di default impostato il codice 400 che si riferisce alla BAD REQUEST
 *
 * @method int getHttpCode()
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
abstract class BaseException extends Exception {

    /**
     * Messaggio custom di default
     *
     * @var string
     * @access protected
     */
    protected string $messageCustom = '';

    /**
     * Codice di risposta di default
     *
     * @var int
     * @access protected
     */
    protected int $httpCode = 400;

    /**
     * Costruttore.
     *
     * @param string|null   $message    Sovrascrive il messaggio di default
     * @param int           $httpCode   Codice di stato
     *
     */
    public function __construct(?string $message = null, int $httpCode = 400) {

        $message = $message ?? $this->messageCustom;
        $this->httpCode = $httpCode;

        parent::__construct($message);
    }

    /**
     * Restituisce il codice di stato
     *
     * @access public
     * @return int
     */
    public function getHttpCode() : int {
        return $this->httpCode;
    }

}