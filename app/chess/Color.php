<?php

namespace app\chess;

/**
 * @method static getBlack()
 * @method static getWhite()
 */
class Color {
    private $name;
    private $direction;
    private static $COLORS = array();
    private static $SERIALIZED_COLORS = array('W' => 'White', 'B' => 'Black');

    public function __construct(string $name, int $direction)
    {
        $this->name = $name;
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDirection(): int
    {
        return $this->direction;
    }

    public static function __callStatic($name, $arguments) : Color
    {
        if (preg_match('/get([^\s]+)/', $name, $matches)) {
            $colorName = $matches[1];
            if (isset($colorName)) {
                $color = self::$COLORS[$colorName];
                if (!isset($color)) {
                    $direction = $colorName == "Black" ? 1 : -1;
                    self::$COLORS[$colorName] = new Color($colorName, $direction);
                    self::$SERIALIZED_COLORS[$colorName[0]] = $colorName;
                }
                return self::$COLORS[$colorName];
            }
        }
        // TODO:
        throw new \Exception("Unsupported method");
    }
}