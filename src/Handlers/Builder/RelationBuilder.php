<?php namespace SamagTech\CoreLumen\Handlers\Builder;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class RelationBuilder implements FilterBuilder {

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

    /**
     * Nome della relazione
     *
     * @access private
     * @static
     *
     * @var string
     */
    private static string $relation;

    //-----------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * @param $builder  Builder di partenza
     * @param string $relation Nome della relazione
     *
     */
    protected function __construct($builder, string $relation) {
        self::$builder = $builder;
        self::$relation = $relation;
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
     * @param string $relation Nome della relazione
     */
    public static function getInstance($builder, $relation) {

        if (is_null(self::$instance)) {
            self::$instance = new static($builder, $relation);
        }

        return self::$instance;
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function where(string $column, string $condition, mixed $value) : EloquentBuilder {
        return self::$builder->whereRelation(self::$relation,  $column, $condition, $value);
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNot(string $column, mixed $value) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $value) {
            $query->whereNot($column, $value);
        });

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereIn(string $column, string $values) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $values) {
            $query->whereIn($column, explode(',', $values));
        });

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNotIn(string $column, string $values) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $values) {
            $query->whereNotIn($column,explode(',', $values));
        });

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNull(string $column) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column) {
            $query->whereNull($column);
        });

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNotNull(string $column) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column) {
            $query->whereNotNull($column);
        });
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereBetween(string $column, string $values) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $values) {
            $query->whereBetween($column,explode(',', $values));
        });
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereNotBetween(string $column, string $values) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $values) {
            $query->whereNotBetween($column,explode(',', $values));
        });

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereDate(string $column, string $value) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $value) {
            $query->whereDate($column, $value);
        });

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereYear(string $column, string $value) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $value) {
            $query->whereYear($column, $value);
        });

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereTime(string $column, string $value) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $value) {
            $query->whereTime($column, $value);
        });

    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereColumn(string $column, string $value) : EloquentBuilder {

        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $value) {
            $query->whereColumn($column, $value);
        });


    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public static function whereFullText(string $column, mixed $value) : EloquentBuilder {
        return self::$builder->whereRelation(self::$relation, function (EloquentBuilder $query) use ($column, $value) {
            $query->whereFullText($column, $value);
        });

    }

    //-----------------------------------------------------------------------
}
