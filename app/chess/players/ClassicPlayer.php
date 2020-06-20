<?php

namespace app\chess\players;

use app\chess\Color;
use app\chess\exceptions\PlayerException;
use app\chess\pieces\Piece;
use app\chess\Constants;
use app\chess\Objects;

require_once "vendor/autoload.php";

class ClassicPlayer extends AbstractPlayer
{
    public function addPiece(Piece $piece)
    {
        Objects::requireNonNull($piece, Constants::NULL_PIECE_ERROR_MESSAGE);
        // TODO Check player and piece color
//        $this->checkIfPieceExists($piece);
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