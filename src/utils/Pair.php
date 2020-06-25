<?php


namespace utils;

/**
 * Class Pair
 * A structure that stores ordered pair of items.
 * @package utils
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class Pair
{
    private $first;
    private $second;

    /**
     * Pair constructor.
     * Creates the pair by passed two elements.
     * @param $first
     * @param $second
     */
    public function __construct($first, $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * @return mixed first element of the pair
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @return mixed second element of the pair
     */
    public function getSecond()
    {
        return $this->second;
    }
}