<?php

// Inclure les fichiers nécessaires
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Titre de la page
$page_title = "Modifier l'utilisateur";

// Accès réservé aux admin
requireAdmin();

// Variables d'erreurs
$error = null;
$current_folder = null;

// Récupérer l'ID du user depuis l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // message d'erreur id manquant
    $error = "L'ID de l'utilisateur est invalide.";
} else {
    $id = (int) $_GET['id'];
    // Récupération du user depuis la base de données
    $stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification + message d'erreur si pas de user
    if (!$user) {
        $error = "L'utilisateur n'existe pas...";
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $fname = cleanInput($_POST['fname']);
    $name = cleanInput($_POST['name']);
    $birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
    $zipcode = cleanInput($_POST['zipcode'] ?? '');
    $email = cleanInput($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['pwd'] ?? '';

    // vérifer le nom
    if (empty($fname) || empty($name) || empty($email)) {
        $error = "Les champs Prénom, Nom et Email sont obligatoires.";
    } else {
        // vérifier si l'email est déjà utilisé par un autre user
        $stmt = $pdo->prepare("SELECT id FROM user WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "Cet email est déjà utilisé par un autre utilisateur.";
        } else {
            // user màj dans la base de données
            $stmt = $pdo->prepare("UPDATE user SET fname = ?, name = ?, birthday = ?, zipcode = ?, email = ?, role = ? WHERE id = ?");
            $stmt->execute([$fname, $name, $birthday, $zipcode, $email, $role, $id]);

            // mdp màj si changé
            if (!empty($password)) {
                $hashed_password = hashPassword($password);
                $stmt = $pdo->prepare("UPDATE user SET fname = ?, name = ?, birthday = ?, zipcode = ?, email = ?, pwd = ?, role = ? WHERE id = ?");
                $stmt->execute([$fname, $name, $birthday, $zipcode, $email, $hashed_password, $role, $id]);
            }

            // Ajout de log pour la modification
            addLog($pdo, $_SESSION['user_id'], 'Modification utilisateur', "Utilisateur modifié (ID: $id, Email: $email)");

            // renvoi vers la liste après modification
            header('Location: ' . SITE_ROOT . 'admin/users.php');
            exit;
        }
    }
}

// header après vérification de connexion
include_once '../includes/header.php';
?>

    <h1>Modifier l'utilisateur <?= htmlspecialchars($user['fname'] . ' ' . $user['name']) ?></h1>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form-max-width">
        <div class="form-group">
            <label for="fname">Prénom :</label>
            <input type="text" name="fname" id="fname" value="<?= htmlspecialchars($user['fname']) ?>" required>
        </div>

        <div class="form-group">
            <label for="name">Nom :</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="birthday">Date de naissance :</label>
            <input type="date" name="birthday" id="birthday" value="<?= $user['birthday'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label for="zipcode">Code postal :</label>
            <input type="text" name="zipcode" id="zipcode" placeholder="Ex: 75000" value="<?= htmlspecialchars($user['zipcode'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="pwd">Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" name="pwd" id="pwd">
        </div>

        <div class="form-group">
            <label for="role">Rôle :</label>
            <select name="role" id="role" required>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="users.php" class="btn btn-cancel">Annuler</a>
        </div>
    </form>

<?
// Footer
include_once '../includes/footer.php';
