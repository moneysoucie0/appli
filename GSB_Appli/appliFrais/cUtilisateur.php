<?php
/**
* Page d'ajout d'un nouvelle utilisateur de l'application web AppliFrais
* @package default
* @todo  RAS
*/
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// page inaccessible si visiteur non connecté
$idUser = obtenirIdUserConnecte();
if ( ! estVisiteurConnecte() )
{

  header("Location: cSeConnecter.php");
}
if (obtenirRole($idUser, $idConnexion)["Role"]!=3){
  header("Location: cAccueil.php");
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
?>
<!-- Division principale -->
<div id="contenu">
  <h2> ajouter un nouvelle utilisateur</h2>
  <p>

    <form method="POST" action="">
      <div class="corpsForm">


        <fieldset>
          <legend>créé un utilisateur</legend>
          <p>
            <label for="id">Id :</label>
            <input type="text" name="id" value="" maxlength="4" required>
            <div class="cb"></div>
          </p>
          <p>
            <label for="nom">Nom :</label>
            <input type="text" name="nom" value="" maxlength="30"  required>
            <div class="cb"></div>
          </p>
          <label for="prenom">Prenom :</label>
          <p>
            <input type="text" name="prenom" value="" maxlength="30"required>
            <div class="cb"></div>
          </p>
          <p>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" value="" maxlength="8" required>
            <div class="cb"></div>
          </p>
          <p>
            <label for="passwordVerif">Confirmer le mot de passe  :</label>
            <input type="password" name="passwordVerif" value="" maxlength="8"  required>
            <div class="cb"></div>
          </p>
          <label for="adresse">Adresse :</label>
          <p>
            <input type="text" name="adresse" value="" maxlength="30" >
            <div class="cb"></div>
          </p>
          <p>
            <label for="cp">Code postal :</label>
            <input type="number" name="cp" value="" maxlength="5" >
            <div class="cb"></div>
          </p>
          <p>
            <label for="ville">Ville :</label>
            <input type="text" name="ville" value="" maxlength="30" >
            <div class="cb"></div>
          </p>
          <p>
            <label for="dateEmbauche">Date d'embauche :</label>
            <input type="date" name="dateEmbauche" value=""  >
            <div class="cb"></div>
          </p>
          <p>
            <label for="Role">Role :</label>
            <select name="Role" required >
              <option disabled="" selected="">selectioner le role </option>

              <?php
              $req = "select * from role";
              $res = mysqli_query($idConnexion, $req);
              $res = mysqli_fetch_all($res);
              foreach ($res as $array ) {

                ?>
                <option value=<?php echo ($array[0]); ?>><?php echo($array[1]); ?></option>
                <?php
              };
              ?>
            </select>
          </p>
          <br><br>
        </fieldset>
        <div class="cb"></div>
        <input id="ok" type="submit" value="Valider" size="20" title="Enregistrer l'Utilisateur" />
        <br>
        <br>
        <?php
        if (!empty($_POST)){
          //var_dump($_POST);
          VerificationIdLibre($_POST['id'], $idConnexion);
          if ($_POST['password'] == $_POST['passwordVerif']) {
            /*
            echo($_POST['id']);
            echo( $_POST['nom']);
            echo($_POST['prenom']);
            echo($_POST['password']);
            echo($_POST['adresse']);
            echo($_POST['cp']);
            echo($_POST['ville']);
            echo($_POST['dateEmbauche']);
            echo($_POST['Role']);*/
            if (VerificationIdLibre($_POST['id'],$idConnexion)) {
              ajouterUtilisateur ($_POST['id'], $_POST['nom'],$_POST['prenom'],$_POST['password'],$_POST['adresse'],$_POST['cp'],$_POST['ville'],$_POST['dateEmbauche'],$_POST['Role'],$idConnexion);
              ?><p class="info"> L'utilisateur a bien été créé</p><?php
            }
            else {
              ?><p class="erreur"> L'id est deja utilisé</p><?php
            }
          }
          else {
            ?><p class="erreur"> Les deux mot de passe sont differant</p><?php
          };


        };
        ?>
      </form>
    </div>
  </div>

  <?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
  ?>
