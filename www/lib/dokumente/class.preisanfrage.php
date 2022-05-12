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

class PreisanfragePDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="preisanfrage";
    $this->doctypeOrig="Preisanfrage";
    parent::__construct($this->app,$projekt);
  } 


  function GetPreisanfrage($id,$info="",$extrafreitext="")
  {
    $this->doctypeid = $id;

    // das muss vom preisanfrage sein!!!!
    $this->setRecipientLieferadresse($id,"preisanfrage");

    $data = $this->app->DB->SelectArr("SELECT adresse, bearbeiter, DATE_FORMAT(datum,'%d.%m.%Y') AS datum, belegnr, freitext, projekt, bodyzusatz, sprache, zusammenfassen FROM preisanfrage WHERE id='$id' LIMIT 1");
    $data = reset($data);
    extract($data,EXTR_OVERWRITE);

    $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    if(empty($sprache))$sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");

    $this->app->erp->BeschriftungSprache($sprache);
    $this->projekt = $projekt;
    $this->sprache = $sprache;

    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
    }

    $this->doctype="preisanfrage";

    if($belegnr =="") $belegnr = "- ".$this->app->erp->Beschriftung("dokument_entwurf");


    if($info=="")
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_preisanfrage")." $belegnr";
    else
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_preisanfrage")."$info $belegnr";

    if($preisanfrage=="") $preisanfrage = "-";
    if($lieferantennummer=="") $lieferantennummer= "-";

    if(empty($kundennummer))$kundennummer = $this->app->DB->Select("SELECT kundennummerlieferant FROM adresse WHERE id='$adresse' LIMIT 1");
    $kundennummer = $this->app->erp->ReadyForPDF($kundennummer);

    $this->setCorrDetails(array(
      $this->app->erp->Beschriftung("dokument_bestellung_unserekundennummer")=>$kundennummer,
      $this->app->erp->Beschriftung("dokument_datum")=>$datum));




    $body=$this->app->erp->Firmendaten("preisanfrage_header");
    if($bodyzusatz!="") $body=$body."\r\n".$bodyzusatz;
    $body = $this->app->erp->ParseUserVars("preisanfrage",$id,$body);

    $this->setTextDetails(array(
          "body"=>$body,
          "footer"=>"$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("preisanfrage",$id,$this->app->erp->Firmendaten("preisanfrage_footer"))));
    $artikel = $this->app->DB->SelectArr("SELECT * FROM preisanfrage_position WHERE preisanfrage='$id' ORDER By sort");
    if($data['zusammenfassen']){
      $artikeltmp = array();

      $letzteartikelnummer = '';
      foreach ($artikel as $key => $value) {
        if($value['nummer'] != $letzteartikelnummer){
          $letzteartikelnummer = $value['nummer'];
        }else{
          $value['beschreibung'] = '';
          $value['nummer'] = '';
          $value['posausblenden'] = 1;
          $value['zusammenfassen'] = 1;
        }
        $artikeltmp[] = $value;
      }

      $artikel = $artikeltmp;
    }


    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM preisanfrage_position WHERE preisanfrage='$id' LIMIT 1");
    foreach($artikel as $key=>$value)
    {
      if($value['seriennummer']!="")
      {
        if( $value['beschreibung']!="")  $value['beschreibung'] =  $value['beschreibung']."\n";
        $value['beschreibung'] = "SN: ".$value['seriennummer']."\n\n";
      }

      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

      $value['menge'] = floatval($value['menge']);
      $this->addItem(array('amount'=>$value['menge'],
            'itemno'=>$value['nummer'],
            'artikel'=>$value['artikel'],
            'desc'=>ltrim($value['beschreibung']),
            'unit'=>$value['einheit'],
            'posausblenden'=>$value['posausblenden'],
            'hersteller'=>$value['hersteller'],
            'herstellernummer'=>trim($value['herstellernummer']),
            'zusammenfassen'=>$value['zusammenfassen'],
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


    /* Dateiname */
    $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    $tmp_name = str_replace('.','',$tmp_name);

    $this->filename = $datum."_AF".$belegnr.".pdf";
    $this->setBarcode($belegnr);
  }


}
