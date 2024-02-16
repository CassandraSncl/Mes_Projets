<?php
    session_start();
?>

<?php
    // Génération du jeton CSRF
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<meta charset="utf-8">
<html lang="fr">

</html>
<html >
    <head>
        <link rel="stylesheet" href="styles/styles.css" type="text/css" media="screen" />
        <title>Librairie en ligne</title>
    </head>
    <body >

        <?php
                /**On appelle la base de données depuis le fichier bd.php
                 * On appelle ensuite la fonction qui contient le PDO
                 * Pour finir on créer une requête SQL qui permet de chercher dans la table librairie toutes les lignes.
                 */
                require('bd.php');
                $bdd = getBD();
                $sql = "SELECT * FROM librairie";
                $resultat = $bdd->query($sql);

        ?>

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
    

        <h2>Articles en stock</h2>
          
        
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
					        echo "<li class='navbarItem'><a style='text-decoration: none, color:white;  padding:3px; text-align:center;' href='deconnexion.php'>Se déconnecter</a></li>";
                        } else {
                            echo "	<li class='navbarItem''><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='nouveau.php'>Nouveau Client</a></li>";
                            echo "	<li class='navbarItem'><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='connexion.php'></i>Se connecter</a></li>";
                    };
                    ?>
                </ul>
            </div>
        </div>

        <?php
        // On vérifie s'il y a des données à afficher
        if ($resultat->rowCount() > 0) {
            echo '<div style="margin-left: 10%; padding: 10px; max-width: 70%;">';
            
            // En-têtes de colonnes
            echo '<table style="border-collapse: collapse; width: 100%; margin-top: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <tr style="background-color: #707070; color: #fff;">
                        <th style="padding: 12px; margin-left:20px;">ID</th>
                        <th style="padding: 12px; margin-left:20px;">Nom</th>
                        <th style="padding: 12px; margin-left:20px;">Description</th>
                        <th style="padding: 12px; margin-left:20px;">Prix</th>
                        <th style="padding: 12px; margin-left:20px;">Quantité</th>
                    </tr>';
            
            // On parcoure les résultats et affichez chaque ligne dans le tableau
            $ligneCount = 0; // Ajout d'un compteur de lignes
            while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
                $ligneCount++;
                $bgColor = ($ligneCount % 2 == 0) ? '#e4e4e4' : '#dbdbdb'; // Alternance de couleurs de fond
                
                echo '<tr style="background-color: ' . $bgColor . ';">';
                echo '<td>' . $ligne['id_art'] . '</td>';
                echo '<td style="padding-left:10px;"><a id="article" href="articles/article.php?id_art=' . $ligne['id_art'] . '">' . $ligne['nom'] . '</a></td>';
                $description = substr($ligne['description'], 0, 100);
                echo '<td style="padding:10px;">' . $description . '...</td>';
                echo '<td style="padding:10px;">' . $ligne['prix'] . '</td>';
                echo '<td style="padding:25px;">' . $ligne['quantite'] . '</td>';
                echo '</tr>';
            }

                echo '</table>';
            } else {
                echo 'Aucun article trouvé dans la base de données.';
            }

        include('chat.php');
            // On ferme la connexion à la base de données
            $resultat->closeCursor();
        ?>

    </body>
</html>
