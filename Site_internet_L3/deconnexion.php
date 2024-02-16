<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles/styles.css" type="text/css" media="screen" />
    <title>Page de Connexion</title>
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
    

<div class="wrapper">
            <div class = "slide">
                <ul>
                    <?php
                        if (isset($_SESSION['client'])) {
                            $prenom = $_SESSION['client']['prenom'];
                            $nom = $_SESSION['client']['nom'];
                            echo '<li id="Bonjour"> Bonjour ' . $prenom . ' ' . $nom .'</p>';
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
    session_start();

    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['client'])) {
        // Inclure le fichier de connexion à la base de données
        require('bd.php');
        
        // Récupérer le panier actuel
        $panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : array();

        try {
            // Démarrez une transaction pour garantir que toutes les opérations réussissent ou échouent ensemble
            $bdd = getBD();
            $bdd->beginTransaction();

            // Parcourir chaque article dans le panier et restaurer la quantité dans la base de données
            foreach ($panier as $article) {
                $article_id = $article['id'];
                $quantite = $article['quantite'];

                // Mettre à jour la quantité des articles en stock
                $sql = "UPDATE librairie SET quantite = quantite + :quantite WHERE id_art = :article_id";
                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':quantite', $quantite);
                $stmt->bindParam(':article_id', $article_id);
                $stmt->execute();
            }

            // Valider la transaction
            $bdd->commit();

            // Vider le panier du client
            unset($_SESSION['panier']);
        } catch (PDOException $e) {
            // En cas d'erreur, annuler la transaction
            $bdd->rollback();
            echo "Une erreur s'est produite lors de la restauration du stock : " . $e->getMessage();
        }
    }

    // Détruire la session après avoir effectué les opérations nécessaires
    session_destroy();
?>

<p style="margin-left: 10px;">Vous venez de vous déconnecter...</p>
</body>
</html>