<?php
error_reporting(0);
session_start();

if ($_SESSION['connected_id'] !== null) {
    header("Location: news.php?user_id=" . $_SESSION['connected_id']);
    exit();
}
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Connexion / c</title> 
        <meta name="author" content="Nora et Awen">
        <link rel="icon" href="images/favicon.svg" type="image/x-icon">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
       

        <div id="wrapper" >

            <aside>
                <h2>Présentation</h2>
                <p>Bienvenu sur notre réseau social.</p>
            </aside>
            <main>
                <article>
                    <h2>Connexion</h2>
                    <?php
                    $enCoursDeTraitement = isset($_POST['email']);
                    if ($enCoursDeTraitement)
                    {
                       
                        $emailAVerifier = $_POST['email'];
                        $passwdAVerifier = $_POST['motpasse'];


                        
                        $mysqli = new mysqli("localhost", "root", " écrivez votre mot de passe ici, sinon si Mac -> root ou Windows -> vide", "socialnetwork");
                        
                        $emailAVerifier = $mysqli->real_escape_string($emailAVerifier);
                        $passwdAVerifier = $mysqli->real_escape_string($passwdAVerifier);
                        
                        $passwdAVerifier = md5($passwdAVerifier);
                        
                        $lInstructionSql = "SELECT * "
                                . "FROM users "
                                . "WHERE "
                                . "email LIKE '" . $emailAVerifier . "'"
                                ;
                        
                        $res = $mysqli->query($lInstructionSql);
                        $user = $res->fetch_assoc();
                        if ( ! $user OR $user["password"] != $passwdAVerifier)
                        {
                            echo "Identifiants incorrects ";
                            
                        } else
                        {
                            echo "Votre connexion est un succès : " . $user['alias'] . ".";
                            
                            $_SESSION['connected_id']=$user['id'];
                            header("Location: news.php?user_id=" . $_SESSION['connected_id']);
                        }
                    }
                    ?>                     
                    <form action="login.php" method="post">
                        <input type='hidden'name='???' value='achanger'>
                        <dl>
                            <dt><label for='email'>E-Mail</label></dt>
                            <dd><input type='email'name='email'></dd>
                            <dt><label for='motpasse'>Mot de passe</label></dt>
                            <dd><input type='password'name='motpasse'></dd>
                        </dl>
                        <input type='submit' class="orange-button">
                    </form>
                    <p>
                        Pas de compte?
                        <a href='registration.php'><B>Inscrivez-vous.</B></a>
                    </p>

                </article>
            </main>
        </div>
    </body>
</html>
