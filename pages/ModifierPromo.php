<?php
session_start();
if (!isset($_SESSION["nom"])) {
    echo '<h1>Connectez-vous</h1>';
    header('Location: ../index.php');
    exit();
}
$_SESSION["actif"] = "ModifierPromo";
?>
<!DOCTYPE html>
<html lang="FR-fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/MonStyle.css">
    <title>Gestion des promos</title>
</head>

<body>
    <?php
    include("nav.php");
    ?>
    <header></header>
    <section class="container-fluid cAuth">
        <form method="POST" action="" class="MonForm row insc">
            <div class="col-md-3"></div>
            <div class="col-md-6 bor">
                <?php
                $existeDeja = false;
                $promoDejaAjouter = false;
                $nepasModif = false;
                $tableVide=true;
                try {
                    include("connexionBDD.php");
                    ///////////-----recuperation des referentiels----///////////
                    $codemysql = "SELECT * FROM referentiels"; //le code mysql
                    $requete = $connexion->prepare($codemysql); //Prépare la requête $codemysql à l'exécution
                    $requete->execute();
                    $referentiels=$requete->fetchAll();
                    ///////////-----Fin recuperation des referentiels----///////////

                    /////////////////--Debut contenu fichier--//////////
                    if(isset($referentiels[0][1])){
                        $tableVide=false;
                    }
                    /////////////////--Fin contenu fichier--////////////

                    if (isset($_POST["premierValidation"]) || isset($_POST["AjouterFin"]) || isset($_POST["valider"])) {
                        for($i=0;$i<count($referentiels);$i++){

                            if ($tableVide==false && strtolower($referentiels[$i]["Nom"]) == strtolower($_POST["nom"])) {
                                $_POST["nom"] = $referentiels[$i]["Nom"]; //pouvoir utiliser le bon nom
                                $ancMois = $referentiels[$i]["Mois"];
                                $ancAnnee = $referentiels[$i]["Annee"];
                                $existeDeja = true;
                            }
                        }
                    }
                    if (isset($_POST["AjouterFin"]) && $existeDeja == true) {
                        $promoDejaAjouter = true;
                    }
                    if (isset($_POST["valider"]) && $existeDeja == false) {
                        $nepasModif = true; //si on veut modifier alors qu'il n existe pas
                    }
                    ///////////////////////////////////////-------Nom------////////////////////
                    echo '<div class="row">
                        <div class="col-md-2"></div>
                        <input  type="text" id="nom" name="nom" ';
                    if (isset($_POST["premierValidation"]) || isset($_POST["Ajouter"]) || $nepasModif == true) {
                        if (empty($_POST["nom"]) && !isset($_POST["Ajouter"]) || $nepasModif == true) {
                            echo ' class="form-control col-md-8 espace rougMoins" placeholder= "Entrez le nom de la promo à modifier !"';
                        } 
                        else {
                            if ($existeDeja == false && !isset($_POST["Ajouter"])) {
                                echo ' class="form-control col-md-8 espace rougMoins" placeholder= "La promo ' . $_POST["nom"] . ' n\'existe pas"';
                            } 
                            elseif ($existeDeja == true || isset($_POST["Ajouter"]) && !empty($_POST["nom"])) {
                                echo ' placeholder="Nom de la promo" class="form-control col-md-8 espace" value="' . $_POST["nom"] . '"';
                            } 
                            else {
                                echo ' placeholder="Nom de la promo" class="form-control col-md-8 espace" ';
                            }
                        }
                    } 
                    elseif ($promoDejaAjouter == true) {
                        echo ' class="form-control col-md-8 espace rougMoins" placeholder= "Cette promo existe déja !"';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["nom"]) || isset($_POST["valider"]) && empty($_POST["nom"])) { //si on enregistre alors que le nom est vide
                        echo ' class="form-control col-md-8 espace rougMoins" placeholder= "Entrez le nom de la promo !"';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["nom"])  || isset($_POST["valider"]) && empty($_POST["nom"])) { //si on enregistre alors que le nom n'etait pas vide on y remet sa valeur
                        echo ' class="form-control col-md-8 espace " placeholder= "Nom de la promo" value="' . $_POST["nom"] . '" ';
                    } 
                    else {
                        echo ' placeholder="Nom de la promo" class="form-control col-md-8 espace" ';
                    }
                    echo '>
                    </div>';
                    ///////////////////////////////////////-------Nom------/////////////////////////


                    if (isset($_POST["premierValidation"]) && $existeDeja == true && !empty($_POST["nom"]) || isset($_POST["Ajouter"]) || isset($_POST["AjouterFin"]) && empty($_POST["nom"]) || $promoDejaAjouter == true || isset($_POST["valider"]) && empty($_POST["nom"])) {
                        ///////////////////////////////////////-------Mois------////////////////////
                        $Tablemois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre");
                        echo '<div class="row">
                            <div class="col-md-2"></div>
                            <select class="form-control col-md-8 espace" name="mois" >';
                        $ann = 2019;
                        for ($i = 0; $i < 12; $i++) {
                            if (!isset($_POST["AjouterFin"]) && !isset($_POST["valider"])) {
                                if ($existeDeja == true && $ancMois == $Tablemois[$i]) {
                                    echo '<option value="' . $Tablemois[$i]  . '" selected >' . $Tablemois[$i]  . '</option>';
                                } 
                                else {
                                    echo '<option value="' . $Tablemois[$i] . '">' . $Tablemois[$i] . '</option>';
                                }
                            } 
                            elseif (isset($_POST["AjouterFin"]) && empty($_POST["nom"]) || $promoDejaAjouter == true || isset($_POST["valider"]) && empty($_POST["nom"])) {
                                if ($_POST["mois"] == $Tablemois[$i]) {
                                    echo '<option value="' . $Tablemois[$i] . '" selected>' . $Tablemois[$i] . '</option>';
                                } 
                                else {
                                    echo '<option value="' . $Tablemois[$i] . '">' . $Tablemois[$i] . '</option>';
                                }
                            }
                        }
                        echo '</select>
                        </div>';

                        ///////////////////////////////////////-------Mois------////////////////////


                        ///////////////////////////////////////-------Année------////////////////////
                        echo '<div class="row">
                            <div class="col-md-2"></div>
                            <select class="form-control col-md-8 espace" name="annee" >';

                        $ann = date('Y');
                        for ($i = $ann; $i <=$ann+5 ; $i++) {
                            if (!isset($_POST["AjouterFin"]) && !isset($_POST["valider"])) {
                                if ($existeDeja == true && $ancAnnee == $i) {
                                    echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                } 
                                else {
                                    echo '<option value="' . $i . '">' . $i . '</option>';
                                }
                            } 
                            elseif (isset($_POST["AjouterFin"]) && empty($_POST["nom"]) || $promoDejaAjouter == true || isset($_POST["valider"]) && empty($_POST["nom"])) {
                                if ($_POST["annee"] == $i) {
                                    echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                } 
                                else {
                                    echo '<option value="' . $i . '">' . $i . '</option>';
                                }
                            }
                        }
                        echo '</select>
                        </div>';
                        ///////////////////////////////////////-------Année------////////////////////

                    }
                    ?>
                    <div class="row">

                        <?php

                        if (isset($_POST["Ajouter"]) || isset($_POST["AjouterFin"]) && empty($_POST["nom"]) || $promoDejaAjouter == true) {
                            echo '<div class="col-md-2"></div>
                            <input type="submit" class="form-control col-md-4 espace" value="Annuller" name="Annuller">
                            <input type="submit" class="form-control col-md-4 espace" value="Enregister" name="AjouterFin">';
                        } 
                        elseif (isset($_POST["premierValidation"]) &&  $existeDeja == true || isset($_POST["valider"]) && empty($_POST["nom"])) {
                            echo '<div class="col-md-2"></div>
                            <input type="submit" class="form-control col-md-4 espace" value="Annuller" name="Annuller">
                            <input type="submit" class="form-control col-md-4 espace" value="Enregister" name="valider">';
                        } 
                        else {
                            echo '<div class="col-md-2"></div>
                            <input type="submit" class="form-control col-md-4 espace" value="Ajouter" name="Ajouter">
                            <input type="submit" class="form-control col-md-4 espace" value="Modifier" name="premierValidation">';
                        }
                        ?>
                    </div>
                </div>
                <?php
                if (isset($_POST["Annuler"])) {
                    //header("location: ModifierPromo.php");
                }
                $existeDeja = 0;
                ///////////////////////////////////------Debut Ajouter-----////////////////////////////////
                if (isset($_POST["AjouterFin"]) && !empty($_POST["nom"]) && $promoDejaAjouter == false) {

                    $monfichier = fopen('promos.txt', 'r');
                    while (!feof($monfichier)) {
                        $ligne = fgets($monfichier);
                        $tab = explode("|", $ligne);
                    }
                    fclose($monfichier);
                    $code = str_replace("SA-", "", $tab[0]);
                    $code = intval($code) + 1;
                    $code = "SA-" . $code;

                    $nom = $_POST["nom"];
                    $mois = $_POST["mois"];
                    $annee = $_POST["annee"];

                    $monfichier = fopen('promos.txt', 'a+');
                    if($tableVide==false){
                        $nouvU = "\n" . $code . "|" .  $nom . "|" . $mois . "|" . $annee . "|"; //ajout d un nouvel utilisateur
                    }
                    else{
                        $nouvU = $code . "|" .  $nom . "|" . $mois . "|" . $annee . "|"; //ajout d un nouvel utilisateur
                    }
                    
                    
                    fwrite($monfichier, $nouvU); //ajout 
                    fclose($monfichier);
                }
                ####################################------Fin Ajouter-----#################################

                ///////////////////////////////////------Debut Modification-----///////////////////////////
                if (isset($_POST["valider"]) && !empty($_POST["nom"])) {
                    $reecrire = "";
                    $monfichier = fopen('promos.txt', 'r');
                    while (!feof($monfichier)) {

                        $ligne = fgets($monfichier);
                        $tab = explode("|", $ligne);
                        if ($tab[1] == $_POST["nom"]) {
                            $modif = $tab[0] . "|" . $_POST["nom"] . "|" . $_POST["mois"] . "|" . $_POST["annee"] . "|"  . "\n";
                        } 
                        else {
                            $modif = $ligne;
                        }
                        $reecrire = $reecrire . $modif;
                    }
                    fclose($monfichier);
                    $monfichier = fopen('promos.txt', 'w+');
                    fwrite($monfichier, trim($reecrire));
                    fclose($monfichier);
                }
                ####################################------Fin Modification----#############################S
                ?>
                </div>
            </form>
            <!-- ///////////////////////////////////------Debut Affichage-----//////////////////////// -->
            <?php
            if($tableVide==false){
            echo'<table class="col-12 tabliste table">
                <thead class="thead-dark">
                    <tr class="row">
                        <td class="col-md-2 text-center gras">Code</td>
                        <td class="col-md-2 text-center gras">Nom</td>
                        <td class="col-md-2 text-center gras">Mois</td>
                        <td class="col-md-2 text-center gras">Année</td>
                        <td class="col-md-2 text-center gras">Effectif</td>
                        <td class="col-md-2 text-center gras">Lister</td>
                    </tr>
                </thead>';
            }

            ///////////-----recuperation des données referentiels----///////////
            $codemysql = "SELECT * FROM referentiels"; //le code mysql
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
                    
                    $ligne=$lesReferentiel[$a]["Nom"]." ".$lesReferentiel[$a]["Mois"]." ".$lesReferentiel[$a]["Annee"];
                    ######-------fin compter effectif####
                    if ( $tableVide==false && !isset($_POST["recherche"]) || isset($_POST["recherche"]) &&  !empty($_POST["aRechercher"]) && strstr(strtolower($ligne), strtolower($_POST["aRechercher"])) || $tableVide==false && isset($_POST["recherche"]) && empty($_POST["aRechercher"])) {
                        echo
                            '<tr class="row">
                                <td class="col-md-2 text-center">' . $lesReferentiel[$a]["id_referentiels"] . '</td>
                                <td class="col-md-2 text-center">' . $lesReferentiel[$a]["Nom"] . '</td>
                                <td class="col-md-2 text-center">' . $lesReferentiel[$a]["Mois"]. '</td>
                                <td class="col-md-2 text-center">' . $lesReferentiel[$a]["Annee"]. '</td>
                                <td class="col-md-2 text-center"> ' . $effectif . '</td>
                                <td class="col-md-2 text-center"><a href="ListerEtudiant.php?promo=' . $lesReferentiel[$a]["Nom"]  . ' "  id="' . $lesReferentiel[$a]["id_referentiels"] . '" ><button class="form-control">Liste</button></a></td>
                            </tr>';
                    }
                }
                ####################################------Fin Affichage-----#################################
            }
            catch (PDOException $e) {
                echo "ECHEC : " . $e->getMessage(); //en cas d erreur lors de la connexion à la base de données mysql
            }
            ?>
        </table>
    </section>
    <?php
    include("piedDePage.php");
    ?>
</body>

</html>