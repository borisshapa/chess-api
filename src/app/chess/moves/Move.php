<?php

namespace app\chess\moves;

use app\chess\board\Position;

/**
 * Class Move
 * Description of move.
 * @package app\chess\moves
 * @see Position
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class Move
{
    private Position $from;
    private Position $to;

    /**
     * Move constructor.
     * @param Position $from what position the move is made from
     * @param Position $to what position the move is made to
     */
    public function __construct(Position $from, Position $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return Position what position the move is made from
     */
    public function getFrom(): Position
    {
        return $this->from;
    }

    /**
     * @return Position what position the move is made to
     */
    public function getTo(): Position
    {
        return $this->to;
    }
}