<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <!-- Incluez jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<!-- Balises pour la fenêtre de discussion -->
<div id="chat-window">
    <div id="chat-header">Discussion</div>
    <div id="chat-body"></div>
    
    <?php
    if (isset($_SESSION['client']) && isset($_SESSION['csrf_token'])) :
    ?>
        <!-- Affichez le champ de saisie et le bouton uniquement si l'utilisateur est connecté -->
        <input type="text" id="message-input" placeholder="Entrez votre message">
        <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button onclick="sendMessage()">Envoyer</button>
        <div id="error-message" style="color: red;"></div>
    <?php else : ?>
        <p style="color:red;">Vous devez être connecté pour accéder au chat.</p>
    <?php endif; ?>
</div>


<script>
    function updateChatWindow() {
        var chatBody = $('#chat-body');
        var lastDisplayedId = chatBody.data('lastDisplayedId') || 0;
        $.ajax({
            url: 'http://localhost/Senecaille/recup_message.php',
            type: 'POST',
            data: { lastDisplayedId: lastDisplayedId },
            success: function(response) {
                var messages = response.split('<br>').filter(function(item) {
                    return item.trim() !== '';
                });
                chatBody.empty();
                if (messages.length > 0) {
                    messages.forEach(function(message) {
                        chatBody.append("<span>"+message+"</span> <br>")
                    });
                    chatBody.data('lastDisplayedId', parseInt(messages[messages.length - 1].split(':')[0]));
                }
            }
        });
    }

    // Fonction pour initier la première mise à jour et déclencher les mises à jour ultérieures
    function startUpdates() {
        updateChatWindow();
        $(document).on('newMessage', function() {
            updateChatWindow();
        });
    }

    // On déclare une variable globale pour stocker le score du message actuel
    var currentMessageScore = 0;

    // Déclarez une variable globale pour le seuil
    var threshold = 100;

    // Fonction pour envoyer un message et déclencher la mise à jour
    function sendMessage() {
        var messageInput = $('#message-input');
        var errorMessage = $('#error-message');
        var message = messageInput.val();
        var csrfToken = $('#token').val();

        // Vérifier le score du message avant l'envoi
        if (currentMessageScore > threshold) {
            // Afficher l'erreur sans vider l'input
            errorMessage.text("Erreur: Message filtré en raison du score TF-IDF élevé.");
            return;
        }

        $.ajax({
            url: 'http://localhost/Senecaille/envoyer_message.php',
            type: 'POST',
            data: {
                message: message,
                csrf_token: csrfToken
            },
            success: function(response) {
                var lastInsertedId = parseInt(response);
                $(document).trigger('newMessage');
                messageInput.val(''); // Vider l'input après l'envoi réussi
                errorMessage.text('');
            },
            error: function(xhr, status, error) {
                // Afficher l'erreur complète dans la console du navigateur
                console.error("Erreur AJAX:", xhr.responseText);
                // Afficher un message d'erreur générique dans la div #error-message
                errorMessage.text("Erreur lors de l'envoi du message.");
            }
        });
    }


    // Appeler la fonction startUpdates au chargement du document
    $(document).ready(function() {
        startUpdates();
    });

</script>

</body>
</html>
