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
     * @var string|null
     */
    private $data;

    /**
     * KevinException constructor.
     *
     * @param string $message
     * @param int $code
     * @param null $previous
     * @param string|null $data
     */
    public function __construct($message, $code = 0, $previous = null, $data = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string|null
     */
    public function getData()
    {
        return $this->data;
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
