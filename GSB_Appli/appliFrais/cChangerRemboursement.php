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
      <div id="corpsForm" >
        <form action="" method="post">

        <fieldset>
          <legend>créé un utilisateur</legend>
          <p>
            <label for="etp">Etape :</label>
            <input type="text" name="etp" value="<?php echo($fraitforfait[0]); ?>" maxlength="4" required readonly>
          </p>
          <p>
            <label for="nui">Nuit :</label>
            <input type="text" name="nui" value="<?php echo($fraitforfait[2]); ?>" maxlength="30"  required>
          </p>
          <p>
            <label for="km">Kilometre :</label>
            <input type="text" name="km" value="<?php echo($fraitforfait[1]); ?>" maxlength="30"required>
          </p>
          <p>
            <label for="rep"> Repas :</label>
            <input type="text" name="rep" value="<?php echo($fraitforfait[3]); ?>" maxlength="8" required>
          </p>
        </fieldset>
        <input id="ok" type="submit" value="Valider" size="20" title="Enregistrer les modification" />
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
