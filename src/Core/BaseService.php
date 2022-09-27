<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Http\Request;
use SamagTech\CoreLumen\Contracts\Logger;
use SamagTech\CoreLumen\Contracts\Service;
use Illuminate\Database\Eloquent\Collection;
use SamagTech\CoreLumen\Core\BaseRepository;
use SamagTech\CoreLumen\Handlers\ListOptions;
use Illuminate\Contracts\Pagination\Paginator;
use SamagTech\CoreLumen\Traits\WithValidation;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;
use SamagTech\CoreLumen\Traits\RequestCleanable;
use SamagTech\CoreLumen\Exceptions\ValidationException;
use SamagTech\CoreLumen\Exceptions\ResourceNotFoundException;

/**
 * Definizione di base di una classe "Service"
 * per la gestione delle richieste tra
 * Controller e Database.
 *
 * @implements SamagTech\CoreLumen\Contracts\Service
 *
 * @trait SamagTech\CoreLumen\Traits\WithValidation
 * @trait SamagTech\CoreLumen\Traits\RequestCleanable
 *
 * @abstract
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
abstract class BaseService implements Service {

    use WithValidation, RequestCleanable;

    /**
     * Tag utilizzato per la creazione dei log.
     *
     * Contiene il namespace della classe
     *
     * @var string
     * @access protected
     */
    private string $tag;

    /**
     * Namespace della classe JsonResource
     * da utilizzare per la restituzione dei
     * dati
     *
     * @link https://laravel.com/docs/9.x/eloquent-resources
     *
     * @var string
     * @access protected
     *
     * Es. GenericResource::class
     *
     */
    protected string $jsonResource;

    /**
     * Modello per gestione dei dati
     *
     * @var SamagTech\CoreLumen\Core\BaseRepository
     *
     * @access protected
     */
    protected BaseRepository $repository;

    /**
     * Logger della classe
     *
     * @var SamagTech\CoreLumen\Contracts\Logger
     *
     * @access protected
     */
    protected Logger $logger;

    /**
     * Lista delle validazioni generiche da utilizzare sia
     * in creazione che modifica.
     *
     * @var array
     * @access protected
     */
    protected array $genericRules = [];

    /**
     * Lista delle validazioni da utilizzare
     * in fase di creazione
     *
     * @var array
     * @access protected
     */
    protected array $insertRules = [];

    /**
     * Lista delle validazioni da utilizzare
     * in fase di modifica
     *
     * @var array
     * @access protected
     */
    protected array $updateRules = [];

    /**
     * Lista delle validazioni da utilizzare
     * in fase di cancellazione
     *
     * @var array
     * @access protected
     */
    protected array $deleteRules = [];

    //---------------------------------------------------------------------------------------------------
    //                          ATTRIBUITI PER LA GESTIONE DEL LISTAGGIO
    //---------------------------------------------------------------------------------------------------

    /**
     * Lista dei campi da recuperare nella select
     *
     * @var array<string>
     * @access protected
     *
     * Default ['*']
     */
    protected array $listSelect = ['*'];

    /**
     * Lista delle clausole where di default
     *
     * vedi SamagTech\CoreLumen\Handlers\ListOptions
     *
     * @link https://laravel.com/docs/9.x/queries
     *
     * Per ogni campo deve essere passato un array con le seguenti
     * chiavi :
     *  - column    Colonna su cui applicare la clausola
     *  - clause    Clausola (=, < , >= ecc..)
     *  - value     Valore
     *
     * @var array<array<string,string>>
     * @access protected
     *
     * Default []
     */
    protected array $listDefaultWhere = [];

    /**
     * Lista delle clausole group by
     *
     * vedi SamagTech\CoreLumen\Handlers\ListOptions
     *
     * @var array<string>
     * @access protected
     *
     * Default []
     */
    protected array $listGroupBy = [];

    /**
     * Numero di righe per pagina
     *
     * @var int
     * @access protected
     *
     * Default 50
     */
    protected int $listPerPage = 50;

    /**
     * Lista dei campi da utilizzare per
     * l'ordinamento
     *
     * vedi SamagTech\CoreLumen\Handlers\ListOptions
     *
     * Per ogni ordinamento deve essere inserito il valore con questo formato
     * <campo>:<verso>
     *
     * @var array<string>
     * @access protected
     *
     * Default ['id:desc']
     */
    protected array $listSortBy = ['id:desc'];

    /**
     * Flag che indica se disabilitare la paginazione
     *
     * @var bool
     *
     * @access protected
     *
     * Default false
     */
    protected bool $disablePagination = false;

    /**
     * Configurazione per le clausole di fullText
     *
     * vedi SamagTech\CoreLumen\Handlers\ListOptions
     *
     * Per utilizzare un fulltext si deve definire la chiave
     * del fulltext e utilizzare la condizione "search" nei filtri.
     *
     * Es. Chiave -> fullname, FullText (firstname,lastname)
     *
     * Il filtro da passare è fullname:search=<valore>
     * mentre l'array deve essere popolato così:
     * [
     *      'fullname' => ['firstname', 'lastname']
     * ]
     *
     * @var array
     * @access protected
     *
     * Default []
     */
    protected array $listFullText = [];

    //---------------------------------------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * @param SamagTech\CoreLumen\Core\BaseRepository $repository     Modello per la gestione dei dati
     */
    public function __construct(BaseRepository $repository, Logger $logger) {

        $this->repository = $repository;
        $this->logger = $logger;

        $this->logger->setService($this);

        // Imposto il tag per i log
        $this->tag = get_class($this);
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public function index(Request $request): JsonResource|array {

        // Recupero i dati della richiesta
        $params = $request->query();

        // Setup sort_by
        $sortBy = $params['sort_by'] ?? null;

        if ( ! is_null($sortBy) && is_string($sortBy) ) {
            $sortBy = [$sortBy];
        }

        // Imposto l'array delle opzioni
        $options = [
            'select'            => $this->listSelect,
            'where'             => $this->listDefaultWhere,
            'groupBy'           => $this->listGroupBy,
            'perPage'           => $params['per_page'] ?? $this->listPerPage,
            'page'              => $params['page'] ?? 1,
            'sortBy'            => $sortBy ,
            'disablePagination' => $this->disablePagination,
            'params'            => $params,
            'fullText'          => $this->listFullText
        ];

        $listOptions = new ListOptions($options);

        // Callback prima di effettuare il listaggio
        $listOptions = $this->beforeRetrieve($listOptions);

        // Recupero delle risorse
        $resources = $this->repository->getList($listOptions);

        // Callback post recupero dati
        $this->afterRetrieve($resources);

        return $this->jsonResource::collection($resources);
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

        // Eseguo la validazione
        $this->validation($request, $this->genericRules, $this->insertRules);

        // Pulisco la richiesta
        $data = $this->cleanRequest($request);

        // Lancio la callback prima di inserire i dati
        $data = $this->beforeInsert($data);

        // Recupera eventuali relazioni
        $relations = $this->getRelations($data);

        // Genero l'ID se il modello ha impostato il tipo stringa
        if ( $this->repository->getKeyType() === 'string' && method_exists($this->repository, 'generateIdString') ) {
            $data[$this->repository->getKeyName()] = $this->repository->generateIdString();
        }

        // Crea la risorsa
        $resource = $this->repository->create($data);

        info($this->tag. ': Creazione della risorsa', ['resource' => $resource]);

        $this->logger->write('store', $resource->id, $resource);

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

    /**
     * {@inheritdoc}
     */
    public function update (Request $request, int | string $id) : bool {

        info($this->tag. ': Richiesta modifica di una risorsa', ['id' => $id, 'request' => $request]);

        $resource = $this->repository->find($id);

        // Salvo i vecchi dati
        $old_data = $resource;

        // Se la risorsa non esiste allora sollevo un eccezione
        if ( is_null($resource) ) {

            info($this->tag. ': Risorsa non trovata per la cancellazione', ['id' => $id]);

            throw new ResourceNotFoundException();
        }

        // Eseguo le validazioni
        $this->validation($request, $this->genericRules, $this->insertRules);

        // Pulisco la richiesta
        $data = $this->cleanRequest($request);

        // Lancio la callback prima di modifica i dati
        $data = $this->beforeUpdate($id, $resource, $data);

        // Recupera eventuali relazioni
        $relations = $this->getRelations($data);

        // Modifica la risorsa
        $resource->fill($data);
        $updated = $resource->save();

        info($this->tag. ': Modifica della risorsa', ['resource' => $resource, 'updated' => $updated > 0]);

        $this->logger->write('update', $id, $old_data, $resource);

        // Lancio la callback dopo aver modificato la risorsa
        $relations = $this->afterUpdate($id, $resource, $relations);

        // Cancello le vecchie relationi e creo le nuove relazioni se sono presenti
        if ( ! empty ($relations) ) {

            foreach ($relations as $relation => $values) {

                // Cancello le vecchie relazoni
                $resource->{$relation}()->delete();

                // Aggiungo le nuove
                $resource->{$relation}()->createMany($values);

                info($this->tag. ': Creazione delle relazione', ['resource' => $resource, 'relation' => $relation]);
            }
        }

        return $updated;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function delete (int|string $id) : bool {

        info($this->tag. ': Cancellazione risorsa', ['id' => $id]);

        $resource = $this->repository->find($id);

        // Salvo i vecchi dati
        $old_data = $resource;

        // Se la risorsa non esiste allora sollevo un eccezione
        if ( is_null($resource) ) {

            info($this->tag. ': Risorsa non trovata per la cancellazione', ['id' => $id]);

            throw new ResourceNotFoundException();
        }

        // Eseguo la validazione
        $this->validation($resource->toArray(), $this->deleteRules);

        // Lancio una callback prima della cancellazione
        $this->beforeDelete($resource, $id);

        $deleted = $resource->delete();

        $this->logger->write('delete', $id, $old_data, $resource);

        // Lancio una callback dopo la cancellazione
        $this->afterDelete($resource, $id);

        if ( ! $deleted ) {
            error($this->tag. ': Errore durante la cancellazione della risorsa', ['id' => $id]);
        }

        return $deleted;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce il repository del servizio
     *
     * @return BaseRepository
     */
    public function getRepository () : BaseRepository {
        return $this->repository;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Callback eseguita prima del recupero della lista delle risorsa.
     *
     * Deve essere sovrascritta per essere utilizzata e serve per la modifica
     * delle opzioni
     *
     * @access protected
     *
     * @param \SamagTech\CoreLumen\Handlers\ListOptions    $listOptions
     *
     * @return \SamagTech\CoreLumen\Handlers\ListOptions
     */
    protected function beforeRetrieve (ListOptions $listOptions) : ListOptions {
        return $listOptions;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Callback eseguita prima del recupero della lista delle risorsa.
     *
     * Deve essere sovrascritta per essere utilizzata e serve per la modifica
     * delle opzioni
     *
     * @link https://laravel.com/docs/9.x/pagination
     * @link https://laravel.com/docs/9.x/eloquent-collections
     *
     * @access protected
     *
     * @param \Illuminate\Contracts\Pagination\Paginator|Illuminate\Database\Eloquent\Collection   $resources   Lista delle risorse recuperate
     *
     * @return Illuminate\Contracts\Pagination\Paginator|Illuminate\Database\Eloquent\Collection
     */
    protected function afterRetrieve (Paginator|Collection $resources) : Paginator|Collection  {
        return $resources;
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
     * Callback eseguita prima della modifica della risorsa
     *
     * Deve essere sovrascritta per essere utilizzata e serve per eseguire
     * ulteriori operazioni sui dati prima di essere modificati
     *
     * @access protected
     *
     * @param int|string $id  ID risorsa da modificare
     * @param \SamagTech\CoreLumen\Core\BaseRepository $resource  Dati della risorsa se è stata trovata
     * @param array $data   Dati estratti dalla richiesta
     *
     * @return array    Restituisce i dati creare la risorsa
     */
    protected function beforeUpdate(int|string $id, BaseRepository $resource, array $data) : array {
        return $data;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Callback eseguita dopo la modifica della risorsa
     *
     * Deve essere sovrascritta per essere utilizzata e serve per eseguire
     * ulteriori operazioni sulle relazioni prima di essere cancellate e reinserite
     *
     * @access protected
     *
     * @param int|string $id  ID risorsa da modificare
     * @param \SamagTech\CoreLumen\Core\BaseRepository $resource   Risorsa Modificata
     * @param array $relations  Relazione recuperate dalla funzione getRelations()
     *
     * @return array    Le relazioni modificate
     */
    protected function afterUpdate(int|string $id, BaseRepository $resource, array $relations) : array {
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

    /**
     * Valida la richiesta
     *
     * @param Request|array $toValidate   Richiesta da validare
     * @param ...$rules                   Regola per la validazione
     *
     * @throws ValidationException  solleva quest'eccezione in caso di errori di validazione
     *
     * @return void
     */
    private function validation(Request|array $toValidate, ...$rules) : void {

        $this->setValidations(array_merge(...$rules));

        if ( ! $this->runValidation($toValidate) ) {

            info($this->tag. ': Errore di validazione', ['request' => $toValidate, ['errors' => $this->getValidationErrors()]]);

            throw new ValidationException($this->getValidationErrors());
        }

    }

    //---------------------------------------------------------------------------------------------------
}
