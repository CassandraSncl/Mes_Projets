<?php
session_start();

$token =bin2hex(random_bytes(32));
$_SESSION['csrf_token']=$token;

require('bd.php');
$nom = $_POST['n'];
    $prenom = $_POST['p'];
    $adresse = $_POST['adr'];
    $numero = $_POST['num'];
    $email = $_POST['mail'];
    $mot_de_passe = $_POST['mdp1'];

function enregistrer($nom, $prenom, $adresse, $numero, $email, $mdp) {
    try {
        require_once('vendor/autoload.php');
        require('stripe.php');
        $bdd = getBD();

        // Vérifier si l'e-mail existe déjà dans la base de données
        $emailQuery = $bdd->prepare("SELECT COUNT(*) as count FROM Clients WHERE mail = ?");
        $emailQuery->execute([$email]);
        $emailResult = $emailQuery->fetch(PDO::FETCH_ASSOC);

        if ($emailResult['count'] > 0) {
            // L'e-mail existe déjà, renvoie une réponse JSON d'erreur
            return json_encode(['success' => false, 'message' => 'L\'adresse email existe déjà.']);
        }

        // Continuer le processus d'enregistrement puisque l'e-mail n'existe pas encore

        $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);
        $query = "INSERT INTO Clients (nom, prenom, adresse, numero, mail, mdp) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $bdd->prepare($query);
        $stmt->execute([$nom, $prenom, $adresse, $numero, $email, $mdpHash]);

        $customer = $stripe->customers->create([
            'email' => $email,
            'name' => $nom
        ]);

        // Stocker l'ID client Stripe dans votre base de données
        $customer_id = $customer->id;
        $query = "UPDATE Clients SET ID_STRIPE = ? WHERE mail = ?";
        $stmt = $bdd->prepare($query);
        $stmt->execute([$customer_id, $email]);

        // Retourner une réponse JSON indiquant le succès
        return json_encode(['success' => true, 'message' => 'Les données ont été enregistrées avec succès.']);
    } catch (PDOException $e) {
        // Retourner une réponse JSON en cas d'erreur
        return json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement des données : ' . $e->getMessage()]);
    }
}


if (isset($_POST['n']) &&
    isset($_POST['p']) &&
    isset($_POST['adr']) &&
    isset($_POST['num']) &&
    isset($_POST['mail']) &&
    isset($_POST['mdp1']) &&
    isset($_POST['mdp2'])) {

    $nom = $_POST['n'];
    $prenom = $_POST['p'];
    $adresse = $_POST['adr'];
    $numero = $_POST['num'];
    $email = $_POST['mail'];
    $mdp1 = $_POST['mdp1'];
    $mdp2 = $_POST['mdp2'];

    if (empty($nom) || empty($prenom) || empty($adresse) || empty($numero) || empty($email) || empty($mdp1) || empty($mdp2) || $mdp1 != $mdp2) {
        // Retourne une réponse JSON indiquant une erreur de validation
        echo json_encode(['success' => false, 'message' => 'Veuillez remplir correctement tous les champs.']);
    } else {
        // Enregistre les données dans la base de données
        $response = enregistrer($nom, $prenom, $adresse, $numero, $email, $mdp1);
        // Retourne la réponse JSON obtenue de la fonction d'enregistrement
        echo $response;
    }
} else {
    // Retourne une réponse JSON indiquant que le formulaire n'est pas complet
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir le formulaire.']);
}
?>
