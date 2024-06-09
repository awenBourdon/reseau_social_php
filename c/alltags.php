<?php
session_start();

if (!isset($_SESSION['connected_id'])) {
    header("Location: registration.php");
    exit();
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title># / C</title> 
    <meta name="author" content="Nora et Awen">
    <link rel="icon" href="images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <header>
        <img src="images/logo.svg" class="logo"/>
        <nav id="menu">
            <a href="news.php"><img src="images/wall.png">Home</a>
            <a href="wall.php?user_id=<?php echo $_SESSION['connected_id']; ?>"><img src="images/flux.png">Mon Profil</a>
            <a href="feed.php?user_id=<?php echo $_SESSION['connected_id']; ?>"><img src="images/news.png">Actualités</a>
            <a href="alltags.php"><img src="images/tag.svg">Mots-clés</a>
        </nav>
        <nav id="user">
            <a href="#"><img src="images/account.svg" class="account">Mon Compte</a>
            <ul>
                <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Paramètres</a></li>
                <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Mes Followers</a></li>
                <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Mes Abonnements</a></li>
                <li><a href="logout.php">Me Déconnecter</a></li>
            </ul>
        </nav>
    </header>
    <div id="wrapper">
    <main>

    <!-- Désolé c'est pas clean de mettre du style ici ! -->
    <style>
        ul {
            list-style-type: none;
            display: grid;
            justify-content: center;
            padding: 0;
            margin: 0;
        }


    </style>

    
    <br>
    <ul>
    <img src="images/tag.svg">
        <?php
        $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
        
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        
        $result = $mysqli->query("SELECT * FROM tags");
        
        while ($row = $result->fetch_assoc()) {
            echo "<li><a href='tags.php?tag_id={$row['id']}' style='color: white; text-decoration: none; font-size: 40px; font-weight: 500'>{$row['label']}</a></li>";
        }
        
        $mysqli->close();
        ?>
    </ul>
</main>


    </div>
</body>
</html>
