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


class EasyTable {
  /** @var ApplicationCore $app */
  var $app;
  /** @var array */
  var $rows;
  /** @var array */
  var $datasets;
  /** @var array */
  var $align;
  /** @var array */
  var $headings;
  /** @var array */
  public $width_headings;
  /** @var int|string */
  public $page;

  /**
   * EasyTable constructor.
   *
   * @param ApplicationCore $app
   */
  function __construct($app)
  {
    $this->app = $app;
    $this->sql ='';
    $this->limit ='';
  }


  function AddRow($rows)
  {
    $this->datasets[] = $rows;
  }

  /**
   * @param string|array $sql
   * @param string|int   $limit
   * @param string       $newevent
   */
  function Query($sql,$limit='',$newevent='')
  {
    if(!is_array($sql)) {
      $this->sql = $sql;
      $this->limit = $limit;
      $this->headings = null;

      if($limit != 0){
        $page = $this->app->Secure->GetGET('page');
        if(!is_numeric($page)){
          $page = 1;
        }

        $this->page = $page;
        $this->start = ($page - 1) * $this->limit;

        $sql .= " LIMIT {$this->start},{$this->limit}";
      }
      $this->searchrow = '';
      $jsarray = null;
      if(!empty($this->app->stringcleaner)){
        $jsarray = $this->app->stringcleaner->CheckSQLHtml($sql);
      }
      $this->app->DB->DisableHTMLClearing(true);
      $this->app->DB->DisableJSClearing(true);
      $this->datasets = $this->app->DB->SelectArr($sql);
      $this->app->DB->DisableHTMLClearing(false);
      $this->app->DB->DisableJSClearing(false);
    }
    else {
      $this->datasets = $sql;
    }

    if($jsarray && $this->datasets)
    {
      foreach($this->datasets as $k => $arr)
      {
        $j = 0;
        foreach($arr as $k2 => $v)
        {
          if(isset($jsarray[$j]) && !$jsarray[$j])
          {
            $this->datasets[$k][$k2] = strip_tags($v);
          }elseif(isset($jsarray[$j]) && 1 == $jsarray[$j])
          {
            $this->datasets[$k][$k2] = $this->app->stringcleaner->xss_clean($v, false);//CleanString($v, 'nojs', $dummy);
          }
          $j++;
        }
      }
    }
    if(!empty($this->datasets) && count($this->datasets)>0){
      foreach($this->datasets[0] as $colkey=>$value)
      {
        $this->headings[]=ucfirst($colkey);
	//$this->searchrow[0][$colkey]="<form action=\"\" method=\"post\"><input type=\"text\" style=\"width:100%; background-color:#fff;\"></form>";
      }
    }
    //$this->searchrow[0][id]="";
   
    //if(count($this->datasets)>=$limit)
    //  $this->datasets = $this->array_insert($this->datasets,0,$this->searchrow); 

    $this->searchrow='';
    if($newevent!=='noAction')
    {
      $this->headings[!empty($this->headings)?(count($this->headings)-1):0] = 'Aktion';
      $this->width_headings[count($this->headings)-1] = '120px'; //TMP fuer Belegtabelle
    } 
    
  }
    // Fügt $value in $array ein, an der Stelle $index
    function array_insert($array, $index, $value)
    {
      return array_merge(array_slice($array, 0, $index), $value, array_slice($array, $index));
    }  


  function DisplayWithSort($parsetarget)
  {
    
    $htmltable = new HTMLTable(0,"100%","",3,1);
    $htmltable->width_headings = $this->width_headings;
    $htmltable->AddRowAsHeading($this->app->Tpl->pruefeuebersetzung($this->headings,'table'));

    $htmltable->ChangingRowColors('#e0e0e0','#fff');

    if(count($this->datasets)>0){
      foreach($this->datasets as $row){
        $htmltable->NewRow();
        foreach($row as $field)
          $htmltable->AddCol($field);
      }
    } 
    $module = $this->app->Secure->GetGET("module");
    $htmltable->ReplaceCol(count($this->headings),
      "<a href=\"index.php?module=$module&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.svg\"></a>
      &nbsp;<a href=\"index.php?module=$module&action=delete&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/new.png\"></a>
      &nbsp;<a href=\"index.php?module=$module&action=delete&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/delete.png\"></a>
      &nbsp;<a href=\"index.php?module=$module&action=delete&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/up.png\"></a>
      &nbsp;<a href=\"index.php?module=$module&action=delete&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/down.png\"></a>
      ");
    
    $this->app->Tpl->Set($parsetarget,$htmltable->Get());
  }
  
  function DisplayWithDelivery($parsetarget)
  {
    
    $htmltable = new HTMLTable(0,"100%","",3,1);
    $htmltable->width_headings = $this->width_headings;
    $htmltable->AddRowAsHeading(array($this->app->Tpl->pruefeuebersetzung('Suchen','table'),'','',''));

    $htmltable->ChangingRowColors('#e0e0e0','#fff');

    if(count($this->datasets)>0){
      foreach($this->datasets as $row){
        $htmltable->NewRow();
        $link="";
        $cols=0;
        foreach($row as $key=>$field){
          if($cols<3){
            $htmltable->AddCol($field);
            $cols++;
          }
          if($key!="id")
            $link = $link."window.opener.document.getElementsByName('$key')[0].value='$field';";
        }
        $htmltable->AddCol("<input type=\"button\" onclick=\"
              $link
                    window.close();
                    \" value=\"OK\">
                    ");

      }
    } 
    $module = $this->app->Secure->GetGET("module");
    /*
    $htmltable->ReplaceCol(4,
      "<input type=\"button\" onclick=\"
      $link
      window.close();
      \" value=\"OK\">
      ");
   */ 
    $this->app->Tpl->Set($parsetarget,$htmltable->Get());
  }


  function DisplayOwn($parsetarget,$click,$limit=30,$idlabel="id",$tmpid="")
  {
    $newevent = null;
    $pages = round(count($this->app->DB->SelectArr($this->sql)) / $this->limit);
    if($pages==0)$pages=1;

    $module = $this->app->Secure->GetGET("module");
    $action = $this->app->Secure->GetGET("action");
    
    if($tmpid>0)
      $id = $tmpid;
    else
      $id = $this->app->Secure->GetGET($idlabel);


    if($this->page ==0 || $this->page=="") $this->page = 1;
    if($this->page <=1) $before = $this->page; else $before=$this->page-1;
    if($this->page >=$pages) $next = $this->page; else $next=$this->page+1;

    $colmenu = "<table width=\"100%\"><tr><td><a href=\"index.php?module=$module&action={$action}&$idlabel=$id&page=1\"><img border=\"0\" src=\"./themes/[THEME]/images/first.png\"></a>&nbsp;
    <a href=\"index.php?module=$module&action={$action}&$idlabel=$id&page=".$before."\"><img border=\"0\" src=\"./themes/[THEME]/images/before.png\"></a></td>";

    //for($i=0;($i<$pages && $i< 10);$i++)
    //{
      /*
      if($this->page==($i+1))
      {
	$colmenu .= "<td><a href=\"index.php?module=$module&action={$action}&$idlabel=$id&page=".($i+1)."\"><b>".
	  ($i+1)."</b></a></td>";
      } else {
	$colmenu .= "<td><a href=\"index.php?module=$module&action={$action}&$idlabel=$id&page=".($i+1)."\">".
	  ($i+1)."</a></td>";
      }
      */
    //}
      $colmenu .= "<td align=center>Seite {$this->page} von $pages</td>";

    $colmenu .= "<td align=right><a href=\"index.php?module=$module&action={$action}&$idlabel=$id&page=".($next)."\"><img border=\"0\" src=\"./themes/[THEME]/images/next.png\"></a>&nbsp;
    <a href=\"index.php?module=$module&action={$action}&$idlabel=$id&page=$pages\"><img border=\"0\" src=\"./themes/[THEME]/images/last.png\"></a></td></tr></table>";

    $this->app->Tpl->Set($parsetarget,$colmenu);

    $htmltable = new HTMLTable(0,"100%","",3,1);
    $htmltable->width_headings = $this->width_headings;
    $htmltable->AddRowAsHeading($this->app->Tpl->pruefeuebersetzung($this->headings,'table'));

    $htmltable->ChangingRowColors('#e0e0e0','#fff');


    if(count($this->datasets)>0){
      foreach($this->datasets as $row){
        $htmltable->NewRow();
        foreach($row as $field)
          $htmltable->AddCol($field);
      }
      $module = $this->app->Secure->GetGET("module");
      if($newevent!="noAction"){
        $htmltable->ReplaceCol(count($this->headings),$click);
      }
      $this->app->Tpl->Add($parsetarget,$htmltable->Get());
    }
    else {
      if($newevent=="noAction") $newevent="";
      $this->app->Tpl->Set($parsetarget,"<div class=\"info\">Keine Daten vorhanden! $newevent</div>");
    }
/*
    if(count($this->datasets)>0){
      foreach($this->datasets as $row){
	$htmltable->NewRow();
	foreach($row as $field)
	  $htmltable->AddCol($field);
      }

      for($i=0;$i<count($menu);$i++)
      {
	$menustring .= "<a href=\"index.php?module=$module&action={$menu[$i]}&$idlabel=%value%&id=$id\">{$menu[$i]}</a>&nbsp;";
      }

      $htmltable->ReplaceCol(count($this->headings),$menustring);
      $this->app->Tpl->Add($parsetarget,$htmltable->Get());
    }
    else {
      $this->app->Tpl->Add($parsetarget,"Keine Daten vorhanden!");
    }
*/
    $this->app->Tpl->Add($parsetarget,$colmenu);
  }


  function DisplayWidthInlineEdit($parsetarget,$click="",$newevent="",$nomenu="false")
  {
    $htmltable = new HTMLTable(0,"100%","",3,1);
    $htmltable->width_headings = $this->width_headings;
    $htmltable->AddRowAsHeading($this->app->Tpl->pruefeuebersetzung($this->headings,'table'));

    $htmltable->ChangingRowColors('#e0e0e0','#fff');

    if(count($this->datasets)>0){
      foreach($this->datasets as $row){
        $htmltable->NewRow();
        foreach($row as $field)
          $htmltable->AddCol($field);

        $htmltable->NewRow();

        $start = "<form>";	
        foreach($row as $key=>$field){
          if($key!="id")
            $htmltable->AddCol($start."<input type=\"text\" size=\"10\" value=\"$field\">");
          else
            $htmltable->AddCol($field."</form>");

          $start="";
        }
      }
      $module = $this->app->Secure->GetGET("module");
      if($newevent!="noAction"){
        $htmltable->ReplaceCol(count($this->headings),$click);
      }
      $this->app->Tpl->Add($parsetarget,$htmltable->Get(1));
    }
    else {
      $this->app->Tpl->Add($parsetarget,"Keine Daten vorhanden! $newevent");
    }
  }

  function DisplayEditable($parsetarget,$click="",$newevent="",$nomenu="false",$arrayEditable="",$editlastrow=false,$nowarp=true)
  {
    $module = $this->app->Secure->GetGET("module");
    $this->app->erp->RunHook('EasyTableDisplayEditableClick', 3, $module, $click, $newevent);
    $htmltable = new HTMLTable(0,"100%","",3,1);

    if(!$nowarp)$htmltable->nowrap="";
    // Letzte Spalte aendern
    if($newevent == "noAction")
      $this->headings[count($this->headings)-1] = $click;

    $htmltable->width_headings = $this->width_headings;
    $htmltable->AddRowAsHeading($this->app->Tpl->pruefeuebersetzung($this->headings,'table'));
    $htmltable->ChangingRowColors('#e0e0e0','#fff');

    $result = '';

    $htmltable->nowrap = array(count($this->headings)-1);

    if(count($this->datasets)>0){
      foreach($this->datasets as $row){
        $htmltable->NewRow();
        foreach($row as $field)
          $htmltable->AddCol($field);
      }

      if($newevent!="noAction"){
        $htmltable->ReplaceCol(count($this->headings),$click);
      } 
      if($parsetarget=="return")
        $result .= $htmltable->GetSpecialCSSClasses($arrayEditable,$editlastrow);
      else
      {
        if(is_array($arrayEditable))
        {
          $this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses($arrayEditable,$editlastrow,2));
        }
        else {
          if($module=="lieferschein" || $module=="anfrage" || $module=="preisanfrage")
          $this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses(array(4,5),$editlastrow,1));
          else if($module==='retoure'){
            $this->app->Tpl->Add($parsetarget, $htmltable->GetSpecialCSSClasses(array(4, 5, 7, 8, 10), $editlastrow, 1));
          }
          else if($module=="produktion")
          $this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses(array(4),$editlastrow,1));
          else if($module=="arbeitsnachweis")
          $this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses(array(2,3,4,5,6),$editlastrow,2));
          else if($module=="reisekosten")
          $this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses(array(1,3),$editlastrow,2));
          else if($module=="kalkulation")
          $this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses(array(2),$editlastrow,2));
          else if($module=="inventur")
          $this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses(array(1,4,5),$editlastrow,2));
          else if($module=="auftrag" || $module=="rechnung" || $module=="gutschrift"|| $module=="bestellung" || $module=="angebot" || $module=="proformarechnung")
          {
            if($module=='bestellung')
            {
              $editcols = array(4,5,6,7);
            }else{
              $einkaufspreiseerlaubt = false;
              if($einkaufspreiseerlaubt)
              {
                $editcols =array(4,5,6,7,8,9);
                //$this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses(array(4,5,6,7,8,9),$editlastrow,2));
              }else{
                $editcols = array(4,5,6,7,8);
                //$this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses(array(4,5,6,7,8),$editlastrow,2));
              }
            }
            $clastRow = 2;
            $this->app->erp->RunHook('EasyTableDisplayEditable', 4, $module, $editcols, $editlastrow,$clastRow);
            $this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses($editcols,$editlastrow,$clastRow));
          }
          else
            {
              $editcols = array(4,5,6);
              $clastRow = 2;
              $this->app->erp->RunHook('EasyTableDisplayEditable', 4, $module, $editcols, $editlastrow, $clastRow);
              $this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses($editcols,$editlastrow,$clastRow));
            }
          //$this->app->Tpl->Add($parsetarget,$htmltable->GetSpecialCSSClasses(array(4,5,6),$editlastrow,2));

        }
      }
    }
    else {
      if($newevent=="noAction") $newevent="";
      
      if($parsetarget=="return")
        $result .= "<div class=\"info\">Keine Daten vorhanden! $newevent</div>";
      else
        $this->app->Tpl->Set($parsetarget,"<div class=\"info\">Keine Daten vorhanden! $newevent</div>");
    }

    if($parsetarget=="return") return $result;
  }

  /**
   * @param string $parsetarget
   * @param string $click
   * @param string $newevent
   * @param string $nomenu
   * @param int    $columns
   * @param int    $rows
   * @param bool   $nowrap
   *
   * @return string|void
   */
  function DisplayNew($parsetarget,$click='',$newevent='',$nomenu='false',$columns=0,$rows=0,$nowrap=true)
  {
    $result = '';
    $secondline = isset($this->secondline)?$this->secondline:false;
    $htmltable = new HTMLTable(0,"99.9%","",3,1,"font-size: 90%; ");

    if(!$nowrap) $htmltable->nowrap="";
    if($secondline) {
      unset($this->headings[count($this->headings)-1]);
    }
    // Letzte Spalte aendern
    if($newevent === 'noAction'){
      $this->headings[count($this->headings) - 1] = $click;
    }

    $htmltable->width_headings = isset($this->width_headings)?$this->width_headings:null;
    $htmltable->AddRowAsHeading($this->app->Tpl->pruefeuebersetzung($this->headings,'table'));
    $htmltable->ChangingRowColors('#e0e0e0','#fff');

    if(!empty($this->datasets) && count($this->datasets)>0){
      $rowcounter=0;
      foreach($this->datasets as $row){
        $htmltable->NewRow();
        $aligncounter=0;
        $c = 0;
        $crow = count($row);
        $_field = '';
        foreach($row as $field)
        {
          $c++;
          if($c < $crow || !$secondline) {
            if (isset($this->align[$aligncounter]))
              $htmltable->AddCol($field, $this->align[$aligncounter]);
            else
              $htmltable->AddCol($field);
            $aligncounter++;
          }elseif($secondline)$_field = $field;
        }
        if($secondline)
        {
          $htmltable->NewRow();
          $htmltable->AddCol('','','',$crow-1);
          $htmltable->NewRow();
          $field = $_field;
          if (isset($this->align[$aligncounter]))
            $htmltable->AddCol($field, $this->align[$aligncounter],'',$crow-1);
          else
            $htmltable->AddCol($field,'','',$crow-1);
        }
        $rowcounter++;
      }

      if($rowcounter<=$rows){
        for($rowcounter;$rows > $rowcounter;$rowcounter++)
        {
          $htmltable->NewRow();
          for($i_c=0;$i_c<$columns;$i_c++)
            $htmltable->AddCol('');
        }
      }

      $module = $this->app->Secure->GetGET("module");
      if($newevent!="noAction"){
        $htmltable->ReplaceCol(count($this->headings),$click);
      } 
      if($parsetarget=="return")
        $result .= $htmltable->Get($secondline?3:false);
      else
        $this->app->Tpl->Add($parsetarget,$htmltable->Get($secondline?3:false));
    }
    else {
      if($newevent=="noAction") $newevent="";
      if($newevent=="Men&uuml;") $newevent="";
      
      if($parsetarget=="return")
        $result .= "<div class=\"info\">Keine Daten vorhanden! $newevent</div>";
      else
        $this->app->Tpl->Set($parsetarget,"<div class=\"info\">Keine Daten  vorhanden! $newevent</div>");
    }

    if($parsetarget=="return") return $result;
  }

  function Display($parsetarget,$clickmodule="",$clickaction="",$clicklabel="",$newevent="")
  {
    
    $htmltable = new HTMLTable(0,"100%","",3,1);
    $htmltable->width_headings = $this->width_headings;
    $htmltable->AddRowAsHeading($this->app->Tpl->pruefeuebersetzung($this->headings,'table'));

    $htmltable->ChangingRowColors('#e0e0e0','#fff');

    if(count($this->datasets)>0){
      foreach($this->datasets as $row){
        $htmltable->NewRow();
        foreach($row as $field)
          $htmltable->AddCol($field);
      }
      $module = $this->app->Secure->GetGET("module");
      if($clickaction=="") {
        $htmltable->ReplaceCol(count($this->headings),
          "<a href=\"index.php?module=$module&action=edit&id=%value%\" alt=\"edit\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.svg\"></a>
          <!--<a href=\"index.php?module=$module&action=copy&id=%value%\">copy</a>-->
          &nbsp;<a href=\"index.php?module=$module&action=delete&id=%value%\" alt=\"del\"><img border=\"0\" src=\"./themes/[THEME]/images/delete.svg\"></a>");
      } else {
        $htmltable->ReplaceCol(count($this->headings),
          "<a href=\"index.php?module=$clickmodule&action=$clickaction&id=%value%\">$clicklabel</a>");

      }
      $this->app->Tpl->Add($parsetarget,$htmltable->Get());
    }
    else {
      $this->app->Tpl->Add($parsetarget,"Keine Daten vorhanden! $newevent");
    }
  }

}
