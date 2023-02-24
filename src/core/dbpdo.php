<?php

namespace Core;

use PDO;
use Exception;
use PDOException;

class DBPdo extends PDO
{
    private static ?self $instance = null;
    private array $batch = [];

    public function __construct(
        string $host,
        int $port,
        string $user,
        string $pass,
        string $name,
        array $options = []
    ) {
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $options = array_merge($defaultOptions, $options);

        parent::__construct($dsn, $user, $pass, $options);
    }

    public static function initialize(string $host, int $port, string $user, string $pass, string $name): void
    {
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        try {
            self::$instance = new DBPdo($dsn, $user, $pass);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new Exception("Failed to connect to database: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (!self::$instance) {
            throw new Exception("DB connection has not been initialized");
        }

        return self::$instance;
    }

    public function getRows(string $table, string $where = "1", string $fields = "*"): array
    {
        $stmt = $this->prepare("SELECT {$fields} FROM {$table} WHERE {$where}");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getRow(string $table, string $where = "1", string $fields = "*"): ?array
    {
        $stmt = $this->prepare("SELECT {$fields} FROM {$table} WHERE {$where} LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function delete(string $table, string $where, string $value): bool
    {
        $stmt = $this->prepare("DELETE FROM {$table} WHERE {$where} = ?");
        $result = $stmt->execute([$value]);

        return $result;
    }

    /**
     * Example use of insertWithPrepare
     * @param string $table
     * @param array $data
     * @return bool
     * 
     * @example $db = DBPdo::getInstance();
     *          $table = "my_table";
     *          $data = [
     *              "column1" => "value1",
     *              "column2" => "value2",
     *              "column3" => "value3"
     *          ];
     *          $result = $db->insertWithPrepare($table, $data);
     */
    public function insertWithPrepare(string $table, array $data): bool
    {
        $keys = array_keys($data);
        $fields = implode(", ", $keys);
        $placeholders = implode(", :", $keys);

        $stmt = $this->prepare("INSERT INTO {$table} ({$fields}) VALUES (:{$placeholders})");

        $result = $stmt->execute($data);

        return $result;
    }

    public function rowExists(string $query): bool
    {
        $stmt = $this->prepare("SELECT EXISTS ({$query}) AS result");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['result'] == 1;
    }

    public function addBatch(string $query): void
    {
        $this->batch[] = $query;
    }

    public function executeBatch(): bool
    {
        if (empty($this->batch)) {
            return false;
        }

        try {
            $this->beginTransaction();

            foreach ($this->batch as $query) {
                $stmt = $this->prepare($query);
                $stmt->execute();
            }

            $this->commit();

            $this->batch = [];

            return true;
        } catch (PDOException $e) {
            $this->rollBack();
            $this->batch = [];
            throw new Exception("Batch execution failed: " . $e->getMessage());
        }
    }

    public function getLastInsertId(): int
    {
        return $this->lastInsertId();
    }

    /**
     * Creating table in database
     * @param string $table
     * @param array $columns
     * @return bool
     * 
     * @example 
     * $table = "users";
     * $columns = [
     *      ["name" => "id", "type" => "INT UNSIGNED AUTO_INCREMENT PRIMARY KEY"],
     *      ["name" => "username", "type" => "VARCHAR(255) NOT NULL"],
     *      ["name" => "email", "type" => "VARCHAR(255) NOT NULL"],
     *      ["name" => "password", "type" => "VARCHAR(255) NOT NULL"],
     * ];
     */
    public function createTable(string $table, array $columns): bool
    {
        // Check if table already exists
        $stmt = $this->prepare("SHOW TABLES LIKE :table");
        $stmt->bindParam(":table", $table, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_NUM);

        if ($result) {
            // Table already exists, return false
            return false;
        }

        // Table doesn't exist, create it
        $sql = "CREATE TABLE `{$table}` (";
        foreach ($columns as $column) {
            $sql .= "`{$column['name']}` {$column['type']}, ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= ")";

        try {
            $this->beginTransaction();
            $this->exec($sql);
            $this->commit();
            return true;
        } catch (PDOException $e) {
            $this->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function getRowCount(string $table, string $where = "1"): int
    {
        $stmt = $this->prepare("SELECT COUNT(*) FROM {$table} WHERE {$where}");
        $stmt->execute();
        $result = $stmt->fetchColumn();

        return $result;
    }
}
