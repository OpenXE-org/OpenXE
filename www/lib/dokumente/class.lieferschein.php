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

class LieferscheinPDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="lieferschein";
    $this->doctypeOrig="Lieferschein";
    parent::__construct($this->app,$projekt);
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


  function GetLieferschein($id,$info="",$extrafreitext="")
  {
    $this->doctypeid = $id;
    $this->parameter = $info;
    if(method_exists($this->app->erp,'LieferscheinSeriennummernberechnen')) {
      $this->app->erp->LieferscheinSeriennummernberechnen($id);
    }
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    $lvl = null;

    // das muss vom lieferschein sein!!!!
    $this->setRecipientLieferadresse($id,"lieferschein");


    // OfferNo, customerId, OfferDate

    $data = $this->app->DB->SelectRow("SELECT kundennummer,adresse,sprache,auftragid,vertrieb,bearbeiter,
       DATE_FORMAT(datum,'%d.%m.%Y') as datum,ohne_artikeltext, DATE_FORMAT(datum,'%Y%m%d') as datum2,
       belegnr,land,freitext,projekt,bodyzusatz,ohne_briefpapier,ihrebestellnummer, email, telefon,
       abweichendebezeichnung as lieferscheinersatz FROM lieferschein WHERE id='$id'");
    extract($data,EXTR_OVERWRITE);
    $kundennummer = $data['kundennummer'];
    $adresse = $data['adresse'];
    $sprache = $data['sprache'];
    $auftragid = $data['auftragid'];
    $vertrieb = $data['vertrieb'];
    $bearbeiter = $data['bearbeiter'];
    $datum = $data['datum'];
    $ohne_artikeltext = $data['ohne_artikeltext'];
    $datum2 = $data['datum2'];
    $belegnr = $data['belegnr'];
    $land = $data['land'];
    $freitext = $data['freitext'];
    $projekt = $data['projekt'];
    $bodyzusatz = $data['bodyzusatz'];
    $ohne_briefpapier = $data['ohne_briefpapier'];
    $ihrebestellnummer = $data['ihrebestellnummer'];
    $lieferscheinersatz = $data['lieferscheinersatz'];
    $email = $data['email'];
    $telefon = $data['telefon'];

    $this->projekt = $projekt;
    if(empty($kundennummer)) {
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    }
    if(empty($sprache)) {
      $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
    }
    $this->sprache = $sprache;
    $this->app->erp->BeschriftungSprache($sprache);
    $auftrag = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);

      $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);



    $trackingnummer = $this->app->DB->Select("SELECT tracking FROM versand WHERE lieferschein='$id' LIMIT 1");

    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }

    if($belegnr=="" || $belegnr=="0") $belegnr = "- ".$this->app->erp->Beschriftung("dokument_entwurf");

    $this->zusatzfooter = " (LS$belegnr)";
    $isKommsionierschein = $info==='kommissionierschein';
    if($isKommsionierschein) {
      $this->doctypeOrig = $this->app->erp->Beschriftung("dokument_kommissionierschein") . " $belegnr";
    }
    else {
      if($lieferscheinersatz){
        $this->doctypeOrig = ($this->app->erp->Beschriftung("bezeichnunglieferscheinersatz") ? $this->app->erp->Beschriftung("bezeichnunglieferscheinersatz") : $this->app->erp->Beschriftung("dokument_lieferschein")) . $info . " $belegnr";
      }
      else {
        $this->doctypeOrig = $this->app->erp->Beschriftung("dokument_lieferschein").$info." $belegnr";
      }
    }



    $lieferschein = "-";

    if($kundennummer=="") $kundennummer= "-";

    if($bearbeiter==$vertrieb) $vertrieb="";

    $projektabkuerzung = $this->app->DB->Select(sprintf('SELECT abkuerzung FROM projekt WHERE id = %d', $projekt));

    $bearbeiteremail = $this->app->DB->Select("SELECT b.email FROM lieferschein l LEFT JOIN adresse b ON b.id=l.bearbeiterid WHERE l.id='$id' LIMIT 1");
    $bearbeitertelefon = $this->app->DB->Select("SELECT b.telefon FROM lieferschein l LEFT JOIN adresse b ON b.id=l.bearbeiterid WHERE l.id='$id' LIMIT 1");


    /** @var \Xentral\Modules\Company\Service\DocumentCustomizationService $service */
    $service = $this->app->Container->get('DocumentCustomizationService');
    if($block = $service->findActiveBlock('corr', 'delivery_note', $projekt)) {
      $sCD = $service->parseBlockAsArray($this->getLanguageCodeFrom($this->sprache),'corr', 'delivery_note',[
        'LIEFERSCHEINNUMMER'  => $belegnr,
        'DATUM'          => $datum,
        'KUNDENNUMMER'   => $kundennummer,
        'BEARBEITER'     => $bearbeiter,
        'BEARBEITEREMAIL' => $bearbeiteremail,
        'BEARBEITERTELEFON' => $bearbeitertelefon,
        'VERTRIEB'       => $vertrieb,
        'PROJEKT'        => $projektabkuerzung,
        'IHREBESTELLNUMMER' => $ihrebestellnummer,
        'AUFTRAGSNUMMER' => $auftrag,
        'EMAIL'          => $email,
        'TELEFON'        => $telefon,
        'TRACKINGNUMMER' => $trackingnummer

      ], $projekt);
      if(!empty($sCD)) {
        switch($block['fontstyle']) {
          case 'f':
            $this->setBoldCorrDetails($sCD);
            break;
          case 'i':
            $this->setItalicCorrDetails($sCD);
            break;
          case 'fi':
            $this->setItalicBoldCorrDetails($sCD);
            break;
          default:
            $this->setCorrDetails($sCD, true);
            break;
        }
      }
    }else{
      if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden){
        $sCD = array($this->app->erp->Beschriftung("dokument_auftrag") => $auftrag, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_lieferdatum") => $datum);
        if(!$briefpapier_bearbeiter_ausblenden){
          if($bearbeiter) $sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")] = $bearbeiter;
        }elseif(!$briefpapier_vertrieb_ausblenden){
          if($vertrieb) $sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")] = $vertrieb;
        }
        $this->setCorrDetails($sCD);
      }else{
        $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_auftrag") => $auftrag, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_lieferdatum") => $datum, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb));
      }
    }

    $body=$this->app->erp->Beschriftung("lieferschein_header");
    if($bodyzusatz!="") $body=$body."\r\n".$bodyzusatz;
    $body = $this->app->erp->ParseUserVars("lieferschein",$id,$body);

    if ($versandart!="" && $trackingnummer!="" && $this->app->erp->Firmendaten("festetrackingnummer")=="1"){
        $versandinfo = "$versandart: $trackingnummer\r\n";
    }else{ $versandinfo ="";}

    if($this->app->erp->Firmendaten("footer_reihenfolge_lieferschein_aktivieren")=="1")      {
      $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_lieferschein");
      if($footervorlage=='') {
        $footervorlage = "{FOOTERVERSANDINFO}{FOOTERFREITEXT}{FOOTEREXTRAFREITEXT}\r\n{FOOTERTEXTVORLAGELIEFERSCHEIN}";
      }
      $footervorlage = str_replace('{FOOTERVERSANDINFO}',$versandinfo,$footervorlage);
      $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
      $footervorlage = str_replace('{FOOTEREXTRAFREITEXT}',$extrafreitext,$footervorlage);
      $footervorlage = str_replace('{FOOTERTEXTVORLAGELIEFERSCHEIN}',$this->app->erp->Beschriftung("lieferschein_footer"),$footervorlage);
      $footervorlage  = $this->app->erp->ParseUserVars("lieferschein",$id,$footervorlage);
      $footer = $footervorlage;
    }
    else {
      $footer = $versandinfo."$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("lieferschein",$id,$this->app->erp->Beschriftung("lieferschein_footer"));
    }


    $this->setTextDetails(
      array(
        'body'  => $body,
        'footer'=> $footer
      )
    );

    $orderpicking_sort = $this->app->erp->Projektdaten($this->projekt, 'orderpicking_sort');

    if($isKommsionierschein && in_array($orderpicking_sort, ['storagelocationrow','storagelocationrowpartlist'])) {
      $artikel = $this->app->DB->SelectArr(
        sprintf("SELECT lp.*, storage.rownumber 
          FROM lieferschein_position AS lp
          LEFT JOIN objekt_lager_platz AS olp ON lp.id = olp.parameter AND olp.objekt = 'lieferschein'
          LEFT JOIN lager_platz AS storage ON olp.lager_platz = storage.id
          WHERE lp.lieferschein=%d 
          GROUP BY lp.id
          ORDER BY IFNULL(storage.rownumber, 0), lp.sort",
          $id
        )
      );
      if($orderpicking_sort === 'storagelocationrow' && !empty($artikel)) {
        $artikel = $this->sortAricleExploded($artikel);
      }
    }
    else{
      $artikel = $this->app->DB->SelectArr(
        sprintf(
          'SELECT lp.* FROM lieferschein_position AS lp WHERE lp.lieferschein=%d ORDER By lp.sort',
          $id
        )
      );
      $this->app->erp->RunHook('lieferscheinpdf_getlieferschein', 2, $id, $artikel);
    }

    $belege_subpositionenstuecklisten = $this->app->erp->Firmendaten('belege_subpositionenstuecklisten');
    $belege_stuecklisteneinrueckenmm = $this->app->erp->Firmendaten('belege_stuecklisteneinrueckenmm');
    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM lieferschein_position WHERE lieferschein='$id' LIMIT 1");
    $ausblenden = [];
    $kommissionierverfahren = $this->app->DB->Select("SELECT kommissionierverfahren FROM projekt WHERE id='".$this->projekt."'");
    $lagerplatzlieferscheinausblenden = $this->app->DB->Select("SELECT lagerplatzlieferscheinausblenden FROM projekt WHERE id='".$this->projekt."'");
    foreach($artikel as $key=>$value)  {

      $snummer = '';
      $seriennummernliste="";

      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

      if($value['explodiert_parent_artikel'] > 0)
      {
        if($belege_subpositionenstuecklisten || $belege_stuecklisteneinrueckenmm) {
          $value['bezeichnung'] = ltrim(ltrim($value['bezeichnung'],'*'));
        }
        if(isset($lvl[$value['explodiert_parent']]))
        {
          $value['lvl'] = $lvl[$value['explodiert_parent']] + 1;
        }else{
          $value['lvl'] = 1;
        }
        $lvl[$value['id']] = $value['lvl'];
        $check_ausblenden = $this->app->DB->Select("SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$value['explodiert_parent_artikel']."' LIMIT 1");

        if($info==='kommissionierschein') {
          $check_ausblenden=0;
        }
        if(!$check_ausblenden && in_array($value['explodiert_parent'], $ausblenden)) {
          $check_ausblenden = true;
        }
        if($check_ausblenden) {
          $ausblenden[] = $value['id'];
        }
      }
      else {
        $check_ausblenden=0;
        $lvl[$value['id']] = 0;
      }


      if(!$this->app->erp->Export($land))
      {
        $value['zolltarifnummer']="";
        $value['herkunftsland']="";
      }

      if($ohne_artikeltext=="1") $value['beschreibung']="";


      $value['menge'] = (float)$value['menge'];


      // nur wenn kommissionierverfahren lieferscheinlager lieferscheinlagerscan

      if($lagerplatzlieferscheinausblenden=='1' ||
        ($kommissionierverfahren !=='lieferscheinlager' && $kommissionierverfahren !=='lieferscheinlagerscan' && $info!=='kommissionierschein')){
        $value['lagertext'] = '';
      }


      if($value['ausblenden_im_pdf'] && $info !=='kommissionierschein') $check_ausblenden=1;

      if($check_ausblenden!=1)
      {
        $this->addItem(array(
              'belegposition'=>$value['id'],
              'amount'=>$value['menge'],'lvl'=>$value['lvl'],
              'itemno'=>$value['nummer'],
              'pos_id'=>$value['id'],
              'artikel'=>$value['artikel'],
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
    }


    /* Dateiname */
    //$tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    //$tmp_name = str_replace('.','',$tmp_name);

    $this->filename = $datum2."_LS".$belegnr.".pdf";
    $this->setBarcode($belegnr);
  }
}
