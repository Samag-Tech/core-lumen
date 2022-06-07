<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Http\Request;
use SamagTech\CoreLumen\Contracts\Service;
use SamagTech\CoreLumen\Core\BaseRepository;
use Illuminate\Validation\ValidationException;
use SamagTech\CoreLumen\Traits\WithValidation;
use Illuminate\Http\Resources\Json\JsonResource;
use SamagTech\CoreLumen\Traits\RequestCleanable;
use SamagTech\CoreLumen\Exceptions\ResourceNotFoundException;

abstract class BaseService implements Service {
    use WithValidation, RequestCleanable;

    private string $tag;

    protected string $jsonResource;

    /**
     * @var SamagTech\CoreLumen\Core\BaseRepository
     */
    protected BaseRepository $repository;

    protected array $genericRules = [];
    protected array $insertRules = [];
    protected array $updateRules = [];

    //---------------------------------------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * @param SamagTech\CoreLumen\Core\BaseRepository $repository     Modello per la gestione dei dati
     */
    public function __construct(BaseRepository $repository) {
        $this->repository = $repository;

        // Imposto il tag per i log
        $this->tag = get_class($this);
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function show (int|string $id) : JsonResource|array {

        $resource = $this->repository->find($id);

        info($this->tag. ': Recupero della risorsa', ['id' => $id]);

        // Se la risorsa non esiste allora sollevo un eccezione
        if ( is_null($resource) ) {

            info($this->tag. ': La risorsa non esiste', ['id' => $id]);

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

        info($this->tag. ': Richiesta creazione della risorsa', ['request' => $request]);

        // Imposto e lancio le validazioni
        $this->setValidations($this->genericRules, $this->insertRules);

        if ( ! $this->runValidation($request) ) {

            info($this->tag. ': Errore di validazione', ['request' => $request, ['errors' => $this->getValidationErrors()]]);

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

        info($this->tag. ': Creazione della risorsa', ['resource' => $resource]);

        // Lancio la callback dopo aver creato le risorse
        $relations = $this->afterInsert($resource, $relations);

        // Creo evenutali relazioni se sono presenti
        if ( ! empty ($relations) ) {

            foreach ($relations as $relation => $values) {

                $resource->{$relation}()->createMany($values);

                info($this->tag. ': Creazione delle relazione', ['resource' => $resource, 'relation' => $relation]);
            }
        }

        return new $this->jsonResource($resource);
    }

    //---------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function delete (int|string $id) : bool {

        info($this->tag. ': Cancellazione risorsa', ['id' => $id]);

        $resource = $this->repository->find($id);

        // Se la risorsa non esiste allora sollevo un eccezione
        if ( is_null($resource) ) {

            info($this->tag. ': Risorsa non trovata per la cancellazione', ['id' => $id]);

            throw new ResourceNotFoundException();
        }

        // Lancio una callback prima della cancellazione
        $this->beforeDelete($resource, $id);

        $deleted = $resource->delete();

        // Lancio una callback dopo la cancellazione
        $this->afterDelete($resource, $id);

        if ( ! $deleted ) {
            error($this->tag. ': Errore durante la cancellazione della risorsa', ['id' => $id]);
        }

        return $deleted;
    }


    //---------------------------------------------------------------------------------------------------

    /**
     * Callback eseguita post recupero della risorsa.
     *
     * Deve essere sovrascritta per essere utilizzata e serve per eseguire
     * ulteriori operazioni prima dell'invio della risposta.
     *
     * @access protected
     *
     * @param \SamagTech\CoreLumen\Core\BaseRepository     $resource   Risorsa appena recuperata
     * @param int|string    $id         ID della risorsa recuperata
     *
     * @return SamagTech\CoreLumen\Core\BaseRepository
     */
    protected function afterRetrieveById (BaseRepository $resource, int|string $id) : BaseRepository {
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
     * @param \SamagTech\CoreLumen\Core\BaseRepository $resource   Risorsa appena creata
     * @param array $relations  Relazione recuperate dalla funzione getRelations()
     *
     * @return array    Le relazioni modificate
     */
    protected function afterInsert(BaseRepository $resource, array $relations) : array {
        return $relations;
    }

    //---------------------------------------------------------------------------------------------------



    /**
     * Callback eseguita prima della cancellazione di una risorsa
     *
     * Deve essere sovrascritta per essere utilizzata e serve per eseguire
     * ulteriori operazioni sulle relazioni prima che una risorsa venga cancellata.
     *
     * @access protected
     *
     * @param \SamagTech\CoreLumen\Core\BaseRepository $resource   Risorsa da cancellare
     * @param int|string $id    ID della risorsa da cancellare
     *
     * @return void
     */
    protected function beforeDelete (BaseRepository $resource, int|string $id ) : void {}

    //---------------------------------------------------------------------------------------------------

    /**
     * Callback eseguita dopo la cancellazione di una risorsa
     *
     * Deve essere sovrascritta per essere utilizzata e serve per eseguire
     * ulteriori operazioni sulle relazioni dopo che la risorsa venga cancellata.
     *
     * @access protected
     *
     * @param \SamagTech\CoreLumen\Core\BaseRepository $resource   Risorsa cancellata
     * @param int|string $id    ID della risorsa cancellata
     *
     * @return void
     */
    protected function afterDelete (BaseRepository $resource, int|string $id ) : void {}

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
