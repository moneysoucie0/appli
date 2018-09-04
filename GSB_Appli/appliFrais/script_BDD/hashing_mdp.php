<?php
try {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new 	PDO('mysql:host=localhost;dbname=gsb_valide', 'root', '',$pdo_options);
	
}
catch (Exception $e)
{
	echo "connexion impossible";
	die('Erreur : ' . $e->getMessage()); 
}
// modification de la taille de la colone des mdp 
//exec 
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

?> 

