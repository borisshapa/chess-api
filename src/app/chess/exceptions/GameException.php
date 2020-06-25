<?php


namespace app\chess\exceptions;

use Throwable;

/**
 * Class GameException
 * An error that occurs during the game.
 * @package app\chess\exceptions
 * @see \Exception
 * @see ChessException
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class GameException extends ChessException
{
    /**
     * GameException constructor.
     * @param string $message error message
     * @param int $code error code
     * @param Throwable|null $previous {@see Throwable} object
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("GameException : " . $message, $code, $previous);
    }
}