<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pdo = getDatabaseConnection();

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Vérifier que les mots de passe correspondent
if ($new_password !== $confirm_password) {
    $_SESSION['error_message'] = "Les mots de passe ne correspondent pas";
    header("Location: profil.php");
    exit();
}

// Vérifier l'ancien mot de passe
$query = "SELECT password FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!password_verify($current_password, $user['password'])) {
    $_SESSION['error_message'] = "Mot de passe actuel incorrect";
    header("Location: profil.php");
    exit();
}

// Mettre à jour le mot de passe
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$query = "UPDATE users SET password = :password WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':password', $hashed_password);
$stmt->bindParam(':user_id', $user_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Mot de passe mis à jour avec succès";
} else {
    $_SESSION['error_message'] = "Erreur lors de la mise à jour du mot de passe";
}

header("Location: profil.php");
exit();