<?php 

$dns='mysql:host=localhost;dbname=synv5';
$utilisateur='root';
$motdepasse='';
//$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

$connection = new PDO($dns,$utilisateur,$motdepasse);

?> 