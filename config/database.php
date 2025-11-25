<?php
class Database {
    private $host = 'db5019071793.hosting-data.io';
    private $db   = 'dbs15000644'; // Nombre de tu base de datos
    private $user = 'dbu1824788'; // Cambia por tu usuario real
    private $pass = 'Gw@o9JD62Qi5b'; // Cambia por tu contraseña real
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
        return $this->conn;
    }
}
