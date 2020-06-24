<?php

namespace app\chess\board;

use utils\IntPair;

class Position extends IntPair {
    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->getFirst();
    }

    /**
     * @return int
     */
    public function getCol(): int
    {
        return $this->getSecond();
    }
}