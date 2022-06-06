<?php namespace SamagTech\CoreLumen\Traits;

use Illuminate\Http\Request;
use SamagTech\CoreLumen\Exceptions\CoreException;

/**
 * Trait per la definizione della pulizia della richiesta.
 *
 * Serve per eliminare tutti i campi che vengono inviati
 * e non servono al funzionamento della richiesta.
 *
 * Per utilizzare questa classe va compilato l'array
 * $onlyUsedFields con la lista dei soli campi da considerare della
 * richiesta (onde evitare di utilizzare gli altri campi):
 *  - $onlyUsedFields = [
 *      'field1',
 *      'field2',
 *      'field3',
 *      ecc..
 *  ]
 *
 * Impostato l'attributo allora è possibile utilizzare la
 * funzione cleanRequest() che accetta la richiesta e recupera
 * solo i campi definiti.
 *
 * @property array $onlyUsedFields
 * @method  array   cleanRequest(Request $request)
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