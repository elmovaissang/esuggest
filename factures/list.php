<?php

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Titre de la page
$page_title = "Liste des factures";

// Vérifier la connexion du user
requireLogin();

// Traitemment de la suppression (POST -> pas de suppression URL)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
 && isset($_POST['action'])
 && $_POST['action'] === 'delete') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = (int) $_POST['id'];
        // si supprimé -> renvoyer sur lidt.php màj
        if (deleteInvoice($pdo, $id, $_SESSION['user_id'])) {
            header('Location: ' . SITE_ROOT . 'factures/list.php');
            exit;
        } else {
            // si échec -> message d'erreur
            $error = "La facture ne peut pas être supprimée.";
        }
    }
}

// header après vérification de connexion
include_once __DIR__ . '/../includes/header.php';
?>

    <h1>Liste des factures</h1>

    <!-- Message si erreur -->
    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Lien création facture -->
    <a href="<?= SITE_ROOT ?>factures/create.php" class="btn">Créer une facture</a>

    <!-- Récupération des factures depuis la base de données -->
    <?php
    // requête préparée
    $stmt = $pdo->prepare("
        SELECT i.*, c.name AS client_name, f.name AS folder_name
        FROM invoice i
        JOIN client c ON i.client_id = c.id
        LEFT JOIN folder f ON i.folder_id = f.id
        WHERE i.issuer_id = ? AND i.issuer_type = 'user'
        ORDER BY i.emission_date DESC
    ");

    // exécution requête préparée
    $stmt->execute([$_SESSION['user_id']]);

    // récupération factures en array
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Affichage factures en array
    if (empty($invoices)):
    ?>
    <!-- si zéro factures -->
    <p>Vous n'avez pas encore de factures.</p>

    <?php else: ?>
    <!-- factures -> tableau -->
    <table>
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Client</th>
                <th>Classeur</th>
                <th>Date</th>
                <th>Montant</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <!-- données de chaque facture -->
                    <td><?= $invoice['number'] ?></td>
                    <td><?= htmlspecialchars($invoice['client_name']) ?></td>
                    <td><?= htmlspecialchars($invoice['folder_name'] ?? 'Aucun') ?></td>
                    <td><?= $invoice['emission_date'] ?></td>
                    <td><?= $invoice['amount_ttc'] ?></td>
                    <td><?= htmlspecialchars(getStatusLabel($invoice['status'])) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="<?= SITE_ROOT ?>factures/view.php?id=<?= $invoice['id'] ?>" class="btn btn-table">Voir</a>
                            <a href="<?= SITE_ROOT ?>factures/edit.php?id=<?= $invoice['id'] ?>" class="btn btn-table">Modifier</a>
                            <form method="post" class="inline-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $invoice['id'] ?>">
                                <button type="submit" class="btn btn-table btn-delete"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

<?php
// Footer
include_once '../includes/footer.php';
