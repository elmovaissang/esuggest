<?php

// Inclure les fichiers nécessaires
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Titre de la page
$page_title = "Accueil";

//  Vérifier si user connecté, si pas co redirection login.php
requireLogin();

// header après vérification de connexion
include_once 'includes/header.php';
?>

<!-- Afficher message de bienvenue personnalisé -->
<div class="container">
    <h1>Bienvenue sur eSuggest, <?= htmlspecialchars($_SESSION['user_fname'] . ' ' . $_SESSION['user_name']) ?> !</h1>
    <p>Vous êtes connecté en tant que <strong><?= htmlspecialchars($_SESSION['role']) ?>.</strong></p>

    <!-- Section des actions avec boutons en ligne -->
    <section class="actions-section">
        <h2>Que souhaitez-vous faire ?</h2>
        <div class="actions">
            <a href="<?= SITE_ROOT ?>factures/list.php" class="btn">Voir les factures</a>
            <a href="<?= SITE_ROOT ?>factures/create.php" class="btn">Créer une facture</a>
            <a href="<?= SITE_ROOT ?>classeurs/list.php" class="btn">Gérer les classeurs</a>
            <?php if (isAdmin()): ?>
                <a href="<?= SITE_ROOT ?>admin/users.php" class="btn">Gérer les utilisateurs</a>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
// Footer
include_once 'includes/footer.php';
