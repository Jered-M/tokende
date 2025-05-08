<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi de commande - Lubumbashi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        #map {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <script>
        const map = L.map('map').setView([-11.6877, 27.5026], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Icônes personnalisées (petit format)
        const icons = {
            hospital: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/24/2965/2965567.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            }),
            school: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/24/3449/3449671.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            }),
            pharmacy: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/24/901/901120.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            }),
            restaurant: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/24/3075/3075977.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            }),
            shop: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/24/1170/1170678.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            }),
            default: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/24/252/252025.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            }),
            taxi: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/24/854/854894.png',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            })
        };

        // Marqueur du taxi (position dynamique simulée)
        let taxiMarker = L.marker([-11.6877, 27.5026], { icon: icons.taxi })
            .addTo(map)
            .bindPopup("Votre taxi est ici");

        // Mise à jour position du taxi
        function updateTaxiPosition() {
            fetch('../api/get_position.php?taxi_id=1')
                .then(res => res.json())
                .then(data => {
                    if (data.latitude && data.longitude) {
                        taxiMarker.setLatLng([data.latitude, data.longitude]);
                    }
                })
                .catch(err => console.error("Erreur de position :", err));
        }

        updateTaxiPosition();
        setInterval(updateTaxiPosition, 3000);

        // Charger les lieux publics depuis Overpass API
        const overpassURL = "https://overpass-api.de/api/interpreter";
        const query = `
            [out:json];
            (
                node["amenity"~"hospital|school|pharmacy|restaurant"](-11.72,27.45,-11.65,27.55);
                node["shop"](-11.72,27.45,-11.65,27.55);
            );
            out body;
        `;

        fetch(overpassURL, {
            method: "POST",
            body: query,
            headers: { "Content-Type": "text/plain" }
        })
        .then(res => res.json())
        .then(data => {
            data.elements.forEach(el => {
                const type = el.tags.amenity || el.tags.shop || "default";
                const name = el.tags.name || "Lieu inconnu";
                const icon = icons[type] || icons.default;

                L.marker([el.lat, el.lon], { icon })
                    .addTo(map)
                    .bindPopup(`<strong>${name}</strong><br>Type : ${type}`);
            });
        })
        .catch(err => console.error("Erreur chargement lieux :", err));
    </script>
</body>
</html>
