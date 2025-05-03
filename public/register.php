<!DOCTYPE html>
<html lang="fr">/config/config.php';
<head>e_once '../src/User.php';
    <meta charset="UTF-8">
    <title>Test</title>ETHOD'] === 'POST') {
</head>ername = $_POST['username'];
<body> $_POST['password'];
    <h1>Test de la page</h1>
</body>(!empty($username) && !empty($password)) {
</html> $user = new User();
            header("Location: login.php");
            exit;
        } else {
            echo "Erreur lors de l'inscription.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Tokende</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 50%;
            max-width: 400px;
            margin: auto;
        }
        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #155db2;
        }
        .form-container p {
            text-align: center;
            margin-top: 15px;
        }
        .form-container a {
            color: #1877f2;
            text-decoration: none;
            font-weight: bold;
        }
        .form-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Tokende - Inscription</h1>
        <form method="POST" action="">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <br>
            <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous ici</a>.</p>
            <button type="submit">S'inscrire</button>
        </form>
    </div>
</body>
</html>
