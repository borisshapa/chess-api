<?php

namespace app\chess\board;

use app\chess\Color;
use app\chess\moves\Move;
use app\chess\pieces\Piece;

/**
 * Interface Board
 * Chess board interface.
 * @package app\chess\board
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
interface Board
{
    /**
     * @return int number of rows of a chess board.
     */
    public function getRows(): int;

    /**
     * @return int number of columns of a chess board.
     */
    public function getCols(): int;

    /**
     * Adds a piece to the board.
     * @param Piece $piece piece to add
     * @param Position $position where to put the {@see Piece}.
     * A position is a pair of row number and column number,
     * counting from the upper left corner of the board, starting from 0.
     */
    public function addPiece(Piece $piece, Position $position): void;

    /**
     * Removes a piece from the specified position.
     * @param Position $position where to remove the piece from.
     * A position is a pair of row number and column number,
     * counting from the upper left corner of the board, starting from 0.
     */
    public function removePiece(Position $position): void;

    /**
     * Returns the piece at the specified position.
     * @param Position $position where to get the piece.
     * A position is a pair of row number and column number,
     * counting from the upper left corner of the board, starting from 0.
     * @return Piece|null piece from a {@see Position} if it is occupied or <var>null</var> otherwise
     */
    public function getPiece(Position $position): ?Piece;

    /**
     * Make a {@see Move} on the chess board.
     * @param Piece $piece the piece that moves
     * @param Move $move piece move
     */
    public function move(Piece $piece, Move $move): void;

    /**
     * Finds the position of a {@see Piece} with a specific color.
     * If several pieces, returns any position.
     * @param string $piece (name of piece class) which piece to find
     * @param Color $color piece color
     * @return Position|null the position of the piece,
     * if the piece with that color is on the board, <var>null</var> otherwise.
     * A position is a pair of row number and column number,
     * counting from the upper left corner of the board, starting from 0.
     */
    public function findPiece(string $piece, Color $color): ?Position;

    /**
     * Checks if the {@see Piece} is in {@see Position} and it never moved.
     * @param Position $position checked position.
     * A position is a pair of row number and column number,
     * counting from the upper left corner of the board, starting from 0.
     * @return bool <var>true</var> if and only if there is a {@see Piece} in the {@see Position} and it never moved,
     * <var>false</var> otherwise.
     */
    public function pieceNeverMovedFromPosition(Position $position): bool;

    /**
     * Checks if the {@see Position} correct for the board.
     * @param Position $position checked position.
     * A position is a pair of row number and column number,
     * counting from the upper left corner of the board, starting from 0.
     * @return bool <var>true</var> if and only if the {@see Position} is correct, <var>false</var> otherwise.
     */
    public function isPositionValid(Position $position): bool;

    /**
     * Checks if the {@see Position} on the board is valid and free.
     * @param Position $position checked position.
     * A position is a pair of row number and column number,
     * counting from the upper left corner of the board, starting from 0.
     * @return bool <var>true</var> if and only if the {@see Position} is valid and free, <var>false</var> otherwise.
     */
    public function isPositionFree(Position $position): bool;

    /**
     * Checks if the {@see Position} on the board is valid and occupied.
     * @param Position $position checked position.
     * A position is a pair of row number and column number,
     * counting from the upper left corner of the board, starting from 0.
     * @return bool <var>true</var> if and only if the {@see Position} is valid and occupied, <var>false</var> otherwise.
     */
    public function isPositionOccupied(Position $position): bool;

    /**
     * Checks if the {@see Position} is under attack of the colors opposite to the passed one.
     * @param Position $position checked position.
     * A position is a pair of row number and column number,
     * counting from the upper left corner of the board, starting from 0.
     * @param Color $color attacked color
     * @return bool <var>true</var> if and only if the {@see Position} is under attack of the colors opposite to the passed one,
     * <var>false</var> otherwise.
     */
    public function isPositionUnderAttack(Position $position, Color $color): bool;
}
