<?php

// CONSTANTES POUR LES URLS (SCALINGO)
// Chemin de base pour les URLs
define('SITE_ROOT', '/');

// Chemin absolu assets (css, js, images)
define('ASSETS_ROOT', SITE_ROOT . 'assets/');

// CONNEXION À LA BASE DE DONNÉES (SCALINGO)
// Format : mysql://user:password@host:port/dbname
$dbUrl = parse_url(getenv('DATABASE_URL'));

// Extraire les informations de connexion
$host = $dbUrl['host'];
$dbname = ltrim($dbUrl['path'], '/'); // Supprime le "/" au début
$username = $dbUrl['user'];
$password = $dbUrl['pass'];

// Conjnexion à la base de données avec PDO
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Configurer PDO pour afficher les erreurs SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreuur, afficher un message et arrêter le script
    die("Erreur de connexion à la base de données :" . $e->getMessage());
}
