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
use Xentral\Modules\GoogleApi\Service\GoogleCredentialsService;
use Xentral\Modules\GoogleCalendar\Client\GoogleCalendarClientFactory;
use Xentral\Modules\GoogleCalendar\Service\GoogleCalendarSynchronizer;
use Xentral\Modules\GoogleCalendar\Service\GoogleSyncGateway;

class Kalender {
  /** @var Application $app */
  var $app;

  const MODULE_NAME = 'Calendar';

  public $javascript = [
    './classes/Modules/Calendar/www/js/fullcalendar.js',
    './classes/Modules/Calendar/www/js/calendar.js',
    './classes/Modules/Calendar/www/js/calendargroups.js',
  ];

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public static function TableSearch($app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "kalender_gruppenlist":
      $allowed['kalender'] = array('gruppenlist');

      $heading = array('Bezeichnung', 'Farbe', 'Inaktiv', 'Men&uuml;');
      $width = array('50%', '30%', '10%','5%');

      $findcols = array('kg.bezeichnung', 'kg.farbe', "if(kg.ausblenden,'ja','nein')",'kg.id');
      $searchsql = array('kg.bezeichnung', 'kg.farbe', 'kg.ausblenden');

      $defaultorder = 1;
      $defaultorderdesc = 0;

      $menu = '<table>';
        $menu .= '<tr>';
          $menu .= '<td nowrap>';
            $menu .= '<a href="#" class="calendar-group-edit" data-calendar-group="%value%">';
              $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
            $menu .= '</a>&nbsp;';
            $menu .= '<a href="#" class="calendar-group-delete" data-calendar-group="%value%">';
              $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
            $menu .= '</a>';
          $menu .= "</td>";
        $menu .= "</tr>";
      $menu .= "</table>";

      $where = " kg.id > 0 ";

      $sql = "SELECT SQL_CALC_FOUND_ROWS kg.id, kg.bezeichnung, CONCAT('<span style=\"background-color:',kg.farbe,';\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>'), if(kg.ausblenden,'ja','nein'), kg.id FROM kalender_gruppen kg";

      $count = "SELECT count(kg.id) FROM kalender_gruppen kg WHERE $where";
      break;

      case 'kalender_gruppen_mitglieder':

        //$doctype = str_replace('belegeinauslagern_bewegungen_','',$name);
//        $id = $app->Secure->GetPost('editid');
        $lid = $app->YUI->TableSearchFilter($name, 1, 'filterlid');
//        $lid = $app->YUI->TableSearchFilter($name, 1, 'filterlid');
        $allowed['kalender'] = array('gruppenlist');
        $heading = array('','Mitarbeiter-Nr.','Name','');
        $width = array('1%','10%','49%','1%');
        $findcols = array('a.id','a.mitarbeiternummer','a.name','a.id');
        $searchsql = array('a.id','a.mitarbeiternummer','a.name');
        $defaultorder = 2;
        $defaultorderdesc = 0;
        $disabled = "";
        //if(!$lid)$disabled = "disabled ";
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,CONCAT('<input type=\"checkbox\" name=\"kg_',a.id,'\" id=\"kg_',a.id,'\" data-calendar-group=\"',a.id,'\" ',IF(kgm.id IS NULL,'','checked'),'>') as auswahl,a.mitarbeiternummer,a.name,a.id FROM adresse a 
        INNER JOIN (SELECT adresse FROM adresse_rolle WHERE subjekt='Mitarbeiter' AND (ifnull(bis,'0000-00-00') = '0000-00-00' OR bis >= CURDATE()) GROUP BY adresse) ar on ar.adresse=a.id
        LEFT JOIN kalender_gruppen_mitglieder kgm ON kgm.adresse = a.id AND kgm.kalendergruppe = '$lid'
        ";

        $where = " a.mitarbeiternummer!='' AND a.geloescht!=1";

        $maxrows = 50;
        $disablebuttons = true;
        break;

    }

    $erg = [];

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  /**
   * Kalender constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app,$intern = false) {
    $this->app=$app;
    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","KalenderList");
    $this->app->ActionHandler("data","KalenderData");
    $this->app->ActionHandler("ics","KalenderICS");
    $this->app->ActionHandler("eventdata","KalenderEventData");
    $this->app->ActionHandler("update","KalenderUpdate");
    $this->app->ActionHandler("delete","KalenderDelete");
    $this->app->ActionHandler("taskstatus","KalenderTaskStatus");
    $this->app->ActionHandler("gruppenlist", "KalenderGruppenList");
    $this->app->ActionHandler("gruppenedit", "KalenderGruppenEdit");
    $this->app->ActionHandler("gruppensave", "KalenderGruppenSave");
    $this->app->ActionHandler("gruppendelete", "KalenderGruppenDelete");
    $this->app->ActionHandler("einladung", "KalenderEinladung");
    $this->app->ActionHandler("gruppenzuordnung", "KalenderGruppenZuordnung");

    $this->publicColor = "#3fa848";	
    $this->taskColor = "#ae161e";	
    $this->urlaubColor = ($this->app->erp->GetKonfiguration("mitarbeiterzeiterfassung_calendarcolor")?$this->app->erp->GetKonfiguration("mitarbeiterzeiterfassung_calendarcolor"):"#00ccff");	

    $this->app->ActionHandlerListen($app);
  }


  function KalenderGruppenZuordnung(){
    $adresse  = $this->app->Secure->GetPOST('lid');
    $kalendergruppe   = $this->app->Secure->GetPOST('kalendergruppe');
    $benutzergruppe = '';
    //$benutzergruppe   = $this->app->Secure->GetPOST('benutzergruppe');
    $wert     = $this->app->Secure->GetPOST('wert');


    if($wert == '0'){
      $delete = $this->app->DB->Delete("DELETE FROM kalender_gruppen_mitglieder WHERE adresse = '$adresse' AND benutzergruppe = '$benutzergruppe' AND kalendergruppe = '$kalendergruppe'");
    }elseif($wert == '1'){
      $insert = $this->app->DB->Insert("INSERT INTO kalender_gruppen_mitglieder(adresse,kalendergruppe,benutzergruppe) VALUES('$adresse','$kalendergruppe','$benutzergruppe')");
    }
    $this->app->ExitXentral();
  }

  /**
   * @param string $parsetarget
   */
  public function showKalenderList($parsetarget)
  {
    $select = '';
    $this->app->erp->Headlines('Kalender');
    $this->app->Tpl->Set('TABTEXT',"Kalender");

    $submit = $this->app->Secure->GetPOST("submitForm");
    $mode = $this->app->Secure->GetPOST("mode");
    $eventid = $this->app->Secure->GetPOST("eventid");

    $hookEvent = 0;
    $hookAction = '';

    $titel = $this->app->Secure->GetPOST("titel");
    $datum = $this->app->Secure->GetPOST("datum");
    $datum_bis = $this->app->Secure->GetPOST("datum_bis");
    $allday = $this->app->Secure->GetPOST("allday");
    $public = $this->app->Secure->GetPOST("public");
    $erinnerung = $this->app->Secure->GetPOST("erinnerung");
    $von = $this->app->Secure->GetPOST("von");
    $bis = $this->app->Secure->GetPOST("bis");
    $beschreibung = $this->app->Secure->GetPOST("beschreibung");
    $ort = $this->app->Secure->GetPOST("ort");
    $adresse = $this->app->Secure->GetPOST("adresse");
    $ansprechpartner = $this->app->Secure->GetPOST("ansprechpartner");
    $adresseintern = $this->app->Secure->GetPOST("adresseintern");
    $projekt = $this->app->Secure->GetPOST("projekt");

    $personen = $this->app->Secure->GetPOST("personen");
    $color = $this->app->Secure->GetPOST("color");
    $gruppenkalender = $this->app->Secure->GetPOST("gruppenkalender");

    $ajax = $this->app->Secure->GetGET("ajax");
    $action = $this->app->Secure->GetGET("action");
    $module = $this->app->Secure->GetGET("module");
    $noRedirect = $this->app->Secure->GetPOST("noRedirect");

    if($module==="welcome"&& ($action==="start" || $action === "list" || $action == ""))
    {
      $this->app->Tpl->Set("STARTSMALLKALENDER","<!--");
      $this->app->Tpl->Set("ENDESMALLKALENDER","-->");
      $this->app->Tpl->Set("AUTOSCROLLTO",0);
    } else {
      $this->app->Tpl->Set("AUTOSCROLLTO",1);
    }

    $user = $this->app->User->GetID();
    $eigene_kalendergruppen = $this->app->DB->SelectArr(
      "SELECT kg.* FROM kalender_gruppen kg LEFT JOIN kalender_gruppen_mitglieder kgm ON kg.id = kgm.kalendergruppe 
      LEFT JOIN user u ON u.adresse = kgm.adresse WHERE ausblenden != 1 AND u.id = '$user' ORDER BY bezeichnung"
    );

    if($submit!="") {
      $von_datum =  $this->app->String->Convert("$datum $von", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");
      $bis_datum =  $this->app->String->Convert("$datum_bis $bis", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");

      if($allday=='1') {
        $von_datum = $this->app->String->Convert("$datum 00:00", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");
        $bis_datum = $this->app->String->Convert("$datum_bis 00:00", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");
        //$bis_datum = $datum_bis;
      }

      if($bis_datum < $von_datum){ $bis_datum = $von_datum; }

      $adresse = trim($adresse);
      $rest = explode(" ",$adresse);
      $rest = $rest[0];
      $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE id='$rest' AND geloescht=0 LIMIT 1");

      $ansprechpartner = trim($ansprechpartner);
      $ansprechpartner = explode(" ", $ansprechpartner);
      $ansprechpartner = $ansprechpartner[0];
      $ansprechpartnerId = $this->app->DB->Select("SELECT id FROM ansprechpartner WHERE id = '$ansprechpartner' LIMIT 1");

      $adresseintern = trim($adresseintern);
      $rest = explode(" ",$adresseintern);
      $rest = $rest[0];
      $adresseintern =  $this->app->DB->Select("SELECT id FROM adresse WHERE id='$rest' AND geloescht=0 LIMIT 1");

      $projekt = trim($projekt);
      $projekt =  $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND abkuerzung!='' LIMIT 1");

      if($mode==="new") {

        if($allday){
          $tmpbis = "DATE_ADD('$bis_datum', INTERVAL 1439 MINUTE)";
        } else {
          $tmpbis = "'$bis_datum'";
        }

        // wenn gruppen kalender farbe von erster gruppe verwenden
        $this->app->DB->Insert(
          "INSERT INTO kalender_event (
             ort, bezeichnung, beschreibung, von, bis, allDay, color, public, adresse, ansprechpartner_id,
             adresseintern, angelegtvon, erinnerung, projekt)
           VALUES ('$ort', '$titel', '$beschreibung', '$von_datum', $tmpbis, '$allday', '$color', '$public', '$adresse',
             '$ansprechpartnerId', '$adresseintern', '".$this->app->User->GetAdresse()."', '$erinnerung', '$projekt')"
        );
        $event = $this->app->DB->GetInsertID();
        $calendarActionType = 'added';
        //$this->app->erp->RunHook('kalender_event_hook', 2, $event, $calendarActionType);
        $hookEvent = $event;
        $hookAction = $calendarActionType;
      }

      if($mode==="edit" && is_numeric($eventid)) {
        $this->app->DB->Update(
          "UPDATE kalender_event SET 
            ort = '$ort', bezeichnung = '$titel', beschreibung = '$beschreibung', von = '$von_datum', 
            bis='$bis_datum', allDay = '$allday', color = '$color', public = '$public', adresse = '$adresse', 
            ansprechpartner_id = '$ansprechpartnerId', adresseintern = '$adresseintern', erinnerung = '$erinnerung', projekt = '$projekt' 
           WHERE id = '$eventid' LIMIT 1"
        );

        $calendarActionType = 'modified';
        //$this->app->erp->RunHook('kalender_event_hook', 2, $eventid, $calendarActionType);
        $hookEvent = $eventid;
        $hookAction = $calendarActionType;
        // Personenzuordnung entfernen (alle)
        $this->app->DB->Delete("DELETE FROM kalender_user WHERE event = '$eventid' AND userid > 0");
        // Gruppenzuordnung entfernen (nur Gruppen in denen der User ist)
        $eigene_kalendergruppen_ids = array_column($eigene_kalendergruppen, 'id');
        $this->app->DB->Delete("DELETE FROM kalender_user WHERE event = '$eventid' AND gruppe > 0 AND gruppe IN (".implode(',', $eigene_kalendergruppen_ids).")");
        $event = $eventid;
      }

      if($mode==="delete" && is_numeric($eventid)) {
        $calendarActionType = 'deleted';
        //$this->app->erp->RunHook('kalender_event_hook', 2, $eventid, $calendarActionType);
        $hookEvent = $eventid;
        $hookAction = $calendarActionType;

        $this->app->DB->Delete("DELETE FROM kalender_event WHERE id='$eventid' LIMIT 1");
        $this->app->DB->Delete("DELETE FROM kalender_user WHERE event='$eventid'");
      }

      if($mode==="copy" && is_numeric($eventid)) {
        $cData = $this->app->DB->SelectArr("SELECT * FROM kalender_event WHERE id = '$eventid' LIMIT 1");
        $this->app->DB->Insert(
          "INSERT INTO kalender_event (
             bezeichnung, beschreibung, von, bis, 
             allDay, color, public, adresse, ansprechpartner_id,
             adresseintern, erinnerung, projekt)
           VALUES ('{$cData[0]['bezeichnung']}', '{$cData[0]['beschreibung']}', '{$cData[0]['von']}', '{$cData[0]['bis']}',
             '{$cData[0]['allDay']}', '{$cData[0]['color']}', '{$cData[0]['public']}', '{$cData[0]['adresse']}',
             '{$cData[0]['ansprechpartner_id']}', '{$cData[0]['adresseintern']}', '{$cData[0]['erinnerung']}', '{$cData[0]['projekt']}')"
        );
        $event = $this->app->DB->GetInsertID();
        $calendarActionType = 'added';
        //$this->app->erp->RunHook('kalender_event_hook', 2, $event, $calendarActionType);
        $hookEvent = $event;
        $hookAction = $calendarActionType;
      }

      if(!is_array($personen)) {
        if($personen!="") $personen = array($personen);
        else $personen = null;
      }

      // Ersteller eintragen, wenn keine Personen ausgewählt
      if(!$personen || empty($personen)){
        $personen[]=$this->app->User->GetID();
      }

      // Schreibe Personen
      if(is_numeric($event) && is_array($personen) && count($personen) && $mode!=='delete') {
        $cpersonen = count($personen);
        for($p=0;$p<$cpersonen;$p++) {
          $this->app->DB->Insert("INSERT INTO kalender_user (event, userid) VALUES ('$event', '{$personen[$p]}')");
        }
      }

      if ((int)$hookEvent > 0 && $hookAction !== '') {
          $this->app->erp->RunHook('kalender_event_hook', 2, $hookEvent, $hookAction);
      }


      // Schreibe Gruppenkalender
      if(is_numeric($event) && is_array($gruppenkalender) && count($gruppenkalender) && $mode!=='delete') {
        $cgruppenkalender = count($gruppenkalender);
        for($p=0;$p<$cgruppenkalender;$p++){
          // stelle farbe von kalender eintrag um wenn gruppe (also erste gruppe in auswahl)
          if($p==0){
            $color = $this->app->DB->Select("SELECT farbe FROM kalender_gruppen WHERE id='".$gruppenkalender[$p]."' LIMIT 1");
            if($color!=""){
              $this->app->DB->Update("UPDATE kalender_event SET color='$color' WHERE id='$event' LIMIT 1");

              $calendarActionType = 'modified';
              $this->app->erp->RunHook('kalender_event_hook', 2, $event, $calendarActionType);
            }
          }
          $this->app->DB->Insert("INSERT INTO kalender_user (event, gruppe) VALUES ('$event', '{$gruppenkalender[$p]}')");
        }
      }

      // Aktualisierten bzw. angelegten Event zurückliefern
      if (!empty($ajax) && in_array($mode, ['new', 'edit', 'copy'])) {
        $eventData = $this->app->DB->SelectArr(
          'SELECT e.id, e.bezeichnung AS title, e.beschreibung, e.von AS `start`, e.bis AS `end`, e.allDay, e.color, 
             e.public, e.erinnerung, e.ort, e.adresse, e.ansprechpartner_id, e.adresseintern, e.projekt 
          FROM kalender_event AS e WHERE e.id = ' . (int)$event
        );
        header('Content-Type: application/json; charset=utf8');
        echo json_encode(current($eventData));
        $this->app->ExitXentral();
      }

      // Gelöschte Event-ID zurückliefern
      if (!empty($ajax) && $mode === 'delete') {
        header('Content-Type: application/json; charset=utf8');
        echo json_encode(['deletedEventId' => $eventid]);
        $this->app->ExitXentral();
      }

      $id = $this->app->Secure->GetGET('id');
      $module = $this->app->Secure->GetGET('module');
      $action = 'list';
      if($module != '' && $noRedirect != '1') {
        $this->app->Location->execute('index.php?module='.$module.'&action='.$action.($id?'&id='.$id:''));
      }
    }

    // Personen Auswahl
    // achtung hier keine select options machen
    $users = $this->app->DB->SelectArr("SELECT u.id, a.name as description FROM user u LEFT JOIN adresse a ON a.id=u.adresse WHERE u.activ='1' AND u.kalender_ausblenden!=1 ORDER BY u.username");
    $cusers = !empty($users)?count($users):0;
    $user_out = '';
    for($i=0; $i<$cusers;$i++){
      $user_out .= "<option value=\"{$users[$i]['id']}\" $select>{$users[$i]['description']}</option>";
    }
    $this->app->Tpl->Set('PERSONEN', $user_out);


    // Gruppenkalender Auswahl
    // achtung hier keine select options machen
    $user = $this->app->User->GetID();
    $gruppenkalender = $eigene_kalendergruppen;
    $cgruppenkalender = !empty($gruppenkalender)?count($gruppenkalender):0;
    for($i=0; $i<$cgruppenkalender;$i++){
      $this->app->Tpl->Add("GRUPPENKALENDER","<option value=\"{$gruppenkalender[$i]['id']}\" $select>{$gruppenkalender[$i]['bezeichnung']}</option>");
    }


    $this->app->Tpl->Set('LINKADRESSE',"<a href=\"#\" onclick=\"splitstring = document.getElementById('adresse').value; felder = splitstring.split(' ', 3); if( felder[0] > 0) window.location.href='index.php?module=adresse&action=brief&id=' + felder[0];\" style=\"font-weight:normal;text-decoration:underline; position:absolute;margin-top:5px;margin-left:5px;\"><img src=\"themes/new/images/forward.svg\"></a>");
    if($this->app->erp->RechteVorhanden("adresse","ansprechpartner")){
      $this->app->Tpl->Set('LINKANSPRECHPARTNER', "<a href=\"#\" onclick=\"splitstring = document.getElementById('adresse').value; felder = splitstring.split(' ', 3); if( felder[0] > 0) window.location.href='index.php?module=adresse&action=ansprechpartner&id=' + felder[0];\" style=\"font-weight:normal;text-decoration:underline;position:absolute;margin-top:5px;margin-left:5px;\"><img src=\"themes/new/images/forward.svg\"></a>");
    }

    $cgruppenkalender = !empty($gruppenkalender)?count($gruppenkalender):0;
    for($gk=0;$gk<$cgruppenkalender;$gk++)
    {
      if($this->app->User->GetParameter('kalender_gruppe_'.$gruppenkalender[$gk]['id'])=="1") $checked="checked"; else $checked="";
      $this->app->Tpl->Add("GRUPPENKALENDERAUSWAHL",'
      <input type="checkbox" value="1" name="kalender_gruppe_'.$gruppenkalender[$gk]['id'].'" id="kalender_gruppe_'.$gruppenkalender[$gk]['id'].'" '.$checked.'>
      <label  name="kalender_gruppe_'.$gruppenkalender[$gk]['id'].'" id="kalender_gruppe_'.$gruppenkalender[$gk]['id'].'" for="kalender_gruppe_'.$gruppenkalender[$gk]['id'].'">
      &nbsp;
        <div style="width:15px;height:15px;border: 1px solid #656565; border-radius: 2px;  background:'.$gruppenkalender[$gk]['farbe'].';display: inline-block;">&nbsp;</div>
        &nbsp;<div class="grup-name">'.$gruppenkalender[$gk]['bezeichnung'].'</div><br></label>');
      $this->app->YUI->AutoSaveUserParameter('kalender_gruppe_'.$gruppenkalender[$gk]['id'],'kalender_gruppe_'.$gruppenkalender[$gk]['id'],"$('#calendar').fullCalendar('refetchEvents');");
    }



    $this->app->YUI->AutoSaveUserParameter("aufgaben","adresse_kalender_aufgaben","$('#calendar').fullCalendar('refetchEvents');");

    if($this->app->User->GetParameter("adresse_kalender_aufgaben")=="1")
    {
      $this->app->Tpl->Set("AUFGABENCHECKED","checked");
    }


    $this->app->YUI->AutoSaveUserParameter("termine","adresse_kalender_termine","$('#calendar').fullCalendar('refetchEvents');");

    if($this->app->User->GetParameter("adresse_kalender_termine")=="1")
    {
      $this->app->Tpl->Set("TERMINECHECKED","checked");
    }


    $this->app->YUI->AutoSaveUserParameter("projekte","adresse_kalender_projekte","$('#calendar').fullCalendar('refetchEvents');");

    if($this->app->User->GetParameter("adresse_kalender_projekte")=="1")
    {
      $this->app->Tpl->Set("PROJEKTECHECKED","checked");
    }


    $this->app->YUI->AutoSaveUserParameter("urlaub","adresse_kalender_urlaub","$('#calendar').fullCalendar('refetchEvents');");

    if($this->app->User->GetParameter("adresse_kalender_urlaub")=="1")
    {
      $this->app->Tpl->Set("URLAUBCHECKED","checked");
    }


    if($this->app->erp->ModulVorhanden("serviceauftrag")){
      $this->app->YUI->AutoSaveUserParameter("serviceauftrag","adresse_kalender_serviceauftrag","$('#calendar').fullCalendar('refetchEvents');");
      if($this->app->User->GetParameter("adresse_kalender_serviceauftrag")=="1")
      {
        $this->app->Tpl->Set("SERVICEAUFTRAGCHECKED","checked");
      }
      $this->app->Tpl->Set("SERVICEAUFTRAGKALENDER",'<input type="checkbox" class="auswahl" value="1" name="serviceauftrag" id="serviceauftrag" [SERVICEAUFTRAGCHECKED]>
                    <label id="serviceauftrag" class="auswahl" for="serviceauftrag">Serviceauftrag</label>');
    }

    if($noRedirect == '1') {
      $return['eventid'] = $event;
      echo json_encode($return);
      $this->app->ExitXentral();
    }
    $defaultCalendarColor = $this->app->User->GetParameter("welcome_defaultcolor_fuer_kalender");
    if($defaultCalendarColor=="")
    {
      $defaultCalendarColor = $this->app->DB->Select("SELECT defaultcolor FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    }
    if($defaultCalendarColor=="")
    {
      $defaultCalendarColor = '#0B8092';
    }

    $this->app->Tpl->Set("DEFAULTKALENDERCOLOR",$defaultCalendarColor);
    $this->app->YUI->ColorPicker("color");
    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("datum_bis");
    $this->app->YUI->TimePicker("von");
    $this->app->YUI->TimePicker("bis");
    $this->app->YUI->AutoComplete("adresse", "adresse", 0,"","","","","TerminForm");
    $this->app->YUI->AutoComplete("adresseintern", "adresse", 0,"","","","","TerminForm");
    $this->app->YUI->AutoComplete("projekt", "projektname", 1,"","","","","TerminForm");
    $this->app->YUI->CkEditor("einladungtext", "internal", array("height" => "250"));

    $this->app->YUI->AutoCompleteAdd("einladungcc", "emailname");
    $this->app->Tpl->Parse($parsetarget, "kalender.tpl");
  }

  public function KalenderList()
  {
    $this->trySynchronizeGoogleChanges();

    $this->app->Tpl->Set('CALENDAR_DAYNAMES','["{|Sonntag|}", "{|Montag|}", "{|Dienstag|}", "{|Mittwoch|}",
        "{|Donnerstag|}", "{|Freitag|}", "{|Samstag|}"]');
    $this->app->Tpl->Set('CALENDAR_MONTHNAMES','["{|Januar|}", "{|Februar|}", "{|März|}", "{|April|}", "{|Mai|}",
        "{|Juni|}", "{|Juli|}", "{|August|}", "{|September|}", "{|Oktober|}", "{|November|}", "{|Dezember|}"]');
    $this->app->Tpl->Set('CALENDAR_TODAY','{|Heute|}');
    $this->app->Tpl->Set('CALENDAR_MONTH','{|Monat|}');
    $this->app->Tpl->Set('CALENDAR_WEEK','{|Woche|}');
    $this->app->Tpl->Set('CALENDAR_DAY','{|Tag|}');
    $this->showKalenderList('TAB1');
    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("datum_bis");

    $this->app->YUI->CkEditor("e_notizen","belege",array("width"=>"625"));
    $this->app->YUI->CkEditor("e_beschreibung","belege",array("width"=>"420"));
    $this->app->YUI->DatePicker("e_datum");
    $this->app->YUI->TimePicker("e_zeit");
    $this->app->Tpl->Parse('TAB1','aufgaben_popup.tpl');

    //		$this->app->Tpl->Parse(TAB1,"kalender.tpl");
    $this->app->Tpl->Set('TABTEXT',"");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
    $this->app->erp->StartseiteMenu();
  }

  public function KalenderICS()
  {
    $findlogin = $this->app->DB->Select("SELECT id FROM user WHERE username='".$this->app->DB->real_escape_string($_SERVER['PHP_AUTH_USER'])."' AND username!=''
        AND kalender_aktiv='1' AND kalender_passwort='".$this->app->DB->real_escape_string($_SERVER['PHP_AUTH_PW'])."' AND kalender_passwort!='' AND `activ`='1' LIMIT 1");

    $this->app->erp->Protokoll("Benutzer: ".$this->app->DB->real_escape_string($_SERVER['PHP_AUTH_USER']));

    //$findlogin='1000';
    //if ($_SERVER['PHP_AUTH_USER']=="sauterbe" && $_SERVER['PHP_AUTH_PW']=="ZakledhLs")
    if($findlogin > 0)	
    {
      $event = new ICS("wawision");

      $data = $this->app->DB->SelectArr("SELECT DISTINCT ke.id, ort,beschreibung, bezeichnung AS title, DATE_FORMAT(von,'%Y-%m-%d %H:%i') AS start, 
          DATE_FORMAT(bis,'%Y-%m-%d %H:%i') AS end, allDay, color, public,erinnerung
          FROM kalender_user AS ku
          LEFT JOIN kalender_event AS ke ON ke.id=ku.event
          WHERE (ku.userid='$findlogin' OR ke.public='1')  AND ke.von!='0000-00-00 00:00:00' AND ke.bis!='0000-00-00 00:00:00' ORDER by von");
      $cdata = !empty($data)?count($data):0;
      for($i=0;$i<$cdata;$i++)
      {
        //	$data[$i]['color'] = (($data[$i]['public']=='1')?$this->publicColor:$data[$i]['color']);
        $data[$i]['allDay'] = (($data[$i]['allDay']=='1')?true:false);
        $data[$i]['public'] = (($data[$i]['public']=='1')?true:false);
        $data[$i]['erinnerung'] = (($data[$i]['erinnerung']=='1')?true:false);
        $data[$i]['title'] = $this->app->erp->ReadyForPDF($data[$i]['title']);
        $data[$i]['ort'] = $this->app->erp->ReadyForPDF($data[$i]['ort']);
        $data[$i]['beschreibung'] = str_ireplace("\x0D", "", $data[$i]['beschreibung']); 
        $data[$i]['beschreibung'] = str_replace("\n", "\\n",$data[$i]['beschreibung']);
        $data[$i]['beschreibung'] = str_replace("\r\n", "\\n",$data[$i]['beschreibung']);
        $data[$i]['beschreibung'] = $this->app->erp->ReadyForPDF($data[$i]['beschreibung']);


        $event->AddEvent($data[$i]['id'],$data[$i]['start'],$data[$i]['end'],$data[$i]['title'],$data[$i]['beschreibung'],$data[$i]['ort']);
      }
      //$event->AddEvent(1,"2014-05-18 11:00","2014-05-18 21:00","Test 444 Event","This is an event made by Benedikt","Augsburg");
      //$event->AddEvent(2,"2014-05-18 09:00","2014-05-18 09:30","Test 3 Event","This is an event made by Benedikt","Augsburg");
      $event->show();
      $this->app->ExitXentral();
    }

    header('WWW-Authenticate: Basic realm="WaWision Kalender"');
    header('HTTP/1.0 401 Unauthorized');
    $this->app->ExitXentral();
  }

  function KalenderTaskStatus()
  {
    $user = $this->app->User->GetID();
    $data = $this->app->DB->SelectArr("SELECT kalender_aufgaben , a.id FROM adresse AS a
        LEFT JOIN user as u ON u.adresse=a.id
        WHERE u.id='$user' LIMIT 1");
    $new_status = '';
    if($data[0]['kalender_aufgaben']=='1'){
      $new_status = '0';
    }
    else{
      $new_status = '1';
    }
    $this->app->DB->Update("UPDATE adresse SET kalender_aufgaben='$new_status' WHERE id='{$data[0]['id']}' LIMIT 1");
    $this->app->ExitXentral();
  }




  function KalenderData()
  {
    $user = $this->app->User->GetID();
    $useradresse = $this->app->User->GetAdresse();
    $start = date("Y-m-d H:i:s", $this->app->Secure->GetGET('start'));
    $end = date("Y-m-d H:i:s", $this->app->Secure->GetGET('end'));

    $start_datum = date("Y-m-d", $this->app->Secure->GetGET('start'));
    $end_datum = date("Y-m-d", $this->app->Secure->GetGET('end'));

    $gruppenkalender = $this->app->DB->SelectArr("SELECT * FROM kalender_gruppen");
    $hideserviceauftrag = false;
    $subwhere = "";
    $cgruppenkalender = !empty($gruppenkalender)?count($gruppenkalender):0;
    for($i=0;$i<$cgruppenkalender;$i++)
    {
      if($this->app->User->GetParameter("kalender_gruppe_".$gruppenkalender[$i]['id'])=="1") {
        if($subwhere!="") $subwhere .= " OR ";
        $subwhere .= " (ku.gruppe='".$gruppenkalender[$i]['id']."' AND kg.id IN(SELECT kgm.kalendergruppe FROM kalender_gruppen_mitglieder kgm LEFT JOIN user u ON u.adresse = kgm.adresse WHERE u.id='$user')) ";
      }
    }
    if($subwhere=="") {
      $subwhere = " AND (ku.userid='$user' OR ke.public=1 OR (ku.gruppe > 0 AND kg.id IN (SELECT kgm.kalendergruppe FROM kalender_gruppen_mitglieder kgm LEFT JOIN user u ON u.adresse = kgm.adresse WHERE u.id='$user')))";
    } else {
      //$hideserviceauftrag = true; 2018-10-05 BS entfernt
      $subwhere = " AND (".$subwhere.") ";
    }


    $nurmeine = $this->app->User->GetParameter("adresse_kalender_termine");

    if($nurmeine > 0)
    {
      $data = $this->app->DB->SelectArr("SELECT DISTINCT ke.id, 'kalender_event' AS typ, ort, beschreibung, ke.bezeichnung AS title, von AS start, bis AS end, allDay, color, public,erinnerung,adresse,ansprechpartner_id,adresseintern,projekt
        FROM kalender_event AS ke
        LEFT JOIN kalender_user AS ku ON ke.id=ku.event
        LEFT JOIN kalender_gruppen AS kg ON kg.id = ku.gruppe
        WHERE (ku.userid='$user' OR (ku.gruppe > 0 AND kg.id IN (SELECT kgm.kalendergruppe FROM kalender_gruppen_mitglieder kgm LEFT JOIN user u ON u.adresse = kgm.adresse WHERE u.id='$user'))) AND (ke.von < '$end' AND (ke.bis >= '$start' OR ke.bis='0000-00-00 00:00:00') ) GROUP by ke.id ORDER by start");
    } else {

      $data = $this->app->DB->SelectArr("SELECT DISTINCT ke.id, 'kalender_event' AS typ, ort, beschreibung, ke.bezeichnung AS title, von AS start, bis AS end, allDay, color, public,erinnerung,adresse,ansprechpartner_id,adresseintern,projekt,kg.farbe
        FROM kalender_event AS ke
        LEFT JOIN kalender_user ku ON ke.id=ku.event
        LEFT JOIN kalender_gruppen kg ON kg.id=ku.gruppe
        WHERE (ke.von < '$end' AND (ke.bis >= '$start' OR (ke.bis='0000-00-00 00:00:00' AND ke.von!='0000-00-00 00:00:00') AND NOT (ke.von < '$start' AND ke.bis='0000-00-00 00:00:00'))  ) $subwhere GROUP by ke.id ORDER by start");
    }
    $cdata = !empty($data)?count($data):0;
    for($i=0;$i<$cdata;$i++)
    {
      $data[$i]['allDay'] = (($data[$i]['allDay']=='1')?true:false);
      $data[$i]['public'] = (($data[$i]['public']=='1')?true:false);
      $data[$i]['erinnerung'] = (($data[$i]['erinnerung']=='1')?true:false);
      $data[$i]['title'] = $this->app->erp->ReadyForPDF($data[$i]['title']);
      $data[$i]['ort'] = $this->app->erp->ReadyForPDF($data[$i]['ort']);
      $data[$i]['adresse'] = $this->app->erp->ReadyForPDF($data[$i]['adresse']);
      $data[$i]['ansprechpartner'] = $this->app->erp->ReadyForPDF($data[$i]['ansprechpartner_id']);
      $data[$i]['adresseintern'] = $this->app->erp->ReadyForPDF($data[$i]['adresseintern']);
      $data[$i]['projekt'] = $this->app->erp->ReadyForPDF($data[$i]['adresseintern']);
      $data[$i]['beschreibung'] = $this->app->erp->ReadyForPDF($data[$i]['projekt']);
      if($data[$i]['farbe']!="")
      $data[$i]['color'] = $data[$i]['farbe'];
    }

    $aufgaben_visible = $this->app->DB->Select("SELECT kalender_aufgaben FROM adresse AS a
        LEFT JOIN user as u ON u.adresse=a.id
        WHERE u.id='$user' LIMIT 1");

    // Mindesthaltbarkeitsdatum einblenden
    if($this->app->erp->RechteVorhanden("mhdwarning","list"))	
    {
        $sql = "SELECT a.id as id,a.name_de, a.nummer, SUM(lm.menge) as menge, lm.mhddatum
        FROM lager_mindesthaltbarkeitsdatum lm
        LEFT JOIN artikel a ON a.id=lm.artikel LEFT JOIN lager_platz l ON l.id=lm.lager_platz WHERE DATE_FORMAT(lm.datum,'%Y-%m') <= DATE_FORMAT('$end_datum','%Y-%m') OR DATE_FORMAT(lm.datum,'%Y-%m') >= DATE_FORMAT('$start_datum','%Y-%m') GROUP By lm.mhddatum, a.id";

      $tmpartikel = $this->app->DB->SelectArr($sql);
      $ctmpartikel = !empty($tmpartikel)?count($tmpartikel):0;
      for($ij=0;$ij<$ctmpartikel;$ij++)
      {
        $data[] = array('id' => -3,
            'title'=>round($tmpartikel[$ij]['menge'],0)." x ".$this->app->erp->ReadyForPDF($tmpartikel[$ij]['name_de']),
            'start'=> $tmpartikel[$ij]['mhddatum'],
            'end'=> $tmpartikel[$ij]['mhddatum'],
            'allDay'=>true,
            'color'=>'#FA5858',
            'public'=>'1',
            'task'=>$tmpartikel[$ij]['id']);			
      }
    }


    //Geburtstage einblenden


      $tmp = explode('-',$start); 
      $startyear = $tmp[0];

      $tmp = explode('-',$end); 
      $endyear = $tmp[0];

      $types = array('adresse','ansprechpartner');

      foreach($types as $key)
      {

        if($key=="adresse") $p_key = "a.id";
        else if($key=="ansprechpartner") $p_key = "a.adresse";

      if($endyear>$startyear)
      {   

        //0111   1230 // neues jahr
        $sql = "SELECT $p_key as id,a.name,DATE_FORMAT(a.geburtstag,'%m-%d') as datum,
          YEAR('$end') - YEAR(a.geburtstag) - IF(DAYOFYEAR('$end') < DAYOFYEAR(CONCAT(YEAR('$end'),DATE_FORMAT(a.geburtstag, '-%m-%d'))),1,0) as alterjahre
          FROM ".$key." a WHERE DATE_FORMAT(a.geburtstag,'%m%d') <= date_format('$end','%m%d') AND a.geloescht!='1' AND a.geburtstag!='0000-00-00' AND a.geburtstagkalender=1";

        $tmpartikel = $this->app->DB->SelectArr($sql);
        $ctmpartikel = !empty($tmpartikel)?count($tmpartikel):0;
        for($ij=0;$ij<$ctmpartikel;$ij++)
        {
          $data[] = array('id' => -4,
              //'title'=>"Geburtstag: ".$this->app->erp->ReadyForPDF($tmpartikel[$ij]['name'])." (".$tmpartikel[$ij]['alterjahre'].")",
              'title'=>"Geburtstag: ".$this->app->erp->ReadyForPDF($tmpartikel[$ij]['name']),
              'start'=> $endyear."-".$tmpartikel[$ij]['datum'],
              'end'=> $endyear."-".$tmpartikel[$ij]['datum'],
              'allDay'=>true,
              'color'=>'#FA5858',
              'public'=>'1',
              'task'=>$tmpartikel[$ij]['id']);			
        }


        //0111   1230 // altes jahr
        $sql = "SELECT $p_key as id,a.name,DATE_FORMAT(a.geburtstag,'%m-%d') as datum,
          YEAR('$end') - YEAR(a.geburtstag) - IF(DAYOFYEAR('$end') < DAYOFYEAR(CONCAT(YEAR('$end'),DATE_FORMAT(a.geburtstag, '-%m-%d'))),1,0) as alterjahre
          FROM ".$key." a WHERE DATE_FORMAT(a.geburtstag,'%m%d') <= 1231 AND a.geloescht!='1' AND a.geburtstag!='0000-00-00' AND a.geburtstagkalender=1";

        $tmpartikel = $this->app->DB->SelectArr($sql);
        $ctmpartikel = !empty($tmpartikel)?count($tmpartikel):0;
        for($ij=0;$ij<$ctmpartikel;$ij++)
        {
          $data[] = array('id' => -4,
              'title'=>"Geburtstag: ".$this->app->erp->ReadyForPDF($tmpartikel[$ij]['name'])." (".$tmpartikel[$ij]['alterjahre'].")",
              'start'=> $startyear."-".$tmpartikel[$ij]['datum'],
              'end'=> $startyear."-".$tmpartikel[$ij]['datum'],
              'allDay'=>true,
              'color'=>'#FA5858',
              'public'=>'1',
              'task'=>$tmpartikel[$ij]['id']);			
        }


      } else {
        $sql = "SELECT $p_key as id,a.name,DATE_FORMAT(a.geburtstag,'%m-%d') as datum,
          YEAR('$end') - YEAR(a.geburtstag) - IF(DAYOFYEAR('$end') < DAYOFYEAR(CONCAT(YEAR('$end'),DATE_FORMAT(a.geburtstag, '-%m-%d'))),1,0) as alterjahre
          FROM ".$key." a WHERE DATE_FORMAT(a.geburtstag,'%m%d') <= date_format('$end','%m%d') AND DATE_FORMAT(a.geburtstag,'%m%d') >= date_format('$start','%m%d') AND a.geloescht!='1' AND a.geburtstag!='0000-00-00' AND a.geburtstagkalender=1";

        $tmpartikel = $this->app->DB->SelectArr($sql);
        $ctmpartikel = !empty($tmpartikel)?count($tmpartikel):0;
        for($ij=0;$ij<$ctmpartikel;$ij++)
        {
          $data[] = array('id' => -4,
              'title'=>"Geburtstag: ".$this->app->erp->ReadyForPDF($tmpartikel[$ij]['name'])." (".$tmpartikel[$ij]['alterjahre'].")",
              'start'=> $startyear."-".$tmpartikel[$ij]['datum'],
              'end'=> $startyear."-".$tmpartikel[$ij]['datum'],
              'allDay'=>true,
              'color'=>'#FA5858',
              'public'=>'1',
              'task'=>$tmpartikel[$ij]['id']);			
        }
      }
      }
    



    //arbeitsfreie tage einblenden
      $sql = "SELECT a.id as id,a.bezeichnung, a.datum,a.typ FROM arbeitsfreietage a WHERE a.datum <='$end_datum' AND a.datum >='$start_datum'";

      $tmpartikel = $this->app->DB->SelectArr($sql);
      $ctmpartikel = !empty($tmpartikel)?count($tmpartikel):0;
      for($ij=0;$ij<$ctmpartikel;$ij++)
      {
        $data[] = array('id' => -7,
            'title'=>ucfirst($tmpartikel[$ij]['typ']).": ".$this->app->erp->ReadyForPDF($tmpartikel[$ij]['bezeichnung']),
            'start'=> $tmpartikel[$ij]['datum'],
            'end'=> $tmpartikel[$ij]['datum'],
            'allDay'=>true,
            'color'=>'#FA5858',
            'public'=>'1',
            'task'=>$tmpartikel[$ij]['id']);			
      }

    // Serviceauftrag
      $serviceauftrag_visible = $this->app->User->GetParameter("adresse_kalender_serviceauftrag");
      if($this->app->erp->ModulVorhanden("serviceauftrag")){
        if(!$hideserviceauftrag && $serviceauftrag_visible){
          $tmpserviceauftragsql = "SELECT sa.id, sa.datum, sa.abschluss_bis, a.name,sa.bearbeiter FROM serviceauftrag sa LEFT JOIN adresse a ON sa.adresse = a.id WHERE sa.datum >= '$start_datum' AND (sa.abschluss_bis = '0000-00-00' OR sa.abschluss_bis < '$end_datum')";
          if($nurmeine > 0){
            $tmpserviceauftragsql .= " AND sa.bearbeiter = '$useradresse'";
          }
          $tmpserviceauftrag = $this->app->DB->SelectArr($tmpserviceauftragsql);
          $ctmpserviceauftrag = !empty($tmpserviceauftrag)?count($tmpserviceauftrag):0;
          for($ij=0;$ij<$ctmpserviceauftrag;$ij++)
          {

            $defaultCalendarColor = $this->app->DB->Select("SELECT defaultcolor FROM user WHERE adresse='".$tmpserviceauftrag[$ij]['bearbeiter']."' AND defaultcolor!='' LIMIT 1");
            if($defaultCalendarColor=="") $defaultCalendarColor = "#DD00DD";

            $data[] = array('id' => -6,
                'title'=> "Serviceauftrag: ".$tmpserviceauftrag[$ij]['name'],
                'start'=> $tmpserviceauftrag[$ij]['datum'],
                'end'=> $tmpserviceauftrag[$ij]['abschluss_bis'],
                'allDay'=>true,
                'color'=>$defaultCalendarColor,
                'public'=>'1',
                'task'=>$tmpserviceauftrag[$ij]['id']);
          }
        }
      }



    $aufgaben_visible = $this->app->User->GetParameter("adresse_kalender_aufgaben");
    // Aufgabene einblenden
    if($aufgaben_visible=='1') {
      // Aufgaben hinzufügen
      $tasks = $this->app->DB->SelectArr("SELECT DISTINCT a.id, a.aufgabe, a.abgabe_bis, a.ganztags, ma.name as mitarbeiter, ku.name as kunde FROM aufgabe AS a 
          LEFT JOIN user AS u ON u.adresse=a.adresse
          LEFT JOIN adresse AS ku ON ku.id=a.kunde
          LEFT JOIN adresse AS ma ON ma.id=a.adresse
          WHERE (u.id='$user' OR oeffentlich='1') AND a.status='offen' AND a.abgabe_bis>='$start' 
          AND a.abgabe_bis<='$end'");
      $ctasks = !empty($tasks)?count($tasks):0;
      for($i=0;$i<$ctasks;$i++)
      {
        $allday = (($tasks[$i]['ganztags']=='1') ? true : false);
        $data[] = array('id' => -2,
            'title'=>$tasks[$i]['mitarbeiter'].": ".$this->app->erp->ReadyForPDF($tasks[$i]['aufgabe']).($tasks[$i]['kunde']!=""?" (".$tasks[$i]['kunde'].")":""),
            'start'=> $tasks[$i]['abgabe_bis'],
            'end'=> $tasks[$i]['abgabe_bis'],
            'allDay'=>$allday,
            'color'=>$this->taskColor,
            'public'=>'',
            'task'=>$tasks[$i]['id']);
      }

    }
  
    $urlaub_visible = $this->app->User->GetParameter("adresse_kalender_urlaub");
    // Aufgabene einblenden
    if($urlaub_visible=='1') {
      // Aufgaben hinzufügen
      $tasks = $this->app->DB->SelectArr("SELECT DISTINCT ms.id, a2.name, ms.datum,ms.kuerzel FROM mitarbeiterzeiterfassung_sollstunden ms 
          LEFT JOIN user AS u ON u.adresse=ms.adresse
          LEFT JOIN adresse a2 ON a2.id=ms.adresse
          WHERE ms.datum>='$start' 
          AND ms.datum<='$end' AND (ms.kuerzel='U' OR ms.kuerzel='K' OR ms.kuerzel='N')");
      $ctasks = !empty($tasks)?count($tasks):0;
      for($i=0;$i<$ctasks;$i++)
      {
        switch($tasks[$i]['kuerzel'])
        {
          case "U": $kuerzel="Abwesend"; break;
          case "N": $kuerzel="Abwesend"; break;
          case "K": $kuerzel="Abwesend"; break;
        } 
        $data[] = array('id' => -7,
            'title'=>$kuerzel.": ".$this->app->erp->ReadyForPDF($tasks[$i]['name']),
            'start'=> $tasks[$i]['datum'],
            'end'=> $tasks[$i]['datum'],
            'allDay'=>1,
            'color'=>$this->urlaubColor,
            'public'=>'1',
            'task'=>$tasks[$i]['id']);
      }

    }


   $projekte_visible = $this->app->User->GetParameter("adresse_kalender_projekte");
    // Aufgabene einblenden
    if($projekte_visible=='1') {
      // Aufgaben hinzufügen

      $nureigene = $this->app->User->GetParameter("adresse_kalender_termine");
      if($nureigene=="1")
      {
        $tasks = $this->app->DB->SelectArr("SELECT DISTINCT a.id, a.aufgabe, a.startdatum,a.farbe,a.projekt FROM arbeitspaket AS a 
          LEFT JOIN user AS u ON u.adresse=a.adresse
          WHERE (u.id='$user') AND a.startdatum>='$start' 
          AND a.startdatum <='$end'");
      } else {
        $tasks = $this->app->DB->SelectArr("SELECT DISTINCT a.id, a.aufgabe, a.startdatum,a.farbe,a.projekt FROM arbeitspaket AS a 
          LEFT JOIN user AS u ON u.adresse=a.adresse
          LEFT JOIN projekt p ON a.projekt=p.id
          WHERE a.startdatum>='$start'  AND p.oeffentlich=1
          AND a.startdatum <='$end'");
      }
      $ctasks = !empty($tasks)?count($tasks):0;
      for($i=0;$i<$ctasks;$i++)
      {
        $data[] = array('id' => -5,
            'title'=>"Teilprojekt Start: ".$this->app->erp->ReadyForPDF($tasks[$i]['aufgabe']),
            'start'=> $tasks[$i]['startdatum'],
            'end'=> $tasks[$i]['startdatum'],
            'allDay'=>true,
            'color'=>$tasks[$i]['farbe'],
            'public'=>'',
            'task'=>$tasks[$i]['projekt']);
      }
      $tasks = $this->app->DB->SelectArr("SELECT DISTINCT a.id, a.aufgabe, a.abgabedatum,a.farbe,a.projekt FROM arbeitspaket AS a 
          LEFT JOIN user AS u ON u.adresse=a.adresse
          WHERE (u.id='$user') AND a.abgabedatum>='$start' 
          AND a.abgabedatum <='$end'");
      $ctasks = !empty($tasks)?count($tasks):0;
      for($i=0;$i<$ctasks;$i++)
      {
        $data[] = array('id' => -5,
            'title'=>"Teilprojekt Abgabe: ".$this->app->erp->ReadyForPDF($tasks[$i]['aufgabe']),
            'start'=> $tasks[$i]['abgabedatum'],
            'end'=> $tasks[$i]['abgabedatum'],
            'allDay'=>true,
            'color'=>$tasks[$i]['farbe'],
            'public'=>'',
            'task'=>$tasks[$i]['projekt']);
      }


    }
    header('Content-type: application/json');		
    echo json_encode($data);
    $this->app->ExitXentral();
  }

  function KalenderUpdate() 
  {
    $id = $this->app->Secure->GetGET("id");
    $task = $this->app->Secure->GetGET("task");

    if(is_numeric($id) && $id > 0) {
      $start = $this->app->Secure->GetGET("start");
      $end = $this->app->Secure->GetGET("end");

      $allday = $this->app->Secure->GetGET("allDay");
      if($allday=="true")
      {
        //$this->app->DB->Update("UPDATE kalender_event SET von='$start', bis=DATE_ADD('$end', INTERVAL 1439 MINUTE) WHERE id='$id' LIMIT 1");
        $this->app->DB->Update("UPDATE kalender_event SET von='$start', bis='$end' WHERE id='$id' LIMIT 1");

        $calendarActionType = 'modified';
        $this->app->erp->RunHook('kalender_event_hook', 2, $id, $calendarActionType);
      }
      else
      {
        $this->app->DB->Update("UPDATE kalender_event SET von='$start', bis='$end' WHERE id='$id' LIMIT 1");

        $calendarActionType = 'modified';
        $this->app->erp->RunHook('kalender_event_hook', 2, $id, $calendarActionType);
      }
    }

    if(is_numeric($task) && $task > 0) {
      $start = $this->app->Secure->GetGET("start");
      // jjjj-mm-tt ss-mm-ss  -> jjjj-mm-tt ss:mm:ss

      $allday_db = (($allday=='true') ? '1' : '0');
      $converted = $this->app->String->Convert($start, "%1-%2-%3 %4-%5-%6", "%1-%2-%3 %4:%5:%6"); 
      $this->app->DB->Update("UPDATE aufgabe SET abgabe_bis='$converted', ganztags='$allday_db' WHERE id='$task' LIMIT 1");
    }

    $this->app->ExitXentral();
  }

  function KalenderEventData() 
  {
    $event = $this->app->Secure->GetGET("id");
    $cmd = $this->app->Secure->GetGET("cmd");
    header('Content-type: application/json');
    switch($cmd) {
      case "getEinladung":
        $data = $this->app->DB->SelectRow("SELECT k.adresse, k.adresseintern, IFNULL(a.sprache,''),k.projekt,k.bezeichnung,k.beschreibung,k.ort,
          DATE_FORMAT(k.von,'%d.%m.%Y %H:%i') as von, DATE_FORMAT(k.bis,'%d.%m.%Y %H:%i') as bis 
          FROM kalender_event k LEFT JOIN adresse a ON a.id=k.adresse WHERE k.id='" . $event . "' LIMIT 1");
        $sprache = $data['sprache'];
        $projekt = 0;//$data['projekt'];


        $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='" . $data['adresse'] . "' AND geloescht!=1 LIMIT 1");
        $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='" . $data['adresse'] . "' AND geloescht!=1 LIMIT 1");

        $vorlage = $this->app->erp->Geschaeftsbriefvorlage($sprache, "EinladungKalender", $projekt);

        foreach ($data as $key => $value) {
          $vorlage['betreff'] = str_replace('{' . strtoupper($key) . '}', $value, $vorlage['betreff']);
          $vorlage['text'] = str_replace('{' . strtoupper($key) . '}', $value, $vorlage['text']);
        }

        // jetzt holen wir alle Email-Adressen
        $emailadresse[$data['adresse']] = $data['adresse'];
        $emailadresse[$data['adresseintern']] = $data['adresseintern'];
        $dataPersonen = $this->app->DB->SelectArr("SELECT a.id as userid FROM kalender_user ku  LEFT JOIN user u ON u.id=ku.userid LEFT JOIN adresse a ON a.id=u.adresse WHERE ku.event='" . $event . "'");
        if (is_array($dataPersonen)) {
          foreach ($dataPersonen as $person) {
            if ($person['userid'] > 0)
              $emailadressenlist[$person['userid']] = $person['userid'];
          }
        }
        $emailadressen = $this->app->DB->SelectArr("SELECT id, name, email FROM adresse WHERE email != '' AND id IN (" . implode(",", $emailadressenlist) . ") AND geloescht!='1'");
        $ret = array();
        if ($to != "") {
          if ($to_name != "")
            $ret['einladungcc'] = $to_name . " <" . $to . ">,";
          else
            $ret['einladungcc'] = $to;
        }

        foreach ($emailadressen as $email) {
          if ($email['name'] != "")
            $ret['einladungcc'] .= $email['name'] . " <" . $email['email'] . ">,";
          else
            $ret['einladungcc'] .= $email['email'] . ",";
        }
        $ret['einladungcc'] = rtrim($ret['einladungcc'], ",");


        $ret['betreff'] = $vorlage['betreff'];
        $ret['text'] = $vorlage['text'];
        $ret['status'] = 1;
        $ret['statusText'] = "";
        echo json_encode($ret);
        $this->app->ExitXentral();
        break;

      case "sendEinladung" :
        $betreff = $this->app->Secure->GetPOST("betreff");
        $text = $this->app->Secure->GetPOST("text");
        $emailcc= $this->app->Secure->GetPOST("emailcc");
        $id = $this->app->Secure->GetPOST("id");
        if($emailcc=="")
        {
          $ret['status'] = 0;
          $ret['statusText'] = "Bitte mindestens einen Empfänger angeben!";
        }
        else if($this->KalenderMail($id,$betreff,$text,$emailcc))
        {
          $ret['status'] = 1;
          $ret['statusText'] = "Einladung wurde per Mail versendet.";
        } else {
          $ret['status'] = 0;
          $ret['statusText'] = "Es gab einen Fehler beim Versenden der Einladung per Mail!";
        }
        echo json_encode($ret);
        $this->app->ExitXentral();
      break;

      default:

        if (is_numeric($event) && $event > 0) {
          $data = $this->app->DB->SelectArr("SELECT id, ort, bezeichnung AS titel, beschreibung, von, bis, allDay, color, public,erinnerung,adresse,ansprechpartner_id,adresseintern,projekt FROM kalender_event WHERE id='$event' LIMIT 1");
          $personen = $this->app->DB->SelectArr("SELECT DISTINCT ku.userid, a.name FROM kalender_user AS ku
          LEFT JOIN user AS u ON u.id=ku.userid 
          LEFT JOIN adresse a ON a.id=u.adresse
          WHERE ku.event='$event' ORDER BY u.username ");

          $gruppenkalender = $this->app->DB->SelectArr("SELECT DISTINCT ku.gruppe as kalendergruppe FROM kalender_user AS ku
          LEFT JOIN kalender_gruppen kg ON kg.id=ku.gruppe
          WHERE ku.event='$event' AND ku.gruppe > 0 ORDER BY kg.bezeichnung ");
        }

        $data[0]['adressid'] = $data[0]['adresse'];
        $data[0]['adresse'] = $this->app->DB->Select("SELECT if(a.lieferantennummer,CONCAT(a.id,' ',a.name,' (Kdr: ',a.kundennummer,' Liefr: ',a.lieferantennummer,')'),CONCAT(a.id,' ',a.name,' (Kdr: ',a.kundennummer,')')) FROM adresse a WHERE a.id='" . $data[0]['adresse'] . "' AND a.geloescht=0 LIMIT 1");
        $data[0]['ansprechpartner'] = $this->app->DB->Select("SELECT CONCAT(an.id, ' ', an.name, ' (', a.name, IF(a.lieferantennummer, CONCAT(', Kdr: ',a.kundennummer,' Liefr: ',a.lieferantennummer,')'),CONCAT(', Kdr: ',a.kundennummer,')')) ) FROM ansprechpartner an LEFT JOIN adresse a ON an.adresse = a.id WHERE an.id = '".$data[0]['ansprechpartner_id']."' AND a.geloescht = 0 LIMIT 1");
        $data[0]['adresseintern'] = $this->app->DB->Select("SELECT if(a.lieferantennummer,CONCAT(a.id,' ',a.name,' (Kdr: ',a.kundennummer,' Liefr: ',a.lieferantennummer,')'),CONCAT(a.id,' ',a.name,' (Kdr: ',a.kundennummer,')')) FROM adresse a WHERE a.id='" . $data[0]['adresseintern'] . "' AND a.geloescht=0 LIMIT 1");
        $data[0]['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='" . $data[0]['projekt'] . "' AND id > 0 LIMIT 1");

        $data[0]['allDay'] = (($data[0]['allDay'] == '1') ? true : false);
        $data[0]['public'] = (($data[0]['public'] == '1') ? true : false);
        $data[0]['erinnerung'] = (($data[0]['erinnerung'] == '1') ? true : false);
        $data[0]['titel'] = $this->app->erp->ReadyForPDF($data[0]['titel']);
        $data[0]['ort'] = $this->app->erp->ReadyForPDF($data[0]['ort']);
        $data[0]['adresse'] = $this->app->erp->ReadyForPDF($data[0]['adresse']);
        $data[0]['adresseintern'] = $this->app->erp->ReadyForPDF($data[0]['adresseintern']);
        $data[0]['beschreibung'] = $this->app->erp->ReadyForPDF($data[0]['beschreibung']);

        //		$data[0]['color'] = (($data[0]['public']=='1')?$this->publicColor:$data[0]['color']);
        $data[0]['personen'] = $personen;


        $data[0]['gruppenkalender'] = $gruppenkalender;

        $googleEventLink = '';
        $canEditGoogleEvent = false;
        try {
          if ($this->isGoogleSynchronizationActive()) {
            /** @var GoogleSyncGateway $gateway */
            $gateway = $this->app->Container->get('GoogleSyncGateway');
            $sync = $gateway->tryGetSyncEntryByEvent((int)$event);
            if ($sync !== null) {
              /** @var GoogleCalendarSynchronizer $synchronizer */
              $synchronizer = $this->app->Container->get('GoogleCalendarSynchronizer');
              $address = (int)$this->app->User->GetAdresse();
              $googleEventLink = $sync->getHtmlLink();
              $canEditGoogleEvent = $synchronizer->canAddressEditEvent($address, (int)$event);
            }
          }
        } catch (Throwable $e) {
            // @todo: error handling
        }

        $data[0]['googleEventLink'] = $googleEventLink;
        $data[0]['googleEventEdit'] = $canEditGoogleEvent;

        $data = $data[0];


        echo json_encode($data);
        $this->app->ExitXentral();
      }
    }

  function Install(){
    $this->app->erp->CheckTable("kalender_gruppen");
    $this->app->erp->CheckColumn("id", "int(11)", "kalender_gruppen", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("bezeichnung", "varchar(255)", "kalender_gruppen", "NOT NULL");
    $this->app->erp->CheckColumn("farbe", "varchar(255)", "kalender_gruppen", "NOT NULL");
    $this->app->erp->CheckColumn("ausblenden","tinyint(1)", "kalender_gruppen", "NOT NULL DEFAULT 0");

    $this->app->erp->CheckTable("kalender_gruppen_mitglieder");
    $this->app->erp->CheckColumn("id", "int(11)", "kalender_gruppen_mitglieder", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("kalendergruppe", "int(11)", "kalender_gruppen_mitglieder", "NOT NULL");
    $this->app->erp->CheckColumn("benutzergruppe", "int(11)", "kalender_gruppen_mitglieder", "NOT NULL");
    $this->app->erp->CheckColumn("adresse", "int(11)", "kalender_gruppen_mitglieder", "NOT NULL");
    $this->app->erp->CheckIndex('kalender_gruppen_mitglieder','kalendergruppe');
    $this->app->erp->CheckIndex('kalender_gruppen_mitglieder','adresse');
  }

  function KalenderGruppenList(){
    $this->app->erp->Headlines('Kalender Gruppen');

    $this->app->erp->MenuEintrag("index.php?module=kalender&action=gruppenlist","&Uuml;bersicht");
    
    $speichern = $this->app->Secure->GetPOST("speichern");
    if($speichern != ""){
      $noredirect = $this->app->Secure->GetPOST("noRedirect");
      $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
      $farbe = $this->app->Secure->GetPOST("farbe");
      $ausblenden = $this->app->Secure->GetPOST("ausblenden");

      $error = "";

      if(trim($bezeichnung) == ""){
        $error .= "Bitte Bezeichnung ausfüllen";
      }
      
      if($error != ""){
        $this->app->Tpl->Set("MESSAGE", "<div class=\"error\" style=\"margin-top:7px\"><ul>$error</ul></div>");
      }else{
        $this->app->DB->Insert("INSERT INTO kalender_gruppen (bezeichnung, farbe, ausblenden) VALUES (\"$bezeichnung\", \"$farbe\", \"$ausblenden\")");
      }
    }

    $this->app->YUI->ColorPicker('farbe');
    $this->app->YUI->ColorPicker('editfarbe');
    $this->app->YUI->TableSearch('TAB1','kalender_gruppenlist', "show","","",basename(__FILE__), __CLASS__);
    $lid = $this->app->User->GetAdresse();


    $this->app->YUI->TableSearch('GRUPPEN','kalender_gruppen_mitglieder', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Add('GRUPPEN','<div style="overflow:hidden;width:0;height:0;padding:0;margin:0;"><input type="text" value="'.$lid.'" id="filterlid" name="filterlid" /></div>');


    $this->app->Tpl->Parse("PAGE","kalender_gruppenlist.tpl");
  }

  function KalenderGruppenEdit(){
    if($this->app->Secure->GetGET('cmd')==='get'){
      $id = (int)$this->app->Secure->GetPOST('editid');
      $data = $this->app->DB->SelectRow("SELECT id, bezeichnung, farbe, ausblenden FROM kalender_gruppen WHERE id = \"$id\" LIMIT 1");
      echo json_encode($data);
      $this->app->ExitXentral();
    }

    
    $this->app->Tpl->Parse('PAGE', "kalender_gruppenlist.tpl");
  }

  function KalenderGruppenSave(){
    $editid = (int)$this->app->Secure->GetPOST('editid');
    $editbezeichnung = $this->app->Secure->GetPOST('editbezeichnung');
    $editfarbe = $this->app->Secure->GetPOST('editfarbe');
    $editausblenden = $this->app->Secure->GetPOST('editausblenden');

    $error = "";
    if(trim($editbezeichnung) == ""){
      $error .= "Bitte Bezeichnung ausfüllen\n";
    }

    if($error == ""){
      if($editid){
        $this->app->DB->Update("UPDATE kalender_gruppen SET bezeichnung = \"$editbezeichnung\", farbe = \"$editfarbe\", ausblenden = \"$editausblenden\" WHERE id = \"$editid\"");
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
      $this->app->DB->Insert("INSERT INTO kalender_gruppen (bezeichnung, farbe, ausblenden) VALUES ('$editbezeichnung', '$editfarbe', '$editausblenden')");
      echo json_encode(array('status'=>2,'id'=>$this->app->DB->GetInsertID(),'farbe' => $editfarbe, 'bezeichnung' => $editbezeichnung, 'ausblenden' => $editausblenden));
      $this->app->ExitXentral();
    }
    echo json_encode(array('status'=>0,'statusText'=>$error));
    $this->app->ExitXentral();
  }

  function KalenderGruppenDelete(){
    $id = (int) $this->app->Secure->GetPOST('editid');
    if($id > 0){
      $this->app->DB->Delete("DELETE FROM kalender_gruppen WHERE id = \"$id\"");
      $this->app->DB->Delete("DELETE FROM kalender_gruppen_mitglieder WHERE kalendergruppe = \"$id\"");
    }
    echo json_encode(array('status'=>1));
    $this->app->ExitXentral();
  }

  public function KalenderMail($event,$betreff='',$text='',$emailcc='')
  {
    $datum = '';
    $arraufgabe = $this->app->DB->SelectArr("SELECT *,DATE_FORMAT(von,'%d.%m.%Y') as datum,
        DATE_FORMAT(bis,'%d.%m.%Y') as datumbis,
        DATE_FORMAT(von,'%Y%m%d') as icaldatumvon,
        DATE_FORMAT(bis,'%Y%m%d') as icaldatumbis, DATE_FORMAT(von,'%H%i00') as icaluhrzeitvon, DATE_FORMAT(bis,'%H%i00') as icaluhrzeitbis,
        DATE_FORMAT(von,'%H:%i') as zeit,
        DATE_FORMAT(bis,'%H:%i') as zeitbis
        FROM kalender_event WHERE id='$event' LIMIT 1");

    $adresse = $arraufgabe[0]["adresse"];
    $adresseintern = $arraufgabe[0]["adresseintern"];

    //$this->LogFile("sende an adresse ".$adresse);

    $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresse' AND geloescht!=1 LIMIT 1");
    $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht!=1 LIMIT 1");

    if($adresseintern >0)
    {
      $initiator_to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresseintern' AND geloescht!=1 LIMIT 1");
      $initiator_to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresseintern' AND geloescht!=1 LIMIT 1");
    } else {
      $initiator_to = $this->app->User->GetEmail();
      $initiator_to_name = $this->app->User->GetName();
    }

    //$this->LogFile("Sende Aufgabe $aufgabe an Email ".$to." und Initiator ".$initiator_to);

    if($betreff=="")
      $betreff = "Einladung für Termin ".$arraufgabe[0]["bezeichnung"];

    if($text=="")
    {
      if($arraufgabe[0]["datumbis"]!="00.00.0000" && $arraufgabe[0]["datum"]!=$arraufgabe[0]["datumbis"])
      {
        $text = "Datum: ".$arraufgabe[0]["datum"]." bis ".$arraufgabe[0]["datumbis"];//Hallo hier die mail";
      }
      else {
        if($arraufgabe[0]["zeit"]!="00:00" && $arraufgabe[0]["zeitbis"]!="00:00")
          $text = "Datum: ".$arraufgabe[0]["datum"]." von ".$arraufgabe[0]["zeit"]." bis ".$arraufgabe[0]["zeitbis"];//Hallo hier die mail";
        else if($arraufgabe[0]["zeit"]!="00:00")
          $text = "Datum: ".$arraufgabe[0]["datum"]." um ".$arraufgabe[0]["zeit"];
        else
          $text = "Datum: ".$arraufgabe[0]["datum"];
      }
    } else {
      $text .= "\r\n";
    }
    $text .= "\r\n";

    $beschreibung = $arraufgabe[0]["bezeichnung"];

    $venue = $arraufgabe[0]["ort"];
    $start = $arraufgabe[0]["icaldatumvon"];
    $start_time = $arraufgabe[0]["icaluhrzeitvon"];
    $end = $arraufgabe[0]["icaldatumbis"];
    $end_time = $arraufgabe[0]["icaluhrzeitbis"];

    $status = 'TENTATIVE';
    $sequence = 0;

    $event_id = $event;

    $ical = "BEGIN:VCALENDAR\r\n";
    $ical .= "VERSION:2.0\r\n";
    $ical .= "PRODID:-//WaWision//Termin//DE\r\n";
    $ical .= "METHOD:REQUEST\r\n";
    $ical .= "BEGIN:VEVENT\r\n";
    //$ical .= "ORGANIZER;SENT-BY=\"MAILTO:$initiator_to\":MAILTO:onbehalfoforganizer@kaserver.com\r\n";
    $ical .= "ORGANIZER;SENT-BY=\"MAILTO:$initiator_to\"\r\n";
    $ical .= "ATTENDEE;CN=$to;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;RSVP=TRUE:mailto:$initiator_to\r\n";
    $ical .= "UID:".strtoupper(md5($event_id))."-wawision\r\n";
    $ical .= "SEQUENCE:".$sequence."\r\n";
    $ical .= "STATUS:".$status."\r\n";
    $ical .= "DTSTAMPTZID=Europe/Berlin:".date('Ymd').'T'.date('His')."\r\n";
    $ical .= "DTSTART:".$start."T".$start_time."\r\n";
    $ical .= "DTEND:".$end."T".$end_time."\r\n";
    $ical .= "LOCATION:".$venue."\r\n";
    $ical .= "SUMMARY:".$beschreibung."\r\n";
//    $ical .= "DESCRIPTION:".$beschreibung."\r\n";
    $ical .= "BEGIN:VALARM\r\n";
    $ical .= "TRIGGER:-PT15M\r\n";
    $ical .= "ACTION:DISPLAY\r\n";
    $ical .= "DESCRIPTION:Reminder\r\n";
    $ical .= "END:VALARM\r\n";
    $ical .= "END:VEVENT\r\n";
    $ical .= "END:VCALENDAR\r\n";

    $datei = $this->app->erp->GetTMP().'Einladung_'.$beschreibung.'_'.$datum.".ics";
    file_put_contents($datei,$ical);
    if($start!='00000000'){
      $dateien = array($datei);
    }
    else{
      $dateien = '';
    }

    $bcc = array();

    if($emailcc!='')
    {
      //$to="";
      //$to_name="";

      $parts = explode(',',$emailcc);
      $cparts = count($parts);
      for($i=0;$i<$cparts;$i++)
      {
        $from = strstr($parts[$i], '<', true); // Ab PHP 5.3.0
        $email = strstr($parts[$i], '<'); // Ab PHP 5.3.0
        if($from !="" )
        {
          $email = str_replace(['<','>'],'',$email);
        } else {
          $email = $parts[$i];
        }

        if($i==0)
        {
          if($to==""){
            $to = $email;
            $to_name = $from;
          }
        } else {
          if($email!=$to)
          {
            if($from=="") $bcc[]=$email;
            else $bcc[]=$email;//$from." <".$email.">";
          }
        }
      }
    }

    $result = $this->app->erp->MailSend($this->app->erp->GetFirmaMail(),$this->app->erp->GetFirmaAbsender(),$to,$to_name,$betreff,$text,$dateien,"",false,$bcc);

    unlink($datei);
    return $result;
  }

  /**
   * @return void
   */
  protected function trySynchronizeGoogleChanges()
  {
      if (!$this->isGoogleSynchronizationActive()) {
          return;
      }
      /** @var GoogleCalendarSynchronizer $sync */
      $sync = $this->app->Container->get('GoogleCalendarSynchronizer');
      $userId = (int)$this->app->User->GetID();
      try {
          /** @var GoogleCalendarClientFactory $clientFactory */
          $clientFactory = $this->app->Container->get('GoogleCalendarClientFactory');
          $client = $clientFactory->createClient($userId);
          $sync->importChangedEvents($client);
      } catch (Throwable $e) {
          return;
      }
  }

  /**
   * @return bool
   */
  protected function isGoogleSynchronizationActive()
  {
      if (!$this->app->Container->has('GoogleCredentialsService')) {
          return false;
      }
      /** @var GoogleCredentialsService $credentialService */
      $credentialService = $this->app->Container->get('GoogleCredentialsService');

      return $this->app->Container->has('GoogleCalendarSynchronizer') && $credentialService->existCredentials();
  }
}
