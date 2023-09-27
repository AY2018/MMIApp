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

    // Utilisation de requêtes préparées pour éviter les injections SQL
    $title = mysqli_real_escape_string($link, $_POST['ajoutTitle']);
    $matiere = mysqli_real_escape_string($link, $_POST['ajoutMatiere']);
    $date = mysqli_real_escape_string($link, $_POST['ajoutDate']);
    $type = mysqli_real_escape_string($link, $_POST['ajoutType']);
    $description = isset($_POST['ajoutDescription']) ? "'" . mysqli_real_escape_string($link, $_POST['ajoutDescription']) . "'" : 'NULL';
    $coef = isset($_POST['ajoutCoef']) && $_POST['ajoutCoef'] !== '' ? "'" . mysqli_real_escape_string($link, $_POST['ajoutCoef']) . "'" : 'NULL';
    $coefMat = isset($_POST['ajoutCoefMat']) && $_POST['ajoutCoefMat'] !== '' ? "'" . mysqli_real_escape_string($link, $_POST['ajoutCoefMat']) . "'" : 'NULL';
    $coefMatValue = isset($_POST['ajoutCoefMatValue']) && $_POST['ajoutCoefMatValue'] !== '' ? "'" . mysqli_real_escape_string($link, $_POST['ajoutCoefMatValue']) . "'" : 'NULL';
    $groupe = mysqli_real_escape_string($link, $_POST['ajoutGroupe']);

    $CheckInfos = "SELECT * FROM `devoirs` WHERE `titre` = '$title' AND `matiere` = '$matiere'";
    $CheckInfosQuery = mysqli_query($link, $CheckInfos);


    // gerer l'erreur de la requete sql
    if (mysqli_num_rows($CheckInfosQuery) > 0) {

        echo "<section class='errorMsg' id='errorMsg'>
            <i class=' fa-solid fa-x'></i>
            <p>Le devoir existe déjà, veuillez rééssayer !</p>
        </section>";
    } else {
        $sql = "INSERT INTO `devoirs`(`titre`, `matiere`, `date`, `description`, `type`, `coefDevoir`, `groupe`, `idEtudiant`) VALUES ('$title', '$matiere', '$date', $description, '$type', $coef, '$groupe', '" . $_SESSION['id'] . "');";
        $result = mysqli_query($link, $sql);

        $idDevoir = mysqli_insert_id($link);
        $sql2 = "INSERT INTO coeffs (`competence`, `coeff`, `idDevoir`) VALUES ($coefMat, $coefMatValue, $idDevoir)";
        $result2 = mysqli_query($link, $sql2);

        if ($result2 && $result) {
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
                        echo $sql4;
                        $result4 = mysqli_query($link, $sql4);

                        if (!$result4) {
                            echo "<section class='errorMsg' id='errorMsg'>
                                <i class=' fa-solid fa-x'></i>
                                <p>Erreur lors de l'ajout du fichier, veuillez modifier le devoir pour ajouter le fichier ! (Code erreur : 11)</p>
                            </section>";
                        }
                    } else {
                        // Gérer les erreurs d'upload
                        echo "<section class='errorMsg' id='errorMsg'>
                                <i class=' fa-solid fa-x'></i>
                                <p>Erreur lors de l'ajout du fichier, veuillez modifier le devoir pour ajouter le fichier ! (Code erreur : 12)</p>
                            </section>";
                    }
                }
            }
            echo "<script>window.location.href = './pages/devoir_ajoute.html';</script>";
        } else {

            echo "<section class='errorMsg' id='errorMsg'>
                <i class=' fa-solid fa-x'></i>
                <p>Erreur lors de l'ajout du devoir, veuillez rééssayer ! (Code erreur : 13)</p>
            </section>";
        }
    }
    // Fermeture de la connexion
    mysqli_close($link);
}

function modifierDevoir()
{
    // Vérification de la connexion à la base de données
    $link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");

    // Utilisation de requêtes préparées pour éviter les injections SQL
    $title = mysqli_real_escape_string($link, $_POST['modifTitle']);
    $matiere = mysqli_real_escape_string($link, $_POST['modifMatiere']);
    $date = mysqli_real_escape_string($link, $_POST['modifDate']);
    $type = mysqli_real_escape_string($link, $_POST['modifType']);
    $description = isset($_POST['modifDescription']) ? mysqli_real_escape_string($link, $_POST['modifDescription']) : 'NULL';
    $coef = isset($_POST['modifCoef']) && $_POST['modifCoef'] !== '' ? (int)$_POST['modifCoef'] : 'NULL';
    $modifMat = isset($_POST['modifMat']) && $_POST['modifMat'] !== '' ? mysqli_real_escape_string($link, $_POST['modifMat']) : 'NULL';
    $modifCoefMatier = isset($_POST['modifCoefMatier']) && $_POST['modifCoefMatier'] !== '' ? "'" . mysqli_real_escape_string($link, $_POST['modifCoefMatier']) . "'" : 'NULL';
    $idDevoir = isset($_POST['modifHidden']) && $_POST['modifHidden'] !== '' ? "'" . mysqli_real_escape_string($link, $_POST['modifHidden']) . "'" : 'NULL';
    $groupe = mysqli_real_escape_string($link, $_POST['modifGroupe']);

    if ($idDevoir != 'NULL') {
        $sqlUpdateDevoir = "UPDATE `devoirs` SET `titre` = '$title', `matiere` = '$matiere', `date` = '$date', `description` = '$description', `type` = '$type', `coefDevoir` = $coef, `groupe` = '$groupe' WHERE `devoirs`.`idDevoir` = $idDevoir";
        $result = mysqli_query($link, $sqlUpdateDevoir);

        $sqlUpdateCoeff = "UPDATE `coeffs` SET `competence` = '$modifMat', `coeff` = $modifCoefMatier WHERE `coeffs`.`idDevoir` = $idDevoir;";
        $result2 = mysqli_query($link, $sqlUpdateCoeff);

        if ($result && $result2) {
            // Vérifiez s'il y a des nouveaux fichiers téléchargés
            if (isset($_FILES['modifFile']) && !empty($_FILES['modifFile']['name'][0])) {

                // Gérer les fichiers uploadés
                $uploadDirectory = "./fichiers/"; // Chemin du dossier où les fichiers seront enregistrés
                $sqlCheckInfosBDD = "SELECT * FROM `fichiers` WHERE `idDevoir` = $idDevoir";
                $resultCheckInfosBDD = mysqli_query($link, $sqlCheckInfosBDD);
                if (!$resultCheckInfosBDD) {

                    echo "<section class='errorMsg' id='errorMsg'>
                        <i class=' fa-solid fa-x'></i>
                        <p>Erreur lors de la modification du devoir, veuillez rééssayer ! (Code erreur : 21)</p>
                    </section>";
                }

                if (mysqli_num_rows($resultCheckInfosBDD) > 0) {
                    // Supprimez d'abord les anciens fichiers liés à ce devoir
                    $sqlDeleteOldFiles = "DELETE FROM `fichiers` WHERE `idDevoir` = $idDevoir";
                    $resultDelete = mysqli_query($link, $sqlDeleteOldFiles);
                    foreach ($_FILES['modifFile']['name'] as $index => $fileName) {
                        // Générer un nom de fichier unique
                        $uniqueFileName = uniqid() . "_" . mysqli_real_escape_string($link, $fileName);
                        $targetPath = $uploadDirectory . $uniqueFileName;

                        // Vérifier si le fichier a été téléchargé avec succès
                        if (move_uploaded_file($_FILES['modifFile']['tmp_name'][$index], $targetPath)) {
                            // Insérer le nom du fichier dans la base de données avec le même ID de devoir
                            $sql4 = "INSERT INTO fichiers (nomFichier, idDevoir) VALUES ('$uniqueFileName', $idDevoir);";
                            $result4 = mysqli_query($link, $sql4);

                            if (!$result4) {

                                echo "<section class='errorMsg' id='errorMsg'>
                                        <i class=' fa-solid fa-x'></i>
                                        <p>Il y a une erreur lors de la modification du devoir, essayez de raccourcir le nom du fichier ou de le changer par un nom sans caractères spéciaux. (Code erreur : 22)</p>
                                    </section>";
                                return;
                            } else {
                                echo "<section class='passedMsg' id='passedMsg'>
                                        <i class=' fa-solid fa-x'></i>
                                        <p>Devoir modifié avec succès !</p>
                                    </section>";
                            }
                        } else {
                            // Gérer les erreurs d'upload

                            echo "<section class='errorMsg' id='errorMsg'>
                                <i class=' fa-solid fa-x'></i>
                                <p>Erreur lors de la modification du devoir, veuillez rééssayer ! (Code erreur : 23)</p>
                            </section>";
                        }
                    }
                    echo "<section class='passedMsg' id='passedMsg'>
                            <i class=' fa-solid fa-x'></i>
                            <p>Devoir modifié avec succès !</p>
                        </section>";
                } else {
                    // Upload des fichiers transmis par le formulaire vers le serveur et la base de données
                    if (isset($_FILES['modifFile']) && !empty($_FILES['modifFile']['name'][0])) {
                        // Gérer les fichiers uploadés
                        $uploadDirectory = "./fichiers/";
                        foreach ($_FILES['modifFile']['name'] as $index => $fileName) {
                            // Générer un nom de fichier unique
                            $uniqueFileName = uniqid() . "_" . mysqli_real_escape_string($link, $fileName);
                            $targetPath = $uploadDirectory . $uniqueFileName;
                            // Vérifier si le fichier a été téléchargé avec succès
                            if (move_uploaded_file($_FILES['modifFile']['tmp_name'][$index], $targetPath)) {
                                // Insérer le nom du fichier dans la base de données avec le même ID de devoir
                                $sql4 = "INSERT INTO fichiers (nomFichier, idDevoir) VALUES ('$uniqueFileName', $idDevoir);";
                                $result4 = mysqli_query($link, $sql4);
                                if (!$result4) {
                                    echo "<section class='errorMsg' id='errorMsg'>
                                        <i class=' fa-solid fa-x'></i>
                                        <p>Erreur lors de la modification du devoir, veuillez rééssayer ! (Code erreur : 24)</p>
                                    </section>";
                                } else {
                                    echo "<section class='passedMsg' id='passedMsg'>
                                            <i class=' fa-solid fa-x'></i>
                                            <p>Devoir modifié avec succès !</p>
                                        </section>";
                                }
                            } else {
                                // Gérer les erreurs d'upload

                                echo "<section class='errorMsg' id='errorMsg'>
                                <i class=' fa-solid fa-x'></i>
                                <p>Erreur lors de la modification du devoir, veuillez rééssayer ! (Code erreur : 25)</p>
                            </section>";
                            }
                        }
                    }
                }
            }
            echo "<script>window.location.href = './pages/devoir_modifie.html';</script>";
        } else {
            echo "<section class='errorMsg' id='errorMsg'>
                <i class=' fa-solid fa-x'></i>
                <p>5Erreur lors de la modification du devoir, veuillez rééssayer ! (Code erreur : 26)</p>
            </section>";
        }
    } else {
        echo "<section class='errorMsg' id='errorMsg'>
                <i class=' fa-solid fa-x'></i>
                <p>6Erreur lors de la modification du devoir, veuillez rééssayer ! (Code erreur : 27)</p>
            </section>";
    }
    mysqli_close($link);
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
    <link rel="stylesheet" href="./styles/general.css">
    <link rel="stylesheet" href="./styles/home.css">

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>MMI Devoirs</title>
</head>


<body>

    <header>
        <figure>
            <img src="./img/Group3.png" alt="">
        </figure>

        <a href="./php/deconnexion.php"><i class="fa-solid fa-right-from-bracket"></i></a>

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
                    echo "Bonjour " . $_SESSION['pseudo'];
                } else {
                    echo "Session admin de : " . $_SESSION['pseudo'];
                }
                ?>
            </p>
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
            $(document).ready(function() {
                $('.iDevoir').click(function() {
                    var devoirID = $(this).data('iddevoir');

                    // Envoyez une requête AJAX au script PHP pour obtenir les détails du devoir
                    $.ajax({
                        url: 'php/id.php',
                        method: 'POST',
                        data: {
                            devoirID: devoirID
                        },
                        dataType: 'json', // Indiquez que vous attendez une réponse JSON
                        success: function(response) {
                            console.log(response);
                            if (response.description == "") {
                                $('#showDesc').html("<p>Aucune description pour ce devoir.</p>");
                            } else {
                                $('#showDesc').text(response.description);
                            }

                            $('#showType').text(response.type);
                            $('#showCoeff').text("coef. " + response.coefDevoir);
                            $('#showCompetence').text(response.competence);
                            $('#showCoefCompetence').text("coef. " + response.coeffCompetence);
                            $('#titre').text(response.titre);
                            // $('#trash-icon').data('iddevoir', response.id);
                            $('#infoDevoir').css('display', 'flex');

                            // Vérifiez s'il y a des fichiers dans la réponse
                            if (response.fichiers.length > 0) {
                                let fichiersHtml = '<ul>';
                                let nomsDesFichiers = []; // Créez un tableau pour stocker les noms de fichiers

                                response.fichiers.forEach(function(fichier) {
                                    // Utilisez une opération de découpage pour obtenir le nom du fichier
                                    let nomDuFichier = fichier.slice(fichier.indexOf('_') + 1); // Suppose que le nom du fichier est séparé par un "_"
                                    fichiersHtml += '<li><a href="fichiers/' + fichier + '" download>' + nomDuFichier + '</a></li>';

                                    nomsDesFichiers.push(nomDuFichier); // Ajoutez le nom du fichier au tableau
                                });

                                fichiersHtml += '</ul>';

                                // Joignez les noms de fichiers avec une virgule et ajoutez-les à #previousFile
                                $('#fichierLink').html(fichiersHtml);
                                $('#previousFile').html("Fichier(s) précédent(s) : " + nomsDesFichiers.join(', ') + "<br><br> <p>Si vous validez le formulaire, le fichier sera remplacé par le nouveau.</p>");
                            } else {
                                $('#fichierLink').html("<p>Aucun fichier associé</p>");
                            }

                            // Reste du code pour mettre à jour les champs de formulaire
                            $('input[name="modifTitle"]').val(response.titre);
                            $('select[name="modifMatiere"]').val(response.matiere);
                            $('input[name="modifDate"]').val(response.date);
                            $('textarea[name="modifDescription"]').val(response.description);
                            $('select[name="modifType"]').val(response.type);
                            $('input[name="modifCoef"]').val(response.coefDevoir);
                            $('select[name="modifMat"]').val(response.competence);
                            $('input[name="modifCoefMatier"]').val(response.coeffCompetence);
                            $('input[name="modifHidden"]').val(response.id);
                            $('select[name="modifGroupe"]').val(response.groupe);


                            $('#hiddenSupprimerDevoir').val(response.id);
                        },
                        error: function(error) {
                            alert("Erreur lors de la modification du devoir, veuillez rééssayer!(Code erreur: 30)");
                        }
                    });
                });
            });
        </script>


        <h1 id="currentDate"></h1>
        <!------------ Contenu principal Start ---------->
        <article class="main_content">
            <ul>

                <?php
                $id = $_SESSION['id'];
                $link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");
                $sql = "SELECT devoirs.*, coeffs.competence, coeffs.coeff, fichiers.fichiers_associes FROM devoirs LEFT JOIN coeffs ON devoirs.idDevoir = coeffs.idDevoir LEFT JOIN ( SELECT idDevoir, GROUP_CONCAT(nomFichier) AS fichiers_associes FROM fichiers GROUP BY idDevoir ) AS fichiers ON devoirs.idDevoir = fichiers.idDevoir WHERE devoirs.groupe = 'Tous' OR devoirs.groupe = SUBSTRING((SELECT groupe FROM etudiants WHERE idEtudiant = '$id'), 1, 1) OR devoirs.groupe = (SELECT groupe FROM etudiants WHERE idEtudiant = '$id') ORDER BY devoirs.date ASC;";
                $result = mysqli_query($link, $sql);
                $rowCount = 0;

                if (mysqli_num_rows($result) == 0) {
                    echo "<p class='noDevoir'>Il n'y a aucun devoir prévue pour le moment.</p>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $rowCount++;
                        $devoir = $row["idDevoir"];
                        $result2 = mysqli_query($link, "SELECT * FROM `etatDevoirs` WHERE `idEtudiant` = $id AND `idDevoir` = $devoir;");
                        $date = date("d/m", strtotime($row["date"]));
                        if (($row2 = mysqli_fetch_assoc($result2)) && $row2["statut"] == "terminé") {
                            $li = "<li id='liDevoir' class='done'>";
                        } else {
                            $li = "<li id='liDevoir'>";
                        }

                        echo $li . "
                        <form action='php/update_devoir.php' method='post'> 
                            <input type='hidden' name='devoirID' value='" . $row['idDevoir'] . "'>
                            <i class='fa-solid fa-check'></i>
                            <input type='submit' value='' name='done-checkbox' class='done-checkbox'>
                        </form>
                        <div class='firstColumn'>
                            <h2 class='title'>" . $row["titre"] . "</h2> 
                            <span class='groupe'>" . $row["groupe"] . "</span>
                            <p class='matiere'>" . $row["matiere"] . "</p>
                        </div>
                        <div class='secondColumn'>
                            <p class='date'>" . $date . "</p>
                            <button id='iDevoir' class='fa-solid fa-circle-info iDevoir' data-iddevoir='" . $row['idDevoir'] . "'></button>
                        </div>
                    </li>";
                    }
                }
                ?>

            </ul>
        </article>
    </main>


    <!------------ Ajouter formulaire  ---------->
    <article class=" addDevoir" id="addForm">
        <form action="index.php" method="post" id="addDevoir_form" class="addDevoir_form" enctype="multipart/form-data">
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

            <label for="description">Description</label>
            <textarea maxlength="255" name="ajoutDescription" id="description" cols="30" rows="5" placeholder="Ajouter une description ..."></textarea>

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

                <label for="coef" class="coefLabel">coef.</label>
                <input id="coef" type="number" name="ajoutCoef">
            </fieldset>

            <label for="file[]">Fichiers</label>
            <input id="file[]" type="file" multiple accept="*/*" name="ajoutFile[]">

            <label for="coefMatier">Coef. de la matière dans la compétence</label>
            <fieldset>
                <select id="coefMatier" name="ajoutCoefMat">
                    <option selected value=""></option>
                    <option value="Comprendre">Comprendre</option>
                    <option value="Developper">Developper</option>
                    <option value="Exprimer">Exprimer</option>
                    <option value="Concevoir">Concevoir</option>
                    <option value="Entreprendre">Entreprendre</option>
                </select>

                <input name="ajoutCoefMatValue" type="number" class="coefLabel">
            </fieldset>

            <label for="groupe">Groupe</label>
            <select id="ajoutGroupe" name="ajoutGroupe">
                <option selected value="Tous">Tous</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="A1">A1</option>
                <option value="A2">A2</option>
            </select>

            <input type="submit" id="ajoutSubmit" name="ajoutSubmit" value="Ajouter">
        </form>
    </article>
    <?php
    if (isset($_POST['ajoutSubmit'])) {
        ajoutDevoir();
    }
    ?>


    <!------------ Modifier formulaire  ---------->
    <article class="addDevoir" id="modifForm">
        <form action="index.php" method="post" class="addDevoir_form" enctype="multipart/form-data">
            <i class=" fa-solid fa-x" onclick="closeModif()"></i>
            <h1>Modifier devoir</h1>

            <label for="title">Titre <span>*</span></label>
            <input type="text" name="modifTitle" placeholder="Titre du devoir ..." required>

            <label for="matiere">Matière <span>*</span></label>
            <select id="matiere" name="modifMatiere" required>
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
            <input type="date" name="modifDate" class="inputDate" required>

            <label for="description">Description</label>
            <textarea name="modifDescription" id="" cols="30" rows="5" placeholder="Ajouter une description ..."></textarea>

            <label for="type">Type <span>*</span></label>
            <fieldset>
                <select id="type" required name="modifType">
                    <option selected></option>
                    <option>DS</option>
                    <option>TP</option>
                    <option>Devoir à rendre</option>
                    <option>Présentation Orale</option>
                    <option>SAE</option>
                </select>

                <label for="coef" class="coefLabel">coef.</label>
                <input type="number" name="modifCoef">
            </fieldset>

            <label for="modifFile[]">Fichiers</label>
            <input type="file" multiple accept="*/*" name="modifFile[]">
            <div id="previousFile"></div>


            <label for="coefMatier">Coef. de la matière dans la compétence</label>
            <fieldset>
                <select id="coefMatier" name="modifMat">
                    <option selected></option>
                    <option value="Comprendre">Comprendre</option>
                    <option value="Developper">Developper</option>
                    <option value="Exprimer">Exprimer</option>
                    <option value="Concevoir">Concevoir</option>
                    <option value="Entreprendre">Entreprendre</option>
                </select>

                <input name="modifCoefMatier" type="number" class="coefLabel">
            </fieldset>

            <label for="groupe">Groupe</label>
            <select id="modifGroupe" name="modifGroupe">
                <option selected value="Tous">Tous</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="A1">A1</option>
                <option value="A2">A2</option>
            </select>

            <input type="hidden" name="modifHidden">
            <input name="modifSubmit" type="submit" value="Modifier">
        </form>
    </article>

    <?php if (isset($_POST['modifSubmit'])) {
        modifierDevoir();
    } ?>

    <!------------ Détails devoir  ---------->
    <article class="infoDevoir" id="infoDevoir">
        <section>
            <?php
            if ($privilege == "admin") {
                echo '<i class="fa-solid fa-pen-to-square btnModifDev" onclick="openModif()"></i>
                    <i id="trash-icon" class="fa-solid fa-trash trash-icon" style="color: #d71414;left: 20%; position: absolute" onclick="openDlt()"></i>';
            }
            ?>
            <i class="fa-solid fa-x" onclick="closeInfo()"></i>
            <h1>Détails</h1>
            <h2 id="titre"></h2>

            <h2>Description</h2>
            <p id="showDesc"></p>

            <h2>Type</h2>
            <div class="row">
                <p id="showType"></p>
                <span id="showCoeff"></span>
            </div>

            <h2>Fichiers</h2>
            <div id="fichierLink">
                <a download href="#"></a>
            </div>


            <h2>Coeff matière</h2>
            <div class="row">
                <p id="showCompetence"></p>
                <span id="showCoefCompetence"></span>
            </div>
        </section>

    </article>

    <!------------ Supprimer devoir  ---------->
    <article class="articleDelete addDevoir" id="articleDelete">
        <form action="php/delete.php" method="post" class="deleteDevoir_form">
            <p>Êtes-vous sûr de vouloir supprimer ce devoir ?</p>
            <fieldset>
                <div id="refreshLink" onclick="closeDlt()">Non</div>
                <input type="hidden" id="hiddenSupprimerDevoir" name="hiddenSupprimerDevoir" value="">
                <input type="submit" name="deleteSubmit" value="Supprimer">
            </fieldset>

        </form>
    </article>
    <script src="./js/main.js"></script>
</body>

</html>