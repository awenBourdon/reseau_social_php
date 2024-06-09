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
        <title>Inscription / C</title> 
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
                    <h2>Inscription</h2>
                    <?php
                    
                    $enCoursDeTraitement = isset($_POST['email']);
                    if ($enCoursDeTraitement)
                    {
                        
                        $new_email = $_POST['email'];
                        $new_alias = $_POST['pseudo'];
                        $new_passwd = $_POST['motpasse'];


                       
                        $mysqli = new mysqli("localhost", "root", " écrivez votre mot de passe ici, sinon si Mac -> root ou Windows -> vide", "socialnetwork");
                        
                        $new_email = $mysqli->real_escape_string($new_email);
                        $new_alias = $mysqli->real_escape_string($new_alias);
                        $new_passwd = $mysqli->real_escape_string($new_passwd);
                        
                        $new_passwd = md5($new_passwd);
                        $lInstructionSql = "INSERT INTO users (email, password, alias) VALUES ('$new_email', '$new_passwd', '$new_alias');";

                        
                        if ($mysqli->query($lInstructionSql) === TRUE) {
                           
                            $_SESSION['connected_id'] = $mysqli->insert_id;
        
                            
                            header("Location: news.php?user_id=" . $_SESSION['connected_id']);
                            exit();
                        } else {
                            echo "L'inscription a échoué : " . $mysqli->error;
                        }
                    }
            
                    ?>                     
                    <form action="registration.php" method="post">
                        <input type='hidden'name='???' value='achanger'>
                        <dl>
                            <dt><label for='pseudo'>Pseudo</label></dt>
                            <dd><input type='text'name='pseudo'></dd>
                            <dt><label for='email'>E-Mail</label></dt>
                            <dd><input type='email'name='email'></dd>
                            <dt><label for='motpasse'>Mot de passe</label></dt>
                            <dd><input class="whiteform" type='password'name='motpasse'></dd>
                        </dl>
                        <input type='submit' class="orange-button">
                    </form>
                    <p>
                        Vous avez déjà un compte ?
                        <a href='login.php' class="orange-button"><B>Se connecter.</B></a>
                    </p>
                </article>
            </main>
        </div>
    </body>
</html>
