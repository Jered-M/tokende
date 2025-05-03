<?php
// Exemple de configuration (ne pas inclure les vraies informations sensibles)
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'example_database');
define('DB_USER', 'example_user');
define('DB_PASS', 'example_password');

/**
 * Fonction de connexion à la base de données avec PDO
 *
 * @return PDO
 */
function getDatabaseConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,         // Active les exceptions en cas d'erreur
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    // Mode de récupération par défaut
            PDO::ATTR_EMULATE_PREPARES => false,                 // Pour utiliser les vraies requêtes préparées
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Affiche l'erreur et arrête l'exécution
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }
}
?>
