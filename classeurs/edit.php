<?php

// Inclure les fichiers nécessaires
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Titre de la page
$page_title = "Modifier le classeur";

// Vérifier la connexion du user
requireLogin();

// Variables d'erreurs
$error = null;
$current_folder = null;

// Récupérer l'ID du classeur depuis l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // message d'erreur id manquant
    $error = "L'ID du classeur est invalide.";
} else {
    $id = (int) $_GET['id'];

    // Récupération du classeur depuis la base de données
    $stmt = $pdo->prepare("
        SELECT * FROM folder
        WHERE id = ? AND user_id = ?
    ");
        $stmt->execute([$id, $_SESSION['user_id']]);
    $current_folder = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification + message d'erreur si pas de classeur
    if (!$current_folder) {
       $error = "Le classeur n'existe pas.";
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $name = cleanInput($_POST['name']);
    $description = cleanInput($_POST['description'] ?? '');

    // vérifier nom
    if (empty($name)) {
        $error = "Le nom du classeur est obligatoire.";
    } else {
        // classeur màj dans la base de données
        $stmt = $pdo->prepare("
            UPDATE folder
            SET name = ?, description = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $description, $id]);

        // Ajout de log pour la modification
        addLog($pdo, $_SESSION['user_id'], 'Modification classeur', "Classeur modifié (ID: $id, Nom: $name)");

        // renvoi vers détails après modification
        header('Location: ' . SITE_ROOT . "classeurs/view.php?id=$id");
        exit;
    }
}

// header après vérification de connexion
include_once '../includes/header.php';
?>

    <?php if ($error): ?>
    <!-- Message si erreur modification -->
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <a href="list.php" class="btn">Retour à la liste des classeurs</a>
    <?php else: ?>
        <h1>Modifier le classeur <?= htmlspecialchars($current_folder['name']) ?></h1>
        <form method="post" class="form">
            <div class="form-group">
                <label for="name">Nom du classeur :</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($current_folder['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <input type="text" name="description" id="description" value="<?= htmlspecialchars($current_folder['description'] ?? '') ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Enregistrer</button>
                <a href="<?= SITE_ROOT ?>classeurs/view.php?id=<?= $current_folder['id'] ?>" class="btn btn-cancel">Annuler</a>
            </div>
        </form>
    <?php endif; ?>

<?php
// Footer
include_once '../includes/footer.php';
