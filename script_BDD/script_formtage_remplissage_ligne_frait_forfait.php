<?php
//connection a la base de données 

try {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new 	PDO('mysql:host=localhost;dbname=gsb_valide', 'root', '',$pdo_options);
	
}
catch (Exception $e)
{
	echo "connexion impossible";
	die('Erreur : ' . $e->getMessage()); 
}
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
			REFERENCES visiteur(id)')
?> 