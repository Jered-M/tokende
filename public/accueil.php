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

$pdo = getDatabaseConnection();

// Récupérer la photo de profil de l'utilisateur connecté
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Définir une photo de profil par défaut si aucune n'est trouvée
$profile_picture = $user && !empty($user['profile_picture']) 
    ? '../uploads/profile_pictures/' . htmlspecialchars($user['profile_picture']) 
    : '../uploads/profile_pictures/default.png';

// Récupérer les publications
$sql = "SELECT p.id, p.photo, p.description, p.created_at, u.username 
        FROM publier p 
        JOIN users u ON p.user_id = u.id 
        ORDER BY p.created_at DESC LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Utilisation de la variable globale $profile_picture pour afficher la photo de profil
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Tokende</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <li><a href="acceuil.php"><i class="fas fa-home fa-fw me-3"></i>Accueil</a></li>
            <li><a href="reservation.php"><i class="fas fa-calendar-check fa-fw me-3"></i>reservation</a></li>
            <li><a href="messages.php"><i class="fas fa-envelope fa-fw me-3"></i>Messages</a></li>
            <li><a href="recherche.php"><i class="fas fa-search fa-fw me-3"></i>Recherche</a></li>
            <hr>
            <li><a href="localisation.php"><i class="fas fa-map-marker-alt fa-fw me-3"></i>Localisation</a></li>
            <li><a href="#"><i class="fas fa-history fa-fw me-3"></i>Historique</a></li>
            <li><a href="#"><i class="far fa-caret-square-right fa-fw me-3"></i>Vos vidéos</a></li>
            <li><a href="#"><i class="fas fa-clock fa-fw me-3"></i>Regarder plus tard</a></li>
            <li><a href="#"><i class="fas fa-thumbs-up fa-fw me-3"></i>Vidéos aimées</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt fa-fw me-3"></i>Se déconnecter</a></li>
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
        // Suppression de l'affichage du message pour l'utilisateur connecté avec ID:1
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1) {
            // Ne rien afficher pour cet utilisateur
        } else {
            echo "<p>Utilisateur connecté: ID = " . $_SESSION['user_id'] . "</p>";
            echo "<p>Si vous cliquez sur l'icône de profil et êtes redirigé vers la page de connexion, c'est que la session n'est pas correctement maintenue.</p>";
        }
        ?>
    </div>

    <div class="main-content">
    <div class="container d-flex flex-column align-items-center mt-5" style="max-width: 600px;">
        <h2 class="text-white mb-4">Publications récentes</h2>

        <?php if ($publications): ?>
            <?php foreach ($publications as $publication): ?>
                <?php
                // Récupérer le nombre de likes pour chaque publication
                $likeStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE publication_id = :publication_id");
                $likeStmt->execute([':publication_id' => $publication['id']]);
                $likeCount = $likeStmt->fetchColumn();

                // Récupérer le nombre de commentaires pour chaque publication
                $commentStmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE publication_id = :publication_id");
                $commentStmt->execute([':publication_id' => $publication['id']]);
                $commentCount = $commentStmt->fetchColumn();
                ?>
                <div class="card w-100 mb-4 shadow-sm bg-white text-dark rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="../uploads/profile_pictures/<?php echo htmlspecialchars($publication['username'] ?? 'default.png'); ?>" 
                                 alt="Profil" class="rounded-circle me-3" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($publication['username'] ?? 'Utilisateur inconnu'); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($publication['created_at'] ?? 'Date inconnue'); ?></small>
                            </div>
                        </div>

                        <p class="mb-2"><?php echo htmlspecialchars($publication['description'] ?? 'Aucune description'); ?></p>

                        <?php if (!empty($publication['photo'])): ?>
                            <img src="../uploads/photos/<?php echo htmlspecialchars($publication['photo']); ?>" 
                                 class="img-fluid rounded mb-3" alt="Photo">
                        <?php endif; ?>

                        <div class="d-flex justify-content-between pt-2 border-top">
                            <button class="btn btn-sm btn-outline-secondary like-btn" data-id="<?php echo $publication['id']; ?>">
                                <i class="fas fa-thumbs-up me-1"></i> J'aime (<span class="like-count"><?php echo $likeCount; ?></span>)
                            </button>
                            <button class="btn btn-sm btn-outline-secondary comment-btn" data-id="<?php echo $publication['id']; ?>">
                                <i class="fas fa-comment me-1"></i> Commenter (<span class="comment-count"><?php echo $commentCount; ?></span>)
                            </button>
                            <button class="btn btn-sm btn-outline-secondary share-btn" data-id="<?php echo $publication['id']; ?>">
                                <i class="fas fa-share me-1"></i> Partager
                            </button>
                        </div>

                        <div class="comments-section mt-3" data-id="<?php echo $publication['id']; ?>" style="display: none;">
                            <div class="comments-list">
                                <!-- Les commentaires seront chargés ici via AJAX -->
                            </div>
                            <div class="input-group mt-2">
                                <input type="text" class="form-control comment-input" placeholder="Ajouter un commentaire...">
                                <button class="btn btn-primary add-comment-btn" data-id="<?php echo $publication['id']; ?>">Envoyer</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-light text-center w-100">Aucune publication disponible.</div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Liker une publication
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function () {
                const publicationId = this.getAttribute('data-id');
                const likeCountSpan = this.querySelector('.like-count');
                fetch('like.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ publication_id: publicationId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        likeCountSpan.textContent = data.like_count;
                    } else {
                        alert(data.message);
                    }
                });
            });
        });

        // Afficher le champ de commentaire lorsqu'on clique sur "Commenter"
        document.querySelectorAll('.comment-btn').forEach(button => {
            button.addEventListener('click', function () {
                const commentsSection = this.closest('.card').querySelector('.comments-section');
                commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';

                // Charger les commentaires via AJAX
                const publicationId = this.getAttribute('data-id');
                const commentsList = commentsSection.querySelector('.comments-list');

                fetch(`comment.php?publication_id=${publicationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            commentsList.innerHTML = ''; // Réinitialiser la liste des commentaires
                            data.comments.forEach(comment => {
                                const commentElement = document.createElement('div');
                                commentElement.classList.add('mb-2');
                                commentElement.innerHTML = `
                                    <strong>${comment.username}</strong> <small class="text-muted">${comment.created_at}</small>
                                    <p>${comment.comment}</p>
                                `;
                                commentsList.appendChild(commentElement);
                            });
                        } else {
                            alert(data.message);
                        }
                    });
            });
        });

        // Ajouter un commentaire
        document.querySelectorAll('.add-comment-btn').forEach(button => {
            button.addEventListener('click', function () {
                const publicationId = this.getAttribute('data-id');
                const input = this.closest('.comments-section').querySelector('.comment-input');
                const commentText = input.value.trim();

                if (commentText) {
                    fetch('comment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ publication_id: publicationId, comment: commentText })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const commentsList = this.closest('.comments-section').querySelector('.comments-list');
                            const commentCountSpan = this.closest('.card').querySelector('.comment-count');
                            commentsList.innerHTML = ''; // Réinitialiser la liste des commentaires
                            data.comments.forEach(comment => {
                                const commentElement = document.createElement('div');
                                commentElement.classList.add('mb-2');
                                commentElement.innerHTML = `
                                    <strong>${comment.username}</strong> <small class="text-muted">${comment.created_at}</small>
                                    <p>${comment.comment}</p>
                                `;
                                commentsList.appendChild(commentElement);
                            });
                            input.value = ''; // Réinitialiser le champ de saisie
                            commentCountSpan.textContent = data.comments.length; // Mettre à jour le nombre de commentaires
                        } else {
                            alert(data.message);
                        }
                    });
                }
            });
        });
    });

    // Ajouter un script JavaScript pour déboguer le clic sur le profil
    document.getElementById('profileLink').addEventListener('click', function(e) {
        console.log('Clic sur le lien de profil');
        // Utiliser localStorage pour stocker un message de débogage
        localStorage.setItem('profileClicked', 'true');
        localStorage.setItem('profileClickTime', new Date().toString());
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
