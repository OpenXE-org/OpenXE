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

class LieferantengutschriftPDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="", $styleData=null)
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="lieferantengutschrift";
    $this->doctypeOrig="Lieferantengutschrift";
    parent::__construct($this->app,$projekt,$styleData);
  }

  /**
   * @param array $articleList
   *
   * @return array
   */
  protected function sortAricleExploded($articleList)
  {
    if(empty($articleList)) {
      return $articleList;
    }

    $ret = [];

    $articleIdToKey = [];
    $children = [];
    foreach($articleList as $aricleKey => $article) {
      $articleIdToKey[$article['id']] = $aricleKey;
      if(!empty($article['explodiert_parent_artikel'])) {
        $children[$article['explodiert_parent']][] = $aricleKey;
      }
      elseif(empty($ret)) {
        $ret[] = $article;
        unset($articleList[$aricleKey]);
      }
    }
    if(empty($ret)) {
      $ret[] = reset($articleList);
      $key = array_keys($articleList);
      $key = reset($key);
      unset($articleList[$key]);
    }

    while(!empty($articleList)) {
      $cRet = count($ret);
      for($i = $cRet -1; $i >= 0; $i--) {
        $last= $ret[$i];
        if(!empty($children[$last['id']])) {
          $child = reset($children[$last['id']]);
          $childKey = array_keys($children[$last['id']]);
          $childKey = reset($childKey);
          $ret[] = $articleList[$child];
          unset($articleList[$child]);
          unset($children[$last['id']][$childKey]);
          break;
        }
      }

      if($cRet === count($ret)) {
        $ret[] = reset($articleList);
        $key = array_keys($articleList);
        $key = reset($key);
        unset($articleList[$key]);
      }
    }

    return $ret;
  }


  function GetLieferantengutschrift($id,$info="",$extrafreitext="")
  {
    $this->doctypeid = $id;
    $this->id = $id;
    $this->table = 'lieferantengutschrift'; // Alles doppelt und dreifach... $#%#!
    $this->parameter = $info;
//    $this->nichtsichtbar_summe = true;
//    $this->nichtsichtbar_box = true;
//    $this->nichtsichtbar_empfaenger = true;
//    $this->nichtsichtbar_zeileabsender = true;
//    $this->nichtsichtbar_footer = true;
    
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    $lvl = null;

    $data = $this->app->DB->SelectRow("
        SELECT 
            ".$this->app->erp->FormatDate('k.datum', 'datum').",
            k.belegnr,
            k.rechnung as lieferantengutschriftnummer,
            k.belastungsanzeige,
            a.name,
            a.lieferantennummer,
            a.id adresse,
            a.sprache,
            DATE_FORMAT(k.datum,'%Y%m%d') as datum2
        FROM
            lieferantengutschrift k
        INNER JOIN 
            adresse a 
        ON 
            a.id = k.adresse
        WHERE k.id='$id'
    ");

    $this->setRecipientLieferadresse($data['adresse'], 'adresse');
    $this->table = 'lieferantengutschrift'; // Reset after setRecipientLieferadresse-hack

    $this->app->erp->BeschriftungSprache($data['sprache']);
    if($waehrung) {
      $this->waehrung = $waehrung;
    }
    $this->sprache = $sprache;

    if ($data['belastungsanzeige']) {
        $doctypeOrig = 'dokument_lieferantengutschrift_belastungsanzeige';
    } else {
        $doctypeOrig = 'dokument_lieferantengutschrift';
    }
    $this->doctypeOrig = $this->app->erp->Beschriftung($doctypeOrig)." ".$data['belegnr'];

    $this->zusatzfooter = " (LG".$data['belegnr'].")";

    $body = $this->app->erp->Beschriftung("lieferantengutschrift_header");
    $body = $this->app->erp->ParseUserVars("lieferantengutschrift",$id,$body);

    if($this->app->erp->Firmendaten("footer_reihenfolge_lieferantengutschrift_aktivieren")=="1")      {
      $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_lieferantengutschrift");
      if($footervorlage=='') {
        $footervorlage = "{FOOTERVERSANDINFO}{FOOTERFREITEXT}{FOOTEREXTRAFREITEXT}\r\n{FOOTERTEXTVORLAGELIEFERANTENGUTSCHRIFT}";
      }
      $footervorlage = str_replace('{FOOTERVERSANDINFO}',$versandinfo,$footervorlage);
      $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
      $footervorlage = str_replace('{FOOTEREXTRAFREITEXT}',$extrafreitext,$footervorlage);
      $footervorlage = str_replace('{FOOTERTEXTVORLAGELIEFERANTENGUTSCHRIFT}',$this->app->erp->Beschriftung("lieferantengutschrift_footer"),$footervorlage);
      $footervorlage  = $this->app->erp->ParseUserVars("lieferantengutschrift",$id,$footervorlage);
      $footer = $footervorlage;
    }
    else {
      $footer = $versandinfo."$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("lieferantengutschrift",$id,$this->app->erp->Beschriftung("lieferantengutschrift_footer"));
    }

    $this->setTextDetails(
      array(
        'body'  => $body,
        'footer'=> $footer
      )
    );

    $artikel = $this->app->DB->SelectArr(
        sprintf(
            "SELECT 
                ks.id,
                a.nummer as itemno,
                ks.beschreibung as `desc`,
                ksp.menge,
                ".$this->app->erp->FormatMengeFuerFormular("ksp.menge")." as amount,
                preis as preis,
                preis as price,
                a.name_de AS name,
                a.herstellernummer,
                ksp.steuersatz,
                datum
            FROM 
                lieferantengutschrift ks
            INNER JOIN lieferantengutschrift_position ksp ON ks.id = ksp.lieferantengutschrift
            INNER JOIN artikel a ON a.id = ksp.artikel
            WHERE ks.id = %d", 
            $id
        )
    );

    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferantengutschrift WHERE id = '$id' LIMIT 1");
    $positionenkaufmaenischrunden = $this->app->erp->Projektdaten($projekt,"preisberechnung");

    foreach($artikel as $key => $value) {
        if (!is_numeric($value['preis'])) {
            $value['preis'] = 0;
        }       
        $this->addItem($value);

        if ($positionenkaufmaenischrunden == 3) {
            $netto_gesamt = $value['menge'] * round($value['preis'] - ($value['preis'] / 100 * $value['rabatt']), 2);
        } else {
            $netto_gesamt = $value['menge'] * ($value['preis'] - ($value['preis'] / 100 * $value['rabatt']));
        }
        if ($positionenkaufmaenischrunden) {
            $netto_gesamt = round($netto_gesamt, 2);
        }
        $summe = $summe + $netto_gesamt;
        if (!isset($summen[$value['steuersatz']])) {
            $summen[$value['steuersatz']] = 0;
        }
        $summen[$value['steuersatz']] += ($netto_gesamt / 100) * $value['steuersatz'];
        $gesamtsteuern += ($netto_gesamt / 100) * $value['steuersatz'];
    }

    if ($positionenkaufmaenischrunden && isset($summen) && is_array($summen)) {
        $gesamtsteuern = 0;
        foreach($summen as $k => $v) {
            $summen[$k] = round($v, 2);
            $gesamtsteuern += round($v, 2);
        }
    }
    if ($positionenkaufmaenischrunden) {
        list($summe, $gesamtsumme, $summen) = $this->app->erp->steuerAusBelegPDF($this->table, $this->id);
        $gesamtsteuern = $gesamtsumme - $summe;
    }
    $this->setTotals(
        [
            'totalArticles' => $summe,
            'total' => ($gesamtsumme != 0 ? $gesamtsumme : ($summe + $gesamtsteuern)),
            'summen' => $summen,
            'totalTaxV' => 0,
            'totalTaxR' => 0
        ]
    );

    $this->filename = $data['datum2']."_LG".$data['belegnr'].".pdf";
    $this->setBarcode($id);

    $corrDetails = array();

    $corrDetails[$this->app->erp->Beschriftung($doctypeOrig)] = $data['belegnr'];
    $corrDetails[$this->app->erp->Beschriftung("dokument_lieferantengutschrift_nummer")] = $data['lieferantengutschriftnummer'];
    $corrDetails[$this->app->erp->Beschriftung("dokument_datum")] = $data['datum'];
    $corrDetails[$this->app->erp->Beschriftung("dokument_lieferantennummer")] = $data['lieferantennummer'];

    $this->setCorrDetails($corrDetails, true);
  }
}
