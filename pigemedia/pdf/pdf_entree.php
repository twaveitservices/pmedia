<?php
require_once('connection.php');
require('html2pdf.class.php');
ob_start();
if($_GET['num_cmd']!=""){
		$num_bc =$_GET['num_cmd'];
		$select=$connection->query("SELECT prenom_j,quantite_j,numero_de_serie_j,nom_article_j,district_j,mag_dest_j,date_j from stock_journal where type_de_mvt_j='Entree' and num_bc_j='".$num_bc."' ");
		$select->setFetchMode(PDO::FETCH_OBJ);
		$date_androany = date("d-m-Y");
		
$tab="";
$tab.="<page>";
					$tab.='<div style="width:100%;">';
			$tab.='<img style="float:left;width:100pt;" id="img_logo" src="logo_usaid.png"  height="50pt">';
			$tab.='<img style="float:right;width:100pt;" id="img_logo" src="logo.png"  height="50pt">';
		$tab.='</div>';
					$tab.='<div style="text-align:center" ><h2>Bon de Reception</h2></div>';
						$tab.='<div class="FloatLeft css_txt" style="width:40%;">Date :&nbsp;'.$date_androany.'</div>';
						$tab.='<div class="FloatLeft css_txt" style="width:40%;">Numero :&nbsp;'.$num_bc.'</div>	';		
						$tab.='<div id="div_clear" style="clear: both;"></div>';
						$tab.='<table style="margin:auto">';
							$tab.='<tr>';
								$tab.='<td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">N&deg;</td>';
								$tab.='<td style="height:20pt;border-bottom:solid 1px #000000;width:50pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">N&deg; serie/N&deg; Lot</td>';
								$tab.='<td style="height:20pt;border-bottom:solid 1px #000000;width:100pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Article </td>';
								$tab.='<td style="height:20pt;border-bottom:solid 1px #000000;width:40pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Qt envoyee</td>';
								$tab.='<td style="height:20pt;border-bottom:solid 1px #000000;width:40pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Qt recue</td>';
								$tab.='<td style=";height:20pt;border-bottom:solid 1px #000000;width:100pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;;text-align:center;font-size:9pt;">Magasin</td>';
								$tab.='<td style="height:20pt;border-right:solid 1px #000000;border-bottom:solid 1px #000000;width:120pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Observation</td>';
							$tab.='</tr>';
							$prenom_mag="";
							$i=1;
							 while( $enregistrement1 = $select->fetch())
							{
							$prenom_mag=$enregistrement1->prenom_j;
								$tab.='<tr style="border:1px">';
										$tab.='<td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$i++.'</td>';
										$tab.='<td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$enregistrement1->numero_de_serie_j.'</td>';
										$tab.='<td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$enregistrement1->nom_article_j.'</td>';
										$tab.='<td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$enregistrement1->quantite_j.'</td>';
										$tab.='<td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">&nbsp;</td>';
										$tab.='<td style="height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;text-align:center;font-size:9pt;">'.$enregistrement1->mag_dest_j.'</td>';
										$tab.='<td style="height:17pt;border-bottom:solid 1px #000000;text-align:center;font-size:9pt;border-left:solid 1px #000000;text-align:center;font-size:9pt;border-right:solid 1px #000000;"></td>';
									$tab.='</tr>';
								}							
						$tab.='</table>';
					// $tab.='</div>';
					$tab.='<div style="text-align:left;margin-top:20pt;">Magasinier :'.$prenom_mag.'</div>';
					
					$tab.='<div style="width:100%;float:left;margin-top:20pt;">';
					$tab.='<hr>';
					    $tab.='<div style="text-decoration:underline">Magasin envoyeur</div>';
						$tab.='<table style="width:250pt;margin-top:5pt;">';
								$tab.='<tr>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:100pt;height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Articles charge par :</td>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:350pt;height:25pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;"></td>';
								$tab.='</tr>';
								$tab.='<tr>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:100pt;height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Voiture N&deg;:</td>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:350pt;height:25pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;"></td>';
								$tab.='</tr>';
								$tab.='<tr>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:100pt;height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Approuve par:</td>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:350pt;height:25pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;"></td>';
								$tab.='</tr>';
						$tab.='</table>';
						
						
						$tab.='<div style="margin-top:5pt;text-decoration:underline">Agent de securite</div>';
						$tab.='<table style="width:250pt;margin-top:5pt;">';
								$tab.='<tr>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:100pt;height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Nom :</td>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:350pt;height:25pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;"></td>';
								$tab.='</tr>';
								$tab.='<tr>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:100pt;height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Signature:</td>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:350pt;height:25pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;"></td>';
								$tab.='</tr>';
						$tab.='</table>';
						
						
						$tab.='<div style="margin-top:5pt;text-decoration:underline">Mag receptionnaire</div>';
						$tab.='<table style="width:250pt;margin-top:5pt;">';
								$tab.='<tr>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:100pt;height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Recu par :</td>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:350pt;height:25pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;"></td>';
								$tab.='</tr>';
								$tab.='<tr>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:100pt;height:17pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Date:</td>';
									$tab.='<td style="border-top:solid 1px #000000;border-right:solid 1px #000000;width:350pt;height:25pt;border-bottom:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;"></td>';
								$tab.='</tr>';
						$tab.='</table>';
					$tab.='</div>';					
$tab.='</page>';					
$content = $tab;

// $content = ob_get_clean();
// $pdf = new HTML2PDF('P','A4','fr');
$pdf = new HTML2PDF('P', 'A4', 'fr', false, 'ISO-8859-1', array(10, 10, 20, 0));
$pdf->pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($content);
$pdf->Output('1.pdf');
}
?>