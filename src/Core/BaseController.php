<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use SamagTech\CoreLumen\Contracts\Factory;
use SamagTech\CoreLumen\Contracts\Service;
use SamagTech\CoreLumen\Exceptions\BaseException;
use Laravel\Lumen\Routing\Controller as LumenController;

abstract class BaseController extends LumenController implements Factory {

    private BaseRepository $repository;

    protected string $model;

    protected string $defaultService;

    protected Service $service;


    public function __construct() {

        if ( ! isset($this->model) ) {
            die('Modello non impostato');
        }

        if ( ! isset($this->defaultService) ) {
            die('Servizio di default non impostato');
        }

        $this->repository = new $this->model;

        $this->service = $this->makeService();
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritDoc}
     *
     */
    public function makeService(?string $token = null): Service {

        // @TODO Gestione multi-service

        return new $this->defaultService($this->repository);
    }


    //---------------------------------------------------------------------------------------------------


    public function index (Request $request) : JsonResponse {

        try {
            $response = $this->service->index($request);

            return respond($response);
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }

    //---------------------------------------------------------------------------------------------------


    public function show (int|string $id) : JsonResponse {

        try {
            $response = $this->service->show($id);

            return respond($response);
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }

    //---------------------------------------------------------------------------------------------------


    public function store (Request $request) : JsonResponse {

        try {
            $response = $this->service->store($request);

            return respond($response, 201);
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }
    //---------------------------------------------------------------------------------------------------


    public function update (Request $request, int | string $id) : JsonResponse {

        try {
            $updated = $this->service->update($request, $id);

            if ( ! $updated ) {
                return respondFail('Errore durante la modifica');
            }

            return respond(['message' => 'Modifica effettuata con successo']);
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }

    //---------------------------------------------------------------------------------------------------


    public function delete (int|string $id) : JsonResponse {

        try {
            $deleted = $this->service->delete($id);

            if ( ! $deleted ) {
                return respondFail('Errore durante la cancellazione');
            }

            return respond(['message' => 'Cancellazione effettuata con successo']);
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }

    //---------------------------------------------------------------------------------------------------
}