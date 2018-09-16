<?php
/**
* Page d'accueil de l'application web AppliFrais
* @package default
* @todo  RAS
*/

require("_init.inc.php");
$idVisiteur = obtenirIdUserConnecte();
// page inaccessible si visiteur non connecté
if ( ! estVisiteurConnecte() )
{
  header("Location: ../cSeConnecter.php");
}
?>
<?php
var_dump($_POST);
var_dump($_FILES);
echo "<br>";

print_r($_POST);
echo "<br>";
print_r($_FILES);
echo "<br>";
echo($_FILES['fichier']['name']);
echo "<br>";
$type_file = $_FILES['fichier']['type'];
var_dump($type_file);

$ext = str_replace('/','.',strstr($_FILES['fichier']['type'], '/'));
echo "<br>";
echo "----------------------------------------------";
var_dump ($ext);
echo "<br>";
if (!isset($_POST['mois'])){
  $mois = sprintf("%04d%02d", date("Y"), date("m"));
}
else{
  $mois = $_POST['mois'];
};
if ($_POST['typeJustificatif'] == 'HF') {
  if ($_POST['etape'] == "validerAjoutLigneHF") {
      verifierLigneFraisHF($_POST['txtDateHF'], $_POST['txtLibelleHF'], $_POST['txtMontantHF'], $tabErreurs);
      if ( nbErreurs($tabErreurs) == 0 ) {
          // la nouvelle ligne ligne doit être ajoutée dans la base de données
          ajouterLigneHF($idConnexion, $mois, obtenirIdUserConnecte(), $_POST['txtDateHF'], $_POST['txtLibelleHF'], $_POST['txtMontantHF']);
      }
  }
  var_dump($_POST['typeJustificatif']);
  if (!file_exists("../justificatif")){
    mkdir("../justificatif");
  }
  if (!file_exists("../justificatif/$idVisiteur")){
    mkdir("../justificatif/$idVisiteur");
  }
  if (!file_exists("../justificatif/$idVisiteur/$mois")){
    mkdir("../justificatif/$idVisiteur/$mois");
  }
  $content_dir = "../justificatif/$idVisiteur/$mois/"; // dossier où sera déplacé le fichier
}
elseif ($_POST['typeJustificatif'] == 'CG') {
  if (!file_exists("../justificatif")){
    mkdir("../justificatif");
  }
  if (!file_exists("../justificatif/$idVisiteur")){
    mkdir("../justificatif/$idVisiteur");
  }
  if (!file_exists("../justificatif/$idVisiteur/carte_grise")){
    mkdir("../justificatif/$idVisiteur/carte_grise");
  }
  $content_dir = "../justificatif/$idVisiteur/carte_grise/";// dossier où sera déplacé le fichier
}
elseif ($_POST['typeJustificatif'] == 'AT') {
  if (!file_exists("../justificatif")){
    mkdir("../justificatif");
  }
  if (!file_exists("../justificatif/$idVisiteur")){
    mkdir("../justificatif/$idVisiteur");
  }
  if (!file_exists("../justificatif/$idVisiteur/autre")){
    mkdir("../justificatif/$idVisiteur/autre");
  }
  $content_dir = "../justificatif/$idVisiteur/autre/";// dossier où sera déplacé le fichier
}
else {
  header("Location: ../cUploadFichier.php");
}





if( isset($_POST) ) // si formulaire soumis
{

  $tmp_file = $_FILES['fichier']['tmp_name'];

  if( !is_uploaded_file($tmp_file) )
  {
    exit("Le fichier est introuvable");
  }

  // on vérifie maintenant l'extension

  if( !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'png') )
  {
    exit("Le fichier n'est pas une image");
  }
  // on copie le fichier dans le dossier de destination
  $_FILES['fichier']['name'] = $_POST['typeJustificatif']."_".str_replace(' ','',substr(microtime(),2 )).$ext;
  echo($_FILES['fichier']['name']);
  $name_file = $_POST['typeJustificatif']."_".str_replace(' ','',substr(microtime(),2 )).$ext;

  if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )
  {
    exit("Impossible de copier le fichier dans $content_dir");
  }

  echo "Le fichier a bien été uploadé";
}
?>
<?php
//header("Location: ../cUploadFichier.php")
?>
