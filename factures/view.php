<?php

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Titre de la page
$page_title = "Détails de la facture - eSuggest";

// Vérifier la connexion du user
requireLogin();

// Variables d'erreurs
$error = null;
$invoice = null;

// Récupérer l'ID de la facture depuis l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // message d'erreur id manquant
    $error = "L'identifiant de la facture est invalide.";
} else {
    $id = (int) $_GET['id'];

    // Récupération de la facture depuis la base de données
    $stmt = $pdo->prepare("
        SELECT i.*, c.name AS client_name, c.email AS client_email, c.address AS client_address, f.name AS folder_name
        FROM invoice i
        JOIN client c ON i.client_id = c.id
        LEFT JOIN folder f ON i.folder_id = f.id
        WHERE i.id = ? AND (i.issuer_id = ? AND i.issuer_type = 'user')
    ");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification + message d'erreur si pas de facture
    if (!$invoice) {
        $error = "La facture n'existe pas...";
    }
}

// header après vérification de connexion
include_once __DIR__ . '/../includes/header.php';
?>

    <?php if ($error): ?>
    <!-- Message si erreur no invoice -->
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <a href="list.php" class="btn">Retour à la liste des factures</a>
    <?php else: ?>
    <!-- Détails si ok -->
        <h1>Facture N°<?= htmlspecialchars($invoice['number']) ?></h1>
        <div class="card">
            <h2>Information du client</h2>
            <p><strong>Nom : </strong><?= htmlspecialchars($invoice['client_name']) ?></p>
            <p><strong>Email : </strong><?= htmlspecialchars($invoice['client_email']) ?></p>
            <p><strong>Adresse : </strong><?= htmlspecialchars($invoice['client_address']) ?></p>

        <h2>Détails de la facture</h2>
            <p><strong>Date d'émission : </strong><?= htmlspecialchars($invoice['emission_date']) ?></p>
            <p><strong>Montant HT : </strong><?= htmlspecialchars($invoice['amount_ht']) ?></p>
            <p><strong>TVA : </strong><?= htmlspecialchars($invoice['tva']) ?></p>
            <p><strong>Montant TTC : </strong><?= htmlspecialchars($invoice['amount_ttc']) ?></p>
            <p><strong>Statut : </strong><?= htmlspecialchars(getStatusLabel($invoice['status'])) ?></p>
            <p><strong>Format : </strong><?= htmlspecialchars($invoice['format']) ?></p>
            <p><strong>Classeur : </strong><?= htmlspecialchars($invoice['folder_name'] ?? 'Aucun') ?></p>

            <div class="form-actions">
                <a href="<?= SITE_ROOT ?>factures/list.php" class="btn">Retour à la liste des factures</a>
                <a href="<?= SITE_ROOT ?>factures/edit.php?id=<?= $invoice['id'] ?>" class="btn">Modifier</a>
            </div>

            <!-- Dans factures/view.php, après les détails de la facture -->
            <!-- <div class="download-actions">
                <h3>Télécharger la facture</h3>
                <div class="download-buttons">
                    <a href="download.php?id=<?= $invoice['id'] ?>&format=pdf" class="btn">PDF</a>
                    <a href="download.php?id=<?= $invoice['id'] ?>&format=ubl" class="btn">UBL</a>
                    <a href="download.php?id=<?= $invoice['id'] ?>&format=cii" class="btn">CII</a>
                </div>
            </div> -->
        </div>
    <?php endif ?>
    
<?php
// Footer
include_once '../includes/footer.php';
