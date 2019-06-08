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
        .mesTitres{
            font-style: italic;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bolder;
        }
        .espace{
            margin-top: 5%;
        }
        .MonForm {
            margin-top: 10%;
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
        <form method="POST" action="" class="MonForm row insc">
            <div class="col-md-1"></div>
            <div class="col-md-10 bor">
                 <fieldset class="">
                    <legend class="mesTitres">Visiteurs</legend>
                    <div class="row">
                        <input type="text" class="form-control col-md-3 entrBouton" value="" placeholder="Nom du visiteur"  name="nom_visiteur">
                        <input type="date" class="form-control col-md-3 entrBouton"  name="nom_ap">
                        <input type="submit" class="btn btn-outline-primary col-md-1 entrBouton" value="PDF" name="pdf_visiteur">
                    </div>
                </fieldset> 
                <fieldset class="espace">
                    <legend class="mesTitres">Apprenants</legend>
                    <div class="row">
                        <input type="text" class="form-control col-md-3  entrBouton" value="" placeholder="Réferentiel"  name="ref_ap">
                        <input type="text" class="form-control col-md-3  entrBouton" value="" placeholder="Nom de l'apprenant"  name="nom_ap">
                        <input type="submit" class="btn btn-outline-primary col-md-1  entrBouton" value="PDF" name="pdf_ap">
                    </div>
                </fieldset>
                <fieldset class="espace">
                    <legend class="mesTitres">Emargement</legend>
                    <div class="row">
                        <input type="text" class="form-control col-md-3 entrBouton" value="" placeholder="Réferentiel"  name="ref_em">
                        <input type="text" class="form-control col-md-3 entrBouton" value="" placeholder="Nom de l'apprenant"  name="nom_em">
                        <input type="date" class="form-control col-md-3 entrBouton" value="" name="nom_em">
                        <input type="submit" class="btn btn-outline-primary col-md-1 entrBouton" value="PDF" name="pdf_ap">
                    </div>
                </fieldset> 
            </div>
        </form>
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