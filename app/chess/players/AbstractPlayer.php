<?php


namespace app\chess\players;


use app\chess\Color;
use app\chess\pieces\King;
use app\chess\Util;

require_once "vendor/autoload.php";

abstract class AbstractPlayer implements Player
{
    private $color;
    private $name;
    protected $pieces;

    public function __construct(string $name, Color $color)
    {
        $this->name = $name;
        $this->color = $color;
        $this->pieces = array();
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

    /**
     * @return array
     */
    public function getPieces(): array
    {
        return $this->pieces;
    }
}