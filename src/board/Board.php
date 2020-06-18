<?php

namespace chess\board;

use chess\pieces\Piece;

interface Board
{
    public function getRows();

    public function getCols();

    public function addPiece(Piece $piece, Position $position);

    public function removePiece(Position $position);

    public function getPiece(Position $position) : Piece;
}
