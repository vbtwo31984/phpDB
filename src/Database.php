<?php

namespace PhpDB;


use PhpDB\Exceptions\InvalidNameException;

class Database
{
    private $name;

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
}