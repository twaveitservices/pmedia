<?php
require_once('config/lang/eng.php');
require_once('tcpdf.php');
include('../configuration.php');
include("../modules/mod_date/date.class.php");
class MYPDF extends TCPDF {

	protected $titreHeader = "";
	//Page header
	public function Header() {
		// Logo
		$image_file = "../templates/mt_template/images/logo.png";
		$this->Image($image_file, 10, 0, '', 20, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 15);
		// Title
		$this->MultiCell('', 1, $this->titreHeader, 0, 'R', 0, 1, '', 10, true);
		//$this->Line(15,20,$this->getPageWidth() - 15,20);
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
	public function setTitreHeader($titreHeader){
		$this->titreHeader = $titreHeader;
	}
}
$conf = new JConfig();
try{
	$connexion = new PDO('mysql:host='.$conf->host.';port=3306;dbname='.$conf->db, $conf->user, $conf->password);
	$article = getArticle($_POST["id"],$connexion);
	$id = $_POST["id"];
	$contact = getContact($_POST["id"],$connexion);
	$contenu = $article->introtext;
	$titre = $article->title;
	$duree = $article->duree;
	$txt_accroche = utf8_encode($article->txt_accroche);
	$code_circuit = $article->code_circuit;
	$contenu = supprimerTexteEntre('<div class="avanana">', '</div>', $contenu);
	$contenu = utf8_encode($contenu);
	$titre = alaivoAnatinyBalise($contenu,"h1");
	$titre = str_replace('{loadposition module_duree}','    ' . $duree,$titre);
	$titre = strip_tags($titre);
	$contenu = supprimerTexteEntre('<h1>', '</h1>', $contenu);
//	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->setTitreHeader(utf8_encode($titre));
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Nicola Asuni');
	$pdf->SetTitle('TCPDF Example 002');
	$pdf->SetSubject('TCPDF Tutorial');
	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
	$pdf->setPrintHeader(true);
	$pdf->setPrintFooter(true);
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	//$pdf->setLanguageArray($l);
	$pdf->AddPage();
	//echo "../images/mt/fille/" . $contact->image;
	$pdf->Image("../images/mt/fille/" . $contact->image, 127, 27, 20, 24, '', '', '', true, 300, '', false, false, 0, false, false, false);
	$pdf->SetFont('times', 'B', 16);
	$pdf->MultiCell(110, 1, $titre, 0, 'J', 0, 1, '', '', true);
	$pdf->Ln(3);
	$pdf->SetFont('times', 'I', 12);
	$pdf->MultiCell(110, 1, $txt_accroche, 0, 'J', 0, 1, '', '', true);
	$pdf->SetFont('times','',8);
	$txt_contact = "<strong>Votre contact : </strong>" . $contact->prenom . "<p>"
			. $contact->texte_intro . "</p>"
			. "<strong>Tel : </strong>" . $contact->contact_eventuel . " (".$contact->horaire_ouv_bur.")<br />"
			.($contact->skype == "" ? "" : "<strong>Skype : </strong>" . $contact->skype . "");
	$txt_contact = utf8_encode($txt_contact);
	//$pdf->MultiCell(50, 1, $txt_contact, 0, 'L', 0, 1, 149, 27, true);
	$pdf->writeHTMLCell(50, 1, 149, 27, $txt_contact, '', 1, 0, true, 'L', true);
	$contenu = str_replace('{loadposition module_contact_mt}','',$contenu);
	$tmp = alaivoTexteEntreMiaraka('<img', '/>', $contenu);
	$saryCarte = alaivoSary($tmp);
	$pdf->Image("../" . $saryCarte, 135, 53, 0, 100, '', '', 'C', true, 300, '', false, false, 0, false, false, false);
	$contenu = str_replace($tmp,'',$contenu);
	$contenu = str_replace('{loadposition module_vtxtaccroche}','',$contenu);
	$texteDesc = alaivoTexteEntre('<p rel="texte_desc">','</p>',$contenu);
	$pdf->SetFont('times', '', 10);
	$pdf->Ln(3);
	$pdf->writeHTMLCell(110, 1, '', '', $texteDesc, '', 1, 0, true, 'J ', true);
	$contenu = str_replace('<p rel="texte_desc">' . $texteDesc . "</p>", "", $contenu);
	$textePF = alaivoTexteEntre('<h2>','</h2>',$contenu);
	$pdf->Ln(3);
	$pdf->SetFont("times",'B',12);
	$pdf->MultiCell(110, 1, $textePF, 0, '', 0, 1, '', '', true);
	$contenu = str_replace('<h2>' . $textePF . "</h2>", "", $contenu);
	$texteLPF = alaivoTexteEntreMiaraka('<ul>','</ul>',$contenu);
	$pdf->SetFont('times', '', 10);
	$pdf->writeHTMLCell(110, 1, '', '', $texteLPF, '', 1, 0, true, 'L', true);
	$contenu = str_replace($texteLPF,'',$contenu);
	$texteCIV = alaivoTexteEntre('<h2>','</h2>',$contenu);
	$pdf->Ln(3);
	$pdf->SetFont("times",'B',12);
	$pdf->MultiCell(110, 1, $texteCIV, 0, '', 0, 1, '', '', true);
	$contenu = str_replace("<h2>" . $texteCIV . "</h2>", "", $contenu);
	$texteLCIV = alaivoTexteEntreMiaraka('<ul>','</ul>',$contenu);
	$texteTmp= $texteLCIV;
	$pdf->SetFont('times', '', 10);
	$texteLCIV = str_replace('{loadposition module_duree}',$duree,$texteLCIV);
	$pdf->writeHTMLCell(60, 1, '', '', $texteLCIV, '', 0, 0, true, 'L', true);
	$contenu = str_replace($texteTmp,'',$contenu);
	$level = getLevel($_POST["id"],$connexion);
	$x = $pdf->GetX();
	$x += 10;
	/*$imageLevel = "<img src='../modules/mod_level/img/difficulte/" . $level->imagediff . "' />";
	$pdf->writeHTMLCell(50, 1, '', '', $imageLevel, '', 1, 0, true, 'L', true);*/
	$pdf->Image("../modules/mod_level/img/difficulte/" . $level->imagediff, $x, '', 15, 0, '', '', 'C', true, 300, '', false, false, 0, false, false, false);
	$pdf->Image("../modules/mod_level/img/level/" . $level->imagelev, $x + 15 + 4, '', 15, 0, '', '', 'C', true, 300, '', false, false, 0, false, false, false);
	$contenu = str_replace('{loadposition mod_level}','',$contenu);
	$contenu = str_replace('{loadposition mod_circuitluxe}','',$contenu);
	$pdf->Ln(3);
	$textePAVI = alaivoTexteEntre('<h2>','</h2>',$contenu);
	$pdf->SetFont("times",'B',12);
	$y = $pdf->GetY() + $pdf->getLastH();
	$pdf->MultiCell(101, 1, $textePAVI, 0, '', 0, 1, '', $y, true);
	$contenu = str_replace('<h2>' . $textePAVI . "</h2>", "", $contenu);
	$x = $pdf->GetX();
	$y = $pdf->GetY() + $pdf->getLastH();
	//echo $contenu;
	for($i = 0 ; $i < 10 ; $i++){
		$tmp = alaivoTexteEntreMiaraka('<img', '/>', $contenu);
		$saryDiapo = alaivoSary($tmp);
		$pdf->Image("../" . $saryDiapo, $x, $y, 35, 0, '', '', 'C', true, 300, '', false, false, 0, false, false, false);
		$contenu = str_replace($tmp,'',$contenu);
		if($i == 4){
			$x = $pdf->GetX();
			$y += 26;
		}else{
			$x += 37;
		}
	}
	$pdf->AddPage();
	$tab = alaivoTexteEntre('{tab=', '}', $contenu)	;
	$pdf->SetFont("times",'B',12);
	$pdf->MultiCell(101, 1, $tab, 0, '', 0, 1, '', '', true);
	$contenu = str_replace('{tab=' . $tab . '}','',$contenu);
	$em = alaivoTexteEntre('<em>','</em>',$contenu);
	$pdf->SetFont('times', 'I', 10);
	$pdf->MultiCell('', 1, $em . "\n", 0, 'J', 0, 1, '', '', true);
	$contenu = str_replace('<p><em>'.$em.'</em></p>','',$contenu);
	$row = getJourJour($id,$connexion,$pdf);
	$y = $pdf->GetY() + $pdf->getLastH();
	for ($i = 0; $i < count($row); $i++) {
		$jour = "J ";
		if((isset($row[$i +1]->numero_jour))){
			if ((($row[$i]->numero_jour + 1) == $row[$i + 1]->numero_jour)) {
				$jour .= $row[$i]->numero_jour;
			} else {
				if(isset($row[$i +1]->numero_jour)){
					$jour .= $row[$i]->numero_jour . utf8_decode(" à ") . ($row[$i + 1]->numero_jour - 1);
				}else{
					$jour .= $row[$i]->numero_jour;
				}
			}
		}else{
			$jour .= $row[$i]->numero_jour;
		}
		$ret =  $jour . ' ' . $row[$i]->titre;
		$pdf->SetFont('times', 'B', 10);
		$pdf->Ln();
		$x = $pdf->GetX();
		$x_tmp = $x;
		if($i >= 2 && (($i - 2) % 3) == 0){
			$pdf->AddPage();
			$y = 27;
		}
		$pdf->writeHTMLCell('', 1, $x, $y, '<span style="color:#EA9015">' . utf8_encode($ret) . "</span>", '', 0, 0, true, 'J', true);
		$y = $pdf->GetY() + $pdf->getLastH();
		$pdf->Image("../images/" . $row[$i]->sary, $x, $y, 25, 0, '', '', 'L', true, 300, '', false, false, 0, false, false, false);
		$x +=27;
		$pdf->SetFont('times', '', 10);
		$pdf->writeHTMLCell('', 1, $x, $y, utf8_encode($row[$i]->contenu), '', 0, 0, true, 'J', true);
		$pdf->Ln(2);
		if($row[$i]->autre_info != ""){
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp;
			$pdf->SetFont('times', 'B', 9);
			$pdf->MultiCell('', 1, "Informations spécifiques :", 0, '', 0, 1, $x, $y, true);
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp;
			$pdf->SetFont('times', '', 9);
			$pdf->writeHTMLCell('', 7, $x, $y, utf8_encode($row[$i]->autre_info), '', 0, 0, true, 'J', true);
		}
		$pdf->SetFont('times', '', 10);
		if(!is_null($row[$i]->id_aerien)){
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp;
			$row_aerien = getAerien($row[$i]->id_aerien,$connexion);
			if($row[$i]->autre_info == ""){
				$pdf->SetFont('times', 'B', 9);
				$pdf->MultiCell('', 1, "Informations spécifiques :", 0, '', 0, 1, $x, $y, true);
			}
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp;
			$pdf->SetFont('times', '', 9);
			$txt = "<p>Le vol intérieur à prévoir est le suivant : <span style=\"color:#0000ff;\"><strong>". utf8_encode($row_aerien->ville_depart)." [" . $row_aerien->iata_depart . "] > " . utf8_encode($row_aerien->ville_arrivee) . " [" . $row_aerien->iata_arrivee . "]</strong></span>";
			$txt .= '<br /><span style="font-size:8pt;"><em>NB : entre crochets [ ] se trouve le code international de l\'aéroport.</em></span></p>';
			$pdf->writeHTMLCell('', 7, $x + 5, $y, $txt, '', 0, 0, true, 'J', true);
		}
		if (($row[$i]->lunch == 1 || $row[$i]->diner == 1)){
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp;
			$repas = '<p><strong>Repas : </strong>' . ($row[$i]->lunch == 1 ? "Déjeuner inclus" : "") . (($row[$i]->lunch == 1 && $row[$i]->diner == 1) ? " / " : "") . ($row[$i]->diner == 1 ? "Dîner inclus" : "") . '</p>';
			$pdf->SetFont('times', '', 9);
			$pdf->Ln(2);
			$pdf->writeHTMLCell('', 7, $x, $y, $repas, '', 0, 0, true, 'J', true);
		}

		if ($row[$i]->distance != "" || $row[$i]->duree != ""){
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp;
			$duree = '<p>' . ($row[$i]->duree != "" ? '<strong>Durée : </strong>' . (!is_null($row[$i]->id_aerien) ? $row_aerien->duree . " de vol " : "") . $row[$i]->duree . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
				: (!is_null($row[$i]->id_aerien) ? '<strong>Durée : </strong>' . $row_aerien->duree . " de vol  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : ""))
				. ($row[$i]->distance != "" ? '<strong>Distance : </strong>' . $row[$i]->distance : "") . '</p>';
			$pdf->SetFont('times', '', 9);
			$pdf->Ln(2);
			$pdf->writeHTMLCell('', 7, $x, $y, $duree, '', 0, 0, true, 'J', true);
		}

		if($row[$i]->hotelnom != "" && utf8_encode($row[$i]->hotelnom) != "Identique au jour précédent" ){
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp;
			$pdf->Ln(4);
			$hotel = '<p><strong>Hébergement indicatif : </strong>' . utf8_encode($row[$i]->hotellieu) . ". " . utf8_encode($row[$i]->hotelnom) . '</p>';
			$pdf->SetFont('times', '', 9);
			$pdf->writeHTMLCell('', 1, $x, $y, $hotel, '', 1, 0, true, 'J', true);
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp;
			$pdf->Image("../images/" . $row[$i]->hotelsary1, $x, $y, 25, 0, '', '', 'L', true, 300, '', false, false, 0, false, false, false);
			/*$y += 20;
			$pdf->Image("../images/" . $row[$i]->hotelsary2, $x, $y, 25, 0, '', '', 'L', true, 300, '', false, false, 0, false, false, false);*/
			$y_tmp = $y + 20;
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp + 27;
			$pdf->writeHTMLCell('',1,$x,$y,utf8_encode($row[$i]->hoteldescriptif),'',0,0,true,'J',true);
			//echo $y . " ===== " .($pdf->GetY() + $pdf->getLastH()) ."<br>";
			if($y_tmp <= $pdf->GetY() + $pdf->getLastH()){
				$y = $pdf->GetY() + $pdf->getLastH();
			}else{
				$y = $y_tmp;
			}
			/*echo '<table class="table_heb_ind_'.$i.'">';
			echo "<tr><td><img src=\"" . $_baseurl . "images/" . $row[$i]->hotelsary1 . "\" alt=\"" . $row[$i]->hotelnom . "\"  width=\"---\"></td><td><img src=\"" . $_baseurl . "images/" . $row[$i]->hotelsary2 . "\" alt=\"" . $row[$i]->hotelnom . "\"  width=\"---\"></td><td rowspan='2'>" . $row[$i]->hoteldescriptif . "</td></tr>";
			echo '</table>';*/
		} elseif( utf8_encode($row[$i]->hotelnom) == "Identique au jour précédent"){
			$y = $pdf->GetY() + $pdf->getLastH();
			$x = $x_tmp;
			$hotel = '<p style="margin-top:10px;"><strong>Hébergement indicatif : </strong>'. utf8_encode($row[$i]->hotelnom) . '</p>';
			$pdf->SetFont('times', '', 9);
			$pdf->writeHTMLCell('', 1, $x, $y, $hotel, '', 0, 0, true, 'J', true);
			$y = $pdf->GetY() + $pdf->getLastH();
		}
		$x = $x_tmp;
	}
	$y = $pdf->GetY() + $pdf->getLastH();
	$x = $x_tmp;
	$contenu = str_replace('{loadposition module_jourjour}','',$contenu);
	$rmq = alaivoTexteEntre('<p rel="rmq">', '</p>', $contenu);
	$pdf->SetFont('times', 'I', 12);
	$pdf->writeHTMLCell('', 1, $x, $y, '<br><br>' . $rmq, '', 1, 0, true, 'J', true);
	$contenu = str_replace('<p rel="rmq">'. $rmq .'</p>','',$contenu);
	$tab = alaivoTexteEntre('{tab=', '}', $contenu)	;
	$pdf->SetFont("times",'B',12);
	$pdf->Ln(4);
	$pdf->MultiCell(101, 1, $tab, 0, '', 0, 1, '', '', true);
	$contenu = str_replace('{tab=' . $tab . '}','',$contenu);
	$date_det = getDateDetermine($id,$connexion);
	if(isset($date_det)){
		$txt_date_det = getTexteDateDetermine($id,$connexion);
		$pdf->Ln(4);
		$pdf->SetFont("times",'BI',10);
		$pdf->writeHTMLCell('', 1, '', '', '<span style="color:#EA9015">' . utf8_encode($txt_date_det->titre) . "</span>", '', 1, 0, true, 'J', true);
		//$pdf->MultiCell('', 1, utf8_encode($txt_date_det->titre), 0, '', 0, 1, '', '', true);
		$txt = $txt_date_det->contenu;
		$txt = str_replace("{//mt_nb_pers_date_determinees}", $date_det[0]->nbre_pers_min_fixes, $txt);
		$liste_date = "<ul>";
		for ($i = 0; $i < count($date_det); $i++) {
			$daty0 = new dateOp($date_det[$i]->date_deb, "aaaa-mm-jj");
			$daty_voalohany = $daty0->GetDate();
			$daty0->AjouteJours($date_det[$i]->duree - 1);
			$daty_farany = $daty0->GetDate();
			$liste_date .= "<li>" . $daty_voalohany . " au " . $daty_farany . "&nbsp;&nbsp; Prix : <strong> " . number_format($date_det[$i]->prix, 0, ".", " ")
				. utf8_decode(" &euro;</strong>&nbsp;&nbsp;")
				. ((!is_null($date_det[$i]->prix_enfant) && ($date_det[$i]->prix_enfant > 0)) ? "Prix enfant : <strong>"
				. number_format($date_det[$i]->prix_enfant, 0, ".", " ") . utf8_decode(" &euro;</strong>&nbsp;&nbsp;") : "")
				. ((!is_null($date_det[$i]->prix_suppl) && ($date_det[$i]->prix_suppl > 0)) ?
				utf8_decode("Prix supplément single : <strong> ") . number_format($date_det[$i]->prix_suppl, 0, ".", " ") . utf8_decode(" &euro;</strong>&nbsp;&nbsp;") : "")
				. "<span class=\"mt_right\">" . (($date_det[$i]->confirme == 1) ? utf8_decode("<span style=\"color:#3fcc5f\">Confirmé</span>&nbsp;&nbsp;") : "")
				. "<a style=\"color:#ffb100\" href=\"http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?option=com_content&view=article&id=78&id_date="
				. $date_det[$i]->id . "&id_voyage=" . $id . "\">Inscription</a></span></li>\n";
		}
		$liste_date .= "</ul>";
		$txt = str_replace("{//mt_liste_date}", $liste_date, $txt);
		$pdf->SetFont("times",'',10);
		if (!is_null($date_det[0]->prix_suppl_chb_indiv) && $date_det[0]->prix_suppl_chb_indiv > 0)
			$txt = str_replace("{//mt_supplement}", "Supplément chambre/tente  individuelle : " . number_format($date_det[0]->prix_suppl_chb_indiv, 0, ".", " ") . "&euro;", $txt);
		$txt = str_replace("{//mt_supplement}", "", $txt);
		$pdf->writeHTMLCell('', 1, '', '', utf8_encode($txt), '', 1, 0, true, 'J', true);
	}
	$date_gpe = getDateGroupe($id,$connexion);
	if(isset($date_gpe)){
		$txt_date_gpe = getTexteDateGroupe($id,$connexion);
		$pdf->SetFont("times",'BI',10);
		$pdf->writeHTMLCell('', 1, '', '', '<span style="color:#EA9015">' . utf8_encode($txt_date_gpe->titre) . "</span>", '', 1, 0, true, 'J', true);
		$txt = $txt_date_gpe->contenu;
		$txt = supprimerTousP($txt);
		for ($i = 0; $i < count($date_gpe); $i++) {
			$nb_pers[$i] = $date_gpe[$i]->nbre_pers;
			$hs[$i] = $date_gpe[$i]->haute_saison;
			$bs[$i] = $date_gpe[$i]->basse_saison;
		}
		$liste_pg = "<p><table>";
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
			$liste_pg .= "<td style=\"text-align:right;border:1px solid #999;vertical-align:middle;\">" . number_format($hs[$i], 0, ".", " ") . " &euro;</td>";
		}
		$liste_pg .= "</tr>";
		$liste_pg .= "<tr><td style=\"border:1px solid #999;vertical-align:middle;\"><strong>Prix basse saison</strong></td>";
		for ($i = 0; $i < sizeof($bs); $i++) {
			$liste_pg .= "<td style=\"text-align:right;border:1px solid #999;vertical-align:middle;\">" . number_format($bs[$i], 0, ".", " ") . " &euro;</td>";
		}
		$liste_pg .= "</tr>";
		$liste_pg .= "</table></p>";
		$txt = str_replace("{//mt_liste_prix_gpe}", $liste_pg, $txt);
		$single = getSingle($id,$connexion);
		if(isset($single)){
			$txt = str_replace("{//mt_if_sg}", "", $txt);
			$liste_sg = "<table>";
			$liste_sg .= "<tr><td><strong>Prix haute saison</strong></td><td>" . number_format($single->haute_saison) . " &euro;</td></tr>";
			$liste_sg .= "<tr><td><strong>Prix basse saison</strong></td><td>" . number_format($single->basse_saison) . " &euro;</td></tr>";
			$liste_sg .= "</table>";
			$txt = str_replace("{//mt_liste_supp_single}", $liste_sg, $txt);
			$txt = str_replace("{//mt_endif_sg}", "", $txt);
		}else{
			$txt = supprimerTexteEntre("{//mt_if_sg}","{//mt_endif_sg}",$txt);
		}
		$enfant = getEnfant($id,$connexion);
		if (isset($enfant)) {
			$txt = str_replace("{//mt_if_e}", "", $txt);
			$liste_ss = "<table>";
			$liste_ss .= "<tr><td><strong>Prix haute saison</strong></td><td>" . number_format($enfant->haute_saison) . " &euro;</td></tr>";
			$liste_ss .= "<tr><td><strong>Prix basse saison</strong></td><td>" . number_format($enfant->basse_saison) . " &euro;</td></tr>";
			$liste_ss .= "</table>";
			$txt = str_replace("{//mt_liste_prix_enfant}", $liste_ss, $txt);
			$txt = str_replace("{//mt_endif_e}", "", $txt);
		} else {
			$txt = supprimerTexteEntre("{//mt_if_e}","{//mt_endif_e}",$txt);
		}
		$btn = '<a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?option=com_content&view=article&id=78&id_voyage='.$id.'&mi=pcv">S\'inscrire au voyage</a>';
		$txt = str_replace('{//mt_bouton}', $btn, $txt);
		$hs_bs = getHS_BS($id,$connexion);
		$date_deb_hs = preg_split('/-/', $hs_bs->date_deb_hs);
		$date_fin_hs = preg_split('/-/', $hs_bs->date_fin_hs);
		$date_deb_bs = preg_split('/-/', $hs_bs->date_deb_bs);
		$date_fin_bs = preg_split('/-/', $hs_bs->date_fin_bs);
		$liste_dhb = "<p><strong>Haute saison : </strong>du " . $date_deb_hs[1] . "/" . $date_deb_hs[0] . " au " . $date_fin_hs[1] . "/" . $date_fin_hs[0] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$liste_dhb .= "<strong>Basse saison : </strong>du " . $date_deb_bs[1] . "/" . $date_deb_bs[0] . " au " . $date_fin_bs[1] . "/" . $date_fin_bs[0] . "</p>";
		$txt = str_replace("{//mt_date_hs_bs}", $liste_dhb, $txt);
		$pdf->SetFont("times",'',10);
		$pdf->writeHTMLCell('', 1, '', '', utf8_encode($txt), '', 1, 0, true, 'J', true);
	}
	$contenu = str_replace('{loadposition module_date}','',$contenu);
	$dm = getDateMesure($id,$connexion);
	if(isset($dm)){
		$txt_dm = getTexteDateMesure($id,$connexion);
		$pdf->SetFont("times",'BI',10);
		$pdf->writeHTMLCell('', 1, '', '', '<span style="color:#EA9015">' . utf8_encode($txt_dm->titre) . "</span>", '', 1, 0, true, 'J', true);
		$txt = $txt_dm->contenu;
		$txt = str_replace("{//mt_nbpers}", $dm->nbre_pers_min_mesure, $txt);
		$txt = str_replace("{//mt_prix}", number_format($dm->prix, 0, ".", " "), $txt);
		$pdf->SetFont("times",'',10);
		$pdf->writeHTMLCell('', 1, '', '', utf8_encode($txt), '', 1, 0, true, 'J', true);
	}
	$contenu = str_replace('{loadposition module_date_mesure}','',$contenu);
	$info_prix = getInfoPrix($id,$connexion);
	for($i = 0 ; $i < count($info_prix) ; $i++){
		$pdf->SetFont("times",'BI',10);
		$pdf->writeHTMLCell('', 1, '', '', '<span style="color:#EA9015">' . utf8_encode($info_prix[$i]->titre) . "</span>", '', 1, 0, true, 'J', true);
		$pdf->SetFont("times",'',10);
		$pdf->writeHTMLCell('', 1, '', '', utf8_encode($info_prix[$i]->contenu), '', 1, 0, true, 'J', true);
	}
	$contenu = str_replace('{loadposition module_info_prix}','',$contenu);
	$autre_proc_inscr = getAutreProcInscr($id,$connexion);
	for($i = 0 ; $i < count($autre_proc_inscr) ; $i++){
		$pdf->SetFont("times",'BI',10);
		$pdf->writeHTMLCell('', 1, '', '', '<span style="color:#EA9015">' . utf8_encode($autre_proc_inscr[$i]->titre) . "</span>", '', 1, 0, true, 'J', true);
		$pdf->SetFont("times",'',10);
		$pdf->writeHTMLCell('', 1, '', '', utf8_encode($autre_proc_inscr[$i]->contenu), '', 1, 0, true, 'J', true);
	}
	$contenu = str_replace('{loadposition module_autres_proc_inscr}','',$contenu);
	$tab = alaivoTexteEntre('{tab=', '}', $contenu)	;
	$pdf->SetFont("times",'B',12);
	$pdf->MultiCell(101, 1, $tab, 0, '', 0, 1, '', '', true);
	$contenu = str_replace('{tab=' . $tab . '}','',$contenu);
	$assurance = getAssurance($id,$connexion);
	$pdf->SetFont("times",'',10);
	$txtAssurance = majSrcImg($assurance->texte);
	$pdf->writeHTMLCell('', 1, '', '', utf8_encode($txt), '', 1, 0, true, 'J', true);
	//echo $jj;
	//echo $contenu;
	//$pdf->Write($h=0, $titre, $link='', $fill=0, $align='L', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
	//$pdf->SetFillColor(255, 235, 235);
	//$pdf->MultiCell(55, 60, '[FIT CELL]', 1, 'J', 1, 1, 125, 145, true, 0, false, true, 60, 'M', true);

	//Close and output PDF document
	$pdf->Output('voyage.pdf', 'I');
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
function getJourJour($id,$connexion,$pdf){
	try{
		$query = "SELECT a.titre, a.contenu, a.lunch, a.distance, a.duree, a.diner, a.numero_jour, b.lieu AS hotellieu, b.nom AS hotelnom, "
    	. " b.descriptif AS hoteldescriptif, b.sary1 AS hotelsary1, b.sary2 AS hotelsary2,a.autre_info,a.sary,a.id_aerien "
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
	function supprimerTousP($txt)	{
		//echo strpos($txt,'<p>');
		$txt = preg_replace('#<p>#','',$txt);
		$txt = preg_replace('#</p>#','',$txt);
		return $txt;
	}
	function majSrcImg($txt){
		return preg_replace('#src="images#','#src="../images#',$txt);
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
?>