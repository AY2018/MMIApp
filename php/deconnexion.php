<?php
// Démarre la session
session_start();

// Vérifie si l'utilisateur est connecté
if (isset($_SESSION['pseudo'])) {
    // Détruit la session
    session_destroy();

    // Redirige vers une page de déconnexion ou une autre page appropriée
    header('Location: ./login.php'); // Vous pouvez créer une page "logout.php" pour afficher un message de déconnexion ou rediriger vers une autre page.
    exit();
} else {
    // Si l'utilisateur n'était pas connecté, vous pouvez rediriger vers une page appropriée
    header('Location: ./login.php'); // Redirige vers la page d'accueil ou une autre page par défaut.
    exit();
}
