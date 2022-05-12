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
include '_gen/adapterbox.php';

class Adapterbox extends GenAdapterbox {
  /** @var Application $app */
  var $app;

  /** @var null|bool $logging */
  protected $logging = null;

  /**
   * @param string $key
   * @param string $deviceid
   *
   * @return string
   */
  public function generateHash($key,$deviceid)
  {
    //$date = gmdate('dmY');
    $hash = '';

    for($i = 0; $i <= 200; $i++) {
      $hash = sha1($hash . $key . $deviceid);
    }

    return $hash;
  }

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public function TableSearch($app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch ($name) {
      case 'adapterbox_devicekeys':
        $validPass = $this->app->DB->Select("SELECT devicekey FROM firmendaten WHERE devicekey!='' LIMIT 1");
        $tmpdevices = $this->app->DB->SelectPairs("SELECT bezeichnung, adapterboxseriennummer FROM drucker WHERE adapterboxseriennummer!='' AND aktiv='1' AND (anbindung='adapterbox' OR anbindung='spooler')");
        $tmpdevices2 = $this->app->DB->SelectFirstCols("SELECT bezeichnung, seriennummer as adapterboxseriennummer FROM adapterbox WHERE seriennummer!=''");
        $tmpdevices = array_unique(array_merge($tmpdevices, $tmpdevices2));
        $col1 = '';
        $col2 = '';
        foreach($tmpdevices as $device) {
          $authkeys[$device] = $this->generateHash($validPass, $device);
          $col1 .= sprintf("
            IF(
            adapterboxseriennummer = '%s', '%s',",
            $this->app->DB->real_escape_string($device),
            $this->app->DB->real_escape_string($authkeys[$device])
          );
          $col2 .= sprintf("
            IF(
            seriennummer = '%s', '%s',",
            $this->app->DB->real_escape_string($device),
            $this->app->DB->real_escape_string($authkeys[$device])
          );
        }
        $col1 .= "''".str_repeat(')', count($tmpdevices));
        $col2 .= "''".str_repeat(')', count($tmpdevices));
        $heading = ['Typ','Bezeichnung','Seriennummer','AuthKey',''];
        $width = ['10%', '20%', '20%', '20%',  '1%'];
        $findcols = [
          't.typ','t.bezeichnung','t.adapterboxseriennummer','t.auth','t.id'
        ];
        $sql = "SELECT SQL_CALC_FOUND_ROWS t.id,t.typ,t.bezeichnung, t.adapterboxseriennummer, 
        t.auth,t.id FROM
        ((
          SELECT 'Adpaterbox' as Typ, bezeichnung, adapterboxseriennummer, $col1 AS auth,id FROM drucker 
          WHERE adapterboxseriennummer!='' AND aktiv='1' AND (anbindung='adapterbox' OR anbindung='spooler')
        ) UNION ALL (
          SELECT 'Drucker' as Typ,
                 bezeichnung, seriennummer as adapterboxseriennummer, $col2 AS auth,id FROM adapterbox WHERE seriennummer!=''        
        )) AS t
        
        ";

        break;
      case 'adapterbox_request_logs':
        $validPass = $this->app->DB->Select("SELECT devicekey FROM firmendaten WHERE devicekey!='' LIMIT 1");
        $passes = $this->app->DB->SelectFirstCols('SELECT DISTINCT validpass FROM adapterbox_request_log WHERE validpass != \'\'');
        $allPasses = [];
        if(!empty($validPass)) {
          $allPasses[] = $validPass;
        }
        $allPasses = array_unique(array_merge($allPasses, $passes));
        $devices = $this->app->DB->SelectFirstCols('SELECT DISTINCT device FROM adapterbox_request_log WHERE device != \'\'');

        $tmpdevices = $this->app->DB->SelectFirstCols("SELECT adapterboxseriennummer FROM drucker WHERE adapterboxseriennummer!='' AND aktiv='1' AND (anbindung='adapterbox' OR anbindung='spooler')");
        $tmpdevices2 = $this->app->DB->SelectFirstCols("SELECT seriennummer as adapterboxseriennummer FROM adapterbox WHERE seriennummer!=''");
        $tmpdevices = array_unique(array_merge($tmpdevices, $tmpdevices2));
        $alldevices = array_unique(array_merge($tmpdevices, $devices));
        $authkeys = [];
        $countKeys = 0;
        $colAuthNeeded = '            IF(
            arl.success OR arl.auth = \'\' AND arl.digets <> \'\', 
            arl.auth,';
        foreach($alldevices as $device) {
          foreach($allPasses as $pass) {
            $authkeys[$device][$pass] = $this->generateHash($pass, $device);
            $colAuthNeeded .= sprintf("
            IF(
            arl.device = '%s' AND arl.validpass = '%s', '%s',",
              $this->app->DB->real_escape_string($device),
              $this->app->DB->real_escape_string($pass),
              $this->app->DB->real_escape_string($authkeys[$device][$pass])
            );
            $countKeys++;
          }
        }

        /*foreach($authkeys as $device => $authArr) {
          foreach($authArr as $pass )
        }*/
        $colAuthNeeded .= "''".str_repeat(')', $countKeys + 1);
        $heading = ['Datum','IP','Device','Auth gesendet','Auth ben&ouml;tigt','Digets','Status','color','Men&uuml;'];
        $width = ['10%', '10%','10%', '20%', '20%', '20%', '10%', '1%'];
        $findcols = [
          'arl.created_at','arl.ip','arl.device','arl.auth',$colAuthNeeded,'arl.digets',
          "IF(arl.success=1,'OK','Fehler')",'arl.success', 'arl.id'
        ];
        $searchsql = ['arl.ip', 'arl.device','arl.auth'];
        $datecols = [0];
        $where = '';
        $menu = '';
        $trcol = 7;
        $sql = "SELECT SQL_CALC_FOUND_ROWS arl.id, 
        DATE_FORMAT(arl.created_at, '%d.%m.%Y %H:%i:%s'),arl.ip,
        arl.device, arl.auth, $colAuthNeeded, arl.digets, IF(arl.success=1,'OK','Fehler'), IF(arl.success=1,'#D2EC9D','#F1B19F'), arl.id
        FROM adapterbox_request_log AS arl";
        $defaultorder = 1;
        $defaultorderdesc = 1;
        break;
      case 'adapterbox_list':
        $allowed['adapterbox'] = array('list');

        // START EXTRA checkboxen

        // ENDE EXTRA checkboxen

        // headings

        $heading = array('Bezeichnung', 'Seriennummer', 'Verwenden als','Letzte IP','Status','Men&uuml;');
        $width = array('40%', '35%','15%','15%', '5%');
        $findcols = array('a.bezeichnung', 'a.seriennummer','a.verwendenals','a.tmpip','a.letzteverbindung', 'a.id');
        $searchsql = array('a.bezeichnung', 'a.seriennummer','a.verwendenals','a.tmpip', 'a.id');

        $defaultorder = 1; // sortiert nach dem oeffnen nach spalte 2

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = '<table cellpadding=0 cellspacing=0><tr><td nowrap>' .
          '<a href="index.php?module=adapterbox&action=endgeraet&id=%value%">' .
          "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" ."<a href=\"index.php?module=adapterbox&action=download&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>". "&nbsp;"."<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adapterbox&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" .
          '&nbsp;</td></tr></table>';

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, if(a.bezeichnung='','Ohne Bezeichnung',a.bezeichnung) as bezeichnung, a.seriennummer, if(a.verwendenals='' || a.verwendenals='etikettendrucker','Etikettendrucker',if(a.verwendenals='waage','Waage',if(a.verwendenals='kamera','Kamera',if(a.verwendenals='metratecrfid','RFID-Leser',if(a.verwendenals='bondrucker','Bondrucker',a.verwendenals))))),a.tmpip,if(TIME_TO_SEC(TIMEDIFF(NOW(),a.letzteverbindung)) > 10 OR a.letzteverbindung IS NULL,'disconnected','connected'),a.id
                                           FROM adapterbox a ";

        $count = 'SELECT COUNT(id) FROM adapterbox';
        break;



      case 'adapterbox_jobs':
        $allowed['adapterbox'] = array('jobs');


        $heading = array('Sender', 'Empf&auml;nger', 'art', 'Zeit', 'Men&uuml;');
        $width = array('20%', '20%', '20%', '20%', '10%');
        $findcols = array('d.deviceidsource', 'd.deviceiddest', 'd.art', 'd.zeitstempel', 'd.id');
        $searchsql = array('d.deviceidsource', 'd.deviceiddest', 'd.art', 'd.zeitstempel');

        $menu = '<table cellpadding=0 cellspacing=0><tr><td nowrap>' .
          "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adapterbox&action=deletejob&id=%value%\");>" .
          "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" .
          '&nbsp;</td></tr></table>';

        // SQL statement
        $sql = 'SELECT SQL_CALC_FOUND_ROWS d.id, d.deviceidsource, d.deviceiddest, d.art, d.zeitstempel,
                                           d.id FROM device_jobs d ';
        $where = '';//d.firma='" . $this->app->User->GetFirma() . "'";


        $count = 'SELECT COUNT(d.id) FROM device_jobs AS d';
        break;
      case 'adapterbox_log':
        $allowed['adapterbox'] = array('list');
        $heading = array('Zeit', 'IP', 'Seriennummer', 'Meldung', 'Men&uuml;');
        $width = array('15%', '15%', '15%', '40%', '10%');
        $findcols = array('a.datum', 'a.ip', 'a.seriennummer', 'a.meldung', 'a.id');
        $searchsql = array("DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s')", 'a.ip', 'a.meldung', 'a.seriennummer');
        $defaultorder = 5;
        $defaultorderdesc = 1;
        $menu = '<table cellpadding=0 cellspacing=0><tr><td nowrap>' .
          "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adapterbox&action=deletelog&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" .
          '&nbsp;</td></tr></table>';

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s'), a.ip, a.seriennummer, a.meldung, a.id FROM adapterbox_log a";


        $count = 'SELECT COUNT(a.id) FROM adapterbox_log a';
        break;
    }

    $erg = [];

    foreach($erlaubtevars as $k => $v) {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }

    return $erg;
  }

  /**
   * Adapterbox constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    //parent::GenAdapterbox($app);
    $this->app=$app;

    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AdapterboxCreate");
    $this->app->ActionHandler("edit","AdapterboxEdit");
    $this->app->ActionHandler("delete","AdapterboxDelete");
    $this->app->ActionHandler("deletelog","AdapterboxDeleteLog");
    $this->app->ActionHandler("list","AdapterboxList");
    $this->app->ActionHandler("log","AdapterboxLog");
    $this->app->ActionHandler("jobs","AdapterboxJobs");
    $this->app->ActionHandler("schritt2","AdapterboxSchritt2");
    $this->app->ActionHandler("download","AdapterboxDownload");
    $this->app->ActionHandler("endgeraet","AdapterboxEndgeraet");
    $this->app->ActionHandler("testdruck","AdapterboxTestdruck");
    $this->app->ActionHandler("testbild","AdapterboxTestbild");
    $this->app->ActionHandler("deletejob","AdapterboxDeletejob");
    $this->app->ActionHandler("deletejobs","AdapterboxDeletejobs");
    $this->app->ActionHandler("demo","AdapterboxDemo");
    $this->app->ActionHandler("log","AdapterboxLogs");

    $this->app->ActionHandlerListen($app);
  }


  public function AdapterboxLogs()
  {
    $this->AdapterboxMenu();
    $this->app->YUI->TableSearch('TAB1','adapterbox_devicekeys','show','','',basename(__FILE__),__CLASS__);
    $this->app->YUI->TableSearch('TAB1','adapterbox_request_logs','show','','',basename(__FILE__),__CLASS__);
    $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
  }

  public function AdapterboxSchritt2()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->AdapterboxMenuSchritte();

    $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Konfiguration &Uuml;bertragen. <br>Anschlie&szlig;end <a href=\"index.php?module=adapterbox&action=endgeraet&id=$id\">weiter mit Schritt 3</a>.</div>");

    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Parse('PAGE','adapterbox_schritt2.tpl');
  }

  public function AdapterboxEndgeraet()
  {
    $id = $this->app->Secure->GetGET('id');
    $submit = $this->app->Secure->GetPOST('submit');
    $seriennummer = $this->app->Secure->GetPOST('seriennummer');
    $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
    $verwendenals = $this->app->Secure->GetPOST('verwendenals');
    $model = $this->app->Secure->GetPOST('model');
    $baudrate = $this->app->Secure->GetPOST('baudrate');
    $this->AdapterboxMenuSchritte();



    if($submit)
    {
      $this->app->DB->Update("UPDATE adapterbox SET seriennummer='$seriennummer',verwendenals='$verwendenals',bezeichnung='$bezeichnung',model='$model',baudrate='$baudrate' WHERE id='$id' LIMIT 1");

      $iddrucker = $this->app->DB->Select("SELECT id FROM drucker WHERE adapterboxseriennummer='$seriennummer' AND adapterboxseriennummer!='' AND art=2 LIMIT 1");
      $idseriennummer = $this->app->DB->Select("SELECT COUNT(id) FROM adapterbox WHERE seriennummer='$seriennummer' AND seriennummer!='' LIMIT 1");

      //$this->app->Tpl->Set(MESSAGE,"<div class=\"info\">Die Einstellung wurde gespeichert!</div>");
      if($idseriennummer>=2) {
        $this->app->Tpl->Set('MESSAGE','<div class="error">Achtung, es gibt bereits eine Adapterboxen mit der Seriennummer $seriennummer.</div>');
      }

      if($verwendenals=='' || $verwendenals==='etikettendrucker')
      {
        if($iddrucker<=0)
        {
          // pruefe ob es namen schon gibt 
          $checkname = $this->app->DB->Select("SELECT id FROM drucker WHERE name LIKE '$bezeichnung' LIMIT 1");

          if($checkname <= 0 && $bezeichnung!='' && $seriennummer!='' && $idseriennummer<2)
          {
            //drucker anlegen  
            $this->app->DB->Insert("INSERT INTO drucker (id,name,art,adapterboxseriennummer,aktiv,anbindung,firma) VALUES ('','$bezeichnung','2','$seriennummer',1,'adapterbox',1)");
            $iddrucker = $this->app->DB->GetInsertID();
            $this->app->DB->Update("UPDATE adapterbox SET bezeichnung='$bezeichnung' WHERE id='$id' LIMIT 1");
          }  else {
            if($checkname > 0)
              $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Es gibt bereits einen Drucker mit dem gleichen Namen! Bitte w&auml;hlen Sie einen anderen Namen.</div>");
            else
              $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Bitte f&uuml;llen Sie alle Felder aus!</div>");
          }
        } else {
          if($idseriennummer<2)
          {
            $this->app->DB->Update("UPDATE drucker SET name='$bezeichnung' WHERE adapterboxseriennummer='$seriennummer' AND adapterboxseriennummer!='' LIMIT 1");
            $this->app->DB->Update("UPDATE adapterbox SET bezeichnung='$bezeichnung' WHERE id='$id' LIMIT 1");
          }
        }
        $standarddrucker = $this->app->erp->Firmendaten('standardetikettendrucker');
        $checkstandarddrucker = $this->app->DB->Select("SELECT id FROM drucker WHERE id='$standarddrucker' LIMIT 1");
        if($standarddrucker <= 0 || $checkstandarddrucker <=0)
        {
          $this->app->erp->FirmendatenSet('standardetikettendrucker',$iddrucker);
        }
        $standarddrucker = $this->app->erp->Firmendaten('etikettendrucker_wareneingang');
        $checkstandarddrucker = $this->app->DB->Select("SELECT id FROM drucker WHERE id='$standarddrucker' LIMIT 1");
        if($standarddrucker <= 0 || $checkstandarddrucker <=0)
        {
          $this->app->erp->FirmendatenSet('etikettendrucker_wareneingang',$iddrucker);
        }

      }


      // pruefe ob es einen standard etikettendrucker gibt wenn nicht lege ihn an
    }

    $verwendenals = $this->app->DB->Select("SELECT verwendenals FROM adapterbox WHERE id='$id' LIMIT 1");
    $baudrate = $this->app->DB->Select("SELECT baudrate FROM adapterbox WHERE id='$id' LIMIT 1");
    $model = $this->app->DB->Select("SELECT model FROM adapterbox WHERE id='$id' LIMIT 1");

    if($idseriennummer<2)
    {
      $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE id='$id' LIMIT 1");
    }
    else { 
      $seriennummer='';
    }


    $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Druckerbezeichnung frei vergeben und Seriennummer der Adapterbox eintragen. Dann einmal Speichern drücken und auf den Reiter \"Schritt 4 - Demo\" wechseln. Bzw. auf die <a href=\"index.php?module=adapterbox&action=list\">Übersicht</a> gehen und prüfen ob die Adapterbox vom Status auf \"connected\" wechselt.</div>");
    $iddrucker = $this->app->DB->Select("SELECT id FROM drucker WHERE adapterboxseriennummer='$seriennummer' AND adapterboxseriennummer!='' AND art=2 LIMIT 1");
    if($iddrucker > 0 && ($verwendenals=='' || $verwendenals==='etikettendrucker'))
    {
      $name_drucker = $this->app->DB->Select("SELECT name FROM drucker WHERE adapterboxseriennummer='$seriennummer' AND art=2 LIMIT 1");
      //$this->app->Tpl->Add(MESSAGE,"<div class=\"warning\">Nach dem Anstecken kann es ca. 1-2 Minuten dauern bis die ersten Testetiketten aus dem Drucker kommen.</div>");
      $this->app->Tpl->Add('MESSAGE',"<div class=\"info\">Einen <a href=\"index.php?module=adapterbox&action=testdruck&id=$id\">Testdruck</a> (kann das erste Mal einige Sekunden dauern) durchf&uuml;hren oder das <a href=\"index.php?module=drucker&action=edit&id=$iddrucker\" target=\"_blank\">Etikettenformat</a> (Men&uuml;punkt Drucker) einstellen.</div>");
    } else {
      $name_drucker = $this->app->DB->Select("SELECT bezeichnung FROM adapterbox WHERE id='$id' LIMIT 1");
    }

    $options = '';
    $tmp = array('marel'=>'Marel','pce'=>'PCE','sartorius'=>'Sartorius');
    foreach($tmp as $key=>$value)
    {
      if($key==$model){
        $options .= "<option value=\"$key\" selected>" . $value . '</option>';
      }
      else{
        $options .= "<option value=\"$key\">" . $value . '</option>';
      }
    }
    $this->app->Tpl->Set('MODEL',$options);



    $options = "";
    $tmp = array('1'=>'4800','2'=>'9600');
    foreach($tmp as $key=>$value)
    {
      if($key==$baudrate){
        $options .= "<option value=\"$key\" selected>" . $value . '</option>';
      }
      else{
        $options .= "<option value=\"$key\">" . $value . '</option>';
      }
    }
    $this->app->Tpl->Set('BAUDRATE',$options);


    $options = '';
    $tmp = array('etikettendrucker'=>'Etikettendrucker','waage'=>'Waage','kamera'=>'Kamera','bondrucker'=>'Bondrucker','display'=>'POS Display','metratecrfid'=>'RFID-Leser','rksvlight'=>'RKSV Light Smartcard');
    foreach($tmp as $key=>$value)
    {
      if($key==$verwendenals){
        $options .= "<option value=\"$key\" selected>" . $value . '</option>';
      }
      else{
        $options .= "<option value=\"$key\">" . $value . '</option>';
      }
    }
    $this->app->Tpl->Set('VERWENDENALS',$options);

    $this->app->Tpl->Set('SERIENNUMMER',$seriennummer);
    $this->app->Tpl->Set('BEZEICHNUNG',$name_drucker);

    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Parse('PAGE','adapterbox_endgeraet.tpl');
  }


  public function AdapterboxDownload()
  {
    $id = $this->app->Secure->GetGET('id');

    $tmp = $this->app->DB->SelectArr("SELECT * FROM adapterbox WHERE id='$id' LIMIT 1");

    $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    $url_parts = parse_url($url);

    if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false)
      $_SERVER['HTTPS']='on';


    if(isset($_SERVER['HTTPS'])) {
      if ($_SERVER['HTTPS'] == 'on') {
        $secure_connection = true;
        $url_parts['scheme'] = 'https';
      }
    }

    if($url_parts['hostname']=='') {
      if($url_parts['port']!=''){
        $url_parts['hostname'] = $url_parts['host'] . ':' . $url_parts['port'];
      }
      else{
        $url_parts['hostname'] = $url_parts['host'];
      }
    }

    $url_parts['path'] = str_replace('index.php','',$url_parts['path']);

    $constructed_url = $url_parts['scheme'] . '://' . $url_parts['hostname'] . rtrim($url_parts['path'],'/');

    $devicekey = $this->app->erp->Firmendaten('devicekey');

    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename=wawision.php');
    echo '<?php'."\r\n";

    echo '$settings["url"]="'.$constructed_url.'";'."\r\n";
    echo '$settings["devicekey"]="'.$devicekey.'";'."\r\n";
    echo '$settings["serial"]="'.$tmp[0]['seriennummer'].'";'."\r\n";

    echo '?>';
    exit;
  }

  public function AdapterboxCreate()
  {
    $this->app->DB->Insert('INSERT INTO adapterbox (id) VALUES (NULL)');
    $id = $this->app->DB->GetInsertID();
    header('Location: index.php?module=adapterbox&action=edit&id='.$id);
    exit;
  }

  /**
   * @return bool
   */
  public function isLoggingActive()
  {
    if($this->logging !== null) {
      return (bool)$this->logging;
    }
    $loggingActive = (string)$this->app->erp->GetKonfiguration('adapterbox_logging_active');
    if($loggingActive === '') {
      $this->app->erp->SetKonfigurationValue('adapterbox_logging_active', '0');
      $this->logging = false;

      return false;
    }
    $this->logging = (bool)$loggingActive;

    return $this->logging;
  }

  public function AdapterboxList()
  {
    $this->AdapterboxMenu();
    $isLoggingActive = $this->isLoggingActive();
    if($isLoggingActive) {
      $this->app->Tpl->Set('LOGGINGACTIVE', 'checked');
    }
    $this->app->YUI->AutoSaveKonfiguration('logginActive', 'adapterbox_logging_active');
    $devicekey = $this->app->erp->Firmendaten('devicekey');
    if($devicekey=='')
    {
      $this->app->Tpl->Set('MESSAGE','<div class="error">Aktuell ist kein API Schl&uuml;ssel hinterlegt! F&uuml;r die Adapterboxen ben&ouml;tigen Sie diesen! <a href="index.php?module=firmendaten&action=edit#tabs-11">aktivieren</a></div>');
    }

    $deviceenable = $this->app->erp->Firmendaten('deviceenable');
    if($deviceenable!='1')
    {
      $this->app->Tpl->Add('MESSAGE','<div class="error">Aktuell ist die API nicht aktiviert! Bitte aktivieren Sie diese! <a href="index.php?module=firmendaten&action=edit#tabs-11">aktivieren</a></div>');
    }

    $this->app->YUI->TableSearch('TAB1','adapterbox_list', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','adapterbox_overview.tpl');
  }

  public function AdapterboxDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    if(is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM adapterbox WHERE id='$id'");
    }
    //$this->AdapterboxList();
    header('Location: index.php?module=adapterbox&action=list');
    exit;
  }


  public function AdapterboxMenuSchritte()
  {
    $id = $this->app->Secure->GetGET('id');
    $data = $this->app->DB->SelectRow("SELECT * FROM adapterbox WHERE id='$id' LIMIT 1");
    if(!empty($data)){
      $this->app->Tpl->Set('KURZUEBERSCHRIFT1', $data['verwendenals']);
      $this->app->Tpl->Add('KURZUEBERSCHRIFT2', 'SN: ' . $data['seriennummer']);
    }
    $this->app->erp->MenuEintrag("index.php?module=adapterbox&action=edit&id=$id",'Schritt 1 - Netzwerkkonfiguration');
    $this->app->erp->MenuEintrag("index.php?module=adapterbox&action=schritt2&id=$id",'Schritt 2 - Konfiguration &uuml;bertragen');
    $this->app->erp->MenuEintrag("index.php?module=adapterbox&action=endgeraet&id=$id",'Schritt 3 - Endger&auml;t einstellen');
    $this->app->erp->MenuEintrag("index.php?module=adapterbox&action=demo&id=$id",'Schritt 4 - Demo');
    // $this->app->erp->MenuEintrag("index.php?module=adapterbox&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  public function AdapterboxDemo()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->AdapterboxMenuSchritte();

    $data = $this->app->DB->SelectArr("SELECT * FROM adapterbox WHERE id='$id' LIMIT 1");
    $data = reset($data);

    switch($data['verwendenals'])
    {
      case 'waage':
        $gewicht = $this->app->erp->GetAdapterboxAPIWaage($data['seriennummer']);  

        if($gewicht!=''){
          $gewicht="Ergebnis: <br><br><textarea rows=10 cols=40>$gewicht</textarea>";
        }
        $this->app->Tpl->Set('TAB1',"<div class=\"info\">Zum Testen bitte klicken: <input type=\"button\" value=\"Gewicht abholen\" 
          onclick=\"window.location.href='index.php?module=adapterbox&action=demo&id=$id'\"></div><br>$gewicht");  
      break;
      case 'rksvlight':
        $result = $this->app->erp->GetAdapterboxAPIRKSVlight($data['seriennummer'],"-getcardnumber -reader 0");  

        if($result!=''){
          $result="Ergebnis: <br><br><textarea rows=10 cols=40>$result</textarea>";
        }
        $this->app->Tpl->Set('TAB1',"<div class=\"info\">Zum Testen bitte klicken: <input type=\"button\" value=\"Kartennummer abfragen\" 
          onclick=\"window.location.href='index.php?module=adapterbox&action=demo&id=$id'\"></div><br>$result");  
      break;

      case 'kamera':
        $this->app->Tpl->Set('TAB1',"<div class=\"info\">Zum Testen bitte klicken: <input type=\"button\" value=\"Bild abholen\" 
          onclick=\"window.location.href='index.php?module=adapterbox&action=demo&id=$id'\"></div><img src=\"index.php?module=adapterbox&action=testbild&id=$id\">");  
      break;

      case 'etikettendrucker':
      $seriennummer = $data['seriennummer'];
      $druckercode = $this->app->DB->Select("SELECT id FROM drucker WHERE adapterboxseriennummer='$seriennummer' AND adapterboxseriennummer!='' AND art=2 LIMIT 1");
      $this->app->erp->EtikettenDrucker("etikettendrucker_einfach",1,"","",array('bezeichnung1'=>'Xentral','bezeichnung2'=>'www.xentral.com'),"",$druckercode);
        $this->app->Tpl->Set('TAB1',"<div class=\"info\">Zum Testen bitte klicken: <input type=\"button\" value=\"Testdruck starten\"
          onclick=\"window.location.href='index.php?module=adapterbox&action=demo&id=$id'\"></div>");  
      break;

      case 'display':
        $seriennummer = $data['seriennummer'];
        $this->app->erp->GetAdapterboxAPIDisplay($seriennummer,array('Xentral POS','www.xentral.com'));

        $this->app->Tpl->Set('TAB1',"<div class=\"info\">Zum Testen bitte klicken: <input type=\"button\" value=\"Testausgabe starten\"
          onclick=\"window.location.href='index.php?module=adapterbox&action=demo&id=$id'\"></div>");  
      break;
      case 'bondrucker':
      $seriennummer = $data['seriennummer'];
      $druckercode = $this->app->DB->Select("SELECT id FROM drucker WHERE adapterboxseriennummer='$seriennummer' AND adapterboxseriennummer!='' AND art=2 LIMIT 1");

      $printer = new phpprint();

      $printer->newline();
      $printer->set_font(phpprint::FONT_A);
      $printer->set_justification(phpprint::JUSTIFY_CENTER);
      $printer->enlargePrint(true);
      $printer->text("Xentral Store\n");
      $printer->newline();
      $printer->enlargePrint();

      $printer->text("Xentral ERP Software GmbH\n");
      $printer->text("Fuggerstrasse 11\n");
      $printer->text("86150 Augsburg\n");
      $printer->text("Tel: 0821/26841041\n");
      $printer->text("www.wawision.de\n");
      $printer->newline();
      $printer->set_justification(phpprint::JUSTIFY_CENTER);
      $printer->feed(1);
      $beleg = rand(111111,999999);
      $printer->qrCode(md5('123456789012345678901234567890'),phpprint::QR_ECLEVEL_L,6);
      //$printer->generateBarcode($beleg, phpprint::BARCODE_CODE39, 80);
      $printer->cut();
      $printer->pulse();
      $bon = base64_encode($printer->str);

      $this->app->erp->GetAdapterboxAPIBondrucker($seriennummer,$bon);

      $this->app->Tpl->Set('TAB1',"<div class=\"info\">Zum Testen bitte klicken: <input type=\"button\" value=\"Testdruck starten\"
          onclick=\"window.location.href='index.php?module=adapterbox&action=demo&id=$id'\"></div>");  
      break;

    }

    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  public function AdapterboxMenu()
  {
    $id = $this->app->Secure->GetGET('id');
    //$this->app->Tpl->Add(KURZUEBERSCHRIFT,"Adapterbox");
    $this->app->erp->MenuEintrag('index.php?module=adapterbox&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=adapterbox&action=jobs','Device Jobs');
    $this->app->erp->MenuEintrag('index.php?module=adapterbox&action=log','Logs');
    //$this->app->erp->MenuEintrag("index.php?module=adapterbox&action=log","Log");
    $this->app->erp->MenuEintrag('index.php?module=adapterbox&action=create','Neu');
    if($this->app->Secure->GetGET('action')==='list'){
      $this->app->erp->MenuEintrag('index.php?module=einstellungen&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    else{
      $this->app->erp->MenuEintrag('index.php?module=adapterbox&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
  }


  public function AdapterboxDeletejobs()
  {
    $msg = $this->app->erp->base64_url_encode('<div class="info">Die Jobs wurden gel&ouml;scht!</div>  ');
    $this->app->DB->Delete('DELETE FROM device_jobs');
    header('Location: index.php?module=adapterbox&action=jobs&msg='.$msg);
    exit;
  }


  public function AdapterboxDeletejob()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $msg = $this->app->erp->base64_url_encode('<div class="info">Der Job wurde gel&ouml;scht!</div>  ');
    if($id > 0){
      $this->app->DB->Delete("DELETE FROM device_jobs WHERE id='$id'");
    }
    header("Location: index.php?module=adapterbox&action=jobs&msg=$msg");
    exit;
  }

  public function AdapterboxJobs()
  {
    $this->AdapterboxMenu();
    $this->app->YUI->TableSearch('TAB1','adapterbox_jobs', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Add('TAB1',"<center><input type=\"button\" value=\"Alle Eintr&auml;ge l&ouml;schen\" onclick=\"window.location.href='index.php?module=adapterbox&action=deletejobs'\"></center>");
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function AdapterboxEdit()
  {
    $this->AdapterboxMenuSchritte();
    $id = $this->app->Secure->GetGET('id');

    $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Bitte richten Sie die Netzwerkverbindung an der Adapterbox ein. <br>Anschlie&szlig;end <a href=\"index.php?module=adapterbox&action=schritt2&id=$id\">weiter mit Schritt 2</a>.</div>");

    parent::AdapterboxEdit();
  }


  public function AdapterboxLog()
  {
    $this->AdapterboxMenu();

    $this->app->DB->Delete("DELETE FROM `adapterbox_log` WHERE id < SELECT MIN(id) FROM `adapterbox_log` ORDER BY id DESC LIMIT 0,100"); 
    $this->app->YUI->TableSearch('TAB1','adapterbox_log', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function AdapterboxDeleteLog()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->DB->Delete("DELETE FROM adapterbox_log WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode('<div class="error2">Der Logeintrag wurde gel&ouml;scht!</div>  ');
    header("Location: index.php?module=adapterbox&action=log&msg=$msg");
    exit;
  }


  public function AdapterboxTestbild()
  {
    $id = $this->app->Secure->GetGET('id');

    $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE id='$id' LIMIT 1");
    //$image = $this->app->erp->GetAdapterboxAPIImage($seriennummer,"480","360");
    $image = $this->app->erp->GetAdapterboxAPIImage($seriennummer,'800','600');
    //$image = $this->app->erp->GetAdapterboxAPIImage("999999999","960","720");
    header('Content-Type: image/jpeg');
    header('Content-Length: ' .strlen($image) );
    echo ($image);
    exit;
  }

  public function AdapterboxTestdruck()
  {
    $id = $this->app->Secure->GetGET('id');

    $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE id='$id' LIMIT 1");
    $druckercode = $this->app->DB->Select("SELECT id FROM drucker WHERE adapterboxseriennummer='$seriennummer' AND adapterboxseriennummer!='' AND art=2 LIMIT 1");
    $this->app->erp->EtikettenDrucker('etikettendrucker_einfach',1,'','',array('bezeichnung1'=>'Xentral','bezeichnung2'=>'www.xentral.biz'),'',$druckercode);
    header('Location: index.php?module=adapterbox&action=endgeraet&id='.$id);
    exit;
  }
}

