<?php
    session_start();

    require('bd.php');
    
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['client'])) {
        // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
        header("Location: connexion.php");
        exit();
    }

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

        // Rediriger l'utilisateur vers la page du panier
        header("Location: panier.php");
        exit();
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction
        $bdd->rollback();
        echo "Une erreur s'est produite lors de la restauration du stock : " . $e->getMessage();
    }

?>
