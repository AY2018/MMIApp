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
$confirmMotDePasse = mysqli_real_escape_string($link, $_POST['confirmInscriptionMDP']);
// hash du mot de passe pour la sécurité
$mdp = password_hash($motDePasse, PASSWORD_DEFAULT);

if ($_POST["inscriptionSubmit"]) {
    if (!empty($pseudo)) {
        if (!empty($email)) {
            if (!empty($groupe)) {
                if (!empty($mdp)) {
                    if (!empty($confirmMotDePasse)) {
                        if ($motDePasse === $confirmMotDePasse) {
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
                                    $_SESSION['pseudo'] = $pseudo;
                                    $_SESSION['email'] = $email;
                                    $_SESSION['groupe'] = $groupe;
                                    $_SESSION['isConnected'] = true;
                                    if ($result) {
                                        header('Location: ../index.php');
                                    } else {
                                        echo "<section class='errorMsg' id='errorMsg'>
                                            <i class=' fa-solid fa-x'></i>
                                            <p>Une erreur est survenue lors de l'inscription, veuillez rééssayer</p>
                                        </section>";
                                    }
                                } else {
                                    echo "<section class='errorMsg' id='errorMsg'>
                                        <i class=' fa-solid fa-x'></i>
                                        <p>Cet email est déjà utilisé, veuillez vous connecter ou contacter un administrateur</p>
                                    </section>";
                                }
                            }
                        } else {
                            echo "<section class='errorMsg' id='errorMsg'>
                                        <i class=' fa-solid fa-x'></i>
                                        <p>Les mots de passe ne sont pas identiques, veuillez rééssayer</p>
                                    </section>";
                        }
                    } else {
                        echo "<section class='errorMsg' id='errorMsg'>
                        <i class=' fa-solid fa-x'></i>
                        <p>Vous n'avez pas confirmé votre mot de passe</p>
                    </section>";
                    }
                } else {

                    echo "<section class='errorMsg' id='errorMsg'>
                        <i class=' fa-solid fa-x'></i>
                        <p>Vous n'avez pas rentré de mot de passe ou le mot de passe n'est pas valide</p>
                    </section>";
                }
            } else {

                echo "<section class='errorMsg' id='errorMsg'>
                        <i class=' fa-solid fa-x'></i>
                        <p>Vous n'avez pas rentré de groupe ou le groupe n'est pas valide</p>
                    </section>";
            }
        } else {

            echo "<section class='errorMsg' id='errorMsg'>
                        <i class=' fa-solid fa-x'></i>
                        <p>Vous n'avez pas rentré d'email ou l'email n'est pas valide</p>
                    </section>";
        }
    } else {
        echo "<section class='errorMsg' id='errorMsg'>
                        <i class=' fa-solid fa-x'></i>
                        <p>Vous n'avez pas rentré de pseudo ou le pseudo n'est pas valide</p>
                    </section>";
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
                    echo "<section class='errorMsg' id='errorMsg'>
                        <i class=' fa-solid fa-x'></i>
                        <p>Mot de passe incorrect</p>
                    </section>";
                }
            } else {
                // Utilisateur non trouvé
                // echo "Ce pseudo n'existe pas";
                echo "<section class='errorMsg' id='errorMsg'>
                    <i class=' fa-solid fa-x'></i>
                    <p>Ce pseudo n'existe pas</p>
                </section>";
            }
        } else {
            // echo "Veuillez entrer un mot de passe";

            echo "<section class='errorMsg' id='errorMsg'>
                <i class=' fa-solid fa-x'></i>
                <p>Veuillez entrer un mot de passe</p>
            </section>";
        }
    } else {
        // echo "Veuillez entrer un pseudo";
        echo "<section class='errorMsg' id='errorMsg'>
        <i class=' fa-solid fa-x'></i>
        <p>Veuillez entrer un pseudo</p>
    </section>";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-91RZ02SB3H"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-91RZ02SB3H');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/login.css">
    <link rel="stylesheet" href="../styles/home.css">
    <title>MMI Devoirs - Log In</title>
</head>

<body>
    <header>
        <figure>
            <img src="../img/Group3.png" alt="logo de l'application">
        </figure>
    </header>

    <main>

        <form action="../php/login.php" method="post" id="formInscription">
            <h1>Inscription</h1>

            <fieldset>
                <div>
                    <i class="fa-solid fa-user"></i>
                    <input required minlength="3" id="pseudoInput" class="pseudoInput" name="inscriptionPseudo" type="text" placeholder="Pseudo">
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
                    <input required minlength="6" name="inscriptionMDP" type="password" autocomplete="current-password" placeholder="Mot de passe">
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>

                </div>

                <div>
                    <i class="fa-solid fa-lock"></i>
                    <input required minlength="6" name="confirmInscriptionMDP" type="password" autocomplete="current-password" placeholder="Confirmation du mdp">
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>

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
                    <input required name="connexionMDP" type="password" autocomplete="current-password" placeholder="Mot de passe">
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                </div>
            </fieldset>

            <fieldset>
                <input required name="connexionSubmit" type="submit" value="Se connecter">
                <p id="switchToSignup">S'inscrire</p>
            </fieldset>

        </form>

    </main>


    <script>
        // Get all elements with the class "toggle-password" within each form
        var toggleButtons = document.querySelectorAll(".toggle-password");

        // Add a click event listener to each element
        toggleButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                // Toggle the classes
                if (this.classList.contains("fa-eye")) {
                    this.classList.remove("fa-eye");
                    this.classList.add("fa-eye-slash");
                } else {
                    this.classList.remove("fa-eye-slash");
                    this.classList.add("fa-eye");
                }

                // Get the input element which is the previous sibling of the button
                var input = this.previousElementSibling;

                // Toggle the input type between "password" and "text"
                if (input.getAttribute("type") === "password") {
                    input.setAttribute("type", "text");
                } else {
                    input.setAttribute("type", "password");
                }
            });
        });
    </script>
    <script src="../js/login.js"></script>
</body>

</html>