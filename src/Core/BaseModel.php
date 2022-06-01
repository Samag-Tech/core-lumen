<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Database\Eloquent\Model;

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