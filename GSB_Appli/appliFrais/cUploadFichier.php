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
    <h2>envoyer les justifivatif</h2>
    <h3>type de fichier</h3>
    <?php
$typeJustificatif = recupererTypeJustificatif($idConnexion);
var_dump($typeJustificatif)
     ?>
     <fieldset>
       <legend>selectioner le fichier</legend>

    <form class="" action="include/_uploadFichier.php" method="POST" enctype="multipart/form-data">
      <select class="" name="typeJustificatif" required>
        <option value="" disabled selected>type de fichier</option>
        <option value="HF">Hors Forfait</option>
        <?php
          foreach ($typeJustificatif as $idJustificatif) {
            ?>
              <option value="<?php echo $idJustificatif[0] ?>"><?php echo $idJustificatif[1] ?></option>
            <?php
          }
         ?>


      </select>
      <br>
      <select class="mois" name="mois" >
        <option value="" disabled selected>mois</option>
        <?php
        $listMois = obtenirTousMoisFicheFrais($idConnexion);

        foreach ($listMois as $mois ) {
          //var_dump($mois);
          $noMois = intval(substr($mois[0], 4, 2));
          $annee = intval(substr($mois[0], 0, 4));
          ?>
          <option value=<?php echo ($mois[0]); ?>><?php echo obtenirLibelleMois($noMois) . " " . $annee; ?></option>
          <?php
        };
        ?>
      </select>
      <br>
<input type="file" name="fichier" value="" title="selectioner un fichier" required>
<br>
<br>
<input type="submit" id="ok" title="valider" name="upload">
    </form>
  </fieldset>

    </div>
