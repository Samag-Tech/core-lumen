<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use SamagTech\CoreLumen\Models\Log;
use SamagTech\CoreLumen\Contracts\Logger;
use SamagTech\CoreLumen\Contracts\Factory;
use SamagTech\CoreLumen\Contracts\Service;
use SamagTech\CoreLumen\Handlers\DBLogger;
use SamagTech\CoreLumen\Models\ServiceKey;
use SamagTech\CoreLumen\Core\BaseRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use SamagTech\CoreLumen\Exceptions\BaseException;
use Illuminate\Http\Resources\Json\ResourceCollection;
use SamagTech\CoreLumen\Exceptions\ValidationException;
use Laravel\Lumen\Routing\Controller as LumenController;

/**
 * Definizione del controller di base con i metodi
 * per la gestione di risorse tramite metodologia CRUD
 *
 * @extends Laravel\Lumen\Routing\Controller as LumenController;
 * @implements Factory
 *
 * @property string $model
 * @property string $defaultService
 * @property Service  $service
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
abstract class BaseController extends LumenController implements Factory {

    /**
     * Modello istanziato
     *
     * @var SamagTech\CoreLumen\Core\BaseRepository;
     *
     * @access private
     *
     */
    private BaseRepository $repository;

    /**
     * Modello che il servizion utilizzerà
     *
     * @var string
     *
     * @access protected
     *
     * Es. Example::class
     */
    protected string $model;

    /**
     * Servizio di default da utilizzare
     *
     * @var string
     *
     * @access protected
     *
     * Es. ExampleService::class
     */
    protected string $defaultService;

    /**
     * Servizio che effettivamente processa
     * le richieste.
     *
     * @var SamagTech\CoreLumen\Contracts\Service
     *
     * @access protected
     */
    protected Service $service;

    //---------------------------------------------------------------------------------------------------

    /**
     * Costruttore.
     *
     */
    public function __construct(ServiceKey $serviceKey) {

        // Per funzionare il modello deve essere impostato
        if ( ! isset($this->model) ) {
            die(__('core.model_not_set'));
        }

        // Per funzionare il servizio di default deve essere impostato
        if ( ! isset($this->defaultService) ) {
            die(__('core.default_service_not_set'));
        }

        // Istanzia il modello
        $this->repository = new $this->model;

        // Recupero il token
        $user = $this->getUser();

        // Implementazione Logger
        app()->singleton(Logger::class, function ($app, $user) {
            return new DBLogger(new Log, $user);
        });

        // Inizializza il servizio da utilizzare
        $this->service = $this->makeService($serviceKey, $user?->app_token);

    }

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritDoc}
     *
     */
    public function makeService(ServiceKey $serviceKey, ?string $token = null): Service {

        /**
         * Controllo se esiste il token e se combacia con un servizio registrato.
         *
         * Se il token è presente allora controllo che ci sia il servizio di riferimento,
         * in caso contrario restituisco il servizio di default.
         *
         */
        if ( ! is_null($token) && ! is_null($key = $serviceKey->find($token)) ) {

            $class = $this->defaultService.$key->suffix;

            if ( class_exists($class) ) {
                return new $class($this->repository, app()->make(Logger::class));
            }

        }

        return new $this->defaultService($this->repository, app()->make(Logger::class));
    }


    //---------------------------------------------------------------------------------------------------

    /**
     * API per la lista di tutte le risorse
     *
     * @access public
     *
     * @param \Illuminate\Http\Request $request Parametri per la ricerca
     *
     * @return Illuminate\Http\Resources\Json\ResourceCollection|Illuminate\Http\JsonResponse
     */
    public function index (Request $request) : ResourceCollection|JsonResponse {

        try {
            $response = $this->service->index($request);
            return respond($response);
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * API per il recupero dei dati di una singola risorsa in base
     * al suo identificativo
     *
     * @access public
     *
     * @param int|string $id    ID della risorsa
     *
     * @return Illuminate\Http\Resources\Json\JsonResource|Illuminate\Http\JsonResponse
     */
    public function show (int|string $id) : JsonResource|JsonResponse {

        try {
            $response = $this->service->show($id);
            return respond($response);
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * API per la creazione di una singola risorsa
     *
     * @access public
     *
     * @param \Illuminate\Http\Request $request     Dati per la creazione di una risorsa
     *
     * @return Illuminate\Http\Resources\Json\JsonResource|Illuminate\Http\JsonResponse
     */
    public function store (Request $request) : JsonResource|JsonResponse {

        try {
            $response = $this->service->store($request);

            return respond($response, 201);
        }
        catch(ValidationException $e ) {
            return respondFail($e->getValidationErrors(), $e->getHttpCode());
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }
    //---------------------------------------------------------------------------------------------------

    /**
     * API per la modifica di una singola risorsa tramite il suo identificativo
     *
     * @access public
     *
     * @param \Illuminate\Http\Request $request     Dati per la modifica di una risorsa
     * @param int|string $id    ID della risorsa da modificare
     *
     * @return Illuminate\Http\Resources\Json\JsonResource|Illuminate\Http\JsonResponse
     */
    public function update (Request $request, int | string $id) : JsonResource|JsonResponse {

        try {
            $updated = $this->service->update($request, $id);

            if ( ! $updated ) {
                return respondFail(__('core.update_failed'));
            }

            return respond(['message' => __('core.update_success')]);
        }
        catch(ValidationException $e ) {
            return respondFail($e->getValidationErrors(), $e->getHttpCode());
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * API per la cancellazione di una risorsa tramite il suo identificativo
     *
     * @access public
     *
     * @param int|string $id    ID della risorsa da cancellare
     *
     * @return Illuminate\Http\Resources\Json\JsonResource|Illuminate\Http\JsonResponse
     */
    public function delete (int|string $id) : JsonResource|JsonResponse {

        try {
            $deleted = $this->service->delete($id);

            if ( ! $deleted ) {
                return respondFail(__('core.delete_failed'));
            }

            return respond(['message' => __('core.delete_success')]);
        }
        catch (BaseException $e ) {
            return respondFail($e->getMessage(), $e->getHttpCode());
        }
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Recupero l'utente
     *
     * @access private
     *
     * @return mixed|null
     */
    private function getUser () {

        try {
            return auth()->user();
        }
        catch (InvalidArgumentException $e) {
            return null;
        }

    }

    //---------------------------------------------------------------------------------------------------
}