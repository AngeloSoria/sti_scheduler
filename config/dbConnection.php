<?php
class Database
{
    private $host;
    private $port;
    private $user;
    private $pass;
    private $db;
    private $conn;

    public function __construct()
    {
        $domainMode = strtolower($_ENV['DOMAIN_MODE'] ?? 'false') === 'true';

        if ($domainMode) {
            $this->host = $_ENV['DB_HOST'] ?? 'localhost';
            $this->port = $_ENV['DB_PORT'] ?? '';
            $this->user = $_ENV['DB_USER'] ?? 'root';
            $this->pass = $_ENV['DB_PASSWORD'] ?? '';
            $this->db = $_ENV['DB_NAME'] ?? 'scheduling_system_db';
        } else {
            $this->host = "localhost";
            $this->port = "";
            $this->user = "root";
            $this->pass = "";
            $this->db = "scheduling_system_db";
        }

        $dsn = "mysql:host={$this->host}";
        if (!empty($this->port)) {
            $dsn .= ";port={$this->port}";
        }
        $dsn .= ";dbname={$this->db};charset=utf8mb4";

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}