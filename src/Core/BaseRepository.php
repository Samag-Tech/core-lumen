<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Database\Eloquent\Model;

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
}