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
if(!class_exists('Gruppen'))
{
  class Gruppen
  {
    function __construct($app, $intern = false)
    {
      $this->app=$app;
      if($intern) {
        return;
      }

      $this->app->ActionHandlerInit($this);

      $this->app->ActionHandler("list","GruppenList");
      $this->app->DefaultActionHandler("list");


      $this->app->ActionHandlerListen($app);
    }    
    function GruppenList()
    {
      $this->app->Tpl->Set('VERS','Professional');
      $this->app->Tpl->Set('MODUL','Professional');
      $this->app->Tpl->Parse('PAGE', "only_version.tpl");
    }
  }

}
