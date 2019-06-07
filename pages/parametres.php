<?php
session_start();
if (!isset($_SESSION["nom"])) {
    echo '<h1>Connectez-vous</h1>';
    header('Location: ../index.php');
    exit();
}
$_SESSION["actif"] = "parametres";
$admin=false;
if($_SESSION["Code_agents"]=="AS-1"){
    $admin=true;
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
        <?php
            $tableVide=true;
            $existeDeja = false;
            $login_existe=false;
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

                ///////////////////////----Verification du login----///////////////////////////
                if(isset($_POST["valider"])){
                    for($i=0;$i<count($agents);$i++){
                        if($agents[$i]["Login"]==$_POST["login"]){
                            $login_existe=true;
                        }
                    }
                }
                ############################--Fin contenu table--##############################

        ?>
        <?php  if($admin==true) {?>
        <form method="POST" action="" class="MonForm row insc">
            <div class="col-md-3"></div>
            <div class="col-md-6 bor">
                <div class="row">
                    <div class="col-md-2"></div>
                    <input  type="text" id="nom_ag" name="nom" class="form-control col-md-8 espace" placeholder= "Nom de l'agent" <?php if($login_existe==true){echo ' value="'.$_POST["nom"].'"';} ?>>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <input  type="text" id="tel_ag" name="tel" class="form-control col-md-8 espace" placeholder= "Téléphone" <?php if($login_existe==true){echo ' value="'.$_POST["tel"].'"';} ?>>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <?php if($login_existe==false){ ?>
                    <input  type="text" id="login_ag" name="login" class="form-control col-md-8 espace" placeholder= "Login" >
                    <?php } 
                    else { 
                    echo'<input  type="text" id="login_ag" name="login" class="form-control col-md-8 espace rougMoins" placeholder= "Le login '.$_POST["login"].' existe déja">';
                    }?>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <input  type="password" id="mdp_ag" name="mdp" class="form-control col-md-8 espace" placeholder= "Mot de passe" <?php if($login_existe==true){echo ' value="'.$_POST["mdp"].'"';} ?>>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <input  type="password" id="confMdp_ag" name="confMdp" class="form-control col-md-8 espace" placeholder= "Confirmez le mot de passe" <?php if($login_existe==true){echo ' value="'.$_POST["confMdp"].'"';} ?>>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                        <input type="submit" class="form-control col-md-4 espace" value="Annuller" name="Annuller">
                        <input type="submit" id="valider_ajout_ag" class="form-control col-md-4 espace" value="Enregister" name="valider">
                    </div>
                </div>
            </div>
        </form>
        <?php } ?>
            <?php

            ///////////////////////////////////------Debut Ajouter-----////////////////////////////////
            if (isset($_POST["valider"]) && $login_existe == false) {
                ///////////-----recuperation des données des agents----///////////
                $codemysql = "SELECT Code_agents FROM agents"; //le code mysql
                $code_des_agents=recuperation($connexion,$codemysql);
                ///////////-----Fin recuperation des données des agents----///////
                if(isset($code_des_agents[0]["Code_agents"])){
                    $Code_agents=$code_des_agents[count($code_des_agents)-1]["Code_agents"];//l'id du dernier visiteur
                    $Code_agents=str_replace("AS-","",$Code_agents);
                    $Code_agents=$Code_agents+1;
                    $Code_agents="AS-".$Code_agents;
                }
                else{
                    $Code_agents="AS-1";
                }
                $nom = securisation($_POST["nom"]);
                $tel = securisation($_POST["tel"]);
                $login = securisation($_POST["login"]);
                $mdp = md5(securisation($_POST["mdp"]));//chiffrer en md5
                $statut="Actif";
                $codemysql = "INSERT INTO `agents` (Code_agents,Nom,Telephone,Login,MDP,statut)
                            VALUES(:Code_agents,:Nom,:Telephone,:Login,:MDP,:statut)"; //le code mysql
                $requete = $connexion->prepare($codemysql);
                $requete->bindParam(":Code_agents", $Code_agents);
                $requete->bindParam(":Nom", $nom);
                $requete->bindParam(":Telephone", $tel);
                $requete->bindParam(":Login", $login);
                $requete->bindParam(":MDP", $mdp);
                $requete->bindParam(":statut", $statut);
                $requete->execute(); //excecute la requete qui a été preparé
            }
            ####################################------Fin Ajouter-----#################################

            ///////////////////////////////////------Debut Modification-----///////////////////////////
            // if (isset($_POST["valider"])  && $valAjout == true) {
            //     $nom = securisation($_POST["nom"]);
            //     $datVis = securisation($_POST["datevisite"]);
            //     $login = securisation($_POST["login"]);
            //     $mdp = securisation($_POST["mdp"]);
            //     if ( isset($_POST["ancienCode"])) {//ils sont plusieurs à avoir ca nom
            //         $sonId=securisation($_POST["ancienCode"]);
            //         $codemysql = "UPDATE `visiteurs` SET Nom='$nom',Date='$datVis',loginephone='$login',mdp='$mdp' WHERE Code_agents='$sonId' ";
            //         $requete = $connexion->prepare($codemysql);
            //         $requete->execute();                   
            //     }
            //     elseif(!isset($_POST["ancienCode"])){//le nom est unique
            //         $sonNom=securisation($_POST["nom"]);
            //         $codemysql = "UPDATE `visiteurs` SET Nom='$nom',Date='$datVis',loginephone='$login',mdp='$mdp' WHERE Nom='$sonNom' ";
            //         $requete = $connexion->prepare($codemysql);
            //         $requete->execute();
            //     }
            // }
            ####################################------Fin Modification----#############################
            if($admin==true) {
                ///////////////////////////////////------Debut Affichage-----////////////////////////
                $nbr=0;
                if($tableVide==false || isset($_POST["valider"]) && $login_existe == false){
                    echo'<table class="col-12 table tabliste table-hover">
                    <thead class="">
                        <tr class="row">
                            <td class="col-md-1 text-center gras"></td>
                            <td class="col-md-2 text-center gras">Code</td>
                            <td class="col-md-2 text-center gras">Nom</td>
                            <td class="col-md-2 text-center gras">Téléphone</td>
                            <td class="col-md-2 text-center gras">Login</td>
                            <td class="col-md-2 text-center gras">Statut</td>
                            <td class="col-md-1 text-center gras"></td>
                        </tr>
                    </thead>
                    <tbody id="developers">';
                }    
                if(isset($_POST["valider"]) && $login_existe == false){
                    
                    echo
                        '<tr class="row">
                            <td class="col-md-1 text-center"></td>
                            <td class="col-md-2 text-center">' . $leCode. '</td>
                            <td class="col-md-2 text-center">' . $_POST["nom"] . '</td>
                            <td class="col-md-2 text-center">' . $_POST["tel"]. '</td>
                            <td class="col-md-2 text-center">' . $_POST["login"] . '</td>
                            <td class="col-md-2 text-center">' . $tatut . '</td>
                            <td class="col-md-1 text-center"></td>
                            
                        </tr>';
                }
                else{
                    ///////////-----recuperation des données des etudiants----///////////
                    $codemysql = "SELECT * FROM visiteurs"; //le code mysql
                    $visiteurs=recuperation($connexion,$codemysql);
                    ///////////-----Fin recuperation des données des etudiants----///////
                    for($i=0;$i<count($visiteurs);$i++) {
                        $ligne = $visiteurs[$i]["id_visiteurs"]." ".$visiteurs[$i]["Nom"]." ".$visiteurs[$i]["Date"]." ".$visiteurs[$i]["Telephone"]." ".$visiteurs[$i]["Email"];
                        if ($tableVide==false && !isset($_POST["recherche"]) || isset($_POST["recherche"]) && !empty($_POST["aRechercher"]) && strstr(strtolower($ligne), strtolower($_POST["aRechercher"])) || $tableVide==false && isset($_POST["recherche"]) && empty($_POST["aRechercher"])) {
                        //si la table n'est pas vide et que on ne recherche rien                          //si on recherche une chose non vide et que cela face partie de la ligne                                 //si on appuis sur le bouton rechercher alors qu'on n'a rien ecrit afficher tous les éléments                                      
                            $datN = new DateTime($visiteurs[$i]["Date"]);
                            $datev = $datN->format('d-m-Y');
                            echo
                                '<tr class="row">
                                    <td class="col-md-1 text-center"></td>
                                    <td class="col-md-2 text-center">' . $visiteurs[$i]["id_visiteurs"] . '</td>
                                    <td class="col-md-2 text-center">' . $visiteurs[$i]["Nom"]. '</td>
                                    <td class="col-md-2 text-center">' . $datev. '</td>
                                    <td class="col-md-2 text-center">' . $visiteurs[$i]["Telephone"] . '</td>
                                    <td class="col-md-2 text-center">' . $visiteurs[$i]["Email"] . '</td>
                                    <td class="col-md-1 text-center"></td>                            
                                </tr>';
                                $nbr++;
                        }
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