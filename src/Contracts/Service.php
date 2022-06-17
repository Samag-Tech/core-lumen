<?php namespace SamagTech\CoreLumen\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
    public function index (Request $request) : JsonResource|array;

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce i dati di una singola risorsa.
     *
     * @access public
     * @param int|string $id    ID della risorsa
     * @throws SamagTech\CoreLumen\Exceptions\ResourceNotFoundException;
     *
     * @return JsonResource|array<string,mixed>
     */
    public function show (int|string $id) : JsonResource|array;

    //---------------------------------------------------------------------------------------------------

    /**
     * Crea una nuova risorsa.
     *
     * Esegue la logica di creazione di un una risorsa in base alla richiesta
     * e, in caso di successo, restituisce l'oggetto creato.
     *
     * @param \Illuminate\Http\Request $request      Dati per la creazione della risorsa
     *
     * @throws SamagTech\CoreLumen\Exceptions\CreateException
     *
     * @return array<string,mixed>  Dati della risorsa appena creata
     */
    public function store (Request $request) : JsonResource|array;

    //---------------------------------------------------------------------------------------------------

    /**
     * Modifica di una risorsa.
     *
     * @param \Illuminate\Http\Request $request      Dati per la modifica della risorsa
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