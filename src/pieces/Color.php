<?php

namespace chess\pieces;

interface Color
{
    public function getDirection() : int;
    public function getColor() : string;
}

final class Black implements Color {

    public function getDirection(): int
    {
        return -1;
    }

    public function getColor(): string
    {
        return "black";
    }
}

final class White implements Color {

    public function getDirection(): int
    {
        return 1;
    }

    public function getColor(): string
    {
        return "white";
    }
}