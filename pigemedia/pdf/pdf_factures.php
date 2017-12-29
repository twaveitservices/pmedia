<?php
//require_once('connection.php');
require('html2pdf.class.php');
$dns='mysql:host=localhost;dbname=synv5';
$utilisateur='twave';
$motdepasse='admin';
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
$connection = new PDO($dns,$utilisateur,$motdepasse,$options);

ob_start();

if($_GET['num_fac']!=""){
	
		$select1=$connection->query("SELECT  now() as dt, `agent`, `num_client`, `ref_article`, `designation`, `qte`, `pu`, `pht`, `total_av_rem`, `remise`, `total_ap_rem`, `ttc`, `dev`, `transport`, `tva`, `rap`, `ca` FROM `commandes` WHERE `factures_cmd`='".$_GET['num_fac']."'");
		$select1->setFetchMode(PDO::FETCH_OBJ);
		
		//donnees
		$date="";
		$agent="";
		$num_client="";
		$tot_ht=0;
		$remise=0;
		$tva=0;
		$transport=0;
		$tot_ap_rem=0;
		$ttc=0;
		$ca=0;
		$rap=0;
		$dev="";
		
		
		
		//tableau affichage
		$info='<div><table><tr><th>Date</th><th>Agent</th><th>Numero</th><th >Client</th></tr>';
		$com='';
		$total="";
		
		while($enregistrementi = $select1->fetch())
	{
		
		$date=$enregistrementi->dt;
		$agent=$enregistrementi->agent;
		$num_client=$enregistrementi->num_client;
		$tot_ht=$enregistrementi->total_av_rem;
		$remise=$enregistrementi->remise;
		$tva=$enregistrementi->tva;
		$transport=$enregistrementi->transport;
		$tot_ap_rem=$enregistrementi->total_ap_rem;
		$ttc=$enregistrementi->ttc;
		$ca=$enregistrementi->ca;
		$rap=$enregistrementi->rap;
		$dev=$enregistrementi->dev;
		
		
		$com.='<tr><td>'.$enregistrementi->ref_article.'</td>
		<td >'.$enregistrementi->designation.'</td>
		<td >'.$enregistrementi->qte.'</td>
		<td >'.number_format(round($enregistrementi->pu,2,1),2 ,  "," , "." ).'</td>
		<td >'.number_format($enregistrementi->pht,2 ,  "," , "." ).'</td></tr>';
		
	}
	
	//$com.='</table>';
	$nf="";
	if($agent!="gustin")
	{
		$nf="IMES/IMS/".$_GET['num_fac'];
	}else{
		$nf="IMES/GCL/".$_GET['num_fac'];
	}
	
	
	$select=$connection->query("SELECT `societe`, `nomcontact`, `adresse`, `ville`, `codep`, `pays`, `tel`, `fax`  FROM `client` WHERE `num`='".$num_client."'");
	$select->setFetchMode(PDO::FETCH_OBJ);
	$societe="";
	$cli="";
	if($enregistrement = $select->fetch())
	{
		$societe=$enregistrement->societe;
		$cli=$enregistrement->nomcontact;
	}
	
	//$info.='<tr><td>'.$date.'</td><td>'.$agent.'</td><td>'.$nf.'</td><td>'.$societe.' '.$cli.'</td></tr></table></div><br/><br/>';
	$info.='<tr><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;";>'.$date.'</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$agent.'</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$nf.'</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$societe.' '.$cli.'</td></tr></table></div><br/><br/>';
	$total.='<table>
			<tr><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Total HT</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$tot_ht.' '.$dev.'</td></tr>
			<tr><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Remise</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$remise.' %</td></tr>
			<tr><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Total apres remise</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$tot_ap_rem.' '.$dev.'</td></tr>
			<tr><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Transport</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$transport.' '.$dev.'</td></tr>
			<tr><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Tva</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$tva.' '.$dev.'</td></tr>
			<tr><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Prix total</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$ttc.' '.$dev.'</td></tr>
			<tr><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Accompte</td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$ca.' '.$dev.'</td></tr>
			<tr><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">Reste </td><td style="height:20pt;border-bottom:solid 1px #000000;width:30pt;border-top:solid 1px #000000;border-left:solid 1px #000000;text-align:center;font-size:9pt;">'.$rap.' '.$dev.'</td></tr>
			</table>';
$entete='<div><img src="logofac.png"></div><br/>';	
$tab="";
$tab.="<page>";
					$tab.=$entete;
					$tab.=$info;
					$tab.=$com;	
					$tab.=$total;
$tab.='</page>';					
//$content = $tab;

// $content = ob_get_clean();
// $pdf = new HTML2PDF('P','A4','fr');
?>
<style type="text/css">
table { 
width: 100%; 
color: #717375; 
font-family: helvetica; 
line-height: 5mm; 
border-collapse: collapse; 
}
h2 { margin: 0; padding: 0; }
p { margin: 5px; }
.border th { 
border: 1px solid #000;  
color: white; 
background: #000; 
padding: 5px; 
font-weight: normal; 
font-size: 14px; 
text-align: center; 
}
.border td { 
border: 1px solid #CFD1D2; 
padding: 5px 10px; 
text-align: center; 
}
.no-border { 
border-right: 1px solid #CFD1D2; 
border-left: none; 
border-top: none; 
border-bottom: none;
}
.space { padding-top: 100px; }
.10p { width: 10%; } .15p { width: 15%; } 
.25p { width: 25%; } .50p { width: 50%; } 
.60p { width: 60%; } .75p { width: 75%; }
.5p	 { width: 5%;}
</style>

<page backtop="5mm" backleft="5mm" backright="5mm" backbottom="5mm" footer="page;">
<table ><tr><td><img src="logofac.png" style="width:100%;" ></td></tr></table>
<page_footer>
<hr />
<p>Siege Ex enceinte FIBATA Analamahitsy, Antananarivo 101 MADAGASCAR, Telephone 22 435 25 Fax 22 430 23</p>
<p>Email; imes@moov.mg; decors-fm@moov.mg siteweb:www.decors-fm.com</p>
<p>Statistique:14200 11 1992 0 00066; RC:2003B00338; NIF:4000059288</p>
</page_footer>
<table  style="margin-top: 30px;" class="border"><tr><th class="10p">Date</th><th class="10p">Agent</th><th class="10p">Numero</th><th class="10p" >Client</th></tr>
<tr><td><?php echo $date; ?></td><td><?php echo $agent; ?></td><td><?php echo $nf; ?></td><td><?php echo $societe.' , '.$cli; ?></td></tr>
</table>
<table style="margin-top: 30px;" class="border">
<thead><tr><th class="15p">Article</th><th class="15p">Description</th><th class="5p">Qté</th><th class="15p">Prix Unitaire</th><th class="15p">Montant</th></tr></thead>
<tbody>
<?php echo $com; ?>
<tr>
<td class="space"></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td colspan="3" class="no-border"></td>
<td style="text-align: center;" ><strong>HT :</strong></td>
<td> <?php echo number_format($tot_ht,2 ,  "," , "." ).' '.$dev; ?> </td>
</tr>
<tr>
<td colspan="3" class="no-border"></td>
<td style="text-align: center;" ><strong>Remise : </strong></td>
<td> <?php echo $remise; ?>  %</td>
</tr>
<tr>
<td colspan="3" class="no-border"></td>
<td style="text-align: center;" ><strong>Apres remise :  </strong></td>
<td> <?php echo number_format($tot_ap_rem,2 ,  "," , "." ).' '.$dev; ?></td>
</tr>
<tr>
<td colspan="3" class="no-border"></td>
<td style="text-align: center;" ><strong>Transport :   </strong></td>
<td> <?php echo number_format($transport,2 ,  "," , "." ).' '.$dev; ?></td>
</tr>
<tr>
<td colspan="3" class="no-border"></td>
<td style="text-align: center;" ><strong>TVA :   </strong></td>
<td> <?php echo number_format($tva,2 ,  "," , "." ).' '.$dev; ?></td>
</tr>
<tr>
<td colspan="3" class="no-border"></td>
<td style="text-align: center;" ><strong>TTC :    </strong></td>
<td> <?php echo number_format($ttc,2 ,  "," , "." ).' '.$dev; ?> </td>
</tr>
<!--tr>
<td colspan="3" class="no-border"></td>
<td style="text-align: center;" ><strong>Accompte :   </strong></td>
<td> <?php echo $ca.' '.$dev; ?></td>
</tr>
<tr>
<td colspan="3" class="no-border"></td>
<td style="text-align: center;" ><strong>Reste : </strong></td>
<td> <?php echo $rap.' '.$dev; ?></td>
</tr-->
</tbody>
</table>
</page>
<?php
 $content = ob_get_clean();
 try {
$pdf = new HTML2PDF('P', 'A4', 'fr');//, false, 'ISO-8859-1', array(10, 10, 20, 0)
$pdf->pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($content);
$pdf->Output('1.pdf');
} catch (HTML2PDF_exception $e) {
die($e);
}

}
?>