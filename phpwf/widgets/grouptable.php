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


class GroupTable{
  
  var $app;

  var $rows;
  var $dataset;
  var $headings;
  var $sourceitemsdataset;
  var $sourceitemsheadings;

  function __construct(&$app) 
  {
    $this->app = $app;
  }

  function DatabaseTable($table)
  {

  }

  function Group($groupid)
  {

  }


  function Search($searchwidget)
  {


  }

  function Table(&$listwidget)
  {
  }

  function SourceItems($sql)
  {
    $this->sourceitemsdatasets = $this->app->DB->SelectArr($sql);
    foreach($this->sourceitemsdatasets[0] as $colkey=>$value)
      $this->sourceitemsheadings[]=ucfirst($colkey);
    //$this->sourceitemsheadings[count($this->sourceitemsheadings)-1] = 'Aktion';
  }


  function DynamicTable($sql)
  {
    
    //$sql.= " LIMIT 1,10";
    $sql.= " order by position";
    $this->datasets = $this->app->DB->SelectArr($sql);
    foreach($this->datasets[0] as $colkey=>$value)
      $this->headings[]=ucfirst($colkey);

    $this->headings[count($this->headings)-1] = 'Aktion';
  }

  function GetTableWithArrows()
  {
    $htmltable = new HTMLTable(0,"100%","",3,1);
    $htmltable->AddRowAsHeading($this->headings);

    $htmltable->ChangingRowColors('#FFf1c5','#FFcc99');

    foreach($this->datasets as $row){
      $htmltable->NewRow();
      foreach($row as $field)
	$htmltable->AddCol($field);
    }
    
    $module = $this->app->Secure->GetGET("module");
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");

    $htmltable->ReplaceCol(count($this->headings),
      "<a href=\"index.php?module=$module&action=$action&eventid=%value%&event=del&id=$id\">del</a>
      &nbsp;<a href=\"index.php?module=$module&action=$action&eventid=%value%&event=new&id=$id\">new</a>
      &nbsp;<a href=\"index.php?module=$module&action=$action&eventid=%value%&event=up&id=$id\">nauf</a>
      &nbsp;<a href=\"index.php?module=$module&action=$action&eventid=%value%&event=down&id=$id\">nunda</a>
      ");
    
    return $htmltable->Get();
  }

  function Step($step,$widget,$method,$parsetarget)
  {
    $mywidget = $this->app->Widget->Get($widget,$parsetarget);
    $mywidget->$method();
  }

  function SaveItem($widget,$method,$formvar)
  {


  }

  function SaveValue($widget,$method,$formvar)
  {


  }



  function Execute($verknuepftabelle,$hauptpunkttabelle,$hauptpunkttabelleid,
    $untertabelle,$parsetarget=PAGE)
  {
    $event = $this->app->Secure->GetGET("event");
    $eventid = $this->app->Secure->GetGET("eventid");
    $module = $this->app->Secure->GetGET("module");
    $action = $this->app->Secure->GetGET("action");

    $id = $this->app->Secure->GetGET("id");

    switch($event) {
      case "new":
	//echo "nach $id und item $eventid";
	$eventnewitem = $this->app->Secure->GetPOST("eventnewitem");
	if(is_numeric($eventnewitem))
	{
	  /* speichern */
	  //wenn nichts dann als erstes
	  $this->app->DB->Insert("INSERT INTO $verknuepftabelle (id,$hauptpunkttabelle,
	    $untertabelle,position) VALUES ('','$hauptpunkttabelleid','$eventnewitem',1)");
	  //echo $eventnewitem." ".$id;
	  //sonst alles nachrutschen

	  /* redirect auf module und action + id */
	  header("Location: index.php?module=$module&action=$action&id=$id");

	} else
	{
	  $htmltable = new HTMLTable(0,"100%","",3,1);
	  $htmltable->AddRowAsHeading($this->sourceitemsheadings);

	  $htmltable->ChangingRowColors('#FFf1c5','#FFcc99');

	  foreach($this->sourceitemsdatasets as $row){
	    $htmltable->NewRow();
	    foreach($row as $field)
	      $htmltable->AddCol($field);
	  }
	  $htmltable->ReplaceCol(1,
	      "<input type=\"radio\" name=\"eventnewitem\" value=\"%value%\">");
   
	  /* anzeige */
	  $this->app->Tpl->Set($parsetarget,$htmltable->Get());
	}
      break;
  
      case "del":
	echo "hau ab!";
	$this->app->Tpl->Set($parsetarget,$this->GetTableWithArrows());
      break;

      case "up":
	echo "nauf";
	$this->app->Tpl->Set($parsetarget,$this->GetTableWithArrows());
      break;

      case "down":
	echo "nunda";
	$this->app->Tpl->Set($parsetarget,$this->GetTableWithArrows());
      break;

      default:
	// entweder fertige tabelle mit pfeilen
	if(count($this->datasets[0])>0)
	  $this->app->Tpl->Set($parsetarget,$this->GetTableWithArrows());
	else
	  $this->app->Tpl->Set($parsetarget,
	    "Keine Datens&auml;tze vorhanden -> Datensatz 
	      <a href=\"index.php?module=$module&action=$action&event=new&id=$id\">
		einf&uuml;gen</a>.");
	//$this->app->Tpl->Parse($parsetarget,"dialog.tpl");
    }
      
    // oder suche + tabelle mit radiobuttons
    
  }

}


?>
