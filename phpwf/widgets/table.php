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
 * SIP Tabelle
 * Stellt eine SIP-Formatierte Tabelle mit optionalem Zeilenmenü zur Verfügung.
 * 
 * @package		SIP
 * @author    Benedikt Sauter <sauter@sistecs.de>
 * @author		coma ag <info@coma.de>
 * @author		*105 - Multimediabüro <development@stern105.de>
 * @version   1.0
 * @since     PHP 4.x
 */
class Table {

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

  /**
   * SQL statement to fill table content. Altrnatively use CreateTableFromArray().
   * @type string
   * @access private
   */
  var $sql;

  /**
   * Data array to fill table content. Altrnatively use CreateTableFromSQL().
   * @type string
   * @access private
   */
  var $contentArr;

  /**
   * Indicates whether data is gathered from SQL statement od provided array
   * @type bool
   * @access private
   */
  var $fromSQL = true;

  var $searchform=false; // normally true
  var $sortheading=true;
  var $html;
  var $menu;
  var $rowlimit=0;


  /**
   * Constructor method
   *
   * @param   $app  Default application class
   *
   * @return  Generated object ($this)
   * @access  public
   */
  function __construct($app) {
    $this->app = $app;
    $this->app->Tpl->Set('TABLEHEADER', "");
    $this->app->Tpl->Set('TABLEFOOTER', "");
  } // end of constructor

  /**
   * Standard method to create a table. The data is gathered 
   * from provided sql statement and formatted, finally printed 
   * to screen.
   * 
   * <br /><br /><strong>
   * NOTE: Method is deprecated. Use CreateTableFromArray() or 
   * CreateTableFromSQL() instead!
   * </strong>
   *
   * @param   $sql          SQL stemenet (Select) to fill the table content
   * @param   $parsetarget  Target template to where the content is parsed [Default: PAGE]
   *
   * @access  public
   */
  function CreateTable($sql, $parsetarget='PAGE',$size="") {
    $this->CreateTableFromSQL($sql, $parsetarget,$size);
  } // end of function

  /**
   * Standard method to create a table: The data is gathered 
   * from provided sql statement and formatted, finally printed 
   * to screen.
   * 
   * @param   $sql          SQL stemenet (Select) to fill the table content
   * @param   $parsetarget  Target template to where the content is parsed [Default: PAGE]
   *
   * @access  public
   */
  function CreateTableFromSQL($sql, $parsetarget='PAGE', $size="") 
  {
    $order=$this->app->Secure->GetGET("order");
    if($order!="")
    	$sql = $sql." ORDER By $order";

    $this->parsetarget  = $parsetarget; 
    $this->sql          = $sql;
    $this->contentArr   = $this->app->DB->SelectArr($this->sql);
    $this->html 	= "";
    $this->fromSQL      = true;
    $this->InitTable($size);
  } // end of function

  /**
   * Alternative method to create a table: The data is gathered 
   * from provided data array, then formatted and finally printed 
   * to screen.
   * 
   * @param   $contentArr   SQL stemenet (Select) to fill the table content
   * @param   $parsetarget  Target template to where the content is parsed [Default: PAGE]
   *
   * @access  public
   */
  function CreateTableFromArray($contentArr, $parsetarget=PAGE, $size="") 
  {
 	/* 
  	echo "<pre>";
	print_r($contentArr[0]);
  	echo "</pre>";
	*/


	$order=$this->app->Secure->GetGET("order");
	if($order!="")
    		$contentArr = $this->SortTableArray($contentArr,$order);

    $this->parsetarget  = $parsetarget; 
    $this->contentArr   = $contentArr;
    $this->fromSQL      = false;
    $this->InitTable($size);
  } // end of function


  function SortTableArray($data,$order)
  {

	// to be sure, that we have a numeric array 
	for ($i=0; $i<count($data); $i++) {
	       	foreach($data[$i] as $key=>$value)
		{
			$numarr[$i][]=$value;
		}
	}
	$data = $numarr;

	// Obtain a list of columns
	// We have an array of rows, but array_multisort()  requires an array of columns, 
	// so we use the below code to obtain the columns, then perform the sorting.
	if(count($data)>0)
	foreach ($data as $key => $row) {
		for($i=0;$i<count($row);$i++)
		{
			$sort[$i][$key]  = $row[$i];
		}
      		//$stueck[$key] = $row[7];
      	}
	      // print_r($sort);
      	// Sort the data with volume descending, edition ascending
      	// Add $data as the last parameter, to sort by the common key
      	@array_multisort($sort[$order],SORT_ASC,SORT_NUMERIC,$data);

  	return $data;
  }

  /**
   * Initializes an HTML table
   * 
   * @param   $size  width table [Default: 700]
   *
   * @access  private
   */
  function InitTable($size=700) {
    // Create html table
    $this->table = new HTMLTable("0",$size,"","0","0");
    $this->table->SetTDClass("divider");
    $this->table->ChangingRowColors('#F5F5F5', '');
    $this->Generate();
  } // end of function

  /**
   * Use Array $contentArr to fill table with data 
   * 
   * @access  private
   */
  function Generate($fromSQL=true) {
    // Check for empty array
    if (count($this->contentArr) < 1)
      return;

    // Build table
    while (list($key, $row) = @each($this->contentArr)) { 

      $this->table->NewRow();
      while (list($col, $val) = @each($row)) { 

        if(count($this->cols)==0) {
          $this->table->AddCol($val); 
        } else {
          if(isset($this->cols[$col]))
            $this->table->AddCol($val); 
        } // end of if
      } // end of inner while
    } // end of outer while
  } // end of function


  function DeleteAsk($colnumber,$msg,$module,$action){
     $link = "<a href=\"#\" onclick=\"str = confirm('{$msg}');
	      if(str!='' & str!=null)
	      window.document.location.href='index.php?module=$module&action=$action&id=%value%';\">
	      loeschen</a>";

    $this->table->ReplaceCol($colnumber,$link); 
  } // end of function


  function ReplaceCol($colnumber,$link) {
    $this->table->ReplaceCol($colnumber,$link); 
  } // end of function


  function RowMenu($col, $menu) {
    $this->menu = $menu; 

    switch($menu) {
      case 'special':
        // $this->RowMenuSpecial($col, "personenform");
        break;

      case 'personen':
        $this->RowMenuGeneric($col, "personenform", true, true);
        break;

      case 'formel':
        $this->RowMenuGeneric($col, "formelform", false, true);
        break;

      case 'produkt':
        $this->RowMenuGeneric($col, "produktform", true, true);
        break;
      
      case 'hut':
        $this->RowMenuHut($col, "hutform", true, true);
      break;

      case 'statistikauswertung_ordner':
        $this->RowMenuAuswertung($col, "statistikauswerten");
        break;

      case 'statistikverwaltung_ordner':
        $this->RowMenuVerwaltungOrdner($col, "statistikverwalten");
        break;

      case 'statistikverwaltung_formeln':
        $this->RowMenuVerwaltungFormeln($col, "statistikverwaltenformel");
        break;

      default:
        $this->RowMenuGeneric($col, $menu."form", true);
    } // end of switch

  } // end of function

  /* spalten nummer ( von 1 gezaehlt), dann der if wert also wenn value gleich
     dem aktuellen Wert in der Zelle ist, dann wird der text in der
     Zeile durch replacestring ersetzt. Im replacestring muss
     sich %value% befinden, an der Stelle wird der alte Wert eingefuegt.

     Falls bestimmte Spalten nicht ersetzt werden sollen, koennen diese
     im array dontreplacecols angegeben werden.
  */
      
  function FormatCondition($col, $value, $replacestring, $dontreplacecols=array()) {
    $rows = count($this->table->Table);
    for($i=0;$i<$rows;$i++) {
      
      // check ob der wert in der spalte mit dem $value uebereinstimmt
      // wenn ja ersetze jede zeile auser die aus dem array dontreplacecols
      if($this->table->Table[$i][$col-1]==$value) {
     
        // ersetze spalten auser die in dontreplacecols
        $cols = count($this->table->Table[$i]);

        for($j=0;$j<$cols;$j++) {
          if(!in_array($j+1,$dontreplacecols)) {
           $content = $this->table->Table[$i][$j];
           $this->table->Table[$i][$j] = str_replace("%value%", $content, $replacestring);
          } // end of if
        } // end of for
      } // end of if
    } // end of for
  } // end of function

 /**
  * Formats a table coolumn with SIP specific format options. 
  * Available options are listed below:
  * <ul>
  * <li>numeric: aligns right</li>
  * </ul>
  *
  * @param  Int     $col            Column number for menu
  * @param  String  $sipStyle       use options from available list (see above)
  * 
  * @access	public
  */
  function FormatColumn($col, $sipStyle) {
    switch ($sipStyle){
      case "numeric":
        $cssStyle = "divider alignRight";
        break;

      case "currency":
        $cssStyle = "divider alignRight number";
        break;

       default:
         $cssStyle = "divider";
    } // end of switch

    $this->table->FormatCol($col, $cssStyle);
  } // end of function

 /**
  * Creates a generic menu for each table row.
  * Presets for this generic menu are:
  * <ul>
  * <li>the menu column contains the data records id (primary key)</li>
  * <li>1st column contains the same id as above</li>
  * <li>2nd column contains 0 or 1 as relevant value for activate and deactivate (zustand / active) if param $showActivate is set to true</li>
  * <li>3rd column contains 0 or 1 as relevant value for delete (deletable or not) if param $showDelete is set to true</li>
  * </ul>
  *
  * @param  Int     $col            Column number for menu
  * @param  String  $module         Modulename used to build links (e.g. personenform, hutform, etc.)
  * @param  Bool    $showActivate   Show active/deactivate buttons depending on value in 2nd column
  * @param  Bool    $showDelete     Show delete button if value indicates that (value in 3rd column)
  * 
  * @access	public
  */
  function RowMenuGeneric($col, $module, $showActivate=true, $showDelete=false) {
    $rows = count($this->table->Table);

    for($i=0;$i<$rows;$i++) {
      $cols = count($this->table->Table[$i]);

      for($j=0;$j<$cols;$j++) {

        if($j==($col-1)){
          $id = $this->table->Table[$i][$j];
          $menu = ""; 
          //historie
          $menu .= "<a href=\"index.php?module=".$module."&action=history&id=$id\">"; 
          $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_history.gif\" border=\"0\" alt=\"Historie\">"; 
          $menu .= "</a>&nbsp;"; 

          // bearbeiten wenn aktiv
          if ($this->table->Table[$i][1] != 0) {
            $menu .= "<a href=\"index.php?module=".$module."&action=edit&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_edit.gif\" border=\"0\" alt=\"bearbeiten\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if

          // aktivieren wenn inaktiv, sonst deaktivieren
          if (($this->table->Table[$i][1] == 0) && ($showActivate)){
            $menu .= "<a href=\"index.php?module=".$module."&action=activate&id=$id&order=[ORDER]\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_reactivate.gif\" border=\"0\" alt=\"reaktivieren\">"; 
            $menu .= "</a>&nbsp;"; 
          } else {
            if($showActivate) {
              $menu .= "<a href=\"index.php?module=".$module."&action=deactivate&id=$id&order=[ORDER]\">"; 
              $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_deactivate.gif\" border=\"0\" alt=\"deaktivieren\">"; 
              $menu .= "</a>&nbsp;"; 
            } // end of if
          } // end of if - anzeige von deaktivieren ODER aktivieren

          // l�sch button einblenden wenn delete kriterium 1
          if ($showDelete) {
            // print_r($this->table->Table);
            if ($this->table->Table[$i][2] == 1) {
              $menu .= "<a href=\"index.php?module=".$module."&action=delete&id=$id\">"; 
              $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_delete.gif\" border=\"0\" alt=\"l&ouml;schen\">"; 
              $menu .= "</a>&nbsp;"; 
            } // end of if - anzeige des l�schen buttons oder nicht nach wert
          } // end of if - anzeige von l�schen oder nicht nach funktionsparmeter

          // wenn noch nicht verlinkt
          $this->table->Table[$i][$j] = $menu;

        } // end of outer if
      } // end of inner for
    } // end pof outer for
  } // end of function




 /**
  * Creates a specific form for Hut 
  *
  * @param  Int     $col            Column number for menu
  * @param  String  $module         Modulename used to build links (e.g. personenform, hutform, etc.)
  * 
  * @access	public
  */
  function RowMenuHut($col, $module, $showActivate=true, $showDelete=false) {
    $rows = count($this->table->Table);

    for($i=0;$i<$rows;$i++) {
      $cols = count($this->table->Table[$i]);

      for($j=0;$j<$cols;$j++) {

        if($j==($col-1)){
          $id = $this->table->Table[$i][$j];
          $menu = ""; 
          //historie
          $menu .= "<a href=\"index.php?module=".$module."&action=history&id=$id\">"; 
          $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_history.gif\" border=\"0\" alt=\"Historie\">"; 
          $menu .= "</a>&nbsp;"; 

          // bearbeiten wenn aktiv
          if ($this->table->Table[$i][1] != 0) {
            $menu .= "<a href=\"index.php?module=".$module."&action=edit&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_edit.gif\" border=\"0\" alt=\"bearbeiten\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if

          // aktivieren wenn inaktiv, sonst deaktivieren
          if (($this->table->Table[$i][1] == 0) && ($showActivate)){
            $menu .= "<a href=\"index.php?module=".$module."&action=activate&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_reactivate.gif\" border=\"0\" alt=\"reaktivieren\">"; 
            $menu .= "</a>&nbsp;"; 
          } else {
            if($showActivate) {
              if (strlen($this->table->Table[$i][8]) == 51) {
                $menu .= "<a href=\"index.php?module=".$module."&action=deactivate&id=$id\">"; 
                $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_deactivate.gif\" border=\"0\" alt=\"deaktivieren\">"; 
                $menu .= "</a>&nbsp;"; 
	            } // end of if
            } // end of if
          } // end of if - anzeige von deaktivieren ODER aktivieren

          // l�sch button einblenden wenn delete kriterium 1
          if ($showDelete) {
            // print_r($this->table->Table);
            if ($this->table->Table[$i][2] == 1) {
              $menu .= "<a href=\"index.php?module=".$module."&action=delete&id=$id\">"; 
              $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_delete.gif\" border=\"0\" alt=\"l&ouml;schen\">"; 
              $menu .= "</a>&nbsp;"; 
            } // end of if - anzeige des l�schen buttons oder nicht nach wert
          } // end of if - anzeige von l�schen oder nicht nach funktionsparmeter

          // wenn noch nicht verlinkt
          $this->table->Table[$i][$j] = $menu;

        } // end of outer if
      } // end of inner for
    } // end pof outer for
  } // end of function


/**
  * Creates a specific form for OIC Module Statistik Auswertung / Ordner�bersicht
  *
  * @param  Int     $col            Column number for menu
  * @param  String  $module         Modulename used to build links (e.g. personenform, hutform, etc.)
  * 
  * @access	public
  */
  function RowMenuAuswertung($col, $module) {
    $rows = count($this->table->Table);

    for($i=0;$i<$rows;$i++) {
      $cols = count($this->table->Table[$i]);

      for($j=0;$j<$cols;$j++) {

        if($j==($col-1)){
          $id = $this->table->Table[$i][$j];
          $menu = ""; 

          if ($this->table->Table[$i][3] != 0) {
            // Detailansicht
            $menu .= "<a href=\"index.php?module=".$module."&action=showordner&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_view.gif\" border=\"0\" alt=\"Detailansicht\">"; 
            $menu .= "</a>&nbsp;"; 

            // Graph
            $menu .= "<a href=\"#\" onclick=\"GesamtGraphOrdnerID($id)\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_statgraph.gif\" border=\"0\" alt=\"Graph\">"; 
            $menu .= "</a>&nbsp;"; 

          } else {
            $menu .= "keine Formeln vorhanden";
          } // end of if - nicht anzeigen wenn keine Formeln im Ordner sind

          // wenn noch nicht verlinkt
          $this->table->Table[$i][$j] = $menu;

        } // end of outer if
      } // end of inner for
    } // end pof outer for
  } // end of function


/**
  * Creates a specific form for OIC Module Statistik Verwaltung / Ordner�bersicht
  *
  * @param  Int     $col            Column number for menu
  * @param  String  $module         Modulename used to build links (e.g. personenform, hutform, etc.)
  * 
  * @access	public
  */
  function RowMenuVerwaltungOrdner($col, $module) {
    $rows = count($this->table->Table);

    for($i=0; $i<$rows; $i++) {
      $cols = count($this->table->Table[$i]);

      for($j=0; $j<$cols; $j++) {

        if($j==($col-1)){
          $id = $this->table->Table[$i][$j];
          $menu = ""; 

          // Edit
          $menu .= "<a href=\"index.php?module=".$module."&action=edit&id=$id\">"; 
          $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_edit.gif\" border=\"0\" alt=\"Ordner bearbeiten\">"; 
          $menu .= "</a>&nbsp;"; 

          // Formeln bearbeiten
          $menu .= "<a href=\"index.php?module=".$module."formel&action=list&id=$id\">"; 
          $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_formeln.gif\" border=\"0\" alt=\"Formeln bearbeiten\">"; 
          $menu .= "</a>&nbsp;"; 

          // Nach unten (wenn nicht letztes in Liste)
          if ($i != $rows-1) {
            $menu .= "<a href=\"index.php?module=".$module."&action=movedown&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_down.gif\" border=\"0\" alt=\"Nach unten\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if

          // Nach oben (wenn nicht erstes in Liste)
          if ($i != 0) {
            $menu .= "<a href=\"index.php?module=".$module."&action=moveup&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_up.gif\" border=\"0\" alt=\"Nach oben\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if

          // Delete - Wenn in Spalte 3 eine 0 steht (keine Formeln mehr im Ordner)
          if ($this->table->Table[$i][3] == 0) {
            $menu .= "<a href=\"index.php?module=".$module."&action=delete&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_delete.gif\" border=\"0\" alt=\"l&ouml;schen\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if - anzeige des l�schen buttons oder nicht nach wert

          // wenn noch nicht verlinkt
          $this->table->Table[$i][$j] = $menu;

        } // end of outer if
      } // end of inner for
    } // end pof outer for
  } // end of function

/**
  * Creates a specific form for OIC Module Statistik Verwaltung / Formel�bersicht innerhalb eines Ordners
  *
  * @param  Int     $col            Column number for menu
  * @param  String  $module         Modulename used to build links (e.g. personenform, hutform, etc.)
  * 
  * @access	public
  */
  function RowMenuVerwaltungFormeln($col, $module) {
    $rows = count($this->table->Table);

    if ($rows == 0) {
      $this->table->Table[] = Array("", "", "", "Bisher existiert kein Eintrag in dieser Liste.", "", "", "", "-1");
      $rows = count($this->table->Table);
    } // end of if

    for($i=0; $i<$rows; $i++) {
      $cols = count($this->table->Table[$i]);

      for($j=0; $j<$cols; $j++) {

        if($j==($col-1)){
          $id = $this->table->Table[$i][$j];
          $menu = ""; 

          // Ansicht (1 oder 1/4)
          if ($this->table->Table[$i][1] == 4 && $id != -1) {
            $menu .= "<a href=\"index.php?module=".$module."&action=viewfull&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_ansicht_1_1.gif\" border=\"0\" alt=\"Ansicht Vollformat\">"; 
            $menu .= "</a>&nbsp;"; 
          } else if ($this->table->Table[$i][0] != "") {
            $menu .= "<a href=\"index.php?module=".$module."&action=viewquarter&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_ansicht_1_4.gif\" border=\"0\" alt=\"Ansicht Viertelformat\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if - Ansicht 1 oder 1/4

          // Delete - Wenn in Spalte 3 eine 0 steht (keine Formeln mehr im Ordner)
	  //echo $this->table->Table[$i][1]."<br>";
          //if ($this->table->Table[$i][1] == 0 && $id != -1) {
          if ($id != -1) {
            $menu .= "<a href=\"index.php?module=".$module."&action=delete&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_delete.gif\" border=\"0\" alt=\"l&ouml;schen\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if - anzeige des l�schen buttons oder nicht nach wert

          // Formeln hinzuf�gen
          if ($id != -1) {
            $menu .= "<a href=\"index.php?module=".$module."&action=addafter&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_add.gif\" border=\"0\" alt=\"Nach diesem hinzuf�gen\">"; 
            $menu .= "</a>&nbsp;"; 
          } else {
            $menu .= "<a href=\"index.php?module=".$module."&action=add&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_add.gif\" border=\"0\" alt=\"Formel hinzuf�gen\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if

          // Nach unten (wenn nicht letztes in Liste)
          if ($i != $rows-1 && $id != -1) {
            $menu .= "<a href=\"index.php?module=".$module."&action=movedown&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_down.gif\" border=\"0\" alt=\"Nach unten\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if

          // Nach oben (wenn nicht erstes in Liste)
          if ($i != 0 && $id != -1) {
            $menu .= "<a href=\"index.php?module=".$module."&action=moveup&id=$id\">"; 
            $menu .= "<img src=\"./sip/themes/[THEME]/images/buttons/bu_up.gif\" border=\"0\" alt=\"Nach oben\">"; 
            $menu .= "</a>&nbsp;"; 
          } // end of if

          // wenn noch nicht verlinkt
          $this->table->Table[$i][$j] = $menu;

        } // end of outer if
      } // end of inner for
    } // end pof outer for

/*
    <tr class="odd">
      <td class="divider alignRight">1</td>
      <td class="divider"><a href="#">Abgeschlossene Vertr&auml;ge </a></td>
      <td class="divider">F3</td>
      <td class="divider">Pkt/Stk</td>
      <td class="divider">1</td>
      <td><a href=""><img src="images/buttons/bu_ansicht_1_4.gif" alt="Ansicht 1/4" width="18" height="18" class="icon" title="Ansicht 1/4" /></a> <a href=""><img src="images/buttons/bu_delete.gif" alt="l&ouml;schen" class="icon" title="l&ouml;schen" /></a> <a href=""><img src="images/buttons/bu_add.gif" alt="nach dieser hinzuf&uuml;gen" class="icon" title="nach dieser hinzuf&uuml;gen" /></a> <a href=""><img src="images/buttons/bu_down.gif" alt="nach unten" class="icon" title="nach unten" /></a> </td>
    </tr>
*/

  } // end of function


 /**
  * Shows the siptable
  *
  * @access	public
  */
  function Show() {
    $this->html = "";
    if($this->searchform)
      $this->html .= $this->SortAndSearchForm();

    //$this->html .= $this->table->Get("&nbsp;","");
    $this->html .= $this->table->Get("&nbsp;",$this->headingtop);
    $this->app->Tpl->Add($this->parsetarget, $this->html);
  } // end of function


  function RowLimit($number) {
    $this->rowlimit=$number; 
  }


  function Cols($fields)
  {
    $this->cols=array_flip($fields); 
  }

  function HideCol($number)
  {
    $this->table->HideCol($number);
  }

  function HeadingTop($value)
  {
	$this->headingtop=$value;

  }

  function Headings($descriptions)
  {
    $this->table->AddRowAsHeading($descriptions);
    $this->descriptions=$descriptions;
  }

  function SetSortAndSearchForm($bool)
  {
    $this->searchform=$bool; 
  }

  function SetSortHeading($bool)
  {
    $this->sortheading=$bool; 
  }


  function SortAndSearchForm()
  {
    $select = new HTMLSelect("sort",1);
    if(count($this->cols)==0)
    {

    }
    else 
    {
      while (list($col, $val) = @each($this->cols))
      {
	if($this->descriptions[$col]!="")
	  $select->AddOption("Nach {$this->descriptions[$col]} Sortieren",$col);
	else
	  $select->AddOption("Nach $col Sortieren",$col);
	
      }
    }
    $html = $select->Get();

    $search = new HTMLInput("search","text","",20);
    $html .= $search->Get();

    $html .="<input type=\"submit\" value=\"Suchen\">";

    $html .="<br>";

    $alphabet = range('A', 'Z');
    $html .="<table width=\"100%\" cellpadding=\"7\"><tr>";
    foreach ($alphabet as $letter) 
      $html .= "<td><a href=\"\">$letter</a></td>";

    $html .="</tr></table>";

    
    return $html;
  }

} // end of class

?>
