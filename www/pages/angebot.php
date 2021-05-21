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
use Xentral\Components\Pdf\Exception\PdfComponentExceptionInterface;
use Xentral\Components\Pdf\PdfMerger;

include '_gen/angebot.php';

class Angebot extends GenAngebot
{
  /** @var Application $app */

  /**
   * Angebot constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","AngebotList");
    $this->app->ActionHandler("create","AngebotCreate");
    $this->app->ActionHandler("positionen","AngebotPositionen");
    $this->app->ActionHandler("upangebotposition","UpAngebotPosition");
    $this->app->ActionHandler("delangebotposition","DelAngebotPosition");
    $this->app->ActionHandler("copyangebotposition","CopyAngebotPosition");
    $this->app->ActionHandler("downangebotposition","DownAngebotPosition");
    $this->app->ActionHandler("positioneneditpopup","AngebotPositionenEditPopup");
    $this->app->ActionHandler("edit","AngebotEdit");
    $this->app->ActionHandler("copy","AngebotCopy");
    $this->app->ActionHandler("zertifikate","AngebotZertifikate");
    $this->app->ActionHandler("auftrag","AngebotAuftrag");
    $this->app->ActionHandler("delete","AngebotDelete");
    $this->app->ActionHandler("undelete","AngebotUndelete");
    $this->app->ActionHandler("freigabe","AngebotFreigabe");
    $this->app->ActionHandler("abschicken","AngebotAbschicken");
    $this->app->ActionHandler("pdf","AngebotPDF");
    $this->app->ActionHandler("inlinepdf","AngebotInlinePDF");
    $this->app->ActionHandler("protokoll","AngebotProtokoll");
    $this->app->ActionHandler("minidetail","AngebotMiniDetail");
    $this->app->ActionHandler("editable","AngebotEditable");
    $this->app->ActionHandler("livetabelle","AngebotLiveTabelle");
    $this->app->ActionHandler("schreibschutz","AngebotSchreibschutz");
    $this->app->ActionHandler("deleterabatte","AngebotDeleteRabatte");
    $this->app->ActionHandler("dateien","AngebotDateien");
    $this->app->ActionHandler("wiedervorlage","AngebotWiedervorlage");
    $this->app->ActionHandler("pdffromarchive","AngebotPDFfromArchiv");
    $this->app->ActionHandler("archivierepdf","AngebotArchivierePDF");
    $this->app->ActionHandler("abgelehnt","AngebotAbgelehnt");
    $this->app->ActionHandler("beauftragt","AngebotBeauftragt");
    $this->app->ActionHandler("kopievon","AngebotKopievon");
    $this->app->ActionHandler("summe","AngebotSumme"); // nur fuer rechte
    $this->app->ActionHandler("einkaufspreise","AngebotEinkaufspreise");
    $this->app->ActionHandler("steuer","AngebotSteuer");
    $this->app->ActionHandler("formeln","AngebotFormeln");
    $this->app->DefaultActionHandler("list");

    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetPOST("adresse");

    if($nummer==''){
      if($id > 0){
        $adresse = $this->app->DB->Select("SELECT a.name FROM angebot b INNER JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      }else{
        $adresse = '';
      }
    }
    else{
      $adresse = $nummer;
    }
    if($id > 0){
      $nummer = $this->app->DB->Select("SELECT b.belegnr FROM angebot b WHERE b.id='$id' LIMIT 1");
    }else{
      $nummer = '';
    }
    if($nummer=="" || $nummer=="0") $nummer="ohne Nummer";

    $this->app->Tpl->Set('UEBERSCHRIFT',"Angebot:&nbsp;".$adresse." (".$nummer.")");
    $this->app->Tpl->Set('FARBE',"[FARBE2]");

    $angebotersatz = $this->app->DB->Select("SELECT abweichendebezeichnung FROM angebot WHERE id='$id' LIMIT 1");
    if($angebotersatz)
      $this->app->Tpl->Set('BEZEICHNUNGTITEL',($this->app->erp->Beschriftung("bezeichnungangebotersatz")?$this->app->erp->Beschriftung("bezeichnungangebotersatz"):$this->app->erp->Beschriftung("dokument_angebot")));
    else
      $this->app->Tpl->Set('BEZEICHNUNGTITEL','Angebot');

    $this->app->erp->Headlines('Angebot');

    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
    $this->app->erp->RegisterHook('supersearch_detail', 'angebot', 'AngebotSupersearchDetail');
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function AngebotSupersearchDetail($detailQuery, $detailResult)
  {
    if ($detailQuery->getGroupKey() !== 'offers') {
      return;
    }

    $angebotId = $detailQuery->getItemIdentifier();
    $sql = sprintf(
      "SELECT a.id, a.belegnr, a.datum FROM `angebot` AS `a` WHERE a.id = '%s' LIMIT 1",
      $this->app->DB->real_escape_string($angebotId)
    );
    $angebot = $this->app->DB->SelectRow($sql);
    if (empty($angebot)) {
      return;
    }

    $datum = date('d.m.Y', strtotime($angebot['datum']));
    $detailResult->setTitle(sprintf('Angebot %s <small>vom %s</small>', $angebot['belegnr'], $datum));
    $detailResult->addButton('Angebot Details', sprintf('index.php?module=angebot&action=edit&id=%s', $angebot['id']));
    $detailResult->setMiniDetailUrl(sprintf('index.php?module=angebot&action=minidetail&id=%s', $angebot['id']));
  }

  function AngebotFormeln()
  {
    
  }

  function AngebotUndelete()
  {
    $id = (int)$this->app->Secure->GetGET("id");

    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");    
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM angebot WHERE id='$id' LIMIT 1");

    if($status==='storniert')
    {
      $this->app->DB->Update("UPDATE angebot SET status='freigegeben' WHERE id='$id' LIMIT 1");
      $this->app->erp->AngebotProtokoll($id,"Angebot Storno rückgängig");

      $this->app->DB->Update("UPDATE wiedervorlage SET abgeschlossen='0' WHERE module='angebot' AND parameter='$id' AND parameter > 0");

      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Angebot \"$name\" ($belegnr) wurde wieder freigegeben!</div>  ");
    } else {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Angebot \"$name\" ($belegnr) kann nicht wieder freigegeben werden da es nicht storniert ist.</div>  ");
    }
    //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");    
    $this->app->Location->execute("index.php?module=angebot&action=list&msg=$msg");
  }
  
  function AngebotSteuer()
  {
    
  }

  function AngebotSumme()
  {
  }

  function AngebotEinkaufspreise()
  {
    //Für Rechte
  }

  function AngebotKopievon()
  {
    $id = (int)$this->app->Secure->GetGET('id');

    $hauptid = $id;

    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
    
    if($status!=='angelegt') {
      $timeout = 0;
      while($timeout <= 100) {
        $checkkopievon = $this->app->DB->Select("SELECT kopievon FROM angebot WHERE id='$hauptid' LIMIT 1");
        if($checkkopievon > 0){
          $hauptid = $checkkopievon;
        }
        else {
          break;
        }
        $timeout++;
      }

      $neuesangebot = $this->app->erp->CopyAngebot($id);
      $altebelegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$hauptid' LIMIT 1");
      $anzahl_kopievon = $this->app->DB->Select("SELECT COUNT(id)+1 FROM angebot WHERE kopievon='$hauptid' AND kopievon > 0");

      $this->app->DB->Update("UPDATE angebot SET belegnr='{$altebelegnr}-$anzahl_kopievon', status='freigegeben',
          kopievon='$hauptid',kopienummer='$anzahl_kopievon' WHERE id='$neuesangebot' LIMIT 1");

      $this->app->erp->AngebotNeuberechnen($id);
      $this->app->Location->execute("index.php?module=angebot&action=edit&id=$neuesangebot");
    }
    $msg = $this->app->erp->base64_url_encode('<div class="error">Das Angebot ist noch im Entwurfsmodus und kann daher noch nicht als neue Version angelegt werden. </div>');
    $this->app->Location->execute("index.php?module=angebot&action=edit&id=$id&msg=$msg");
  }

  function AngebotArchivierePDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $projektbriefpapier = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id = '$id' LIMIT 1");
    if(class_exists('AngebotPDFCustom')) {
      $Brief = new AngebotPDFCustom($this->app,$projektbriefpapier);
    }
    else{
      $Brief = new AngebotPDF($this->app,$projektbriefpapier);
    }
    $Brief->GetAngebot($id);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument(1);
    $this->app->DB->Update("UPDATE angebot SET schreibschutz='1' WHERE id='$id'");
    @unlink($tmpfile);
    $this->app->Location->execute('index.php?module=angebot&action=edit&id='.$id);
  }

  function AngebotDateien()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->AngebotMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
    $this->app->YUI->DateiUpload('PAGE',"Angebot",$id);
  }



  public function AngebotDeleteRabatte()
  {
    $id=$this->app->Secure->GetGET('id');
    $this->app->DB->Update("UPDATE angebot SET rabatt='',rabatt1='',rabatt2='',rabatt3='',rabatt4='',rabatt5='',realrabatt='' WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Die Rabatte wurden entfernt!</div>  ");
    $this->app->Location->execute("index.php?module=angebot&action=edit&id=$id&msg=$msg");
  } 

  function AngebotEditable()
  {
    $this->app->YUI->AARLGEditable();
  }

  function AngebotSchreibschutz()
  {

    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE angebot SET zuarchivieren='1' WHERE id='$id'");
    $this->app->DB->Update("UPDATE angebot SET schreibschutz='0' WHERE id='$id'");
    $this->app->erp->AngebotProtokoll($id,"Schreibschutz entfernt");
    $this->app->Location->execute("index.php?module=angebot&action=edit&id=$id");
  }

  public function AngebotPDFfromArchiv()
  {
    $id = $this->app->Secure->GetGET('id');
    $archiv = $this->app->DB->Select(
      sprintf(
        'SELECT `table_id` from `pdfarchiv` where `id` = %d LIMIT 1',
        $id
      )
    );
    if(empty($archiv)) {
      header('Content-type: application/pdf');
      header('Content-Disposition: attachment; filename="Fehler.pdf"');
      $this->app->ExitXentral();
    }

    $projekt = $this->app->DB->Select("SELECT projekt from angebot where id = '".(int)$archiv."'");

    if(class_exists('AngebotPDFCustom')) {
      $Brief = new AngebotPDFCustom($this->app,$projekt);
    }
    else{
      $Brief = new AngebotPDF($this->app,$projekt);
    }
    if($content = $Brief->getArchivByID($id)) {
      header('Content-type: application/pdf');
      header('Content-Disposition: attachment; filename="'.$content['belegnr'].'.pdf"');
      echo $content['file'];
      $this->app->ExitXentral();
    }
    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="Fehler.pdf"');
    $this->app->ExitXentral();
  }

  function AngebotMiniDetail($parsetarget='',$menu=true)
  {
    $id = $this->app->Secure->GetGET('id');
    
    if(!$this->app->DB->Select("SELECT deckungsbeitragcalc FROM angebot WHERE  id='$id' LIMIT 1")) {
      $this->app->erp->BerechneDeckungsbeitrag($id,'angebot');
    }
    
    $auftragArr = $this->app->DB->SelectArr("SELECT * FROM angebot WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='{$auftragArr[0]['projekt']}' LIMIT 1");
    $kundenname = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");


    $this->app->Tpl->Set('KUNDE',"<a href=\"index.php?module=adresse&action=edit&id=".$auftragArr[0]['adresse']."\" target=\"_blank\">".$kundennummer."</a> ".$kundenname);
    //$this->app->Tpl->Set('KUNDE',$kundennummer." ".$kundenname);
    $this->app->Tpl->Set('DECKUNGSBEITRAG',0);
    $this->app->Tpl->Set('DBPROZENT',0);    

    if($this->app->erp->RechteVorhanden('projekt','dashboard')){
      $this->app->Tpl->Set('PROJEKT', "<a href=\"index.php?module=projekt&action=dashboard&id=" . $auftragArr[0]['projekt'] . "\" target=\"_blank\">$projekt</a>");
    }
    else{
      $this->app->Tpl->Set('PROJEKT', $projekt);
    }

    $this->app->Tpl->Set('ZAHLWEISE',$auftragArr[0]['zahlungsweise']);
    $this->app->Tpl->Set('STATUS',$auftragArr[0]['status']);
    $this->app->Tpl->Set('ANFRAGE',$auftragArr[0]['anfrage']);

    if($auftragArr[0]['ust_befreit']==0)
      $this->app->Tpl->Set('STEUER',"Inland");
    else if($auftragArr[0]['ust_befreit']==1)
      $this->app->Tpl->Set('STEUER',"EU-Lieferung");
    else
      $this->app->Tpl->Set('STEUER',"Export");

    $auftrag = $id <= 0?[]: $this->app->DB->SelectPairs(
      "SELECT l.id,
        CONCAT(
            '<a href=\"index.php?module=auftrag&action=edit&id=',l.id,'\" target=\"_blank\">',
            if(l.belegnr='0' OR l.belegnr='','ENTWURF',l.belegnr)
            ,'</a>&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=',
            l.id,
            '\"><img src=\"./themes/new/images/pdf.svg\" title=\"Lieferschein PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=auftrag&action=edit&id=',
            l.id,
            '\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Lieferschein bearbeiten\" border=\"0\" target=\"_blank\"></a>'
            ) as auftrag
      FROM auftrag AS l 
      WHERE l.angebotid='$id'"
    );

    /*$auftragid = $this->app->DB->Select("SELECT l.id
        FROM angebot l WHERE l.auftragid='$id' AND l.auftrag!='' LIMIT 1");

    $auftragbelegnr = $this->app->DB->Select("SELECT l.belegnr
        FROM auftrag l WHERE l.id='$id' LIMIT 1");*/
    if(empty($auftrag)) {
      $this->app->Tpl->Set('AUFTRAG','-');
    }
    else {
      $this->app->Tpl->Set('AUFTRAG',implode('<br />', $auftrag));
    }

    if($menu) {
      $menu = $this->AngebotIconMenu($id);
      $this->app->Tpl->Set('MENU',$menu);
    }

    // CRM
    $dokumente = $id <= 0?null: $this->app->DB->SelectArr(
      "SELECT betreff,text FROM dokumente_send WHERE dokument='angebot' AND parameter='$id'"
    );
    if(!empty($dokumente))
    {
      foreach($dokumente as $send) {
        $this->app->Tpl->Set('DOKUMENTBETREFF',$send['betreff']);
        $this->app->Tpl->Set('DOKUMENTTEXT',$send['text']);
        $this->app->Tpl->Parse('DOKUMENTETEXT','dokument_send_minidetail.tpl');
      }
    }

    // ARTIKEL

    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status==='freigegeben')
    {
      $table->Query(
        "SELECT if(CHAR_LENGTH(ap.beschreibung) > 0,CONCAT(ap.bezeichnung,' *'),ap.bezeichnung) as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', ap.nummer,'</a>') as Nummer, TRIM(ap.menge)+0 as Menge,
          if(a.porto,'-',if(
              (
                  SELECT SUM(l.menge) 
                  FROM lager_platz_inhalt l 
                  WHERE l.artikel=ap.artikel
                  ) > ap.menge,(
                      SELECT TRIM(SUM(l.menge))+0 
                      FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),
              if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel)>0,CONCAT('<font color=red><b>',(SELECT TRIM(SUM(l.menge))+0 FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),'</b></font>'),
                '<font color=red><b>aus</b></font>'))) as Lager,".$this->app->erp->FormatPreis("ap.preis*(100-ap.rabatt)/100",2)." as preis
        FROM angebot_position ap, artikel a 
        WHERE ap.angebot='$id' AND a.id=ap.artikel 
        ORDER by ap.sort"
      );
      $artikel = $table->DisplayNew("return","Preis","noAction","false",0,0,false);

      $this->app->Tpl->Add('JAVASCRIPT',"
          var auto_refresh = setInterval(
            function ()
            {
            $('#artikeltabellelive$id').load('index.php?module=angebot&action=livetabelle&id=$id').fadeIn('slow');
            }, 3000); // refresh every 10000 milliseconds
          ");
    }
    else {
      $table->Query(
        "SELECT if(CHAR_LENGTH(ap.beschreibung) > 0,CONCAT(ap.bezeichnung,' *'),ap.bezeichnung)  as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', ap.nummer,'</a>') as Nummer, TRIM(ap.menge)+0 as Menge,"
        .$this->app->erp->FormatPreis("ap.preis*(100-ap.rabatt)/100",2)." as preis
        FROM angebot_position ap, artikel a
        WHERE ap.angebot='$id' AND a.id=ap.artikel 
        ORDER by ap.sort"
      );
      $artikel = $table->DisplayNew("return","Preis","noAction");
    }

    $this->app->Tpl->Set('ARTIKEL','<div id="artikeltabellelive'.$id.'">'.$artikel.'</div>');

    if($auftragArr[0]['belegnr']=="0" || $auftragArr[0]['belegnr']=="") $auftragArr[0]['belegnr'] = "ENTWURF";
    $this->app->Tpl->Set('BELEGNR',$auftragArr[0]['belegnr']);
    $this->app->Tpl->Set('ANGEBOTID',$auftragArr[0]['id']);


    if($auftragArr[0]['status']=="freigegeben")
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"orange");
      $this->app->Tpl->Set('ANGEBOTTEXT',"<div class=\"warning\">Das Angebot wurde noch nicht als Auftrag weitergef&uuml;hrt!</div>");
    }
    else if($auftragArr[0]['status']=="versendet")
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"red");
      $this->app->Tpl->Set('ANGEBOTTEXT',"<div class=\"warning\">Das Angebot versendet aber noch kein Auftrag vom Kunden erhalten!</div>");
    }
    else if($auftragArr[0]['status']=="beauftragt")
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"green");
      $this->app->Tpl->Set('ANGEBOTTEXT',"<div class=\"info\">Das Angebot wurde beauftragt und abgeschlossen!</div>");
    }
    else if($auftragArr[0]['status']=="angelegt")
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"grey");
      $this->app->Tpl->Set('ANGEBOTTEXT',"<div class=\"warning\">Das Angebot wird bearbeitet und wurde noch nicht freigegeben und abgesendet!</div>");
    }

    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM angebot_protokoll WHERE angebot='$id' ORDER by zeit DESC");
    $tmp->DisplayNew('PROTOKOLL',"Protokoll","noAction");

    $this->app->Tpl->Set('RECHNUNGLIEFERADRESSE',$this->AngebotRechnungsLieferadresse($auftragArr[0]['id']));
    if(class_exists('AngebotPDFCustom'))
    {
      $Brief = new AngebotPDFCustom($this->app,$auftragArr[0]['projekt']);
    }else{
      $Brief = new AngebotPDF($this->app,$auftragArr[0]['projekt']);
    }

    $Dokumentenliste = $Brief->getArchivedFiles($id, 'angebot');
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
          $tmpr['menu'] = '<a href="index.php?module=angebot&action=pdffromarchive&id='.$v['id'].'"><img src="themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/pdf.svg" /></a>';
          $tmp3->datasets[] = $tmpr;
        }
      }

      $tmp3->DisplayNew('PDFARCHIV','Men&uuml;',"noAction");
    }


    $nettogewicht = $this->app->erp->AngebotNettoGewicht($id);
    if($nettogewicht!="") {
      $nettogewicht = number_format($nettogewicht, 2, ',','.');
      $gewichtbezeichnung = $this->app->erp->Firmendaten('gewichtbezeichnung');
      if($gewichtbezeichnung == '')$gewichtbezeichnung = 'Kg';

      $this->app->Tpl->Set("GEWICHT", $nettogewicht . " ".$gewichtbezeichnung);
    }


    if($parsetarget=='') {
      $this->app->Tpl->Output("angebot_minidetail.tpl");
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Parse($parsetarget,"angebot_minidetail.tpl");
  }

  function AngebotRechnungsLieferadresse($angebotid)
  { 
    $data = $this->app->DB->SelectArr("SELECT * FROM angebot WHERE id='$angebotid' LIMIT 1");

    foreach($data[0] as $key=>$value)
    {
      if($data[0][$key]!="" && $key!="abweichendelieferadresse" && $key!="land" && $key!="plz" && $key!="lieferland" && $key!="lieferplz") $data[0][$key] = $data[0][$key]."<br>";
    }


    $rechnungsadresse = $data[0]['name']."".$data[0]['ansprechpartner']."".$data[0]['abteilung']."".$data[0]['unterabteilung'].
      "".$data[0]['strasse']."".$data[0]['adresszusatz']."".$data[0]['land']."-".$data[0]['plz']." ".$data[0]['ort'];

    // wenn abweichende rechnungsadresse bei kunden aktiv ist dann diese verwenden

    $abweichende = $this->app->DB->Select("SELECT abweichende_rechnungsadresse FROM adresse WHERE id='".$data[0][adresse]."' LIMIT 1");
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
      <tr valign=\"top\"><td width=\"50%\"><b>Rechnungsadresse:</b><br><br>$rechnungsadresse<br></td></tr>
      <tr><td><b>Lieferadresse:</b><br><br>$lieferadresse</td></tr></table>";
  }



  function AngebotFreigabe($id='')
  {
    if($id=='') {
      $id = $this->app->Secure->GetGET('id');
      $freigabe= $this->app->Secure->GetGET('freigabe');
      $weiter= $this->app->Secure->GetPOST('weiter');
      $this->app->Tpl->Set('TABTEXT','Freigabe');
    }
    else {
      $intern = true;
      $freigabe=$intern;
    }

    $this->app->erp->CheckVertrieb($id,"angebot");
    $this->app->erp->CheckBearbeiter($id,"angebot");

    $allowedFrm = true;
    $showDefault = true;
    $doctype = 'angebot';
    if(empty($intern)){
      $this->app->erp->RunHook('beleg_freigabe', 4, $doctype, $id, $allowedFrm, $showDefault);
    }

    if($weiter!='') {
      $this->app->Location->execute('index.php?module=angebot&action=abschicken&id='.$id);
    }

    $check = $this->app->DB->Select("SELECT b.belegnr FROM angebot b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");

    if($allowedFrm && ($freigabe==$id)) {
      $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
      if($belegnr=="" || $belegnr=="0")
      {
        $this->app->erp->BelegFreigabe("angebot",$id);
        if($intern) {
          return 1;
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Das Angebot wurde freigegeben und kann jetzt versendet werden!</div>  ");
        $this->app->Location->execute("index.php?module=angebot&action=edit&id=$id&msg=$msg");
      }
      if($intern) {
        return 0;
      }
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Das Angebot wurde bereits freigegeben!</div>  ");
      $this->app->Location->execute("index.php?module=angebot&action=edit&id=$id&msg=$msg");
    }

    if($showDefault){
      $name = $this->app->DB->Select("SELECT a.name FROM angebot b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      $summe = $this->app->DB->Select("SELECT gesamtsumme FROM angebot WHERE id='$id' LIMIT 1");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM angebot_position
        WHERE angebot='$id' LIMIT 1");

      $summe = $this->app->erp->EUR($summe);

      $extra = $this->app->erp->CheckboxEntwurfsmodus('angebot', $id);

      if($this->app->erp->Firmendaten("oneclickrelease")=="1" && $extra=="")
      {
        $this->app->Location->execute("index.php?module=angebot&action=freigabe&id=$id&freigabe=$id");
      } else {
        $this->app->Tpl->Set('TAB1', "<div class=\"info\">Soll das Angebot  
        jetzt freigegeben werden? <input type=\"button\" value=\"Jetzt freigeben\" class=\"btnImportantLarge\" onclick=\"window.location.href='index.php?module=angebot&action=freigabe&id=$id&freigabe=$id'\">&nbsp;$extra
        </div>");
      }
    }
    $this->AngebotMenu();
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  function AngebotCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $newid = $this->app->erp->CopyAngebot($id);

    header("Location: index.php?module=angebot&action=edit&id=$newid");
    exit;
  }

  function AngebotZertifikate()
  {
    $id = $this->app->Secure->GetGET("id");
    $adresse = $this->app->DB->Select("SELECT adresse FROM angebot WHERE id = '$id' LIMIT 1");
    if($adresse)
    {
      $zertifikate = $this->app->DB->SelectArr("SELECT ds.datei 
      FROM datei_stichwoerter ds 
      INNER JOIN datei_stichwoerter ds2 ON ds.datei = ds2.datei AND ds2.objekt = 'Artikel'
      INNER JOIN angebot_position ap ON ap.artikel = ds2.parameter AND ap.angebot = '$id'
      WHERE ds.objekt = 'Adressen' AND ds.parameter = '$adresse'
      GROUP BY ds.datei");
      if($zertifikate)
      {
        foreach($zertifikate as $zertifikat)
        {
          $this->app->erp->AddDateiStichwort($zertifikat['datei'],"Sonstige","Angebot",$id);
        }
      }
    }
    
    header("Location: index.php?module=angebot&action=dateien&id=$id");
    exit;
  }

  function AngebotLiveTabelle()
  {
    $id = $this->app->Secure->GetGET("id");
    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status=="freigegeben")
    {
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, TRIM(ap.menge)+0 as Menge,
          if(a.porto,'-',if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel) > ap.menge,(SELECT TRIM(SUM(l.menge))+0 FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),
              if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel)>0,CONCAT('<font color=red><b>',(SELECT TRIM(SUM(l.menge))+0 FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),'</b></font>'),
                '<font color=red><b>aus</b></font>'))) as Lager,".$this->app->erp->FormatPreis("ap.preis*(100-ap.rabatt)/100",2)." as preis
          FROM angebot_position ap, artikel a WHERE ap.angebot='$id' AND a.id=ap.artikel");
      $artikel = $table->DisplayNew("return","Preis","noAction");
    } else {
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, TRIM(ap.menge)+0 as Menge,".$this->app->erp->FormatPreis("ap.preis*(100-ap.rabatt)/100",2)." as preis
          FROM angebot_position ap, artikel a WHERE ap.angebot='$id' AND a.id=ap.artikel");
      $artikel = $table->DisplayNew("return","Preis","noAction");
    }
    echo $artikel;
    $this->app->ExitXentral();
  }


  function AngebotAuftrag()
  {
    $id = $this->app->Secure->GetGET("id");

    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id=$id");

    if($status!="angelegt")
    { 
      $newid = $this->app->erp->WeiterfuehrenAngebotZuAuftrag($id);
      $this->app->erp->AngebotProtokoll($id,"Angebot als Auftrag weitergeführt");
    
      // offene wiedervorlage als abgeschlossen
      $this->app->DB->Update("UPDATE wiedervorlage SET abgeschlossen='1' WHERE module='angebot' AND parameter='$id'");
      header("Location: index.php?module=auftrag&action=edit&id=$newid");
    } else {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Das Angebot befindet sich noch im Entwurfsmodus und kann daher nicht als Auftrag weiterführt werden!</div>");
      header("Location: index.php?module=angebot&action=edit&id=$id&msg=$msg");
    }
    exit;
  }

  /**
   * @return JsonResponse
   */
  public function HandleCreateProforma()
  {
    $justStorage = $this->app->Secure->GetPOST('just_strorage');
    $this->app->User->SetParameter('proforma_just_storage', (int)$justStorage);
    $id = $this->app->Secure->GetPOST('elementid');
    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id=$id");
    if($status!=='angelegt') {
      /** @var Proformarechnung $obj */
      $obj = $this->app->loadModule('proformarechnung');
      if(!empty($obj) && method_exists($obj, 'createFromOffer')) {
        $newid = $obj->createFromOffer($id, $justStorage);
        $this->app->erp->AngebotProtokoll($id,'Angebot als Proformarechnung weitergeführt');
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
        'error' => 'Das Angebot befindet sich noch im Entwurfsmodus und kann daher nicht als Proformarechnung weiterführt werden!'
      ],
      JsonResponse::HTTP_BAD_REQUEST
    );
  }

  
  function AngebotAbschicken()
  {
    $this->AngebotMenu();
    $this->app->erp->DokumentAbschicken();
  }



  function AngebotAbgelehnt()
  {
    $id = (int)$this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM angebot WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");

    $this->app->DB->Update("UPDATE angebot SET status='abgelehnt' WHERE id='$id' LIMIT 1");                                                                             
    $this->app->erp->AngebotProtokoll($id,"Angebot abgelehnt");                                                                                                         

    $this->app->DB->Update("UPDATE wiedervorlage SET abgeschlossen='1' WHERE module='angebot' AND parameter='$id' AND parameter > 0");

    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Das Angebot \"$name\" ($belegnr) wurde abgelehnt!</div>");                                                                                                 
    header("Location: index.php?module=angebot&action=list&msg=$msg#tabs-1");
    exit;
  }

  function AngebotBeauftragt()
  {
    $id = (int)$this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM angebot WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");

    $this->app->DB->Update("UPDATE angebot SET status='beauftragt' WHERE id='$id' LIMIT 1");                                                                             
    $this->app->erp->AngebotProtokoll($id,"Angebot als beauftragt markiert");                                                                                                         

    $this->app->DB->Update("UPDATE wiedervorlage SET abgeschlossen='1' WHERE module='angebot' AND parameter='$id' AND parameter > 0");

    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Das Angebot \"$name\" ($belegnr) wurde als beauftragt markiert!</div>");                                                                                                 
    header("Location: index.php?module=angebot&action=list&msg=$msg#tabs-1");
    exit;
  }



  function AngebotDelete()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM angebot WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");

    if($belegnr=="0" || $belegnr=="")
    {

      $this->app->erp->DeleteAngebot($id);
      $belegnr="ENTWURF";
      $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Das Angebot \"$name\" ($belegnr) wurde gel&ouml;scht!</div>  ");
      //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
      header("Location: index.php?module=angebot&action=list&msg=$msg");
      exit;
    }
    if($status=="abgeschlossen")
    {
      // KUNDE muss RMA starten
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Angebot \"$name\" ($belegnr) kann nicht storniert werden da Angebot als Auftrag bereits weitergef&uuml;hrt worden ist!</div>  ");
    }
    else if($status=="storniert")
    {
      $maxbelegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot");
      if(0)//$maxbelegnr == $belegnr)
      {
        $this->app->DB->Delete("DELETE FROM angebot_position WHERE angebot='$id'");
        $this->app->DB->Delete("DELETE FROM angebot_protokoll WHERE angebot='$id'");
        $this->app->DB->Delete("DELETE FROM angebot WHERE id='$id'");
        $this->app->DB->Update("DELETE FROM wiedervorlage WHERE module='angebot' AND parameter='$id' AND parameter > 0");
        $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Angebot \"$name\" ($belegnr) wurde ge&ouml;scht !</div>  ");
      } else
      {
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Angebot \"$name\" ($belegnr) kann nicht storniert werden da es bereits storniert ist!</div>  ");
      }
      header("Location: index.php?module=angebot&action=list&msg=$msg");
      exit;
    }

    else {
      $this->app->DB->Update("UPDATE angebot SET status='storniert' WHERE id='$id' LIMIT 1");
      $this->app->erp->AngebotProtokoll($id,"Angebot storniert");
      $this->app->DB->Update("UPDATE wiedervorlage SET abgeschlossen='0' WHERE module='angebot' AND parameter='$id' AND parameter > 0");
      $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Das Angebot \"$name\" ($belegnr) wurde storniert!</div>");
    }
    //$msg = $this->app->erp->base64_url_encode("<div class=\"error\">Angebot \"$name\" ($belegnr) kann nicht storniert werden, da es bereits versendet wurde!</div>");
    header("Location: index.php?module=angebot&action=list&msg=$msg#tabs-1");
    exit;

  }

  function AngebotProtokoll()
  {
    $this->AngebotMenu();
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Set('TABTEXT',"Protokoll");
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM angebot_protokoll WHERE angebot='$id' ORDER by zeit DESC");
    $tmp->DisplayNew('TAB1',"Protokoll","noAction");

    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }


  function AngebotInlinePDF()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->AngebotNeuberechnen($id);

    $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$id' LIMIT 1");
    $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM angebot WHERE id='$id' LIMIT 1");
    $frame = $this->app->Secure->GetGET('frame');
    if($frame != '') {
      $file = urlencode("../../../../index.php?module=angebot&action=inlinepdf&id=$id");
      echo "<iframe width=\"100%\" height=\"100%\" style=\"height:calc(100vh - 100px)\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
      $this->app->ExitXentral();
    }

    if(class_exists('AngebotPDFCustom')) {
      $Brief = new AngebotPDFCustom($this->app,$projekt);
    }else{
      $Brief = new AngebotPDF($this->app,$projekt);
    }
    $Brief->GetAngebot($id);
    $Brief->inlineDocument($schreibschutz);

  }

  public function AngebotPDF()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->AngebotNeuberechnen($id);

    //$belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $row = $this->app->DB->SelectRow(
      sprintf(
        'SELECT projekt, schreibschutz FROM angebot WHERE id = %d',
        $id
      )
    );
    $projekt = $row['projekt'];
    $schreibschutz =  $row['schreibschutz'];

    if(class_exists('AngebotPDFCustom')) {
      $Brief = new AngebotPDFCustom($this->app,$projekt);
    }
    else{
      $Brief = new AngebotPDF($this->app,$projekt);
    }
    $Brief->GetAngebot($id);
    $Brief->displayDocument($schreibschutz);

    $this->AngebotList();
  }




  function AngebotMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM angebot WHERE id='$id' LIMIT 1");


    if($belegnr=="0" || $belegnr=="") $belegnr ="(Entwurf)";
    //    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Angebot $belegnr");
    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$name Angebot $belegnr");
    $this->app->erp->AngebotNeuberechnen($id);

    // status bestell
    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
    if ($status=="angelegt")
    {
      $this->app->erp->MenuEintrag("index.php?module=angebot&action=freigabe&id=$id","Freigabe");
    }


    $this->app->erp->MenuEintrag("index.php?module=angebot&action=edit&id=$id","Details");

    $anzahldateien = $this->app->erp->AnzahlDateien("Angebot",$id);
    if($anzahldateien > 0) $anzahldateien = " (".$anzahldateien.")"; else $anzahldateien="";

    $this->app->erp->MenuEintrag("index.php?module=angebot&action=dateien&id=$id","Dateien".$anzahldateien);

    if($status=='bestellt')
    { 
      $this->app->erp->MenuEintrag("index.php?module=angebot&action=wareneingang&id=$id","Wareneingang<br>R&uuml;ckst&auml;nde");
      $this->app->erp->MenuEintrag("index.php?module=angebot&action=wareneingang&id=$id","Mahnstufen");
    } 

    //   $this->app->erp->MenuEintrag("index.php?module=angebot&action=abschicken&id=$id","Abschicken / Protokoll");
    //    $this->app->erp->MenuEintrag("index.php?module=angebot&action=protokoll&id=$id","Protokoll");
    if($this->app->Secure->GetGET("action")!="abschicken")
      $this->app->erp->MenuEintrag("index.php?module=angebot&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
      $this->app->erp->MenuEintrag("index.php?module=angebot&action=edit&id=$id","Zur&uuml;ck zum Angebot");

    if($id && $this->app->erp->RechteVorhanden('wiedervorlage','list'))
      $this->app->erp->MenuEintrag("index.php?module=angebot&action=wiedervorlage&id=$id","Wiedervorlage");
    $this->app->Tpl->Parse('MENU',"angebot_menu.tpl");

    $this->app->erp->RunMenuHook('angebot');
  }

  function AngebotWiedervorlage()
  {
    $id = (int)$this->app->Secure->GetGET("id");
    $this->AngebotMenu();
    if($id)
    {
      $wiedervorlage = $this->app->DB->SelectArr("SELECT * from wiedervorlage WHERE module = 'angebot' AND parameter = '$id'");
      if($wiedervorlage)
      {
        $wiedervorlage = reset($wiedervorlage);
      }
      if($this->app->Secure->GetPOST('save'))
      {
        $datum_angelegt = $this->app->Secure->GetPOST('datum_angelegt');
        $zeit_angelegt = $this->app->Secure->GetPOST('zeit_angelegt');
        $datum_erinnerung = $this->app->Secure->GetPOST('datum_erinnerung');
        $zeit_erinnerung = $this->app->Secure->GetPOST('zeit_erinnerung');
        $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
        $beschreibung = $this->app->Secure->GetPOST('beschreibung');
        $betrag = $this->app->Secure->GetPOST('betrag');
        $chance = $this->app->Secure->GetPOST('chance');

        $betrag = str_replace(',','.',str_replace('.','',$betrag));

        $adresse_mitarbeiter = $this->app->Secure->GetPOST('adresse_mitarbeiter');
        $tmp = explode(' ',$adresse_mitarbeiter);
        $adresse_mitarbeiter = $tmp[0];

        $adresse = $this->app->Secure->GetPOST('adresse');
        $tmp = explode(' ',$adresse);
        $adresse = $tmp[0];

        $bearbeiter = $this->app->Secure->GetPOST('bearbeiter');
        $tmp = explode(' ',$bearbeiter);
        $bearbeiter = $tmp[0];

        $stages = $this->app->Secure->GetPOST('stages');
        $tmp = explode(' ',$stages);
        $stages = $tmp[0];


        $adresse_mitarbeiter = (int)$this->app->DB->Select("SELECT id from adresse where mitarbeiternummer = '".$adresse_mitarbeiter."' AND mitarbeiternummer!='' LIMIT 1");
        $adresse = (int)$this->app->DB->Select("SELECT id from adresse where kundennummer = '".$adresse."' AND kundennummer!='' LIMIT 1");
        $bearbeiter = (int)$this->app->DB->Select("SELECT id from adresse where mitarbeiternummer = '".$bearbeiter."' AND mitarbeiternummer!='' LIMIT 1");
        $abgeschlossen = $this->app->Secure->GetPOST('abgeschlossen');

        $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id = '$id' LIMIT 1");

        if($adresse_mitarbeiter<=0)$adresse_mitarbeiter=0;
        if($adresse<=0)$adresse=0;
        if($bearbeiter<=0)$bearbeiter=0;

        if($datum_erinnerung && $datum_erinnerung != '..')
        {
          $datum_angelegt = $this->app->String->Convert($datum_angelegt,"%1.%2.%3","%3-%2-%1");
          $datum_erinnerung = $this->app->String->Convert($datum_erinnerung,"%1.%2.%3","%3-%2-%1");
          if($wiedervorlage)
          {
            if($this->app->DB->Update("UPDATE wiedervorlage
                  set
                  datum_angelegt = '$datum_angelegt',
                  zeit_angelegt = '$zeit_angelegt',
                  datum_erinnerung = '$datum_erinnerung',
                  zeit_erinnerung = '$zeit_erinnerung',
                  bezeichnung = '".$this->app->DB->real_escape_string($bezeichnung)."',
                  beschreibung = '".$this->app->DB->real_escape_string($beschreibung)."',
                  abgeschlossen = '".($abgeschlossen?1:0)."',
                  bearbeiter = '$bearbeiter',
                  adresse = '$adresse',
                  betrag = '$betrag',
                  chance = '$chance',
                  stages = '$stages',
                  adresse_mitarbeiter = '$adresse_mitarbeiter',
                  projekt = '$projekt'
                  where  module = 'angebot' AND parameter = '$id'
                  "))
            {
              $this->app->Tpl->Set('NEUMESSAGE','<div class="info">Erfolgreich gespeichert!</div>');
            }else{
              $this->app->Tpl->Set('NEUMESSAGE',$this->app->DB->error());
            }

          }else{
            if($this->app->DB->Insert("INSERT INTO wiedervorlage (
              datum_angelegt, zeit_angelegt,datum_erinnerung, zeit_erinnerung, bezeichnung,beschreibung,abgeschlossen,bearbeiter,adresse_mitarbeiter,module,parameter,adresse,stages,chance,betrag,projekt) values (
                '$datum_angelegt','$zeit_angelegt','$datum_erinnerung','$zeit_erinnerung','".$this->app->DB->real_escape_string( $bezeichnung)."',
                '".$this->app->DB->real_escape_string($beschreibung)."','".($abgeschlossen?1:0)."','$bearbeiter','$adresse_mitarbeiter','angebot','$id','$adresse','".(int)$stages."','$chance','$betrag', '$projekt')"
                  ))
            {
              $this->app->Tpl->Set('NEUMESSAGE','<div class="info">Erfolgreich gespeichert!</div>');
            }else{
              $this->app->Tpl->Set('NEUMESSAGE',$this->app->DB->error());
            }

          }

        }else{
          $datum_erinnerung = '';
          $this->app->Tpl->Set('NEUMESSAGE','<div class="error">Bitte geben Sie ein Datum ein!</div>');
        }
      } else {
        $datum_angelegt = date("Y-m-d");
        $zeit_angelegt = date("H:i");
        $datum_erinnerung = date("Y-m-d",strtotime ("+1 day"));
        $zeit_erinnerung = '0:00';
        $bezeichnung = 'Angebot '.$this->app->DB->Select("SELECT a.name from angebot an left join adresse a on an.adresse = a.id where an.id = $id limit 1")." vom ".$this->app->String->Convert($this->app->DB->Select("SELECT an.datum from angebot an where an.id = $id limit 1"),"%1-%2-%3","%3.%2.%1");
        $beschreibung = '';
        $adresse_mitarbeiter = $this->app->User->GetAdresse();//$_POST['adresse_mitarbeiter'];
        $bearbeiter = $this->app->User->GetAdresse();
        $adresse = $this->app->DB->Select("SELECT adresse from angebot where id = ".(int)$id);
        $abgeschlossen = $this->app->Secure->GetPOST('abgeschlossen');
      }
      $wiedervorlage = $this->app->DB->SelectArr("SELECT * from wiedervorlage WHERE module = 'angebot' AND parameter = '$id'");
      if(count($wiedervorlage)> 0)
      {
        $wiedervorlage = reset($wiedervorlage);
        $datum_angelegt = $wiedervorlage['datum_angelegt'];
        $zeit_angelegt = $wiedervorlage['zeit_angelegt'];
        $datum_erinnerung = $wiedervorlage['datum_erinnerung'];
        $zeit_erinnerung = $wiedervorlage['zeit_erinnerung'];
        $bezeichnung = $wiedervorlage['bezeichnung'];
        $beschreibung = $wiedervorlage['beschreibung'];
        $adresse_mitarbeiter = (int)$wiedervorlage['adresse_mitarbeiter'];
        $bearbeiter = (int)$wiedervorlage['bearbeiter'];
        $adresse = (int)$wiedervorlage['adresse'];
        $stages = (int)$wiedervorlage['stages'];
        $abgeschlossen = $wiedervorlage['abgeschlossen'];
        $betrag = $wiedervorlage['betrag'];
        $chance = $wiedervorlage['chance'];
      }
      else{
        $sql = "SELECT gesamtsumme FROM angebot WHERE id = %s";
        $betrag = $this->app->DB->Select(sprintf($sql,$id));
      }


      for($i=0;$i<=100;$i += 10)
      {
        $this->app->Tpl->Add("CHANCE","<option value=\"$i\"  ".($chance==$i?"selected":"").">$i %</option>");
      } 


      $mitarbeiternummer = trim($this->app->DB->Select("SELECT mitarbeiternummer from adresse where id = ".(int)$adresse_mitarbeiter." limit 1"));
      $bearbeiternummer = trim($this->app->DB->Select("SELECT mitarbeiternummer from adresse where id = ".(int)$bearbeiter." limit 1"));
      $kundennummer = "".$this->app->DB->Select("SELECT kundennummer from adresse where id = ".(int)$adresse." limit 1");
      if($mitarbeiternummer!="")
        $mitarbeitername = "".$this->app->DB->Select("SELECT name from adresse where id = ".(int)$adresse_mitarbeiter." limit 1");

      if($bearbeiternummer!="")
        $bearbeitername = "".$this->app->DB->Select("SELECT name from adresse where id = ".(int)$bearbeiter." limit 1");

      $kundenname = "".$this->app->DB->Select("SELECT name from adresse where id = ".(int)$adresse." limit 1");      
      $this->app->Tpl->Set('DATUM_ANGELEGT',$this->app->String->Convert($datum_angelegt,"%1-%2-%3","%3.%2.%1"));
      $this->app->Tpl->Set('ZEIT_ANGELEGT',$zeit_angelegt);
      $this->app->Tpl->Set('DATUM_ERINNERUNG',$this->app->String->Convert($datum_erinnerung,"%1-%2-%3","%3.%2.%1"));
      $this->app->Tpl->Set('ZEIT_ERINNERUNG',$zeit_erinnerung);
      $this->app->Tpl->Set('BEZEICHNUNG',$bezeichnung);
      $this->app->Tpl->Set('BESCHREIBUNG',$beschreibung);
      $this->app->Tpl->Set('ADRESSE_MITARBEITER',$mitarbeiternummer.' '.$mitarbeitername);
      $this->app->Tpl->Set('ADRESSE',$kundennummer.' '.$kundenname);
      $this->app->Tpl->Set('STAGES',$this->app->DB->Select("SELECT CONCAT(id,' ',kurzbezeichnung,' (',name,')') FROM wiedervorlage_stages WHERE id='$stages' LIMIT 1"));
      $this->app->Tpl->Set('BEARBEITER',$bearbeiternummer.' '.$bearbeitername);
      $this->app->Tpl->Set('ABGESCHLOSSEN',$abgeschlossen?' checked="checked" ':'');
      $this->app->Tpl->Set('BETRAG',number_format($betrag,"2",",","."));

      /*
         <tr><td width="">Datum:</td><td><input type="text" name="datum_angelegt" size="5" value="[DATUM_ANGELEGT]" /> [MSGDATUM_ANGELEGT]</td><td width="">Zeit:</td><td><input type="text" name="zeit_angelegt" size="5" value="[ZEIT_ANGELEGT]" /> [MSGZEIT_ANGELEGT]</td></tr>
         <tr><td width="">Bezeichnung:</td><td colspan="3"><input type="text" name="bezeichnung" value="[BEZEICHNUNG]" /> [MSGBEZEICHNUNG]</td></tr>
         <tr><td width="">Bearbeiter:</td><td colspan="3"><input type="text" name="adresse" value="[ADRESSE]" /> [MSGADRESSE]</td></tr>
         <tr><td>Beschreibung:</td><td colspan="3"><textarea name="beschreibung" >[BESCHREIBUNG]</textarea>[MSGBESCHREIBUNG]</td></tr>
         <tr><td colspan="4">Wiedervorlage:</td></tr>
         <tr><td width="">Datum:</td><td><input type="text" name="datum_erinnerung" size="5" value="[DATUM_ERINNERUNG]"/> [MSGDATUM_ERINNERUNG]</td><td width="">Zeit:</td><td><input type="text" name="zeit_erinnerung" size="5" value="[ZEIT_ERINNERUNG]" /> [MSGZEIT_ERINNERUNG]</td></tr>
         <tr><td width="">Mitarbeiter:</td><td colspan="3"><input type="text" name="adresse_mitarbeiter" value="[ADRESSE_MITARBEITER]" /> [MSGADRESSE_MITARBEITER]</td></tr>
         <tr><td width="200">abgeschlossen:</td><td colspan="3"><input type="checkbox" name="abgeschlossen" value="1" [ABGESCHLOSSEN] />[MSGABGESCHLOSSEN]</td></tr>

       */
      if($this->app->erp->Firmendaten("briefhtml")=="1")$this->app->YUI->CkEditor("beschreibung","belege");
    }

    $this->app->Tpl->Parse('PAGE',"angebot_wiedervorlage.tpl");
  }

  function AngebotPositionen()
  {

    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->AngebotNeuberechnen($id);
    $this->app->YUI->AARLGPositionen(false);

    return;


    $this->AngebotMenu();
    $id = $this->app->Secure->GetGET("id");

    /* neu anlegen formular */
    $artikelart = $this->app->Secure->GetPOST("artikelart");
    $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
    $vpe = $this->app->Secure->GetPOST("vpe");
    $umsatzsteuerklasse = $this->app->Secure->GetPOST("umsatzsteuerklasse");
    $waehrung = $this->app->Secure->GetPOST("waehrung");
    $projekt= $this->app->Secure->GetPOST("projekt");
    $preis = $this->app->Secure->GetPOST("preis");
    $preis = str_replace(',','.',$preis);
    $menge = $this->app->Secure->GetPOST("menge");
    $lieferdatum = $this->app->Secure->GetPOST("lieferdatum");

    if($lieferdatum=="") $lieferdatum="00.00.0000";


    $angebotsart = $this->app->DB->Select("SELECT angebotsart FROM angebot WHERE id='$id' LIMIT 1");
    $lieferant  = $this->app->DB->Select("SELECT adresse FROM angebot WHERE id='$id' LIMIT 1");

    $anlegen_artikelneu = $this->app->Secure->GetPOST("anlegen_artikelneu");

    if($anlegen_artikelneu!="")
    {

      if($bezeichnung!="" && $menge!="" && $preis!="")
      {
        $sort = $this->app->DB->Select("SELECT MAX(sort) FROM angebot_position WHERE angebot='$id' LIMIT 1");
        $sort = $sort + 1;

        $neue_nummer = $this->app->erp->NeueArtikelNummer($artikelart,$this->app->User->GetFirma(),$projekt);

        // anlegen als artikel
        $this->app->DB->InserT("INSERT INTO artikel (id,typ,nummer,projekt,name_de,umsatzsteuer,adresse,firma) 	
            VALUES ('','$artikelart','$neue_nummer','$projekt','$bezeichnung','$umsatzsteuerklasse','$lieferant','".$this->app->User->GetFirma()."')"); 	

          $artikel_id = $this->app->DB->GetInsertID();
        // einkaufspreis anlegen

        $this->app->DB->Insert("INSERT INTO verkaufspreise (id,artikel,adresse,objekt,projekt,preis,ab_menge,angelegt_am,bearbeiter)
            VALUES ('','$artikel_id','$lieferant','Standard','$projekt','$preis','$menge',NOW(),'".$this->app->User->GetName()."')");

        $lieferdatum = $this->app->String->Convert($lieferdatum,"%1.%2.%3","%3-%2-%1");

        $this->app->DB->Insert("INSERT INTO angebot_position (id,angebot,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
            VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");

        header("Location: index.php?module=angebot&action=positionen&id=$id");
        exit;
      } else
        $this->app->Tpl->Set('NEUMESSAGE',"<div class=\"error\">Bestellnummer, bezeichnung, Menge und Preis sind Pflichtfelder!</div>");

    }

    $ajaxbuchen = $this->app->Secure->GetPOST("ajaxbuchen");
    if($ajaxbuchen!="")
    {
      $artikel = $this->app->Secure->GetPOST("artikel");
      $nummer = $this->app->Secure->GetPOST("nummer");
      $projekt = $this->app->Secure->GetPOST("projekt");
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM angebot_position WHERE auftrag='$id' LIMIT 1");
      $sort = $sort + 1;
      $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
      $bezeichnung = $artikel;
      $neue_nummer = $nummer;
      $waehrung = 'EUR';
      $umsatzsteuerklasse = $this->app->DB->Select("SELECT umsatzsteuerklasse FROM artikel WHERE nummer='$nummer' LIMIT 1");
      $vpe = 'einzeln';

      $this->app->DB->Insert("INSERT INTO angebot_position (id,angebot,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
          VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");
    }
    $weiter = $this->app->Secure->GetPOST("weiter");
    if($weiter!="")
    {
      header("Location: index.php?module=angebot&action=freigabe&id=$id");
      exit;
    }


    if(1)
    {
      $this->app->Tpl->Set('ARTIKELART',$this->app->erp->GetSelect($this->app->erp->GetArtikelart(),$artikelart));
      $this->app->Tpl->Set('VPE',$this->app->erp->GetSelect($this->app->erp->GetVPE(),$vpe));
      $this->app->Tpl->Set('WAEHRUNG',$this->app->erp->GetSelect($this->app->erp->GetWaehrung(),$vpe));
      $this->app->Tpl->Set('UMSATZSTEUERKLASSE',$this->app->erp->GetSelect($this->app->erp->GetUmsatzsteuerklasse(),$umsatzsteuerklasse));
      $this->app->Tpl->Set('PROJEKT',$this->app->erp->GetProjektSelect($projekt));
      $this->app->Tpl->Set('PREIS',$preis);
      $this->app->Tpl->Set('MENGE',$menge);
      $this->app->Tpl->Set('LIEFERDATUM',$lieferdatum);
      $this->app->Tpl->Set('BEZEICHNUNG',$bezeichung);
      $this->app->Tpl->Set('BESTELLNUMMER',$bestellnummer);

      $this->app->Tpl->Set('SUBSUBHEADING',"Neuen Artikel anlegen");
      $this->app->Tpl->Parse('INHALT',"angebot_artikelneu.tpl");
      $this->app->Tpl->Set('EXTEND',"<input type=\"submit\" value=\"Artikel unter Stammdaten anlegen\" name=\"anlegen_artikelneu\">");
      $this->app->Tpl->Parse('UEBERSICHT',"rahmen70.tpl");
      $this->app->Tpl->Set('EXTEND',"");
      $this->app->Tpl->Set('INHALT',"");

      /* ende neu anlegen formular */


      $this->app->Tpl->Set('SUBSUBHEADING',"Artikelstamm");

      $lieferant = $this->app->DB->Select("SELECT adresse FROM angebot WHERE id='$id' LIMIT 1");

      $table = new EasyTable($this->app);
      $table->Query("SELECT CONCAT(LEFT(a.name_de,80),'...') as artikel, a.nummer, 
          v.ab_menge as ab, v.preis, p.abkuerzung as projekt,
          CONCAT('<input type=\"text\" size=\"8\" value=\"00.00.0000\" id=\"datum',v.id,'\">
            <img src=\"./themes/new/images/kalender.png\" height=\"12\" onclick=\"displayCalendar(document.forms[1].datum',v.id,',\'dd.mm.yyyy\',this)\" border=0 align=right>') as Lieferdatum, 
          CONCAT('<input type=\"text\" size=\"3\" value=\"\" id=\"menge',v.id,'\">') as menge, v.id as id
          FROM artikel a LEFT JOIN verkaufspreise v ON a.id=v.artikel LEFT JOIN projekt p ON v.projekt=p.id WHERE v.ab_menge>=1");
      $table->DisplayNew('INHALT', "<input type=\"button\" 
          onclick=\"document.location.href='index.php?module=angebot&action=addposition&id=$id&sid=%value%&menge=' + document.getElementById('menge%value%').value + '&datum=' + document.getElementById('datum%value%').value;\" value=\"anlegen\">");
      $this->app->Tpl->Parse('UEBERSICHT',"rahmen70.tpl");
      $this->app->Tpl->Set('INHALT',"");

      // child table einfuegen

      $this->app->Tpl->Set('SUBSUBHEADING',"Positionen");
      $menu = array("up"=>"upangebotposition",
          "down"=>"downangebotposition",
          //"add"=>"addstueckliste",
          "edit"=>"positioneneditpopup",
          "copy"=>"copyangebotposition",
          "del"=>"delangebotposition");

      $sql = "SELECT a.name_de as Artikel, p.abkuerzung as projekt, a.nummer as nummer, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, b.preis as preis, b.id as id
        FROM angebot_position b
        LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id 
        WHERE b.angebot='$id'";

      //      $this->app->Tpl->Add(EXTEND,"<input type=\"submit\" value=\"Gleiche Positionen zusammenf&uuml;gen\">");

      $this->app->YUI->SortListAdd('INHALT',$this,$menu,$sql);
      $this->app->Tpl->Parse('TAB1',"rahmen70.tpl");

      if($anlegen_artikelneu!="")
        $this->app->Tpl->Set('AKTIV_TAB2',"selected");
      else
        $this->app->Tpl->Set('AKTIV_TAB1',"selected");
      $this->app->Tpl->Parse('PAGE',"angebot_positionuebersicht.tpl");
    } 
  }
  
  function CopyAngebotPosition()
  {
    $this->app->YUI->SortListEvent("copy","angebot_position","angebot");
    $this->AngebotPositionen();
  }

  function DelAngebotPosition()
  {
    $this->app->YUI->SortListEvent("del","angebot_position","angebot");
    $this->AngebotPositionen();
  }

  function UpAngebotPosition()
  {
    $this->app->YUI->SortListEvent("up","angebot_position","angebot");
    $this->AngebotPositionen();
  }

  function DownAngebotPosition()
  {
    $this->app->YUI->SortListEvent("down","angebot_position","angebot");
    $this->AngebotPositionen();
  }


  public function AngebotPositionenEditPopup()
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
          $accordions[$k] = 'angebot_accordion'.$v;
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
              $ret['accordions'][] = str_replace('angebot_accordion','',$v['name']);
            }
          }
        }
      }
      echo json_encode($ret);
      exit;
    }elseif($cmd === 'setaccordion')
    {
      $name = $this->app->Secure->GetPOST('name');
      $active = $this->app->Secure->GetPOST('active');
      $this->app->User->SetParameter('angebot_accordion'.$name, $active);
      echo json_encode(array('success'=>1));
      exit;
    }

    $id = $this->app->Secure->GetGET('id');

    $artikel= $this->app->DB->Select("SELECT artikel FROM angebot_position WHERE id='$id' LIMIT 1");

    // nach page inhalt des dialogs ausgeben
    $filename = 'widgets/widget.angebot_position_custom.php';
    if(is_file($filename)) 
    {
      include_once $filename;
      $widget = new WidgetAngebot_positionCustom($this->app,'PAGE');
    } else {
      $widget = new WidgetAngebot_position($this->app,'PAGE');
    }

    $sid= $this->app->DB->Select("SELECT angebot FROM angebot_position WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute('close_refresh',
        "index.php?module=angebot&action=positionen&id=$sid");
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }



  function AngebotIconMenu($id,$prefix='')
  { 

    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
    $freigabe = '';
    if($status==='angelegt'){
      $freigabe = "<option value=\"freigabe\">Angebot freigeben</option>";
    }
    
    $zertifikatoption = '';
    $zertifikatcase = '';
    $proformarechnungoption = '';
    $proformarechnungcase = '';
    if($this->app->erp->RechteVorhanden('zertifikatgenerator','list'))
    {
      $adresse = $this->app->DB->Select("SELECT adresse FROM angebot WHERE id = '$id' LIMIT 1");
      if($adresse)
      {
        $zertifikate = $this->app->DB->Select("SELECT ds.datei 
      FROM datei_stichwoerter ds 
      INNER JOIN datei_stichwoerter ds2 ON ds.datei = ds2.datei AND ds2.objekt = 'Artikel'
      INNER JOIN angebot_position ap ON ap.artikel = ds2.parameter AND ap.angebot = '$id'
      WHERE ds.objekt = 'Adressen' AND ds.parameter = '$adresse'
      GROUP BY ds.datei LIMIT 1");
        if($zertifikate)
        {
          $zertifikatoption = '<option value="zertifikate">Zertifikate anh&auml;ngen</option>';
          $zertifikatcase = "case 'zertifikate': if(!confirm('Zertifikate wirklich laden?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=zertifikate&id=%value%'; break; ";
        }
      }
      
    }

    if($status=="storniert")      
      $storno = "<option value=\"unstorno\">Angebot Storno r&uuml;ckg&auml;ngig</option>";    
    else if($status!="storniert")
      $storno = "<option value=\"storno\">Angebot stornieren</option>";

    if($this->app->erp->RechteVorhanden('belegeimport', 'belegcsvexport'))
    {
      $casebelegeimport = "case 'belegeimport':  window.location.href='index.php?module=belegeimport&action=belegcsvexport&cmd=angebot&id=%value%'; break;";
      $optionbelegeimport = "<option value=\"belegeimport\">Export als CSV</option>";
    }


    $hookoption = '';
    $hookcase = '';
    $this->app->erp->RunHook('Angebot_Aktion_option',3, $id, $status, $hookoption);
    $this->app->erp->RunHook('Angebot_Aktion_case',3, $id, $status, $hookcase);
    $menu ="

      <script type=\"text/javascript\">
      function onchangeangebot(cmd)
      {
        switch(cmd)
        {
          case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=delete&id=%value%'; break;
          case 'unstorno':    if(!confirm('Wirklich storniertes Angebot wieder freigeben?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=undelete&id=%value%'; break;
          case 'kopievon': if(!confirm('Wirklich neue Version anlegen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=kopievon&id=%value%'; break;
          case 'pdf': window.location.href='index.php?module=angebot&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
          case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=copy&id=%value%'; break;
          case 'auftrag': if(!confirm('Wirklich als Auftrag weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=auftrag&id=%value%'; break;
          $proformarechnungcase
          case 'beauftragt': if(!confirm('Wirklich als beauftragt markieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=beauftragt&id=%value%'; break;
          case 'abgelehnt': if(!confirm('Wirklich Angebot als abgelehnt markieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=abgelehnt&id=%value%'; break;
          case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;

          case 'freigabe':  window.location.href='index.php?module=angebot&action=freigabe&id=%value%'; break;
          $zertifikatcase
          $casebelegeimport
          $hookcase
        }
      }
    </script>
      Aktion:&nbsp;<select onchange=\"onchangeangebot(this.value)\" id=\"aktion$prefix\"><option>bitte w&auml;hlen ...</option>
      $storno
      <option value=\"copy\">Angebot kopieren</option>
      $freigabe
      <option value=\"abschicken\">Angebot abschicken</option>
      <option value=\"abgelehnt\">Angebot abgelehnt</option>
      <option value=\"kopievon\">Angebot neue Version</option>
      <option value=\"auftrag\">als Auftrag weiterf&uuml;hren</option>
      $proformarechnungoption
      <option value=\"beauftragt\">als beauftragt markieren</option>
      $optionbelegeimport
      <option value=\"pdf\">PDF &ouml;ffnen</option>
      $zertifikatoption
      $hookoption
      </select>&nbsp;


    <a href=\"index.php?module=angebot&action=pdf&id=%value%\" title=\"PDF\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
      <!--
      <a href=\"index.php?module=angebot&action=edit&id=%value%\" title=\"Bearbeiten\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
      <a onclick=\"if(!confirm('Wirklich stornieren?')) return false; else window.location.href='index.php?module=angebot&action=delete&id=%value%';\">
      <img src=\"./themes/new/images/delete.svg\" title=\"Stornieren\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=angebot&action=copy&id=%value%';\" title=\"Kopieren\">
      <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich als Auftrag weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=angebot&action=auftrag&id=%value%';\" title=\"Als Auftrag weiterf&uuml;hren\">
      <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Lieferschein\"></a>-->";
    //$tracking = $this->AuftragTrackingTabelle($id);

    $menu = str_replace('%value%',$id,$menu);

    return $menu.$proformapopup;
  }


  function AngebotEdit()
  {
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");
    //   $this->app->erp->AngebotNeuberechnen($id);

    // zum aendern vom Vertrieb    
    $sid = $this->app->Secure->GetGET("sid");
    $cmd = $this->app->Secure->GetGET("cmd");

    if($cmd === 'dadown') {
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
    
    if($cmd === 'daup') {
      $erg['status'] = 0;
      $daid = $this->app->Secure->GetPOST("da_id");
      $check = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.id = '$daid' and d.geloescht <> 1 LIMIT 1");
      if($check)
      {
        $sort = $check[0]['sort']-1;
        if($sort > 0)
        {
          $check2 = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.objekt like 'angebot' AND ds.sort = '$sort' AND d.geloescht <> 1 AND ds.parameter = '$id' LIMIT 1");
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
    
    if($this->app->erp->VertriebAendern('angebot',$id,$cmd,$sid)){
      return;
    }

    if($this->app->erp->InnendienstAendern('angebot',$id,$cmd,$sid)){
      return;
    }

    if($this->app->erp->DisableModul('angebot',$id)) {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->AngebotMenu();
      return;
    }


    $adresse = $this->app->DB->Select("SELECT adresse FROM angebot WHERE id='$id' LIMIT 1");
    if($adresse <=0)
    {
      $this->app->Tpl->Add('JAVASCRIPT','$(document).ready(function() { if(document.getElementById("adresse"))document.getElementById("adresse").focus(); });');
      $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Achtung! Dieses Dokument ist mit keiner Kunden-Nr. verlinkt. Bitte geben Sie die Kundennummer an und klicken Sie &uuml;bernehmen oder Speichern!</div>");
    }
    $kopievon= $this->app->DB->Select("SELECT kopievon FROM angebot WHERE id='$id' LIMIT 1");
    $kopienummer= $this->app->DB->Select("SELECT kopienummer FROM angebot WHERE id='$id' LIMIT 1");

    $hauptid = $id;
    $timeout = 0;
    while($timeout <= 100)
    { 
      $checkkopievon = $this->app->DB->Select(
        "SELECT kopievon FROM angebot WHERE id='$hauptid' LIMIT 1"
      );
      if($checkkopievon > 0){
        $hauptid = $checkkopievon;
      }
      else {
        break;
      }
      $timeout++;
    }
    $kopienummermax= $this->app->DB->Select("SELECT MAX(kopienummer)+1 FROM angebot WHERE id='$hauptid' LIMIT 1");
    $kopie_von_angebot_nummer= $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$hauptid' LIMIT 1");


    if($kopievon>0) {
      $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Dies ist Version $kopienummer des Angebots: <a href=\"index.php?module=angebot&action=edit&id=$hauptid\" target=\"_blank\">$kopie_von_angebot_nummer</a></div>");
    }

    $anzahlkopieen = $this->app->DB->SelectPairs(
      "SELECT id,belegnr FROM angebot WHERE kopievon='$id' ORDER by belegnr"
    );
    if(!empty($anzahlkopieen)) {
      foreach($anzahlkopieen as $offerId => $offerNumberr) {
        $this->app->Tpl->Add(
          'MESSAGE',
          "<div class=\"info\">Zu diesem Angebot geh&ouml;ren das weitere Angebot Nr. <a href=\"index.php?module=angebot&action=edit&id="
          .$offerId."\" target=\"_blank\">"
          .$offerNumberr."</a></div>"
        );
      }
    } 

    $this->app->erp->InfoAuftragsErfassung('angebot',$id);

    $this->app->erp->DisableVerband();
    $this->app->erp->CheckBearbeiter($id,'angebot');
    $this->app->erp->CheckVertrieb($id,'angebot');


    $this->app->YUI->AARLGPositionen();

    $icons = $this->app->YUI->IconsSQLAll();
    $icons = $this->app->DB->Select("SELECT $icons FROM angebot a WHERE a.id='$id' LIMIT 1");

    $this->app->Tpl->Set('STATUSICONS',$icons);

    $belegnr = '';
    $nummer = '';
    $adresse = 0;
    $status = '';
    $schreibschutz = 0;
    //$this->AngebotMiniDetail(MINIDETAIL,false);
    if($id > 0) {
      $angebotarr = $this->app->DB->SelectRow("SELECT * FROM angebot WHERE id='$id' LIMIT 1");
    }
    if(!empty($angebotarr)){
      $belegnr = $angebotarr['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
      $nummer = $angebotarr['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
      $adresse = $angebotarr['adresse'];//$this->app->DB->Select("SELECT adresse FROM angebot WHERE id='$id' LIMIT 1");
      $status = $angebotarr['status'];//$this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
      $schreibschutz = $angebotarr['schreibschutz'];//$this->app->DB->Select("SELECT schreibschutz FROM angebot WHERE id='$id' LIMIT 1");
    }
    $kundennummer = '';
    if($adresse > 0){
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    }


    if($status !== 'angelegt' && $status !== 'angelegta' && $status !== 'a') {
      $Brief = new Briefpapier($this->app);
      if($Brief->zuArchivieren($id, 'angebot')) {
        $this->app->Tpl->Add(
          'MESSAGE',
          "<div class=\"warning\">Das Angebot ist noch nicht archiviert! Bitte versenden oder manuell archivieren.<input type=\"button\" onclick=\"if(!confirm('Soll das Dokument archiviert werden?')) return false;else window.location.href='index.php?module=angebot&action=archivierepdf&id=$id';\" value=\"Manuell archivieren\" /> <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('angebot',$id)\"></div>"
        );
      }
      elseif(!$this->app->DB->Select("SELECT versendet FROM angebot WHERE id = '$id' LIMIT 1")) {
        $this->app->Tpl->Add(
          'MESSAGE',
          "<div class=\"warning\">Das Angebot wurde noch nicht versendet! <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('angebot',$id)\"></div>"
        );
      }
    }

    $liefersperre= $this->app->DB->Select("SELECT liefersperre FROM adresse WHERE id='$adresse' LIMIT 1");
    if($liefersperre=='1' && ($status==='freigegeben' || $status==='angelegt')) {
      $this->app->Tpl->Add(
        'MESSAGE',
        "<div class=\"error\">Achtung: Der Kunde hat eine Liefersperre!</div>"
      );
    }



    $this->app->Tpl->Set('ICONMENU',$this->AngebotIconMenu($id));
    $this->app->Tpl->Set('ICONMENU2',$this->AngebotIconMenu($id,2));

    if($schreibschutz!="1")// && $this->app->erp->RechteVorhanden("angebot","schreibschutz"))
    {
      $this->app->erp->AnsprechpartnerButton($adresse);
      $this->app->erp->LieferadresseButton($adresse);
      $this->app->erp->AnsprechpartnerAlsLieferadresseButton($adresse);
      $this->app->erp->AdresseAlsLieferadresseButton($adresse);
    }

    if($nummer!="")
    {
      $this->app->Tpl->Set('NUMMER',$nummer);
      if($this->app->erp->RechteVorhanden('adresse','edit')){
        $this->app->Tpl->Set(
          'KUNDE',
          "&nbsp;&nbsp;&nbsp;Kd-Nr. <a href=\"index.php?module=adresse&action=edit&id=$adresse\" target=\"_blank\">" . $kundennummer . "</a>"
        );
      }
      else{
        $this->app->Tpl->Set('KUNDE', '&nbsp;&nbsp;&nbsp;Kd-Nr. ' . $kundennummer);
      }
    }


    if($this->app->Secure->GetPOST("speichern")!="")
    {
      $abweichenderechnungsadresse = $this->app->Secure->GetPOST("abweichenderechnungsadresse");
      $abweichendelieferdresse = $this->app->Secure->GetPOST("abweichendelieferadresse");
    } else {
      $abweichenderechnungsadresse = $this->app->DB->Select("SELECT abweichenderechnungsadresse FROM angebot WHERE id='$id' LIMIT 1");
      $abweichendelieferadresse = $this->app->DB->Select("SELECT abweichendelieferadresse FROM angebot WHERE id='$id' LIMIT 1");
    }
    if($abweichenderechnungsadresse) {
      $this->app->Tpl->Set('RECHNUNGSADRESSE','visible');
    } else $this->app->Tpl->Set('RECHNUNGSADRESSE','none');
    if($abweichendelieferadresse) {
      $this->app->Tpl->Set('LIEFERADRESSE','visible');
    } else {
      $this->app->Tpl->Set('LIEFERADRESSE','hidden');
    }

    if($belegnr=='' || $belegnr=='0')
    {
      $this->app->Tpl->Set(
        'LOESCHEN',
        "<input type=\"button\" value=\"Abbrechen\" onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=angebot&action=delete&id=$id';\">"
      );
    }
    $status= $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
    if($status==''){
      $this->app->DB->Update("UPDATE angebot SET status='angelegt' WHERE id='$id' LIMIT 1");
    }

    if($this->app->DB->Select("SELECT id FROM wiedervorlage WHERE module='angebot' AND parameter='$id' AND adresse=0 LIMIT 1"))
      $this->app->DB->Update("UPDATE wiedervorlage SET adresse='$adresse' WHERE module='angebot' AND parameter='$id' AND adresse=0 LIMIT 1");


    if($schreibschutz=='1' && $this->app->erp->RechteVorhanden('angebot','schreibschutz')){
      $optional = '';
      $auftrag = $id <= 0?[]:$this->app->DB->SelectPairs(
        "SELECT id,belegnr FROM auftrag WHERE angebotid='$id'"
      );
      foreach($auftrag as $orderId => $orderNumber) {

        $optional .= "&nbsp;<input type=\"button\" value=\"AB "
          .$orderNumber."\" onclick=\"window.location.href='index.php?module=auftrag&action=edit&id="
          .$orderId."'\">";
      }

      if($optional!='') {
        $optional = "Zum Auftrag: ".$optional;
      }
      else {
        $hinweis = "<div class=\"info\">Zu diesem Angebot gibt es noch keinen Auftrag.</div>";
      }

      $this->app->Tpl->Set(
        'MESSAGE',
        "<div class=\"warning\">Dieses Angebot ist schreibgesch&uuml;tzt und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml;r dieses Angebot wirklich entfernt werden?')) return false;else window.location.href='index.php?module=angebot&action=schreibschutz&id=$id';\">&nbsp;$optional</div>$hinweis"
      );
    }

    if($schreibschutz=='1') {
      $this->app->erp->CommonReadonly();
      $this->app->erp->RemoveReadonly('planedorderdate');

      $speichern = $this->app->Secure->GetPOST('speichern');
      if($speichern!='') {
        $planedorderdate = $this->app->Secure->GetPOST('planedorderdate');
        $planedorderdate = $this->app->String->Convert($planedorderdate,'%1.%2.%3','%3-%2-%1');

        $this->app->DB->Update("UPDATE angebot SET planedorderdate='$planedorderdate' WHERE id='$id' LIMIT 1");
      }
    }

    if($schreibschutz != '1'){
      if($this->app->erp->Firmendaten('schnellanlegen') == '1'){
        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN', '      <input type="button" value="&uuml;bernehmen" onclick="document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
          ');
      }else{
        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN', '
          <input type="button" value="&uuml;bernehmen" onclick="if(!confirm(\'Soll der neue Kunde wirklich &uuml;bernommen werden? Es werden alle Felder &uuml;berladen.\')) return false;else document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
          ');
      }
    }

    // immer wenn sich der lieferant genändert hat standartwerte setzen
    if($schreibschutz != '1' && $this->app->Secure->GetPOST('adresse')!='') {
      $tmp = $this->app->Secure->GetPOST('adresse');
      $tmp = trim($tmp);
      $rest = explode(" ",$tmp);
      $kundennummer = $rest[0];
      $filter_projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id = '$id' LIMIT 1");
      //if($filter_projekt)$filter_projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id= '$filter_projekt' and eigenernummernkreis = 1 LIMIT 1");
      $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' AND geloescht=0 ".$this->app->erp->ProjektRechte("projekt", true, 'vertrieb')." ORDER by ".($filter_projekt?" projekt = '$filter_projekt' DESC, ":"")." projekt LIMIT 1");

      $uebernehmen =$this->app->Secure->GetPOST("uebernehmen");
      if($uebernehmen=="1") // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
      {
        $this->app->erp->LoadAngebotStandardwerte($id,$adresse);
        header("Location: index.php?module=angebot&action=edit&id=$id");
        exit;
      }
    }

    $table = new EasyTable($this->app);
    $table->Query("SELECT bezeichnung as artikel, nummer as Nummer, menge, vpe as VPE, FORMAT(preis,4) as preis
        FROM angebot_position 
        WHERE angebot='$id'");
    $table->DisplayNew('POSITIONEN',"Preis","noAction");

    // $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM angebot WHERE id='$id' LIMIT 1");
    // $this->app->Tpl->Set(BEARBEITER,"<input type=\"text\" value=\"".$this->app->erp->GetAdressName($bearbeiter)."\" readonly>");


    $status= $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('STATUS',"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");

    $angebot = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    if($angebot=="0" || $angebot=="") $angebot="keine Nummer";
    $this->app->Tpl->Set('ANGEBOT',"<input type=\"text\" value=\"".$angebot."\" readonly>");



    $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM angebot WHERE id='$id' LIMIT 1");
    if($this->app->Secure->GetPOST("zahlungsweise")!="") $zahlungsweise = $this->app->Secure->GetPOST("zahlungsweise");
    $zahlungsweise = strtolower($zahlungsweise);

    $zahlungsweisenmodule = $this->app->DB->SelectArr("SELECT id, modul, verhalten FROM zahlungsweisen WHERE type = '".$this->app->DB->real_escape_string($zahlungsweise)."' AND
     (projekt = '$projekt' OR projekt = 0) ORDER BY projekt = '$projekt' DESC LIMIT 1");

    $this->app->Tpl->Set('RECHNUNG',"none");
    $this->app->Tpl->Set('KREDITKARTE',"none");
    $this->app->Tpl->Set('VORKASSE',"none");
    $this->app->Tpl->Set('PAYPAL',"none");
    $this->app->Tpl->Set('EINZUGSERMAECHTIGUNG',"none");
    if($zahlungsweise=="rechnung" || isset($zahlungsweisenmodule[0]['verhalten']) && $zahlungsweisenmodule[0]['verhalten']=='rechnung') $this->app->Tpl->Set('RECHNUNG',"");
    if($zahlungsweise=="paypal") $this->app->Tpl->Set('PAYPAL',"");
    if($zahlungsweise=="kreditkarte") $this->app->Tpl->Set('KREDITKARTE',"");
    if($zahlungsweise=="einzugsermaechtigung" || $zahlungsweise=="lastschrift") $this->app->Tpl->Set('EINZUGSERMAECHTIGUNG',"");
    if($zahlungsweise=="vorkasse" || $zahlungsweise=="kreditkarte" || $zahlungsweise=="paypal" || $zahlungsweise=="bar") $this->app->Tpl->Set('VORKASSE',"");


    $abweichendelieferadresse= $this->app->DB->Select("SELECT abweichendelieferadresse FROM angebot WHERE id='$id' LIMIT 1");
    if($this->app->Secure->GetPOST("abweichendelieferadresse")!="") $versandart = $this->app->Secure->GetPOST("abweichendelieferadresse");
    $this->app->Tpl->Set('ABWEICHENDELIEFERADRESSESTYLE',"hidden");
    if($abweichendelieferadresse=="1") $this->app->Tpl->Set('ABWEICHENDELIEFERADRESSESTYLE',"");


    $this->app->Tpl->Set('AKTIV_TAB1',"selected");
    parent::AngebotEdit();
    if($id > 0 && $this->app->DB->Select(
        sprintf(
          'SELECT id FROM angebot WHERE schreibschutz =1  AND zuarchivieren = 1 AND id = %d',
          $id
        )
      )
    ) {
      $this->app->erp->PDFArchivieren('angebot', $id, true);
    }

    /*
       if($this->app->Secure->GetPOST("speichern")!="" && $storno=="")
       {

       if($this->app->Secure->GetGET("msg")=="")
       {
       $msg = $this->app->Tpl->Get(MESSAGE)." ";
       $msg = $this->app->erp->base64_url_encode($msg);
       } else {
       $msg = $this->app->Secure->GetGET("msg");
    //$msg = $this->app->erp->base64_url_encode($msg);
    }
    header("Location: index.php?module=angebot&action=edit&id=$id&msg=$msg");
    exit;
    } 
     */
    $this->app->erp->MessageHandlerStandardForm();

    /*
       $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM angebot_position
       WHERE angebot='$id'");

       $waehrung = $this->app->DB->Select("SELECT waehrung FROM angebot_position
       WHERE angebot='$id' LIMIT 1");

       $ust_befreit_check = $this->app->DB->Select("SELECT ust_befreit FROM angebot WHERE id='$id' LIMIT 1");
       $summebrutto  = $summe *1.19;

       if($ust_befreit_check==0)
       $tmp = "Kunde zahlt mit UST";
       else if($ust_befreit_check==1)
       $tmp = "Kunde ist UST befreit";
       else
       $tmp = "Kunde zahlt keine UST";


       if($summe > 0)
       $this->app->Tpl->Add(POSITIONEN, "<br><center>Zu zahlen: <b>$summe (netto) $summebrutto (brutto) $waehrung</b> ($tmp)&nbsp;&nbsp;");

     */
    if($this->app->Secure->GetPOST("weiter")!="")
    {
      header("Location: index.php?module=angebot&action=positionen&id=$id");
      exit;
    }
    $this->AngebotMenu();

  }

  function AngebotCreate()
  {
    //$this->app->Tpl->Add(TABS,"<li><h2>Angebot</h2></li>");

    $this->app->erp->Headlines('Angebot anlegen');
    $this->app->erp->MenuEintrag("index.php?module=angebot&action=list","Zur&uuml;ck zur &Uuml;bersicht");


    $anlegen = $this->app->Secure->GetGET("anlegen");

    if($this->app->erp->Firmendaten("schnellanlegen")=="1" && $anlegen!="1")
    {
      header("Location: index.php?module=angebot&action=create&anlegen=1");
      exit;
    }


    if($anlegen != "")
    {
      $id = $this->app->erp->CreateAngebot();

      header("Location: index.php?module=angebot&action=edit&id=$id");
      exit;
    }
    $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">M&ouml;chten Sie eine Angebot jetzt anlegen? &nbsp;
        <input type=\"button\" onclick=\"window.location.href='index.php?module=angebot&action=create&anlegen=1'\" value=\"Ja - Angebot jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set('TAB1',"
        <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><b style=\"font-size: 14pt\">Angebote in Bearbeitung</b>
        <br>
        <br>
        Offene Angebote, die durch andere Mitarbeiter in Bearbeitung sind.
        <br>
        </td>
        </tr>
        </table>
        <br>
        [ANGEBOTE]");


    $this->app->Tpl->Set('AKTIV_TAB1',"selected");

    $this->app->YUI->TableSearch('ANGEBOTE',"angeboteinbearbeitung");
 
    $this->app->Tpl->Set('TABTEXT',"Angebot anlegen");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
    //parent::AngebotCreate();
  }


  function AngebotList()
  {

    //    $this->app->Tpl->Set('UEBERSCHRIFT',"Angebote");
    //    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Angebote");

    if($this->app->Secure->GetPOST('ausfuehren') && $this->app->erp->RechteVorhanden('angebot', 'edit'))
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
          case 'freigeben':
            foreach($selectedIds as $v) {
              if($this->app->DB->Select("SELECT id FROM angebot WHERE id = '$v' AND belegnr = '' AND status <> 'freigeben' AND status <> 'abgeschlossen' AND status <> 'storniert' LIMIT 1")){
                $this->AngebotFreigabe($v);
              }
            }
            break;
          case 'storniert':
            foreach($selectedIds as $v) {
              $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$v' LIMIT 1");
              $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$v' LIMIT 1");
              if($status=='angelegt' || $status=='')
              {
                $this->app->erp->DeleteAngebot($v);
              } else {
                if(class_exists('AngebotPDFCustom'))
                {
                  $Brief = new AngebotPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new AngebotPDF($this->app,$projekt);
                }
                $Brief->GetAngebot($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->erp->AngebotProtokoll($v,'Angebot storniert');
                $this->app->DB->Update("UPDATE angebot SET status='storniert',schreibschutz=1, versendet = 1 WHERE id = '$v' AND status!='angelegt' LIMIT 1");
                $this->app->DB->Update("UPDATE wiedervorlage SET abgeschlossen='0' WHERE module='angebot' AND parameter='$v' AND parameter > 0");
                unlink($tmpfile);
              }
            }

            break;

          case 'versendet':
            foreach($selectedIds as $v) {
              $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$v' LIMIT 1");
              if(class_exists('AngebotPDFCustom'))
              {
                $Brief = new AngebotPDFCustom($this->app,$projekt);
              }else{
                $Brief = new AngebotPDF($this->app,$projekt);
              }
              $Brief->GetAngebot($v);
              $tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              $this->app->erp->AngebotProtokoll($v,'Angebot versendet');
              $this->app->DB->Update("UPDATE angebot SET schreibschutz=1, versendet = 1 WHERE id = '$v' LIMIT 1");
              $this->app->DB->Update("UPDATE angebot SET status='versendet' WHERE id = '$v' AND status='freigegeben' LIMIT 1");
              unlink($tmpfile);
            }

            break;
          case 'beauftragt':
            foreach($selectedIds as $v) {
              $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$v' LIMIT 1");
              if(class_exists('AngebotPDFCustom'))
              {
                $Brief = new AngebotPDFCustom($this->app,$projekt);
              }else{
                $Brief = new AngebotPDF($this->app,$projekt);
              }
              $Brief->GetAngebot($v);
              $tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              $this->app->erp->AngebotProtokoll($v,"Angebot als beauftragt markiert");
              $this->app->DB->Update("UPDATE angebot SET status='beauftragt' WHERE id = '$v' LIMIT 1");
              $this->app->DB->Update("UPDATE wiedervorlage SET abgeschlossen='1' WHERE module='angebot' AND parameter='$v' AND parameter > 0");
              unlink($tmpfile);
            }
            break;

          case 'abgelehnt':
            foreach($selectedIds as $v) {
              $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$v' LIMIT 1");
              if(class_exists('AngebotPDFCustom'))
              {
                $Brief = new AngebotPDFCustom($this->app,$projekt);
              }else{
                $Brief = new AngebotPDF($this->app,$projekt);
              }
              $Brief->GetAngebot($v);
              $tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              $this->app->erp->AngebotProtokoll($v,'Angebot abgelehnt');
              $this->app->DB->Update("UPDATE angebot SET status='abgelehnt' WHERE id = '$v' LIMIT 1");
              $this->app->DB->Update("UPDATE wiedervorlage SET abgeschlossen='1' WHERE module='angebot' AND parameter='$v' AND parameter > 0");
              unlink($tmpfile);
            }
            break;

          case 'drucken':
            if($drucker)
            {
              foreach($selectedIds as $v) {
                $angebotsdaten = $this->app->DB->SelectRow("SELECT projekt, adresse FROM angebot WHERE id='$v' LIMIT 1");
                $projekt = $angebotsdaten['projekt'];
                $adressId = $angebotsdaten['adresse'];
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('AngebotPDFCustom'))
                {
                  $Brief = new AngebotPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new AngebotPDF($this->app,$projekt);
                }
                $Brief->GetAngebot($v);
                $_tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                unlink($_tmpfile);
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('AngebotPDFCustom'))
                {
                  $Brief = new AngebotPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new AngebotPDF($this->app,$projekt);
                }
                $Brief->GetAngebot($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->printer->Drucken($drucker,$tmpfile);
                $doctype = 'angebot';
                $this->app->erp->RunHook('dokumentsend_ende', 5, $doctype, $v, $projekt, $adressId, $aktion);
                $this->app->erp->AngebotProtokoll($v,"Angebot versendet");
                unlink($tmpfile);
              }
            }
            break;
          case 'pdf':
            $tmpfile = [];
            foreach($selectedIds as $v) {
              $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$v' LIMIT 1");
              $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
              if(class_exists('AngebotPDFCustom'))
              {
                $Brief = new AngebotPDFCustom($this->app,$projekt);
              }else{
                $Brief = new AngebotPDF($this->app,$projekt);
              }
              $Brief->GetAngebot($v);
              $_tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              unlink($_tmpfile);
              $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
              if(class_exists('AngebotPDFCustom'))
              {
                $Brief = new AngebotPDFCustom($this->app,$projekt);
              }else{
                $Brief = new AngebotPDF($this->app,$projekt);
              }
              $Brief->GetAngebot($v);
              $tmpfile[] = $Brief->displayTMP();
              //$Brief->ArchiviereDocument();
            }

            if(count($tmpfile) > 0)
            {
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



    $backurl = $this->app->Secure->GetGET("backurl");
    $msg = $this->app->Secure->GetGET("msg");
    $backurl = $this->app->erp->base64_url_decode($backurl);

    //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE2]\">Allgemein</h2></li>");
    $this->app->erp->MenuEintrag("index.php?module=angebot&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=angebot&action=create","Neues Angebot anlegen");

    if(strlen($backurl)>5)
      $this->app->erp->MenuEintrag("$backurl","Zur&uuml;ck zur &Uuml;bersicht");
    else
      $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");


    $zahlungsweisen = $this->app->DB->SelectArr('
        SELECT
        zahlungsweise
        FROM
        angebot
        GROUP BY
        zahlungsweise
        ');

    $zahlungsweiseStr = '';
    if ($zahlungsweisen) {
      foreach ($zahlungsweisen as $zahlungsweise) {
        $zahlungsweiseStr .= '<option name="' . $zahlungsweise['zahlungsweise'] . '">' . ucfirst($zahlungsweise['zahlungsweise']) . '</option>';
      }
    }

    $status = $this->app->DB->SelectArr('
        SELECT
        status
        FROM
        angebot
        GROUP BY
        status
        ');

    $statusStr = '';
    if ($status) {
      foreach ($status as $statusE) {
        $statusStr .= '<option name="' . $statusE['status'] . '">' . ucfirst($statusE['status']) . '</option>';
      }
    }

    $versandarten = $this->app->DB->SelectArr('
        SELECT
        versandart
        FROM
        angebot
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
    $this->app->YUI->AutoComplete("angebotsnummer", "angebot", 1);
    $this->app->YUI->AutoComplete("artikel", "artikelnummer", 1);

    $this->app->Tpl->Set('AKTIV_TAB1',"selected");
    $this->app->Tpl->Set('INHALT',"");
    $this->app->Tpl->Set('SELDRUCKER', $this->app->erp->GetSelectDrucker($this->app->User->GetParameter('rechnung_list_drucker')));


    //$this->AngebotFilter();

    //$this->app->YUI->TableSearch('TAB2',"angeboteoffene");

    $this->app->Tpl->Add('ZAHLUNGSWEISEN',$zahlungsweiseStr);
    $this->app->Tpl->Add('STATUS',$statusStr);
    $this->app->Tpl->Add('VERSANDARTEN',$versandartenStr);
    $this->app->Tpl->Add('LAENDER',$laenderStr);

    $this->app->Tpl->Parse('TAB1',"angebot_table_filter.tpl");

    $this->app->YUI->TableSearch('TAB1',"angebote");
    $this->app->YUI->TableSearch('TAB3',"angeboteinbearbeitung");

    $this->app->Tpl->Parse('PAGE',"angebotuebersicht.tpl");

    return;

    /*
    // suche
    $sql = $this->app->erp->AngebotSuche();

    // offene Angeboten
    $this->app->Tpl->Set('SUBSUBHEADING',"Offene Angebote");

    $table = new EasyTable($this->app);
    $table->Query($sql,$_SESSION[angebottreffer]);

    //$table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Angebot, a.name, p.abkuerzung as projekt, a.id
    //  FROM angebot a, projekt p WHERE (a.status='freigegeben' OR a.status='versendet') AND p.id=a.projekt order by a.datum DESC, a.id DESC",10);


    $table->DisplayOwn(INHALT, "<a href=\"index.php?module=angebot&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
    <a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
    <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=angebot&action=copy&id=%value%';\">
    <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
    <a onclick=\"if(!confirm('Weiterf&uuml;fhren als Auftrag?')) return false; else window.location.href='index.php?module=angebot&action=auftrag&id=%value%';\">
    <img src=\"./themes/new/images/right.png\" border=\"0\"></a>

    ");
    $this->app->Tpl->Parse('TAB1',"rahmen70.tpl");

    $this->app->Tpl->Set('INHALT',"");
    // wartende Angeboten

    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Angebot, ad.kundennummer as kunde, a.name, p.abkuerzung as projekt, a.id
    FROM angebot a, projekt p, adresse ad WHERE (a.status='freigegeben' OR a.status='versendet') AND p.id=a.projekt AND a.adresse=ad.id order by a.datum DESC, a.id DESC");
    $table->DisplayNew('INHALT', "<a href=\"index.php?module=angebot&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
    <a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
    <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=angebot&action=copy&id=%value%';\">
    <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
    ");
    $this->app->Tpl->Parse(TAB2,"rahmen70.tpl");


    $this->app->Tpl->Set('INHALT',"");
    // In Bearbeitung
    $this->app->Tpl->Set('SUBSUBHEADING',"In Bearbeitung");
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as auftrag, name, vertrieb, status, id
    FROM angebot WHERE status='angelegt' order by datum DESC, id DESC");
    $table->DisplayNew('INHALT', "<a href=\"index.php?module=angebot&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
    <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=angebot&action=delete&id=%value%';\">
    <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
    <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=angebot&action=copy&id=%value%';\">
    <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
    ");

    $this->app->Tpl->Parse(TAB3,"rahmen70.tpl");
     */


    /*
       $this->app->Tpl->Set(TAB2,"lieferant, angebot, waehrung, sprache, liefertermin, steuersatz, einkäufer, freigabe<br>
       <br>Angebot (NR),Bestellart (NB), Bestelldatum
       <br>Projekt
       <br>Kostenstelle pro Position
       <br>Terminangebot (am xx.xx.xxxx raus damit)
       <br>vorschlagsdaten für positionen
       <br>proposition reinklicken zum ändern und reihenfolge tabelle 
       <br>Angebot muss werden wie angebot (angebot beschreibung = allgemein)
       <br>Positionen (wie stueckliste)
       <br>Wareneingang / Rückstand
       <br>Etiketten
       <br>Freigabe
       <br>Dokument direkt faxen
       ");
     */
  }

}
