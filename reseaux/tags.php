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
    <meta name="author" content="Julien Falconnet">
    <link rel="icon" href="images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <header>
        <img src="images/logo.svg" class="logo"/>
        <nav id="menu">
            <a href="news.php"><img src="images/news.png">Home</a>
            <a href="wall.php?user_id=<?php echo $_SESSION['connected_id']; ?>"><img src="images/wall.png">Mon Profil</a>
            <a href="feed.php?user_id=<?php echo $_SESSION['connected_id']; ?>"><img src="images/flux.png">Actualités</a>
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
        <?php
        $tagId = intval($_GET['tag_id']);
        $mysqli = new mysqli("localhost", "root", "^f2.?abH;Cp?3ZU", "socialnetwork");
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['like'])) {
                $postId = $_POST['post_id'];
                $checkLikeQuery = "SELECT * FROM likes WHERE user_id='{$_SESSION['connected_id']}' AND post_id='$postId'";
                $result = $mysqli->query($checkLikeQuery);
                if ($result && $result->num_rows == 0) {
                    $likeQuery = "INSERT INTO likes (user_id, post_id) VALUES ('{$_SESSION['connected_id']}', '$postId')";
                    if (!$mysqli->query($likeQuery)) {
                        echo "Échec du like : " . $mysqli->error;
                    }
                }
            } elseif (isset($_POST['dislike'])) {
                $postId = $_POST['post_id'];
                $dislikeQuery = "DELETE FROM likes WHERE user_id='{$_SESSION['connected_id']}' AND post_id='$postId'";
                if (!$mysqli->query($dislikeQuery)) {
                    echo "Échec du dislike : " . $mysqli->error;
                }
            }
        }
        ?>
        
        <aside>
            <?php
            $laQuestionEnSql = "SELECT * FROM tags WHERE id='$tagId'";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $tag = $lesInformations->fetch_assoc();
            ?>
            <img src="images/profil.png" alt="Portrait de l'utilisateur"/>
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez les derniers messages comportant
                    le mot-clé <?php echo $tag['label'] ?>
                    (n° <?php echo $tagId ?>)
                </p>
            </section>
        </aside>
        <main>
            <?php
            $laQuestionEnSql = "
                SELECT posts.id as post_id, posts.content, posts.created,
                users.id as user_id, users.alias as author_name,  
                count(likes.id) as like_number,  
                GROUP_CONCAT(DISTINCT tags.label) AS taglist,
                IF(likes.user_id IS NOT NULL, 1, 0) AS liked_by_user
                FROM posts_tags as filter 
                JOIN posts ON posts.id=filter.post_id
                JOIN users ON users.id=posts.user_id
                LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                LEFT JOIN tags ON posts_tags.tag_id = tags.id 
                LEFT JOIN likes ON likes.post_id = posts.id AND likes.user_id = '{$_SESSION['connected_id']}'
                WHERE filter.tag_id = '$tagId' 
                GROUP BY posts.id
                ORDER BY posts.created DESC";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo("Échec de la requete : " . $mysqli->error);
            }
            
            while ($post = $lesInformations->fetch_assoc()) {
            ?>
                <article>
                    <h3>
                        <time datetime='<?php echo $post['created']; ?>'><?php echo $post['created']; ?></time>
                    </h3>
                    <address><a href="wall.php?user_id=<?php echo $post['user_id']?>"><?php echo $post['author_name']; ?></a></address>
                    <div>
                        <p><?php echo $post['content'] ?></p>
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
                        <small><?php echo $post['like_number']; ?> ♥</small>
                        <a href="tags.php?tag_id=<?php echo $post['tag_id']?>"><?php echo $post['taglist'] ?></a>
                    </footer>
                </article>
            <?php } ?>
        </main>
    </div>
</body>
</html>