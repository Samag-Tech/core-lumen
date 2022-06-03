<?php namespace SamagTech\CoreLumen\Contracts;

/**
 * Definizione di una classe per la validazione di
 * una richiesta.
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
interface ValidationRequest {

    //---------------------------------------------------------------------------------------------------

    /**
     * Esecuzione della validazione della richiesta.
     *
     * @return void
     */
    public function run () : void;

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce gli errori di validazione
     *
     * @return array<string,array>
     */
    public function getErrors() : array;

    //---------------------------------------------------------------------------------------------------

    /**
     * Indica se la richiesta è valida
     *
     * @return bool TRUE se la richiesta è valida, FALSE altrimenti
     */
    public function isValid() : bool;

    //---------------------------------------------------------------------------------------------------
}