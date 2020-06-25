<?php

namespace app\chess\game;

use app\chess\board\Board;
use app\chess\board\ClassicBoard;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\exceptions\GameException;
use app\chess\exceptions\GameInitializationException;
use app\chess\exceptions\InvalidMoveException;
use app\chess\moves\Move;
use app\chess\pieces\King;
use app\chess\pieces\Pawn;
use app\chess\pieces\Piece;
use app\chess\pieces\Rook;
use app\chess\players\Player;

/**
 * Class ClassicGame
 * A game of chess with classic rules.
 * @package app\chess\game
 * @see Game
 * @see AbstractGame
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class ClassicGame extends AbstractGame
{
    private ?Position $canBeEatenByEnPassant;
    private const CASTLING_ERROR_MESSAGE = "Unable to castle.";

    /**
     * ClassicGame constructor.
     * Creates a classic game.
     * @param Player $player1 first player
     * @param Player $player2 second player
     * @throws GameException If there is no player playing with white pieces among two players.
     */
    public function __construct(Player $player1, Player $player2)
    {
        parent::__construct(array($player1, $player2), new ClassicBoard(), Color::getWhite());
    }

    /**
     * @inheritDoc
     * @throws GameInitializationException if there is no opponent {@see King} on the {@see Board}
     */
    public function checkIfColorWon(Color $color): bool
    {
        $rivalColor = self::getOppositeColor($color);
        if (!self::isKingUnderAttack($this->board, $rivalColor)) {
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

    /**
     * Throws an exception if the position is under attack by an opponent.
     * @param Board $board the board on which the check is done.
     * @param Color $color if there was a {@see Piece} of this color in the position,
     * if would be under attack.
     * @param Position $position checked position
     * @param string $errorMessage error message if thrown.
     * @throws InvalidMoveException if the position is under attack by an opponent.
     */
    private static function checkIfPositionUnderAttack(Board $board, Color $color,
                                                       Position $position, string $errorMessage): void
    {
        if ($board->isPositionUnderAttack($position, $color)) {
            throw new InvalidMoveException($errorMessage);
        }
    }

    /**
     * Checks if the move is castling.
     * @param Move $move checked move
     * @return bool <var>true</var> if and only if the passed move is castling, <var>false</var> otherwise.
     * @throws InvalidMoveException if the {@see King} takes more than one step,
     * but the conditions for castling are not fulfilled.
     */
    private function isMoveCastling(Move $move): bool
    {
        $board = $this->board;

        $from = $move->getFrom();
        $to = $move->getTo();

        $piece = $board->getPiece($from);

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
                    $this->checkIfPositionUnderAttack($board, $piece->getColor(), new Position($row, $col), self::CASTLING_ERROR_MESSAGE);
                    $col += $singleColMove;
                }
                $this->checkIfPositionUnderAttack($board, $piece->getColor(), new Position($row, $col), self::CASTLING_ERROR_MESSAGE);
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the move is en passant.
     * @param Move $move checked move
     * @return bool <var>true</var> if and only if the move is en passant, <var>false</var> otherwise.
     */
    private function isMoveEnPassant(Move $move): bool
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

    /**
     * @inheritDoc
     * A player makes a move defined by a field {@see AbstractGame::$currentPlayer}.
     * After that, the move passes to another player.
     * @param string|null $pieceForPawnTransformationClass (name of piece class)
     * if a pawn moves to a far row from its initial position,
     * then it turns into this piece. If another move was made, then this parameter is ignored,
     * also if the parameter was not passed with such a move, the pawn remains itself.
     * @throws GameInitializationException there is no {@see King} on the {@see Board} that belongs
     * to the {@see Player} making the move.
     * @throws InvalidMoveException if there is no {@see Piece} in the position with which the move is made,
     * a {@see Piece} that is in the position which the move is made from can not moves like that or
     * after the player’s move, his king was under attack.
     */
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
            throw new InvalidMoveException("After the player’s move, his king was under attack.");
        }
        $this->nextPlayer();
    }

    /**
     * @param Color $color the color for which the opposite is sought.
     * @return Color the color of the opponent of the player with the passed color.
     */
    public static function getOppositeColor(Color $color): Color
    {
        return $color == Color::getWhite() ? Color::getBlack() : Color::getWhite();
    }

    /**
     * Checks
     * @param Board $board the board on which the check is done.
     * @param Color $color which king is being checked
     * @return bool <var>true</var> if and only if the {@see King} of the specified
     * color is under attack from an opponent’s piece, <var>false</var> otherwise.
     * @throws GameInitializationException if there is no {@see King} of the specified color on the board.
     */
    private static function isKingUnderAttack(Board $board, Color $color): bool
    {
        $kingPosition = $board->findPiece(King::class, $color);
        if (!isset($kingPosition)) {
            throw new GameInitializationException("The {$color->getName()} king is expected on the board");
        }
        return $board->isPositionUnderAttack($kingPosition, $color);
    }
}