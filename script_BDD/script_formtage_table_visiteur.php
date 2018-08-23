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
$bdd->exec('ALTER TABLE visiteur
			ADD vehicule int');
$bdd->exec('ALTER TABLE visiteur
			ADD role int');

echo ('done')
?>
