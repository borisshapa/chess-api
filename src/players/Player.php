<?php


namespace chess\players;


use chess\pieces\Color;
use chess\pieces\Piece;

interface Player
{
    public function getColor() : Color;

    public function addPiece(Piece $piece);

    public function removePiece(Piece $piece);
}