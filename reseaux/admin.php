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
        <title>ReSoC - Administration</title> 
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

        <?php
        
        $mysqli = new mysqli("localhost", "root", "^f2.?abH;Cp?3ZU", "socialnetwork");
        
        if ($mysqli->connect_error)
        {
            echo("Échec de la connexion : " . $mysqli->connect_error);
            exit();
        }
        ?>
        <div id="wrapper" class='admin'>
            <aside>
                <h2>Mots-clés</h2>
                <?php
                
                $laQuestionEnSql = "SELECT * FROM `tags` LIMIT 50";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                    exit();
                }

                
                while ($tag = $lesInformations->fetch_assoc())
                {
                    
                    ?>
                    <article>
                        <h3><?php echo $tag['label'] ?></h3>
                        <p><?php echo $tag['id'] ?></p>
                        <nav>
                            <a href="tags.php?tag_id=<?php echo $tag['id']?>">Messages</a>
                        </nav>
                    </article>
                <?php } ?>
            </aside>
            <main>
                <h2>Utilisatrices</h2>
                <?php
                
                $laQuestionEnSql = "SELECT * FROM `users` LIMIT 50";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                    exit();
                }

                
                while ($users = $lesInformations->fetch_assoc())
                {
                
                    ?>
                    <article>
                        <h3><?php echo $users['alias'] ?></h3>
                        <p><?php echo $users['email'] ?></p>
                        <nav>
                            <a href="wall.php?user_id=<?php echo $users['id']?>">Mur</a>
                            | <a href="feed.php?user_id=<?php echo $users['id']?>">Actualités</a>
                            | <a href="settings.php?user_id=<?php echo $users['id']?>">Paramètres</a>
                            | <a href="followers.php?user_id=<?php echo $users['id']?>">Suiveurs</a>
                            | <a href="subscriptions.php?user_id=<?php echo $users['id']?>">Abonnements</a>
                        </nav>
                    </article>
                <?php } ?>
            </main>
        </div>
    </body>
</html>
