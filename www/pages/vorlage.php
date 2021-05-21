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
include ("_gen/vorlage.php");

class Vorlage Extends GenVorlage {
  var $app;

  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "vorlage_list":
        // angeben welches recht der Benutzer braucht damit er auf diese tabelle zugreifen darf
        $allowed['vorlage'] = array('list');

        $heading = array('Name','Datum','Projekt','Men&uuml;');
        $width = array('50%','15%','15%','1%');

        $findcols = array('v.name', "v.datum",'p.abkuerzung','v.id');
        $searchsql = array('v.name', "DATE_FORMAT(v.datum,'%d.%m.%Y')",'p.abkuerzung');

        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc = 1;

        $menu = "<a href=\"index.php?module=vorlage&action=edit&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=vorlage&action=edit&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

        //$menucol = 4;
        //$moreinfo = true;
        //$sumcol = 9;

        $where = " v.id > 0 ";

        $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, v.name, DATE_FORMAT(v.datum,'%d.%m.%Y'), p.abkuerzung, v.id
          FROM vorlage v LEFT JOIN projekt p ON v.projekt = p.id ";

        $count = "SELECT count(v.id) FROM vorlage v LEFT JOIN projekt p ON v.projekt = p.id WHERE $where"; 
      break;
    }

    $erg = false;

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))$erg[$v] = $$v;
    }
    return $erg;
  }


  function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern)return;
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("list", "VorlageList");
    $this->app->ActionHandler("create", "VorlageCreate");
    $this->app->ActionHandler("edit", "VorlageEdit");
    $this->app->ActionHandler("pdf", "VorlagePDF");

    $this->app->ActionHandlerListen($app);

    $this->Install();
  }

  function Install()
  {
    $this->app->erp->CheckTable("vorlage");
    $this->app->erp->CheckColumn("id", "int(11)", "vorlage", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("name", "varchar(255)", "vorlage", "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn("adresse", "int(11)", "vorlage", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("datum", "DATE", "vorlage", "NOT NULL");
    $this->app->erp->CheckColumn("bemerkung", "TEXT", "vorlage", "NOT NULL");
    $this->app->erp->CheckColumn("projekt", "int(11)", "vorlage", "NOT NULL DEFAULT '0'");
  }

  function VorlageMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=vorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function VorlageList()
  {
    $this->app->erp->MenuEintrag("index.php?module=vorlage&action=create","Neue Vorlage");
    $this->app->erp->MenuEintrag("index.php?module=vorlage&action=list","&Uuml;bersicht");

    $this->app->YUI->TableSearch('TAB1','vorlage_list', "show","","",basename(__FILE__), __CLASS__);

    $this->app->Tpl->Parse("PAGE","vorlage_list.tpl");
  }

  function GenerateWord()
  {
    //Get a random word
    $nb=rand(3,10);
    $w='';
    for($i=1;$i<=$nb;$i++)
        $w.=chr(rand(ord('a'),ord('z')));
    return $w;
  }

  function GenerateSentence()
  {
    //Get a random sentence
    $nb=rand(1,10);
    $s='';
    for($i=1;$i<=$nb;$i++)
        $s.=$this->GenerateWord().' ';
    return substr($s,0,-1);
  }

  function VorlagePDF()
  {
    //Create a new PDF file
    $pdf=new SuperFPDF('P','mm','A4',$this->app);
    $pdf->AddPage();
    $pdf->SetFont('Arial','',8);
    //$pdf->WriteHTML("huhu<b>dick</b>"); 
    $pdf->SetTableWidths(array(30,50,30,40));
    $pdf->SetTableFonts(array(array('Arial','',8),array('Arial','',8),array('Arial','',8),array('Arial','',8)));
    srand(microtime()*1000000);
    for($i=0;$i<20;$i++)
      $pdf->TableRow(array($this->GenerateSentence(),$this->GenerateSentence(),$this->GenerateSentence(),$this->GenerateSentence()));

    $pdf->Output(date('Ymd')."_".$name."_ABRECHNEN.pdf",'D');
    exit;

  }



  function VorlageCreate()
  {
    $this->VorlageMenu();
    parent::VorlageCreate();
  }


  function VorlageEdit()
  {
    $this->VorlageMenu();
    parent::VorlageEdit();
  }

}
