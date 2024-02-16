<?php
    session_start();

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

  // Vérification du jeton CSRF
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
          // Le jeton est valide, traitez les données du formulaire de connexion
        } else {
          // Le jeton CSRF est invalide, vous pouvez afficher une erreur ou prendre d'autres mesures
            die("Tentative d'attaque CSRF détectée.");
        }
    }

?>

<!DOCTYPE html>
<meta charset="utf-8">
<html lang="fr">
    
<html>
    <head>
        <link rel="stylesheet" href="../styles/styles.css" type="text/css" media="screen" />
        <title>Contact</title>
    </head>
    <body>

        <!-- On crée une nav barre de navigation -->
    <header class="head">
        <nav>
            <img src="../images/logo.jpeg" alt="Logo" id="logo"></a>
            <h1 id="titre">Librairie en ligne</h1>
            <ul>
                <li><a class="acc" href="../index.php">Accueil </a></li>
                <li><a class="acc" href="contact/contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- On crée une barre de navigation verticale.
        Elle comprend quand le client n'est pas connecté:
            -Un lien vers la page inscription
            - Un lien vers la page connexion
        Quand le clien est connecté:
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
                            echo '<li id="Bonjour" style="list-style: none; color:grey; font-size:20px; "> Bonjour ' . $prenom . ' ' . $nom .'</p>';
                            echo "<li class='navbarItem'> <a style='text-decoration: none; color:white; padding:3px; text-align:center;'  href='../panier.php'> Panier </a> </li> ";
                            echo "	<li class='navbarItem'><a  style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='../historique.php'>Historique des commandes</a></li>";
					        echo "	<li  class='navbarItem'><a style='text-decoration: none, color:white;  padding:3px; text-align:center;' href='../deconnexion.php'>Se déconnecter</a></li>";
                        } else {
                            echo "	<li class='navbarItem''><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='../nouveau.php'>Nouveau Client</a></li>";
                            echo "	<li class='navbarItem'><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='../connexion.php'></i>Se connecter</a></li>";
                        };
                    ?>
                </ul>
            </div>
        </div>

    <?php
        
    // Vérifier si l'utilisateur est connecté
        if (isset($_SESSION['client'])) {
            $prenom = $_SESSION['client']['prenom'];
            $nom = $_SESSION['client']['nom'];
            $article_id = 'id_art';
        }

        include('../chat.php');

        
    ?>

        <h2>Me contacter</h1>

        <div class="conteneur_contact">
            <img src="../images/photo.jpg" alt="Ma photo">

            <p class="contact">Nom: Sénécaille Cassandra</p>
            <p class="contact">Parcours académique:
                <li class="list">Collège: Paul Bert, Capestang </li>
                <li class="list"> Lycée: Marc Bloch, Sérignan </li>
                <li class="list"> Université: Paul Valéry, Monpellier</li>
                <li class="list"> Actuellement: 3ème année de licence MIASHS,
                </br>Université Montpellier 3 </li></p>
            <p class="contact">Parcours professionnel: Etudiante</p>
            <p class="contact">Passe-temps: Equitation, musique, films,...</p>
            <p class="contact">Adresse e-mail: cassandra.senecaille@etu.univ-montp3.fr </p>
        </div>

    </body>
</html>
