<?php

namespace Core;

use PDO;
use Exception;
use PDOException;

class DBPdo extends PDO
{
    private static ?self $instance = null;

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

    public function getList(string $table, string $where = "1", string $fields = "*"): array
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

    public function rowExists( string $query ): bool
    {
        $stmt = $this->prepare("SELECT EXISTS ({$query}) AS result");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['result'] == 1;
    }
}
