<?php

if (! function_exists('error')) {
    /**
     * Write some errorrmation to the log.
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