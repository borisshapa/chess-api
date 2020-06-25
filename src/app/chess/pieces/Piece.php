<?php

namespace app\chess\pieces;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;

/**
 * Interface Piece
 * Interface for creating a chess piece.
 * @package app\chess\pieces
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
interface Piece
{
    /**
     * @return Color piece color
     */
    public function getColor(): Color;

    /**
     * According to the situation on the board,
     * returns the positions on which the opponent’s pieces stand that can be eaten.
     * @param Board $board where to check positions
     * @param Position $position where is this piece located
     * @return array array of the positions on which the opponent’s pieces stand that can be eaten.
     */
    public function attackedMoves(Board $board, Position $position): array;

    /**
     * According to the situation on the board,
     * returns the positions to which the piece can move, while not eating anyone.
     * @param Board $board where to check positions
     * @param Position $position where is this piece located
     * @return array array of the positions to which the piece can move, while not eating anyone.
     */
    public function normalMoves(Board $board, Position $position): array;

    /**
     * According to the situation on the board,
     * returns all possible positions where the piece can move.
     * @param Board $board where to check positions
     * @param Position $position where is this piece located
     * @return array array of all possible positions where the piece can move.
     */
    public function possibleMoves(Board $board, Position $position): array;
}