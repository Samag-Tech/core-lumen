<?php namespace SamagTech\CoreLumen\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modello per la gestione delle configurazioni di sistema
 *
 * @extends Model
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class System extends Model {

    protected $fillable = [
        'option',
        'value',
    ];

    protected $table = 'system';

    protected $primaryKey = 'option';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}