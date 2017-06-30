<?php

namespace PhpDB;


use PhpDB\Exceptions\InvalidNameException;
use PhpDB\Exceptions\InvalidTableDefinitionException;
use PhpDB\Exceptions\UnsupportedTypeException;

/**
 * A representation of a table, stores data, can query data using where clause
 * @package PhpDB
 */
class Table
{
    private $name;
    private $columnDefinitions;
    private $supportedTypes = ['int', 'varchar'];
    private $data = [];

    public function __construct($name, $columnDefinitions)
    {
        // name can only contain letters, numbers, and underscors
        if (preg_match('/[^a-zA-Z0-9_]/', $name)) {
            throw new InvalidNameException("Name $name is invalid");
        }
        // cannot have table with no column definitions supplied
        if (!is_array($columnDefinitions) || count($columnDefinitions) === 0) {
            throw new InvalidTableDefinitionException('No columns defined');
        }
        $this->checkForInvalidColumnNames($columnDefinitions);
        $this->checkForUnsupportedTypes($columnDefinitions);

        $this->name = $name;
        $this->columnDefinitions = $columnDefinitions;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getColumnDefinitions()
    {
        return $this->columnDefinitions;
    }

    /**
     * @param array $columnDefinitions
     * @throws UnsupportedTypeException if any column type is not supported
     */
    private function checkForUnsupportedTypes($columnDefinitions)
    {
        foreach ($columnDefinitions as $type) {
            if (!in_array($type, $this->supportedTypes)) {
                throw new UnsupportedTypeException("Type $type is not supported");
            }
        }
    }

    /**
     * @param array $columnDefinitions
     * @throws InvalidNameException if any name contains anything other than letters, numbers, and underscores
     */
    private function checkForInvalidColumnNames($columnDefinitions)
    {
        foreach ($columnDefinitions as $name => $type) {
            if (preg_match('/[^a-zA-Z0-9_]/', $name)) {
                throw new InvalidNameException("Column name $name is invalid");
            }
        }
    }

    public function insert($data)
    {
        $insertData = [];

        // go through all columns in table
        foreach ($this->columnDefinitions as $columnName => $type) {
            // if value is supplied for the column
            if (array_key_exists($columnName, $data)) {
                switch ($type) {
                    case 'int':
                        // convert to int
                        $insertData[$columnName] = intval($data[$columnName]);
                        break;
                    case 'varchar':
                        // convert to string
                        $insertData[$columnName] = (string)$data[$columnName];
                        break;
                }
            } else { // no value supplied, set to null
                $insertData[$columnName] = null;
            }
        }
        $this->data[] = $insertData;
    }

    public function select($where)
    {
        // if where was supplied, need to filter
        if (count($where) > 0) {
            $filteredData = array_filter($this->data,
                function ($row) use ($where) {
                    // check if row's key column had same value as the one in where clause
                    $key = key($where);
                    $value = $where[$key];
                    return $row[$key] == $value;
                });
        } else { // no where clause supplied, no need to filter
            $filteredData = $this->data;
        }

        // need to return array_values so that the resulting array has indexes in order from 0 to n as filter does not reindex
        return array_values($filteredData);
    }
}