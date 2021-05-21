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
class Paketmarke {
  /** @var Application $app */
  var $app;

  /**
   * Paketmarke constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    //parent::GenPaketmarke($app);
    $this->app=$app;
    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","PaketmarkeCreatePopup");
    $this->app->ActionHandler("tracking","PaketmarkeTracking");

    $this->app->ActionHandlerListen($app);

  }

  function PaketmarkeTracking()
  {
    $this->app->erp->Headlines('Paketmarken Drucker');

    $this->app->Tpl->Set('PAGE',"Tracking-Nummer: <input type=\"text\" id=\"tracking\"><script type=\"text/javascript\">document.getElementById(\"tracking\").focus(); </script>");
    //$this->app->BuildNavigation=false;
  }

  function PaketmarkeCreatePopup()
  {
    $this->app->erp->Headlines('Paketmarken Drucker');
      $this->app->erp->PaketmarkeDHLEmbedded('PAGE','lieferschein');
  }
}

