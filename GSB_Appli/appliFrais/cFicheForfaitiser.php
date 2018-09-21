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
  <h2>Gestion des fiches de frais forfaitisée</h2>
  <form class="" action="" method="post">
    <div class="corpsForm">
      <fieldset>
        <legend>Selectionez le mois des fiches de frais à afficher</legend>

        <select class="mois" name="mois" >
          <option value="" disabled selected>mois</option>
          <?php
          $listMois = obtenirTousMoisFicheFrais($idConnexion);

          foreach ($listMois as $mois ) {
            //var_dump($mois);
            $noMois = intval(substr($mois[0], 4, 2));
            $annee = intval(substr($mois[0], 0, 4));
            $mois = $mois[0];
            ?>
            <option value=<?php echo ($mois[0]); ?>><?php echo obtenirLibelleMois($noMois) . " " . $annee; ?></option>
            <?php
          };
          ?>
        </select>
        <input type="submit" name="recherche mois" value="valider le mois" size="20" >
      </fieldset>
    </div>
  </form>

  <?php
  //var_dump($_POST);
  if(!empty($_POST)){
    //  var_dump($listMois);
    //var_dump($_POST['mois']);
    $mois = $_POST['mois'];
  }
  /*else {
  $mois = array_pop($listMois);
  var_dump($mois[0]);
};*/
$fiches = obtenirFicheFrait($mois,$idConnexion);
//var_dump($fiches);

?>
<fieldset >
  <legend>selection des fiche de frait et l'etat a changer</legend>


  <form class="" action="" method="post">
    <input type="hidden" name="mois" value="<?php echo($mois); ?>">
    <table class="listeLegere">
      <thead>
        <tr>
          <th></th>
          <th>nom</th>
          <th>prenom</th>
          <th>etapes</th>
          <th>kilométres</th>
          <th>nuitées</th>
          <th>repas</th>
          <th>etat</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i =0;
        /*$mois = 201806;
        $fiches = obtenirFicheFrait($mois,$idConnexion);*/
        foreach ($fiches as $fiche) {
          //var_dump($fiche);
          ?>
          <tr>


            <td><input type="checkbox" value="<?php echo($fiche[0]); ?> " name = "<?php echo($i); ?>" checked></td>
            <td><?php echo($fiche[1]) ?></td>
            <td><?php echo($fiche[2]) ?></td>
            <td><?php echo($fiche[3]) ?></td>
            <td><?php echo($fiche[4]) ?></td>
            <td><?php echo($fiche[5]) ?></td>
            <td><?php echo($fiche[6]) ?></td>
            <td><?php echo($fiche[7]) ?></td>

          </tr>


          <?php
          //echo($fiche[8]);
          $i++;
          $Mois = $fiche[8];
        };
        ?>
      </tbody>
    </table>
    <select name="etat" required id="etat">
      <option disabled="" selected="" value="">etat </option>
      <option value="CL">Saisie clôturée</option>
      <option value="RB">Rembourcée</option>
      <option value="VA">validée et mise en paiement</option>
      <option value="RF">Refusé</option>
    </select>
  </fieldset>
  <br>

  <input type="submit" id="ok" title="valider les fiche de frais" >
</form>
</div>
<?php
//var_dump($_POST['listeAccept']);
if (isset($_POST)){
  //var_dump($_POST);
  $etat = array_pop($_POST);
  //var_dump($etat);
  //var_dump($_POST);
  foreach ($_POST as $info) {
    //print_r($info);
    ?><br><br><?php
    if(modifierEtatFicheFrais($idConnexion, $mois, $info, $etat)){
      ?><p class="info"> les fiches ont bien été modifié</p><?php
    }

  }
}
?>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>
