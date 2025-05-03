<?php
require_once __DIR__ . '/config/config.php'; // Inclure la configuration de la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    try {
        $pdo = getDatabaseConnection();

        // Vérifier si l'utilisateur existe
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // Connexion réussie, redirection vers la page d'accueil
            header("Location: accueil.php");
            exit;
        } else {
            // Afficher un message d'erreur si les informations sont incorrectes
            echo "Email ou mot de passe incorrect. <a href='login.php'>Réessayez</a>.";
        }
    } catch (PDOException $e) {
        die('Erreur lors de la connexion : ' . $e->getMessage());
    }
} else {
    echo "Méthode non autorisée.";
}
?>
