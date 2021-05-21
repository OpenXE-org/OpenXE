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
use Xentral\Components\Http\JsonResponse;

include '_gen/aufgabe.php';

class Aufgaben extends GenAufgabe {
  /** @var Application $app */
  var $app;

  /** @var string MODULE_NAME */
  const MODULE_NAME = 'Task';

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public function TableSearch($app, $name, $erlaubtevars)
  {
    switch($name)
    {
      case "aufgaben_meine":
        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenmeineprio').on('click', function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenmeineueberfaellige').on('click', function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenmeineintervall').on('click', function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenabgeschlossene').on('click', function() { fnFilterColumn4( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenmeine').on('click', function() { fnFilterColumn5( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenmeinevergebene').on('click', function() { fnFilterColumn6( 0 ); } );");
        for ($r = 1;$r < 7;$r++) {
          $this->app->Tpl->Add('JAVASCRIPT', '
                  function fnFilterColumn' . $r . ' ( i )
                  {
                  if(oMoreData' . $r . $name . '==1)
                  oMoreData' . $r . $name . ' = 0;
                  else
                  oMoreData' . $r . $name . ' = 1;

                  $(\'#' . $name . '\').dataTable().fnFilter( 
                    \'\',
                    i, 
                    0,0
                    );
                  }
                  ');
        }

        // ENDE EXTRA checkboxen

        // headings
        $sid = (int)$this->app->User->GetParameter('aufgabe_benutzer_simulieren');
        if($sid > 0 && $this->app->User->GetType()==='admin')
        {
          $adresse = $sid;
        }
        else
        {
          $adresse = $this->app->User->GetAdresse();
          $sid="";
        }
        $heading = [
          '', 'Aufgabe', 'Mitarbeiter', 'Kunde', 'Dauer',
          'Teilprojekt', 'Projekt', 'Prio', 'Abgabe', 'Status', 'Men&uuml;'];
        $width = [
          '1%', '25%', '25%', '25%', '1%',
          '5%', '1%', '1%', '1%'
        ];
        $findcols = [
          'open', 'a.aufgabe',
          "if(adr.id='".$adresse."' OR adr.id IS NULL,(if(a.initiator!='$adresse' AND a.initiator!=a.adresse,CONCAT('Initiator: ',initi.name),adr.name)),adr.name)","CONCAT(kdr.kundennummer,' ',kdr.name)",
          'stunden', 'ap.aufgabe',
          'p.abkuerzung',
          "if(a.prio=1,'<b><font color=red>hoch</font></b>',if(a.prio=-1,'niedrig','mittel'))",
          'a.abgabe_bis', "if(a.status = 'inbearbeitung', 'in Bearbeitung', a.status)", 'a.id'
        ];

        $searchsql = [
          'a.aufgabe',
          "CONCAT(kdr.kundennummer,' ',kdr.name)",
          'adr.name',
          $this->app->erp->FormatPreis('a.stunden',2), 'ap.aufgabe',
          'p.abkuerzung', "if(a.prio=1,'<b><font color=red>hoch</font></b>',if(a.prio=-1,'niedrig','mittel'))",
          "if(a.status = 'inbearbeitung', 'in Bearbeitung', a.status)", "DATE_FORMAT(a.abgabe_bis, '%d.%m.%Y')",
          'a.id'
        ];

        $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE verwendenals='bondrucker' LIMIT 1");

        if($seriennummer!=''){
          $menu_bon = "&nbsp;<a href=\"#\" onclick=AjaxCall(\"index.php?module=aufgaben&action=bondrucker&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/bon_druck.png\" border=\"0\"></a>";
        }
        else{
          $menu_bon = "&nbsp;<a href=\"#\" onclick=InfoBox(\"aufgabe_bondrucker\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/bon_druck.png\" border=\"0\"></a>";
        }

        $menu = "<table cellpadding=0 cellspacing=0>";
        $menu .= "<tr>";
        $menu .= "<td nowrap>";
        $menu .= '<a href="javascript:;" onclick="AufgabenEdit(%value%);">';
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>" . "&nbsp;";
        $menu .= "<a href=\"#\" onclick=FinalDialog(\"index.php?module=aufgaben&action=abschluss&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/haken.png\" border=\"0\">";
        $menu .= "</a>" . "&nbsp;";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=aufgaben&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>" . $menu_bon . "";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"2\" data-label-reference-id=\"%value%\" data-label-reference-table=\"aufgabe\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        //            $menucol=9;

        $datecols = array(8);
        $numbercols = array(4);

        // SQL statement
        $alignright=array(5);
        $sumcol = 5;

        $defaultorder = 11;
        $moreinfo=true;
        $menucol=10;

        // START EXTRA more


        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 
CONCAT(if(a.prio=1 OR (a.abgabe_bis <= NOW() AND a.abgabe_bis!='0000-00-00' AND a.status!='abgeschlossen'),CONCAT('<b><font color=',if(DATE_FORMAT(a.abgabe_bis,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d'),'blue','red'),'>',a.aufgabe,'</font></b>',if(a.abgabe_bis <= NOW() AND a.abgabe_bis!='0000-00-00' AND a.status!='abgeschlossen',if(DATE_FORMAT(a.abgabe_bis,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d'),' <b>Abgabe Heute!</b>',' <b>*** Abgabe &uuml;berf&auml;llig! ***</b>'),'')),a.aufgabe),if(a.intervall_tage>0,' (',''),if(a.intervall_tage=2,'w&ouml;chentlich',if(a.intervall_tage=3,'monatlich',if(a.intervall_tage=4,'j&auml;hrlich',if(a.intervall_tage=1,'t&auml;glich','')))),if(a.intervall_tage>0,')','')) as aufgabe,

                    if(adr.id='".$adresse."' OR adr.id IS NULL,(if(a.initiator!='$adresse' AND a.initiator!=a.adresse,CONCAT('Initiator: ',initi.name),adr.name)),adr.name) as mitarbeiter,
                  CONCAT(kdr.kundennummer,' ',kdr.name) as kunde, 
                if(a.stunden > 0,".$this->app->erp->FormatPreis('a.stunden',2).",'') as stunden, 
                ap.aufgabe,   p.abkuerzung as projekt,
                  if(a.prio=1,'<b><font color=red>hoch</font></b>',if(a.prio='-1','niedrig','mittel')) as prio, 
                  if(a.abgabe_bis,DATE_FORMAT(abgabe_bis,'%d.%m.%Y'),'') as abgabe,
                        if(a.status = 'inbearbeitung', 'in Bearbeitung', a.status) as status, a.id
                      FROM `aufgabe` AS `a` 
                      LEFT JOIN `projekt` AS `p` ON p.id=a.projekt
                      LEFT JOIN `arbeitspaket` AS `ap` ON a.teilprojekt = ap.id 
                      LEFT JOIN `adresse` AS `adr` ON a.adresse=adr.id  
                      LEFT JOIN `adresse` AS `initi` ON a.initiator=initi.id  
                      LEFT JOIN `adresse` AS `kdr` ON a.kunde=kdr.id  
        ";

        // Fester filter


        $more_data1 = $this->app->Secure->GetGET("more_data1");
        if ($more_data1 == 1) $subwhere[] = " (a.prio=1 OR a.prio=2 OR a.prio=3)";

        $more_data2 = $this->app->Secure->GetGET("more_data2");
        if ($more_data2 == 1) $subwhere[] = " (a.abgabe_bis <= NOW() AND a.abgabe_bis!='0000-00-00')";

        $more_data3 = $this->app->Secure->GetGET("more_data3");
        if ($more_data3 == 1) { $subwhere[] = " a.intervall_tage > 0 "; }
        else {
          if($sid=="0")
            $where_wdh = "(a.intervall_tage > 0 AND a.abgabe_bis <=NOW() AND a.status!='abgeschlossen' {TMP}) OR ";
          else
            $where_wdh = "(a.intervall_tage > 0 AND a.abgabe_bis <=NOW() AND a.status!='abgeschlossen' {TMP} AND a.adresse='$adresse') OR ";
          $where_wdh_else ="a.intervall_tage = 0 AND";
        }

        $more_data4 = $this->app->Secure->GetGET("more_data4");
        if ($more_data4 == 1) $subwhere[] = " a.status='abgeschlossen' ";
        else $subwhere[] = " a.status!='abgeschlossen' ";

        $more_data5 = $this->app->Secure->GetGET("more_data5");
        if ($more_data5 == 1) $subwhere[] = " a.adresse='".$this->app->User->GetAdresse()."' ";

        $more_data6 = $this->app->Secure->GetGET("more_data6");
        if ($more_data6 == 1) $subwhere[] = " (a.initiator='$adresse' AND a.initiator!=a.adresse) ";

        $tmp = '';
        $csubwhere = !empty($subwhere)?count($subwhere):0;
        for ($j = 0;$j < $csubwhere;$j++) $tmp.= " AND " . $subwhere[$j];

        $where_wdh = str_replace('{TMP}',$tmp,$where_wdh);

        if($sid <= 0){
          $where = " $where_wdh ($where_wdh_else 1 $tmp)";
        }
        else{
          $where = " $where_wdh ($where_wdh_else (a.adresse='" . $adresse . "' OR a.initiator='" . $adresse . "') $tmp)";
        }

        if($sid <= 0){
          $count = "SELECT COUNT(a.id) FROM aufgabe a WHERE $where_wdh ($where_wdh_else 1 $tmp)";
        }
        else{
          $count = "SELECT COUNT(a.id) FROM aufgabe a WHERE $where_wdh ($where_wdh_else (a.adresse='" . $adresse . "' OR a.initiator='" . $adresse . "') $tmp)";
        }

        break;
      case "aufgaben_archiv":
        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenoffeneigenearchiv').on('click', function() { fnFilterColumn11( 0 ); } );");
        for ($r = 11;$r < 12;$r++) {
          $this->app->Tpl->Add('JAVASCRIPT', '
                  function fnFilterColumn' . $r . ' ( i )
                  {
                  if(oMoreData' . $r . $name . '==1)
                  oMoreData' . $r . $name . ' = 0;
                  else
                  oMoreData' . $r . $name . ' = 1;

                  $(\'#' . $name . '\').dataTable().fnFilter( 
                    \'\',
                    i, 
                    0,0
                    );
                  }
                  ');
        }

        // ENDE EXTRA checkboxen

        // headings

        $heading = array('Aufgabe', 'Mitarbeiter', 'Projekt', 'Prio', 'Abgabe-Termin', 'Startseite', 'Status', 'Men&uuml;');
        $width = array('35%', '20%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('aufgabe', 'mitarbeiter', 'projekt', 'prio', 'abgabe', 'status', 'id');
        $searchsql = array('a.aufgabe', 'p.abkuerzung', 'adr.name', 'a.status', 'a.abgabe_bis', 'a.id');
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=aufgaben&action=edit&id=%value%#tabs-3\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=FinalDialog(\"index.php?module=aufgaben&action=abschluss&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/haken.png\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=aufgaben&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

        //            $menucol=9;

        // SQL statement

        $sql = "SELECT a.id, 
              if(a.prio,CONCAT('<b><font color=red>',a.aufgabe,'</font></b>'),a.aufgabe) as aufgabe,
                adr.name as mitarbeiter,
                  p.abkuerzung as projekt,
                  if(a.prio,'<b><font color=red>Prio</font></b>','Normal') as prio, 
                    if(a.abgabe_bis,DATE_FORMAT(abgabe_bis,'%d.%m.%Y'),'') as abgabe,
                      if(a.startseite,'Ja','Nein') as startseite,if((angelegt_am AND a.status='offen'), CONCAT(a.status,' (',DATE_FORMAT(angelegt_am,'%d.%m.%Y'),')'),a.status) as status, a.id
                          FROM  aufgabe a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";

        // Fester filter

        // START EXTRA more

        $subwhere[] = " a.status='abgeschlossen' ";
        $count = "SELECT COUNT(a.id) FROM aufgabe a WHERE  (a.adresse='" . $this->app->User->GetAdresse() . "' OR a.initiator='" . $this->app->User->GetAdresse() . "') AND a.startdatum='0000-00-00' AND a.status='abgeschlossen'";
        $more_data1 = $this->app->Secure->GetGET("more_data1");

        if ($more_data1 == 1) $subwhere[] = " a.adresse='" . $this->app->User->GetAdresse() . "' ";
        $tmp = '';
        $csubwhere = !empty($subwhere)?count($subwhere):0;
        for ($j = 0;$j < $csubwhere;$j++) $tmp.= " AND " . $subwhere[$j];
        $where = " (a.adresse='" . $this->app->User->GetAdresse() . "' OR a.initiator='" . $this->app->User->GetAdresse() . "' OR a.oeffentlich='1') AND a.startdatum='0000-00-00' AND a.id!='' $tmp";


        break;

      case "aufgaben":
        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenprio').on('click', function() { fnFilterColumn6( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenoffeneigene').on('click', function() { fnFilterColumn7( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#aufgabenintervall').on('click', function() { fnFilterColumn8( 0 ); } );");
        for ($r = 6;$r < 9;$r++) {
          $this->app->Tpl->Add('JAVASCRIPT', '
                  function fnFilterColumn' . $r . ' ( i )
                  {
                  if(oMoreData' . $r . $name . '==1)
                  oMoreData' . $r . $name . ' = 0;
                  else
                  oMoreData' . $r . $name . ' = 1;

                  $(\'#' . $name . '\').dataTable().fnFilter( 
                    \'\',
                    i, 
                    0,0
                    );
                  }
                  ');
        }

        // ENDE EXTRA checkboxen

        // headings

        $heading = array('Aufgabe','Kunde','Dauer (h)','Mitarbeiter', 'Projekt', 'Prio', 'Abgabe', 'Men&uuml;');
        $width = array('200px', '20%','1%', '20%', '1%', '1%', '1%');
        $findcols = array('aufgabe','kunde','stunden', 'mitarbeiter', 'projekt', 'prio', 'abgabe', 'id');
        $searchsql = array('a.aufgabe', 'kdr.name', 'a.stunden','p.abkuerzung', 'adr.name', 'a.abgabe_bis', 'a.id');


        $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE verwendenals='bondrucker' LIMIT 1");
        if($seriennummer!="")
          $menu_bon = "&nbsp;<a href=\"#\" onclick=AjaxCall(\"index.php?module=aufgaben&action=bondrucker&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/bon_druck.png\" border=\"0\"></a>";
        else
          $menu_bon = "&nbsp;<a href=\"#\" onclick=InfoBox(\"aufgabe_bondrucker\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/bon_druck.png\" border=\"0\"></a>";


        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=aufgaben&action=edit&id=%value%&back=alle#tabs-3\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=FinalDialog(\"index.php?module=aufgaben&action=abschluss&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/haken.png\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=aufgaben&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" .$menu_bon. "</td></tr></table>";
        //            $menucol=9;

        // SQL statement
        $alignright=array(3);
        $sumcol = 3;

        $defaultorder = 8;

        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, 
              
CONCAT(if(a.prio=1 OR (a.abgabe_bis <= NOW() AND a.abgabe_bis!='0000-00-00'),CONCAT('<b><font color=',if(DATE_FORMAT(a.abgabe_bis,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d'),'blue','red'),'>',a.aufgabe,'</font></b>',if(a.abgabe_bis <= NOW() AND a.abgabe_bis!='0000-00-00' AND a.status!='abgeschlossen',if(DATE_FORMAT(a.abgabe_bis,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d'),' <b>Abgabe Heute!</b>',' <b>*** Abgabe &uuml;berf&auml;llig! ***</b>'),'')),a.aufgabe),if(a.intervall_tage>0,' (',''),if(a.intervall_tage=2,'w&ouml;chentlich',if(a.intervall_tage=3,'monatlich',if(a.intervall_tage=4,'j&auml;hrlich',if(a.intervall_tage=1,'t&auml;glich','')))),if(a.intervall_tage>0,')','')) as aufgabe,

              kdr.name as kunde,
                if(a.stunden > 0,a.stunden,'') as stunden, 
                adr.name as mitarbeiter,
                  p.abkuerzung as projekt,
                  if(a.prio=1,'<b><font color=red>Prio</font></b>',if(a.prio=-1,'Keine&nbsp;Prio','Normal')) as prio, 
                  if(a.abgabe_bis,DATE_FORMAT(abgabe_bis,'%d.%m.%Y'),'') as abgabe,
                    a.id
                          FROM  aufgabe a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  LEFT JOIN adresse kdr ON a.adresse=kdr.id";

        // Fester filter

        // START EXTRA more

        $count = "SELECT COUNT(a.id) FROM aufgabe a WHERE  (a.adresse='" . $this->app->User->GetAdresse() . "' OR a.initiator='" . $this->app->User->GetAdresse() . "')";

        $more_data6 = $this->app->Secure->GetGET("more_data6");
        if ($more_data6 == 1) $subwhere[] = " a.prio=1 ";

        $more_data7 = $this->app->Secure->GetGET("more_data7");
        if ($more_data7 == 1) $subwhere[] = " (a.abgabe_bis <= NOW() AND a.abgabe_bis!='0000-00-00') ";


        $more_data8 = $this->app->Secure->GetGET("more_data8");
        if ($more_data8 == 1) { $subwhere[] = " a.intervall_tage > 0 "; }
        else {
          $where_wdh = "(a.intervall_tage > 0 AND a.abgabe_bis <=NOW() AND a.status!='abgeschlossen' {TMP} AND (a.adresse='" . $this->app->User->GetAdresse() . "' OR a.initiator='" . $this->app->User->GetAdresse() . "')) OR ";
          $where_wdh_else ="a.intervall_tage = 0 AND";
        }
        $tmp = '';
        $csubwhere = !empty($subwhere)?count($subwhere):0;
        for ($j = 0;$j < $csubwhere;$j++) $tmp.= " AND " . $subwhere[$j];

        $where_wdh = str_replace('{TMP}',$tmp,$where_wdh);

        $where = " $where_wdh ($where_wdh_else (a.adresse='" . $this->app->User->GetAdresse() . "' OR a.initiator='" . $this->app->User->GetAdresse() . "') AND a.status!='abgeschlossen' $tmp)";

        $count = "SELECT COUNT(a.id) FROM aufgabe a WHERE $where_wdh ((a.adresse='" . $this->app->User->GetAdresse() . "' OR a.initiator='" . $this->app->User->GetAdresse() . "') AND a.status!='abgeschlossen')";

        break;

      case 'task_project_create_employee':

        $allowed['aufgabe'] = ['list'];
        $id = (int)$this->app->Secure->GetGET('id');
        $smodule = $this->app->Secure->GetGET('smodule');
        if($smodule === 'aufgabenliste') {
          $id = (int)$this->app->User->GetParameter('task_project_id');
        }
        // headings
        $heading = ['','Mitarbeiter-Nr.', 'Name',''];
        $width = ['1%', '10%', '25%','1%'];
        $findcols = ['a.id','a.mitarbeiternummer', 'a.name', 'a.id'];
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        $searchsql = ['a.mitarbeiternummer', 'a.name'];
        $menu = '';
        $sql = sprintf(
          "SELECT SQL_CALC_FOUND_ROWS a.id,
           CONCAT('<input type=\"checkbox\" data-id=\"',a.id,
               '\" class=\"projectcreateemployee\" ',(IF(uk.value = '1',' checked ', '')),' />'), 
                  a.mitarbeiternummer, a.name, a.id
           FROM `adresse` AS `a` 
           LEFT JOIN `userkonfiguration` AS `uk` 
               ON CONCAT('task_project_create_employee_',a.id) = uk.name AND uk.user = %d",
          $this->app->User->GetID()
        )
        ;
        $where = " a.mitarbeiternummer <> '' AND a.geloescht <> 1 ";
        $count = "SELECT count(a.id) FROM `adresse` AS `a` WHERE $where";

        break;
      case 'task_subproject_create_employee':
        $allowed['aufgabe'] = ['list'];

        $projectId = (int)$this->app->User->GetParameter('task_project_id');
        $heading = array('Mitarbeiternummer','Mitarbeiter','Bezeichnung','Stunden','Stundenlohn','');
        $width = array('20%','25%','20%','15%','14%','1%');
        $findcols = array( 'a.mitarbeiternummer','a.name','uktitle.value','ukhours.value','ukhourlyrate.value', 'a.id');
        $searchsql = array('a.mitarbeiternummer','a.name','uktitle.value','ukhours.value','ukhourlyrate.value', 'a.id');

        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;

        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.mitarbeiternummer, a.name AS `name`,
            concat('<input data-id=',a.id,' class=\"subprojectemployee title\"  type=\"text\" value=\"',
                ifnull(uktitle.value,ifnull(
                    (SELECT zk.beschreibung FROM zeiterfassung_kosten zk WHERE zk.adresse = a.id 
                    AND (zk.gueltig_ab = '0000-00-00' OR zk.gueltig_ab <= curdate()) ORDER by zk.gueltig_ab DESC LIMIT 1 ),
                    ''))
                ,'\" name=\"stundensatzname_',a.id,'\" id=\"stundensatzname_',a.id,'\" />'
                ) , 
            concat('<input data-id=',a.id,' class=\"subprojectemployee hours\" type=\"text\" value=\"',
                ifnull(ukhours.value,0),'\" name=\"stunden_',a.id,'\" id=\"stunden_',a.id,'\" />') , 
            concat('<input data-id=',a.id,' class=\"subprojectemployee hourlyrate\"  type=\"text\" value=\"',
                ifnull(ukhourlyrate.value,ifnull((SELECT zk.stundensatz FROM zeiterfassung_kosten zk WHERE zk.adresse = a.id 
                AND (zk.gueltig_ab = '0000-00-00' OR zk.gueltig_ab <= curdate()) ORDER by zk.gueltig_ab DESC LIMIT 1 ),0))
                ,'\" name=\"stundensatz_',a.id,'\" id=\"stundensatz_',a.id,'\" />')
            , a.id as menu
            FROM `adresse` AS `a` 
            INNER JOIN `adresse_rolle` AS `a2` ON a2.adresse=a.id 
            INNER JOIN `projekt` AS `p` ON p.id=a2.projekt OR p.id = a.projekt AND p.id = '".$projectId."'
            LEFT JOIN `userkonfiguration` AS `uktitle` 
            ON CONCAT('task_subproject_create_employee_title_',a.id) = uktitle.name AND uktitle.user = '".$this->app->User->GetID()."'
            LEFT JOIN `userkonfiguration` AS `ukhours` 
            ON CONCAT('task_subproject_create_employee_hours_',a.id) = ukhours.name AND ukhours.user = '".$this->app->User->GetID()."'
            LEFT JOIN `userkonfiguration` AS `ukhourlyrate` 
            ON CONCAT('task_subproject_create_employee_hourlyrate_',a.id) = ukhourlyrate.name AND ukhours.user = '".$this->app->User->GetID()."'
            
            ";
        $groupby = ' GROUP BY a.id ';
        $where = " (a2.projekt='$projectId' 
        AND (a2.bis='0000-00-00' OR a2.bis >= CURDATE()) 
        AND a2.objekt like 'Projekt' AND a.geloescht != 1 AND mitarbeiternummer != ''
        ) ";

        $count = "SELECT COUNT(DISTINCT a.id) 
            FROM `adresse` AS `a` 
            INNER JOIN `adresse_rolle` AS `a2` ON a2.adresse=a.id 
            INNER JOIN `projekt` AS `p` ON p.id=a2.projekt OR p.id = a.projekt AND p.id = '".$projectId."'
            WHERE $where";
        break;
    }

    $erg = [];

    //$erlaubtevars = array('heading','width','sql','count','findcols','searchsql','defaultorder','defaultorderdesc','menu','menucol','where','groupby','allowed');


    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))
      {
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  /**
   * Aufgaben constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    
    $this->app=$app;
    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("edit","AufgabenEdit");
    $this->app->ActionHandler("bondrucker","AufgabenBondrucker");
    $this->app->ActionHandler("editwdh","AufgabenEditWdh");
    $this->app->ActionHandler("abschluss","AufgabenAbschluss");
    $this->app->ActionHandler("pdf","AufgabenPDF");
    $this->app->ActionHandler("pdfextern","AufgabenPDFExtern");
    $this->app->ActionHandler("delete","AufgabenDelete");
    $this->app->ActionHandler("list","AufgabenList");
    $this->app->ActionHandler("create","AufgabenCreate");
    $this->app->ActionHandler("kalender","AufgabenKalender");
    $this->app->ActionHandler("dragdropaufgabe","AufgabenDragDrop");
    $this->app->ActionHandler("sortaufgabe","AufgabenSort");
    $this->app->ActionHandler("dateien","AufgabenDateien");
    $this->app->ActionHandler("data","AufgabenData");
    $this->app->ActionHandler("minidetail","AufgabenMiniDetail");
    $this->app->ActionHandler("mail","AufgabenMail");
    $this->app->ActionHandler("alle","AufgabenAlle");
    $this->app->ActionHandler("bon", "AufgabenBon");

    $this->app->Tpl->Set('UEBERSCHRIFT',"Aufgaben");
    $this->app->ActionHandlerListen($app);
  }

  public function Install(): void
  {
    $this->app->erp->CheckTable("task_timeline");
    $this->app->erp->CheckColumn("id", "int(11)", "task_timeline", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("task_id", "int(11)", "task_timeline", "NOT NULL");
    $this->app->erp->CheckColumn("address_id", "int(11)", "task_timeline", "NOT NULL");
    $this->app->erp->CheckColumn("time","timestamp","task_timeline","DEFAULT CURRENT_TIMESTAMP");
    $this->app->erp->CheckColumn("content", "TEXT", "task_timeline", "NOT NULL");
    $this->app->erp->CheckIndex('task_timeline', 'task_id');

    $this->app->erp->CheckTable('task_subscription');
    $this->app->erp->CheckColumn("id", "int(11)", "task_subscription", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("task_id", "int(11)", "task_subscription", "NOT NULL");
    $this->app->erp->CheckColumn("address_id", "int(11)", "task_subscription", "NOT NULL");
    $this->app->erp->CheckIndex('task_subscription', 'task_id');
  }

  function AufgabenMenu()
  {
    $this->app->Tpl->Set('BEFORETABS','$( "#accordion" ).accordion();');
    $this->app->erp->StartseiteMenu();
  } 

  function AufgabenAlle()
  {
    return;
  } 

  function AufgabenMail()
  {
    $id = $this->app->Secure->GetGET("id");

    if($id <= 0){
      $msg =  "Bitte zuerst die Aufgabe speichern";
      echo json_encode(array('status'=>0,'statusText'=>$msg));
      $this->app->erp->ExitWawi();
    }

    $mail = $this->app->DB->Select("SELECT adr.email FROM aufgabe a LEFT JOIN adresse adr ON a.adresse=adr.id WHERE a.id='$id' LIMIT 1");

    $result = $this->app->erp->AufgabenMail($id);

    if($result){
      $msg =  "Die Aufgabe wurde per Mail an $mail gesendet!";
      echo json_encode(array('status'=>1,'statusText'=>$msg));
      $this->app->erp->ExitWawi();
    }else{
      if($mail==""){
        $empfaenger = $this->app->DB->Select("SELECT a.name FROM adresse a LEFT JOIN aufgabe auf ON a.id = auf.adresse WHERE auf.id = '$id' LIMIT 1");
        $mail=" $empfaenger (Mailadresse von Mitarbeiter fehlt)";
      }
      $msg =  "Fehler beim Senden der Aufgabe an:$mail!";
      echo json_encode(array('status'=>0,'statusText'=>$msg));
      $this->app->erp->ExitWawi();
    }
  }

  function AufgabenBon(){
    $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE verwendenals='bondrucker' LIMIT 1");
    if($seriennummer!=""){
      echo json_encode(array('status'=>1));
      $this->app->erp->ExitWawi();
    }else{
      echo json_encode(array('status'=>0));
      $this->app->erp->ExitWawi();
    }      
  }

  function AufgabenMiniDetail($parsetarget = '')
  {
    $id = $this->app->Secure->GetGET("id");
    $tmp = $this->app->DB->SelectArr("SELECT a.aufgabe,a.beschreibung,a.sonstiges,p.abkuerzung,ap.aufgabe as teilprojekt,a.status,
        if(abgabe_bis!='0000-00-00',DATE_FORMAT(abgabe_bis,'%d.%m.%Y'),'') as abgabebis,a.stunden as dauer,if(a.zeiterfassung_abrechnung,'Ja','') as abrechnung,
        CONCAT('<a href=index.php?module=adresse&action=edit&id=',a.kunde,' target=_blank>',ku.kundennummer,' ',ku.name,'</a>') as kunde,
        CONCAT('<a href=index.php?module=adresse&action=edit&id=',a.adresse,' target=_blank>',mi.mitarbeiternummer,' ',mi.name,'</a>') as mitarbeiter  
        FROM aufgabe a LEFT JOIN adresse ku ON ku.id=a.kunde LEFT JOIN adresse mi ON mi.id=a.adresse LEFT JOIN projekt p ON p.id=a.projekt
        LEFT JOIN arbeitspaket ap ON ap.id=a.teilprojekt
        WHERE a.id='$id' LIMIT 1");
    $this->app->Tpl->Set("AUFGABE",$tmp[0]['aufgabe']);
    $this->app->Tpl->Set("BESCHREIBUNG",nl2br($tmp[0]['beschreibung']));
    $this->app->Tpl->Set("NOTIZEN",nl2br($tmp[0]['sonstiges']));
    $this->app->Tpl->Set("KUNDE",nl2br($tmp[0]['kunde']));
    $this->app->Tpl->Set("MITARBEITER",nl2br($tmp[0]['mitarbeiter']));
    $this->app->Tpl->Set("PROJEKT",$tmp[0]['abkuerzung']);
    $this->app->Tpl->Set("TEILPROJEKT",$tmp[0]['teilprojekt']);
    $this->app->Tpl->Set("ABGABEBIS",$tmp[0]['abgabebis']);
    $this->app->Tpl->Set("DAUER",$tmp[0]['dauer']);
    $this->app->Tpl->Set("ABRECHNUNG",$tmp[0]['abrechnung']);
    $this->app->Tpl->Set("STATUS",$tmp[0]['status']);
    //echo "HUHU $id";
    if($parsetarget=="")
    {
      $this->app->Tpl->Output("aufgaben_minidetail.tpl");
      $this->app->erp->ExitWawi();
    }  else {
      $this->app->Tpl->Parse($parsetarget,"aufgaben_minidetail.tpl");
    }
  }

  function AufgabenBondrucker()
  {
    $id = $this->app->Secure->GetGET("id");

    //$projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$id' LIMIT 1");
    $bondrucker = $this->app->erp->Firmendaten("aufgaben_bondrucker");
    if($bondrucker > 0)
    {
      $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE id='$bondrucker' LIMIT 1");
    } 
    $this->app->erp->AufgabeBonDrucker($id,$seriennummer);
    $this->app->ExitXentral();
  }


  function AufgabenDateien()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=aufgaben&action=edit&id=$id","Details");

    $anzahldateien = $this->app->erp->AnzahlDateien("Aufgaben",$id);
    if($anzahldateien > 0) $anzahldateien = " (".$anzahldateien.")"; else $anzahldateien="";

    $this->app->erp->MenuEintrag("index.php?module=aufgaben&action=dateien&id=$id","Dateien".$anzahldateien);

    $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
    $this->app->YUI->DateiUpload('PAGE',"Aufgaben",$id);
  }

  function AufgabenPDFExtern()
  {
    //Create a new PDF file
    $pdf= new SuperFPDF('P','mm','A4',$this->app);
    $pdf->AddPage();

    $pdf->SetFontClassic('Arial','B',11);
    //Create lines (boxes) for each ROW (Product)
    //If you don't use the following code, you don't create the lines separating each row
    $tmp = $this->app->DB->SelectArr("SELECT a.aufgabe, if(a.stunden > 0,CONCAT(a.stunden,' h'),'') as dauer, adr.name as name,
        if(a.abgabe_bis,DATE_FORMAT(a.abgabe_bis,'%d.%m.%Y'),'') as datum, a.prio, a.beschreibung FROM aufgabe a 
        LEFT JOIN adresse adr ON adr.id=a.adresse WHERE a.status!='abgeschlossen' ORDER by a.prio DESC, a.abgabe_bis, adr.name");
    // Colors, line width and bold font
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(.3);
    //$pdf->SetFontClassic('','B');
    // Header

    $header = array('Aufgabe','Dauer','Mitarbeiter','Termin','Prio');
    $w = array(85,15,40,30,20);
    $pdf->Cell($w[0],7,$header[0],1,0,'L',true);
    $pdf->Cell($w[1],7,$header[1],1,0,'L',true);
    $pdf->Cell($w[2],7,$header[2],1,0,'L',true);
    $pdf->Cell($w[3],7,$header[3],1,0,'C',true);
    $pdf->Cell($w[4],7,$header[4],1,0,'C',true);
    $pdf->Ln();
    $pdf->SetFontClassic('Arial','',10);
    // Color and font restoration

    // Data
    $fill = false;
    $ctmp = !empty($tmp)?count($tmp):0;
    for($i=0;$i<$ctmp;$i++)
    {
      if($tmp[$i]["prio"]==0) $prio="Nein";
      else if($tmp[$i]["prio"]==1) $prio="Hoch";
      else if($tmp[$i]["prio"]==2) $prio = "Mittel";
      else if($tmp[$i]["prio"]==3) $prio = "Niedrig";
      else if($tmp[$i]["prio"]==-1) $prio = "Nein";
      $pdf->Cell($w[0],6,$tmp[$i]["aufgabe"],'LRT',0,'L',$fill);
      $pdf->Cell($w[1],6,$tmp[$i]["dauer"],'LRTB',0,'L',$fill);
      $pdf->Cell($w[2],6,$tmp[$i]["name"],'LRTB',0,'L',$fill);
      $pdf->Cell($w[3],6,$tmp[$i]["datum"],'LRTB',0,'C',$fill);
      $pdf->Cell($w[4],6,$prio,'LRTB',1,'C',$fill);
      $pdf->MultiCell(0,6,$tmp[$i]['beschreibung'],'LRB','L',$fill);
      $pdf->Ln(2);
      $fill = !$fill;
    }
    $pdf->Ln();
    $pdf->SetFontClassic('Arial','',8);
    $name="ALL";

    $pdf->Cell(array_sum($w),0,date('Ymd')."_".$name."_TODO.pdf",'',0,'R');
    $pdf->Output(date('Ymd')."_".$name."_TODO.pdf",'D');
    $this->app->ExitXentral();
  }

  function AufgabenPDF()
  {
    //Create a new PDF file
    $pdf=new SuperFPDF('P','mm','A4',$this->app);
    $pdf->AddPage();

    $pdf->SetFontClassic('Arial','B',11);


    //Create lines (boxes) for each ROW (Product)
    //If you don't use the following code, you don't create the lines separating each row
    $tmp = $this->app->DB->SelectArr("SELECT aufgabe, if(stunden > 0,CONCAT(stunden,' h'),'') as dauer,
        if(abgabe_bis,DATE_FORMAT(abgabe_bis,'%d.%m.%Y'),'') as datum, prio, beschreibung FROM aufgabe 
        WHERE adresse='".$this->app->User->GetAdresse()."' AND status!='abgeschlossen' ORDER by prio DESC,abgabe_bis ");
    // Colors, line width and bold font
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(.3);
    //$pdf->SetFontClassic('','B');
    // Header

    $header = array('Aufgabe','Dauer','Termin','Prio','OK');
    $w = array(85,15,40,30,20);
    $pdf->Cell($w[0],7,$header[0],1,0,'L',true);
    $pdf->Cell($w[1],7,$header[1],1,0,'L',true);
    $pdf->Cell($w[2],7,$header[2],1,0,'C',true);
    $pdf->Cell($w[3],7,$header[3],1,0,'C',true);
    $pdf->Cell($w[4],7,$header[4],1,0,'C',true);
    $pdf->Ln();
    $pdf->SetFontClassic('Arial','',11);
    // Color and font restoration

    // Data
    $fill = false;
    $ctmp = !empty($tmp)?count($tmp):0;
    for($i=0;$i<$ctmp;$i++)
    {
      if($tmp[$i]["prio"]==0) $prio="Nein";
      else if($tmp[$i]["prio"]==1) $prio="Hoch";
      else if($tmp[$i]["prio"]==2) $prio = "Mittel";
      else if($tmp[$i]["prio"]==3) $prio = "Niedrig";
      else if($tmp[$i]["prio"]==-1) $prio = "Nein";
      $pdf->Cell($w[0],6,$tmp[$i]["aufgabe"],'LRT',0,'L',$fill);
      $pdf->Cell($w[1],6,$tmp[$i]["dauer"],'LRTB',0,'C',$fill);
      $pdf->Cell($w[2],6,$tmp[$i]["datum"],'LRTB',0,'C',$fill);
      $pdf->Cell($w[3],6,$prio,'LRTB',0,'C',$fill);
      $pdf->Cell($w[4],6,"",'LRTB',1,'C',$fill);
      $pdf->MultiCell(0,6,$tmp[$i]['beschreibung'],'LRB','L',$fill);

      $pdf->Ln(2);
      $fill = !$fill;
    }
    $pdf->Ln();
    $pdf->SetFontClassic('Arial','',8);
    $name=preg_replace("/[^a-zA-Z0-9_]/" , "" , str_replace(' ','_',$this->app->User->GetName()));
    $name = strtoupper($name);

    $pdf->Cell(array_sum($w),0,date('Ymd')."_".$name."_TODO.pdf",'',0,'R');

    $pdf->Output(date('Ymd')."_".$name."_TODO.pdf",'D');
    $this->app->ExitXentral();
  }

  function AufgabenDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->Secure->GetGET("sid");
    $referrer = $this->app->Secure->GetGET("referrer");

    $check = $this->app->DB->Select("Select initiator FROM aufgabe WHERE id='$id' LIMIT 1");

    if($check==$this->app->User->GetAdresse() || $this->app->User->GetType()=="admin")
    {
      $this->app->DB->Update("DELETE FROM aufgabe WHERE id='$id' LIMIT 1");
      $msg = base64_encode("<div class=\"error2\">Die Aufgabe wurde gel&ouml;scht!</div>");
    } else {
      $msg = base64_encode("<div class=\"error2\">Die Aufgabe darf nur vom Initiator gel&ouml;scht werden!</div>");
    }
    if($referrer=="projekt")
      header("Location: index.php?module=projekt&action=dashboard&id=$sid&msg=$msg");
    else
      header("Location: index.php?module=aufgaben&action=list&msg=$msg");
    exit;
  }


  function AufgabenAbschluss()
  {
    $id = $this->app->Secure->GetGET("id");
    $back = $this->app->Secure->GetGET("back");
    $referrer = $this->app->Secure->GetGET("referrer");
    $sid = $this->app->Secure->GetGET("sid");

    // einmalig immer weg

    // aufgaben kopieren und dann wenn intervall_tage 2 = woechen 3 monatlich 4 jaehrlich
    // alles kopieren 1:1 neue hat mit dem datum von turnus +1 tag + 7 Tage oder monatlich immer wieder dann rein

    // ab taeglich kann man nur abschliessen abgabe_bis <= heute ist
    $result = $this->app->erp->AbschlussAufgabe($id);

    //$this->app->DB->Update("UPDATE aufgabe SET status='abgeschlossen',abgeschlossen_am=NOW() WHERE id='$id' LIMIT 1");
    if($result <=0)
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Aufgabe ist eine wiederholende und liegt in der Zukunft und darf noch nicht abgeschlossen werden!</div>");
    else {
      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Aufgabe wurde abgeschlossen!</div>");

      $data = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE id='$id' LIMIT 1");
      if($data[0]['zeiterfassung_pflicht']=="1")
      { 
        $mitarbeiter = $this->app->User->GetAdresse();
        $zeit = new DateTime(); //current date/time
        $vonZeit = $zeit ->format('Y-m-d '.$data[0]['abgabe_bis_zeit']);
        $bisZeit = $zeit ->format('Y-m-d H:i:s');

        $newid = $this->app->erp->AddArbeitszeit($mitarbeiter, $vonZeit,$bisZeit, $data[0]['aufgabe'],$data[0]['beschreibung'],"",
           "", "","arbeit",$data[0]['kunde'],$data[0]['zeiterfassung_abrechnung'],"","","","",$id);   
        $this->app->DB->Update('UPDATE zeiterfassung SET projekt='.$data[0]['projekt'].',arbeitspaket='.$data[0]['teilprojekt'].', art=\'Arbeit\' WHERE id='.$newid);


        if($back=="")$back = "aufgabe";
        header("Location: index.php?module=zeiterfassung&action=create&id=$newid&back=$back");
        exit;
      }

    }

    if($referrer=="1")
      header("Location: index.php?msg=$msg");
    else if($referrer=="projekt")
      header("Location: index.php?module=projekt&action=dashboard&id=$sid&msg=$msg");
    else {
      if($back=="wochenplan")
        header("Location: index.php?module=aufgaben&action=list&cmd=wochenplan&msg=$msg");
      else
        header("Location: index.php?module=aufgaben&action=list&msg=$msg");
    }
    exit;
  }

  function AufgabenKalender()
  {
    $this->AufgabenMenu();
    $adr_id = $this->app->User->GetAdresse();
    $this->app->erp->Wochenplan($adr_id,'TAB1');
    $this->app->Tpl->Set('AKTIV_TAB1',"selected");
    $this->app->Tpl->Set('TABSADD', '');
    $this->app->Tpl->Parse('PAGE',"aufgabenkalender.tpl");
  }

  function AufgabenCreate()
  {

    //$this->app->erp->MenuEintrag("index.php?module=artikel&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=aufgaben&action=list';\">");

    parent::AufgabeCreate();

  }

  /**
   * @return JsonResponse
   */
  public function HandleGetProjectShortCodeAjax(): JsonResponse
  {
    $shortCode = $this->app->Secure->GetPOST('projectshortcode');
    $index = 0;
    while(
      !empty(
        $this->app->DB->Select(
          sprintf(
            "SELECT `id` FROM `projekt` WHERE `abkuerzung` = '%s' LIMIT 1",
            $shortCode.($index===0?'':$index)
          )
        )
      )
    ) {
      $index++;
    }

    return new JsonResponse(['projectshortcode' => $shortCode.($index===0?'':$index)]);
  }

  /**
   * @param string $shortCode
   *
   * @return int|null
   */
  public function getProjectIdByShortCode(string $shortCode): ?int
  {
    $shortCodeExploded = explode(' ', $shortCode);
    $shortCode = (string)reset($shortCodeExploded);
    $projectId = $this->app->DB->Select(
      sprintf(
        "SELECT `id` FROM `projekt` WHERE `abkuerzung` = '%s' AND `geloescht` = 0",
        $shortCode
      )
    );
    if(empty($projectId)) {
      return null;
    }

    return (int)$projectId;
  }

  /**
   * @return JsonResponse
   */
  public function HandleGetSubProjectsByProjectAjax(): JsonResponse
  {
    $optionHtml = '';
    $shortCode = explode(' ', $this->app->Secure->GetPOST('projectshortcode'));
    $shortCode = reset($shortCode);
    if(empty($shortCode)) {
      $this->app->User->SetParameter('task_project_id', 0);
      return new JsonResponse(['html' => $optionHtml]);
    }
    $projectId = $this->getProjectIdByShortCode($shortCode);
    if(empty($projectId)) {
      $this->app->User->SetParameter('task_project_id', 0);
      return new JsonResponse(['html' => $optionHtml]);
    }
    $this->app->User->SetParameter('task_project_id', $projectId);
    $subProjects = $this->app->DB->SelectPairs(
      sprintf(
        "SELECT `id`, `aufgabe` FROM `arbeitspaket` WHERE `projekt` = %d ORDER BY `sort`, `id`",
        $projectId
      )
    );
    if(empty($subProjects)) {
      return new JsonResponse(['html' => $optionHtml]);
    }
    foreach($subProjects as $subProjectId => $title) {
      $optionHtml .= sprintf('<option value="%s">%s</option>', $subProjectId,htmlspecialchars($title));
    }

    return new JsonResponse(['html' => $optionHtml]);
  }

  /**
   * @return JsonResponse
   */
  public function HandleChangeProjectEmployeeAjax(): JsonResponse
  {
    $addressId = $this->app->Secure->GetPOST('address_id');
    $value = $this->app->Secure->GetPOST('value');
    if(empty($addressId)) {
      return new JsonResponse(['status' => false, 'error' => 'no address_id']);
    }
    if(empty($value)) {
      $this->app->User->deleteParameter('task_project_create_employee_' . $addressId);
    }
    else{
      $this->app->User->SetParameter('task_project_create_employee_' . $addressId, '1');
    }

    return new JsonResponse(['status' => true]);
  }

  /**
   * @return array
   */
  public function getUserMarkedProjectEmployeeIds(): array
  {
    return $this->app->DB->SelectFirstCols(
      sprintf(
        "SELECT a.id 
        FROM `adresse` AS `a`
        INNER JOIN `userkonfiguration` AS `uk` 
         ON CONCAT('task_project_create_employee_',a.id) = uk.name AND uk.user = %d
        WHERE a.mitarbeiternummer <> '' AND a.geloescht <> 1",
        $this->app->User->GetID()
      )
    );
  }

  /**
   * @param int $projectId
   *
   * @return bool
   */
  public function hasProjectSubProjects(int $projectId): bool
  {
    if($projectId < 0) {
      return false;
    }

    return (int)$this->app->DB->Select(
      sprintf(
        'SELECT COUNT(`id`) FROM `arbeitspaket` WHERE `projekt` = %d',
        $projectId
      )
    ) > 0;
  }

  public function HandleChangeSubProjectEmployeeAjax(): JsonResponse
  {
    $addressId = $this->app->Secure->GetPOST('address_id');
    $hours = (string)$this->app->Secure->GetPOST('hours');
    $hourlyRate = (string)$this->app->Secure->GetPOST('hourlyrate');
    $title = (string)$this->app->Secure->GetPOST('title');
    if($hours === '') {
      $this->app->User->deleteParameter('task_subproject_create_employee_hours_' . $addressId);
    }
    else{
      $this->app->User->SetParameter('task_subproject_create_employee_hours_' . $addressId, $hours);
    }
    if($hourlyRate === '') {
      $this->app->User->deleteParameter('task_subproject_create_employee_hourlyrate_' . $addressId);
    }
    else{
      $this->app->User->SetParameter('task_subproject_create_employee_hourlyrate_' . $addressId, $hourlyRate);
    }
    if($title === '') {
      $this->app->User->deleteParameter('task_subproject_create_employee_title_' . $addressId);
    }
    else{
      $this->app->User->SetParameter('task_subproject_create_employee_title_' . $addressId, $title);
    }

    return new JsonResponse([]);
  }

  /**
   * @return JsonResponse
   */
  public function HandleCreateSubProjectAjax(): JsonResponse
  {
    $projectId = $this->getProjectIdByShortCode((string)$this->app->Secure->GetPOST('project'));
    $error = [];
    if($projectId === null) {
      $error[] = 'Bitte ein Projekt eingeben';
    }
    $title = (string)$this->app->Secure->GetPOST('title');
    if(empty($title)) {
      $error[] = 'Bitte eine Bezeichnung angeben';
    }
    $description = (string)$this->app->Secure->GetPOST('description');
    $status = (string)$this->app->Secure->GetPOST('status');
    $parentId = (int)$this->app->Secure->GetPOST('parentid');
    if($parentId > 0) {
      $parentId = $this->app->DB->Select(
        sprintf(
          'SELECT `id` FROM `arbeitspaket` WHERE `id` = %d AND `projekt` = %d',
          $parentId, $projectId
        )
      );
    }
    $positionType = (string)$this->app->Secure->GetPOST('positiontype');
    if(!in_array($positionType, ['postypnachbar', 'postypkind'], true)) {
      if($this->hasProjectSubProjects($parentId)){
        $error[] = 'Bitte eine Bezeichnung angeben';
      }
      else {
        $positionType = 'postypkind';
      }
    }
    $startDate = (string)$this->app->Secure->GetPOST('startdate');
    if(!empty($startDate) && strpos($startDate, '.') !== false) {
      $startDate = $this->app->String->Convert($startDate, '%1.%2.%3', '%3-%2-%1');
    }
    if($startDate === '' || $startDate === '--') {
      $startDate = '0000-00-00';
    }
    $endDate = (string)$this->app->Secure->GetPOST('enddate');
    if(!empty($endDate) && strpos($endDate, '.') !== false) {
      $endDate = $this->app->String->Convert($endDate, '%1.%2.%3', '%3-%2-%1');
    }
    if($endDate === '' || $endDate === '--') {
      $endDate = '0000-00-00';
    }
    $color = (string)$this->app->Secure->GetPOST('color');
    $leader = explode(' ', (string)$this->app->Secure->GetPOST('leader'));
    $leaderAddressId = (int)reset($leader);

    if(!empty($error)) {
      return new JsonResponse(['error' => implode('<br />', $error)]);
    }

    if($positionType === 'postypnachbar')
    {
      $neigbourId = $parentId;
      if($neigbourId) {
        $parentId = (int)$this->app->DB->Select(
          "SELECT `vorgaenger` FROM `arbeitspaket` WHERE `projekt` = '$projectId' AND `id` = '$neigbourId' LIMIT 1"
        );
        $sortNeighbour = (int)$this->app->DB->Select(
          "SELECT `sort` FROM `arbeitspaket` WHERE `projekt` = '$projectId' AND `id` = '$neigbourId' LIMIT 1"
        );
        $this->app->DB->Update(
          "UPDATE `arbeitspaket` SET `sort` = `sort` + 1 
          WHERE `vorgaenger` = '$parentId' AND `sort` > $sortNeighbour AND `projekt` = '$projectId'"
        );
        $sort = $sortNeighbour + 1;
      }
      else {
        $sort = 1;
        $parentId = 0;
      }
    }
    else{
      if($parentId > 0){
        $sort = 1 + (int)$this->app->DB->Select(
          "SELECT max(`sort`) FROM `arbeitspaket` WHERE `projekt` = '$projectId' AND `vorgaenger` = '$parentId' LIMIT 1"
          );
      }
      else{
        $sort = 1 + (int)$this->app->DB->Select(
          "SELECT max(`sort`) FROM `arbeitspaket` WHERE `projekt` = '$projectId' LIMIT 1"
          );
      }
    }

    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `arbeitspaket` 
            (`adresse`, `aufgabe`, `beschreibung`, `projekt`, `startdatum`, `abgabedatum`, `sort`,
             `farbe`, `status`,
             `vorgaenger`, `art`, `geloescht`, `aktiv`) 
             VALUES 
            (%d, '%s', '%s', %d,'%s','%s', %d, '%s', '%s', %d, 'teilprojekt',0,1) ",
        $leaderAddressId, $title, $description, $projectId, $startDate, $endDate, $sort, $color, $status, $parentId
      )
    );
    $subProjectId = (int)$this->app->DB->GetInsertID();
    $employeeDataSets = $this->app->DB->SelectArr("SELECT  a.id,uktitle.value AS `title`,
       ukhours.value AS `hours`,
       ukhourlyrate.value AS `hourlyrate`
            FROM `adresse` AS `a` 
            INNER JOIN `adresse_rolle` AS `a2` ON a2.adresse=a.id 
            INNER JOIN `projekt` AS `p` ON p.id=a2.projekt OR p.id = a.projekt 
                  AND p.id = '".$projectId."'
            LEFT JOIN `userkonfiguration` AS `uktitle` 
            ON CONCAT('task_subproject_create_employee_title_',a.id) = uktitle.name 
            AND uktitle.user = '".$this->app->User->GetID()."'
            LEFT JOIN `userkonfiguration` AS `ukhours` 
            ON CONCAT('task_subproject_create_employee_hours_',a.id) = ukhours.name 
            AND ukhours.user = '".$this->app->User->GetID()."'
            LEFT JOIN `userkonfiguration` AS `ukhourlyrate` 
            ON CONCAT('task_subproject_create_employee_hourlyrate_',a.id) = ukhourlyrate.name 
            AND ukhours.user = '".$this->app->User->GetID()."'"
    );
    if(!empty($employeeDataSets)) {
      foreach($employeeDataSets as $employeeDataSet) {
        if(!empty($employeeDataSet['title'])
          || !empty($employeeDataSet['hourlyrate'])
          || !empty($employeeDataSet['hours'])
        ) {
          $this->app->DB->Insert(
            sprintf(
              "INSERT INTO `teilprojekt_geplante_zeiten` 
                (`projekt`, `teilprojekt`, `adresse`, `bezeichnung`, `stundensatz`, `stunden`) 
                VALUES (%d, %d, %d, '%s', %f, %f)",
              $projectId, $subProjectId, $employeeDataSet['id'],
              $employeeDataSet['title'],
              $employeeDataSet['hourlyrate'],
              $employeeDataSet['hours']
            )
          );
          $this->app->User->deleteParameterPrefix('task_subproject_create_employee_title_'.$employeeDataSet['id']);
          $this->app->User->deleteParameterPrefix('task_subproject_create_employee_hours_'.$employeeDataSet['id']);
          $this->app->User->deleteParameterPrefix('task_subproject_create_employee_hourlyrate_'.$employeeDataSet['id']);
        }
      }
    }

    return new JsonResponse(['id' => $subProjectId]);
  }

  /**
   * @return JsonResponse
   */
  public function HandleCreateProjectAjax(): JsonResponse
  {
    /** @var Projekt $projectObj */
    $projectObj = $this->app->loadModule('projekt');
    $error = $projectObj->getProjectCreateErrorMessagesByForm();
    $employeeIds = $this->getUserMarkedProjectEmployeeIds();
    if(empty($employeeIds)) {
      $error[] = 'Bitte markiere mindestens ein Mitarbeiter';
    }
    if($error) {
      return new JsonResponse(['error' => implode('<br />', $error)]);
    }
    $abkuerzung =  $this->app->Secure->GetPOST('abkuerzung');
    $typ = $this->app->Secure->GetPOST('typ');
    $customerId = (int)$this->app->Secure->GetPOST('kunde');
    if($typ === 'kundennummer') {
      $customerInfo = $projectObj->getNextProjectShortCutByCustomer($customerId);
      $abkuerzung = $customerInfo['shortcode'];
    }
    elseif($typ === 'fortlaufend') {
      $abkuerzung = $this->app->erp->GetNextNummer('projektnummer');
    }
    $projectId = $projectObj->finishCreateProject($abkuerzung, 0);

    if($projectId <= 0) {
      return new JsonResponse(
        ['error' => 'Es gab einen Fehler. Das Projekt konnte nicht angelegt werden.']
      );
    }

    if(!empty($employeeIds)) {
      foreach($employeeIds as $employeeId) {
        $this->app->DB->Insert(
          sprintf(
            "INSERT INTO `adresse_rolle` 
            (`adresse`, `subjekt`, `praedikat`, `objekt`, `parameter`, `von`, `projekt`)
            VALUES (%d, 'Mitglied', 'von', 'Projekt', '%d', NOW(), '%d')",
            $employeeId, $projectId, $projectId
          )
        );
        $this->app->User->deleteParameterPrefix('task_project_create_employee_'.$employeeId);
      }
    }

    return new JsonResponse(['id' => $projectId]);
  }

  function AufgabenList()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'getprojectshortcode') {
      return $this->HandleGetProjectShortCodeAjax();
    }
    if($cmd === 'changeemployee') {
      return $this->HandleChangeProjectEmployeeAjax();
    }
    if($cmd === 'getsubprojectsbyproject') {
      return $this->HandleGetSubProjectsByProjectAjax();
    }
    if($cmd === 'createproject') {
      return $this->HandleCreateProjectAjax();
    }
    if($cmd === 'createsubproject') {
      return $this->HandleCreateSubProjectAjax();
    }
    if($cmd === 'changesubprojectemployee') {
      return $this->HandleChangeSubProjectEmployeeAjax();
    }

    $this->app->YUI->DateiPopup('DATEIENPOPUP','aufgaben','#e_id',array(
        'openbuttontarget'=>'DATEIBUTTON'
    ,'frompopup'=>'editAufgaben','afteropen'=>'AFTERPOPUPOPEN'
    ));


    $this->AufgabenMenu();

    if($this->app->erp->RechteVorhanden("aufgaben","pdfextern"))
    {
      $this->app->Tpl->Set('EXTERNELISTE','<a href="index.php?module=aufgaben&action=pdfextern">{|Externe Aufgaben-Liste als PDF|}</a>');
    }

    /* offene Aufgabens */
    $adresse = $this->app->User->GetAdresse();
    $options = '';
    if($this->app->User->GetType()=="admin" || $this->app->erp->RechteVorhanden("aufgaben","alle"))
    {
      $mitarbeiter_arr = $this->app->erp->GetMitarbeiter();
      $sid = $this->app->Secure->GetGET("sid");
      $cmd = $this->app->Secure->GetGET("cmd");

      if($this->app->User->GetType()=="admin" || $this->app->erp->RechteVorhanden("aufgaben","alle"))  
        $options .= "<option value=\"0\">Alle</option>";

      if($sid=="") $sid = $this->app->User->GetParameter("aufgabe_benutzer_simulieren");

      if($sid!="") {
        $adresse = $sid;
        $this->app->Tpl->Set('MITARBEITER',$sid);
        $this->app->User->SetParameter("aufgabe_benutzer_simulieren",$sid);
      }
      $cmitarbeiter_arr = !empty($mitarbeiter_arr)?count($mitarbeiter_arr):0;
      for($mi=0;$mi<$cmitarbeiter_arr;$mi++)
      {
        if($adresse==$mitarbeiter_arr[$mi]['id']) {
	  $selected = "selected"; 
	} else {
	  $selected="";
	}
        $options .= "<option value=\"".$mitarbeiter_arr[$mi]['id']."\" $selected>".$mitarbeiter_arr[$mi]['name']."</option>";
      }

      if($this->app->User->GetParameter("aufgabe_benutzer_simulieren") > 0 && $this->app->User->GetParameter("aufgabe_benutzer_simulieren")!=$this->app->User->GetAdresse())
      {
	$name = $this->app->DB->Select("SELECT CONCAT(mitarbeiternummer,' ',name) FROM adresse WHERE id='".$this->app->User->GetParameter("aufgabe_benutzer_simulieren")."' LIMIT 1");
	$color = "#E5F5D2";
	$this->app->Tpl->Set('KURZUEBERSCHRIFT2',$name);
      } else {

	$name = $this->app->DB->Select("SELECT CONCAT(mitarbeiternummer,' ',name) FROM adresse WHERE id='".$this->app->User->GetAdresse()."' LIMIT 1");
	$this->app->Tpl->Set('KURZUEBERSCHRIFT2',$name);
      }
      $this->app->Tpl->Set('RTABSELECT',"
      {|Ansicht Mitarbeiter|}:&nbsp;<select style=\"background-color:$color;\" name=\"mitarbeiter\" id=\"mitarbeiter\" onchange=\"window.location.href='index.php?module=aufgaben&action=list&cmd=$cmd&sid=' + this.value\">".$options."</select>");
    }

    switch($cmd) {
      case 'wochenplan':
        $this->app->Tpl->Add('ANZEIGE',$this->AufgabenWochenplan($adresse));
        if($this->app->User->GetType()=="admin" || $this->app->erp->RechteVorhanden("aufgaben","alle"))
        {
          $this->app->Tpl->Add('ANZEIGE',$this->AufgabenWochenplan($adresse,1));
          $this->app->Tpl->Add('ANZEIGE',$this->AufgabenWochenplan($adresse,2));
        }
        $this->app->Tpl->Set('AKTIVWOCHENPLAN',"aktiv");
        break;
      case 'kalender':
        $this->app->Tpl->Set('AKTIVKALENDER',"aktiv");
        $this->app->Tpl->Set("AUFGABE_KALENDER_FILTER_KUNDE", $this->app->User->GetParameter("aufgabe_kalender_filter_kunde"));
        $this->app->Tpl->Parse('ANZEIGE',"aufgaben_kalender.tpl");
        break;
      case 'projects':
        $this->app->Tpl->Set('ACTIVEPROJECTS', 'aktiv');
        break;
      case 'eingang':
        $this->app->Tpl->Set('AKTIVEINGANG',"aktiv");
        break;
      default:
        $this->app->Tpl->Set('AKTIVAUFGABENLISTE',"aktiv");
        $this->app->Tpl->Parse('ANZEIGE',"aufgabenuebersicht_filtermeine.tpl");
        $this->app->YUI->TableSearch('ANZEIGE','aufgaben_meine', 'show','','',basename(__FILE__), __CLASS__);
        break;

    }
    //$this->app->YUI->TableSearch('WIEDERHOLENDE',"aufgabenwdh");
    //$this->app->YUI->TableSearch('AUFGABENARCHIV',"aufgaben_archiv");

    $pinnwaende = $this->app->erp->GetPinwandSelect();
    $pinnwand = "";
    foreach($pinnwaende as $key=>$value){
      $pinnwand .= "<option value='$key'>".$value."</option>";
    }

    $this->app->Tpl->Set("PINNWAND", $pinnwand);

    $this->app->YUI->AutoSaveUserParameter("aufgabe_kalender_filter_kunde","aufgabe_kalender_filter_kunde");
    $this->app->YUI->AutoComplete("aufgabe_kalender_filter_kunde", "adresse");


    $this->app->YUI->AutoComplete("e_mitarbeiter", "mitarbeiteraktuell");
    $this->app->YUI->AutoComplete("e_kunde", "adresse");
    //$this->app->YUI->AutoComplete("e_projekt", "projektname");
    //$this->app->YUI->AutoComplete("e_teilprojekt", "arbeitspaket");
    $this->app->YUI->CkEditor("e_notizen","belege",array("width"=>"625"));
    $this->app->YUI->CkEditor("e_beschreibung","belege",array("width"=>"420"));
    $this->app->YUI->DatePicker("e_datum");
    $this->app->YUI->TimePicker("e_zeit");
    $this->app->Tpl->Set('TABSADD', '');
    if(!$this->app->erp->RechteVorhanden('projekt', 'dashboard')) {
      $this->app->Tpl->Set('BEFOREPROJECTDASHBOARD', '<!--');
      $this->app->Tpl->Set('AFTERPROJECTDASHBOARD', '-->');
    }
    else {
      $this->app->YUI->AutoComplete('subprojectleader', 'adresse');
      $this->app->YUI->AutoComplete('subprojectproject', 'projektname');
      $this->app->YUI->ColorPicker('subprojectcolor');
      $this->app->YUI->CkEditor('subprojectdescription', 'belege');
      $this->app->YUI->DatePicker('subprojectstartdate');
      $this->app->YUI->DatePicker('subprojectenddate');
      $this->app->YUI->TableSearch(
        'SUBPROJECTEMPLOYETABLE', 'task_subproject_create_employee', 'show', '', '', basename(__FILE__), __CLASS__
      );
    }
    if(!$this->app->erp->RechteVorhanden('projekt', 'create')) {
      $this->app->Tpl->Set('BEFOREPROJECTCREATE', '<!--');
      $this->app->Tpl->Set('AFTERPROJECTCREATE', '-->');
    }
    else {
      /** @var Projekt $projectModule */
      $projectModule = $this->app->loadModule('projekt');
      $this->app->Tpl->Add('STATUSSEL', $projectModule->getProjectStatusSelect());
      $this->app->YUI->AutoComplete('projectcustomer', 'adresse');
      $this->app->YUI->AutoComplete('projectleader', 'adresse');
      $this->app->YUI->ColorPicker('projectcolor');
      $this->app->YUI->CkEditor('projectdescription', 'belege');
      $this->app->YUI->TableSearch(
        'EMPLOYETABLE', 'task_project_create_employee', 'show', '', '', basename(__FILE__), __CLASS__
      );
      $this->app->Tpl->Parse('CREATEPROJECTFIELDSET', 'aufgaben_create_project.tpl');
    }
    $this->app->Tpl->Parse('AUFGABENPOPUP','aufgaben_popup.tpl');
    $this->app->Tpl->Parse('PAGE', "aufgabenuebersicht.tpl");
  }

  function AufgabenWochenplan($adresse=0,$moveweek=0)//,$jahr="",$woche="")
  {
      $jahr = '';
      $woche = '';
      if($jahr=="") $jahr=date('Y');
      if($woche=="")$woche=date('W');

      if($moveweek > 0)
      {
        $jahr = date('Y',strtotime("+$moveweek week"));
        $woche = date('W',strtotime("+$moveweek week"));
      } else if ($moveweek <0)
      {
        $jahr = date('Y',strtotime("-$moveweek week"));
        $woche = date('W',strtotime("-$moveweek week"));
      }

      $tag[0] = "So";
      $tag[1] = "Mo";
      $tag[2] = "Di";
      $tag[3] = "Mi";
      $tag[4] = "Do";
      $tag[5] = "Fr";
      $tag[6] = "Sa";


      $tpl  = '
      <center>Jahr '.$jahr.' KW '.$woche.'</center><table class="mkTable" width="100%">';

     $timestamp_montag = strtotime("{$jahr}-W{$woche}");
     $tpl1 = '';
     $tpl2 = '';
     $tpl3 = '';
      for($i=0;$i<7;$i++)
      {
        $datum = date("Y-m-d", $timestamp_montag) ;
        $timestamp_montag += 3600*24;

        $erste_zeiterfassung = $this->app->DB->Select("SELECT DATE_FORMAT(MIN(von),'%H:%i') FROM zeiterfassung WHERE adresse='$adresse' AND DATE_FORMAT(von,'%Y-%m-%d')='$datum'");
        $letzte_zeiterfassung = $this->app->DB->Select("SELECT DATE_FORMAT(MAX(bis),'%H:%i') FROM zeiterfassung WHERE adresse='$adresse' AND DATE_FORMAT(von,'%Y-%m-%d')='$datum'");

        // alle aufgaben von dem Tag
        if($adresse==0)
        {
          $tmp = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE DATE_FORMAT(abgabe_bis,'%Y-%m-%d')='$datum' ORDER by sort,abgabe_bis,id");
        }
        else
        {
          $tmp = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE DATE_FORMAT(abgabe_bis,'%Y-%m-%d')='$datum' AND adresse='".$adresse."' ORDER by sort,abgabe_bis,id");
          $summe_dauer = $this->app->DB->Select("SELECT SUM(stunden) FROM aufgabe WHERE DATE_FORMAT(abgabe_bis,'%Y-%m-%d')='$datum' AND adresse='".$adresse."'");
          $summe_dauer_abrechnen = $this->app->DB->Select("SELECT SUM(stunden) FROM aufgabe WHERE DATE_FORMAT(abgabe_bis,'%Y-%m-%d')='$datum' 
            AND adresse='".$adresse."' AND zeiterfasung_abrechnung=1");
          if($summe_dauer<=0) $summe_dauer=0;
          if($summe_dauer_abrechnen<=0) $summe_dauer_abrechnen=0;
        }
        $tmp_td = '';
        $tmp_td .= '<ul style="min-height: 30px; padding: 0; margin: 0; list-style: none;" class="drag_drop_list">';
        $ctmp = !empty($tmp)?count($tmp):0;
        for($tmpi=0;$tmpi<$ctmp;$tmpi++)
        {
          $kunde = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$tmp[$tmpi]['kunde']."' LIMIT 1");

          if($kunde != "") {
            $kunde = $kunde."<br>";
          }

          if($tmp[$tmpi]['stunden'] > 0) {
            $dauer = "<i style=\"color:grey\"><br>".$kunde."Geplant: ".$tmp[$tmpi]['stunden']." (h)</i>";
          } else {
            $dauer="";
          }

          $style = array();
          $style[] = 'font-size: 8pt;';
          $style[] = 'border: 2px solid #E0E0E0;';
          $style[] = 'cursor: pointer;';
          $style[] = 'position: absolute;';

          if($tmp[$tmpi]['status']=="abgeschlossen") {
            // $background_aufgabe = "#f0f0f0";
            // $color_aufgabe = "#aaa";
            $gedauert = $this->app->erp->ZeitGesamtAufgabe($tmp[$tmpi]['id']);
            if($gedauert > 0) $gedauert = "<br><i>Gebucht: ".number_format($gedauert,2)." (h)</i>"; else $gedauert="";
            $dauer .=$gedauert;

            $style[] = 'color: #AAA;';
            $style[] = 'background: #F0F0F0;';
          } else {
            // $background_aufgabe = "#D5ECF2";
            // $color_aufgabe = "black";
            $style[] = 'color: #000;';
            $style[] = 'background: #D5ECF2;';
          }

          $tmp_td .= '<li class="drag_drop_aufgabe drag_drop_relative" data-id="' . $tmp[$tmpi]['id'] . '" style="' . implode(' ', $style) . '">';
            $tmp_td .= '<table style="border:0;background:none; width:100%;">';
              $tmp_td .= '<tr>';
                $tmp_td .= '<td valign="top" style="border:0;background:none;">';
                  $tmp_td .= '<a style="" href="index.php?module=aufgaben&action=edit&id=' . $tmp[$tmpi]['id'] . '&back=wochenplan#tabs-3"><img src="themes/new/images/edit.svg" height="20"></a>';
              $tmp_td .= '</td>';
              $tmp_td .= '<td valign="middle" style="border:0;background:none;">';
                $tmp_td .= '<span style="display: inline-block;"> ' . $tmp[$tmpi]['aufgabe'].$dauer . '</span>';
              $tmp_td .= '</td>';
            $tmp_td .= '</tr>';
          $tmp_td .= '</table>';
        $tmp_td .= '<li>';

        // $tmp_td .= "<li class=\"drag_drop_aufgabe\" data-id=\"" . $tmp[$tmpi]['id'] . "\" style=\"background-color:$background_aufgabe;border: 1px solid #E0E0E0;cursor:pointer;\" ><a onclick=\"window.location.href='index.php?module=aufgaben&action=edit&id=".$tmp[$tmpi]['id']."&back=wochenplan#tabs-3'\"><img src=\"themes/new/images/edit.svg\" height=20></a> ".$tmp[$tmpi]['aufgabe'].$dauer."</li>";
      }

      $tmp_td .= "</ul>";
      if(date('Y-m-d')==$datum) {
        $background="style=\"background-color:white; color:red; border: 1px solid #E0E0E0;\""; 
      } else {
        $background="";
      }

      $tpl1 .= "<th width=\"14%\" $background>".$tag[date('w',strtotime($datum))].". ".date('d.m',strtotime($datum))."</th>";
      if($this->app->User->GetType()=="admin" || $this->app->erp->RechteVorhanden("aufgaben","alle"))
      {
        if($erste_zeiterfassung!="") { $strich = "-";  $arbeit="A:";}
	else { $strich=""; $arbeit="";}
        $tpl2 .= "
      <td>
      <table cellpadding=\"0\" cellspacing=\"0\" style=\"padding:0px;font-size:8pt;width:100%\">
      <tr><td style=\"padding:0px\">Geplant:</td><td style=\"padding:0px\">".$this->app->erp->ZeitInStundenMinuten($summe_dauer)."</td></tr>
      <tr><td style=\"padding:0px\">Mitarbeiter Soll:</td><td style=\"padding:0px\">".$this->app->erp->ZeitInStundenMinuten($this->app->erp->ZeitSollDatumArbeit($adresse,$datum))."</td></tr>
      <tr><td style=\"padding:0px\">Abrechnen Soll:</td><td style=\"padding:0px\">".$this->app->erp->ZeitInStundenMinuten($summe_dauer_abrechnen)."</td></tr>
      <tr><td style=\"padding:0px\">Mitarbeiter Ist:</td><td style=\"padding:0px\">".$this->app->erp->ZeitInStundenMinuten($this->app->erp->ZeitGesamtDatumArbeit($adresse,$datum))."</td></tr>
      <!--<tr><td style=\"padding:0px\">Mitarbeiter Pause:</td><td style=\"padding:0px\">".$this->app->erp->ZeitInStundenMinuten($this->app->erp->ZeitGesamtDatumPause($adresse,$datum))."</td></tr>-->
      <tr><td style=\"padding:0px\">Abrechnen Ist: </td><td style=\"padding:0px\">".$this->app->erp->ZeitInStundenMinuten($this->app->erp->ZeitGesamtDatumArbeitAbrechnen($adresse,$datum))."</td></tr>
      </table>
<!--	  <i style=\"padding:0px;font-size:8pt;\">$arbeit $erste_zeiterfassung $strich $letzte_zeiterfassung</i>-->
        </td>";
      } else {
        $tpl2 .= "<td><table cellpadding=\"0\" cellspacing=\"0\" style=\"padding:0px;font-size:8pt;width:100%\">
          <tr style=\"font-size:8pt;\"><td style=\"padding:0px\">Arbeitsstunden:</td><td style=\"padding:0px\">".
          number_format($this->app->erp->ZeitSollDatumArbeit($adresse,$datum),2)."</td></tr>
          <tr style=\"font-size:8pt;\"><td style=\"padding:0px\">Zeiterfassung:</td><td style=\"padding:0px\">".
          number_format($this->app->erp->ZeitGesamtDatumArbeit($adresse,$datum),2)."</td></tr></table></td>";

      }
      $tpl3 .= '<td style="min-height:60px" data-datum="' . $datum . '">' . $tmp_td . '</td>';
    }

    

    $tpl .='<tr valign="top">'.$tpl1.'</tr>';
    $tpl .='<tr valign="top">'.$tpl2.'</tr>';
    $tpl .='<tr valign="top" class="drag_drop_datum">'.$tpl3.'</tr>';
    $tpl .='</table><br><br>';

    return $tpl;
  }

  /**
   * @param int $taskId
   *
   * @return array
   */
  public function getTimeLineElementsForTaskId(int $taskId): array
  {
    $timeLines = (array)$this->app->DB->SelectArr(
      sprintf(
        "SELECT tt.*, a.name as `username` 
        FROM `task_timeline` AS `tt` 
        LEFT JOIN `adresse` AS `a` ON a.id = tt.address_id 
        WHERE tt.task_id = %d 
        ORDER BY tt.time DESC",
        $taskId
      )
    );

    return array_merge(
      [[
        'id' => 0,
        'task_id' => $taskId,
        'address_id' => $this->app->User->GetAdresse(),
        'username' => $this->app->User->GetName(),
        'time' => date('Y-m-d H:i:s'),
        'content' => '',
      ]],
      $timeLines
    );
  }

  /**
   * @return JsonResponse
   */
  public function HandleTaskGetAction(): JsonResponse
  {
    $id = (int)$this->app->Secure->GetPOST('id');

    $data = $this->app->DB->SelectRow(
      sprintf(
        "SELECT auf.id, auf.aufgabe, auf.adresse, auf.kunde, auf.ansprechpartner_id AS `ansprechpartner`, 
           auf.beschreibung, auf.projekt, auf.teilprojekt, auf.prio, auf.stunden, auf.abgabe_bis, auf.abgabe_bis_zeit, 
           auf.intervall_tage, auf.zeiterfassung_pflicht, auf.zeiterfassung_abrechnung, auf.emailerinnerung, 
           auf.emailerinnerung_tage, auf.vorankuendigung, auf.oeffentlich, auf.startseite, auf.pinwand, 
           auf.note_color, auf.pinwand_id, auf.status, auf.sonstiges 
        FROM `aufgabe` AS `auf` 
        WHERE auf.id = %d 
        LIMIT 1",
        $id
      )
    );
    if(!empty($data)){
      $data['timeline'] = $this->getTimeLineElementsForTaskId($id);
      if($data['prio'] != '-1' && $data['prio'] != 1){
        $data['prio'] = 0;
      }

      if($data['adresse'] != "" && $data['adresse'] > 0){
        $employee = $this->app->DB->SelectRow(
          sprintf(
            "SELECT `mitarbeiternummer`, `name` FROM `adresse` WHERE `id` = %d LIMIT 1",
            $data['adresse']
          )
        );
        $mitarbeiternummer = $employee['mitarbeiternummer'];
        $mitarbeitername = $employee['name'];
        if($mitarbeiternummer != '' && $mitarbeitername != ''){
          $data['adresse'] = $mitarbeiternummer.' '.$mitarbeitername;
        }else{
          $data['adresse'] = '';
        }
      }
      if($data['kunde'] != '' && $data['kunde'] > 0){
        $customer = $this->app->DB->SelectRow(
          sprintf(
            'SELECT `id`, `name`, `kundennummer`, `lieferantennummer` FROM `adresse` WHERE `id` = %d LIMIT 1',
            $data['kunde']
          )
        );
        $kundenid = 0;
        if(!empty($customer)){
          $kundenid = $customer['id'];
          $kundenname = $customer['name'];
          if($kundenname != ''){
            $kundennr = $customer['kundennummer'];
            $lieferantennr = $customer['lieferantennummer'];

            $restadresse = '';
            $restadresse .= '(Kdr: '.$kundennr.' ';

            if($lieferantennr != ''){
              $restadresse .= 'Liefr: '.$lieferantennr;
            }

            $restadresse .= ')';

            $data['kunde'] = $kundenid.' '.$kundenname.' '.$restadresse;

            if($this->app->erp->RechteVorhanden('adresse', 'edit')) {
              $data['kundenbutton'] = 1;
              $data['kundenid'] = $kundenid;
            }
            else {
              $data['kundenbutton'] = 0;
              $data['kundenid'] = '';
            }
          }
        }
      }else{
        $data['kunde'] = '';
      }

      if($data['ansprechpartner'] != '' && $data['ansprechpartner'] > 0){
        $ansprechpartner = $this->app->DB->SelectRow(
          sprintf(
            "SELECT `id`, `name` FROM `ansprechpartner` WHERE `id` = %d LIMIT 1",
            $data['ansprechpartner']
          )
        );

        if(!empty($ansprechpartner)){
          $ansprechpartnerId = $ansprechpartner['id'];
          $ansprechpartnerName = $ansprechpartner['name'];
          $ansprechpartner = $ansprechpartnerId.' '.$ansprechpartnerName;
          $ansprechpartner .= ' ('.$kundenname.', Kdr: '.$kundennr.' ';
          if($lieferantennr != ''){
            $ansprechpartner .= 'Liefr: '.$lieferantennr;
          }
          $ansprechpartner .= ')';

          if($this->app->erp->RechteVorhanden('adresse', 'ansprechpartner') && $kundenid > 0) {
            $data['ansprechpartnerbutton'] = 1;
            $data['ansprechpartnerid'] = $ansprechpartnerId;
          }else{
            $data['ansprechpartnerbutton'] = 0;
            $data['ansprechpartnerid'] = '';
          }
        }
      }
      else{
        $data['ansprechpartner'] = '';
      }

      if($data['projekt'] == 0 || $data['projekt'] == '') {
        $data['projekt'] = '';
      }
      else{
        if($data['projekt'] > 0){
          $project = $this->app->DB->SelectRow(
            sprintf('SELECT `abkuerzung`, `name` FROM `projekt` WHERE `id` = %d ', $data['projekt'])
          );
          $abkuerzung = '';
          $name = '';
          if(!empty($project)) {
            $abkuerzung = $project['abkuerzung'];
            $name = $project['name'];
          }
          if($abkuerzung != '' && $name != ''){
            $projektid = $data['projekt'];
            $this->app->User->SetParameter('teilprojekt_filter',$projektid);
            $data['projekt'] = $abkuerzung.' '.$name;
            if($this->app->erp->RechteVorhanden('projekt', 'dashboard')) {
              $data['projektbutton'] = 1;
              $data['projektid'] = $projektid;
            }else{
              $data['projektbutton'] = 0;
              $data['projektid'] = '';
            }
          }
        }
      }
      if($data['teilprojekt'] == 0 || $data['teilprojekt'] == ''){
        $data['teilprojekt'] = '';
      }else{
        if($data['teilprojekt'] > 0){
          $subProject = $this->app->DB->SelectRow(
            sprintf(
              "SELECT p.abkuerzung, a.id, a.aufgabe
              FROM `projekt` AS `p` 
              LEFT JOIN `arbeitspaket` AS `a` ON p.id = a.projekt 
              WHERE a.id = %d
              LIMIT 1",
              $data['teilprojekt']
            )
          );
          $teilprojektid = 0;
          $teilprojektprojekt = '';
          $teilprojektaufgabe = '';
          if(!empty($subProject)) {
            $teilprojektid = $subProject['id'];
            $teilprojektprojekt = $subProject['abkuerzung'];
            $teilprojektaufgabe = $subProject['aufgabe'];
          }
          if($teilprojektid != "" && $teilprojektprojekt != "" && $teilprojektaufgabe != ""){
            $data['teilprojekt'] = $teilprojektid." ".$teilprojektprojekt." ".$teilprojektaufgabe;
          }
        }
      }
      if($data['stunden'] == 0 || is_null($data['stunden'])){
        $data['stunden'] = '';
      }else{
        $data['stunden'] = str_replace('.', ',', $data['stunden']);
      }
      if($data['abgabe_bis'] == '0000-00-00' || $data['abgabe_bis'] == "" || is_null($data['abgabe_bis'])){
        $data['abgabe_bis'] = '';
      }else{
        $data['abgabe_bis'] = date('d.m.Y',strtotime($data['abgabe_bis']));
      }
      if($data['abgabe_bis_zeit'] === '00:00:00' || $data['abgabe_bis_zeit'] == '' || is_null($data['abgabe_bis_zeit'])){
        $data['abgabe_bis_zeit'] = '';
      }else{
        $data['abgabe_bis_zeit'] = date('H:i',strtotime($data['abgabe_bis_zeit']));
      }
      if($data['vorankuendigung'] == 0 || is_null($data['vorankuendigung'])){
        $data['vorankuendigung'] = '';
      }
      if($data['emailerinnerung_tage'] == 0 || is_null($data['emailerinnerung_tage'])){
        $data['emailerinnerung_tage'] = '';
      }

      $data['abgeschlossentext'] = $this->app->User->GetParameter('aufgaben_abgeschlossen_mail');

      $data['ansprechpartner'] = $ansprechpartner;

      return new JsonResponse($data);
    }
    $data = [];
    $data['id'] = 0;
    $data['aufgabe'] = '';
    $data['adresse'] = '';
    $data['kunde'] = '';
    $data['ansprechpartner'] = '';
    $data['beschreibung'] = '';
    $data['projekt'] = '';
    $data['teilprojekt'] = '';
    $data['prio'] = 0;
    $data['stunden'] = '';
    $data['abgabe_bis'] = '';
    $data['abgabe_bis_zeit'] = '';
    $data['intervall_tage'] = 0;
    $data['zeiterfassung_pflicht'] = '';
    $data['zeiterfassung_abrechnung'] = '';
    $data['emailerinnerung'] = '';
    $data['emailerinnerung_tage'] = '';
    $data['vorankuendigung'] = '';
    $data['oeffentlich'] = '';
    $data['startseite'] = '';
    $data['pinwand'] = '';
    $data['note_color'] = 'yellow';
    $data['pinwand_id'] = 0;
    $data['status'] = 'offen';
    $data['sonstiges'] = '';
    $data['kundenbutton'] = 0;
    $data['ansprechpartnerbutton'] = 0;
    $data['projektbutton'] = 0;
    $data['kundenid'] = '';
    $data['projektid'] = '';
    $data['abgeschlossentext'] = '';
    $data['timeline'] = $this->getTimeLineElementsForTaskId($id);

    return new JsonResponse($data);
  }

  /**
   * @return JsonResponse
   */
  public function HandleTaskSaveAction(): JsonResponse
  {
    $sid = trim($this->app->Secure->GetPOST("id"));
    $aufgabe = trim($this->app->Secure->GetPOST("aufgabe"));
    $mitarbeiter = trim($this->app->Secure->GetPOST("mitarbeiter"));
    $kunde = trim($this->app->Secure->GetPOST("kunde"));
    $ansprechpartner = trim($this->app->Secure->GetPOST("ansprechpartner"));
    $beschreibung = trim($this->app->Secure->GetPOST("beschreibung"));
    $projekt = trim($this->app->Secure->GetPOST("projekt"));
    $teilprojekt = trim($this->app->Secure->GetPOST("teilprojekt"));
    $prio = trim($this->app->Secure->GetPOST("prio"));
    $dauer = trim($this->app->Secure->GetPOST("dauer"));
    $datum = trim($this->app->Secure->GetPOST("datum"));
    $zeit = trim($this->app->Secure->GetPOST("zeit"));
    $intervall_tage = trim($this->app->Secure->getPOST("intervall"));
    $pflicht = trim($this->app->Secure->GetPOST("pflicht"));
    $abgerechnet = trim($this->app->Secure->GetPOST("abgerechnet"));
    $mailerinnerung = trim($this->app->Secure->GetPOST("mailerinnerung"));
    $anzahltage = trim($this->app->Secure->GetPOST("anzahltage"));
    $countdown = trim($this->app->Secure->GetPOST("countdown"));
    $oeffentlich = trim($this->app->Secure->GetPOST("oeffentlich"));
    $startseite = trim($this->app->Secure->GetPOST("startseite"));
    $aufpinwand = trim($this->app->Secure->GetPOST("aufpinwand"));
    $farbe = trim($this->app->Secure->GetPOST("farbe"));
    $pinwand = trim($this->app->Secure->GetPOST("pinwand"));
    $status = trim($this->app->Secure->GetPOST("status"));
    $notizen = trim($this->app->Secure->GetPOST("notizen"));
    $abgeschlossentext = trim($this->app->Secure->GetPOST("abgeschlossentext"));
    $msg = '';
    $error = "";

    if($mitarbeiter != ""){
      $mitarbeiter = explode(" ", $mitarbeiter);
      $mitarbeiternummer = $mitarbeiter[0];
      $mitarbeiterid = $this->app->DB->Select("SELECT `id` FROM `adresse` WHERE `mitarbeiternummer` = '$mitarbeiternummer' AND `geloescht` = 0 LIMIT 1");
      if($mitarbeiterid != "" && $mitarbeiterid > 0){
        if($sid != "" && $sid > 0){
          $altemitarbeiterid = $this->app->DB->Select("SELECT `adresse` FROM `aufgabe` WHERE `id` = '$sid' LIMIT 1");
        }
      }else{
        $error .= "Bitte gültigen Mitarbeiter ausfüllen"."\n";
      }
    }else{
      //pruefe ob mitarbeiter gesetzt ist wenn nicht sich selbst setzen //altes bisheriges verhalten
      $mitarbeiterid = $this->app->User->GetAdresse();
    }

    if($kunde != ""){
      $kunde = explode(" ", $kunde);
      $kundenid = $kunde[0];
      $kundenid = $this->app->DB->Select("SELECT `id` FROM `adresse` WHERE `id` = '$kundenid' AND `geloescht` = 0 LIMIT 1");
      if($kundenid != "" && $kundenid > 0){
      }else{
        $error .= "Bitte gültigen Kunden ausfüllen"."\n";
      }
    }

    if($ansprechpartner != ""){
      $ansprechpartner = explode(" ", $ansprechpartner);
      $ansprechpartnerId = $ansprechpartner[0];
      $ansprechpartnerId = $this->app->DB->Select("SELECT `id` FROM `ansprechpartner` WHERE `id` = '$ansprechpartnerId' LIMIT 1");
      if($ansprechpartnerId === '' || $ansprechpartnerId <= 0){
        $error .= "Bitte gültigen Ansprechpartner ausfüllen"."\n";
      }
    }else{
      $ansprechpartnerId = 0;
    }

    if($projekt != ""){
      $projekt = explode(" ", $projekt);
      $projekt = $projekt[0];
      $projektid = $this->app->DB->Select("SELECT `id` FROM `projekt` WHERE `abkuerzung` = '$projekt' LIMIT 1");
      if($projektid != "" && $projektid > 0){
      }else{
        $error .= "Bitte gültiges Projekt ausfüllen"."\n";
      }
    }

    if($teilprojekt != ""){
      $teilprojekt = explode(" ", $teilprojekt);
      $teilprojekt = $teilprojekt[0];
      $teilprojektid = $this->app->DB->Select("SELECT `id` FROM `arbeitspaket` WHERE `id` = '$teilprojekt' LIMIT 1");
      if($teilprojektid != "" && $teilprojektid > 0){
      }else{
        $error .= "Bitte gültiges Teilprojekt ausfüllen"."\n";
      }
    }

    if($datum != '' && $datum != '0000-00-00' && $datum !== '00.00.0000'){
      $datum = date('Y-m-d',strtotime($datum));
    }else{
      $datum = "0000-00-00";
    }

    if($zeit != '' && $zeit !== '00:00'){
      $zeit = date('H:i',strtotime($zeit));
    }else{
      $zeit = "00:00";
    }

    if($dauer != ''){
      $dauer = str_replace(',', '.', $dauer);
    }

    if($status === 'abgeschlossen'){
      $this->app->User->SetParameter("aufgaben_abgeschlossen_mail", $abgeschlossentext);
    }
    if(!empty($error)) {
      return new JsonResponse(['status'=>0,'statusText'=>$error]);
    }

    if($sid){
      $this->app->DB->Update(
        "UPDATE `aufgabe` SET `aufgabe` = '$aufgabe', `adresse` = '$mitarbeiterid', `kunde` = '$kundenid', 
         `ansprechpartner_id` = '$ansprechpartnerId', `beschreibung` = '$beschreibung', 
         `projekt` = '$projektid', `teilprojekt` = '$teilprojektid', `prio` = '$prio', 
         `stunden` = '$dauer', `abgabe_bis` = '$datum', `abgabe_bis_zeit` = '$zeit', 
         `intervall_tage` = '$intervall_tage', `zeiterfassung_pflicht` = '$pflicht', 
         `zeiterfassung_abrechnung` = '$abgerechnet', `emailerinnerung` = '$mailerinnerung', 
         `emailerinnerung_tage` = '$anzahltage', `vorankuendigung` = '$countdown', 
         `oeffentlich` = '$oeffentlich', `startseite` = '$startseite', `pinwand` = '$aufpinwand', 
         `note_color` = '$farbe', `pinwand_id` = '$pinwand', `status` = '$status', `sonstiges` = '$notizen' 
        WHERE `id` = '$sid'"
      );


      if($status === "abgeschlossen" && $abgeschlossentext!=""){
        $to = $this->app->DB->Select("SELECT `email` FROM `adresse` WHERE `id` = '$mitarbeiterid' AND `geloescht` != 1 LIMIT 1");
        $to_name = $this->app->DB->Select("SELECT `name` FROM `adresse` WHERE `id` = '$mitarbeiterid' AND `geloescht` != 1 LIMIT 1");

        if($this->app->erp->MailSend($this->app->erp->GetFirmaMail(),$this->app->erp->GetFirmaName(),$to,$to_name,"Aufgabe wurde als abgeschlossen markiert",$abgeschlossentext)){
          $msg .= "Die Aufgabe wurde als abgeschlossen markiert und wurde per Mail an $to gesendet!"."\n";
        }else{
          if($to==""){
            $empfaenger = $this->app->DB->Select("SELECT a.name FROM `adresse` AS `a` LEFT JOIN `aufgabe` AS `auf` ON a.id = auf.adresse WHERE auf.id = '$sid' LIMIT 1");
            $mail=" $empfaenger (Mailadresse von Mitarbeiter fehlt)";
          }else{
            $mail = $to;
          }
          $msg .= "Die Aufgabe wurde als abgeschlossen markiert, aber es gab einen Fehler beim Senden der Aufgabe per Mail an:$mail!"."\n";
        }
      }
      else if($status == "abgeschlossen")
      {
        $msg = "Die Aufgabe wurde als abgeschlossen markiert.";
      } else {
        $msg = "Die Aufgabe wurde angelegt.";
      }

      return new JsonResponse(['status'=>1,'statusText'=>$msg]);
    }
    $this->app->DB->Insert(
      "INSERT INTO `aufgabe` 
    (`aufgabe`, `adresse`, `kunde`, `ansprechpartner_id`, `beschreibung`, `projekt`, `teilprojekt`, 
     `prio`, `stunden`, `abgabe_bis`, `abgabe_bis_zeit`, `intervall_tage`, `zeiterfassung_pflicht`, `zeiterfassung_abrechnung`, 
     `emailerinnerung`, `emailerinnerung_tage`, `vorankuendigung`, 
     `oeffentlich`, `startseite`, `pinwand`, `note_color`, `pinwand_id`, `status`, `sonstiges`, `angelegt_am`, 
     `initiator`) 
     VALUES ('$aufgabe', '$mitarbeiterid', '$kundenid', '$ansprechpartnerId', '$beschreibung', '$projektid', 
             '$teilprojektid', '$prio', '$dauer', '$datum', '$zeit', '$intervall_tage', '$pflicht', '$abgerechnet',
             '$mailerinnerung', '$anzahltage', '$countdown', '$oeffentlich', 
                                                                                                                                                                                                                                                                                       '$startseite', '$aufpinwand', '$farbe', '$pinwand', '$status', '$notizen', CURDATE(), ".$this->app->User->GetAdresse().")");

    if($status === "abgeschlossen" && $abgeschlossentext!=""){
      $to = $this->app->DB->Select("SELECT `email` FROM `adresse` WHERE `id` = '$mitarbeiterid' AND `geloescht` != 1 LIMIT 1");
      $to_name = $this->app->DB->Select("SELECT `name` FROM `adresse` WHERE `id` = '$mitarbeiterid' AND `geloescht` != 1 LIMIT 1");
      if($this->app->erp->MailSend(
        $this->app->erp->GetFirmaMail(),$this->app->erp->GetFirmaName(),$to,$to_name,
        "Aufgabe wurde als abgeschlossen markiert",$abgeschlossentext)
      ){
        $msg .= "Die Aufgabe wurde als abgeschlossen markiert und wurde per Mail an $to gesendet!"."\n";
      }else{
        if($to==""){
          $empfaenger = $this->app->DB->Select("SELECT a.name FROM `adresse` AS `a` LEFT JOIN `aufgabe` AS `auf` ON a.id = auf.adresse WHERE auf.id = '$sid' LIMIT 1");
          $mail=" $empfaenger (Mailadresse von Mitarbeiter fehlt)";
        }else{
          $mail = $to;
        }
        $msg .= "Die Aufgabe wurde als abgeschlossen markiert, aber es gab einen Fehler beim Senden der Aufgabe per Mail an:$mail!"."\n";
      }
    }
    else if($status === "abgeschlossen")
    {
      $msg = "Die Aufgabe wurde als abgeschlossen markiert.";
    } else {
      $msg = "Die Aufgabe wurde angelegt.";
    }
    return new JsonResponse(['status'=>1,'statusText'=>$msg]);
  }

  public function HandleTaskAddToTimeLineAction(): JsonResponse
  {
    $taskId = (int)$this->app->Secure->GetPOST('task_id');
    $text = (string)$this->app->Secure->GetPOST('text', '', '', true);
    $timeLineId = $this->createTaskTimeline($taskId, $text);

    return new JsonResponse([ 'id' => $timeLineId, 'timeline' => $this->getTimeLineElementsForTaskId($taskId)]);
  }

  /**
   * @param int      $taskId
   * @param string   $content
   * @param int|null $addressId
   *
   * @return int
   */
  public function createTaskTimeline(int $taskId, string $content, ?int $addressId = null): int
  {
    if($addressId === null) {
      $addressId = $this->app->User->GetAdresse();
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `task_timeline` (`task_id`, `address_id`, `time`, `content`) 
        VALUES (%d, %d, NOW(), '%s')",
        $taskId, $addressId , $this->app->DB->real_escape_string($content)
      )
    );

    return (int)$this->app->DB->GetInsertID();
  }

  /**
   * @return JsonResponse
   */
  public function HandleChangeStatusAction(): JsonResponse
  {
    $taskId = (int)$this->app->Secure->GetPOST('task_id');
    $status = (string)$this->app->Secure->GetPOST('status');
    $task = $this->app->DB->SelectRow(sprintf('SELECT `status` FROM `aufgabe` WHERE `id` = %d', $taskId));
    if(empty($task)) {
      return new JsonResponse(['error' => 'Aufgabe nicht gefunden']);
    }
    if($this->app->DB->real_escape_string($task['status']) !== $status) {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `aufgabe` SET `status` = '%s' WHERE `id` = %d",
          $status, $taskId
        )
      );
      $this->createTaskTimeline($taskId, sprintf("Verschoben von '%s' nach '%s'", $task['status'], $status));
    }

    return new JsonResponse(['timeline' => $this->getTimeLineElementsForTaskId($taskId)]);
  }

  /* inhalt des popup fenster */
  public function AufgabenEdit()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    switch($cmd) {
      case 'get':
        return $this->HandleTaskGetAction();
      case 'save':
        return $this->HandleTaskSaveAction();
      case 'addtotimeline':
        return $this->HandleTaskAddToTimeLineAction();
      case 'changestatus':
        return $this->HandleChangeStatusAction();
    }
  }

  function AufgabenEditWdh()
  {
    $this->AufgabenMenu();
    $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=aufgaben&action=list#tabs-2';\">");
    $widget = new WidgetAufgabe($this->app,'NEUEAUFGABE'); 
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=aufgaben&action=list#tabs-2");

    $widget->Edit();

    $this->app->Tpl->Parse('PAGE', 'aufgabenuebersicht.tpl');
  }

  /**
   * @return JsonResponse
   */
  public function AufgabenDragDrop(): JsonResponse
  {

    $aufgabeId = $this->app->Secure->GetGET('id');
    $aufgabeDatum = $this->app->Secure->GetGET('start');

    if ($aufgabeId && $aufgabeDatum) {
      $this->app->DB->Update('
        UPDATE `aufgabe` SET `abgabe_bis` = "' . $aufgabeDatum . '" WHERE `id` = "' . $aufgabeId . '"
      ');
      return new JsonResponse([
        'status' => 1,
        'statusText' => 'Gespeichert',
        'debug' => $_GET
      ]);
    }

    return new JsonResponse([
      'status' => 0,
      'statusText' => 'Fehler',
      'debug' => $_GET
    ]);
  }

  function AufgabenSort() {

    $idList = $this->app->Secure->GetGET('idList');
    if ($idList) {
      $pos = 1;
      foreach ($idList as $id) {
        $this->app->DB->Update('UPDATE aufgabe SET sort = ' . $pos . ' WHERE id = ' . $id);
        $pos++;
      }
    }

    echo json_encode(array(
      'status' => 1,
      'statusText' => 'Gepsichert'
    ));

  }

  function AufgabenData()
  {
    $subwhere = "";
    $withoutName = true;

    if($this->app->erp->RechteVorhanden("aufgaben","alle"))
    {
      $adresseIdUser = $this->app->User->GetParameter("aufgabe_benutzer_simulieren");
      if($adresseIdUser<=0) $withoutName = false;
    } else {
      $adresseIdUser = $this->app->User->GetID();
    }

    if($adresseIdUser > 0)
    {
      $subwhere = "AND a.adresse='$adresseIdUser'";
    }

    $kunde = $this->app->erp->FirstTillSpace($this->app->User->GetParameter("aufgabe_kalender_filter_kunde")); 
    if($kunde > 0)
    {
      $subwhere .= " AND a.kunde='$kunde' ";
    }

    $start = date("Y-m-d", $this->app->Secure->GetGET('start'));
    $end = date("Y-m-d", $this->app->Secure->GetGET('end'));

    //Produktion start - ende
    $data = $this->app->DB->SelectArr("SELECT DISTINCT a.id, CONCAT(IF(a.status='abgeschlossen','&#10003; ',''),".($withoutName?"''":"adr.name,': '").",a.aufgabe, if(a.kunde > 0,CONCAT(' (',ak.name,')'),'')) as title, a.abgabe_bis as start, a.abgabe_bis as ende FROM aufgabe a LEFT JOIN adresse adr ON adr.id=a.adresse LEFT JOIN adresse ak ON ak.id=a.kunde
      WHERE a.abgabe_bis<='$end' AND a.abgabe_bis >='$start' $subwhere ORDER by a.abgabe_bis");

    if($data){
      $cdata = count($data);
      for ($i = 0; $i < $cdata; $i++) {
        $data[$i]['allDay'] = 1;//(($data[$i]['allDay']=='1')?true:false);
        $data[$i]['public'] = 1;//(($data[$i]['public']=='1')?true:false);
        $data[$i]['title'] = $this->app->erp->ReadyForPDF($data[$i]['title']);
        $data[$i]['beschreibung'] = $this->app->erp->ReadyForPDF($data[$i]['beschreibung']);
      }
    }
    header('Content-type: application/json');    
    echo json_encode($data);    
    $this->app->ExitXentral();
  }

  public function sendAufgabenMail($aufgabe,$vorabankuendigung=false)
  {
    $arraufgabe = $this->app->DB->SelectArr("SELECT *,DATE_FORMAT(abgabe_bis,'%d.%m.%Y') as datum,DATE_FORMAT(abgabe_bis,'%Y%m%d') as icaldatum, DATE_FORMAT(abgabe_bis_zeit,'%H%i00') as icaluhrzeit,
        DATE_FORMAT(abgabe_bis_zeit,'%H:%i') as zeit FROM aufgabe WHERE id='$aufgabe' LIMIT 1");

    $adresse = $arraufgabe[0]["adresse"];
    $adresse_initiator = $arraufgabe[0]["initiator"];

    //$this->LogFile("sende an adresse ".$adresse);

    $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresse' AND geloescht!=1 LIMIT 1");
    $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht!=1 LIMIT 1");

    $initiator_to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresse_initiator' AND geloescht!=1 LIMIT 1");
    $initiator_to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse_initiator' AND geloescht!=1 LIMIT 1");

    //$this->LogFile("Sende Aufgabe $aufgabe an Email ".$to." und Initiator ".$initiator_to);

    $aufgabe_name = $arraufgabe[0]["aufgabe"];
    $beschreibung = $arraufgabe[0]["beschreibung"];
    $sonstiges= $arraufgabe[0]["sonstiges"];
    $datum = $arraufgabe[0]["datum"];
    $zeit = $arraufgabe[0]["zeit"];

    $text = "Aufgabe: $aufgabe_name\r\n\r\n";
    $text .= "Mitarbeiter: $to_name\r\n\r\n";
    $text .= "Abgabe bis: $datum $zeit Uhr\r\n";

    if($beschreibung!="")
      $text .= "Beschreibung: \r\n\r\n$beschreibung\r\n";


    if($sonstiges!="")
      $text .= "Notizen: \r\n\r\n$sonstiges\r\n";

    $event_id = $aufgabe;

    $summary = $arraufgabe[0]["aufgabe"];
    $venue = 'WaWision Aufgabe';

    $start = $arraufgabe[0]["icaldatum"];
    $start_time = $arraufgabe[0]["icaluhrzeit"];
    $end = $arraufgabe[0]["icaldatum"];
    $end_time = $arraufgabe[0]["icaluhrzeit"];

    $status = 'TENTATIVE';
    $sequence = 0;


    $beschreibung = preg_replace('#<[^>]+>#', ' ', $arraufgabe[0]["beschreibung"]);
    $beschreibung = preg_replace('/\s\s+/', ' ', $beschreibung);

    $ical = "BEGIN:VCALENDAR\r\n";
    $ical .= "VERSION:2.0\r\n";
    $ical .= "PRODID:-//WaWision//Aufgabe//DE\r\n";
    $ical .= "METHOD:REQUEST\r\n";
    $ical .= "BEGIN:VEVENT\r\n";
    //$ical .= "ORGANIZER;SENT-BY=\"MAILTO:$initiator_to\":MAILTO:onbehalfoforganizer@kaserver.com\r\n";
    $ical .= "ORGANIZER;SENT-BY=\"MAILTO:$initiator_to\"\r\n";
    $ical .= "ATTENDEE;CN=$to;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;RSVP=TRUE:mailto:$initiator_to\r\n";
    $ical .= "UID:".strtoupper(md5($event_id))."-wawision\r\n";
    $ical .= "SEQUENCE:".$sequence."\r\n";
    $ical .= "STATUS:".$status."\r\n";
    $ical .= "DTSTAMPTZID=Europe/Berlin:".date('Ymd').'T'.date('His')."\r\n";
    $ical .= "DTSTART:".$start."T".$start_time."\r\n";
    $ical .= "DTEND:".$end."T".$end_time."\r\n";
    $ical .= "LOCATION:".$venue."\r\n";
    $ical .= "SUMMARY:".$summary."\r\n";
    $ical .= "DESCRIPTION:".$beschreibung."\r\n";
    $ical .= "BEGIN:VALARM\r\n";
    $ical .= "TRIGGER:-PT15M\r\n";
    $ical .= "ACTION:DISPLAY\r\n";
    $ical .= "DESCRIPTION:Reminder\r\n";
    $ical .= "END:VALARM\r\n";
    $ical .= "END:VEVENT\r\n";
    $ical .= "END:VCALENDAR\r\n";

    $datei = $this->app->erp->GetTMP().'Aufgabe_'.$arraufgabe[0]["aufgabe"].".ics";
    file_put_contents($datei,$ical);
    if($start!="00000000")
      $dateien = array($datei);
    else $dateien = "";

    if($vorabankuendigung)
    {
      $result = $this->app->erp->MailSend($this->app->erp->GetFirmaMail(),
        $this->app->erp->GetFirmaAbsender(),$to,$to_name,
        "VORABERINNERUNG: ".$aufgabe_name,$text,$dateien,"",false);
      if($to!=$initiator_to){
        $this->app->erp->MailSend($this->app->erp->GetFirmaMail(),
          $this->app->erp->GetFirmaAbsender(), $initiator_to, $initiator_to_name,
          "INITIATOR VORABERINNERUNG: " . $aufgabe_name, $text, $dateien, "", false);
      }
    }
    else
    {
      $result = $this->app->erp->MailSend($this->app->erp->GetFirmaMail(),
        $this->app->erp->GetFirmaAbsender(),$to,$to_name,
        "ERINNERUNG: ".$aufgabe_name,$text,$dateien,"",false);
      if($to!=$initiator_to)
        $this->app->erp->MailSend($this->app->erp->GetFirmaMail(),
          $this->app->erp->GetFirmaAbsender(),$initiator_to,$initiator_to_name,
          "INITIATOR ERINNERUNG: ".$aufgabe_name,$text,$dateien,"",false);
    }

    unlink($datei);
    return $result;
  }
}
