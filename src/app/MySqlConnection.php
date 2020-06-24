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
        $dbName = DB_NAME;
        $host = HOST;
        if (!self::$connection) {
            self::$connection = new \PDO("mysql:dbname={$dbName};host={$host}", USERNAME);
        }
        return self::$connection;
    }
}