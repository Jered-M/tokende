<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $publicationId = $data['publication_id'] ?? null;
    $comment = $data['comment'] ?? null;

    if ($publicationId && $comment && isset($_SESSION['user_id'])) {
        $pdo = getDatabaseConnection();

        try {
            // Ajouter le commentaire
            $stmt = $pdo->prepare("INSERT INTO comments (user_id, publication_id, comment, created_at) VALUES (:user_id, :publication_id, :comment, NOW())");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':publication_id' => $publicationId,
                ':comment' => $comment
            ]);

            // Récupérer tous les commentaires pour la publication
            $commentsStmt = $pdo->prepare("SELECT c.comment, c.created_at, u.username 
                                           FROM comments c 
                                           JOIN users u ON c.user_id = u.id 
                                           WHERE c.publication_id = :publication_id 
                                           ORDER BY c.created_at DESC");
            $commentsStmt->execute([':publication_id' => $publicationId]);
            $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'comments' => $comments]);
        } catch (PDOException $e) {
            // En cas d'erreur, renvoyer un message d'erreur
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du commentaire : ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $publicationId = $_GET['publication_id'] ?? null;

    if ($publicationId) {
        $pdo = getDatabaseConnection();

        try {
            // Récupérer tous les commentaires pour la publication
            $commentsStmt = $pdo->prepare("SELECT c.comment, c.created_at, u.username 
                                           FROM comments c 
                                           JOIN users u ON c.user_id = u.id 
                                           WHERE c.publication_id = :publication_id 
                                           ORDER BY c.created_at DESC");
            $commentsStmt->execute([':publication_id' => $publicationId]);
            $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'comments' => $comments]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des commentaires : ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    }
    exit();
}
