<?php
session_start();
if (!isset($_SESSION["nom"])) {
    echo '<h1>Connectez-vous</h1>';
    header('Location: ../index.php');
    exit();
}
$_SESSION["actif"] = "accueil";
?>
<!DOCTYPE html>
<html lang="FR-fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/MonStyle.css">
    <title>Accueil</title>
    <style>
    g text tspan {
        font-weight: bolder;
    }
    .nonSoulign {
        text-decoration: none !important;
    }
    .margBot{
        margin-bottom:15%;
    }
    </style>
</head>

<body>
    <?php
    include("nav.php");
    ?>
    <header></header>
    <section class="container-fluid">
        <a href="presence.php" class="nonSoulign"><h1 class="textAccueil">Pourcentage d'étudiants présents/absents</h1></a>
        
        <?php
        ///////////////////////////-------rechercher par jour---------------------//////////////////////
        echo'<form method="POST" action="" class="monformAcc row insc">
                <div class="col-md-3"></div>
                <div class="col-md-6 bor">';
                echo '<div class="row">
                    <div class="col-md-2"></div>
                    <input type="date" class="form-control col-md-8 espace" name="jourRech" value="';if(!isset($_POST["jourRech"])){echo date('Y-m-d');}else{echo $_POST["jourRech"];}echo'">
                </div>';
                echo '<div class="row">
                    <div class="col-md-3"></div>
                    <input type="submit" class="form-control col-md-6 espace" value="Lister" name="valider">
                </div>
                </div>
            </form>';
        ///////////////////////////-------rechercher par jour---------------------//////////////////////
        ?>
        <div id="chartdiv"></div>
        <?php
        try {
            $serveur = "localhost";
            $Monlogin = "root";
            $Monpass = "101419";
            $connexion = new PDO("mysql:host=$serveur;dbname=SA;charset=utf8", $Monlogin, $Monpass); //se connecte au serveur mysquel
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //setAttribute — Configure l'attribut PDO $connexion
            
            

            ///////////-----recuperation des données referentiels----///////////
            $codemysql = "SELECT id_referentiels,Nom FROM referentiels"; //le code mysql
            $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
            $requete->execute();
            $lesReferentiel=$requete->fetchAll();
            ///////////-----recuperation des données referentiels----///////////

            ///////////-----recuperation des données des etudiants----///////////
            $codemysql = "SELECT Nom,id_referentiels,NCI FROM etudiants"; //le code mysql
            $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
            $requete->execute();
            $etudiants=$requete->fetchAll();
            ///////////-----recuperation des données des etudiants----///////////

            ///////////-----recuperation des données de la table emargement----///////////
            $codemysql = "SELECT NCI,Date_emargement FROM emargement"; //le code mysql
            $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
            $requete->execute();
            $emargement=$requete->fetchAll();
            ///////////-----recuperation des données de la table emargement-----///////////
            $i=0;
            for($a=0;$a<count($lesReferentiel);$a++){
                $referentiel = $lesReferentiel[$a]["Nom"];
                $id_ref=$lesReferentiel[$a]["id_referentiels"];
                //////------compter effectif---//////
                $effectif = 0;
                for($b=0;$b<count($etudiants);$b++) {
                    $etudiant = $etudiants[$i]["Nom"];
                    $id_ref_etudiant=$etudiants[$b]["id_referentiels"];//la clé de son referentiel
                    if (isset($etudiant) && isset($referentiel) && $id_ref == $id_ref_etudiant) {
                        $effectif++;
                    }
                }
                //////------Fin compter effectif---//////

                ////////------compter emarger---///////
                $emarger=0;
                for($c=0;$c<count($emargement);$c++) {
                    $NCI_emarger = $emargement[$c]["NCI"];
                    $date_emargement = $emargement[$c]["Date_emargement"];
                    ///////////-----recuperation des referentiels des personnes qui ont emargés----///////////
                    $codemysql = "SELECT referentiels.Nom FROM referentiels INNER JOIN etudiants ON referentiels.id_referentiels=etudiants.id_referentiels WHERE etudiants.NCI='$NCI_emarger'"; //le code mysql
                    $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
                    $requete->execute();
                    $le_ref_emargement=$requete->fetchAll();
                    ///////////-----recuperation des referentiels des personnes qui ont emargés----///////////
                    $ref_emargement=$le_ref_emargement[0]["Nom"];
                    if (isset($ref_emargement) && isset($referentiel) && $referentiel == $ref_emargement && $date_emargement==date('Y-m-d') && !isset($_POST["valider"]) || isset($_POST["valider"]) && isset($ref_emargement) && isset($referentiel) && $referentiel == $ref_emargement && $date_emargement==$_POST["jourRech"]) {
                        $emarger++;
                    }
                }
                //////------Fin compter emarger---//////
                $absent=$effectif-$emarger;
                $i++;
                echo'<div id="present'.$i.'" class="'.$emarger.'"></div>
                    <div id="absent'.$i.'" class="'.$absent.'"></div>';
            }
            
            if(!isset($_POST["valider"])){
                echo'<div id="jourR" class="'.date('Y-m-d').'"></div>';
            }
            else{
                echo'<div id="jourR" class="'.$_POST["jourRech"].'"></div>';
            }
        } 
        catch (PDOException $e) {
            echo "ECHEC : " . $e->getMessage(); //en cas d erreur lors de la connexion à la base de données mysql
        }

        echo "<h2 class='margBot'></h2>
        </section>
            <footer class='piedPageaccueil'>
                <p class='cpr'>Copyright 2019 Sonatel Academy</p>
            </footer>";
        ?>
    <script src="../js/core.js"></script>
    <script src="../js/charts.js"></script>
    <script src="../js/animated.js"></script>
    <script src="../js/index.js"></script>
    <script src="../js/jq.js"></script>
    <script src="../js/monjs.js"></script>
</body>

</html>