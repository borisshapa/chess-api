<?php

namespace app\chess\game;


use app\chess\Color;
use app\chess\game\initialization\GameInitialization;
use app\chess\moves\Move;

interface Game
{
    public function initialize(GameInitialization $initialization);

    public function move(Move $move);

    public function winningConditions(Color $color) : bool;
}