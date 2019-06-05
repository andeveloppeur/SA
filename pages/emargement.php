<?php
session_start();
if (!isset($_SESSION["nom"])) {
    echo '<h1>Connectez-vous</h1>';
    header('Location: ../index.php');
    exit();
}
$_SESSION["actif"] = "emargement";
$Promo="";
if (isset($_GET["promo"])) {
    $Promo = $_GET["promo"];
} 
elseif (isset($_POST["promo"])) {
    $Promo = $_POST["promo"];
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
    .nonSoulign:hover{
        color:#495057;
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
        $FichierVide=true;
        $sortie=false;
        $heureDepart="";
       $monfichier = fopen('emargement.txt', 'r');
        while (!feof($monfichier)) {
            $ligne = fgets($monfichier);
            $etudiant = explode('|', $ligne);
            if(isset($etudiant[1])){
                $FichierVide=false;
            }
            if (isset($_GET["code"]) && $etudiant[0]==$_GET["code"] && $etudiant[3]==date('d-m-Y')||isset($_POST["code"]) && $etudiant[0]==$_POST["code"] && $etudiant[3]==date('d-m-Y')){
                $sortie=true;
                $heurArrive=$etudiant[4];
            }
        }
        fclose($monfichier);
        if(isset($_GET["code"]) || isset($_GET["aModifier"])){
            
            echo'<form method="POST" action="emargement.php" class="MonForm row insc">
                    <div class="col-md-3"></div>
                    <div class="col-md-6 bor">';
                    $codeRecup="";
                    $nomRecup="";
                    $dateNow="";
                    $heureNow="";
                    ///////////////////////////-------Promo---------------------//////////////////////
                    echo '<div class="row">
                            <div class="col-md-2"></div>
                             <select class="form-control col-md-8 espace" name="promo" readonly="readonly">';
                                echo '<option value="' . $Promo. '" selected>' . $Promo. '</option>';
                        echo'</select>
                        </div>';
                    ///////////////////////////-------Fin Promo---------------------//////////////////////
                    

                    ///////////////////////////-------Récupération informations---------------------//////////////////////

                    
                    if (!isset($_POST["valider"]) && isset($_POST["promo"]) || !isset($_POST["valider"]) && isset($_GET["promo"])) {
                        $monfichier = fopen('etudiants.txt', 'r');
                        while (!feof($monfichier)) {
                            $ligne = fgets($monfichier);
                            $tab = explode("|", $ligne);
                            if(isset($_GET["code"]) && $tab[0]==$_GET["code"] || isset($_POST["code"]) && tab[0]==$_POST["code"] ){
                                $nomRecup=$tab[2];
                                $codeRecup=$tab[0];
                            }
                        }
                        fclose($monfichier);
                        $dateNow = date('Y-m-d');
                        if($sortie==false){
                            $heureNow=date("H:i");
                        }
                        else{
                            $heureNow=$heurArrive;
                            $heureDepart=date("H:i");
                        }

                    }
                    ///////////////////////////-------Récupération informations---------------------//////////////////////
                    
                    ///////////////////////////-------recup information si modification---------------------//////////////////////
                    if(isset($_GET["aModifier"])){
                        $monfichier = fopen('emargement.txt', 'r');
                        while (!feof($monfichier)) {
                            $ligne = fgets($monfichier);
                            $tab = explode("|", $ligne);
                            if($tab[0]==$_GET["aModifier"] && $tab[3]==$_GET["date"]){
                                $codeRecup=$tab[0];
                                $nomRecup=$tab[2];
                                $datN = new DateTime($tab[3]);
                                $dateNow = $datN->format('Y-m-d');
                                $heureNow=$tab[4];
                                $heureDepart=$tab[5];
                            }
                        }
                    }
                    ///////////////////////////-------recup information si modification----------------------//////////////////////

                    ///////////////////////////-------Code---------------------//////////////////////
                    echo'<div class="row">
                            <div class="col-md-2"></div>
                            <input type="" class="form-control col-md-8 espace" name="code" value="'.$codeRecup.'" readonly="readonly" placeholder="Code">
                        </div>';
                    ///////////////////////////-------Code---------------------//////////////////////

                    ///////////////////////////-------Nom---------------------//////////////////////
                    echo'<div class="row">
                            <div class="col-md-2"></div>
                            <input type="" class="form-control col-md-8 espace" name="nom" value="'.$nomRecup.'" readonly="readonly" placeholder="Nom">
                        </div>';
                    ///////////////////////////-------Nom---------------------//////////////////////

                    ///////////////////////////-------Date---------------------//////////////////////
                    echo'<div class="row">
                            <div class="col-md-2"></div>
                            <input type="date" class="form-control col-md-8 espace" name="auj" value="'.$dateNow.'" readonly="readonly">
                        </div>';
                    ///////////////////////////-------Date---------------------//////////////////////

                    ///////////////////////////-------Arrivée---------------------//////////////////////
                    echo'<div class="row">
                            <div class="col-md-2"></div>
                            <input type="time" class="form-control col-md-8 espace" name="arrivee" value="'.$heureNow.'"   ';if($sortie==true && !isset($_GET["aModifier"])){echo' readonly="readonly"';} echo'>
                        </div>';
                    ///////////////////////////-------Arrivée---------------------//////////////////////

                    ///////////////////////////-------Sortie---------------------//////////////////////
                    echo'<div class="row">
                            <div class="col-md-2"></div>
                            <input type="time" class="form-control col-md-8 espace" name="depart" value="'.$heureDepart.'"   ';if($sortie==false && !isset($_GET["aModifier"])){echo' readonly="readonly"';} echo'>
                        </div>';
                    ///////////////////////////-------Sortie---------------------//////////////////////

                    ///////////////////////////-------EnregistrerEnregistrer---------------------//////////////////////
                    echo '<div class="row">
                        <div class="col-md-2"></div>
                        <input type="submit" class="form-control col-md-4 espace" value="Annuller" name="Annuller">
                        <input type="submit" class="form-control col-md-4 espace" value="Enregistrer" name="valider">
                    </div>';
                    ///////////////////////////-------Enregistrer---------------------//////////////////////
                    
                    echo'</div>
                </form>';
            }
            ///////////////////////////-------Continuer emargement---------------------//////////////////////
            elseif(!isset($_GET["promo"]) && isset($_POST["valider"])){
                    echo'<form method="POST" action="ListerEtudiant.php?promo='.$_POST["promo"].'" class="MonForm row insc">
                    <div class="col-md-3"></div>
                    <div class="col-md-6 bor">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <label for="" class="form-control col-md-8 text-center">Voulez-vous continuer les émargements ?</label>
                        </div>
                        <div class="row">
                            <div class="col-md-2"></div>
                            <input type="submit" class="form-control col-md-4 espace" value="Oui" name="contEmarg">
                            <a href="emargement.php" class="nonSoulign col-md-4 espace form-control text-center">Non</a>
                        </div>
                    </div>
                </form>';
            }
            ///////////////////////////-------Continuer emargement---------------------//////////////////////

            ///////////////////////////-------rechercher par jour---------------------//////////////////////
            else{
            echo'<form method="POST" action="emargement.php" class="MonForm row insc">
                    <div class="col-md-3"></div>
                    <div class="col-md-6 bor">';
                    echo '<div class="row">
                        <div class="col-md-2"></div>
                        <input type="date" class="form-control col-md-8 espace" name="jourRech"'; if(!isset($_POST["jourRech"])){echo' value="'.date('Y-m-d').'" ';}else{echo' value="'.$_POST["jourRech"].'" ';} echo'>
                    </div>';
                    echo '<div class="row">
                        <div class="col-md-3"></div>
                        <input type="submit" class="form-control col-md-6 espace" value="Lister" name="validerRechJour">
                    </div>
                    </div>
                </form>';
            }
            ///////////////////////////-------rechercher par jour---------------------//////////////////////

        ?>
        <?php
        if (isset($_POST["promo"]) || isset($_GET["promo"])) {

            ///////////////////////////////////------Debut Ajouter-----////////////////////////////////
            if (isset($_POST["valider"]) && $sortie==false) {
                $monfichier = fopen('emargement.txt', 'r');
                $code= $_POST["code"];
                $promo = $_POST["promo"];
                $nom = $_POST["nom"];
                $datN = new DateTime($_POST["auj"]);
                $date = $datN->format('d-m-Y');
                $arrivee = $_POST["arrivee"];
                $depart =$_POST["depart"];


                $monfichier = fopen('emargement.txt', 'a+');
                if($FichierVide==false){
                    $nouvU = "\n" . $code . "|" . $promo . "|" . $nom . "|" . $date . "|" . $arrivee . "|" . $depart . "|" ; //emargement
                }
                else{
                    $nouvU = $code . "|" . $promo . "|" . $nom . "|" . $date . "|" . $arrivee . "|" . $depart . "|" ; //emargement
                }
                fwrite($monfichier, $nouvU); //ajout 
                fclose($monfichier);
            }
            ####################################------Fin Ajouter-----#################################

            ///////////////////////////////////------Sortie-----///////////////////////////
            if (isset($_POST["valider"])  && $sortie == true) {

                $reecrire = "";
                $monfichier = fopen('emargement.txt', 'r');
                while (!feof($monfichier)) {

                    $ligne = fgets($monfichier);
                    $tab = explode("|", $ligne);
                    $datN = new DateTime($_POST["auj"]);
                    $dateaujj = $datN->format('d-m-Y');
                    if ( $tab[0] == $_POST["code"] && $dateaujj==$tab[3] && !isset($_GET["aModifier"])|| isset($_GET["aModifier"]) && $tab[0] == $_GET["aModifier"] && $dateaujj==$tab[3]) {//modifier si le code correspond                             
                       
                        $modif = $tab[0] . "|" . $_POST["promo"] . "|" . $_POST["nom"] . "|" . $dateaujj . "|" . $_POST["arrivee"] . "|" . $_POST["depart"] . "|\n";
                    } 
                    else {
                        $modif = $ligne;
                    }
                    $reecrire = $reecrire . $modif;
                }
                fclose($monfichier);
                $monfichier = fopen('emargement.txt', 'w+');//$reecrire="";
                fwrite($monfichier, trim($reecrire));
                fclose($monfichier);
            }
            ####################################------Sortie----#############################
        }
            if($FichierVide==false && !isset($_GET["aModifier"]) || isset($_GET["code"]) || isset($_POST["valider"])){
            echo '<table class="col-12 table tabliste table-hover">
            <thead class="">
                <tr class="row">
                    <td class="col-md-2 text-center gras">N° CI</td>
                    <td class="col-md-2 text-center gras">Référentiel</td>
                    <td class="col-md-2 text-center gras">Nom</td>
                    <td class="col-md-2 text-center gras">Date</td>
                    <td class="col-md-1 text-center gras">Arrivée</td>
                    <td class="col-md-1 text-center gras">Sortie</td>
                    <td class="col-md-2 text-center gras">Modification</td>
                </tr>
            </thead>
            <tbody id="developers">';
            }
            /////////////////////////////////////////------Debut Affichage-----///////////////////////// 
            if(!isset($_POST["valider"]) && !isset($_GET["aModifier"])){
                $monfichier = fopen('emargement.txt', 'r');
                if(isset($_POST["jourRech"])){
                    $datN = new DateTime($_POST["jourRech"]);
                    $date = $datN->format('d-m-Y');
                }
                $nbr=0;
                while (!feof($monfichier)) {
                    $ligne = fgets($monfichier);
                    $etudiant = explode('|', $ligne);
                    if (isset($_POST["validerRechJour"]) && $FichierVide==false && $etudiant[3]==$date||!isset($_POST["validerRechJour"]) && $FichierVide==false && !isset($_POST["recherche"]) && $etudiant[3]==date('d-m-Y')||!isset($_POST["validerRechJour"]) &&  $FichierVide==false && isset($_POST["recherche"])  && !empty($_POST["aRechercher"]) && strstr(strtolower($ligne), strtolower($_POST["aRechercher"])) && !empty($_POST["aRechercher"]) ||!isset($_POST["validerRechJour"]) &&  $FichierVide==false && $etudiant[1] == $Promo && isset($_POST["recherche"]) && empty($_POST["aRechercher"])) {
                        echo
                            '<tr class="row">
                                <td class="col-md-2 text-center">' . $etudiant[0] . '</td>
                                <td class="col-md-2 text-center">' . $etudiant[1] . '</td>
                                <td class="col-md-2 text-center">' . $etudiant[2] . '</td>
                                <td class="col-md-2 text-center">' . $etudiant[3] . '</td>
                                <td class="col-md-1 text-center">' . $etudiant[4] . '</td>
                                <td class="col-md-1 text-center">' . $etudiant[5] . '</td>
                                <td class="col-md-2 text-center"><a href="emargement.php?aModifier='.$etudiant[0].'&promo='.$etudiant[1].'&&date='.$etudiant[3].'"><button class="btn btn-outline-primary" >Modifier</button></a></td>
                            </tr>';
                            $nbr++;
                    }
                }
                fclose($monfichier);
            }
            elseif(isset($_POST["valider"])){
                $datN = new DateTime($_POST["auj"]);
                $date = $datN->format('d-m-Y');
                echo
                    '<tr class="row">
                        <td class="col-md-2 text-center">' . $_POST["code"] . '</td>
                        <td class="col-md-2 text-center">' . $_POST["promo"] . '</td>
                        <td class="col-md-2 text-center">' . $_POST["nom"]  . '</td>
                        <td class="col-md-2 text-center">' . $date . '</td>
                        <td class="col-md-1 text-center">' . $_POST["arrivee"]  . '</td>
                        <td class="col-md-1 text-center">' . $_POST["depart"]  . '</td>
                        <td class="col-md-2 text-center"><a href="emargement.php?aModifier='.$_POST["code"] .'&promo='.$_POST["promo"].'&&date='.$date.'"><button class="btn btn-outline-primary" >Modifier</button></a></td>
                    </tr>';
                    $nbr++;
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