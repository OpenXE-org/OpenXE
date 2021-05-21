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
class ProvisionsgutschriftPDF extends Briefpapier {
  public $doctype;
  
  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="provisionsgutschrift";
    $this->doctypeOrig="Provisionsgutschrift";
    parent::__construct($this->app,$projekt);
  } 


  function GetProvisionsgutschrift($id)
  {
    $this->table="provisionsgutschrift";
    $this->id=$id;
    $adresse = $this->app->DB->Select("SELECT adresse FROM mlm_abrechnung_adresse WHERE id='$id' LIMIT 1");
    $abrechnungtmp = $this->app->DB->SelectArr("SELECT * FROM mlm_abrechnung_adresse WHERE id='$id' LIMIT 1");
    $abrechnungtmp = $abrechnungtmp[0];

    $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($abrechnungtmp['rechnung_name']);
    $this->recipient['city'] = $this->app->erp->ReadyForPDF($abrechnungtmp['rechnung_ort']);
    $this->recipient['areacode'] = $this->app->erp->ReadyForPDF($abrechnungtmp['rechnung_plz']);
    $this->recipient['address1'] = $this->app->erp->ReadyForPDF($abrechnungtmp['rechnung_strasse']);
    $this->recipient['country'] = $this->app->erp->ReadyForPDF($abrechnungtmp['rechnung_land']);

    $abrechnung = $abrechnungtmp['abrechnung'];
    $steuernummer = $abrechnungtmp['steuernummer'];
    $neueposition = $abrechnungtmp['neueposition'];
    $mlmabrechnung = $abrechnungtmp['mlmabrechnung'];

    $belegnr=$abrechnungtmp['belegnr'];

    $datum_abrechnung = $this->app->DB->Select("SELECT CONCAT(DATE_FORMAT(von,'%d.%m.%Y'),' - ',DATE_FORMAT(bis,'%d.%m.%Y')) FROM mlm_abrechnung WHERE id='$abrechnung' LIMIT 1");
    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM mlm_abrechnung WHERE id='$abrechnung' LIMIT 1");

      // OfferNo, customerId, OfferDate

    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    $iban = $this->app->DB->Select("SELECT iban FROM adresse WHERE id='$adresse' LIMIT 1");
    $bic = $this->app->DB->Select("SELECT swift FROM adresse WHERE id='$adresse' LIMIT 1");


    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }

      $zahlungsweise = strtolower($zahlungsweise);
     
      if($belegnr<=0) $belegnr = "- Entwurf";


			if($stornorechnung)
      	$this->doctypeOrig="Stornorechnung $belegnr";
			else
      	$this->doctypeOrig="Provisionsgutschrift $belegnr";

      if($provisionsgutschrift=="") $provisionsgutschrift = "-";
      if($kundennummer=="") $kundennummer= "-";

      if($auftrag=="0") $auftrag = "-";
      if($lieferschein=="0") $lieferschein= "-";

	//$this->setCorrDetails(array("Auftrag"=>$auftrag,"Datum"=>$datum,"Ihre Kunden-Nr."=>$kundennummer,"Lieferschein"=>$lieferschein,"Buchhaltung"=>$buchhaltung));
	if($rechnung >0)
		$this->setCorrDetails(array("Rechnung"=>$rechnung,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,"Datum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer));
	else
		$this->setCorrDetails(array("Datum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer));



/*
      if(!$this->app->erp->ProvisionsgutschriftMitUmsatzeuer($id) && $keinsteuersatz!="1")
			{
       if($this->app->erp->Export($land))
          $steuer = $this->app->erp->Firmendaten("export_lieferung_vermerk");
        else
          $steuer = $this->app->erp->Firmendaten("eu_lieferung_vermerk");
        $steuer = str_replace('{USTID}',$ustid,$steuer);
        $steuer = str_replace('{LAND}',$land,$steuer);
			}
*/
			$provisionsgutschrift_header=$this->app->erp->Firmendaten("provisionsgutschrift_header");
      $provisionsgutschrift_header = $this->app->erp->ParseUserVars("provisionsgutschrift",$id,$provisionsgutschrift_header);

		  if($stornorechnung)
			{
			  $provisionsgutschrift_header = str_replace('{ART}',"Stornorechnung",$provisionsgutschrift_header);
     	} else {
			  $provisionsgutschrift_header = str_replace('{ART}',"Provisionsgutschrift",$provisionsgutschrift_header);
			} 

      $freitext .= "Aktuelle Stufe: $neueposition\r\n\r\n";

      if($steuernummer!="")
        $freitext .= "Ihre Steuernummer: $steuernummer\r\n";


      if($mlmabrechnung=='' || $mlmabrechnung=='sammelueberweisung')
        $freitext .= "Der Betrag wird Ihnen auf Ihr Konto (IBAN: $iban BIC: $bic) überwiesen.";
      else
        $freitext .= "Manuelle Auszahlung:";

      //$freitext .= "Der Betrag wird Ihnen auf Ihr Konto (IBAN: $iban) überwiesen.";

     	$this->setTextDetails(array(
  					"body"=>$provisionsgutschrift_header,
  					"footer"=>"$freitext"."\r\n".$this->app->erp->ParseUserVars("provisionsgutschrift",$id,$this->app->erp->Firmendaten("provisionsgutschrift_footer"))."\r\n$zahlungsweisetext\r\n$steuer"));

      $value = $this->app->DB->SelectArr("SELECT * FROM mlm_abrechnung_adresse WHERE id='$id' LIMIT 1");
      $value = $value[0];

      $this->addItem(array('currency'=>$value['waehrung'],
          'amount'=>1,
          'price'=>$value['betrag_netto'],
          'tax'=>"normal",
          'itemno'=>$value['nummer'],
          'unit'=>$value['einheit'],
          'desc'=>"Abrechnung $datum_abrechnung\r\n(Details siehe gesonderte Aufstellung)",
          "name"=>"Provisionsabrechnung"));

      $mitsteuer = $this->app->DB->Select("SELECT mitsteuer FROM mlm_abrechnung_adresse WHERE id='$id' LIMIT 1");

      $summe = $value['betrag_netto'];
      $summeV = ($value['betrag_netto'] * (100+$value['steuersatz'])/100)-$value['betrag_netto'];
 
      if($mitsteuer)
      {
        $this->ust_befreit=false;
				$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
      } else
      {
        $this->ust_befreit=false;
      	$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));
      }

			if($stornorechnung)
      	$this->filename = $datum."_STORNO_".$belegnr.".pdf";
			else
      	$this->filename = str_replace(' ','',$kundennummer."_".$belegnr."_Provisionsgutschrift.pdf");
      $this->setBarcode($belegnr);
  }
}
