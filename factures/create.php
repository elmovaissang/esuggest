<?php

// Inclure les fichiers nécessaires
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Titre de la page
$page_title = "Créer une facture";

// Vérifier la connexion du user
requireLogin();

// Récupérer la liste des clients pour le formulaire
$stmt = $pdo->query("SELECT * FROM client");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des classeurs pour le form
$stmt = $pdo->prepare("SELECT * FROM folder WHERE user_id = ? ORDER BY name ASC");
$stmt->execute([$_SESSION['user_id']]);
$folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*
    // test débogage
    var_dump($_POST['client_id']);
    exit;
    */
    // récupérer et nettoyer les donnes du formulaire
    $emission_date = $_POST['emission_date'];
    $amount_ht = (float) str_replace(',', '.', $_POST['amount_ht']);
    $tva = (float) str_replace(',', '.', $_POST['tva']);
    $amount_ttc = $amount_ht + $tva;
    $status = 'draft'; // brouillon par défaut
    $format = $_POST['format'];
    $client_id = (int) $_POST['client_id'];
    $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int) $_POST['folder_id'] : null;

    // Générer num facture unique
    $number = generateNumber($pdo, 'FACT');

    // Insérer une facture dans la base de données
    $stmt = $pdo->prepare("
        INSERT INTO invoice
        (number, emission_date, amount_ht, tva, amount_ttc, status, format, issuer_id, issuer_type, client_id, folder_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $number, $emission_date, $amount_ht, $tva, $amount_ttc,
        $status, $format, $_SESSION['user_id'], 'user', $client_id, $folder_id
    ]);

    // Ajout de log à la création de la facture
    $folder_name = 'aucun';
    // trouver le nom du folder pour le log
    if ($folder_id) {
        foreach ($folders as $folder) {
            if ($folder['id'] == $folder_id) {
                $folder_name = $folder['name'];
                break;
            }
        }
    }
    addLog($pdo, $_SESSION['user_id'], 'Création facture', "Facture n°$number créée (Classeur: $folder_name)");

    // Redirection vers la liste
    header('Location: ' . SITE_ROOT . 'factures/list.php');
    exit;
}

// header après vérification de connexion
include_once '../includes/header.php';
?>

    <h1>Créer une facture</h1>

    <!-- Formulaire -->
    <form method="post" class="form">
        <div class="form-group">
            <label for="emission_date">Date d'émission :</label>
            <input type="date" name="emission_date" id="emission_date" required>
        </div>

        <div class="form-group">
            <label for="client_id">Client :</label>
            <select name="client_id" id="client_id" required>
                <option value="">-- Sélectionner un client --</option>

                <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['id'] ?>">
                        <?= htmlspecialchars($client['name'] . ' ' . ($client['fname'] ?? '')) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="amount_ht">Montant HT (€) :</label>
            <input type="text" name="amount_ht" id="amount_ht" placeholder="100.00" required>
        </div>

        <div class="form-group">
            <label for="tva">TVA (€) :</label>
            <input type="text" name="tva" id="tva" placeholder="20.00" required>
        </div>

        <div class="form-group">
            <label for="format">Format :</label>
            <select name="format" id="format">
                <option value="UBL">UBL</option>
                <option value="CII">CII</option>
                <option value="mixed">UBL + PDF</option>
            </select>
        </div>

        <div class="form-group">
            <label for="folder_id">Classeur :</label>
            <select name="folder_id" id="folder_id">
                <option value="">-- Aucun classeur --</option>
                <?php foreach ($folders as $folder): ?>
                    <option value="<?= $folder['id'] ?>"><?= htmlspecialchars($folder['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Créer la facture</button>
            <a href="list.php" class="btn btn-cancel">Annuler</a>
        </div>
    </form>

<?php
// Footer
include_once '../includes/footer.php';
