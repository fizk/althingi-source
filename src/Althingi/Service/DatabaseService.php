<?php

namespace Althingi\Service;

use DateTime;

trait DatabaseService
{

    /**
     * This is a simple utility function that creates
     * a SQL INSERT string bases on the name of the table
     * (1st parameter) and an object (2nd param).
     *
     * The INSERT string does not inject the actual values
     * of the object but places a placeholder (:value_name)
     * so this this string can be used in `prepare / execute`
     * operation.
     *
     *
     * @param string $table
     * @param \stdClass $data
     * @return string valid MySQL insert string
     */
    protected function insertString($table, $data)
    {
        $data = array_keys((array)$data);
        $columns = implode(',', array_map(function ($i) {
            return " `{$i}`";
        }, $data));
        $values = implode(',', array_map(function ($i) {
            return " :{$i}";
        }, $data));

        return "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});";
    }

    /**
     * This is a simple utility function that creates
     * a SQL UPDATE string bases on the name of the table
     * (1st parameter) and an object (2nd param)
     * as well as a condition.
     *
     * The UPDATE string does not inject the actual values
     * of the object but places a placeholder (:value_name)
     * so this the string can be used in `prepare / execute`
     * operation.
     *
     * @param string $table
     * @param \stdClass $data
     * @param string $condition
     * @return string valid MySQL update string
     */
    protected function updateString($table, $data, $condition)
    {
        $data = array_keys((array)$data);
        $columns = implode(',', array_map(function ($i) {
            return " `{$i}` = :{$i}";
        }, $data));

        return "UPDATE `{$table}` SET {$columns} WHERE {$condition};";
    }

    /**
     * Convert \stdClass to array.
     * Convert \DateTime to string
     * @param $data
     * @return array
     */
    protected function convert($data)
    {
        return array_map(function ($i) {
            if ($i instanceof DateTime) {
                return $i->format('Y-m-d H:i:s');
            }
            return $i;
        }, (array)$data);
    }
}
