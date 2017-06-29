<?php

namespace PhpDB;


use PhpDB\Exceptions\InvalidNameException;
use PhpDB\Exceptions\InvalidTableDefinitionException;
use PhpDB\Exceptions\UnsupportedTypeException;

class Table
{
    private $name;
    private $columnDefinitions;
    private $supportedTypes = ['int', 'varchar'];
    public function __construct($name, $columnDefinitions)
    {
        if(preg_match('/[^a-zA-Z0-9_]/', $name)) {
            throw new InvalidNameException("Name $name is invalid");
        }
        if(!is_array($columnDefinitions) || count($columnDefinitions) === 0) {
            throw new InvalidTableDefinitionException('No columns defined');
        }
        $this->checkForInvalidColumnNames($columnDefinitions);
        $this->checkForUnsupportedTypes($columnDefinitions);
        $this->name = $name;
        $this->columnDefinitions = $columnDefinitions;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getColumnDefinitions()
    {
        return $this->columnDefinitions;
    }

    private function checkForUnsupportedTypes($columnDefinitions)
    {
        foreach($columnDefinitions as $type) {
            if(!in_array($type, $this->supportedTypes)) {
                throw new UnsupportedTypeException("Type $type is not supported");
            }
        }
    }

    private function checkForInvalidColumnNames($columnDefinitions)
    {
        foreach($columnDefinitions as $name=>$type) {
            if(preg_match('/[^a-zA-Z0-9_]/', $name)) {
                throw new InvalidNameException("Column name $name is invalid");
            }
        }
    }
}