<?php

// Racine du site pour l'URL
define('SITE_ROOT', '/Projects/esuggest/');

// Chemin absolu assets (css, js, images)
define('ASSETS_ROOT', SITE_ROOT . 'assets/');

// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'esuggest';
$username = 'root';
$password = '';

// Connexion à la base de données avec PDO
try {
    // création de la connexion à MySQL
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // configurer PDO -> affichage erreurs SQL (utile pour débogage)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // en cas d'erreur, message d'erreur + stop le script (die)
    die("Erreur de connexion à la base de données : ".$e->getMessage());
}
