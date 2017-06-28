<?php

namespace PhpDB;

use PhpDB\Exceptions\DuplicateDatabaseException;

class DataStore
{
    private $databases = [];

    public function getListOfDatabases()
    {
        return array_keys($this->databases);
    }

    public function addDatabase(Database $database)
    {
        if(array_key_exists($database->getName(), $this->databases)) {
            throw new DuplicateDatabaseException("Database {$database->getName()} already exists");
        }
        $this->databases[$database->getName()] = $database;
    }

    public function getDatabase($name)
    {
        return $this->databases[$name];
    }
}