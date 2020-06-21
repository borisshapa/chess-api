<?php


namespace api;


abstract class Model
{
    public abstract function getById($id);

    public abstract function all();

    public abstract function delete($id);

    public abstract function updateById($id, $data);

    public abstract function create($fields) : int;

    protected function getIdField() {
        return "id";
    }
}