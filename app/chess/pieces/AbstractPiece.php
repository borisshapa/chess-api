<?php

namespace app\chess\pieces;

use app\chess\board\{
    Board,
    Position
};

use app\chess\Color;

require_once "vendor/autoload.php";

abstract class AbstractPiece implements Piece
{
    protected $color;

    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    public function getColor(): Color
    {
        return $this->color;
    }
}