<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe astratta per la definizione di un modello
 * pre-impostato
 *
 * @abstract
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since 2022-06-03
 */
abstract class BaseModel extends Model {

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
     * Nome della colonna da u
     */
    const CREATED_AT = 'created_date';

    /**
     * {@inheritdoc}
     */
    const UPDATED_AT = 'updated_date';
}