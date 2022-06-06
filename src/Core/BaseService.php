<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Http\Request;
use SamagTech\CoreLumen\Contracts\Service;
use SamagTech\CoreLumen\Traits\WithValidation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use SamagTech\CoreLumen\Traits\RequestCleanable;
use SamagTech\CoreLumen\Exceptions\ResourceNotFoundException;

abstract class BaseService implements Service {
    use WithValidation, RequestCleanable;


    protected string $jsonResource;

    protected BaseModel $repository;

    protected array $genericRules = [];
    protected array $insertRules = [];
    protected array $updateRules = [];
    protected array $deleteRules = [];

    //---------------------------------------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * @param BaseModel $repository     Modello per la gestione dei dati
     */
    public function __construct(BaseModel $repository) {
        $this->repository = $repository;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function show (int|string $id) : JsonResource|array {

        $resource = $this->repository->find($id);

        // Se la risorsa non esiste allora sollevo un eccezione
        if ( is_null($resource) ) {
            throw new ResourceNotFoundException();
        }

        // Applico una callback
        $resource = $this->afterRetrieveById($resource, $id);

        return new $this->jsonResource($resource);
    }


    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function store (Request $request) : JsonResource|array {

        // Imposto e lancio le validazioni
        $this->setValidations($this->genericRules, $this->insertRules);

        if ( ! $this->runValidation($request) ) {
            throw new ValidationException($this->getValidationErrors());
        }

        // Pulisco la richiesta
        $data = $this->cleanRequest($request);

        // Lancio la callback prima di inserire i dati
        $data = $this->beforeInsert($data);

        // Recupera eventuali relazioni
        $relations = $this->getRelations($data);

        // Crea la risorsa
        $resource = $this->repository->create($data);

        // Lancio la callback dopo aver creato le risorse
        $relations = $this->afterInsert($resource, $relations);

        // Creo evenutali relazioni se sono presenti
        if ( ! empty ($relations) ) {

            foreach ($relations as $relation => $values) {
                $resource->{$relation}()->createMany($values);
            }
        }

        return new $this->jsonResource($resource);
    }

    //---------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------------------------------------------

    /**
     * Callback eseguita post recupero della risorsa.
     *
     * Deve essere sovrascritta per essere utilizzata e serve per eseguire
     * ulteriori operazioni prima dell'invio della risposta.
     *
     * @access protected
     *
     * @param BaseModel     $resource   Risorsa appena recuperata
     * @param int|string    $id         ID della risorsa recuperata
     *
     * @return BaseModel
     */
    protected function afterRetrieveById (BaseModel $resource, int|string $id) : BaseModel {
        return $resource;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Callback eseguita prima della creazione della risorsa
     *
     * Deve essere sovrascritta per essere utilizzata e serve per eseguire
     * ulteriori operazioni sui dati prima di essere inseriti
     *
     * @access protected
     *
     * @param array $data   Dati estratti dalla richiesta
     *
     * @return array    Restituisce i dati creare la risorsa
     */
    protected function beforeInsert(array $data) : array {
        return $data;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Callback eseguita dopo la creazione della risorsa
     *
     * Deve essere sovrascritta per essere utilizzata e serve per eseguire
     * ulteriori operazioni sulle relazioni prima di essere inserite
     *
     * @access protected
     *
     * @param BaseModel $resource   Risorsa appena creata
     * @param array $relations  Relazione recuperate dalla funzione getRelations()
     *
     * @return array    Le relazioni modificate
     */
    protected function afterInsert(BaseModel $resource, array $relations) : array {
        return $relations;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Recupera la lista delle relazioni da inserire e pulisce
     * l'array contenente i dati della richiesta.
     *
     * Se l'array $data è formato in questo modo:
     *  [
     *      'key' => 'value',
     *      'key1' => 'value1',
     *      'key3' => [
     *          'subkey' => 'subvalue'
     *       ]
     * ]
     *
     * Restituisce i dati della richiesta senza la chiave 'key3'
     * e tale chiave verrà considerata una relazione che verrà inserita post
     * creazione della risorsa.
     *
     * @access private
     *
     * @param array &$data  Dati della richiesta
     *
     * @return array    Dati della richiesta puliti
     */
    private function getRelations(array &$data) : array {

        $relations = [];

        // Ciclo l'array data per trovare sottoarray,
        // se ci sono li estraggo
        foreach ( $data as $key => $value ) {

            if ( is_array($value) ) {
                $relations[$key] = $value;
                unset($data[$key]);
            }
        }

        return $relations;
    }

    //---------------------------------------------------------------------------------------------------
}
