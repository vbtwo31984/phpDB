<?php

namespace PhpDB;

use PhpDB\Exceptions\DuplicateDatabaseException;

/**
 * Stores databases
 * @package PhpDB
 */
class DataStore
{
    private $databases = [];

    /**
     * @return string[] An array of database names
     */
    public function getListOfDatabases()
    {
        return array_keys($this->databases);
    }

    /**
     * @param Database $database
     * @throws DuplicateDatabaseException if the name is a duplicate
     */
    public function addDatabase(Database $database)
    {
        // check for duplicates
        if (array_key_exists($database->getName(), $this->databases)) {
            throw new DuplicateDatabaseException("Database {$database->getName()} already exists");
        }
        $this->databases[$database->getName()] = $database;
    }

    /**
     * @param string $name
     * @return Database If not found, returns null
     */
    public function getDatabase($name)
    {
        return $this->databases[$name];
    }
}