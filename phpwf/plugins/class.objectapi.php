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

class ObjectAPI 
{
  private $app;

  function __construct($app)
  {
    $this->app = &$app;
  }

  function Get($name)
  {


			if(file_exists("objectapi/mysql/object.$name.php")) {
						include_once("objectapi/mysql/object.$name.php");
						//echo "es gibt ein modifiziertes objecy";
						$classname = "Obj".ucfirst($name);
						return new $classname($this->app);
					} else {
						//echo "es gibt nur das generiewrte";
						include_once("objectapi/mysql/_gen/object.gen.$name.php");
						//echo "es gibt ein modifiziertes objecy";
						$classname = "ObjGen".ucfirst($name);
						return new $classname($this->app);
					}



  }

 
 /* 
  function CreatePage($widgets,$tplfile)
  {
    if(count($widgets)>0){
      foreach($widgets as $key=>$varname) {
	// pruefen ob es ein widget sein soll
	if(preg_match("/^[\[]WIDGET_/",$varname)) {
	  $classname = "";
	  $varname = str_replace('[','',$varname);
	  $varname = str_replace(']','',$varname);
	  list($type,$classname,$action)=split('_',$varname);
	  
	  // pruefe ob es ein abgeleitetes gibt wenn nicht starte das generierte
	  if(file_exists("widgets/widget.".strtolower($classname).".php")) {
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
	  
	           
	  $mywidget = new $classname(&$this->app,$varname);
	  $mywidget->$action();
	 
	}
      }
    }
  
    $this->app->Tpl->Parse(PAGE,$tplfile);
  }
*/
}
?>
