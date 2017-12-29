<?php
	require_once('connection.php');
	//echo "okdoki";
	if( isset($_POST['login']) && isset($_POST['password']) )
	{
	 	$log= $_POST['login'];
	 	$pass =$_POST['password'];
	 	$id= gethostbyaddr($_SERVER["REMOTE_ADDR"]);
	 	$select=$connection->query("SELECT * FROM users  WHERE log='".$log."' and pwd='".$pass."'");
		$select->setFetchMode(PDO::FETCH_OBJ);
		
		if( $enregistrement = $select->fetch())
		{
			echo $enregistrement->perm1,":",$enregistrement->prenom,":",$enregistrement->nom;
			$select1=$connection->query("INSERT INTO `connection`(`machine`, `utilisateur`, `heure`, `droit`,`prenom`) VALUES ('".$id."','".$log."',NOW(),'".$enregistrement->perm1."','".$enregistrement->prenom."')");
			$select1->setFetchMode(PDO::FETCH_OBJ);
		} 
		else
		{
			echo "0";
		}
    }
	
?>
