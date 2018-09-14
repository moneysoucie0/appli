<?php
require("_init.inc.php");
var_dump($_POST);
$size = count($_POST);
$size = ($size-2)/3;
echo "<br>";
$date = str_replace("-", "",$_POST["date"]);
//echo($date);
$date = substr($date, 0 , 6 );
echo ($date);
$i=0;
echo "<br>";
$label = "montant".$i;
$labelB = "acceptation".$i;

while (true) {

  if ($i == $size) {
    break;
  }

  $labelA = "montant".$i;
  $labelB = "acceptation".$i;
  $labelC = "id".$i;


  echo ($label);
  echo "<br>";
  echo ($_POST[$label]);
  echo "<br>";
  echo ($labelB);
  echo "<br>";
  echo "<br>";
  echo ($_POST[$labelB]);
  echo "<br>";

  echo "<br>";
  echo ($_POST[$labelA]);
  echo "<br>";
  echo ($_POST[$labelB]);
  echo "<br>";
  echo ($_POST[$labelC]);
  echo "<br>";
changerEtatHorsForfait($_POST[$labelC],$_POST[$labelB], $idConnexion );
  $i++;
  // if ($i==10) {
  //   break;
  // }
  // if ($error) {
  //   break;
  // }
}
header("Location: ../cConsultFichesFrais.php")
?>
