<?php

require_once('connection.php');


$id= gethostbyaddr($_SERVER["REMOTE_ADDR"]); 

        $select=$connection->query("DELETE FROM `connection` WHERE `machine`='".$id."'");
		$select->setFetchMode(PDO::FETCH_OBJ);

	echo '0';

?>