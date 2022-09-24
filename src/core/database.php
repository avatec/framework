<?php
namespace Core;

class Database
{
    private $host = 'localhost';
    private $port = 3306;
    private $username = 'root';
    private $password = 'root';
    private $adaptor;
    private $instance;

    public function __construct( $adaptor )
    {
        $this->adaptor = $adaptor;
    }

    public function setHost( $host )
    {
        $this->host = $host;
        return $this;
    }

    public function setPort( int $port )
    {
        $this->port = $port;
        return $this;
    }

    public function setUsername( string $username )
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword( string $password )
    {
        $this->password = $password;
        return $this;
    }

    private function connect()
    {
        try {
            $this->instance = $this->adaptor->connect( $this->host, $this->port, $this->database, $this->username, $this->password );
        } catch( DatabaseConnectErrorException $e ) {
            throw new $e->getMessage();
        }
    }
}