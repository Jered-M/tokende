<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .profile-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background-color: #1877f2;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }
        .profile-icon img {
            width: 30px;
            height: 30px;
        }
    </style>
</head>
<body>
    <h1>Bienvenue sur la page d'accueil</h1>
    <p>Vous êtes connecté avec succès.</p>

    <!-- Icône de profil -->
    <a href="public/profile.php" class="profile-icon">
        <img src="https://via.placeholder.com/30" alt="Profil">
    </a>
</body>
</html>
