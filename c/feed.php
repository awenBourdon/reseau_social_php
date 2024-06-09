<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['connected_id'])) {
    header("Location: registration.php");
    exit();
}
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Feed / C</title>         
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
        <div id="wrapper">
            <?php
            
            $userId = intval($_GET['user_id']);
            ?>
            <?php
            
            $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
            ?>

            <aside>
                <?php
                
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
               
                ?>
                <img src="images/people.png" alt="Portrait de"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message des utilisateurs
                        auxquel est abonnée  <?php echo $user['alias'] ?>
                        (n° <?php echo $user['id'] ?>)
                    </p>

                </section>
            </aside>
            <main>
                <?php
                
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    users.id as user_id,
                    tags.id as tag_id,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM followers 
                    JOIN users ON users.id=followers.followed_user_id
                    JOIN posts ON posts.user_id=users.id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE followers.following_user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                while ($followers = $lesInformations->fetch_assoc())
                {
                  
                ?>                
                    <article>
                        <h3>
                        <time datetime='<?php echo date("d-m-Y", strtotime($followers["created"])); ?>'><?php echo date("d / m /Y", strtotime($followers['created'])); ?></time>
                        </h3>
                        <br/>
                        <address><?php echo $followers['author_name'] ?></address>
                        <div>
                            <p><?php echo $followers['content'] ?></p>
                            
                        </div>                                            
                        <footer>
                        <form action="" method="post" style="display: inline;">
                        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                        <?php if ($post['liked_by_user'] == 0) : ?>
                            <button type="submit" name="like" style="border: none; background: none; cursor: pointer;">
                                <img src="images/like_icon.png" alt="Like" width="30" height="30">
                            </button>
                        <?php else : ?>
                            <button type="submit" name="dislike" style="border: none; background: none; cursor: pointer;">
                                <img src="images/dislike_icon.png" alt="Dislike" width="30" height="30">
                            </button>
                        <?php endif; ?>
                    </form>
                            <small><?php echo $followers['like_number'] ?> ♥</small>
                            <a href="tags.php?tag_id=<?php echo $post['tag_id']?>"><?php echo $followers['taglist'] ?></a>
                        </footer>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>
