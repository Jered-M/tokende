<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$pdo = getDatabaseConnection();
$userId = $_SESSION['user_id'];
$contactId = $_GET['contact_id'] ?? null;

if (!$contactId) {
    header("Location: messages.php");
    exit();
}

// Récupérer les informations du contact
$sqlContact = "SELECT username, profile_picture FROM users WHERE id = :contact_id";
$stmtContact = $pdo->prepare($sqlContact);
$stmtContact->execute([':contact_id' => $contactId]);
$contact = $stmtContact->fetch(PDO::FETCH_ASSOC);

if (!$contact) {
    header("Location: messages.php");
    exit();
}

// Récupérer les messages échangés
$sqlMessages = "SELECT m.message, m.created_at, 
                       CASE WHEN m.user_id = :user_id THEN 'sent' ELSE 'received' END AS direction,
                       u.username AS sender_name, u.profile_picture 
                FROM message m 
                JOIN users u ON m.user_id = u.id 
                WHERE (m.user_id = :user_id AND m.recipient_id = :contact_id) 
                   OR (m.user_id = :contact_id AND m.recipient_id = :user_id)
                ORDER BY m.created_at ASC";
$stmtMessages = $pdo->prepare($sqlMessages);
$stmtMessages->execute([
    ':user_id' => $userId,
    ':contact_id' => $contactId,
]);
$messages = $stmtMessages->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageContent = trim($_POST['message'] ?? '');

    if (!empty($messageContent)) {
        try {
            $sql = "INSERT INTO message (user_id, recipient_id, message, created_at) 
                    VALUES (:user_id, :recipient_id, :message, CURRENT_TIMESTAMP)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId, // L'utilisateur connecté (chauffeur ou client)
                ':recipient_id' => $contactId, // L'autre participant à la conversation
                ':message' => $messageContent,
            ]);

            // Redirection après l'envoi pour éviter la resoumission
            header("Location: conversation.php?contact_id=$contactId");
            exit();
        } catch (PDOException $e) {
            die("Erreur lors de l'envoi du message : " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Veuillez écrire un message avant d\'envoyer.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Conversation avec <?php echo htmlspecialchars($contact['username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<section style="background-color: #F0F2F5;">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card" id="chat3" style="border-radius: 15px;">
          <div class="card-body">
            <div class="d-flex align-items-center mb-4">
              <img src="<?php echo htmlspecialchars($contact['profile_picture'] ?? 'uploads/default.jpg'); ?>" alt="avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
              <h5 class="mb-0">Conversation avec <?php echo htmlspecialchars($contact['username']); ?></h5>
            </div>
            <div class="pt-3 pe-3" data-mdb-perfect-scrollbar-init
              style="position: relative; height: 400px; overflow-y: auto;">
              <?php foreach ($messages as $message): ?>
                <div class="d-flex flex-row justify-content-<?php echo $message['direction'] === 'sent' ? 'end' : 'start'; ?> mb-3">
                  <?php if ($message['direction'] === 'received'): ?>
                    <img src="<?php echo htmlspecialchars($message['profile_picture'] ?? 'uploads/default.jpg'); ?>"
                      alt="avatar" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                  <?php endif; ?>
                  <div class="p-2 rounded" style="background-color: <?php echo $message['direction'] === 'sent' ? '#D1E7DD' : '#FFFFFF'; ?>; max-width: 70%;">
                    <p class="mb-1 fw-bold"><?php echo htmlspecialchars($message['sender_name']); ?></p>
                    <p class="mb-1"><?php echo htmlspecialchars($message['message']); ?></p>
                    <small class="text-muted"><?php echo htmlspecialchars($message['created_at']); ?></small>
                  </div>
                  <?php if ($message['direction'] === 'sent'): ?>
                    <img src="<?php echo htmlspecialchars($message['profile_picture'] ?? 'uploads/default.jpg'); ?>"
                      alt="avatar" class="rounded-circle ms-2" style="width: 40px; height: 40px;">
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="text-muted d-flex justify-content-start align-items-center pe-3 pt-3 mt-2">
              <img src="<?php echo htmlspecialchars($selectedUserProfile['profile_picture'] ?? 'uploads/default.jpg'); ?>"
                alt="avatar" class="rounded-circle me-2" style="width: 40px; height: 40px;">
              <form action="conversation.php?contact_id=<?php echo $contactId; ?>" method="post" class="d-flex w-100">
                <input type="hidden" name="recipient_id" value="<?php echo $contactId; ?>">
                <input type="text" class="form-control form-control-lg" name="message" placeholder="Écrivez un message..." required>
                <button type="submit" class="btn btn-primary ms-3"><i class="fas fa-paper-plane"></i></button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>