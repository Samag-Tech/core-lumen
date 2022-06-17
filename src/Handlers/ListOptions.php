<?php namespace SamagTech\CoreLumen\Handlers;

use SamagTech\CoreLumen\Exceptions\CoreException;

/**
 * Classe per la gestione delle opzioni per il listaggio.
 *
 * In base alle opzioni impostate viene effettuata la visualizzazione
 * del listaggio classico delle API.
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 *
 * @property array $select
 * @property array $where
 * @property array $groupBy
 * @property int $perPage
 * @property int $page
 * @property array $sortBy
 * @property bool $disablePagination
 * @property array $params
 *
 * Sono presenti i getter e i setter di ogni proprietà
 */
class ListOptions {

    /**
     * Costante che definisce la chiave per le ricerche fulltext
     *
     * @var string
     */
    const FULL_TEXT_CONDITION = 'search';

    /**
     * Lista delle colonne per la select
     *
     * @var array
     * @access private
     *
     * @default [*]
     */
    private array $select = ['*'];

    /**
     * Lista delle clausole where di default da applicare
     * indipendentemente dai parametri
     *
     * @var array
     * @access private
     *
     * @default []
     */
    private array $where = [];

    /**
     * Lista delle clausole per il group by da applicare
     *
     * @var array
     * @access private
     *
     * @default []
     */
    private array $groupBy = [];

    /**
     * Numero di righe per pagina da mostrare.
     *
     * Utilizzato per la paginazione insieme a $page
     * Viene sovrascritto in caso di parametro "per_page=x"
     *
     * @var int
     * @access private
     *
     * @default 50
     */
    private int $perPage = 50;

    /**
     * Pagina corrente.
     *
     * Utilizzato per la paginazione insieme a $perPage
     *
     * @var int
     * @access private
     *
     * @default 1
     */
    private int $page = 1;

    /**
     * Lista delle clausole per l'ordinamento da applicare.
     *
     * @var array
     * @access private
     *
     * Di default si ordina per ID
     * @default [
     *  [
     *      'column' => 'id',
     *      'order'  => 'desc']
     * ]
     */
    private array $sortBy = [
        [
            'column' => 'id',
            'order'  => 'desc'
        ]
    ];

    /**
     * Flag che indica se la paginazione è disabilitata
     *
     * @var bool
     * @access private
     *
     * @default false
     */
    private bool $disablePagination = false;

    /**
     * Contiene la lista di tutti i parametri passati
     * per filtrare la lista
     *
     * @var array
     * @access private
     *
     * @default []
     *
     */
    private array $params = [];

    /**
     *
     *
     */
    private array $fullText = [];

    //---------------------------------------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * Inizializza le proprietà se passate
     *
     * @param array $options    Array che contiene tutte le opzioni per impostare le proprietà
     *  - Lista delle chiavi considerate:
     *      select
     *      where
     *      groupBy
     *      perPage
     *      page
     *      sortBy
     *      disablePagination
     *      params
     *      fullText
     */
    public function __construct(array $options = []) {

        if ( ! empty($options) ) {
            $this->initialize($options);
        }
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista delle select
     *
     * @access public
     *
     * @return array
     */
    public function getSelect() : array {
        return $this->select;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista delle where di default
     *
     * @access public
     *
     * @return array
     */
    public function getWhere() : array {
        return $this->where;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista dei group by
     *
     * @access public
     *
     * @return array
     */
    public function getGroupBy() : array {
        return $this->groupBy;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce il numero di righe per pagina
     *
     * @access public
     *
     * @return int
     */
    public function getPerPage() : int {
        return $this->perPage;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la pagina corrente
     *
     * @access public
     *
     * @return int
     */
    public function getPage() : int {
        return $this->page;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista delle clausole di ordinamento
     *
     * @access public
     *
     * @return array
     */
    public function getSortBy() : array {
        return $this->sortBy;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Controlla se la paginazione è disabilitata
     *
     * @access public
     *
     * @return bool TRUE se è disabilitata, FALSE altrimenti
     */
    public function isDisablePagination () : bool {
        return $this->disablePagination;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista dei parametri passati come filtri
     *
     * @access public
     *
     * @return array
     */
    public function getParams() : array {
        return $this->params;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista delle fullText
     *
     * @access public
     *
     * @return array
     */
    public function getFullText() : array {
        return $this->fullText;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Imposta la lista per la select
     *
     * @param array $select
     *
     * @access public
     *
     * @return self
     */
    public function setSelect(array $select) : self {
        $this->select = $select;
        return $this;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Imposta la lista delle where di default
     *
     * @param array $where
     *
     * @access public
     *
     * @return self
     */
    public function setWhere(array $where) : self {
        $this->where = $where;
        return $this;

    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Imposta la lista dei group by
     *
     * @param array $groupBy
     *
     * @access public
     *
     * @return self
     */
    public function setGroupBy(array $groupBy) : self {
        $this->groupBy = $groupBy;
        return $this;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Imposta il numero di righe per pagina
     *
     * @param int $perPage
     *
     * @access public
     *
     * @return self
     */
    public function setPerPage(int $perPage) : self {

        $this->perPage = $perPage;

        return $this;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Imposta la pagina corrente
     *
     * @param int $page
     *
     * @access public
     *
     * @return self
     */
    public function setPage(int $page) : self {
        $this->page = $page;
        return $this;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Imposta la lista degli ordinamenti
     *
     * Per ogni clausola passata viene creato un array con
     * le seguenti chiavi:
     *  - column -> Colonna su cui applicare l'ordinamento
     *  - order -> Tipo di ordinamento (ASC o DESC)
     *
     * Se il campo passato è nel formato <campo>:<verso>
     * esempio created_at:asc allora in automatico verrà
     * impostata la clausola ORDER BY created_at ASC
     *
     * Se <verso> non è presente di default viene impostato "desc"
     *
     * @param array $sortBy
     *
     * @access public
     *
     * @return self
     */
    public function setSortBy(array $sortBy) : self {

        // Resetto l'ordinamento
        $this->sortBy = [];

        foreach ($sortBy as $value) {

            [$column, $order] = array_pad(explode(':', $value), 2, null);

            $this->sortBy[] = [
                'column'    => $column,
                'order'     => is_null($order) ? 'asc' : $order
            ];
        }

        return $this;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Imposta il flag della paginazione.
     *
     * @param bool $disablePagination
     *
     * @access public
     *
     * @return self
     *
     */
    public function setDisablePagination (bool $disablePagination) : self {
        $this->disablePagination = $disablePagination;
        return $this;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Imposta i parametri passati come filtro per la lista.
     *
     * I campi possono essere nel seguente formato
     * <campo>=<valore> (viene applicata la clausola where)
     * oppure nel <campo>:<condizione>=<valore>
     * dove condizione definisce la tipologia di where applicare
     * sul campo.
     *
     * La lista delle condizioni è presente nella funzione BaseRepository::getList().
     *
     * Es. name:like=pippo      Si applica il like sulla colonna "name"
     *     is_admin:bool=1      Applica il controllo sul valore true della colonna "is_admin"
     *     company_id:in=1,2,3  Applica il where in per la colonna "company_id" con i valore 1,2,3
     *
     * Per ogni clausola viene formato l'array con le seguenti chiavi:
     *  - column    Colonna su cui applicare la condizione
     *  - value     Valore per la condizione
     *  - condition Tipologia di condizione da applicare
     *
     *
     * NB. Per la clausola di FullText deve essere impostato l'array $fullText con chiave <campo> e come valore la lista
     * dei campi che formano il FullText
     *
     * Es. Se nel DB abbiamo un fulltext del tipo (firstname,lastname) e voglio utilizzare come chiave fullname allora
     * il parametro passato deve essere fullname:search=<valore> e nell'array $fullText deve essere impostata la chiave
     * [
     *      'fullname' => [
     *          'firstname',
     *          'lastname'
     *      ]
     * ]
     *
     *
     * @param array $params
     *
     * @access public
     *
     * @throws  SamagTech\CoreLumen\Exceptions\CoreException    Solleva l'eccezione se non è imposta la chiave per il fullText
     *
     * @return self
     */
    public function setParams(array $params) : self {

        // Queste chiavi vengono rimosse perché gestite da altre parti
        foreach (['sort_by', 'page', 'per_page'] as $field) {
            unset($params[$field]);
        }

        foreach ( $params as $param => $value ) {

            [$field, $condition] = array_pad(explode(':', $param), 2, null);

            // Se la condizione è quella di fullText allora imposto la lista delle colonna su cui applicarlo
            if ( $condition == self::FULL_TEXT_CONDITION ) {

                if ( ! isset($this->fullText[$field]) ) {
                    throw new CoreException('La chiave di ricerca del fulltext non è impostata');
                }

                $field = implode(',', $this->fullText[$field]);
            }

            $this->params[$field] = [
                'column'    => $field,
                'value'     => $value,
                'condition' => $condition
            ];
        }

        return $this;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Imposta i parametri per le fullText.
     *
     * @param array $fullText
     *
     * @access public
     *
     * @return self
     */
    public function setFullText(array $fullText) {
        $this->fullText = $fullText;
        return $this;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Funzione per l'inizializzazione delle opzioni
     *
     * @param array $options    Lista delle opzioni
     *
     * @access private
     *
     * @return void
     */
    private function initialize(array $options) : void {

        $fields = [
            'select',
            'where',
            'groupBy',
            'perPage',
            'page',
            'sortBy',
            'disablePagination',
            'params',
            'fullText'
        ];

        foreach ( $fields as $field ) {

            if ( isset($options[$field]) && ! is_null($options[$field]) ) {
                $this->{'set'.ucfirst($field)}($options[$field]);
            }
        }

    }

    //---------------------------------------------------------------------------------------------------


}