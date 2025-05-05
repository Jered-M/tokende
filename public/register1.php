<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $postnom = $_POST['postnom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $sexe = $_POST['sexe'];
    $date_naissance = $_POST['date_naissance'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);

    // Connexion à la base de données
    require_once '../config/database.php'; // Utilisez un fichier de configuration partagé

    try {
        // Vérifiez si l'email existe déjà
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email"; // Mise à jour pour utiliser la table `users`
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            echo "<p style='color: red; text-align: center;'>Ce compte ou cet email existe déjà.</p>";
        } else {
            // Insérer les données dans la table `users`
            $sql = "INSERT INTO users (nom, postnom, prenom, email, telephone, sexe, date_naissance, mot_de_passe) 
                    VALUES (:nom, :postnom, :prenom, :email, :telephone, :sexe, :date_naissance, :mot_de_passe)";
            $stmt = $pdo->prepare($sql);

            // Exécuter la requête
            $stmt->execute([
                ':nom' => $nom,
                ':postnom' => $postnom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':sexe' => $sexe,
                ':date_naissance' => $date_naissance,
                ':mot_de_passe' => $mot_de_passe,
            ]);

            echo "Données insérées avec succès.<br>";

            // Redirection vers la page de connexion après inscription réussie
            header("Location: /login.php");
            exit;
        }
    } catch (PDOException $e) {
        // Affichez l'erreur pour le diagnostic
        echo "Erreur : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, rgba(57,49,175,1) 0%, rgba(0,198,255,1) 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            display: flex;
            width: 80%;
            max-width: 1000px;
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .register-left {
            background: rgba(57,49,175,1);
            color: #fff;
            width: 40%;
            text-align: center;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .register-left img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .register-right {
            width: 60%;
            padding: 40px;
        }
        .register-right h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #495057;
        }
        .register-right label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .register-right input, .register-right select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .register-right button {
            width: 100%;
            padding: 10px;
            background: #0062cc;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .register-right button:hover {
            background: #004a99;
        }
        .register-right p {
            text-align: center;
            margin-top: 15px;
        }
        .register-right a {
            color: #0062cc;
            text-decoration: none;
            font-weight: bold;
        }
        .register-right a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Vidéo à gauche -->
        <div class="register-left">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                <img src="styles/images/inscription1.jpg" alt="Inscription" style="width: 80%; border-radius: 10px; margin-bottom: 20px;">
                <p style="font-size: 1.2rem; font-weight: bold;">Découvrez nos services de transport rapide et fiable.</p>
            </div>
        </div>
        <!-- Formulaire à droite -->
        <div class="register-right">
            <h1>Inscription</h1>
            <form action="" method="post">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="postnom">Postnom :</label>
                <input type="text" id="postnom" name="postnom" required>

                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required>

                <label for="email">Adresse e-mail :</label>
                <input type="email" id="email" name="email" required>

                <label for="telephone">Numéro de téléphone :</label>
                <input type="tel" id="telephone" name="telephone" required>

                <label for="sexe">Sexe :</label>
                <select id="sexe" name="sexe" required>
                    <option value="">Sélectionnez votre sexe</option>
                    <option value="Homme">Homme</option>
                    <option value="Femme">Femme</option>
                    <option value="Autre">Autre</option>
                </select>

                <label for="date_naissance">Date de naissance :</label>
                <input type="date" id="date_naissance" name="date_naissance" required>

                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>

                <label for="confirmation">Confirmation :</label>
                <input type="password" id="confirmation" name="confirmation" required>

                <button type="submit">S'inscrire</button>
                <p>Vous avez déjà un compte ? <a href="/login.php">Connectez-vous ici</a>.</p>
                <p>Vous êtes nouveau ? <a href="/public/register1.php">Inscrivez-vous ici</a>.</p>
                <p>Ou connectez-vous avec :</p>
                <a href="/public/google_login.php" style="display: inline-block; background: #4285F4; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold;">
                    <img src="styles/images/google_logo.png" alt="Google" style="width: 20px; vertical-align: middle; margin-right: 10px;">
                    Continuer avec Google
                </a>
            </form>
        </div>
    </div>
</body>
</html>
