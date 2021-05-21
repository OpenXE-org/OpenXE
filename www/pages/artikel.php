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

use Xentral\Widgets\SuperSearch\Result\ResultGroup;
use Xentral\Widgets\SuperSearch\Result\ResultItem;

include '_gen/artikel.php';

class Artikel extends GenArtikel {
  /** @var Application $app */
  var $app;
  const MODULE_NAME = 'Article';

  public function TableSearch($app, $name, $erlaubtevars)
  {
    switch($name) {
      case 'eigenschaften':
        $id = $this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('eigenschaften');
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        $alignright = array(3,4,5);
        $heading = array('Hauptkategorie', 'Unterkategorie (Optional)', 'Wert', 'Einheit (Optional)', 'Men&uuml;');
        $width = array('15%', '15%', '12%', '8%', '10%');
        $findcols = array('e.hauptkategorie', 'e.unterkategorie', 'e.wert', 'e.einheit', 'e.id');
        $searchsql = array('e.hauptkategorie', 'e.unterkategorie', 'e.wert', 'e.einheit');
        $menu = "<a href=\"index.php?module=artikel&action=eigenschafteneditpopup&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=eigenschaftencopy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>". "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=eigenschaftendelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS e.id, e.hauptkategorie, e.unterkategorie, CONCAT(e.wert,'&nbsp;&nbsp;'),
                                     e.einheit, e.id 
                                     FROM eigenschaften e ";
        $where = "e.artikel='$id' ";
        break;
      case 'artikel_eigenschaften':
        $id = $this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('eigenschaften');
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        $alignright = array(3,4,5);
        $heading = array('Eigenschaft', 'Wert', 'Einheit (Optional)', 'Men&uuml;');
        $width = array('32%', '32%', '20%', '10%');
        $findcols = array('e.name', 'ew.wert', 'ew.einheit', 'ew.id');
        $searchsql = array('e.name', 'ew.wert', 'ew.einheit');
        $menu = "<a href=\"#\" onclick=\"editeigenschaft(%value%)\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=\"copyeigenschaft(%value%)\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>". "&nbsp;<a href=\"#\" onclick=\"deleteeigenschaft(%value%)\";><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS ew.id, e.name,  CONCAT(ew.wert,'&nbsp;&nbsp;'),
                                     ew.einheit, ew.id FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften";

        $where = " ew.artikel='$id' ";

        // gesamt anzahl
        $count = "SELECT COUNT(ew.id) FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften WHERE $where ";
        break;

      case 'artikel_eigenschaften_neu':
        $id = $this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('eigenschaften');

        $heading = array('Eigenschaft DE', 'Wert DE', 'Einheit (Optional)', 'Eigenschaft', 'Wert', 'Sprache', 'Shop', 'Artikelspezifisch', 'Men&uuml;', '');
        $width = array('10%', '10%', '10%', '10%', '10%', '4%', '10%', '4%', '1%', '1%');

        $findcols = array('b.name', 'b.wert', 'b.einheit', 'b.eigenschafts', 'b.werts', 'b.spraches', 'b.shops', 'b.artikelspezifisch', 'b.menux', 'b.menu');
        $searchsql = array('b.name', 'b.wert', 'b.einheit', 'b.eigenschafts', 'b.werts', 'b.spraches', 'b.shops', 'b.artikelspezifisch');

        $menu = "";

        $sqla[] = "(SELECT ew.id, e.name, ew.wert, ew.einheit, '' as eigenschafts, '' as werts, 'DE' as spraches, '' as shops, '' as artikelspezifisch, CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"#\" onclick=\"editeigenschaft(',ew.id,')\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.svg\" title=\"Bearbeiten\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=\"deleteeigenschaft(',ew.id,')\";><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.svg\" title=\"L&ouml;schen\" border=\"0\"></a>&nbsp;',IF(e.typ='select' AND ew.vorlage>0,'',CONCAT('<a href=\"#\" onclick=\"copyeigenschaft(',ew.id,')\";><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.svg\" title=\"Kopieren\" border=\"0\"></a></td></tr></table>'))) as menux, '' as menu 
                    FROM artikeleigenschaften e 
                    INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften 
                    WHERE ew.artikel = '$id')";

        $sqla[] = "(SELECT ap.id, ae.name, aw.wert, aw.einheit, ap.property_to as eigenschafts, ap.property_value_to as werts, ap.language_to as spraches, s.bezeichnung as shops, IF(ap.article_id > 0, 'ja', '') as artikelspezifisch, IF(ap.article_id = 0, CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=propertytranslation&action=list\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.svg\" title=\"Eigenschaften &Uuml;bersetzung\" border=\"0\"></a>'), IF(ap.article_id != 0, CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"#\" onclick=\"editUebersetzung(',ap.id,');\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.svg\" title=\"Bearbeiten\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=\"deleteUebersetzung(',ap.id,');\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.svg\" title=\"L&ouml;schen\" border=\"0\"></a>'), '')) as menux, '' as menu
                    FROM article_property_translation ap 
                    LEFT JOIN shopexport s ON ap.shop_id = s.id 
                    LEFT JOIN artikeleigenschaften ae ON ap.property_from = ae.name 
                    JOIN artikeleigenschaftenwerte aw ON ae.id = aw.artikeleigenschaften AND ap.property_value_from = aw.wert
                    WHERE ap.language_from = 'DE' AND aw.artikel = '$id' AND (ap.article_id  = '$id' OR ap.article_id = 0))";

        $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, b.name, b.wert, b.einheit, b.eigenschafts, b.werts, b.spraches, b.shops, b.artikelspezifisch, b.menux, b.menu
            FROM (            
              ".implode(" UNION ALL ", $sqla)."
            )b
        ";

        break;

      case 'artikel_fremdnummern':
        $allowed['artikel'] = array('fremdnummern');
        $id = (int)$this->app->Secure->GetGET('id');

        $this->app->Tpl->Add('JQUERYREADY', "$('#inaktiv').click( function() { fnFilterColumn1( 0 ); } );");

        for ($r = 1;$r <= 1;$r++) {
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
        $more_data1 = $this->app->Secure->GetGET('more_data1');

        $heading = array('Aktiv','Bezeichnung','Fremdnummer','Shop','Scanner', 'Men&uuml;');
        $width = array('1%','20%','20%','15%','5%','1%');
        $findcols = array("if(af.aktiv = 1,'ja','-')",'af.bezeichnung','af.nummer','s.bezeichnung',"if(af.scannable = 1,'ja','-')",'af.id');
        $searchsql = array("if(af.aktiv = 1,'ja','-')",'af.bezeichnung','af.nummer','s.bezeichnung',"if(af.scannable = 1,'ja','-')");
        $defaultorder = 0;
        $defaultorderdesc = 1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"javascript:;\" onclick=\"ArtikelFremdnummernEdit(%value%);\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"javascript:;\" onclick=\"ArtikelFremdnummernDelete(%value%);\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a></tr></table>";

        $sql = "SELECT SQL_CALC_FOUND_ROWS af.id, if(af.aktiv = 1,'ja','-') as aktiv,
        af.bezeichnung,
        af.nummer,
        s.bezeichnung,if(af.scannable = 1,'ja','-') as scannable,af.id
          from artikelnummer_fremdnummern af
          left join shopexport s on af.shopid = s.id
        ";
        $where = "af.artikel = '$id' AND af.aktiv = 1";
        if($more_data1 == 1){
          $where = "af.artikel = '$id'";
        }
        break;
      case 'manuellagerlampe':
        $allowed['artikel'] = array('lagerlampe');
        $this->app->Tpl->Add('JQUERYREADY', "$('#green').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#yellow').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#red').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#imlager').click( function() { fnFilterColumn4( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nichtimlager').click( function() { fnFilterColumn5( 0 ); } );");

        // headings
        $heading = array('', 'Ampel', 'Nummer', 'Artikel', 'Lieferant', 'Im Lager', 'Projekt', 'Men&uuml;');
        $width = array('3%', '5%', '10%', '35%', '20%', '8%', '15%', '10%');
        $findcols = array('wahl', 'a.lieferzeit', 'a.nummer', 'a.name_de', 'a.lieferant', 'lager', 'projekt', 'a.id');
        $searchsql = array('a.name_de', 'a.nummer', 'adr.name', 'p.abkuerzung');
        $menu = "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>-->";

        // SQL statement

        /*                      CONCAT('<input type=\"checkbox\" class=\"chcktbl2\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl,

                                    CONCAT('<img src=./themes/new/images/shop_stock',a.lieferzeit,'.png>') as ampel,
        */
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, 
              CONCAT('<input type=\"checkbox\" class=\"chcktbl\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, 
              CONCAT('<img src=./themes/new/images/shop_stock',a.lieferzeit,'.png>') as ampel, 

              If(a.inaktiv,CONCAT('<strike>',a.nummer,'</strike>'),a.nummer) as nummer, 
              If(a.inaktiv,CONCAT('<strike>',a.name_de,'</strike>'),a.name_de) as name_de, 
              If(a.inaktiv,CONCAT('<strike>',adr.name,'</strike>'),adr.name) as lieferant, 

              (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, 

              If(a.inaktiv,CONCAT('<strike>',p.abkuerzung,'</strike>'),p.abkuerzung) as projekt, 
              a.id as menu                                                                          
                FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN  adresse adr ON a.adresse=adr.id ";
        for ($r = 1;$r < 9;$r++) {
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

        // START EXTRA more
        $more_data1 = $this->app->Secure->GetGET('more_data1');

        if ($more_data1 == 1) {
          $subwhere[] = " a.lieferzeit='green' ";
        }
        $more_data2 = $this->app->Secure->GetGET('more_data2');

        if ($more_data2 == 1) {
          $subwhere[] = " a.lieferzeit='yellow' ";
        }
        $more_data3 = $this->app->Secure->GetGET('more_data3');

        if ($more_data3 == 1) {
          $subwhere[] = " a.lieferzeit='red' ";
        }
        $more_data4 = $this->app->Secure->GetGET('more_data4');

        if ($more_data4 == 1) {
          $subwhere[] = ' (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0 ';
        }
        $more_data5 = $this->app->Secure->GetGET('more_data5');

        if ($more_data5 == 1) {
          $subwhere[] = ' (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) IS NULL ';
        }

        $tmp = '';
        $csubwhere = !empty($subwhere)?count($subwhere):0;
        for ($j = 0;$j < $csubwhere;$j++) {
          $tmp.= ' AND ' . $subwhere[$j];
        }

        $where = 'a.geloescht=0 AND a.shop > 0 AND a.lagerartikel=1 AND autolagerlampe!=1 ' . $tmp;
        $count = 'SELECT COUNT(id) FROM artikel WHERE geloescht=0 AND shop > 0  AND lagerartikel=1';
        break;
      case 'autolagerlampe':
        $allowed['artikel'] = array('lagerlampe');

        // headings
        $heading = array('', 'Ampel', 'Art', 'Nummer', 'Artikel', 'Lieferant', 'Im Lager', 'Projekt', 'Men&uuml;');
        $width = array('1%', '7%', '3%', '10%', '30%', '20%', '10%', '10%');
        $findcols = array('a.id', 'a.lieferzeit', 'art', 'a.nummer', 'a.name_de', 'lieferant', 'projekt', 'a.id');
        $searchsql = array('a.name_de', 'a.nummer', 'adr.name', 'p.abkuerzung');
        $menu = "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>-->";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, 

              if(a.autolagerlampe,CONCAT('<input type=\"checkbox\" class=\"chcktbl2\" name=\"artikelmarkiert[',a.id,']\" checked value=\"1\"><input type=\"hidden\" name=\"artikelmarkierthidden[',a.id,']\" value=\"1\">'),
                  CONCAT('<input type=\"checkbox\" class=\"chcktbl2\" name=\"artikelmarkiert[',a.id,']\" value=\"1\"><input type=\"hidden\" name=\"artikelmarkierthidden[',a.id,']\" value=\"0\">')) as wahl, 
                CONCAT('<img src=./themes/new/images/shop_stock',a.lieferzeit,'.png>') as ampel, 

                  if(a.autolagerlampe,'auto','manuell') as art,

                    If(a.inaktiv,CONCAT('<strike>',a.nummer,'</strike>'),a.nummer) as nummer, 
                      If(a.inaktiv,CONCAT('<strike>',a.name_de,'</strike>'),a.name_de) as name_de, 
                      If(a.inaktiv,CONCAT('<strike>',adr.name,'</strike>'),adr.name) as lieferant, 
                      (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, 
                      If(a.inaktiv,CONCAT('<strike>',p.abkuerzung,'</strike>'),p.abkuerzung) as projekt, 

                      a.id as menu                                                                          
                        FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN  adresse adr ON a.adresse=adr.id ";
        $where = 'a.geloescht=0 AND a.shop > 0 AND a.lagerartikel=1 ';
        $count = 'SELECT COUNT(id) FROM artikel WHERE geloescht=0 AND shop > 0  AND lagerartikel=1';
        break;
      case 'artikeltabelleneu':
        $allowed['artikel'] = array('lagerlampe');

        // headings
        $heading = array('', 'Nummer', 'Artikel', 'Im Lager', 'Projekt', 'Men&uuml;');
        $width = array('5%', '10%', '45%', '8%', '15%', '1%');
        $findcols = array('nummer', 'name_de', 'projekt', 'lager', 'id');
        $searchsql = array('a.nummer', 'a.name_de','p.abkuerzung');
        $menu = "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>-->";

        // SQL statement

        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<input type=\"checkbox\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, a.nummer as nummer, 
                a.name_de as name_de, (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, p.abkuerzung as projekt, a.id as menu                                                                          
                FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";

        // fester filter
        $where = "a.geloescht=0 AND a.neu='1' AND a.shop >0 " . $this->app->erp->ProjektRechte();
        $count = 'SELECT COUNT(a.id) FROM artikel a WHERE a.geloescht=0 AND a.shop > 0 AND a.neu=1 ' . $this->app->erp->ProjektRechte();
        break;
      case 'lieferantartikelpreise':
        $id = (int)$this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('profisuche');
        // alle artikel die ein Kunde kaufen kann mit preisen netto brutto
        $cmd = $this->app->Secure->GetGET('smodule');
        $adresse = $this->app->DB->Select("SELECT adresse FROM {$cmd} WHERE id='$id' LIMIT 1");

        // headings
        $heading = array('', 'Nummer', 'Artikel', 'Ab', 'Preis', 'Lager', 'Res.', 'Menge', 'Projekt', 'Men&uuml;');
        $width = array('1%', '10%', '45%', '10%', '10%', '10%', '10%', '15%', '10%');
        $findcols = array('auswahlbox', 'nummer', 'name_de', 'v.ab_menge', 'v.preis', 'lager', 'reserviert', 'auswahlmenge', 'p.abkuerzung', 'id');
        $searchsql = array('a.name_de', 'a.nummer', 'p.abkuerzung');

        $alignright = array(4,5,6);
        $numbercols = array(4);

        $menu = "<a href=\"#\" class=\"articlematrix-quickadd\" data-id=\"%value%\" data-insert-url=\"index.php?module=artikel&action=profisuche&id=%value%&cmd=$cmd&sid=$id&insert=true\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/add.png\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,
              CONCAT('<input type=\"checkbox\" name=\"auswahl[', v.id, ']\" class=\"articlematrix-checkbox\" id=\"articlematrix-checkbox-', v.id, '\" data-id=\"', v.id, '\">') AS auswahlbox,
              a.nummer as nummer, a.name_de as name_de, ".$this->app->erp->FormatMenge('v.ab_menge').' as abmenge,'.$this->app->erp->FormatPreis('v.preis',2).' as preis,
              '.$this->app->erp->FormatMenge('(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id)').' as lager,
              '.$this->app->erp->FormatMenge('(SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id)')." as reserviert, 
              CONCAT('<input type=\"text\" size=\"5\" name=\"menge[', v.id, ']\" class=\"articlematrix-quantity\" id=\"articlematrix-quantity-', v.id, '\" data-id=\"', v.id, '\" data-default-quantity=\"', ".$this->app->erp->FormatMenge("v.ab_menge").", '\" autocomplete=\"off\">') AS auswahlmenge,
              p.abkuerzung as projekt, v.id as menu
              FROM  einkaufspreise v, artikel a LEFT JOIN projekt p ON p.id=a.projekt  ";

        // fester filter
        //$where = "a.geloescht=0 AND v.artikel=a.id AND (v.adresse='' OR v.adresse='$adresse' OR v.adresse='0') AND (v.gueltig_bis='0000-00-00' OR v.gueltig_bis >=NOW()) ";
        $where = "a.geloescht=0 AND v.artikel=a.id AND (v.adresse='$adresse') AND (v.gueltig_bis='0000-00-00' OR v.gueltig_bis >=NOW()) ";
        //$count = "SELECT COUNT(v.id) FROM einkaufspreise v, artikel a WHERE a.geloescht=0 AND v.artikel=a.id AND (v.adresse='' OR v.adresse='$adresse')";
        $count = "SELECT COUNT(v.id) FROM einkaufspreise v, artikel a WHERE a.geloescht=0 AND v.artikel=a.id AND (v.adresse='$adresse')";
        break;
      case 'kundeartikelpreise':
        $id = (int)$this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('profisuche');
        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#eigene').click( function() { fnFilterColumn1( 0 ); } );");
        for ($r = 1;$r < 2;$r++) {
          $this->app->Tpl->Add('JAVASCRIPT', '
                  function fnFilterColumn' . $r . ' ( i )
                  {
                  if(oMoreData' . $r . $name . '==1)
                  oMoreData' . $r . $name . ' = 0;
                  else
                  oMoreData' . $r . $name . ' = 1;

                  $(\'#' . $name . '\').dataTable().fnFilter(                    \'\',                    i,
                    0,0
                    );                  }
                  ');
        }

        // alle artikel die ein Kunde kaufen kann mit preisen netto brutto
        $cmd = $this->app->Secure->GetGET('smodule');
        $adresse = $this->app->DB->Select(sprintf('SELECT adresse FROM `%s` WHERE id=%d LIMIT 1',$cmd,$id));

        $sEcho = (int)$this->app->Secure->GetGET('sEcho');
        if ($sEcho === 1) {
          // Beim allerersten Aufruf Filterbaum-Selektion löschen, damit alle Artikel angezeigt werden.
          // Ansonsten ist Kategorie-Filter aktiv; die ausgewählte Kategorie ist aber nicht markiert im Baum.
          $this->app->User->SetParameter('filterbaum_'.$cmd, '');
        }

        $filterbaum = $this->app->User->GetParameter('filterbaum_'.$cmd);
        $baum = null;
        if($filterbaum)
        {
          $this->app->erp->GetArtikelunterbaumids($filterbaum, $baum);
        }
        $baumids = null;
        $typwhere = [];
        if($baum)
        {
          foreach($baum as $v)
          {
            $baumids[] = " kategorie = '$v' ";
            $typwhere[] = ' a.typ = \''.$v.'_kat\' ';
          }
        }

        // headings
        $heading = array('', 'Nummer', 'Artikel', 'Ab', 'Preis','W&auml;hrung', 'Lager', 'Res.', 'Menge', 'Projekt');
        $width = array('1%', '10%', '45%', '10%', '10%', '10%', '10%', '10%', '15%');
        $findcols = array('auswahlbox', 'nummer', 'name_de', 'v.ab_menge', 'v.preis','v.waehrung', 'l.menge', 'r.menge', 'auswahlmenge', 'p.abkuerzung');
        $fmodul = $this->app->Secure->GetGET('fmodul');
        $searchsql = array('a.name_de', 'a.nummer', 'p.abkuerzung','a.herstellernummer');
        $alignright = array(4,5,7,8);
        $numbercols = array(4);

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche=='1') {
          $searchsql[]='a.freifeld1';
        }
        if($artikel_freitext1_suche=='1') {
          $searchsql[]='a.freifeld2';
        }

        $artikel_suche_kurztext = $this->app->erp->Firmendaten('artikel_suche_kurztext');
        if($artikel_suche_kurztext=='1') {
          $searchsql[]='a.kurztext_de';
        }



        $artikelzusatzfelder = $this->app->erp->getZusatzfelderArtikel();
        $artikelcachedfelder = $this->app->erp->getArtikelCachedfelder();
        $zusatzcols = null;
        $joincached = '';
        $joineig = '';
        for($i = 1; $i <= 5; $i++)
        {
          $zusatzfeld = $this->app->erp->Firmendaten('artikeltabellezusatz'.$i);
          if($zusatzfeld && isset($artikelzusatzfelder[$zusatzfeld]))
          {
            $heading[] = $artikelzusatzfelder[$zusatzfeld];
            $width[] = '10%';
            if(isset($artikelcachedfelder[$zusatzfeld]))
            {
              $findcols[] = 'cache.'.$zusatzfeld;
              switch($artikelcachedfelder[$zusatzfeld])
              {
                case 'Menge':
                  if($zusatzfeld == 'lager_verfuegbar')
                  {
                    $zusatzcols[] = 'if(a.lagerartikel=\'1\','.$this->app->erp->FormatMenge('cache.' . $zusatzfeld).',\'\')';
                  }else{
                    $zusatzcols[] = $this->app->erp->FormatMenge('cache.' . $zusatzfeld);
                  }
                  $alignright[] = count($findcols);
                  break;
                case 'Preis':
                  $zusatzcols[] = $this->app->erp->FormatPreis('cache.'.$zusatzfeld,2);
                  $alignright[] = count($findcols);
                  break;
                default:
                  $zusatzcols[] = 'cache.'.$zusatzfeld;
                  break;
              }
              $joincached = ' LEFT JOIN artikel_cached_fields cache ON a.id = cache.artikel ';
            }else{
              switch($zusatzfeld)
              {
                case 'vknetto':
                case 'eknetto':
                case 'vkbrutto':
                case 'ekbrutto':
                case 'lagerverfuegbar':
                  $findcols[] = 'a.id';
                  $zusatzcols[] = "''";
                  break;
                case 'eigenschaften':
                  /*$findcols[] = 'eig.value';
                  $zusatzcols[] = 'eig.value';
                  $joineig = "
                  LEFT JOIN (
                  SELECT aew.artikel, 
                   GROUP_CONCAT(DISTINCT concat(ae.name,': ',aew.wert)
                      ORDER BY ae.name  SEPARATOR ', ') as value
                   FROM artikeleigenschaftenwerte aew
                  INNER JOIN artikeleigenschaften ae ON aew.artikeleigenschaften = ae.id AND ifnull(ae.geloescht,0) = 0 AND ae.name != ''
                  GROUP BY aew.artikel
                  ) eig ON a.id = eig.artikel
                  ";*/
                  break;
                default:
                  $findcols[] = 'a.'.$zusatzfeld;
                  $zusatzcols[] = 'a.'.$zusatzfeld;
                  break;
              }
            }
          }
        }

        $joins = '';

        $joins .= $joincached;
        $joins .= $joineig;


        $menu = "<a href=\"#\" class=\"articlematrix-quickadd\" data-id=\"%value%\" data-insert-url=\"index.php?module=artikel&action=profisuche&id=%value%&cmd=$cmd&sid=$id&insert=true&fmodul=$fmodul\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/add.png\" border=\"0\"></a>";

        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, 
            CONCAT('<input type=\"checkbox\" name=\"auswahl[', v.id, ']\" class=\"articlematrix-checkbox\" id=\"articlematrix-checkbox-', v.id, '\" data-id=\"', v.id, '\">') AS auswahlbox,
            a.nummer as nummer, 
            CONCAT(a.name_de,' (',v.art,')') 
            as name_de, trim(v.ab_menge)+0 as abmenge,".$this->app->erp->FormatPreis('v.preis')." as preis,v.waehrung,
            trim(l.menge)+0 as lager, 
            trim(r.menge)+0 as reserviert, 
            CONCAT('<input type=\"text\" size=\"5\" name=\"menge[', v.id, ']\" class=\"articlematrix-quantity\" id=\"articlematrix-quantity-', v.id, '\" data-id=\"', v.id, '\" data-default-quantity=\"', ".$this->app->erp->FormatMenge('v.ab_menge').", '\" autocomplete=\"off\">') AS auswahlmenge,
            p.abkuerzung as projekt, ";

        if($zusatzcols)
        {
          $sql .= implode(', ', $zusatzcols).', ';
          foreach($zusatzcols as $v)
          {
            if(!in_array($v, $searchsql)){
              $searchsql[] = $v;
            }
          }
        }

        $sql .= "v.id as menu
            FROM  verkaufspreise v
            INNER JOIN artikel a ON v.artikel = a.id AND a.geloescht <> 1
            LEFT JOIN (SELECT artikel, sum(menge) as menge FROM lager_platz_inhalt GROUP BY artikel) l ON l.artikel = a.id
            LEFT JOIN (SELECT artikel, sum(menge) as menge FROM lager_reserviert GROUP BY artikel) r ON r.artikel = a.id
            LEFT JOIN projekt p ON p.id=a.projekt  $joins";

        $heading[] = 'Men&uuml;';
        $width[] = '1%';
        $findcols[] = 'id';

        $gruppenarr = $this->app->erp->GetGruppen($adresse);
        $cgruppenarr =  $gruppenarr?count($gruppenarr):0;
        $gruppen = '';
        for ($i = 0;$i < $cgruppenarr;$i++) {

          if ($gruppenarr[$i] > 0) {
            $gruppen.= " OR v.gruppe='" . $gruppenarr[$i] . "' ";
          }
        }

        //$more_data1 = $this->app->Secure->GetGET("more_data1");

        if(!$this->app->Secure->GetGET('more_data1')){
          $tmp_or = " OR ((v.adresse='' OR v.adresse='0') AND v.art='Kunde') ";
        }

        $where = "((v.adresse='$adresse' $gruppen) $tmp_or) AND (v.gueltig_bis='0000-00-00' OR v.gueltig_bis>=NOW()) AND a.intern_gesperrt!=1 ".$this->app->erp->ProjektRechte('a.projekt').' ';
        if(!empty($baumids))
        {
          $sql .= 'LEFT JOIN (SELECT artikel FROM `artikelbaum_artikel` WHERE ('.implode(' OR ', $baumids).') GROUP BY artikel) b ON a.id = b.artikel ';
          $where .= 'AND (b.artikel > 0 OR '.implode(' OR ',$typwhere).') ';
        }

        //$count = "SELECT COUNT(v.id) FROM verkaufspreise v INNER JOIN artikel a ON v.artikel = a.id AND a.geloescht <> 1 WHERE ((v.adresse='$adresse' $gruppen) $tmp_or) AND (v.gueltig_bis='0000-00-00' OR v.gueltig_bis>=NOW()) ";

        break;
      case 'artikel_auftraege_offen':
        $id = (int)$this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('offeneauftraege');

        // headings
        $heading = array('Auftrag', 'Datum', 'Status', 'Zahlweise', 'Freigabe', 'Kunde', 'Menge', 'Geliefert', 'Preis', 'Men&uuml;');
        $width = array('10%', '10%', '15%', '10%', '10%', '30%', '10%', '10%');
        $findcols = array('a.id', 'a.belegnr', 'a.datum', 'a.status', 'a.zahlungsweise', 'adr.kundenfreigabe', 'adr.name', 'ap.menge', 'ap.geliefert_menge', "FORMAT(ap.preis*(100-ap.rabatt)/100,2)");
        $searchsql = array('a.belegnr', "DATE_FORMAT(a.datum,'%d.%m.%Y')", 'a.status', 'a.zahlungsweise', 'adr.kundenfreigabe', 'adr.name', 'ap.menge', 'ap.geliefert_menge', "FORMAT(ap.preis*(100-ap.rabatt)/100,2)");
        $menu = "<a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";
        $alignright = array(7,8,9);
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<a href=\"index.php?module=auftrag&action=edit&id=',a.id,'\">',a.belegnr,'</a>') as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.status, a.zahlungsweise, adr.kundenfreigabe as freigabe, CONCAT(a.name,'<br>', a.email) as Kunde, 
              trim(ap.menge)+0, trim(ap.geliefert_menge)+0 as gelieferte, FORMAT(ap.preis*(100-ap.rabatt)/100,2) as preis, a.id 
              FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr";

        if ($name == 'artikel_auftraege_offen') {

          // fester filter
          $where = " adr.id=a.adresse AND ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben'";
          $count = "SELECT COUNT(a.id) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr 
                WHERE adr.id=a.adresse AND ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben'";
        } else {

          // fester filter
          $where = " adr.id=a.adresse AND ap.artikel='$id' AND a.status='abgeschlossen'";
          $count = "SELECT COUNT(a.id) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr 
                WHERE adr.id=a.adresse AND ap.artikel='$id' AND a.status='abgeschlossen'";
        }
        break;
      case 'artikel_auftraege_versendet':
        $id = (int)$this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('offeneauftraege');
        $alignright = array(7,8,9);
        // headings
        $heading = array('Auftrag', 'Datum', 'Status', 'Zahlweise', 'Freigabe', 'Kunde', 'Menge', 'Geliefert', 'Preis', 'Men&uuml;');
        $width = array('10%', '10%', '15%', '10%', '10%', '30%', '10%', '10%');
        $findcols = array('a.id', 'a.belegnr', 'a.datum', 'a.status', 'a.zahlungsweise', 'adr.kundenfreigabe', 'adr.name', 'ap.menge', 'ap.geliefert_menge', "FORMAT(ap.preis*(100-ap.rabatt)/100,2)");
        $searchsql = array('a.belegnr', "DATE_FORMAT(a.datum,'%d.%m.%Y')", 'a.status', 'a.zahlungsweise', 'adr.kundenfreigabe', 'adr.name', 'ap.menge', 'ap.geliefert_menge', "FORMAT(ap.preis*(100-ap.rabatt)/100,2)");
        $menu = "<a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<a href=\"index.php?module=auftrag&action=edit&id=',a.id,'\">',a.belegnr,'</a>') as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.status, a.zahlungsweise, adr.kundenfreigabe as freigabe, CONCAT(a.name,'<br>', a.email) as Kunde, 
              trim(ap.menge)+0, trim(ap.geliefert_menge)+0 as gelieferte, FORMAT(ap.preis*(100-ap.rabatt)/100,2) as preis, a.id 
              FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr";

        // fester filter
        $where = " adr.id=a.adresse AND ap.artikel='$id' AND a.status='abgeschlossen'";
        $count = "SELECT COUNT(a.id) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr 
              WHERE adr.id=a.adresse AND ap.artikel='$id' AND a.status='abgeschlossen'";
        break;
      case 'verkaufspreise':
        $id = (int)$this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('verkauf');
        $this->app->Tpl->Add('JQUERYREADY', "$('#alteverkaufspreise').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nurkunde').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nurgruppe').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nurstandard').click( function() { fnFilterColumn4( 0 ); } );");
        $defaultorder = 3; //Optional wenn andere Reihenfolge gewuenscht

        $alignright=array(3,4,5,6,7);

        $defaultorderdesc = 0;
        for ($r = 1;$r < 5;$r++) {
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
        $heading = array('Kunde/Gruppe', 'Hinweis', 'ab', 'Preis', 'W&auml;hrung', 'G&uuml;ltig ab', 'G&uuml;ltig bis', 'Men&uuml;');
        $width = array('40%', '15%', '10%', '5%', '10%', '10%','15%');
        $findcols = array("if(v.art='Kunde',if(v.adresse='' or v.adresse=0,'Standardpreis',CONCAT(adr.kundennummer,' ',adr.name)),CONCAT(g.name,' ',g.kennziffer))", 'hinweis', $this->app->erp->FormatMenge('v.ab_menge'), 'preis', 'v.waehrung','gueltig_ab','gueltig_bis', 'id');
        $searchsql = array("if(v.art='Kunde',if(v.adresse='' or v.adresse=0,'Standardpreis',CONCAT(adr.kundennummer,' ',adr.name)),CONCAT(g.name,' ',g.kennziffer))",'adr.name', 'g.name', $this->app->erp->FormatMenge('v.ab_menge'), 'v.waehrung',$this->app->YUI->FormatPreis('v.preis'),"DATE_FORMAT(v.gueltig_bis,'%d.%m.%Y')","DATE_FORMAT(v.gueltig_ab,'%d.%m.%Y')");
        $menu = '<table cellpadding=0 cellspacing=0>';
        $menu .= '<tr>';
        $menu .= '<td nowrap>';
        $menu .= '<a href="javascript:;" onclick="VerkaufspreiseEdit(%value%);">';
        //$menu .= "<a href=\"index.php?module=artikel&action=verkaufeditpopup&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= '</a>' . '&nbsp;';
        $menu .= "<a href=\"#\" onclick=DisableDialog(\"index.php?module=artikel&action=verkaufdisable&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/disable.png\" border=\"0\">";
        $menu .= '</a>' . '&nbsp;';
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=verkaufdelete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= '</a>&nbsp;';
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=verkaufcopy&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= '</a>';
        $menu .= '</td>';
        $menu .= '</tr>';
        $menu .= '</table>';

        $numbercols = array(3);

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, 
                            if(v.art='Kunde',if(v.adresse='' or v.adresse=0,'Standardpreis',CONCAT(adr.kundennummer,' ',adr.name)),CONCAT(g.name,' ',g.kennziffer)) as kunde,  
                             if(v.adresse > 0 OR v.gruppe >0,'','') as hinweis,
                                ".$this->app->erp->FormatMenge('v.ab_menge').' as ab_menge, '.$this->app->YUI->FormatPreis('v.preis')." as preis, v.waehrung, DATE_FORMAT(v.gueltig_ab, '%d.%m.%Y') as gueltig_ab, DATE_FORMAT(v.gueltig_bis, '%d.%m.%Y') as gueltig_bis, v.id as menu
                                 FROM  verkaufspreise v LEFT JOIN adresse adr ON v.adresse=adr.id  LEFT JOIN gruppen g ON g.id=v.gruppe ";
        $more_data1 = $this->app->Secure->GetGET('more_data1');

        // kunde
        $more_data2 = $this->app->Secure->GetGET('more_data2');
        if($more_data2=='1') {
          $subwhere[] = ' v.adresse > 0 AND v.gruppe <= 0 ';
        }

        //Gruppe
        $more_data3 = $this->app->Secure->GetGET('more_data3');
        if($more_data3=='1') {
          $subwhere[] = ' v.gruppe > 0 AND v.adresse<=0 ';
        }

        //listenpreise
        $more_data4 = $this->app->Secure->GetGET('more_data4');
        if($more_data4=='1') {
          $subwhere[] = ' v.adresse = 0 AND v.gruppe=0 ';
        }

        $csubwhere = !empty($subwhere)?count($subwhere):0;
        $tmp = '';
        for ($j = 0;$j < $csubwhere;$j++) {
          $tmp.= '  AND ' . $subwhere[$j];
        }

        if ($more_data1 == 1){
          $where = "v.artikel='$id' " . $tmp;
        }
        else{
          $where = "v.artikel='$id'  AND v.geloescht='0' AND (v.gueltig_bis>NOW() OR v.gueltig_bis='0000-00-00') " . $tmp;
        }



        // gesamt anzahl
        $count = "SELECT COUNT(v.id) FROM verkaufspreise v WHERE $where";
        break;
      case 'einkaufspreise':
        $id = (int)$this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('einkauf');
        $this->app->Tpl->Add('JQUERYREADY', "$('#alteeinkaufspreise').click( function() { fnFilterColumn1( 0 ); } );");
        $defaultorder = 4; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
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

        // headings
        $heading = array('Lieferant', 'Bezeichnung', 'Bestellnummer', 'ab', 'VPE', 'Preis', 'W&auml;hrung', 'bis', 'Rahmenvert.', 'Men&uuml;');
        $width = array('35%', '20%', '3%', '3%', '1%', '1%', '1%', '1%', '10%', '10%');
        $findcols = array('adr.name', 'bezeichnunglieferant', 'bestellnummer', 'ab_menge', 'vpe', 'preis', 'e.waehrung', 'e.gueltig_bis', "if(e.rahmenvertrag='1',CONCAT(e.rahmenvertrag_menge,' / ',IFNULL((SELECT trim(SUM(bp.menge)) FROM bestellung b LEFT JOIN bestellung_position bp ON bp.bestellung=b.id WHERE b.datum >=e.rahmenvertrag_von AND b.datum <= e.rahmenvertrag_bis AND b.status!='storniert' AND e.adresse=b.adresse AND bp.artikel=e.artikel),0)),'-')", 'id');
        $searchsql = array('adr.name', 'e.bezeichnunglieferant', 'e.bestellnummer', 'e.ab_menge', 'e.vpe', $this->app->YUI->FormatPreis('e.preis'), 'e.waehrung', "DATE_FORMAT(e.gueltig_bis,'%d.%m.%Y')", "if(e.rahmenvertrag='1',CONCAT(e.rahmenvertrag_menge,' / ',IFNULL((SELECT trim(SUM(bp.menge)) FROM bestellung b LEFT JOIN bestellung_position bp ON bp.bestellung=b.id WHERE b.datum >=e.rahmenvertrag_von AND b.datum <= e.rahmenvertrag_bis AND b.status!='storniert' AND e.adresse=b.adresse AND bp.artikel=e.artikel),0)),'-')");

        $menu = '<a href="javascript:;" onclick="EinkaufspreiseEdit(%value%);">';
        //$menu = "<a href=\"index.php?module=artikel&action=einkaufeditpopup&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= '</a>" . "&nbsp;';
        $menu .= "<a href=\"#\" onclick=DisableDialog(\"index.php?module=artikel&action=einkaufdisable&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/disable.png\" border=\"0\">";
        $menu .= '</a>" . "&nbsp;';
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=einkaufdelete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= '</a>" . "&nbsp;';
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=einkaufcopy&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= '</a>';

        // SQL statement

        $datecols = array(7);
        $numbercols = array(5);

        $alignright=array(4,5,6,7,8,9);

        $sql = "SELECT SQL_CALC_FOUND_ROWS e.id, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',adr.id,'\" target=\"_blank\">',adr.name,'</a>') as lieferant, e.bezeichnunglieferant, e.bestellnummer, 
                                       ".$this->app->erp->FormatMenge('e.ab_menge')." as ab_menge ,e.vpe as vpe, ".$this->app->YUI->FormatPreis('e.preis')." as preis,e.waehrung as waehrung, if(e.gueltig_bis='0000-00-00','-',DATE_FORMAT(e.gueltig_bis, '%d.%m.%Y')) as gueltig_bis, 
                                        if(e.rahmenvertrag='1',CONCAT(e.rahmenvertrag_menge,' / ',IFNULL((SELECT trim(SUM(bp.menge)) FROM bestellung b LEFT JOIN bestellung_position bp ON bp.bestellung=b.id WHERE b.datum >=e.rahmenvertrag_von AND b.datum <= e.rahmenvertrag_bis AND b.status!='storniert' AND e.adresse=b.adresse AND bp.artikel=e.artikel),0)),'-') as rahmenvertrag, e.id as menu
                                       FROM  einkaufspreise e LEFT JOIN projekt p ON p.id=e.projekt LEFT JOIN adresse adr ON e.adresse=adr.id  ";

        $more_data1 = $this->app->Secure->GetGET('more_data1');
        //              if($tmp!="")$tmp .= " AND e.geloescht='1' ";

        // Fester filter


        if ($more_data1 == 1){
          $where = "e.artikel='$id' AND e.geloescht='0' ";
        }
        else{
          $where = "e.artikel='$id'  AND e.geloescht='0' AND (e.gueltig_bis>NOW() OR e.gueltig_bis='0000-00-00') ";
        }

        // Fester filter
        //            $where = "e.artikel='$id' AND e.geloescht='0' ";


        // gesamt anzahl

        $count = "SELECT COUNT(e.id) FROM einkaufspreise e WHERE $where";
        break;
      case 'lagerbewegungartikel':
        $id = (int)$this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('lager');

        $heading = array('Datum', 'Lager', 'Menge', 'VPE', 'Richtung', 'Referenz', 'Bearbeiter', 'Projekt', 'Inventur','Men&uuml;');
        $width = array('1%', '5%', '5%', '5%', '5%', '40%', '20%', '5%','10%', '1%');
        $findcols = array('lpi.zeit', 'lp.kurzbezeichnung', 'lpi.menge', 'vpe', "if(lpi.eingang,'Eingang','Ausgang')", 'referenz', 'lpi.bearbeiter', 'p.abkuerzung', 'zeitstempel','id');
        $searchsql = array('DATE_FORMAT(lpi.zeit,\'%d.%m.%Y\')', 'lp.kurzbezeichnung', 'lpi.menge', 'vpe', "if(lpi.eingang,'Eingang','Ausgang')", 'lpi.referenz', 'lpi.bearbeiter', 'p.abkuerzung');
        $defaultorder = 10; //Optional wenn andere Reihenfolge gewuenscht
        $alignright = array(3);
        $defaultorderdesc = 1;
        $menu = '-';

        $datecols = array(0);

        //$menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap></td></tr></table>";

        //<a href=\"#\"onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=dateien&action=delete&id=%value%';\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" ></a>


        //            $menucol=3;
        /*

                                      SELECT DATE_FORMAT(lpi.zeit,'%d.%m.%Y') as datum, lp.kurzbezeichnung as lager, lpi.menge as menge, lpi.vpe as VPE, if(lpi.eingang,'Eingang','Ausgang') as Richtung, substring(lpi.referenz,1,60) as referenz, lpi.bearbeiter as bearbeiter, p.abkuerzung as projekt,
                                      lpi.id FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  WHERE lpi.artikel='$id' order by lpi.zeit DESC*/

        // SQL statement

        if($this->app->erp->GetKonfiguration('artikel_lager_bestandsanzeige')=='1')
        {
          $sql = "SELECT SQL_CALC_FOUND_ROWS lpi.id,
                 DATE_FORMAT(lpi.zeit,'%d.%m.%Y') as datum, lp.kurzbezeichnung as lager,
                   ".$this->app->erp->FormatMenge('lpi.menge')." as menge, 
                   lpi.vpe as VPE, if(lpi.eingang,'Eingang','Ausgang') as Richtung, 
                   CONCAT(
                     lpi.referenz,
                     if(
                       lpi.bestand >= 0 AND DATE_FORMAT(lpi.zeit,'%Y-%m-%d') >='2015-06-07' ,
                       CONCAT(
                         ' (Neuer Bestand: ', 
                         ".$this->app->erp->FormatMenge('lpi.bestand').",
                         ')'
                       ),
                       ''
                     ),
                     IF(art.mindesthaltbarkeitsdatum = 1,
                      CONCAT(' <br /><i style=\"color:#aaa;\">', 
                        IFNULL(
                          (
                            SELECT CONCAT(IFNULL(mhddatum,''),' ',IFNULL(charge,'') ) 
                            FROM mhd_log 
                            WHERE artikel = art.id AND zeit >= lpi.zeit 
                            ORDER BY id 
                            LIMIT 1
                          ),''
                        ),'</i>'
                      ),
                      IF(
                        art.chargenverwaltung > 0,
                        CONCAT(' <br /><i style=\"color:#aaa;\">',
                          IFNULL(
                            (
                              SELECT IFNULL(bezeichnung,'') 
                              FROM chargen_log 
                              WHERE artikel = art.id AND zeit >= lpi.zeit 
                              ORDER BY id 
                              LIMIT 1
                            ),
                          ''
                          ),'</i>'
                        ),
                        ''
                      )
                     )
                   ) as referenz, 
                   lpi.bearbeiter as bearbeiter, p.abkuerzung as projekt, 
                   DATE_FORMAT(api.zeitstempel,'%d.%m.%Y'),
                 lpi.id 
                 FROM lager_bewegung AS lpi
                 INNER JOIN `artikel` AS `art` ON lpi.artikel = art.id 
                 LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id 
                 LEFT JOIN projekt p ON lpi.projekt=p.id 
                 LEFT JOIN artikel_permanenteinventur api ON api.id=lpi.permanenteinventur";
        } else {
          $sql = "SELECT SQL_CALC_FOUND_ROWS lpi.id,
                  DATE_FORMAT(lpi.zeit,'%d.%m.%Y') as datum, lp.kurzbezeichnung as lager,
                    ".$this->app->erp->FormatMenge("lpi.menge")." as menge,
                     lpi.vpe as VPE, if(lpi.eingang,'Eingang','Ausgang') as Richtung, 
                     CONCAT(
                        lpi.referenz,
                                             IF(art.mindesthaltbarkeitsdatum = 1,
                      CONCAT(' <br /><i style=\"color:#aaa;\">', 
                        IFNULL(
                          (
                            SELECT CONCAT(IFNULL(mhddatum,''),' ',IFNULL(charge,'') ) 
                            FROM mhd_log 
                            WHERE artikel = art.id AND zeit >= lpi.zeit 
                            ORDER BY id 
                            LIMIT 1
                          ),''
                        ),'</i>'
                      ),
                      IF(
                        art.chargenverwaltung > 0,
                        CONCAT(' <br /><i style=\"color:#aaa;\">',
                          IFNULL(
                            (
                              SELECT IFNULL(bezeichnung,'') 
                              FROM chargen_log 
                              WHERE artikel = art.id AND zeit >= lpi.zeit 
                              ORDER BY id 
                              LIMIT 1
                            ),
                          ''
                          ),'</i>'
                        ),
                        '')
                      )
                     ) as referenz, lpi.bearbeiter as bearbeiter, 
                     p.abkuerzung as projekt, DATE_FORMAT(api.zeitstempel,'%d.%m.%Y'),
                  lpi.id 
                  FROM lager_bewegung lpi
                  INNER JOIN `artikel` AS `art` ON lpi.artikel = art.id 
                  LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id 
                  LEFT JOIN projekt p ON lpi.projekt=p.id  
                  LEFT JOIN artikel_permanenteinventur api ON api.id=lpi.permanenteinventur";

        }
        // Fester filter
        $where = " lpi.artikel='$id'  ";

        // gesamt anzahl
        $count = "SELECT COUNT(lpi.id) FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  WHERE lpi.artikel='$id'";
        break;
      case 'stueckliste':
        $id = (int)$this->app->Secure->GetGET('id');
        $allowed['artikel'] = array('stueckliste');
        $defaultorder = 7;

        // headings
        $heading = array('Artikel', 'Nummer','Menge', 'Einheit', 'Lager', 'Reserviert', 'Men&uuml;');
        $width = array('50%', '10%', '5%', '5%','5%', '5%', '9%');
        $findcols = array('a.name_de', 'a.nummer', 's.menge','a.einheit',
          'lag.menge', 'CASE WHEN (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=a.id)  > 0
                THEN (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=a.id)
                ELSE 0
                END','id');
        $searchsql = array('a.name_de', 'a.nummer','a.einheit', 's.menge','s.art',"CONCAT(IF(s.art='' OR s.art='et','Einkaufsteil',''),IF(s.art='it','Informationsteil/Dienstleistung',''),IF(s.art='bt','Beistellung',''))");

        $menu = "<table cellpadding=0 cellspacing=0>";
          $menu .= "<tr>";
            $menu .= "<td>";
              $menu .= '<a href="javascript:;" onclick="StuecklisteNeuePositionEdit(%value%);" title="Bearbeiten">';
              //$menu = "<a href=\"index.php?module=artikel&action=editstueckliste&id=%value%\">";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
              $menu .= '</a>' . '&nbsp;';
              $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delstueckliste&id=%value%\"); title=\"L&ouml;schen\">";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
              $menu .= '</a>' . '&nbsp;';
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        $alignright = array(3,5,6);
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id,  
          CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\" target=\"_blank\">',a.name_de,'</a>&nbsp;',
          IF(s.art='it','<br><i style=color:#999>- Informationsteil/Dienstleistung</i>',''),IF(s.art='bt','<br><i style=color:#999>- Beistellung</i>',''), COALESCE((SELECT GROUP_CONCAT('<br><i style=color:#999>- ', art.nummer, ' ', art.name_de, ' (', alt.reason, ')', '</i>' SEPARATOR '') FROM parts_list_alternative AS alt INNER JOIN artikel AS art ON art.id = alt.alternative_article_id WHERE alt.parts_list_id = s.id), '')) as artikel,
              CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\" target=\"_blank\">',a.nummer,'</a>') as nummer,
              ".$this->app->erp->FormatMenge('s.menge').' as menge, a.einheit,

                '.$this->app->erp->FormatMenge('ifnull(lag.menge,0)').'  as lager, 

              CASE WHEN (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=a.id)  > 0 
                THEN (SELECT '.$this->app->erp->FormatMenge('SUM(lr.menge)')." FROM lager_reserviert lr WHERE lr.artikel=a.id)  
                ELSE 0
                END as reserviert, 

              s.id as menu
                FROM stueckliste s 
                INNER JOIN artikel a ON s.artikel=a.id 
                LEFT JOIN (SELECT sum(lpi.menge) as menge ,lpi.artikel
                  FROM lager_platz_inhalt AS lpi 
                  INNER JOIN (
                    SELECT artikel 
                    FROM stueckliste 
                    WHERE stuecklistevonartikel='$id' GROUP BY artikel
                  ) AS s2 ON lpi.artikel = s2.artikel
                  INNER JOIN lager_platz AS lp ON lpi.lager_platz = lp.id AND ifnull(lp.sperrlager,0) = 0
                  GROUP BY lpi.artikel) AS lag ON a.id = lag.artikel  
                ";

        // Fester filter
        $where = "s.stuecklistevonartikel='$id' ";

        // gesamt anzahl
        $count = "SELECT COUNT(s.id) FROM stueckliste s WHERE s.stuecklistevonartikel='$id' ";
        break;

      case 'parts_list_alternatives':
        $allowed['artikel'] = array('stueckliste');

        $heading = array('Nummer','Artikel','Men&uuml;');
        $width = array('40%','50%','5%');

        $findcols = array('a.nummer', 'a.name_de', 'p.id');
        $searchsql = array('a.nummer', 'a.name_de');

        $id = $app->Secure->GetGET('more_data1');

        $menu = "<table cellpadding=0 cellspacing=0>";
          $menu .= "<tr>";
            $menu .= "<td nowrap>";
              $menu .= '<a href="javascript:;" onclick="PartsListAlternativeEditEntry(%value%);">';
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
              $menu .= '</a>&nbsp;';
              $menu .= '<a href="javascript:;" onclick="PartsListAlternativeDelete(%value%);">';
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
              $menu .= '</a>';
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, a.nummer, CONCAT(a.name_de, IF(p.reason != '', CONCAT('<br><i style=color:#999>- Grund: ',p.reason,'</i>'),'')), p.id
                FROM parts_list_alternative p
                LEFT JOIN stueckliste s ON p.parts_list_id = s.id
                LEFT JOIN artikel a ON p.alternative_article_id = a.id";

        $where = " s.id = '$id'";

        break;

      case 'artikel_onlineshops':
        $id = $this->app->Secure->GetGET('id');
        $disablebuttons=true;
        $allowed['artikel'] = array('edit');
        $heading = array('Shop','aktiv','Men&uuml;');
        $width = array('50%','10%','1%');
        $findcols = array('ao.id','s.bezeichnung',"if(ao.aktiv = 1, 'ja','-')",'ao.id');
        $searchsql = array('s.bezeichnung',"if(ao.aktiv = 1, 'ja','-')");
        $sql = "SELECT SQL_CALC_FOUND_ROWS ao.id, concat(s.bezeichnung,'<span style=\"display:none;\" class=\"shopid\">',ao.shop,'</span>'), if(ao.aktiv = 1, 'ja','-'), ao.id 
        FROM artikel_onlineshops ao
        INNER JOIN shopexport s ON ao.shop = s.id AND ao.artikel = '$id'
        ";
        //$menu = "<table><tr><td nowrap><input type=\"submit\" value=\"Export\" name=\"shopexport_%value%\" onclick=\"this.form.action += &apos;#tabs-4&apos;;\"><input type=\"submit\" value=\"Import\" name=\"shopimport_%value%\" onclick=\"this.form.action += &apos;#tabs-4&apos;\">&nbsp;<span class=\"aftershop\" style=\"display:none;\">%value%</span><a href=\"#\" onclick=\"editonlineshop(%value%);\" style=\"position:relative;top:5px;\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=\"deleteonlineshop(%value%);\" style=\"position:relative;top:5px;\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a></td></tr></table>";
        $menu = '<table><tr><td nowrap class="idtd" data-id="%value%">'.
          "<input type=\"hidden\" name=\"shopexport_%value%\" value=\"\" /><img title=\"Artikel exportieren\" class=\"exportbutton hidden\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\" />&nbsp;".
          "<span class=\"placeholderexport hidden\"></span>".
          "<input type=\"hidden\" name=\"shopimport_%value%\" value=\"\" /><img title=\"Artikel importieren\" class=\"importbutton hidden\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/back.svg\" border=\"0\" />&nbsp;".
          "<span class=\"placeholderimport hidden\"></span>".
          "<span class=\"aftershop hidden\">%value%</span>".
          "<span class=\"placeholderaftershop\"></span>".
          "<a class=\"editbutton\" href=\"#\" onclick=\"editonlineshop(%value%);\" ><img title=\"Shopverkn&uuml;pfung editieren\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;".
          "<a class=\"delbutton\" href=\"#\" onclick=\"deleteonlineshop(%value%);\" ><img title=\"Shopverkn&uuml;pfung l&ouml;schen\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>".
          '</td></tr></table>';
        $where = ' ao.id > 0 '.$this->app->erp->ProjektRechte('s.projekt');
;
        $count = "select count(ao.id) FROM artikel_onlineshops ao INNER JOIN shopexport s ON ao.shop = s.id AND ao.artikel = '$id' WHERE $where";
        break;
      case 'artikel_zertifikate':
        $allowed['artikel'] = array('zertifikate');
        $id = (int)$this->app->Secure->GetGET('id');

        $heading = array('Datum','Kunden-Nr','Kunde','Artikelnummer','Artikel','Men&uuml;');
        $width = array('5%','8%','8%','8%','15%','1%');
        $alignright = array(7);
        $findcols = array('z.erstellt_datum','a.kundennummer','a.name','ar.nummer','ar.name_de','z.id');
        $searchsql = array('z.erstellt_datum','a.kundennummer','a.name','ar.nummer','ar.name_de','z.id');
        $menucol = 5;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zertifikatgenerator&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";
        $defaultorder = 0;
        $defaultorderdesc = 1;
        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id, 
        date_format(z.erstellt_datum,'%d.%m.%Y'),
        
        a.kundennummer,
        a.name,
        ar.nummer, 
        ar.name_de,
        z.id
          from zertifikatgenerator z
          inner join artikel ar on ar.id = z.artikel
          left join adresse a on z.adresse_kunde = a.id
        ";
        $where = "z.artikel = '$id'";
        $count = "SELECT count(z.id) from zertifikatgenerator z where z.artikel = '$id'";
        break;
      case 'artikeltabellebilder':
      case 'artikeltabelle':
        $begrenzen_artikeltabelle = $this->app->erp->Firmendaten('begrenzen_artikeltabelle');
        $bilder = false;
        if($name === 'artikeltabellebilder'){
          $bilder = true;
        }
        $dark = $this->app->erp->Firmendaten('iconset_dunkel')?1:0;
        $allowed['artikel'] = array('list');

        $filterbaum = $this->app->erp->Firmendaten('artikel_baum_uebersicht')?$this->app->User->GetParameter('filterbaum_artikel'):null;
        $baum = null;
        if($filterbaum)
        {
          $this->app->erp->GetArtikelunterbaumids($filterbaum, $baum);
        }
        $baumids = null;
        if($baum)
        {
          foreach($baum as $v)
          {
            $baumids[] = " kategorie = '$v' ";
          }
        }

        // headings
        $artikel_beschleunigte_suche = $this->app->erp->Firmendaten('artikel_beschleunigte_suche');
        $useLpiCache = $artikel_beschleunigte_suche || $begrenzen_artikeltabelle;

        $heading = array('');
        $width = array('1%');
        $findcols = array('open');
        if($bilder)
        {
          $heading[] = 'Bild';
          $width[] = '5%';
          $findcols[] = 'bildvorschau';
        }
        $heading = array_merge($heading, array('Nummer', 'Artikel'));
        $width = array_merge($width, array('10%', '55%'));
        $findcols = array_merge($findcols, array('nummer', 'name_de'));
        if($this->app->erp->RechteVorhanden("multilevel","list") && $this->app->erp->Firmendaten("modul_mlm")=="1")
        {
          $mlm = true;
          $heading[] = 'Provionspunkte';
          $width[] = '10%';
          $findcols[] = 'mlmpunkte';
        }
        $heading = array_merge($heading, array('Lagerbestand', 'Projekt'));
        $width = array_merge($width, array('5%', '15%'));

        //$lpicol = 'CAST((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as SIGNED)';
        $lpicol = '(SELECT TRIM(IFNULL(SUM(l.menge),0))+0 FROM lager_platz_inhalt l WHERE l.artikel=a.id)';
        $lpicol2 = $lpicol;
        if($useLpiCache) {
          $lpicol = '`cache`.lager_gesamt';
          $lpicol2 = 'trim(cache.lager_gesamt)+0';
        }

        $findcols = array_merge($findcols, array($lpicol, 'p.abkuerzung'));
        $alignright = array(count($heading)-1);
        $artikelzusatzfelder = $this->app->erp->getZusatzfelderArtikel();
        $artikelcachedfelder = $this->app->erp->getArtikelCachedfelder();
        $zusatzcols = null;
        $joincached = '';
        $joineig = '';
        for($i = 1; $i <= 5; $i++)
        {
          $zusatzfeld = $this->app->erp->Firmendaten('artikeltabellezusatz'.$i);
          if($zusatzfeld && isset($artikelzusatzfelder[$zusatzfeld]))
          {
            $heading[] = $artikelzusatzfelder[$zusatzfeld];
            $width[] = '10%';
            if(isset($artikelcachedfelder[$zusatzfeld]))
            {
              switch($artikelcachedfelder[$zusatzfeld])
              {
                case 'Menge':
                  if($zusatzfeld === 'lager_verfuegbar')
                  {
                    $zusatzcols[] = 'if(a.lagerartikel=\'1\','.$this->app->erp->FormatMenge('cache.' . $zusatzfeld).',\'\')';
                  }else{
                    $zusatzcols[] = $this->app->erp->FormatMenge('cache.' . $zusatzfeld);
                  }
                  $findcols[] = 'cache.'.$zusatzfeld;
                  $alignright[] = count($findcols);
                  $numbercols[] = count($findcols) - 1;
                  break;
                case 'Preis':
                  $findcols[] = 'ROUND(cache.'.$zusatzfeld.',2)';
                  $zusatzcols[] = $this->app->erp->FormatPreis('cache.'.$zusatzfeld,2);
                  $alignright[] = count($findcols);
                  $numbercols[] = count($findcols) - 1;
                  break;
                default:
                  $zusatzcols[] = 'cache.'.$zusatzfeld;
                  $findcols[] = 'cache.'.$zusatzfeld;
                  break;
              }
              $joincached = ' LEFT JOIN artikel_cached_fields `cache` ON a.id = `cache`.artikel ';
            }else{
              switch($zusatzfeld)
              {
                case 'vk_netto':
                case 'ek_netto':
                case 'vk_brutto':
                case 'ek_brutto':
                case 'lager_verfuegbar':
                  $findcols[] = 'a.id';
                  $zusatzcols[] = "''";
                  break;
                case 'eigenschaften':
                  /*$findcols[] = 'eig.value';
                  $zusatzcols[] = 'eig.value';
                  $joineig = "
                  LEFT JOIN (
                  SELECT aew.artikel, 
                   GROUP_CONCAT(DISTINCT concat(ae.name,': ',aew.wert)
                      ORDER BY ae.name  SEPARATOR ', ') as value
                   FROM artikeleigenschaftenwerte aew
                  INNER JOIN artikeleigenschaften ae ON aew.artikeleigenschaften = ae.id AND ifnull(ae.geloescht,0) = 0 AND ae.name != ''
                  GROUP BY aew.artikel
                  ) eig ON a.id = eig.artikel
                  ";*/
                  break;
                case 'inproduktion':

                  "(SELECT SUM(pos.menge) 
                    FROM produktion_position pos 
                    WHERE pos.status = 'gestartet' AND (pos.explodiert=1 or pos.id>0) 
                    GROUP BY pos.artikel
                    ORDER BY pos.sort, pos.id 
                    LIMIT 1)";

                  break;
                default:
                  $findcols[] = 'a.'.$zusatzfeld;
                  $zusatzcols[] = 'a.'.$zusatzfeld;
                  break;
              }
            }
          }
        }
        $useCacheEkNumbers = false;
        if($useLpiCache) {
          $joincached = ' LEFT JOIN artikel_cached_fields `cache` ON a.id = cache.artikel ';
          $this->app->DB->Select("SELECT ek_customnumber FROM artikel_cached_fields LIMIT 1");
          if(!$this->app->DB->error()) {
            $useCacheEkNumbers = true;
          }
        }

        $heading[] = 'Men&uuml;';
        $width[] = '1%';
        $findcols[] = 'a.id';
        $menucol = count($heading) - 1;

        /*
        $heading = array('', 'Bild', 'Nummer', 'Artikel', 'Lagerbestand', 'Projekt', 'Men&uuml;');
        $width = array('1%', '5%', '10%', '55%', '5%', '15%', '10%');
        $findcols = array('open', 'bildvorschau','nummer', 'name_de', 'CAST((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as SIGNED)', 'p.abkuerzung', 'id');
        $menucol = 6;
        $defaultorder = 7; //Optional wenn andere Reihenfolge gewuenscht
        $mlm = false;
        $alignright = array(5);
        if($this->app->erp->RechteVorhanden("multilevel","list") && $this->app->erp->Firmendaten("modul_mlm")=="1")
        {
          $mlm = true;
          $heading = array('', 'Bild', 'Nummer', 'Artikel','Provionspunkte', 'Lagerbestand', 'Projekt', 'Men&uuml;');
          $width = array('1%', '5%', '10%', '45%','10%', '5%', '15%', '10%');
          $findcols = array('open', 'bildvorschau','nummer', 'name_de','mlmpunkte', 'CAST((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as SIGNED)', 'p.abkuerzung', 'id');
          $menucol = 7;
          $defaultorder = 8;
          $alignright = array(6);
        }*/
        $this->app->Tpl->Add('JQUERYREADY', "$('#f_lagerartikel').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#f_keinlagerartikel').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#f_variante').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#f_stueckliste').click( function() { fnFilterColumn4( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#f_keinestueckliste').click( function() { fnFilterColumn5( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#f_gesperrt').click( function() { fnFilterColumn6( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#f_variantekeine').click( function() { fnFilterColumn7( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#f_alleartikel').click( function() { fnFilterColumn8( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#f_nurlagerndeartikel').click( function() { fnFilterColumn9( 0 ); } );");
        for ($r = 1;$r <= 9;$r++) {
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

        // START EXTRA more
        $more_data1 = $this->app->Secure->GetGET('more_data1');
        $more_data2 = $this->app->Secure->GetGET('more_data2');
        $more_data3 = $this->app->Secure->GetGET('more_data3');
        $more_data4 = $this->app->Secure->GetGET('more_data4');
        $more_data5 = $this->app->Secure->GetGET('more_data5');
        $more_data6 = $this->app->Secure->GetGET('more_data6');
        $more_data7 = $this->app->Secure->GetGET('more_data7');
        $more_data8 = $this->app->Secure->GetGET('more_data8');
        $more_data9 = $this->app->Secure->GetGET('more_data9');

        if ($this->app->erp->Firmendaten('iconset_dunkel')) {
          $str = '<img src="./themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/keinbild_dunkel.png" width=50>';
        } else {
          $str = '<img src="./themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/keinbild_hell.png" width=50>';
        }
        $loading = '<img src="./images/ui-anim_basic_16x16.gif" width=50>';

        $defaultorderdesc = 1;
        $isArticleDescriptionSearchEnabled = $this->app->erp->Firmendaten('artikel_suche_kurztext') == '1';
        if ($isArticleDescriptionSearchEnabled) {
          $searchsql = array('a.nummer','a.name_de','p.abkuerzung','a.kurztext_de', 'a.hersteller', 'a.herstellernummer', 'a.ean', 'a.anabregs_text');
        } else {
          $searchsql = array('a.nummer','a.name_de','p.abkuerzung', 'a.hersteller', 'a.herstellernummer', 'a.ean');
        }
        if($this->app->erp->Firmendaten('artikel_suche_variante_von') == '1'){
          $searchsql[] = '(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1)';
        }

        if($this->app->erp->Firmendaten('artikel_freitext1_suche')){
          $searchsql[] = 'a.freifeld1';
        }
        if($this->app->erp->Firmendaten('artikel_freitext1_suche')){
          $searchsql[] = 'a.freifeld2';
        }
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=artikel&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"javascript:;\" onclick=\"ArtikelCopyEdit(%value%);\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"4\" data-label-reference-id=\"%value%\" data-label-reference-table=\"artikel\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        if($artikel_beschleunigte_suche && $isArticleDescriptionSearchEnabled){
          $searchsql = ['a.nummer','a.name_de','a.ean', 'a.anabregs_text'];
        }
        elseif($artikel_beschleunigte_suche) {
          $searchsql = ['a.nummer','a.name_de','a.ean'];
        }
        // SQL statement
        $sql = "SELECT 
               a.id, 
              '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 
              ";
    
        if($bilder) {
          if(!function_exists('imagejpeg')) {
            $sql .= "'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/icon_img_error.png title=\"Keine GD-Erweiterung installiert\" />' as bild,";
          }
          else{
            $sql .="
            IF(
              a.bildvorschau = 'KEINBILD',
              '$str',
              CONCAT(
                '<img class=\"thumbnails',
                if(a.bildvorschau <> '','',' tocheck'),'\" src=\"index.php?module=artikel&action=thumbnail&id=',
                a.id,
                '&projectid=',IFNULL(a.projekt,0),'&dark="
                .$dark."&bildvorschau=',a.bildvorschau,'\" style=\"max-width:50px;max-height:50px;\">'
              )
            ) as bild,";
          }
        }

        $parameter = $this->app->User->GetParameter('table_filter_artikel');
        $parameter = base64_decode($parameter);
        $parameter = json_decode($parameter, true);

        $joins = '';
        for($i = 1; $i <= 3; $i++) {
          if((isset($parameter['eigenschaftname'.$i]) && $parameter['eigenschaftname'.$i] !== '')
            || (isset($parameter['eigenschaftwert'.$i]) && $parameter['eigenschaftwert'.$i] !== '')
          ) {
            $joins .= ' INNER JOIN artikeleigenschaftenwerte aw'.$i.' ON a.id = aw'.$i.'.artikel 
                INNER JOIN artikeleigenschaften ae'.$i.' ON ae'.$i.'.id = aw'.$i.'.artikeleigenschaften AND (ae1.geloescht = 0 OR isnull(ae'.$i.".geloescht))
                ";
            if(isset($parameter['eigenschaftname'.$i]) && $parameter['eigenschaftname'.$i] !== ''){
              $paramsArray[] = 'ae'.$i.".name LIKE '".$parameter['eigenschaftname'.$i]."' ";
            }
            if(isset($parameter['eigenschaftwert'.$i]) && $parameter['eigenschaftwert'.$i] !== ''){
              $paramsArray[] = 'aw'.$i.".wert LIKE '".$parameter['eigenschaftwert'.$i]."' ";
            }
          }
        }

        $joins .= $joincached;
        $joins .= $joineig;

        if($more_data9 == 1){
          $joins .= ' INNER JOIN (SELECT artikel, SUM(menge) AS menge FROM lager_platz_inhalt WHERE menge>0 GROUP BY artikel) AS lpi ON lpi.artikel = a.id';
        }

        $sql .= "
              a.nummer as nummer, 
              CONCAT(
                IF(a.intern_gesperrt,'<strike>',''),
                name_de,
                IF(a.variante AND a.variante_von > 0,CONCAT(' <a href=\"index.php?module=artikel&action=edit&id=',a.variante_von,'\"><font color=#848484>(Variante von ',IFNULL((SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),''),')</font></a>'),''),
                IF(a.intern_gesperrt,'</strike>','') 
              ) AS name_de,        
              ".(!empty($mlm)?" a.mlmpunkte, ":'')."
              if(a.lagerartikel=1,$lpicol2,'') as lagerbestand,  
              p.abkuerzung as projekt, ";
        if($zusatzcols) {
          $sql .= implode(', ', $zusatzcols).', ';
          foreach($zusatzcols as $v) {
            if(!in_array($v, $searchsql)){
              $searchsql[] = $v;
            }
          }
        }

        $sql .= '  a.id as menu 
                FROM  artikel a
                ';
        if($baumids) {
          $sql .= ' LEFT JOIN (SELECT artikel FROM `artikelbaum_artikel` WHERE ('.implode(' OR ', $baumids).") GROUP BY artikel) aa ON a.id = aa.artikel ";
        }

        $sql .=  "LEFT JOIN projekt p ON p.id=a.projekt $joins ";


        // fester filter

        //if(a.variante,CONCAT('Variante von ',(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),': ',a.name_de),a.name_de)

        //$where = "a.geloescht=0 ".$this->app->erp->ProjektRechte();
        $where = 'a.geloescht=0 ' . $this->app->erp->ProjektRechte('a.projekt');
        $fastcount = " SELECT count(a.id) FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt $joins ";
        $extracountjoin = '';
        if($baumids)
        {
          $extracountjoin = ' LEFT JOIN (SELECT artikel FROM `artikelbaum_artikel` WHERE ('.implode(' OR ', $baumids).") GROUP BY artikel) aa ON a.id = aa.artikel ";

          $fastcount .= $extracountjoin;
          $where .= " AND ( NOT isnull(aa.artikel) OR a.typ = '".$filterbaum."_kat' ) ";
        }



        /* STAMMDATEN */
        if(isset($parameter['name']) && !empty($parameter['name'])) {
          $paramsArray[] = "a.name_de LIKE '%".$parameter['name']."%' ";
        }

        if(isset($parameter['nummer']) && !empty($parameter['nummer'])) {
          $paramsArray[] = "a.nummer LIKE '%".$parameter['nummer']."%' ";
        }

        if(isset($parameter['hersteller']) && !empty($parameter['hersteller'])) {
          $paramsArray[] = "a.hersteller LIKE '%".$parameter['hersteller']."%' ";
        }

        if($more_data1 == 1 || ($more_data1 != 1 && !empty($parameter['lagerartikel']))) {
          $paramsArray[] = 'a.lagerartikel = 1 ';
        }

        if($more_data2 == 1)
        {
          $paramsArray[] = 'a.lagerartikel <> 1 ';
        }

        if(!empty($parameter['variante']) || $more_data3 == 1) {
          $paramsArray[] = 'a.variante = 1 ';
        }

        if($more_data7 == 1) {
          $paramsArray[] = ' (a.variante <> 1 OR isnull(a.variante)) ';
        }



        if(isset($parameter['freigabenotwending']) && !empty($parameter['freigabenotwending'])) {
          $paramsArray[] = 'a.freigabenotwendig = 1 ';
        }

        if(isset($parameter['abverkauf']) && !empty($parameter['abverkauf'])) {
          $paramsArray[] = 'a.restmenge > 0 ';
        }


        if(isset($parameter['standardlieferant']) && !empty($parameter['standardlieferant'])) {
          if(isset($parameter['standardlieferant']) && !empty($parameter['standardlieferant'])) {
            $lieferant = $this->app->DB->Select('
              SELECT
                id
              FROM
                adresse
              WHERE
                lieferantennummer = "' . reset(explode(' ',trim($parameter['standardlieferant']))) . '" AND geloescht = 0 LIMIT 1
            ');

            $paramsArray[] = "a.adresse = '" . $lieferant . "'";
          }
        }

        if($more_data4 == 1 || !empty($parameter['stueckliste'])) {
          $paramsArray[] = 'a.stueckliste > 0 ';
        }

        if($more_data5 == 1)
        {
          $paramsArray[] = 'a.stueckliste = 0 ';
        }

        if(isset($parameter['justintimestueckliste']) && !empty($parameter['justintimestueckliste'])) {
          $paramsArray[] = 'a.juststueckliste > 0 ';
        }

        if(isset($parameter['inventursperre']) && !empty($parameter['inventursperre'])) {
          $paramsArray[] = 'a.inventursperre > 0 ';
        }


        if(!empty($parameter['gesperrt']) || $more_data6 == 1) {
          $paramsArray[] = '(a.intern_gesperrt > 0) ';
        }else{
          $paramsArray[] = '(a.intern_gesperrt = 0) ';
        }

        if(empty($parameter['unikat'])) {
          $paramsArray[] = '(a.unikat = 0) ';
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

        if(isset($parameter['freifeld1']) && !empty($parameter['freifeld1'])) {
          $paramsArray[] = "a.freifeld1 LIKE '%".$parameter['freifeld1']."%' ";
        }

        if(isset($parameter['internebemerkung']) && !empty($parameter['internebemerkung'])) {
          $paramsArray[] = "a.internerkommentar LIKE '%".$parameter['internebemerkung']."%' ";
        }

        if(isset($parameter['ean']) && !empty($parameter['ean'])) {
          $paramsArray[] = "a.ean LIKE '%" . $parameter['ean'] . "%' ";
        }

        if(isset($parameter['shop']) && !empty($parameter['shop'])) {
          $shop = explode(' ', $parameter['shop']);
          $shop = (int)reset($shop);
          $paramsArray[] = " ((a.shop = '" . $shop . "' OR a.shop2 = '" . $shop . "' OR a.shop3 = '" . $shop . "') OR a.id IN (SELECT artikel FROM artikel_onlineshops WHERE shop = '" . $shop . "')) ";
        }

        if(isset($parameter['herstellernummer']) && !empty($parameter['herstellernummer'])) {
          $paramsArray[] = "a.herstellernummer LIKE '%" . $parameter['herstellernummer'] . "%' ";
        }

        if(isset($parameter['typ']) && !empty($parameter['typ'])) {

          $artikelkategorie = explode(" ", $parameter['typ']);
          $artikelkategorieid = $artikelkategorie[0];
          $artikelkategorieid = $app->DB->Select("SELECT id FROM artikelkategorien WHERE id = '$artikelkategorieid' LIMIT 1");
          if($artikelkategorieid != ''){
            $artikelkategorie = $artikelkategorieid;
          }else{
            $artikelkategorie = 0;
          }

          if($artikelkategorie > 0){
            $joinartikelbaum = ' LEFT JOIN artikelbaum_artikel aba ON a.id = aba.artikel';
            $sql .= $joinartikelbaum;
            $groupby = ' GROUP BY a.id ';
          }

        }

        if ($paramsArray) {
          $where .= ' AND ' . implode(' AND ', $paramsArray);
        }

        if($more_data8){
          $where = 'a.geloescht=0 ' . $this->app->erp->ProjektRechte();
        }


        $moreinfo = true;
        $count = "SELECT COUNT(DISTINCT a.id) FROM artikel a $joins";
        $extrawhere = '';

        if($baumids)
        {
          $count .= ' LEFT JOIN (SELECT artikel FROM `artikelbaum_artikel` WHERE ('.implode(' OR ', $baumids).') GROUP BY artikel) aa ON a.id = aa.artikel ';
          $extrawhere = " AND ( NOT isnull(aa.artikel) OR a.typ = '".$filterbaum."_kat' ) ";
        }


      if($artikelkategorie > 0){
        $count .= $joinartikelbaum;
        $where .= " AND (aba.kategorie = '$artikelkategorie' OR a.typ = '".$artikelkategorie."_kat') ";
        $extrawhere .= " AND (aba.kategorie = '$artikelkategorie' OR a.typ = '".$artikelkategorie."_kat') ";
        $fastcount .= $joinartikelbaum;
      }


      if($this->app->erp->Firmendaten('artikel_artikelnummer_suche') == '1'){

        /*$maxEinkauf = $this->app->DB->Select(
          "SELECT MAX(ct) as mx FROM(
                SELECT artikel, COUNT(bestellnummer) as ct FROM einkaufspreise
                WHERE bestellnummer IS NOT NULL 
                AND bestellnummer !=''
                GROUP BY artikel
            ) as data");

        $maxVerkauf = $this->app->DB->Select(
          "SELECT MAX(ct) as mx FROM(
                SELECT artikel, COUNT(kundenartikelnummer) as ct FROM verkaufspreise
                WHERE kundenartikelnummer IS NOT NULL 
                AND kundenartikelnummer !=''
                GROUP BY artikel
            ) as data");

        for($i=0;$i<$maxEinkauf;$i++){
          $searchsql[] = '(SELECT bestellnummer FROM einkaufspreise e WHERE e.artikel=a.id AND bestellnummer != "" LIMIT '.$i.',1)';
        }

        for($i=0;$i<$maxVerkauf;$i++){
          $searchsql[] = '(SELECT kundenartikelnummer FROM verkaufspreise v WHERE v.artikel=a.id AND kundenartikelnummer != "" LIMIT '.$i.',1)';
        }*/

        if($useCacheEkNumbers){
          $searchsql[] = 'cache.ek_customnumber';
          $searchsql[] = 'cache.vk_customnumber';
        }
        else{
          $sql .= " LEFT JOIN einkaufspreise AS ekpr ON a.id = ekpr.artikel AND IFNULL(ekpr.bestellnummer,'') <> '' 
        AND (IFNULL(ekpr.gueltig_bis,'0000-00-00')='0000-00-00' OR ekpr.gueltig_bis >= CURDATE()) ";
          $sql .= " LEFT JOIN verkaufspreise AS vkpr ON a.id = vkpr.artikel AND IFNULL(vkpr.kundenartikelnummer,'') <> ''
         AND (IFNULL(vkpr.gueltig_bis,'0000-00-00')='0000-00-00' OR vkpr.gueltig_bis >= CURDATE())";

          $count .= " LEFT JOIN einkaufspreise AS ekpr ON a.id = ekpr.artikel AND IFNULL(ekpr.bestellnummer,'') <> '' 
        AND (IFNULL(ekpr.gueltig_bis,'0000-00-00')='0000-00-00' OR ekpr.gueltig_bis >= CURDATE()) ";

          $count .= " LEFT JOIN verkaufspreise AS vkpr ON a.id = vkpr.artikel AND IFNULL(vkpr.kundenartikelnummer,'') <> ''
         AND (IFNULL(vkpr.gueltig_bis,'0000-00-00')='0000-00-00' OR vkpr.gueltig_bis >= CURDATE())";

          $fastcount .=" LEFT JOIN einkaufspreise AS ekpr ON a.id = ekpr.artikel AND IFNULL(ekpr.bestellnummer,'') <> '' 
        AND (IFNULL(ekpr.gueltig_bis,'0000-00-00')='0000-00-00' OR ekpr.gueltig_bis >= CURDATE()) ";

          $fastcount .= " LEFT JOIN verkaufspreise AS vkpr ON a.id = vkpr.artikel AND IFNULL(vkpr.kundenartikelnummer,'') <> ''
         AND (IFNULL(vkpr.gueltig_bis,'0000-00-00')='0000-00-00' OR vkpr.gueltig_bis >= CURDATE())";
          $searchsql[] = " IFNULL(ekpr.bestellnummer,'') ";
          $searchsql[] = " IFNULL(vkpr.kundenartikelnummer,'') ";
          $groupby = ' GROUP BY a.id ';
          if(!empty($joincached)){
            $groupby .= ' ,cache.id ';
          }
        }
      }
      else {
        $groupby = '';
      }

        $count .= " WHERE a.geloescht=0 $extrawhere " . $this->app->erp->ProjektRechte('a.projekt');
        $cached_count = 'SELECT COUNT(id) FROM artikel WHERE geloescht = 0 AND intern_gesperrt <> 1 '.$this->app->erp->ProjektRechte('projekt');
        break;
      case 'artikel_offenebestellungen':
        $id = (int)$this->app->Secure->GetGET('id');
        $heading = array('Datum', 'Bestellung Nr.', 'Nummer', 'Menge', 'Geliefert', 'VPE','Einzelpreis','Gesamtpreis','Lieferant', 'Name', 'Lieferdatum', 'Status Bestellung', 'Men&uuml;');
        $width = array('10%', '10%', '10%', '10%', '10%', '10%', '10%', '10%', '10%','10%','10%', '10%', '1%');
        $findcols = array('b.datum', 'b.belegnr', 'bp.bestellnummer', 'bp.menge', 'bp.geliefert', 'bp.vpe', 'bp.preis','bp.preis*bp.menge', 'a.lieferantennummer', 'a.name', "if(bp.lieferdatum!='0000-00-00', bp.lieferdatum,'sofort')", 'b.status', 'bp.bestellung', 'b.id');
        $searchsql = array("DATE_FORMAT(b.datum,'%d.%m.%Y')", 'b.belegnr', 'bp.bestellnummer', $this->app->erp->FormatMenge("bp.menge"), $this->app->erp->FormatMenge("bp.geliefert"), 'bp.vpe', 'a.lieferantennummer', 'a.name', "if(bp.lieferdatum!='0000-00-00', DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort')", 'b.status', 'bp.bestellung', $this->app->erp->FormatPreis("bp.preis",2), $this->app->erp->FormatPreis("bp.preis*bp.menge",2));

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $menu = "<a href=\"index.php?module=bestellung&action=pdf&id=%value%\">";
        $menu .= '<img src="./themes/new/images/pdf.svg" border="0">';
        $menu .= '</a>&nbsp;';
        $menu .= "<a href=\"index.php?module=bestellung&action=edit&id=%value%\">";
        $menu .= '<img src="./themes/new/images/edit.svg" border="0">';
        $menu .= '</a>';

        $where = " artikel='$id' AND b.status!='storniert'" . $this->app->erp->ProjektRechte("b.id");

        $alignright=array(4,5,6,7,8);
        $numbercols = array(3,4,5,6,7);
        $datecols = array(10);

        $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, DATE_FORMAT(b.datum,'%d.%m.%Y') as datum, b.belegnr, bp.bestellnummer, ".$this->app->erp->FormatMenge("bp.menge").", ".$this->app->erp->FormatMenge("bp.geliefert").",bp.vpe, ".$this->app->erp->FormatPreis("bp.preis",2).",".$this->app->erp->FormatPreis("bp.preis*bp.menge",2).",a.lieferantennummer, a.name, if(bp.lieferdatum!='0000-00-00', DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, b.status, bp.bestellung, b.id FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN adresse a ON b.adresse=a.id";

        //$orderby = " ORDER by b.datum DESC";

        break;
      case 'artikel_belege':
        $id = (int)$this->app->Secure->GetGET('id');
        $heading = array('Beleg','Belegnr','Datum','Status','Zahlweise','Kd- / Lf-Nr.','Kunde / Lieferant','Bezeichnung','Menge','Geliefert','Preis' , 'Men&uuml;','');
        $width = array('5%','5%','5%','5%','5%','5%','15%','20%','5%','5%','5%', '1%','1%');
        $findcols = array('b.typ', 'b.belegnr' , 'b.datum','b.status','b.zahlungsweise','b.kundennummer','b.name','b.bezeichnung','b.menge','b.geliefert','b.preis','b.id','b.id');
        $searchsql = array('b.typ', 'b.belegnr' , "DATE_FORMAT(b.datum, '%d.%m.%Y')",'b.status','b.zahlungsweise','b.kundennummer','b.name','b.bezeichnung', $this->app->erp->FormatPreis('preis',2), $this->app->erp->FormatMenge('b.menge'), $this->app->erp->FormatMenge('b.geliefert'));
        $fstatusoffen = $this->app->YUI->TableSearchFilter($name, 1, 'statusoffen', '0',0,'checkbox');
        $fstatusabgeschlossen = $this->app->YUI->TableSearchFilter($name, 2, 'statusabgeschlossen', '0',0,'checkbox');
        $fauftrag = $this->app->YUI->TableSearchFilter($name, 3, 'auftrag', '0',0,'checkbox');
        $frechnung = $this->app->YUI->TableSearchFilter($name, 4, 'rechnung', '0',0,'checkbox');
        $fgutschrift = $this->app->YUI->TableSearchFilter($name, 5, 'gutschrift', '0',0,'checkbox');
        $fangebot = $this->app->YUI->TableSearchFilter($name, 6, 'angebot', '0',0,'checkbox');
        $flieferschein = $this->app->YUI->TableSearchFilter($name, 7, 'lieferschein', '0',0,'checkbox');
        $fproduktion = $this->app->YUI->TableSearchFilter($name, 8, 'produktion', '0',0,'checkbox');
        $fbestellung = $this->app->YUI->TableSearchFilter($name, 9, 'bestellung', '0', 0, 'checkbox');
        if(!$fstatusoffen && !$fstatusabgeschlossen)
        {
          $fstatusoffen = 1;
          $fstatusabgeschlossen = 1;
        }
        if(!$fauftrag && !$frechnung && !$fgutschrift && !$fangebot && !$flieferschein && !$fproduktion && !$fbestellung)
        {
          $fauftrag = 1;
          $frechnung = 1;
          $fgutschrift = 1;
          $fangebot = 1;
          $flieferschein = 1;
          $fproduktion = 1;
          $fbestellung = 1;
        }

        $numbercols = array(8,9,10);

        if($fauftrag && $this->app->erp->RechteVorhanden('auftrag','list'))
        {
          $sqla[] = "(SELECT a.id, 'auftrag' as typ ,a.belegnr, a.datum, a.status, a.zahlungsweise, a.kundennummer, a.name, ap.bezeichnung, ifnull(sum(ap.menge),0) as menge,ifnull(sum(ap.geliefert_menge),0) as geliefert,ifnull(ap.preis*(100-ap.rabatt)/100,0) as preis 
                 FROM auftrag a INNER JOIN auftrag_position ap ON a.id = ap.auftrag AND ap.artikel = '$id'
               WHERE a.id > 0 ".(($fstatusabgeschlossen && !$fstatusoffen)?" AND a.status = 'abgeschlossen' ":(($fstatusoffen && !$fstatusabgeschlossen)?" AND a.status != 'abgeschlossen' AND a.status != 'storniert' ":""))."
          ".$this->app->erp->ProjektRechte('a.projekt').'  GROUP BY a.id)';
        }
        if($frechnung && $this->app->erp->RechteVorhanden('rechnung','list'))
        {
          $sqla[] = "(SELECT a.id, 'rechnung' as typ ,a.belegnr, a.datum, a.status, a.zahlungsweise, a.kundennummer, a.name,ap.bezeichnung, ifnull(sum(ap.menge),0) as menge,0 as geliefert,ifnull(ap.preis*(100-ap.rabatt)/100,0) as preis 
                 FROM rechnung a INNER JOIN rechnung_position ap ON a.id = ap.rechnung AND ap.artikel = '$id'
               WHERE a.id > 0 ".(($fstatusabgeschlossen && !$fstatusoffen)?" AND a.status = 'abgeschlossen' ":(($fstatusoffen && !$fstatusabgeschlossen)?" AND a.status != 'abgeschlossen'  ":""))."
          ".$this->app->erp->ProjektRechte('a.projekt').'    GROUP BY a.id )';
        }
        if($fgutschrift && $this->app->erp->RechteVorhanden('gutschrift','list'))
        {
          $sqla[] = "(SELECT a.id, 'gutschrift' as typ ,a.belegnr, a.datum, a.status, a.zahlungsweise, a.kundennummer, a.name,ap.bezeichnung, ifnull(sum(ap.menge),0) as menge,0 as geliefert,ifnull(ap.preis*(100-ap.rabatt)/100,0) as preis 
                 FROM gutschrift a INNER JOIN gutschrift_position ap ON a.id = ap.gutschrift AND ap.artikel = '$id'
               WHERE a.id > 0 ".(($fstatusabgeschlossen && !$fstatusoffen)?" AND a.status = 'abgeschlossen' ":(($fstatusoffen && !$fstatusabgeschlossen)?" AND a.status != 'abgeschlossen' AND a.status != 'storniert' ":""))."
          ".$this->app->erp->ProjektRechte('a.projekt').'   GROUP BY a.id  )';
        }
        if($flieferschein && $this->app->erp->RechteVorhanden('lieferschein','list'))
        {
          $sqla[] = "(SELECT a.id, 'lieferschein' as typ ,a.belegnr, a.datum, a.status, '' as zahlungsweise, a.kundennummer, a.name,ap.bezeichnung, ifnull(sum(ap.menge),0) as menge,ifnull(sum(ap.geliefert),0) as geliefert,0 as preis 
                 FROM lieferschein a INNER JOIN lieferschein_position ap ON a.id = ap.lieferschein AND ap.artikel = '$id'
               WHERE a.id > 0 ".(($fstatusabgeschlossen && !$fstatusoffen)?" AND a.status = 'versendet' ":(($fstatusoffen && !$fstatusabgeschlossen)?" AND a.status != 'versendet' AND a.status != 'storniert' ":""))."
          ".$this->app->erp->ProjektRechte('a.projekt').'    GROUP BY a.id )';
        }
        if($fangebot && $this->app->erp->RechteVorhanden('angebot','list'))
        {
          $sqla[] = "(SELECT a.id, 'angebot' as typ ,a.belegnr, a.datum, a.status, a.zahlungsweise, a.kundennummer, a.name,ap.bezeichnung, ifnull(sum(ap.menge),0) as menge,ifnull(sum(ap.geliefert),0) as geliefert,ifnull(ap.preis*(100-ap.rabatt)/100,0) as preis  
                 FROM angebot a INNER JOIN angebot_position ap ON a.id = ap.angebot AND ap.artikel = '$id'
               WHERE a.id > 0 ".(($fstatusabgeschlossen && !$fstatusoffen)?" AND a.status = 'abgeschlossen' ":(($fstatusoffen && !$fstatusabgeschlossen)?" AND a.status != 'abgeschlossen' AND a.status != 'storniert' ":""))."
          ".$this->app->erp->ProjektRechte('a.projekt').'   GROUP BY a.id  )';
        }
        if($fbestellung && $this->app->erp->RechteVorhanden('bestellung','list'))
        {
          $sqla[] = "(SELECT a.id, 'bestellung' as typ ,a.belegnr, a.datum, a.status, a.zahlungsweise, a.lieferantennummer as kundennummer, a.name, ap.bezeichnunglieferant as bezeichnung, ifnull(sum(ap.menge),0) as menge,ifnull(sum(ap.geliefert),0) as geliefert,ifnull(ap.preis,0) as preis  
                 FROM bestellung a INNER JOIN bestellung_position ap ON a.id = ap.bestellung AND ap.artikel = '$id'
               WHERE a.id > 0 ".(($fstatusabgeschlossen && !$fstatusoffen)?" AND a.status = 'abgeschlossen' ":(($fstatusoffen && !$fstatusabgeschlossen)?" AND a.status != 'abgeschlossen' AND a.status != 'storniert' ":""))."
          ".$this->app->erp->ProjektRechte('a.projekt').'    GROUP BY a.id )';
        }
        if($fproduktion && $this->app->erp->ModulVorhanden('produktion') && $this->app->erp->RechteVorhanden('produktion','list'))
        {
          $sqla[] = "(SELECT a.id, 'produktion' as typ ,a.belegnr, a.datum, a.status, a.zahlungsweise, a.kundennummer, a.name, ap.bezeichnung, ifnull(sum(ap.menge),0) as menge,ifnull(sum(ap.geliefert),0) as geliefert,ifnull(ap.preis,0) as preis  
                 FROM produktion a INNER JOIN produktion_position ap ON a.id = ap.produktion AND ap.artikel = '$id'
               WHERE a.id > 0 ".(($fstatusabgeschlossen && !$fstatusoffen)?" AND a.status = 'abgeschlossen' ":(($fstatusoffen && !$fstatusabgeschlossen)?" AND a.status != 'abgeschlossen' AND a.status != 'storniert' ":""))."
          ".$this->app->erp->ProjektRechte('a.projekt').'   GROUP BY a.id  )';
        }

        $where = 'b.id > 0';
        if(!isset($sqla))
        {
          $sqla[] = "(SELECT 0, '' as typ ,a.belegnr, a.datum, a.status, a.zahlungsweise, a.kundennummer, a.name, ap.bezeichnung, ifnull(sum(ap.menge),0) as menge,ifnull(sum(ap.geliefert_menge),0) as geliefert,ifnull(ap.preis*(100-ap.rabatt)/100,0) as preis 
                 FROM auftrag a INNER JOIN auftrag_position ap ON a.id = ap.auftrag AND ap.artikel = '$id'
               WHERE a.id < 0
              )";
          $where = '0';
        }


        $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=0;
        $alignright = array(9,10,11);
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, b.typ,b.belegnr, DATE_FORMAT(b.datum, '%d.%m.%Y'), b.status, b.zahlungsweise, b.kundennummer, b.name, b.bezeichnung, ".$this->app->erp->FormatMenge("b.menge").", ".$this->app->erp->FormatMenge("b.geliefert").", ".$this->app->erp->FormatPreis("b.preis",2).",
        concat('<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=',b.typ,'&action=edit&id=',b.id,'\" target\"_blank\" ><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=',b.typ,'&action=pdf&id=',b.id,'\" target\"_blank\" ><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>'),''
            FROM (            
              ".implode(' UNION ALL ', $sqla).'
            )b
        
        ';
        break;
      case 'instueckliste':
        $allowed['artikel'] = array('instueckliste');
        $id = (int)$this->app->Secure->GetGET('id');
        // headings
        $heading = array('Artikel', 'Nummer', 'Menge', 'Men&uuml;');
        $width = array('70%', '10%', '5%', '10%');
        $findcols = array('artikel', 'nummer', 'menge', 'id');
        $searchsql = array('a.name_de', 'a.nummer', 's.menge');
        $defaultorder = 4;
        $defaultorderdesc = 1;
        $alignright = array(3);
        $menu = "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";

        // SQL statement

        if (!empty($this->app->Conf->WFdbType) && $this->app->Conf->WFdbType == 'postgre') {
          $sql = 'SELECT s.id, a.name_de as artikel,a.nummer as nummer, trim(s.menge)+0 as menge, 
                CASE WHEN (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0
                THEN (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id)
                ELSE 0
                END  as lager, s.artikel as menu
                FROM stueckliste s LEFT JOIN artikel a ON s.artikel=a.id ';
        } else {
          $sql = 'SELECT SQL_CALC_FOUND_ROWS s.id, a.name_de as artikel,a.nummer as nummer, trim(s.menge)+0 as menge,
                s.stuecklistevonartikel
                  as menu
                  FROM stueckliste s LEFT JOIN artikel a ON s.stuecklistevonartikel=a.id ';
        }

        // Fester filter
        $where = "s.artikel='$id' ";

        // gesamt anzahl
        $count = "SELECT COUNT(s.id) FROM stueckliste s WHERE s.stuecklistevonartikel='$id' ";
        break;
      case 'artikel_etiketten':
        $allowed['artikel'] = array('etiketten');
        $artikelId = (int)$this->app->Secure->GetGET('id');
        // headings
        $heading = array('Etikett', 'Menge', 'Drucker', 'Art', 'Men&uuml;');
        $width = array('30%', '10%', '20%', '20%', '5%');

        $findcols = array('e.name', 'al.amount', 'd.name', 'al.type', 'al.id');
        $searchsql = array('e.name', 'al.amount', 'd.name', 'al.type');

        $defaultorder = 0;
        $defaultorderdesc = 1;

        $alignright = array(2);

        $menu = "<table cellpadding=0 cellspacing=0>";
        $menu .= "<tr>";
        $menu .= "<td nowrap>";
        $menu .= '<a href="javascript:;" onclick="ArticleLabelEdit(%value%);">';
        $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>&nbsp;";
        $menu .= '<a href="javascript:;" onclick="ArticleLabelDelete(%value%);">';
        $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        $sql = "SELECT SQL_CALC_FOUND_ROWS al.id, e.name, al.amount, d.name, CONCAT(UCASE(LEFT(al.type, 1)), SUBSTRING(al.type, 2)), al.id
                FROM article_label al
                LEFT JOIN etiketten e ON al.label_id = e.id
                LEFT JOIN drucker d ON al.printer_id = d.id";

        $where = "al.article_id = '$artikelId'";

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
  public function __construct($app, $intern = false) {
    //parent::GenArtikel($app);
    $this->app=$app;
    if($intern){
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArtikelCreate");
    $this->app->ActionHandler("demo","ArtikelDemo");
    $this->app->ActionHandler("edit","ArtikelEdit");
    $this->app->ActionHandler("list","ArtikelList");
    $this->app->ActionHandler("newlist","ArtikelNewList");
    $this->app->ActionHandler("stueckliste","ArtikelStueckliste");
    $this->app->ActionHandler("stuecklisteimport","ArtikelStuecklisteImport");
    $this->app->ActionHandler("stuecklisteupload","ArtikelStuecklisteUpload");
    $this->app->ActionHandler("instueckliste","ArtikelInStueckliste");
    $this->app->ActionHandler("delstueckliste","DelStueckliste");
    $this->app->ActionHandler("stuecklisteempty","ArtikelStuecklisteEmpty");
    $this->app->ActionHandler("stuecklisteexport","ArtikelStuecklisteExport");
    $this->app->ActionHandler("upstueckliste","UpStueckliste");
    $this->app->ActionHandler("downstueckliste","DownStueckliste");
    $this->app->ActionHandler("editstueckliste","ArtikelStuecklisteEditPopup");
    $this->app->ActionHandler("verkauf","ArtikelVerkauf");
    $this->app->ActionHandler("copy","ArtikelCopy");
    $this->app->ActionHandler("copyedit", "ArtikelCopyEdit");
    $this->app->ActionHandler("copysave", "ArtikelCopySave");
    $this->app->ActionHandler("schliessen","ArtikelSchliessen");
    $this->app->ActionHandler("verkaufcopy","ArtikelVerkaufCopy");
    $this->app->ActionHandler("verkaufdelete","ArtikelVerkaufDelete");
    $this->app->ActionHandler("verkaufdisable","ArtikelVerkaufDisable");
    $this->app->ActionHandler("einkauf","ArtikelEinkauf");
    $this->app->ActionHandler("einkaufdelete","ArtikelEinkaufDelete");
    $this->app->ActionHandler("einkaufdisable","ArtikelEinkaufDisable");
    $this->app->ActionHandler("einkaufcopy","ArtikelEinkaufCopy");
    $this->app->ActionHandler("projekte","ArtikelProjekte");
    $this->app->ActionHandler("rabatt","ArtikelRabatt");
    $this->app->ActionHandler("lager","ArtikelLager");
    $this->app->ActionHandler("mindesthaltbarkeitsdatum","ArtikelMHD");
    $this->app->ActionHandler("mhddelete","ArtikelMHDDelete");
    $this->app->ActionHandler("bestbeforeadd","ArticleBestbeforeAdd");
    $this->app->ActionHandler("bestbeforereduce","ArticleBestbeforeReduce");
    $this->app->ActionHandler("batchadd","ArticleBatchAdd");
    $this->app->ActionHandler("batchreduce","ArticleBatchReduce");
    $this->app->ActionHandler("chargedelete","ArtikelChargeDelete");
    $this->app->ActionHandler("chargen","ArtikelChargen");
    $this->app->ActionHandler("wareneingang","ArtikelWareneingang");
    $this->app->ActionHandler("offenebestellungen","ArtikelOffeneBestellungen");
    $this->app->ActionHandler("belege","ArtikelBelege");
    $this->app->ActionHandler("statistik","ArtikelStatistik");
    $this->app->ActionHandler("offeneauftraege","ArtikelOffeneAuftraege");
    $this->app->ActionHandler("dateien","ArtikelDateien");

    $this->app->ActionHandler("eigenschaften","ArtikelEigenschaften");
    $this->app->ActionHandler("eigenschaftensuche","ArtikelEigenschaftenSuche");
    $this->app->ActionHandler("eigenschaftendelete","ArtikelEigenschaftenDelete");
    $this->app->ActionHandler("eigenschaftencopy","ArtikelEigenschaftenCopy");
    $this->app->ActionHandler("eigenschafteneditpopup","ArtikelEigenschaftenEditPopup");

    $this->app->ActionHandler("provision","Artikelprovision");
    $this->app->ActionHandler("delete","ArtikelDelete");
    $this->app->ActionHandler("auslagern","ArtikelAuslagern");
    $this->app->ActionHandler("einlagern","ArtikelEinlagern");
    $this->app->ActionHandler("umlagern","ArtikelUmlagern");
    $this->app->ActionHandler("ausreservieren","ArtikelAusreservieren");
    $this->app->ActionHandler("etiketten","ArtikelEtiketten");
    $this->app->ActionHandler("reservierung","ArtikelReservierung");
    $this->app->ActionHandler("onlineshop","ArtikelOnlineShop");
    $this->app->ActionHandler("ajaxwerte","ArtikelAjaxWerte");
    $this->app->ActionHandler("profisuche","ArtikelProfisuche");
    $this->app->ActionHandler("lagerlampe","ArtikelLagerlampe");
    $this->app->ActionHandler("shopexport","ArtikelShopexport");
    $this->app->ActionHandler("shopimport","ArtikelShopimport");
    $this->app->ActionHandler("shopexportfiles","ArtikelShopexportFiles");
    $this->app->ActionHandler("stuecklisteetiketten","ArtikelStuecklisteEtiketten");
    $this->app->ActionHandler("minidetail","ArtikelMiniDetail");

    $this->app->ActionHandler("artikelfreifelder", "ArtikelFreifelder");
    $this->app->ActionHandler("artikelfreifelderedit", "ArtikelFreifelderEdit");
    $this->app->ActionHandler("artikelfreifeldersave", "ArtikelFreifelderSave");
    $this->app->ActionHandler("artikelfreifelderdelete", "ArtikelFreifelderDelete");

    $this->app->ActionHandler("lagersync","ArtikelLagerSync");
    $this->app->ActionHandler("thumbnail", "ArtikelThumbnail");
    $this->app->ActionHandler("schnellanlegen", "ArtikelSchnellanlegen");

    $this->app->ActionHandler("baum", "ArtikelBaum");
    $this->app->ActionHandler("baumajax", "ArtikelBaumAjax");
    $this->app->ActionHandler("baumdetail", "ArtikelBaumDetail");
    $this->app->ActionHandler("baumedit","ArtikelBaumEdit");

    $id = $this->app->Secure->GetGET('id');
    $nummer = $this->app->Secure->GetPOST('nummer');

    if(is_numeric($id) && $id > 0)
    {
      $arr = $this->app->DB->SelectRow("SELECT CONCAT(name_de,' (',nummer,')') as name2, name_de,nummer FROM artikel WHERE id='$id' LIMIT 1");
      if(!empty($arr)){
        $artikel = $arr['name2'];
        $nummer = $arr['nummer'];
        $namede = $arr['name_de'];
      }
    } 
    else{
      $artikel = $nummer;
    }

    if($artikel!=''){
      $this->app->Tpl->SetText('UEBERSCHRIFT', 'Artikel: ' . $artikel);
    }
    else {
      $this->app->Tpl->SetText('UEBERSCHRIFT','Artikel');
    }

    $this->app->Tpl->SetText('ANZEIGENUMMER',$nummer);
    if(isset($namede)){
      $this->app->Tpl->SetText('ANZEIGENAMEDE',' '.$this->app->erp->LimitChar($namede,65));
    }
    $this->app->Tpl->Set('FARBE','[FARBE1]');

    $this->app->erp->Headlines('Artikel');

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }

  public function ArtikelRabatt()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $id = $this->app->Secure->GetGET('id');
    $rabatt = $this->app->Secure->GetGET('rabatt');
    $fmodul = $this->app->Secure->GetGET('fmodul');
    $rabatt = str_replace(',','.',$rabatt);

    if($cmd==='angebot' || $cmd==='auftrag' || $cmd==='rechnung' || $cmd==='gutschrift')
    {
      $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM $cmd WHERE id='$id'");
      if($schreibschutz!='1')
      {
        $this->app->DB->Update(
          "UPDATE `{$cmd}_position` AS `pos`
          INNER JOIN `artikel` AS `art` ON pos.artikel = art.id
          SET pos.rabatt='{$rabatt}',
          pos.keinrabatterlaubt = 1 
          WHERE pos.{$cmd} = {$id}
          AND (art.keinrabatterlaubt = 0 OR art.keinrabatterlaubt IS NULL)"
        );
      }
    }
    $this->app->Location->execute('index.php?module='.$cmd.'&action=positionen&id='.$id.'&fmodul='.$fmodul);
  }

  public function ArtikelDemo()
  {
    $projekt = $this->app->erp->Firmendaten('projekt');
    if(class_exists('EtikettenPDFCustom'))
    {
      $Brief = new EtikettenPDFCustom($this->app,$projekt);
    }else{
      $Brief = new EtikettenPDF($this->app,$projekt);
    }
    $Brief->SetXML('<label><settings width="30" height="15" distance="3" offsetx="0" offsety="16" />
                <barcode y="1" x="3" size="4" type="1">NUMMER</barcode>
                <line x="3" y="7" size="2">NR NUMMER</line>
                <line x="3" y="10" size="2">NAME_DE</line>
                </label>
    ');
    $Brief->Output('test.pdf','D');
    $this->app->ExitXentral();
  }
  
  public function ArtikelEinkaufAdd()
  {
    $bezeichnunglieferant = '';
    $artikel = (int)$this->app->Secure->GetPOST('artikel');
    $lieferant = $this->app->Secure->GetPOST('lieferant');
    $waehrung = $this->app->Secure->GetPOST('waehrung');
    $bestellnummer = $this->app->Secure->GetPOST('bestellnummer');
    $ab_menge = (float)str_replace(',','.',$this->app->Secure->GetPOST('ab_menge'));
    $preis = (float)str_replace(',','.',$this->app->Secure->GetPOST('preis'));
    if(!$artikel) {
      echo json_encode(array('status'=>0,'error'=>'Kein Artikel'));
      $this->app->ExitXentral();
    }
    $lieferantennummera = explode(' ',$lieferant);
    $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer <> '' AND lieferantennummer = '".$this->app->DB->real_escape_string($lieferantennummera[0])."' LIMIT 1");
    if(!$adresse) {
      echo json_encode(array('status'=>0,'error'=>'Lieferant nicht gefunden'));
      $this->app->ExitXentral();
    }
    $artikelprojekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id = '$artikel' LIMIT 1");
    if($artikelprojekt && !$this->app->erp->UserProjektRecht($artikelprojekt))
    {
      echo json_encode(array('status'=>0,'error'=>'Fehlende Projektrechte'));
      $this->app->ExitXentral();
    }
    $adressprojekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id = '$adresse' LIMIT 1");
    if($adressprojekt && !$this->app->erp->UserProjektRecht($adressprojekt)) {
      echo json_encode(array('status'=>0,'error'=>'Fehlende Projektrechte'));
      $this->app->ExitXentral();
    }
    if($ab_menge < 0) {
      echo json_encode(array('status'=>0,'error'=>'Menge ist kleiner 0'));
      $this->app->ExitXentral();
    }
    $this->app->erp->AddEinkaufspreis($artikel,$ab_menge,$adresse,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);
    echo json_encode(array('status'=>1,'html'=>$this->app->erp->EinkaufspreiseTabelle($artikel)));
    $this->app->ExitXentral();
  }
  
  public function ArtikelEigenschaften()
  {
    if(!$this->app->DB->Select('SELECT id FROM eigenschaften LIMIT 1'))// $this->app->erp->ModulVorhanden('artikeleigenschaften'))
    {
      $id = $this->app->Secure->GetGET('id');
      $cmd = $this->app->Secure->GetGET('cmd');
      if($cmd === 'save') {
        $name = $this->app->Secure->GetPOST('name');
        $wert = trim($this->app->Secure->GetPOST('wert'));
        $einheit = $this->app->Secure->GetPOST('einheit');
        $status = 0;
        if($name)// && $wert != "")
        {
          $status = 1;
          $checkkategorie = $this->app->DB->Select("SELECT id FROM artikeleigenschaften WHERE name = '$name' AND geloescht <> 1 LIMIT 1");
          if(!$checkkategorie)
          {
            $this->app->DB->Insert("INSERT INTO artikeleigenschaften (name) values ('$name')");
            $checkkategorie = $this->app->DB->GetInsertID();
          }
          $checkwert = $this->app->DB->Select("SELECT id FROM artikeleigenschaftenwerte WHERE artikeleigenschaften = '$checkkategorie' AND artikel = '$id' AND wert = '$wert' LIMIT 1");
          if(!$checkwert)$this->app->DB->Insert("INSERT INTO artikeleigenschaftenwerte (wert, artikeleigenschaften, artikel, einheit) values ('$wert','$checkkategorie','$id','$einheit')");
        }
        
        echo json_encode(array('status'=>$status));
        $this->app->ExitXentral();
      }
      if($cmd === 'get') {
        $eigenschaftid = (int)$this->app->Secure->GetPOST('eigenschaftid');
        $erg = $this->app->DB->SelectRow("SELECT ew.id, ew.wert, ew.einheit, e.name, ew.artikeleigenschaften, e.typ, ew.vorlage FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften
        WHERE ew.id = '$eigenschaftid' AND ew.artikel = '$id' AND e.geloescht <> 1 LIMIT 1");

        if(!$erg['vorlage']){
          $erg['typ'] = 'einzeilig';
        }

        if($erg['typ'] === 'select'){
          $tmp = $this->app->DB->SelectArr("SELECT wert FROM eigenschaften_vorlagen_werte WHERE name = '".$erg['name']."' AND vorlage='".$erg['vorlage']."'");
          $erlaubteWerte = array();
          foreach ($tmp as $wert){
            $erlaubteWerte[] = $wert['wert'];
          }
          $erg['erlaubtewerte'] = $erlaubteWerte;
        }

        echo json_encode($erg);
        $this->app->ExitXentral();
      }
      if($cmd === 'update')
      {
        $name = trim($this->app->Secure->GetPOST('e_name'));
        $wert = trim($this->app->Secure->GetPOST('e_wert'));
        $einheit = trim($this->app->Secure->GetPOST('e_einheit'));
        $eigenschaftid = (int)$this->app->Secure->GetPOST('eigenschaftid');


        $error = "";

        if($name == ""){
          $error .= "Bitte Eigenschaft ausfüllen\n";
        }else{
          $artikeleigenschaftid = $this->app->DB->Select("SELECT id FROM artikeleigenschaften WHERE name = '$name' AND geloescht = 0 LIMIT 1");
          if($artikeleigenschaftid == "" || $artikeleigenschaftid <= 0){
            $this->app->DB->Insert("INSERT INTO artikeleigenschaften (name) VALUES ('$name')");
            $artikeleigenschaftid = $this->app->DB->Select("SELECT id FROM artikeleigenschaften WHERE name = '$name' LIMIT 1");
          }
        }

        if($wert == ""){
          $error .= "Bitte Wert ausfüllen\n";
        }

        $eigenschaftvorhanden = $this->app->DB->Select("SELECT ew.id FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften WHERE ew.id != '$eigenschaftid' AND ew.artikel = '$id' AND ew.artikeleigenschaften = '$artikeleigenschaftid' AND ew.wert = '$wert' AND e.geloescht = 0 LIMIT 1");
        
        if($eigenschaftvorhanden != "" && $eigenschaftvorhanden > 0){
          $error .= 'Eigenschaft ist bereits vorhanden.';
        }


        if($error == ''){
          if($id && $eigenschaftid){
            $this->app->DB->Update("UPDATE artikeleigenschaftenwerte SET artikeleigenschaften = '$artikeleigenschaftid', wert = '$wert', einheit = '$einheit' WHERE id = '$eigenschaftid' LIMIT 1");

            echo json_encode(array('status'=>1));
            $this->app->ExitXentral();
          }
          if($id){
            $this->app->DB->Insert("INSERT INTO artikeleigenschaftenwerte (artikeleigenschaften, wert, einheit, artikel) 
              VALUES ('$artikeleigenschaftid', '$wert', '$einheit', '$id')");

            echo json_encode(array('status'=>1));
            $this->app->ExitXentral();
          }
          echo json_encode(array('status'=>0,'statusText'=>"Fehler"));
          $this->app->ExitXentral();
        }
        echo json_encode(array('status'=>0,'statusText'=>$error));
        $this->app->ExitXentral();
      }

      if($cmd === 'copy'){
        
        $name = trim($this->app->Secure->GetPOST('e_name'));
        $wert = trim($this->app->Secure->GetPOST('e_wert'));
        $einheit = trim($this->app->Secure->GetPOST('e_einheit'));
        $eigenschaftid = (int)$this->app->Secure->GetPOST('eigenschaftid');


        $error = "";

        if($name == ""){
          $error .= "Bitte Eigenschaft ausfüllen\n";
        }else{
          $artikeleigenschaftid = $this->app->DB->Select("SELECT id FROM artikeleigenschaften WHERE name = '$name' AND geloescht = 0 LIMIT 1");
          if($artikeleigenschaftid == "" || $artikeleigenschaftid <= 0){
            $this->app->DB->Insert("INSERT INTO artikeleigenschaften (name) VALUES ('$name')");
            $artikeleigenschaftid = $this->app->DB->Select("SELECT id FROM artikeleigenschaften WHERE name = '$name' LIMIT 1");
          }
        }

        if($wert == ""){
          $error .= "Bitte Wert ausfüllen\n";
        }

        $eigenschaftvorhanden = $this->app->DB->Select("SELECT ew.id FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften WHERE ew.id != '$eigenschaftid' AND ew.artikel = '$id' AND ew.artikeleigenschaften = '$artikeleigenschaftid' AND ew.wert = '$wert' AND e.geloescht = 0 LIMIT 1");
        
        if($eigenschaftvorhanden != "" && $eigenschaftvorhanden > 0){
          $error .= 'Eigenschaft ist bereits vorhanden.';
        }


        if($error == ''){
          if($id && $eigenschaftid == 0){
            $this->app->DB->Insert("INSERT INTO artikeleigenschaftenwerte (artikeleigenschaften, wert, einheit, artikel) 
              VALUES ('$artikeleigenschaftid', '$wert', '$einheit', '$id')");

            echo json_encode(array('status'=>1));
            $this->app->ExitXentral();
          }
          echo json_encode(array('status'=>0,'statusText'=>"Fehler"));
          $this->app->ExitXentral();
        }
        echo json_encode(array('status'=>0,'statusText'=>$error));
        $this->app->ExitXentral();
      }
      if($cmd === 'delete'){
        $eigenschaftid = (int)$this->app->Secure->GetPOST('eigenschaftid');
        $eigenschaftid = $this->app->DB->Select("SELECT id FROM artikeleigenschaftenwerte 
            WHERE artikel='$id' AND id='$eigenschaftid' LIMIT 1");
        $status = 0;
        if($eigenschaftid > 0)
        {
          $this->app->DB->Delete("DELETE FROM artikeleigenschaftenwerte WHERE id = '$eigenschaftid'");
          $status = 1;
        }
        echo json_encode(array('status'=>$status,'statusText'=>'Fehler'));
        $this->app->ExitXentral();
      }
      if($cmd === 'getuebersetzung'){

        $uebersetzungid = (int)$this->app->Secure->GetPOST('id');
          
        $data = $this->app->DB->SelectRow("SELECT ap.id, ap.article_id, ap.language_to, ap.property_to, ap.property_value_to, 
            ap.language_from, ap.property_from, ap.property_value_from, ap.shop_id 
            FROM article_property_translation ap 
            WHERE ap.id = '$uebersetzungid' AND ap.article_id = '$id' LIMIT 1");
          
        if($data){
          if($data['article_id'] > 0){
            $articleNumber = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '".$data['article_id']."' LIMIT 1");
            $articleName = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '".$data['article_id']."' LIMIT 1");
            if($articleNumber != "" && $articleName != ""){
              $data['article'] = $articleNumber." ".$articleName;
            }
          }

          if($data['shop_id'] > 0){
            $shopName = $this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id = '".$data['shop_id']."' LIMIT 1");
            $data['shop'] = $data['shop_id']." ".$shopName;
          }else{
            $data['shop'] = '';
          }        

        }else{
          $data['id'] = 0;
          $articleNumber = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$id' LIMIT 1");
          $articleName = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '$id' LIMIT 1");
          if($articleNumber != "" && $articleName != ""){
            $data['article'] = $articleNumber." ".$articleName;
          }else{
            $data['article'] = '';
          }
          $data['language_to'] = '';
          $data['property_to'] = '';
          $data['property_value_to'] = '';
          $data['language_from'] = '';
          $data['property_from'] = '';
          $data['property_value_from'] = '';
          $data['shop'] = '';
        }

        echo json_encode($data);
        $this->app->ExitXentral();
      }
      if($cmd === 'saveuebersetzung'){

        $eintragid = (int)$this->app->Secure->GetPOST('id');
        $languageTo = trim($this->app->Secure->GetPOST('languageTo'));
        $propertyTo = trim($this->app->Secure->GetPOST('propertyTo'));
        $propertyValueTo = trim($this->app->Secure->GetPOST('propertyValueTo'));
        $languageFrom = trim($this->app->Secure->GetPOST('languageFrom'));
        $propertyFrom = trim($this->app->Secure->GetPOST('propertyFrom'));
        $propertyValueFrom = trim($this->app->Secure->GetPOST('propertyValueFrom'));
        $shop = trim($this->app->Secure->GetPOST('shop'));

        $error = "";
        
        if($id != ""){
          $id = (int)$this->app->DB->Select("SELECT id FROM artikel WHERE id = '$id' LIMIT 1");
          if($id === 0){
            $error .= "Bitte gültigen Artikel auswählen"."\n";
          }
        }

        if($shop != ""){
          $shop = explode(' ', $shop);
          $shopId = $shop[0];
          if($shopId != ""){
            $shopId = (int)$this->app->DB->Select("SELECT id FROM shopexport WHERE id = '$shopId' LIMIT 1");
            if($shopId === 0){
              $error .= "Bitte gültigen Shop auswählen"."\n";
            }
          }
        }else{
          $shopId = 0;
        }

        if($propertyTo == "" && $propertyValueTo == ""){
          $error .= "Bitte Eigenschaft oder Wert der Zielsprache ausfüllen"."\n";
        }
        
        //shop bedenken
        $uebersetzungschonvorhanden = $this->app->DB->Select("SELECT id FROM article_property_translation WHERE article_id = '$id' AND language_from = '$languageFrom' AND language_to = '$languageTo' AND property_from = '$propertyFrom' AND property_to = '$propertyTo' AND property_value_from = '$propertyValueFrom' AND property_value_to = '$propertyValueTo' AND id != '$eintragid' LIMIT 1");
        if($uebersetzungschonvorhanden != "" && $uebersetzungschonvorhanden > 0){
          $error .= "Diese Übersetzung gibt es bereits für diesen Artikel.\n";
        }

        if($error == ''){

          if($languageFrom === 'DE'){
            if($propertyFrom != ''){
              $propertyFromExists = $this->app->DB->Select("SELECT id FROM artikeleigenschaften WHERE name = '$propertyFrom' LIMIT 1");
              if(!$propertyFromExists){
                $this->app->DB->Insert("INSERT INTO artikeleigenschaften (name) VALUES ('$propertyFrom')");
              }
            }
          }

          if($eintragid){
            $this->app->DB->Update("UPDATE article_property_translation SET article_id = '$id', language_to = '$languageTo', 
              property_to = '$propertyTo', property_value_to = '$propertyValueTo', language_from = '$languageFrom', 
              property_from = '$propertyFrom', property_value_from = '$propertyValueFrom', shop_id = '$shopId' 
              WHERE id = '$eintragid'");

            echo json_encode(array('status'=>1));
            $this->app->ExitXentral();
          }
          $eigenschaftvorhanden = $this->app->DB->Select("SELECT aw.id FROM artikeleigenschaftenwerte aw JOIN artikeleigenschaften ae ON aw.artikeleigenschaften = ae.id WHERE aw.wert = '$propertyValueFrom' AND ae.name = '$propertyFrom' AND aw.artikel = '$id' LIMIT 1");
          if($eigenschaftvorhanden == "" || $eigenschaftvorhanden <= 0){
            $eigenschaftsid = $this->app->DB->Select("SELECT id FROM artikeleigenschaften WHERE name = '$propertyFrom' LIMIT 1");
            $this->app->DB->Insert("INSERT INTO artikeleigenschaftenwerte (artikeleigenschaften, wert, artikel) VALUES ('$eigenschaftsid', '$propertyValueFrom', '$id')");
          }


          $this->app->DB->Insert("INSERT INTO article_property_translation (article_id, language_to, property_to, property_value_to, 
            language_from, property_from, property_value_from, shop_id) 
            VALUES ('$id', '$languageTo', '$propertyTo', '$propertyValueTo', '$languageFrom', '$propertyFrom', 
            '$propertyValueFrom', '$shopId')");

          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }
        echo json_encode(array('status'=>0,'statusText'=>$error));
        $this->app->ExitXentral();
      }
      if($cmd === 'deleteuebersetzung'){
        $eintragid = (int)$this->app->Secure->GetPOST('id');
        if($eintragid){
          $this->app->DB->Update("DELETE FROM article_property_translation WHERE id = '$eintragid'");
        }
            
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }

      //VORLAGE LADEN
      $laden = $this->app->Secure->GetPOST('laden');
      if($laden){
        $vorlage = $this->app->Secure->GetPOST('vorlage');
        if(trim($vorlage) !== ''){

          $vorlagenid = $this->app->DB->Select("SELECT id FROM eigenschaften_vorlagen WHERE bezeichnung = '$vorlage'");
          $query = sprintf("SELECT evw.name, evw.wert, evw.einheit, ae.id FROM eigenschaften_vorlagen_werte evw 
            LEFT JOIN artikeleigenschaften ae ON evw.name = ae.name 
            WHERE evw.vorlage=%d AND (ISNULL(ae.name) OR ae.typ<>'select' 
              AND NOT ae.name IN(SELECT name FROM artikeleigenschaften WHERE geloescht=0 AND typ='select'))
            GROUP BY ae.name, evw.wert, evw.einheit",
            $vorlagenid);
          $alleigenschaften = $this->app->DB->SelectArr($query);
          $eigenschaftenNurSelect= $this->app->DB->SelectArr("SELECT evw.name, evw.wert, evw.einheit, ae.id FROM eigenschaften_vorlagen_werte evw JOIN artikeleigenschaften ae ON evw.name = ae.name WHERE ae.geloescht = 0 AND evw.vorlage='$vorlagenid' AND ae.typ='select' GROUP BY evw.name");
          foreach ($eigenschaftenNurSelect as $eigenschaft){
            $alleigenschaften[] = $eigenschaft;
          }

          foreach($alleigenschaften as $eigenschaft){
            $eigenschaftid = $this->app->DB->Select("SELECT id FROM artikeleigenschaften WHERE id='$eigenschaft[id]' LIMIT 1");
            if(!$eigenschaftid){
              $this->app->DB->Insert("INSERT INTO artikeleigenschaften (name) values ('".$eigenschaft['name']."')");
              $eigenschaftid = $this->app->DB->GetInsertID();
            }
            $checkwert = $this->app->DB->Select("SELECT id FROM artikeleigenschaftenwerte WHERE artikeleigenschaften = '$eigenschaftid' AND artikel = '$id' AND wert = '".$eigenschaft['wert']."' LIMIT 1");
            if(!$checkwert){
              $this->app->DB->Insert("INSERT INTO artikeleigenschaftenwerte (wert, artikeleigenschaften, artikel, einheit, vorlage) values ('".$eigenschaft['wert']."','$eigenschaftid','$id','".$eigenschaft['einheit']."', '$vorlagenid')");
            }
          }
        }
      }

      $sprachen = $this->app->DB->SelectArr("SELECT iso, bezeichnung_de FROM sprachen");
      $select = '';
      foreach($sprachen as $key=>$value){
        $select .= "<option value='".$value['iso']."'>".$value['bezeichnung_de']."</option>";           
      }
      $this->app->Tpl->Add("SPRACHEN", $select);

      $this->app->YUI->AutoComplete('u_propertyFrom', 'artikeleigenschaften');
      $this->app->YUI->AutoComplete('u_propertyValueFrom', 'artikeleigenschaftenwerte');
      $this->app->YUI->AutoComplete('u_shop', 'shopnameid');

      $this->app->YUI->AutoComplete('e_einheit','artikeleinheit');
      $this->app->YUI->AutoComplete('e_name','artikeleigenschaften');
      $this->app->YUI->AutoComplete('e_textwert', 'artikeleigenschaftenwerte');
      $this->app->YUI->AutoComplete('vorlage','eigenschaften_vorlagen');
      
      $this->ArtikelMenu();
      $this->app->YUI->TableSearch('TAB1','artikel_eigenschaften_neu', 'show','','',basename(__FILE__), __CLASS__);
      $this->app->Tpl->Parse('PAGE','artikel_artikeleigenschaften.tpl');

    }else{

      $this->app->Tpl->Add('UEBERSCHRIFT',' (Verkauf)');
      $this->app->Tpl->Set('SUBSUBHEADING','Verkaufspreise');    $this->ArtikelMenu();
      $id = $this->app->Secure->GetGET('id');    // neues arbeitspaket

      $this->app->YUI->AutoComplete('einheit','artikeleinheit');
      $this->app->YUI->AutoComplete('einheit2','artikeleinheit');
      $this->app->YUI->AutoComplete('einheit3','artikeleinheit');

      $widget = new WidgetEigenschaften($this->app,'TAB2');
      $widget->form->SpecialActionAfterExecute('none',
          "index.php?module=artikel&action=eigenschaften&id=$id");
      if($this->app->Secure->GetPOST('submit')!=''){
        $this->app->erp->EnableTab('tabs-2');
      }

      $widget->Create();

      $this->app->YUI->TableSearch('TAB1','eigenschaften', 'show','','',basename(__FILE__), __CLASS__);
      $this->app->Tpl->Parse('PAGE','eigenschaftenuebersicht.tpl');
    }
  }

  public function CheckRights()
  {
    if($this->app->User->GetType()==='admin'){
      return true;
    }
    $id = (int)$this->app->Secure->GetGET('id');

    $action = $this->app->Secure->GetGET('action');
    if($id && ($action == 'verkaufdisable' || $action == 'verkaufcopy' || $action == 'verkaufdelete' || $action == 'verkaufeditpopup'))
    {
      $projekt = $this->app->DB->Select("SELECT a.projekt FROM artikel a INNER JOIN verkaufspreise v on v.artikel = a.id WHERE v.id = '$id' LIMIT 1");
      if($projekt){
        return $this->app->erp->UserProjektRecht($projekt);
      }
    }elseif($id && ($action == 'einkaufdisable' || $action == 'einkaufcopy' || $action == 'einkaufdelete' || $action == 'einkaufeditpopup'))
    {
      $projekt = $this->app->DB->Select("SELECT a.projekt FROM artikel a INNER JOIN einkaufspreise e on e.artikel = a.id WHERE e.id = '$id' LIMIT 1");
      if($projekt){
        return $this->app->erp->UserProjektRecht($projekt);
      }
    }
    return true;
  }

  public function ArtikelEigenschaftenEditPopup()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->Tpl->Set('OPENDISABLE','<!--');
    $this->app->Tpl->Set('CLOSEDISABLE','-->');

    $this->app->YUI->AutoComplete('einheit','artikeleinheit');
    $this->app->YUI->AutoComplete('einheit2','artikeleinheit');
    $this->app->YUI->AutoComplete('einheit3','artikeleinheit');

    $sid = $this->app->DB->Select("SELECT artikel FROM eigenschaften WHERE id='$id' LIMIT 1");
    $this->ArtikelMenu($sid);

    $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=eigenschaften&id=$sid';\">");
    $widget = new WidgetEigenschaften($this->app,'TAB1');
    $widget->form->SpecialActionAfterExecute('close_refresh',
        "index.php?module=artikel&action=eigenschaften&id=$sid&&22#tabs-1");
    $widget->Edit();
    $this->app->Tpl->Add('TAB2','Sie bearbeiten gerade eine Eigenschaft. Erst nach dem Speichern k&ouml;nnen neue Preise angelegt werden.');
    $this->app->Tpl->Add('TAB3','Sie bearbeiten gerade eine Eigenschaft. Erst nach dem Speichern k&ouml;nnen Statistiken betrachtet werden.');
    $this->app->Tpl->Parse('PAGE','eigenschaftenuebersicht.tpl');
  }


  public function ArtikelEigenschaftenDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    $sid = $this->app->DB->Select("SELECT artikel FROM eigenschaften WHERE id='$id' LIMIT 1");
    if($id > 0){
      $this->app->DB->Delete("DELETE FROM eigenschaften WHERE id='$id' LIMIT 1");
    }
    $this->app->Location->execute('index.php?module=artikel&action=eigenschaften&id='.$sid);
  }

  public function ArtikelLagerSync()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->DB->Update("UPDATE artikel SET cache_lagerplatzinhaltmenge='-100' WHERE id='$id'");
    }
    $sync =  $this->app->erp->LagerSync($id,true);
    if($sync==1) {
      echo 'gruen';
    }
    else {
      echo 'gelb';
    }
    $this->app->ExitXentral();
  }

  function Preisrechner()
  {
    $steuer_normal_komma = ($this->app->erp->GetStandardSteuersatzNormal() + 100)/100.0;
    $steuer_ermaessigt_komma = ($this->app->erp->GetStandardSteuersatzErmaessigt() + 100)/100.0;

    $this->app->Tpl->Set('PREISRECHNER',"<input style='width:5em' type=\"button\" class=\"button button-secondary\" value=\"+".$this->app->erp->GetStandardSteuersatzNormal()."\" onclick=\"this.form.preis.value=parseFloat(this.form.preis.value.split(',').join('.'))*$steuer_normal_komma;\">");
    $this->app->Tpl->Add('PREISRECHNER',"<input style='width:5em' type=\"button\" class=\"button button-secondary\" value=\"-".$this->app->erp->GetStandardSteuersatzNormal()."\" onclick=\"this.form.preis.value=parseFloat(this.form.preis.value.split(',').join('.'))/$steuer_normal_komma;\">");
    if($this->app->erp->Version()!=='stock')
    {
      $this->app->Tpl->Add('PREISRECHNER',"<br><input style='width:5em' type=\"button\" class=\"button button-secondary\" value=\"+".$this->app->erp->GetStandardSteuersatzErmaessigt()."\" onclick=\"this.form.preis.value=parseFloat(this.form.preis.value.split(',').join('.'))*$steuer_ermaessigt_komma;\">");
      $this->app->Tpl->Add('PREISRECHNER',"<input style='width:5em' type=\"button\" class=\"button button-secondary\" value=\"-".$this->app->erp->GetStandardSteuersatzErmaessigt()."\" onclick=\"this.form.preis.value=parseFloat(this.form.preis.value.split(',').join('.'))/$steuer_ermaessigt_komma;\">");
    }
  }


  public function ArtikelMiniDetail($parsetarget='',$menu=true)
  {
    $id=$this->app->Secure->GetGET('id');

    $this->app->Tpl->Set('ID',$id);

    $artikelarr = $this->app->DB->SelectRow("SELECT * FROM artikel WHERE id='$id' LIMIT 1");
    $kurztext_de = '';
    $name_de = '';
    $nummer = '';
    $lagerartikel = 0;
    $lager_platz = 0;
    $standardbild = 0;
    if(!empty($artikelarr)) {
      $kurztext_de = $artikelarr['anabregs_text'];
      //$kurztext_de = !empty($artikelarr['kurztext_de'])?$artikelarr['kurztext_de']:$artikelarr['anabregs_text'];
      $name_de = $artikelarr['name_de'];
      $nummer = $artikelarr['nummer'];
      $lagerartikel = $artikelarr['lagerartikel'];
      $lager_platz = $artikelarr['lager_platz'];
      $standardbild = $this->app->erp->GetArtikelStandardbild($id,true);
    }

    $this->app->Tpl->Set('NAME_DE',$name_de);
    $this->app->Tpl->Set('NUMMER',$nummer);

    if($standardbild > 0) {
      //index.php?module=artikel&amp;action=thumbnail&amp;id=2&amp;bildvorschau=62_100_100
      //$this->app->Tpl->Set('ARTIKELBILD', "<img src=\"index.php?module=dateien&action=send&id=$standardbild\" align=\"left\" width=\"200\" style=\"margin-right:10px; margin-bottom:10px;\">");
      $this->app->Tpl->Set('ARTIKELBILD',
        '<img alt="Artikelbild" src="index.php?module=artikel&action=thumbnail&id='.$id.'&fileid='.$standardbild.'&size=200&direkt=1" align="left" width="200" style="margin-right:10px; margin-bottom:10px;" />'
      );
    }

    if($lagerartikel>0){
      $this->app->Tpl->Set('LAGERLINK', "&nbsp;<a href=\"index.php?module=artikel&action=lager&id=$id\">&rArr;</a>");
    }
    else{
      $this->app->Tpl->Set('LAGERLINK', '');
    }

    $this->app->Tpl->Set('KURZTEXT',$kurztext_de);

    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $table->Query("SELECT CONCAT(l.bezeichnung,' / ',lp.kurzbezeichnung, if(lp.sperrlager,' (Kein Auto-Versand Lager)',''),
      if(lp.poslager,' (POS Lager)',''),if(lp.verbrauchslager,' (Verbrauchslager)',''),if(lp.autolagersperre,' (Nachschublager)','')) as lager, trim(lpi.menge)+0 as menge
        FROM lager_platz_inhalt lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  
        LEFT JOIN lager l ON l.id=lp.lager WHERE lpi.artikel='$id' ");

    $table->DisplayNew('ARTIKEL','Menge','noAction');

    if($lager_platz > 0)
    {
      $lagerarr = $this->app->DB->SelectRow("SELECT lp.lager,lp.kurzbezeichnung,lag.bezeichnung 
        FROM lager_platz AS lp LEFT JOIN lager AS lag ON lp.lager = lag.id 
        WHERE lp.id = '$lager_platz' LIMIT 1");
      if(!empty($lagerarr))
      {
        $lager = $lagerarr['lager'];
        $lagerhauptbezeichung = $lagerarr['bezeichnung'];
        $lagerbezeichnung = $lagerarr['kurzbezeichnung'];
      }else{
        $lagerhauptbezeichung = '';
      }
    }else{
      $lagerhauptbezeichung = '';
    }
    if($lagerhauptbezeichung=='') {
      $lagerhauptbezeichung='kein Standardlager eingestellt';
    }
    $this->app->Tpl->Add('ARTIKEL',"<br>Standardlager: $lagerhauptbezeichung ".(!empty($lagerbezeichnung)?"/ $lagerbezeichnung":'')."<br><br>");

    $this->app->Tpl->Add('ARTIKEL',$this->ArtikelLagerInfo($id));

    $table = new EasyTable($this->app);
    $table->Query("SELECT adr.name as kunde, adr.kundennummer as kdnr, trim(r.menge)+0 as menge ,p.abkuerzung as projekt,r.grund  FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
        p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id WHERE r.firma='{$this->app->User->GetFirma()}' AND a.id='$id'");

    $table->DisplayNew('RESERVIERT','Grund','noAction');


    $table = new EasyTable($this->app);
    $table->Query("SELECT p.belegnr,a.name, a.kundennummer as kdnr, p.status,trim(po.menge)+0 as menge FROM auftrag_position po LEFT JOIN auftrag p ON p.id=po.auftrag LEFT JOIN adresse a ON a.id=p.adresse  WHERE po.artikel='$id' AND (p.status!='abgeschlossen' AND p.status!='storniert')");


    $table->DisplayNew('AUFTRAG','Menge','noAction');

    $table = new EasyTable($this->app);
    /* $table->Query("SELECT DATE_FORMAT(b.datum,'%d.%m.%Y') as datum, CONCAT('<a href=\"index.php?module=bestellung&action=edit&id=',b.id,'\" target=\"_blank\">',b.belegnr,'</a>') as 'bestellung Nr.', bp.bestellnummer as Nummer, bp.menge, bp.geliefert, bp.vpe as VPE, a.lieferantennummer as lieferant, a.name as name, if(bp.lieferdatum!='0000-00-00', DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, b.status as status
       FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN adresse a ON b.adresse=a.id
       WHERE artikel='$id' AND b.status!='storniert' AND b.status!='abgeschlossen' AND bp.geliefert<bp.menge ORDER by bp.lieferdatum DESC");
     */

    $table->Query("SELECT DATE_FORMAT(b.datum,'%d.%m.%Y') as datum, CONCAT('<a href=\"index.php?module=bestellung&action=edit&id=',b.id,'\" target=\"_blank\">',b.belegnr,'</a>') as 'bestellung', trim(bp.menge)+0 as menge, trim(bp.geliefert)+0 as geliefert, a.name as name, if(bp.lieferdatum!='0000-00-00', DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum
        FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN adresse a ON b.adresse=a.id
        WHERE artikel='$id' AND b.status!='storniert' AND b.status!='abgeschlossen' AND bp.geliefert<bp.menge ORDER by bp.lieferdatum DESC");

    $table->DisplayNew('BESTELLUNG','Lieferdatum','noAction');

    $table = new EasyTable($this->app);
    $table->Query('SELECT a.name as lieferant, trim(e.ab_menge)+0 ab, '.$this->app->erp->FormatPreis("e.preis")." as preis, e.waehrung FROM einkaufspreise e LEFT JOIN adresse a ON a.id=e.adresse
        WHERE e.artikel='$id' AND e.geloescht!=1 AND (e.gueltig_bis >= curdate() OR e.gueltig_bis='0000-00-00')");

    $table->DisplayNew('EINKAUFSPREISE','Waehrung','noAction');

    $verwendeberechneterek = $this->app->DB->Select("SELECT verwendeberechneterek FROM artikel WHERE id='$id' LIMIT 1");
    $berechneterek = $this->app->DB->Select('SELECT '.$this->app->erp->FormatPreis("berechneterek")." FROM artikel WHERE id='$id' LIMIT 1");
    $berechneterekwaehrung = $this->app->DB->Select("SELECT berechneterekwaehrung FROM artikel WHERE id='$id' LIMIT 1");


    if($verwendeberechneterek > 0){
      $this->app->Tpl->Set('KALKULIERTEREK', 'Kalkulierter EK-Preis: ' . $berechneterek . ' ' . $berechneterekwaehrung);
    }

    $table = new EasyTable($this->app);
    $table->Query("SELECT if(a.name='' OR a.id IS NULL,if(v.gruppe > 0,(SELECT CONCAT(g.name,' ',g.kennziffer) FROM gruppen g WHERE g.id=v.gruppe ),'Alle'),a.name) as kunde, trim(v.ab_menge)+0 ab, ".$this->app->erp->FormatPreis("v.preis")." as preis, v.waehrung FROM verkaufspreise v LEFT JOIN adresse a ON a.id=v.adresse
        WHERE v.artikel='$id' AND v.geloescht!=1 AND (v.gueltig_bis >= curdate() OR v.gueltig_bis='0000-00-00')");

    $table->DisplayNew('VERKAUFSPREISE','Waehrung','noAction');


    $table = new EasyTable($this->app);
    $table->Query("
    
    SELECT e.name, CONCAT(ew.wert,'&nbsp;&nbsp;') as wert, ew.einheit FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften WHERE ew.artikel='$id'


   /* SELECT t.hauptkategorie, t.unterkategorie,format(sum(t.wert),2) as wert,t.einheit
    FROM (
    
    
      (SELECT e.hauptkategorie, e.unterkategorie, e.wert, e.einheit
        FROM eigenschaften e LEFT JOIN artikel a ON a.id=e.artikel 
        WHERE a.id='$id')UNION ALL 
        (SELECT e.hauptkategorie, e.unterkategorie, (s.menge* e.wert) as wert, e.einheit
        FROM eigenschaften e LEFT JOIN stueckliste s ON s.artikel=e.artikel 
        WHERE s.stuecklistevonartikel='$id')
        )t GROUP BY t.hauptkategorie, t.unterkategorie,t.einheit*/
        
        
        ");// ORDER by e.bezeichnung
    $table->DisplayNew('EIGENSCHAFTEN','Einheit','noAction');

    $table = new EasyTable($this->app);
    $table->Query('SELECT a.nummer, a.name_de as artikel, '.$this->app->erp->FormatMenge('s.menge')." as menge FROM stueckliste s 
        LEFT JOIN artikel a ON s.artikel=a.id 
        WHERE s.stuecklistevonartikel='$id' ORDER by a.nummer");
    $table->DisplayNew('STUECKLISTE','Menge','noAction');

    $this->app->erp->RunHook('artikel_minidetail_hook1', 1, $id);

    $this->app->Tpl->Output('artikel_minidetail.tpl');
    $this->app->ExitXentral();
  }





  public function ArtikelShopimport()
  {
    $id = $this->app->Secure->GetGET('id'); 
    $shop = $this->app->Secure->GetGET('shop'); 
    $artikel = array($id);
    $artikelshopid = (int)$this->app->Secure->GetGET('artikelshopid');

    if($artikelshopid > 0)
    {
      $shop = $this->app->DB->Select("SELECT shop FROM artikel_onlineshops WHERE id = '$artikelshopid' AND artikel = '$id' AND aktiv = 1 LIMIT 1");
    }else{
      if($shop=='1'){
        $shop = $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$id' LIMIT 1");
      }
      elseif($shop=='2'){
        $shop = $this->app->DB->Select("SELECT shop2 FROM artikel WHERE id='$id' LIMIT 1");
      }
      elseif($shop=='3'){
        $shop = $this->app->DB->Select("SELECT shop3 FROM artikel WHERE id='$id' LIMIT 1");
      }
    }

    $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
    $extnummer = $this->app->DB->Select("SELECT nummer FROM artikelnummer_fremdnummern WHERE artikel = '$id' AND shopid='$shop' AND aktiv = 1 AND nummer <> '' LIMIT 1");
    if($extnummer){
      $nummer = $extnummer;
    }
    $result = $this->app->remote->RemoteGetArticle($shop,$nummer);

    $update = $this->updateShopArticle($id, $result);

    $msg = $update['msg_encoded'];
    $this->app->Location->execute("index.php?module=artikel&action=edit&id=$id&msg=$msg#tabs-4");
  }

  /**
   * @param int          $id
   * @param array|string $result
   *
   * @return array
   */
  public function updateShopArticle($id, $result)
  {
    if(is_array($result)) {
      $result['uebersicht_de'] = htmlentities($result['uebersicht_de'],ENT_QUOTES, 'UTF-8');
      $result['uebersicht_en'] = htmlentities($result['uebersicht_en'],ENT_QUOTES, 'UTF-8');

      if($result['name_de']=='' && $result['name']!='') {
        $result['name_de']=$result['name'];
      }
    }
    $isOk = false;
    $error = '';
    $info = '';
    if(is_array($result) && $result['name_de']!='' && !is_array($result['name_de']) && !empty($result['name_de'])) {
      $isOk = true;
      //$result['name_de'] = $result['name'];
      if($result['aktiv'] !='1'){
        $result['inaktiv']=1;
      }
      else{
        $result['inaktiv']=0;
      }

      $fields = [
        'name_de','kurztext_de','uebersicht_de','name_en','kurztext_en','uebersicht_en','beschreibung_de',
        'beschreibung_en','metakeywords_de','metakeywords_en','metatitle_de','metatitle_en','metadescription_de',
        'metadescription_en',
        'inaktiv','pseudopreis','lieferzeitmanuell','pseudolager','autolagerlampe','restmenge','gewicht',
        'downloadartikel','ean','herstellernummer','hersteller'
      ];

      if($result['restmenge']!='1') {
        $result['restmenge']=0;
      }

      // pseudolager
      $result['autolagerlampe']=1;

      //name
      foreach($fields as $nameofcolumn) {
        if(isset($result[$nameofcolumn])) {
          if(
            ($result[$nameofcolumn]!='' && !is_array($result[$nameofcolumn]))
            || $nameofcolumn==='lieferzeitmanuell' || $nameofcolumn==='pseudopreis'
          ){
            $this->app->DB->Update(
              "UPDATE artikel 
              SET " . $nameofcolumn . "='" . $this->app->DB->real_escape_string($result[$nameofcolumn]) . "' 
              WHERE id='$id' LIMIT 1"
            );
          }
        }
      }

      //preis_netto
      if($result['preis_netto'] > 0) {
        $this->app->erp->AddVerkaufspreis($id,1,0,$result['preis_netto']);
      }
      $info = 'Der Artikel wurde aus dem Shop geladen und nach Xentral importiert!';
      $msg = $this->app->erp->base64_url_encode('<div class="info">'.$info.'</div>');
    }
    else {
      $check = strpos($result ,'error:');

      if($check===0) {
        $result = str_replace('error:','',$result);
        $error = 'Fehlermeldung vom Shop: '.$result;
        $msg = $this->app->erp->base64_url_encode('<div class="error">'.$error.'</div>');
      }
      else {
        $error = 'Es gab keine R&uuml;ckmeldung vom Shop! '.$result;
        $msg = $this->app->erp->base64_url_encode('<div class="error">'.$error.'</div>');
      }
    }

    return ['status'=>$isOk, 'info'=>$info, 'msg_encoded' => $msg, 'error' => $error];
  }

  public function ArtikelShopexport()
  {
    $id = $this->app->Secure->GetGET('id'); 
    $shop = $this->app->Secure->GetGET('shop'); 
    $artikel = array($id);
    $artikelshopid = (int)$this->app->Secure->GetGET('artikelshopid');
    
    if($artikelshopid > 0)
    {
      $shop = $this->app->DB->Select("SELECT shop FROM artikel_onlineshops WHERE id = '$artikelshopid' AND artikel = '$id' AND aktiv = 1 LIMIT 1");
      $this->app->User->SetParameter('artikel_shopexport_shop', '');
    }else{
      if($shop=='1'){
        $shop = $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$id' LIMIT 1");
      }
      elseif($shop=='2'){
        $shop = $this->app->DB->Select("SELECT shop2 FROM artikel WHERE id='$id' LIMIT 1");
      }
      elseif($shop=='3'){
        $shop = $this->app->DB->Select("SELECT shop3 FROM artikel WHERE id='$id' LIMIT 1");
      }
    }
    
    $artikelexport = $this->app->DB->Select("SELECT artikelexport FROM shopexport WHERE id='$shop' LIMIT 1");
    $lagerexport = $this->app->DB->Select("SELECT lagerexport FROM shopexport WHERE id='$shop' LIMIT 1");

    $externenummer = $this->app->DB->Select("SELECT nummer FROM artikelnummer_fremdnummern WHERE artikel = '$id' AND aktiv = 1 AND shopid = '$shop' AND nummer <> '' ORDER BY bezeichnung = 'SHOPID' DESC LIMIT 1");

    if($externenummer)
    {
      $extartikelnummer = array($externenummer);
    }else{
      $extartikelnummer = '';
    }

    $pageContents = $this->app->remote->RemoteSendArticleList($shop,$artikel,$extartikelnummer);
    $check = strpos($pageContents ,'error:');
    $msg = '';
    if(!empty($pageContents) && is_array($pageContents)) {
      if(!empty($pageContents['status']) && !empty($pageContents['message'])) {
        $msg = $this->app->erp->base64_url_encode('<div class="info">'.$pageContents['message'].'</div>');
      }
      elseif(isset($pageContents['status']) && !empty($pageContents['message'])) {
        $msg = $this->app->erp->base64_url_encode('<div class="error">'.$pageContents['message'].'</div>');
      }
    }
    elseif($pageContents=='1') {
      $pageContents='success';
    }

    if(empty($msg) && $pageContents!='') {
      $pageContents = " ($pageContents)";
    }

    $this->app->erp->LogFile($this->app->DB->real_escape_string('manueller Shopexport Artikel: '.$this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$id' LIMIT 1").' Shop: '.$shop.' Status: '.$pageContents));
    // keine fehlermeldung vom shop
    if(empty($msg)) {
      $linkToImporterSettings = '';
      if($this->app->erp->RechteVorhanden('onlineshops', 'edit')){
        $url = 'index.php?module=onlineshops&action=edit&id='.$shop;
        $linkToImporterSettings = "<a href='$url' class='button button-primary'>Zu den Shopeinstellungen</a>";
      }

      if($check === 0){
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Es gab einen Fehler beim Aktualisieren des Artikels im Shop!$pageContents</div>");
      }
      else if($pageContents == ''){
        if($artikelexport != 1 && $lagerexport != 1){
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel konnte nicht zum Shop &uuml;bertragen werden! In den Shopeinstellungen ist festgelegt, dass die Artikelinformation- und Lagerbestands&uuml;bertragung nicht erlaubt ist!$pageContents $linkToImporterSettings</div>");
        }
        else{
          if($pageContents === '0' || $pageContents === 0){
            $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Es gab einen Fehler beim Aktualisieren des Artikels im Shop! Stellen Sie sicher, dass die Zugangsdaten und URL's korrekt sind! M&ouml;glicherweise kein Artikelpreis hinterlegt</div>");
          }else{
            $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Es gab einen Fehler beim Aktualisieren des Artikels im Shop! Stellen Sie sicher, dass die Zugangsdaten und URL's korrekt sind!$pageContents $linkToImporterSettings</div>");
          }
        }
      }
      else{
        if($artikelexport != 1 && $lagerexport == 1){
          $msg = $this->app->erp->base64_url_encode("<div class=info>Es wurde nur der Lagerbestand (nicht die Artikelinfos entsprechend der Einstellungen) im Shop aktualisiert!$pageContents $linkToImporterSettings</div>");
        }
        else if($lagerexport != 1 && $artikelexport == 1){
          $msg = $this->app->erp->base64_url_encode("<div class=info>Es wurde nur der Artikel (nicht der Lagerbestand entsprechend der Einstellungen) im Shop aktualisiert!$pageContents $linkToImporterSettings</div>");
        }
        else{
          $msg = $this->app->erp->base64_url_encode("<div class=info>Der Artikel wurde im Shop aktualisiert!$pageContents</div>");
        }
      }
    }
    $this->app->erp->LagerSync($artikel);

    $this->app->Location->execute("index.php?module=artikel&action=edit&id=$id&msg=$msg#tabs-4");
  }

  public function ArtikelShopexportFiles()
  {
    $id = $this->app->Secure->GetGET('id'); 
    $shop = $this->app->Secure->GetGET('shop'); 

    if($shop=='1'){
      $shop = $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$id' LIMIT 1");
    }
    elseif($shop=='2'){
      $shop = $this->app->DB->Select("SELECT shop2 FROM artikel WHERE id='$id' LIMIT 1");
    }
    elseif($shop=='3'){
      $shop = $this->app->DB->Select("SELECT shop3 FROM artikel WHERE id='$id' LIMIT 1");
    }

    if($this->app->remote->RemoteUpdateFilesArtikel($id,$shop)){
      $msg = $this->app->erp->base64_url_encode('<div class="info">Der Artikel wurde im Shop aktualisiert!</div>');
    }
    else{
      $msg = $this->app->erp->base64_url_encode('<div class="error">Es gab einen Fehler beim Aktualisieren des Artikels im Shop!</div>');
    }

    $this->app->Location->execute("index.php?module=artikel&action=edit&id=$id&msg=$msg#tabs-5");
  }


  public function ArtikelStuecklisteEtiketten()
  {
    $id = $this->app->Secure->GetGET('id'); 
    $this->app->erp->ArtikelStuecklisteDrucken($id);
    $this->app->Location->execute("index.php?module=artikel&action=stueckliste&id=$id");
  }

  public function ArtikelSchliessen()                                                                       
  {
    $id = $this->app->Secure->GetGET('id');                                                              
    if($id > 0 && is_numeric($id)){
      $this->app->DB->Update("UPDATE bestellung_position SET abgeschlossen='1' WHERE artikel='$id'");
    }
    $referer = $_SERVER['HTTP_REFERER'];
    if(empty($referer)) {
      $referer = 'index.php';
    }
    $this->app->Location->execute($referer);
  }

  public function ArtikelLagerlampe()
  {
    $jetztnichtlagernd = '';
    $aktivieren = $this->app->Secure->GetPOST('aktivieren');
    $deaktivieren = $this->app->Secure->GetPOST('deaktivieren');
    $jetztnichtlagerndrot = '';
    //$jetztnichtlagerndrot = $this->app->Secure->GetPOST('jetztnichtlagerndrot');
    $jetztgruen = $this->app->Secure->GetPOST('jetztgruen');
    $jetztgelb = $this->app->Secure->GetPOST('jetztgelb');
    $jetztrot = $this->app->Secure->GetPOST('jetztrot');
    $tab3gruen = $this->app->Secure->GetPOST('tab3gruen');
    $neuweg = $this->app->Secure->GetPOST('neuweg');
    $artikelmarkiert = $this->app->Secure->GetPOST('artikelmarkiert');
    $artikelmarkierthidden = $this->app->Secure->GetPOST('artikelmarkierthidden');

    $cartikelmarkiert = count($artikelmarkiert);
    if($jetztgruen!='') 
    {
      for($i=0;$i < $cartikelmarkiert; $i++) {
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='green',ausverkauft='0' WHERE id='" . $artikelmarkiert[$i] . "'  LIMIT 1");
      }
    }

    else if($jetztgelb!='') 
    {
      for($i=0;$i < $cartikelmarkiert; $i++) {
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='yellow',ausverkauft='0' WHERE id='" . $artikelmarkiert[$i] . "'  LIMIT 1");
      }
    }

    else if($jetztrot!='') 
    {
      for($i=0;$i < $cartikelmarkiert; $i++) {
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='red' WHERE id='" . $artikelmarkiert[$i] . "'  LIMIT 1");
      }
    }

    else if($aktivieren!='') 
    {
      foreach($artikelmarkierthidden as $key=>$value)
      {
        if($artikelmarkiert[$key]=='1')
        {
          $this->app->DB->Update("UPDATE artikel SET autolagerlampe='1' WHERE id='".$key."'  LIMIT 1");
        }
        else {
          $this->app->DB->Update("UPDATE artikel SET autolagerlampe='0' WHERE id='".$key."'  LIMIT 1");
        }
      }
    }

    else if($neuweg!='')
    {
      for($i=0;$i < $cartikelmarkiert; $i++) {
        $this->app->DB->Update("UPDATE artikel SET neu='0' WHERE id='" . $artikelmarkiert[$i] . "' LIMIT 1");
      }
    } 

    else if($jetztnichtlagernd!='')
    {
      for($i=0;$i < $cartikelmarkiert; $i++) {
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='bestellt' WHERE id='" . $artikelmarkiert[$i] . "' LIMIT 1");
      }
    } 
    else if($jetztnichtlagerndrot!='')
    {
      for($i=0;$i < $cartikelmarkiert; $i++) {
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='nichtlieferbar' WHERE id='" . $artikelmarkiert[$i] . "' LIMIT 1");
      }
    } 

    //    $this->app->erp->MenuEintrag("index.php?module=artikel&action=create","Neuen Artikel anlegen");
    $this->app->erp->MenuEintrag('index.php?module=lager&action=list','zur&uuml;ck zur &Uuml;bersicht');

    $this->app->Tpl->Set('TAB1','<div class="info">Hier werden alle Artikel die als nicht lagernd Online-Shop markierten Artikel angezeigt.</div>');
    $this->app->Tpl->Set('TAB2','<div class="info">Hier werden alle Artikel die als lagernd im Online-Shop markiert sind jedoch nicht im Lager liegen.</div>');
    $this->app->Tpl->Set('TAB3','<div class="info">Hier werden alle Artikel die als ausverkauf im Online-Shop markierten sind jedoch im Lager liegen.</div>');

    $this->app->YUI->TableSearch('TAB1','manuellagerlampe', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('TAB2','autolagerlampe', 'show','','',basename(__FILE__), __CLASS__);
    //    $this->app->YUI->TableSearch('TAB2','artikeltabellelagerndabernichtlagernd');                                                  
    //   $this->app->YUI->TableSearch('TAB3','artikeltabellehinweisausverkauft');                                                  
    $this->app->YUI->TableSearch('TAB3','artikeltabelleneu', 'show','','',basename(__FILE__), __CLASS__);

    $this->app->erp->Headlines('Lagerlampen berechnen');
    $this->app->Tpl->Set('TABTEXT','Lagerlampen berechnen');

    $this->app->Tpl->Parse('MANUELLCHECKBOX','checkbox.tpl');
    $this->app->Tpl->Parse('AUTOCHECKBOX','checkbox2.tpl');
    $this->app->Tpl->Parse('PAGE','lagerlampen.tpl');
  }


  public function ArtikelProfisuche()
  {
    $id = $this->app->Secure->GetGET('id'); // abhaengig von cmd
    $cmd = $this->app->Secure->GetGET('cmd');
    $fmodul = $this->app->Secure->GetGET('fmodul');
    if($cmd === 'filterbaum')
    {
      $this->app->User->SetParameter('filterbaum_'.$fmodul,$this->app->Secure->GetPOST('id'));
      echo json_encode(array('status'=>1));
      $this->app->ExitXentral();
    }
    $this->app->User->SetParameter('filterbaum_'.$fmodul,'');
    $anlegen = $this->app->Secure->GetPOST('anlegen');
    $bezeichnung = '';
    $projekt = $this->app->Secure->GetPOST('projekt');
    $adresse = $this->app->Secure->GetPOST('adresse');
    $menge = $this->app->Secure->GetPOST('menge');
    $preis = $this->app->Secure->GetPOST('preis');
    $bestellnummer = $this->app->Secure->GetPOST('bestellnummer');
    $bezeichnunglieferant = $this->app->Secure->GetPOST('bezeichnunglieferant');
    $typ = $this->app->Secure->GetPOST('typ');
    $name_de = $this->app->Secure->GetPOST('name_de');
    $kurztext_de = $this->app->Secure->GetPOST('kurztext_de');
    $umsatzsteuer = $this->app->Secure->GetPOST('umsatzsteuer');
    $steuersatz = $this->app->Secure->GetPOST('steuersatz');
    $allelieferanten = $this->app->Secure->GetPOST('allelieferanten');
    $internerkommentar = $this->app->Secure->GetPOST('internerkommentar');
    $lagerartikel = $this->app->Secure->GetPOST('lagerartikel');

    // Anfang: FIX Batch-Insert übermittelt keine Umsatzsteuer und keinen Steuersatz > Werte aus DB holen
    $insert = $this->app->Secure->GetGET('insert');
    if($insert=='true'){
      if(empty($umsatzsteuer)){
        if($cmd != 'bestellung' && $cmd != 'anfrage' && $cmd != 'preisanfrage'){
          $umsatzsteuer = $this->app->DB->Select("SELECT a.umsatzsteuer FROM verkaufspreise AS v INNER JOIN artikel AS a ON v.artikel = a.id WHERE v.id='$id' LIMIT 1");
        }else{
          $umsatzsteuer = $this->app->DB->Select("SELECT a.umsatzsteuer FROM einkaufspreise AS e INNER JOIN artikel AS a ON e.artikel = a.id WHERE e.id='$id' LIMIT 1");
        }
      }
      if(empty($steuersatz)){
        if($cmd != 'bestellung' && $cmd != 'anfrage' && $cmd != 'preisanfrage'){
          $steuersatz = $this->app->DB->Select("SELECT a.steuersatz FROM verkaufspreise AS v INNER JOIN artikel AS a ON v.artikel = a.id WHERE v.id='$id' LIMIT 1");
        }else{
          $steuersatz = $this->app->DB->Select("SELECT a.steuersatz FROM einkaufspreise AS e INNER JOIN artikel AS a ON e.artikel = a.id WHERE e.id='$id' LIMIT 1");
        }
        if ((float)$steuersatz <= 0) {
          $steuersatz = null;
        }
      }
    }
    // Ende: FIX Batch-Insert übermittelt keine Umsatzsteuer und keinen Steuersatz > Werte aus DB holen

    if (empty($menge)) {
      $menge = $this->app->Secure->GetGET('menge');
    }
    if (empty($name_de)){
      $name_de = $this->app->Secure->GetGET('name_de');
    }

    if(!in_array($umsatzsteuer, ['normal','ermaessigt','befreit'], true)){
      $umsatzsteuer = 'normal';
    }
    if (!empty($steuersatz)) {
      $umsatzsteuer = '';
      $steuersatz = (float)$steuersatz;
    } else {
      $steuersatz = null;
    }
    $steuersatzSqlValue = empty($steuersatz) ? 'NULL' : "'$steuersatz'";

    $insert = $this->app->Secure->GetGET('insert');

    if($insert=='true')
    {
      // hole alles anhand der verkaufspreis id

      $id = $this->app->Secure->GetGET('sid');
      $vid = $this->app->Secure->GetGET('id');
      $cmd = $this->app->Secure->GetGET('cmd');

      if($cmd!=='bestellung' && $cmd!=='anfrage' && $cmd!=='preisanfrage')
      {
        $artikel_id = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$vid' LIMIT 1");
        $preis = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$vid' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$vid' LIMIT 1");
        $waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$vid' LIMIT 1");
        if (empty($menge)){
          $menge = $this->app->DB->Select("SELECT ab_menge FROM verkaufspreise WHERE id='$vid' LIMIT 1");
        }
      } else {
        $artikel_id = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        $preis = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        $waehrung = $this->app->DB->Select("SELECT waehrung FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        if (empty($menge)){
          $menge = $this->app->DB->Select("SELECT ab_menge FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        }
      }
      $lieferdatum = '0000-00-00';

      $vpe = '';

      if($projekt <=0 ){
        $projekt = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel_id' LIMIT 1");
      }

      if($projekt <=0){
        $projekt = $this->app->DB->Select("SELECT projekt FROM {$cmd} WHERE id='$id' LIMIT 1");
      }

      if($waehrung==''){
        $waehrung = $this->app->DB->Select("SELECT waehrung FROM {$cmd} WHERE id='$id' LIMIT 1");
      }
      
      if($waehrung==''){
        $waehrung = $this->app->erp->GetStandardWaehrung($projekt);
      }

      $sprache = $this->app->DB->Select("SELECT sprache FROM {$cmd} WHERE id='$id' LIMIT 1");

      if($sprache==='englisch')
      {
        if($kurztext_de==''){
          $kurztext_de = $this->app->DB->Select("SELECT anabregs_text_en FROM artikel WHERE id='$artikel_id' LIMIT 1");
        }
        if($bezeichnung==''){
          $bezeichnung = $this->app->DB->Select("SELECT name_en FROM artikel WHERE id='$artikel_id' LIMIT 1");
        }
      } 
      
      if($kurztext_de==''){
        $kurztext_de = $this->app->DB->Select("SELECT anabregs_text FROM artikel WHERE id='$artikel_id' LIMIT 1");
      }

      if($bezeichnung==""){
        $bezeichnung = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel_id' LIMIT 1");
      }

      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $allelieferanten = $this->app->DB->Select("SELECT allelieferanten FROM artikel WHERE id='$artikel_id' LIMIT 1");

      $sort = (int)$this->app->DB->Select("SELECT IFNULL(MAX(sort),0) FROM {$cmd}_position WHERE {$cmd}='$id' LIMIT 1");
      $sort++;

      $mlmpunkte = $this->app->DB->Select("SELECT mlmpunkte FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $mlmbonuspunkte = $this->app->DB->Select("SELECT mlmbonuspunkte FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $mlmdirektpraemie = $this->app->DB->Select("SELECT mlmdirektpraemie FROM artikel WHERE id='$artikel_id' LIMIT 1");

      if($cmd==='lieferschein')
      {
        $this->app->DB->Insert("INSERT INTO lieferschein_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, status,projekt,vpe)
            VALUES ('','$id','$artikel_id','".$this->app->DB->real_escape_string($bezeichnung)."','$kurztext_de','$nummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe')");

        $posid = $this->app->DB->GetInsertID();
      } 
      else if($cmd==='anfrage')
      {
        $this->app->DB->Insert("INSERT INTO anfrage_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, status,projekt,vpe)
            VALUES ('','$id','$artikel_id','".$this->app->DB->real_escape_string($bezeichnung)."','$kurztext_de','$nummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe')");
        $posid = $this->app->DB->GetInsertID();
      } 
      else if($cmd==='preisanfrage')
      {
        $this->app->DB->Insert("INSERT INTO preisanfrage_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, projekt,vpe)
            VALUES ('','$id','$artikel_id','".$this->app->DB->real_escape_string($bezeichnung)."','$kurztext_de','$nummer','$menge','$sort','$lieferdatum','$projekt','$vpe')");

        $posid = $this->app->DB->GetInsertID();
      } 
      else if($cmd==='bestellung')
      {
        $bestellnummer = $this->app->DB->Select("SELECT bestellnummer FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        $bezeichnunglieferant = $this->app->DB->Select("SELECT bezeichnunglieferant FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        if (empty($bezeichnunglieferant)) { $bezeichnunglieferant = $bezeichnung; }

        $this->app->DB->Insert("INSERT INTO bestellung_position (id,{$cmd},artikel,beschreibung,menge,sort,lieferdatum, status,projekt,vpe,bestellnummer,bezeichnunglieferant,preis,waehrung,umsatzsteuer,steuersatz)
            VALUES ('','$id','$artikel_id','$kurztext_de','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe','$bestellnummer','".$this->app->DB->real_escape_string($bezeichnunglieferant)."','$preis','$waehrung','$umsatzsteuer',$steuersatzSqlValue)");

        $posid = $this->app->DB->GetInsertID();
      }
      else if ($cmd==='auftrag' || $cmd==='angebot' || $cmd==='rechnung')
      {
        $this->app->DB->Insert("INSERT INTO {$cmd}_position (id,{$cmd},artikel,bezeichnung,beschreibung,
          nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, steuersatz, status,projekt,vpe,punkte,bonuspunkte,mlmdirektpraemie)
            VALUES ('','$id','$artikel_id','".$this->app->DB->real_escape_string($bezeichnung)."','$kurztext_de','$nummer','$menge','$preis','$waehrung','$sort',
              '$lieferdatum','$umsatzsteuer',$steuersatzSqlValue,'angelegt','$projekt','$vpe','$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie')");

        $posid = $this->app->DB->GetInsertID();
      } 
      else {
        $posid = null;
        $this->app->erp->RunHook('artikel_profisuche', 4, $cmd, $id, $artikel_id, $posid);
        if($posid === null){
          $this->app->DB->Insert("INSERT INTO {$cmd}_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, steuersatz, status,projekt,vpe)
            VALUES ('','$id','$artikel_id','".$this->app->DB->real_escape_string($bezeichnung)."','$kurztext_de','$nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer',$steuersatzSqlValue,'angelegt','$projekt','$vpe')");

          $posid = $this->app->DB->GetInsertID();
        }
      }

      if($cmd=="angebot" || $cmd=="auftrag" || $cmd=="rechnung" || $cmd=="gutschrift"){
        $this->app->erp->RunHook("beleg_afterinsertposition", 5, $cmd, $id, $artikel_id, $menge, $posid);
      }

      if($cmd!="" && $posid > 0 && $id > 0)
      {
        $this->app->erp->CheckFreifelderEinzelPos($cmd, $id, true, $posid);
      }

      // Batch-Insert wird für Artikelmatrix benötigt
      // Pro Artikel der eingefügt werden soll, wird ein AJAX-Request auf diese Methode ausgeführt
      $batchInsert = $this->app->Secure->GetGET('batch') === 'true';
      if ($batchInsert === true) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        $this->app->erp->ExitWawi();
      }

      $this->app->Location->execute("index.php?module={$cmd}&action=positionen&id=$id&fmodul=$fmodul");
    }

    if($anlegen!='')
    {
      // speichern ??
      //echo 'speichern';

      if($cmd==='lieferschein')
      {
        if($name_de=='' || $menge=='')
        {
          $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Artikel (DE) und Menge sind Pflichtfelder!</div>");
          $error = 1;
        }
      } else {
        if($name_de=='' || $menge=='' || $preis=='')
        {
          $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Artikel (DE), Preis und Menge sind Pflichtfelder!</div>");
          $error = 1;
        }
      }
      if($error!=1)
      {
        $sort = (int)$this->app->DB->Select("SELECT IFNULL(MAX(sort),0) FROM {$cmd}_position WHERE {$cmd}='$id' LIMIT 1");
        $sort++;

        $tmp = trim($adresse);
        $rest = $this->app->erp->FirstTillSpace($tmp);

        if($rest > 0){
          $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='$rest' AND geloescht=0 AND firma='" . $this->app->User->GetFirma() . "' AND lieferantennummer!='' LIMIT 1");
        }
        else {
          $adresse='';
        }

        if($adresse <= 0 && $cmd==='bestellung')
        {
          $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");
        }

        $artikelart = $typ;
        $lieferant = $adresse;
        $bezeichnung = $name_de;
        $waehrung = 'EUR';
        $lieferdatum = '00.00.0000';
        $vpe = '';
        $preis = str_replace(',','.',$preis);

        if($projekt!=''){
          $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND firma='" . $this->app->User->GetFirma() . "' LIMIT 1");
        }
        else {
          $projekt_bevorzugt = (int)$this->app->DB->Select("SELECT u.projekt_bevorzugen FROM `user` AS u WHERE u.id = '".$this->app->User->GetID()."' LIMIT 1");
          if($projekt_bevorzugt === 1){
            $projekt = $this->app->DB->Select("SELECT u.projekt FROM `user` AS u WHERE u.id = '".$this->app->User->GetID()."' LIMIT 1");
          }else{
            $projekt = $this->app->User->DefaultProjekt();
          }
        }

        $neue_nummer = $this->app->erp->GetNextArtikelnummer($artikelart,$this->app->User->GetFirma(),$projekt);

        // anlegen als artikel
        $umsatzsteuerArtikel = (empty($umsatzsteuer)) ? 'normal' : $umsatzsteuer;
        $this->app->DB->Insert("INSERT INTO artikel (typ,nummer,projekt,name_de,anabregs_text,umsatzsteuer,adresse,firma,internerkommentar,lagerartikel,allelieferanten)
            VALUES ('$artikelart','$neue_nummer','$projekt','$bezeichnung','$kurztext_de','$umsatzsteuerArtikel','$lieferant','".$this->app->User->GetFirma()."','$internerkommentar','$lagerartikel','$allelieferanten')");

        $artikel_id = $this->app->DB->GetInsertID();
        // einkaufspreis anlegen

        $lieferdatum = $this->app->String->Convert($lieferdatum,"%1.%2.%3","%3-%2-%1");

        if($cmd==="lieferschein")
        {
          $this->app->DB->Insert("INSERT INTO lieferschein_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, status,projekt,vpe)
              VALUES ('','$id','$artikel_id','$bezeichnung','$kurztext_de','$neue_nummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe')");
        }
        else if($cmd==="anfrage")
        {
          $this->app->DB->Insert("INSERT INTO anfrage_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, projekt,vpe)
              VALUES ('','$id','$artikel_id','$bezeichnung','$kurztext_de','$neue_nummer','$menge','$sort','$lieferdatum','$projekt','$vpe')");

          $this->app->erp->AddEinkaufspreis($artikel_id,$menge,$lieferant,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);
        }
        else if($cmd=="preisanfrage")
        {
          $this->app->DB->Insert("INSERT INTO preisanfrage_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, projekt,vpe)
              VALUES ('','$id','$artikel_id','$bezeichnung','$kurztext_de','$neue_nummer','$menge','$sort','$lieferdatum','$projekt','$vpe')");

          $this->app->erp->AddEinkaufspreis($artikel_id,$menge,$lieferant,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);
        }


        else if($cmd==="bestellung")
        {
          if($bezeichnunglieferant=="") $bezeichnunglieferant=$bezeichnung;
          $this->app->DB->Insert("INSERT INTO bestellung_position ({$cmd},artikel,beschreibung,menge,sort,lieferdatum, status,projekt,vpe,bestellnummer,bezeichnunglieferant,preis,waehrung,umsatzsteuer,steuersatz)
              VALUES ('$id','$artikel_id','$kurztext_de','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe','$bestellnummer','$bezeichnunglieferant','$preis','$waehrung','$umsatzsteuer',$steuersatzSqlValue)");

          //      $this->app->DB->Insert("INSERT INTO einkaufspreise (id,artikel,adresse,objekt,projekt,preis,ab_menge,angelegt_am,bearbeiter,bestellnummer,bezeichnunglieferant)
          //          VALUES ('','$artikel_id','$lieferant','Standard','$projekt','$preis','$menge',NOW(),'".$this->app->User->GetName()."','$bestellnummer','$bezeichnunglieferant')");

          $this->app->erp->AddEinkaufspreis($artikel_id,$menge,$lieferant,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);

        } else { // angebot auftrag rechnung gutschrift
          $this->app->DB->Insert("INSERT INTO verkaufspreise (artikel,adresse,objekt,projekt,preis,ab_menge,angelegt_am,bearbeiter)
              VALUES ('$artikel_id','0','Standard','$projekt','$preis','$menge',NOW(),'".$this->app->User->GetName()."')");

          $this->app->DB->Insert("INSERT INTO {$cmd}_position ({$cmd},artikel,bezeichnung,beschreibung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, steuersatz, status,projekt,vpe)
              VALUES ('$id','$artikel_id','$bezeichnung','$kurztext_de','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer',$steuersatzSqlValue,'angelegt','$projekt','$vpe')");
        }

        $this->app->Location->execute("index.php?module={$cmd}&action=positionen&id=$id");
      } 
    }

    $umsatzsteuerauswahl = '<option value="normal"'.(($umsatzsteuer === 'normal') ? ' selected="selected"' : '').'>Standard</option>';
    $umsatzsteuerauswahl .= '<option value="ermaessigt"'.(($umsatzsteuer === 'ermaessigt') ? ' selected="selected"' : '').'>Erm&auml;&szlig;igt</option>';
    $umsatzsteuerauswahl .= '<option value="befreit"'.(($umsatzsteuer === 'befreit') ? ' selected="selected"' : '').'>Befreit</option>';

    $this->app->Tpl->Set('UMSATZSTEUERAUSWAHL',$umsatzsteuerauswahl);
    $this->app->Tpl->Set('INDIVIDUELLERSTEUERSATZ',$steuersatz);
    $this->app->Tpl->Set('PROJEKT',$projekt);
    $this->app->Tpl->Set('ADRESSE',$adresse);
    $this->app->Tpl->Set('MENGE',$menge);
    $this->app->Tpl->Set('PREIS',$preis);
    $this->app->Tpl->Set('BESTELLNUMMER',$bestellnummer);
    $this->app->Tpl->Set('BEZEICHNUNGLIEFERANT',$bezeichnunglieferant);
    $this->app->Tpl->Set('NAME_DE',$name_de);
    $this->app->Tpl->Set('KURZTEXT_DE',$kurztext_de);
    $this->app->Tpl->Set('INTERNERKOMMENTAR',$internerkommentar);

    if (!empty($steuersatz)) {
      $this->app->Tpl->Set('STEUERSATZEINBLENDEN','checked');
    }
    if($lagerartikel=='1'){
      $this->app->Tpl->Set('LAGERARTIKEL','checked');
    }

    $this->app->YUI->AutoComplete('projekt','projektname',1);
    $this->app->YUI->AutoComplete('adresse','lieferant');
    $this->app->YUI->AutoComplete('steuersatz-individuell','steuersatz',1);

    if($cmd==='auftrag' || $cmd==='rechnung' || $cmd==='lieferschein' || $cmd==='angebot' || $cmd==='gutschrift' || $cmd==='anfrage')
    {
      $adresse = $this->app->DB->Select("SELECT adresse FROM {$cmd} WHERE id='$id' LIMIT 1");
      $kunde = $this->app->DB->Select("SELECT CONCAT(name,' ',kundennummer,'') FROM adresse WHERE id='$adresse' LIMIT 1");
    } else if ($cmd==='bestellung' || $cmd==='preisanfrage') {
      $adresse = $this->app->DB->Select("SELECT adresse FROM {$cmd} WHERE id='$id' LIMIT 1");
      $kunde = $this->app->DB->Select("SELECT CONCAT(name,' ',lieferantennummer,'') FROM adresse WHERE id='$adresse' LIMIT 1");
    }


    if($cmd==='lieferschein'){
      $this->app->YUI->ParserVarIf('LIEFERSCHEIN', 1);
    }
    else{
      $this->app->YUI->ParserVarIf('LIEFERSCHEIN', 0);
    }


    $this->app->Tpl->Set('KUNDE',$kunde);

    if($cmd==='bestellung' || $cmd==='preisanfrage'){
      $this->app->YUI->TableSearch('ARTIKEL', 'lieferantartikelpreise', 'show','','',basename(__FILE__), __CLASS__);
    }
    else{
      $this->app->YUI->TableSearch('ARTIKEL', 'kundeartikelpreise', 'show','','',basename(__FILE__), __CLASS__);
    }

    $this->app->Tpl->Set('ID', $id);
    $this->app->Tpl->Set('FMODULE', $cmd);
    $this->app->Tpl->Set('ARTIKELBAUM',$this->app->Tpl->Parse('return', 'artikel_artikelbaum.tpl',true));

    $this->app->Tpl->Set('PAGE',"<div class='info'>Sie können hier die Stammdaten durchsuchen oder einen neuen Artikel angelegen <a class='button' id='profisuche-back-button' href='index.php?module={$cmd}&action=positionen&id={$id}'>Abbrechen</a></div>");

    $artikelart = $this->app->erp->GetArtikelgruppe($projekt);
    $typ = $this->app->Secure->GetPOST('typ');
    $this->app->Tpl->Set('ARTIKELGRUPPE',$this->app->erp->GetSelectAsso($artikelart, $typ));

    if($this->app->erp->Firmendaten('briefhtml')=='1')
    { 
      $this->app->YUI->CkEditor("kurztext_de","belege",array('height'=>'100px'));
      $this->app->YUI->CkEditor("internerkommentar","basic",array('height'=>'100px'));
    }

    $this->app->Tpl->Add('PROCESSID', $id);
    $this->app->Tpl->Add('PROCESSTYPE', $cmd);

    if ($cmd==='bestellung' || $cmd==='preisanfrage'){
      $this->app->Tpl->Parse('PAGE', 'aarlg_artikelbestellungneu.tpl');
    } else{
      $this->app->Tpl->Parse('PAGE', 'aarlg_artikelneu.tpl');
    }

    $this->app->BuildNavigation=false;
  }

  public function ArtikelAjaxWerte()
  {
    $id = $this->app->Secure->GetGET('id', 'base64');
    if(!is_numeric($id)) {
      $id = base64_decode($id);
    }
    $name = $this->app->Secure->GetGET('name');
    $sid = $this->app->Secure->GetGET('sid');
    $smodule = $this->app->Secure->GetGET('smodule');
    $menge = $this->app->Secure->GetGET('menge');
    $mengeold = $menge;
    $menge = str_replace(',','.',$menge);
    $cmd = $this->app->Secure->GetGET('cmd');
    $adresse = $this->app->Secure->GetGET('adresse');
    $vpe = $this->app->Secure->GetGET('vpe');

    // wenn plus in Artikelnummer IN GET
    //$id = str_replace('++',' ',$id);

    //          if($id=="") exit;

    if($smodule==='bestellung')
    {
      if($name!=''){
        $id = $this->app->DB->Select("SELECT id FROM artikel WHERE name_de='$name' AND geloescht!=1 AND intern_gesperrt!=1 LIMIT 1");
        if($id<=0){
          $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$name' AND geloescht!=1 AND intern_gesperrt!=1 LIMIT 1");
        }
      } else {
        $commandline = $id;
        $tmp_id = explode(' ',$commandline);
        $tmp_id = $tmp_id[0];
        //     $id = substr($id,0,6);
        if($tmp_id!='')
        {
          $id = $tmp_id;
          $tmp_id = $commandline;
          // hole ab menge aus

          $n = strpos($tmp_id, $id.' ');
          if ( false!==$n ) {
            $tmp_id = substr($tmp_id, 0, $n);
          } 
          $start_pos = strpos ($commandline, 'ab Menge ');
          $commandline = substr($commandline,$start_pos + strlen('ab Menge '));
          $end_pos = strpos ($commandline, ' ');
          if(trim(substr($commandline,0,$end_pos)) > 0)
            $menge = trim(substr($commandline,0,$end_pos));

        } else {
          $this->app->ExitXentral();
        }
        $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$id' AND geloescht!=1 AND intern_gesperrt!=1 LIMIT 1");
      }
      if(!is_numeric($id))
      {
        echo '#*##*##*##*##*##*##*#';
        $this->app->ExitXentral();
      }

      $waehrung = $this->app->DB->Select("SELECT waehrung FROM $smodule WHERE id='$sid' LIMIT 1");
      if(strtoupper($waehrung)==='EURO') {
        $waehrung='EUR';
      } // nur fuer den uebergang damit nicht alte Kunden dauern mit Tickets kommen 2.2.2019 BS

      $adresse = $this->app->DB->Select("SELECT adresse FROM $smodule WHERE id='$sid' LIMIT 1");
      //      $id = substr($id,0,6);

      $name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");

      $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");

      $name_en = $this->app->DB->Select("SELECT name_en FROM artikel WHERE id='$id' LIMIT 1");

      if($sprache==='englisch' && $name_en!=''){
        $name = $name_en;
      }

      $bestellnummer = $this->app->DB->Select("SELECT bestellnummer FROM einkaufspreise
        WHERE artikel='$id' AND adresse='$adresse' AND ab_menge<='$menge'  AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 ORDER by ab_menge DESC LIMIT 1");
      $bezeichnunglieferant = $this->app->DB->Select("SELECT bezeichnunglieferant FROM einkaufspreise
        WHERE artikel='$id' AND adresse='$adresse' AND ab_menge<='$menge' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 ORDER by ab_menge DESC LIMIT 1");
      if(empty($vpe)){
        $vpe = $this->app->DB->Select("SELECT vpe FROM einkaufspreise
        WHERE artikel='$id' AND adresse='$adresse' AND ab_menge<='$menge'  AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 ORDER by ab_menge DESC LIMIT 1");
      }
      if($bestellnummer=="") {
        $bestellnummer = $this->app->DB->Select("SELECT bestellnummer FROM einkaufspreise 
          WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 LIMIT 1");
      }

      if($bezeichnunglieferant=="") {
        $bezeichnunglieferant = $this->app->DB->Select("SELECT bezeichnunglieferant FROM einkaufspreise 
          WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 LIMIT 1");
      }

      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT p.abkuerzung FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.id='$id' LIMIT 1");
      //$projekt_id = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$id' LIMIT 1");
      //$ab_menge = $this->app->DB->Select("SELECT ab_menge FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 LIMIT 1");
      //$vpe = $this->app->DB->Select("SELECT vpe FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 LIMIT 1");
      $vpe = str_replace(',','.',$vpe);

      if($vpe == 1){
        $andVpe = '';
      }
      else{
        $andVpe = 'AND e.vpe = \''.$vpe.'\'';
      }

      if($waehrung=="" || $waehrung=="EUR")
      {

        $ek = $this->app->DB->Select(
          "SELECT e.preis 
          FROM `einkaufspreise` AS `e`
          WHERE e.artikel='$id' 
          AND e.adresse='$adresse' 
          AND e.ab_menge<='$menge' 
          AND (e.gueltig_bis>=NOW() OR e.gueltig_bis='0000-00-00') 
          AND e.geloescht=0
          $andVpe
          ORDER BY e.ab_menge DESC 
          LIMIT 1"
        );

        $waehrung = $this->app->DB->Select(
          "SELECT e.waehrung 
          FROM `einkaufspreise` AS `e`
          WHERE e.artikel='$id' 
          AND e.adresse='$adresse' 
          AND e.ab_menge<='$menge' 
          AND (e.gueltig_bis>=NOW() OR e.gueltig_bis='0000-00-00') 
          AND e.geloescht=0 
          $andVpe
          ORDER BY e.ab_menge DESC 
          LIMIT 1"
        );
      } else{
        $ek = $this->app->DB->Select(
          "SELECT e.preis 
          FROM `einkaufspreise` AS `e`
          WHERE e.artikel='$id' 
          AND e.adresse='$adresse' 
          AND e.ab_menge<='$menge' 
          AND (e.gueltig_bis>=NOW() OR e.gueltig_bis='0000-00-00') 
          AND e.waehrung='$waehrung' 
          AND e.geloescht=0
          $andVpe
          ORDER BY e.ab_menge DESC 
          LIMIT 1"
        );

      }

      if($bezeichnunglieferant=='') {
        $bezeichnunglieferant=$name;
      }

      $name = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
      if($vpe > 1)
      {
        if($menge < $vpe) {
          $menge = $vpe;
        }        
        else {
          $menge_vpe = $menge / $vpe;
          $menge = ceil($menge_vpe)*$vpe;       
        }
        //$ek = $menge*$ek;
      }
      $menge = (float)$menge;
      // bei Bestellung
      echo "$name#*#$nummer#*#$projekt#*#$ek#*#$menge#*#$bestellnummer#*#$bezeichnunglieferant#*#$vpe#*#$waehrung";
    } else {
      //Pinguio fehler
      if($id=='')
      {
        $name = $this->app->Secure->GetGET('name');
        if(trim($name)!='')
        {
          $id = $this->app->DB->Select("SELECT nummer FROM artikel WHERE nummer LIKE '$name' AND intern_gesperrt!=1 LIMIT 1");
          if($id =='')
          {
            $id = $this->app->DB->Select("SELECT nummer FROM artikel WHERE name_de LIKE '$name' AND intern_gesperrt!=1 LIMIT 1");

            if($id=='')
            {
              $name = str_replace(' ','&nbsp;',$name);
              $id = $this->app->DB->Select("SELECT nummer FROM artikel WHERE name_de LIKE '$name' AND intern_gesperrt!=1 LIMIT 1");
              //naechster fall
            }   
          }
        } else {
          if(trim($name)!='')
          {
            // wenn name leer ist hole max position id
            $id = $this->app->DB->Select("SELECT MAX(id) FROM ".$smodule."_position WHERE $smodule='$sid'");
            $id = $this->app->DB->Select("SELECT artikel FROM ".$smodule."_position WHERE id='$id' LIMIT 1");
            $id = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' AND intern_gesperrt!=1 LIMIT 1");
          }

        }
        if($id ==''){
          $this->app->ExitXentral();
        }

      }
      //      $id = substr($id,0,6);
      //echo $id;
      //      if(!is_numeric($id))
      //        exit;
      $tmp_id = explode(' ',$id);
      $id = $tmp_id[0];

      $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$id' AND intern_gesperrt!=1 LIMIT 1");
      $warnung = 1-(int)$this->app->DB->Select("SELECT if(rabatt=1,1,vkmeldungunterdruecken) FROM artikel WHERE id = '$id' LIMIT 1");
      $adresse = $this->app->DB->Select("SELECT adresse FROM $smodule WHERE id='$sid' LIMIT 1");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM $smodule WHERE id='$sid' LIMIT 1");
      $posanz = (int)$this->app->DB->Select("SELECT count(id) FROM $smodule"."_position WHERE $smodule = '$sid'");
      //if($posanz == 0){
      //  $waehrung = '';
      //}
      
      $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");

      $name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");

      $name_en = $this->app->DB->Select("SELECT name_en FROM artikel WHERE id='$id' LIMIT 1");

      if($sprache==='englisch' && $name_en!=''){
        $name = $name_en;
      }

      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT p.abkuerzung FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.id='$id' LIMIT 1");

      //$projekt_id = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$id' LIMIT 1");

      //      $ab_menge = $this->app->DB->Select("SELECT ab_menge FROM verkaufspreise WHERE artikel='$id' AND ab_menge=1  AND geloescht=0 LIMIT 1");
      $ab_menge = $menge;

      if($smodule==="inventur")
        $preis = $this->app->erp->GetEinkaufspreis($id,$menge,$adresse);
      else {
        if(method_exists($this->app->erp, 'GetVerkaufspreisMitWaehrung'))
        {
          $preis = $this->app->erp->GetVerkaufspreisMitWaehrung($id,$menge,$adresse, $waehrung);
        }else{
          $preis = $this->app->erp->GetVerkaufspreis($id,$menge,$adresse, $waehrung);
        }
      }
      
      if($smodule === 'auftrag' || $smodule === 'rechnung' || $smodule === 'gutschrift' || $smodule === 'angebot' || $smodule === 'proformarechnung')
      {
        $_anrede = $this->app->DB->Select("SELECT typ FROM $smodule WHERE id = '$sid' LIMIT 1");
        $_projekt = $this->app->DB->Select("SELECT projekt FROM $smodule WHERE id = '$sid' LIMIT 1");
        $_adresse = $this->app->DB->Select("SELECT adresse FROM $smodule WHERE id = '$sid' LIMIT 1");
        $funktion = ucfirst($smodule).'MitUmsatzeuer';
        if($this->app->erp->AnzeigePositionenBrutto($_anrede, $smodule, $_projekt, $_adresse) && $this->app->erp->$funktion($sid))
        {
          $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$id' LIMIT 1");
          if($umsatzsteuer === 'ermaessigt')
          {
            $preis = round($preis* (1+ (float)$this->app->DB->Select("SELECT steuersatz_ermaessigt FROM $smodule WHERE id = '$sid' LIMIT 1")/100),7);
          }elseif($umsatzsteuer !== 'befreit')
          {
            $preis = round($preis * (1+ (float)$this->app->DB->Select("SELECT steuersatz_normal FROM $smodule WHERE id = '$sid' LIMIT 1")/100),7);
          }
        }
      }

      $name = html_entity_decode($name, ENT_QUOTES, 'UTF-8');

      $ab_menge = round((float)$ab_menge,8);
      switch($smodule)
      {
        case 'auftrag':
        case 'gutschrift':
        case 'angebot':
        case 'rechnung':
        case 'proformarechnung':
          if(!isset($waehrung) || $waehrung == ''){
            $waehrung = $this->app->erp->GetVerkaufspreisWaehrung($id,$menge,$adresse);
          }
          if(strpos($mengeold, ',') && strpos($ab_menge,'.') && strpos($ab_menge ,',') === false){
            $ab_menge = str_replace('.',',',$ab_menge);
          }
          $this->app->erp->RunHook('artikel_ajaxwerte',8, $id, $smodule, $name,$nummer, $preis, $ab_menge, $waehrung, $warnung);
          echo "$name#*#$nummer#*#$projekt#*#$preis#*#$ab_menge#*#$waehrung#*#$warnung#";
        break;
        default:
          echo "$name#*#$nummer#*#$projekt#*#$preis#*#$ab_menge";
        break;
      }
    }
    $this->app->ExitXentral();
  }

  public function ArtikelWareneingang()
  {
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Wareneingang)');
    $this->ArtikelMenu();
    $this->app->Tpl->Set('PAGE','wareneingang');
  }

  public function ArtikelReservierung()
  {
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Reservierungen)');
    $this->ArtikelMenu();
    $this->app->Tpl->Set('PAGE','reservierung');
  }


  public function ArtikelOffeneAuftraege()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->Tpl->Add('TABTEXT','Auftr&auml;ge');
    $this->ArtikelMenu();

    // easy table mit arbeitspaketen YUI als template 

    $this->app->YUI->TableSearch('TAB1','artikel_auftraege_offen', 'show','','',basename(__FILE__), __CLASS__);

    $summe = $this->app->DB->Select("SELECT TRIM(SUM(ap.menge)-SUM(ap.geliefert_menge))+0 FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben'");
    $euro= $this->app->DB->Select("SELECT FORMAT(SUM(ap.preis*(100-ap.rabatt)/100*ap.menge),2,'de_DE') FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben'");

    $this->app->Tpl->Add('TAB1',"<table width=\"100%\"><tr><td align=\"right\">Summe offen: $summe St&uuml;ck (Summe EUR: $euro EUR)</td></tr></table>");

    $this->app->YUI->TableSearch('TAB2','artikel_auftraege_versendet', 'show','','',basename(__FILE__), __CLASS__);

    $this->app->Tpl->Parse('PAGE','artikel_auftraege.tpl');
  }

  public function ArtikelDateien()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->ArtikelMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Dateien)');
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'down' || $cmd === 'up') {
      $this->app->DB->Update(
        sprintf(
          'UPDATE artikel SET bildvorschau = \'\' WHERE id = %d LIMIT 1',
          (int)$id
        )
      );
    }
    $this->app->YUI->DateiUpload('PAGE','Artikel',$id);
  }
  
  public function ArtikelCopyWaehrungen($id, $typ = 'verkauf')
  {
    $res = array('status' => 0);
    $dat = $this->app->Secure->GetPOST('dat');
    $menge_ab = (int)$this->app->Secure->GetPOST('menge_ab');
    $art = $this->app->Secure->GetPOST('art');
    $bezeichnunglieferant = $this->app->Secure->GetPOST('bezeichnunglieferant');
    $bestellnummer = $this->app->Secure->GetPOST('bestellnummer');
    
    $gruppe = $this->app->erp->ReplaceGruppe(true, $this->app->Secure->GetPOST('gruppe'),true);
    $kundenartikelnummer = $this->app->Secure->GetPOST('kundenartikelnummer');
    $gueltig_bis = $this->app->Secure->GetPOST('gueltig_bis');
    if($gueltig_bis){
      $gueltig_bis = $this->app->String->Convert($gueltig_bis,'%1.%2.%3','%3-%2-%1');
    }
    $vpe = (int)$this->app->Secure->GetPOST('vpe');
    if($vpe < 1){
      $vpe = 1;
    }
    if($menge_ab < 1){
      $menge_ab = 1;
    }
    if(!$dat)
    {
      echo json_encode($res);
      $this->app->ExitXentral();
    }
    $data = explode(';',$dat);
    foreach($data as $k => $v)
    {
      $v = trim($v);
      if(strpos($v,':'))
      {
        $va = explode(':',$v);
        if(count($va) > 1){
          $waehrung = trim($va[0]);
          $preis = (float)str_replace(',','.',trim($va[1]));
          if($waehrung)
          {
            $waehrungen[$waehrung] = $preis;
          }
        }
      }
    }
    if(!isset($waehrungen))
    {
      echo json_encode($res);
      $this->app->ExitXentral();
    }
    
    switch($typ)
    {
      case 'einkauf':
        $adresse =  $this->app->erp->ReplaceLieferant(true,$this->app->Secure->GetPOST('adresse'),true);
        $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE id = '$id' LIMIT 1");
        
        if(!$artikel)
        {
          echo json_encode($res);
          $this->app->ExitXentral();
        }
        foreach($waehrungen as $waehrung => $preis)
        {
          $this->app->erp->AddEinkaufspreis($artikel,$menge_ab,$adresse,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung,$vpe);
        }
        $res['status'] = 1;
        echo json_encode($res);
        $this->app->ExitXentral();
      break;
      case 'einkaufeditpopup':
        $artikel = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id = '$id' LIMIT 1");
        if(!$artikel)
        {
          echo json_encode($res);
          $this->app->ExitXentral();
        }
        foreach($waehrungen as $waehrung => $preis)
        {        
          $newid = $this->app->DB->MysqlCopyRow("einkaufspreise","id",$id);
          $this->app->DB->Update("UPDATE einkaufspreise SET geloescht='0', gueltig_bis='0000-00-00', nichtberechnet = 0,preis = '$preis', waehrung = '".$waehrung."' WHERE id='$newid' LIMIT 1");
        }
        $res['status'] = 1;
        echo json_encode($res);
        $this->app->ExitXentral();
      break;
      case 'verkauf':
        $adresse =  $this->app->erp->ReplaceKunde(true,$this->app->Secure->GetPOST('adresse'),true);
        $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE id = '$id' LIMIT 1");
        if(!$artikel)
        {
          echo json_encode($res);
          $this->app->ExitXentral();
        }
        foreach($waehrungen as $waehrung => $preis)
        { 
          $this->app->erp->AddVerkaufspreis($artikel,$menge_ab,$adresse,$preis,$waehrung,$kundenartikelnummer, $gruppe);
        }
        $res['status'] = 1;
        echo json_encode($res);
        $this->app->ExitXentral();
      break;
      case 'verkaufeditpopup':
        $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id = '$id' LIMIT 1");
        if(!$artikel)
        {
          echo json_encode($res);
          $this->app->ExitXentral();
        }
        foreach($waehrungen as $waehrung => $preis)
        {        
          $newid = $this->app->DB->MysqlCopyRow('verkaufspreise','id',$id);
          $this->app->DB->Update("UPDATE verkaufspreise SET geloescht='0', gueltig_bis='0000-00-00', nichtberechnet = 0,preis = '$preis', waehrung = '".$waehrung."' WHERE id='$newid' LIMIT 1");
        }
        $res['status'] = 1;
        echo json_encode($res);
        $this->app->ExitXentral();
      break;
    }
    
    echo json_encode($res);
    $this->app->ExitXentral();
  }

  public function ArtikelVerkauf()
  {
    // rechne gueltig_bis gestern aus
    // erstelle array objekt, adressse, ab_menge,preis
    // wenn es doppelte gibt rote meldung!!!
    //$this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Achtung es gibt f&uuml;r eine Kundengruppe bei einer gleichen Menge den Preis &ouml;fters! Deaktvieren oder l&ouml;schen Sie doppelte Preise!</div>");
    if($this->app->Secure->GetPOST('newpreis'))
    {
      $id = $this->app->Secure->GetGET('id');
      $this->ArtikelCopyWaehrungen($id, 'verkauf');
    }
    $waehrungenstringvk = '';

    $standardwaehrung = $this->app->erp->GetStandardWaehrung();

    $waehrungenvk = $this->app->DB->SelectArr("SELECT DISTINCT waehrung FROM (SELECT DISTINCT waehrung_von AS waehrung FROM waehrung_umrechnung WHERE (ISNULL(gueltig_bis) OR gueltig_bis >= NOW() OR gueltig_bis = '0000-00-00') UNION SELECT DISTINCT waehrung_nach AS waehrung FROM waehrung_umrechnung WHERE (ISNULL(gueltig_bis) OR gueltig_bis >= NOW() OR gueltig_bis = '0000-00-00')) AS waehrungen");
    if($waehrungenvk != ''){
      $standardwvorhanden = false;
      if($standardwaehrung == '') {
        $standardwaehrung = $waehrungenvk[0]['waehrung'];
      }
      foreach($waehrungenvk as $key=>$value) {
        if($value['waehrung'] == $standardwaehrung){
          $standardwvorhanden = true;
        }
        $waehrungenstringvk .= "<option value=\"".$value['waehrung']."\">".$value['waehrung'].'</option>';
      }
      $this->app->Tpl->Set('STANDARDWAEHRUNGV', $standardwaehrung);
      if($standardwvorhanden == false){
        $waehrungenstringvk .= "<option value=\"".$standardwaehrung.'>'.$standardwaehrung.'</option>';
      }      
    }else{
      if($standardwaehrung != ''){
        if($standardwaehrung !== 'EUR' && $standardwaehrung !== 'USD' && $standardwaehrung !== 'CAD' && $standardwaehrung !== 'CHF' && $standardwaehrung !== 'GBP'){
          $waehrungenstringvk .= "<option value=".$standardwaehrung.' selected>'.$standardwaehrung.'</option>';
        }
        $this->app->Tpl->Set('STANDARDWAEHRUNGV', $standardwaehrung);
      }else{
          $this->app->Tpl->Set('STANDARDWAEHRUNGV', 'EUR');
      }

      $waehrungenstringvk .= "<option value='EUR'>EUR</option>
                              <option value='USD'>USD</option>
                              <option value='CAD'>CAD</option>
                              <option value='CHF'>CHF</option>
                              <option value='GBP'>GBP</option>";
    }

    
    $this->app->Tpl->Set('WAEHRUNGVERKAUF', $waehrungenstringvk);



    $this->app->Tpl->Add('UEBERSCHRIFT',' (Verkauf)');
    $this->app->Tpl->Set('SUBSUBHEADING','Verkaufspreise');
    $this->ArtikelMenu();
    $this->Preisrechner();
    $id = $this->app->Secure->GetGET('id');
    // neues arbeitspaket
    $widget = new WidgetVerkaufspreise($this->app,'TAB2');
    $widget->form->SpecialActionAfterExecute('none',
      "index.php?module=artikel&action=verkauf&id=$id");

    //if($this->app->Secure->GetPOST("submit")!="")
    //$this->app->erp->EnableTab("tabs-2");

    //$widget->Create();


    $this->app->YUI->TableSearch('TAB1','verkaufspreise', 'show','','',basename(__FILE__), __CLASS__);
    $stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='$id' LIMIT 1");
    $porto = $this->app->DB->Select("SELECT porto FROM artikel WHERE id='$id' LIMIT 1");

    if($stueckliste!='1') {
      $max_preis = $this->app->DB->Select("SELECT MAX(preis) FROM einkaufspreise WHERE artikel='$id' AND (gueltig_bis='0000-00-00' 
        OR gueltig_bis >= curdate()) AND geloescht!=1 LIMIT 1");

      $min_preis = $this->app->DB->Select("SELECT MIN(preis) FROM einkaufspreise WHERE artikel='$id' AND (gueltig_bis='0000-00-00' 
        OR gueltig_bis >= curdate()) AND geloescht!=1 LIMIT 1");

      $waehrung = $this->app->DB->Select("SELECT waehrung FROM einkaufspreise WHERE artikel='$id' AND (gueltig_bis='0000-00-00'
        OR gueltig_bis >= curdate()) AND geloescht!=1 ORDER by preis LIMIT 1");

      $max_preis2 = $max_preis;
      $min_preis2 = $min_preis;
      
      $min_preis = $this->app->erp->EUR($min_preis*(($this->app->erp->GetStandardMarge()/100.0)+1.0)*1.0);
      $max_preis = $this->app->erp->EUR($max_preis*(($this->app->erp->GetStandardMarge()/100.0)+1.0)*1.0);

      $min_preis2 = $this->app->erp->EUR($min_preis2/(100-$this->app->erp->GetStandardMarge())*100);
      $max_preis2 = $this->app->erp->EUR($max_preis2/(100-$this->app->erp->GetStandardMarge())*100);

    }

    if($porto=='1') {
      $this->app->Tpl->Add('TAB1',"<div class=\"warning\">Kundenspezifische Preise werden immer priorisiert!</div>");
    } 
    else {

      if($this->app->erp->GetStandardMarge() > 0)// && $stueckliste!="1")
      {
        if($max_preis <>0){
          $this->app->Tpl->Add('TAB1', "<div class=\"warning\">Empfohlener Verkaufspreis netto (f&uuml;r teuersten EK Preis): <b>$max_preis $waehrung</b> bzw. als Aufschlagsrechnung $max_preis2 $waehrung</div>");
        }

        if($min_preis <>0){
          $this->app->Tpl->Add('TAB1', "<div class=\"warning\">Empfohlener Verkaufspreis netto (f&uuml;r billigsten EK Preis): <b>$min_preis $waehrung</b> bzw. als Aufschlagsrechnung $min_preis2 $waehrung</div>");
        }
        
        
        if($max_preis <>0){
          $this->app->Tpl->Add('TAB2', "<div class=\"warning\">Empfohlener Verkaufspreis netto (f&uuml;r teuersten EK Preis): <b>$max_preis $waehrung</b> bzw. als Aufschlagsrechnung $max_preis2 $waehrung</div>");
        }
        if($min_preis <>0){
          $this->app->Tpl->Add('TAB2', "<div class=\"warning\">Empfohlener Verkaufspreis netto (f&uuml;r billigsten EK Preis): <b>$min_preis $waehrung</b> bzw. als Aufschlagsrechnung $min_preis2 $waehrung</div>");
        }
      }
    }

    if($this->app->Secure->GetGET('cmd')==='popupedit'){
      $eid = (int)$this->app->Secure->GetPOST('id');
      
      $data = $this->app->DB->SelectRow("SELECT id, artikel, adresse, preis, waehrung, ab_menge, vpe, vpe_menge, gueltig_bis, bemerkung, kundenartikelnummer, art, gruppe, nichtberechnet, gueltig_ab,if(kurs<0,'',".$this->app->erp->FormatMenge('kurs').") as kurs,IF(ISNULL(kursdatum),'',DATE_FORMAT(kursdatum,'%d.%m.%Y')) as kursdatum, inbelegausblenden FROM verkaufspreise WHERE id = '$eid' LIMIT 1");
      
      if($data){
        if($data['adresse'] > 0 && $data['art'] === 'Kunde'){
          $ekundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '".$data['adresse']."' LIMIT 1");
          $ekundenname = $this->app->DB->Select("SELECT name FROM adresse WHERE id = '".$data['adresse']."' LIMIT 1");
          $data['adresse'] = $ekundennummer." ".$ekundenname;
          $data['gruppe'] = '';
        }elseif($data['adresse'] == 0 && $data['art'] === 'Kunde'){
          $data['adresse'] = '';
          $data['gruppe'] = '';
        }
        
        if($data['gruppe'] > 0 && $data['art'] === 'Gruppe'){
          $egruppenname = $this->app->DB->Select("SELECT name FROM gruppen WHERE id = '".$data['gruppe']."' LIMIT 1");
          $egruppenkennziffer = $this->app->DB->Select("SELECT kennziffer FROM gruppen WHERE id = '".$data['gruppe']."' LIMIT 1");
          $data['gruppe'] = $egruppenkennziffer." ".$egruppenname;
          $data['adresse'] = '';
        }

        $data['ab_menge'] = str_replace('.',',',$data['ab_menge']);
        $data['preis'] = str_replace('.',',',$data['preis']);
        
        if($data['gueltig_ab'] == '0000-00-00' || $data['gueltig_ab'] == '' || is_null($data['gueltig_ab'])){
          $data['gueltig_ab'] = "00.00.0000";
        }else{
          $data['gueltig_ab'] = date('d.m.Y',strtotime($data['gueltig_ab'])); 
        }

        if($data['gueltig_bis'] == '0000-00-00' || $data['gueltig_bis'] == '' || is_null($data['gueltig_bis'])){
          $data['gueltig_bis'] = "00.00.0000";
        }else{
          $data['gueltig_bis'] = date('d.m.Y',strtotime($data['gueltig_bis']));
        }
       
      }else{
        //$data['artikel'] = 0;
        $data['adresse'] = '';
        $data['gruppe'] = '';
        $data['art'] = "Kunde";
        $data['preis'] = '';
        $data['waehrung'] = "EUR";
        $data['ab_menge'] = '';
        $data['vpe'] = '';
        $data['kundenartikelnummer'] = '';
        $data['gueltig_ab'] = '';
        $data['gueltig_bis'] = '';
        $data['bemerkung'] = '';
        $data['kurs'] = '';
        $data['kurdatum'] = '';
        $data['nichtberechnet'] = 0; 
        $data['inbelegausblenden'] = 0;
      }

      echo json_encode($data);
      $this->app->ExitXentral();
    }

    if($this->app->Secure->GetGET('cmd')==='popupsave'){
      $eid = $this->app->Secure->GetPOST('eid');
      $eartikelid = $this->app->Secure->GetPOST('eartikelid');
      $eart = trim($this->app->Secure->GetPOST('eart'));
      $eadresse = trim($this->app->Secure->GetPOST('eadresse'));
      $egruppe = trim($this->app->Secure->GetPOST('egruppe'));
      $ekundenartikelnummer = trim($this->app->Secure->GetPOST('ekundenartikelnummer'));
      $eab_menge = trim($this->app->Secure->GetPOST('eab_menge'));
      $evpe = trim($this->app->Secure->GetPOST('evpe'));
      $epreis = trim($this->app->Secure->GetPOST('epreis'));
      $ewaehrung = trim($this->app->Secure->GetPOST('ewaehrung'));
      $enichtberechnet = $this->app->Secure->GetPOST('enichtberechnet');
      $inbelegausblenden = $this->app->Secure->GetPOST('inbelegausblenden');
      $egueltig_ab = trim($this->app->Secure->GetPOST('egueltig_ab'));
      $egueltig_bis = trim($this->app->Secure->GetPOST('egueltig_bis'));
      $ebemerkung = trim($this->app->Secure->GetPOST('ebemerkung'));

      $error = '';

      $eab_menge = str_replace(',', '.', $eab_menge);
      if($eab_menge > 0 && $eab_menge != ''){
      }else{
        $error .= 'Mengef'."\n";
      }

      $kundenname = $eadresse;
      if($eadresse != '' && $eart === 'Kunde'){
        $eadresse = explode(' ', $eadresse, 2);
        $eadresse = $eadresse[0];
        
        $eadresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer = '$eadresse' AND kundennummer != '' AND geloescht = 0 LIMIT 1");
        if($eadresse != ''){
        }else{
          $error .= 'Adressef'."\n";
        }
      }elseif($eadresse == '' || $eadresse <= 0){
        $eadresse = 0;
      }

      if($egruppe != '' && $eart === 'Gruppe'){
        $egruppe = explode(' ', $egruppe, 2);
        $egruppe = $egruppe[0];
        $egruppe = $this->app->DB->Select("SELECT id FROM gruppen WHERE kennziffer = '$egruppe' AND name != '' LIMIT 1");
      }else{
        if($egruppe == '' && $eart === 'Gruppe'){
          $error .= 'Gruppe ausfüllen';
        }
      }

      if($epreis != ''){
        $epreis = str_replace(',', '.', $epreis);
      }else{
        $error .= 'Preisf'."\n";
      }

      if($eartikelid > 0 && $eartikelid != ''){
        $eartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE id = '$eartikelid' LIMIT 1");
        if($eartikelid != ''){
        }else{
          $error .= 'Bitte gültigen Artikel wählen'."\n";
        }
      }else{
        $error .= 'Bitte gültigen Artikel wählen'."\n";
      }

      
      if($egueltig_ab != '' && $egueltig_ab != '0000-00-00' && $egueltig_ab != '00.00.0000'){
        $egueltig_ab = date('Y-m-d',strtotime($egueltig_ab)); 
      }else{
        $egueltig_ab = '0000-00-00';
      }
      if($egueltig_bis != '' && $egueltig_bis != '0000-00-00' && $egueltig_bis != '00.00.0000'){
        $egueltig_bis = date('Y-m-d',strtotime($egueltig_bis));
      }else{
        $egueltig_bis = '0000-00-00';
      }

      if($ewaehrung == ''){
        $ewaehrung = $this->app->erp->GetStandardWaehrung();
      }


      if($error == ''){
        if($eid > 0){
          if($eart === 'Kunde'){
            $standardvorhanden = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE artikel = '$eartikelid' AND ab_menge = '$eab_menge' AND preis = '$epreis' AND art = 'Kunde' AND adresse = '$eadresse' AND gueltig_ab = '$egueltig_ab' AND gueltig_bis = '$egueltig_bis' AND geloescht != 1 AND id != '$eid' LIMIT 1");
          }

          if($eart === 'Gruppe' && $egruppe > 0){
            $gruppenpreisvorhanden = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE artikel = '$eartikelid' AND ab_menge = '$eab_menge' AND preis = '$epreis' AND art = 'Gruppe' AND gruppe = '$egruppe' AND gueltig_ab = '$egueltig_ab' AND gueltig_bis = '$egueltig_bis' AND geloescht != 1 AND id != '$eid' LIMIT 1");
          }

          if($standardvorhanden != ''){
            $error .= 'Diesen Preis gibt es bereits.'."\n";
            echo json_encode(array('status'=>0,'statusText'=>$error));
            $this->app->ExitXentral();
          }
          if($gruppenpreisvorhanden != ''){
            $error .= 'Diesen Preis gibt es bereits.'."\n";
            echo json_encode(array('status'=>0,'statusText'=>$error));
            $this->app->ExitXentral();
          }
          $this->app->DB->Update("UPDATE verkaufspreise SET adresse = '$eadresse', preis = '$epreis', waehrung = '$ewaehrung', ab_menge = '$eab_menge', vpe = '$evpe', gueltig_bis = '$egueltig_bis', bemerkung = '$ebemerkung', firma = 1, geloescht = 0, kundenartikelnummer = '$ekundenartikelnummer', art = '$eart', gruppe = '$egruppe', nichtberechnet = '$enichtberechnet', gueltig_ab = '$egueltig_ab', inbelegausblenden = '$inbelegausblenden' WHERE id = '$eid'");
          if($ewaehrung === 'EUR' || $ewaehrung === '')
          {
            $kurs = -1;
            $kursdatum = 'NULL';
          }else{
            $kurs = $this->app->erp->GetWaehrungUmrechnungskurs('EUR', $ewaehrung, true);
          }
          if($kurs !== false){
            if($kurs !== -1){
              $kursdatum = "'" . date('Y-m-d') . "'";
            }
            $this->app->DB->Update("UPDATE verkaufspreise SET kurs = $kurs, kursdatum = $kursdatum WHERE id = $eid LIMIT 1");
          }
          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }

        if($eart === 'Kunde'){
          $standardvorhanden = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE artikel = '$eartikelid' AND ab_menge = '$eab_menge' AND preis = '$epreis' AND art = 'Kunde' AND adresse = '$eadresse' AND gueltig_ab = '$egueltig_ab' AND gueltig_bis = '$egueltig_bis' AND geloescht != 1 LIMIT 1");
        }

        if($eart === 'Gruppe' && $egruppe > 0){
          $gruppenpreisvorhanden = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE artikel = '$eartikelid' AND ab_menge = '$eab_menge' AND preis = '$epreis' AND art = 'Gruppe' AND gruppe = '$egruppe' AND gueltig_ab = '$egueltig_ab' AND gueltig_bis = '$egueltig_bis' AND geloescht != 1 LIMIT 1");
        }

        if($standardvorhanden != ''){
          $error .= 'Diesen Preis gibt es bereits.'."\n";
          echo json_encode(array('status'=>0,'statusText'=>$error));
          $this->app->ExitXentral();
        }
        if($gruppenpreisvorhanden != ''){
          $error .= 'Diesen Preis gibt es bereits.'."\n";
          echo json_encode(array('status'=>0,'statusText'=>$error));
          $this->app->ExitXentral();
        }
        $this->app->DB->Insert("INSERT INTO verkaufspreise (artikel, adresse, preis, waehrung, ab_menge, vpe, gueltig_bis, bemerkung, firma, geloescht, kundenartikelnummer, art, gruppe, apichange, nichtberechnet, gueltig_ab, inbelegausblenden) VALUES ('$eartikelid', '$eadresse', '$epreis', '$ewaehrung', '$eab_menge', '$evpe', '$egueltig_bis', '$ebemerkung', 1, 0, '$ekundenartikelnummer', '$eart', '$egruppe', '', '$enichtberechnet', '$egueltig_ab', '$inbelegausblenden')");
        $insid = $this->app->DB->GetInsertID();
        if($ewaehrung === 'EUR' || $ewaehrung === '')
        {
          $kurs = false;
        }else{
          $kurs = $this->app->erp->GetWaehrungUmrechnungskurs('EUR', $ewaehrung, true);
        }
        if($kurs !== false){
          if($kurs !== -1){
            $kursdatum = "'" . date('Y-m-d') . "'";
          }
          $this->app->DB->Update("UPDATE verkaufspreise SET kurs = $kurs, kursdatum = $kursdatum WHERE id = $insid LIMIT 1");
        }
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
      echo json_encode(array('status'=>0,'statusText'=>$error));
      $this->app->ExitXentral();
    }

    $this->app->YUI->AutoComplete('adresse', 'kunde');
    $this->app->YUI->AutoComplete('gruppe', 'gruppekennziffer');
    $this->app->YUI->DatePicker('gueltig_ab');
    $this->app->YUI->DatePicker('gueltig_bis');

    $this->app->Tpl->Parse('PAGE','verkaufspreiseuebersicht.tpl');
  }

  public function ArtikelVerkaufDisable()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE id='$id' LIMIT 1");
      $this->app->erp->ObjektProtokoll('verkaufspreise', $id, 'verkaufspreise_edit', 'Verkaufspreis deaktiviert');
    }
    $sid = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$id' LIMIT 1");
    $this->app->Location->execute('index.php?module=artikel&action=verkauf&id='.$sid);
  }

  public function ArtikelVerkaufDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->DB->Update("UPDATE verkaufspreise SET geloescht='1', gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY), logdatei = now() WHERE id='$id' LIMIT 1");
    }
    $sid = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$id' LIMIT 1");
    $this->app->erp->ObjektProtokoll('verkaufspreise', $id, 'verkaufspreise_edit', 'Verkaufspreis gel&ouml;scht');
    $this->app->Location->execute('index.php?module=artikel&action=verkauf&id='.$sid);
  }

  public function ArtikelVerkaufCopy()
  {
    $id = $this->app->Secure->GetGET('id');

    $id = $this->app->DB->MysqlCopyRow('verkaufspreise','id',$id);
    $this->app->DB->Update("UPDATE verkaufspreise SET geloescht='0', gueltig_bis='0000-00-00', logdatei = now(), angelegt_am = now() WHERE id='$id' LIMIT 1");
    $this->app->erp->ObjektProtokoll('verkaufspreise', $id, 'verkaufspreise_create', 'Verkaufspreis kopiert');
    $sid = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$id' LIMIT 1");
    $this->app->Location->execute('index.php?module=artikel&action=verkauf&id='.$sid);
  }

  public function ArtikelVerkaufEditPopup()
  {
    $id = $this->app->Secure->GetGET('id');
    
    if($this->app->Secure->GetPOST('newpreis'))
    {
      $id = $this->app->Secure->GetGET('id');
      $this->ArtikelCopyWaehrungen($id, 'verkaufeditpopup');
    }
    
    $this->app->Tpl->Set('OPENDISABLE','<!--');
    $this->app->Tpl->Set('CLOSEDISABLE','-->');

    $this->Preisrechner();
    $sid = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$id' LIMIT 1");
    $this->ArtikelMenu($sid);
    $artikel = $this->app->DB->Select("SELECT CONCAT(name_de,' (',nummer,')') FROM artikel WHERE id='$sid' LIMIT 1");
    $this->app->Tpl->Set('UEBERSCHRIFT','Artikel: '.$artikel);
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Verkauf)');

    $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=verkauf&id=$sid';\">");

    $widget = new WidgetVerkaufspreise($this->app,'TAB1');
    $widget->form->SpecialActionAfterExecute('close_refresh',
        "index.php?module=artikel&action=verkauf&id=$sid&&22#tabs-1");
    $widget->Edit();

    $this->app->Tpl->Add('TAB2','Sie bearbeiten gerade einen Verkaufspreis. Erst nach dem Speichern k&ouml;nnen neue Preise angelegt werden.');
    $this->app->Tpl->Add('TAB3','Sie bearbeiten gerade einen Verkaufspreis. Erst nach dem Speichern k&ouml;nnen Statistiken betrachtet werden.');
    $this->app->Tpl->Parse('PAGE','verkaufspreiseuebersicht.tpl');
  }

  public function ArtikelEinkauf()
  {
    $waehrungenstringek = '';
    if($this->app->Secure->GetPOST('ajaxadd')){
      $this->ArtikelEinkaufAdd();
    }
    if($this->app->Secure->GetPOST('newpreis'))
    {
      $id = $this->app->Secure->GetGET('id');
      $this->ArtikelCopyWaehrungen($id, 'einkauf');
    }
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Einkauf)');
    $this->Preisrechner();
    $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET('id');


    $standardwaehrung = $this->app->erp->GetStandardWaehrung();

    $waehrungenek = $this->app->DB->SelectArr("SELECT DISTINCT waehrung FROM (SELECT DISTINCT waehrung_von AS waehrung FROM waehrung_umrechnung WHERE (ISNULL(gueltig_bis) OR gueltig_bis >= NOW() OR gueltig_bis = '0000-00-00') UNION SELECT DISTINCT waehrung_nach AS waehrung FROM waehrung_umrechnung WHERE (ISNULL(gueltig_bis) OR gueltig_bis >= NOW() OR gueltig_bis = '0000-00-00')) AS waehrungen");
    if($waehrungenek != ''){
      $standardwvorhanden = false;
      if($standardwaehrung == ''){
        $standardwaehrung = $waehrungenek[0]['waehrung'];
      }
      foreach($waehrungenek as $key=>$value){
        if($value['waehrung'] == $standardwaehrung){
          $standardwvorhanden = true;
        }
        $waehrungenstringek .= "<option value=\"".$value['waehrung']."\">".$value['waehrung'].'</option>';
      }
      $this->app->Tpl->Set('STANDARDWAEHRUNG', $standardwaehrung);
      if($standardwvorhanden == false){
        $waehrungenstringek .= "<option value=\"".$standardwaehrung.">".$standardwaehrung.'</option>';
      }      
    }else{
      if($standardwaehrung != ""){
        if($standardwaehrung !== 'EUR' && $standardwaehrung !== 'USD' && $standardwaehrung !== 'CAD' && $standardwaehrung !== 'CHF' && $standardwaehrung !== 'GBP'){
          $waehrungenstringek .= '<option value='.$standardwaehrung.' selected>'.$standardwaehrung.'</option>';
        }
        $this->app->Tpl->Set('STANDARDWAEHRUNG', $standardwaehrung);
      }else{
          $this->app->Tpl->Set('STANDARDWAEHRUNG', 'EUR');
      }

      $waehrungenstringek .= "<option value='EUR'>EUR</option>
                              <option value='USD'>USD</option>
                              <option value='CAD'>CAD</option>
                              <option value='CHF'>CHF</option>
                              <option value='GBP'>GBP</option>";
    }

    $this->app->Tpl->Set('WAEHRUNGEINKAUF', $waehrungenstringek);

    $standardlieferant = $this->app->DB->Select("SELECT CONCAT(adr.lieferantennummer,' ',adr.name) FROM artikel a LEFT
        JOIN adresse adr ON adr.id=a.adresse WHERE a.id='$id'");

    $herstellernummer = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='$id' LIMIT 1");
    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");

    $bereinigtername = str_replace('&apos;', "\'", $name_de);

    $this->app->Tpl->Set('BUTTONLADEN',"<input type=\"button\" class=\"button button-secondary\" value=\"Standard laden\" 
        onclick=\"
        $('#editEinkaufspreis').find('#adresse').val('$standardlieferant');
        $('#editEinkaufspreis').find('#standard').prop('checked', 1);
        $('#editEinkaufspreis').find('#bezeichnunglieferant').val('".htmlspecialchars($bereinigtername)."');
        $('#editEinkaufspreis').find('#ab_menge').val('1');
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'.'+mm+'.'+yyyy;
        $('#editEinkaufspreis').find('#preis_anfrage_vom').val(today);
        $('#editEinkaufspreis').find('#bestellnummer').val('$herstellernummer');
        \">");

    //$this->app->YUI->DatePicker('preis_anfrage_vom');

    if($this->app->Secure->GetGET('cmd')==='popupedit'){
      $eid = (int)$this->app->Secure->GetPOST('id');
      
      $data = $this->app->DB->SelectRow("SELECT id, artikel, adresse, preis, waehrung, ab_menge, vpe, preis_anfrage_vom, 
              gueltig_bis, lieferzeit_standard, lieferzeit_standard_einheit, lieferzeit_aktuell, lieferzeit_aktuell_einheit, lager_lieferant, datum_lagerlieferant, bestellnummer, 
              bezeichnunglieferant, sicherheitslager, bemerkung, standard, rahmenvertrag, rahmenvertrag_von, rahmenvertrag_bis, 
              rahmenvertrag_menge, nichtberechnet 
              FROM einkaufspreise WHERE id = '$eid' LIMIT 1");
      
      if($data){
        $elieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id = '".$data['adresse']."' LIMIT 1");
        $elieferantenname = $this->app->DB->Select("SELECT name FROM adresse WHERE id = '".$data['adresse']."' LIMIT 1");
        $data['adresse'] = $elieferantennummer." ".$elieferantenname;

        $data['ab_menge'] = str_replace('.',',',$data['ab_menge']);
        $data['preis'] = str_replace('.',',',$data['preis']);
        
        if($data['preis_anfrage_vom'] == '0000-00-00' || $data['preis_anfrage_vom'] == "" || is_null($data['preis_anfrage_vom'])){
          $data['preis_anfrage_vom'] = "00.00.0000";
        }else{
          $data['preis_anfrage_vom'] = date('d.m.Y',strtotime($data['preis_anfrage_vom'])); 
        }

        if($data['gueltig_bis'] == '0000-00-00' || $data['gueltig_bis'] == "" || is_null($data['gueltig_bis'])){
          $data['gueltig_bis'] = "00.00.0000";
        }else{
          $data['gueltig_bis'] = date('d.m.Y',strtotime($data['gueltig_bis']));
        }

        if($data['datum_lagerlieferant'] == '0000-00-00' || $data['datum_lagerlieferant'] == "" || is_null($data['datum_lagerlieferant'])){
          $data['datum_lagerlieferant'] = "00.00.0000";
        }else{
          $data['datum_lagerlieferant'] = date('d.m.Y',strtotime($data['datum_lagerlieferant']));
        }

        if($data['rahmenvertrag_von'] == '0000-00-00' || $data['rahmenvertrag_von'] == '' || is_null($data['rahmenvertrag_von'])){
          $data['rahmenvertrag_von'] = '00.00.0000';
        }else{
          $data['rahmenvertrag_von'] = date('d.m.Y',strtotime($data['rahmenvertrag_von']));
        }

        if($data['rahmenvertrag_bis'] == '0000-00-00' || $data['rahmenvertrag_bis'] == '' || is_null($data['rahmenvertrag_bis'])){
          $data['rahmenvertrag_bis'] = '00.00.0000';
        }else{
          $data['rahmenvertrag_bis'] = date('d.m.Y',strtotime($data['rahmenvertrag_bis']));
        }

        if($data['lieferzeit_standard_einheit'] == ''){
          $data['lieferzeit_standard_einheit'] = 'wochen';
        }
        if($data['lieferzeit_aktuell_einheit'] == ''){
          $data['lieferzeit_aktuell_einheit'] = 'wochen';
        }

      }else{
        //$data['artikel'] = 0;
        $data['adresse'] = 0;
        $data['preis'] = '';
        $data['waehrung'] = 'EUR';
        $data['ab_menge'] = '';
        $data['vpe'] = '';
        $data['preis_anfrage_vom'] = '';
        $data['gueltig_bis'] = '';
        $data['lieferzeit_standard'] = '';
        $data['lieferzeit_standard_einheit'] = 'wochen';
        $data['lieferzeit_aktuell'] = '';
        $data['lieferzeit_aktuell_einheit'] = 'wochen';
        $data['lager_lieferant'] = '';
        $data['datum_lagerlieferant'] = '';
        $data['bestellnummer'] = '';
        $data['bezeichnunglieferant'] = '';
        $data['sicherheitslager'] = '';
        $data['bemerkung'] = '';
        $data['standard'] = 0;
        $data['rahmenvertrag'] = 0;
        $data['rahmenvertrag_von'] = '';
        $data['rahmenvertrag_bis'] = '';
        $data['rahmenvertrag_menge'] = '';
        $data['nichtberechnet'] = 0; 
      }
      echo json_encode($data);
      $this->app->ExitXentral();
    }

    if($this->app->Secure->GetGET('cmd')==='popupsave'){
      $eid = $this->app->Secure->GetPOST('eid');
      $eartikelid = $this->app->Secure->GetPOST('eartikelid');
      $estandard = $this->app->Secure->GetPOST('estandard');
      $eadresse = $this->app->Secure->GetPOST('eadresse');
      $ebezeichnunglieferant = $this->app->Secure->GetPOST('ebezeichnunglieferant');
      $ebestellnummer = $this->app->Secure->GetPOST('ebestellnummer');
      $eab_menge = $this->app->Secure->GetPOST('eab_menge');
      $evpe = $this->app->Secure->GetPOST('evpe');
      $epreis = $this->app->Secure->GetPOST('epreis');
      $ewaehrung = $this->app->Secure->GetPOST('ewaehrung');
      $elivepreisvpe = $this->app->Secure->GetPOST('elivepreisvpe');
      $enichtberechnet = $this->app->Secure->GetPOST('enichtberechnet');
      $epreis_anfrage_vom = $this->app->Secure->GetPOST('epreis_anfrage_vom');
      $egueltig_bis = $this->app->Secure->GetPOST('egueltig_bis');
      $elager_lieferant = $this->app->Secure->GetPOST('elager_lieferant');
      $edatum_lagerlieferant = $this->app->Secure->GetPOST('edatum_lagerlieferant');
      $esicherheitslager = $this->app->Secure->GetPOST('esicherheitslager');
      $elieferzeit_standard = $this->app->Secure->GetPOST('elieferzeit_standard');
      $elieferzeit_standard_einheit = $this->app->Secure->GetPOST('elieferzeit_standard_einheit');
      $elieferzeit_aktuell = $this->app->Secure->GetPOST('elieferzeit_aktuell');
      $elieferzeit_aktuell_einheit = $this->app->Secure->GetPOST('elieferzeit_aktuell_einheit');
      $erahmenvertrag = $this->app->Secure->GetPOST('erahmenvertrag');
      $erahmenvertrag_menge = $this->app->Secure->GetPOST('erahmenvertrag_menge');
      $erahmenvertrag_von = $this->app->Secure->GetPOST('erahmenvertrag_von');
      $erahmenvertrag_bis = $this->app->Secure->GetPOST('erahmenvertrag_bis');
      $ebemerkung = $this->app->Secure->GetPOST('ebemerkung');

      $error = '';

      if($eab_menge != ''){
        $eab_menge = str_replace(',','.',$eab_menge);
      }else{
        $error .= 'Mengef'."\n";
      }

      $lieferantenname = $eadresse;
      if($eadresse != ''){
        $eadresse = explode(' ', $eadresse, 2);
        $eadresse = $eadresse[0];

        $eadresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer = '$eadresse' AND lieferantennummer != '' AND geloescht = 0 LIMIT 1");
        if($eadresse != ''){
        }else{
          $error .= 'Adressef'."\n";
        }
      }else{
        $error .= 'Adressef'."\n";
      }

      if($epreis != ""){
        $epreis = str_replace(',', '.', $epreis);
        /*if($epreis > 0){
        }else{
          $error .= 'Preisf'."\n";
        }*/
      }else{
        $error .= 'Preisf'."\n";
      }

      if($eartikelid > 0 && $eartikelid != ''){
        $eartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE id = '$eartikelid' LIMIT 1");
        if($eartikelid != ''){
        }else{
          $error .= 'Bitte gültigen Artikel wählen'."\n";
        }
      }else{
        $error .= 'Bitte gültigen Artikel wählen'."\n";
      }


      if($epreis_anfrage_vom != '' && $epreis_anfrage_vom != '0000-00-00' && $epreis_anfrage_vom != '00.00.0000'){
        $epreis_anfrage_vom = date('Y-m-d',strtotime($epreis_anfrage_vom)); 
      }else{
        $epreis_anfrage_vom = '0000-00-00';
      }
      if($egueltig_bis != '' && $egueltig_bis != '0000-00-00' && $egueltig_bis != '00.00.0000'){
        $egueltig_bis = date('Y-m-d',strtotime($egueltig_bis));
      }else{
        $egueltig_bis = '0000-00-00';
      }
      if($edatum_lagerlieferant != '' && $edatum_lagerlieferant != '0000-00-00' && $edatum_lagerlieferant != '00.00.0000'){
        $edatum_lagerlieferant = date('Y-m-d',strtotime($edatum_lagerlieferant));
      }else{
        $edatum_lagerlieferant = '0000-00-00';
      }
      if($erahmenvertrag_von != '' && $erahmenvertrag_von != '0000-00-00' && $erahmenvertrag_von != '00.00.0000'){
        $erahmenvertrag_von = date('Y-m-d',strtotime($erahmenvertrag_von));
      }else{
        $erahmenvertrag_von = '0000-000-00';
      }
      if($erahmenvertrag_bis != '' && $erahmenvertrag_bis != '0000-00-00' && $erahmenvertrag_bis != '00.00.0000'){
        $erahmenvertrag_bis = date('Y-m-d',strtotime($erahmenvertrag_bis));
      }else{
        $erahmenvertrag_bis = '0000-00-00';
      }

      if($ewaehrung == ''){
        $ewaehrung = $this->app->erp->GetStandardWaehrung();
      }

      if($elieferzeit_standard_einheit == ''){
        $elieferzeit_standard_einheit = 'wochen';
      }
      if($elieferzeit_aktuell_einheit == ''){
        $elieferzeit_aktuell_einheit = 'wochen';
      }


      if($error == ''){
        if($eid > 0){
          $this->app->DB->Update("UPDATE einkaufspreise SET adresse = '$eadresse', preis = '$epreis', waehrung = '$ewaehrung', 
                          ab_menge = '$eab_menge', vpe = '$evpe', preis_anfrage_vom = '$epreis_anfrage_vom', gueltig_bis = '$egueltig_bis', 
                          lieferzeit_standard = '$elieferzeit_standard', lieferzeit_standard_einheit = '$elieferzeit_standard_einheit',
                          lieferzeit_aktuell = '$elieferzeit_aktuell', lieferzeit_aktuell_einheit = '$elieferzeit_aktuell_einheit', 
                          lager_lieferant = '$elager_lieferant', datum_lagerlieferant = '$edatum_lagerlieferant', 
                          bestellnummer = '$ebestellnummer', bezeichnunglieferant = '$ebezeichnunglieferant', 
                          sicherheitslager = '$esicherheitslager', bemerkung = '$ebemerkung', logdatei = NOW(), standard = '$estandard', 
                          geloescht = 0, firma = 1, apichange = 0, rahmenvertrag = '$erahmenvertrag', rahmenvertrag_von = '$erahmenvertrag_von', 
                          rahmenvertrag_bis = '$erahmenvertrag_bis', rahmenvertrag_menge = '$erahmenvertrag_menge', 
                          nichtberechnet = '$enichtberechnet' 
                          WHERE id = '$eid'");
          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }
        $this->app->DB->Insert("INSERT INTO einkaufspreise (artikel, adresse, preis, waehrung, ab_menge, vpe, preis_anfrage_vom, 
                            gueltig_bis, lieferzeit_standard, lieferzeit_standard_einheit, lieferzeit_aktuell, lieferzeit_aktuell_einheit, 
                            lager_lieferant, datum_lagerlieferant, bestellnummer, bezeichnunglieferant, sicherheitslager, 
                            bemerkung, logdatei, standard, geloescht, firma, apichange, rahmenvertrag, rahmenvertrag_von, 
                            rahmenvertrag_bis, rahmenvertrag_menge, nichtberechnet) 
                            VALUES ('$eartikelid', '$eadresse', '$epreis', '$ewaehrung', '$eab_menge', '$evpe', '$epreis_anfrage_vom', 
                            '$egueltig_bis', '$elieferzeit_standard', '$elieferzeit_standard_einheit', '$elieferzeit_aktuell', 
                            '$elieferzeit_aktuell_einheit', '$elager_lieferant', '$edatum_lagerlieferant', '$ebestellnummer', 
                            '$ebezeichnunglieferant', '$esicherheitslager', '$ebemerkung', NOW(), '$estandard', 0, 1, 0, 
                            '$erahmenvertrag', '$erahmenvertrag_von', '$erahmenvertrag_bis', '$erahmenvertrag_menge', '$enichtberechnet')");
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
      echo json_encode(array('status'=>0,'statusText'=>$error));
      $this->app->ExitXentral();
    }

    $stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='$id' LIMIT 1");

    if($stueckliste < 1)
    {
      $nicht_vorhanden = '<br><div class="info">Nicht vorhanden, da es keine St&uuml;ckliste ist.</div>';
      $this->app->Tpl->Set('TAB5KALKULATION',$nicht_vorhanden);
      $this->app->Tpl->Set('TAB5',$nicht_vorhanden);
      $this->app->Tpl->Set('TABELLE',$nicht_vorhanden);
    }

    $check = $this->app->DB->Select("SELECT id FROM stueckliste WHERE stuecklistevonartikel='$id' AND art='bt' LIMIT 1");

    // neues arbeitspaket
    $widget = new WidgetEinkaufspreise($this->app,'TAB2');
    $widget->form->SpecialActionAfterExecute('none',
        "index.php?module=artikel&action=einkauf&id=$id");

    if($this->app->Secure->GetPOST('submit')!=''){
      $this->app->erp->EnableTab('tabs-2');
    }

    $widget->Create();

    if($this->app->Secure->GetPOST('objekt')!=''){
      $this->app->Tpl->Set('AKTIV_TAB2', 'selected');
    }
    else{
      $this->app->Tpl->Set('AKTIV_TAB1', 'selected');
    }


    if ($stueckliste=='1') {

      $preis_min_ek = $this->getCalcPriceByMinBasePrice($id);
      $preis_max_ek =$this->getCalcPriceByMaxBasePrice($id);
          
      $this->app->Tpl->Set('TAB5KALKULATION','<div class="info">Dies ist nur ein grober Richtpreis aus dem kleinsten und größten Einkaufspreis.</div>');

      $kalkulationstabelle = "<table class='mkTable'>";
      $kalkulationstabelle .= "<tr>";
      $kalkulationstabelle .= "<td><b>Artikel</b></td>";
      $kalkulationstabelle .= "<td><b>Nummer</b></td>";
      $kalkulationstabelle .= "<td><b>Menge</b></td>";
      $kalkulationstabelle .= "<td><b>Lieferant</b></td>";
      $kalkulationstabelle .= "<td><b>Bestellnummer</b></td>";
      $kalkulationstabelle .= "<td><b>Basierend auf EK (Min)</b></td>";
      $kalkulationstabelle .= "<td><b>Basierend auf EK (Max)</b></td>";
      $kalkulationstabelle .= "<td><b>Kalkulierter EK</b></td>";
      $kalkulationstabelle .= "</tr>";

      $artikelkalkulation = $this->app->DB->SelectArr("SELECT a.id as artikelid, a.name_de as artikelname, a.nummer, trim(s.menge)+0 as menge, 
          (SELECT l2.name FROM einkaufspreise e LEFT JOIN adresse l2 ON l2.id=e.adresse WHERE e.artikel=a.id AND (e.objekt='Standard' OR e.objekt='') AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1 ORDER by e.preis ASC LIMIT 1) as lieferant,
      (SELECT e.bestellnummer FROM einkaufspreise e WHERE e.artikel=a.id AND (e.objekt='Standard' OR e.objekt='') AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1 ORDER by e.preis ASC LIMIT 1) as bestellnummer,

      ".$this->app->erp->FormatPreis("if(a.stueckliste,
      
      (SELECT SUM(
                    (SELECT MIN(e.preis/ifnull(wt.kurs,if(e.waehrung = 'CHR',$kurschf,if(e.waehrung = 'USD',$kursusd,1)))) FROM einkaufspreise e 
                                                   LEFT JOIN (
              SELECT max(kurs) as kurs, waehrung_von, waehrung_nach FROM waehrung_umrechnung WHERE  (isnull(gueltig_bis) OR gueltig_bis >= now() OR gueltig_bis = '0000-00-00') AND (waehrung_von LIKE 'EUR' OR waehrung_nach LIKE 'EUR') GROUP BY waehrung_von,waehrung_nach
              ) wt ON (wt.waehrung_nach <> 'EUR' AND wt.waehrung_nach = e.waehrung) OR (wt.waehrung_von <> 'EUR' AND wt.waehrung_von = e.waehrung)
                    WHERE e.artikel=s2.artikel AND (e.objekt='Standard' OR e.objekt='')
                      AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1) * s2.menge)
                      FROM stueckliste s2
                      WHERE s2.stuecklistevonartikel=s.artikel),

      (SELECT MIN(e.preis/ifnull(wt.kurs,if(e.waehrung = 'CHR',$kurschf,if(e.waehrung = 'USD',$kursusd,1)))) FROM einkaufspreise e 
                                                     LEFT JOIN (
              SELECT max(kurs) as kurs, waehrung_von, waehrung_nach FROM waehrung_umrechnung WHERE  (isnull(gueltig_bis) OR gueltig_bis >= now() OR gueltig_bis = '0000-00-00') AND (waehrung_von LIKE 'EUR' OR waehrung_nach LIKE 'EUR') GROUP BY waehrung_von,waehrung_nach
              ) wt ON (wt.waehrung_nach <> 'EUR' AND wt.waehrung_nach = e.waehrung) OR (wt.waehrung_von <> 'EUR' AND wt.waehrung_von = e.waehrung)
                      WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt='') AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1 ))*s.menge",4)."
      
       as 'ekmin',
      
      
      ".$this->app->erp->FormatPreis("if(a.stueckliste,
      
      (SELECT SUM(
            (SELECT MAX(e.preis/ifnull(wt.kurs,if(e.waehrung = 'CHR',$kurschf,if(e.waehrung = 'USD',$kursusd,1)))) FROM einkaufspreise e 
            
                                           LEFT JOIN (
              SELECT max(kurs) as kurs, waehrung_von, waehrung_nach FROM waehrung_umrechnung WHERE  (isnull(gueltig_bis) OR gueltig_bis >= now() OR gueltig_bis = '0000-00-00') AND (waehrung_von LIKE 'EUR' OR waehrung_nach LIKE 'EUR') GROUP BY waehrung_von,waehrung_nach
              ) wt ON (wt.waehrung_nach <> 'EUR' AND wt.waehrung_nach = e.waehrung) OR (wt.waehrung_von <> 'EUR' AND wt.waehrung_von = e.waehrung)
            WHERE e.artikel=s2.artikel AND (e.objekt='Standard' OR e.objekt='')
              AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1) * s2.menge)
              FROM stueckliste s2
              WHERE s2.stuecklistevonartikel=s.artikel),(SELECT MAX(e.preis/ifnull(wt.kurs,if(e.waehrung = 'CHR',$kurschf,if(e.waehrung = 'USD',$kursusd,1)))) FROM einkaufspreise e 
                                             LEFT JOIN (
              SELECT max(kurs) as kurs, waehrung_von, waehrung_nach FROM waehrung_umrechnung WHERE  (isnull(gueltig_bis) OR gueltig_bis >= now() OR gueltig_bis = '0000-00-00') AND (waehrung_von LIKE 'EUR' OR waehrung_nach LIKE 'EUR') GROUP BY waehrung_von,waehrung_nach
              ) wt ON (wt.waehrung_nach <> 'EUR' AND wt.waehrung_nach = e.waehrung) OR (wt.waehrung_von <> 'EUR' AND wt.waehrung_von = e.waehrung)
              
              WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt='') AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1 ))*s.menge",4)."

        as 'ekmax'


          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel 
          LEFT JOIN adresse l ON l.id=a.adresse
          WHERE s.stuecklistevonartikel='$id' ORDER by s.sort");


      $calculatedPurchasePriceService = $this->app->Container->get('PurchasePriceService');

      $kalkulierterekgesamt = 0;

      foreach($artikelkalkulation as $artikelindex => $kalkulierterArtikel){
        $kalkulationstabelle .= "<tr>";
        $kalkulationstabelle .= "<td>".$kalkulierterArtikel['artikelname']."</td>";
        $kalkulationstabelle .= "<td>".$kalkulierterArtikel['nummer']."</td>";

        $runden = true;
        if($kalkulierterArtikel['menge'] != round($kalkulierterArtikel['menge']))$runden = false;

        if($runden) {
          $kalkulierterArtikel['menge'] = round($kalkulierterArtikel['menge']);
        }


        $kalkulationstabelle .= "<td>".$kalkulierterArtikel['menge']."</td>";
        $kalkulationstabelle .= "<td>".$kalkulierterArtikel['lieferant']."</td>";
        $kalkulationstabelle .= "<td>".$kalkulierterArtikel['bestellnummer']."</td>";
        $kalkulationstabelle .= "<td align='right'>".$kalkulierterArtikel['ekmin']."</td>";
        $kalkulationstabelle .= "<td align='right'>".$kalkulierterArtikel['ekmax']."</td>";
        $berechneterEK = 0;
        $berechneterEK += $calculatedPurchasePriceService->calculateCalculatedPurchasePrice($kalkulierterArtikel['artikelid'], $kalkulierterArtikel['menge']);
        $kalkulationstabelle .= "<td align='right'>".number_format($berechneterEK,4,',','.')."</td>";
        $kalkulierterekgesamt += $berechneterEK;
        $kalkulationstabelle .= "</tr>";
      }

      $kalkulationstabelle .= "<tr>";
      $kalkulationstabelle .= "<td></td>";
      $kalkulationstabelle .= "<td></td>";
      $kalkulationstabelle .= "<td></td>";
      $kalkulationstabelle .= "<td></td>";
      $kalkulationstabelle .= "<td></td>";
      $kalkulationstabelle .= "<td align='right'>&Sigma; ".number_format($preis_min_ek,4,',','.')."</td>";
      $kalkulationstabelle .= "<td align='right'>&Sigma; ".number_format($preis_max_ek,4,',','.')."</td>";
      $kalkulationstabelle .= "<td align='right'>&Sigma; ".number_format($kalkulierterekgesamt,4,',','.')."</td>";
      $kalkulationstabelle .= "</tr>";

      $kalkulationstabelle .= "</table>";

      $this->app->Tpl->Add('TAB5KALKULATION', $kalkulationstabelle);


//        $this->app->Tpl->Add('TAB5KALKULATION',"<div class=\"info\">St&uuml;cklisten Grundpreis bei Menge 1: <b>$preis EUR - $preis_max EUR (Anzahl Positionen $pos / Teile $teile)</b></div>");
      $this->app->Tpl->Parse('TAB1','rahmen70.tpl');

      $sql = "SELECT s.artikel, SUM(s.menge) FROM stueckliste s LEFT JOIN artikel a ON a.id=s.artikel WHERE s.stuecklistevonartikel='$id' GROUP by a.id";
      $array_artikel = $this->app->DB->SelectArr($sql);

      $array_mindestmengen = array();
      $runden = true;
      $carray_artikel = $array_artikel?count($array_artikel):0;
      for($i_stk=0;$i_stk < $carray_artikel;$i_stk++) {
        $artikel = $array_artikel[$i_stk]['artikel'];
        $menge = $array_artikel[$i_stk]['menge'];

        // gehe einkaufspreis fuer einkaufspreis durch
        $einkaufspreise = $this->app->DB->SelectArr("SELECT ab_menge FROM einkaufspreise WHERE artikel='$artikel' AND 
            (gueltig_bis='0000-00-00' OR gueltig_bis >= curdate()) AND geloescht!=1 AND ab_menge >='$menge' ORDER by ab_menge");

          // liste mit artikel wo e keinen 1er Preis gibt
          $check_ek_eins = $this->app->DB->Select("SELECT id FROM einkaufspreise WHERE artikel='$artikel' AND           
              (gueltig_bis='0000-00-00' OR gueltig_bis >= curdate()) AND geloescht!=1 AND ab_menge =1 LIMIT 1");

        if($check_ek_eins <=0) {
          $artikel_ohne_ek_eins[]=$artikel;
        }
        $ceinkaufspreise = $einkaufspreise?count($einkaufspreise):0;
        for($i_ek = 0;$i_ek < $ceinkaufspreise; $i_ek++) {
          if(!in_array($einkaufspreise[$i_ek]['ab_menge'],$array_mindestmengen)) {
            $array_mindestmengen[] = $einkaufspreise[$i_ek]['ab_menge'];
            if(round($einkaufspreise[$i_ek]['ab_menge']) != $einkaufspreise[$i_ek]['ab_menge']) {
              $runden = false;
            }
          }
        }
      }
      if($runden) {
        foreach($array_mindestmengen as $k => $v){
          $array_mindestmengen[$k] = round($v);
        }
      }
      sort($array_mindestmengen);
      $tpl = '';
      $tpl2 = '';
      $tpl3 = '';
      $tpl4 = '';
      $tpl5 = '';
      $tpl22 = '';
      $teile_tpl ='';
      $carray_mindestmengen = isset($array_mindestmengen)?count($array_mindestmengen):0;
      for($j_am=0;$j_am<$carray_mindestmengen;$j_am++) {
        $sql = "SELECT SUM( 
          (SELECT e.preis FROM einkaufspreise e WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt='') 
           AND e.ab_menge <= ".$array_mindestmengen[$j_am]." AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1 ORDER by preis ASC LIMIT 1)*s.menge)
           FROM stueckliste s
           LEFT JOIN artikel a ON a.id=s.artikel 
           WHERE s.stuecklistevonartikel='$id'";

        $preis = $this->app->DB->Select($sql);

        $sql = "SELECT s.artikel
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel 
          WHERE s.stuecklistevonartikel='$id' AND (SELECT e.preis FROM einkaufspreise e WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt='') 
              AND e.ab_menge <= ".$array_mindestmengen[$j_am]." AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1 ORDER by preis ASC LIMIT 1) IS NULL GROUP by s.artikel ORDER by s.artikel";

        $fehlende_preise = $this->app->DB->SelectArr($sql);

        $tpl .= "<td class=\"gentable\">".$array_mindestmengen[$j_am].'</td>';

        if(count($fehlende_preise) > 0) {
          $tpl3 .= "<td class=\"gentable;\"><font color=red>".count($fehlende_preise).'</font></td>';
          $tpl2 .= '<td class="gentable">-</td>';
        }
        else {
          $tpl3 .= '<td class="gentable">-</td>';
          $tpl2 .= '<td class="gentable">'.number_format($preis,2,',','').'</td>';
        }

        $fehlende_artikel_links='';
        $summe_fehlende_preise_max = 0;
        $summe_fehlende_preise_min = 0;

        $cfehlende_preise = $fehlende_preise?count($fehlende_preise):0;
        for($jj=0;$jj<$cfehlende_preise;$jj++) {
          $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='".$fehlende_preise[$jj]['artikel']."' LIMIT 1");

          $menge = $this->app->DB->Select("SELECT SUM(s.menge) FROM stueckliste s LEFT JOIN artikel a ON a.id=s.artikel                 
            WHERE s.stuecklistevonartikel='$id' AND s.artikel='".$fehlende_preise[$jj]['artikel']."'");

          //TODO VK
          $preis_min = $this->app->DB->Select("SELECT MIN(preis) FROM einkaufspreise WHERE artikel='".$fehlende_preise[$jj]['artikel']."' AND geloescht!=1 
              AND (gueltig_bis='0000-00-00' OR gueltig_bis >= curdate()) ")*$menge;

          //TODO VK
          $preis_max = $this->app->DB->Select("SELECT MAX(preis) FROM einkaufspreise WHERE artikel='".$fehlende_preise[$jj]['artikel']."' AND geloescht!=1 
              AND (gueltig_bis='0000-00-00' OR gueltig_bis >= curdate()) ")*$menge;

          $fehlende_artikel_links .="<a style=\"font-weight:normal;font-size:6pt;\" href=\"index.php?module=artikel&action=einkauf&id=".$fehlende_preise[$jj]['artikel']."\" 
            target=\"_blink\">$nummer</a><br>";
            //target=\"_blink\">$nummer/$preis_min/$preis_max</a><br>";

          $summe_fehlende_preise_max = $summe_fehlende_preise_max + $preis_max;
          $summe_fehlende_preise_min = $summe_fehlende_preise_min + $preis_min;
        }
        $teile_tpl .="<td nowrap>$fehlende_artikel_links</td>";

        if(count($fehlende_preise) <=0){
          $tpl22 .='<td>-</td>';
        } else {
          $tpl22 .='<td><s>'.number_format($summe_fehlende_preise_max+$preis,2,",","")."</s></td>";
        }
        //$tpl4 .='<td>'.number_format($summe_fehlende_preise_min,2,',','').'</td>';
        //$tpl5 .='<td>'.number_format($summe_fehlende_preise_max,2,',','').'</td>';
      }

      $this->app->Tpl->Add('TABELLE',"<tr style=\"background-color:#e0e0e0;display:;\"><td>Anzahl</td>".$tpl.'</tr>');

      $this->app->Tpl->Add('TABELLE','<tr><td>Gesamtpreis</td>'.$tpl2.'</tr>');

      $this->app->Tpl->Add('TABELLE',"<tr style=\"background-color:#e0e0e0;display:;\"><td nowrap>Anzahl fehlende Preise f&uuml;r Staffel&nbsp;</td>".$tpl3."</tr>");

      $this->app->Tpl->Add('TABELLE',"<tr><td>Zwischensumme (falls Preise fehlen)</td>".$tpl22."</tr>");

      if(!empty($artikel_ohne_ek_eins)) {
        $this->app->Tpl->Add('TABELLE',"<tr valign=\"top\"><td>Fehlende Preise f&uuml;r Preisstaffel bei Artikel</td>".$teile_tpl."</tr>");
        $this->app->Tpl->Set('TAB5',"
            <table width=\"100%\"><tr><td width=\"100\"><b>Artikel-Nr.</b></td><td><b>Name</b></td><td width=100><b>Aktion</b></td></tr>");
        sort($artikel_ohne_ek_eins);
        $cartikel_ohne_ek_eins = count($artikel_ohne_ek_eins);
        for($j_am=0;$j_am<$cartikel_ohne_ek_eins;$j_am++) {
          if($j_am % 2) {
            $color='#e0e0e0';
          } else {
            $color='#fff';
          }
          $artikel_arr = $this->app->DB->SelectArr("SELECT id,name_de, nummer FROM artikel WHERE id='".$artikel_ohne_ek_eins[$j_am]."' LIMIT 1");
          $this->app->Tpl->Add('TAB5',"<tr style=\"background-color:$color;\"><td>".$artikel_arr[0]['nummer']."</td><td>".$artikel_arr[0]['name_de']."</td><td><a href=\"index.php?module=artikel&action=einkauf&id=".$artikel_arr[0]['id']."\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\"></a></td></tr>");
        }
        $this->app->Tpl->Add('TAB5','</table>');
      }
      if(!$this->app->erp->ModulVorhanden('produktion'))
      {
        //$this->app->Tpl->Parse(TAB5,"rahmen70.tpl");
        $this->app->Tpl->Set('TABELLE',"<div class=\"info\">Die Preismatrix (Kosten Stueckzahl ab&auml;ngig von Einkaufspreisen ist nur mit dem Modul Produktion verf&uuml;gbar");
      }

      //$this->app->Tpl->Parse('PAGE',"einkaufspreiseuebersicht_stueckliste.tpl");
    }

    // easy table mit arbeitspaketen YUI als template
    $this->app->YUI->TableSearch('TAB7','einkaufspreise', 'show','','',basename(__FILE__), __CLASS__);

    $adresse = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id='$id' LIMIT 1");
    $hauptlieferant = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
    /*$min_preis = $this->app->DB->Select("SELECT FORMAT(MIN(preis),2) FROM verkaufspreise WHERE artikel='$id' AND (gueltig_bis='0000-00-00'
          OR gueltig_bis >= curdate()) AND (gueltig_ab='0000-00-00' OR gueltig_ab <= curdate()) AND geloescht!=1 LIMIT 1");
    $max_preis = $this->app->DB->Select("SELECT FORMAT(MAX(preis),2) FROM verkaufspreise WHERE artikel='$id' AND (gueltig_bis='0000-00-00' 
          OR gueltig_bis >= curdate()) AND (gueltig_ab='0000-00-00' OR gueltig_ab <= curdate()) AND geloescht!=1 LIMIT 1");*/

    $this->app->Tpl->Add('TAB1',"<div class=\"info\">Der Hauptlieferant ist <b>$hauptlieferant</b></div>");

    //$min_preis = $this->app->erp->EUR($min_preis/(($this->app->erp->GetStandardMarge()/100.0)+1.0)*1.0);
    //$max_preis = $this->app->erp->EUR($max_preis/(($this->app->erp->GetStandardMarge()/100.0)+1.0)*1.0);

    $this->app->erp->CheckArtikel($id);
    $this->app->Tpl->Parse('PAGE','einkaufspreiseuebersicht.tpl');
  }

  /**
   * @param int $articleId
   *
   * @return float
   */
  public function getCalcPriceByMinBasePrice($articleId)
  {
    $kursusd = (float)$this->app->erp->GetWaehrungUmrechnungskurs('EUR','USD');
    $kurschf = (float)$this->app->erp->GetWaehrungUmrechnungskurs('EUR','CHF');
    $sql = "
        SELECT 
          SUM(if(a.stueckliste,
            (SELECT SUM(  
            (SELECT MIN(e.preis/ifnull(wt.kurs,if(e.waehrung = 'CHR',$kurschf,if(e.waehrung = 'USD',$kursusd,1)))) FROM einkaufspreise e 
              LEFT JOIN (
              SELECT max(kurs) as kurs, waehrung_von, waehrung_nach FROM waehrung_umrechnung WHERE  (isnull(gueltig_bis) OR gueltig_bis >= now() OR gueltig_bis = '0000-00-00') AND (waehrung_von LIKE 'EUR' OR waehrung_nach LIKE 'EUR') GROUP BY waehrung_von,waehrung_nach
              ) wt ON (wt.waehrung_nach <> 'EUR' AND wt.waehrung_nach = e.waehrung) OR (wt.waehrung_von <> 'EUR' AND wt.waehrung_von = e.waehrung)
            WHERE e.artikel=s2.artikel AND (e.objekt='Standard' OR e.objekt='')
              AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1) * s2.menge)
              FROM stueckliste s2
              WHERE s2.stuecklistevonartikel=s.artikel),(SELECT MIN(e.preis/ifnull(wt.kurs,if(e.waehrung = 'CHR',$kurschf,if(e.waehrung = 'USD',$kursusd,1)))) FROM einkaufspreise e 
                                                           LEFT JOIN (
              SELECT max(kurs) as kurs, waehrung_von, waehrung_nach FROM waehrung_umrechnung WHERE  (isnull(gueltig_bis) OR gueltig_bis >= now() OR gueltig_bis = '0000-00-00') AND (waehrung_von LIKE 'EUR' OR waehrung_nach LIKE 'EUR') GROUP BY waehrung_von,waehrung_nach
              ) wt ON (wt.waehrung_nach <> 'EUR' AND wt.waehrung_nach = e.waehrung) OR (wt.waehrung_von <> 'EUR' AND wt.waehrung_von = e.waehrung)
              WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt='') AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1 ))*s.menge)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel
          LEFT JOIN adresse l ON l.id=a.adresse
          WHERE s.stuecklistevonartikel=$articleId";

    return (float)$this->app->DB->Select($sql);
  }

  /**
   * @param int $articleId
   *
   * @return float mixed
   */
  public function getCalcPriceByMaxBasePrice($articleId)
  {
    $kursusd = (float)$this->app->erp->GetWaehrungUmrechnungskurs('EUR','USD');
    $kurschf = (float)$this->app->erp->GetWaehrungUmrechnungskurs('EUR','CHF');
    $sql = "
        SELECT 
          SUM(if(a.stueckliste,
            (SELECT SUM(  
            (SELECT MAX(e.preis/ifnull(wt.kurs,if(e.waehrung = 'CHR',$kurschf,if(e.waehrung = 'USD',$kursusd,1)))) FROM einkaufspreise e 
                                                         LEFT JOIN (
              SELECT max(kurs) as kurs, waehrung_von, waehrung_nach FROM waehrung_umrechnung WHERE  (isnull(gueltig_bis) OR gueltig_bis >= now() OR gueltig_bis = '0000-00-00') AND (waehrung_von LIKE 'EUR' OR waehrung_nach LIKE 'EUR') GROUP BY waehrung_von,waehrung_nach
              ) wt ON (wt.waehrung_nach <> 'EUR' AND wt.waehrung_nach = e.waehrung) OR (wt.waehrung_von <> 'EUR' AND wt.waehrung_von = e.waehrung)
            WHERE e.artikel=s2.artikel AND (e.objekt='Standard' OR e.objekt='')
              AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1) * s2.menge)
              FROM stueckliste s2
              WHERE s2.stuecklistevonartikel=s.artikel),(SELECT MAX(e.preis/ifnull(wt.kurs,if(e.waehrung = 'CHR',$kurschf,if(e.waehrung = 'USD',$kursusd,1)))) FROM einkaufspreise e 
                                                           LEFT JOIN (
              SELECT max(kurs) as kurs, waehrung_von, waehrung_nach FROM waehrung_umrechnung WHERE  (isnull(gueltig_bis) OR gueltig_bis >= now() OR gueltig_bis = '0000-00-00') AND (waehrung_von LIKE 'EUR' OR waehrung_nach LIKE 'EUR') GROUP BY waehrung_von,waehrung_nach
              ) wt ON (wt.waehrung_nach <> 'EUR' AND wt.waehrung_nach = e.waehrung) OR (wt.waehrung_von <> 'EUR' AND wt.waehrung_von = e.waehrung)
              WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt='') AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis >= curdate()) AND e.geloescht!=1 ))*s.menge)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel
          LEFT JOIN adresse l ON l.id=a.adresse
          WHERE s.stuecklistevonartikel=$articleId";

    return (float)$this->app->DB->Select($sql);
  }

  public function ArtikelEinkaufEditPopup()
  {
    if($this->app->Secure->GetPOST('newpreis'))
    {
      $id = $this->app->Secure->GetGET('id');
      $this->ArtikelCopyWaehrungen($id, 'einkaufeditpopup');
    }
    //$frame = $this->app->Secure->GetGET('frame');
    $id = $this->app->Secure->GetGET('id');
    $this->app->Tpl->Set('OPENDISABLE','<!--');
    $this->app->Tpl->Set('CLOSEDISABLE','-->');
    $this->Preisrechner();


    $sid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
    $this->ArtikelMenu($sid);
    $artikel = $this->app->DB->Select("SELECT CONCAT(name_de,' (',nummer,')') FROM artikel WHERE id='$sid' LIMIT 1");
    $this->app->Tpl->Set('UEBERSCHRIFT','Artikel: '.$artikel);
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Einkauf)');

    $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=einkauf&id=$sid';\">");

    $widget = new WidgetEinkaufspreise($this->app,'TAB7');
    $widget->form->SpecialActionAfterExecute('close_refresh',
        "index.php?module=artikel&action=einkauf&id=$sid#tabs-1");
    $widget->Edit();


    $this->app->Tpl->Add('TAB2','Sie bearbeiten gerade einen Einkaufspreis. Erst nach dem Speichern k&ouml;nnen neue Preise angelegt werden.');
    $this->app->Tpl->Add('TAB3','Sie bearbeiten gerade einen Einkaufspreis. Erst nach dem Speichern k&ouml;nnen Statistiken betrachtet werden.');
    $this->app->Tpl->Parse('PAGE','einkaufspreiseuebersicht.tpl');
  }

  public function ArtikelEinkaufDisable()
  {
    //   $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET('id');
    $sid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
    $tagespreise = $this->app->DB->Select("SELECT tagespreise FROM artikel WHERE id = '$sid' LIMIT 1");
    if(!$tagespreise || $this->app->DB->Select("SELECT id FROM einkaufspreise WHERE id='$id' AND (gueltig_bis = '0000-00-00' || isnull(gueltig_bis) || gueltig_bis >= date(now()) LIMIT 1")){
      $this->app->DB->Update("UPDATE einkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY), logdatei = now() WHERE id='$id' LIMIT 1");
    }
    $this->app->erp->ObjektProtokoll('einkaufspreise', $id, 'einkaufspreise_edit', 'Einkaufspreis deaktiviert');
    $this->app->Location->execute('index.php?module=artikel&action=einkauf&id='.$sid);
  }

  public function ArtikelEinkaufDelete()
  {
    //    $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET('id');
    $sid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
    $tagespreise = $this->app->DB->Select("SELECT tagespreise FROM artikel WHERE id = '$sid' LIMIT 1");
    if(!$tagespreise || $this->app->DB->Select("SELECT id FROM einkaufspreise WHERE id='$id' AND (gueltig_bis = '0000-00-00' || isnull(gueltig_bis) || gueltig_bis >= date(now()) LIMIT 1")){
      $this->app->DB->Update("UPDATE einkaufspreise SET geloescht='1',gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY), logdatei = now() WHERE id='$id' LIMIT 1");
    }
    $this->app->erp->ObjektProtokoll('einkaufspreise', $id, 'einkaufspreise_edit', 'Einkaufspreis gel&ouml;scht');
    $this->app->Location->execute('index.php?module=artikel&action=einkauf&id='.$sid);
  }



  public function ArtikelEigenschaftenCopy()
  {
    $id = $this->app->Secure->GetGET('id');

    $id = $this->app->DB->MysqlCopyRow('eigenschaften','id',$id);

    $this->app->Location->execute('index.php?module=artikel&action=eigenschafteneditpopup&id='.$id);
  }


  public function ArtikelEinkaufCopy()
  {
    $id = $this->app->Secure->GetGET('id');
    $id = $this->app->DB->MysqlCopyRow('einkaufspreise','id',$id);
    $this->app->DB->Update("UPDATE einkaufspreise SET geloescht='0', gueltig_bis='0000-00-00', logdatei = now() WHERE id='$id' LIMIT 1");
    $this->app->erp->ObjektProtokoll('einkaufspreise', $id, 'einkaufspreise_create', 'Einkaufspreis kopiert');
    //$this->app->DB->Update("UPDATE einkaufspreise SET geloescht='1' WHERE id='$id' LIMIT 1");
    $sid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
    $this->app->Location->execute("index.php?module=artikel&action=einkauf&id=".$sid);
  }


  public function ArtikelCopyEdit()
  {
    //$id = (int)$this->app->Secure->GetGET('id');
    if($this->app->Secure->GetGET('cmd')==='get'){
      $id = (int)$this->app->Secure->GetPOST('id');
      $data = $this->app->DB->SelectRow("SELECT id FROM artikel WHERE id = \"$id\" LIMIT 1 ");
      echo json_encode($data);
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Parse('PAGE', 'artikeluebersicht.tpl');
  }

  public function ArtikelCopySave(){
    $id = (int)$this->app->Secure->GetPOST('editid');
    $einkaufspreise = $this->app->Secure->GetPOST('editeinkaufspreise');
    $verkaufspreise = $this->app->Secure->GetPOST('editverkaufspreise');
    $dateien = $this->app->Secure->GetPOST('editdateien');
    $eigenschaften = $this->app->Secure->GetPOST('editeigenschaften');
    $anweisungen = $this->app->Secure->GetPOST('editanweisungen');
    $stuecklisten = $this->app->Secure->GetPOST('editstuecklisten');
    $freifelderuebersetzung = $this->app->Secure->GetPOST('editfreifelderuebersetzung');

    if($id == '' || $id <= 0){
      $id = 0;
    }
    if($einkaufspreise == '' || $einkaufspreise <= 0){
      $einkaufspreise = 0;
    }
    if($verkaufspreise == '' || $einkaufspreise <= 0){
      $verkaufspreise = 0;
    }
    if($dateien == '' || $dateien <= 0){
      $dateien = 0;
    }
    if($eigenschaften == '' || $eigenschaften <= 0){
      $eigenschaften = 0;
    }
    if($anweisungen == '' || $anweisungen <= 0){
      $anweisungen = 0;
    }
    if($stuecklisten == '' || $stuecklisten <= 0){
      $stuecklisten = 0;
    }
    if($freifelderuebersetzung == '' || $freifelderuebersetzung <= 0){
      $freifelderuebersetzung = 0;
    }

    $data = array($id, $einkaufspreise, $verkaufspreise, $dateien, $eigenschaften, $anweisungen, $stuecklisten, $freifelderuebersetzung);    
   
    $idnew = $this->ArtikelCopy($data, true);
    echo json_encode(array('status'=>1,'url'=>'index.php?module=artikel&action=edit&id='.$idnew));
    $this->app->ExitXentral();
  }

  function ArtikelCopy($data = null, $return = false)
  {
    //$id = $this->app->Secure->GetGET("id");
    $id = $data[0];
    $einkaufspreise = $data[1];
    $verkaufspreise = $data[2];
    $dateien = $data[3];
    $eigenschaften = $data[4];
    $anweisungen = $data[5];
    $stuecklisten = $data[6];
    $freifelderuebersetzung = $data[7];

    $this->app->DB->MysqlCopyRow('artikel','id',$id);

    $idnew = $this->app->DB->GetInsertID();

    $steuersatz = $this->app->DB->Select("SELECT steuersatz FROM artikel WHERE id = '$id' LIMIT 1");
    if($steuersatz == ''){
      $steuersatz = -1.00;
      $this->app->DB->Update("UPDATE artikel SET steuersatz = '$steuersatz' WHERE id = '$idnew' LIMIT 1");
    }

    $this->app->DB->Update("UPDATE artikel SET nummer='' WHERE id='$idnew' LIMIT 1");
    if($this->app->DB->Select("SELECT variante_kopie FROM artikel WHERE id = '$id' LIMIT 1"))$this->app->DB->Update("UPDATE artikel SET variante = 1, variante_von = '$id' WHERE id = '$idnew' LIMIT 1");

    if($stuecklisten == 1){
      // wenn stueckliste
      $stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='$id' LIMIT 1");
      if($stueckliste==1)
      {
        $artikelarr = $this->app->DB->SelectArr("SELECT * FROM stueckliste WHERE stuecklistevonartikel='$id'");
        $cartikelarr = $artikelarr?count($artikelarr):0;
        for($i=0;$i<$cartikelarr;$i++)
        {
          $sort = $artikelarr[$i]['sort'];        
          $artikel = $artikelarr[$i]['artikel'];  
          $referenz = $artikelarr[$i]['referenz'];        
          $place = $artikelarr[$i]['place'];      
          $layer = $artikelarr[$i]['layer'];        
          $stuecklistevonartikel = $idnew;        
          $menge = $artikelarr[$i]['menge'];
          $firma = $artikelarr[$i]['firma'];

          $this->app->DB->Insert("INSERT INTO stueckliste (id,sort,artikel,referenz,place,layer,stuecklistevonartikel,menge,firma) VALUES
            ('','$sort','$artikel','$referenz','$place','$layer','$stuecklistevonartikel','$menge','$firma')"); 
        }
      }
    }

    /*$arbeitsanweisungen = $this->app->DB->SelectArr("SELECT id FROM `artikel_arbeitsanweisung` WHERE artikel = '$id'");
    if($arbeitsanweisungen)
    {
      foreach($arbeitsanweisungen as $arbeitsanweisung)
      {
        $newarbeitsanweisung = $this->app->DB->MysqlCopyRow('artikel_arbeitsanweisung', 'id', $arbeitsanweisung['id']);
        $this->app->DB->Update("UPDATE `artikel_arbeitsanweisung` SET artikel = '$idnew' WHERE id = '$newarbeitsanweisung' LIMIT 1");
      }
    }*/
    //TODO hinweis es wuren keine Preise kopiert

    if($einkaufspreise == 1){
      $einkaufspreise = $this->app->DB->SelectArr("SELECT id FROM einkaufspreise WHERE artikel = '$id'");
      if($einkaufspreise){
        foreach($einkaufspreise as $preis){
          $neuereinkaufspreis = $this->app->DB->MysqlCopyRow("einkaufspreise", "id", $preis['id']);
          $this->app->DB->Update("UPDATE einkaufspreise SET artikel = '$idnew' WHERE id = '$neuereinkaufspreis' LIMIT 1");
        }
      }
    }

    if($verkaufspreise == 1){
      $verkaufspreise = $this->app->DB->SelectArr("SELECT id FROM verkaufspreise WHERE artikel = '$id'");
      if($verkaufspreise){
        foreach($verkaufspreise as $preis){
          $neuerverkaufspreis = $this->app->DB->MysqlCopyRow("verkaufspreise", "id", $preis['id']);
          $this->app->DB->Update("UPDATE verkaufspreise SET artikel = '$idnew' WHERE id = '$neuerverkaufspreis' LIMIT 1");
        }
      }
    }
      
    if($dateien == 1){
      $dateien = $this->app->DB->SelectArr("SELECT DISTINCT datei FROM datei_stichwoerter WHERE parameter = '$id' AND objekt = 'Artikel'");
      $datei_stichwoerter = $this->app->DB->SelectArr("SELECT id,datei FROM datei_stichwoerter WHERE parameter = '$id' AND objekt = 'Artikel'");

      if($dateien){
        foreach($dateien as $datei){
          $titel = $this->app->DB->Select("SELECT titel FROM datei WHERE id='".$datei['datei']."' LIMIT 1");
          $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM datei WHERE id='".$datei['datei']."' LIMIT 1");
          $nummer = $this->app->DB->Select("SELECT nummer FROM datei WHERE id='".$datei['datei']."' LIMIT 1");
          $name = $this->app->DB->Select("SELECT dateiname FROM datei_version WHERE datei='".$this->app->DB->real_escape_string($datei['datei'])."' ORDER by version DESC LIMIT 1");
          $ersteller = $this->app->User->GetName();
          $tmpnewdateiid = $this->app->erp->CreateDatei($name,$titel,$beschreibung,$nummer,$this->app->erp->GetDateiPfad($datei['datei']),$ersteller);
          $datei_mapping[$datei['datei']] = $tmpnewdateiid; 
        }
      }

      if($datei_stichwoerter){
        foreach($datei_stichwoerter as $datei){
          $neuesstichwort = $this->app->DB->MysqlCopyRow("datei_stichwoerter", "id", $datei['id']);
          $newdatei = $datei_mapping[$datei['datei']];
          $this->app->DB->Update("UPDATE datei_stichwoerter SET datei='$newdatei', parameter = '$idnew', objekt = 'Artikel' WHERE id = '$neuesstichwort' LIMIT 1");
        }
      }
    }

    if($eigenschaften == 1){
      $aeigenschaften = $this->app->DB->SelectArr("SELECT id FROM artikeleigenschaftenwerte WHERE artikel = '$id'");
      if($aeigenschaften){
        foreach($aeigenschaften as $eigenschaft){
          $neue_eigenschaft = $this->app->DB->MysqlCopyRow("artikeleigenschaftenwerte", "id", $eigenschaft['id']);
          $this->app->DB->Update("UPDATE artikeleigenschaftenwerte SET artikel = '$idnew' WHERE id = '$neue_eigenschaft' LIMIT 1");
        }
      }
    }

    //WERDEN AUCH SCHON IMMER KOPIERT
    if($anweisungen == 1){
      $arbeitsanweisungen = $this->app->DB->SelectArr("SELECT id FROM artikel_arbeitsanweisung WHERE artikel = '$id'");
      if($arbeitsanweisungen){
        foreach($arbeitsanweisungen as $anweisung){
          $neue_anweisung = $this->app->DB->MysqlCopyRow("artikel_arbeitsanweisung", "id", $anweisung['id']);
          $this->app->DB->Update("UPDATE artikel_arbeitsanweisung SET artikel = '$idnew' WHERE id = '$neue_anweisung' LIMIT 1");
        }
      }
    }

    if($freifelderuebersetzung == 1){
      $freifelderuebersetzungen = $this->app->DB->SelectArr("SELECT id FROM artikel_freifelder WHERE artikel = '$id'");
      if($freifelderuebersetzungen){
        $this->app->DB->Insert("INSERT INTO artikel_freifelder (artikel, sprache, nummer, wert) SELECT '$idnew', sprache, nummer, wert FROM artikel_freifelder WHERE artikel = '$id'");
      }
    }

    // artikelbilder kopieren

    if($return){
      return $idnew;
    }

    // eventuell einkaufspreise verkaufspreise und stueckliste kopieren?
    $msg = $this->app->erp->base64_url_encode("<div class=error>Sie befinden sich in der neuen Kopie des Artikels. Bitte legen Sie Verkaufs- und Einkaufspreise und Bilder bzw. Dateien an! Diese wurden nicht kopiert!</div>"); 
    $this->app->Location->execute("index.php?module=artikel&action=edit&msg=$msg&id=".$idnew);
  }

  public function ArtikelProjekte()
  {
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Projekte)');
    $this->ArtikelMenu();
    $this->app->Tpl->Set('PAGE','hier sieht man in welchen projekten es verwendet wird');
  }

  public function ArtikelLager()
  {
    $id = $this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'getvpe')
    {
      $lpiid = (int)$this->app->Secure->GetPOST('lpiid');
      $erg = $this->app->DB->SelectRow("SELECT lpv.* FROM lager_platz_inhalt lpi LEFT JOIN lager_platz_vpe lpv ON lpi.lager_platz_vpe = lpv.id WHERE lpi.id = '".$lpiid."' LIMIT 1");
      echo json_encode($erg);
      $this->app->ExitXentral();
    }
    
    if($cmd === 'getvpevorlage')
    {
      $vpeid = (int)$this->app->Secure->GetPOST('vpeid');
      $erg = $this->app->DB->SelectRow("SELECT lpv.* FROM lager_platz_vpe lpv WHERE lpv.id = '".$vpeid."' LIMIT 1");
      echo json_encode($erg);
      $this->app->ExitXentral();
    }
    
    if($cmd === 'savevpe')
    {
      $lpiid = (int)$this->app->Secure->GetPOST('lpiid');
      $artikel = $this->app->DB->Select("SELECT artikel FROM lager_platz_inhalt WHERE id = '".$lpiid."' LIMIT 1");
      $status = 0;
      if($artikel)
      {
        $status = 1;
        $menge = str_replace(',','.',$this->app->Secure->GetPOST('menge'));
        $menge2 = str_replace(',','.',$this->app->Secure->GetPOST('menge2'));
        $gewicht = str_replace(',','.',$this->app->Secure->GetPOST('gewicht'));
        $breite = str_replace(',','.',$this->app->Secure->GetPOST('breite'));
        $laenge = str_replace(',','.',$this->app->Secure->GetPOST('laenge'));
        $hoehe = str_replace(',','.',$this->app->Secure->GetPOST('hoehe'));
        $gewicht2 = str_replace(',','.',$this->app->Secure->GetPOST('gewicht2'));
        $breite2 = str_replace(',','.',$this->app->Secure->GetPOST('breite2'));
        $laenge2 = str_replace(',','.',$this->app->Secure->GetPOST('laenge2'));
        $hoehe2 = str_replace(',','.',$this->app->Secure->GetPOST('hoehe2'));
        $check = str_replace(',','.',$this->app->DB->Select("SELECT id FROM lager_platz_vpe WHERE artikel = '$artikel' AND
        menge = '$menge' AND menge2 = '$menge2' 
        AND gewicht = '$gewicht' AND gewicht2 = '$gewicht2'
        AND breite = '$breite' AND breite2 = '$breite2'
        AND laenge = '$laenge' AND laenge2 = '$laenge2'
        AND hoehe = '$hoehe' AND hoehe2 = '$hoehe2'
        LIMIT 1
        "));
        if(!$check && ($menge > 0 || $gewicht > 0 || $breite > 0 || $laenge > 0 || $hoehe > 0))
        {
          $this->app->DB->Insert("INSERT INTO lager_platz_vpe (artikel, menge, menge2, gewicht, gewicht2, breite, breite2, laenge, laenge2, hoehe, hoehe2)
          VALUES ('$artikel','$menge','$menge2','$gewicht','$gewicht2','$breite','$breite2','$laenge','$laenge2','$hoehe','$hoehe2')
          ");
          $check = $this->app->DB->GetInsertID();
        }
        $menge = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE id = '$lpiid' LIMIT 1");
        $lager_platz = $this->app->DB->Select("SELECT lager_platz FROM lager_platz_inhalt WHERE id = '$lpiid' LIMIT 1");
        $lager_platz_vpe = $this->app->DB->Select("SELECT lager_platz_vpe FROM lager_platz_inhalt WHERE id = '".$lpiid."' LIMIT 1");
        if(!$lager_platz_vpe)
        {
          $lagerbewegung = $this->app->DB->SelectArr("SELECT * FROM lager_bewegung WHERE lager_platz = '$lager_platz' AND artikel = '$artikel' AND menge = '$menge' AND eingang = 1 ORDER BY zeit DESC, id DESC LIMIT 1");
          if($lagerbewegung)
          {
            if(!$lagerbewegung[0]['vpeid'])
            {
              $this->app->DB->Update("UPDATE lager_bewegung SET vpeid = '$check' WHERE id = '".$lagerbewegung[0]['id']."' LIMIT 1");
            }
            if(stripos($lagerbewegung[0]['referenz'],'(VPE') === false)
            {
              $referenz = $this->app->DB->real_escape_string($lagerbewegung[0]['referenz'].$this->app->erp->GetVPEBezeichnung($check));
              $this->app->DB->Update("UPDATE lager_bewegung SET referenz = '$referenz' WHERE id = '".$lagerbewegung[0]['id']."' LIMIT 1");
            }
          }
        }
        $this->app->DB->Update("UPDATE lager_platz_inhalt SET lager_platz_vpe = '$check' WHERE id = '$lpiid' LIMIT 1");
      }
      echo json_encode(array('status'=>$status));
      $this->app->ExitXentral();
    }
    
    $msg = $this->app->Secure->GetGET('msg');

    if(!is_numeric($id))
    {
      $tmp = explode('_',$id);
      $id = $tmp[0];
      if(is_numeric($id))
      {
        $this->app->Location->execute("index.php?module=artikel&action=lager&id=$id");
        return;
      }
    }
    $this->app->erp->LagerArtikelZusammenfassen($id);

    if(method_exists($this->app->erp,'GetTmpMessageOut'))
    {
      $this->app->Tpl->Set('MESSAGE',$this->app->erp->GetTmpMessageOut());
    }else{
      $msg = $this->app->erp->base64_url_decode($msg);
      $this->app->Tpl->Set('MESSAGE',$msg);
    }
    if($this->app->DB->Select("SELECT id FROM lager_platz_inhalt WHERE artikel = '$id' AND lager_platz <> 0 AND NOT isnull(inventur)  LIMIT 1"))
    {
      $this->app->Tpl->Add('MESSAGE','<div class="warning">F&uuml;r diesen Artikel l&auml;uft aktuell eine Inventur</div>');
    }
    $check = $this->app->DB->Select("SELECT trim(SUM(menge))+0 FROM zwischenlager WHERE artikel='$id'");
    if($check > 0)
    { 
      $this->app->Tpl->Add('MESSAGE','<div class="warning">Hinweis: Es befinden sich noch '.$check.' St&uuml;ck des Artikels im Zwischenlager!</div>');
    }

    $this->ArtikelMenu();
    $this->app->Tpl->Add('TAB1','<h2>Lagerbestand</h2>');

    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $bestbeforeBatchSn = $this->app->DB->SelectRow(
      sprintf(
      'SELECT mindesthaltbarkeitsdatum, seriennummern, chargenverwaltung 
        FROM artikel 
        WHERE id=%d 
        LIMIT 1',
        $id
      )
    );
    $mindesthaltbarkeitsdatum = $bestbeforeBatchSn['mindesthaltbarkeitsdatum'];
    $seriennummern = $bestbeforeBatchSn['seriennummern'];
    $chargenverwaltung= $bestbeforeBatchSn['chargenverwaltung'];

    $artikelvpe = 'lpi.vpe';
    $artikeljoin = '';
    

    if($seriennummern!=='vomprodukteinlagern' && $chargenverwaltung <1 && $mindesthaltbarkeitsdatum!='1')
    {
      $table->Query("SELECT CONCAT(l.bezeichnung,' / ',lp.kurzbezeichnung, if(lp.sperrlager,' (Kein Auto-Versand Lager)',''),
        if(lp.poslager,' (POS Lager)',''),if(lp.verbrauchslager,' (Verbrauchslager)',''),if(lp.autolagersperre,' (Nachschublager)',''),if(isnull(lpi.inventur),'',concat(' <i style=\"color:red;\">(Inventurwert: ',(trim(lpi.inventur)+0),')</i>'))) as lager , trim(lpi.menge)+0 as menge, 
        $artikelvpe as VPE,
        p.abkuerzung as projekt, 
          lpi.id FROM lager_platz_inhalt lpi 
          LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id 
          LEFT JOIN projekt p ON lpi.projekt=p.id  
          LEFT JOIN lager l ON l.id=lp.lager 
          $artikeljoin
          WHERE lpi.artikel='$id' ");

      $submenu='';
      if($this->app->erp->RechteVorhanden('artikel','auslagern')){
        $submenu .= "<a onclick=\"var menge =  prompt('St&uuml;ckzahl der Artikel die aus diesem Regal genommen werden sollen:',1); var grund =  prompt('Auslagerungsgrund:','Korrektur'); if(parseFloat(menge.replace(',','.')) > 0 && (grund!=null && grund!='')) { window.location.href='index.php?module=artikel&action=auslagern&id=$id&lid=%value%&menge='+menge+'&grund='+grund;}\" href=\"#\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a>";
      }

      if($this->app->erp->RechteVorhanden('artikel','einlagern')){
        $submenu .= "<a onclick=\"var menge =  prompt('St&uuml;ckzahl der Artikel in dieses Regal legen:',1); var grund =  prompt('Einlagerungsgrund:','Anpassung im Artikel'); if(parseFloat(menge.replace(',','.')) > 0 && (grund!=null && grund!='')) { window.location.href='index.php?module=artikel&action=einlagern&id=$id&lid=%value%&menge='+menge+'&grund='+grund;}\" href=\"#\"><img src=\"./themes/[THEME]/images/einlagern.png\" border=\"0\"></a>";
      }


      if($this->app->erp->RechteVorhanden('artikel','umlagern')){
        $submenu .="<a onclick=\"var menge =  prompt('St&uuml;ckzahl der Artikel in dieses Regal umlagern:',%field1%); var grund =  prompt('Grund:','Anpassung im Artikel'); if(parseFloat(menge.replace(',','.')) > 0 && (grund!=null && grund!='')) { window.location.href='index.php?module=artikel&action=umlagern&id=$id&lid=%value%&menge='+menge+'&grund='+grund;}\" href=\"#\"><img src=\"./themes/[THEME]/images/forward.svg\" border=\"0\"></a>";
      }

      if($this->app->erp->RechteVorhanden('artikel','auslagern') || $this->app->erp->RechteVorhanden('artikel','einlagern') 
          || $this->app->erp->RechteVorhanden('artikel','umlagern'))
      {
        $table->DisplayNew('INHALT',$submenu);
      }
      else {
        $table->DisplayNew('INHALT','');
      }
    } else {
      $table->Query("SELECT CONCAT(l.bezeichnung,' / ',lp.kurzbezeichnung, if(lp.sperrlager,' (Kein Auto-Versand Lager)',''),
        if(lp.poslager,' (POS Lager)',''),if(lp.verbrauchslager,' (Verbrauchslager)',''),if(lp.autolagersperre,' (Nachschublager)','')) as lager , trim(lpi.menge)+0 as menge, 
        $artikelvpe as VPE,
        p.abkuerzung as projekt
          FROM lager_platz_inhalt lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  
          LEFT JOIN lager l ON l.id=lp.lager 
          $artikeljoin
          WHERE lpi.artikel='$id'");

      if($this->app->erp->RechteVorhanden('artikel','auslagern') || $this->app->erp->RechteVorhanden('artikel','einlagern') 
          || $this->app->erp->RechteVorhanden('artikel','umlagern')){
        $table->DisplayNew('INHALT', 'Projekt', 'noAction');
      }
      else{
        $table->DisplayNew('INHALT', '');
      }
    }

    $this->app->Tpl->Parse('TAB1','rahmen70.tpl');

    $this->app->Tpl->Set('INHALT','');

    $mindesthaltbarkeitsdatum = $this->app->DB->Select("SELECT mindesthaltbarkeitsdatum FROM artikel WHERE id='$id' LIMIT 1");  
    $chargenverwaltung = $this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id='$id' LIMIT 1");        
    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='$id' LIMIT 1");        

    if($seriennummern==='vomprodukteinlagern' || $mindesthaltbarkeitsdatum=='1' || $chargenverwaltung=='2')
    {
      $this->app->Tpl->Add('TAB1',"<center>
          <input type=\"button\" value=\"Einlagern\" onclick=\"window.location.href='index.php?module=lager&action=bucheneinlagern&artikelid=$id&back=artikel'\">
          <input type=\"button\" value=\"Auslagern\" onclick=\"window.location.href='index.php?module=lager&action=buchenauslagern&artikelid=$id&back=artikel'\">
          <input type=\"button\" value=\"Umlagern\" onclick=\"window.location.href='index.php?module=lager&action=buchenauslagern&cmd=umlagern&artikelid=$id&back=artikel'\">
          </center>");
    } else {
      $this->app->Tpl->Add('TAB1',"<center><input type=\"button\" value=\"Artikel in neuen Lagerplatz einlagern\" onclick=\"window.location.href='index.php?module=lager&action=bucheneinlagern&artikelid=$id&back=artikel'\"></center>");
    }
    //    $this->app->Tpl->Set('SUBSUBHEADING',"Reservierungen Stand ".date('d.m.Y'));
    $this->app->Tpl->Add('TAB1',$this->ArtikelLagerInfo($id));

    if($this->app->erp->Version()!=='stock')
    {
      $this->app->Tpl->Add('TAB1','<h2>Reservierungen</h2>');

      // easy table mit arbeitspaketen YUI als template 
      $table = new EasyTable($this->app);
      $table->Query("SELECT adr.name as kunde, trim(r.menge)+0 as menge, if(r.datum='0000-00-00','Kein Datum hinterlegt',r.datum) as bis,
          p.abkuerzung as projekt,r.grund, IF(".$this->app->erp->RechteVorhanden("artikel","ausreservieren")."=1,CONCAT('<a onclick=\"var menge = prompt(\'Anzahl Artikel aus Reservierung entfernen:\',',(trim(r.menge)+0),'); if(parseFloat(menge.replace(\',\',\'.\')) > 0) window.location.href=\'index.php?module=artikel&action=ausreservieren&id=$id&lid=',r.id,'&menge=\'+menge;\" href=\"#\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>'),'') AS Aktion FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
          p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id WHERE  r.artikel='$id'");
      //$summe = round($this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$id'"),4);
      //$reserviert = round($this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$id'"),4);// AND datum >= NOW()");
      //    if($this->app->User->GetType()=="admin")


      $table->DisplayNew('INHALT');

      $this->app->Tpl->Parse('TAB1','rahmen70.tpl');

      $this->app->Tpl->Set('INHALT','');

      $this->app->Tpl->Add('TAB1','<h2>Offene Auftr&auml;ge</h2>');
      // easy table mit arbeitspaketen YUI als template 
      $table = new EasyTable($this->app);
      $table->Query("SELECT 
          CONCAT('<a href=\"index.php?module=auftrag&action=edit&id=',a.id,'\">',a.belegnr,'</a>') as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, 
          DATE_FORMAT(a.tatsaechlicheslieferdatum,'%d.%m.%Y') as 'Auslieferung Lager',
          trim(SUM(ap.menge))+0 as menge,
          CONCAT(
          (SELECT trim(SUM(li.menge))+0 FROM lager_reserviert li WHERE li.objekt='auftrag' AND li.parameter=a.id AND li.artikel='$id'),'&nbsp;
            <!--<a onclick=\"var menge =  prompt(\'Anzahl Artikel aus Reservierung entfernen:\',1); if(menge > 0) window.location.href=\'index.php?module=artikel&action=ausreservieren&id=$id&lid=',
            (SELECT li.id FROM lager_reserviert li WHERE li.objekt='auftrag' AND li.parameter=a.id AND li.artikel='$id' LIMIT 1)
            ,'&menge=\'+menge;\" href=\"#\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>-->
            ') as reserviert,
          a.zahlungsweise, adr.kundenfreigabe as freigabe, CONCAT(a.name, ' ',a.ansprechpartner
            ,'<br>', a.email) as Kunde, 
            if(
            (SELECT b.belegnr FROM auftrag sub_a Left Join auftrag_position ap on ap.auftrag=sub_a.id LEFT JOIN bestellung_position bp on ap.id=bp.auftrag_position_id LEFT JOIN bestellung b on b.id= bp.bestellung where sub_a.id=a.id LIMIT 1)
            ,CONCAT('<a href=\"index.php?module=bestellung&action=edit&id=',
            (SELECT b.id FROM auftrag sub_a Left Join auftrag_position ap on ap.auftrag=sub_a.id LEFT JOIN bestellung_position bp on ap.id=bp.auftrag_position_id LEFT JOIN bestellung b on b.id= bp.bestellung where sub_a.id=a.id LIMIT 1)
            ,'\" target=\"_blank\">',
            (SELECT b.belegnr FROM auftrag sub_a Left Join auftrag_position ap on ap.auftrag=sub_a.id LEFT JOIN bestellung_position bp on ap.id=bp.auftrag_position_id LEFT JOIN bestellung b on b.id= bp.bestellung where sub_a.id=a.id LIMIT 1),'</a>'),'-') as 'Bestell Nr.',            
            a.zahlungsweise, 
          format(ap.geliefert_menge,4) as gelieferte, 
          FORMAT(ap.preis,2) as preis  FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr WHERE adr.id=a.adresse AND ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben' GROUP by a.belegnr
          ORDER by a.tatsaechlicheslieferdatum, a.id
          ");
      if($table->datasets)
      {
        $lagerrunden = true;
        foreach($table->datasets as $k => $v)
        {
          if($v['menge'] != round($v['menge'])){
            $lagerrunden = false;
          }
          if($v['gelieferte'] != round($v['gelieferte'])){
            $lagerrunden = false;
          }
        }
        if($lagerrunden)
        {
          foreach($table->datasets as $k => $v)
          {
            $table->datasets[$k]['menge'] = round($v['menge']);
            $table->datasets[$k]['gelieferte'] = round($v['gelieferte']);
          }          
        }
      }
      //$table->DisplayNew('INHALT',"<a href=\"index.php?module=bestellung&action=edit&id=%value%\">Bestellung</a>");
      $table->DisplayNew('INHALT','Preis','noAction');

      $this->app->Tpl->Parse('TAB1','rahmen70.tpl');

      $this->app->Tpl->Set('INHALT','');

      $this->app->Tpl->Set('INHALT','');
      $this->app->Tpl->Add('TAB1','<h2>Offene Bestellungen</h2>');

      $table = new EasyTable($this->app);
      $table->Query("SELECT DATE_FORMAT(b.datum,'%d.%m.%Y') as datum, CONCAT('<a href=\"index.php?module=bestellung&action=edit&id=',b.id,'\" target=\"_blank\">',b.belegnr,'</a>') as 'bestellung Nr.', 
      if(
      (SELECT nummer from auftrag_position where id=bp.auftrag_position_id LIMIT 1),CONCAT('<a href=\"index.php?module=auftrag&action=edit&id=',
      (SELECT auftrag from auftrag_position where id=bp.auftrag_position_id LIMIT 1),'\" target=\"_blank\">',
      (SELECT a.belegnr from auftrag a LEFT JOIN auftrag_position ap on a.id=ap.auftrag where ap.id=bp.auftrag_position_id LIMIT 1),'</a>'),'-') as 'Auftrag Nr.',
      bp.bestellnummer as Nummer, 
      
      bp.menge, bp.geliefert, bp.vpe as VPE, a.lieferantennummer as lieferant, a.name as name, if(bp.lieferdatum!='0000-00-00', DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, 
      if(b.bestaetigteslieferdatum!='0000-00-00', DATE_FORMAT(b.bestaetigteslieferdatum,'%d.%m.%Y'),'-') as 'best. Lieferdatum', b.status as status_Bestellung, bp.bestellung
          FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN adresse a ON b.adresse=a.id
          WHERE artikel='$id' AND b.status!='storniert' AND b.status!='abgeschlossen' AND bp.geliefert<bp.menge ORDER by bp.lieferdatum DESC");
      if($table->datasets)
      {
        $lagerrunden = true;
        foreach($table->datasets as $k => $v)
        {
          if($v['menge'] != round($v['menge'])){
            $lagerrunden = false;
          }
          if($v['geliefert'] != round($v['geliefert'])){
            $lagerrunden = false;
          }
        }
        if($lagerrunden)
        {
          foreach($table->datasets as $k => $v)
          {
            $table->datasets[$k]['menge'] = round($v['menge']);
            $table->datasets[$k]['geliefert'] = round($v['geliefert']);
          }          
        }
      }
      $table->DisplayNew('INHALT',"<a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img src=\"./themes/new/images/pdf.svg\" border=\"0\"></a>&nbsp;      <a href=\"index.php?module=bestellung&action=edit&id=%value%\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" border=\"0\"></a>");
      $this->app->Tpl->Parse('TAB1','rahmen70.tpl');
    }   
    $this->app->Tpl->Set('INHALT','');
    $this->app->Tpl->Add('TAB1','<h2>Lagerplatz Bewegungen</h2>');
    // easy table mit arbeitspaketen YUI als template 
    $this->app->YUI->TableSearch('TAB1','lagerbewegungartikel', 'show','','',basename(__FILE__), __CLASS__);


    $this->app->Tpl->Set('INHALT','');

    //$this->app->Tpl->Set('TABTEXT',"Lagerbestand");
    
    $lager_platz_vpe = $this->app->DB->SelectArr("SELECT * FROM lager_platz_vpe WHERE artikel = '$id'");
    if($lager_platz_vpe)
    {
      foreach($lager_platz_vpe as $v)
      {
        $this->app->Tpl->Add('SELECTVPE','<option value="'.$v['id'].'">'.round($v['menge'],2).': '.round($v['gewicht'],2).' kg '.
        round($v['breite'],2).' x '.round($v['laenge'],2).' x '.round($v['hoehe'],2).' cm'.(
        $v['menge2'] > 1?' (in VPE '.round($v['menge2'],2).': '.round($v['gewicht2'],2).' kg '.
        round($v['breite2'],2).' x '.round($v['laenge2'],2).' x '.round($v['hoehe2'],2).' cm'.')':''
        ).'</option>');
      }
    }
    $this->app->Tpl->Parse('PAGE','artikel_lager.tpl');
  }


  public function ArtikelChargeDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    $sid = $this->app->Secure->GetGET('sid');
    if($sid > 0){
      $data = $this->app->DB->SelectRow("SELECT * FROM lager_charge WHERE id='$sid' LIMIT 1");
    }
    if(!empty($data)){
      $lager_platz = $data['lager_platz'];
      $artikel = $data['artikel'];
      $menge = $data['menge'];
      $this->app->DB->Delete("DELETE FROM lager_charge WHERE id='$sid' LIMIT 1");
      if($this->app->DB->Select("SELECT mindesthaltbarkeitsdatum FROM artikel WHERE id = '$artikel' LIMIT 1"))
      {
        $this->app->erp->Chargenlog($artikel, $lager_platz, 0, $data['charge'], $menge, 'Charge und/oder MHD gel&ouml;scht', "", 0);
      }else{
        $this->app->erp->Chargenlog($artikel, $lager_platz, 0, $data['charge'], $menge, 'Charge gel&ouml;scht', "", 0);
      }
    }
    $this->app->Location->execute("index.php?module=artikel&action=chargen&id=$id");
  }     


  public function ArticleBatchAdd()
  {
    $id = $this->app->Secure->GetGET('id');
    $lid = $this->app->Secure->GetGET('lid');
    $grund = $this->app->Secure->GetGET('grund');
    $menge = $this->app->erp->ReplaceMenge(1, $this->app->Secure->GetGET('menge'), 1);
    if($menge <= 0) {
      $this->app->Location->execute('index.php?module=artikel&action=chargen&id='.$id);
    }
    if($lid > 0){
      $data = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `lager_charge` WHERE `id` = %d LIMIT 1',
          $lid
        )
      );
    }
    if(!empty($data)){
      $lager_platz = $data['lager_platz'];
      $artikel = $data['artikel'];
      $this->app->erp->LagerEinlagern($artikel, $menge, $lager_platz, 0, $grund);
    }
    $this->app->Location->execute('index.php?module=artikel&action=chargen&id='.$id);
  }

  public function ArticleBestbeforeAdd()
  {
    $id = $this->app->Secure->GetGET('id');
    $lid = $this->app->Secure->GetGET('lid');
    $grund = $this->app->Secure->GetGET('grund');
    $menge = $this->app->erp->ReplaceMenge(1, $this->app->Secure->GetGET('menge'), 1);
    if($menge <= 0) {
      $this->app->Location->execute('index.php?module=artikel&action=mindesthaltbarkeitsdatum&id='.$lid);
    }
    if($lid > 0){
      $data = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `lager_mindesthaltbarkeitsdatum` WHERE `id` = %d LIMIT 1',
          $lid
        )
      );
    }
    if(!empty($data)){
      $lager_platz = $data['lager_platz'];
      $artikel = $data['artikel'];
      $this->app->erp->LagerEinlagern($artikel, $menge, $lager_platz, 0, $grund);
    }

    $this->app->Location->execute('index.php?module=artikel&action=mindesthaltbarkeitsdatum&id='.$id);
  }

  public function ArticleBatchReduce()
  {
    $id = $this->app->Secure->GetGET('id');
    $lid = $this->app->Secure->GetGET('lid');
    $grund = $this->app->Secure->GetGET('grund');
    $menge = $this->app->erp->ReplaceMenge(1, $this->app->Secure->GetGET('menge'), 1);
    if($menge <= 0) {
      $this->app->Location->execute('index.php?module=artikel&action=chargen&id='.$id);
    }
    if($lid > 0){
      $data = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `lager_charge` WHERE `id` = %d LIMIT 1',
          $lid
        )
      );
    }
    if(!empty($data)){
      $lager_platz = $data['lager_platz'];
      $artikel = $data['artikel'];
      $lpi = round((float)$this->app->DB->Select(
        sprintf(
          'SELECT SUM(menge) FROM `lager_platz_inhalt` WHERE `artikel` = %d AND `lager_platz` = %d',
          $artikel, $lager_platz
        )
      ),8);
      if($menge > $lpi) {
        $menge = $lpi;
      }

      $this->app->erp->LagerAuslagernRegal($artikel, $lager_platz, $menge, 0, $grund);
    }
    $this->app->Location->execute('index.php?module=artikel&action=chargen&id='.$id);
  }

  public function ArticleBestbeforeReduce()
  {
    $id = $this->app->Secure->GetGET('id');
    $lid = $this->app->Secure->GetGET('lid');
    $grund = $this->app->Secure->GetGET('grund');
    $menge = $this->app->erp->ReplaceMenge(1, $this->app->Secure->GetGET('menge'), 1);
    if($menge <= 0) {
      $this->app->Location->execute('index.php?module=artikel&action=mindesthaltbarkeitsdatum&id='.$id);
    }
    if($lid > 0){
      $data = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `lager_mindesthaltbarkeitsdatum` WHERE `id` = %d LIMIT 1',
          $lid
        )
      );
    }
    if(!empty($data)){
      $lager_platz = $data['lager_platz'];
      $artikel = $data['artikel'];
      $lpi = round((float)$this->app->DB->Select(
        sprintf(
          'SELECT SUM(menge) FROM `lager_platz_inhalt` WHERE `artikel` = %d AND `lager_platz` = %d',
          $artikel, $lager_platz
        )
      ),8);
      if($menge > $lpi) {
        $menge = $lpi;
      }

      $this->app->erp->LagerAuslagernRegal($artikel, $lager_platz, $menge, 0, $grund);
    }
    $this->app->Location->execute('index.php?module=artikel&action=mindesthaltbarkeitsdatum&id='.$id);
  }

  public function ArtikelMHDDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    $sid = $this->app->Secure->GetGET('sid');
    if($sid > 0){
      $data = $this->app->DB->SelectArr("SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE id='$sid' LIMIT 1");
    }
    if(!empty($data)){
      $lager_platz = $data[0]['lager_platz'];
      $artikel = $data[0]['artikel'];
      $menge = $data[0]['menge'];
      $charge = $data[0]['charge'];

      $this->app->DB->Delete("DELETE FROM lager_mindesthaltbarkeitsdatum WHERE id='$sid' LIMIT 1");
      if($this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id = '$artikel' LIMIT 1")){
        if(!empty($charge))
        {
          $charge = $this->app->DB->real_escape_string($charge);
          $amount_mhdcharge = $this->app->DB->Select("SELECT IFNULL(SUM(lm.menge),0) 
            FROM lager_mindesthaltbarkeitsdatum as lm 
            WHERE lm.artikel = $artikel AND lm.charge = '$charge'");
          $amount_charge = $this->app->DB->Select("SELECT IFNULL(SUM(lc.menge),0)
            FROM lager_charge AS lc
            WHERE lc.artikel = $artikel AND lc.charge = '$charge'");
          if($amount_charge >= $amount_mhdcharge + $menge)
          {
            $chargen = $this->app->DB->SelectArr("SELECT lc.* 
            FROM lager_charge AS lc
            WHERE lc.artikel = $artikel AND lc.charge = '$charge' ORDER BY lc.menge >= $menge DESC");
            $nochmenge = $menge;
            foreach($chargen as $chargenrow)
            {
              if($nochmenge <= 0)
              {
                break;
              }
              if($nochmenge >= $chargenrow['menge'])
              {
                $this->app->DB->Delete("DELETE FROM lager_charge WHERE id = ".$chargenrow['id']);
                if($this->app->DB->affected_rows() > 0){
                  $nochmenge = round($nochmenge - $chargenrow['menge'], 8);
                }
              }else{
                $this->app->DB->Update("UPDATE lager_charge SET menge = menge - $nochmenge WHERE id = ".$chargenrow['id']);
                if($this->app->DB->affected_rows() > 0){
                  $nochmenge = 0;
                  break;
                }
              }
            }
            if($nochmenge < $menge)
            {
              $this->app->erp->Chargenlog($artikel, $lager_platz, 0, $charge, round($menge-$nochmenge,8), 'Charge und / oder MHD gel&ouml;scht');
            }
          }
        }
        $this->app->erp->MHDLog($artikel, $lager_platz, 0, $data[0]['mhddatum'], $menge, 'Charge und / oder MHD gel&ouml;scht', "", 0, $data[0]['charge'], 0);
      }else{
        $this->app->erp->MHDLog($artikel, $lager_platz, 0, $data[0]['mhddatum'], $menge, 'MHD gel&ouml;scht', "", 0, $data[0]['charge'], 0);
      }
    }
    $this->app->Location->execute("index.php?module=artikel&action=mindesthaltbarkeitsdatum&id=$id");
  }     

  public function ArtikelChargen()
  {
    $this->ArtikelMenu();
    $obj = $this->app->erp->LoadModul('chargen');
    if($obj)
    {
      $obj->ChargenArtikelChargen();
    }
  }


  public function ArtikelMHD()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->ArtikelMenu();
    //$this->app->Tpl->Set('TABTEXT','Mindesthaltbarkeitsdatum');

    $this->app->YUI->DatePicker('datum');
    $this->app->YUI->AutoComplete('lagerplatz','lagerplatz');

    if($this->app->Secure->GetPOST('anlegen')!='')
    {
      $formmenge = str_replace(',','.',$this->app->Secure->GetPOST('menge'));
      $datum = $this->app->Secure->GetPOST('datum');
      $charge = $this->app->Secure->GetPOST('charge');
      $lagerplatz = $this->app->Secure->GetPOST('lagerplatz');
      $datum = $this->app->String->Convert($datum,'%1.%2.%3','%3-%2-%1');
      $lagerplatz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$lagerplatz' LIMIT 1");

      if(is_numeric($formmenge) && is_numeric($lagerplatz) && $datum!='--')
      {
        if($this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id = '$id' LIMIT 1"))
        {
          if(!empty($charge)){
          $this->app->erp->AddChargeLagerOhneBewegung(
            $id,
            $formmenge,
            $lagerplatz,
            $datum,
            $charge,
            'Charge und / oder MHD angelegt');
          }
          $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($id,$formmenge,$lagerplatz,$datum,$charge,0 , '', 0, 'Charge und / oder MHD angelegt');
        }else{
          $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($id,$formmenge,$lagerplatz,$datum,$charge,0 , '', 0, 'MHD angelegt');
        }
      } else {
        $this->app->Tpl->Add('TAB1','<div class="error">Fehler: Bitte Menge, MHD und Lager angeben!</div>');        
      }
    }

    $menge = $this->app->erp->ArtikelImLager($id);
    $mhd = $this->app->DB->Select("SELECT SUM(menge) FROM lager_mindesthaltbarkeitsdatum WHERE artikel='$id'");
    if($menge > $mhd)
      $this->app->Tpl->Add('TAB1',"<div class=error>Achtung: Es sind ".($menge-$mhd)." Eintr&auml;ge zu wenig vorhanden!</div>"); 
    else if ($menge < $mhd)
      $this->app->Tpl->Add('TAB1',"<div class=error>Achtung: Es sind ".($mhd-$menge)." Eintr&auml;ge zu viel vorhanden!</div>");  

    $this->app->Tpl->Add('TAB1',"<br><center><form method=\"post\" action=\"\">Menge:&nbsp;<input name=\"menge\" type=\"text\" size=\"5\" value=\"1\">&nbsp;MHD:&nbsp;<input type=text size=\"15\" id=\"datum\" name=\"datum\">&nbsp;Lager:&nbsp;<input type=\"text\" size=\"20\" id=\"lagerplatz\" name=\"lagerplatz\">&nbsp;Charge (optional):&nbsp;<input type=text size=\"15\" id=\"charge\" name=\"charge\">&nbsp;<input type=\"submit\" value=\"fehlende Eintr&auml;ge anlegen\" name=\"anlegen\"></form></center>");


    $this->app->YUI->TableSearch('TAB1','mindesthaltbarkeitsdatum', 'show','','','mhdwarning.php', 'Mhdwarning');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  
  function getStuecklistenbaum(&$sbaum, $parent, $menge = 1)
  {
    $res = $this->app->DB->SelectArr("SELECT s.id, trim(s.menge)+0 as menge, s.art, s.stuecklistevonartikel, art.nummer, s.artikel, art.name_de FROM stueckliste s INNER JOIN artikel art ON s.artikel = art.id WHERE stuecklistevonartikel = '$parent' ORDER by sort");
    if(!empty($res))
    {
      foreach($res as $k => $v)
      {
        $v['menge'] *= $menge;
        $sbaum[] = $v;
        $this->getStuecklistenbaum($sbaum, $v['artikel'], $v['menge']);
      }
    }
  }

  public function ArtikelStueckliste()
  {
    if($this->app->Secure->GetGET('cmd') === 'getbaum')
    {
      $disabled = false;
      $id = (int)$this->app->Secure->GetGET('id');
      $this->getStuecklistenbaum($sbaum, $id);
      $maxlvl = 0;
      foreach($sbaum as $k => $v)
      {
        $ind[$v['artikel']] = $k;

        if($v['stuecklistevonartikel'] == $id)
        {
          $sbaum[$k]['lvl'] = 0;
          $name = 'node'.$k;
          $$name = new stdClass();
          $$name->id = $v['id'];
          $$name->label = ' '.$v['menge'].' x <a target="_blank" href="index.php?module=artikel&action=edit&id='.$v['artikel'].'">'.$v['nummer'].' '.(strlen($v['name_de']) < 30?$v['name_de']:(mb_substr($v['name_de'],0,27).'...')).'</a>';
          $$name->checkbox = false;
          $$name->inode = false;
          $$name->radio = false;
            switch($v['art'])
            {
              case 'it':
                $$name->icon = 'imgit';
              break;
              case 'bt':
                $$name->icon = 'imgbt';
              break;
              default:
                $$name->icon = 'imget';
              break;
            }
          if($disabled)$$name->disabled = true;
          /*if($kategorie)
          {
            foreach($kategorie as $ka)
            {
              if($ka['kategorie'] == $v['id'])$$name->checked = true;
            }
          }*/
          $baum[] = $$name;
          $sbaum[$k]['node'] = $$name;
        }else{
          if(isset($ind[$v['stuecklistevonartikel']]))
          {
            $name = 'node'.$k;
            $$name = new stdClass();
            $$name->id = $v['id'];
            $$name->label = ' '.$v['menge'].' x <a target="_blank" href="index.php?module=artikel&action=edit&id='.$v['artikel'].'">'.$v['nummer'].' '.(strlen($v['name_de']) < 30?$v['name_de']:(mb_substr($v['name_de'],0,27).'...')).'</a>';
            $$name->checkbox = false;
            $$name->inode = false;
            $$name->radio = false;
            if($disabled)$$name->disabled = true;
            /*if($kategorie)
            {
              foreach($kategorie as $ka)
              {
                if($ka['kategorie'] == $v['id'])$$name->checked = true;
              }
            }*/
            switch($v['art'])
            {
              case 'it':
                $$name->icon = 'imgit';
              break;
              case 'bt':
                $$name->icon = 'imgbt';
              break;
              default:
                $$name->icon = 'imget';
              break;
            }
            $sbaum[$k]['node'] = $$name;
            $sbaum[$k]['lvl'] = 1+$sbaum[$ind[$v['stuecklistevonartikel']]]['lvl'];
            if($sbaum[$k]['lvl'] > $maxlvl)$maxlvl = $sbaum[$k]['lvl'];
            $sbaum[$ind[$v['stuecklistevonartikel']]]['inode'] = true;
            //$kategorien[$ind[$v['parent']]]['node']->inode = true;
            $sbaum[$ind[$v['stuecklistevonartikel']]]['node']->open = true;
            $sbaum[$ind[$v['stuecklistevonartikel']]]['node']->branch[] = $$name;
          }
        }
      }
      echo json_encode($baum);
      $this->app->ExitXentral();
    }


    $this->app->Tpl->Add('UEBERSCHRIFT','" (St&uuml;ckliste)');
    $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET('id');

    if($this->app->Secure->GetPOST('artikel')!=''){
      $this->app->Tpl->Set('AKTIV_TAB2', 'selected');
    }
    else{
      $this->app->Tpl->Set('AKTIV_TAB1', 'selected');
    }

    $this->app->YUI->TableSearch('TAB1','stueckliste', 'show','','',basename(__FILE__), __CLASS__);

    $stueck = $this->app->erp->ArtikelAnzahlLagerStueckliste($id);

    $this->ArtikelStuecklisteImport('TAB3');
    $url = 'index.php?module=artikel&action=stueckliste&cmd=getbaum&id='.$id;
    $this->app->Tpl->Set('URL',$url);
    $this->app->Tpl->Add('TAB1',"<center>");
    $this->app->Tpl->Add('TAB1',"<input type='button' name='neuestueckliste' class='btnGreen' style='width:10em; height:3em; text-align:center; line-height:1em' onclick=\"StuecklisteNeuePositionEdit(0)\" value='&#10010; Neue Position'>");
    $this->app->Tpl->Add('TAB1',"<input type='button' style='width:10em; height:3em; text-align: center; line-height:1em' onclick=\"if(!confirm('Wirklich St&uuml;ckliste leeren?')) return false; else window.location.href='index.php?module=artikel&action=stuecklisteempty&id=$id';\" value='St&uuml;ckliste leeren'>");
    //$this->app->Tpl->Add('TAB1',"<button onclick=\"if(!confirm('Wirklich St&uuml;ckliste leeren?')) return false; else window.location.href='index.php?module=artikel&action=stuecklisteempty&id=$id';\">St&uuml;ckliste leeren</button>");
    $this->app->Tpl->Add('TAB1',"<input type='button' style='width:10em; height:3em; text-align: center; line-height:1em' onclick=\"window.location.href='index.php?module=artikel&action=stuecklisteexport&id=$id';\" value='St&uuml;ckliste als CSV'>");
    //$this->app->Tpl->Add('TAB1',"<button onclick=\"window.location.href='index.php?module=artikel&action=stuecklisteexport&id=$id';\">St&uuml;ckliste als CSV</button>");
    $this->app->Tpl->Add('TAB1',"</center><br><br>");

    $this->app->Tpl->Add('TAB1',"<div class=\"info\">Aktuell k&ouml;nnen $stueck St&uuml;ck produziert werden (<a href=\"index.php?module=artikel&action=stuecklisteetiketten&id=".$id."\">Etiketten f&uuml;r St&uuml;ckliste drucken</a>)</div>");
    $this->app->Tpl->Add('TAB1','<style>');
    $icons = array('et'=>'./themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/dokumentoffen.png','it'=>'./themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/dokumentok.png','bt'=>'./themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/dialog_o.gif');
    foreach($icons as $k => $icon)
    {
      $this->app->Tpl->Add('TAB1','
      span.img'.$k.' {
        background-image:url('.$icon.') !important;
        
      }
      ');
      
    }

    $this->app->YUI->AutoComplete('artikel', 'artikelnummerstueckliste');
    //$this->app->YUI->AutoComplete('alternative', 'artikelnummer');

    $this->app->Tpl->Add('TAB1','</style>');
    
    if($this->app->erp->IstStuecklistenZirkel($id))
    {
      $this->app->Tpl->Add('MESSAGE', '<div class="error">{|Diese St&uuml;ckliste enth&auml;lt Artikel die einen Zirkelbezug verursachen!|}</div>');
    }

    $addAlternative = '<a href="javascript:;" onclick="PartsListAlternativeEditSave();">';
    $addAlternative .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pluspaket_gruen_kl.png\" border=\"0\">";
    $addAlternative .= '</a>';

    if($this->app->Secure->GetGET('cmd') === 'getalternative'){
      $id = (int)$this->app->Secure->GetPOST('id');

      $data = $this->app->DB->SelectRow("SELECT s.id, s.art FROM stueckliste s WHERE s.id = '$id' LIMIT 1");

      if(empty($data)) {
        $data['id'] = 0;
        $data['art'] = '';
      }

      echo json_encode($data);
      $this->app->ExitXentral();

    }
    elseif($this->app->Secure->GetGET('cmd') === 'getalternativedetails') {
      $alternativeId = (int)$this->app->Secure->GetPOST('id');

      $data = $this->app->DB->SelectArr("SELECT id as alternativeId, alternative_article_id, reason FROM parts_list_alternative WHERE id = '$alternativeId'");

      if($data){
        $data = reset($data);
        $data['article'] = $this->app->DB->Select("SELECT CONCAT(nummer, ' ', name_de) FROM artikel WHERE id = '".$data['alternative_article_id']."' LIMIT 1");


      }else{
        $data['id'] = 0;
        $data['art'] = '';
      }

      echo json_encode($data);
      $this->app->ExitXentral();

    }
    elseif($this->app->Secure->GetGET('cmd') === 'savealternative') {
      $id = (int)$this->app->Secure->GetPOST('id');
      $alternativeId = $this->app->Secure->GetPOST('alternativeId');
      $alternativeArticle = trim($this->app->Secure->GetPOST('alternativeArticle'));
      $reason = trim($this->app->Secure->GetPOST('reason'));

      $error = "";

      if($alternativeArticle == ""){
        $error .= "Bitte alternativen Artikel ausfüllen\n";
      }else{
        $alternativeArticleNo = explode(' ', $alternativeArticle);
        $alternativeArticleNo = $alternativeArticleNo[0];
        if($alternativeArticleNo != ""){
          $alternativeArticleId = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '$alternativeArticleNo' LIMIT 1 ");
          if($alternativeArticleId <= 0 || $alternativeArticleId == ''){
            $error .= "Kein gültiger Artikel\n";
          }
        }else{
          $error .= "Keine Artikelnummer\n";
        }
      }

      if($alternativeArticleId > 0){
        //NOCH PRUEFEN
        $alternativeArticleAlreadyExists = $this->app->DB->Select("SELECT id FROM parts_list_alternative WHERE alternative_article_id = '$alternativeArticleId' AND parts_list_id = '$id' AND id != '$alternativeId' LIMIT 1");
        if($alternativeArticleAlreadyExists != '' && $alternativeArticleAlreadyExists > 0){
          $error .= "Alternativer Artikel existiert bereits\n";
        }
      }

      if($error == ""){
        if($id){
          if($alternativeId > 0 && $alternativeId != ''){
            $this->app->DB->Update("UPDATE parts_list_alternative SET alternative_article_id = '$alternativeArticleId', reason = '$reason' WHERE id = '$alternativeId'");
          }else{
            $this->app->DB->Insert("INSERT INTO parts_list_alternative (parts_list_id, alternative_article_id, reason) VALUES ('$id', '$alternativeArticleId', '$reason')");
          }

          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }else{
          echo json_encode(array('status'=>0,'statusText'=>'Fehler'));
          $this->app->ExitXentral();
        }
      }else{
        echo json_encode(array('status'=>0,'statusText'=>$error));
        $this->app->ExitXentral();
      }

    }elseif($this->app->Secure->GetGET('cmd') === 'deletealternative'){
      $id = (int) $this->app->Secure->GetPOST('id');
      if($id)
        $this->app->DB->Update("DELETE FROM parts_list_alternative WHERE id = '$id'");

      echo json_encode(array('status'=>1));
      $this->app->ExitXentral();
    }elseif($this->app->Secure->GetGET('cmd') === 'savetype'){
      $id = (int) $this->app->Secure->GetPOST('id');
      $type = $this->app->Secure->GetPOST('type');

      if($type != "" && $id > 0){
        $this->app->DB->Update("UPDATE stueckliste SET art = '$type' WHERE id = '$id'");
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }else{
        echo json_encode(array('status'=>0,'statusText'=>'Fehler'));
        $this->app->ExitXentral();
      }
    }


    //$this->app->Tpl->Add('ADDALTERNATIVE', $addAlternative );
    $this->app->YUI->AutoComplete('parts_list_alternative_article', 'artikelnummer');

    $this->app->YUI->TableSearch('PARTSLISTALTERNATIVES', 'parts_list_alternatives', 'show', '', '', basename(__FILE__), __CLASS__);
    
    $this->app->Tpl->Parse('PAGE','stuecklisteuebersicht.tpl');
  }

  public function ArtikelStuecklisteEmpty()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->DB->Delete("DELETE FROM stueckliste WHERE stuecklistevonartikel='$id'");
    }
    $this->app->Location->execute("index.php?module=artikel&action=stueckliste&id=$id");
  }

  function ArtikelStuecklisteExport()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $result = $this->app->DB->SelectArr("SELECT a.nummer, a.name_de, a.hersteller,a.herstellernummer,  REPLACE(TRIM(s.menge)+0,'.',',') as menge, s.referenz, s.place, s.layer, s.wert, s.bauform, s.zachse,s.xpos, s.ypos, s.art FROM stueckliste s 
      LEFT JOIN artikel a ON a.id=s.artikel WHERE s.stuecklistevonartikel='$id'");
    }
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename=file.csv');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');
    $cresult = !empty($result)?count($result):0;
    for($i=0;$i<$cresult;$i++)
    {
      if($i==0) {
        foreach($result[$i] as $spalte=>$wert)
          echo '"'.$spalte.'";';
        echo "\r\n";
      }
      fputcsv($out, $result[$i],';','"');
    }

    fclose($out);
    $this->app->ExitXentral();
  }

  public function UpStueckliste()
  {
    $this->app->YUI->SortListEvent('up','stueckliste','stuecklistevonartikel');
    $this->ArtikelStueckliste();
  }

  public function DownStueckliste()
  {
    $this->app->YUI->SortListEvent('down','stueckliste','stuecklistevonartikel');
    $this->ArtikelStueckliste();
  }


  public function DelStueckliste()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $sort = $this->app->DB->Select("SELECT sort FROM stueckliste WHERE id='$id' LIMIT 1");
      $sid = $this->app->DB->Select("SELECT stuecklistevonartikel FROM stueckliste WHERE id='$id' LIMIT 1");

      $this->app->DB->Delete("DELETE FROM stueckliste WHERE id='$id'");

      $this->app->DB->Delete("UPDATE stueckliste SET sort=sort-1 WHERE stuecklistevonartikel='$sid' AND sort > $sort LIMIT 1");
    }
    $this->app->Location->execute('index.php?module=artikel&action=stueckliste&id='.$sid);
  }


  function ArtikelInStueckliste()
  {
    $this->ArtikelMenu();
    $this->app->Tpl->Set('TABTEXT','In St&uuml;ckliste von folgenden Artikel vorhanden');
    $this->app->YUI->TableSearch('TAB1','instueckliste', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function ArtikelStuecklisteEditPopup()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'edit'){

      $id = (int)$this->app->Secure->GetPOST('id');
        
      $data = $this->app->DB->SelectRow('SELECT s.id, s.artikel, '.$this->app->erp->FormatMenge("s.menge")." as menge, s.art, s.referenz, s.layer, s.place, s.wert, s.bauform, s.zachse, s.xpos, s.ypos FROM stueckliste s WHERE s.id = '$id' LIMIT 1");
      
        if($data){
          if($data['artikel'] == 0){
            $data['artikel'] = '';
          }else{
            if($data['artikel'] != ''){
              $artikel = $this->app->DB->SelectRow("SELECT nummer, name_de FROM artikel WHERE id = '".$data['artikel']."' LIMIT 1");
              if(!empty($artikel)){
                $data['artikel'] = $artikel['nummer'].' '.$artikel['name_de'];
              }
            }
          }

          if($data['art'] == ''){
            $data['art'] = 'et';
          }

          /*if($data['alternative'] == 0){
            $data['alternative'] = '';
          }else{
            if($data['alternative'] != ''){
              $alternative = $this->app->DB->SelectRow("SELECT nummer, name_de FROM artikel WHERE id = '".$data['alternative']."' LIMIT 1");
              if(!empty($alternative)){
                $data['alternative'] = $alternative['nummer'].' '.$alternative['name_de'];
              }
            }
          }*/


        }else{
          $data['id'] = 0;
          $data['artikel'] = '';
          $data['menge'] = '';
          $data['art'] = 'et';
          //$data['alternative'] = '';
          $data['referenz'] = '';
          $data['layer'] = 'Top';
          $data['place'] = 'DP';
          $data['wert'] = '';
          $data['bauform'] = '';
          $data['zachse'] = '';
          $data['xpos'] = '';
          $data['ypos'] = '';

        }
        echo json_encode($data);
      $this->app->ExitXentral();

    }
    if($cmd === 'save' || $cmd === 'doppeltsave'){

      $cmdsave = $cmd;

      $id = (int)$this->app->Secure->GetPOST('eid');
      $startikelid = (int)$this->app->Secure->GetPOST('estartikelid');
      $artikel = trim($this->app->Secure->GetPOST('eartikel'));
      $menge = str_replace(',','.',trim($this->app->Secure->GetPOST('emenge')));
      $art = trim($this->app->Secure->GetPOST('eart'));
      //$alternative = trim($this->app->Secure->GetPOST('ealternative'));
      $referenz = trim($this->app->Secure->GetPOST('ereferenz'));
      $layer = trim($this->app->Secure->GetPOST('elayer'));
      $place = trim($this->app->Secure->GetPOST('eplace'));
      $wert = trim($this->app->Secure->GetPOST('ewert'));
      $bauform = trim($this->app->Secure->GetPOST('ebauform'));
      $zachse = trim($this->app->Secure->GetPOST('ezachse'));
      $xpos = trim($this->app->Secure->GetPOST('expos'));
      $ypos = trim($this->app->Secure->GetPOST('eypos'));

      if($cmdsave === 'doppeltsave'){
        $einfuegen = trim($this->app->Secure->GetPOST('eeinfuegen'));
        $mengeerhoehen = trim($this->app->Secure->GetPOST('emengeerhoehen'));

      }

      $error = '';

      if($artikel == ''){
        $error .= "Bitte Artikel ausfüllen\n";
      }else{
        $artikelnr = explode(' ', $artikel);
        $artikelnr = $artikelnr[0];
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '$artikelnr' LIMIT 1");
        if($artikelid != ''){
        }else{
          $error .= "Bitte gültigen Artikel ausfüllen\n";
        }
      }

      //12.07.19 LG keine menge 0 bei stuecklistenposition
      if($menge == '' || $menge <= 0){
        $error .= "Bitte Menge ausfüllen"."\n";
      }

      $errordoppelt = '';
      $doppeltid = '';


      $ergebnis = $this->artinsturec($startikelid, $artikelid, $cmdsave, $id);

      $errordoppelt = $ergebnis['errordoppelt'];
      $doppeltid = $ergebnis['doppeltid'];

        
      /*$alternativeid = 0;
      if($alternative != ''){
        $alternativenr = explode(' ', $alternative);
        $alternativenr = $alternativenr[0];
        $alternativeid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '$alternativenr' LIMIT 1");
        if($alternativeid != ''){
        }else{
          $error .= "Bitte gültigen Alternativ Artikel ausfüllen\n";
        }
      }*/

      if($this->app->erp->IstStuecklistenZirkel($artikelid, $startikelid)){
        $error .= 'Stückliste enthält Artikel die einen Zirkelbezug verursachen!';
      }

      if($error == "" && $errordoppelt == ''){
        if($cmdsave === 'doppeltsave' && $mengeerhoehen == 1 && $doppeltid > 0){
          $this->app->DB->Update("UPDATE stueckliste SET menge = menge + '$menge' WHERE id = '$doppeltid'");
          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }
        if($id){
          $this->app->DB->Update("UPDATE stueckliste SET artikel = '$artikelid', menge = '$menge', art = '$art', referenz = '$referenz', layer = '$layer', place = '$place', wert = '$wert', bauform = '$bauform', zachse = '$zachse', xpos = '$xpos', ypos = '$ypos' WHERE id = '$id'");
          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }
        if(($cmdsave === 'doppeltsave' && $einfuegen == 1) || $cmdsave === 'save'){
          $this->app->DB->Insert("INSERT INTO stueckliste (sort, artikel, referenz, place, layer, stuecklistevonartikel, menge, art, firma, wert, bauform, zachse, xpos, ypos) VALUES (0, '$artikelid', '$referenz', '$place', '$layer', '$startikelid', '$menge', '$art', 1, '$wert', '$bauform', '$zachse', '$xpos', '$ypos')");
          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }
        echo json_encode(array('status'=>0,'statusText'=>'Fehler'));
        $this->app->ExitXentral();
      }
      if($error != ''){
        echo json_encode(array('status'=>0,'statusText'=>$error,'doppelt'=>''));
        $this->app->ExitXentral();
      }
      if($error == '' && $errordoppelt != ''){
        echo json_encode(array('status' => 0, 'statusText' => '', 'doppelt' => $errordoppelt));
        $this->app->ExitXentral();
      }
    }
  }

  function artinsturec($hauptartikelid, $unterartikelid, $cmdsave, $id){

    $stuecklistenunterartikel = $this->app->DB->SelectArr("SELECT id, artikel FROM stueckliste WHERE stuecklistevonartikel = '$hauptartikelid' AND id != '$id'");
    foreach($stuecklistenunterartikel as $key=>$value){
      if($unterartikelid == $value['artikel']){
        $ergebnis['doppeltid'] = $value['id'];
        //$doppeltid = $value['id'];
        if($cmdsave === 'save'){
          $ergebnis['errordoppelt'] = 'doppelt';
          //$errordoppelt .= "doppelt";
        }            
      }else{
        $ergebnis = $this->artinsturec($value['artikel'], $unterartikelid, $cmdsave, $id);
      }
    }
    return $ergebnis;
  }

  public function ArtikelStatistik()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->ArtikelMenu();

    //$this->app->Tpl->Set('TABTEXT',"Statistik");
    //$this->app->Tpl->Set('TAB1',"<h2>Statistik Mengen</h2><br>");

    $summe['jahr']='<b>Summe</b>';
    $summe['monat']='';

    $auftraege = $this->app->DB->SelectArr("SELECT  EXTRACT(YEAR FROM a.datum) as jahr,  EXTRACT(MONTH FROM a.datum) as monat, TRIM(sum(ap.menge))+0 as menge
        FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag 
        WHERE ap.artikel='$id'  AND (a.status<>'storniert' and a.status <> 'angelegt')  GROUP By monat,jahr ORDER by jahr DESC, monat DESC"
        );
    $mengeauftraege = 0;
    $mengeanfragen = 0;
    $mengeangebote = 0;
    $mengelieferscheine = 0;
    $mengebestellungen = 0;

    if($auftraege)
    {
      foreach($auftraege as $el)
      {
        $tab[$el['jahr']][$el['monat']]['auftraege'] = $el['menge'];
        $summe['auftrag'] += $el['menge'];
      }
    } else {
        $summe['auftrag']='';
    }


    $lieferscheine = $this->app->DB->SelectArr(
        "SELECT  EXTRACT(YEAR FROM l.datum) as jahr,  EXTRACT(MONTH FROM l.datum) as monat, TRIM(sum(lp.menge))+0 as menge
        from lieferschein l 
        LEFT JOIN lieferschein_position lp on l.id = lp.lieferschein
        WHERE lp.artikel='$id' AND (l.status='versendet')  GROUP By monat,jahr ORDER by jahr DESC, monat DESC"
        );

    if($lieferscheine)
    {
      foreach($lieferscheine as $el)
      {
        $tab[$el['jahr']][$el['monat']]['lieferscheine'] = $el['menge'];
        $summe['lieferscheine'] += $el['menge'];
      }
    } else {
        $summe['lieferschein']='';
    }

    $rechnungen = $this->app->DB->SelectArr(
        "SELECT  EXTRACT(YEAR FROM r.datum) as jahr,  EXTRACT(MONTH FROM r.datum) as monat, TRIM(sum(rp.menge))+0 as menge
        from rechnung r 
        LEFT JOIN rechnung_position rp on r.id = rp.rechnung
        WHERE rp.artikel='$id' AND (r.status!='angelegt' AND r.status!='storniert')  GROUP By monat,jahr ORDER by jahr DESC, monat DESC"
        );

    if($rechnungen)
    {
      foreach($rechnungen as $el)
      {
        $tab[$el['jahr']][$el['monat']]['rechnungen'] = $el['menge'];
        $summe['rechnungen'] += $el['menge'];
      }
    } else {
        $summe['rechnungen'] = '';
    }
    $angebote = $this->app->DB->SelectArr("SELECT  EXTRACT(YEAR FROM a.datum) as jahr,  EXTRACT(MONTH FROM a.datum) as monat, TRIM(sum(ap.menge))+0 as menge
        FROM angebot_position ap LEFT JOIN angebot a ON a.id=ap.angebot 
        WHERE ap.artikel='$id'  AND (a.status<>'storniert' and a.status <> 'angelegt')  GROUP By monat,jahr ORDER by jahr DESC, monat DESC"
        );

    if($angebote)
    {
      foreach($angebote as $el)
      {
        $tab[$el['jahr']][$el['monat']]['angebote'] = $el['menge'];
        $summe['angebote'] += $el['menge'];
      }
    } else {
      $summe['angebote']='';
    }



    $bestellungen = $this->app->DB->SelectArr("SELECT  EXTRACT(YEAR FROM a.datum) as jahr,  EXTRACT(MONTH FROM a.datum) as monat, TRIM(sum(ap.menge))+0 as menge
        FROM bestellung_position ap LEFT JOIN bestellung a ON a.id=ap.bestellung 
        WHERE ap.artikel='$id'  AND (a.status<>'storniert' and a.status <> 'angelegt')  GROUP By monat,jahr ORDER by jahr DESC, monat DESC"
        );

    if($bestellungen)
    {
      foreach($bestellungen as $el)
      {
        $tab[$el['jahr']][$el['monat']]['bestellungen'] = $el['menge'];
        $summe['bestellungen'] += $el['menge'];
      }
    } else {
        $summe['bestellungen']='';
    }

    if($tab)
    {
      $table = new EasyTable($this->app);
      $table->headings = array('Jahr','Monat','Auftr&auml;ge','Lieferscheine','Rechnungen','Angebote','Bestellungen');
      krsort($tab);
      foreach($tab as $jahr => $monate)
      {
        krsort($monate);
        foreach($monate as $monat => $row)
        {
          
          $displayrow[0] = $jahr;
          $displayrow[1] = $monat;
          $displayrow[2] = isset($row['auftraege'])?$row['auftraege']:'';
          $displayrow[6] = isset($row['lieferscheine'])?$row['lieferscheine']:'';
          $displayrow[3] = isset($row['rechnungen'])?$row['rechnungen']:'';
          $displayrow[4] = isset($row['angebote'])?$row['angebote']:'';
          $displayrow[5] = isset($row['bestellungen'])?$row['bestellungen']:'';
          $table->AddRow($displayrow);
        }
      }
      $table->AddRow($summe);
      //$html .= "</table>";
      $table->DisplayNew('TAB1','Bestellungen','noAction');
    } else {
      $this->app->Tpl->Set('TAB1','<div class="info">Zu dem Artikel gibt es noch keine Statistiken, da dieser noch nicht verkauft wurde!</div>');
    }

    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function ArtikelBelege()
  {
    $this->ArtikelMenu();
    $this->app->YUI->TableSearch('TAB1','artikel_belege', 'show','','',basename(__FILE__), __CLASS__);
    if(!($this->app->erp->ModulVorhanden('produktion') && $this->app->erp->RechteVorhanden('produktion','list')))
    {
      $this->app->Tpl->Set('VORPRODUKTION','<!--');
      $this->app->Tpl->Set('NACHPRODUKTION','-->');
    }
    $belege = array('auftrag','rechnung','angebot','bestellung','lieferschein');
    
    foreach($belege as $beleg)
    {
      if(!$this->app->erp->RechteVorhanden($beleg,'list'))
      {
        $this->app->Tpl->Set('VOR'.strtoupper($beleg),'<!--');
        $this->app->Tpl->Set('NACH'.strtoupper($beleg),'-->');
      }
    }
    $this->app->Tpl->Parse('PAGE','artikel_belege.tpl');
  }


  public function ArtikelOffeneBestellungen()
  {
    $this->ArtikelMenu();

    $this->app->YUI->TableSearch('TAB1', 'artikel_offenebestellungen', 'show','','',basename(__FILE__), __CLASS__);

    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  public function ArtikelEinlagern()
  {
    $id = $this->app->Secure->GetGET('id');
    $lid = (int)$this->app->Secure->GetGET('lid');
    $vpeid = $lid?$this->app->DB->Select("SELECT lager_platz_vpe FROM lager_platz_inhalt WHERE id = '$lid' LIMIT 1"):0;
    $menge = str_replace(',','.',$this->app->Secure->GetGET('menge'));
    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id = '$id' LIMIT 1");
    if($seriennummern != '' && $seriennummern !== 'keine'){
      $menge = (int)$menge;
    }
    $grund = $this->app->Secure->GetGET('grund');

    // menge holen in lagerregaplplatz
    //$menge_lager = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $lager_platz = $this->app->DB->Select("SELECT lager_platz FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");

    //$neuemenge = $menge_lager + $menge;
    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
    if(is_numeric($menge)  || is_float($menge))
    {
      $this->app->erp->LagerEinlagern($id,$menge,$lager_platz,$projekt,'Manuell Bestand angepasst ('.$grund.')','','','',0,$vpeid);
      $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Der Artikel \"$name_de\" wurde $menge mal eingelagert.</div>");
    } else {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Fehler: Unbekannte oder falsche Menge: $menge bei Artikel \"$name_de\". Die Artikel wurden nicht eingelagert!</div>");
    }
    

    $this->app->Location->execute("index.php?module=artikel&action=lager&id=$id&msg=$msg");
  }

  public function ArtikelUmlagern()
  {
    $id = $this->app->Secure->GetGET('id');
    $lid = (int)$this->app->Secure->GetGET('lid');
    $vpeid = $this->app->DB->Select("SELECT lager_platz_vpe FROM lager_platz_inhalt WHERE id = '$lid' LIMIT 1");
    $menge = str_replace(',','.',$this->app->Secure->GetGET('menge'));
    $grund = $this->app->Secure->GetGET('grund');
    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id = '$id' LIMIT 1");
    if($seriennummern != '' && $seriennummern !== 'keine'){
      $menge = (int)$menge;
    }
    // menge holen in lagerregaplplatz
    $lager_platz = $this->app->DB->Select("SELECT lager_platz FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    //$projekt = $this->app->DB->Select("SELECT projekt FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $menge_lager = $this->app->erp->ArtikelImLagerPlatz($id,$lager_platz);
    
    if($menge_lager > 0)
    {
      if($menge > $menge_lager)
      {
        $this->app->erp->LagerAuslagernRegal($id, $lager_platz, $menge_lager, 'Manuell Bestand angepasst ('.$grund.')','','','',0,$vpeid);
      }else{
        $this->app->erp->LagerAuslagernRegal($id, $lager_platz, $menge, 'Manuell Bestand angepasst ('.$grund.')','','','',0,$vpeid);
      }
    }

    if($menge_lager < $menge) {
      $menge = $menge_lager;
    }

    $grund = $this->app->erp->base64_url_encode($grund);
    $this->app->Location->execute("index.php?module=lager&action=bucheneinlagern&artikelid=$id&menge=$menge&cmd=umlagern&back=artikel&grund=$grund".($vpeid?"&vpeid=".$vpeid:''));
  }



  public function ArtikelAuslagern()
  {
    $id = $this->app->Secure->GetGET('id');
    $lid = $this->app->Secure->GetGET('lid');
    $vpeid = $lid?$this->app->DB->Select("SELECT lager_platz_vpe FROM lager_platz_inhalt WHERE id = '$lid' LIMIT 1"):0;
    $menge = str_replace(',','.',$this->app->Secure->GetGET('menge'));
    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id = '$id' LIMIT 1");
    if($seriennummern != '' && $seriennummern !== 'keine'){
      $menge = (int)$menge;
    }
    $grund = $this->app->Secure->GetGET('grund');

    // menge holen in lagerregaplplatz
    //$menge_lager = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $lager_platz = $this->app->DB->Select("SELECT lager_platz FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    
    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");

    if(is_numeric($menge) || is_float($menge))
    {
      $result = $this->app->erp->LagerAuslagernRegal($id,$lager_platz,$menge,$projekt,'Manuell Bestand angepasst ('.$grund.')','','',0,$vpeid);
      if($result < 0)
      {
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel \"$name_de\" wurde nicht ausgelagert! Er ist nicht so oft im Lager!</div>");
      } else {
        $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Der Artikel \"$name_de\" wurde $menge mal ausgelagert.</div>");
      }
    }
    else {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Fehler: Unbekannte oder falsche Menge: $menge bei Artikel \"$name_de\". Die Artikel wurden nicht ausgelagert!</div>");
    }
    $this->app->Location->execute("index.php?module=artikel&action=lager&id=$id&msg=$msg");
  }

  public function ArtikelAusreservieren()                                                                                                                                                                                   
  {                                                                                                                                                                   
    $id = $this->app->Secure->GetGET('id');                                                                                                                           
    $lid = $this->app->Secure->GetGET('lid');                                                                                                                         
    $menge = str_replace(',','.',$this->app->Secure->GetGET('menge'));
    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id = '$id' LIMIT 1");
    if($seriennummern != '' && $seriennummern !== 'keine'){
      $menge = (int)$menge;
    }                                                                                                                                                          
    // menge holen in lagerregaplplatz                                                                                                                                                                          
    $menge_lager = $this->app->DB->Select("SELECT menge FROM lager_reserviert WHERE id='$lid' LIMIT 1");                                                                                                      
    $neuemenge = $menge_lager - $menge;                                                                                                                           
    //echo "menge_lager = $menge_lager; menge raus = $menge; neuemenge = $neuemenge; lid=$lid";                                                                                                                     
    if($menge_lager <= $menge){
      $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE id='$lid' LIMIT 1");
    }
    else{
      $this->app->DB->Update("UPDATE lager_reserviert SET menge='$neuemenge' WHERE id='$lid' LIMIT 1");
    }
    if($menge_lager < $menge) {
      $menge = $menge_lager;
    }                                                                                                                                                            

    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");                                                                                                                    
    $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Reservierung \"$name_de\" wurde $menge mal entfernt.</div>");                                                                                                  
    $this->app->Location->execute("index.php?module=artikel&action=lager&id=$id&msg=$msg");
  }

  /**
   * @param int $id
   *
   * @return array
   */
  public function isPossibleToDeleteArticle($id) {
    $check_tables = array('anfrage','angebot','auftrag','rechnung','gutschrift','lieferschein','produktion','bestellung','retoure');
    $anzahl = 0;
    foreach($check_tables as $table)
    {
      $anzahl = (int)$this->app->DB->Select("SELECT id FROM ".$table."_position WHERE artikel='$id'");
      if($anzahl > 0) {
        return ['status'=>false, 'article_in'=>$table];
      }
    }

    $anzahl_stueckliste = $this->app->DB->Select("SELECT id FROM stueckliste WHERE artikel='$id'");
    if($anzahl_stueckliste > 0) {
      return ['status'=>false, 'article_in'=>'stueckliste'];
    }

    $abos = $this->app->DB->Select("SELECT COUNT(id) FROM abrechnungsartikel WHERE artikel='$id'");
    if($abos > 0) {
      return ['status'=>false, 'article_in'=>'abrechnungsartikel'];
    }

    return ['status'=>true];
  }

  public function deleteArticleById($id, $sperrenbeiLager = false)
  {
    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");

    $check_tables = array('anfrage','angebot','auftrag','rechnung','gutschrift','lieferschein','produktion','bestellung','retoure');
    $anzahl = 0;
    foreach($check_tables as $table)  {
      $anzahl += (int)$this->app->DB->Select("SELECT COUNT(id) FROM ".$table."_position WHERE artikel='$id'");
    }

    $anzahl_stueckliste = $this->app->DB->Select("SELECT SUM(menge) FROM stueckliste WHERE artikel='$id'");

    $abos = $this->app->DB->Select("SELECT COUNT(id) FROM abrechnungsartikel WHERE artikel='$id'");

    $anzahl += (int)$abos;
    $sperren = false;
    if($sperrenbeiLager){
      if(
        $this->app->DB->Select(sprintf('SELECT id FROM lager_platz_inhalt WHERE artikel = %d', $id))
        || $this->app->DB->Select(sprintf('SELECT id FROM lager_mindesthaltbarkeitsdatum WHERE artikel = %d', $id))
        || $this->app->DB->Select(sprintf('SELECT id FROM lager_charge WHERE artikel = %d', $id))
        || $this->app->DB->Select(sprintf('SELECT id FROM lager_seriennummern WHERE artikel = %d', $id))
        || $this->app->DB->Select(sprintf('SELECT id FROM mhd_log WHERE artikel = %d', $id))
        || $this->app->DB->Select(sprintf('SELECT id FROM chargen_log WHERE artikel = %d', $id))
      ) {
        $sperren = true;
      }
    }

    if($anzahl <=0 && $anzahl_stueckliste <=0) {
      if($sperren) {
        $this->app->DB->Update("UPDATE artikel SET intern_gesperrt = 1 WHERE id='$id'");
      }
      else{
        $this->app->DB->Update("UPDATE artikel SET geloescht='1', nummer='DEL' WHERE id='$id'");
        // Lager reseten
        $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE artikel='$id'");
        $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE artikel='$id'");
        $this->app->DB->Delete("DELETE FROM lager_charge WHERE artikel='$id'");
        $this->app->DB->Delete("DELETE FROM lager_bewegung WHERE artikel='$id'");
        $this->app->DB->Delete("DELETE FROM lager_mindesthaltbarkeitsdatum WHERE artikel='$id'");
        $this->app->DB->Delete("DELETE FROM einkaufspreise WHERE artikel='$id'");
        $this->app->DB->Delete("DELETE FROM verkaufspreise WHERE artikel='$id'");
        $this->app->DB->Delete("DELETE FROM artikelbaum_artikel WHERE artikel='$id'");
        $this->app->DB->Delete("DELETE FROM artikel_arbeitsanweisung WHERE artikel='$id'");
        $this->app->DB->Delete("DELETE FROM eigenschaften WHERE artikel='$id'");
        $this->app->DB->Delete(
          sprintf('DELETE FROM shopexport_artikel WHERE artikel = %d', $id)
        );
        $this->app->DB->Delete(
          sprintf('DELETE FROM shopexport_artikeluebertragen WHERE artikel = %d', $id)
        );

        //TODO vielleicht besser machen? mit Hinweis oder so
      }
      $this->app->DB->Update("UPDATE artikel SET variante=0,variante_von=0 WHERE variante_von='$id' AND variante_von > 0");

      $this->app->erp->RunHook('article_delete', 1, $id);

      return ['success'=>true, 'msg'=>["<div class=\"error\">Der Artikel \"$name_de\" und der Lagerbestand wurde gel&ouml;scht</div>"]];
    }
    $msg = [];
    if($anzahl > 0){

      $msg[] = "<div class=\"error\">Der Artikel \"$name_de\" ist Belegen eingebucht. Gefunden: $anzahl</div>";
    }

    if($anzahl_stueckliste > 0){
      $msg[] = "<div class=\"error\">Der Artikel \"$name_de\" ist in $anzahl_stueckliste St&uuml;ckliste(n) vorhanden.</div>";
    }

    return ['success'=>true, 'msg'=>$msg];
  }

  /**
   * @param null|int $id
   *
   * @return bool|void
   */
  public function ArtikelDelete($id = null)
  {
    $intern = false;
    if($id !== null) {
      $intern = true;
    } else{
      $id = $this->app->Secure->GetGET('id');
    }

    $ret = $this->deleteArticleById($id);
    if($intern) {
      return $ret;
    }
    $msg = '';
    foreach($ret['msg'] as $m) {
      $msg .= $this->app->erp->base64_url_encode($m);
    }
    $this->app->Location->execute('index.php?module=artikel&action=list&msg='.$msg);
  }

  public function ArtikelCreate()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'getkategorien')
    {
      $projekttmp = $this->app->Secure->GetPOST('projekt');
      if($projekttmp)
      {
        $projekttmp = explode(' ', $projekttmp);
        $projekttmp = reset($projekttmp);
        $projekttmp = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projekttmp' LIMIT 1");
      }
      $_artikelart = $this->app->erp->GetArtikelgruppe($projekttmp);
      foreach($_artikelart as $k => $v){
        echo '<option value="'.$k.'">'.$v.'</option>';
      }
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Add('JQUERYREADY','
    $(\'#projekt\').on("change",function(){
          $.ajax({
        url: \'index.php?module=artikel&action=create&cmd=getkategorien\',
        type: \'POST\',
        dataType: \'text\',
        data: {projekt:$("#projekt").val() },
        success: function(data) {
          $(\'select[name="typ"]\').html(data);
        }});
    });
    $(\'#projekt\').on("focusout",function(){
          $.ajax({
        url: \'index.php?module=artikel&action=create&cmd=getkategorien\',
        type: \'POST\',
        dataType: \'text\',
        data: {projekt:$("#projekt").val() },
        success: function(data) {
          $(\'select[name="typ"]\').html(data);
        }});
      
    });
    ');
    
    $this->app->Tpl->Set('UEBERSCHRIFT','Artikel (Neu anlegen)');
    $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=list';\">");
    //    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Artikel anlegen");
    $this->app->erp->MenuEintrag('index.php?module=artikel&action=list','Zur&uuml;ck zur &Uuml;bersicht');
    parent::ArtikelCreate();
  }

  function ArtikelListMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=artikel&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=artikel&action=create','Neuen Artikel anlegen');
  }

  protected function formatArticleList($articleListStr, $delimiter = ';')
  {
    $articleListStr = explode($delimiter, $articleListStr);
    foreach($articleListStr as $key => $value) {
      if((int)$value > 0) {
        $articleListStr[$key] = (int)$value;
      } else {
        unset($articleListStr[$key]);
      }
    }

    return array_unique($articleListStr);
  }

  public function ArtikelList()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'delete') {
      $ret = ['success' => 0];
      if($this->app->erp->RechteVorhanden('artikel', 'delete')) {
        $articleList = $this->formatArticleList($this->app->Secure->GetPOST('list'));
        if(!empty($articleList)) {
          foreach($articleList as $article) {
            $r = $this->deleteArticleById($article, true);
            if($r['success']){
              $ret['success'] = 1;
            } else {
              $ret['error'] = reset($r['msg']);
            }
          }
        } else {
          $ret['error'] = 'Es sind keine Artikel ausgewählt worden';
        }
      }else{
        $ret['error'] = 'Sie haben nicht die Rechte Artikel zu löschen';
      }
      
      echo json_encode($ret);
      $this->app->ExitXentral();
    }

    if($cmd === 'getshopuebertragung')
    {
      $liste = $this->formatArticleList($this->app->Secure->GetPOST('list'));
      $ret = array('html'=>'','success'=>0);
      if(!empty($liste))
      {
        $ret['articlelist'] = implode(';', $liste);
        foreach($liste as $v)
        {
          $this->app->erp->CheckShopTabelle($v);
        }
        $shops = $this->app->DB->SelectArr('SELECT s.id,s.bezeichnung FROM artikel_onlineshops AS ao
        INNER JOIN shopexport s ON ao.shop = s.id AND s.aktiv = 1 AND (s.lagerexport = 1 OR s.artikelexport = 1)
          WHERE ao.aktiv = 1 AND ao.artikel IN ('.implode(', ',$liste).')
          GROUP BY s.id, s.bezeichnung
        ');
        if(empty($shops))
        {
          $ret['error'] = 'Es sind keine aktiven Shops verknüpft';
        }else{
          $ret['success'] = 1;
          foreach($shops as $shop)
          {
            $this->app->Tpl->Add('LISTE','<tr><td>'.$shop['bezeichnung'].':</td><td><input type="checkbox" name="shop[]" value="'.$shop['id'].'" /></td></tr>');
          }
          $ret['html'] = $this->app->Tpl->Parse('', 'artikel_listonlineshops.tpl', 1);
        }
      }
      echo json_encode($ret);
      $this->app->ExitXentral();
    }
    if(($frmshops = $this->app->Secure->GetPOST('shop')) && ($articlelist =  $this->app->Secure->GetPOST('articlelist'))){
      $articlelist = explode(';',$articlelist);
      $articlelista = [];
      foreach($articlelist as $v)
      {
        if((int)$v > 0)
        {
          $articlelista[] = (int)$v;
        }
      }
      $allready = (int)$this->app->DB->Select('SELECT count(DISTINCT ao.shop,ao.artikel)
      FROM artikel_onlineshops AS ao
            INNER JOIN shopexport s ON ao.shop = s.id AND s.aktiv = 1 AND (s.lagerexport = 1 OR s.artikelexport = 1)
            INNER JOIN shopexport_artikeluebertragen AS sa ON ao.artikel = sa.artikel AND ao.shop = sa.shop
            WHERE  ao.aktiv = 1 AND ao.artikel IN ('.implode(', ',$articlelista).') AND s.id IN ('.implode(', ',$frmshops).')');

      $this->app->DB->Insert('INSERT INTO  `shopexport_artikeluebertragen` (shop, artikel)
          SELECT ao.shop,ao.artikel FROM artikel_onlineshops AS ao
            INNER JOIN shopexport s ON ao.shop = s.id AND s.aktiv = 1 AND (s.lagerexport = 1 OR s.artikelexport = 1)
            LEFT JOIN shopexport_artikeluebertragen AS sa ON ao.artikel = sa.artikel AND ao.shop = sa.shop
            WHERE ISNULL(sa.id) AND ao.aktiv = 1 AND ao.artikel IN ('.implode(', ',$articlelista).') AND s.id IN ('.implode(', ',$frmshops).')');
      $anz = $this->app->DB->affected_rows();
      $msg = $this->app->erp->base64_url_encode('<div class="info">Es wurden '.($anz > 0?$anz:'keine') .' Artikel zum &Uuml;bertragen &uuml;bergeben'.($allready > 0?' (Es sind bereits '. $allready.' Eintr&auml;ge vorhanden)':'').'.</div>');
      $this->app->Location->execute('index.php?module=artikel&action=list&msg='.$msg);
    }
    $this->ArtikelListMenu();
    $this->app->YUI->AutoComplete('projekt', 'projektname', 1);
    $this->app->YUI->AutoComplete('lieferantname', 'lieferant', 1);
    $this->app->YUI->AutoComplete('hersteller', 'hersteller');

    $freifeld1bezeichnung = $this->app->erp->Firmendaten('freifeld1');
    if($freifeld1bezeichnung == ''){
      $freifeld1bezeichnung = 'Freifeld 1';
    }
    $this->app->Tpl->Set('FREIFELD1BEZEICHNUNG',$freifeld1bezeichnung);
    $this->app->Tpl->Parse('TAB1','artikel_table_filter.tpl');

    $artikel_baum_uebersicht = $this->app->erp->Firmendaten('artikel_baum_uebersicht') && $this->app->DB->Select("SELECT id FROM artikelkategorien LIMIT 1") && $this->app->erp->RechteVorhanden('artikelbaum','baumajax') && $this->app->erp->RechteVorhanden('artikel','profisuche');
    if($artikel_baum_uebersicht)
    {
      $this->app->Tpl->Set('FMODULE', 'artikel');
      $this->app->Tpl->Set('ARTIKELBAUM','<table width="100%"><tr><td valign="top">'.$this->app->Tpl->Parse('return', 'artikel_artikelbaum.tpl',true).'</td><td valign="top">');
    }
    $this->app->User->SetParameter('filterbaum_artikel','');

    if($this->app->erp->Firmendaten('artikel_bilder_uebersicht')=='1')
    {
      $this->app->YUI->TableSearch('TAB1','artikeltabellebilder', 'show','','',basename(__FILE__), __CLASS__);
      
      $this->app->Tpl->Add('JSSCRIPTS',"
      <script>
      var ttimeouthandle = null;
      var tintervalhandle = null;
      function donextthumb()
      {
        if(tintervalhandle)clearInterval(tintervalhandle);
        if(ttimeouthandle)clearTimeout(ttimeouthandle);
        
        $('#artikeltabellebilder').find('img.tocheck').first().each(function()
        {
          var el = this;
          var aid = 0;
          var todo = false;
          var isrc = $(this).attr('src');
          var urla = isrc.split('?');
          var vorschau = '';
          if(typeof urla[1] != 'undefined')
          {
            var urlattr = urla[1].split('&');
            $.each(urlattr, function(k,v){
              var kv = v.split('=');
              if(kv[0] === 'bildvorschau')
              {
                if(typeof kv[1] != 'undefined')
                {
                  if(kv[1] == '')
                  {
                    vorschau = kv[1];
                    todo = true;
                  }
                }else{
                  todo = true;
                }
              }
              if(kv[0] === 'id')
              {
                if(typeof kv[1] != 'undefined')
                {
                  if(kv[1] != '')
                  {
                    aid = kv[1];
                  }
                }
              }
            });
          }
          
          if(todo && aid > 0)
          {
            $(this).toggleClass('tocheck',false);
            $.ajax({
            url: 'index.php?module=artikel&action=thumbnail&id='+aid,
            type: 'POST',
            dataType: 'json',
            data: {ajaxthumbnail:1, bildvorschau:vorschau}
            }).done( function(data) {
              if (typeof data == 'undefined' || data == null || typeof data.status == 'undefined' || data.status == 0)
              {
                //$('#artikeltabellebilder').after(aid+' fehler ');
                $(el).toggleClass('tocheck',false);
              } else {
                if(typeof data.reload != 'undefined' && data.reload == 1 && typeof data.bildvorschau != 'undefined')
                {
                  var zusatz = data.bildvorschau;
                  $(el).attr('src','index.php?module=artikel&action=thumbnail&id='+aid+'&bildvorschau='+ zusatz);
                  $(el).toggleClass('tocheck',false);
                  ttimeouthandle = setTimeout(function(){donextthumb();},750);
                  return;
                }else{
                  if(typeof data.bildvorschau != 'undefined')
                  {
                    var zusatz = data.bildvorschau;
                    $(el).attr('src','index.php?module=artikel&action=thumbnail&id='+aid+'&bildvorschau='+ zusatz);
                    $(el).toggleClass('tocheck',false);
                  }
                }
              }
            }).fail( function( jqXHR, textStatus ) {
              
              $(el).toggleClass('tocheck',false);
            });
            ttimeouthandle = setTimeout(function(){donextthumb();},1000);
            return;
          }else{
            
          }
        });
        tintervalhandle = setInterval(function(){donextthumb();},2000);
      }
      $(document).ready(function() {
        tintervalhandle = setInterval(function(){donextthumb();},1000);
      });
      </script>
      ");
      
      
    } else {
      $this->app->YUI->TableSearch('TAB1','artikeltabelle', 'show','','',basename(__FILE__), __CLASS__);
    }

    if($this->app->erp->Firmendaten('schnellsuche')){
      $this->app->YUI->EnterSearch('SCHNELLSUCHE', 'artikeltabelle');
    }
    $this->app->YUI->MassenbearbeitungsWidget('massenedit','artikel',array('width'=>'90%'));
    $this->app->Tpl->Parse('PAGE','artikeluebersicht.tpl');
  }

  function ArtikelgenEigenschaften(&$hw, &$produkte, $found = null, $lvl = 0)
  {
    if($produkte)
    {
      if($found === null)
      {
        foreach($produkte as $kp => $produkt)
        {
          $found[$produkt['id']] = true;
        }
      }
    }
    $keys = array_keys($hw);
    $html = '';
    $html .= '<table class="mkTable">';

    foreach($hw[$keys[$lvl]] as $k => $v)
    {
      $html .= '<tr><td>'.$k.'</td><td>';

      if($found !== null)
      {
        $where = 'and (0 ';
        foreach($found as $kf => $el)
        {
          if($el)$where .= " or artikel = ".$kf;
        }
        if($where !== 'and (0 ')
        {
          $where .= ')';
          $artikel = $this->app->DB->SelectArr("select artikel from eigenschaften where hauptkategorie = '".$keys[$lvl]."' and wert = '".$k."' ".$where);
          foreach($found as $kf => $el)
          {
            $foundnew[$kf] = false;
          }
          if($artikel)
          {

            foreach($artikel as $ka => $art)
            {
              $foundnew[$art['artikel']] = true;
            }
          }
        }

      }
      if($lvl == count($hw) -1)
      {
        if($found !== null)
        {
          foreach($foundnew as $kf => $gef)
          {
            if($gef)
            {
              foreach($produkte as $kp => $produkt)
              {
                if($kf == $produkt['id'])
                {
                  $html .= $produkt['nummer'];
                  break;
                }
              }
            }
          }
        }

      } elseif($lvl < count($hw) -1) {

        $html .= $this->ArtikelgenEigenschaften($hw, $produkte, $foundnew , $lvl +1);
      }
      $html .= '</td></tr>';
    }

    $html .= '</table>';
    return $html;
  }


  public function ArtikelMenu($id='')
  {
    if(!is_numeric($id)){
      $id = $this->app->Secure->GetGET('id');
    }

    //$action = $this->app->Secure->GetGET('action');

    if($id)
    {
      $tmp = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE id='$id' LIMIT 1");
      if($tmp)
      {
        $nummer = $tmp[0]['nummer'];
        $name_de = $tmp[0]['name_de'];

        $this->app->Tpl->SetText('KURZUEBERSCHRIFT2',$this->app->erp->LimitChar($name_de,100)." (Artikel $nummer)");
        $this->app->erp->MenuEintrag("index.php?module=artikel&action=edit&id=$id",'Details');

        $anzahldateien = $this->app->erp->AnzahlDateien("Artikel",$id);
        if($anzahldateien > 0) {
          $anzahldateien = ' ('.$anzahldateien.')';
        } else {
          $anzahldateien='';
        }

        $this->app->erp->MenuEintrag("index.php?module=artikel&action=dateien&id=$id",'Dateien'.$anzahldateien);

        if($tmp[0]['stueckliste']==1){
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=stueckliste&id=$id", 'St&uuml;ckliste');
        }


        if($tmp[0]['rohstoffe']==1){
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=rohstoffe&id=$id", 'Rohstoffe');
        }


        $rabatt = $this->app->DB->Select("SELECT rabatt FROM artikel WHERE id='$id' LIMIT 1");

        $this->app->erp->MenuEintrag("index.php?module=artikel&action=artikelfreifelder&id=$id", 'Freifelder');

        if($this->app->erp->Version()!=='stock' && $rabatt!='1'){
          $this->app->erp->MenuEintrag('index.php?module=artikel&action=eigenschaften&id='.$id, 'Eigenschaften');
        }

        if($rabatt!='1'){
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=einkauf&id=$id",'Einkauf');
          if($this->app->erp->RechteVorhanden('einkaufabgleich','einkaufapi'))
          {
            $this->app->erp->MenuEintrag("index.php?module=einkaufabgleich&action=einkaufapi&id=$id",'EK API');
          }
        }

        if($this->app->erp->Version()!='stock' && $rabatt!='1')
        {
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=verkauf&id=$id",'Verkauf');
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=statistik&id=$id",'Statistik');
        }
        if($tmp[0]['lagerartikel']=='1')
        {
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=lager&id=$id",'Lager');
        }


        if($tmp[0]['mindesthaltbarkeitsdatum']=='1' && $tmp[0]['chargenverwaltung']<=0)
        {
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=mindesthaltbarkeitsdatum&id=$id",'Mindesthalt.');
        }

        if($tmp[0]['mindesthaltbarkeitsdatum']=='1' && $tmp[0]['chargenverwaltung']>0)
        {
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=mindesthaltbarkeitsdatum&id=$id",'Mindesthalt. + Charge');
        }


        if($tmp[0]['chargenverwaltung']>0 && $tmp[0]['mindesthaltbarkeitsdatum']!='1')
        {
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=chargen&id=$id",'Chargen');
        }


        if($this->app->DB->Select("SELECT COUNT(id) FROM stueckliste WHERE artikel='$id' AND stuecklistevonartikel!='$id'") > 0){
          $this->app->erp->MenuEintrag("index.php?module=artikel&action=instueckliste&id=$id",'In St&uuml;ckliste');
        }


        $this->app->erp->MenuEintrag("index.php?module=artikel&action=etiketten&id=$id",'Etikett');

        $this->app->erp->MenuEintrag("index.php?module=artikel&action=offenebestellungen&id=$id",'Bestellungen');

        if($this->app->erp->Version()!=='stock')
        {
          if($this->app->erp->RechteVorhanden('auftrag','list')
            ||   $this->app->erp->RechteVorhanden('rechnung','list')
            ||   $this->app->erp->RechteVorhanden('gutschrift','list')
            ||   $this->app->erp->RechteVorhanden('angebot','list')
            ||   $this->app->erp->RechteVorhanden('lieferschein','list')
            ||   $this->app->erp->RechteVorhanden('produktion','list')
            ||   $this->app->erp->RechteVorhanden('bestellung','list')
          ){
            $this->app->erp->MenuEintrag("index.php?module=artikel&action=belege&id=$id",'Belege');
          }
        }

        $this->app->erp->MenuEintrag('index.php?module=artikel&action=list','Zur&uuml;ck zur &Uuml;bersicht');
        $this->app->erp->InsertMenuAfter("index.php?module=artikel&action=baum&id=$id",'Artikelbaum','artikel','eigenschaften');
      }
    }
    $this->app->erp->MenuEintrag('index.php?module=artikel&action=create','Neuen Artikel anlegen');
    $this->app->erp->RunMenuHook('artikel');
  }


  public function ArtikelEdit()
  {
    if(!$this->app->erp->RechteVorhanden('matrixprodukt','artikel'))
    {
      $this->app->Tpl->Set('VORMATRIX','<!--');
      $this->app->Tpl->Set('NACHMATRIX','-->');
    }
    if(!$this->app->erp->ModulVorhanden('tagespreise'))
    {
      $this->app->Tpl->Set('VORTAGESPREISE','<!--');
      $this->app->Tpl->Set('NACHTAGESPREISE','-->');
    }
    $id = $this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd');
    if($id || $cmd === 'getshopbuttons')
    {
      if($cmd === 'getonlineshop')
      {
        $sid = (int)$this->app->Secure->GetPOST('sid');
        $arr = null;
        if($sid){
          $arr = $this->app->DB->SelectRow("SELECT * FROM artikel_onlineshops WHERE id = '$sid' AND artikel = '$id' LIMIT 1");
        }
        
        $felder = array(
        'lagerkorrekturwert',
        'pseudolager',
        'autolagerlampe',
        'restmenge',
        'lieferzeitmanuell',
        'pseudopreis',
        'generierenummerbeioption',
        'variante_kopie',
        'unikat',
        'unikatbeikopie',
        'autoabgeleicherlaubt'
        );
        if($arr)
        {
          $arr['sid'] = $arr['id'];
          $arr['shop'] = $arr['shop']?($arr['shop'].' '.$this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id = '".$arr['shop']."' LIMIT 1")):'';
          echo json_encode($arr);
          $this->app->ExitXentral();
        }
        foreach($felder as $feld){
          $arr[$feld] = '';
        }
        $arr['artikel'] = $id;
        $arr['aktiv'] = 1;
        $arr['ausartikel'] = 1;
        echo json_encode($arr);
        $this->app->ExitXentral();
      }
      if($cmd === 'saveonlineshops')
      {
        $sid = $this->app->Secure->GetPOST('sid');
        $felder = array(
        'lagerkorrekturwert',
        'pseudolager',
        'autolagerlampe',
        'restmenge',
        'ausartikel',
        'lieferzeitmanuell',
        'pseudopreis',
        'generierenummerbeioption',
        'variante_kopie',
        'unikat',
        'unikatbeikopie',
        'autoabgeleicherlaubt',
          'aktiv'
        );
        $shop = explode(' ', $this->app->Secure->GetPOST('shop'));
        $shop = (int)reset($shop);
        if($this->app->DB->Select("SELECT id FROM artikel_onlineshops WHERE aktiv = 1 AND shop = '$shop' AND id <> '$sid' AND artikel = '$id' LIMIT 1"))
        {
          echo json_encode(array('status'=>0,'Error'=>'Es existiert bereits ein Eintrag mit diesem Shop'));
          $this->app->ExitXentral();
        }
        if($sid && !$this->app->DB->Select("SELECT id FROM artikel_onlineshops WHERE artikel = '$id' AND id = '$sid'"))
        {
          echo json_encode(array('status'=>0,'Error'=>'Der Eintrag wurde nicht gefunden oder passt nicht zum Artikel'));
          $this->app->ExitXentral();
        }
        if(!$sid)
        {
          $this->app->DB->Insert("INSERT INTO artikel_onlineshops (artikel,autolagerlampe,ausartikel) VALUES ('$id',0,1)");
          $sid = $this->app->DB->GetInsertID();
        }
        if($sid)
        {
          
          $this->app->DB->Update("UPDATE artikel_onlineshops SET shop = '$shop' WHERE id = '$sid' LIMIT 1");
          $inhalt = null;
          foreach($felder as $feld)
          {
            $inhalt[] = " $feld = '".$this->app->Secure->GetPOST($feld)."'";
          }
          $this->app->DB->Update('UPDATE artikel_onlineshops SET '.implode(', ', $inhalt)." WHERE id = '$sid' LIMIT 1");
          if(!$this->app->DB->error())
          {
            $shop = $this->app->DB->Select("SELECT shop FROM artikel_onlineshops WHERE artikel = '$id' AND id = '$sid' LIMIT 1");
            if($this->app->DB->Select("SELECT id FROM artikel_onlineshops WHERE artikel = '$id' AND shop = '$shop' AND aktiv = 1 LIMIT 1")){
              $shop = null;
            }
            if($shop)
            {
              $this->app->DB->Update("UPDATE artikel SET shop = 0 WHERE id = '$id' AND shop = '$shop' LIMIT 1");
              $this->app->DB->Update("UPDATE artikel SET shop2 = 0 WHERE id = '$id' AND shop2 = '$shop' LIMIT 1");
              $this->app->DB->Update("UPDATE artikel SET shop3 = 0 WHERE id = '$id' AND shop3 = '$shop' LIMIT 1");
            }
          }
          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }
        echo json_encode(array('status'=>0,'Error'=>'Der Eintrag konnte nicht angelegt werden'));
        $this->app->ExitXentral();
      }
      if($cmd === 'deleteonlineshop')
      {
        $sid = $this->app->Secure->GetPOST("sid");
        $shop = $this->app->DB->Select("SELECT shop FROM artikel_onlineshops WHERE artikel = '$id' AND id = '$sid' LIMIT 1");
        if($shop)
        {
          $this->app->DB->Update("UPDATE artikel SET shop = 0 WHERE id = '$id' AND shop = '$shop' LIMIT 1");
          $this->app->DB->Update("UPDATE artikel SET shop2 = 0 WHERE id = '$id' AND shop2 = '$shop' LIMIT 1");
          $this->app->DB->Update("UPDATE artikel SET shop3 = 0 WHERE id = '$id' AND shop3 = '$shop' LIMIT 1");
        }
        $this->app->DB->Delete("DELETE FROM artikel_onlineshops WHERE artikel = '$id' AND id = '$sid' LIMIT 1");
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
      if($cmd === 'getshopbuttons')
      {
        $ids = explode(',',$this->app->Secure->GetPOST('ids'));
        $firstid = (int)$this->app->Secure->GetPOST('firstid');
        $html = '';
        if(!empty($ids))
        {
          $wherea = [];
          foreach($ids as $sid)
          {
            $sid = (int)$sid;
            if($sid > 0)
            {
              $wherea[] = $sid;
            }
          }
          if(!empty($wherea))
          {
            $shops = $this->app->DB->SelectArr("SELECT ao.* FROM artikel_onlineshops ao INNER JOIN shopexport s ON ao.shop = s.id 
            WHERE ao.id IN (".implode(',', $wherea).") AND s.shoptyp = 'intern' AND s.modulename != '' ORDER BY s.shoptyp");
            if(!empty($shops))
            {
              foreach($shops as $shop)
              {
                $buttontarget = 'SHOPTABELLE';
                $nr = 'ONLINESHOPBUTTON'.$shop['id'];
                $this->app->erp->RunHook('artikel_shopbutton',4, $nr, $shop['shop'], $shop['artikel'],$buttontarget);
              }
              if(!empty($this->app->Tpl->VARARRAY['SHOPTABELLE'])){
                $html = $this->app->Tpl->VARARRAY['SHOPTABELLE'];
              }
            }
          }
        }
        $return = array('html'=>$html);
        if($firstid > 0 && $artikel = $this->app->DB->Select("SELECT artikel FROM artikel_onlineshops WHERE id = ".$firstid))
        {
          $shops = $this->app->DB->SelectArr("SELECT ao.id, s.shoptyp, s.modulename FROM artikel_onlineshops AS ao INNER JOIN shopexport AS s ON ao.shop = s.id  WHERE ao.artikel = $artikel ORDER BY s.modulename");
          if(!empty($shops))
          {
            $canimport = 0;
            $canexport = 0;
            foreach ($shops as $shop)
            {
              if(empty($shop['modulename']) || $shop['shoptyp'] !== 'intern')
              {
                $return['canimport'][] = $shop['id'];
                $canimport++;
                $return['canexport'][] = $shop['id'];
                $canexport++;
              }else{
                $obj = $this->app->erp->LoadModul($shop['modulename']);
                if(empty($obj) || !method_exists($obj,'importerCanImport') || $obj->importerCanImport())
                {
                  $return['canimport'][] = $shop['id'];
                  $canimport++;
                }
                if(empty($obj) || !method_exists($obj,'importerCanExport') || $obj->importerCanExport())
                {
                  $return['canexport'][] = $shop['id'];
                  $canexport++;
                }
              }
            }
            if($canimport === 0 || $canimport === count($shops))
            {
              $return['hideallimportplaceholder'];
            }
            if($canexport === 0 || $canexport === count($shops))
            {
              $return['hideallexportplaceholder'];
            }
          }
        }

        echo json_encode($return);
        $this->app->ExitXentral();
      }
      $this->app->YUI->Autocomplete('onlinshopspopup_shop','shopnameid');
      //$this->app->YUI->HideFormular('onlinshopspopup_ausartikel', array('unchecked'=>'dummy','checked'=>'onlineshopeditdis'));
      
      
      $this->app->Tpl->Set('NEUERONLINESHOPBUTTON','<input type="button" class="btnGreen" value="{|Neuer Eintrag|}" onclick="editonlineshop(0);" />');
      $this->app->erp->CheckShopTabelle($id);
    }
    
    $this->app->YUI->TableSearch('SHOPTABELLE','artikel_onlineshops', 'show','','',basename(__FILE__), __CLASS__);
    
    $shop1export =$this->app->Secure->GetPOST('shop1export');
    $shop2export =$this->app->Secure->GetPOST('shop2export');
    $shop3export =$this->app->Secure->GetPOST('shop3export');

    if($this->app->erp->Version()==='stock')
    {
      $this->app->DB->Update("UPDATE artikel SET lagerartikel=1 WHERE id='$id' LIMIT 1");
    }
    // 18.05 heute entfernt
    //$this->app->DB->Update("UPDATE artikel SET herkunftsland='".$this->app->erp->Firmendaten('land')."' WHERE id='$id' AND herkunftsland='' LIMIT 1");

    $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$id' LIMIT 1");

    if(strpos($nummer, ' ') !== false)
    {
      $nummer = $this->app->DB->real_escape_string(str_replace(' ','', trim($nummer)));
      if($nummer=='') {
        $artikelart = $this->app->DB->Select("SELECT projekt,typ FROM artikel WHERE id='$id'");
        $nummer=$this->app->erp->GetNextArtikelnummer($artikelart[0]['typ'],$this->app->User->GetFirma(),$artikelart[0]['projekt']);
      }
      $this->app->DB->Update("UPDATE artikel SET nummer = '$nummer' WHERE id = '$id' LIMIT 1");
    }
    
    $this->app->erp->CheckArtikel($id);

    if($shop1export!=''){
      $this->app->User->SetParameter('artikel_shopexport_shop1', 1);
    }

    if($shop2export!=''){
      $this->app->User->SetParameter('artikel_shopexport_shop2', 1);
    }

    if($shop3export!=''){
      $this->app->User->SetParameter('artikel_shopexport_shop3', 1);
    }

    $shop1import =$this->app->Secure->GetPOST('shop1import');
    if($shop1import!='')
    {
      $this->app->Location->execute("index.php?module=artikel&action=shopimport&id=$id&shop=1");
      return;
    }
    $shop2import =$this->app->Secure->GetPOST('shop2import');
    if($shop2import!='')
    {
      $this->app->Location->execute("index.php?module=artikel&action=shopimport&id=$id&shop=2");
      return;
    }
    $shop3import =$this->app->Secure->GetPOST('shop3import');
    if($shop3import!='')
    {
      $this->app->Location->execute("index.php?module=artikel&action=shopimport&id=$id&shop=3");
      return;
    }
    
    $POST = $this->app->Secure->POST;
    
    if($POST && is_array($POST))
    {
      foreach($POST as $key => $value)
      {
        if(!empty($value)){
          if(strpos($key, 'shopimport') === 0){
            $shopid = (int)substr($key, 11);
            if($shopid){
              $this->app->Location->execute("index.php?module=artikel&action=shopimport&id=$id&artikelshopid=" . $shopid);
              return;
            }
          }
          if(strpos($key, 'shopexport') === 0){
            $shopid = (int)substr($key, 11);
            if($shopid){
              $this->app->User->SetParameter('artikel_shopexport_shop', $shopid);
            }
          }
        }
      }
    }

    if($this->app->erp->Version()==='stock')
    {
      $this->app->Tpl->Set('DISABLEOPENTEXTE','<!--');
      $this->app->Tpl->Set('DISABLECLOSETEXTE','-->');
      $this->app->Tpl->Set('DISABLEOPENSHOP','<!--');
      $this->app->Tpl->Set('DISABLECLOSESHOP','-->');
      $this->app->Tpl->Set('DISABLEOPENSTOCK','<!--');
      $this->app->Tpl->Set('DISABLECLOSESTOCK','-->');
    }

    if($this->app->erp->DisableModul('artikel',$id))
    {
      $this->ArtikelMenu();
      return;
    }   // Einzelposten im gleichen LagerRegal zusammenführen


    $this->app->YUI->SaveReally();
    $this->app->erp->LagerArtikelZusammenfassen($id);

    $nummer = $this->app->Secure->GetGET('nummer'); 
    if(!is_numeric($id) && $nummer!='')
    {
      $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$nummer."' LIMIT 1");
      $this->app->Location->execute("index.php?module=artikel&action=edit&id=$id");
      return;
    }

    $mark = $this->app->Secure->GetPOST('bookmark');
    if($mark!='' && !in_array($id, $_SESSION['bookmarked'])) {
      $_SESSION['bookmarked'][] = $id; 
    }

    $articleArr = $this->app->DB->SelectRow(
      sprintf(
        "SELECT juststueckliste, lagerartikel,name_de, kurztext_de,anabregs_text 
        FROM artikel 
        WHERE id=%d 
        LIMIT 1",
        $id
      )
    );
    $juststueckliste = $articleArr['juststueckliste'];
    $lagerartikel = $articleArr['lagerartikel'];

    $shops = $this->app->DB->SelectArr("SELECT * FROM artikel_onlineshops WHERE artikel = '$id' ORDER BY aktiv DESC, shop");
    if($shops)
    {
      $exshops = null;
      foreach($shops as $shop)
      {
        if($shop['shop'] && !isset($exshops[$shop['shop']]))
        {
          $buttontarget = 'SHOPTABELLE';
          $nr = 'ONLINESHOPBUTTON'.$shop['id'];
          $this->app->erp->RunHook('artikel_shopbutton',4, $nr, $shop['shop'], $id,$buttontarget);
        }
      }
      
      $this->app->Tpl->Parse('SHOPTABELLE','artikel_onlineshopbuttons.tpl');
      
    }
    $this->app->Tpl->Add('SHOPTABELLE','<span id="shoptabelleafter"></span>');
    
    //$shop= $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$id' LIMIT 1");

    $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=list';\">");

    if($lagerartikel=='1' && $juststueckliste=='1')
    {
      $this->app->Tpl->Add('MESSAGE','<div class="error">Dieser Artikel ist als Lagerartikel und <i>Explodiert im Auftrag</i> markiert. Bitte nur eine Option w&auml;hlen!</div>');
    }

    $artikel_de_anzeige = $articleArr['name_de'];
    $kurztext_de_anzeige = $articleArr['kurztext_de'];
    $artikelbeschreibung_de_anzeige = $articleArr['anabregs_text'];

    $this->app->YUI->CkEditor('artikelbeschreibung_de_anzeige','belege');

    $this->app->Tpl->Set('ARTIKEL_DE_ANZEIGE','<input type="text" name="" readonly style="background-color:#eee; border-color:#ddd;" size="70" maxlength="60" value="'.$artikel_de_anzeige.'">');
    $this->app->Tpl->Set('KURZTEXT_DE_ANZEIGE','<textarea readonly rows="2" cols="70" readonly style="background-color:#eee; border-color:#ddd;">'.$kurztext_de_anzeige.'</textarea>');
    $this->app->Tpl->Set('ARTIKELBESCHREIBUNG_DE_ANZEIGE','<textarea style="background-color:#eee; border-color:#ddd;" readonly rows="5" cols="70" style="color:grey" name="artikelbeschreibung_de_anzeige" id="artikelbeschreibung_de_anzeige">'.$artikelbeschreibung_de_anzeige.'</textarea>');

    parent::ArtikelEdit();

    /* anzeige formular */ 
    $this->ArtikelMenu();
    $artikel = $this->app->DB->Select("SELECT CONCAT(name_de,' (',nummer,')') FROM artikel WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('UEBERSCHRIFT','Artikel: '.$artikel);

    $shop1export = $this->app->User->GetParameter('artikel_shopexport_shop1');
    $shop2export = $this->app->User->GetParameter('artikel_shopexport_shop2');
    $shop3export = $this->app->User->GetParameter('artikel_shopexport_shop3');
    $artikel_shopexport_shop = (int)$this->app->User->GetParameter('artikel_shopexport_shop');
    if($artikel_shopexport_shop > 0)
    {
      $this->app->User->SetParameter('artikel_shopexport_shop','');
      $this->app->Location->execute("index.php?module=artikel&action=shopexport&id=$id&artikelshopid=".$artikel_shopexport_shop);
      return;
    }
    
    if($shop1export!='')
    {
      $this->app->User->SetParameter('artikel_shopexport_shop1','');
      $this->app->Location->execute("index.php?module=artikel&action=shopexport&id=$id&shop=1");
      return;
    }

    if($shop2export!='')
    {
      $this->app->User->SetParameter('artikel_shopexport_shop2','');
      $this->app->Location->execute("index.php?module=artikel&action=shopexport&id=$id&shop=2");
      return;
    }

    if($shop3export!='')
    {
      $this->app->User->SetParameter('artikel_shopexport_shop3','');
      $this->app->Location->execute("index.php?module=artikel&action=shopexport&id=$id&shop=3");
      return;
    }	

    $this->app->erp->MessageHandlerStandardForm();

    /* sperrmeldung */
    $intern_gesperrt = $this->app->DB->Select("SELECT intern_gesperrt FROM artikel WHERE id='$id' LIMIT 1");
    if($intern_gesperrt)
    {
      if($this->app->erp->CheckSamePage())
      {
        $intern_gesperrtgrund = $this->app->DB->Select("SELECT intern_gesperrtgrund FROM artikel WHERE id='$id' LIMIT 1");
        if($intern_gesperrtgrund=='') {
          $intern_gesperrtgrund='Artikel gesperrt';
        }
        $this->app->erp->SeitenSperrAuswahl('Wichtiger Hinweis',$intern_gesperrtgrund);
      }
    }

    $this->app->YUI->AutoComplete('herkunftsland', 'laender', 1);

  }

  public function ArtikelEtiketten()
  {
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Etiketten)');
    $id = (int)$this->app->Secure->GetGET('id');
    $external= $this->app->Secure->GetGET('external');
    $menge = $this->app->Secure->GetPOST('menge');
    $mhd = $this->app->Secure->GetPOST('mhd');
    $charge = $this->app->Secure->GetPOST('charge');
    $speichern = $this->app->Secure->GetPOST('speichern');
    $seriennummer = $this->app->Secure->GetPOST('seriennummer');
    $etikettenauswahl = $this->app->Secure->GetPOST('etikettenauswahl');
    $etikettendrucker = $this->app->Secure->GetPOST('etikettendrucker');
    $this->ArtikelMenu();

    if($speichern!='')
    {
      $formetikettautodruck = $this->app->Secure->GetPOST('etikettautodruck');
      if($formetikettautodruck) {
        $this->app->DB->Update("UPDATE artikel SET etikettautodruck='1' WHERE id='$id' LIMIT 1");
      }
      else {
        $this->app->DB->Update("UPDATE artikel SET etikettautodruck='0' WHERE id='$id' LIMIT 1");
      }
      $formautodrucketikett = $this->app->Secure->GetPOST('autodrucketikett');
      $this->app->DB->Update("UPDATE artikel SET autodrucketikett='$formautodrucketikett' WHERE id='$id' LIMIT 1");
    }

    $etikettautodruck = $this->app->DB->Select("SELECT etikettautodruck FROM artikel WHERE id='$id' LIMIT 1");
    if($etikettautodruck) {
      $this->app->Tpl->Set('ETIKETTAUTODRUCK','checked');
    }

    $autodrucketikett = $this->app->DB->Select("SELECT autodrucketikett FROM artikel WHERE id='$id' LIMIT 1");
    $etiketten_tmp = $this->app->DB->SelectArr("SELECT * FROM etiketten WHERE verwendenals='artikel_klein' ORDER by name");
    $autodrucketiketttpl = '<option value="0">-</option>';
    $cetiketten_tmp = $etiketten_tmp?count($etiketten_tmp):0;
    for($i=0;$i<$cetiketten_tmp;$i++)
    {
      if($etiketten_tmp[$i]['id']==$autodrucketikett) {
        $mark='selected';
      } else {
        $mark='';
      }
      $autodrucketiketttpl .="<option value=\"{$etiketten_tmp[$i]['id']}\" $mark>{$etiketten_tmp[$i]['name']}</option>";
    }
    $this->app->Tpl->Set('AUTODRUCKETIKETT',$autodrucketiketttpl);

    $etiketten = $this->app->erp->GetSelectEtiketten('artikel_klein',$etikettenauswahl);
    if($etiketten=='') {
      $etiketten='<option>Standard</option>';
    }

    $drucker = $this->app->erp->GetSelectEtikettenDrucker($etikettendrucker); 

    $this->app->Tpl->Set('FORMULAR',"<form action=\"\" method=\"post\"><table class=\"mkTableFormular\">
      <tr><td>Menge:</td><td><input type=\"text\" name=\"menge\" value=\"1\">&nbsp;<input type=\"submit\" value=\"Drucken\" class=\"btnBlue\"></td></tr>
      <tr><td>Etikett:</td><td><select name=\"etikettenauswahl\">".$etiketten."</select></td></tr>
      <tr><td>Drucker:</td><td><select name=\"etikettendrucker\">".$drucker."</select></td></tr>");
    $mhdartikel = $this->app->DB->Select("SELECT mindesthaltbarkeitsdatum FROM artikel WHERE id = '$id' LIMIT 1");
    if($mhdartikel)
    {
      $this->app->Tpl->Add('FORMULAR',"<tr><td>Mindeshaltbarkeitsdatum:</td><td><input type=\"text\" id=\"mhd\" name=\"mhd\" value=\"\"></td></tr>");
      $this->app->YUI->DatePicker('mhd');
      $this->app->YUI->AutoComplete('mhd', 'lagermindesthaltbarkeitsdatum',0,"&artikel=$id");
    }
    $chargenverwaltung = $this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id = '$id' LIMIT 1");
    if($chargenverwaltung)
    {
      $this->app->Tpl->Add('FORMULAR',"<tr><td>Charge:</td><td><input type=\"text\" id=\"charge\" name=\"charge\" value=\"\" size=\"40\"></td></tr>");
      $this->app->YUI->AutoComplete('charge', 'lagercharge',0,"&artikel=$id");
    }

    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id = '$id' LIMIT 1");
    if($seriennummern==='eigene' || $seriennummern==='vomprodukt' || $seriennummern==='vomprodukteinlagern')
    {
      $this->app->Tpl->Add('FORMULAR',"<tr><td>Seriennummer:</td><td><input type=\"text\" id=\"seriennummer\" name=\"seriennummer\" value=\"\" size=\"40\"></td></tr>");
      $this->app->YUI->AutoComplete('seriennummer', 'lagerseriennummern',0,"&artikel=$id");
    }    
    $this->app->Tpl->Add('FORMULAR',"</table></form><br><br>");

    $standardbild = $this->app->erp->GetEtikettenbild($id,true);

    if($standardbild==''){
      $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$id' LIMIT 1");
    }

    if($standardbild > 0){
      //$this->app->Tpl->Add('BILD', "<img src=\"index.php?module=dateien&action=send&id=$standardbild\" width=\"200\">");
      $this->app->Tpl->Set('BILD',
        '<img alt="Artikelbild" src="index.php?module=artikel&action=thumbnail&id='.$id.'&fileid='.$standardbild.'&size=200&direkt=1" align="left" width="200" style="margin-right:10px; margin-bottom:10px;" />'
      );
    }

    if($external=='1')
    {
      $menge = $this->app->Secure->GetGET('menge');
    }    


    if($menge!='')
    {
      //$nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
      //$projekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$id' LIMIT 1");
      $name_de = $this->app->erp->UmlauteEntfernen($this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1"));
      //$name_de_base64 = $this->app->erp->base64_url_encode($name_de);

      if(!is_numeric($etikettenauswahl)) {
        $etikettenauswahl = 'artikel_klein';
      }

      $variablen = null;
      if($mhd){
        $variablen['mhd'] = $this->app->String->Convert($mhd,'%3.%2.%1','%1-%2-%3');
        $variablen['mhd2'] = $mhd;
        $variablen['mhd3'] = date('ymd',strtotime($mhd));
      }
      if($charge){
        $variablen['charge'] = $charge;
      }
      if($seriennummer){
        $variablen['seriennummer'] = $seriennummer;
      }
      $this->app->erp->EtikettenDrucker($etikettenauswahl,$menge,'artikel',$id,$variablen,'',$etikettendrucker);
    }

    if($external=='1')
    { 
      $this->app->Location->execute($_SERVER['HTTP_REFERER']);
    }

    $cmd = $this->app->Secure->GetGET('cmd');

    if($cmd === 'get'){
      $id = (int)$this->app->Secure->GetPOST('id');

      $data = $this->app->DB->SelectRow("SELECT al.id, e.id as label, al.type, al.amount, d.id as printer
                                         FROM article_label al 
                                         LEFT JOIN etiketten e ON al.label_id = e.id
                                         LEFT JOIN drucker d ON al.printer_id = d.id
                                         WHERE al.id = '$id' LIMIT 1");

      if(empty($data)) {
        $data['id'] = 0;
        $data['labelname'] = '';
        $data['type'] = '';
        $data['amount'] = '';
        $data['printername'] = 1;

      }
      echo json_encode($data);
      $this->app->ExitXentral();
    }
    elseif($cmd === 'save'){
      $id = (int)$this->app->Secure->GetPOST('id');
      $articleId = $this->app->Secure->GetPOST('article');
      $label = trim($this->app->Secure->GetPOST('label'));
      $type = trim($this->app->Secure->GetPOST('type'));
      $amount = trim($this->app->Secure->GetPOST('amount'));
      $printer = $this->app->Secure->GetPOST('printer');

      $error = "";

      if($label == ""){
        $error .= "Bitte ein Etikett auswählen\n";
      }else{
        $labelId = $this->app->DB->Select("SELECT id FROM etiketten WHERE id = '$label' LIMIT 1");
        if($labelId <= 0 || $labelId == ''){
          $error .= "Bitte gültiges Etikett auswählen\n";
        }
      }

      if($type == ""){
        $error .= "Bitte Art auswählen"."\n";
      }

      if($amount == "" || $amount <= 0){
        $error .= "Mindestmenge ist 1"."\n";
      }else{
        $amount = (int)$amount;
      }

      //PFLICHTFELD?
      if($printer == ""){
        $error .= "Bitte einen Drucker auswählen"."\n";
      }else{
        $printerId = $this->app->DB->Select("SELECT id FROM drucker WHERE id = '$printer' LIMIT 1");
        if($printerId <= 0 || $printerId == ''){
          $error .= "Bitte gültigen Drucker auswählen\n";
        }
      }

      $labelAlreadyExists = $this->app->DB->Select("SELECT id 
                                                    FROM article_label 
                                                    WHERE label_id = '$labelId' AND type = '$type' AND article_id = '$articleId' 
                                                          AND id != '$id' LIMIT 1");
      if($labelAlreadyExists != '' && $labelAlreadyExists > 0){
        $error .= "Dieses Label gibt es bereits für diesen Artikel mit dieser Art";
      }

      if($error == ""){
        if($id){
          $this->app->DB->Update("UPDATE article_label SET label_id = '$labelId', type = '$type', amount = '$amount', printer_id = '$printerId' WHERE id = '$id'");

          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }else{
          $this->app->DB->Insert("INSERT INTO article_label (article_id, label_id, type, amount, printer_id) 
                                  VALUES ('$articleId', '$labelId', '$type', '$amount', '$printerId')");

          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }
      }else{
        echo json_encode(array('status'=>0,'statusText'=>$error));
        $this->app->ExitXentral();
      }
    }elseif($cmd == 'delete'){
      $id = (int) $this->app->Secure->GetPOST('id');
      if($id)
        $this->app->DB->Update("DELETE FROM article_label WHERE id = '$id'");

      echo json_encode(array('status'=>1));
      $this->app->ExitXentral();
    }

    $articleLabels = $this->app->erp->GetSelectEtiketten('artikel_klein',$etikettenauswahl);
    $this->app->Tpl->Set("ARTICLELABELS", $articleLabels);

    $articleLabelPrinter = $this->app->DB->SelectArr("SELECT id, name FROM drucker WHERE aktiv='1' AND art='2'");
    $articleLabelPrinterSelection = "";
    for($i=0;$i<count($articleLabelPrinter);$i++)
    {
      $articleLabelPrinterSelection .="<option value='".$articleLabelPrinter[$i]['id']."'>".$articleLabelPrinter[$i]['name']."</option>";
    }
    $this->app->Tpl->Set("ARTICLELABELPRINTER", $articleLabelPrinterSelection);

    $this->app->YUI->TableSearch('TAB1','artikel_etiketten', "show","","",basename(__FILE__), __CLASS__);


    $this->app->Tpl->Parse('PAGE','artikel_etiketten.tpl');
  }

  public function ArtikelOnlineShop()
  {
    $id = $this->app->Secure->GetGET('id');

    // neue warengruppe hinzugefuegt
    $artikelgruppe = $this->app->Secure->GetPOST('artikelgruppe');
    $ok= $this->app->Secure->GetPOST('ok');
    if($artikelgruppe!='' && $ok=='') {
      $this->app->DB->Insert("INSERT INTO artikel_artikelgruppe (id,artikel,artikelgruppe) VALUES ('','$id','$artikelgruppe')");
    }
    //warengruppe geloescht
    $sid= $this->app->Secure->GetGET('sid');
    $cmd= $this->app->Secure->GetGET('cmd');
    if($sid!='' && $cmd==='del') {
      $this->app->DB->DELETE("DELETE FROM artikel_artikelgruppe WHERE id='$sid' LIMIT 1");
    }
    if($sid!='' && $cmd==='image') {
      $this->app->DB->DELETE("UPDATE artikel SET standardbild='$sid' WHERE id='$id' LIMIT 1");
    }

    $name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
    $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('SUBSUBHEADING',"Online-Shop Attribute: $name ($nummer)");
    $this->app->Tpl->Set('AKTIV_TAB1','selected');

    //Warengruppen
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT a.bezeichnung, aa.id FROM artikel_artikelgruppe aa LEFT JOIN artikelgruppen a ON a.id=aa.artikelgruppe WHERE artikel='$id'");
    $tmp->DisplayNew('WARENGRUPPEN',"<a href=\"#\" onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=artikel&cmd=del&action=onlineshop&id=$id&sid=%value%';\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>");

    $shop = $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$id' LIMIT 1");

    $arr = $this->app->DB->SelectArr("SELECT bezeichnung,id FROM artikelgruppen WHERE shop='$shop'");
    $html = '';
    if(!empty($arr)){
      foreach ($arr as $key => $value) {
        $html .= "<option value=\"{$value['id']}\">{$value['bezeichnung']}</option>";
      }
    }

    $this->app->Tpl->Add('WARENGRUPPEN',"<center><select name=\"artikelgruppe\">$html</select>");
    $this->app->Tpl->Add('WARENGRUPPEN',"<input type=submit value=\"hinzuf&uuml;gen\"></center>");

    // standard bild
    $standardbild = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='$id'");
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT d.titel, d.id FROM datei d LEFT JOIN datei_stichwoerter s ON d.id=s.datei  
        LEFT JOIN datei_version v ON v.datei=d.id
        WHERE s.objekt='Artikel' AND s.parameter='$id' AND s.subjekt='Shopbild' AND d.geloescht=0");

    $tmp->DisplayNew('HAUPTBILD',
        "<a href=\"#\" onclick=\"if(!confirm('Als Standard definieren?')) return false; else window.location.href='index.php?module=artikel&action=onlineshop&cmd=image&id=$id&sid=%value%';\"><img src=\"./themes/[THEME]/images/ack.png\" border=\"0\"></a>");

    $standardbild_name = $this->app->DB->Select("SELECT titel FROM datei WHERE id='$standardbild'");
    $this->app->Tpl->Add('HAUPTBILD',"<br>Standardbild: <b>$standardbild_name</b>");

    $this->app->Tpl->Parse('PAGE','onlineshop.tpl');

    $this->app->BuildNavigation=false;
  }

  function ArtikelNewList()
  {
    $this->app->Tpl->Parse('PAGE','datatable.tpl');
  }

  function ArtikelStuecklisteUpload()
  {

    $this->app->Tpl->Set('TAB1','

        <table><tr><td>Datei:</td><td><input type="file"></td></tr></table>');        
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  function ArtikelStuecklisteImport($parsetarget='')
  {
    $id = $this->app->Secure->GetGET('id');
    //$this->app->BuildNavigation=false;

    $vorlage = $this->app->Secure->GetPOST('vorlage');
    if($vorlage==='altium'){
      $result = $this->StuecklisteImport(
          array('menge'=>'Menge','nummer'=>'Artikelnummer','wert'=>'Wert','bauform'=>'Package','referenz'=>'Referenz'),
          array('menge'=>2,'nummer'=>13,'bauform'=>5,'wert'=>6,'referenz'=>3),
          ';',$parsetarget);

    } 
    else if($vorlage==='solidedgest2'){
      $result = $this->StuecklisteImport(
          array('nummer'=>'Artikelnummer','menge'=>'Menge'),
          array('nummer'=>2,'menge'=>5),
          ';',$parsetarget);
    } 
    else if($vorlage==='minimal'){
      $result = $this->StuecklisteImport(
          array('nummer'=>'Artikelnummer','menge'=>'Menge'),
          array('nummer'=>1,'menge'=>2),
          ';',$parsetarget);
    } 
    else {
      $result = $this->StuecklisteImport(
          array('nummer'=>'Artikelnummer','menge'=>'Menge'),
          array('nummer'=>1,'menge'=>2),
          ';',$parsetarget);
    }

    if(is_array($result))
    {
      $fehlerhaftes_bauteil = '';
      $this->app->DB->Delete("DELETE FROM stueckliste WHERE stuecklistevonartikel='$id'");
      foreach($result as $key=>$value)
      {
        $value['menge'] = str_replace(',','.',$value['menge']);
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$value['nummer']."' AND nummer!='' LIMIT 1");
        $maxsort = $this->app->DB->Select("SELECT MAX(sort) FROM stueckliste WHERE stuecklistevonartikel='".$id."'") + 1;
        if($artikelid > 0) 
        {
          if(!$this->app->erp->IstStuecklistenZirkel($artikelid, $id))
          {
            $this->app->DB->Insert("INSERT INTO stueckliste 
              (id,sort,artikel,menge,wert,bauform,referenz,stuecklistevonartikel,firma) VALUE ('','$maxsort','$artikelid','".$value['menge']."',
            '".$value['wert']."','".$value['bauform']."','".$value['referenz']."','$id','".$this->app->User->GetFirma()."')");
          }else{
            $fehlerhaftes_bauteil .= 'St&uuml;ckliste enth&auml;lt Artikel die einen Zirkelbezug verursachen!<br>';
          }
        }else {
          if($value['nummer']!=''){
            $fehlerhaftes_bauteil .= 'Unbekannte Artikelnummer: ' . $value['nummer'] . ' (Menge ' . $value['menge'] . ' St&uuml;ck)<br>';
          }
        } 
      }
      if($fehlerhaftes_bauteil!='')
      {
        $this->app->Tpl->Set($parsetarget,"<div class=\"error\">$fehlerhaftes_bauteil</div>");
      }
    }
  }

  function StuecklisteImport($fields, $preselected="",$startdelimititer=";",$parsetarget)
  {

    $stueckliste_csv = $this->app->erp->GetTMP().'stueckliste'.$this->app->User->GetID();

    $quote = htmlentities($this->app->Secure->GetPOST('quote'));
    $delimiter = htmlentities($this->app->Secure->GetPOST('delimiter'));
    $cancel = $this->app->Secure->GetPOST('cancel');

    if($cancel!='')
    {
      unlink($stueckliste_csv);
      $this->app->User->SetParameter("artikel_stueckliste_importfilename","");
    }

    $import = $this->app->Secure->GetPOST('import');
    if($import!='')
    {
      $findcols = '';
      $row_post = $this->app->Secure->GetPOST('row');
      $cols = $this->app->Secure->GetPOST('cols');

      $importerror=0;
      if($row_post=='')
      {
        $findcols .= '<div class="error">Zeile wählen</div>';
        $importerror++;
      } 
      $ccols = !empty($cols)?count($cols):0;
      $colcounter = 0;
      for($i=0;$i<$ccols;$i++)
      {
        if($cols[$i]!='') $colcounter++;
      }
      if($colcounter<count($fields))
      {
        $findcols .= '<div class="error">Alle Spalten müssen auswählt werden</div>';
        $importerror++;
      }

      if($importerror==0)
      {
        $findcols .= '<div class="info">Erfolgreich importiert</div>';
        if (($handle = fopen($stueckliste_csv, 'r')) !== FALSE) {
          $rowcounter = 1;
          while (($data = fgetcsv($handle, 1000, $this->app->User->GetParameter("artikel_stueckliste_delimiter"))) !== FALSE) {
            $rowcounter++;
            $num = count($data);

            if($rowcounter > $row_post){        
              for ($c=0; $c < $num; $c++) {
                // wenn schluessel vorhanden feld uebernehmen
                if($cols[$c]!='')
                  $singlerow[$cols[$c]]=$data[$c];
              }
              $result[] = $singlerow;
              $singlerow=array();
            }   
          }
        }
        fclose($handle);
        unlink($stueckliste_csv);
        $this->app->User->SetParameter("artikel_stueckliste_importfilename","");
        //      $this->app->Tpl->Set('PAGE',$findcols);
      }
    }


    //$_SESSION['quote']=$quote;
    $this->app->User->SetParameter("artikel_stueckliste_quote",$quote);
    //$_SESSION['delimiter']=$delimiter;
    $this->app->User->SetParameter("artikel_stueckliste_delimiter",$delimiter);


    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $stueckliste_csv)) {
      //$_SESSION['importfilename'] = $_FILES['userfile']['name'];
      $this->app->User->SetParameter("artikel_stueckliste_importfilename",$_FILES['userfile']['name']);
    }

    $row = 1;
    if (($handle = fopen($stueckliste_csv, "r")) !== FALSE) {
      $findcols .= "
        <table width=\"1070\"><tr><td>
        <h2>Datei: ".$this->app->User->GetParameter("artikel_stueckliste_importfilename")."</h2> (Die Anzeige ist limitiert auf max 10 Zeilen)</td><td>

        <form action=\"#tabs-3\" method=\"post\" enctype=\"multipart/form-data\">
        <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"30000\" />
        <input name=\"userfile\" type=\"file\" />

        </td><td align=\"right\">
        Trennzeichen: &nbsp;<input type=\"text\" size=\"3\" value=\"".html_entity_decode($this->app->User->GetParameter("artikel_stueckliste_delimiter"))."\" name=\"delimiter\">&nbsp;
      <!--Daten: &nbsp;<input type=\"text\" size=\"3\" value=\"".html_entity_decode($this->app->User->GetParameter("artikel_stueckliste_quote"))."\" name=\"quote\">&nbsp;-->
        <input type=\"submit\" value=\"aktualisieren\">
        </td></tr></table>
        ";


      $findcols .= "
        <div style=\"background: #eeeeee;
height: 350px;
overflow: scroll;
          font-size:7pt;
width: 1050px;
border: 1px solid #000;
padding: 10px;\">
           <table border=0 cellpadding=0 cellspacing=0>";       
           while (($data = fgetcsv($handle, 1000, $this->app->User->GetParameter("artikel_stueckliste_delimiter"))) !== FALSE) {
             $num = count($data);

             if($row==1)
             {
               $findcols .= "<tr><td></td><td colspan=\"".($num)."\" 
                 style=\"border: 1px solid black; background-color:#ffcc00;font-size:10pt;\">&nbsp;Spalten ausw&auml;hlen</td></tr>";
               $findcols .= "<tr><td style=\"border: 1px solid black; background-color:#ff6666; font-size:10pt;\" nowrap>&nbsp;Erste Zeile mit Daten&nbsp;<br>&nbsp;ausw&auml;hlen</td>";
               for ($c=0; $c < $num; $c++) {
                 $findcols .= "<td style=\"border: 1px solid black; background-color:#FFCC00; padding:5px;\">
                   &nbsp;&nbsp;<select name=\"cols[$c]\"><option></option>";

                 foreach($fields as $key=>$value){
                   if(count($cols)==0) { 
                     if($preselected[$key]==($c+1)) {
                       $selected='selected';
                     } else $selected='';
                   } else {
                     if($cols[$c]==$key) {
                       $selected='selected';
                     } else $selected='';
                   }    
                   $findcols .="<option value=\"$key\" $selected>$value</option>";
                 }

                 $findcols .='</select>&nbsp;</td>';
               }
               $findcols .= '</tr>';
             }
             if($row_post==$row) $checked="checked"; else $checked="";
             $findcols .= "<tr><td style=\"border: 1px solid black; background-color:#ff6666; padding:5px;\" align=\"center\">
               <input type=\"radio\" value=\"$row\" name=\"row\" $checked></td>";
             $row++;
             for ($c=0; $c < $num; $c++) {
               $findcols .= "<td style=\"border: 1px solid black;\">".$data[$c] . "&nbsp;</td>";
             }
             $findcols .= '</tr>';
             if($row > 10) {
               break;
             }
           }
         fclose($handle);
         $findcols .= "</table></div>
           <table width=\"1080\"><tr><td>
           <br><br>
           Bitte w&auml;hlen Sie aus:
           <ul><li>Die erste Zeile die Daten Ihrer Stueckliste enthält</li>
           <li>Die Spalten: Menge und Artikelnummer</li>
           </ul></td><td align=\"right\">
           <input type=\"submit\" value=\"Import abbrechen\" name=\"cancel\">
           <input type=\"submit\" value=\"Jetzt importieren\" name=\"import\">
           </td></tr></table>
           </form>
           ";
    } else {
      $findcols .= "
        <form action=\"#tabs-3\" method=\"post\" enctype=\"multipart/form-data\">
        <table width=\"1070\"><tr><td>
        Datei:&nbsp;
      <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"30000\" />
        <input name=\"userfile\" type=\"file\" />
        Vorlage: <select name=\"vorlage\">
        <!--<option></option>-->
        <option value=\"minimal\">Artikelnummer; Menge</option>
        <option value=\"altium\">Altium Designer</option>
        <option value=\"solidedgest2\">Solid Edge ST2</option>
        <!--<option value=\"eagle\">Eagle (Cadsoft) </option>-->
        </select> 
        </td><td align=\"right\">
        Trennzeichen: &nbsp;<input type=\"text\" size=\"3\" name=\"delimiter\" value=\";\">&nbsp;
      <!--Daten: &nbsp;<input type=\"text\" size=\"3\" value=\"&quot;\" name=\"quote\">&nbsp;-->
        <input type=\"submit\" value=\"St&uuml;ckliste laden\">
        </td></tr></table>
        </form>";



    }
    $this->app->Tpl->Set($parsetarget,$findcols);
    if(!empty($result)) {
      return $result;
    }
  }
  
  function getArtikelThumbnailDateiVersion($id)
  {
    $datei = $this->app->DB->SelectArr('
        SELECT 
        datei_version.id
        FROM
        datei_stichwoerter
        INNER JOIN datei_version ON datei_version.datei = datei_stichwoerter.datei
        WHERE
        datei_stichwoerter.objekt LIKE "artikel"
        AND
        datei_stichwoerter.parameter = "' . $id . '"
        AND
        (datei_stichwoerter.subjekt like "Shopbild" OR datei_stichwoerter.subjekt like "Druckbild" OR datei_stichwoerter.subjekt like "Bild" OR datei_stichwoerter.subjekt like "Gruppenbild")
        ORDER BY  datei_stichwoerter.subjekt like "Shopbild" DESC,subjekt like "Druckbild" DESC, datei_stichwoerter.subjekt like "Bild" DESC,datei_stichwoerter.sort, datei_version.version DESC
        ');
    if(empty($datei)){
      return;
    }
    foreach($datei as $v)
    {
      $pfad = $this->app->erp->GetDateiPfadVersion($v['id']);
      if(file_exists($pfad)){
        return $v['id'];
      }
    }
  }

  /**
   * @param int $articleId
   * @param int $projectId
   * @param int $size
   *
   * @return string
   */
  public function getThumbnailCacheFilename($articleId, $projectId, $size = 100)
  {
    return $this->app->getTmpFolder().$this->app->Conf->WFdbname
      .'/'.implode('/',str_split($articleId)).'/'.$articleId.'_'.$projectId.'_'.$size.'_'.$size;
  }

  public function ArtikelThumbnailCache()
  {
    $ajaxthumbnail = !empty($this->app->Secure->POST['ajaxthumbnail'])?(int)$this->app->Secure->POST['ajaxthumbnail']:'';
    if(!empty($ajaxthumbnail)) {
      return;
    }

    $direct = !empty($this->app->Secure->GET['direkt'])?(int)$this->app->Secure->GET['direkt']:'';
    if(!empty($direct)) {
      return;
    }

    $vorschau = !empty($this->app->Secure->GET['bildvorschau'])?$this->app->Secure->GET['bildvorschau']:'';
    $dark = !empty($this->app->Secure->GET['dark']);
    $projectid = !empty($this->app->Secure->GET['projectid'])?(int)$this->app->Secure->GET['projectid']:0;
    $noPicture = $vorschau === 'KEINBILD' || $vorschau == '';
    $projectOk = null;
    if(!$noPicture) {
      $projectOk = $this->app->User->projectOk($projectid);
      if($projectOk === null) {
        return;
      }
    }
    if($noPicture || !$projectOk){
      if($dark) {
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
      }else{
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
      }
      header('Content-type: image/png');
      if($vorschau === 'KEINBILD') {
        header('Cache-Control: public, max-age=14400, s-maxage=14400');
        header('Pragma: ');
        header('Expires: '.date('D, d M Y H:i:s e'), time()+14400);
        header('Content-type: image/jpg');
      }
      echo $str;
      exit; //kein ExitXentral, damit die Datenbank nicht geladen wird (mit isset)
    }
    $id = !empty($this->app->Secure->GET['id'])?(int)$this->app->Secure->GET['id']:0;
    if($id <= 0) {
      return;
    }

    $file = $this->getThumbnailCacheFilename($id, $projectid);
    if(!file_exists($file) || filesize($file) === 0) {
      return;
    }

    $type = mime_content_type($file);
    switch($type) {
      case 'image/png':
      case 'png':
        header('Content-type: image/png');
        break;
      case 'image/gif':
      case 'gif':
      header('Content-type: image/gif');
        break;
      default:
        header('Content-type: image/jpg');
        break;
    }
    header('Cache-Control: public, max-age=14400, s-maxage=14400');
    header('Pragma: ');
    header('Expires: '.date('D, d M Y H:i:s e'), time()+14400);
    header('Content-type: image/jpg');
    echo file_get_contents($file);
    exit;
  }

  /**
   * @param int $fileId
   *
   * @return array|null
   */
  public function getPreviewFileFromFileId($fileId)
  {
    if($fileId <= 0) {
      return null;
    }

    return $this->app->DB->SelectRow(
      sprintf(
        'SELECT dv.*
        FROM `datei_version` AS `dv`
        WHERE dv.datei = %d
        ORDER BY dv.version DESC
        LIMIT 1',
        $fileId
      )
    );
  }

  /**
   * @param int $articleId
   *
   * @return array|null
   */
  public function getPreviewFileFromArticleId($articleId)
  {
    if($articleId <= 0) {
      return null;
    }
    return $this->app->DB->SelectRow(
      sprintf(
        "SELECT  dv.*
        FROM `datei_stichwoerter` AS `ds`
        INNER JOIN `datei_version` AS `dv` ON dv.datei = ds.datei
        INNER JOIN `datei` AS `d` ON dv.datei = d.id AND IFNULL(d.geloescht, 0) = 0
        WHERE ds.objekt LIKE 'artikel' AND ds.parameter = '%d'
              AND
              (
                  ds.subjekt like 'Shopbild' 
                  OR ds.subjekt like 'Druckbild' 
                  OR ds.subjekt like 'Bild' 
                  OR ds.subjekt like 'Gruppenbild'
              )
        ORDER BY ds.subjekt like 'Shopbild' DESC,
                 ds.subjekt like 'Druckbild' DESC, 
                 ds.subjekt like 'Bild' DESC, 
                 ds.sort,
                 dv.version DESC 
        LIMIT 1",
        $articleId
      )
    );
  }

  /**
   * @param int $id
   * @param int $fileId
   * @param int $size
   *
   * @return array
   */
  public function ajaxGenerateThumbnail($id, $fileid = 0, $size = 100)
  {
    $res = ['result' => 0];
    if($id <= 0) {
      return $res;
    }

    $bildvorschau = $this->app->DB->SelectRow(
      sprintf(
        'SELECT `id`, `bildvorschau`, `projekt` FROM `artikel` WHERE `id` = %d LIMIT 1',
        $id
      )
    );

    if(empty($bildvorschau)) {
      return $res;
    }

    if(!$size){
      $size = 100;
    }
    $cachefolder = $this->app->Conf->WFuserdata . '/dms/' . $this->app->Conf->WFdbname . '/cache';
    $datei = 0;
    if($fileid > 0){
      $datei = $this->getPreviewFileFromFileId($fileid);
    }

    if(empty($datei)) {
      $datei = $this->getPreviewFileFromArticleId($id);
    }
    if(!empty($datei)) {
      $contentfile = $this->app->erp->GetDMSPath($datei['id'].'_'.$size.'_'.$size, $cachefolder, true);
      $created = false;
      if(file_exists($contentfile.'/'.$datei['id'].'_'.$size.'_'.$size)
        && filesize($contentfile.'/'.$datei['id'].'_'.$size.'_'.$size) === 0
        && @unlink($contentfile.'/'.$datei['id'].'_'.$size.'_'.$size)) {
        $contentfile = $this->app->erp->GetDMSPath($datei['id'].'_'.$size.'_'.$size, $cachefolder, true);
      }
      if(!file_exists($contentfile.'/'.$datei['id'].'_'.$size.'_'.$size)) {
        if(!class_exists('image')){
          include dirname(__DIR__).'/lib/class.image.php';
        }
        $img = new image($this->app);
        $img->scaledPicByFileId($datei['id'], $size, $size);
        $created = true;
      }
      if(!file_exists($contentfile.'/'.$datei['id'].'_'.$size.'_'.$size)) {
        return $res;
      }

      if($bildvorschau['bildvorschau'] != $datei['id'].'_'.$size.'_'.$size) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE `artikel` 
            SET `bildvorschau` = '%d_%d_%d' 
            WHERE `id` = %d 
            LIMIT 1",
            $datei['id'], $size, $size, $id
          )
        );
        $res['reload'] = 1;
      }
      $res['status'] = 1;
      $res['bildvorschau'] = $datei['id'].'_'.$size.'_'.$size;
      $cachefile = $this->getThumbnailCacheFilename($id, (int)$bildvorschau['projekt']);
      $cachedir = dirname($cachefile);
      if(!is_dir($cachedir) && !mkdir($cachedir, 0777,true) && !is_dir($cachedir)) {
        return $res;
      }
      if(empty($bildvorschau['bildvorschau']) || $created || !is_file($cachefile) || filesize($cachefile) === 0) {
        @copy($contentfile.'/'.$datei['id'].'_'.$size.'_'.$size, $cachefile);
      }

      return $res;
    }

    $cachefile = $this->getThumbnailCacheFilename($id, (int)$bildvorschau['projekt']);
    $cachedir = dirname($cachefile);
    if(!is_dir($cachedir) && !mkdir($cachedir, 0777, true) && !is_dir($cachedir)) {

    }
    elseif(!is_file($cachefile) || filesize($cachefile) > 0) {
      @file_put_contents($cachefile, '');
    }

    if($bildvorschau['bildvorschau'] !== 'KEINBILD') {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `artikel` SET `bildvorschau` = 'KEINBILD' WHERE `id` = %d LIMIT 1",
          $id
        )
      );
      $res['reload'] = 1;
    }
    $res['status'] = 1;
    $res['bildvorschau'] = 'KEINBILD';

    return $res;
  }

  public function ArtikelThumbnail() {

    $id = $this->app->Secure->GetGET('id');
    $fileid = $this->app->Secure->GetGET('fileid');
    $size = $this->app->Secure->GetGET('size');
    if(!$size){
      $size = 100;
    }

    $cachefolder = $this->app->Conf->WFuserdata . '/dms/' . $this->app->Conf->WFdbname . '/cache';
    if($this->app->Secure->GetPOST('ajaxthumbnail')) {
      $res = $this->ajaxGenerateThumbnail($id, $fileid, $size);
      echo json_encode($res);
      $this->app->ExitXentral();
    }

    $direkt = $this->app->Secure->GetGET('direkt');

    if($direkt && $id) {
      $bildvorschau = $this->app->DB->SelectRow(
        sprintf(
          'SELECT `id`, `bildvorschau`, `projekt` FROM `artikel` WHERE `id` = %d LIMIT 1',
          $id
        )
      );
      $datei = 0;
      if($fileid > 0){
        $datei = $this->getPreviewFileFromFileId($fileid);
      }

      if(empty($datei)){
        $datei = $this->getPreviewFileFromArticleId($id);
      }
      if($datei) {
        $contentfile = $this->app->erp->GetDMSPath($datei['id'].'_'.$size.'_'.$size, $cachefolder, true)
          .'/'.$datei['id'].'_'.$size.'_'.$size;
        if(is_file($contentfile) && filesize($contentfile) === 0 && @unlink($contentfile)) {
          $contentfileOld = $contentfile;
          $contentfile = $this->app->erp->GetDMSPath($datei['id'].'_'.$size.'_'.$size, $cachefolder, true)
            .'/'.$datei['id'].'_'.$size.'_'.$size;
          if($contentfile !== $contentfileOld && is_file($contentfile) && filesize($contentfile) === 0) {
            @unlink($contentfile);
          }
        }
        if(!file_exists($contentfile)) {
          $img = new image($this->app);
          $img->scaledPicByFileId($datei['id'], $size, $size);
          $contentfile = $this->app->erp->GetDMSPath(
            $datei['id'].'_'.$size.'_'.$size, $cachefolder, true
            )
            .'/'.$datei['id'].'_'.$size.'_'.$size;
          $cachefile = $this->getThumbnailCacheFilename($id, (int)$bildvorschau['projekt']);
          if(is_file($contentfile) && is_file($cachefile) && filesize($cachefile) === 0) {
            @copy($contentfile, $cachefile);
          }
        }

        if(file_exists($contentfile)) {
          $str = file_get_contents($contentfile);
          header('Cache-Control: public, max-age=14400, s-maxage=14400');
          header('Pragma: ');
          header('Expires: '.date('D, d M Y H:i:s e'), time()+14400);
          header('Content-type: image/jpg');
          echo $str;
          $this->app->ExitXentral();
        }
      }
    }

    $vorschau = $this->app->Secure->GetGET('bildvorschau');
    
    if(!$vorschau) {
      if ($this->app->erp->Firmendaten('iconset_dunkel')) {
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
      } else {
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
      }
      header('Content-type: image/png');
      echo $str;
      $this->app->ExitXentral();
    }
    if($vorschau === 'KEINBILD') {
      if ($this->app->erp->Firmendaten('iconset_dunkel')) {
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
      } else {
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
      }
      header('Content-type: image/png');
      echo $str;
      $this->app->ExitXentral();
    }
    if(file_exists($cachefolder)) {
      $vorschaua = explode('_',$vorschau);
      if(count($vorschaua) == 3) {
        $cachefolderPrev = $cachefolder;
        $cachefolder = $this->app->erp->GetDMSPath($vorschau, $cachefolderPrev, true);
        if(is_numeric($vorschaua[0]) && is_numeric($vorschaua[1]) && is_numeric($vorschaua[2])
          && file_exists($cachefolder.'/'.$vorschau) && filesize($cachefolder.'/'.$vorschau) === 0) {
          if(@unlink($cachefolder.'/'.$vorschau)) {
            $cachefolder = $this->app->erp->GetDMSPath($vorschau, $cachefolderPrev, true);
          }
        }

        if(is_numeric($vorschaua[0]) && is_numeric($vorschaua[1]) && is_numeric($vorschaua[2]) && file_exists($cachefolder.'/'.$vorschau)) {
          if($this->app->DB->Select(
            sprintf(
              "SELECT ds.id
              FROM datei_stichwoerter AS ds
              INNER JOIN datei_version AS dv ON dv.datei = ds.datei
              WHERE ds.objekt LIKE 'artikel' AND ds.parameter = '%d' AND dv.id = %d LIMIT 1",
              $id, $vorschaua[0]
            )
          )
          ) {
            $bildvorschau = $this->app->DB->SelectRow(
              "SELECT id, bildvorschau, projekt FROM artikel WHERE id = '$id' LIMIT 1"
            );
            $cachefile = $this->getThumbnailCacheFilename($id, (int)$bildvorschau['projekt']);
            $cachedir = dirname($cachefile);
            if(!is_dir($cachedir) && !mkdir($cachedir, 0777,true) && !is_dir($cachedir)) {

            } elseif(!is_file($cachefile) || filesize($cachefile) === 0) {
              @copy($cachefolder.'/'.$vorschau, $cachefile);
            }

            $str = file_get_contents($cachefolder.'/'.$vorschau);
            header('Content-type: image/jpg');
            echo $str;
            $this->app->ExitXentral();
          }
        }
      }
      if($id) {
        $this->app->DB->Update(sprintf("UPDATE artikel SET bildvorschau = '' WHERE id = %d", $id));
      }
    }

    if ($this->app->erp->Firmendaten('iconset_dunkel')) {
      $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
    }
    else {
      $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
    }
    header('Content-type: image/png');
    echo $str;
    $this->app->ExitXentral();
  }


  function ArtikelSchnellanlegen()
  {
    $submit_barcode = $this->app->Secure->GetPOST('submit_barcode');
    $submit_anlegen = $this->app->Secure->GetPOST('submit_anlegen');
    $barcode = $this->app->Secure->GetPOST('barcode');

    $this->app->erp->MenuEintrag('index.php?module=artikel&action=list','zur&uuml;ck zur &Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=artikel&action=schnellanlegen','Schnell anlegen');

    if($submit_barcode!='' && $barcode!='')
    {
      $checkbarcode = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$barcode' OR herstellernummer='$barcode' OR nummer='$barcode' LIMIT 1");

      if($checkbarcode > 0)
      {
        $name_nummer = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$checkbarcode' LIMIT 1");
        $msg = $this->app->erp->base64_url_encode("<div class=info>Es gibt bereits einen Artikel mit dieser Nummer ($name_nummer).</div>");
        $this->app->Location->execute("index.php?module=artikel&action=schnellanlegen&msg=$msg");
        return;
      }

      // Default einträge laden wenn vorhanden
      $mhd = $this->app->User->GetParameter('artikel_schnellanlegen_mhd');
      $chargen = $this->app->User->GetParameter('artikel_schnellanlegen_chargen');

      if($mhd=='1') {
        $this->app->Tpl->Set('MHD','checked');
      }
      if($chargen=='1') {
        $this->app->Tpl->Set('CHARGEN','checked');
      }

      $this->app->Tpl->Parse('PAGE','artikel_schnellanlegen_formular.tpl');

    } 
    else if ($submit_anlegen=='') {
      $this->app->Tpl->Parse('PAGE','artikel_schnellanlegen.tpl');
    }
  }

  // Zum Uberladen in Custom
  function ArtikelEigenschaftenSuche()
  {
  }


  public function ArtikelBaumDetail()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $todo = $this->app->Secure->GetGET('todo');
    $artikel = (int)$this->app->Secure->GetGET('artikel');
    if($todo === 'check')
    {
      if($artikel && $id)
      {
        $check = $this->app->DB->Select("SELECT id FROM artikelbaum_artikel WHERE artikel = '$artikel' AND kategorie = '$id' LIMIT 1");
        if(!$check)
        {
          $this->app->DB->Insert("INSERT INTO artikelbaum_artikel (artikel, kategorie) VALUES ('$artikel','$id')");
        }
      }
      $this->app->ExitXentral();
    }
    if($todo === 'uncheck'){
      $this->app->DB->Delete("DELETE FROM artikelbaum_artikel WHERE artikel = '$artikel' AND kategorie = '$id'");
      $this->app->ExitXentral();
    }
    echo "<h1>hallo $id</h1>";
    $this->app->ExitXentral();
  }
  
  function getKategorien(&$kategorien, $parent)
  {
    $res = $this->app->DB->SelectArr("SELECT id, bezeichnung, parent FROM artikelkategorien WHERE geloescht != 1 AND parent = '$parent' ORDER by bezeichnung");
    if($res)
    {
      foreach($res as $k => $v)
      {
        $kategorien[] = $v;
        $this->getKategorien($kategorien, $v['id']);
      }
    }
  }
  
  public function ArtikelBaumAjax()
  {
    $disabled = true;
    if($this->app->Secure->GetGET('enabled') == 1){
      $disabled = false;
    }
    $id = (int)$this->app->Secure->GetGET('artikel');
    $kategorien = null;
    $this->getKategorien($kategorien, 0);
    $kategorie = $this->app->DB->SelectArr("SELECT kategorie FROM artikelbaum_artikel WHERE artikel = '$id'");
    
    if(!empty($kategorien))
    {
      $maxlvl = 0;
      foreach($kategorien as $k => $v)
      {
        $ind[$v['id']] = $k;

        if($v['parent'] == 0)
        {
          $kategorien[$k]['lvl'] = 0;
          $name = 'node'.$k;
          $$name = new stdClass();
          $$name->id = $v['id'];
          $$name->label = $v['bezeichnung'];
          $$name->checkbox = true;
          $$name->inode = false;
          $$name->radio = false;
          if($disabled)$$name->disabled = true;
          if($kategorie)
          {
            foreach($kategorie as $ka)
            {
              if($ka['kategorie'] == $v['id'])$$name->checked = true;
            }
          }
          $baum[] = $$name;
          $kategorien[$k]['node'] = $$name;
        }else{
          if(isset($ind[$v['parent']]))
          {
            $name = 'node'.$k;
            $$name = new stdClass();
            $$name->id = $v['id'];
            $$name->label = $v['bezeichnung'];
            $$name->checkbox = true;
            $$name->inode = false;
            $$name->radio = false;
            if($disabled)$$name->disabled = true;
            if($kategorie)
            {
              foreach($kategorie as $ka)
              {
                if($ka['kategorie'] == $v['id'])$$name->checked = true;
              }
            }
            $kategorien[$k]['node'] = $$name;
            $kategorien[$k]['lvl'] = 1+$kategorien[$ind[$v['parent']]]['lvl'];
            if($kategorien[$k]['lvl'] > $maxlvl)$maxlvl = $kategorien[$k]['lvl'];
            $kategorien[$ind[$v['parent']]]['inode'] = true;
            //$kategorien[$ind[$v['parent']]]['node']->inode = true;
            $kategorien[$ind[$v['parent']]]['node']->open = true;
            $kategorien[$ind[$v['parent']]]['node']->branch[] = $$name;
          }
        }
      }
      echo json_encode($baum);
      $this->app->erp->ExitWawi();
    }
    echo '[]';
    $this->app->erp->ExitWawi();
  }


  public function ArtikelBaum()
  {
    $extraurl = '';
    if($this->app->Secure->GetPOST('enabled'))
    {
      $extraurl = '&enabled=1';
      $this->app->Tpl->Set('BUTTONBEARBEITEN','<form method="post"><input type="submit" name="disable" value="Bearbeitung deaktivieren" /></form>');
    }elseif($this->app->erp->RechteVorhanden('artikel','baumedit')){
      $this->app->Tpl->Set('BUTTONBEARBEITEN', '<form method="post"><input type="submit" name="enabled" value="Bearbeitung aktivieren" /></form>');
    }
    $id = $this->app->Secure->GetGET('id');
    $url = 'index.php?module=artikel&action=baumajax'.$extraurl.'&artikel='.$id;
    $this->ArtikelMenu();


    $this->app->Tpl->Set('URL',$url);
    $this->app->Tpl->Parse('PAGE','artikel_baum.tpl');
  }

  public function ArtikelBaumEdit()
  {
    //Rechte
    $this->app->ExitXentral();
  }


  public function ArtikelFreifelder(){
    $this->ArtikelMenu();

    $artikelid = $this->app->Secure->GetGET('id');

    if($this->app->Secure->GetPOST('nachladen') != ''){
      $sqla = null;
      for ($i = 1; $i <= 40; $i++) {
        $sqla[] = ' SELECT '.$i.' as nummer ';
      }
      $sql = "INSERT INTO artikel_freifelder (artikel, sprache, nummer, wert)
        SELECT '$artikelid', s.iso, n.nummer,'' 
        FROM (SELECT iso FROM sprachen WHERE aktiv = 1 AND iso <> 'DE' AND iso <> '' GROUP BY iso) s
        INNER JOIN (".implode(' UNION ', $sqla).") n 
        LEFT JOIN artikel_freifelder af ON s.iso = af.sprache AND af.artikel = '$artikelid' AND n.nummer = af.nummer
        WHERE  isnull(af.id)
      ";
      $this->app->DB->Insert($sql);
    }

    $this->app->YUI->TableSearch('TAB1','artikelfreifelder_list', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','artikelfreifelder_list.tpl');
  }

  public function ArtikelFreifelderEdit(){

    if($this->app->Secure->GetGET('cmd')==='get'){
      $id = (int)$this->app->Secure->GetPOST('id');
      $sprache = $this->app->Secure->GetPOST('sprache');
      $nummer = (int)$this->app->Secure->GetPOST('nummer');
      
      for($i=1; $i<=40; $i++){
        $freifelderfirmendaten[] = '(SELECT '.$i.' AS nummer, if(f.freifeld'.$i." != '', f.freifeld".$i.", CONCAT('Freifeld ',".$i.')) AS bezeichnung FROM firmendaten f)';
        $freifeldtypen[] = '(SELECT '.$i.' AS nummer, wert AS inputtyp FROM firmendaten_werte WHERE name = CONCAT("freifeld",'.$i.',"typ"))';
      }

      if($sprache != '' && $sprache !== 'DE'){
        $data = $this->app->DB->SelectRow('SELECT af.id, af.nummer as nummer, af.wert, af.sprache, f.bezeichnung, ft.inputtyp 
                                           FROM artikel_freifelder af 
                                           LEFT JOIN ('.implode(' UNION ALL ', $freifelderfirmendaten).") f ON af.nummer = f.nummer 
                                           LEFT JOIN (".implode(' UNION ALL ', $freifeldtypen).") ft ON af.nummer = ft.nummer
                                           WHERE af.id = '$id' 
                                           LIMIT 1");

      }elseif($sprache === 'DE'){
        $data = $this->app->DB->SelectRow('SELECT a.id, '.$nummer.' as nummer, a.freifeld'.$nummer." AS wert, 'DE' AS sprache, 
                              (SELECT IF(f.freifeld".$nummer." != '', f.freifeld".$nummer.", CONCAT('Freifeld ',".$nummer.")) FROM firmendaten f) AS bezeichnung,
                              (SELECT wert AS inputtyp FROM firmendaten_werte ft WHERE name = CONCAT('freifeld',".$nummer.",'typ')) AS inputtyp 
                      FROM artikel a 
                      WHERE a.id = '$id' 
                      LIMIT 1");

      }else{
        $data = null;
      }

      if(empty($data)){
        $data['id'] = 0;
        $data['wert'] = '';
        $data['sprache'] = '';
        $data['bezeichnung'] = '';
        $data['nummer'] = '';
        $data['inputtyp'] = 'einzeilig';
        
      }
      echo json_encode($data);
      $this->app->ExitXentral();
    }

    $this->app->Tpl->Parse('PAGE', 'artikelfreifelder_list.tpl');
  }

  public function ArtikelFreifelderSave(){

    $id = (int)$this->app->Secure->GetPOST('id');
    $inhalt = trim($this->app->Secure->GetPOST('freifeldinhalttext'));
    $sprache = $this->app->Secure->GetPOST('sprache');
    $nummer = (int)$this->app->Secure->GetPOST('nummer');
    $error = '';
    if($error == ''){
      if($id){
        if($sprache === 'DE'){
          if($nummer > 0)
            $this->app->DB->Update("UPDATE artikel SET freifeld".$nummer." = '$inhalt' WHERE id = '$id'");
        }else{
          $this->app->DB->Update("UPDATE artikel_freifelder SET wert = '$inhalt' WHERE id = '$id'");
        }
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
      if($sprache === 'DE'){
        echo json_encode(array('status'=>0,'statusText'=>'Kein gültiger Artikel'));
        $this->app->ExitXentral();
      }
      $this->app->DB->Insert("INSERT INTO artikel_freifelder (wert) VALUES ('$inhalt')");

      echo json_encode(array('status'=>1));
      $this->app->ExitXentral();
    }
    echo json_encode(array('status'=>0,'statusText'=>$error));
    $this->app->ExitXentral();
  }

  public function ArtikelFreifelderDelete(){
    $id = (int) $this->app->Secure->GetPOST('id');
    if($id > 0){
      $this->app->DB->Delete("DELETE FROM artikel_freifelder WHERE id = '$id' LIMIT 1");
    }
    echo json_encode(array('status'=>1));
    $this->app->ExitXentral();
  }

  public function Install(){
    $this->app->erp->CheckTable('artikel_freifelder');
    $this->app->erp->CheckColumn('id', 'int(11)', 'artikel_freifelder', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('artikel', 'int(11)', 'artikel_freifelder', 'NOT NULL DEFAULT 0');
    $this->app->erp->CheckColumn('sprache', 'varchar(255)', 'artikel_freifelder', 'NOT NULL');
    $this->app->erp->CheckColumn('nummer', 'int(11)', 'artikel_freifelder', 'NOT NULL');
    $this->app->erp->CheckColumn('wert', 'text', 'artikel_freifelder', 'NOT NULL');
    $this->app->erp->CheckIndex('artikel_freifelder', 'artikel');

    $this->app->erp->CheckColumn('lieferzeit_standard_einheit', 'varchar(64)', 'einkaufspreise', 'NOT NULL');
    $this->app->erp->CheckColumn('lieferzeit_aktuell_einheit', 'varchar(64)', 'einkaufspreise', 'NOT NULL');

    $this->app->erp->CheckAlterTable("ALTER TABLE `artikel_freifelder` CHANGE `wert` `wert` text NOT NULL");

    $this->app->erp->CheckTable('parts_list_alternative');
    $this->app->erp->CheckColumn('id', 'int(11)', 'parts_list_alternative', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('parts_list_id', 'int(11)', 'parts_list_alternative', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('alternative_article_id', 'int(11)', 'parts_list_alternative', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('reason', 'varchar(255)', 'parts_list_alternative', 'DEFAULT "" NOT NULL');

    $this->app->erp->CheckTable('artikel_cached_fields');
    $this->app->erp->CheckColumn('id','int(11)','artikel_cached_fields','NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('artikel','INT(11)','artikel_cached_fields','DEFAULT 0 NOT NULL');

    $this->app->erp->CheckColumn('project_id','INT(11)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('project_name','VARCHAR(64)','artikel_cached_fields','DEFAULT \'\' NOT NULL');

    $this->app->erp->CheckColumn('number','VARCHAR(64)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('ean','VARCHAR(64)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('factory_number','VARCHAR(64)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('name','VARCHAR(255)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('manufactor','VARCHAR(255)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('customfield1','VARCHAR(255)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('customfield2','VARCHAR(255)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('ek_customnumber','VARCHAR(1024)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('vk_customnumber','VARCHAR(1024)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('eigenschaften','VARCHAR(1024)','artikel_cached_fields','DEFAULT \'\' NOT NULL');

    $this->app->erp->CheckColumn('is_storage_article','TINYINT(1)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('is_variant','TINYINT(1)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('variant_from_id','INT(11)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('variant_from_name','VARCHAR(64)','artikel_cached_fields','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('is_partlist','TINYINT(1)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('is_shipping','TINYINT(1)','artikel_cached_fields','DEFAULT 0 NOT NULL');

    $this->app->erp->CheckColumn('locked','TINYINT(1)','artikel_cached_fields','DEFAULT 0 NOT NULL');

    $this->app->erp->CheckColumn('zeitstempel','TIMESTAMP','artikel_cached_fields','DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckColumn('lager_verfuegbar','DECIMAL(14,4)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('ek_netto','DECIMAL(14,4)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('ek_brutto','DECIMAL(14,4)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('vk_netto','DECIMAL(14,4)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('vk_brutto','DECIMAL(14,4)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('inzulauf','DECIMAL(14,4)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('imsperrlager','DECIMAL(14,4)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('inproduktion','DECIMAL(14,4)','artikel_cached_fields','DEFAULT 0 NOT NULL');
    $this->app->DB->Select('SELECT lager_gesamt FROM artikel_cached_fields LIMIT 1');
    if($this->app->DB->error()){
      $this->app->erp->CheckColumn('lager_gesamt', 'DECIMAL(14,4)', 'artikel_cached_fields', 'DEFAULT 0 NOT NULL');
      $this->app->DB->Update("UPDATE artikel_cached_fields acf 
        INNER JOIN artikel a ON acf.artikel = a.id AND a.lagerartikel = 1
        LEFT JOIN (
           SELECT lpi2.artikel, sum(lpi2.menge) as minmenge 
          FROM lager_platz_inhalt  lpi2 
          GROUP BY lpi2.artikel 
         ) verf ON a.id = verf.artikel
        SET acf.lager_gesamt = ifnull(verf.minmenge,0)
      ");
    }
    $this->app->erp->CheckIndex('artikel_cached_fields', 'artikel');

    $this->app->erp->CheckTable('article_label');
    $this->app->erp->CheckColumn('id', 'int(11)', 'article_label', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('article_id', 'int(11)', 'article_label', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('label_id', 'int(11)', 'article_label', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('printer_id', 'int(11)', 'article_label', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('amount', 'int(11)', 'article_label', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('type', 'varchar(64)', 'article_label', 'NOT NULL');

    $this->app->erp->RegisterHook('supersearch_detail', 'artikel', 'ArtikelSupersearchDetail');
    $this->app->erp->RegisterHook('LayouttemplateAttachmentItemsDelete', 'artikel', 'ArtikelDeleteFile');

    $totalArticles = $this->app->DB->Select('SELECT COUNT(a.id) AS `total` FROM `artikel` AS `a` LIMIT 1');
    if ($totalArticles > 0) {
      $this->app->DB->Update('UPDATE `artikel` SET `intern_gesperrt` = `gesperrt` WHERE `gesperrt` = 1');
    }
  }

  /**
   * @param int $fileId
   * @param string $cmd
   *
   * @return void
   */
  public function ArtikelDeleteFile($fileId, $cmd)
  {
      if ($cmd !== 'artikel') {
          return;
      }

      $query = sprintf("UPDATE `artikel` AS `a` 
      INNER JOIN `datei_stichwoerter` AS `ds` ON a.id = ds.parameter 
      INNER JOIN `datei_version` AS `dv` ON ds.id = dv.datei
      INNER JOIN (SELECT MAX(dv.version) AS `version` FROM `datei_version` AS `dv` WHERE dv.datei = %d) AS `v` ON v.version = dv.version 
      SET a.bildvorschau = '' 
      WHERE ds.id = %d AND a.bildvorschau = CONCAT(dv.id,'_100_100')", $fileId, $fileId);
      $this->app->DB->Update($query);
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function ArtikelSupersearchDetail($detailQuery, $detailResult)
  {
    if ($detailQuery->getGroupKey() !== 'articles') {
      return;
    }

    $articleId = $detailQuery->getItemIdentifier();
    $sql = sprintf(
      "SELECT a.id, CONCAT(a.name_de, ' (', a.nummer, ')') AS title, a.name_de, a.kurztext_de, a.nummer 
       FROM artikel AS a 
       WHERE a.id = '%s' LIMIT 1",
      $this->app->DB->real_escape_string($articleId)
    );
    $article = $this->app->DB->SelectRow($sql);
    if (empty($article)) {
      return;;
    }

    $detailResult->setTitle($article['title']);
    $detailResult->addButton('Artikel Details', sprintf('index.php?module=artikel&action=edit&id=%s', $article['id']));
    $detailResult->setDescription(sprintf('<p>%s</p>', strip_tags($article['kurztext_de'])));
    $detailResult->setMiniDetailUrl(sprintf('index.php?module=artikel&action=minidetail&id=%s', $article['id']));
  }

  public function createNewAricleCache()
  {
    $this->app->DB->Insert(
      "INSERT INTO artikel_cached_fields 
      (artikel,lager_verfuegbar, ek_netto, vk_netto, ek_brutto, vk_brutto, inzulauf,imsperrlager,inproduktion) 
      SELECT a.id,0, 0, 0, 0, 0, 0, 0, 0
      FROM artikel a 
      LEFT JOIN artikel_cached_fields acf ON a.id = acf.artikel 
      WHERE isnull(acf.id)"
    );
  }

  public function createAricleCache()
  {
    $ermaessigt = (float)$this->app->erp->Firmendaten('steuersatz_ermaessigt');
    if($ermaessigt <= 0)
    {
      $ermaessigt = 7;
    }
    $normal = (float)$this->app->erp->Firmendaten('steuersatz_normal');
    if($normal <= 0)
    {
      $normal = 19;
    }
    $normal = 1+ $normal / 100;
    $ermaessigt = 1+ $ermaessigt / 100;
    $this->app->DB->Update(
      "UPDATE artikel_cached_fields AS acf 
      INNER JOIN artikel AS a ON acf.artikel = a.id
      LEFT JOIN (
        SELECT artikel, min(preis) as minpreis 
        FROM einkaufspreise 
        WHERE (gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate())
          AND preis > 0 
        GROUP BY artikel
      ) AS ek ON a.id = ek.artikel
      LEFT JOIN (
        SELECT artikel, min(preis) as minpreis 
        FROM einkaufspreise 
        WHERE (gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate())
          AND preis > 0 
          AND (ifnull(waehrung,'EUR') = 'EUR' OR waehrung = '') 
        GROUP BY artikel
      ) AS ek2 ON a.id = ek2.artikel
      SET acf.ek_netto = ifnull( ifnull(ek2.minpreis,ek.minpreis),0), 
          acf.ek_brutto = if(a.umsatzsteuer = 'befreit', 1, if(a.umsatzsteuer = 'ermaessigt', $ermaessigt,$normal))
                              *ifnull( ifnull(ek2.minpreis,ek.minpreis),0)
      WHERE a.geloescht <> 1"
    );
    $this->app->DB->Update(
      "UPDATE artikel_cached_fields AS acf 
      INNER JOIN artikel AS a ON acf.artikel = a.id
      LEFT JOIN (
        SELECT artikel, min(preis) as minpreis 
        FROM verkaufspreise 
        WHERE (gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate()) 
          AND (gueltig_ab = '0000-00-00' OR gueltig_ab <= curdate())
          AND art <> 'Gruppe' AND art <> 'gruppe' 
          AND adresse=0 AND preis > 0 
        GROUP BY artikel
      ) AS vk ON a.id = vk.artikel
      LEFT JOIN (
        SELECT artikel, min(preis) as minpreis 
        FROM verkaufspreise 
        WHERE (gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate())
          AND (gueltig_ab = '0000-00-00' OR gueltig_ab <= curdate())
          AND art <> 'Gruppe' AND art <> 'gruppe'
          AND adresse=0 AND preis > 0 AND (ifnull(waehrung,'EUR') = 'EUR' OR waehrung = '') 
        GROUP BY artikel 
      ) AS vk2 ON a.id = vk2.artikel
          LEFT JOIN (
        SELECT artikel, min(preis) as minpreis 
        FROM verkaufspreise 
        WHERE (gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate())
          AND (gueltig_ab = '0000-00-00' OR gueltig_ab <= curdate())
          AND art <> 'Gruppe' AND art <> 'gruppe'
          AND adresse=0 AND preis > 0 AND (ifnull(waehrung,'EUR') = 'EUR' OR waehrung = '') AND ab_menge = 1 
        GROUP BY artikel 
      ) AS vk3 ON a.id = vk3.artikel

      SET acf.vk_netto = ifnull( ifnull( ifnull(vk3.minpreis,vk2.minpreis),vk.minpreis),0), 
          acf.vk_brutto = if(a.umsatzsteuer = 'befreit', 1, if(a.umsatzsteuer = 'ermaessigt', $ermaessigt,$normal))
                              *ifnull( ifnull( ifnull(vk3.minpreis,vk2.minpreis),vk.minpreis),0)
      WHERE a.geloescht <> 1"
    );

    $this->app->DB->Update(
      "UPDATE artikel_cached_fields AS acf 
      INNER JOIN artikel AS a ON acf.artikel = a.id
      LEFT JOIN artikel AS a2 ON a.variante_von = a2.id AND a.variante = 1 AND IFNULL(a2.geloescht,0) = 0 AND a2.nummer <> 'DEL'
      LEFT JOIN projekt as pr ON a.projekt = pr.id
      LEFT JOIN (
        SELECT pos.artikel, SUM(pos.menge) AS produktion 
        FROM produktion_position pos 
        LEFT JOIN produktion p ON pos.produktion = p.id
        WHERE p.status = 'gestartet' AND pos.sort = 1
        GROUP BY pos.artikel
      ) AS prod ON a.id = prod.artikel
      LEFT JOIN (
        SELECT artikel, sum(bp.menge - bp.geliefert) as zulauf 
        FROM bestellung_position AS bp 
        LEFT JOIN bestellung b ON bp.bestellung=b.id
        WHERE b.status!='storniert' AND b.status!='abgeschlossen' AND bp.geliefert<bp.menge 
        GROUP BY bp.artikel
      ) AS zul ON a.id = zul.artikel
      SET acf.name = a.name_de,
          acf.number = a.nummer,
          acf.ean = a.ean,
          acf.customfield1 = a.freifeld1,
          acf.customfield2 = a.freifeld2,
          acf.is_variant = IFNULL(a.variante,0),
          acf.variant_from_id = IFNULL(a.variante_von,0),
          acf.variant_from_name = IFNULL(a2.name_de,''),
          acf.factory_number = IFNULL(a.herstellernummer,''),
          acf.manufactor = IFNULL(a.hersteller,''),
          acf.is_storage_article = a.lagerartikel,
          acf.is_partlist = a.stueckliste,
          acf.locked = a.intern_gesperrt,
          acf.is_shipping = a.porto,
          acf.project_id = a.projekt,
          acf.project_name = IFNULL(pr.abkuerzung,''),
          acf.inzulauf = ifnull(zul.zulauf,0),
          acf.inproduktion = ifnull(prod.produktion,0),
          acf.inzulauf = ifnull(zul.zulauf,0)
      WHERE a.geloescht <> 1"
    );

    $ekNumbers = $this->app->DB->Query(
      sprintf(
        "SELECT DISTINCT artikel, bestellnummer FROM einkaufspreise WHERE IFNULL(bestellnummer,'') <> '' ORDER BY artikel"
      )
    );
    if($ekNumbers) {
      $lastArtikel = 0;
      $numbers = [];
      while($row = $this->app->DB->Fetch_Assoc($ekNumbers)) {
        if($row['artikel'] != $lastArtikel) {
          if($lastArtikel > 0 && !empty($numbers)) {
            $this->app->DB->Update(
              sprintf(
                "UPDATE artikel_cached_fields SET ek_customnumber = '%s' WHERE artikel = %d",
                implode(',', $numbers), $lastArtikel
              )
            );
          }
          $numbers = [];
          $lastArtikel = $row['artikel'];
        }
        $numbers[] = $this->app->DB->real_escape_string($row['bestellnummer']);
      }
      $this->app->DB->free($ekNumbers);
      if($lastArtikel > 0 && !empty($numbers)) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE artikel_cached_fields SET ek_customnumber = '%s' WHERE artikel = %d",
            implode(',', $numbers), $lastArtikel
          )
        );
      }
    }
    $ekNumbers = $this->app->DB->Query(
      sprintf(
        "SELECT DISTINCT artikel, kundenartikelnummer 
        FROM verkaufspreise WHERE IFNULL(kundenartikelnummer,'') <> '' 
        ORDER BY artikel"
      )
    );
    if($ekNumbers) {
      $lastArtikel = 0;
      $numbers = [];
      while($row = $this->app->DB->Fetch_Assoc($ekNumbers)) {
        if($row['artikel'] != $lastArtikel) {
          if($lastArtikel > 0 && !empty($numbers)) {
            $this->app->DB->Update(
              sprintf(
                "UPDATE artikel_cached_fields SET vk_customnumber = '%s' WHERE artikel = %d",
                implode(',', $numbers), $lastArtikel
              )
            );
          }
          $numbers = [];
          $lastArtikel = $row['artikel'];
        }
        $numbers[] = $this->app->DB->real_escape_string($row['kundenartikelnummer']);
      }
      $this->app->DB->free($ekNumbers);
      if($lastArtikel > 0 && !empty($numbers)) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE artikel_cached_fields SET vk_customnumber = '%s' WHERE artikel = %d",
            implode(',', $numbers), $lastArtikel
          )
        );
      }
    }
  }

  public function updateStorageToAricleCache()
  {
    $this->app->DB->Update(
      "UPDATE artikel_cached_fields AS acf 
      INNER JOIN artikel AS a ON acf.artikel = a.id AND a.lagerartikel = 1
      LEFT JOIN (
        SELECT lpi2.artikel, sum(lpi2.menge) AS minmenge 
        FROM lager_platz_inhalt  lpi2 
        GROUP BY lpi2.artikel 
      ) AS verf ON a.id = verf.artikel
      SET acf.lager_gesamt = ifnull(verf.minmenge,0)"
    );
  }

  public function updateAttributesToArticleCache()
  {
    $this->app->DB->Update(
      sprintf(
        "UPDATE artikel_cached_fields AS acf 
        LEFT JOIN artikeleigenschaftenwerte AS aew ON acf.artikel = aew.artikel
        LEFT JOIN artikeleigenschaften AS ae 
          ON aew.artikeleigenschaften = ae.id AND ifnull(ae.geloescht,0) = 0 AND ae.name <> ''
        SET acf.eigenschaften = '' 
        WHERE ISNULL(ae.name)"
      )
    );
    $attributes = $this->app->DB->Query(
      "SELECT aew.artikel, ae.name,aew.wert
      FROM artikeleigenschaftenwerte AS aew
      INNER JOIN artikeleigenschaften AS ae 
          ON aew.artikeleigenschaften = ae.id AND ifnull(ae.geloescht,0) = 0 AND ae.name != ''
      ORDER BY aew.artikel, ae.name, aew.wert"
    );
    if(!$attributes) {
      return;
    }
    $value = [];
    $actAricle = 0;
    while($row = $this->app->DB->Fetch_Assoc($attributes)) {
      if($actAricle > 0 && $row['artikel'] != $actAricle) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE artikel_cached_fields SET eigenschaften = '%s' WHERE artikel = %d",
            implode(', ', $value), $actAricle
          )
        );
        $value = [];
      }
      $actAricle = $row['artikel'];
      $value[] = $this->app->DB->real_escape_string($row['name']).': '
        .$this->app->DB->real_escape_string($row['wert']);
    }
    $this->app->DB->free($attributes);
    if(empty($value)) {
      return;
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE artikel_cached_fields SET eigenschaften = '%s' WHERE artikel = %d",
        implode(', ', $value), $actAricle
      )
    );
  }

  /**
   * @return int
   */
  public function updateArticlePicturePreview($limit = 1000)
  {
    $articleIds = $this->app->DB->SelectFirstCols(
      sprintf(
        "SELECT art.id
        FROM `artikel` AS `art`
        WHERE IFNULL(art.bildvorschau,'') = ''
        LIMIT %d",
        $limit
      )
    );
    $res = 0;
    if(empty($articleIds)) {
      return 0;
    }
    $files = $this->app->DB->SelectFirstCols(
      sprintf(
        "SELECT ds.parameter
        FROM `datei_stichwoerter` AS `ds`
        WHERE ds.objekt LIKE 'artikel' AND ds.parameter IN ('%s') AND ds.datei > 0",
        implode("','", $articleIds)
      )
    );
    $toSetNull = array_diff($articleIds, $files);
    if(!empty($toSetNull)) {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `artikel` SET `bildvorschau` = 'KEINBILD' WHERE  IFNULL(bildvorschau,'') = '' AND `id` IN (%s)",
          implode(',', $toSetNull)
        )
      );
      $res = $this->app->DB->affected_rows();
      if($res <= 0) {
        $res = 0;
      }
    }

    if(!empty($files)) {
      foreach($files as $id) {
        try {
          $ret = $this->ajaxGenerateThumbnail($id);
        }
        catch(Exception $e) {
          $ret = ['result' => 0];
        }
        if(!empty($ret['bildvorschau'])) {
          $res++;
        }
      }
    }

    return $res;
  }

  public function updateExplodingPartListSellableToAricleCache()
  {
    $this->app->DB->Update(
      "UPDATE artikel_cached_fields AS acf 
      INNER JOIN artikel AS a ON acf.artikel = a.id AND a.lagerartikel = 1 AND a.juststueckliste = 1
      LEFT JOIN
      (
        SELECT s.stuecklistevonartikel, min(FLOOR((ifnull(lpi.menge,0) - if(ifnull(r.menge,0) > ifnull(off.menge,0),ifnull(r.menge,0),ifnull(off.menge,0))) / s.menge)) as minmenge
        FROM
        (
          SELECT artikel,sum(menge) as menge ,stuecklistevonartikel 
          FROM stueckliste 
          WHERE art!='it' 
          GROUP BY artikel,stuecklistevonartikel
        ) AS s
        INNER JOIN artikel AS a2 ON s.artikel = a2.id
        LEFT JOIN (
          SELECT lpi2.artikel, sum(lpi2.menge) as menge 
          FROM lager_platz_inhalt  lpi2 
            INNER JOIN lager_platz lp ON lpi2.lager_platz = lp.id 
          WHERE lp.sperrlager <> 1 
          GROUP BY lpi2.artikel 
        ) AS lpi ON a2.id = lpi.artikel
        LEFT JOIN (
          SELECT artikel, ifnull(sum(menge),0) as menge 
          FROM lager_reserviert 
          GROUP BY artikel 
        ) AS r ON a2.id = r.artikel
        LEFT JOIN (
          SELECT ifnull(SUM(ap.menge),0) as menge,ap.artikel 
          FROM auftrag_position ap 
          INNER JOIN auftrag a ON a.id=ap.auftrag 
          WHERE a.status='freigegeben' 
          GROUP BY ap.artikel
        ) AS off ON a2.id = off.artikel
        GROUP BY s.stuecklistevonartikel
      ) AS verf ON acf.artikel = verf.stuecklistevonartikel
      SET acf.lager_verfuegbar = if(ifnull(verf.minmenge,0) <= 0,0, verf.minmenge)"
    );
  }

  public function updatePartListJitArticleStorageCache()
  {
    $jitArticles = $this->app->DB->Query(
      'SELECT `id` 
      FROM `artikel` 
      WHERE `juststueckliste` = 1 AND `lagerartikel` = 0 AND (`geloescht` = 0 OR `geloescht` IS NULL)'
    );
    while($article = $this->app->DB->Fetch_Assoc($jitArticles)) {
      $article = $article['id'];
      $inStock = (float)$this->app->erp->ArtikelAnzahlLagerStueckliste($article);
      if($inStock < 0) {
        $inStock = 0;
      }
      $this->app->DB->Update(
        sprintf(
          'UPDATE `artikel_cached_fields` SET `lager_gesamt` = %f WHERE `artikel` = %d',
          $inStock, $article
        )
      );
    }
    $this->app->DB->free($jitArticles);
  }

  public function updatePartListSellableToAricleCache()
  {
    $this->app->DB->Update(
      "UPDATE artikel_cached_fields AS acf 
      INNER JOIN artikel AS a ON acf.artikel = a.id AND a.lagerartikel = 1 AND a.juststueckliste = 1
      LEFT JOIN
      (
        SELECT s.stuecklistevonartikel, min(FLOOR((ifnull(lpi.menge,0) - if(ifnull(r.menge,0) > ifnull(off.menge,0),ifnull(r.menge,0),ifnull(off.menge,0))) / s.menge)) as minmenge
          FROM
            (
              SELECT artikel,sum(menge) as menge ,stuecklistevonartikel 
              FROM stueckliste 
              WHERE art!='it' 
              GROUP BY artikel,stuecklistevonartikel
            ) AS s
            INNER JOIN artikel a2 ON s.artikel = a2.id
            LEFT JOIN (
              SELECT lpi2.artikel, sum(lpi2.menge) as menge 
              FROM lager_platz_inhalt  lpi2 
              INNER JOIN lager_platz lp ON lpi2.lager_platz = lp.id 
              WHERE lp.sperrlager <> 1 
              GROUP BY lpi2.artikel 
            ) AS lpi ON a2.id = lpi.artikel
            LEFT JOIN (SELECT artikel, ifnull(sum(menge),0) as menge FROM lager_reserviert GROUP BY artikel ) r ON a2.id = r.artikel
            LEFT JOIN (SELECT ifnull(SUM(ap.menge),0) as menge,ap.artikel FROM auftrag_position ap INNER JOIN auftrag a ON a.id=ap.auftrag WHERE a.status='freigegeben' GROUP BY ap.artikel) off ON a2.id = off.artikel
        GROUP BY s.stuecklistevonartikel
      ) verf ON acf.artikel = verf.stuecklistevonartikel
      LEFT JOIN (
        SELECT artikel, ifnull(sum(menge),0) as menge 
        FROM lager_reserviert 
        GROUP BY artikel 
      ) AS r2 ON acf.artikel = r2.artikel
      LEFT JOIN (
        SELECT ifnull(SUM(ap.menge),0) as menge,ap.artikel 
        FROM auftrag_position ap 
        INNER JOIN auftrag a ON a.id=ap.auftrag 
        WHERE a.status='freigegeben' 
        GROUP BY ap.artikel
      ) AS off2 ON acf.artikel = off2.artikel
      SET acf.lager_verfuegbar = 
        if(
          ifnull(verf.minmenge,0) - 
            if(
              ifnull(r2.menge,0) > ifnull(off2.menge,0),
              ifnull(r2.menge,0),
              ifnull(off2.menge,0)
              ) <= 0,
          0, 
            verf.minmenge - 
            if(
              ifnull(r2.menge,0) > ifnull(off2.menge,0),
              ifnull(r2.menge,0),
              ifnull(off2.menge,0)
          )
        )
  ");
  }

  public function updateQuarantineStoreToAricleCache() {
    $this->app->DB->Update(
      "UPDATE artikel_cached_fields acf 
      INNER JOIN artikel a ON acf.artikel = a.id AND a.lagerartikel = 1 AND a.juststueckliste = 0
      LEFT JOIN (
        SELECT lpi2.artikel, sum(lpi2.menge) as minmenge 
        FROM lager_platz_inhalt  lpi2 
        INNER JOIN lager_platz lp ON lpi2.lager_platz = lp.id 
        WHERE lp.sperrlager = 1 
        GROUP BY lpi2.artikel 
      ) verf ON a.id = verf.artikel
      SET acf.imsperrlager = ifnull(verf.minmenge,0)"
    );
  }

  public function updateSellableToAricleCache()
  {
    $this->app->DB->Update(
      "UPDATE artikel_cached_fields acf 
     INNER JOIN artikel a ON acf.artikel = a.id AND a.lagerartikel = 1 AND a.juststueckliste = 0
     LEFT JOIN (SELECT artikel, ifnull(sum(menge),0) as menge FROM lager_reserviert GROUP BY artikel ) r2 ON acf.artikel = r2.artikel
     LEFT JOIN (SELECT ifnull(SUM(ap.menge),0) as menge,ap.artikel FROM auftrag_position ap INNER JOIN auftrag a ON a.id=ap.auftrag WHERE a.status='freigegeben' GROUP BY ap.artikel) off2 ON acf.artikel = off2.artikel
     LEFT JOIN (SELECT lpi2.artikel, sum(lpi2.menge) as minmenge FROM lager_platz_inhalt  lpi2 INNER JOIN lager_platz lp ON lpi2.lager_platz = lp.id WHERE lp.sperrlager <> 1 GROUP BY lpi2.artikel ) verf ON a.id = verf.artikel
     SET acf.lager_verfuegbar = 
       if(
         ifnull(verf.minmenge,0) - 
         if(
           ifnull(r2.menge,0) > ifnull(off2.menge,0),
           ifnull(r2.menge,0),
           ifnull(off2.menge,0)
           ) <= 0,
         0, 
         verf.minmenge - 
         if(
           ifnull(r2.menge,0) > ifnull(off2.menge,0),
           ifnull(r2.menge,0),
           ifnull(off2.menge,0
             )
           )
         )"
    );
  }

  public function ArtikelLagerInfo($artikel)
  {

    $summe = $this->app->DB->Select("SELECT SUM(lpi.menge) FROM lager_platz_inhalt lpi LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz
        WHERE lpi.artikel='$artikel' AND lp.sperrlager!=1");

    $reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$artikel'");// AND datum >= NOW()");

    $auftraege = $this->app->DB->Select("SELECT SUM(ap.menge) as menge,ap.bezeichnung FROM auftrag_position ap
      LEFT JOIN artikel a ON a.id=ap.artikel LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE a.id='$artikel' AND a.lagerartikel=1 AND auf.status='freigegeben'");

    $liefern= $this->app->DB->Select("SELECT SUM(ap.menge) as menge,ap.bezeichnung FROM auftrag_position ap, auftrag aa, artikel a WHERE a.id=ap.artikel AND aa.id = ap.auftrag AND a.id='$artikel' AND a.lagerartikel=1 AND aa.status='freigegeben'");

    $reserviert_im_versand = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$artikel' AND objekt='lieferschein'");

    $berechnet = $summe -  $auftraege - $reserviert_im_versand;

    $offenebestellungen = $this->app->DB->Select("SELECT SUM(bp.menge-bp.geliefert) as menge FROM bestellung_position bp, bestellung bs, artikel a WHERE a.id=bp.artikel AND bs.id = bp.bestellung AND a.id='$artikel' AND a.lagerartikel=1 AND (bs.status='freigegeben' OR bs.status='versendet') AND bp.menge > bp.geliefert");

    $verkaufte = $auftraege + $reserviert_im_versand;

    $berechnetmitoffenebestellungen = $berechnet + $offenebestellungen;

    if($reserviert=='') {
      $reserviert =0;
    }
    if($liefern <=0) {
      $liefern=0;
    }

    $verkaufbare = $this->app->erp->ArtikelAnzahlVerkaufbar($artikel);
    $summe = round($summe, $this->app->erp->GetLagerNachkommastellen());
    $reserviert = round($reserviert ,$this->app->erp->GetLagerNachkommastellen());
    $liefern = round($liefern ,$this->app->erp->GetLagerNachkommastellen());
    $verkaufte = round($verkaufte ,$this->app->erp->GetLagerNachkommastellen());
    $berechnet = round($berechnet ,$this->app->erp->GetLagerNachkommastellen());
    $verkaufbare = round($verkaufbare ,$this->app->erp->GetLagerNachkommastellen());
    $berechnetmitoffenebestellungen = round($berechnetmitoffenebestellungen ,$this->app->erp->GetLagerNachkommastellen());

    return "<br>Lagerbestand: $summe &nbsp;| &nbsp;Reserviert: $reserviert &nbsp;|&nbsp;Offene Auftr&auml;ge: $liefern&nbsp;| Verkaufte: $verkaufte | Berechneter Bestand: $berechnet | Verkaufbare: $verkaufbare | Berechneter Bestand mit offenen Bestellungen: $berechnetmitoffenebestellungen<br><br>";
  }
}
