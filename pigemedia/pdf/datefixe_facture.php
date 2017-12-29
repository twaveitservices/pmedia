<?php
include("getcircuit.php");
if(isset($_SERVER["HTTP_HOST"])){
	if($_SERVER["HTTP_HOST"] == "moulinette2015.malagasy-tours.fr"){
		$url = "http://moulinette2015.malagasy-tours.fr/";
	}else{
		$url = "http://" . $_SERVER["HTTP_HOST"] . "/moulinette/";
	}
	$wikipediaURL = $url . 'index.php/datefixe/apercufacture/' . $_GET["i"] . '/pdf';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $wikipediaURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Le blog de Samy Dindane (www.dinduks.com)');
	$resultat = curl_exec ($ch);
	curl_close($ch);
	$content = alaivoTexteEntre("<body>","</body>",$resultat);
	$content = '<page backleft="5mm" backright="5mm" backtop="5mm" backbottom="10mm">' . $content . '
				<page_footer><div style="border-top:0.5pt solid #000;width:530pt;margin-top:15pt;margin-left:18pt;"></div><div style="width:590pt;font-size:8pt;line-height:1pt;text-align:center;">
				<p class="kely_gras">VR 54 DN Mahazoarivo - Ambohidraserika - Antananarivo 101 - MADAGASCAR</p>
				 <p class="kely">E-mail : contact@malagasy-tours.com; Tél : (261 20)22 356 07 - Fax : (261 20)22 622 13</p>
				 <p class="kely">Sarl au capital de 10 000 000ar - licence n°027/MINTOUR/ SG/ DG/ DADI</p>
				 <p class="kely">ID STAT : 55 101 11 1993 0 10031 / NIF 40000 58722 / RCS ANTANANARIVO 4942/1993 B 00348</p></div></page_footer></page>';
	// echo $content;
	$content = majSrcImg($content,$url);
	require_once('html2pdf.class.php');
	$html2pdf = new HTML2PDF('P','A4','fr');
	// $html2pdf->setModeDebug();
	$content = $html2pdf->getHtmlFromPage($content);
	$html2pdf->WriteHTML($content);
	$html2pdf->Output('exemple.pdf');
}
?>