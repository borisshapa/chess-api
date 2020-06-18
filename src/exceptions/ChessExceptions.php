<?php

namespace chess\exceptions;

use Exception;
use Throwable;

class OutOfBoardException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("The position indicates a square that is not on the board: " . $message, $code, $previous);
    }
}

class PlayerException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Player exception: " . $message, $code, $previous);
    }
}

class GameException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Game exception: " . $message, $code, $previous);
    }
}