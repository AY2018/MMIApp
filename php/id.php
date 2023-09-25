<?php
// Assurez-vous que vous avez une connexion à la base de données ici
$link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");

if (isset($_POST['devoirID'])) {
    $id = $_POST['devoirID'];

    // Écrivez une requête SQL pour récupérer les détails du devoir en fonction de l'ID
    $sqlDevoir = "SELECT * FROM devoirs WHERE idDevoir = '$id'";
    $sqlCoeff = "SELECT * FROM `coeffs` WHERE `idDevoir`= '$id'";
    $sqlFichier = "SELECT * FROM `fichiers` WHERE `idDevoir`= '$id'";
    $resDevoir = mysqli_query($link, $sqlDevoir);
    $resCoeff = mysqli_query($link, $sqlCoeff);
    $resFichier = mysqli_query($link, $sqlFichier);

    if ($resDevoir && $resCoeff) {
        $rowDevoir = mysqli_fetch_assoc($resDevoir);
        $rowCoeff = mysqli_fetch_assoc($resCoeff);

        $titre = isset($rowDevoir['titre']) ? $rowDevoir['titre'] : "Aucune information sur le devoir";
        $matiere = isset($rowDevoir['matiere']) ? $rowDevoir['matiere'] : "Aucune information sur le devoir";
        $date = isset($rowDevoir['date']) ? $rowDevoir['date'] : "Aucune information sur le devoir";

        $description = isset($rowDevoir['description']) ? $rowDevoir['description'] : "Aucune information sur le devoir";

        $type = isset($rowDevoir['type']) ? $rowDevoir['type'] : "Aucune information sur le devoir";
        $coefDevoir = isset($rowDevoir['coefDevoir']) ? $rowDevoir['coefDevoir'] : "Aucune information sur le devoir";
        $competence = isset($rowCoeff['competence']) && $rowCoeff['competence'] != 'NULL' ? $rowCoeff['competence'] : "Aucune information sur le devoir";
        $coeffCompetence = isset($rowCoeff['coeff']) ? $rowCoeff['coeff'] : "Aucune information sur le devoir";

        // Requête pour récupérer les fichiers associés
        $sqlFichiers = "SELECT nomFichier FROM fichiers WHERE idDevoir = '$id'";
        $resFichiers = mysqli_query($link, $sqlFichiers);

        // Créez un tableau associatif avec les données que vous souhaitez renvoyer
        $response = array(
            "titre" => $titre,
            "matiere" => $matiere,
            "date" => $date,
            "description" => $description,
            "type" => $type,
            "coefDevoir" => $coefDevoir,
            "competence" => $competence,
            "coeffCompetence" => $coeffCompetence,
            "id" => $id,
            "fichiers" => array() // Tableau pour stocker les noms de fichiers
        );

        if ($resFichiers) {
            while ($rowFichier = mysqli_fetch_assoc($resFichiers)) {
                $response["fichiers"][] = basename($rowFichier['nomFichier']);
            }
        }

        // Renvoyez les données au format JSON
        echo json_encode($response);
    } else {
        echo "Erreur lors de la requête SQL : " . mysqli_error($link);
    }
} else {
    echo "ID du devoir non spécifié.";
}
