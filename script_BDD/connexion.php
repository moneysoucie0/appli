<?php
try {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new 	PDO('mysql:host=localhost;dbname=monsite', 'root', '',$pdo_options);
	
}
catch (Exception $e)
{
	echo "connexion impossible";
	die('Erreur : ' . $e->getMessage()); 
}
//code php avec les req 
?> 
