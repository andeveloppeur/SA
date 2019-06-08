<?php
session_start();
try {
    include("connexionBDD.php");
    //////////////////////-------index----////////////////
    if (isset($_POST["submit"])) {
        $reussi = false;
        $bloquer = 0;
        $login = "";
        $mDp = "";

        $login = $_POST["login"]; //recuperation du login 
        $mDp = $_POST["MDP"]; //recuperation du MDP

        ///////////-----recuperation login et mdp----///////////
        $codemysql = "SELECT * FROM agents"; //le code mysql
        $agents=recuperation($connexion,$codemysql);
        ///////////-----recuperation login et mdp-----///////////
        

        if ($login != "" && $mDp != "") {
            for($i=0;$i<count($agents);$i++){
                if ($login == $agents[$i]["Login"] && md5($mDp)==$agents[$i]["MDP"] && $agents[$i]["statut"]=="Actif") {
                        header('Location: accueil.php');
                        $_SESSION["nom"] = $agents[$i]["Nom"];
                        $_SESSION["Code_agents"] = $agents[$i]["Code_agents"];
                        $reussi = true;
                        break;
                }
            }
        }
        if ($reussi == false) { //verification du login et du MDP
            $_SESSION["reussi"]=false;
            header('Location: ../index.php');
            $_SESSION["ancLogin"]=$login;
            $_SESSION["ancMDP"]=$mDp;   
        }
    }
    //////////////////////-------Fin index----////////////////

    //////////////////////-------Bloquer debloquer agent----////////////////
    if(isset($_GET["code_agents"])){
        $sonId=$_GET["code_agents"];
        $tatut="";
        if($_GET["statut"]=="Actif" && $sonId!=$_SESSION["Code_agents"]){
            $tatut="Bloquer";
        }
        if($_GET["statut"]=="Bloquer"){
            $tatut="Actif";
        }
        
        $codemysql = "UPDATE `agents` SET statut='$tatut' WHERE Code_agents='$sonId' ";
        $requete = $connexion->prepare($codemysql);
        $requete->execute();
        header('Location: parametres.php');
    }
    //////////////////////-------Fin Bloquer debloquer agent----////////////////

    //////////////////////-------Supprimer agent----////////////////
    if(isset($_GET["code_agents_a_supp"]) && $_GET["code_agents_a_supp"]!=$_SESSION["Code_agents"]){
        $sonId=$_GET["code_agents_a_supp"];
        $codemysql = "DELETE FROM `agents` WHERE Code_agents='$sonId' ";
        $requete = $connexion->prepare($codemysql);
        $requete->execute();
        header('Location: parametres.php');
    }
    if(isset($_GET["code_agents_a_supp"]) && $_GET["code_agents_a_supp"]==$_SESSION["Code_agents"]){
         header('Location: parametres.php');
    }
    //////////////////////-------Fin Supprimer agent----////////////////

    //////////////////////-------Supprimer visiteur----////////////////
    if(isset($_GET["code_visiteur_a_supp"])){
        $sonId=$_GET["code_visiteur_a_supp"];
        $codemysql = "DELETE FROM `visiteurs` WHERE id_visiteurs='$sonId' ";
        $requete = $connexion->prepare($codemysql);
        $requete->execute();
        header('Location: visiteur.php');
    }
    //////////////////////-------Fin Supprimer visiteur----////////////////
}
catch (PDOException $e) {
    echo "ECHEC : " . $e->getMessage(); //en cas d erreur lors de la connexion à la base de données mysql
}
    

?>