<?php


namespace api;

/**
 * Class Model
 * A class for working with a table in a database.
 * @package api
 */
abstract class Model
{
    /**
     * @param int $id the id of the database row to be returned.
     * @return mixed the row of the database table by row id.
     */
    public abstract function getById(int $id);

    /**
     * @return array all rows of the database table.
     */
    public abstract function all(): array;

    /**
     * Deletes a row from the database table by id.
     * @param int $id id of the row of the database table to be deleted.
     */
    public abstract function delete(int $id): void;

    /**
     * Changes the values in the row of the database table by id.
     * @param int $id id of the database row to be changed.
     * @param array $data dictionary with new data.
     */
    public abstract function updateById(int $id, array $data): void;

    /**
     * Creates a new row in the database table.
     * @param array $fields Values in the row
     * @return int id of created string.
     */
    public abstract function create(array $fields): int;

    /**
     * @return string the name of the column in the database table that stores the row id.
     */
    protected function getIdField(): string
    {
        return "id";
    }
}