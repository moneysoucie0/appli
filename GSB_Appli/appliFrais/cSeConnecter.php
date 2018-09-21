<?php
/**
* Script de contrôle et d'affichage du cas d'utilisation "Se connecter"
* @package default
* @todo  RAS
*/
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// est-on au 1er appel du programme ou non ?
$etape=(count($_POST)!=0)?'validerConnexion' : 'demanderConnexion';

if ($etape=='validerConnexion') { // un client demande à s'authentifier
  // acquisition des données envoyées, ici login et mot de passe
  $login = lireDonneePost("txtLogin");
  $mdp = lireDonneePost("txtMdp");
  $lgUser = verifierInfosConnexion($idConnexion, $login, $mdp) ;
  // si l'id utilisateur a été trouvé, donc informations fournies sous forme de tableau
  if ( is_array($lgUser) ) {
    affecterInfosConnecte($lgUser["id"], $lgUser["login"]);
  }
  else {
    ajouterErreur($tabErreurs, "Pseudo et/ou mot de passe incorrects");
  }
}
if ( $etape == "validerConnexion" && nbErreurs($tabErreurs) == 0 ) {
  if (obtenirVoiture($lgUser["id"],$idConnexion)["vehicule"]==NULL && obtenirRole($idVisiteur, $idConnexion)["role"]==1){
    header("Location:cSaisirVoiture.php");
  }
  else {
    header("Location:cAccueil.php");
  }
}

require($repInclude . "_entete.inc.html");

?>
<!-- Division pour le contenu principal -->
<div id="contenu">
  <h2>Identification utilisateur</h2>
  <?php
  if ( $etape == "validerConnexion" )
  {
    if ( nbErreurs($tabErreurs) > 0 )
    {
      echo toStringErreurs($tabErreurs);
    }
  }
  ?>
  <form id="frmConnexion" action="" method="post">
    <div class="corpsForm">
      <input type="hidden" name="etape" id="etape" value="validerConnexion" />

      <p>
        <label for="txtLogin" accesskey="n">* Login : </label>
        <input type="text" id="txtLogin" name="txtLogin" maxlength="20" size="15" value="" title="Entrez votre login" />
        <div class="cb"></div>
      </p>
      <p>
        <label for="txtMdp" accesskey="m">* Mot de passe : </label>

        <input type="password" id="txtMdp" name="txtMdp" maxlength="8" size="15" value=""  title="Entrez votre mot de passe"/>
        <div class="cb"></div>
      </p>
      <div style="clear:both">

      </div>
    </div>
    <div style="clear:both">

    </div>
    <div class="piedForm">
      <p>
        <input class="bouton" type="submit" id="ok" value="Valider" />
        <div class="cb"></div>
      </p>
    </div>
  </form>
</div>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>
