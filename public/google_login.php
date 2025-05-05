<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google\Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/likes/public/google_login.php');
$client->addScope('email');
$client->addScope('profile');

if (!isset($_GET['code'])) {
    // Redirige vers Google pour l'authentification
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit;
} else {
    // Récupère le token d'accès
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Récupère les informations utilisateur
    $oauth = new Google\Service\Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    // Stocke les informations utilisateur dans la base de données
    $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=your_database_name;charset=utf8mb4';
    $username = 'your_database_user';
    $password = 'your_database_password';

    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $sql = "INSERT INTO user (nom, email, telephone, sexe) 
                VALUES (:nom, :email, :telephone, :sexe)
                ON DUPLICATE KEY UPDATE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $userInfo->name,
            ':email' => $userInfo->email,
            ':telephone' => null,
            ':sexe' => null,
        ]);

        // Redirige vers la page de connexion ou d'accueil
        header('Location: /login.php');
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
