<?php

namespace app\chess\board;

class Position {
    private $row;
    private $col;

    public function __construct(int $row, int $col)
    {
        $this->row = $row;
        $this->col = $col;
    }

    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function getCol(): int
    {
        return $this->col;
    }
}