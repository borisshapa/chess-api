<?php


namespace app\chess\players;


use app\chess\Color;
use app\chess\pieces\Piece;

interface Player
{
    public function getColor(): Color;

    public function getName(): string;
}