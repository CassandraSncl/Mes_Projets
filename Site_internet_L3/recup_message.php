<?php
session_start();

require('bd.php');
$bdd = getBD();
// On récupére le dernier ID affiché
$lastDisplayedMessageId = isset($_GET['lastDisplayedId']) ? $_GET['lastDisplayedId'] : 0;

$deleteSql = "DELETE FROM messages WHERE time < NOW() - INTERVAL 10 MINUTE";
$bdd->exec($deleteSql);

// On sélectionne les messages avec un ID supérieur au dernier affiché
$selectSql = "SELECT clients.prenom, messages.id_client, messages.message, messages.time
    FROM messages
    JOIN clients ON clients.id_client = messages.id_client
    WHERE messages.id_message > :lastDisplayedMessageId
    ORDER BY messages.id_message ASC";

$stmt = $bdd->prepare($selectSql);
$stmt->bindParam(':lastDisplayedMessageId', $lastDisplayedMessageId, PDO::PARAM_INT);
$stmt->execute();

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// On formate les messages comme nécessaire
$formattedMessages = array_map(function ($message) {
$prenom = isset($message['prenom']) ? $message['prenom'] : '';
$time = isset($message['time']) ? $message['time'] : '';
$formattedMessage = '<span> <strong>' .  htmlspecialchars($prenom) . '</strong></span>  dit \'' .  htmlspecialchars($message['message']) . '\' <span style="color: #888; font-size: 10px;">   [' . $time . ']';
return $formattedMessage;
}, $messages);


echo implode('<br>', $formattedMessages);


?>
