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
        if (!empty($email)) {
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
                            $sql3 = "INSERT INTO `etudiants` (`pseudo`, `email`, `groupe`, `motDePasse`, `admin`) VALUES ('$pseudo', '$email', '$groupe', '$mdp', 0)";
                            $result = mysqli_query($link, $sql3);
                            echo "Inscription réussie";
                            $_SESSION['pseudo'] = $pseudo;
                            $_SESSION['email'] = $email;
                            $_SESSION['groupe'] = $groupe;
                            $_SESSION['isConnected'] = true;
                            // $_SESSION['admin'] = false;
                            if ($result) {
                                echo "Inscription réussie";
                                header('Location: ../index.php');
                            } else {
                                echo "Erreur lors de l'inscription";
                            }
                        } else {
                            echo "Cet email est déjà utilisé";
                        }
                    } else {
                        echo "Ce pseudo est déjà utilisé";
                    }
                } else {
                    echo "Vous n'avez pas rentré de mot de passe ou le mot de passe n'est pas valide";
                }
            } else {
                echo "Vous n'avez pas rentré de groupe ou le groupe n'est pas valide";
            }
        } else {
            echo "Vous n'avez pas rentré d'email ou l'email n'est pas valide";
        }
    } else {
        echo "Vous n'avez pas rentré de pseudo ou le pseudo n'est pas valide";
    }
} else if ($_POST["connexionSubmit"]) {
    // Partie gestion des données du formulaire de connexion
    // Récupération des variables nécessaires à la connexion
    $pseudo = mysqli_real_escape_string($link, $_POST['connexionPseudo']);
    $motDePasse = mysqli_real_escape_string($link, $_POST['connexionMDP']);
    // hash du mot de passe pour la sécurité
    $mdp = password_hash($motDePasse, PASSWORD_DEFAULT);

    // Connexion en tant qu'admin
    // if ($pseudo == "admin" and $motDePasse == "admin") {
    //     $_SESSION['pseudo'] = "Admin";
    //     $_SESSION['isConnected'] = true;
    //     $_SESSION['admin'] = true;
    //     header('Location: ../index.php');
    // } else 
    if (!empty($pseudo)) {
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
                    header('Location: ../index.php');
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
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/login.css">
    <title>MMI Devoirs - Log In</title>
</head>

<body>
    <header>
        <figure>
            <img src="../img/Group 3.png" alt="">
        </figure>
    </header>

    <main>

        <form action="../php/login.php" method="post" id="formInscription">
            <h1>Inscription</h1>

            <fieldset>
                <div>
                    <i class="fa-solid fa-user"></i>
                    <input required minlength="3" name="inscriptionPseudo" type="text" placeholder="Pseudo">
                </div>

                <div>
                    <i class="fa-solid fa-envelope"></i>
                    <input required name="inscriptionEmail" type="email" placeholder="Email">
                </div>

                <div>
                    <i class="fa-solid fa-book-bookmark"></i>
                    <select name="inscriptionGroupe" id="">
                        <option value="none" disabled selected>Groupe</option>
                        <option value="a1">A1</option>
                        <option value="a2">A2</option>
                        <option value="b1">B1</option>
                        <option value="b2">B2</option>
                    </select>
                </div>


                <div>
                    <i class="fa-solid fa-lock"></i>
                    <input required minlength="6" name="inscriptionMDP" type="password" placeholder="Mot de passe">
                </div>
            </fieldset>

            <fieldset>
                <input required name="inscriptionSubmit" type="submit" value="S'inscrire">
                <p id="switchToLogin">Se connecter</p>
            </fieldset>

        </form>

        <form action="../php/login.php" method="post" id="formConnexion">
            <h1>Connexion</h1>

            <fieldset>
                <div>
                    <i class="fa-solid fa-user"></i>
                    <input required name="connexionPseudo" type="text" placeholder="Pseudo">
                </div>

                <div>
                    <i class="fa-solid fa-lock"></i>
                    <input required name="connexionMDP" type="password" placeholder="Mot de passe">
                </div>
            </fieldset>

            <fieldset>
                <input required name="connexionSubmit" type="submit" value="Se connecter">
                <p id="switchToSignup">S'inscrire</p>
            </fieldset>

        </form>

    </main>


    <script src="../js/login.js"></script>
</body>

</html>