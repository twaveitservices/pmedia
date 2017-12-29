<?php

require_once('connection.php');


$id= gethostbyaddr($_SERVER["REMOTE_ADDR"]); //SELECT `machine`, `utilisateur` FROM `connection` WHERE `machine`=?
//$requette = $connection->prepare("SELECT `machine`, `utilisateur`, `droit` FROM `connection` WHERE `machine`=?");
//	$td=count($donnees);
//$requette-> execute(array($id));
        $select=$connection->query("SELECT `machine`, `utilisateur`, `droit`, `prenom`  FROM `connection` WHERE `machine`='".$id."'");
		$select->setFetchMode(PDO::FETCH_OBJ);

		$select1=$connection->query("SELECT `machine`, `utilisateur`, `heure`, `prenom`  FROM `connection` WHERE 1");
		$select1->setFetchMode(PDO::FETCH_OBJ);

$utilisateurs='<table><tr ><th>Utilisateurs présents</th><th>dernière connection</th></tr>';		
while($resultat1 = $select1->fetch())
	{
		$utilisateurs=$utilisateurs.'<tr ><td style="background-color:white;">'.$resultat1->prenom.'</td><td style="background-color:white;">'.$resultat1->heure.'</td></tr>';
	}
	
	$utilisateurs=$utilisateurs.'</table>';


if($resultat = $select->fetch())
	{
		$utilisateur=$resultat->prenom;
		$droit=$resultat->droit;
		echo $utilisateur.'|'.$droit.'|'.$utilisateurs;
	}
	else{
		echo "1";

	}

//echo "ok";

?>