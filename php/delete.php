<?php session_start();
$link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");

$idDevoir = $_POST['hiddenSupprimerDevoir'];


if (isset($_POST['hiddenSupprimerDevoir'])) {

    $sqlDeleteCoeff = "DELETE FROM `coeffs` WHERE `idDevoir` = '$idDevoir'";
    $resultDeleteCoeff = mysqli_query($link, $sqlDeleteCoeff);

    $sqlDeleteEtatDevoir = "DELETE FROM `etatDevoirs` WHERE `idDevoir` = '$idDevoir'";
    $resultDeleteEtatDevoir = mysqli_query($link, $sqlDeleteEtatDevoir);

    $sqlDeleteFichiers = "DELETE FROM `fichiers` WHERE `idDevoir` = '$idDevoir'";
    $resultDeleteFichiers = mysqli_query($link, $sqlDeleteFichiers);

    $sql = "DELETE FROM `devoirs` WHERE `idDevoir` = '$idDevoir'";
    $result = mysqli_query($link, $sql);
    if ($result) {
        echo "<script>location.href='../index.php'</script>";
    } else {
        echo "Erreur lors de la requête SQL : " . mysqli_error($link);
    }
} else {
    echo "<script>location.href='../index.php'</script>";
}
