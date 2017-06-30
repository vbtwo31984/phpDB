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
     * @expectedExceptionMessage No columns defined
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
        $string = 'create table test (id-id int)';
        SyntaxParser::parseCreateTable($string);
    }

    /**
     * @expectedException \PhpDB\Exceptions\InvalidTableDefinitionException
     * @expectedExceptionMessage Column id has no data type
     */
    public function testParseNoDataTypeReturnsError()
    {
        $string = 'create table test (id)';
        SyntaxParser::parseCreateTable($string);
    }

    /**
     * @expectedException \PhpDB\Exceptions\ParseException
     * @expectedExceptionMessage Create table syntax invalid
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
        $this->assertCount(2, $table->getColumnDefinitions());
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
        $this->assertCount(2, $table->getColumnDefinitions());
        $this->assertArrayHasKey('id', $table->getColumnDefinitions());
        $this->assertEquals('int', $table->getColumnDefinitions()['id']);
        $this->assertArrayHasKey('name', $table->getColumnDefinitions());
        $this->assertEquals('varchar', $table->getColumnDefinitions()['name']);
    }

    public function testParseSelectReturnsTableName()
    {
        $string = 'select * from table1';
        $result = SyntaxParser::parseSelect($string);
        $this->assertArrayHasKey('table1', $result);
    }

    public function testParseSelectReturnsWhereClauseColumnAndValue()
    {
        $string = "select * from table1 where name = 'John'";
        $result = SyntaxParser::parseSelect($string);
        $whereClause = $result['table1'];
        $this->assertArrayHasKey('name',$whereClause);
        $this->assertContains('John', $whereClause);
    }

    /**
     * @expectedException \PhpDB\Exceptions\ParseException
     * @expectedExceptionMessage Select syntax invalid
     */
    public function testParseSelectEmptyWhereReturnsError()
    {
        $string = "select * from table1 where";
        SyntaxParser::parseSelect($string);
    }

    /**
     * @expectedException \PhpDB\Exceptions\ParseException
     * @expectedExceptionMessage Select syntax invalid
     */
    public function testParseSelectInvalidWhereReturnsError()
    {
        $string = "select * from table1 where id 123";
        SyntaxParser::parseSelect($string);
    }

    public function testParseInsertReturnsTableName()
    {
        $string = 'insert into table1 (id) values (1)';
        $result = SyntaxParser::parseInsert($string);
        $this->assertArrayHasKey('table1', $result);
    }

    public function testParseInsertReturnsColumnValues()
    {
        $string = "insert into table1 (id, name) values (1, 'Appleseed, John')";
        $result = SyntaxParser::parseInsert($string);
        $columnValues = $result['table1'];
        $this->assertCount(2, $columnValues);
        $this->assertArrayHasKey('id', $columnValues);
        $this->assertEquals(1, $columnValues['id']);
        $this->assertArrayHasKey('name', $columnValues);
        $this->assertEquals('Appleseed, John', $columnValues['name']);
    }

    /**
     * @expectedException \PhpDB\Exceptions\ParseException
     * @expectedExceptionMessage Number of columns and values does not match
     */
    public function testParseInsertWithDifferentNumberOfColumnsAndValuesThrowsError()
    {
        $string = 'insert into table1 (id, name) values (1)';
        SyntaxParser::parseInsert($string);
    }

    /**
     * @expectedException \PhpDB\Exceptions\ParseException
     * @expectedExceptionMessage Insert syntax invalid
     */
    public function testParseInsertWithNoValuesThrowsError()
    {
        $string = 'insert into table1 (id)';
        SyntaxParser::parseInsert($string);
    }
}
