<?php namespace SamagTech\CoreLumen\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Modello per la gestione delle chiavi dei servizi
 *
 * @extends Model
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 */
class ServiceKey extends Model {

    use HasUuids;

    protected $fillable = [
        'id',
        'suffix'
    ];

    protected $table = 'services_keys';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}