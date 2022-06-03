<?php namespace SamagTech\CoreLumen\Contracts;

use Illuminate\Http\Request;

/**
 * Definizione di un service.
 *
 * Un service è il servizio che viene chiamato dal controller
 * per svolgere le operazioni desiderate in base al chiamante.
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 *
 */
interface Service {

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista delle risorse in base ai parametri delle richiesta.
     *
     * @param Illuminate\Http\Request $request      Dati della richiesta
     *
     * @return array<int,array<string,mixed>
     */
    public function index (Request $request) : array;

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce i dati di una singola risorsa
     *
     * @param int|string $id    ID della risorsa
     *
     * @return array<string,mixed>
     */
    public function show (int|string $id) : array;

    //---------------------------------------------------------------------------------------------------

    /**
     * Crea una nuova risorsa.
     *
     * @param Illuminate\Http\Request $request      Dati per la creazione della risorsa
     *
     * @return array<string,mixed>  Dati della risorsa appena creata
     */
    public function store (Request $request) : array;

    //---------------------------------------------------------------------------------------------------

    /**
     * Modifica di una risorsa.
     *
     * @param Illuminate\Http\Request $request      Dati per la modifica della risorsa
     * @param int|string    $id     ID della risorsa da modificare
     *
     * @return bool     TRUE se la risorsa è stata modificata, FALSE altrimenti
     */
    public function update (Request $request, int | string $id) : bool;

    //---------------------------------------------------------------------------------------------------

    /**
     * Cancellazione di una risorsa.
     *
     * @param int|string    $id     ID della risorsa da cancellare
     *
     * @return bool     TRUE se la risorsa è stata modificata, FALSE altrimenti
     */
    public function delete (int|string $id) : bool;

    //---------------------------------------------------------------------------------------------------

}