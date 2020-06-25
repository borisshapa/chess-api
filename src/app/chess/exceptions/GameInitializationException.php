<?php


namespace app\chess\exceptions;

use Throwable;

/**
 * Class GameInitializationException
 * Error of incorrect alignment of pieces on the board.
 * @package app\chess\exceptions
 * @see \Exception
 * @see GameException
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class GameInitializationException extends GameException
{
    /**
     * GameInitializationException constructor.
     * @param string $message error message
     * @param int $code error code
     * @param Throwable|null $previous {@see Throwable} object
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("GameInitializationException : " . $message, $code, $previous);
    }
}