<?php

namespace app\chess\game;

use app\chess\board\Board;
use app\chess\board\ClassicBoard;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\exceptions\GameInitializationException;
use app\chess\exceptions\InvalidMoveException;
use app\chess\moves\Move;
use app\chess\pieces\AbstractPiece;
use app\chess\pieces\King;
use app\chess\pieces\Pawn;
use app\chess\pieces\Piece;
use app\chess\pieces\Rook;
use app\chess\players\Player;

class ClassicGame extends AbstractGame
{
    private ?Position $canBeEatenByEnPassant;
    private const CASTLING_ERROR_MESSAGE = "Unable to castle.";

    public function __construct(Player $player1, Player $player2)
    {
        parent::__construct(array($player1, $player2), new ClassicBoard(), Color::getWhite());
    }

    public function checkIfColorWon(Color $color): bool
    {
        $rivalColor = self::getOppositeColor($color);
        if (!self::isKingUnderAttack($this->board, $rivalColor)) {
            return false;
        }
        $board = $this->board;
        for($row = 0; $row < $board->getRows(); $row++) {
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
                            if (!self::isKingUnderAttack($boardClone, $rivalColor)) {
                                return false;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    private function checkIfPositionUnderAttack(Board $board, Piece $piece, Position $position, string $errorMessage)
    {
        if ($board->isPositionUnderAttack($position, $piece->getColor())) {
            throw new InvalidMoveException($errorMessage);
        }
    }

    public function isMoveCastling(Move $move): bool
    {
        $board = $this->board;

        $from = $move->getFrom();
        $to = $move->getTo();

        $piece = $board->getPiece($from);

//        if ($piece instanceof King && $board->pieceNeverMovedFromPosition($from)) {
//            var_dump($from);
//        }
        if ($piece instanceof King && $board->pieceNeverMovedFromPosition($from)) {
            $rowDiff = $to->getRow() - $from->getRow();
            $colDiff = $to->getCol() - $from->getCol();
            if ($rowDiff == 0 && (abs($colDiff) == 2)) {
                $singleColMove = $colDiff / 2;
                $row = $from->getRow();
                $col = $from->getCol() + $singleColMove;
                while ($col > 0 && $col < $board->getCols() - 1) {
                    if (!$board->isPositionFree(new Position($row, $col))) {
                        throw new InvalidMoveException(self::CASTLING_ERROR_MESSAGE . " Not all positions on the path are free.");
                    }
                    $col += $singleColMove;
                }
                $rookPos = new Position($row, $col);
                if (!($board->isPositionOccupied($rookPos)
                    && $board->getPiece($rookPos) instanceof Rook
                    && $board->pieceNeverMovedFromPosition($rookPos))) {
                    throw new InvalidMoveException(self::CASTLING_ERROR_MESSAGE . "Rook does not satisfy all conditions.");
                }

                $row = $from->getRow();
                $col = $from->getCol();
                while ($col != $to->getCol()) {
                    $this->checkIfPositionUnderAttack($board, $piece, new Position($row, $col), self::CASTLING_ERROR_MESSAGE);
                    $col += $singleColMove;
                }
                $this->checkIfPositionUnderAttack($board, $piece, new Position($row, $col), self::CASTLING_ERROR_MESSAGE);
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
        $color = $piece->getColor();
        if ($piece instanceof Pawn
            && $to->getRow() - $from->getRow() == $color->getDirection()
            && abs($to->getCol() - $from->getCol()) == 1
            && $board->isPositionFree($to)) {
            $attackedPawnPosition = new Position($from->getRow(), $to->getCol());
            if ($board->isPositionOccupied($attackedPawnPosition)) {
                $attackedPawn = $board->getPiece($attackedPawnPosition);
                if ($attackedPawn instanceof Pawn
                    && $attackedPawn->getColor() !== $color
                    && $attackedPawnPosition == $this->canBeEatenByEnPassant) {
                    return true;
                }
            }
        }
        return false;
    }

    public function move(Move $move, string $pieceForPawnTransformationClass = null): void
    {
        $board = $this->board;
        $boardSnapshot = clone $board;

        $from = $move->getFrom();
        $to = $move->getTo();

        $piece = $board->getPiece($from);

        if (!isset($piece)) {
            throw new InvalidMoveException("There is no piece in ({$from->getRow()}, {$from->getCol()}) position");
        }

        $color = $piece->getColor();
        $enPassantPossibilitySetted = false;
        if ($this->isMoveCastling($move)) {
            $board->move($piece, $move);

            $col = $from->getCol();
            $colDiff = ($to->getCol() - $from->getCol()) / 2;
            while ($col > 0 && $col < $board->getCols() - 1) {
                $col += $colDiff;
            }
            $rookMove = new Move(new Position($from->getRow(), $col), new Position($to->getRow(), $to->getCol() - $colDiff));
            $board->move($board->getPiece($rookMove->getFrom()), $rookMove);

        } else if ($this->isMoveEnPassant($move)) {
            $board->move($piece, $move);
            $attackedPawnPosition = new Position($from->getRow(), $to->getCol());
            $board->removePiece($attackedPawnPosition);

        } else if (in_array($to, $piece->possibleMoves($board, $from))) {
            if ($piece instanceof Pawn
                && abs($to->getRow() - $from->getRow()) == 2) {
                $this->canBeEatenByEnPassant = $to;
                $enPassantPossibilitySetted = true;
            }

            $board->move($piece, $move);

            if ($piece instanceof Pawn && ($to->getRow() == 0 || $to->getRow() == $board->getRows())
                && isset($pieceForPawnTransformationClass)) {
                $pieceForPawnTransformation = new $pieceForPawnTransformationClass($color);
                if ($pieceForPawnTransformation instanceof Piece
                    && !($pieceForPawnTransformationClass instanceof King)) {
                    $board->addPiece($pieceForPawnTransformation, $to);
                }
            }
        } else {
            throw new InvalidMoveException("Unsupported move: from ({$from->getRow()}, {$from->getCol()}) position, to ({$to->getRow()}, {$to->getCol()}) position");
        }

        if (!$enPassantPossibilitySetted) {
            $this->canBeEatenByEnPassant = null;
        }

        if (self::isKingUnderAttack($board, $color)) {
            $this->board = $boardSnapshot;
            throw new InvalidMoveException();
        }
        $this->nextPlayer();
    }

    public static function getOppositeColor(Color $color): Color
    {
        return $color == Color::getWhite() ? Color::getBlack() : Color::getWhite();
    }

    private static function isKingUnderAttack(Board $board, Color $color): bool
    {
        $kingPosition = $board->findPiece(King::class, $color);
        if (!isset($kingPosition)) {
            throw new GameInitializationException("The {$color->getName()} king is expected on the board");
        }
        return $board->isPositionUnderAttack($kingPosition, $color);
    }
}