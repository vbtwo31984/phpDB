<?php

namespace PhpDB;


use PhpDB\Exceptions\DuplicateDatabaseException;
use PhpDB\Exceptions\DuplicateTableException;
use PhpDB\Exceptions\InvalidNameException;
use PhpDB\Exceptions\ParseException;

class PhpDB
{
    private $inputStream;
    private $outputStream;
    /** @var DataStore */
    private $dataStore;
    /** @var Database */
    private $activeDatabase;

    /**
     * PhpDB constructor.
     * @param resource $inputStream input stream to use for reading input, usually STDIN
     * @param resource $outputStream output stream to use for writing output, usually STDOUT
     */
    function __construct($inputStream = STDIN, $outputStream = STDOUT)
    {
        $this->inputStream = $inputStream;
        $this->outputStream = $outputStream;
        $this->dataStore = new DataStore();
    }

    public function run()
    {
        fwrite($this->outputStream, "Welcome to PhpDB. Enter a command or 'quit' to exit\n");
        // loop reading a line, processing the command, writing output
        while (true) {
            fwrite($this->outputStream, '> ');
            $command = strtolower(trim(fgets($this->inputStream, 1024)));

            if ($command === 'quit') {
                fwrite($this->outputStream, "Bye\n");
                break;
            }

            $message = $this->processCommand($command);
            fwrite($this->outputStream, "$message\n");
        }
    }

    /**
     * @param string $command
     * @return string
     *
     * Choose which command to run, return output
     */
    public function processCommand($command)
    {
        $createDbCommand = 'create database ';
        if (starts_with($command, $createDbCommand)) {
            return $this->createDatabase(trim_leading_string($command, $createDbCommand));
        }
        if ($command == 'list databases') {
            return $this->listDatabases();
        }
        $useDbCommand = 'use database ';
        if (starts_with($command, $useDbCommand)) {
            return $this->useDatabase(trim_leading_string($command, $useDbCommand));
        }
        if ($command == 'list tables') {
            return $this->listTables();
        }
        if (starts_with($command, 'create table ')) {
            return $this->createTable($command);
        }
        if (starts_with($command, 'select * from ')) {
            return $this->select($command);
        }
        if (starts_with($command, 'insert into ')) {
            return $this->insert($command);
        }

        return 'Unknown command';
    }

    private function createDatabase($dbName)
    {
        try {
            $db = new Database($dbName);
            $this->dataStore->addDatabase($db);
            return "Database $dbName created";
        } catch (DuplicateDatabaseException|InvalidNameException $e) {
            return $e->getMessage();
        }
    }

    private function listDatabases()
    {
        $databases = $this->dataStore->getListOfDatabases();
        if (count($databases) === 0) {
            return 'No databases';
        }

        return implode(', ', $databases);
    }

    private function useDatabase($name)
    {
        $database = $this->dataStore->getDatabase($name);
        if ($database != null) {
            $this->activeDatabase = $database;
            return "Active database: $name";
        }
        return "Unknown database $name";
    }

    private function createTable($command)
    {
        if ($this->activeDatabase == null) {
            return 'No active database';
        }
        try {
            $table = SyntaxParser::parseCreateTable($command);
            $this->activeDatabase->addTable($table);
            return "Table {$table->getName()} created";
        } catch (DuplicateTableException|ParseException|InvalidNameException $e) {
            return $e->getMessage();
        }
    }

    private function listTables()
    {
        if ($this->activeDatabase == null) {
            return 'No active database';
        }
        $tableNames = $this->activeDatabase->getListOfTables();

        // combine list with commas
        if (count($tableNames) > 0) {
            return implode(', ', $tableNames);
        }

        return "No tables in database {$this->activeDatabase->getName()}";
    }

    private function select($command)
    {
        if ($this->activeDatabase == null) {
            return 'No active database';
        }

        try {
            // parsedTable contains table name as key, and where clause as associative array of column => value
            $parsedTable = SyntaxParser::parseSelect($command);

            $tableName = key($parsedTable);
            $table = $this->activeDatabase->getTable($tableName);
            if ($table == null) {
                return "Table $tableName does not exist";
            }

            $whereClause = $parsedTable[$tableName];

            $data = $table->select($whereClause);
            if (count($data) > 0) {
                // output column names
                $result = implode(', ', array_keys($data[0]));

                // output data rows
                foreach ($data as $row) {
                    // if data is a string, wrap with single quotes
                    $stringifiedRow = array_map(function ($item) {
                        if (is_string($item)) {
                            $item = "'$item'";
                        }
                        return $item;
                    }, $row);

                    // output row, joined with commas
                    $result .= "\n" . implode(', ', $stringifiedRow);
                }
                return $result;
            }

            return 'No rows';
        } catch (ParseException $e) {
            return $e->getMessage();
        }
    }

    private function insert($command)
    {
        if ($this->activeDatabase == null) {
            return 'No active database';
        }

        try {
            // parsedCommand contains table name as key and associative array of $column => data
            $parsedCommand = SyntaxParser::parseInsert($command);

            $tableName = key($parsedCommand);
            $table = $this->activeDatabase->getTable($tableName);
            if ($table == null) {
                return "Table $tableName does not exist";
            }

            $table->insert($parsedCommand[$tableName]);
            return 'Row inserted successfully';
        } catch (ParseException $e) {
            return $e->getMessage();
        }
    }
}