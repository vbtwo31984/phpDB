<?php

namespace PhpDB;


use PhpDB\Exceptions\DuplicateTableException;
use PhpDB\Exceptions\InvalidNameException;

class Database
{
    private $name;
    private $tables = [];

    public function __construct($name)
    {
        if(preg_match('/[^a-zA-Z0-9_]/', $name)) {
            throw new InvalidNameException("Name $name is invalid");
        }
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Table
     */
    public function getTable($name)
    {
        return $this->tables[$name];
    }

    public function addTable(Table $table)
    {
        if(array_key_exists($table->getName(), $this->tables)) {
            throw new DuplicateTableException("Table {$table->getName()} already exists");
        }
        $this->tables[$table->getName()] = $table;
    }

    public function getListOfTables()
    {
        return array_keys($this->tables);
    }
}