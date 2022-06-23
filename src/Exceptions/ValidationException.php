<?php namespace SamagTech\CoreLumen\Exceptions;

use SamagTech\CoreLumen\Exceptions\BaseException;

/**
 * Eccezione per la segnalazione degli errori
 * di validazione
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
class ValidationException extends BaseException {

    /**
     * Attributo per la gestione degli errori
     *
     * @var string|array
     * @access private
     */
    private array|string $errors;

    /**
     * Codice di risposta di default
     *
     * @var int
     * @access protected
     */
    protected int $httpCode = 422;

    //---------------------------------------------------------------------------------------------------

    /**
     * Costruttore.
     *
     */
    public function __construct(array|string $errors) {
        $this->errors = $errors;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce gli errori di validazione
     *
     */
    public function getValidationErrors() : array|string {
        return $this->errors;
    }
}