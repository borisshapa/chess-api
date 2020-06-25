<?php

namespace app\chess\board;

use utils\Pair;

/**
 * Class Position
 * Pair of row numbers and column number,
 * counting from the upper left corner of the board, starting from zero.
 * @package app\chess\board
 * @see Pair
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class Position extends Pair
{
    /**
     * @return int row number, counting from the top, starting from 0.
     */
    public function getRow(): int
    {
        return $this->getFirst();
    }

    /**
     * @return int column number, counting from the left, starting from 0.
     */
    public function getCol(): int
    {
        return $this->getSecond();
    }
}