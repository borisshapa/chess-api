<?php

namespace app\chess\moves;

use app\chess\board\Position;

class Move
{
    private $from;
    private $to;

    public function __construct(Position $from, Position $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return Position
     */
    public function getFrom(): Position
    {
        return $this->from;
    }

    /**
     * @return Position
     */
    public function getTo(): Position
    {
        return $this->to;
    }
}