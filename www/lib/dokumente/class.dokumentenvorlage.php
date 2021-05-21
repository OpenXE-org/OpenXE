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
include_once __DIR__."/class.superfpdf.php";
class Dokumentenvorlage extends SuperFPDF {
  public $doctype;
  public $doctypeOrig;

  public $logofile;
  public $sender;
  public $recipient;
  public $corrDetails;
  public $InfoBox;
  public $endInfoBox;
  public $boldCorrDetails;
  public $textDetails;
  public $items;

  public $ust_befreit;

  public $barcode;
  public $firmendatenid;
  public $absender;


  /***********************************
   *     data aggregation functions
   ***********************************/
  public function __construct($app,$projekt='') {


    //$orientation='P';$unit='mm';$format='A4';
    //parent::PDF_EPS($orientation,$unit,$format);

    $this->app=$app;
    $this->absender = '';
    $this->firmendatenid = $this->app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");

    $hintergrund = $this->app->DB->Select("SELECT hintergrund FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
    if($hintergrund=="logo")
    {
      $logo = $this->app->erp->getSettingsFile('logo');
      $filename = $this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_logo.jpg';
      if ($handle = fopen($filename, 'w')) {
        fwrite($handle, $logo);
        fclose($handle);
      }

      if(is_file($this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_logo.jpg')){
        $this->logofile = $this->app->erp->GetTMP() . $this->app->Conf->WFdbname . '_logo.jpg';
      }
      $this->briefpapier='';
    }
    else if($hintergrund=="briefpapier")
    {
      $briefpapier = $this->app->erp->getSettingsFile('briefpapier');
      $filename = $this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_briefpapier.pdf';
      if ($handle = fopen($filename, 'w')) {
        fwrite($handle, $briefpapier);
        fclose($handle);
      }

      if(is_file($this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_briefpapier.pdf')){
        $this->briefpapier = $this->app->erp->GetTMP() . $this->app->Conf->WFdbname . '_briefpapier.pdf';
      }

      $this->logofile = '';

      $briefpapier2vorhanden = $this->app->erp->Firmendaten('briefpapier2vorhanden');
      $this->briefpapier2vorhanden = $briefpapier2vorhanden;
      if($briefpapier2vorhanden > 0)
      {
        $briefpapier2 = $this->app->erp->getSettingsFile('briefpapier2');
        $filename = $this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_briefpapier2.pdf';
        if ($handle = fopen($filename, 'w')) {
          fwrite($handle, $briefpapier2);
          fclose($handle);
        }
        if(is_file($this->app->erp->GetTMP().$this->app->Conf->WFdbname.'_briefpapier2.pdf')){
          $this->briefpapier2 = $this->app->erp->GetTMP() . $this->app->Conf->WFdbname . '_briefpapier2.pdf';
        }
      }
    }
    else if($hintergrund=="dokumentenvorlage")
    {
      $dokumentenvorlage = $this->app->DB->Select("SELECT dokumentenvorlage FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
      $filename = '/tmp/dokumentenvorlage.pdf';
      if ($handle = fopen($filename, 'w')) {
        fwrite($handle, base64_decode($dokumentenvorlage));
        fclose($handle);
      }

      $this->dokumentenvorlage='/tmp/dokumentenvorlage.pdf';
      $this->logofile = '';
    }
    else {
      $this->logofile = '';
      $this->dokumentenvorlage='';
    }


    $footersichtbar = $this->app->erp->Firmendaten('footersichtbar');
    if($footersichtbar==1) {
      $this->nichtsichtbar_footer = false;
    } else {
      $this->nichtsichtbar_footer = true;
    }

    $seite_von_sichtbar = $this->app->erp->Firmendaten('seite_von_sichtbar');
    if($seite_von_sichtbar==1) {
      $this->seite_von_sichtbar = false;
    } else {
      $this->seite_von_sichtbar = true;
    }

    $this->seite_von_ausrichtung = $this->app->erp->Firmendaten('seite_von_ausrichtung');


    $this->abstand_adresszeileoben = $this->app->erp->Firmendaten('abstand_adresszeileoben');
    $this->abstand_boxrechtsoben = $this->app->erp->Firmendaten('abstand_boxrechtsoben');
    $this->abstand_betreffzeileoben = $this->app->erp->Firmendaten('abstand_betreffzeileoben');
    $this->abstand_artikeltabelleoben = $this->app->erp->Firmendaten('abstand_artikeltabelleoben');


    $sichtbar = $this->app->erp->Firmendaten('sichtbar');
    if($sichtbar==1) $this->nichtsichtbar_zeileabsender = false; else $this->nichtsichtbar_zeileabsender =true;


    $this->barcode_sichtbar = $this->app->erp->Firmendaten('barcode');

    // kann man herausfinden was fuer ein projekt angegeben ist???
    $speziallieferschein = $this->app->DB->Select("SELECT speziallieferschein FROM projekt WHERE id='$projekt' LIMIT 1");

    if($speziallieferschein>0)
    {
      $this->dokumentenvorlage="./lib/dokumente/demo.pdf"; //TODO
      $this->logofile = "";

      $this->nichtsichtbar_zeileabsender = true;
      $this->nichtsichtbar_footer = true;
      $this->nichtsichtbar_rechtsoben = true;
    }

    $this->nichtsichtbar_rechtsoben = true;
    $this->nichtsichtbar_summe = false;
    $this->nichtsichtbar_box=false;
    $this->nichtsichtbar_empfaenger=false;

  }





  public function addItem($rdata){
    // add rabatt
    $rdata['tprice'] = $rdata['amount']*($rdata['price']-($rdata['price']/100*$rdata['rabatt'])    );
    $this->items[]=$rdata;
  }

  public function setSender($rdata){
    $this->sender['enterprise']   = $this->app->erp->ReadyForPDF($rdata[0]);
    $this->sender['firstname']     = $this->app->erp->ReadyForPDF($rdata[1]);
    $this->sender['familyname']   = $this->app->erp->ReadyForPDF($rdata[2]);
    $this->sender['address1']     = $this->app->erp->ReadyForPDF($rdata[3]);
    $this->sender['areacode']     = $this->app->erp->ReadyForPDF($rdata[4]);
    $this->sender['city']         = $this->app->erp->ReadyForPDF($rdata[5]);
    if(isset($rdata[6]))$this->sender['country'] = $this->app->erp->ReadyForPDF($rdata[6]);
  }


  function setInfoBox($infobox)
  {
    if(is_array($infobox))
      $this->InfoBox=$infobox;
    else
      $this->InfoBox=$this->app->erp->ReadyForPDF($infobox);

  }

  function endInfoBox($infobox)
  {
    if(is_array($infobox))
      $this->endInfoBox=$infobox;
    else
      $this->endInfoBox=$this->app->erp->ReadyForPDF($infobox);
  }



  function setRecipientRechnung($id)
  {


  }


  function setRecipientLieferadresse($id,$table)
  {
    $this->id = $id;
    $this->table = $table;

    $tmp = $this->app->DB->SelectArr("SELECT * FROM $table WHERE id='$id' LIMIT 1");
    if($tmp[0]['typ']!="person")
    {
      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($tmp[0][name]);


      if($tmp[0][ansprechpartner]!="" && strlen($tmp[0][ansprechpartner])>1)
        $this->recipient['firstname']   = "z.H. ".$this->app->erp->ReadyForPDF($tmp[0][ansprechpartner]);

      if($tmp[0][abteilung]!="" && strlen($tmp[0][abteilung]) >1)
        $this->recipient['address2']   =  $this->app->erp->ReadyForPDF($tmp[0][abteilung]);

      if($tmp[0][unterabteilung]!="")
        $this->recipient['address3'] = $this->app->erp->ReadyForPDF($tmp[0][unterabteilung]);

      if($tmp[0][adresszusatz]!="")
        $this->recipient['address4'] = $this->app->erp->ReadyForPDF($tmp[0][adresszusatz]);

    }

    else {
      $vorname = '';
      if(isset($tmp[0][vorname]) && $tmp[0][vorname]!='' && strlen(trim($tmp[0][vorname]))>0)
        $vorname = "{$tmp[0][vorname]} ";

      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($vorname.$tmp[0][name]);
      $this->recipient['address2']   = $this->app->erp->ReadyForPDF($tmp[0][adresszusatz]);
    }


    $this->recipient['address1']     = $this->app->erp->ReadyForPDF($tmp[0][strasse]);
    $this->recipient['areacode']     = $this->app->erp->ReadyForPDF($tmp[0][plz]);
    $this->recipient['city']         = $this->app->erp->ReadyForPDF($tmp[0][ort]);
    if($this->recipient['city']!="")
      $this->recipient['country']      = $this->app->erp->ReadyForPDF($tmp[0][land]);
  }


  function setRecipientDB($adresse)
  {
    $tmp = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");
    if($tmp[0]['typ']!="person")
    {
      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($tmp[0][name]);
      if($tmp[0][ansprechpartner]!="")
        $this->recipient['firstname']   = $this->app->erp->ReadyForPDF("z.H. ".$tmp[0][ansprechpartner]);

      if($tmp[0][abteilung]!="" && strlen($tmp[0][abteilung])>1)
        $this->recipient['address2']   = $this->app->erp->ReadyForPDF($tmp[0][abteilung]);

      if($tmp[0][unterabteilung]!="")
        $this->recipient['address3'] = $this->app->erp->ReadyForPDF($tmp[0][unterabteilung]);

      if($tmp[0][adresszusatz]!="")
        $this->recipient['address4'] = $this->app->erp->ReadyForPDF($tmp[0][adresszusatz]);

    }

    else {
      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($tmp[0][name]);
      $this->recipient['address2']   = $this->app->erp->ReadyForPDF($tmp[0][adresszusatz]);
    }


    $this->recipient['address1']     = $this->app->erp->ReadyForPDF($tmp[0][strasse]);
    $this->recipient['areacode']     = $this->app->erp->ReadyForPDF($tmp[0][plz]);
    $this->recipient['city']         = $this->app->erp->ReadyForPDF($tmp[0][ort]);
    if($this->recipient['city']!="")
      $this->recipient['country']      = $this->app->erp->ReadyForPDF($tmp[0][land]);
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

  public function setCorrDetails($rdata){
    $this->corrDetails = $rdata;
  }
  public function setBoldCorrDetails($rdata){
    $this->boldCorrDetails = $rdata;
  }
  public function setTextDetails($rdata){
    $this->textDetails = $rdata;
  }


  public function setTotals($rdata){
    $this->totals   = $rdata;
  }

  // Dokumentenvorlage festlegen
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
    if($this->barcode_sichtbar)
      $this->barcode = $barcode;
    else $this->barcode="";

  }


  public function Header() {
    $this->Line(0,87,5,87);
    $this->Line(0,148,7,148);
    $this->Line(0,192,5,192);

    if($this->logofile!="")
      $this->Image($this->logofile,15,10,110);

    if($this->barcode!=""){
      //$this->Rotate(90);
      $this->Code39(12, 260, $this->barcode, 1, 3);
      //$this->Rotate(0);
    }

    if($this->briefpapier!="")
      $this->setStationery($this->briefpapier);
  }

  public function Footer() {

    $this->SetXY(12,-34);
    $this->SetFont($this->GetFont(),'',8);


    if($this->seite_von_sichtbar!="1")
      $this->Cell(0,8,'Seite '.$this->PageNo().' von {nb}',0,0,$this->seite_von_ausrichtung);

    if($this->nichtsichtbar_footer!=true)
    {

      $footerarr = $this->app->DB->SelectArr("SELECT * FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
      $footerarr = $footerarr[0];

      foreach($footerarr as $key=>$value)
        $footerarr[$key] = $this->app->erp->ReadyForPDF($value);

      $this->SetXY(10,-26);
      $this->SetDrawColor(30);
      $this->SetTextColor(30);
      $this->SetFont($this->GetFont(),'',7);


      $this->MultiCell(190,3,utf8_encode($footerarr['name'])." | ".'Seite '.$this->PageNo().' von {nb} | Dokument: '.$this->filename,0,0,$this->seite_von_ausrichtung,'','L');
      $this->SetXY(60,-26);
      /*
         $this->MultiCell(35,3,utf8_encode("  ".$footerarr['footer_1_0']."\n  ".$footerarr['footer_1_1']."\n  ".$footerarr['footer_1_2']."\n  ".$footerarr['footer_1_3']."\n  ".$footerarr['footer_1_4']."\n  ".$footerarr['footer_1_5'].""),'L','L');
         $this->SetXY(95,-26);
         $this->MultiCell(60,3,utf8_encode("  ".$footerarr['footer_2_0']."\n  ".$footerarr['footer_2_1']."\n  ".$footerarr['footer_2_2']."\n  ".$footerarr['footer_2_3']."\n  ".$footerarr['footer_2_4']."\n  ".$footerarr['footer_2_5'].""),'L','L');
         $this->SetXY(155,-26);
         $this->MultiCell(40,3,utf8_encode("  ".$footerarr['footer_3_0']."\n  ".$footerarr['footer_3_1']."\n  ".$footerarr['footer_3_2']."\n  ".$footerarr['footer_3_3']."\n  ".$footerarr['footer_3_4']."\n  ".$footerarr['footer_3_5'].""),'L','L');
       */
    }
  }

  public function inlineDocument() {
    $this->renderDocument();
    //    $this->archiveDocument();
    header('Content-type: application/pdf');
    $this->Output();
    exit;
  }

  public function ArchiviereDocument($schreibschutz = false)
  {
    $this->filename = $this->app->erp->Dateinamen($this->filename);
    $dir = $this->app->Conf->WFuserdata."/pdfarchiv/".$this->app->Conf->WFdbname;
    if(!is_dir($dir)){
      if(!mkdir($dir, 0700,true))$this->app->erp->LogFile('Fehler beim erstellen von '.$dir);
      //				echo "fehlt";
    }
    if(!is_dir($dir))return false;
    if(!is_dir($dir."/".$this->table))
      if(!mkdir ($dir."/".$this->table,0700,true))$this->app->erp->LogFile('Fehler beim erstellen von '.$dir."/".$this->table);
    if(!is_dir($dir."/".$this->table))return;
    $md5alt = false;
    $altesdokument = $this->app->DB->SelectArr("SELECT * from pdfarchiv where table_id = '".$this->id."'  and table_name = '".$this->table."' AND doctype = '".$this->app->DB->real_escape_string($this->doctype)."' AND doctypeorig = '".$this->app->DB->real_escape_string($this->doctypeOrig)."' ORDER BY zeitstempel DESC LIMIT 1");
    if($altesdokument)
    {
      $altesdokument = reset($altesdokument);
      $md5alt = $altesdokument['checksum'];
      if(!is_file($dir."/".$this->table."/".$md5alt."_".$this->id."_".$this->filename))
      {
        if(file_exists($dir."/".$this->table."/".$this->id."_".$this->filename))
        {
          rename($dir."/".$this->table."/".$this->id."_".$this->filename,$dir."/".$this->table."/".$md5alt."_".$this->id."_".$this->filename);
          if(file_exists($dir."/".$this->table."/".$md5alt."_".$this->id."_".$this->filename))
          {
            $md5sum_tmp = md5_file($dir."/".$this->table."/".$md5alt."_".$this->id."_".$this->filename);
            if($md5sum_tmp !== $md5alt)
            {
              $this->app->DB->Update("UPDATE pdfarchiv set checksum = '$md5sum_tmp' WHERE id = '".$altesdokument['id']."'");
              $md5alt = $md5sum_tmp;
            }
          }
        }
      }
    }


    $this->Output($dir."/".$this->table."/".$this->id."_".$this->filename,'F');
    
    if($this->table)$this->app->DB->Update("UPDATE ".$this->table." SET zuarchivieren = 0 WHERE id = '".$this->id."'");
		// neue sicherung
		$md5sum_tmp = md5_file($dir."/".$this->table."/".$this->id."_".$this->filename);
    
    if($md5sum_tmp !== $md5alt || empty($altesdokument) || empty($altesdokument['dateiname']))
    {
      
      $belegnummer = str_ireplace('.pdf','', substr($this->filename,strrpos($this->filename,'_')+1));
      
      $this->app->DB->Insert("INSERT INTO pdfarchiv (zeitstempel, checksum, table_id, table_name, bearbeiter,erstesoriginal,doctype,doctypeorig,dateiname,belegnummer ) values (NOW(),'$md5sum_tmp','".$this->id."','".$this->table."','".$this->app->User->GetName()."',".($md5alt?0:0).",'".$this->app->DB->real_escape_string($this->doctype)."','".$this->app->DB->real_escape_string($this->doctypeOrig)."','".$this->app->DB->real_escape_string($md5sum_tmp."_".$this->id."_".$this->filename)."','".$this->app->DB->real_escape_string($belegnummer)."')");
      $newid = $this->app->DB->GetInsertID();
      if($schreibschutz || $this->app->DB->Select("SELECT schreibschutz FROM ".$this->table." WHERE id = '".$this->id."' LIMIT 1"))$this->app->DB->Update("UPDATE pdfarchiv SET schreibschutz = 1 WHERE id = '$newid' LIMIT 1");
      if(!file_exists($dir."/".$this->table."/".$md5sum_tmp."_".$this->id."_".$this->filename))
      {
        rename($dir."/".$this->table."/".$this->id."_".$this->filename, $dir."/".$this->table."/".$md5sum_tmp."_".$this->id."_".$this->filename);
      }
    }elseif($md5alt && isset($altesdokument['id']) && $altesdokument['id'])
    {
      if($schreibschutz || $this->app->DB->Select("SELECT schreibschutz FROM ".$this->table." WHERE id = '".$this->id."' LIMIT 1"))$this->app->DB->Update("UPDATE pdfarchiv SET schreibschutz = 1 WHERE id = '".$altesdokument['id']."' LIMIT 1");
    }
  }

  public function archiveDocument()
  {
    if($this->table && $this->id)
    {
      $this->ArchiviereDocument($this->app->DB->Select("SELECT schreibschutz FROM `".trim($this->app->DB->real_escape_string($this->table))."` WHERE id = '".(int)$this->id."' LIMIT 1")?true:false);
    }else{
      $this->ArchiviereDocument();
    }
    return;
    $dir = $this->app->Conf->WFuserdata."/pdfmirror/".$this->app->Conf->WFdbname;
    if(!is_dir($dir)){
      mkdir($dir, 0700,true);
      //				echo "fehlt";
    }
    if(!is_dir($dir."/".$this->table))
      mkdir ($dir."/".$this->table,0700,true);

    $this->Output($dir."/".$this->table."/".$this->id."_".$this->filename,'F');
  }

  public function displayDocument() {
    $this->renderDocument();
    $this->archiveDocument();
    $this->Output($this->filename,'D');
    exit;
  }

  public function displayTMP() {
    $this->renderDocument();
    $this->archiveDocument();
    $this->Output("/tmp/".$this->filename,'F');
    return "/tmp/".$this->filename;
  }


  public function sendDocument() {
    $this->renderDocument();
    $this->archiveDocument();
    $this->Output($this->filename,'S');
    exit;
  }


  /***********************************
   *       public functions
   ***********************************/
  // setup relevant data for a invoice
  public function setupTax() {
    // full taxrate
    define("USTV",0.19);
    // reduced taxrate
    define("USTR",0.07);
  }


  public function calcTotals() {
    $total=$totalFullTax=$totalReducedTax=0;
    for($i=0;$i<count($this->items);$i++) {
      $total += $this->items[$i]['tprice'];
      if($this->items[$i]['tax']=="USTV") $totalFullTax+= $this->items[$i]['tprice']*USTV;
      else                                $totalReducedTax+= $this->items[$i]['tprice']*USTR;
    }
    return array($total,$totalFullTax,$totalReducedTax);
  }

  function GetFont()
  {
    //if($this->app->erp->Firmendaten("schriftart")!="")
    //  return $this->app->erp->Firmendaten("schriftart");
    //else
    return 'Arial';
  }

  public function renderRecipient(){
    /*
       $this->SetY(50+$this->abstand_adresszeileoben);
       $this->SetFont($this->GetFont(),'',10);
       if($this->recipient['enterprise']) {
       $this->Cell(80,5,$this->recipient['enterprise'],0,1);
       }

       if($this->recipient['firstname']!="")
       $this->Cell(80,5,$this->recipient['firstname'],0,1);

       if($this->recipient['address2']!="")
       $this->Cell(80,5,$this->recipient['address2'],0,1);

       if($this->recipient['address3']!="")
       $this->Cell(80,5,$this->recipient['address3'],0,1);

       if($this->recipient['address4']!="")
       $this->Cell(80,5,$this->recipient['address4'],0,1);


    //$this->Cell(80,5,$this->recipient['firstname']." ".$this->recipient['familyname'],0,1);
    $this->Cell(80,5,$this->recipient['address1'],0,1);


    $this->SetFont($this->GetFont(),'B',11);
    if($this->recipient['country']!="")
    $this->Cell(80,5,$this->recipient['country']."-".$this->recipient['areacode']." ".$this->recipient['city'],0,1);
    else
    $this->Cell(80,5,$this->recipient['areacode']." ".$this->recipient['city'],0,1);
     */
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
      /*
      //abstand_adresszeileoben
      // line above address field
      $absender = (($this->absender!='') ? $this->absender : $this->app->DB->Select("SELECT absender FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1"));
      $schriftgroesse  = $this->app->DB->Select("SELECT schriftgroesse FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");

      $this->SetY(43+$this->abstand_adresszeileoben);
      $this->SetFont($this->GetFont(),'',$schriftgroesse);
      //$cellStr = $this->sender['enterprise']." � ".$this->sender['address1']." � ".$this->sender['areacode']." ".$this->sender['city'];
      $cellStr = $absender;
      $this->Cell($this->GetStringWidth($cellStr)+2,5,$cellStr,'B');
       */
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

    return;

    $boldTitleStr = $boldValueStr = $titleStr = $valueStr = "";
    if(isset($this->boldCorrDetails)){
      foreach($this->boldCorrDetails as $title => $value) {
        $boldTitleStr .= $title.": \n";
        $boldValueStr .= $value."\n";
      }
    }
    if(isset($this->corrDetails)){
      foreach($this->corrDetails as $title => $value) {
        $titleStr .= $title.": \n";
        $valueStr .= $value."\n";
      }
    }

    if($boldTitleStr!="")
    {
      $this->SetFont($this->GetFont(),'B',9);
      $this->SetXY(-85,20);
      $this->MultiCell(37,4,$this->app->erp->ReadyForPDF($boldTitleStr),"TL",'R');
      $this->SetXY(-48,20);
      $this->MultiCell(33,4,$this->app->erp->ReadyForPDF($boldValueStr),"TR",'R');
    } else {
      $this->SetXY(-85,20+$this->abstand_boxrechtsoben);
      $this->MultiCell(37,0,"","TL",'R');
      $this->SetXY(-48,20+$this->abstand_boxrechtsoben);
      $this->MultiCell(33,0,"","TR",'R');
    }

    $this->SetY(20);

    $this->SetY($this->GetY()+$this->abstand_boxrechtsoben);

    $this->SetFont($this->GetFont(),'',9);
    $tempY = $this->GetY();
    $this->SetX(-85);
    $this->MultiCell(37,4,$this->app->erp->ReadyForPDF($titleStr),"BL",'R');
    $this->SetXY(-48,$tempY);
    $this->MultiCell(33,4,$this->app->erp->ReadyForPDF($valueStr),"RB",'R');

    $this->SetY(20+$this->abstand_artikeltabelleoben); //Hoehe Box
    //$this->SetY(60);//+$this->abstand_artikeltabelleoben); //Hoehe Box

  }


  public function renderDoctype() {

    //$this->Ln(1);

    if($this->doctype=="brief")
      $betreffszeile  = $this->app->erp->Firmendaten('brieftext');
    else
      $betreffszeile  = $this->app->erp->Firmendaten('betreffszeile');


    $this->SetY(20);//+$this->abstand_artikeltabelleoben); //Hoehe Box
    //$this->SetY(80+$this->abstand_artikeltabelleoben); //Hoehe Box
    $this->SetFont($this->GetFont(),'B',$betreffszeile);
    $this->SetY(40);//+$this->abstand_artikeltabelleoben); //Hoehe Box
    $this->SetY($this->GetY()+$this->abstand_betreffzeileoben);
    $this->Cell(85,6,$this->app->erp->ReadyForPDF($this->doctypeOrig));
    $this->SetY($this->GetY()-$this->abstand_betreffzeileoben);

    //$this->SetY($this->GetY()+$this->abstand_betreffzeileoben);
    $this->SetY($this->GetY()+$this->abstand_artikeltabelleoben); //Hoehe Box

    $this->SetY(30);//+$this->abstand_artikeltabelleoben); //Hoehe Box

    $dokumententext  = $this->app->erp->Firmendaten('dokumententext');
    $this->SetFont($this->GetFont(),'',$dokumententext);
    $this->SetY(50);//+$this->abstand_artikeltabelleoben); //Hoehe Box

    /*
       $this->MultiCell(90,5,"Mitarbeiter:              Benedikt Sauter","LR",'L');
       $this->MultiCell(90,5,"Termin:                    11.11.2013 - 17.12.2013","LR",'L');
       $this->MultiCell(95,5,"Von:                         08:00","L",'L');
       $this->SetY($this->GetY()-5); $this->SetX(95);
       $this->MultiCell(10,5,"18:00","R",'L');
       $this->MultiCell(90,5,"Reiseanlass:            Musik machen mit lustigen Leuten","LR",'L');
       $this->MultiCell(90,5,"Kunde (optional):     Mueller AG","BLR",'L');
     */
  }


  public function renderText() {
    if(isset($this->textDetails['body'])) {
      if($this->doctype=="brief")
        $dokumententext  = $this->app->erp->Firmendaten('brieftext');
      else
        $dokumententext  = $this->app->erp->Firmendaten('dokumententext');

      $this->SetFont($this->GetFont(),'',$dokumententext);
      $this->Ln(11);
      $this->MultiCell(180,4,$this->app->erp->ReadyForPDF($this->textDetails['body']));
    }
  }

  public function renderFooter() {
    if(isset($this->textDetails['footer'])) {
      $freitext  = $this->app->erp->Firmendaten('freitext');
      $this->SetFont($this->GetFont(),'',$freitext);
      $this->Ln(5);
      $this->MultiCell(180,4,$this->app->erp->ReadyForPDF($this->textDetails['footer']));
    }
  }

  public function renderReisekostenItems() {


    $posWidth     = 10;
    $amWidth     = 20;
    $itemNoWidth = 20;
    $descWidth   = 80;
    $taxWidth   = 15;

    $priceWidth = 15;
    $sumWidth   = 15;
    $rabattWidth   = 15;
    // $lineLength = $amWidth + $itemNoWidth + $descWidth + $taxWidth + $priceWidth + $sumWidth;


    $cellhoehe   = 5;

    // render table header
    if(isset($this->textDetails['body'])) $this->Ln();
    else $this->Ln(8);
    $tabellenbeschriftung  = $this->app->erp->Firmendaten('tabellenbeschriftung');

    $this->SetFont($this->GetFont(),'B',$tabellenbeschriftung);

    if($this->doctype=="reisekosten")
    {
      $this->Cell($posWidth,6,'Pos');
      $this->Cell($descWidth,6,'Beschreibung');
      $this->Cell($amWidth,6,'Brutto (EUR)',0,0,'R');
      $this->Cell($amWidth,6,'MwSt',0,0,'R');
      $this->Cell($amWidth,6,'Netto',0,0,'R');
      $this->Cell($amWidth,6,'Bezahlt',0,0,'R');
    }

    $this->Ln();
    $this->Line($this->GetX(), $this->GetY(), 190, $this->GetY());
    $this->Ln(2);

    // render table body
    $tabelleninhalt  = $this->app->erp->Firmendaten('tabelleninhalt');

    $this->SetFont($this->GetFont(),'',$tabelleninhalt);

    $pos=0;
    foreach($this->items as $item){
      $cellhoehe   = 3;
      //position
      $this->Cell($posWidth,$cellhoehe,++$pos,0,0,'C');
      //artikelnummer
      $position_x   = $this->GetX();
      $position_y   = $this->GetY();
      $this->MultiCell($descWidth,$cellhoehe,trim($this->app->erp->ReadyForPDF($item['name'])),0,Alignment.LEFT, false);
      $position_y_end_name   = $this->GetY();

      $this->SetXY(($position_x + $descWidth), $position_y);

      $this->Cell($amWidth,$cellhoehe,$item['brutto'],0,0,'R');
      $this->Cell($amWidth,$cellhoehe,$item['tax']." %",0,0,'R');
      $this->Cell($amWidth,$cellhoehe,$item['netto'],0,0,'R');
      $this->Cell($amWidth,$cellhoehe,$item['bezahlt_wie'],0,0,'R');

      $this->Ln();
      $zeilenuntertext  = $this->app->erp->Firmendaten('zeilenuntertext');
      //$this->SetY($position_y_end_name+$zeilenuntertext/2);
      $this->SetFont($this->GetFont(),'',$zeilenuntertext);
      $this->Cell($posWidth);
      //$this->Cell($descWidth);
      //if($this->doctype=="arbeitsnachweis") $this->Cell($taxWidth);
      $this->MultiCell($descWidth,($zeilenuntertext/2),trim($this->app->erp->ReadyForPDF($item['desc'])),0); // 4 = abstand
      $this->Ln();
      $this->SetFont($this->GetFont(),'',$tabelleninhalt);

    }
    $this->Line($this->GetX(), $this->GetY(), 190, $this->GetY());
  }


  public function renderHeading($heading,$height=5)
  {

    $betreffszeile  = $this->app->erp->Firmendaten('betreffszeile');

    $this->SetFont($this->GetFont(),'B',$betreffszeile);
    $this->Cell(85,6,$this->app->erp->ReadyForPDF($heading));
    $this->SetFont($this->GetFont(),'',$betreffszeile);
    $this->Ln($height);
  }

  public function renderTextBox($infoBox,$height=5)
  {
    if(is_array($infoBox))
    {
      for($i=0;$i<count($infoBox);$i++)
      {
        $this->MultiCell(190,$height,$this->WriteHTML($this->app->erp->ReadyForPDF($infoBox[$i])),"",'L');
      }
    } else {
      $this->MultiCell(190,$height,$this->WriteHTML($this->app->erp->ReadyForPDF($infoBox)),"",'L');
    }
  }

  public function renderInfoBoxSingle($infoBox,$height=5)
  {
    if(is_array($infoBox))
    {
      for($i=0;$i<count($infoBox);$i++)
      {
        $this->MultiCell(175,$height,$this->WriteHTML($this->app->erp->ReadyForPDF($infoBox[$i])),"",'L');
      }
    } else {
      $this->MultiCell(175,$height,$this->WriteHTML($this->app->erp->ReadyForPDF($infoBox)),"BTLR",'L');
    }
  }


  public function renderInfoBox($infoBox,$height=5) {

    for($i=0;$i<count($infoBox);$i++)
    {
      $this->MultiCell(50,$height,$this->app->erp->ReadyForPDF($infoBox[$i][0]).":","BTL",'L');
      $this->SetY($this->GetY()-$height); $this->SetX(50); $this->MultiCell(140,$height,$this->app->erp->ReadyForPDF($infoBox[$i][1]),"BTR",'L');
    }
  }



  public function renderTotals() {

    for($i=0;$i<count($this->endInfoBox);$i++)
    {
      $this->MultiCell(50,5,$this->app->erp->ReadyForPDF($this->endInfoBox[$i][0]).":","BTL",'L');$this->SetY($this->GetY()-5); $this->SetX(50);
      $this->MultiCell(140,5,$this->app->erp->ReadyForPDF($this->endInfoBox[$i][1]),"BTR",'L');
    }

    /*
       $this->SetY($this->GetY()+10);
       if($this->doctype!="deliveryreceipt" && $this->doctype!="arbeitsnachweis") {
       $this->Line(110, $this->GetY(), 190, $this->GetY());
       $this->Ln(1);
       $this->SetFont($this->GetFont(),'',9);
       $this->Cell(100,5,'',0);
       $this->Cell(30,5,'Gesamt netto',0,0,'L');
       $this->Cell(40,5,number_format($this->totals['totalArticles'], 2, ',', '').' EUR',0,'L','R');
       $this->Ln();

       if(isset($this->totals['modeOfDispatch'])) $versand = 'Versand: '.$this->totals['modeOfDispatch'];
       else $versand = 'Versandkosten: ';
       if(isset($this->totals['priceOfDispatch'])) {
       $this->Cell(100,5,'',0);
       $this->Cell(30,5,$versand,0,'L','L');
       $this->Cell(40,5,number_format($this->totals['priceOfDispatch'], 2, ',', '').' EUR',0,'L','R');
       }
       $this->Ln();

       if(isset($this->totals['priceOfPayment']) && $this->totals['priceOfPayment']!='0.00'){
       $this->Cell(100,5,'',0);
       $this->Cell(30,5,$this->totals['modeOfPayment'],0,'L','L');
       $this->Cell(40,5,number_format($this->totals['priceOfPayment'], 2, ',', '').' EUR',0,'L','R');
       $this->Ln();
       }

       $this->SetY($this->GetY());
       $this->SetFont($this->GetFont(),'',8);


       if(isset($this->totals['totalTaxV']) && $this->totals['totalTaxV']!="0.00"){
       $this->Cell(100,3,'',0);
       $this->Cell(30,3,'zzgl. MWSt. 19 %',0,'L','L');
       $this->Cell(40,3,number_format($this->totals['totalTaxV'], 2, ',', '').' EUR',0,'L','R');
       $this->Ln();
       }

       if(isset($this->totals['totalTaxR']) && $this->totals['totalTaxR']!="0.00"){
       $this->Cell(100,3,'',0);
       $this->Cell(30,3,'zzgl. MWSt. 7 %',0,'L','L');
       $this->Cell(40,3,number_format($this->totals['totalTaxR'], 2, ',', '').' EUR',0,'L','R');
       $this->Ln();
       }

       if(!isset($this->totals['totalTaxR']) && !isset($this->totals['totalTaxV']))
       {
       $this->Cell(100,3,'',0);
       $this->Cell(30,3,'zzgl. MWSt. 0 %',0,'L','L');
       $this->Cell(40,3,'0,00 EUR',0,'L','R');
       $this->Ln();
       }
       $this->SetY($this->GetY()+2);
       $this->Line(110, $this->GetY(), 190,$this->GetY());
       }

       $this->SetFont($this->GetFont(),'B',9);
       $this->Cell(100,5,'',0);
       if($this->doctype=="offer")
       $this->Cell(30,5,'Angebotssumme',0,'L','L');
       elseif($this->doctype=="creditnote")
       $this->Cell(30,5,'Gutschriftbetrag',0,'L','L');
       else if($this->doctype=="arbeitsnachweis")
       $this->Cell(30,5,'Stunden',0,'L','L');
       else
       $this->Cell(30,5,'Rechnungsbetrag',0,'L','L');

       if($this->doctype=="arbeitsnachweis")
       $this->Cell(40,5,$this->totals['total'].' ',0,'L','R');
       else
       $this->Cell(40,5,number_format($this->totals['total'], 2, ',', '').' EUR',0,'L','R');

    $this->Ln();
    $this->Line(110, $this->GetY(), 190,$this->GetY());
    $this->Line(110, $this->GetY()+1, 190,$this->GetY()+1);

    $this->SetY($this->GetY()+10);
    */
  }

}
