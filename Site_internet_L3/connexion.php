<?php
    session_start();

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

  // Vérification du jeton CSRF
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
          // Le jeton est valide, traitez les données du formulaire de connexion
        } else {
          // Le jeton CSRF est invalide, vous pouvez afficher une erreur ou prendre d'autres mesures
            die("Tentative d'attaque CSRF détectée.");
        }
    }

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles/styles.css" type="text/css" media="screen" />
    <title>Page de Connexion</title>
</head>
<body>
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

    <h2>Connexion</h2>

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
    
    <div class='container-connexion' >
        <form action="connecter.php" method="post" class="formNV">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <table>

                <tr>
                    <td>E-mail : </td>
                    <td> <input type="text" placeholder="Votre mail" name="mail" value="<?php if (isset($_GET['mail'])) {
                                                                                        echo htmlentities($_GET['mail']);
                                                                                    } ?>" /></td>
                </tr>
                <tr>
                    <td>Mot de passe : </td>
                    <td> <input type="password" placeholder="Votre mot de passe" name="mdp" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="connexion" value="OK"></td>
                </tr>

            </table>

        </form>

    </div>

    <p class="nv">Si vous n'avez pas de compte, veuillez créer un <a href="nouveau.php">Compte</a></p>


</body>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function() {
    // On capture l'événement de soumission du formulaire
    $('form').on('submit', function(event) {
        // On empêche la soumission du formulaire par défaut
        event.preventDefault();
        // On récupére les données du formulaire
        var formData = $(this).serialize();
        // On ffectue la requête Ajax
        $.ajax({
            url: 'connecter.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = 'index.php';
                } else {
                    alert('Erreur de connexion : ' + response.message);
                }
            },
            error: function() {
                console.error("Erreur lors de la requête Ajax vers connecter.php.");
            }
        });
    });
});

</script>

</html>
