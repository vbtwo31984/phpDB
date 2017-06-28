<?php

use PhpDB\PhpDB;
use PHPUnit\Framework\TestCase;

class PhpDBTest extends TestCase
{
    private $inputStream;
    private $outputStream;
    /** @var PhpDB */
    private $db;

    protected function setUp()
    {
        parent::setUp();

        // The input stream used to send commands to the app
        $this->inputStream = fopen('php://memory', 'w');
        // The output stream is used to read output from the app
        $this->outputStream = fopen('php://memory', 'w');

        $this->db = new PhpDB($this->inputStream, $this->outputStream);
    }

    public function testCanRunAndQuitDB()
    {
        fwrite($this->inputStream, "quit\n");
        rewind($this->inputStream);
        $this->db->run();
        rewind($this->outputStream);
        $line = stream_get_line($this->outputStream, 1024);
        $this->assertEquals("Welcome to PhpDB. Enter a command or 'quit' to exit\n> Bye\n", $line);
    }

    public function testUnknownCommandReturnsError()
    {
        $result = $this->db->processCommand('Testing');
        $this->assertEquals('Unknown command', $result);
    }

    public function testListDatabasesReturnsMessageWhenNoDatabases()
    {
        $result = $this->db->processCommand('list databases');
        $this->assertEquals('No databases', $result);
    }

    public function testListDatabasesReturnsDatabaseNameAfterAddingDatabase()
    {
        $result = $this->db->processCommand('create database test');
        $this->assertEquals('Database test created', $result);
        $result = $this->db->processCommand('list databases');
        $this->assertEquals('test', $result);
    }

    public function testListDatabasesReturnsDatabaseNamesAfterAddingMultipleDatabases()
    {
        $result = $this->db->processCommand('create database test');
        $this->assertEquals('Database test created', $result);
        $result = $this->db->processCommand('create database test2');
        $this->assertEquals('Database test2 created', $result);
        $result = $this->db->processCommand('list databases');
        $this->assertEquals('test, test2', $result);
    }

    public function testCreatingDuplicateDatabasesReturnsError()
    {
        $this->db->processCommand('create database test');
        $result = $this->db->processCommand('create database test');

        $this->assertEquals('Database test already exists', $result);
    }

    public function testCreatingDatabaseWithInvalidNameReturnsError()
    {
        $result = $this->db->processCommand('create database test one');
        $this->assertEquals('Name test one is invalid', $result);
    }
}
