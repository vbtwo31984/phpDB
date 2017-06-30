<?php

namespace PhpDB;


use PhpDB\Exceptions\DuplicateTableException;
use PhpDB\Exceptions\InvalidNameException;

/**
 * A representation of a database, stores tables
 * @package PhpDB
 */
class Database
{
    private $name;
    private $tables = [];

    public function __construct($name)
    {
        // name can only contain letters, numbers, and underscore
        if (preg_match('/[^a-zA-Z0-9_]/', $name)) {
            throw new InvalidNameException("Name $name is invalid");
        }
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Table if table does not exist, returns null
     */
    public function getTable($name)
    {
        return $this->tables[$name];
    }

    /**
     * @param Table $table
     * @throws DuplicateTableException if table name already exists in this database
     */
    public function addTable(Table $table)
    {
        // check for duplicates
        if (array_key_exists($table->getName(), $this->tables)) {
            throw new DuplicateTableException("Table {$table->getName()} already exists");
        }
        $this->tables[$table->getName()] = $table;
    }

    /**
     * @return string[] An array of table names
     */
    public function getListOfTables()
    {
        return array_keys($this->tables);
    }
}