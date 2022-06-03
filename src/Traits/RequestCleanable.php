<?php namespace SamagTech\CoreLumen\Traits;

use Illuminate\Http\Request;
use SamagTech\CoreLumen\Exceptions\CoreException;

/**
 * Trait per la definizione della pulizia della richiesta.
 *
 * Serve per eliminare tutti i campi che vengono inviati
 * e non servono al funzionamento della richiesta.
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
trait RequestCleanable {

    /**
     * Lista dei soli campi da utilizzare
     *
     * @var array<string>
     * @access protected
     */
    protected array $onlyUsedFields = [];

    //---------------------------------------------------------------------------------------------------

    /**
     * Pulisce i dati della richiesta.
     *
     * @access protected
     *
     * @param Illuminate\Http\Request $request
     * @throws CoreException    Se non sono stati definiti campi da utilizzare
     * @return array
     */
    protected function cleanRequest(Request $request) : array {

        if ( empty($this->onlyUsedFields) ) {
            throw new CoreException('La richiesta deve avere almeno un campo');
        }

        return $request->only($this->onlyUsedFields);
    }

    //---------------------------------------------------------------------------------------------------

}