<?php

// --- 1. IDENTIFIANTS DE LA BASE DE DONNÉES ---
$host = 'sql306.infinityfree.com';
$dbname = 'if0_42157903_esuggest';      // Nom de la DB
$username = 'if0_42157903';             // Nom user MySQL
$password = 'ton_mot_de_passe_mysql';   // Mot de passe MySQL généré par InfinityFree

// --- 2. CHEMIN DE BASE POUR LE SITE ---
$siteRoot = '/';        // racine

// --- 3. CHEMIN POUR LES FICHIERS STATIQUES (CSS, JS, IMAGES) ---
// Backblaze B2
define('ASSETS_ROOT', 'https://f001.backblazeb2.com/file/esuggest-assets/');

// --- 4. CONNEXION À LA BASE DE DONNÉES ---
try {
    // Connexion PDO avec les identifiants InfinityFree
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Affiche les erreurs SQL
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Récupère les résultats sous forme de tableau associatif
        ]
    );
} catch (PDOException $e) {
    // Message d'erreur personnalisé (ne pas afficher les détails en production)
    die("Une erreur est survenue. Veuillez réessayer plus tard.");
}

// --- 5. DÉSACTIVER L'AFFICHAGE DES ERREURS EN PRODUCTION ---
ini_set('display_errors', 0);                   // Désactive l'affichage des erreurs
ini_set('log_errors', 1);                       // Active la journalisation des erreurs
ini_set('error_log', '/tmp/php_errors.log');    // Chemin pour les logs (InfinityFree gère les logs automatiquement)
