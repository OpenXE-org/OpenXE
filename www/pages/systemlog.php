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
class Systemlog {
  /** @var Application $app */
  var $app;

  public const TLS_TEST_URL = 'https://update.xentral.biz/tlstest.php';

  /**
   * Systemlog constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern)
    {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","SystemlogList");
    $this->app->ActionHandler("minidetail","SystemlogMiniDetail");
    $this->app->ActionHandler("datenbank","SystemlogDatenbank");
    $this->app->ActionHandler("timeout","SystemlogTimeout");
    //$this->app->ActionHandler("delete","SystemlogDelete");
    //$this->app->ActionHandler("deleteall","SystemlogDeleteAll");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }
  /*
     function SystemlogDelete()
     {
     $id = $this->app->Secure->GetGET("id");
     $this->app->DB->Delete("DELETE FROM systemlog WHERE id='$id' LIMIT 1");
     $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Der Logeintrag wurde gel&ouml;scht!</div>  ");
     header("Location: index.php?module=systemlog&action=list&msg=$msg");
     exit;	
     }	
   */
  /*
     function SystemlogDeleteAll()
     {
     $id = $this->app->Secure->GetGET("id");
     $this->app->DB->Delete("DELETE FROM systemlog WHERE id > 0");
     $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Alle Logeintr&auml;ge wurden wurden gel&ouml;scht!</div>  ");
     header("Location: index.php?module=systemlog&action=list&msg=$msg");
     exit;	
     }	
   */

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
    switch($name) {
      case 'systemlog':
        $allowed['systemlog'] = array('list');

        // headings
        $heading = array('', 'Level', 'Zeit', 'Bearbeiter', 'Module', 'Action', 'Parameter', 'Funktion', 'Meldung', 'Men&uuml;');
        $width = array('4%', '4%', '15%', '10%', '10%', '10%', '10%', '10%', '40%', '10%', '5%');
        $findcols = array('open', 'a.level', 'a.datum', 'a.bearbeiter', 'a.module', 'a.action', 'a.parameter', 'a.funktionsname', 'a.meldung', 'a.id');
        $searchsql = array("DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s')", 'a.bearbeiter', 'a.module', 'a.meldung', 'a.action', 'a.parameter', 'a.funktionsname');
        $defaultorder = 3;
        $defaultorderdesc = 1;
        $menucol = 1;
        $moreinfo = true;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" .
          "</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, a.level,DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s'), a.bearbeiter, 
  a.module, a.action, a.parameter, a.funktionsname,a.meldung,CONCAT('module=',a.module,'&action=',a.action,'&id=',a.parameter) FROM systemlog a";

        $count = 'SELECT COUNT(a.id) FROM systemlog a';
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
   * @return array
   */
  public function testDatabase()
  {
    $ret = ['html' => [],'deleteformdata' => []];
    foreach($this->dataBaseLinkings() as $verknuepfung) {
      $verknuepfung[4] = 'delete';
      $tabelle = $verknuepfung[0];
      $spalte = $verknuepfung[1];
      $subtabelle = $verknuepfung[2];
      $subspalte = $verknuepfung[3];
      $subwhere = '';
      if(isset($verknuepfung[5]) && $verknuepfung[5] != '' && isset($verknuepfung[6]) && $verknuepfung[6] != ''){
        $subwhere = ' AND s.'.$verknuepfung[5]." = '".$verknuepfung[6]."' ";
      }
      if(isset($verknuepfung[7])) {
        $subwhere .= $verknuepfung[7];
      }
      $check = $this->app->DB->Select(
        "SELECT s.`$subspalte` 
        FROM `$subtabelle` s 
        LEFT JOIN `$tabelle` t ON s.`$subspalte` = t.`$spalte` 
        WHERE isnull(t.`$spalte`) AND s.`$subspalte` <> 0 AND not isnull(s.`$subspalte`) $subwhere 
        LIMIT 1"
      );
      if($check) {
        $aktion = '';
        if(isset($verknuepfung[4]) && $verknuepfung[4] === 'delete') {
          $aktion = '<form method="POST">
            <input type="hidden" name="tabelle" value="'.$tabelle.'" />
            <input type="hidden" name="spalte" value="'.$spalte.'" />
            <input type="hidden" name="subtabelle" value="'.$subtabelle.'" />
            <input type="hidden" name="subspalte" value="'.$subspalte.'" />
            <input type="submit" name="delete" value="Daten l&ouml;schen" />
            </form>';
          $warnung = true;
          $ret['deleteformdata'][] = [
            'tabelle'=>$tabelle,
            'spalte' => $spalte,
            'subtabelle' => $subtabelle,
            'subspalte'=>$subspalte,
          ];
        }
        $ret['html'][] = '<tr><td>'.$subtabelle.'.'.$subspalte.'</td><td>'.$tabelle.'.'.$spalte.'</td><td>-</td><td style="color:red;">Eintr&auml;ge mit nicht existierender '.$tabelle.'.'.$spalte.' gefunden</td><td>'.$aktion.'</td></tr>';
        $ret['warning'] = true;
      }
      else{
        if(!$this->app->DB->error()) {
          $ret['html'][] = '<tr><td>'.$subtabelle.'.'.$subspalte.'</td><td>'.$tabelle.'.'.$spalte.'</td><td>OK</td><td></td><td></td></tr>';
        }
      }
    }

    if(!empty($ret['warning']) && method_exists($this->app->erp, 'setSystemHealth')) {
      $this->app->erp->setSystemHealth(
        'database',
        'sql_integrity',
        'warning',
        '<a target="_blank" href="index.php?module=systemlog&action=datenbank">Eintr&auml;ge mit nicht existierender Verkn&uuml;pfungen gefunden</a>.'
      );
    }
    else {
      if(method_exists($this->app->erp, 'setSystemHealth')) {
        $this->app->erp->setSystemHealth(
          'database',
          'sql_integrity',
          'ok',
          ''
        );
      }
    }

    return $ret;
  }

  /**
   * @return array
   */
  public function dataBaseLinkings()
  {
    return array(
      array('adresse','id','adresse_accounts','adresse','delete'),
      array('adresse','id','adresse_kontakte','adresse','delete'),
      array('adresse','id','lieferadressen','adresse','delete'),
      array('adresse','id','ansprechpartner','adresse','delete'),
      array('adresse','id','abrechnungsartikel','adresse'),
      array('artikel','id','abrechnungsartikel','artikel'),
      //array('adresse','id','dokumente','adresse_from'),
      array('adresse','id','dokumente','adresse_to'),
      array('adresse','id','dokumente_send','adresse'),
      array('artikel','id','einkaufspreise','artikel'),
      array('adresse','id','einkaufspreise','adresse'),
      array('artikel','id','verkaufspreise','artikel'),
      array('drucker','id','drucker_spooler','drucker'),
      array('datei','id','datei_version','datei'),
      array('datei','id','datei_stichwoerter','datei'),
      array('adresse','id','user','adresse'),
      array('projekt','id','arbeitspaket','projekt'),
      array('lager','id','lager_platz','lager'),
      array('lager_platz','id','lager_platz_inhalt','lager_platz'),
      array('artikel','id','lager_reserviert','artikel','delete'),
      array('adresse','id','lager_reserviert','adresse'),
      array('artikel','id','lager_seriennummern','artikel','delete'),
      array('artikel','id','lager_platz_inhalt','artikel','delete'),
      array('artikel','id','lager_mindesthaltbarkeitsdatum','artikel'),
      array('lager_platz','id','lager_mindesthaltbarkeitsdatum','lager_platz'),
      array('artikel','id','artikel','variante_von'),
      array('artikel','id','stueckliste','stuecklistevonartikel'),
      array('artikel','id','stueckliste','artikel'),
      array('rechnung','id','rechnung_position','rechnung','delete'),
      array('rechnung','id','rechnung_protokoll','rechnung','delete'),
      array('auftrag','id','auftrag_position','auftrag','delete'),
      array('auftrag','id','auftrag_protokoll','auftrag','delete'),
      array('angebot','id','angebot_position','angebot','delete'),
      array('angebot','id','angebot_protokoll','angebot','delete'),
      array('gutschrift','id','gutschrift_position','gutschrift','delete'),
      array('gutschrift','id','gutschrift_protokoll','gutschrift','delete'),
      array('bestellung','id','bestellung_position','bestellung','delete'),
      array('bestellung','id','bestellung_protokoll','bestellung','delete'),
      array('produktion','id','produktion_position','produktion','delete'),
      array('produktion','id','produktion_protokoll','produktion','delete'),
      array('inventur','id','inventur_position','inventur','delete'),
      array('inventur','id','inventur_protokoll','inventur','delete'),
      array('adresse','id','rechnung','adresse'),
      array('adresse','id','lieferschein','adresse'),
      array('adresse','id','gutschrift','adresse'),
      array('adresse','id','auftrag','adresse'),
      array('adresse','id','angebot','adresse'),
      array('adresse','id','bestellung','adresse'),
      array('adresse','id','produktion','adresse'),
      array('adresse','id','inventur','adresse'),
      array('artikel','id','produktion_position','artikel'),
      array('artikel','id','rechnung_position','artikel'),
      array('artikel','id','auftrag_position','artikel'),
      array('artikel','id','lieferschein_position','artikel'),
      array('artikel','id','gutschrift_position','artikel'),
      array('artikel','id','bestellung_position','artikel'),
      array('artikel','id','angebot_position','artikel'),
      array('rechnung','id','pdfarchiv','table_id','delete','table_name','rechnung'),
      array('auftrag','id','pdfarchiv','table_id','delete','table_name','auftrag'),
      array('angebot','id','pdfarchiv','table_id','delete','table_name','angebot'),
      array('gutschrift','id','pdfarchiv','table_id','delete','table_name','gutschrift'),
      array('bestellung','id','pdfarchiv','table_id','delete','table_name','bestellung'),
      array('lieferschein','id','pdfarchiv','table_id','delete','table_name','lieferschein'),
      array('produktion','id','pdfarchiv','table_id','delete','table_name','produktion'),
      array('rechnung','id','pdfmirror_md5pool','table_id','delete','table_name','rechnung'),
      array('auftrag','id','pdfmirror_md5pool','table_id','delete','table_name','auftrag'),
      array('angebot','id','pdfmirror_md5pool','table_id','delete','table_name','angebot'),
      array('gutschrift','id','pdfmirror_md5pool','table_id','delete','table_name','gutschrift'),
      array('bestellung','id','pdfmirror_md5pool','table_id','delete','table_name','bestellung'),
      array('lieferschein','id','pdfmirror_md5pool','table_id','delete','table_name','lieferschein'),
      array('produktion','id','pdfmirror_md5pool','table_id','delete','table_name','auftrag'),
      array('auftrag','id','beleg_chargesnmhd','doctypeid','delete','doctype','auftrag'),
      array('angebot','id','beleg_chargesnmhd','doctypeid','delete','doctype','angebot'),
      array('gutschrift','id','beleg_chargesnmhd','doctypeid','delete','doctype','gutschrift'),
      array('lieferschein','id','beleg_chargesnmhd','doctypeid','delete','doctype','lieferschein'),
      array('rechnung','id','beleg_chargesnmhd','doctypeid','delete','doctype','rechnung'),
      array('bestellung','id','beleg_chargesnmhd','doctypeid','delete','doctype','bestellung')
    );
  }

  /**
   * @param string $table
   * @param string $column
   * @param string $subTable
   * @param string $subColumn
   *
   * @return int
   */
  public function deleteTableEntries($table, $column, $subTable, $subColumn) 
  {
    $ret = 0;
    if(empty($table) || empty($column) || empty($subColumn) || empty($subTable)) {
      return 0;
    }
    
    $linkinSchemas = $this->dataBaseLinkings();
    foreach ($linkinSchemas as $linkinSchema) {
      $linkinSchema[4] = 'delete';
      if(
        $table == $linkinSchema[0] 
        && $column == $linkinSchema[1] 
        && $subTable == $linkinSchema[2] 
        && $subColumn == $linkinSchema[3] 
        && !empty($linkinSchema[4]) 
        && $linkinSchema[4] === 'delete'
      ) {
        $subwhere = '';
        if(!empty($linkinSchema[5]) && $linkinSchema[5] != '' && isset($linkinSchema[6]) && $linkinSchema[6] != '') {
          $subwhere = ' AND s.' . $linkinSchema[5] . " = '" . $linkinSchema[6] . "' ";
        }
        if(!empty($linkinSchema[7])){
          $subwhere .= $linkinSchema[7];
        }
        $this->app->DB->Delete(
          "DELETE `s` 
          FROM `$subTable` AS `s` 
          LEFT JOIN `$table` `t` ON s.`$subColumn` = t.`$column` 
          WHERE isnull(t.`$column`) AND s.`$subColumn` <> 0 AND not isnull(s.`$subColumn`) $subwhere"
        );
        $anz = (int)$this->app->DB->affected_rows();
        if($anz <= 0) {
          continue;
        }
        $ret += $anz;
      }
    }
    
    return $ret;
  }

  public function SystemlogDatenbank()
  {
    if($this->app->Secure->GetPOST('cleanconfirm') && $this->app->Secure->GetPOST('cleanall')) {
      $test = $this->testDatabase();
      $message = '<div class="info">Keine Datenintegriet&auml;tsprobleme gefunden</div>';
      if(!empty($test['warning']) && !empty($test['deleteformdata'])) {
        $sum = 0;
        foreach($test['deleteformdata'] as $formdata) {
          $anz = $this->deleteTableEntries(
            $formdata['tabelle'], $formdata['spalte'], $formdata['subtabelle'], $formdata['subspalte']
          );
          if($anz > 0) {
            $sum += $anz;
          }
        }
        //$anz = $this->deleteTableEntries($tabelle, $spalte, $subtabelle, $subspalte);
        $test = $this->testDatabase();
        if(empty($test['warning'])) {
          $message = sprintf('<div class="info">Es wurden %d Datens&auml;tze gel&ouml;scht</div>', $sum);
        }
        else {
          $message = sprintf('<div class="warning">Es wurden %d Datens&auml;tze gel&ouml;scht. 
            Es konnten nicht alle Datenintegriet&auml;tsprobleme gel&ouml;st werden</div>', $sum);
        }
      }

      $this->app->Location->execute(
        'index.php?module=systemlog&action=datenbank&msg='.$this->app->erp->base64_url_encode($message)
      );
    }
    $this->SystemlogMenu();

    $this->app->Tpl->Add(
      'MESSAGE', 
      '<form method="POST">
        <div class="info">
          In dieser &Uuml;bersicht k&ouml;nnen Sie die Datenintegrit&auml;t von Xentral &uuml;berpr&uuml;fen.<br />
          F&uuml;r weitere Informationen lesen Sie bitte im Handbuch nach. 
          <input type="submit" name="testen" value="Datenintegrit&auml;t pr&uuml;fen" />
        </div>
      </form>'
    );

    if($this->app->Secure->GetPOST('delete')) {
      $tabelle = $this->app->Secure->GetPOST('tabelle');
      $spalte = $this->app->Secure->GetPOST('spalte');
      $subtabelle = $this->app->Secure->GetPOST('subtabelle');
      $subspalte = $this->app->Secure->GetPOST('subspalte');
      $anz = $this->deleteTableEntries($tabelle, $spalte, $subtabelle, $subspalte);
      if($anz > 0) {
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="info">'.$anz.' Eintr&auml;ge gel&ouml;scht</div>'
        );
        $this->SystemlogShowResultTable();
      }
    }
    
    if($this->app->Secure->GetPOST('testen')) {
      $this->SystemlogShowResultTable();
    }
    
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function SystemlogShowResultTable(){
    $test = $this->testDatabase();

    $warnung = false;
    if(!empty($test)) {
      $this->app->Tpl->Add(
        'TAB1',
        '<table class="mkTable">
          <tr><th>Tabelle von</th><th>zu</th><th>Status</th><th>Beschreibung</th><th>Aktion</th></tr>'
      );
      foreach($test['html'] as $row) {
        $this->app->Tpl->Add('TAB1', $row);
      }
      $warnung = !empty($test['warning']);
    }

    if($warnung) {
      $this->app->Tpl->Add(
        'MESSAGE',
        '<div class="warning">
          Vor dem L&ouml;schen ist es dringend empfohlen ein Backup der Datenbank zu machen. 
          <form method="post"><input type="checkbox" name="cleanconfirm" value="1" /> 
          <label for="cleanconfirm">{|Ich bestätige dass ich die Daten vorher gesichert habe und wirklich die Daten löschen will|}</label>
          <input type="submit" name="cleanall" value="{|Alle fehlerhaften Verknüpfungen löschen|}"></form>
        </div>'
      );
    }
    $this->app->Tpl->Add('TAB1','</table>');
  }

  public function SystemlogMiniDetail()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $dump = $id <= 0?null:$this->app->DB->SelectRow(
      sprintf(
        'SELECT `argumente`, `funktionsname`, `dump` FROM `systemlog` WHERE `id` = %d LIMIT 1', 
        $id
      )
    );
    if(empty($dump)) {
      $this->app->ExitXentral();
    }
    echo '<pre>Argumente der Funktion '.$dump['funktionsname']
      .':<br><br>'.base64_decode($dump['argumente'])
      .'</pre>';
    echo '<br><br><pre>Dump:'.$dump['dump'].'</pre>';
    
    $this->app->ExitXentral();
  }

  public function SystemlogTimeout()
  {
    $cmd = $this->app->Secure->GetGET('cmd');

    if(is_numeric($cmd) && (int)$cmd > 0) {
      usleep((int)$cmd * 1000000);
    }
    echo 'Erfolgreich durchgelaufen!';
    $this->app->ExitXentral();
  }

  /**
   * @param string $name
   *
   * @return bool
   */
  public function hasExectuableExtension($name) {
    if (!function_exists('exec')) {
      throw new Exception('Required Function exec is missing');
    }
    if (!is_string($name)) {
      return false;
    }
    exec(sprintf('whereis %s',$name), $out);
    if (empty($out)) {
      return false;
    }
    $result = $out[0];
    $resultExploded = explode(':', $result);
    array_shift($resultExploded);
    return !empty(trim(implode("",$resultExploded)));
  }

  /**
   * @return bool
   */
  public function isTls12Available(): bool
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::TLS_TEST_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_SSLVERSION, 6);
    curl_exec($ch);
    $curlInfo = curl_getinfo($ch);
    curl_close($ch);

    return (int)$curlInfo['http_code'] !== 0;
  }

  /**
   * @return array
   */
  public function getCompatibilities()
  {
    $ret = [];
    $typeToMessage = [];
    if(!function_exists('fsockopen')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'fsocket nicht installiert!';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'fsocket verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['fsocket'] = count($ret) - 1;
    if(!function_exists('mysqli_connect')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'MYSQLi nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'MYSQLi verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['mysqli'] = count($ret) - 1;
    if(!extension_loaded('mysqlnd')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'Mysqlnd nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'Mysqlnd verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['mysqlnd'] = count($ret) - 1;
    if(!class_exists('\PDO')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'PDO nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'PDO verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['pdo'] = count($ret) - 1;
    if(!function_exists('curl_init')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'cURL nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'cURL verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['curl'] = count($ret) - 1;
    if(!function_exists('stream_socket_enable_crypto')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'stream_socket_enable_crypto nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'stream_socket_enable_crypto verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['crypto'] = count($ret) - 1;
    if(!function_exists('openssl_error_string')) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'OpenSSL nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'OpenSSL verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['openssl'] = count($ret) - 1;
    if(!function_exists('mb_encode_numericentity')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'php-mbstring nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'php-mbstring verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['php-mbstring'] = count($ret) - 1;
    if(!class_exists('SoapClient')) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'php-soap nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'php-soap verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['php-soap'] = count($ret) - 1;
    if(!function_exists('imap_open')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'imap nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'imap verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['imap'] = count($ret) - 1;
    if(!function_exists('simplexml_load_string')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'php-xml nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'php-xml verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['php-xml'] = count($ret) - 1;
    if(!class_exists('ZipArchive')) {
      $tmp['status'] = 'error';
      $tmp['text'] = 'php-zip nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'php-zip verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['php-zip'] = count($ret) - 1;
    try {
      $hasZip = $this->hasExectuableExtension('zip');
    }
    catch(Exception $e) {
      $hasZip = false;
    }

    if(!$hasZip) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'Binary Zip-extension f&uuml;r Kommandozeilen nicht installiert';
      $ret[] = $tmp;
      if(method_exists($this->app->erp, 'setSystemHealth')){
        $this->app->erp->setSystemHealth(
          'server',
          'extension_zip',
          'warning',
          'nicht installiert. Backups können nicht erstellt werden'
        );
      }
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'Binary Zip-extension f&uuml;r Kommandozeilen verf&uuml;gbar';
      $ret[] = $tmp;
      if(!empty($_GET) && method_exists($this->app->erp, 'setSystemHealth')){
        $this->app->erp->setSystemHealth(
          'server',
          'extension_zip',
          'ok',
          'installiert.'
        );
      }
    }
    $typeToMessage['extension-zip'] = count($ret) - 1;

    if(!function_exists('imagejpeg')) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'PHP GD-Erweiterung nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'PHP GD-Erweiterung verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['php-gd'] = count($ret) - 1;
    if(!function_exists('ldap_connect')) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'PHP LDAP-Erweiterung nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'PHP LDAP-Erweiterung verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['php-ldap'] = count($ret) - 1;
    if(!function_exists('ssh2_connect')) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'PHP SSH2-Erweiterung nicht installiert';
      $ret[] = $tmp;
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'PHP SSH2-Erweiterung verf&uuml;gbar';
      $ret[] = $tmp;
    }
    $typeToMessage['tls1-2'] = count($ret) - 1;
    if(!$this->isTls12Available()) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'TLS 1.2 ist nicht verfügbar';
      $ret[] = $tmp;
      if(method_exists($this->app->erp, 'setSystemHealth')){
        $this->app->erp->setSystemHealth(
          'server',
          'tls1-2',
          'warning',
          'TLS 1.2 ist nicht verfügbar'
        );
      }
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'TLS 1.2 ist verfügbar';
      $ret[] = $tmp;
      if(method_exists($this->app->erp, 'setSystemHealth')){
        $this->app->erp->setSystemHealth(
          'server',
          'tls1-2',
          'ok',
          'TLS 1.2 ist verfügbar'
        );
      }
    }
    $typeToMessage['php-ssh2'] = count($ret) - 1;
    if(!function_exists('ioncube_loader_version')) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'Ioncube ist nicht installiert (Eine Installation ist trotzdem m&ouml;glich)';
      $ret[] = $tmp;
    }
    else {
      $ioncube_loader_version = ioncube_loader_version();
      if($ioncube_loader_version[0]< 5 && $ioncube_loader_version[1] === '.') {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'Die Ioncubeversion ist zu alt (Eine Installation ist trotzdem m&ouml;glich)';
        $ret[] = $tmp;
      }
      else{
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Ioncube verf&uuml;gbar';
        $ret[] = $tmp;
      }
    }
    $typeToMessage['ioncube'] = count($ret) - 1;
    $post_max_size = @ini_get('client_max_body_size');
    if($post_max_size == ''){
      $post_max_size = @ini_get('post_max_size');
    }
    if(!$post_max_size) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'Es war nicht m&ouml;glich die verf&uuml;gbare Upload-Kapazit&auml;t zu ermitteln. Es werden mindestens 8 MB empfohlen';
      $ret[] = $tmp;
    }
    else{
      if(strpos($post_max_size, 'M') !== false) {
        $post_max_size = str_replace('M','', $post_max_size);
        $post_max_size *= 1024*1024;
      }
      elseif(strpos($post_max_size, 'K') !== false) {
        $post_max_size = str_replace('K','', $post_max_size);
        $post_max_size *= 1024;
      }

      if($post_max_size < 8*1024*1024) {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'Es sind nur '.round($post_max_size/(1024*1024)).' MB Datei-Upload m&ouml;glich. Empfohlen werden mindestens 8 MB';
        $ret[] = $tmp;
        if(method_exists($this->app->erp, 'setSystemHealth')){
          $this->app->erp->setSystemHealth(
            'server',
            'max_upload',
            'warning',
            'Es sind nur '.round($post_max_size/(1024*1024)).' MB Datei-Upload m&ouml;glich. Empfohlen werden mindestens 8 MB'
          );
        }
      }
      else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Ausreichend Upload-Kapazit&auml;t verf&uuml;gbar (>=8MB)';
        $ret[] = $tmp;
        if(method_exists($this->app->erp, 'setSystemHealth')){
          $this->app->erp->setSystemHealth(
            'server',
            'max_upload',
            'ok',
            'Ausreichend Upload-Kapazit&auml;t verf&uuml;gbar (>=8MB)'
          );
        }
      }
    }
    $typeToMessage['max_post_size'] = $post_max_size;
    $max_execution_time = @ini_get('fastcgi_read_timeout');
    if($max_execution_time == ''){
      $max_execution_time = @ini_get('max_execution_time');
    }
    if(!$max_execution_time) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'Es war nicht m&ouml;glich die verf&uuml;gbare die Scriptlauftzeit zu ermitteln. Es werden mindestens 30 Sekunden empfohlen';
      $ret[] = $tmp;
    }
    elseif($max_execution_time < 30) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'Es stehen nur '.$max_execution_time.' Sekunden Scriptlauftzeit zur verf&uuml;gung. Es werden mindestens 30 Sekunden empfohlen';
      $ret[] = $tmp;
      if(method_exists($this->app->erp, 'setSystemHealth')){
        $this->app->erp->setSystemHealth(
          'server',
          'max_execution_time',
          'warning',
          $tmp['text']
        );
      }
    }
    else {
      $tmp['status'] = 'ok';
      $tmp['text'] = 'Es steht gen&uuml;gend Scriptlauftzeit zur Verf&uuml;gung (>= 30 Sekunden).';
      $ret[] = $tmp;
      if(method_exists($this->app->erp, 'setSystemHealth')){
        $this->app->erp->setSystemHealth(
          'server',
          'max_execution_time',
          'ok',
          'Es steht gen&uuml;gend Scriptlauftzeit zur Verf&uuml;gung (>= 30 Sekunden).'
        );
      }
    }
    $typeToMessage['max_execution_size'] = $max_execution_time;
    $system_cronjob_memory_limit = (string)$this->app->erp->GetKonfiguration('system_cronjob_memory_limit');
    $system_cronjob_max_execution_time = (string)$this->app->erp->GetKonfiguration('system_cronjob_max_execution_time');
    if($system_cronjob_memory_limit !== '') {
      if((int)$system_cronjob_memory_limit <= 0) {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Der Prozessstarter hat gen&uuml;gend Speicher zur Verf&uuml;gung';
        $ret[] = $tmp;
      }
      elseif((int)$system_cronjob_memory_limit <  256 * 1024 * 1024) {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'Es stehen nur '.round($system_cronjob_memory_limit / 1024 / 1024).' MB Arbeitsspeicher f&uuml;r den Prozessstarter zur Verf&uuml;gung';
        $ret[] = $tmp;
      }
      else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Der Prozessstarter hat gen&uuml;gend Speicher zur Verf&uuml;gung';
        $ret[] = $tmp;
      }
    }
    if($system_cronjob_max_execution_time !== '') {
      if((int)$system_cronjob_max_execution_time <= 0) {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Die Prozessstarterlaufzeit ist unbegrenzt';
        $ret[] = $tmp;
      }
      elseif((int)$system_cronjob_max_execution_time < 300) {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'Die Prozessstarterlaufzeit betr&auml;gt nur '.$system_cronjob_max_execution_time.' Sekunden';
        $ret[] = $tmp;
      }
      else{
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Die Prozessstarterlaufzeit betr&auml;gt '.$system_cronjob_max_execution_time.' Sekunden';
        $ret[] = $tmp;
      }
    }


    if(PHP_VERSION < 7.2) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'Die PHP Version ist unter 7.2. Ab Xentral 20.2 wird mindestens PHP 7.2 ben&ouml;tigt';
      $ret[] = $tmp;
    }
    if($this->app->DB->GetVersion() < 57) {
      $tmp['status'] = 'warning';
      $tmp['text'] = 'Die MySQL Version ist unter 5.7. Ab Xentral 20.2 wird mindestens Mysql 5.7 ben&ouml;tigt';
      $ret[] = $tmp;
    }

    $fopenUrlEnabled = ini_get('allow_url_fopen');
    $fopenUrlEnabled = strtolower($fopenUrlEnabled);
    if(!in_array($fopenUrlEnabled, ['1', 'true', 'on'])){
      $ret[] = [
        'status' => 'warning',
        'text'   => 'allow_url_fopen ist nicht in der php.ini aktiviert'
      ];
    }

    return ['type_to_message' => $typeToMessage ,'messages' => $ret];
  }

  public function SystemlogList()
  {
    $this->SystemlogMenu();
    $this->app->YUI->TableSearch('TAB1','systemlog','show','','',basename(__FILE__), __CLASS__);
    $cli_infos = [];
    $system_phpversion_cli = $this->app->erp->GetKonfiguration('system_phpversion_cli');
    if(!empty($system_phpversion_cli)){
      $cli_infos[] = 'CLI-PHP-Version: '.$system_phpversion_cli;
    }
    $system_cronjob_memory_limit = (string)$this->app->erp->GetKonfiguration('system_cronjob_memory_limit');
    if($system_cronjob_memory_limit === '-1' || $system_cronjob_memory_limit === '0') {
      $cli_infos[] = 'Memory-Limit: unbegrenzt';
    }
    elseif(!empty($system_cronjob_memory_limit)) {
      $cli_infos[] = 'Memory-Limit: '.$system_cronjob_memory_limit;
    }
    $system_cronjob_max_execution_time = (string)$this->app->erp->GetKonfiguration('system_cronjob_max_execution_time');
    if($system_cronjob_max_execution_time === '0') {
      $cli_infos[] = 'Max-Execution-Time: unbegrenzt';
    }
    elseif(!empty($system_cronjob_max_execution_time)) {
      $cli_infos[] = 'Max-Execution-Time: '.$system_cronjob_max_execution_time;
    }
    $system_cronjob_php_ini_loaded_file = $this->app->erp->GetKonfiguration('system_cronjob_php_ini_loaded_file');
    if(!empty($system_cronjob_php_ini_loaded_file)) {
      $cli_infos[] = 'Geladene ini-Datei: '.$system_cronjob_php_ini_loaded_file;
    }
    $system_cronjob_get_current_user = $this->app->erp->GetKonfiguration('system_cronjob_get_current_user');
    if(!empty($system_cronjob_get_current_user)) {
      $cli_infos[] = 'CLI-Benutzer: '.$system_cronjob_get_current_user;
    }
    $cli_infos[] = 'Xentral-Pfad: '.dirname(dirname(__DIR__));

    $cli_infos[] = 'Userdata-Pfad: '.$this->app->Conf->WFuserdata;

    $size = (int)$this->app->erp->GetKonfiguration('userdatasize');
    if($size > 0) {
      $cli_infos[] = 'Useredata: '.number_format($size,0,',','.') .' MB';
    }
    foreach([
      'dms',
      'pdfarchiv',
      'pdfmirror',
      'tmp',
      'wiki',
      'emailbackup',
      'uebertragung',
    ] as $value) {
      $size = (int)$this->app->erp->GetKonfiguration('userdata'.$value.'size');
      if($size > 0) {
        $cli_infos[] = 'Useredata '.$value.': '.number_format($size,0,',','.') .' MB';
      }
    }
    $backupsize =  (int)$this->app->erp->GetKonfiguration('backupsize');
    if($backupsize > 0) {
      $cli_infos[] = 'Backup: '.number_format($backupsize,0,',','.') .' MB';
    }
    $databasesize =  (int)$this->app->erp->GetKonfiguration('databasesize');
    if($databasesize > 0) {
      $cli_infos[] = 'Datenbank: '.number_format($databasesize,0,',','.') .' MB';
    }
    $databasesizelog =  (int)$this->app->erp->GetKonfiguration('databasesizelog');
    if($databasesizelog > 0) {
      $cli_infos[] = 'davon Logeinträge: '.number_format($databasesizelog,0,',','.') .' MB';
    }

    $lastSizeUpdate = $this->app->erp->GetKonfiguration('lastsizeupdate');
    if(!empty($lastSizeUpdate) && strtotime($lastSizeUpdate) > 1) {
      $cli_infos[] = 'Letzte Speicherprüfung: '.date('d.m.Y H:i:s',strtotime($lastSizeUpdate));
    }
    /** @var Systemhealth $sytemHealth */
    $sytemHealth = $this->app->loadModule('systemhealth');
    $memory = method_exists($sytemHealth, 'getMemory')?$sytemHealth->getMemory():[];
    if(!empty($memory['memory'])) {
      $cli_infos[] = 'Arbeitssteicher: '.number_format($memory['memory']['sum'] / 1024,0,',','.') .' MB ('.
        number_format($memory['memory']['free'] / 1024,0,',','.').' MB frei)';
    }
    if(!empty($memory['swap'])) {
      $cli_infos[] = 'Swap: '.number_format($memory['swap']['sum'] / 1024,0,',','.') .' MB ('.
        number_format($memory['swap']['free'] / 1024,0,',','.').' MB frei)';
    }

    if(!empty($cli_infos)) {
      $this->app->Tpl->Add('TAB3','<div class="info">'.implode('<br />',$cli_infos).'</div>');
    }
    $system_cronjob_phpinfo = $this->app->erp->GetKonfiguration('system_cronjob_phpinfo');
    if(!empty($system_cronjob_phpinfo)) {
      $this->app->Tpl->Add('TAB3','<fieldset><legend>PHP-Info</legend><pre>');
      $this->app->Tpl->Add('TAB3',$system_cronjob_phpinfo);
      $this->app->Tpl->Add('TAB3','</pre></fieldset>');
    }

    ob_start();
    phpinfo();
    $phpinfo = ob_get_clean();
    
    $phpinfo = explode('<body>',$phpinfo,2);
    $phpinfo = $phpinfo[1];
    $phpinfo = explode('</body>', $phpinfo);
    $phpinfo = reset($phpinfo);
    $this->app->Tpl->Add('TAB2','<textarea id="phpinfo">'.$phpinfo.'</textarea>');
    $this->app->YUI->CkEditor('phpinfo','none',array('height'=>'500px'));

    $ret = $this->getCompatibilities();

    foreach($ret['messages'] as $Message) {
      $this->app->Tpl->Add(
        'TAB4',
        '<div style="font-size:12px;background-color:'
        .($Message['status'] === 'error'?'#FA5858'
          :($Message['status'] == 'warning'?'#F4FA58':'#9FF781')
        ).';">'.$Message['text']."</div><div style=\"height:10px;\"></div>\r\n"
      );
    }
    
    $this->app->Tpl->Parse('PAGE','systemlog_list.tpl');
  }

  public function SystemlogMenu()
  {
    $this->app->erp->Headlines('Systemlog');
    $this->app->erp->MenuEintrag('index.php?module=servicetools&action=list','Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=systemlog&action=list','Aktualisieren');
    $this->app->erp->MenuEintrag('index.php?module=systemlog&action=datenbank','Datenbank pr&uuml;fen');
  }

  public function Install() {
    $this->app->erp->CheckTable('systemlog');
    $this->app->erp->CheckColumn('id','int(11)','systemlog','NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('meldung','TEXT','systemlog','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('dump','TEXT','systemlog','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('module','VARCHAR(64)','systemlog','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('action','VARCHAR(64)','systemlog','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('bearbeiter','VARCHAR(64)','systemlog','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('funktionsname','VARCHAR(64)','systemlog','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('datum','DATETIME','systemlog');
    $this->app->erp->CheckColumn('parameter','int(11)','systemlog','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('argumente','TEXT','systemlog','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('level','int(11)','systemlog','DEFAULT 0 NOT NULL');

    $this->app->erp->RegisterHook('systemhealth_cronjob', 'systemlog', 'SystemhealthCronjob');
  }

  public function SystemhealthCronjob()
  {
    $this->testDatabase();

    $this->getCompatibilities();
  }

  /**
   * @param string $meldung
   * @param int    $level
   * @param string $dump
   * @param string $module
   * @param string $action
   * @param string $functionname
   *
   * @return int|string|null
   */
  public function addSystemLog($meldung='',$level=0,$dump='',$module='',$action='',$functionname='')
  {
    $isSecure = isset($this->app->Secure);
    if($module=='' && $isSecure) {
      $module = (string)$this->app->Secure->GetGET('module');
    }
    if($action=='' && $isSecure) {
      $action = (string)$this->app->Secure->GetGET('action');
    }
    $id = 0;
    if($isSecure){
      $id = $this->app->Secure->GetGET('id');
    }

    $argumente = '';
    if($functionname=='') {
      if (strnatcmp(phpversion(),'5.0.0') >= 0) {
        $backtrace = debug_backtrace();
        $functionname = $backtrace[1]['function'];
        if($functionname!=='Run'){
          $argumente = base64_encode(print_r($backtrace[1]['args'], true));
        }
      }
    }

    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `systemlog` (`module`, `action`, `meldung`, `dump`, `datum`, `bearbeiter`,
                       `funktionsname`, `parameter`, `argumente`, `level`)
        VALUES ('%s','%s','%s','%s',NOW(), '%s', '%s',%d,'%s',%d)",
        $module,$action, $meldung, $dump, isset($this->app->User)?$this->app->User->GetName():'',
        $this->app->DB->real_escape_string($functionname), $id, $argumente, $level
      )
    );

    return $this->app->DB->GetInsertID();
  }
}
