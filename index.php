<?php session_start();

if (!isset($_SESSION['isConnected']) || $_SESSION['isConnected'] == false) {
    header('Location: ./php/login.php');
}
$link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");
$sql = "SELECT * FROM `etudiants` WHERE `pseudo` = '" . $_SESSION['pseudo'] . "'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);
$_SESSION['pseudo'] = $row['pseudo'];
$_SESSION['id'] = $row['idEtudiant'];
if ($row['admin'] == 1) {
    $privilege = "admin";
} else {
    $privilege = "utilisateur";
}

function ajoutDevoir()
{
    // Vérification de la connexion à la base de données
    $link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");
    if (!$link) {
        die("Erreur de connexion à la base de données : " . mysqli_connect_error());
    }

    // Vérification des données POST
    if (empty($_POST['ajoutTitle']) || empty($_POST['ajoutMatiere']) || empty($_POST['ajoutDate']) || empty($_POST['ajoutDescription']) || empty($_POST['ajoutType'])) {
        die("Tous les champs sont obligatoires.");
    }

    // Utilisation de requêtes préparées pour éviter les injections SQL
    $title = mysqli_real_escape_string($link, $_POST['ajoutTitle']);
    $matiere = mysqli_real_escape_string($link, $_POST['ajoutMatiere']);
    $date = mysqli_real_escape_string($link, $_POST['ajoutDate']);
    $description = isset($_POST['ajoutDescription']) ? mysqli_real_escape_string($link, $_POST['ajoutDescription']) : 'null'; // Vérifiez si le champ description est renseigné
    $type = mysqli_real_escape_string($link, $_POST['ajoutType']);
    $coef = isset($_POST['ajoutCoef']) ? mysqli_real_escape_string($link, $_POST['ajoutCoef']) : 'null'; // Vérifiez si le champ coef est renseigné
    $coefMat = isset($_POST['ajoutCoefMat']) ? mysqli_real_escape_string($link, $_POST['ajoutCoefMat']) : 'null'; // Vérifiez si le champ coefMat est renseigné
    $coefMatValue = isset($_POST['ajoutCoefMatValue']) ? mysqli_real_escape_string($link, $_POST['ajoutCoefMatValue']) : 'null'; // Vérifiez si le champ coefMatValue est renseigné

    $CheckInfos = "SELECT * FROM `devoirs` WHERE `titre` = '$title' AND `matiere` = '$matiere' AND `date` = '$date' AND `description` = '$description' AND `type` = '$type' AND `coefDevoir` = $coef AND `idEtudiant` = '" . $_SESSION['id'] . "'";
    $CheckInfosQuery = mysqli_query($link, $CheckInfos);
    if (mysqli_num_rows($CheckInfosQuery) > 0) {
        die("Ce devoir existe déjà.");
    } else {
        // Insertion du devoir
        $sql = "INSERT INTO `devoirs` (`titre`, `matiere`, `date`, `description`, `type`, `coefDevoir`, `idEtudiant`)
            VALUES ('$title', '$matiere', '$date', '$description', '$type', $coef, '" . $_SESSION['id'] . "')";
        $result = mysqli_query($link, $sql);

        if ($result) {
            // Récupération de l'ID du devoir inséré
            $idDevoir = mysqli_insert_id($link);

            // Insertion des coefficients s'ils sont renseignés
            $sql2 = "INSERT INTO coeffs (`competence`, `coeff`, `idDevoir`) VALUES ('$coefMat', $coefMatValue, $idDevoir)";
            $result2 = mysqli_query($link, $sql2);

            if ($result2) {
                // Upload des fichiers transmis par le formulaire vers le serveur et la base de données
                if (isset($_FILES['ajoutFile']) && !empty($_FILES['ajoutFile']['name'][0])) {
                    // Gérer les fichiers uploadés
                    $uploadDirectory = "./fichiers/"; // Chemin du dossier où les fichiers seront enregistrés

                    foreach ($_FILES['ajoutFile']['name'] as $index => $fileName) {
                        // Générer un nom de fichier unique
                        $uniqueFileName = uniqid() . "_" . $fileName;
                        $targetPath = $uploadDirectory . $uniqueFileName;

                        // Vérifier si le fichier a été téléchargé avec succès
                        if (move_uploaded_file($_FILES['ajoutFile']['tmp_name'][$index], $targetPath)) {
                            // Insérer le nom du fichier dans la base de données avec le même ID de devoir
                            $sql4 = "INSERT INTO fichiers (nomFichier, idDevoir) VALUES ('$uniqueFileName', $idDevoir)";
                            $result4 = mysqli_query($link, $sql4);


                            // if (!$result4) {
                            //     echo "Erreur lors de l'insertion du nom du fichier : " . mysqli_error($link);
                            // }
                        }
                        // else {
                        //     // Gérer les erreurs d'upload
                        //     echo "Erreur lors du téléchargement du fichier : " . $_FILES['ajoutFile']['error'][$index];
                        // }
                    }
                }
                // else {
                //     echo "Aucun fichier n'a été téléchargé.";
                // }
                echo "<script>location.reload();</script>";
            } else {
                echo "Erreur lors de l'insertion des coefficients.";
            }
        } else {
            echo "Erreur lors de l'insertion du devoir : " . mysqli_error($link);
        }
    }
    // Fermeture de la connexion
    mysqli_close($link);
}




?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" href="./styles/general.css">
    <link rel="stylesheet" href="./styles/home.css">

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>MMI Devoirs</title>
</head>


<body>

    <header>
        <figure>
            <img src="./img/Group3.png" alt="">
        </figure>
    </header>

    <main>
        <section class="heading">
            <h1>Devoirs</h1>
            <?php
            if ($privilege == "admin") {
                echo '<i class="fa-solid fa-plus btnAddDevoir" onclick="openAdd()"></i>';
            }

            ?>
            <style>
                .session {
                    color: white;
                }
            </style>
            <p class="session">
                <?php
                if ($row["admin"] == 0) {
                    echo "Session utilisateur de : " . $_SESSION['pseudo'];
                } else {
                    echo "Session admin de : " . $_SESSION['pseudo'];
                }
                ?>
            </p>

            <a href="./php/deconnexion.php">déco</a>
        </section>

        <section class="sub_heading">

            <div class="row_type_done">
                <h2>Afficher devoirs finis</h2>
                <div>
                    <input type="checkbox" id="toggle-btn">
                    <label for="toggle-btn"></label>
                </div>
            </div>
        </section>

        <script>
            //Utilisez jQuery pour écouter les clics sur les checkboxes
            // $(document).ready(function() {
            //     $('#iDevoir').click(function() {
            //         var devoirID = document.getElementById('iDevoir').getAttribute('data-iddevoir');

            //         // Envoyez une requête AJAX au script PHP pour mettre à jour la base de données
            //         $.ajax({
            //             url: 'index.php', // Le nom de ce fichier
            //             method: 'POST',
            //             data: {
            //                 devoirID: devoirID
            //             },
            //             success: function(response) {},
            //             error: function(error) {
            //                 alert('Erreur lors de la mise à jour du devoir : ' + error);
            //             }
            //         });
            //     });
            // });
        </script>

        <h1 id="currentDate"></h1>
        <!------------ Contenu principal Start ---------->
        <article class="main_content">
            <ul>

                <?php
                $id = $_SESSION['id'];
                $link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");
                $sql = "SELECT devoirs.*, coeffs.competence, coeffs.coeff, fichiers.fichiers_associes FROM devoirs LEFT JOIN coeffs ON devoirs.idDevoir = coeffs.idDevoir LEFT JOIN ( SELECT idDevoir, GROUP_CONCAT(nomFichier) AS fichiers_associes FROM fichiers GROUP BY idDevoir ) AS fichiers ON devoirs.idDevoir = fichiers.idDevoir ORDER BY `devoirs`.`date` ASC";
                $result = mysqli_query($link, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    $devoir = $row["idDevoir"];
                    $result2 = mysqli_query($link, "SELECT * FROM `etatDevoirs` WHERE `idEtudiant` = $id AND `idDevoir` = $devoir;");
                    $date = date("d/m", strtotime($row["date"]));
                    if (($row2 = mysqli_fetch_assoc($result2)) && $row2["statut"] == "terminé") {
                        $li = "<li class='done'>";
                    } else {
                        $li = "<li>";
                    }

                    echo $li . "
                    <form action='php/update_devoir.php' method='post'> 
                        <input type='hidden' name='devoirID' value='" . $row['idDevoir'] . "'>
                        <input type='submit' name='done-checkbox' class='done-checkbox'>
                        <i class='fa-solid fa-check'></i>
                    </form>
                    <div class='firstColumn'>
                        <h2 class='title'>" . $row["titre"] . "</h2>
                        <p class='matiere'>" . $row["matiere"] . "</p>
                    </div>
                    <div class='secondColumn'>
                        <p class='date'>" . $date . "</p>
                        <button id='iDevoir' class='fa-solid fa-circle-info' onclick='openInfo(" . $row['idDevoir'] . ")'></button>
                    </div>
                </li>";
                }
                ?>
            </ul>
        </article>
    </main>

    <!------------ Ajouter formulaire  ---------->
    <article class="addDevoir" id="addForm">
        <form action="index.php" method="post" class="addDevoir_form" enctype="multipart/form-data">
            <i class=" fa-solid fa-x" onclick="closeAdd()"></i>
            <h1>Nouveau devoir</h1>

            <label for="title">Titre <span>*</span></label>
            <input id="title" type="text" name="ajoutTitle" placeholder="Titre du devoir ..." required>

            <label for="matiere">Matière <span>*</span></label>
            <select id="matiere" name="ajoutMatiere" required>
                <option selected value=""></option>
                <option>MM2R03 Ergonomie & Accessibilité</option>
                <option>MM2R04 Culture Numérique</option>
                <option>MM2R16 Représentation et traitement de l'information</option>
                <option>MM2R05 Stratégie de communication</option>
                <option>MM2R06 Expression, communication et rhétorique</option>
                <option>MM2R07 Écriture multimédia et narration</option>
                <option>MM2R08 Production graphique</option>
                <option>MM2R09 Culture artistique</option>
                <option>MM2R10 Production audio & vidéo</option>
                <option>MM2R11 Gestion de contenus</option>
                <option>MM2R12 Intégration</option>
                <option>MM2R13 Développement Web</option>
                <option>MM2R14 Système d'information</option>
                <option>MM2R17 Gestion de projet</option>
                <option>MM2R18 Economie et Droit du numérique</option>
                <option>MM2R19 Projet Personnel et Professionnel</option>
            </select>

            <label for="date">Date <span>*</span></label>
            <input id="date" type="date" name="ajoutDate" class="inputDate" required>

            <label for="description">Description <span>*</span></label>
            <textarea maxlength="255" name="ajoutDescription" id="description" cols="30" rows="5" placeholder="Ajouter une description ..." required></textarea>

            <label for="type">Type <span>*</span></label>
            <fieldset>
                <select id="type" required name="ajoutType">
                    <option selected></option>
                    <option>DS</option>
                    <option>TP</option>
                    <option>Devoir à rendre</option>
                    <option>Présentation Orale</option>
                    <option>SAE</option>
                </select>

                <label for="coef" class="coefLabel">coef. <span>*</span></label>
                <input id="coef" required type="number" name="ajoutCoef">
            </fieldset>

            <label for="file[]">Fichiers</label>
            <input id="file[]" type="file" multiple accept="*/*" name="ajoutFile[]">

            <label for="coefMatier">Coef. de la matière dans la compétence <span>*</span></label>
            <fieldset>
                <select id="coefMatier" required name="ajoutCoefMat">
                    <option selected value=""></option>
                    <option value="Comprendre">Comprendre</option>
                    <option value="Developper">Developper</option>
                    <option value="Exprimer">Exprimer</option>
                    <option value="Concevoir">Concevoir</option>
                    <option value="Entreprendre">Entreprendre</option>
                </select>

                <input name="ajoutCoefMatValue" required type="number" class="coefLabel">
            </fieldset>

            <input type="submit" name="ajoutSubmit" value="Ajouter">
        </form>
        <?php if (isset($_POST['ajoutSubmit'])) {
            ajoutDevoir();
        } ?>
    </article>

    <!------------ Modifier formulaire  ---------->
    <article class="addDevoir" id="modifForm">
        <form action="" class="addDevoir_form">
            <i class="fa-solid fa-x" onclick="closeModif()"></i>
            <h1>Modifier devoir</h1>

            <label for="title">Titre <span>*</span></label>
            <input type="text" name="title" placeholder="Titre du devoir ..." required>

            <label for="matiere">Matière <span>*</span></label>
            <select name="matiere" id="" required>
                <option value="">MM2R03 Ergonomie & Accessibilité</option>
                <option value="">MM2R03 Ergonomie & Accessibilité</option>
                <option value="">MM2R03 Ergonomie & Accessibilité</option>
            </select>

            <label for="date">Date <span>*</span></label>
            <input type="date" name="date" class="inputDate" required>

            <label for="description">Description</label>
            <textarea required name="description" id="" cols="30" rows="5" placeholder="Ajouter une description ..."></textarea>

            <label for="type">Type <span>*</span></label>
            <fieldset>
                <select required name="type">
                    <option selected></option>
                    <option>DS</option>
                    <option>TP</option>
                    <option>Devoir à rendre</option>
                    <option>Présentation Orale</option>
                    <option>SAE</option>
                </select>

                <label for="coef" class="coefLabel">coef.</label>
                <input type="number" required name="coef">
            </fieldset>

            <label for="file[]">Fichiers</label>
            <input type="file" multiple accept="*/*" name="file[]">

            <label for="coefMatier">Coef. de la matière dans la compétence</label>
            <fieldset>
                <select required>
                    <option selected>C1</option>
                    <option>C2</option>
                    <option>C3</option>
                    <option>C4</option>
                    <option>C5</option>
                </select>

                <input type="number" required class="coefLabel">
            </fieldset>

            <input type="submit" value="Modifier">
        </form>
    </article>

    <article class="infoDevoir" id="infoDevoir">
        <section>

            <?php
            // // Vérifier si c'est une requête AJAX
            // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            //     // C'est une requête AJAX
            //     if (isset($_GET['idDevoir'])) {
            //         $idDevoir = $_GET['idDevoir'];
            //         // Faites ce que vous voulez avec l'ID du devoir ici
            //         echo "L'ID du devoir est : " . $idDevoir;
            //     } else {
            //         echo "L'ID du devoir n'a pas été spécifié dans la requête AJAX.";
            //     }
            // } else {
            //     // Ce n'est pas une requête AJAX, vous pouvez gérer cela différemment si nécessaire
            //     echo "Requête HTTP normale.";
            // }
            ?>

            <div id="resultat"></div>



            <i class="fa-solid fa-pen-to-square btnModifDev" onclick="openModif()"></i>
            <i class="fa-solid fa-x" onclick="closeInfo()"></i>
            <h1>Détails</h1>
            <?php
            // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //     // Récupérer les données envoyées par AJAX
            //     $data = $_POST['data'];
            //     $idDevoir = $data['idDevoir'];
            //     $idD = $_POST['idDevoir'];
            //     echo $idDevoir . " " . $idD . " " . $data;
            // } else {
            //     echo "Erreur lors de la récupération des données.";
            // }
            if ($id_devoir = $_COOKIE['id']) {
                $id_devoir = $_COOKIE['id'];
            } else {
                echo "Erreur lors de la récupération des données.";
            }
            echo $id_devoir;
            ?>
            <h2>Description</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae ipsa qui natus distinctio doloremque
                voluptas quidem, dolore temporibus delectus! Hic, tempore iure in sit voluptas repellat rem maiores
                aspernatur ut.</p>

            <h2>Type</h2>
            <div class="row">
                <p>DS</p>
                <span>coef. 2</span>
            </div>

            <h2>Fichiers</h2>
            <a href="#">fichier.pdf</a>

            <h2>Coeff matière</h2>
            <div class="row">
                <p>C1 - Comprendre</p>
                <span>coef. 15</span>
            </div>
        </section>

    </article>

    <script src="./js/main.js"></script>
</body>

</html>