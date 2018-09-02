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
$mois = $_POST['mois'];
if (!file_exists("../justificatif")){
  mkdir("../justificatif");
}
if (!file_exists("../justificatif/$idVisiteur")){
  mkdir("../justificatif/$idVisiteur");
}
if (!file_exists("../justificatif/$idVisiteur/$mois")){
  mkdir("../justificatif/$idVisiteur/$mois");
}
if( isset($_POST['upload']) ) // si formulaire soumis
{
    $content_dir = "../justificatif/$idVisiteur/$mois/"; // dossier où sera déplacé le fichier

    $tmp_file = $_FILES['fichier']['tmp_name'];

    if( !is_uploaded_file($tmp_file) )
    {
        exit("Le fichier est introuvable");
    }

    // on vérifie maintenant l'extension
    $type_file = $_FILES['fichier']['type'];

    if( !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif')&& !strstr($type_file, 'png') )
    {
        exit("Le fichier n'est pas une image");
    }

    // on copie le fichier dans le dossier de destination
    $_FILES['fichier']['name'] = $_POST['typeJustificatif']."_".str_replace(' ','',substr(microtime(),2 )).".png";
    echo($_FILES['fichier']['name']);
    $name_file = $_FILES['fichier']['name'];

    if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )
    {
        exit("Impossible de copier le fichier dans $content_dir");
    }

    echo "Le fichier a bien été uploadé";
}
 ?>
 <?php
header("Location: ../cUploadFichier.php")
  ?>
