<?php namespace SamagTech\CoreLumen\Traits;

use Illuminate\Http\Request;
use SamagTech\CoreLumen\Exceptions\CoreException;
use SamagTech\CoreLumen\Core\BaseValidationRequest;

/**
 * Trait per la gestione della validazioni.
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
trait WithValidation {

    /**
     * Lista delle validazioni da effettuare.
     *
     * Ogni validazione è una classe dove è definita la logica della validazione.
     *
     * Es. Classe 1 -> Valida i campi obbligatori
     *      Classe 2 -> Valida X campi se il campo Y ha un valore Z
     *
     * @var array<string>
     *
     * @access protected
     *
     */
    protected array $validations = [];

    /**
     * Lista delgi errori di validazione.
     *
     * In caso di errori, questa lista conterrà i
     * messaggi come definiti dal framework
     *
     * @var array<string,array>
     *
     * @access protected
     */
    protected array $validationErrors = [];

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista degli errori
     *
     * @access public
     * @return array<string,array>
     */
    public function getValidationErrors() : array {
        return $this->validationErrors;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Controlla se la richiesta è valida.
     *
     * @access protected
     *
     * @param Illuminate\Http\Request $request  Dati della richiesta
     * @throws CoreException    Se una validazione non è un istanza di BaseValidationRequest
     * @return bool
     */
    protected function validation (Request $request) : bool {

        // Se non ci sono validazioni allora ritorna true
        if ( empty($this->validations) ) {
            return true;
        }

        /**
         * Per ogni validazione si controlla che la richiesta sia valida
         * se la richiesta non è valida allora la validazione viene bloccata
         * e vengono restituisti gli errori
         */
        foreach ($this->validations as $validate) {

            $istance = new $validate($request);

            if ( ! $istance instanceof BaseValidationRequest ) {
                throw new CoreException('La validazione deve estendere BaseValidationRequest');
            }

            if ( ! $istance->isValid() ) {

                $this->validationErrors = $istance->getErrors();
                return false;
            }
        }

        return true;
    }

    //---------------------------------------------------------------------------------------------------
}