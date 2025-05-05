<?php
// Inclure la configuration de session
require_once __DIR__ . '/config/session.php';
// Vérification si un message d'erreur est présent dans la session
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
            margin: 0;
        }
        .register-left p {
            font-size: 1.2rem;
            margin-top: 20px;
            color: #00ffcc;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
            font-style: italic;
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
        .register-right input {
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
        .error {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-left">
            <img src="connexion.gif" alt="Transport">
            <p>Rejoignez notre plateforme pour un transport rapide, sûr et fiable.</p>
        </div>
        <div class="register-right">
            <h1>Connexion</h1>
            <?php if ($error_message): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form action="process_login.php" method="post">
                <label for="email">Adresse e-mail :</label>
                <input type="email" id="email" name="email" required>

                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>

                <button type="submit">Se connecter</button>
                <p>Si vous n'avez pas de compte, <a href="public/register1.php">inscrivez-vous ici</a>.</p>
            </form>
        </div>
    </div>
</body>
</html>
