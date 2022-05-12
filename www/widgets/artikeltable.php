<?php


class ArtikelTabelle{
  
  var $app;

  var $rows;
  var $dataset;
  var $headings;
  var $sourceitemsdataset;
  var $sourceitemsheadings;

  var $id;
  var $table;

  function __construct($app) 
  {
    $this->app = $app;
  }

  function Auftrag($id)
  {
    $this->table="auftrag";
    $this->id = $id;


    // update kompletter Tabelle pruefen
    if($this->app->Secure->GetPOST("komplettsubmit")=="1"){
      $nummer = $this->app->Secure->GetPOST("nummer");
      $menge= $this->app->Secure->GetPOST("menge");
      $netto= $this->app->Secure->GetPOST("netto");

      foreach($nummer as $key=>$value)
      {
	$netto[$key] = str_replace(',','.',$netto[$key]);
        $sql ="UPDATE auftrag_artikel SET menge='{$menge[$key]}',
          preis='{$netto[$key]}' WHERE auftrag='{$this->id}' 
          and id='{$key}' LIMIT 1;";
	$this->app->DB->Update($sql);
      }
    }

  }


  function ShowEdit($page)
  {


  }


  function Quelltabelle($sql)
  {
    $this->sourceitemsdatasets = $this->app->DB->SelectArr($sql);
    foreach($this->sourceitemsdatasets[0] as $colkey=>$value)
      $this->sourceitemsheadings[]=ucfirst($colkey);
    //$this->sourceitemsheadings[count($this->sourceitemsheadings)-1] = 'Aktion';
  }


  function Haupttabelle($sql)
  {
    
    //$sql.= " LIMIT 1,10";
    $sql.= " order by position";
    $this->datasets = $this->app->DB->SelectArr($sql);
    foreach($this->datasets[0] as $colkey=>$value)
      $this->headings[]=ucfirst($colkey);

    $this->headings[count($this->headings)-1] = 'Aktion';
  }

  function BearbeitenTabelle()
  {
    $htmltable = new HTMLTable(0,"100%","",3,1);
    $htmltable->AddRowAsHeading($this->headings);

    $htmltable->ChangingRowColors('#FFf1c5','#FFcc99');

    $summe = 0;
    foreach($this->datasets as $row){
      $htmltable->NewRow();
      foreach($row as $key=>$field)
	{
	  if($key=="nummer"|| $key=="name_de" || $key=="pos")
	    $htmltable->AddCol($field."<input type=\"hidden\" name=\"{$key}[{$row[id]}]\" value=\"$field\">");
	  elseif($key=="id")
	    $htmltable->AddCol($field);
	  elseif($key=="summe")
	  {
	    $artikelsumme = $row[menge]*$row[netto];
	    $htmltable->AddCol($artikelsumme*$this->app->erp->GetAuftragSteuersatz($this->id));
	    $summe = $summe + $artikelsumme;
	  }
	  else
	    $htmltable->AddCol("<input type=\"text\" name=\"{$key}[{$row[id]}]\" size=\"5\" value=\"$field\">");


	}
    }
    $htmltable->NewRow();
    $htmltable->AddCol("");
    $htmltable->AddCol("");
    $htmltable->AddCol("");
    $htmltable->AddCol("");
    $htmltable->AddCol("");
    $htmltable->AddCol("");
    $htmltable->AddCol($summe*$this->app->erp->GetAuftragSteuersatz($this->id));
    
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
	  $auftragsid = $hauptpunkttabelleid;
	  $kunde = $this->app->DB->Select("SELECT kundeadressid	FROM auftrag WHERE id='$auftragsid' LIMIT 1");
	  $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$auftragsid' LIMIT 1");
	  $artikel = $eventnewitem;
	  $menge = 1;
	  $preis = $this->app->erp->GetArtikelPreisvorlageProjekt($kunde,$projekt,$artikel,$menge);

	  //wenn nichts dann als erstes
	  $this->app->DB->Insert("INSERT INTO $verknuepftabelle (id,$hauptpunkttabelle,
	    $untertabelle,position,menge,preis) VALUES ('','$hauptpunkttabelleid','$eventnewitem',1,1,$preis)");
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
	$this->app->Tpl->Set($parsetarget,$this->BearbeitenTabelle());
      break;

      case "up":
	echo "nauf";
	$this->app->Tpl->Set($parsetarget,$this->BearbeitenTabelle());
      break;

      case "down":
	echo "nunda";
	$this->app->Tpl->Set($parsetarget,$this->BearbeitenTabelle());
      break;

      default:
	// entweder fertige tabelle mit pfeilen
	if(count($this->datasets[0])>0)
	{
	  $this->app->Tpl->Set($parsetarget,$this->BearbeitenTabelle());
	  $this->app->Tpl->Add($parsetarget,"<input type=\"hidden\" name=\"komplettsubmit\" value=\"1\">");
	}
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
