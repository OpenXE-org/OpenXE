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
use Xentral\Components\Pdf\Exception\PdfComponentExceptionInterface;
use Xentral\Components\Pdf\PdfMerger;

include '_gen/lieferschein.php';

class Lieferschein extends GenLieferschein
{
  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  static function TableSearch($app, $name, $erlaubtevars)
  {
    $useProjectAb = $app->erp->ModulVorhanden('batches');
    $projectCol = 'p.abkuerzung';
    $abJoin = '';
    if($useProjectAb) {
      $projectCol = 'IFNULL(pab.abkuerzung ,p.abkuerzung)';
      $abJoin = ' LEFT JOIN auftrag AS ab ON l.auftragid = ab.id
       LEFT JOIN projekt AS pab ON ab.projekt = pab.id ';
    }
    switch($name)
    {
      case 'lieferscheineinbearbeitung':
        $allowed['lieferschein'] = array('create', 'list');

        // headings
        $heading = array('', 'Lieferschein', 'Vom', 'Kd-Nr./Lf-Nr.', 'Kunde/Lieferant', 'Land', 'Projekt', 'Versand', 'Art', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '10%', '10%', '35%', '5%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'l.belegnr', 'l.datum', 'if(l.lieferantenretoure=1,lfr.lieferantennummer,adr.kundennummer)', 'l.name', 'l.land', $projectCol, 'l.versandart', 'l.lieferscheinart', 'l.status', 'id');
        $searchsql = array('l.id', 'DATE_FORMAT(l.datum,\'%d.%m.%Y\')', 'l.belegnr', 'if(l.lieferantenretoure=1,lfr.lieferantennummer,adr.kundennummer)', 'l.name', 'l.land', $projectCol, 'l.status', 'l.plz', 'l.id', 'adr.freifeld1', 'l.ihrebestellnummer','l.internebezeichnung','l.versandart');
        $defaultorder = 11; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lieferschein&action=edit&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" .
          "&nbsp;<a href=\"#\" onclick=DeleteDialogLieferschein(\"%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" .

          //             "&nbsp;<a href=\"index.php?module=paketmarke&action=create&frame=false&sid=lieferschein&id=%value%\" class=\"popup\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/stamp.png\" border=\"0\"></a>".
          "&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>".
          "&nbsp;<a href=\"#\" class=\"label-manager\" data-label-column-number=\"5\" data-label-reference-id=\"%value%\" data-label-reference-table=\"lieferscheineinbearbeitung\"><span class=\"label-manager-icon\"></span></a>"."</td></tr></table>";
        $menucol = 10;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 'ENTWURF' as belegnr, DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, if(l.lieferantenretoure=1,lfr.lieferantennummer,adr.kundennummer) as kundennummer,
          CONCAT(" . $app->erp->MarkerUseredit("l.name", "l.useredittimestamp") . ", if(l.internebezeichnung!='',CONCAT('<br><i style=color:#999>',l.internebezeichnung,'</i>'),'')) as kunde,
              l.land as land, $projectCol as projekt, l.versandart as versandart,  
              l.lieferscheinart as art, UPPER(l.status) as status, l.id
                FROM  lieferschein AS l 
                LEFT JOIN projekt AS p ON p.id=l.projekt 
                LEFT JOIN adresse AS lfr ON l.lieferant=lfr.id
                LEFT JOIN adresse AS adr ON l.adresse=adr.id  ".$abJoin;
        $where = " ( l.status='angelegt') " . $app->erp->ProjektRechte('p.id', true, 'l.vertriebid');

        // gesamt anzahl
        $count = "SELECT COUNT(l.id) FROM lieferschein l WHERE ( l.status='angelegt')";
        $moreinfo = true;
        break;
      case 'lieferscheineoffene':
        $allowed['lieferschein'] = array('list');

        // headings
        $heading = array('', 'Lieferschein', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Versand', 'Art', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '10%', '10%', '35%', '5%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'l.belegnr', 'l.datum',  'if(l.lieferantenretoure=1,lfr.lieferantennummer,adr.kundennummer)', 'l.name', 'l.land',$projectCol, 'l.versandart', 'l.lieferscheinart', 'l.status', 'id');
        $searchsql = array('l.id', 'DATE_FORMAT(l.datum,\'%d.%m.%Y\')', 'l.belegnr',  'if(l.lieferantenretoure=1,lfr.lieferantennummer,adr.kundennummer)', 'l.name', 'l.land', $projectCol, 'l.status', 'l.plz', 'l.id', 'adr.freifeld1', 'l.ihrebestellnummer','l.internebezeichnung','l.versandart');
        $defaultorder = 11; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lieferschein&action=edit&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialogLieferschein(\"%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" .

          //             "&nbsp;<a href=\"index.php?module=paketmarke&action=create&frame=false&sid=lieferschein&id=%value%\" class=\"popup\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/stamp.png\" border=\"0\"></a>".
          "&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 10;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, l.belegnr, DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, if(l.lieferantenretoure=1,lfr.lieferantennummer,adr.kundennummer) as kundennummer,
          CONCAT(" . $app->erp->MarkerUseredit("l.name", "l.useredittimestamp") . ", if(l.internebezeichnung!='',CONCAT('<br><i style=color:#999>',l.internebezeichnung,'</i>'),'')) as kunde,
              l.land as land, $projectCol as projekt, l.versandart as versandart,  
              l.lieferscheinart as art, UPPER(l.status) as status, l.id
                FROM  lieferschein AS l 
                LEFT JOIN projekt AS p ON p.id=l.projekt 
                LEFT JOIN adresse AS lfr ON l.lieferant=lfr.id
                LEFT JOIN adresse AS adr ON l.adresse=adr.id  ".$abJoin;
        $where = " l.id!='' AND l.status='freigegeben' " . $app->erp->ProjektRechte('p.id', true, 'l.vertriebid');

        // gesamt anzahl
        $count = "SELECT COUNT(l.id) FROM lieferschein l WHERE l.status='freigegeben'";
        $moreinfo = true;
        break;
      case "lieferschein_seriennummern_assistent":
        $id = $app->Secure->GetGET('id');
        $heading = array('Artikel-Nr.','Artikel','Lagerplatz','Seriennummer','Men&uuml;');
        $width = array('10%','10%','10%','10%', '1%');
        $findcols = array('art.nummer','art.name_de','t2.seriennummer','lag.kurzbezeichnung','t2.id');
        $searchsql = array('art.nummer','art.name_de','t2.seriennummer','lag.kurzbezeichnung' );

          $sql = "SELECT  SQL_CALC_FOUND_ROWS t2.id, art.nummer, art.name_de, lag.kurzbezeichnung,  t2.seriennummer ,CONCAT('this,',t2.menge)
          FROM (
            SELECT ls.id, t.artikel, t.menge, count(s.id) as co, ls.seriennummer, ls.lager_platz 
            FROM lager_seriennummern AS ls 
            INNER JOIN 
            (
              SELECT lp.artikel, sum(menge) as menge 
              FROM lieferschein_position AS lp 
              WHERE lp.lieferschein = '$id' 
              GROUP BY lp.artikel 
            ) AS t ON ls.artikel = t.artikel 
            LEFT JOIN seriennummern s ON s.artikel = t.artikel AND s.lieferschein = '$id' AND s.seriennummer <> ''
            GROUP BY ls.id, t.artikel
          ) AS t2 
          INNER JOIN artikel AS art ON t2.artikel = art.id
          INNER JOIN lager_platz AS lag ON t2.lager_platz = lag.id
          
          ";

        $menu = "<a href=\"#\" onclick=\"uebernehme(%value%);\" ><img src=./themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg border=\"0\"></a>";
        
        $where = "t2.menge > t2.co ";
        $count = "SELECT  count(t2.id)
          FROM (SELECT ls.id, t.artikel, t.menge, count(s.id) as co, ls.seriennummer, ls.lager_platz 
          FROM lager_seriennummern ls 
          INNER JOIN 
          (SELECT lp.artikel, sum(menge) as menge 
          FROM lieferschein_position lp WHERE lp.lieferschein = '$id') t 
          ON ls.artikel = lp.artikel LEFT JOIN seriennummern s ON s.artikel = t.artikel AND s.lieferschein = '$id'  AND s.seriennummer <> ''
          GROUP BY ls.id, t.artikel) t2 
          INNER JOIN artikel art ON t2.artikel = art.id
          INNER JOIN lager_platz lag ON t2.lager_platz = lag.id
           WHERE $where";


        
      break;

    }
    
    $erg = [];
       
    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v)){
        $erg[$v] = $$v;
      }
    }
    return $erg; 
  }

  /** @var Application $app */
  function __construct($app, $intern = false)
  {
    $this->app=$app;
    if($intern)return;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","LieferscheinList");
    $this->app->ActionHandler("create","LieferscheinCreate");
    $this->app->ActionHandler("paketmarke","LieferscheinPaketmarke");
    $this->app->ActionHandler("positionen","LieferscheinPositionen");
    $this->app->ActionHandler("uplieferscheinposition","UpLieferscheinPosition");
    $this->app->ActionHandler("dellieferscheinposition","DelLieferscheinPosition");
    $this->app->ActionHandler("copylieferscheinposition","CopyLieferscheinPosition");
    $this->app->ActionHandler("downlieferscheinposition","DownLieferscheinPosition");
    $this->app->ActionHandler("positioneneditpopup","LieferscheinPositionenEditPopup");
    $this->app->ActionHandler("edit","LieferscheinEdit");
    $this->app->ActionHandler("copy","LieferscheinCopy");
    $this->app->ActionHandler("delete","LieferscheinDelete");
    $this->app->ActionHandler("freigabe","LieferscheinFreigabe");
    $this->app->ActionHandler("abschicken","LieferscheinAbschicken");
    $this->app->ActionHandler("abschliessen","LieferscheinAbschliessen");
    $this->app->ActionHandler("auslagern","LieferscheinAuslagern");
    $this->app->ActionHandler("pdf","LieferscheinPDF");
    $this->app->ActionHandler("inlinepdf","LieferscheinInlinePDF");
    $this->app->ActionHandler("protokoll","LieferscheinProtokoll");
    $this->app->ActionHandler("minidetail","LieferscheinMiniDetail");
    $this->app->ActionHandler("minidetailkommissionierung","LieferscheinMiniDetailkommissionierung");
    $this->app->ActionHandler("editable","LieferscheinEditable");
    $this->app->ActionHandler("livetabelle","LieferscheinLiveTabelle");
    $this->app->ActionHandler("schreibschutz","LieferscheinSchreibschutz");
    $this->app->ActionHandler("positionenetiketten","LieferscheinPositionenEtiketten");
    $this->app->ActionHandler("rechnung","LieferscheinRechnung");
    $this->app->ActionHandler("proformarechnung","LieferscheinProformarechnung");
    $this->app->ActionHandler("dateien","LieferscheinDateien");
    $this->app->ActionHandler("pdffromarchive","LieferscheinPDFfromArchiv");
    $this->app->ActionHandler("archivierepdf","LieferscheinArchivierePDF");

    $this->app->DefaultActionHandler("list");

    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetPOST("adresse");

    if($nummer==''){
      if($id > 0){
        $adresse = $this->app->DB->Select("SELECT a.name FROM lieferschein b INNER JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      }else{
        $adresse = 0;
      }
    }
    else{
      $adresse = $nummer;
    }
    if($id > 0){
      $nummer = $this->app->DB->Select("SELECT b.belegnr FROM lieferschein b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    }else{
      $nummer = '';
    }
    if($nummer=="" || $nummer==0) $nummer="ohne Nummer";

    $this->app->Tpl->Set('UEBERSCHRIFT',"Lieferschein:&nbsp;".$adresse." (".$nummer.")");
    $this->app->Tpl->Set('FARBE',"[FARBE3]");

    $this->app->erp->Headlines('Lieferschein');

    $this->app->ActionHandlerListen($app);
  }

  public function Install(){
    $this->app->erp->RegisterHook('supersearch_detail', 'lieferschein', 'LieferscheinSupersearchDetail');
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function LieferscheinSupersearchDetail($detailQuery, $detailResult)
  {
    if($detailQuery->getGroupKey() === 'deliverynote'){
      $this->LieferscheinSupersearchDetailDeliveryNote($detailQuery, $detailResult);
    }
    if ($detailQuery->getGroupKey() === 'trackingnumber') {
      $this->LieferscheinSupersearchDetailTrackingNumber($detailQuery, $detailResult);
    }
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function LieferscheinSupersearchDetailDeliveryNote($detailQuery, $detailResult)
  {
    if ($detailQuery->getGroupKey() !== 'deliverynote') {
      return;
    }

    $lieferscheinId = $detailQuery->getItemIdentifier();
    $sql = sprintf(
      "SELECT l.id, l.belegnr, l.datum FROM `lieferschein` AS `l` WHERE l.id = '%s' LIMIT 1",
      $this->app->DB->real_escape_string($lieferscheinId)
    );
    $lieferschein = $this->app->DB->SelectRow($sql);
    if (empty($lieferschein)) {
      return;
    }

    $datum = date('d.m.Y', strtotime($lieferschein['datum']));
    $detailResult->setTitle(sprintf('Lieferschein %s <small>vom %s</small>', $lieferschein['belegnr'], $datum));
    $detailResult->addButton('Lieferschein Details', sprintf('index.php?module=lieferschein&action=edit&id=%s', $lieferschein['id']));
    $detailResult->setMiniDetailUrl(sprintf('index.php?module=lieferschein&action=minidetail&id=%s', $lieferschein['id']));
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function LieferscheinSupersearchDetailTrackingNumber($detailQuery, $detailResult)
  {
    if ($detailQuery->getGroupKey() !== 'trackingnumber') {
      return;
    }

    $versandId = $detailQuery->getItemIdentifier();
    $sql = sprintf(
      "SELECT v.lieferschein FROM `versand` AS `v` WHERE v.id = '%s' LIMIT 1",
      $this->app->DB->real_escape_string($versandId)
    );
    $lieferscheinId = $this->app->DB->Select($sql);

    $sql = sprintf(
      "SELECT l.id, l.belegnr, l.datum FROM `lieferschein` AS `l` WHERE l.id = '%s' LIMIT 1",
      $this->app->DB->real_escape_string($lieferscheinId)
    );
    $lieferschein = $this->app->DB->SelectRow($sql);
    if (empty($lieferschein)) {
      return;
    }

    $datum = date('d.m.Y', strtotime($lieferschein['datum']));
    $detailResult->setTitle(sprintf('Lieferschein %s <small>vom %s</small>', $lieferschein['belegnr'], $datum));
    $detailResult->addButton('Lieferschein Details', sprintf('index.php?module=lieferschein&action=edit&id=%s', $lieferschein['id']));
    $detailResult->setMiniDetailUrl(sprintf('index.php?module=lieferschein&action=minidetail&id=%s', $lieferschein['id']));
  }


  function LieferscheinMiniDetailkommissionierung()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    
    $table = new EasyTable($this->app);
    $table->Query("SELECT date_format(datum,'%d.%m.%Y') as Datum,belegnr as Lieferschein FROM lieferschein WHERE kommissionierung = '$id' ORDER BY id");
    echo $table->DisplayNew('return', 'Lieferschein', 'noAction');
    exit;
  }

  /**
   * @param int $deliveryNoteId
   */
  public function archivePdf($deliveryNoteId)
  {
    if($deliveryNoteId <= 0) {
      return;
    }
    $projectId = $this->app->DB->Select(
      sprintf(
        'SELECT projekt FROM lieferschein WHERE id = %d LIMIT 1',
        $deliveryNoteId
      )
    );
    if(class_exists('LieferscheinPDFCustom')) {
      $Brief = new LieferscheinPDFCustom($this->app, $projectId);
    }
    else{
      $Brief = new LieferscheinPDF($this->app, $projectId);
    }
    $Brief->GetLieferschein($deliveryNoteId);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument(1);
    unlink($tmpfile);
    $this->app->DB->Update(
      sprintf(
        'UPDATE lieferschein SET schreibschutz=1 WHERE id = %d',
        $deliveryNoteId
      )
    );
  }
  
  function LieferscheinArchivierePDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $this->archivePdf($id);
    header('Location: index.php?module=lieferschein&action=edit&id='.$id);
    exit;
  }
  
  
  function LieferscheinAbschliessen()  {    

    $id = $this->app->Secure->GetGET("id");

    if($id > 0)
    {
      $this->app->DB->Update("UPDATE lieferschein SET status='abgeschlossen' WHERE id='$id' LIMIT 1");
      $this->app->erp->LieferscheinProtokoll($id,"Lieferschein abgeschlossen");
      $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$id' LIMIT 1");
      if($auftragid && $this->app->erp->ModulVorhanden('produktion') && method_exists($this->app->erp, 'ProduktionEinzelnBerechnen'))
      {
        $produktionen = $this->app->DB->SelectArr("SELECT id FROM produktion WHERE auftragid = '$auftragid'");
        if($produktionen)
        {
          foreach($produktionen as $v)$this->app->erp->ProduktionEinzelnBerechnen($v['id']);
        }
      }
    }
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Lieferschein wurde als abgeschlossen markiert!</div>");
    header("Location: index.php?module=lieferschein&action=list&msg=$msg");
    exit;
  }
  
  function LieferscheinAuslagern()
  {
    $id = (int)$this->app->Secure->GetGET("id");

    if($id > 0)
    {
      if($this->LieferscheinCheck($id))
      {
        // wenn alles lagernd ist und nicht ausgelagert ist
        $standardlager = $this->app->DB->Select("SELECT standardlager FROM lieferschein WHERE id = '$id' LIMIT 1");
        if($standardlager && $this->app->DB->Select("SELECT count(id) FROM lager") <= 1)$standardlager = 0;
        $this->app->erp->LieferscheinAuslagern($id, true, $standardlager, 'lieferschein', 0, true);
        $this->app->erp->RunHook('lieferschein_auslagern', 1, $id);
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Lieferschein wurde ausgelagert!</div>");
      } else {
        if($this->LieferscheinCheck($id, true)) {
          $article = $this->LieferscheinCheck($id, 'article');
          $typ = '';
          if(!empty($article)) {
            $mhd = $article['mindesthaltbarkeitsdatum'] > 0;
            $charge = $article['chargenverwaltung'] > 0;
            $sn = $article['seriennummern'] != '' && $article['seriennummern'] !== 'keine';
            if($mhd && $charge) {
              $typ = 'MHD/Chargen';
            }
            elseif($mhd) {
              $typ = 'MHDs';
            }
            elseif($charge) {
              $typ = 'Chargen';
            }
            elseif($sn) {
              $typ = 'Seriennummern';
            }
          }
          if(!empty($typ)) {
            $msg = $this->app->erp->base64_url_encode(
              "<div class=\"error\">Der Lieferschein kann nicht ausgelagert werden da zu wenig ".$typ." im Artikel ".$article['nummer']." vorhanden sind!</div>"
            );
          }
          else {
            $msg = $this->app->erp->base64_url_encode(
              "<div class=\"error\">Der Lieferschein kann nicht ausgelagert werden da zu wenig MHD/Chargen/Seriennummern in einem Artikel vorhanden sind!</div>"
            );
          }
        }
        else{
          // wenn nur teilmenge ausgelagert werden konnte
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Lieferschein kann nicht ausgelagert werden da nicht alle Artikel vorhanden sind!</div>");
        }
      }

      header("Location: index.php?module=lieferschein&action=edit&id=$id&msg=$msg");
    }
    exit;
  }

  function LieferscheinPaketmarke()
  {
    $id = $this->app->Secure->GetGET("id");

    $versandart = $this->app->DB->Select("SELECT versandart FROM lieferschein WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id = '$id' LIMIT 1");
    $this->LieferscheinMenu();
    $this->app->Tpl->Set('TABTEXT',"Paketmarke");

    $versandart = strtolower($versandart);
    $versandartenmodul = $this->app->DB->SelectArr("SELECT id, modul FROM versandarten WHERE aktiv = 1 AND ausprojekt = 0 AND modul != '' AND type = '".$this->app->DB->real_escape_string($versandart)."' AND (projekt = '$projekt' || projekt = 0) ORDER BY projekt DESC LIMIT 1");
    if($versandartenmodul && is_file(dirname(__DIR__).'/lib/versandarten/'.$versandartenmodul[0]['modul'].'.php'))
    {
      $this->app->erp->Paketmarke('TAB1','lieferschein',"",$versandart);
    }else{
      if($versandart=="dpd")
        $this->app->erp->PaketmarkeDPDEmbedded('TAB1',"lieferschein");
      else if($versandart=="express_dpd")
        $this->app->erp->PaketmarkeDPDEmbedded('TAB1',"lieferschein","express");
      else if($versandart=="export_dpd")
        $this->app->erp->PaketmarkeDPDEmbedded('TAB1',"lieferschein","export");
      else if($versandart=="ups")
        $this->app->erp->PaketmarkeUPSEmbedded('TAB1',"lieferschein");
      else if($versandart=="fedex")
        $this->app->erp->PaketmarkeFEDEXEmbedded('TAB1',"lieferschein");
      else if($versandart=="go")
        $this->app->erp->PaketmarkeGo('TAB1',"lieferschein");
      else {
        $this->app->erp->Paketmarke('TAB1','lieferschein',"","");
      }
        //$this->app->erp->PaketmarkeDHLEmbedded('TAB1',"lieferschein");
    }
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }
  
  function LieferscheinEditable()
  { 
    $this->app->YUI->AARLGEditable();
  }

  function LieferscheinSchreibschutz()
  {

    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE lieferschein SET zuarchivieren='1' WHERE id='$id'");
    $this->app->DB->Update("UPDATE lieferschein SET schreibschutz='0' WHERE id='$id'");
    header("Location: index.php?module=lieferschein&action=edit&id=$id");
    exit;

  }


  function LieferscheinLiveTabelle()
  { 
    $id = $this->app->Secure->GetGET("id");
    $status = $this->app->DB->Select("SELECT status FROM lieferschein WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status=="freigegeben")
    {
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M,
          if(a.porto,'-',if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel) > ap.menge,(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),
              if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel)>0,CONCAT('<font color=red><b>',(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),'</b></font>'),
                if(a.lagerartikel=1,'<font color=red><b>aus</b></font>','kein Lagerartikel' ))) as L
          FROM lieferschein_position ap, artikel a WHERE ap.lieferschein='$id' AND a.id=ap.artikel");
      $artikel = $table->DisplayNew("return","A","noAction");
    } else {
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M
          FROM lieferschein_position ap, artikel a WHERE ap.lieferschein='$id' AND a.id=ap.artikel");
      $artikel = $table->DisplayNew("return","Menge","noAction");
    }
    echo $artikel;
    exit;
  }

  function LieferscheinCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $newid = $this->CopyLieferschein($id);

    header("Location: index.php?module=lieferschein&action=edit&id=$newid");
    exit;
  }


  function Custom($typ)
  {
    return '';
  }

  function LieferscheinIconMenu($id,$prefix="")
  {
    $status = $this->app->DB->Select("SELECT status FROM lieferschein WHERE id='$id' LIMIT 1");
    $lieferantenretoure = $this->app->DB->Select("SELECT lieferantenretoure FROM lieferschein WHERE id='$id' LIMIT 1");

    if($status=="angelegt" || $status=="")
      $freigabe = "<option value=\"freigabe\">Lieferschein freigeben</option>";


    if(($status=="versendet" || $status=="freigegeben") && $lieferantenretoure=="1")
      $abschliessen = "<option value=\"abschliessen\">Lieferschein abschliessen</option>";


    $checkifrgexists = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein='$id' LIMIT 1");

    $optioncustom = $this->Custom('option');
    $casecustom = $this->Custom('case');

    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
    $auslagern = '';
    $erneut = '';
    $casehook = '';
    $optionhook = '';
    $this->app->erp->RunHook('lieferscheiniconmenu_option', 5, $id, $casehook, $optionhook, $status, $prefix);

    $hookoption = '';
    $hookcase = '';
    $this->app->erp->RunHook('Lieferschein_Aktion_option',3, $id, $status, $hookoption);
    $this->app->erp->RunHook('Lieferschein_Aktion_case',3, $id, $status, $hookcase);

    
    $bestellmengelagerartikel = $this->app->DB->Select("SELECT sum(lp.menge) as bestellmenge from lieferschein_position lp INNER JOIN artikel a on a.id=lp.artikel where a.lagerartikel=1 AND lp.lieferschein = '$id'");
    $liefermengelagerartikel = $this->app->DB->Select("SELECT sum(lp.geliefert) as liefermenge from lieferschein_position lp INNER JOIN artikel a on a.id=lp.artikel where a.lagerartikel=1 AND lp.lieferschein = '$id'");
    $liefermengelagerartikel2 = $this->app->DB->Select("SELECT sum(olp.menge) as liefermenge from  lieferschein_position lp INNER JOIN objekt_lager_platz olp ON olp.objekt='lieferschein' AND olp.parameter=lp.id INNER JOIN artikel a on a.id=lp.artikel where a.lagerartikel=1 AND lp.lieferschein = '$id'");
    $lieferscheinpositionen = (int)$this->app->DB->Select("SELECT count(id) from lieferschein_position where lieferschein = '$id'");
    $mengegeliefert = $this->app->DB->Select("SELECT ifnull(sum(geliefert),0) from lieferschein_position where lieferschein = '$id'");
    $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM lieferschein WHERE id='$id' LIMIT 1");

    $mengegeliefert = $mengegeliefert + $this->app->DB->Select("SELECT ifnull(sum(olp.menge),0)+0 FROM objekt_lager_platz olp INNER JOIN lieferschein_position lp ON olp.objekt='lieferschein' AND olp.parameter=lp.id AND lp.lieferschein = '$id'");

    if($mengegeliefert <= 0 && $liefermengelagerartikel > 0  && $schreibschutz=="1" && $status!='angelegt' && $status!='storniert') {
      $auslagern = '<option value="auslagern">Lieferschein auslagern</option>';
    }else{
      //12.07.19 LG lieferscheinlager als kommissionierverfahren zum if hinzugefuegt
      $projektkommissionierverfahren = $this->app->DB->Select("SELECT kommissionierverfahren FROM projekt where id = '$projekt'");
      if($projekt && ($projektkommissionierverfahren == "" || $projektkommissionierverfahren == "rechnungsmail" || $projektkommissionierverfahren == "lieferschein" || $projektkommissionierverfahren == "lieferscheinscan" || $projektkommissionierverfahren == "lieferscheinlager" || $projektkommissionierverfahren == "lieferscheinlagerscan"))
      {
        if(($bestellmengelagerartikel != $liefermengelagerartikel && $bestellmengelagerartikel != $liefermengelagerartikel2) && $status!='angelegt' && $status!='storniert') {
            $auslagern = '<option value="auslagern">Lieferschein auslagern</option>';
        }
      }
    }

    if($status!="angelegt" && $lieferantenretoure!="1")
    {
      $alsrechnung = "<option value=\"rechnung\">als Rechnung weiterf&uuml;hren</option>";
      if($this->app->erp->RechteVorhanden('lieferschein', 'proformarechnung') && $this->app->erp->ModulVorhanden('proformarechnung'))
      {
        $alsrechnung .= "<option value=\"proformarechnung\">als Proformarechnung weiterf&uuml;hren</option>";
      }
    }

    if($this->app->erp->RechteVorhanden('belegeimport', 'belegcsvexport'))
    { 
      $casebelegeimport = "case 'belegeimport':  window.location.href='index.php?module=belegeimport&action=belegcsvexport&cmd=lieferschein&id=%value%'; break;";
      $optionbelegeimport = "<option value=\"belegeimport\">Export als CSV</option>";
    }
    
    $etiketten_positionen = $this->app->DB->Select("SELECT etiketten_positionen FROM projekt WHERE id='$projekt' LIMIT 1");
    if($etiketten_positionen  > 0)
      $etiketten = "<option value=\"positionenetiketten\">Positionen als Etiketten</option>";
    $casestorno = "case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=lieferschein&action=delete&id=%value%'; break;";
    if($this->app->DB->Select("SELECT olp.id FROM objekt_lager_platz olp INNER JOIN lieferschein_position pos ON olp.parameter = pos.id AND olp.objekt = 'lieferschein' WHERE  pos.lieferschein = '$id' LIMIT 1"))$casestorno = "case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else if(!confirm('Artikel wieder einlagern?')) window.location.href='index.php?module=lieferschein&action=delete&id=%value%';else window.location.href='index.php?module=lieferschein&action=delete&cmd=einlagern&id=%value%'; break;";
    
    if($checkifrgexists>0) $extendtext = "HINWEIS: Es existiert bereits eine Rechnung zu diesem Lieferschein! "; else $extendtext="";
    $menu ="
      <script type=\"text/javascript\">
      function onchangelieferschein(cmd)
      {
        switch(cmd)
        {
          $casestorno
          case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=lieferschein&action=copy&id=%value%'; break;
          case 'pdf': window.location.href='index.php?module=lieferschein&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
          case 'positionenetiketten': window.location.href='index.php?module=lieferschein&action=positionenetiketten&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
          case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;
          case 'freigabe': window.location.href='index.php?module=lieferschein&action=freigabe&id=%value%';  break;
          case 'abschliessen': if(!confirm('Wirklich abschliessen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=lieferschein&action=abschliessen&id=%value%'; break;
          case 'auslagern': if(!confirm('Wirklich$erneut auslagern?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=lieferschein&action=auslagern&id=%value%'; break;
          case 'rechnung': if(!confirm('".$extendtext."Wirklich als Rechnung weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=lieferschein&action=rechnung&id=%value%'; break;
          case 'proformarechnung': if(!confirm('".$extendtext."Wirklich als Proformarechnung weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=lieferschein&action=proformarechnung&id=%value%'; break;
          $casecustom
          $casehook
          $hookcase 
          $casebelegeimport
        }

      }
    </script>

      &nbsp;Aktion:&nbsp;<select id=\"aktion$prefix\" onchange=\"onchangelieferschein(this.value)\"> 
      <option>bitte w&auml;hlen ...</option>
      <option value=\"storno\">Lieferschein stornieren</option>
      <option value=\"copy\">Lieferschein kopieren</option>
      $freigabe
      <option value=\"abschicken\">Lieferschein abschicken</option>
      $abschliessen
      $auslagern
      $alsrechnung
      $optionbelegeimport
      <option value=\"pdf\">PDF &ouml;ffnen</option>
      $etiketten
      $optioncustom
      $optionhook
      $hookoption 
      </select>&nbsp;

    <a href=\"index.php?module=lieferschein&action=pdf&id=%value%\" title=\"PDF\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
      <!--        <a href=\"index.php?module=lieferschein&action=edit&id=%value%\" title=\"Bearbeiten\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
      <a onclick=\"if(!confirm('Wirklich stornieren?')) return false; else window.location.href='index.php?module=lieferschein&action=delete&id=%value%';\" title=\"Stornieren\">
      <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=lieferschein&action=copy&id=%value%';\" title=\"Kopieren\">
      <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>-->";
    //$tracking = $this->AuftragTrackingTabelle($id);

    $menu = str_replace('%value%',$id,$menu);
    return $menu;
  }
  
  function LieferscheinPDFfromArchiv()
  {
    $id = $this->app->Secure->GetGET("id");
    $archiv = $this->app->DB->Select("SELECT table_id from pdfarchiv where id = '$id' LIMIT 1");
    if($archiv)
    {
      $projekt = $this->app->DB->Select("SELECT projekt from lieferschein where id = '".(int)$archiv."'");
    }
    if(class_exists('LieferscheinPDFCustom'))
    {
      if($archiv)$Brief = new LieferscheinPDFCustom($this->app,$projekt);
    }else{
      if($archiv)$Brief = new LieferscheinPDF($this->app,$projekt);
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

  function LieferscheinMiniDetail($parsetarget="",$menu=true)
  {
    $id = $this->app->Secure->GetGET("id");
    if($id > 0){
      $auftragArr = $this->app->DB->SelectArr("SELECT * FROM lieferschein WHERE id='$id' LIMIT 1");
    }
    $kundennummer = '';
    $projekt = 0;
    $kundenname = '';
    $lieferantenretoure = '';
    $lieferantenretoureinfo = '';
    if(!empty($auftragArr)) {
      $kundennummer = $auftragArr[0]['kundennummer'];
      $projektid = $auftragArr[0]['projekt'];
      $projekt = '';
      if($projektid){
        $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='{$auftragArr[0]['projekt']}' LIMIT 1");
      }
      $kundenname = '';
      if($auftragArr[0]['adresse'] > 0){
        $kundenname = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");
      }
      $lieferantenretoure = $auftragArr[0]['lieferantenretoure'];
      $lieferantenretoureinfo = $auftragArr[0]['lieferantenretoureinfo'];
    }


    $this->app->Tpl->Set('LIEFERANTENRETOUREINFO',$lieferantenretoureinfo);

    if($lieferantenretoure!='1') {
      $this->app->Tpl->Set('LIEFERANTENRETOUREINFOSTART','<!--');
      $this->app->Tpl->Set('LIEFERANTENRETOUREINFOENDE','<!--');
    }	

    $this->app->Tpl->Set('LIEFERSCHEINID',$id);

    $this->app->Tpl->Set('KUNDE',"<a href=\"index.php?module=adresse&action=edit&id=".$auftragArr[0]['adresse']."\" target=\"_blank\">".$kundennummer."</a> ".$kundenname);
    if($this->app->erp->RechteVorhanden('projekt','dashboard')){
      $this->app->Tpl->Set('PROJEKT', "<a href=\"index.php?module=projekt&action=dashboard&id=" . $auftragArr[0]['projekt'] . "\" target=\"_blank\">$projekt</a>");
    }
    else{
      $this->app->Tpl->Set('PROJEKT', $projekt);
    }
    $this->app->Tpl->Set('ZAHLWEISE',$auftragArr[0]['zahlungsweise']);
    $this->app->Tpl->Set('STATUS',$auftragArr[0]['status']);


    if($auftragArr[0]['auftragid'] > 0){
      $orderRow = $this->app->DB->SelectRow(
        "SELECT belegnr, projekt FROM auftrag WHERE id='".$auftragArr[0]['auftragid']."' LIMIT 1"
      );
      if($this->app->erp->ModulVorhanden('batches')) {
        $abProjekt = $this->app->DB->Select(
          sprintf(
            'SELECT abkuerzung FROM projekt WHERE id = %d',
            $orderRow['projekt']
          )
        );
        if(!empty($abProjekt)){
          $this->app->Tpl->Set('PROJEKT', $abProjekt);
        }
      }
      $belegnr_auftrag = $orderRow['belegnr'];

      $this->app->Tpl->Set('AUFTRAG',"<a href=\"index.php?module=auftrag&action=edit&id=".$auftragArr[0]['auftragid']."\" target=\"_blank\">$belegnr_auftrag</a>
        &nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=".$auftragArr[0]['auftragid']."\" target=\"blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Auftrag PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=auftrag&action=edit&id=".$auftragArr[0]['auftragid']."\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Auftrag bearbeiten\" border=\"0\"></a>
      ");
    }else{
      $this->app->Tpl->Set('AUFTRAG','-');
    }

    
    if($auftragArr[0]['auftragid'] > 0){
      $rechnung = $this->app->DB->SelectArr(
        ($auftragArr[0]['auftragid']?
          "SELECT 
          CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),'&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>') as rechnung
          FROM rechnung r WHERE r.auftragid='".$auftragArr[0]['auftragid']."' AND r.auftragid <> '0'
          union 
          SELECT 
          CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),'&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>') as rechnung
          FROM rechnung r INNER JOIN sammelrechnung_position s ON r.id = s.rechnung INNER JOIN auftrag_position p ON s.auftrag_position_id = p.id WHERE p.auftrag='".$auftragArr[0]['auftragid']."'
          union ":"")."
          SELECT 
          CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),'&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>') as rechnung
          FROM rechnung r INNER JOIN sammelrechnung_position s ON r.id = s.rechnung 
          INNER JOIN lieferschein_position lp ON lp.id = s.lieferschein_position_id
          WHERE lp.lieferschein='$id'
          "
      );

    }
    else{
      if($auftragArr[0]['rechnungid'] > 0){

        $rechnung = $this->app->DB->SelectArr("SELECT 
          CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),'&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>') as rechnung
          FROM rechnung r WHERE r.id='".$auftragArr[0]['rechnungid']."' AND r.id <> '0'
          union 
          
          SELECT 
          CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),'&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>') as rechnung
          FROM rechnung r INNER JOIN sammelrechnung_position s ON r.id = s.rechnung 
          INNER JOIN lieferschein_position lp ON lp.id = s.lieferschein_position_id
          WHERE lp.lieferschein='$id'
          ");

      }

    }

    $crechnung = !empty($rechnung)?count($rechnung):0;
    if($crechnung>0)
    {
      for($li=0;$li<$crechnung;$li++)
      {
        $this->app->Tpl->Add('RECHNUNG',$rechnung[$li]['rechnung']);
        if($li<count($rechnung))
          $this->app->Tpl->Add('RECHNUNG',"<br>");
      }
    }
    else
      $this->app->Tpl->Set('RECHNUNG',"-");
    if($auftragArr[0]['ust_befreit']==0)
      $this->app->Tpl->Set('STEUER',"Inland");
    else if($auftragArr[0]['ust_befreit']==1)
      $this->app->Tpl->Set('STEUER',"EU-Lieferung");
    else
      $this->app->Tpl->Set('STEUER',"Export");


    if($menu)
    {
      $menu = $this->LieferscheinIconMenu($id);
      $this->app->Tpl->Set('MENU',$menu);
    }
    // ARTIKEL

    $lieferscheinarr = $this->app->DB->SelectRow("SELECT * FROM  lieferschein WHERE id='$id' LIMIT 1");

    $status = $lieferscheinarr['status'];//$this->app->DB->Select("SELECT status FROM lieferschein WHERE id='$id' LIMIT 1");
    $projekt = $lieferscheinarr['projekt'];
    $projektlager = $this->app->DB->Select("SELECT projektlager FROM projekt WHERE id = $projekt");
    $auftrag = $lieferscheinarr['auftragid'];
    $standardlager = $lieferscheinarr['standardlager'];//$this->app->DB->Select("SELECT standardlager FROM auftrag WHERE id = $auftrag LIMIT 1");
    $positionIdsToArticle = $id <= 0?[]:$this->app->DB->SelectPairs(
      sprintf(
        'SELECT id, artikel FROM lieferschein_position WHERE lieferschein = %d',
        $id
      )
    );
    if(empty($positionIdsToArticle)) {
      $positionIdsToArticle = [ 0 => 0];
    }
    //$artikelarr = array_m $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lieferschein_position WHERE lieferschein = $id");
    $artikelids = array_unique(array_values($positionIdsToArticle));
    $positionIds = array_keys($positionIdsToArticle);

    $artikelidsImplode = implode(',', $artikelids);
    $positionIdsImplode = implode(',', $positionIds);

    $table = new EasyTable($this->app);

    $sql = "SELECT if(CHAR_LENGTH(ap.beschreibung) > 0,CONCAT(ap.bezeichnung,' *'),ap.bezeichnung) as artikel, 
      CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', 
      ap.nummer,'</a>'
      ) as Nummer,
      a.gewicht as gewicht, 
      TRIM(ap.menge)+0 as Menge,
          if(
            a.porto,
            '-',
            if(
              IFNULL(lpi2.menge,0) >= ap.menge AND a.lagerartikel=1, 
              IF(
                ROUND(ap2.offen,7) > 0 AND 
                  ROUND(IFNULL(lpi2.menge,0) - IFNULL(r.menge,0) + IFNULL(r2.menge,0),7) < ROUND(ap2.offen,7) 
                  AND ROUND(IFNULL(r2.menge,0),7) < ROUND(ap2.offen,7),
                CONCAT('<b style=\"color:red;\">',".$this->app->erp->FormatMenge('IFNULL(lpi2.menge,0)').",'</b>'),
                ".$this->app->erp->FormatMenge('IFNULL(lpi2.menge,0)')."
              ),
              if(
                IFNULL(lpi2.menge,0)>0 AND a.lagerartikel=1,
                CONCAT('<b style=\"color:red;\">',".$this->app->erp->FormatMenge('lpi2.menge').",'</b>'),
                if(
                  a.lagerartikel=1,
                  '<b style=\"color:red;\">aus</b>',
                  'kein Lagerartikel'
                )
              )
            )
          ) as Lager,
                
          CONCAT(".$this->app->erp->FormatMenge('IFNULL(r2.menge,0)').",' von ',"
            .$this->app->erp->FormatMenge('IFNULL(r.menge,0)')
            .",' (Gesamtres.)')as `Res. für Kunde`
         
          FROM lieferschein_position AS ap 
          INNER JOIN artikel AS a ON ap.artikel = a.id
          INNER JOIN (
            SELECT sum(menge-geliefert) as offen, artikel 
            FROM lieferschein_position 
            WHERE id IN (".$positionIdsImplode.") 
            GROUP BY artikel
          ) as ap2 ON a.id = ap2.artikel
          LEFT JOIN (
            SELECT sum(lpi.menge) as menge,lpi.artikel 
            FROM lager_platz_inhalt AS lpi 
            INNER JOIN lager_platz AS lp 
              ON lpi.lager_platz = lp.id AND IFNULL(lp.sperrlager,0) = 0 AND IFNULL(lp.autolagersperre,0) = 0
            INNER JOIN lager AS `lag` ON lp.lager = `lag`.id 
            WHERE lpi.artikel IN (".$artikelidsImplode.")
            ".($standardlager?" AND `lag`.id = $standardlager ":($projektlager?" AND `lag`.projekt = $projekt ":''))."
            GROUP BY lpi.artikel
          ) AS lpi2 ON a.id = lpi2.artikel
          LEFT JOIN (
            SELECT SUM(menge) as menge,artikel 
            FROM lager_reserviert 
            WHERE artikel IN (".$artikelidsImplode.")
            ".($projektlager || ($standardlager && false)?" AND projekt = $projekt ":'')."
            GROUP BY artikel 
          ) r ON a.id = r.artikel
          LEFT JOIN (
            SELECT SUM(menge) as menge,artikel 
            FROM lager_reserviert
            WHERE ((objekt = 'lieferschein' AND parameter = $id) "
              .($auftrag > 0?" OR (objekt = 'auftrag' AND parameter = $auftrag) ":'').") 
             AND artikel IN (".$artikelidsImplode.")
            GROUP BY artikel 
          ) r2 ON a.id = r2.artikel
          WHERE ap.id IN (".$positionIdsImplode.") AND ap.lieferschein='$id' 
          ORDER BY ap.sort, ap.id";

      $table->Query($sql);
      $gewichtanzeigen = false;
      if($table->datasets) {
        foreach($table->datasets as $k => $row) {
          if($row['gewicht'] > 0)$gewichtanzeigen = true;
        }
      }
      if(!$gewichtanzeigen)
      {
        foreach($table->datasets as $k => $row)
        {
          unset($table->datasets[$k]['gewicht']);
        }
        $table->headings[2] = $table->headings[3];
        $table->headings[3] = $table->headings[4];
        $table->headings[4] = $table->headings[5];
        unset($table->headings[5]);
      }
          
      $artikel = $table->DisplayNew("return","Res. für Kunde","noAction","false",0,0,false);


    $this->app->Tpl->Set('ARTIKEL','<div id="artikeltabellelive'.$id.'">'.$artikel.'</div>');

    if($auftragArr[0]['lieferantenretoure']=="1" && ($auftragArr[0]['status']=="versendet" || $auftragArr[0]['status']=="freigegeben"))
      $this->app->Tpl->Add('ARTIKEL',"<br><center><input type=\"button\" value=\"Lieferschein abschliessen\" onclick=\"window.open('index.php?module=lieferschein&action=abschliessen&id=$id')\"></center>");

    if($auftragArr[0]['belegnr']=="0" || $auftragArr[0]['belegnr']=="") $auftragArr[0]['belegnr'] = "ENTWURF";
    $this->app->Tpl->Set('BELEGNR',$auftragArr[0]['belegnr']);
    $this->app->Tpl->Set('LIEFERSCHEIN',$auftragArr[0]['id']);

    $this->app->Tpl->Set('VERSANDART',$auftragArr[0]['versandart']);

    $tracking = $this->app->DB->SelectArr("SELECT
       if(v.tracking_link IS NOT NULL AND v.tracking_link != '', CONCAT(UPPER(versandunternehmen), ':<a href=\"', v.tracking_link, '\">', v.tracking, '</a>'),
         if(versandunternehmen = 'dhlexpress' AND l.land = 'DE' AND v.tracking != '', CONCAT(UPPER(versandunternehmen), ':<a href=\"https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?piececode=', v.tracking, '\" target=\"_blank\">', v.tracking, '</a>'),
           if(versandunternehmen = 'dhlexpress' AND l.land != 'DE' AND v.tracking != '', CONCAT(UPPER(versandunternehmen), ':<a href=\"https://www.dhl.com/en/hidden/component_library/express/local_express/dhl_de_tracking/de/sendungsverfolgung_dhlde.html?AWB=', v.tracking, '&brand=DHL\" target=\"_blank\">', v.tracking, '</a>'),
             if( (versandunternehmen='dhl' OR versandunternehmen='intraship' OR versandunternehmen LIKE '%dhl%') AND v.tracking!='',          CONCAT(UPPER(versandunternehmen),':<a href=\"http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=',v.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),        
               if(versandunternehmen LIKE '%dpd%',CONCAT(UPPER(versandunternehmen),':<a href=\"https://tracking.dpd.de/parcelstatus/?locale=de_DE&query=',v.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),
                 if(versandunternehmen LIKE '%ups%' AND v.tracking != '', CONCAT(UPPER(versandunternehmen),':<a href=\"https://www.ups.com/track?loc=de_DE&tracknum=',v.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),
                   if(versandunternehmen LIKE '%gls%' AND v.tracking != '', CONCAT(UPPER(versandunternehmen),':<a href=\"https://www.gls-group.eu/276-I-PORTAL-WEB/content/GLS/DE03/DE/5004.htm?txtRefNo=',v.tracking,'\" target=\"_blank\">',v.tracking,'</a>'),
                     if(versandunternehmen LIKE '%postch%' AND v.tracking != '', CONCAT(UPPER(versandunternehmen),':<a href=\"https://service.post.ch/EasyTrack/submitParcelData.do?formattedParcelCodes=',v.tracking,'&from_directentry=True&directSearch=false&p_language=de&VTI-GROUP=1&lang=de&service=ttb\" target=\"_blank\">',v.tracking,'</a>'),  
                       if(v.tracking!='',
                         CONCAT(UPPER(versandunternehmen),': ',v.tracking),'nicht vorhanden')
                     )
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
                   if(versandunternehmen LIKE '%postch%' AND v.tracking != '', CONCAT(UPPER(versandunternehmen),':<a href=\"https://service.post.ch/EasyTrack/submitParcelData.do?formattedParcelCodes=',v.tracking,'&from_directentry=True&directSearch=false&p_language=de&VTI-GROUP=1&lang=de&service=ttb\" target=\"_blank\">',v.tracking,'</a>'), 
                     if(vp.tracking!='',
                       CONCAT(UPPER(versandunternehmen),': ',vp.tracking),'nicht vorhanden')
                   )
                 )
               )
             )
           )
         )
       ) as versand3,
        v.tracking as tracking2, vp.tracking as tracking3
      FROM versand AS v
      LEFT JOIN versandpakete AS vp ON v.id = vp.versand 
      LEFT JOIN lieferschein AS l ON v.lieferschein=l.id WHERE l.id='$id'"
    );

    $ctracking = !empty($tracking)?count($tracking):0;
    for($counti=0;$counti < $ctracking; $counti++)
      if($tracking[$counti]['tracking2']!="")
      {
        if($counti == 0 || $tracking[$counti]['tracking2'] != $tracking[$counti-1]['tracking2'])
        {
          $tmp[]=$tracking[$counti]['versand2'];
        }
        if($tracking[$counti]['tracking3'] != '')
        {
          $tmp[]=$tracking[$counti]['versand3'];
        }
      }

    $this->app->Tpl->Set('TRACKING',implode(', ',$tmp));


    $returnOrders = (array)$this->app->DB->SelectArr(
      sprintf(
        'SELECT ro.id, ro.belegnr, ro.status
        FROM `retoure` AS `ro`
        LEFT JOIN `auftrag` AS `o` ON ro.auftragid = o.id
        LEFT JOIN `lieferschein` AS `dn` ON o.id = dn.auftragid
        WHERE ro.lieferscheinid = %d OR dn.id = %d
        ORDER BY ro.id',
        $id, $id
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

    $this->app->Tpl->Set('LIEFERADRESSE',$this->Lieferadresse($auftragArr[0]['id']));
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM lieferschein_protokoll WHERE lieferschein='$id' ORDER by zeit DESC");
    $tmp->DisplayNew('PROTOKOLL',"Protokoll","noAction");

    if(class_exists('LieferscheinPDFCustom'))
    {
      $Brief = new LieferscheinPDFCustom($this->app,$auftragArr[0]['projekt']);
    }else{
      $Brief = new LieferscheinPDF($this->app,$auftragArr[0]['projekt']);
    }
    
    $Dokumentenliste = $Brief->getArchivedFiles($id, 'lieferschein');
    if($Dokumentenliste)
    {
      $tmp3 = new EasyTable($this->app);
      $tmp3->headings = array('Datum','Belegnr','Bearbeiter','Men&uuml;');
      foreach($Dokumentenliste as $k => $v)
      {
        if(!$v['erstesoriginal'])
        {
          $tmpr['datum'] = date('d.m.Y H:i:s',strtotime($v['zeitstempel']));
          $tmpr['belegnr'] = str_replace('.pdf','',$v['file']);
          $tmpr['belegnr'] = substr($tmpr['belegnr'],strrpos($tmpr['belegnr'],'_')+1);
          if(isset($v['belegnummer']) && $v['belegnummer'])$tmpr['belegnr'] = $v['belegnummer'];
          $tmpr['bearbeiter'] = $v['bearbeiter'];
          $tmpr['menu'] = '<a href="index.php?module=lieferschein&action=pdffromarchive&id='.$v['id'].'"><img src="themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/pdf.svg" /></a>';
          $tmp3->datasets[] = $tmpr;
        }
      }
      
      $tmp3->DisplayNew('PDFARCHIV','Men&uuml;',"noAction");
    }

    $nettogewicht = $this->app->erp->LieferscheinNettoGewicht($id);
    if($nettogewicht!="") {
      $nettogewicht = number_format($nettogewicht, 2, ',','.');
      $gewichtbezeichnung = $this->app->erp->Firmendaten('gewichtbezeichnung');
      if($gewichtbezeichnung == '')$gewichtbezeichnung = 'Kg';

      $this->app->Tpl->Set("GEWICHT", $nettogewicht . " ".$gewichtbezeichnung);
    }
  
    if($parsetarget=='')
    { 
      $this->app->Tpl->Output("lieferschein_minidetail.tpl");
      $this->app->ExitXentral();
    }

    $this->app->Tpl->Parse($parsetarget,"lieferschein_minidetail.tpl");
  }



  function LieferscheinFreigabe($id="")
  {
    if($id<=0)
    {
      $id = $this->app->Secure->GetGET("id");
      $freigabe= $this->app->Secure->GetGET("freigabe");
    } else {
      $intern = true;
      $freigabe=$intern;
    }

    $this->app->Tpl->Set('TABTEXT',"Freigabe");

    $this->app->erp->CheckVertrieb($id,"lieferschein");
    $this->app->erp->CheckBearbeiter($id,"lieferschein");

    if($freigabe==$id)
    {
      //$projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$id' LIMIT 1");
      if($belegnr=="")
      {
        $this->app->erp->BelegFreigabe("lieferschein",$id);
        if($intern) return 1;
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Lieferschein wurde freigegeben und kann jetzt versendet werden!</div>");
        header("Location: index.php?module=lieferschein&action=edit&id=$id&msg=$msg");
        exit;
      }
      if($intern) return 0;
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Lieferschein wurde bereits freigegeben!</div>");
      header("Location: index.php?module=lieferschein&action=edit&id=$id&msg=$msg");
      exit;

    }

    $name = $this->app->DB->Select("SELECT a.name FROM lieferschein b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    $summe = $this->app->DB->Select("SELECT FORMAT(SUM(menge*preis),2) FROM lieferschein_position
        WHERE lieferschein='$id'");
    $waehrung = $this->app->DB->Select("SELECT waehrung FROM lieferschein_position
        WHERE lieferschein='$id' LIMIT 1");


    $extra = $this->app->erp->CheckboxEntwurfsmodus("lieferschein",$id);

    if($this->app->erp->Firmendaten("oneclickrelease")=="1" && $extra=="")
    {
      $this->app->Location->execute("index.php?module=lieferschein&action=freigabe&id=$id&freigabe=$id");
    } else {
      $this->app->Tpl->Set('TAB1',"<div class=\"info\">Soll der Lieferschein an <b>$name</b> jetzt freigegeben werden? <input type=\"button\" class=\"btnImportantLarge\" value=\"Jetzt freigeben\" onclick=\"window.location.href='index.php?module=lieferschein&action=freigabe&id=$id&freigabe=$id'\">&nbsp;$extra
        </div>");
    }

    $this->LieferscheinMenu();
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }


  function LieferscheinAbschicken()
  {
    $this->LieferscheinMenu();
    $this->app->erp->DokumentAbschicken();
  }



  function LieferscheinDelete($id = null)
  {
    if(is_null($id))
    {
      $intern = false;
      $id = $this->app->Secure->GetGET("id");
    }else $intern = true;

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM lieferschein WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM lieferschein WHERE id='$id' LIMIT 1");
    $objekt_lager_platz = $this->app->DB->SelectArr("SELECT olp.id, olp.menge, olp.lager_platz, olp.artikel, olp.parameter FROM 
      lieferschein_position lp INNER JOIN  objekt_lager_platz olp ON lp.id = olp.parameter AND olp.objekt = 'lieferschein' AND lp.lieferschein = '$id'");
    $cmdEinlagern = $this->app->Secure->GetGET('cmd') === 'einlagern';
    if($cmdEinlagern)
    {
      if($objekt_lager_platz)
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id = '$id' LIMIT 1");
        foreach($objekt_lager_platz as $olp)  {
          $this->app->erp->LagerEinlagern($olp['artikel'],$olp['menge'],$olp['lager_platz'],$projekt,
                'Stornierung Lieferschein '.$belegnr,$this->app->User->GetName(),'','lieferschein',$id);
          $beleg_chargesnmhd = $this->app->DB->SelectArr("SELECT * FROM `beleg_chargesnmhd` WHERE doctype = 'lieferschein' AND doctypeid = '$id' AND pos = '".$olp['parameter']."'");
          $seriennummern = $this->app->DB->SelectArr("SELECT * FROM seriennummern WHERE lieferschein = '$id' AND lieferscheinpos = '".$olp['parameter']."'");
          if($seriennummern) {
            foreach($seriennummern as $sn)  {
              $belegesnarr['sn'][] = array('menge'=>1, 'value'=>$sn['seriennummer'],'table'=>'seriennummern','id'=>$sn['id']);
            }
          }
          if($beleg_chargesnmhd) {
            $belegesnarr = null;
            foreach($beleg_chargesnmhd as $bc) {
              $belegesnarr[$bc['type']][] = array('menge'=>$bc['menge'], 'value'=>$bc['wert'],'table'=>'beleg_chargesnmhd','id'=>$bc['id']);
            }
          }
          if(isset($belegesnarr['sn'])){
            foreach($belegesnarr['sn'] as $v) {
              if(isset($belegesnarr['charge']) && isset($belegesnarr['mhd']))
              {
                $charge = '';
                $mhd = '';
                foreach($belegesnarr['charge'] as $k2 => $v2)
                {
                  if($v2['menge'] > 1)
                  {
                    $charge = $v2['value'];
                    $belegesnarr['charge'][$k2]['menge'] -= 1;
                    break;
                  }
                  if($v2['menge'] == 1)
                  {
                    $charge = $v2['value'];
                    unset($belegesnarr['charge'][$k2]);
                    break;
                  }
                }
                foreach($belegesnarr['mhd'] as $k2 => $v2)
                {
                  if($v2['menge'] > 1)
                  {
                    $mhd = $v2['value'];
                    $belegesnarr['mhd'][$k2]['menge'] -= 1;
                    break;
                  }
                  if($v2['menge'] == 1)
                  {
                    $mhd = $v2['value'];
                    unset($belegesnarr['mhd'][$k2]);
                    break;
                  }
                }
                if(isset($v['table']) &&  $v['table'] === 'seriennummern' && !empty($v['id']))
                {
                  $this->app->DB->Delete("DELETE FROM seriennummern WHERE id = '".$v['id']."' LIMIT 1");
                }
                elseif(isset($v['table']) && $v['table'] === 'beleg_chargesnmhd' && !empty($v['id'])){
                  $this->app->DB->Delete("DELETE FROM beleg_chargesnmhd WHERE id = '".$v['id']."' LIMIT 1");
                }
                if(method_exists($this->app->erp,'AddSeriennummerLager')) {
                  $this->app->erp->AddSeriennummerLager($olp['artikel'], $olp['lager_platz'], $v['value'],'Stornierung Lieferschein '.$belegnr,  '', $mhd, $charge,'lieferschein', $id);
                }
                if(method_exists($this->app->erp,'AddMindesthaltbarkeitsdatumLagerOhneBewegung')) {
                  $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($olp['artikel'], 1,$olp['lager_platz'], $mhd, $charge,0,'lieferschein',$id,'Stornierung Lieferschein '.$belegnr);
                }
                if(method_exists($this->app->erp,'AddChargeLagerOhneBewegung')) {
                  $this->app->erp->AddChargeLagerOhneBewegung($olp['artikel'],1, $olp['lager_platz'],$mhd,$charge,'Stornierung Lieferschein '.$belegnr,0,'lieferschein',$id);
                }
              }
              elseif(isset($belegesnarr['charge']))
              {
                $charge = '';
                $mhd = '';
                foreach($belegesnarr['charge'] as $k2 => $v2)  {
                  if($v2['menge'] > 1)
                  {
                    $charge = $v2['value'];
                    $belegesnarr['charge'][$k2]['menge'] -= 1;
                    break;
                  }
                  if($v2['menge'] == 1) {
                    $charge = $v2['value'];
                    unset($belegesnarr['charge'][$k2]);
                    break;
                  }
                }
                if(isset($v['table']) &&  $v['table'] === 'seriennummern' && !empty($v['id']))
                {
                  $this->app->DB->Delete("DELETE FROM seriennummern WHERE id = '".$v['id']."' LIMIT 1");
                }
                elseif(isset($v['table']) && $v['table'] === 'beleg_chargesnmhd' && !empty($v['id'])){
                  $this->app->DB->Delete("DELETE FROM beleg_chargesnmhd WHERE id = '".$v['id']."' LIMIT 1");
                }
                if(method_exists($this->app->erp,'AddSeriennummerLager')) {
                  $this->app->erp->AddSeriennummerLager($olp['artikel'],1, $olp['lager_platz'], $v['value'],'Stornierung Lieferschein '.$belegnr,  '', $mhd, $charge,'lieferschein',$id);
                }
                if(method_exists($this->app->erp,'AddChargeLagerOhneBewegung')) {
                  $this->app->erp->AddChargeLagerOhneBewegung($olp['artikel'], 1,$olp['lager_platz'],$mhd,$charge,'Stornierung Lieferschein '.$belegnr,0,'lieferschein',$id);
                }
              }
              elseif(isset($belegesnarr['mhd']))
              {
                $charge = '';
                $mhd = '';
                foreach($belegesnarr['mhd'] as $k2 => $v2)
                {
                  if($v2['menge'] > 1) {
                    $mhd = $v2['value'];
                    $belegesnarr['mhd'][$k2]['menge'] -= 1;
                    break;
                  }
                  if($v2['menge'] == 1) {
                    $mhd = $v2['value'];
                    unset($belegesnarr['mhd'][$k2]);
                    break;
                  }
                }
                if(isset($v['table']) &&  $v['table'] === 'seriennummern' && !empty($v['id'])) {
                  $this->app->DB->Delete("DELETE FROM seriennummern WHERE id = '".$v['id']."' LIMIT 1");
                }
                elseif(isset($v['table']) && $v['table'] === 'beleg_chargesnmhd' && !empty($v['id'])){
                  $this->app->DB->Delete("DELETE FROM beleg_chargesnmhd WHERE id = '".$v['id']."' LIMIT 1");
                }
                if(method_exists($this->app->erp,'AddSeriennummerLager')) {
                  $this->app->erp->AddSeriennummerLager($olp['artikel'], $olp['lager_platz'], $v['value'],'Stornierung Lieferschein '.$belegnr,  '', $mhd, $charge,'lieferschein', $id);
                }
                if(method_exists($this->app->erp,'AddMindesthaltbarkeitsdatumLagerOhneBewegung')) {
                  $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($olp['artikel'],1, $olp['lager_platz'], $mhd, $charge, 0, 'lieferschein', $id,'Stornierung Lieferschein '.$belegnr);
                }
              }
              else{
                if(isset($v['table']) &&  $v['table'] === 'seriennummern' && !empty($v['id']))
                {
                  $this->app->DB->Delete("DELETE FROM seriennummern WHERE id = '".$v['id']."' LIMIT 1");
                }
                elseif(isset($v['table']) && $v['table'] === 'beleg_chargesnmhd' && !empty($v['id'])){
                  $this->app->DB->Delete("DELETE FROM beleg_chargesnmhd WHERE id = '".$v['id']."' LIMIT 1");
                }
                if(method_exists($this->app->erp,'AddSeriennummerLager')) {
                  $this->app->erp->AddSeriennummerLager($olp['artikel'], $olp['lager_platz'], $v['value'],'Stornierung Lieferschein '.$belegnr, '','','','lieferschein', $id);
                }
              }
            }
          }
          else{
            if(isset($belegesnarr['charge']) && isset($belegesnarr['mhd'])) {
              foreach($belegesnarr['charge'] as $v2) {
                $nochmenge = $v2['menge'];
                foreach($belegesnarr['mhd'] as $k3 => $v3) {
                  if($v3['menge'] <= 0) {
                    continue;
                  }
                  if($nochmenge <= 0) {
                    break;
                  }
                  if($nochmenge == $v3['menge']) {
                    $mhd = $v3['value'];
                    $charge = $v2['value'];
                    if(method_exists($this->app->erp,'AddMindesthaltbarkeitsdatumLagerOhneBewegung')) {
                      $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($olp['artikel'], $nochmenge,$olp['lager_platz'], $mhd, $charge, 0, 'lieferschein', $id,'Stornierung Lieferschein '.$belegnr);
                    }
                    if(method_exists($this->app->erp,'AddChargeLagerOhneBewegung')) {
                      $this->app->erp->AddChargeLagerOhneBewegung($olp['artikel'],$nochmenge, $olp['lager_platz'],$mhd,$charge,'Stornierung Lieferschein '.$belegnr, 0, 'lieferschein', $id);
                    }
                    unset($belegesnarr['mhd'][$k3]);
                    break;
                  }
                  if($nochmenge > $v3['menge']) {
                    $mhd = $v3['value'];
                    $charge = $v2['value'];
                    if(method_exists($this->app->erp,'AddMindesthaltbarkeitsdatumLagerOhneBewegung')) {
                      $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($olp['artikel'], $v3['menge'],$olp['lager_platz'], $mhd, $charge, 0, 'lieferschein', $id,'Stornierung Lieferschein '.$belegnr);
                    }
                    if(method_exists($this->app->erp,'AddChargeLagerOhneBewegung')) {
                      $this->app->erp->AddChargeLagerOhneBewegung($olp['artikel'],$v3['menge'], $olp['lager_platz'],$mhd,$charge,'Stornierung Lieferschein '.$belegnr, 0, 'lieferschein', $id);
                    }
                    unset($belegesnarr['mhd'][$k3]);
                    $nochmenge -= $v3['menge'];
                    if($nochmenge <= 0) {
                      break;
                    }
                    continue;
                  }

                  if($nochmenge < $v3['menge']) {
                    $mhd = $v3['value'];
                    $charge = $v2['value'];
                    if(method_exists($this->app->erp,'AddMindesthaltbarkeitsdatumLagerOhneBewegung')) {
                      $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($olp['artikel'], $nochmenge,$olp['lager_platz'], $mhd, $charge, 0, 'lieferschein', $id,'Stornierung Lieferschein '.$belegnr);
                    }
                    if(method_exists($this->app->erp,'AddChargeLagerOhneBewegung')) {
                      $this->app->erp->AddChargeLagerOhneBewegung($olp['artikel'],$nochmenge, $olp['lager_platz'],$mhd,$charge,'Stornierung Lieferschein '.$belegnr, 0, 'lieferschein', $id);
                    }
                    $belegesnarr['mhd'][$k3]['menge'] -= $nochmenge;
                    break;
                  }
                }
              }
            }
            elseif($belegesnarr['charge']) {
              foreach($belegesnarr['charge'] as $v2) {
                if($v2['menge'] <= 0) {
                  continue;
                }
                if(method_exists($this->app->erp,'AddChargeLagerOhneBewegung')) {
                  $this->app->erp->AddChargeLagerOhneBewegung($olp['artikel'],$v2['menge'], $olp['lager_platz'],'',$v2['value'],'Stornierung Lieferschein '.$belegnr, 0, 'lieferschein', $id);
                }
              }
            }
            elseif($belegesnarr['mhd']) {
              foreach($belegesnarr['mhd'] as $v2) {
                if($v2['menge'] <= 0) {
                  continue;
                }
                if(method_exists($this->app->erp, 'AddMindesthaltbarkeitsdatumLagerOhneBewegung')) {
                  $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($olp['artikel'], $v2['menge'], $olp['lager_platz'], $v2['value'],'',0,'lieferschein', $id,'Stornierung Lieferschein '.$belegnr);
                }
              }
            }
          }
          $this->app->DB->Delete("DELETE FROM objekt_lager_platz WHERE id = '".$olp['id']."' LIMIT 1");
        }
      }
    }

    if($cmdEinlagern || empty($objekt_lager_platz)) {
      $sns = $this->app->DB->SelectArr(
        sprintf(
          'SELECT bc.id, bc.wert, bc.lagerplatz, lp.artikel, bc.wert2, bc.type2
                  FROM `beleg_chargesnmhd` AS bc
                  INNER JOIN lieferschein_position AS lp ON bc.pos = lp.id AND bc.`doctypeid` = %d
                  WHERE bc.`doctype` = \'lieferschein\' AND bc.`doctypeid` = %d AND bc.`type` = \'sn\' AND bc.lagerplatz > 0 AND bc.wert <> \'\'',
          $id, $id
        )
      );
      if(!empty($sns)) {
        foreach($sns as $sn) {
          $mhd = '';
          $charge = '';
          if($sn['type2'] === 'charge') {
            $charge = $sn['wert2'];
          }elseif($sn['type2'] === 'mhd') {
            $mhd = $sn['wert2'];
          }
          $this->app->erp->AddSeriennummerLager($sn['artikel'], $sn['lagerplatz'], $sn['wert'],'Stornierung Lieferschein '.$belegnr,  '', $mhd, $charge,'lieferschein', $id);
          $this->app->DB->Delete(sprintf('DELETE FROM `beleg_chargesnmhd` WHERE id = %d LIMIT 1', $sn['id']));
        }
      }
    }
    
    if($belegnr=="0" || $belegnr=="")
    {
      $this->DeleteLieferschein($id);
      if($intern)return;
      $belegnr="ENTWURF";
      $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Der Lieferschein \"$name\" ($belegnr) wurde gel&ouml;scht!</div>");
      //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
      header("Location: index.php?module=lieferschein&action=list&msg=$msg");
      exit;
    }
    if($status=="storniert")
    {
      $maxbelegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM lieferschein");
      if(0)//$maxbelegnr == $belegnr)
      {
        $this->app->DB->Delete("DELETE FROM lieferschein_position WHERE lieferschein='$id'");
        $this->app->DB->Delete("DELETE FROM lieferschein_protokoll WHERE lieferschein='$id'");
        $this->app->DB->Delete("DELETE FROM lieferschein WHERE id='$id'");
        $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Lieferschein \"$name\" ($belegnr) wurde ge&ouml;scht !</div>");
      } else
      {
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Lieferschein \"$name\" ($belegnr) kann nicht storniert werden da er bereits storniert ist!</div>");
      }
      if($intern)return;
      header("Location: index.php?module=lieferschein&action=list&msg=$msg");
      exit;
    }
    else {
      $this->app->DB->Update("UPDATE lieferschein SET status='storniert' WHERE id='$id' LIMIT 1");
      $checkversand = $this->app->DB->Select("SELECT id FROM versand WHERE lieferschein='$id' LIMIT 1");
      if($checkversand > 0)
      {
        if($id > 0) $this->app->DB->Delete("UPDATE versand SET abgeschlossen='1' WHERE lieferschein='$id' AND lieferschein > 0");
        $this->app->erp->LieferscheinProtokoll($id,"Lieferschein aus Versandzentrum entfernt");
      }

      $this->app->erp->LieferscheinProtokoll($id,"Lieferschein storniert");
      $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Das Lieferschein \"$name\" ($belegnr) wurde storniert!</div>");
    }
    if($intern)return;
    header("Location: index.php?module=lieferschein&action=list&msg=$msg#tabs-1");
    exit;
  }

  function LieferscheinProtokoll()
  {
    $this->LieferscheinMenu();
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Set('TABTEXT',"Protokoll");
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM lieferschein_protokoll WHERE lieferschein='$id' ORDER by zeit DESC");
    $tmp->DisplayNew('TAB1',"Protokoll","noAction");

    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }

  function LieferscheinRechnung()  {    
    $id = $this->app->Secure->GetGET("id");    
    $newid = $this->app->erp->WeiterfuehrenLieferscheinZuRechnung($id);
    $this->app->erp->LieferscheinProtokoll($id,"Lieferschein als Rechnung weitergeführt");


    header("Location: index.php?module=rechnung&action=edit&id=$newid");
    exit;
  }

  function LieferscheinProformarechnung()  {    
    $id = $this->app->Secure->GetGET("id");    
    $proformaRechnung = $this->app->erp->LoadModul('proformarechnung');
    if(!empty($proformaRechnung) && method_exists($proformaRechnung, 'CreateProformaInvoiceAndPrint')) {
      $tmp = $this->app->DB->SelectArr("SELECT adresse,projekt FROM lieferschein WHERE id='$id'");
      $newid = $proformaRechnung->createProformaInvoiceFromDeliveryNotes([$id], $tmp[0]['adresse'], $tmp[0]['projekt']);
    }
    $this->app->erp->LieferscheinProtokoll($id,"Lieferschein als Proformarechnung weitergeführt");

    header("Location: index.php?module=proformarechnung&action=edit&id=$newid");
    exit;
  }


  function LieferscheinInlinePDF()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->LieferscheinNeuberechnen($id);
    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
    $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM lieferschein WHERE id='$id' LIMIT 1");
    $frame = $this->app->Secure->GetGET("frame");

    if($frame=="")
    {
      if(class_exists('LieferscheinPDFCustom'))
      {
        $Brief = new LieferscheinPDFCustom($this->app,$projekt);
      }else{
        $Brief = new LieferscheinPDF($this->app,$projekt); 
      }
      $Brief->GetLieferschein($id);
      $Brief->inlineDocument($schreibschutz);
    } else {
      $file = urlencode("../../../../index.php?module=lieferschein&action=inlinepdf&id=$id");
      echo "<iframe width=\"100%\" height=\"100%\" style=\"height:calc(100vh - 110px)\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
      exit;
    }
  }

   
  function LieferscheinPDF()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->LieferscheinNeuberechnen($id);
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
    $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM lieferschein WHERE id='$id' LIMIT 1");
    //    if(is_numeric($belegnr) && $belegnr!=0)
    {
      if(class_exists('LieferscheinPDFCustom'))
      {
        $Brief = new LieferscheinPDFCustom($this->app,$projekt);
      }else{
        $Brief = new LieferscheinPDF($this->app,$projekt);
      }
      $Brief->GetLieferschein($id);
      $Brief->displayDocument($schreibschutz); 
    }// else
    // $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Noch nicht freigegebene Lieferscheinen k&ouml;nnen nicht als PDF betrachtet werden.!</div>");


    $this->LieferscheinList();
  }


  function LieferscheinMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM lieferschein WHERE id='$id' LIMIT 1");

    if($belegnr=="0" || $belegnr=="") $belegnr ="(Entwurf)";
    // $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Lieferschein $belegnr");
    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$name Lieferschein $belegnr");




    //    $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=edit&id=$id","Lieferscheindaten");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=lieferschein&action=positionen&id=$id\">Positionen</a></li>");

    // status bestell
    $status = $this->app->DB->Select("SELECT status FROM lieferschein WHERE id='$id' LIMIT 1");

    if ($status=="angelegt")
    {
      $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=freigabe&id=$id","Freigabe");
    }

    $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=edit&id=$id","Details");
    $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=paketmarke&id=$id","Paketmarke");
    //    $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=abschicken&id=$id","Abschicken / Protokoll");
    //    $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=protokoll&id=$id","Protokoll");
    $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    
    $this->app->erp->RunMenuHook('lieferschein');
  }

  function LieferscheinPositionen()
  {
    $this->app->YUI->AARLGPositionen(false);
    return;
  }

  function CopyLieferscheinPosition()
  {
    $this->app->YUI->SortListEvent("copy","lieferschein_position","lieferschein");
    $this->LieferscheinPositionen();
  }
  
  function DelLieferscheinPosition()
  {
    $this->app->YUI->SortListEvent("del","lieferschein_position","lieferschein");
    $this->LieferscheinPositionen();
  }

  function UpLieferscheinPosition()
  {
    $this->app->YUI->SortListEvent("up","lieferschein_position","lieferschein");
    $this->LieferscheinPositionen();
  }

  function DownLieferscheinPosition()
  {
    $this->app->YUI->SortListEvent("down","lieferschein_position","lieferschein");
    $this->LieferscheinPositionen();
  }


  function LieferscheinPositionenEditPopup()
  {
    $id = $this->app->Secure->GetGET("id");

    // nach page inhalt des dialogs ausgeben
    $filename = "widgets/widget.lieferschein_position_custom.php";
    if(is_file($filename))
    {
      include_once($filename);
      $widget = new WidgetLieferschein_positionCustom($this->app,'PAGE');
    } else {
      $widget = new WidgetLieferschein_position($this->app,'PAGE');
    }
    $sid= $this->app->DB->Select("SELECT lieferschein FROM lieferschein_position WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=lieferschein&action=positionen&id=$sid");
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }


  function LieferscheinEdit()
  {
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");

    $sid = $this->app->Secure->GetGET("sid");
    $cmd = $this->app->Secure->GetGET("cmd");
    
    if($cmd == 'dadown')
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
      exit;
    }
    
    if($cmd == 'daup')
    {
      $erg['status'] = 0;
      $daid = $this->app->Secure->GetPOST("da_id");
      $check = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.id = '$daid' and d.geloescht <> 1 LIMIT 1");
      if($check)
      {
        $sort = $check[0]['sort']-1;
        if($sort > 0)
        {
          $check2 = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.objekt like 'lieferschein' AND ds.sort = '$sort' AND d.geloescht <> 1 AND ds.parameter = '$id' LIMIT 1");
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
      exit;
    }
    
    if($this->app->erp->VertriebAendern("lieferschein",$id,$cmd,$sid))
      return;
    if($this->app->erp->InnendienstAendern("lieferschein",$id,$cmd,$sid))
      return;

    if($this->app->erp->DisableModul("lieferschein",$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->LieferscheinMenu();
      return;
    }



    $this->app->YUI->AARLGPositionen();

    $this->app->erp->CheckVertrieb($id,"lieferschein");
    $this->app->erp->CheckBearbeiter($id,"lieferschein");
    if($id > 0){
      $lieferscheinarr = $this->app->DB->SelectRow("SELECT * FROM lieferschein WHERE id='$id' LIMIT 1");
    }
    if(!empty($lieferscheinarr)){
      $nummer = $lieferscheinarr['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$id' LIMIT 1");
      $projekt = $lieferscheinarr['projekt'];//$this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
      $adresse = $lieferscheinarr['adresse'];//$this->app->DB->Select("SELECT adresse FROM lieferschein WHERE id='$id' LIMIT 1");
      $lieferant = $lieferscheinarr['lieferant'];//$this->app->DB->Select("SELECT lieferant FROM lieferschein WHERE id='$id' LIMIT 1");
      $lieferantenretoure = $lieferscheinarr['lieferantenretoure'];//$this->app->DB->Select("SELECT lieferantenretoure FROM lieferschein WHERE id='$id' LIMIT 1");
      $schreibschutz = $lieferscheinarr['schreibschutz'];//$this->app->DB->Select("SELECT schreibschutz FROM lieferschein WHERE id='$id' LIMIT 1");
      $status = $lieferscheinarr['status'];//$this->app->DB->Select("SELECT status FROM lieferschein WHERE id='$id' LIMIT 1");
    }else{
      $nummer = '';
      $projekt = 0;
      $adresse = 0;
      $lieferant = null;
      $lieferantenretoure = null;
      $schreibschutz = 0;
      $status = '';
    }
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    if($lieferantenretoure=="1" && $lieferant<=0)
    {
      $this->app->Tpl->Add('JAVASCRIPT','$(document).ready(function() { if(document.getElementById("adresse"))document.getElementById("adresse").focus(); });');
      $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Pflichtfeld! Bitte geben Sie eine Lieferanten-Nr. an!</div>");
    } else if ($adresse <=0 && $lieferantenretoure!="1")
    {
      $this->app->Tpl->Add('JAVASCRIPT','$(document).ready(function() { if(document.getElementById("adresse"))document.getElementById("adresse").focus(); });');
      $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Pflichtfeld! Bitte geben Sie eine Kunden-Nr. an!</div>");
    }	


    $bestellmengelagerartikel = $this->app->DB->Select("SELECT sum(lp.menge) as bestellmenge from lieferschein_position lp INNER JOIN artikel a on a.id=lp.artikel where a.lagerartikel=1 AND lp.lieferschein = '$id'");
    $liefermengelagerartikel = $this->app->DB->Select("SELECT sum(lp.geliefert) as liefermenge from lieferschein_position lp INNER JOIN artikel a on a.id=lp.artikel where a.lagerartikel=1 AND lp.lieferschein = '$id'");
    $anzahllagerartikel = $this->app->DB->Select("SELECT count(lp.id) as liefermenge from lieferschein_position lp INNER JOIN artikel a on a.id=lp.artikel where a.lagerartikel=1 AND lp.lieferschein = '$id'");
    $lieferscheinpositionen = (int)$this->app->DB->Select("SELECT count(id) from lieferschein_position where lieferschein = '$id'");
    $mengegeliefert = $this->app->DB->Select("SELECT ifnull(sum(geliefert),0) from lieferschein_position where lieferschein = '$id'");

    $mengegeliefert = $mengegeliefert + $this->app->DB->Select("SELECT ifnull(sum(olp.menge),0)+0 FROM objekt_lager_platz olp INNER JOIN lieferschein_position lp ON olp.objekt='lieferschein' AND olp.parameter=lp.id AND lp.lieferschein = '$id'");

    if($mengegeliefert <= 0 && $liefermengelagerartikel <=0 && $schreibschutz=="1" && $status!='angelegt' && $status!='storniert' && $anzahllagerartikel > 0) {
      $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Der Lieferschein wurde noch nicht ausgelagert! <input type=\"button\" value=\"Jetzt auslagern\" onclick=\"window.location.href='index.php?module=lieferschein&action=auslagern&id=$id'\"></div>");
    }else{
      $projektkommissionierverfahren = $this->app->DB->Select("SELECT kommissionierverfahren FROM projekt where id = '$projekt'");
      if($projekt && ($projektkommissionierverfahren == "" || $projektkommissionierverfahren == "rechnungsmail" || $projektkommissionierverfahren == "lieferschein" || $projektkommissionierverfahren == "lieferscheinscan"))
      {
        if($mengegeliefert <= 0 && $liefermengelagerartikel <=0  && $status!='angelegt' && $status!='storniert' && $anzahllagerartikel > 0) {
            $this->app->Tpl->Add('MESSAGE', "<div class=\"warning\">Der Lieferschein wurde noch nicht ausgelagert! <input type=\"button\" value=\"Jetzt auslagern\" onclick=\"window.location.href='index.php?module=lieferschein&action=auslagern&id=$id'\"></div>");
        }else{
          if($liefermengelagerartikel > 0 && ($bestellmengelagerartikel != $liefermengelagerartikel)){
            $this->app->Tpl->Add('MESSAGE', "<div class=\"warning\">Die Lieferscheinpositionen wurden nachträglich verändert. Ein erneutes Auslagern ist nicht möglich.</div>");
          }
        }
      }
    }

    $deliveryData = $this->app->DB->SelectRow(
      "SELECT l.status, l.zuarchivieren, l.schreibschutz 
      FROM `lieferschein` AS `l` 
      WHERE l.id='$id' 
      LIMIT 1"
    );

    $status = $deliveryData['status'];
    $zuArchivieren = $deliveryData['zuarchivieren'];
    $schreibschutz = $deliveryData['schreibschutz'];

    if($id > 0 && $zuArchivieren == 1 && $schreibschutz == 1) {
      $this->app->erp->PDFArchivieren('lieferschein', $id, true);
    }

    if($status != "angelegt" && $status != "angelegta" && $status != "a")
    {
      $Brief = new Briefpapier($this->app);
      if($Brief->zuArchivieren($id, "lieferschein"))
      {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Der Lieferschein ist noch nicht archiviert! Bitte versenden oder manuell archivieren.
<input type=\"button\" onclick=\"if(!confirm('Soll das Dokument archiviert werden?')) return false;else window.location.href='index.php?module=lieferschein&action=archivierepdf&id=$id';\" value=\"Manuell archivieren\" /> <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('lieferschein',$id)\"></div>");
      }elseif(!$this->app->DB->Select("SELECT versendet FROM lieferschein WHERE id = '$id' LIMIT 1"))
      {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Der Lieferschein wurde noch nicht versendet! <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('lieferschein',$id)\"></div>");
      }
    }
    
    if($schreibschutz!="1")// && $this->app->erp->RechteVorhanden("lieferschein","schreibschutz"))
    {
      $this->app->erp->AnsprechpartnerButton($adresse);
      $this->app->erp->LieferadresseButton($adresse);
      $this->app->erp->AdresseAlsAnsprechpartner($adresse);

      $this->app->erp->LieferscheinNeuberechnen($id);
    }


    //$this->LieferscheinMiniDetail(MINIDETAIL,false);
    $this->app->Tpl->Set('ICONMENU',$this->LieferscheinIconMenu($id));
    $this->app->Tpl->Set('ICONMENU2',$this->LieferscheinIconMenu($id,2));


    if($nummer!="")
    {
      $this->app->Tpl->Set('NUMMER',$nummer);
      if($this->app->erp->RechteVorhanden("adresse","edit"))
        $this->app->Tpl->Set('KUNDE',"&nbsp;&nbsp;&nbsp;Kd-Nr. <a href=\"index.php?module=adresse&action=edit&id=$adresse\" target=\"_blank\">".$kundennummer."</a>");
      else
        $this->app->Tpl->Set('KUNDE',"&nbsp;&nbsp;&nbsp;Kd-Nr. ".$kundennummer);
    }

    if($schreibschutz=="1" && $this->app->erp->RechteVorhanden("lieferschein","schreibschutz"))
    {
      $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Dieser Lieferschein ist schreibgesch&uuml;tzt und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml;r diesen Lieferschein wirklich entfernt werden?')) return false;else window.location.href='index.php?module=lieferschein&action=schreibschutz&id=$id';\"></div>");
      //      $this->app->erp->CommonReadonly();
    }
    if($schreibschutz=="1")
      $this->app->erp->CommonReadonly();

    $status= $this->app->DB->Select("SELECT status FROM lieferschein WHERE id='$id' LIMIT 1");
    if($status=="")
      $this->app->DB->Update("UPDATE lieferschein SET status='angelegt' WHERE id='$id' LIMIT 1");

    if($schreibschutz != '1'){
      $this->app->Tpl->Set('BUTTON_UEBERNEHMEN', '
        <input type="button" value="&uuml;bernehmen" onclick="if(!confirm(\'Soll der neue Kunde wirklich &uuml;bernommen werden? Es werden alle Felder &uuml;berladen.\')) return false;else document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
        ');


      $this->app->Tpl->Set('BUTTON_UEBERNEHMEN2', '
        <input type="button" value="&uuml;bernehmen" onclick="if(!confirm(\'Soll der neue Lieferant wirklich &uuml;bernommen werden? Es werden alle Felder &uuml;berladen.\')) return false;else document.getElementById(\'uebernehmen2\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen2" name="uebernehmen2" value="0">
        ');
    }


    // immer wenn sich der lieferant genändert hat standartwerte setzen
    if($this->app->Secure->GetPOST("adresse")!="")
    {
      $tmp = $this->app->Secure->GetPOST("adresse");
      $kundennummer = $this->app->erp->FirstTillSpace($tmp);
      $filter_projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id = '$id' LIMIT 1");
      //if($filter_projekt)$filter_projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id= '$filter_projekt' and eigenernummernkreis = 1 LIMIT 1");
      $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer'  AND kundennummer!='' AND geloescht=0 ".$this->app->erp->ProjektRechte("projekt")." ORDER by ".($filter_projekt?" projekt = '$filter_projekt' DESC, ":"")." projekt LIMIT 1");

      $uebernehmen =$this->app->Secure->GetPOST("uebernehmen");
      if($uebernehmen=="1") // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
      {
        $this->app->DB->Update("UPDATE lieferschein SET lieferantenretoure=0,lieferant=0 WHERE id='$id'");
        $this->LoadLieferscheinStandardwerte($id,$adresse);
        header("Location: index.php?module=lieferschein&action=edit&id=$id");
        exit;
      }
    }

    if($this->app->Secure->GetPOST("lieferant")!="")
    {
      $tmplieferant = $this->app->Secure->GetPOST("lieferant");
      $lieferantennummer = $this->app->erp->FirstTillSpace($tmplieferant);

      $adresselieferant =  $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='$lieferantennummer' AND lieferantennummer!=''  AND geloescht=0 LIMIT 1");

      $uebernehmen2 =$this->app->Secure->GetPOST("uebernehmen2");
      if($uebernehmen2=="1") // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
      {
        $this->app->DB->Update("UPDATE lieferschein SET lieferantenretoure=1 WHERE id='$id'");
        $this->LoadLieferscheinStandardwerte($id,$adresselieferant,true);
        header("Location: index.php?module=lieferschein&action=edit&id=$id");
        exit;
      }

    }


    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $table->Query("SELECT nummer as Nummer, bezeichnung, menge,vpe as VPE
        FROM lieferschein_position
        WHERE lieferschein='$id'");
    $table->DisplayNew('POSITIONEN',"VPE","noAction");

    $status= $this->app->DB->Select("SELECT status FROM lieferschein WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('STATUS',"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");


    $this->app->Tpl->Set('AKTIV_TAB1',"selected");
    parent::LieferscheinEdit();
    if($id > 0 && $this->app->DB->Select(
        sprintf(
          'SELECT id FROM lieferschein WHERE schreibschutz =1  AND zuarchivieren = 1 AND id = %d',
          $id
        )
      )
    ) {
      $this->app->erp->PDFArchivieren('lieferschein', $id, true);
    }
    $this->app->erp->MessageHandlerStandardForm();

    if($this->app->Secure->GetPOST("weiter")!="")
    {
      header("Location: index.php?module=lieferschein&action=positionen&id=$id");
      exit;
    }
    $this->LieferscheinMenu();

  }

  /**
   * @param int $deliveryNoteId
   *
   * @return bool
   */
  public function printPostitionLabels($deliveryNoteId)
  {
    if(empty($deliveryNoteId)) {
      return false;
    }
    $projectId = $this->app->DB->Select(
      sprintf(
        'SELECT projekt FROM lieferschein WHERE id=%d LIMIT 1',
        $deliveryNoteId
      )
    );
    if(empty($projectId)) {
      return false;
    }
    $projectRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT etiketten_positionen, etiketten_art, etiketten_drucker, etiketten_sort  
        FROM projekt 
        WHERE id = %d',
        $projectId
      )
    );
    if(empty($projectRow) || $projectRow['etiketten_positionen'] <= 0) {
      return false;
    }
    /** @var Etiketten $obj */
    $obj = $this->app->erp->LoadModul('etiketten');
    if(empty($obj) || !method_exists($obj, 'LieferscheinPositionenDrucken')) {
      return false;
    }
    $obj->LieferscheinPositionenDrucken(
      $deliveryNoteId,
      $projectRow['etiketten_drucker'],
      $projectRow['etiketten_art'],
      $projectRow['etiketten_sort']
    );

    return true;
  }

  public function LieferscheinPositionenEtiketten()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->printPostitionLabels($id);
    $this->app->Location->execute('index.php?module=lieferschein&action=edit&id='.$id);
  }

  function Lieferadresse($id)
  {
    $data = $this->app->DB->SelectArr("SELECT * FROM lieferschein WHERE id='$id' LIMIT 1");

    foreach($data[0] as $key=>$value)
    {
      if($data[0][$key]!="" && $key!="abweichendelieferadresse" && $key!="land" && $key!="plz" && $key!="lieferland" && $key!="lieferplz") $data[0][$key] = $data[0][$key]."<br>";
    }


    $rechnungsadresse = $data[0]['name']."".$data[0]['ansprechpartner']."".$data[0]['abteilung']."".$data[0]['unterabteilung'].
      "".$data[0]['strasse']."".$data[0]['adresszusatz']."".$data[0]['land']."-".$data[0]['plz']." ".$data[0]['ort'];
    return "<table width=\"100%\">
      <tr valign=\"top\"><td width=\"50%\"><b>Lieferschein:</b><br><br>$rechnungsadresse</td></tr></table>";
  }

  function LieferscheinCreate()
  {
    //    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Lieferschein");
    $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $anlegen = $this->app->Secure->GetGET("anlegen");

    if($this->app->erp->Firmendaten("schnellanlegen")=="1" && $anlegen!="1")
    {
      header("Location: index.php?module=lieferschein&action=create&anlegen=1");
      exit;
    }


    if($anlegen != "")
    {
      $id = $this->app->erp->CreateLieferschein();
      $this->app->erp->LieferscheinProtokoll($id,"Lieferschein angelegt");

      header("Location: index.php?module=lieferschein&action=edit&id=$id");

      exit;
    }
    $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">M&ouml;chten Sie eine Lieferschein jetzt anlegen? &nbsp;
        <input type=\"button\" onclick=\"window.location.href='index.php?module=lieferschein&action=create&anlegen=1'\" value=\"Ja - Lieferschein jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set('TAB1',"
        <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><b style=\"font-size: 14pt\">Lieferscheine in Bearbeitung</b>
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

    $this->app->YUI->TableSearch('AUFTRAGE','lieferscheineinbearbeitung', 'show','','',basename(__FILE__), __CLASS__);
    /*
       $table = new EasyTable($this->app);
       $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as beleg, name, status, id
       FROM lieferschein WHERE status='angelegt' order by datum DESC, id DESC");
       $table->DisplayNew(AUFTRAGE, "<a href=\"index.php?module=lieferschein&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
       <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=lieferschein&action=delete&id=%value%';\">
       <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
       <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=lieferschein&action=copy&id=%value%';\">
       <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
       ");
     */

    $this->app->Tpl->Set('TABTEXT',"Lieferschein anlegen");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");



    //parent::LieferscheinCreate();
  }

  function LieferscheinDateien()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->LieferscheinMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
    $this->app->YUI->DateiUpload('PAGE',"Lieferschein",$id);
  }



  function LieferscheinList()
  {
    if($this->app->Secure->GetPOST('ausfuehren') && $this->app->erp->RechteVorhanden('lieferschein', 'edit'))
    {
      $drucker = $this->app->Secure->GetPOST('seldrucker');
      $aktion = $this->app->Secure->GetPOST('sel_aktion');
      $auswahl = $this->app->Secure->GetPOST('auswahl');
      if($drucker > 0) {
        $this->app->erp->BriefpapierHintergrundDisable($drucker);
      }
      if(is_array($auswahl)) {
        $selectedIds = [];
        foreach($auswahl as $v) {
          $v = (int)$v;
          if($v > 0) {
            $selectedIds[] = $v;
          }
          $selectedIds = array_unique($selectedIds);
        }
        switch($aktion) {
          case 'offen':
            if(!empty($selectedIds)) {
              $this->app->DB->Update(
                sprintf(
                  "UPDATE `lieferschein` 
                  SET `versendet` = 0, `status` = 'freigegeben', `schreibschutz` = 0 
                  WHERE `id` IN (%s)",
                  implode(',' , $selectedIds)
                )
              );
            }
          break;
          case 'storniert':
            $deliveryNotes = empty($selectedIds)? null: $this->app->DB->SelectArr(
              sprintf(
                'SELECT id, projekt, status FROM lieferschein WHERE id IN (%s)',
                implode(',', $selectedIds)
              )
            );
            if(empty($deliveryNotes)) {
              $deliveryNotes = [];
            }
            foreach($deliveryNotes as $deliveryNote) {
              $deliveryNoteId = $deliveryNote['id'];
              $projekt = $deliveryNote['projekt'];
              $status = $deliveryNote['status'];
              if($status==='angelegt' || $status=='') {
                $this->app->erp->DeleteLieferschein($deliveryNoteId);
                continue;
              }
              if(class_exists('LieferscheinPDFCustom')) {
                $Brief = new LieferscheinPDFCustom($this->app,$projekt);
              }
              else{
                $Brief = new LieferscheinPDF($this->app,$projekt);
              }
              $Brief->GetLieferschein($deliveryNoteId);
              $tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              $this->app->erp->LieferscheinProtokoll($v,'Lieferschein storniert');
              $this->app->DB->Update(
                sprintf(
                  "UPDATE lieferschein 
                  SET status='storniert',schreibschutz=1, versendet = 1 
                  WHERE id = %d AND status!='angelegt' 
                  LIMIT 1",
                  $deliveryNoteId
                )
              );
              @unlink($tmpfile);
            }
          break;
          case 'versendet':
            $deliveryNotes = empty($selectedIds)?[]: $this->app->DB->SelectPairs(
              sprintf(
                'SELECT id, projekt FROM lieferschein WHERE id in (%s)', implode(',', $selectedIds)
              )
            );
            foreach($deliveryNotes as $deliveryNoteId => $projectId) {
              if(class_exists('LieferscheinPDFCustom')) {
                $Brief = new LieferscheinPDFCustom($this->app,$projectId);
              }
              else{
                $Brief = new LieferscheinPDF($this->app,$projectId);
              }
              $this->app->DB->Update(
                sprintf(
                  "UPDATE lieferschein 
                  SET schreibschutz=1, versendet = 1, status='versendet' 
                  WHERE id = %d
                  AND status != 'storniert'
                  LIMIT 1",
                  $deliveryNoteId
                )
              );
              $this->app->erp->LieferscheinProtokoll($deliveryNoteId, 'Lieferschein versendet');
              $Brief->GetLieferschein($deliveryNoteId);
              $tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              @unlink($tmpfile);
            }
          break;
          case 'drucken':
            if($drucker) {
              $this->app->User->SetParameter('lieferschein_list_drucker', $drucker);
              $deliveryNotes = empty($selectedIds)?[]: $this->app->DB->SelectPairs(
                sprintf(
                  'SELECT id, projekt FROM lieferschein WHERE id in (%s)', implode(',', $selectedIds)
                )
              );
              foreach($deliveryNotes as $deliveryNoteId => $projectId) {
                if(class_exists('LieferscheinPDFCustom')) {
                  $Brief = new LieferscheinPDFCustom($this->app,$projectId);
                }
                else{
                  $Brief = new LieferscheinPDF($this->app,$projectId);
                }
                $Brief->GetLieferschein($deliveryNoteId);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->printer->Drucken($drucker,$tmpfile);
                $doctype = 'lieferschein';
                $adressId = $this->app->DB->Select("SELECT adresse FROM lieferschein WHERE id = '$deliveryNoteId' LIMIT 1");
                $this->app->erp->RunHook('dokumentsend_ende', 5, $doctype, $deliveryNoteId, $projectId, $adressId, $aktion);
                $this->app->erp->LieferscheinProtokoll($deliveryNoteId, 'Lieferschein versendet');
                $this->app->DB->Update(
                  sprintf(
                    "UPDATE lieferschein 
                    SET schreibschutz=1, versendet = 1, status='versendet' 
                    WHERE id = %d 
                    AND status != 'storniert'
                    LIMIT 1",
                    $deliveryNoteId
                  )
                );
                @unlink($tmpfile);
              }
            }
          break;
          case 'pdf':
            $tmpfile = [];
            $deliveryNotes = empty($selectedIds)?[]: $this->app->DB->SelectPairs(
              sprintf(
                'SELECT id, projekt FROM lieferschein WHERE id in (%s)', implode(',', $selectedIds)
              )
            );
            foreach($deliveryNotes as $deliveryNoteId => $projectId) {
              if(class_exists('LieferscheinPDFCustom')) {
                $Brief = new LieferscheinPDFCustom($this->app,$projectId);
              }
              else{
                $Brief = new LieferscheinPDF($this->app,$projectId);
              }
              $Brief->GetLieferschein($deliveryNoteId);
              $tmpfile[] = $Brief->displayTMP();
            }

            if(count($tmpfile) > 0) {
              try {
                /** @var PdfMerger $pdfMerger */
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
              } catch (PdfComponentExceptionInterface $exception) {
                echo 'Fehler beim Generieren der Sammelpdf: ' . htmlspecialchars($exception->getMessage());
                $this->app->ExitXentral();
              }
            }
          break;
        }
      }      
    }
    $speichern = $this->app->Secure->GetPOST("speichern");
    $lieferantenretoureinfo = $this->app->Secure->GetPOST("lieferantenretoureinfo");
    $lieferscheinid = $this->app->Secure->GetPOST("lieferscheinid");

    if($lieferantenretoureinfo!="" && $speichern!="" && $lieferscheinid > 0)
      $this->app->DB->Update("UPDATE lieferschein SET lieferantenretoureinfo='$lieferantenretoureinfo' WHERE id='$lieferscheinid' LIMIT 1");



    $this->app->Tpl->Set('UEBERSCHRIFT',"Lieferscheine");

    $backurl = $this->app->Secure->GetGET("backurl");
    $backurl = $this->app->erp->base64_url_decode($backurl);

    //     $this->app->Tpl->Add(KURZUEBERSCHRIFT,"Lieferscheine");
    $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=lieferschein&action=create","Neuen Lieferschein anlegen");

    if(strlen($backurl)>5)
      $this->app->erp->MenuEintrag("$backurl","Zur&uuml;ck zur &Uuml;bersicht");
    else
      $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");

    $status = $this->app->DB->SelectArr('
      SELECT
        status
      FROM
        lieferschein
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
        lieferschein
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
    $this->app->YUI->AutoComplete("lieferscheinnummer", "lieferschein", 1);
    $this->app->YUI->AutoComplete("artikel", "artikelnummer", 1);

    $this->app->Tpl->Add('STATUS',$statusStr);
    $this->app->Tpl->Add('VERSANDARTEN',$versandartenStr);
    $this->app->Tpl->Add('LAENDER',$laenderStr);

    $this->app->Tpl->Parse('TAB1',"lieferschein_table_filter.tpl");

    //$this->app->YUI->TableSearch('TAB2','lieferscheineoffene', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('TAB1',"lieferscheine");
    $this->app->YUI->TableSearch('TAB3','lieferscheineinbearbeitung', 'show','','',basename(__FILE__), __CLASS__);
    
    $this->app->Tpl->Set('SELDRUCKER', $this->app->erp->GetSelectDrucker($this->app->User->GetParameter('lieferschein_list_drucker')));
    
    $this->app->Tpl->Parse('PAGE',"lieferscheinuebersicht.tpl");
  }

  public function DeleteLieferschein($id)
  {
    if($id <= 0)
    {
      return;
    }
    $this->app->DB->Delete("DELETE FROM lieferschein_position WHERE lieferschein='$id'");
    $this->app->DB->Delete("DELETE FROM lieferschein_protokoll WHERE lieferschein='$id'");
    $this->app->DB->Delete("DELETE FROM lieferschein WHERE id='$id' LIMIT 1");
  }

  public function CreateLieferschein($adresse='')
  {
    $projekt = $this->app->erp->GetCreateProjekt($adresse);

    $standardlager = $this->app->DB->Select("SELECT l.id FROM projekt p INNER JOIN lager l ON p.standardlager = l.id WHERE p.id = '$projekt' LIMIT 1");

    $belegmax = '';
    $ohnebriefpapier = $this->app->erp->Firmendaten('lieferschein_ohnebriefpapier');
    $this->app->DB->Insert("INSERT INTO lieferschein (datum,bearbeiter,firma,belegnr,adresse,ohne_briefpapier,projekt)
            VALUES (NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax','$adresse','".$ohnebriefpapier."','".$projekt."')");
    $id = $this->app->DB->GetInsertID();

    $this->app->erp->LieferscheinProtokoll($id,'Lieferschein angelegt');
    if($standardlager){
      $this->app->DB->Update("UPDATE lieferschein SET standardlager = '$standardlager' WHERE id = '$id' LIMIT 1");
    }
    $type='lieferschein';
    $this->app->erp->ObjektProtokoll($type,$id,$type.'_create',ucfirst($type).' angelegt');
    $this->app->erp->SchnellFreigabe($type,$id);

    $this->app->erp->EventAPIAdd('EventLieferscheinCreate',$id,'lieferschein','create');
    return $id;
  }
  
  public function LoadLieferscheinStandardwerte($id,$adresse,$lieferantenretoure=false)
  {
    $arr = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    $field = array('anschreiben','name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','bundesstaat','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit','typ','titel','lieferbedingung','ansprechpartner');


    $rolle_projekt = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE adresse='$adresse' AND subjekt='Kunde' AND objekt='Projekt' AND (bis ='0000-00-00' OR bis <= NOW()) LIMIT 1");

    if($rolle_projekt > 0)
    {
      $arr['projekt'] = $rolle_projekt;
    }

    foreach($field as $key=>$value)
    {

      if($value==='projekt' && $this->app->Secure->POST[$value]!="" && 0)
      {
        $uparr[$value] = $this->app->Secure->POST[$value];
      } else {
        $this->app->Secure->POST[$value] = str_replace("'", '&apos;',$arr[$value]);
        $uparr[$value] = str_replace("'", '&apos;',$arr[$value]);
      }
    }
    $uparr['adresse']=$adresse;

    if($lieferantenretoure)
    {
      $uparr['lieferant']=$adresse;
      $uparr['lieferantenretoure'] = 1;
    }

    $this->app->DB->UpdateArr('lieferschein',$id,'id',$uparr,true);
    $uparr=null;

    //liefernantenvorlage
    $arr = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");

    // falls von Benutzer projekt ueberladen werden soll
    $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    if($projekt_bevorzugt=='1')
    {
      $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      $arr['projekt'] = $uparr['projekt'];
      $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr['projekt']."' AND id > 0 LIMIT 1");
    }

    $field = array('versandart');
    foreach($field as $key=>$value)
    {
      $uparr[$value] = $arr[$value];
      $this->app->Secure->POST[$value] = $arr[$value];
    }

    if($uparr['versandart']==''){
      $uparr['versandart'] = $this->app->erp->StandardVersandart($arr['projekt']);
    }

    $this->app->DB->UpdateArr('lieferschein',$id,'id',$uparr,true);

    $this->app->erp->LoadStandardLieferadresse($adresse,$id,'lieferschein');
    $this->app->erp->LoadAdresseStandard('lieferschein',$id,$adresse);
    $this->app->erp->RunHook('erpapi_loadlieferscheinstandardwerte',2, $id, $adresse);
  }

  public function CopyLieferschein($id)
  {
    $this->app->DB->Insert('INSERT INTO lieferschein (id) VALUES (NULL)');
    $newid = $this->app->DB->GetInsertID();
    $arr = $this->app->DB->SelectRow("SELECT NOW() as datum,projekt,bodyzusatz,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,bearbeiter,'angelegt' as status,typ,standardlager,ansprechpartner,titel,anschreiben,sprache,kostenstelle,
            firma,bundesstaat,keinerechnung,ihrebestellnummer,lieferbedingung,internebezeichnung,lieferantenretoure,lieferant FROM lieferschein WHERE id='$id' LIMIT 1");
    $this->app->DB->UpdateArr('lieferschein',$newid,'id',$arr, true);
    $pos = $this->app->DB->SelectArr("SELECT * FROM lieferschein_position WHERE lieferschein='$id'");
    $cpos = !empty(count($pos))?count($pos):0;
    for($i=0;$i<$cpos;$i++){
      $this->app->DB->Insert("INSERT INTO lieferschein_position (lieferschein) VALUES ($newid)");
      $newposid = $this->app->DB->GetInsertID();
      $altzuneu[$pos[$i]['id']] = $newposid;
      $pos[$i]['lagertext']='';
      $pos[$i]['lieferschein']=$newid;
      if($pos[$i]['explodiert_parent'] && isset($altzuneu[$pos[$i]['explodiert_parent']])){
        $pos[$i]['explodiert_parent'] = $altzuneu[$pos[$i]['explodiert_parent']];
      }
      $this->app->DB->UpdateArr('lieferschein_position',$newposid,'id',$pos[$i], true);
    }
    $this->app->erp->CheckFreifelder('lieferschein',$newid);
    $this->app->erp->CopyBelegZwischenpositionen('lieferschein',$id,'lieferschein',$newid);
    $this->app->DB->Update("UPDATE lieferschein_position SET geliefert=0, abgerechnet=0 WHERE lieferschein='$newid'");

    $this->app->erp->SchnellFreigabe('lieferschein',$newid);
    $this->app->erp->LieferscheinNeuberechnen($newid);

    return $newid;
  }

  public function AddLieferscheinPosition($lieferschein, $verkauf,$menge,$datum)
  {
    $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
    $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
    $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
    $vpe = '';
    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
    //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
    $sort = $this->app->DB->Select("SELECT MAX(sort) FROM lieferschein_position WHERE lieferschein='$lieferschein' LIMIT 1");
    $sort++;
    $this->app->DB->Insert("INSERT INTO lieferschein_position (lieferschein,artikel,bezeichnung,nummer,menge,sort,lieferdatum,status,projekt,vpe)
            VALUES ('$lieferschein','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$sort','$datum','angelegt','$projekt','$vpe')");
  }

  public function AddLieferscheinPositionArtikelID($lieferschein, $artikel,$menge,$bezeichnung,$beschreibung,$datum)
  {
    $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");

    if($bezeichnung==''){
      $bezeichnung = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
    }
    $vpe = '';
    $projekt = 0;
    $sort = $this->app->DB->Select("SELECT MAX(sort) FROM lieferschein_position WHERE lieferschein='$lieferschein' LIMIT 1");
    $sort++;
    $this->app->DB->Insert("INSERT INTO lieferschein_position (lieferschein,artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum,status,projekt,vpe)
            VALUES ('$lieferschein','$artikel','$bezeichnung','$beschreibung','$bestellnummer','$menge','$sort','$datum','angelegt','$projekt','$vpe')");
  }

  /**
   * @param int         $lieferschein
   * @param bool|string $justStorage
   *
   * @return bool|array
   */
  public function LieferscheinCheck($lieferschein, $justStorage = false)
  {
    $lagercheckjustStorage = $justStorage;
    if($justStorage === 'article') {
      $lagercheckjustStorage = false;
    }
    $adresse = 0;
    $positionen_vorhanden = null;
    $artikelzaehlen=null;
    if($lieferschein > 0){
      $artikelarr = $this->app->DB->SelectArr(
        "SELECT ap.id, ap.artikel, ap.menge, ap.geliefert, art.lagerartikel as artlagerartikel, 
       ap.nummer,art.chargenverwaltung,art.mindesthaltbarkeitsdatum,art.seriennummern
      FROM lieferschein_position AS ap 
      LEFT JOIN artikel AS art ON ap.artikel = art.id 
      WHERE ap.lieferschein='$lieferschein' AND ap.geliefert < ap.menge ");
    }
    $cartikelarr = !empty($artikelarr)?count($artikelarr):0;
    for($k=0;$k<$cartikelarr; $k++) {
      $menge = $artikelarr[$k]['menge'] - $artikelarr[$k]['gelieferte'];
      $artikel = $artikelarr[$k]['artikel'];
      $artikel_position_id = $artikelarr[$k]['id'];
      $lagerartikel = $artikelarr[$k]['artlagerartikel'];
      if($lagerartikel==1)
      {
        $gesamte_menge_im_lieferschein= $this->app->DB->Select("SELECT SUM(menge-geliefert) FROM lieferschein_position WHERE lieferschein='$lieferschein' AND artikel='$artikel'");
        if($gesamte_menge_im_lieferschein > $menge) {
          $menge = $gesamte_menge_im_lieferschein;
        }

        if($this->app->erp->LagerCheck($adresse,$artikel,$menge,'lieferschein',$lieferschein, $lagercheckjustStorage)>0){
          $positionen_vorhanden++;
        }
        else {
          if($justStorage === 'article') {
            return $artikelarr[$k];
          }
          return false;
        }
        $artikelzaehlen++;
      }
    }

    if($positionen_vorhanden==$artikelzaehlen && $positionen_vorhanden !=null && $artikelzaehlen!=null){
      return true;
    }
    return false;
  }
}
