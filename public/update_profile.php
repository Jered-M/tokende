<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pdo = getDatabaseConnection();

if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $username = $_POST['username'] ?? null;
    $nom = $_POST['nom'] ?? null;
    $email = $_POST['email'] ?? null;
    $numero = $_POST['numero'] ?? null;
    $location = $_POST['location'] ?? null;
    $country = $_POST['country'] ?? null;
    $bio = $_POST['bio'] ?? null;
    $profilePicturePath = null;
    $deleteProfilePicture = isset($_POST['delete_profile_picture']); // Vérifier si la suppression de la photo est demandée

    // Gestion de l'upload de la photo de profil
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = basename($_FILES['profile_picture']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = array("jpg", "jpeg", "png");
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($fileExt, $allowedExts)) {
            $_SESSION['error'] = "Type de fichier non autorisé. Seuls JPG, JPEG et PNG sont acceptés.";
            header("Location: profil.php");
            exit();
        }

        if ($_FILES['profile_picture']['size'] > $maxFileSize) {
            $_SESSION['error'] = "La taille de l'image ne doit pas dépasser 2MB.";
            header("Location: profil.php");
            exit();
        }

        // Créer le répertoire d'upload si nécessaire
        $uploadDir = __DIR__ . '/../../uploads/profile_pictures/';
        if (!file_exists($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            $_SESSION['error'] = "Erreur lors de la création du répertoire d'upload.";
            header("Location: profil.php");
            exit();
        }

        // Générer un nom de fichier unique
        $newFileName = md5(uniqid()) . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $uploadPath)) {
            $_SESSION['error'] = "Erreur lors de l'upload de l'image.";
            header("Location: profil.php");
            exit();
        }

        $profilePicturePath = '/uploads/profile_pictures/' . $newFileName;
    }

    // Déterminer le nom de la colonne username
    try {
        $checkUsernameColumnQuery = "SHOW COLUMNS FROM users LIKE 'username'";
        $checkUsernameColumnStmt = $pdo->query($checkUsernameColumnQuery);

        if ($checkUsernameColumnStmt->fetch(PDO::FETCH_ASSOC)) {
            $usernameColumn = 'username';
        } else {
            $checkNomUtilisateurColumnQuery = "SHOW COLUMNS FROM users LIKE 'nom_utilisateur'";
            $checkNomUtilisateurColumnStmt = $pdo->query($checkNomUtilisateurColumnQuery);

            if ($checkNomUtilisateurColumnStmt->fetch(PDO::FETCH_ASSOC)) {
                $usernameColumn = 'nom_utilisateur';
            } else {
                $_SESSION['error'] = "Erreur: La colonne du nom d'utilisateur est introuvable dans la base de données.";
                header("Location: profil.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données: " . $e->getMessage();
        header("Location: profil.php");
        exit();
    }

    // Préparer la requête de mise à jour
    $query = "UPDATE users SET $usernameColumn = :username, nom = :nom, email = :email,
              telephone = :numero, location = :location, country = :country, bio = :bio";

    if ($profilePicturePath !== null) {
        $query .= ", profile_picture = :profile_picture";
    } elseif ($deleteProfilePicture) {
        $query .= ", profile_picture = NULL"; // Définir la colonne profile_picture à NULL pour supprimer la photo
    }

    $query .= " WHERE id = :user_id";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($profilePicturePath !== null) {
            $stmt->bindParam(':profile_picture', $profilePicturePath);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profil mis à jour avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour du profil.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données: " . $e->getMessage();
    }

    header("Location: profil.php");
    exit();
} else {
    // Si la requête n'est pas POST, rediriger
    header("Location: profil.php");
    exit();
}
?>