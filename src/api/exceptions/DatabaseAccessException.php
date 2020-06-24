<?php


namespace api\exceptions;


use Throwable;

class DatabaseAccessException extends ChessApiException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("DatabaseAccessException : " . $message, $code, $previous);
    }
}