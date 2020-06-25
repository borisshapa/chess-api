<?php

namespace app\chess\pieces;

use app\chess\exceptions\OutOfBoardException;
use app\chess\board\{
    Board,
    Position
};

use app\chess\Color;

/**
 * Class AbstractPiece
 * Template for creating chess pieces.
 * @package app\chess\pieces
 * @see Piece
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
abstract class AbstractPiece implements Piece
{
    /**
     * @var Color piece color
     */
    protected Color $color;

    /**
     * @var string literal representation of a piece in chess notation.
     */
    protected string $pieceDesignation;

    /**
     * AbstractPiece constructor.
     * @param Color $color piece color
     */
    public function __construct(Color $color, string $pieceDesignation)
    {
        $this->color = $color;
        $this->pieceDesignation = $pieceDesignation;
    }

    /**
     * @return Color piece color
     */
    public function getColor(): Color
    {
        return $this->color;
    }

    public function possibleMoves(Board $board, Position $position): array
    {
        return array_merge($this->attackedMoves($board, $position),
            $this->normalMoves($board, $position));
    }

    /**
     * Checks if the color of the piece matches the passed color.
     * @param Piece $piece checked piece
     * @param Color $color expected color
     * @return bool if and only if the color of the piece matches the expected color
     */
    protected static function checkColor(Piece $piece, Color $color): bool
    {
        return $piece->getColor() == $color;
    }

    /**
     * Returns the possible positions that the piece can move to without eating anyone.
     * <b>For pieces whose move consists of repeating several small moves
     * ({@example A rook move may consist of several moves that move it one square}).</b>
     * @param Board $board where to check positions
     * @param Position $position where is this piece ({@see Rook}, {@see Bishop} or {@see Queen}) located
     * @param array $singleMoves moves that move a piece one cell.
     * @return array array of the positions to which the piece can move, while not eating anyone
     */
    protected function longNormalMoves(Board $board, Position $position, array $singleMoves): array
    {
        $possibleMoves = array();
        foreach ($singleMoves as $move) {
            $rowOffset = $move->getFirst();
            $colOffset = $move->getSecond();

            $newPosition = new Position($position->getRow() + $rowOffset,
                $position->getCol() + $colOffset);
            while ($this->isPositionValidAndFree($board, $newPosition)) {
                array_push($possibleMoves, $newPosition);
                $newPosition = new Position($newPosition->getRow() + $rowOffset,
                    $newPosition->getCol() + $colOffset);
            }
        }
        return $possibleMoves;
    }

    /**
     * Returns the positions on which the opponent’s pieces stand that can be eaten.
     * <b>For pieces whose move consists of repeating several small moves
     * ({@example A rook move may consist of several moves that move it one square}).</b>
     * @param Board $board where to check positions
     * @param Position $position where is this piece ({@see Rook}, {@see Bishop} or {@see Queen}) located
     * @param array $singleMoves moves that move a piece one cell.
     * @return array array of the positions on which the opponent’s pieces stand that can be eaten.
     */
    protected function longAttackMoves(Board $board, Position $position, array $singleMoves): array
    {
        $color = $this->getColor();
        $possibleMoves = array();
        foreach ($singleMoves as $move) {
            $rowOffset = $move->getFirst();
            $colOffset = $move->getSecond();

            $newPosition = new Position($position->getRow() + $rowOffset,
                $position->getCol() + $colOffset);
            while ($board->isPositionValid($newPosition)) {
                if (self::isPositionValidAndOccupied($board, $newPosition)) {
                    $attackedPiece = $board->getPiece($newPosition);
                    if (!$this->checkColor($attackedPiece, $color)) {
                        array_push($possibleMoves, $newPosition);
                    }
                    break;
                }
                $newPosition = new Position($newPosition->getRow() + $rowOffset,
                    $newPosition->getCol() + $colOffset);
            }
        }
        return $possibleMoves;
    }

    /**
     * Returns the positions on which the opponent’s pieces stand that can be eaten.
     * <b>For pieces whose move consists of one movement({@example {@see Knight} or {@see King}}).</b>
     * @param Board $board where to check positions
     * @param Position $position where is this piece ({@see Knight} or {@see King}) located
     * @param array $singleMoves moves that move a piece one cell.
     * @return array array of the positions on which the opponent’s pieces stand that can be eaten.
     */
    protected function singleAttackMoves(Board $board, Position $position, array $singleMoves): array
    {
        $color = $this->getColor();
        $possibleMoves = array();
        foreach ($singleMoves as $move) {
            $row = $position->getRow() + $move->getFirst();
            $col = $position->getCol() + $move->getSecond();
            $attackedPosition = new Position($row, $col);
            if (self::isPositionValidAndOccupied($board, $attackedPosition)) {
                $attackedPiece = $board->getPiece($attackedPosition);
                if (!self::checkColor($attackedPiece, $color)) {
                    array_push($possibleMoves, $attackedPosition);
                }
            }
        }
        return $possibleMoves;
    }

    /**
     * Returns the possible positions that the piece can move to without eating anyone.
     * <b>For pieces whose move consists of one movement({@example {@see Knight} or {@see King}}).</b>
     * @param Board $board where to check positions
     * @param Position $position where is this piece ({@see Knight} or {@see King}) located
     * @param array $singleMoves moves that move a piece one cell.
     * @return array array of the positions to which the piece can move, while not eating anyone
     */
    protected function singleNormalMoves(Board $board, Position $position, array $singleMoves): array
    {
        $possibleMoves = array();
        foreach ($singleMoves as $move) {
            $row = $position->getRow() + $move->getFirst();
            $col = $position->getCol() + $move->getSecond();
            $newPosition = new Position($row, $col);
            if (self::isPositionValidAndFree($board, $newPosition)) {
                array_push($possibleMoves, $newPosition);
            }
        }
        return $possibleMoves;
    }

    /**
     * Checks if the position is correct and free for the given board.
     * @param Board $board where to check
     * @param Position $position what position to check
     * @return bool <var>true</var> if and only if the position is correct and free for the given board
     */
    protected function isPositionValidAndFree(Board $board, Position $position): bool
    {
        try {
            return $board->isPositionFree($position);
        } catch (OutOfBoardException $e) {
            return false;
        }
    }

    /**
     * Checks if the position is correct and occupied for the given board.
     * @param Board $board where to check
     * @param Position $position what position to check
     * @return bool <var>true</var> if and only if the position is correct and occupied for the given board
     */
    protected static function isPositionValidAndOccupied(Board $board, Position $position): bool
    {
        try {
            return $board->isPositionOccupied($position);
        } catch (OutOfBoardException $e) {
            return false;
        }
    }

    /**
     * @return string the string value of the piece,
     * where the first letter is a color and the second is a type ({@example "WB" — white bishop}).
     */
    public function __toString()
    {
        return $this->getColor()->getName()[0] . $this->pieceDesignation;
    }
}