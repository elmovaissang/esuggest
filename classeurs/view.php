<?php

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Titre de la page
$page_title = "Classeur - eSuggest";

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
    // var_dump($folder);
    // exit;

    // Vérification + message d'erreur si pas de classeur
    if (!$current_folder) {
       $error = "Le classeur n'existe pas...";
    }
}

// header après vérification de connexion
include_once __DIR__ . '/../includes/header.php';
?>

    <?php if ($error): ?>
    <!-- Message si erreur no folder -->
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <a href="list.php" class="btn">Retour à la liste des classeurs</a>
    <?php else: ?>
        <!-- détails classeur -->
        <h1>Classeur : <?= htmlspecialchars($current_folder['name']) ?></h1>
        <div class="card">
            <p><strong>Description : </strong><?= htmlspecialchars($current_folder['description']) ?></p>
            <p><strong>Date de création : </strong><?= $current_folder['created_at'] ?></p>
        </div>

        <!-- factures dans le classeur -->
        <h2>Factures</h2>
        <?php
        // Récupérer les factures associées au classeur
        $stmt = $pdo->prepare("
            SELECT i.*, c.name AS client_name
            FROM invoice i
            JOIN client c ON i.client_id = c.id
            WHERE i.folder_id = ? AND i.issuer_id = ? AND i.issuer_type = 'user'
            ORDER BY i.emission_date DESC
        ");

        $stmt->execute([$id, $_SESSION['user_id']]);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (empty($invoices)): ?>
            <p>Aucune facture dans ce classeur.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Montant TTC</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?= htmlspecialchars($invoice['number']) ?></td>
                            <td><?= htmlspecialchars($invoice['client_name']) ?></td>
                            <td><?= htmlspecialchars($invoice['emission_date']) ?></td>
                            <td><?= htmlspecialchars($invoice['amount_ttc']) ?></td>
                            <td><?= htmlspecialchars(getStatusLabel($invoice['status'])) ?></td>
                            <td><a href="../factures/view.php?id=<?= $invoice['id'] ?>" class="btn">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="folder-actions">
            <a href="<?= SITE_ROOT ?>classeurs/list.php" class="btn">Retour à la liste</a>
            <a href="<?= SITE_ROOT ?>classeurs/edit.php?id=<?= $current_folder['id'] ?>" class="btn">Modifier le classeur</a>
        </div>
    <?php endif ?>

<?php
// Footer
include_once '../includes/footer.php';
