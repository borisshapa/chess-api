<?php


namespace app\chess\players;


use app\chess\Color;

class ClassicPlayer implements Player
{
    private Color $color;
    private string $name;

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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}