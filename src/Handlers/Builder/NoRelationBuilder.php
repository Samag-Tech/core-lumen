<?php namespace SamagTech\CoreLumen\Handlers\Builder;

use SamagTech\CoreLumen\Handlers\Builder\FilterBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Classe per la gestione della costruzione di query sulla tabella
 * corrente.
 *
 * @implements \SamagTech\CoreLumen\Handlers\Builder\FilterBuilder;
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v1.7.0
 */
class NoRelationBuilder implements FilterBuilder {

    /**
     * Istanza della classe
     *
     * @var self
     * @access private
     * @static
     */
    private static ?self $instance = null;

    /**
     * Istanza del builder
     *
     * @access private
     * @static
     */
    private static $builder;

    //-----------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * @param $builder  Builder di partenza
     */
    protected function __construct($builder) {
        self::$builder = $builder;
    }

    //-----------------------------------------------------------------------

    /**
     * Restituisce l'istanza della classe
     *
     * @access public
     *
     * @static
     *
     * @param $builder Istanza del builder
     */
    public static function getInstance($builder) {

        if (!is_null(self::$instance)) {
            self::$instance = new static($builder);
        }

        return self::$instance;
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function where(string $column, string $condition, mixed $value) : EloquentBuilder {
        return self::$builder->where($column, $condition, $value);
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNot(string $column, mixed $value) : EloquentBuilder {
        return self::$builder->whereNot($column, $value);
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereIn(string $column, string $values) : EloquentBuilder {
        return self::$builder->whereIn($column,explode(',', $values));
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNotIn(string $column, string $values) : EloquentBuilder {
        return self::$builder->whereNotIn($column,explode(',', $values));
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNull(string $column) : EloquentBuilder {
        return self::$builder->whereNull($column);
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNotNull(string $column) : EloquentBuilder {
        return self::$builder->whereNotNull($column);
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereBetween(string $column, string $values) : EloquentBuilder {
        return self::$builder->whereBetween($column, explode(',', $values));
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNotBetween(string $column, string $values) : EloquentBuilder {
        return self::$builder->whereNotBetween($column, explode(',', $values));
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereDate(string $column, string $value) : EloquentBuilder {
        return self::$builder->whereDate($column, $value);
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereYear(string $column, string $value) : EloquentBuilder {
        return self::$builder->whereYear($column, $value);
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereTime(string $column, string $value) : EloquentBuilder {
        return self::$builder->whereTime($column, $value);

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereColumn(string $column, string $value) : EloquentBuilder {
        return self::$builder->whereColumn($column, $value);

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereFullText(string $column, mixed $value) : EloquentBuilder {
        return self::$builder->whereFullText($column, $value);

    }

    //-----------------------------------------------------------------------
}
