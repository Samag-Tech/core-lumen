<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Database\Eloquent\Model;
use SamagTech\CoreLumen\Handlers\ListOptions;

/**
 * Classe astratta per la definizione di un modello
 * pre-impostato.
 *
 * @link https://laravel.com/docs/9.x/eloquent
 *
 * @abstract
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
abstract class BaseRepository extends Model {

    //---------------------------------------------------------------------------------------------------
    //      Valori di default del Modello
    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    protected $table = '';

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'id';

    /**
     * {@inheritdoc}
     */
    public $incrementing = true;

    /**
     * {@inheritdoc}
     */
    protected $keyType = 'int';

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    const CREATED_AT = 'created_date';

    /**
     * {@inheritdoc}
     */
    const UPDATED_AT = 'updated_date';


    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista dei dati in base alle opzioni passate
     *
     * In base alle opzioni vengono applicate le clausole del query builder
     *
     * @link https://laravel.com/docs/9.x/queries
     *
     * @access public
     *
     * @param \SamagTech\CoreLumen\Handlers\ListOptions $options    Opzioni per la gestione della lista
     *
     * Per i filtri devono essere passati come parametri nell'url e possono avvere 2 formati:
     *  - <campo>=<valore>  Applica la classica where
     *  - <campo>:<condizione>=<valore> In base alla condizione applica una clausola specifica
     *
     * Lista delle condizioni:
     * - not            campo:not=valore            campo != valore
     * - like           campo:like=valore           campo like '%valore%'
     * - gte            campo:gte=valore            campo >= valore
     * - gt             campo:gt=valore             campo > valore
     * - lte            campo:lte=valore            campo <= valore
     * - lt             campo:lt=valore             campo < valore
     * - bool           campo:bool=0/1              campo = 1 / campo = 0
     * - in             campo:in=1,2,3              campo IN (1,2,3)
     * - not_in         campo:not_in=1,2,3          campo NOT IN (1,2,3)
     * - null           campo:null=true/false       campo IS/IS NOT NULL
     * - between        campo:between=1,10          campo BETWEEN 1 AND 10
     * - between_not    campo:between_not=1,10      campo NOT BETWEEN 1 AND 10
     * - date           campo:date=2021-01-01       campo = '2021-01-01'
     * - year           campo:year=2021             campo = 2021
     * - time           campo:time=00:00:00         campo = '00:00:00'
     * - column         campo:column=campo2         campo = campo2
     * - search         campo:search=valore         campo MATCH AGAINST (valore)
     *
     * @return self
     */
    public function getList (ListOptions $options) : self {

        // Imposta le clausole where di default
        foreach ( $options->getWhere() as $where ) {
            $this->where($where['column'], $where['clause'], $where['value']);
        }

        // Imposto l'ordinamento
        foreach($options->getSortBy() as $sortBy) {
            $this->orderBy($sortBy['column'], $sortBy['order']);
        }

        // Controllo se sono impostati i group by
        if ( ! empty($groupBy = $options->getGroupBy()) ) {
            $this->groupBy($groupBy);
        }

        // Se sono passati i parametri allora le applico alla query in base al query builder
        if ( ! empty($params = $options->getParams()) ) {

            foreach ($params as $param ) {

                // Se la condizione è null allora applica la classica clausola where
                if ( is_null($param['condition']) ) {
                    $this->where($param['field'], '=', $param['value']);
                    continue;
                }

                /**
                 * In base alla condizione impostata viene utilizza la funzione del query
                 * builder più adatta
                 *
                 */
                switch ($param['condition']) {
                    case 'not':
                        $this->whereNot($param['field'], $param['value']);
                    break;
                    case 'like':
                        $this->where($param['field'], 'like', $param['value']);
                    break;
                    case 'gte':
                        $this->where($param['field'], '>=', $param['value']);
                    break;
                    case 'gt':
                        $this->where($param['field'], '>', $param['value']);
                    break;
                    case 'lte':
                        $this->where($param['field'], '<=', $param['value']);
                    break;
                    case 'lt':
                        $this->where($param['field'], '<', $param['value']);
                    break;
                    case 'bool':
                        $this->where($param['field'], '=', $param['value']);
                    break;
                    case 'in':
                        $this->whereIn($param['field'], explode(',', $param['value']));
                    break;
                    case 'not_in':
                        $this->whereNotIn($param['field'], explode(',', $param['value']));
                    break;
                    case 'null':
                        if ( $param['value'] == 'true' ) {
                            $this->whereNull($param['field']);
                        }
                        else if ($param['value'] == 'false') {
                            $this->whereNotNull($param['field']);
                        }
                    break;
                    case 'between':
                        $this->whereBetween($param['field'], explode(',', $param['value']));
                    break;
                    case 'between_not':
                        $this->whereNotBetween($param['field'], explode(',', $param['value']));
                    break;
                    case 'date':
                        $this->whereDate($param['field'], $param['value']);
                    break;
                    case 'year':
                        $this->whereYear($param['field'], $param['value']);
                    break;
                    case 'time':
                        $this->whereTime($param['field'], $param['value']);
                    break;
                    case 'column':
                        $this->whereColumn($param['field'],  $param['value']);
                    break;
                    case 'search':
                        $this->whereFullText($param['field'],  $param['value']);
                    break;
                }
            }

            // Aggiunge altre clausole prima di recuperare i dati
            $this->addCustomClause($options);
        }

        // Se non è disabilitata la paginazione allora la utilizzo
        if ( ! $options->isDisablePagination() ) {

            return $this->paginate(
                $options->getPerPage(),
                $options->getSelect(),
                page: $options->getPage()
            );
        }
        else {
            return $this->get();
        }
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Funziona da sovrascrivere per l'applicazione di ulteriori clausole
     * custom
     *
     * @access public
     *
     * @param \SamagTech\CoreLumen\Handlers\ListOptions $options    Opzioni per la gestione della lista
     *
     * @return void
     */
    protected function addCustomClause(ListOptions $options) : void {}

    //---------------------------------------------------------------------------------------------------
}