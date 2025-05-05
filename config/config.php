<?php
// Remplacez les valeurs ci-dessous par vos informations de connexion correctes
define('DB_HOST', '127.0.0.1'); // Adresse du serveur MySQL
define('DB_PORT', '3306');      // Port MySQL par défaut
define('DB_NAME', 'votre_base_de_donnees'); // Remplacez par le nom exact de votre base de données
define('DB_USER', 'votre_utilisateur'); // Nom d'utilisateur MySQL
define('DB_PASS', 'votre_mot_de_passe'); // Mot de passe MySQL

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
