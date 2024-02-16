<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification du jeton CSRF
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        // Le jeton est valide, traitez les données du formulaire d'ajout au panier
        // Récupérez les données du formulaire
        $id_art = $_POST["id_art"];
        $quantite = $_POST["quantite"];

        // Vérifiez si le panier existe, sinon initialisez-le
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = array();
        }

        require('bd.php');
        $bdd = getBD();

        // Vérifiez si l'article est déjà dans le panier
        $article_existe = false;
        foreach ($_SESSION['panier'] as &$article) {
            if ($article['id'] == $id_art) {
                $article['quantite'] += $quantite;
                $article_existe = true;
                
                // Mettez à jour la quantité dans la base de données
                $sql_update = "UPDATE librairie SET quantite = quantite - :quantite WHERE id_art = :article_id";
                $stmt_update = $bdd->prepare($sql_update);
                $stmt_update->bindParam(':quantite', $quantite);
                $stmt_update->bindParam(':article_id', $id_art);
                $stmt_update->execute();
            }
        }

        // Si l'article n'est pas dans le panier, on l'ajoute
        if (!$article_existe) {
            // Récupérez la quantité originale de l'article depuis la base de données
        
            $query = $bdd->prepare("SELECT quantite FROM librairie WHERE id_art = :article_id");
            $query->bindParam(':article_id', $id_art);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);

            // Vérifiez si la quantité disponible est suffisante
            if ($result && $result['quantite'] >= $quantite) {
                // Ajoutez l'article avec la quantité au panier
                $nouvel_article = array(
                    'id' => $id_art,
                    'quantite' => $quantite,
                );
                $_SESSION['panier'][] = $nouvel_article;

                // Mettez à jour la quantité dans la base de données
                $sql_update = "UPDATE librairie SET quantite = quantite - :quantite WHERE id_art = :article_id";
                $stmt_update = $bdd->prepare($sql_update);
                $stmt_update->bindParam(':quantite', $quantite);
                $stmt_update->bindParam(':article_id', $id_art);
                $stmt_update->execute();
            } else {
                // La quantité n'est pas suffisante, affichez un message d'erreur ou prenez d'autres mesures
                echo "La quantité demandée n'est pas disponible.";
            }
        }
    } else {
        // Le jeton CSRF est invalide, vous pouvez afficher une erreur ou prendre d'autres mesures
        die("Tentative d'attaque CSRF détectée.");
    }
}

// Redirigez l'utilisateur vers la page d'accueil (index.php)
header("Location: index.php");
exit();
?>
