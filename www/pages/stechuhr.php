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
class Stechuhr {
  /** @var ApplicationCore $app */
  var $app;
  
  function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern)
    {
      return;
    }
    if(isset($_COOKIE['nonavigation']) && $_COOKIE['nonavigation'])$this->app->BuildNavigation = false;
    if(4 == $this->app->DB->Select("SELECT hwtoken from user where id = '".$this->app->User->GetID()."' LIMIT 1"))$this->app->BuildNavigation = false;
    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("change","StechuhrChange");
    $this->app->ActionHandlerListen($app);
  }

	function StechuhrChange($intern = false)
	{
    if($intern)
    {
      $cmd = $intern;
    }else{
			$cmd = $this->app->Secure->GetGET("cmd");
    }
    $smodule = $this->app->Secure->GetGET("smodule");
    $saction = $this->app->Secure->GetGET("saction");
    $sid = $this->app->Secure->GetGET("sid");
    $location = '';
    if($smodule)$location = 'index.php?module='.$smodule;
    if($location && $saction)$location .= '&action='.$saction;
    if($location && $sid)$location .= '&id='.$sid;

    if($cmd==='pause' || $cmd==='pausestart' || $cmd==='gehen') {
      $kommen=0;
    } else {
      $kommen=1;
    }
    $status = '';
    switch($cmd)
    {
      case 'pausestart':
      case 'pausestop':
      case 'kommen':
      case 'gehen':
        $status = $cmd;
      break;
      
    }
    if($status || $cmd === 'arbeit' || $cmd === 'pause')
    {
      $alterstatus = $this->app->DB->SelectArr("Select status, TIMESTAMPDIFF(HOUR,datum,now()) as dd, kommen from stechuhr where adresse = ".$this->app->User->GetAdresse()." order by datum desc limit 1");
      if($alterstatus)
      {
        $dd = $alterstatus[0]['dd'];
        $altkommen = $alterstatus[0]['kommen'];
        $alterstatus = $alterstatus[0]['status'];
      }
      if((!$alterstatus && $status === 'kommen') ||
        (!$alterstatus && $status === 'gehen') ||
        ($alterstatus === 'kommen' && $status !== 'kommen') ||
        ($alterstatus === 'gehen' && $status === 'kommen') ||
        ($alterstatus === 'pausestart' && $status === 'pausestop') ||
        ($alterstatus === 'pausestop' && $status === 'pausestart') ||
        ($alterstatus === 'pausestop' && $status === 'gehen') ||
        ($alterstatus === 'pausestart' && $status === 'gehen') ||
        ($cmd === 'arbeit') ||
        ($cmd === 'pause')
      )
      {
        if(!(!$status && $alterstatus && $altkommen == 1 && $kommen == 0 ) ||  $cmd == 'arbeit' || $cmd == 'pause')
        {
          if($status == '' && $kommen == 1 && $alterstatus === 'pausestart')$status = 'pausestop';
          if(($alterstatus === false || $alterstatus === 'gehen')&& $kommen == 1 && $status == '')$status = 'kommen';
          
          $this->app->DB->Insert("INSERT INTO stechuhr (id,adresse,user,datum,kommen, status) 
            VALUES ('','".$this->app->User->GetAdresse()."','".$this->app->User->GetID()."',NOW(),'".$kommen."','".($status)."')");
          $insid = $this->app->DB->GetInsertID();  
        }
      }
    }
    if($intern)return;
    if($this->app->BuildNavigation === false)
    {
      $this->app->Location->execute("index.php?module=welcome&action=logout");
      
    } else {
      $this->app->Location->execute(($location?$location:$_SERVER['HTTP_REFERER']));
    }
    $this->app->ExitXentral();
	}
  
}

