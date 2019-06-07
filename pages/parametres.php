<?php
session_start();
if (!isset($_SESSION["nom"])) {
    echo '<h1>Connectez-vous</h1>';
    header('Location: ../index.php');
    exit();
}
$_SESSION["actif"] = "parametres";
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
        .active>.nav-link{
            background-color: #d0c9d675;
            border-bottom: 4px solid #ce2e7469;
        }
        .navbar-expand-lg{
            padding:0px 16px 0px 16px;
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
                    ############################--Debut contenu table--############################
                    ///////////-----recuperation des données des etudiants----///////////
                    $codemysql = "SELECT * FROM agents"; //le code mysql
                    $agents=recuperation($connexion,$codemysql);
                    ///////////-----Fin recuperation des données des etudiants----///////
                    if(isset($agents[0][1])){
                        $tableVide=false;
                    }
                    ############################--Fin contenu table--##############################


                ///////////////////////////////----Validation des élements avant ajout definitif------/////////////////
                if (isset($_POST["AjouterFin"]) || isset($_POST["valider"])) {
                    if (!empty($_POST["nom"]) && !empty($_POST["nom"]) && !empty($_POST["login"]) && !empty($_POST["mdp"]) && !empty($_POST["confMdp"])) {
                        $valAjout = true;
                    }
                }
                ////////////////////////////----Fin de la validation des élements avant ajout definitif------///////

                if (isset($_POST["premierValidation"])) {
                    ////////////----même nom----//////////////////
                   for($i=0;$i<count($agents);$i++) {
                        if ($tableVide==false && strtolower($agents[$i]["Nom"]) == strtolower($_POST["nom"])) {
                            $nombre++;
                            $existeDeja = true;
                        }
                    }
                    ////////////----Fin même nom----//////////////

                    ////////////----Recupération anciennes données---//////////////
                    for($i=0;$i<count($agents);$i++) {
                        if ($tableVide==false && strtolower($agents[$i]["Nom"]) == strtolower($_POST["nom"]) && $nombre == 1 || isset($_POST["ancienCode"]) && strtolower($agents[$i]["Nom"]) == strtolower($_POST["nom"]) && $nombre > 1 && $_POST["ancienCode"] == $agents[$i]["id_visiteurs"]) {
                            //soit on cherche avec le nom si il y a une seule personne qui porte ce nom soit avec le nom et le code si plusieurs personnes ont ce nom
                            $_POST["nom"] = $visiteurs[$i]["Nom"]; //pouvoir utiliser le bon nom
                            $date_deVisite =$visiteurs[$i]["Date"];
                            $anclogin = $visiteurs[$i]["loginephone"];
                            $ancmdp = $visiteurs[$i]["mdp"];
                            $confirmer = true;
                        }
                    }
                    ////////////----Fin Recupération anciennes données---//////////////
                }
                //////////////////////////-------Code----------------------//////////////////////
                if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre>1 || isset($_POST["valider"]) && $valAjout == false) {
                    echo '<div class="row">
                        <div class="col-md-2"></div>
                        <select class="form-control col-md-8 espace" name="ancienCode" >';

                   for($i=0;$i<count($visiteurs);$i++) {
                        if ($visiteurs[$i]["Nom"] == $_POST["nom"] && !isset($_POST["ancienCode"])) {
                            echo '<option value="' . $visiteurs[$i]["id_visiteurs"]. '" selected>' . $visiteurs[$i]["id_visiteurs"] . '</option>';
                        } 
                        elseif (isset($_POST["ancienCode"]) && $visiteurs[$i]["id_visiteurs"] == $_POST["ancienCode"]) { //apres validation du code le selectionné
                            echo '<option value="' . $_POST["ancienCode"] . '" selected>' . $_POST["ancienCode"] . '</option>';
                        }
                    }
                    echo '</select>
                    </div>';
                }
                elseif (isset($_POST["Ajouter"]) || isset($_POST["AjouterFin"]) && $valAjout == false) {
                        echo '<div class="row">
                        <div class="col-md-2"></div>';
                    if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"])) {
                        echo '<input class="form-control col-md-8 espace" name="code" placeholder="Numéro carte d\'identité" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["code"]) || isset($_POST["valider"]) && empty($_POST["code"])) { //si le téléphone vide lors de l'ajout
                        echo '<input class="form-control col-md-8 espace rougMoins" type="text" name="code" placeholder="Remplir le numéro de le carte d\'identité" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && $valAjout == false && $ne_pas_ajouter_code_existe==false|| isset($_POST["valider"]) && $valAjout == false) { //si il manque des informations avant l'ajout remettre le téléphone
                        echo '<input class="form-control col-md-8 espace" type="text"  name="code" placeholder="Numéro carte d\'identité" value ="' . $_POST["code"] . '" ';
                    }
                    elseif (isset($_POST["AjouterFin"]) && $valAjout == false && $ne_pas_ajouter_code_existe==true) { //si il manque des informations avant l'ajout remettre le téléphone
                        echo '<input class="form-control col-md-8 espace rougMoins" type="text"  name="code" placeholder ="' . $_POST["code"] . ' existe déja !" ';
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
                    
                    //////////////////////////-------Login----------------------//////////////////////
                    echo '<div class="row">
                        <div class="col-md-2"></div>';
                    if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"])) {
                        echo '<input class="form-control col-md-8 espace" type="text" id="login" name="login" placeholder="Login" ';
                        if ($existeDeja == true) {
                            echo 'value="' . $anclogin . '" ';
                        }
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["login"]) || isset($_POST["valider"]) && empty($_POST["login"])) { //si le Login vide lors de l'ajout
                        echo '<input class="form-control col-md-8 espace rougMoins" type="text" id="login" name="login" placeholder="Remplir login" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && $valAjout == false || isset($_POST["valider"]) && $valAjout == false) { //si il manque des informations avant l'ajout remettre le Login
                        echo '<input class="form-control col-md-8 espace" type="text" id="login" name="login" placeholder="Login" value ="' . $_POST["login"] . '" ';
                    }

                    echo '">
                    </div>';
                    //////////////////////////-------Fin Login----------------------//////////////////////


                    //////////////////////////-------mdp---------------------//////////////////////
                    echo '<div class="row">
                        <div class="col-md-2"></div>';
                    if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"])) {
                        echo '<input class="form-control col-md-8 espace" type="mdp" id="mdp" name="mdp" placeholder="Mot de passe" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["mdp"]) || isset($_POST["valider"]) && empty($_POST["mdp"])) { //si mdp vide lors de l'ajout
                        echo '<input class="form-control col-md-8 espace rougMoins" type="mdp" id="mdp" name="mdp" placeholder="Remplir le mot de passe" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && $valAjout == false || isset($_POST["valider"]) && $valAjout == false) { //si il manque des informations avant l'ajout remettre l'mdp
                        echo '<input class="form-control col-md-8 espace" type="mdp" id="mdp" name="mdp" placeholder="Mot de passe" value ="' . $_POST["mdp"] . '" ';
                    }

                    echo '>
                    </div>';
                    //////////////////////////-------Fin mdp---------------------//////////////////////

                    //////////////////////////-------confMdp---------------------//////////////////////
                    echo '<div class="row">
                        <div class="col-md-2"></div>';
                    if (isset($_POST["premierValidation"]) && $existeDeja == true && $nombre == 1 || isset($_POST["premierValidation"]) && $existeDeja == true && $nombre > 1 && $confirmer == true || isset($_POST["Ajouter"])) {
                        echo '<input class="form-control col-md-8 espace" type="confMdp" id="confMdp" name="confMdp" placeholder="Confirmez le mot de passe" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && empty($_POST["confMdp"]) || isset($_POST["valider"]) && empty($_POST["confMdp"])) { //si confMdp vide lors de l'ajout
                        echo '<input class="form-control col-md-8 espace rougMoins" type="confMdp" id="confMdp" name="confMdp" placeholder="Confirmez le mot de passe" ';
                    } 
                    elseif (isset($_POST["AjouterFin"]) && $valAjout == false || isset($_POST["valider"]) && $valAjout == false) { //si il manque des informations avant l'ajout remettre l'mdp
                        echo '<input class="form-control col-md-8 espace" type="confMdp" id="confMdp" name="confMdp" placeholder="Confirmez le mot de passe" value ="' . $_POST["confMdp"] . '" ';
                    }

                    echo '>
                    </div>';
                    //////////////////////////-------Fin confMdp---------------------//////////////////////
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
                ///////////-----recuperation des données des etudiants----///////////
                $codemysql = "SELECT id_visiteurs FROM visiteurs"; //le code mysql
                $id_des_visiteurs=recuperation($connexion,$codemysql);
                ///////////-----Fin recuperation des données des etudiants----///////
                if(isset($id_des_visiteurs[0]["id_visiteurs"])){
                    $id_visiteurs=$id_des_visiteurs[count($id_des_visiteurs)-1]["id_visiteurs"];//l'id du dernier visiteur
                    $id_visiteurs=str_replace("SA-V-","",$id_visiteurs);
                    $id_visiteurs=$id_visiteurs+1;
                    $id_visiteurs="SA-V-".$id_visiteurs;
                }
                else{
                    $id_visiteurs="SA-V-1";
                }
                $nom = securisation($_POST["nom"]);
                $datVis = securisation($_POST["datevisite"]);

                $login = securisation($_POST["login"]);
                $mdp = securisation($_POST["mdp"]);
                $agent=$_SESSION["NCI_agents"];
                $codemysql = "INSERT INTO `visiteurs` (id_visiteurs,Nom,Date,loginephone,mdp,NCI_agents)
                            VALUES(:id_visiteurs,:Nom,:Date,:loginephone,:mdp,:NCI_agents)"; //le code mysql
                $requete = $connexion->prepare($codemysql);
                $requete->bindParam(":id_visiteurs", $id_visiteurs);
                $requete->bindParam(":Nom", $nom);
                $requete->bindParam(":Date", $datVis);
                $requete->bindParam(":loginephone", $login);
                $requete->bindParam(":mdp", $mdp);
                $requete->bindParam(":NCI_agents", $agent);
                $requete->execute(); //excecute la requete qui a été preparé
            }
            ####################################------Fin Ajouter-----#################################

            ///////////////////////////////////------Debut Modification-----///////////////////////////
            if (isset($_POST["valider"])  && $valAjout == true) {
                $nom = securisation($_POST["nom"]);
                $datVis = securisation($_POST["datevisite"]);
                $login = securisation($_POST["login"]);
                $mdp = securisation($_POST["mdp"]);
                if ( isset($_POST["ancienCode"])) {//ils sont plusieurs à avoir ca nom
                    $sonId=securisation($_POST["ancienCode"]);
                    $codemysql = "UPDATE `visiteurs` SET Nom='$nom',Date='$datVis',loginephone='$login',mdp='$mdp' WHERE id_visiteurs='$sonId' ";
                    $requete = $connexion->prepare($codemysql);
                    $requete->execute();                   
                }
                elseif(!isset($_POST["ancienCode"])){//le nom est unique
                    $sonNom=securisation($_POST["nom"]);
                    $codemysql = "UPDATE `visiteurs` SET Nom='$nom',Date='$datVis',loginephone='$login',mdp='$mdp' WHERE Nom='$sonNom' ";
                    $requete = $connexion->prepare($codemysql);
                    $requete->execute();
                }
            }
            ####################################------Fin Modification----#############################S
            
            echo'</div>
        </form>';
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