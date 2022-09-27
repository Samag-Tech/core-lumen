<?php namespace SamagTech\CoreLumen\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * Trait per la generazione automatica dell'ID
 * di tipo stringa
 *
 * @method array $generateIdString()
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v1.1
 * @deprecated  use HasUuids https://laravel.com/docs/9.x/eloquent#uuid-and-ulid-keys
 */
trait HasIDString {

    //-----------------------------------------------------------------------

    /**
     * Genera l'ID stringa
     *
     * @access public
     *
     * @throws  Exception   Solleva questa eccezione in caso di istanza
     *                      errata oppure tipologia non corretta
     *
     * @return string
     */
    public function generateIdString() : string {

        if ( ! $this instanceof Model) {
            throw new Exception("Il trait può essere utilizzato solo da un modello");
        }

        if ( $this->getKeyType() != 'string') {
            throw new Exception("La tipologia del modello non è string");
        }

        $uuid = null;

        do {
            $uuid = Uuid::uuid4()->toString();
        }
        while( is_null($this->find($uuid)));

        return $uuid;

    }

    //-----------------------------------------------------------------------


}