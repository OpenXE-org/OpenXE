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

class ProjektPDF extends Briefpapier {
  public $doctype;
  
  function __construct($app)
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="angebot";
    $this->doctypeOrig="Projekt";
    parent::__construct($this->app);
  } 

  function GetProjekt($id)
  {
    $data = $this->app->DB->SelectArr("SELECT kunde AS adresse, beschreibung, abkuerzung AS abk, name AS projektname FROM projekt WHERE id='$id' LIMIT 1");
    $data = reset($data);
    extract($data,EXTR_OVERWRITE);


      $this->setRecipientDB($adresse);
//      $this->setRecipientLieferadresse($id,"angebot");
            
			$this->nichtsichtbar_zeileabsender = true;
    	$this->nichtsichtbar_footer = true;
    	$this->nichtsichtbar_rechtsoben = true;

      $this->logofile = "";
      $this->briefpapier="";

//  	$zahlungsweise = ucfirst($zahlungsweise);	

			//$zahlungstext = "\nZahlungsweise: $zahlungsweise ";
/*
			$zahlungstext = "\n$zahlungsweise ";
			if($zahlungszieltage >0) $zahlungstext .= "zahlbar innerhalb $zahlungszieltage Tagen.";
			else
				$zahlungstext .= "zahlbar sofort.";

			if($zahlungszielskonto>0) $zahlungstext .= "\nSkonto $zahlungszielskonto% innerhalb $zahlungszieltageskonto Tagen";	
*/
      if($belegnr<=0) $belegnr = "- Entwurf";

      $this->doctypeOrig="Projekt $belegnr";

      if($angebot=="") $angebot = "-";
      if($kundennummer=="") $kundennummer= "-";

//      $this->setCorrDetails(array("Anfrage"=>$anfrage,"Ihre Kunden-Nr."=>$kundennummer,"Bestelldatum"=>$datum,"Vertrieb"=>$vertrieb));
//      if(!$this->app->erp->ProjektMitUmsatzeuer($id) && $ustid!=""  && $keinsteuersatz!="1")
      {
        //$steuer = "\nSteuerfreie innergemeinschaftliche Lieferung. Ihre USt-IdNr. $ustid Land: $land";
//        $steuer = "\nSteuerfrei nach § 4 Nr. 1b i.V.m. § 6 a UStG. Ihre USt-IdNr. $ustid Land: $land";
//        $steuer = $this->app->erp->Firmendaten("eu_lieferung_vermerk");
        $steuer = str_replace('{USTID}',$ustid,$steuer);
        $steuer = str_replace('{LAND}',$land,$steuer);
      }

			$freitext = $beschreibung;

      $this->setTextDetails(array(
	  		"body"=>$projektname." ".$this->app->erp->Firmendaten("projekt_header"),
	  		"footer"=>"$freitext\n\n".$this->app->erp->Firmendaten("projekt_footer")));
      
      $artikel = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket WHERE projekt='$id' AND art!='meilenstein' ORDER By id");
//      if(!$this->app->erp->ProjektMitUmsatzeuer($id)) $this->ust_befreit=true;

      $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM angebot_position WHERE angebot='$id'");
			if($summe_rabatt > 0) $this->rabatt=1;

			$summe = 0;
      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM angebot_position WHERE angebot='$id' LIMIT 1");
      foreach($artikel as $key=>$value)
      {
        if($value['umsatzsteuer'] != "ermaessigt" && $value['umsatzsteuer'] != "befreit") $value['umsatzsteuer'] = "normal";

				if($value['kosten_geplant']>0) 
				{ 
					$value['preis']=$value['kosten_geplant']; 
					$value['menge']=1;
				}
				else {
					$value['menge']=$value['zeit_geplant'];
					$value['preis']=65;
				}

   			$this->addItem(array('currency'=>$value[waehrung],
					'amount'=>$value['menge'],
					'price'=>$value['preis'],
					'tax'=>$value['umsatzsteuer'],
					'itemno'=>$value['nummer'],
					'desc'=>$value['beschreibung'],
	    		"name"=>strtoupper($value['art']).": ".ltrim($value['aufgabe']),
					"rabatt"=>$value['rabatt']));

					$netto_gesamt = $value['menge']*($value['preis']-($value['preis']/100*$value['rabatt']));
					$summe = $summe + $netto_gesamt;

					if($value['umsatzsteuer']=="" || $value['umsatzsteuer']=="normal")
					{
						$summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal());
					}
					elseif($value['umsatzsteuer'] != "befreit") {
						$summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt());
					}
					unset($value);
      }
		/*	
      $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM angebot_position WHERE angebot='$id'");
			// voller steuersatz
      $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM angebot_position WHERE angebot='$id' AND (umsatzsteuer='normal' or umsatzsteuer='')")/100 * 19;
			// reduzierter steuersatz
      $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM angebot_position WHERE angebot='$id' AND umsatzsteuer='ermaessigt'")/100 * 7;
     */ 

//      if($this->app->erp->ProjektMitUmsatzeuer($id))
				$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));

      /* Dateiname */
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM angebot WHERE id='$id' LIMIT 1");
      $belegnr= $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
      //$tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
      //$tmp_name = str_replace('.','',$tmp_name);

      $this->filename = $abk."_PR".$belegnr.".pdf";
      $this->setBarcode($belegnr);
  }


}
