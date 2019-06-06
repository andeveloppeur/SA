<?php
require('../fpdf.php');
try {
    $tout="";
    include("../../pages/connexionBDD.php");
    ///////////-----recuperation des données des etudiants----///////////
    $codemysql = "SELECT * FROM etudiants"; //le code mysql
    $etudiants=recuperation($connexion,$codemysql);
    ///////////-----Fin recuperation des données des etudiants----///////
    for($i=0;$i<count($etudiants);$i++) {
        $id_ref=$etudiants[$i]["id_referentiels"];
        ///////////-----recuperation des données des etudiants----///////////
        $codemysql = "SELECT referentiels.Nom FROM referentiels WHERE id_referentiels='$id_ref'"; //le code mysql
        $nom_ref=recuperation($connexion,$codemysql);
        ///////////-----Fin recuperation des données des etudiants----///////
        $tout=$tout.$etudiants[$i]["NCI"].";".$nom_ref[0]["Nom"].";".$etudiants[$i]["Nom"].";".$etudiants[$i]["Naissance"].";".$etudiants[$i]["Telephone"].";".$etudiants[$i]["Email"].";\n";
        $monfichier=fopen("../Mes_fichiers_texte/etudiants.txt","w");
        fwrite($monfichier,trim($tout));
        fclose($monfichier);
    }
}
catch (PDOException $e) {
    echo "ECHEC : " . $e->getMessage(); //en cas d erreur lors de la connexion à la base de données mysql
} 




function pour_conversion($value){//pour consersion en utf-8
    $value = mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
    return $value;
}
class PDF extends FPDF
{
// Chargement des données
function LoadData($file){
    // Lecture des lignes du fichier
    $lines = file($file);
    $data = array();
    foreach($lines as $line)
        $data[] = explode(';',trim($line));
    return $data;
}

// Tableau coloré
function FancyTable($header, $data)
{
    // Couleurs, épaisseur du trait et police grasse
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // En-tête
    $w = array(30, 17, 55, 20,20,53);//modifier le nombre d'élement max 190

    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();

    // Restauration des couleurs et de la police
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Données
    $fill = false;



    foreach($data as $row)
    {
        for($i=0;$i<=5;$i++){
            $this->Cell($w[$i],6,pour_conversion($row[$i]),'LR',0,'L',$fill);
        } 

        $this->Ln();
        $fill = !$fill;
    }
    // Trait de terminaison
    $this->Cell(array_sum($w),0,'','T');
}
}

$pdf = new PDF();
// Titres des colonnes
$header = array('NCI', 'Ref', 'Nom', 'Naissance',"Telephone","Email");
// Chargement des données
$data = $pdf->LoadData('../Mes_fichiers_texte/etudiants.txt');
$pdf->SetFont('Arial','',10);
$pdf->AddPage();

$pdf->FancyTable($header,$data);
$pdf->Output();
?>