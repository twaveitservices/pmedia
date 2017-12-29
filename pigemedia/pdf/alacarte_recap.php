<?php
include("getcircuit.php");
if(isset($_SERVER["HTTP_HOST"])){
	if($_SERVER["HTTP_HOST"] == "moulinette2015.malagasy-tours.fr"){
		$url = "http://moulinette2015.malagasy-tours.fr/";
	}else{
		$url = "http://" . $_SERVER["HTTP_HOST"] . "/moulinette/";
	}
	if(!isset($_GET["j"])) { 	
		$wikipediaURL = $url . 'index.php/alacarte/apercuRecap/' . $_GET["i"] ."/". "X" . '/pdf';
	} else {
		$wikipediaURL = $url . 'index.php/alacarte/apercuRecap/' . $_GET["i"] ."/". $_GET["j"] . '/pdf';
	}	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $wikipediaURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Le blog de Samy Dindane (www.dinduks.com)');
	$resultat = curl_exec ($ch);
	curl_close($ch);
	$content = alaivoTexteEntre("<body>","</body>",$resultat);
	$content = '<page backleft="5mm" backright="5mm" backtop="5mm" backbottom="10mm"><page_footer><p style="font-size:11px;font-style:italic; text-align: right;font-weight:bold;">Page [[page_cu]]/[[page_nb]]</p></page_footer>' . $content . '</page>';
	//echo $content;
	$content = majSrcImg($content,$url);
	require_once('/var/www/html/moulinette/pdf/html2pdf.class.php');
	$html2pdf = new HTML2PDF('P','A4','en');
	// $html2pdf->setModeDebug();
	$content = $html2pdf->getHtmlFromPage($content);
	$html2pdf->WriteHTML($content);
	$daty_androany = date('d-m-Y');
	try{
	$bdd = new PDO('mysql:host=localhost;dbname=malagasy_tours;charset=utf8', 'root', '');
	}catch (Exception $e){
        die('Erreur : ' . $e->getMessage());
	}
	$id_programme = $_GET["i"];
	if(!isset($_GET["j"])) {	
		$reponse = $bdd->query("SELECT nom_client  as dossier FROM clients AS c, programmes AS p WHERE c.id_client=p.id_client AND p.id_programme=".$id_programme);
	} else {
		$id_df=$_GET["j"];
		$reponse = $bdd->query("SELECT nom_date_fixe  as dossier FROM date_fixe  where id_programme=".$id_programme." and id_date_fixe=".$id_df);
	}		
	while ($donnees = $reponse->fetch()){	
		$dossier=$donnees['dossier'];
	}
	$reponse->closeCursor();
	$html2pdf->Output('Recap-'.$dossier."-".$daty_androany.'.pdf');
}
?>