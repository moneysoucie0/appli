<?php

require("fpdf.php");

class PDF extends FPDF
{
// En-tête
    function Header(){
        // Logo
        $this->Image("./images/logo.jpg",10,6,30);
        // Police Arial gras 15
        $this->SetFont('Arial','B',15);
        // Décalage à droite
        $this->Cell(80);
        // Titre
        $this->Cell(30,10,'Fiche de Frais de '.avoirDate($_POST['mois']),0,0,'C');
        // Saut de ligne
        $this->Line(10, 30, 200, 30);
        $this->Ln(25);


    }

// Pied de page
    function Footer(){
        $this->Line(10, 280, 200, 280);
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Police Arial italique 8
        $this->SetFont('Arial','I',8);
        // Numéro de page
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

//Chargement des données
    function LoadData($file){
        // Lecture des lignes du fichier
        $lines = file($file);
        $data = array();
        foreach($lines as $line)
            $data[] = explode(';',trim($line));
        return $data;
    }

// Tableau coloré
    function HorsForfait($header, $data){
        // Couleurs, épaisseur du trait et police grasse
        $this->SetFillColor(0,0,255);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,128);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // En-tête
        $w = array(20, 105, 40, 25);
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
            $this->Cell($w[0],6,utf8_decode($row[0]),'LR',0,'L',$fill);
            $this->Cell($w[1],6,utf8_decode($row[1]),'LR',0,'L',$fill);
            $this->Cell($w[2],6,utf8_decode($row[2]),'LR',0,'R',$fill);
            $this->Cell($w[3],6,utf8_decode($row[3]),'LR',0,'R',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Trait de terminaison
        $this->Cell(array_sum($w),0,'','T');
    }

    // Tableau coloré
    function Forfait($header, $data){
        // Couleurs, épaisseur du trait et police grasse
        $this->SetFillColor(0,0,255);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,128);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // En-tête
        $w = array(47.5, 47.5, 47.5, 47.5);
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
            $this->Cell($w[0],6,utf8_decode($row[0]),'LR',0,'C',$fill);
            $this->Cell($w[1],6,utf8_decode($row[1]),'LR',0,'C',$fill);
            $this->Cell($w[2],6,utf8_decode($row[2]),'LR',0,'C',$fill);
            $this->Cell($w[3],6,utf8_decode($row[3]),'LR',0,'C',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Trait de terminaison
        $this->Cell(array_sum($w),0,'','T');
    }













}
?>