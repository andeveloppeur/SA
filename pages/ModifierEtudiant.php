<?php
session_start();
if (!isset($_SESSION["nom"])) {
    echo '<h1>Connectez-vous</h1>';
    header('Location: ../index.php');
    exit();
}
$_SESSION["actif"] = "ModifierEtudiant";
?>
<!DOCTYPE html>
<html lang="FR-fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/MonStyle.css">
    <title>Gestion des étudiants</title>
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
    <section class="container-fluid cAuth">
        <form method="POST" action="" class="MonForm row insc">
            <div class="col-md-3"></div>
            <div class="col-md-6 bor">
                <?php
                $tableVide=true;
                $existeDeja = false;
                $confirmer = false;
                $nombre = 0;
                $valAjout = false;
                try {
                    include("connexionBDD.php");
                ############################--Debut contenu fichier--############################
                ///////////-----recuperation des données des etudiants----///////////
                $codemysql = "SELECT * FROM etudiants"; //le code mysql
                $etudiants=recuperation($connexion,$codemysql);
                ///////////-----Fin recuperation des données des etudiants----///////
                if(isset($etudiants[0][1])){
                    $tableVide=false;
                }
                ############################--Fin contenu fichier--##############################


                ///////////////////////////////----Validation des élements avant ajout definitif------/////////////////
                if (isset($_POST["AjouterFin"]) || isset($_POST["valider"]) && isset($_POST["promo"])) {
                    if (!empty($_POST["code"]) && !empty($_POST["nom"]) && !empty($_POST["dateNaiss"]) && !empty($_POST["tel"]) && !empty($_POST["email"]) && !empty($_POST["promo"])||!empty($_POST["ancienCode"]) && !empty($_POST["nom"]) && !empty($_POST["dateNaiss"]) && !empty($_POST["tel"]) && !empty($_POST["email"]) && !empty($_POST["promo"])) {
                        $valAjout = true;
                    }
                }
                ////////////////////////////----Fin de la validation des élements avant ajout definitif------///////

                if (isset($_POST["premierValidation"])) {
                    ////////////----même nom----//////////////////
                    for($i=0;$i<count($etudiants);$i++) {
                        if ($tableVide==false && strtolower($etudiants[$i]["Nom"]) == strtolower($_POST["nom"])) {
                            $nombre++;
                            $existeDeja = true;
                        }
                    }

                    ////////////----Fin même nom----//////////////

                    ////////////----Recupération anciennes données---//////////////
                    
                    for($i=0;$i<count($etudiants);$i++)  {

                        if ($tableVide==false && strtolower($etudiants[$i]["Nom"]) == strtolower($_POST["nom"]) && $nombre == 1 || isset($_POST["ancienCode"]) && strtolower($etudiants[$i]["Nom"]) == strtolower($_POST["nom"]) && $nombre > 1 && $_POST["ancienCode"] == $etudiants[$i]["NCI"]) {
                            //soit on cherche avec le nom si il y a une seule personne qui porte ce nom soit avec le nom et le code si plusieurs personnes ont ce nom
                            
                            ///////////-----recuperation des referentiels des personnes qui ont emargés----///////////
                            $NCI_etudiant=$etudiants[$i]["NCI"];
                            $codemysql = "SELECT referentiels.Nom FROM referentiels INNER JOIN etudiants ON referentiels.id_referentiels=etudiants.id_referentiels WHERE etudiants.NCI='$NCI_etudiant'"; //le code mysql
                            $le_ref_etudiant=recuperation($connexion,$codemysql);
                            ///////////-----Fin recuperation des referentiels des personnes qui ont emargés----////////

                            $_POST["nom"] = $etudiants[$i]["Nom"]; //pouvoir utiliser le bon nom
                            $ancDNaiss = $etudiants[$i]["Naissance"];
                            $ancTel = $$etudiants[$i]["Te"];
                            $ancEmail = $etudiants[$i]["NCI"];
                            $anciePromo = $le_ref_etudiant[0]["Nom"];
                            $confirmer = true;
                        }
                    }
                
                    ////////////----Fin Recupération anciennes données---//////////////
                }

                //////////////////////////-------Code----------------------//////////////////////
                if (isset($_POST["premierValidation"]) && $existeDeja == true || isset($_POST["valider"]) && $valAjout == false) {
                    echo '<div class="row">
                        <div class="col-md-2"></div>
                        <select class="form-control col-md-8 espace" name="ancienCode" >';
                    for($i=0;$i<count($etudiants);$i++)  {
                        $ligne = fgets($monfichier);
                        if ($etudiants[$i]["Nom"]== $_POST["nom"] && !isset($_POST["ancienCode"])) {
                            echo '<option value="' . $etudiants[$i]["NCI"] . '" selected>' . $etudiants[$i]["NCI"] . '</option>';
                        } 
                        elseif (isset($_POST["ancienCode"]) && $etudiants[$i]["NCI"] == $_POST["ancienCode"]) { //apres validation du code le selectionné
                            echo '<option value="' . $_POST["ancienCode"] . '" selected>' . $_POST["ancienCode"] . '</option>';
                        }
                    }
                    echo '</select>
                    </div>';
                }
                if (isset($_POST["Ajouter"])) {
                        echo '<div class="row">
                        <div class="col-md-2"></div>';
                    if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"])) {
                        echo '<input class="form-control col-md-8 espace" name="code" placeholder="Numéro carte d\'identité" ';
                        // if ($existeDeja == true) {
                        //     echo 'value="' . $ancTel . '" ';
                        // }
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["code"]) || isset($_POST["valider"]) && empty($_POST["code"])) { //si le téléphone vide lors de l'ajout
                        echo '<input class="form-control col-md-8 espace rougMoins" type="text" name="code" placeholder="Remplir le numéro de le carte d\'identité" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && $valAjout == false || isset($_POST["valider"]) && $valAjout == false) { //si il manque des informations avant l'ajout remettre le téléphone
                        echo '<input class="form-control col-md-8 espace" type="text"  name="code" placeholder="Numéro carte d\'identité" value ="' . $_POST["code"] . '" ';
                    }

                    echo '">
                    </div>';
                }
                //////////////////////////-------Fin Code----------------------//////////////////////

                //////////////////////////-------Nom----------------------/////////////////////////
                echo '<div class="row">
                    <div class="col-md-2"></div>
                    <input  type="text" id="nom" name="nom" ';
                if (isset($_POST["premierValidation"]) || isset($_POST["Ajouter"])) {
                    if (empty($_POST["nom"]) && !isset($_POST["Ajouter"])) {
                        echo ' class="form-control col-md-8 espace rougMoins" placeholder= "Entrez le nom de l\'apprenant à modifier !"';
                    } 
                    else { //si on ajoute ou on modifie
                        if ($existeDeja == false && !isset($_POST["Ajouter"])) { //si on essaie de modifier une personne qui n'existe pas
                            echo ' class="form-control col-md-8 espace rougMoins" placeholder= "' . $_POST["nom"] . ' ne fait pas partie des apprenants"';
                        } 
                        elseif ($existeDeja == true || isset($_POST["Ajouter"]) && !empty($_POST["nom"])) { //soit on veut modifier une personne qui existe soit on veut ajouter une personne dont on a écrit le nom
                            echo ' class="form-control col-md-8 espace" placeholder= "Nom et prénom" value="' . $_POST["nom"] . '"';
                        } 
                        else {
                            echo ' placeholder="Nom et prénom" class="form-control col-md-8 espace" ';
                        }
                    }
                } 
                elseif (isset($_POST["AjouterFin"]) && empty($_POST["nom"]) || isset($_POST["valider"]) && empty($_POST["nom"])) { //si on enregistre alors que le nom est vide
                    echo ' class="form-control col-md-8 espace rougMoins" placeholder= "Entrez le nom de l\'apprenant à ajouter !"';
                } 
                elseif (isset($_POST["AjouterFin"]) && $valAjout == false  || isset($_POST["valider"]) && $valAjout == false) { //si on enregistre alors que le nom n'etait pas vide on y remet sa valeur
                    echo ' class="form-control col-md-8 espace " placeholder= "Nom et prénom" value="' . $_POST["nom"] . '" ';
                } 
                else { //chargement de la page 
                    echo ' placeholder="Nom et prénom" class="form-control col-md-8 espace" ';
                }
                echo '>
                </div>';
                //////////////////////////-------Fin Nom----------------------/////////////////////////

                if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"]) || isset($_POST["AjouterFin"]) && $valAjout == false || isset($_POST["valider"]) && $valAjout == false) {
                    //////////////////////////-------Date de naissance----------------------//////////////////////
                    echo '<div class="row">
                        <div class="col-md-2"></div>';
                    if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"])) {
                        echo '<input class="form-control col-md-8 espace" type="date" id="dateNaiss" name="dateNaiss" ';
                        if ($existeDeja == true) {
                            echo 'value="' . $ancDNaiss . '" ';
                        }
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["dateNaiss"]) || isset($_POST["valider"]) && empty($_POST["dateNaiss"])) { //si la date de naissance vide lors de l'ajout
                        echo '<input class="form-control col-md-8 espace rougMoins" type="date" id="dateNaiss" name="dateNaiss" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && $valAjout == false || isset($_POST["valider"]) && $valAjout == false) { //si il manque des informations avant l'ajout remettre la date de naissance
                        echo '<input class="form-control col-md-8 espace " type="date" id="dateNaiss" name="dateNaiss" value ="' . $_POST["dateNaiss"] . '" ';
                    }
                    echo '>
                    </div>';
                    //////////////////////////-------Fin Date de naissance----------------------//////////////////////

                    //////////////////////////-------Telephone----------------------//////////////////////
                    echo '<div class="row">
                        <div class="col-md-2"></div>';
                    if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"])) {
                        echo '<input class="form-control col-md-8 espace" type="number" id="tel" name="tel" placeholder="Téléphone" ';
                        if ($existeDeja == true) {
                            echo 'value="' . $ancTel . '" ';
                        }
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["tel"]) || isset($_POST["valider"]) && empty($_POST["tel"])) { //si le téléphone vide lors de l'ajout
                        echo '<input class="form-control col-md-8 espace rougMoins" type="number" id="tel" name="tel" placeholder="Remplir le numéro de téléphone" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && $valAjout == false || isset($_POST["valider"]) && $valAjout == false) { //si il manque des informations avant l'ajout remettre le téléphone
                        echo '<input class="form-control col-md-8 espace" type="number" id="tel" name="tel" placeholder="Téléphone" value ="' . $_POST["tel"] . '" ';
                    }

                    echo '">
                    </div>';
                    //////////////////////////-------Fin Telephone----------------------//////////////////////


                    //////////////////////////-------Email---------------------//////////////////////
                    echo '<div class="row">
                        <div class="col-md-2"></div>';
                    if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"])) {
                        echo '<input class="form-control col-md-8 espace" type="email" id="email" name="email" placeholder="Email" ';
                        if ($existeDeja == true) {
                            echo 'value="' . $ancEmail . '" ';
                        }
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["email"]) || isset($_POST["valider"]) && empty($_POST["email"])) { //si email vide lors de l'ajout
                        echo '<input class="form-control col-md-8 espace rougMoins" type="email" id="email" name="email" placeholder="Remplir l\'email" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && $valAjout == false || isset($_POST["valider"]) && $valAjout == false) { //si il manque des informations avant l'ajout remettre l'email
                        echo '<input class="form-control col-md-8 espace" type="email" id="email" name="email" placeholder="Email" value ="' . $_POST["email"] . '" ';
                    }

                    echo '>
                    </div>';
                    //////////////////////////-------Fin Email---------------------//////////////////////

                    //////////////////////////-------Promo---------------------//////////////////////
                    echo '<div class="row">
                        <div class="col-md-2"></div>
                        <select class="form-control col-md-8 espace" name="promo" >';
                    ///////////-----recuperation des referentiels des personnes qui ont emargés----///////////
                    $codemysql = "SELECT Nom FROM referentiels";
                    $lesReferentiel=recuperation($connexion,$codemysql);
                    ///////////-----Fin recuperation des referentiels des personnes qui ont emargés----///////////

                    for($i=0;$i<count($lesReferentiel);$i++){
                        if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"])) {
                            if (isset($_POST["premierValidation"]) && $anciePromo == $lesReferentiel[$i]["Nom"]) {
                                echo '<option value="' . $lesReferentiel[$i]["Nom"] . '" selected>' . $lesReferentiel[$i]["Nom"]. '</option>';
                            } 
                            else {
                                echo '<option value="' . $lesReferentiel[$i]["Nom"] . '">' . $lesReferentiel[$i]["Nom"] . '</option>';
                            }
                        } 
                        elseif (isset($_POST["AjouterFin"]) && $valAjout == false || isset($_POST["valider"]) && $valAjout == false) { //si il manque des informations avant l'ajout remettre la promo
                            if ($_POST["promo"] == $lesReferentiel[$i]["Nom"]) { //selectionner la bonne promo
                                echo '<option value="' . $lesReferentiel[$i]["Nom"] . '" selected>' . $lesReferentiel[$i]["Nom"] . '</option>';
                            } 
                            else {
                                echo '<option value="' . $lesReferentiel[$i]["Nom"] . '">' . $lesReferentiel[$i]["Nom"] . '</option>';
                            }
                        }
                    }
                    echo '</select>
                    </div>';
                    ///////////////////////////-------Fin Promo---------------------//////////////////////
                }
                ?>
                <div class="row">
                    <?php
                    ////////////////////////////////////////------Gestion des submit-------///////////////////////
                    if (isset($_POST["Ajouter"]) || isset($_POST["AjouterFin"]) && $valAjout == false) {
                        echo '<div class="col-md-2"></div>
                        <input type="submit" class="form-control col-md-4 espace" value="Annuller" name="Annuller">
                        <input type="submit" class="form-control col-md-4 espace" value="Enregister" name="AjouterFin">';
                    } 
                    elseif (isset($_POST["premierValidation"]) &&  $existeDeja == true && $nombre == 1 || isset($_POST["ancienCode"]) && isset($_POST["premierValidation"]) || isset($_POST["valider"]) && $valAjout == false) {
                        echo '<div class="col-md-2"></div>
                        <input type="submit" class="form-control col-md-4 espace" value="Annuller" name="Annuller">
                        <input type="submit" class="form-control col-md-4 espace" value="Enregister" name="valider">';
                    } 
                    elseif (isset($_POST["premierValidation"]) &&  $existeDeja == true && $nombre > 1) {
                        echo '<div class="col-md-2"></div>
                        <input type="submit" class="form-control col-md-4 espace" value="Annuller" name="Annuller">
                        <input type="submit" class="form-control col-md-4 espace" value="Confirmer" name="premierValidation">';
                    } 
                    else {
                        echo '<div class="col-md-2"></div>
                        <input type="submit" class="form-control col-md-4 espace" value="Ajouter" name="Ajouter">
                        <input type="submit" class="form-control col-md-4 espace" value="Modifier" name="premierValidation">';
                    }
                    ////////////////////////////////////////------Fin Gestion des submit-------///////////////////////
                    ?>
                </div>
            </div>
            <?php
            if (isset($_POST["Annuler"])) {
                //header("location: ModifierEtudiant.php");
            }
            $existeDeja = 0;
            $nouv = "";


            ///////////////////////////////////------Debut Ajouter-----////////////////////////////////
            if (isset($_POST["AjouterFin"]) && $valAjout == true) {
                $monfichier = fopen('etudiants.txt', 'r');
                while (!feof($monfichier)) {
                    $ligne = fgets($monfichier);
                    $tab = explode("|", $ligne);
                }
                fclose($monfichier);
                $code = $_POST["code"];

                $promo = $_POST["promo"];
                $nom = $_POST["nom"];
                $datN = new DateTime($_POST["dateNaiss"]);
                $dateNaiss = $datN->format('d-m-Y');

                $tel = $_POST["tel"];
                $email = $_POST["email"];
                $statut = "Accepter";

                $monfichier = fopen('etudiants.txt', 'a+');
                if( $tableVide==false){
                    $nouvU = "\n" . $code . "|" . $promo . "|" . $nom . "|" . $dateNaiss . "|" . $tel . "|" . $email . "|" . $statut . "|"; //ajout d un nouvel utilisateur
                }
                else{
                    $nouvU = $code . "|" . $promo . "|" . $nom . "|" . $dateNaiss . "|" . $tel . "|" . $email . "|" . $statut . "|"; //ajout d un nouvel utilisateur
                }
                fwrite($monfichier, $nouvU); //ajout 
                fclose($monfichier);
            }
            ####################################------Fin Ajouter-----#################################

            ///////////////////////////////////------Debut Modification-----///////////////////////////
            if (isset($_POST["valider"])  && $valAjout == true) {

                $reecrire = "";
                $monfichier = fopen('etudiants.txt', 'r');
                while (!feof($monfichier)) {

                    $ligne = fgets($monfichier);
                    $tab = explode("|", $ligne);
                    if ( $tab[0] == $_POST["ancienCode"] ) {
                        //modifier si le code correspond                             
                        $datN = new DateTime($_POST["dateNaiss"]);
                        $NouvdateNaiss = $datN->format('d-m-Y');
                        $modif = $tab[0] . "|" . $_POST["promo"] . "|" . $_POST["nom"] . "|" . $NouvdateNaiss . "|" . $_POST["tel"] . "|" . $_POST["email"] . "|\n";
                    } 
                    else {
                        $modif = $ligne;
                    }
                    $reecrire = $reecrire . $modif;
                }
                fclose($monfichier);
                $monfichier = fopen('etudiants.txt', 'w+');
                //$reecrire="";
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
            echo'<table class="col-12 table tabliste table-hover">
                <thead class="">
                    <tr class="row">
                        <td class="col-md-2 text-center gras">N° CI</td>
                        <td class="col-md-2 text-center gras">Référentiel</td>
                        <td class="col-md-2 text-center gras">Nom</td>
                        <td class="col-md-2 text-center gras">Date de naissance</td>
                        <td class="col-md-1 text-center gras">Téléphone</td>
                        <td class="col-md-3 text-center gras">Email</td>
                    </tr>
                </thead>
                <tbody id="developers">';
            }   
            $nbr=0;
            ///////////-----recuperation des données des etudiants----///////////
            $codemysql = "SELECT * FROM etudiants"; //le code mysql
            $etudiants=recuperation($connexion,$codemysql);
            ///////////-----Fin recuperation des données des etudiants----//////

            for($i=0;$i<count($etudiants);$i++){
                ///////////-----recuperation des referentiels des personnes qui ont emargés----///////////
                $NCI_etudiant=$etudiants[$i]["NCI"];
                $codemysql = "SELECT referentiels.Nom FROM referentiels INNER JOIN etudiants ON referentiels.id_referentiels=etudiants.id_referentiels WHERE etudiants.NCI='$NCI_etudiant'"; //le code mysql
                $le_ref_etudiant=recuperation($connexion,$codemysql);
                ///////////-----Fin recuperation des referentiels des personnes qui ont emargés----////////
                $ligne = $NCI_etudiant." ".$le_ref_etudiant[0]["Nom"]." ".$etudiants[$i]["Nom"]." ".$etudiants[$i]["Naissance"]." ".$etudiants[$i]["Telephone"]." ".$etudiants[$i]["Email"];
                if ($tableVide==false && !isset($_POST["recherche"]) || isset($_POST["recherche"]) && !empty($_POST["aRechercher"]) && strstr(strtolower($ligne), strtolower($_POST["aRechercher"])) || $tableVide==false && isset($_POST["recherche"]) && empty($_POST["aRechercher"])) {
                //si le code n'est pas vide et que on ne recherche rien                          //si on recherche une chose non vide et que cela face partie de la ligne                                 //si on appuis sur le bouton rechercher alors qu'on n'a rien ecrit afficher tous les éléments                                      
                    echo
                        '<tr class="row">
                            <td class="col-md-2 text-center">' . $NCI_etudiant . '</td>
                            <td class="col-md-2 text-center">' . $le_ref_etudiant[0]["Nom"] . '</td>
                            <td class="col-md-2 text-center">' . $etudiants[$i]["Nom"] . '</td>
                            <td class="col-md-2 text-center">' . $etudiants[$i]["Naissance"] . '</td>
                            <td class="col-md-1 text-center">' . $etudiants[$i]["Telephone"] . '</td>
                            <td class="col-md-3 text-center">' . $etudiants[$i]["Email"] . '</td>
                            
                        </tr>';
                        $nbr++;
                }
            }
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