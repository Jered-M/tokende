<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$pdo = getDatabaseConnection();
$searchResults = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = trim($_GET['query']);

    if (!empty($query)) {
        $sql = "SELECT id, username, email, nom FROM users WHERE username LIKE :query OR email LIKE :query OR nom LIKE :query";
        $stmt = $pdo->prepare($sql);
        $searchTerm = "%" . $query . "%";
        $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Recherche d'utilisateurs</h1>
        <form action="recherche.php" method="get" class="d-flex mb-4">
            <input class="form-control me-2" type="search" name="query" placeholder="Rechercher un utilisateur" aria-label="Search" value="<?php echo htmlspecialchars($_GET['query'] ?? ''); ?>">
            <button class="btn btn-primary" type="submit">Rechercher</button>
        </form>

        <?php if (!empty($searchResults)): ?>
            <h2>Résultats de recherche :</h2>
            <ul class="list-group">
                <?php foreach ($searchResults as $user): ?>
                    <li class="list-group-item">
                        <strong>Nom d'utilisateur :</strong> <?php echo htmlspecialchars($user['username']); ?><br>
                        <strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?><br>
                        <strong>Nom :</strong> <?php echo htmlspecialchars($user['nom']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])): ?>
            <p>Aucun utilisateur trouvé pour la recherche "<?php echo htmlspecialchars($_GET['query']); ?>".</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>