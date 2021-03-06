<?php
/**
* Page d'de modification d'un utilisateur de l'application web AppliFrais
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
/* if (obtenirRole($idUser, $idConnexion)["Role"]!=3){
header("Location: cAccueil.php");
} */
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
?>

<div id="contenu">
  <h2> modifier mes information</h2>
  <p>
    <form method="POST" action="">
      <?php
      if (obtenirRole($idUser, $idConnexion)["Role"]==3){
        $etape = 'recherche utilisateur';
        ?>
        <p>
          <label for="utilisateur">Utilisateur :</label>
          <select name="utilisateur" required >
            <option disabled="" selected="" value="">entrer l' utilisateur</option>

            <?php
            $listUser = obtenirTousUtilisateur($idConnexion);
            var_dump($listUser);
            foreach ($listUser as $user ) {
              var_dump($user);

              ?>
              <option value=<?php echo ($user[0]); ?>><?php echo($user[1]);?></option>
              <?php
            };
            ?>
          </select>
        </p>
        <?php
      };

      $utilisateur = obtenirTouteInfoUtilisateur ($idUser,$idConnexion)
      ?>
      <?php
      if (obtenirRole($idUser, $idConnexion)["Role"]==3){
        ?>
        <input id="recherche" type="submit" value="recherche" size="20" title="rechercher l'utilisateur" />
        <?php
      };
      ?>
    </form>
    <form method="POST" action="">
      <?php
      //var_dump($_POST['utilisateur']);
      //var_dump($_POST);
      if (!empty($_POST)){
        if (!isset($_POST['utilisateur'])){
          $_POST['utilisateur'] = $idUser;
        };
        $utilisateur = obtenirTouteInfoUtilisateur ($_POST['utilisateur'],$idConnexion);

      };

      ?>

      <div class="corpsForm">


        <fieldset>
          <legend>Modifier un utilisateur</legend>
          <?php
          if (obtenirRole($idUser, $idConnexion)["Role"]==3){
            ?>
            <p>
              <label for="id">Id :</label>
              <input type="text" name="id" value="<?php echo($utilisateur['id']); ?>" maxlength="4" required readonly>
            </p>
            <div class="cb"></div>
            <?php
          }
          ?>
          <p>
            <label for="nom">Nom :</label>
            <input type="text" name="nom" value="<?php echo($utilisateur['nom']); ?>" maxlength="30"  required>
          </p>
          <div class="cb"></div>

          <p>
            <label for="prenom">Prenom :</label>
            <input type="text" name="prenom" value="<?php echo($utilisateur['prenom']); ?>" maxlength="30"required>
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
          <p>
            <label for="adresse">Adresse :</label>
            <input type="text" name="adresse" value="<?php echo($utilisateur['adresse']); ?>" maxlength="30" >
            <div class="cb"></div>
          </p>
          <p>
            <label for="cp">Code postal :</label>
            <input type="number" name="cp" value="<?php echo($utilisateur['cp']); ?>" maxlength="5" >
            <div class="cb"></div>
          </p>
          <p>
            <label for="ville">Ville :</label>
            <input type="text" name="ville" value="<?php echo($utilisateur['ville']); ?>" maxlength="30" >
            <div class="cb"></div>
          </p>
          <?php
          if (obtenirRole($idUser, $idConnexion)["Role"]==3){
            ?>
            <p>
              <label for="dateEmbauche">Date d'embauche :</label>
              <input type="date" name="dateEmbauche" value="<?php echo($utilisateur['dateEmbauche']); ?>"  readonly>
              <div class="cb"></div>
            </p>
            <?php
          }
          ?>
          <?php
          if (obtenirRole($idUser, $idConnexion)["Role"]==3){
            ?>
            <p>
              <label for="Role">Role :</label>
              <select name="Role" required value='<?php $utilisateur['role'] ?>'>
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
                <div class="cb"></div>
              </select>
            </p>
            <?php
          };
          ?>
          <br><br>
        </fieldset>
      </div>

      <div class="piedForm">



        <div class="cb"></div>
        <input id="ok" type="submit" value="Valider" size="20" title="Enregistrer les modification" />
      </div>
      <br>
      <br>
      <?php
      if (!empty($_POST)){
        if (!isset($_POST['password'])){
          $_POST['password'] = 'init';
        };
        if (!isset($_POST['passwordVerif'])){
          $_POST['passwordVerif'] = 'initi';
        };
        //var_dump($_POST);
        //VerificationIdLibre($_POST['id'], $idConnexion);
        if ($_POST['password'] == $_POST['passwordVerif'] && !empty($_POST['password'])) {

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
          ?>

          <?php
          ModifierUtilisateur ($_POST['id'], $_POST['nom'],$_POST['prenom'],$_POST['password'],$_POST['adresse'],$_POST['cp'],$_POST['ville'],$_POST['Role'],$idConnexion);
          ?><p class="info"> L'utilisateur a bien modifié</p><?php
        }
        else {

          ?><p class="erreur" id="error"> Les deux mot de passe sont differants</p>
          <script type="text/javascript">
          if (!error){
            document.getElementById(error)style.display = "block";
            var error = true;
          }
          </script>
          <?php
        };



        ?>
      </form>
    </div>
    <?php
  }
  ?>
  <?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
  ?>
  <script type="text/javascript">
  document.getElementById("error").style.display = "none";
  </script>
