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

class Mailausgang {
  var $app;

  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "mailausgang_list":
      $allowed['mailausgang'] = array('list');

      $heading = array('','Zeit','To','Subject','From','Fehler', 'Men&uuml;');
      $width = array('1%','10%', '10%', '30%','10%','20%','1%');

      $moreinfo=true;
      $menucol = 6;

      $findcols = array('open','m.zeit', 'm.to', 'm.subject','m.from','m.status','m.id');
      $searchsql = array('m.zeit', 'm.to', 'm.subject','m.from','m.status');

      $datecols = array(1);

      $defaultorder = 2;
      $defaultorderdesc = 1;

      $menu = "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=mailausgang&action=delete&id=%value%\")><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

      $where = " m.id > 0 ";

      $sql = "SELECT SQL_CALC_FOUND_ROWS m.id, '<img src=./themes/{$app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
         m.zeit, IF(m.art=0,m.to,IF(m.art = 1,CONCAT('<u>',m.to,'</u>'),CONCAT('<span style=\"border-bottom: 1px dotted #000;\">',m.to,'</span>'))), m.subject, m.from, m.status, m.id FROM mailausgang m";


      $count = "SELECT count(m.id) FROM mailausgang m WHERE $where";

      break;

    }

    $erg = [];

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))$erg[$v] = $$v;
    }
    return $erg;
  }

  function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern)return;
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("list", "MailausgangList");
    $this->app->ActionHandler("delete", "MailausgangDelete");
    $this->app->ActionHandler("minidetail", "MailausgangMiniDetail");
    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);

  }

  

  function MailausgangMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=mailausgang&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function MailausgangList()
  {
    $this->app->erp->MenuEintrag("index.php?module=mailausgang&action=list","&Uuml;bersicht");

    $this->app->YUI->TableSearch('TAB1','mailausgang_list', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","tabview.tpl");
  }

  function MailausgangMiniDetail()
  {
    $id = $this->app->Secure->GetGET("id");
    echo utf8_encode($this->app->DB->Select("SELECT body FROM mailausgang WHERE id = '$id' LIMIT 1"));
    exit;
  }


  function MailausgangDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->DELETE("DELETE FROM mailausgang WHERE id = '$id' LIMIT 1");

    header("Location: index.php?module=mailausgang&action=list");
    exit;
  }


}
