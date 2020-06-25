<?php


namespace app\chess\pieces;


use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;

/**
 * Class Queen
 * @package app\chess\pieces
 * @see Piece
 * @see AbstractPiece
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class Queen extends AbstractPiece
{
    private static array $rooks;
    private static array $bishops;

    /**
     * Initialization of static fields that contain information
     * about the minimum possible moves of the <var>Queen</var> as an {@see Bishop} and as a {@see Rook}.
     */
    public function addColor(Color $color)
    {
        $colorName = $color->getName();
        $rook = &self::$rooks[$colorName];
        $bishop = &self::$bishops[$colorName];

        if (!isset($rook)) {
            $rook = new Rook($color);
        }
        if (!isset($bishop)) {
            $bishop = new Bishop($color);
        }
    }

    /**
     * @return string string representation of the queen ({@example "WQ" — white queen}).
     */
    public function __toString(): string
    {
        return $this->toStr("Q");
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        $color = $this->color;
        $this->addColor($color);

        $colorName = $color->getName();
        return array_merge(self::$rooks[$colorName]->attackedMoves($board, $position),
            self::$bishops[$colorName]->attackedMoves($board, $position));
    }

    public function normalMoves(Board $board, Position $position): array
    {
        $color = $this->color;
        $this->addColor($color);

        $colorName = $color->getName();
        return array_merge(self::$rooks[$colorName]->normalMoves($board, $position),
            self::$bishops[$colorName]->normalMoves($board, $position));
    }
}