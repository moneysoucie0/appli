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
<?php
$fraitforfait = (recupererFraitForfait($idConnexion));
// var_dump($fraitforfait);
?>
<div id="contenu">
  <h2>gestion des fraits forfaitisés</h2>
  <form action="" method="post">
    <div class="corpsForm" >

      <fieldset>
        <legend>changer les remboursements</legend>
        <p>
          <label for="etp">Etape :</label>
          <input type="text" name="etp" value="<?php echo($fraitforfait[0]); ?>" maxlength="4" required readonly>
          <div class="cb"></div>
        </p>
        <p>
          <label for="nui">Nuitée :</label>
          <input type="text" name="nui" value="<?php echo($fraitforfait[2]); ?>" maxlength="30"  required>
          <div class="cb"></div>
        </p>
        <label for="rep"> Repas :</label>
        <p>
          <input type="text" name="rep" value="<?php echo($fraitforfait[3]); ?>" maxlength="8" required>
          <div class="cb"></div>
        </p>
      </fieldset>
    </div>
    <div class="piedForm">
      <input id="ok" type="submit" value="Valider" size="20" title="Enregistrer les modification" />
      <div class="cb"></div>
    </div>
    <?php
    if (!empty($_POST)){
      // var_dump($_POST);
      // var_dump(floatval($_POST['etp']));
      // var_dump(floatval($_POST['rep']));
      // var_dump(floatval($_POST['nui']));
      // var_dump(floatval($_POST['km']));

      if (changerRemboursement(floatval($_POST['etp']),floatval($_POST['rep']),floatval($_POST['nui']),floatval($_POST['km']), $idConnexion)){
        ?><p class="info"> Le barem de remboursement a bien modifié</p><?php

      }
    };
    ?>
  </div>
</form>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>
