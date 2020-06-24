<?php


namespace api;


use api\exceptions\DatabaseAccessException;
use app\MySqlConnection;

/**
 * Class MySqlModel
 * Work with MySql database table.
 * @package api
 */
class MySqlModel extends Model
{
    /**
     * @var string the name of MySql database table that queries are accessing.
     */
    protected string $table;

    /**
     * @var MySqlConnection|mixed|\PDO connection to the MySql database.
     */
    protected MySqlConnection $connection;

    /**
     * MySqlModel constructor.
     */
    public function __construct()
    {
        $this->connection = MySqlConnection::getConnection();
    }

    public function updateById(int $id, $data): void
    {
        $dataStr = implode(", ", array_map(function ($key) use ($data) {
            return "`$key` = :$key";
        }, array_keys($data)));
        $query = $this->connection->prepare("UPDATE `{$this->table}` SET {$dataStr} WHERE {$this->getIdField()} = $id");

        foreach ($data as $key => $field) {
            $query->bindParam(":$key", $field);
        }
        $query->execute();
    }

    public function create(array $fields): int
    {
        $keysArray = array_keys($fields);
        $keys = implode(", ", $keysArray);
        $placeholders = implode(", ", array_map(function ($x) {
            return ":$x";
        }, $keysArray));
        $query = $this->connection->prepare("INSERT INTO `{$this->table}` ({$keys}) VALUES ($placeholders)");

        foreach ($fields as $key => &$field) {
            $query->bindParam(":$key", $field);
        }
        $query->execute();
        return $this->connection->lastInsertId();
    }

    public function all(): array
    {
        $query = $this->connection->query("SELECT * FROM `{$this->table}`");
        return $query->fetchAll(\PDO::FETCH_CLASS);
    }

    public function getById(int $id)
    {
        $query = $this->connection->prepare("SELECT * FROM `{$this->table}` WHERE {$this->getIdField()} = $id");
        $query->execute();
        $fetch = $query->fetchAll(\PDO::FETCH_CLASS);
        if (count($fetch) < 1) {
            throw new DatabaseAccessException("There is not model with {$id} id in the database");
        }
        return $fetch[0];
    }

    public function delete(int $id): void
    {
        $query = $this->connection->query("DELETE FROM  `{$this->table}` WHERE {$this->getIdField()} = $id");
        $query->execute();
    }
}