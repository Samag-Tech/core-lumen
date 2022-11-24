<?php namespace SamagTech\CoreLumen\Traits;

/**
 * Trait per gestire gli array che si trasformano in json
 *
 * @author Alessadro Marotta <alessandro.marotta@samag.tech>
 *
 * @since v1.5.0
 */
trait FieldsConvertIntoJson {

    /**
     * Lista dei campi da convertire
     *
     * @access protected
     * @var array
     */
    protected array $fieldsToConvert = [];

    //-----------------------------------------------------------------------

    /**
     * Recupera la lista delle relazioni da inserire e pulisce
     * l'array contenente i dati della richiesta.
     *
     * Se l'array $data è formato in questo modo e key4 deve essere convertita in json:
     *  [
     *      'key' => 'value',
     *      'key1' => 'value1',
     *      'key3' => [
     *          'subkey' => 'subvalue'
     *       ],
     *      'key4' => [
     *          'subkey' => 'subvalue'
     *       ]
     * ]
     *
     * Restituisce i dati della richiesta senza la chiave 'key3'
     * e tale chiave verrà considerata una relazione che verrà inserita post
     * creazione della risorsa mentre key4 resta e poi verrà convertita in una colonna
     * json da @SamagTech\Casts\Json implementato nel modello
     *
     * @access protected
     *
     * @param array &$data  Dati della richiesta
     *
     * @return array    Dati della richiesta puliti
     */
    protected function getRelations(array &$data) : array {

        $relations = [];

        // Ciclo l'array data per trovare sottoarray,
        // se ci sono li estraggo
        foreach ( $data as $key => $value ) {

            if ( in_array($key, $this->fieldsToConvert) ) continue;

            if ( is_array($value) ) {
                $relations[$key] = $value;
                unset($data[$key]);
            }
        }

        return $relations;
    }
}