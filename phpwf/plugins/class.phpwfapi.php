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


/// special layer for webapplications
class phpWFAPI
{
  function __construct(&$app)
  {
    $this->app=&$app;
  }

  function ReBuildPageFrame()
  {
    $this->app->Tpl->ResetParser();
    $this->BuildPageFrame();
  }


  function BuildPageFrame()
  {
    $this->app->Tpl->ReadTemplatesFromPath(__DIR__."/../defaulttemplates/");

    // build template tree
    $this->app->Page->LoadTheme($this->app->WFconf['defaulttheme']);

    if($this->app->User->GetType()=="")
      $this->app->Page->CreateNavigation($this->app->WFconf['menu'][$this->app->WFconf['defaultgroup']]);
    else
      $this->app->Page->CreateNavigation($this->app->WFconf['menu'][$this->app->User->GetType()]);

    // start acutally application instance
    $this->app->Tpl->ReadTemplatesFromPath("pages/content/_gen");
    $this->app->Tpl->ReadTemplatesFromPath("pages/content/");
  }


  function StartRequestedCommand()
  {
    $defaultpage = $this->app->WFconf['defaultpage'];
    $defaultpageaction = $this->app->WFconf['defaultpageaction'];
  
    $module = $this->app->Secure->GetGET('module','alpha'); 
    $action = $this->app->Secure->GetGET('action','alpha'); 
    
    if(!file_exists("pages/".$module.".php"))
      $module = $defaultpage;
   
    if($action=="")
      $action = $defaultpageaction;
   
    if(!$this->app->acl->Check($this->app->User->GetType(),$module,$action))
      return;


    // start module
    if(file_exists("pages/".$module.".php"))
    {
      include("pages/".$module.".php");
      //create dynamical an object
      $constr=strtoupper($module[0]).substr($module, 1);
      $myApp = new $constr($this->app);
    } 
    else 
    {
      echo $this->app->WFM->Error("Module <b>$module</b> doesn't exists in pages/");

    }
    $this->app->acl->CheckTimeOut();
  }

  /// mit dem "erstellen Formular" einfach bearbeiten liste + formular anzeigen
  function EasyTableList($tablename,$cols,$parsetarget,$pkname,$delmsg,$delmsgcol)
  {
    // show list

    // create html table
    $table = new HTMLTable("0","100%");
    $table->AddRowAsHeading($cols); 
      
    $all = $this->app->DB->SelectTable($tablename,$cols);

    $table->AddField($all); 

    $action = $this->app->Secure->GetGET("action","alpha");
    $module = $this->app->Secure->GetGET("module","alpha");

    $table->AddCompleteCol(0,
      "<a href=\"index.php?module=$module&action=$action&id=%col%\">bearbeiten</a>");
    
    $table->AddCompleteCol(0,
      "<a href=\"#\" onclick=\"str = confirm('{$delmsg}');
      if(str!='' & str!=null) 
      window.document.location.href='index.php?module=$module&action=$action&id=%col%&formaction=delete';\">
      loeschen</a>",$delmsgcol);
 
    $table->ChangingRowColors('#ffffff','#dddddd');
      
    $this->app->Tpl->Set($parsetarget,$table->Get()); 
  }

  function Message($msg,$parsetarget='MSGBOX')
  {
    $this->app->Tpl->Add('MSGBOXTEXT',$msg);
    $this->app->Tpl->Parse($parsetarget,"messagebox.tpl");
  }
  // emailvorlage aus db senden

  function EmailFromTemplate($template,$to,$values)
  {
    $betreff = $this->app->DB->Select("SELECT betreff 
      FROM emailvorlagen WHERE name='$template' LIMIT 1");

    $nachricht = $this->app->DB->Select("SELECT nachricht 
      FROM emailvorlagen WHERE name='$template' LIMIT 1");

    if(count($values) > 0)
    {
      foreach($values as $key=>$value)
      {
        $nachricht = str_replace("%".$key."%",$value,$nachricht);
        $betreff = str_replace("%".$key."%",$value,$betreff);
      }
    }
    
    $nachricht = str_replace('#BR#',"\n",$nachricht);
    mail($to,$betreff,$nachricht,"From: ActConnect Team <info@actconnect.de>");

  }
}
?>
