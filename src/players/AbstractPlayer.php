<?php


namespace chess\players;


use chess\pieces\Color;

abstract class AbstractPlayer implements Player
{
    private Color $color;
    private $name;

    public function __construct(string $name, Color $color)
    {
        $this->name = $name;
        $this->color = $color;
    }

    /**
     * @return Color
     */
    public function getColor(): Color
    {
        return $this->color;
    }
}