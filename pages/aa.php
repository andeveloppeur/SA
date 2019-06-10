<?php
    try {
        include("connexionBDD.php");
        ///////////-----recuperation des données des etudiants----///////////
        $codemysql = "SELECT NCI FROM etudiants"; //le code mysql
        $les_nci=recuperation($connexion,$codemysql);
        ///////////-----Fin recuperation des données des etudiants-----///////////
        for($jr=12;$jr<=16;$jr++){
            for($i=0;$i<count($les_nci);$i++){          
                $code= $les_nci[$i]["NCI"];
                $date_emar = "2019-06-".$jr;
                $mn=rand(0, 59);
                $h=rand(6, 8);

                if($mn<10) $hArriv = "0".$h.":0".$mn;
                else $hArriv = "0".$h.":".$mn;

                $mn=rand(0, 59);
                $h=rand(16, 21);

                if($mn<10) $hDepart = $h.":0".$mn;
                else $hDepart = $h.":".$mn;

                $agent="1 AS";
                $codemysql = "INSERT INTO `emargement` (NCI,Date_emargement,Arrivee,Depart,Code_agents_arrivee,Code_agents_depart)
                    VALUES(:NCI,:Date_emargement,:Arrivee,:Depart,:Code_agents_arrivee,:Code_agents_depart)"; //le code mysql
                $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
                $requete->bindParam(":NCI", $code);
                $requete->bindParam(":Date_emargement", $date_emar);
                $requete->bindParam(":Arrivee", $hArriv);
                $requete->bindParam(":Depart", $hDepart);
                $requete->bindParam(":Code_agents_arrivee", $agent);
                $requete->bindParam(":Code_agents_depart", $agent);
                $requete->execute(); //excecute la requete qui a été preparé
            }
        }
    }
    catch (PDOException $e) {
        echo "ECHEC : " . $e->getMessage(); //en cas d erreur lors de la connexion à la base de données mysql
    }
?>