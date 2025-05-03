<?php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $depart = $_POST['depart'];
    $destination = $_POST['destination'];
    $client_id = 1; // Remplacez par l'ID de l'utilisateur connecté

    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("INSERT INTO rides (client_id, depart, destination, status) VALUES (?, ?, ?, 'en attente')");
        $stmt->execute([$client_id, $depart, $destination]);

        echo "Votre demande de trajet a été enregistrée.";
    } catch (PDOException $e) {
        die('Erreur lors de la demande de trajet : ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demander un trajet</title>
</head>
<body>
    <h1>Demander un trajet</h1>
    <form method="POST" action="">
        <label for="depart">Point de départ :</label>
        <input type="text" id="depart" name="depart" required>
        <br>
        <label for="destination">Destination :</label>
        <input type="text" id="destination" name="destination" required>
        <br>
        <button type="submit">Demander un trajet</button>
    </form>
</body>
</html>
