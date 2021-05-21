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
use Xentral\Modules\RoleSurvey\SurveyGateway;
use Xentral\Modules\RoleSurvey\SurveyService;

use Xentral\Components\Database\Exception\QueryFailureException;

class Benutzer
{
  function __construct($app, $intern = false)
  {
    $this->app=$app;
    if($intern)return;

    $this->app->erp->inline['german']['benutzer']['default']['weitereyoutube'][] = array('titel'=>'Zwei-Faktor-Authentifizierung mit mOTP','youtube'=>'QfNbDsEQB9M');

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","UserCreate");
    $this->app->ActionHandler("delete","UserDelete");
    $this->app->ActionHandler("edit","UserEdit");
    $this->app->ActionHandler("history","UserHistory");
    $this->app->ActionHandler("list","UserList");
    $this->app->ActionHandler("chrights","UserChangeRights");
    $this->app->ActionHandler("download","UserDownload");


    $this->app->DefaultActionHandler("list");

    //$this->Templates = $this->GetTemplates();

    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
    try {
      /** @var SurveyService $surveyService */
      $surveyService = $this->app->Container->get('SurveyService');
      $surveyService->create('user_create', 'benutzer', 'list', false, false);
    }
    catch (Exception $e) {

    }
    $this->app->erp->RegisterHook('welcome_surveysave', 'benutzer', 'UserWelcomeSurveySave');
  }

  /**
   * @param int   $surveyId
   * @param int   $surveyUserId
   * @param array $resonse
   */
  public function UserWelcomeSurveySave($surveyId, $surveyUserId, &$response)
  {
    /** @var SurveyGateway $surveyGateway */
    $surveyGateway = $this->app->Container->get('SurveyGateway');
    $survey = $surveyGateway->getById($surveyId);
    if(empty($survey) || $survey['name'] !== 'user_create') {
      return;
    }
    $dataRow = $surveyGateway->getFilledById($surveyUserId);
    $data = json_decode($dataRow['data'], true);
    if(!empty($data['name'])) {
      foreach($data['name'] as $key => $name) {
        if(empty($name)) {
          continue;
        }

        //@todo Benutzer anlegen
      }
    }
    /** @var SurveyService $surveyService */
    $surveyService = $this->app->Container->get('SurveyService');
    $surveyService->clearUserData($surveyId, $this->app->User->GetID());
    $response['url'] = 'index.php?module=benutzer&action=list';
  }

  function UserDownload()
  {
    $id = $this->app->Secure->GetGET("id");
    if($id > 0)
    {
      $result = $this->app->DB->SelectArr("SELECT module,action FROM userrights WHERE `user`='$id'");

      $tmp['bezeichnung']=$this->app->DB->Select("SELECT username FROM `user` WHERE id='$id' LIMIT 1");
      $tmp['beschreibung']=$this->app->DB->Select("SELECT description FROM `user` WHERE id='$id' LIMIT 1");
      $tmp['rechte']=$result;

      
      header('Content-Type: application/json');
      header('Content-disposition: attachment; filename="'.$tmp['bezeichnung'].'.json"');
      echo json_encode($tmp);
      exit;
    }
  }

  function UserList()
  {
    //		$this->app->Tpl->Add(KURZUEBERSCHRIFT,"Benutzer");
    $this->app->erp->MenuEintrag("index.php?module=benutzer&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=benutzer&action=history","Historie");
    $this->app->erp->MenuEintrag("index.php?module=benutzer&action=create","Neuen Benutzer anlegen");
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $this->app->YUI->TableSearch('USER_TABLE',"userlist");
    $this->app->Tpl->Parse('PAGE', "benutzer_list.tpl");

  }

  /**
   * @param int $userId
   *
   * @return bool
   */
  public function isUserLastAdmin(int $userId): bool
  {
    return $this->isUserAdmin($userId) &&
      (int)$this->app->DB->Select(
        "SELECT COUNT(`id`) FROM `user` WHERE `type` = 'admin' AND `activ` = 1 AND `id` <> {$userId}"
      ) === 0;
  }

  public function isUserAdmin(int $userId): bool
  {
    return $this->app->DB->Select("SELECT COUNT(`id`) FROM `user` WHERE `type` = 'admin' AND `id` = {$userId}") > 0;
  }

  public function UserDelete(): void
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $isOwnAccount = $id === (int)$this->app->User->GetId();
    if($isOwnAccount) {
      $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">{|Du kannst deinen eigenen Account nicht löschen.|}</div>");
    } else{
      $username = $this->app->DB->Select("SELECT `username` FROM `user` WHERE `id` = '{$id}'");
      if(!$this->isUserLastAdmin($id)){
        $this->app->DB->Delete("DELETE FROM `user` WHERE `id` = '{$id}'");
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Benutzer \"$username\" wurde gel&ouml;scht.</div>");
      }else{
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der einzige aktive Admin \"$username\" kann nicht gel&ouml;scht werden.</div>");
      }
    }

    $this->UserList();
  }


  function UserCreate()
  {
    //		$this->app->Tpl->Add(KURZUEBERSCHRIFT,"Benutzer");
    $this->app->erp->MenuEintrag("index.php?module=benutzer&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $input = $this->GetInput();
    $submit = $this->app->Secure->GetPOST('submituser');

    $error = '';
    $maxlightuser = 0;

    if($submit!='') {


      if($input['username']=='' && $this->app->Secure->GetPOST('hwtoken') != 4) $error .= 'Geben Sie bitte einen Benutzernamen ein.<br>';		
      if($input['password']=='' && $this->app->Secure->GetPOST('hwtoken') != 4 && $this->app->Secure->GetPOST('hwtoken') != 5) $error .= 'Geben Sie bitte ein Passwort ein.<br>';		
      if($input['repassword']=='' && $this->app->Secure->GetPOST('hwtoken') != 4 && $this->app->Secure->GetPOST('hwtoken') != 5 ) $error .= 'Wiederholen Sie bitte Ihr Passwort.<br>';		
      if($input['password'] != $input['repassword']) $error .= 'Die eingegebenen Passw&ouml;rter stimmen nicht &uuml;berein.<br>';
      if($this->app->DB->Select("SELECT '1' FROM `user` WHERE username='{$input['username']}' LIMIT 1")=='1')
        $error .= "Es existiert bereits ein Benutzer mit diesem Namen";

      $input['adresse'] = $this->app->erp->ReplaceAdresse($input['adresse'],$input['adresse'],1);
      $input['projekt'] = $this->app->erp->ReplaceProjekt($input['projekt'],$input['projekt'],1);

      if($input['adresse'] <=0)
        $error .= 'Geben Sie bitte eine g&uuml;ltige Adresse aus den Stammdaten an.<br>';

      if($error!=='')
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
      else {
        if($input['hwtoken'] == 4 && $input['type'] == 'admin')
        {
          $input['type'] = 'standard';
          $input['startseite'] = 'index.php?module=stechuhr&action=list';         
        }
        $input['passwordunenescaped'] = $_POST['password'];
        $id = $this->app->erp->CreateBenutzer($input);

        //$this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Der Benutzer wurde erfolgreich angelegt</div>");
        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Der Benutzer wurde erfolgreich angelegt.</div>");
        header("Location: index.php?module=benutzer&action=edit&id=$id&msg=$msg");
        exit;
      }
    }

    $this->SetInput($input);


    $this->app->YUI->ColorPicker('defaultcolor');

    $this->app->Tpl->Set('ACTIVCHECKED',"checked");
    $this->app->Tpl->Set('VORRECHTE',"<!--");
    $this->app->Tpl->Set('NACHRECHTE',"-->");
    $extra = '
    if($(\'#hwtoken\').val() == \'4\' || $(\'#hwtoken\').val() == \'5\')
    {
      message = \'\';
    }
    ';
    $this->app->YUI->PasswordCheck('password', 'repassword', 'username', 'submit', $extra);
    $this->app->Tpl->Parse('PAGE', "benutzer_create.tpl");
  }

  function UserHistory(){
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->MenuEintrag("index.php?module=benutzer&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=benutzer&action=history","History");

    $this->app->YUI->TableSearch('USER_TABLE',"permissionhistory");
    $this->app->Tpl->Parse('PAGE', "benutzer_list.tpl");
  }

  function UserEdit()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->Tpl->Set('ID', $id);
    $defaultcolor = $this->app->Secure->GetPOST('defaultcolor');
    if($defaultcolor === 'transparent') $defaultcolor = '';

    // convert value to user DB
    if($this->app->User->GetParameter('welcome_defaultcolor_fuer_kalender')!=''){

      $this->app->DB->Update("UPDATE user SET defaultcolor='$defaultcolor' WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      $this->app->User->SetParameter('welcome_defaultcolor_fuer_kalender',"");
    }

    
    if($this->app->Secure->GetGET('cmd') == 'qrruecksetzen' && $id)
    {
      $this->app->DB->Update("UPDATE `user` set stechuhrdevice = '' WHERE id = '$id' LIMIT 1");
      echo json_encode(array('status'=>1));
      exit;
    }
    
    if($this->app->Secure->GetGET('cmd') == 'getrfid' && $id)
    {
      $rfid = '';
      $swhere = '';
      $seriennummer = $this->app->Secure->GetPOST('seriennummer');
      if($seriennummer != '')$swhere = " AND seriennummer = '$seriennummer' ";
      $deviceiddest = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE verwendenals = 'metratecrfid' $swhere LIMIT 1");
      if($deviceiddest)
      {
        $rfid = trim($this->app->erp->GetAdapterboxAPIRFID($deviceiddest));
        if($rfid == 'no answer from device (not timeout)')$rfid = '';
        if($rfid)
        {
          $rfida = explode(';',$rfid);
          if(!empty($rfida[1]))$rfid = $rfida[1];
        }
        if($this->app->DB->Select("SELECT id FROM `user` WHERE rfidtag = '".$this->app->DB->real_escape_string($rfid)."' AND id <> '$id' LIMIT 1"))$rfid = '';
      }
      if($rfid == "0")$rfid = '';
      echo json_encode(array('rfid'=>$rfid));
      exit;
    }
    $jsonvorlage = $_FILES['jsonvorlage']['tmp_name'];
    if($jsonvorlage!="")
    {
        $content = file_get_contents($jsonvorlage);
        $tmp = json_decode($content);
        $neuerechte=0;

        $anzahl = count($tmp->{'rechte'});
        for($i=0;$i<=$anzahl;$i++)
        {
          //echo " $i M ".$tmp->{'rechte'}[$i]->{'module'}." A ".$tmp->{'rechte'}[$i]->{'action'};
          $tmpmodule  = $this->app->DB->real_escape_string($tmp->{'rechte'}[$i]->{'module'});
          $tmpaction = $this->app->DB->real_escape_string($tmp->{'rechte'}[$i]->{'action'});

          if($tmpmodule!="" && $tmpaction!="")
          {
            $check = $this->app->DB->Select("SELECT id FROM userrights WHERE module='".$tmpmodule."' AND action='".$tmpaction."' AND user='".$id."' LIMIT 1");

            if($check > 0)
              $this->app->DB->Update("UPDATE userrights SET permission=1 WHERE module='".$tmpmodule."' AND action='".$tmpaction."' AND user='".$id."' LIMIT 1");
            else {
              $neuerechte++;
              $this->app->DB->Insert("INSERT INTO userrights (id,module,action,user,permission) VALUES ('','".$tmpmodule."','".$tmpaction."','$id','1')");
            }
            $this->permissionLog($this->app->User->GetID(),$id,$tmpmodule,$tmpaction,1);
          }
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Es wurden $neuerechte neue Rechte dem Benutzer hinzugefügt!</div>");
        header("Location: index.php?module=benutzer&action=edit&id=$id&msg=$msg");
        exit;
    }
    
    $this->app->erp->MenuEintrag("index.php?module=benutzer&action=edit&id=$id","Details");
    $username = $this->app->DB->Select("SELECT username FROM `user` WHERE id='$id'");
    //		$this->app->Tpl->Add(KURZUEBERSCHRIFT2,$username);

    $this->app->erp->MenuEintrag("index.php?module=benutzer&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $id = $this->app->Secure->GetGET('id');
    $input = $this->GetInput();

    if($input['hwtoken'] == 'totp'){
      $input['hwtoken'] = '0';
    }else if($input['hwtoken'] != ''){
      /** @var \Xentral\Modules\TOTPLogin\TOTPLoginService $tokenManager */
      $tokenManager = $this->app->Container->get('TOTPLoginService');
      $tokenManager->disableTotp($id);
    }

    $submit = $this->app->Secure->GetPOST('submituser');
    $benutzer = $this->app->DB->Select("SELECT description FROM `user` WHERE id='$id' LIMIT 1");
    $name_angezeigt = $this->app->DB->Select("SELECT adresse FROM `user` WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$name_angezeigt' LIMIT 1");
    if($benutzer!="")$tmp = "(".$benutzer.")";
    $this->app->Tpl->Add('KURZUEBERSCHRIFT2',$name." ".$tmp);


    if(is_numeric($id) && $submit!='') {
      $isUserLastAdmin = $this->isUserLastAdmin((int)$id);
      $error = '';
      if($input['username']=='') $error .= 'Geben Sie bitte einen Benutzernamen ein.<br>';
      if($input['password'] != $input['repassword'] && $input['hwtoken']!=5) $error .= 'Die eingegebenen Passw&ouml;rter stimmen nicht &uuml;berein.<br>';

      $input['adresse'] = $this->app->erp->ReplaceAdresse(1,$input['adresse'],1);
      if($input['adresse'] <=0)
        $error .= 'Geben Sie bitte eine g&uuml;ltige Adresse aus den Stammdaten an.<br>';

      $input['projekt'] = $this->app->erp->ReplaceProjekt(1,$input['projekt'],1);
      $isOwnAccount = $id == $this->app->User->GetId();
      if($isOwnAccount && empty($input['activ'])) {
        $error .= '{|Du kannst deinen eigenen Account nicht deaktivieren.|}<br>';
      } elseif($isOwnAccount && $this->isUserAdmin((int)$id) && $input['type'] !== 'admin') {
        $error .= '{|Du kannst deinen eigenen Account nicht in einem Benutzer umwandeln.|}<br>';
      } elseif($isUserLastAdmin && empty($input['activ'])) {
        $error .= '{|Der letzte Administrator kann nicht deaktiviert werden.|}<br>';
      } elseif($isUserLastAdmin && $input['type'] !== 'admin') {
        $error .= '{|Der letzte Administrator kann nicht in einem Benutzer umgewandelt werden.|}<br>';
      }
      if($error!='')
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
      else {
        //$settings = base64_encode(serialize($input['settings']));
        $firma = $this->app->User->GetFirma();

        if($input['gpsstechuhr']!="1")
        {
          $check = $this->app->DB->Delete("DELETE FROM gpsstechuhr 
              WHERE `user`='".$id."'
              AND DATE_FORMAT(zeit,'%Y-%m-%d')= DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) LIMIT 1");
        }
        
        if(($input['hwtoken'] == 4) && $input['type'] == 'admin')
        {
          $anzaktivadmin = $this->app->DB->Select("SELECT count(*) from `user` where activ=1 and type = 'admin' and id <> '$id'");
          if($anzaktivadmin < 1)
          {
            $error = 'Sie k&ouml;nnen den einzigen Administrator als Stechuhruer einbinden. Legen Sie daf&uuml;r einen neuen User an';
            $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
          } else {
            $input['type'] = 'standard';
            $input['startseite'] = 'index.php?module=stechuhr&action=list';
          }
          
        }
        if($error == "")
        {
          if($input['hwtoken'] == 4)
          {
            $stechuhrdevice = $this->app->DB->Select("SELECT stechuhrdevice from `user` where id = '$id'");
            if(substr($input['username'], 0,6) !== substr($stechuhrdevice,0,6))
            {
              $this->app->DB->Update("UPDATE `user` set stechuhrdevice = '' where id = '$id'");
            }
          }

          $spracheBevorzugen = $this->getCurrentDefaultLanguage($input['sprachebevorzugen']);
          
          $this->app->DB->Update(
            sprintf(
              "UPDATE `user` 
            SET username='%s', 
                description='%s',
              activ='%d',
                type='%s', 
                adresse='%d', 
                vorlage='%s',
              gpsstechuhr='%d',
              rfidtag='%s',
              kalender_aktiv='%d',
              kalender_ausblenden='%d',
              projekt='%d',
              projekt_bevorzugen='%d',
              sprachebevorzugen='%s',
              email_bevorzugen='%d',
              fehllogins='%d', 
                standarddrucker='%d',
                standardetikett='%d',
              standardversanddrucker='%d',
              paketmarkendrucker='%d',
              standardfax='%d',
              defaultcolor='%s',
              startseite='%s', 
                hwtoken='%d', 
                hwkey='%s', 
              hwcounter='%d', 
                hwdatablock='%s', 
                motppin='%s',
              motpsecret='%s', 
                externlogin='%d', 
                firma='%d',
              kalender_passwort='%s', 
              docscan_aktiv='%d', 
              docscan_passwort='%s',
                `role` = '%s'
              WHERE id=%d 
              LIMIT 1",
              $input['username'],
              $input['description'],
              $input['activ'],
              $input['type'],
              $input['adresse'],
              $input['vorlage'],
              $input['gpsstechuhr'],
              $input['rfidtag'],
              $input['kalender_aktiv'],
              $input['kalender_ausblenden'],
              $input['projekt'],
              $input['projekt_bevorzugen'],
              $spracheBevorzugen,
              $input['email_bevorzugen'],
              $input['fehllogins'],
              $input['standarddrucker'],
              $input['standardetikett'],
              $input['standardversanddrucker'],
              $input['paketmarkendrucker'],
              $input['standardfax'],
              $input['defaultcolor'],
              $input['startseite'],
              $input['hwtoken'],
              $input['hwkey'],
              $input['hwcounter'],
              $input['hwdatablock'],
              $input['motppin'],
              $input['motpsecret'],
              $input['externlogin'],
              $firma,
              $input['kalender_passwort'],
              $input['docscan_aktiv'],
              $input['docscan_passwort'],
              $input['role'],
              $id
            )
          );

          if($input['password']!='' && $input['password']!='***************') {
            $this->app->DB->Select("SELECT passwordhash FROM `user` WHERE id = '$id' LIMIT 1");
            if(!$this->app->DB->error()){
              $options = array(
                'cost' => 12,
              );
              $passwordhash = @password_hash($input['passwordunescaped'], PASSWORD_BCRYPT, $options);
              if($passwordhash != '')
              {
                $this->app->DB->Update("UPDATE `user` SET passwordhash = '".$this->app->DB->real_escape_string($passwordhash)."',
                password='',passwordmd5='', salt = '', passwordsha512 = '' 
                WHERE id = '".$id."' LIMIT 1");
              }
            }
            else{
              $salt = $this->app->DB->Select("SELECT salt FROM `user` WHERE id = '$id' LIMIT 1");
              if(!$this->app->DB->error()){
                if(empty($salt)) $salt = hash('sha512', microtime(true));
                $passwordsha512 = hash('sha512', $_POST['password'] . $salt);
                $this->app->DB->Update("UPDATE `user` SET password='',passwordmd5='', salt = '$salt', passwordsha512 = '$passwordsha512' WHERE id='$id' LIMIT 1");
                if($salt == "" || $passwordsha512 == "") {
                  $this->app->DB->Update("UPDATE `user` SET `password` = '', `passwordmd5` = MD5('{$input['password']}') WHERE `id` = '$id' LIMIT 1");
                } //TODO rausnehmen
              }
              else{
                $this->app->DB->Update("UPDATE `user` SET `password` = '', `passwordmd5` = MD5('{$input['password']}') WHERE `id` = '$id' LIMIT 1");
              }
            }
          }

          $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");

          $this->app->erp->AbgleichBenutzerVorlagen($id);
        }
      }	
    }



    $data = $this->app->DB->SelectArr("SELECT * FROM `user` WHERE id='$id' LIMIT 1");
    if($data)
    {
      
      if($data[0]['stechuhrdevice'] != '')$this->app->Tpl->Set('BUTTONQRRESET', '<input type="button" value="Code zur&uuml;cksetzen" onclick="qrruecksetzen();" />');
    }
    if(is_array($data[0])) {
      $data[0]['password'] = '***************';
      $data[0]['repassword'] = '***************';
      //			$data[0]['motpsecret']	= $this->app->DB->Select("SELECT DECRYPT('{$input[0]['motpsecret']}')");
      //			$data[0]['hwkey']	= $this->app->DB->Select("SELECT DECRYPT('{$input[0]['hwkey']}')");
      //$data[0]['settings'] = unserialize(base64_decode($data[0]['settings']));
    }

    if($data[0]['type']=="admin"){
      $this->app->Tpl->Set('HINWEISADMIN',"<div class=\"info\">Dieser Benutzer ist vom Typ Administrator. Administratoren haben immer Vollzugriff - daher können diesem keine Rechte genommen werden.</div>");
    } else {
      $this->app->Tpl->Add("HINWEISADMIN","<br><i>Hinweis: Blau = erlaubt, Grau = gesperrt</i>");
    }
    $this->SetInput($data[0]);
    $this->UserRights();
      
                       
    $rfids = $this->app->DB->SelectArr("SELECT seriennummer,bezeichnung FROM adapterbox WHERE verwendenals = 'metratecrfid'");
    if($rfids)
    {
      foreach($rfids as $v)
      {
        $this->app->Tpl->Add('SELRFID','<option value="'.$v['seriennummer'].'">'.$v['bezeichnung'].'</option>');
      }
    }
    //                   
    $this->app->YUI->ColorPicker('defaultcolor');

    $extra = '
    if($(\'#hwtoken\').val() == \'4\' || $(\'#hwtoken\').val() == \'5\')
    {
      message = \'\';
    }
    ';
    $this->app->YUI->PasswordCheck('password', 'repassword', 'username', 'submit', $extra);
    $roles = $this->getRoleOptions();
    $hasSelection = false;
    foreach($roles as $roleKey => $roleValue) {
      $selected = $roleKey === $data[0]['role']?' selected="selected"':'';
      if($selected !== '') {
        $hasSelection = true;
      }
      if(!$hasSelection && $roleKey === 'Sonstiges') {
        $selected = ' selected="selected"';
      }
      $this->app->Tpl->Add(
        'SELROLE',
        sprintf(
          '<option value="%s"%s>%s</option>',
          $roleKey, $selected, $roleValue
        )
      );
    }
    $this->app->Tpl->Set('ROLETEXT', $data[0]['role']);
    $this->app->Tpl->Set('ROLE', $data[0]['role']);
    $this->app->Tpl->Parse('PAGE', "benutzer_create.tpl");
  }


  /**
   * @return string[]
   */
  public function getRoleOptions(): array
  {
    return [
      'Buchhaltung' => 'Buchhaltung',
      'Vertrieb' => 'Vertrieb',
      'Einkauf / Produktion' => 'Einkauf / Produktion',
      'Logistik' => 'Logistik',
      'HR / Personalmanagement' => 'HR / Personalmanagement',
      'Office' => 'Office',
      'Marketing' => 'Marketing',
      'Administration / IT' => 'Administration / IT',
      'Management' => 'Management',
      'Sonstiges' => 'Sonstiges',
    ];
  }

  /**
   * @return array
   */
  public function GetInput(): array
  {
    // username is an array with multiple (hidden) fields, so filter the first filled one.
    $usernames = (array) $this->app->Secure->GetPOST('username');
    $usernames = array_filter($usernames);
    // make sure, at least one (empty) string is present in this array.
    $usernames[] = '';
    // reset all indexes.
    $usernames = array_values($usernames);
    $username = $usernames[0];

    $input = array();
    $input['description'] = $this->app->Secure->GetPOST('description');
    $input['type'] = $this->app->Secure->GetPOST('type');
    $input['username'] = $username;
    $input['vorlage'] = $this->app->Secure->GetPOST('vorlage');
    $input['adresse'] = $this->app->Secure->GetPOST('adresse');
    $input['externlogin'] = $this->app->Secure->GetPOST('externlogin');
    $input['activ'] = $this->app->Secure->GetPOST('activ');
    $input['gpsstechuhr'] = $this->app->Secure->GetPOST('gpsstechuhr');
    $input['rfidtag'] = $this->app->Secure->GetPOST('rfidtag');
    $input['kalender_aktiv'] = $this->app->Secure->GetPOST('kalender_aktiv');
    $input['kalender_ausblenden'] = $this->app->Secure->GetPOST('kalender_ausblenden');
    $input['projekt'] = $this->app->Secure->GetPOST('projekt');
    $input['projekt_bevorzugen'] = $this->app->Secure->GetPOST('projekt_bevorzugen');
    $input['email_bevorzugen'] = $this->app->Secure->GetPOST('email_bevorzugen');
    $input['startseite'] = $this->app->Secure->GetPOST('startseite');
    $input['defaultcolor'] = $this->app->Secure->GetPOST('defaultcolor');
    if($input['defaultcolor'] === 'transparent') $input['defaultcolor'] = '';
    $input['fehllogins'] = $this->app->Secure->GetPOST('fehllogins');
    $input['password'] = $this->app->Secure->GetPOST('password');
    $input['repassword'] = $this->app->Secure->GetPOST('repassword');
    $input['passwordunescaped'] = $this->app->Secure->GetPOST('password',"","","noescape");
    $input['hwtoken'] = $this->app->Secure->GetPOST('hwtoken');
    $input['motppin'] = $this->app->Secure->GetPOST('motppin');
    $input['motpsecret'] = $this->app->Secure->GetPOST('motpsecret');
    $input['hwkey'] = $this->app->Secure->GetPOST('hwkey');
    $input['hwcounter'] = $this->app->Secure->GetPOST('hwcounter');
    $input['hwdatablock'] = $this->app->Secure->GetPOST('hwdatablock');
    $input['standarddrucker'] = $this->app->Secure->GetPOST('standarddrucker');
    $input['standardversanddrucker'] = $this->app->Secure->GetPOST('standardversanddrucker');
    $input['paketmarkendrucker'] = $this->app->Secure->GetPOST('paketmarkendrucker');
    $input['standardetikett'] = $this->app->Secure->GetPOST('standardetikett');
    $input['standardfax'] = $this->app->Secure->GetPOST('standardfax');
    $input['sprachebevorzugen'] = $this->app->Secure->GetPOST('sprachebevorzugen');
    $input['role'] = $this->app->Secure->GetPOST('role');

    //$input['settings'] = $this->app->Secure->GetPOST('settings');
    $input['kalender_passwort'] = $this->app->Secure->GetPOST('kalender_passwort');
    $input['docscan_aktiv'] = $this->app->Secure->GetPOST('docscan_aktiv');
    $input['docscan_passwort'] = $this->app->Secure->GetPOST('docscan_passwort');
    return $input;
  }

  function SetInput($input)
  {
    $this->app->Tpl->Set('DESCRIPTION', $input['description']);
    $this->app->Tpl->Set('TYPESELECT', $this->TypeSelect($input['type']));
    $this->app->Tpl->Set('USERNAME', $input['username']);
    $this->app->Tpl->Set('VORLAGE', $input['vorlage']);
    $this->app->Tpl->Set('ADRESSE', $this->app->erp->ReplaceAdresse(0,$input['adresse'],0));
    $this->app->Tpl->Set('PROJEKT', $this->app->erp->ReplaceProjekt(0,$input['projekt'],0));
    $this->app->Tpl->Set('RFIDTAG', $input['rfidtag']);

    $this->app->YUI->AutoComplete("adresse","adresse");
    $this->app->YUI->AutoComplete("vorlage","uservorlage");
    $this->app->YUI->AutoComplete("projekt","projektname",1);

    if($input['externlogin']=='1') $this->app->Tpl->Set('EXTERNLOGINCHECKED', 'checked');
    if($input['activ']=='1') $this->app->Tpl->Set('ACTIVCHECKED', 'checked');
    if($input['gpsstechuhr']=='1') $this->app->Tpl->Set('GPSSTECHUHRCHECKED', 'checked');
    if($input['kalender_aktiv']=='1') $this->app->Tpl->Set('KALENDERAKTIVCHECKED', 'checked');
    if($input['kalender_ausblenden']=='1') $this->app->Tpl->Set('KALENDERAUSBLENDENCHECKED', 'checked');
    if($input['projekt_bevorzugen']=='1') $this->app->Tpl->Set('PROJEKTBEVORZUGENCHECKED', 'checked');
    if($input['email_bevorzugen']=='1') $this->app->Tpl->Set('EMAILBEVORZUGENCHECKED', 'checked');
    if($input['docscan_aktiv']=='1') $this->app->Tpl->Set('DOCSCANAKTIVCHECKED', 'checked');

    $this->app->Tpl->Set('STARTSEITE', $input['startseite']);
    $this->app->Tpl->Set('DEFAULTCOLOR', $input['defaultcolor']);
    $this->app->Tpl->Set('SPRACHEBEVORZUGEN',$this->languageSelectOptions($input['sprachebevorzugen']));
    $this->app->Tpl->Set('FEHLLOGINS', $input['fehllogins']);
    $this->app->Tpl->Set('PASSWORD', $input['password']);
    $this->app->Tpl->Set('REPASSWORD', $input['repassword']);
    $this->app->Tpl->Set('TOKENSELECT', $this->TokenSelect($input['hwtoken']));
    $this->app->Tpl->Set('MOTPPIN', $input['motppin']);
    $this->app->Tpl->Set('MOTPSECRET', $input['motpsecret']);
    $this->app->Tpl->Set('HWKEY', $input['hwkey']);
    $this->app->Tpl->Set('HWCOUNTER', $input['hwcounter']);
    $this->app->Tpl->Set('HWDATABLOCK', $input['hwdatablock']);
    $this->app->Tpl->Set('STANDARDDRUCKER', $this->app->erp->GetSelectDrucker($input['standarddrucker']));
    $this->app->Tpl->Set('STANDARDVERSANDDRUCKER', $this->app->erp->GetSelectVersanddrucker($input['standardversanddrucker']));
    $this->app->Tpl->Set('PAKETMARKENDRUCKER', $this->app->erp->GetSelectVersanddrucker($input['paketmarkendrucker']));
    $this->app->Tpl->Set('STANDARDETIKETT', $this->app->erp->GetSelectEtikettenDrucker($input['standardetikett']));
    $this->app->Tpl->Set('STANDARDFAX', $this->app->erp->GetSelectFax($input['standardfax']));
    //$this->app->Tpl->Set('SETTINGS', $input['settings']);
    $this->app->Tpl->Set('SERVERNAME', $this->app->erp->UrlOrigin($_SERVER));
    $this->app->Tpl->Set('KALENDERPASSWORT', $input['kalender_passwort']);
    $this->app->Tpl->Set('DOCSCANPASSWORT', $input['docscan_passwort']);
    $this->app->Tpl->Set('ROLE', $input['role']);
    $this->app->Tpl->Set('ROLETEXT', $input['role']);
  }

  function TypeSelect($select='admin')
  {
    $data = array('standard'=>'Benutzer','admin'=>'Administrator');
    //, 'verwaltung'=>'Verwaltung', 'vollzugriff'=>'Vollzugriff', 'mitarbeiter'=>'Mitarbeiter', 'produktion'=>'Produktion');

    $out = "";
    foreach($data as $key=>$value) {
      $selected = (($select==$key) ? 'selected' : '');
      $out .= "<option value=\"$key\" $selected>$value</option>";
    }
    return $out;
  }

  private function getCurrentDefaultLanguage($fromPost){

    if(empty($fromPost)){
      $fromPost = $this->app->erp->Firmendaten('preferredLanguage');

      if(empty($fromPost)){
        $fromPost = 'deutsch';
      }
    }
    return $fromPost;
  }

  /**
   * Liefert einen String aus HTML-Optionen zurück
   * @param string $fromPost
   * @return string
   */
  private function languageSelectOptions($fromPost=''){

    $select = $this->getCurrentDefaultLanguage($fromPost);

    $out = "";
    $sprachen = $this->getLanguages();

    foreach($sprachen as $sprache) {
      $selected = (($select==$sprache) ? 'selected' : '');
      $out .= "<option value=\"$sprache\" $selected>$sprache</option>";
    }
    return $out;
  }

  /**
   * Liefert einen Array aus Strings zurück. Immer mindestens 'deutsch' enthalten
   * @return array
   */
  private function getLanguages(){

    $sprachen[] = 'deutsch';
    $folder = __DIR__ .'/../../languages';
    if(is_dir($folder))
    {
      $handle = opendir($folder);
      if($handle){
        while($file = readdir($handle))
        {
          if($file[0] !== '.')
          {
            if(is_dir($folder.'/'.$file) && (file_exists($folder.'/'.$file.'/variablen.php')|| file_exists($folder.'/'.$file.'/variablen_custom.php')))
            {
              if($file == 'german')$file = 'deutsch';
              if(!in_array($file, $sprachen))$sprachen[] = $file;
            }
          }
        }
        closedir($handle);
      }
    }
    return $sprachen;
  }

  /**
   * @param string $select
   *
   * @return string
   */
  public function TokenSelect($select='0')
  {
    //$data = array('0'=>'Benutzername + Passwort', '1'=>'Benutzername + Passwort + mOTP', '2'=>'Benutzername + Passwort + Picosafe Login','3'=>'WaWision OTP + Passwort');
    $data = array('0'=>'Benutzername + Passwort', 
        '3'=>'WaWision LoginKey + Benutzername + Passwort',
        '5'=>'LDAP Verzeichnis'
        );

    /** @var \Xentral\Modules\TOTPLogin\TOTPLoginService $tokenManager */
    $tokenManager = $this->app->Container->get('TOTPLoginService');
    $user = $this->app->Secure->GetGET('id');
    try {
      if($user != null && $user != '' && $tokenManager->isTOTPEnabled($user)){
        $data['totp'] = 'Benutzername + Passwort + TOTP 2FA';
        $select = 'totp';
      }
    }
    catch(QueryFailureException $e) {
      $this->app->erp->InstallModul('totp');
    }

    if($this->app->erp->RechteVorhanden('stechuhrdevice','list') || $this->app->erp->RechteVorhanden('mitarbeiterzeiterfassung','list'))
    {
      $data['4'] = 'Mitarbeiterzeiterfassung QR-Code';
    }

    $out = "";
    foreach($data as $key=>$value) {
      $selected = (($select==$key) ? 'selected' : '');
      $out .= "<option value=\"$key\" $selected>$value</option>";
    }
    return $out;
  }

  function UserRights()
  {
    $id = $this->app->Secure->GetGET('id');
    $template = $this->app->Secure->GetPOST('usertemplate');
    $copytemplate = $this->app->Secure->GetPOST('copyusertemplate');
    $hwtoken = $this->app->DB->Select("SELECT hwtoken FROM `user` where id = '$id' LIMIT 1");
    $modules = $this->ScanModules();
    if($hwtoken == 4)
    {
      $modulecount = count($modules);
      $curModule = 0;
      foreach($modules as $module=>$actions) {
        $lower_m = strtolower($module);	
        $curModule++;
        $actioncount = count($actions);
        for($i=0;$i<$actioncount;$i++) {
          $delimiter = (($curModule<$modulecount || $i+1<$actioncount) ? ', ' : ';');  
          $active = 0;
          if($lower_m == 'stechuhr' && ($actions[$i] == 'list' || $actions[$i] == 'change'))$active = 1;
          if($active==1){
            $this->app->DB->Insert("INSERT INTO userrights (`user`, module, action, permission) VALUES ('$id', '$lower_m', '{$actions[$i]}', '$active')");
            $this->permissionLog($this->app->User->GetID(),$id,$module,$actions[$i],$active);
          }
        }
      }     
    }else {

      if($template!='') {
        $mytemplate = $this->app->Conf->WFconf['permissions'][$template];
        $permissions = $this->app->DB->SelectArr("SELECT module,action FROM userrights WHERE user=$id");
        $this->app->DB->Delete("DELETE FROM userrights WHERE `user`='$id'");
        foreach ($permissions as $permission){
          $this->permissionLog($this->app->User->GetID(),$id,$permission['module'],$permission['action'],0);
        }
        //$sql = 'INSERT INTO userrights (user, module, action, permission) VALUES ';

        $modulecount = count($modules);
        $curModule = 0;
        foreach($modules as $module=>$actions) {
          $lower_m = strtolower($module);	
          $curModule++;
          $actioncount = count($actions);
          for($i=0;$i<$actioncount;$i++) {
            $delimiter = (($curModule<$modulecount || $i+1<$actioncount) ? ', ' : ';');  
            $active = ((isset($mytemplate[$lower_m]) && in_array($actions[$i], $mytemplate[$lower_m])) ? '1' : '0');
            if($active==1){
              $this->app->DB->Insert("INSERT INTO userrights (`user`, module, action, permission) VALUES ('$id', '$lower_m', '{$actions[$i]}', '$active')");
              $this->permissionLog($this->app->User->GetID(),$id,$module,$actions[$i],$active);
            }
          }
        }
        //$this->app->DB->Query($sql);
      }

      if($copytemplate!='') {
        $ok = true;
        //			echo "User $id $copytemplate";	
        if($ok)
        {
          $permissions = $this->app->DB->SelectArr("SELECT module,action FROM userrights WHERE user=$id");
          foreach ($permissions as $permission){
            $this->permissionLog($this->app->User->GetID(),$id,$permission['module'],$permission['action'],0);
          }
          $this->app->DB->Delete("DELETE FROM userrights WHERE `user`='$id'");
          $permissions = $this->app->DB->SelectArr("SELECT module,action FROM userrights WHERE user=$copytemplate");
          $this->app->DB->Update("INSERT INTO userrights (`user`, module,action,permission) (SELECT '$id',module, action,permission FROM userrights WHERE user='".$copytemplate."')");
          foreach ($permissions as $permission){
            $this->permissionLog($this->app->User->GetID(),$id,$permission['module'],$permission['action'],1);
          }
        }
      }
    }

    $dbrights = $this->app->DB->SelectArr("SELECT module, action, permission FROM userrights WHERE `user`='$id' ORDER BY module");
    $group = $this->app->DB->Select("SELECT `type` FROM `user` WHERE id='$id' LIMIT 1");

    $rights = $this->app->Conf->WFconf['permissions'][$group];
    if(is_array($dbrights) && count($dbrights)>0) 
      $rights = $this->AdaptRights($dbrights, $rights, $group);

    $modules = $this->ScanModules();
    $table = $this->CreateTable($id, $modules, $rights);	


    //$this->app->Tpl->Set('USERTEMPLATES', $this->TemplateSelect());	
    $this->app->Tpl->Set('USERNAMESELECT', $this->app->erp->GetSelectUser("",$id));	
    $this->app->Tpl->Set('MODULES', $table);
  }

  function UserChangeRights()
  {
    $user = $this->app->Secure->GetGET('b_user');
    $module = $this->app->Secure->GetGET('b_module');
    $action = $this->app->Secure->GetGET('b_action');
    $value = $this->app->Secure->GetGET('b_value');

    if(is_numeric($user) && $module!='' && $action!='' && $value!='') {
      $id = $this->app->DB->Select("SELECT id FROM userrights WHERE user='$user' AND module='$module' AND action='$action' LIMIT 1");
      if($value && $this->app->erp->isIoncube() && method_exists('erpAPI','Ioncube_getMaxLightusersRights') && method_exists('erpAPI','Ioncube_LightuserRechteanzahl'))
      {
        $lightuser = $this->app->DB->Select("SELECT id FROM `user` WHERE id = '$user' AND type='lightuser' LIMIT 1");
        if($lightuser)
        {
          $anzerlaubt = erpAPI::Ioncube_getMaxLightusersRights();
          $anzvorhanden = erpAPI::Ioncube_LightuserRechteanzahl($this->app, $user);
          if($anzvorhanden >= $anzerlaubt)
          {
            exit;
          }
          if($id)
          {
            if(!$this->app->DB->Select("SELECT permission FROM userrights WHERE id = '$id'"))exit;
          }else{
            if($anzvorhanden + 1 > $anzerlaubt)exit;
          }
        }
        if($value && method_exists($this->app->erp, 'ModuleBenutzeranzahlLizenzFehler') && ($err = $this->app->erp->ModuleBenutzeranzahlLizenzFehler($module)))
        {
          if(isset($err['Error']))
          {
            if(is_array($err['Error']))
            {
              echo "Error".implode('<br />',$err['Error']);
            }else{
              echo "Error".$err['Error'];
            }
          }
          exit;
        }
      }
      if(is_numeric($id) && $id>0)
      {
        if($value=="1")
        {
          $this->app->DB->Update("UPDATE userrights SET permission='$value' WHERE id='$id' LIMIT 1");
        }
        else
          $this->app->DB->Delete("DELETE FROM userrights WHERE user='$user' AND module='$module' AND action='$action'");
      }
      //$this->app->DB->Update("UPDATE userrights SET permission='$value' WHERE id='$id' LIMIT 1");
      else
        $this->app->DB->Insert("INSERT INTO userrights (user, module, action, permission) VALUES ('$user', '$module', '$action', '$value')");

      $this->permissionLog($this->app->User->GetID(),$user,$module,$action,$value);
    }

    echo $this->app->DB->Select("SELECT permission FROM userrights WHERE user='$user' AND module='$module' AND action='$action' LIMIT 1");


    exit;
  }

  public function permissionLog($grantingUserId,$receivingUserId,$module,$action,$permission){
    $grantingUserName = $this->app->DB->Select("SELECT username FROM user WHERE id=$grantingUserId");
    $receivingUserName = $this->app->DB->Select("SELECT username FROM user WHERE id=$receivingUserId");
    $permission = !empty($permission);
    try {
      $userPermission = $this->app->Container->get('UserPermissionService');
      $userPermission->log($grantingUserId,$grantingUserName,$receivingUserId,$receivingUserName,$module,$action,$permission);
    }catch (Exception $ex){
      $this->app->erp->LogFile('Fehler bei Zuweisung Rechtehistore',$ex->getMessage());
    }
  }


  function AdaptRights($dbarr, $rights) 
  {
    $cnt = count($dbarr);
    for($i=0;$i<$cnt;$i++) {
      $module = $dbarr[$i]['module'];
      $action = $dbarr[$i]['action'];
      $perm = $dbarr[$i]['permission'];

      if(isset($rights[$module])) {
        if($perm=='1' && !in_array($action, $rights[$module])) 
          $rights[$module][] = $action;

        if($perm=='0' && in_array($action, $rights[$module])) {
          $index = array_search($action, $rights[$module]);
          unset($rights[$module][$index]);
          $rights[$module] = array_values($rights[$module]);
        }
      }else if($perm=='1') $rights[$module][] = $action;
    }
    return $rights;
  }

  function CreateTable($user, $modules, $rights) 
  {
    $maxcols = 6;
    $width = 100 / $maxcols;
    $out = '';
    foreach($modules as $key=>$value) {
      if(strtolower($key) == 'api' || strtolower($key) == 'ajax')continue;
      $out .= "<tr><td class=\"name\">$key</td></tr>";

      $out .= "<tr><td><table class=\"action\">";
      $module = strtolower($key); 
      for($i=0;$i<$maxcols || $i<count($value);$i++) {
        if($i%$maxcols==0) $out .= "<tr>";

        if(isset($value[$i]) && in_array($value[$i], $rights[$module])) {
          $class = 'class="blue"';
          $active = '1';
        }else{
          $class = 'class="grey"';
          $active = 0;
        }
        $class = ((isset($value[$i])) ? $class : '');

        $action = ((isset($value[$i])) ? strtolower($value[$i]) : '');
        $onclick = ((isset($value[$i])) ? "onclick=\"ChangeRights(this, '$user','$module','$action')\"" : '');
        $out .= "<td width=\"$width%\" $class value=\"$active\" $onclick>{$action}</td>";

        if($i%$maxcols==($maxcols-1)) $out .= "</tr>";
      }
      $out .= "</table></td></tr>";
    }

    return $out;
  }

  /**
   * @param string $page
   * @param array  $actions
   *
   * @return array
   */
  public function getActionsFromFile($page, $actions = [])
  {
    if(substr($page,-8) === '.src.php') {
      return $actions;
    }
    $content = file_get_contents($page);
    $foundItems = preg_match_all('/ActionHandler\([\"|\\\'][[:alnum:]].*[\"|\\\'],/', $content, $matches);
    if($foundItems <= 0) {
      return $actions;
    }
    $action = str_replace(array('ActionHandler("','ActionHandler(\'','",' , '\',' ),'', $matches[0]);
    if(empty($action) || !is_array($action)) {
      return $actions;
    }
    if(isset($actions)) {
      $actionsCount = $action ? count($action) : 0;
      for ($i = 0; $i < $actionsCount; $i++) {
        if(empty($action[$i])) {
          continue;
        }
        $found = false;
        foreach ($actions as $v) {
          if($v == $action[$i]){
            $found = true;
            break;
          }
        }
        if(!$found){
          $actions[] = $action[$i];
        }
      }
    }
    else{
      $actionsCount = $action ? count($action) : 0;
      for ($i = 0; $i < $actionsCount; $i++) {
        $actions[] = $action[$i];
      }
    }
    sort($actions);

    return $actions;
  }

  /**
   * @return array
   */
  public function ScanModules()
  {
    //$files = glob('./pages/*.php');
    $files = glob(__DIR__.'/*.php');
    $encodedActions = [];
    if(method_exists($this->app->erp,'getEncModullist')) {
      $encodedActions = $this->app->erp->getEncModullist();
    }
    if(empty($encodedActions)) {
      $encodedActions = [];
    }
    $modules = array();
    if(empty($files)) {
      return $encodedActions;
    }
    foreach($files as $page) {
      $name = ucfirst(str_replace('_custom','',basename($page,'.php')));
      if(substr($page,-8) === '.src.php') {
        continue;
      }

      $modules[$name] = $this->getActionsFromFile($page, isset($modules[$name]) ? $modules[$name]: []);

      if(!empty($encodedActions[$name]) && is_array($encodedActions[$name]) && count($encodedActions[$name]) > 0) {
        if(isset($modules[$name])) {
          $encodedActionsCount = $encodedActions[$name]?count($encodedActions[$name]):0;
          for($i=0;$i<$encodedActionsCount;$i++) {
            $found = false;
            foreach($modules[$name] as $moduleAction) {
              if($moduleAction == $encodedActions[$name][$i]) {
                $found = true;
                break;
              }
            }
            if(!$found) {
              $modules[$name][] = $encodedActions[$name][$i];
            }
          }
        }
        else{
          $modules[$name] = $encodedActions[$name];
        }
        sort($modules[$name]);
      }
    }

    foreach($modules as $name => $actions) {
      if(empty($actions)) {
        unset($modules[$name]);
      }
    }

    return $modules;	
  }

  function TemplateSelect()
  {
    $options = "<option value=\"\">-- Bitte ausw&auml;hlen --</option>";
    foreach($this->Templates as $key=>$value) {
      if($key!="web")
      $options .= "<option value=\"$key\">".ucfirst($key)."</option>";
     }

     return $options;
  }

  function GetTemplates()
  {
     return $this->app->Conf->WFconf['permissions'];
  }
}
