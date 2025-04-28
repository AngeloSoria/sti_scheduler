<?php

class DbConnection {
    private $db;
    private $host;
    private $port;
    private $user;
    private $password;
    private $database;
    private $charset;
    private $charset_collate;
    private $dsn;
    private $options;
    private $connection;
    private $error;

    public function __construct($host, $port, $user, $password, $database, $charset = 'utf8mb4', $charset_collate = 'utf8mb4_general_ci') {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->charset = $charset;
        $this->charset_collate = $charset_collate;
        $this->dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset={$this->charset}";
        $this->options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }
    public function connect() {
        try {
            $this->connection = new PDO($this->dsn, $this->user, $this->password, $this->options);
            $this->connection->exec("SET NAMES {$this->charset} COLLATE {$this->charset_collate}");
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            throw new Exception("Database connection error: " . $this->error);
        }
    }
}