<?php

abstract class Square
{
    private $coordinate;

    function __construct($coordinate)
    {
        $this->coordinates = $coordinate;
    }

    abstract public function isOccupied();

    abstract public function getPiece();
}

class EmptySquare extends Square {

    public function isOccupied()
    {
        return false;
    }

    public function getPiece()
    {
        return null;
    }
}

class OccupiedSquare extends Square {
    private $piece;

    function __construct($coordinate, AbstractPiece $piece)
    {
        parent::__construct($coordinate);
        $this->piece = $piece;
    }

    public function isOccupied()
    {
        return true;
    }

    public function getPiece()
    {
        return $this->piece;
    }
}