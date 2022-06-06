<?php namespace SamagTech\CoreLumen\Exceptions;

/**
 * Eccezione per la gestione delle risorse non trovate
 *
 * @extends BaseException
 *
 * @property string $messageCustom
 * @property int $httpCode
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 * @since v0.1
 */
class ResourceNotFoundException extends BaseException {

    /**
     * {@inheritdoc}
     *
     */
    private string $messageCustom = 'Risorsa non trovata';

    /**
     * {@inheritdoc}
     *
     */
    private int $httpCode = 404;

}

