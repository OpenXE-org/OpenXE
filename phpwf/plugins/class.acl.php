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


use Xentral\Components\EnvironmentConfig\EnvironmentConfig;

class Acl
{
  protected $session_id;
  /** @var Application $app */
  public function __construct($app)
  {
    $this->app = $app;
  }


  public function CheckTimeOut()
  {
    $this->session_id = session_id();

    if(isset($_COOKIE['CH42SESSION']) && $_COOKIE['CH42SESSION']!='')
    {
      $this->session_id = $_COOKIE['CH42SESSION'];
      if(!(isset($_GET) && isset($_GET['module']) && isset($_GET['action']) && $_GET['module'] == 'welcome' && $_GET['action'] == 'poll'))$this->app->DB->Update("UPDATE useronline SET time=NOW(),login=1 WHERE sessionid='".$this->app->DB->real_escape_string($_COOKIE["CH42SESSION"])."' LIMIT 1");
    }

    if (empty($this->session_id)) {
      return false;
    }

    // check if user is applied
    $sessid =  $this->app->DB->Select("SELECT sessionid FROM useronline,user WHERE
          login='1' AND sessionid='".$this->app->DB->real_escape_string($this->session_id)."' AND user.id=useronline.user_id AND user.activ='1' LIMIT 1");

    if($this->session_id == $sessid)
    { 
      // check if time is expired
      $time =  $this->app->DB->Select("SELECT UNIX_TIMESTAMP(time) FROM useronline,user WHERE
            login='1' AND sessionid='".$this->app->DB->real_escape_string($this->session_id)."' AND user.id=useronline.user_id AND user.activ='1' LIMIT 1");

      if(($this->app->DB->Select('SELECT UNIX_TIMESTAMP(now())')-$time) > $this->app->Conf->WFconf['logintimeout'])
      {
        if(!isset($_COOKIE['CH42SESSION']) || $_COOKIE['CH42SESSION']=='')
        {
          $this->Logout("Ihre Zeit ist abgelaufen, bitte melden Sie sich erneut an.",true);
          return false;
        }
      }
      else {
        // update time
        if(!(isset($_GET) && isset($_GET['module']) && isset($_GET['action']) && $_GET['module'] == 'welcome' && $_GET['action'] == 'poll'))$this->app->DB->Update("UPDATE useronline,user SET useronline.time=NOW() WHERE
            login='1' AND sessionid='".$this->app->DB->real_escape_string($this->session_id)."' AND user.id=useronline.user_id AND user.activ='1'");

        session_write_close(); // Blockade wegnehmen           

        return true; 
      }
    }
  }

  /**
   * @param string $usertype
   * @param string $module
   * @param string $action
   * @param string $userid
   *
   * @return bool
   */
  public function Check($usertype,$module,$action, $userid='')
  {
    $ret = false;
    $permissions =
      !empty($this->app->Conf->WFconf['permissions'])
      && !empty($this->app->Conf->WFconf['permissions'][$usertype])
      && isset($this->app->Conf->WFconf['permissions'][$usertype][$module])
      ?$this->app->Conf->WFconf['permissions'][$usertype][$module]
      :null;

    if($usertype==='admin'){
      return true;
    }

    if($this->app->User->GetID() > 0) {
      if($module==='ajax') {
        return true;
      }
      if($module === 'welcome') {
        if(
          in_array(
            $action,
            [
              'css',
              'logo',
              'start',
              'meineapps',
              'spooler',
              'redirect',
              'login',
              'logout',
              'passwortvergessen',
            ]
          )
        ) {
          return true;
        }
      }
      if($module === 'gpsstechuhr') {
        if(in_array($action, ['create','save'])) {
          return true;
        }
      }

      if($module === 'learningdashboard') {
        if(in_array($action, ['list', 'ajax', ''])) {
          return true;
        }
      }

      if($module==='drucker' && $action==='spoolerdownload') {
        return true;
      }
      if($module==='wizard' && $action==='ajax') {
        return true;
      }
      if($module==='supersearch' && $action==='ajax') {
        return true;
      }
      if($module === 'appstore' && $action = 'list') {
        return true;
      }
    }

    // Change Userrights with new 'userrights'-Table	
    if(!is_array($permissions)) {
      $permissions = [];
    }
    if(is_numeric($userid) && $userid>0) {
      $permission_db = $this->app->DB->Select("SELECT permission FROM userrights WHERE module='".$this->app->DB->real_escape_string($module)."' AND action='".$this->app->DB->real_escape_string($action)."' AND user='$userid' LIMIT 1");
      $actionkey = array_search($action, $permissions);
      if($actionkey===false) {
        if($permission_db=='1')
          $permissions[] = $action;
      }else {
        if($permission_db=='0'){
          unset($permissions[$actionkey]);
          $permissions = array_values($permissions);
        }				
      }
    }
    // --- END ---

    foreach($permissions as $key => $val) {
      if($val==$action) {
        $ret = true;
        break;
      }
    }

    if($action=='' && $module==''){
      $ret = true;
    }

    if($module === 'welcome' && in_array($action, array('login','main','logout'))) {
      $ret = true;
    }
    
    if($ret && $usertype!=='admin') {
      $id = (int)$this->app->Secure->GetGET('id');
      if($id) {
        if(
          $action === 'edit' || $action === 'delete' || $action === 'copy' || $action === 'dateien'
          || ($action === 'rollen' && $module === 'adresse')
          || $action === 'inlinepdf' || $action === 'pdf' || $action === 'send'
        ) {
          switch($module)
          {
            case 'auftrag':
            case 'rechnung':
            case 'gutschrift':
            case 'angebot':
            case 'anfrage':
            case 'lieferschein':
              $ret = $this->app->erp->UserProjektRecht($this->app->DB->Select("SELECT projekt FROM $module WHERE id = '$id'")) || ($this->app->erp->ModulVorhanden('vertriebscockpit') && ($this->app->DB->Select("SELECT a.id FROM adresse a INNER JOIN $module t ON a.id = t.adresse WHERE t.id = '$id' AND a.vertrieb = '".$this->app->User->GetAdresse()."' LIMIT 1") > 0 || $this->app->DB->Select("SELECT usereditid FROM $module t WHERE t.id = '$id' AND t.usereditid = '".$this->app->User->GetID()."' LIMIT 1")));
            break;
            case 'dateien':

              $sql = "SELECT objekt FROM datei_stichwoerter WHERE datei = %s";
              $dateiModul = strtolower($this->app->DB->Select(sprintf($sql,$id)));

              //TODO datei_stichwoerter.objekt ist nicht zuverlässig für alle Datentypen. Deswegen nur zur Absicherung der bekannten Fälle #604706
              if(array_search($dateiModul,['auftrag','rechnung','lieferschein','bestellung','angebot','verbindlichkiet','proformarechnung','anfrage','artikel','adresse','produktion'])!==false){

                $sql = "SELECT parameter FROM datei_stichwoerter WHERE datei = %s";
                $idModul = $this->app->DB->Select(sprintf($sql,$id));

                $ret = $this->app->erp->UserProjektRecht($this->app->DB->Select("SELECT projekt FROM $dateiModul WHERE id = '$idModul'"));
              }
              break;
            case 'konten':
            case 'artikel':
            case 'onlineshops':
            case 'benutzer':
            case 'bestellung':
            case 'produktion':
              $ret = $this->app->erp->UserProjektRecht($this->app->DB->Select("SELECT projekt FROM $module WHERE id = '$id'"));
            break;
            case 'adresse':
              $ret = $this->app->erp->UserProjektRecht($this->app->DB->Select("SELECT projekt FROM $module WHERE id = '$id'")) || ($this->app->erp->ModulVorhanden('vertriebscockpit') && $this->app->DB->Select("SELECT id FROM adresse WHERE id = '$id' AND vertrieb = '".$this->app->User->GetAdresse()."' LIMIT 1") > 0);
            break;
          }
        } else {
          $modact = array('artikel'=>array('einkauf', 'dateien','eigenschaften','verkauf','statistik','etikett','offenebestellungen','offeneauftraege','zertifikate','fremdnummern')
          ,'adresse' => array('rollen','ansprechpartner','lieferadresse','accounts','brief','belege','kundeartikel','abrechnungzeit','artikel','service','serienbrief')
          ,'lieferschein' => array('paketmarke')
          );
          foreach($modact as $mod => $actarr)
          {
            if($module == $mod)
            {
              foreach($actarr as $v)
              {
                if($v == $action)
                {
                  if($module === 'adresse')
                  {
                    $ret = $this->app->erp->UserProjektRecht($this->app->DB->Select("SELECT projekt FROM $module WHERE id = '$id'")) || ($this->app->erp->ModulVorhanden('vertriebscockpit') && $this->app->DB->Select("SELECT id FROM adresse WHERE id = '$id' AND vertrieb = '".$this->app->User->GetAdresse()."' LIMIT 1") > 0);
                  }else{
                    $ret = $this->app->erp->UserProjektRecht($this->app->DB->Select("SELECT projekt FROM $module WHERE id = '$id'"));
                  }
                }
              }
            }
          }
        }
      }
    }

    // wenn es nicht erlaubt ist 
    if($ret!=true)
    {
      if($this->app->User->GetID()<=0)
      {
        $this->app->erp->Systemlog("Keine gueltige Benutzer ID erhalten",1);
        echo str_replace('BACK',"index.php?module=welcome&action=login",$this->app->Tpl->FinalParse("permissiondenied.tpl"));
      }
      else {
        $this->app->erp->Systemlog("Fehlendes Recht",1);
        echo str_replace('BACK',isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'',$this->app->Tpl->FinalParse("permissiondenied.tpl"));
      }
      http_response_code(401);
      exit;
    }
    return $ret;
  }

  /**
   * @param int $userId
   * @param int $addressId
   *
   * @return array
   */
  public function getEmailAddressFromUserAddress(int $userId, int $addressId): array
  {
    $mailAddress = trim((string)$this->app->DB->Select(
      "SELECT `email` FROM `adresse` WHERE `id` = '{$addressId}' AND `geloescht` <> 1 LIMIT 1"
    ));
    $mailAddresses = [];
    if($mailAddress !== '') {
      $mailAddresses[] = $mailAddress;
    }
    $isUserAdmin = $this->app->DB->Select(
      "SELECT `id` FROM `user` WHERE `id` = '{$userId}' AND `type` = 'admin' LIMIT 1"
      ) > 0;
    if(!$isUserAdmin) {
      return $mailAddresses;
    }
    $mailAddress = trim((string)$this->app->erp->Firmendaten('email'));
    if($mailAddress !== '' && $mailAddress !== 'mail@ihr_mail_server.de') {
      $mailAddresses[] =  $mailAddress;
    }

    /** @var EnvironmentConfig $environmentConfig */
    $environmentConfig = $this->app->Container->get('EnvironmentConfig');

    $mailAddresses = array_merge($mailAddresses, $environmentConfig->getSystemFallbackEmailAddresses());

    return array_unique($mailAddresses);
  }

  public function Passwortvergessen()
  {
    $code = $this->app->Secure->GetGET('code');
    $vergessenusername = $this->app->Secure->GetPOST('vergessenusername');
    $aendern = $this->app->Secure->GetPOST('aendern');
    $this->app->DB->Update("UPDATE `user` SET vergessencode = '' WHERE vergessencode <> '' AND (isnull(`vergessenzeit`) OR `vergessenzeit` = '0000-00-00 00:00:00' OR now() > DATE_ADD(`vergessenzeit`, INTERVAL 1 DAY) )");
    if($code)
    {
      $user = $this->app->DB->Select("SELECT id FROM `user` WHERE vergessencode <> '' AND vergessencode = '$code' LIMIT 1");
      if($user)
      {
        if($aendern)
        {
          $passwortwiederholen = $this->app->Secure->GetPOST('passwortwiederholen');
          $passwort = $this->app->Secure->GetPOST('passwort');
          if((string)$passwort !== '') {
            if($passwort === $passwortwiederholen) {
              if(strlen($passwort) >= 6) {
                $salt = hash('sha512',microtime(true));
                $passwordsha512 = $this->app->DB->real_escape_string(hash('sha512', $_POST['passwort'].$salt));
                $salt = $this->app->DB->real_escape_string($salt);
                $this->app->DB->Update("UPDATE `user` SET `vergessencode` = '',`fehllogins` = 0, `password` = '', `passwordmd5` = '',`passwordhash`='', `salt` = '$salt',`passwordsha512` = '".$passwordsha512."'  WHERE `id` = '$user' LIMIT 1");
                $this->app->DB->Delete("DELETE FROM `useronline` WHERE `user_id`='".$user."'");

                $this->app->DB->Insert("INSERT INTO `useronline` (`user_id`,`sessionid`, `ip`, `login`, `time`)
                    VALUES ('".$user."','".$this->session_id."','".$_SERVER['REMOTE_ADDR']."','1',NOW())");
                header('Location: index.php?module=welcome&action=start&msg='.$this->app->erp->base64_url_encode('<div class="info">Passwort wurde ge&auml;ndert</div>'));
                exit;
              }
              $this->app->Tpl->Set('SPERRMELDUNGNACHRICHT', '<div style="fontsize=120%;font-weigt:bold;color:red; ">Das Passwort muss mindestens 6 Zeichen besitzen.</div>');
            }else{
              $this->app->Tpl->Set('SPERRMELDUNGNACHRICHT', '<div style="fontsize=120%;font-weigt:bold;color:red; ">Passw&ouml;rter stimmen nicht &uuml;berein.</div>'); 
            }
          }else{
            $this->app->Tpl->Set('SPERRMELDUNGNACHRICHT', '<div style="fontsize=120%;font-weigt:bold;color:red; ">Bitte ein Passwort eingeben.</div>'); 
          }
        }
        $this->app->Tpl->Set('VORZURUECKSETZEN', '<!--');
        $this->app->Tpl->Set('NACHZURUECKSETZEN', '-->');
        $this->app->Tpl->Set('USERNAME', $this->app->DB->Select("SELECT `username` FROM `user` WHERE `id` = '$user' LIMIT 1"));
      }else{
        $this->app->Tpl->Set('SPERRMELDUNGNACHRICHT', '<div style="fontsize=120%;font-weigt:bold;color:red; ">Der Link ist nicht mehr g&uuml;ltig.</div>');      
        $this->app->Tpl->Set('VORPASSWORT', '<!--');
        $this->app->Tpl->Set('NACHPASSWORT', '-->');
      }
    }
    else{
      if((string)$vergessenusername !== '') {
        $user = $this->app->DB->SelectRow(
          "SELECT `id`, `adresse` FROM `user` WHERE `activ` = 1 AND `username` = '{$vergessenusername}' LIMIT 1"
        );
        $userId = $user['id'] ?? null;
        $addressId = $user['adresse'] ?? null;
        $emailAddresses = [];
        $mailSuccessfullySent = false;
        if($userId > 0) {
          $emailAddresses = $this->getEmailAddressFromUserAddress((int)$userId, (int)$addressId);
        }
        if(!empty($emailAddresses)) {
          $name = $vergessenusername;
          $anrede = '';
          if($addressId > 0) {
            $addressFields = $this->app->DB->SelectRow(
              "SELECT `name`, `anschreiben` FROM `adresse` WHERE `id` = '{$addressId}' LIMIT 1"
            );
            $name = $addressFields['name'] ?? null;
            $anrede = $addressFields['anschreiben'] ?? null;
          }

          $code = sha1(microtime(true));

          if(
            !$this->app->DB->Select(
              "SELECT `id`
              FROM `user`
              WHERE `id` = '{$userId}' AND `vergessencode` <> ''
                AND ifnull(`vergessenzeit`, '0000-00-00 00:00:00') <> '0000-00-00 00:00:00'
                  AND `vergessenzeit` > DATE_SUB(now(), INTERVAL 5 MINUTE)
              LIMIT 1"
            )
          ) {
            $this->app->DB->Update(
              "UPDATE `user` SET `vergessencode` = '{$code}', `vergessenzeit` = now() WHERE `id` = '{$userId}' LIMIT 1"
            );
            $language = $this->app->DB->Select("SELECT `sprachebevorzugen` FROM `user` WHERE `id`='{$userId}' LIMIT 1");
            if($language==''){
              $language = $this->app->DB->Select("SELECT `sprache` FROM `adresse` WHERE `id`='{$addressId}' LIMIT 1");
            }
            if($language == ''){
              $language = 'deutsch';
            }
            $mailContent = $this->app->erp->GetGeschaeftsBriefText('passwortvergessen', $language, 0);
            $mailSubject = $this->app->erp->GetGeschaeftsBriefBetreff('passwortvergessen', $language, 0);
            if((string)$mailContent === '' && $language !== 'deutsch') {
              $language = 'deutsch';
              $mailContent = $this->app->erp->GetGeschaeftsBriefText('passwortvergessen', $language, 0);
              $mailSubject = $this->app->erp->GetGeschaeftsBriefBetreff('passwortvergessen', $language ,0);
            }
            if((string)$mailSubject === '') {
              $mailSubject = 'Xentral Passwort zurücksetzen';
            }
            if((string)$mailContent === '') {
              $mailContent = "{ANREDE} {NAME} Bitte klicken Sie auf dem Link <a href=\"{URL}\">{URL}</a> um Ihr Xentral-Passwort zu ändern";
            }
            $server = '';
            $isSecure = false;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
              $isSecure = true;
            }
            elseif ((!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')) {
              $isSecure = true;
            }
            $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
            if($_SERVER['SERVER_NAME']!='' && $_SERVER['SERVER_NAME'] !== '_') //MAMP auf macos
            {
              $server = $REQUEST_PROTOCOL.'://'.$_SERVER['SERVER_NAME'].(($_SERVER['SERVER_PORT']!=80 && $_SERVER['SERVER_PORT'] != 433)?":".$_SERVER['SERVER_PORT']:'').$_SERVER['REQUESR_URI'].$_SERVER['SCRIPT_NAME'];
            }
            elseif($_SERVER['SCRIPT_URI'] != '')
            {
              $server = $_SERVER['SCRIPT_URI'];
            }
            elseif($_SERVER['REQUEST_URI'] != '' && $_SERVER['SERVER_ADDR']!='' && $_SERVER['SERVER_ADDR']!=='::1' && strpos($_SERVER['SERVER_SOFTWARE'],"nginx")===false)
            {
              $server = (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']?$REQUEST_PROTOCOL.'://'.$_SERVER['SERVER_ADDR'].(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443?':'.$_SERVER['SERVER_PORT']:''):'').$_SERVER['SCRIPT_NAME'];
            }

            $pos = strripos($server, 'index.php');
            if($pos) {
              $server = rtrim(substr($server, 0, $pos), '/') . '?module=welcome&action=passwortvergessen&code=' . $code;
            }
            else {
              $server .= '/index.php?module=welcome&action=passwortvergessen&code=' . $code;
            }

            $serverLocation = $this->app->Location->getServer();
            if(!empty($serverLocation)) {
              $server = rtrim($serverLocation,'/') . '?module=welcome&action=passwortvergessen&code=' . $code;
            }
            foreach(['default', 'fallback'] as $sentSetting) {
              if($sentSetting === 'fallback') {
                $db = $this->app->Conf->WFdbname;
                if(
                  empty(erpAPI::Ioncube_Property('cloudemail'))
                  || $this->app->erp->firmendaten[$db]['email'] === erpAPI::Ioncube_Property('cloudemail')
                ) {
                  break;
                }
                $this->app->erp->firmendaten[$db]['mailanstellesmtp'] = 1;
                $this->app->erp->firmendaten[$db]['email'] = erpAPI::Ioncube_Property('cloudemail');
              }
              foreach ($emailAddresses as $email) {
                $recipientMailAddress = $email;
                $recipientName = $name;
                if(empty($recipientMailAddress) || empty($recipientName)) {
                  continue;
                }

                $mailContent = str_replace(['{NAME}', '{ANREDE}', '{URL}'], [$recipientName, $anrede, $server], $mailContent);

                if(!$this->app->erp->isHTML($mailContent)){
                  $mailContent = str_replace("\r\n", '<br>', $mailContent);
                }
                $mailSuccessfullySent = $this->app->erp->MailSend(
                  $this->app->erp->GetFirmaMail(), $this->app->erp->GetFirmaAbsender(),
                  $recipientMailAddress, $recipientName, $mailSubject, $mailContent, '', 0, true, '', '', true
                );
                if($mailSuccessfullySent){
                  break 2;
                }
              }
            }
          }
        }
        if($mailSuccessfullySent || $userId <= 0) {
          $this->app->Tpl->Set(
            'SPERRMELDUNGNACHRICHT',
            '<div>Bitte pr&uuml;fen Sie Ihr E-Mail-Postfach. Falls keine E-Mail angekommen ist wenden Sie sich bitte an den Administrator.</div>'
          );
        }
        elseif(empty($emailAddresses)) {
          $this->app->Tpl->Set(
            'SPERRMELDUNGNACHRICHT',
            '<div>Es ist keine Email hinterlegt. Bitte wenden Sie sich an den Administrator.</div>'
          );
        }
        else{
          $this->app->Tpl->Set(
            'SPERRMELDUNGNACHRICHT',
            '<div>Es ist ein Fehler beim Senden der Email aufgetreten. Bitte wenden Sie sich an den Administrator.</div>'
          );
        }
      }
      $this->app->Tpl->Set('VORPASSWORT', '<!--');
      $this->app->Tpl->Set('NACHPASSWORT', '-->');
    }
    
    $this->app->Tpl->Parse('PAGE','passwortvergessen.tpl');
  }

  /**
   * @param int|null $id
   *
   * @return bool|int
   */
  public function IsAdminadmin($id = null)
  {
    if($id === null && !empty($this->app->User)  && method_exists($this->app->User, 'GetID')) {
      $id = $this->app->User->GetID();
    }
    if(!$id) {
      return false;
    }
    $userarr = $this->app->DB->SelectRow("SELECT * FROM `user` WHERE id = '$id' AND activ = 1 AND ifnull(hwtoken, 0) = 0 LIMIT 1");
    if(empty($userarr)) {
      return false;
    }
    $hash = 'isadminadmin_'.md5(json_encode($userarr));
    $cache = (string)$this->app->User->GetParameter($hash);
    if($cache !== '') {
      $cache = (int)$cache;
      if($cache === 0) {
        return false;
      }
      if($cache === 1) {
        return true;
      }
      if($cache === 2) {
        return 2;
      }
    }
    $lastCache = $this->app->User->GetParameter('isadminadmin_lastcache');
    $isSameHash = $lastCache === $hash;
    if((string)$lastCache !== '' && !$isSameHash){
      $this->app->User->deleteParameter($lastCache);
    }
    if(!$isSameHash) {
      $this->app->User->SetParameter('isadminadmin_lastcache', $hash);
    }
    if($userarr['passwordhash'] != '' && password_verify (  'admin' ,  $userarr['passwordhash'] )) {
      $this->app->User->SetParameter($hash, 1);
      return true;
    }
    if($userarr['passwordhash'] != '') {
      $ret = password_verify (  $userarr['username'] ,  $userarr['passwordhash'] )?2:false;
      $this->app->User->SetParameter($hash, (int)$ret);
      return $ret;
    }

    if($userarr['passwordsha512'] != '' && hash('sha512','admin'.$userarr['salt']) === $userarr['passwordsha512']) {
      $this->app->User->SetParameter($hash, 1);
      return true;
    }
    if($userarr['passwordsha512'] != '') {
      $ret =  hash('sha512',$userarr['username'].$userarr['salt']) === $userarr['passwordsha512']?2:false;
      $this->app->User->SetParameter($hash, (int)$ret);
      return $ret;
    }

    if(md5('admin') == $userarr['passwordmd5']) {
      $this->app->User->SetParameter($hash, 1);
      return true;
    }

    $ret = md5($userarr['username']) == $userarr['passwordmd5']?2:false;
    $this->app->User->SetParameter($hash, (int)$ret);
    return $ret;
  }

  public function Login()
  {
    $this->app->Tpl->Set('LOGINWARNING', 'display:none;visibility:hidden;');
    if($this->IsInLoginLockMode() === true){
      $this->app->Tpl->Set('LOGINWARNING', '');
      return;
    }

    $multidbs = $this->app->getDbs();
    if(count($multidbs) > 1)
    {
      $options = '';
      foreach($multidbs as $k => $v)
      {
        $options .= '<option value="'.$k.'">'.$v.'</options>';
      }
      $this->app->Tpl->Add(
        'MULTIDB',
        '<div class="field">
						<select id="db" name="db">
							<option value="'.$this->app->Conf->WFdbname.'">- System wählen -</option>
							'.$options.'
						</select><input type="hidden" name="dbselect" value="true">
					</div>'
      );
    }
    $username = $this->app->DB->real_escape_string($this->app->Secure->GetPOST("username"));
    $password = $this->app->Secure->GetPOST("password");
    $passwordunescaped = $this->app->Secure->GetPOST('password',"","","noescape");
    $stechuhrdevice = $this->app->Secure->GetPOST('stechuhrdevice');
    $rfidcode = $this->app->Secure->GetPOST('rfidcode');
    $rfid = $this->app->Secure->GetPOST('rfid');
    $code = $this->app->Secure->GetPOST('code');

    $adminadmin = false;
    if(strtolower($username) === 'admin' && $password === 'admin') {
      $adminadmin = true;
    }
    elseif($username === $password) {
      $adminadmin = 2;
    }

    $token = $this->app->Secure->GetPOST("token");


    if($username==''&& ($password=='' || $token=='') && $stechuhrdevice == '' && $rfid == ''){
      setcookie('nonavigation',false);

      if($this->app->DB->connection)
        $this->app->Tpl->Set('LOGINMSG','Bitte geben Sie Benutzername und Passwort ein.');
      else
        $this->app->Tpl->Set('LOGINMSG', '<div style="fontsize=120%;font-weigt:bold;color:red; ">Fehler: Keine Verbindung zur Datenbank möglich!</div>');

      if($this->app->erp->UserDevice()==='smartphone')
      {
        $this->app->Tpl->Parse('PAGE','login_smartphone.tpl');
      }
      else {
        $this->selectLanguageOptionByServerVariable();
        $this->app->Tpl->Parse('PAGE','login.tpl');
      }
    }
    else {
      // Benutzer hat Daten angegeben
      $userdata = $this->app->DB->SelectArr("SELECT * FROM `user` WHERE username='".$username."' AND activ='1' LIMIT 1");
      if($userdata)
      {
        $userdata = reset($userdata);
      }
      $encrypted = isset($userdata['encrypted'])?$userdata['encrypted']:'';
      $encrypted_md5 = isset($userdata['passwordmd5'])?$userdata['passwordmd5']:'';
      $fehllogins = isset($userdata['fehllogins'])?$userdata['fehllogins']:'';
      $type = isset($userdata['type'])?$userdata['type']:'';
      $externlogin = isset($userdata['externlogin'])?$userdata['externlogin']:'';
      $hwtoken = isset($userdata['hwtoken'])?$userdata['hwtoken']:'';
      $salt = isset($userdata['salt'])?$userdata['salt']:'';

      $usepasswordhash = true;
      $passwordhash = $this->app->DB->Select("SELECT passwordhash FROM `user` WHERE username='".$username."' AND activ='1' LIMIT 1");
      if($this->app->DB->error())$usepasswordhash = false;

      $usesha512 = true;
      $passwordsha512 = $this->app->DB->Select("SELECT passwordsha512 FROM `user` WHERE username='".$username."' AND activ='1' LIMIT 1");
      if($this->app->DB->error())$usesha512 = false;
      
      $stechuhrdevicelogin = false;
      $devices = $this->app->DB->SelectArr("SELECT * from stechuhrdevice where aktiv = 1 and code = '$code' AND code <> ''");
      if($devices)
      {
        foreach($devices as $device)
        {
          $IP = ip2long($_SERVER['REMOTE_ADDR']);
          $devIP = ip2long($device['IP']);
          $submask = ip2long($device['submask']);
          
          $maskIP = $IP & $submask;
          $dbIP = $devIP & $submask;
          if($maskIP == $dbIP)
          {
            $stechuhrdevicelogin = true;
          }
        }
      }
      if($code && !$stechuhrdevicelogin)
      {
        setcookie('nonavigation',false);
        $this->app->Tpl->Set('RESETSTORAGE','
        
        if(typeof(Storage) !== "undefined") {
          var devicecode = localStorage.getItem("devicecode"); 
          if(devicecode)
          {
            localStorage.setItem("devicecode", "");
          }
        }
        if(typeof indexedDB != "undefined")
        {
          var request = indexedDB.open(\'wawisionstechuhrdevice\', 1);

          request.onupgradeneeded = function(){
            var db = this.result;
            if(!db.objectStoreNames.contains(\'stechuhr\')){
              store = db.createObjectStore(\'stechuhr\', {
                keyPath: \'key\',
                autoIncrement: true
              });
            }
          };

          request.onsuccess = function(){
            var db = this.result;
            var trans = db.transaction([\'stechuhr\'], \'readonly\');
            var store = trans.objectStore(\'stechuhr\');

            var range = IDBKeyRange.lowerBound(0);
            var cursorRequest = store.openCursor(range);


            cursorRequest.onsuccess = function(evt){
              var result = evt.target.result;
              if(result){
                if(typeof result.value != \'undefined\' && typeof result.value.code != \'undefined\')
                {
                  var trans2 = db.transaction([\'stechuhr\'], \'readwrite\');
                  var store2 = trans2.objectStore(\'stechuhr\');            
                  var request2 = store2.delete(result.key);
                }
              }
            }
          }
        }
        ');
      }
      // try login and set user_login if login was successfull
      // wenn intern geht immer passwort???

      // MOTP

      $user_id="";

      $userip = $_SERVER['REMOTE_ADDR'];
      $ip_arr = explode('.',$userip);

      if($ip_arr[0]=="192" || $ip_arr[0]=="10" || $ip_arr[0]=="127" || $ip_arr[0]=="172")
        $localconnection = 1;
      else 
        $localconnection = 0;
    
      if($stechuhrdevicelogin && $rfidcode)
      {
        $userarr = $this->app->DB->SelectArr("SELECT * FROM `user` WHERE rfidtag = '$rfidcode' AND rfidtag <> '' AND activ = 1 LIMIT 1");
        if($userarr)
        {
          $userarr = reset($userarr);
          $user_id = $userarr['id'];
        }else{
          $user_id = '';
        }
        if($user_id)
        {
          $encrypted = $userarr['password'];
          $encrypted_md5 = $userarr['passwordmd5'];
          $fehllogins = $userarr['fehllogins'];

          $type = $userarr['type'];
          $externlogin = $userarr['externlogin'];
          $hwtoken = $userarr['hwtoken'];
          $stechuhruser = $userarr['stechuhrdevice'];
          $usesha512 = true;
          $salt = isset($userarr['salt'])?$userarr['salt']:'';
          $passwordsha512 = isset($userarr['passwordsha512'])?$userarr['passwordsha512']:'';
          if(!isset($userarr['passwordsha512']))
          {
            $usesha512 = false;
          }

          if($rfid == ''){
            if($stechuhrdevice == $stechuhruser)
            {
              setcookie('nonavigation',true);
            } elseif($stechuhruser == "") {
              $this->app->DB->Update("UPDATE `user` set stechuhrdevice = '$stechuhrdevice' where id = '$user_id' LIMIT 1");
              setcookie('nonavigation',true);
            } else {
              $user_id = "";
              setcookie('nonavigation',false);
            }
          }
        }
        
      }
      elseif($stechuhrdevicelogin && $stechuhrdevice)
      {
        $nr = substr($stechuhrdevice,0,6);
        if(is_numeric($nr) && strlen($stechuhrdevice) >= 6)
        {
          $userarr = $this->app->DB->SelectArr("SELECT * FROM `user` WHERE (username = '$nr' OR username = '$stechuhrdevice') and hwtoken = 4 AND activ = 1 LIMIT 1");
          if($userarr)
          {
            $userarr = reset($userarr);
            $user_id = $userarr['id'];
          }else $user_id = '';
          if($user_id)
          {
            $encrypted = $userarr['password'];
            $encrypted_md5 = $userarr['passwordmd5'];
            $fehllogins = $userarr['fehllogins'];

            $type = $userarr['type'];
            $externlogin = $userarr['externlogin'];
            $hwtoken = $userarr['hwtoken'];
            $stechuhruser = $userarr['stechuhrdevice'];
            $usesha512 = true;
            $salt = isset($userarr['salt'])?$userarr['salt']:'';
            $passwordsha512 = isset($userarr['passwordsha512'])?$userarr['passwordsha512']:'';
            if(!isset($userarr['passwordsha512']))
            {
              $usesha512 = false;
            }

            $stechuhruser = $this->app->DB->Select("SELECT stechuhrdevice FROM `user` WHERE id = '$user_id'");
            {
              if($stechuhrdevice == $stechuhruser)
              {
                setcookie('nonavigation',true);
              } elseif($stechuhruser == '') {
                $this->app->DB->Update("UPDATE `user` set stechuhrdevice = '$stechuhrdevice' where id = '$user_id' LIMIT 1");
                setcookie('nonavigation',true);
              } else {
                $user_id = '';
                setcookie('nonavigation',false);
              }
            }
          }
        }
      }
      elseif($hwtoken==5) //ldap
      {
        // verbinden zum ldap server
        $ds = ldap_connect($this->app->erp->Firmendaten("ldap_host"));
        $suche = $this->app->erp->Firmendaten("ldap_searchbase");
        $filter = $this->app->erp->Firmendaten("ldap_filter");
        //$bind_name = str_replace('%user%',$username,$this->app->erp->Firmendaten("ldap_bindname"));
        $bind_name = str_replace('{USER}',$username,$this->app->erp->Firmendaten("ldap_bindname"));

        if ($ds) {
        // binden zum ldap server
          $ldapbind = ldap_bind($ds, $bind_name, $password);
          if($filter!="")
          {
            // pruefe ob ein treffer in der Liste ist 
            $sr=ldap_search($ds,$suche, $filter);
            if(ldap_count_entries($ds,$sr) > 0)
              $user_id = $this->app->DB->Select("SELECT id FROM `user` WHERE username='".$username."' AND activ='1' LIMIT 1");
            else
              $user_id ='';
          } else {
            if($ldapbind)
              $user_id = $this->app->DB->Select("SELECT id FROM `user` WHERE username='".$username."' AND activ='1' LIMIT 1");
            else
              $user_id ='';
          }
        } else {
          $user_id ='';
        }
      } 

      //wawision otp 
      else if ($hwtoken==3)
      {
        setcookie('nonavigation',false);
        $wawi = new WaWisionOTP();
        $hwkey = $this->app->DB->Select("SELECT hwkey FROM `user` WHERE username='".$username."' AND activ='1' LIMIT 1");
        $hwcounter = $this->app->DB->Select("SELECT hwcounter FROM `user` WHERE username='".$username."' AND activ='1' LIMIT 1");
        $hwdatablock = $this->app->DB->Select("SELECT hwdatablock FROM `user` WHERE username='".$username."' AND activ='1' LIMIT 1");

        //$wawi->SetKey($hwkey);
        //$wawi->SetCounter($hwcounter);

        $serial =$hwdatablock;
        //$key = pack('V*', 0x01,0x02,0x03,0x04);
        $hwkey = trim(str_replace(' ','',$hwkey));
        $hwkey_array = explode(",",$hwkey);  
        $key = pack('V*', $hwkey_array[0], $hwkey_array[1], $hwkey_array[2], $hwkey_array[3]);
        $check = (int)$wawi->wawision_pad_verify($token,$key,$serial);

        // Fix fuer HW
        if($check >= 2147483647) $check = 0;

        if($encrypted_md5!="")
        {
          if ( $check > 0 && (md5($password) == $encrypted_md5 || md5($passwordunescaped) == $encrypted_md5)  && $fehllogins<8 && $check > $hwcounter)
          {
            $user_id = $this->app->DB->Select("SELECT id FROM `user`
                WHERE username='".$username."' AND activ='1' LIMIT 1");

            // Update counter
            $this->app->DB->Update("UPDATE `user` SET hwcounter='$check' WHERE id='$user_id' LIMIT 1");
            $this->app->erp->SystemLog("Xentral Login OTP Success User: $username Token: $token");

          } else {
            if($check===false)
            {
              $this->app->erp->SystemLog("Xentral Login OTP Falscher Key (Unkown Key) User: $username Token: $token");
            } else if ($check < $hwcounter && $check > 0)
            {
              $this->app->erp->SystemLog("Xentral Login OTP Counter Fehler (Replay Attacke) User: $username Token: $token");
            }
            $user_id = '';
          }
        } else {
          $user_id = '';
        }
      }

      else {
        setcookie('nonavigation',false);
        if(isset($passwordhash) && $passwordhash != '' && $usepasswordhash)
        {
          $checkunescaped = password_verify (  $passwordunescaped , $passwordhash );
          if(!$checkunescaped)
          {
            $checkescaped = password_verify (  $password , $passwordhash );
          }else {
            $checkescaped = false;
          }

          $passwordValid = $checkunescaped || $checkescaped;

          if($passwordValid){
            $this->app->erp->RunHook('login_password_check_otp', 3, $userdata['id'], $token, $passwordValid);
          }

          if($passwordValid)
          {
            $user_id = $this->app->DB->Select("SELECT id FROM `user`
                WHERE username='".$username."' AND activ='1' LIMIT 1");
            if($checkescaped && $user_id)
            {
              $options = array(
                'cost' => 12,
              );
              $passwordhash = @password_hash($passwordunescaped, PASSWORD_BCRYPT, $options);
              $this->app->DB->Update("UPDATE `user` SET passwordhash = '".$this->app->DB->real_escape_string($passwordhash)."',
                password='',passwordmd5='', salt = '', passwordsha512 = '' 
                WHERE id = '".$user_id."' LIMIT 1");
            }
          }else{
            $user_id = '';
          }
        }
        elseif(!empty($passwordsha512) && $usesha512) {
          if(hash('sha512',$passwordunescaped.$salt) === $passwordsha512 && $fehllogins<8) {
            $user_id = $this->app->DB->Select("SELECT id FROM `user`
                WHERE username='".$username."' AND activ='1' LIMIT 1");

            $passwordValid = true;
            $token = $this->app->Secure->GetPOST('token');
            $this->app->erp->RunHook('login_password_check_otp', 3, $user_id, $token, $passwordValid);
            if(!$passwordValid){
              $user_id = false;
            }
          }
          else {
            $user_id = '';
          }
        }
        elseif($encrypted_md5!='') {
          if ((md5($password ) == $encrypted_md5 || md5($passwordunescaped) == $encrypted_md5) && $fehllogins<8) {
            if(isset($this->app->Conf->WFdbType) && $this->app->Conf->WFdbType=="postgre"){
              $user_id = $this->app->DB->Select("SELECT id FROM \"user\"
                  WHERE username='".$username."' AND activ='1' LIMIT 1");
            }
            else {
              $user_id = $this->app->DB->Select("SELECT id FROM `user`
                  WHERE username='".$username."' AND activ='1' LIMIT 1");
            }
            if($user_id && $usesha512) {
              $salt = $this->app->DB->Select("SELECT salt FROM `user` WHERE id = '$user_id' LIMIT 1");
              $sha512 = $this->app->DB->Select("SELECT passwordsha512 FROM `user` WHERE id = '$user_id' LIMIT 1");
              if(empty($salt) && empty($sha512))
              {
                $salt = hash('sha512',microtime(true));
                $sha512 = hash('sha512',$passwordunescaped.$salt);
                $this->app->DB->Update("UPDATE `user` SET salt = '$salt', passwordsha512 = '$sha512' WHERE id = '$user_id' LIMIT 1");
              }
            }
          }
          else {
            $user_id = '';
          }
        } else {
          if (((crypt( $password,  $encrypted ) == $encrypted) || (crypt( $passwordunescaped,  $encrypted ) == $encrypted))  && $fehllogins<8)
          {
            if(isset($this->app->Conf->WFdbType) && $this->app->Conf->WFdbType=="postgre"){
              $user_id = $this->app->DB->Select("SELECT id FROM \"user\"
                  WHERE username='".$username."' AND activ='1' LIMIT 1");
            } else {
              $user_id = $this->app->DB->Select("SELECT id FROM `user`
                  WHERE username='".$username."' AND activ='1' LIMIT 1");

            }
            if($user_id && $usesha512)
            {
              $salt = $this->app->DB->Select("SELECT salt FROM `user` WHERE id = '$user_id' LIMIT 1");
              $sha512 = $this->app->DB->Select("SELECT passwordsha512 FROM `user` WHERE id = '$user_id' LIMIT 1");
              if(empty($salt) && empty($sha512))
              {
                $salt = hash('sha512',microtime(true));
                $sha512 = hash('sha512',$passwordunescaped.$salt);
                $this->app->DB->Update("UPDATE `user` SET salt = '$salt', passwordsha512 = '$sha512' WHERE id = '$user_id' LIMIT 1");
              }
            }
          }
          else {
            $user_id = '';
          }
        }
      }
      //$password = substr($password, 0, 8); //TODO !!! besseres verfahren!!

      //pruefen ob extern login erlaubt ist!!

      // wenn keine externerlogin erlaubt ist und verbindung extern
      if($externlogin==0 && $localconnection==0)
      {
        $this->app->Tpl->Set('LOGINERRORMSG',"Es ist kein externer Login mit diesem Account erlaubt.");
        $this->selectLanguageOptionByServerVariable();
        $this->app->Tpl->Parse('PAGE','login.tpl');
      }
      else if(is_numeric($user_id))
      {
        $this->app->DB->Delete("DELETE FROM useronline WHERE user_id='".$user_id."'");

        if (empty($this->session_id)) {
          throw new RuntimeException('Session ID can not be empty.');
        }

        $this->app->DB->Insert("INSERT INTO useronline (user_id, sessionid, ip, login, time)
            VALUES ('".$user_id."','".$this->session_id."','".$_SERVER['REMOTE_ADDR']."','1',NOW())");

        $this->app->DB->Select("UPDATE `user` SET fehllogins=0
            WHERE username='".$username."' LIMIT 1");
        $language = $this->app->Secure->GetPOST('language');
        $this->app->User->SetParameter('wawisionuebersetzung_sprache', $language);
        $this->app->erp->calledOnceAfterLogin($type);
        $this->app->User->createCache();
        if($adminadmin && !$this->app->DB->Select("SELECT id FROM `user` WHERE id = '$user_id' AND type = 'admin' LIMIT 1")) {
          $adminadmin = false;
        }
        //$module=$this->app->Secure->GetGET("module");
        //$action=$this->app->Secure->GetGET("action");
        //$id=$this->app->Secure->GetGET("id");
        if($adminadmin) {
          //$startseite = "index.php?module=welcome&action=settings&msg=".$this->app->erp->base64_url_encode('<div class="error">Bitte ändern Sie Ihr Passwort. Das Passwort entspricht noch dem Passwort der Installation!</div>');
          $startseite = 'index.php?module=welcome&action=start';
          $this->app->erp->Startseite($startseite);
          exit;
        }

        if($code && !$this->app->Secure->GetPOST('username')) {
          $result = $this->app->DB->SelectArr("SELECT url, reduziert FROM stechuhrdevice WHERE code = '$code' AND aktiv = 1 LIMIT 1");

          $startseite = $result[0]['url'] ;
          $isReduziert = $result[0]['reduziert'];

          if($isReduziert){
              $this->app->User->SetParameter('stechuhrdevicereduziert',true);
          }

          if($isReduziert && empty($startseite)){
            $startseite = 'index.php?module=stechuhr&action=list&prodcmd=arbeitsschritt';
          }

          if($startseite)
          {
            $this->app->erp->Startseite($startseite);
            exit;
          }
        }
        $ref = $_SERVER['HTTP_REFERER'];
        $refData = parse_url($ref);
        if($refData['query']!='' && !(strpos($ref, 'module=welcome') !== false && strpos($ref, 'action=login') !== false))
        {
          header('Location: index.php?'.$refData['query']);
          exit;
        }
        $this->app->erp->Startseite();
        exit;
      }
      else if ($fehllogins>=8)
      {
        $this->app->Tpl->Set('LOGINERRORMSG','Max. Anzahl an Fehllogins erreicht. Bitte wenden Sie sich an Ihren Administrator.');
        $this->selectLanguageOptionByServerVariable();
        $this->app->Tpl->Parse('PAGE','login.tpl');
      }
      else
      { 

        if(isset($this->app->Conf->WFdbType) && $this->app->Conf->WFdbType=="postgre")
          $this->app->DB->Select("UPDATE `user` SET fehllogins=fehllogins+1 WHERE username='".$username."'");
        else
          $this->app->DB->Select("UPDATE `user` SET fehllogins=fehllogins+1 WHERE username='".$username."' LIMIT 1");

        $this->app->Tpl->Set('LOGINERRORMSG','Benutzername oder Passwort falsch.');
        $this->selectLanguageOptionByServerVariable();
        $this->app->Tpl->Parse('PAGE','login.tpl');
      }
      //setcookie('DBSELECTED', '',-1);
    }
  }

  public function selectLanguageOptionByServerVariable(): void
  {
    $language = !isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])?'':strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));
    switch($language) {
      case 'DE':
        $this->app->Tpl->Set('OPTIONLANGUAGEGERMAN', ' selected="selected" ');
        break;
      case 'EN':
        $this->app->Tpl->Set('OPTIONLANGUAGEENGLISH', ' selected="selected" ');
        break;
    }
  }

  public function Logout($msg='',$logout=false)
  {
    setcookie('DBSELECTED','');
    if($logout)
      $this->app->Tpl->Parse('PAGE','sessiontimeout.tpl');

    $userid = (int)$this->app->User->GetID();
    if($userid)
    {
      $this->app->User->SetParameter('stechuhrdevicelogin', 0);
      $this->app->User->SetParameter('stechuhrdevicereduziert', false);
    }
    $this->app->DB->Delete("DELETE FROM `useronline` WHERE user_id='".$userid."'");
    $this->app->erp->RunHook('logout');
    @session_destroy();
    if($userid > 0)
    {
      @session_start();
      @session_regenerate_id(true);
      $_SESSION['database']='';
    }

    if(!$logout)
    {
      $server = $this->app->Location->getServer();
      if(!empty($server))
      {
        header('Location: '.$server);
      }else{
        header('Location: ' . $this->app->http . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['REQUEST_URI']), '/'));
      }
      exit;
    }
  }


  public function CreateAclDB()
  {

  }

  protected function mOTP($pin,$otp,$initsecret)
  {
    $maxperiod = 3*60; // in seconds = +/- 3 minutes
    $time=$this->app->DB->Select('SELECT UNIX_TIMESTAMP()');//gmdate('U');
    for($i = $time - $maxperiod; $i <= $time + $maxperiod; $i++) {
      $md5 = substr(md5(substr($i,0,-1).$initsecret.$pin),0,6);

      if($otp == $md5) {
        return true;
      }
    }
    return false;
  }

  /**
   * @return bool
   */
  private function IsInLoginLockMode()
  {

    if($this->app->erp->GetKonfiguration('login_lock_mode') === '1'){
      $timeMaintenance = (int)$this->app->erp->GetKonfiguration('login_lock_mode_time');

      if(empty($timeMaintenance)){
        $this->app->erp->SetKonfigurationValue('login_lock_mode_time', time());
        return true;
      }

      $timeOutMaintenance = (int)$this->app->erp->GetKonfiguration('login_lock_mode_timeout');
      // default 10min
      $timeOut = empty($timeOutMaintenance) ? 600 : $timeOutMaintenance;

      if(time() - $timeMaintenance < $timeOut){
        return true;
      }

      $this->app->erp->SetKonfigurationValue('login_lock_mode', 0);
      $this->app->erp->SetKonfigurationValue('login_lock_mode_time', 0);
      $this->app->erp->SetKonfigurationValue('login_lock_mode_timeout', 0);
    }
    return false;

  }

}
