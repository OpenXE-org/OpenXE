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
use Xentral\Components\Http\Session\SessionHandler;
use Xentral\Modules\Article\Gateway\ArticleGateway;
use Xentral\Modules\PartialDelivery\Service\PartialDeliveryService;
use Xentral\Modules\ScanArticle\Exception\InvalidArgumentException;
use Xentral\Modules\ScanArticle\Service\ScanArticleService;
use Xentral\Modules\ScanArticle\Exception\ArticleNotFoundException;

include '_gen/auftrag.php';



class Auftrag extends GenAuftrag
{
  /** @var int|bool $kommissionierung */
  var $kommissionierung;
  /** @var Application $app */

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
      case 'auftraege':
        $allowed['auftrag'] = array('list');

        $projectsearchdisabled = (string)$this->app->erp->GetKonfiguration('auftraege_projectsearchdisabled');
        if($projectsearchdisabled==='') {
          $projectsearchdisabled = ($this->app->DB->Select('SELECT COUNT(id) FROM auftrag') >= 100000)?1:0;
          $this->app->erp->SetKonfigurationValue('auftraege_projectsearchdisabled', $projectsearchdisabled);
        }
        $projectCol = 'p.abkuerzung';
        $useAddr = true;
        if($projectsearchdisabled) {
          $projectCol = '(SELECT  LEFT(UPPER(abkuerzung),10) FROM projekt WHERE id = a.projekt)';
          if(!$this->app->erp->Firmendaten('adresse_freitext1_suche')) {
            $useAddr = false;
          }
        }
        $projectColSql = $projectCol.' AS projektabkuerung';

        $rowcallback_gt = 1;
        $auftragmarkierenegsaldo = $this->app->erp->Firmendaten('auftragmarkierenegsaldo');
        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#artikellager').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#ustpruefung').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#zahlungseingang').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#zahlungseingangfehlt').click( function() { fnFilterColumn5( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#manuellepruefung').click( function() { fnFilterColumn4( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#ohnerechnung').click( function() { fnFilterColumn10( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#auftragheute').click( function() { fnFilterColumn6( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#auftragoffene').click( function() { fnFilterColumn7( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#auftragstornierte').click( function() { fnFilterColumn8( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#auftragabgeschlossene').click( function() { fnFilterColumn9( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#teillieferung').click( function() { fnFilterColumn11( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#portofehlt').click( function() { fnFilterColumn12( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#teilzahlung').click( function() { fnFilterColumn13( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#autoversandfehlt').click( function() { fnFilterColumn14( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#autoversandok').click( function() { fnFilterColumn15( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#fastlanea').click( function() { fnFilterColumn16( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#tolate').click( function() { fnFilterColumn17( 0 ); } );");
        //            $this->app->Tpl->Add('JQUERYREADY',"$('#artikellager').click( function() {  oTable".$name.".fnDraw(); } );");
        for ($r = 1;$r < 18;$r++) {
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

        $zusatzcols = array();
        $auftragzusatzfelder = $this->app->erp->getZusatzfelderAuftrag();

        $heading = array('','', 'Auftrag', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)');
        $width = array('1%','1%', '10%', '10%', '10%', '31%', '5%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open','a.belegnr', 'a.belegnr', 'a.datum'
        ,$useAddr? 'if(a.lieferantenauftrag=1,adr.lieferantennummer,adr.kundennummer)':'a.lieferantkdrnummer',
          'a.name', 'a.land',
          //'p.abkuerzung',
          $projectCol,
          'a.zahlungsweise', 'a.gesamtsumme');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')', 'a.belegnr', 'a.ihrebestellnummer','a.transaktionsnummer',
          'a.internet', "a.lieferantkdrnummer", 'a.name', 'a.land', 'a.zahlungsweise', 'a.status', $app->erp->FormatPreis('a.gesamtsumme',2),
          //'adr.freifeld1',
          'a.internebezeichnung'
        );
        if($useAddr) {
          $searchsql[] = 'adr.freifeld1';
        }
        if(!$projectsearchdisabled) {
          $searchsql[] = $projectCol;
        }
        $alignright = array('10');
        $defaultorder = 13; //Optional wenn andere Reihenfolge gewuenscht
        $hide767 = array('5');
        $defaultorderdesc = 1;

        if($this->app->erp->RechteVorhanden('auftrag','summe')){
          $sumcol = array(10);
        }
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=auftrag&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=auftrag&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=auftrag&action=copy&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=auftrag&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"6\" data-label-reference-id=\"%value%\" data-label-reference-table=\"auftrag\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        $menucol = 11;
        for($i = 1; $i <= 5; $i++) {
          $zusatzfeld = $this->app->erp->Firmendaten('auftragtabellezusatz' . $i);
          if($zusatzfeld && isset($auftragzusatzfelder[$zusatzfeld])){
            $defaultorder++;
            $menucol++;
            $heading[] = $auftragzusatzfelder[$zusatzfeld];
            $width[] = '10%';
            $findcols[] = 'a.'.$zusatzfeld;
            switch($zusatzfeld)
            {
              case 'umsatz_netto':
                $searchsql[] = $app->erp->FormatPreis($zusatzfeld, 2);
                $zusatzcols[] = $app->erp->FormatPreis($zusatzfeld,2);
                $numbercols[] = array_search('a.'.$zusatzfeld, $findcols);
                $sumcol[] = array_search('a.'.$zusatzfeld, $findcols)+1;
                $alignright[] = array_search('a.'.$zusatzfeld, $findcols)+1;
                break;
              case 'tatsaechlicheslieferdatum':
              case 'lieferdatum':
                $searchsql[] = 'IF(a.'.$zusatzfeld.'!="0000-00-00", DATE_FORMAT(a.'.$zusatzfeld.",'%d.%m.%Y'), '')";
                $zusatzcols[] = 'IF(a.'.$zusatzfeld.'!="0000-00-00", DATE_FORMAT(a.'.$zusatzfeld.",'%d.%m.%Y'), '')";
                $datecols[] = array_search('a.'.$zusatzfeld, $findcols);

                break;
              default:
                $searchsql[] = 'a.'.$zusatzfeld;
                $zusatzcols[] = 'a.'.$zusatzfeld;
            }
          }
        }
        $findcols[] = 'a.status';

        $width[] = '1%';
        $width[] = '1%';
        $findcols[] = 'a.status';
        $findcols[] = 'a.id';
        $findcols[] = 'a.id';
        $heading[] = 'Monitor';
        $heading[] = 'Men&uuml;';




        $parameter = $this->app->User->GetParameter('table_filter_auftrag');
        $parameter = base64_decode($parameter);
        $parameter = json_decode($parameter, true);

        $sql = "";
        $sql .=
          "SELECT SQL_CALC_FOUND_ROWS 
          a.id,
          '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' AS `open`, 
          CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',a.id,'\" />') AS `auswahl`,
          CONCAT(
            IF(a.status = 'angelegt','ENTWURF',a.belegnr),
            IF(
              a.projektfiliale > 0 OR a.fastlane = 1,
              CONCAT(
                ' (', 
                IF(a.projektfiliale > 0,'F',''),
                IF(
                  a.fastlane = 1,
                  IF(a.projektfiliale > 0,',FL','FL'),
                  ''
                ),
                ')'
              ),
              ''
            )
          ) AS `auftrag`, 
          DATE_FORMAT(a.datum,'%d.%m.%Y') AS `vom`, 
          a.lieferantkdrnummer AS `kunde`,
          CONCAT(
            " . $this->app->erp->MarkerUseredit("a.name", "a.useredittimestamp") . ",
            IF(
              a.internebemerkung = '',
              '',
              ' <font color=red><strong>*</strong></font>'
            ),
            IF(
              a.freitext = '',
              '',
              ' <font color=blue><strong>*</strong></font>'
            ),
            IF(
              a.internebezeichnung != '',
              CONCAT(
                '<br><i style=color:#999>',
                a.internebezeichnung,
                '</i>'
              ),
              ''
            ),
            IF(
              a.abweichendelieferadresse = 1,
              CONCAT(
                '<br><i style=color:#999>Abw. Lieferadr.: ',
                a.liefername,', ',
                a.lieferstrasse,', ',
                a.lieferland,'-',
                a.lieferplz,' ',
                a.lieferort,
                '</i>'
              ),
              ''
            )
          ) AS `name`, 
          a.land AS `land`,
          $projectColSql, 
          ".(
              $auftragmarkierenegsaldo ?
              "CONCAT(
                '<span',
                IF(
                  a.status = 'angelegt'
                  OR a.status = 'storniert'
                  OR ISNULL(a.saldogeprueft)
                  OR ( -(a.saldo) <= (IF(ISNULL(a.skontobetrag),a.gesamtsumme * ( a.zahlungszielskonto) / 100.0,a.skontobetrag)))
                  OR (a.vorabbezahltmarkieren = 1 and a.zahlungsweise = 'vorkasse'),
                  '',
                  ' style=\"color:red;\" '
                ),
                '>'," :
                ''
          )."
          a.zahlungsweise".($auftragmarkierenegsaldo?",'<span>')":"")." AS `zahlungsweise`,  
          ".$app->erp->FormatPreis('a.gesamtsumme',2)." AS `betrag`,
          ".(!empty($zusatzcols)?implode(', ',$zusatzcols).',':'')."
          (" . $this->app->YUI->IconsSQL() . ")  AS icons, 
          a.id
          FROM `auftrag` AS `a` ";

        if($useAddr) {
          $sql .= ' LEFT JOIN `adresse` AS `adr` ON a.adresse = adr.id ';
        }

        if(!$projectsearchdisabled) {
          $sql .= ' LEFT JOIN `projekt` AS `p` ON p.id = a.projekt ';
        }

        if(isset($parameter['artikel']) && !empty($parameter['artikel'])) {
          $artikelid = $this->app->DB->Select("SELECT id FROM artikel where geloescht != 1 AND nummer != 'DEL' AND nummer != '' AND nummer = '".$this->app->DB->real_escape_string(reset(explode(' ',trim($parameter['artikel']))))."' LIMIT 1");
          if($artikelid) {
            $sql .= sprintf(
              ' INNER JOIN `auftrag_position` AS `ap` ON a.id = ap.auftrag AND ap.artikel = %d ',
              $artikelid
            );
            $groupby = ' GROUP BY a.id ';
          }
        }

        // Fester filter

        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET("more_data1");

        if ($more_data1 == 1) $subwhere[] = " a.lager_ok=0 ";
        $more_data2 = $this->app->Secure->GetGET("more_data2");

        if ($more_data2 == 1) $subwhere[] = " a.ust_ok=0 ";

        $more_data3 = $this->app->Secure->GetGET("more_data3");
        if ($more_data3 == 1) $subwhere[] = " a.vorkasse_ok=1 ";

        $more_data13 = $this->app->Secure->GetGET("more_data13");
        if ($more_data13 == 1) $subwhere[] = " a.vorkasse_ok=2 ";



        //$more_data4 = $this->app->Secure->GetGET("more_data4"); if($more_data4==1) $subwhere[] = " a.check_ok=0 ";
        $more_data4 = $this->app->Secure->GetGET("more_data4");

        if ($more_data4 == 1) $subwhere[] = " (a.check_ok=0 OR a.liefersperre_ok=0 OR a.kreditlimit_ok='0') ";
        $more_data5 = $this->app->Secure->GetGET("more_data5");

        if ($more_data5 == 1) $subwhere[] = " a.vorkasse_ok=0 ";
        $more_data6 = $this->app->Secure->GetGET("more_data6");

        if ($more_data6 == 1) {
          $subwhere[] = " a.datum=CURDATE() ";
          $ignore = true;
        }
        $more_data7 = $this->app->Secure->GetGET("more_data7");

        if ($more_data7 == 1) {
          $subwherestatus[] = " a.status='freigegeben' ";
          $ignore = true;
        }

        $more_data8 = $this->app->Secure->GetGET("more_data8");

        if ($more_data8 == 1) {
          $subwherestatus[] = " a.status='storniert' ";
          $ignore = true;
        }
        $more_data9 = $this->app->Secure->GetGET("more_data9");

        if ($more_data9 == 1) {
          $subwherestatus[] = " a.status='abgeschlossen' ";
          $ignore = true;
        }
        $more_data10 = $this->app->Secure->GetGET("more_data10");

        if ($more_data10 == 1) {
          $subwhere[] = " (SELECT COUNT(r.id) FROM rechnung r WHERE r.auftragid=a.id) <= 0 
          AND (SELECT count(sp1.id) FROM sammelrechnung_position sp1 INNER JOIN auftrag_position ap1 ON sp1.auftrag_position_id = ap1.id  WHERE ap1.auftrag = a.id) <= 0
          AND (SELECT count(sp1.id) FROM sammelrechnung_position sp1 INNER JOIN lieferschein_position lsp1 ON sp1.lieferschein_position_id = lsp1.id INNER JOIN auftrag_position ap1 ON ap1.id = lsp1.auftrag_position_id WHERE ap1.auftrag = a.id) <= 0
          AND (SELECT COUNT(l.id) FROM lieferschein l WHERE l.auftragid = a.id AND l.keinerechnung = 1) <= 0
          AND a.gesamtsumme > 0 AND ifnull(a.rechnungid,0) = 0 ";
          $ignore = true;
        }
        $more_data11 = $this->app->Secure->GetGET("more_data11");

        if ($more_data11 == 1) {
          $subwhere[] = " a.teillieferung_moeglich='1' ";
        }
        $more_data12 = $this->app->Secure->GetGET("more_data12");

        if ($more_data12 == 1) {
          $subwhere[] = " a.porto_ok=0 ";
        }

        $more_data14 = $this->app->Secure->GetGET("more_data14");
        if ($more_data14 == 1) {
          $subwhere[] = " a.autoversand=0 ";
        }

        $more_data15 = $this->app->Secure->GetGET("more_data15");
        if ($more_data15 == 1) {
          $subwhere[] = " a.autoversand=1 ";
        }

        $more_data16 = $this->app->Secure->GetGET("more_data16");
        if ($more_data16 == 1) {
          $subwhere[] = " a.fastlane=1 ";
        }

        $more_data17 = $this->app->Secure->GetGET("more_data17");
        if ($more_data17 == 1) {
          /*$subwhere[] = "
          (
            (
              (a.datum < DATE_SUB(NOW(), INTERVAL 1 DAY)) 
              OR (a.lieferdatum < DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.lieferdatum!='0000-00-00')  OR
              (a.tatsaechlicheslieferdatum < DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.tatsaechlicheslieferdatum!='0000-00-00')
              )
              AND a.status='freigegeben'
            
          )";*/

          $subwhere[] =
          "(
            (
            (a.datum < DATE_SUB(NOW(), INTERVAL 1 DAY))
                OR (a.lieferdatum < DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.lieferdatum!='0000-00-00')
                OR (a.tatsaechlicheslieferdatum < DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.tatsaechlicheslieferdatum!='0000-00-00')
            )
            AND a.status='freigegeben'
                  AND NOT (
                    IFNULL(a.tatsaechlicheslieferdatum,'0000-00-00')!='0000-00-00' AND a.tatsaechlicheslieferdatum >= CURDATE()
            )
            AND NOT (
                    IFNULL(a.tatsaechlicheslieferdatum,'0000-00-00')='0000-00-00' AND
                    IFNULL(a.lieferdatum,'0000-00-00')!='0000-00-00' AND a.lieferdatum >= CURDATE()
                  )
           )";
        }



        $tmp = '';
        if(!empty($subwhere)) {
          foreach($subwhere as $s) {
            $tmp .= ' AND '.$s;
          }
        }

        if(!empty($subwherestatus)){
          $tmp .= " AND (".implode(" OR ", $subwherestatus).")";
        }

        if ($tmp != "" && !$ignore) $tmp.= " AND a.status='freigegeben' ";

        // ENDE EXTRA more
        $where = " a.id!='' $tmp " . $this->app->erp->ProjektRechte('a.projekt', true, 'a.vertriebid');

        /* STAMMDATEN */
        if(isset($parameter['kundennummer']) && !empty($parameter['kundennummer'])) {
          if(!$useAddr) {
            $sql .= ' LEFT JOIN adresse adr ON a.adresse=adr.id ';
            $useAddr = true;
          }
          $paramsArray[] = " (a.kundennummer LIKE '%".$parameter['kundennummer']."%' OR adr.kundennummer LIKE '%".$parameter['kundennummer']."%') ";
        }

        if(isset($parameter['kundengruppe']) && !empty($parameter['kundengruppe'])){
          $gruppenId = $this->app->DB->Select("SELECT id FROM gruppen WHERE kennziffer LIKE '%".$parameter['kundengruppe']."%' LIMIT 1");
          $sql .= ' LEFT JOIN adresse_rolle ar ON a.adresse = ar.adresse ';
          $paramsArray[] = " ( ar.subjekt LIKE 'Mitglied' AND ar.objekt LIKE 'Gruppe' AND ar.parameter = '$gruppenId')";
        }


        if(isset($parameter['name']) && !empty($parameter['name'])) {
          $paramsArray[] = "a.name LIKE '%".$parameter['name']."%' ";
        }

        if(isset($parameter['ansprechpartner']) && !empty($parameter['ansprechpartner'])) {
          $paramsArray[] = "a.ansprechpartner LIKE '%".$parameter['ansprechpartner']."%' ";
        }

        if(isset($parameter['abteilung']) && !empty($parameter['abteilung'])) {
          $paramsArray[] = "a.abteilung LIKE '%".$parameter['abteilung']."%' ";
        }

        if(isset($parameter['strasse']) && !empty($parameter['strasse'])) {
          $paramsArray[] = "a.strasse LIKE '%".$parameter['strasse']."%' ";
        }

        if(isset($parameter['plz']) && !empty($parameter['plz'])) {
          $paramsArray[] = "a.plz LIKE '".$parameter['plz']."%'";
        }

        if(isset($parameter['ort']) && !empty($parameter['ort'])) {
          $paramsArray[] = "a.ort LIKE '%".$parameter['ort']."%' ";
        }

        if(isset($parameter['land']) && !empty($parameter['land'])) {
          $paramsArray[] = "a.land LIKE '%".$parameter['land']."%' ";
        }

        if(isset($parameter['ustid']) && !empty($parameter['ustid'])) {
          $paramsArray[] = "a.ustid LIKE '%".$parameter['ustid']."%' ";
        }

        if(isset($parameter['telefon']) && !empty($parameter['telefon'])) {
          $paramsArray[] = "a.telefon LIKE '%".$parameter['telefon']."%' ";
        }

        if(isset($parameter['email']) && !empty($parameter['email'])) {
          $paramsArray[] = "a.email LIKE '%".$parameter['email']."%' ";
        }


        /* XXX */
        if(isset($parameter['datumVon']) && !empty($parameter['datumVon'])) {

          $paramsArray[] = "a.datum >= '" . date('Y-m-d',strtotime($parameter['datumVon']))."' ";
        }

        if(isset($parameter['datumBis']) && !empty($parameter['datumBis'])) {
          $paramsArray[] = "a.datum <= '" . date('Y-m-d',strtotime($parameter['datumBis']))."' ";
        }

        if(isset($parameter['projekt']) && !empty($parameter['projekt'])) {

          $projektData = $this->app->DB->SelectRow('
            SELECT
              *
            FROM
              projekt
            WHERE
              abkuerzung LIKE "' . $parameter['projekt'] . '"
          ');
          $paramsArray[] = "a.projekt = '".$projektData['id']."' ";
        }

        if(isset($parameter['belegnummer']) && !empty($parameter['belegnummer'])) {
          $paramsArray[] = "a.belegnr LIKE '".$parameter['belegnummer']."' ";
        }

        if(isset($parameter['internebemerkung']) && !empty($parameter['internebemerkung'])) {
          $paramsArray[] = "a.internebemerkung LIKE '%".$parameter['internebemerkung']."%' ";
        }

        if(isset($parameter['aktion']) && !empty($parameter['aktion'])) {
          $paramsArray[] = "a.aktion LIKE '%".$parameter['aktion']."%' ";
        }

        if(isset($parameter['transaktionsnummer']) && !empty($parameter['transaktionsnummer'])) {
          $paramsArray[] = "a.transaktionsnummer LIKE '%".$parameter['transaktionsnummer']."%' ";
        }

        if(isset($parameter['freitext']) && !empty($parameter['freitext'])) {
          $paramsArray[] = "a.freitext LIKE '%".$parameter['freitext']."%' ";
        }

        if(isset($parameter['zahlungsweise']) && !empty($parameter['zahlungsweise'])) {
          $paramsArray[] = "a.zahlungsweise LIKE '%".$parameter['zahlungsweise']."%' ";
        }

        if(isset($parameter['status']) && !empty($parameter['status'])) {
          $paramsArray[] = "a.status LIKE '%".$parameter['status']."%' ";
        }

        if(isset($parameter['versandart']) && !empty($parameter['versandart'])) {
          $paramsArray[] = "a.versandart LIKE '%".$parameter['versandart']."%' ";
        }

        if(isset($parameter['betragVon']) && !empty($parameter['betragVon'])) {
          $paramsArray[] = "a.gesamtsumme >= '" . $parameter['betragVon'] . "' ";
        }

        if(isset($parameter['betragBis']) && !empty($parameter['betragBis'])) {
          $paramsArray[] = "a.gesamtsumme <= '" . $parameter['betragBis'] . "' ";
        }
        // projekt, belegnummer, internetnummer, bestellnummer, transaktionsId, freitext, internebemerkung, aktionscodes

        if ($paramsArray) {
          $where .= ' AND ' . implode(' AND ', $paramsArray);
        }

        $this->app->erp->RunHook('auftraege_tablesearch', 2, $sql, $where);
        // gesamt anzahl
        $count = "SELECT COUNT(a.id) FROM auftrag a ";
        $moreinfo = true; // EXTRA

        break;
    }
    $erg = [];
    foreach($erlaubtevars as $k => $v) {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }

    return $erg;
  }

  /**
   * Auftrag constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->kommissionierung = false;
    $this->app=$app;
    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","AuftragList");
    $this->app->ActionHandler("create","AuftragCreate");
    $this->app->ActionHandler("positionen","AuftragPositionen");
    $this->app->ActionHandler("upauftragposition","UpAuftragPosition");
    $this->app->ActionHandler("delauftragposition","DelAuftragPosition");
    $this->app->ActionHandler("copyauftragposition","CopyAuftragPosition");
    $this->app->ActionHandler("downauftragposition","DownAuftragPosition");
    $this->app->ActionHandler("positioneneditpopup","AuftragPositionenEditPopup");
    $this->app->ActionHandler("checkdisplay","AuftragCheckDisplayPopup");
    $this->app->ActionHandler("edit","AuftragEdit");
    $this->app->ActionHandler("tracking","AuftragTracking");
    $this->app->ActionHandler("ausversand","AuftragDeleteAusVersand");
    $this->app->ActionHandler("search","AuftragSuche");
    $this->app->ActionHandler("ean","AuftragEAN");
    $this->app->ActionHandler("versandzentrum","AuftragVersandzentrum");
    $this->app->ActionHandler("uststart","AuftragUstStart");
    $this->app->ActionHandler("delete","AuftragDelete");
    $this->app->ActionHandler("undelete","AuftragUndelete");
    $this->app->ActionHandler("anfrage","AuftragAnfrage");
    $this->app->ActionHandler("abschluss","AuftragAbschluss");
    $this->app->ActionHandler("copy","AuftragCopy");
    $this->app->ActionHandler("zertifikate","AuftragZertifikate");
    $this->app->ActionHandler("verfuegbar","AuftragVerfuegbar");
    $this->app->ActionHandler("rechnung","AuftragRechnung");
    $this->app->ActionHandler("lieferschein","AuftragLieferschein");
    $this->app->ActionHandler("lieferscheinrechnung","AuftragLieferscheinRechnung");

    $this->app->ActionHandler("nachlieferung","AuftragNachlieferung");
    //    $this->app->ActionHandler("versand","AuftragVersand");
    $this->app->ActionHandler("freigabe","AuftragFreigabe");
    $this->app->ActionHandler("abschicken","AuftragAbschicken");
    $this->app->ActionHandler("pdf","AuftragPDF");
    $this->app->ActionHandler("inlinepdf","AuftragInlinePDF");
    $this->app->ActionHandler("proforma","AuftragProforma");
    $this->app->ActionHandler("versand","AuftragVersand");
    $this->app->ActionHandler("zahlungsmail","AuftragZahlungsmail");
    $this->app->ActionHandler("reservieren","AuftragReservieren");
    $this->app->ActionHandler("nachlieferung","AuftragNachlieferung");
    $this->app->ActionHandler("protokoll","AuftragProtokoll");
    $this->app->ActionHandler("minidetail","AuftragMiniDetail");
    $this->app->ActionHandler("editable","AuftragEditable");
    $this->app->ActionHandler("dateien","AuftragDateien");
    $this->app->ActionHandler("livetabelle","AuftragLiveTabelle");
    $this->app->ActionHandler("zahlungsmahnungswesen","AuftragZahlungMahnungswesen");
    $this->app->ActionHandler("schreibschutz","AuftragSchreibschutz");
    $this->app->ActionHandler("shopexport","AuftragShopexport");
    $this->app->ActionHandler("deleterabatte","AuftragDeleteRabatte");
    $this->app->ActionHandler("kreditlimit","AuftragKreditlimit");
    $this->app->ActionHandler("updateverband","AuftragUpdateVerband");
    $this->app->ActionHandler("pdffromarchive","AuftragPDFfromArchiv");

    $this->app->ActionHandler("archivierepdf", "AuftragArchivierePDF");
    $this->app->ActionHandler("paypal", "AuftragPaypal");
    $this->app->ActionHandler("summe","AuftragSumme"); // nur fuer rechte
    $this->app->ActionHandler("einkaufspreise", "AuftragEinkaufspreise");
    $this->app->ActionHandler("alsfreigegeben", "AuftragAlsfreigegeben");
    $this->app->ActionHandler("steuer", "AuftragSteuer");
    $this->app->DefaultActionHandler("list");

    $id = $this->app->Secure->GetGET('id');
    $nummer = $this->app->Secure->GetPOST('adresse');
    $name = '';
    $belegnr = '';
    $abweichendebezeichnung = 0;
    if($id > 0) {
      $abArr = $this->app->DB->SelectRow("SELECT b.belegnr, a.name,b.abweichendebezeichnung  FROM auftrag b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      if(!empty($abArr)) {
        $name = $abArr['name'];
        $belegnr = $abArr['belegnr'];
        $abweichendebezeichnung = $abArr['abweichendebezeichnung'];
      }
    }
    if($nummer==''){
      $adresse = $name;
    }
    else{
      $adresse = $nummer;
    }

    $nummer = $belegnr;
    if($nummer=='' || $nummer=='0') {
      $nummer='ohne Nummer';
    }

    $this->app->Tpl->Set('UEBERSCHRIFT','Auftrag:&nbsp;'.$adresse.' ('.$nummer.')');
    $this->app->Tpl->Set('FARBE','[FARBE2]');

    if($abweichendebezeichnung){
      $this->app->Tpl->Set('BEZEICHNUNGTITEL', $this->app->erp->Firmendaten('bezeichnungauftragersatz'));
    }
    else{
      $this->app->Tpl->Set('BEZEICHNUNGTITEL', 'Auftrag');
    }

    $this->app->erp->Headlines('Auftrag');

    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
    $this->app->erp->RegisterHook('supersearch_detail', 'auftrag', 'AuftragSupersearchDetail');
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function AuftragSupersearchDetail($detailQuery, $detailResult)
  {
    if ($detailQuery->getGroupKey() !== 'orders') {
      return;
    }

    $auftragId = $detailQuery->getItemIdentifier();
    $sql = sprintf(
      "SELECT a.id, a.belegnr, a.datum FROM `auftrag` AS `a` WHERE a.id = '%s' LIMIT 1",
      $this->app->DB->real_escape_string($auftragId)
    );
    $auftrag = $this->app->DB->SelectRow($sql);
    if (empty($auftrag)) {
      return;
    }

    $datum = date('d.m.Y', strtotime($auftrag['datum']));
    $detailResult->setTitle(sprintf('Auftrag %s <small>vom %s</small>', $auftrag['belegnr'], $datum));
    $detailResult->addButton('Auftrag Details', sprintf('index.php?module=auftrag&action=edit&id=%s', $auftrag['id']));
    $detailResult->setMiniDetailUrl(sprintf('index.php?module=auftrag&action=minidetail&id=%s', $auftrag['id']));
  }

  function AuftragAARLGPositionen_aktion($module, $id, &$aktionoptionen, &$aktionjs)
  {
    if($module !== 'auftrag') {
      return;
    }
    $aktionoptionen .= "<option value=\"delivery\">als Lieferschein weiterf&uuml;hren</option>";
    $aktionjs .= '
    if(wert == \'delivery\')
    {
      var parameter = \'\';
      var ok = false;
      $(checkboxen).each(function(){
        if(parameter != \'\')parameter += \',\';
        if($(this).attr(\'name\') == \'belegsort[]\')
        {
          parameter += \'b\'+$(this).val();
          ok = true;
        }else{
          parameter += \'z\'+$(this).val();
        }
      });
      if(ok)
      {
        if(confirm(\'{|Wirklich als Lieferschein weiterführen?|}\'))
        {
          window.location.href=\'index.php?module=auftrag&action=lieferschein&id='.$id.'&posids=\'+parameter;
        }else{
          $(el).val(\'\');
        }
      }else{
        $(el).val(\'\');
        alert(\'Bitte einen Artikel auswählen\');
      }
    }
    ';
    
  }
  
  function AuftragSteuer()
  {
      
  }
  
  function AuftragEinkaufspreise()
  {
    
  }
  
  function AuftragSumme()
  {
  }

  function AuftragArchivierePDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id = '$id' LIMIT 1");
    $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
    if(class_exists('AuftragPDFCustom'))
    {
      $Brief = new AuftragPDFCustom($this->app,$projekt);
    }else{
      $Brief = new AuftragPDF($this->app,$projekt);
    }
    $Brief->GetAuftrag($id);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument();
    unlink($tmpfile);
    $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
    if(class_exists('AuftragPDFCustom'))
    {
      $Brief = new AuftragPDFCustom($this->app, $projekt);
    }else{
      $Brief = new AuftragPDF($this->app, $projekt);
    }
    $Brief->GetAuftrag($id);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument(1);
    $this->app->DB->Update("UPDATE auftrag SET schreibschutz='1' WHERE id='$id'");
    unlink($tmpfile);
    $this->app->Location->execute('index.php?module=auftrag&action=edit&id='.$id);
  }

  function AuftragPaypal()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id)
    {
      $erg = $this->app->erp->SendPaypalFromAuftrag($id);
      if($erg === false)
      {
        $msg = $this->app->erp->base64_url_encode('<div class="error">Es ist ein Fehler aufgetreten</div>');
      }else if(isset($erg['error']))
      {
        $msg = $this->app->erp->base64_url_encode('<div class="error">'.$erg['error'].'</div>');
        
      }else{
        $msg = $this->app->erp->base64_url_encode('<div class="info">Anfrage gesendet</div>');
      }
    }else{
      $msg = $this->app->erp->base64_url_encode('<div class="error">Kein Auftrag</div>');
    }
    header('Location: index.php?module=auftrag&action=edit&id='.$id.'&msg='.$msg);
  }
  
  function AuftragEAN()
  {
    $id=$this->app->Secure->GetGET('id');
    $scanner=$this->app->Secure->GetPOST('scanner');
    $menge=$this->app->Secure->GetPOST('menge');
    $posAdd = $this->app->Secure->GetPOST('posadd');
    $posDel = $this->app->Secure->GetPost('posdel');
    $sumposition = $this->app->Secure->GetPOST('sumposition');
    /** @var ScanArticleService  $scanArticleService */
    $scanArticleService = $this->app->Container->get('ScanArticleService');


    $this->AuftragMenu();
    $schreibschutz = $this->app->DB->Select(
      sprintf(
        'SELECT `schreibschutz` FROM `auftrag` WHERE `id` = %d LIMIT 1',
        $id
      )
    );

    if($scanner!='' && $schreibschutz!='1') {
      if(!is_numeric($menge)){
        $menge=1;
      }
      //$adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$id' LIMIT 1");
      try{
        $scanArticleService->writeArticleToSession('auftrag',$scanner,$menge,$id);
      } catch(ArticleNotFoundException $e){
        $this->app->Tpl->Set('TAB1',"<div class=\"error\">{|Der Artikel wurde nicht gefunden|}: ".$scanner."</div>");
      } catch(InvalidArgumentException $e){
        $this->app->Tpl->Set('TAB1',"<div class=\"error\">{|Fehler beim Schreiben in die Session|}</div>");
      }
    }

    if($schreibschutz=="1"){
      $this->app->Tpl->Set('TAB1',"<div class=\"info\">Der Schreibschutz ist aktiviert. Bitte entfernen Sie diesen.</div>");
    }
    else{

      if(!empty($posAdd)){
        try{
          if($sumposition) {
            $scanArticleService->saveSumedPositions('auftrag', $id);
          }
          else{
            $scanArticleService->savePositions('auftrag', $id);
          }
          $this->app->Tpl->Set('MESSAGE',"<div class=\"info\">{|Positionen hinzugefügt|}</div>");
        } catch(Exception $e){
          $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">{|Positionen nicht gespeichert|}!</div>");
        }
      }

      if(!empty($posDel)){
        $scanArticleService->clearAllArticleDataInSession('auftrag');
      }

      $gescannteArtikel = $scanArticleService->getAllArticleDataFromSession('auftrag');
      if(!empty($gescannteArtikel)){

        $table = new EasyTable($this->app);
        $table->headings = ['Artikelnummer','Artikel','Menge','Preis'];

        foreach($gescannteArtikel as $a){

          $row['nummer'] = $a['number'];
          $row['name'] = $a['name'];
          $row['menge'] = $a['amount'];
          $row['preis'] = $a['price'];

          if(!empty($row)){
            $table->AddRow($row);
          }
        }
        $table->DisplayNew('GESCANNTEARTIKEL','Preis','noAction');
        $this->app->Tpl->Set('POSITIONENSPEICHERN','<input type="submit" id="posadd" name="posadd" value="Positionen hinzufügen"><input type="submit" id="posdel" name="posdel" value="Löschen">');
      }

      $this->app->Tpl->Parse('TAB1','auftrag_ean.tpl');
    }
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  function AuftragUpdateVerband()
  {
    $id=$this->app->Secure->GetGET("id");
    $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Verbandsinformation wurde neu geladen!</div>  ");
    $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$id&msg=$msg");
  }       

  function AuftragDeleteRabatte()
  {

    $id=$this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE auftrag SET rabatt='',rabatt1='',rabatt2='',rabatt3='',rabatt4='',rabatt5='',realrabatt='' WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Die Rabatte wurden entfernt!</div>  ");
    $this->app->Location->execute('index.php?module=auftrag&action=edit&id='.$id.'&msg='.$msg);
  }       

  function AuftragShopexport()
  {
    $id=$this->app->Secure->GetGET('id');
    $shop=$this->app->DB->Select("SELECT shop FROM auftrag WHERE id='$id' LIMIT 1");
    $this->app->remote->RemoteUpdateAuftrag($shop,$id);
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Versandstatus an Shop &uuml;bertragen</div>  ");
    $this->app->Location->execute('index.php?module=auftrag&action=edit&id='.$id.'&msg='.$msg);
  }

  function AuftragDateien()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->AuftragMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Dateien)');
    $this->app->YUI->DateiUpload('PAGE','Auftrag',$id);
  }

  function AuftragSchreibschutz()
  {

    $id = $this->app->Secure->GetGET('id');
    $this->app->DB->Update("UPDATE auftrag SET zuarchivieren='1' WHERE id='$id'");
    $this->app->DB->Update("UPDATE auftrag SET schreibschutz='0' WHERE id='$id'");
    $this->app->erp->AuftragProtokoll($id,"Schreibschutz entfernt");
    $this->app->Location->execute('index.php?module=auftrag&action=edit&id='.$id);
  }

  function AuftragTracking()
  {
    $tracking = $this->app->Secure->GetGET("tracking");
    // Wir werden eine PDF Datei ausgeben
    header('Content-type: application/html');

    // Es wird downloaded.pdf benannt
    header('Content-Disposition: attachment; filename="'.$tracking.'.html"');

    // Die originale PDF Datei heißt original.pdf
    if(is_file('/var/data/userdata/tracking/'.$tracking.'.html')){
      readfile('/var/data/userdata/tracking/' . $tracking . '.html');
    }
    $this->app->ExitXentral();
  }


  function AuftragZahlungMahnungswesen()
  {
    $this->AuftragMenu();
    $this->AuftragMiniDetail('TAB1',true);
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }



  function AuftragLiveTabelleZeit($parsetarget="")
  {
    $id = $this->app->Secure->GetGET("id");

    $table = new EasyTable($this->app);

    $sql = "SELECT
              z.aufgabe as aufgabe, a.name as name, DATE_FORMAT(z.von,'%d.%m.%y %H:%i') as von, DATE_FORMAT(z.bis,'%d.%m.%y %H:%i') as bis,
              CONCAT(LPAD(HOUR(TIMEDIFF(bis, von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(bis, von)),2,'0')) AS dauer

                FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse WHERE z.auftrag='$id' order by z.von";
                //,if(z.abrechnen,if(z.abgerechnet!=1 AND z.abrechnen='1','offen','abgerechnet'),'abgeschlossen') as status

    $table->Query($sql);
    $artikel = $table->DisplayNew("return","Dauer","noAction");

    if($parsetarget==''){
      echo $artikel;
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Add($parsetarget,$artikel);
  }

  function AuftragLiveTabelle()
  {
    $id = $this->app->Secure->GetGET("id");
    $status = $this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status==='freigegeben')
    {
      $table->Query("SELECT ap.bezeichnung as artikel, ap.nummer as Nummer, ap.menge as Menge,
          if(a.lagerartikel,if(a.porto,'-',if((SELECT TRIM(SUM(l.menge))+0 FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel) > ap.menge,(SELECT TRIM(SUM(l.menge))+0 FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),
                if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel)>0,CONCAT('<font color=red><b>',(SELECT TRIM(SUM(l.menge))+0 FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),'</b></font>'),
                  '<font color=red><b>aus</b></font>'))),'-') as Lager
          FROM auftrag_position ap, artikel a WHERE ap.auftrag='$id' AND a.id=ap.artikel");
      $artikel = $table->DisplayNew("return","Lager","noAction");
    } else {
      $table->Query("SELECT ap.bezeichnung as artikel, ap.nummer as Nummer, if(a.lagerartikel,ap.menge,'-') as M
          FROM auftrag_position ap, artikel a WHERE ap.auftrag='$id' AND a.id=ap.artikel");
      $artikel = $table->DisplayNew("return","Menge","noAction");
    }
    echo $artikel;
    $this->app->ExitXentral();
  }




  function AuftragEditable()
  {
    $this->app->YUI->AARLGEditable();
  }

  function Custom($typ)
  {
    return '';
  }

  function AuftragIconMenu($id,$prefix="")
  {
    $storno = '';
    $status = '';
    $projekt = 0;
    $anfrageid = 0;
    $kreditlimit_ok = 0;
    $lieferantenauftrag = 0;
    $art = '';
    $adresse = 0;
    $shop = 0;
    $shopexportstatus = '';
    $auftragArr = $id <=0?null:$this->app->DB->SelectRow(
      sprintf(
        'SELECT status,projekt,anfrageid,kreditlimit_ok,lieferantenauftrag,art,adresse,shopextstatus,shop 
         FROM auftrag 
         WHERE id = %d 
         LIMIT 1',
        $id
      )
    );
    if(!empty($auftragArr)){
      $shop = $auftragArr['shop'];
      $shopextstatus = $auftragArr['shopextstatus'];
      $adresse = $auftragArr['adresse'];
      $status = $auftragArr['status'];//$auftragArr[''];// $this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
      $projekt = $auftragArr['projekt'];//$this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
      $anfrageid = $auftragArr['anfrageid'];//$this->app->DB->Select("SELECT anfrageid FROM auftrag WHERE id='$id' LIMIT 1");
      $kreditlimit_ok = $auftragArr['kreditlimit_ok'];//$this->app->DB->Select("SELECT kreditlimit_ok FROM auftrag WHERE id='$id' LIMIT 1");
      $lieferantenauftrag = $auftragArr['lieferantenauftrag'];//$this->app->DB->Select("SELECT lieferantenauftrag FROM auftrag WHERE id='$id' LIMIT 1");
      $art = $auftragArr['art'];
    }
    $freigabe = '';
    if($status==='angelegt' || $status=='') {
      $freigabe = "<option value=\"freigabe\">Auftrag freigeben</option>";
      $freigabe .= "<option value=\"freigabemail\">Auftrag freigeben + AB per Mail</option>";
    }

    $proformapopup = '';
    $proformarechnungoption = '';
    $proformarechnungcase = '';
    
    $casehook = '';
    $optionhook = '';
    $kreditlimit = '';
    $shopexport = '';
    $auswahlentsprechendkommissionierung = '';
    $this->app->erp->RunHook('auftragiconmenu_option', 5, $id, $casehook, $optionhook, $status, $prefix);
    
    if($anfrageid > 0){
      $freigabe .= "<option value=\"anfrage\">in Anfrage r&uuml;ckf&uuml;hren</option>";
    }

    if($kreditlimit_ok==0 && $this->app->erp->RechteVorhanden('auftrag','kreditlimit')){
      $kreditlimit .= "<option value=\"kreditlimit\">Kreditlimit f&uuml;r diesen Auftrag freigeben</option>";
    }

    $checkifrgexists = $this->app->DB->Select("SELECT id FROM rechnung WHERE auftragid='$id' LIMIT 1");

    if($status !== 'storniert') {
      $storno = '<option value="storno">Auftrag stornieren</option>';
    }
    else {
      $showrueckgaenig = $this->app->DB->Select(sprintf('SELECT id FROM lieferschein WHERE auftragid=%d LIMIT 1', $id)) <= 0
        && $this->app->DB->Select(sprintf('SELECT id FROM rechnung WHERE auftragid=%d LIMIT 1', $id)) <= 0;
      if($showrueckgaenig) {
        $storno = '<option value="unstorno">Auftrag Storno r&uuml;ckg&auml;ngig</option>';
      }
    }

    /*$anzahl_ls = $this->app->DB->Select("SELECT COUNT(id) FROM lieferschein WHERE auftragid='$id'");
    $anzahl_re = $this->app->DB->Select("SELECT COUNT(id) FROM rechnung WHERE auftragid='$id'");

    if($status=="storniert" && $anzahl_ls <=0 && $anzahl_re<=0)
      $storno = "<option value=\"unstorno\">Auftrag Storno r&uuml;ckg&auml;ngig</option>";
    else if($status!="storniert")
      $storno = "<option value=\"storno\">Auftrag stornieren</option>";*/

    $kommissionierart = $this->app->DB->Select("SELECT kommissionierverfahren FROM projekt WHERE id='$projekt' LIMIT 1");   
    //$art = $this->app->DB->Select("SELECT art FROM auftrag WHERE id='$id' LIMIT 1");
    $alleartikelreservieren = '';
    $teillieferungen = '';
    if($status==='freigegeben') {
      $alleartikelreservieren = "<option value=\"reservieren\">alle Artikel reservieren</option>";


      if($kommissionierart === "zweistufig" || $kommissionierart === "lieferscheinlagerscan" || $kommissionierart==="lieferscheinscan") {
        if($art==="rechnung"){
          $auswahlentsprechendkommissionierung = "<option value=\"versand\">Auto-Versand: RE u. LS erstellen (ohne Lager)</option>";
        }
      	else{
          $auswahlentsprechendkommissionierung = "<option value=\"versand\">Auto-Versand: an Versandzentrum &uuml;bergeben</option>";
        }
      }
      else if($kommissionierart === "lieferschein" || $kommissionierart === "lieferscheinlager"){
        $auswahlentsprechendkommissionierung = "<option value=\"versand\">Auto-Versand: RE u. LS erstellen (mit Lager)</option>";
      }
      else{
        $auswahlentsprechendkommissionierung = "<option value=\"versand\">Auto-Versand: RE u. LS erstellen (ohne Lager)</option>";
      }
    }


    $zertifikatoption = '';
    $zertifikatcase = '';
    if($adresse > 0 && $this->app->erp->RechteVorhanden('zertifikatgenerator','list')) {
      $zertifikate = $this->app->DB->Select("SELECT ds.datei 
      FROM datei_stichwoerter ds 
      INNER JOIN datei_stichwoerter ds2 ON ds.datei = ds2.datei AND ds2.objekt = 'Artikel'
      INNER JOIN auftrag_position ap ON ap.artikel = ds2.parameter AND ap.auftrag = '$id'
      WHERE ds.objekt = 'Adressen' AND ds.parameter = '$adresse'
      GROUP BY ds.datei LIMIT 1");
      if(!empty($zertifikate)) {
        $zertifikatoption = '<option value="zertifikate">Zertifikate anh&auml;ngen</option>';
        $zertifikatcase = "case 'zertifikate': if(!confirm('Zertifikate wirklich laden?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=zertifikate&id=%value%'; break; ";
      }
    }
    
    $artikeleinlagern = '';
    $artikelauslagern = '';
    $menueinlagern = '';
    $menuauslagern = '';
    $shopupdate = '';
    $menushopupdate = '';
    $optioncustom = $this->Custom('option');
    $casecustom = $this->Custom('case');
    if($this->app->erp->RechteVorhanden('auftrageinauslagern','einlagern'))
    {
      $artikeleinlagern = '<option value="artikeleinlagern">Artikel einlagern</option>';
      $menueinlagern = "   case 'artikeleinlagern': if(!confirm('Artikel wirklich einlagern?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrageinauslagern&action=einlagern&id=%value%'; break;";          
    }
    if($this->app->erp->RechteVorhanden('auftrageinauslagern','auslagern'))
    {
      $artikelauslagern = '<option value="artikelauslagern">Artikel auslagern</option>';
      $menuauslagern = " case 'artikelauslagern': if(!confirm('Artikel wirklich auslagern?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrageinauslagern&action=auslagern&id=%value%'; break;";
    }
    
    if($status === 'abgeschlossen' && $this->app->erp->RechteVorhanden('auftrag','shopexport'))
    {
      if($shop > 0 && $shopexportstatus != 'abgeschlossen') {
        $shopexport = '<option value="shopexport">Status an Shop zur&uuml;ckmelden</option>';
        $menushopupdate = " case 'shopexport': if(!confirm('Status wirklich an Shop melden?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=shopexport&id=%value%'; break;";        
      }
    }
    if($this->app->erp->RechteVorhanden('lieferkette', 'create'))
    {
      if(!$this->app->DB->Select("SELECT id FROM lieferkette WHERE auftrag = '$id'"))
      {
        $caselieferkette = "case 'lieferkette':  window.location.href='index.php?module=lieferkette&action=create&id=%value%'; break;";
        $optionlieferkette = "<option value=\"lieferkette\">Lieferkette erzeugen</option>";
      }
    }

    if($this->app->erp->RechteVorhanden('belegeimport', 'belegcsvexport'))
    {
      $casebelegeimport = "case 'belegeimport':  window.location.href='index.php?module=belegeimport&action=belegcsvexport&cmd=auftrag&id=%value%'; break;";
      $optionbelegeimport = "<option value=\"belegeimport\">Export als CSV</option>";
    }

    $extendtext = '';
    if($checkifrgexists>0) {
      $extendtext = 'HINWEIS: Es existiert bereits eine Rechnung zu diesem Auftrag! ';
    }

    if($lieferantenauftrag!='1') {
      $alsrechnung = '<option value="invoice">als Rechnung weiterf&uuml;hren</option>';
    }

    $hookoption = '';
    $hookcase = '';
    $this->app->erp->RunHook('Auftrag_Aktion_option',3, $id, $status, $hookoption);
    $this->app->erp->RunHook('Auftrag_Aktion_case',3, $id, $status, $hookcase);
    $menu ="

      <script type=\"text/javascript\">
      function onchangeauftrag(cmd)
      {
        switch(cmd)
        {
          case 'storno':    if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=delete&id=%value%'; break;
          case 'unstorno':    if(!confirm('Wirklich stornierten Auftrag wieder freigeben?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=undelete&id=%value%'; break;
          case 'teillieferung':     window.location.href='index.php?module=auftrag&action=teillieferung&id=%value%'; break;
          case 'anfrage':   if(!confirm('Wirklich rückführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=anfrage&id=%value%'; break;
          case 'kreditlimit':       if(!confirm('Wirklich Kreditlimit für diesen Auftrag freigeben?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=kreditlimit&id=%value%'; break;
          case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=copy&id=%value%'; break;
          case 'delivery': if(!confirm('Wirklich als Lieferschein weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=lieferschein&id=%value%'; break;
          case 'deliveryinvoice': if(!confirm('Wirklich als Lieferschein und Rechnung weiterführen und Artikel automatisch aus Lager abziehen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=lieferscheinrechnung&id=%value%'; break;
          case 'invoice': if(!confirm('".$extendtext."Wirklich als Rechnung weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=rechnung&id=%value%'; break;
          case 'produktion': if(!confirm('Wirklich als Produktion weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=produktion&id=%value%'; break;
          case 'auftragzubestellung': if(!confirm('Wirklich als Bestellung weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftragzubestellung&action=edit&id=%value%&cmd=auftrag'; break;
          case 'reservieren': if(!confirm('Sollen alle Artikel für diesen Auftrag reserviert werden?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=reservieren&id=%value%'; break;
          case 'pdf':  window.location.href='index.php?module=auftrag&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
          case 'proforma': window.location.href='index.php?module=auftrag&action=proforma&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
          case 'versand': if(!confirm('Wirklich als Versand weiterführen oder Auftrag abschliessen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=versand&id=%value%&cmd=manually'; break;
          case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;
          $caselieferkette
          case 'freigabe':  window.location.href='index.php?module=auftrag&action=freigabe&id=%value%'; break;
          case 'abschluss': if(!confirm('Wirklich manuell ohne erstellen von Lieferschein und/oder Rechnung als abgeschlossen markieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=abschluss&id=%value%&abschluss=%value%'; break;
          case 'alsfreigegeben': if(!confirm('Wirklich als freigegeben markieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=auftrag&action=alsfreigegeben&id=%value%&alsfreigegeben=%value%'; break;
          $menueinlagern
          $menuauslagern

          case 'freigabemail':  window.location.href='index.php?module=auftrag&action=freigabe&id=%value%&freigabe=%value%&cmd=mail'; break;
          $zertifikatcase
          $menushopupdate
          $casecustom
          $casehook
          $casebelegeimport
          $proformarechnungcase
          $hookcase
        }
      }
    </script>


      Aktion:&nbsp;<select id=\"aktion$prefix\" onchange=\"onchangeauftrag(this.value);\">
      <option>bitte w&auml;hlen ...</option>
      $storno
      <option value=\"copy\">Auftrag kopieren</option>
      $freigabe
      <option value=\"abschicken\">Auftrag abschicken</option>
      <!--<option value=\"proforma\">Proforma Rechnung &ouml;ffnen</option>-->
      $alsbestellung
      $alsproduktion
      <option value=\"delivery\">als Lieferschein weiterf&uuml;hren</option>
      $alsrechnung
      $proformarechnungoption
      <option value=\"abschluss\">als abgeschlossen markieren</option>
      $alsfreigegeben
      <!--<option value=\"deliveryinvoice\">manuell weiterf&uuml;hren + ausbuchen</option>-->
      $kreditlimit
      $teillieferungen
      $auswahlentsprechendkommissionierung
      $zertifikatoption
      $artikeleinlagern
      $artikelauslagern
      $shopexport
      $optionlieferkette
      $optioncustom
      $optionhook
      $optionbelegeimport
      $alleartikelreservieren
      $hookoption
      <option value=\"pdf\">PDF &ouml;ffnen</option>
      </select>&nbsp;

    <a href=\"index.php?module=auftrag&action=pdf&id=%value%\" title=\"PDF\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>";
    
    
    $menu .=  "<!--<a href=\"index.php?module=auftrag&action=proforma&id=%value%\" title=\"Proforma Rechnung\"><img border=\"0\" src=\"./themes/new/images/proforma.gif\"></a>-->
      <!--
      <a href=\"index.php?module=auftrag&action=edit&id=%value%\" title=\"Bearbeiten\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
      <a onclick=\"if(!confirm('Wirklich stornieren?')) return false; else window.location.href='index.php?module=auftrag&action=delete&id=%value%';\" title=\"Stornieren\">
      <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=auftrag&action=copy&id=%value%';\" title=\"Kopieren\">
      <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich als Lieferschein weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=auftrag&action=lieferschein&id=%value%';\" title=\"weiterf&uuml;hren als Lieferschein\">
      <img src=\"./themes/new/images/lieferung.png\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich als Rechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=auftrag&action=rechnung&id=%value%';\" title=\"weiterf&uuml;hren als Rechnung\">
      <img src=\"./themes/new/images/rechnung.png\" border=\"0\"></a>

      <a onclick=\"if(!confirm('Sollen alle Artikel f&uuml;r diesen Auftrag reserviert werden?')) return false; else window.location.href='index.php?module=auftrag&action=reservieren&id=%value%';\" title=\"Reservieren\">
      <img src=\"./themes/new/images/reservieren.png\" border=\"0\"></a>

      <a onclick=\"if(!confirm('Wirklich als Versand weiterf&uuml;hren oder Auftrag abschlie&szlig;en?')) return false; else window.location.href='index.php?module=auftrag&action=versand&id=%value%';\" title=\"weiterf&uuml;hren als Versand\">
      <img src=\"./themes/new/images/versand.png\" border=\"0\" alt=\"weiterf&uuml;hren als Versand\"></a>-->";

    //$tracking = $this->AuftragTrackingTabelle($id);

    $menu = str_replace('%value%',$id,$menu);
    return $menu.$proformapopup;
  }
  

  /**
   * @param int $id
   *
   * @return string
   */
  public function AuftragTrackingTabelle($id)
  {
    $table = new EasyTable($this->app);

    if($id > 0) {
      $table->Query(
        "SELECT if(v.versendet_am!='0000-00-00', DATE_FORMAT(v.versendet_am,'%d.%m.%Y'),CONCAT('Heute im Versand<br><a href=\"#\" onclick=\"if(!confirm(\'Auftrag wirklich aus dem Versand nehmen?\')) return false; else window.location.href=\'index.php?module=auftrag&action=ausversand&id=',v.id,'\'\">Aktuell im Versand <br>-> als RMA markieren</a>')) as datum, v.versandunternehmen as versand, v.tracking as L,
      CONCAT('<a href=\"index.php?module=lieferschein&action=pdf&id=',v.lieferschein,'\">',l.belegnr,'</a><br><a href=\"index.php?module=lieferschein&action=edit&id=',v.lieferschein,'\">zum LS</a>') as LS,
      CONCAT('<a href=\"index.php?module=rechnung&action=pdf&id=',v.rechnung,'\">',r.belegnr,'</a><br><a href=\"index.php?module=rechnung&action=edit&id=',v.rechnung,'\">zur RE</a>') as RE,
      if(tracking!='',CONCAT('<a href=\"http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=',tracking,'\" target=\"_blank\">Online-Status</a>'),'') 
      FROM versand v 
      INNER JOIN lieferschein l ON v.lieferschein=l.id 
      LEFT JOIN rechnung r ON v.rechnung=r.id 
      WHERE l.auftragid='$id' AND l.auftrag!=''"
      );
    }

    if(empty($table->datasets)) {
      return  'Keine Versandinformationen vorhanden';
    }

    $result = $table->DisplayNew('return','Tracking','noAction');

    $heuteimversand = $this->app->DB->Select(
      "SELECT if(v.versendet_am!='0000-00-00', DATE_FORMAT(v.versendet_am,'%d.%m.%Y'),'Heute im Versand') as datum
      FROM versand v 
      INNER JOIN lieferschein l ON v.lieferschein=l.id 
      WHERE l.auftragid='$id' AND l.auftrag!=''"
    );

    if($heuteimversand==='Heute im Versand'){
      $result .= "<center><a href=\"\" onclick=\"if(!confirm('Wirklich RMA starten?')) return false; else window.location.href='index.php';\">RMA jetzt starten</a></center>";
    }

    return $result;
  }
  
  function AuftragPDFfromArchiv()
  {
    $id = $this->app->Secure->GetGET("id");
    $archiv = $this->app->DB->Select("SELECT table_id from pdfarchiv where id = '$id' LIMIT 1");
    if($archiv)
    {
      $projekt = $this->app->DB->Select("SELECT projekt from auftrag where id = '".(int)$archiv."'");
    }
    if(class_exists('AuftragPDFCustom'))
    {
      if($archiv)$Brief = new AuftragPDFCustom($this->app,$projekt);
    }else{
      if($archiv)$Brief = new AuftragPDF($this->app,$projekt);
    }
    if($archiv && $content = $Brief->getArchivByID($id))
    {
      header('Content-type: application/pdf');
      header('Content-Disposition: attachment; filename="'.$content['belegnr'].'.pdf"');
      echo $content['file'];
      $this->app->ExitXentral();
    }
    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="Fehler.pdf"');
    $this->app->ExitXentral();
  }

  /**
   * @var null|int $id
   *
   * @return array
   */
  protected function ajaxMiniDetailLager($id = null)
  {
    if($id === null){
      $id = (int)$this->app->Secure->GetGET('id');
    }
    $artikelRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT id, mindesthaltbarkeitsdatum, chargenverwaltung 
        FROM artikel WHERE id = %d AND lagerartikel = 1 LIMIT 1', $id
      )
    );
    if(empty($artikelRow)){
      return ['inhalt' => '<div class="inlinetooltiptable">kein Lagerartikel</div>'];
    }

    $lastCol = 'Lager';
    $table = new EasyTable($this->app);
    $warning = '';
    if(!empty($artikelRow['mindesthaltbarkeitsdatum'])) {
      $table->Query(
        sprintf(
          'SELECT ' . $this->app->erp->FormatMenge('sum(lpi.menge)') . ' as menge, l.bezeichnung as Lager, ' . $this->app->erp->FormatMenge('sum(lm.menge)') . ' as `MHD-Menge`  
          FROM lager l 
          INNER JOIN lager_platz lp ON l.id = lp.lager AND l.geloescht = 0 
          INNER JOIN lager_platz_inhalt lpi ON lp.id = lpi.lager_platz AND lpi.artikel = %d 
          LEFT JOIN (
            SELECT SUM(menge) AS menge, lager_platz 
            FROM `lager_mindesthaltbarkeitsdatum` 
            WHERE artikel = %d
            GROUP BY lager_platz 
          ) AS lm ON lp.id = lm.lager_platz 
          GROUP BY l.id
          ORDER BY l.bezeichnung',
          (int)$id, (int)$id
        )
      );
      if(!empty($table->datasets)) {
        $showBestbefore = false;
        foreach($table->datasets as $dataSetKey => $dataset) {
          if((string)$dataset['menge'] !== (string)$dataset['MHD-Menge']) {
            $showBestbefore = true;
            break;
          }
        }
        if(!$showBestbefore) {
          foreach($table->datasets as $dataSetKey => $dataset) {
            unset($table->datasets[$dataSetKey]['MHD-Menge']);
          }
          unset($table->headings[count($table->headings)-1]);
        } else {
          $lastCol = 'MHD-Menge';
          $table->align[2] = 'right';
          foreach($table->datasets as $dataSetKey => $dataset) {
            if(str_replace(',','.',$dataset[$lastCol]) < str_replace(',','.',$dataset['menge'])) {
              $table->datasets[$dataSetKey][$lastCol] = '<span style="color:red;">'.$dataset[$lastCol].'</span>';
              $warning = '<br /><i style="color:red;">Es sind weniger MHD-Mengen als Lagerartikel vorhanden!</i>';
            }
          }
        }
      }
    }
    elseif(!empty($artikelRow['chargenverwaltung'])) {
      $table->Query(
        sprintf(
          'SELECT ' . $this->app->erp->FormatMenge('sum(lpi.menge)') . ' as menge, l.bezeichnung as Lager , ' . $this->app->erp->FormatMenge('sum(lc.menge)') . ' as `Chargen-Menge`
          FROM lager l 
          INNER JOIN lager_platz lp ON l.id = lp.lager AND l.geloescht = 0 
          INNER JOIN lager_platz_inhalt lpi ON lp.id = lpi.lager_platz AND lpi.artikel = %d
          LEFT JOIN (
            SELECT SUM(menge) AS menge, lager_platz 
            FROM `lager_charge` 
            WHERE artikel = %d
            GROUP BY lager_platz 
          ) AS lc ON lp.id = lc.lager_platz  
          GROUP BY l.id
          ORDER BY l.bezeichnung',
          (int)$id, (int)$id
        )
      );
      if(!empty($table->datasets)) {
        $showBatch = false;
        foreach($table->datasets as $dataSetKey => $dataset) {
          if((string)$dataset['menge'] !== (string)$dataset['Chargen-Menge']) {
            $showBatch = true;
            break;
          }
        }
        if(!$showBatch) {
          foreach($table->datasets as $dataSetKey => $dataset) {
            unset($table->datasets[$dataSetKey]['Chargen-Menge']);
          }
          unset($table->headings[count($table->headings)-1]);
        } else {
          $lastCol = 'Chargen-Menge';
          $table->align[2] = 'right';
          foreach($table->datasets as $dataSetKey => $dataset) {
            if(str_replace(',','.',$dataset[$lastCol]) < str_replace(',','.',$dataset['menge'])) {
              $table->datasets[$dataSetKey][$lastCol] = '<span style="color:red;">'.$dataset[$lastCol].'</span>';
              $warning = '<br /><i style="color:red;">Es sind weniger Chargen-Mengen als Lagerartikel vorhanden!</i>';
            }
          }
        }
      }
    }
    else{
      $table->Query(
        sprintf(
          'SELECT ' . $this->app->erp->FormatMenge('sum(lpi.menge)') . ' as menge, l.bezeichnung as Lager 
          FROM lager l 
          INNER JOIN lager_platz lp ON l.id = lp.lager AND l.geloescht = 0 
          INNER JOIN lager_platz_inhalt lpi ON lp.id = lpi.lager_platz AND lpi.artikel = %d 
          GROUP BY l.id
          ORDER BY l.bezeichnung',
          (int)$id
        )
      );
    }
    $table->align[0] = 'right';
    $table->align[1] = 'left';

    return  [
      'inhalt'=>
        '<div class="inlinetooltiptable"><style> div.inlinetooltiptable > table.mkTable > tbody > tr:nth-child(2n+1) td {background-color:#e0e0e0;} </style>'.$table->DisplayNew('return',$lastCol,'noAction').$warning.'</div>'
    ];
  }

  /**
   * @param null|int $id
   *
   * @return array
   */
  protected function ajaxMiniDetailLagerSaveInterneBemerkung($id = null)
  {
    if($id === null){
      $id = (int)$this->app->Secure->GetGET('id');
    }
    $internebemerkung = $this->app->Secure->GetPOST('internebemerkung');
    $this->app->DB->Update(
      sprintf(
        "UPDATE `auftrag` SET `internebemerkung` = '%s' WHERE `id` = %d LIMIT 1",
        $internebemerkung, $id
      )
    );
    return ['status' => 1];
  }

  /**
   * @param mixed $ret
   */
  protected function sendAjax($ret)
  {
    header('Content-Type: application/json');
    echo json_encode($ret);
    $this->app->ExitXentral();
  }

  /**
   * @param string $parsetarget
   * @param bool   $menu
   */
  public function AuftragMiniDetail($parsetarget='',$menu=true)
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'lager') {
      $ret = $this->ajaxMiniDetailLager($id);
      $this->sendAjax($ret);
    }
    if($cmd === 'saveinternebemerkung') {
      $ret = $this->ajaxMiniDetailLagerSaveInterneBemerkung($id);
      $this->sendAjax($ret);
    }

    if(
      !$this->app->DB->Select(
        sprintf('SELECT deckungsbeitragcalc FROM auftrag WHERE id = %d LIMIT 1',$id)
      )
    ){
      $this->app->erp->BerechneDeckungsbeitrag($id,'auftrag');
    }
    $auftragArr = $this->app->DB->SelectArr(
      sprintf(
        "SELECT *,DATE_FORMAT(lieferdatum,'%%d.%%m.%%Y') as lieferdatum 
        FROM auftrag 
          WHERE id=%d 
          LIMIT 1",
        $id
      )
    );
    $addressId = $auftragArr[0]['adresse'];
    $projectId = $auftragArr[0]['projekt'];
    $adrArr = $this->app->DB->SelectRow(
      sprintf(
        'SELECT kundennummer, name FROM adresse WHERE id=%d LIMIT 1',
        $addressId
      )
    );
    $kundennummer = $adrArr['kundennummer'];
    $projekt = empty($projectId)?'':$this->app->DB->Select(
      sprintf(
        'SELECT abkuerzung FROM projekt WHERE id= %d LIMIT 1',
        $projectId
      )
    );
    $kundenname = $adrArr['name'];
    $this->app->Tpl->Set(
      'KUNDE',
      "<a href=\"index.php?module=adresse&action=edit&id=".$addressId."\" target=\"_blank\">"
      .$kundennummer."</a> ".$kundenname
    );
    if($this->app->erp->RechteVorhanden('projekt','dashboard')){
      $this->app->Tpl->Set(
        'PROJEKT',
        "<a href=\"index.php?module=projekt&action=dashboard&id="
        . $projectId . "\" target=\"_blank\">$projekt</a>"
      );
    }
    else{
      $this->app->Tpl->Set('PROJEKT', $projekt);
    }
    $this->app->Tpl->Set('IHREBESTELLNUMMER',$auftragArr[0]['ihrebestellnummer']);

    $rechnungs = $addressId <= 0?0: $this->app->DB->Select(
      "SELECT SUM(soll-ist) 
      FROM rechnung 
      WHERE status != 'angelegt' AND zahlungsstatus != 'bezahlt' 
        AND adresse = '".$addressId."'"
    );
    $gutschrifts = $addressId <= 0?0:$this->app->DB->Select(
      "SELECT SUM(soll-ist) 
      FROM gutschrift 
      WHERE status != 'angelegt' 
        AND (manuell_vorabbezahlt != '0000-00-00' OR manuell_vorabbezahlt IS NOT NULL) 
        AND adresse = '".$addressId."'"
    );
    
    $kundensaldo = $rechnungs - $gutschrifts;
    $kundensaldo = round($kundensaldo, 2);

    if($kundensaldo > 0){
      $kundensaldo = "<font color=red>-".number_format($kundensaldo,2,',','.')."</font>";
    }
    else if($kundensaldo == 0){
      $kundensaldo = '0,00';
    }
    else{
      $kundensaldo = number_format($kundensaldo,2,',','.');
    }

    $this->app->Tpl->Set('KUNDENSALDO', $kundensaldo);

    $zahlungsweisearr = $this->app->erp->GetZahlungsweise('auftrag', $id);
    if($zahlungsweisearr) {
      foreach($zahlungsweisearr as $k => $v) {
        if($k == $auftragArr[0]['zahlungsweise']) {
          $auftragArr[0]['zahlungsweise'] = $v;
          break;
        }
      }
    }

    $versandartarr = $this->app->erp->GetVersandartAuftrag((int)$projectId);
    if($versandartarr){
      foreach($versandartarr as $k => $v) {
        if($k == $auftragArr[0]['versandart']){
          $auftragArr[0]['versandart'] = $v;
          break;
        }
      }
    }

    $this->app->Tpl->Set('ZAHLWEISE',$auftragArr[0]['zahlungsweise']);
    if($auftragArr[0]['lieferdatum']==='00.00.0000') {
      $auftragArr[0]['lieferdatum']='sofort';
    }
    $this->app->Tpl->Set('WUNSCHLIEFERDATUM',$auftragArr[0]['lieferdatum']);
    $gebuchtezeit = $this->app->DB->Select(
      "SELECT IFNULL(SUM(TIME_TO_SEC(TIMEDIFF(bis, von)))/3600,0) 
      FROM zeiterfassung z 
      LEFT JOIN auftrag_position ap ON ap.id = z.auftragpositionid 
      WHERE z.auftrag = '$id' OR ap.auftrag = '$id'"
    );

    if(0){
      $gebuchtezeit = (int)$gebuchtezeit.":".round(fmod($gebuchtezeit,1)*60);//Alternative Zeitangabe
    }else{
      $gebuchtezeit = str_replace(".", ",", round($gebuchtezeit,2));
    }
    $summebrutto = $this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('DECKUNGSBEITRAG',0);
    $this->app->Tpl->Set('DBPROZENT',0);
    $this->app->Tpl->Set('GEBUCHTEZEIT',0);

    if($auftragArr[0]['ust_befreit']==0){
      $this->app->Tpl->Set('STEUER', "Inland");
    }
    else if($auftragArr[0]['ust_befreit']==1){
      $this->app->Tpl->Set('STEUER', "EU-Lieferung");
    }
    else{
      $this->app->Tpl->Set('STEUER', "Export");
    }

    $this->app->Tpl->Set('DELIVERYTHRESHOLDVATID',!empty($auftragArr[0]['deliverythresholdvatid'])?$auftragArr[0]['deliverythresholdvatid']:'');


    $this->app->Tpl->Set('GESAMTSUMME',number_format($summebrutto,2,",",""));

    //ENDE ZUSTANDSAUTOMAT FARBEN

    // angebot
      
    $angebot[]['angebot'] = $this->app->DB->Select(
      "SELECT CONCAT('<a href=\"index.php?module=angebot&action=edit&id=',an.id,'\" target=\"_blank\">',if(an.belegnr='0' OR an.belegnr='','ENTWURF',an.belegnr),'</a>&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=',an.id,'\" target=\"blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Angebot PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=angebot&action=edit&id=',an.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Angebot bearbeiten\" border=\"0\"></a>') 
      FROM auftrag a 
      LEFT JOIN angebot an ON an.id=a.angebotid 
      WHERE a.id='$id' 
      LIMIT 1"
    );

    if(!empty($angebot)) {
      $cangebot = count($angebot);
      for($li=0;$li<$cangebot;$li++) {
        $this->app->Tpl->Add('ANGEBOT',$angebot[$li]['angebot']);
        if($li<count($angebot)){
          $this->app->Tpl->Add('ANGEBOT', "<br>");
        }
      }
    }
    else{
      $this->app->Tpl->Set('ANGEBOT', '-');
    }
    $lieferschein = $this->app->DB->SelectPairs(
      "SELECT 
        l.id, CONCAT(
            '<a href=\"index.php?module=lieferschein&action=edit&id=',
            l.id,'\" target=\"_blank\"',
            if(l.status='storniert',' title=\"Lieferschein storniert\"><s>','>'),
            if(l.belegnr='0' OR l.belegnr='','ENTWURF',l.belegnr),
            if(l.status='storniert','</s>',''),
            '</a>&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=',l.id,
            '\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Lieferschein PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=lieferschein&action=edit&id=',
            l.id,
            '\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Lieferschein bearbeiten\" border=\"0\"></a>'
            ) as lieferschein
        FROM lieferschein l 
        WHERE l.auftragid='$id'"
    );
    $deliveryNoteIds = [0];
    $hasDeliveryNotes = false;
    $deliveryNoteIdsImplode = '0';
    if(empty($lieferschein)) {
      $this->app->Tpl->Set('LIEFERSCHEIN', '-');
    }
    else{
      $hasDeliveryNotes = true;
      $deliveryNoteIds = array_keys($lieferschein);
      $deliveryNoteIdsImplode = implode(',', $deliveryNoteIds);
      $this->app->Tpl->Set('LIEFERSCHEIN', implode('<br />', $lieferschein));
    }

    /* rechnungen */
  
    $sammelrechnungcheck = $this->app->DB->Select("SELECT * FROM sammelrechnung_position LIMIT 1");
    if($sammelrechnungcheck) {
      $rechnung = $this->app->DB->SelectPairs(
        "SELECT 
          r.id, CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"',if(r.status='storniert',' title=\"Rechnung storniert\"><s>','>'),if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),if(r.status='storniert','</s>',''),'</a>&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>') as rechnung
          FROM rechnung r 
          WHERE r.auftragid='$id'
          union 
          SELECT 
          r.id,CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"',if(r.status='storniert',' title=\"Rechnung storniert\"><s>','>'),if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),if(r.status='storniert','</s>',''),'</a>&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>') as rechnung
          FROM rechnung r 
          INNER JOIN sammelrechnung_position s ON r.id = s.rechnung 
          INNER JOIN auftrag_position p ON s.auftrag_position_id = p.id 
          WHERE p.auftrag='$id'
          union 
          SELECT 
          r.id,CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"',if(r.status='storniert',' title=\"Rechnung storniert\"><s>','>'),if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),if(r.status='storniert','</s>',''),'</a>&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>') as rechnung
          FROM rechnung r 
          INNER JOIN sammelrechnung_position s ON r.id = s.rechnung 
          INNER JOIN lieferschein_position lp ON lp.id = s.lieferschein_position_id
          INNER JOIN auftrag_position p ON p.id = lp.auftrag_position_id
          WHERE p.auftrag='$id'
          ");
      $rechnungids = array_keys($rechnung);
      /*$this->app->DB->SelectArr(
        "SELECT r.id as rid
        FROM rechnung r
        WHERE r.auftragid ='$id' AND r.auftrag!=''
        union
        SELECT s.rechnung as rid
        FROM sammelrechnung_position s
        INNER JOIN auftrag_position p ON s.auftrag_position_id = p.id
        WHERE p.auftrag = '$id' "
      );*/
    }
    else{
      $rechnung = $this->app->DB->SelectPairs(
        "SELECT 
          r.id, CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"',if(r.status='storniert',' title=\"Rechnung storniert\"><s>','>'),if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),if(r.status='storniert','</s>',''),'</a>&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>'
          ) as rechnung
        FROM rechnung r 
        WHERE r.auftragid='$id'"
      );

      $rechnungids =array_keys($rechnung);
      /*$this->app->DB->SelectArr(
        "SELECT r.id as rid
        FROM rechnung r
        WHERE r.auftragid='$id' AND r.auftrag!='' "
      );*/
    }
    
    if(!$rechnung) {
      $rechnung = $this->app->DB->SelectPairs(
        "SELECT 
                r.id, CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"',if(r.status='storniert',' title=\"Rechnung storniert\"><s>','>'),if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),if(r.status='storniert','</s>',''),'</a>&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>'
                    ) as rechnung
         FROM rechnung r 
         INNER JOIN auftrag a ON a.rechnungid = r.id
         WHERE a.id='$id' 
      ");
    }

    if(!empty($rechnung)){
      $this->app->Tpl->Set('RECHNUNG', implode('<br />', $rechnung));
    }
    else{
      $this->app->Tpl->Set('RECHNUNG', '-');
    }

    /* ende rechnungen */
    $positionIdToArticleIds = $this->app->DB->SelectPairs(
      sprintf(
        'SELECT id, artikel 
        FROM auftrag_position 
        WHERE auftrag = %d ',
        $id
      )
    );
    $articleIds = array_unique(array_values($positionIdToArticleIds));
    $positionIds = array_keys($positionIdToArticleIds);
    if(empty($articleIds)) {
      $articleIds = [0];
    }
    $bpPositions = [];
    $bpIds = [0];
    $bIds = [0];
    $hasSupplierOrder = false;
    if(empty($positionIds)) {
      $positionIds = [0];
    }
    else {
      $bpPositions = $this->app->DB->SelectPairs(
        sprintf(
          'SELECT bp.id, bp.bestellung 
          FROM bestellung_position AS bp 
          WHERE bp.auftrag_position_id IN (%s)',
          implode(',', $positionIds)
        )
      );

      if(!empty($bpPositions)) {
        $bpIds = array_keys($bpPositions);
        $bIds = array_unique(array_values($bpPositions));
        $hasSupplierOrder = true;
      }
    }
    $bpIdsImplode = implode(', ', $bpIds );
    $bIdsImplode = implode(', ', $bIds );

    $articleIdsimplode = implode(', ', $articleIds );
    $positionIdsImplode = implode(', ', $positionIds );

    $check = empty($hasSupplierOrder)?[]:$this->app->DB->SelectPairs(
      sprintf(
        'SELECT b.id, b.belegnr
        FROM bestellung b 
        WHERE b.id IN (%s) 
        ORDER BY b.belegnr, b.id',
        $bIdsImplode
      )
    );
    if($check) {
      $bestellungen = [];
      foreach($check as $supplierOrderId => $supplierOrderNumber) {
        $bestellungen[] = '<a href="index.php?module=bestellung&action=edit&id='
          .$supplierOrderId.'" target="_blank">'
          .($supplierOrderNumber?$supplierOrderNumber:'ENTWURF')
          .'</a>
                            <a href="index.php?module=bestellung&action=pdf&id='
          .$supplierOrderId.'" target="_blank"><img src="./themes/new/images/pdf.svg" title="Bestellung PDF" border="0"></a>
                            <a href="index.php?module=bestellung&action=edit&id='
          .$supplierOrderId.'" target="_blank"><img src="./themes/new/images/edit.svg" title="Bestellung bearbeiten" border="0"></a>';
      }
      $this->app->Tpl->Set('BESTELLUNG', implode('<br />', $bestellungen));
    }

    $returnOrders = (array)$this->app->DB->SelectArr(
      sprintf(
        'SELECT ro.id, ro.belegnr, ro.status
        FROM `auftrag` AS `o`
        LEFT JOIN `lieferschein` AS `dn` ON o.id = dn.auftragid  
        INNER JOIN `retoure` AS `ro` ON ro.auftragid = o.id OR (ro.lieferscheinid = dn.id)
        WHERE o.id = %d
        ORDER BY ro.id',
        $id
      )
    );
    $returnOrderHtml = [];
    foreach($returnOrders as $returnOrderKey => $returnOrder) {
      $returnOrderId = $returnOrder['id'];
      $returnOrderNumber = $returnOrder['belegnr'];
      if(empty($returnOrderNumber)) {
        $returnOrderNumber = 'ENTWURF';
      }
      $isReturnOrderCanceled = $returnOrder['status'] === 'storniert';
      $returnOrderHtml[] = '<a href="index.php?module=retoure&action=edit&id='
        .$returnOrderId.'" target="_blank">'
        .($isReturnOrderCanceled?'<s>':'').$returnOrderNumber.($isReturnOrderCanceled?'</s>':'')
        .'</a>
                            <a href="index.php?module=retoure&action=pdf&id='
        .$returnOrderId
        .'" target="_blank"><img src="./themes/new/images/pdf.svg" alt="Retoure PDF" title="Retoure PDF" border="0"></a>
                            <a href="index.php?module=retoure&action=edit&id='
        .$returnOrderId
        .'" target="_blank"><img src="./themes/new/images/edit.svg" alt="Retoure bearbeiten" title="Retoure bearbeiten" border="0"></a>';
    }
    $this->app->Tpl->Set('RETOURE', implode('<br />', $returnOrderHtml));

    $priceRequests = empty($hasSupplierOrder)?[]: (array)$this->app->DB->SelectArr(
      sprintf(
        "SELECT pa.id, pa.belegnr, pa.status
        FROM `bestellung` AS `b` 
        INNER JOIN `preisanfrage` AS `pa` ON pa.id=b.preisanfrageid
        WHERE b.id IN (%s) 
        GROUP BY pa.belegnr, pa.id 
        ORDER BY pa.belegnr, pa.id",
        $bIdsImplode
      )
    );
    if(!empty($priceRequests)){
      $priceRequestsHtml = [];
      foreach($priceRequests as $priceRequest) {
        $priceRequestId = $priceRequest['id'];
        $priceRequestNumber = $priceRequest['belegnr'];
        if(empty($priceRequestNumber)) {
          $priceRequestNumber = 'ENTWURF';
        }
        $isPriceRequestIsCanceled = $priceRequest['status'] === 'storniert';
        $priceRequestsHtml[] = '<a href="index.php?module=preisanfrage&action=edit&id='
          .$priceRequestId.'" target="_blank">'
          .($isPriceRequestIsCanceled?'<s>':'').($priceRequestNumber).($isPriceRequestIsCanceled?'</s>':'').'</a>
                            <a href="index.php?module=preisanfrage&action=pdf&id='
          .$priceRequestId.'" target="_blank"><img src="./themes/new/images/pdf.svg" title="Preisanfrage PDF" border="0"></a>
                            <a href="index.php?module=preisanfrage&action=edit&id='
          .$priceRequestId.'" target="_blank"><img src="./themes/new/images/edit.svg" title="Preisanfrage bearbeiten" border="0"></a>';
      }
      $this->app->Tpl->Set('PREISANFRAGE', implode('<br />', $priceRequestsHtml));
    }

    $tmpVersand = !$hasDeliveryNotes?[]: $this->app->DB->SelectFirstCols(
      "SELECT if(v.versendet_am!='0000-00-00', 
        CONCAT(DATE_FORMAT( v.versendet_am,'%d.%m.%Y'),' ',v.versandunternehmen),
        CONCAT(
        'Heute im Versand<br><a href=\"#\" onclick=\"if(!confirm(\'Auftrag wirklich aus dem Versand nehmen?\')) return false; else window.location.href=\'index.php?module=auftrag&action=ausversand&id=',
        v.id,
        '\'\">Aktuell im Versand <br>-> als RMA markieren</a>'
        )
        ) as datum
      FROM versand AS v 
      INNER JOIN lieferschein l ON v.lieferschein=l.id 
      LEFT JOIN rechnung r ON v.rechnung=r.id 
      WHERE l.id IN (".$deliveryNoteIdsImplode.") AND l.auftragid='$id' AND l.auftrag!=''"
    );
    $tracking = !$hasDeliveryNotes?null:$this->app->DB->SelectArr("SELECT
       if(v.tracking_link IS NOT NULL AND v.tracking_link != '', CONCAT(UPPER(versandunternehmen), ':<a href=\"', v.tracking_link, '\">', v.tracking, '</a>'),
        if(versandunternehmen = 'dhlexpress' AND l.land = 'DE' AND v.tracking != '', CONCAT(UPPER(versandunternehmen), ':<a href=\"https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?piececode=', v.tracking, '\" target=\"_blank\">', v.tracking, '</a>'),
          if(versandunternehmen = 'dhlexpress' AND l.land != 'DE' AND v.tracking != '', CONCAT(UPPER(versandunternehmen), ':<a href=\"https://www.dhl.com/en/hidden/component_library/express/local_express/dhl_de_tracking/de/sendungsverfolgung_dhlde.html?AWB=', v.tracking, '&brand=DHL\" target=\"_blank\">', v.tracking, '</a>'),
            if((versandunternehmen='dhl' OR versandunternehmen='intraship' OR versandunternehmen LIKE '%dhl%') AND v.tracking!='',          CONCAT(UPPER(versandunternehmen),':<a href=\"http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=',v.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),        
              if(versandunternehmen LIKE '%dpd%',CONCAT(UPPER(versandunternehmen),':<a href=\"https://tracking.dpd.de/parcelstatus/?locale=de_DE&query=',v.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),
                if(versandunternehmen LIKE '%ups%' AND v.tracking != '', CONCAT(UPPER(versandunternehmen),':<a href=\"https://www.ups.com/track?loc=de_DE&tracknum=',v.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),
                  if(versandunternehmen LIKE '%gls%' AND v.tracking != '', CONCAT(UPPER(versandunternehmen),':<a href=\"https://www.gls-group.eu/276-I-PORTAL-WEB/content/GLS/DE03/DE/5004.htm?txtRefNo=',v.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),
                    if(v.tracking!='',
                      CONCAT(UPPER(versandunternehmen),': ',v.tracking),'nicht vorhanden')
                  )
                )
              )
            )
          )
        )
      ) as versand2,
      if(versandunternehmen = 'dhlexpress' AND l.land = 'DE' AND vp.tracking != '', CONCAT(UPPER(versandunternehmen), ':<a href=\"https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?piececode=', vp.tracking, '\" target=\"_blank\">', vp.tracking, '</a>'),  
        if(versandunternehmen = 'dhlexpress' AND l.land != 'DE' AND vp.tracking != '', CONCAT(UPPER(versandunternehmen), ':<a href=\"https://www.dhl.com/en/hidden/component_library/express/local_express/dhl_de_tracking/de/sendungsverfolgung_dhlde.html?AWB=', vp.tracking, '&brand=DHL\" target=\"_blank\">', vp.tracking, '</a>'),
          if( (versandunternehmen='dhl' OR versandunternehmen='intraship') AND vp.tracking!='',          CONCAT(UPPER(versandunternehmen),':<a href=\"http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=',vp.tracking,'\" target=\"_blank\">',vp.tracking,'</a>'),        
            if(versandunternehmen LIKE '%dpd%',CONCAT(UPPER(versandunternehmen),':<a href=\"https://tracking.dpd.de/parcelstatus/?locale=de_DE&query=',vp.tracking,'\" target=\"_blank\">',vp.tracking,'</a>'),
              if(versandunternehmen LIKE '%ups%' AND vp.tracking != '', CONCAT(UPPER(versandunternehmen),':<a href=\"https://www.ups.com/track?loc=de_DE&tracknum=',vp.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),
                if(versandunternehmen LIKE '%gls%' AND vp.tracking != '', CONCAT(UPPER(versandunternehmen),':<a href=\"https://www.gls-group.eu/276-I-PORTAL-WEB/content/GLS/DE03/DE/5004.htm?txtRefNo=',vp.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),
                  if(vp.tracking!='',
                    CONCAT(UPPER(versandunternehmen),': ',vp.tracking),'nicht vorhanden')
                )
              )
            )
          )
        ) 
      ) as versand3,
         v.tracking as tracking2, vp.tracking as tracking3
        FROM versand AS v 
        INNER JOIN lieferschein AS l ON v.lieferschein=l.id
        LEFT JOIN versandpakete AS vp ON v.id = vp.versand  
        WHERE l.id IN (".$deliveryNoteIdsImplode.") AND l.auftragid='$id' AND l.auftrag!='' 
        ORDER BY v.id, vp.nr"
    );

    $ctracking = $tracking?count($tracking):0;
    for($counti=0;$counti < $ctracking; $counti++) {
      if($tracking[$counti]['tracking2']!=''){
        if($counti == 0 || $tracking[$counti]['tracking2'] != $tracking[$counti-1]['tracking2']){
          $tmp[]=$tracking[$counti]['versand2'];
        }
        if($tracking[$counti]['tracking3'] != ''){
          $tmp[]=$tracking[$counti]['versand3'];
        }
      }
    }

    if(is_array($tmp)){
      $tracking = implode(', ', $tmp);
    }
    else{
      $tracking = '';
    }

    if(is_array($tmpVersand)){
      $tmpVersand = implode(', ', $tmpVersand);
    }

    if($tracking!='' && $tracking!=' '){
      $this->app->Tpl->Set('TRACKING', $tracking);
    }
    else {
      $this->app->Tpl->Set('TRACKING',$tmpVersand);
    }


    $icons = $this->app->YUI->IconsSQL();
    if(strpos($icons,'aac.status')) {
      $icons = $this->app->DB->Select(
        "SELECT $icons 
        FROM auftrag a 
        LEFT JOIN `auftragsampel_auftrag_cache` aac ON a.id = aac.auftrag 
        WHERE a.id='$id' 
        LIMIT 1"
      );
    }
    else{
      $icons = $this->app->DB->Select(
        "SELECT $icons FROM auftrag a WHERE a.id='$id' LIMIT 1"
      );
    }
    $this->app->Tpl->Set('STATUSICONS',$icons);

    $this->app->Tpl->Set('STATUS',$auftragArr[0]['status']);
    $this->app->Tpl->Set('VERSANDART',$auftragArr[0]['versandart']);
    $this->app->Tpl->Set('INTERNET',$auftragArr[0]['internet']);
    $this->app->Tpl->Set('TRANSAKTIONSNUMMER',$auftragArr[0]['transaktionsnummer']);

    $onlineshopName = $auftragArr[0]['shop'] <= 0?'':$this->app->DB->Select(
      sprintf(
        'SELECT `bezeichnung` FROM `shopexport` WHERE `id` = %d LIMIT 1',
        $auftragArr[0]['shop']
      )
    );
    if($onlineshopName != ''){
      $this->app->Tpl->Set('ONLINESHOP', $onlineshopName);
    }

    if($menu) {
      $menu = $this->AuftragIconMenu($id);
      $this->app->Tpl->Set('MENU',$menu);
    }

    // ARTIKEL
    $auftragRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT status, projekt, standardlager,teillieferungvon FROM auftrag WHERE id = %d',
        $id
      )
    );
    $status = $auftragRow['status'];//$this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);
    
    //$lagermehr = " <img class=\"contenttooltip\" src=\"./themes/".$this->app->Conf->WFconf['defaulttheme']."/images/tooltip_grau.png\" />";
    
    if($status==='freigegeben' || $status==='angelegt') {
      $anzahllager = $this->app->DB->Select("SELECT count(id) FROM lager WHERE geloescht = 0");
      $standardlager = $auftragRow['standardlager'];//$this->app->DB->Select("SELECT standardlager FROM auftrag WHERE id = '$id' LIMIT 1");
      $projektlager = 0;
      if(!$standardlager){
        $projektlager = $this->app->DB->Select("SELECT projektlager FROM projekt WHERE id = '".$auftragArr[0]['projekt']."' LIMIT 1");
      }
      if($projektlager){
        $projektlager = $auftragArr[0]['projekt'];
      }
      $standardlagertext = '';
      if($standardlager){
        $standardlagertext = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id = '$standardlager' LIMIT 1");
      }
      
      $hookjoins = '';
      $hookcolumns = '';

      $lastcolumn = 'Reservierung';
      $_lastcolumn = $lastcolumn;
      $this->app->erp->RunHook("auftrag_minidetail_hook1",4, $id, $hookcolumns, $hookjoins, $lastcolumn);

      $sql =
        "SELECT 

          if(ap.explodiert_parent > 1,CONCAT('***',if(CHAR_LENGTH(TRIM(ap.beschreibung)) > 0,CONCAT(ap.bezeichnung,' *'),ap.bezeichnung)),if(CHAR_LENGTH(ap.beschreibung) > 0,CONCAT(ap.bezeichnung,' *'),ap.bezeichnung)) as artikel, 

          CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', ap.nummer,'</a>') as Nummer, 
          if(isnull(b.id),'-',concat('<a href=\"index.php?module=bestellung&action=edit&id=',b.id,'\">',if(b.belegnr <> '', b.belegnr,'ENTWURF'),'</a>')) as Bestellung,
          ".$this->app->erp->FormatMenge("ifnull(a.gewicht,0)")." as gewicht,
          
          ".$this->app->erp->FormatMenge("ifnull(ap.menge,0)")." as Menge,

          concat(if(a.lagerartikel,
            if(a.porto,
              'Porto',
                if(
                    (round(( 
                        (SELECT TRIM(SUM(l.menge))+0 
                          FROM lager_platz_inhalt l 
                          LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ) 
                        - IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        
                        WHERE r.artikel=ap.artikel AND ((r.objekt='auftrag' AND
                          (
                            (  r.parameter!='$id')
                              OR (
                              r.parameter='$id' AND r.posid!=ap.id)
                          )) OR r.objekt!='auftrag' )
                        
                        
                        ),0)
                      ),8) 
                        >= round(ap.menge,8) AND (
                        
                        round((SELECT TRIM(SUM(l.menge))+0 
                          FROM lager_platz_inhalt l 
                          LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ) 
                        - IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        WHERE r.artikel=ap.artikel AND ((r.objekt='auftrag' AND
                          
                              r.parameter!='$id')
                              
                           OR r.objekt!='auftrag' )
                        
                        
                        ),0),8) >= 
                        round((SELECT sum(ap3.menge) FROM auftrag_position ap3 WHERE ap3.auftrag = '$id' AND ap3.artikel = ap.artikel),8)
                        OR round(ap.menge,8) <= round(IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        WHERE r.artikel=ap.artikel AND r.objekt='auftrag' AND
                          
                              r.parameter='$id'
                              
                           
                        
                        
                        ),0),8)
                        )) OR  ((
                        round(ifnull((SELECT SUM(l.menge)
                          FROM lager_platz_inhalt l 
                          INNER JOIN lager_platz lp ON lp.id=l.lager_platz 
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ),0),8) - round(IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        WHERE r.artikel=ap.artikel AND ((r.objekt!='auftrag' OR
                          
                              r.parameter!='$id')
                              
                            )
                        
                        
                        ),0),8)
                        > round(IFNULL(apc.menge,0),8)
                        
                        )
                        ) ,
                

                    CONCAT(
                    ifnull(
                      ifnull((
                        SELECT  ".$this->app->erp->FormatMenge("TRIM(SUM(l.menge))+0")."
                        FROM lager_platz_inhalt l 
                        LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                        WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1)
                      ,0)

                    ,0),
                      if(round((
                        SELECT TRIM(SUM(l.menge))+0 
                        FROM lager_platz_inhalt l 
                        LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                        WHERE l.artikel=ap.artikel AND lp.autolagersperre=1 AND lp.sperrlager!=1),8)>0,
                      CONCAT(' + <a href=\"index.php?module=artikel&action=lager&id=',ap.artikel,'\" title=\"Nachschublager\" target=\"_blank\"><font color=red><b>',(SELECT ".$this->app->erp->FormatMenge("(SUM(l.menge))")." FROM lager_platz_inhalt l LEFT JOIN lager_platz lp ON lp.id=l.lager_platz WHERE l.artikel=ap.artikel AND lp.autolagersperre=1 AND lp.sperrlager!=1),'(N)<b></font></a>'),'')
                      )


                    ,
                    if(round((
                    
                    (SELECT ".$this->app->erp->FormatMenge("(SUM(l.menge))")." 
                      FROM lager_platz_inhalt l 
                    LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                    WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1)
                    - IFNULL((SELECT SUM(r.menge) FROM lager_reserviert r WHERE r.artikel=ap.artikel AND r.objekt='auftrag' AND r.parameter!='$id'),0)),8)>=0,
                    CONCAT('<font color=red><b>',
                        (SELECT ".$this->app->erp->FormatMenge("(SUM(l.menge))")." 
                        FROM lager_platz_inhalt l 
                        LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                        WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1)

                        ,'</b></font>'),
                      '<font color=red><b>aus</b></font>')
                  )
            )
                  
           ,if(a.porto,'Porto',if(a.stueckliste,'Set','kein Lagerartikel'))),'".($anzahllager >= 1?" lagermehr(',ap.artikel,') ":"")."') as Lager, 
          ".($standardlager?"
          
          concat(if(a.lagerartikel,
            if(a.porto,
              'Porto',
                if(
                    (( 
                        (SELECT TRIM(SUM(l.menge))+0 
                          FROM lager_platz_inhalt l 
                          INNER JOIN lager_platz lp ON lp.id=l.lager_platz AND lp.lager = '$standardlager'
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ) 
                        - IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        INNER JOIN auftrag a2 ON r.parameter = a2.id AND r.objekt = 'auftrag' AND a2.standardlager = '$standardlager'
                        WHERE r.artikel = ap.artikel  AND (r.parameter <> '$id' OR r.parameter = '$id' AND r.posid != ap.id)
                        ),0)
                      ) 
                        >= ap.menge AND (
                        
                        (SELECT TRIM(SUM(l.menge))+0 
                          FROM lager_platz_inhalt l 
                          INNER JOIN lager_platz lp ON lp.id=l.lager_platz  AND lp.lager = '$standardlager'
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ) 
                        - IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        INNER JOIN auftrag a2 ON r.parameter = a2.id AND r.objekt = 'auftrag' AND a2.standardlager = '$standardlager'
                        WHERE r.artikel = ap.artikel  AND (r.parameter <> '$id')
                        ),0)
                        >= 
                        (SELECT sum(ap3.menge) FROM auftrag_position ap3 WHERE ap3.auftrag = '$id' AND ap3.artikel = ap.artikel)
                        OR ap.menge <= IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        WHERE r.artikel=ap.artikel AND r.objekt='auftrag' AND
                          
                              r.parameter='$id'
                              
                           
                        
                        
                        ),0)
                        ))  AND (
                        
                        ( 
                        (SELECT TRIM(SUM(l.menge))+0 
                          FROM lager_platz_inhalt l 
                          LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ) 
                        - IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        
                        WHERE r.artikel=ap.artikel AND ((r.objekt='auftrag' AND
                          (
                            (  r.parameter!='$id')
                              OR (
                              r.parameter='$id' AND r.posid!=ap.id)
                          )) OR r.objekt!='auftrag' )
                        
                        
                        ),0)
                      ) 
                        >= ap.menge AND (
                        
                        (SELECT TRIM(SUM(l.menge))+0 
                          FROM lager_platz_inhalt l 
                          LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ) 
                        - IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        WHERE r.artikel=ap.artikel AND ((r.objekt='auftrag' AND
                          
                              r.parameter!='$id')
                              
                           OR r.objekt!='auftrag' )
                        
                        
                        ),0) >= 
                        (SELECT sum(ap3.menge) FROM auftrag_position ap3 WHERE ap3.auftrag = '$id' AND ap3.artikel = ap.artikel)
                        OR ap.menge <= IFNULL((
                        SELECT SUM(r.menge) FROM lager_reserviert r 
                        WHERE r.artikel=ap.artikel AND r.objekt='auftrag' AND
                          
                              r.parameter='$id'
                              
                        
                        ),0)
                        )
                        
                        )
                        
                        ,
                

                    CONCAT(
                    ifnull(
                      trim(ifnull(lpin2.menge
                      ,0))+0

                    ,0),
                      if((
                        SELECT TRIM(SUM(l.menge))+0 
                        FROM lager_platz_inhalt l 
                        INNER JOIN lager_platz lp ON lp.id=l.lager_platz  AND lp.lager = '$standardlager'
                        WHERE l.artikel=ap.artikel AND lp.autolagersperre=1 AND lp.sperrlager!=1)>0,
                      CONCAT(' + <a href=\"index.php?module=artikel&action=lager&id=',ap.artikel,'\" title=\"Nachschublager\" target=\"_blank\"><font color=red><b>',(SELECT TRIM(SUM(l.menge))+0 FROM lager_platz_inhalt l LEFT JOIN lager_platz lp ON lp.id=l.lager_platz WHERE l.artikel=ap.artikel AND lp.lager='$standardlager' AND lp.autolagersperre=1 AND lp.sperrlager!=1),'(N)<b></font></a>'),'')
                      )


                    ,
                    if((
                    
                    (SELECT TRIM(SUM(l.menge))+0 
                      FROM lager_platz_inhalt l 
                    INNER JOIN lager_platz lp ON lp.id=l.lager_platz  AND lp.lager = '$standardlager'
                    WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1) 
                    - IFNULL((SELECT SUM(r.menge) FROM lager_reserviert r WHERE r.artikel=ap.artikel AND r.objekt='auftrag' AND r.parameter!='$id'),0))>=0,
                    CONCAT('<font color=red><b>',
                        (SELECT TRIM(ifnull(SUM(l.menge),0))+0
                        FROM lager_platz_inhalt l 
                        INNER JOIN lager_platz lp ON lp.id=l.lager_platz  AND lp.lager = '$standardlager'
                        WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1)

                        ,'</b></font>'),
                      '<font color=red><b>aus</b></font>')
                  )
            )
                  
           ,if(a.porto,'Porto',if(a.stueckliste,'Set','-'))) ,'".($anzahllager >= 1?" lagermehr(',ap.artikel,') ":"")."') as `$standardlagertext` ,
          
          ":($projektlager?"
          
        
          
          concat(if(a.lagerartikel,
            if(a.porto,
              'Porto',
                if(
                    (( 
                        ifnull((SELECT TRIM(ifnull(SUM(l.menge),0))+0 
                          FROM lager_platz_inhalt l 
                          INNER JOIN lager_platz lp ON lp.id=l.lager_platz 
                          INNER JOIN lager lag ON lp.lager = lag.id AND lag.projekt = '$projektlager'
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ),0) 
                        - IFNULL((
                        SELECT ifnull(SUM(r.menge),0) FROM lager_reserviert r 
                        INNER JOIN auftrag a2 ON r.parameter = a2.id AND r.objekt = 'auftrag' AND a2.projekt = '$projektlager'
                        WHERE r.artikel = ap.artikel  AND (r.parameter <> '$id' OR r.parameter = '$id' AND r.posid != ap.id)
                        ),0)
                      ) 
                        >= ap.menge AND (
                        
                        (SELECT TRIM(ifnull(SUM(l.menge),0))+0 
                          FROM lager_platz_inhalt l 
                          INNER JOIN lager_platz lp ON lp.id=l.lager_platz 
                          INNER JOIN lager lag ON lp.lager = lag.id AND lag.projekt = '$projektlager'
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ) 
                        - IFNULL((
                        SELECT ifnull(SUM(r.menge),0) FROM lager_reserviert r 
                        INNER JOIN auftrag a2 ON r.parameter = a2.id AND r.objekt = 'auftrag' AND a2.projekt = '$projektlager'
                        WHERE r.artikel = ap.artikel  AND (r.parameter <> '$id')
                        ),0)
                        >= 
                        (SELECT ifnull(sum(ap3.menge),0) FROM auftrag_position ap3 WHERE ap3.auftrag = '$id' AND ap3.artikel = ap.artikel)
                        OR ap.menge <= IFNULL((
                        SELECT ifnull(SUM(r.menge),0) FROM lager_reserviert r 
                        WHERE r.artikel=ap.artikel AND r.objekt='auftrag' AND
                          
                              r.parameter='$id'
                              
                           
                        
                        
                        ),0)
                        ))  AND (
                        
                        ( 
                        (SELECT TRIM(ifnull(SUM(l.menge),0))+0 
                          FROM lager_platz_inhalt l 
                          LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ) 
                        - IFNULL((
                        SELECT ifnull(SUM(r.menge),0) FROM lager_reserviert r 
                        
                        WHERE r.artikel=ap.artikel AND ((r.objekt='auftrag' AND
                          (
                            (  r.parameter!='$id')
                              OR (
                              r.parameter='$id' AND r.posid!=ap.id)
                          )) OR r.objekt!='auftrag' )
                        
                        
                        ),0)
                      ) 
                        >= ap.menge AND (
                        
                        (SELECT TRIM(ifnull(SUM(l.menge),0))+0 
                          FROM lager_platz_inhalt l 
                          LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
                          WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1 
                        ) 
                        - IFNULL((
                        SELECT ifnull(SUM(r.menge),0) FROM lager_reserviert r 
                        WHERE r.artikel=ap.artikel AND ((r.objekt='auftrag' AND
                          
                              r.parameter!='$id')
                              
                           OR r.objekt!='auftrag' )
                        
                        
                        ),0) >= 
                        (SELECT ifnull(sum(ap3.menge),0) FROM auftrag_position ap3 WHERE ap3.auftrag = '$id' AND ap3.artikel = ap.artikel)
                        OR ap.menge <= IFNULL((
                        SELECT ifnull(SUM(r.menge),0) FROM lager_reserviert r 
                        WHERE r.artikel=ap.artikel AND r.objekt='auftrag' AND
                          
                              r.parameter='$id'
                              
                        
                        ),0)
                        )
                        
                        )
                        
                        ,
                

                    CONCAT(
                    
                       ".$this->app->erp->FormatMenge("ifnull(lpin2.menge,0)")."

                    ,
                      if((
                        SELECT ifnull(SUM(l.menge),0)
                        FROM lager_platz_inhalt l 
                        INNER JOIN lager_platz lp ON lp.id=l.lager_platz 
                        INNER JOIN lager lag ON lp.lager = lag.id AND lag.projekt = '$projektlager'
                        WHERE l.artikel=ap.artikel AND lp.autolagersperre=1 AND lp.sperrlager!=1)>0,
                      CONCAT(' + <a href=\"index.php?module=artikel&action=lager&id=',ap.artikel,'\" title=\"Nachschublager\" target=\"_blank\"><font color=red><b>',(SELECT ".$this->app->erp->FormatMenge("ifnull(SUM(l.menge),0)")." FROM lager_platz_inhalt l LEFT JOIN lager_platz lp ON lp.id=l.lager_platz WHERE l.artikel=ap.artikel AND lp.lager='$standardlager' AND lp.autolagersperre=1 AND lp.sperrlager!=1),'(N)<b></font></a>'),'')
                     )


                    ,
                    if((
                    
                    (SELECT TRIM(ifnull(SUM(l.menge),0))+0 
                      FROM lager_platz_inhalt l 
                      INNER JOIN lager_platz lp ON lp.id=l.lager_platz 
                      INNER JOIN lager lag ON lp.lager = lag.id AND lag.projekt = '$projektlager'
                    WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1) 
                    - IFNULL((SELECT ifnull(SUM(r.menge),0) FROM lager_reserviert r WHERE r.artikel=ap.artikel AND r.objekt='auftrag' AND r.parameter!='$id'),0))>=0,
                    CONCAT('<font color=red><b>',
                        (SELECT ".$this->app->erp->FormatMenge("ifnull(SUM(l.menge),0)")."
                        FROM lager_platz_inhalt l 
                        INNER JOIN lager_platz lp ON lp.id=l.lager_platz 
                        INNER JOIN lager lag ON lp.lager = lag.id AND lag.projekt = '$projektlager'
                        WHERE l.artikel=ap.artikel AND lp.autolagersperre!=1 AND lp.sperrlager!=1)

                        ,'</b></font>'),
                      '<font color=red><b>aus</b></font>')
                  )
            )
                  
           ,if(a.porto,'Porto',if(a.stueckliste,'Set','-'))),'".($anzahllager >= 1?" lagermehr(',ap.artikel,') ":"")."') as `Projektlager` ,
          
          
          ":''))."
            CONCAT(
                IFNULL((SELECT ".$this->app->erp->FormatMenge("ifnull(SUM(r.menge),0)")." FROM lager_reserviert r WHERE r.artikel=ap.artikel AND r.objekt='auftrag' AND r.parameter='$id' AND (r.posid=ap.id OR (r.artikel=ap.artikel AND r.posid=0))),'0')

                ,
                '&nbsp;/ ',(SELECT ".$this->app->erp->FormatMenge("ifnull(SUM(r.menge),0)")." FROM lager_reserviert r WHERE r.artikel=ap.artikel )
                ,' *') as '$_lastcolumn' $hookcolumns

              FROM auftrag_position ap 
              INNER JOIN artikel a ON a.id = ap.artikel 
              LEFT JOIN (
                SELECT apa.id, sum(apb.menge) as menge 
                FROM auftrag_position apa
                INNER JOIN auftrag_position apb ON apa.artikel = apb.artikel AND apa.auftrag = '$id' 
                AND apb.auftrag = '$id'
                AND apb.sort <= apa.sort 
                WHERE apa.id IN (".$positionIdsImplode.") AND apb.id IN (".$positionIdsImplode.")
                GROUP BY apa.id
              ) apc ON ap.id = apc.id
              
              LEFT JOIN (
                SELECT ifnull(sum(lpi1.menge),0) as menge, lpi1.artikel 
                FROM lager_platz_inhalt lpi1 
                INNER JOIN lager_platz lp1 ON lpi1.lager_platz = lp1.id AND lp1.autolagersperre != 1 AND lp1.sperrlager != 1 ".($standardlager?" AND lp1.lager != '$standardlager' ":"")."
                WHERE lpi1.artikel IN (".$articleIdsimplode.") 
                GROUP BY lpi1.artikel
              ) as lpin1 ON ap.artikel = lpin1.artikel
              ".($standardlager?"
              
              LEFT JOIN (
                SELECT ifnull(sum(lpi2.menge),0) as menge, lpi2.artikel 
                FROM lager_platz_inhalt lpi2 
                INNER JOIN lager_platz lp2 ON lpi2.lager_platz = lp2.id AND lp2.autolagersperre != 1 
                AND lp2.sperrlager != 1 AND lp2.lager = '$standardlager'  
                WHERE lpi2.artikel IN (".$articleIdsimplode.")
                GROUP BY lpi2.artikel
              ) as lpin2 ON ap.artikel = lpin2.artikel
              
              ":($projektlager?"
              
              LEFT JOIN (
                SELECT ifnull(sum(lpi2.menge),0) as menge, lpi2.artikel 
                FROM lager_platz_inhalt lpi2 
                INNER JOIN lager_platz lp2 ON lpi2.lager_platz = lp2.id AND lp2.autolagersperre != 1 AND lp2.sperrlager != 1 
                INNER JOIN lager l2 ON lp2.lager = l2.id  AND l2.projekt = '$projektlager' 
                WHERE lpi2.artikel IN (".$articleIdsimplode.")
                GROUP BY lpi2.artikel
              ) as lpin2 ON ap.artikel = lpin2.artikel
              
              ":""))."
              
              LEFT JOIN bestellung_position bp ON bp.id IN (".$bpIdsImplode.") AND ap.id = bp.auftrag_position_id AND bp.artikel IN (".$articleIdsimplode.")
              LEFT JOIN bestellung b ON b.id IN (".$bIdsImplode.") AND b.id = bp.bestellung 
              $hookjoins
              WHERE ap.auftrag='$id' AND ap.id IN (".$positionIdsImplode.")
              ORDER by ap.sort";
      //$time = microtime(true);
      $table->Query($sql);
      //$time = microtime(true) - $time;
      $gewichtanzeigen = false;
      $bestellunganzeigen = false;
      $reserviertfuerkundeanzeigen = false;
      if($table->datasets) {
        foreach($table->datasets as $k => $row) {
          if(str_replace(',','.',$row['gewicht']) > 0){
            $gewichtanzeigen = true;
          }
          if($row['Bestellung'] != '-')
          {
            $bestellunganzeigen = true;
          }
          if(isset($row['Res']) && $row['Res'] != ''){
            $reserviertfuerkundeanzeigen = true;
          }
          if($row[$_lastcolumn] != ''){
            $reserviertfuerkundeanzeigen = true;
          }
        }
      }
      if(!$bestellunganzeigen)
      {
        foreach($table->datasets as $k => $row)
        {
          unset($table->datasets[$k]['Bestellung']);
        }
        $table->headings[2] = $table->headings[3];
        if(isset($table->headings[4]))$table->headings[3] = $table->headings[4];
        if(isset($table->headings[5]))$table->headings[4] = $table->headings[5];
        if(isset($table->headings[6]))$table->headings[5] = $table->headings[6];
        if(isset($table->headings[7]))$table->headings[6] = $table->headings[7];
        if(isset($table->headings[8]))$table->headings[7] = $table->headings[8];
        if(isset($table->headings[8]))
        {
          unset($table->headings[8]);
        }elseif(isset($table->headings[7]))
        {
          unset($table->headings[7]);
        }elseif(isset($table->headings[6]))
        {
          unset($table->headings[6]);
        }elseif(isset($table->headings[5])){
          unset($table->headings[5]);
        }else{
          unset($table->headings[4]);
        }
      }

      if(!$gewichtanzeigen)
      {
        foreach($table->datasets as $k => $row)
        {
          unset($table->datasets[$k]['gewicht']);
        }
        
        if(!$bestellunganzeigen)
        {
          $table->headings[2] = $table->headings[3];
        }
        if(isset($table->headings[4]))$table->headings[3] = $table->headings[4];
        if(isset($table->headings[5]))$table->headings[4] = $table->headings[5];
        if(isset($table->headings[6]))$table->headings[5] = $table->headings[6];
        if(isset($table->headings[7]))$table->headings[6] = $table->headings[7];
        if(isset($table->headings[8]))$table->headings[7] = $table->headings[8];
        if(isset($table->headings[8]))
        {
          unset($table->headings[8]);
        }elseif(isset($table->headings[7]))
        {
          unset($table->headings[7]);
        }elseif(isset($table->headings[6]))
        {
          unset($table->headings[6]);
        }elseif(isset($table->headings[5]))
        {
          unset($table->headings[5]);
        }elseif(isset($table->headings[4]))
        {
          unset($table->headings[4]);
        }else{
          unset($table->headings[3]);
        }
      }

      if(!$reserviertfuerkundeanzeigen)
      {
        foreach($table->datasets as $k => $row)
        {
          unset($table->datasets[$k][$_lastcolumn]);
        }
        if($lastcolumn == $_lastcolumn)
        {
          unset($table->headings[count($table->headings)-1]);
        }else {
          $cheading = !empty($table->headings)?count($table->headings):0;
          for($hc = 0; $hc < $cheading; $hc++)
          {
            if($table->headings[$hc] == $_lastcolumn)
            {
              break;
            }
          }
          for($hci = $hc; $hci < $cheading ;$hci++)
          {
            if(isset($table->headings[$hci])) {
              if(isset($table->headings[$hci + 1])) {
                $table->headings[$hci] = $table->headings[$hci + 1];
              }else{
                unset($table->headings[$hci]);
              }
            }
          }
        }

        if($lastcolumn == $_lastcolumn)
        {
          $lastcolumn = $table->headings[count($table->headings)-1];
          if($lastcolumn == 'Aktion'){
            $lastcolumn = $table->headings[count($table->headings)-2];
          }
        }
      }

      if($table->datasets)
      {
        foreach($table->datasets as $k => $v)
        {
          foreach($v as $k2 =>  $v2)
          {
            if(preg_match_all('/^(.*)lagermehr\((.*)\)(.*)$/', $v2, $matches,PREG_OFFSET_CAPTURE))
            {
              $table->datasets[$k][$k2] = $matches[1][0][0].'&nbsp;'.$this->app->YUI->ContentTooltip('return','index.php?module=auftrag&action=minidetail&cmd=lager&id='.$matches[2][0][0],'url').$matches[3][0][0];
            }
          }
        }
        foreach($table->headings as $k => $v)
        {
          if($v === 'Lager'){
            $table->align[$k] = 'right';
          }elseif($v === 'Menge'){
            $table->align[$k] = 'right';
          }elseif($v === 'Projektlager'){
            $table->align[$k] = 'right';
          }elseif($v == $standardlagertext){
            $table->align[$k] = 'right';
          }
        }
      }
      $artikel = $table->DisplayNew("return",$lastcolumn,"noAction","false",0,0,false);

    }
    else {
      //$table->Query("SELECT ap.bezeichnung as artikel, ap.nummer as Nummer, if(a.lagerartikel,ap.menge,'-') as Menge
      $hookjoins = '';
      $hookcolumns = '';
      $lastcolumn = 'Menge';
      $this->app->erp->RunHook('auftrag_minidetail_hook1',4, $id, $hookcolumns, $hookjoins, $lastcolumn);
      
      $sql =
        "SELECT if(ap.explodiert_parent > 1,
        CONCAT('***',if(CHAR_LENGTH(ap.beschreibung) > 0,CONCAT(ap.bezeichnung,' *'),ap.bezeichnung)),if(CHAR_LENGTH(ap.beschreibung) > 0,CONCAT(ap.bezeichnung,' *'),ap.bezeichnung)) as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', ap.nummer,'</a>') as Nummer, 
         if(isnull(b.id),'-',concat('<a href=\"index.php?module=bestellung&action=edit&id=',b.id,'\" target=\"_blank\">',if(b.belegnr <> '', b.belegnr,'ENTWURF'),'</a>')
          ) as Bestellung,a.gewicht as gewicht,
        TRIM(ap.menge)+0 as Menge 
        $hookcolumns
        FROM auftrag_position AS ap 
        INNER JOIN artikel AS a ON a.id=ap.artikel 
        LEFT JOIN bestellung_position AS bp ON ap.id = bp.auftrag_position_id AND bp.id IN (".$bpIdsImplode.") 
        LEFT JOIN bestellung AS b ON b.id = bp.bestellung AND b.id IN (".$bIdsImplode.")
          $hookjoins
        WHERE ap.id IN (".$positionIdsImplode.") AND ap.auftrag='$id'  
        ORDER by ap.sort ";

      $table->Query($sql);

      $gewichtanzeigen = false;
      $bestellunganzeigen = false;
      if($table->datasets){
        foreach($table->datasets as $k => $row) {
          if($row['gewicht'] > 0){
            $gewichtanzeigen = true;
          }
          if($row['Bestellung'] != '-'){
            $bestellunganzeigen = true;
          }
        }
      }
      
      if(!$bestellunganzeigen) {
        foreach($table->datasets as $k => $row)  {
          unset($table->datasets[$k]['Bestellung']);
        }
        $table->headings[2] = $table->headings[3];
        if(isset($table->headings[4])){
          $table->headings[3] = $table->headings[4];
        }
        if(isset($table->headings[5])){
          $table->headings[4] = $table->headings[5];
        }
        if(isset($table->headings[6])){
          $table->headings[5] = $table->headings[6];
        }
        if(isset($table->headings[7])){
          $table->headings[6] = $table->headings[7];
        }
        if(isset($table->headings[7]))
        {
          unset($table->headings[7]);
        }elseif(isset($table->headings[6]))
        {
          unset($table->headings[6]);
        }elseif(isset($table->headings[5])){
          unset($table->headings[5]);
        }else{
          unset($table->headings[4]);
        }
      }

      if(!$gewichtanzeigen)
      {
        foreach($table->datasets as $k => $row)
        {
          unset($table->datasets[$k]['gewicht']);
        }
        if(!$bestellunganzeigen)
        {
          $table->headings[2] = $table->headings[3];
        }
        if(isset($table->headings[4]))$table->headings[3] = $table->headings[4];
        if(isset($table->headings[5]))$table->headings[4] = $table->headings[5];
        if(isset($table->headings[6]))$table->headings[5] = $table->headings[6];
        if(isset($table->headings[7]))$table->headings[6] = $table->headings[7];
        if(isset($table->headings[7]))
        {
          unset($table->headings[7]);
        }elseif(isset($table->headings[6]))
        {
          unset($table->headings[6]);
        }elseif(isset($table->headings[5])){
          unset($table->headings[5]);
        }elseif(isset($table->headings[4])){
          unset($table->headings[4]);
        }else{
          unset($table->headings[3]);
        }
      }
      
      $artikel = $table->DisplayNew("return",$lastcolumn,"noAction");
    }
    $this->app->Tpl->Set('ARTIKEL','<div id="artikeltabellelive'.$id.'">'.$artikel.'</div>');
    $nachartikeltarget = 'MINIDETAILNACHARTIKEL';
    $this->app->erp->RunHook("AuftragMiniDetailNachArtikel", 2, $id, $nachartikeltarget);
    //START ZUSTANDSAUTOMAT FARBEN
    if($auftragArr[0]['status']==='freigegeben'){
      $this->app->Tpl->Set(
        'VERSANDTEXT','<div class="warning">Versand: Noch nicht versendet!</div>'
      );
    }
    else if ($auftragArr[0]['status']==='abgeschlossen')
    {
      $this->app->Tpl->Set(
        'VERSANDTEXT','<div class="info">Versand: Bereits versendet!</div>'
      );
    }  else {
      $this->app->Tpl->Set(
        'VERSANDTEXT','<div class="info">Versand: -</div>'
      );
    }


    $vorkasse_ok = $this->app->DB->Select("SELECT vorkasse_ok FROM auftrag WHERE id='$id' LIMIT 1");
    $zahlungsweise = $auftragArr[0]['zahlungsweise'];
    if($vorkasse_ok==1){
      if($zahlungsweise==='vorkasse' || $zahlungsweise==='paypal' || $zahlungsweise==='kreditkarte') {
        $this->app->Tpl->Add(
          'ZAHLUNGEN',"<div class=\"info\">Der Auftrag wurde bezahlt.</div>");}
      else if ($zahlungsweise==='rechnung') {
        $this->app->Tpl->Add(
          'ZAHLUNGEN',"<div class=\"info\">Der Auftrag wird per Rechnung bezahlt.</div>");
      }
      else if ($zahlungsweise==='amazon') {
        $this->app->Tpl->Add(
          'ZAHLUNGEN',"<div class=\"info\">Der Auftrag wird per Amazon bezahlt.</div>");
      }
      else if ($zahlungsweise==='lastschrift'||$zahlungsweise==='einzugsermaechtigung') {
        $this->app->Tpl->Add(
          'ZAHLUNGEN',"<div class=\"info\">Der Auftrag wird per Lastschrift bezahlt.</div>");
      }
      else if ($zahlungsweise==='bar' || $zahlungsweise==='nachnahme' ) {
        $this->app->Tpl->Add(
          'ZAHLUNGEN',"<div class=\"success\">Der Auftrag wird bei &Uuml;bergabe bezahlt.</div>");
      }
    }
    else if($vorkasse_ok==2){
			$this->app->Tpl->Add('ZAHLUNGEN','<div class="warning">Es liegt eine Teilzahlung vor!</div>');
		}

    elseif(in_array($zahlungsweise, ['vorkasse','paypal','kreditkarte','bar'])) {
      $this->app->Tpl->Add(
        'ZAHLUNGEN',
        '<div class="error">Vorkasse noch nicht abgeschlossen!</div>'
      );
    }

    // schaue ob es eine GS zu diesem Auftrag gibt
    // schaue ob es eine GS zu diesem Auftrag gibt
    //$gutschriftid = $this->app->DB->Select("SELECT id FROM gutschrift WHERE rechnungid='$rechnungid' LIMIT 1");

    if(!empty($rechnungids)) {
      foreach($rechnungids as $rechnungid) {
        $gutschriftid = $rechnungid <= 0?0:$this->app->DB->Select(
          sprintf(
            'SELECT id FROM gutschrift WHERE rechnungid=%d AND rechnungid > 0 LIMIT 1',
            $rechnungid
          )
        );

        if($gutschriftid > 0) {
          $tmp = $this->app->DB->Select(
            "SELECT 
              CONCAT('<a href=\"index.php?module=gutschrift&action=edit&id=',r.id,'\" target=\"_blank\">',if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),'&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Gutschrift PDF\" border=\"0\"></a>&nbsp;
                <a href=\"index.php?module=gutschrift&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>') as rechnung
            FROM gutschrift r 
            WHERE r.id='".$gutschriftid."' LIMIT 1"
          );
          $this->app->Tpl->Add('GUTSCHRIFT',$tmp);
        }
      }
    }
    if($auftragArr[0]['rma']==1){
      $this->app->YUI->ParserVarIf('RMA', 1);
    }
    else{
      $this->app->YUI->ParserVarIf('RMA', 0);
    }

    $this->app->Tpl->Set('RMAFARBE','red');
    $this->app->Tpl->Set('RMATEXT','RMA zu diesem Auftrag vorhanden!');


    if($auftragArr[0]['belegnr']=='0' || $auftragArr[0]['belegnr']=='') {
      $auftragArr[0]['belegnr'] = 'ENTWURF';
    }
    $this->app->Tpl->Set('BELEGNR',$auftragArr[0]['belegnr']);
    $this->app->Tpl->Set('AUFTRAGID',$auftragArr[0]['id']);

    $this->app->Tpl->Set(
      'RECHNUNGLIEFERADRESSE',
      $this->AuftragRechnungsLieferadresse($auftragArr[0]['id'])
    );

    $this->app->Tpl->Set('RMA',"Es ist kein RMA-Prozess zu diesem Auftrag vorhanden.");

    $tmp = new EasyTable($this->app);
    $tmp->Query(
      sprintf(
        "SELECT DATE_FORMAT(ap.zeit, '%%d.%%m.%%Y %%H:%%i:%%s') AS `zeit`, ap.bearbeiter, ap.grund
        FROM `auftrag_protokoll` AS `ap`
        WHERE ap.auftrag = %d
        ORDER by ap.zeit DESC, ap.id DESC",
        $id
      )
    );
    $tmp->DisplayNew('PROTOKOLL',"Protokoll","noAction");

    $produktionsId = $this->app->DB->Select("SELECT id FROM produktion WHERE auftragid = '$id' LIMIT 1");
    if($produktionsId > 0){
      $this->app->Tpl->Set(
        'VORPRODUKTIONPROTOKOLL',
        '<div style="background-color:white">
          <h2 class="greyh2">{|Produktion Protokoll|}</h2>
        <div style="padding:10px;">'
      );
      $produktionProtokoll = new EasyTable($this->app);
      $produktionProtokoll->Query(
        sprintf(
          "SELECT DATE_FORMAT(pp.zeit,'%%d.%%m.%%Y %%H:%%i:%%s') AS `zeit`, pp.bearbeiter, pp.grund 
          FROM `produktion_protokoll` AS `pp` 
          WHERE pp.produktion = %d
          ORDER BY pp.zeit DESC, pp.id DESC",
          $produktionsId
        )
      );
      $produktionProtokoll->DisplayNew('PRODUKTIONPROTOKOLL', 'Produktion Protokoll', 'noAction');
      $this->app->Tpl->Set('NACHPRODUKTIONPROTOKOLL', '</div></div>');
    }
    if(class_exists('AuftragPDFCustom')) {
      $Brief = new AuftragPDFCustom($this->app,$auftragArr[0]['projekt']);
    }
    else{
      $Brief = new AuftragPDF($this->app,$auftragArr[0]['projekt']);
    }
    $Dokumentenliste = $Brief->getArchivedFiles($id, 'auftrag');
    if($Dokumentenliste) {
      $tmp3 = new EasyTable($this->app);
      $tmp3->headings = array('Datum','Belegnr','Bearbeiter','Men&uuml;');
      foreach($Dokumentenliste as $k => $v) {
        if(!$v['erstesoriginal']) {
          $tmpr['datum'] = date('d.m.Y H:i:s',strtotime($v['zeitstempel']));
          $tmpr['belegnr'] = str_replace('.pdf','',$v['file']);
          $tmpr['belegnr'] = substr($tmpr['belegnr'],strrpos($tmpr['belegnr'],'_')+1);
          if(isset($v['belegnummer']) && $v['belegnummer']) {
            $tmpr['belegnr'] = $v['belegnummer'];
          }
          $tmpr['bearbeiter'] = $v['bearbeiter'];
          $tmpr['menu'] = '<a href="index.php?module=auftrag&action=pdffromarchive&id='
            .$v['id']
            .'" target="_blank"><img src="themes/'
            .$this->app->Conf->WFconf['defaulttheme'].'/images/pdf.svg" /></a>';
          $tmp3->datasets[] = $tmpr;
        }
      }
      
      $tmp3->DisplayNew('PDFARCHIV','Men&uuml;',"noAction");
    }

    $nettogewicht = $this->app->erp->AuftragNettoGewicht($id);
    if($nettogewicht!='') {
      $nettogewicht = number_format($nettogewicht, 2, ',','.');
      $gewichtbezeichnung = $this->app->erp->Firmendaten('gewichtbezeichnung');
      if($gewichtbezeichnung == '') {
        $gewichtbezeichnung = 'Kg';
      }

      $this->app->Tpl->Set('GEWICHT', $nettogewicht . ' '.$gewichtbezeichnung);
    }
    if($this->app->erp->Firmendaten('internebemerkungminidetails')) {
      //$schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM auftrag WHERE id = '$id' LIMIT 1");
      $schreibschutz=0;
      $this->app->Tpl->Set(
        'INTERNEBEMERKUNGEDIT',
        '<div style="background-color:white">
      <h2 class="greyh2">Interne Bemerkung</h2>
      <div style="padding:10px;overflow:auto; width:500px;">
        <textarea id="internebemerkung_'.$id.'"'
        .($schreibschutz?' readonly ':' onchange="saveinternebemerkung_'.$id.'('.$id.');" ').'>'
        .str_replace(
          '"','&quot;',
          $this->app->DB->Select("SELECT internebemerkung FROM auftrag WHERE id = '$id'")
        )
        .'</textarea>
      </div>
      </div>');
      $this->app->Tpl->Add('INTERNEBEMERKUNGEDIT','<script>');
      if(!$schreibschutz) {
        $this->app->Tpl->Add(
          'INTERNEBEMERKUNGEDIT',
          'function saveinternebemerkung_'.$id.'(auftragid)
        {
          var wert = $("#internebemerkung_'.$id.'").val();
            $.ajax({
              url: "index.php?module=auftrag&action=minidetail&cmd=saveinternebemerkung&id='.$id.'",
              type: "POST",
              dataType: "json",
              data: { internebemerkung: wert},
              success: function(data) {
                
              }
            });
        }
       '
        );
      }
      if($this->app->erp->Firmendaten('briefhtml')=='1') {
        $this->app->YUI->CkEditor('internebemerkung_'.$id,"belege",null, 'INTERNEBEMERKUNGEDIT');
        $this->app->Tpl->Add(
          'INTERNEBEMERKUNGEDIT',
          '</script><input type="button" onclick="saveinternebemerkung_'.$id.'('.$id.');" value="speichern" />'
        );
      }
      else{
        $this->app->Tpl->Add('INTERNEBEMERKUNGEDIT','</script>');
      }
      
    }
    if($parsetarget=='') {
      $this->app->Tpl->Output('auftrag_minidetail.tpl');
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Parse($parsetarget,'auftrag_minidetail.tpl');
  }

  function AuftragRechnungsLieferadresse($auftragid)
  {
    $data = $this->app->DB->SelectArr("SELECT * FROM auftrag WHERE id='$auftragid' LIMIT 1");

    foreach($data[0] as $key=>$value)
    {
      if($data[0][$key]!='' && $key!=='abweichendelieferadresse' && $key!=='land' && $key!=='plz' && $key!=='lieferland' && $key!=='lieferplz') {
        $data[0][$key] = $data[0][$key].'<br>';
      }
    }


    $rechnungsadresse = $data[0]['name']."".$data[0]['ansprechpartner']."".$data[0]['abteilung']."".$data[0]['unterabteilung'].
      "".$data[0]['strasse']."".$data[0]['adresszusatz']."".$data[0]['land']."-".$data[0]['plz']." ".$data[0]['ort'];

    // wenn abweichende rechnungsadresse bei kunden aktiv ist dann diese verwenden

    $abweichende = $this->app->DB->Select("SELECT abweichende_rechnungsadresse FROM adresse WHERE id='".$data[0]['adresse']."' LIMIT 1");
    if($abweichende=="1") 
    {
      $adresse_data = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='".$data[0]['adresse']."' LIMIT 1");

      foreach($adresse_data[0] as $key=>$value)
      {
        if($adresse_data[0][$key]!="" && $key!="abweichendelieferadresse" && $key!="rechnung_land" && $key!="rechnung_plz") 
        {
          $adresse_data[0][$key] = $adresse_data[0][$key]."<br>";
        }
      }

      $rechnungsadresse = $adresse_data[0]['rechnung_name']."".$adresse_data[0]['rechnung_ansprechpartner']."".$adresse_data[0]['rechnung_abteilung']."".$adresse_data[0]['rechnung_unterabteilung'].
      "".$adresse_data[0]['rechnung_strasse']."".$adresse_data[0]['rechnung_adresszusatz']."".$adresse_data[0]['rechnung_land']."-".$adresse_data[0]['rechnung_plz']." ".$adresse_data[0]['rechnung_ort'];
    }

    if($data[0]['abweichendelieferadresse']!=0){

      $lieferadresse = $data[0]['liefername']."".$data[0]['lieferansprechpartner']."".$data[0]['lieferabteilung']."".$data[0]['lieferunterabteilung'].
        "".$data[0]['lieferstrasse']."".$data[0]['lieferadresszusatz']."".$data[0]['lieferland']."-".$data[0]['lieferplz']." ".$data[0]['lieferort'];


    } else {
      $lieferadresse = "entspricht Rechnungsadresse";
    }

    return "<table width=\"100%\">
      <tr valign=\"top\"><td width=\"50%\"><b>Rechnungsadresse ".($abweichende=="1"?' (abweichend)':'').":</b><br><br>$rechnungsadresse<br></td></tr>
      <tr><td><b>Lieferadresse:</b><br><br>$lieferadresse</td></tr></table>";
  }



  function AuftragZahlungsmail()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->AuftragZahlungsmail($id,1);
    $this->app->Location->execute('index.php?module=auftrag&action=edit&id='.$id);
  }

  function AuftragSuche()
  {
    $this->app->erp->Headlines('Auftr&auml;ge');
    $this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=auftrag&action=create","Neuen Auftrag anlegen");
    $this->app->erp->MenuEintrag("index.php?module=auftrag&action=search","Auftrag Suchen");
    $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");

    $this->app->Tpl->Set('TABTEXT',"Auftr&auml;ge");

    $name = trim($this->app->Secure->GetPOST("name"));
    $suchwort = trim($this->app->Secure->GetPOST("suchwort"));
    $email = trim($this->app->Secure->GetPOST("email"));
    $plz = trim($this->app->Secure->GetPOST("plz"));
    $auftrag = trim($this->app->Secure->GetPOST("auftrag"));
    $proforma = trim($this->app->Secure->GetPOST("proforma"));
    $kundennummer = trim($this->app->Secure->GetPOST("kundennummer"));
    $betrag= trim($this->app->Secure->GetPOST("betrag"));

    $betrag = str_replace(',','.',$betrag);

    if($name!="" || $plz!="" || $proforma!="" || $kundennummer!="" || $auftrag!="" || $email!="" || $betrag!="" || $suchwort!="")
    {
      $table = new EasyTable($this->app);
      $this->app->Tpl->Add('ERGEBNISSE',"<h2>Trefferliste:</h2><br>");
      if($suchwort!="")
      {
        $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.name, a.belegnr as auftrag, adr.kundennummer, a.internet, a.plz, a.ort, a.strasse, a.zahlungsweise, a.status, a.id FROM auftrag a 
            LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.name LIKE '%$suchwort%' OR a.email LIKE '%$suchwort%' OR a.plz LIKE '$suchwort%' OR a.internet LIKE '%$suchwort%' OR (adr.kundennummer='$suchwort' AND adr.kundennummer!=0)
              OR (a.gesamtsumme='$suchwort' AND a.gesamtsumme!=0) OR (a.belegnr='$suchwort' AND a.belegnr!='' ))");
      } else {
        if($name!="")
          $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.name, a.belegnr as auftrag, adr.kundennummer, a.internet, a.plz, a.ort, a.strasse,  a.zahlungsweise, a.status, a.id FROM auftrag a 
              LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.name LIKE '%$name%')");
        else if($email!="")
          $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.name, a.belegnr as auftrag, adr.kundennummer, a.internet, a.plz, a.ort, a.strasse, a.zahlungsweise, a.status, a.id FROM auftrag a 
              LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.email LIKE '%$email%')");
        else if($plz!="")
          $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.name, a.belegnr as auftrag, adr.kundennummer, a.internet, a.plz, a.ort, a.strasse, a.zahlungsweise, a.status, a.id FROM auftrag a 
              LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.plz LIKE '$plz%')");
        else if($proforma!="")
          $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.name, a.belegnr as auftrag, adr.kundennummer, a.internet, a.plz, a.ort, a.strasse, a.zahlungsweise, a.status, a.id FROM auftrag a 
              LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.internet LIKE '%$proforma%')");
        else if($kundennummer!="")
          $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.name, a.belegnr as auftrag, adr.kundennummer, a.internet, a.plz, a.ort, a.strasse,  a.zahlungsweise, a.status, a.id FROM auftrag a 
              LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (adr.kundennummer='$kundennummer')");
        else if($betrag!="")
          $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.name, a.belegnr as auftrag, adr.kundennummer, a.internet, a.plz, a.ort, a.strasse,  a.zahlungsweise, a.status, a.id FROM auftrag a 
              LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.gesamtsumme='$betrag')");
        else if($auftrag!="")
          $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.name, a.belegnr as auftrag, adr.kundennummer, a.internet, a.plz, a.ort, a.strasse, a.zahlungsweise, a.status, a.id FROM auftrag a 
              LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.belegnr='$auftrag')");

      }
      $table->DisplayNew('ERGEBNISSE',"<a href=\"index.php?module=auftrag&action=edit&id=%value%\">Lesen</a>");
    } else {
      $this->app->Tpl->Add('ERGEBNISSE',"<div class=\"info\">Auftragssuche (bitte entsprechende Suchparameter eingeben)</div>");
    }

    $this->app->Tpl->Parse('INHALT',"auftragssuche.tpl");

    $this->app->Tpl->Set('AKTIV_TAB1',"selected");
    $this->app->Tpl->Parse('TAB1',"rahmen77.tpl");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }



  public function AuftragRechnung()
  {
    $id = $this->app->Secure->GetGET('id');

    $anzahl_artikel = $this->app->DB->Select("SELECT id FROM auftrag_position WHERE auftrag=$id LIMIT 1");
    if($anzahl_artikel <= 0)
    {
      $belegnr = $this->app->DB->Select("SELECT if(belegnr = '','ENTWURF',belegnr) FROM auftrag WHERE id = '$id' LIMIT 1");
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Auftrag $belegnr kann nicht weitergefuehrt werden, da keine Artikel gebucht sind!</div>  ");
      $this->app->Location->execute('index.php?module=auftrag&action=edit&id='.$id.'&msg='.$msg);
    }
    $newid = $this->app->erp->WeiterfuehrenAuftragZuRechnung($id);

    $this->app->erp->AuftragProtokoll($id,'Auftrag manuell als Rechnung weitergeführt');
    $this->app->Location->execute('index.php?module=rechnung&action=edit&id='.$newid);
  }

  /**
   * @return JsonResponse
   */
  public function HandleCreateProforma()
  {
    $justStorage = $this->app->Secure->GetPOST('just_strorage');
    $this->app->User->SetParameter('proforma_just_storage', (int)$justStorage);
    $id = $this->app->Secure->GetPOST('elementid');
    $status = $this->app->DB->Select("SELECT status FROM auftrag WHERE id=$id");
    if($status!=='angelegt') {
      /** @var Proformarechnung $obj */
      $obj = $this->app->loadModule('proformarechnung');
      if(!empty($obj) && method_exists($obj, 'createFromOrder')) {
        $newid = $obj->createFromOrder($id, $justStorage);
        $this->app->erp->AuftragProtokoll($id,'Auftrag als Proformarechnung weitergeführt');
        return new JsonResponse(
          ['success' => true, 'url' => "index.php?module=proformarechnung&action=edit&id=$newid"]
        );
      }
      return new JsonResponse(
        [
          'success' => false,
          'error' => 'Das Modul Proformarechnung konnte nicht geladen werden!'
        ],
        JsonResponse::HTTP_BAD_REQUEST
      );
    }

    return new JsonResponse(
      [
        'success' => false,
        'error' => 'Das Auftrag befindet sich noch im Entwurfsmodus und kann daher nicht als Proformarechnung weiterführt werden!'
      ],
      JsonResponse::HTTP_BAD_REQUEST
    );
  }



  public function AuftragLieferschein()
  {
    $id = $this->app->Secure->GetGET('id');
    $posids = $this->app->Secure->GetGET('posids');
    if($posids)
    {
      $positionen = null;
      $zwischenpositionen = null;
      if(strpos($posids, 'z') !== false || strpos($posids, 'b') !== false)
      {
        $sida = explode(',',$posids);
        foreach($sida as $v)
        {
          if($v[0] === 'b')
          {
            $v = substr($v ,1);
            $positionen[] = $v;
          }else{
            $v = substr($v ,1);
            $zwischenpositionen[] = $v;
          }
        }
      }
    }
    $this->app->erp->AuftragProtokoll($id,'Auftrag manuell als Lieferschein weitergeführt');
    $msgNoArticle = $this->app->erp->base64_url_encode('<div class="error">Der Auftrag kann nicht zum Lieferschein weitergef&uuml;hrt werden, da er keine Positionen oder keine Lagerartikel enth&auml;lt</div>');
    if(isset($positionen))
    {
      $newid = $this->app->erp->WeiterfuehrenAuftragZuLieferschein($id, $positionen, $zwischenpositionen);
      $this->app->BuildNavigation = false;
      if($newid){
        $this->app->Tpl->Add('TAB1', '
        <script>
      
        window.parent.window.location.href=\'index.php?module=lieferschein&action=edit&id=' . $newid . '\';
      
        </script>
      ');
      }else{
        $this->app->Tpl->Add('TAB1', '
        <script>
      
        window.parent.window.location.href=\'index.php?module=auftrag&action=edit&id=' . $id.'&msg='.$msgNoArticle . '\';
      
        </script>
      ');
      }
      $this->app->Tpl->Parse('PAGE','tabview.tpl');
      return;
    }
    $newid = $this->app->erp->WeiterfuehrenAuftragZuLieferschein($id);

    if($newid > 0){
      $this->app->Location->execute("Location: index.php?module=lieferschein&action=edit&id=$newid");
    }
    $this->app->Location->execute("Location: index.php?module=auftrag&action=edit&id=$id&msg=".$msgNoArticle);
  }

  public function AuftragLieferscheinRechnung()
  {
    $id = $this->app->Secure->GetGET('id');

    $auftragarr = $this->app->DB->SelectRow("SELECT projekt,art FROM auftrag WHERE id='$id' LIMIT 1");
    if(!empty($auftragarr))
    {
      $projekt = $auftragarr['projekt'];//$this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
      $art = $auftragarr['art'];//$this->app->DB->Select("SELECT art FROM auftrag WHERE id='$id' LIMIT 1");
    }else{
      $projekt = 0;
      $art = '';
    }

    $druckercode = $this->app->erp->Firmendaten('standardversanddrucker');
    $this->app->erp->BriefpapierHintergrundDisable($druckercode);

    if($art!=='rechnung')
    {
      // automatisch drucken bzw. freigeben
      $newid = $this->app->erp->WeiterfuehrenAuftragZuLieferschein($id);

      $belegnr = (string)$this->app->DB->Select("SELECT belegnr FOM lieferschein WHERE id='$newid' LIMIT 1");
      if($belegnr==='' || $belegnr==='0') {
        $belegnr = $this->app->erp->GetNextNummer('lieferschein',$projekt,$newid);
      }

      $this->app->DB->Update("UPDATE lieferschein SET belegnr='$belegnr', status='freigegeben' WHERE id='$newid' LIMIT 1");
      $this->app->erp->LieferscheinProtokoll($newid,'Lieferschein freigegeben');

      $this->app->erp->LieferscheinAuslagern($newid,true, (int)$this->app->DB->Select("SELECT standardlager FROM auftrag WHERE id = '$id' LIMIT 1"),'lieferschein',true);
      $Brief = new LieferscheinPDF($this->app,$projekt);
      $Brief->GetLieferschein($newid);
      $tmpfile = $Brief->displayTMP();
      $Brief->ArchiviereDocument();
      $this->app->printer->Drucken($druckercode,$tmpfile);

      $fileid_lieferschein = $this->app->erp->CreateDatei($Brief->filename,'lieferschein','','',$tmpfile,$this->app->User->GetName());
      $this->app->erp->AddDateiStichwort($fileid_lieferschein,'lieferschein','lieferschein',$newid,$without_log=false);
      unlink($tmpfile);
      $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
      $Brief = new LieferscheinPDF($this->app,$projekt);
      $Brief->GetLieferschein($newid);
      $tmpfile = $Brief->displayTMP();
      $Brief->ArchiviereDocument();
      unlink($tmpfile);
    }

    if($art!=='lieferung')
    {

      //TODO eventuell wenn es Rechnung schon gibt keine weitere erstellen
      $newid = $this->app->erp->WeiterfuehrenAuftragZuRechnung($id);
      $belegnr = (string)$this->app->DB->Select("SELECT belegnr FOM rechnung WHERE id='$newid' LIMIT 1");
      if($belegnr==='' || $belegnr==='0') {
        $belegnr = $this->app->erp->GetNextNummer('rechnung',$projekt,$newid);
      }
      $this->app->DB->Update("UPDATE rechnung SET belegnr='$belegnr', status='freigegeben' WHERE id='$newid' LIMIT 1");
      $this->app->erp->RechnungProtokoll($newid,'Rechnung freigegeben');
      $this->app->erp->ANABREGSNeuberechnen($newid,"rechnung");

      $Brief = new RechnungPDF($this->app,$projekt);
      $Brief->GetRechnung($newid);
      $tmpfile = $Brief->displayTMP();
      $Brief->ArchiviereDocument();
      $this->app->printer->Drucken($druckercode,$tmpfile);

      $fileid_rechnung = $this->app->erp->CreateDatei($Brief->filename,'rechnung','','',$tmpfile,$this->app->User->GetName());
      $this->app->erp->AddDateiStichwort($fileid_rechnung,'rechnung','rechnung',$newid,$without_log=false);
      unlink($tmpfile);
      $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
      $Brief = new RechnungPDF($this->app,$projekt);
      $Brief->GetRechnung($newid);
      $tmpfile = $Brief->displayTMP();
      $Brief->ArchiviereDocument();
      unlink($tmpfile);
    }
    $this->app->Location->execute('index.php?module=auftrag&action=edit&id='.$id);
  }


  public function AuftragZertifikate()
  {
    $id = $this->app->Secure->GetGET('id');
    $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id = '$id' LIMIT 1");
    if($adresse)
    {
      $zertifikate = $this->app->DB->SelectArr("SELECT ds.datei 
      FROM datei_stichwoerter ds 
      INNER JOIN datei_stichwoerter ds2 ON ds.datei = ds2.datei AND ds2.objekt = 'Artikel'
      INNER JOIN auftrag_position ap ON ap.artikel = ds2.parameter AND ap.auftrag = '$id'
      WHERE ds.objekt = 'Adressen' AND ds.parameter = '$adresse'
      GROUP BY ds.datei");
      if(!empty($zertifikate))
      {
        foreach($zertifikate as $zertifikat)
        {
          $this->app->erp->AddDateiStichwort($zertifikat['datei'],'Sonstige','Auftrag',$id);
        }
      }
    }

    $this->app->Location->execute('index.php?module=auftrag&action=dateien&id='.$id);
  }


  public function AuftragCopy()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $newid = $this->app->erp->CopyAuftrag($id);
    }else{
      $newid = $id;
    }
    $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$newid");
  }

  public function AuftragKreditlimit()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->DB->Update("UPDATE auftrag SET kreditlimit_freigabe='1' WHERE id='$id' LIMIT 1");

      $this->app->erp->AuftragProtokoll($id, 'Kreditlimit manuell freigegeben');

      $this->app->erp->AuftragAutoversandBerechnen($id);
      $this->app->erp->AuftragNeuberechnen($id);
    }
    header('Location: index.php?module=auftrag&action=edit&id='.$id);
  }

  function AuftragFreigabe($id='')
  {

    if($id=='') {
      $id = $this->app->Secure->GetGET('id');
      $freigabe= $this->app->Secure->GetGET('freigabe');
      //$this->app->Tpl->Set('TABTEXT',"Freigabe");
      //$module = $this->app->Secure->GetGET("module");
    } else {
      $intern = true;
      $freigabe=$intern;
    }
    $allowedFrm = true;
    $showDefault = true;
    if($id > 0){
      $this->app->erp->CheckVertrieb($id, 'auftrag');
      $this->app->erp->CheckBearbeiter($id, 'auftrag');
      $auftragarr = $this->app->DB->SelectRow("SELECT projekt,belegnr,status,email,gesamtsumme FROM auftrag WHERE id='$id' LIMIT 1");
      $doctype = 'auftrag';
      if(empty($intern)){
        $this->app->erp->RunHook('beleg_freigabe', 4, $doctype, $id, $allowedFrm, $showDefault);
      }
      
    }
    if(!empty($auftragarr)) {
      $projekt = $auftragarr['projekt'];//$this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
      $checkbelegnr = $auftragarr['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
      $status = $auftragarr['status'];//$this->app->DB->Select("SELECT status FROM auftrag WHERE id = '$id' LIMIT 1");
      $email = $auftragarr['email'];
      $summe = $auftragarr['gesamtsumme'];
    }
    else{
      $status = '';
      $projekt = 0;
      $checkbelegnr = '';
      $email = '';
      if($intern)
      {
        return 0;
      }
      $id = 0;
    }

    if($allowedFrm && $freigabe==$id && !empty($id))
    {
      if($checkbelegnr==''){
        $belegnr = $this->app->erp->GetNextNummer('auftrag', $projekt, $id);
      }
      else {
        $belegnr = $checkbelegnr;
      }

      if($this->app->User->GetParameter('auftrag_create_entwurfsdatumuebernehmen')=='1'){
        $this->app->DB->Update("UPDATE auftrag SET belegnr='$belegnr', status='freigegeben'  WHERE id='$id' LIMIT 1");
      }
      else{
        $this->app->DB->Update("UPDATE auftrag SET datum=NOW(), belegnr='$belegnr', status='freigegeben'  WHERE id='$id' LIMIT 1");
      }

      $this->app->erp->AuftragProtokoll($id,'Auftrag freigegeben');
      if($status !== 'freigegeben')
      {
        $_beleg = 'auftrag';
        $_id = $id;
        $this->app->erp->RunHook('BelegFreigabe', 2, $_beleg, $_id);
      }
      // auftrag abschliessen und event senden
      if($intern) {
        return 1;
      }
      $cmd = $this->app->Secure->GetGET('cmd');
      if($cmd==='mail')
      {
	      $this->app->erp->BelegVersand('auftrag',$id,'email');
        $msg = $this->app->erp->base64_url_encode('<div class="info">Der Auftrag wurde freigegeben und die AB an den Kunden per Mail gesendet!</div>  ');
      }
      else{
        $msg = $this->app->erp->base64_url_encode('<div class="info">Der Auftrag wurde freigegeben und kann jetzt versendet werden!</div>  ');
      }
      $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$id&msg=$msg");
    }
    if($id && $showDefault) {
      $name = $this->app->DB->Select("SELECT a.name FROM auftrag b INNER JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      //$email = $this->app->DB->Select("SELECT email FROM auftrag WHERE id='$id' LIMIT 1");
      //$summe = $this->app->DB->Select("SELECT FORMAT(SUM(menge*preis),2) FROM auftrag_position
      //WHERE auftrag='$id'");
      //$summe = $this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$id' LIMIT 1");

      $waehrung = $this->app->DB->Select("SELECT waehrung FROM auftrag_position
          WHERE auftrag='$id' LIMIT 1");

      // check ob entwurdsdataum alt ist
      $extra = $this->app->erp->CheckboxEntwurfsmodus('auftrag',$id);

      if($email!='')
      {
        $this->app->Tpl->Set('TAB1',"<div class=\"info\">Soll der Auftrag an <b>$name</b> im Wert von <b>$summe $waehrung</b> 
            jetzt freigegeben werden?<table cellspacing=5><tr><td width=100></td><td>
            <input type=\"button\" class=\"btnImportantLarge\" value=\"Jetzt freigeben +  Mail ($email)\" onclick=\"window.location.href='index.php?module=auftrag&action=freigabe&id=$id&freigabe=$id&cmd=mail'\">
            &nbsp;oder&nbsp;ohne automatische Mail:&nbsp;
            <input type=\"button\" class=\"btnImportantLarge\" value=\"Jetzt freigeben\" onclick=\"window.location.href='index.php?module=auftrag&action=freigabe&id=$id&freigabe=$id'\">&nbsp;$extra</td></tr></table>
            </div>");
      } else {

        if($this->app->erp->Firmendaten("oneclickrelease")=="1" && $extra=="")
        {
          $this->app->Location->execute("index.php?module=auftrag&action=freigabe&id=$id&freigabe=$id");
        } else {
          $this->app->Tpl->Set('TAB1',"<div class=\"info\">Soll der Auftrag an <b>$name</b> im Wert von <b>$summe $waehrung</b> 
            jetzt freigegeben werden? 
            <input type=\"button\" class=\"btnImportantLarge\" value=\"Jetzt freigeben\" onclick=\"window.location.href='index.php?module=auftrag&action=freigabe&id=$id&freigabe=$id'\">&nbsp;$extra
            </div>");

        }
      }
    }
    $this->AuftragMenu();
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function AuftragAbschicken()
  {
    $this->AuftragMenu();
    $this->app->erp->DokumentAbschicken();
  }



  public function AuftragAnfrage()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $anfrageid = $this->app->DB->Select("SELECT anfrageid FROM auftrag WHERE id='$id' LIMIT 1");
    }else{
      $anfrageid = 0;
    }
    // loesche alle positionen im Auftrag
    if($anfrageid > 0){
      $this->app->erp->WeiterfuehrenAuftragZuAnfrage($id);
    }

    $this->app->Location->execute('index.php?module=anfrage&action=edit&id='.$anfrageid);
  }

  public function AuftragAlsfreigegeben()
  {
    $id = $this->app->Secure->GetGET('id');
    $alsfreigegeben= $this->app->Secure->GetGET('alsfreigegeben');

    $auftragarr = $this->app->DB->SelectRow("SELECT name,belegnr,status FROM auftrag WHERE id='$id' LIMIT 1");
    if(!empty($auftragarr))
    {
      $name = $auftragarr['name'];//$this->app->DB->Select("SELECT name FROM auftrag WHERE id='$id' LIMIT 1");
      $belegnr = $auftragarr['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
      $status = $auftragarr['status'];//$this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
    }else{
      $name = '';
      $status = '';
      $belegnr = '';
    }
    if(empty($auftragarr)){

    }elseif(!empty($auftragarr) && $alsfreigegeben==$id)
    {
      if($status==='angelegt')
      {
        // KUNDE muss RMA starten
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Auftrag \"$name\" ($belegnr) kann nicht als offen markiert werden. Er muss erst freigegeben werden!</div>  ");
      }
      else if($status==="freigegeben")
      {
        // KUNDE muss RMA starten
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Auftrag \"$name\" ($belegnr) ist bereits freigegeben!</div>  ");
      }
      else if($status==="abgeschlossen" || $status==="storniert" || $status==="versendet")
      {
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Auftrag \"$name\" ($belegnr) wurde wieder als freigegeben markiert!</div>  ");
        $this->app->DB->Update("UPDATE auftrag SET status='freigegeben',schreibschutz=0 WHERE id='$id' LIMIT 1"); 
        $this->app->DB->Update("UPDATE auftrag_position SET geliefert_menge=0,geliefert=0 WHERE auftrag='$id'"); 
        $this->app->erp->AuftragProtokoll($id,'Auftrag manuell als freigegeben markiert');
      } 
      else 
      {
        $msg = $this->app->erp->base64_url_encode('<div class="warning">Der Auftrag wurde abgeschlossen!</div>  ');
      }
      $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$id&msg=$msg");
    }
    else {
      $name = $this->app->DB->Select("SELECT a.name FROM auftrag b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      $this->app->Tpl->Set('TAB1',"<div class=\"info\">Soll der Auftrag an <b>$name</b> jetzt wirklich als freigegeben markiert werden?
          <input type=\"button\" value=\"Freigeben\" onclick=\"window.location.href='index.php?module=auftrag&action=alsfreigegeben&id=$id&alsfreigegeben=$id&msg=$msg'\">
          </div>");
    }
    $this->AuftragMenu();
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  /**
   * @param int $orderId
   *
   * @return array
   */
  public function closeOrder($orderId)
  {
    $orderRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT name, belegnr, status FROM auftrag WHERE id=%d LIMIT 1',
        $orderId
      )
    );
    if(empty($orderRow)) {
      return ['error' => 'Auftrag nicht gefunden'];
    }
    $status = $orderRow['status'];
    $number = $orderRow['belegnr'];
    $name = $orderRow['name'];
    if($status === 'angelegt') {
      return ['error' => "Der Auftrag \"$name\" ($number) kann nicht abgeschlossen werden da er noch nicht freigeben wurde! Bitte Auftrag erst freigeben!"];
    }
    if($status==='storniert') {
      return ['error' => "Der Auftrag \"$name\" ($number) kann nicht abgeschlossen werden da er bereits storniert ist!"];
    }
    if($status === 'abgeschlossen') {
      return ['error' => "Der Auftrag \"$name\" ($number) kann nicht abgeschlossen werden da er bereits abgeschlossen ist!"];
    }
    if($status!=='freigegeben' && $status!=='versendet') {
      return ['error' => "Der Auftrag \"$name\" ($number) kann nicht abgeschlossen werden"];
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE auftrag SET status='abgeschlossen',schreibschutz=1 WHERE id=%d LIMIT 1",
        $orderId
      )
    );
    $this->app->erp->AuftragProtokoll($orderId,'Auftrag wurde manuell abgeschlossen');

    return ['info' => 'Der Auftrag wurde abgeschlossen!'];
  }

  public function AuftragAbschluss()
  {
    $id = $this->app->Secure->GetGET('id');
    $abschluss= $this->app->Secure->GetGET('abschluss');
    $auftragarr = empty($id)?null: $this->app->DB->SelectRow(
      sprintf(
        'SELECT name, belegnr, status FROM auftrag WHERE id=%d LIMIT 1',
        $id
      )
    );
    $name = '';
    $belegnr = '';
    $status = '';
    if(!empty($auftragarr)){
      $name = $auftragarr['name'];
      $belegnr = $auftragarr['belegnr'];
      $status = $auftragarr['status'];
    }

    if($abschluss==$id) {
      $ret = $this->closeOrder($id);
      if(!empty($ret['error'])) {
        $msg = $this->app->erp->base64_url_encode('<div class="error">'.$ret['error'].'</div>');
      }
      else{
        $msg = $this->app->erp->base64_url_encode('<div class="info">'.$ret['info'].'</div>');
      }
      $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$id&msg=$msg");
    }

    $name = $this->app->DB->Select("SELECT a.name FROM auftrag b INNER JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    $this->app->Tpl->Set('TAB1',"<div class=\"info\">Soll der Auftrag an <b>$name</b> jetzt abgeschlossen werden?
          <input type=\"button\" value=\"Abschluss\" onclick=\"window.location.href='index.php?module=auftrag&action=abschluss&id=$id&abschluss=$id&msg=$msg'\">
          </div>");

    $this->AuftragMenu();
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  public function AuftragUndelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $auftragarr = $this->app->DB->SelectRow("SELECT name, belegnr, status FROM auftrag WHERE id='$id' LIMIT 1");
    if(!empty($auftragarr)){
      $name = $auftragarr['name'];//$this->app->DB->Select("SELECT name FROM auftrag WHERE id='$id' LIMIT 1");
      $belegnr = $auftragarr['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
      $status = $auftragarr['status'];//$this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
    }else{
      $name = '';
      $belegnr = '';
      $status = '';
    }
    $ok = true;
    if($status !== 'storniert')
    {
      $ok = false;
    }
    if($ok)
    {
      $anzahl_ls = $this->app->DB->Select("SELECT id FROM lieferschein WHERE auftragid='$id' LIMIT 1");
      if($anzahl_ls)
      {
        $ok = false;
      }
    }
    if($ok)
    {
      $anzahl_re = $this->app->DB->Select("SELECT id FROM rechnung WHERE auftragid='$id' LIMIT 1");
      if($anzahl_re){
        $ok = false;
      }
    }

    if($ok)
    {
      $this->app->DB->Update("UPDATE auftrag SET status='freigegeben' WHERE id='$id' LIMIT 1");
      $this->app->erp->AuftragProtokoll($id,'Auftrag Storno rückgängig');
      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Auftrag \"$name\" ($belegnr) wurde wieder freigegeben!</div>  ");
    } else {
      $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Auftrag \"$name\" ($belegnr) kann nicht wieder freigegeben werden da es bereits Rechnungen und/oder Lieferrscheine zu dem Auftrag gibt.</div>  ");
    }
    //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
    $this->app->Location->execute("index.php?module=auftrag&action=list&msg=$msg");
  }

  public function AuftragDelete($id = null)
  {
    if($id === null)
    {
      $intern = false;
      $id = $this->app->Secure->GetGET('id');
      $abschluss = $this->app->Secure->GetGET('abschluss');
      $mail = $this->app->Secure->GetGET('mail');
    }else 
    {
      $intern = true;
      $abschluss = $intern;
      $mail = 0;
    }

    $auftragarr = $this->app->DB->SelectRow("SELECT name, belegnr, status,shop,shopextid FROM auftrag WHERE id='$id' LIMIT 1");
    if(!empty($auftragarr)){
      $name = $auftragarr['name'];//$this->app->DB->Select("SELECT name FROM auftrag WHERE id='$id' LIMIT 1");
      $belegnr = $auftragarr['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
      $status = $auftragarr['status'];//$this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
      $shop = $auftragarr['shop'];
      $shopextid = (string)$auftragarr['shopextid'];
    }else{
      $name = '';
      $belegnr = '';
      $status = '';
      $shop = 0;
      $shopextid = '';
    }

    if($abschluss==$id)
    {
      if($shop && $this->app->DB->Select("SELECT id FROM shopexport WHERE aktiv = 1 AND id = '$shop' AND stornoabgleich AND demomodus = 0 LIMIT 1"))
      {
        if($shopextid !== ''){
          $this->app->remote->RemoteStorniereAuftrag($shop, $id);
        }
      }
      if($belegnr=='0' || $belegnr=='')
      {
        $this->app->erp->DeleteAuftrag($id);
        if($belegnr!=''){
          $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Auftrag \"$name\" ($belegnr) wurde gel&ouml;scht!</div>  ");
        }
        else{
          $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Auftrag \"$name\" wurde gel&ouml;scht!</div>  ");
        }
        //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
        if($intern){
          return;
        }
        $this->app->Location->execute("index.php?module=auftrag&action=list&msg=$msg");
      }

      if($status==='storniert')
      {
        if($intern){
          return;
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Auftrag \"$name\" ($belegnr) kann nicht storniert werden da er bereits storniert ist!</div>  ");
        $this->app->Location->execute("index.php?module=auftrag&action=list&msg=$msg");
      }


      $this->app->DB->Update("UPDATE auftrag SET status='storniert' WHERE id='$id' LIMIT 1");
      $this->app->erp->AuftragProtokoll($id,'Auftrag storniert');

      // stornierungen loeschen
      $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE objekt='auftrag' AND parameter='$id'");

      // ausfuellen automatisch stornofelder
      //stornobetrag // summe des zahlungseingangs!!!!
      //stornogutschrift
      //stornowareerhalten
      //stornorueckzahlung
      // zureuckzahlen per

      // email senden?
      if($mail==1){
        $this->app->erp->Stornomail($id);
      }

      $recheck = $this->app->DB->Select("SELECT id FROM rechnung WHERE auftrag='$belegnr' LIMIT 1");

      if($recheck <= 0)
      {
        // Fall 1 keine RE und LS
        // -> stornieren und Geld zurueckueberweisen (Paypal, Kredit oder Bank)
        // geld wird ueber ipayment oder paypal zurueckgebucht!!!
        // negatives auftragssguthaben loescht auftragsguthaben
        $this->app->Tpl->Add('MESSAGE','<div class="warning">Achtung dem Kunden sofort das Geld &uuml;berweisen!</div>');

      }
      $msg = $this->app->erp->base64_url_encode('<div class="warning">Der Auftrag wurde storniert!</div>  ');

      $this->app->erp->RunHook('auftrag_delete', 1, $id);
      if($intern){
        return;
      }
      $this->app->Location->execute('index.php?module=auftrag&action=list&msg='.$msg);
    }
    if($intern){
      return;
    }
    $name = $this->app->DB->Select("SELECT a.name FROM auftrag b INNER JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    $auftragarr = $this->app->DB->SelectRow("SELECT email,projekt FROM auftrag WHERE id='$id' LIMIT 1");
    if(!empty($auftragarr)){
      $email = $auftragarr['email'];//$this->app->DB->Select("SELECT email FROM auftrag WHERE id='$id' LIMIT 1");
      $projekt = $auftragarr['projekt'];//$this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
    }else{
      $email = '';
      $projekt = 0;
    }
    if($projekt > 0){
      $stornomail = $this->app->DB->Select("SELECT stornomail FROM projekt WHERE id='$projekt' LIMIT 1");
    }else{
      $stornomail = 0;
    }

    if($email!='') {
      $name =$name.' '.$email;
    }

    if($email!='' && $stornomail=='1')
    {
      $this->app->Tpl->Set('TAB1',"<div class=\"info\">Soll der Auftrag an <b>$name</b> jetzt storniert werden?
        <input type=\"button\" value=\"Stornierung MIT E-Mail an den Kunden\" onclick=\"window.location.href='index.php?module=auftrag&action=delete&id=$id&abschluss=$id&msg=$msg&mail=1'\">&nbsp;
        <input type=\"button\" value=\"Stornierung OHNE E-Mail an den Kunden\" onclick=\"window.location.href='index.php?module=auftrag&action=delete&id=$id&abschluss=$id&msg=$msg&mail=0'\">&nbsp;
        </div>");
    } else {
      $this->app->Tpl->Set('TAB1',"<div class=\"info\">Soll der Auftrag an <b>$name</b> jetzt storniert werden?
        <input type=\"button\" value=\"Stornierung OHNE E-Mail an den Kunden\" onclick=\"window.location.href='index.php?module=auftrag&action=delete&id=$id&abschluss=$id&msg=$msg&mail=0'\">&nbsp;
        </div>");
    }

    $this->AuftragMenu();
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function AuftragDeleteAusVersand()
  {
    $id = $this->app->Secure->GetGET('id');

    //$rechnung = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$id' LIMIT 1");
    if($id > 0){
      $lieferschein = $this->app->DB->Select("SELECT lieferschein  FROM versand WHERE id='$id' LIMIT 1");
    }else{
      $lieferschein = 0;
    }
    if($lieferschein > 0){
      $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
    }else{
      $auftragid = 0;
    }
    if($auftragid > 0){
      $this->app->DB->Update("UPDATE auftrag SET rma=1,status='storniert' WHERE id='$auftragid' LIMIT 1");
    }
    if($lieferschein > 0){
      $auftrag = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
    }else{
      $auftrag = 0;
    }

    // status aendern
    if($lieferschein > 0)
    {
      $this->app->DB->Update("UPDATE lieferschein SET versandart='rma',status='storniert' WHERE id='$lieferschein' LIMIT 1");
      $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE objekt='lieferschein' AND parameter='$lieferschein'");
      $this->app->erp->LieferscheinProtokoll($lieferschein,'Lieferschein durch Auftrag aus Versand storniert');
    }

    $this->app->erp->AuftragProtokoll($auftrag,'Auftrag aus Versand storniert');

    // RMA anlegen 

    $msg = $this->app->erp->base64_url_encode('<div class="warning">Der Auftrag wurde als RMA im Versand markiert!</div>  ');

    $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$auftrag&msg=$msg");
  }



  public function AuftragProtokoll()
  {
    $this->AuftragMenu();
    $id = $this->app->Secure->GetGET('id');
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM auftrag_protokoll WHERE auftrag='$id' ORDER by zeit DESC");
    $tmp->DisplayNew('TAB1','Protokoll','noAction');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  public function AuftragProforma()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->AuftragNeuberechnen($id);

    $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");

    if(class_exists('AuftragPDFCustom'))
    {
      $Brief = new AuftragPDFCustom($this->app,$projekt,'proforma');
    }
    else{
      $Brief = new AuftragPDF($this->app,$projekt,'proforma');
    }
    $Brief->GetAuftrag($id);
    $Brief->displayDocument(); 

    $this->AuftragList();
  }

  public function AuftragInlinePDF()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0)
    {
      $this->app->erp->AuftragNeuberechnen($id);
      $auftragarr = $this->app->DB->SelectRow("SELECT projekt,schreibschutz FROM auftrag WHERE id='$id' LIMIT 1");
    }

    if(!empty($auftragarr))
    {
      $projekt = $auftragarr['projekt'];//$this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
      $schreibschutz = $auftragarr['schreibschutz'];//$this->app->DB->Select("SELECT schreibschutz FROM auftrag WHERE id='$id' LIMIT 1");
    }else{
      $projekt = 0;
      $schreibschutz = 0;
    }

    $frame = $this->app->Secure->GetGET('frame');
    if($frame != ''){
      $file = urlencode("../../../../index.php?module=auftrag&action=inlinepdf&id=$id");
      echo "<iframe width=\"100%\" height=\"100%\" style=\"height:calc(100vh - 110px)\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
      $this->app->ExitXentral();
    }
    if(class_exists('AuftragPDFCustom'))
    {
      $Brief = new AuftragPDFCustom($this->app,$projekt);
    }else{
      $Brief = new AuftragPDF($this->app,$projekt);
    }
    $Brief->GetAuftrag($id);
    $Brief->inlineDocument($schreibschutz);
  }


  public function AuftragPDF()
  {
    $id = $this->app->Secure->GetGET('id');

    if($id > 0){
      $this->app->erp->AuftragNeuberechnen($id);
      $auftragarr = $this->app->DB->SelectRow("SELECT projekt,schreibschutz FROM auftrag WHERE id='$id' LIMIT 1");
    }
    if(!empty($auftragarr))
    {
      $projekt = $auftragarr['projekt'];
      $schreibschutz = $auftragarr['schreibschutz'];
    }else{
      $projekt = 0;
      $schreibschutz = 0;
    }

    if(class_exists('AuftragPDFCustom'))
    {
      $Brief = new AuftragPDFCustom($this->app,$projekt);
    }else{
      $Brief = new AuftragPDF($this->app,$projekt);
    }
    $Brief->GetAuftrag($id);
    $Brief->displayDocument($schreibschutz);
    $this->AuftragList();
  }


  public function AuftragMenu()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->erp->AuftragNeuberechnen($id);
      $auftragarr = $this->app->DB->SelectRow("SELECT belegnr,name, status FROM auftrag WHERE id='$id' LIMIT 1");
    }
    $backurl = $this->app->Secure->GetGET('backurl');
    $backurl = $this->app->erp->base64_url_decode($backurl);

    $belegnr = '';
    $name = '';
    $status = '';
    if(!empty($auftragarr)) {
      $belegnr = $auftragarr['belegnr'];
      $name = $auftragarr['name'];
      $status = $auftragarr['status'];
    }

    if($belegnr=='0' || $belegnr=='') {
      $belegnr ='(Entwurf)';
    }

    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$name Auftrag $belegnr");
    // status bestell

    if ($status==='angelegt') {
      $this->app->erp->MenuEintrag("index.php?module=auftrag&action=freigabe&id=$id","Freigabe");
    }

    $this->app->erp->MenuEintrag("index.php?module=auftrag&action=edit&id=$id","Details");


    if($status==='bestellt') {
      $this->app->Tpl->Add('TABS',"<li><a href=\"index.php?module=auftrag&action=wareneingang&id=$id\">Wareneingang<br>R&uuml;ckst&auml;nde</a></li>");
      $this->app->Tpl->Add('TABS',"<li><a class=\"tab\" href=\"index.php?module=auftrag&action=wareneingang&id=$id\">Mahnstufen</a></li>");
    }
    $anzahldateien='';
    if($id > 0) {
      $anzahldateien = $this->app->erp->AnzahlDateien('Auftrag',$id);
      $anzahldateien = $anzahldateien > 0?' ('.$anzahldateien.')':'';
    }
    $action = $this->app->Secure->GetGET('action');
    $this->app->erp->MenuEintrag("index.php?module=auftrag&action=dateien&id=$id",'Dateien'.$anzahldateien);
    //  $this->app->erp->MenuEintrag("index.php?module=auftrag&action=protokoll&id=$id","Protokoll");

    if($this->app->erp->Firmendaten('auftrag_eantab')=='1'){
      $this->app->erp->MenuEintrag('index.php?module=auftrag&action=ean&id='.$id, 'Barcodescanner');
    }

    if($action==='abschicken' || $action==='zahlungsmahnungswesen'){
      $this->app->erp->MenuEintrag("index.php?module=auftrag&action=edit&id=$id", 'Zur&uuml;ck zum Auftrag');
    }
    else if($backurl==''){
      $this->app->erp->MenuEintrag('index.php?module=auftrag&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    else{
      $this->app->erp->MenuEintrag((string)$backurl, 'Zur&uuml;ck zur &Uuml;bersicht');
    }

    $this->app->erp->RunMenuHook('auftrag');
    if($id) {
      $this->app->erp->RunMenuHook('auftrag_edit');
    }
  }

  function AuftragPositionstabelle($parsetarget)
  {
    $this->app->YUI->TableSearch($parsetarget,'auftraegeoffene');
  }


  public function AuftragPositionen()
  {
    $id = $this->app->Secure->GetGET('id');

    $this->app->erp->AuftragNeuberechnen($id);
    $this->app->YUI->AARLGPositionen(false);
  }

  function DelAuftragStueckliste($id, $sid, $lvl = 0)
  {
    if($sid <= 0 || $id <= 0 || $lvl > 10){
      return;
    }

    $unterartikel = $this->app->DB->SelectArr("SELECT * FROM auftrag_position WHERE explodiert_parent='$sid' ORDER BY sort");

    if($unterartikel && count($unterartikel)>0 && $sid >0 && $id >0)
    {
      $cunterartikel = count($unterartikel);
      for($i=0;$i<$cunterartikel;$i++)
      {
        $sidexplodiert = $unterartikel[$i]['id'];
        if($sidexplodiert>0)
        {
          $sort = $this->app->DB->Select("SELECT sort FROM auftrag_position WHERE id='$sidexplodiert' LIMIT 1");
          if($sort>0)
          {
            $this->app->DB->Delete("DELETE FROM auftrag_position WHERE id='$sidexplodiert' LIMIT 1");
            $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE parameter='$id' AND objekt='auftrag'
                AND artikel='".$unterartikel[$i]['artikel']."'");
            $this->app->DB->Update("UPDATE auftrag_position SET sort=sort-1 WHERE auftrag='$id' AND sort > $sort");
            $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos - 1 WHERE doctype = 'auftrag' AND doctypeid = '$id' AND pos >= ".($sort-1)." ");
          }else{
            $this->app->DB->Delete("DELETE FROM auftrag_position WHERE id='$sidexplodiert' LIMIT 1");
            $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE parameter='$id' AND objekt='auftrag'
                AND artikel='".$unterartikel[$i]['artikel']."'");              
          }
        }
        $this->DelAuftragStueckliste($id, $sidexplodiert, $lvl + 1);
        $this->app->DB->Update(
          sprintf(
            'UPDATE `artikel` SET `laststorage_changed` = NOW() WHERE `id` = %d',
            $unterartikel[$i]['artikel']
          )
        );
      }
      // alle wirklich loeschen
      $this->app->DB->Delete("DELETE FROM auftrag_position WHERE explodiert_parent='$sid' AND auftrag='$id'");
      
    }
  }
  
  public function DelAuftragPosition()
  {
    $sid = (int)$this->app->Secure->GetGET('sid');
    $id = (int)$this->app->Secure->GetGET('id');
    if($sid > 0){
      $this->DelAuftragStueckliste($id, $sid);
    }
    $this->app->YUI->SortListEvent('del','auftrag_position','auftrag');
    if($sid > 0)
    {
      $this->DelAuftragStueckliste($id, $sid);
      $this->app->erp->AuftragNeuberechnen($id);
    }
    $this->AuftragPositionen();
  }
  
  public function CopyAuftragPosition()
  {
    $this->app->YUI->SortListEvent('copy','auftrag_position','auftrag');
    $this->AuftragPositionen();
  }

  public function UpAuftragPosition()
  {
    $this->app->YUI->SortListEvent('up','auftrag_position','auftrag');
    $this->AuftragPositionen();
  }

  public function DownAuftragPosition()
  {
    $this->app->YUI->SortListEvent('down','auftrag_position','auftrag');
    $this->AuftragPositionen();
  }

  public function AuftragCheckDisplayPopup()
  { 
    $frame = $this->app->Secure->GetGET('frame');
    $id = $this->app->Secure->GetGET('id');

    if($frame=='false')
    { 
      // hier nur fenster größe anpassen
      $this->app->YUI->IframeDialog(700,700);
    } else {
      // nach page      

      $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
      $projektcheckname = $this->app->DB->Select("SELECT checkname FROM projekt WHERE id='$projekt' LIMIT 1");

      include_once './plugins/class.'.$projektcheckname.'.php';
      $tmp = new $projektcheckname($this->app);
      $tmp->CheckDisplay('PAGE',$id);

      $this->app->BuildNavigation=false;
    }
  }


  public function AuftragPositionenEditPopup()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'getopenaccordions')
    {
      $accordions = $this->app->Secure->GetPOST('accordions');
      $accordions = explode('*|*',$accordions);
      foreach($accordions as $k => $v)
      {
        if(empty($v))
        {
          unset($accordions[$k]);
        }else{
          $accordions[$k] = 'auftrag_accordion'.$v;
        }
      }
      $ret = [];
      if(!empty($accordions))
      {
        $accordions = $this->app->User->GetParameter($accordions);
        if(!empty($accordions))
        {
          foreach($accordions as $v)
          {
            if(!empty($v['value']))
            {
              $ret['accordions'][] = str_replace('auftrag_accordion','',$v['name']);
            }
          }
        }
      }
      echo json_encode($ret);
      $this->app->ExitXentral();
    }
    if($cmd === 'setaccordion')
    {
      $name = $this->app->Secure->GetPOST('name');
      $active = $this->app->Secure->GetPOST('active');
      $this->app->User->SetParameter('auftrag_accordion'.$name, $active);
      echo json_encode(array('success'=>1));
      $this->app->ExitXentral();
    }
    $id = $this->app->Secure->GetGET('id');
    $fmodul = $this->app->Secure->GetGET('fmodul');
    $artikel= $this->app->DB->Select("SELECT artikel FROM auftrag_position WHERE id='$id' LIMIT 1");

    // nach page inhalt des dialogs ausgeben
    $filename = 'widgets/widget.auftag_position_custom.php';
    if(is_file($filename)) 
    {
      include_once $filename;
      $widget = new WidgetAuftrag_positionCustom($this->app,'PAGE');
    } else {
      $widget = new WidgetAuftrag_position($this->app,'PAGE');
    }

    $sid= $this->app->DB->Select("SELECT auftrag FROM auftrag_position WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute('close_refresh',
        "index.php?module=auftrag&action=positionen&id=$sid&fmodul=$fmodul");
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }


  function AuftragEdit()
  {
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");
    $storno = $this->app->Secure->GetGET("storno");

    // zum aendern vom Vertrieb
    $sid = $this->app->Secure->GetGET("sid");
    $cmd = $this->app->Secure->GetGET("cmd");
    if($this->app->Secure->GetPOST('resetextsoll')) {
      $this->app->DB->Update(
        sprintf(
          'UPDATE auftrag SET extsoll = 0 WHERE id = %d',
          $id
        )
      );
      $this->app->erp->AuftragNeuberechnen($id);
    }
    $this->app->erp->RunHook('auftrag_edit_hook1',1, $id);
    if($cmd === 'dadown')
    {
      $erg['status'] = 0;
      $daid = $this->app->Secure->GetPOST("da_id");
      $check = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.id = '$daid' and d.geloescht <> 1 LIMIT 1");
      if($check)
      {
        $sort = $check[0]['sort']+1;
        if($sort > 1)
        {
          $check2 = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.objekt like 'angebot' AND ds.sort = '$sort' AND d.geloescht <> 1 AND ds.parameter = '$id' LIMIT 1");
          if($check2)
          {
            $erg['status'] = 1;
            $erg['from'] = $check2[0]['id'];
            $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = sort + 1 WHERE id = '$daid' LIMIT 1");
            $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = sort - 1 WHERE id = '".$check2[0]['id']."' LIMIT 1");
          }
        }
      }
      echo json_encode($erg);
      $this->app->ExitXentral();
    }
    
    if($cmd === 'daup')
    {
      $erg['status'] = 0;
      $daid = $this->app->Secure->GetPOST("da_id");
      $check = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.id = '$daid' and d.geloescht <> 1 LIMIT 1");
      if($check)
      {
        $sort = $check[0]['sort']-1;
        if($sort > 0)
        {
          $check2 = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.objekt like 'auftrag' AND ds.sort = '$sort' AND d.geloescht <> 1 AND ds.parameter = '$id' LIMIT 1");
          if($check2)
          {
            $erg['status'] = 1;
            $erg['from'] = $check2[0]['id'];
            $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = sort - 1 WHERE id = '$daid' LIMIT 1");
            $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = sort + 1 WHERE id = '".$check2[0]['id']."' LIMIT 1");
          }
        }
      }
      echo json_encode($erg);
      $this->app->ExitXentral();
    }

    if($this->app->erp->VertriebAendern('auftrag',$id,$cmd,$sid)){
      return;
    }
  
    if($this->app->erp->InnendienstAendern('auftrag',$id,$cmd,$sid)){
      return;
    }
 

    if($this->app->erp->DisableModul('auftrag',$id)) {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->AuftragMenu();
      return;
    }
    $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$id' LIMIT 1");
    if($adresse <=0) {
      $this->app->Tpl->Add('JAVASCRIPT','$(document).ready(function() { if(document.getElementById("adresse"))document.getElementById("adresse").focus(); });');
      $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Achtung! Dieses Dokument ist mit keiner Kunden-Nr. verlinkt. Bitte geben Sie die Kundennummer an und klicken Sie &uuml;bernehmen oder Speichern!</div>");
    }
    $this->app->erp->InfoAuftragsErfassung("auftrag",$id);
    $teilArr = $this->app->DB->SelectRow(
      sprintf(
        'SELECT teillieferungvon, teillieferungnummer, belegnr FROM auftrag WHERE id=%d LIMIT 1',
        $id
      )
    );
    $teillieferungvon= $teilArr['teillieferungvon'];//$this->app->DB->Select("SELECT teillieferungvon FROM auftrag WHERE id='$id' LIMIT 1");
    $teillieferungnummer= $teilArr['teillieferungnummer'];//$this->app->DB->Select("SELECT teillieferungnummer FROM auftrag WHERE id='$id' LIMIT 1");

    $hauptid = $id;
    $timeout = 0;
    while($timeout < 100) {
      $checkteillieferungvon = $this->app->DB->Select(
        sprintf(
          'SELECT teillieferungvon FROM auftrag WHERE id=%d LIMIT 1',
          $hauptid
        )
      );
      if($checkteillieferungvon > 0) {
        $hauptid = $checkteillieferungvon;
      }
      else {
        break;
      }
      $timeout++;
    }
    $teillieferungnummermax = 0;
    $anzahlteillieferungen = $this->app->DB->SelectArr(
      sprintf(
        'SELECT id,belegnr,teillieferungnummer FROM auftrag WHERE teillieferungvon=%d ORDER BY belegnr',
        $id
      )
    );
    if(!empty($anzahlteillieferungen)) {
      $canzahlteillieferungen = count($anzahlteillieferungen);
      for($ati=0;$ati<$canzahlteillieferungen;$ati++) {
        if($canzahlteillieferungen[$ati]['teillieferungnummer'] + 1 > $teillieferungnummermax) {
          $teillieferungnummermax = $canzahlteillieferungen[$ati]['teillieferungnummer'] + 1;
        }
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="info">Zu diesem Auftrag geh&ouml;rt Teilauftrag Nr. <a href="index.php?module=auftrag&action=edit&id='.
          $anzahlteillieferungen[$ati]['id'].'" target="_blank">'
          .$anzahlteillieferungen[$ati]['belegnr'].'</a></div>'
        );
      }
    }
    $teillieferung_von_auftrag_nummer = $teilArr['belegnr'];

    if($teillieferungvon>0) {
      if($hauptid != $id) {
        $teillieferungnummermax = $this->app->DB->Select(
          "SELECT MAX(teillieferungnummer)+1 FROM auftrag WHERE teillieferungvon='$hauptid' LIMIT 1"
        );
        $teillieferung_von_auftrag_nummer = $this->app->DB->Select(
          "SELECT belegnr FROM auftrag WHERE id='$hauptid' LIMIT 1"
        );
      }
      $this->app->Tpl->Add(
        'MESSAGE',
        "<div class=\"warning\">Dies ist Teilauftrag Nr. $teillieferungnummer (Aktuell gesplittet in $teillieferungnummermax Auftr&auml;ge). Der urspr&uuml;ngliche Auftrag war: <a href=\"index.php?module=auftrag&action=edit&id=$hauptid\" target=\"_blank\">$teillieferung_von_auftrag_nummer</a></div>"
      );
    }       


    $check = $this->app->DB->SelectPairs(
      "SELECT b.id, b.belegnr
      FROM auftrag_position ap
      INNER JOIN bestellung_position bp ON ap.id = bp.auftrag_position_id
      INNER JOIN bestellung b ON bp.bestellung = b.id
      WHERE ap.auftrag='$id' 
      GROUP BY b.belegnr, b.id
      ORDER BY b.belegnr, b.id"
    );
    if($check) {
      $this->app->Tpl->Add(
        'MESSAGE',
        "<div class=\"info\">Zu diesem Auftrag geh&ouml;r"
        .(count($check) == 1?'t':'en')
        ." die Bestellung".(count($check) == 1?':':'en:')
      );
      foreach($check as $supplierOrderId => $supplieryNumber) {
        $this->app->Tpl->Add('MESSAGE','&nbsp;<a href="index.php?module=bestellung&action=edit&id='
          .$supplierOrderId.'" target="_blank"><input type="button" value="'
          .($supplieryNumber?$supplieryNumber:'ENTWURF').'" /></a>'
        );
      }
      $this->app->Tpl->Add('MESSAGE',"</div>");
    }

    
    if($this->app->erp->ModulVorhanden('lieferkette')) {
      $auftraglieferkette = $this->app->DB->Select(
        "SELECT l.auftrag 
        FROM lieferkette l 
        INNER JOIN lieferkette_bestellung lb 
            ON l.id = lb.lieferkette AND lb.belegtyp = 'auftrag' AND belegid = '$id' AND l.auftrag > 0 
        LIMIT 1"
      );
      if($auftraglieferkette) {
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="info">Dieser Auftrag geh&ouml;rt zur Lieferkette des Auftrags <a href="index.php?module=auftrag&action=edit&id='
          .$auftraglieferkette.'" target="_blank"><input type="button" value="'
          .$this->app->DB->Select(
            "SELECT if(belegnr = '','ENTWURF',belegnr) 
            FROM auftrag 
            WHERE id = '$auftraglieferkette' 
            LIMIT 1"
          )
          .'" /></a></div>'
        );
      }
    }

    $orderRow = $this->app->DB->SelectRow(sprintf('SELECT * FROM auftrag WHERE id = %d', $id));

    $zahlungsweise= $orderRow['zahlungsweise'];//$this->app->DB->Select("SELECT zahlungsweise FROM auftrag WHERE id='$id' LIMIT 1");
    $zahlungszieltage= $orderRow['zahlungszieltage'];//$this->app->DB->Select("SELECT zahlungszieltage FROM auftrag WHERE id='$id' LIMIT 1");

    $status= $orderRow['status'];//$this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
    $schreibschutz= $orderRow['schreibschutz'];//$this->app->DB->Select("SELECT schreibschutz FROM auftrag WHERE id='$id' LIMIT 1");


    $adresse= $orderRow['adresse'];//$this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$id' LIMIT 1");
    $liefersperre= $this->app->DB->Select("SELECT liefersperre FROM adresse WHERE id='$adresse' LIMIT 1");

    if($id > 0 && $this->app->DB->Select(
        sprintf(
          'SELECT id FROM auftrag WHERE schreibschutz =1  AND zuarchivieren = 1 AND id = %d',
          $id
        )
      )
    ) {
      $this->app->erp->PDFArchivieren('auftrag', $id, true);
    }

    if($status !== 'angelegt' && $status !== 'angelegta' && $status !== 'a') {
      $Brief = new Briefpapier($this->app);

      if($Brief->zuArchivieren($id, 'auftrag')) {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Der Auftrag ist noch nicht archiviert! Bitte versenden oder manuell archivieren. <input type=\"button\" onclick=\"if(!confirm('Soll das Dokument archiviert werden?')) return false;else window.location.href='index.php?module=auftrag&action=archivierepdf&id=$id';\" value=\"Manuell archivieren\" /> <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('auftrag',$id)\"></div>");
      }
      elseif(!$this->app->DB->Select("SELECT versendet FROM auftrag WHERE id = '$id' LIMIT 1")) {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Der Auftrag wurde noch nicht versendet! <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('auftrag',$id)\"></div>");
      }
    }

    if($liefersperre=='1' && ($status==='freigegeben' || $status==='angelegt')) {
      $this->app->Tpl->Add('MESSAGE','<div class="warning">Achtung: Der Kunde hat eine Liefersperre!</div>');
    }
    $this->app->erp->AuftragEinzelnBerechnen($id);
    $this->app->erp->AuftragNeuberechnen($id);
    $this->app->erp->AuftragAutoversandBerechnen($id); // heute wieder eingebaut 09.03.2019 BS weil Termin ampel falsch

    $this->app->erp->DisableVerband();
    $this->AuftragMiniDetail('MINIDETAIL',true);

    $icons = $this->app->YUI->IconsSQL();
    if(strpos($icons,'aac.status')) {
      $icons = $this->app->DB->Select("SELECT $icons FROM auftrag a LEFT JOIN `auftragsampel_auftrag_cache` aac ON a.id = aac.auftrag WHERE a.id='$id' LIMIT 1");
    }
    else{
      $icons = $this->app->DB->Select("SELECT $icons FROM auftrag a WHERE a.id='$id' LIMIT 1");
    }
    $this->app->Tpl->Set('STATUSICONS',$icons);
    
    $this->app->YUI->AARLGPositionen();

    $orderRow = $this->app->DB->SelectRow(sprintf('SELECT * FROM auftrag WHERE id = %d', $id));
    $status= $orderRow['status'];//$this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
    if($status=='') {
      $this->app->DB->Update("UPDATE auftrag SET status='angelegt' WHERE id='$id' LIMIT 1");
      $orderRow['status'] = $this->app->DB->Select(sprintf('SELECT status FROM auftrag WHERE id = %d', $id));
    }

    $tmpcheckversand = $orderRow['versandart'];//$this->app->DB->Select("SELECT versandart FROM auftrag WHERE id='$id' LIMIT 1");
    if($tmpcheckversand==='packstation' && $orderRow['abweichendelieferadresse'] != 0) {
      $this->app->DB->Update("UPDATE auftrag SET abweichendelieferadresse='0' WHERE id='$id' LIMIT 1");
    }

    $status = $orderRow['status'];//$this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
    $nummer = $orderRow['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
    $adresse = $orderRow['adresse'];//$this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$id' LIMIT 1");

    $lieferant = $orderRow['lieferant'];//$this->app->DB->Select("SELECT lieferant FROM auftrag WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    $lieferantenretoure = $orderRow['lieferantenauftrag'];//$this->app->DB->Select("SELECT lieferantenauftrag FROM auftrag WHERE id='$id' LIMIT 1");
    if($lieferantenretoure=='1' && $lieferant<=0) {
      $this->app->Tpl->Add('JAVASCRIPT','$(document).ready(function() { if(document.getElementById("adresse"))document.getElementById("adresse").focus(); });');
      $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Pflichtfeld! Bitte geben Sie eine Lieferanten-Nr. an!</div>");
    }
    else if ($adresse <=0 && $lieferantenretoure!='1') {
      $this->app->Tpl->Add('JAVASCRIPT','$(document).ready(function() { if(document.getElementById("adresse"))document.getElementById("adresse").focus(); });');
      $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Pflichtfeld! Bitte geben Sie eine Kunden-Nr. an!</div>");
    }

    if($schreibschutz!='1')// && $this->app->erp->RechteVorhanden("auftrag","schreibschutz"))
    {
      $this->app->erp->AnsprechpartnerButton($adresse);
      $this->app->erp->LieferadresseButton($adresse);
      $this->app->erp->AnsprechpartnerAlsLieferadresseButton($adresse);
      $this->app->erp->AdresseAlsLieferadresseButton($adresse);
    }

    if($nummer!='') {
      $this->app->Tpl->Set('NUMMER',$nummer);
      if($this->app->erp->RechteVorhanden('adresse','edit')){
        $this->app->Tpl->Set(
          'KUNDE',
          "&nbsp;&nbsp;&nbsp;Kd-Nr. <a href=\"index.php?module=adresse&action=edit&id=$adresse\" target=\"_blank\">"
          . $kundennummer . "</a>"
        );
      }
      else{
        $this->app->Tpl->Set(
          'KUNDE',
          '&nbsp;&nbsp;&nbsp;Kd-Nr. ' . $kundennummer
        );
      }
    }

    $this->app->Tpl->Set('ICONMENU',$this->AuftragIconMenu($id));
    $this->app->Tpl->Set('ICONMENU2',$this->AuftragIconMenu($id,2));

    if($status==='angelegt')
    {
      $this->app->Tpl->Set('ABGESCHLOSSENENABLE',"<!--"); //TODO
      $this->app->Tpl->Set('ABGESCHLOSSENDISABLE',"-->"); //TODO
    }

    if($status!=='storniert')
    {
      $this->app->Tpl->Set('STORNOENABLE','<!--');
      $this->app->Tpl->Set('STORNODISABLE','-->');
    }

    $stornobezahlt = $orderRow['stornobezahlt'];// $this->app->DB->Select("SELECT stornobezahlt FROM auftrag WHERE id='$id' LIMIT 1");

    if($storno!=='abschluss' && $stornobezahlt==0) {
      $this->app->Tpl->Set('STORNORETOUREENABLE','<!--');
      $this->app->Tpl->Set('STORNORETOUREDISABLE','-->');
    }
    else {
      $this->app->Tpl->Set('HIDDENFIELD',"<input type=\"hidden\" name=\"storno_abschluss\" value=\"1\">");
      // bearbeiter 
      $stornobezahltvon = $orderRow['stornobezahltvon'];//$this->app->DB->Select("SELECT stornobezahltvon FROM auftrag WHERE id='$id' LIMIT 1");
      $stornobezahltam = $orderRow['stornobezahltam'];//$this->app->DB->Select("SELECT stornobezahltam FROM auftrag WHERE id='$id' LIMIT 1");
      if($stornobezahltvon==''){
        $this->app->DB->Update(
          "UPDATE auftrag SET stornobezahltvon='"
          . $this->app->DB->real_escape_string($this->app->User->GetName())
          . "' WHERE id='$id' LIMIT 1"
        );
      }
      if($stornobezahltam=="0000-00-00"){
        $this->app->DB->Update("UPDATE auftrag SET stornobezahltam=NOW() WHERE id='$id' LIMIT 1");
      }
    }

    $this->AuftragAmpel($id,'AMPEL');
    $optional = '';
   
      $lieferscheine = $this->app->DB->SelectPairs(
        "SELECT id,belegnr FROM lieferschein WHERE auftragid='$id' AND auftragid > 0"
      );
      $deliveryNoteIds = array_keys($lieferscheine);
      $deliveryNoteIdsImplode = implode(',', $deliveryNoteIds);
      if(!empty($lieferscheine)) {
        foreach($lieferscheine as $deliveryNoteId => $deliveryNoteNumber) {
          $optional .= "&nbsp;<input type=\"button\" value=\"LS "
            .$deliveryNoteNumber
            ."\" onclick=\"window.location.href='index.php?module=lieferschein&action=pdf&id="
            .$deliveryNoteId."'\">";
        }
      }

      $rechnungid = $this->app->DB->Select("SELECT rechnungid FROM auftrag WHERE id = '$id' LIMIT 1");
      $rechnungen = $this->app->DB->SelectPairs(
        "SELECT id,belegnr FROM rechnung WHERE (auftragid='$id' AND auftragid > 0) OR id = '$rechnungid'"
      );
      if(!empty($rechnungen)) {
        foreach($rechnungen as $invoiceId => $invoiceNumber) {
          $optional .= "&nbsp;<input type=\"button\" value=\"RE "
            .$invoiceNumber
            ."\" onclick=\"window.location.href='index.php?module=rechnung&action=pdf&id="
            .$invoiceId."'\">";
        }
      }

      $projekt = $this->app->DB->Select("SELECT projekt from auftrag where id = '$id' LIMIT 1");
    if($schreibschutz=='1' && $this->app->erp->RechteVorhanden('auftrag','schreibschutz')) {
      $this->app->Tpl->Add(
        'MESSAGE',
        "<div class=\"warning\">Dieser Auftrag ist schreibgesch&uuml;tzt und darf daher nicht bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml;r diesen Auftrag wirklich entfernt werden?')) return false;else window.location.href='index.php?module=auftrag&action=schreibschutz&id=$id';\">&nbsp;$optional</div>"
      );
      //      $this->app->erp->CommonReadonly();
    }
    else {
      if(isset($optional) && (string)$optional !== '') {
        $this->app->Tpl->Add(
          'MESSAGE',
          "<div class=\"warning\">Zu diesem Auftrag gibt es folgende Dokumente. &nbsp;$optional</div>"
        );
      }
    }
    if($schreibschutz=='1') {
      $this->app->erp->CommonReadonly();
    }

    if($schreibschutz != '1'){
      if($this->app->erp->Firmendaten('schnellanlegen') == '1'){
        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN', '      <input type="button" value="&uuml;bernehmen" onclick="document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
          ');
        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN2', '
        <input type="button" value="&uuml;bernehmen" onclick="if(!confirm(\'Soll der neue Lieferant wirklich &uuml;bernommen werden? Es werden alle Felder &uuml;berladen.\')) return false;else document.getElementById(\'uebernehmen2\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen2" name="uebernehmen2" value="0">
        ');

      }else{
        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN', '
          <input type="button" value="&uuml;bernehmen" onclick="document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
          ');

        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN2', '
        <input type="button" value="&uuml;bernehmen" onclick="if(!confirm(\'Soll der neue Lieferant wirklich &uuml;bernommen werden? Es werden alle Felder &uuml;berladen.\')) return false;else document.getElementById(\'uebernehmen2\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen2" name="uebernehmen2" value="0">
        ');

      }
    }
    // immer wenn sich der lieferant genändert hat standartwerte setzen
    if($this->app->Secure->GetPOST('adresse')!='') {
      $tmp = $this->app->Secure->GetPOST('adresse');
      $kundennummer = $this->app->erp->FirstTillSpace($tmp);
      $filter_projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id = '$id' LIMIT 1");
      $adresse =  $this->app->DB->Select(
        "SELECT id 
        FROM adresse 
        WHERE kundennummer='$kundennummer' AND geloescht=0 "
        .$this->app->erp->ProjektRechte('projekt', true, 'vertrieb')." 
        ORDER by ".($filter_projekt?" projekt = '$filter_projekt' DESC, ":"")." projekt 
        LIMIT 1"
      );

      $uebernehmen =$this->app->Secure->GetPOST('uebernehmen');
      if($uebernehmen=='1' && $schreibschutz != '1') {
        // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
        $this->app->erp->LoadAuftragStandardwerte($id,$adresse);
        $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id = '$id' LIMIT 1");
        if($projekt != $filter_projekt) {
          $deactivateAutoversand = (int)$this->app->erp->Projektdaten($projekt,'deactivateautoshipping');
          if($deactivateAutoversand) {
            $this->app->DB->Update(
              sprintf(
                'UPDATE auftrag SET autoversand = 0 WHERE autoversand = 1 AND schreibschutz = 0 AND id = %d',
                $id
              )
            );
          }
        }
        $projektlager = $this->app->DB->Select("SELECT standardlager FROM projekt WHERE id = '$projekt' LIMIT 1");
        $this->app->DB->Update("UPDATE auftrag SET standardlager = '$projektlager' WHERE id = '$id' LIMIT 1");
        $this->app->DB->Update("UPDATE auftrag SET lieferantenauftrag=0 WHERE id='$id'");
        $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$id");
      }
    }

    if($this->app->Secure->GetPOST('lieferant')!='') {
      $tmplieferant = $this->app->Secure->GetPOST('lieferant');
      $lieferantennummer = $this->app->erp->FirstTillSpace($tmplieferant);

      $adresselieferant =  $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='$lieferantennummer' AND lieferantennummer!=''  AND geloescht=0 LIMIT 1");

      $uebernehmen2 =$this->app->Secure->GetPOST('uebernehmen2');
      if($uebernehmen2=='1' && $schreibschutz != '1') {
        // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
        $this->app->DB->Update("UPDATE auftrag SET lieferantenauftrag=1 WHERE id='$id'");
        $this->app->erp->LoadAuftragStandardwerte($id,$adresselieferant,true);
        $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$id");
      }
    }

    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $table->Query(
      "SELECT ap.bezeichnung as artikel, ap.nummer as Nummer, ap.menge, 
       (SELECT TRIM(SUM(l.menge))+0 
       FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel
           ) as Lagerbestand, ap.geliefert ausgeliefert,ap.vpe as VPE
       FROM auftrag_position ap
       WHERE ap.auftrag='$id'"
    );
    $table->DisplayNew('POSITIONEN','VPE','noAction');
    $summe = $this->app->DB->Select(
      "SELECT FORMAT(SUM(menge*preis),2) FROM auftrag_position
        WHERE auftrag='$id'"
    );

    $waehrung = $this->app->DB->Select(
      "SELECT waehrung FROM auftrag_position
        WHERE auftrag='$id' LIMIT 1"
    );

    $orderRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM auftrag WHERE id = %d',
        $id
      )
    );

    $summebrutto = $orderRow['gesamtsumme'];//$this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$id' LIMIT 1");
    $ust_befreit_check = $orderRow['ust_befreit'];//$this->app->DB->Select("SELECT ust_befreit FROM auftrag WHERE id='$id' LIMIT 1");

    if($ust_befreit_check==1){
      $tmp = 'Kunde ist UST befreit';
    }
    else{
      $tmp = 'Kunde zahlt mit UST';
    }

    if($summe > 0){
      $this->app->Tpl->Add(
        'POSITIONEN',
        "<br><center>Zu zahlen: <b>$summe (netto) $summebrutto (brutto) $waehrung</b> ($tmp)&nbsp;&nbsp;"
      );
    }

    $vertrieb = $orderRow['vertrieb'];//$this->app->DB->Select("SELECT vertrieb FROM auftrag WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('VERTRIEB',"<input type=\"text\" value=\"".$vertrieb."\" size=\"30\" readonly>");

    $belegnr= $orderRow['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('BELEGNR',"<input type=\"text\" value=\"".$belegnr."\" readonly size=\"30\">");

    $status = $orderRow['status'];//$this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('STATUS',"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");

    // ENDE
    

    //alle RE und LS zu diesem Auftrag
    $auftragsnummer  = $orderRow['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
    $anzahl =   $this->app->DB->Select(
      "SELECT COUNT(r.belegnr)
        FROM rechnung r 
        WHERE r.adresse='$adresse' AND r.auftrag='$auftragsnummer' AND r.auftrag!=''");

    if($anzahl >0) {
      $this->app->Tpl->Set('AUFTRAGSDOKUMENTE',"<fieldset><legend>Rechnungen und Lieferscheine</legend>");

      $table = new EasyTable($this->app);
      $table->Query(
        "SELECT r.belegnr as rechnung, DATE_FORMAT(r.datum,'%d.%m.%Y') as ausgang, 
          l.belegnr as lieferschein, r.soll as betrag 
        FROM rechnung r 
        LEFT JOIN lieferschein l ON r.lieferschein=l.id 
        WHERE r.adresse='$adresse' AND r.auftrag='$auftragsnummer' AND r.auftrag!=''"
      );
      $table->DisplayNew('AUFTRAGSDOKUMENTE',"Betrag","noAction");

      $this->app->Tpl->Add('AUFTRAGSDOKUMENTE',"</fieldset>");
    }

    //suche alle LS zu diesem Auftrag
    if($auftragsnummer>0) {
      $trackingInfo = $this->AuftragTrackingTabelle(empty($deliveryNoteIds)?0: $id);
      $this->app->Tpl->Set('VERSAND', $trackingInfo);
    } 

    // UST
    $ust_ok = $orderRow['ust_ok'];//$this->app->DB->Select("SELECT ust_ok FROM auftrag WHERE id='$id' LIMIT 1");
    $ust_befreit = $orderRow['ust_befreit'];//$this->app->DB->Select("SELECT ust_befreit FROM auftrag WHERE id='$id' LIMIT 1");

    $ustprfid = $this->app->DB->Select(
      "SELECT id 
      FROM ustprf 
      WHERE DATE_FORMAT(datum_online,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') AND adresse='$adresse' AND status='erfolgreich' 
      LIMIT 1"
    );

    if($ust_befreit==0) {
      $this->app->Tpl->Set('USTPRUEFUNG','Abgabe in Inland');
    }
    else if ($ust_befreit==1) {

      if($ust_ok == 1) {
        $datum = $this->app->DB->Select("SELECT briefbestellt FROM ustprf WHERE id='$ustprfid' LIMIT 1");
        $datum = $this->app->String->Convert($datum,"%1-%2-%3","%3.%2.%1");
        $this->app->Tpl->Set('USTPRUEFUNG',"EU-Lieferung mit Pruefung<br>Brief bestellt: $datum");
      }
      else{
        $this->app->Tpl->Set('USTPRUEFUNG', "Pruefung notwendig! (<a href=\"index.php?module=adresse&action=ustprf&id=$adresse\">Starten</a>)");
      }
    }
    else {
      if($ust_ok == 1) {
        $this->app->Tpl->Set('USTPRUEFUNG', 'Freigabe Export (Drittland)');
      }
      else{
        $this->app->Tpl->Set('USTPRUEFUNG', 'Fehlende Freigabe Export!');
      }
    }

    $versandart = $orderRow['versandart'];// $this->app->DB->Select("SELECT versandart FROM auftrag WHERE id='$id' LIMIT 1");
    if($this->app->Secure->GetPOST('versandart')!='') {
      $versandart = $this->app->Secure->GetPOST('versandart');
    }
    $this->app->Tpl->Set('PACKSTATION','none');
    if($versandart==='packstation') {
      $this->app->Tpl->Set('PACKSTATION','');
    }

    $abweichendelieferadresse=  $orderRow['abweichendelieferadresse'];//$this->app->DB->Select("SELECT abweichendelieferadresse FROM auftrag WHERE id='$id' LIMIT 1");
    if($this->app->Secure->GetPOST('abweichendelieferadresse')!='') {
      $versandart = $this->app->Secure->GetPOST('abweichendelieferadresse');
    }
    $this->app->Tpl->Set('ABWEICHENDELIEFERADRESSESTYLE','none');
    if($abweichendelieferadresse=='1') {
      $this->app->Tpl->Set('ABWEICHENDELIEFERADRESSESTYLE','');
    }
    if($id && $status !== 'storniert'
      && $auftragsnummer && $this->app->erp->RechteVorhanden('auftrag','paypal')
      && false !== $this->app->erp->SendPaypalFromAuftrag($id, true)
    ) {
      $this->app->Tpl->Add(
        'BUTTON_UEBERNEHMEN',
        '<a href="index.php?module=auftrag&action=paypal&id='
        .$id.'"><input type="button" value="PayPal Rechnung senden"></a>'
      );
    }

    $this->app->Tpl->Set('AKTIV_TAB1','selected');
    $sollExtSoll = $this->app->DB->SelectRow(
      sprintf(
        "SELECT extsoll, gesamtsumme 
        FROM auftrag 
        WHERE id = %d AND schreibschutz = 0 AND status = 'abgeschlossen' AND extsoll <> 0",
        $id
      )
    );
    if(!empty($sollExtSoll['extsoll']) && $sollExtSoll['extsoll'] == $sollExtSoll['gesamtsumme']) {
      $sollExtSoll['gesamtsumme'] = $this->app->DB->Select(
        sprintf(
          'SELECT ROUND(SUM(`umsatz_brutto_gesamt`),2) FROM `auftrag_position` WHERE `auftrag` = %d ',
          $id
        )
      );
    }
    if(!empty($sollExtSoll) && $sollExtSoll['gesamtsumme'] != $sollExtSoll['extsoll']) {
      $extsoll = $sollExtSoll['extsoll'];
      $this->app->Tpl->Add(
        'MESSAGE',
        '<form method="post"><div class="error">
Die Gesamtsumme stimmt nicht mehr mit urspr&uuml;nglich festgelegten Betrag '.
        number_format($extsoll,2,',','.').
        ' &uuml;berein <input type="submit" name="resetextsoll" value="Festgeschriebene Summe zur&uuml;cksetzen" /></div></form>'
      );
    }
    parent::AuftragEdit();
    $this->app->erp->CheckBearbeiter($id,'auftrag');
    $this->app->erp->CheckVertrieb($id,'auftrag');

    if($this->app->Secure->GetPOST('speichern')!='' && $storno==''){
      $msg = $this->app->Secure->GetGET('msg');
      if($this->app->Secure->GetGET('msg')==''){
        $msg .= $this->app->Tpl->Get('MESSAGE').' ';
        $msg = $this->app->erp->base64_url_encode($msg);
      }

      $this->app->Location->execute(
        "index.php?module=auftrag&action=edit&id=$id&msg=$msg"
        .($this->app->Secure->GetGET('msgid')?'&msgid='.(int)$this->app->Secure->GetGET('msgid'):'')
      );
    }
    if ($this->app->Secure->GetPOST('speichern')!='' && $storno==='abschluss'){
      //header("Location: index.php?module=stornierungen&action=list");
      $this->app->ExitXentral();
    }


    if($this->app->Secure->GetPOST('weiter')!='') {
      $this->app->Location->execute("index.php?module=auftrag&action=positionen&id=$id");
    }

    $this->AuftragMenu();
  }

  function AuftragUstStart()
  {
    $frame = $this->app->Secure->GetGET("frame");

    if($frame=="false")
    {
      // hier nur fenster größe anpassen
      $this->app->YUI->IframeDialog(600,320);
    } else {
      $this->app->BuildNavigation=false;
    }
  }


  function AuftragCreate()
  {
    //$this->app->Tpl->Add(KURZUEBERSCHRIFT,"Auftrag");
    $this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $anlegen = $this->app->Secure->GetGET("anlegen");

    if($this->app->erp->Firmendaten("schnellanlegen")=="1" && $anlegen!="1")
    {
      $this->app->Location->execute("index.php?module=auftrag&action=create&anlegen=1");
    }

    if($anlegen != '')
    {
      $id = $this->app->erp->CreateAuftrag();
      $deactivateautoshipping = $this->app->DB->Select(
        sprintf(
          'SELECT pr.deactivateautoshipping 
          FROM auftrag AS o 
          INNER JOIN projekt AS pr ON o.projekt = pr.id 
          WHERE o.id = %d',
          $id
        )
      );
      if($deactivateautoshipping) {
        $this->app->DB->Update(
          sprintf(
            'UPDATE auftrag SET autoversand = 0 WHERE id = %d',
            $id
          )
        );
      }
      $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$id");
    }

    $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">M&ouml;chten Sie ein Auftrag jetzt anlegen? &nbsp;
        <input type=\"button\" onclick=\"window.location.href='index.php?module=auftrag&action=create&anlegen=1'\" value=\"Ja - Auftrag jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set('TAB1',"
        <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><b style=\"font-size: 14pt\">Auftr&auml;ge in Bearbeitung</b>
        <br>
        <br>
        Offene Auftr&auml;ge, die durch andere Mitarbeiter in Bearbeitung sind.
        <br>
        </td>
        </tr>  
        </table>
        <br> 
        [AUFTRAGE]");


    $this->app->Tpl->Set('AKTIV_TAB1',"selected");

    $this->app->YUI->TableSearch('AUFTRAGE',"auftraegeinbearbeitung");

    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }




  function AuftragReservieren()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->AuftragEinzelnBerechnen($id,true);
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Artilel f&uuml;r diesen Auftrag reserviert!</div>  ");

    $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$id&msg=$msg");
  }

  /**
   * @param int $kommissionierungId
   */
  public function updateCase($kommissionierungId)
  {
    if($kommissionierungId <= 0) {
      return;
    }
    $deliveryNotes = $this->app->DB->SelectArr(
      sprintf(
        'SELECT id, kiste FROM lieferschein WHERE kommissionierung = %d ORDER BY id',
        $kommissionierungId
      )
    );
    if(empty($deliveryNotes)) {
      return;
    }

    $kiste = 0;
    foreach($deliveryNotes as $deliveryNote) {
      $kiste++;
      if($deliveryNote['kiste'] != $kiste) {
        $this->app->DB->Update(
          sprintf(
            'UPDATE lieferschein SET kiste = %d WHERE id = %d',
            $kiste, $deliveryNote['id']
          )
        );
      }
    }
  }

  /**
   * @param array $orders
   * @param int   $projectId
   * @param int   $cronjobCommissionId
   * @param int   $cronjobId
   * @param int   $commissionId
   *
   * @return int
   */
  public function sendOrders($orders, $projectId, $cronjobCommissionId, $cronjobId = 0, $commissionId = 0)
  {
    $return = 0;
    if(empty($orders) || !is_array($orders)) {
      return $return;
    }

    $commissionName = empty($cronjobCommissionId)?'': $this->app->DB->real_escape_string(
      $this->app->DB->Select(
        sprintf(
          'SELECT `bezeichnung` FROM `cronjob_kommissionierung` WHERE `id` = %d',
          $cronjobCommissionId
        )
      )
    );
    $this->kommissionierung = $commissionId > 0
      ? $commissionId
      : $this->app->erp->GetNextKommissionierung($commissionName);
    foreach($orders as $auftrag) {
      if(
      $auftragRow = $this->app->DB->SelectRow(
        sprintf(
          "SELECT a.id, a.belegnr 
          FROM auftrag AS a 
          WHERE (cronjobkommissionierung = '$cronjobCommissionId' OR 0 = '$cronjobCommissionId') 
            AND a.id != '' 
            AND (a.belegnr!=0 OR a.belegnr!='') AND a.status='freigegeben' AND a.inbearbeitung=0 
            AND a.nachlieferung!='1' AND a.autoversand='1'  AND a.liefertermin_ok='1' AND kreditlimit_ok='1' 
            AND liefersperre_ok='1'
            AND a.vorkasse_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' 
            AND a.id = %d
          GROUP BY a.id 
          ORDER by a.id",
          $auftrag
        )
      )
      ) {
        if($cronjobId > 0){
          $this->app->erp->ProzessstarterStatus(
            'Auftrag Versand Auftrag: ' . $auftragRow['belegnr'], $cronjobId
          );
        }
        //$this->app->erp->AuftragEinzelnBerechnen($auftrag);
        $this->app->DB->Update(
          "UPDATE prozessstarter 
          SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() 
          WHERE (parameter = 'autoversand_standard' OR parameter = 'autoversand_manuell') AND aktiv = 1"
        );
        $erg = null;
        $this->app->erp->RunHook('VorAutoversand', 1, $auftrag);
        if(
        $this->app->DB->Select(
          sprintf(
            "SELECT a.id 
            FROM auftrag AS a 
            WHERE a.id = %d AND (a.belegnr!=0 OR a.belegnr!='') AND a.status='freigegeben' 
              AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.autoversand='1'  AND a.liefertermin_ok='1' 
              AND kreditlimit_ok='1' AND liefersperre_ok='1'
              AND a.vorkasse_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1'      
            GROUP BY a.id",
            $auftrag
          )
        )
        ) {
          $this->AuftragVersand($auftrag, false, $erg, true);
          $return++;
        }
        $this->app->DB->Update(
          sprintf(
            'UPDATE auftrag SET cronjobkommissionierung = 0 WHERE id = %d LIMIT 1',
            $auftrag
          )
        );
      }
    }

    if(
      empty($projectId)
      || empty($this->kommissionierung)
      || !$this->app->DB->Select(
        sprintf(
          'SELECT `id` FROM `lieferschein` WHERE `kommissionierung` = %d LIMIT 1',
          $this->kommissionierung
        )
      )
    ) {
      return $return;
    }

    $kommissionierlistestufe1 = $this->app->erp->Projektdaten($projectId, 'kommissionierlistestufe1');
    if($kommissionierlistestufe1) {
      $druckercode = $this->app->erp->Projektdaten($projectId, 'druckerlogistikstufe1');
      if($druckercode <=0) {
        $druckercode = $this->app->erp->Firmendaten('standardversanddrucker');
      }
      $kommissionierlistestufe1menge = $this->app->erp->Projektdaten(
        $projectId, 'kommissionierlistestufe1menge'
      );
      if($kommissionierlistestufe1menge < 1) {
        $kommissionierlistestufe1menge = 1;
      }
      /** @var Kommissionierlauf $obj2 */
      $obj2 = $this->app->erp->LoadModul('kommissionierlauf');
      if($obj2 && $this->kommissionierung) {
        if($cronjobId > 0){
          $this->app->erp->ProzessstarterStatus(
            'KommissionierlaufPDF: ' . $this->kommissionierung, $cronjobId
          );
        }
        $tmpfile = $obj2->KommissionierlaufPDF($this->kommissionierung);
        for($mengedruck=$kommissionierlistestufe1menge;$mengedruck > 0;$mengedruck--) {
          $this->app->printer->Drucken($druckercode,$tmpfile);
        }
        unlink($tmpfile);
      }
    }

    return $return;
  }

  public function AuftragVersand($id='', $ignoriereliefertermin = false, &$ergebnis = null, $paketmarkedrucken = false)
  {
    if(!$this->kommissionierung)
    {
      $this->kommissionierung = $this->app->erp->GetNextKommissionierung();
    }
    // mit der funktionen koennen nur erstauftraege abgewickelt koennen!!!
    $internmodus = 0;
    if($id!='')
    {
      $internmodus=1;
    }
    if($id==''){
      $id = $this->app->Secure->GetGET('id');
    }
    $cmd = $this->app->Secure->GetGET("cmd");
    if($cmd=="manually" && $id > 0)
    {
      $this->app->DB->Update(sprintf("UPDATE auftrag SET autoversand=1 WHERE id=%d AND status='freigegeben' LIMIT 1",$id));
    }

    @ignore_user_abort(true);
    @set_time_limit(0);
    // Prozess haengt so lange bis der nächste frei ist
    $fp = $this->app->erp->ProzessLock('auftrag_autoversand');
    $posids = $this->app->Secure->GetGET('posids');
    if($posids)
    {
      $positionen = null;
      $zwischenpositionen = null;
      if(strpos($posids, 'z') !== false || strpos($posids, 'b') !== false)
      {
        $sida = explode(',',$posids);
        foreach($sida as $v)
        {
          if(strpos($v, 'b') === 0)
          {
            $v = substr($v ,1);
            $positionen[] = $v;
          }else{
            $v = substr($v ,1);
            $zwischenpositionen[] = $v;
          }
        }
      }
    }
    $this->app->erp->AuftragEinzelnBerechnen($id);
    // artikel reservieren
    $adresse = 0;
    $versandart = '';
    $projekt = 0;
    $belegnr = '';
    $tmpname = '';
    $keinetrackingmail = 0;
    $usereditid = 0;
    $auftrag = $this->app->DB->SelectArr("SELECT * FROM auftrag WHERE id='$id' LIMIT 1");
    if(!empty($auftrag)){
      $adresse = $auftrag[0]['adresse'];
      $versandart = $auftrag[0]['versandart'];
      $projekt = (int)$auftrag[0]['projekt'];
      $belegnr = $auftrag[0]['belegnr'];
      $tmpname = $auftrag[0]['name'];
      $keinetrackingmail = $auftrag[0]['keinetrackingmail'];
      $usereditid = $auftrag[0]['usereditid'];//$this->app->DB->Select("SELECT usereditid FROM auftrag WHERE id='$id' LIMIT 1");
    }
    $useredittimestamp = $this->app->DB->Select("SELECT TIME_TO_SEC(TIMEDIFF(NOW(), useredittimestamp)) FROM auftrag WHERE id='$id' LIMIT 1");

    $projektarr = null;
    if($projekt > 0){
      $projektarr = $this->app->DB->SelectRow("SELECT * FROM projekt WHERE id='$projekt' LIMIT 1");
    }
    if(!empty($projektarr)){
      $kommissionierverfahren = $projektarr['kommissionierverfahren'];//$this->app->DB->Select("SELECT kommissionierverfahren FROM projekt WHERE id='$projekt' LIMIT 1");
    }else{
      $kommissionierverfahren = '';
    }
    if($usereditid == $this->app->User->GetID())
    {
      $useredittimestamp = 1000;
    }

    $anzahl_artikel = $this->app->DB->Select("SELECT id FROM auftrag_position WHERE auftrag=$id LIMIT 1");
    if($anzahl_artikel <= 0)
    {
      $meldung = "Auftrag $belegnr kann nicht weitergefuehrt werden, da keine Artikel gebucht sind!";
      $this->app->erp->EventMitSystemLog($this->app->User->GetID(), $meldung, -1,'', 'alert', 1);
    }
    $ret = false;
    $zusatzcheck = true;
    $this->app->erp->RunHook('AuftragVersandZusatzcheck', 2, $id, $zusatzcheck);
    if(($auftrag[0]['status']==='freigegeben' && $auftrag[0]['nachlieferung']=='0'
        && $auftrag[0]['lager_ok']=='1'&&$auftrag[0]['porto_ok']=='1'&&$auftrag[0]['ust_ok']=='1'
        && $auftrag[0]['vorkasse_ok']=='1'&&$auftrag[0]['nachnahme_ok']=='1' &&($auftrag[0]['liefertermin_ok']=='1' || $ignoriereliefertermin)
        && $auftrag[0]['check_ok']=='1' && $auftrag[0]['autoversand']=='1'
        && $auftrag[0]['kreditlimit_ok']=='1' && $auftrag[0]['liefersperre_ok']=='1' && ($useredittimestamp > 45 || $useredittimestamp <= 0 || $internmodus)
        && $anzahl_artikel >=1) && $zusatzcheck)
    {
      // Start
      $ret = true;

      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO auftrag_protokoll (auftrag, zeit, bearbeiter, grund) VALUES 
          (%d,now(),'%s','Lieferschein an Versandzentrum &uuml;bergeben')",
          (int)$id,(isset($this->app->User)?$this->app->DB->real_escape_string($this->app->User->GetName()):'Cronjob')
        )
      );

      $this->app->erp->Protokoll("WeiterfuehrenAuftrag AB $belegnr Art: ".$auftrag[0]['art']);
      // pruefe ob es lagerartikel gibt
      /*$summe_lagerartikel = $this->app->DB->Select("SELECT SUM(ap.id) FROM auftrag_position ap,
          artikel a WHERE ap.auftrag='$id'  AND a.id=ap.artikel AND a.lagerartikel='1'");*/

      $nurRestmenge = false;
      //if($summe_lagerartikel >0 || $auftrag[0][art]=="rma")
      //TODO wenn nur dienstleistung keinen lieferschein
      if($auftrag[0]['art']==='lieferung' || $auftrag[0]['art']==='standardauftrag' || $auftrag[0]['art']=='')
      {
        if($posids) {
          $lieferschein = $this->app->erp->WeiterfuehrenAuftragZuLieferschein($id, $positionen, $zwischenpositionen);
          $this->app->erp->Protokoll('WeiterfuehrenAuftragZuLieferschein AB '.$belegnr);
        }
        else {
          $lieferschein = $this->app->DB->Select(
            sprintf(
              "SELECT dn.id 
              FROM lieferschein AS dn
              WHERE dn.auftragid = %d AND dn.status <> 'storniert'
              LIMIT 1",
              $id
            )
          );
          if(empty($lieferschein)) {
            $lieferschein = $this->app->erp->WeiterfuehrenAuftragZuLieferschein($id);
            $this->app->erp->Protokoll('WeiterfuehrenAuftragZuLieferschein AB ' . $belegnr);
          }
          else {
            $nurRestmenge = true;
          }
        }

        $ls_belegnr = (string)$this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
        if($ls_belegnr==='' || $ls_belegnr==='0') {
          $ls_belegnr = $this->app->erp->GetNextNummer('lieferschein',$projekt,$lieferschein);
        }

        $this->app->DB->Update("UPDATE lieferschein SET 
            belegnr='$ls_belegnr', status='freigegeben', versand='".$this->app->User->GetDescription()."' 
            WHERE id='$lieferschein' LIMIT 1");

        $this->app->erp->LieferscheinProtokoll($lieferschein, 'Lieferschein freigegeben');

        if(!($kommissionierverfahren==='lieferscheinlager' ||
          $kommissionierverfahren==='lieferscheinlagerscan' ||
          $kommissionierverfahren==='lieferschein')){
          $this->app->erp->PDFArchivieren('lieferschein', $lieferschein);
        }
        $etiketten_positionen = 0;
        $etiketten_art = '';
        $etiketten_drucker = 0;
        $etiketten_sort = 0;
        if(!empty($projektarr))
        {
          $etiketten_positionen = $projektarr['etiketten_positionen'];//$this->app->DB->Select("SELECT etiketten_positionen FROM projekt WHERE id='$projekt' LIMIT 1");
          $etiketten_art = $projektarr['etiketten_art'];//$this->app->DB->Select("SELECT etiketten_art FROM projekt WHERE id='$projekt' LIMIT 1");
          $etiketten_drucker = $projektarr['etiketten_drucker'];//$this->app->DB->Select("SELECT etiketten_drucker FROM projekt WHERE id='$projekt' LIMIT 1");
          $etiketten_sort= $projektarr['etiketten_sort'];//$this->app->DB->Select("SELECT etiketten_drucker FROM projekt WHERE id='$projekt' LIMIT 1");
        }
        if($etiketten_positionen > 0)
        {
          $this->app->erp->LieferscheinPositionenDrucken($lieferschein,$etiketten_drucker,$etiketten_art,$etiketten_sort);
        }
      } else {
        // sonst ist lieferschein = 0
        $lieferschein = 0;
      }

      // rechnung  immer außer es ist beistellung bzw. kostenlose lieferung
      $rechnung = 0;
      if(($auftrag[0]['art']==='rechnung' || $auftrag[0]['art']==='standardauftrag' || $auftrag[0]['art']=='') && !$this->app->erp->Projektdaten($projekt,'rechnungerzeugen'))
      {
        // nur erzeugen wenn positionen betrag hpoch genug ist
        $artikelarrsumme = $this->app->DB->Select("SELECT SUM(preis*menge) FROM auftrag_position WHERE auftrag='$id' AND auftrag > 0");
        $this->app->erp->Protokoll("WeiterfuehrenAuftragZuRechnung AB $belegnr Preis ".$artikelarrsumme);

        // wenn mindestesten zwei verschiedene steuersaetze und rechnung 0 ist dann muss man auch erzeugen
        $anzahlsteuer = $this->app->DB->Select("Select sum(t.summe) FROM (
          (SELECT count(distinct 1) as summe FROM auftrag_position WHERE umsatzsteuer = 'ermaessigt' AND (isnull(steuersatz) OR steuersatz < 0) AND auftrag = '$id' LIMIT 1)
          union all (SELECT count(distinct 1) as summe FROM auftrag_position WHERE umsatzsteuer = 'befreit' AND (isnull(steuersatz) OR steuersatz < 0) AND auftrag = '$id' LIMIT 1)
          union all (SELECT count(distinct 1) as summe FROM auftrag_position WHERE umsatzsteuer <> 'ermaessigt' AND (isnull(steuersatz) OR steuersatz < 0) AND auftrag = '$id' LIMIT 1)
          union all (SELECT count(distinct 1) as summe FROM auftrag_position WHERE steuersatz >= 0 and not isnull(steuersatz) AND auftrag = '$id' LIMIT 1) )t");

        if($artikelarrsumme>=0.01 || $anzahlsteuer > 1)
        {
          // versand erzeugen (RE + LS) und verlinken und wenn vorkasse auftrag geld als bezahlt markieren in rechnung
          if($posids) {

            $rechnung = $this->app->erp->WeiterfuehrenAuftragZuRechnung($id, $positionen, $zwischenpositionen);
          }
          else{
            $rechnung = $this->app->DB->Select(
              sprintf(
                "SELECT i.id FROM rechnung AS i WHERE auftragid = %d AND status <> 'storniert' LIMIT 1",
                $id
              )
            );
            if(empty($rechnung)){
              $rechnung = $this->app->erp->WeiterfuehrenAuftragZuRechnung($id);
            }
          }
          $this->app->DB->Update("UPDATE rechnung SET lieferschein='$lieferschein' WHERE id='$rechnung' LIMIT 1");

          $re_belegnr = (string)$this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$rechnung' LIMIT 1");
          if($re_belegnr==='' || $re_belegnr==='0')
          {
            $re_belegnr = $this->app->erp->GetNextNummer('rechnung',$projekt,$rechnung);
          }
          $this->app->erp->Protokoll("WeiterfuehrenAuftragZuRechnung AB $belegnr (id $id) RE $re_belegnr (id $rechnung)");

          $this->app->DB->Update(
            sprintf(
              "UPDATE rechnung SET belegnr = '%s' WHERE id = %d AND (belegnr = '' OR belegnr = '0')",
              $re_belegnr, $rechnung
            )
          );
          $this->app->DB->Update(
            sprintf(
              "UPDATE rechnung SET status = 'freigegeben' WHERE status <> 'storniert' AND status <> 'versendet' AND id = %d",
              $rechnung
            )
          );
          $this->app->erp->RechnungProtokoll($rechnung, 'Rechnung freigegeben');

          $this->app->DB->Update(
            sprintf(
              "UPDATE rechnung 
              SET buchhaltung='%s' 
              WHERE id=%d 
              LIMIT 1",
              $this->app->DB->real_escape_string($this->app->User->GetDescription()), $rechnung
            )
          );

          $this->app->erp->ANABREGSNeuberechnen($rechnung,"rechnung");
          $this->app->erp->PDFArchivieren("rechnung",$rechnung);
        }
      }
      // auftrag_position geliefert_menge und geliefert anpassen
      $artikelarr = $this->app->DB->SelectArr(
        sprintf(
          "SELECT ap.id,ap.artikel,ap.menge 
          FROM auftrag_position AS ap 
          INNER JOIN artikel AS art ON ap.artikel = art.id AND art.lagerartikel = 1 
          WHERE ap.auftrag=%d AND ap.auftrag > 0",
          (int)$id
        )
      );

      $cartikelarr = $artikelarr?count($artikelarr):0;
      for($i=0;$i<$cartikelarr; $i++)
      {
        $auftragspositionsid = $artikelarr[$i]['id'];
        $artikel = $artikelarr[$i]['artikel'];
        $menge= $artikelarr[$i]['menge'];
        // lager teile reservieren

        $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE objekt='auftrag' 
            AND parameter='$id' AND artikel='$artikel' ");

        $this->app->DB->Update("UPDATE auftrag_position SET geliefert_menge='$menge', 
              geliefert='1' WHERE id='$auftragspositionsid' LIMIT 1");
      }

      // nur wenn autoversand projekt
      $autoversand_pruefung = '';
      $automailrechnung = '';
      $autodruckrechnungstufe1mail = '';
      if(!empty($projektarr)){
        $autoversand_pruefung = $projektarr['autoversand'];// $this->app->DB->Select("SELECT autoversand FROM projekt WHERE id='$projekt' LIMIT 1");
        $automailrechnung = $projektarr['automailrechnung'];//$this->app->DB->Select("SELECT automailrechnung FROM projekt WHERE id='$projekt' LIMIT 1");
        $autodruckrechnungstufe1mail = $projektarr['autodruckrechnungstufe1mail'];//$this->app->DB->Select("SELECT autodruckrechnungstufe1mail FROM projekt WHERE id='$projekt' LIMIT 1");
      }

      $druckercode = $this->app->erp->Firmendaten('standardversanddrucker');


      $this->app->erp->Protokoll("WeiterfuehrenAuftragZuRechnung AB $belegnr Kommissionierverfahren: $kommissionierverfahren Projekt $projekt");

      switch($kommissionierverfahren)
      {
        case 'rechnungsmail':
          // rechnung per mail versenden????
          if($automailrechnung && $rechnung > 0)
          {
            // rechnung per mail versenden
            // sende 
            // $this->app->erp->Rechnungsmail($rechnung);
          }
          $this->app->DB->Insert(
            sprintf(
              "INSERT INTO auftrag_protokoll (auftrag, zeit, bearbeiter, grund) VALUES 
          (%d,now(),'%s','Autoversand ausgef&uuml;hrt')",
              (int)$id,(isset($this->app->User)?$this->app->DB->real_escape_string($this->app->User->GetName()):'Cronjob')
            )
          );
          break;
        default:

          if($kommissionierverfahren==='lieferschein' && $lieferschein > 0)
          {
            //FALL 1 Lieferschein mit Lagerplatz
            if($this->kommissionierung){
              $this->app->DB->Update(
                sprintf(
                  "UPDATE lieferschein SET kommissionierung = %d WHERE id = %d LIMIT 1",
                  $this->kommissionierung, $lieferschein
                )
              );
              $this->updateCase($this->kommissionierung);
            }
            $this->app->erp->LieferscheinAuslagern(
              $lieferschein,
              true,
              (int)$this->app->DB->Select(sprintf('SELECT standardlager FROM auftrag WHERE id = %d LIMIT 1', $id)),
              'lieferschein',
              true,
              false,
              $nurRestmenge
            );
            
            // Prozesse ohne Versandzentrum


            $this->app->erp->BriefpapierHintergrundDisable($druckercode);

            $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
            if(class_exists('LieferscheinPDFCustom')) {
              $Brief = new LieferscheinPDFCustom($this->app,$projekt);
            }
            else{
              $Brief = new LieferscheinPDF($this->app,$projekt);
            }
            $Brief->GetLieferschein($lieferschein);
            $tmpfile = $Brief->displayTMP();
            $Brief->ArchiviereDocument(false, true);
            unlink($tmpfile);
            $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
            
            if(class_exists('LieferscheinPDFCustom')) {
              $Brief = new LieferscheinPDFCustom($this->app,$projekt);
            }
            else{
              $Brief = new LieferscheinPDF($this->app,$projekt);
            }
            $Brief->GetLieferschein($lieferschein);
            $tmpfile = $Brief->displayTMP();
            $Brief->ArchiviereDocument(false, true);
            //$this->app->printer->Drucken($druckercode,$tmpfile);

            $fileid_lieferschein = $this->app->erp->CreateDatei($Brief->filename,'lieferschein','','',$tmpfile,$this->app->User->GetName());
            $this->app->erp->AddDateiStichwort($fileid_lieferschein,'lieferschein','lieferschein',$lieferschein,$without_log=false);

            $sprache = $this->app->DB->Select("SELECT sprache FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
            if($sprache=='')
            {
              $sprache='deutsch';
            }
            $text = $this->app->erp->GetGeschaeftsBriefText('Lieferschein',$sprache,$projekt);
            $betreff = $this->app->erp->GetGeschaeftsBriefBetreff('Lieferschein',$sprache,$projekt);
            if($betreff=='')
            {
              $betreff='Mitgesendet bei Lieferung';
            }

            $this->app->DB->Update("UPDATE lieferschein SET status='versendet',versendet='1',schreibschutz='1' WHERE id='$lieferschein' LIMIT 1");
            $this->app->DB->Insert("INSERT INTO dokumente_send 
                (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,dateiid) VALUES ('','lieferschein',NOW(),'".$this->app->User->GetName()."',
                  '$adresse','$lieferschein','versand','$betreff','$text','$projekt','','$fileid_lieferschein')");
            $this->app->erp->LieferscheinProtokoll($lieferschein,'Lieferschein versendet (Auto-Versand)');

            unlink($tmpfile);
            // Druck Auftrag Anhang wenn aktiv
            if(1)//if($this->app->erp->Projektdaten($projekt,"autodruckanhang")=="1")
            {
              // alle anhaenge drucken! wo auftrag datei anhang
              $this->app->erp->GetDateiSubjektObjekt('anhang','Auftrag',$id);
              //                                                                     for($i=0;$i<count($tmpanhang);$i++)
              //                                                                     $this->app->printer->Drucken($druckercode,$tmpanhang[$i]);
              $tmpanhang ='';
            }
            $this->app->DB->Insert(
              sprintf(
                "INSERT INTO auftrag_protokoll (auftrag, zeit, bearbeiter, grund) VALUES 
          (%d,now(),'%s','Auftrag an Versandzentrum &uuml;bergeben')",
                (int)$id,(isset($this->app->User)?$this->app->DB->real_escape_string($this->app->User->GetName()):'Cronjob')
              )
            );
          }


          //FALL 2 // logistikzentrum

          // auftrag_position geliefert_menge und geliefert anpassen
          $artikelarr = $this->app->DB->SelectArr(
            sprintf(
              "SELECT ap.id, ap.artikel,ap.menge 
              FROM auftrag_position AS ap
              INNER JOIN artikel AS art ON ap.artikel = art.id AND art.lagerartikel = 1
              WHERE ap.auftrag=%d AND ap.auftrag > 0",
              (int)$id
            )
          );
          $cartikelarr = $artikelarr?count($artikelarr):0;
          for($i=0;$i<$cartikelarr; $i++) {
            $auftragspositionsid = $artikelarr[$i]['id'];
            $artikel = $artikelarr[$i]['artikel'];
            $menge= $artikelarr[$i]['menge'];
            // lager teile reservieren

            $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE objekt='auftrag' 
                AND parameter='$id' AND artikel='$artikel' ");

            if($kommissionierverfahren==='zweistufig' && $lieferschein > 0)
            {
              $this->app->DB->Insert("INSERT INTO lager_reserviert (id,adresse,artikel,menge,grund,projekt,
                firma,bearbeiter,datum,objekt,parameter)
                  VALUES('','$adresse','$artikel','$menge','Versand f&uuml;r Auftrag $belegnr','$projekt',
                    '".$this->app->User->GetFirma()."','".$this->app->User->GetName()."','9999-01-01','lieferschein','$lieferschein')");
            }

            if($lieferschein > 0) {
              $this->app->DB->Update("UPDATE auftrag_position SET geliefert_menge='$menge', 
              geliefert='1' WHERE id='$auftragspositionsid' LIMIT 1");
            }

          }
          //ende

          $this->app->DB->Insert(
            sprintf(
              "INSERT INTO auftrag_protokoll (auftrag, zeit, bearbeiter, grund) VALUES 
            (%d,now(),'%s','Autoversand ausgef&uuml;hrt')",
              (int)$id,(isset($this->app->User)?$this->app->DB->real_escape_string($this->app->User->GetName()):'Cronjob')
            )
          );
      }
      $autodruckrechnungstufe1 = 0;
      $autodruckrechnungstufe1menge = 0;
      $exportdruckrechnungstufe1 = 0;
      $printOrderQuantity = 0;
      if(!empty($projektarr))
      {
        $autodruckrechnungstufe1 = $projektarr['autodruckrechnungstufe1'];
        $autodruckrechnungstufe1menge = $projektarr['autodruckrechnungstufe1menge'];
        $exportdruckrechnungstufe1 = $projektarr['exportdruckrechnungstufe1'];
        if($projektarr['auftragdrucken'] == '1') {
          $printOrderQuantity = $projektarr['auftragdruckenmenge'] > 1 ? $projektarr['auftragdruckenmenge'] : 1;
        }
      }

      if($exportdruckrechnungstufe1)
      {
        if(!empty($projektarr))
        {
          $exportdruckrechnungstufe1menge = $projektarr['exportdruckrechnungstufe1menge'];//$this->app->DB->Select("SELECT exportdruckrechnungstufe1menge FROM projekt WHERE id='$projekt' LIMIT 1");
        }else{
          $exportdruckrechnungstufe1menge = 0;
        }

        $exportland = $this->app->DB->Select("SELECT if(abweichendelieferadresse = 1 AND lieferland <> '',lieferland, land) FROM auftrag WHERE id = '$id' LIMIT 1");
        $exportdruckrechnungstufe1 = $this->app->erp->Export($exportland);
      }



    
      if(($autodruckrechnungstufe1=='1' || $exportdruckrechnungstufe1) && $rechnung > 0)
      {
        $this->app->DB->Update("UPDATE rechnung SET status='versendet', versendet='1',schreibschutz='1' WHERE id='$rechnung' LIMIT 1");
        $druckercode = $this->app->erp->Projektdaten($projekt,'druckerlogistikstufe1');
        $this->app->erp->BriefpapierHintergrundDisable($druckercode);
        if(class_exists('RechnungPDFCustom'))
        {
          $Brief = new RechnungPDFCustom($this->app,$projekt);
        }else{
          $Brief = new RechnungPDF($this->app,$projekt);
        }
        $Brief->GetRechnung($rechnung);
        $tmpfile = $Brief->displayTMP();
        $Brief->ArchiviereDocument();
        if($autodruckrechnungstufe1=='1')
        {
          for($imenge=0;$imenge<$autodruckrechnungstufe1menge;$imenge++) {
            $this->app->printer->Drucken($druckercode, $tmpfile);
          }
        }
        if($exportdruckrechnungstufe1 == '1')
        {
          for($imenge=0;$imenge<$exportdruckrechnungstufe1menge;$imenge++) {
            $this->app->printer->Drucken($druckercode, $tmpfile);
          }
        }
        unlink($tmpfile);
        $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
        if(class_exists('RechnungPDFCustom'))
        {
          $Brief = new RechnungPDFCustom($this->app,$projekt);
        }else{
          $Brief = new RechnungPDF($this->app,$projekt);
        }
        $Brief->GetRechnung($rechnung);
        $tmpfile = $Brief->displayTMP();
        $Brief->ArchiviereDocument();
        unlink($tmpfile);
        $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
      }
      
      if($autodruckrechnungstufe1mail && $rechnung > 0)
      {
        $this->app->erp->Rechnungsmail($rechnung);
      }
      
      // auftrag abschliessen
      $this->app->DB->Update("UPDATE auftrag SET status='abgeschlossen',schreibschutz='1' WHERE id='$id' LIMIT 1");
      $this->app->erp->PDFArchivieren('auftrag',$id);

      // auftrag abschliessen und event senden

      $this->app->erp->ProzessUnlock($fp);

      // kundenfreigabe loeschen wenn das im projekt eingestellt ist
      if(!empty($projektarr))
      {
        $checkok = $projektarr['kundenfreigabe_loeschen'];//$this->app->DB->Select("SELECT kundenfreigabe_loeschen FROM projekt WHERE id='$projekt' LIMIT 1");
      }else{
        $checkok = 0;
      }

      if($checkok==1){
        $this->app->DB->Update("UPDATE adresse SET kundenfreigabe='0' WHERE id='$adresse' LIMIT 1");
      }

      if($this->app->erp->ModulVorhanden('produktion') && method_exists($this->app->erp, 'ProduktionEinzelnBerechnen'))
      {
        $produktionen = $this->app->DB->SelectArr("SELECT id FROM produktion WHERE auftragid = '$id'");
        if($produktionen)
        {
          foreach($produktionen as $v)
          {
            $this->app->erp->ProduktionEinzelnBerechnen($v['id']);
          }
        }
      }

      //if($internmodus && $lieferschein) // 2018-10-09 BS ab jetzt immer diese Optionen auswerten - nicht nur wenn es intern ist
      if($lieferschein)
      {
        $paketmarkedruckenprojekt = !empty($projektarr['paketmarkedrucken'])?$projektarr['paketmarkedrucken']:0;
        if($paketmarkedrucken && $paketmarkedruckenprojekt && !$this->app->erp->PaketmarkeDrucken($lieferschein, 'lieferschein'))
        {
          //$this->app->DB->Update("UPDATE auftrag SET schreibschutz = 0, status = 'freigegeben' WHERE id = '$id' LIMIT 1");
          $this->app->erp->AuftragProtokoll($id, 'Paketmarke drucken fehlgeschlagen');
          if(!empty($fp))
          {
            $this->app->erp->ProzessUnlock($fp);
          }
          return $ret;
        }
        $lieferscheinedruckenprojekt = !empty($projektarr['lieferscheinedrucken'])?$projektarr['lieferscheinedrucken']:0;
        if($lieferscheinedruckenprojekt)
        {
          $lieferscheinedruckenmenge = !empty($projektarr['lieferscheinedruckenmenge'])?$projektarr['lieferscheinedruckenmenge']:0;//$this->app->DB->Select("SELECT lieferscheinedruckenmenge FROM projekt WHERE id = '$projekt' LIMIT 1");
          if($lieferscheinedruckenmenge > 0)
          {
            $druckercode = !empty($projektarr['druckerlogistikstufe1'])?$projektarr['druckerlogistikstufe1']:0;//$this->app->DB->Select("SELECT druckerlogistikstufe1 FROM projekt WHERE id='$projekt' LIMIT 1");
            if($druckercode <=0){
              $druckercode = $this->app->erp->Firmendaten('standardversanddrucker');
            }

            $this->app->erp->BriefpapierHintergrundDisable($druckercode);

            if(class_exists('LieferscheinPDFCustom'))
            {
              $Brief = new LieferscheinPDFCustom($this->app,$projekt);
            }else{
              $Brief = new LieferscheinPDF($this->app,$projekt);
            }
            $Brief->GetLieferschein($lieferschein);

            $tmpfile = $Brief->displayTMP();
            $Brief->ArchiviereDocument();

            for($imenge=0;$imenge<$lieferscheinedruckenmenge;$imenge++) {
              $this->app->printer->Drucken($druckercode, $tmpfile);
            }
            unlink($tmpfile);
            $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
            if(class_exists('LieferscheinPDFCustom'))
            {
              $Brief = new LieferscheinPDFCustom($this->app,$projekt);
            }else{
              $Brief = new LieferscheinPDF($this->app,$projekt);
            }
            $Brief->GetLieferschein($lieferschein);

            $tmpfile = $Brief->displayTMP();
            $Brief->ArchiviereDocument();
            unlink($tmpfile);
            $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
          }
        }
      }

      // Druck Auftrag Anhang wenn aktiv
      if($this->app->erp->Projektdaten($projekt,'druckanhang')=='1') {
        $obj = $this->app->erp->LoadModul('versanderzeugen');
        if(!empty($obj) && method_exists($obj,'autoPrintAttachment'))
        {
          $obj->autoPrintAttachment($druckercode,$id,$lieferschein,$rechnung);
        }
      }

      if($printOrderQuantity > 0 && $druckercode) {
        if(class_exists('AuftragPDFCustom'))
        {
          $Brief = new AuftragPDFCustom($this->app,$projekt);
        }else{
          $Brief = new AuftragPDF($this->app,$projekt);
        }
        $Brief->GetAuftrag($id);

        $tmpfile = $Brief->displayTMP();
        for($printOrderQuantity; $printOrderQuantity > 0; $printOrderQuantity--) {
          $this->app->printer->Drucken($druckercode, $tmpfile);
        }
        unlink($tmpfile);
      }

      $this->app->erp->RunHook('auftrag_versand_ende', 1, $id);

      // wenn per URL aufgerufen      
      if($internmodus!='1')
      {
        if(!empty($fp))
        {
          $this->app->erp->ProzessUnlock($fp);
        }
        $this->app->Location->execute("index.php?module=auftrag&action=edit&id=$id&msg=$msg");
      }
      if(!empty($fp))
      {
        $this->app->erp->ProzessUnlock($fp);
      }
      return $ret;
    }

    //$this->app->erp->ProzessUnlock("auftrag_autoversand");
    $this->app->erp->ProzessUnlock($fp);

    if($posids)
    {
      $this->app->Location->execute('index.php?module=auftrag&action=positionen&id='.$id);
    }
    
    // wenn per URL aufgerufen
    if($internmodus!='1')
    {
      //      $this->AuftragList();

      //header("Location: index.php?module=auftrag&action=search");
      if($id > 0){
        $this->app->Location->execute('index.php?module=auftrag&action=edit&id=' . $id);
      }
      $this->app->Location->execute('index.php?module=auftrag&action=versandzentrum');
    }
  }


  function AuftragSelbstabholerNachricht()
  {
    // kann man immer wieder aufrufen wenn ein teilchen gekommen ist bis auftrag voll erfuellt ist

  }

  function AuftragSelbstabholerAbgeholt()
  {
    // kann man immer wieder aufrufen wenn ein teilchen gekommen ist bis auftrag voll erfuellt ist

  }


  function AuftragNachlieferungCheck()
  {

    //echo "pruefe ob eine Nachlieferung gemacht werden kann";

  }


  function AuftragNachlieferung()
  {
    // kann man immer wieder aufrufen wenn ein teilchen gekommen ist bis auftrag voll erfuellt ist

  }




  public function AuftragVerfuegbar()
  {
    $frame = $this->app->Secure->GetGET('frame');
    $id = $this->app->Secure->GetGET('id');
    if($frame=='false')
    {
      // hier nur fenster größe anpassen
      $this->app->YUI->IframeDialog(600,400);
    } else {
      // nach page inhalt des dialogs ausgeben
      $table = new EasyTable($this->app); 
      $table->Query("SELECT ap.nummer, ap.bezeichnung, ap.menge, (SELECT TRIM(SUM(lp.menge))+0 FROM lager_platz_inhalt lp WHERE lp.artikel=ap.artikel) as lager, 
          (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=ap.artikel AND lr.datum>=NOW() AND lr.objekt!='lieferschein') as reserviert, 
          if(((SELECT SUM(lp.menge) FROM lager_platz_inhalt lp WHERE lp.artikel=ap.artikel) - (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=ap.artikel AND lr.datum>=NOW() AND lr.objekt!='lieferschein') - ap.menge)>=0,'',
            TRIM((SELECT SUM(lp.menge) FROM lager_platz_inhalt lp WHERE lp.artikel=ap.artikel) - (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=ap.artikel AND lr.datum>=NOW() AND lr.objekt!='lieferschein') - ap.menge)+0
            ) as fehlend 
          FROM auftrag_position ap LEFT JOIN artikel a ON a.id=ap.artikel WHERE ap.auftrag='$id' AND a.lagerartikel=1");

      $table->DisplayNEW('PAGE','Fehlende','noAction');
      
      $this->app->BuildNavigation=false;
    }
  }

  public function AuftragAmpel($id,$parsetarget)
  {

    $status = $this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");

    if($status=='abgeschlossen' || $status=='storniert')
    {
      $go = '<img src="./themes/new/images/grey.png" width="17" border="0">';
      $stop = '<img src="./themes/new/images/grey.png" width="17" border="0">';
      $reserviert = '<img src="./themes/new/images/grey.png" width="17" border="0">';
      $check = '<img src="./themes/new/images/grey.png" width="17" border="0">';
    } else {

      $go = '<img src="./themes/new/images/go.png" width="17" border="0">';
      $stop = '<img src="./themes/new/images/stop.png" width="17" border="0">';
      $reserviert = '<img src="./themes/new/images/reserviert.png" width="17" border="0">';
      $check = '<img src="./themes/new/images/mail-mark-important.png" width="17" border="0">';

    }

    // offene Auftraege
    $table = new EasyTable($this->app);
    $sql = "SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, if(a.belegnr!='',a.belegnr,'ohne Nummer') as auftrag, a.internet, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.adresse,'\">',a.name,'</a>') as name, a.land, p.abkuerzung as projekt, a.zahlungsweise as per, a.gesamtsumme as soll,"; 
    $subsql = "'0' as ist,";
    $sql .= $subsql. "if(a.check_ok,'','<a href=\"index.php?module=auftrag&action=checkdisplay&id=1031&frame=false\" onclick=\"makeRequest(this); return false;\">$check</a>') as AC, 

        if(a.reserviert_ok,'$reserviert','') as AR, 
        if(a.lager_ok,'$go','$stop') as LA, 
        if(a.porto_ok,'$go','$stop') as PO, 
        if(a.ust_ok,'$go',CONCAT('<a href=\"/index.php?module=adresse&action=ustprf&id=',a.adresse,'\">','$stop','</a>')) as ST, 
        if(a.vorkasse_ok,'$go','$stop') as ZE, 
        if(a.nachnahme_ok,'$go','$stop') as N, 
        if(a.autoversand,'$go','$stop') as A, 
        if(a.liefertermin_ok,'$go','$stop') as LT, 
        a.id
        FROM auftrag a, projekt p WHERE a.inbearbeitung=0 AND p.id=a.projekt AND a.id=$id LIMIT 1";
        
    $table->Query($sql);

    $table->DisplayNew($parsetarget, "
        <a href=\"index.php?module=auftrag&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
        <a onclick=\"if(!confirm('Wirklich stornieren?')) return false; else window.location.href='index.php?module=auftrag&action=delete&id=%value%';\">
        <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
        ");


  }

  public function AuftraguebersichtMenu()
  {
    $backurl = $this->app->Secure->GetGET('backurl');
    $backurl = $this->app->erp->base64_url_decode($backurl);

    $this->app->erp->MenuEintrag('index.php?module=auftrag&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=auftrag&action=create','Neuen Auftrag anlegen');

    if(strlen($backurl)>5){
      $this->app->erp->MenuEintrag("$backurl", 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    else{
      $this->app->erp->MenuEintrag('index.php', 'Zur&uuml;ck zur &Uuml;bersicht');
    }

    $this->app->erp->RunMenuHook('auftrag_list');
  }

  /**
   * @param string $description
   * @param bool   $escaped
   *
   * @return int
   */
  public function createCronjobCommission(string $description = ''): int
  {
    $nextCronjobCommissionId = 1 + (int)$this->app->DB->Select(
        'SELECT MAX(`cronjobkommissionierung`) FROM `auftrag`'
    );

    $this->app->DB->Insert(
      "INSERT INTO `cronjob_kommissionierung` (`id`, `bezeichnung`) 
        VALUES ({$nextCronjobCommissionId}, '{$description}') "
    );
    if($this->app->DB->GetInsertID() > 0) {
      return $this->app->DB->GetInsertID();
    }
    $this->app->DB->Update(
      "UPDATE `cronjob_kommissionierung` 
      SET `bezeichnung` = '{$description}' 
      WHERE `id` = {$nextCronjobCommissionId}"
    );

    return $nextCronjobCommissionId;
  }

  public function AuftragVersandzentrum()
  {
    $this->AuftraguebersichtMenu();
    $targetMessage = 'AUTOVERSANDBERECHNEN';
    $autoshipmentEnabled = true;
    $this->app->erp->RunHook('OrderAutoShipment', 2, $targetMessage, $autoshipmentEnabled);

    $plusCronjobs = $this->app->DB->SelectRow(
      "SELECT art, periode 
        FROM prozessstarter 
        WHERE (parameter = 'autoversand' OR parameter = 'autoversand_plus') AND aktiv = 1
        ORDER BY art = 'periodisch', periode >= 30
        LIMIT 1"
    );

    if(!empty($plusCronjobs) && $plusCronjobs['art'] === 'periodisch' && $plusCronjobs['periode'] < 30) {
      $message = 'Autoversand Cronjob ist mit '.$plusCronjobs['periode'].' Minuten zu kurz eingestellt (mindestens 30).';
      $this->app->Tpl->Add('AUTOVERSANDBERECHNEN', '<div class="warning">'.$message.'</div>');
    }

    // ZAHLUNGSMAIL 
    $zahlungsmail= $this->app->Secure->GetPOST('zahlungsmail');

    if($zahlungsmail!=''){
      $meineauftraege = $this->app->DB->SelectArr("SELECT id FROM auftrag WHERE status='freigegeben' 
          AND vorkasse_ok!='1' AND zahlungsweise!='rechnung' AND zahlungsweise!='nachnahme' AND zahlungsweise!='bar' AND zahlungsweise!='lastschrift'");
      $cmeineauftraege = $meineauftraege?count($meineauftraege):0;
      for($i=0;$i<$cmeineauftraege;$i++) {
        $this->app->erp->AuftragNeuberechnen($meineauftraege[$i]['id']);

        $this->app->erp->AuftragEinzelnBerechnen($meineauftraege[$i]['id']);
        $this->app->erp->AuftragZahlungsmail($meineauftraege[$i]['id']);
      }
    }

    // AUFTAEGE ABSCHLIESSEN!
    $submit = $this->app->Secure->GetPOST('submit');
    $auftraegemarkiert = $this->app->Secure->GetPOST('auftraegemarkiert');
    $entfernen = $this->app->Secure->GetPOST('entfernen');
    $bezeichnung = (string)$this->app->Secure->GetPOST('bezeichnung');
    if($entfernen && $auftraegemarkiert){
      $cauftraegemarkiert = count($auftraegemarkiert);
      for($i=0;$i<$cauftraegemarkiert;$i++) {
        $this->app->DB->Update("UPDATE auftrag SET cronjobkommissionierung = 0 WHERE id = '".$auftraegemarkiert[$i]."' LIMIT 1");
      }
    }

    if($this->app->Secure->GetPOST('ausfuehren')){
      $drucker = $this->app->Secure->GetPOST('seldruckerversand');
      $aktion = $this->app->Secure->GetPOST('auftrag_versandauswahl');
      $auftraegemarkiert = $this->app->Secure->GetPOST('auftraegemarkiert');
      $bezeichnung = (string)$this->app->Secure->GetPOST('bezeichnung');

      $selectedIds = [];
      if(!empty($auftraegemarkiert)) {
        foreach($auftraegemarkiert as $selectedId) {
          $selectedId = (int)$selectedId;
          if($selectedId > 0) {
            $selectedIds[] = $selectedId;
          }
        }
      }
      if($drucker > 0) {
        $this->app->erp->BriefpapierHintergrundDisable($drucker);
      }
      if(is_array($auftraegemarkiert)){

        switch($aktion){
          case 'versandstarten':
            $cronjobActive = $this->app->DB->Select(
              "SELECT ps.id 
              FROM `prozessstarter` AS `ps`
              WHERE ps.aktiv = 1 and (ps.parameter = 'autoversand_standard' OR ps.parameter = 'autoversand_manuell') 
              LIMIT 1"
            );
            $check = $cronjobActive;
            if(!$check){
              $check = $this->app->DB->Select(
                "SELECT id 
                FROM auftrag AS a 
                WHERE  a.id!='' AND (a.belegnr!=0 OR a.belegnr!='') 
                  AND a.status='freigegeben' AND a.autoversand='1' AND a.cronjobkommissionierung > 0
                  AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.vorkasse_ok='1' 
                  AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' 
                  AND a.liefertermin_ok='1' AND kreditlimit_ok='1' AND liefersperre_ok='1' 
                LIMIT 1"
              );
            }

            if(!empty($auftraegemarkiert)){
              $datuma = null;
              foreach ($auftraegemarkiert as $k => $v) {
                $datuma[$k] = $this->app->DB->Select(
                  sprintf(
                    'SELECT datum FROM auftrag WHERE id = %d LIMIT 1',
                    (int)$v
                  )
                );
              }
              //Sortieren nach Datum
              array_multisort($datuma, SORT_ASC, $auftraegemarkiert);
              // aufsteigend sortieren erst die alten IDs
              //sort($auftraegemarkiert);
            }
            if($check){
              $maxcronjobkommissionierung = $this->createCronjobCommission((string)$bezeichnung);

              $cauftraegemarkiert = $auftraegemarkiert ? count($auftraegemarkiert) : 0;
              for ($i = 0; $i < $cauftraegemarkiert; $i++) {
                $this->app->DB->Update(
                  sprintf(
                    'UPDATE `auftrag` 
                    SET `cronjobkommissionierung` = %d
                    WHERE `id` = %d 
                    LIMIT 1',
                    $maxcronjobkommissionierung, $auftraegemarkiert[$i]
                  )
                );
              }
            }
            else {
              $cauftraegemarkiert = $auftraegemarkiert ? count($auftraegemarkiert) : 0;
              for ($i = 0; $i < $cauftraegemarkiert; $i++) {
                $projekt = (int)$this->app->DB->Select(
                  sprintf(
                    'SELECT `projekt` FROM `auftrag` WHERE `id` = %d LIMIT 1',
                    $auftraegemarkiert[$i]
                  )
                );
                $auftraegenachprojekt[$projekt][] = $auftraegemarkiert[$i];
              }

              foreach ($auftraegenachprojekt as $projekt => $auftraege) {
                if(!is_array($auftraege) || empty($auftraege)) {
                  continue;
                }
                $this->kommissionierung = $this->app->erp->GetNextKommissionierung($bezeichnung);
                foreach ($auftraege as $auftrag) {
                  $this->AuftragVersand($auftrag);
                }
                if(empty($this->kommissionierung)) {
                  continue;
                }
                if(
                  empty(
                    $this->app->DB->Select(
                      sprintf(
                        'SELECT `id` FROM `lieferschein` WHERE `kommissionierung` = %d',
                        $this->kommissionierung
                      )
                    )
                  )
                ) {
                  continue;
                }
                $kommissionierlistestufe1 = $this->app->erp->Projektdaten($projekt, 'kommissionierlistestufe1');
                if(empty($kommissionierlistestufe1)) {
                  continue;
                }
                $druckercode = $this->app->DB->Select(
                  sprintf(
                    'SELECT druckerlogistikstufe1 FROM projekt WHERE id= %d LIMIT 1',
                    $projekt
                  )
                );
                if($druckercode <= 0){
                  $druckercode = $this->app->erp->Firmendaten('standardversanddrucker');
                }
                $kommissionierlistestufe1menge = $this->app->erp->Projektdaten(
                  $projekt, 'kommissionierlistestufe1menge'
                );
                if($kommissionierlistestufe1menge < 1){
                  $kommissionierlistestufe1menge = 1;
                }
                /** @var Kommissionierlauf $obj */
                $obj = $this->app->erp->LoadModul('kommissionierlauf');
                if($obj && $this->kommissionierung){
                  $tmpfile = $obj->KommissionierlaufPDF($this->kommissionierung);
                  for ($mengedruck = $kommissionierlistestufe1menge; $mengedruck > 0; $mengedruck--) {
                    $this->app->printer->Drucken($druckercode, $tmpfile);
                  }
                  unlink($tmpfile);
                }
              }
            }
          break;
          case 'drucken':
            if($drucker){
              foreach ($selectedIds as $v) {
                $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$v' LIMIT 1");
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('AuftragPDFCustom')){
                  $Brief = new AuftragPDFCustom($this->app, $projekt);
                }else{
                  $Brief = new AuftragPDF($this->app, $projekt);
                }
                $Brief->GetAuftrag($v);
                $_tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                unlink($_tmpfile);
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('AuftragPDFCustom')){
                  $Brief = new AuftragPDFCustom($this->app, $projekt);
                }else{
                  $Brief = new AuftragPDF($this->app, $projekt);
                }
                $Brief->GetAuftrag($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->printer->Drucken($drucker, $tmpfile);
                $this->app->erp->AuftragProtokoll($v, "Auftrag versendet");
                $this->app->erp->AuftragProtokoll($v, "In Versandübergabe gedruckt");
                unlink($tmpfile);
              }
            }
          break;
        }
      }
    }

    $check = null;
    $cronjobActive = $this->app->DB->Select(
      "SELECT ps.id 
      FROM `prozessstarter` AS `ps` 
      WHERE ps.aktiv = 1 and (ps.parameter = 'autoversand_standard' OR ps.parameter = 'autoversand_manuell') 
      LIMIT 1"
    );
    if(!$cronjobActive) {
      $check = $this->app->DB->Select(
        sprintf(
        "SELECT id 
          FROM auftrag AS a
          WHERE  a.id!='' AND (a.belegnr!=0 OR a.belegnr!='') AND a.status='freigegeben' AND a.autoversand='1' AND 
                a.cronjobkommissionierung > 0
                                     AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.vorkasse_ok='1' AND a.porto_ok='1' 
            AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' AND a.liefertermin_ok='1' AND kreditlimit_ok='1' AND 
                liefersperre_ok='1' 
          LIMIT 1"
        )
      );
    }

    if($check || $cronjobActive) {
      $unversendet = $this->app->DB->Select("SELECT count(a.id) FROM auftrag as a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.id!='' AND (a.belegnr!=0 OR a.belegnr!='') AND a.status='freigegeben' AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.autoversand='1' AND a.cronjobkommissionierung = 0  AND a.liefertermin_ok='1' AND kreditlimit_ok='1' AND liefersperre_ok='1'
                                     AND a.vorkasse_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' " . $this->app->erp->ProjektRechte('p.id', true, 'a.vertriebid'));

      $warteschleife = $this->app->DB->Select("SELECT count(a.id) FROM auftrag as a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.id!='' AND (a.belegnr!=0 OR a.belegnr!='') AND a.status='freigegeben' AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.autoversand='1'  AND a.liefertermin_ok='1' AND kreditlimit_ok='1' AND liefersperre_ok='1'
                                     AND a.vorkasse_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' AND a.cronjobkommissionierung > 0 " . $this->app->erp->ProjektRechte('p.id', true, "a.vertriebid"));
 
      if($unversendet > 0) {
        $unversendet ='('.$unversendet.')';
      }
      else {
        $unversendet='';
      }
      if($warteschleife > 0) {
        $warteschleife ='('.$warteschleife.')';
      }
      else {
        $warteschleife='';
      }

      $this->app->Tpl->Set('TABTEXT2','Unversendet '.$unversendet);
      $this->app->Tpl->Set('TABTEXT3','Warteschleife '.$warteschleife);
      $this->app->YUI->TableSearch('TAB3','auftraegeoffeneautowartend');
      if($warteschleife > 0 && !$cronjobActive) {
        $this->app->Tpl->Add(
          'AUTOVERSANDBERECHNEN',
          '<div class="warning">Der Prozessstarter &quot;Autoversand Manuell&quot; ist deaktivert, 
          es befinden sich aber Auftr&auml;ge in der Warteschlange. 
          Bitte aktieren Sie den Prozessstarter 
          oder entfernen Sie die betreffenden Auftr&auml;ge in der Warteschlange</div>'
        );
      }
    }
    else{
      $this->app->Tpl->Set('VORTABS3UEBERSCHRIFT','<!--');
      $this->app->Tpl->Set('NACHTABS3UEBERSCHRIFT','-->');
    }

    $this->app->Tpl->Set('SELDRUCKERVERSAND', $this->app->erp->GetSelectDrucker($this->app->User->GetParameter('rechnung_list_drucker')));

    if(!$autoshipmentEnabled) {
      $this->app->Tpl->Parse('PAGE','auftraguebersicht.tpl');
      return;
    }
    if($this->app->erp->RechteVorhanden('auftrag','berechnen'))
    {
      $this->app->Tpl->Set('AUTOBERECHNEN','<input type="button" class="btnGreen" value="Auto-Versand berechnen" onclick="window.location.href=\'index.php?module=auftrag&action=berechnen\'">');
    }else{
      $this->app->Tpl->Set('AUTOBERECHNEN2','');
    }
    $infolink = '<a href="https://xentral.biz/helpdesk/kurzanleitung-ablauf-des-versands-von-auftraegen#nav-autoversand-mit-prozessstarter-berechnen" target="_blank">(Information)</a>';
    $last_order_calc = $this->app->erp->GetKonfiguration('last_order_calc');
    if(!empty($last_order_calc)) {
      $this->app->Tpl->Add('AUTOVERSANDBERECHNEN','<div class="info">Die letzte Berechnung der Auftragsampeln war am '.$last_order_calc.'. '.$infolink.' [AUTOBERECHNEN]</div>');
    }
    else{
      $this->app->Tpl->Add('AUTOVERSANDBERECHNEN','<div class="info">Die letzte Berechnung der Auftragsampeln wurde noch nicht ermittelt. '.$infolink.' [AUTOBERECHNEN]</div>');
    }
    $this->app->YUI->TableSearch('TAB2','auftraegeoffeneauto');
    $this->app->Tpl->Parse('PAGE','auftrag_versandzentrum.tpl');
  }


  public function AuftragList()
  {
    if($this->app->Secure->GetPOST('ausfuehren') && $this->app->erp->RechteVorhanden('auftrag', 'edit'))
    {
      $drucker = $this->app->Secure->GetPOST('seldrucker');
      $aktion = $this->app->Secure->GetPOST('sel_aktion');
      $auswahl = $this->app->Secure->GetPOST('auswahl');
      $selectedIds = [];
      if(!empty($auswahl)) {
        foreach($auswahl as $selectedId) {
          $selectedId = (int)$selectedId;
          if($selectedId > 0) {
            $selectedIds[] = $selectedId;
          }
        }
      }
      if($drucker > 0) {
        $this->app->erp->BriefpapierHintergrundDisable($drucker);
      }
      if(is_array($auswahl))
      {
        switch($aktion)
        {
          case 'stapelproduktionweiter':
            $error_artikel = '';
            foreach($selectedIds as $v) {
               $weiteralsproduktion = $this->AuftragProduktion($v,true);
               /** @var Produktion $produktion */
               $produktion = $this->app->erp->LoadModul('produktion');
               $produktion->ProduktionFreigabe($weiteralsproduktion);
               if(!is_int($weiteralsproduktion)){
                 $auftragsnummer = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id = '$v'");
                 $error_artikel .= $auftragsnummer . ', ';
               }
            }
            $error_artikel = rtrim($error_artikel, ', ');
            if($error_artikel != '') {
              $msg = $this->app->erp->base64_url_encode('<div class="error">Der Auftrag kann nicht als Produktion weitergeführt werden, da im Auftrag $error_artikel Artikel nicht als Produktionsartikel markiert sind!</div>');
            }else {
              $msg = $this->app->erp->base64_url_encode('<div class="warning">Die Aufträge wurden als Produktion weitergeführt.</div>');
            }
            $this->app->Location->execute('index.php?module=auftrag&action=list&msg='.$msg);
          break;
          case 'fastlane':
            if(!empty($selectedIds)) {
              $this->app->DB->Update('UPDATE auftrag SET fastlane = 1 WHERE id IN (' . implode(', ', $selectedIds) . ')');
            }
            break;
          case 'fastlaneentfernen':
            if(!empty($selectedIds)) {
              $this->app->DB->Update('UPDATE auftrag SET fastlane = 0 WHERE id IN (' . implode(', ', $selectedIds) . ')');
            }
            break;
          case 'freigeben':
            foreach($selectedIds as $v) {
              if($this->app->DB->Select("SELECT id FROM auftrag WHERE id = '$v' AND belegnr = '' AND status <> 'freigeben' AND status <> 'abgeschlossen' AND status <> 'storniert' LIMIT 1")){
                $this->AuftragFreigabe($v);
              }
            }
          break;
          case 'versandfreigeben':
            if(!empty($selectedIds)) {
              $this->app->DB->Update('UPDATE auftrag SET autoversand = 1 WHERE id IN ('. implode(', ', $selectedIds) . ')');
            }
          break;
          case 'versandentfernen':
            if(!empty($selectedIds)) {
              $this->app->DB->Update('UPDATE auftrag SET autoversand = 0 WHERE id IN ('. implode(', ', $selectedIds) . ')');
            }
          break;
          case 'mail':
            foreach($selectedIds as $v) {
              $auftragarr = $this->app->DB->SelectRow("SELECT email,adresse,projekt,name,sprache FROM auftrag WHERE id = '$v' LIMIT 1");
              $email = '';
              $adresse = 0;
              $projekt = 0;
              $name = '';
              $sprache = '';
              if(!empty($auftragarr)) {
                $email = (string)$auftragarr['email'];//$this->app->DB->Select("SELECT email FROM auftrag WHERE id = '$v' LIMIT 1");
                $adresse = (int)$auftragarr['adresse'];//$this->app->DB->Select("SELECT adresse FROM auftrag WHERE id = '$v' LIMIT 1");
                $projekt = (int)$auftragarr['projekt'];//$this->app->DB->Select("SELECT projekt FROM auftrag WHERE id = '$v' LIMIT 1");
                $name = $auftragarr['name'];// $this->app->DB->Select("SELECT name FROM auftrag WHERE id = '$v' LIMIT 1");
                $sprache = $auftragarr['sprache'];// $this->app->DB->Select("SELECT sprache FROM auftrag WHERE id='$v' LIMIT 1");
              }

              if($sprache=='' || $email === '') {
                $adrArr = $this->app->DB->SelectRow("SELECT sprache, email FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
                if($sprache == '') {
                  $sprache = $adrArr['sprache'];
                }
                if($email === '') {
                  $email = (string)$adrArr['email'];
                }
              }

              if($sprache=='') {
                $sprache='de';
              }

              if($email !== '')
              {
                $emailtext = $this->app->erp->Geschaeftsbriefvorlage($sprache,'auftrag',$projekt,$name,$v);
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('AuftragPDFCustom'))
                {
                  $Brief = new AuftragPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new AuftragPDF($this->app,$projekt);
                }
                $Brief->GetAuftrag($v);
                $_tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                unlink($_tmpfile);
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('AuftragPDFCustom'))
                {
                  $Brief = new AuftragPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new AuftragPDF($this->app,$projekt);
                }
                $Brief->GetAuftrag($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();

                $fileid = $this->app->erp->CreateDatei($Brief->filename,'auftrag','','',$tmpfile,$this->app->User->GetName());
                $this->app->erp->AddDateiStichwort($fileid,'auftrag','auftrag',$v);
                $this->app->erp->DokumentSend($adresse,'auftrag', $v, 'email',$emailtext['betreff'],$emailtext['text'],array($tmpfile),"","",$projekt,$email, $name);
                $ansprechpartner = $name." <".$email.">";
                $this->app->DB->Insert("INSERT INTO dokumente_send
                    (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,versendet,dateiid) VALUES ('','auftrag',NOW(),'".$this->app->DB->real_escape_string($this->app->User->GetName())."',
                      '$adresse','$v','email','".$this->app->DB->real_escape_string($emailtext['betreff'])."','".$this->app->DB->real_escape_string($emailtext['text'])."','$projekt','$ansprechpartner',1,'$fileid')");
                $tmpid = $this->app->DB->GetInsertID();
                unlink($tmpfile);
                $this->app->DB->Update("UPDATE auftrag SET versendet=1, versendet_am=NOW(),
                  versendet_per='email',versendet_durch='".$this->app->DB->real_escape_string($this->app->User->GetName())."',schreibschutz='1' WHERE id='$v' LIMIT 1");
                $this->app->erp->AuftragProtokoll($v,'Auftrag versendet');
              }
            }
          
          break;
          case 'storniert':
            foreach($selectedIds as $v) {
              $orderArr = $this->app->DB->SelectRow(
                sprintf(
                  'SELECT projekt, status FROM auftrag WHERE id= %d',
                  $v
                )
              );
              $projekt = $orderArr['projekt'];
              $status = $orderArr['status'];
              if($status==='angelegt' || $status=='') {
                $this->app->erp->DeleteAuftrag($v);
              }
              else {
                if(class_exists('AuftragPDFCustom')) {
                  $Brief = new AuftragPDFCustom($this->app,$projekt);
                }
                else{
                  $Brief = new AuftragPDF($this->app,$projekt);
                }
                $Brief->GetAuftrag($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->erp->AuftragProtokoll($v,'Auftrag storniert');
                $this->app->DB->Update(
                  sprintf(
                    "UPDATE auftrag 
                    SET status='storniert', schreibschutz=1, versendet = 1 
                    WHERE id = %d AND status!='angelegt' 
                    LIMIT 1",
                    $v
                  )
                );
                unlink($tmpfile);
                $this->app->DB->Delete(
                  sprintf(
                    "DELETE FROM lager_reserviert WHERE parameter = '%d' AND objekt LIKE 'auftrag'",
                    $v
                  )
                );
              }
            }
          break;
          case 'versendet':
            foreach($selectedIds as $v) {
              $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$v' LIMIT 1");
              if(class_exists('AuftragPDFCustom'))
              {
                $Brief = new AuftragPDFCustom($this->app,$projekt);
              }else{
                $Brief = new AuftragPDF($this->app,$projekt);
              }
              $Brief->GetAuftrag($v);
              $tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              $this->app->erp->AuftragProtokoll($v,'Auftrag versendet');
              $this->app->DB->Update("UPDATE auftrag SET schreibschutz=1, versendet = 1 WHERE id = '$v' LIMIT 1");
              $this->app->DB->Update("UPDATE auftrag SET status='versendet' WHERE id = '$v' AND status='freigegeben' LIMIT 1");
              unlink($tmpfile);
            }

          break;

          case 'drucken':
            if($drucker)
            {
              foreach($selectedIds as $v) {
                $auftragsdaten = $this->app->DB->SelectRow("SELECT projekt, adresse FROM auftrag WHERE id='$v' LIMIT 1");
                $projekt = $auftragsdaten['projekt'];
                $adressId = $auftragsdaten['adresse'];
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('AuftragPDFCustom'))
                {
                  $Brief = new AuftragPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new AuftragPDF($this->app,$projekt);
                }
                $Brief->GetAuftrag($v);
                $_tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                unlink($_tmpfile);
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('AuftragPDFCustom'))
                {
                  $Brief = new AuftragPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new AuftragPDF($this->app,$projekt);
                }
                $Brief->GetAuftrag($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->printer->Drucken($drucker,$tmpfile);
                $doctype = 'auftrag';
                $this->app->erp->RunHook('dokumentsend_ende', 5, $doctype, $v, $projekt, $adressId, $aktion);
                $this->app->erp->AuftragProtokoll($v,"Auftrag versendet");
                unlink($tmpfile);
              }

            }
          break;
          case 'pdf':
            $tmpfile = [];
            foreach($selectedIds as $v) {
              $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$v' LIMIT 1");
              $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
              if(class_exists('AuftragPDFCustom'))
              {
                $Brief = new AuftragPDFCustom($this->app,$projekt);
              }else{
                $Brief = new AuftragPDF($this->app,$projekt);
              }
              $Brief->GetAuftrag($v);
              $_tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              unlink($_tmpfile);
              $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
              if(class_exists('AuftragPDFCustom'))
              {
                $Brief = new AuftragPDFCustom($this->app,$projekt);
              }else{
                $Brief = new AuftragPDF($this->app,$projekt);
              }
              $Brief->GetAuftrag($v);
              $tmpfile[] = $Brief->displayTMP();
              //$Brief->ArchiviereDocument();
            }

            if(count($tmpfile) > 0){
              try {
                /** @var \Xentral\Components\Pdf\PdfMerger $pdfMerger */
                $pdfMerger = $this->app->Container->get('PdfMerger');
                $mergeOutputPath = realpath($this->app->erp->GetTMP()) . '/' . uniqid('sammelpdf_', true) . '.pdf';
                $pdfMerger->merge($tmpfile, $mergeOutputPath);

                foreach($tmpfile as $key=>$value) {
                  unlink($value);
                }

                header('Content-type:application/pdf');
                header('Content-Disposition: attachment;filename='.md5(microtime(true)).'.pdf');
                readfile($mergeOutputPath);
                $this->app->ExitXentral();
              } catch (\Xentral\Components\Pdf\Exception\PdfComponentExceptionInterface $exception) {
                echo 'Fehler beim Generieren der Sammelpdf: ' . htmlspecialchars($exception->getMessage());
                $this->app->ExitXentral();
              }
            }
          break;
          default:
            $this->app->erp->RunHook('auftrag_batch', 1, $selectedId);
            break;
        }
      }
    }
    
    $this->AuftraguebersichtMenu();

    if(strlen($backurl)>5){
      $this->app->erp->MenuEintrag("$backurl", 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    else{
      $this->app->erp->MenuEintrag('index.php', 'Zur&uuml;ck zur &Uuml;bersicht');
    }


    $this->app->erp->RunMenuHook('auftrag_list');

    // ZAHLUNGSMAIL 
    $zahlungsmail= $this->app->Secure->GetPOST('zahlungsmail');

    if($zahlungsmail!='')
    {
      $orders = $this->app->DB->SelectArr(
        "SELECT id
        FROM auftrag 
        WHERE status='freigegeben' 
          AND vorkasse_ok!='1' AND zahlungsweise!='rechnung' AND zahlungsweise!='nachnahme' AND zahlungsweise!='bar' AND zahlungsweise!='lastschrift'"
      );
      if(!empty($orders)){
        foreach($orders as $order) {
          $this->app->erp->AuftragNeuberechnen($order['id']);
          $this->app->erp->AuftragEinzelnBerechnen($order['id']);
          $this->app->erp->AuftragZahlungsmail($order['id']);
        }
      }
    }
    // AUFTAEGE ABSCHLIESSEN!
    $submit = $this->app->Secure->GetPOST('submit');
    $auftraegemarkiert = $this->app->Secure->GetPOST('auftraegemarkiert');
    if($submit!='' || $this->app->Secure->GetPOST('frmauto'))
    {
      $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
      $orderIds = [];
      if(!empty($auftraegemarkiert)) {
        foreach($orderIds as $orderId) {
          if((int)$orderId > 0) {
            $orderIds[] = (int)$orderId;
          }
        }
        $orders = empty($orderIds)?null:$this->app->DB->SelectArr(
          sprintf(
            'SELECT id, projekt 
            FROM `auftrag` 
            WHERE id IN (%s) 
            ORDER BY id',implode(', ', $orderIds)
          )
        );

        if(!empty($orders)) {
          $auftraegenachprojekt = [];
          foreach($orders as $order) {
            $auftraegenachprojekt[$order['projekt']][] = $order['id'];
          }
          foreach ($auftraegenachprojekt as $projekt => $auftraege) {
            if(is_array($auftraege)){
              $this->kommissionierung = $this->app->erp->GetNextKommissionierung($bezeichnung);
              foreach ($auftraege as $auftrag) {
                $this->AuftragVersand($auftrag);
              }
            }
          }
        }
      }
    }

    $zahlungsweisen = $this->app->DB->SelectArr('
      SELECT
        zahlungsweise
      FROM
        auftrag
      GROUP BY
        zahlungsweise
    ');

    $zahlungsweiseStr = '';
    if ($zahlungsweisen) {
      foreach ($zahlungsweisen as $zahlungsweise) {
        if (empty($zahlungsweise['zahlungsweise'])) {
          continue;
        }
        $zahlungsweiseStr .= '<option name="' . $zahlungsweise['zahlungsweise'] . '">' . ucfirst($zahlungsweise['zahlungsweise']) . '</option>';
      }
    }

    $status = $this->app->DB->SelectArr('
      SELECT
        status
      FROM
        auftrag
      GROUP BY
        status
    ');

    $statusStr = '';
    if ($status) {
      foreach ($status as $statusE) {
        if (empty($statusE['status'])) {
          continue;
        }
        $statusStr .= '<option name="' . $statusE['status'] . '">' . ucfirst($statusE['status']) . '</option>';
      }
    }

    $versandarten = $this->app->DB->SelectArr('
      SELECT
        versandart
      FROM
        auftrag
      GROUP BY
        versandart
    ');

    $versandartenStr = '';
    if ($versandarten) {
      foreach ($versandarten as $versandart) {
        if (empty($versandart['versandart'])) {
          continue;
        }
        $versandartenStr .= '<option name="' . $versandart['versandart'] . '">' . ucfirst($versandart['versandart']) . '</option>';
      }
    }

    $laender = $this->app->erp->GetSelectLaenderliste();
    $laenderStr = '';
    foreach ($laender as $landKey => $land) {
      $laenderStr .= '<option value="' . $landKey . '">' . $land . '</option>';
    }


    $this->app->YUI->DatePicker("datumVon");
    $this->app->YUI->DatePicker("datumBis");
    $this->app->YUI->AutoComplete("projekt", "projektname", 1);
    $this->app->YUI->AutoComplete("kundennummer", "kunde", 1);
    $this->app->YUI->AutoComplete("auftragsnummer", "auftrag", 1);
    $this->app->YUI->AutoComplete("artikel", "artikelnummer");
    $this->app->YUI->AutoComplete("kundengruppe", "gruppekennziffer",1);

    $this->app->Tpl->Add('ZAHLUNGSWEISEN',$zahlungsweiseStr);
    $this->app->Tpl->Add('STATUS',$statusStr);
    $this->app->Tpl->Add('VERSANDARTEN',$versandartenStr);
    $this->app->Tpl->Add('LAENDER',$laenderStr);
    $this->app->Tpl->Parse('TAB1',"auftrag_table_filter.tpl");

    $this->app->YUI->TableSearch('TAB2',"auftraegeoffeneauto");
    $this->app->YUI->TableSearch('TAB1','auftraege', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('TAB3',"auftraegeoffene");

    $this->app->Tpl->Set('SELDRUCKER', $this->app->erp->GetSelectDrucker($this->app->User->GetParameter('rechnung_list_drucker')));
    $this->app->YUI->TableSearch('TAB5',"auftraegeinbearbeitung");
    $this->app->erp->RunHook('auftraguebersicht_filter', 0);
    $this->app->Tpl->Parse('PAGE',"auftraguebersicht.tpl");
  }

  /**
   * @param int $order_id
   * @param int $pos_id
   */
  public function removeExplodedPosition($order_id, $pos_id)
  {
    if($order_id <= 0 || $pos_id <= 0)
    {
      return;
    }
    $pos_arr = $this->app->DB->SelectArr(
      sprintf(
        'SELECT id FROM auftrag_position WHERE auftrag = %d AND explodiert_parent = %d',
        (int)$order_id, (int)$pos_id
      )
    );
    if(empty($pos_arr))
    {
      return;
    }
    foreach($pos_arr as $position)
    {
      $this->DelAuftragStueckliste($order_id, $position['id']);
      $this->app->YUI->SortListEvent('del','auftrag_position','auftrag',$order_id, $position['id']);
      $this->removeExplodedPosition($order_id, $pos_id);
    }
  }

}
