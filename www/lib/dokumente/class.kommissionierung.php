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

class KommissionierungPDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="", $styleData=null)
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="kommissionierung";
    $this->doctypeOrig="Kommissionierung";
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


  function GetKommissionierung($id,$info="",$extrafreitext="")
  {
    $this->doctypeid = $id;
    $this->id = $id;
    $this->table = 'kommissionierung'; // Alles doppelt und dreifach... $#%#!
    $this->parameter = $info;
    $this->nichtsichtbar_summe = true;
//    $this->nichtsichtbar_box = true;
    $this->nichtsichtbar_empfaenger = true;
    $this->nichtsichtbar_zeileabsender = true;
    $this->nichtsichtbar_footer = true;
  
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    $lvl = null;

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
    ");

    $this->zusatzfooter = " (KS$id)";

    $this->doctypeOrig = $this->app->erp->Beschriftung("dokument_kommissionierschein") . " $id";

    $body = $this->app->erp->Beschriftung("Kommissionierung_header");
    $body = $this->app->erp->ParseUserVars("Kommissionierung",$id,$body);

    if($this->app->erp->Firmendaten("footer_reihenfolge_Kommissionierung_aktivieren")=="1")      {
      $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_Kommissionierung");
      if($footervorlage=='') {
        $footervorlage = "{FOOTERVERSANDINFO}{FOOTERFREITEXT}{FOOTEREXTRAFREITEXT}\r\n{FOOTERTEXTVORLAGEKommissionierung}";
      }
      $footervorlage = str_replace('{FOOTERVERSANDINFO}',$versandinfo,$footervorlage);
      $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
      $footervorlage = str_replace('{FOOTEREXTRAFREITEXT}',$extrafreitext,$footervorlage);
      $footervorlage = str_replace('{FOOTERTEXTVORLAGEKommissionierung}',$this->app->erp->Beschriftung("Kommissionierung_footer"),$footervorlage);
      $footervorlage  = $this->app->erp->ParseUserVars("Kommissionierung",$id,$footervorlage);
      $footer = $footervorlage;
    }
    else {
      $footer = $versandinfo."$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("Kommissionierung",$id,$this->app->erp->Beschriftung("Kommissionierung_footer"));
    }

    $this->setTextDetails(
      array(
        'body'  => $body,
        'footer'=> $footer
      )
    );

    $orderpicking_sort = $this->app->erp->Projektdaten($this->projekt, 'orderpicking_sort');

    $artikel = $this->app->DB->SelectArr(
        sprintf(
            "SELECT 
                ks.id,
                a.nummer as itemno,
                lp.kurzbezeichnung as `desc`,
                ksp.menge as amount,
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
    );

    foreach($artikel as $key=>$value)  {
        $this->addItem($value);
    }

    $this->filename = $data['datum']."_KS".$id.".pdf";
    $this->setBarcode($id);

    $corrDetails = array();

    if (!empty($data['auftragnummer'])) {
        $corrDetails['Auftrag'] = $data['auftragnummer'];
    }   
    if (!empty($data['lieferscheinnummer'])) {
        $corrDetails['Lieferschein'] = $data['lieferscheinnummer'];
    }
    if (!empty($data['name'])) {
        $corrDetails['Adresse'] = $data['name'];
    }       
    if (!empty($data['tatsaechlicheslieferdatum'])) {
        $corrDetails['Liefertermin'] = $data['tatsaechlicheslieferdatum'];
    }
    if (!empty($data['ausgelagert'])) {
        $corrDetails['Ausgelagert'] = "ja";
    } else {
        $corrDetails['Ausgelagert'] = "nein";
    }
    $this->setCorrDetails($corrDetails, true);
  }
}
