<?php
/**
* Script de contrôle et d'affichage du cas d'utilisation "Consulter une fiche de frais"
* @package default
* @todo  RAS
*/
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// page inaccessible si visiteur non connecté
if ( ! estVisiteurConnecte() ) {
  header("Location: cSeConnecter.php");
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");

// acquisition des données entrées, ici le numéro de mois et l'étape du traitement
$moisSaisi=lireDonneePost("lstMois", "");
$etape=lireDonneePost("etape","");
$idVisiteur = obtenirIdUserConnecte();
$role = obtenirRole($idVisiteur, $idConnexion)['Role'];
if ($etape != "demanderConsult" && $etape != "validerConsult") {
  // si autre valeur, on considère que c'est le début du traitement
  $etape = "demanderConsult";
}
if ($etape == "validerConsult") { // l'utilisateur valide ses nouvelles données

  // vérification de l'existence de la fiche de frais pour le mois demandé
  $existeFicheFrais = existeFicheFrais($idConnexion, $moisSaisi, obtenirIdUserConnecte());
  // si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
  if ( !$existeFicheFrais ) {
    ajouterErreur($tabErreurs, "Le mois demandé est invalide");
  }
  else {
    // récupération des données sur la fiche de frais demandée
    $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, obtenirIdUserConnecte());
  }
}

?>
<!-- Division principale -->
<div id="contenu">

  <h2>Fiches de frais</h2>
  <div class="corpsForm">
    <form method="POST" action="">
      <?php
      if (obtenirRole($idUser, $idConnexion)["Role"]==2){
        ?>
        <h3>Selectionez l'utilisateur </h3>
        <p>
          <select name="utilisateur" required >
            <option disabled="" selected="" value="">Selectionez l' utilisateur</option>

            <?php
            $listUser = obtenirTousVisiteur($idConnexion);
            var_dump($listUser);
            foreach ($listUser as $user ) {
              var_dump($user);

              ?>
              <option value=<?php echo ($user[0]); ?>><?php echo($user[1].' ' ); echo ($user[2]);?></option>
              <?php
            };
            ?>
          </select>
        </p>
        <?php
      };

      ?>

      <br>
      <h3>Mois à sélectionner : </h3>

      <input type="hidden" name="etape" value="validerConsult" />
      <p>
        <label for="lstMois"> </label>

        <select id="lstMois" name="lstMois" title="Sélectionnez le mois souhaité pour la fiche de frais">
          <option value="" disabled selected>mois</option>
          <?php
          if ($role == 1){
            // on propose tous les mois pour lesquels le visiteur a une fiche de frais
            $req = obtenirReqMoisFicheFrais(obtenirIdUserConnecte());
            $idJeuMois = mysqli_query($idConnexion, $req);
            var_dump($idJeuMois);
            $listMois = mysqli_fetch_all($idJeuMois);
            //echo ('visiteur');
          }
          elseif ($role == 2) {
            $listMois = obtenirTousMoisFicheFrais($idConnexion);
            //var_dump($listMois);
            //echo('comptable');
          };
          //var_dump($lgMois);

          foreach ($listMois as $mois ) {
            $mois = $mois[0];
            $noMois = intval(substr($mois, 4, 2));
            $annee = intval(substr($mois, 0, 4));
            $mois = $mois;
            ?>
            <option value=<?php echo ($mois); ?>><?php echo obtenirLibelleMois($noMois) . " " . $annee; ?></option>
            <?php
          };
          ?>
        </select>
        <div style="clear: both;"></div>
      </p>
    </div>
    <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20"
        title="Demandez à consulter cette fiche de frais" />
      </p>
    </div>

  </form>
  <?php


  // demande et affichage des différents éléments (forfaitisés et non forfaitisés)
  // de la fiche de frais demandée, uniquement si pas d'erreur détecté au contrôle
  if ( $etape == "validerConsult" ) {
    if ( nbErreurs($tabErreurs) > 0 ) {
      echo toStringErreurs($tabErreurs) ;
    }
    else {
      ?>
      <h3>Fiche de frais du mois de <?php echo obtenirLibelleMois(intval(substr($moisSaisi,4,2))) . " " . substr($moisSaisi,0,4); ?> :
        <em><?php echo $tabFicheFrais["libelleEtat"]; ?> </em>
        depuis le <em><?php echo $tabFicheFrais["dateModif"]; ?></em></h3>
        <div class="encadre">
          <p>Montant validé : <?php echo $tabFicheFrais["montantValide"] ;
          ?>
        </p>
        <?php
        // demande de la requête pour obtenir la liste des éléments
        // forfaitisés du visiteur connecté pour le mois demandé
        $moisSaisi = $_POST['lstMois'];
        if ($role == 2 ){
          $idVis = $_POST['utilisateur'];
        }
        elseif ($role == 1 ) {
          $idVis = obtenirIdUserConnecte();
        }
        $req = obtenirReqEltsForfaitFicheFrais($moisSaisi,$idVis, $idConnexion);
        $idJeuEltsFraisForfait = mysqli_query($idConnexion, $req);
        echo mysqli_error($idConnexion);
        $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
        // parcours des frais forfaitisés du visiteur connecté
        // le stockage intermédiaire dans un tableau est nécessaire
        // car chacune des lignes du jeu d'enregistrements doit être doit être
        // affichée au sein d'une colonne du tableau HTML
        $tabEltsFraisForfait = array();
        //var_dump($lgEltForfait);


        //affichage des frait


        foreach ($lgEltForfait as $libelle => $quantite){
          $tabEltsFraisForfait[$libelle] = $quantite;
          $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
        }
        mysqli_free_result($idJeuEltsFraisForfait);
        ?>
        <table class="listeLegere">
          <caption>Quantités des éléments forfaitisés</caption>
          <thead>
            <th>libelle</th>
            <th>quantite</th>
            <th>remboursement</th>
          </thead>
          <tr>
            <?php
            $i = 0;
            $km = calculRemboursementKm($idVis, $moisSaisi, $idConnexion);
            $prix = recupererFraitForfait($idConnexion);
            // var_dump($idVis);
            // var_dump($moisSaisi);
            // premier parcours du tableau des frais forfaitisés du visiteur connecté
            // pour afficher la ligne des libellés des frais forfaitisés
            // var_dump($km);
            foreach ( $tabEltsFraisForfait as $unLibelle => $uneQuantite ) {
              ?>
              <tr>
                <td class="qteForfait"><?php echo $unLibelle ; ?></td>
                <td class="qteForfait"><?php echo $uneQuantite ; ?></td>
                <?php
                if ($unLibelle == 'KM') {
                  $remboursement = calculRemboursementKm($idVis, $moisSaisi, $idConnexion);

                  //var_dump($remboursement);
                }
                else {

                  $remboursement = $prix[$i]*$tabEltsFraisForfait[$unLibelle];
                }
                /*var_dump($prix);

                var_dump($tabEltsFraisForfait[$unLibelle]);
                */
                ?>
                <td class="qteForfait"><?php echo($remboursement); echo " €";?></td>
                <?php
                $i++;
              }
              ?>
            </tr>
            <tr>
              <?php
              // second parcours du tableau des frais forfaitisés du visiteur connecté
              // pour afficher la ligne des quantités des frais forfaitisés
              ?>
              <td colspan="2">Total</td>
              <td><?php
              $km = calculRemboursementKm($idVis, $moisSaisi, $idConnexion);
              $prix = recupererFraitForfait($idConnexion);
              $remboursement = $prix[0]*$tabEltsFraisForfait['ETP']+$km+$prix[2]*$tabEltsFraisForfait['NUI']+$prix[3]*$tabEltsFraisForfait['REP'];
              echo $remboursement;
              echo " €";

              ?></td>
            </tr>
            <tr>

            </tr>
          </table>
          <table class="listeLegere">
            <caption>Descriptif des éléments hors forfait - <?php echo $tabFicheFrais["nbJustificatifs"]; ?> justificatifs reçus -
            </caption>
            <thead>

              <th class="date">Date</th>
              <th class="libelle">Libellé</th>
              <th class="montant">Montant</th>
              <th>Etat</th>

            </thead>
            <tbody>

              <?php
              // demande de la requête pour obtenir la liste des éléments hors
              // forfait du visiteur connecté pour le mois demandé
              $req = obtenirReqEltsHorsForfaitFicheFrais($moisSaisi, $idVis, $idConnexion);
              $idJeuEltsHorsForfait = mysqli_query( $idConnexion, $req);
              $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);

              // parcours des éléments hors forfait
              $i=0;
              while ( is_array($lgEltHorsForfait) ) {
                ?>
                <tr>
                  <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
                  <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
                  <td><?php echo $lgEltHorsForfait["montant"] ; ?></td>
                  <form class="" action="<?php echo ($repInclude . '_FraisHF.php')?>" method="post">
                    <td>
                      <input type="date" name="date" value="<?php echo ( $lgEltHorsForfait["date"]); ?>" hidden>
                      <input type="text" name="idvis" value="<?php echo($idVis); ?>" hidden>
                      <input type="text" name="<?php echo ("montant".$i); ?>" value="<?php echo($lgEltHorsForfait["montant"]); ?>" hidden>
                      <input type="text" name="<?php echo ("id".$i); ?>" value="<?php echo ( $lgEltHorsForfait["id"]); ?>" hidden>
                      <select class="" name="<?php echo ("acceptation".$i); ?>" <?php if ($role == 1) {?> disabled <?php }; ?> >

                        <option value="" disabled <?php if ($lgEltHorsForfait["acceptation"]=='--'){?> selected <?php } ; ?> >etat</option>
                        <option value="Ok" <?php if ($lgEltHorsForfait["acceptation"]=='Ok'){?> selected <?php } ; ?> >Accepter</option>
                        <option value="No" <?php if ($lgEltHorsForfait["acceptation"]=='No'){?> selected <?php } ; ?> >Refuser</option>
                        <option value="At" <?php if ($lgEltHorsForfait["acceptation"]=='At'){?> selected <?php } ; ?> >en attente de justificatif </option>
                        <option value="Iv" <?php if ($lgEltHorsForfait["acceptation"]=='Iv'){?> selected <?php } ; ?> >justifiactif invalide</option>

                      </select>
                    </td>

                  </tr>
                  <?php
                  $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);
                  $i++;
                }
                ?>
              </tbody>

            </table>
            <input type="submit" name="" value="Accepter" title="valider la selection" <?php if ($role == 1) {?> disabled <?php }; ?> >
          </form>
          <?php
          if (isset($_POST)){
            var_dump($_POST);
            $idVis = lireDonneePost('id', $idVis);
            $moisSaisi = lireDonneePost('date', $moisSaisi);
          };


          ?>
        </div>
        <?php
      }
    }
    ?>
    <fieldset>
      <legend>selectioné le mois pour la generation des fiche de frais</legend>

      <form class="" action="pdf.php" method="post">
        <select class="" name="mois">
          <?php
          $allMois = (recupererToutLesMoisVisiteur($idVisiteur, $idConnexion));

          foreach ($allMois as $mois) {
            $mois = ($mois[0]);
            $noMois = intval(substr($mois, 4, 2));
            $annee = intval(substr($mois, 0, 4));
            ?>
            <option value="<?php echo $mois; ?>"><?php echo obtenirLibelleMois($noMois) . " " . $annee; ?></option>
            <?php
          }
          ?>
        </select>
        <input id="ok" type="submit" value="Valider" size="20" title="générer le pdf" />

      </form>
    </fieldset>
  </div>

  <?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
  ?>
