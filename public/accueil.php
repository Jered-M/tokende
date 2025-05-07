<?php
// Inclure la configuration de session
require_once __DIR__ . '/../config/session.php';
// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/database.php'; // Inclure la configuration de la base de données

// Utilisation de la variable globale $profile_picture pour afficher la photo de profil
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, rgba(57,49,175,1) 0%, rgba(0,198,255,1) 100%);
            color: #fff;
        }
        .sidenav {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding-top: 20px;
        }
        .sidenav a {
            color: #fff;
            display: block;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidenav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            text-align: center;
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.9);
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 20px;
            z-index: 1000;
        }
        .navbar .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
        }
        .navbar .profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Sidenav -->
    <div class="sidenav">
        <a href="#!" class="d-flex justify-content-center py-3">
            <img id="MDB-logo" src="https://mdbootstrap.com/wp-content/uploads/2018/06/logo-mdb-jquery-small.png"
                alt="MDB Logo" draggable="false" height="25" />
        </a>
        <ul class="sidenav-menu">
            <li><a href="#"><i class="fas fa-home fa-fw me-3"></i>Accueil</a></li>
            <li><a href="reservation.php"><i class="fas fa-calendar-check fa-fw me-3"></i>reservation</a></li>
            <li><a href="messages.php"><i class="fas fa-envelope fa-fw me-3"></i>Messages</a></li>
            <li><a href="recherche.php"><i class="fas fa-search fa-fw me-3"></i>Recherche</a></li>
            <hr>
            <li><a href="#"><i class="fas fa-caret-square-right fa-fw me-3"></i>Bibliothèque</a></li>
            <li><a href="#"><i class="fas fa-history fa-fw me-3"></i>Historique</a></li>
            <li><a href="#"><i class="far fa-caret-square-right fa-fw me-3"></i>Vos vidéos</a></li>
            <li><a href="#"><i class="fas fa-clock fa-fw me-3"></i>Regarder plus tard</a></li>
            <li><a href="#"><i class="fas fa-thumbs-up fa-fw me-3"></i>Vidéos aimées</a></li>
        </ul>
    </div>
     <!-- Navbar -->
     <div class="navbar">
        <!-- Redirige vers le profil lorsqu'on clique sur l'icône -->
        <a href="profil.php" class="profile-icon" id="profileLink">
            <img src="<?php echo $profile_picture; ?>" alt="Photo de profil">
        </a>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Afficher des informations de débogage de session (à retirer en production)
        if (isset($_SESSION['user_id'])) {
            echo "<p>Utilisateur connecté: ID = " . $_SESSION['user_id'] . "</p>";
            echo "<p>Si vous cliquez sur l'icône de profil et êtes redirigé vers la page de connexion, c'est que la session n'est pas correctement maintenue.</p>";
        } else {
            echo "<p>Erreur: Vous ne devriez pas voir cette page sans être connecté.</p>";
        }
        ?>
    </div>

    <!-- Ajouter un script JavaScript pour déboguer le clic sur le profil -->
    <script>
    document.getElementById('profileLink').addEventListener('click', function(e) {
        console.log('Clic sur le lien de profil');
        // Utiliser localStorage pour stocker un message de débogage
        localStorage.setItem('profileClicked', 'true');
        localStorage.setItem('profileClickTime', new Date().toString());
    });
    </script>
</body>
</html>
