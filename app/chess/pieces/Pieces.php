<?php

namespace app\chess\pieces;

use app\chess\Color;
use app\chess\board\{
    Board,
    Position
};

require_once "vendor/autoload.php";

class Pawn extends AbstractPiece {

    public function move(Board $board, Position $position): array
    {
        $possibleMoves = array();
        return $possibleMoves;
    }
}

class Knight extends AbstractPiece {

    public function move(Board $board, Position $position): array
    {
        // TODO: Implement move() method.
    }
}

class Bishop extends AbstractPiece {

    public function move(Board $board, Position $position): array
    {
        // TODO: Implement move() method.
    }
}

class Rook extends AbstractPiece {

    public function move(Board $board, Position $position): array
    {
        // TODO: Implement move() method.
    }
}

class Queen extends AbstractPiece {

    public function move(Board $board, Position $position): array
    {
        // TODO: Implement move() method.
    }
}

class King extends AbstractPiece {

    public function move(Board $board, Position $position): array
    {
        // TODO: Implement move() method.
    }
}