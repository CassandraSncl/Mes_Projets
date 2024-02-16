<?php 
session_start(); 
// Génération et stockage du token CSRF
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;
?>

<!DOCTYPE html>
<html lang="fr">

<html >
    <head>
        <link rel="stylesheet" href="styles/styles.css" type="text/css" media="screen" />
        <title>Acheter</title>
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

            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['client'])) {
                // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
                header("Location: connexion.php");
                exit();
            }

            // Inclure le fichier de connexion à la base de données
            require('bd.php');

            // Récupérer le panier depuis la session
            $panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : array();

            if (!empty($panier)) {
                try {
                    // Démarrez une transaction pour garantir que toutes les opérations réussissent ou échouent ensemble
                    $bdd = getBD();
                    $bdd->beginTransaction();

                    // Parcourez chaque article dans le panier et ajoutez-le à la table Commandes
                    foreach ($panier as $article) {
                        $article_id = $article['id'];
                        $quantite = $article['quantite'];

                        // Insérez l'article dans la table Commandes
                        $sql = "INSERT INTO Commandes (id_art, id_client, quantite) VALUES (:id_art, :id_client, :quantite)";
                        $stmt = $bdd->prepare($sql);
                        $stmt->bindParam(':id_art', $article_id);
                        $stmt->bindParam(':id_client', $_SESSION['client']['id_client']);
                        $stmt->bindParam(':quantite', $quantite);
                        $stmt->execute();
                    }

                    // Validez la transaction
                    $bdd->commit();

                    // Videz le panier du client
                    unset($_SESSION['panier']);

                    echo "<p style='margin:50px; font-size:20px;'> Votre commande a bien été enregistrée.</p>";
                } catch (PDOException $e) {
                    // En cas d'erreur, annulez la transaction
                    $bdd->rollback();
                    echo "Une erreur s'est produite lors de l'enregistrement de la commande : " . $e->getMessage();
                }
            } else {
                echo "Votre panier est vide. <a href='index.php' style='text-decoration: none; color:black;'>Retour à la page d'accueil</a>";
            }

            include('chat.php');

        ?>


    </div>

</body>

</html>