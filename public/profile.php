<?php
require_once '../config/config.php';

// Simuler un utilisateur connecté (remplacez par une vraie gestion de session)
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $pdo = getDatabaseConnection();

    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT nom, postnom, prenom, date_naissance, sexe, email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        die("Utilisateur non trouvé.");
    }
} catch (PDOException $e) {
    die('Erreur lors de la récupération des données : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .profile-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .profile-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-container p {
            margin: 10px 0;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Profil</h1>
        <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
        <p><strong>Postnom :</strong> <?= htmlspecialchars($user['postnom']) ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
        <p><strong>Date de naissance :</strong> <?= htmlspecialchars($user['date_naissance']) ?></p>
        <p><strong>Sexe :</strong> <?= htmlspecialchars($user['sexe']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
    </div>
</body>
</html>
