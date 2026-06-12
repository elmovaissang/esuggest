<?php

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Titre de la page
$page_title = "Gestion des utilisateurs";

// Accès réservé aux admin
requireAdmin();

// Traitement de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = (int) $_POST['id'];
        // Empêcher la suppression de soi-même
        if ($id !== $_SESSION['user_id']) {
            if (deleteUser($pdo, $id, $_SESSION['user_id'])) {
                header('Location: users.php');
                exit;
            } else {
                $error = "L'utilisateur ne peut pas être supprimé.";
            }
        } else {
            $error = "Vous ne pouvez pas vous supprimer vous-même.";
        }
    }
}

// header après vérification de connexion
include_once __DIR__ . '/../includes/header.php';
?>

    <h1>Gestion des utilisateurs</h1>

    <!-- Messages d'erreur/succès -->
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Lien pour créer un nouvel utilisateur -->
    <a href="user_create.php" class="btn">Créer un utilisateur</a>

    <!-- Récupération des utilisateurs -->
    <?php
    $stmt = $pdo->query("SELECT * FROM user ORDER BY name ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (empty($users)): ?>
        <p>Aucun utilisateur trouvé.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['fname']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-table">Modifier</a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-table btn-delete"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php
// Footer
include_once '../includes/footer.php';
