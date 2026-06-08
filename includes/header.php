<?php

// Inclure auth.php pour utiliser isLoggedIn() et isAdmin()
require_once 'config.php';
require_once 'functions.php';
require_once 'auth.php';

// Récupérer les classeurs si connecté (pour le menu déroulant)
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
    <title>eSuggest - <?= isset($page_title) ? htmlspecialchars($page_title) : 'Accueil' ?></title>

    <!-- Feuille de style CSS -->
    <link rel="stylesheet" href="<?= ASSETS_ROOT ?>css/style.css">

    <!-- Google Fonts (Montserrat + Inter) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;600;700&display=swap">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= ASSETS_ROOT ?>images/favicon.ico">
</head>
<body>
    <header>
        <div class="header-container">
            <!-- Logo -->
            <a href="<?= SITE_ROOT ?>index.php" class="logo">eSuggest</a>

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
    <main class="main-content">
        <div class="container">
