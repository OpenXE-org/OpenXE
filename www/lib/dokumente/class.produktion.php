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

class ProduktionPDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="", $styleData=null)
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="produktion";
    $this->doctypeOrig="produktion";
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


  function GetProduktion($id,$info="",$extrafreitext="")
  {
    $this->doctypeid = $id;
    $this->id = $id;
    $this->table = 'produktion'; // Alles doppelt und dreifach... $#%#!
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
            a.name as adressname,
            DATE_FORMAT(p.datum,'%d.%m.%Y') as datum,
            p.belegnr,
            p.freitext,
            p.internebemerkung,
            p.bearbeiter,
            p.internebezeichnung,
            auf.belegnr auftragsnummer,
            projekt.name projekt
        FROM
            produktion p
        LEFT JOIN 
            adresse a 
        ON 
            a.id = p.adresse
        LEFT JOIN
            auftrag auf
        ON
            auf.id = p.auftragid
        LEFT JOIN projekt ON projekt.id = p.projekt
        WHERE p.id='$id'
    ");

    $this->zusatzfooter = " (PD".$data['belegnr'].")";

    $this->doctypeOrig = $this->app->erp->Beschriftung("dokument_produktion") ." ".$data['belegnr'];

    $produktionsartikel = $this->app->DB->SelectRow("
        SELECT
            a.id,
            a.name_de,
            a.nummer,
            a.internerkommentar,
            a.hinweis_einfuegen
        FROM 
            produktion p
        INNER JOIN produktion_position pp ON p.id = pp.produktion
        INNER JOIN artikel a ON a.id = pp.artikel
        WHERE p.id = ".$id." AND pp.stuecklistestufe = 1
    ");

    $bodytexte = Array();
    if (!empty($produktionsartikel['internerkommentar'])) {
        $bodytext['Artikelkommentar'] = $produktionsartikel['internerkommentar'];
    }
    if (!empty($produktionsartikel['hinweis_einfuegen'])) {
        $bodytext['Artikelhinweis'] = $produktionsartikel['hinweis_einfuegen'];
    }
    if (!empty($data['freitext'])) {
        $bodytext['Freitext'] = $data['freitext'];
    }
    if (!empty($data['internebemerkung'])) {
        $bodytext['Interne Bemerkung'] = $data['internebemerkung'];
    }

    $sql = "SELECT
                etiketten.name,
                al.amount
            FROM article_label al
            INNER JOIN artikel a ON a.id = al.article_id
            INNER JOIN etiketten ON etiketten.id = al.label_id
            WHERE
                al.type = 'produktion' AND al.article_id = ".$produktionsartikel['id'];
    $produktionsetiketten = $this->app->DB->SelectArr($sql);

    if (!empty($produktionsetiketten)) {
        $komma = "";
        foreach ($produktionsetiketten as $produktionsetikett) {
            $etikettentext .= $komma.$produktionsetikett['name']." (".$produktionsetikett['amount'].")";
            $komma = ", ";
        }
        $bodytext['Etiketten'] = $etikettentext;
    }

    $nlbr = "";
    foreach ($bodytext as $key => $value) {
        $body .= $nlbr.$key.":\n".$value;
        $nlbr = "\n\n";
    }

    $artikel = $this->app->DB->SelectArr(
        sprintf(
            "SELECT
                p.id,
                a.nummer as itemno,
                a.name_de as name,
                a.herstellernummer,
                TRIM(pp.menge)+0 as amount,
                '' as steuersatz_ermaessigt,
                DATE_FORMAT(datum,'%%Y%%m%%d') as datum                             
            FROM 
                produktion p
            INNER JOIN produktion_position pp ON p.id = pp.produktion
            INNER JOIN artikel a ON a.id = pp.artikel
            WHERE p.id = %d
            AND pp.stuecklistestufe = 0
            ", 
            $id
        )
    );

    /*
          $item['name'] = ($langeartikelnummern?"\r\n\r\n":'').$this->app->erp->ReadyForPDF($item['name']);
      $item['desc'] = $this->app->erp->ReadyForPDF($item['desc']);
      $item['itemno'] = $this->app->erp->ReadyForPDF($item['itemno']);
      $item['herstellernummer'] = $this->app->erp->ReadyForPDF($item['herstellernummer']);
      $item['artikelnummerkunde'] = $this->app->erp->ReadyForPDF($item['artikelnummerkunde']);
      $item['lieferdatum'] = $this->app->erp->ReadyForPDF($item['lieferdatum']);
      $item['hersteller'] = $this->app->erp->ReadyForPDF($item['hersteller']);

     if($this->getStyleElement('herstellernummerimdokument')=='1' && $item['herstellernummer']!='')

    */

   foreach($artikel as $key=>$value)  {
        $this->addItem($value);
    }

    if($this->app->erp->Firmendaten("footer_reihenfolge_Produktion_aktivieren")=="1")      {
      $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_Produktion");
      if($footervorlage=='') {
        $footervorlage = "{FOOTERVERSANDINFO}{FOOTERFREITEXT}{FOOTEREXTRAFREITEXT}\r\n{FOOTERTEXTVORLAGEProduktion}";
      }
      $footervorlage = str_replace('{FOOTERVERSANDINFO}',$versandinfo,$footervorlage);
      $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
      $footervorlage = str_replace('{FOOTEREXTRAFREITEXT}',$extrafreitext,$footervorlage);
      $footervorlage = str_replace('{FOOTERTEXTVORLAGEProduktion}',$this->app->erp->Beschriftung("Produktion_footer"),$footervorlage);
      $footervorlage  = $this->app->erp->ParseUserVars("Produktion",$id,$footervorlage);
      $footer = $footervorlage;
    }
    else {
      $footer = $versandinfo."$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("Produktion",$id,$this->app->erp->Beschriftung("Produktion_footer"));
    }

    $this->filename = $data['datum']."_PD".$data['belegnr'].".pdf";
    $this->setBarcode($data['belegnr']);

    $corrDetails = array();

    $corrDetails['Artikel'] = $produktionsartikel['name_de']." (".$produktionsartikel['nummer'].")";
    $corrDetails['Projekt'] = $data['projekt'];
    $corrDetails['Adresse'] = $data['adressname'];
    $corrDetails['Auftrag'] = $data['auftragsnummer'];
    $corrDetails['Interne Bezeichnung'] = $data['internebezeichnung'];
    $corrDetails['Datum'] = $data['datum'];

    $body = $this->app->erp->ParseUserVars("Produktion",$id,$body);

    $this->setTextDetails(
      array(
        'body'  => $body,
        'footer'=> $footer
      )
    );

    $this->setCorrDetails($corrDetails, true);
  }
}
