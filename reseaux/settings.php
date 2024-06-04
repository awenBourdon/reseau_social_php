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
        <title>Paramètres / C</title>
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

                </ul>            </nav>
        </header>
        <div id="wrapper" class='profile'>            <aside>
                <img src="images/people.png" alt="Portrait de l'utilisateur"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les informations de l'utilisatrice
                        n° <?php echo intval($_GET['user_id']) ?></p>                </section>
            </aside>
            <main>
                <?php
                
                $userId = intval($_GET['user_id']);                
                 
                $mysqli = new mysqli("localhost", "root", "^f2.?abH;Cp?3ZU", "socialnetwork");                
                 
                $laQuestionEnSql = "
                    SELECT users.*,
                    count(DISTINCT posts.id) as totalpost,
                    count(DISTINCT given.post_id) as totalgiven,
                    count(DISTINCT recieved.user_id) as totalrecieved
                    FROM users
                    LEFT JOIN posts ON posts.user_id=users.id
                    LEFT JOIN likes as given ON given.user_id=users.id
                    LEFT JOIN likes as recieved ON recieved.post_id=posts.id
                    WHERE users.id = '$userId'
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                $user = $lesInformations->fetch_assoc();                
                
                ?>
                <article class='parameters'>
                    <h3>Mes paramètres</h3>
                    <dl>
                        <dt>Pseudo</dt>
                        <dd><?php echo $user['alias'] ?></dd>
                        <dt>Email</dt>
                        <dd><?php echo $user['email'] ?></dd>
                        <dt>Nombre de message</dt>
                        <dd><?php echo $user['totalpost'] ?></dd>
                        <dt>Nombre de "J'aime" donnés </dt>
                        <dd><?php echo $user['totalgiven'] ?></dd>
                        <dt>Nombre de "J'aime" reçus</dt>
                        <dd><?php echo $user['totalrecieved'] ?></dd>
                        <a href="delete.php"><dd>Supprimer Compte</dd></a>
                    </dl>                
                </article>
            </main>
        </div>
    </body>
</html>