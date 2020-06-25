<?php

namespace app\chess\game;


use app\chess\Color;
use app\chess\game\initialization\GameInitialization;
use app\chess\moves\Move;

/**
 * Interface Game
 * An interface describing the methods required to play chess.
 * @package app\chess\game
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
interface Game
{
    /**
     * Places the pieces on the board.
     * @param GameInitialization $initialization how to arrange pieces
     * @see GameInitialization
     */
    public function initialize(GameInitialization $initialization): void;

    /**
     * Make a move. The order of moves is determined by the implementation.
     * @param Move $move how to move
     * @see Move
     */
    public function move(Move $move): void;

    /**
     * Winning conditions for a player with a given color.
     * @param Color $color candidate for victory
     * @return bool <var>true</var> if and only if a player with a given color won, <var>false</var> otherwise.
     */
    public function checkIfColorWon(Color $color): bool;
}