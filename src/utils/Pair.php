<?php


namespace utils;


// TODO rename to int pair
class Pair
{
    private $first;
    private $second;

    public function __construct($first, $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * @return mixed
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @return mixed
     */
    public function getSecond()
    {
        return $this->second;
    }
}