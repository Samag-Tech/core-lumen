<?php namespace SamagTech\CoreLumen\Handlers\Builder;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Interfaccia per la definizione di un builder per i filtri della lista
 *
 * @interface
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v1.7.0
 */
interface FilterBuilder {

    //-----------------------------------------------------------------------

    /**
     * Imposta una clausola sulla query
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param string $condition Condizione da utilizzare
     * @param mixed $value
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function where(string $column, string $condition, mixed $value) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Imposta la clausola !=
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param mixed $value
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereNot(string $column, mixed $value) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Imposta una clausola where in
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param string $values    Lista dei valori da cercare separati da virgola
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereIn(string $column, string $values) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Imposta una clausola where not in
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param string $values    Lista dei valori da non recuperare separati da virgola
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereNotIn(string $column, string $values) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Recupera i dati se una colonna ha valore null
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereNull(string $column) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Recupera i dati se una colonna non ha valore null
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereNotNull(string $column) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Restituisce i dati in base al range interno di valori di una colonna
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param string $values    Lista dei valori separati da virgola
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereBetween(string $column, string $values) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Restituisce i dati in base al range esterno di valori di una colonna
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param string $values    Lista dei valori separati da virgola
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereNotBetween(string $column, string $values) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Imposta una clausola di ricerca per data
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param string $value
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereDate(string $column, string $value) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Imposta una clausola di ricerca per anno
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param string $value
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereYear(string $column, string $value) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Imposta una clausola di ricerca per time
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param string $value
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereTime(string $column, string $value) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Imposta una clausola di ricerca per una colonna uguale ad un'altra
     *
     * @static
     *
     * @access public
     *
     * @param string $column    Colonna da utilizzare
     * @param string $value
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereColumn(string $column, string $value) : EloquentBuilder;

    //-----------------------------------------------------------------------

    /**
     * Imposta una ricerca full text
     *
     * @static
     *
     * @access public
     *
     * @param string $condition Condizione da utilizzare
     * @param mixed $value
     *
     * @return \Illuminate\Database\Eloquent\Builder as EloquentBuilder
     */
    public static function whereFullText(string $column, mixed $value) : EloquentBuilder;

    //-----------------------------------------------------------------------

}