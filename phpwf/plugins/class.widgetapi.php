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

class WidgetAPI 
{
  private $app;

  function __construct(&$app)
  {
    $this->app = &$app;
  }

  function Get($name, $parsetarget)
  {
    if(file_exists("widgets/widget.$name.php")) {
      include_once("widgets/widget.$name.php");
      //echo "es gibt ein modifiziertes objecy";
      $classname = "Widget".ucfirst($name);
      return new $classname($this->app,$parsetarget);	
    } else {
      //echo "es gibt nur das generiewrte";
      include_once("widgets/_gen/widget.gen.$name.php");
      //echo "es gibt ein modifiziertes objecy";
      $classname = "WidgetGen".ucfirst($name);
      return new $classname($this->app,$parsetarget);	
    }


  }
}
?>
