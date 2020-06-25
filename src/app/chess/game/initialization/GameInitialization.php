<?php

namespace app\chess\game\initialization;

use app\chess\board\Board;

/**
 * Interface GameInitialization
 * Arrangement of pieces on the board.
 * @package app\chess\game\initialization
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
interface GameInitialization
{
    /**
     * Arrangement of pieces on the board.
     * @param array $players players
     * @param Board $board the board on which the pieces are placed
     */
    public function initialize(array $players, Board $board): void;

    /**
     * Since it is not necessary to create an object for initialization each time,
     * the classes that are the implementation of this interface are singleton-classes.
     * @return GameInitialization the only object of this class.
     */
    public static function getInstance(): GameInitialization;
}