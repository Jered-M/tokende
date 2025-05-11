<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Coordonnées par défaut si aucune n'est spécifiée
$latitude = $_GET['latitude'] ?? -11.6877; // Lubumbashi par défaut
$longitude = $_GET['longitude'] ?? 27.5026; // Lubumbashi par défaut

// Vérifier si la position de l'utilisateur doit être affichée
$showUser = isset($_GET['showUser']) && $_GET['showUser'] == '1';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Localisation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <style>
        html, body { margin: 0; height: 100%; }
        #map { width: 100%; height: 100vh; }
        #formulaire-confirmation {
            position: absolute;
            top: 20px;
            left: 20px;
            background: white;
            padding: 10px;
            border: 1px solid #ccc;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <script>
        const latitude = <?php echo json_encode($latitude); ?>;
        const longitude = <?php echo json_encode($longitude); ?>;
        const showUser = <?php echo json_encode($showUser); ?>;

        const map = L.map('map').setView([latitude, longitude], 13);
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
</body>
</html>
