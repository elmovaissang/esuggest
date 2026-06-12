<?php

// Stocker les sessions
ini_set('session.save_path', __DIR__ . '/../../sessions/');
session_start();

// ID database - InfinityFree
$host = 'sql306.infinityfree.com';      // hôte MySQL
$dbname = 'if0_42157903_esuggest';      // Nom de la DB
$username = 'if0_42157903';             // Nom user MySQL
$password = 'pEBuZVpvFEYkI';            // Mot de passe MySQL généré par InfinityFree

// Chemin de base, racine domaine - InfinityFree
define('SITE_ROOT', '/');

// Chemin files statics - B2
define('ASSETS_ROOT', 'https://f003.backblazeb2.com/file/esuggest-projet/assets/');

// Connexion database
try {
    // Connexion PDO w/ id InfinityFree
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            // erreurs SQL comm exceptions
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // résultat sous forme de tableau associatif
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // désactive l'émulation des requêtes préparées
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // msg si erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Désactiver display error en prod
ini_set('display_errors', 1);                   // 0 - Désactive l'affichage des erreurs, 1 - Active l'affichage
ini_set('log_errors', 1);                       // Active la journalisation des erreurs
ini_set('error_log', '/tmp/php_errors.log');    // Chemin pour les logs (InfinityFree gère les logs automatiquement)
