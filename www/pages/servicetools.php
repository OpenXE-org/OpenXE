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

class Servicetools {
  var $app;
  
  function __construct(&$app) {
    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","ServicetoolsList");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }



  function ServicetoolsList()
  {

    $this->app->erp->MenuEintrag("index.php?module=servicetools&action=list","&Uuml;bersicht");

    $this->app->Tpl->Parse('TAB1',"servicetools_list.tpl");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }




}

?>
