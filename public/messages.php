<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$pdo = getDatabaseConnection();
$userId = $_SESSION['user_id'];

// Récupérer la liste des contacts avec leur dernier message envoyé ou reçu
$sqlContacts = "
    SELECT u.id AS sender_id, u.username AS sender_name, u.profile_picture,
           m.message AS last_message, m.created_at AS last_message_time
    FROM (
        SELECT 
            CASE 
                WHEN m.user_id = :user_id THEN m.recipient_id 
                ELSE m.user_id 
            END AS contact_id,
            MAX(m.created_at) AS last_message_time
        FROM message m
        WHERE m.user_id = :user_id OR m.recipient_id = :user_id
        GROUP BY contact_id
    ) latest
    JOIN users u ON u.id = latest.contact_id
    JOIN message m ON (
        (m.user_id = :user_id AND m.recipient_id = u.id OR m.user_id = u.id AND m.recipient_id = :user_id)
        AND m.created_at = latest.last_message_time
    )
    ORDER BY last_message_time DESC
";

$stmtContacts = $pdo->prepare($sqlContacts);
$stmtContacts->execute([':user_id' => $userId]);
$contacts = $stmtContacts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Messagerie</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #F0F2F5;
    }
    .rounded-circle {
      object-fit: cover;
    }
  </style>
</head>
<body>
<section class="py-5">
  <div class="container">
    <div class="card" style="border-radius: 15px;">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 col-lg-5 col-xl-4 mb-4 mb-md-0">
            <div class="p-3">
              <div class="input-group rounded mb-3">
                <input type="search" class="form-control rounded" placeholder="Rechercher..." aria-label="Search">
                <span class="input-group-text border-0">
                  <i class="fas fa-search"></i>
                </span>
              </div>
              <div style="position: relative; height: 400px; overflow-y: auto;">
                <ul class="list-unstyled mb-0">
                  <?php if (empty($contacts)): ?>
                    <li class="p-2 text-center text-muted">Aucune conversation</li>
                  <?php endif; ?>
                  <?php foreach ($contacts as $contact): ?>
                    <li class="p-2 border-bottom">
                      <a href="conversation.php?contact_id=<?= $contact['sender_id']; ?>" class="d-flex justify-content-between text-decoration-none text-dark">
                        <div class="d-flex flex-row">
                          <img src="<?= htmlspecialchars($contact['profile_picture'] ?? 'uploads/default.jpg'); ?>" alt="avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                          <div class="pt-1">
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($contact['sender_name']); ?></h6>
                            <p class="small text-muted mb-0">
                              <?= htmlspecialchars(strlen($contact['last_message']) > 40 ? substr($contact['last_message'], 0, 40) . '...' : $contact['last_message']); ?>
                            </p>
                          </div>
                        </div>
                        <div class="pt-1 text-end">
                          <p class="small text-muted mb-1"><?= date('H:i', strtotime($contact['last_message_time'])); ?></p>
                          <p class="small text-muted"><?= date('d/m', strtotime($contact['last_message_time'])); ?></p>
                        </div>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
          <!-- Colonne des messages à droite à ajouter ici si nécessaire -->
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>
