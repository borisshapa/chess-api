<?php

namespace app\chess\exceptions;

use Exception;
use Throwable;

class NullPointerException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("NullPointerException: " . $message, $code, $previous);
    }
}