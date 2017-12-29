<?php

//require_once('connection.php');
require('html2pdf.class.php');
$dns='mysql:host=localhost;dbname=synv5';
$utilisateur='twave';
$motdepasse='admin';
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

$connection = new PDO($dns,$utilisateur,$motdepasse,$options);

ob_start();
//$pdf->ob_start();
if($_GET['num_cmd']!=""){
//$code_html=	$_GET['num_cmd'];

//if(isset($_POST["idAuteur"])){		
//debut
$d=explode(':',$_GET['num_cmd']);
$date_androany = date("d-m-Y");
	
    $select=$connection->query("SELECT  `code`, `designation`, `codemp`, `mp`, `qte` FROM `nomenclature` WHERE `code`=TRIM('".$d[0]."')");
		$select->setFetchMode(PDO::FETCH_OBJ);
	//$tableau="";	
    $etat=1;
    //$nom='<table class="custnom" style="width:100%;"><tr><th>code</th><th>d&eacutesignation</th><th>quantit&eacute</th></tr>';

    $nom='<table style="margin:auto;border:solid 1px #000000">';
							$nom.='<tr>';
								$nom.='<td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Code</td>';
								$nom.='<td style="height:20pt;border-bottom:solid 1px #000000;width:50pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Designation</td>';
								$nom.='<td style="height:20pt;border-bottom:solid 1px #000000;width:100pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Quantite </td>';
								$nom.='</tr>';


    $haut='';
    while( $enregistrement = $select->fetch())
	{
	$dem=floatval($d[1])*floatval($enregistrement->qte);
	
	$nom=$nom.'<tr><td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$enregistrement->codemp.'</td><td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$enregistrement->mp.'</td><td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$dem.'</td></tr>';
    /* $nom=$nom.'<tr style="border:1px">';
								$nom.='<td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$enregistrement->codemp'</td>';
								$nom.='<td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$enregistrement->mp.'</td>';
								$nom.='<td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$dem.'</td>';
								$nom.='</tr>';*/
	}
//fin		
$tab="<page>";
/*$tab.='<div style="width:100%;">';
			$tab.='<img style="float:left;width:100pt;" id="img_logo" src="logo_usaid.png"  height="50pt">';
			$tab.='<img style="float:right;width:100pt;" id="img_logo" src="logo.png"  height="50pt">';
		$tab.='</div>';*/
		$tab.='<div style="text-align:center" ><h2>Ordre de fabrication</h2></div>';
						$tab.='<div class="FloatLeft css_txt" style="width:40%;">Date :&nbsp;'.$date_androany.'</div>';
						$tab.='<div class="FloatLeft css_txt" style="width:40%;">Numero :&nbsp;'.$d[2].'</div>	';
						$tab.='<div class="FloatLeft css_txt" style="width:40%;">Article :&nbsp;'.$d[0].'</div>	';	
						$tab.='<div class="FloatLeft css_txt" style="width:40%;">Quantite :&nbsp;'.$d[1].'</div>	';	
						$tab.='<div id="div_clear" style="clear: both;"></div>';
						$tab.='<div style="text-align:center" ><h3>EMARGEMENT</h3></div>';
						$tab.='<div id="div_clear" style="clear: both;"></div>';
						$tab.='<div style="width:100%;">Reception matieres premieres<a style="color:white;">---------------------------</a>Reception produit fini<a style="color:white;">------------------------------------</a>Livraison</div>';
						$tab.='<div id="div_clear" style="clear: both;"></div>';
						$tab.='<div id="div_clear" style="clear: both;"></div>';
						$tab.='<div id="div_clear" style="clear: both;"></div>';
						$tab.='<div id="div_clear" style="clear: both;"></div>';
						$tab.='<div style="text-align:center" ><h3>Besoin en matieres premieres</h3></div>';
						$tab.='<div id="div_clear" style="clear: both;"></div>';
						$tab.='<div id="div_clear" style="clear: both;"></div>';
$tab.=$nom.'</table>'."</page>";
//$content = ob_clean();			
$content = $tab;


// $pdf = new HTML2PDF('P','A4','fr');



$pdf = new HTML2PDF('P', 'A4', 'fr', false, 'ISO-8859-1', array(10, 10, 20, 0));
//$pdf->Output->ob_clean();
$pdf->pdf->SetDisplayMode('fullpage');

$pdf->WriteHTML($content);



ob_clean();
ob_end_clean();
$pdf->Output('imp.pdf');


}

?>