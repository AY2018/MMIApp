<?php
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


$CheckInfos = "SELECT * FROM `devoirs` WHERE `titre` = '$title' AND `matiere` = '$matiere'";
$CheckInfosQuery = mysqli_query($link, $CheckInfos);


// gerer l'erreur de la requete sql
if (mysqli_num_rows($CheckInfosQuery) > 0) {

    echo "<section class='errorMsg' id='errorMsg'>
    <i class=' fa-solid fa-x'></i>
    <p>Le devoir existe déjà, veuillez rééssayer !</p>
</section>";
} else {
    $sql = "INSERT INTO `devoirs` (`titre`, `matiere`, `date`, `description`, `type`, `coefDevoir`, `idEtudiant`)
SELECT '$title', '$matiere', '$date', $description, '$type', $coef, '" . $_SESSION['id'] . "'
FROM DUAL
WHERE NOT EXISTS (
SELECT 1
FROM `devoirs`
WHERE `titre` = '$title' AND `matiere` = '$matiere' AND `date` = '$date' AND `description` = '$description' AND `type` = '$type' AND `coefDevoir` = $coef
)";
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
                            <p>Erreur lors de l'ajout du fichier, veuillez modifier le devoir pour ajouter le fichier !</p>
                        </section>";
                    }
                } else {
                    // Gérer les erreurs d'upload

                    echo "<section class='errorMsg' id='errorMsg'>
                            <i class=' fa-solid fa-x'></i>
                            <p>Erreur lors de l'ajout du fichier, veuillez modifier le devoir pour ajouter le fichier !</p>
                        </section>";
                }
            }
        }
        echo "<section class='passedMsg' id='passedMsg'>
                <i class=' fa-solid fa-x'></i>
                <p>Devoir ajouté avec succès !</p>
            </section>";
    } else {

        echo "<section class='errorMsg' id='errorMsg'>
                <i class=' fa-solid fa-x'></i>
                <p>Erreur lors de l'ajout du devoir, veuillez rééssayer !</p>
            </section>";
    }
}
// Fermeture de la connexion
mysqli_close($link);
