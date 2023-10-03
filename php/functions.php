<?php
function ajoutDevoir()
{
    // Vérification de la connexion à la base de données
    $link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");

    // Utilisation de requêtes préparées pour éviter les injections SQL
    $title = mysqli_real_escape_string($link, htmlspecialchars($_POST['ajoutTitle']));
    $matiere = mysqli_real_escape_string($link, htmlspecialchars($_POST['ajoutMatiere']));
    $date = mysqli_real_escape_string($link, htmlspecialchars($_POST['ajoutDate']));
    $type = mysqli_real_escape_string($link, htmlspecialchars($_POST['ajoutType']));
    $description = isset($_POST['ajoutDescription']) ? "'" . mysqli_real_escape_string($link, htmlspecialchars($_POST['ajoutDescription'])) . "'" : 'NULL';
    $coef = isset($_POST['ajoutCoef']) && $_POST['ajoutCoef'] !== '' ? "'" . mysqli_real_escape_string($link, htmlspecialchars($_POST['ajoutCoef'])) . "'" : 'NULL';
    $coefMat = isset($_POST['ajoutCoefMat']) && $_POST['ajoutCoefMat'] !== '' ? "'" . mysqli_real_escape_string($link, htmlspecialchars($_POST['ajoutCoefMat'])) . "'" : 'NULL';
    $coefMatValue = isset($_POST['ajoutCoefMatValue']) && $_POST['ajoutCoefMatValue'] !== '' ? "'" . mysqli_real_escape_string($link, htmlspecialchars($_POST['ajoutCoefMatValue'])) . "'" : 'NULL';
    $groupe = mysqli_real_escape_string($link, htmlspecialchars($_POST['ajoutGroupe']));

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
            echo "<script>
    window.location.href = './pages/devoir_ajoute.html';
</script>";
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
    $title = mysqli_real_escape_string($link, htmlspecialchars($_POST['modifTitle']));
    $matiere = mysqli_real_escape_string($link, htmlspecialchars($_POST['modifMatiere']));
    $date = mysqli_real_escape_string($link, htmlspecialchars($_POST['modifDate']));
    $type = mysqli_real_escape_string($link, htmlspecialchars($_POST['modifType']));
    $description = isset($_POST['modifDescription']) ? mysqli_real_escape_string($link, htmlspecialchars($_POST['modifDescription'])) : 'NULL';
    $coef = isset($_POST['modifCoef']) && $_POST['modifCoef'] !== '' ? htmlspecialchars((int)$_POST['modifCoef']) : 'NULL';
    $modifMat = isset($_POST['modifMat']) && $_POST['modifMat'] !== '' ? mysqli_real_escape_string($link, htmlspecialchars($_POST['modifMat'])) : 'NULL';
    $modifCoefMatier = isset($_POST['modifCoefMatier']) && $_POST['modifCoefMatier'] !== '' ? "'" . mysqli_real_escape_string($link, htmlspecialchars($_POST['modifCoefMatier'])) . "'" : 'NULL';
    $idDevoir = isset($_POST['modifHidden']) && $_POST['modifHidden'] !== '' ? "'" . mysqli_real_escape_string($link, htmlspecialchars($_POST['modifHidden'])) . "'" : 'NULL';
    $groupe = mysqli_real_escape_string($link, htmlspecialchars($_POST['modifGroupe']));

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
            echo "<script>
    window.location.href = './pages/devoir_modifie.html';
</script>";
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

function suppressionAutomatiqueDevoirs()
{
    $link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");
    // Étape 1 : Sélectionner les enregistrements avec une date dépassée de 2 jours ou plus
    $sql = "SELECT * FROM `devoirs` WHERE `date` < DATE_SUB(NOW(), INTERVAL 2 DAY)";

    // Exécutez la requête SQL
    $result = mysqli_query($link, $sql);

    if ($result) {
        // Étape 2 : Supprimer les enregistrements
        while ($row = mysqli_fetch_assoc($result)) {
            $devoirId = $row['idDevoir'];

            $sqlDeleteCoeff = "DELETE FROM `coeffs` WHERE `idDevoir` = '$devoirId'";
            $resultDeleteCoeff = mysqli_query($link, $sqlDeleteCoeff);

            $sqlDeleteEtatDevoir = "DELETE FROM `etatDevoirs` WHERE `idDevoir` = '$devoirId'";
            $resultDeleteEtatDevoir = mysqli_query($link, $sqlDeleteEtatDevoir);

            $sqlDeleteFichiers = "DELETE FROM `fichiers` WHERE `idDevoir` = '$devoirId'";
            $resultDeleteFichiers = mysqli_query($link, $sqlDeleteFichiers);

            $sqlDeleteDevoir = "DELETE FROM `devoirs` WHERE `idDevoir` = '$devoirId'";
            $resultDeleteDevoir = mysqli_query($link, $sqlDeleteDevoir);

            if (!$resultDeleteCoeff || !$resultDeleteEtatDevoir || !$resultDeleteFichiers || !$resultDeleteDevoir) {
                echo "<section class='errorMsg' id='errorMsg'>
                    <i class=' fa-solid fa-x'></i>
                    <p>Erreur lors de la suppression automatique des devoirs, veuillez rééssayer ! (Code erreur : 50)</p>
                </section>";
            } else {
                echo "<section class='passedMsg' id='passedMsg'>
                    <i class=' fa-solid fa-x'></i>
                    <p>Un devoir à été supprimé car la date de rendu est dépassée depuis 2 jours</p>
                </section>";
            }
        }

        // Libérez les ressources du résultat
        mysqli_free_result($result);
    }
    // Fermez la link à la base de données
    mysqli_close($link);
}
