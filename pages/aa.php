<?php
    try {
        include("connexionBDD.php");
        
    }
    catch (PDOException $e) {
        echo "ECHEC : " . $e->getMessage(); //en cas d erreur lors de la connexion à la base de données mysql
    }
?>