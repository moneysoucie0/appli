<?php
/**
* Script de contrôle et d'affichage du cas d'utilisation "Saisir fiche de frais"
* @package default
* @todo  RAS
*/
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// page inaccessible si visiteur non connecté
if (!estVisiteurConnecte()) {
  header("Location: cSeConnecter.php");
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
// affectation du mois courant pour la saisie des fiches de frais
$mois = sprintf("%04d%02d", date("Y"), date("m"));
// vérification de l'existence de la fiche de frais pour ce mois courant
$existeFicheFrais = existeFicheFrais($idConnexion, $mois, obtenirIdUserConnecte());
// si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
if ( !$existeFicheFrais ) {
  ajouterFicheFrais($idConnexion, $mois, obtenirIdUserConnecte());
}
// acquisition des données entrées
// acquisition de l'étape du traitement
$etape=lireDonnee("etape","demanderSaisie");
// acquisition des quantités des éléments forfaitisés
$tabQteEltsForfait=lireDonneePost("txtEltsForfait", "");
// acquisition des données d'une nouvelle ligne hors forfait
$idLigneHF = lireDonnee("idLigneHF", "");
$dateHF = lireDonnee("txtDateHF", "");
$libelleHF = lireDonnee("txtLibelleHF", "");
$montantHF = lireDonnee("txtMontantHF", "");

// structure de décision sur les différentes étapes du cas d'utilisation
if ($etape == "validerSaisie") {
  // l'utilisateur valide les éléments forfaitisés
  // vérification des quantités des éléments forfaitisés
  $ok = verifierEntiersPositifs($tabQteEltsForfait);
  if (!$ok) {
    ajouterErreur($tabErreurs, "Chaque quantité doit être renseignée et numérique positive.");
  }
  else { // mise à jour des quantités des éléments forfaitisés
    modifierEltsForfait($idConnexion, $mois, obtenirIdUserConnecte(),$tabQteEltsForfait);
  }
}
elseif ($etape == "validerSuppressionLigneHF") {
  supprimerLigneHF($idConnexion, $idLigneHF);
}
elseif ($etape == "validerAjoutLigneHF") {
  verifierLigneFraisHF($dateHF, $libelleHF, $montantHF, $tabErreurs);
  if ( nbErreurs($tabErreurs) == 0 ) {
    // la nouvelle ligne ligne doit être ajoutée dans la base de données
    ajouterLigneHF($idConnexion, $mois, obtenirIdUserConnecte(), $dateHF, $libelleHF, $montantHF);
  }
}
else { // on ne fait rien, étape non prévue

}
?>
<!-- Division principale -->
<div id="contenu">
  <h2>Renseigner ma fiche de frais du mois de <?php echo obtenirLibelleMois(intval(substr($mois,4,2))) . " " . substr($mois,0,4); ?></h2>
  <?php
  if ($etape == "validerSaisie" || $etape == "validerAjoutLigneHF" || $etape == "validerSuppressionLigneHF") {
    if (nbErreurs($tabErreurs) > 0) {
      echo toStringErreurs($tabErreurs);
    }
    else {
      ?>
      <p class="info">Les modifications de la fiche de frais ont bien été enregistrées</p>
      <?php
    }
  }
  ?>
  <form action="" method="post">
    <div class="corpsForm">
      <input type="hidden" name="etape" value="validerSaisie" />
      <fieldset>
        <legend>Eléments forfaitisés
        </legend>
        <?php
        // demande de la requête pour obtenir la liste des éléments
        // forfaitisés du visiteur connecté pour le mois demandé
        $req = obtenirReqEltsForfaitFicheFrais($mois, obtenirIdUserConnecte(), $idConnexion);
        $idJeuEltsFraisForfait = mysqli_query($idConnexion, $req);
        echo mysqli_error($idConnexion);

        // var_dump($idJeuEltsFraisForfait);
        // var_dump($req);

        $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);

        //var_dump($lgEltForfait);

        $dico = array('ETP' => 'Etape',
        'KM'=> 'Kilométre',
        'NUI' => 'Nuité',
        'REP'=> 'Repas');
        foreach ($lgEltForfait  as $key => $value) {

          $quantite =$value;
          $libelle = $dico[$key];
          //$quantite = $lgEltForfait["quantite"];

          ?>

          <label for="<?php echo $libelle ?>">* <?php echo $libelle; ?> :

          </label>
          <input type="text" id="<?php echo $libelle ?>"
          name="txtEltsForfait[<?php echo $libelle ?>]"
          size="10" maxlength="5"
          title="Entrez la quantité de l'élément forfaitisé"
          value="<?php echo $quantite; ?>" />
          <?php

          $lgEltForfait = mysqli_fetch_all($idJeuEltsFraisForfait);
        }
        modifierEltsForfait($idConnexion , $mois, obtenirIdUserConnecte() ,$lgEltForfait);
        mysqli_free_result($idJeuEltsFraisForfait);
        ?>
      </fieldset>
    </div>
    <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20"
        title="Enregistrer les nouvelles valeurs des éléments forfaitisés" />
      </p>
    </div>

  </form>
  <table class="listeLegere">
    <caption>Descriptif des éléments hors forfait
    </caption>
    <tr>
      <th class="date">Date</th>
      <th class="libelle">Libellé</th>
      <th class="montant">Montant</th>
      <th class="action">&nbsp;</th>
    </tr>
    <?php
    // demande de la requête pour obtenir la liste des éléments hors
    // forfait du visiteur connecté pour le mois demandé
    $req = obtenirReqEltsHorsForfaitFicheFrais($mois, obtenirIdUserConnecte(),$idConnexion);
    $idJeuEltsHorsForfait = mysqli_query($idConnexion, $req);
    $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);

    // parcours des frais hors forfait du visiteur connecté
    while ( is_array($lgEltHorsForfait) ) {
      ?>
      <tr>
        <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
        <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
        <td><?php echo $lgEltHorsForfait["montant"] ; ?></td>
        <td><a href="?etape=validerSuppressionLigneHF&amp;idLigneHF=<?php echo $lgEltHorsForfait["id"]; ?>"
          onclick="return confirm('Voulez-vous vraiment supprimer cette ligne de frais hors forfait ?');"
          title="Supprimer la ligne de frais hors forfait">Supprimer</a></td>
        </tr>
        <?php
        $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);
      }
      mysqli_free_result($idJeuEltsHorsForfait);
      ?>
    </table>
    <form action="include\_uploadFichier.php" method="post" enctype="multipart/form-data">
      <div class="corpsForm">
        <input type="hidden" name="etape" value="validerAjoutLigneHF" />
        <input type="hidden" name="typeJustificatif" value="HF" />

        <fieldset>
          <legend>Nouvel élément hors forfait
          </legend>
          <p>
            <label for="txtDateHF">* Date : </label>
            <input type="date" id="txtDateHF" name="txtDateHF" size="12" maxlength="10"
            title="Entrez la date d'engagement des frais au format JJ/MM/AAAA"
            value="<?php echo $dateHF; ?>" />
          </p>
          <div class="cb"></div>
          <p>
            <label for="txtLibelleHF">* Libellé : </label>
            <input type="text" id="txtLibelleHF" name="txtLibelleHF"  maxlength="100"
            title="Entrez un bref descriptif des frais"
            value="<?php echo filtrerChainePourNavig($libelleHF); ?>" />
          </p>
          <div class="cb"></div>
          <p>
            <label for="txtMontantHF">* Montant : </label>
            <input type="text" id="txtMontantHF" name="txtMontantHF" size="12" maxlength="10"
            title="Entrez le montant des frais (le point est le séparateur décimal)" value="<?php echo $montantHF; ?>" />
            <div class="cb"></div>
          </p>
          <p>
            <label for="fichier">* Fichier : </label>
            <input type="file" id="fichier" name="fichier" accept=".jpg, .bmp, .png , .jpeg"
            title="Sélectionnez une pièce justificative à faire valoir pour vos depenses hors forfait" value="null" required/>
            <div class="cb"></div>
          </p>

        </fieldset>
      </div>
      <div class="piedForm">
        <p>
          <input id="ajouter" type="submit" value="Ajouter" size="20"
          title="Ajouter la nouvelle ligne hors forfait" />
          <div class="cb"></div>
        </p>
      </div>
    </form>
  </div>
  <?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
  ?>
