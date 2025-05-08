<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $publicationId = $data['publication_id'] ?? null;

    if ($publicationId && isset($_SESSION['user_id'])) {
        $pdo = getDatabaseConnection();

        // Vérifier si l'utilisateur a déjà liké cette publication
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = :user_id AND publication_id = :publication_id");
        $checkStmt->execute([':user_id' => $_SESSION['user_id'], ':publication_id' => $publicationId]);
        $alreadyLiked = $checkStmt->fetchColumn();

        if ($alreadyLiked) {
            echo json_encode(['success' => false, 'message' => 'Vous avez déjà liké cette publication.']);
        } else {
            // Ajouter un like
            $stmt = $pdo->prepare("INSERT INTO likes (user_id, publication_id) VALUES (:user_id, :publication_id)");
            $stmt->execute([':user_id' => $_SESSION['user_id'], ':publication_id' => $publicationId]);

            // Récupérer le nombre total de likes
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE publication_id = :publication_id");
            $countStmt->execute([':publication_id' => $publicationId]);
            $likeCount = $countStmt->fetchColumn();

            echo json_encode(['success' => true, 'like_count' => $likeCount]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    }
    exit();
}
