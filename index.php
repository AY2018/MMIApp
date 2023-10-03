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

if ($row["admin"] == 1) {
    $privilege = "admin";
} else if ($row["admin"] == 2) {
    $privilege = "prof";
} else if ($row["admin"] == 3) {
    $privilege = "owner";
} else {
    $privilege = "etudiant";
}

// importer les fonctions d'ajout, de modifications et de suppression automatique des devoirs
include_once './php/functions.php';
suppressionAutomatiqueDevoirs();

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
            if ($row["admin"] == 1 || $row["admin"] == 3) {
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
                } else if ($row["admin"] == 1) {
                    echo "Session ajout de devoir de : " . $_SESSION['pseudo'];
                } else if ($row["admin"] == 2) {
                    echo "Session professeur de : " . $_SESSION['pseudo'];
                } else if ($row["admin"] == 3) {
                    echo "Session owner de : " . $_SESSION['pseudo'];
                } else {
                    echo "<section class='errorMsg' id='errorMsg'>
                            <i class=' fa-solid fa-x'></i>
                            <p>Un problème est survenue lors de la récupération de votre profil, veuillez vous déconnecter puis reconnecter ou contacter un administrateur(Code erreur : 60)</p>
                        </section>";
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

                if ($row["admin"] == 3 || $row["admin"] == 2) {
                    $sql = "SELECT devoirs.*, coeffs.competence, coeffs.coeff, fichiers.fichiers_associes FROM devoirs LEFT JOIN coeffs ON devoirs.idDevoir = coeffs.idDevoir LEFT JOIN (SELECT idDevoir, GROUP_CONCAT(nomFichier) AS fichiers_associes FROM fichiers GROUP BY idDevoir) AS fichiers ON devoirs.idDevoir = fichiers.idDevoir ORDER BY devoirs.date ASC;";
                    $result = mysqli_query($link, $sql);
                } else {
                    $sql = "SELECT devoirs.*, coeffs.competence, coeffs.coeff, fichiers.fichiers_associes FROM devoirs LEFT JOIN coeffs ON devoirs.idDevoir = coeffs.idDevoir LEFT JOIN ( SELECT idDevoir, GROUP_CONCAT(nomFichier) AS fichiers_associes FROM fichiers GROUP BY idDevoir ) AS fichiers ON devoirs.idDevoir = fichiers.idDevoir WHERE devoirs.groupe = 'Tous' OR devoirs.groupe = SUBSTRING((SELECT groupe FROM etudiants WHERE idEtudiant = '$id'), 1, 1) OR devoirs.groupe = (SELECT groupe FROM etudiants WHERE idEtudiant = '$id') ORDER BY devoirs.date ASC;";
                    $result = mysqli_query($link, $sql);
                }

                if (mysqli_num_rows($result) == 0) {
                    echo "<p class='noDevoir'>Il n'y a aucun devoir prévu pour le moment.</p>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $devoir = $row["idDevoir"];
                        $result2 = mysqli_query($link, "SELECT * FROM `etatDevoirs` WHERE `idEtudiant` = $id AND `idDevoir` = $devoir;");
                        $date = date("d/m", strtotime($row["date"]));
                        if (($row2 = mysqli_fetch_assoc($result2)) && $row2["statut"] == "terminé") {
                            $li = "<li id='liDevoir' class='done'>";
                        } else {
                            $li = "<li id='liDevoir' class='notDone'>";
                        }

                        $titre = htmlspecialchars_decode($row["titre"]);
                        $groupe = htmlspecialchars_decode($row["groupe"]);
                        $matiere = htmlspecialchars_decode($row["matiere"]);

                        echo $li . "
                        <form action='php/update_devoir.php' method='post'> 
                            <input type='hidden' name='devoirID' value='" . $row['idDevoir'] . "'>
                            <i class='fa-solid fa-check'></i>
                            <input type='submit' value='' name='done-checkbox' class='done-checkbox'>
                        </form>
                        <div class='firstColumn'>
                            <h2 class='title'>" . $titre . "</h2> 
                            <span class='groupe'>" . $groupe . "</span>
                            <p class='matiere'>" . $matiere . "</p>
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
                <option>MM3R01 - Anglais</option>
                <option>MM3R02 - Anglais renforcé</option>
                <option>MM3R07 - Expression - communication</option>
                <option>MM3R08 - Ecriture multimédia et narration</option>
                <option>MM3R09 - Création et design interactif</option>
                <option>MM3R10 - Culture artistique</option>
                <option>MM3R11 - Audiovisuel, 3D</option>
                <option>MM3R12 - Développement front</option>
                <option>MM3R13 - Développement back</option>
                <option>MM3R14 - Déploiement de services</option>
                <option>MM3R15 - Représentation de l'information</option>
                <option>MM3R16 - Gestion de projet</option>
                <option>MM3R17 - Economie et droit du numérique</option>
                <option>MM3R18 - Projet Personnel et Professionnel</option>
                <option>MM3R19 - Développement interactif</option>
                <option>MM3SA01 - UX/UI</option>
                <option>MM3SA02 - Communication plurimédia</option>
                <option>MM3SA03 - Datavisualisation</option>
                <option>MM3R03 - Design XP</option>
                <option>MM3R04 - Culture numérique</option>
                <option>MM3R05 - Stratégie de communication</option>
                <option>MM3R06 - Référencement</option>
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
                <option>MM3R01 - Anglais</option>
                <option>MM3R02 - Anglais renforcé</option>
                <option>MM3R07 - Expression - communication</option>
                <option>MM3R08 - Ecriture multimédia et narration</option>
                <option>MM3R09 - Création et design interactif</option>
                <option>MM3R10 - Culture artistique</option>
                <option>MM3R11 - Audiovisuel, 3D</option>
                <option>MM3R12 - Développement front</option>
                <option>MM3R13 - Développement back</option>
                <option>MM3R14 - Déploiement de services</option>
                <option>MM3R15 - Représentation de l'information</option>
                <option>MM3R16 - Gestion de projet</option>
                <option>MM3R17 - Economie et droit du numérique</option>
                <option>MM3R18 - Projet Personnel et Professionnel</option>
                <option>MM3R19 - Développement interactif</option>
                <option>MM3SA01 - UX/UI</option>
                <option>MM3SA02 - Communication plurimédia</option>
                <option>MM3SA03 - Datavisualisation</option>
                <option>MM3R03 - Design XP</option>
                <option>MM3R04 - Culture numérique</option>
                <option>MM3R05 - Stratégie de communication</option>
                <option>MM3R06 - Référencement</option>
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
            if ($privilege == "admin" || $privilege == "owner") {
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