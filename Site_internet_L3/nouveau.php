<?php
    session_start();

    // Génération et stockage du jeton CSRF
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;

    // Connexion à la base de données
    require('bd.php');

?>

<!DOCTYPE html>
<meta charset="utf-8">
<html lang="fr">

</html>
<html >
    <head>
        <link rel="stylesheet" href="styles/styles.css" type="text/css" media="screen" />
        <title>Librairie en ligne</title>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <script>
        $(document).ready(function() {

            // Fonction pour mettre à jour le style du champ en fonction de sa validité
            function updateFieldStatus(field, isValid, message) {
                var $field = $(field);
                if (isValid) {
                    $field.css({
                        'border': '2px solid green',
                        'background-color': '#c8e6c9' // Fond vert clair
                    });
                    $field.next('.error-message').text('');
                } else {
                    $field.css({
                        'border': '2px solid red',
                        'background-color': '#ffcdd2' // Fond rouge clair
                    });
                    $field.next('.error-message').text(message);
                }
            }


            // Fonction pour valider l'email
            function isValidEmail(mail) {
                // On utilise une expression régulière pour valider l'email (aaa@aaa.aaa)
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var ValidEmail = emailRegex.test(mail);
                if(ValidEmail){
                    $.ajax({
                        url: 'http://localhost/senecaille/check_email.php',
                        method: 'POST',
                        data: { mail: mail },
                        dataType:'json',
                        success: function (data) {
                            var exists= data.exists;
                            var msg= exists ? 'mail existe':'';
                            updateFieldStatus($('input[name="mail"]'),!exists,msg)
                        },
                        error: function () {
                            console.error("Erreur lors de la vérification de l'adresse e-mail.");
                            reject();
                        }
                    });
                }
                else{
                    updateFieldStatus($('input[name="mail"]'),false,"mail non valide")
                }

            }

            // Fonction pour valider le mot de passe
            function isValidPassword(password) {
                // On utilise une expression régulière pour valider le mot de passe (8 caractères, un chiffre et un spécial)
                var passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                return passwordRegex.test(password);
            }

            // Fonction pour valider le nom et prénom (lettres et accents)
            function isValidName(name) {
                var nameRegex = /^[a-zA-ZÀ-ÖØ-öø-ÿ\s']+$/;
                return nameRegex.test(name);
            }

            function isValidAdr(adr) {
                var adrRegex = /^[a-zA-Z0-9\s,'-]*$/;
                return adrRegex.test(adr);
            }

            // Fonction pour valider le numéro (chiffres, 10 caractères)
            function isValidNumber(number) {
                var numberRegex = /^[0-9]{10}$/;
                return numberRegex.test(number);
            }

            // Fonction pour vérifier tous les champs avant de soumettre le formulaire
            function validateForm() {
                var isValid = true;

                function validateField(field, validationFunction, errorMessage) {
                    var $field = $(field);
                    var value = $field.val();
                    var fieldIsValid = validationFunction(value);

                    if (fieldIsValid) {
                        updateFieldStatus($field, true, '');
                    } else {
                        updateFieldStatus($field, false, errorMessage);
                        isValid = false;
                    }

                    return fieldIsValid;
                }

                var nomIsValid = validateField('input[name="n"]', isValidName, 'Le nom doit contenir uniquement des lettres et des accents.');
                var prenomIsValid = validateField('input[name="p"]', isValidName, 'Le prénom doit contenir uniquement des lettres et des accents.');
                var adrIsValid = validateField('input[name="adr"]', isValidAdr, 'L\'adresse doit contenir uniquement des lettres, des chiffres, des espaces et des tirets.');
                var numIsValid = validateField('input[name="num"]', isValidNumber, 'Le numéro doit contenir 10 chiffres.');
                var mailIsValid = validateField('input[name="mail"]', isValidEmail, 'L\'adresse e-mail n\'est pas valide ou déjà utilisée.');
                var mdp1IsValid = validateField('input[name="mdp1"]', isValidPassword, 'Le mot de passe doit contenir au moins 8 caractères, une lettre, un chiffre et un caractère spécial.');
                var mdp2IsValid = validateField('input[name="mdp2"]', function(value) {
                    // Ajoutez la logique de validation supplémentaire pour le champ de confirmation du mot de passe
                    return value === $('input[name="mdp1"]').val();
                }, 'Les mots de passe ne correspondent pas.');

                return isValid;
            }

            $('input[name="n"], input[name="p"], input[name="adr"], input[name="num"], input[name="mail"], input[name="mdp1"], input[name="mdp2"]').on('input', function() {
                validateForm();
            });

            $('form').on('submit', function(event) {
                // Empêcher la soumission du formulaire si la validation échoue
                if (!validateForm()) {
                    event.preventDefault();
                } else {
                    // Réactiver le bouton d'envoi si tous les champs sont à nouveau valides
                    $('input[name="inscription"]').prop('disabled', false);

}
});

        });
    </script>


    </head>
    <body >

	<?php
        /**On appelle la base de données depuis le fichier bd.php
        * On appelle ensuite la fonction qui contient le PDO
        * Pour finir on créer une requête SQL qui permet de chercher dans la table librairie toutes les lignes.
        */
        $bdd = getBD();
        $sql = "SELECT * FROM librairie";
        $resultat = $bdd->query($sql);
    ?>

    <!-- On crée une nav barre de navigation -->
    <header class="head">
        <nav>
            <img src="images/logo.jpeg" alt="Logo" id="logo"></a>
            <h1 id="titre">Librairie en ligne</h1>
            <ul>
                <li><a class="acc" href="index.php">Accueil </a></li>
                <li><a class="acc" href="contact/contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- On crée une barre de navigation verticale.
        Elle comprend quand le client n'est pas connecté:
            -Un lien vers la page inscription
            - Un lien vers la page connexion
        Quand le client est connecté:
            - Un lien vers la page Deconnexion
            - Un lien vers le panier
            - Un message "Bonjour <'nom'> <'prenom'>
        -->
        <div class="wrapper">
            <div class = "slide">
                <ul>
                    <?php
                        if (isset($_SESSION['client'])) {
                            $prenom = $_SESSION['client']['prenom'];
                            $nom = $_SESSION['client']['nom'];
                            echo '<li id="Bonjour" style="list-style: none; color:grey; font-size:20px;"> Bonjour ' . $prenom . ' ' . $nom .'</p>';
                            echo "<li class='navbarItem'> <a style='text-decoration: none; color:white; padding:3px; text-align:center;'  href='panier.php'> Panier </a> </li> ";
                            echo "	<li class='navbarItem'><a  style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='historique.php'>Historique des commandes</a></li>";
					        echo "	<li  class='navbarItem'><a style='text-decoration: none, color:white;  padding:3px; text-align:center;' href='deconnexion.php'>Se déconnecter</a></li>";
                        } else { 
                            echo "	<li class='navbarItem''><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='nouveau.php'>Nouveau Client</a></li>";
                            echo "	<li class='navbarItem'><a style='text-decoration: none; color:white;  padding:3px; text-align:center;' href='connexion.php'></i>Se connecter</a></li>";
                    };
                    ?>
                </ul>
            </div>
        </div>

    
	
    
	<h2 >Nouveau Client: </h2>
		<form id="registrationForm" action="enregistrement.php" method="post" autocomplete="on" class="formNV">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <table  >
			<tr>
			<td>Nom : </td>
			<td> <input type="text" placeholder="Votre nom" name="n" value="<?php if (isset($_GET['n'])) {
																				echo htmlentities($_GET['n']);
																			} ?>" />
            </td>
            

			</tr>
            <tr>
                <td colspan="2">
                    <span class="validation-message"></span>
                </td>
            </tr>
			<tr >

			<td>Prénom : </td>
			<td> <input type="text" placeholder="Votre prénom" name="p" value="<?php if (isset($_GET['p'])) {
																					echo htmlentities($_GET['p']);
																				} ?>" />
                </td>
			</tr>
            <tr>
                <td colspan="2">
                    <span class="validation-message"></span>
                </td>
            </tr>
			<tr>

			<td>Adresse : </td>
			<td> <input type="text" placeholder="Votre adresse" name="adr" value="<?php if (isset($_GET['adr'])) {
																					echo htmlentities($_GET['adr']);
																					} ?>" />
            </td>
			</tr>
            <tr>
                <td colspan="2">
                    <span class="validation-message"></span>
                </td>
            </tr>
			<tr>

			<td>Numéro de téléphone : </td>
			<td> <input type="text" placeholder="Votre numéro" name="num" value="<?php if (isset($_GET['num'])) {
																					echo htmlentities($_GET['num']);
																					} ?>" />
            </td>
			</tr>
            <tr>
                <td colspan="2">
                    <span class="validation-message"></span>
                </td>
            </tr>
			<tr>

			<td>Adresse mail : </td>
			<td> <input type="email" placeholder="Votre mail" name="mail" value="<?php if (isset($_GET['mail'])) {
																					echo htmlentities($_GET['mail']);
																					} ?>" />
            </td>
			</tr>
            <tr>
                <td colspan="2">
                    <span class="validation-message"></span>
                </td>
            </tr>
			<tr>


			<td>Mot de passe : </td>
			<td> <input type="password" placeholder="Votre mot de passe" name="mdp1" value="<?php if (isset($_GET['mdp1'])) {
																						echo htmlentities($_GET['mdp1']);
																					} ?>" />
            </td>
			</tr>
            <tr>
                <td colspan="2">
                    <span class="validation-message"></span>
                </td>
            </tr>
			<tr>
			<td>Confirmer votre mot de passe : </td>
			<td> <input type="password" placeholder="Confirmer le mot de passe" name="mdp2" />
            </td>
			</tr>
            <tr>
                <td colspan="2">
                    <span class="validation-message"></span>
                </td>
            </tr>

			<tr>
			<td></td>
			<td><input type="submit" name="inscription" value="OK"></td>
			</tr>
		</table>
		</form>

        <script>
    $(document).ready(function () {
    // On capture l'événement de soumission du formulaire
    $('#registrationForm').on('submit', function (event) {
        // On empêche la soumission du formulaire par défaut
        event.preventDefault();

        // On récupére les données du formulaire
        var formData = $(this).serialize();

        $.ajax({
            url: 'enregistrement.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                console.log(response);

                if (response.success) {
                    var email = $('input[name="mail"]').val();
                    var password = $('input[name="mdp1"]').val();
                    $.ajax({
                    url: 'connecter.php',
                    method: 'POST',
                    data: { mail: email, mdp: password },
                    dataType: 'json',
                    success: function (loginResponse) {
                    if (loginResponse.success) {
                    window.location.href = 'index.php';
                    } else {
                    }
            },
            error: function () {
                console.error("Erreur lors de la connexion automatique.");
            }
        });
    } else {
        alert('Erreur: ' + response.message);
    }
},
            error: function () {
                console.error("Erreur lors de l'envoi des données au script enregistrement.php.");
            }
        });
    });
});

include('chat.php');

</script>

    </body>
</html>