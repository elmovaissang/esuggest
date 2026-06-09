<?php
// Charger les variables d'environnement depuis .env
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    $host = $env['DB_HOST'] ?? 'localhost';
    $dbname = $env['DB_NAME'] ?? 'esuggest';
    $username = $env['DB_USER'] ?? 'root';
    $password = $env['DB_PASS'] ?? '';
    $siteRoot = $env['SITE_ROOT'] ?? '/';
} else {
    // Si pas de .env (ex: Scalingo), utiliser DATABASE_URL
    $dbUrl = parse_url(getenv('DATABASE_URL') ?? '');
    $host = $dbUrl['host'] ?? 'localhost';
    $dbname = ltrim($dbUrl['path'] ?? '', '/') ?: 'esuggest';
    $username = $dbUrl['user'] ?? 'root';
    $password = $dbUrl['pass'] ?? '';
    $siteRoot = '/';
}

// CONSTANTES POUR LES URLS (SCALINGO)
// Chemin de base pour les URLs
define('SITE_ROOT', $siteRoot);

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
