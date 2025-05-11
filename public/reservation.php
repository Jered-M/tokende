<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$pdo = getDatabaseConnection();

// Ajouter les colonnes si elles n'existent pas
$columns = [
    'username' => "VARCHAR(100) DEFAULT NULL",
    'marque_voiture' => "VARCHAR(100) DEFAULT NULL",
    'plaque_immatriculation' => "VARCHAR(50) DEFAULT NULL",
    'couleur_voiture' => "VARCHAR(50) DEFAULT NULL",
    'profile_picture' => "VARCHAR(255) DEFAULT NULL",
    'latitude' => "DOUBLE DEFAULT NULL", // Nouvelle colonne
    'longitude' => "DOUBLE DEFAULT NULL" // Nouvelle colonne
];

foreach ($columns as $column => $definition) {
    $check = $pdo->query("SHOW COLUMNS FROM users LIKE '$column'")->fetch();
    if (!$check) {
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN $column $definition");
        } catch (PDOException $e) {
            die("Erreur lors de l'ajout de la colonne '$column' : " . $e->getMessage());
        }
    }
}

// Récupération de la position de l'utilisateur
$userLat = $_SESSION['latitude'] ?? null;
$userLon = $_SESSION['longitude'] ?? null;

// Récupération de tous les chauffeurs avec calcul de la distance si la position de l'utilisateur est disponible
if ($userLat !== null && $userLon !== null) {
    $sql = "SELECT u.id, u.username, u.marque_voiture, u.plaque_immatriculation, u.couleur_voiture, u.profile_picture,
                   (6371 * acos(cos(radians(:userLat)) * cos(radians(u.latitude)) * cos(radians(u.longitude) - radians(:userLon)) + sin(radians(:userLat)) * sin(radians(u.latitude)))) AS distance
            FROM users u
            WHERE u.statut = 'chauffeur'
            ORDER BY distance ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':userLat' => $userLat, ':userLon' => $userLon]);
} else {
    $sql = "SELECT u.id, u.username, u.marque_voiture, u.plaque_immatriculation, u.couleur_voiture, u.profile_picture
            FROM users u
            WHERE u.statut = 'chauffeur'";
    $stmt = $pdo->query($sql);
}
$chauffeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['check_availability'])) {
        $reservationDate = $_POST['reservation_date'] ?? '';
        $reservationTime = $_POST['reservation_time'] ?? '';
        if (!empty($reservationDate) && !empty($reservationTime)) {
            // Récupération des chauffeurs disponibles à la date et heure spécifiées
            $sql = "SELECT u.id, u.username, u.marque_voiture, u.plaque_immatriculation, u.couleur_voiture, u.profile_picture,
                           (6371 * acos(cos(radians(:userLat)) * cos(radians(u.latitude)) * cos(radians(u.longitude) - radians(:userLon)) + sin(radians(:userLat)) * sin(radians(u.latitude)))) AS distance
                    FROM users u
                    LEFT JOIN reservation r ON u.id = r.user_id AND r.date_reservation = :reservationDate AND r.heure_reservation = :reservationTime
                    WHERE u.statut = 'chauffeur' AND r.id IS NULL
                    HAVING distance <= 10
                    ORDER BY distance ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':userLat' => $userLat,
                ':userLon' => $userLon,
                ':reservationDate' => $reservationDate,
                ':reservationTime' => $reservationTime
            ]);
            $chauffeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $errorMessage = "Veuillez spécifier une date et une heure pour vérifier les disponibilités.";
        }
    } else {
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

                // Extraire les coordonnées de départ
                preg_match('/Latitude: ([^,]+), Longitude: (.+)/', $depart, $matches);
                $latitude = $matches[1] ?? null;
                $longitude = $matches[2] ?? null;

                // Générer un lien vers localisation.php avec showUser=1
                $locationLink = "localisation.php?latitude=" . urlencode($latitude) . "&longitude=" . urlencode($longitude) . "&showUser=1";

                // Envoyer un message au chauffeur avec un lien clair
                $message = "Vous avez été réservé pour une course le $reservationDate à $reservationTime. 
                            <a href='$locationLink' target='_blank'>
                                <i class='fa fa-map-marker' style='color: red;'></i> Voir la localisation
                            </a>";
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
    <!-- Formulaire pour vérifier les disponibilités -->
    <form action="reservation.php" method="post" class="mb-4">
        <div class="row">
            <div class="col-md-5">
                <label for="reservation_date" class="form-label">Date de réservation</label>
                <input type="date" class="form-control" id="reservation_date" name="reservation_date" required>
            </div>
            <div class="col-md-5">
                <label for="reservation_time" class="form-label">Heure de réservation</label>
                <input type="time" class="form-control" id="reservation_time" name="reservation_time" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" name="check_availability" class="btn btn-primary w-100">Vérifier</button>
            </div>
        </div>
    </form>
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
                            <h6><?php echo htmlspecialchars($chauffeur['username'] ?? 'username non disponible'); ?></h6>
                            <p>
                                <strong>Marque :</strong> <?php echo htmlspecialchars($chauffeur['marque_voiture'] ?? 'Non spécifiée'); ?><br>
                                <strong>Plaque :</strong> <?php echo htmlspecialchars($chauffeur['plaque_immatriculation'] ?? 'Non spécifiée'); ?><br>
                                <strong>Couleur :</strong> <?php echo htmlspecialchars($chauffeur['couleur_voiture'] ?? 'Non spécifiée'); ?><br>
                                <?php if (isset($chauffeur['distance'])): ?>
                                    <strong>Distance :</strong> <?php echo round($chauffeur['distance'], 2); ?> km
                                <?php endif; ?>
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
                    <div id="chauffeurDetails" class="mb-3">
                        <!-- Les détails du chauffeur seront affichés ici -->
                    </div>
                    <div class="mb-3">
                        <label for="depart" class="form-label">Lieu de départ</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="depart" name="depart" readonly required>
                            <button type="button" class="btn btn-primary" onclick="setCurrentLocation()">Utiliser ma position actuelle</button>
                            <button type="button" class="btn btn-secondary" onclick="openMap('depart')">Choisir sur la carte</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="destination" class="form-label">Lieu de destination</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="destination" name="destination" readonly required>
                            <button type="button" class="btn btn-primary" onclick="openMap('destination')">Choisir sur la carte</button>
                        </div>
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
                    <button type="submit" class="btn btn-primary">Confirmer la réservation</button>
                    <button type="button" class="btn btn-secondary" onclick="closeReservationForm()">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour la carte -->
<div id="mapModal" class="modal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sélectionnez un lieu sur la carte</h5>
                <button type="button" class="btn-close" onclick="closeMap()"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 400px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="confirmLocation()">Confirmer</button>
                <button type="button" class="btn btn-secondary" onclick="closeMap()">Annuler</button>
            </div>
        </div>
    </div>
</div>

<script>
    let map, marker, selectedField;

    function openMap(field) {
        selectedField = field;
        document.getElementById('mapModal').style.display = 'block';

        // Initialiser la carte si elle n'existe pas encore
        if (!map) {
            map = L.map('map').setView([-11.6877, 27.5026], 13); // Coordonnées par défaut (Lubumbashi)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Ajouter un événement de clic pour placer un marqueur
            map.on('click', function (e) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
            });
        }
    }

    function closeMap() {
        document.getElementById('mapModal').style.display = 'none';
    }

    function confirmLocation() {
        if (marker) {
            const latLng = marker.getLatLng();
            const field = document.getElementById(selectedField);
            field.value = `Latitude: ${latLng.lat}, Longitude: ${latLng.lng}`;
            closeMap();
        } else {
            alert("Veuillez sélectionner un lieu sur la carte.");
        }
    }

    function setCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const departField = document.getElementById('depart');
                departField.value = `Latitude: ${latitude}, Longitude: ${longitude}`;
                alert("Votre position actuelle a été définie comme lieu de départ.");
            }, function (error) {
                alert("Erreur lors de la récupération de la position : " + error.message);
            });
        } else {
            alert("La géolocalisation n'est pas prise en charge par ce navigateur.");
        }
    }
</script>

<script>
        const map = L.map('map').setView([-11.6877, 27.5026], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Icônes personnalisées
        const icons = {
            hospital: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/201/201818.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            }),
            school: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/201/201818.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            }),
            pharmacy: L.icon({
                iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/pharmacy.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            }),
            restaurant: L.icon({
                iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/restaurant.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            }),
            shop: L.icon({
                iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/shopping.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            }),
            bank: L.icon({
                iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/dollar.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24]
            }),
            fuel: L.icon({
                iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/gas.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24]
            }),
            driver: L.icon({
                iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/cabs.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            }),
            default: L.icon({
                iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/blue-dot.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24]
            })
        };

        let userLat = null;
        let userLon = null;
        let routingControl = null;

        // Fonction pour afficher la route la plus courte
        function afficherRoute(lat, lon) {
            if (userLat === null || userLon === null) {
                alert("Position de l'utilisateur non disponible.");
                return;
            }

            if (routingControl) {
                map.removeControl(routingControl);
            }

            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(userLat, userLon),
                    L.latLng(lat, lon)
                ],
                routeWhileDragging: false,
                geocoder: L.Control.Geocoder.nominatim(),
                router: new L.Routing.osrmv1({
                    serviceUrl: 'https://router.project-osrm.org/route/v1',
                    language: 'fr',
                    profile: 'car'
                }),
                createMarker: function(i, wp) {
                    return L.marker(wp.latLng, {
                        icon: i === 0 ? icons.default : icons.driver
                    });
                },
                lineOptions: {
                    styles: [{color: 'blue', opacity: 0.6, weight: 4}]
                },
                addWaypoints: false
            }).addTo(map);

            routingControl.on('routesfound', function(e) {
                const routes = e.routes;
                const summary = routes[0].summary;
                const distance = (summary.totalDistance / 1000).toFixed(2);
                const time = (summary.totalTime / 60).toFixed(2);
                L.popup()
                    .setLatLng([lat, lon])
                    .setContent(`<strong>Distance :</strong> ${distance} km<br><strong>Temps estimé :</strong> ${time} minutes`)
                    .openOn(map);
            });
        }

        // Fonction pour ouvrir un formulaire de confirmation avec le nom du lieu
        function ouvrirFormulaireAvecNom(lat, lon, nom) {
            // Supprimer le formulaire existant s'il y en a un
            const existingForm = document.getElementById('formulaire-confirmation');
            if (existingForm) {
                existingForm.remove();
            }

            const formulaireHtml = `
                <div id="formulaire-confirmation">
                    <p>Voulez-vous aller à <strong>${nom}</strong> ?</p>
                    <button id="confirmer">Oui</button>
                    <button id="annuler">Non</button>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', formulaireHtml);

            document.getElementById('confirmer').addEventListener('click', () => {
                afficherRoute(lat, lon);
                document.getElementById('formulaire-confirmation').remove();
            });

            document.getElementById('annuler').addEventListener('click', () => {
                document.getElementById('formulaire-confirmation').remove();
            });
        }

        // Localisation de l'utilisateur
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                userLat = position.coords.latitude;
                userLon = position.coords.longitude;

                // Centrer la carte sur la position de l'utilisateur
                map.setView([userLat, userLon], 15);

                // Ajouter un marqueur pour l'utilisateur
                L.marker([userLat, userLon], { icon: icons.default })
                    .addTo(map)
                    .bindPopup("<strong>Vous êtes ici</strong>")
                    .openPopup();
            }, err => {
                alert("Erreur de géolocalisation : " + err.message);
                console.error("Erreur de géolocalisation :", err);
            });
        } else {
            alert("La géolocalisation n'est pas supportée par ce navigateur.");
            console.error("La géolocalisation n'est pas supportée par ce navigateur.");
        }

        // Overpass API Query
        const overpassQuery = `
            [out:json];
            (
                node["amenity"~"hospital|school|pharmacy|restaurant|bank|fuel"](-11.8,27.4,-11.6,27.6);
                node["shop"](-11.8,27.4,-11.6,27.6);
            );
            out body;
        `;

        fetch("https://overpass-api.de/api/interpreter", {
            method: "POST",
            headers: { "Content-Type": "text/plain" },
            body: overpassQuery
        })
        .then(res => res.json())
        .then(data => {
            data.elements.forEach(el => {
                const type = el.tags.amenity || el.tags.shop || "default";
                const name = el.tags.name || "Lieu inconnu";
                const icon = icons[type] || icons.default;

                const marker = L.marker([el.lat, el.lon], { icon })
                    .addTo(map)
                    .bindPopup(`<strong>${name}</strong><br>Type : ${type}`);

                marker.on('click', () => {
                    ouvrirFormulaireAvecNom(el.lat, el.lon, name);
                });
            });
        })
        .catch(err => {
            alert("Erreur lors du chargement des lieux. Veuillez réessayer plus tard.");
            console.error("Erreur chargement lieux :", err);
        });
    </script>

<!-- Inclure Leaflet.js pour la carte -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    function openReservationForm(chauffeurId) {
        // Récupérer les informations du chauffeur via AJAX
        fetch(`get_chauffeur_details.php?chauffeur_id=${chauffeurId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Vérifier si le champ username est défini, sinon afficher un message par défaut
                    const username = data.username || 'Nom non disponible';
                    document.getElementById('chauffeurDetails').innerHTML = `
                        <h6>Nom : ${username}</h6>
                        <p>
                            <strong>Marque :</strong> ${data.marque_voiture || 'Non spécifiée'}<br>
                            <strong>Plaque :</strong> ${data.plaque_immatriculation || 'Non spécifiée'}<br>
                            <strong>Couleur :</strong> ${data.couleur_voiture || 'Non spécifiée'}
                        </p>
                    `;
                    document.getElementById('chauffeur_id').value = chauffeurId;
                    document.getElementById('reservationForm').style.display = 'block';
                } else {
                    alert("Erreur lors de la récupération des détails du chauffeur.");
                }
            })
            .catch(error => {
                console.error("Erreur AJAX :", error);
                alert("Une erreur s'est produite lors de la récupération des détails du chauffeur.");
            });
    }

    function closeReservationForm() {
        document.getElementById('reservationForm').style.display = 'none';
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
