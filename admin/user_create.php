<?php

// Inclure les fichiers nécessaires
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Titre de la page
$page_title = "Ajouter un utilisateur";

// Accès réservé aux admin
requireAdmin();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = cleanInput($_POST['fname']);
    $name = cleanInput($_POST['name']);
    $birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
    $zipcode = cleanInput($_POST['zipcode'] ?? '');
    $email = cleanInput($_POST['email']);
    $password = $_POST['pwd'];
    $role = $_POST['role'];

    if (empty($fname) || empty($name) || empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hashed_password = hashPassword($password);
            $stmt = $pdo->prepare("INSERT INTO user (fname, name, birthday, zipcode, email, pwd, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fname, $name, $birthday, $zipcode, $email, $hashed_password, $role]);
            $user_id = $pdo->lastInsertId();
            addLog($pdo, $_SESSION['user_id'], 'Création utilisateur', "Utilisateur créé (ID: $user_id, Email: $email)");
            header('Location: ' . SITE_ROOT . 'admin/users.php');
            exit;
        }
    }
}

// header après vérification de connexion
include_once '../includes/header.php';
?>

    <h1>Créer un utilisateur</h1>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form-max-width">
        <div class="form-group">
            <label for="fname">Prénom :</label>
            <input type="text" name="fname" id="fname" required>
        </div>

        <div class="form-group">
            <label for="name">Nom :</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="form-group">
            <label for="birthday">Date de naissance :</label>
            <input type="date" name="birthday" id="birthday">
        </div>

        <div class="form-group">
            <label for="zipcode">Code postal :</label>
            <input type="text" name="zipcode" id="zipcode" placeholder="Ex: 75000">
        </div>

        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="pwd">Mot de passe :</label>
            <input type="password" name="pwd" id="pwd" required>
        </div>

        <div class="form-group">
            <label for="role">Rôle :</label>
            <select name="role" id="role" required>
                <option value="user">Utilisateur</option>
                <option value="admin">Administrateur</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Créer l'utilisateur</button>
            <a href="users.php" class="btn btn-cancel">Annuler</a>
        </div>
    </form>

<?php
// Footer
include_once '../includes/footer.php';
