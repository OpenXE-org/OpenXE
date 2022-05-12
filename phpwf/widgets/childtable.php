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

/**
 *  CHild Tabelle
 * Stellt eine -Formatierte Child Tabelle mit optionalem Zeilenmenü zur Verfügung.
 * 
 * @package   
 * @author    Benedikt Sauter <sauter@ixbat.de>
 * @author    coma ag <info@coma.de>
 * @author    *105 - Multimediabüro <development@stern105.de>
 * @version   1.0
 * @since     PHP 4.x
 */
class ChildTable {

  /**
   * Target template to wich the table will be parsed. [Default: PAGE]
   * @type string
   */
  var $parsetarget;

  /**
   * HTMLTable. Apply format changes to this object.
   * @type Object
   * @access public
   */
  var $table;

  var $editdate;
  var $datasetssql;


  var $arrayuse;

  /**
   * Constructor method
   *
   * @param   $app  Default application class
   *
   * @return  Generated object ($this)
   * @access  public
   */
  function __construct(&$app, $name, $parsetarget="PAGE",$editdate=false) {
    $this->app = &$app;
    $this->parsetarget=$parsetarget;
    $this->name=$name;
    $this->editdate = $editdate;
  } // end of constructor


  // defines all datasets
  function DataSets($sql) {
    $this->datasetssql = $sql;
  } // end of function

  function Register($pointer, $event, $function) {
    $this->events[$event] = $function;
    $this->pointer = $pointer;
  } // end of function

  function CatchEvents() {
    $event=$this->app->Secure->GetPOST("{$this->name}_event");
    
    if(is_array($event)){
      $function = $this->events[key($event)];
      $this->pointer->$function($event[key($event)]);
    } // end of if
  } // end of function


   function MarkLineIfColHasValue($col,$value,$replace,$exclude) 
   {
      $this->mark_col = $col;
      $this->mark_value = $value;
      $this->mark_replace = $replace;
      $this->mark_exclude = $exclude;

      $this->mark = true;
   }

  /* Diese Funktion dient fuer tabellen in denen die sipchildtables zeilen verschiedene bedeutungen haben
   */

  function ShowMultiRow($contentArr){
    $this->arrayuse = true;

    //$this->app->Table->CreateTable($this->datasetssql, $this->parsetarget, 400);
    //$contentArr = $this->app->DB->SelectArr( $this->datasetssql );
    $this->app->Table->CreateTableFromArray($contentArr, $this->parsetarget, 450);
    //$this->app->Table->FormatCondition(1, "", "<span style=\"color:red;\"class=\"inactive\">%value%</span>", array(5,6));

    $this->Show();
  }

  function Show() {
    $this->CatchEvents();
    if(!$this->arrayuse)
      $this->app->Table->CreateTable($this->datasetssql, $this->parsetarget, 400);

    
    $numberofdatasets = count($this->app->DB->SelectArr($this->datasetssql));
    $module = $this->app->Secure->GetGET("module");
    $id = $this->app->Secure->GetGET("id");

    $delbutton =  "<input type=\"submit\" class=\"bu_delete\" title=\"Eintrag löschen\" name=\"{$this->name}_event[delete]\" value=\"%value%\">";
    // $editbutton =  "<input type=\"submit\" class=\"bu_edit\" title=\"Datum bearbeiten\" name=\"{$this->name}_event[editdate]\" value=\"%value%\">";
    $editbutton =  "<input type=\"button\" 
      onclick=\"window.location.href='index.php?module=$module&action=editdate&id=$id&value=%value%&childtable={$this->name}'\" 
      class=\"bu_edit\"  title=\"Datum bearbeiten\">";
    $downbutton =  "<input type=\"submit\" class=\"bu_down\" title=\"Nach unten\" name=\"{$this->name}_event[down]\" value=\"%value%\">";
    $upbutton =  "<input type=\"submit\" class=\"bu_up\"  title=\"Nach oben\" name=\"{$this->name}_event[up]\" value=\"%value%\">";
    $addbutton =  "<input type=\"button\" 
      onclick=\"window.location.href='index.php?module=$module&action=addafter&id=$id&value=%value%&childtable={$this->name}'\" 
      class=\"bu_add\"  title=\"Nach diesem Eintrag einfügen\">";
    $addfbutton =  "<input type=\"button\" 
      onclick=\"window.location.href='index.php?module=$module&action=addfafter&hut=$id&value=%value%&childtable={$this->name}'\" 
      class=\"bu_addf\"  title=\"Nach diesem Eintrag einfügen\">";


    $menunew = $addbutton;
    $menusingle = $delbutton.$addbutton.$addfbutton;
    $menufirst = $delbutton.$addbutton.$addfbutton.$downbutton;
    $menu  = $delbutton.$addbutton.$addfbutton.$downbutton.$upbutton;
    $menulast  = $delbutton.$addbutton.$addfbutton.$upbutton;

    // falls zeit relation berarbeitet werden soll
    if($this->editdate)
    {
      $menulast .= $editbutton; 
      $menu .= $editbutton; 
      $menufirst .= $editbutton; 
      $menusingle .= $editbutton; 

      $header = '
  <thead>
  <tr>
  <th class="divider">Nr.</th>
  <th class="divider">Name</th>
  <th class="divider">ID</th>
  <th class="divider">Gültig von&nbsp;-&nbsp;bis</th>
      ';
    }
    else {
      $header = '
  <thead>
  <tr>
  <th class="divider">Nr.</th>
  <th class="divider">Name</th>
  <th class="divider">ID</th>
      ';
    }

   
    if(count($this->events)>0)
      $header .= '<th class="divider">Aktion</th>';

    $header .= '
      </tr>
      </thead>';

    $this->app->Table->HeadingTop($header);


    if($this->editdate){
      $arr = array(1, 2, 3, 4,6);
    }
    else{
      $arr = array(1, 2, 3, 5);
    }

    // erstellen der richtigen menues 
    for($i=1;$i<=$numberofdatasets;$i++) {
      if($numberofdatasets==1)
        $this->app->Table->FormatCondition(1, 1, $menusingle, $arr);
      else if($i==1)
        $this->app->Table->FormatCondition(1, 1, $menufirst, $arr);
      else if($i == $numberofdatasets)
        $this->app->Table->FormatCondition(1, $numberofdatasets, $menulast, $arr);
      else
        $this->app->Table->FormatCondition(1, $i, $menu, $arr);
    } // end of for


      if(count($this->events)==0) {
        if($this->editdate){
          $this->app->Table->FormatCondition(6, 0, "<span class=\"inactive\">%value%</span>", array(5,6));
          $this->app->Table->HideCol(6);
        }
        else {
          $this->app->Table->FormatCondition(5, 0, "<span class=\"inactive\">%value%</span>", array(4, 5));
          $this->app->Table->HideCol(5);
          $this->app->Table->HideCol(4); // menu ausblenden falls keine events registriert sind
        }
      } else {
      // inaktiv zeile
      if($this->editdate){
        $this->app->Table->FormatCondition(6, 0, "<span class=\"inactive\">%value%</span>", array(5, 6));
        $this->app->Table->HideCol(6);
      }
      else {
        $this->app->Table->FormatCondition(5, 0, "<span class=\"inactive\">%value%</span>", array(4, 5));
        $this->app->Table->HideCol(5);
      }
    }  // end of if/else
    
    
 
    if($this->mark && count($this->events)!=0) {    
      $this->app->Table->FormatCondition($this->mark_col, $this->mark_value, $this->mark_replace,$this->mark_exclude);
      $this->app->Table->HideCol(6);
    }
   
    
    if ($numberofdatasets > 0)
      $this->app->Table->Show();
    else {
      $table = '
        <table width=400 cellpadding="0" cellspacing="0">
        [TABLEHEADER]
        <tr>
        <td></td>
        <td>keine Elemente vorhanden</td>
        <td></td>';

      if(count($this->events)!=0)
        $table .= '<td>'.$menunew.'</td>';

      $table .= '</tr></table>';
      //$this->app->Tpl->Set(TABLEHEADER,$header);
      $this->app->Tpl->Set($this->parsetarget,$table, array('TABLEHEADER'));
    } // end of if
  } // end of function
  
} // end of class
?>