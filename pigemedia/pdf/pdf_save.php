<?php
require_once('html2pdf.class.php');
include('../configuration.php');
include("../modules/mod_date/date.class.php");
$conf = new JConfig();
set_time_limit(0);
try{
	$connexion = new PDO('mysql:host='.$conf->host.';port=3306;dbname='.$conf->db, $conf->user, $conf->password);
	$article = getArticle($_POST["id"],$connexion);
	$id = $_POST["id"];
	$adresse = $_POST["adresse"];
	$contact = getContact($_POST["id"],$connexion);
	$contenu = $article->introtext;
	$titre = $article->title;
	$titre = clean($titre," ");
	$duree = $article->duree;
	$txt_accroche = $article->txt_accroche;
	$code_circuit = $article->code_circuit;
	$contenu = supprimerTexteEntre('<div class="avanana">', '</div>', $contenu);
	$titre = alaivoAnatinyBalise($contenu,"h1");
	$titre = str_replace('{loadposition module_duree}','    ' . $duree,$titre);
	$titre = strip_tags($titre);
	$contenu = supprimerTexteEntre('<h1>', '</h1>', $contenu);
	$page = '<style type="text/css"><!--a{color:#9f4000}--></style>';
	$page .= '<page style="font-size: 11px;" backimg="../templates/mt_template/images/fond2.jpg" backimgx="left" backimgy="top" backimgw="100%" backleft="7mm" backright="7mm" backtop="10mm" backbottom="15mm">';
	$androany = utf8_encode($article->title) . "  -  Mis &agrave; jour le : " . date("d/m/Y");
	$page .= <<<EOT
<page_header>
<table style="width:100%">
<tr>
<td style="width: 100%;text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">$androany</span>
</td>
</tr>
</table>
</page_header>
<page_footer>
<table style="width:100%;background-color:#d0c4ae">
<tr>
<td align="justify" style="width: 90%;font-size:10px;">
<strong>Malagasy Tours</strong> Sarl - Tel : 00 261 20 22 356 07 - <a href="http://www.malagasy-tours.com">www.malagasy-tours.com</a><br>
Lot VR 54 DN Mahazoarivo - Ambohidraserika - 101 Antananarivo - Madagascar  |  Licences n° A027 et B017<br>
Id.Stat. 55.101.11 1993.0.10031 / NIF 40000 58722 RC N° RCS ANTANANARIVO 4942 / 1993 B 00348<br>
</td>
<td style="width: 10%; text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">Page [[page_cu]]/[[page_nb]]</span>
</td>
</tr>
</table>
</page_footer>
EOT;
	/*$page .= '<table style="text-align: center; border: solid 2px red; background: #FFEEEE;width: 100%" valign="top">';
	$page .= '<tr><td><img src="../templates/mt_template/images/logo1.png" width="300"/><br>Depuis plus de 15 ans sur les chemins de Madagascar</td>';
	$page .= '<td style="text-align:center;">Votre voyage à Madagascar</td></tr>';
	$page .= '</table>';*/
	$page .= '<table style="text-align: center;width: 100%;" align="center">'
		.'<tr><td style="width: auto;"><img src="../templates/mt_template/images/logo1.png" width="360"/><br><span style="font-size:11px;">Depuis plus de 15 ans sur les chemins de Madagascar</span></td>'
		.'<td style="text-align:center;width:51%;"><span style="font-size:28px;">Votre voyage &agrave; Madagascar</span><br><br><br><span style="font-size:20px;">'
		. '<a href="'.$adresse.'" style="color:black;text-decoration:none;">' . strtoupper(enleve($article->title)).'</a></span></td></tr></table><br>';
	$page .= '<div style="text-align:right;font-size:9pt"><a href="'.$adresse.'" style="color:black;text-decoration:none;"><img src="../images/mt/fleche_retour.png" width="35" /></a></div>';
	$tmp = alaivoTexteEntreMiaraka('<img', '/>', $contenu);
	$saryCarte = alaivoSary($tmp);
	$contenu = str_replace($tmp,'',$contenu);
	$txt_contact = '<span style="text-align:right;"><table style="text-align: center;width: 100%;margin-left:4mm;" valign="top">'
		. '<tr><td style="width:20%"><img src="../images/mt/fille/'.$contact->image.'" style="width:50px"></td>'
		. '<td style="text-align:justify;width:75%;"><strong>Votre contact : </strong>'.utf8_encode($contact->prenom).'<br><br>'
		. utf8_encode($contact->texte_intro).'<br><br><strong>Tel : </strong>'
		. $contact->contact_eventuel. ($contact->horaire_ouv_bur == "" ? '' :' ('.replacea(utf8_encode($contact->horaire_ouv_bur)).')').'<br>'.($contact->skype == "" ? "" : "<strong>Skype : </strong>" . $contact->skype . "")
		. '<br><strong>Email : </strong><a href="mailto:malagasytours@blueline.mg">malagasytours@blueline.mg</a></td></tr></table><br><br><img src="../' . $saryCarte . '" style="width:200px"></span>';
	$contenu = str_replace('{loadposition module_contact_mt}','',$contenu);
	$contenu = str_replace('{loadposition module_vtxtaccroche}','',$contenu);
	$texteDesc = alaivoTexteEntre('<p rel="texte_desc">','</p>',$contenu);
	$contenu = str_replace('<p rel="texte_desc">' . $texteDesc . "</p>", "", $contenu);
        $tmp = alaivoTexteEntreMiaraka("<h2", ">", $contenu);
	$textePF = alaivoTexteEntre($tmp,'</h2>',$contenu);
	$contenu = str_replace($tmp . $textePF . "</h2>", "", $contenu);
	$texteLPF = alaivoTexteEntreMiaraka('<ul>','</ul>',$contenu);
	$contenu = str_replace($texteLPF,'',$contenu);
        $tmp = alaivoTexteEntreMiaraka("<h2", ">", $contenu);
	$texteCIV = alaivoTexteEntre($tmp,'</h2>',$contenu);
	$contenu = str_replace($tmp . $texteCIV . "</h2>", "", $contenu);
	$texteLCIV = alaivoTexteEntreMiaraka('<ul>','</ul>',$contenu);
	$texteTmp= $texteLCIV;
	$texteLCIV = str_replace('{loadposition module_duree}',$duree,$texteLCIV);
	$contenu = str_replace($texteTmp,'',$contenu);
	$level = getLevel($_POST["id"],$connexion);
	$texteTmp = '<table style="width:100%"><tr><td style="width:80%">'.utf8_encode($texteLCIV).'</td>'
		.'<td style="width:10%;text-align: right; "><img src="../modules/mod_level/img/difficulte/'.$level->imagediff.'" style="width:40px"></td>'
		.'<td style="width:10%;text-align: right; "><img src="../modules/mod_level/img/level/'.$level->imagelev.'" style="width:40px;"></td>'
		.'</tr></table>';
	$contenu = str_replace('{loadposition mod_level}','',$contenu);
	$contenu = str_replace('{loadposition mod_circuitluxe}','',$contenu);
	$desc = '<table style="width:100%">'
		.'<tr><td style="width:100%;text-align:center;font-style:italic;font-size:14px;">'.utf8_encode($txt_accroche).'<br><br></td></tr>'
		.'<tr><td style="width:100%;text-align:justify;line-height:15px;">'.utf8_encode($texteDesc).'<br><br><br></td></tr>'
		.'<tr><td style="width:100%;text-align:justify;font-weight:bold;line-height:15px;"><span style="font-size:12px;">'.utf8_encode($textePF).'</span></td></tr>'
		.'<tr><td style="width:100%;text-align:justify;line-height:15px;">'.utf8_encode($texteLPF).'</td></tr>'
		.'<tr><td style="width:100%;text-align:justify;font-weight:bold;line-height:15px;"><span style="font-size:12px;">'.utf8_encode($texteCIV).'</span></td></tr>'
		.'<tr><td style="width:100%;text-align:justify;line-height:15px;">'.$texteTmp.'</td></tr>'
		. '</table>';
	$page .= '<table style="text-align: left;width: 100%;border-left:1px solid #000;padding-left:5px;" valign="top">';
	$page .= '<tr><td style="width:65%;">'.$desc.'</td>'
		.'<td style="width:35%;">'.$txt_contact.'</td></tr>';
	$page .= '</table><br><br><br>';
	$textePAVI = alaivoTexteEntre('<h2>','</h2>',$contenu);
	$page .= '<span style="font-weight:bold;font-size:14px">'.replacea(utf8_encode($textePAVI)).'</span>';
	$contenu = str_replace('<h2>' . $textePAVI . "</h2>", "", $contenu);
	$page .= '<table style="width:100%">';
	for($i = 0 ; $i < 10 ; $i++){
		$tmp = alaivoTexteEntreMiaraka('<img', '/>', $contenu);
		$saryDiapo = alaivoSary($tmp);
		if($i%5 == 0 || $i == 0){
			$page.="<tr>";
		}
		$page.='<td><img src="../'.$saryDiapo.'" style="width:135px" /></td>';
		if(($i + 1)%5 == 0 && $i > 0){
			$page .= '</tr>';
		}
		$contenu = str_replace($tmp,'',$contenu);
	}
	$page .= '</table>';
	$page .= '</page>';
	$page .= '<page style="font-size: 11px;" backcolor="#FFFFCC" backleft="7mm" backright="7mm" backtop="10mm" backbottom="15mm">';
$page .= <<<EOT
<page_header>
<table style="width:100%">
<tr>
<td style="width: 100%;text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">$androany</span>
</td>
</tr>
</table>
</page_header>
<page_footer>
<table style="width:100%;background-color:#d0c4ae">
<tr>
<td style="width: 90%; text-align: left">
<span style="font-size:10px;font-style:italic; text-align: left">Malagasy Tours</span>
</td>
<td style="width: 10%; text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">Page [[page_cu]]/[[page_nb]]</span>
</td>
</tr>
</table>
</page_footer>
EOT;
	//$page .= '<table style="text-align: left;width: 100%;border-left:1px solid #000;padding-left:5px;">';
	$tab = alaivoTexteEntre('{tab=', '}', $contenu)	;
	$contenu = str_replace('{tab=' . $tab . '}','',$contenu);
	//$page .= '<tr><td><span style="font-weight:bold;font-size:14px">'.utf8_encode($tab).'</span><br></td></tr>';
	$page .= '<span style="font-weight:bold;font-size:18px">'.utf8_encode($tab).'</span><br>';
	$em = alaivoTexteEntre('<em>','</em>',$contenu);
	$contenu = str_replace('<p><em>'.$em.'</em></p>','',$contenu);
	//$page .= '<tr><td><span style="font-style:italic;font-size:12px;text-align:justify;">'.utf8_encode($em).'</span><br><br><br></td></tr>';
	$page .= '<span style="font-style:italic;font-size:12px;text-align:justify;">'.utf8_encode($em).'</span><br><br><br>';
	$row = getJourJour($id,$connexion);
	$page .= '<table style="text-align: left;width: 100%;border-left:1px solid #000;padding-left:5px;">';
	for ($i = 0; $i < count($row); $i++) {
	//for ($i = 0; $i < 11; $i++) {
		$jour = "J ";
		$diff = 0;
		if((isset($row[$i +1]->numero_jour))){
			if ((($row[$i]->numero_jour + 1) == $row[$i + 1]->numero_jour)) {
				$jour .= $row[$i]->numero_jour;
			} else {
				if(isset($row[$i +1]->numero_jour)){
					$diff = ($row[$i + 1]->numero_jour - 1) - $row[$i]->numero_jour;
					if($diff > 1){
						$txt_diff = ' à ';
					}else{
						$txt_diff = ' et ';
					}
					$jour .= $row[$i]->numero_jour . utf8_decode($txt_diff) . ($row[$i + 1]->numero_jour - 1);
				}else{
					$jour .= $row[$i]->numero_jour;
				}
			}
		}else{
			$jour .= $row[$i]->numero_jour;
		}
		$ret =  $jour . ' ' . $row[$i]->titre;
//		//$page .= '<tr><td>';
//		//$page .= '<table style="width:100%;vertical-align:top;">';
		$page .= '<tr><td style="width:100%;">';
		$page .= '<table style="width:100%">';
		$page .='<tr><td><span style="font-weight:bold;font-size:12px;text-align:left;">' . utf8_encode($ret) . '</span></td></tr>';
		$page .= '<tr><td>';
                $page .= '<table style="width:100%">';
		$page .= '<tr><td style="width:20%;vertical-align:top;"><img src="../images/'.$row[$i]->sary.'" style="width:130px"></td><td style="width:80%;" valign="top"><span style="text-align:justify;">'.trim(utf8_encode(supprimerTousPAttr($row[$i]->contenu,true))).'</span></td></tr>';
		$page .= '</table><br>';
                $page .= '</td></tr>';
		$page .= '</table></td></tr>';
		if($row[$i]->autre_info != ""){
			$page .= '<tr><td style="width:100%;"><span style="font-weight:bold;text-align:left;">Informations spécifiques :</span></td></tr>';
			$ai = supprimerTousBalise($row[$i]->autre_info, false, 'p');
			$ai = supprimerTousBalise($ai, false, 'ul');
			$ai = supprimerTousBalise($ai, true, 'li',true);
			$page .= '<tr><td style="width:100%;"><span style="text-align:justify;">' . replacea(utf8_encode(trim($ai))) . '</span><br></td></tr>';
		}
		if(!is_null($row[$i]->id_aerien)){
			$row_aerien = getAerien($row[$i]->id_aerien,$connexion);
			if($row[$i]->autre_info == ""){
				$page .= '<tr><td style="width:100%;"><span style="font-weight:bold;text-align:left;">Informations spécifiques :</span></td></tr>';
			}
			$txt = "Le vol intérieur à prévoir est le suivant : <span style=\"color:#0000ff;\"><strong>". utf8_encode($row_aerien->ville_depart)." [" . $row_aerien->iata_depart . "] > " . utf8_encode($row_aerien->ville_arrivee) . " [" . $row_aerien->iata_arrivee . "]</strong></span>";
			$txt .= '<br /><span style="font-size:9px;font-style:italic;">NB : entre crochets [ ] se trouve le code international de l\'aéroport.</span>';
			$page .= '<tr><td style="width:100%;"><span style="text-align:justify;">' . replacea(trim($txt)) . '</span><br></td></tr>';
		}
		if($row[$i]->info_botanique != ""){
			$page .= '<tr><td style="width:100%;"><span style="font-weight:bold;text-align:left;">Informations botaniques :</span></td></tr>';
			$ai = supprimerTousBalise($row[$i]->info_botanique, false, 'p');
			$ai = supprimerTousBalise($ai, false, 'ul');
			$ai = supprimerTousBalise($ai, true, 'li',true);
			$page .= '<tr><td style="width:100%;"><span style="text-align:justify;">' . replacea(utf8_encode(trim($ai))) . '</span><br></td></tr>';
		}
		if (($row[$i]->lunch == 1 || $row[$i]->diner == 1)){
			$repas = '<strong>Repas : </strong>' . ($row[$i]->lunch == 1 ? "Déjeuner".($diff > 1 ? "s" : "")." inclus" : "") . (($row[$i]->lunch == 1 && $row[$i]->diner == 1) ? " / " : "") . ($row[$i]->diner == 1 ? "Dîner".($diff > 1 ? "s" : "")." inclus" : "") . '<br>';
			$page .= '<tr><td style="width:100%;"><span style="text-align:justify;">' . replacea(trim($repas)) . '</span><br></td></tr>';
		}
		if ($row[$i]->distance != "" || $row[$i]->duree != ""){
			$duree = ($row[$i]->duree != "" ? '<strong>Durée : </strong>' . (!is_null($row[$i]->id_aerien) ? utf8_encode($row_aerien->duree) . " de vol " : "") . utf8_encode($row[$i]->duree) . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
				: (!is_null($row[$i]->id_aerien) ? '<strong>Durée : </strong>' . utf8_encode($row_aerien->duree) . " de vol  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : ""))
				. ($row[$i]->distance != "" ? '<strong>Distance : </strong>' . utf8_encode($row[$i]->distance) : "") . '<br>';
			$page .= '<tr><td style="width:100%;"><span style="text-align:justify;">' . replacea(trim($duree)) . '</span><br></td></tr>';

		}
		if($row[$i]->hotelnom != "" && utf8_encode($row[$i]->hotelnom) != "Identique au jour précédent" ){
			$page .= '<tr><td><table style="width:100%">';
			$page .= '<tr>';
			$hotel = '<span style="font-size:12px;"><strong>Hébergement indicatif : </strong></span>' . utf8_encode($row[$i]->hotellieu) . ". " . utf8_encode($row[$i]->hotelnom) . '<br><br>'
				. trim(utf8_encode(supprimerTousP($row[$i]->hoteldescriptif,true)));
			$page .= '<td style="width:80%;vertical-align:top;"><span style="text-align:justify;">'.replacea($hotel).'</span></td>'
				. '<td style="width:20%;vertical-align:top;"><img src="../images/'.$row[$i]->hotelsary1.'" style="width:130px"></td>'
				. '</tr>';
			$page .= '</table></td></tr>';
		} elseif( utf8_encode($row[$i]->hotelnom) == "Identique au jour précédent"){
			$page .= '<tr><td style="width:100%;"><span style="text-align:justify;font-size:12px;"><strong>Hébergement indicatif : </strong>' . utf8_encode($row[$i]->hotelnom) . '</span></td></tr>';
		}
		//$page .= '</table>';
		$page .= '<tr><td style="width:100%;"><br><hr style="border:1px solid #ccc;"><br></td></tr>';
		//$page .='</td></tr>';
	}
	$contenu = str_replace('{loadposition module_jourjour}','',$contenu);
	$rmq = alaivoTexteEntre('<p rel="rmq">', '</p>', $contenu);
	$contenu = str_replace('<p rel="rmq">'. $rmq .'</p>','',$contenu);
	$page .= '<tr><td><span style="font-size:12px">'.utf8_encode($rmq).'</span><br></td></tr>';
	$page .= '</table>';
	$page .= '</page>';
	$page .= '<page style="font-size: 11px;" backcolor="#FFFFCC" backleft="7mm" backright="7mm" backtop="10mm" backbottom="15mm">';
	$page .= <<<EOT
<page_header>
<table style="width:100%">
<tr>
<td style="width: 100%;text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">$androany</span>
</td>
</tr>
</table>
</page_header>
<page_footer>
<table style="width:100%;background-color:#d0c4ae">
<tr>
<td style="width: 90%; text-align: left">
<span style="font-size:10px;font-style:italic; text-align: left">Malagasy Tours</span>
</td>
<td style="width: 10%; text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">Page [[page_cu]]/[[page_nb]]</span>
</td>
</tr>
</table>
</page_footer>
EOT;
	$tab = alaivoTexteEntre('{tab=', '}', $contenu)	;
	$contenu = str_replace('{tab=' . $tab . '}','',$contenu);
	$page .= '<span style="font-weight:bold;font-size:18px">'.utf8_encode($tab).'</span><br><br>';
	$date_det = getDateDetermine($id,$connexion);
	$page .= '<table style="text-align: left;width: 100%;border-left:1px solid #000;padding-left:5px;">';
	if(isset($date_det) && $date_det != ""){
		$txt_date_det = getTexteDateDetermine($id,$connexion);
		if(isset($txt_date_det) && $txt_date_det != ""){
			$page_dd = '<table style="width:100%">';
			$page_dd .= '<tr><td style="width:100%;border:2px solid #f09800;padding:5px;"><span style="font-weight:bold;font-size:12px;text-align:left;">' . utf8_encode($txt_date_det->titre) . '</span></td></tr>';
			$txt = $txt_date_det->contenu;
			$txt = supprimerTousPAttr($txt,true);
			$txt = str_replace("{//mt_nb_pers_date_determinees}", $date_det[0]->nbre_pers_min_fixes, trim($txt));
			$liste_date = "<br>";
			$liste_date .= "<table style=\"width:100%\">";
			for ($i = 0; $i < count($date_det); $i++) {
				$daty0 = new dateOp($date_det[$i]->date_deb, "aaaa-mm-jj");
				$daty_voalohany = $daty0->GetDate();
				$daty0->AjouteJours($date_det[$i]->duree - 1);
				$daty_farany = $daty0->GetDate();
				$liste_date .= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;" . $daty_voalohany . " au " . $daty_farany . "&nbsp;&nbsp; </td><td><table><tr><td>Prix : </td><td style=\"width:13mm;text-align:right;\"><strong> " . number_format($date_det[$i]->prix, 0, ".", " ")
					. utf8_decode(" &euro;</strong>&nbsp;&nbsp;") . "</td></tr></table>"
					. "</td><td>" . ((!is_null($date_det[$i]->prix_enfant) && ($date_det[$i]->prix_enfant > 0)) ? "<table><tr><td>Prix enfant : </td><td style=\"text-align:right;\"><strong>"
					. number_format($date_det[$i]->prix_enfant, 0, ".", " ") . utf8_decode(" &euro;</strong>&nbsp;&nbsp;") . "</td></tr></table>" : "&nbsp;")
					. "</td><td>" . ((!is_null($date_det[$i]->prix_suppl) && ($date_det[$i]->prix_suppl > 0)) ? "<table style=\"width:100%\"><tr><td>" .
					utf8_decode("Prix supplément single : </td><td style=\"text-align:right;\"> <strong> ") . number_format($date_det[$i]->prix_suppl, 0, ".", " ") . utf8_decode(" &euro;</strong>&nbsp;&nbsp;") . "</td></tr></table>" : "&nbsp;")
					. "</td><td style=\"text-align:right;width:45mm;\"><span>" . (($date_det[$i]->confirme == 1) ? utf8_decode("<span style=\"color:#3fcc5f\">Confirmé</span>&nbsp;&nbsp;") : "")
					. "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/index.php?option=com_content&view=article&id=78&id_date="
					. $date_det[$i]->id . "&id_voyage=" . $id . "\">Inscription</a></span></td></tr>";
			}
			$liste_date .= "</table>";
			$txt = str_replace("{//mt_liste_date}", $liste_date, $txt);
			if (!is_null($date_det[0]->prix_suppl_chb_indiv) && $date_det[0]->prix_suppl_chb_indiv > 0)
				$txt = str_replace("{//mt_supplement}", utf8_decode("Supplément chambre/tente  individuelle : ") . number_format($date_det[0]->prix_suppl_chb_indiv, 0, ".", " ") . "&euro;", $txt);
			$txt = str_replace("{//mt_supplement}", "", $txt);
			$page_dd .= '<tr><td style="width:100%;"><span style="text-align:justify;">' . utf8_encode($txt) . '</span></td></tr>';
			$page_dd .= '</table>';
			$page .= '<tr><td style="width:100%;"><span style="text-align:justify;">' . replacea($page_dd) . '</span></td></tr>';
			//$page .= '<tr><td style="width:100%;"><hr style="border:1px dotted #ccc;"></td></tr>';
		}
	}
	$date_gpe = getDateGroupe($id,$connexion);
	if(isset($date_gpe) && $date_gpe != ""){
		$txt_date_gpe = getTexteDateGroupe($id,$connexion);
		if(isset($txt_date_gpe) && $txt_date_gpe != ""){
			$page_dd = '<table style="width:100%">';
			$page_dd .= '<tr><td style="width:100%;border:2px solid #f09800;padding:5px;"><span style="font-weight:bold;font-size:12px;text-align:left;">' . utf8_encode($txt_date_gpe->titre) . '</span></td></tr>';
			$txt = $txt_date_gpe->contenu;
			$txt = supprimerTousPAttr($txt);
			$txt = supprimerTousBalise($txt, false, "div");
			for ($i = 0; $i < count($date_gpe); $i++) {
				$nb_pers[$i] = $date_gpe[$i]->nbre_pers;
				$hs[$i] = $date_gpe[$i]->haute_saison;
				$bs[$i] = $date_gpe[$i]->basse_saison;
			}
			$liste_pg = "<br><table style=\"border-collapse: collapse\">";
			$liste_pg .= "<tr><td style=\"border:1px solid #999;\"><strong>Nombre de personne</strong></td>";
			for ($i = 0; $i < sizeof($nb_pers); $i++) {
				if ($i + 1 < sizeof($nb_pers)) {
					if (($nb_pers[$i] + 1 ) < $nb_pers[$i + 1]) {
						$liste_pg .= "<td style=\"text-align:center;border:1px solid #999;vertical-align:middle;\">" . $nb_pers[$i] . utf8_decode(" à ") . ($nb_pers[$i + 1] - 1) . "</td>";
					} else {
						$liste_pg .= "<td style=\"text-align:center;border:1px solid #999;vertical-align:middle;\">" . $nb_pers[$i] . "</td>";
					}
				} else {
					$liste_pg .= "<td style=\"text-align:center;border:1px solid #999;vertical-align:middle;\">" . $nb_pers[$i] . "</td>";
				}
			}
			$liste_pg .= "</tr>";
			$saison = getSaison($id,$connexion);
			$date_deb_hs = preg_split('/-/', $saison->date_deb_hs);
			$date_fin_hs = preg_split('/-/', $saison->date_fin_hs);
			$date_deb_bs = preg_split('/-/', $saison->date_deb_bs);
			$date_fin_bs = preg_split('/-/', $saison->date_fin_bs);
			$liste_pg .= "<tr><td style=\"border:1px solid #999;\"><strong>Prix haute saison</strong></td>";
			for ($i = 0; $i < sizeof($hs); $i++) {
				$liste_pg .= "<td style=\"text-align:right;border:1px solid #999;vertical-align:middle;padding:5px;\">" . number_format($hs[$i], 0, ".", " ") . " &euro;</td>";
			}
			$liste_pg .= "</tr>";
			$liste_pg .= "<tr><td style=\"border:1px solid #999;vertical-align:middle;\"><strong>Prix basse saison</strong></td>";
			for ($i = 0; $i < sizeof($bs); $i++) {
				$liste_pg .= "<td style=\"text-align:right;border:1px solid #999;vertical-align:middle;padding:5px;\">" . number_format($bs[$i], 0, ".", " ") . " &euro;</td>";
			}
			$liste_pg .= "</tr>";
			$liste_pg .= "</table>";
			$txt = str_replace("{//mt_liste_prix_gpe}", $liste_pg, $txt);
			$single = getSingle($id,$connexion);
			$liste_sg = "";
			if(isset($single)){
				$liste_sg = "<table>";
				$liste_sg .= "<tr><td colspan=\"2\"><strong><span style=\"color:#800000;\">".utf8_decode("Supplément chambre single")." :</span></strong></td></tr>";
				$liste_sg .= "<tr><td><strong>Prix haute saison</strong></td><td>" . number_format($single->haute_saison) . " &euro;</td></tr>";
				$liste_sg .= "<tr><td><strong>Prix basse saison</strong></td><td>" . number_format($single->basse_saison) . " &euro;</td></tr>";
				$liste_sg .= "</table>";
			}
			$enfant = getEnfant($id,$connexion);
			if (isset($enfant)) {
				$liste_ss = "<table>";
				$liste_ss .= "<tr><td colspan=\"2\"><strong><span style=\"color:#800000;\">".utf8_decode("Prix du voyage enfant de - 12 ans :")." </span></strong></td></tr>";
				$liste_ss .= "<tr><td><strong>Prix haute saison</strong></td><td>" . number_format($enfant->haute_saison) . " &euro;</td></tr>";
				$liste_ss .= "<tr><td><strong>Prix basse saison</strong></td><td>" . number_format($enfant->basse_saison) . " &euro;</td></tr>";
				$liste_ss .= "</table>";
			}
			$txtListe = "";
			if(isset($single) || isset($enfant)){
				if(isset($single) && isset($enfant)){
					$txtListe = "<table><tr><td>" . $liste_sg . "</td><td>&nbsp;</td><td>" . $liste_ss . "</td></tr></table>";
				}else if(isset($single) && !isset($enfant)){
					$txtListe = "<table><tr><td>" . $liste_sg . "</td></tr></table>";
				}else if (!isset($single) && isset($enfant)) {
					$txtListe = "<table><tr><td>" . $liste_ss . "</td></tr></table>";
				}
			}
			$txt = supprimerTexteEntre("{//mt_if_sg}","{//mt_endif_sg}",$txt);
			$tmp = alaivoTexteEntreMiaraka("{//mt_if_e}","{//mt_endif_e}",$txt);
			$txt = str_replace($tmp, $txtListe, $txt);
			$btn = '<div style="text-align:right;"><a href="http://' . $_SERVER['HTTP_HOST'] . '/index.php?option=com_content&view=article&id=78&id_voyage='.$id.'&mi=pcv">S\'inscrire au voyage</a></div>';
			$txt = str_replace('{//mt_bouton}', $btn, $txt);
			$hs_bs = getHS_BS($id,$connexion);
			$date_deb_hs = preg_split('/-/', $hs_bs->date_deb_hs);
			$date_fin_hs = preg_split('/-/', $hs_bs->date_fin_hs);
			$date_deb_bs = preg_split('/-/', $hs_bs->date_deb_bs);
			$date_fin_bs = preg_split('/-/', $hs_bs->date_fin_bs);
			$liste_dhb = "<br><strong>Haute saison : </strong>du " . $date_deb_hs[1] . "/" . $date_deb_hs[0] . " au " . $date_fin_hs[1] . "/" . $date_fin_hs[0] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$liste_dhb .= "<strong>Basse saison : </strong>du " . $date_deb_bs[1] . "/" . $date_deb_bs[0] . " au " . $date_fin_bs[1] . "/" . $date_fin_bs[0] . "<br>";
			$txt = str_replace("{//mt_date_hs_bs}", $liste_dhb, $txt);
			$page_dd .= '<tr><td style="width:100%"><span style="text-align:justify;">' . utf8_encode($txt) . '</span></td></tr>';
			$page_dd .= '</table><br>';
			$page .= '<tr><td style="width:100%"><span style="text-align:justify;">' . replacea($page_dd) . '</span></td></tr>';
			//$page .= '<tr><td style="width:100%;"><hr style="border:1px dotted #ccc;"></td></tr>';
		}
	}
	$contenu = str_replace('{loadposition module_date}','',$contenu);
	$dm = getDateMesure($id,$connexion);
	if(isset($dm) && $dm != ""){
		$txt_dm = getTexteDateMesure($id,$connexion);
		if(isset($txt_dm) && $txt_dm != ""){
			$page_dd = '<table style="width:100%">';
			$page_dd .= '<tr><td style="width:100%;border:2px solid #f09800;padding:5px;"><span style="font-weight:bold;font-size:12px;text-align:left;">' . utf8_encode($txt_dm->titre) . '</span></td></tr>';
			$txt = supprimerTousPAttr($txt_dm->contenu);
			$txt = str_replace("{//mt_nbpers}", $dm->nbre_pers_min_mesure, trim($txt));
			$txt = str_replace("{//mt_prix}", number_format($dm->prix, 0, ".", " "), $txt);
			$page_dd .= '<tr><td style="width:100%"><br><br><span style="text-align:justify;">' . utf8_encode($txt) . '</span></td></tr>';
			$page_dd .= '</table><br>';
			$page .= '<tr><td style="width:100%"><span style="text-align:justify;">' . replacea($page_dd) . '</span></td></tr>';
			//$page .= '<tr><td style="width:100%;"><hr style="border:1px dotted #ccc;"></td></tr>';
		}
	}
	$contenu = str_replace('{loadposition module_date_mesure}','',$contenu);
//	$info_prix = getInfoPrix($id,$connexion);
//	$page_dd = '<table style="width:100%">';
//	for($i = 0 ; $i < count($info_prix) ; $i++){
//		$txt = supprimerTousPAttr($info_prix[$i]->contenu);
//		$page_dd .= '<tr><td style="width:100%"><span style="font-weight:bold;font-size:12px;text-align:left;">' . utf8_encode($info_prix[$i]->titre) . '</span></td></tr>';
//		$page_dd .= '<tr><td style="width:100%"><span style="text-align:justify;">' . utf8_encode($txt) . '</span></td></tr>';
//	}
//	$page_dd .= '</table>';
//	$page .= '<tr><td style="width:100%"><span style="text-align:justify;">' . $page_dd . '</span></td></tr>';
//	$contenu = str_replace('{loadposition module_info_prix}','',$contenu);
//	$autre_proc_inscr = getAutreProcInscr($id,$connexion);
//	$page_dd = '<table style="width:100%">';
//	for($i = 0 ; $i < count($autre_proc_inscr) ; $i++){
//		$txt = supprimerTousPAttr($autre_proc_inscr[$i]->contenu);
//                $txt = preg_replace("#<ul[^>]*>#i", "<ul>", $txt);
//                $txt = preg_replace("#<li[^>]*>#i", "<li>", $txt);
//		$doc = strpos($txt,"conditions_generales_de_vente_de_Malagasy_Tours") ;
//		if($doc > 0){
//			preg_match('#<a[^>]*>#',$txt,$tmp);
//			$tmp = alaivoTexteEntreMiaraka($tmp[0], '</a>', $txt);
//			$txt = str_replace($tmp, utf8_decode(' à la fin de ce document'), $txt);
//		}
//		$page_dd .= '<tr><td style="width:100%"><span style="font-weight:bold;font-size:12px;text-align:left;">' . utf8_encode($autre_proc_inscr[$i]->titre) . '</span><br><br></td></tr>';
//		$page_dd .= '<tr><td style="width:100%;"><span style="text-align:justify;">' . utf8_encode($txt) . '</span><br><br></td></tr>';
//	}
//	$page_dd .= '</table>';
//	$page .= '<tr><td style="width:100%"><span style="text-align:justify;">' . $page_dd . '</span></td></tr>';
	$info_prix = getInfoPrix($id,$connexion);
	for($i = 0 ; $i < count($info_prix) ; $i++){
		$page_dd = '<table style="width:100%">';
		$txt = supprimerTousPAttr($info_prix[$i]->contenu);
		$page_dd .= '<tr><td style="width:100%"><span style="font-weight:bold;font-size:12px;text-align:left;">' . utf8_encode($info_prix[$i]->titre) . '</span></td></tr>';
		$page_dd .= '<tr><td style="width:100%"><span style="text-align:justify;">' . utf8_encode($txt) . '</span></td></tr>';
		$page_dd .= '</table>';
		$page .= '<tr><td style="width:100%"><span style="text-align:justify;">' . replacea($page_dd) . '</span></td></tr>';
	}
	$contenu = str_replace('{loadposition module_info_prix}','',$contenu);
	$autre_proc_inscr = getAutreProcInscr($id,$connexion);
	for($i = 0 ; $i < count($autre_proc_inscr) ; $i++){
		$page_dd = '<table style="width:100%">';
		$txt = supprimerTousPAttr($autre_proc_inscr[$i]->contenu);
		$txt = preg_replace("#<ul[^>]*>#i", "<ul>", $txt);
		$txt = preg_replace("#<li[^>]*>#i", "<li>", $txt);
		$doc = strpos($txt,"conditions_generales_de_vente_de_Malagasy_Tours") ;
		if($doc > 0){
			preg_match('#<a[^>]*>#',$txt,$tmp);
			$tmp = alaivoTexteEntreMiaraka($tmp[0], '</a>', $txt);
			$txt = str_replace($tmp, utf8_decode(' à la fin de ce document'), $txt);
		}
		$page_dd .= '<tr><td style="width:100%"><span style="font-weight:bold;font-size:12px;text-align:left;">' . utf8_encode($autre_proc_inscr[$i]->titre) . '</span><br><br></td></tr>';
		$page_dd .= '<tr><td style="width:100%;"><span style="text-align:justify;">' . utf8_encode($txt) . '</span><br><br></td></tr>';
		$page_dd .= '</table>';
		$page .= '<tr><td style="width:100%"><span style="text-align:justify;">' . replacea($page_dd) . '</span></td></tr>';
	}
	$contenu = str_replace('{loadposition module_autres_proc_inscr}','',$contenu);
	$page .= '</table>';
	$page .= '</page>';
	$page .= '<page style="font-size: 11px;" backcolor="#FFFFCC" backleft="7mm" backright="7mm" backtop="10mm" backbottom="15mm">';
	$page .= <<<EOT
<page_header>
<table style="width:100%">
<tr>
<td style="width: 100%;text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">$androany</span>
</td>
</tr>
</table>
</page_header>
<page_footer>
<table style="width:100%;background-color:#d0c4ae">
<tr>
<td style="width: 90%; text-align: left">
<span style="font-size:10px;font-style:italic; text-align: left">Malagasy Tours</span>
</td>
<td style="width: 10%; text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">Page [[page_cu]]/[[page_nb]]</span>
</td>
</tr>
</table>
</page_footer>
EOT;
	$tab = alaivoTexteEntre('{tab=', '}', $contenu)	;
	$contenu = str_replace('{tab=' . $tab . '}','',$contenu);
	$txtInfoComp = getInfoComplementaires($id,$connexion);
	if(isset($txtInfoComp)){
		$page .= '<span style="font-weight:bold;font-size:18px">'.utf8_encode($tab).'</span><br><br>';
        $page .= '<table style="width:100%;;border-left:1px solid #000;padding-left:5px;">';
        for($i = 0 ; $i < count($txtInfoComp) ; $i++){
        //for($i = 0 ; $i < 14 ; $i++){
            $txt = supprimerTousPAttr($txtInfoComp[$i]->contenu);
            $txt = supprimerTousBalise($txt, false, "ul");
            $txt = supprimerTousBalise($txt, true, "li",true);
        	$page_dd = '<table style="width:100%">';
            $page_dd .= '<tr><td style="width:100%"><br><span style="font-weight:bold;font-size:12px;text-align:left;">' . utf8_encode($txtInfoComp[$i]->titre) . '</span></td></tr>';
            if(isset($txtInfoComp[$i]->sary) && $txtInfoComp[$i]->sary != ""){
                    $page_dd .= '<tr><td><table><tr><td style="width:580"><span style="text-align:justify;">' . utf8_encode($txt) . '</span></td><td style="width:100"><img src="../images/'.$txtInfoComp[$i]->sary.'" style="width:100"></td></tr></table></td></tr>';
            }else{
                    $page_dd .= '<tr><td style="width:600"><span style="text-align:justify;">' . utf8_encode($txt) . '</span></td></tr>';
            }
        	$page_dd .= "</table>";
        	$page .= '<tr><td>' . replacea($page_dd) . '</td></tr>';
        }
        $page .= '</table>';
	}
	$page .= '</page>';
	$page .= '<page style="font-size: 11px;" backcolor="#FFFFCC" backleft="7mm" backright="7mm" backtop="10mm" backbottom="15mm">';
	$page .= <<<EOT
<page_header>
<table style="width:100%">
<tr>
<td style="width: 100%;text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">$androany</span>
</td>
</tr>
</table>
</page_header>
<page_footer>
<table style="width:100%;background-color:#d0c4ae">
<tr>
<td style="width: 90%; text-align: left">
<span style="font-size:10px;font-style:italic; text-align: left">Malagasy Tours</span>
</td>
<td style="width: 10%; text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">Page [[page_cu]]/[[page_nb]]</span>
</td>
</tr>
</table>
</page_footer>
EOT;
	$tab = alaivoTexteEntre('{tab=', '}', $contenu)	;
	$contenu = str_replace('{tab=' . $tab . '}','',$contenu);
	$page .= '<span style="font-weight:bold;font-size:18px">'.utf8_encode($tab).'</span><br><br>';
	$txtVols = getVols($id,$connexion);
	if(isset($txtVols)){
		$page_dd = '<table style="width:100%;;border-left:1px solid #000;padding-left:5px;">';
		for($i = 0 ; $i < count($txtVols) ; $i++){
			$txt = supprimerTousP($txtVols[$i]->contenu);
			$txt = supprimerTousBalise($txt, false, "ul");
			$txt = supprimerTousBalise($txt, true, "li",true);
			$page_dd .= '<tr><td style="width:100%" colspan="2"><br><span style="font-weight:bold;font-size:12px;text-align:left;">' . $txtVols[$i]->titre . '</span></td></tr>';
			if(isset($txtVols[$i]->sary) && $txtVols[$i]->sary != ""){
				$page_dd .= '<tr><td style="width:500"><span style="text-align:justify;">' . $txt . '</span></td><td style="width:100"><img src="../images/'.$txtVols[$i]->sary.'" style="width:130px"></td></tr>';
			}else{
				$page_dd .= '<tr><td style="width:600" colspan="2"><span style="text-align:justify;">' . $txt . '</span></td></tr>';
			}
		}
		$page_dd .= '</table>';
		//echo $page_dd;
		$page .=  replacea(utf8_encode($page_dd));
	}
	$page .= "</page>";
	$page .= '<page style="font-size: 11px;" backcolor="#FFFFCC" backleft="7mm" backright="7mm" backtop="10mm" backbottom="15mm">';
	$page .= <<<EOT
<page_header>
<table style="width:100%">
<tr>
<td style="width: 100%;text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">$androany</span>
</td>
</tr>
</table>
</page_header>
<page_footer>
<table style="width:100%;background-color:#d0c4ae">
<tr>
<td style="width: 90%; text-align: left">
<span style="font-size:10px;font-style:italic; text-align: left">Malagasy Tours</span>
</td>
<td style="width: 10%; text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">Page [[page_cu]]/[[page_nb]]</span>
</td>
</tr>
</table>
</page_footer>
EOT;
	$tab = alaivoTexteEntre('{tab=', '}', $contenu)	;
	$contenu = str_replace('{tab=' . $tab . '}','',$contenu);
	$page .= '<span style="font-weight:bold;font-size:18px">'.utf8_encode($tab).'</span><br><br>';
	$assurance = getAssurance($id,$connexion);
	$txtAssurance = majSrcImg($assurance->texte);
	$txtAssurance = supprimerTousPAttr($txtAssurance);
	$txtAssurance = doublerBR($txtAssurance);
	$txtAssurance = preg_replace("#font-size:[^;]*;#", '', $txtAssurance);
	$txtAssurance = str_replace("<h1>", "<span style=\"font-size:12px\">", $txtAssurance);
	$txtAssurance = str_replace("</h1>", "</span>", $txtAssurance);
	$tmp = alaivoTexteEntreMiaraka('<table', '>', $txtAssurance);
	$tmp = alaivoTexteEntreMiaraka($tmp, '</table>', $txtAssurance);
	$img = alaivoBaliseAttribut($txtAssurance, "img","src");
	$imgAssurance = '<br><br><table align="center"><tr><td><img src="'.$img[0]["img"][0]["src"].'" style="width:68"></td><td><img src="'.$img[0]["img"][1]["src"].'" style="width:68"></td></tr></table><br>';
	$txtAssurance = str_replace($tmp, "etoilaysoloinarefavitalemanovaanletablerehetra", $txtAssurance);
	$txtAssurance = preg_replace("#<table[^>]*>#","<br><table>",$txtAssurance);
	$txtAssurance = preg_replace("#<tr[^>]*>#","<tr>",$txtAssurance);
	$txtAssurance = preg_replace("#<td[^>]*>[^(<a)]*<a#","<td style=\"width:100;\" valign=\"top\"><a",$txtAssurance);
	$txtAssurance = preg_replace("#<td[^>]*>[^(<span)]*<span[^>]*>[(<a)]*<a#","<td style=\"width:100;\" valign=\"top\"><span><a",$txtAssurance);
	$txtAssurance = preg_replace("#<td[^>]*>[^<a]#","<td style=\"width:500;padding-left:3mm;\" valign=\"top\">",$txtAssurance);
	$txtAssurance = str_replace("etoilaysoloinarefavitalemanovaanletablerehetra", $imgAssurance, $txtAssurance);
	$txtAssurance = preg_replace('#<img src="../images/cg.png"[^>]*>#','<img src="../images/cg.png" style="width:20;">',$txtAssurance);
	$txtAssurance = preg_replace('#<img src="../images/tg.png"[^>]*>#','<img src="../images/tg.png" style="width:20;">',$txtAssurance);
	$tmp = alaivoTexteEntreMiaraka('IL VOUS APPARTIENT DE', 'PAS DANS CETTE PROCEDURE.', $txtAssurance);
	$txtAssurance = str_replace($tmp,'<br>'.$tmp.'<br>',$txtAssurance);
	$tmp = alaivoTexteEntreMiaraka('<strong>Malagasy Tours</strong>', '+ prestation Malagasy Tours).', $txtAssurance);
	$txtAssurance = str_replace($tmp,'<br><br>'.$tmp,$txtAssurance);
	$txtAssurance = str_replace('La convention, ','<br><br>La convention, ',$txtAssurance);
	$txtAssurance = str_replace('Entre Malagasy tours et l\'assureur',utf8_decode('<br><br>Entre Malagasy tours et l\'assureur'),$txtAssurance);
	$page .= '<table style="text-align: left;width: 100%;border-left:1px solid #000;padding-left:5px;">';
	$page .= '<tr><td style="width:100%">' . replacea(utf8_encode($txtAssurance)) . '</td></tr>';
	$page .= '</table>';
	$contenu .= str_replace('{loadposition module_assurance}','',$contenu);
	$page .= '</page>';
	$page .= '<page style="font-size: 11px;" backcolor="#FFFFCC" backleft="0mm" backright="0mm" backtop="3mm" backbottom="0mm">';
	$page .= <<<EOT
<page_header>
<table style="width:100%">
<tr>
<td style="width: 100%;text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">$androany</span>
</td>
</tr>
</table>
</page_header>
<page_footer>
<table style="width:100%;background-color:#d0c4ae">
<tr>
<td style="width: 90%; text-align: left">
<span style="font-size:10px;font-style:italic; text-align: left">Malagasy Tours</span>
</td>
<td style="width: 10%; text-align: right">
<span style="font-size:10px;font-style:italic; text-align: right">Page [[page_cu]]/[[page_nb]]</span>
</td>
</tr>
</table>
</page_footer>
EOT;
	$page .= '<img src="../fichiers/conditions_generales_de_vente_de_Malagasy_Tours.png"  style="width:196mm;"/>';
	$page .= '</page>';
	//echo $page;*/
	$lohateny = clean($article->title) . ".pdf";
	$html2pdf = new HTML2PDF('P', 'A4', 'fr');
	$html2pdf->setDefaultFont('helvetica');
	$html2pdf->pdf->SetAuthor('Malagasy Tours');
	$html2pdf->pdf->SetTitle(utf8_encode($article->title));
	//$html2pdf->pdf->SetSubject('HTML2PDF Wiki');
	//$html2pdf->pdf->SetKeywords('HTML2PDF, TCPDF, example, wiki');
	$html2pdf->writeHTML($page, isset($_GET['vuehtml']));
	$html2pdf->Output($lohateny);
	unset($html2pdf);
}catch(Exception $e){
	echo 'Erreur : '.$e->getMessage().'<br />';
	echo 'N° : '.$e->getCode();
}
?>
<?php
function Html2Excel($texte){
	$corres = array(
		"&quot;" => "\"","&apos;" => "'","&amp;" => "&","&lt;"=>"<","&gt;" => ">","&nbsp;"=>" ","&iexcl;"=>"¡","&cent;"=>"¢","&pound;"=>"£","&curren;"=>"¤",
		"&yen;"=>"¥","&brvbar;"=>"¦","&sect;"=>"§","&uml;"=>"¨","&copy;"=>"©","&ordf;"=>"ª","&laquo;"=>"«","&not;"=>"¬","&shy;"=>" ","&reg;"=>"®","&macr;"=>"¯",
		"&deg;"=>"°","&plusmn;"=>"±","&sup2;"=>"²","&sup3;"=>"³","&acute;"=>"´","&micro;"=>"µ","&para;"=>"¶","&middot;"=>"·",	"&cedil;"=>"¸",	"&sup1;"=>"¹","&ordm;"=>"º",
		"&raquo;"=>"»","&frac14;"=>"¼","&frac12;"=>"½","&frac34;"=>"¾","&iquest;"=>"¿","&times;"=>"×","&divide;"=>"÷","&Agrave;"=>"À","&Aacute;"=>"Á","&Acirc;"=>"Â","&Atilde;"=>"Ã",
		"&Auml;"=>"Ä","&Aring;"=>"Å","&AElig;"=>"Æ","&Ccedil;"=>"Ç","&Egrave;"=>"È","&Eacute;"=>"É","&Ecirc;"=>"Ê","&Euml;"=>"Ë","&Igrave;"=>"Ì","&Iacute;"=>"Í",
		"&Icirc;"=>"Î","&Iuml;"=>"Ï","&ETH;"=>"Ð","&Ntilde;"=>"Ñ","&Ograve;"=>"Ò","&Oacute;"=>"Ó","&Ocirc;"=>"Ô","&Otilde;"=>"Õ","&Ouml;"=>"Ö","&Oslash;"=>"Ø",
		"&Ugrave;"=>"Ù","&Uacute;"=>"Ú","&Ucirc;"=>"Û","&Uuml;"=>"Ü","&Yacute;"=>"Ý","&THORN;"=>"Þ","&szlig;"=>"ß","&agrave;"=>"à","&aacute;"=>"á","&acirc;"=>"â",
		"&atilde;"=>"ã","&auml;"=>"ä","&aring;"=>"å","&aelig;"=>"æ","&ccedil;"=>"ç","&egrave;"=>"è","&eacute;"=>"é","&ecirc;"=>"ê","&euml;"=>"ë","&igrave;"=>"ì",
		"&iacute;"=>"í","&icirc;"=>"î","&iuml;"=>"ï","&eth;"=>"ð","&ntilde;"=>"ñ","&ograve;"=>"ò","&oacute;"=>"ó","&ocirc;"=>"ô","&otilde;"=>"õ","&ouml;"=>"ö",
		"&oslash;"=>"ø","&ugrave;"=>"ù","&uacute;"=>"ú","&ucirc;"=>"û","&uuml;"=>"ü","&yacute;"=>"ý","&thorn;"=>"þ","&yuml;"=>"ÿ","&OElig;"=>"Œ","&oelig;"=>"œ",
		"&Scaron;"=>"Š","&scaron;"=>"š","&Yuml;"=>"Ÿ","&fnof;"=>"ƒ","&circ;"=>"ˆ","&tilde;"=>"˜","&ensp;"=>" ","&emsp;"=>" ","&thinsp;"=>" ","&zwnj;"=>" ","&zwj;"=>" ",
		"&lrm;"=>" ","&rlm;"=>" ","&ndash;"=>"–","&mdash;"=>"—","&lsquo;"=>"‘","&rsquo;"=>"’","&sbquo;"=>"‚","&ldquo;"=>"“","&rdquo;"=>"”",	"&bdquo;"=>"„",	"&dagger;"=>"†",
		"&Dagger;"=>"‡","&bull;"=>"•","&hellip;"=>"…","&permil;"=>"‰","&prime;"=>"'","&Prime;"=>"?","&lsaquo;"=>"‹","&rsaquo;"=>"›","&oline;"=>"?","&euro;"=>"€",
		"&trade;"=>"™","&larr;"=>"?","&uarr;"=>"?","&rarr;"=>"?","&darr;"=>"?","&harr;"=>"?","&crarr;"=>"?","&lceil;"=>"?","&rceil;"=>"?","&lfloor;"=>"?","&rfloor;"=>"?",
		"&loz;"=>"?","&spades;"=>"?","&clubs;"=>"?","&hearts;"=>"?","&diams;"=>"?",	);
	foreach($corres as $key => $value){
		if(preg_match("/".$key."/", $texte)){
			$texte = preg_replace("/".$key."/", $corres[$key], $texte);
		}
	}
	return $texte;
}

/**
 * transformApostrophe()
 *
 * @param mixed $contenu
 * @return
 */
function transformApostrophe($contenu){
	return preg_replace("/â€™/","'",$contenu);
}


function alaivoSaryRehetra($contenu){
	return alaivoBaliseAttribut($contenu, "img", "src");
}
function alaivoBaliseAttribut($str,$tag,$att = null){

/*$str = <<< END
   <img src="imag1.png" title ='photo de toto' alt="tot1" />
   <img title='photo de toto' src = "image2.png" alt="toto2" />
   <img src=imag3.gif title='photo de toto' alt='toto3' />
   END;*/

	/*$tag = 'img';
	   $att = 'src';*/
	$regex_balise = '/<'.$tag.'[^>]*>/';
	$i = 0;
	if (preg_match_all($regex_balise, $str, $m)) {
		$result = array();
		foreach($m[0] as $balise) {
			$buffer = array();
			//$str = preg_replace($balise, "{sary" . $i . "}", $str);
			//$str = str_replace("<{sary" . $i . "}>", "{sary" . $i . "}", $str);
			$reg = sprintf('/(%s) \s* = \s* (["\']?) ([^">\s]*) \2/ix', $att);
			if (preg_match_all($reg, $balise, $n)) {
				foreach($n[0] as $key=>$value) {
					$yTmp = $n[1][$key];
					$buffer[$yTmp] = $n[3][$key];
				}
			}
			$result[$tag][] = $buffer;
			$i++;
		}
		return array($result,$str);
	}
}

/**
 * getArticle()
 *
 * @param mixed $_POST
 * @return
 */
function getArticle($id,$connexion){
	try{
		$query = 'SELECT id,title,introtext,duree,txt_accroche,code_circuit from jos_content where catid = 3 and id = ' . $id;
		$resultats=$connexion->query($query);
		$resultats->setFetchMode(PDO::FETCH_OBJ);
		$ligne = $resultats->fetch();
		$resultats->closeCursor();
	}catch(Exception $e){
		echo 'Erreur : '.$e->getMessage().'<br />';
		echo 'N° : '.$e->getCode();
	}
	return $ligne;
}
function supprimerTexteEntre($manomboka, $mamarana, $contenu){
	return str_replace(alaivoTexteEntreMiaraka($manomboka, $mamarana, $contenu), '', $contenu);
}
function alaivoTexteEntreMiaraka($manomboka, $mamarana, $contenu){
	$texte_interne = substr($contenu, strpos($contenu, $manomboka));
	$texte_interne = substr($texte_interne,0,(strpos($texte_interne,$mamarana) + strlen($mamarana)));
	return $texte_interne;
}
function alaivoTexteEntre($manomboka,$mamarana,$contenu){
	$texte_interne = alaivoTexteEntreMiaraka($manomboka, $mamarana, $contenu);
	$texte_interne = str_replace($manomboka,'',$texte_interne);
	$texte_interne = str_replace($mamarana,'',$texte_interne);
	return $texte_interne;
}

/**
 * alaivoAnatinyBalise()
 *
 * @param mixed $contenu
 * @param mixed $p2
 * @return
 */
function alaivoAnatinyBalise($contenu, $p2){
	return alaivoTexteEntre('<' . $p2 . '>', '</' . $p2 . '>', $contenu);
}

/**
 * getContact()
 *
 * @param mixed $_POST
 * @param mixed $connexion
 * @return
 */
function getContact($id, $connexion){
	try{
		$query = "select b.image,b.nom,b.prenom,b.contact_eventuel,b.skype,b.email,b.texte_intro,b.horaire_ouv_bur from jos_content as a, contact as b where a.id_contact_mt=b.id_contact and a.id=" . $id;
		$resultats=$connexion->query($query);
		$resultats->setFetchMode(PDO::FETCH_OBJ);
		$ligne = $resultats->fetch();
		$resultats->closeCursor();
	}catch(Exception $e){
		echo 'Erreur : '.$e->getMessage().'<br />';
		echo 'N° : '.$e->getCode();
	}
	return $ligne;
}

/**
 * alaivoSaryCarte()
 *
 * @param mixed $saryCarte
 * @param mixed $p2
 * @param mixed $p3
 * @return
 */
function alaivoSary($saryCarte){
	$tmp = alaivoBaliseAttribut($saryCarte, 'img','src');
	return $tmp[0]['img'][0]['src'];
}

/**
 * getLevel()
 *
 * @param mixed $_POST
 * @param mixed $connexion
 * @return
 */
function getLevel($id, $connexion){
	try{
		$query = 'SELECT dl.*,d.image as imagediff,l.image as imagelev,l.description as descrl,d.description as descrd,d.name as named, l.name as namel FROM mt_difficulty_level dl'
		. ' LEFT JOIN  mt_difficulty d ON d.id_difficulty = dl.id_difficulty '
		. ' LEFT JOIN  mt_level l ON l.id_level = dl.id_level '
		. ' WHERE dl.id_content='.$id;
		$resultats=$connexion->query($query);
		$resultats->setFetchMode(PDO::FETCH_OBJ);
		$ligne = $resultats->fetch();
		$resultats->closeCursor();
	}catch(Exception $e){
		echo 'Erreur : '.$e->getMessage().'<br />';
		echo 'N° : '.$e->getCode();
	}
	return $ligne;
}

/**
 * getJourJour()
 *
 * @param mixed $id
 * @return
 */
function getJourJour($id,$connexion){
	try{
		$query = "SELECT a.titre, a.contenu, a.lunch, a.distance, a.duree, a.diner, a.numero_jour, b.lieu AS hotellieu, b.nom AS hotelnom, "
    	. " b.descriptif AS hoteldescriptif, b.sary1 AS hotelsary1, b.sary2 AS hotelsary2,a.autre_info,a.sary,a.id_aerien,a.info_botanique "
		. "FROM jourjour AS a LEFT OUTER JOIN jourjourhotelhour AS c ON a.id = c.id_jourjour "
		. "LEFT OUTER JOIN hoteljour AS b ON b.id = c.id_hoteljour, voyagejourjour as vjj "
		. "WHERE vjj.id_voyage=" . $id . " and vjj.id_jourjour = a.id order by a.numero_jour";
		$resultats=$connexion->query($query);
		$resultats->setFetchMode(PDO::FETCH_OBJ);
		$ret = "";
		$i = 0;
		$row = array();
		while($ligne = $resultats->fetch()){
			$row[$i] = $ligne;
			$i++;
		}
		$resultats->closeCursor();
	}catch(Exception $e){
		echo 'Erreur : '.$e->getMessage().'<br />';
		echo 'N° : '.$e->getCode();
	}
	return $row;
}

/**
 * getAerien()
 *
 * @param mixed $row
 * @param mixed $connexion
 * @return
 */
function getAerien($id_aerien, $connexion){
	try{
		$query = "select v.nom_ville as ville_depart, v1.nom_ville as ville_arrivee, a.duree,v.code_iata as iata_depart, v1.code_iata as iata_arrivee "
    			. " from aerien as a, villes as v, villes as v1 "
    			. "where a.id_ville_depart = v.id_ville and a.id_ville_arrivee = v1.id_ville and a.id_aerien=" . $id_aerien;
		$resultats=$connexion->query($query);
		$resultats->setFetchMode(PDO::FETCH_OBJ);
		$ligne = $resultats->fetch();
		$resultats->closeCursor();
	}catch(Exception $e){
		echo 'Erreur : '.$e->getMessage().'<br />';
		echo 'N° : '.$e->getCode();
	}
	return $ligne;
}

	/**
	 * getDateDetermine()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getDateDetermine($id, $connexion)	{
		try{
			$query = "select a.date_deb,b.duree,a.prix,b.nbre_pers_min_fixes,b.prix_suppl_chb_indiv , a.id,a.prix_enfant,a.prix_suppl, a.confirme "
        		. "from date_deb as a,jos_content as b where a.id_content=b.id and a.id_content=" . $id . " and a.date_deb > now() ";
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$i = 0;
			$row = array();
			while($ligne = $resultats->fetch()){
				$row[$i] = $ligne;
				$i++;
			}
			$resultats->closeCursor();
		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $row;
	}
	function getTexteDateDetermine($id,$connexion){
		try{
			$query = "select a.titre,a.contenu from contenu_date_determinee as a,jos_content as b where a.aafficher = 1 and b.id_contenu_dd=a.id and b.id=" . $id;
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne = $resultats->fetch();
			$resultats->closeCursor();

		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $ligne;
	}

	/**
	 * getDateGroupe()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getDateGroupe($id, $connexion)	{
		try{
			$query = "SELECT c.nbre_pers,c.haute_saison,c.basse_saison FROM jos_content AS a, voyage_prix_gpe AS b, prix_gpe AS c "
        			." WHERE a.id = b.id_voyage AND b.id=c.id_vpg and a.id=" . $id . " ORDER BY c.nbre_pers";
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$i = 0;
			$row = array();
			while($ligne = $resultats->fetch()){
				$row[$i] = $ligne;
				$i++;
			}
			$resultats->closeCursor();
		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $row;
	}

	/**
	 * getTexteDateGroupe()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getTexteDateGroupe($id, $connexion)	{
		try{
			$query = "select a.titre,a.contenu from contenu_groupe as a,jos_content as b where a.aafficher = 1 and b.id_contenu_gpe=a.id and b.id=" . $id;
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne = $resultats->fetch();
			$resultats->closeCursor();

		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $ligne;
	}

	/**
	 * getSaison()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getSaison($id, $connexion){
		try{
			$query = "SELECT id,date_deb_hs,date_fin_hs,date_deb_bs,date_fin_bs FROM date_hs_bs where actif=1";
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne = $resultats->fetch();
			$resultats->closeCursor();

		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $ligne;
	}

	/**
	 * getSingle()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getSingle($id, $connexion)	{
		try{
			$query = "SELECT c.haute_saison,c.basse_saison FROM jos_content AS a, voyage_prix_ss AS b, prix_supplement_single AS c WHERE a.id = b.id_voyage AND b.id=c.id_vpss and a.id=" . $id;
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne = $resultats->fetch();
			$resultats->closeCursor();

		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $ligne;
	}

	/**
	 * getEnfant()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getEnfant($id, $connexion){
		try{
			$query = "SELECT c.haute_saison,c.basse_saison FROM jos_content AS a, voyage_prix_enfant AS b, prix_enfant AS c WHERE a.id = b.id_voyage AND b.id=c.id_vpe and a.id=" . $id;
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne = $resultats->fetch();
			$resultats->closeCursor();

		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $ligne;
	}

	/**
	 * getHS_BS()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getHS_BS($id, $connexion){
		try{
			$query = "SELECT id,date_deb_hs,date_fin_hs,date_deb_bs,date_fin_bs FROM date_hs_bs where actif=1";
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne = $resultats->fetch();
			$resultats->closeCursor();

		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $ligne;
	}

	/**
	 * supprimerTousP()
	 *
	 * @param mixed $txt
	 * @return
	 */
	function supprimerTousP($txt,$alaligne=true)	{
		//echo strpos($txt,'<p>');
		$txt = preg_replace('#<p>#i','',$txt);
		if($alaligne)
			$br = '<br />';
		else
			$br = '';
		$txt = preg_replace('#</p>#i',$br,$txt);
		return $txt;
	}
	function supprimerTousPAttr($txt,$alaligne=true)	{
		//echo strpos($txt,'<p>');
		$txt = preg_replace('#<p[^>]*>#i','',$txt);
		if($alaligne)
			$br = '<br />';
		else
			$br = '';
		$txt = preg_replace('#</p>#i',$br,$txt);
		return $txt;
	}
	function supprimerTousBalise($txt,$alaligne,$balise,$espaceavant = false)	{
		//echo strpos($txt,'<p>');
		if($espaceavant)
			$nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		else
			$nbsp = '';
		$txt = preg_replace('#<' . $balise . '[^>]*>#i',$nbsp,$txt);
		if($alaligne)
			$br = '<br />';
		else
			$br = '';
		$txt = preg_replace('#</' . $balise . '>#i',$br,$txt);
		return $txt;
	}
	function majSrcImg($txt){
		return preg_replace('#src="images#','src="../images',$txt);
	}

	/**
	 * getDateMesure()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getDateMesure($id, $connexion)	{
		try{
			$query = "select nbre_pers_min_mesure,prix, prix_luxe from jos_content where id=" . $id;
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne = $resultats->fetch();
			$resultats->closeCursor();

		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $ligne;
	}

	/**
	 * getTexteDateMesure()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getTexteDateMesure($id, $connexion)	{
		try{
			$query = "select a.titre,a.contenu from contenu_date_mesure as a, jos_content as b where a.aafficher = 1 and b.id_contenu_dm=a.id and b.id=" . $id;
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne = $resultats->fetch();
			$resultats->closeCursor();

		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $ligne;
	}

	/**
	 * getInfoPrix()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getInfoPrix($id, $connexion){
		try{
			$query = "select a.titre, a.contenu from info_prix as c, jos_content as b, info_prix_det as a where a.id_info_prix = b.id_info_prix and a.id_info_prix = c.id_info_prix and b.id=" . $id;
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$i = 0;
			$row = array();
			while($ligne = $resultats->fetch()){
				$row[$i] = $ligne;
				$i++;
			}
			$resultats->closeCursor();
		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $row;
	}

	/**
	 * getAutreProcInscr()
	 *
	 * @param mixed $id
	 * @param mixed $connexion
	 * @return
	 */
	function getAutreProcInscr($id, $connexion){
		try{
			$query = "select titre, contenu from autre_info_voyage order by ordre ";
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$i = 0;
			$row = array();
			while($ligne = $resultats->fetch()){
				$row[$i] = $ligne;
				$i++;
			}
			$resultats->closeCursor();
		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $row;
	}

	/**
	 * getAssurance()
	 *
	 * @param mixed $id
	 * @param mixed $contenu
	 * @return
	 */
	function getAssurance($id, $connexion)	{
		try{
			$query = "SELECT concat_ws(' ',a.introtext, a.fulltext) as texte from jos_content as a where id=39";
			$resultats=$connexion->query($query);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne = $resultats->fetch();
			$resultats->closeCursor();

		}catch(Exception $e){
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
		return $ligne;
	}

/**
 * getInfoComplementaires()
 *
 * @param mixed $id
 * @param mixed $connexion
 * @return
 */
function getInfoComplementaires($id, $connexion){
	try{
		$query = "select a.titre, a.contenu,a.sary from info_complementaire as c, jos_content as b, info_complementaire_det as a "
	    	. "where (a.onglet_aerien <> 1 or a.onglet_aerien is null) and a.id_info_comp = b.id_info_comp and a.id_info_comp = c.id_info_comp and "
	    	. " a.ordre > 0 and b.id=" . $id . " order by a.ordre";
		$resultats=$connexion->query($query);
		$resultats->setFetchMode(PDO::FETCH_OBJ);
		$i = 0;
		$row = array();
		while($ligne = $resultats->fetch()){
			$row[$i] = $ligne;
			$i++;
		}
		$resultats->closeCursor();
	}catch(Exception $e){
		echo 'Erreur : '.$e->getMessage().'<br />';
		echo 'N° : '.$e->getCode();
	}
	return $row;
}

/**
 * getVols()
 *
 * @param mixed $id
 * @param mixed $connexion
 * @return
 */
function getVols($id, $connexion){
	try{
		$query = "select a.titre, a.contenu,a.sary from info_complementaire as c, jos_content as b, info_complementaire_det as a "
    	. "where (a.onglet_aerien = 1) and a.id_info_comp = b.id_info_comp and a.id_info_comp = c.id_info_comp and "
    	. " a.ordre > 0 and b.id=" . $id . " order by a.ordre";
		$resultats=$connexion->query($query);
		$resultats->setFetchMode(PDO::FETCH_OBJ);
		$i = 0;
		$row = array();
		while($ligne = $resultats->fetch()){
			$row[$i] = $ligne;
			$i++;
		}
		$resultats->closeCursor();
	}catch(Exception $e){
		echo 'Erreur : '.$e->getMessage().'<br />';
		echo 'N° : '.$e->getCode();
	}
	return $row;
}

/**
 * doublerBR()
 *
 * @param mixed $txtAssurance
 * @return
 */
function doublerBR($txtAssurance){
	return preg_replace("#<br[^>]*>#i", "<br>", $txtAssurance);
}
function clean ($str,$replace="_")
{
	/** Mise en minuscules (chaîne utf-8 !) */
	$str = strtolower($str);
	/** Nettoyage des caractères */
	$str = utf8_encode($str);
	$str = strtr($str, array(
	'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'a'=>'a', 'a'=>'a', 'a'=>'a', 'ç'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c', 'd'=>'d', 'd'=>'d', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'g'=>'g', 'g'=>'g', 'g'=>'g', 'h'=>'h', 'h'=>'h', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', '?'=>'i', 'j'=>'j', 'k'=>'k', '?'=>'k', 'l'=>'l', 'l'=>'l', 'l'=>'l', '?'=>'l', 'l'=>'l', 'ñ'=>'n', 'n'=>'n', 'n'=>'n', 'n'=>'n', '?'=>'n', '?'=>'n', 'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'o'=>'o', 'o'=>'o', 'o'=>'o', 'œ'=>'o', 'ø'=>'o', 'r'=>'r', 'r'=>'r', 's'=>'s', 's'=>'s', 's'=>'s', 'š'=>'s', '?'=>'s', 't'=>'t', 't'=>'t', 't'=>'t', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'w'=>'w', 'ý'=>'y', 'ÿ'=>'y', 'y'=>'y', 'z'=>'z', 'z'=>'z', 'ž'=>'z'
	));
	$str = trim(preg_replace('#[^a-zA-Z0-9]+#', $replace, $str));
	return $str;
}
function enleve($str){
	$str = strtolower($str);
	/** Nettoyage des caractères */
	$str = utf8_encode($str);
	$str = strtr($str, array(
	'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'a'=>'a', 'a'=>'a', 'a'=>'a', 'ç'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c', 'd'=>'d', 'd'=>'d', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'g'=>'g', 'g'=>'g', 'g'=>'g', 'h'=>'h', 'h'=>'h', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', '?'=>'i', 'j'=>'j', 'k'=>'k', '?'=>'k', 'l'=>'l', 'l'=>'l', 'l'=>'l', '?'=>'l', 'l'=>'l', 'ñ'=>'n', 'n'=>'n', 'n'=>'n', 'n'=>'n', '?'=>'n', '?'=>'n', 'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'o'=>'o', 'o'=>'o', 'o'=>'o', 'œ'=>'o', 'ø'=>'o', 'r'=>'r', 'r'=>'r', 's'=>'s', 's'=>'s', 's'=>'s', 'š'=>'s', '?'=>'s', 't'=>'t', 't'=>'t', 't'=>'t', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'w'=>'w', 'ý'=>'y', 'ÿ'=>'y', 'y'=>'y', 'z'=>'z', 'z'=>'z', 'ž'=>'z'
	));
	return $str;
}
function replacea($str){
    return strtr($str,array('à'=>'&agrave;','À'=>'&Agrave'));
}
?>