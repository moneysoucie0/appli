<?php
/**
 * Contient la division pour le sommaire, sujet à des variations suivant la
 * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur
 * @todo  RAS
 */

?>
    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
    <?php
      if (estVisiteurConnecte() ) {
          $idUser = obtenirIdUserConnecte() ;
          $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
          $nom = $lgUser['nom'];
          $prenom = $lgUser['prenom'];
    ?>
        <h2>
    <?php
            echo $nom . " " . $prenom ;
    ?>
        </h2>
        <h3><?php echo(obtenirLibelleRole($idUser, $idConnexion)['role']) ?></h3>
    <?php
       }
    ?>
      </div>
<?php
  if (estVisiteurConnecte() ) {
?>
        <ul id="menuList">
          <li class="smenu">
              <a href="cAccueil.php" title="Page d'accueil">Accueil</a>
           </li>
           <?php
           $role = obtenirRole($idUser, $idConnexion);

           if ($role['Role'] == 1){
             ?>
           <li class="smenu">
              <a href="cSaisieFicheFrais.php" title="Saisie fiche de frais du mois courant">Saisie fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="cConsultFichesFrais.php" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
           </li>
           <li class="smenu">
              <a href="cSaisirVoiture.php" title="Saisie de ma voiture">Saisie du vehicule</a>
            </li>
            <li class="smenu">
               <a href="pdf.php" title="Saisie de ma voiture">generer les pdf </a>
             </li>

            <?php
             };
            if ($role['Role'] == 2){
              ?>
            <li class="smenu">
               <a href="cFicheForfaitiser.php" title="fiche forfaitiser">fiche forfaitiser </a>
            </li>
            <!--
            <li class="smenu">
               <a href="cFicheHorsForfait.php" title="fiche hors forfait">fiche hors forfait</a>
            </li>
            -->
            <li class="smenu">
               <a href="cChangerRemboursement.php" title="changer les remboursement">changer les remboursement</a>
             </li>

         <?php
          };
         if ($role['Role'] == 3){
           ?>
         <li class="smenu">
            <a href="cUtilisateur.php" title="ajouter un utilisateur">ajouter un utilisateur</a>
         </li>
         <li class="smenu">
            <a href="cConsultFichesFrais.php" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
         </li>
         <li class="smenu">
            <a href="cSaisirVoiture.php" title="Saisie de ma voiture">Saisie du vehicule</a>
          </li>


       <?php
        };
       ?>
       <li class="smenu">
          <a href="cModifierInformationUtilisateur.php" title="modifier ses information">changer mes informations</a>
       </li>
         <li class="smenu">
            <a href="cSeDeconnecter.php" title="Se déconnecter">Se déconnecter</a>
         </li>
       </ul>
        <?php
          // affichage des éventuelles erreurs déjà détectées
          if ( nbErreurs($tabErreurs) > 0 ) {
              echo toStringErreurs($tabErreurs) ;
          }
  }
        ?>
    </div>
