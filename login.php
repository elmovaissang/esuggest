<?php

// Inclure les fichiers nécessaires
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Titre de la page
$page_title = "Connexion";

// Si user connecté, redirigé vers index.php
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Traitement du formulaire de connexion (si formulaire soumis)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // récupérer et nettoyer les données du formulaire
    $email = cleanInput($_POST['email']);
    $password = $_POST['pwd'];

    // essayer de connecter l'utilisateur
    if (login($pdo, $email, $password)) {
        // rediriger si connexion ok
        header('Location: ' . SITE_ROOT . 'index.php');
        exit;
    } else {
        // si connexion echec -> message d'erreur
        $error = "Email ou mot de passe incorrect.";
    }
}

// header après vérification de connexion
include_once 'includes/header.php';
?>

<div class="login-container">
    <h1>Connexion à eSuggest</h1>

    <!-- Message d'erreur si echec de connexion -->
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form action="" method="post" class="login-form">
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" placeholder="email@exemple.fr" required>
        </div>
        <div class="form-group">
            <label for="pwd">Mot de passe :</label>
            <input type="password" name="pwd" id="pwd" placeholder="Votre mot de passe" required>
        </div>
        <button class="btn" type="submit">Se connecter</button>
    </form>
</div>

<?php
// Footer
include_once 'includes/footer.php';
