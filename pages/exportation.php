<?php
session_start();
if (!isset($_SESSION["nom"])) {
    echo '<h1>Connectez-vous</h1>';
    header('Location: ../index.php');
    exit();
}
$_SESSION["actif"] = "exportation";
?>

<!DOCTYPE html>
<html lang="FR-fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/MonStyle.css">
    <title>Exportation</title>
    <style>
        .entrBouton{
            margin-left:3%;
        }
        .l2p{
            margin-left:2%;
        }
        .l3p{
            margin-left:3%;
        }
        label{
            font-style: italic;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bolder;
        }
        .mesTitres{
            font-style: italic;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bolder;
            text-decoration: underline dotted black;
        }
        .espace{
            margin-top: 5%;
        }
        .MonForm {
            margin-top: 7%;
        }
        .bor{
            background-color: #d0c9d6a1;
            border: 2px solid #3e3f40b9;
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
        try {
            include("connexionBDD.php");
    ?> 
        <div class="MonForm row insc">
            <div class="col-md-1"></div>
            <div class="col-md-10 bor">
                <form method="POST" action="../pdf/Mes_PDF/pdf_etudiants.php" target="_blank" class="">
                    <legend class="mesTitres">Apprenants</legend>
                    <div class="row">
                        <label for="" class="col-md-2 l2p" >Référentiel</label>
                        <label for="" class="col-md-3 l3p"  name="date_visiteur">Nom apprenant</label>
                    </div>
                    <div class="row">
                        <input type="text" class="form-control col-md-2  entrBouton" value="" placeholder="Réferentiel"  name="ref_ap">
                        <input type="text" class="form-control col-md-3  entrBouton" value="Tous les apprenants" placeholder="Nom de l'apprenant"  name="nom_ap">
                        <input type="submit" class="btn btn-outline-primary col-md-1  entrBouton" value="PDF" name="pdf_ap">
                    </div>
                </form>
                <form method="POST" action="../pdf/Mes_PDF/pdf_emargement.php" target="_blank" class="espace">
                    <legend class="mesTitres">Emargement</legend>
                     <div class="row">
                        <label for="" class="col-md-2 l2p" >Référentiel</label>
                        <label for="" class="col-md-3 l3p"  name="date_visiteur">Nom apprenant</label>
                        <label for="" class="col-md-2 l3p"  name="date_visiteur">Date début</label>
                        <label for="" class="col-md-2 l3p"  name="date_visiteur">Date fin</label>
                    </div>
                    <div class="row">
                        <input type="text" class="form-control col-md-2 entrBouton" value="" placeholder="Réferentiel"  name="ref_em">
                        <input type="text" class="form-control col-md-3 entrBouton" value="Tous les apprenants" placeholder="Nom de l'apprenant"  name="nom_em">
                        <input type="date" class="form-control col-md-2 entrBouton" value="" name="date_debut_em">
                        <input type="date" class="form-control col-md-2 entrBouton" value="" name="date_fin_em">
                        <input type="submit" class="btn btn-outline-primary col-md-1 entrBouton" value="PDF" name="pdf_em">
                    </div>
                </form> 
                <form method="POST" action="../pdf/Mes_PDF/pdf_visiteurs.php" target="_blank" class="espace">
                    <legend class="mesTitres">Visiteurs</legend>
                    <div class="row">
                        <label for="" class="col-md-3 l2p" >Nom</label>
                        <label for="" class="col-md-2 l3p"  name="date_visiteur">Date début</label>
                        <label for="" class="col-md-2 l3p"  name="date_visiteur">Date fin</label>
                    </div>
                    <div class="row">
                        <input type="text" class="form-control col-md-3 entrBouton" value="Tous les visiteurs" placeholder="Nom du visiteur"  name="nom_visiteur">
                        <input type="date" class="form-control col-md-2 entrBouton"  name="date_debu_visiteur">
                        <input type="date" class="form-control col-md-2 entrBouton"  name="date_fin_visiteur">
                        <input type="submit" class="btn btn-outline-primary col-md-1 entrBouton" value="PDF" name="pdf_visiteur">
                    </div>
                </form> 
            </div>
        </div>
    <?php
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