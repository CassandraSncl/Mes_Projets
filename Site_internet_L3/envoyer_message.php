<?php
session_start();

if (!(isset($_POST['csrf_token']) && $_POST['csrf_token'] == $_SESSION['csrf_token'])) {
    echo "<p style='color: red;'>Jeton CSRF invalide</p>";
    exit; // Pas besoin de header() ici car vous ne souhaitez pas rediriger
}

if (!isset($_SESSION['client'])) {
    echo "<p style='color: red;'>Utilisateur non connecté</p>";
    exit; // Pas besoin de header() ici car vous ne souhaitez pas rediriger
}

require('bd.php');
$bdd = getBD();
$user_id = $_SESSION['client']['id_client'];
$message = trim($_POST['message']);

// On vérifie si le message n'est pas vide
if (!empty($message) && strlen($message) <= 256) {
    $scoreMapFile = 'score_map.json';
    $scoreMap = json_decode(file_get_contents($scoreMapFile), true);

    $messageWords = preg_split("/\s+/", $message);
    $totalScore = 0;
    foreach ($messageWords as $word) {
        if (isset($scoreMap[$word])) {
            $totalScore += $scoreMap[$word];
        }
    }

    $isOffensive = $totalScore < 0;

    if (!$isOffensive) {
        $sql = "INSERT INTO messages (id_client, message) VALUES (?, ?)";
        $stmt = $bdd->prepare($sql);
        $stmt->execute([$user_id, $message]);
        $lastInsertedId = $bdd->lastInsertId();
        echo $lastInsertedId;
    } else {
        // Message considéré comme offensant, renvoie une réponse d'erreur
        print "<p style='color: red;'>Attention: Le message est considéré comme offensant.</p>";
    }
} else {
    // Message vide ou trop long, on renvoie une réponse d'erreur
    echo "<p style='color: red;'>Erreur : Le message est vide ou dépasse la limite de 256 caractères.</p>";
}
?>
