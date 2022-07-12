<?php namespace SamagTech\CoreLumen\Exceptions;

/**
 * Eccezione per la gestione delle risorse non trovate
 *
 * @extends SamagTech\CoreLumen\Exceptions\BaseException
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
    protected string $messageCustom;

    /**
     * {@inheritdoc}
     *
     */
    protected int $httpCode = 404;

    public function __construct(?string $message = null, ?int $httpCode = null) {

        $this->messageCustom = __('core.resource_not_found_message');

        parent::__construct($message, $httpCode);

    }

}

