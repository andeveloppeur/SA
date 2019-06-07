<?php
session_start();
if (isset($_POST["submit"])) {
    $reussi = false;
    $bloquer = 0;
    $login = "";
    $mDp = "";

    $login = $_POST["login"]; //recuperation du login 
    $mDp = $_POST["MDP"]; //recuperation du MDP
    try {
            include("connexionBDD.php");
             ///////////-----recuperation login et mdp----///////////
            $codemysql = "SELECT * FROM agents"; //le code mysql
            $agents=recuperation($connexion,$codemysql);
            ///////////-----recuperation login et mdp-----///////////
    }
    catch (PDOException $e) {
        echo "ECHEC : " . $e->getMessage(); //en cas d erreur lors de la connexion à la base de données mysql
        exit();
    }


    if ($login != "" && $mDp != "") {
        for($i=0;$i<count($agents);$i++){
            if ($login == $agents[$i]["Login"] && $mDp==$agents[$i]["MDP"] ) {
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
?>