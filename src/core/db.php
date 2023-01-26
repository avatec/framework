<?php
namespace Core;

use mysqli;
use Core\Database\Exceptions\DatabaseConnectErrorException;
use Exception;

/**
 *	Klasa obsługuje połączenie z MySQL w systemie CMS
 *  @author Grzegorz Miśkiewicz <biuro@avatec.pl>
 *  @version 2.0
 *  @copyright Avatec.pl
 */

class Db
{
    private static $instance;
    public static $debug;

    public function __construct()
    {
        self::call();
    }

    public function __destruct()
    {
        self::$instance->close();
    }

    public static function call( $data = null )
    {
        if (!isset(self::$instance)) {
            if(!empty( $data )) {
                $config = $data;
            } else {
                global $config;
            }
            self::$instance = new MySQLi($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name'], (!empty($config['db_port']) ? (int) $config['db_port'] : 3306));
            if (self::$instance->connect_error) {
                throw new DatabaseConnectErrorException();
            }

            self::$instance->set_charset("utf8mb4");
            self::$instance->query("SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
            self::$instance->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
    }

    // @deprecated since 1.2 - please use getErrorMessage instead
    public static function error()
    {
        self::call();

        return self::$instance->error;
    }

    public static function getErrorMessage(): string
    {
        self::call();
        return self::$instance->error;
    }

    public static function getErrors(): array
    {
        self::call();
        return self::$instance->error_list;
    }

    public static function real_escape($string)
    {
        self::call();

        return self::$instance->real_escape_string($string);
    }

    public static function count($t, $w = null)
    {
        self::call();
        if (is_null($w)) {
            $result = self::$instance->query("SELECT * FROM " . $t);
        } else {
            $result = self::$instance->query("SELECT * FROM " . $t . " WHERE " . $w);
        }

        if (!empty($result)) {
            return $result->num_rows;
        } else {
            return 0;
        }
    }

    public static function query($query)
    {
        self::call();

        $result = self::$instance->query($query);
        if (!empty(self::$instance->error)) {
            throw new Exception('Error MySQL: ' . self::$instance->error);
        }

        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }

        if (isset($array)) {
            return $array;
        }
    }

    public static function query_row($query)
    {
        self::call();

        $result = self::$instance->query($query);
        if (!empty(self::$instance->error)) {
            throw new Exception('Error MySQL: ' . self::$instance->error);
        }

        if (!empty($result)) {
            while ($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
        }
        if (isset($array[0])) {
            return $array[0];
        }
    }

    public static function last_id($table, $column = "id")
    {
        self::call();

        $result = self::row("*", $table, "ORDER BY ".$column." DESC");
        return $result[$column];
    }

    public static function run($query)
    {
        self::call();

        $result = self::$instance->query($query);
        if (!empty(self::$instance->error)) {
            throw new Exception('Error MySQL: ' . self::$instance->error);
        }

        if (!empty($result->num_rows)) {
            return true;
        }

        return false;
    }

    public static function update($t, $q, $w)
    {
        self::call();

        $query = "UPDATE " . $t . " SET " . $q . " WHERE ".$w;
        if (self::$instance->query($query) === true) {
            return true;
        } 
        
        throw new Exception('Error MySQL: ' . $query . ' => ' . self::$instance->error);
    }

    public static function exec($e = "*", $t = null, $w = null)
    {
        $result = self::query("SELECT " . $e . " FROM " . $t . " " . $w);
        return $result;
    }

    public static function row($e = "*", $t = null, $w = null)
    {
        $query = "SELECT " . $e . " FROM " . $t . " " . $w;

        self::call();

        $result = self::$instance->query($query);
        if (!empty(self::$instance->error)) {
            throw new Exception('Error MySQL: ' . self::$instance->error);
        }

        $result = $result->fetch_assoc();
        return $result;
    }

/**
 * Funkcja zwaraca kolejny numer dla kolejności
 * @param string $table
 * @param string $conditions (optional)
 * @return int
 */
    public static function getLastPriority( string $table, $conditions = null ): int
    {
        $query = "SELECT priority FROM " . $table . 
            (!empty( $conditions ) ? " WHERE " . $conditions : "") . " ORDER BY priority DESC LIMIT 0,1";

        $result  = self::$instance->query( $query );
        if (!empty(self::$instance->error)) {
            throw new Exception('Error MySQL: ' . $query . " - returns: " . self::$instance->error);
        }

        $result = $result->fetch_assoc();
        if(!empty( $result['priority'] )) {
            $result['priority']++;
            return $result['priority'];
        }

        return 1;
    }

    public static function check($t, $w, $silent = false)
    {
        self::call();
        $query = "SELECT * FROM " . $t . " WHERE " . $w;

        $result = mysqli_query(self::$instance, $query);
        if (!empty($result) && $result->num_rows > 0) {
            return true;
        } 

        return false;
    }

    public static function insert($t, $v)
    {
        self::call();

        $query = "INSERT INTO " . $t . " VALUES(" . $v . ");";

        $result = mysqli_query(self::$instance, $query);
        return $result;
    }

/**
 * Zwraca ostatnio utworzony ID
 * @return int
 */
    public static function insert_id()
    {
        self::call();

        return !empty( self::$instance->insert_id ) ? self::$instance->insert_id : false;
    }

    public static function save($t, $v, $w)
    {
        self::call();

        $query = "UPDATE  " . $t . " SET " . $v . " WHERE " . $w . ";";

        $result = mysqli_query(self::$instance, $query);
        return $result;
    }

    public static function delete($t, $w)
    {
        self::call();

        $query = "DELETE FROM  " . $t . " WHERE " . $w . ";";
        $result = mysqli_query(self::$instance, $query);

        /* $query = "OPTIMIZE TABLE " . $t;
        mysqli_query(self::$instance, $query); */

        return $result;
    }

    public static function has_table($t)
    {
        global $config;

        self::call();

        $query = "SHOW TABLES FROM " . $config['db_name'] . " LIKE '" . $t . "';";
        $result = self::$instance->query( $query );
        if (!empty(self::$instance->error)) {
            throw new Exception('Error MySQL: ' . self::$instance->error . '. Query that returned error: ' . $query);
        }

        if (!empty($result->num_rows)) {
            return true;
        }

        return false;
    }

    public static function install($sql_array)
    {
        if ((!empty($sql_array)) && (is_array($sql_array))) {
            foreach ($sql_array as $sql) {
                self::run($sql);
            }
        } else {
            if (!empty($sql_array)) {
                self::run($sql_array);
            }
        }

        return true;
    }

    public static function begin()
    {
        self::call();
        self::$instance->begin_transaction();
        return self::$instance;
    }

    private $transactions = [];
    public static function addTransaction( string $query, array $data, $instance )
    {   
        try {
            $stmt = $instance->prepare( $query );
            foreach ($data as i) {
                $stmt->bind_param($i['type'], $i['value']);
            }
            $stmt->execute();
        } catch (\mysqli_sql_exception $exception) {
            throw $exception;
        }

        return $instance;
    }

    public static function commit($instance)
    {
        try {
            $instance->commit();
        } catch( \mysqli_sql_exception $exception ) {
            throw $exception;
        }
    }

    public static function close()
    {
        self::$instance->close();
    }
}
