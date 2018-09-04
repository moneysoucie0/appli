<?php
//instance de connection a la base de donné
try {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new 	PDO('mysql:host=localhost;dbname=gsb_valide', 'root', '',$pdo_options);

}
catch (Exception $e)
{
	echo "connexion impossible";
	die('Erreur : ' . $e->getMessage());
}


$req= 'CREATE TABLE role (
      idRole int(1),
      role VARCHAR(32),
		PRIMARY KEY(idRole))';

$bdd->exec($req);
echo ('data base creat');
$req = 'INSERT INTO role VALUES (1, 'Visiteur'),(2, 'Comptable'),(3, 'Administrateur')'
$bdd->exec($req);
echo ('data base remplis');

$bdd->exec('ALTER TABLE visiteur
            ADD CONSTRAINT FK_Role
            FOREIGN KEY (role)
            REFERENCES role(idRole)');

echo ('FK creat');

    ?>
