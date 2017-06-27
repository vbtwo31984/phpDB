<?php

namespace PhpDB;


class PhpDB
{
    private $inputStream;
    private $outputStream;

    function __construct($inputStream, $outputStream)
    {
        $this->inputStream = $inputStream;
        $this->outputStream = $outputStream;
    }

    public function run() {
        fwrite($this->outputStream, "Welcome to PhpDB. Enter a command or 'quit' to exit\n");
        while(true) {
            fwrite($this->outputStream, '> ');
            $command = strtolower(trim(fgets($this->inputStream,1024)));

            if($command == 'quit') {
                fwrite($this->outputStream, "Bye\n");
                break;
            }
        }
    }
}