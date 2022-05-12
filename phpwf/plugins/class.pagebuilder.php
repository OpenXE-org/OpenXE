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

class PageBuilder
{
  private $app;

  function __construct($app)
  {
    $this->app = &$app;
  }

 
  function CreateGen($tplfile)
  {
    $widgets = $this->app->Tpl->GetVars("pages/content/_gen/".$tplfile); 
    $this->CreatePage($widgets,$tplfile);
  }
  
  function Create($tplfile)
  {
    $widgets = $this->app->Tpl->GetVars("pages/content/".$tplfile); 
    $this->CreatePage($widgets,$tplfile);
  }
  
  
  function CreatePage($widgets,$tplfile)
  {
    if(count($widgets)>0){
      foreach($widgets as $key=>$varname) {
	// pruefen ob es ein widget sein soll
	if(preg_match("/^[\[]WIDGET_/",$varname)) {
	  $classname = "";
	  $varname = str_replace('[','',$varname);
	  $varname = str_replace(']','',$varname);
	  if(count(explode('_',$varname))>3)
	  {
	    list($type,$classname,$tmp,$action)=explode('_',$varname);
	    $classname = $classname."_".$tmp;
	  }
	  else {
	  	list($type,$classname,$action)=explode('_',$varname);
		}
	  // pruefe ob es ein abgeleitetes gibt wenn nicht starte das generierte
    $classnamecustom = $classname.'Custom';
    $filenamecustom = strtolower($classname.'_custom');
    if(file_exists("widgets/widget.".$filenamecustom.".php")) {
      $classname = "Widget".ucfirst(strtolower($classname)).'Custom';
      $filename = "widget.".$filenamecustom.".php";
      include_once("widgets/$filename");
	  }elseif(file_exists("widgets/widget.".strtolower($classname).".php")) {
	    $filename = "widget.".strtolower($classname).".php";
	    $classname = "Widget".ucfirst(strtolower($classname));
	    $action = ucfirst(strtolower($action));
	    include_once("widgets/$filename");
	  } else {
	    $filename = "widget.gen.".strtolower($classname).".php";
	    $classname = "WidgetGen".ucfirst(strtolower($classname));

	    $action = ucfirst(strtolower($action));
	    include_once("widgets/_gen/$filename");
	  }

	  $mywidget = new $classname($this->app,$varname);
	  $mywidget->$action();
//	  $mywidget->__destruct();
	}
      }
    }
    $this->app->Tpl->Parse('PAGE',$tplfile);
  }

}
?>
