<?php


namespace app\chess\players;


use app\chess\Color;

/**
 * Interface Player
 * Description of methods for working with chess players.
 * @package app\chess\players
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
interface Player
{
    /**
     * @return Color player color
     */
    public function getColor(): Color;

    /**
     * @return string player name
     */
    public function getName(): string;
}