<?php
session_start();
$mysqli = new mysqli("localhost", "root", " écrivez votre mot de passe ici, sinon si Mac -> root ou Windows -> vide", "socialnetwork");

// Vérification de la connexion
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$userId = $_SESSION['connected_id'];

// Suppression des lignes dans les tables qui ont des clés étrangères pointant vers 'posts'
$postForeignKeyTables = ['posts_tags']; // Ajouter toutes les tables avec des FK vers 'posts'
foreach ($postForeignKeyTables as $table) {
    $deleteQuery = $mysqli->prepare("DELETE FROM `$table` WHERE `post_id` IN (SELECT `id` FROM `posts` WHERE `user_id` = ?)");
    $deleteQuery->bind_param("i", $userId);
    if ($deleteQuery->execute() === FALSE) {
        echo "Error deleting records from $table: " . $deleteQuery->error;
    }
    $deleteQuery->close();
}

// Liste des tables ayant des clés étrangères référencées à 'users'
$foreignKeyTables = ['posts', 'followers', 'likes']; // 'users' n'a pas besoin d'être dans cette liste

// Suppression des lignes dans les tables enfants
foreach ($foreignKeyTables as $table) {
    $deleteQuery = $mysqli->prepare("DELETE FROM `$table` WHERE `id` = ?");
    $deleteQuery->bind_param("i", $userId);
    $deleteQuery->close();
}

// Suppression de
$deleteUserQuery = $mysqli->prepare("DELETE FROM `users` WHERE `id` = ?");
$deleteUserQuery->bind_param("i", $userId);
if ($deleteUserQuery->execute() === TRUE) {
    // Redirection vers la page d'inscription
    header("Location: registration.php");
    exit();
} else {
    echo "Error deleting user: " . $deleteUserQuery->error;
}
$deleteUserQuery->close();

$mysqli->close();
?>
