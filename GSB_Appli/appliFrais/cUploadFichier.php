<?php
/**
 * Page d'accueil de l'application web AppliFrais
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
  if ( ! estVisiteurConnecte() )
  {
        header("Location: cSeConnecter.php");
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
?>
  <!-- Division principale -->
  <div id="contenu">
    <h2>envoyer les justifivatifs</h2>
    <h3>type de fichier</h3>
    <?php
$typeJustificatif = recupererTypeJustificatif($idConnexion);
//var_dump($typeJustificatif)
     ?>
     <fieldset>
       <legend>selectioner le fichier</legend>

    <form  action="include\_uploadFichier.php" method="POST" enctype="multipart/form-data" >
      <select class="" name="typeJustificatif" required>
        <option disabled selected>type de justificatif </option>
        <!-- <option value="HF">Hors Forfait</option> -->
        <option value="CG">Carte Grise </option>
        <option value="AT">Autre</option>
      </select>
      <br>
      <input type="date" name="mois" value="000000" hidden>
<!-- <input type="file" name="fichier" title="selectioner un fichier" required> -->
<input type="file"  name="fichier" accept=".jpg, .bmp, .png , .jpeg"
                       title="Sélectionnez une pièce justificative à faire valoir pour vos depenses hors forfait" value="null" required />
<br>
<br>
<input type="submit" id="ok" title="valider" >
    </form>
  </fieldset>

    </div>
