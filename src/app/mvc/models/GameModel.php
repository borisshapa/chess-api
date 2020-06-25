<?php

namespace app\mvc\models;

use api\Model;
use api\MySqlModel;

/**
 * Class GameModel
 * A model that stores information about the current state of the game.
 * @package app\mvc\models
 * @see Model
 * @see MySqlModel
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class GameModel extends MySqlModel
{
    /**
     * @var string the name of the table that stores all games.
     */
    protected string $table = "game";
}