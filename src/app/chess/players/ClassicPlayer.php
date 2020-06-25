<?php


namespace app\chess\players;


use app\chess\Color;

/**
 * Class ClassicPlayer
 * Classic chess player.
 * @package app\chess\players
 * @see Player
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class ClassicPlayer implements Player
{
    private Color $color;
    private string $name;

    /**
     * ClassicPlayer constructor.
     * Creates a new chess player
     * @param string $name player name
     * @param Color $color player color
     */
    public function __construct(string $name, Color $color)
    {
        $this->name = $name;
        $this->color = $color;
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function getName(): string
    {
        return $this->name;
    }
}