<?php


namespace api;


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

    public function create($fields)
    {
        $keysArray = array_keys($fields);
        $keys = implode(", ", $keysArray);
        $placeholders = implode(", ", array_map(function ($x) {
            return ":$x";
        }, $keysArray));
        $query = $this->connection->prepare("INSERT INTO `{$this->table}` ({$keys}) VALUES ($placeholders)");

        foreach ($fields as $key => &$field) {
            var_dump($field);
            $query->bindParam(":$key", $field);
        }
        var_dump($query->queryString);
        $query->execute();
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
        return $query->fetchAll(\PDO::FETCH_CLASS)[0];
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}