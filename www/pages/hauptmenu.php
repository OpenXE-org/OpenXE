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
class Hauptmenu {
  var $app;
  
  function __construct(&$app) {
    //parent::GenHauptmenu($app);
    $this->app=&$app;


    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","HauptmenuList");


    $this->app->ActionHandlerListen($app);
    $this->app->Tpl->Set('UEBERSCHRIFT',"Hauptmen&uuml;");

  }



  function HauptmenuList()
  {

//this->WFconf[menu][mitarbeiter]
    $menu = $this->app->Conf->WFconf['menu'][$this->app->User->GetType()];
    $this->app->Tpl->Add('PAGE',"<table border=\"0\" width=\"100%\" style=\"background-color: #ffffff;\"><tr valign=\"top\">");

    $spalten_anzahl = 5;

    $i=0;
    if(count($menu)>0){
      foreach($menu as $key=>$value){
        $i++;
        if($value[first][2]!="")
//          $this->app->Tpl->Set(FIRSTNAV,' href="index.php?module='.$value[first][1].'&action='.$value[first][2].'"
          $this->app->Tpl->Set('FIRSTNAV','

          >'.$value['first'][0].'</a>');
        else
          $this->app->Tpl->Set('FIRSTNAV',' href="index.php?module='.$value['first'][1].'"
          >'.$value['first'][0].'</a>');

        $this->app->Tpl->Add('PAGE',"<td>");
        $this->app->Tpl->Parse('PAGE','firstnav.tpl');
        $spalten++;
        if(count($value['sec'])>0){
          $this->app->Tpl->Add('PAGE','<ul>');
          foreach($value['sec'] as $secnav){
            if($secnav[2]!="")
              $this->app->Tpl->Set('SECNAV',' href="index.php?module='.$secnav[1].'&action='.$secnav[2].'"
              >'.$secnav[0].'</a>');
            else
              $this->app->Tpl->Set('SECNAV',' href="index.php?module='.$secnav[1].'">'.$secnav[0].'</a>');

            $this->app->Tpl->Parse('PAGE','secnav.tpl');
          }
        $this->app->Tpl->Add('PAGE',"</td>");
        if($spalten % $spalten_anzahl == 0)
	{
          $this->app->Tpl->Add('PAGE',"</tr><tr valign=\"top\">");
	}
        }
      }
    }
    $restliche_td = $spalten % $spalten_anzahl;

    for($i=0;$i<$restliche_td;$i++) 
      $this->app->Tpl->Add('PAGE',"<td></td>");
    $this->app->Tpl->Add('PAGE',"</tr></table>");



  }





}

?>
