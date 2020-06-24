<?php


namespace api;


use api\exceptions\DatabaseAccessException;
use app\MySqlConnection;

class MySqlModel extends Model
{
    protected $table;
    protected $connection;

    public function __construct()
    {
        $this->connection = MySqlConnection::getConnection();
    }

    public function updateById($id, $data)
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

    public function create($fields) : int
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

    public function all()
    {
        $query = $this->connection->query("SELECT * FROM `{$this->table}`");
        return $query->fetchAll(\PDO::FETCH_CLASS);
    }

    public function getById($id)
    {
        $query = $this->connection->prepare("SELECT * FROM `{$this->table}` WHERE {$this->getIdField()} = $id");
        $query->execute();
        $fetch = $query->fetchAll(\PDO::FETCH_CLASS);
        if (count($fetch) < 1) {
            throw new DatabaseAccessException("There is not model with {$id} id in the database");
        }
        return $fetch[0];
    }

    public function delete($id)
    {
        $query = $this->connection->query("DELETE FROM  `{$this->table}` WHERE {$this->getIdField()} = $id");
        $query->execute();
    }
}