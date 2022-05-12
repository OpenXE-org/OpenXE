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
class AdressstammblattPDF extends Dokumentenvorlage {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Dokumentenvorlage();
    $this->doctype="adresse";
    $this->doctypeOrig="Adressstammblatt";
    parent::__construct($this->app,$projekt);
  } 

  public function renderDocument() {
    // prepare page details
    SuperFPDF::__construct('P','mm','A4');


    $this->AddPage();
    $this->SetDisplayMode("real","single");

    $this->SetMargins(15,50);
    $this->SetAutoPageBreak(true,40);
    $this->AliasNbPages('{nb}');
    

    // Bei Adressstammblatt immer oben beginnen
    $this->abstand_betreffzeileoben=0;
    $this->logofile = "";//$this->app->erp->GetTMP()."/".$this->app->Conf->WFdbname."_logo.jpg";
    $this->briefpapier="";


    $schrift =  $this->app->erp->Firmendaten('schriftgroesse');

    $this->SetFontSize($schrift);
    $this->SetFont($this->GetFont(),'','10');
    $this->SetX($this->GetX()+160);
    $this->Cell(10,0,date("d.m.Y"),"","","L");

    $this->renderDoctype();

    $adresse = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='".$this->id."'");
    $adresse = reset($adresse);

    if($adresse['typ']=="firma")
    {
      $infofields[]=array("Firma",$adresse['name']);
      if($adresse['ansprechpartner']!="")
      $infofields[]=array("Ansprechpartner",$adresse['ansprechpartner']);
    } else {
      $infofields[]=array("Name",$adresse['name']);
    }

    $infofields[]=array("Anschrift",$adresse['land']."-".$adresse['plz']." ".$adresse['ort'].", ".$adresse['strasse']);

    $felder = array('telefon','telefax','mobil','email','web');
    foreach($felder as $feldname)
    {
      $infofields[]=array(ucfirst($feldname),$adresse[$feldname=='web'?'internetseite':$feldname]);
    }


    if($this->app->erp->Firmendaten("modul_mlm")==1)
    {
      $mlmvertragsbeginn = $this->app->DB->Select("SELECT DATE_FORMAT(mlmvertragsbeginn,'%d.%m.%Y') FROM adresse WHERE id='".$adresse['id']."' LIMIT 1");
      if($mlmvertragsbeginn=="00.00.0000") $mlmvertragsbeginn = "kein Vertragsbeginn eingestellt";
      $sponsorid = $this->app->DB->Select("SELECT sponsor FROM adresse WHERE id='".$adresse['id']."' LIMIT 1");
      if($sponsorid> 0)
        $sponsor = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='$sponsorid' LIMIT 1");
      else
        $sponsor = "Kein Sponsor vorhanden";

      $erfasstam = $this->app->DB->Select("SELECT DATE_FORMAT(zeitstempel,'%d.%m.%Y') FROM objekt_protokoll WHERE objekt='adresse' AND objektid='".$adresse['id']."'                         AND action_long='adresse_create' LIMIT 1");

      $infofields[]=array("Sponsor",$sponsor);
      $infofields[]=array("Erfasst am",$erfasstam);
      $infofields[]=array("Vertragsbeginn am",$mlmvertragsbeginn);
    }

    

    $infofields[]=array("UST-ID",$adresse['ustid']);


    if($adresse['kundennummer']!="")
      $numbers[] = array("Kunden Nr.",$adresse['kundennummer']);  

    if($adresse['lieferantennummer']!="")
      $numbers[] = array("Lieferanten Nr.",$adresse['lieferantennummer']);  

    if($adresse['mitarbeiternummer']!="")
      $numbers[] = array("Mitarbeiter Nr.",$adresse['mitarbeiternummer']);  

    if(count($numbers)>0)
      $this->renderInfoBox($numbers);

    $this->Ln(5);
    $this->renderHeading("Adressstammblatt",8);
    $this->renderInfoBox($infofields);


    
    $ansprechpartner_tmp = $this->app->DB->SelectArr("SELECT CONCAT(name,', ',if(bereich='','-',bereich),
      ', Telefon: ',telefon,
      ', Mobil: ',mobil,
      ', E-Mail: ',email
      ) as 'value' FROM ansprechpartner WHERE adresse='".$adresse['id']."'");

    for($i=0;$i<count($ansprechpartner_tmp);$i++) $ansprechpartner[] = $ansprechpartner_tmp[$i]['value'];
    if(count($ansprechpartner) > 0)
    { 
      $this->Ln(5);
      $this->renderHeading("Ansprechpartner",8);
      $this->renderInfoBoxSingle($ansprechpartner);
    }

    $lieferadressen_tmp = $this->app->DB->SelectArr("SELECT name,abteilung,unterabteilung,adresszusatz,strasse,plz,ort,land,
      telefon,email
      FROM lieferadressen WHERE adresse='".$adresse['id']."' ORDER by standardlieferadresse DESC");

    for($i=0;$i<count($lieferadressen_tmp);$i++) {
      $lieferadressen_tmp[$i]['value']=""; 
      foreach($lieferadressen_tmp[$i] as $key=>$value)
      {
        switch($key)
        {
          case "email": 
            $lieferadressen_tmp[$i]['value'] .= "E-Mail: $value, ";
          break;
       
          case "telefon": 
            $lieferadressen_tmp[$i]['value'] .= "Telefon: $value, ";
          break;
          case "telefax": 
            $lieferadressen_tmp[$i]['value'] .= "Telefax: $value, ";
          break;

          default:
            if($value!="")
              $lieferadressen_tmp[$i]['value'] .= "$value, ";
        }
      }

      $lieferadressen_tmp[$i]['value'] = trim($lieferadressen_tmp[$i]['value'],', ');
      if($i==0) $standard = " (Standard)"; else $standard="";

      $lieferadressen[] = $lieferadressen_tmp[$i]['value'].$standard;
    }
   
    if(count($lieferadressen) > 0)
    { 
      $this->Ln(5);
      $this->renderHeading("Lieferadressen",8);
      $this->renderInfoBoxSingle($lieferadressen);
    }


    $this->Ln(5);

    if($adresse['sonstiges']!="")
    {
      $this->renderHeading("Sonstiges",8);
      $this->SetFont($this->GetFont(),'',7);
      $this->MultiCell(180,4,$this->WriteHTML($adresse['sonstiges']));
    }

    $this->renderFooter();
  }


  function GetAdressstammblatt($id,$info="",$extrafreitext="")
  {
    $this->id = $id;
    // das muss vom reisekosten sein!!!!
    //$this->setRecipientLieferadresse($id,"reisekosten");

    // OfferNo, customerId, OfferDate
    /*$auftrag = $this->app->DB->Select("SELECT auftragid FROM reisekosten WHERE id='$id' LIMIT 1");
    $auftrag = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM reisekosten WHERE id='$id' LIMIT 1");
    $prefix = $this->app->DB->Select("SELECT prefix FROM reisekosten WHERE id='$id' LIMIT 1");
    $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM reisekosten WHERE id='$id' LIMIT 1");

    $this->von = $this->app->DB->Select("SELECT DATE_FORMAT(von,'%d.%m.%Y') FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->bis = $this->app->DB->Select("SELECT DATE_FORMAT(bis,'%d.%m.%Y') FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->von_zeit = $this->app->DB->Select("SELECT von_zeit FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->bis_zeit = $this->app->DB->Select("SELECT bis_zeit FROM reisekosten WHERE id='$id' LIMIT 1");
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->anlass = $this->app->DB->Select("SELECT anlass FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->freitext = $this->app->DB->Select("SELECT freitext FROM reisekosten WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM reisekosten WHERE id='$id' LIMIT 1");
    $mitarbeiter = $this->app->DB->Select("SELECT mitarbeiter FROM reisekosten WHERE id='$id' LIMIT 1");*/
    $kunde= $this->app->DB->SelectArr("SELECT name,kundennummer,lieferantennummer FROM adresse WHERE id='$id' LIMIT 1");
    $kunde = reset($kunde);

    $kundennummer = $kunde['kundennummer'];
    $this->name = $kunde['name'];

    $this->projektabkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt'");
    $this->firmenname = $this->app->erp->Firmendaten('name');
    $this->doctypeOrig="Adresse: ".$kunde['name'];
    $this->barcode=$belegnr;

    //$ohne_briefpapier = $this->app->DB->Select("SELECT ohne_briefpapier FROM reisekosten WHERE id='$id' LIMIT 1");




    //$artikel = $this->app->DB->SelectArr("SELECT *,DATE_FORMAT(datum,'%d.%m.%Y') as datum, CONCAT(rk.nummer,'- ',rk.beschreibung) as reisekostenart FROM reisekosten_position rp LEFT JOIN reisekostenart rk ON rk.id=rp.reisekostenart WHERE rp.reisekosten='$id' ORDER By rp.sort");

    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM reisekosten_position WHERE reisekosten='$id' LIMIT 1");

    /* Dateiname */
    $this->filename = date('Ymd')."_STAMMDATEN_ADRESSE_".$this->app->erp->Dateinamen($kunde['name']).".pdf";

    $this->setBarcode($id);
  }


}
