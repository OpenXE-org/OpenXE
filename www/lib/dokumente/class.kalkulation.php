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

class KalkulationPDF extends Briefpapier {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="kalkulation";
    $this->doctypeOrig="Kalkulation";
    parent::__construct($this->app,$projekt);
  } 

  function GetKalkulation($id)
  {
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    //$this->setRecipientDB($adresse);
    $this->setRecipientLieferadresse($id,"kalkulation");

    $data = $this->app->DB->SelectArr("SELECT adresse, kundennummer, ustid, land, bearbeiter, DATE_FORMAT(datum,'%d.%m.%Y') AS datum, belegnr, freitext, projekt, ohne_briefpapier FROM kalkulation WHERE id='$id' LIMIT 1");
    $data = reset($data);
    extract($data,EXTR_OVERWRITE);

    $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");

    $zahlungszieltageskonto = '';
    $zahlungszielskonto = '';
    $kalkulationersatz = '';
    $zahlungszieltage = '';
    $zahlungsweise = '';
    $waehrung = '';
    $vertrieb = '';
    $anfrage = '';
    $typ = '';


    $zahlungsweise = $this->app->erp->ReadyForPDF($zahlungsweise);
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);
    $anfrage = $this->app->erp->ReadyForPDF($anfrage);
    $this->app->erp->BeschriftungSprache($sprache);
    if($waehrung)$this->waehrung = $waehrung;
    $this->projekt = $projekt;
    $this->sprache = $sprache;
    $this->anrede = $typ;




    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }


    //$zahlungstext = "\nZahlungsweise: $zahlungsweise ";
    if($zahlungsweise=="rechnung")
    {
      if($zahlungszieltage >0) {
        $zahlung_rechnung_de = $this->app->erp->Beschriftung("zahlung_rechnung_de");
        if(empty($zahlung_rechnung_de)){
          $zahlungstext = $this->app->erp->Beschriftung("dokument_zahlung_rechnung_anab");
        }
        else{
          $zahlungstext = $zahlung_rechnung_de;
        }
      }
      else {
        $zahlungstext = $this->app->erp->Beschriftung("zahlung_rechnung_sofort_de");
      }

      if($zahlungszielskonto>0) $zahlungstext .= "\n".$this->app->erp->Beschriftung("dokument_skonto")." $zahlungszielskonto% ".$this->app->erp->Beschriftung("dokument_innerhalb")." $zahlungszieltageskonto ".$this->app->erp->Beschriftung("dokument_tagen");
    } else {
      $zahlungstext = $this->app->erp->Beschriftung("zahlung_".$zahlungsweise."_de");
      if($zahlungstext=="")
        $zahlungstext = "Bezahlung per ".ucfirst($zahlungsweise);
    }

    $zahlungsweise = ucfirst($zahlungsweise);	
 
    if($belegnr=="" || $belegnr=="0") $belegnr = "- ".$this->app->erp->Beschriftung("dokument_entwurf");

    if($kalkulationersatz)
      $this->doctypeOrig=($this->app->erp->Beschriftung("bezeichnungkalkulationersatz")?$this->app->erp->Beschriftung("bezeichnungkalkulationersatz"):$this->app->erp->Beschriftung("dokument_kalkulation"))." $belegnr";
    else
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_kalkulation")." $belegnr";
    
    $this->zusatzfooter = " (AN$belegnr)";

    if($kalkulation=="") $kalkulation = "-";
    if($kundennummer=="") $kundennummer= "-";

    if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden)
    {
      $sCD = array($this->app->erp->Beschriftung("dokument_kalkulation_anfrage")=>$anfrage,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("dokument_datum")=>$datum);
      if(!$briefpapier_bearbeiter_ausblenden)
      {
        if($bearbeiter)$sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")] = $bearbeiter;
      }elseif(!$briefpapier_vertrieb_ausblenden)
      {
        if($vertrieb)$sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")] = $vertrieb;
      }
      $this->setCorrDetails($sCD);
      //$this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_kalkulation_anfrage")=>$anfrage,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("dokument_datum")=>$datum));
    }else{
    if($vertrieb==$bearbeiter)
        $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_kalkulation_anfrage")=>$anfrage,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("dokument_datum")=>$datum,$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")=>$bearbeiter));
      else
        $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_kalkulation_anfrage")=>$anfrage,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("dokument_datum")=>$datum,$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")=>$vertrieb));
    }

    $body=$this->app->erp->Beschriftung("kalkulation_header");
    $body = $this->app->erp->ParseUserVars("kalkulation",$id,$body);
    $this->setTextDetails(array(
          "body"=>$body,
          "footer"=>"$freitext\r\n".$this->app->erp->ParseUserVars("kalkulation",$id,$this->app->erp->Beschriftung("kalkulation_footer")."\r\n$steuer\r\n$zahlungstext")));

    $artikel = $this->app->DB->SelectArr("SELECT * FROM kalkulation_position WHERE kalkulation='$id' ORDER By sort");

    $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM kalkulation_position WHERE kalkulation='$id'");
    if($summe_rabatt > 0) $this->rabatt=1;

    if($this->app->erp->Firmendaten("modul_verband")=="1") $this->rabatt=1; 

    $summe = 0;
    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM kalkulation_position WHERE kalkulation='$id' LIMIT 1");
    foreach($artikel as $key=>$value)
    {
      if($value[umsatzsteuer] != "ermaessigt") $value[umsatzsteuer] = "normal";
      // Herstellernummer von Artikel
      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");

      $is_kalkulation_mit_bild=0;
      if($is_kalkulation_mit_bild) {
          $image_tmp = $this->app->erp->GetArtikelStandardbild($value['artikel']);
          $value['image'] = $image_tmp['image'];
          $value['image_type'] = $image_tmp['extenstion'];
      }

      if($value[optional]=="1") $value[bezeichnung] = "Optional: ".$value[bezeichnung];


      if(!$this->app->erp->Export($land))
      {
        $value[zolltarifnummer]="";
        $value[herkunftsland]="";
      }

      $tmp = $this->app->erp->GetKalkulationartAssoc();
      $value[kalkulationart] = $tmp[$value[kalkulationart]];

      $this->addItem(array('currency'=>$value[waehrung],
            'amount'=>$value[menge],
            'price'=>$value[betrag],
            'tax'=>$value[umsatzsteuer],
            'itemno'=>$value[nummer],
            'desc'=>$value[kalkulationart]."\r\n".$value[beschreibung],
            'optional'=>$value[optional],
            'unit'=>$value[einheit],
            'hersteller'=>$value[hersteller],
            'zolltarifnummer'=>$value[zolltarifnummer],
            'herkunftsland'=>$value[herkunftsland],
            'herstellernummer'=>trim($value[herstellernummer]),
            'lieferdatum'=>$value['lieferdatum'],
            'lieferdatumkw'=>$value['lieferdatumkw'],
            'artikelnummerkunde'=>$value['artikelnummerkunde'],
            'grundrabatt'=>$value[grundrabatt],
            'rabatt1'=>$value[rabatt1],
            'rabatt2'=>$value[rabatt2],
            'rabatt3'=>$value[rabatt3],
            'rabatt4'=>$value[rabatt4],
            'rabatt5'=>$value[rabatt5],

            "name"=>ltrim($value[bezeichnung]),
            "rabatt"=>$value[rabatt]));

      $netto_gesamt = $value[menge]*($value[preis]-($value[preis]/100*$value[rabatt]));

      if($value[optional]!="1"){
        $summe = $summe + $netto_gesamt;

        if($value[umsatzsteuer]=="" || $value[umsatzsteuer]=="normal")
        {
          $summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal(false,$id,"kalkulation"));
        }
        else {
          $summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt(false,$id,"kalkulation"));
        }
      }
    }

    if(0)//$this->app->erp->KalkulationMitUmsatzeuer($id))
    {
      $this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
    } else {
      $this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));
    }

    /* Dateiname */
    //$tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    //$tmp_name = str_replace('.','',$tmp_name);

    $this->filename = $datum."_KA".$belegnr.".pdf";
    $this->setBarcode($belegnr);
  }


}
