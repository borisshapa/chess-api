<?php


namespace app;


use PDO;

/**
 * Class MySqlConnection
 * Creates a database connection using data from a file <var>Config.php</var>.
 * @package app
 * @see PDO
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class MySqlConnection
{
    private static ?PDO $connection = null;

    /**
     * If there was no connection yet,
     * it creates a new database connection and returns it,
     * otherwise it returns the existing one.
     * @return PDO сonnection to the database, which is established by the parameters from the file <var>Config.php</var>.
     */
    public static function getConnection(): PDO
    {
        $dbName = DB_NAME;
        $host = HOST;
        if (!self::$connection) {
            self::$connection = new PDO("mysql:dbname={$dbName};host={$host}", USERNAME);
        }
        return self::$connection;
    }
}