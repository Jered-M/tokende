<?php
require_once '../config/config.php';

class User {
    private $db;

    public function __construct() {
        $this->db = getDatabaseConnection();
    }

    public function register($username, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        return $stmt->execute(['username' => $username, 'password' => $hashedPassword]);
    }
}
?>
