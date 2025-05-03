<?php
require_once __DIR__ . '/config/config.php'; // Utilisation d'un chemin absolu basé sur __DIR__

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $postnom = $_POST['postnom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $sexe = $_POST['sexe'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
    $role = $_POST['role']; // Nouveau champ pour le rôle (client ou chauffeur)

    try {
        $pdo = getDatabaseConnection();

        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $emailExists = $stmt->fetchColumn() > 0;

        if ($emailExists) {
            // Si l'email existe, afficher un message et un lien vers la page de connexion
            echo "Un compte avec cet email existe déjà. <a href='login.php'>Connectez-vous ici</a>.";
        } else {
            // Insérer les données dans la table `users`
            $stmt = $pdo->prepare("INSERT INTO users (nom, postnom, prenom, date_naissance, sexe, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $postnom, $prenom, $date_naissance, $sexe, $email, $mot_de_passe, $role]);

            // Redirection vers la page de connexion
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        die('Erreur lors de l\'enregistrement : ' . $e->getMessage());
    }
} else {
    echo "Méthode non autorisée.";
}
?>
