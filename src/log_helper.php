<?php

if (! function_exists('error')) {

    /**
     * Scrive un log di errore
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    function error($message, $context = [])
    {
        return app('Psr\Log\LoggerInterface')->error($message, $context);
    }
}