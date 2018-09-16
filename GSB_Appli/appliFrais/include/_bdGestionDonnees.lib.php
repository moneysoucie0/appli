<?php
/**
* Regroupe les fonctions d'acc�s aux donn�es.
* @package default
* @author Arthur Martin
* @todo Fonctions retournant plusieurs lignes sont � r��crire.
*/

/**
* Se connecte au serveur de donn�es MySql.
* Se connecte au serveur de donn�es MySql � partir de valeurs
* pr�d�finies de connexion (h�te, compte utilisateur et mot de passe).
* Retourne l'identifiant de connexion si succ�s obtenu, le bool�en false
* si probl�me de connexion.
* @return resource identifiant de connexion
*/
function connecterServeurBD() {
  $hote = "localhost";
  $login = "root";
  $mdp = "";
  $dataBase = "gsb_valide";
  return mysqli_connect($hote, $login, $mdp, $dataBase);
}

/**
* S�lectionne (rend active) la base de donn�es.
* S�lectionne (rend active) la BD pr�d�finie gsb_frais sur la connexion
* identifi�e par $idCnx. Retourne true si succ�s, false sinon.
* @param resource $idCnx identifiant de connexion
* @return boolean succ�s ou �chec de s�lection BD
*/
function activerBD($idCnx) {
  $bd = "gsb_valide";
  $query = "SET CHARACTER SET utf8";
  // Modification du jeu de caract�res de la connexion
  $res = mysqli_query($idCnx, $query);
  $ok = mysqli_select_db($idCnx, $bd);
  return $ok;
}

/**
* Ferme la connexion au serveur de donn�es.
* Ferme la connexion au serveur de donn�es identifi�e par l'identifiant de
* connexion $idCnx.
* @param resource $idCnx identifiant de connexion
* @return void
*/
function deconnecterServeurBD($idCnx) {
  mysqli_close($idCnx);
}

/**
* Echappe les caract�res sp�ciaux d'une cha�ne.
* Envoie la cha�ne $str �chapp�e, c�d avec les caract�res consid�r�s sp�ciaux
* par MySql (tq la quote simple) pr�c�d�s d'un \, ce qui annule leur effet sp�cial
* @param string $str cha�ne � �chapper
* @return string cha�ne �chapp�e
*/
function filtrerChainePourBD($str, $idCnx) {
  if ( ! get_magic_quotes_gpc() ) {
    // si la directive de configuration magic_quotes_gpc est activ�e dans php.ini,
    // toute cha�ne re�ue par get, post ou cookie est d�j� �chapp�e
    // par cons�quent, il ne faut pas �chapper la cha�ne une seconde fois
    $str = mysqli_real_escape_string($idCnx, $str);
  }
  return $str;
}

/**
* Fournit les informations sur un visiteur demand�.
* Retourne les informations du visiteur d'id $unId sous la forme d'un tableau
* associatif dont les cl�s sont les noms des colonnes(id, nom, prenom).
* @param resource $idCnx identifiant de connexion
* @param string $unId id de l'utilisateur
* @return array  tableau associatif du visiteur
*/
function obtenirDetailVisiteur($idCnx, $unId) {
  $id = filtrerChainePourBD($unId, $idCnx);
  $requete = "select id, nom, prenom from visiteur where id='" . $unId . "'";
  $idJeuRes = mysqli_query( $idCnx, $requete);
  $ligne = false;
  if ( $idJeuRes ) {
    $ligne = mysqli_fetch_assoc($idJeuRes);
    mysqli_free_result($idJeuRes);
  }
  return $ligne ;
}

/**
* Fournit les informations d'une fiche de frais.
* Retourne les informations de la fiche de frais du mois de $unMois (MMAAAA)
* sous la forme d'un tableau associatif dont les cl�s sont les noms des colonnes
* (nbJustitificatifs, idEtat, libelleEtat, dateModif, montantValide).
* @param resource $idCnx identifiant de connexion
* @param string $unMois mois demand� (MMAAAA)
* @param string $unIdVisiteur id visiteur
* @return array tableau associatif de la fiche de frais
*/
function obtenirDetailFicheFrais($idCnx, $unMois, $unIdVisiteur) {
  $unMois = filtrerChainePourBD($unMois, $idCnx);
  $ligne = false;
  $requete="select IFNULL(nbJustificatifs,0) as nbJustificatifs, Etat.id as idEtat, libelle as libelleEtat, dateModif, montantValide
  from FicheFrais inner join Etat on idEtat = Etat.id
  where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";
  $idJeuRes = mysqli_query($idCnx, $requete);
  if ( $idJeuRes ) {
    $ligne = mysqli_fetch_assoc($idJeuRes);
  }
  mysqli_free_result($idJeuRes);

  return $ligne ;
}

/**
* V�rifie si une fiche de frais existe ou non.
* Retourne true si la fiche de frais du mois de $unMois (MMAAAA) du visiteur
* $idVisiteur existe, false sinon.
* @param resource $idCnx identifiant de connexion
* @param string $unMois mois demand� (MMAAAA)
* @param string $unIdVisiteur id visiteur
* @return bool �en existence ou non de la fiche de frais
*/
function existeFicheFrais($idCnx, $unMois, $unIdVisiteur) {
  $unMois = filtrerChainePourBD($unMois, $idCnx);
  $requete = "select idVisiteur from FicheFrais where idVisiteur='" . $unIdVisiteur .
  "' and mois='" . $unMois . "'";
  $idJeuRes = mysqli_query($idCnx, $requete);
  $ligne = false ;
  if ( $idJeuRes ) {
    $ligne = mysqli_fetch_assoc($idJeuRes);
    mysqli_free_result($idJeuRes);
  }

  // si $ligne est un tableau, la fiche de frais existe, sinon elle n'exsite pas
  return is_array($ligne) ;
}

/**
* Fournit le mois de la derni�re fiche de frais d'un visiteur.
* Retourne le mois de la derni�re fiche de frais du visiteur d'id $unIdVisiteur.
* @param resource $idCnx identifiant de connexion
* @param string $unIdVisiteur id visiteur
* @return string dernier mois sous la forme AAAAMM
*/
function obtenirDernierMoisSaisi($idCnx, $unIdVisiteur) {
  $requete = "select max(mois) as dernierMois from FicheFrais where idVisiteur='" .
  $unIdVisiteur . "'";
  $idJeuRes = mysqli_query($idCnx, $requete);
  $dernierMois = false ;
  if ( $idJeuRes ) {
    $ligne = mysqli_fetch_assoc($idJeuRes);
    $dernierMois = $ligne["dernierMois"];
    mysqli_free_result($idJeuRes);

  }
  return $dernierMois;
}

/**
* Ajoute une nouvelle fiche de frais et les �l�ments forfaitis�s associ�s,
* Ajoute la fiche de frais du mois de $unMois (MMAAAA) du visiteur
* $idVisiteur, avec les �l�ments forfaitis�s associ�s dont la quantit� initiale
* est affect�e � 0. Cl�t �ventuellement la fiche de frais pr�c�dente du visiteur.
* @param resource $idCnx identifiant de connexion
* @param string $unMois mois demand� (MMAAAA)
* @param string $unIdVisiteur id visiteur
* @return void
*/
function ajouterFicheFrais($idCnx, $unMois, $unIdVisiteur) {
  $unMois = filtrerChainePourBD($unMois, $idCnx);
  // modification de la derni�re fiche de frais du visiteur
  $dernierMois = obtenirDernierMoisSaisi($idCnx, $unIdVisiteur);
  $laDerniereFiche = obtenirDetailFicheFrais($idCnx, $dernierMois, $unIdVisiteur);
  if ( is_array($laDerniereFiche) && $laDerniereFiche['idEtat']=='CR'){
    modifierEtatFicheFrais($idCnx, $dernierMois, $unIdVisiteur, 'CL');
  }

  // ajout de la fiche de frais � l'�tat Cr��
  $requete = "insert into FicheFrais (idVisiteur, mois, nbJustificatifs, montantValide, idEtat, dateModif) values ('"
  . $unIdVisiteur
  . "','" . $unMois . "',0,NULL, 'CR', '" . date("Y-m-d") . "')";
  mysqli_query($idCnx, $requete);

  // ajout des �l�ments forfaitis�s
  $requete = "select id from FraisForfait";
  $idJeuRes = mysqli_query($idCnx, $requete);
  //var_dump($idJeuRes);
  if (existeFicheFrais($idCnx, $unMois, $unIdVisiteur)){
    // var_dump($idJeuRes);
    if ( $idJeuRes ) {
      $ligne = mysqli_fetch_assoc($idJeuRes);
      while ( is_array($ligne) ) {
        $idFraisForfait = $ligne["id"];
        // insertion d'une ligne frais forfait dans la base
        $requete = "insert into LigneFraisForfait (idVisiteur, mois, ETP, KM, NUI, REP)
        values ('" . $unIdVisiteur . "','" . $unMois . "',0,0,0,0)";
        mysqli_query($idCnx, $requete);
        // passage au frais forfait suivant
        $ligne = mysqli_fetch_assoc ($idJeuRes);
      }
      mysqli_free_result($idJeuRes);
    }
  }
}

/**
* Retourne le texte de la requ�te select concernant les mois pour lesquels un
* visiteur a une fiche de frais.
*
* La requ�te de s�lection fournie permettra d'obtenir les mois (AAAAMM) pour
* lesquels le visiteur $unIdVisiteur a une fiche de frais.
* @param string $unIdVisiteur id visiteur
* @return string texte de la requ�te select
*/
function obtenirReqMoisFicheFrais($unIdVisiteur) {
  $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='"
  . $unIdVisiteur . "' order by fichefrais.mois desc ";
  return $req ;
}

/**
* Retourne le texte de la requ�te select concernant les �l�ments forfaitis�s
* d'un visiteur pour un mois donn�s.
*
* La requ�te de s�lection fournie permettra d'obtenir l'id, le libell� et la
* quantit� des �l�ments forfaitis�s de la fiche de frais du visiteur
* d'id $idVisiteur pour le mois $mois
* @param string $unMois mois demand� (MMAAAA)
* @param string $unIdVisiteur id visiteur
* @return string texte de la requ�te select
*/
function obtenirReqEltsForfaitFicheFrais($unMois, $unIdVisiteur, $idCnx) {
  $unMois = filtrerChainePourBD($unMois, $idCnx);
  $requete = "SELECT ETP ,KM ,NUI ,REP
  FROM lignefraisforfait
  WHERE idVisiteur = '" . $unIdVisiteur . "'
  AND mois = " . $unMois . "";
  return $requete;
}

/**
* Retourne le texte de la requ�te select concernant les �l�ments hors forfait
* d'un visiteur pour un mois donn�s.
*
* La requ�te de s�lection fournie permettra d'obtenir l'id, la date, le libell�
* et le montant des �l�ments hors forfait de la fiche de frais du visiteur
* d'id $idVisiteur pour le mois $mois
* @param string $unMois mois demand� (MMAAAA)
* @param string $unIdVisiteur id visiteur
* @return string texte de la requ�te select
*/
function obtenirReqEltsHorsForfaitFicheFrais($unMois, $unIdVisiteur, $idCnx) {
  $unMois = filtrerChainePourBD($unMois, $idCnx);
  $requete = "select id, date, libelle, montant, acceptation from LigneFraisHorsForfait
  where idVisiteur='" . $unIdVisiteur
  . "' and mois='" . $unMois . "'
  and acceptation <> 'No'";
  return $requete;
}

/**
* Supprime une ligne hors forfait.
* Supprime dans la BD la ligne hors forfait d'id $unIdLigneHF
* @param resource $idCnx identifiant de connexion
* @param string $idLigneHF id de la ligne hors forfait
* @return void
*/
function supprimerLigneHF($idCnx, $unIdLigneHF) {
  $requete = "delete from LigneFraisHorsForfait where id = " . $unIdLigneHF;
  mysqli_query($idCnx,  $requete);
}

/**
* Ajoute une nouvelle ligne hors forfait.
* Ins�re dans la BD la ligne hors forfait de libell� $unLibelleHF du montant
* $unMontantHF ayant eu lieu � la date $uneDateHF pour la fiche de frais du mois
* $unMois du visiteur d'id $unIdVisiteur
* @param resource $idCnx identifiant de connexion
* @param string $unMois mois demand� (AAMMMM)
* @param string $unIdVisiteur id du visiteur
* @param string $uneDateHF date du frais hors forfait
* @param string $unLibelleHF libell� du frais hors forfait
* @param double $unMontantHF montant du frais hors forfait
* @return void
*/
function ajouterLigneHF($idCnx, $unMois, $unIdVisiteur, $uneDateHF, $unLibelleHF, $unMontantHF) {
  $unLibelleHF = filtrerChainePourBD($unLibelleHF, $idCnx);
  $uneDateHF = filtrerChainePourBD(convertirDateFrancaisVersAnglais($uneDateHF), $idCnx);
  $unMois = filtrerChainePourBD($unMois, $idCnx);
  $requete = "insert into LigneFraisHorsForfait(idVisiteur, mois, date, libelle, montant)
  values ('" . $unIdVisiteur . "','" . $unMois . "','" . $uneDateHF . "','" . $unLibelleHF . "'," . $unMontantHF .")";
  mysqli_query($idCnx, $requete);
}

/**
* Modifie les quantit�s des �l�ments forfaitis�s d'une fiche de frais.
* Met � jour les �l�ments forfaitis�s contenus
* dans $desEltsForfaits pour le visiteur $unIdVisiteur et
* le mois $unMois dans la table LigneFraisForfait, apr�s avoir filtr�
* (annul� l'effet de certains caract�res consid�r�s comme sp�ciaux par
*  MySql) chaque donn�e
* @param resource $idCnx identifiant de connexion
* @param string $unMois mois demand� (MMAAAA)
* @param string $unIdVisiteur  id visiteur
* @param array $desEltsForfait tableau des quantit�s des �l�ments hors forfait
* avec pour cl�s les identifiants des frais forfaitis�s
* @return void
*/
function modifierEltsForfait($idCnx, $unMois, $unIdVisiteur, $desEltsForfait) {
  $unMois=filtrerChainePourBD($unMois, $idCnx);
  $unIdVisiteur=filtrerChainePourBD($unIdVisiteur, $idCnx);
  //var_dump($desEltsForfait);
  foreach ($desEltsForfait as $idFraisForfait => $quantite) {
    $requete =
    "UPDATE gsb_valide.LigneFraisForfait
    SET ".$idFraisForfait."=".$quantite."
    WHERE lignefraisforfait.idVisiteur = '" .$unIdVisiteur. "'
    AND lignefraisforfait.mois = '".$unMois ."'";
    //var_dump($requete);
    mysqli_query($idCnx, $requete);
  }
}

/**
* Contr�le les informations de connexionn d'un utilisateur.
* V�rifie si les informations de connexion $unLogin, $unMdp sont ou non valides.
* Retourne les informations de l'utilisateur sous forme de tableau associatif
* dont les cl�s sont les noms des colonnes (id, nom, prenom, login, mdp)
* si login et mot de passe existent, le bool�en false sinon.
* @param resource $idCnx identifiant de connexion
* @param string $unLogin login
* @param string $unMdp mot de passe
* @return array tableau associatif ou bool�en false
*/
function verifierInfosConnexion($idCnx, $unLogin, $unMdp) {
  $unLogin = filtrerChainePourBD($unLogin, $idCnx);
  $unMdp = filtrerChainePourBD($unMdp, $idCnx);
  // le mot de passe est crypt� dans la base avec la fonction de hachage md5
  $MDP = "xZob693r3DiS".$unMdp."UOa1edIyMb0q";
  $unMdp=hash('sha256',$MDP );
  substr($unMdp, 0,64);
  $req = "select id, nom, prenom, login, mdp from Visiteur where login='".$unLogin."' and mdp='" . $unMdp . "'";
  $idJeuRes = mysqli_query($idCnx, $req);
  $ligne = false;
  if ( $idJeuRes ) {
    $ligne = mysqli_fetch_assoc($idJeuRes);
    mysqli_free_result($idJeuRes);
  }
  return $ligne;
}

/**
* Modifie l'�tat et la date de modification d'une fiche de frais

* Met � jour l'�tat de la fiche de frais du visiteur $unIdVisiteur pour
* le mois $unMois � la nouvelle valeur $unEtat et passe la date de modif �
* la date d'aujourd'hui
* @param resource $idCnx identifiant de connexion
* @param string $unIdVisiteur
* @param string $unMois mois sous la forme aaaamm
* @return void
*/
function modifierEtatFicheFrais($idCnx, $unMois, $unIdVisiteur, $unEtat) {
  $requete = "UPDATE fichefrais
  SET dateModif=now(),idEtat='".$unEtat."'
  WHERE  idVisiteur ='" .$unIdVisiteur . "'
  AND mois = '". $unMois . "'";
  //echo($requete);
  var_dump( mysqli_query($idCnx, $requete));
}
/**
*Permet l'obtention du nombre de Km

*@param $idVisiteur l'id du visiteur un string
*@param $idCnx un string de l'instance de connexion
*@param $mois un string de la correspondant au mois en cours
*/
function obtenirKM($idVisiteur, $mois, $idCnx){
  $req = "SELECT KM
  FROM lignefraisforfait
  WHERE idvisiteur = '".$idVisiteur."'
  AND mois = '".$mois."'";

  $km = mysqli_query($idCnx,$req);
  return (mysqli_fetch_assoc(mysqli_query($idCnx,$req)));
}
/**
*Permet l'obtention du nombre de cheveaux de la voiture du visiteur

*@param $idVisiteur l'id du visiteur un string
*@param $idCnx un string de l'instance de connexion
*/
function obtenirVoiture($idVisiteur, $idCnx){
  $req = "SELECT vehicule
  FROM  visiteur
  WHERE id = '".$idVisiteur."'";
  return (mysqli_fetch_assoc(mysqli_query($idCnx,$req )));

}
/**
*Modifie le nombre de chevaux du visiteur m�dicale

*@param $idVisiteur l'id du visiteur un string
*@param $idCnx un string de l'instance de connexion
*@param $voiture un int du nombre de chevaux du vehicule
*/
function modifierVehicule($idVisiteur, $idCnx, $voiture){
  $req = "UPDATE visiteur
  SET vehicule = ".$voiture."
  WHERE id = '".$idVisiteur."'";
  mysqli_query($idCnx, $req);
};

/**
*Permet l'obtention de l'id du role du visiteur

*@param $idVisiteur l'id du visiteur un string
*@param $idCnx un string de l'instance de connexion
*/
function obtenirRole($idVisiteur, $idCnx){
  $req = "SELECT Role
  FROM visiteur
  WHERE id = '".$idVisiteur."'";
  return (mysqli_fetch_assoc(mysqli_query($idCnx,$req )));

};
/**
*Permet l'obtention du libelle role du visiteur

*@param $idVisiteur l'id du visiteur un string
*@param $idCnx un string de l'instance de connexion
*/
function obtenirLibelleRole($idVisiteur, $idCnx){
  $role = obtenirRole($idVisiteur, $idCnx);
  $role = ($role['Role']);
  $sql = "SELECT role.role
  from role
  where ".$role." = role.idRole";
  return (mysqli_fetch_assoc(mysqli_query($idCnx,$sql )));

};

/**
*ajoute un utilisateur
*@param $id l'id du nouvelle utilisateur  un string
*@param $prenom le prenon du nouvelle utilisateure un string
*@param $nom le nom du nouvelle utilisateure un string
*@param $mdp le mdp du nouvelle utilisateure un string
*@param $adresse l'adresse du nouvelle utilisateure un string
*@param $cp le cp du nouvelle utilisateure un string
*@param $ville la ville du nouvelle utilisateure un string
*@param $dateEmbauche la date d'emboche du nouvelle utilisateure un string
*@param $role  le role du nouvelle utilisateure un string
*@param $idCnx un string de l'instance de connexion
*/
function ajouterUtilisateur ($id, $nom,$prenom,$mdp,$adresse,$cp,$ville,$dateEmbauche,$role,$idCnx){


  $login = mb_strtolower( $prenom[0].$nom);
  $mdp = "xZob693r3DiS".$mdp."UOa1edIyMb0q";
  $mdp = hash('sha256',$mdp);


  $req = "INSERT INTO visiteur(id, nom, prenom, login, mdp, adresse, cp, ville, dateEmbauche, vehicule, role)
  VALUES ('".$id."', '".$nom."','".$prenom."','".$login."','".$mdp."','".$adresse."','".$cp."','".$ville."','".$dateEmbauche."',null,".$role.")";
  //var_dump ($req);
  mysqli_query($idCnx, $req);
};

/**
*Permet la verification si l'id du visiteur saisie est disponible

*@param $idVisiteur l'id du visiteur un string
*@param $idCnx un string de l'instance de connexion
*/
function VerificationIdLibre($id , $idCnx){
  $req = 'SELECT id FROM visiteur';
  $res = mysqli_query($idCnx, $req);
  $res = (  mysqli_fetch_all($res));
  foreach ($res as $idUser) {
    if ($id == $idUser[0]) {
      return false;
    }
  }
  return true ;
}
/**
*Modifie un utilisateur
*@param $id l'id du nouvelle utilisateur  un string
*@param $prenom le prenon du nouvelle utilisateure un string
*@param $nom le nom du nouvelle utilisateure un string
*@param $mdp le mdp du nouvelle utilisateure un string
*@param $adresse l'adresse du nouvelle utilisateure un string
*@param $cp le cp du nouvelle utilisateure un string
*@param $ville la ville du nouvelle utilisateure un string
*@param $dateEmbauche la date d'emboche du nouvelle utilisateure un string
*@param $role  le role du nouvelle utilisateure un string
*@param $idCnx un string de l'instance de connexion
*/
function ModifierUtilisateur ($id, $nom,$prenom,$mdp,$adresse,$cp,$ville,$role,$idCnx){
  $login = mb_strtolower( $prenom[0].$nom);
  $mdp = "xZob693r3DiS".$mdp."UOa1edIyMb0q";
  $mdp = hash('sha256',$mdp);
  $req = "UPDATE visiteur
  SET nom = '".$nom."',
  prenom = '".$prenom."',
  mdp = '".$mdp."',
  adresse = '".$adresse."',
  cp = '".$cp."',
  ville = '".$ville."',
  role = ".$role."
  WHERE id = '".$id."'";
  //var_dump ($req);
  mysqli_query($idCnx, $req);
};

/**
*Permet l'obtention de toute les information sur un utilisateur

*@param $id l'id du visiteur un string
*@param $idCnx un string de l'instance de connexion
*/
function obtenirTouteInfoUtilisateur ($id,$idCnx){

  $req = "SELECT * FROM `visiteur` WHERE id ='".$id."' ";
  //mysqli_query($idCnx, $req)
  return(mysqli_fetch_assoc(mysqli_query($idCnx, $req)));
};

/**
*Permet l'obtention de tous les utilisateur de l'application

*@param $idCnx un string de l'instance de connexion
*/
function obtenirTousUtilisateur($idCnx){

  $req = "SELECT id ,login  FROM visiteur";
  //mysqli_query($idCnx, $req)
  return(mysqli_fetch_all(mysqli_query($idCnx, $req)));
};

/**
*Permet l'obtention de tous les visiteur  de l'application

*@param $idCnx un string de l'instance de connexion
*/
function obtenirTousVisiteur($idCnx){

  $req = "SELECT id ,nom, prenom  FROM visiteur Where Role = 1";
  //mysqli_query($idCnx, $req)
  return(mysqli_fetch_all(mysqli_query($idCnx, $req)));
};


/**
*Permet l'obtention de tous les utilisateur de l'application

*@param $idCnx un string de l'instance de connexion
*/
function obtenirTousUtilisateurNom($idCnx){

  $req = "SELECT id ,nom, prenom  FROM visiteur";
  //mysqli_query($idCnx, $req)
  return(mysqli_fetch_all(mysqli_query($idCnx, $req)));
};


/**
*Permet l'encryptage d'une chaine de caractére selon la metode utilisé pour les mot de passe

*@param $string une chaine de caractére
*/
function encrypte($string){
  $string = "xZob693r3DiS".$string."UOa1edIyMb0q";
  $string = hash('sha256', $string);
  return ($string);
};
/**
*permet au comptable de changer les montant de rembousement

*@param $etp est un float corespondant au forfait etape
*@param $rep est un float corespondant au frait pour un repas
*@param $nui est un float corespondant au frait pour une nuit
*@param $km est un float corespondant au frait kilometrique
**/
function changerRemboursement($etp, $rep , $nui, $km, $idCnx){
  $req = "UPDATE fraisforfait
  set montant = " .$etp. "
  WHERE id = 'ETP'";
  mysqli_query($idCnx, $req);

  $req = "UPDATE fraisforfait
  set montant = ".$rep."
  WHERE id = 'REP'";
  mysqli_query($idCnx, $req);

  $req = "UPDATE fraisforfait
  set montant = ".$nui."
  WHERE id = 'NUI'";
  mysqli_query($idCnx, $req);

  $req = "UPDATE fraisforfait
  set montant = ". $km ."
  WHERE id = 'KM'";
  mysqli_query($idCnx, $req);
  return true;

};

/**
*Permet l'obtention de tous les utilisateur de l'application

*@param $idCnx un string de l'instance de connexion
*/
function recupererFraitForfait($idcnx){
  $tab = [];
  $req = "SELECT id , montant
  FROM fraisforfait";
  $res = mysqli_fetch_all(mysqli_query($idcnx , $req));
  foreach ($res as $array) {
    array_push( $tab, $array[1]);
  };
  return $tab ;
};

/**
*Permet le calcule du remboursement

*@param $idVisiteur un string de l'id fu visiteur
*@param $mois un string du mois selectionée
*@param $idCnx un string de l'instance de connexion
*/
function calculRemboursementKm($idVisiteur, $mois, $idCnx){
  /*$km = obtenirKM($idVisiteur, $mois, $idCnx);
  var_dump($km);
  var_dump(floatval($km['KM']));
  $chv = obtenirVoiture($idVisiteur, $idCnx);
  var_dump($chv);*/
  $km = floatval(obtenirKM($idVisiteur, $mois, $idCnx)['KM']);
  $chv = intval(obtenirVoiture($idVisiteur, $idCnx)['vehicule']);
  /*var_dump($km);
  var_dump($chv);*/
  if ($km <5000) {
    if ($chv == 3){
      return ($km * 0.41);
    }if ($chv == 4){
      return ($km * 0.493);
    }if ($chv == 5){
      return ($km * 0.543);
    }if ($chv == 6){
      return ($km * 0.568);
    }if ($chv == 7){
      return ($km * 0.595);
    }
  }
  elseif ($km >20000) {
    if ($chv == 3){
      return ($km * 0.286);
    }if ($chv == 4){
      return ($km * 0.332);
    }if ($chv == 5){
      return ($km * 0.364);
    }if ($chv == 6){
      return ($km * 0.382);
    }if ($chv == 7){
      return ($km * 0.401);
    }
  }
  else{
    if ($chv == 3){
      return ($km * 0.245 + 824);
    }if ($chv == 4){
      return ($km * 0.493 + 1082);
    }if ($chv == 5){
      return ($km * 0.543 + 1188);
    }if ($chv == 6){
      return ($km * 0.568 + 1244);
    }if ($chv == 7){
      return ($km * 0.595 + 1288);
    }
  }
};

/**
*Permet l'obtention des fiche de frait

*@param $mois un string du mois selectionée
*@param $idCnx un string de l'instance de connexion
*/
function obtenirFicheFrait($mois, $idCnx){
  $req =  "SELECT 	visiteur.id, visiteur.nom, visiteur.prenom, lignefraisforfait.etp,
  lignefraisforfait.km, lignefraisforfait.nui, lignefraisforfait.rep,
  etat.libelle, fichefrais.mois
  FROM 	    visiteur, lignefraisforfait, fichefrais , etat
  WHERE 	  visiteur.id = fichefrais.idVisiteur
  AND	      visiteur.id = lignefraisforfait.idVisiteur
  and 	    fichefrais.mois = '".$mois."'
  AND 	    lignefraisforfait.mois = fichefrais.mois
  AND 	    fichefrais.idEtat = etat.id
  AND	      etat.id != 'RB'
  AND	      etat.id != 'RF'";
  //echo($req);
  $rep = mysqli_query($idCnx, $req);
  $rep = mysqli_fetch_all(mysqli_query($idCnx, $req));
  return $rep;

};

/**
*Permet l'obtention de tous les mois avec les fiche de frais

*@param $idCnx un string de l'instance de connexion
*/
function obtenirTousMoisFicheFrais($idCnx){
  $req = "SELECT distinct mois FROM fichefrais WHERE 1 ORDER BY mois";
  $rep = mysqli_query($idCnx,$req);
  $rep = mysqli_fetch_all(mysqli_query($idCnx, $req));
  //var_dump($rep);
  return $rep;

};

/**
*Permet l'obtention les type de justificatif

*@param $idCnx un string de l'instance de connexion
*/
function recupererTypeJustificatif($idCnx){
  $req = "SELECT id , libelle FROM fraisforfait";
  $rep = mysqli_query($idCnx,$req);
  $rep = mysqli_fetch_all($rep);
  return $rep;
};


/**
*Permet l'obtention de tous lesmois ou le visiteur a une fiche de frais


*@param $idVisiteur un string de l'id du visiteur
*@param $idCnx un string de l'instance de connexion
*/
function recupererToutLesMoisVisiteur($idVisiteur , $idCnx){
  $req = "SELECT mois
  FROM lignefraisforfait
  WHERE idvisiteur = '".$idVisiteur."'
  ORDER BY mois DESC
  LIMIT 3";
  $res = mysqli_query($idCnx , $req);
  $res = mysqli_fetch_all($res);
  return $res;
};

/**
*Permet le changement de l'atat des fiche hors forfait


*@param $id un string de l'id de la fiche hors forfait
*@param $etat un string de l'etat a mettre de la fiche 
*@param $idCnx un string de l'instance de connexion
*/
function changerEtatHorsForfait($id, $etat, $idCnx){
  $req = "UPDATE lignefraishorsforfait SET acceptation = '".$etat."' WHERE id = '".$id."'";
  mysqli_query($idCnx , $req);

}
?>
