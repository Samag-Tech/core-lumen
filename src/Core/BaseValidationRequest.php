<?php namespace SamagTech\CoreLumen\Core;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Support\Facades\Validator;
use SamagTech\CoreLumen\Contracts\ValidationRequest;

/**
 * Modello di base per la creazione di una validazione di una richiesta.
 *
 * Viene implementata per definire una lista di validazioni da eseguire
 * su una richiesta. Le validazioni vengono eseguite nel costruttore e
 * in base all'esito vengono impostati le variabili $valid (se la validazione ha successo
 * o meno) e $errors.
 *
 * Le validazioni sono eseguite solo se la funzione condition() restituisce un valore TRUE
 * e per modificarne il comportamento bisogna sovrascriverla e, in base alla richiesta, eseguirla o meno.
 *
 * @abstract
 *
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
     * @var Illuminate\Http\Request|null
     */
    protected ?Request $request = null;

    /**
     * Array con i dati da validare
     *
     * @access protected
     *
     * @var array
     */
    protected array $toValidate = [];

    //---------------------------------------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * @param Illuminate\Http\Request|array $toValidate     Dati da validare
     */
    public function __construct(Request|array $toValidate) {

        if ( $toValidate instanceof Request ) {

            $this->toValidate = $toValidate->all();

            $this->request = $toValidate;
        }
        else {
            $this->toValidate = $toValidate;
        }

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
            $this->toValidate,
            $this->rules(),
            $this->messages(),
            $this->customAttributes()
        );

        // Se la validazione fallisce allora imposto gli attribuiti
        if ( $validator->fails() ) {
            $this->errors = $validator->errors()->all();
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
     * Restituisce la regola Unique per la fase di creazione o modifica
     * di un elemento in base l'id
     *
     * @access protected
     *
     * @param string $table         Tabella su cui effettuare il check
     * @param string $column        Colonna dell'ID (Default 'id')
     *
     * @return Illuminate\Validation\Rules\Unique
     */
    protected function getUniqueById(string $table, string $column = 'id') : Unique {

        return ! isset($this->toValidate['id'])
            ? Rule::unique($table)
            : Rule::unique($table)->ignore($this->toValidate['id'], $column);
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