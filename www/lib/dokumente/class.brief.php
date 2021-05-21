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
class BriefPDF extends Briefpapier {
  public $logofile;
  public $sender;
  public $recipient;
  public $letterDetails;
  public $app;



  function __construct($app,$projekt="")
  {
    $this->app=$app;
    $this->doctype="brief";
    $this->projekt = $projekt;
            
    parent::__construct($this->app,$projekt);
  }


  function GetBriefTMP($adresse,$betreff,$text,$fax="")
  {
    $this->doctypeOrig=$betreff;

    $this->setRecipientDB($adresse);

    $details['Bearbeiter'] = $this->app->erp->ReadyForPDF($this->app->User->GetName());
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    if($kundennummer!="" && $kundennummer!=0)
      $details['Ihre Kundennummer']=$this->app->erp->ReadyForPDF($kundennummer);

    $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    $unserekundennummer = $this->app->DB->Select("SELECT kundennummer FROM lieferantvorlage WHERE adresse='$adresse' LIMIT 1"); 

    if($lieferantennummer!="" && $lieferantennummer!=0)
      $details['Lieferantennummer']=$this->app->erp->ReadyForPDF($lieferantennummer);

    if($lieferantennummer!="" && $lieferantennummer!=0 && $unserekundennummer!="" && $unserekundennummer!=0)
      $details['Unsere Kundennummer']=$this->app->erp->ReadyForPDF($unserekundennummer);


    $telefax= $this->app->DB->Select("SELECT telefax FROM adresse WHERE id='$adresse' LIMIT 1");
    if($telefax!="" && $telefax!=0)
      $details['Ihre Faxnummer']=$this->app->erp->ReadyForPDF($telefax);
 
 
    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM dokumente_send WHERE id='$id' LIMIT 1");
      $details['Datum']=$this->app->erp->ReadyForPDF($datum);

    
    $this->setCorrDetails($details);

    $this->setBarcode($adresse);

    $this->setLetterDetails(array($betreff,str_replace('\r\n',"\n\n",$this->app->erp->ReadyForPDF($text))));

    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(NOW(),'%Y%m%d')");
    //$this->filename = $datum."_".str_replace(' ','',trim($this->recipient['enterprise'])).".pdf";
    $this->filename = $datum.".pdf";

    //$this->setBarcode($id);
  }

  function GetBrief($id)
  {
    $type = $this->app->Secure->GetGET("type");
    $typeid = $this->app->Secure->GetGET("typeid");

    $tmp = $this->app->DB->SelectArr("SELECT * FROM dokumente_send WHERE id='$id' LIMIT 1");
    $adresse = $tmp[0]['adresse'];

    if($type=="auftrag")
      $this->setRecipientLieferadresse($typeid,$type);
    else
      $this->setRecipientDB($adresse);

    $details['Bearbeiter'] = $this->app->erp->ReadyForPDF($this->app->User->GetName());
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    if($kundennummer!="" && $kundennummer!=0)
      $details['Ihre Kundennummer']=$this->app->erp->ReadyForPDF($kundennummer);

    $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    if($lieferantennummer!="" && $lieferantennummer!=0)
      $details['Lieferantennummer']=$this->app->erp->ReadyForPDF($lieferantennummer);

    $telefax= $this->app->DB->Select("SELECT telefax FROM adresse WHERE id='$adresse' LIMIT 1");
    if($telefax!="" && $telefax!=0)
      $details['Ihre Faxnummer']=$this->app->erp->ReadyForPDF($telefax);
 
    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM dokumente_send WHERE id='$id' LIMIT 1");
      $details['Datum']=$datum;

    $this->setCorrDetails($details);


    $this->setLetterDetails(array($this->app->erp->ReadyForPDF($tmp[0]['betreff']),$this->app->erp->ReadyForPDF($tmp[0]['text'])));
    $this->doctypeOrig=$this->app->erp->ReadyForPDF($tmp[0]['betreff']);
    $this->docBody=$this->app->erp->ReadyForPDF($tmp[0]['text']);

    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(NOW(),'%Y%m%d') LIMIT 1");
    $this->filename = $datum."_".str_replace(' ','',trim($this->app->erp->ReadyForPDF($this->recipient['enterprise']))).".pdf";

    $this->setBarcode($id);
  }

  
  /***********************************
   *     user space functions
   ***********************************/
  public function setLetterDetails($rdata){
    $this->letterDetails['subject']     = $this->app->erp->ReadyForPDF($rdata[0]);
    $this->letterDetails['body']         = $this->app->erp->ReadyForPDF($rdata[1]);
  }

  /***********************************
   *       public functions
   ***********************************/  
/* 
  public function renderDocument() {
    // prepare page details
    parent::PDF('P','mm','A4');

    $this->AddFont('HelveticaBoldCond','','HLBC____.php');
    $this->AddFont('HelveticaBoldCondItalic','','HLBCO___.php');
    $this->AddFont('HelveticaCond','','HLC_____.php');
    $this->AddFont('HelveticaCondItalic','','HLCO____.php');
    // invoke Header() and Footer() by adding a new page
    $this->AddPage();
    $this->SetDisplayMode("real","single");
      
    $this->SetMargins(15,50);
    $this->SetAutoPageBreak(true,50); 
    $this->AliasNbPages('{nb}');
    
    // render document top to bottom
    if(!empty($this->recipient)) 
      $this->renderRecipient();
      
//    if(!empty($this->sender)) 
//      $this->renderSender();
    
    $this->renderCorrDetails();
  
    $this->renderSubject();
    $this->renderBody();
  }
*/

  public function renderSubject() {
    $this->SetFont($this->app->erp->Firmendaten("schriftart"),'',10);
    $this->SetY(100);
    $this->Cell(80,5,$this->app->erp->ReadyForPDF($this->letterDetails['subject']));
  }
  
  public function renderBody() {
    //$this->SetFont($this->app->erp->Firmendaten("schriftart"),'',10);
    //$this->SetY(90);
    //$this->MultiCell(180,5,$this->app->erp->ReadyForPDF($this->letterDetails['body']));
    //$this->Ln(10);
    //$this->Cell(80,5,$this->letterDetails['valediction']);
    //$this->Ln(20);
    //$this->Cell(80,5,$this->sender['firstname']." ".$this->sender['familyname']);
  }
  
  
  
}
