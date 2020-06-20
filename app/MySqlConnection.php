<?php


namespace app;


class MySqlConnection
{
    private static $connection;

    /**
     * @return mixed
     */
    public static function getConnection()
    {
        if (!self::$connection) {
            self::$connection = new \PDO("mysql:dbname=chess;host=localhost", "root");
        }
        return self::$connection;
    }
}