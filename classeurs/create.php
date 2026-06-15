<?php

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Titre de la page
$page_title = "Créer un classeur - eSuggest";

// Vérifier la connexion du user
requireLogin();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // récupérer et nettoyer les donnes du formulaire
    $name = cleanInput($_POST['name']);
    $description = cleanInput($_POST['description'] ?? '');

    // vérifier nom
    if (empty($name)) {
        $error = "Le nom du classeur est obligatoire.";
    } else {
        // Insérer un classeur dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO folder (name, description, user_id)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$name, $description, $_SESSION['user_id']]);

        // id dernier classeur créé
        $folder_id = $pdo->lastInsertId();

        // Ajout de log à la création du classeur
        addLog($pdo, $_SESSION['user_id'], 'Création classeur', "Classeur créé (ID: $folder_id, Nom: $name)");

        // Redirection liste classeur
        header('Location: ' . SITE_ROOT . 'classeurs/list.php');
        exit;
    }
}

// header après vérification de connexion
include_once __DIR__ . '/../includes/header.php';
?>

    <h1>Créer un classeur</h1>

    <!-- Message si erreur -->
    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire classeur -->
    <form method="post" class="form">
        <div class="form-group">
            <label for="name">Nom du classeur :</label>
            <input type="text" name="name" id="name" placeholder="Ex: Classeur pour les factures clients" required>
        </div>

        <div class="form-group">
            <label for="name">Description :</label>
            <input type="text" name="description" id="description" placeholder="Ex: Classeur factures clients">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"
                    aria-label="Créer un nouveau classeur">
                Créer le classeur
            </button>
            <a href="list.php" class="btn btn-cancel">Annuler</a>
        </div>
    </form>

<?php
// Footer
include_once '../includes/footer.php';
