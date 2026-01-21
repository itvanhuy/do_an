<?php
// File: includes/database.php
require_once 'config.php';

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Thực thi query SELECT
    public function select($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return ['success' => true, 'data' => $stmt->fetchAll()];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Thực thi query INSERT, UPDATE, DELETE
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return ['success' => true, 'lastInsertId' => $this->connection->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Thực thi query và trả về một row
    public function selectOne($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return ['success' => true, 'data' => $stmt->fetch()];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Thực thi query và trả về result
    public function query($sql) {
        return $this->connection->query($sql);
    }

    // Chuẩn bị prepared statement
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    // Thực thi query trực tiếp (không có parameters)
    public function exec($sql) {
        return $this->connection->exec($sql);
    }
}