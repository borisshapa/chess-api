<?php

namespace app\chess\board;

use utils\Pair;

class Position extends Pair {
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