<?php
class Database {
    private static $instance = null;
    private $conn;

    private $servername = "localhost";
    private $username = "root"; 
    private $password = "12345"; 
    private $dbname = "BankDB";

    private function __construct() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->servername . ";dbname=" . $this->dbname, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
