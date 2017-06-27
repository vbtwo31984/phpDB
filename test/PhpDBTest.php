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

    public function testListDatabasesReturnsEmptyStringWhenNoDatabases()
    {
        $result = $this->db->processCommand('list databases');
        $this->assertEquals('', $result);
    }

    public function testListDatabasesReturnsDatabaseNameAfterAddingDatabase()
    {
        $result = $this->db->processCommand('create database test');
        $this->assertEquals('Database test created', $result);
        $result = $this->db->processCommand('list databases');
        $this->assertEquals('test', $result);
    }
}
