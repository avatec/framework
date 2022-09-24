<?php
namespace Core\Database\Interfaces;

interface AdapterInterface
{
    public function connect( string $host, int $port, string $database, string $user, string $pass );
    public function setQuery( string $query );
    public function prepare();
    public function execute();
    public function getRows();
    public function getRow();
    public function lastInsertId();
}