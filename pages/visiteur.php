<?php
session_start();
if (!isset($_SESSION["nom"])) {
    echo '<h1>Connectez-vous</h1>';
    header('Location: ../index.php');
    exit();
}
$_SESSION["actif"] = "visiteur";
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
                $FichierVide=true;
                $existeDeja = false;
                $confirmer = false;
                $nombre = 0;
                $valAjout = false;
                /////////////////--Debut contenu fichier--//////////
                $monfichier = fopen('visiteur.txt', 'r');
                while (!feof($monfichier)) {
                    $ligne = fgets($monfichier);
                    $tab = explode("|", $ligne);
                    if(isset($tab[1])){
                        $FichierVide=false;
                    }
                }
                fclose($monfichier);
                /////////////////--Fin contenu fichier--//////////


                ///////////////////////////////----Validation des élements avant ajout definitif------/////////////////
                if (isset($_POST["AjouterFin"]) || isset($_POST["valider"])) {
                    if (!empty($_POST["nom"]) && !empty($_POST["dateNaiss"]) && !empty($_POST["tel"]) && !empty($_POST["email"])) {
                        $valAjout = true;
                    }
                }
                ////////////////////////////----Fin de la validation des élements avant ajout definitif------///////

                if (isset($_POST["premierValidation"])) {
                    ////////////----même nom----//////////////////
                    $monfichier = fopen('visiteur.txt', 'r');
                    while (!feof($monfichier)) {
                        $ligne = fgets($monfichier);
                        $tab = explode("|", $ligne);
                        if ($FichierVide==false && strtolower($tab[1]) == strtolower($_POST["nom"])) {
                            $nombre++;
                            $existeDeja = true;
                        }
                    }
                    fclose($monfichier);

                    ////////////----Fin même nom----//////////////

                    ////////////----Recupération anciennes données---//////////////
                    $monfichier = fopen('visiteur.txt', 'r');
                    while (!feof($monfichier)) {
                        $ligne = fgets($monfichier);
                        $tab = explode("|", $ligne);
                        if ($FichierVide==false && strtolower($tab[1]) == strtolower($_POST["nom"]) && $nombre == 1 || isset($_POST["ancienCode"]) && strtolower($tab[1]) == strtolower($_POST["nom"]) && $nombre > 1 && $_POST["ancienCode"] == $tab[0]) {
                            //soit on cherche avec le nom si il y a une seule personne qui porte ce nom soit avec le nom et le code si plusieurs personnes ont ce nom
                            $_POST["nom"] = $tab[1]; //pouvoir utiliser le bon nom
                            $ancDNaiss = $tab[2];
                            $ancTel = $tab[3];
                            $ancEmail = $tab[4];
                            $datN = new DateTime($ancDNaiss);
                            $ancDNaiss = $datN->format('Y-m-d');
                            $confirmer = true;
                        }
                    }
                    fclose($monfichier);
                    ////////////----Fin Recupération anciennes données---//////////////
                }
                //////////////////////////-------Code----------------------//////////////////////
                if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre>1 || isset($_POST["valider"]) && $valAjout == false) {
                    echo '<div class="row">
                        <div class="col-md-2"></div>
                        <select class="form-control col-md-8 espace" name="ancienCode" >';
                        
                    $monfichier = fopen("visiteur.txt", "r");
                    while (!feof($monfichier)) {
                        $ligne = fgets($monfichier);
                        $visiteurs = explode("|", $ligne);
                        if ($visiteurs[1] == $_POST["nom"] && !isset($_POST["ancienCode"])) {
                            echo '<option value="' . $visiteurs[0] . '" selected>' . $visiteurs[0] . '</option>';
                        } 
                        elseif (isset($_POST["ancienCode"]) && $visiteurs[0] == $_POST["ancienCode"]) { //apres validation du code le selectionné
                            echo '<option value="' . $_POST["ancienCode"] . '" selected>' . $_POST["ancienCode"] . '</option>';
                        }
                    }
                    fclose($monfichier);
                    echo '</select>
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
            $existeDeja = 0;
            $nouv = "";


            ///////////////////////////////////------Debut Ajouter-----////////////////////////////////
            if (isset($_POST["AjouterFin"]) && $valAjout == true) {
                $monfichier = fopen('visiteur.txt', 'r');
                while (!feof($monfichier)) {
                    $ligne = fgets($monfichier);
                    $tab = explode("|", $ligne);
                }
                fclose($monfichier);
                if(isset($tab[0])){
                    $code = $tab[0]+1;
                }
                else{
                    $code = 1;//premier ajout
                }
                

                $nom = $_POST["nom"];
                $datN = new DateTime($_POST["dateNaiss"]);
                $dateNaiss = $datN->format('d-m-Y');

                $tel = $_POST["tel"];
                $email = $_POST["email"];
                $statut = "Accepter";

                $monfichier = fopen('visiteur.txt', 'a+');
                if( $FichierVide==false){
                    $nouvU = "\n" .  $code. "|". $nom . "|" . $dateNaiss . "|" . $tel . "|" . $email . "|" ; //ajout d un nouvel utilisateur
                }
                else{
                    $nouvU = $code. "|". $nom . "|" . $dateNaiss . "|" . $tel . "|" . $email . "|" ; //ajout d un nouvel utilisateur
                }
                fwrite($monfichier, $nouvU); //ajout 
                fclose($monfichier);
            }
            ####################################------Fin Ajouter-----#################################

            ///////////////////////////////////------Debut Modification-----///////////////////////////
            if (isset($_POST["valider"])  && $valAjout == true) {

                $reecrire = "";
                $monfichier = fopen('visiteur.txt', 'r');
                while (!feof($monfichier)) {

                    $ligne = fgets($monfichier);
                    $tab = explode("|", $ligne);
                    if ( isset($_POST["ancienCode"]) && $tab[0] == $_POST["ancienCode"] || !isset($_POST["ancienCode"]) && $tab[1]==$_POST["nom"]) {
                        //modifier si le code correspond                             
                        $datN = new DateTime($_POST["dateNaiss"]);
                        $NouvdateNaiss = $datN->format('d-m-Y');
                        $modif = $tab[0] . "|"  . $_POST["nom"] . "|" . $NouvdateNaiss . "|" . $_POST["tel"] . "|" . $_POST["email"] . "|"  . "\n";
                    } 
                    else {
                        $modif = $ligne;
                    }
                    $reecrire = $reecrire . $modif;
                }
                fclose($monfichier);
                $monfichier = fopen('visiteur.txt', 'w+');
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
        $FichierVide=true;
        $monfichier = fopen('visiteur.txt', 'r');
            while (!feof($monfichier)) {
                $ligne = fgets($monfichier);
                $visiteur = explode('|', $ligne);
                if(isset($visiteur[1])){
                    $FichierVide=false;
                }
            }
        fclose($monfichier);
        if($FichierVide==false){
        echo'<table class="col-12 table tabliste table-hover">
            <thead class="">
                <tr class="row">
                    <td class="col-md-1 text-center gras"></td>
                    <td class="col-md-2 text-center gras">N°</td>
                    <td class="col-md-2 text-center gras">Nom</td>
                    <td class="col-md-2 text-center gras">Date</td>
                    <td class="col-md-2 text-center gras">Téléphone</td>
                    <td class="col-md-2 text-center gras">Email</td>
                    <td class="col-md-1 text-center gras"></td>
                </tr>
            </thead>
            <tbody id="developers">';
        }    
            $nbr=0;
            $monfichier = fopen('visiteur.txt', 'r');
            while (!feof($monfichier)) {
                $ligne = fgets($monfichier);
                $visiteur = explode('|', $ligne);
                if ($visiteur[0] != "" && !isset($_POST["recherche"]) || isset($_POST["recherche"]) && !empty($_POST["aRechercher"]) && strstr(strtolower($ligne), strtolower($_POST["aRechercher"])) || $visiteur[0] != "" && isset($_POST["recherche"]) && empty($_POST["aRechercher"])) {
                //si le code n'est pas vide et que on ne recherche rien                          //si on recherche une chose non vide et que cela face partie de la ligne                                 //si on appuis sur le bouton rechercher alors qu'on n'a rien ecrit afficher tous les éléments                                      
                    echo
                        '<tr class="row">
                            <td class="col-md-1 text-center"></td>
                            <td class="col-md-2 text-center">' . $visiteur[0] . '</td>
                            <td class="col-md-2 text-center">' . $visiteur[1] . '</td>
                            <td class="col-md-2 text-center">' . $visiteur[2] . '</td>
                            <td class="col-md-2 text-center">' . $visiteur[3] . '</td>
                            <td class="col-md-2 text-center">' . $visiteur[4] . '</td>
                            <td class="col-md-1 text-center"></td>                            
                        </tr>';
                        $nbr++;
                }
            }
            fclose($monfichier);
            ####################################------Fin Affichage-----#################################
            echo'</tbody>
                </table>';
                if($nbr>8){
                    echo'<div class="col-md-12 text-center">
                        <ul class="pagination pagination-sm pager" id="developer_page"></ul>
                    </div>';
                }
                echo'<div class="bas"></div>';
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