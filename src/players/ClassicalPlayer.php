<?php

namespace chess\players;

use chess\exceptions\PlayerException;
use chess\pieces\Piece;
use chess\util\Constants;
use chess\util\Objects;

class ClassicalPlayer extends AbstractPlayer
{
    private $pieces;

    public function __construct(string $name, $color)
    {
        parent::__construct($name, $color);
        $this->pieces = array();
    }

    public function addPiece(Piece $piece)
    {
        Objects::requireNonNull($piece, Constants::NULL_PIECE_ERROR_MESSAGE);
        // TODO Check player and piece color
        $this->checkIfPieceExists($piece);
        array_push($this->pieces, $piece);
    }

    public function removePiece(Piece $piece)
    {
        Objects::requireNonNull($piece, Constants::NULL_PIECE_ERROR_MESSAGE);
        // TODO Check player and piece color
        $index = $this->checkIfPieceDoesNotExists($piece);
        unset($this->pieces[$index]);
    }

    private function checkIfPieceExists(Piece $piece)
    {
        if (in_array($piece, $this->pieces)) {
            throw new PlayerException("The player already has the piece");
        }
    }

    private function checkIfPieceDoesNotExists(Piece $piece): int
    {
        if (($index = array_search($piece, $this->pieces)) !== false) {
            return $index;
        } else {
            throw new PlayerException("The player does not have the piece");
        }
    }
}