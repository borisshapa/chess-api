<?php


namespace app\chess\pieces;


use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;

class Queen extends AbstractPiece
{
    private static array $rooks;
    private static array $bishops;

    public function __construct(Color $color)
    {
        parent::__construct($color);
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

    public function __toString(): string
    {
        return $this->toStr("Q");
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        $colorName = $this->color->getName();
        return array_merge(self::$rooks[$colorName]->attackedMoves($board, $position),
            self::$bishops[$colorName]->attackedMoves($board, $position));
    }

    public function normalMoves(Board $board, Position $position): array
    {
        $colorName = $this->color->getName();
        return array_merge(self::$rooks[$colorName]->normalMoves($board, $position),
            self::$bishops[$colorName]->normalMoves($board, $position));
    }
}