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

// Ajout d'une fonction utilitaire pour récupérer les informations utilisateur
function getUserData($pdo, $user_id) {
    $query = "SELECT * FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Exemple d'utilisation dans le fichier
$user = getUserData($pdo, $user_id);
if (!$user) {
    header("Location: ../login.php");
    exit();
}

// Ajout d'une fonction pour récupérer ou définir la photo de profil
function getOrSetProfilePicture($pdo, $user_id, $uploadedFile = null) {
    if ($uploadedFile && $uploadedFile['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
        $fileName = uniqid() . '-' . basename($uploadedFile['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($uploadedFile['tmp_name'], $uploadFile)) {
            $query = "UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':profile_picture', $fileName, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            return '../uploads/profile_pictures/' . $fileName;
        }
    }

    $query = "SELECT profile_picture FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['profile_picture']) && !empty($result['profile_picture'])
        ? '../uploads/profile_pictures/' . $result['profile_picture']
        : 'https://via.placeholder.com/150';
}

// Utilisation de la fonction pour gérer la photo de profil
$profile_picture = getOrSetProfilePicture($pdo, $user_id, $_FILES['profile_picture'] ?? null);

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statut = $_POST['statut'] ?? '';
    $marqueVoiture = $_POST['marque_voiture'] ?? null;
    $couleurVoiture = $_POST['couleur_voiture'] ?? null;
    $plaqueVoiture = $_POST['plaque_voiture'] ?? null;

    if (!empty($statut)) {
        $sql = "UPDATE users SET statut = :statut";
        $params = [':statut' => $statut, ':user_id' => $_SESSION['user_id']];

        if ($statut === 'chauffeur') {
            $sql .= ", marque_voiture = :marque_voiture, couleur_voiture = :couleur_voiture, plaque_voiture = :plaque_voiture";
            $params[':marque_voiture'] = $marqueVoiture;
            $params[':couleur_voiture'] = $couleurVoiture;
            $params[':plaque_voiture'] = $plaqueVoiture;
        }

        $sql .= " WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $successMessage = "Votre profil a été mis à jour avec succès.";
    } else {
        $errorMessage = "Veuillez sélectionner un statut.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $description = $_POST['description'] ?? '';
    $uploadedFile = $_FILES['photo'];

    if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '-' . basename($uploadedFile['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($uploadedFile['tmp_name'], $uploadFile)) {
            $sql = "INSERT INTO publier (user_id, photo, description, created_at) VALUES (:user_id, :photo, :description, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':photo' => $fileName,
                ':description' => $description,
            ]);

            // Redirection après succès pour éviter la resoumission
            header("Location: profil.php?success=1");
            exit();
        } else {
            $errorMessage = "Erreur lors du téléchargement de la photo.";
        }
    } else {
        $errorMessage = "Veuillez sélectionner une photo valide.";
    }
}

// Vérifier si les colonnes nécessaires existent dans la table `users`
$columns = ['marque_voiture', 'couleur_voiture', 'plaque_voiture'];
foreach ($columns as $column) {
    $columnCheck = $pdo->query("SHOW COLUMNS FROM users LIKE '$column'")->fetch();
    if (!$columnCheck) {
        $pdo->exec("ALTER TABLE users ADD COLUMN $column VARCHAR(255) DEFAULT NULL");
    }
}

// Récupérer les informations actuelles de l'utilisateur
$sql = "SELECT statut, marque_voiture, couleur_voiture, plaque_voiture FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer le statut actuel de l'utilisateur
$sql = "SELECT statut FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$currentStatut = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Tokende</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
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
            padding-top: 20px;
            z-index: 1000;
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
        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
        }
        .profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-content {
            margin-left: 270px;
            margin-top: 80px;
            padding: 20px;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .widget-card {
            margin-bottom: 20px;
        }
        .text-bg-primary {
            background-color: #0062cc !important;
        }
        .badge {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        #profile-picture-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
    </style>
    <script>
        function toggleChauffeurForm() {
            const statut = document.getElementById('statut').value;
            const chauffeurForm = document.getElementById('chauffeur-form');
            chauffeurForm.style.display = (statut === 'chauffeur') ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="sidenav">
        <a href="#!" class="d-flex justify-content-center py-3">
            <img src="https://mdbootstrap.com/wp-content/uploads/2018/06/logo-mdb-jquery-small.png" alt="MDB Logo" height="25" />
        </a>
        <ul class="sidenav-menu">
            <li><a href="accueil.php"><i class="fas fa-home fa-fw me-3"></i>Accueil</a></li>
            <li><a href="#"><i class="fas fa-fire fa-fw me-3"></i>Tendances</a></li>
            <li><a href="#"><i class="fab fa-youtube-square fa-fw me-3"></i>Abonnements</a></li>
            <hr>
            <li><a href="#"><i class="fas fa-caret-square-right fa-fw me-3"></i>Bibliothèque</a></li>
            <li><a href="#"><i class="fas fa-history fa-fw me-3"></i>Historique</a></li>
            <li><a href="#"><i class="far fa-caret-square-right fa-fw me-3"></i>Vos vidéos</a></li>
            <li><a href="#"><i class="fas fa-clock fa-fw me-3"></i>Regarder plus tard</a></li>
            <li><a href="#"><i class="fas fa-thumbs-up fa-fw me-3"></i>Vidéos aimées</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt fa-fw me-3"></i>Se déconnecter</a></li>
        </ul>
    </div>

    <div class="navbar">
        <a href="profil.php" class="profile-icon">
            <img src="<?php echo $profile_picture; ?>" alt="Profil">
        </a>
    </div>

    <div class="profile-content">
        <section class="bg-light py-3 py-md-5 py-xl-8">
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6">
                        <h2 class="mb-4 display-5 text-center">Profil</h2>
                        <p class="text-secondary text-center lead fs-4 mb-5">Bienvenue sur votre page de profil. Vous pouvez gérer vos informations personnelles ici.</p>
                        <hr class="w-50 mx-auto mb-5 mb-xl-9 border-dark-subtle">
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row gy-4 gy-lg-0">
                    <div class="col-12 col-lg-4 col-xl-3">
                        <div class="row gy-4">
                            <div class="col-12">
                                <div class="card widget-card border-light shadow-sm">
                                    <div class="card-header text-bg-primary">Bienvenue, <?php echo htmlspecialchars($user['username'] ?? 'Utilisateur'); ?></div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <img src="<?php echo $profile_picture; ?>" class="img-fluid rounded-circle" id="sidebar-profile-picture" alt="Photo de profil">
                                        </div>
                                        <h5 class="text-center mb-1"><?php echo htmlspecialchars($user['username'] ?? 'Utilisateur'); ?></h5>
                                        <p class="text-center text-secondary mb-4"><?php echo htmlspecialchars($user['role'] ?? 'Membre'); ?></p>
                                        <ul class="list-group list-group-flush mb-4">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <h6 class="m-0">Abonnés</h6>
                                                <span>0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <h6 class="m-0">Abonnements</h6>
                                                <span>0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <h6 class="m-0">Amis</h6>
                                                <span>0</span>
                                            </li>
                                        </ul>
                                        <div class="d-grid m-0">
                                            <button class="btn btn-outline-primary" type="button" onclick="document.getElementById('profile-tab').click()">Modifier</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card widget-card border-light shadow-sm">
                                    <div class="card-header text-bg-primary">Réseaux sociaux</div>
                                    <div class="card-body">
                                        <a href="#!" class="d-inline-block bg-dark link-light lh-1 p-2 rounded">
                                            <i class="bi bi-youtube"></i>
                                        </a>
                                        <a href="#!" class="d-inline-block bg-dark link-light lh-1 p-2 rounded">
                                            <i class="bi bi-twitter-x"></i>
                                        </a>
                                        <a href="#!" class="d-inline-block bg-dark link-light lh-1 p-2 rounded">
                                            <i class="bi bi-facebook"></i>
                                        </a>
                                        <a href="#!" class="d-inline-block bg-dark link-light lh-1 p-2 rounded">
                                            <i class="bi bi-linkedin"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card widget-card border-light shadow-sm">
                                    <div class="card-header text-bg-primary">À propos</div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush mb-0">
                                            <li class="list-group-item">
                                                <h6 class="mb-1">
                                                    <span class="bi bi-person-fill me-2"></span>
                                                    Nom complet
                                                </h6>
                                                <span><?php echo htmlspecialchars($user['nom'] ?? 'Non spécifié'); ?></span>
                                            </li>
                                            <li class="list-group-item">
                                                <h6 class="mb-1">
                                                    <span class="bi bi-envelope-fill me-2"></span>
                                                    Email
                                                </h6>
                                                <span><?php echo htmlspecialchars($user['email'] ?? 'Non fourni'); ?></span>
                                            </li>
                                            <li class="list-group-item">
                                                <h6 class="mb-1">
                                                    <span class="bi bi-telephone-fill me-2"></span>
                                                    Téléphone
                                                </h6>
                                                <span><?php echo htmlspecialchars($user['telephone'] ?? 'Non spécifié'); ?></span> 
                                            </li>
                                            <li class="list-group-item">
                                                <h6 class="mb-1">
                                                    <span class="bi bi-geo-alt-fill me-2"></span>
                                                    Localisation
                                                </h6>
                                                <span><?php echo htmlspecialchars($user['location'] ?? 'Non spécifiée'); ?></span>
                                            </li>
                                            <li class="list-group-item">
                                                <h6 class="mb-1">
                                                    <span class="bi bi-calendar-event me-2"></span>
                                                    Date d'inscription
                                                </h6>
                                                <span><?php echo isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'Inconnue'; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card widget-card border-light shadow-sm">
                                    <div class="card-header text-bg-primary">Compétences</div>
                                    <div class="card-body">
                                        <span class="badge text-bg-primary">PHP</span>
                                        <span class="badge text-bg-primary">MySQL</span>
                                        <span class="badge text-bg-primary">HTML</span>
                                        <span class="badge text-bg-primary">CSS</span>
                                        <span class="badge text-bg-primary">JavaScript</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card widget-card border-light shadow-sm">
                                    <div class="card-header text-bg-primary">Publier une photo</div>
                                    <div class="card-body">
                                        <button class="btn btn-primary" onclick="togglePublicationForm()">Publier</button>
                                        <form id="publicationForm" action="profil.php" method="post" enctype="multipart/form-data" style="display: none; margin-top: 20px;">
                                            <div class="mb-3">
                                                <label for="photoUpload" class="form-label">Choisir une photo</label>
                                                <input type="file" class="form-control" id="photoUpload" name="photo" accept="image/*" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success">Publier</button>
                                            <button type="button" class="btn btn-secondary" onclick="togglePublicationForm()">Annuler</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function togglePublicationForm() {
                                    const form = document.getElementById('publicationForm');
                                    form.style.display = form.style.display === 'none' ? 'block' : 'none';
                                }
                            </script>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 col-xl-9">
                        <div class="card widget-card border-light shadow-sm">
                            <div class="card-body p-4">
                                <ul class="nav nav-tabs" id="profileTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-tab-pane" type="button" role="tab" aria-controls="overview-tab-pane" aria-selected="true">Aperçu</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Profil</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab" aria-controls="password-tab-pane" aria-selected="false">Mot de passe</button>
                                    </li>
                                </ul>
                                <div class="tab-content pt-4" id="profileTabContent">
                                    <div class="tab-pane fade show active" id="overview-tab-pane" role="tabpanel" aria-labelledby="overview-tab" tabindex="0">
                                        <h5 class="mb-3">À propos</h5>
                                        <p class="lead mb-3"><?php echo htmlspecialchars($user['bio'] ?? 'Aucune biographie disponible'); ?></p>
                                        <h5 class="mb-3">Profil</h5>
                                        <div class="row g-0">
                                            <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                <div class="p-2">Nom d'utilisateur</div>
                                            </div>
                                            <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                <div class="p-2"><?php echo htmlspecialchars($user['username'] ?? 'Non fourni'); ?></div>
                                            </div>
                                            <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                <div class="p-2">Nom complet</div>
                                            </div>
                                            <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                <div class="p-2"><?php echo htmlspecialchars($user['nom'] ?? 'Non spécifié'); ?></div>
                                            </div>
                                            <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                <div class="p-2">Email</div>
                                            </div>
                                            <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                <div class="p-2"><?php echo htmlspecialchars($user['email'] ?? 'Non fourni'); ?></div>
                                            </div>
                                            <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                <div class="p-2">Téléphone</div>
                                            </div>
                                            <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                <div class="p-2"><?php echo htmlspecialchars($user['numero'] ?? 'Non spécifié'); ?></div>
                                            </div>
                                            <div class="col-5 col-md-3 bg-light border-bottom border-white border-3"><div class="p-2">Localisation</div>
                                            </div>
                                            <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                <div class="p-2"><?php echo htmlspecialchars($user['location'] ?? 'Non spécifiée'); ?></div>
                                            </div>
                                            <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                <div class="p-2">Pays</div>
                                            </div>
                                            <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                <div class="p-2"><?php echo htmlspecialchars($user['country'] ?? 'Non spécifié'); ?></div>
                                            </div>
                                            <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                <div class="p-2">Rôle</div>
                                            </div>
                                            <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                <div class="p-2"><?php echo htmlspecialchars($user['role'] ?? 'Membre'); ?></div>
                                            </div>
                                            <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                <div class="p-2">Date d'inscription</div>
                                            </div>
                                            <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                <div class="p-2"><?php echo isset($user['created_at']) ? date('d/m/Y H:i', strtotime($user['created_at'])) : 'Inconnue'; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                        <form action="profil.php" method="post" enctype="multipart/form-data" class="row gy-3 gy-xxl-4">
                                            <div class="col-12">
                                                <div class="row gy-2">
                                                    <label class="col-12 form-label m-0">Image de profil</label>
                                                    <div class="col-12">
                                                        <img src="<?php echo $profile_picture; ?>" class="img-fluid rounded-circle" id="profile-picture-preview" alt="Photo de profil">
                                                    </div>
                                                    <div class="col-12">
                                                        <input type="file" id="profile-picture-input" name="profile_picture" accept="image/*" style="display: none;">
                                                        <button type="button" class="d-inline-block bg-primary link-light lh-1 p-2 rounded" onclick="document.getElementById('profile-picture-input').click()">
                                                            <i class="bi bi-upload"></i> Choisir une image
                                                        </button>
                                                        <button type="button" class="d-inline-block bg-danger link-light lh-1 p-2 rounded" id="remove-profile-picture">
                                                            <i class="bi bi-trash"></i> Supprimer
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="inputUsername" class="form-label">Nom d'utilisateur</label>
                                                <input type="text" class="form-control" id="inputUsername" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="inputNom" class="form-label">Nom complet</label>
                                                <input type="text" class="form-control" id="inputNom" name="nom" value="<?php echo htmlspecialchars($user['nom'] ?? ''); ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="inputEmail" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="inputEmail" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="inputNumero" class="form-label">Téléphone</label>
                                                <input type="tel" class="form-control" id="inputNumero" name="numero" value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="inputLocation" class="form-label">Localisation</label>
                                                <input type="text" class="form-control" id="inputLocation" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="inputCountry" class="form-label">Pays</label>
                                                <select class="form-select" id="inputCountry" name="country">
                                                    <option value="">Sélectionnez un pays</option>
                                                    <option value="France" <?php echo (isset($user['country']) && $user['country'] === 'France') ? 'selected' : ''; ?>>France</option>
                                                    <option value="Belgique" <?php echo (isset($user['country']) && $user['country'] === 'Belgique') ? 'selected' : ''; ?>>Belgique</option>
                                                    <option value="Suisse" <?php echo (isset($user['country']) && $user['country'] === 'Suisse') ? 'selected' : ''; ?>>Suisse</option>
                                                    <option value="Canada" <?php echo (isset($user['country']) && $user['country'] === 'Canada') ? 'selected' : ''; ?>>Canada</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label for="inputBio" class="form-label">Biographie</label>
                                                <textarea class="form-control" id="inputBio" name="bio" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="statut" class="form-label">Statut</label>
                                                <select class="form-select" id="statut" name="statut" onchange="toggleChauffeurForm()" required>
                                                    <option value="utilisateur" <?php echo $currentStatut === 'utilisateur' ? 'selected' : ''; ?>>Utilisateur</option>
                                                    <option value="chauffeur" <?php echo $currentStatut === 'chauffeur' ? 'selected' : ''; ?>>Chauffeur</option>
                                                </select>
                                            </div>
                                            <div id="chauffeur-form" style="display: <?php echo $userData['statut'] === 'chauffeur' ? 'block' : 'none'; ?>;">
                                                <div class="col-12 col-md-6">
                                                    <label for="marque_voiture" class="form-label">Marque de la voiture</label>
                                                    <input type="text" class="form-control" id="marque_voiture" name="marque_voiture" value="<?php echo htmlspecialchars($userData['marque_voiture'] ?? ''); ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="couleur_voiture" class="form-label">Couleur de la voiture</label>
                                                    <input type="text" class="form-control" id="couleur_voiture" name="couleur_voiture" value="<?php echo htmlspecialchars($userData['couleur_voiture'] ?? ''); ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="plaque_voiture" class="form-label">Plaque de la voiture</label>
                                                    <input type="text" class="form-control" id="plaque_voiture" name="plaque_voiture" value="<?php echo htmlspecialchars($userData['plaque_voiture'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                            </div>
                                        </form>
                                        <?php if (!empty($successMessage)): ?>
                                            <div class="alert alert-success mt-3"><?php echo htmlspecialchars($successMessage); ?></div>
                                        <?php elseif (!empty($errorMessage)): ?>
                                            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($errorMessage); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tab-pane fade" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                                        <form action="update_password.php" method="post">
                                            <div class="row gy-3 gy-xxl-4">
                                                <div class="col-12">
                                                    <label for="currentPassword" class="form-label">Mot de passe actuel</label>
                                                    <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                                </div>
                                                <div class="col-12">
                                                    <label for="newPassword" class="form-label">Nouveau mot de passe</label>
                                                    <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                                </div>
                                                <div class="col-12">
                                                    <label for="confirmPassword" class="form-label">Confirmer le nouveau mot de passe</label>
                                                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-12">
                                        <div class="card widget-card border-light shadow-sm">
                                            <div class="card-header text-bg-primary">Mes Publications</div>
                                            <div class="card-body">
                                                <?php
                                                $sql = "SELECT p.photo, p.description, p.created_at, u.username 
                                                        FROM publier p 
                                                        JOIN users u ON p.user_id = u.id 
                                                        WHERE p.user_id = :user_id 
                                                        ORDER BY p.created_at DESC";
                                                $stmt = $pdo->prepare($sql);
                                                $stmt->execute([':user_id' => $user_id]);
                                                $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                if ($publications): ?>
                                                    <div class="row">
                                                        <?php foreach ($publications as $publication): ?>
                                                            <div class="col-md-4 mb-4">
                                                                <div class="card">
                                                                    <img src="../uploads/photos/<?php echo htmlspecialchars($publication['photo']); ?>" class="card-img-top" alt="Photo">
                                                                    <div class="card-body">
                                                                        <h5 class="card-title">Publié par <?php echo htmlspecialchars($publication['username'] ?? 'Utilisateur inconnu', ENT_QUOTES, 'UTF-8'); ?></h5>
                                                                        <p class="card-text">Description : <?php echo htmlspecialchars($publication['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                                                        <p class="card-text"><small class="text-muted">Publié le <?php echo htmlspecialchars($publication['created_at']); ?></small></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <!-- Fin de la boucle foreach -->
                                                    </div>
                                                <?php else: ?>
                                                    <p class="text-center">Aucune publication trouvée.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('profile-picture-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const imageUrl = event.target.result;
                    document.getElementById('profile-picture-preview').src = imageUrl;
                    document.getElementById('sidebar-profile-picture').src = imageUrl;
                    document.querySelector('.profile-icon img').src = imageUrl;
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('remove-profile-picture').addEventListener('click', function() {
            const defaultImageUrl = 'https://via.placeholder.com/150';
            document.getElementById('profile-picture-preview').src = defaultImageUrl;
            document.getElementById('sidebar-profile-picture').src = defaultImageUrl;
            document.querySelector('.profile-icon img').src = defaultImageUrl;
            document.getElementById('profile-picture-input').value = '';

            // Vous pourriez ajouter ici une requête AJAX pour supprimer la photo de profil du serveur et de la base de données
            // ou simplement laisser le champ vide et gérer la suppression lors de la soumission du formulaire principal.
        });
    </script>
</body>
</html>