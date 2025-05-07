<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php'; // This should already start the session

// Remove the duplicate session_start() since it's already in session.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ajout d'une fonction utilitaire pour récupérer les informations utilisateur
function getUserByEmail($pdo, $email) {
    $query = "SELECT id, username, mot_de_passe FROM users WHERE email = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mot_de_passe)) {
        $_SESSION['error_message'] = "Veuillez remplir tous les champs.";
        header('Location: login.php');
        exit;
    }

    try {
        $pdo = getDatabaseConnection();

        if (!$pdo) {
            throw new Exception("La connexion à la base de données a échoué.");
        }

        $user = getUserByEmail($pdo, $email);
        if (!$user) {
            $_SESSION['error_message'] = "Identifiants incorrects";
            header("Location: login.php");
            exit;
        }

        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'] ?? ''; // Handle case where username might be null
            
            header("Location: public/accueil.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Identifiants incorrects";
            header("Location: login.php");
            exit;
        }

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['error_message'] = "Une erreur est survenue. Veuillez réessayer.";
        header("Location: login.php");
        exit;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        $_SESSION['error_message'] = "Une erreur est survenue. Veuillez réessayer.";
        header("Location: login.php");
        exit;
    }

} else {
    header("Location: login.php");
    exit;
}
?>