<?php
error_reporting(0);
session_start();


if (!isset($_SESSION['connected_id'])) {
    header("Location: registration.php");
    exit();
}


$mysqli = new mysqli("localhost", "root", "", "socialnetwork");
if ($mysqli->connect_errno) {
    echo "Échec de la connexion à MySQL : " . $mysqli->connect_error;
    exit();
}


$connectedUserId = $_SESSION['connected_id'];
$userId = intval($_GET['user_id']);


$laQuestionEnSql = "SELECT * FROM users WHERE id='$userId'";
$lesInformations = $mysqli->query($laQuestionEnSql);
if ($lesInformations) {
    $user = $lesInformations->fetch_assoc();
} else {
    echo "Échec de la requête : " . $mysqli->error;
    exit();
}

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Mon Profil / C</title> 
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
                    <?php if ($_SESSION['connected_id'] !== null) : ?>
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
        <img src="images/people.png" alt="Portrait de"/>
        <section>
            <h3>Présentation</h3>
            <p>Sur cette page vous trouverez tous les messages de : <?php echo $user['alias']; ?>
                (n° <?php echo $user['id']; ?>)
            </p>
        </section>
    </aside>
    <main>
        <?php
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['like'])) {
                $postId = $_POST['post_id'];
                $checkLikeQuery = "SELECT * FROM likes WHERE user_id='$connectedUserId' AND post_id='$postId'";
                $result = $mysqli->query($checkLikeQuery);
                
                if ($result && $result->num_rows == 0) {
                    $likeQuery = "INSERT INTO likes (user_id, post_id) VALUES ('$connectedUserId', '$postId')";
                    if (!$mysqli->query($likeQuery)) {
                    }
                }
            } elseif (isset($_POST['dislike'])) {
                $postId = $_POST['post_id'];
                $dislikeQuery = "DELETE FROM likes WHERE user_id='$connectedUserId' AND post_id='$postId'";
                if (!$mysqli->query($dislikeQuery)) {}
            }
        }
        
        
        if ($userId == $_SESSION['connected_id']) {
        ?>
            <article>
                <h2>Poster un message</h2>
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
                    $authorId = $_SESSION['connected_id'];
                    $postContent = $_POST['message'];
                    $authorId = intval($mysqli->real_escape_string($authorId));
                    $postContent = $mysqli->real_escape_string($postContent);
                    $lInstructionSql = "INSERT INTO posts (id, user_id, content, created, parent_id) VALUES (NULL, '$authorId', '$postContent', NOW(), NULL)";
                    $ok = $mysqli->query($lInstructionSql);
                    if (!$ok) {
                    }
                }
                ?>
                <form action="wall.php?user_id=<?php echo $_SESSION['connected_id']; ?>" method="post">
                    <dl>
                        <dt><label>Message</label></dt>
                        <dd><textarea name='message'></textarea></dd>
                    </dl>
                    <input type='submit' value='Poster' class="orange-button" >
                </form>               
            </article>
        <?php
        }

        
        $laQuestionEnSql = "
            SELECT posts.id as post_id, posts.content, posts.created, users.alias as author_name,
            users.id as user_id,
            tags.id as tag_id, 
            COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist,
            IF(likes.user_id IS NOT NULL, 1, 0) AS liked_by_user
            FROM posts
            JOIN users ON users.id=posts.user_id
            LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
            LEFT JOIN tags ON posts_tags.tag_id = tags.id 
            LEFT JOIN likes ON likes.post_id = posts.id AND likes.user_id = '$connectedUserId'
            WHERE posts.user_id='$userId'
            GROUP BY posts.id
            ORDER BY posts.created DESC  
        ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        if (!$lesInformations) {
            echo "Échec de la requête : " . $mysqli->error;
        }

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['delete'])) {
    
            $postIdToDelete = $_POST['post_id'];
        
           
            if ($mysqli->query($deleteQuery)) {
               
                header("Location: {$_SERVER['REQUEST_URI']}");
                exit();
            } else {
            }
        }

        
        while ($post = $lesInformations->fetch_assoc()) {
        ?>

            <article>
                <h3>
                <time datetime='<?php echo date("d-m-Y", strtotime($post["created"])); ?>'><?php echo date("d / m /Y", strtotime($post['created'])); ?></time>
                </h3>
                <br/>
                <address><a href="wall.php?user_id=<?php echo $post['user_id']; ?>"><?php echo $post['author_name']; ?></a></address>
                <div>
                    <p><?php echo $post['content']; ?></p>
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
                    <a href="tags.php?tag_id=<?php echo $post['tag_id']; ?>"><?php echo $post['taglist']; ?></a>

                </footer>
            </article>

        <?php
        }
        ?>
    </main>
</div>
</body>
</html>