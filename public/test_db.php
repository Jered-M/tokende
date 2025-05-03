<?php
require_once '../config/config.php';

try {
    $db = getDatabaseConnection();
    echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
