<?php

namespace PhpDB;


use PhpDB\Exceptions\DuplicateDatabaseException;
use PhpDB\Exceptions\InvalidNameException;

class PhpDB
{
    private $inputStream;
    private $outputStream;
    /** @var DataStore */
    private $dataStore;

    function __construct($inputStream, $outputStream)
    {
        $this->inputStream = $inputStream;
        $this->outputStream = $outputStream;
        $this->dataStore = new DataStore();
    }

    public function run() {
        fwrite($this->outputStream, "Welcome to PhpDB. Enter a command or 'quit' to exit\n");
        while(true) {
            fwrite($this->outputStream, '> ');
            $command = strtolower(trim(fgets($this->inputStream,1024)));

            if($command === 'quit') {
                fwrite($this->outputStream, "Bye\n");
                break;
            }

            $message = $this->processCommand($command);
            fwrite($this->outputStream, "$message\n");
        }
    }

    public function processCommand($command)
    {
        $command = strtolower(trim($command));

        $createDbCommand = 'create database ';
        if(starts_with($command, $createDbCommand)) {
            return $this->createDatabase(trim_leading_string($command, $createDbCommand));
        }
        if($command == 'list databases') {
            return $this->listDatabases();
        }

        return 'Unknown command';
    }

    private function createDatabase($dbName)
    {
        try {
            $db = new Database($dbName);
            $this->dataStore->addDatabase($db);
            return "Database $dbName created";
        }
        catch (DuplicateDatabaseException|InvalidNameException $e) {
            return $e->getMessage();
        }
    }

    private function listDatabases()
    {
        $databases = $this->dataStore->getListOfDatabases();
        if(count($databases) === 0) {
            return 'No databases';
        }

        return implode(', ', $databases);
    }
}