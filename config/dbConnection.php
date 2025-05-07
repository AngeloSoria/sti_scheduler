<?php

require_once __DIR__ . '../../vendor/autoload.php';

use Dotenv\Dotenv;

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
        $dotenv = Dotenv::createImmutable(__DIR__ . '../../');
        $dotenv->load();

        $domainMode = strtolower($_ENV['DOMAIN_MODE']) == 'true';

        if ($domainMode) {
            $this->host = $_ENV['DB_HOST'];
            $this->port = $_ENV['DB_PORT'];
            $this->user = $_ENV['DB_USER'];
            $this->pass = $_ENV['DB_PASSWORD'];
            $this->db = $_ENV['DB_NAME'];
        } else {
            $this->host = "localhost";
            $this->port = "3306";
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
