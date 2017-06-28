<?php
use PhpDB\SyntaxParser;
use PHPUnit\Framework\TestCase;

class SyntaxParserTest extends TestCase
{
    /**
     * @expectedException \PhpDB\Exceptions\InvalidNameException
     * @expectedExceptionMessage Name test test is invalid
     */
    public function testParseInvalidNameReturnsError()
    {
        $string = 'create table test test (id int)';
        SyntaxParser::parseCreateTable($string);
    }

    /**
     * @expectedException \PhpDB\Exceptions\InvalidTableDefinitionException
     * @expectedExceptionMessage No column definitions
     */
    public function testParseNoColumnDefinitionsReturnsError()
    {
        $string = 'create table test';
        SyntaxParser::parseCreateTable($string);
    }

    /**
     * @expectedException \PhpDB\Exceptions\UnsupportedTypeException
     * @expectedExceptionMessage Type double is not supported
     */
    public function testParseUnsupportedDataTypeReturnsError()
    {
        $string = 'create table test (cost double)';
        SyntaxParser::parseCreateTable($string);
    }

    /**
     * @expectedException \PhpDB\Exceptions\InvalidNameException
     * @expectedExceptionMessage Column name id-id is invalid
     */
    public function testParseInvalidColumnNameReturnsError()
    {
        $string = 'create table test test (id-id int)';
        SyntaxParser::parseCreateTable($string);
    }

    /**
     * @expectedException \PhpDB\Exceptions\InvalidTableDefinitionException
     * @expectedExceptionMessage Column id has no data type
     */
    public function testParseNoDataTypeReturnsError()
    {
        $string = 'create table test test (id)';
        SyntaxParser::parseCreateTable($string);
    }

    /**
     * @expectedException \PhpDB\Exceptions\ParseException
     * @expectedExceptionMessage Invalid create table syntax
     */
    public function testInvalidSyntaxReturnError()
    {
        $string = 'create table test (id int) (name double)';
        SyntaxParser::parseCreateTable($string);
    }

    public function testParseValidCreateSyntaxReturnsTable()
    {
        $string = 'create table test (id int, name varchar)';
        $table = SyntaxParser::parseCreateTable($string);
        $this->assertEquals('test', $table->getName());
        $this->assertCount(2, $table->getColumnDefinitions);
        $this->assertArrayHasKey('id', $table->getColumnDefinitions());
        $this->assertEquals('int', $table->getColumnDefinitions()['id']);
        $this->assertArrayHasKey('name', $table->getColumnDefinitions());
        $this->assertEquals('varchar', $table->getColumnDefinitions()['name']);
    }

    public function testParseValidCreateSyntaxWithoutSpacesReturnsTable()
    {
        $string = 'create table test(id int,name varchar)';
        $table = SyntaxParser::parseCreateTable($string);
        $this->assertEquals('test', $table->getName());
        $this->assertCount(2, $table->getColumnDefinitions);
        $this->assertArrayHasKey('id', $table->getColumnDefinitions());
        $this->assertEquals('int', $table->getColumnDefinitions()['id']);
        $this->assertArrayHasKey('name', $table->getColumnDefinitions());
        $this->assertEquals('varchar', $table->getColumnDefinitions()['name']);
    }
}
