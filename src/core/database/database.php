<?php namespace Core\Database;
/**
 *  @package    Avatec Framework
 *  @author     Grzegorz Miskiewicz
 *  @copyright  Copyright (c) 2009-2022 Avatec (https://www.avatec.pl)
 *  @license    MIT
 *  @link       https://www.avatec.pl
 */

/**
 *  Database class
 */

class Database
{
    private $adaptor;

    public function __construct()
    {
        global $config;

        $class = '\\Core\\Database\\Drivers\\' . $config['db_engine'];

        if( class_exists( $class )) {
            $this->adaptor = new $class(
                $config['db_host'],
                $config['db_user'],
                $config['db_pass'],
                $config['db_name'],
                $config['db_port']
            );
        } else {
            throw new \Exception('Error: Could not load database adaptor ' . $config['db_engine'] . '!');
        }
    }

    public function query( $sql )
    {
        return $this->adaptor->query( $sql );
    }

    public function insert( $t, $v )
    {
        return $this->adaptor->query(
            "INSERT INTO {$t} VALUES({$v})"
        );
    }

    public function update( $t, $v, $c )
    {
        return $this->adaptor->query(
            "UPDATE TABLE {$t} SET {$v} WHERE {$c}"
        );
    }

    public function delete( $t, $c )
    {
        return $this->adaptor->query(
            "DELETE FROM {$t} WHERE {$c}"
        );
    }

    public function escape( $value )
    {
        $this->adaptor->escape( $value );
    }

    public function affected()
    {
        return $this->adaptor->countAffected();
    }

    public function last_id()
    {
        return $this->adaptor->getLastId();
    }

    public function connected()
    {
        return $this->adaptor->connected();
    }
}
