<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php
include_once "class.superfpdf.php";

class Briefpapier extends SuperFPDF {
  public $doctype;
  public $doctypeOrig;

  public $logofile;
  public $sender;
  public $recipient;
  public $corrDetails;
  public $boldCorrDetails;
  public $textDetails;
  public $items;

  public $ust_befreit;

  public $barcode;
  public $firmendatenid;
  public $absender;
  public $knickfalz;
  public $projekt;
  public $filename;
  public $id;
  public $table;
  public $parameter;
  public $ausarchiv = false;
  public $waehrung;
  public $anzahlkomma;
  /** @var array */
  public $mhd = [];
  /** @var array */
  public $charge = [];
  /** @var array */
  public $sn = [];

  /** @var Application $app */
  public $app;

  /** @var array **/
  private $styleData;

  /**
   * Briefpapier constructor.
   *
   * @param Application $app
   * @param string      $projekt
   * @param array       $styleData
   */
  public function __construct($app,$projekt="", $styleData = null) {

    if($styleData != null) $this->setStyleData($styleData);
    //$orientation='P';$unit='mm';$format='A4';
    //parent::PDF_EPS($orientation,$unit,$format);
    $this->projekt = $projekt;

    $this->app=$app;
    $this->absender = '';
    $this->firmendatenid = $this->app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");
    $this->parameter = '';

    $viernachkommastellen_belege = $this->getStyleElement('viernachkommastellen_belege');
    if($viernachkommastellen_belege=='1')
    {
      $this->anzahlkomma=4;
    } else {
      $this->anzahlkomma=2;
    }

    $this->ust_spalteausblende=false;

    if($this->getStyleElement('kleinunternehmer')=='1')
    {
      $this->ust_befreit=1;
      $this->ust_spalteausblende=true;
    }


    $hintergrund = $this->getStyleElement('hintergrund');

    if(!empty(erpAPI::Ioncube_Property('isdevelopmentversion'))) {
      $this->setDevelopmentVersionBackground();
    }
    elseif($this->app->erp->BriefpapierHintergrunddisable)
    {
    }
    else if($hintergrund=='logo')
    {
      $logo = $this->app->erp->getSettingsFile('logo');
      $filename = $this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_logo.jpg';
      if ($handle = fopen($filename, 'w')) {
        fwrite($handle, $logo);
        fclose($handle);
      }
      $this->logofile = $this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_logo.jpg';
      $this->briefpapier='';
    }
    else if($hintergrund=='briefpapier')
    {
      $briefpapier = $this->app->erp->getSettingsFile('briefpapier');
      $filename = $this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_briefpapier.pdf';
      if ($handle = fopen($filename, 'w')) {
        fwrite($handle, $briefpapier);
        fclose($handle);
      }

      $this->briefpapier=$this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_briefpapier.pdf';
      $this->logofile = '';

      $briefpapier2vorhanden = $this->getStyleElement('briefpapier2vorhanden');
      $this->briefpapier2vorhanden = $briefpapier2vorhanden;
      if($briefpapier2vorhanden > 0)
      {
        $briefpapier2 = $this->app->erp->getSettingsFile('briefpapier2');
        $filename = $this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_briefpapier2.pdf';
        if ($handle = fopen($filename, 'w')) {
          fwrite($handle, $briefpapier2);
          fclose($handle);
        }

        $this->briefpapier2=$this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_briefpapier2.pdf';
      }
    }
    else {
      $this->logofile = '';
      $this->briefpapier='';
    }

    if(isset($this->app->Conf->WFtestmode) && $this->app->Conf->WFtestmode==true)
    {
      $this->briefpapier='./lib/dokumente/briefpapier_testmodus.pdf';
      $this->briefpapier2='./lib/dokumente/briefpapier_testmodus.pdf';
    }

    $this->knickfalz = $this->getStyleElement('knickfalz');
    $this->abseite2y = $this->getStyleElement('abseite2y');

    $footersichtbar = $this->getStyleElement('footersichtbar');
    if($footersichtbar==1) {
      $this->nichtsichtbar_footer = false;
    } else {
      $this->nichtsichtbar_footer = true;
    }

    $seite_von_sichtbar = $this->getStyleElement('seite_von_sichtbar');
    if($seite_von_sichtbar==1) {
      $this->seite_von_sichtbar = false;
    } else {
      $this->seite_von_sichtbar = true;
    }

    $this->seite_von_ausrichtung = $this->getStyleElement('seite_von_ausrichtung');


    $this->abstand_adresszeileoben = $this->getStyleElement('abstand_adresszeileoben');
    $this->abstand_boxrechtsoben = $this->getStyleElement('abstand_boxrechtsoben');
    $this->abstand_boxrechtsoben_lr = $this->getStyleElement('abstand_boxrechtsoben_lr');
    $this->abstand_betreffzeileoben = $this->getStyleElement('abstand_betreffzeileoben');
    $this->abstand_artikeltabelleoben = $this->getStyleElement('abstand_artikeltabelleoben');

    $this->boxausrichtung = $this->getStyleElement('boxausrichtung');

    $sichtbar = $this->getStyleElement('sichtbar');
    if($sichtbar==1) {
      $this->nichtsichtbar_zeileabsender = false;
    } else {
      $this->nichtsichtbar_zeileabsender =true;
    }


    $this->barcode_sichtbar = $this->getStyleElement('barcode');
    $this->barcode_x = $this->getStyleElement('barcode_x');
    $this->barcode_y = $this->getStyleElement('barcode_y');
    $this->barcode_x_header = $this->getStyleElement('barcode_x_header');
    $this->barcode_y_header = $this->getStyleElement('barcode_y_header');

    $projekt = $this->projekt;


    $this->waehrung=$this->getStyleElement('waehrung');



    // kann man herausfinden was fuer ein projekt angegeben ist???
    $speziallieferschein = $this->app->DB->Select("SELECT speziallieferschein FROM projekt WHERE id='$projekt' LIMIT 1");
    $speziallieferscheinbeschriftung = $this->app->DB->Select("SELECT speziallieferscheinbeschriftung FROM projekt WHERE id='$projekt' LIMIT 1");
    $eigenesteuer = $this->app->DB->Select("SELECT eigenesteuer FROM projekt WHERE id='$projekt' LIMIT 1");
    if($eigenesteuer=='1'){
      $this->waehrung = $this->app->DB->Select("SELECT waehrung FROM projekt WHERE id='$projekt' LIMIT 1");
    }
    if($speziallieferschein>0)
    {
      if(!$this->app->erp->BriefpapierHintergrunddisable)
      {
        $seite1 = $this->app->DB->Select("SELECT ds.datei FROM datei_stichwoerter ds INNER JOIN datei d ON d.id=ds.datei AND IFNULL(d.geloescht,0)=0  WHERE ds.subjekt LIKE 'Briefpapier1' AND ds.objekt LIKE 'Projekt' AND ds.parameter='$projekt' LIMIT 1");
        $seite2 = $this->app->DB->Select("SELECT ds.datei FROM datei_stichwoerter ds INNER JOIN datei d ON d.id=ds.datei AND IFNULL(d.geloescht,0)=0  WHERE ds.subjekt LIKE 'Briefpapier2' AND ds.objekt LIKE 'Projekt' AND ds.parameter='$projekt' LIMIT 1");
      }

      if(!empty($seite1) && is_file($this->app->erp->GetDateiPfad($seite1))){
        $this->briefpapier = $this->app->erp->GetDateiPfad($seite1);
      }
      if(!empty($seite2) && is_file($this->app->erp->GetDateiPfad($seite2))){
        $this->briefpapier2 = $this->app->erp->GetDateiPfad($seite2);
      }

      if($seite2>0 && is_file($this->app->erp->GetDateiPfad($seite2))){
        $this->briefpapier2vorhanden = 1;
      }

      $this->logofile = '';

      if($speziallieferscheinbeschriftung!=1){
        $this->nichtsichtbar_zeileabsender = true;
        $this->nichtsichtbar_footer = true;
        $this->nichtsichtbar_rechtsoben = true;
      }
    }

    $this->nichtsichtbar_rechtsoben = true;
    $this->nichtsichtbar_summe = false;
    $this->nichtsichtbar_box=false;
    $this->nichtsichtbar_empfaenger=false;
  }

  /**
   * set Development-watermark as pdf-background
   *
   * @return void
   */
  protected function setDevelopmentVersionBackground(): void
  {
    $this->briefpapier = $this->app->erp->GetTMP().'development_version.pdf';
    $this->briefpapier2 = '';
    $this->logofile = '';
    if(is_file($this->briefpapier)) {
      return;
    }
    $waterMarkPdf = new SuperFPDF('P', 'mm', 'A4');
    $waterMarkPdf->filename = $this->briefpapier;
    $waterMarkPdf->AddPage();
    $waterMarkPdf->SetTextColor(200);
    $waterMarkPdf->SetFont(
      ($this->app->erp->Firmendaten('schriftart') ? $this->app->erp->Firmendaten('schriftart') : 'Arial'),
      'B',
      45
    );
    $waterMarkPdf->Rotate(45, 45, 180);
    $waterMarkPdf->Text(45, 180, 'DEVELOPMENT VERSION');
    $waterMarkPdf->Rotate(0);
    $waterMarkPdf->SetTextColor(0);
    file_put_contents($waterMarkPdf->filename, $waterMarkPdf->displayAnhaenge('S'));
  }

  /**
   * @param string $language
   *
   * @return string
   */
  public function getLanguageCodeFrom($language) {
    if(empty($language)) {
      return '';
    }
    $languages = $this->app->DB->SelectArr("SELECT * FROM sprachen WHERE aktiv = 1 AND iso <> ''");
    if(empty($languages)) {
      return '';
    }
    foreach($languages as $languagesRow) {
      if(in_array(
        $language,
        [
          $languagesRow['iso'],
          $languagesRow['alias'],
          $languagesRow['bezeichnung_de'],
          $languagesRow['bezeichnung_en'
          ]
        ]
      )) {
        return $languagesRow['iso'];
      }
    }

    return '';
  }

  public function GetChargeMHDSNString($type,$doctype,$doctypeid,$posid, $returnSimpleString = false)
  {
    $lieferschein_posid = 0;
    $auftrag_position_id = 0;
    $lieferschein = 0;
    if($doctype === 'rechnung'){
      $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM rechnung WHERE id = '$doctypeid' LIMIT 1");
    }
    if($doctype === 'rechnung' && !$lieferschein){
      $lieferschein = $this->app->DB->Select("SELECT id FROM lieferschein WHERE rechnungid = '$doctypeid' LIMIT 1");
    }
    if($doctype === 'rechnung' && !$lieferschein)
    {
      $auftrag = $this->app->DB->Select("SELECT auftragid FROM rechnung WHERE id = '$doctypeid' LIMIT 1");
      if($auftrag){
        $lieferschein = $this->app->DB->Select("SELECT id FROM lieferschein WHERE auftragid = '$auftrag' LIMIT 1");
      }
    }
    if($doctype === 'lieferschein'){
      $lieferschein_posid = $posid;
    }
    if($doctype === 'rechnung'){
      $auftrag_position_id = $this->app->DB->Select("SELECT auftrag_position_id FROM rechnung_position WHERE id='$posid'");
    }
    if($doctype === 'gutschrift'){
      $auftrag_position_id = $this->app->DB->Select("SELECT gutschrift_position_id FROM rechnung_position WHERE id='$posid'");
    }
    if($doctype === 'rechnung')
    {
      if(!empty($auftrag_position_id) && $auftrag_position_id > 0){
        $lieferschein_posid = $this->app->DB->Select("SELECT id FROM lieferschein_position WHERE auftrag_position_id='$auftrag_position_id' AND auftrag_position_id <> 0 ORDER BY lieferschein = '$lieferschein' DESC LIMIT 1");
      }
      if(!$lieferschein_posid)
      {
        $lieferschein_posarr = $this->app->DB->SelectArr("SELECT lieferschein_position_id FROM `sammelrechnung_position` WHERE rechnung_position_id = '$posid' AND lieferschein_position_id <> 0 AND menge <> 0");
        if(!empty($lieferschein_posarr))
        {
          $lieferschein_posid = null;
          foreach($lieferschein_posarr as $v)
          {
            $lieferschein_posid[] = $v['lieferschein_position_id'];
          }
          if(!empty($lieferschein_posid) && count($lieferschein_posid) === 1)
          {
            $lieferschein_posid = reset($lieferschein_posid);
            if($lieferschein_posid){
              $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM lieferschein_position WHERE id = '$lieferschein_posid' LIMIT 1");
            }
          }
        }
      }
    }else{
      if(!empty($auftrag_position_id) && $auftrag_position_id > 0){
        $lieferschein_posid = $this->app->DB->Select("SELECT id FROM lieferschein_position WHERE auftrag_position_id='$auftrag_position_id' AND auftrag_position_id <> 0 LIMIT 1");
      }
    }
    if($type === 'sn' && ($doctype === 'lieferschein' || $doctype === 'rechnung' || $doctype === 'gutschrift'))
    {
      if(!empty($tmp))
      {
        $ctmp = count($tmp);
        for($i=0;$i<$ctmp;$i++)
        {
          $tmp_string[]=$tmp[$i]['seriennummer'];
        }
      }
    }

    $belegPosCharge = $this->getStyleElement('beleg_pos_charge');
    $belegPosMhd = $this->getStyleElement('beleg_pos_mhd');
    if($returnSimpleString) {
      $belegPosCharge = 1;
      $belegPosMhd = 1;
    }
    if($belegPosCharge=='1'
      && $belegPosMhd =='1') {
      if($type === 'mhd')
      {
        $tmp = $this->app->DB->SelectArr("SELECT SUM(menge) as menge,wert, wert2 ,type2
        FROM beleg_chargesnmhd 
        WHERE type='$type' AND doctype='$doctype' AND doctypeid='$doctypeid' AND pos='$posid' 
        GROUP by wert,wert2");
        if(empty($tmp) && $doctype === 'rechnung' && $lieferschein && $lieferschein_posid)
        {
          if(is_array($lieferschein_posid))
          {
            $tmp = $this->app->DB->SelectArr("SELECT SUM(menge) as menge,wert, wert2 ,type2
            FROM beleg_chargesnmhd 
            WHERE type='$type' AND doctype='lieferschein' AND pos in (".implode(', ',$lieferschein_posid ).")
            GROUP by wert,wert2");
          }else{
            $tmp = $this->app->DB->SelectArr("SELECT SUM(menge) as menge,wert, wert2 ,type2
            FROM beleg_chargesnmhd 
            WHERE type='$type' AND doctype='lieferschein' AND doctypeid='$lieferschein' AND pos='$lieferschein_posid' 
            GROUP by wert,wert2");
          }
        }

      }elseif($type === 'charge')
      {
        if($returnSimpleString) {
          $tmp = $this->app->DB->SelectArr("SELECT SUM(bc1.menge) as menge,bc1.wert
          FROM beleg_chargesnmhd bc1
          WHERE bc1.type='$type' AND bc1.doctype='$doctype' AND bc1.doctypeid='$doctypeid' AND bc1.pos='$posid'
          GROUP by bc1.wert");
        }
        else{
          $tmp = $this->app->DB->SelectArr("SELECT SUM(bc1.menge) as menge,bc1.wert
          FROM beleg_chargesnmhd bc1
          LEFT JOIN beleg_chargesnmhd bc2 
          ON bc1.doctype = bc2.doctype AND bc1.doctypeid = bc2.doctypeid AND bc1.pos = bc2.pos AND bc1.lagerplatz = bc2.lagerplatz
          AND bc2.type = 'mhd' AND bc2.type2 = 'charge' AND bc2.wert2 = bc1.wert
          WHERE bc1.type='$type' AND bc1.doctype='$doctype' AND bc1.doctypeid='$doctypeid' AND bc1.pos='$posid' AND isnull(bc2.id)
          GROUP by bc1.wert");
        }
        if(empty($tmp) && $doctype === 'rechnung' && $lieferschein && $lieferschein_posid)
        {
          if($returnSimpleString) {
            if(is_array($lieferschein_posid)){
              $tmp = $this->app->DB->SelectArr("SELECT SUM(bc1.menge) as menge,bc1.wert
              FROM beleg_chargesnmhd bc1
              WHERE bc1.type='$type' AND bc1.doctype='lieferschein' AND  
                    bc1.pos in ('" . implode(', ', $lieferschein_posid) . "') 
              GROUP by bc1.wert");
            }else{
              $tmp = $this->app->DB->SelectArr("SELECT SUM(bc1.menge) as menge,bc1.wert
              FROM beleg_chargesnmhd bc1
              WHERE bc1.type='$type' AND bc1.doctype='lieferschein' AND bc1.doctypeid='$lieferschein' 
              AND bc1.pos='$lieferschein_posid'
              GROUP by bc1.wert");
            }
          }
          else{
            if(is_array($lieferschein_posid)){
              $tmp = $this->app->DB->SelectArr("SELECT SUM(bc1.menge) as menge,bc1.wert
              FROM beleg_chargesnmhd bc1
              LEFT JOIN beleg_chargesnmhd bc2 
              ON bc1.doctype = bc2.doctype AND bc1.doctypeid = bc2.doctypeid AND bc1.pos = bc2.pos AND bc1.lagerplatz = bc2.lagerplatz
              AND bc2.type = 'mhd' AND bc2.type2 = 'charge' AND bc2.wert2 = bc1.wert
              WHERE bc1.type='$type' AND bc1.doctype='lieferschein' AND  bc1.pos in ('" . implode(', ', $lieferschein_posid) . "') AND isnull(bc2.id)
              GROUP by bc1.wert");
            }else{
                $tmp = $this->app->DB->SelectArr("SELECT SUM(bc1.menge) as menge,bc1.wert
              FROM beleg_chargesnmhd bc1
              LEFT JOIN beleg_chargesnmhd bc2 
              ON bc1.doctype = bc2.doctype AND bc1.doctypeid = bc2.doctypeid AND bc1.pos = bc2.pos AND bc1.lagerplatz = bc2.lagerplatz
              AND bc2.type = 'mhd' AND bc2.type2 = 'charge' AND bc2.wert2 = bc1.wert
              WHERE bc1.type='$type' AND bc1.doctype='lieferschein' AND bc1.doctypeid='$lieferschein' AND bc1.pos='$lieferschein_posid' AND isnull(bc2.id)
              GROUP by bc1.wert");
            }
          }
        }
      }else{
        $tmp = $this->app->DB->SelectArr("SELECT SUM(menge) as menge,wert
        FROM beleg_chargesnmhd 
        WHERE type='$type' AND doctype='$doctype' AND doctypeid='$doctypeid' AND pos='$posid' 
        GROUP by wert");
        if(empty($tmp) && $lieferschein && $lieferschein_posid)
        {
          if(is_array($lieferschein_posid))
          {
            $tmp = $this->app->DB->SelectArr("SELECT SUM(menge) as menge,wert
            FROM beleg_chargesnmhd 
            WHERE type='$type' AND doctype='lieferschein'  AND pos in ('".implode(', ',$lieferschein_posid )."')
            GROUP by wert");
          }else{
            $tmp = $this->app->DB->SelectArr("SELECT SUM(menge) as menge,wert
            FROM beleg_chargesnmhd 
            WHERE type='$type' AND doctype='lieferschein' AND doctypeid='$lieferschein' AND pos='$lieferschein_posid' 
            GROUP by wert");
          }
        }
      }
      if(!empty($tmp)){
        $ctmp = count($tmp);
        for ($i = 0; $i < $ctmp; $i++) {
          if($type === 'mhd' && $tmp[$i]['wert'] != ""){
            $tmp[$i]['wert'] = ($i > 0 ? $this->app->erp->Beschriftung('dokument_mhd') . ': ' : '') . date('d.m.Y', strtotime($tmp[$i]['wert']));
            if($tmp[$i]['wert2'] != '' && $tmp[$i]['type2'] === 'charge') {
              $tmp[$i]['wert'] .= ' ' . $this->app->erp->Beschriftung('dokument_charge') . ': ' . $tmp[$i]['wert2'];
            }
          }elseif($type === 'charge'){
            if($i > 0) {
              $tmp[$i]['wert'] = $this->app->erp->Beschriftung('dokument_charge') . ': ' . $tmp[$i]['wert'];
            }
          }elseif($type === 'sn'){
            if($i > 0) {
              $tmp[$i]['wert'] = $this->app->erp->Beschriftung('dokument_seriennummer') . ': ' . $tmp[$i]['wert'];
            }
          }
          if($tmp[$i]['menge'] > 1) {
            $tmp[$i]['menge'] = ' (' . (float)$tmp[$i]['menge'] . ')';
          }else {
            $tmp[$i]['menge'] = '';
          }
          $tmp_string[] = $tmp[$i]['wert'] . $tmp[$i]['menge'];
        }
      }
    }else{
      $tmp = $this->app->DB->SelectArr("SELECT SUM(menge) as menge,wert, wert2 
      FROM beleg_chargesnmhd 
      WHERE type='$type' AND doctype='$doctype' AND doctypeid='$doctypeid' AND pos='$posid' 
      GROUP by wert");
      if(empty($tmp) && $doctype === 'rechnung' && $lieferschein && $lieferschein_posid)
      {
        if(is_array($lieferschein_posid))
        {
          $tmp = $this->app->DB->SelectArr("SELECT SUM(menge) as menge,wert, wert2 
          FROM beleg_chargesnmhd 
          WHERE type='$type' AND doctype='lieferschein' AND pos in ('".implode(', ',$lieferschein_posid )."')
          GROUP by wert");
        }else{
          $tmp = $this->app->DB->SelectArr("SELECT SUM(menge) as menge,wert, wert2 
          FROM beleg_chargesnmhd 
          WHERE type='$type' AND doctype='lieferschein' AND doctypeid='$lieferschein' AND pos='$lieferschein_posid' 
          GROUP by wert");
        }
      }

      if(!empty($tmp)){
        $ctmp = count($tmp);
        for ($i = 0; $i < $ctmp; $i++) {
          if($type === 'mhd' && $tmp[$i]['wert'] != ''){
            $tmp[$i]['wert'] =
              ($i > 0 && !$returnSimpleString ? $this->app->erp->Beschriftung('dokument_mhd') . ': ' : '') .
              date('d.m.Y', strtotime($tmp[$i]['wert']));
          }
          elseif($type === 'charge'){
            if($i > 0) {
              $tmp[$i]['wert'] = (!$returnSimpleString?$this->app->erp->Beschriftung('dokument_charge') . ': ':'') . $tmp[$i]['wert'];
            }
          }
          elseif($type === 'sn'){
            if($i > 0) {
              $tmp[$i]['wert'] = (!$returnSimpleString?$this->app->erp->Beschriftung('dokument_seriennummer') . ': ':'') . $tmp[$i]['wert'];
            }
          }
          if($tmp[$i]['menge'] > 1) {
            $tmp[$i]['menge'] = ' (' . (float)$tmp[$i]['menge'] . ')';
          }
          else {
            $tmp[$i]['menge'] = '';
          }
          $tmp_string[] = $tmp[$i]['wert'] . $tmp[$i]['menge'];
        }
      }
    }

    if(!empty($tmp_string)){
      if($returnSimpleString) {
        return implode(', ', $tmp_string);
      }
      return implode("\r\n",$tmp_string);
    }
    return '';
  }

  function CheckPosition($value,$doctype,$doctypeid,$posid)
  {
    // ean
    if($this->getStyleElement('beleg_pos_ean')=='1')
    {
      $ean = $this->app->DB->Select("SELECT ean FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      if($ean!='') {
        $tmpvalue['ean'] = $this->app->erp->Beschriftung('dokument_ean').': '.$ean;
      }
    }

    // zolltarif // check ust id
    $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM $doctype WHERE id='$doctypeid' LIMIT 1");
    if(($ust_befreit==2 && ($doctype=='rechnung' || $doctype=='gutschrift')) ||
      ($doctype!='proformarechnung' && $this->getStyleElement('beleg_pos_zolltarifnummer')=='1'))
    {
      if($value['zolltarifnummer']=='' || $value['zolltarifnummer']==0) {
        $value['zolltarifnummer'] = $this->app->DB->Select("SELECT zolltarifnummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      }
      if($value['herkunftsland']=='' || $value['herkunftsland']==0) {
        $value['herkunftsland'] = $this->app->DB->Select("SELECT herkunftsland FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      }
    } else {
      $value['zolltarifnummer']='';
      $value['herkunftsland']='';
    }

    if($this->getStyleElement('beleg_pos_herkunftsland')=='1')
    {
      $value['herkunftsland']='';
    }

    $this->mhd[$doctype][$doctypeid][$posid] = $this->GetChargeMHDSNString('mhd',$doctype,$doctypeid,$posid);
    // mhd
    if($this->getStyleElement('beleg_pos_mhd')=='1')
    {
      $mhd=$this->mhd[$doctype][$doctypeid][$posid];//$this->GetChargeMHDSNString('mhd',$doctype,$doctypeid,$posid);
      if(strpos($value['beschreibung'], '{MHD}') !== false) {
        $value['beschreibung'] = str_replace(
          '{MHD}',
          $mhd,
          $value['beschreibung']
        );
      }
      if($mhd!='') {
        $tmpvalue['mhd'] = $this->app->erp->Beschriftung('dokument_mhd').': '.$mhd;
      }
    }

    $this->charge[$doctype][$doctypeid][$posid] = $this->GetChargeMHDSNString('charge',$doctype,$doctypeid,$posid);
    // charge
    if($this->getStyleElement('beleg_pos_charge')=='1')
    {
      $charge=$this->charge[$doctype][$doctypeid][$posid];
      if(strpos($value['beschreibung'], '{CHARGE}') !== false) {
        $value['beschreibung'] = str_replace(
          '{CHARGE}',
          $charge,
          $value['beschreibung']
        );
      }
      if($charge!='') {
        $tmpvalue['charge'] = $this->app->erp->Beschriftung('dokument_charge').': '.$charge;
      }
    }

    $this->sn[$doctype][$doctypeid][$posid] = $this->GetChargeMHDSNString('sn',$doctype,$doctypeid,$posid);
    // sn
    if($this->getStyleElement('beleg_pos_sn')=='1')
    {
      $seriennr=$this->sn[$doctype][$doctypeid][$posid];
      if(strpos($value['beschreibung'], '{SN}') !== false) {
        $value['beschreibung'] = str_replace(
          '{SN}',
          $seriennr,
          $value['beschreibung']
        );
      }
      if($seriennr!='') {
        $tmpvalue['sn'] = $this->app->erp->Beschriftung('dokument_seriennummer').': '.$seriennr;
        $value['seriennummer']='';
      }
    }

    if(!empty($tmpvalue)) {
      if($value['beschreibung']!=''){
        $value['beschreibung'] = $value['beschreibung'] . "\r\n" . implode("\r\n", $tmpvalue);
      }
      else{
        $value['beschreibung'] = implode("\r\n", $tmpvalue);
      }
    }
    return $value;
  }




  public function addItem($rdata){
    // add rabatt
    if($rdata['price']!='-'){
      if($rdata['rabatt'] == 100){
        $rdata['tprice'] = round($rdata['amount'] * ((double)$rdata['price'] - (double)($rdata['price'] / 100.00 * (double)$rdata['rabatt'])), 13);
      }else{
        $rdata['tprice'] = $rdata['amount'] * ((double)$rdata['price'] - (double)($rdata['price'] / 100.00 * (double)$rdata['rabatt']));
      }
    }
    else {
      $rdata['tprice']='-';
    }
    $this->items[]=$rdata;
  }

  public function setSender($rdata){
    $this->sender['enterprise']   = $this->app->erp->ReadyForPDF($rdata[0]);
    $this->sender['firstname']     = $this->app->erp->ReadyForPDF($rdata[1]);
    $this->sender['familyname']   = $this->app->erp->ReadyForPDF($rdata[2]);
    $this->sender['address1']     = $this->app->erp->ReadyForPDF($rdata[3]);
    $this->sender['areacode']     = $this->app->erp->ReadyForPDF($rdata[4]);
    $this->sender['city']         = $this->app->erp->ReadyForPDF($rdata[5]);
    if(isset($rdata[6])){
      $this->sender['country'] = $this->app->erp->ReadyForPDF($rdata[6]);
    }
  }

  function setRecipientRechnung($id)
  {


  }


  function setRecipientLieferadresse($id,$table)
  {
    $this->id = $id;
    $this->table = $table;

    if($table == 'serviceauftrag'){
      $adressid = $this->app->DB->Select("SELECT adresse FROM serviceauftrag WHERE id = '$id' LIMIT 1");
      if($adressid != "" && $adressid > 0){
        $tmp = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adressid' LIMIT 1");
      }
    }else{
      $tmp = $this->app->DB->SelectArr("SELECT * FROM $table WHERE id='$id' LIMIT 1");
    }

    $titelansprechpartner="";
    $titelname="";

    if($tmp[0]['ansprechpartner']!='' && $tmp[0]['titel']!='')
    {
      $titelansprechpartner = $tmp[0]['titel'].' ';
    }
    else if ($tmp[0]['titel']!='')  {
      $titelname = $tmp[0]['titel'].' ';
    }

    if($table != 'serviceauftrag'){
      if($tmp[0]['typ']==''){
        $tmp[0]['typ'] = $this->app->DB->Select("SELECT typ FROM adresse WHERE id='" . $tmp[0]['adresse'] . "' LIMIT 1");
      }
    }

    $check = $this->app->DB->Select("SELECT bezeichnung FROM adresse_typ WHERE type='".$tmp[0]['typ']."' LIMIT 1");

    if($check!="")
      $this->recipient['anrede'] = $this->app->erp->ReadyForPDF($check);
    else
      $this->recipient['anrede'] = $this->app->erp->ReadyForPDF(ucfirst($tmp[0]['typ']));


    if($tmp[0]['typ']!="person")
    {
      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($titelname.$tmp[0]['name']);


      if($tmp[0]['abteilung']!='' && strlen($tmp[0]['abteilung']) >1){
        $this->recipient['address2'] = $this->app->erp->ReadyForPDF($tmp[0]['abteilung']);
      }

      if($tmp[0]['ansprechpartner']!='' && strlen($tmp[0]['ansprechpartner'])>1){
        $this->recipient['firstname'] = $this->app->erp->ReadyForPDF($titelansprechpartner . $tmp[0]['ansprechpartner']);
      }

      if(($table=='rechnung' || $table=='gutschrift') && $this->getStyleElement('rechnung_gutschrift_ansprechpartner')!='1'){
        $this->recipient['firstname'] = '';
      }
      else if(($table=='angebot' || $table=='auftrag' || $table=='bestellung') && $this->getStyleElement('angebot_auftrag_bestellung_ansprechpartner')!='1'){
        $this->recipient['firstname'] = '';
      }


      if($tmp[0]['unterabteilung']!=''){
        $this->recipient['address3'] = $this->app->erp->ReadyForPDF($tmp[0]['unterabteilung']);
      }

      if($tmp[0]['adresszusatz']!=''){
        $this->recipient['address4'] = $this->app->erp->ReadyForPDF($tmp[0]['adresszusatz']);
      }

    }

    else {
      $vorname = '';
      if(isset($tmp[0]['vorname']) && $tmp[0]['vorname']!='' && strlen(trim($tmp[0]['vorname']))>0){
        $vorname = $tmp[0]['vorname'] . ' ';
      }

      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($vorname.$tmp[0]['name']);
      $this->recipient['address2']   = $this->app->erp->ReadyForPDF($tmp[0]['adresszusatz']);
    }


    $this->recipient['address1']     = $this->app->erp->ReadyForPDF($tmp[0]['strasse']);
    $this->recipient['areacode']     = $this->app->erp->ReadyForPDF($tmp[0]['plz']);
    $this->recipient['city']         = $this->app->erp->ReadyForPDF($tmp[0]['ort']);
    //if($this->recipient['city']!="")
    $this->recipient['country']      = $this->app->erp->ReadyForPDF($tmp[0]['land']);
  }


  function setRecipientDB($adresse)
  {
    $tmp = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");
    if($tmp[0]['typ']!="person")
    {
      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($tmp[0]['name']);

      if($tmp[0]['abteilung']!='' && strlen($tmp[0]['abteilung'])>1){
        $this->recipient['address2'] = $this->app->erp->ReadyForPDF($tmp[0]['abteilung']);
      }


      $tmp[0]['anrede'] = $this->app->DB->Select("SELECT anrede FROM adresse WHERE id='".$tmp[0]['id']."' LIMIT 1");
      $check = $this->app->DB->Select("SELECT bezeichnung FROM adresse_typ WHERE type='".$tmp[0]['anrede']."' LIMIT 1");

      if($check!=''){
        $this->recipient['anrede'] = $this->app->erp->ReadyForPDF($check);
      }
      else{
        $this->recipient['anrede'] = $this->app->erp->ReadyForPDF(ucfirst($tmp[0]['anrede']));
      }


      if(($this->table=='rechnung' || $this->table=='gutschrift') && $this->getStyleElement('rechnung_gutschrift_ansprechpartner')=='1')
      {
        if($tmp[0]['ansprechpartner']!=''){
          $this->recipient['firstname'] = $this->app->erp->ReadyForPDF($tmp[0]['ansprechpartner']);
        }
      }
      else if(($this->table=='angebot' || $this->table=='auftrag' || $this->table=='bestellung') && $this->getStyleElement("angebot_auftrag_bestellung_ansprechpartner")=='1')
      {
        if($tmp[0]['ansprechpartner']!=''){
          $this->recipient['firstname'] = $this->app->erp->ReadyForPDF($tmp[0]['ansprechpartner']);
        }
      }


      if($tmp[0]['unterabteilung']!=''){
        $this->recipient['address3'] = $this->app->erp->ReadyForPDF($tmp[0]['unterabteilung']);
      }

      if($tmp[0]['adresszusatz']!=''){
        $this->recipient['address4'] = $this->app->erp->ReadyForPDF($tmp[0]['adresszusatz']);
      }

    }

    else {
      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($tmp[0]['name']);
      $this->recipient['address2']   = $this->app->erp->ReadyForPDF($tmp[0]['adresszusatz']);
    }


    $this->recipient['address1']     = $this->app->erp->ReadyForPDF($tmp[0]['strasse']);
    $this->recipient['areacode']     = $this->app->erp->ReadyForPDF($tmp[0]['plz']);
    $this->recipient['city']         = $this->app->erp->ReadyForPDF($tmp[0]['ort']);
    //if($this->recipient['city']!="")
    $this->recipient['country']      = $this->app->erp->ReadyForPDF($tmp[0]['land']);
  }


  public function setRecipient($rdata){
    $this->recipient['enterprise']   = $this->app->erp->ReadyForPDF($rdata[0]);
    $this->recipient['firstname']   = $this->app->erp->ReadyForPDF($rdata[1]);
    $this->recipient['familyname']   = $this->app->erp->ReadyForPDF($rdata[2]);
    $this->recipient['address1']     = $this->app->erp->ReadyForPDF($rdata[3]);
    $this->recipient['areacode']     = $this->app->erp->ReadyForPDF($rdata[4]);
    $this->recipient['city']         = $this->app->erp->ReadyForPDF($rdata[5]);
    if(isset($rdata[3]))$this->recipient['country'] = $this->app->erp->ReadyForPDF($rdata[6]);
  }

  public function setCorrDetails($rdata, $onlyData = false){
    if($onlyData) {
      $this->corrDetails = $rdata;
      return;
    }

    if($this->getStyleElement("projektnummerimdokument")=='1'){
      $rdata[$this->app->erp->Beschriftung("dokument_projekt")] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='" . $this->projekt . "' LIMIT 1");
    }

    if($this->getStyleElement("internetnummerimbeleg")=='1')
    {
      switch($this->doctype)
      {
        case "rechnung":
        case "lieferschein":
          $internetnummer = $this->app->DB->Select("SELECT a.internet FROM auftrag a LEFT JOIN ".$this->doctype." b ON b.auftragid=a.id WHERE b.id='".$this->doctypeid."' LIMIT 1");
        break;
        case "gutschrift":
          $internetnummer = $this->app->DB->Select("SELECT a.internet FROM gutschrift g LEFT JOIN rechnung r ON r.id=g.rechnungid LEFT JOIN auftrag a ON a.id=r.auftragid WHERE g.id='".$this->doctypeid."' LIMIT 1");
        break;
        case "auftrag":
          $internetnummer = $this->app->DB->Select("SELECT internet FROM auftrag WHERE id='".$this->doctypeid."' LIMIT 1");
        break;
        default:
          $internetnummer = '';

      }
      //$rdata[$this->getStyleElement("beschriftunginternetnummer")]=$internetnummer;
      $rdata[$this->app->erp->Beschriftung("beschriftunginternetnummer")]=$internetnummer;
    }
    if($this->doctype=="rechnung"||$this->doctype=="lieferschein"||$this->doctype=="gutschrift"||$this->doctype=="auftrag"||$this->doctype=="angebot"||$this->doctype=="lieferschein")
    {
        if($this->doctype=='lieferschein'){
          $tabelle='lieferschein';
        }else {
          $tabelle=$this->doctype;
        }
//      $bearbeiteremail = $this->app->DB->Select("SELECT b.email FROM ".$this->doctype." a LEFT JOIN adresse adr ON a.adresse=adr.id LEFT JOIN adresse b ON b.id=adr.innendienst WHERE a.id='".$this->doctypeid."' LIMIT 1");
 //     $bearbeitertelefon = $this->app->DB->Select("SELECT b.telefon FROM ".$this->doctype." a LEFT JOIN adresse adr ON a.adresse=adr.id LEFT JOIN adresse b ON b.id=adr.innendienst WHERE a.id='".$this->doctypeid."' LIMIT 1");

      $bearbeiteremail = $this->app->DB->Select("SELECT b.email FROM ".$tabelle." a  LEFT JOIN adresse b ON b.id=a.bearbeiterid WHERE a.id='".$this->doctypeid."' LIMIT 1");
      $bearbeitertelefon = $this->app->DB->Select("SELECT b.telefon FROM ".$tabelle." a  LEFT JOIN adresse b ON b.id=a.bearbeiterid WHERE a.id='".$this->doctypeid."' LIMIT 1");
    }

    if($this->getStyleElement('bearbeiteremailimdokument')=='1'){
      $rdata[$this->app->erp->Beschriftung('dokument_email')] = $bearbeiteremail;
    }
    if($this->getStyleElement('bearbeitertelefonimdokument')=='1'){
      $rdata[$this->app->erp->Beschriftung('dokument_telefon')] = $bearbeitertelefon;
    }


    //$rdata[$this->getStyleElement("beschriftungbearbeiteremail")]=$internetnummer;
    //$rdata[$this->getStyleElement("beschriftungbearbeitertelefonnummer")]=$internetnummer;

    $this->corrDetails = $rdata;
  }
  public function setBoldCorrDetails($rdata){
    $this->boldCorrDetails = $rdata;
  }

  public function setItalicBoldCorrDetails($rdata){
    $this->italicBoldCorrDetails = $rdata;
  }

  public function setItalicCorrDetails($rdata){
    $this->italicCorrDetails = $rdata;
  }

  public function setTextDetails($rdata){
    $this->textDetails = $rdata;
  }


  public function setTotals($rdata){
    $this->totals   = $rdata;
  }

  // Briefpapier festlegen
  public function setStationery($stationeryfile) {
    $this->setSourceFile($stationeryfile);
    $tplidx = $this->ImportPage(1);
    $this->useTemplate($tplidx);
  }
  /*
     public function setLogo($logofile) {
     $this->logofile = "./lib/pdf/images/".$logofile;
     }
   */

  // label settings
  public function setBarcode($barcode) {
    $barcode = preg_replace('/[^0-9a-zA-Z\-]/', '', $barcode);
    if($this->barcode_sichtbar)
      $this->barcode = $barcode;
    else $this->barcode="";

  }


  public function Header() {
    if($this->knickfalz!='1'){
      $this->Line(0,105,5,105);
      $this->Line(0,148,7,148);
      $this->Line(0,210,5,210);
    }
    if($this->logofile!='')
      $this->Image($this->logofile,15,10,110);

    if($this->barcode!='' && $this->briefpapier==''){
      //$this->Rotate(90);
      $this->Code39($this->barcode_x, $this->barcode_y, $this->barcode, 1, 3);
      //$this->Rotate(0);
    }

    if($this->briefpapier!='' && $this->PageNo()<=1)
      $this->setStationery($this->briefpapier);

    // wenn
    if($this->PageNo() > 1 && $this->briefpapier2!='' && $this->briefpapier2vorhanden=='1'){
      if($this->doctype != 'produktion'){
        $this->setStationery($this->briefpapier2);
      }
    }
    else if ( $this->PageNo() > 1 && $this->briefpapier!=''){
      $this->setStationery($this->briefpapier);
    }


    if($this->PageNo() > 1)
    {
      $this->SetY($this->abseite2y);
    }
  }

  public function Footer() {
    $differenz=12;
    $this->SetXY(12,$this->getStyleElement("abstand_seiten_unten")*-1);
    $this->SetFont($this->GetFont(),'',8);


    if($this->seite_von_sichtbar!="1")
    {
      if($this->getStyleElement("seite_von_ausrichtung_relativ")=="1")// && $this->seite_von_ausrichtung=="C")
      {
        $this->SetX($this->getStyleElement("abstand_seitenrandlinks"));
        $differenz = $this->getStyleElement("abstand_seitenrandlinks");
      }

    $tmp = $this->rMargin;
    if($this->getStyleElement("abstand_seitenrandrechts")>0)
      $this->rMargin=$this->getStyleElement("abstand_seitenrandrechts");
    else
      $this->rMargin=$this->getStyleElement("abstand_seitenrandlinks");

      $tmpc = $this->cMargin;
      if($this->seite_von_ausrichtung=="R")
        $this->cMargin=-3;

      if($this->getStyleElement("seite_belegnr"))
        $this->Cell(0,8,$this->app->erp->Beschriftung("dokument_seite").' '.$this->PageNo().' '.$this->app->erp->Beschriftung("dokument_seitevon").' {nb} '.$this->zusatzfooter,0,0,$this->seite_von_ausrichtung);
      else
        $this->Cell(0,8,$this->app->erp->Beschriftung("dokument_seite").' '.$this->PageNo().' '.$this->app->erp->Beschriftung("dokument_seitevon").' {nb}',0,0,$this->seite_von_ausrichtung);

      $this->cMargin = $tmpc;

      $this->rMargin=$tmp;
    }

    if($this->nichtsichtbar_footer!=true)
    {

      $footerarr = $this->app->DB->SelectArr("SELECT * FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
      $firmendaten_extra = $this->app->DB->SelectArr("SELECT * FROM firmendaten_werte");
      if($firmendaten_extra)
      {
        foreach($firmendaten_extra as $v)
        {
          if(!isset($footerarr[0][$v['name']]))$footerarr[0][$v['name']] = $v['wert'];
        }
      }
      $footerarr = $footerarr[0];

      foreach($footerarr as $key=>$value)
        $footerarr[$key] = utf8_decode($value);

      $this->SetXY(10,-26);
      $this->SetDrawColor($this->getStyleElement("footer_farbe"));
      $this->SetTextColor($this->getStyleElement("footer_farbe"));

      $this->SetFont($this->GetFont(),'',7);


      if($this->getStyleElement("footer_zentriert")!=1)
      {
      $this->MultiCell($footerarr['footer_breite1'],3,utf8_encode("  ".$footerarr['footer_0_0']."\n  ".$footerarr['footer_0_1']."\n  ".$footerarr['footer_0_2']."\n  ".$footerarr['footer_0_3']."\n  ".$footerarr['footer_0_4']."\n  ".$footerarr['footer_0_5'].""),'','L');
      $this->SetXY($footerarr['footer_breite1'] + 10,-26); // Breite 1 + 10
      $this->MultiCell($footerarr['footer_breite2'],3,utf8_encode("  ".$footerarr['footer_1_0']."\n  ".$footerarr['footer_1_1']."\n  ".$footerarr['footer_1_2']."\n  ".$footerarr['footer_1_3']."\n  ".$footerarr['footer_1_4']."\n  ".$footerarr['footer_1_5'].""),'','L');
      $this->SetXY($footerarr['footer_breite1'] + $footerarr['footer_breite2'] + 10,-26); //breite 1 + breite 2 + 10
      $this->MultiCell($footerarr['footer_breite3'],3,utf8_encode("  ".$footerarr['footer_2_0']."\n  ".$footerarr['footer_2_1']."\n  ".$footerarr['footer_2_2']."\n  ".$footerarr['footer_2_3']."\n  ".$footerarr['footer_2_4']."\n  ".$footerarr['footer_2_5'].""),'','L');
      $this->SetXY($footerarr['footer_breite1'] + $footerarr['footer_breite2'] + $footerarr['footer_breite3'] + 10,-26); //breite 1 + breite 2 + breite 3 + 10
      $this->MultiCell($footerarr['footer_breite4'],3,utf8_encode("  ".$footerarr['footer_3_0']."\n  ".$footerarr['footer_3_1']."\n  ".$footerarr['footer_3_2']."\n  ".$footerarr['footer_3_3']."\n  ".$footerarr['footer_3_4']."\n  ".$footerarr['footer_3_5'].""),'','L');
      } else {
        $this->MultiCell(0,3,utf8_encode("  ".$footerarr['footer_0_0']."\n  ".$footerarr['footer_0_1']."\n  ".$footerarr['footer_0_2']."\n  ".$footerarr['footer_0_3']."\n  ".$footerarr['footer_0_4']."\n  ".$footerarr['footer_0_5'].""),'','C');


      }
    }
  }

  /**
   * @param string $parameter
   *
   * @return array|null
   */
  protected function getOldDocument(string $parameter): ?array
  {
    return $this->app->DB->SelectRow(
      "SELECT * 
      FROM `pdfarchiv` 
      WHERE `table_id` = '".$this->id."'  
        AND `table_name` = '".$this->table."' 
        AND `doctype` = '".$this->app->DB->real_escape_string($this->doctype)."' 
        AND `doctypeorig` = '".$this->app->DB->real_escape_string($this->doctypeOrig)."' 
        AND CHAR_LENGTH(`belegnummer`) > 2 
        AND `belegnummer` <> 'SAB' 
        AND `parameter` = '$parameter' 
        AND `keinhintergrund` = '".($this->app->erp->BriefpapierHintergrunddisable?'1':'0')."' 
      ORDER BY `zeitstempel` DESC 
      LIMIT 1"
    );
  }

  /**
   * @param string $path
   *
   * @return bool
   */
  protected function checkAndUpdateDocumentName(string $path): bool
  {
    if(!file_exists($path)){
      return false;
    }
    $pathInfos = explode('_', basename($path),3);
    $md5 = md5_file($path);
    if($md5 === $pathInfos[0]){
      return false;
    }

    return $this->updateDocumentEntry($pathInfos, $md5,$path);
  }

  /**
   * @param array  $fileInfos
   * @param string $md5
   * @param string $filePath
   */
  protected function updateDocumentEntry(array $fileInfos, string $md5, string $filePath): bool
  {
    $fileName = basename($filePath);
    $dir = dirname($filePath);
    $newFileName = $md5 . '_' . explode('_', basename($filePath),2)[1];
    $newFilePath = $dir . '/' . $newFileName;
    $tableId = $fileInfos[1];
    if(file_exists($newFilePath)){
      $this->checkAndUpdateDocumentName($newFilePath);
    }
    if(file_exists($newFilePath)){
      return false;
    }

    $this->updatePdfArchiveEntry((int)$tableId, $md5, $newFileName, $fileName);
    rename($dir . '/' . $fileName, $newFilePath);

    return true;
  }

  /**
   * @param int    $tableId
   * @param string $md5
   * @param string $newFileName
   * @param string $oldFilename
   */
  protected function updatePdfArchiveEntry(int $tableId, string $md5, string $newFileName, string $oldFilename): void
  {
    $this->app->DB->Update(
      "UPDATE `pdfarchiv` 
      SET `checksum` = '{$md5}', `dateiname` = '{$newFileName}' 
      WHERE `dateiname` = '{$oldFilename}' AND `table_id` = {$tableId} AND `table_name` = '{$this->table}'"
    );
  }

  /**
   * @param string $checkSum
   * @param string $fileName
   * @param string $documentNumber
   * @param string $parameter
   *
   * @return int
   */
  protected function createPdfArchiveEntry(
    string $checkSum, string $fileName, string $documentNumber, string $parameter = ''
  ): int
  {
    $userName = $this->app->DB->real_escape_string($this->app->User->GetName());
    $documentType = $this->app->DB->real_escape_string($this->doctype);
    $originalDocumentType = $this->app->DB->real_escape_string($this->doctypeOrig);
    $documentNumber = $this->app->DB->real_escape_string($documentNumber);
    $parameter = $this->app->DB->real_escape_string($parameter);
    $noBackGround = $this->app->erp->BriefpapierHintergrunddisable ? 1 : 0;
    $this->app->DB->Insert(
      "INSERT INTO `pdfarchiv` 
      (
         `schreibschutz`, `zeitstempel`, `checksum`, `table_id`, `table_name`, `bearbeiter`,
         `erstesoriginal`, `doctype`, `doctypeorig`, `dateiname`, 
         `belegnummer`, `keinhintergrund`, `parameter` 
      ) 
      VALUES 
      (
       1, NOW(), '{$checkSum}', '{$this->id}', '{$this->table}', '{$userName}',
       0, '{$documentType}', '{$originalDocumentType}', '{$fileName}',
       '{$documentNumber}', '{$noBackGround}', '{$parameter}'
      )"
    );

    return (int)$this->app->DB->GetInsertID();
  }

  /**
   * @param false  $schreibschutz
   * @param false  $force
   * @param string $parameter
   */
  public function ArchiviereDocument($schreibschutz = false, $force = false, $parameter = ''): void
  {
    if(!$schreibschutz){
      $schreibschutz = (bool)$this->app->DB->Select(
        "SELECT `schreibschutz` FROM `{$this->table}` WHERE `id` = '{$this->id}' LIMIT 1"
      );
    }

    if($parameter == ''){
      $parameter = $this->parameter;
    }

    if($this->ausarchiv){
      return;
    }

    $isDraft = $this->app->DB->Select(
      "SELECT `id` FROM `{$this->table}` WHERE `id` = '{$this->id}' AND `belegnr` <> '' AND `belegnr` <> '0' LIMIT 1"
    ) === null;

    if($isDraft){
      return;
    }

    if($force) {
      $this->app->DB->Update("UPDATE `{$this->table}` SET `schreibschutz` = 1 WHERE `id` = '{$this->id}' LIMIT 1");
      $schreibschutz = true;
    }

    if(!$schreibschutz){
      return;
    }

    $this->filename = $this->app->erp->Dateinamen($this->filename);
    $dir = rtrim($this->app->Conf->WFuserdata, '/') . '/pdfarchiv/' . $this->app->Conf->WFdbname . '/' . $this->table;
    if(!is_dir($dir) && !mkdir($dir, 0700,true) && !is_dir($dir)){
      $this->app->erp->LogFile('Fehler beim erstellen von '.$dir);
      return;
    }

    $dir = dirname(Briefpapier::getPDFfolder($dir, $this->id, $this->id . '_' . $this->filename, false, true));
    $absoluteFilePath = $dir . '/' .$this->id . '_' . $this->filename;

    $oldDocument = $this->getOldDocument((string)$parameter);
    $isOldDocumentFileExists = !empty($oldDocument['dateiname']) && is_file($dir . '/' . $oldDocument['dateiname']);
    $hasOldDocumentFileMd5Sum = $isOldDocumentFileExists
      && !empty($oldDocument['checksum'])
      && strpos($oldDocument['dateiname'], $oldDocument['checksum']) ===0;
    if($hasOldDocumentFileMd5Sum) {
      $oldFile = $dir . '/' . $oldDocument['dateiname'];
      if($this->checkAndUpdateDocumentName($oldFile)) {
        $oldDocument = $this->getOldDocument((string)$parameter);
        $isOldDocumentFileExists = !empty($oldDocument['dateiname']) && is_file($dir . '/' . $oldDocument['dateiname']);
      }
    }
    $oldFile = $isOldDocumentFileExists ? $dir . '/' . $oldDocument['dateiname'] : null;
    if($oldFile !== null && !$force) {
      return;
    }
    $documentNumber = str_ireplace('.pdf', '', substr($this->filename, strrpos($this->filename, '_') + 1));
    // If the current document file does not exist
    if(!file_exists($absoluteFilePath)){
      // save this document to file
      $this->Output($absoluteFilePath, 'F');
      if(empty($oldDocument) || $oldDocument['dateiname'] !== basename($absoluteFilePath)) {
        $this->createPdfArchiveEntry(
          md5_file($absoluteFilePath), basename($absoluteFilePath), $documentNumber, (string)$parameter
        );
      }
      $this->resetToArchiveFlag();
      return;
    }
    // create a new temp file
    $tempFilePath =  $dir . '/' .$this->id . '_temp' . $this->filename;
    $this->Output($tempFilePath, 'F');
    if($oldFile !== null && $this->areFilesEqualExceptCreationDate($oldFile, $tempFilePath)) {
      unlink($tempFilePath);
      $this->resetToArchiveFlag();
      return;
    }
    $md5CurrentFile = md5_file($absoluteFilePath);
    // and compare with existing file
    if($this->areFilesEqualExceptCreationDate($absoluteFilePath, $tempFilePath)){
    //if($md5CurrentFile === md5_file($tempFilePath)){
      // If same, delete temp file and
      unlink($tempFilePath);
      $this->resetToArchiveFlag();
      return;
    }
    // build the file name with md5_hash
    $md5FileName = $dir . '/' . $md5CurrentFile . '_' . $this->id . '_' . $this->filename;
    // check if the file exists
    $this->checkAndUpdateDocumentName($md5FileName);
    if(!file_exists($md5FileName)){
      rename($absoluteFilePath, $md5FileName);
      $this->updatePdfArchiveEntry($this->id, $md5CurrentFile, basename($md5FileName), basename($absoluteFilePath));

      rename($tempFilePath, $absoluteFilePath);
      $this->createPdfArchiveEntry(
        md5_file($absoluteFilePath), basename($absoluteFilePath), $documentNumber, (string)$parameter
      );
      $this->resetToArchiveFlag();
      return;
    }
    if($this->areFilesEqualExceptCreationDate($absoluteFilePath, $md5FileName)){
      rename($tempFilePath, $absoluteFilePath);
      $this->createPdfArchiveEntry(
        md5_file($absoluteFilePath), basename($absoluteFilePath), $documentNumber, (string)$parameter
      );
      $this->resetToArchiveFlag();
      return;
    }

    unlink($tempFilePath);
    $this->resetToArchiveFlag();
  }

  /**
   * resolve to-archive request for document, after creating new pdf-file
   */
  public function resetToArchiveFlag(): void
  {
    if(empty($this->id) || empty($this->table)) {
      return;
    }
    $this->app->DB->Update("UPDATE `{$this->table}` SET `zuarchivieren` = 0 WHERE `id` = {$this->id} LIMIT 1");
  }

  /**
   * @param string $firstFilePath
   * @param string $secondFilePath
   * @return bool
   */
  public function areFilesEqualExceptCreationDate(string $firstFilePath, string $secondFilePath)
  {
    if(!file_exists($firstFilePath) || !file_exists($secondFilePath)){
      return false;
    }

    $pattern = '#CreationDate \(D:[0-9]+\)#';

    $contentFirstFile = preg_replace($pattern, '', @file_get_contents($firstFilePath));
    $contentSecondFile = preg_replace($pattern, '', @file_get_contents($secondFilePath));

    return md5($contentFirstFile) === md5($contentSecondFile);
  }

  public function DocumentArchiviert()
  {
    $this->filename = $this->app->erp->Dateinamen($this->filename);
    $dir = $this->app->Conf->WFuserdata.'/pdfarchiv/'.$this->app->Conf->WFdbname;
    if(!is_dir($dir)){
      return false;
    }
    if(!is_dir($dir.'/'.$this->table)){
      return false;
    }

    $md5alt = false;
    $altesdokument = $this->app->DB->SelectRow("SELECT * from pdfarchiv where table_id = '".$this->id."'  and table_name = '".$this->table."' AND doctype = '".$this->app->DB->real_escape_string($this->doctype)."' AND doctypeorig = '".$this->app->DB->real_escape_string($this->doctypeOrig)."' AND CHAR_LENGTH(belegnummer) > 2 AND belegnummer <> 'SAB' AND parameter = '".$this->parameter."' ORDER BY zeitstempel DESC LIMIT 1");
    if(empty($altesdokument)){
      return false;
    }

    $md5alt = $altesdokument['checksum'];
    if(!file_exists($dir.'/'.$this->table.'/'.$md5alt.'_'.$this->id.'_'.$this->filename)) {
      $file = Briefpapier::getPDFfolder($dir.'/'.$this->table, $this->id,$md5alt.'_'.$this->id.'_'.$this->filename);
      if(!file_exists($file)){
        return false;
      }
    }

    $this->Output($dir.'/'.$this->table.'/TEMP_'.$this->id.'_'.$this->filename,'F');
    $md5sum_tmp = md5_file($dir.'/'.$this->table.'/TEMP_'.$this->id.'_'.$this->filename);
    unlink($dir.'/'.$this->table.'/TEMP_'.$this->id.'_'.$this->filename);
    return $md5sum_tmp == $md5alt;
  }


  public function archiveDocument($parameter = '')
  {
    if($this->table && $this->id)
    {
      $this->ArchiviereDocument($this->app->DB->Select('SELECT schreibschutz FROM `'.trim($this->app->DB->real_escape_string($this->table))."` WHERE id = '".(int)$this->id."' LIMIT 1")?true:false,false, $parameter);
    }else{
      $this->ArchiviereDocument();
    }
  }

  public static function getPDFfolder($folder, $tableid, $file = '', $forcenew = false, $createfolder = false)
  {
    if($file === '') {
      $file = $folder;
      $folder = dirname($folder);
      $file = substr($file, strlen($folder)+1);
    }
    if(!$forcenew && file_exists($folder.'/'.$file)) {
      return $folder.'/'.$file;
    }
    $tableida = str_split($tableid, 1);
    $newfolder = $folder. '/'.implode('/',$tableida);
    if(!$createfolder) {
      return $newfolder.'/'.$file;
    }

    if(!is_dir($newfolder) && !mkdir($newfolder,0700,true) && !is_dir($newfolder)){
      return $folder.'/'.$file;
    }

    return $newfolder.'/'.$file;
  }

  public function inlineDocument($from_archiv = false) {
    //$from_archiv=false;
    //$filenameprefix = substr($this->filename,9,2);
    $content = null;
    if($from_archiv && !empty($this->table) && !empty($this->id)) {
      $dir = $this->app->Conf->WFuserdata.'/pdfarchiv/'.$this->app->Conf->WFdbname.'/'.$this->table;
      if(is_dir($dir)) {
        if($this->table === 'rechnung') {
          $filenameprefix = substr($this->filename,9,2);
          $mirror = $this->app->DB->Select(
            "SELECT dateiname 
            FROM pdfarchiv 
            WHERE table_name = '".$this->table."' AND table_id = '".$this->id."' AND schreibschutz = 1  AND substring(belegnummer,1,2) = '$filenameprefix' AND parameter = '".$this->parameter."'  AND keinhintergrund = '".(int)$this->app->erp->BriefpapierHintergrunddisable."'  
            ORDER BY zeitstempel DESC 
            LIMIT 1"
          );
        }
        else{
          $mirror = $this->app->DB->Select(
            "SELECT dateiname 
            FROM pdfarchiv 
            WHERE table_name = '".$this->table."' AND table_id = '".$this->id."' AND schreibschutz = 1 AND parameter = '".$this->parameter."' AND keinhintergrund = '".(int)$this->app->erp->BriefpapierHintergrunddisable."'  
            ORDER BY zeitstempel DESC 
            LIMIT 1"
          );
        }
        if(!empty($mirror)) {
          $file = Briefpapier::getPDFfolder($dir,$this->id, $mirror);
          //if(is_file($dir.'/'.$mirror))
          if(is_file($file)) {
            //$content = file_get_contents($dir.'/'.$mirror);
            $content = file_get_contents($file);
            if(!empty($content)) {
              header('Content-type: application/pdf');
              echo $content;
              $this->app->ExitXentral();
            }
          }
        }
      }
    }

    $this->renderDocument();
    $this->archiveDocument();
    header('Content-type: application/pdf');
    $this->Output();

    $this->app->ExitXentral();
  }

  /**
   * @param int    $tableid
   * @param string $tablename
   *
   * @return bool
   */
  public function zuArchivieren($tableid, $tablename)
  {
    if($tablename) {
      $check = $this->app->DB->Select("SELECT zuArchivieren FROM `$tablename` WHERE id = '$tableid'");
    }
    if($check){
      return true;
    }
    $mirrors = $this->app->DB->SelectArr(
      "SELECT * 
      FROM pdfarchiv 
      WHERE table_id = '".$tableid."' AND `table_name` = '".$tablename."' AND erstesoriginal = 0 
      AND CHAR_LENGTH(belegnummer) > 2 AND belegnummer <> 'SAB' AND parameter = '".$this->parameter."'  
      AND keinhintergrund = '".(int)$this->app->erp->BriefpapierHintergrunddisable."' 
      ORDER BY zeitstempel DESC"
    );
    if(empty($mirrors)) {
      return true;
    }
    $dir = $this->app->Conf->WFuserdata.'/pdfarchiv/'.$this->app->Conf->WFdbname.'/'.$tablename;
    if(!is_dir($dir)) {
      return true;
    }
    foreach($mirrors as $k => $mirror) {
      $dateipfad = Briefpapier::getPDFfolder($dir,$tableid,$mirror['dateiname']);
      if(!empty($mirror['dateiname']) && file_exists($dateipfad)) {
        return false;
      }
    }

    return true;
  }

  /**
   * @param int $id
   */
  public function movePDFArchiv($id)
  {

    $mirrors = $id <=0?null:$this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM pdfarchiv WHERE id = %d',
        $id
      )
    );

    if(empty($mirrors) || empty($mirrors['dateiname'])) {
      return;
    }
    $dir = $this->app->Conf->WFuserdata.'/pdfarchiv/'.$this->app->Conf->WFdbname.'/'.$mirrors['table_name'];
    if(!is_file($dir.'/'.$mirrors['dateiname'])) {
      return;
    }
    $newfile = self::getPDFfolder($dir,$mirrors['table_id'],$mirrors['dateiname'],true,true);
    if($newfile != $dir.'/'.$mirrors['dateiname'] && !is_file($newfile)) {
      @rename($dir.'/'.$mirrors['dateiname'], $newfile);
    }
  }

  /**
   * @param int    $tableid
   * @param string $tablename
   *
   * @return array|bool
   */
  public function getArchivedFiles($tableid, $tablename)
  {
    $mirrors = $this->app->DB->SelectArr("SELECT * from pdfarchiv where table_id = '".$tableid."' and table_name = '".$tablename."' AND CHAR_LENGTH(belegnummer) > 2 AND belegnummer <> 'SAB' AND keinhintergrund = 0 ORDER BY zeitstempel DESC");
    if(!$mirrors){
      return false;
    }

    $dir = $this->app->Conf->WFuserdata.'/pdfarchiv/'.$this->app->Conf->WFdbname.'/'.$tablename;
    if(!is_dir($dir)) {
      return false;
    }

    foreach($mirrors as $k => $mirror)  {
      $dateipfad = Briefpapier::getPDFfolder($dir,$tableid,$mirror['dateiname']);
      if(!$mirror['dateiname'] || !file_exists($dateipfad)) {
        unset($mirrors[$k]);
      }
      else{
        $mirrors[$k]['file'] = $mirror['dateiname'];
      }
    }

    if(empty($mirrors)) {
      return false;
    }

    return $mirrors;
  }

  public function getArchivedFiles_alt($tableid, $tablename)
  {

    $mirrors = $this->app->DB->SelectArr("SELECT * from pdfmirror_md5pool where table_id = '".$tableid."' and table_name = '".$tablename."' ORDER BY zeitstempel DESC");

    if(!$mirrors)return false;

    $dir = $this->app->Conf->WFuserdata."/pdfmirror/".$this->app->Conf->WFdbname.'/'.$tablename;
    if(is_dir($dir))
    {


      $files = glob($dir.'/*.pdf');
      if(!$files)return false;

      foreach($files as $file)
      {
        $path_parts = pathinfo($file);
        foreach($mirrors as $k => $mirror)
        {
          if($mirror['checksum'])
          {

            if(strpos($path_parts['basename'],$mirror['checksum'])=== 0)
            {
              $mirrors[$k]['file'] = $path_parts['basename'];
            }

          } else {
            if(strpos($path_parts['filename'], $tableid.'_') === 0){
              $mirrors[$k]['file'] = $path_parts['basename'];
            }

          }

        }
      }
      foreach($mirrors as $k => $mirror)
      {
        if(!isset($mirror['file']))unset($mirrors[$k]);
      }

      if(!isset($mirrors) || count($mirrors) < 1)return false;

      return $mirrors;
    }
    return false;
  }

  /**
   * @param int $id
   *
   * @return bool|array
   */
  public function getArchivByID($id)
  {
    $id = (int)$id;
    if($id <= 0) {
      return false;
    }
    $dir = $this->app->Conf->WFuserdata.'/pdfarchiv/'.$this->app->Conf->WFdbname;
    if(!is_dir($dir)) {
      return false;
    }
    $mirror = $this->app->DB->SelectRow(sprintf('SELECT * FROM pdfarchiv WHERE id = %d LIMIT 1', $id));
    if(empty($mirror)){
      return false;
    }

    if(empty($mirror['dateiname'])) {
      return false;
    }
    $file = Briefpapier::getPDFfolder($dir.'/'.$mirror['table_name'], $mirror['table_id'], $mirror['dateiname']);

    if(!file_exists($file)) {
      return false;
    }

    $tmpr['belegnr'] = $mirror['belegnummer'];
    $tmpr['file'] = file_get_contents($file);

    return $tmpr;
  }

  public function getArchivByID_alt($id)
  {
    $id = (int)$id;
    if(!$id)return false;
    $dir = $this->app->Conf->WFuserdata."/pdfmirror/".$this->app->Conf->WFdbname;
    if(!is_dir($dir))return false;
    $mirror = $this->app->DB->SelectArr("SELECT * from pdfmirror_md5pool where id = '$id' LIMIT 1");
    if(!$mirror)return false;
    $mirror = reset($mirror);
    $mirrors = $this->getArchivedFiles_alt($mirror['table_id'], $mirror['table_name']);
    if(!$mirrors)return false;
    foreach($mirrors as $mirror)
    {
      //echo ".";
      if($mirror['id'] == $id)
      {
        //echo ":";
        if(file_exists($dir."/".$mirror['table_name']."/".$mirror['file']))
        {
          //echo ";";
          $tmpr['belegnr'] = str_replace('.pdf','',$mirror['file']);
          $tmpr['belegnr'] = substr($tmpr['belegnr'],strrpos($tmpr['belegnr'],'_')+1);
          $tmpr['file'] = file_get_contents($dir."/".$mirror['table_name']."/".$mirror['file']);
          return $tmpr;
        }
        return false;
      }
    }
    return false;
  }

  /**
   * @param bool $from_archiv
   */
  public function displayDocument($from_archiv = false) {
    //$from_archiv=false;
    if($from_archiv) {
      $dir = $this->app->Conf->WFuserdata.'/pdfarchiv/'.$this->app->Conf->WFdbname.'/'.$this->table;
      if($this->table && $this->id && is_dir($dir)) {
        if($this->table === 'rechnung') {
          $filenameprefix = substr($this->filename,9,2);
          $mirror = $this->app->DB->Select("SELECT dateiname FROM pdfarchiv WHERE table_name = '".$this->table."' AND table_id = '".$this->id."' AND schreibschutz = 1  AND substring(belegnummer,1,2) = '$filenameprefix' AND parameter = '".$this->parameter."' AND keinhintergrund = '".(int)$this->app->erp->BriefpapierHintergrunddisable."'  ORDER by zeitstempel DESC LIMIT 1");
        }
        else{
          $mirror = $this->app->DB->Select("SELECT dateiname FROM pdfarchiv WHERE table_name = '".$this->table."' AND table_id = '".$this->id."' AND schreibschutz = 1   AND parameter = '".$this->parameter."'   AND keinhintergrund = '".(int)$this->app->erp->BriefpapierHintergrunddisable."' ORDER by zeitstempel DESC LIMIT 1");
        }
        if($mirror) {
          $file = Briefpapier::getPDFfolder($dir, $this->id, $mirror);
          //if(is_file($dir.'/'.$mirror))
          if(is_file($file)) {
            //$content = file_get_contents($dir.'/'.$mirror);
            $content = file_get_contents($file);
            if(!empty($content)) {
              $this->filename = $this->app->erp->Dateinamen($this->filename);
              header('Content-type: application/pdf');
              header('Content-Disposition: attachment; filename="'.$this->filename.'"');
              echo $content;
              $this->app->ExitXentral();
            }
          }
        }
      }
    }
    $this->filename = $this->app->erp->Dateinamen($this->filename);

    $this->renderDocument();
    $this->archiveDocument();
    $this->Output($this->filename,'D');
    $this->app->ExitXentral();
  }

  public function displayTMP($from_archiv = false) {
    //if(!$from_archiv && $this->table && $this->id)$from_archiv = $this->app->DB->Select("SELECT schreibschutz FROM `".$this->table."` WHERE id = '".$this->id."' LIMIT 1");
    if($from_archiv) {
      $dir = $this->app->Conf->WFuserdata.'/pdfarchiv/'.$this->app->Conf->WFdbname.'/'.$this->table;
      if($this->table && $this->id && is_dir($dir)) {
        if($this->table === 'rechnung') {
          $filenameprefix = substr($this->filename,9,2);
          $mirror = $this->app->DB->Select("SELECT dateiname FROM pdfarchiv WHERE table_name = '".$this->table."' AND table_id = '".$this->id."' AND schreibschutz = 1  AND substring(belegnummer,1,2) = '$filenameprefix'  AND parameter = '".$this->parameter."' AND keinhintergrund = '".(int)$this->app->erp->BriefpapierHintergrunddisable."'   ORDER by zeitstempel DESC LIMIT 1");
        }
        else{
          $mirror = $this->app->DB->Select("SELECT dateiname FROM pdfarchiv WHERE table_name = '".$this->table."' AND table_id = '".$this->id."' AND schreibschutz = 1   AND parameter = '".$this->parameter."' AND keinhintergrund = '".(int)$this->app->erp->BriefpapierHintergrunddisable."'   ORDER by zeitstempel DESC LIMIT 1");
        }
        if($mirror){
          $mirror2 = Briefpapier::getPDFfolder($dir,$this->id, $mirror);
          if(is_file($mirror2)) {
            $mirror = substr($mirror2, strlen($dir)+1);
          }
          if(is_file($dir.'/'.$mirror)){
            $this->filename = $this->app->erp->Dateinamen($this->filename);
            $content = file_get_contents($dir.'/'.$mirror);
            if($content != '' && file_put_contents($this->app->erp->GetTMP().$this->filename,$content)){
              if($this->addpdf){
                $files[] = $this->app->erp->GetTMP().$this->filename;
                foreach($this->addpdf as $file){
                  $files[] = $file;
                }
                $content = $this->app->erp->MergePDF($files);
                file_put_contents($this->app->erp->GetTMP().$this->filename,$content);
              }
              $this->ausarchiv = true;
              return $this->app->erp->GetTMP().$this->filename;
            }
          }
        }
      }
    }

    $this->renderDocument();
    $this->archiveDocument();
    $this->filename = $this->app->erp->Dateinamen($this->filename);
    $this->Output($this->app->erp->GetTMP().$this->filename,'F');

    return $this->app->erp->GetTMP().$this->filename;
  }

  public function sendDocument($from_archiv = false) {
    $this->renderDocument();
    $this->archiveDocument();
    $this->filename = $this->app->erp->Dateinamen($this->filename);
    $this->Output($this->filename,'S');
    $this->app->ExitXentral();
  }

  /***********************************
   *       public functions
   ***********************************/
  // setup relevant data for a invoice
  public function setupTax() {
    // full taxrate
    //   define("USTV",0.19);
    // reduced taxrate
    //    define("USTR",0.07);
  }


  public function calcTotals() {
    $total=$totalFullTax=$totalReducedTax=0;
    $citems = !empty($this->items)?count($this->items):0;
    for($i=0;$i<$citems;$i++) {
      $total += $this->items[$i]['tprice'];
      if($this->items[$i]['tax']=="USTV") {
        $totalFullTax+= $this->items[$i]['tprice']*USTV;
      }
      else                                {
        $totalReducedTax+= $this->items[$i]['tprice']*USTR;
      }
    }
    return array($total,$totalFullTax,$totalReducedTax);
  }

  function GetFont()
  {
    if($this->getStyleElement('schriftart')!=''){
      return $this->getStyleElement('schriftart');
    }

    return 'Arial';
  }

  public function setStyleData($styleData){
    $this->styleData = $styleData;
  }

  private function getStyleElement($key){
    if(isset($this->styleData[$key]) && !empty($this->styleData[$key])) return $this->styleData[$key];

    return $this->app->erp->Firmendaten($key);
  }

  public function renderDocument() {
    // prepare page details
    parent::__construct('P','mm','A4');
    $this->app->erp->RunHook('briefpapier_render_document_hook1', 1, $this);


    //		if($this->getStyleElement("schriftart")!="")
    //			$this->SetFont($this->getStyleElement("schriftart"));

    $font = $this->getStyleElement('schriftart');

    if(!in_array($font, ['', 'Arial', 'Courier', 'Helvetica', 'Times'])){
      $this->AddFont($font,'',strtolower($font).'.php');

      if(is_file("lib/pdf/font/".strtolower($font).'i.php'))
        $this->AddFont($font,'I',strtolower($font).'i.php');
      else
        $this->AddFont($font,'I',strtolower($font).'.php');

      if(is_file("lib/pdf/font/".strtolower($font).'b.php'))
        $this->AddFont($font,'B',strtolower($font).'b.php');
      else
        $this->AddFont($font,'B',strtolower($font).'.php');

      if(is_file("lib/pdf/font/".strtolower($font).'bi.php'))
        $this->AddFont($font,'BI',strtolower($font).'bi.php');
      else
        $this->AddFont($font,'BI',strtolower($font).'.php');

    }

    // invoke Header() and Footer() by adding a new page
    $this->AddPage();
    //$this->setStationery("/home/eproo/eproo-master/app/main/www/lib/dokumente/demo.pdf");
    $this->SetDisplayMode("real","single");

    if($this->getStyleElement("abstand_seitenrandrechts")=="")
      $this->getStyleElementSet("abstand_seitenrandrechts",$this->getStyleElement("abstand_seitenrandlinks"));

    $this->SetMargins($this->getStyleElement("abstand_seitenrandlinks"),50,$this->getStyleElement("abstand_seitenrandrechts"));
    $this->SetAutoPageBreak(true,$this->getStyleElement("abstand_umbruchunten"));
    $this->AliasNbPages('{nb}');

    // render document top to bottom
    if(!$this->nichtsichtbar_empfaenger)
    {
      if(!empty($this->recipient))
        $this->renderRecipient();
    }

    if($this->doctype=="lieferschein" && !empty($this->barcode))
    {
      if($this->barcode_y_header!=0)
        $y = $this->barcode_y_header;
      else
        $y = $this->GetY();
      $this->Code39($this->barcode_x_header, $y+1, $this->barcode, 1, 5);
    }

    if($this->barcode!="" && $this->briefpapier!=""){
      $this->Code39($this->barcode_x, $this->barcode_y, $this->barcode, 1, 3);
    }

    //if(!empty($this->sender))
    $this->renderSender();

    if(!$this->nichtsichtbar_box)
    {
      if(!empty($this->corrDetails) || !empty($this->boldCorrDetails) || !empty($this->italicCorrDetails)
        || !empty($this->italicBoldCorrDetails))
        $this->renderCorrDetails();
    }

    $this->renderDoctype();
    if($this->doctype=="brief")
    {
      $this->SetY(95);
      $this->textDetails['body']=$this->letterDetails['body'];
    }

    $this->renderText();
    if(!empty($this->items)) {
      $this->renderItems();
      $this->SetTextColor(0,0,0);
      if($this->doctype!="lieferschein" && $this->doctype!="preisanfrage" && !$this->nichtsichtbar_summe) {
        $this->renderTotals();
      } else $this->Cell(1,5,'',0);
    }
    $this->renderFooter();
    $this->logofile = "";
    $this->briefpapier="";
    $this->briefpapier2="";
    if($this->addpdf)
    {
      foreach($this->addpdf as $addpdf)
      {
        $filename = $addpdf;
        $this->AddPage();
        $this->SetXY(0,0);
        $anz = $this->setSourceFile($filename);
        for($i = 1; $i <= $anz; $i++)
        {
          if($i > 1)
          {
            $this->AddPage();
            $this->SetXY(0,0);
          }
          $tplidx = $this->ImportPage($i);
          $this->useTemplate($tplidx);
        }
      }
    }
    $this->app->erp->RunHook('briefpapier_render_document_hook2', 1, $this);
  }


  public function renderRecipient(){
    //    $this->SetY(50);
    $this->SetY(50+$this->abstand_adresszeileoben);
    $this->SetFont($this->GetFont(),'',10);

    $schriftgroesse  = $this->getStyleElement('schriftgroesse');
    if($schriftgroesse>0)
      $this->SetFont($this->GetFont(),'',$schriftgroesse);

    if($this->recipient['anrede']!="" && $this->getStyleElement('typimdokument'))
    {
      $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));
      $this->Cell(80,5,$this->recipient['anrede'],0,1);
    }

    $this->SetMargins($this->getStyleElement("abstand_adresszeilelinks"),50);
    if($this->recipient['enterprise']) {
      $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));
      $charlimit=40;
      if(strlen($this->recipient['enterprise'])>$charlimit + 15)
      {
        $array = explode( "\n", wordwrap($this->recipient['enterprise'], $charlimit));
        foreach($array as $row)
        {
          $this->Cell(80,5,$this->app->erp->ReadyForPDF($row),0,1);
        }
      } else {
        $this->Cell(80,5,$this->app->erp->ReadyForPDF($this->recipient['enterprise']),0,1);
      }

    }


    if($this->recipient['firstname']!="")
    {
      $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));
      $this->Cell(80,5,$this->recipient['firstname'],0,1);
    }

    if($this->recipient['address2']!="") {
      $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));
      $this->Cell(80,5,$this->recipient['address2'],0,1);
    }

    if($this->recipient['address3']!="")
    {
      $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));
      $this->Cell(80,5,$this->recipient['address3'],0,1);
    }

    if($this->recipient['address4']!="")
    {
      $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));
      $this->Cell(80,5,$this->recipient['address4'],0,1);
    }


    //$this->Cell(80,5,$this->recipient['firstname']." ".$this->recipient['familyname'],0,1);
    $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));
    $this->Cell(80,5,$this->recipient['address1'],0,1);


    $this->SetFont($this->GetFont(),'',10);
    if($schriftgroesse>0)
      $this->SetFont($this->GetFont(),'',$schriftgroesse);


    $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));
    $inland = $this->getStyleElement("land");
    if($this->recipient['country']!=$inland)
    {
      //$this->Cell(80,5,$this->recipient['country']."-".$this->recipient['areacode']." ".$this->recipient['city'],0,1);

      if(function_exists('mb_strtoupper'))
        $this->Cell(80,5,mb_strtoupper($this->recipient['areacode']." ".$this->recipient['city'],"UTF-8"),0,1);
      else
        $this->Cell(80,5,strtoupper($this->recipient['areacode']." ".$this->recipient['city']),0,1);

      $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));
      $this->Cell(80,5,strtoupper($this->app->erp->UmlauteEntfernen($this->app->GetLandLang($this->recipient['country'],$this->sprache))),0,1);

    }
    else {
      $this->Cell(80,5,$this->recipient['areacode']." ".$this->recipient['city'],0,1);
    }
    //$this->SetFont($this->GetFont(),'',9);
    //if(isset($this->recipient['country'])) $this->Cell(80,5,$this->recipient['country'],0,1);


    //FREITEXT1
    $freitext1aktiv = $this->getStyleElement('freitext1aktiv');
    if($freitext1aktiv){
      $freitext1inhalt = $this->app->erp->Beschriftung("freitext1inhalt");
      if($freitext1inhalt=="") $freitext1inhalt = $this->getStyleElement('freitext1inhalt');

      $freitext1inhalt = $this->app->erp->ParseUserVars($this->table,$this->id,$freitext1inhalt);
      $freitext1inhalt = $this->app->erp->ReadyForPDF($freitext1inhalt);
      $freitext1schriftgroesse = $this->getStyleElement('freitext1schriftgroesse');
      $freitext1y = $this->getStyleElement('freitext1y');
      $freitext1x = $this->getStyleElement('freitext1x');
      $freitext1breite = $this->getStyleElement('freitext1breite');
      $this->SetFont($this->GetFont(),'',$freitext1schriftgroesse);
      $this->SetY($freitext1y);
      $this->SetX($freitext1x);
      $this->MultiCell($freitext1breite, 5, $this->WriteHTML($freitext1inhalt), 0, 'L');
    }


    //FREITEXT2
    $freitext2aktiv = $this->getStyleElement('freitext2aktiv');
    if($freitext2aktiv){
      $freitext2inhalt = $this->app->erp->Beschriftung("freitext2inhalt");
      if($freitext2inhalt=="") $freitext1inhalt = $this->getStyleElement('freitext2inhalt');

      $freitext2inhalt = $this->app->erp->ParseUserVars($this->table,$this->id,$freitext2inhalt);
      $freitext2inhalt = $this->app->erp->ReadyForPDF($freitext2inhalt);
      $freitext2schriftgroesse = $this->getStyleElement('freitext2schriftgroesse');
      $freitext2y = $this->getStyleElement('freitext2y');
      $freitext2x = $this->getStyleElement('freitext2x');
      $freitext2breite = $this->getStyleElement('freitext2breite');
      $this->SetFont($this->GetFont(),'',$freitext2schriftgroesse);
      $this->SetY($freitext2y);
      $this->SetX($freitext2x);
      $this->MultiCell($freitext2breite, 5, $this->WriteHTML($freitext2inhalt), 0, 'L');
    }


    $this->SetMargins($this->getStyleElement("abstand_seitenrandlinks"),50,$this->getStyleElement("abstand_seitenrandrechts"));
  }

  public function setAbsender($sender)
  {
    $this->absender = $sender;

  }

  public function renderSender() {
    $monthlu = array("", "Januar", "Februar", "M�rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
    $monthnom = date("n");
    $month = $monthlu[$monthnom];
    $date = date("j").". ".$month." ".date("Y");


    if($this->nichtsichtbar_zeileabsender!=true)
    {

      //abstand_adresszeileoben
      // line above address field
      $absender = (($this->absender!='') ? $this->absender : $this->getStyleElement('absender'));
      $schriftgroesse  = $this->getStyleElement('schriftgroesseabsender');
      $this->SetY(43+$this->abstand_adresszeileoben);
      $this->SetFont($this->GetFont(),'',$schriftgroesse);
      //$cellStr = $this->sender['enterprise']." � ".$this->sender['address1']." � ".$this->sender['areacode']." ".$this->sender['city'];
      $cellStr = $this->app->erp->ReadyForPDF($absender);

      $this->SetX($this->getStyleElement("abstand_adresszeilelinks"));

      if($this->getStyleElement("absenderunterstrichen")=="1")
        $this->Cell($this->GetStringWidth($cellStr)+2,5,$cellStr,'B');
      else
        $this->Cell($this->GetStringWidth($cellStr)+2,5,$cellStr,'');
    }

    if($this->nichtsichtbar_rechtsoben!=true)
    {
      // detailed sender data
      $lineHeight = 4;
      $xOffset = 131;
      $absatz = 3;

      $this->SetXY($xOffset,10);
      $this->SetFont($this->GetFont(),'',9);
      $this->Cell(30,$lineHeight,"Name der Gesellschaft: ",0,0,'R');
      $this->SetFont($this->GetFont(),'B',9);
      $this->Cell(60,$lineHeight,$this->sender['enterprise'],0,2);
      if(isset($this->sender['enterprise2']))
        $this->Cell(60,$lineHeight,$this->sender['enterprise2'],0,2);

      $this->SetXY($xOffset,$this->GetY());
      $this->SetFont($this->GetFont(),'',9);
      $this->Cell(30,$lineHeight,"Sitz der Gesellschaft: ",0,0,'R');
      $this->SetFont($this->GetFont(),'B',9);
      $this->Cell(60,$lineHeight,$this->sender['address1'],0,2);
      if(isset($this->sender['address2']))
        $this->Cell(60,$lineHeight,$this->sender['address2'],0,2);
      $this->Cell(60,$lineHeight,$this->sender['areacode']." ".$this->sender['city'],0,2);

      $this->SetXY($xOffset,$this->GetY()+$absatz); //abstand
      $this->SetFont($this->GetFont(),'',9);
      if(isset($this->sender['phone1'])) {
        $this->Cell(30,$lineHeight,"Fon: ",0,0,'R');
        $this->Cell(60,$lineHeight,$this->sender['phone1'],0,2);
      }

      if(isset($this->sender['fax'])) {
        $this->SetXY($xOffset,$this->GetY());
        $this->Cell(30,$lineHeight,"Fax: ",0,0,'R');
        $this->Cell(60,$lineHeight,$this->sender['fax'],0,2);
      }


      $this->SetXY($xOffset, $this->GetY()+$absatz); //abstand
      if(isset($this->sender['email'])) {
        $this->Cell(30,$lineHeight,"Mail: ",0,0,'R');
        $this->Cell(60,$lineHeight,$this->sender['email'],0,2);
      }

      if(isset($this->sender['web'])) {
        $this->SetXY($xOffset,$this->GetY());
        $this->Cell(30,$lineHeight,"Web: ",0,0,'R');
        $this->Cell(60,$lineHeight,$this->sender['web'],0,2);
      }

      $this->SetXY($xOffset, $this->GetY()+$absatz); //abstand
      if(isset($this->sender['ustid'])) {
        $this->Cell(30,$lineHeight,"UST-ID: ",0,0,'R');
        $this->Cell(60,$lineHeight,$this->sender['ustid'],0,2);
      }
      if(isset($this->sender['taxnr'])) {
        $this->SetXY($xOffset,$this->GetY());
        $this->Cell(30,$lineHeight,"Steuer-Nr.: ",0,0,'R');
        $this->Cell(60,$lineHeight,$this->sender['taxnr'],0,2);
      }
      if(isset($this->sender['hreg'])) {
        $this->SetXY($xOffset,$this->GetY());
        $this->Cell(30,$lineHeight,"Handelsregister: ",0,0,'R');
        $this->Cell(60,$lineHeight,$this->sender['hreg'],0,2);
      }

      $this->SetXY($xOffset,$this->GetY());
      $this->Cell(30,$lineHeight,utf8_encode("Gesch�ftsf�hrung: "),0,0,'R');
      $this->Cell(60,$lineHeight,$this->sender['firstname'].' '.$this->sender['familyname'],0,2);

      //$this->SetXY($xOffset, $this->GetY()+$absatz+2); //abstand
      //$this->Cell(30,$lineHeight,"Datum: ",0,0,'R');
      //$this->Cell(60,$lineHeight,utf8_encode($date),0,2);
    }
  }


  // kundennummer rechnungsnummer und datum
  public function renderCorrDetails() {

    $breite_spalte_rechts = 30;

    $recht_links_verschieben = $this->abstand_boxrechtsoben_lr;

    $startpos_links_rechts = -83 + $recht_links_verschieben;

    $boldTitleStr = $boldValueStr = $titleStr = $valueStr = "";
    $italicTitleStr = $italicValueStr = '';
    $italicBoldTitleStr = $italicBoldValueStr = '';
    if(isset($this->boldCorrDetails)){
      foreach($this->boldCorrDetails as $title => $value) {
        $boldTitleStr .= $title !== ''?$this->app->erp->ReadyForPDF($title).": \n":" \n";
        $boldValueStr .= $this->app->erp->ReadyForPDF($value)."\n";
      }
    }
    if(!empty($this->italicCorrDetails)) {
      foreach($this->italicCorrDetails as $title => $value) {
        $italicTitleStr .= $title !== ''?$this->app->erp->ReadyForPDF($title).": \n":" \n";
        $italicValueStr .= $this->app->erp->ReadyForPDF($value)."\n";
      }
    }

    if(!empty($this->italicBoldCorrDetails)) {
      foreach($this->italicBoldCorrDetails as $title => $value) {
        $italicBoldTitleStr .= $title !== ''?$this->app->erp->ReadyForPDF($title).": \n":" \n";
        $italicBoldValueStr .= $this->app->erp->ReadyForPDF($value)."\n";
      }
    }

    if(isset($this->corrDetails)){
      foreach($this->corrDetails as $title => $value) {
        if($value!="")
        {
          $titleStr .= $title !== ''?$this->app->erp->ReadyForPDF($title).": \n":" \n";
          $valueStr .= $this->app->erp->ReadyForPDF($value)."\n";
        }
      }
    }

    if($this->boxausrichtung=="") $this->boxausrichtung="R";

    $pos = strpos($this->boxausrichtung, ';');
    if($pos > 0)
    {
      $tmp_box = explode(";",$this->boxausrichtung);
      $this->boxausrichtung = $tmp_box[0];
      $this->box_breite1 = $tmp_box[1];
      $this->box_breite2 = $tmp_box[2];
    }
    if($this->box_breite1=="") $this->box_breite1=34;
    if($this->box_breite2=="") $this->box_breite2=38;


    $fontinfobox = $this->getStyleElement("infobox");

    if($italicBoldTitleStr != '') {
      $this->SetFont($this->GetFont(),'BI',$fontinfobox);
      $this->SetXY($startpos_links_rechts,80);
      $this->MultiCell($this->box_breite1,4,$italicBoldTitleStr,'',!empty($this->boxalignmentleft)?strtoupper($this->boxalignmentleft): $this->boxausrichtung); //TL
      $this->SetXY($startpos_links_rechts+$breite_spalte_rechts,80);
      $this->MultiCell($this->box_breite2,4,$italicBoldValueStr,'',!empty($this->boxalignmentright)?strtoupper($this->boxalignmentright):$this->boxausrichtung);   //TR
    }
    elseif($italicTitleStr != '') {
      $this->SetFont($this->GetFont(),'I',$fontinfobox);
      $this->SetXY($startpos_links_rechts,80);
      $this->MultiCell($this->box_breite1,4,$italicTitleStr,'',!empty($this->boxalignmentleft)?strtoupper($this->boxalignmentleft):$this->boxausrichtung); //TL
      $this->SetXY($startpos_links_rechts+$breite_spalte_rechts,80);
      $this->MultiCell($this->box_breite2,4,$italicValueStr,'',!empty($this->boxalignmentright)?strtoupper($this->boxalignmentright):$this->boxausrichtung);
    }
    elseif($boldTitleStr!='') {
      $this->SetFont($this->GetFont(),'B',$fontinfobox);
      $this->SetXY($startpos_links_rechts,80);
      $this->MultiCell($this->box_breite1,4,$boldTitleStr,'',!empty($this->boxalignmentleft)?strtoupper($this->boxalignmentleft):$this->boxausrichtung); //TL
      $this->SetXY($startpos_links_rechts+$breite_spalte_rechts,80);
      $this->MultiCell($this->box_breite2,4,$boldValueStr,'',!empty($this->boxalignmentright)?strtoupper($this->boxalignmentright):$this->boxausrichtung);   //TR
    }
    else {
      $this->SetXY($startpos_links_rechts,80+$this->abstand_boxrechtsoben);
      $this->MultiCell($this->box_breite1,0,'','',!empty($this->boxalignmentleft)?strtoupper($this->boxalignmentleft):$this->boxausrichtung); //TL
      $this->SetXY($startpos_links_rechts+$breite_spalte_rechts,80+$this->abstand_boxrechtsoben);
      $this->MultiCell($this->box_breite2,0,'','',!empty($this->boxalignmentright)?strtoupper($this->boxalignmentright):$this->boxausrichtung);   //TR
    }

    $this->SetY(80);
    $this->SetY($this->GetY()+$this->abstand_boxrechtsoben);

    $this->SetFont($this->GetFont(),'',$fontinfobox);
    $tempY = $this->GetY();
    $this->SetX($startpos_links_rechts);
    $this->MultiCell($this->box_breite1,4,$titleStr,"",$this->boxausrichtung); //BL
    $this->SetXY($startpos_links_rechts+$breite_spalte_rechts,$tempY);
    $this->MultiCell($this->box_breitexi21,4,$valueStr,"",$this->boxausrichtung); //BR

    $this->SetY(80+$this->abstand_artikeltabelleoben); //Hoehe Box
    //$this->SetY(60);//+$this->abstand_artikeltabelleoben); //Hoehe Box
  }


  public function renderDoctype() {
    //$this->Ln(1);

    if($this->doctype=="brief")
      $betreffszeile  = $this->getStyleElement('betreffszeile');
    else
      $betreffszeile  = $this->getStyleElement('betreffszeile');

    $this->SetY(80);//+$this->abstand_artikeltabelleoben); //Hoehe Box
    //$this->SetY(80+$this->abstand_artikeltabelleoben); //Hoehe Box

    $this->SetFont($this->GetFont(),'B',$betreffszeile);
    $this->SetY($this->GetY()+$this->abstand_betreffzeileoben);
    //$this->Cell(85,6,$this->doctypeOrig);
    $this->MultiCell(210-83+$this->abstand_boxrechtsoben_lr-$this->getStyleElement("abstand_seitenrandlinks")-5,6,html_entity_decode($this->doctypeOrig,ENT_QUOTES),0,'L');
    $this->SetY($this->GetY()-$this->abstand_betreffzeileoben);

    //$this->SetY($this->GetY()+$this->abstand_betreffzeileoben);
    $this->SetY($this->GetY()+$this->abstand_artikeltabelleoben); //Hoehe Box
  }

  public function renderText() {

    if(isset($this->textDetails['body'])) {
      if($this->doctype=="brief")
        $dokumententext  = $this->getStyleElement('dokumententext');
      else
        $dokumententext  = $this->getStyleElement('dokumententext');
      $this->SetFont($this->GetFont(),'',$dokumententext);
      //if($this->doctype!="brief")
      $this->Ln(11);

      $this->textDetails['body'] = $this->app->erp->ParseUserVars($this->table,$this->id,$this->textDetails['body']);

      if($this->getStyleElement("briefhtml")=="1")
      {
        $html = $this->app->erp->ReadyForPDF($this->app->erp->RemoveNewlineAfterBreaks($this->textDetails['body']));
        $this->MultiCell(180,4,$this->WriteHTML($html));
      } else {
        $this->MultiCell(180,4,$this->app->erp->ReadyForPDF($this->textDetails['body']));
      }
    }
  }

  public function renderFooter() {

    $this->app->erp->RunHook('briefpapier_render_footer_hook1', 1, $this);

    if(isset($this->textDetails['footer'])) {
      $freitext  = $this->getStyleElement('freitext');

      if($this->getStyleElement("kleinunternehmer"))
      {
        if($this->textDetails['footer']=="") $this->textDetails['footer'] ="Als Kleinunternehmer im Sinne von §19 Abs.1 UStG wird Umsatzsteuer nicht berechnet!";
        else $this->textDetails['footer'] .="\r\nAls Kleinunternehmer im Sinne von § 19 Abs. 1 UStG wird Umsatzsteuer nicht berechnet!";
      }

      $this->textDetails['footer'] = $this->app->erp->ParseUserVars($this->table,$this->id,$this->textDetails['footer']);

      $this->SetFont($this->GetFont(),'',$freitext);
      //      $this->Ln();
      $y = $this->GetY();

      //			if($this->doctype!="lieferschein")
      //				$this->SetY($y-5);
      //$parsed = $this->app->erp->ReadyForPDF($this->textDetails['footer']);
      if($this->getStyleElement("briefhtml")=="1")
      {
        $html = $this->app->erp->ReadyForPDF($this->app->erp->RemoveNewlineAfterBreaks($this->textDetails['footer']));
        $this->MultiCell(180,4,$this->WriteHTML($html));
      } else {
        $this->MultiCell(180,4,$this->app->erp->ReadyForPDF($this->textDetails['footer']));
      }
    }

    $this->app->erp->RunHook('briefpapier_render_footer_hook2', 1, $this);
  }

  public function CalcPosString($posstr, $oldpostr, $hauptnummer, $oldlvl, $newlvl)
  {
    if($oldpostr == 0)
    {
      if($hauptnummer)
      {
        return $hauptnummer.'.1';
      }
      return '1';
    }
    if($newlvl > $oldlvl)
    {
      return $oldpostr.str_repeat('.1', $newlvl - $oldlvl);
    }
    $oldpostra = explode('.', $oldpostr);

    $diff = $oldlvl - $newlvl;
    $length = count($oldpostra);
    if($hauptnummer && $hauptnummer != $oldpostra[0]){
      return (String)((int)$oldpostra[0]+1).'.1';
    }

    $ret = '';
    if($diff >= $length -1)
    {
      return (String)((int)$oldpostra[0]+1);
    }
    for($i = 0; $i < $length - 1 - $diff; $i++)
    {
      $ret .= $oldpostra[$i].'.';
    }

    return $ret.(String)((int)$oldpostra[$length - 1 - $diff]+1);
  }

  /**
   * @param string $type
   * @param int    $orderId
   * @param int    $orderPositionId
   * @param string $doctype
   *
   * @return string
   */
  public function getDeliveryNoteBestBeforeBatchSnFromOrder($type, $orderId, $orderPositionId, $doctype = 'auftrag')
  {
    if($doctype === 'rechnung') {
      $orderPositionId = $this->app->DB->Select(
        sprintf(
          'SELECT auftrag_position_id FROM rechnung_position WHERE id = %d',
          $orderPositionId
        )
      );
      if($orderPositionId <= 0) {
        return '';
      }
    }

    $position = $this->app->DB->SelectRow(
      sprintf(
        'SELECT id, lieferschein
      FROM lieferschein_position 
      WHERE auftrag_position_id = %d AND auftrag_position_id > 0 
      LIMIT 1',
        $orderPositionId
      )
    );

    if(empty($position)) {
      return '';
    }
    if($type === 'mhd') {
      $this->mhd['lieferschein'][$position['lieferschein']][$position['id']] =
        $this->GetChargeMHDSNString('mhd', 'lieferschein', $position['lieferschein'], $position['id']);
      return $this->mhd['lieferschein'][$position['lieferschein']][$position['id']];
    }
    if($type === 'charge') {
      $this->charge['lieferschein'][$position['lieferschein']][$position['id']] =
        $this->GetChargeMHDSNString('charge', 'lieferschein', $position['lieferschein'], $position['id']);
      return $this->charge['lieferschein'][$position['lieferschein']][$position['id']];
    }
    if($type === 'sn') {
      $this->sn['lieferschein'][$position['lieferschein']][$position['id']] =
        $this->GetChargeMHDSNString('sn', 'lieferschein', $position['lieferschein'], $position['id']);
      return $this->sn['lieferschein'][$position['lieferschein']][$position['id']];
    }

    return '';
  }

  /**
   * @param string $text
   * @param string $doctype
   * @param int    $doctypeId
   * @param int    $positionId
   *
   * @return string
   */
  public function parseBestBeforeBatchSn($text, $doctype, $doctypeId, $positionId){
    if(strpos($text, '{MHD}') !== false) {
      $entry = !empty($this->mhd[$doctype]) && !empty($this->mhd[$doctype][$doctypeId])
        && !empty($this->mhd[$doctype][$doctypeId][$positionId])?
        $this->mhd[$doctype][$doctypeId][$positionId]:'';
      if($entry === '') {
        $this->mhd[$doctype][$doctypeId][$positionId] = $this->GetChargeMHDSNString(
          'mhd',$doctype,$doctypeId,$positionId, true
        );
        $entry = $this->mhd[$doctype][$doctypeId][$positionId];
        if($entry === '' && ($doctype === 'auftrag' || $doctype === 'rechnung')) {
          $entry = $this->getDeliveryNoteBestBeforeBatchSnFromOrder('mhd', $doctypeId, $positionId, $doctype);
        }
      }

      $text = str_replace('{MHD}', $entry, $text);
    }
    if(strpos($text, '{CHARGE}') !== false) {
      $entry = !empty($this->charge[$doctype]) && !empty($this->charge[$doctype][$doctypeId])
      && !empty($this->charge[$doctype][$doctypeId][$positionId])?
        $this->charge[$doctype][$doctypeId][$positionId]:'';
      if($entry === '') {
        $this->charge[$doctype][$doctypeId][$positionId] = $this->GetChargeMHDSNString(
          'charge',$doctype,$doctypeId,$positionId, true
        );
        $entry = $this->charge[$doctype][$doctypeId][$positionId];
        if($entry === '' && ($doctype === 'auftrag' || $doctype === 'rechnung')) {
          $entry = $this->getDeliveryNoteBestBeforeBatchSnFromOrder('charge', $doctypeId, $positionId, $doctype);
        }
      }
      $text = str_replace('{CHARGE}', $entry, $text);
    }
    if(strpos($text, '{SN}') !== false) {
      $entry = !empty($this->sn[$doctype]) && !empty($this->sn[$doctype][$doctypeId])
      && !empty($this->sn[$doctype][$doctypeId][$positionId])?
        $this->sn[$doctype][$doctypeId][$positionId]:'';
      if($entry === '') {
        $this->sn[$doctype][$doctypeId][$positionId] = $this->GetChargeMHDSNString(
          'sn',$doctype,$doctypeId,$positionId, true
        );
        $entry = $this->sn[$doctype][$doctypeId][$positionId];
        if($entry === '' && ($doctype === 'auftrag' || $doctype === 'rechnung')) {
          $entry = $this->getDeliveryNoteBestBeforeBatchSnFromOrder('sn', $doctypeId, $positionId, $doctype);
        }
      }
      $text = str_replace('{SN}', $entry, $text);
    }

    return $text;
  }

  public function renderItems() {

    $this->app->erp->RunHook('briefpapier_renderitems',1, $this);
    //		if($this->bestellungohnepreis) $this->doctype="lieferschein";
    $posWidth     = $this->getStyleElement("breite_position");
    $amWidth     = $this->getStyleElement("breite_menge");
    $itemNoWidth = $this->getStyleElement("breite_nummer");
    $einheitWidth    = $this->getStyleElement("breite_einheit");
    $descWidth    = $this->getStyleElement("breite_artikel");
    $taxWidth    = $this->getStyleElement("breite_steuer");
    $belege_subpositionen = $this->getStyleElement("belege_subpositionen");
    $belege_subpositionenstuecklisten = $this->getStyleElement('belege_subpositionenstuecklisten') && in_array(($this->table?$this->table:$this->doctype),array('rechnung', 'auftrag','lieferschein','gutschrift','angebot'));
    $belege_stuecklisteneinrueckenmm = $this->getStyleElement('belege_stuecklisteneinrueckenmm');
    $doctype = $this->table?$this->table:$this->doctype;
    $doctypeId = $this->id;
    $docArr = $this->app->DB->SelectRow(
      sprintf(
        'SELECT projekt,adresse,steuersatz_normal,steuersatz_ermaessigt FROM `%s` WHERE id = %d',
        $doctype, $doctypeId
      )
    );
    $query = sprintf("SELECT `sprache` FROM `%s` 
      WHERE `id` = %d 
      LIMIT 1",
        $doctype, $doctypeId);
    $documentLanguage = $this->app->DB->Select($query);
    if(!empty($docArr)) {
      $projekt = $docArr['projekt'];
      $adresse = $docArr['adresse'];
    }
    else{
      $projekt = $this->app->DB->Select("SELECT projekt FROM " . ($this->table ? $this->table : $this->doctype) . " WHERE id = '" . $this->id . "' LIMIT 1");
      $adresse = $this->app->DB->Select("SELECT adresse FROM " . ($this->table ? $this->table : $this->doctype) . " WHERE id = '" . $this->id . "' LIMIT 1");
    }
    $inventurohnepreis = null;
    if(!in_array($this->table ? $this->table : $this->doctype, ['rechnung','auftrag','angebot','bestellung'])) {
      $inventurohnepreis = $this->app->DB->Select(
        sprintf(
          'SELECT noprice 
          FROM `%s`
          WHERE id = %d 
          LIMIT 1',
          $this->table ? $this->table : $this->doctype, $this->id
        )
      );
    }
    if($inventurohnepreis){
      $descWidth += 40;
    }

    if($this->doctype=="arbeitsnachweis")
    {
      $itemNoWidth = 20;
      $taxWidth = 40;
      $descWidth   = 95;
    }

    if($this->rabatt=='1') {
      $descWidth -= 15;
    }
    $priceWidth = 20;
    $sumWidth   = 20;
    $rabattWidth   = 15;
    // $lineLength = $amWidth + $itemNoWidth + $descWidth + $taxWidth + $priceWidth + $sumWidth;

    // zwischenloesung um platz zu sparen
    if($this->ust_spalteausblende)
    {
      $taxWidth--;
      $descWidth += $taxWidth;
      $taxWidth= 1;
    }

    $cellhoehe   = 5;

    // render table header
    if(isset($this->textDetails['body'])) {
      $this->Ln();
    }
    else {
      $this->Ln(8);
    }
    $tabellenbeschriftung  = $this->getStyleElement('tabellenbeschriftung');

    $this->SetX($this->getStyleElement('abstand_seitenrandlinks')+1); // eventuell einstellbar per GUI

    $this->SetFont($this->GetFont(),'B',$tabellenbeschriftung);
    $this->Cell($posWidth,6,$this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_position'),0,0,'C'));
    if($this->doctype!='arbeitsnachweis')
    {
      if($this->doctype=='zahlungsavis')
      {
        $this->Cell($itemNoWidth,6,'Nummer');
        $this->Cell($descWidth-$einheitWidth+$taxWidth+$priceWidth+$rabattWidth,6,'Beleg');

        $this->Cell($amWidth,6,'',0,0,'R');
      }
      else {
        $this->Cell($itemNoWidth,6,$this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_artikelnummer')));
        if($this->getStyleElement('artikeleinheit')=='1'){
          $this->Cell($descWidth - $einheitWidth, 6, $this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_artikel')));
        }
        else{
          $this->Cell($descWidth, 6, $this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_artikel')));
        }
        $this->Cell($amWidth,6,$this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_menge')),0,0,'R');
      }
    } else {
      $this->Cell($taxWidth,6,'Mitarbeiter');
      $this->Cell($itemNoWidth,6,'Ort');
      $this->Cell($descWidth,6,'Tätigkeit');
      $this->Cell($amWidth,6,'Stunden',0,0,'R');
    }

    if($this->doctype!='lieferschein' && $this->doctype!='arbeitsnachweis' && $this->doctype!='produktion' && $this->doctype!='zahlungsavis' && $this->doctype!='preisanfrage'){
      if($this->getStyleElement('artikeleinheit')=='1'){
        $this->Cell($einheitWidth, 6, $this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_einheit')), 0, 0, 'R');
      }

      if($this->ust_spalteausblende){
        $this->Cell($taxWidth, 6, '', 0, 0, 'R');
      }
      else{
        $this->Cell($taxWidth, 6, $this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_mwst')), 0, 0, 'R');
      }

      if($this->getStyleElement('artikeleinheit')=='1'){
        if(!$inventurohnepreis){
          $this->Cell($priceWidth, 6, $this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_einzel')), 0, 0, 'R');
        }
      }
      else{
        if(!$inventurohnepreis){
          $this->Cell($priceWidth, 6, $this->app->erp->ParseUserVars($this->doctype, $this->id,$this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_stueck'))), 0, 0, 'R');
        }
      }

      if($this->rabatt=='1') {
        if(!$inventurohnepreis){
          $this->Cell($rabattWidth,6,$this->app->erp->Beschriftung('dokument_rabatt'),0,0,'R');
          $this->Cell($sumWidth,6,$this->app->erp->ParseUserVars($this->doctype, $this->id,$this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_gesamt'))),0,0,'R');
        }
      } else {
        if(!$inventurohnepreis){
          $this->Cell($sumWidth,6,$this->app->erp->ParseUserVars($this->doctype, $this->id,$this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_gesamt'))),0,0,'R');
        }
      }
    }
    else if ($this->doctype=='lieferschein' || $this->doctype=='preisanfrage')
    {
      if($this->getStyleElement("artikeleinheit")=='1'){
        $this->Cell($einheitWidth, 6, $this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_einheit')), 0, 0, 'R');
      }
    }
    else if ($this->doctype=='zahlungsavis')
    {
      $this->Cell($sumWidth,6,$this->app->erp->ParseUserVars($this->doctype, $this->id,$this->app->erp->ReadyForPDF($this->app->erp->Beschriftung('dokument_gesamt'))),0,0,'R');
    }

    $this->Ln();
    $this->Line($this->getStyleElement('abstand_seitenrandlinks')+1, $this->GetY(), 210-$this->getStyleElement("abstand_seitenrandrechts"), $this->GetY());
    $this->Ln(2);

    // render table body
    $tabelleninhalt  = $this->getStyleElement('tabelleninhalt');

    $this->SetFont($this->GetFont(),'',$tabelleninhalt);
    $topos = 0;
    if(is_array($this->items))$topos = count($this->items);
    $zwischenpositionen = $this->app->DB->Select("SELECT count(distinct pos) FROM beleg_zwischenpositionen WHERE doctype='".$this->doctype."' AND doctypeid='".$this->doctypeid."' AND pos >= 0 AND pos <'$topos' AND (postype = 'gruppe'  OR postype = 'zwischensumme' OR postype = 'gruppensumme' OR postype = 'gruppensummemitoptionalenpreisen') ORDER by sort");
    if($zwischenpositionen < 1)$belege_subpositionen = false;
    $hauptnummer = 0;
    $posoffset = 0;
    if($belege_subpositionen)$hauptnummer = 1;
    $oldlvl = 0;
    $oldpostr = 0;
    $langeartikelnummern = ($this->getStyleElement('langeartikelnummern')?true:false);
    if($this->doctype==='bestellung') {
      $langeartikelnummern = $this->app->DB->Select("SELECT langeartikelnummern FROM bestellung WHERE id='".$this->doctypeid."' LIMIT 1")?true:false;
    }
    $pos=0;
    $umsatzsteuerermaessigtProzent = !empty($docArr)?$docArr['steuersatz_ermaessigt']: $this->app->erp->GetSteuersatzErmaessigt(false,!empty($this->id)?$this->id:$this->doctypeid,$this->table);
    $umsatzsteuernormalProzent = !empty($docArr)?$docArr['steuersatz_normal']:$this->app->erp->GetSteuersatzNormal(false,!empty($this->id)?$this->id:$this->doctypeid,$this->table);

    $umsatzsteuerermaessigt = (1+$umsatzsteuerermaessigtProzent/100);
    $umsatzsteuernormal = (1+$umsatzsteuernormalProzent/100);

    $jitposfix = 0; //Die Anzahl der der Unterartikel des vorherigen Stücklistenartikels
    $jitposfixbase = 0; //Die Gesamtzahl der bisher ausgeblendeten Stücklistenartikel
    $anzeigeBelegNettoAdrese = $this->app->erp->AnzeigeBelegNettoAdresse($this->anrede, $this->doctype, $projekt, $adresse,$this->id);
    $docRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM `%s` WHERE id = %d', $this->doctype, $this->id
      )
    );
    foreach($this->items as $item){
      $this->SetTextColor(0,0,0);
      for ($l=$pos; $l <= $pos+$jitposfix ; $l++) {
        //Arbeitet die Zwischenpositionen durch falls der vorherige Artikel ein Stücklistenartikel war bei dem Teile ausgeblendet wurden
        $iszwichenpos = $this->DrawZwischenpositionen($l+$jitposfixbase);
      }
      $jitposfixbase += $jitposfix;
      if($item['keineeinzelartikelanzeigen'] == 1){
        $jitposfix = $item['anzahlunterartikel'];
      }else{
        $jitposfix = 0;
      }
      $item['name'] = ($langeartikelnummern?"\r\n\r\n":'').$this->app->erp->ReadyForPDF($item['name']);
      $item['desc'] = $this->app->erp->ReadyForPDF($item['desc']);
      $item['itemno'] = $this->app->erp->ReadyForPDF($item['itemno']);
      $item['herstellernummer'] = $this->app->erp->ReadyForPDF($item['herstellernummer']);
      $item['artikelnummerkunde'] = $this->app->erp->ReadyForPDF($item['artikelnummerkunde']);
      $item['lieferdatum'] = $this->app->erp->ReadyForPDF($item['lieferdatum']);
      $item['hersteller'] = $this->app->erp->ReadyForPDF($item['hersteller']);

      //TODO Soll einstellbar werden: Zeilenabstand in Tabelle normal mittel
      $cellhoehe  = 3;
      //position

      if($iszwichenpos && $belege_subpositionen && $pos > $posoffset)
      {
        $hauptnummer++;
        $posoffset = $pos;
      }
      if(isset($item['posausblenden']) && $item['posausblenden']){
        $posstr = '';
      }else{
        $pos++;
        $posstr = $pos;
      }

      if($belege_subpositionen && $hauptnummer)
      {
        $posstr = $hauptnummer.'.'.($pos-$posoffset);
      }
      $newlvl = isset($item['lvl'])?(int)$item['lvl']:0;


      $itemNoWidthold = $itemNoWidth;
      $posWidthold = $posWidth;
      if($belege_stuecklisteneinrueckenmm && $newlvl > 0)
      {
        $this->Cell($belege_stuecklisteneinrueckenmm * $newlvl,$cellhoehe,'');
        $posWidth -= $belege_stuecklisteneinrueckenmm * $newlvl;
        if($posWidth < 2* strlen($posstr))
        {
          $diff =  2* strlen($posstr) - $posWidth;
          $posWidth += $diff;
          $itemNoWidth -= $diff;
        }
      }
      if($belege_subpositionenstuecklisten)$posstr = $this->CalcPosString($posstr,$oldpostr, $hauptnummer, $oldlvl, $newlvl);
      $oldpostr = $posstr;
      $oldlvl = isset($item['lvl'])?(int)$item['lvl']:0;
      $this->Cell($posWidth,$cellhoehe,$posstr,0,0,$belege_stuecklisteneinrueckenmm?'':'C');
      //artikelnummer
      if($this->doctype==='arbeitsnachweis')
      {
        $this->Cell($taxWidth,$cellhoehe,trim($item['person']),0);

        $zeilenuntertext  = $this->getStyleElement('zeilenuntertext');
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);

        // ort
        $tmpy = $this->GetY();
        $tmpx = $this->GetX();
        $this->MultiCell($itemNoWidth,($zeilenuntertext/2),trim($item['itemno']),0); // 4 = abstand
        $tmpy2 = $this->GetY();
        $this->SetXY($tmpx+$itemNoWidth,$tmpy);
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      }
      else {
        //TODO BENE
        if($this->doctype==='lieferschein' && $this->getStyleElement('modul_verband')=='1'){
          $this->SetFont($this->GetFont(), '', $tabelleninhalt + 3);
        }else{
          $this->SetFont($this->GetFont(), '', $tabelleninhalt);
        }
        if(isset($item['itemno'])) {
          $this->Cell($itemNoWidth,$cellhoehe,$item['itemno'],0);
        }
        else {
          $this->Cell($itemNoWidth);
        }
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      }

      $position_x   = $this->GetX();
      $position_y   = $this->GetY();

// start am Ende der Zeile Zeichnen
      $this->SetAutoPageBreak(false,$this->getStyleElement("abstand_umbruchunten")); //2306BS
      // Artikel Name
      if($item['tax']!=='hidden' && $newlvl == 0){
        $this->SetFont($this->GetFont(), 'B', $tabelleninhalt);
      }
      if($item['tax'] === 'hidden' && $item['ohnepreis'] == 2 && $newlvl == 0){
        $this->SetFont($this->GetFont(), 'B', $tabelleninhalt);
      }
      if($this->getStyleElement("artikeleinheit")=='1'){
        $this->MultiCell($descWidth - $einheitWidth, $cellhoehe, $item['name'], 0, 'L', false);
      }
      else{
        $this->MultiCell($descWidth, $cellhoehe, $item['name'], 0, 'L', false);
      }

      $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      $this->SetAutoPageBreak(true,$this->getStyleElement('abstand_umbruchunten')); //2306BS

      $position_y_end_name   = $this->GetY();
//

      // wenn vorhanden Artikel Einheit


      if($this->getStyleElement('artikeleinheit')=='1'){
        $this->SetXY(($position_x + $descWidth - $einheitWidth), $position_y);
      }
      else{
        $this->SetXY(($position_x + $descWidth), $position_y);
      }


      if($this->doctype==='arbeitsnachweis'){
        $this->SetXY(($position_x + $descWidth), $position_y);
      }


      // Menge

      if($this->doctype==='zahlungsavis'){
        $this->Cell($amWidth, $cellhoehe, '', 0, 0, 'R');
      }
      else{
        $this->Cell($amWidth, $cellhoehe, $item['amount'], 0, 0, 'R');
      }

      if($this->doctype!=='lieferschein' && $this->doctype!=='arbeitsnachweis' && $this->doctype!=='produktion' && $this->doctype!=='preisanfrage') {
        if($this->getStyleElement('artikeleinheit')=='1')
        {
          if($item['unit']!='')
            $einheit = $item['unit'];
          else {
            if(!empty($item['artikel']))
            {
              $einheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE 
                  id='".$item['artikel']."' LIMIT 1");
            }else{
              $einheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE 
                  nummer='".$item['itemno']."' LIMIT 1");
            }
            if($einheit=='') {
              $einheit = $this->getStyleElement('artikeleinheit_standard');
            }
          }
          if(!empty($einheit) && !empty($documentLanguage)){
            $query = sprintf("SELECT ae.id FROM `artikeleinheit` AS `ae` 
              WHERE ae.einheit_de = '%s'",
                $this->app->DB->real_escape_string($einheit));
            $unitId = $this->app->DB->Select($query);
            if(!empty($unitId)){
              $query = sprintf("SELECT u.beschriftung FROM `uebersetzung` AS `u` 
                WHERE u.sprache = '%s' AND u.label='%s'",
                  $documentLanguage,'artikeleinheit_'.$unitId);
              $unitTranslation = $this->app->DB->Select($query);
              if(!empty($unitTranslation)){
                $einheit = $unitTranslation;
              }
            }
          }

          $this->Cell($einheitWidth,$cellhoehe,$this->app->erp->ReadyForPDF($einheit),0,0,'R');
        }

        //			if($item['tax']=="hidden") $item['tax']=="hidden";
        if($item['tax']!=='hidden')
        {
          if($this->ust_befreit>0) {
            $item['tax'] = 0;
          } else {
            if($item['tax'] === 'normal') {
              $item['tax'] = $umsatzsteuernormal - 1;
            }
            else {
              $item['tax'] = $umsatzsteuerermaessigt - 1;
            }
          }
          if(isset($item['steuersatz'])){
            $item['tax'] = $item['steuersatz'] / 100;
          }
        }


        // wenn steuerfrei komplett immer 0 steuer anzeigen
        $item['tmptax'] = $item['tax'] + 1;

        // standard anzeige mit steuer
        if(!$this->ust_spalteausblende){
          if($item['tax']==='hidden'){
            $this->Cell($taxWidth,$cellhoehe,"",0,0,'R');
          } else {
            $tax = $item['tax']; //= $tax; //="USTV"?0.19:0.07;
            $tax *= 100; $tax = $tax.'%';

            if($this->doctype==='zahlungsavis'){
              $this->Cell($taxWidth,$cellhoehe,"",0,0,'R');
            }
            else{
              $this->Cell($taxWidth, $cellhoehe, $item['ohnepreis'] ? '' : $tax, 0, 0, 'R');
            }
          }
        } else {
          //kleinunternehmer
          $this->Cell($taxWidth,$cellhoehe,"",0,0,'R');
        }

        if($this->doctype!=='lieferschein' && $this->doctype!=='produktion' && $this->doctype!=='preisanfrage') {
          // preis pro Artikel

          if($this->doctype!=='zahlungsavis')
          {
            if($item['tax']!=='hidden'){

              if($anzeigeBelegNettoAdrese){
                //if(($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
                //&& $this->getStyleElement("immerbruttorechnungen",$projekt)!="1")
                if(!$inventurohnepreis){
                  $this->Cell($priceWidth, $cellhoehe, $item['ohnepreis'] ? '' : $this->formatMoney((double)$item['price']), 0, 0, 'R');
                }
              }
              else{
                if(!$inventurohnepreis){
                  $this->Cell($priceWidth, $cellhoehe, $item['ohnepreis'] ? '' : $this->formatMoney((double)$item['price'] * $item['tmptax']), 0, 0, 'R');
                }
              }
            } else
            {
              if($item['ohnepreis']==2) {
                if(!$inventurohnepreis){
                  $this->Cell($priceWidth,$cellhoehe,$item['price'],0,0,'R');
                }
              } // text alternativ zu preis
              else {
                if(!$inventurohnepreis){
                  $this->Cell($priceWidth,$cellhoehe,$item['ohnepreis']?'':$this->formatMoney((double)$item['price']),0,0,'R');
                }
              }
            }
          } else {
            $this->Cell($priceWidth,$cellhoehe,"",0,0,'R');
          }

          // zentale rabatt spalte
          if($this->rabatt=='1') {
            $rabatt_string='';

            //rabatt
            if($item['grundrabatt'] > 0 || $item['rabatt1'] > 0 || $item['rabatt2'] > 0)
            {
              if($item['grundrabatt']>0) {
                $rabatt_string .= $item['grundrabatt']." %\r\n";
              }
              if($item['rabatt1']>0) {
                $rabatt_string .= $item['rabatt1']." %\r\n";
              }
              if($item['rabatt2']>0) {
                $rabatt_string .= $item['rabatt2']." %\r\n";
              }
              if($item['rabatt3']>0) {
                $rabatt_string .= $item['rabatt3']." %\r\n";
              }
              if($item['rabatt4']>0) {
                $rabatt_string .= $item['rabatt4']." %\r\n";
              }
              if($item['rabatt5']>0) {
                $rabatt_string .= $item['rabatt5']." %\r\n";
              }


              $tmpy = $this->GetY();
              $tmpx = $this->GetX();

              if($item['keinrabatterlaubt']=='1' || $item['rabatt']<=0 || $item['rabatt']==='') {
                $rabatt_or_porto = $this->app->DB->Select("SELECT id FROM artikel WHERE 
                    nummer='".$item['itemno']."' AND (porto='1' OR rabatt='1') LIMIT 1");
                if($rabatt_or_porto)
                  $rabatt_string='';
                else {
                  if($this->getStyleElement('modul_verband')=='1') {
                    $rabatt_string='SNP';
                  }
                  else {
                    $rabatt_string='';
                  }
                }
                if($item['keinrabatterlaubt']=='1' && $item['rabatt']<>0){
                  $rabatt_string = $item['rabatt'] . " %\r\n";
                }
              }

              // rabatt nur klein anzeigen wenn es mehr als einer ist
              if(strstr(trim($rabatt_string), PHP_EOL)) {
                $this->SetFont($this->GetFont(),'',6);
              }

              if($item['tax']!=='hidden'){
                $this->MultiCell($rabattWidth, $cellhoehe - 1, $rabatt_string, 0, 0);
              }
              else{
                $this->MultiCell($rabattWidth, $cellhoehe - 1, "", 0, 0);
              }

              $this->SetXY($tmpx+$rabattWidth,$tmpy);
              $this->SetFont($this->GetFont(),'',$tabelleninhalt);
            } else {
              if($item['rabatt']<>0){
                // && $item['keinrabatterlaubt']!="1")
                $this->Cell($rabattWidth, $cellhoehe, $item['ohnepreis'] ? '' : $item['rabatt'] . " %", 0, 0, 'R');
              }
              else
              {
                if($this->getStyleElement('modul_verband')=='1')
                {
                  $rabatt_or_porto = $this->app->DB->Select("SELECT id FROM artikel WHERE 
                      nummer='".$item['itemno']."' AND (porto='1' OR rabatt='1') LIMIT 1");
                  if($rabatt_or_porto){
                    $this->Cell($rabattWidth, $cellhoehe, '', 0, 0, 'R');
                  }
                  else{
                    $this->Cell($rabattWidth, $cellhoehe, 'SNP', 0, 0, 'R');
                  }
                } else {
                  $this->Cell($rabattWidth,$cellhoehe,"",0,0,'R');
                }
              }
            }
          }
          else {
            // anzeige ohne zentrale rabatt spalte
            if ($item['tax']==="hidden"){
              $this->Cell($priceWidth,$cellhoehe,"",0,0,'R');
            }
            else {
              if($anzeigeBelegNettoAdrese)
              //if(($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
              //   && $this->getStyleElement("immerbruttorechnungen",$projekt)!="1")
              {
                if(!$inventurohnepreis){
                  $this->Cell($priceWidth,$cellhoehe,$item['ohnepreis']?'':$this->formatMoney((double)$item['tprice']),0,0,'R');
                }
              }
              else{
                if(!$inventurohnepreis){
                  $this->Cell($priceWidth, $cellhoehe, $item['ohnepreis'] ? '' : $this->formatMoney((double)$item['tprice'] * $item['tmptax']), 0, 0, 'R');
                }
              }

              $this->Cell($rabattWidth,$cellhoehe,"",0,0,'R');
            }
          }
        }
        else {
          if($anzeigeBelegNettoAdrese)
          // if(($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
          //       && $this->getStyleElement("immerbruttorechnungen",$projekt)!="1")
            if(!$inventurohnepreis){
              $this->Cell($priceWidth,$cellhoehe,$item['ohnepreis']?'':$this->formatMoney((double)$item['price']),0,0,'R');
            }
          else{
            if(!$inventurohnepreis){
              $this->Cell($priceWidth, $cellhoehe, $item['ohnepreis'] ? '' : $this->formatMoney((double)$item['price'] * $item['tmptax']), 0, 0, 'R');
            }
          }
        }
        //$this->Cell($sumWidth,$cellhoehe,$this->formatMoney($item['tprice']).' '.$item['currency'],0,0,'R');
        if($this->rabatt=='1')
        {
          //gesamt preis
          if ($item['tax']==='hidden'){
            $this->Cell($priceWidth,$cellhoehe,'',0,0,'R');
          }
          else {
            if($this->rabatt=='1'){
              if($anzeigeBelegNettoAdrese){
                //if(($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
                //   && $this->getStyleElement("immerbruttorechnungen",$projekt)!="1")
                if(!$inventurohnepreis){
                  $this->Cell($sumWidth, $cellhoehe, $item['ohnepreis'] ? '' : $this->formatMoney((double)$item['tprice']), 0, 0, 'R');
                }
              }
              else{
                if(!$inventurohnepreis){
                  $this->Cell($sumWidth, $cellhoehe, $item['ohnepreis'] ? '' : $this->formatMoney((double)$item['tprice'] * $item['tmptax']), 0, 0, 'R');
                }
              }
            }
            else {
              if($anzeigeBelegNettoAdrese){
                // if(($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
                //   && $this->getStyleElement("immerbruttorechnungen",$projekt)!="1")
                if(!$inventurohnepreis){
                  $this->Cell($sumWidth, $cellhoehe, $item['ohnepreis'] ? '' : $this->formatMoney((double)$item['tprice']), 0, 0, 'R');
                }
              }
              else{
                if(!$inventurohnepreis){
                  $this->Cell($sumWidth, $cellhoehe, $item['ohnepreis'] ? '' : $this->formatMoney((double)$item['tprice'] * $item['tmptax']), 0, 0, 'R');
                }
              }
            }
          }
        }

      }
      else if(($this->doctype==='lieferschein' || $this->doctype==='preisanfrage') && $this->getStyleElement('artikeleinheit')=='1')
        {
          if($item['unit']!='')
            $einheit = $item['unit'];
          else {
            $einheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE
                nummer='".$item['itemno']."' LIMIT 1");
            if($einheit=='') {
              $einheit = $this->getStyleElement('artikeleinheit_standard');
            }
          }

          if(!empty($einheit) && !empty($documentLanguage)){
            $query = sprintf("SELECT ae.id FROM `artikeleinheit` AS `ae` 
            WHERE ae.einheit_de = '%s'",
              $this->app->DB->real_escape_string($einheit));
            $unitId = $this->app->DB->Select($query);
            if(!empty($unitId)){
              $query = sprintf("SELECT u.beschriftung FROM `uebersetzung` AS `u` 
              WHERE u.sprache = '%s' AND u.label='%s'",
                  $documentLanguage,'artikeleinheit_'.$unitId);
              $unitTranslation = $this->app->DB->Select($query);
              if(!empty($unitTranslation)){
                  $einheit = $unitTranslation;
              }
            }
          }

          $this->Cell($einheitWidth,$cellhoehe,$this->app->erp->ReadyForPDF($einheit),0,0,'R');
        }

      $this->Ln();
      if($this->getStyleElement('herstellernummerimdokument')=='1' && $item['herstellernummer']!='')
      {
        if($item['desc']!=''){
          $item['desc'] = $item['desc'] . "\r\n" . $this->app->erp->Beschriftung('dokument_herstellernummer') . ': ' . $item['herstellernummer'];
        }
        else{
          $item['desc'] = $this->app->erp->Beschriftung('dokument_herstellernummer') . ': ' . $item['herstellernummer'];
        }
      }

      $daten = $this->app->DB->SelectRow("SELECT laenge, breite, hoehe  FROM artikel WHERE id = '".$item['artikel']."'");
      if($this->getStyleElement('abmessungimdokument')=='1' && (!empty($daten['laenge']) || $daten['breite']!='' || $daten['hoehe']!='')) {
        $tmp = array();
        if($daten['laenge']<>0) {
          $tmp[]=number_format($daten['laenge'],2,',','.');
        }
        if($daten['breite']<>0) {
          $tmp[]=number_format($daten['breite'],2,',','.');
        }
        if($daten['hoehe']<>0) {
          $tmp[]=number_format($daten['hoehe'],2,',','.');
        }

        if(count($tmp) > 0)
        {
          if($item['desc']!=''){
            $item['desc'] = $item['desc'] . "\r\n" . $this->app->erp->Beschriftung('dokument_abmessung') . ': ' . implode('x', $tmp);
          }
          else{
            $item['desc'] = $this->app->erp->Beschriftung('dokument_abmessung') . ': ' . implode('x', $tmp);
          }
        }
      }

      if($item['lieferdatum']!='' && $item['lieferdatum']!='0000-00-00' && $item['lieferdatum']!=='00.00.0000')
      {
        if(strpos($item['lieferdatum'],"-")!==false){
          $item['lieferdatum'] = $this->app->erp->ReadyForPDF($this->app->String->Convert($item['lieferdatum'], '%1-%2-%3', '%3.%2.%1'));
        }

        if($item['lieferdatumkw']==1)
        {
          $ddate = $this->app->String->Convert($item['lieferdatum'],'%3.%2.%1','%1-%2-%3');
          $duedt = explode("-", $ddate);
          $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
          $week  = date('W/o', $date);
          $item['lieferdatum'] = $this->app->erp->Beschriftung('dokument_lieferdatumkw').' '.$week;
        }

        if($item['desc']!=''){
          $item['desc'] = $item['desc'] . "\r\n" . $this->app->erp->Beschriftung('dokument_lieferdatum') . ': ' . $item['lieferdatum'];
        }
        else{
          $item['desc'] = $this->app->erp->Beschriftung('dokument_lieferdatum') . ': ' . $item['lieferdatum'];
        }
      }


      if($this->getStyleElement('freifelderimdokument')=='1')
      {
        for($ifreifeld=1;$ifreifeld<=40;$ifreifeld++)
        {
          if($item['freifeld'.$ifreifeld]!='')
          {
            $freifeldbeschriftung = $this->app->erp->Beschriftung('artikel_freifeld' . $ifreifeld);
            $freifeldtyp = $this->getStyleElement('freifeld' . $ifreifeld.'typ');
            if($freifeldtyp==='select')
            {
              $freifeldbeschriftung = strstr($freifeldbeschriftung, '|', true);
            }

            if($item['desc']!=''){
              $item['desc'] = $item['desc'] . "\r\n" . $freifeldbeschriftung . ': ' . $item['freifeld' . $ifreifeld];
            }
            else{
              $item['desc'] = $freifeldbeschriftung . ': ' . $item['freifeld' . $ifreifeld];
            }
          }
        }
        if(strpos($item['desc'],'{{') !== false && $this->app->erp->ModulVorhanden('formeln')) {
          $item['desc'] = $this->app->erp->BerechneFormel($item['desc'], $this->table, $this->id, $pos, $item);
        }

        if(strpos($item['desc'],'{') !== false) {
          if(!empty($item['belegposition']) && !empty($this->doctype) && !empty($this->id)) {
            $item['desc'] = $this->parseBestBeforeBatchSn($item['desc'], $this->doctype, $this->id, $item['belegposition']);
          }
          $item['desc'] = $this->app->erp->ParseIfVars($item['desc']);
        }
      }

      if(strpos($item['desc'],'{') !== false) {
        if(!empty($item['belegposition']) && !empty($this->doctype) && !empty($this->id)){
          $item['desc'] = $this->parseBestBeforeBatchSn(
            $item['desc'], $this->doctype, $this->id, $item['belegposition']
          );
        }
      }

      if(!empty($this->doctype) && !empty($this->id) && strpos($item['desc'], '{') !== false) {
        $item['desc'] = $this->app->erp->ParseUserVars($this->doctype, $this->id ,$item['desc']);
      }

      if($item['artikelnummerkunde']!="" && $item['artikelnummerkunde']!='0')
      {
        if($item['desc']!=''){
          $item['desc'] = $item['desc'] . "\r\n" . $this->app->erp->Beschriftung('dokument_artikelnummerkunde') . ': ' . $item['artikelnummerkunde'];
        }
        else{
          $item['desc'] = $this->app->erp->Beschriftung('dokument_artikelnummerkunde') . ': ' . $item['artikelnummerkunde'];
        }
      }

      if($item['zolltarifnummer']!='' && $item['zolltarifnummer']!='0' && $this->doctype!='proformarechnung')
      {
        if($item['desc']!='') {
          $item['desc']=$item['desc']."\r\n".$this->app->erp->Beschriftung('dokument_zolltarifnummer').': '.$item['zolltarifnummer'].' '.($item['herkunftsland']!=''?$this->app->erp->Beschriftung('dokument_herkunftsland').': '.$item['herkunftsland']:'');
        }
        else {
          $item['desc']=$this->app->erp->Beschriftung('dokument_zolltarifnummer').': '.$item['zolltarifnummer'].' '.($item['herkunftsland']!=""?$this->app->erp->Beschriftung('dokument_herkunftsland').': '.$item['herkunftsland']:'');
        }
      }

      if($item['ean']!='' && $item['ean']!='0')
      {
        if($item['desc']!=''){
          $item['desc'] = $item['desc'] . "\r\n" . $this->app->erp->Beschriftung('dokument_ean') . ': ' . $item['ean'];
        }
        else{
          $item['desc'] = $this->app->erp->Beschriftung('dokument_ean') . ': ' . $item['ean'];
        }
      }

      if($item['zusammenfassen']){
        $item['desc'] = '';
      }

      $startpageBeforeDescription = $this->page;

      if($item['desc']!='' || !empty($item['steuertext'])) {
        //Herstellernummer einblenden wenn vorhanden und aktiviert
        $zeilenuntertext  = $this->getStyleElement('zeilenuntertext');
        $this->SetY($position_y_end_name+$this->getStyleElement('abstand_name_beschreibung'));
        $yBeforeDescription = $this->GetY();
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        if($belege_stuecklisteneinrueckenmm && $newlvl > 0){
          $this->Cell($belege_stuecklisteneinrueckenmm * $newlvl, $cellhoehe, '');
        }
        $this->Cell($posWidth);
        $this->Cell($itemNoWidth);
        if($this->doctype==='arbeitsnachweis') {
          $this->Cell($taxWidth);
        }

        if($this->doctype==='lieferschein' && $this->getStyleElement('modul_verband')=='1'){
          $this->SetFont($this->GetFont(), '', $tabelleninhalt + 1);
        }

        if($this->getStyleElement('briefhtml')=='1')
        {
          $html = $this->app->erp->ReadyForPDF($this->app->erp->RemoveNewlineAfterBreaks($item['desc']));
          if($this->getStyleElement('artikeleinheit')=='1')
          {
            if($this->getStyleElement('breite_artikelbeschreibung'))
            {
              $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$this->WriteHTML($html),0,'L'); // 4 = abstand
              if(!empty($item['steuertext'])){
                $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
            else
            {
              $this->MultiCell($descWidth-$einheitWidth,($zeilenuntertext/2),$this->WriteHTMLCell($descWidth-$einheitWidth,$html),0,'L'); // 4 = abstand //ALT
              if(!empty($item['steuertext'])){
                $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
          }
          else
          {
            if($this->getStyleElement('breite_artikelbeschreibung')=='1')
            {
              $this->MultiCell($descWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$this->WriteHTML($html),0,'L'); // 4 = abstand
              if(!empty($item['steuertext'])){
                $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
            else
            {
              $this->MultiCell($descWidth,($zeilenuntertext/2),$this->WriteHTMLCell($descWidth,$html),0,'L'); // 4 = abstand //ALT
              if(!empty($item['steuertext'])){
                $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
          }
        } else {
          if($this->getStyleElement('artikeleinheit')=='1')
          {
            if($this->getStyleElement('breite_artikelbeschreibung'))
            {
              $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand
              if(!empty($item['steuertext'])){
                $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
            else
            {
              $this->MultiCell($descWidth-$einheitWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand //ALT
              if(!empty($item['steuertext'])){
                $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
          }
          else
          {
            if($this->getStyleElement('breite_artikelbeschreibung')=='1')
            {
              $this->MultiCell($descWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand
              if(!empty($item['steuertext'])){
                $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
            else
            {
              $this->MultiCell($descWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand //ALT
              if(!empty($item['steuertext'])){
                $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
          }
        }

        $this->Cell($taxWidth);
        $this->Cell($amWidth);
        $this->Ln();
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);

        $zeilenuntertext  = $this->getStyleElement('zeilenuntertext');
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        $this->Cell($posWidth);
        $this->Cell($itemNoWidth);
        if($this->doctype==='arbeitsnachweis') {
          $this->Cell($taxWidth);
        }
        if($this->getStyleElement('artikeleinheit')=='1'){
          $this->MultiCell($descWidth - $einheitWidth, 4, '', 0); // 4 = abstand zwischen Artikeln
        }
        else{
          $this->MultiCell($descWidth, 4, '', 0); // 4 = abstand zwischen Artikeln
        }
        $this->Cell($taxWidth);
        $this->Cell($amWidth);
        $this->Ln();
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
        $yAfterDescription = $this->GetY();
      } else {

        $zeilenuntertext  = $this->getStyleElement('zeilenuntertext');
        $this->SetY($position_y_end_name);
        $yBeforeDescription = $this->GetY();
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        $this->Cell($posWidth);
        $this->Cell($itemNoWidth);
        if($this->doctype==='arbeitsnachweis')
        {
          $this->Cell($taxWidth);
        }
        if($this->getStyleElement('artikeleinheit')=='1')
        {
          $this->MultiCell($descWidth-$einheitWidth,3,trim($item['desc']),0); // 4 = abstand
          if(!empty($item['steuertext']))
          {
            $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
            $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
          }
        }
        else
        {
          $this->MultiCell($descWidth,3,trim($item['desc']),0); // 4 = abstand
          if(!empty($item['steuertext']))
          {
            $this->SetX($this->GetX()+$posWidth+$itemNoWidth);
            $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
          }
        }

        if(!empty($staffelpreiseanzeigen)){
          $this->MultiCell($posWidth+$itemNoWidth+$descWidth+$amWidth+$taxWidth+$sumWidth+$priceWidth,($zeilenuntertext/2),trim($staffelpreistext),0,'R');
        }

        $this->Cell($taxWidth);
        $this->Cell($amWidth);
        $this->Ln();
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
        $yAfterDescription = $this->GetY();
      }

      $itemNoWidth = $itemNoWidthold;
      $posWidth = $posWidthold;



      if(
        ($this->doctype == "auftrag" && $this->getStyleElement("beleg_artikelbild")) ||
        ($this->doctype == "lieferschein" && $this->getStyleElement("lieferschein_artikelbild")) ||
        ($this->doctype == "rechnung" && $this->getStyleElement("rechnung_artikelbild")) ||
        ($this->doctype == "bestellung" && $this->getStyleElement("bestellung_artikelbild")) ||
        ($this->doctype == "gutschrift" && $this->getStyleElement("gutschrift_artikelbild")) ||
        ($this->doctype == "angebot" && $this->getStyleElement("angebot_artikelbild"))
      ){
        $datei = $this->app->DB->Select("SELECT datei FROM `datei_stichwoerter` WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='" . $item['artikel'] . "' ORDER by sort ASC LIMIT 1");
        if(!empty($datei)){
          $datei = $this->app->DB->Select("SELECT id FROM datei_version WHERE datei = '$datei' ORDER BY id DESC LIMIT 1");
        }
        $startpage = $this->page;
        if(!empty($datei)){
          $xBeforePic = $this->GetX();
          $folder = $this->app->erp->GetDMSPath($datei);
          if(file_exists($folder . '/' . $datei)){
            if(!class_exists('image')) include_once(__DIR__ . '/../class.image.php');
            $img = new image($this->app);
            $breite = 20 * 10;
            $hoehe = $breite;

            [$width, $height] = getimagesize($folder . '/' . $datei);
            if($width > 0 && $height > 0){
              $scalex = $breite / $width;
              $scaley = $hoehe / $height;
              if($scalex < $scaley){
                $hoehe /= $scaley / $scalex;
                $hoehe = ceil($hoehe);
              }else{
                $breite /= $scalex / $scaley;
                $breite = ceil($breite);
              }

              $cachefolder = $this->app->erp->CreateDMSPath($this->app->Conf->WFuserdata . '/dms/' . $this->app->Conf->WFdbname . '/cache', $datei, true) . "/";
              $dateiname = $cachefolder . $datei . '_' . $breite . '_' . $hoehe;

              if(!is_file($dateiname)) $img->scaledPicByFileId($datei, $breite, $hoehe, false, 'jpg');
              if(is_file($dateiname)){
                [$width, $height] = getimagesize($dateiname);
                //$value['datei'] = array('file'=>$folder.'/cache/'.$datei.'_'.$breite.'_'.$breite,'width'=>$width/10,'height'=>$height/10);

                $this->SetX($posWidth + (int)$this->getStyleElement("abstand_seitenrandlinks") - 5);
                $dateityp = mime_content_type($dateiname);
                $dateityp = substr($dateityp,6);
                if($dateityp == ''){
                  $dateityp = 'jpg';
                }

                if($startpageBeforeDescription != $this->page){
                  $this->SetY($this->abseite2y);
                  $this->SetX($posWidth + (int)$this->getStyleElement("abstand_seitenrandlinks") - 5);
                  $position_y_end_name = $this->abseite2y;
                }
                $this->Image($dateiname, $this->GetX(), $position_y_end_name + 2, $width / 10, $hoehe / 10,$dateityp);
                $this->SetXY($this->GetX(), $position_y_end_name + 20);
                $yAfterPic = $this->GetY();
              }
            }
          }

          if($yAfterPic < $yAfterDescription){
            $this->SetY($yAfterDescription);
          }

          $this->SetX($xBeforePic);

          $y_nach_bild = $this->GetY();

          if($yAfterDescription < $yAfterPic+5 && $startpage == $this->page){
            $this->SetY($y_nach_bild+5);
          }
          if($this->GetY() > 240){
            $this->AddPage();
          }

        }

      }
    }

    for ($l=$pos; $l <= $pos+$jitposfix ; $l++) {
      //Letzten Artikel durcharbeiten
      $this->DrawZwischenpositionen($l+$jitposfixbase, $umsatzsteuerermaessigtProzent,$umsatzsteuernormalProzent);
    }

    //$this->Line($this->GetX(), $this->GetY(), $this->getStyleElement("abstand_seitenrandlinks")+195-$this->getStyleElement("abstand_seitenrandrechts"), $this->GetY());

    $this->Line($this->getStyleElement("abstand_seitenrandlinks")+1, $this->GetY(), 210-$this->getStyleElement("abstand_seitenrandrechts"), $this->GetY());
  }

  function DrawZwischenpositionen($pos,$umsatzsteuerermaessigt = null,$umsatzsteuernormal = null) {
    $ret = false;
    if(is_null($umsatzsteuerermaessigt))
    {
      $umsatzsteuerermaessigt = $this->app->erp->GetSteuersatzErmaessigt(false,$this->doctypeid,$this->table);
    }
    if(is_null($umsatzsteuernormal))
    {
      $umsatzsteuernormal = $this->app->erp->GetSteuersatzNormal(false,$this->doctypeid,$this->table);
    }
    if($this->table != 'lieferschein' && $this->table != 'preisanfrage')
    {
      $zwischenpositionen = $this->app->DB->SelectArr("SELECT * FROM beleg_zwischenpositionen WHERE doctype='".$this->table."' AND doctypeid='".$this->doctypeid."' AND pos='$pos' ORDER by sort");
    }else{
      $zwischenpositionen = $this->app->DB->SelectArr("SELECT * FROM beleg_zwischenpositionen WHERE doctype='".$this->table."' AND doctypeid='".$this->doctypeid."' AND pos='$pos' AND postype <> 'gruppensumme' AND postype <> 'gruppensummemitoptionalenpreisen' AND postype <> 'zwischensumme' ORDER by sort");
    }

    $projekt = $this->app->DB->Select("SELECT projekt FROM ".$this->table." WHERE id = '".$this->doctypeid."' LIMIT 1");
    $czwischenpositionen = !empty($zwischenpositionen)?count($zwischenpositionen):0;
    for($i=0;$i<$czwischenpositionen;$i++)
    {
      $data = json_decode($zwischenpositionen[$i]['wert'], true);
      switch($zwischenpositionen[$i]['postype'])
      {
        case "gruppe":
          $ret = true;
          $schriftgroesse = $this->FontSizePt;
          $schriftgroesse_alt = $schriftgroesse;
          if($schriftgroesse == 0)$schriftgroesse = 7;
          if(!empty($data['Schriftgroesse']))
          {
            $schriftgroesse = $data['Schriftgroesse'];
            if($schriftgroesse == 0)$schriftgroesse = 7;
            $this->SetFontSize($schriftgroesse);
          }
          $fett = true;
          if(isset($data['Fett']))
          {
            if($data['Fett'] === false || $data['Fett'] === 'false')$fett = false;
            if($data['Fett'] === true || $data['Fett'] === 'true')$fett = true;
          }
          $unterstrichen = false;
          if(isset($data['Unterstrichen']))
          {
            $unterstrichen = $data['Unterstrichen'];
          }
          $this->underline = $unterstrichen;
          $html = ($fett?"<b>":"").$data['name'].($fett?"</b>":"");
          if(!empty($data['Abstand_Oben']))
          {
            $this->Ln((int)$data['Abstand_Oben']);
          }else{
            $this->Ln(0);
          }
          $schriftgroesse = 7;
          if(!empty($data['Schriftgroesse']))
          {
            $schriftgroesse = $data['Schriftgroesse'];
          }
          $this->SetFontSize($schriftgroesse_alt);
          $x = $this->GetX();
          $abstand_links = 0;
          if(isset($data['Abstand_Links']))
          {
            $abstand_links = (int)$data['Abstand_Links'];
            $this->SetX($x+$abstand_links);
          }

          $this->MultiCell(0,4,$this->WriteHTML($html));
          $this->SetX($x);
          $this->Ln();
          $this->underline = false;
          if($data['kurztext']!="")
          {

            if(isset($data['Kurztext_Unterstrichen']))
            {
              $this->underline = $data['Kurztext_Unterstrichen'];
            }
            $x = $this->GetX();
            $abstand_links = 0;
            if(isset($data['Kurztext_Abstand_Links'])){
              $abstand_links = (int)$data['Kurztext_Abstand_Links'];
              $this->SetX($x+$abstand_links);
            }
            elseif(isset($data['Abstand_Links']))
            {
              $abstand_links = (int)$data['Abstand_Links'];
              $this->SetX($x+$abstand_links);
            }
            $this->MultiCell(0,4,$this->WriteHTML($data['kurztext']));
            $this->SetX($x);
            if(!empty($data['Abstand_Unten']))
            {
              $this->Ln((int)$data['Abstand_Unten']);
            }else{
              $this->Ln(10);
            }
          }
          $this->SetFontSize($schriftgroesse_alt);
          $this->underline = false;
        break;

        case "bild":
          if($data['bild'])
          {
            $nochtext = '';
            if($data['kurztext']!="")
            {
              $kurztexta = explode('{BILD}', $data['kurztext'], 2);
            }else {
              $kurztexta[] = '';
            }
            if(count($kurztexta) > 1)$nochtext = $kurztexta[1];
            $data['kurztext'] = $kurztexta[0];

            $__x = $this->GetX();
            //$ret = true;
            $schriftgroesse = $this->FontSizePt;
            $schriftgroesse_alt = $schriftgroesse;
            if($schriftgroesse == 0)$schriftgroesse = 7;
            if(!empty($data['Schriftgroesse']))
            {
              $schriftgroesse = $data['Schriftgroesse'];
              if($schriftgroesse == 0)$schriftgroesse = 7;
              $this->SetFontSize($schriftgroesse);
            }
            $fett = true;
            if(isset($data['Fett']))
            {
              if($data['Fett'] === false || $data['Fett'] === 'false')$fett = false;
              if($data['Fett'] === true || $data['Fett'] === 'true')$fett = true;
            }
            $unterstrichen = false;
            if(isset($data['Unterstrichen']))
            {
              $unterstrichen = $data['Unterstrichen'];
            }
            $this->underline = $unterstrichen;
            $html = ($fett?"<b>":"").$data['name'].($fett?"</b>":"");
            if(!empty($data['Abstand_Oben']))
            {
              $this->Ln((int)$data['Abstand_Oben']);
            }else{
              $this->Ln(0);
            }
            $schriftgroesse = 7;
            if(!empty($data['Schriftgroesse']))
            {
              $schriftgroesse = $data['Schriftgroesse'];
            }
            $this->SetFontSize($schriftgroesse_alt);
            $x = $this->GetX();
            $abstand_links = 0;
            if(isset($data['Abstand_Links']))
            {
              $abstand_links = (int)$data['Abstand_Links'];
              $this->SetX($x+$abstand_links);
            }

            $this->MultiCell(0,4,$this->WriteHTML($html));
            $this->SetX($x);
            $this->Ln();
            $this->underline = false;
            if($data['kurztext']!="")
            {

              if(isset($data['Kurztext_Unterstrichen']))
              {
                $this->underline = $data['Kurztext_Unterstrichen'];
              }
              $x = $this->GetX();
              $abstand_links = 0;
              if(isset($data['Kurztext_Abstand_Links'])){
                $abstand_links = (int)$data['Kurztext_Abstand_Links'];
                $this->SetX($x+$abstand_links);
              }
              elseif(isset($data['Abstand_Links']))
              {
                $abstand_links = (int)$data['Abstand_Links'];
                $this->SetX($x+$abstand_links);
              }
              $this->MultiCell(0,4,$this->WriteHTML($data['kurztext']));
              $this->SetX($x);
              if($nochtext == '')
              {
                if(!empty($data['Abstand_Unten']))
                {
                  $this->Ln((int)$data['Abstand_Unten']);
                }else{
                  $this->Ln(10);
                }
              }
            }
            $this->SetFontSize($schriftgroesse_alt);
            $this->underline = false;

            $posWidth     = $this->getStyleElement("breite_position");
            $amWidth     = $this->getStyleElement("breite_menge");
            $itemNoWidth = $this->getStyleElement("breite_nummer");
            $datei = $data['bild'];
            if(!empty($datei))
            {
              $datei = $this->app->DB->Select("SELECT id FROM datei_version WHERE datei = '$datei' ORDER BY id DESC LIMIT 1");
            }
            if(!empty($datei))
            {
              $folder = $this->app->erp->GetDMSPath($datei);
              if(file_exists($folder.'/'.$datei))
              {
                if(!class_exists('image'))include_once(__DIR__.'/../class.image.php');
                $img = new image($this->app);
                if(isset($data['bildbreite']) && (int)$data['bildbreite'] > 0)
                {
                  $breite = $data['bildbreite'] * 10;
                }else{
                  $breite = 30 * 10;
                }
                if(isset($data['bildhoehe']) && (int)$data['bildhoehe'] > 0)
                {
                  $hoehe = $data['bildhoehe'] * 10;
                }else{
                  $hoehe = $breite;
                }
                [$width, $height] = getimagesize($folder.'/'.$datei);
                if($width > 0 && $height > 0)
                {
                  $scalex = $breite / $width;
                  $scaley = $hoehe / $height;
                  if($scalex < $scaley)
                  {
                    $hoehe /= $scaley / $scalex;
                    $hoehe = ceil($hoehe);
                  }else{
                    $breite /= $scalex / $scaley;
                    $breite = ceil($breite);
                  }

                  $cachefolder = $this->app->erp->CreateDMSPath($this->app->Conf->WFuserdata.'/dms/'.$this->app->Conf->WFdbname.'/cache', $datei, true)."/";
                  $dateiname = $cachefolder.$datei.'_'.$breite.'_'.$hoehe;

                  if(!is_file($dateiname))$img->scaledPicByFileId($datei, $breite, $hoehe, false, 'jpg');
                  if(is_file($dateiname))
                  {
                    [$width, $height] = getimagesize($dateiname);
                    //$value['datei'] = array('file'=>$folder.'/cache/'.$datei.'_'.$breite.'_'.$breite,'width'=>$width/10,'height'=>$height/10);
                    $x = $this->GetX();
                    $y = $this->GetY();
                    $this->SetX($posWidth+$itemNoWidth+(int)$this->getStyleElement("abstand_seitenrandlinks"));
                    $this->Image($dateiname, $this->GetX(), $this->GetY(),$width / 10, $hoehe / 10, 'jpg');
                    if($nochtext == '')
                    {
                      $this->Cell($picwidth,6,'',0,0,'C');
                    }
                    $this->SetXY($this->GetX(), $y + $height / 10 + ($nochtext == ''?5:0));
                  }
                }
              }
            }
            $this->SetXY($__x, $this->GetY());

            if($nochtext !== '')
            {
              $data['kurztext'] = $nochtext;
              $__x = $this->GetX();
              $ret = true;
              $schriftgroesse = $this->FontSizePt;
              $schriftgroesse_alt = $schriftgroesse;
              if($schriftgroesse == 0)$schriftgroesse = 7;
              if(!empty($data['Schriftgroesse']))
              {
                $schriftgroesse = $data['Schriftgroesse'];
                if($schriftgroesse == 0)$schriftgroesse = 7;
                $this->SetFontSize($schriftgroesse);
              }
              $fett = true;
              if(isset($data['Fett']))
              {
                if($data['Fett'] === false || $data['Fett'] === 'false')$fett = false;
                if($data['Fett'] === true || $data['Fett'] === 'true')$fett = true;
              }

              $this->SetFontSize($schriftgroesse_alt);
              $x = $this->GetX();
              $this->underline = false;
              if($data['kurztext']!="")
              {

                if(isset($data['Kurztext_Unterstrichen']))
                {
                  $this->underline = $data['Kurztext_Unterstrichen'];
                }
                $x = $this->GetX();
                $abstand_links = 0;
                if(isset($data['Kurztext_Abstand_Links'])){
                  $abstand_links = (int)$data['Kurztext_Abstand_Links'];
                  $this->SetX($x+$abstand_links);
                }
                elseif(isset($data['Abstand_Links']))
                {
                  $abstand_links = (int)$data['Abstand_Links'];
                  $this->SetX($x+$abstand_links);
                }
                $this->MultiCell(0,4,$this->WriteHTML($data['kurztext']));
                $this->SetX($x);
                if(!empty($data['Abstand_Unten']))
                {
                  $this->Ln((int)$data['Abstand_Unten']);
                }else{
                  $this->Ln(10);
                }
              }
              $this->SetFontSize($schriftgroesse_alt);
              $this->underline = false;
            }
          }
        break;
        case "seitenumbruch":
          $this->AddPage();
        break;

        case "gruppensumme":
        case "gruppensummemitoptionalenpreisen":
        case "zwischensumme":
          $ret = true;
          $posWidth     = $this->getStyleElement("breite_position");
          $amWidth     = $this->getStyleElement("breite_menge");
          $itemNoWidth = $this->getStyleElement("breite_nummer");
          $einheitWidth    = $this->getStyleElement("breite_einheit");
          $descWidth    = $this->getStyleElement("breite_artikel");
          $taxWidth    = $this->getStyleElement("breite_steuer");

          if($this->doctype=="arbeitsnachweis")
          {
            $itemNoWidth = 20;
            $taxWidth = 40;
            $descWidth   = 95;
          }
          else if($this->doctype!="lieferschein" && $this->doctype!="produktion" && $this->doctype!="preisanfrage") {
            if($descWidth <=0)
              $descWidth   = 76;

            if($taxWidth <=0)
              $taxWidth   = 15;
          }
          else
          {
            $itemNoWidth = 30;
            $descWidth   = 91;
            if($taxWidth <=0)
              $taxWidth   = 15;
          }
          $priceWidth = 20;
          $sumWidth   = 20;
          $rabattWidth   = 15;


          $schriftgroesse = $this->FontSizePt;
          $schriftgroesse_alt = $schriftgroesse;
          if($schriftgroesse == 0)$schriftgroesse = 4;
          if(!empty($data['Schriftgroesse']))
          {
            $schriftgroesse = $data['Schriftgroesse'];
            if($schriftgroesse == 0)$schriftgroesse = 4;
            $this->SetFontSize($schriftgroesse);
          }
          $fett = true;
          if(isset($data['Fett']))
          {
            if($data['Fett'] === false || $data['Fett'] === 'false')$fett = false;
            if($data['Fett'] === true || $data['Fett'] === 'true')$fett = true;
          }
          $unterstrichen = false;
          if(isset($data['Unterstrichen']))
          {
            $unterstrichen = $data['Unterstrichen'];
          }
          $this->underline = $unterstrichen;

          switch($zwischenpositionen[$i]['postype'])
          {
            case "gruppensumme":
            case "gruppensummemitoptionalenpreisen":
              $maxpos_untergruppedrueber = (int)$this->app->DB->Select("SELECT pos FROM beleg_zwischenpositionen WHERE doctype='".$this->doctype."' AND doctypeid='".$this->doctypeid."' AND pos < '$pos' AND postype='gruppe' ORDER by pos DESC LIMIT 1");

              if($this->doctype=="angebot")
              {
                $subwhere = " AND (explodiert_parent = 0 OR 0 = ifnull((SELECT id FROM angebot_position WHERE id = ap.explodiert_parent AND berechnen_aus_teile = 1 LIMIT 1),0))";
                if($zwischenpositionen[$i]['postype'] !== 'gruppensummemitoptionalenpreisen'){
                  $subwhere = ' AND optional!=1 '.$subwhere;
                }
                $zwischenpositionen[$i]['postype'] = 'gruppensumme';
              }
              if($this->doctype=="bestellung")
              {
                $summe = $this->formatMoney($this->app->DB->Select("SELECT SUM(preis*menge) FROM ".$this->doctype."_position WHERE ".$this->doctype."='".$this->doctypeid."' AND sort <='$pos' AND sort >'$maxpos_untergruppedrueber' $subwhere"));
              }else{
                $anzeigesteuerbelege = $this->app->DB->Select("SELECT anzeigesteuerbelege FROM projekt WHERE id = ".$projekt);
                if(
                  (
                    (
                      $this->anrede=="firma" ||
                      $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt,$this->table,$this->id) ||
                      $this->doctype=="bestellung" ||
                      $this->getStyleElement("immernettorechnungen",$projekt)=="1" ||
                      $anzeigesteuerbelege =='1'
                    ) &&
                    $this->getStyleElement("immerbruttorechnungen",$projekt)!="1") ||
                    $this->ust_befreit
                ) {
                  $summe = $this->formatMoney($this->app->DB->Select("SELECT SUM((preis-(preis/100*rabatt))*menge) FROM " . $this->doctype . "_position ap WHERE " . $this->doctype . "='" . $this->doctypeid . "' AND sort <='$pos' AND sort >'$maxpos_untergruppedrueber' $subwhere"));
                }else{
                  $summe = $this->formatMoney($this->app->DB->Select("SELECT SUM((preis-(preis/100*rabatt))*menge)*
                    (1 + if(ifnull(steuersatz,-1) < 0,if(umsatzsteuer = 'befreit',0,if(umsatzsteuer = 'ermaessigt',$umsatzsteuerermaessigt,$umsatzsteuernormal)),steuersatz
                    )/100) FROM " . $this->doctype . "_position ap WHERE " . $this->doctype . "='" . $this->doctypeid . "' AND sort <='$pos' AND sort >'$maxpos_untergruppedrueber' $subwhere"));
                }
              }
            break;
            default:
              //zwischensumme
              if($this->doctype=="angebot")
              {
                $subwhere = " AND optional!=1 AND (explodiert_parent = 0 OR 0 = ifnull((SELECT id FROM angebot_position WHERE id = ap.explodiert_parent AND berechnen_aus_teile = 1 LIMIT 1),0))";
              }
              if($this->doctype=="bestellung")
              {
                $summe = $this->formatMoney($this->app->DB->Select("SELECT SUM(preis*menge) FROM ".$this->doctype."_position WHERE ".$this->doctype."='".$this->doctypeid."' AND sort <='$pos' $subwhere"));
              }else{
                if((($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt,$this->table,$this->id) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
                  && $this->getStyleElement("immerbruttorechnungen",$projekt)!="1") || $this->ust_befreit ) {
                  $summe = $this->formatMoney($this->app->DB->Select("SELECT SUM((preis-(preis/100*rabatt))*menge) FROM " . $this->doctype . "_position ap WHERE " . $this->doctype . "='" . $this->doctypeid . "' AND sort <='$pos' $subwhere"));
                }else{
                  $summe = $this->formatMoney($this->app->DB->Select("SELECT SUM((preis-(preis/100*rabatt))*menge)*
                    (1 + if(ifnull(steuersatz,-1) < 0,if(umsatzsteuer = 'befreit',0,if(umsatzsteuer = 'ermaessigt',$umsatzsteuerermaessigt,$umsatzsteuernormal)),steuersatz
                    )/100)  FROM " . $this->doctype . "_position ap WHERE " . $this->doctype . "='" . $this->doctypeid . "' AND sort <='$pos' $subwhere"));
                }
              }
            break;
          }

          $beschriftung_zeile = ucfirst($zwischenpositionen[$i]['postype']);

          if($data['name']=="")
            $html = ($fett?"<b>":"").$beschriftung_zeile.($fett?"</b>":"");
          else
            $html = ($fett?"<b>":"").$data['name'].($fett?"</b>":"");
          if(!empty($data['Abstand_Oben']))
          {
            $this->Ln((int)$data['Abstand_Oben']);
          }else{
            $this->Ln(0);
          }
          $x = $this->GetX();

          if(isset($data['Abstand_Links']) && $data['Abstand_Links'] > 0)
          {
            $abstand_links = (int)$data['Abstand_Links'];
            $this->SetX($x+$abstand_links);
          }else{
            $abstand_links = $posWidth +$itemNoWidth;
          }

          $this->SetX($x+$abstand_links);
          $this->Cell($descWidth,4,$this->WriteHTML($html));
          $this->SetX($x+$abstand_links+$descWidth);
          //$this->SetX($x);

          $rahmen_links = false;
          $rahmen_rechts = false;
          $rahmen_oben = false;
          $rahmen_unten = false;
          if(isset($data['Rahmen_Links']))
          {
            $rahmen_links = $data['Rahmen_Links'];
          }
          if(isset($data['Rahmen_Rechts']))
          {
            $rahmen_rechts = $data['Rahmen_Rechts'];
          }
          if(isset($data['Rahmen_Oben']))
          {
            $rahmen_oben = $data['Rahmen_Oben'];
          }
          if(isset($data['Rahmen_Unten']))
          {
            $rahmen_unten = $data['Rahmen_Unten'];
          }
          $rahmen = '';
          if($rahmen_links)$rahmen .= 'L';
          if($rahmen_rechts)$rahmen .= 'R';
          if($rahmen_oben)$rahmen .= 'T';
          if($rahmen_unten)$rahmen .= 'B';
          $ausrichtung = 'R';
          if(isset($data['Text_Ausrichtung']))
          {
            $ausrichtung = $data['Text_Ausrichtung'];
          }
          $this->Cell($priceWidth+$amWidth+$taxWidth+$priceWidth,4,$summe,$rahmen,0,$ausrichtung);
          if(!empty($data['Abstand_Unten']))
          {
            $this->Ln((int)$data['Abstand_Unten']);
          }else{
            $this->Ln(10);
          }
          $this->SetFontSize($schriftgroesse_alt);
          $this->underline = false;
        break;
      }
    }
    return $ret;
  }


  /*
     totals (
     totalArticles   - Summe aller Artikelpreise
     modeOfDispatch   - Versandart
     priceOfDispatch  - Versandkosten
     modeOfPayment    - Zahlungsweise
     priceOfPayment  - Kosten der Zahlungsweise
     total           = totalArticles + priceOfDispatch + priceOfPayment
     totalTaxV      - Summe voller Steuersatz
     totalTaxR      - Summe reduzierter Steuersatz
     )
   */
  public function renderTotals() {

    if($this->doctype === 'angebot' && $this->app->DB->Select("SELECT gesamtsummeausblenden FROM angebot WHERE id = '".$this->doctypeid."'")){
      $this->Ln(8);
      return '';
    }

    if($this->doctype === 'inventur' && $this->app->DB->Select("SELECT noprice FROM inventur WHERE id = '".$this->id."'")){
      $this->Ln(8);
      return '';
    }

    $this->SetY($this->GetY()+1);

    $differenz_wegen_abstand = $this->getStyleElement('abstand_gesamtsumme_lr');

    if($this->doctype!='lieferschein' && $this->doctype!='arbeitsnachweis' && $this->doctype!='preisanfrage') {
      //$this->Line(110, $this->GetY(), 190, $this->GetY());
      $this->Ln(1);
      $this->SetFont($this->GetFont(),'',$this->getStyleElement('schriftgroesse_gesamt'));
      $this->Cell($differenz_wegen_abstand,2,'',0);
      if($this->getStyleElement('kleinunternehmer')!='1' && $this->doctype!='zahlungsavis'){
        $nettoText = $this->app->erp->Beschriftung('dokument_gesamtnetto');
        $nettoAmount = $this->formatMoney(round((double)$this->totals['totalArticles'], 2), 2).' '.$this->waehrung;
        $doctype = $this->doctype;
        $doctypeid = !empty($this->doctypeid)?$this->doctypeid: $this->id;
        $this->app->erp->RunHook('class_briefpapier_render_netto', 4, $doctype, $doctypeid, $nettoText, $nettoAmount);
        $this->Cell(30,5,$nettoText,0,0,'L');
        $this->Cell(40,5,$nettoAmount,0,'L','R');
      } else {
        //kleinunzernehmer
        $this->Cell(30,5,'',0,0,'L');
        $this->Cell(40,5,'',0,'L','R');
      }
      $this->Ln();

      if(isset($this->totals['modeOfDispatch'])) {
        $versand = 'Versand: '.$this->totals['modeOfDispatch'];
      }
      else {
        $versand = 'Versandkosten: ';
      }
      if(isset($this->totals['priceOfDispatch'])) {
        $this->Cell($differenz_wegen_abstand,2,'',0);
        $this->Cell(30,5,$versand,0,'L','L');
        $this->Cell(40,5,$this->formatMoney((double)$this->totals['priceOfDispatch'], 2).' '.$this->waehrung,0,'L','R');
      }
      //$this->Ln();

      if(isset($this->totals['priceOfPayment']) && $this->totals['priceOfPayment']!='0.00'){
        $this->Cell($differenz_wegen_abstand,2,'',0);
        $this->Cell(30,5,$this->totals['modeOfPayment'],0,'L','L');
        $this->Cell(40,5,$this->formatMoney((double)$this->totals['priceOfPayment'], 2).' '.$this->waehrung,0,'L','R');
        $this->Ln();
      }

      $this->SetY($this->GetY());
      $this->SetFont($this->GetFont(),'',$this->getStyleElement("schriftgroesse_gesamt_steuer"));


      if(isset($this->totals['totalTaxV']) && $this->totals['totalTaxV']!="0.00"){
        $this->Cell($differenz_wegen_abstand,1,'',0);

        if($this->getStyleElement('kleinunternehmer')!='1'){
          if(!empty($this->doctype) && !empty($this->id) && is_numeric($this->id)){
            $projekt = $this->app->DB->Select('SELECT projekt FROM `' . $this->doctype . '` WHERE id = ' . $this->id);
            $adresse = $this->app->DB->Select('SELECT adresse FROM `' . $this->doctype . '` WHERE id = ' . $this->id);
          }else{
            $projekt = 0;
            $adresse = 0;
          }

          if($this->app->erp->AnzeigeBelegNettoAdresse($this->anrede, $this->doctype, $projekt, $adresse,$this->id))
          //if(($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
          //       && $this->getStyleElement("immerbruttorechnungen",$projekt)!="1")
          {
            $this->Cell(30,3,$this->app->erp->Beschriftung('dokument_zzglmwst').' '.$this->app->erp->GetSteuersatzNormal(false,$this->id,$this->table).' %',0,'L','L'); //1
          }
          else {
            $this->Cell(30,3,$this->app->erp->Beschriftung('dokument_inklmwst').' '.$this->app->erp->GetSteuersatzNormal(false,$this->id,$this->table).' %',0,'L','L');
          }
          $this->Cell(40,3,$this->formatMoney((double)$this->totals['totalTaxV'], 2).' '.$this->waehrung,0,'L','R');
        } else {
          //kleinunternehmer
          $this->Cell(30,3,'',0,'L','L');
          $this->Cell(40,3,'',0,'L','R');
        }
        $this->Ln();
      }
      $projekt = $this->projekt;
      $adresse = $this->app->DB->Select("SELECT adresse FROM ".($this->table?$this->table:$this->doctype)." WHERE id = '".$this->id."' LIMIT 1");
      if(!empty($this->totals['totalTaxR']) && $this->totals['totalTaxR']!='0.00'){
        $this->Cell($differenz_wegen_abstand,1,'',0);

        if($this->getStyleElement('kleinunternehmer')!='1'){

          if($this->app->erp->AnzeigeBelegNettoAdresse($this->anrede, $this->doctype, $projekt, $adresse,$this->id))
          //if(($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
          //       && $this->getStyleElement("immerbruttorechnungen",$projekt)!="1")
            {
              $this->Cell(30,3,$this->app->erp->Beschriftung('dokument_zzglmwst').' '.$this->app->erp->GetSteuersatzErmaessigt(false,$this->id,$this->table).' %',0,'L','L'); //1
            }
          else {
            $this->Cell(30,3,$this->app->erp->Beschriftung('dokument_inklmwst').' '.$this->app->erp->GetSteuersatzErmaessigt(false,$this->id,$this->table).' %',0,'L','L');
            }

          $this->Cell(40,3,$this->formatMoney(round((double)$this->totals['totalTaxR'],2), 2).' '.$this->waehrung,0,'L','R');
        } else {
          //kleinunternehmer
          $this->Cell(30,3,'',0,'L','L');
          $this->Cell(40,3,"",0,'L','R');
        }

        $this->Ln();
      }

      if(!empty($this->totals['summen']))
      {
        ksort($this->totals['summen'], SORT_NUMERIC);
        foreach($this->totals['summen'] as $k => $value)
        {
          if($value == 0)
          {
            continue;
          }
          $this->Cell($differenz_wegen_abstand,1,'',0);

          if($this->getStyleElement('kleinunternehmer')!='1'){
            if($this->app->erp->AnzeigeBelegNettoAdresse($this->anrede, $this->doctype, $projekt, $adresse,$this->id))
            //if(($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
            //       && $this->getStyleElement("immerbruttorechnungen",$projekt)!="1")
            {
              $this->Cell(30,3,$this->app->erp->Beschriftung('dokument_zzglmwst').' '.$k.' %',0,'L','L'); //1
            }else {
              //$this->Cell(30,3,$this->app->erp->Beschriftung('dokument_inklmwst').' '.$k.' %',0,'L','L'); 09.12.2018 ab heute auskommentiert wegen 829087
              $this->Cell(30,3,$this->app->erp->Beschriftung('dokument_zzglmwst').' '.$k.' %',0,'L','L');
            }

            $this->Cell(40,3,$this->formatMoney(round($value,2), 2).' '.$this->waehrung,0,'L','R');
          } else {
            //kleinunternehmer
            $this->Cell(30,3,'',0,'L','L');
            $this->Cell(40,3,"",0,'L','R');
          }

          $this->Ln();

        }

      }

      if(!isset($this->totals['totalTaxR']) && !isset($this->totals['totalTaxV']) && !isset($this->totals['summen']) && $this->doctype!="zahlungsavis")
      {
        $this->Cell($differenz_wegen_abstand,3,'',0);

        if($this->getStyleElement('kleinunternehmer')!='1')
        {
          if($this->app->erp->AnzeigeBelegNettoAdresse($this->anrede, $this->doctype, $projekt, $adresse,$this->id))
          //if(($this->anrede=="firma" || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype=="bestellung" || $this->getStyleElement("immernettorechnungen",$projekt)=="1")
          //       && $this->getStyleElement("immerbruttorechnungen",$projekt)!="1")
            {
              if(!($this->ust_befreit==3 && $this->getStyleElement('steuerfrei_inland_ausblenden')=='1')) //steuerfrei inland
              {
                $this->Cell(30, 3, $this->app->erp->Beschriftung('dokument_zzglmwst') . ' 0.00 %', 0, 'L', 'L'); //1
              }
            }
          else {
            if(!($this->ust_befreit==3 && $this->getStyleElement('steuerfrei_inland_ausblenden')=='1')) //steuerfrei inland
            {
              $this->Cell(30, 3, $this->app->erp->Beschriftung('dokument_inklmwst') . ' 0.00 %', 0, 'L', 'L');
            }
          }

          if(!($this->ust_befreit==3 && $this->getStyleElement('steuerfrei_inland_ausblenden')=='1')) //steuerfrei inland
          {
            $this->Cell(40, 3, '0,00 ' . $this->waehrung, 0, 'L', 'R');
          }
        } else {
          //kleinunternehmer
          $this->Cell(30,3,'',0,'L','L');
          $this->Cell(40,3,'',0,'L','R');
        }
        $this->Ln();
      }
      $this->SetY($this->GetY()+2);
      //$this->Line(110, $this->GetY(), 190,$this->GetY());
    }

    $this->SetFont($this->GetFont(),'B',$this->getStyleElement('schriftgroesse_gesamt'));
    $this->Cell($differenz_wegen_abstand,5,'',0);
    if($this->doctype=='offer'){
      $this->Cell(30, 5, $this->app->erp->Beschriftung('dokument_gesamt_total'), 0, 'L', 'L');
    }
    elseif($this->doctype=='creditnote'){
      $this->Cell(30, 5, $this->app->erp->Beschriftung('dokument_gesamt_total'), 0, 'L', 'L');
    }
    else if($this->doctype=='arbeitsnachweis'){
      $this->Cell(30, 5, $this->app->erp->Beschriftung('dokument_gesamt_total'), 0, 'L', 'L');
    }
    else if($this->doctype=='zahlungsavis'){
      $this->Cell(30, 5, $this->app->erp->Beschriftung('dokument_gesamt_total'), 0, 'L', 'L');
    }
    else{
      $this->Cell(30, 5, $this->app->erp->Beschriftung('dokument_gesamt_total'), 0, 'L', 'L');
    }

    if($this->doctype=='arbeitsnachweis'){
      $this->Cell(40, 5, $this->totals['total'] . ' ', 0, 'L', 'R');
    }
    else {
      if($this->getStyleElement('kleinunternehmer')!='1'){
        $this->Cell(40, 5, $this->formatMoney(round((double)$this->totals['total'], 2), 2) . ' ' . $this->waehrung, 0, 'L', 'R');
      }
      else{
        $this->Cell(40, 5, $this->formatMoney(round((double)$this->totals['totalArticles'], 2), 2) . ' ' . $this->waehrung, 0, 'L', 'R');
      }
    }

    $this->Ln();
    if($this->getStyleElement('briefpapier_ohnedoppelstrich')!='1')
    {
      $this->Line($differenz_wegen_abstand+5, $this->GetY(), 210-$this->getStyleElement('abstand_seitenrandrechts'),$this->GetY());
      $this->Line($differenz_wegen_abstand+5, $this->GetY()+1, 210-$this->getStyleElement('abstand_seitenrandrechts'),$this->GetY()+1);
    }

    $this->SetY($this->GetY()+10);
  }

  function displayAnhaenge($output='D')
  {
    SuperFPDF::__construct('P','mm','A4');
    $this->seite_von_sichtbar = 1;



    //		if($this->getStyleElement("schriftart")!="")
    //			$this->SetFont($this->getStyleElement("schriftart"));

    if($this->getStyleElement('schriftart')!='' && $this->getStyleElement('schriftart')!='Arial' &&
        $this->getStyleElement('schriftart')!='Courier' && $this->getStyleElement('schriftart')!='Helvetica'
        && $this->getStyleElement('schriftart')!='Times' && $this->getStyleElement('schriftart')!='Arial'){
      $this->AddFont($this->getStyleElement('schriftart'),'',strtolower($this->getStyleElement('schriftart')).'.php');
      $this->AddFont($this->getStyleElement('schriftart'),'I',strtolower($this->getStyleElement('schriftart')).'.php');
      $this->AddFont($this->getStyleElement('schriftart'),'B',strtolower($this->getStyleElement('schriftart')).'.php');
    }
    if(!empty($this->addpdf) && is_array($this->addpdf))
    {

      foreach($this->addpdf as $addpdf)
      {

        $filename = $addpdf;

        $pn = $this->setSourceFile($filename);
        //$this->AddPage();
        //$this->SetXY(0,0);
        for($i = 1; $i <= $pn ; $i++)
        {
          $fn =& $this->current_filename;
          $mediabox = $this->parsers[$fn]->getPageMediaBox($i);
          //$this->app->erp->LogFile(addslashes(json_encode($mediabox)));
          if($i >= 1)
          {
            if(isset($mediabox['w']) && (float)$mediabox['w'] > 210.1 )
            {
              $this->AddPage('L');
            } else {
              $this->AddPage();
            }
            $this->SetXY(0,0);
          }
          $tplidx = $this->ImportPage($i);
          $this->useTemplate($tplidx);
        }
      }
    }
    $this->filename = $this->app->erp->Dateinamen($this->filename);
    $this->Output($this->filename,$output);
    exit;
  }

  /**
   * @param float    $value
   * @param int|null $decimals
   *
   * @return string
   */
  protected function formatMoney($value, $decimals = null)
  {
    $decimals = $decimals !== null ? (int)$decimals : $this->anzahlkomma;
    $currency = $this->waehrung;
    return $this->app->erp->formatMoney($value, $currency,$decimals);
  }
}
