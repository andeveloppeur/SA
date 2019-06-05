<?php
session_start();
if (!isset($_SESSION["nom"])) {
    echo '<h1>Connectez-vous</h1>';
    header('Location: ../index.php');
    exit();
}
$_SESSION["actif"] = "presence";
$Ref="";
if (isset($_GET["Ref"])) {
    $Ref = $_GET["Ref"];
} 
elseif (isset($_POST["Ref"])) {
    $Ref = $_POST["Ref"];
}
?>
<!DOCTYPE html>
<html lang="FR-fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/MonStyle.css">
    <title>Emargement</title>
    <style>
        .nonSoulign {
            text-decoration: none !important;
        }
        .page_link,.prev_link,.next_link{
            border:1px solid #007bffb9;
            border-radius: 50px;
            font-size:30px;
            background-color: #d0c9d6;
            padding:2px 10px 3px 10px;
            text-decoration: none ;
            color: #212529;
        }
        .pager{
            justify-content: center;
        }
        .page_link:hover,.prev_link:hover,.next_link:hover{
            text-decoration: none;
            color: #212529;
        }
        .pager>.active>a{
            border-radius: 50px;
            background-color: #007bffb9;
        }
        .table {
            margin-bottom: 2em;
        }
    </style>
</head>

<body>
    <?php
    include("nav.php");
    ?>
    <header></header>
    <section class="container-fluid  pageLister">
        <?php
        try {
            include("connexionBDD.php");
             ///////////-----recuperation des données de la table emargement----///////////
            $codemysql = "SELECT * FROM emargement"; //le code mysql
            $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
            $requete->execute();
            $emargement=$requete->fetchAll();
            ///////////-----recuperation des données de la table emargement-----///////////
            $tableVide=true;
            $sortie=false;
            $heureDepart="";
        
            if(isset($emargement[0][1])){
                $tableVide=false;
            }
            ///////////////////////////-------rechercher par jour---------------------//////////////////////
                
            echo'<form method="POST" action="presence.php" class="MonForm row insc">
                    <div class="col-md-3"></div>
                    <div class="col-md-6 bor">';
                    echo '<div class="row">
                        <div class="col-md-2"></div>
                        <input type="date" class="form-control col-md-8 espace" name="jourRech" '; 
                        if(!isset($_POST["jourRech"]) && !isset($_GET["laDate"])){
                            echo' value="'.date('Y-m-d').'" ';
                        }
                        elseif(isset($_POST["jourRech"]) && !isset($_GET["laDate"])){
                            echo' value="'.$_POST["jourRech"].'" ';
                        }
                        elseif(isset($_GET["laDate"])){
                            $datN = new DateTime($_GET["laDate"]);
                            $ladate = $datN->format('Y-m-d');
                            echo' value="'.$ladate.'" ';
                        } 
                            echo'>
                    </div>';
                    ///////////////////////////-------Ref---------------------//////////////////////
                    echo '<div class="row">
                            <div class="col-md-2"></div>
                            <select class="form-control col-md-8 espace" name="Ref" >';
                    ///////////-----recuperation des données referentiels----///////////
                    $codemysql = "SELECT id_referentiels,Nom FROM referentiels"; //le code mysql
                    $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
                    $requete->execute();
                    $lesReferentiel=$requete->fetchAll();
                    ///////////-----recuperation des données referentiels----///////////
                    
                    for($i=0;$i<count($lesReferentiel);$i++) {
                        if ($Ref == $lesReferentiel[$i]["Nom"]) {
                            echo '<option value="' . $lesReferentiel[$i]["Nom"] . '" selected>' . $lesReferentiel[$i]["Nom"] . '</option>';
                        } 
                        else {
                            echo '<option value="' . $lesReferentiel[$i]["Nom"] . '">' . $lesReferentiel[$i]["Nom"] . '</option>';
                        }
                    }
                    
                    echo '</select>
                        </div>';
                    ///////////////////////////-------Fin Ref---------------------//////////////////////

                    /////////////////////////////-------Present/absent---------------------//////////////////////      
                    echo '<div class="row">
                        <div class="col-md-2"></div>
                        <select class="form-control col-md-8 espace" name="presence" >
                            <option value="present" ';if(isset($_POST["presence"]) && $_POST["presence"]=="present" && !isset($_GET["statut"])|| isset($_GET["statut"]) && $_GET["statut"]=="present"){echo' selected';}echo'>Présents</option>
                            <option value="absents" ';if(isset($_POST["presence"]) && $_POST["presence"]=="absents" && isset($_GET["statut"]) || isset($_GET["statut"]) && $_GET["statut"]=="absents"){echo' selected';}echo'>Absents</option>
                        </select>                   
                    </div>';
                    ///////////////////////////-------Fin Present/absent---------------------//////////////////////

                    echo '<div class="row">
                        <div class="col-md-3"></div>
                        <input type="submit" class="form-control col-md-6 espace" value="Lister" name="validerRechJour">
                    </div>
                    </div>
                </form>';
            
            ///////////////////////////-------rechercher par jour---------------------//////////////////////
            //<table  id="dtBasicExample"  class="col-12 table table-hover tabliste table"   >
                if($tableVide==false || isset($_POST["validerRechJour"])){   //donc la table n'est pas vide ou qu'on appuis sur le submit
                echo '
                    <table class="col-12 table tabliste table-hover">
               
                <thead class="">
                    <tr class="row">
                        <th class="col-md-2 text-center gras ">N° CI</th>
                        <th class="col-md-2 text-center gras ">Référentiel</th>
                        <th class="col-md-2 text-center gras ">Nom</th>
                        <th class="col-md-2 text-center gras ">Date</th>
                        <th class="col-md-2 text-center gras ">Arrivée</th>
                        <th class="col-md-1 text-center gras ">Sortie</th>
                        <th class="col-md-1 text-center gras ">Stats</th>
                    </tr>
                </thead>
                <tbody id="developers">';
                }
                /////////////////////////////////////////------Debut Affichage-----///////////////////////// 
                if(isset($_POST["jourRech"])){
                    $datN = new DateTime($_POST["jourRech"]);
                    $date = $datN->format('d-m-Y');
                }
                ///////////////////////////////////////////----Present----//////////////////////////////////////////////
                $nbr=0;
                if(!isset($_POST["validerRechJour"])|| isset($_POST["validerRechJour"]) && $_POST["presence"]=="present"){           
                    for($i=0;$i<count($emargement);$i++){
                        $date_emargement = $emargement[$i]["Date_emargement"];
                        $NCI_emarger = $emargement[$i]["NCI"];
                        ///////////-----recuperation des referentiels des personnes qui ont emargés----///////////
                        $codemysql = "SELECT referentiels.Nom FROM referentiels INNER JOIN etudiants ON referentiels.id_referentiels=etudiants.id_referentiels WHERE etudiants.NCI='$NCI_emarger'"; //le code mysql
                        $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
                        $requete->execute();
                        $le_ref_emargement=$requete->fetchAll();
                        ///////////-----Fin recuperation des referentiels des personnes qui ont emargés----///////////
                        if (!isset($_POST["validerRechJour"]) && isset($date_emargement) && $date_emargement==date('Y-m-d') && !isset($_GET["Ref"])||
                        isset($_POST["validerRechJour"]) && isset($date_emargement) && $date_emargement==$_POST["jourRech"] && $le_ref_emargement[0]["Nom"]==$_POST["Ref"] && $_POST["presence"]=="present" && !isset($_GET["Ref"]) ||
                        isset($_GET["Ref"])&& !isset($_POST["validerRechJour"]) && isset($date_emargement) && $date_emargement==date('Y-m-d') && $le_ref_emargement[0]["Nom"]==$_GET["Ref"] && !isset($_GET["laDate"]) ||
                        isset($_GET["Ref"])&& !isset($_POST["validerRechJour"]) && isset($date_emargement)  && $le_ref_emargement[0]["Nom"]==$_GET["Ref"] && isset($_GET["laDate"]) && isset($_GET["statut"]) && $_GET["laDate"]==$date_emargement && $_GET["statut"]=="present") {
                             ///////////-----recuperation des données des etudiants----///////////
                            $codemysql = "SELECT NCI,Nom FROM etudiants WHERE NCI='$NCI_emarger'"; //le code mysql
                            $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
                            $requete->execute();
                            $etudiants=$requete->fetchAll();
                            ///////////-----Fin recuperation des données des etudiants----///////////
                            $datN = new DateTime($emargement[$i]["Date_emargement"] );
                            $date_emargement = $datN->format('d-m-Y');
                            echo
                            '<tr class="row">
                                <td class="col-md-2 text-center">' . $etudiants[0]["NCI"] . '</td>
                                <td class="col-md-2 text-center">' . $le_ref_emargement[0]["Nom"] . '</td>
                                <td class="col-md-2 text-center">' . $etudiants[0]["Nom"] . '</td>
                                <td class="col-md-2 text-center">' . $date_emargement . '</td>
                                <td class="col-md-2 text-center">' . $emargement[$i]["Arrivee"] . '</td>
                                <td class="col-md-1 text-center">' . $emargement[$i]["Depart"] . '</td>
                                <td class="col-md-1 text-center"><a class="nonSoulign" href="stat.php?code=' . $etudiants[0]["NCI"]  . '" ><button class="form-control" >Stat</button></a></td>
                            </tr>';
                            $nbr++;
                        }
                    }
                }
                ///////////////////////////////////////////----Fin Present----//////////////////////////////////////////////
                            
                ///////////////////////////////////////////----Absents----//////////////////////////////////////////////
                if(isset($_POST["validerRechJour"]) && $_POST["presence"]=="absents" || !isset($_POST["validerRechJour"]) && isset($_GET["statut"]) && $_GET["statut"]=="absents"){
                    ///////////-----recuperation des données des etudiants----///////////
                    $codemysql = "SELECT NCI,Nom FROM etudiants"; //le code mysql
                    $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
                    $requete->execute();
                    $etudiants=$requete->fetchAll();
                    ///////////-----Fin recuperation des données des etudiants----///////////           
                    for($i=0;$i<count($etudiants);$i++) {
                       
                        $absent=true;
                                   
                        for($j=0;$j<count($emargement);$j++)  {
                           
                            if(!isset($_GET["laDate"])){
                                $date = $_POST["jourRech"];
                            }
                            else{
                                $date = $_GET["laDate"];
                            }
                            
                            if($etudiants[$i]["NCI"]==$emargement[$j]["NCI"] && $emargement[$j]["Date_emargement"]==$date){
                                $absent=false;
                            }
                        }
                        if($absent==true){
                        ///////////-----recuperation des referentiels des personnes qui ont emargés----///////////
                        $NCI_etudiant=$etudiants[$i]["NCI"];
                        $codemysql = "SELECT referentiels.Nom FROM referentiels INNER JOIN etudiants ON referentiels.id_referentiels=etudiants.id_referentiels WHERE etudiants.NCI='$NCI_etudiant'"; //le code mysql
                        $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
                        $requete->execute();
                        $le_ref_etudiant=$requete->fetchAll();
                        ///////////-----Fin recuperation des referentiels des personnes qui ont emargés----///////////
                        }
                    
                        if($absent==true && isset($_POST["Ref"]) && $_POST["Ref"]==$le_ref_etudiant[0]["Nom"] || $absent==true && isset($_GET["Ref"]) && $_GET["Ref"]==$le_ref_etudiant[0]["Nom"]){
                            echo
                            '<tr class="row">
                                <td class="col-md-2 text-center">' . $etudiants[$i]["NCI"]. '</td>
                                <td class="col-md-2 text-center">' . $le_ref_etudiant[0]["Nom"] . '</td>
                                <td class="col-md-2 text-center">' . $etudiants[$i]["Nom"] . '</td>
                                <td class="col-md-2 text-center">' . $date . '</td>
                                <td class="col-md-2 text-center">--:--</td>
                                <td class="col-md-1 text-center">--:--</td>
                                <td class="col-md-1 text-center"><a class="nonSoulign" href="stat.php?code=' . $etudiants[$i]["NCI"]. '" ><button class="form-control" >Stat</button></a></td>
                            </tr>';
                            $nbr++;
                        }
                    }
                }
                ///////////////////////////////////////////----Fin Absents----//////////////////////////////////////////////

                ####################################------Fin Affichage-----#################################
                echo'</tbody>
                </table>';
                if($nbr>8){
                    echo'<div class="col-md-12 text-center">
                        <ul class="pagination pagination-sm pager" id="developer_page"></ul>
                    </div>';
                }
                echo'<div class="bas"></div>';
            }
            catch (PDOException $e) {
                echo "ECHEC : " . $e->getMessage(); //en cas d erreur lors de la connexion à la base de données mysql
            }
        ?>
    </section>
    <?php
    include("piedDePage.php");
    ?>
    <script src="../js/jq.js"></script>
    <script src="../js/bootstrap-table-pagination.js"></script>
    <script src="../js/monjs.js"></script>


</body>

</html>