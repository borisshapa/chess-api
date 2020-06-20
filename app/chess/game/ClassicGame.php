<?php

namespace app\chess\game;

use app\chess\board\Board;
use app\chess\board\ClassicBoard;
use app\chess\Color;

/*
 * TODO
 *  to abstract
 */

class ClassicGame extends AbstractGame
{

    public function __construct(array $players)
    {
        parent::__construct($players, new ClassicBoard(), Color::getWhite());
    }

    public function winningConditions()
    {
        // TODO: Implement winningConditions() method.
    }
}