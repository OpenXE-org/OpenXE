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
class ReisekostenPDF extends Dokumentenvorlage {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Dokumentenvorlage();
    $this->doctype="reisekosten";
    $this->doctypeOrig="Reisekosten";
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

    //if($this->barcode!="")
    {
      $y = $this->GetY();
      $this->Code39(155, $y+1, $this->barcode, 1, 5);
    }

    // Bei Reisekosten immer oben beginnen
    $this->abstand_betreffzeileoben=0;
    //$this->logofile = "";//$this->app->erp->GetTMP()."/".$this->app->Conf->WFdbname."_logo.jpg";
    //$this->briefpapier="";

    $this->renderDoctype();

    $infofields[]=array("Firma",$this->firmenname);
    $infofields[]=array("Kunde",$this->name);
    $infofields[]=array("Mitarbeiter",$this->mitarbeiter);
    $infofields[]=array("Projekt",$this->projektabkuerzung);
    $infofields[]=array("Datum Abrechnung",$this->datum);
    $infofields[]=array("Anlass",$this->anlass);
    $infofields[]=array("Von",$this->von." (".$this->von_zeit." Uhr)");
    $infofields[]=array("Bis",$this->bis." (".$this->bis_zeit." Uhr)");

    $this->renderInfoBox($infofields);

    $this->renderReisekostenItems();
    if(count($this->gesamt)>0)
    {
      foreach($this->gesamt as $key=>$value)
      {
        $summe_infofields[]=array("Summe $key %",number_format($value,2,',','.')." EUR (inkl. $key % MwSt)");
      }
    }
    $this->renderInfoBox($summe_infofields);

    if($this->freitext!="") {
      $this->Ln(5);
      $this->renderTextBox("Bemerkung:");
      $this->renderTextBox($this->freitext);
    }

    $this->Ln(5);

    $unterschrift[]=array("Unterschrift Arbeitnehmer","");
    $unterschrift[]=array("Ort, Datum","");
    $this->renderInfoBox($unterschrift,10);

    $this->Ln(5);

    $unterschrift2[]=array("Betrag Privat ausgelegt",number_format($this->gesamt_privat['privat'],2,',','.')." EUR");
    $unterschrift2[]=array("Betrag erstattet","");
    $this->renderInfoBox($unterschrift2,10);

    $this->renderFooter();
  }



  function GetReisekosten($id,$info="",$extrafreitext="")
  {
    $this->doctypeid = $id;
    // das muss vom reisekosten sein!!!!
    //$this->setRecipientLieferadresse($id,"reisekosten");

    $data = $this->app->DB->SelectArr("SELECT r.adresse, r.auftragid, r.bearbeiter, r.prefix, DATE_FORMAT(r.datum,'%d.%m.%Y') AS datum, DATE_FORMAT(r.von,'%d.%m.%Y') AS von, DATE_FORMAT(r.bis,'%d.%m.%Y') AS bis, r.von_zeit, r.bis_zeit, r.belegnr, r.anlass, r.freitext, r.projekt, a.name as mitarbeiter, r.name, r.ohne_briefpapier FROM reisekosten r LEFT JOIN adresse a ON r.mitarbeiter = a.id WHERE r.id='$id' LIMIT 1");

    $data = reset($data);
    extract($data,EXTR_OVERWRITE);

    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    $auftrag = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
    $projektname = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projekt' LIMIT 1");

    $this->firmenname = $this->app->erp->Firmendaten('name');
    $this->projektabkuerzung = $projektname;
    $this->von_zeit = $von_zeit;
    $this->bis_zeit = $bis_zeit;
    $this->freitext = $freitext;
    $this->anlass = $anlass;
    $this->barcode=$belegnr;
    $this->datum = $datum;
    $this->name = $name;
    $this->von = $von;
    $this->bis = $bis;
    $this->mitarbeiter = $mitarbeiter;


    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }

    $this->doctype="reisekosten";
    if($belegnr=="") $belegnr = "- Entwurf";

    if($info=="")
      $this->doctypeOrig="Reisekostenabrechnung $belegnr";
    else
      $this->doctypeOrig="Reisekosten$info $belegnr";

    $artikel = $this->app->DB->SelectArr("SELECT *,DATE_FORMAT(datum,'%d.%m.%Y') as datum, CONCAT(rk.nummer,'- ',rk.beschreibung) as reisekostenart FROM reisekosten_position rp LEFT JOIN reisekostenart rk ON rk.id=rp.reisekostenart WHERE rp.reisekosten='$id' ORDER By rp.sort");

    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM reisekosten_position WHERE reisekosten='$id' LIMIT 1");
    foreach($artikel as $key=>$value)
    {

      if($value[seriennummer]!="")
      {
        if( $value[beschreibung]!="")  $value[beschreibung] =  $value[beschreibung]."\n";
        $value[beschreibung] = "Seriennummer: ".$value[seriennummer]."\n\n";
      }

      $value[menge] = $this->app->erp->get_time_difference($value[von].":00",$value[bis].":00");

      $adr_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$value[adresse]}' LIMIT 1");

      $add_sum = explode(":", $value[menge]);
      $hour = $hour + $add_sum[0];
      $minutes = $minutes + $add_sum[1];

      if($value[uststeuersatz]>0)
      {
        $netto =		($value[betrag] * 100)/(100+$value[uststeuersatz]);
      } else $netto = $value[betrag];

      $this->gesamt[$value[uststeuersatz]] = $this->gesamt[$value[uststeuersatz]] + $value[betrag]; 

      $this->gesamt_privat[$value[bezahlt_wie]] = $this->gesamt_privat[$value[bezahlt_wie]] + $value[betrag];

      $this->addItem(array('brutto'=>number_format($value[betrag],2,',','.'),
            'tax'=>$value[uststeuersatz],
            'bezahlt_wie'=>$value[bezahlt_wie],
            'netto'=>number_format($netto,2,',','.'),
            'desc'=>ltrim($value[bezeichnung]." (am ".$value[datum].")"),
            "name"=>$value[reisekostenart]));
    }

    /* Dateiname */
    $this->filename = $datum."_RK".$belegnr.".pdf";

    $this->setBarcode($belegnr);
  }


}
