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
//include ("_gen/zeiterfassung.php");
class Zeiterfassung { //extends GenZeiterfassung {
  /** @var Application $app */
  var $app;

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public function TableSearch($app, $name, $erlaubtevars)
  {
    switch($name) {
      case 'zeiterfassunguser':
        $allowed['zeiterfassung'] = array('listuser');

        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#offen').click( function() { fnFilterColumn1( 0 ); } );");

        //$this->app->Tpl->Add('JQUERYREADY',"$('#abrechnung').click( function() { fnFilterColumn2( 0 ); } );");
        for ($r = 1;$r < 2;$r++) {
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


        //$heading =  array('','A','Datum','Von','Bis','Dauer','Mitarbeiter','Art','Tätigkeit','Projekt','Men&uuml;');

        $heading = array('', 'Datum', 'Von', 'Bis', 'Dauer', 'Mitarbeiter','Art', 'Tätigkeit', 'Abr.','Projekt', 'Men&uuml;');

        //$width   =  array('1%','1%','1%','1%','1%','5%','20%','40%','10%','1%');
        $width = array('1%', '1%', '1%', '1%', '5%', '20%','5%', '40%', '5%','5%', '1%');

        //$findcols = array('open','Auswahl','z.von','von','bis','Dauer','Mitarbeiter','id');
        $findcols = array('open', 'z.von', 'von', 'bis', 'Dauer', 'Mitarbeiter','z.art','z.aufgabe','z.abrechnen','p.abkuerzung','id');
        $searchsql = array('z.id', 'z.bis', 'z.aufgabe', 'a.name','z.art', "if(z.adresse_abrechnung!=0,CONCAT('<i>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe)", "p.abkuerzung");
        $defaultorder = 2;
        $defaultorderdesc = 1;
        $menu = '<table cellpadding=0 cellspacing=0><tr><td nowrap>'.
          "<a href=\"index.php?module=zeiterfassung&action=create&id=%value%&back=zeiterfassunguser\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;" .
          "<a href=\"index.php?module=zeiterfassung&action=create&copyid=%value%&back=zeiterfassunguser\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>" .
          "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zeiterfassung&action=listuser&do=stornieren&lid=%value%&back=zeiterfassunguser\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" .
          '</td></tr></table>';

        //CONCAT('<input type=\"checkbox\">') as auswahl,

        //$menucol=9;

        $menucol = 10;
        $alignright = array(5);
        $sumcol = 5;

        //CONCAT(LPAD(HOUR(TIMEDIFF(z.bis, z.von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(z.bis, z.von)),2,'0')) AS Dauer,
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,
                  '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
                  DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR')) AS Datum, 
                  DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis,
                  format((HOUR(TIMEDIFF(z.bis, z.von))) + MINUTE(TIMEDIFF(z.bis, z.von))/60,2) AS Dauer,
                  a.name as Mitarbeiter,z.art,
                  if(z.art='Pause','<font color=green>Pause</font>',if(z.adresse_abrechnung!=0,CONCAT('<i style=color:#999>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe)) as Taetigkeit,
                  if(z.abrechnen > 0,'(A)',''),
                  p.abkuerzung,
                  z.id
                FROM zeiterfassung z 
                LEFT JOIN adresse a ON a.id=z.adresse 
                LEFT JOIN adresse b ON b.id=z.adresse_abrechnung
                LEFT JOIN projekt p ON p.id=z.projekt 
                LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id";

        // Fester filter

        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET('more_data1');

        if ($more_data1 == 1) {
          $subwhere[] = " z.abrechnen='1' AND z.abgerechnet!='1' ";
        }

        //        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.datum=CURDATE() AND a.status='freigegeben'";

        $von = $this->app->User->GetParameter('zeiterfassung_listuser_von');
        if($von!='') {
          $subwhere[] = " DATE_FORMAT(z.von,'%Y-%m-%d') >= '".$this->app->String->Convert($von,'%3.%2.%1','%1-%2-%3')."'";
        }

        $bis = $this->app->User->GetParameter('zeiterfassung_listuser_bis');
        if($bis!='') {
          $subwhere[] = " DATE_FORMAT(z.bis,'%Y-%m-%d') <= '".$this->app->String->Convert($bis,'%3.%2.%1','%1-%2-%3')."'";
        }

        $tmp = '';
        $csubwhere = !empty($subwhere)?count($subwhere):0;
        for ($j = 0;$j < $csubwhere;$j++) {
          $tmp.= ' AND ' . $subwhere[$j];
        }

        $where = " z.id!='' AND z.adresse='" . $this->app->User->GetAdresse() . "' $tmp";
        $count = "SELECT COUNT(z.id) FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse
                                                 LEFT JOIN adresse b ON b.id=z.adresse_abrechnung
                                                 LEFT JOIN projekt p ON p.id=z.projekt
                                                 LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id WHERE $where ";
        $moreinfo = true;
        break;
      case 'zeiterfassung':
        $allowed['zeiterfassung'] = array('list');


        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#offen').click( function() { fnFilterColumn10( 0 ); } );");
        for ($r = 10;$r < 12;$r++) {
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


        //$heading =  array('','A','Datum','Von','Bis','Dauer','Mitarbeiter','Art','Aufabe','Projekt','Men&uuml;');

        $heading = array('', 'Datum', 'Von', 'Bis', 'Dauer', 'Mitarbeiter','Art', 'Aufgabe', 'Abr.','Projekt', 'Men&uuml;');

        //$width   =  array('1%','1%','1%','1%','1%','5%','20%','40%','10%','1%');
        $width = array('1%', '1%', '1%', '1%', '5%', '20%','5%', '40%','5%','5%', '1%');

        //$findcols = array('open','Auswahl','z.von','von','bis','Dauer','Mitarbeiter','id');
        $findcols = array('open', "z.von", 'z.von', 'z.bis', '(HOUR(TIMEDIFF(z.bis, z.von))) + MINUTE(TIMEDIFF(z.bis, z.von))/60', 'a.name','z.art',"if(z.adresse_abrechnung!=0,CONCAT('<i style=color:#999>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe)", "if(z.abrechnen > 0,'(A)','')",'p.abkuerzung', 'z.id');
        $searchsql = array('z.id', 'z.von', 'z.bis', $this->app->erp->FormatPreis('(HOUR(TIMEDIFF(z.bis, z.von))) + MINUTE(TIMEDIFF(z.bis, z.von))/60',2), 'a.name','z.art', 'a.name', 'z.art', "if(z.adresse_abrechnung!=0,CONCAT('<i style=color:#999>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe)", "if(z.abrechnen > 0,'(A)','')", 'p.abkuerzung', "DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR'))",'b.name','b.kundennummer', );
        $defaultorder = 2;
        $defaultorderdesc = 1;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zeiterfassung&action=create&id=%value%&back=zeiterfassung\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zeiterfassung&action=list&do=stornieren&lid=%value%&back=zeiterfassung\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        //CONCAT('<input type=\"checkbox\">') as auswahl,

        $datecols = array(1);
        $numbercols = array(4);

        //$menucol=9;
        $sumcol = 5;
        $alignright = array(5);
        $menucol = 10;
        //CONCAT(LPAD(HOUR(TIMEDIFF(z.bis, z.von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(z.bis, z.von)),2,'0')) AS Dauer,
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,
                                           '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,

                                           CONCAT('<!--',DATE_FORMAT(z.von,'%Y%m%d'),'-->',DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR'))) AS Datum, 
                                           DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis,
                                            round(HOUR(TIMEDIFF(z.bis, z.von)) + MINUTE(TIMEDIFF(z.bis, z.von))/60,2) AS Dauer,

                                           a.name as Mitarbeiter,z.art,
                                           if(z.adresse_abrechnung!=0,CONCAT('<i style=color:#999>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe) as Taetigkeit,
                                            if(z.abrechnen > 0,'(A)',''),
                                             p.abkuerzung,
                                               z.id

                                                 FROM zeiterfassung z 
                                                 LEFT JOIN adresse a ON a.id=z.adresse 
                                                 LEFT JOIN adresse b ON b.id=z.adresse_abrechnung
                                                 LEFT JOIN projekt p ON p.id=z.projekt 
                                                 LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id";

        // Fester filter

        // START EXTRA more

        $fvon = $this->app->YUI->TableSearchFilter($name, 2, 'von', $this->app->User->GetParameter('zeiterfassung_von'));
        $fbis = $this->app->YUI->TableSearchFilter($name, 3, 'bis', $this->app->User->GetParameter('zeiterfassung_bis'));
        $fprojekt = reset(explode(' ',trim($this->app->YUI->TableSearchFilter($name, 4, 'projekt', $this->app->User->GetParameter('zeiterfassung_projekt')))));
        $fmitarbeiter = reset(explode(' ',trim($this->app->YUI->TableSearchFilter($name, 5, 'mitarbeiter', $this->app->User->GetParameter('zeiterfassung_projekt')))));
        if($fvon)
        {
          if(strpos($fvon, '.') !== false){
            $fvon = $this->app->String->Convert($fvon, '%1.%2.%3', '%3-%2-%1');
          }
        }
        if($fbis)
        {
          if(strpos($fbis, '.') !== false){
            $fbis = $this->app->String->Convert($fbis, '%1.%2.%3', '%3-%2-%1');
          }
        }
        if($fprojekt){
          $fprojekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$fprojekt' AND geloescht != 1 LIMIT 1");
        }
        if($fmitarbeiter){
          $fmitarbeiter = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer = '$fmitarbeiter' AND geloescht != 1 LIMIT 1");
        }

        $more_data1 = $this->app->Secure->GetGET('more_data10');

        if ($more_data1 == 1) {
          $subwhere[] = " z.abrechnen='1' AND z.abgerechnet!='1' ";
        }
        if ($fprojekt){
          $subwhere[] = " z.projekt = '$fprojekt' ";
        }
        if ($fmitarbeiter){
          $subwhere[] = " z.adresse = '$fmitarbeiter' ";
        }
        if ($fvon){
          $subwhere[] = " DATE_FORMAT(z.bis,'%Y-%m-%d') >= '$fvon' ";
        }
        if ($fbis){
          $subwhere[] = " DATE_FORMAT(z.von,'%Y-%m-%d') <= '$fbis' ";
        }
        $tmp = '';
        $csubwhere = !empty($subwhere)?count($subwhere):0;
        for ($j = 0;$j < $csubwhere;$j++) {
          $tmp.= ' AND ' . $subwhere[$j];
        }
        $where = " z.id!='' $tmp";

        // gesamt anzahl
        $count = 'SELECT COUNT(z.id) FROM zeiterfassung z';
        $moreinfo = true;

        break;
      case 'zeiterfassungkundenoffen':
        $allowed['zeiterfassung'] = array('list');


        $this->app->Tpl->Add('JQUERYREADY',"$('#kunden').click( function() { fnFilterColumn1( 0 ); } );");



        for($r=1;$r<2;$r++)
        {
          $this->app->Tpl->Add('JAVASCRIPT','
                                            function fnFilterColumn'.$r.' ( i )
                                            {
                                            if(oMoreData'.$r.$name.'==1)
                                            oMoreData'.$r.$name.' = 0;
                                            else
                                            oMoreData'.$r.$name.' = 1;
        
                                            $(\'#'.$name.'\').dataTable().fnFilter( 
                                            \'\',
                                            i, 
                                            0,0
                                            );
                                            }
                                            ');
        }


        $heading = array('Kunde', 'Kundennr', 'Offen Abr.', 'Offen ohne Abr.','Men&uuml;');
        $alignright = array(3,4);

        $width = array('10%', '5%', '15%', '15%','1%');

        $findcols = array('a.name', 'a.kundennummer', '(SELECT SUM(TIME_TO_SEC(TIMEDIFF(z2.bis, z2.von))) FROM zeiterfassung z2 WHERE z2.adresse_abrechnung=a.id AND z2.abrechnen=1 AND z2.abgerechnet!=1)','(SELECT SUM(TIME_TO_SEC(TIMEDIFF(z2.bis, z2.von))) FROM zeiterfassung z2 WHERE z2.adresse_abrechnung=a.id AND z2.abrechnen!=1 AND z2.abgerechnet!=1)', 'z.id');
        $searchsql = array('a.name','a.kundennummer');
        $defaultorder = 4;
        $defaultorderdesc = 1;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=adresse&action=abrechnungzeit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . '&nbsp;</td></tr></table>';

        $numbercols = array(3,4);

        if($this->app->Secure->GetGET('more_data1')=='1')
        {
          $subwhere = ' AND z.aufgabe_id <=0 AND z.auftrag <=0 AND z.produktion <=0 AND z.arbeitsanweisung <=0 AND z.projekt <=0  ';
          $subwhere2 = ' AND z2.aufgabe_id <=0 AND z2.auftrag <=0 AND z2.produktion <=0 AND z2.arbeitsanweisung <=0 AND z2.projekt <=0  ';
        }else{
          $subwhere = '';
          $subwhere2 = '';
        }


        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,
                                           a.name,a.kundennummer,
         (SELECT ".$this->app->erp->FormatMenge("SUM(TIME_TO_SEC(TIMEDIFF(z2.bis, z2.von)))/3600")." FROM zeiterfassung z2 LEFT JOIN projekt p2 ON z2.projekt = p2.id WHERE (z2.adresse_abrechnung = a.id OR p2.kunde = a.id) AND z2.abrechnen=1 AND z2.abgerechnet!=1 $subwhere2) as offen,                                
         (SELECT ".$this->app->erp->FormatMenge("SUM(TIME_TO_SEC(TIMEDIFF(z2.bis, z2.von)))/3600")." FROM zeiterfassung z2 LEFT JOIN projekt p2 ON z2.projekt = p2.id WHERE (z2.adresse_abrechnung = a.id OR p2.kunde = a.id) AND z2.abrechnen!=1 AND z2.abgerechnet!=1 $subwhere2) as offen2,
                                           a.id
                                             FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse_abrechnung ";
        $where = ' z.abgerechnet!=1 AND a.id > 0 '.$subwhere;
        $groupby = ' GROUP by z.adresse_abrechnung ';

        // gesamt anzahl
        $count = 'SELECT COUNT(distinct z.adresse_abrechnung) FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse_abrechnung WHERE z.abgerechnet!=1 AND a.id > 0 '.$subwhere;
        break;
    }
    $erg = [];
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
   * Zeiterfassung constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern){
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ZeiterfassungCreate");
    $this->app->ActionHandler("edit","ZeiterfassungEdit");
    $this->app->ActionHandler("list","ZeiterfassungList");
    $this->app->ActionHandler("listuser","ZeiterfassungListUser");
    $this->app->ActionHandler("delete","ZeiterfassungDelete");
    $this->app->ActionHandler("arbeitspaket","ArbeitspaketDetails");
    $this->app->ActionHandler("details","ZeiterfassungDetails");
    $this->app->ActionHandler("minidetail","ZeiterfassungMinidetail");
    $this->app->ActionHandler("abrechnenpdf","ZeiterfassungAbrechnenpdf");
    $this->app->ActionHandler("dokuarbeitszeitpdf","ZeiterfassungDokuArbeitszeit");
    $this->app->ActionHandler("bearbeitenerlauben","ZeiterfassungBearbeitenerlauben");

    $this->app->erp->Headlines('Zeiterfassung');

    $this->app->ActionHandlerListen($app);

  }

  public function ZeiterfassungBearbeitenerlauben()
  {
    //Rechte
  }

  public function ZeiterfassungMinidetail()
  {
    $id = $this->app->Secure->GetGET('id');

    $tmp = $this->app->DB->SelectRow("SELECT * FROM zeiterfassung WHERE id='$id'");
    $teilprojekt = $this->app->DB->Select("SELECT aufgabe FROM arbeitspaket WHERE id='".$tmp['arbeitspaket']."'");

    echo '<table width="710">';
    echo '<tr><td width="200"><b>Ort:</b></td><td>'.$tmp['ort'].'</td></tr>';
    echo '<tr><td><b>Tätigkeit:</b></td><td>'.$tmp['aufgabe'].'</td></tr>';
    echo "<tr valign=\"top\"><td><b>Beschreibung:</b></td><td>".nl2br($tmp['beschreibung']).'</td></tr>';
    echo '<tr><td><b>Teilprojekt:</b></td><td>'.$teilprojekt."</td></tr>";
    echo '<tr><td><b>Kostenstelle:</b></td><td>'.$tmp['kostenstelle'].'</td></tr>';
    echo '<tr><td><b>Verrechnungsart:</b></td><td>'.$tmp['verrechnungsart'].'</td></tr>';
    if($tmp['gps']!='') {
      $tmpgps = explode(';',$tmp['gps']);
      $link = '<a href="http://maps.google.com/maps?q='.$tmpgps[0].",".$tmpgps[1].'" target="_blank">Google Maps</a>';
    }
    echo '<tr><td><b>GPS Koordinaten:</b></td><td>'.$tmp['gps']."&nbsp;$link</td></tr>";
    echo '</table>';
    $this->app->ExitXentral();
  }

  public function ZeiterfassungAbrechnenpdf()
  {
    //Create a new PDF file
    $pdf=new FPDF();
    $pdf->AddPage();

    $pdf->SetFontClassic('Arial','B',11);

    //Create lines (boxes) for each ROW (Product)
    //If you don't use the following code, you don't create the lines separating each row
    $tmp = $this->app->DB->SelectArr("SELECT a.kundennummer as kundennummer, a.name as name, z.aufgabe, SUM((UNIX_TIMESTAMP(z.bis)-UNIX_TIMESTAMP(z.von))/3600.0) as stunden
        FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse_abrechnung WHERE z.abrechnen='1' AND (z.ist_abgerechnet IS NULL OR z.ist_abgerechnet='0') AND z.adresse_abrechnung > 0 GROUP BY 1");
    // Colors, line width and bold font
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(.3);
    //$pdf->SetFontClassic('','B');
    // Header

    $header = array('Kundennr.','Name','Stunden','OK');
    $w = array(30,130,20,10);
    $pdf->Cell($w[0],7,$header[0],1,0,'L',true);
    $pdf->Cell($w[1],7,$header[1],1,0,'L',true);
    $pdf->Cell($w[2],7,$header[2],1,0,'C',true);
    $pdf->Cell($w[3],7,$header[3],1,0,'C',true);
    $pdf->Ln();
    $pdf->SetFontClassic('Arial','',11);
    // Color and font restoration

    // Data
    $fill = false;
    for($i=0;$i<count($tmp);$i++)
    {
      if($tmp[$i]["prio"]==0) $prio="";
      else if($tmp[$i]["prio"]==1) $prio="Ja";
      $pdf->Cell($w[0],6,$tmp[$i]["kundennummer"],'LRTB',0,'L',$fill);
      $pdf->Cell($w[1],6,$this->app->erp->ReadyForPDF($tmp[$i]["name"]),'LRTB',0,'L',$fill);
      $pdf->Cell($w[2],6,round($tmp[$i]["stunden"],2),'LRTB',0,'C',$fill);
      $pdf->Cell($w[3],6,"",'LRTB',0,'C',$fill);
      $pdf->Ln();
      $fill = !$fill;
    }
    $pdf->Ln();
    $pdf->SetFontClassic('Arial','',8);
    $name=preg_replace('/[^a-zA-Z0-9_]/' , '' , $this->app->User->GetName());
    $name = strtoupper($name);

    $pdf->Cell(array_sum($w),0,date('Ymd').'_'.$name.'_ABRECHNEN.pdf','',0,'R');

    $pdf->Output(date('Ymd').'_'.$name.'_ABRECHNEN.pdf','D');
    $this->app->ExitXentral();
  }

  public function ZeiterfassungCreate()
  {
    $cmd=$this->app->Secure->GetGET('cmd');
    $back=$this->app->Secure->GetGET('back');
    $msg=$this->app->Secure->GetGET('msg');


    if($cmd=='data' ||  $cmd=='getzeiterfassung' || $cmd=='updatezeiterfassung' || $cmd=='savezeiterfassung' || $cmd=='kalenderansicht' || $cmd=='formularansicht' || $cmd=='delzeiterfassung' || $cmd=='mitarbeiteransichtdata' || $cmd=='copyzeiterfassung')
    {
      $start_datum = date('Y-m-d', $this->app->Secure->GetGET('start'));
      $end_datum = date('Y-m-d', $this->app->Secure->GetGET('end'));
      switch($cmd)
      {
        case 'formularansicht':
          $this->app->User->SetParameter('zeiterfassung_buchen_formularansicht',1);
          header('Location: index.php?module=zeiterfassung&action=create');
          exit;
        break;
        case 'kalenderansicht':
          $this->app->User->SetParameter('zeiterfassung_buchen_formularansicht',0);
          header('Location: index.php?module=zeiterfassung&action=create');
          exit;
        break;
        case 'mitarbeiteransichtdata':
        case 'data':
          if($this->app->User->GetParameter('zeiterfassung_buchen_termine')=='1' && $cmd!='mitarbeiteransichtdata') {
           if($nurmeine > 0)
            {
              $data = $this->app->DB->SelectArr("SELECT DISTINCT ke.id, ort,beschreibung, bezeichnung AS title, von AS start, bis AS end, allDay, color, public,erinnerung,adresse,adresseintern,projekt
        FROM kalender_event AS ke
        LEFT JOIN kalender_user AS ku ON ke.id=ku.event
        WHERE (ku.userid='$user') AND (ke.von < '$end' AND (ke.bis >= '$start' OR ke.bis='0000-00-00 00:00:00') ) GROUP by ke.id ORDER by start");
            } else {
      $data = $this->app->DB->SelectArr("SELECT DISTINCT ke.id, ort,beschreibung, ke.bezeichnung AS title, von AS start, bis AS end, allDay, color, public,erinnerung,adresse,adresseintern,projekt,kg.farbe
        FROM kalender_event AS ke
        LEFT JOIN kalender_user ku ON ke.id=ku.event
        LEFT JOIN kalender_gruppen kg ON kg.id=ku.gruppe
        WHERE (ke.von < '$end_datum' AND (ke.bis >= '$start_datum' OR (ke.bis='0000-00-00 00:00:00' AND ke.von!='0000-00-00 00:00:00') AND NOT (ke.von < '$start_datum' AND ke.bis='0000-00-00 00:00:00'))  ) $subwhere GROUP by ke.id ORDER by start");
            }
            $cdata = !empty($data)?count($data):0;
            for($i=0;$i<$cdata;$i++)
            {
              $data[$i]['allDay'] = (($data[$i]['allDay']=='1')?true:false);
              $data[$i]['public'] = (($data[$i]['public']=='1')?true:false);
              $data[$i]['erinnerung'] = (($data[$i]['erinnerung']=='1')?true:false);
              $data[$i]['title'] = $this->app->erp->ReadyForPDF($data[$i]['title']);
              $data[$i]['ort'] = $this->app->erp->ReadyForPDF($data[$i]['ort']);
              $data[$i]['adresse'] = $this->app->erp->ReadyForPDF($data[$i]['adresse']);
              $data[$i]['adresseintern'] = $this->app->erp->ReadyForPDF($data[$i]['adresseintern']);
              $data[$i]['projekt'] = $this->app->erp->ReadyForPDF($data[$i]['adresseintern']);
              $data[$i]['beschreibung'] = $this->app->erp->ReadyForPDF($data[$i]['projekt']);
              //if($data[$i]['farbe']!="")
              $data[$i]['color'] = '#999';//$data[$i]['farbe'];
              $data[$i]['task'] = 0;//$data[$i]['farbe'];
              $data[$i]['id'] = 'kalender_'.$data[$i]['id'];
            }
          }

          if($cmd=='mitarbeiteransichtdata')
          {
            $tmpadresse = $this->app->User->GetParameter('zeiterfassung_list_mitarbeiterkalenderansicht');
          }
          else  {
            $tmpadresse = $this->app->User->GetAdresse();
          }

          $result = $this->app->DB->SelectArr("SELECT * FROM zeiterfassung WHERE adresse='".$tmpadresse."' 
              AND DATE_FORMAT(von,'%Y-%m-%d') BETWEEN '$start_datum' AND '$end_datum' ");
          $cresult = !empty($result)?count($result):0;
          for($i=0;$i<$cresult;$i++)
          {

            if($result[$i]['art']=='Arbeit') {
              $color='var(--sidebar-background)';
            } else {
              $color='#333';
            }
            $data[] = array('id' => $result[$i]['id'],
              'title'=>$result[$i]['aufgabe'],
              'start'=> $result[$i]['von'],
              'end'=> $result[$i]['bis'],
              'allDay'=>false,
              'color'=>$color,
              'public'=>'',
              'task'=>1);
          }
          //background
          if($this->app->User->GetParameter('zeiterfassung_buchen_stechuhr')=='1') {

            $stechuhr = $this->app->DB->SelectArr("SELECT *, datum as start,
                DATE_ADD(datum, INTERVAL 30 MINUTE) as end FROM stechuhr WHERE adresse='".$tmpadresse."' 
              AND DATE_FORMAT(datum,'%Y-%m-%d') between '$start_datum' AND '$end_datum'");
            $cstechuhr = !empty($stechuhr)?count($stechuhr):0;
            for($si=0; $si<$cstechuhr;$si++)
            {
              $data[] = array(
                'title'=> $stechuhr[$si]['status'],
                'start'=> $stechuhr[$si]['start'],
                'end'=> $stechuhr[$si]['end'],
                'color'=>'#f90000',
                'allDay'=>false);
//                'rendering'=>'background');
            }
          }

        break;
        case 'getzeiterfassung':
          $id=$this->app->Secure->GetGET('id');
          $result = $this->app->DB->SelectArr("SELECT *,DATE_FORMAT(von,'%d.%m.%Y') as datum, 
            DATE_FORMAT(von,'%H:%i') as vonzeit, DATE_FORMAT(bis,'%H:%i') as biszeit 
            FROM zeiterfassung WHERE adresse='".$this->app->User->GetAdresse()."'  AND id='$id' LIMIT 1");
          $result = reset($result);

          $data['id']=$result['id'];
          $data['aufgabe']=$result['aufgabe'];
          $data['beschreibung']=$result['beschreibung'];
          $data['datum']=$result['datum'];
          $data['vonzeit']=$result['vonzeit'];
          $data['biszeit']=$result['biszeit'];
          $data['ort']=$result['ort'];
          $data['art']=$result['art'];
          $data['internerkommentar']=$result['internerkommentar'];
          $data['projekt_manuell']=$this->app->DB->Select("SELECT CONCAT(abkuerzung,' ',name) FROM projekt WHERE id='".$result['projekt']."' LIMIT 1");
          $data['serviceauftrag'] = $this->app->DB->Select("SELECT belegnr FROM serviceauftrag WHERE id='".$result["serviceauftrag"]."' LIMIT 1");
          $data['adresse_abrechnung'] = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='".$result["adresse_abrechnung"]."'");
          $data['auftragpositionid']=$this->app->DB->Select("SELECT CONCAT(a.belegnr,'-',ap.sort,' ',a.name,' ',DATE_FORMAT(a.datum,'%d.%m.%Y'),' ',ap.bezeichnung) FROM auftrag_position ap LEFT JOIN auftrag a ON ap.auftrag=a.id WHERE ap.id='".$result["auftragpositionid"]."'");
          $data['produktion']=$this->app->DB->Select("SELECT CONCAT(belegnr,' ',name,' ', DATE_FORMAT(datum,'%d.%m.%Y')) FROM produktion WHERE id='".$result["produktion"]."'");

          $data['auftrag'] = $this->app->DB->Select("SELECT CONCAT(belegnr,' ',name,' ', DATE_FORMAT(datum,'%d.%m.%Y')) FROM auftrag WHERE id='".$result["auftrag"]."'");
          $data['arbeitspaket'] = $this->app->DB->Select("SELECT CONCAT(ap.id,' ',p.abkuerzung,' ',ap.aufgabe) as name2 FROM arbeitspaket ap LEFT JOIN projekt p ON p.id=ap.projekt WHERE ap.id='".$result["arbeitspaket"]."' LIMIT 1");
          $data['abrechnen'] = $result['abrechnen'];
/*
      $adresse_abrechnung_komplett = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='".$tmp[0]["adresse_abrechnung"]."'");
      if($tmp[0]["adresse_abrechnung"]<=0)        $adresse_abrechnung_komplett="";

      $mitarbeiter_komplett = $this->app->DB->Select("SELECT CONCAT(mitarbeiternummer,' ',name) FROM adresse WHERE id='".$tmp[0]["adresse"]."'");
      $kostenstelle_komplett = $this->app->DB->Select("SELECT CONCAT(nummer,' ',beschreibung) FROM kostenstellen WHERE nummer='".$tmp[0]["kostenstelle"]."'");
      $verrechnungsart_komplett = $this->app->DB->Select("SELECT CONCAT(nummer,' ',beschreibung) FROM verrechnungsart WHERE nummer='".$tmp[0]["verrechnungsart"]."'");
      $auftragpositionid_komplett = $this->app->DB->Select("SELECT CONCAT(a.belegnr,'-',ap.sort,' ',a.name,' ',DATE_FORMAT(a.datum,'%d.%m.%Y'),' ',ap.bezeichnung) FROM auftrag_position ap LEFT JOIN auftrag a ON ap.auftrag=a.id WHERE ap.id='".$tmp[0]["auftragpositionid"]."'");
      $produktion_komplett = $this->app->DB->Select("SELECT CONCAT(belegnr,' ',name,' ', DATE_FORMAT(datum,'%d.%m.%Y')) FROM produktion WHERE id='".$tmp[0]["produktion"]."'");

*/


          $data['write']=1; // nur schreiben erlaubt wenn nicht zu alt bzw. gibt es eine Einstelloption in den Firmendaten TODO
        break;

        case 'copyzeiterfassung':
          $id = $this->app->Secure->GetPOST('id');
          $zeiterfassungData = $this->app->DB->SelectRow("SELECT * FROM zeiterfassung WHERE id = '$id' LIMIT 1");
          $datum = explode(' ', $zeiterfassungData['von']);
          $datum = $datum[0];
          $checkzeit = $this->app->erp->ZeiterfassungAllowEdit($datum);

          if($id > 0 && $checkzeit){
            $this->app->erp->AddArbeitszeit($this->app->User->GetAdresse(), $zeiterfassungData['von'], $zeiterfassungData['bis'],
              $zeiterfassungData['aufgabe'], $zeiterfassungData['beschreibung'],$zeiterfassungData['ort'], $zeiterfassungData['projekt'],
              $zeiterfassungData['arbeitspaket'],$zeiterfassungData['art'],$zeiterfassungData['adresse_abrechnung'],$zeiterfassungData['abrechnen'],'',
              '',0,'',0,$zeiterfassungData['internerkommentar'],$zeiterfassungData['auftrag'],$zeiterfassungData['produktion'],0,
              $zeiterfassungData['auftragpositionid'],$zeiterfassungData['serviceauftrag']);

            $status['status']=1;
            $status['statusText']='';
          }else{
            $status['status']=0;
            $status['statusText'] = 'Die Zeiterfassung konnte nicht kopiert werden, da das Datum im Zeitbereich liegt der bereits geschlossen ist.';
          }

          echo json_encode($status);
          $this->app->ExitXentral();
        break;

        case 'delzeiterfassung':
          $id=$this->app->Secure->GetPOST('id');
          $check = $this->app->DB->Select("SELECT id FROM zeiterfassung WHERE adresse='".$this->app->User->GetAdresse()."' AND id='$id' AND id>0 LIMIT 1");
          $checkdatum = $this->app->DB->Select("SELECT DATE_FORMAT(von,'%Y-%m-%d') FROM zeiterfassung WHERE adresse='".$this->app->User->GetAdresse()."' AND id='$id' AND id>0 LIMIT 1");

          $checkzeit = $this->app->erp->ZeiterfassungAllowEdit($checkdatum);

          if($check == $id && $check > 0 && $checkzeit)
          {
            $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id='$check' LIMIT 1");
            $status['status']=1;
            $status['statusText']=''; 

          } else if ($checkzeit==false)
          {
            $status['status']=0;
            $status['statusText']='Die Zeit ist zu alt und darf daher nicht mehr bearbeitet werden!'; 
          }
          else {
            $status['status']=0;
            $status['statusText']='Diese Zeiterfassung konnte nicht gelöscht werden (fehlende Rechte)'; 
          }
          echo json_encode($status);
          exit;
        break;

        case 'savezeiterfassung':
          $id=$this->app->Secure->GetPOST('id');
          $start=$this->app->Secure->GetPOST('start');
          $end=$this->app->Secure->GetPOST('end');
          $datum=$this->app->Secure->GetPOST('datum');
          $aufgabe=$this->app->Secure->GetPOST('aufgabe');
          $beschreibung=$this->app->Secure->GetPOST('beschreibung');
          $internerkommentar=$this->app->Secure->GetPOST('internerkommentar');
          $ort=$this->app->Secure->GetPOST('ort');
          $art=$this->app->Secure->GetPOST('art');

          $checkvon = (int)str_replace(':','',$this->app->Secure->GetPOST('start'));
          $checkbis = (int)str_replace(':','',$this->app->Secure->GetPOST('end'));
          if ($checkbis <= $checkvon) {
            $status['status']=0;
            $status['statusText']='Fehler! Zeit bis ist kleiner bzw gleich als Zeit von!';
            header('Content-Type: application/json');
            echo json_encode($status);
            $this->app->ExitXentral();
          }

          $projekt_manuell=$this->app->Secure->GetPOST('projekt_manuell');
          $arbeitspaket=$this->app->Secure->GetPOST('arbeitspaket');
          $adresse_abrechnung=$this->app->Secure->GetPOST('adresse_abrechnung');
          $auftragpositionid=$this->app->Secure->GetPOST('auftragpositionid');
          $auftrag=$this->app->Secure->GetPOST('auftrag');
          $produktion=$this->app->Secure->GetPOST('produktion');
          $serviceauftrag=$this->app->Secure->GetPOST('serviceauftrag');
          $abrechnen=$this->app->Secure->GetPOST('abrechnen');

          $datum = $this->app->String->Convert($datum,'%1.%2.%3','%3-%2-%1');

          $adresse_abrechnung = trim ($adresse_abrechnung);
          $adresse_abrechnung = substr ($adresse_abrechnung , 0 , (strpos ($adresse_abrechnung , ' ')));
          $adresse_abrechnung = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$adresse_abrechnung' AND kundennummer!='' LIMIT 1");

          $auftrag = trim ($auftrag);
          $auftrag = substr ($auftrag , 0 , (strpos ($auftrag , ' ')));
          $auftrag = $this->app->DB->Select("SELECT id FROM auftrag WHERE belegnr='$auftrag' AND belegnr!='' LIMIT 1");

          $string = $auftragpositionid;
          $string = trim ($string);
          $string = substr ($string , 0 , (strpos ($string , ' ')));
          $tmpauftrag = substr ($string , 0 , (strrpos ($string , '-')));
          $tmpauftrag = $this->app->DB->Select("SELECT id FROM auftrag WHERE belegnr='$tmpauftrag' AND belegnr!='' LIMIT 1");
          $auftragpositionsort = substr ($string , (strrpos ($string , '-')+1),strlen($string));
          $auftragpositionid = $this->app->DB->Select("SELECT id FROM auftrag_position WHERE auftrag='$tmpauftrag' AND sort='$auftragpositionsort' LIMIT 1");

          $string = $produktion;
          $string = trim ($string);
          $produktion = substr ($string , 0 , (strpos ($string , ' ')));
          $produktion = $this->app->DB->Select("SELECT id FROM produktion WHERE belegnr='$produktion' AND belegnr!='' LIMIT 1");

          $arbeitspaket = strstr($arbeitspaket, ' ', true);
          $arbeitspaket = $this->app->DB->Select("SELECT id FROM arbeitspaket WHERE id='".$arbeitspaket."' LIMIT 1");

          $serviceauftrag = reset(explode(' ',$serviceauftrag));
          $serviceauftrag = $this->app->DB->Select("SELECT id FROM serviceauftrag WHERE belegnr='".$serviceauftrag."' LIMIT 1");

          // Projekt grabben und notfalls wieder anzeigen
          $projekt_kennung = reset(explode(' ',$projekt_manuell));
          $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$projekt_kennung."' LIMIT 1");

          $checkzeit = $this->app->erp->ZeiterfassungAllowEdit($datum);

          if($checkzeit)
          {
            if($id > 0)
            {
              $this->app->DB->Update("UPDATE zeiterfassung SET aufgabe='$aufgabe',beschreibung='$beschreibung',ort='$ort',internerkommentar='$internerkommentar',art='$art',adresse_abrechnung='$adresse_abrechnung',von='".$datum." ".$start."',bis='".$datum." ".$end."',
               abrechnen='$abrechnen',arbeitspaket='$arbeitspaket',auftrag='$auftrag',produktion='$produktion',auftragpositionid='$auftragpositionid',projekt='$projekt',serviceauftrag='$serviceauftrag' WHERE id='$id' LIMIT 1");
            }
            else {
            $this->app->erp->AddArbeitszeit($this->app->User->GetAdresse(), $datum.' '.$start, $datum.' '.$end, $aufgabe, $beschreibung,$ort, $projekt, $arbeitspaket,$art,$adresse_abrechnung,$abrechnen,'',
              '',0,'',0,$internerkommentar,$auftrag,$produktion,0,$auftragpositionid,$serviceauftrag);
            //$this->app->erp->AddArbeitszeit($adr_id, $start, $end, $aufgabe, $beschreibung,$ort, $projekt, $paketauswahl,$art,$kunde="",$abrechnen="",$verrechnungsart="",$kostenstelle="",$abgerechnet="0",$gps="",$aufgabeid=0,$internerkommentar="",$auftrag=0,$produktion=0, $preis = 0,$auftragpositionid="");
            }
            $status['status']=1; 
            $status['statusText']=''; 
          } else {
            $status['status']=0; 
            $status['statusText']='Die Zeiterfassung konnte nicht angelegt werden da das Datum im Zeitbereich liegt der bereits geschlossen ist.'; 
          }
          echo json_encode($status);
          exit;
        break;
        case 'updatezeiterfassung':
          $eid=$this->app->Secure->GetGET('eid');
          $start=$this->app->Secure->GetGET('start');
          $end=$this->app->Secure->GetGET('end');

          $datum = strstr($start, ' ', true); 
          $checkzeit = $this->app->erp->ZeiterfassungAllowEdit($datum);
          if($eid > 0 && $checkzeit)
          {
            $this->app->DB->Update("UPDATE zeiterfassung SET von='$start', bis='$end' WHERE id='$eid' LIMIT 1");
          }
          else if(!$checkzeit)
          {
            $status['status']=0; 
            $status['statusText']='Die Zeiterfassung konnte nicht angelegt werden da das Datum im Zeitbereich liegt der bereits geschlossen ist.'; 
            echo json_encode($status);
            exit;
          }
        break;

      }

      header('Content-type: application/json');
      echo json_encode($data);
      exit;
    }  


    $id=$this->app->Secure->GetGET('id');
    if($msg=='' && $id <=0){
      $this->app->User->SetParameter('zeiterfassung_create_datumzeiterfassung', '');
    }
      

    //    $this->app->erp->MenuEintrag("index.php?module=zeiterfassung&action=list","&Uuml;bersicht");
    //   $this->app->erp->MenuEintrag("index.php?module=zeiterfassung&action=create","Neue Zeiterfassung");

    if($back=='zeiterfassung' || $cmd=='extern'){
      $this->ZeiterfassungMenu();
    }
    else {
      $this->app->erp->StartseiteMenu();
      //      $this->app->Tpl->Add(KURZUEBERSCHRIFT,"Zeiterfassung");
    }

    $id =  $this->app->User->GetId();      
    if(is_numeric($id)){
      $adr_id = $this->app->DB->Select("SELECT adresse FROM user WHERE id='$id'");
    }


    if($this->app->erp->Firmendaten('zeiterfassung_abrechnenvorausgewaehlt')=='1' && $this->app->Secure->GetGET('id') <=0)
    {
      $this->app->Tpl->Set('ABRECHNEN','checked');
    } 


    $this->ZeiterfassungManuell($adr_id);
    //$this->app->Tpl->Parse(PAGE,"zeiterfassung_create.tpl");
  }

  public function ZeiterfassungListUser()
  {
    //$this->app->Tpl->Add(KURZUEBERSCHRIFT,"Zeiterfassung");
    $this->app->Tpl->Set('UEBERSCHRIFT','Zeiterfassung');
    $this->app->erp->StartseiteMenu();

    if($this->app->Secure->GetGET('do')=='stornieren'){
      $lid = $this->app->Secure->GetGET('lid');
      $back = $this->app->Secure->GetGET('back');
      if($lid!='')
      {
        if($back=='zeiterfassung') {
          $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id='".$lid."'");
          header('Location: index.php?module=zeiterfassung&action=list');
        }
        else if($back=='zeiterfassungmitarbeiter') {
          $id = $this->app->Secure->GetGET('id');
          $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id='".$lid."'");
          header("Location: index.php?module=adresse&action=zeiterfassung&id=$id");
        }
        else if($back=='service') {
          $id = $this->app->Secure->GetGET('id');
          $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id='".$lid."'");
          header("Location: index.php?module=service&action=list");
        }
        else if($back=='aufgabe') {
          $id = $this->app->Secure->GetGET('id');
          $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id='".$lid."'");
          header("Location: index.php?module=aufgaben&action=list");
        }

        else if($back=='zeiterfassunguser')
        {
          $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id='".$lid."' AND gebucht_von_user='".$this->app->User->GetID()."'");
          header('Location: index.php?module=zeiterfassung&action=listuser');
        }
        else if ($back=='projekt')
        {
          $back_id = $this->app->Secure->GetGET('back_id');
          $back_sid = $this->app->Secure->GetGET('back_sid');
          header("Location: index.php?module=projekt&action=zeit&id=$back_id&sid=$back_sid");
        }
        else if ($back=='lohnabrechnung')
        {
          header('Location: index.php?module=lohnabrechnung&action=list');
        }
        else if ($back=='adresse')
        {
          $back_id = $this->app->Secure->GetGET('back_id');
          header("Location: index.php?module=adresse&action=abrechnungzeit&id=$back_id");                  
        }
        else{
          header('Location: index.php?module=zeiterfassung&action=create#tabs-1');
        }
        exit;
      }
    }

    $this->ZeiteerfassungEigeneUebersicht();
    $this->app->YUI->TableSearch('TAB1','zeiterfassunguser', 'show','','',basename(__FILE__), __CLASS__);

    //TODO wenn man das Recht  hat
    /*if(0)
    {
      $this->app->Tpl->Add('TAB1',
          "<table width=\"100%\"><tr><td align=\"center\"><input type=\"submit\" value=\"in Arbeitsnachweis &uuml;bernehmen\">&nbsp;".
          "<input type=\"submit\" value=\"in Auftrag &uuml;bernehmen\">&nbsp;".
          "<input type=\"submit\" value=\"in Lieferschein &uuml;bernehmen\">&nbsp;<input type=\"submit\" value=\"als abgeschlossen markieren\"></td></tr></table>");
    }*/

    $von = $this->app->User->GetParameter('zeiterfassung_listuser_von');
    if($von!='') {
      $this->app->Tpl->Set('VON',$von);
    }

    $bis = $this->app->User->GetParameter('zeiterfassung_listuser_bis');
    if($bis!='') {
      $this->app->Tpl->Set('BIS',$bis);
    }



    $this->app->YUI->DatePicker('von');
    $this->app->YUI->DatePicker('bis');

    $this->app->YUI->AutoSaveUserParameter('von','zeiterfassung_listuser_von',"var oTable = $('#zeiterfassunguser').DataTable( );oTable.ajax.reload();");
    $this->app->YUI->AutoSaveUserParameter('bis','zeiterfassung_listuser_bis',"var oTable = $('#zeiterfassunguser').DataTable( );oTable.ajax.reload();");

    $this->app->Tpl->Parse('PAGE','zeiterfassunguseruebersicht.tpl');
  }

  function ZeiteerfassungEigeneUebersicht()
  {
    $datumzeiterfassung = $this->app->User->GetParameter('zeiterfassung_create_datumzeiterfassung');
    if($this->app->erp->CheckDateValidate($datumzeiterfassung)){
      $datum = $this->app->User->GetParameter('zeiterfassung_create_datumzeiterfassung');
    }
    else{
      $datum = date('Y-m-d', time());
    }

    /*if(0){
      $heute = (int)$this->app->erp->ZeitGesamtArbeit($this->app->User->GetAdresse(),$datum).":".round(fmod($this->app->erp->ZeitGesamtArbeit($this->app->User->GetAdresse(),$datum),1)*60);//Alternative Zeitangabe
      $pause =(int)$this->app->erp->ZeitGesamtPause($this->app->User->GetAdresse(),$datum).":".round(fmod($this->app->erp->ZeitGesamtPause($this->app->User->GetAdresse(),"","",$datum),1)*60);
      $wocheist = (int)$this->app->erp->ZeitGesamtWocheIst($this->app->User->GetAdresse(),"","",$datum).":".round(fmod($this->app->erp->ZeitGesamtWocheIst($this->app->User->GetAdresse(),"","",$datum),1)*60);
      $monatist = (int)$this->app->erp->ZeitGesamtMonatIst($this->app->User->GetAdresse(),"","",$datum).":".round(fmod($this->app->erp->ZeitGesamtMonatIst($this->app->User->GetAdresse(),"","",$datum),1)*60);
      $monatsoll = (int)$this->app->erp->ZeitGesamtMonatSoll($this->app->User->GetAdresse(),"","",$datum).":".round(fmod($this->app->erp->ZeitGesamtMonatSoll($this->app->User->GetAdresse(),"","",$datum),1)*60);
    }else{*/
      $heute = number_format($this->app->erp->ZeitGesamtArbeit($this->app->User->GetAdresse(),$datum),2,",","");
      $pause = number_format($this->app->erp->ZeitGesamtPause($this->app->User->GetAdresse(),$datum),2,",","");
      $wocheist = number_format($this->app->erp->ZeitGesamtWocheIst($this->app->User->GetAdresse(),"","",$datum),2,",","");
      $monatist = number_format($this->app->erp->ZeitGesamtMonatIst($this->app->User->GetAdresse(),"","",$datum),2,",","");
      $monatsoll = number_format($this->app->erp->ZeitGesamtMonatSoll($this->app->User->GetAdresse(),"","",$datum),2,",","");
    //}
    $this->app->Tpl->Set('HEUTE',$heute);
    $this->app->Tpl->Set('PAUSE',$pause);
    $this->app->Tpl->Set('WOCHEIST',$wocheist);
    $this->app->Tpl->Set('MONATIST',$monatist);
    $this->app->Tpl->Set('MONATSOLL',$monatsoll);

    $offen = $this->app->erp->ZeitGesamtWocheOffen($this->app->User->GetAdresse());
    if($offen > 0)
    {
      $this->app->Tpl->Set('WOCHESOLL',number_format($this->app->erp->ZeitGesamtWocheSoll($this->app->User->GetAdresse(),"","",$datum),2,",",""));
      $this->app->Tpl->Set('OFFEN',"<font color=blue>".number_format($offen,2,",","")."</font>");
    } else if ($offen < 0)
    {
      $this->app->Tpl->Set('WOCHESOLL','-');
      $this->app->Tpl->Set('OFFEN','-');
    }
    else {
      $this->app->Tpl->Set('WOCHESOLL',number_format($this->app->erp->ZeitGesamtWocheSoll($this->app->User->GetAdresse(),"","",$datum),2,",",""));
      $this->app->Tpl->Set('OFFEN',number_format($offen,2,",",""));
    }
    
    $this->app->Tpl->Set('URLAUBOFFEN',number_format($this->app->erp->ZeitUrlaubOffen($this->app->User->GetAdresse(), $datum),2,",",""));
    $this->app->Tpl->Set('URLAUBGENOMMEN',number_format($this->app->erp->ZeitUrlaubGenommen($this->app->User->GetAdresse(), $datum),2,",",""));
    
  }



  function ZeiterfassungList()
  {
    //		$this->app->Tpl->Set(UEBERSCHRIFT,"Zeiterfassung");
    $this->ZeiterfassungMenu();

    if($this->app->Secure->GetGET('do')=='stornieren'){
      $lid = $this->app->Secure->GetGET('lid');
      $back = $this->app->Secure->GetGET('back');
      if($lid!='')
      {
        $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id=$lid");

        if($back=='zeiterfassung')
          header('Location: index.php?module=zeiterfassung&action=list');
        else if($back=='zeiterfassunguser')
          header('Location: index.php?module=zeiterfassung&action=listuser');
        else if($back=='service')
          header('Location: index.php?module=service&action=list');
        else if($back=='aufgabe')
          header('Location: index.php?module=aufgaben&action=list');
        else if($back=='zeiterfassungmitarbeiter')
        {
          $sid = $this->app->Secure->GetGET('sid');
          header('Location: index.php?module=adresse&action=zeiterfassung&id=$sid');
        }
        else if ($back=='projekt')
        {
          $back_id = $this->app->Secure->GetGET('back_id');
          $back_sid = $this->app->Secure->GetGET('back_sid');
          header("Location: index.php?module=projekt&action=zeit&id=$back_id&sid=$back_sid");
        }
        else if ($back=='lohnabrechnung')
        {
          header('Location: index.php?module=lohnabrechnung&action=list');
        }
        else if ($back=='adresse')
        {
          $back_id = $this->app->Secure->GetGET('back_id');
          header("Location: index.php?module=adresse&action=abrechnungzeit&id=$back_id");                  
        }
        else
          header('Location: index.php?module=zeiterfassung&action=create#tabs-1');
        exit;
      }
    }

    $this->app->YUI->DatePicker('von');
    $this->app->YUI->DatePicker('bis');
    $this->app->YUI->AutoComplete('projekt','projektname');
    $this->app->YUI->AutoComplete('mitarbeiter','mitarbeiter');
    $this->app->YUI->AutoComplete('mitarbeiterkalenderansicht','mitarbeiter');

    $this->app->YUI->TableSearch('TAB1','zeiterfassung', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('TAB2','zeiterfassungkundenoffen', 'show','','',basename(__FILE__), __CLASS__);
    //$this->app->YUI->TableSearch('TAB3',"zeiterfassungprojektoffen");

    $mitarbeiterkalenderansicht = $this->app->Secure->GetPOST('mitarbeiterkalenderansicht');
    if($mitarbeiterkalenderansicht!='')
    {
      $tmpmitarbeiter = strtok($mitarbeiterkalenderansicht, ' ');
      $mitarbeiterid = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$tmpmitarbeiter' AND mitarbeiternummer!='' LIMIT 1");
      $this->app->User->SetParameter('zeiterfassung_list_mitarbeiterkalenderansicht',$mitarbeiterid);
    }

    $mitarbeiterid = $this->app->User->GetParameter('zeiterfassung_list_mitarbeiterkalenderansicht');
    $mitarbeiterkalenderansicht = $this->app->DB->Select("SELECT concat(mitarbeiternummer,' ',name) FROM adresse WHERE id='".$mitarbeiterid."'");
    $this->app->Tpl->Set('MITARBEITERKALENDERANSICHT',$mitarbeiterkalenderansicht);

    //TODO wenn man das Recht  hat
    /*if(0)
    {
      $this->app->Tpl->Add('TAB1',
          "<table width=\"100%\"><tr><td align=\"center\"><input type=\"submit\" value=\"in Arbeitsnachweis &uuml;bernehmen\">&nbsp;".
          "<input type=\"submit\" value=\"in Auftrag &uuml;bernehmen\">&nbsp;".
          "<input type=\"submit\" value=\"in Lieferschein &uuml;bernehmen\">&nbsp;<input type=\"submit\" value=\"als abgeschlossen markieren\"></td></tr></table>");
    }*/


    $this->app->Tpl->Parse('PAGE','zeiterfassunguebersicht.tpl');
  }


  function ZeiterfassungMenu()
  {
    $this->app->erp->Headlines('Zeiterfassung');
    $this->app->erp->MenuEintrag('index.php?module=zeiterfassung&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=zeiterfassung&action=create&cmd=extern','Neue Zeiterfassung');
  }


  public function ZeiterfassungEdit()
  {
    $back = $this->app->Secure->GetGET('back');
    if($back=='zeiterfassung'){
      $this->ZeiterfassungMenu();
    }
    else{
      $this->app->erp->StartseiteMenu();
    }

    $this->ZeiterfassungManuell($adr_id,true);

    //parent::ZeiterfassungEdit();
  }

  function ArbeitspaketReadDetails($index,&$ref)
  {
    $pakete = $ref->app->DB->SelectArr("SELECT * FROM arbeitspakete WHERE id='$index'");
    $myArr = $pakete[0];

    $ref->app->Tpl->Set('AUFGABE', $myArr['aufgabe']);
    $ref->app->Tpl->Set('PROJEKT', $myArr['projekt']);
    $this->app->Tpl->Set('BESCHREIBUNG', $myArr['beschreibung']);
    $this->app->Tpl->Set('ZEITGEPLANT', $myArr['zeit_geplant']);
    $this->app->Tpl->Set('KOSTENSTELLE', $myArr['kostenstelle']);
    $this->app->Tpl->Set('STATUS', $myArr['status']);
  }

  public function ArbeitspaketDetails()
  {
    $this->app->Tpl->Set('HEADING','Details zum Arbeitspaket');

    $this->app->Tpl->Set('SUBSUBHEADING', 'Details');
    $this->app->Tpl->Set('DATUM', date('d.m.Y', time()));

    $this->app->Tpl->Add('TABS',
        '<li><h2>Zeiterfassung</h2></li>');

    $this->app->Tpl->Add('TABS',
        "<li><a href=\"index.php?module=zeiterfassung&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");

    // Adress-ID mit USER-ID abfragen
    $id =  $this->app->User->GetId();
    if($id!=''){
      $adr_id = $this->app->DB->Select("SELECT adresse FROM user WHERE id=$id");
    }

    $ap_id = $this->app->Secure->GetGET('lid');

    $pakete = $this->app->DB->SelectArr('SELECT * FROM arbeitspaket WHERE id='.$ap_id.' AND adresse='.$adr_id);
    $myArr = $pakete[0];    

    $this->app->Tpl->Set('AUFGABE', $myArr['aufgabe']);
    $this->app->Tpl->Set('PROJEKT', $myArr['projekt']);
    $this->app->Tpl->Set('BESCHREIBUNG', $myArr['beschreibung']);
    $this->app->Tpl->Set('ZEITGEPLANT', $myArr['zeit_geplant']);
    $this->app->Tpl->Set('KOSTENSTELLE', $myArr['kostenstelle']);
    $this->app->Tpl->Set('STATUS', $myArr['status']);

    if($myArr['abgabe'] == 'abgegeben'){
      $this->app->Tpl->Set('ABGABE', 'fertig');
    }
    else{
      $this->app->Tpl->Set('ABGABE', '<input type="checkbox" name="abgabefeld" value="nicht abgegeben">');
    }

    if($this->app->Secure->GetPOST("abgabefeld") == 'nicht abgegeben'){
      $this->app->DB->Update('UPDATE arbeitspakete SET abgabe="abgegeben", abgabedatum="'.date('Y-m-d').'" WHERE id='.$myArr['id']);
      $myArr['abgabe'] = 'abgegeben';
    }
    $this->app->Tpl->Parse('INHALT', 'arbeitspaket_details.tpl');
    $this->app->Tpl->Parse('PAGE','rahmen_submit_extend.tpl');
  }


  function ZeiterfassungManuell($adr_id)
  {
    $this->app->Tpl->Set('HEADING','Zeiterfassung (&Uuml;bersicht)');

    $this->app->Tpl->Set('SUBSUBHEADING', 'Zeit erfassen');

    $datumzeiterfassung = $this->app->Secure->GetPOST('datumzeiterfassung');
    $datumzeiterfassung = $this->app->String->Convert($datumzeiterfassung ,'%1.%2.%3','%3-%2-%1');

    if($this->app->erp->CheckDateValidate($datumzeiterfassung)) {
      $this->app->User->SetParameter('zeiterfassung_create_datumzeiterfassung',$datumzeiterfassung);
    }

    $datumzeiterfassung = $this->app->User->GetParameter('zeiterfassung_create_datumzeiterfassung');

    if(!$this->app->erp->CheckDateValidate($datumzeiterfassung)) {
      $datumzeiterfassung = date('Y-m-d');
    }

    $this->app->Tpl->Set('ZURUECKDATUM',$this->app->DB->Select("SELECT DATE_FORMAT(DATE_SUB('$datumzeiterfassung',INTERVAL 1 DAY),'%d.%m.%Y')"));
    $this->app->Tpl->Set('VORWAERTSDATUM',$this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD('$datumzeiterfassung',INTERVAL 1 DAY),'%d.%m.%Y')"));


    $this->app->Tpl->Set('DATUMZEITERFASSUNG',$this->app->String->Convert($datumzeiterfassung,'%3-%2-%1','%1.%2.%3'));
    if($datumzeiterfassung!=date('Y-m-d')){
      $this->app->Tpl->Set('ANZEIGEDATUMZEITERFASSUNG', $this->app->String->Convert($datumzeiterfassung, '%3-%2-%1', '%1.%2.%3'));
    }
    else{
      $this->app->Tpl->Set('ANZEIGEDATUMZEITERFASSUNG', 'heute');
    }


    if($this->app->erp->ModulVorhanden('fahrtenbuch'))
    {
      $spalte = "if((SELECT f.id FROM fahrtenbuch_fahrten f WHERE f.zeiterfassung=z.id) > 0,'ja','-') as Fahrt,";
    } else {
      $spalte='';
    }

    //DATE_FORMAT(z.bis, '%d.%m.') AS Datum, 
    $table = new EasyTable($this->app);
    $table->Query("SELECT 
        if(z.adresse_abrechnung > 0, CONCAT('<i style=color:#999>Kunde: ', a.name, ' (', a.kundennummer,')</i><br />', z.aufgabe), z.aufgabe) as Taetigkeit,
        DATE_FORMAT(z.von,'%H:%i') AS Von,
        CONCAT(TIME_FORMAT(TIMEDIFF(z.bis, z.von),'%H:%i'),IF(z.abrechnen=1,' (A)','')) AS Dauer,
        p.abkuerzung as Projekt,
        $spalte

        CONCAT('<a href=\"#\" onclick=\"if(!confirm(\'Wirklich stornieren?\')) return false; else window.location.href=\'index.php?module=zeiterfassung&action=list&do=stornieren&lid=', z.id, '\'\"><img src=\"./themes/new/images/delete.svg\"></a>&nbsp;<a href=\"index.php?module=zeiterfassung&action=create&id=', z.id, '#tabs-1\"><img src=\"./themes/new/images/edit.svg\"></a>&nbsp;<a href=\"index.php?module=zeiterfassung&action=create&copyid=',z.id,'%&back=zeiterfassunguser\"><img src=\"./themes/new/images/copy.svg\"</a>')
        FROM zeiterfassung z LEFT JOIN adresse a ON z.adresse_abrechnung = a.id LEFT JOIN projekt p ON p.id=z.projekt LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id
        WHERE z.gebucht_von_user=".$this->app->User->GetID()."  AND DATE_FORMAT(z.von, '%Y-%m-%d')  = '$datumzeiterfassung'
        ORDER BY z.von
        ");
    $table->DisplayNew('BUCHUNGEN','Aktion','noAction','false',0,0,false);
    $this->app->YUI->DatePicker('datumzeiterfassung');


    //wenn id und kein post einmalig aus db holen    
    $id = $this->app->Secure->GetGET('id');
    $id_old = $id;
    $copyid = $this->app->Secure->GetGET('copyid');
    if(empty($id) && !empty($copyid))
    {
      $id = $copyid;
    }
    $projektabgeschlossen = false;
    if($id!='' && $this->app->Secure->GetPOST('art')=='')
    {
      $tmp = $this->app->DB->SelectArr("SELECT *,DATE_FORMAT(von,'%H:%i') as von, if(bis!='0000-00-00 00:00:00',DATE_FORMAT(bis,'%H:%i'),'') as bis, DATE_FORMAT(von,'%d.%m.%Y') as datum FROM zeiterfassung WHERE  id='$id' LIMIT 1");
      $aufgabe = $tmp[0]['aufgabe'];
      $art = $tmp[0]['art'];
      $ort = $tmp[0]['ort'];
      $gps = $tmp[0]['gps'];
      $beschreibung = $tmp[0]['beschreibung'];
      $internerkommentar = $tmp[0]['internerkommentar'];
      $paketauswahl = $tmp[0]['arbeitspaket'];
      $abrechnen = $tmp[0]['abrechnen'];
      $abgerechnet = $tmp[0]['abgerechnet'];


      $this->app->User->SetParameter('teilprojekt_filter',$tmp[0]['projekt']);

      //      if($paketauswahl > 0){
      //        $this->app->Tpl->Set(PROJEKTROW,"none");
      //      }

      if($tmp[0]['adresse']!=$this->app->User->GetAdresse())
      {
        $this->app->Tpl->Set('ANDERERMITARBEITER','checked');
        $this->app->Tpl->Set('DISPLAYANDERERMITARBEITER','');
      } else {
        $this->app->Tpl->Set('DISPLAYANDERERMITARBEITER','none');
      }

      $mitarbeiter = $this->app->DB->Select("SELECT CONCAT(mitarbeiternummer,' ',name) FROM adresse WHERE id='".$tmp[0]['adresse']."'");
      $adresse_abrechnung = $tmp[0]['adresse_abrechnung'];
      $projektabgeschlossen = $this->app->DB->Select("SELECT id FROM projekt WHERE id = '".$tmp[0]['projekt']."' AND status = 'abgeschlossen' LIMIT 1");
      $projekt_komplett = $this->app->DB->Select("SELECT CONCAT(abkuerzung,' ',name) FROM projekt WHERE id='".$tmp[0]['projekt']."'");
      $serviceauftrag_anzeige = $this->app->DB->Select("SELECT belegnr FROM serviceauftrag WHERE id='".$tmp[0]['serviceauftrag']."' LIMIT 1");
      $adresse_abrechnung_komplett = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='".$tmp[0]['adresse_abrechnung']."'");

      if($tmp[0]['adresse_abrechnung']<=0){
        $adresse_abrechnung_komplett = '';
      }

      $mitarbeiter_komplett = $this->app->DB->Select("SELECT CONCAT(mitarbeiternummer,' ',name) FROM adresse WHERE id='".$tmp[0]['adresse']."'");
      $kostenstelle_komplett = $this->app->DB->Select("SELECT CONCAT(nummer,' ',beschreibung) FROM kostenstellen WHERE nummer='".$tmp[0]['kostenstelle']."'");
      $verrechnungsart_komplett = $this->app->DB->Select("SELECT CONCAT(nummer,' ',beschreibung) FROM verrechnungsart WHERE nummer='".$tmp[0]['verrechnungsart']."'");
      $auftrag_komplett = $this->app->DB->Select("SELECT CONCAT(belegnr,' ',name,' ', DATE_FORMAT(datum,'%d.%m.%Y')) FROM auftrag WHERE id='".$tmp[0]['auftrag']."'");
      $auftragpositionid_komplett = $this->app->DB->Select("SELECT CONCAT(a.belegnr,'-',ap.sort,' ',a.name,' ',DATE_FORMAT(a.datum,'%d.%m.%Y'),' ',ap.bezeichnung) FROM auftrag_position ap LEFT JOIN auftrag a ON ap.auftrag=a.id WHERE ap.id='".$tmp[0]['auftragpositionid']."'");
      $produktion_komplett = $this->app->DB->Select("SELECT CONCAT(belegnr,' ',name,' ', DATE_FORMAT(datum,'%d.%m.%Y')) FROM produktion WHERE id='".$tmp[0]['produktion']."'");

      $vonZeit = $tmp[0]['von'];
      $bisZeit = $tmp[0]['bis'];
      $datum = $tmp[0]['datum'];
    } else {
      if($this->app->Secure->GetPOST('datum')=='') {
        $datumzeiterfassung = $this->app->User->GetParameter('zeiterfassung_create_datumzeiterfassung');
        if($this->app->erp->CheckDateValidate($datumzeiterfassung)){
          $datum = $this->app->String->Convert($this->app->User->GetParameter('zeiterfassung_create_datumzeiterfassung'), '%3-%2-%1', '%1.%2.%3');
        }
        else{
          $datum = date('d.m.Y', time());
        }
      }
      else {
        $datum = $this->app->Secure->GetPOST('datum');
        $this->app->User->SetParameter('zeiterfassung_create_datumzeiterfassung',$this->app->String->Convert($datum,'%1.%2.%3','%3-%2-%1'));
      }

      $aufgabe = $this->app->Secure->GetPOST('aufgabe');



      $art= $this->app->Secure->GetPOST('art');

      $vonZeit = $this->app->Secure->GetPOST('vonZeit');
      $bisZeit = $this->app->Secure->GetPOST('bisZeit');

      $beschreibung = $this->app->Secure->GetPOST('beschreibung');
      $internerkommentar = $this->app->Secure->GetPOST('internerkommentar');
      $projekt = $this->app->Secure->GetPOST('projekt_manuell');
      $serviceauftrag = $this->app->Secure->GetPOST('serviceauftrag');
      $paketauswahl = $this->app->Secure->GetPOST('arbeitspaket');
      $abrechnen = $this->app->Secure->GetPOST('abrechnen');
      $abgerechnet = $this->app->Secure->GetPOST('abgerechnet');

      $auftrag = $this->app->Secure->GetPOST('auftrag');
      $produktion = $this->app->Secure->GetPOST('produktion');
      $auftragpositionid = $this->app->Secure->GetPOST('auftragpositionid');

      $mitarbeiter = $this->app->Secure->GetPOST('mitarbeiter');
      $kostenstelle = $this->app->Secure->GetPOST('kostenstelle');
      $verrechnungsart = $this->app->Secure->GetPOST('verrechnungsart');


      $ort = $this->app->Secure->GetPOST('ort');
      $gps = $this->app->Secure->GetPOST('gps');
      $adresse_abrechnung = $this->app->Secure->GetPOST('adresse_abrechnung');

      $mitarbeiter_komplett = $mitarbeiter;
      $kostenstelle_komplett = $kostenstellen;
      $verrechnungsart_komplett = $verrechnungsart;
      $auftrag_komplett = $verrechnungsart;
      $produktion_komplett = $verrechnungsart;

      $projekt_komplett = $projekt;
      $serviceauftrag_anzeige = $this->app->Secure->GetPOST('serviceauftrag');
      $adresse_abrechnung_komplett = $adresse_abrechnung;

      // Projekt grabben und notfalls wieder anzeigen
      $paketauswahl = strstr($paketauswahl, ' ', true);
      $paketauswahl = $this->app->DB->Select("SELECT id FROM arbeitspaket WHERE id='".$paketauswahl."' LIMIT 1");


      //      if($paketauswahl > 0)
      //        $this->app->Tpl->Set(PROJEKTROW,'none');

      if($mitarbeiter!=''){
        $this->app->Tpl->Set('ANDERERMITARBEITER','checked');
      }
      else {
        $this->app->Tpl->Set('DISPLAYANDERERMITARBEITER','none');
      }
    }
    $id = $id_old;
    $datum_db = $this->app->String->Convert($datum,'%1.%2.%3','%3-%2-%1');
    $zeiteedit = $this->app->erp->ZeiterfassungAllowEdit($datum_db);

    $string = $mitarbeiter;	
    $string = trim ($string);
    $mitarbeiter = substr ($string , 0 , strpos ($string , ' '));

    $string = $kostenstelle;	
    $string = trim ($string);
    $kostenstelle = substr ($string , 0 , strpos ($string , ' '));

    $string = $verrechnungsart;
    $string = trim ($string);
    $verrechnungsart = substr ($string , 0 , strpos ($string , ' '));

    $string = $auftrag;
    $string = trim ($string);
    $auftrag = substr ($string , 0 , strpos ($string , ' '));
    $auftrag = $this->app->DB->Select("SELECT id FROM auftrag WHERE belegnr='$auftrag' AND belegnr!='' LIMIT 1");

    $string = $auftragpositionid;
    $string = trim ($string);
    $string = substr ($string , 0 , strpos ($string , ' '));
    $tmpauftrag = substr ($string , 0 , (strrpos ($string , '-')));
    $tmpauftrag = $this->app->DB->Select("SELECT id FROM auftrag WHERE belegnr='$tmpauftrag' AND belegnr!='' LIMIT 1");
    $auftragpositionsort = substr ($string , strrpos ($string , '-')+1,strlen($string));
    $auftragpositionid = $this->app->DB->Select("SELECT id FROM auftrag_position WHERE auftrag='$tmpauftrag' AND sort='$auftragpositionsort' LIMIT 1");

    $string = $produktion;
    $string = trim ($string);
    $produktion = substr ($string , 0 , (strpos ($string , ' ')));	
    $produktion = $this->app->DB->Select("SELECT id FROM produktion WHERE belegnr='$produktion' AND belegnr!='' LIMIT 1");



    if($mitarbeiter!=''){
      $adr_id = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$mitarbeiter' LIMIT 1");
    }

    $this->app->YUI->AutoSaveUserParameter('projekt_manuell','teilprojekt_filter');
    $this->app->YUI->AutoSaveUserParameter('projekt_manuell2','teilprojekt_filter');
    $this->app->YUI->AutoComplete('arbeitspaket','arbeitspaket');
    $this->app->YUI->AutoComplete('arbeitspaket2', 'arbeitspaket');
    $this->app->YUI->AutoComplete('schnell_teilprojekt','arbeitspaket');
    $this->app->YUI->AutoComplete('adresse_abrechnung','kunde');
    $this->app->YUI->AutoComplete('adresse_abrechnung2', 'kunde');
    $this->app->YUI->AutoComplete('schnell_kunde','kunde');

    $checkvorlage = $this->app->DB->Select("SELECT COUNT(id) FROM zeiterfassungvorlage WHERE ausblenden!=1");

    if($checkvorlage >= 1)
    {
//      $this->app->YUI->AutoComplete('aufgabe','zeiterfassungvorlage');
//      $this->app->YUI->AutoComplete('aufgabe2','zeiterfassungvorlage');
      $this->app->YUI->AutoComplete('schnell_beschreibung','zeiterfassungvorlage');
      $this->app->YUI->AutoComplete('schnell_kundebeschreibung','zeiterfassungvorlage');
    }

    $this->app->YUI->AutoComplete('mitarbeiter','mitarbeiter');

    $this->app->YUI->AutoComplete('kostenstelle','kostenstelle');
    $this->app->YUI->AutoComplete('verrechnungsart','verrechnungsart');

    $this->app->YUI->AutoComplete('auftrag','auftrag');
    $this->app->YUI->AutoComplete('auftragpositionid','alle_auftrag_positionen');
    $this->app->YUI->AutoComplete('produktion','produktion');

    if($id=='' && $this->app->Secure->GetPOST('vonZeit')=='')
    {
      if($datumzeiterfassung=='') {
        $datumzeiterfassung = date('Y-m-d');
      }
      $vonZeit =  $this->app->DB->Select("SELECT DATE_FORMAT(MAX(bis),'%H:%i') FROM zeiterfassung 
          WHERE adresse='$adr_id' AND DATE_FORMAT(bis,'%Y-%m-%d')=DATE_FORMAT('$datumzeiterfassung','%Y-%m-%d')");

      if($this->app->erp->ModulVorhanden('mitarbeiterzeiterfassung'))
      {
        if(!$vonZeit){
          $vonZeit = $this->app->DB->Select("SELECT date_format(standardstartzeit,'%H:%i') FROM mitarbeiterzeiterfassung_einstellungen WHERE adresse = '".$this->app->User->GetAdresse()."' ORDER by id DESC LIMIT 1");
        }
      }
    }


    $this->app->Tpl->Set('VONZEIT',!empty($vonZeit)?$vonZeit:'09:00');

    if($vonZeit==$bisZeit) {
      $bisZeit='';
    }
    $this->app->Tpl->Set('BISZEIT',$bisZeit?$bisZeit:'');
    $serviceauftrag = $this->app->Secure->GetPOST('serviceauftrag');
    $serviceauftrag = reset(explode(' ',$serviceauftrag));
    $serviceauftrag = $this->app->DB->Select("SELECT id FROM serviceauftrag WHERE belegnr='".$serviceauftrag."' LIMIT 1");
    
    // Projekt grabben und notfalls wieder anzeigen
    $projekt_kennung = reset(explode(' ',$projekt));
    $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$projekt_kennung."' LIMIT 1");

    // Kunde
    $adresse_abrechnung = strstr($adresse_abrechnung, ' ', true);
    if($adresse_abrechnung!=''){
      $adresse_kunde = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$adresse_abrechnung."' LIMIT 1");
    }
    else{
      $adresse_kunde = 'NULL';
    }



    if($abrechnen=='1'){
      $this->app->Tpl->Set('ABRECHNEN', 'checked');
    }

    if($abgerechnet=='1'){
      $this->app->Tpl->Set('ABGERECHNET', 'checked');
    }

    $this->app->Tpl->Set('DATUM', $datum);
    $this->app->Tpl->Set('PROJEKT_MANUELL',$projekt_komplett);
    $this->app->Tpl->Set('SERVICEAUFTRAG',$serviceauftrag_anzeige);
    
    $this->app->Tpl->Set('ADRESSE_ABRECHNUNG',$adresse_abrechnung_komplett);
    $this->app->Tpl->Set('KOSTENSTELLE',$kostenstelle_komplett);
    $this->app->Tpl->Set('VERRECHNUNGSART',$verrechnungsart_komplett);

    $this->app->Tpl->Set('AUFTRAG',$auftrag_komplett);
    $this->app->Tpl->Set('AUFTRAGPOSITIONID',$auftragpositionid_komplett);
    $this->app->Tpl->Set('PRODUKTION',$produktion_komplett);

    $this->app->Tpl->Set('AUFGABE',str_replace('"', '&quot;', $aufgabe));
    $this->app->Tpl->Set('MITARBEITER',$mitarbeiter_komplett);
    $this->app->Tpl->Set('ORT',$ort);
    $this->app->Tpl->Set('GPS',$gps);
    $this->app->Tpl->Set('BESCHREIBUNG',str_replace('\r\n',"\r\n",$beschreibung));
    $this->app->Tpl->Set('INTERNERKOMMENTAR',str_replace('\r\n',"\r\n",$internerkommentar));

    if($gps!='')
    {
      $tmpgps = explode(';',$gps);
      $this->app->Tpl->Set('GPSIMAGE',"<img width='180' height='180' src='http://maps.google.com/maps/api/staticmap?center=".$tmpgps[0].",".$tmpgps[1].
          '&markers=size:small|color:blue|'.$tmpgps[0].",".$tmpgps[1]."&zoom=14&size=180x180&sensor=false' />");
    } else {
      $this->app->Tpl->Set('GPSBUTTON','<input type="button" value="GPS Daten laden" onclick="Standpunkt();">');	
    }
    $this->app->Tpl->Set('ART', $this->app->erp->GetSelect($this->app->erp->GetZeiterfassungArt(),$art));

    $pakete = $this->app->DB->SelectArr('SELECT id, aufgabe,art,projekt FROM arbeitspaket WHERE (adresse = '.$adr_id.' or (art="teilprojekt" '.$this->app->erp->ProjektRechte("projekt").')) AND abgenommen!=1 AND geloescht!=1 AND status!=\'abgeschlossen\' order by projekt');
    /*
       $select = '<option value="0">-- kein --</option>';
       for($i =0; $i<sizeof($pakete); $i++){
       $myArr = $pakete[$i];
       if($myArr["art"]=="") $myArr["art"]="arbeitspaket";
       $projekt_ap = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$myArr["projekt"]."' LIMIT 1");
       if($paketauswahl==$myArr["id"])$checked=" selected"; else $checked ="";
       $select = $select.'<option value="'.$myArr["id"].'" '.$checked.'>Projekt: '.$projekt_ap.'->'.ucfirst($myArr["art"]).': '.$myArr["aufgabe"].'</option>';  
       }
     */
    if($paketauswahl > 0) {
      $tmp = $this->app->DB->Select("SELECT CONCAT(ap.id,' ',p.abkuerzung,' ',ap.aufgabe) as name2 FROM arbeitspaket ap LEFT JOIN projekt p ON p.id=ap.projekt WHERE ap.id='$paketauswahl' LIMIT 1");
      $this->app->Tpl->Set('PAKETAUSWAHL', $tmp);
    }

    $checkvon = str_replace(':','',$this->app->Secure->GetPOST('vonZeit'));
    $checkbis = str_replace(':','',$this->app->Secure->GetPOST('bisZeit'));
    $fehlerangezeigt = false;

    if($this->app->Secure->GetPOST('ok')){
      $vonZeit = $this->app->String->Convert($datum,'%1.%2.%3','%3-%2-%1').' '.$this->app->Secure->GetPOST('vonZeit').':00';
      $bisZeit = $this->app->String->Convert($datum,'%1.%2.%3','%3-%2-%1').' '.$this->app->Secure->GetPOST('bisZeit').':00';



      if($paketauswahl == 0){
        if(($aufgabe!='') && ($this->app->Secure->GetPOST('vonZeit')!='') && ($this->app->Secure->GetPOST('bisZeit')!='') && ($datum!='') && ($checkbis > $checkvon) && $zeiteedit && !$this->app->DB->Select("SELECT id FROM projekt WHERE id = '$projekt' AND status = 'abgeschlossen' LIMIT 1")){	
          {
            // Hier fehlt abrechnen und adresse_abrechnung
            $zeitid = $this->app->erp->AddArbeitszeit($adr_id, $vonZeit, $bisZeit, $aufgabe, $beschreibung, $ort, 
                $projekt, 0,$art,$adresse_kunde,$abrechnen,$verrechnungsart,$kostenstelle,$abgrechnet,$gps,0,$internerkommentar,$auftrag,$produktion,"",$auftragpositionid);
            $this->app->erp->RunHook('zeiterfassung_create_guihook1',1,$zeitid);

            if($serviceauftrag){
              $this->app->DB->Update("UPDATE zeiterfassung SET serviceauftrag = '$serviceauftrag' WHERE id = '$zeitid' LIMIT 1");
            }
            $this->app->Tpl->Set('MESSAGE',"<div class=\"success\">Zeit erfolgreich gebucht!</div>");
            $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Zeit erfolgreich gebucht!</div>  ");
            header("Location: index.php?module=zeiterfassung&action=create&msg=$msg#tabs-1");
            exit;
          }
        }else{
          if($this->app->DB->Select("SELECT id FROM projekt WHERE id = '$projekt' AND status = 'abgeschlossen' LIMIT 1"))
          {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Das Projekt '.$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projekt' LIMIT 1").' ist bereits abgeschlossen.</div>');
            $this->app->Tpl->Set('MESSAGE','<div class="error">Das Projekt '.$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projekt' LIMIT 1").' ist bereits abgeschlossen.</div>');
          }
          elseif(!$zeiteedit)
          {
            $fehlerangezeigt = true;
            $this->app->Tpl->Set('MESSAGE','<div class="error">Fehler! Das Anlegen/Editieren von Zeiten älter als '.$this->app->erp->Firmendaten('zeiterfassung_schliessentage').' Tagen ist nicht erlaubt!</div>');
          }elseif($checkbis <= $checkvon){
            $this->app->Tpl->Set('MESSAGE', '<div class="error">Fehler! Zeit bis ist kleiner bzw gleich als Zeit von!</div>');
          }
          else{
            $this->app->Tpl->Set('MESSAGE', '<div class="error">Fehler! Die Felder "Kurze Beschreibung", am, von, bis m&uuml;ssen korrekt ausgef&uuml;llt sein!</div>');
          }
          //          $this->app->Tpl->Add('JQUERYREADY', '$("#accordion").accordion({ active: 2, event: "click" });');
        }
      }else{
        if(($this->app->Secure->GetPOST('vonZeit')!='') && ($this->app->Secure->GetPOST('bisZeit')!='') && ($datum!='') && $aufgabe!='' && ($checkbis > $checkvon) && $zeiteedit && !$this->app->DB->Select("SELECT id FROM projekt WHERE id = '$projekt' AND status = 'abgeschlossen' LIMIT 1")){
          //Paketauswahl buchen ...
          $projekt = $this->app->DB->Select("SELECT projekt FROM arbeitspaket WHERE id='$paketauswahl' LIMIT 1");
          if($this->app->DB->Select("SELECT id FROM projekt WHERE id = '$projekt' AND status = 'abgeschlossen' LIMIT 1"))
          {
            $this->app->Tpl->Set('MESSAGE','<div class="error">Das Projekt '.$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projekt' LIMIT 1").' ist bereits abgeschlossen.</div>');
          }else{
            $kunde = $this->app->DB->Select("SELECT kunde FROM projekt WHERE id='$projekt' LIMIT 1");
            if($adresse_kunde=='') {
              $adresse_kunde=$kunde;
            }


            $zeitid = $this->app->erp->AddArbeitszeit($adr_id, $vonZeit, $bisZeit, $aufgabe, $beschreibung, $ort, 
                $projekt, $paketauswahl,$art,$adresse_kunde,$abrechnen,$verrechnungsart,$kostenstelle,$abgerechnet,$gps,0,$internerkommentar,$auftrag,$produktion); 

            $this->app->erp->RunHook('zeiterfassung_create_guihook1',1,$zeitid);

            if($serviceauftrag){
              $this->app->DB->Update("UPDATE zeiterfassung SET serviceauftrag = '$serviceauftrag' WHERE id = '$zeitid' LIMIT 1");
            }
            $msg = $this->app->erp->base64_url_encode('<div class="success">Zeit erfolgreich gebucht!</div>  ');
            header("Location: index.php?module=zeiterfassung&action=create&msg=$msg#tabs-1");
            exit;
          }
        }else{
          if($this->app->DB->Select("SELECT id FROM projekt WHERE id = '$projekt' AND status = 'abgeschlossen' LIMIT 1"))
          {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Das Projekt '.$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projekt' LIMIT 1").' ist bereits abgeschlossen.</div>');
            $this->app->Tpl->Add('MESSAGE','<div class="error">Das Projekt '.$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projekt' LIMIT 1").' ist bereits abgeschlossen.</div>');
          }elseif(!$zeiteedit)
          {
            $fehlerangezeigt = true;
            $this->app->Tpl->Set('MESSAGE','<div class="error">Fehler! Das Anlegen/Editieren von Zeiten kleiner '.$this->app->erp->Firmendaten('zeiterfassung_schliessentage').' Tagen ist nicht erlaubt!</div>');
          }elseif($checkbis <= $checkvon){
            $this->app->Tpl->Set('MESSAGE', '<div class="error">Fehler! Zeit bis ist kleiner bzw gleich als Zeit von!</div>');
          }
          else{
            $this->app->Tpl->Set('MESSAGE', '<div class="error">Fehler! Die Felder "Kurze Beschreibung", am, von, bis m&uuml;ssen korrekt ausgef&uuml;llt sein!</div>');
          }
          //          $this->app->Tpl->Add('JQUERYREADY', '$("#accordion").accordion({ active: 2, event: "click" });');
        }
      }

      if($id==''){
        $vonZeit = $this->app->DB->Select("SELECT DATE_FORMAT(MAX(bis),'%H:%i') FROM zeiterfassung WHERE adresse='$adr_id' AND DATE_FORMAT(bis,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d')");
      }

    }  else {
      if($this->app->Secure->GetPOST('update')){
        if(($aufgabe!='') && ($this->app->Secure->GetPOST('vonZeit')!='') && ($this->app->Secure->GetPOST('bisZeit')!='') && ($datum!='') && ($checkbis > $checkvon) && $zeiteedit && !$this->app->DB->Select("SELECT id FROM projekt WHERE id = '$projekt' AND status = 'abgeschlossen' LIMIT 1")){	
          //				echo "update";
          //echo $datum;
          $vonZeit = $this->app->String->Convert($datum,'%1.%2.%3','%3-%2-%1').' '.$this->app->Secure->GetPOST('vonZeit').':00';
          $bisZeit = $this->app->String->Convert($datum,'%1.%2.%3','%3-%2-%1').' '.$this->app->Secure->GetPOST('bisZeit').':00';

          if($paketauswahl == 0)
          {
            $this->app->erp->UpdateArbeitszeit($id,$adr_id, $vonZeit, $bisZeit, $aufgabe, $beschreibung, $ort, 
                $projekt, $paketauswahl,$art,$adresse_kunde,$abrechnen,$verrechnungsart,$kostenstelle,$abgerechnet,$gps,$internerkommentar,$auftrag,$produktion,"",$auftragpositionid); 

            if($serviceauftrag){
              $this->app->DB->Update("UPDATE zeiterfassung SET serviceauftrag = '$serviceauftrag' WHERE id = '$id' LIMIT 1");
            }
          }
          else
          {
            $this->app->erp->UpdateArbeitszeit($id,$adr_id, $vonZeit, $bisZeit, $aufgabe, $beschreibung, $ort, 
                $projekt, $paketauswahl,$art,'',$abrechnen,$verrechnungsart,$kostenstelle,$gps,$internerkommentar,$auftrag,$produktion,'',$auftragpositionid); 

            if($serviceauftrag){
              $this->app->DB->Update("UPDATE zeiterfassung SET serviceauftrag = '$serviceauftrag' WHERE id = '$id' LIMIT 1");
            }
          }
          $this->app->erp->RunHook('zeiterfassung_create_guihook1');


          $back = $this->app->Secure->GetGET('back');
          if($back=='zeiterfassung'){
            header('Location: index.php?module=zeiterfassung&action=list');
          }
          else if($back=='zeiterfassunguser'){
            header('Location: index.php?module=zeiterfassung&action=listuser');
          }
          else if($back=='service'){
            header('Location: index.php?module=service&action=list');
          }
          else if($back=='aufgabe'){
            header('Location: index.php?module=aufgaben&action=list');
          }
          else if($back=='zeiterfassungmitarbeiter') 
          {
            $sid = $this->app->Secure->GetGET('sid');
            header("Location: index.php?module=adresse&action=zeiterfassung&id=$sid");
          }


          else if ($back=='projekt')
          {
            $back_id = $this->app->Secure->GetGET('back_id');
            $back_sid = $this->app->Secure->GetGET('back_sid');
            header("Location: index.php?module=projekt&action=zeit&id=$back_id&sid=$back_sid");	
          }
          else if ($back=='lohnabrechnung')
          {
            header('Location: index.php?module=lohnabrechnung&action=list');
          }
          else if ($back=='adresse')
          {
            $back_id = $this->app->Secure->GetGET('back_id');
            header("Location: index.php?module=adresse&action=abrechnungzeit&id=$back_id");	
          }
          else{
            header('Location: index.php?module=zeiterfassung&action=create#tabs-1');
          }
          exit;

        } else {
          if(!$this->app->DB->Select("SELECT id FROM projekt WHERE id = '$projekt' AND status = 'abgeschlossen' LIMIT 1"))
          {
            $this->app->Tpl->Add('MESSAGE','<div class="error">Das Projekt '.$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projekt' LIMIT 1").' ist bereits abgeschlossen.</div>');
          }elseif(!$zeiteedit)
          {
            $fehlerangezeigt = true;
            $this->app->Tpl->Set('MESSAGE','<div class="error">Fehler! Das Anlegen/Editieren von Zeiten kleiner '.$this->app->erp->Firmendaten('zeiterfassung_schliessentage').' Tagen ist nicht erlaubt!</div>');
          }elseif($checkbis <= $checkvon){
            $this->app->Tpl->Set('MESSAGE', '<div class="error">Fehler! Zeit bis ist kleiner bzw gleich als Zeit von!</div>');
          }
          else{
            $this->app->Tpl->Set('MESSAGE', '<div class="error">Fehler! Die Felder "Kurze Beschreibung", am, von, bis m&uuml;ssen ausgef&uuml;llt sein!</div>');
          }
          //          $this->app->Tpl->Add('JQUERYREADY', '$("#accordion").accordion({ active: 2, event: "click" });');
        }

      }
    }

    //tabelle mit gebuchten tätigkeiten heute
    if($id=='')
    {
      $table = new EasyTable($this->app);

      $table->Query("SELECT DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR')) AS Datum, 
          z.aufgabe as Taetigkeit,

          (SELECT name FROM adresse adr WHERE adr.id=z.adresse) as mitarbeiter,
          DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis,
          TIMEDIFF(z.bis, z.von) AS Dauer,

          p.abkuerzung as Projekt,
          ap.aufgabe as 'Unterprojekt/Arbeitspaket',


          IF(DATEDIFF(CURDATE(), z.bis)<= 5, 
            CONCAT('<a href=\"#\" onclick=\"if(!confirm(\'Wirklich stornieren?\')) return false; else window.location.href=\'index.php?module=zeiterfassung&action=list&do=stornieren&lid=', z.id, '\'\">Stornieren</a>&nbsp;|&nbsp;<a href=\"index.php?module=zeiterfassung&action=create&id=', z.id, '#tabs-1\">Edit</a>'), '')
          FROM zeiterfassung z LEFT JOIN projekt p ON p.id=z.projekt LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id
          WHERE z.gebucht_von_user=".$this->app->User->GetID()." AND DATE_FORMAT(z.bis,'%Y-%m-%d')= DATE_FORMAT(NOW(),'%Y-%m-%d')
          ORDER BY 1 DESC,2 DESC, 3 DESC
          ");
      $table->DisplayNew('TABELLE','Aktion','noAction');
      $this->app->Tpl->Set('BUTTON',"<input type=\"submit\" value=\"Buchen\" name=\"ok\">");
    } else {

      $back = $this->app->Secure->GetGET('back');

      if($back=='zeiterfassung'){
        $link = 'index.php?module=zeiterfassung&action=list';
      }
      else if($back=='service'){
        $link = 'index.php?module=service&action=list';
      }
      else if($back=='aufgabe'){
        $link = 'index.php?module=aufgaben&action=list';
      }
      else if ($back=='projekt')
      {
        $back_id = $this->app->Secure->GetGET('back_id');
        $back_sid = $this->app->Secure->GetGET('back_sid');
        $link = "index.php?module=projekt&action=zeit&id=$back_id&sid=$back_sid";
      }
      else if ($back=="adresse")
      {
        $back_id = $this->app->Secure->GetGET("back_id");
        $link = "index.php?module=adresse&action=abrechnungzeit&id=$back_id";                  
      }
      else{
        $link = 'index.php?module=zeiterfassung&action=create#tabs-1';
      }
      //      $this->app->Tpl->Add('JQUERYREADY', '$("#accordion").accordion({ active: 2, event: "click" });');
      if($zeiteedit && !$projektabgeschlossen)
      {
        $this->app->Tpl->Set('BUTTON',"
            <input type=\"submit\" value=\"Zeiterfassung jetzt &auml;ndern\" name=\"update\">&nbsp;
            <input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='".$link."'\">&nbsp;
            ");
      }else{
        if($projektabgeschlossen)
        {
          $this->app->Tpl->Add('MESSAGE','<div class="warning">Das Projekt '.$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projekt' LIMIT 1").' ist bereits abgeschlossen.</div>');
        }else{
          if(!$fehlerangezeigt)$this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Das Anlegen/Editieren von Zeiten kleiner ".$this->app->erp->Firmendaten('zeiterfassung_schliessentage')." Tagen ist nicht erlaubt!</div>");
        }
        $this->app->Tpl->Set('BUTTON',
            '<script>$(document).ready(function() {$(\'#accordion input\').prop(\'disabled\', true);});</script>'."<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='".$link."'\">&nbsp;");
      }
    }


    $this->app->YUI->DatePicker('datum');
    //$this->app->YUI->DatePicker('teilprojekt_datum');
    //$this->app->YUI->DatePicker('kunde_datum');
    $this->app->YUI->TimePicker('vonZeit');
    $this->app->YUI->TimePicker('bisZeit');



    if($this->app->erp->Firmendaten('zeiterfassung_anderemitarbeiter')!=1)
    {
      $this->app->Tpl->Set('STARTANDEREERLAUBEN','<!--');
      $this->app->Tpl->Set('ENDEANDEREERLAUBEN','-->');
    } 

    if($this->app->erp->Firmendaten('zeiterfassung_erweitert')!=1)
    {
      $this->app->Tpl->Set('STARTERWEITERT','<!--');
      $this->app->Tpl->Set('ENDEERWEITERT','-->');
    } 

    if($this->app->erp->Firmendaten('zeiterfassung_kommentar')!=1)
    {
      $this->app->Tpl->Set('STARTKOMMENTAR','<!--');
      $this->app->Tpl->Set('ENDEKOMMENTAR','-->');
    } 

    if($this->app->erp->Firmendaten('zeiterfassung_ort')!=1)
    {
      $this->app->Tpl->Set('STARTORT','<!--');
      $this->app->Tpl->Set('ENDEORT','-->');
    } 

    $this->app->YUI->AutoSaveUserParameter('projekt_manuell','teilprojekt_filter');

    if($this->app->erp->ModulVorhanden('serviceauftrag'))
    {
      $this->app->YUI->AutoComplete('serviceauftrag','serviceauftrag');
    }else{
      $this->app->Tpl->Set('VORSERVICEAUFTRAG','<!--');
      $this->app->Tpl->Set('NACHSERVICEAUFTRAG','-->');
    }

    $this->ZeiteerfassungEigeneUebersicht();

    $this->app->erp->RunHook('zeiterfassung_create_guihook1');

    $this->app->YUI->AutoSaveUserParameter('zeiterfassung_buchen_termine','zeiterfassung_buchen_termine',"$('#calendar').fullCalendar('refetchEvents');");
    $this->app->YUI->AutoSaveUserParameter('zeiterfassung_buchen_stechuhr','zeiterfassung_buchen_stechuhr',"$('#calendar').fullCalendar('refetchEvents');");
    if($this->app->User->GetParameter('zeiterfassung_buchen_termine')=='1') {
      $checked='checked';
    } else {
      $checked='';
    }
    $this->app->Tpl->Set('CHECKEDZEITERFASSUNGBUCHENTERMINE',$checked);


    if($this->app->User->GetParameter('zeiterfassung_buchen_stechuhr')=='1') {
      $checked='checked';
    } else {
      $checked='';
    }
    $this->app->Tpl->Set('CHECKEDZEITERFASSUNGBUCHENSTECHUHR',$checked);


    if($this->app->User->GetParameter('zeiterfassung_buchen_formularansicht')=='1')
    {
      $this->app->Tpl->Set('FORMULARANSICHT','<input type="button" class="btnBlueNew" value="{|Kalender-Ansicht als Standard|}" onclick="window.location.href=\'index.php?module=zeiterfassung&action=create&cmd=kalenderansicht\'">');
      $this->app->Tpl->Set('ZEITERFASSUNGTABS','<li><a href="#tabs-1">{|Formular|}</a></li><li id="list"><a href="#tabs-2">{|Kalenderansicht|}</a></li>');
    } else {
      $this->app->Tpl->Set('FORMULARANSICHT','<input type="button" class="btnBlueNew" value="{|Formular-Ansicht als Standard|}" onclick="window.location.href=\'index.php?module=zeiterfassung&action=create&cmd=formularansicht\'">');
      $this->app->Tpl->Set('ZEITERFASSUNGTABS','<li id="list"><a href="#tabs-2">{|Kalenderansicht|}</a></li><li><a href="#tabs-1">{|Formular|}</a></li>');
    }

    //$this->app->Tpl->Set(TABTEXT,'Zeiterfassung'); 
    $this->app->Tpl->Parse('TAB1', 'zeiterfassung_manuell.tpl');
    $this->app->Tpl->Parse('PAGE', 'zeiterfassung_buchen.tpl');
  }



  function ZeiterfassungOffen($adr_id)
  {
    $this->app->Tpl->Set('SUBSUBHEADING', 'offene Arbeitspakete');
    $this->app->Tpl->Set('INHALT','');

    $table = new EasyTable($this->app);
    $table->Query("SELECT DISTINCT a.aufgabe, a.zeit_geplant as vorgabe, 
        (SELECT FORMAT(SUM(TIMEDIFF(bis,von)/10000),2) FROM zeiterfassung WHERE arbeitspaket=a.id) as Gebucht, 
        DATE_FORMAT(a.abgabedatum,'%d.%m.%y') as bis, a.id
        FROM arbeitspaket a LEFT JOIN zeiterfassung z ON z.arbeitspaket=a.id
        WHERE a.adresse='$adr_id' AND (a.status = 'gestartet' OR a.status = 'besprochen')");

    $table->DisplayNew('INHALT','[DETAILS%value%]');
    $this->app->YUI->Dialog($table,'DETAILS','Arbeitspaket','aufgabe','arbeitspaket_details.tpl',$this,'ArbeitspaketReadDetails');

    $this->app->Tpl->Parse('ARBEITSPAKETE','rahmen70.tpl');

  }

  function ZeiterfassungAbgeschlossen($adr_id)
  {

    $monat = $this->app->DB->Select("SELECT DATE_FORMAT(NOW(),'%M')");
    $this->app->Tpl->Set('SUBSUBHEADING', "Zeiterfassung ($monat)");
    $this->app->Tpl->Set('INHALT','');
    $table = new EasyTable($this->app);

    //  SELECT SUM(hour(z.bis) - hour(z.von)) AS dauer, a.aufgabe, a.zeit_geplant FROM zeiterfassung AS z, arbeitspakete AS a WHERE z.adresse = 2 AND z.adresse = a.adresse AND z.arbeitspaket = a.id  AND a.id = 2 AND (status = "gestartet" OR status = "besprochen") GROUP BY aufgabe

    $table->Query("SELECT DATE_FORMAT(bis, GET_FORMAT(DATE,'EUR')) AS Datum, DATE_FORMAT(von,'%H:%i') as von, DATE_FORMAT(bis,'%H:%i') as bis,
        TIMEDIFF(bis, von) AS Dauer,
        aufgabe as Taetigkeit,
        IF(DATEDIFF(CURDATE(), bis)<= 5, 
          CONCAT('<a href=\"#\" onclick=\"if(!confirm(\'Wirklich stornieren?\')) return false; else window.location.href=\'index.php?module=zeiterfassung&action=list&do=stornieren&lid=', id, '\'\">Stornieren</a>&nbsp;|&nbsp;<a href=\"\">Edit</a>'), '')
        FROM zeiterfassung
        WHERE adresse=$adr_id
        AND (
          MONTH(bis) = MONTH(NOW()) OR DATEDIFF(CURDATE(), bis)<= 5
          )  
        ORDER BY 1 DESC,2 DESC
        ");

    $table->DisplayNew('INHALT','Aktion','noAction');


    // $this->app->Tpl->Set(INHALT,"Abgschlossen");
    $this->app->Tpl->Parse('ZEITERFASSUNGEN','rahmen70.tpl');
  }

  function AufgabenOffen($adr_id){
    $this->app->Tpl->Set('SUBHEADING', 'offene Aufgaben');
    $this->app->Tpl->Set('INHALT','');

    // selektierte Aufgabe Updaten
    if($this->app->Secure->GetPOST('ok') != ''){
      $aufg_id = $this->app->Secure->GetPOST('aufg_id');
      foreach($aufg_id as $myId){
        $this->app->DB->Update("UPDATE aufgabe SET abgeschlossen_am = CURDATE(), abgeschlossen='1' WHERE id=$myId LIMIT 1");
        // Kopie nach aufgabe_erledigt
        $this->app->DB->Insert("INSERT INTO aufgabe_erledigt (adresse, aufgabe, abgeschlossen_am)
            VALUES ($adr_id, $myId, CURDATE())");
      }
    }

    $table = new EasyTable($this->app); 
    // Hole einmalige und wiederholende Aufgaben
    // bei wiederholenden Aufgaben werden nur die vom heutigen Tag und nach Schema startdatum + (intervall*n) geholt
    $table->Query("SELECT
        CONCAT(\"<input type='checkbox' name='aufg_id[]' value='\", id, \"'>\") AS Ok, 
        IF(intervall_tage=0, DATE_FORMAT(startdatum, '%d.%m.%Y'), 
          DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL MOD(DATEDIFF(CURDATE(),DATE_FORMAT(startdatum, '%Y:%m:%d')),intervall_tage) day),'%d.%m.%Y')) AS Datum,
        IF(intervall_tage=0, DATEDIFF(CURDATE(), startdatum) ,MOD(DATEDIFF(CURDATE(), startdatum),intervall_tage)) AS Verzug,
        IF(intervall_tage=0, 'einmalig', intervall_tage) AS Intervall,
        aufgabe, beschreibung
        FROM aufgabe
        WHERE
        (abgeschlossen=0 AND (adresse=$adr_id OR adresse=0))
        OR 
        (DATE_SUB(CURDATE(), INTERVAL MOD(DATEDIFF(CURDATE(),DATE_FORMAT(startdatum, '%Y:%m:%d')),intervall_tage) day)=CURDATE()
         AND DATE_SUB(CURDATE(), INTERVAL MOD(DATEDIFF(CURDATE(),DATE_FORMAT(startdatum, '%Y:%m:%d')),intervall_tage) day) != abgeschlossen_am
         AND intervall_tage>0 AND (adresse=$adr_id OR adresse=0))
        ORDER BY Datum");

    $table->DisplayNew('INHALT','Beschreibung','noAction');

    $this->app->Tpl->Parse('AUFGABEN','rahmen_submit_100.tpl');   
  }

  function ZeiterfassungArchiv($adr_id)
  {
    $this->app->Tpl->Set('SUBHEADING', 'Archiv');
    $this->app->Tpl->Set('INHALT','');  

    $table = new EasyTable($this->app);
    $table->Query("SELECT 
        MONTHNAME(bis) AS Monat,
        YEAR(bis) Jahr,
        CONCAT(ROUND(SUM(TIME_TO_SEC(TIMEDIFF(bis,von)))/3600,1),' Stunden') AS Arbeitszeit,
        CONCAT('<a href=\"index.php?module=zeiterfassung&action=details&month=', MONTH(bis),'&year=', YEAR(bis),'\" class=\"popup\" title=\"Details\">Details</a>')
        FROM `zeiterfassung` 
        WHERE adresse=$adr_id
        GROUP BY MONTHNAME(bis)
        ORDER BY bis DESC");

    $table->DisplayNew('INHALT','Aktion', 'noAction');

    $this->app->Tpl->Parse('ARCHIV','rahmen100.tpl');
  }

  public function ZeiterfassungDetails()
  {
    $monat = $this->app->Secure->GetGET('month');
    $jahr = $this->app->Secure->GetGET('year');
    $frame = $this->app->Secure->GetGET('frame');

    if($frame=='false')
    {
      // hier nur fenster größe anpassen
      $this->app->YUI->IframeDialog(650,730);
    } else {

      $id =  $this->app->User->GetId();
      if($id!=''){
        $adr_id = $this->app->DB->Select("SELECT adresse FROM user WHERE id=$id");
      }

      $monatsname = $this->app->DB->Select("SELECT MONTHNAME('$jahr-$monat-01')");

      $this->app->Tpl->Set('SUBHEADING', "Arbeitszeiten f&uuml;r $monatsname");
      $this->app->Tpl->Set('INHALT','');  


      //    $this->app->Tpl->Add(KURZUEBERSCHRIFT,"Zeiterfassung");

      //$this->app->Tpl->Add(TABS,
      //  "<li><a href=\"index.php?module=zeiterfassung&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");

      $table = new EasyTable($this->app);
      $table->Query("SELECT  
          DATE_FORMAT(bis,'%d.%m.%y') as Datum,
          ROUND((TIME_TO_SEC(TIMEDIFF(bis,von))/3600)) AS Dauer,
          buchungsart,
          aufgabe
          FROM zeiterfassung
          WHERE adresse=$adr_id
          AND MONTH(bis)=$monat
          AND YEAR(bis)=$jahr");	

        $table->DisplayNew('INHALT','T&auml;tigkeit', 'noAction');
      //$this->app->Tpl->Set(AKTIV_TAB5,'selected'); 
      $this->app->Tpl->Parse('PAGE','rahmen.tpl');
      $this->app->BuildNavigation=false;
    }
  }

  public function ZeiterfassungDokuArbeitszeit()
  {
    $pdf = new DokuArbeitszeit();
    $filename = $tmp.date('Ymd').'_'.$kundennummer.'_'.$name.'_DOKUARBEITSZEIT.pdf';
    $pdf->Output($filename,'D');
  }
}
