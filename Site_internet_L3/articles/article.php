<?php
session_start();
// Génération et stockage du token CSRF
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/styles.css" type="text/css" media="screen" />
    <title>Article</title>
</head>
<body>

<?php
require('../bd.php');
$bdd = getBD();
$sql = "SELECT * FROM librairie";
$resultat = $bdd->query($sql);
?>

<header class="head">
    <nav>
        <img src="../images/logo.jpeg" alt="Logo" id="logo"></a>
        <h1 id="titre">Librairie en ligne</h1>
        <ul>
            <li><a class="acc" href="../index.php">Accueil </a></li>
            <li><a class="acc" href="../contact/contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="wrapper">
    <div class="slide">
        <ul>
            <?php
            if (isset($_SESSION['client'])) {
                $prenom = $_SESSION['client']['prenom'];
                $nom = $_SESSION['client']['nom'];
                echo '<li id="Bonjour" style="list-style: none; color:grey; font-size:20px;"> Bonjour ' . $prenom . ' ' . $nom .'</p>';
                echo "<li class='navbarItem'> <a style='text-decoration: none; color:white; padding:3px; text-align:center'  href='../panier.php'> Panier </a> </li> ";
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
if (isset($_GET['id_art'])) {
    $id_art = $_GET['id_art'];

    // Requête SQL pour récupérer les informations de l'article
    $query = $bdd->prepare("SELECT * FROM librairie WHERE id_art = :id");
    $query->bindParam(':id', $id_art);
    $query->execute();

    // Vérifier si l'article a été trouvé
    $article = $query->fetch(PDO::FETCH_ASSOC);
    if (!$article) {
        // Rediriger vers une page d'erreur ou afficher un message
        echo '<p style="margin-left:20px; color:red; font-size:20px;"> Article non trouvé. </p>';
        exit;
    }

    // On affiche les informations de l'article
    echo '<h2 style="margin-left:2%; text-align: center; font-size: 25px;">' . $article['nom'] . '</h2>';
    echo '<img style="margin-left:43%; width:15%;" src="' . $article['url_photo'] . '" alt="' . $article['nom'] . '">';
    echo '<p style="margin-left:48%; text-align: left; font-size: 20px;"> Prix : ' . $article['prix'] . '</p>';
    echo '<p style="margin-left:48%; text-align: left; font-size: 20px;"> Quantité : ' . $article['quantite'] . '</p>';
    echo '<p style="margin-left:25%; max-width:50%; text-align: center; font-size: 20px;"> Description : ' . $article['description'] . '</p>';
} else {
    echo 'Identifiant d\'article non spécifié.';
}
?>

<?php
// On vérifie si l'utilisateur est connecté
if (isset($_SESSION['client'])) {
    $prenom = $_SESSION['client']['prenom'];
    $nom = $_SESSION['client']['nom'];
    $article_id = 'id_art';

    if ($article['quantite'] > 0){
?>

<form action="../ajouter.php" method="POST">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <!-- Champ caché pour l'identifiant de l'article -->
    <input type="hidden" name="id_art" value="<?php echo $id_art; ?>">
    <!-- Champ pour le nombre d'exemplaires -->
    <label style="margin-left:45%; font-size: 20px;" for="quantite">Nombre d'exemplaires :</label>
    <input style="margin-left:45%;" type="number" name="quantite" id="quantite" value="1" min="1" max="<?php echo $article['quantite']; ?>" required><br><br>
    <!-- Bouton d'ajout au panier -->
    <input style="margin-left:45%; background-color: grey; padding:5px; color: white; font-size:20px;"  type="submit" value="Ajoutez à votre panier">
</form>

<?php
    } else {
        echo '<p style="color:red; text-align:center;"> Cet article n\'est plus disponible actuellement . </p>';
    }
} else {
    echo '<p style="color:red; text-align:center;"> Vous devez être connecté pour ajouter des articles à votre panier. </p>';
}

include('../chat.php');
?>

</body>
</html>
