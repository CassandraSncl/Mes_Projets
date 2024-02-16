<?php /**On démarre une session active */
    session_start();

        // Générer un jeton CSRF unique
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Créez un jeton CSRF et stockez-le en session
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérification du jeton CSRF lors de la soumission de formulaires
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Erreur CSRF : Action non autorisée.");
        }
    }
?>

<meta charset="utf-8">
<html lang="fr">

</html>
<html >
    <head>
        <link rel="stylesheet" href="styles/styles.css" type="text/css" media="screen" />
        <title>Historique</title>
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
    

        <h2>Historique des commandes</h2>

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
					        echo "<li class='navbarItem' style='texte-decoration: none;  list-style: none; color:white; padding:3px; text-align:center;'><a href='deconnexion.php'>Se déconnecter</a></li>";
                        } else {
                            echo "	<li class='navbarItem' style='texte-decoration: none;  list-style: none;'><a href='nouveau.php'>Nouveau Client</a></li>";
                            echo "	<li  class='navbarItem' style='texte-decoration: none;  list-style: none;'><a href='connexion.php'></i>Se connecter</a></li>";
                    };
                    ?>
                </ul>
            </div>
        </div>
        
        <?php

// On vérifie si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    // On redirige l'utilisateur vers la page de connexion s'il n'est pas connecté
    header("Location: connexion.php");
    exit();
}

// Inclure le fichier de connexion à la base de données
require('bd.php');

// Récupérer l'identifiant du client connecté
$id_client = $_SESSION['client']['id_client'];

// Connexion à la base de données
$bdd = getBD();

// On sélectionne les commandes du client connecté
$sql = "SELECT c.id_commande, c.id_art, c.quantite, c.envoi, l.nom, l.prix
        FROM Commandes c
        JOIN librairie l ON c.id_art = l.id_art
        WHERE c.id_client = :id_client";

$requete = $bdd->prepare($sql);
$requete->bindParam(':id_client', $id_client);
$requete->execute();

// On affiche les commandes sous forme de tableau
echo '<table border="1" style="margin-left:10%  ; max-width:70%">
    <tr>
        <th>Identifiant Commande</th>
        <th>Nom de l\'article</th>
        <th>Prix unitaire</th>
        <th>Quantité commandée</th>
        <th>Prix total</th>
        <th>État de la commande</th>
    </tr>';

while ($row = $requete->fetch()) {
    $etat_commande = $row['envoi'] ? 'Envoyée' : 'En attente';
    $prix_unitaire = $row['prix'];
    $quantite = $row['quantite'];
    $prix_total = $prix_unitaire * $quantite;

    echo '<tr>
        <td>' . $row['id_commande'] . '</td>
        <td>' . $row['nom'] . '</td>
        <td>' . $prix_unitaire . ' €</td>
        <td>' . $quantite . '</td>
        <td>' . $prix_total . ' €</td>
        <td>' . $etat_commande . '</td>
    </tr>';
}

echo '</table>';

include('chat.php');

?>





</body>

</html>
