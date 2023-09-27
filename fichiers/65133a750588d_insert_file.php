<?php
// Connection a la database 
$host = "localhost";
$username = "root";
$password = "";
$database = "tpvelizy";
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}



$id_livre = $_POST['selection'];

// verifier si le upload a fonctionné
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
	// collecter les informations sur le fichier
	$fileName = $_FILES['file']['name'];
	$fileTmpName = $_FILES['file']['tmp_name'];
	$fileSize = $_FILES['file']['size'];
	$fileType = $_FILES['file']['type'];

	// lire le fichier
	$fileContent = file_get_contents($fileTmpName);

	// echaper les caracteres speciaux
	$fileContent = $conn->real_escape_string($fileContent);

	// Sauvegarder le fichier sur le serveur
	$targetDir = "uploads/";
	$targetFile = $targetDir . $fileName;
	move_uploaded_file($fileTmpName, $targetFile);
}
	
$sql = "INSERT INTO fichiers (filename, size, type, path,id_livre) VALUES ('$fileName', $fileSize, '$fileType', '$targetFile','$id_livre')";
if ($conn->query($sql) === TRUE) {
	echo "Data inserted successfully";
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}
// fermer la connexion
$conn->close();
?>