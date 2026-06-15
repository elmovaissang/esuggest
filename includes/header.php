<?php

// Inclure auth.php pour utiliser isLoggedIn() et isAdmin()
require_once 'config.php';
require_once 'functions.php';
require_once 'auth.php';

// Titre de la page par défaut
$page_title = $page_title ?? 'eSuggest';

// Récupérer les classeurs si connecté
$folders = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT id, name FROM folder WHERE user_id = ? ORDER BY name ASC");
    $stmt->execute([$_SESSION['user_id']]);
    $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- tag meta SEO -->
    <meta name="description" content="eSuggest - Gestion de factures électroniques conforme à la réforme 2024-2026. Créez, modifiez et gérez vos factures en ligne.">
    <meta name="keywords" content="facture électronique, gestion facture, facturation, eSuggest, réforme 2024-2026, facture UBL, facture CII, facture PDF">
    <meta name="author" content="Zehra Yosma ARIK">
    
    <!-- tag Open Graph réseaux -->
    <meta property="og:title" content="eSuggest - <?= isset($page_title) ? $page_title : 'Accueil' ?>">
    <meta property="og:description" content="Gestion de factures électroniques conforme à la réforme 2024-2026.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://esuggest.free.nf/">
    <meta property="og:image" content="<?= ASSETS_ROOT ?>images/eSuggest.svg">
    <title>eSuggest - <?= isset($page_title) ? htmlspecialchars($page_title) : 'Accueil' ?></title>

    <!-- Feuille de style CSS (Backblaze B2) -->
    <link rel="stylesheet" href="<?= ASSETS_ROOT ?>css/style.css">

    <!-- Google Fonts (Montserrat + Inter) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;600;700&display=swap">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= ASSETS_ROOT ?>images/favicon.ico">

    <!-- Navigation SPA-like -->
    <!-- <script src="<?= ASSETS_ROOT ?>js/main.js" defer></script> -->
</head>
<body>
    <a href="main-content" class="skip-link">Aller au contenu principal</a>
    <header role="banner">
        <div class="header-container">
            <a href="<?= SITE_ROOT ?>index.php" class="logo">
                <img src="<?= ASSETS_ROOT ?>images/eSuggest.svg" alt="eSuggest - Logo de l'application de gestion de factures électroniques" class="logo-img">
            </a>

            <!-- Barre de navigation -->
            <nav class="main-nav">
                <a href="<?= SITE_ROOT ?>index.php" class="nav-link">Accueil</a>
                <a href="<?= SITE_ROOT ?>factures/list.php" class="nav-link">Factures</a>
                <a href="<?= SITE_ROOT ?>classeurs/list.php" class="nav-link">Classeurs</a>

                <!-- Lien vers la gestion des utilisateurs (uniquement pour les admins) -->
                <?php if (isAdmin()): ?>
                    <a href="<?= SITE_ROOT ?>admin/users.php" class="nav-link">Utilisateurs</a>
                <?php endif; ?>

                <!-- Lien de déconnexion (uniquement si connecté) -->
                <?php if (isLoggedIn()): ?>
                    <a href="<?= SITE_ROOT ?>logout.php" class="nav-link logout-link">Déconnexion</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Début du conteneur principal (fermé dans footer.php) -->
    <main id="main-content" class="main-content" role="main">
        <div class="container">
