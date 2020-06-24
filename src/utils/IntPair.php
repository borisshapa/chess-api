<?php


namespace utils;


// TODO rename to int pair
class IntPair
{
    private $first;
    private $second;

    public function __construct(int $first, int $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * @return mixed
     */
    public function getFirst() : int
    {
        return $this->first;
    }

    /**
     * @return mixed
     */
    public function getSecond() : int
    {
        return $this->second;
    }
}