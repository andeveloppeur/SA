<?php
            $serveur = "localhost";
            $Monlogin = "root";
            $Monpass = "101419";
            $connexion = new PDO("mysql:host=$serveur;dbname=SA;charset=utf8", $Monlogin, $Monpass); //se connecte au serveur mysquel
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //setAttribute — Configure l'attribut PDO $connexion
?>