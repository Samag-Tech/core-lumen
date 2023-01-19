<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use SamagTech\CoreLumen\Handlers\ListOptions;
use Illuminate\Contracts\Pagination\Paginator;

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
    public $timestamps = true;

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista dei dati in base alle opzioni passate
     *
     * In base alle opzioni vengono applicate le clausole del query builder
     *
     * @link https://laravel.com/docs/9.x/queries
     * @link https://laravel.com/docs/9.x/pagination
     * @link https://laravel.com/docs/9.x/eloquent-collections
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
     * @return Illuminate\Contracts\Pagination\Paginator|Illuminate\Database\Eloquent\Collection
     */
    public function getList (ListOptions $options) : Paginator|Collection {

        // Variabile per concatenare il query builder
        $builder = $this;

        // Imposta le clausole where di default
        foreach ( $options->getWhere() as $where ) {
            $builder->where($where['column'], $where['clause'], $where['value']);
        }

        // Controllo se sono impostati i group by
        if ( ! empty($groupBy = $options->getGroupBy()) ) {
            $builder->groupBy($groupBy);
        }

        // Imposto l'ordinamento
        foreach($options->getSortBy() as $sortBy) {
            $builder->orderBy($sortBy['column'], $sortBy['order']);
        }

        // Se sono passati i parametri allora le applico alla query in base al query builder
        if ( ! empty($params = $options->getParams()) ) {

            foreach ($params as $param ) {

                // Se la condizione è null allora applica la classica clausola where
                if ( is_null($param['condition']) ) {
                    $builder->where($param['column'], '=', $param['value']);
                    continue;
                }

                /**
                 * In base alla condizione impostata viene utilizza la funzione del query
                 * builder più adatta
                 *
                 */
                switch ($param['condition']) {
                    case 'not':
                        $builder->whereNot($param['column'], $param['value']);
                    break;
                    case 'like':
                        $builder->where($param['column'], 'like', '%'.$param['value'].'%');
                    break;
                    case 'gte':
                        $builder->where($param['column'], '>=', $param['value']);
                    break;
                    case 'gt':
                        $builder->where($param['column'], '>', $param['value']);
                    break;
                    case 'lte':
                        $builder->where($param['column'], '<=', $param['value']);
                    break;
                    case 'lt':
                        $builder->where($param['column'], '<', $param['value']);
                    break;
                    case 'bool':
                        $builder->where($param['column'], '=', $param['value']);
                    break;
                    case 'in':
                        $builder->whereIn($param['column'], explode(',', $param['value']));
                    break;
                    case 'not_in':
                        $builder->whereNotIn($param['column'], explode(',', $param['value']));
                    break;
                    case 'null':
                        if ( $param['value'] == 'true' ) {
                            $builder->whereNull($param['column']);
                        }
                        else if ($param['value'] == 'false') {
                            $builder->whereNotNull($param['column']);
                        }
                    break;
                    case 'between':
                        $builder->whereBetween($param['column'], explode(',', $param['value']));
                    break;
                    case 'between_not':
                        $builder->whereNotBetween($param['column'], explode(',', $param['value']));
                    break;
                    case 'date':
                        $builder->whereDate($param['column'], $param['value']);
                    break;
                    case 'year':
                        $builder->whereYear($param['column'], $param['value']);
                    break;
                    case 'time':
                        $builder->whereTime($param['column'], $param['value']);
                    break;
                    case 'column':
                        $builder->whereColumn($param['column'],  $param['value']);
                    break;
                    case $options::FULL_TEXT_CONDITION:
                        $builder->whereFullText($param['column'],  $param['value']);
                    break;
                }
            }

            // Aggiunge altre clausole prima di recuperare i dati
            $builder = $this->addCustomClause($options, $builder);
        }

        // Se non è disabilitata la paginazione allora la utilizzo
        if ( ! $options->isDisablePagination() ) {

            return $builder->paginate(
                $options->getPerPage(),
                $options->getSelect(),
                page: $options->getPage()
            );
        }
        else {
            return $builder->get();
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
     * @param self $builder    Istanza del builder
     *
     * @return self
     */
    protected function addCustomClause(ListOptions $options, self $builder ) : self {
        return $builder;
    }

    //---------------------------------------------------------------------------------------------------
}