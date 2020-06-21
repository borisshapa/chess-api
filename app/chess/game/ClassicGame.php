<?php

namespace app\chess\game;

use app\chess\board\Board;
use app\chess\board\ClassicBoard;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\exceptions\GameException;
use app\chess\moves\Move;
use app\chess\pieces\King;
use app\chess\pieces\Pawn;
use app\chess\pieces\Piece;
use app\chess\pieces\Rook;

/*
 * TODO
 *  to abstract
 */

require_once "app/chess/exceptions/ChessExceptions.php";
require_once "vendor/autoload.php";

class ClassicGame extends AbstractGame
{

    private $canBeEatenEnPassant;

    public function __construct(array $players)
    {
        parent::__construct($players, new ClassicBoard(), Color::getWhite());
    }


    public function winningConditions(Color $color): bool
    {
        $rivalColor = self::getOppositeColor($color);
        $rivalKingPosition = $this->board->findPiece(King::class, $rivalColor);
        if (!$this->board->isPositionUnderAttack($rivalKingPosition, $rivalColor)) {
            return false;
        }
        $board = $this->board;
        for ($row = 0; $row < $board->getRows(); $row++) {
            for ($col = 0; $col < $board->getCols(); $col++) {
                $position = new Position($row, $col);
                if ($board->isPositionOccupied($position)) {
                    $piece = $board->getPiece($position);
                    if ($piece->getColor() == $rivalColor) {
                        $possibleMoves = $piece->possibleMoves($board, $position);
                        foreach ($possibleMoves as $possibleMove) {
                            $boardClone = clone $board;
                            $move = new Move($position, $possibleMove);
                            $boardClone->move($piece, $move);
                            $kingClonePosition = $boardClone->findPiece(King::class, $rivalColor);
                            if (!$boardClone->isPositionUnderAttack($kingClonePosition, $rivalColor)) {
                                return false;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    private function checkPositionUnderAttack(Board $board, Piece $piece, int $row, int $col)
    {
        if ($board->isPositionUnderAttack(new Position($row, $col), $piece->getColor())) {
            throw new \Exception("THINK ABOUT IT");
        }
    }

    public function isMoveCastling(Move $move): bool
    {
        $board = $this->board;

        $from = $move->getFrom();
        $to = $move->getTo();

        $piece = $board->getPiece($from);
        if ($piece instanceof King && $board->neverMovesAtPosition($from)) {
            $rowDiff = $to->getRow() - $from->getRow();
            $colDiff = $to->getCol() - $from->getCol();
            if ($rowDiff == 0 && (abs($colDiff) == 2)) {
                $singleColMove = $colDiff / 2;
                $row = $from->getRow();
                $col = $from->getCol() + $singleColMove;
                var_dump($col);
                while ($col > 0 && $col < $board->getCols() - 1) {
                    if (!$board->isPositionFree(new Position($row, $col))) {
                        throw new \Exception("THINK ABOUT IT");
                    }
                    $col += $singleColMove;
                }
                $rookPos = new Position($row, $col);
                if (!($board->isPositionOccupied($rookPos)
                    && $board->getPiece($rookPos) instanceof Rook
                    && $board->neverMovesAtPosition($rookPos))) {
                    throw new \Exception("THINK ABOUT IT");
                }

                $row = $from->getRow();
                $col = $from->getCol();
                while ($col != $to->getCol()) {
                    $this->checkPositionUnderAttack($board, $piece, $row, $col);
                    $col += $singleColMove;
                }
                $this->checkPositionUnderAttack($board, $piece, $row, $col);
                return true;
            }
        }
        return false;
    }

    public function isMoveEnPassant(Move $move)
    {
        $board = $this->board;

        $from = $move->getFrom();
        $to = $move->getTo();

        $piece = $board->getPiece($from);

        if ($piece instanceof Pawn && $board->isPositionValid($to)) {
            if ($to->getRow() - $from->getRow() == $piece->getColor()->getDirection()
                && abs($to->getCol() - $from->getCol()) == 1) {
                if ($board->isPositionFree($to)) {
                    $attackedPawnPosition = new Position($from->getRow(), $to->getCol());
                    $attackedPawn = $board->getPiece($attackedPawnPosition);
                    if ($attackedPawn instanceof Pawn
                        && $attackedPawn->getColor() !== $piece->getColor()
                        && $attackedPawnPosition == $this->canBeEatenEnPassant) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function move(Move $move)
    {
        $board = $this->board;
        $boardSnapshot = clone $board;

        $from = $move->getFrom();
        $to = $move->getTo();

        $piece = $board->getPiece($from);

        // castling
        if ($this->isMoveCastling($move)) {
            $board->move($piece, $move);

            $col = $from->getCol();
            $colDiff = ($to->getCol() - $from->getCol()) / 2;
            while ($col > 0 && $col < $board->getCols() - 1) {
                $col += $colDiff;
            }
            $rookMove = new Move(new Position($from->getRow(), $col), new Position($to->getRow(), $to->getCol() - $colDiff));
            $board->move($board->getPiece($rookMove->getFrom()), $rookMove);
            $this->canBeEatenEnPassant = null;

        } else if ($this->isMoveEnPassant($move)) {
            $board->move($piece, $move);
            $attackedPawnPosition = new Position($from->getRow(), $to->getCol());
            $board->removePiece($attackedPawnPosition);
            $this->canBeEatenEnPassant = null;

        } else if (in_array($to, $piece->possibleMoves($board, $from))) {
            $this->canBeEatenEnPassant = null;
            if ($piece instanceof Pawn
                && abs($to->getRow() - $from->getRow()) == 2) {
                $this->canBeEatenEnPassant = $to;
            }
            $board->move($piece, $move);
        } else {
            throw new \Exception("THINK ABOUT IT");
        }

        $color = $piece->getColor();
        $kingPosition = $board->findPiece(King::class, $color);
        var_dump($kingPosition);
        if ($board->isPositionUnderAttack($kingPosition, $color)) {
            $this->board = $boardSnapshot;
            throw new \Exception("THINK ABOUT IT");
        }
        $this->nextPlayer();
    }

    public static function getOppositeColor(Color $color): Color
    {
        return $color == Color::getWhite() ? Color::getBlack() : Color::getWhite();
    }
}