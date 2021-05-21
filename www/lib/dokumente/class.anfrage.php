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
if(!class_exists('BriefpapierCustom'))
{
  class BriefpapierCustom extends Briefpapier
  {
    
  }
}


class AnfragePDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="anfrage";
    $this->doctypeOrig="Anfrage";
    parent::__construct($this->app,$projekt);
  } 


  function GetAnfrage($id,$info="",$extrafreitext="")
  {

    // das muss vom anfrage sein!!!!
    $this->setRecipientLieferadresse($id,"anfrage");

    $data = $this->app->DB->SelectArr("SELECT adresse, auftragid, bearbeiter, DATE_FORMAT(datum,'%d.%m.%Y') AS datum, belegnr, freitext, projekt, bodyzusatz FROM anfrage WHERE id='$id' LIMIT 1");
    $data = reset($data);
    extract($data,EXTR_OVERWRITE);

    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    $auftrag = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
    $ihrebestellnummer = "";
    $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);

    $this->projekt = $projekt;


    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
    }

    $this->doctype="deliveryreceipt";

    //if($belegnr<=0) $belegnr = "- Entwurf";


    if($info=="")
      $this->doctypeOrig="Anfrage $belegnr";
    else
      $this->doctypeOrig="Anfrage$info $belegnr";

    if($anfrage=="") $anfrage = "-";
    if($kundennummer=="") $kundennummer= "-";

    //$this->setCorrDetails(array("Auftrag"=>$auftrag,"Ihre Kunden-Nr."=>$kundennummer,"Versand"=>$datum,"Versand"=>$bearbeiter));
    $this->setCorrDetails(array("Auftrag"=>$auftrag,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,"Ihre Bestellnummer"=>$ihrebestellnummer,"Datum"=>$datum));



    $body=$this->app->erp->Firmendaten("anfrage_header");
    if($bodyzusatz!="") $body=$body."\r\n".$bodyzusatz;
    $body = $this->app->erp->ParseUserVars("anfrage",$id,$body);

    $this->setTextDetails(array(
          "body"=>$body,
          "footer"=>"$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("anfrage",$id,$this->app->erp->Firmendaten("anfrage_footer"))));

    $artikel = $this->app->DB->SelectArr("SELECT * FROM anfrage_position WHERE anfrage='$id' ORDER By sort");

    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM anfrage_position WHERE anfrage='$id' LIMIT 1");
    foreach($artikel as $key=>$value)
    {

      if($value[seriennummer]!="")
      {
        if( $value[beschreibung]!="")  $value[beschreibung] =  $value[beschreibung]."\n";
        $value[beschreibung] = "SN: ".$value[seriennummer]."\n\n";
      }

      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");

      $this->addItem(array('amount'=>$value[menge],
            'itemno'=>$value[nummer],
            'artikel'=>$value[artikel],
            'desc'=>ltrim($value[beschreibung]),
            'unit'=>$value[einheit],
            'hersteller'=>$value[hersteller],
            'herstellernummer'=>trim($value[herstellernummer]),
            "name"=>$value[bezeichnung]));
    }


    /* Dateiname */
    $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    $tmp_name = str_replace('.','',$tmp_name);

    $this->filename = $datum."_AF".$belegnr.".pdf";
    $this->setBarcode($belegnr);
  }


}
