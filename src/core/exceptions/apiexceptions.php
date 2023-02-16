<?php namespace Core\Exceptions;

class ApiExceptions extends Exception
{
    // Exception code, message and data properties
    private $code;
    private $message;
    private $data;

    public function __construct($message = "", $code = 0, $data = null) {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
        parent::__construct($message, $code);
    }

    public function getCode() {
        return $this->code;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getData() {
        return $this->data;
    }
}