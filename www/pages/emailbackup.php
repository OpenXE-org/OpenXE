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

Copyright (c) 2022 Xenomporio project

*/
?>
<?php

use Xentral\Components\Database\Exception\QueryFailureException;

class Emailbackup
{
  function __construct($app, $intern = false)
  {
    $this->app=$app;
    if($intern)return;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","EmailbackupCreate");
    $this->app->ActionHandler("delete","EmailbackupDelete");
    $this->app->ActionHandler("edit","EmailbackupEdit");
    $this->app->ActionHandler("list","EmailbackupList");

    $this->app->DefaultActionHandler("list");

    //$this->Templates = $this->GetTemplates();

    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
  }

  function EmailbackupList()
  {
    $this->app->erp->MenuEintrag("index.php?module=uservorlage&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=uservorlage&action=history","Historie");
    $this->app->erp->MenuEintrag("index.php?module=uservorlage&action=create","Neue Benutzervorlage anlegen");
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $this->app->YUI->TableSearch('ACCOUNT_TABLE','mailaccount_list',"show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE', "emailbackup_list.tpl");

  }

	static function TableSearch(&$app, $name, $erlaubtevars)  {
    switch ($name) {
      case "mailaccount_list":

        $allowed['mailaccount_list'] = array('list');
        $heading = array('Adresse', 'Angezeigter Name','Ticket','Emailbackup', 'Men&uuml;');
        $width = array('40%','50%', '10%');

        $findcols = array('id','email', 'angezeigtername','ticket','emailbackup');
        $searchsql = array('id','email', 'angezeigtername');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>"."<a href=\"index.php?module=emailbackup&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.png\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=emailbackup&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>"."</td></tr></table>";

        $sql = "SELECT id, email, angezeigtername, CASE WHEN ticket=1 THEN 'Ja' ELSE '' END, CASE WHEN emailbackup=1 THEN 'Ja' ELSE '' END, id FROM emailbackup"; 

//        $where = "m.geloescht = 0";

        $groupby = "";

//        $count = "SELECT count(DISTINCT id) FROM emailbackup WHERE $where";
      break;
    }

    $erg = false;

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))$erg[$v] = $$v;
    }
    return $erg;
  }

  public function UservorlageDelete(): void
  {
    $id = (int)$this->app->Secure->GetGET('id');

    $benutzervorlage = $this->app->DB->Select("SELECT bezeichnung FROM `uservorlage` WHERE id='$id' LIMIT 1");	
    $users = $this->app->DB->Select("SELECT `username` FROM `user` WHERE `vorlage` = '$benutzervorlage'");
    $prefix = "\"";
	if (!empty($users)) {		
		$usernames = "";
		if (is_array($users)) {
			foreach ($users as $user) {
				$usernames = $usernames.$prefix.$user[0]."\"";
				$prefix = ", \"";
			}
		} else {
			$usernames = $users;
		}

	      $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">{|Benutzervorlage \"$benutzervorlage\" ist in Benutzung durch ".$usernames.".|}</div>");
	} else {
	        $this->app->DB->Delete("DELETE FROM `uservorlage` WHERE `id` = '{$id}'");
	        $this->app->DB->Delete("DELETE FROM `uservorlagerights` WHERE `vorlage` = '{$id}'");
	        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Die Benutzervorlage \"$benutzervorlage\" wurde gel&ouml;scht.</div>");		
	}    

    $this->UservorlageList();
  }

  function UservorlageCreate()
  {
    $this->app->erp->MenuEintrag("index.php?module=uservorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $input = $this->GetInput();
    $submit = $this->app->Secure->GetPOST('submituservorlage');

    $error = '';
    $maxlightuser = 0;

    if($submit!='') {

      if($input['bezeichnung']=='') {
	 $error .= 'Geben Sie bitte einen Vorlagennamen ein.<br>';		
      }
      if($this->app->DB->Select("SELECT '1' FROM `uservorlage` WHERE bezeichnung='{$input['bezeichnung']}' LIMIT 1")=='1') {
        $error .= "Es existiert bereits eine Vorlage mit diesem Namen";
      }

      if($error!=='')
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
      else {

        $id = $this->app->erp->CreateBenutzerVorlage($input);

        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Benutzervorlage wurde erfolgreich angelegt.</div>");
        header("Location: index.php?module=uservorlage&action=edit&id=$id&msg=$msg");
        exit;
      }
    }

    $this->SetInput($input);

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
    $this->app->Tpl->Parse('PAGE', "uservorlage_edit.tpl");
  }

  function EmailbackupEdit()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->Tpl->Set('ID', $id);

    $this->app->erp->MenuEintrag("index.php?module=emailbackup&action=edit&id=$id","Details");
    $this->app->erp->MenuEintrag("index.php?module=emailbackup&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $id = $this->app->Secure->GetGET('id');
    $input = $this->GetInput();
    $submit = $this->app->Secure->GetPOST('submitemailbackup');

	// Input GET
    if(is_numeric($id) && $submit!='') {
      $error = '';
      if ($input['bezeichnung']=='') {
	 $error .= 'Geben Sie bitte eine Bezeichnung ein.<br>';
	}
	else {
          
          $this->app->DB->Update(
            sprintf(
              "UPDATE `uservorlage` 
            SET bezeichnung='%s', 
                beschreibung='%s'
              WHERE id=%d 
              LIMIT 1",
              $input['bezeichnung'],
              $input['beschreibung'],
              $id
            )
          );

          $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
        }	
    }	// END Input Get

    $email = $this->app->DB->Select("SELECT email FROM `emailbackup` WHERE id='$id' LIMIT 1");
    $angezeigtername = $this->app->DB->Select("SELECT angezeigtername FROM `emailbackup` WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Add('KURZUEBERSCHRIFT2',$email);
    $this->app->Tpl->Add('EMAIL',$email);
    $this->app->Tpl->Add('ANGEZEIGTERNAME',$angezeigtername);

    $this->app->Tpl->Parse('PAGE', "emailbackup_edit.tpl");
  }

  /**
   * @return array
   */
  public function GetInput(): array
  {
    $input = array();
    $input['EMAIL'] = $this->app->Secure->GetPOST('email');
    $input['ANGEZEIGTERNAME'] = $this->app->Secure->GetPOST('angezeigtername');

    return $input;
  }

  function SetInput($input)
  {
    $this->app->Tpl->Set('EMAIL', $input['email']);
    $this->app->Tpl->Set('ANGEZEIGTERNAME', $input['angezeigtername']);
  }

}
