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

// Fonction pour récupérer le prix d'un article depuis la base de données
function getPrixArticle($article_id) {
    $sql = "SELECT prix FROM librairie WHERE id_art = :id_art";
    
    $bdd = getBD();
    $requete = $bdd->prepare($sql);
    $requete->bindParam(':id_art', $article_id);
    $requete->execute();
    
    $result = $requete->fetch();
    return $result['prix'];
}

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
                <img src="images/logo.jpeg" alt="Logo" id="logo"></a>
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
                            echo "	<li class='navbarItem'><a  style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='historique.php'>Historique des commandes</a></li>";
                            if(!empty($panier)) {
                            echo "	<li class='navbarItem'><a  style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='commande.php'> Passer Commande</a></li>";
                            echo "	<li class='navbarItem'><a  style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='vider_panier.php'> Vider le panier</a></li>";
                            }
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
    if (empty($panier)) {
        echo '<div style="margin-left: 10%; padding: 10px; max-width: 80%;">';
        echo '<p style="margin-left: 20px; margin-top: 20px; max-width: 70%;">Votre panier ne contient aucun article.</p>';
        echo '</div>';
    } else {
        // Afficher un tableau HTML pour lister les articles du panier
        echo '<div style="margin-left: 10%; padding: 10px; max-width: 70%;">';
        echo '<table style="border-collapse: collapse; width: 100%; margin-top: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <tr style="background-color: #707070; color: #fff;">
                    <th style="padding: 12px; margin-left:20px;">ID Article</th>
                    <th style="padding: 12px; margin-left:20px;">Nom</th>
                    <th style="padding: 12px; margin-left:20px;">Prix unitaire</th>
                    <th style="padding: 12px; margin-left:20px;">Quantité demandée</th>
                    <th style="padding: 12px; margin-left:20px;">Prix total</th>
                </tr>';
        echo '</div>';
    
        // Boucle pour afficher chaque article dans le panier
        $montant_total = 0;
        $ligneCount = 0;
    
        foreach ($panier as $article) {
            if (isset($article['id']) && isset($article['quantite'])) {
                $article_id = $article['id'];
                $quantite = $article['quantite'];
    
                // Récupérer les détails de l'article depuis la base de données
                $sql = "SELECT * FROM librairie WHERE id_art = :article_id";
    
                $bdd = getBD();
                $requete = $bdd->prepare($sql);
                $requete->bindParam(':article_id', $article_id);
                $requete->execute();
    
                $result = $requete->fetch();
    
                $prix_unitaire = getPrixArticle($article_id);
                $prix_total = $prix_unitaire * $quantite;
                $montant_total += $prix_total;
                
    
                // Alternance de couleurs de fond
                $bgColor = ($ligneCount % 2 == 0) ? '#e4e4e4' : '#dbdbdb';
    
                // Afficher les détails de l'article dans le tableau
                echo '<tr style="background-color: ' . $bgColor . ';">';
                echo '<td>' . $result['id_art'] . '</td>';
                echo '<td style="padding-left:10px; text-decoration:none;"><a id="article" href="articles/article.php?id_art=' . $result['id_art'] . '">' . $result['nom'] . '</a></td>';
                echo '<td>' . $prix_unitaire . ' €</td>';
                echo '<td>' . $quantite . '</td>';
                echo '<td>' . $prix_total . ' €</td>';
                echo '</tr>';
    
                $ligneCount++;
            }
        }
        
        // Afficher le montant total de la commande
        echo '<tr><td colspan="4">Montant Total :</td><td>' . $montant_total . ' €</td></tr>';
        
        echo '</table>';
    }

    include('chat.php');

    ?>
</body>
</html>
