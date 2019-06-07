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
        ?>
        <form method="POST" action="" class="MonForm row insc">
            <div class="col-md-3"></div>
            <div class="col-md-6 bor">
                <div class="row">
                    <div class="col-md-2"></div>
                    <input  type="text" id="nom_ag" name="nom" class="form-control col-md-8 espace" placeholder= "Nom de l'agent">
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <input  type="text" id="tel_ag" name="tel" class="form-control col-md-8 espace" placeholder= "Téléphone">
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <input  type="text" id="login_ag" name="login" class="form-control col-md-8 espace" placeholder= "Login">
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <input  type="password" id="mdp_ag" name="mdp" class="form-control col-md-8 espace" placeholder= "Mot de passe">
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <input  type="password" id="confMdp_ag" name="confMdp" class="form-control col-md-8 espace" placeholder= "Confirmez le mot de passe">
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                        <input type="submit" class="form-control col-md-4 espace" value="Annuller" name="Annuller">
                        <input type="submit" id="valider_ajout_ag" class="form-control col-md-4 espace" value="Enregister" name="valider">
                    </div>
                </div>
            </div>
        </form>
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
                $agent=$_SESSION["Code_agents"];
                $codemysql = "INSERT INTO `visiteurs` (id_visiteurs,Nom,Date,loginephone,mdp,Code_agents)
                            VALUES(:id_visiteurs,:Nom,:Date,:loginephone,:mdp,:Code_agents)"; //le code mysql
                $requete = $connexion->prepare($codemysql);
                $requete->bindParam(":id_visiteurs", $id_visiteurs);
                $requete->bindParam(":Nom", $nom);
                $requete->bindParam(":Date", $datVis);
                $requete->bindParam(":loginephone", $login);
                $requete->bindParam(":mdp", $mdp);
                $requete->bindParam(":Code_agents", $agent);
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
        ';
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