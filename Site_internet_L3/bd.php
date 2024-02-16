<?php
    function getBD(){
        try {
            $bdd = new PDO('mysql:host=localhost;dbname=librairie_en_ligne;charset=utf8','root', '');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $bdd;
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            return null;
        }
    }
?>

