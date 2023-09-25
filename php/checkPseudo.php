<?php
// $link = mysqli_connect("localhost", "nlerond_utilisateur", "utilisateur123", "nlerond_mmiapp");

// $pseudo = mysqli_real_escape_string($link, $_POST['pseudo']);

// if (!empty($pseudo)) {
//     $sql = "SELECT pseudo FROM `etudiants` WHERE `pseudo` = '$pseudo'";
//     $result = mysqli_query($link, $sql);
//     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
//     if (mysqli_num_rows($result) === 0) {
//         $response = "Ce pseudo est disponible";
//     } else if ($pseudo === "") {
//         $response = "Veuillez saisir un pseudo";
//     } else {
//         $response = "Ce pseudo est déjà pris, veuillez en choisir un autre";
//     }
//     echo $response;
// }
