<?php
/*
 * SPDX-FileCopyrightText: 2024 OpenXE-org
 * SPDX-FileCopyrightText: 2019 Xentral (c) Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg, Germany
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */
?>
<?php
if(!class_exists('BriefpapierCustom'))
{
  class BriefpapierCustom extends Briefpapier
  {
    
  }
}

class VersandpaketscheinPDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="", $styleData=null)
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="versandpaketschein";
    $this->doctypeOrig="Versandpaket";
    parent::__construct($this->app,$projekt,$styleData);
  }

  function GetVersandpaketschein($id,$info="",$extrafreitext="")
  {
    $this->doctypeid = $id;
    $this->id = $id;
    $this->table = 'versandpakete'; // Alles doppelt und dreifach... $#%#!
    $this->parameter = $info;
    $this->nichtsichtbar_summe = true;
  
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    $lvl = null;

    $sql = "SELECT DISTINCT
                        lieferschein
                    FROM
                        versandpaket_lieferschein_position vlp
                    INNER JOIN lieferschein_position lp ON
                        vlp.lieferschein_position = lp.id
                    LIMIT 1                    
    ";
    $lieferschein = $this->app->DB->Select($sql);

    $this->setRecipientLieferadresse($lieferschein,"lieferschein");

    $sql = "
        SELECT
            vp.datum,
            vp.id versandpaketnummer,
            vp.gewicht,
            vp.tracking,
            vp.tracking_link,
            vp.versandart
        FROM
            versandpakete vp
        WHERE vp.id = ".$id."
    ";

    $data = $this->app->DB->SelectRow($sql);

/*
    $data = $this->app->DB->SelectRow("
        SELECT 
            k.kommentar,
            k.bezeichnung,
            k.bearbeiter,
            DATE_FORMAT(k.zeitstempel,'%Y%m%d') as datum,
            k.ausgelagert,
            l.belegnr as lieferscheinnummer,
            ab.belegnr as auftragnummer,
            DATE_FORMAT(ab.tatsaechlicheslieferdatum,'%d.%m.%Y') as tatsaechlicheslieferdatum,
            a.name            
        FROM
            kommissionierung k
        LEFT JOIN 
            lieferschein l
        ON
            k.lieferschein = l.id
        LEFT JOIN
            auftrag ab
        ON
            l.auftragid = ab.id OR k.auftrag = ab.id
        LEFT JOIN 
            adresse a 
        ON 
            a.id = k.adresse
        WHERE k.id='$id'
    ");*/

    $this->zusatzfooter = " (PS$id)";

    $this->doctypeOrig = $this->app->erp->Beschriftung("dokument_versandpaketschein") . " $id";

    $body = $this->app->erp->Beschriftung("Versandpaketschein_header");
    $body = $this->app->erp->ParseUserVars("Versandpaketschein",$id,$body);

    if($this->app->erp->Firmendaten("footer_reihenfolge_Versandpaketschein_aktivieren")=="1")      {
      $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_Versandpaketschein");
      if($footervorlage=='') {
        $footervorlage = "{FOOTERVERSANDINFO}{FOOTERFREITEXT}{FOOTEREXTRAFREITEXT}\r\n{FOOTERTEXTVORLAGEVersandpaketschein}";
      }
      $footervorlage = str_replace('{FOOTEREXTRAFREITEXT}',$extrafreitext,$footervorlage);
      $footervorlage = str_replace('{FOOTERTEXTVORLAGEVersandpaketschein}',$this->app->erp->Beschriftung("Versandpaketschein_footer"),$footervorlage);
      $footervorlage  = $this->app->erp->ParseUserVars("Versandpaketschein",$id,$footervorlage);
      $footer = $footervorlage;
    }
    else {
      $footer = $versandinfo."$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("Versandpaketschein",$id,$this->app->erp->Beschriftung("Versandpaketschein_footer"));
    }
   
/*    $artikel = $this->app->DB->SelectArr(
        sprintf(
            "SELECT 
                ks.id,
                a.nummer as itemno,
                lp.kurzbezeichnung as `desc`,
                ".$this->app->erp->FormatMengeFuerFormular("ksp.menge")." as amount,
                a.gewicht,
                a.herstellernummer as `name`,
                '' as steuersatz_ermaessigt,
                DATE_FORMAT(zeitstempel,'%%Y%%m%%d') as datum               
            FROM 
                kommissionierung ks
            INNER JOIN kommissionierung_position ksp ON ks.id = ksp.kommissionierung
            INNER JOIN artikel a ON a.id = ksp.artikel
            INNER JOIN lager_platz lp ON lp.id = ksp.lager_platz
            WHERE ks.id = %d", 
            $id
        )
    );*/

    $artikel = $this->app->DB->SelectArr("
         SELECT
            l.auftrag,
            auf.ihrebestellnummer,
            vp.id,
            lp.nummer,
            lp.bezeichnung bezeichnung,
            TRIM(vlp.menge)+0 menge,
            TRIM(lp.menge)+0 lieferscheinmenge,
            a.gewicht,
            lp.artikelnummerkunde,            
            '' as datum
        FROM
            versandpakete vp
        INNER JOIN
            versandpaket_lieferschein_position vlp ON vlp.versandpaket = vp.id
        INNER JOIN
            lieferschein_position lp ON vlp.lieferschein_position = lp.id
        INNER JOIN
            lieferschein l ON lp.lieferschein = l.id
        INNER JOIN
            artikel a ON lp.artikel = a.id
        LEFT JOIN
            auftrag auf ON l.auftragid = auf.id
        WHERE vp.id = ".$id
    );

    foreach($artikel as $key=>$value) {
        $this->addItem(array(
              'belegposition'=>$value['id'],
              'amount'=> ($value['menge'] != $value['lieferscheinmenge'])?($value['menge']." / ".$value['lieferscheinmenge']):$value['menge'],
              'gewicht'=>$value['gewicht'],
              'lvl'=>$value['lvl'],
              'itemno'=>$value['nummer'],
              'pos_id'=>$value['id'],
              'artikel'=>$value['artikel'],
              'auftrag' => $value['auftrag'],
              'ihrebestellnummer' =>  $value['ihrebestellnummer'],
              'desc'=>ltrim($value['beschreibung']).(strpos($value['beschreibung'], str_replace(' ', '', $value['lagertext'])) !== false?'':($value['beschreibung']!=""?"\r\n":'').$value['lagertext']),
              'unit'=>$value['einheit'],
              'hersteller'=>$value['hersteller'],
              'artikelnummerkunde'=>$value['artikelnummerkunde'],
              'lieferdatum'=>$value['lieferdatum'],
              'lieferdatumkw'=>$value['lieferdatumkw'],
              'zolltarifnummer'=>$value['zolltarifnummer'],
              'herkunftsland'=>$value['herkunftsland'],
              'herstellernummer'=>trim($value['herstellernummer']),
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
              "name"=>$value['bezeichnung']));
    }
    
    $this->filename = $data['datum']."_PS".$id.".pdf";
    $this->setBarcode($id);

    $corrDetails = array();

    if (!empty($data['versandpaketnummer'])) {
        $corrDetails['Paketnummer'] = $data['versandpaketnummer'];
    }   
    if (!empty($data['gewicht'])) {
        $corrDetails['Gewicht'] = $data['gewicht'];
    }
    if (!empty($data['versandart'])) {
        $corrDetails['Versandart'] = $data['versandart'];
    }
    if (!empty($data['tracking'])) {
        $corrDetails['Tracking'] = $data['tracking'];
    }       
    if (!empty($data['gewicht'])) {
        $corrDetails['Gewicht'] = $data['gewicht'];
    }

    $this->setCorrDetails($corrDetails, true);
  }
}
