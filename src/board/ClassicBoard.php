<?php

namespace chess\board;

use chess\exceptions\OutOfBoardException;
use chess\pieces\Piece;
use chess\util\Constants;
use chess\util\Objects;

class ClassicBoard extends AbstractBoard
{
    private $board;
    private $rows;
    private $cols;

    public function __construct(int $rows = Constants::CLASSIC_BOARD_ROWS, int $cols = Constants::CLASSIC_BOARD_COLS)
    {
        parent::__construct($rows, $cols);
        foreach (range(1, $rows) as $row) {
            foreach (range(1, $cols) as $col) {
                $this->board[$row][$col] = null;
            }
        }
    }

    public function addPiece(Piece $piece, Position $position)
    {
        Objects::requireNonNull($piece, Constants::NULL_PIECE_ERROR_MESSAGE);
        $this->checkPosition($position);
        $arrayRow = $this->getArrayRow($position->getRow());
        $arrayCol = $position->getCol();
        $this->board[$arrayRow][$arrayCol] = $piece;
    }

    public function removePiece(Position $position)
    {
        $this->checkPosition($position);
        $arrayRow = $this->getArrayRow($position->getRow());
        $arrayCol = $position->getCol();
        $this->board[$arrayRow][$arrayCol] = null;
    }

    private function getArrayRow(int $boardRow): int
    {
        return $this->rows - $boardRow - 1;
    }

    public function checkPosition(Position $position)
    {
        if ($position->getRow() < 0 || $position->getRow() >= $this->rows
            || $position->getCol() < 0 || $position->getCol() >= $this->cols) {
            throw new OutOfBoardException("Row: " . $position->getRow() . ", col: " . $position->getCol());
        }
    }

    public function getPiece(Position $position): Piece
    {
        $arrayRow = $this->getArrayRow($position->getRow());
        $arrayCol = $position->getCol();
        return $this->board[$arrayRow][$arrayCol];
    }
}