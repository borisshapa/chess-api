<?php

namespace chess\pieces;

use chess\board\{
    Board,
    Position
};

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