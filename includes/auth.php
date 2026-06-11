<?php

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    // vérifie si la variable de session 'user_id' existe
    return isset($_SESSION['user_id']);
}

// Vérification si l'utilisateur est admin
function isAdmin() {
    // vérifie si l'utilisateur est connecté ET si son rôle est 'admin'
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// Rediriger si l'utilisateur n'est pas connecté
function requireLogin() {
    // si user non connecté, redirigé vers login.php
    if (!isLoggedIn()) {
        header('Location: ' . SITE_ROOT . 'login.php');
        exit;
    }
}

// Redirige l'utilisateur si pas admin
function requireAdmin() {
    // si user pas admin, redirigé vers index.php
    if (!isAdmin()) {
        header('Location: ' . SITE_ROOT . 'index.php');
        exit;
    }
}

// Connecter un utilisateur
function login($pdo, $email, $password) {
    // récupérer l'utilisateur via son email
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    // récupère le user sous forme de tableau associatif
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // vérifier si l'utilisateur existe te si le mot de passe est correct
    if ($user && verifyPassword($password, $user['pwd'])) {
        // stocker les informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_fname'] = $user['fname'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // ajouter un log à la connexion
        addLog($pdo, $user['id'], 'Connexion');

        // renvoyer true si la connexion est réussie
        return true;
    }

    // renvoyer false si la connexion échoue
    return false;
}

// Déconnecter un utilisateur
function logout($pdo) {
    // ajouter un log pour la déconnexion
    addLog($pdo, $_SESSION['user_id'], 'Déconnexion');

    // détruire la session
    session_unset();
    session_destroy();

    // rediriger vers la page de connexion
    header('Location ' . SITE_ROOT . 'login.php');
    exit;
}
