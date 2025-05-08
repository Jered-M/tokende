<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$pdo = getDatabaseConnection();

// Ajouter les colonnes si elles n'existent pas
$columns = [
    'nom' => "VARCHAR(100) DEFAULT NULL",
    'marque_voiture' => "VARCHAR(100) DEFAULT NULL",
    'plaque_immatriculation' => "VARCHAR(50) DEFAULT NULL",
    'couleur_voiture' => "VARCHAR(50) DEFAULT NULL",
    'profile_picture' => "VARCHAR(255) DEFAULT NULL",
];

foreach ($columns as $column => $definition) {
    $check = $pdo->query("SHOW COLUMNS FROM users LIKE '$column'")->fetch();
    if (!$check) {
        $pdo->exec("ALTER TABLE users ADD COLUMN $column $definition");
    }
}

// Récupération des chauffeurs pour la liste déroulante
$sql = "SELECT id, nom FROM users WHERE statut = 'chauffeur'";
$stmt = $pdo->query($sql);
$chauffeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des chauffeurs
$sql = "SELECT id, nom, marque_voiture, plaque_immatriculation, couleur_voiture, profile_picture 
        FROM users WHERE statut = 'chauffeur'";
$stmt = $pdo->query($sql);
$chauffeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chauffeurId = $_POST['chauffeur_id'] ?? '';
    $depart = $_POST['depart'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $reservationDate = $_POST['reservation_date'] ?? '';
    $reservationTime = $_POST['reservation_time'] ?? '';

    if (!empty($chauffeurId) && !empty($depart) && !empty($destination) && !empty($reservationDate) && !empty($reservationTime)) {
        try {
            $sql = "INSERT INTO reservation (user_id, depart, destination, date_reservation, heure_reservation) 
                    VALUES (:user_id, :depart, :destination, :date_reservation, :heure_reservation)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $chauffeurId,
                ':depart' => $depart,
                ':destination' => $destination,
                ':date_reservation' => $reservationDate,
                ':heure_reservation' => $reservationTime,
            ]);

            // Envoyer un message au chauffeur
            $message = "Vous avez été réservé pour une course le $reservationDate à $reservationTime.";
            $sqlMessage = "INSERT INTO message (user_id, recipient_id, message, created_at) 
                           VALUES (:user_id, :recipient_id, :message, CURRENT_TIMESTAMP)";
            $stmtMessage = $pdo->prepare($sqlMessage);
            $stmtMessage->execute([
                ':user_id' => $_SESSION['user_id'], // L'utilisateur qui réserve
                ':recipient_id' => $chauffeurId,   // Le chauffeur réservé
                ':message' => $message,
            ]);

            // Redirection après succès pour éviter la resoumission
            header("Location: reservation.php?success=1");
            exit();
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de la réservation : " . $e->getMessage();
        }
    } else {
        $errorMessage = "Veuillez remplir tous les champs.";
    }
}

// Afficher un message de succès si redirigé après une réservation réussie
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Votre réservation a été effectuée avec succès.";
}

// Récupération des réservations
$sql = "SELECT r.id, r.depart, r.destination, r.date_reservation, r.heure_reservation, u.username AS client_name 
        FROM reservation r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Chauffeurs disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            margin-top: 20px;
            background: #eee;
        }
        .single_advisor_profile {
            position: relative;
            margin-bottom: 50px;
            transition-duration: 500ms;
            z-index: 1;
            border-radius: 15px;
            box-shadow: 0 0.25rem 1rem 0 rgba(47, 91, 234, 0.125);
        }
        .single_advisor_profile .advisor_thumb {
            position: relative;
            z-index: 1;
            border-radius: 15px 15px 0 0;
            margin: 0 auto;
            padding: 30px 30px 0 30px;
            background-color: #3f43fd;
            overflow: hidden;
        }
        .single_advisor_profile .advisor_thumb img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
        .single_advisor_profile .advisor_thumb .social-info {
            position: absolute;
            z-index: 1;
            width: 100%;
            bottom: 0;
            right: 30px;
            text-align: right;
        }
        .single_advisor_profile .advisor_thumb .social-info a {
            font-size: 14px;
            color: #020710;
            padding: 0 5px;
        }
        .single_advisor_profile .single_advisor_details_info {
            position: relative;
            z-index: 1;
            padding: 30px;
            text-align: right;
            transition-duration: 500ms;
            border-radius: 0 0 15px 15px;
            background-color: #ffffff;
        }
        .single_advisor_profile .single_advisor_details_info h6 {
            margin-bottom: 0.25rem;
        }
        .single_advisor_profile .single_advisor_details_info p {
            margin-bottom: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-center">Liste des chauffeurs disponibles</h1>
        <a href="messages.php" class="btn btn-primary">
            <i class="fa fa-envelope"></i> Messages
        </a>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success text-center"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php elseif (!empty($errorMessage)): ?>
        <div class="alert alert-danger text-center"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <?php if (!empty($chauffeurs)): ?>
        <div class="row justify-content-center">
            <?php foreach ($chauffeurs as $chauffeur): ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single_advisor_profile" onclick="openReservationForm(<?php echo $chauffeur['id']; ?>)">
                        <div class="advisor_thumb">
                            <img src="<?php echo htmlspecialchars(
                                !empty($chauffeur['profile_picture']) 
                                ? $chauffeur['profile_picture'] 
                                : 'uploads/default.jpg'
                            ); ?>" alt="Photo de profil">
                        </div>
                        <div class="single_advisor_details_info">
                            <h6><?php echo htmlspecialchars($chauffeur['nom'] ?? 'Nom non disponible'); ?></h6>
                            <p>
                                <strong>Marque :</strong> <?php echo htmlspecialchars($chauffeur['marque_voiture'] ?? 'Non spécifiée'); ?><br>
                                <strong>Plaque :</strong> <?php echo htmlspecialchars($chauffeur['plaque_immatriculation'] ?? 'Non spécifiée'); ?><br>
                                <strong>Couleur :</strong> <?php echo htmlspecialchars($chauffeur['couleur_voiture'] ?? 'Non spécifiée'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Aucun chauffeur disponible pour le moment.</div>
    <?php endif; ?>
</div>

<!-- Formulaire de réservation -->
<div id="reservationForm" class="modal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="reservation.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Formulaire de réservation</h5>
                    <button type="button" class="btn-close" onclick="closeReservationForm()"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="chauffeur_id" name="chauffeur_id">
                    <div class="mb-3">
                        <label for="depart" class="form-label">Lieu de départ</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="depart" name="depart" readonly required>
                            <button type="button" class="btn btn-primary" onclick="getCurrentLocation()">Récupérer ma position</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="destination" class="form-label">Lieu de destination</label>
                        <input type="text" class="form-control" id="destination" name="destination" required>
                    </div>
                    <div class="mb-3">
                        <label for="reservation_date" class="form-label">Date de réservation</label>
                        <input type="date" class="form-control" id="reservation_date" name="reservation_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="reservation_time" class="form-label">Heure de réservation</label>
                        <input type="time" class="form-control" id="reservation_time" name="reservation_time" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Réserver</button>
                    <button type="button" class="btn btn-secondary" onclick="closeReservationForm()">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openReservationForm(chauffeurId) {
        document.getElementById('chauffeur_id').value = chauffeurId;
        document.getElementById('reservationForm').style.display = 'block';
    }

    function closeReservationForm() {
        document.getElementById('reservationForm').style.display = 'none';
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const departField = document.getElementById('depart');
                departField.value = `Latitude: ${latitude}, Longitude: ${longitude}`;
            }, function(error) {
                alert("Erreur lors de la récupération de la position : " + error.message);
            });
        } else {
            alert("La géolocalisation n'est pas prise en charge par ce navigateur.");
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
