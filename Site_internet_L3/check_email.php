<?php

require('bd.php');

if (isset($_POST['mail'])) {
    $email = $_POST['mail'];

    // Connexion à la base de données
    $bdd = getBD();

    // Vérifier si l'e-mail existe déjà dans la base de données
    $query = $bdd->prepare("SELECT COUNT(*) as count FROM clients WHERE mail = ?");
    $query->execute([$email]);
    $result = $query->fetch(PDO::FETCH_ASSOC);

    // Renvoyer le résultat au format JSON
    echo json_encode(['exists' => $result['count'] > 0]);
}
?>
