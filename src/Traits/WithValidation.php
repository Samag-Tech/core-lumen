<?php namespace SamagTech\CoreLumen\Traits;

use Illuminate\Http\Request;
use SamagTech\CoreLumen\Exceptions\CoreException;
use SamagTech\CoreLumen\Core\BaseValidationRequest;

/**
 * Trait per la gestione della validazioni.
 *
 * Per utilizzare questo trait bisogna compilare l'array
 * $validations aggiungendo le classi di validazione definite
 * (tali classi devono estendere SamagTech\CoreLumen\Core\BaseValidationRequest)
 * in questo modo:
 *  - $validations = [
 *      Validation1::class,
 *      Validation2::class,
 *      ecc
 *  ];
 *
 * Impostato le classi si può usufruire del metodo validation()
 * che accetta la richiesta ed eseguirà tutta la lista delle validazioni
 * su essa.
 * Se tutte le validazioni vanno a buon fine allora la funzione
 * restituirà TRUE in caso contrario FALSE.
 *
 * Se la funzione restituisce FALSE allora è possibile recuperare
 * la lista dei messaggi di errore tramite la funzione getValidationErrors().
 *
 * @property array $validations
 * @property array $validationErrors
 * @method array $getValidationErrors()
 * @method bool validation(Request $request)
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
     * Setta le validazioni passate
     *
     * @access public
     *
     * @param array|string $validations   Validazione da settare
     *
     * @return self
     */
    public function setValidations(array|string $validations) : self {

        if ( ! is_array($validations) ) {
            $this->validations = [$validations];
        }
        else {
            $this->validations = array_filter($validations);
        }

        return $this;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Controlla se la richiesta è valida.
     *
     * @access protected
     *
     * @param \Illuminate\Http\Request|array $toValidate  Dati da validare
     *
     * @throws SamagTech\CoreLumen\Exceptions\CoreException    Se una validazione non è un istanza di BaseValidationRequest
     *
     * @return bool
     */
    protected function runValidation (Request|array $toValidate) : bool {

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

            $istance = new $validate($toValidate);

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