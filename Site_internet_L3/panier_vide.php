<?php
session_start();

// Génération et stockage du token CSRF
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
    header("Location: connexion.php");
    exit();
}

// Inclure le fichier de connexion à la base de données
require('bd.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification du jeton CSRF
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        // Le jeton est valide, traitez les données du formulaire
        // Vous pouvez continuer à afficher ou traiter le panier ici
    } else {
        // Le jeton CSRF est invalide, vous pouvez afficher une erreur ou prendre d'autres mesures
        die("Tentative d'attaque CSRF détectée.");
    }
}

// Créer un tableau pour stocker le contenu du panier
$panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : array();

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles/styles.css" type="text/css" media="screen" />
    <title>Panier</title>
</head>
<body>
<header class="head">
            <nav>
                <h1 id="titre">Librairie en ligne</h1>
                <ul>
                    <li><a class="acc" href="index.php">Accueil </a></li>
                    <li><a class="acc" href="contact/contact.php">Contact</a></li>
                </ul>
            </nav>
        </header>

    <h2> Panier</h2>

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
                            echo '<li id="Bonjour" style="list-style: none; color:grey; font-size:20px;"> Bonjour ' . $prenom . ' ' . $nom .'</p>';
                            echo "<li class='navbarItem'> <a style='text-decoration: none; color:white; padding:3px; text-align:center;'  href='panier.php'> Panier </a> </li> ";
                            echo "	<li class='navbarItem'><a  style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='historique.php'>Historique des commandes</a></li>";
                            echo "	<li class='navbarItem'><a  style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='commande.php'> Passer Commande</a></li>";
					        echo "	<li  class='navbarItem'><a style='text-decoration: none, color:white;  padding:3px; text-align:center;' href='deconnexion.php'>Se déconnecter</a></li>";
                        } else {
                            echo "	<li class='navbarItem''><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='nouveau.php'>Nouveau Client</a></li>";
                            echo "	<li class='navbarItem'><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='connexion.php'></i>Se connecter</a></li>";
                    };
                    ?>
                </ul>
            </div>
        </div>
    
    <p>Votre panier ne contient aucun article. </p>
</body>
</html>
