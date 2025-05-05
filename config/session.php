<?php
// Configuration commune des sessions
// S'assurer que les cookies de session sont disponibles dans tout le site
$currentCookieParams = session_get_cookie_params();
session_set_cookie_params(
    $currentCookieParams["lifetime"],
    '/', // Le cookie est disponible dans tout le site
    $currentCookieParams["domain"],
    $currentCookieParams["secure"],
    $currentCookieParams["httponly"]
);

// Démarrer ou poursuivre la session
session_start();
?>