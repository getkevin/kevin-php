<?php

namespace Kevin;

use Exception;

/**
 * Exception class used to throw errors in Kevin library.
 *
 * @package Kevin
 */
class KevinException extends Exception
{
    /**
     * KevinException constructor.
     *
     * @param $message
     * @param int $code
     * @param null $previous
     */
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Return modified error message.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}" . PHP_EOL;
    }
}
