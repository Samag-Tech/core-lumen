<?php namespace SamagTech\CoreLumen\Contracts;

use SamagTech\CoreLumen\Contracts\Service;
use SamagTech\CoreLumen\Models\ServiceKey;

/**
 * Definizione di un interfaccia per l'applicazione
 * del pattern Factory per la creazione dinamica
 * del servizio giusto
 *
 * @interface
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
interface Factory {

    /**
     * Funzione che restituisce il servizio da chiamare
     * in base ad un token.
     *
     * @param ServiceKey    $serviceKey Modello per la gestione delle chiavi
     * @param string $token Token che identifica quale servizio istanziare
     *
     * @return \SamagTech\CoreLumen\Contracts\Service;
     */
    public function makeService(ServiceKey $serviceKey, ?string $token = null ) : Service;
}