<?php

require("./include/fdpf/pdf.php");
$repInclude = './include/';
require($repInclude . "_init.inc.php");
if (isset($_POST['idVisiteur'])) {
  $idVisiteur = $_POST['idVisiteur'];
}
else {
  $idVisiteur = obtenirIdUserConnecte();
}

function formatageDate($date){
  return $date[8] . $date[9] . "/" . $date[5] . $date[6];
}

function avoirDate($date){
  $moisL = array('Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
  $mois = $date[4] . $date[5];
  $mois = intval($mois);
  $annee = $date[0].$date[1].$date[2].$date[3];
  return $moisL[$mois - 1] . " " . $annee;
}

$header = array();



$req = "SELECT `libelle` FROM `fraisforfait` ORDER BY `fraisforfait`.`id` ASC";
$result = mysqli_query($idConnexion, $req);
while($row = mysqli_fetch_assoc($result)){
  array_push($header, $row['libelle']);
}
$result = "";
?>



<?php
//var_dump($_POST);
$mois = $_POST['mois'];
//var_dump($mois);

$req = "SELECT `ETP`,`KM`,`NUI`,`REP` FROM `lignefraisforfait` WHERE idVisiteur = '".$idVisiteur."' and mois = '".$mois."'";
//var_dump($req);
$result = mysqli_query($idConnexion, $req);
//var_dump($result);

$docforfait = "forfait.txt";
$texte = "";
$row = mysqli_fetch_assoc($result);
//var_dump($row);
$row = [$row['ETP'],$row['KM'],$row['NUI'],$row['REP']];
foreach ($row as $element) {
    $texte = $texte . $element . ";";
}
file_put_contents($docforfait, $texte);



$reqHorsForfait = "select date, libelle, montant from lignefraishorsforfait where idVisiteur = '".$idVisiteur."' and mois = '".$_POST['mois']."' order by date ASC";
$resultHorsForfait = mysqli_query($idConnexion, $reqHorsForfait);

// Création d'un document texte pour permettre de renseigner tout les champs du tableau contenu dans le PDF
$doc = "texte.txt";
$txt = "";
while($ligneHorsForfait =  mysqli_fetch_assoc($resultHorsForfait)){
  $txt = $txt . formatageDate($ligneHorsForfait['date']) . ";" . $ligneHorsForfait['libelle'] . ";" . "2727" . ";" . $ligneHorsForfait['montant'] . PHP_EOL;
}
file_put_contents($doc, $txt);


// Création du PDF
$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetDrawColor(0,0,128);
$data = $pdf->LoadData('forfait.txt');
$header = array('Forfait Etape', 'Frais kilométrique', 'Nuitée Hôtel', 'Repas Restaurant');
$pdf->Forfait($header, $data);
$pdf->Ln();
$pdf->Cell(0, 10, "", 0, 1);
// Titres des colonnes
$header = array('Date', 'Libelle', 'NbJustificatif', 'Montant');
// Chargement des données
$data = $pdf->LoadData('texte.txt');
$pdf->HorsForfait($header, $data);

// Création du PDF et suppression des fichiers texte crées précédemment
$pdf->Output();
unlink("texte.txt");
unlink("forfait.txt");
?>
