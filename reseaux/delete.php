<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = intval($_POST['userId']);

    $servername = "localhost";
    $username = "root";
    $password = "^f2.?abH;Cp?3ZU";
    $dbname = "socialnetwork";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    //Filter on DB
    $sql = "DELETE FROM user WHERE id=$userId";

    //Check result
    if ($conn->query($sql) === TRUE) {
        echo "Utilisateur supprimé avec succès";
    } else {
        echo "Erreur lors de la suppression: " . $conn->error;
    }

    $conn->close();
    header("Location: registration.php");
    exit();
}
?>




