<?php
session_start();
$link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");
if (isset($_POST['done-checkbox'])) {
    $devoirID = $_POST['devoirID'];
    $idEtudiant = $_SESSION['id'];

    $recupInfo = "SELECT * FROM etatDevoirs WHERE idEtudiant = $idEtudiant AND idDevoir = $devoirID";
    $recupInfoQuery = mysqli_query($link, $recupInfo);
    $rowInfos = mysqli_fetch_assoc($recupInfoQuery);

    if (mysqli_num_rows($recupInfoQuery) == 0) {
        $sql = "INSERT INTO etatDevoirs (`idEtudiant`, `idDevoir`, `statut`) VALUES ($idEtudiant, $devoirID, 'terminé')";
        $result = mysqli_query($link, $sql);
        header('Location: index.php');
    } else {
        if ($rowInfos['statut'] == 'terminé') {
            $sql = "UPDATE etatDevoirs SET statut = 'non terminé' WHERE idEtudiant = $idEtudiant AND idDevoir = $devoirID";
            $result = mysqli_query($link, $sql);
            header('Location: index.php');
        } else {
            $sql = "UPDATE etatDevoirs SET statut = 'terminé' WHERE idEtudiant = $idEtudiant AND idDevoir = $devoirID";
            $result = mysqli_query($link, $sql);
            header('Location: index.php');
        }
    }
}
