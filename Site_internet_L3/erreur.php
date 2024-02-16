<?php
session_start();
// Génération et stockage du token CSRF
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;
?>

<!DOCTYPE html>
<meta charset="utf-8">
<html lang="fr">

</html>
<html >
    <head>
        <link rel="stylesheet" href="styles/styles.css" type="text/css" media="screen" />
        <title>Librairie en ligne</title>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    </head>
    <body >

    <!-- On crée une nav barre de navigation -->
    <header class="head">
        <nav>
            <img src="images/logo.jpeg" alt="Logo" id="logo"></a>
            <h1 id="titre">Librairie en ligne</h1>
            <ul>
                <li><a class="acc" href="index.php">Accueil </a></li>
                <li><a class="acc" href="contact/contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <?php
        
    // Vérifier si l'utilisateur est connecté
        if (isset($_SESSION['client'])) {
            $prenom = $_SESSION['client']['prenom'];
            $nom = $_SESSION['client']['nom'];
            $article_id = 'id_art';
        
    ?>

    <?php
        /**On appelle la base de données depuis le fichier bd.php
        * On appelle ensuite la fonction qui contient le PDO
        * Pour finir on créer une requête SQL qui permet de chercher dans la table clients toutes les lignes.
        */
        // Connexion à la base de données
        require('bd.php');
        $bdd = getBD();
        $sql = "SELECT * FROM clients";
        $resultat = $bdd->query($sql);
        
    ?>
    
    <!-- On crée une barre de navigation verticale.
        Elle comprend quand le client n'est pas connecté:
            -Un lien vers la page inscription
            - Un lien vers la page connexion
        Quand le client est connecté:
            - Un lien vers la page Deconnexion
            - Un lien vers le panier
            - Un message "Bonjour <'nom'> <'prenom'>
        -->
        <div class="wrapper">
            <div class = "slide">
                <ul>
                    <?php
                        if (isset($_SESSION['client'])) {
                            $prenom = $_SESSION['client']['prenom'];
                            $nom = $_SESSION['client']['nom'];
                            echo '<li id="Bonjour" style="list-style: none; color:grey; font-size:20px;"> Bonjour ' . $prenom . ' ' . $nom .'</p>';
                            echo "<li class='navbarItem'> <a style='text-decoration: none; color:white; padding:3px; text-align:center;'  href='panier.php'> Panier </a> </li> ";
                            echo "	<li class='navbarItem'><a  style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='historique.php'>Historique des commandes</a></li>";
					        echo "	<li  class='navbarItem'><a style='text-decoration: none, color:white;  padding:3px; text-align:center;' href='deconnexion.php'>Se déconnecter</a></li>";
                        } else {
                            echo "	<li class='navbarItem''><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='nouveau.php'>Nouveau Client</a></li>";
                            echo "	<li class='navbarItem'><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='connexion.php'></i>Se connecter</a></li>";
                    };
                    ?>
                </ul>
            </div>
        </div>
        <?php
            echo '<p style=" color: red; margin:10px; font-size:15px;">Erreur lors du paiement.</p>';
        }
        ?>


    </body>
</html>