<?php
function getDatabaseConnection() {
    $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=site_likes;charset=utf8mb4';
    $username = 'root';
    $password = '';

    try {
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        return null; // Tu peux aussi gérer ou logger l'erreur ici
    }
}

function createMessagesTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        recipient_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);
}

$pdo = getDatabaseConnection();
if ($pdo) {
    createMessagesTable($pdo);
}
