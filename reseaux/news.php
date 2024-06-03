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
        <title>Actualités / C</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="icon" href="images/favicon.svg" type="image/x-icon">
        <link rel="icon" href="images/favicon.svg" type="image/x-icon">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
    <header>
            <img src="images/logo.svg" class="logo"/>
            <nav id="menu">
                <a href="news.php"><img src="images/news.png">Actualités</a>
                <a href="wall.php?user_id=<?php echo $_SESSION['connected_id']; ?>"><img src="images/wall.png">Mon Profil</a>
                <a href="feed.php?user_id=<?php echo $_SESSION['connected_id']; ?>"><img src="images/flux.png">Flux</a>
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
                <img src="images/profil.png" alt="Portrait de l'utilisateur"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages de
                        tous les utilisatrices du site.</p>
                </section>
            </aside>
            <main>
                <!-- L'article qui suit est un exemple pour la présentation et 
                  @todo: doit etre retiré -->
                           

                <?php
                /*
                  // C'est ici que le travail PHP commence
                  // Votre mission si vous l'acceptez est de chercher dans la base
                  // de données la liste des 5 derniers messsages (posts) et
                  // de l'afficher
                  // Documentation : les exemples https://www.php.net/manual/fr/mysqli.query.php
                  // plus généralement : https://www.php.net/manual/fr/mysqli.query.php
                 */

                // Etape 1: Ouvrir une connexion avec la base de donnée.
                $mysqli = new mysqli("localhost", "root", "^f2.?abH;Cp?3ZU", "socialnetwork");
                //verification
                if ($mysqli->connect_errno)
                {
                    echo "<article>";
                    echo("Échec de la connexion : " . $mysqli->connect_error);
                    echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                    echo "</article>";
                    exit();
                }

                // Etape 2: Poser une question à la base de donnée et récupérer ses informations
                // cette requete vous est donnée, elle est complexe mais correcte, 
                // si vous ne la comprenez pas c'est normal, passez, on y reviendra
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    users.id as user_id,
                    tags.id as tag_id,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    LIMIT 5
                    ";
                    
                $lesInformations = $mysqli->query($laQuestionEnSql);
                // Vérification
                if ( ! $lesInformations)
                {
                    echo "<article>";
                    echo("Échec de la requete : " . $mysqli->error);
                    echo("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                    exit();
                }

                // Etape 3: Parcourir ces données et les ranger bien comme il faut dans du html
                // NB: à chaque tour du while, la variable post ci dessous reçois les informations du post suivant.
                while ($post = $lesInformations->fetch_assoc())
                {
                    //la ligne ci-dessous doit etre supprimée mais regardez ce 
                    //qu'elle affiche avant pour comprendre comment sont organisées les information dans votre 
                    //echo "<pre>" . print_r($post, 1) . "</pre>";

                    // @todo : Votre mission c'est de remplacer les AREMPLACER par les bonnes valeurs
                    // ci-dessous par les bonnes valeurs cachées dans la variable $post 
                    // on vous met le pied à l'étrier avec created
                    // 
                    // avec le ? > ci-dessous on sort du mode php et on écrit du html comme on veut... mais en restant dans la boucle
                    ?>
                    <article>
                        <h3>
                            <time><?php echo $post['created'] ?></time>
                        </h3>
                        <address><a href="wall.php?user_id=<?php echo $post['user_id']?>"><?php echo $post['author_name']; ?></a></address>
                        <div>
                            <p><?php echo $post['content'] ?></p>
                        </div>
                        <footer>
                            <small><?php echo $post['like_number'] ?> ♥</small>
                            <a href="tags.php?tag_id=<?php echo $post['tag_id']?>"><?php echo $post['taglist'] ?></a>
                        </footer>
                    </article>
                    <?php
                    // avec le <?php ci-dessus on retourne en mode php 
                }// cette accolade ferme et termine la boucle while ouverte avant.
                ?>

            </main>
        </div>
    </body>
</html>
