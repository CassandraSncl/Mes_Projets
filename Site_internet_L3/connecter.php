<?php
// Démarrer la session
session_start();
require('bd.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification du jeton CSRF
    
        // Le jeton est valide, traitez les données du formulaire de connexion
        // Récupérer les données du formulaire
        $mail = $_POST["mail"];
        $mdp = $_POST["mdp"];

        // Vérifier l'existence de l'utilisateur dans la base de données
        $bdd = getBD();
        $requete = $bdd->prepare("SELECT * FROM clients WHERE mail = :mail");
        $requete->bindParam(':mail', $mail);
        $requete->execute();
        $utilisateur = $requete->fetch();

        if ($utilisateur && password_verify($mdp, $utilisateur['mdp'])) {
            // Créer une variable de session contenant les informations de l'utilisateur
            $_SESSION['client'] = $utilisateur;

            // Rediriger vers la page d'accueil (index.php) ou renvoyer une réponse JSON
            echo json_encode(['success' => true, 'message' => 'Connexion réussie']);
            exit();
        } else {
            // Si les informations sont incorrectes, renvoyer une réponse JSON avec un message d'erreur
            echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
            exit();
        }

} else {
    // Si les données du formulaire n'ont pas été soumises, renvoyer une réponse JSON avec un message d'erreur
    echo json_encode(['success' => false, 'message' => 'Données du formulaire manquantes.']);
    exit();
}
?>
