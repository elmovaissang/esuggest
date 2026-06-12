<?php

// Inclure les fichiers nécessaires
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Appel de la fonction pour déconnecter le user
logout($pdo);
