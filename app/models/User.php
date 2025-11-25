<?php
class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $name;
    public $email;
    public $password;

    public function __construct($db) { $this->conn = $db; }

    public function create() {
        $sql = "INSERT INTO {$this->table} (name, email, password, created_at)
                VALUES (:name, :email, :password, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $this->name,
            ':email' => $this->email,
            ':password' => $this->password,
        ]);
    }

    public function findByEmail($email) {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
}
