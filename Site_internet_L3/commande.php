<?php /**On démarre une session active */
    
    require_once('vendor/autoload.php');
    require('stripe.php');

    session_start();
    
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['client'])) {
        // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
        header("Location: connexion.php");
        exit();
    }

    // Inclure le fichier de connexion à la base de données
    require('bd.php');

    // Créer un tableau pour stocker le contenu du panier
    $panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : array();

    // Fonction pour récupérer le prix d'un article depuis la base de données
    function getPrixArticle($article_id) {
        $sql = "SELECT prix FROM librairie WHERE id_art = :id";
        
        $bdd = getBD();
        $requete = $bdd->prepare($sql);
        $requete->bindParam(':id', $article_id);
        $requete->execute();
        
        $result = $requete->fetch();
        return $result['prix'];
    }

    function getIdArticle($article_id) {
        $sql = "SELECT ID_STRIPE FROM librairie WHERE id_art = :id";
        
        $bdd = getBD();
        $requete = $bdd->prepare($sql);
        $requete->bindParam(':id', $article_id);
        $requete->execute();
        
        $result = $requete->fetch();
        return $result['ID_STRIPE'];
    }


        // Créer un tableau pour les articles dans le format attendu par Stripe
        // Récupérer l'ID de l'utilisateur connecté dans votre base de données
    $id_utilisateur = $_SESSION['client']['ID_STRIPE'];

    // Créer un tableau pour les articles dans le format attendu par Stripe
    $line_items = [];
    $montant_total = 0;

    foreach ($panier as $article) {
        if (isset($article['quantite'])) {
            $quantite = $article['quantite'];
            $article_id = getIdArticle($article["id"]);

            // Ajouter l'article au tableau des items
            $line_items[] = [
                'price' => $article_id, // Remplacez par l'ID du produit dans Stripe
                'quantity' => $quantite,
            ];
        }
    }

    // Créer une session de paiement avec Stripe
    $checkout_session = $stripe->checkout->sessions->create([
        'customer' => $id_utilisateur,
        'success_url' => 'http://localhost/senecaille/acheter.php',
        'cancel_url' => 'http://localhost/senecaille/erreur.php',
        'line_items' => $line_items,
        'mode' => 'payment',
        'automatic_tax' => ['enabled' => false],
        
    ]);

    // Redirection vers la page de paiement Stripe
    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
?>

