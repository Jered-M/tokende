<?php
require_once __DIR__ . '/database.php'; // Inclusion du fichier pour initialiser $pdo

// Initialisation de la connexion à la base de données
$pdo = getDatabaseConnection();
if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

// Configuration commune des sessions
// S'assurer que les cookies de session sont disponibles dans tout le site
$currentCookieParams = session_get_cookie_params();
session_set_cookie_params(
    $currentCookieParams["lifetime"],
    '/', // Le cookie est disponible dans tout le site
    $currentCookieParams["domain"],
    $currentCookieParams["secure"],
    $currentCookieParams["httponly"]
);

// Démarrer ou poursuivre la session
session_start();

// Ajout d'une fonction pour récupérer la photo de profil de l'utilisateur
function getProfilePicture($pdo, $user_id) {
    $query = "SELECT profile_picture FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['profile_picture']) && !empty($result['profile_picture'])
        ? '../uploads/profile_pictures/' . $result['profile_picture']
        : 'https://via.placeholder.com/150';
}

// Ajout d'une variable globale pour la photo de profil
if (isset($_SESSION['user_id'])) {
    $profile_picture = getProfilePicture($pdo, $_SESSION['user_id']);
} else {
    $profile_picture = 'https://via.placeholder.com/150';
}
?>