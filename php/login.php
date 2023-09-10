<?php
session_start();
// Connexiojn à la base de données
$link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");
// Partie gestion des données du formulaire d'inscription

// Récupération des variables nécessaires à l'inscription
$pseudo = mysqli_real_escape_string($link, $_POST['inscriptionPseudo']);
$email = mysqli_real_escape_string($link, $_POST['inscriptionEmail']);
$groupe = mysqli_real_escape_string($link, $_POST['inscriptionGroupe']);
$motDePasse = mysqli_real_escape_string($link, $_POST['inscriptionMDP']);
// hash du mot de passe pour la sécurité
$mdp = password_hash($motDePasse, PASSWORD_DEFAULT);

if ($_POST["inscriptionSubmit"]) {
    if (!empty($pseudo)) {
        if (!empty($email) and filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (!empty($groupe)) {
                if (!empty($mdp)) {
                    // Vérification de l'unicité du pseudo
                    $sql = "SELECT * FROM `etudiants` WHERE `pseudo` = '$pseudo'";
                    $sql2 = "SELECT * FROM `etudiants` WHERE `email` = '$email'";

                    $result = mysqli_query($link, $sql);
                    $result2 = mysqli_query($link, $sql2);

                    if (mysqli_num_rows($result) === 0) {
                        if (mysqli_num_rows($result2) === 0) {
                            // Insertion des données dans la base de données
                            $sql3 = "INSERT INTO `etudiants` (`pseudo`, `email`, `groupe`, `motDePasse`) VALUES ('$pseudo', '$email', '$groupe', '$mdp')";
                            $result = mysqli_query($link, $sql3);
                            // echo "Inscription réussie";
                            $_SESSION['pseudo'] = $pseudo;
                            $_SESSION['email'] = $email;
                            $_SESSION['groupe'] = $groupe;
                            $_SESSION['isConnected'] = true;
                            $_SESSION['admin'] = false;
                            header('Location: ../index.html');
                        } else {
                            // echo "Cet email est déjà utilisé";
                        }
                    } else {
                        // echo "Ce pseudo est déjà utilisé";
                    }
                } else {
                    // echo "Vous n'avez pas rentré de mot de passe ou le mot de passe n'est pas valide";
                }
            } else {
                // echo "Vous n'avez pas rentré de groupe ou le groupe n'est pas valide";
            }
        } else {
            // echo "Vous n'avez pas rentré d'email ou l'email n'est pas valide";
        }
    } else {
        // echo "Vous n'avez pas rentré de pseudo ou le pseudo n'est pas valide";
    }
} else if ($_POST["connexionSubmit"]) {
    // Partie gestion des données du formulaire de connexion
    // Récupération des variables nécessaires à la connexion
    $pseudo = mysqli_real_escape_string($link, $_POST['connexionPseudo']);
    $motDePasse = mysqli_real_escape_string($link, $_POST['connexionMDP']);
    // hash du mot de passe pour la sécurité
    $mdp = password_hash($motDePasse, PASSWORD_DEFAULT);

    // Connexion en tant qu'admin
    if ($pseudo == "admin" and $motDePasse == "admin") {
        $_SESSION['pseudo'] = "Admin";
        $_SESSION['isConnected'] = true;
        $_SESSION['admin'] = true;
        header('Location: ../index.html');
    } else if (!empty($pseudo)) {
        if (!empty($motDePasse)) {
            // Vérification de l'existence du pseudo
            $sql = "SELECT * FROM `etudiants` WHERE `pseudo` = '$pseudo'";
            $result = mysqli_query($link, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $hashedPassword = $row['motDePasse'];

                if (password_verify($motDePasse, $hashedPassword)) {
                    // Mot de passe correct, connectez l'utilisateur
                    $_SESSION['pseudo'] = $row['pseudo'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['groupe'] = $row['groupe'];
                    $_SESSION['isConnected'] = true;
                    header('Location: ../index.html');
                } else {
                    // Mot de passe incorrect
                    // echo "Mot de passe incorrect";
                }
            } else {
                // Utilisateur non trouvé
                // echo "Ce pseudo n'existe pas";
            }
        } else {
            // echo "Veuillez entrer un mot de passe";
        }
    } else {
        // echo "Veuillez entrer un pseudo";
    }
} else {
    // echo "Veuillez remplir tous les champs";
}
