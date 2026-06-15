<?php

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Titre de la page
$page_title = "Liste des classeurs - eSuggest";

// Vérifier la connexion du user
requireLogin();

// Traitemment de la suppression (POST -> pas de suppression URL)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
 && isset($_POST['action'])
 && $_POST['action'] === 'delete') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = (int) $_POST['id'];

        // vérifier que le classeur appartient au user
        $stmt  = $pdo->prepare("SELECT id FROM folder WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $folder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($folder) {
            // supprimer classeur
            $stmt = $pdo->prepare("DELETE FROM folder WHERE id = ?");
            $stmt->execute([$id]);

            // Ajout de log suppression classeur
            addLog($pdo, $_SESSION['user_id'], 'Suppression classeur', "Classeur supprimé (ID: $id, Nom: {$folder['name']})");

            // renvoi vers la liste classeur (list.php)
            header('Location: ' . SITE_ROOT . 'classeurs/list.php');
            exit;
        } else {
            $error = "Le classeur ne peut pas être supprimé.";
        }
    }
}

// header après vérification de connexion
include_once __DIR__ . '/../includes/header.php';
?>

    <h1 id="folders-title">Liste des classeurs</h1>

    <!-- Message si erreur -->
    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Lien création classeur -->
    <a href="<?= SITE_ROOT ?>classeurs/create.php" class="btn">Créer un classeur</a>

    <!-- Récupération des classeurs depuis la base de données -->
    <?php
    // requête préparée
    $stmt = $pdo->prepare("
        SELECT * FROM folder
        WHERE user_id = ?
        ORDER BY name ASC
    ");

    // exécution requête préparée
    $stmt->execute([$_SESSION['user_id']]);

    // récupération classeurs en array
    $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Affichage classeurs en array
    if (empty($folders)):
    ?>
    <!-- si zéro classeurs -->
    <p>Vous n'avez pas encore de classeurs.</p>

    <?php else: ?>
        <table aria-labelledby="folders-title">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($folders as $folder): ?>
                    <tr>
                        <td><?= htmlspecialchars($folder['name']) ?></td>
                        <td><?= htmlspecialchars($folder['description'] ?? '') ?></td>
                        <td><?= $folder['created_at'] ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="<?= SITE_ROOT ?>classeurs/view.php?id=<?= $folder['id'] ?>" class="btn btn-table">Voir</a>
                                <a href="<?= SITE_ROOT ?>classeurs/edit.php?id=<?= $folder['id'] ?>" class="btn btn-table">Modifier</a>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $folder['id'] ?>">
                                    <button type="submit" class="btn btn-table btn-delete"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')"
                                            aria-label="Supprimer le classeur <?= htmlspecialchars($folder['name']) ?>">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>

<?php
// Footer
include_once '../includes/footer.php';
