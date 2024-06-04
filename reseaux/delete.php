<?php
session_start();
$mysqli = new mysqli("localhost", "root", "^f2.?abH;Cp?3ZU", "socialnetwork");

if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

$userId = $_SESSION['connected_id'];

// Begin a transaction
$mysqli->begin_transaction();

try {
    
    $mysqli->query("DELETE FROM posts WHERE user_id = $userId");
    $mysqli->query("DELETE FROM followers WHERE followed_user_id = $userId");
    $mysqli->query("DELETE FROM likes WHERE user_id = $userId");
    

    
    $delete = "DELETE FROM users WHERE id_user = $userId";
    $mysqli->query($delete);

    
    $mysqli->commit();
} catch (Exception $e) {
   
    $mysqli->rollback();
    echo "Failed to delete user: " . $e->getMessage();
}

// Redirect to registration page or another appropriate page
header("Location: registration.php");
exit();
?>





