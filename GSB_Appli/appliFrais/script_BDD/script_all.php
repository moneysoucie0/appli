<?php
//instance de connection a la base de donné
try {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new 	PDO('mysql: host=localhost; dbname=test', 'root', '',$pdo_options);

}
catch (Exception $e)
{
	echo "connexion impossible";
	die('Erreur : ' . $e->getMessage());
}

//script de hashing de MDP
// modification de la taille de la colone des mdp
//exec
$bdd->exec('SET NAMES utf8');
$bdd->exec("ALTER TABLE visiteur
			MODIFY COLUMN mdp varchar(64)");
$req = 'SELECT id,mdp
		FROM visiteur';
$val = $bdd->query($req);



foreach ($val as $colonne) {
	$ID = $colonne['id'];
	$MDP = ($colonne['mdp']);
	if (mb_strlen($MDP)<=5){
		$MDP = "xZob693r3DiS".$MDP."UOa1edIyMb0q";
//var_dump($MDP);
// pour le cryptage des mpd
//saltav = xZob693r3DiS
//saltap = UOa1edIyMb0q
//$MDPh=hash('sha256',$MDP );
		$MDPh=hash('sha256',$MDP );
		substr($MDPh, 0,64);
//var_dump($MDPh);
		$change = $bdd->prepare('	UPDATE visiteur
									set mdp = :MDPh
									where id = :ID ');
		$change->execute( array(':MDPh'=>$MDPh ,':ID'=>$ID));
	}
}
//var_dump($val);
echo (' MDP hasher');


//scripte de formatage de la BBD




//creation de la table tempon
//mise en place des contraintes de la table

$req = 'CREATE TABLE ligneFraisForfaitBis (
			idVisiteur VARCHAR(4),
			mois VARCHAR(6),
			ETP int (11),
			KM int (11),
			NUI int (11),
			REP int (11),
		PRIMARY KEY(idVisiteur,mois))';

$bdd->exec($req);


//recuperation des donné de la premiere table
$req = 'SELECT idVisiteur, mois, quantite from ligneFraisForfait';
$val = $bdd->query($req);
$i=0;
$etp = 1 ;
$km = 1;
$nui = 1;
$rep = 1;

//remplissage de la table tempon
foreach ($val as $colone) {
	if ($i != 0){
	$etp = $km;
	$km = $nui;
	$nui = $rep;}
	$rep = $colone['quantite'];
	$i++;
	if ($i==4){
		$id = $colone['idVisiteur'];
		$mois = $colone['mois'];

//creation des ligne dans la base de donné avec els donné  recupéré
		$change = $bdd->prepare('	INSERT INTO ligneFraisForfaitBis
									VALUES (:idVisiteur, :mois, :etp, :km, :nui, :rep)');


		$change->execute(array(':idVisiteur'=>$id ,':mois'=>$mois ,':etp'=>$etp ,':km'=>$km ,':nui'=>$nui ,':rep'=>$rep));


		$i = 0;
	}

}
// supression de la table ligneFraitForfait et renomage de la table
// tempon en ligneFraitForfait
$bdd->exec('DROP TABLE ligneFraisForfait' ) ;
$bdd->exec('RENAME TABLE ligneFraisForfaitBis TO ligneFraisForfait');
$bdd->exec('ALTER TABLE lignefraisforfait
			ADD CONSTRAINT FK_idVisiteur
			FOREIGN KEY (idVisiteur)
			REFERENCES visiteur(id)');
			echo (' table ligne frait forfait refaite');


//script de formatage de la base de donné visiteur


$bdd->exec('ALTER TABLE visiteur
			ADD vehicule int');
$bdd->exec('ALTER TABLE visiteur
			ADD role int');

$req = "UPDATE visiteur SET role=1 WHERE role != 3";
$bdd->exec($req);
echo (' table visiteur modifier');

$req = "ALTER TABLE lignefraishorsforfait
				ADD `acceptation` char (2)";
$bdd->exec($req);
echo('table ligne dfrait hors forfait modifier ');
$bdd->exec('ALTER TABLE lignefraishorsforfait ADD COLUMN justificatif char(150)');
echo "ajout des lien des justificatif ";

// script de créationde la table Role
//ajout des valeur nessecaire et des clef etrangére
$req= 'CREATE TABLE role (
			      idRole int(1),
			      role VARCHAR(32),
					PRIMARY KEY(idRole))';

			$bdd->exec($req);
echo ('data base creat');
			$req = "INSERT INTO role VALUES (1, 'Visiteur Médical'),(2, 'Comptable'),(3, 'Administrateur')";
			$bdd->exec($req);
echo (' data base remplis');

			$bdd->exec('ALTER TABLE visiteur
			            ADD CONSTRAINT FK_Role
			            FOREIGN KEY (role)
			            REFERENCES role(idRole)');

			echo ('FK creat');
$req = "INSERT INTO visiteur(id, nom, prenom, login, mdp, adresse, cp, ville, dateEmbauche, vehicule, role)
				VALUES ('A000', 'admin','admin','aadmin','08697536d494d1c3b6b42bf9a96efcb2d79b85f49f47125cf9502245ad2d7aef','','','','',null,3)";
$bdd->exec($req);
echo (' admin créé');

$req = "INSERT INTO etat(id, libelle) VALUES ('RF','Refusé')";
$bdd->exec($req);
echo (' état refusé créé');

echo (' done');

?>
