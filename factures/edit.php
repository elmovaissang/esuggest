<?php

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Titre de la page
$page_title = "Modifier la facture - eSuggest";

// Vérifier la connexion du user
requireLogin();

// Variables d'erreurs
$error = null;
$invoice = null;

// Récupérer l'ID de la facture depuis l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // message d'erreur id manquant
    $error = "L'ID de la facture est invalide.";
} else {
    $id = (int) $_GET['id'];

    // Récupération de la facture depuis la base de données
    $stmt = $pdo->prepare("
        SELECT i.*, c.id AS client_id, c.name AS client_name, f.id AS folder_id, f.name AS folder_name
        FROM invoice i
        JOIN client c ON i.client_id = c.id
        LEFT JOIN folder f ON i.folder_id = f.id
        WHERE i.id = ? AND (i.issuer_id = ? AND i.issuer_type = 'user')
    ");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si la facture existe
    if (!$invoice) {
        $error = "La facture demandée n'existe pas...";
    }
}

// Liste des clients pour le formulaire
$stmt = $pdo->query("SELECT * FROM client");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des classeurs pour le form
$stmt = $pdo->prepare("SELECT * FROM folder WHERE user_id = ? ORDER BY name ASC");
$stmt->execute([$_SESSION['user_id']]);
$folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    // récupérer et nettoyer les données du formulaire
    $emission_date = $_POST['emission_date'];
    $amount_ht = (float) str_replace(',', '.', $_POST['amount_ht']);
    $tva = (float) str_replace(',', '.', $_POST['tva']);
    $amount_ttc = $amount_ht + $tva;
    $status = $_POST['status'];
    $format = $_POST['format'];
    $client_id = (int) $_POST['client_id'];
    $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int) $_POST['folder_id'] : null;

    // mise à jour de la base de données
    $stmt = $pdo->prepare("
        UPDATE invoice
        SET emission_date = ?, amount_ht = ?, tva = ?, amount_ttc = ?,
            status = ?, format = ?, client_id = ?, folder_id = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $emission_date, $amount_ht, $tva, $amount_ttc,
        $status, $format, $client_id, $folder_id, $id
    ]);

    // log pour la modification
    $folder_name = 'aucun';
    // trouver le nom du classeur pour le log
    if ($folder_id) {
        foreach ($folders as $folder) {
            if ($folder['id'] == $folder_id) {
                $folder_name = $folder['name'];
                break;
            }
        }
    }
    addLog($pdo, $_SESSION['user_id'], 'Modification facture', "Facture n°{$invoice['number']} modifiée (Classeur: $folder_name)");

    // redirection après modification
    header('Location: ' . SITE_ROOT . 'factures/list.php');
    exit;
}

// header après vérification de connexion
include_once __DIR__ . '/../includes/header.php';
?>

    <?php if ($error): ?>
    <!-- Message si erreur modification -->
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <a href="<?= SITE_ROOT ?>factures/list.php" class="btn">Retour à la liste des factures</a>
    <?php else: ?>
    <!-- Formulaire de modification -->
        <h1>Modifier la facture n°<?= htmlspecialchars($invoice['number']) ?></h1>

        <form method="post" class="form">
            <div class="form-group">
                <label for="emission_date">Date d'émission :</label>
                <input type="date" name="emission_date" id="emission_date" value="<?= $invoice['emission_date'] ?>" required>
            </div>

            <div class="form-group">
                <label for="client_id">Client :</label>
                <select name="client_id" id="client_id" required>
                    <option value="">-- Sélectionner un client --</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>" <?= $client['id'] == $invoice['client_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client['name'] . ' ' . ($client['fname'] ?? '')) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="amount_ht">Montant HT (€) :</label>
                <input type="text" name="amount_ht" id="amount_ht" value="<?= str_replace('.', ',', $invoice['amount_ht']) ?>" required>
            </div>

            <div class="form-group">
                <label for="tva">TVA (€) :</label>
                <input type="text" name="tva" id="tva" value="<?= str_replace('.', ',', $invoice['tva']) ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Statut :</label>
                <select name="status" id="status" required>
                    <option value="draft" <?= $invoice['status'] === 'draft' ? 'selected' : '' ?>><?= htmlspecialchars(getStatusLabel('draft')) ?></option>
                    <option value="validated" <?= $invoice['status'] === 'validated' ? 'selected' : '' ?>><?= htmlspecialchars(getStatusLabel('validated')) ?></option>
                    <option value="sent" <?= $invoice['status'] === 'sent' ? 'selected' : '' ?>><?= htmlspecialchars(getStatusLabel('sent')) ?></option>
                    <option value="paid" <?= $invoice['status'] === 'paid' ? 'selected' : '' ?>><?= htmlspecialchars(getStatusLabel('paid')) ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="format">Format :</label>
                <select name="format" id="format" required>
                    <option value="UBL" <?= $invoice['format'] === 'UBL' ? 'selected' : '' ?>>UBL</option>
                    <option value="CII" <?= $invoice['format'] === 'CII' ? 'selected' : '' ?>>CII</option>
                    <option value="mixed" <?= $invoice['format'] === 'mixed' ? 'selected' : '' ?>>UBL + PDF</option>
                </select>
            </div>

            <div class="form-group">
                <label for="folder_id">Classeur :</label>
                <select name="folder_id" id="folder_id">
                    <option value="">-- Aucun classeur --</option>
                    <?php foreach ($folders as $folder): ?>
                        <!-- classeur présélectionné -->
                        <option value="<?= $folder['id'] ?>" <?= $folder['id'] == $invoice['folder_id'] ? 'selected' : '' ?>><?= htmlspecialchars($folder['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"
                        aria-label="Enregistrer les modifications de la facture n°<?= htmlspecialchars($invoice['number']) ?>">
                    Enregistrer
                </button>
                <a href="<?= SITE_ROOT ?>factures/list.php" class="btn btn-cancel">Annuler</a>
            </div>
        </form>
    <?php endif ?>
    
<?php
// Footer
include_once '../includes/footer.php';

