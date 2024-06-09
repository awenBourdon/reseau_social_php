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
        <title>ReSoC - Home</title> 
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

        <div id="wrapper" >

            <aside>
                <h2>Présentation</h2>
                <p>Sur cette page on peut poster un message en se faisant 
                    passer pour quelqu'un d'autre</p>
            </aside>
            <main>
                <article>
                    <h2>Poster un message</h2>
                    <?php
                    
                    $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
                    
                    $listAuteurs = [];
                    $laQuestionEnSql = "SELECT * FROM users";
                    $lesInformations = $mysqli->query($laQuestionEnSql);
                    while ($user = $lesInformations->fetch_assoc())
                    {
                        $listAuteurs[$user['id']] = $user['alias'];
                    }


                 
                    $enCoursDeTraitement = isset($_POST['message']);
                    if ($enCoursDeTraitement)
                    {
                       
                        $authorId = $_SESSION['connected_id'];
                        $postContent = $_POST['message'];


                        
                        $authorId = intval($mysqli->real_escape_string($authorId));
                        $postContent = $mysqli->real_escape_string($postContent);
                        
                        $lInstructionSql = "INSERT INTO posts "
                                . "(id, user_id, content, created, parent_id) "
                                . "VALUES (NULL, "
                                . $authorId . ", "
                                . "'" . $postContent . "', "
                                . "NOW(), "
                                . "NULL);"
                                ;
                        
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter le message: " . $mysqli->error;
                        } else
                        {
                            echo "Message posté en tant que :" . $listAuteurs[$authorId];
                        }
                    }
                    ?>                     
                    <form action="usurpedpost.php" method="post">
                        <input type='hidden' name='???' value='achanger'>
                        <dl>
                            <dt><label for='message'>Message</label></dt>
                            <dd><textarea name='message'></textarea></dd>
                        </dl>
                        <input type='submit'>
                    </form>               
                </article>
            </main>
        </div>
    </body>
</html>