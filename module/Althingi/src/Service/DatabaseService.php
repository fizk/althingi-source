<?php

namespace Althingi\Service;

use Althingi\Model\ModelInterface;
use DateTime;

trait DatabaseService
{
    /**
     * This is a simple utility function that creates
     * a SQL INSERT string bases on the name of the table
     * (1st parameter) and an model (2nd param).
     *
     * The INSERT string does not inject the actual values
     * of the object but places a placeholder (:value_name)
     * so this this string can be used in `prepare / execute`
     * operation.
     *
     * @param string $table
     * @param ModelInterface $data
     * @return string valid MySQL insert string
     */
    protected function toInsertString(string $table, ModelInterface $data): string
    {
        $data = array_keys($data->jsonSerialize());
        $columns = implode(',', array_map(function ($i) {
            return " `{$i}`";
        }, $data));
        $values = implode(',', array_map(function ($i) {
            return " :{$i}";
        }, $data));

        return "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});";
    }

    /**
     * @param $table
     * @param ModelInterface $data
     * @return string
     */
    protected function toSaveString(string $table, ModelInterface $data): string
    {
        $data = array_keys($data->jsonSerialize());
        $columns = implode(',', array_map(function ($i) {
            return " `{$i}`";
        }, $data));
        $values = implode(',', array_map(function ($i) {
            return " :{$i}";
        }, $data));
        $update = implode(', ', array_map(function ($i) {
            return "`{$i}` = :{$i}";
        }, $data));

        return "INSERT INTO `{$table}` ({$columns}) VALUES ({$values}) on duplicate key update {$update};";
    }

    /**
     * This is a simple utility function that creates
     * a SQL UPDATE string bases on the name of the table
     * (1st parameter) and a model (2nd param)
     * as well as a condition.
     *
     * The UPDATE string does not inject the actual values
     * of the object but places a placeholder (:value_name)
     * so this the string can be used in `prepare / execute`
     * operation.
     *
     * @param string $table
     * @param ModelInterface $data
     * @param string $condition
     * @return string valid MySQL update string
     */
    protected function toUpdateString(string $table, ModelInterface $data, $condition): string
    {
        $data = array_keys($data->jsonSerialize());
        $columns = implode(',', array_map(function ($i) {
            return " `{$i}` = :{$i}";
        }, $data));

        return "UPDATE `{$table}` SET {$columns} WHERE {$condition};";
    }

    /**
     * Takes in a model and returns a key=>value Array.
     *
     * This method also converts value objects like new DateTime()
     * into a string (Y-m-d H:i:s).... so if one of the values in
     * the model is a DateTime, it will be converted into something
     * that a Database would understand.
     *
     * Same goes for boolean values, since MySQL doesn't have a bool type
     * but PHP does, this function converts bools to ints.
     *
     * @param ModelInterface $data
     * @return array
     */
    protected function toSqlValues(ModelInterface $data): array
    {
        return array_map(function ($i) {
            if ($i instanceof DateTime) {
                return $i->format('Y-m-d H:i:s');
            }
            if (is_bool($i)) {
                return (int) $i;
            }
            return $i;
        }, $data->toArray());
    }
}
