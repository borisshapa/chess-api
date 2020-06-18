<?php

namespace chess\pieces;

use chess\board\{
    Board,
    Position
};

abstract class AbstractPiece implements Piece
{
    private Color $color;

    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public abstract function move(Board $board, Position $position): array;
}