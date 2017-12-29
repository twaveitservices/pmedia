<?php
include("getcircuit.php");
if(isset($_SERVER["HTTP_HOST"])){
	if($_SERVER["HTTP_HOST"] == "moulinette2015.malagasy-tours.fr"){
		$url = "http://moulinette2015.malagasy-tours.fr/";
	}else{
		$url = "http://" . $_SERVER["HTTP_HOST"] . "/moulinette/";
	}
	if(!isset($_GET["j"])) { 	
		$wikipediaURL = $url . 'index.php/alacarte/apercufacture/' . $_GET["i"] ."/". "X" . '/pdf';
	} else {
		$wikipediaURL = $url . 'index.php/alacarte/apercufacture/' . $_GET["i"] ."/". $_GET["j"] . '/pdf';
	}	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $wikipediaURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Le blog de Samy Dindane (www.dinduks.com)');
	$resultat = curl_exec ($ch);
	curl_close($ch);
	$content = alaivoTexteEntre("<body>","</body>",$resultat);
	$content = '<page backleft="5mm" backright="5mm" backtop="5mm" backbottom="1mm">' . $content . '
				<page_footer><div style="border-top:0.5pt solid #000;width:530pt;margin-left:18pt;line-height:1pt;"></div><div style="width:590pt;font-size:8pt;text-align:center;">
				<p class="kely_gras" style="margin-top:-5pt;">VR 54 DN Mahazoarivo - Ambohidraserika - Antananarivo 101 - MADAGASCAR</p>
				 <p class="kely" style="margin-top:-5pt;">E-mail : contact@malagasy-tours.com; Tél : (261 20)22 356 07</p>
				 <p class="kely" style="margin-top:-5pt;">Sarl au capital de 10 000 000 Ar - licence n&deg; 027/MINTOUR/ SG/ DG/ DADI</p>
				 <p class="kely" style="margin-top:-5pt;">ID STAT : 55 101 11 1993 0 10031 / NIF 40000 58722 / RCS ANTANANARIVO 4942/1993 B 00348</p></div></page_footer></page>';
	// echo $content;
	$content = majSrcImg($content,$url);
	require_once('/var/www/html/moulinette/pdf/html2pdf.class.php');
	$html2pdf = new HTML2PDF('P','A4','fr');
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
	$html2pdf->Output('Facture-'.$dossier."-".$daty_androany.'.pdf');
}
?>