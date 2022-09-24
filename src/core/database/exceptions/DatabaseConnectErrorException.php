<?php
namespace Exception;

class DatabaseConnectErrorException extends Exception
{
    public function errorMessage()
    {
        return 'Database connection error. Could not connect to database server on ' . $this->getMessage();
    }
}