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
        <title>Mes abonnés / C</title> 
        <meta name="author" content="Julien Falconnet">
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
                <a href="tags.php?tag_id=<?php echo $_SESSION['connected_id']; ?>"><img src="images/tag.svg">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#"><img src="images/account.svg" class="account">Mon Compte</a>
                 <ul>
                    <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Paramêtres</a></li>
                    <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Mes Followers</a></li>
                    <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Mes Abonnements</a></li>
                    <li>
                    <div class="user-widget">
                    <?php if($_SESSION['connected_id'] !== null ) : ?>
                    <a href="logout.php">Me Déconnecter</a>
                    <?php else : ?>
                    <a href="login.php">Me connecter</a>
                     <?php endif; ?>
                    </div>
                    </li>

                </ul>

            </nav>
        </header>
        <div id="wrapper">          
            <aside>
                <img src = "images/people.png" alt = "Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez la liste des personnes qui
                        suivent les messages de l'utilisatrice
                        n° <?php echo intval($_GET['user_id']) ?></p>

                </section>
            </aside>
            <main class='contacts'>
                <?php
                
                $userId = intval($_GET['user_id']);
               
                $mysqli = new mysqli("localhost", "root", "^f2.?abH;Cp?3ZU", "socialnetwork");
                
                $laQuestionEnSql = "
                    SELECT users.*
                    FROM followers
                    LEFT JOIN users ON users.id=followers.following_user_id
                    WHERE followers.followed_user_id='$userId'
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                 
                while ($followers = $lesInformations->fetch_assoc()) {
                    ?>
                    <article>
                        <img src="images/people.png" alt="blason"/>
                        <h3><?php echo $followers['alias'] ?></h3>
                        <p>id:<?php echo $followers['id'] ?></p>
                    </article>
                    <?php } ?>
                </main>
            </div>
        </body>
    </html>
