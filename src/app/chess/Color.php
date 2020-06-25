<?php

namespace app\chess;

use app\chess\exceptions\ChessException;

/**
 * Color Class
 * Class for working with colors in chess. Uniquely stores each color in a static structure without re-creating.
 * @method static getBlack() returns black color
 * @method static getWhite() returns white color
 * @package app\chess
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class Color
{
    private string $name;
    private int $direction;
    private static array $COLORS = array();

    private function __construct(string $name, int $direction)
    {
        $this->name = $name;
        $this->direction = $direction;
    }

    /**
     * @return string color name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int the direction in which the pieces of
     * this color move ({@example in the classic game 1 for black, -1 for white}).
     */
    public function getDirection(): int
    {
        return $this->direction;
    }

    /**
     * Universal function for receiving color.
     * If the called static method has the get prefix,
     * then the color with the name corresponding to the
     * string that comes after the get is returned.
     * If this method has already been called for the specified color,
     * then the object of this color will not be recreated.
     * Objects representing colors are stored uniquely in a static structure.
     * @param string $name the called method in the format 'getColorName' ({@example 'getWhite'})
     * @param array $arguments when receiving color, arguments passed to the function are ignored.
     * @return Color the object responsible for the color with the name specified in the name of the called method
     * @throws ChessException the color method does not match the format 'getColorName'
     */
    public static function __callStatic(string $name, array $arguments): Color
    {
        if (preg_match('/get([^\s]+)/', $name, $matches)) {
            $colorName = $matches[1];
            if (isset($colorName)) {
                if (!isset(self::$COLORS[$colorName])) {
                    $direction = $colorName == "Black" ? 1 : -1;
                    self::$COLORS[$colorName] = new Color($colorName, $direction);
                }
                return self::$COLORS[$colorName];
            }
        }
        throw new ChessException("The color method does not match the format 'getColorName'");
    }
}