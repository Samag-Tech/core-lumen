<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SamagTech\CoreLumen\Contracts\ValidationRequest;

/**
 * Modello di base per la creazione di una validazione di una richiesta
 *
 * @abstract
 * @implements ValidationRequest
 *
 * @property Illuminate\Http\Request $request
 * @method  void run()
 * @method  bool isValid()
 * @method  array<string,array> getErrors()
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
abstract class BaseValidationRequest implements ValidationRequest {

    /**
     * Flag per indicare se la richiesta è valida
     *
     * @access private
     * @var bool
     *
     * Default true
     */
    private bool $valid = true;

    /**
     * Lista degli errori restituiti
     * dalla validazione
     *
     * @access private
     * @var array<string,array>
     *
     */
    private array $errors = [];

    /**
     * Richiesta da validare
     *
     * @access protected
     * @var Illuminate\Http\Request
     */
    protected Request $request;

    //---------------------------------------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * @param Illuminate\Http\Request $request  Richiesta
     */
    public function __construct(Request $request) {

        $this->request = $request;

        // Lancia la validazione
        $this->run();
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     */
    public function run () : void {

        // Se la condizione non è soddisfatta non utilizzo la validazione
        if ( ! $this->condition() ) return;

        // Creo il validatore
        $validator = Validator::make(
            $this->request->all(),
            $this->rules(),
            $this->messages(),
            $this->customAttributes()
        );

        // Se la validazione fallisce allora imposto gli attribuiti
        if ( $validator->fails() ) {
            $this->errors = $validator->errors();
            $this->valid  = false;
        }
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function isValid(): bool {
        return $this->valid;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array {
        return $this->errors;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Definisce la condizione per l'utilizzo della validazione
     *
     * @access protected
     *
     * @return bool TRUE allora deve essere eseguita la validaizone, FALSE altrimenti
     */
    protected function condition () : bool {
        return true;
    }

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista per la definizione della validazione
     *
     * @abstract
     *
     * @link https://laravel.com/docs/9.x/validation#quick-writing-the-validation-logic
     *
     * @return array
     */
    abstract protected function rules () : array;

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista per la definizione dei messaggi custom
     *
     * @abstract
     *
     * @link https://laravel.com/docs/9.x/validation#manual-customizing-the-error-messages
     *
     * @return array
     */
    abstract protected function messages () : array;

    //---------------------------------------------------------------------------------------------------

    /**
     * Restituisce la lista per la definizione degli attributi custom
     *
     * @link https://laravel.com/docs/9.x/validation#specifying-custom-attribute-values`
     *
     * @return array
     */
    protected function customAttributes () : array {
        return [];
    }

    //---------------------------------------------------------------------------------------------------
}