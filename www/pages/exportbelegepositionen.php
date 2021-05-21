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

class Exportbelegepositionen
{
  /** @var Application $app */
  var $app;
  var $belegnummer;
  var $headerwritten = false;

  /**
   * Exportbelegepositionen constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    if ($intern == true) {
      return;
    }
    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("export", "ExportbelegepositonenExport");
    $this->app->ActionHandlerListen($app);
    $this->app->erp->Headlines('Belegepositionen Export');
  }


  private function getCSVData($typ, $projekt, $von, $bis)
  {
    $csvdata = '';
    if ($typ == "auftrag") {
      $ihrebestellnummer = "r.ihrebestellnummer as beleg_ihrebestellnummer,";
      $lieferdatum = "r.datum as beleg_lieferdatum, r.lieferdatum as beleg_tatsaechlicheslieferdatum,";
      $aktion = "r.aktion as beleg_aktion,";
      $preis = "Replace(format(rp.preis,2),'.',',')  as artikel_preis,";
      $waehrung = "rp.waehrung  as artikel_waehrung,";
      $umsatzsteuer = "if(ifnull(rp.umsatzsteuer,'') = '', art.umsatzsteuer,rp.umsatzsteuer)  as artikel_umsatzsteuer,";
      $rabatt = "replace(ifnull(rp.rabatt,0),'.',',')  as artikel_rabatt,";
    }
    elseif ($typ == "rechnung") {
      $lieferdatum = "r.datum as beleg_lieferdatum, r.lieferdatum as beleg_tatsaechlicheslieferdatum,";
      $ihrebestellnummer = "r.ihrebestellnummer as beleg_ihrebestellnummer,";
      $aktion = "r.aktion as beleg_aktion,";
      $preis = "rp.preis  as artikel_preis,";
      $waehrung = "rp.waehrung  as artikel_waehrung,";
      $umsatzsteuer = "if(ifnull(rp.umsatzsteuer,'') = '', art.umsatzsteuer,rp.umsatzsteuer)  as artikel_umsatzsteuer,";
      $rabatt = "replace(ifnull(rp.rabatt,0),'.',',')  as artikel_rabatt,";
    }
    elseif ($typ == "angebot") {
      $lieferdatum = "r.datum as beleg_lieferdatum, r.lieferdatum as beleg_tatsaechlicheslieferdatum,";
      $ihrebestellnummer = "'' as beleg_ihrebestellnummer,";
      $aktion = "r.aktion as beleg_aktion,";
      $preis = "rp.preis  as artikel_preis,";
      $waehrung = "rp.waehrung  as artikel_waehrung,";
      $umsatzsteuer = "if(ifnull(rp.umsatzsteuer,'') = '', art.umsatzsteuer,rp.umsatzsteuer)  as artikel_umsatzsteuer,";
      $rabatt = "replace(ifnull(rp.rabatt,0),'.',',')  as artikel_rabatt,";
    }
    elseif ($typ == "gutschrift") {
      $lieferdatum = "r.datum as beleg_lieferdatum, r.lieferdatum as beleg_tatsaechlicheslieferdatum,";
      $ihrebestellnummer = "r.ihrebestellnummer as beleg_ihrebestellnummer,";
      $aktion = "r.aktion as beleg_aktion,";
      $preis = "rp.preis  as artikel_preis,";
      $waehrung = "rp.waehrung  as artikel_waehrung,";
      $umsatzsteuer = "if(ifnull(rp.umsatzsteuer,'') = '', art.umsatzsteuer,rp.umsatzsteuer) as artikel_umsatzsteuer,";
      $rabatt = "replace(ifnull(rp.rabatt,0),'.',',')  as artikel_rabatt,";
    }
    elseif ($typ == "lieferschein") {
      $lieferdatum = "r.datum as beleg_lieferdatum,";
      $ihrebestellnummer = "r.ihrebestellnummer as beleg_ihrebestellnummer,";
      $aktion = "'' as beleg_aktion,";
      $preis = "'' as artikel_preis,";
      $waehrung = "'' as artikel_waehrung,";
      $umsatzsteuer = "'' as artikel_umsatzsteuer,";
      $rabatt = "''  as artikel_rabatt,";
    }
    if(in_array($typ, array('auftrag','rechnung','gutschrift','bestellung')))
    {
      $steuersatz = " replace(ifnull(rp.steuersatz,-1),'.',',') as artikel_steuersatz ,";
    }else{
      $steuersatz = " -1 as artikel_steuersatz ,";
    }
    if($typ == 'auftrag')
    {
      $art = "r.art as beleg_art";
    }else{
      $art = "'' as beleg_art";
    }
    $and = false;
    if ($projekt || $von || $bis) {
      $where = "WHERE ";

      if ($projekt) {
        $where .= "r.projekt='$projekt'";
        $and = true;
      }
      if ($von) {
        if ($and) {
          $where .= " AND ";
        }
        $where .= "r.datum >= '$von' ";
        $and = true;
      }
      if ($bis) {
        if ($and) {
          $where .= " AND ";
        }
        $where .= "r.datum <= '$bis' ";
      }
      $where = rtrim($where, " AND");
    }
    $sql = "SELECT 
                '$typ' as art,
                r.belegnr as beleg_belegnr, 
                $lieferdatum
                r.versandart as beleg_versandart, 
                r.status as beleg_status, 
                r.belegnr as beleg_hauptbelegnr, 
                if(r.kundennummer != '',r.kundennummer,(SELECT kundennummer FROM adresse WHERE id=r.adresse LIMIT 0,1)) as beleg_kundennummer, 
                r.name as beleg_name,
                r.abteilung as beleg_abteilung,
                r.unterabteilung as beleg_unterabteilung,
                r.adresszusatz as beleg_adresszusatz,
                r.ansprechpartner as beleg_ansprechpartner,
                r.telefon as beleg_telefon,
                r.email as beleg_email,
                r.land as beleg_land,
                r.strasse as beleg_strasse,
                r.plz as beleg_plz,
                r.ort as beleg_ort,
                pr.abkuerzung as beleg_projekt,
                $ihrebestellnummer
                $aktion
                r.internebemerkung as beleg_internebemerkung,
                r.internebezeichnung as beleg_internebezeichnung,
                r.freitext as beleg_freitext,
                r.lieferbedingung as beleg_lieferbedingung,
                ".$art.",                
                rp.nummer as artikel_nummer,
                rp.bezeichnung  as artikel_bezeichnung,
                rp.beschreibung  as artikel_beschreibung,
                Replace(format(rp.menge,2),'.',',')  as artikel_menge,
                $preis
                '1'  as artikel_preisfuermenge,
                $waehrung
                rp.lieferdatum  as artikel_lieferdatum,
                rp.sort  as artikel_sort,
                $umsatzsteuer
                rp.einheit as artikel_einheit,
                $rabatt
                $steuersatz
                rp.zolltarifnummer  as artikel_zolltarifnummer,
                rp.herkunftsland  as artikel_herkunftsland,
                rp.artikelnummerkunde  as artikel_artikelnummerkunde,
                rp.freifeld1  as artikel_freifeld1,
                rp.freifeld2  as artikel_freifeld2,
                rp.freifeld3  as artikel_freifeld3,
                rp.freifeld4  as artikel_freifeld4,
                rp.freifeld5  as artikel_freifeld5,
                rp.freifeld6  as artikel_freifeld6,
                rp.freifeld7  as artikel_freifeld7,
                rp.freifeld8  as artikel_freifeld8,
                rp.freifeld9  as artikel_freifeld9,
                rp.freifeld10  as artikel_freifeld10
             FROM 
                $typ r 
             INNER JOIN {$typ}_position rp on rp.$typ = r.id 
             LEFT JOIN artikel art ON rp.artikel = art.id
             LEFT JOIN projekt pr ON r.projekt = pr.id
             $where
             ORDER BY r.belegnr,r.id, rp.sort
             ";
    $arr = $this->app->DB->Query($sql);
    while ($row = $this->app->DB->Fetch_Assoc($arr)) {
      if(!$this->headerwritten)
      {
        foreach ($row as $key => $value) {
          $csvdata .= html_entity_decode($key) . ';';
        }
        $csvdata .= "\r\n";
        $this->headerwritten = true;
      }
      $colcounter = 0;
      foreach ($row as $key => $value) {
        if ($key == 'beleg_belegnr') {
          if ($this->belegnummer == $value) {
            $value = 'PARENT';
          } else {
            $this->belegnummer = $value;
          }
        }
        if(is_null($value))
        {
          $csvdata .= '"";';
        }else{
          $csvdata .= '"' . html_entity_decode($value) . '";';
        }
      }
      $csvdata .= "\r\n";
    }
    return $csvdata;
  }


  function ExportbelegepositonenExport()
  {
    if ($this->app->Secure->GetPOST("download") == 'Download') {
      $projektkuerzel = $this->app->Secure->GetPOST("projekt");
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projektkuerzel' LIMIT 0,1");
      $von = $this->app->Secure->GetPOST("von");
      $bis = $this->app->Secure->GetPOST("bis");
      if ($von != '' && strpos($von, '.') !== false) {
        $von = $this->app->String->Convert($von, "%3.%2.%1", "%1-%2-%3");
      }
      if ($bis != '' && strpos($bis, '.') !== false) {
        $bis = $this->app->String->Convert($bis, "%3.%2.%1", "%1-%2-%3");
      }
      $typ = '';
      if (
          (!$this->app->Secure->GetPOST("auftrag")) &&
          (!$this->app->Secure->GetPOST("rechnung")) &&
          (!$this->app->Secure->GetPOST("angebot")) &&
          (!$this->app->Secure->GetPOST("gutschrift")) &&
          (!$this->app->Secure->GetPOST("lieferschein"))
      ) {
        $msg = $this->app->erp->base64_url_encode("<div class=error>Sie müssen mindestens eine Belegart auswählen.</div>");
        header("Location: ./index.php?module=exportbelegepositionen&action=export&msg=$msg");
        exit;
      }
      $filename = date('Ymd') . "_Belege_export.csv";
      header("Content-Disposition: attachment; filename=" . $filename);
      header("Pragma: no-cache");
      header("Expires: 0");
      $this->headerwritten = false;
      $csv = "";
      if ($this->app->Secure->GetPOST("auftrag") == "1") {
        $csv .= $this->getCSVData("auftrag", $projekt, $von, $bis);
      }
      if ($this->app->Secure->GetPOST("rechnung") == "1") {
        $csv .= $this->getCSVData("rechnung", $projekt, $von, $bis);
      }
      if ($this->app->Secure->GetPOST("angebot") == "1") {
        $csv .= $this->getCSVData("angebot", $projekt, $von, $bis);
      }
      if ($this->app->Secure->GetPOST("gutschrift") == "1") {
        $csv .= $this->getCSVData("gutschrift", $projekt, $von, $bis);
      }
      if ($this->app->Secure->GetPOST("lieferschein") == "1") {
        $csv .= $this->getCSVData("lieferschein", $projekt, $von, $bis);
      }
      echo $csv;
      $this->app->ExitXentral();
    }
    $this->app->erp->MenuEintrag("index.php?module=exportbelegepositionen&action=export","&Uuml;bersicht");
    $this->app->Tpl->Parse('PAGE', "exportbelegepositionen_export.tpl");
    $this->app->YUI->AutoComplete("projekt", "projektname", 1);
    $this->app->YUI->DatePicker("von");
    $this->app->YUI->DatePicker("bis");
  }
}

