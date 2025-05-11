<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_GET['chauffeur_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID du chauffeur manquant.']);
    exit();
}

$pdo = getDatabaseConnection();
$chauffeurId = intval($_GET['chauffeur_id']);

$stmt = $pdo->prepare("SELECT username, marque_voiture, plaque_immatriculation, couleur_voiture FROM users WHERE id = :id AND statut = 'chauffeur'");
$stmt->execute([':id' => $chauffeurId]);
$chauffeur = $stmt->fetch(PDO::FETCH_ASSOC);

if ($chauffeur) {
    echo json_encode(['success' => true] + $chauffeur);
} else {
    echo json_encode(['success' => false, 'message' => 'Chauffeur introuvable.']);
}
?>
