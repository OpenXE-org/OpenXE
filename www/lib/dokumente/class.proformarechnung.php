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

class ProformarechnungPDF extends BriefpapierCustom {
  public $doctype;
  public $doctypeid;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="proformarechnung";
    $this->doctypeOrig=($this->app->erp->Beschriftung("bezeichnungproformarechnungersatz")?$this->app->erp->Beschriftung("bezeichnungproformarechnungersatz"):$this->app->erp->Beschriftung("dokument_proformarechnung"))." $belegnr";
    parent::__construct($this->app,$projekt);
  } 

  function GetProformarechnung($id,$als="",$doppeltmp=0, $_datum = null)
  {

    if($this->app->erp->Firmendaten("steuerspalteausblenden")=="1")
    { 
      // pruefe ob es mehr als ein steuersatz gibt // wenn ja dann darf man sie nicht ausblenden      
      $check = $this->app->erp->SteuerAusBeleg($this->doctype,$id);      
      if(count($check)>1)$this->ust_spalteausblende=false;
      else $this->ust_spalteausblende=true;
    }

    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    $this->doctypeid=$id;
    $this->setRecipientLieferadresse($id,"proformarechnung");

    $data = $this->app->DB->SelectArr(
      "SELECT adresse, kundennummer, sprache, auftrag, buchhaltung, bearbeiter, vertrieb, 
       lieferschein AS lieferscheinid, projekt, DATE_FORMAT(datum,'%d.%m.%Y') AS datum, 
       DATE_FORMAT(mahnwesen_datum,'%d.%m.%Y') AS mahnwesen_datum, DATE_FORMAT(lieferdatum,'%d.%m.%Y') AS lieferdatum, 
       belegnr, bodyzusatz, doppel, freitext, systemfreitext, ustid, typ, keinsteuersatz, soll, ist, land, 
       zahlungsweise, zahlungsstatus, zahlungszieltage, zahlungszieltageskonto, zahlungszielskonto, ohne_briefpapier, 
       ihrebestellnummer, ust_befreit, waehrung, versandart, 
       DATE_FORMAT(DATE_ADD(datum, INTERVAL zahlungszieltage DAY),'%d.%m.%Y') AS zahlungdatum, 
       DATE_FORMAT(DATE_ADD(datum, INTERVAL zahlungszieltageskonto DAY),'%d.%m.%Y') AS zahlungszielskontodatum,  
       abweichendebezeichnung AS proformarechnungersatz, zollinformation, lieferland, abweichendelieferadresse
       FROM proformarechnung WHERE id='$id' LIMIT 1"
    );
    $data = reset($data);
    extract($data,EXTR_OVERWRITE);

    if($abweichendelieferadresse==1 && !empty($lieferland)){
      $land = $lieferland;
    }

    if(empty($kundennummer))$kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    if(empty($sprache))$sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
    $trackingnummer = $this->app->DB->Select("SELECT tracking FROM versand WHERE proformarechnung='$id' LIMIT 1");
    $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
    $this->app->erp->BeschriftungSprache($sprache);

    if($waehrung)$this->waehrung = $waehrung;
    if($doppeltmp==1) $doppel = $doppeltmp;
    $this->projekt = $projekt;
    $this->anrede = $typ;

    $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);

    $seriennummern  = array();
    if($lieferscheinid)$seriennummern = $this->app->DB->SelectArr("SELECT * FROM seriennummern WHERE lieferschein='$lieferscheinid' AND lieferschein <> 0");
    $chargen = array();
    if($lieferscheinid)$chargen = $this->app->DB->SelectArr("SELECT * FROM chargen WHERE lieferschein='$lieferscheinid' AND lieferschein <> 0");

    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }

    if($zahlungszieltageskonto<=0)      
      $zahlungszielskontodatum = $zahlungdatum;
    else      
      $zahlungszielskontodatum = $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(datum, INTERVAL $zahlungszieltageskonto DAY),'%d.%m.%Y') FROM proformarechnung WHERE id='$id' LIMIT 1");                       

    if(!$this->app->erp->ProformarechnungMitUmsatzeuer($id)){
      $this->ust_befreit=true;
    }


    $zahlungsweise = strtolower($zahlungsweise);
    //if($zahlungsweise=="proformarechnung"&&$zahlungsstatus!="bezahlt")
/*
    if(1)$zahlungsweise=="rechnung" || $zahlungsweise=="einzugsermaechtigung" || $zahlungsweise=="lastschrift")
    {
      if($zahlungsweise=="rechnung")
      {
             } else {
        //lastschrift
        $zahlungsweisetext = $this->app->erp->Beschriftung("zahlung_".$zahlungsweise."_de");
        if($zahlungsweisetext=="") $zahlungsweisetext ="Der Betrag wird von Ihrem Konto abgebucht.";
        if($zahlungszielskonto!=0)
          $zahlungsweisetext .="\r\n".$this->app->erp->Beschriftung("dokument_skonto")." $zahlungszielskonto % aus Zahlungskonditionen";	
      }
    } 
    else {
      $zahlungsweisetext = $this->app->erp->Beschriftung("zahlung_".$zahlungsweise."_de");
      if($zahlungsweisetext=="" || $zahlungsweise=="vorkasse")
        $zahlungsweisetext = $this->app->erp->Beschriftung("dokument_zahlung_per")." ".ucfirst($zahlungsweise);
    }
*/
    if($zahlungszieltage==0){
      $zahlungsweisetext = $this->app->erp->Beschriftung("zahlung_proformarechnung_sofort_de");
      if($zahlungsweisetext=="") $zahlungsweisetext ="Proformarechnung zahlbar sofort. ";
    }
    else {
      $zahlungsweisetext = $this->app->erp->Beschriftung("zahlung_proformarechnung_de");
      if($zahlungsweisetext=="") $zahlungsweisetext ="Proformarechnung zahlbar innerhalb von {ZAHLUNGSZIELTAGE} Tagen bis zum {ZAHLUNGBISDATUM}. ";
      $zahlungsweisetext = str_replace("{ZAHLUNGSZIELTAGE}",$zahlungszieltage,$zahlungsweisetext);
      $zahlungsweisetext = str_replace("{ZAHLUNGBISDATUM}",$zahlungdatum,$zahlungsweisetext);
    }

    if($zahlungszielskonto!=0)
    {
      $zahlungsweisetext .="\n".$this->app->erp->Beschriftung("dokument_skonto")." $zahlungszielskonto % ".$this->app->erp->Beschriftung("dokument_innerhalb")." $zahlungszieltageskonto ".$this->app->erp->Beschriftung("dokument_tagebiszum")." ".$zahlungszielskontodatum;
    }


    if($zahlungszielskonto!=0)
    {
      $zahlungsweisetext = str_replace("{ZAHLUNGSZIELSKONTO}",$zahlungszielskonto,$zahlungsweisetext);
      $zahlungsweisetext = str_replace("{ZAHLUNGSZIELTAGESKONTO}",$zahlungszieltageskonto,$zahlungsweisetext);
      $zahlungsweisetext = str_replace("{ZAHLUNGSZIELSKONTODATUM}",$zahlungszielskontodatum,$zahlungsweisetext);
    } else {
      $zahlungsweisetext = str_replace("{ZAHLUNGSZIELSKONTO}","",$zahlungsweisetext);
      $zahlungsweisetext = str_replace("{ZAHLUNGSZIELTAGESKONTO}","",$zahlungsweisetext);
      $zahlungsweisetext = str_replace("{ZAHLUNGSZIELSKONTODATUM}","",$zahlungsweisetext);
    }

/*
    if($belegnr=="" || $belegnr=="0") $belegnr = "- ".$this->app->erp->Beschriftung("dokument_entwurf");
    else {
      if($doppel==1 || $als=="doppel")
        $belegnr .= " (".$this->app->erp->Beschriftung("dokument_proformarechnung_kopie").")";
    }
*/
    if($als=="zahlungserinnerung") 
      $this->doctypeOrig="Zahlungserinnerung vom ".(is_null($_datum)?$mahnwesen_datum:$_datum);
    else if($als=="mahnung1") 
      $this->doctypeOrig="1. Mahnung vom ".(is_null($_datum)?$mahnwesen_datum:$_datum);
    else if($als=="mahnung2") 
      $this->doctypeOrig="2. Mahnung vom ".(is_null($_datum)?$mahnwesen_datum:$_datum);
    else if($als=="mahnung3") 
      $this->doctypeOrig="3. Mahnung vom ".(is_null($_datum)?$mahnwesen_datum:$_datum);
    else if($als=="inkasso") 
      $this->doctypeOrig="Inkasso-Mahnung vom ".(is_null($_datum)?$mahnwesen_datum:$_datum);
    else
    {
      if($proformarechnungersatz)
        $this->doctypeOrig=($this->app->erp->Beschriftung("bezeichnungproformarechnungersatz")?$this->app->erp->Beschriftung("bezeichnungproformarechnungersatz"):$this->app->erp->Beschriftung("dokument_proformarechnung"))." $belegnr";
      else
        $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_proformarechnung")." $belegnr";
    }



    $this->zusatzfooter = " (RE$belegnr)";

    if($proformarechnung=="") $proformarechnung = "-";
    if($kundennummer=="") $kundennummer= "-";

    if($auftrag=="0") $auftrag = "-";
    if($lieferschein=="0") $lieferschein= "-";
    if($lieferschein=="") $lieferschein= "-";

    $datumlieferschein = $this->app->DB->Select("SELECT DATE_FORMAT(datum, '%d.%m.%Y') 
        FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");

    if($datumlieferschein=="00.00.0000") $datumlieferschein = $datum;
    if($lieferdatum=="00.00.0000") $lieferdatum = $datum;
    if($mahnwesen_datum=="00.00.0000") $mahnwesen_datum = "";

    //* start
    if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden)
    {
      if($lieferschein!='-')
      {
        if($auftrag!="-")
        {
          $sCD = array($this->app->erp->Beschriftung("dokument_auftrag")=>$auftrag,$this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferschein")=>$lieferschein,$this->app->erp->Beschriftung("dokument_lieferdatum")=>$datumlieferschein
                );
        }
        else
        {
          $sCD = array($this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferschein")=>$lieferschein,$this->app->erp->Beschriftung("dokument_lieferdatum")=>$datumlieferschein
                );
        }
      }
      else {
        if($auftrag!="-")
        {
          $sCD = array($this->app->erp->Beschriftung("dokument_auftrag")=>$auftrag,$this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferdatum")=>$lieferdatum
                );
        }
        else
        {
          $sCD = array($this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferdatum")=>$lieferdatum,
                $this->app->erp->Beschriftung("dokument_ansprechpartner")=>$buchhaltung
                );
        }
      }
      if(!$briefpapier_bearbeiter_ausblenden)
      {
        if($bearbeiter)$sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")] = $bearbeiter;
      }elseif(!$briefpapier_vertrieb_ausblenden)
      {
        if($vertrieb)$sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")] = $vertrieb;
      }
      $this->setCorrDetails($sCD);
      
    }else{
      if($vertrieb!=$bearbeiter)
      {
        if($lieferschein!='-')
        {
          if($auftrag!="-")
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_auftrag")=>$auftrag,$this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferschein")=>$lieferschein,$this->app->erp->Beschriftung("dokument_lieferdatum")=>$datumlieferschein,
                  $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")=>$vertrieb
                  ));
          else
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferschein")=>$lieferschein,$this->app->erp->Beschriftung("dokument_lieferdatum")=>$datumlieferschein,
                  $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")=>$vertrieb
                  ));
        }
        else {
          if($auftrag!="-")
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_auftrag")=>$auftrag,$this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferdatum")=>$lieferdatum,
                  $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")=>$vertrieb
                  ));
          else
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferdatum")=>$lieferdatum,
                  $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")=>$vertrieb
                  ));
        }
        //*ende hack
      } else {
        //start hack
        if($lieferschein!='-')
        {
          if($auftrag!="-")
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_auftrag")=>$auftrag,$this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferschein")=>$lieferschein,$this->app->erp->Beschriftung("dokument_lieferdatum")=>$datumlieferschein,
                  $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")=>$bearbeiter
                  ));
          else
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferschein")=>$lieferschein,$this->app->erp->Beschriftung("dokument_lieferdatum")=>$datumlieferschein,
                  $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")=>$bearbeiter
                  ));
        }
        else {
          if($auftrag!="-")
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_auftrag")=>$auftrag,$this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferdatum")=>$lieferdatum,
                  $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")=>$bearbeiter
                  ));
          else
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_proformarechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferdatum")=>$lieferdatum,
                  $this->app->erp->Beschriftung("dokument_ansprechpartner")=>$buchhaltung
                  ));
        }
      }
    }
    //ende hack

    //if(!$this->app->erp->ProformarechnungMitUmsatzeuer($id) && $ustid!="" )
    if(!$this->app->erp->ProformarechnungMitUmsatzeuer($id) && $keinsteuersatz!="1")
    {
      $this->ust_befreit=true;
      if($keinsteuersatz!="1"){
        if($this->app->erp->Export($land))
          $steuer = $this->app->erp->Beschriftung("export_lieferung_vermerk");
        else
          $steuer = $this->app->erp->Beschriftung("eu_lieferung_vermerk");
        $steuer = str_replace('{USTID}',$ustid,$steuer);
        $steuer = str_replace('{LAND}',$land,$steuer);
      }
    }

    if($als!="")
    {
      $body = $this->app->erp->MahnwesenBody($id,$als,$_datum);
      $footer =$this->app->erp->ParseUserVars("proformarechnung",$id, $this->app->erp->Beschriftung("proformarechnung_footer"));
    }
    else {
      $body = $this->app->erp->Beschriftung("proformarechnung_header");
      if($bodyzusatz!="") $body=$body."\r\n".$bodyzusatz;
      $body = $this->app->erp->ParseUserVars("proformarechnung",$id,$body);

      if ($versandart!="" && $trackingnummer!=""){
        $versandinfo = "$versandart: $trackingnummer\r\n";
      }else{ $versandinfo ="";}


      if($systemfreitext!="") $systemfreitext = "\r\n\r\n".$systemfreitext;
      //$footer = $versandinfo."$freitext"."\r\n".$this->app->erp->ParseUserVars("proformarechnung",$id,$this->app->erp->Beschriftung("proformarechnung_footer").
      //  "\r\n$steuer\r\n$zahlungsweisetext").$systemfreitext;


      if($this->app->erp->Firmendaten("footer_reihenfolge_proformarechnung_aktivieren")=="1")
      {
        $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_proformarechnung");
        if($footervorlage=="")          $footervorlage = "{FOOTERVERSANDINFO}{FOOTERFREITEXT}\r\n{FOOTERTEXTVORLAGERECHNUNG}\r\n{FOOTERSTEUER}\r\n{FOOTERZAHLUNGSWEISETEXT}{FOOTERSYSTEMFREITEXT}";

        $footervorlage = str_replace('{FOOTERVERSANDINFO}',$versandinfo,$footervorlage);
        $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
        $footervorlage = str_replace('{FOOTERTEXTVORLAGERECHNUNG}',$this->app->erp->Beschriftung("proformarechnung_footer"),$footervorlage);
        $footervorlage = str_replace('{FOOTERSTEUER}',$steuer,$footervorlage);        $footervorlage = str_replace('{FOOTERZAHLUNGSWEISETEXT}',$zahlungsweisetext,$footervorlage);
        $footervorlage = str_replace('{FOOTERSYSTEMFREITEXT}',$systemfreitext,$footervorlage);
        $footervorlage  = $this->app->erp->ParseUserVars("proformarechnung",$id,$footervorlage);
        $footer = $footervorlage;
      } else {
        $footer = $versandinfo."$freitext"."\r\n".$this->app->erp->ParseUserVars("proformarechnung",$id,$this->app->erp->Beschriftung("proformarechnung_footer").
          "\r\n$steuer\r\n$zahlungsweisetext").$systemfreitext;
      }
    }

    $this->setTextDetails(array(
          "body"=>$body,
          "footer"=>$footer));

    $artikel = $this->app->DB->SelectArr("SELECT * FROM proformarechnung_position WHERE proformarechnung='$id' ORDER By sort");
    $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM proformarechnung_position WHERE proformarechnung='$id'");
    if($summe_rabatt <> 0) $this->rabatt=1;

    if($this->app->erp->Firmendaten("modul_verband")=="1") $this->rabatt=1; 
    $steuersatzV = $this->app->erp->GetSteuersatzNormal(false,$id,"proformarechnung");
    $steuersatzR = $this->app->erp->GetSteuersatzErmaessigt(false,$id,"proformarechnung");
    $gesamtsteuern = 0;
    $mitumsatzsteuer = $this->app->erp->ProformarechnungMitUmsatzeuer($id);
    foreach($artikel as $key=>$value)
    {
      if($value['umsatzsteuer'] != "ermaessigt" && $value['umsatzsteuer'] != "befreit") $value['umsatzsteuer'] = "normal";
      $tmpsteuersatz = null;
      $tmpsteuertext = null;
      $this->app->erp->GetSteuerPosition('proformarechnung', $value['id'],$tmpsteuersatz, $tmpsteuertext);
      if(is_null($value['steuersatz']) || $value['steuersatz'] < 0)
      {
        if($value['umsatzsteuer'] == "ermaessigt")
        {
          $value['steuersatz'] = $steuersatzR;
        }elseif($value['umsatzsteuer'] == "befreit")
        {
          $value['steuersatz'] = 0;
        }else{
          $value['steuersatz'] = $steuersatzV;
        }
        if(!is_null($tmpsteuersatz))$value['steuersatz'] = $tmpsteuersatz;
      }
      if($tmpsteuertext && !$value['steuertext'])$value['steuertext'] = $tmpsteuertext;
      if(!$mitumsatzsteuer)$value['steuersatz'] = 0;
      /*
      $limit = 60;	
      $summary= $value['bezeichnung'];
      if (strlen($summary) > $limit)
      {
        $value['desc']= $value['bezeichnung'];
        $value['bezeichnung'] = substr($summary, 0, strrpos(substr($summary, 0, $limit), ' ')) . '...';
      }
*/

      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

      if($zollinformation)
      {

        if($value['gewicht']=="") $value['gewicht'] = $this->app->DB->Select("SELECT gewicht FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
        if($value['zolltarifnummer']=="" || $value['zolltarifnummer']=="0") $value['zolltarifnummer'] = $this->app->DB->Select("SELECT zolltarifnummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
        if($value['herkunftsland']=="" || $value['herkunftsland']=="0") $value['herkunftsland'] = $this->app->DB->Select("SELECT herkunftsland FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");


        $gewichtbezeichnung = $this->app->erp->Firmendaten('gewichtbezeichnung');
        if($gewichtbezeichnung=="") $gewichtbezeichnung = "kg";
        $value['beschreibung'] = $value['beschreibung']."\r\nCustoms tariff number: ".$value['zolltarifnummer']." Country of origin: ".$value['herkunftsland'];
        if($value['gewicht']!="") $value['beschreibung'] = $value['beschreibung']."\r\nWeight: ".floatval($value['menge'])." x ".$value['gewicht']." ".$gewichtbezeichnung." = ".floatval($value['gewicht']*$value['menge'])." ".$gewichtbezeichnung;
      } else {
        $value['zolltarifnummer']="";
        $value['herkunftsland']="";
      }


      if(!empty($seriennummern))
      {
        foreach($seriennummern as $seriennummern=>$seriennummer)
        {
          $seriennummernliste = $seriennummernliste.$seriennummer['seriennummer'].";";
        }

        $seriennummernliste=substr($seriennummernliste, 0, -1);

        $value['seriennummern'] = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

        if ($value['seriennummern']=="keine"){
          $value['beschreibung'] = $value['beschreibung'];
        }elseif ($value['seriennummern']==""){
          $value['beschreibung'] = $value['beschreibung'];
        }else{
          $value['beschreibung'] = $value['beschreibung']."\r\n".$this->app->erp->Beschriftung("dokument_seriennummer").": ". $seriennummernliste;
        }
      }

      if(!empty($chargen))
      {
        foreach($chargen as $chargen=>$charge)
        {
          $chargenliste = $chargenliste.$charge['charge'].";";
        }

        $chargenliste=substr($chargenliste, 0, -1);

        $value['chargen'] = $this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

        if ($value['chargen']=="0"){
          $value['beschreibung'] = $value['beschreibung'];
        }else{
          $value['beschreibung'] = $value['beschreibung']."\r\n".$this->app->erp->Beschriftung("dokument_charge").": ". $chargenliste;
        }
      }


      if($value['explodiert_parent_artikel'] > 0)
      {
        $check_ausblenden = $this->app->DB->Select("SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$value['explodiert_parent_artikel']."' LIMIT 1");
      } else $check_ausblenden=0;

      $value = $this->CheckPosition($value,"proformarechnung",$this->doctypeid,$value['id']);

      $value['menge'] = floatval($value['menge']);

      if($check_ausblenden!=1)// && $als=="") //TODO MAHNWESEN
      {
        $this->addItem(array('currency'=>$value['waehrung'],
              'amount'=>$value['menge'],
              'price'=>$value['preis'],
              'tax'=>$value['umsatzsteuer'],
              'steuersatz'=>$value['steuersatz'],
              'itemno'=>$value['nummer'],
              'artikel'=>$value['artikel'],
              'unit'=>$value['einheit'],
              'desc'=>$value['beschreibung'],
              'hersteller'=>$value['hersteller'],
              'zolltarifnummer'=>$value['zolltarifnummer'],
              'herkunftsland'=>$value['herkunftsland'],
              'herstellernummer'=>trim($value['herstellernummer']),
              'artikelnummerkunde'=>$value['artikelnummerkunde'],
              'lieferdatum'=>$value['lieferdatum'],
              'lieferdatumkw'=>$value['lieferdatumkw'],
              'grundrabatt'=>$value['grundrabatt'],
              'rabatt1'=>$value['rabatt1'],
              'rabatt2'=>$value['rabatt2'],
              'rabatt3'=>$value['rabatt3'],
              'rabatt4'=>$value['rabatt4'],
              'rabatt5'=>$value['rabatt5'],
              'freifeld1'=>$value['freifeld1'],
              'freifeld2'=>$value['freifeld2'],
              'freifeld3'=>$value['freifeld3'],
              'freifeld4'=>$value['freifeld4'],
              'freifeld5'=>$value['freifeld5'],
              'freifeld6'=>$value['freifeld6'],
              'freifeld7'=>$value['freifeld7'],
              'freifeld8'=>$value['freifeld8'],
              'freifeld9'=>$value['freifeld9'],
              'freifeld10'=>$value['freifeld10'],
              'freifeld11'=>$value['freifeld11'],
              'freifeld12'=>$value['freifeld12'],
              'freifeld13'=>$value['freifeld13'],
              'freifeld14'=>$value['freifeld14'],
              'freifeld15'=>$value['freifeld15'],
              'freifeld16'=>$value['freifeld16'],
              'freifeld17'=>$value['freifeld17'],
              'freifeld18'=>$value['freifeld18'],
              'freifeld19'=>$value['freifeld19'],
              'freifeld20'=>$value['freifeld20'],
              'freifeld21'=>$value['freifeld21'],
              'freifeld22'=>$value['freifeld22'],
              'freifeld23'=>$value['freifeld23'],
              'freifeld24'=>$value['freifeld24'],
              'freifeld25'=>$value['freifeld25'],
              'freifeld26'=>$value['freifeld26'],
              'freifeld27'=>$value['freifeld27'],
              'freifeld28'=>$value['freifeld28'],
              'freifeld29'=>$value['freifeld29'],
              'freifeld30'=>$value['freifeld30'],
              'freifeld31'=>$value['freifeld31'],
              'freifeld32'=>$value['freifeld32'],
              'freifeld33'=>$value['freifeld33'],
              'freifeld34'=>$value['freifeld34'],
              'freifeld35'=>$value['freifeld35'],
              'freifeld36'=>$value['freifeld36'],
              'freifeld37'=>$value['freifeld37'],
              'freifeld38'=>$value['freifeld38'],
              'freifeld39'=>$value['freifeld39'],
              'freifeld40'=>$value['freifeld40'],
              "name"=>ltrim($value['bezeichnung']),
              'steuertext'=>$value['steuertext'],
              "rabatt"=>$value['rabatt']));
      }
      $netto_gesamt = $value['menge']*($value['preis']-($value['preis']/100*$value['rabatt']));
      $summe = $summe + $netto_gesamt;
      if(!isset($summen[$value['steuersatz']]))$summen[$value['steuersatz']] = 0;
      $summen[$value['steuersatz']] += ($netto_gesamt/100)*$value['steuersatz'];
      $gesamtsteuern +=($netto_gesamt/100)*$value['steuersatz'];

      /*
      if($value['umsatzsteuer']=="" || $value['umsatzsteuer']=="normal")
      {
        $summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal(false,$id,"proformarechnung"));
      }
      else {
        $summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt(false,$id,"proformarechnung"));
      }*/


    }
    /*
       $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM proformarechnung_position WHERE proformarechnung='$id'");
       $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM proformarechnung_position WHERE proformarechnung='$id' AND (umsatzsteuer!='ermaessigt')")/100 * 19;
       $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM proformarechnung_position WHERE proformarechnung='$id' AND umsatzsteuer='ermaessigt'")/100 * 7;
     */     
    if($this->app->erp->ProformarechnungMitUmsatzeuer($id))
    {
      //$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
      $this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $gesamtsteuern,"summen"=>$summen,"totalTaxV"=>0,"totalTaxR"=>0));
    } else
    {
      $this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));
    }

    /* Dateiname */
    $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    $tmp_name = str_replace('.','',$tmp_name);

    $this->filename = $datum."_PRE".$belegnr.".pdf";

    $this->setBarcode($belegnr);
  }


}
