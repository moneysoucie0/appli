<?php
/**
 * Page de saisie et de modification d'un vehicule de l'utilisateur de l'application web AppliFrais
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");
 // require("functiontutur.php");


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
      <h2>Ma voiture</h2>

      <form method="POST" action=''>
         <fieldset required>
          <legend>Renseigné le vehicule</legend>
          <select name="nbChevaux">
            <option disabled="" selected="">nombre de cheveau fiscaux</option>
            <option value=3>3 et moins</option>
            <option value=4>4</option>
            <option value=5>5</option>
            <option value=6>6</option>
            <option value=7>7 et plus</option>
          </select>
          XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
          <br>
          <?php
          $idVisiteur = obtenirIdUserConnecte();
          $voiture = obtenirVoiture($idVisiteur, $idConnexion);
          if (!is_null($voiture)){
             echo "vous avez un vehicule à ".$voiture["vehicule"]." chevaux fiscaux";
           }
          ?>
  <br><br>
          <input id="ok" type="submit" value="Valider" size="20" title="Enregistrer le vehicule" />
          <input id="annuler" type="reset" value="Effacer" size="20" maxlength="2" />
       </fieldset>
      </form>
  </div>
<?php

if (!empty($_POST)){
  $nbChevaux = $_POST['nbChevaux'];
      if (!is_null($nbChevaux)){
         $nbChevaux = intval($nbChevaux);
         modifierVehicule($idVisiteur, $idConnexion, $nbChevaux);
      }
}
?>
<?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>
