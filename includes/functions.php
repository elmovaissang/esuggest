<?php

require_once 'config.php';

// Hacher un mot de passe
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Vérifier un mot de passe
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Ajouter un log dans la base de données
function addLog($pdo, $user_id, $action, $details = '') {
    // requête préparée
    $stmt = $pdo->prepare("INSERT INTO log (user_id, action, details) VALUES (?, ?, ?)");
    // exécution de la requête
    $stmt->execute([$user_id, $action, $details]);
}

// Générer un numéro de facture/devis numérique
function generateNumber($pdo, $prefix = 'FACT') {
    // récupérer le dernier numéro utilisé pour le préfixe
    $stmt = $pdo->prepare("SELECT MAX(number) as max_nb FROM invoice WHERE number LIKE ?");
    $stmt->execute(["$prefix%"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // si aucun numéro, commencer à 000
    $max_nb = $result['max_nb'] ?? $prefix . '-000';

    // extraire le suffixe numérique
    $parts = explode('-', $max_nb);
    $suffix = end($parts);

    // incrémenter le suffix et formater sur 3 chiffres
    $new_suffix = str_pad((int)$suffix + 1, 3, '0', STR_PAD_LEFT);

    // renvoyer le nouveau numéro
    return $prefix . '-' . $new_suffix;
}

// Nettoyer les entrées utilisateur
function cleanInput($data) {
    $data = trim($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Tableau de traduction
function getStatusLabel($status) {
    $statusLabels = [
        'draft' => 'Brouillon',
        'validated' => 'Validée',
        'sent' => 'Envoyée',
        'paid' => 'Payée'
    ];
    return $statusLabels[$status] ?? $status;
}

// Suppression facture + Log
function deleteInvoice($pdo, $invoice_id, $user_id) {
    // récupérer nb facture pour le log
    $stmt = $pdo->prepare("SELECT number FROM invoice WHERE id = ?");
    $stmt->execute([$invoice_id]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($invoice) {
        // suppression
        $stmt = $pdo->prepare("DELETE FROM invoice WHERE id = ?");
        $stmt->execute([$invoice_id]);

        // log
        addLog($pdo, $user_id, 'Suppression facture', "Facture n°{$invoice['number']} supprimée");
        return true;
    }
    return false;
}

// Suppression classeur + Log
function deleteFolder($pdo, $folder_id, $user_id) {
    $stmt = $pdo->prepare("SELECT name FROM folder WHERE id = ?");
    $stmt->execute([$folder_id]);
    $folder = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($folder) {
        $stmt = $pdo->prepare("DELETE FROM  folder WHERE id = ?");
        $stmt->execute([$folder_id]);
        addLog($pdo, $user_id, 'Suppression classeur', "Classeur {$folder['name']} supprimé");
        return true;
    }
    return false;
}

// Suppression user + Log
function deleteUser($pdo, $user_id, $admin_id) {
    $stmt = $pdo->prepare("SELECT fname, name FROM user WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
        $stmt->execute([$user_id]);
        addLog($pdo, $admin_id, 'Suppression utilisateur', "Utilisateur {$user['fname']} {$user['name']} supprimé");
        return true;
    }
    return false;
}
