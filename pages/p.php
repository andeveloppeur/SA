<?php
    try {
        $serveur = "localhost";
        $Monlogin = "root";
        $Monpass = "101419";
        $connexion = new PDO("mysql:host=$serveur;dbname=SA;charset=utf8", $Monlogin, $Monpass); //se connecte au serveur mysquel
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //setAttribute — Configure l'attribut PDO $connexion
        $codemysql = "INSERT INTO `emargement` (NCI,Date_emargement,Arrivee,Depart)
                        VALUES(:NCI,:Date_emargement,:Arrivee,:Depart)"; //le code mysql
        $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution 
        $monfichier = fopen('emargement.txt', 'r');
        $referentiel="";
        while (!feof($monfichier)) {
            $ligne = fgets($monfichier);
            $etudiant = explode('|', $ligne);
            $requete->bindParam(":NCI", $etudiant[0]); //bindParam lie un paramètre (:nom) à un nom de variable spécifique ($nom)
            $datN = new DateTime($etudiant[3]);
            $dateNow = $datN->format('Y-m-d');
            $requete->bindParam(":Date_emargement", $dateNow);
            $requete->bindParam(":Arrivee", $etudiant[4]);
            $requete->bindParam(":Depart", $etudiant[5]);
            $requete->execute(); //excecute la requete qui a été preparé
        }
        fclose($monfichier);

    } 
    catch (PDOException $e) {
        echo "ECHEC : " . $e->getMessage(); //en cas d erreur lors de la connexion à la base de données mysql
        exit(); //arreter le code
    }

?>