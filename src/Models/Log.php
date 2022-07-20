<?php namespace SamagTech\CoreLumen\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modello per la gestione dei Log nel DB
 *
 * @extends Model
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class Log extends Model {

    protected $fillable = [
        'table',
        'row_id',
        'service',
        'old_data',
        'type',
    ];

    protected $table = 'logs';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;
}