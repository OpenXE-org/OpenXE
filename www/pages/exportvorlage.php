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
include '_gen/exportvorlage.php';

class Exportvorlage extends GenExportvorlage {
  /** @var Application $app */
  var $app;

  /**
   * Exportvorlage constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app,$intern=false)
  {
    $this->app=$app;
    if($intern==true) {
      return;
    }
    //parent::GenExportvorlage($app);

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ExportvorlageCreate");
    $this->app->ActionHandler("edit","ExportvorlageEdit");
    $this->app->ActionHandler("export","ExportvorlageExport");
    $this->app->ActionHandler("list","ExportvorlageList");
    $this->app->ActionHandler("delete","ExportvorlageDelete");
    $this->app->ActionHandler("adressen","ExportvorlageAdressen");
    $this->app->ActionHandler("adresseedit","ExportvorlageAdresseEdit");

    $this->app->ActionHandlerListen($app);

    $this->app->erp->Headlines('Daten Export');
  }

  function ExportvorlageAdresseEdit()
  {
    $this->app->Tpl->Parse('TAB1',"exportvorlage_uebersicht.tpl");
    $this->app->Tpl->Set('TABTEXT',"Export");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }

  function ExportvorlageAdressen()
  {
    //    $this->app->Tpl->Parse(TAB1,"exportvorlage_adressen.tpl");
    $this->app->YUI->TableSearch('TAB1',"adresse_export");
    $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=uebersicht","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->Tpl->Set('TABTEXT',"Export");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }



  function ExportvorlageCreate()
  {
    $this->ExportvorlageMenu();
    parent::ExportvorlageCreate();
  }

  function ExportvorlageDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM exportvorlage WHERE id='$id'");
    }
    $this->ExportvorlageList();
  }


  function ExportvorlageList()
  {
    $this->ExportvorlageMenu();
    if($this->app->DB->Select("SELECT COUNT(id) FROM exportvorlage") <=0)
    {
      $this->app->DB->Insert("INSERT INTO `exportvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterexport`, `mitarbeiterletzterexport`, `exporttrennzeichen`, `exporterstezeilenummer`, `exportdatenmaskierung`, `exportzeichensatz`) VALUES
          ('', 'Standard Artikel Export (Format siehe Wiki)', 'nummer;\r\nname_de;\r\nname_en;\r\nbeschreibung_de;\r\nbeschreibung_en;\r\nkurztext_de;\r\nkurztext_en;\r\ninternerkommentar;\r\nhersteller;\r\nherstellernummer;\r\nherstellerlink;\r\nean;', '', 'artikel', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '');");
    }


    parent::ExportvorlageList();
  }

  function ExportvorlageMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM exportvorlage WHERE id='$id' LIMIT 1");

    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',$bezeichnung);

    if($this->app->Secure->GetGET("action")=="list")
    {
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=create","Neue Exportvorlage anlegen");
      $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=uebersicht","Zur&uuml;ck zur &Uuml;bersicht");
    }
    else
    {
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=edit&id=$id","Details");
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=export&id=$id","Export starten: CSV Datei herunterladen");
      //Excel herunterladen hat nicht funktioniert, stattdessen kam eine CSV, wurde wegen Ticket 127034 erstmal rausgenommen
      //$this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=export&format=xls&id=$id","Export starten: Excel Datei herunterladen");
    }
  }


  function ExportvorlageEdit()
  {
    $this->ExportvorlageMenu();
    parent::ExportvorlageEdit();
  }

  function ExportvorlageGetFields($id)
  {
    $fields = $this->app->DB->Select("SELECT fields FROM exportvorlage WHERE id='$id' LIMIT 1");

    $fields = nl2br($fields);
    $fields = str_replace('<br />',';',$fields);
    $fields = str_replace(';;',';',$fields);

    $fieldsarray = explode(';',$fields);
    for($i=0;$i<count($fieldsarray);$i++)
    {
      $fieldsarray_items = explode(':',$fieldsarray[$i]);
      foreach($fieldsarray_items as $k => $v)$fieldsarray_items[$k] = trim($v);
      if($fieldsarray_items[1]!=""){
        $csv_fields[$fieldsarray_items[0]]= $fieldsarray_items[1];
        $csv_fields_keys[] = $fieldsarray_items[0];
      }
    }         
    return $csv_fields;
  }

  function ExportvorlageExport($internal=false,$id="",$filter=array(), $parameter = null)
  {
    //$output = "";
    $xls = false;
    if($internal!=true)
    {
      $id = $this->app->Secure->GetGET('id');

      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=edit&id=$id","Details");
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=export&id=$id","Export Einstellungen f&uuml;r aktuellen Export anpassen");
      $exportvorlageArr = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM exportvorlage WHERE id = %d LIMIT 1',
          (int)$id
        )
      );
      if($this->app->Secure->GetGET('format') === 'xls') {
        $xls = true;
      }
      //$bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM exportvorlage WHERE id='$id' LIMIT 1");
      $exporttrennzeichen = $exportvorlageArr['exporttrennzeichen'];
      $exporterstezeilenummer = $exportvorlageArr['exporterstezeilenummer'];
      $exportdatenmaskierung = $exportvorlageArr['exportdatenmaskierung'];
      //$exportzeichensatz = $this->app->DB->Select("SELECT exportzeichensatz FROM exportvorlage WHERE id='$id' LIMIT 1");
      $fields = $exportvorlageArr['fields'];
      $fields_where = $exportvorlageArr['fields_where'];
      $ziel = $exportvorlageArr['ziel'];

      $filterdatum = $exportvorlageArr['filterdatum'];
      $filterprojekt = $exportvorlageArr['filterprojekt'];
      $submit = $this->app->Secure->GetPOST('submit');
      if(($filterdatum=='1' || $filterprojekt=='1') && $submit=='')
      {
        $this->app->YUI->DatePicker('datum_von');
        $this->app->YUI->DatePicker('datum_bis');
        $this->app->YUI->AutoComplete('projekt','projektname',1);

        $this->app->Tpl->Parse('PAGE','exportvorlage_filter.tpl');
        return '';
      }
    }elseif($parameter && $internal && !$id)
    {
      if(!empty($parameter['format']) && $parameter['format'] === 'xls') {
        $xls = true;
      }
      
      //$bezeichnung = isset($parameter['bezeichnung'])?$parameter['bezeichnung']:'';
      $exporttrennzeichen = isset($parameter['exporttrennzeichen'])?$parameter['exporttrennzeichen']:'semikolon';
      $exporterstezeilenummer = isset($parameter['exporterstezeilenummer'])?$parameter['exporterstezeilenummer']:1;
      $exportdatenmaskierung = isset($parameter['exportdatenmaskierung'])?$parameter['exportdatenmaskierung']:'gaensefuesschen';
      //$exportzeichensatz = isset($parameter['exportzeichensatz'])?$parameter['exportzeichensatz']:'';
      $fields = isset($parameter['fields'])?$parameter['fields']:'nummer;
name_de;
name_en;
beschreibung_de;
beschreibung_en;
kurztext_de;
kurztext_en;
internerkommentar;
hersteller;
herstellernummer;
herstellerlink;
ean;';
      $fields_where = isset($parameter['fields_where'])?$parameter['fields_where']:'';
      $ziel = isset($parameter['ziel'])?$parameter['ziel']:'artikel';

      $filterdatum = isset($parameter['filterdatum'])?$parameter['filterdatum']:'';
      $filterprojekt = isset($parameter['filterprojekt'])?$parameter['filterprojekt']:'';
    }elseif($internal && $id){
      $exportvorlageArr = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM exportvorlage WHERE id = %d LIMIT 1',
          (int)$id
        )
      );
      if($this->app->Secure->GetGET('format') === 'xls') {
        $xls = true;
      }
      $exporttrennzeichen = $exportvorlageArr['exporttrennzeichen'];
      $exporterstezeilenummer = $exportvorlageArr['exporterstezeilenummer'];
      $exportdatenmaskierung = $exportvorlageArr['exportdatenmaskierung'];
      $fields = $exportvorlageArr['fields'];
      $fields_where = $exportvorlageArr['fields_where'];
      $ziel = $exportvorlageArr['ziel'];
    }



    $fields = nl2br($fields);
    $fields = str_replace('<br />',';',$fields);
    $fields = str_replace(';;',';',$fields);
    $fieldsarray = explode(';',$fields);

    list($fields_array, $joins) = $this->getFieldSqlCols($fieldsarray, $ziel);
    $join = implode( ' ', $joins);
    $subwhere = '';
    $this->app->erp->RunHook('exportvorlage_export',4,$ziel,$fields_array, $join, $subwhere);
    //for($i=0;$i<count($fieldsarray);$i++)


    $sql_fields = implode(',',$fields_array);
    $sql_fields = trim($sql_fields);
    $sql_fields = rtrim($sql_fields,',');

    $fields_where = str_replace('&apos;',"'",$fields_where);

    $fieldsarray = explode(';',$fields_where);
    foreach($fieldsarray as $fieldarray) {
      $fields_array_where[] = $fieldarray;
    }

    if($internal==true)
    {
      $datum_von = isset($filter['von'])?$filter['von']:'';
      $datum_bis = isset($filter['bis'])?$filter['bis']:'';
      $projekt = isset($filter['projekt'])?$filter['projekt']:'';
    } else {
      $datum_von = $this->app->Secure->GetPOST('datum_von');
      $datum_bis = $this->app->Secure->GetPOST('datum_bis');
      $projekt = $this->app->Secure->GetPOST('projekt');
    }

    if($ziel==="angebot_position" || $ziel==="auftrag_position" || $ziel==="rechnung_position" || $ziel==="gutschrift_position" || $ziel==="lieferschein_position" || $ziel==="bestellung_position")
    {
      if($datum_von !="")
      {
        $datum_von = $this->app->String->Convert($datum_von,"%1.%2.%3","%3-%2-%1");
        $fields_array_where[] = "b.datum >='$datum_von'";
      }

      if($datum_bis !="")
      {
        $datum_bis = $this->app->String->Convert($datum_bis,"%1.%2.%3","%3-%2-%1");
        $fields_array_where[] = "b.datum <='$datum_bis'";
      }

      if($projekt !="")
      {
        $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $fields_array_where[] = "bp.projekt =$projektid";
      }
    }
    else {
      if($datum_von !="")
      {
        $datum_von = $this->app->String->Convert($datum_von,"%1.%2.%3","%3-%2-%1");
        $fields_array_where[] = "datum >='$datum_von'";
      }

      if($datum_bis !="")
      {
        $datum_bis = $this->app->String->Convert($datum_bis,"%1.%2.%3","%3-%2-%1");
        $fields_array_where[] = "datum <='$datum_bis'";
      }

      if($projekt !="")
      {
        $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $fields_array_where[] = "projekt =$projektid";
      }
    }


    $fields_array_where = array_filter($fields_array_where);
    $sql_fields_where = implode(' AND ',$fields_array_where);
    $sql_fields_where = rtrim($sql_fields_where,'AND');


    $sql_fields_where = ltrim($sql_fields_where,' AND');


    switch($ziel)
    {
      case "artikel":
        $sql = "SELECT $sql_fields, art.id as systemid FROM artikel AS art ";
        break;
      case "ansprechpartner":
        $sql = "SELECT $sql_fields FROM ansprechpartner AS anspr ";
        break;
      case "adresse":
        $sql = "SELECT $sql_fields,adr.id as systemid FROM adresse AS adr ";
        break;
      case "angebot":
        $sql = "SELECT $sql_fields FROM angebot AS b ";
        break;
      case "auftrag":
        $sql = "SELECT $sql_fields FROM auftrag AS b ";
        break;
      case "rechnung":
        $sql = "SELECT $sql_fields,b.id as systemid FROM rechnung AS b ";
        break;
      case "lieferschein":
        $sql = "SELECT $sql_fields FROM lieferschein AS b ";
        break;
      case "bestellung":
        $sql = "SELECT $sql_fields FROM bestellung AS b ";
        break;
      case "gutschrift":
        $sql = "SELECT $sql_fields,b.id as systemid FROM gutschrift AS b ";
        break;
      case "angebot_position":
        $sql = "SELECT $sql_fields,bp.id as systemid FROM angebot_position bp LEFT JOIN angebot b ON b.id=bp.angebot";
        break;
      case "auftrag_position":
        $sql = "SELECT $sql_fields,bp.id as systemid FROM auftrag_position bp LEFT JOIN auftrag b ON b.id=bp.auftrag";
        break;
      case "rechnung_position":
        $sql = "SELECT $sql_fields,bp.id as systemid FROM rechnung_position bp LEFT JOIN rechnung b ON b.id=bp.rechnung";
        break;
      case "gutschrift_position":
        $sql = "SELECT $sql_fields,-bp.preis as preis_negativ, bp.id as systemid FROM gutschrift_position bp LEFT JOIN gutschrift b ON b.id=bp.gutschrift";
        break;
      case "lieferschein_position":
        $sql = "SELECT $sql_fields,bp.id as systemid FROM lieferschein_position bp LEFT JOIN lieferschein b ON b.id=bp.lieferschein";
        break;
      case "bestellung_position":
        $sql = "SELECT $sql_fields,bp.id as systemid FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung";
        break;

    }

    $sql .= ' '.$join. ' ';

    if(count($fields_array_where) > 0 && trim($sql_fields_where)!=''){
      $sql .= ' WHERE ' . trim($sql_fields_where).' '.$subwhere ;
    } elseif(!empty($subwhere)) {
      $sql .= ' WHERE '.$subwhere;
    }

    if($exporttrennzeichen==='semikolon') {
      $exporttrennzeichen=';';
    }elseif($exporttrennzeichen==='komma') {
      $exporttrennzeichen=',';
    }
    if($exportdatenmaskierung==='gaensefuesschen') {
      $exportdatenmaskierung='"';
    }
    else if($exportdatenmaskierung==='hochkomma') {
      $exportdatenmaskierung="'";
    }
    else {
      $exportdatenmaskierung='';
    }

    $memory_limit = @ini_get('memory_limit');
    $max_execution_time = @ini_get('max_execution_time');
    if($memory_limit)
    {
      if(strpos($memory_limit, 'M') !== false)
      {
        $memory_limit = str_replace('M','', $memory_limit);
        $memory_limit *= 1024*1024;
      }
    }
    if(class_exists('DevTools')) {
      DevTools::$aktiv = false;
    }
    return $this->generateExport($xls, $sql, $exporterstezeilenummer, $exportdatenmaskierung,$exporttrennzeichen,$ziel,$internal,$memory_limit,$max_execution_time);
  }

  public function getFieldSqlCols($fieldsarray, $ziel)
  {
    $fields_array = [];
    $joins = [];

    $artikeleigenscahftenJoinen = false;
    $verkaufspreiseJoinen = false;

    foreach($fieldsarray as $fieldarray) {
      switch(trim($fieldarray))
      {
        case 'verkaufspreisnetto':
          $fields_array[] = "'VAR:VERKAUFSPREISNETTO' as verkaufspreisnetto";
          break;
        case strpos($fieldarray, 'verkaufspreisnetto') !== false || strpos($fieldarray,'verkaufspreispreisfuermenge') !== false ||
          strpos($fieldarray, 'verkaufspreismenge') !== false || strpos($fieldarray,'verkaufspreiswaehrung') !== false ||
          strpos($fieldarray, 'verkaufspreisgruppe') !== false || strpos($fieldarray,'verkaufspreiskundennummer') !== false ||
          strpos($fieldarray, 'verkaufspreisartikelnummerbeikunde') !== false || strpos($fieldarray,'verkaufspreisgueltigab') !== false ||
          strpos($fieldarray, 'verkaufspreisgueltigbis') !== false || strpos($fieldarray,'verkaufspreisinternerkommentar') !== false :
          if($ziel==='artikel' && !empty(trim($fieldarray))){
            $verkaufspreiseJoinen = true;
            $fieldarray = str_replace(["\r\n","\r","\n"],'',$fieldarray);
            $fields_array[] = 'vp.'.$fieldarray;
          }
          break;
        case strpos($fieldarray, 'eigenschaftname') !== false || strpos($fieldarray,'eigenschaftwert') !== false:
          if($ziel==='artikel' && !empty(trim($fieldarray))){
            $artikeleigenscahftenJoinen = true;
            $fieldarray = str_replace(["\r\n","\r","\n"],'',$fieldarray);
            $fields_array[] = 'ae.'.$fieldarray;
          }
          break;
        case strpos($fieldarray, 'freifeldname') !== false:
          if($ziel==='artikel' && !empty(trim($fieldarray))){
            $join = ' LEFT JOIN firmendaten AS fd ON 1 ';
            $fieldarray = str_replace(["\r\n","\r","\n"],'',$fieldarray);
            $feldname = str_replace('name','',$fieldarray);
            $fields_array[] = 'fd.'.$feldname.' AS '.$fieldarray;
            if(!in_array($join, $joins, true)){
              $joins[] = $join;
            }
          }
          break;
        case strpos($fieldarray, 'freifeld') !== false:
            $fieldarray = str_replace(["\r\n","\r","\n"],'',$fieldarray);
            if($ziel==='artikel' && !empty(trim($fieldarray))){
              $fields_array[] = 'art.'.$fieldarray;
            } else if ($ziel==='adresse' && !empty(trim($fieldarray))){
              $fields_array[] = 'adr.'.$fieldarray;
            }
          break;
        case 'aktiv':
          if($ziel==='artikel' && !empty(trim($fieldarray))){
            $fields_array[] = 'IF(inaktiv=1,0,1) AS aktiv';
          }
          break;
        case 'inaktiv':
          if($ziel==='artikel' && !empty(trim($fieldarray))){
            $fields_array[] = 'IF(inaktiv=1,1,0) AS inaktiv';
          }
          break;
        case 'variante_von':
          $fields_array[] = "'VAR:VARIANTE_VON' as variante_von";
          break;
        case 'projekt':
          $fields_array[] = "'VAR:PROJEKT' as projekt";
          break;
        case "einkaufspreisnetto":
          $fields_array[] = "'VAR:EINKAUFSPREISNETTO' as einkaufspreisnetto";
          break;
        case "lieferantname":
          if($ziel === 'artikel') {
            $join = ' LEFT JOIN adresse AS adr ON art.adresse = adr.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $fields_array[] = ' adr.name AS lieferantname ';
          }else{
            $fields_array[] = "'VAR:LIEFERANTNAME' as lieferantname";
          }
          break;
        case "lieferantnummer":
          if($ziel === 'artikel') {
            $join = ' LEFT JOIN adresse AS adr ON art.adresse = adr.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $fields_array[] = ' adr.lieferantennummer AS lieferantnummer ';
          }else{
            $fields_array[] = "'VAR:LIEFERANTNUMMER' as lieferantnummer";
          }
          break;
        case "lager_menge":
          $fields_array[] = "'VAR:LAGER_MENGE' as lager_menge";
          break;
        case "gegenkonto":
          $fields_array[] = "'VAR:GEGENKONTO' as gegenkonto";
          break;
        case "auftrag_internet":
          if($ziel === 'gutschrift') {
            $join = ' LEFT JOIN rechnung AS re ON b.rechnungid = re.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $join = ' LEFT JOIN auftrag AS ab ON re.auftragid = ab.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $fields_array[] = "ab.internet AS auftrag_internet";
          }
          elseif($ziel === 'rechnung') {
            $join = ' LEFT JOIN auftrag AS ab ON b.auftragid = ab.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $fields_array[] = "ab.internet as auftrag_internet";
          }else{
            $fields_array[] = "'VAR:AUFTRAG_INTERNET' as auftrag_internet";
          }
          break;
        case "auftrag_transaktionsnummer":
          if($ziel === 'gutschrift') {
            $join = ' LEFT JOIN rechnung AS re ON b.rechnungid = re.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $join = ' LEFT JOIN auftrag AS ab ON re.auftragid = ab.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $fields_array[] = "ab.transaktionsnummer AS auftrag_internet";
          }
          elseif($ziel === 'rechnung') {
            $join = ' LEFT JOIN auftrag AS ab ON b.auftragid = ab.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $fields_array[] = "ab.transaktionsnummer AS auftrag_transaktionsnummer";
          }else{
            $fields_array[] = "ab.transaktionsnummer AS auftrag_transaktionsnummer";
          }
          break;
        case "steuersatz_normal_betrag":
          $fields_array[] = "'VAR:STEUER_NORMAL_BETRAG' as steuersatz_normal_betrag";
          break;
        case "steuersatz_ermaessigt_betrag":
          $fields_array[] = "'VAR:STEUER_ERMAESSIGT_BETRAG' as steuersatz_ermaessigt_betrag";
          break;
        case "beleg_kundennummer":
          $fields_array[] = "'VAR:BELEG_KUNDENNUMMER' as beleg_kundennummer";
          break;
        case "beleg_name":
          $fields_array[] = "'VAR:BELEG_NAME' as beleg_name";
          break;
        case "beleg_land":
          $fields_array[] = "'VAR:BELEG_LAND' as beleg_land";
          break;
        case "beleg_belegnr":
          $fields_array[] = "'VAR:BELEG_BELEGNR' as beleg_belegnr";
          break;
        case "beleg_datum":
          $fields_array[] = "'VAR:BELEG_DATUM' as beleg_datum";
          break;
        case "beleg_status":
          $fields_array[] = "'VAR:BELEG_STATUS' as beleg_status";
          break;
        case 'beleg_bearbeiter':
          $fields_array[] = "'VAR:BELEG_BEARBEITER' as beleg_bearbeiter";
          break;
        case 'beleg_vertrieb':
          $fields_array[] = "'VAR:BELEG_VERTRIEB' as beleg_vertrieb";
          break;
        case 'projekt':
          if($ziel === 'artikel'){
            $join = ' LEFT JOIN projekt AS pr ON art.projekt = pr.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $fields_array[] = ' pr.abkuerzung AS projekt ';
          }elseif($ziel === 'adresse') {
            $join = ' LEFT JOIN projekt AS pr ON adr.projekt = pr.id ';
            if(!in_array($join, $joins)){
              $joins[] = $join;
            }
            $fields_array[] = ' pr.abkuerzung AS projekt ';
          }else{
            $fields_array[] = "'VAR:PROJEKT' as projekt";
          }
          break;
        case 'bp.nummer':
          if( $ziel==="bestellung_position")
          {
            $fields_array[] = "'VAR:NUMMER' as nummer";
          }
          break;
        case 'bp.bezeichnung':
          if( $ziel==="bestellung_position")
          {
            $fields_array[] = "bp.bezeichnunglieferant as bezeichnung";
          }
          break;
        case 'einheit':
          if($ziel === 'artikel') {
            $fields_array[] = ' art.einheit ';
          } else{
            $fields_array[] = "'VAR:EINHEIT' as einheit";
          }
          break;
        case 'inventurek':
          if($ziel === 'artikel') {
            $fields_array[] = ' art.inventurek ';
          } else{
            $fields_array[] = "'VAR:INVENTUREK' as inventurek";
          }
          break;
        case 'inventurekaktiv':
          if($ziel === 'artikel') {
            $fields_array[] = ' art.inventurekaktiv ';
          } else{
            $fields_array[] = "'VAR:INVENTUREKAKTIV' as inventurekaktiv";
          }
          break;
        case 'artikelbeschreibung_de':
          if($ziel === 'artikel'){
            $fields_array[] = 'art.anabregs_text AS artikelbeschreibung_de';
          }else {
            $fields_array[] = "'VAR:ARTIKELBESCHREIBUNG_DE' as artikelbeschreibung_de";
          }
          break;
        case 'artikelbeschreibung_en':
          if($ziel === 'artikel'){
            $fields_array[] = 'art.anabregs_text_en AS artikelbeschreibung_en';
          }else{
            $fields_array[] = "'VAR:ARTIKELBESCHREIBUNG_EN' as artikelbeschreibung_en";
          }
          break;
        case 'artikelkategorie':
          $fields_array[] = "'VAR:ARTIKELKATEGORIE' as artikelkategorie";
          break;
        case 'artikelkategorie_name':
          $fields_array[] = "'VAR:ARTIKELKATEGORIE_NAME' as artikelkategorie_name";
          break;
        case (strpos($fieldarray, 'artikelbaum') !== false):
          $fieldarray = str_replace(["\r\n","\r","\n"],'',$fieldarray);
          $fields_array[] = "'VAR:".strtoupper($fieldarray)."' AS ".$fieldarray;
          break;
        case 'standardlagerplatz':
          $fields_array[] = "'VAR:STANDARDLAGERPLATZ' AS standardlagerplatz";
          break;
        case 'typ':
          if($ziel === 'artikel'){
            $fields_array[] = 'art.typ';
          }else{
            $fields_array[] = "typ";
          }
          break;
        case 'geloescht':
          if($ziel === 'artikel'){
            $fields_array[] = 'art.geloescht';
          }else{
            $fields_array[] = "geloescht";
          }
          break;
        default:
          if(($ziel==='angebot' || $ziel==='auftrag' || $ziel === 'rechnung' || $ziel === 'lieferschein' ||
            $ziel === 'bestellung' || $ziel === 'gutschrift') && preg_match('/^[\w]+$/',trim($fieldarray))){
            $fields_array[] = 'b.'.trim($fieldarray);
          }else{
            $fields_array[] = trim($fieldarray);
          }
      }
    }

    if($artikeleigenscahftenJoinen){
      $pivotParts = [];
      for ($i=1;$i<=50;$i++){
       $pivotParts[] = "MAX((CASE WHEN ae.row_number=$i THEN ae.name ELSE '' END)) AS eigenschaftname$i";
       $pivotParts[] = "MAX((CASE WHEN ae.row_number=$i THEN ae.wert ELSE '' END)) AS eigenschaftwert$i";
      }

      $join = 'SELECT ae.artikel AS artikel, '.implode(',',$pivotParts).'
        FROM
        (SELECT IF(@previd=e.artikel,@rownum := @rownum + 1,@rownum := 1) as row_number, @previd ,@previd:=e.artikel, e.artikel AS artikel, e.name AS name, e.wert AS wert 
          FROM 
            (SELECT aew.artikel, ae.name, aew.wert
            FROM artikeleigenschaften ae 
            JOIN artikeleigenschaftenwerte aew ON ae.id = aew.artikeleigenschaften 
            WHERE ae.geloescht=0
            ORDEr BY aew.artikel) AS e
          JOIN (select @rownum := 0) r
          JOIN (select @previd := 0) p) AS ae
        GROUP BY ae.artikel';

      $joins[] = 'LEFT JOIN ('.$join.') AS ae ON ae.artikel = art.id';
    }

    if($verkaufspreiseJoinen){
      $pivotParts = [];
      for ($i=1;$i<=100;$i++){
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.preis ELSE '' END)) AS verkaufspreisnetto$i";
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.ab_menge ELSE '' END)) AS verkaufspreisabmenge$i";
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.vpe_menge ELSE'' END)) AS verkaufspreisvpemenge$i";
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.waehrung ELSE'' END)) AS verkaufspreiswaehrung$i";
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.kennziffer ELSE'' END)) AS verkaufspreisgruppe$i";
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.kundennummer ELSE'' END)) AS verkaufspreiskundennummer$i";
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.kundenartikelnummer ELSE'' END)) AS verkaufspreisartikelnummerbeikunde$i";
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.gueltig_ab ELSE'' END)) AS verkaufspreisgueltigab$i";
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.gueltig_bis ELSE'' END)) AS verkaufspreisgueltigbis$i";
        $pivotParts[] = "MAX((CASE WHEN vp.row_number=$i THEN vp.bemerkung ELSE'' END)) AS verkaufspreisinternerkommentar$i";
      }

      $join =  'SELECT vp.artikel, '.implode(',',$pivotParts).'
        FROM(
        SELECT  IF(@previdvp=vp.artikel,@rownumvp := @rownumvp + 1,@rownumvp := 1) AS row_number,@previdvp:=vp.artikel, vp.*
        FROM 
        (SELECT vp.*, a.kundennummer,g.kennziffer
                FROM verkaufspreise vp 
            LEFT JOIN adresse a ON vp.adresse = a.id
            LEFT JOIN gruppen g ON vp.gruppe = g.id
                WHERE vp.geloescht =0 
                ORDER BY vp.artikel ASC, vp.preis ASC) AS vp
                JOIN (select @rownumvp := 0) r
                JOIN (select @previdvp := 0) p
        ) AS vp GROUP BY vp.artikel';

      $joins[] = 'LEFT JOIN ('.$join.') AS vp ON vp.artikel = art.id';
    }

    return [$fields_array, $joins];
  }

  public function generateExport($xls, $sql, $exporterstezeilenummer, $exportdatenmaskierung, $exporttrennzeichen, $ziel, $returnResultByFunction = false, $maxMemory = 0, $maxTime = 0)
  {
    if(!$returnResultByFunction) {
      if($xls)
      {
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment; filename="export.csv"');
      }else{
        header('Content-Type: text/plain;');
        header('Content-Disposition: attachment; filename=export.csv');
      }
    }
    $returnValue = '';

    $limit = 10000;
    $offset = 0;
    $firstLinePassed = false;
    $queryContainsLimit = false;
    if(preg_match('/\s(LIMIT\s*\d)+|(OFFSET\s*\d)+/i', $sql) === 1){
      $queryContainsLimit = true;
    }

    do{
      if($queryContainsLimit){
        $workingQuery = $sql;
      }else{
        $workingQuery = $sql." LIMIT $limit OFFSET $offset";
      }

      $query = $this->app->DB->Query($workingQuery);

      if(!$firstLinePassed && $exporterstezeilenummer=='1') {
        foreach($this->app->DB->Fetch_Assoc($query) as $value=>$tmp)
        {
          if($xls) {
            $value = iconv('UTF-8','ISO-8859-1//TRANSLIT', $value);
          }
          $returnValue .= $exportdatenmaskierung.$value.$exportdatenmaskierung.$exporttrennzeichen;
        }
        $returnValue .= "\r\n";
        if(!$returnResultByFunction) {
          echo $returnValue;
        }
        $query->data_seek(0);
      }

      while($row = $this->app->DB->Fetch_Assoc($query))
      {
        $line = $this->Exportinner($row,$exportdatenmaskierung,$exporttrennzeichen, $returnResultByFunction, $ziel, $xls);
        if(!$returnResultByFunction) {
          echo $line;
        }else {
          $returnValue .= $line;
        }
      }
      $firstLinePassed = true;
      $offset += $limit;
    }while(!$queryContainsLimit && $query->num_rows === $limit);

    if(!$returnResultByFunction) {
      $this->app->ExitXentral();
    }
    return $returnValue;
  }


  function Exportinner($row,&$exportdatenmaskierung,&$exporttrennzeichen, &$internal, &$ziel, &$xls)
  {
    $output = '';
    $systemid = $row['systemid'];
    if($systemid <=0) {
      $systemid = $row['id'];
    }
    $replaces = [
      'VAR:VERKAUFSPREISNETTO' => 'verkaufspreis',
      'VAR:EINKAUFSPREISNETTO' => 'einkaufspreis',
      'VAR:LIEFERANTNAME' => 'lieferantname',
      'VAR:LIEFERANTNUMMER' => 'lieferantnummer',
      'VAR:LAGER_MENGE' => 'lager_menge',
      'VAR:GEGENKONTO' => 'gegenkonto',
      'VAR:AUFTRAG_INTERNET' => 'auftrag_internet',
      'VAR:AUFTRAG_TRANSAKTIONSNUMMER' => 'auftrag_transaktionsnummer',
      'VAR:STEUER_NORMAL_BETRAG' => 'steuersatz_normal_betrag',
      'VAR:STEUER_ERMAESSIGT_BETRAG' => 'steuersatz_ermaessigt_betrag',
      'VAR:BELEG_KUNDENNUMMER' => 'beleg_kundennummer',
      'VAR:BELEG_DATUM' => 'beleg_datum',
      'VAR:BELEG_STATUS' => 'beleg_status',
      'VAR:BELEG_NAME' => 'beleg_name',
      'VAR:BELEG_LAND' => 'beleg_land',
      'VAR:BELEG_BELEGNR' => 'beleg_belegnr',
      'VAR:BELEG_BEARBEITER' => 'beleg_bearbeiter',
      'VAR:BELEG_VERTRIEB' => 'beleg_vertrieb',
      'VAR:EINHEIT' => 'einheit',
      'VAR:NUMMER' => 'nummer',
      'VAR:PROJEKT' => 'projekt',
      'VAR:INVENTUREKAKTIV' => 'inventurekaktiv',
      'VAR:INVENTUREK' => 'inventurek',
      'VAR:ARTIKELBESCHREIBUNG_DE' => 'anabregs_text',
      'VAR:ARTIKELBESCHREIBUNG_EN' => 'anabregs_text_en',
      'VAR:ARTIKELKATEGORIE' => 'artikelkategorie',
      'VAR:ARTIKELKATEGORIE_NAME' => 'artikelkategorie_name',
      'VAR:STANDARDLAGERPLATZ' => 'standardlagerplatz',
      'VAR:VARIANTE_VON' => 'variante_von'
    ];

    for($i = 1; $i <= 20; $i++){
      $replaces['VAR:ARTIKELBAUM'.$i] = 'artikelbaum'.$i;
    }

    $replacesKey = array_keys($replaces);
    $notFounds = $replacesKey;
    foreach($row as $value) {
      $value = $this->app->erp->fixeUmlaute($value);
      foreach($notFounds as $key => $notFound) {
        if(strpos($value, $notFound) !== false) {
          unset($notFounds[$key]);
        }
      }
    }

    foreach($notFounds as $notFound) {
      unset($replaces[$notFound]);
    }

    $replacesR = array_flip($replaces);


    $params=array();
    switch($ziel)
    {
      case "adresse":
        if(!empty($replacesR['projekt'])){
          $projektid = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$systemid' LIMIT 1");
          $params['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projektid' LIMIT 1");
        }
      break;

      case "artikel": 
        if($systemid > 0 && is_numeric($systemid))
        {
          if(!empty($replacesR['lager_menge'])) {
            $params['lager_menge'] = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='" . $systemid . "'");

            $intlager_menge = (int)$params['lager_menge'];
            if($intlager_menge == $params['lager_menge']){
              $params['lager_menge'] = $intlager_menge;
            }
          }
          if(!empty($replacesR['projekt'])){
            $projektid = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$systemid' LIMIT 1");
            $params['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projektid' LIMIT 1");
          }
          if(!empty($replacesR['variante_von'])){
            $hauptartikelid = $this->app->DB->Select("SELECT variante_von FROM artikel WHERE id='$systemid' LIMIT 1");
            $params['variante_von'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$hauptartikelid' LIMIT 1");
          }
          if(!empty($replacesR['lieferantname'])) {
            $params['lieferantname'] = $this->app->DB->Select(
              "SELECT adr.name FROM artikel a LEFT JOIN adresse adr ON adr.id=a.adresse WHERE a.id=$systemid LIMIT 1");
          }
          if(!empty($replacesR['lieferantnummer'])){
            $params['lieferantnummer'] = $this->app->DB->Select("SELECT adr.lieferantennummer FROM artikel a LEFT JOIN adresse adr ON adr.id=a.adresse WHERE a.id=$systemid LIMIT 1");
          }
          if(!empty($replacesR['einkaufspreis'])){
            $params['einkaufspreis'] = $this->app->erp->GetEinkaufspreis($systemid, 1);
          }
          if(!empty($replacesR['verkaufspreis'])){
            $params['verkaufspreis'] = $this->app->erp->GetVerkaufspreis($systemid, 1);
          }
          if(!empty($replacesR['inventurek'])){
            $params['inventurek'] = $this->app->DB->Select("SELECT inventurek FROM artikel WHERE id = '$systemid' LIMIT 1");
          }
          if(!empty($replacesR['standardlagerplatz'])){
            $params['standardlagerplatz'] = $this->app->DB->Select("SELECT lp.kurzbezeichnung FROM artikel a LEFT JOIN lager_platz lp ON lp.id=a.lager_platz WHERE a.id = '$systemid' LIMIT 1");
          }
          if(!empty($replacesR['inventurekaktiv'])){
            $params['inventurekaktiv'] = $this->app->DB->Select("SELECT inventurekaktiv FROM artikel WHERE id = '$systemid' LIMIT 1");
          }
          if(!empty($replacesR['anabregs_text'])){
            $params['anabregs_text'] = $this->app->DB->Select("SELECT anabregs_text FROM artikel WHERE id = '$systemid' LIMIT 1");
          }
          if(!empty($replacesR['anabregs_text_en'])){
            $params['anabregs_text_en'] = $this->app->DB->Select("SELECT anabregs_text_en FROM artikel WHERE id = '$systemid' LIMIT 1");
          }
          if(!empty($replacesR['artikelkategorie']) || !empty($replacesR['artikelkategorie_name'])){
            $params['artikelkategorie'] = $this->app->DB->Select("SELECT if(CONVERT(SUBSTRING_INDEX(typ,'_', 1),UNSIGNED INTEGER)=0,'',CONVERT(SUBSTRING_INDEX(typ,'_', 1),UNSIGNED INTEGER)) as artikelkategorie FROM artikel WHERE id = '$systemid' LIMIT 1");

            if($params['artikelkategorie'] > 0){
              $params['artikelkategorie_name'] = $this->app->DB->Select("SELECT bezeichnung AS artikelkategorie_name FROM artikelkategorien WHERE id = '{$params['artikelkategorie']}' LIMIT 1");
            }else{
              $params['artikelkategorie_name'] = '';
            }
          }

          $markierteArtikelkategorien = $this->app->DB->SelectArr("SELECT * FROM artikelkategorien WHERE id IN (SELECT kategorie FROM artikelbaum_artikel WHERE artikel = '$systemid')");
          $alleArtikelkategorien = [];

          foreach($markierteArtikelkategorien as $key => $value){
            $alleArtikelkategorien[$value['id']] = $value;
          }

          for($i = 1; $i <= 20; $i++){
            if(!empty($replacesR['artikelbaum'.$i]) && !empty($markierteArtikelkategorien[$i-1])){
              $params['artikelbaum'.$i] = $this->ExportvorlageArtikelbaum($markierteArtikelkategorien[$i-1]['id'], $alleArtikelkategorien);
            }else{
              $params['artikelbaum'.$i] = '';
            }
          }
        }
        break;
      case "rechnung": 

        if($systemid > 0 && is_numeric($systemid))
        {

          if(!empty($replacesR['gegenkonto'])){
            $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM rechnung WHERE id='$systemid' LIMIT 1");
            $ustid = $this->app->DB->Select("SELECT ustid FROM rechnung WHERE id='$systemid' LIMIT 1");
            $params['gegenkonto'] = $this->app->erp->Gegenkonto($ust_befreit, $ustid, 'rechnung', $systemid);
          }
          if(!empty($replacesR['auftrag_internet']) || !empty($replacesR['auftrag_transaktionsnummer'])){
            $auftragid = $this->app->DB->Select("SELECT auftragid FROM rechnung WHERE id='$systemid' LIMIT 1");
            $params['auftrag_internet'] = $this->app->DB->Select("SELECT internet FROM auftrag WHERE id='$auftragid' LIMIT 1");
            $params['auftrag_transaktionsnummer'] = $this->app->DB->Select("SELECT transaktionsnummer FROM auftrag WHERE id='$auftragid' LIMIT 1");
          }
          if(!empty($replacesR['steuersatz_normal_betrag'])) {
            $params['steuersatz_normal_betrag'] = round($this->app->erp->RechnungZwischensummeSteuersaetzeBrutto($systemid, "normal"), 2);
          }
          if(!empty($replacesR['steuersatz_ermaessigt_betrag'])) {
            $params['steuersatz_ermaessigt_betrag'] = round($this->app->erp->RechnungZwischensummeSteuersaetzeBrutto($systemid, "ermaessigt"), 2);
          }
        }
        break;
      case "gutschrift": 

        if($systemid > 0 && is_numeric($systemid))
        {
          if(!empty($replacesR['gegenkonto'])) {
            $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM gutschrift WHERE id='$systemid' LIMIT 1");
            $ustid = $this->app->DB->Select("SELECT ustid FROM gutschrift WHERE id='$systemid' LIMIT 1");
            $params['gegenkonto'] = $this->app->erp->Gegenkonto($ust_befreit, $ustid, 'gutschrift', $systemid);
          }
          if(!empty($replacesR['auftrag_internet']) || !empty($replacesR['auftrag_transaktionsnummer'])){
            $rechnungid = $this->app->DB->Select("SELECT rechnungid FROM gutschrift WHERE id='$systemid' LIMIT 1");
            $auftragid = $this->app->DB->Select("SELECT auftragid FROM rechnung WHERE id='$rechnungid' LIMIT 1");
            $params['auftrag_internet'] = $this->app->DB->Select("SELECT internet FROM auftrag WHERE id='$auftragid' LIMIT 1");
            $params['auftrag_transaktionsnummer'] = $this->app->DB->Select("SELECT transaktionsnummer FROM auftrag WHERE id='$auftragid' LIMIT 1");
          }
          if(!empty($replacesR['steuersatz_normal_betrag'])){
            $params['steuersatz_normal_betrag'] = round($this->app->erp->GutschriftZwischensummeSteuersaetzeBrutto($systemid, "normal"), 2);
          }
          if(!empty($replacesR['steuersatz_ermaessigt_betrag'])){
            $params['steuersatz_ermaessigt_betrag'] = round($this->app->erp->GutschriftZwischensummeSteuersaetzeBrutto($systemid, "ermaessigt"), 2);
          }
        }
        break;
      case "auftrag_position": 
      case "angebot_position": 
      case "rechnung_position": 
      case "lieferschein_position": 
      case "gutschrift_position": 
      case "bestellung_position": 

        $tmp = explode('_',$ziel);

        $tabellenname = $tmp[0];
        unset($tmp);
        if($systemid > 0 && is_numeric($systemid))
        {
          $tmpsystemid = $this->app->DB->Select("SELECT $tabellenname FROM ".$tabellenname."_position WHERE id='$systemid' LIMIT 1");
          $params['beleg_name'] =$this->app->DB->Select("SELECT name FROM ".$tabellenname." WHERE id='$tmpsystemid' LIMIT 1");

          $params['beleg_kundennummer'] =$this->app->DB->Select("SELECT kundennummer FROM ".$tabellenname." WHERE id='$tmpsystemid' LIMIT 1");
          if($params['beleg_kundennummer']=="")
          {
            $tmpadresse = $this->app->DB->Select("SELECT adresse FROM ".$tabellenname." WHERE id='$tmpsystemid' LIMIT 1");
            $params['beleg_kundennummer'] =$this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$tmpadresse' LIMIT 1");
          }

          $params['beleg_belegnr'] =$this->app->DB->Select("SELECT belegnr FROM ".$tabellenname." WHERE id='$tmpsystemid' LIMIT 1");
          $params['beleg_datum'] =$this->app->DB->Select("SELECT datum FROM ".$tabellenname." WHERE id='$tmpsystemid' LIMIT 1");
          $params['beleg_status'] =$this->app->DB->Select("SELECT status FROM ".$tabellenname." WHERE id='$tmpsystemid' LIMIT 1");
          $params['beleg_land'] =$this->app->DB->Select("SELECT land FROM ".$tabellenname." WHERE id='$tmpsystemid' LIMIT 1");
          $params['beleg_bearbeiter'] =$this->app->DB->Select("SELECT bearbeiter FROM ".$tabellenname." WHERE id='$tmpsystemid' LIMIT 1");
          $params['beleg_vertrieb'] =$this->app->DB->Select("SELECT vertrieb FROM ".$tabellenname." WHERE id='$tmpsystemid' LIMIT 1");
          if(!empty($replacesR['projekt'])){
            $projektid = $this->app->DB->Select("SELECT projekt FROM " . $tabellenname . "_position WHERE id='$systemid' LIMIT 1");
            $params['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projektid' LIMIT 1");
          }
          if(!empty($replacesR['nummer'])){
            $tmpartikelid = $this->app->DB->Select("SELECT artikel FROM " . $tabellenname . "_position WHERE id='$systemid' LIMIT 1");

            $params['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$tmpartikelid' LIMIT 1");
          }
          if(!empty($replacesR['einheit'])) {
            $einheitcheck = $this->app->DB->Select("SELECT einheit FROM " . $tabellenname . "_position WHERE id='$systemid' LIMIT 1");
            if($einheitcheck == ''){
              $einheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE id='$tmpartikelid' LIMIT 1");
              unset($tmpartikelid);
              if($einheit == ''){
                $einheit = $this->app->erp->Firmendaten("artikeleinheit_standard");
              }
              $params['einheit'] = $einheit;
              unset($einheit);
            }else{
              $params['einheit'] = $einheitcheck;
              unset($einheitcheck);
            }
          }
        }
        break;
    }

    foreach($row as $value)
    {
      $value = $this->app->erp->fixeUmlaute($value);
      // ersetzte platzhalter
      foreach($replaces as $k => $v) {
        $value = str_replace($k, $params[$v], $value);
      }

      $value = $this->app->erp->ParseDecimalForCSV($value);
      if($xls) {
        $value = iconv('UTF-8','ISO-8859-1//TRANSLIT', $value);
      }
      $output .= $exportdatenmaskierung.$value.$exportdatenmaskierung.$exporttrennzeichen;
    }

    return $output."\r\n";
  }

  function ExportvorlageArtikelbaum($kategorieId, &$alleArtikelkategorien){
    if(!array_key_exists($kategorieId, $alleArtikelkategorien)){
      $fehlendeArtikelkategorie = $this->app->DB->SelectRow("SELECT * FROM artikelkategorien WHERE id = '$kategorieId' LIMIT 1");
      $alleArtikelkategorien[$kategorieId] = $fehlendeArtikelkategorie;
    }
    $artikelkategorieBezeichnung = $alleArtikelkategorien[$kategorieId]['bezeichnung'];

    if($alleArtikelkategorien[$kategorieId]['parent'] != 0){
      $artikelkategorieBezeichnung = $this->ExportvorlageArtikelbaum($alleArtikelkategorien[$kategorieId]['parent'], $alleArtikelkategorien).'|'.$artikelkategorieBezeichnung;
    }

    return $artikelkategorieBezeichnung;
  }

  function ExportvorlageDo()
  {
    $id = $this->app->Secure->GetGET("id");
    $ziel = $this->app->DB->Select("SELECT ziel FROM exportvorlage WHERE id='$id' LIMIT 1");
    $fields = $this->ExportvorlageGetFields($id);


    $ekpreisaenderungen = 0;
    $vkpreisaenderungen = 0;

    $tmp = $this->app->Secure->GetPOST("row");

    $number_of_rows = count($tmp['cmd']);
    for($i=1;$i<=$number_of_rows;$i++)
    {
      $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$tmp['lieferantennummer'][$i]."' 
          AND lieferantennummer!='' LIMIT 1");

      $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$tmp['nummer'][$i]."' AND nummer!='' LIMIT 1");
      $kundenid = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp['kundennummer'][$i]."' AND kundennummer!='' LIMIT 1");
      if($kundenid<=0) $kundenid=0;
      if($lieferantid<=0) $lieferantid=0;

      if($lieferantid<=0)
        $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE name='".$tmp['lieferantname'][$i]."' LIMIT 1");

      switch($ziel)
      {
        case "einkauf":
        case "artikel":

          // wenn es artikel nicht gibt muss man diesen neu anlegen
          if($tmp['cmd'][$i]=="create" && $tmp['checked'][$i]=="1")
          {
            if($tmp['name_de']!="")
            {
              foreach($fields as $key=>$value)
                $felder[$value]=$tmp[$value][$i];
            }

            if($tmp['nummer'][$i]=="")
              $felder['nummer']=$this->app->erp->GetNextArtikelnummer($tmp['typ'][$i]);
            else
              $felder['nummer']=$tmp['nummer'][$i];

            // ek preis
            if($lieferantid <=0 && $tmp['lieferantname'][$i]!="")
            {
              $lieferantid = $this->app->erp->CreateAdresse($tmp['lieferantname'][$i]);
              $this->app->erp->AddRolleZuAdresse($lieferantid, "Lieferant", "von","Projekt","");
            }
            if($lieferantid>0)
              $felder['adresse']=$lieferantid;
            // mit welcher Artikelgruppe?
            $artikelid = $this->app->erp->ExportCreateArtikel($felder);

            // vk preis
            if($tmp['lieferanteinkaufnetto'][$i]!="" && $lieferantid > 0){

              if($tmp['lieferantbestellnummer'][$i]!="") $nr = $tmp['lieferantbestellnummer'][$i];
              else if($tmp['herstellernummer'][$i]!="") $nr = $tmp['herstellernummer'][$i];
              else $nr = $tmp['name_de'][$i];

              if($tmp['lieferanteinkaufvpemenge'][$i] > 0 && $tmp['lieferanteinkaufmenge'][$i]<=0)
                $tmp['lieferanteinkaufmenge'][$i] = $tmp['lieferanteinkaufvpemenge'][$i];

              if($tmp['lieferanteinkaufmenge'][$i] > 1)
              {
                $tmp['lieferanteinkaufnetto'][$i] = $tmp['lieferanteinkaufnetto'][$i] / $tmp['lieferanteinkaufmenge'][$i];
                $tmp['lieferanteinkaufmenge'][$i] = 1;
              }

              if($tmp['lieferanteinkaufmenge'][$i]<=0)
                $tmp['lieferanteinkaufmenge'][$i] = 1;

              $this->app->erp->AddEinkaufspreis($artikelid,$tmp['lieferanteinkaufmenge'][$i],
                  $lieferantid,$nr,$nr,
                  str_replace(',','.',$tmp['lieferanteinkaufnetto'][$i]),$tmp['lieferanteinkaufwaehrung'][$i],$tmp['lieferanteinkaufvpemenge'][$i]);
            }

            if($tmp['verkaufspreis1netto'][$i]!=""){
              $this->app->erp->AddVerkaufspreis($artikelid,$tmp['verkaufspreis1menge'][$i],
                  $kundenid,str_replace(',','.',$tmp['verkaufspreis1netto'][$i]),$tmp['verkaufspreis1waehrung'][$i]);
            }
            if($tmp['verkaufspreis2netto'][$i]!=""){
              $this->app->erp->AddVerkaufspreis($artikelid,$tmp['verkaufspreis2menge'][$i],
                  $kundenid,str_replace(',','.',$tmp['verkaufspreis2netto'][$i]),$tmp['verkaufspreis2waehrung'][$i]);
            }

            if($tmp['verkaufspreis3netto'][$i]!=""){
              $this->app->erp->AddVerkaufspreis($artikelid,$tmp['verkaufspreis3menge'][$i],
                  $kundenid,str_replace(',','.',$tmp['verkaufspreis3netto'][$i]),$tmp['verkaufspreis3waehrung'][$i]);
            }

            $lager_id = $this->app->DB->Select("SELECT id FROM lager WHERE geloescht!='1' LIMIT 1");
            if($tmp['lager'][$i]!=''){
              if(empty($lager_id)) {
                $this->app->DB->Insert(
                  "INSERT INTO lager (bezeichnung,firma,manuell,logdatei,projekt,geloescht,beschreibung) 
                  VALUES ('Hauptlager',1,0,NOW(),0,0,'')"
                );
                $lager_id = $this->app->DB->GetInsertID();
              }
              $this->app->DB->Update("UPDATE artikel SET lagerartikel='1' WHERE id='$artikelid' LIMIT 1");
              $regal = $this->app->erp->CreateLagerplatz($lager_id,$tmp['lager'][$i]);
              $this->app->erp->LagerEinlagernDifferenz($artikelid,$tmp['lagermenge'][$i],$regal,"","Erstbef&uuml;llung",1);
            }
            //17:lieferanteinkaufvpemenge;

          } else if ($tmp['cmd'][$i]=="update" && $tmp['checked'][$i]=="1") {

            // wenn er vorhanden ist nur ein Update braucht

            if($artikelid > 0)
            {
              foreach($fields as $key=>$value)
              {                       
                switch($value)
                {
                  case "name_de":
                  case "name_en":
                  case "kurztext_en":
                  case "kurztext_de":
                  case "beschreibung_de":
                  case "beschreibung_en":
                  case "anabregs_text":
                  case "typ":
                  case "ean":
                  case "gewicht":
                  case "hersteller":
                  case "herstellerlink":
                  case "herstellernummer":
                    $this->app->DB->Update("UPDATE artikel SET ".$value."='".$tmp[$value][$i]."' WHERE id='".$artikelid."' LIMIT 1");
                    break;
                  case  "lieferanteinkaufnetto":
                    $alterek = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE ab_menge='".$tmp['lieferanteinkaufmenge'][$i]."' 
                        AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW()) AND adresse='".$lieferantid."' LIMIT 1");
                    if($alterek != str_replace(',','.',$tmp['lieferanteinkaufnetto'][$i]))
                    {
                      $ekpreisaenderungen++;
                      $this->app->DB->Update("UPDATE einkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) 
                          WHERE adresse='".$lieferantid."' AND artikel='".$artikelid."' 
                          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW())
                          AND ab_menge='".$tmp['lieferanteinkaufmenge'][$i]."' LIMIT 1");

                      if($tmp['lieferantbestellnummer'][$i]!="") $nr = $tmp['lieferantbestellnummer'][$i];
                      else if($tmp['herstellernummer'][$i]!="") $nr = $tmp['herstellernummer'][$i];
                      else $nr = $tmp['name_de'][$i];

                      if($tmp['lieferanteinkaufvpemenge'][$i] > 0 && $tmp['lieferanteinkaufmenge'][$i]<=0)
                        $tmp['lieferanteinkaufmenge'][$i] = $tmp['lieferanteinkaufvpemenge'][$i];

                      if($tmp['lieferanteinkaufmenge'][$i] > 1)
                      {
                        $tmp['lieferanteinkaufnetto'][$i] = $tmp['lieferanteinkaufnetto'][$i] / $tmp['lieferanteinkaufmenge'][$i];
                        $tmp['lieferanteinkaufmenge'][$i] = 1;
                      }

                      if($tmp['lieferanteinkaufmenge'][$i]<=0)
                        $tmp['lieferanteinkaufmenge'][$i] = 1;

                      $this->app->erp->AddEinkaufspreis($artikelid,$tmp['lieferanteinkaufmenge'][$i],
                          $lieferantid,$nr,$nr,
                          str_replace(',','.',$tmp['lieferanteinkaufnetto'][$i]),$tmp['lieferanteinkaufwaehrung'][$i],$tmp['lieferanteinkaufvpemenge'][$i]);
                    } 
                    break;
                  case  "verkaufspreis1netto":
                    $altervk = $this->app->DB->Select("SELECT preis FROM verkaufspreis WHERE ab_menge='".$tmp['verkaufspreis1menge'][$i]."' 
                        AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW() ) WHERE adresse <='$kundenid' LIMIT 1");
                    if($altervk != str_replace(',','.',$tmp['verkaufspreis1netto'][$i]))
                    {
                      $vkpreisaenderungen++;
                      $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) 
                          WHERE artikel='".$artikelid."' AND adresse='$kundenid'
                          AND ab_menge='".$tmp['verkaufspreis1menge'][$i]."' LIMIT 1");

                      $this->app->erp->AddVerkaufspreis($artikelid,$tmp['verkaufspreis1menge'][$i],
                          $kundenid,str_replace(',','.',$tmp['verkaufspreis1netto'][$i]),$tmp['verkaufspreis1waehrung'][$i]);
                    } 
                    break;
                  case  "verkaufspreis2netto":
                    $altervk = $this->app->DB->Select("SELECT preis FROM verkaufspreis WHERE ab_menge='".$tmp['verkaufspreis2menge'][$i]."' 
                        AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW() ) WHERE adresse <='$kundenid' LIMIT 1");
                    if($altervk != str_replace(',','.',$tmp['verkaufspreis2netto'][$i]))
                    {
                      $vkpreisaenderungen++;
                      $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) 
                          WHERE artikel='".$artikelid."' AND adresse='$kundenid'
                          AND ab_menge='".$tmp['verkaufspreis2menge'][$i]."' LIMIT 1");

                      $this->app->erp->AddVerkaufspreis($artikelid,$tmp['verkaufspreis2menge'][$i],
                          $kundenid,str_replace(',','.',$tmp['verkaufspreis2netto'][$i]),$tmp['verkaufspreis2waehrung'][$i]);
                    } 
                    break;
                  case  "verkaufspreis3netto":
                    $altervk = $this->app->DB->Select("SELECT preis FROM verkaufspreis WHERE ab_menge='".$tmp['verkaufspreis3menge'][$i]."' 
                        AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW() ) WHERE adresse <='$kundenid' LIMIT 1");
                    if($altervk != str_replace(',','.',$tmp['verkaufspreis3netto'][$i]))
                    {
                      $vkpreisaenderungen++;
                      $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) 
                          WHERE artikel='".$artikelid."' AND adresse='$kundenid'
                          AND ab_menge='".$tmp['verkaufspreis3menge'][$i]."' LIMIT 1");

                      $this->app->erp->AddVerkaufspreis($artikelid,$tmp['verkaufspreis3menge'][$i],
                          $kundenid,str_replace(',','.',$tmp['verkaufspreis3netto'][$i]),$tmp['verkaufspreis3waehrung'][$i]);
                    } 
                    break;

                }
              }
            }
          }   
          break;
        case "zeiterfassung":
          if($tmp['cmd'][$i]=="create" && $tmp['checked'][$i]=="1")
          {
            if($tmp['nummer'][$i]!="")
            {
              foreach($fields as $key=>$value)
                $felder[$value]=$tmp[$value][$i];
              $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp['nummer'][$i]."' LIMIT 1");
            }
            $vonZeit = $felder['datum_von']." ".$felder['zeit_von'].":00";
            $bisZeit = $felder['datum_bis']." ".$felder['zeit_bis'].":00";
            $ort = "";
            $projekt = "";
            $art = "";
            $kunde = $adresse;
            if($felder['taetigkeit']=="")$felder['taetigkeit']="Zeiterfassung";
            $this->app->erp->AddArbeitszeit($this->app->User->GetID(), $vonZeit, $bisZeit, $felder['taetigkeit'], $felder['details'],$ort, $projekt, 0,$art,$kunde);
          }
          break;
        case "adresse":

          if($tmp['cmd'][$i]=="create" && $tmp['checked'][$i]=="1")
          {
            $adresse=0;
            foreach($fields as $key=>$value)
              $felder[$value]=$tmp[$value][$i];

            if($tmp['kundennummer'][$i]!="" || $tmp['lieferantennummer'][$i]!="")
            {
              $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp['kundennummer'][$i]."' AND kundennummer!='' LIMIT 1");
              if($adresse <=0)
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$tmp['lieferantennummer'][$i]."' AND lieferantennummer!='' LIMIT 1");
            }
            if($adresse <=0 && $felder['name']!="")
            { 
              //adresse anlegen
              $adresse =$this->app->erp->ExportCreateAdresse($felder);
              if($tmp['lieferantennummer'][$i]!="")
                $this->app->erp->AddRolleZuAdresse($adresse, "Lieferant", "von","Projekt","");
              if($tmp['kundennummer'][$i]!="")
                $this->app->erp->AddRolleZuAdresse($adresse, "Kunde", "von","Projekt","");
              //rolle verpassen
            }

          }
          else if($tmp['cmd'][$i]=="update" && $tmp['checked'][$i]=="1")
          {
            $adresse=0;
            //            foreach($fields as $key=>$value)
            //              $felder[$value]=$tmp[$value][$i];

            if($tmp['kundennummer'][$i]!="" || $tmp['lieferantennummer'][$i]!="")
            {
              $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp['kundennummer'][$i]."' AND kundennummer!='' LIMIT 1");
              if($adresse <=0)
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$tmp['lieferantennummer'][$i]."' AND lieferantennummer!='' LIMIT 1");
            }
            if($adresse > 0)
            {
              foreach($fields as $key=>$value)
              {
                $felder[$key]=$tmp[$value][$i];
                if($key=="typ" || $key=="zahlungsweise") $tmp[$value][$i] = strtolower($tmp[$value][$i]);
                if($key=="land") {
                  if($tmp[$value][$i]=="Deutschland" || $tmp[$value][$i]=="Germany" || $tmp[$value][$i]=="")
                    $tmp[$value][$i] = "DE";
                }

                $this->app->DB->Update("UPDATE adresse SET ".$fields[$key]."='".$tmp[$value][$i]."' WHERE id='$adresse' LIMIT 1");
              }

            }
          }



          break;
      }
    }
    if($ziel=="zeiterfassung")
    {
      $msg=$this->app->erp->base64_url_encode("<div class=\"info\">Export durchgef&uuml;hrt.</div>");
      header("Location: index.php?module=exportvorlage&action=export&id=$id&msg=$msg");
      exit;
    } else {  
      $msg=$this->app->erp->base64_url_encode("<div class=\"info\">Export durchgef&uuml;hrt.</div>");
      header("Location: index.php?module=exportvorlage&action=export&id=$id&msg=$msg");
      exit;
    }
  }   


  function ExportPrepareHeader($ziel,$csv_fields_keys,$csv_fields)
  {
    $number_of_fields =count($csv_fields_keys);

    switch($ziel)
    {
      case "einkauf":
      case "artikel":
        $this->app->Tpl->Add('ERGEBNIS','<tr><td width="100"><b>Auswahl</b></td><td width="100"><b>Aktion</b></td><td><b>Artikel</b></td>');
        break;
      case "adresse":
        $this->app->Tpl->Add('ERGEBNIS','<tr><td width="100"><b>Auswahl</b></td><td width="100"><b>Aktion</b></td><td><b>Adresse</b></td>');
        break;

      case "zeiterfassung":
        $this->app->Tpl->Add('ERGEBNIS','<tr><td width="100"><b>Auswahl</b></td>
            <td width="100"><b>Aktion</b></td><td><b>Kunde</b></td>');
        break;
    }

    for($j=0;$j<$number_of_fields;$j++)
    {
      $this->app->Tpl->Add('ERGEBNIS','<td><b>'.$csv_fields[($csv_fields_keys[$j])].'</b></td>');
    }
    $this->app->Tpl->Add('ERGEBNIS','</tr>');
  }

  function ExportPrepareRow($rowcounter,$ziel,$data,$csv_fields_keys,$csv_fields)
  {
    $number_of_fields =count($csv_fields_keys);
    //Standard
    $fields[waehrung] = 'EUR';

    for($j=0;$j<$number_of_fields;$j++)
    {
      $value = trim($data[($csv_fields_keys[$j]-1)]);

      $fieldname = $csv_fields[$csv_fields_keys[$j]];
      switch($fieldname)
      {
        case "herstellernummer":
          $fields['herstellernummer'] = $value;
          $fields['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer 
              FROM artikel WHERE herstellernummer='".$fields['herstellernummer']."' LIMIT 1");
          //                                                  if($fields[herstellernummer]<=0) $fields[herstellernummer]="";
          break;
        case "nummer":
          $fields['nummer'] = $value;
          $fields['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE nummer='".$fields['nummer']."' LIMIT 1");
          //if($fields[nummer]==0) $fields[nummer]="";
          break;
        case "lieferantennummer":
          $fields['lieferantennummer'] = $value;
          $fields['lieferantennummer'] = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE lieferantennummer='".$fields['lieferantennummer']."' LIMIT 1");
          $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$fields['lieferantennummer']."' LIMIT 1");
          if($fields['lieferantennummer']<=0) $fields['lieferantennummer']="";
          break;
        case "kundennummer":
          $fields['kundennummer'] = $value;
          $fields['kundennummer'] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE lieferantennummer='".$fields['lieferantennummer']."' LIMIT 1");
          if($fields['kundennummer']<=0) $fields['kundennummer']="";
          break;
        case "ab_menge":
          $fields['ab_menge'] = $value;
          break;
        case "ean":
          $fields['ab_menge'] = $value;
          break;
        case "waehrung":
          $fields['waehrung'] = $value;
          break;
        case "ekpreis":
          $value = str_replace('EUR','',$value);
          $value = str_replace(' ','',$value);
          if(preg_match('#^(?<integer>.*)(?<separator>[\.,])(?<decimals>[0-9]+)$#', $value, $matches))
          {
            /* clean integer and append decimals with your own separator */
            $number = ((int) preg_replace('#[^0-9]+#', '', $matches['integer']) . ',' . $matches['decimals']);
          }
          else
          {
            $number = (int) preg_replace('#[^0-9]+#', '', $input);
          }
          // $formatter = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);

          // prüfe von rechts letztes zeichen das keine 0 ist

          // let's print the international format for the en_US locale
          $value = $number;
          $fields['ekpreis'] = $value;
          break;
        case "datum_von":
          $value = $this->app->String->Convert($value,"%1.%2.%3","20%3-%2-%1");
          $fields['datum_von'] = $value;
          break;
        case "datum_bis":
          $value = $this->app->String->Convert($value,"%1.%2.%3","20%3-%2-%1");
          $fields['datum_bis'] = $value;
          break;
        case "kennung":
          $fields['kennung'] = $value;
          break;
        case "zeit_bis":
          $fields['zeit_bis'] = $value;
          break;
        case "zeit_von":
          $fields['zeit_von'] = $value;
          break;



        default:
          $fields[$fieldname] = $value;       
          //$value = $data[($csv_fields_keys[$j]-1)];
          //  $value = $data[($csv_fields_keys[$j]-1)];
      }

      $output .= '<td><input type="text" size="15" name="row['.$fieldname.']['.$rowcounter.']" value="'.$value.'"></td>';
    }


    switch($ziel)
    {
      case "einkauf":
        $checked = "checked";
        if($fields['lieferantennummer']=="")
        {
          $action_anzeige = "Keine (Lieferant fehlt)";
          $action="none";
          $checked="";
        }
        else if($fields['lieferantennummer']!="" && $fields['nummer']!="")
        {
          $nummer = $fields['nummer'];
          $action_anzeige = "Update (Artikelnr. gefunden)";
          $action="update";
        }
        else if($fields['lieferantennummer']!="" && $fields['herstellernummer']!="")
        {
          $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE herstellernummer='".$fields['herstellernummer']."' LIMIT 1");
          $action_anzeige = "Update (Herstellernr. gefunden)";
          $action="update";
        } 
        else if($fields['lieferantennummer']!="" && $fields['bestellnummer']!="")
        {
          $artikelid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE bestellnummer='".$fields['bestellnummer']."'
              AND adresse='".$lieferantid."' LIMIT 1");
          $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='".$artikelid."' LIMIT 1");
          $action_anzeige = "Update (Bestellnr. gefunden)";
          $action="update";
        } 


        else {
          $action_anzeige = "Keine (Artikel- oder Herstellernr. fehlt)";
          $action="none";
          $checked="";
        }
        break;
      case "adresse":
        if($fields['kundennummer']=="" && $fields['lieferantennummer']=="" && $fields['name']=="")
        {
          $action_anzeige = "Keine (Kd.- und Lieferanten-Nr. und name fehlt)";
          $action="none";
          $checked="";
        }
        else if($fields['kundennummer']=="" && $fields['name']!="" && $fields['lieferantennummer']=="")
        {
          $action_anzeige = "Neu (Adresse neu anlegen)";
          $action="create";
          $checked="checked";
        }
        else if($fields['lieferantennummer']!="" || $fields['kundennummer']!="")
        {
          $checkkunde = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$fields['kundennummer']."' AND kundennummer!='' LIMIT 1");
          if($checkkunde <= 0)
          {
            $action_anzeige = "Neu (Adresse neu anlegen)";
            $action="create";
            $checked="checked";
          } else {
            $action_anzeige = "Update (Kundennummer gefunden)";
            $action="update";
            $checked="checked";
          }

          $checklieferant = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$fields['lieferantennummer']."' AND lieferantennummer!='' LIMIT 1");
          if($checklieferant <= 0)
          {
            $action_anzeige = "Neu (Adresse neu anlegen)";
            $action="create";
            $checked="checked";
          } else {
            $action_anzeige = "Update (Lieferantennummer gefunden)";
            $action="update";
            $checked="checked";
          }
        }

        break;

      case "artikel":
        if($fields['nummer']=="" && $fields['name_de']=="")
        {
          $action_anzeige = "Keine (Artikel Nr. und name_de fehlt)";
          $action="none";
          $checked="";
        }
        else if($fields['nummer']=="" && $fields['name_de']!="")
        {
          $action_anzeige = "Neu (Artikel neu anlegen)";
          $action="create";
          $checked="checked";
        }
        else if($fields['nummer']!="")
        {
          $action_anzeige = "Update (Artikel update)";
          $action="update";
          $checked="checked";
        }
        break;
      case "zeiterfassung":
        $checked = "checked";
        if($fields['kennung']!="")
          $nummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE kennung='".$fields['kennung']."' LIMIT 1");
        else $nummer="";
        if($nummer=="")
        {
          $action_anzeige = "Keine (Kennung oder Kundennummer fehlt)";
          $action="none";
          $checked="";
        } else {
          $action="create";
        }
        break;


    }

    $this->app->Tpl->Add('ERGEBNIS','<tr><td width="100"><input type="hidden" name="row[cmd]['.$rowcounter.']" value="'.$action.'">
        <input type="checkbox" name="row[checked]['.$rowcounter.']" '.$checked.' value="1"></td><td nowrap>'.$action_anzeige.'</td>
        <td>'.$nummer.'<input type="hidden" name="row[nummer]['.$rowcounter.']" value="'.$nummer.'"></td>'.$output);
    $this->app->Tpl->Add('ERGEBNIS','</tr>');
  }

}
