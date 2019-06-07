<?php
require('../fpdf.php');
try {
    
    if(isset($_GET["date_pdf"])) {
        $datN=new DateTime($_GET["date_pdf"]);
        $ladate = $datN->format('Y-m-d');
    }
    $tout="";
    include("../../pages/connexionBDD.php");
    /////////-----recuperation des données des etudiants----///////////
    if(isset($_GET["date_pdf"])) {
        $codemysql = "SELECT * FROM emargement WHERE Date_emargement='$ladate'";
    }
    elseif(isset($_GET["Nci_etudiant"])){
        $nci_etudiants=$_GET["Nci_etudiant"];
        $codemysql = "SELECT * FROM emargement WHERE NCI='$nci_etudiants'";
    }
    else{
        $codemysql = "SELECT * FROM emargement ORDER BY Date_emargement ASC";
    }
    $emargement=recuperation($connexion,$codemysql);
    ///////////-----Fin recuperation des données des etudiants----///////
    for($i=0;$i<count($emargement);$i++) {
        $nci=$emargement[$i]["NCI"];

        ///////////-----recuperation des données des etudiants----///////////
        $codemysql = "SELECT etudiants.Nom,etudiants.id_referentiels FROM etudiants WHERE NCI='$nci'"; //le code mysql
        $etudiants=recuperation($connexion,$codemysql);
        ///////////-----Fin recuperation des données des etudiants----///////
        
        $id_ref=$etudiants[0]["id_referentiels"];

        ///////////-----recuperation des données des etudiants----///////////
        $codemysql = "SELECT referentiels.Nom FROM referentiels WHERE id_referentiels='$id_ref'"; //le code mysql
        $nom_ref=recuperation($connexion,$codemysql);
        ///////////-----Fin recuperation des données des etudiants----///////

        $tout=$tout.$emargement[$i]["NCI"].";".$nom_ref[0]["Nom"].";".$etudiants[0]["Nom"].";".$emargement[$i]["Date_emargement"].";".$emargement[$i]["Arrivee"].";".$emargement[$i]["Depart"].";".$emargement[$i]["Code_agents_arrivee"].";".$emargement[$i]["Code_agents_depart"].";\n";
        $monfichier=fopen("../Mes_fichiers_texte/emargement.txt","w");
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
        // Données
        $fill = false;
        $a=0;
        foreach($data as $row)
        {
            $w = array(30, 17, 57, 22,17,17,17,17);//modifier le nombre d'élement max 190
            if($a!=0 && $a%43==0){
                $this->Cell(array_sum($w),0,' ','T');//tracer jusqu'a la fin
                $this->Cell(-array_sum($w),0,' ','');//revenir à la ligne
            }
                
            if($a==0||$a%43==0){
                // Couleurs, épaisseur du trait et police grasse
                $this->SetFillColor(0,123,255);
                $this->SetTextColor(255);
                $this->SetDrawColor(128,0,0);
                $this->SetLineWidth(.3);
                $this->SetFont('','B');
                // En-tête
                
                
                for($i=0;$i<count($header);$i++){
                    $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
                }
                $this->Ln();
                
                // Restauration des couleurs et de la police
                $this->SetFillColor(224,235,255);
                $this->SetTextColor(0);
                $this->SetFont('');
            }
            
            $a++;
            for($données=0;$données<=7;$données++){
                $this->Cell($w[$données],6,pour_conversion($row[$données]),'LR',0,'L',$fill);
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
$header = array('NCI', 'Ref', 'Nom', 'Date',"Arrive","Depart","Agent 1","Agent 2");
// Chargement des données
$data = $pdf->LoadData('../Mes_fichiers_texte/emargement.txt');
$pdf->SetFont('Arial','',10);
$pdf->AddPage();

$pdf->FancyTable($header,$data);
$pdf->Output();
?>