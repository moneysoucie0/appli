<?php
/**
* Page d'accueil de l'application web AppliFrais
* @package default
* @todo  RAS
*/

require("_init.inc.php");

// page inaccessible si visiteur non connecté
if ( ! estVisiteurConnecte() )
{
  header("Location: ../cSeConnecter.php");
}
?>
<?php
var_dump(is_uploaded_file());
var_dump($_POST);
var_dump($_FILES);
 ?>
 <?php
//header("Location: ../cUploadFichier.php")
  ?>
