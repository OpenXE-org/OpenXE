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

include __DIR__.'/_gen/gutschrift.php';

class Gutschrift extends GenGutschrift
{
  /**
   * Gutschrift constructor.
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

    $this->app->ActionHandler("list","GutschriftList");
    $this->app->ActionHandler("create","GutschriftCreate");
    $this->app->ActionHandler("positionen","GutschriftPositionen");
    $this->app->ActionHandler("upgutschriftposition","UpGutschriftPosition");
    $this->app->ActionHandler("delgutschriftposition","DelGutschriftPosition");
    $this->app->ActionHandler("copygutschriftposition","CopyGutschriftPosition");
    $this->app->ActionHandler("downgutschriftposition","DownGutschriftPosition");
    $this->app->ActionHandler("positioneneditpopup","GutschriftPositionenEditPopup");
    $this->app->ActionHandler("edit","GutschriftEdit");
    $this->app->ActionHandler("copy","GutschriftCopy");
    $this->app->ActionHandler("delete","GutschriftDelete");
    $this->app->ActionHandler("storno","GutschriftStorno");
    $this->app->ActionHandler("freigabe","GutschriftFreigabe");
    $this->app->ActionHandler("abschicken","GutschriftAbschicken");
    $this->app->ActionHandler("pdf","GutschriftPDF");
    $this->app->ActionHandler("inlinepdf","GutschriftInlinePDF");
    $this->app->ActionHandler("protokoll","GutschriftProtokoll");
    $this->app->ActionHandler("zahlungseingang","GutschriftZahlungseingang");
    $this->app->ActionHandler("minidetail","GutschriftMiniDetail");
    $this->app->ActionHandler("editable","GutschriftEditable");
    $this->app->ActionHandler("livetabelle","GutschriftLiveTabelle");
    $this->app->ActionHandler("schreibschutz","GutschriftSchreibschutz");
    $this->app->ActionHandler("zahlungsmahnungswesen","GutschriftZahlungMahnungswesen");
    $this->app->ActionHandler("deleterabatte","GutschriftDeleteRabatte");
    $this->app->ActionHandler("dateien","GutschriftDateien");
    $this->app->ActionHandler("pdffromarchive","GutschriftPDFFromArchiv");
    $this->app->ActionHandler("archivierepdf","GutschriftArchivierePDF");
    $this->app->ActionHandler("summe","GutschriftSumme"); // nur fuer rechte
    $this->app->ActionHandler("einkaufspreise","GutschriftEinkaufspreise");
    $this->app->ActionHandler("steuer","GutschriftSteuer");
    $this->app->ActionHandler("formeln","GutschriftFormeln");
    $this->app->ActionHandler("createpayment","GutschriftCreatePayment");

    $this->app->DefaultActionHandler("list");


    $id = (int)$this->app->Secure->GetGET('id');
    $returnOrderRow = $id <= 0 ? null : $this->app->DB->SelectRow(
      "SELECT ro.stornorechnung, ro.belegnr, adr.name
      FROM `gutschrift` AS `ro` 
      LEFT JOIN `adresse` AS `adr` ON ro.adresse = adr.id 
      WHERE ro.id = {$id}"
    );
    $stornorechnung = !empty($returnOrderRow['stornorechnung']);
    if($stornorechnung){
      $this->app->Tpl->Set('BEZEICHNUNGTITEL', $this->app->erp->Firmendaten('bezeichnungstornorechnung'));
    }
    else{
      $this->app->Tpl->Set('BEZEICHNUNGTITEL', 'Gutschrift');
    }
    $nummer = $this->app->Secure->GetPOST('adresse');

    if($nummer==''){
      $adresse = empty($returnOrderRow)?'':$returnOrderRow['name'];
    }
    else{
      $adresse = $nummer;
    }

    $nummer = empty($returnOrderRow)?'':$returnOrderRow['belegnr'];
    if($nummer=='' || $nummer==0) {
      $nummer='ohne Nummer';
    }

    $this->app->Tpl->Set('UEBERSCHRIFT','Auftrag:&nbsp;'.$adresse.' ('.$nummer.')');

    $this->app->erp->Headlines('Gutschrift');

    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
      $this->app->erp->RegisterHook('supersearch_detail', 'gutschrift', 'GutschriftSupersearchDetail');
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function GutschriftSupersearchDetail($detailQuery, $detailResult)
  {
      if ($detailQuery->getGroupKey() !== 'creditnotes') {
          return;
      }

      $guschriftId = $detailQuery->getItemIdentifier();
      $sql = sprintf(
          "SELECT gs.id, gs.belegnr, gs.datum, gs.soll FROM `gutschrift` AS `gs` WHERE gs.id = '%s' LIMIT 1",
          $this->app->DB->real_escape_string($guschriftId)
      );
      $gutschrift = $this->app->DB->SelectRow($sql);
      if (empty($gutschrift)) {
          return;
      }
      $datum = date('d.m.Y', strtotime($gutschrift['datum']));
      $detailResult->setTitle(sprintf('Gutschrift %s', $gutschrift['belegnr']));
      $detailResult->addButton('Gutschrift Details', sprintf('index.php?module=gutschrift&action=edit&id=%s', $gutschrift['id']));
      $detailResult->setMiniDetailUrl(sprintf('index.php?module=gutschrift&action=minidetail&id=%s', $gutschrift['id']));
  }

  public function GutschriftFormeln()
  {
    
  }

  public function GutschriftCreatePayment()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $ids = [$id];
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `payment_transaction` 
            (`returnorder_id`, `payment_status`, `address_id`, `amount`, `currency`, `payment_json`, `payment_info`)
        SELECT ro.id,'angelegt', ro.adresse, ro.soll, IF(ro.waehrung <> '', ro.waehrung, 'EUR'),
          JSON_OBJECT('bic', ad.swift, 'iban', ad.iban, 'empfaenger', ad.inhaber, 'betrag', ro.soll, 'waehrung',
          IF(ro.waehrung <> '', ro.waehrung, 'EUR'), 'datum', ro.datum, 'vz1', '', 'vz2', ''),
          CONCAT(IF(ad.swift != '', IF(ad.iban != '', CONCAT('BIC: ', ad.swift, '<br />'), 
          CONCAT('BIC: ', ad.swift)), ''), IF(ad.iban != '', CONCAT('IBAN: ', ad.iban), ''))  
        FROM `gutschrift` AS `ro`
        LEFT JOIN `payment_transaction` AS `pt` ON ro.id = pt.returnorder_id
        LEFT JOIN `adresse` AS `ad` ON ro.adresse = ad.id
        WHERE pt.id IS NULL AND ro.id IN (%s) 
          AND ro.status <> '' AND ro.status <> 'angelegt' AND ro.status <> 'storniert'",
        implode(',', $ids)
      )
    );
    if($this->app->DB->affected_rows() > 0) {
      $this->app->Location->execute('index.php?module=zahlungsverkehr&action=ueberweisung');
    }
    $this->app->Location->execute('index.php?module=gutschrift&action=edit&id='.$id);
  }
  
  public function GutschriftSteuer()
  {
    
  }
  
  public function GutschriftSumme()
  {

  }
  
  public function GutschriftEinkaufspreise()
  {
  
  }

  public function GutschriftArchivierePDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $projektbriefpapier = $this->app->DB->Select(
      sprintf(
        'SELECT `projekt` FROM `gutschrift` WHERE `id` = %d LIMIT 1',
        $id
      )
    );
    if(class_exists('GutschriftPDFCustom')) {
      $Brief = new GutschriftPDFCustom($this->app,$projektbriefpapier);
    }
    else{
      $Brief = new GutschriftPDF($this->app,$projektbriefpapier);
    }
    $Brief->GetGutschrift($id);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument(1, 1);
    $this->app->DB->Update(
      sprintf(
        'UPDATE `gutschrift` SET `schreibschutz` = 1 WHERE `id` = %d',
        $id
      )
    );
    @unlink($tmpfile);
    $this->app->Location->execute('index.php?module=gutschrift&action=edit&id='.$id);
  }

  public function GutschriftPDFFromArchiv()
  {
    $id = $this->app->Secure->GetGET('id');
    $archiv = $this->app->DB->Select(
      sprintf(
        'SELECT `table_id` FROM `pdfarchiv` WHERE `id` = %d LIMIT 1',
        $id
      )
    );
    if(empty($archiv)) {
      header('Content-type: application/pdf');
      header('Content-Disposition: attachment; filename="Fehler.pdf"');
      $this->app->ExitXentral();
    }

    $projekt = $this->app->DB->Select(
      sprintf(
        'SELECT `projekt` FROM `gutschrift` WHERE `id` = %d',
        $archiv
      )
    );
    if(class_exists('GutschriftPDFCustom')) {
      $Brief = new GutschriftPDFCustom($this->app,$projekt);
    }
    else{
      $Brief = new GutschriftPDF($this->app,$projekt);
    }
    $content = $Brief->getArchivByID($id);
    if(empty($content)) {
      header('Content-type: application/pdf');
      header('Content-Disposition: attachment; filename="Fehler.pdf"');
      $this->app->ExitXentral();
    }

    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$content['belegnr'].'.pdf"');
    echo $content['file'];
    $this->app->ExitXentral();
  }

  public function GutschriftCopy()
  {
    $id = $this->app->Secure->GetGET('id');

    $newid = $this->CopyGutschrift($id);

    $this->app->Location->execute('index.php?module=gutschrift&action=edit&id='.$newid);
  }


  public function GutschriftDeleteRabatte()
  {
    $id=$this->app->Secure->GetGET('id');
    $this->app->DB->Update(
      sprintf(
        'UPDATE `gutschrift` 
        SET `rabatt` = 0, `rabatt1` = 0,`rabatt2` = 0,`rabatt3` = 0,`rabatt4` = 0,`rabatt5` = 0,`realrabatt`= 0 
        WHERE `id` = %d 
        LIMIT 1',
        $id
      )
    );
    $msg = $this->app->erp->base64_url_encode('<div class="info">Die Rabatte wurden entfernt!</div>  ');
    $this->app->Location->execute("index.php?module=gutschrift&action=edit&id=$id&msg=$msg");
  }

  public function GutschriftSchreibschutz()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->DB->Update(
      sprintf(
        'UPDATE gutschrift SET zuarchivieren=1, schreibschutz = 0 WHERE id = %d',
        $id
      )
    );
    $this->app->erp->GutschriftProtokoll($id,'Schreibschutz entfernt');
    $this->app->Location->execute('index.php?module=gutschrift&action=edit&id='.$id);
  }

  public function GutschriftDateien()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->GutschriftMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Dateien)');
    $this->app->YUI->DateiUpload('PAGE','Gutschrift',$id);
  }


  public function GutschriftZahlungMahnungswesen()
  {
    $this->GutschriftMenu();
    $this->app->Tpl->Set('TABTEXT','Zahlung-/Mahnwesen');
    $this->GutschriftMiniDetail('TAB1',true);

    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  public function GutschriftEditable()
  { 
    $this->app->YUI->AARLGEditable();
  }

  /**
   * @param int    $id
   * @param string $prefix
   *
   * @return string|string[]
   */
  public function GutschriftIconMenu($id,$prefix='')
  {
    $status = $this->app->DB->Select(
      sprintf(
        "SELECT `status` FROM `gutschrift` WHERE `id`= %d LIMIT 1",
        $id
      )
    );

    if($status==='angelegt' || $status==''){
      $freigabe = "<option value=\"freigabe\">Gutschrift freigeben</option>";
    }

    if($this->app->erp->RechteVorhanden('belegeimport', 'belegcsvexport')) {
      $casebelegeimport = "case 'belegeimport':  window.location.href='index.php?module=belegeimport&action=belegcsvexport&cmd=gutschrift&id=%value%'; break;";
      $optionbelegeimport = "<option value=\"belegeimport\">Export als CSV</option>";
    }

    $createpayment = '';
    $casecreatepayment = '';

    $hookoption = '';
    $hookcase = '';
    $this->app->erp->RunHook('Gutschrift_Aktion_option',3, $id, $status, $hookoption);
    $this->app->erp->RunHook('Gutschrift_Aktion_case',3, $id, $status, $hookcase);

    $menu ="

      <script type=\"text/javascript\">
      function onchangegutschrift(cmd)
      {
        switch(cmd)
        {
          case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=gutschrift&action=delete&id=%value%'; break;
          case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=gutschrift&action=copy&id=%value%'; break;
          case 'pdf': window.location.href='index.php?module=gutschrift&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
          case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;
          case 'freigabe': window.location.href='index.php?module=gutschrift&action=freigabe&id=%value%';  break;
          $casecreatepayment
          $casebelegeimport
          $hookcase
        }

      }
    </script>

      &nbsp;Aktion:&nbsp;<select id=\"aktion$prefix\" onchange=\"onchangegutschrift(this.value);\">
      <option>bitte w&auml;hlen ...</option>
      <option value=\"storno\">Gutschrift stornieren</option>
      <option value=\"copy\">Gutschrift kopieren</option>
      $freigabe
      <option value=\"abschicken\">Gutschrift abschicken</option>
      $optionbelegeimport
      <option value=\"pdf\">PDF &ouml;ffnen</option>
      $createpayment
      $hookoption
      </select>&nbsp;

    <a href=\"index.php?module=gutschrift&action=pdf&id=%value%\" title=\"PDF\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
      ";

    $menu = str_replace('%value%',$id,$menu);
    return $menu;
  }



  function GutschriftLiveTabelle()
  { 
    $id = $this->app->Secure->GetGET('id');
    $status = $this->app->DB->Select(
      sprintf(
        "SELECT `status` FROM `gutschrift` WHERE `id`= %d LIMIT 1",
        $id
      )
    );

    $table = new EasyTable($this->app);

    if($status==='freigegeben')
    {
      $table->Query(
        "SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, 
          TRIM(ap.menge)+0 as Menge,FORMAT(ap.preis,2,'de_DE') as Preis
        FROM gutschrift_position ap, artikel a 
        WHERE ap.gutschrift='$id' AND a.id=ap.artikel"
      );
      $artikel = $table->DisplayNew('return','Preis','noAction');
    }
    else {
      $table->Query(
        "SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, TRIM(ap.menge)+0 as Menge,
        FORMAT(ap.preis,2,'de_DE') as Preis
        FROM gutschrift_position ap, artikel a 
        WHERE ap.gutschrift='$id' AND a.id=ap.artikel"
      );
      $artikel = $table->DisplayNew('return','Preis','noAction');
    }
    echo $artikel;
    $this->app->ExitXentral();
  }

  /**
   * @param string $parsetarget
   * @param bool   $menu
   */
  public function GutschriftMiniDetail($parsetarget='',$menu=true)
  { 
    $id = $this->app->Secure->GetGET('id');
    if(!$this->app->DB->Select("SELECT deckungsbeitragcalc FROM gutschrift WHERE  id='$id' LIMIT 1")) {
      $this->app->erp->BerechneDeckungsbeitrag($id,'gutschrift');
    }
    $auftragArr = $this->app->DB->SelectArr("SELECT * FROM gutschrift WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='{$auftragArr[0]['projekt']}' LIMIT 1");
    $kundenname = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");
    $this->app->Tpl->Set('DECKUNGSBEITRAG',0);
    $this->app->Tpl->Set('DBPROZENT',0);    
    $this->app->Tpl->Set('KUNDE',"<a href=\"index.php?module=adresse&action=edit&id=".$auftragArr[0]['adresse']."\">".$kundennummer."</a> ".$kundenname);

   if($this->app->erp->RechteVorhanden("projekt","dashboard"))
      $this->app->Tpl->Set('PROJEKT',"<a href=\"index.php?module=projekt&action=dashboard&id=".$auftragArr[0]['projekt']."\" target=\"_blank\">$projekt</a>");
    else
      $this->app->Tpl->Set('PROJEKT',$projekt);

    $this->app->Tpl->Set('ZAHLWEISE',$auftragArr[0]['zahlungsweise']);
    $this->app->Tpl->Set('STATUS',$auftragArr[0]['status']);

    $internet = $this->app->DB->Select("SELECT a.internet FROM gutschrift g LEFT JOIN rechnung r ON r.id=g.rechnungid LEFT JOIN auftrag a ON a.id=r.auftragid WHERE g.id='$id' AND g.id > 0 LIMIT 1");
    $this->app->Tpl->Set('INTERNET',$internet);

   $rechnung = $this->app->DB->SelectArr(
     "SELECT
        `r`.auftragid,
        CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',
            `r`.id,'\" target=\"_blank\">',
            if(`r`.belegnr='0' OR `r`.belegnr='','ENTWURF',`r`.belegnr),
            '&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=', `r`.id,
            '\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=rechnung&action=edit&id=',`r`.id,
            '\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>'
            ) as `rechnung`
        FROM `gutschrift` AS `g` 
        LEFT JOIN `rechnung` AS `r` ON `r`.id=`g`.rechnungid 
        WHERE `g`.id='$id'"
   );

  $orderIds = [];
    if(!empty($rechnung)) {
      $cRechnung = count($rechnung);
      for($li=0;$li<$cRechnung;$li++) {
        $orderIds[] = (int)$rechnung[$li]['auftragid'];
        $this->app->Tpl->Add('RECHNUNG',$rechnung[$li]['rechnung']);
        if($li<$cRechnung) {
          $this->app->Tpl->Add('RECHNUNG',"<br>");
          $lieferscheinid = $this->app->DB->Select("SELECT r.lieferschein FROM gutschrift g LEFT JOIN rechnung r ON r.id=g.rechnungid WHERE g.id='$id'");
          if($lieferscheinid > 0) {
            $lieferschein = $this->app->DB->Select("SELECT CONCAT(belegnr,'&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=',id,'\">
       <img src=\"./themes/new/images/pdf.svg\" title=\"Lieferschein PDF\" border=\"0\"></a>&nbsp;
       <a href=\"index.php?module=lieferschein&action=edit&id=',id,'\"><img src=\"./themes/new/images/edit.svg\" title=\"Lieferschein bearbeiten\" border=\"0\"></a>') 
       FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");
          }
    
          $this->app->Tpl->Set('LIEFERSCHEIN',$lieferschein);
        }
      }
    }
    else{
      $this->app->Tpl->Set('RECHNUNG', '-');
    }

    $orders = empty($orderIds)?[]:$this->app->DB->SelectFirstCols(
      sprintf(
        "SELECT CONCAT('<a href=\"index.php?module=auftrag&action=edit&id=',
            `o`.id,'\" target=\"_blank\">',
            if(`o`.belegnr='0' OR `o`.belegnr='','ENTWURF',`o`.belegnr),
            '&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=', `o`.id,
            '\"><img src=\"./themes/new/images/pdf.svg\" title=\"Auftrag PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=auftrag&action=edit&id=',`o`.id,
            '\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Auftrag bearbeiten\" border=\"0\"></a>'
            )
        FROM `auftrag` AS `o`
        WHERE `o`.`id` IN (%s)
        ORDER BY `o`.datum, `o`.id",
        implode(',', $orderIds)
      )
    );

    if(!empty($orders)) {
      $this->app->Tpl->Set('AUFTRAG', implode('<br />', $orders));
    }
    else {
      $this->app->Tpl->Set('AUFTRAG', '-');
    }

/*
    $rechnung = $this->app->DB->Select("SELECT CONCAT(rechnung,'&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',rechnungid,'\">
      <img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
    <a href=\"index.php?module=rechnung&action=edit&id=',rechnungid,'\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>')
        FROM gutschrift WHERE id='$id' LIMIT 1");

    if($rechnung=="" || $rechnung <=0 ) $rechnung = "-";
    $this->app->Tpl->Set(RECHNUNG,$rechnung);
*/
      if($auftragArr[0]['ust_befreit']==0){
        $this->app->Tpl->Set('STEUER', "Inland");
      }
    else if($auftragArr[0]['ust_befreit']==1){
      $this->app->Tpl->Set('STEUER', "EU-Lieferung");
    }
    else{
      $this->app->Tpl->Set('STEUER', 'Export');
    }


    if($menu)
    {
      $menu = $this->GutschriftIconMenu($id);
      $this->app->Tpl->Set('MENU',$menu);
    }
    // ARTIKEL

    $status = $this->app->DB->Select("SELECT status FROM gutschrift WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    $table->Query("SELECT if(CHAR_LENGTH(ap.beschreibung) > 0,CONCAT(ap.bezeichnung,' *'),ap.bezeichnung) as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', ap.nummer,'</a>') as Nummer, ".$this->app->erp->FormatMenge("ap.menge")." as Menge,".$this->app->erp->FormatPreis("ap.preis*(100-ap.rabatt)/100",2)." as Preis
          FROM gutschrift_position ap, artikel a WHERE ap.gutschrift='$id' AND a.id=ap.artikel ORDER by ap.sort");


    $table->align = array('left','left','right','right');
    $artikel = $table->DisplayNew('return','Preis','noAction','false',0,0,false);
    $this->app->Tpl->Set('ARTIKEL','<div id="artikeltabellelive'.$id.'">'.$artikel.'</div>');

    if($auftragArr[0]['belegnr']=='0' || $auftragArr[0]['belegnr']=='') {
      $auftragArr[0]['belegnr'] = 'ENTWURF';
    }
    $this->app->Tpl->Set('BELEGNR',$auftragArr[0]['belegnr']);
    $this->app->Tpl->Set('GUTSCHRIFTID',$auftragArr[0]['id']);
    $this->app->Tpl->Set('DELIVERYTHRESHOLDVATID',!empty($auftragArr[0]['deliverythresholdvatid'])?$auftragArr[0]['deliverythresholdvatid']:'');

    if($auftragArr[0]['status']==='freigegeben')
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"orange");
      $this->app->Tpl->Set('ANGEBOTTEXT',"Das Angebot wurde noch nicht als Auftrag weitergef&uuml;hrt!");
    }
    else if($auftragArr[0]['status']==='versendet')
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"red");
      $this->app->Tpl->Set('ANGEBOTTEXT',"Das Angebot versendet aber noch kein Auftrag vom Kunden erhalten!");
    }
    else if($auftragArr[0]['status']==='beauftragt')
    {
      $this->app->Tpl->Set('ANGEBOTFARBE','green');
      $this->app->Tpl->Set('ANGEBOTTEXT',"Das Angebot wurde beauftragt und abgeschlossen!");
    }
    else if($auftragArr[0]['status']==='angelegt')
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"grey");
      $this->app->Tpl->Set('ANGEBOTTEXT',"Das Angebot wird bearbeitet und wurde noch nicht freigegeben und abgesendet!");
    }


    $this->app->Tpl->Set('GUTSCHRIFTADRESSE',$this->Gutschriftadresse($auftragArr[0]['id']));

    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM gutschrift_protokoll WHERE gutschrift='$id' ORDER by zeit DESC");
    $tmp->DisplayNew('PROTOKOLL',"Protokoll","noAction");

    if(class_exists('GutschriftPDFCustom'))
    {
      $Brief = new GutschriftPDFCustom($this->app,$projekt);
    }else{
      $Brief = new GutschriftPDF($this->app,$projekt);
    }
    
    $Dokumentenliste = $Brief->getArchivedFiles($id, 'gutschrift');
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
          if(isset($v['belegnummer']) && $v['belegnummer']) {
            $tmpr['belegnr'] = $v['belegnummer'];
          }
          $tmpr['bearbeiter'] = $v['bearbeiter'];
          $tmpr['menu'] = '<a href="index.php?module=gutschrift&action=pdffromarchive&id='.$v['id'].'"><img src="themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/pdf.svg" /></a>';
          $tmp3->datasets[] = $tmpr;
        }
      }
      $tmp3->DisplayNew('PDFARCHIV','Men&uuml;','noAction');
    }

    if($parsetarget=='') {
      $this->app->Tpl->Output('gutschrift_minidetail.tpl');
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Parse($parsetarget,'gutschrift_minidetail.tpl');
  }

  function Gutschriftadresse($id)
  {
    $data = $this->app->DB->SelectArr("SELECT * FROM gutschrift WHERE id='$id' LIMIT 1");

    foreach($data[0] as $key=>$value)
    {
      if($data[0][$key]!='' && $key!=='abweichendelieferadresse'
        && $key!=='land' && $key!=='plz' && $key!=='lieferland' && $key!=='lieferplz') {
        $data[0][$key] = $data[0][$key].'<br>';
      }
    }


    $rechnungsadresse = $data[0]['name']."".$data[0]['ansprechpartner']."".$data[0]['abteilung']."".$data[0]['unterabteilung'].
      "".$data[0]['strasse']."".$data[0]['adresszusatz']."".$data[0]['land']."-".$data[0]['plz']." ".$data[0]['ort'];
    return "<table width=\"100%\">
      <tr valign=\"top\"><td width=\"50%\"><b>Gutschrift:</b><br><br>$rechnungsadresse</td></tr>";
  }


  /**
   * @param bool $return
   *
   * @return string
   */
  function GutschriftZahlung($return=false)
  {
    $id = $this->app->Secure->GetGET('id');

    $gutschriftArr = $this->app->DB->SelectArr(
      "SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum, belegnr, soll, waehrung, rechnungid 
      FROM gutschrift WHERE id='$id' LIMIT 1"
    );
    $waehrung = empty($gutschriftArr)?'EUR':$gutschriftArr[0]['waehrung'];
    if(!$waehrung) {
      $waehrung = 'EUR';
    }

    $rechnungid = empty($gutschriftArr)?0: $gutschriftArr[0]['rechnungid'];

    $auftragid = $rechnungid <= 0?0:$this->app->DB->Select(
      sprintf(
        'SELECT `auftragid` FROM `rechnung` WHERE `id` = %d LIMIT 1',
        $rechnungid
      )
    );
    $eingang ="<tr><td colspan=\"3\"><b>Zahlungen</b></td></tr>";


    $eingang .="<tr><td class=auftrag_cell>".$gutschriftArr[0]['datum']
      ."</td><td class=auftrag_cell>GS ".$gutschriftArr[0]['belegnr']
      ."</td><td class=auftrag_cell align=right>".$this->app->erp->EUR($gutschriftArr[0]['soll'])
      ." $waehrung</td></tr>";

    $eingangArr = $this->app->DB->SelectArr(
      "SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, k.id as kontoauszuege, 
       ke.betrag as betrag, k.id as zeile,k.waehrung 
      FROM kontoauszuege_zahlungseingang ke 
      LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id 
      LEFT JOIN konten ko ON k.konto=ko.id 
      WHERE (ke.objekt='gutschrift' AND ke.parameter='$id') 
         OR (ke.objekt='auftrag' AND ke.parameter='$auftragid' AND ke.parameter>0)
        OR (ke.objekt='rechnung' AND ke.parameter='$rechnungid'  AND ke.parameter>0)"
    );
    $ceingangArr = empty($eingangArr)?0:count($eingangArr);

    for($i=0;$i<$ceingangArr;$i++) {
      $waehrung = 'EUR';
      if($eingangArr[$i]['waehrung']) {
        $waehrung = $eingangArr[$i]['waehrung'];
      }
      $eingang .="<tr><td class=auftrag_cell>".$eingangArr[$i]['datum']
        ."</td><td class=auftrag_cell>".$eingangArr[$i]['konto']
        ."&nbsp;(<a href=\"index.php?module=zahlungseingang&action=editzeile&id="
        .$eingangArr[$i]['zeile']."\">zur Buchung</a>)</td><td class=auftrag_cell align=right>"
        .$this->app->erp->EUR($eingangArr[$i]['betrag'])
        ." $waehrung</td></tr>";
    }
    // gutschriften zu dieser rechnung anzeigen
/*
    $gutschriften = $this->app->DB->SelectArr("SELECT belegnr, DATE_FORMAT(datum,'%d.%m.%Y') as datum,soll FROM gutschrift WHERE rechnungid='$id'");

    for($i=0;$i<count($gutschriften);$i++)
      $eingang .="<tr><td class=auftrag_cell>".$gutschriften[$i]['datum']."</td><td class=auftrag_cell>GS ".$gutschriften[$i]['belegnr']."</td><td class=auftrag_cell align=right>".$this->app->erp->EUR($gutschriften[$i]['soll'])." EUR</td></tr>";

*/

    $ausgang = '';
    $ausgangArr = $this->app->DB->SelectArr(
      "SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, ke.betrag as betrag, 
       k.id as zeile,k.waehrung 
      FROM kontoauszuege_zahlungsausgang ke 
      LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id 
      LEFT JOIN konten ko ON k.konto=ko.id 
      WHERE (ke.objekt='gutschrift' AND ke.parameter='$id') 
         OR (ke.objekt='rechnung' AND ke.parameter='$rechnungid'  AND ke.parameter>0) 
        OR (ke.objekt='auftrag' AND ke.parameter='$auftragid'  AND ke.parameter>0)"
    );
    $cAusgangArr = empty($ausgangArr)?0:count($ausgangArr);
    for($i=0;$i<$cAusgangArr;$i++) {
      $waehrung = 'EUR';
      if($ausgangArr[$i]['waehrung']) {
        $waehrung = $ausgangArr[$i]['waehrung'];
      }
      $ausgang .="<tr><td class=auftrag_cell>".$ausgangArr[$i]['datum']."</td><td class=auftrag_cell>"
        .$ausgangArr[$i]['konto']."&nbsp;(<a href=\"index.php?module=zahlungseingang&action=editzeile&id="
        .$ausgangArr[$i]['zeile']."\">zur Buchung</a>)</td><td class=auftrag_cell align=right>"
        .$this->app->erp->EUR($ausgangArr[$i]['betrag'])
        ." $waehrung</td></tr>";
    }

    $saldo = $this->app->erp->EUR($this->app->erp->GutschriftSaldo($id));

    if($saldo < 0) {
      $saldo = "<b style=\"color:red\">$saldo</b>";
    }
    $waehrung = $this->app->DB->Select("SELECT waehrung FROM gutschrift WHERE id = '$id' LIMIT 1");
    if(!$waehrung) {
      $waehrung = 'EUR';
    }
    $ausgang .="<tr><td class=auftrag_cell></td><td class=auftrag_cell align=right>Saldo</td><td class=auftrag_cell align=right>$saldo $waehrung</td></tr>";

    if($return) {
      return "<table width=100% border=0 class=auftrag_cell cellpadding=0 cellspacing=0>".$eingang." ".$ausgang."</table>";
    }
  }


  /**
   * @param string|int $id
   *
   * @return int
   */
  public function GutschriftFreigabe($id='')
  {
    if($id<=0) {
      $id = (int)$this->app->Secure->GetGET('id');
      $freigabe= $this->app->Secure->GetGET('freigabe');
    }
    else {
      $intern = true;
      $freigabe=$intern;
    }
    $allowedFrm = true;
    $showDefault = true;
    $this->app->Tpl->Set('TABTEXT','Freigabe');
    $this->app->erp->GutschriftNeuberechnen($id);

    $this->app->erp->CheckVertrieb($id,'gutschrift');
    $this->app->erp->CheckBearbeiter($id,'gutschrift');
    $doctype = 'gutschrift';
    if(empty($intern)){
      $this->app->erp->RunHook('beleg_freigabe', 4, $doctype, $id, $allowedFrm, $showDefault);
    }
    if($allowedFrm && $freigabe==$id) {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM gutschrift WHERE id='$id' LIMIT 1");
      if($belegnr=='') {
        $this->app->erp->BelegFreigabe('gutschrift',$id);
        if($intern) {
          return 1;
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Gutschrift wurde freigegeben und kann jetzt versendet werden!</div>");
        $this->app->Location->execute("index.php?module=gutschrift&action=edit&id=$id&msg=$msg");
      }
      if($intern) {
        return 0;
      }
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Gutschrift war bereits freigegeben!</div>");
      $this->app->Location->execute("index.php?module=gutschrift&action=edit&id=$id&msg=$msg");
    }
    if($showDefault){
      $name = $this->app->DB->Select("SELECT a.name FROM gutschrift b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      $summe = $this->app->DB->Select("SELECT soll FROM gutschrift WHERE id='$id' LIMIT 1");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM gutschrift_position
        WHERE gutschrift='$id' LIMIT 1");

      $this->app->Tpl->Set('TAB1', "<div class=\"info\">Soll die Gutschrift an <b>$name</b> im Wert von <b>$summe $waehrung</b> 
        jetzt freigegeben werden? <input type=\"button\" value=\"Freigabe\" onclick=\"window.location.href='index.php?module=gutschrift&action=freigabe&id=$id&freigabe=$id'\">
        </div>");
    }
    $this->GutschriftMenu();
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  function GutschriftAbschicken()
  {
    $this->GutschriftMenu();
    $this->app->erp->DokumentAbschicken();
  }

  function GutschriftDelete()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM gutschrift WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM gutschrift WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM gutschrift WHERE id='$id' LIMIT 1");

    if($belegnr=="0" || $belegnr=="")
    {

      $this->app->erp->DeleteGutschrift($id);
      $belegnr="ENTWURF";
      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Gutschrift \"$name\" ($belegnr) wurde gel&ouml;scht!</div>");
      //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
      header("Location: index.php?module=gutschrift&action=list&msg=$msg");
      exit;
    } else
    {
      if(0)//$status=="versendet")
      {
        // KUNDE muss RMA starten                                                                                                                             
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Gutschrift \"$name\" ($belegnr) kann nicht storniert werden sie bereits versendet ist.</div>");
      }
      else
      {
        $maxbelegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM gutschrift");
        if(0)//$maxbelegnr == $belegnr)
        {
          $this->app->DB->Delete("DELETE FROM gutschrift_position WHERE gutschrift='$id'");
          $this->app->DB->Delete("DELETE FROM gutschrift_protokoll WHERE gutschrift='$id'");
          $this->app->DB->Delete("DELETE FROM gutschrift WHERE id='$id'");
          $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Gutschrift \"$name\" ($belegnr) wurde ge&ouml;scht !</div>");
        } else
        {
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Gutschrift \"$name\" ($belegnr) kann nicht storniert werden da sie bereits freigegeben oder versendet ist!</div>");
        }
        header("Location: index.php?module=gutschrift&action=list&msg=$msg");
        exit;
      }
      header("Location: index.php?module=gutschrift&action=list&msg=$msg#tabs-1");
      exit;
    }

  }


  function GutschriftDelete2()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM gutschrift WHERE id='$id' LIMIT 1");

    if($belegnr=="" || $belegnr=="0")
    {
      $this->app->erp->DeleteGutschrift($id);
      $this->app->Tpl->Set('MESSAGE',"<div class=\"info\">Gutschrift wurde gel&ouml;scht!</div>");
    } else 
    {
      $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Die Gutschrift kann nicht mehr gel&ouml;scht werden, da diese bereits versendet wurde!</div>");
    }
    $this->GutschriftList();

  }

  function GutschriftProtokoll()
  {
    $this->GutschriftMenu();
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Set('TABTEXT',"Protokoll");
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM gutschrift_protokoll WHERE gutschrift='$id' ORDER by zeit DESC");
    $tmp->DisplayNew('TAB1',"Protokoll","noAction");

    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }


  function GutschriftInlinePDF()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->GutschriftNeuberechnen($id);

    $frame = $this->app->Secure->GetGET('frame');

    if($frame=='') {
      $projekt = $this->app->DB->Select(
        sprintf(
          "SELECT `projekt` FROM `gutschrift` WHERE `id` = %d LIMIT 1",
          $id
        )
      );
      if(class_exists('GutschriftPDFCustom')) {
        $Brief = new GutschriftPDFCustom($this->app,$projekt);
      }
      else{
        $Brief = new GutschriftPDF($this->app,$projekt);
      }
      $Brief->GetGutschrift($id);
      $Brief->inlineDocument();
    }
    else {
      $file = urlencode("../../../../index.php?module=gutschrift&action=inlinepdf&id=$id");
      echo "<iframe width=\"100%\" height=\"100%\" style=\"height:calc(100vh - 110px)\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
      $this->app->ExitXentral();
    }
  }


  public function GutschriftPDF()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->GutschriftNeuberechnen($id);
    $projekt = $this->app->DB->Select("SELECT projekt FROM gutschrift WHERE id='$id' LIMIT 1");

    //    if(is_numeric($belegnr) && $belegnr!=0)
    {
      if(class_exists('GutschriftPDFCustom'))
      {
        $Brief = new GutschriftPDFCustom($this->app,$projekt);
      }
      else{
        $Brief = new GutschriftPDF($this->app,$projekt);
      }
      $Brief->GetGutschrift($id);
      $Brief->displayDocument(); 
    }// else
    // $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Noch nicht freigegebene Gutschriften k&ouml;nnen nicht als PDF betrachtet werden.!</div>");

    $this->GutschriftList();
  }


  function GutschriftMenu()
  {
    $id = $this->app->Secure->GetGET('id');

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM gutschrift WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM gutschrift WHERE id='$id' LIMIT 1");

    if($belegnr=='0' || $belegnr=='') {
      $belegnr ='(Entwurf)';
    }
    //    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Gutschrift $belegnr");
    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$name Gutschrift $belegnr");

    $this->app->erp->GutschriftNeuberechnen($id);

    // status bestell
    $status = $this->app->DB->Select("SELECT status FROM gutschrift WHERE id='$id' LIMIT 1");


    if ($status==='angelegt') {
      $this->app->erp->MenuEintrag('index.php?module=gutschrift&action=freigabe&id='.$id,'Freigabe');
    }

    $this->app->erp->MenuEintrag('index.php?module=gutschrift&action=edit&id='.$id,'Details');

    $anzahldateien = $this->app->erp->AnzahlDateien('Gutschein',$id);
    if($anzahldateien > 0) {
      $anzahldateien = ' ('.$anzahldateien.')';
    } else {
      $anzahldateien='';
    }

    $this->app->erp->MenuEintrag('index.php?module=gutschrift&action=dateien&id='.$id,'Dateien'.$anzahldateien);
    $this->app->erp->MenuEintrag('index.php?module=gutschrift&action=zahlungsmahnungswesen&id='.$id,'Zahlung-/Mahnwesen');

    $this->app->erp->RunMenuHook('gutschrift');

    $this->app->erp->MenuEintrag('index.php?module=gutschrift&action=list','Zur&uuml;ck zur &Uuml;bersicht');
  }


  public function GutschriftPositionen()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->GutschriftNeuberechnen($id);
    $this->app->YUI->AARLGPositionen(false);
  }

  public function CopyGutschriftPosition()
  {
    $this->app->YUI->SortListEvent('copy','gutschrift_position','gutschrift');
    $this->GutschriftPositionen();
  }
  
  public function DelGutschriftPosition()
  {
    $this->app->YUI->SortListEvent('del','gutschrift_position','gutschrift');
    $this->GutschriftPositionen();
  }

  public function UpGutschriftPosition()
  {
    $this->app->YUI->SortListEvent('up','gutschrift_position','gutschrift');
    $this->GutschriftPositionen();
  }

  public function DownGutschriftPosition()
  {
    $this->app->YUI->SortListEvent('down','gutschrift_position','gutschrift');
    $this->GutschriftPositionen();
  }


  public function GutschriftPositionenEditPopup()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'getopenaccordions')
    {
      $accordions = $this->app->Secure->GetPOST('accordions');
      $accordions = explode('*|*',$accordions);
      foreach($accordions as $k => $v) {
        if(empty($v)) {
          unset($accordions[$k]);
        }
        else{
          $accordions[$k] = 'gutschrift_accordion'.$v;
        }
      }
      $ret = [];
      if(!empty($accordions)) {
        $accordions = $this->app->User->GetParameter($accordions);
        if(!empty($accordions)) {
          foreach($accordions as $v) {
            if(!empty($v['value'])) {
              $ret['accordions'][] = str_replace('gutschrift_accordion','',$v['name']);
            }
          }
        }
      }
      echo json_encode($ret);
      $this->app->ExitXentral();
    }
    if($cmd === 'setaccordion') {
      $name = $this->app->Secure->GetPOST('name');
      $active = $this->app->Secure->GetPOST('active');
      $this->app->User->SetParameter('gutschrift_accordion'.$name, $active);
      echo json_encode(array('success'=>1));
      $this->app->ExitXentral();
    }
    $id = $this->app->Secure->GetGET('id');

    $artikel= $this->app->DB->Select("SELECT artikel FROM gutschrift_position WHERE id='$id' LIMIT 1");

    // nach page inhalt des dialogs ausgeben
    $filename = 'widgets/widget.gutschrift_position_custom.php';
    if(is_file($filename)) {
      include_once $filename;
      $widget = new WidgetGutschrift_positionCustom($this->app,'PAGE');
    }
    else {
      $widget = new WidgetGutschrift_position($this->app,'PAGE');
    }
    $sid= $this->app->DB->Select("SELECT gutschrift FROM gutschrift_position WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute('close_refresh',
        "index.php?module=gutschrift&action=positionen&id=$sid");
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }


  public function GutschriftEdit()
  {
    $action = $this->app->Secure->GetGET('action');
    $id = $this->app->Secure->GetGET('id');
    if($this->app->Secure->GetPOST('resetextsoll')) {
      $this->app->DB->Update(
        sprintf(
          'UPDATE gutschrift SET extsoll = 0 WHERE id = %d',
          $id
        )
      );
      $this->app->erp->GutschriftNeuberechnen($id);
    }
    // zum aendern vom Vertrieb
    $sid = $this->app->Secure->GetGET("sid");
    $cmd = $this->app->Secure->GetGET("cmd");
    
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
          $check2 = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.objekt like 'gutschrift' AND ds.sort = '$sort' AND d.geloescht <> 1 AND ds.parameter = '$id' LIMIT 1");
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
    
    if($this->app->erp->VertriebAendern('gutschrift',$id,$cmd,$sid)) {
      return;
    }
    if($this->app->erp->InnendienstAendern('gutschrift',$id,$cmd,$sid)){
      return;
    }

    if($this->app->erp->Firmendaten('modul_verband')!='1') {
      $this->app->Tpl->Set('VERBANDSTART','<!--');
      $this->app->Tpl->Set('VERBANDENDE','-->');
    }

    if($this->app->erp->DisableModul('gutschrift',$id)) {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->GutschriftMenu();
      return;
    }

    $adresse = $this->app->DB->Select("SELECT adresse FROM gutschrift WHERE id='$id' LIMIT 1");
    if($adresse <=0)
    {
      $this->app->Tpl->Add('JAVASCRIPT','$(document).ready(function() { if(document.getElementById("adresse"))document.getElementById("adresse").focus(); });');
      $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Achtung! Dieses Dokument ist mit keiner Kunden-Nr. verlinkt. Bitte geben Sie die Kundennummer an und klicken Sie &uuml;bernehmen oder Speichern!</div>");
    }
    $this->app->YUI->AARLGPositionen();

    $this->app->erp->CheckBearbeiter($id,"gutschrift");
    $this->app->erp->CheckBuchhaltung($id,"gutschrift");


    $this->app->erp->GutschriftNeuberechnen($id);

    $this->app->erp->DisableVerband();

    //$this->GutschriftMiniDetail(MINIDETAIL,false);
    $this->app->Tpl->Set('ICONMENU',$this->GutschriftIconMenu($id));
    $this->app->Tpl->Set('ICONMENU2',$this->GutschriftIconMenu($id,2));


    $belegnr = $this->app->DB->Select("SELECT belegnr FROM gutschrift WHERE id='$id' LIMIT 1");
    $nummer = $this->app->DB->Select("SELECT belegnr FROM gutschrift WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM gutschrift WHERE id='$id' LIMIT 1");
    $adresse = $this->app->DB->Select("SELECT adresse FROM gutschrift WHERE id='$id' LIMIT 1");
    


    $status= $this->app->DB->Select("SELECT status FROM gutschrift WHERE id='$id' LIMIT 1");
    $schreibschutz= $this->app->DB->Select("SELECT schreibschutz FROM gutschrift WHERE id='$id' LIMIT 1");
    if($status !== 'angelegt' && $status !== 'angelegta' && $status !== 'a')
    {
      $Brief = new Briefpapier($this->app);
      if($Brief->zuArchivieren($id, 'gutschrift'))
      {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Die Gutschrift ist noch nicht archiviert! Bitte versenden oder manuell archivieren. <input type=\"button\" onclick=\"if(!confirm('Soll das Dokument archiviert werden?')) return false;else window.location.href='index.php?module=gutschrift&action=archivierepdf&id=$id';\" value=\"Manuell archivieren\" /> <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('gutschrift',$id)\"></div>");
      }
      elseif(!$this->app->DB->Select("SELECT versendet FROM gutschrift WHERE id = '$id' LIMIT 1"))
      {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Die Gutschrift wurde noch nicht versendet! <input type=\"button\" onclick=\"if(!confirm('Soll das Dokument archiviert werden?')) return false;else window.location.href='index.php?module=gutschrift&action=archivierepdf&id=$id';\" value=\"Manuell archivieren\" /> <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('gutschrift',$id)\"></div>");
      }
    }
    
    if($schreibschutz!='1' && $this->app->erp->RechteVorhanden('gutschrift','schreibschutz')){
      $this->app->erp->AnsprechpartnerButton($adresse);
    }

    if($nummer!='')
    {
      $this->app->Tpl->Set('NUMMER',$nummer);
      if($this->app->erp->RechteVorhanden('adresse','edit')){
        $this->app->Tpl->Set('KUNDE', "&nbsp;&nbsp;&nbsp;Kd-Nr. <a href=\"index.php?module=adresse&action=edit&id=$adresse\" target=\"_blank\">" . $kundennummer . "</a>");
      }
      else{
        $this->app->Tpl->Set('KUNDE', '&nbsp;&nbsp;&nbsp;Kd-Nr. ' . $kundennummer);
      }
    }

    $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM gutschrift WHERE id='$id' LIMIT 1");
    if($this->app->Secure->GetPOST('zahlungsweise')!='') {
      $zahlungsweise = $this->app->Secure->GetPOST('zahlungsweise');
    }
    $zahlungsweise = strtolower($zahlungsweise);
    $this->app->Tpl->Set('RECHNUNG',"none");
    $this->app->Tpl->Set('UEBERWEISUNG',"none");
    $this->app->Tpl->Set('KREDITKARTE',"none");
    $this->app->Tpl->Set('VORKASSE',"none");
    $this->app->Tpl->Set('PAYPAL',"none");
    $this->app->Tpl->Set('EINZUGSERMAECHTIGUNG',"none");
    if($zahlungsweise=="rechnung") $this->app->Tpl->Set('RECHNUNG',"");
    if($zahlungsweise=="paypal") $this->app->Tpl->Set('PAYPAL',"");
    if($zahlungsweise=="ueberweisung") $this->app->Tpl->Set('UEBERWEISUNG',"");
    if($zahlungsweise=="kreditkarte") $this->app->Tpl->Set('KREDITKARTE',"");
    if($zahlungsweise=="einzugsermaechtigung" || $zahlungsweise=="lastschrift") $this->app->Tpl->Set('EINZUGSERMAECHTIGUNG',"");
    if($zahlungsweise=="vorkasse" || $zahlungsweise=="kreditkarte" || $zahlungsweise=="paypal" || $zahlungsweise=="bar") $this->app->Tpl->Set('VORKASSE',"");



    if($schreibschutz=="1" && $this->app->erp->RechteVorhanden("gutschrift","schreibschutz"))
    {
      $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">Diese Gutschrift ist schreibgesch&uuml;tzt und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml;r diese Gutschrift wirklich entfernt werden?')) return false;else window.location.href='index.php?module=gutschrift&action=schreibschutz&id=$id';\"></div>");
      //      $this->app->erp->CommonReadonly();
    }

    if($schreibschutz=="1")
      $this->app->erp->CommonReadonly();

    $rechnungid = $this->app->DB->Select("SELECT rechnungid FROM gutschrift WHERE id='$id' LIMIT 1");
    $rechnungid = $this->app->DB->Select("SELECT id FROM rechnung WHERE id='$rechnungid' AND belegnr!='' LIMIT 1");
    $alle_gutschriften = $this->app->DB->SelectArr("SELECT id,belegnr FROM gutschrift WHERE rechnungid='$rechnungid' AND rechnungid>0");

    if(count($alle_gutschriften) > 1)
    {
      for($agi=0;$agi<count($alle_gutschriften);$agi++)
        $gutschriften .= "<a href=\"index.php?module=gutschrift&action=edit&id=".$alle_gutschriften[$agi][id]."\" target=\"_blank\">".$alle_gutschriften[$agi][belegnr]."</a> ";
      $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">F&uuml;r die angebene Rechnung gibt es schon folgende Gutschriften: $gutschriften</div>");
    }


    //    if($status=="versendet")
    //      $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Diese Gutschrift wurde bereits versendet und darf daher nicht mehr bearbeitet werden!</div>");

    if($status=="")
      $this->app->DB->Update("UPDATE gutschrift SET status='angelegt' WHERE id='$id' LIMIT 1");
    if($schreibschutz != '1'){
      if($this->app->erp->Firmendaten("schnellanlegen") == "1"){
        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN', '      <input type="button" value="&uuml;bernehmen" onclick="document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
          ');
      }else{
        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN', '
          <input type="button" value="&uuml;bernehmen" onclick="if(!confirm(\'Soll der neue Kunde wirklich &uuml;bernommen werden? Es werden alle Felder &uuml;berladen.\')) return false;else document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
          ');
      }
    }

    // immer wenn sich der lieferant genändert hat standartwerte setzen
    if($this->app->Secure->GetPOST("adresse")!="")
    {
      $tmp = $this->app->Secure->GetPOST("adresse");
      $kundennummer = $this->app->erp->FirstTillSpace($tmp);
      $filter_projekt = $this->app->DB->Select("SELECT projekt FROM gutschrift WHERE id = '$id' LIMIT 1");
      //if($filter_projekt)$filter_projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id= '$filter_projekt' and eigenernummernkreis = 1 LIMIT 1");
      $name = substr($tmp,6);
      $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' AND geloescht=0 ".$this->app->erp->ProjektRechte("projekt", true, 'vertrieb')." ORDER by ".($filter_projekt?" projekt = '$filter_projekt' DESC, ":"")." projekt LIMIT 1");

      $uebernehmen =$this->app->Secure->GetPOST("uebernehmen");
      if($uebernehmen=="1" && $schreibschutz != '1') // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
      {
        $this->app->erp->LoadGutschriftStandardwerte($id,$adresse);
        $this->app->erp->GutschriftNeuberechnen($id);
        header("Location: index.php?module=gutschrift&action=edit&id=$id");
        exit;
      }
    }

    // optional rechnungen als bezahlt markieren wenn es jetzt gutschriften gibt

    $land = $this->app->DB->Select("SELECT land FROM gutschrift WHERE id='$id' LIMIT 1");
    $ustid = $this->app->DB->Select("SELECT ustid FROM gutschrift WHERE id='$id' LIMIT 1");
    $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM gutschrift WHERE id='$id' LIMIT 1");
    if($ust_befreit) {
      $this->app->Tpl->Set('USTBEFREIT',"<div class=\"info\">EU-Lieferung <br>(bereits gepr&uuml;ft!)</div>");
    }
    else if($land!=='DE' && $ustid!='') {
      $this->app->Tpl->Set('USTBEFREIT',"<div class=\"error\">EU-Lieferung <br>(Fehler bei Pr&uuml;fung!)</div>");
    }


    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $table->Query("SELECT bezeichnung as artikel, nummer as Nummer, menge, vpe as VPE, FORMAT(preis,4) as preis
        FROM gutschrift_position
        WHERE gutschrift='$id'");
    $table->DisplayNew('POSITIONEN','Preis','noAction');
    /*
       $table->Query("SELECT nummer as Nummer, menge,vpe as VPE, FORMAT(preis,4) as preis, FORMAT(menge*preis,4) as gesamt
       FROM gutschrift_position
       WHERE gutschrift='$id'");
       $table->DisplayNew(POSITIONEN,"Preis","noAction");
     */
    $summe = $this->app->DB->Select("SELECT FORMAT(SUM(menge*preis),2) FROM gutschrift_position
        WHERE gutschrift='$id'");
    $waehrung = $this->app->DB->Select("SELECT waehrung FROM gutschrift_position
        WHERE gutschrift='$id' LIMIT 1");

    if($summe > 0)
      $this->app->Tpl->Add('POSITIONEN', "<br><center>Gesamtsumme: <b>$summe $waehrung</b>&nbsp;&nbsp;
          <a href=\"index.php?module=buchhaltung&action=preview&frame=false\" onclick=\"makeRequest(this);return false\"><img src=\"./themes/new/images/money_preview.png\" border=\"0\"></a></center>");

    $status= $this->app->DB->Select("SELECT status FROM gutschrift WHERE id='$id' LIMIT 1");
    //    $this->app->Tpl->Set(STATUS,"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly>");
    $this->app->Tpl->Set('STATUS',"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");

    $internet = $this->app->DB->Select("SELECT a.internet FROM gutschrift g LEFT JOIN rechnung r ON r.id=g.rechnungid LEFT JOIN auftrag a ON a.id=r.auftragid WHERE g.id='$id' AND g.id > 0 LIMIT 1");
    if($internet!="")
    {
      $this->app->Tpl->Set('INTERNET',"<tr><td>Internet:</td><td><input type=\"text\" size=\"30\" value=\"".$internet."\" readonly [COMMONREADONLYINPUT]></td></tr>");
    }


    $this->app->Tpl->Set('AKTIV_TAB1','selected');

    $sollExtSoll = $this->app->DB->SelectRow(
      sprintf(
        "SELECT extsoll, soll 
        FROM gutschrift 
        WHERE id = %d AND schreibschutz = 0 AND status = 'versendet' AND extsoll <> 0",
        $id
      )
    );
    if(!empty($sollExtSoll['extsoll']) && $sollExtSoll['extsoll'] == $sollExtSoll['soll']) {
      $sollExtSoll['soll'] = $this->app->DB->Select(
        sprintf(
          'SELECT ROUND(SUM(`umsatz_brutto_gesamt`),2) FROM `gutschrift_position` WHERE `gutschrift` = %d ',
          $id
        )
      );
    }

    if(!empty($sollExtSoll) && $sollExtSoll['soll'] != $sollExtSoll['extsoll']) {
      $extsoll = $sollExtSoll['extsoll'];
      $this->app->Tpl->Add(
        'MESSAGE','<form method="post"><div class="error">
          Der Sollbetrag stimmt nicht mehr mit urspr&uuml;nglich festgelegten Betrag '.
        number_format($extsoll,2,',','.').
        ' &uuml;berein <input type="submit" name="resetextsoll" value="Festgeschriebene Summe zur&uuml;cksetzen" /></div></form>'
      );
    }

    parent::GutschriftEdit();
    if($id > 0 && $this->app->DB->Select(
        sprintf(
          'SELECT id FROM gutschrift WHERE schreibschutz =1  AND zuarchivieren = 1 AND id = %d',
          $id
        )
      )
    ) {
      $this->app->erp->PDFArchivieren('gutschrift', $id, true);
    }
    $this->app->erp->MessageHandlerStandardForm();


    if($this->app->Secure->GetPOST('weiter')!='') {
      $this->app->Location->execute('index.php?module=gutschrift&action=positionen&id='.$id);
    }
    $this->GutschriftMenu();
  }

  public function GutschriftCreate()
  {
    $this->app->erp->MenuEintrag('index.php?module=gutschrift&action=list','Zur&uuml;ck zur &Uuml;bersicht');

    $anlegen = $this->app->Secure->GetGET('anlegen');

    if($anlegen!='1' && $this->app->erp->Firmendaten('schnellanlegen')=='1') {
      $this->app->Location->execute('index.php?module=gutschrift&action=create&anlegen=1');
    }

    if($anlegen != '') {
      $id = $this->app->erp->CreateGutschrift();
      $this->app->Location->execute('index.php?module=gutschrift&action=edit&id='.$id);
    }
    $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">M&ouml;chten Sie eine Gutschrift jetzt anlegen? &nbsp;
        <input type=\"button\" onclick=\"window.location.href='index.php?module=gutschrift&action=create&anlegen=1'\" value=\"Ja - Gutschrift jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set('TAB1',"
        <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><b style=\"font-size: 14pt\">Gutschriften in Bearbeitung</b>
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
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr!='',belegnr,'ohne Nummer') as beleg, name, status, id
        FROM gutschrift WHERE status='angelegt' order by datum DESC, id DESC");
    $table->DisplayNew('AUFTRAGE', "<a href=\"index.php?module=gutschrift&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=gutschrift&action=delete&id=%value%';\">
        <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=gutschrift&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
        ");

    $this->app->Tpl->Set('TABTEXT','Gutschrift anlegen');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  public function GutschriftList()
  {
    $this->app->Tpl->Set('UEBERSCHRIFT', 'Gutschriften');

    if($this->app->Secure->GetPOST('ausfuehren') && $this->app->erp->RechteVorhanden('gutschrift', 'edit')) {
      $drucker = $this->app->Secure->GetPOST('seldrucker');
      $aktion = $this->app->Secure->GetPOST('sel_aktion');
      $auswahl = $this->app->Secure->GetPOST('auswahl');
      if($drucker > 0) {
        $this->app->erp->BriefpapierHintergrundDisable($drucker);
      }
      if(is_array($auswahl)) {
        $ids = [];
        foreach($auswahl as $id) {
          $id = (int)$id;
          if($id > 0) {
            $ids[] = $id;
          }
        }
        $ids = array_unique($ids);

        switch($aktion) {
          case 'erledigtam':
            if(!empty($ids)){
              $this->app->DB->Update(
                sprintf(
                  "UPDATE `gutschrift` 
                  SET `manuell_vorabbezahlt`=CURDATE(), 
                  `manuell_vorabbezahlt_hinweis`=CONCAT(`manuell_vorabbezahlt_hinweis`,'\r\n','Erledigt am manuell auf %s gesetzt') 
                  WHERE `id` IN (%s)",
                  date('d.m.Y'), implode(',', $ids)
                )
              );
            }
          break;
          case 'offen':
            if(!empty($ids)) {
              $this->app->DB->Update(
                sprintf(
                  "UPDATE `gutschrift` 
                  SET `manuell_vorabbezahlt` = NULL, 
                    `manuell_vorabbezahlt_hinweis`=CONCAT(`manuell_vorabbezahlt_hinweis`,'\r\n','Erledigt am manuell zurückgesetzt am %s') 
                  WHERE `id` IN (%s)",
                  date('d.m.Y'), implode(',', $ids)
                )
              );
            }
          break;
          case 'mail':
            $returnOrders = empty($ids)?[]:$this->app->DB->SelectArr(
              sprintf(
                "SELECT * FROM `gutschrift` WHERE `id` IN (%s)",
                implode(',', $ids)
              )
            );
            if(empty($returnOrders)) {
              $returnOrders = [];
            }
            foreach($returnOrders as $returnOrder) {
              $v = $returnOrder['id'];
              $email = (string)$returnOrder['email'];
              $adresse = $returnOrder['adresse'];
              $projekt = $returnOrder['projekt'];
              $name = $returnOrder['name'];
              $sprache = $returnOrder['sprache'];
              if($sprache=='' && $adresse > 0){
                $sprache = $this->app->DB->Select(
                  sprintf(
                    'SELECT `sprache` FROM `adresse` WHERE `id`= %d AND `geloescht` = 0 LIMIT 1',
                    $adresse
                  )
                );
              }

              if($sprache=='') {
                $sprache='de';
              }

              $emailtext = $this->app->erp->Geschaeftsbriefvorlage($sprache,'gutschrift',$projekt,$name,$v);

              if($email === '' && $adresse > 0) {
                $email = (string)$this->app->DB->Select(
                  sprintf(
                    "SELECT `email` FROM `adresse` WHERE `id` = %d LIMIT 1",
                    $adresse
                  )
                );
              }
              if($email !== '') {
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('GutschriftPDFCustom')) {
                  $Brief = new GutschriftPDFCustom($this->app,$projekt);
                }
                else{
                  $Brief = new GutschriftPDF($this->app,$projekt);
                }
                $Brief->GetGutschrift($v);
                $_tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                unlink($_tmpfile);
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('GutschriftPDFCustom')) {
                  $Brief = new GutschriftPDFCustom($this->app,$projekt);
                }
                else{
                  $Brief = new GutschriftPDF($this->app,$projekt);
                }
                $Brief->GetGutschrift($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();

                $fileid = $this->app->erp->CreateDatei(
                  $Brief->filename,'gutschrift','','',$tmpfile,$this->app->User->GetName()
                );
                $this->app->erp->AddDateiStichwort($fileid,'gutschrift','gutschrift',$v);
                $this->app->erp->DokumentSend(
                  $adresse,'gutschrift', $v, 'email',$emailtext['betreff'],$emailtext['text'],
                  [$tmpfile],'','',$projekt,$email, $name
                );
                $ansprechpartner = $name.' <'.$email.'>';
                $this->app->DB->Insert("INSERT INTO dokumente_send
                    (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,versendet,dateiid) 
                    VALUES ('','gutschrift',NOW(),'".$this->app->DB->real_escape_string($this->app->User->GetName())."',
                      '$adresse','$v','email','$betreff','$text','$projekt','$ansprechpartner',1,'$fileid')");
                $tmpid = $this->app->DB->GetInsertID();
                unlink($tmpfile);
                $this->app->DB->Update("UPDATE gutschrift SET versendet=1, versendet_am=NOW(),
                  versendet_per='email',versendet_durch='".$this->app->DB->real_escape_string($this->app->User->GetName())."',schreibschutz='1' WHERE id='$v' LIMIT 1");
                $this->app->erp->GutschriftProtokoll($v,'Gutschrift versendet');
              }
            }
          break;
          case 'versendet':
            $returnOrders = empty($ids)?[]: $this->app->DB->SelectPairs(
              sprintf(
                'SELECT `id`, `projekt` FROM `gutschrift` WHERE `id` IN (%s)',
                implode(',', $ids)
              )
            );
            foreach($returnOrders as $returnOrderId => $projectId) {
              if(class_exists('GutschriftPDFCustom')) {
                $Brief = new GutschriftPDFCustom($this->app,$projectId);
              }
              else{
                $Brief = new GutschriftPDF($this->app,$projectId);
              }
              $Brief->GetGutschrift($returnOrderId);
              $tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              $this->app->erp->GutschriftProtokoll($returnOrderId, 'Gutschrift versendet');
              $this->app->DB->Update(
                sprintf(
                  "UPDATE `gutschrift` 
                  SET `schreibschutz`=1, `versendet` = 1, `status`='versendet' 
                  WHERE `id` = %d 
                  LIMIT 1",
                  $returnOrderId
                )
              );
              @unlink($tmpfile);
            }
          break;
          case 'drucken':
            if($drucker && !empty($ids)) {
              $returnOrders = $this->app->DB->SelectPairs(
                sprintf(
                  'SELECT `id`, `projekt` FROM `gutschrift` WHERE `id` IN (%s)',
                  implode(',', $ids)
                )
              );
              foreach($returnOrders as $returnOrderId => $projekt) {
                if(class_exists('GutschriftPDFCustom')) {
                  $Brief = new GutschriftPDFCustom($this->app,$projekt);
                }
                else{
                  $Brief = new GutschriftPDF($this->app,$projekt);
                }
                $Brief->GetGutschrift($returnOrderId);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->printer->Drucken($drucker,$tmpfile);
                $doctype = 'gutschrift';
                $adressId = $this->app->DB->Select("SELECT adresse FROM gutschrift WHERE id = '$returnOrderId' LIMIT 1");
                $this->app->erp->RunHook('dokumentsend_ende', 5, $doctype, $returnOrderId, $projekt, $adressId, $aktion);
                $this->app->erp->GutschriftProtokoll($returnOrderId,'Gutschrift versendet');
                $this->app->DB->Update(
                  sprintf(
                    "UPDATE `gutschrift` 
                    SET `schreibschutz` = 1, `versendet` = 1, `status`='versendet' 
                    WHERE `id` = %d 
                    LIMIT 1",
                    $returnOrderId
                  )
                );
              }
            }
          break;
          case 'pdf':
            $tmpfile = [];
            $returnOrders = empty($ids)?[]: $this->app->DB->SelectPairs(
              sprintf(
                'SELECT `id`, `projekt` FROM `gutschrift` WHERE `id` IN (%s)',
                implode(',', $ids)
              )
            );
            foreach($returnOrders as $returnOrderId => $projectId) {
              if(class_exists('GutschriftPDFCustom')) {
                $Brief = new GutschriftPDFCustom($this->app,$projectId);
              }
              else{
                $Brief = new GutschriftPDF($this->app,$projectId);
              }
              $Brief->GetGutschrift($returnOrderId);
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

    $backurl = $this->app->Secure->GetGET('backurl');
    $backurl = $this->app->erp->base64_url_decode($backurl);

    $this->app->erp->MenuEintrag('index.php?module=gutschrift&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=gutschrift&action=create','Neue Gutschrift anlegen');

    if(strlen($backurl)>5){
      $this->app->erp->MenuEintrag((string)$backurl, 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    else{
      $this->app->erp->MenuEintrag('index.php', 'Zur&uuml;ck zur &Uuml;bersicht');
    }

    $zahlungsweisen = $this->app->DB->SelectArr('
      SELECT `zahlungsweise` 
      FROM `gutschrift`
      GROUP BY `zahlungsweise`
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
      SELECT status
      FROM gutschrift
      GROUP BY status
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
      SELECT versandart
      FROM gutschrift
      GROUP BY versandart
    ');

    $versandartenStr = '';
    if ($versandarten) {
      foreach ($versandarten as $versandart) {
        if (empty($versandart['versandart'])) {
          continue;
        }
        $versandartenStr .= '<option name="' . $versandart['versandart'] . '">' . ucfirst($versandart['versandart'])
          . '</option>';
      }
    }

    $laender = $this->app->erp->GetSelectLaenderliste();
    $laenderStr = '';
    foreach ($laender as $landKey => $land) {
      $laenderStr .= '<option value="' . $landKey . '">' . $land . '</option>';
    }

    $this->app->YUI->DatePicker('datumVon');
    $this->app->YUI->DatePicker('datumBis');
    $this->app->YUI->AutoComplete('projekt', 'projektname', 1);
    $this->app->YUI->AutoComplete('kundennummer', 'kunde', 1);
    $this->app->YUI->AutoComplete('gutschriftnummer', 'gutschrift', 1);
    $this->app->YUI->AutoComplete('artikel', 'artikelnummer', 1);

    $this->app->Tpl->Add('ZAHLUNGSWEISEN',$zahlungsweiseStr);
    $this->app->Tpl->Add('STATUS',$statusStr);
    $this->app->Tpl->Add('VERSANDARTEN',$versandartenStr);
    $this->app->Tpl->Add('LAENDER',$laenderStr);
    $this->app->Tpl->Parse('TAB1','gutschrift_table_filter.tpl');

    $this->app->Tpl->Set('AKTIV_TAB1','selected');
    $this->app->Tpl->Set('INHALT','');

    $this->app->YUI->TableSearch('TAB2','gutschriftenoffene');
    $this->app->YUI->TableSearch('TAB1','gutschriften');
    $this->app->YUI->TableSearch('TAB3','gutschrifteninbearbeitung');

    $this->app->Tpl->Set(
      'SELDRUCKER',
      $this->app->erp->GetSelectDrucker($this->app->User->GetParameter('rechnung_list_drucker'))
    );

    $this->app->Tpl->Parse('PAGE','gutschriftuebersicht.tpl');
  }

  /**
   * @param int $id
   *
   * @return int|string|null
   */
  public function CopyGutschrift($id)
  {
    $this->app->DB->Insert('INSERT INTO gutschrift (id) VALUES (NULL)');
    $newid = $this->app->DB->GetInsertID();
    $arr = $this->app->DB->SelectRow("SELECT NOW() as datum,projekt,bodyzusatz,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer, bearbeiter,zahlungszieltage,zahlungszieltageskonto,zahlungsweise,ohne_artikeltext,ohne_briefpapier,'angelegt' as status,typ,
            zahlungszielskonto,ust_befreit,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,gruppe,vertriebid,bearbeiterid,provision,provision_summe,sprache,anzeigesteuer,waehrung,kurs,kostenstelle,
            firma FROM gutschrift WHERE id='$id' LIMIT 1");
    $this->app->DB->LogIfError();
    $arr['kundennummer'] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '".$arr['adresse']."' LIMIT 1");
    $arr['bundesstaat'] = $this->app->DB->Select("SELECT bundesstaat FROM gutschrift WHERE id='$id' LIMIT 1");
    $this->app->DB->UpdateArr('gutschrift',$newid,'id',$arr, true);
    $pos = $this->app->DB->SelectArr("SELECT * FROM gutschrift_position WHERE gutschrift='$id'");
    $cpos = !empty($pos)?count($pos):0;
    for($i=0;$i<$cpos;$i++){
      $this->app->DB->Insert("INSERT INTO gutschrift_position (gutschrift) VALUES ($newid)");
      $newposid = $this->app->DB->GetInsertID();
      $pos[$i]['gutschrift']=$newid;
      $this->app->DB->UpdateArr('gutschrift_position',$newposid,'id',$pos[$i], true);
      if(is_null($pos[$i]['steuersatz'])){
        $this->app->DB->Update("UPDATE gutschrift_position SET steuersatz = null WHERE id = '$newposid' LIMIT 1");
      }
    }
    $this->app->erp->CheckFreifelder('gutschrift',$newid);
    $this->app->erp->CopyBelegZwischenpositionen('gutschrift',$id,'gutschrift',$newid);
    $this->app->erp->LoadSteuersaetzeWaehrung($newid,'gutschrift');

    return $newid;
  }

  /**
   * @param int $id
   * @param int $adresse
   */
  public function LoadGutschriftStandardwerte($id,$adresse)
  {
    if($id==0 || $id=='' || $adresse=='' || $adresse=='0') {
      return;
    }

    // standard adresse von lieferant
    $arr = $this->app->DB->SelectRow(
      sprintf(
        "SELECT adr.*,adr.vertrieb as vertriebid, '' as bearbeiter, adr.innendienst as bearbeiterid 
        FROM `adresse` AS `adr` 
        WHERE adr.id= %d AND adr.geloescht=0 
        LIMIT 1",
        $adresse
      )
    );

    if($arr['bearbeiterid'] <=0 ){
      $arr['bearbeiterid'] = $this->app->User->GetAdresse();
    }

    $arr['gruppe'] = $this->app->erp->GetVerband($adresse);

    $rolle_projekt = $this->app->DB->Select(
      sprintf(
        "SELECT ar.parameter 
        FROM `adresse_rolle` AS `ar`  
        WHERE ar.adresse= %d
          AND ar.subjekt='Kunde' AND ar.objekt='Projekt' 
          AND (IFNULL(ar.bis,'0000-00-00') ='0000-00-00' OR ar.bis <= CURDATE()) 
        LIMIT 1",
        $adresse
      )
    );

    if($rolle_projekt > 0) {
      $arr['projekt'] = $rolle_projekt;
    }

    $field = [
      'gln','anschreiben','name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid',
      'email','telefon','telefax','kundennummer','projekt','ust_befreit','gruppe','typ','vertriebid','bearbeiter',
      'ansprechpartner','bearbeiterid','titel','lieferbedingung'
    ];
    foreach($field as $key=>$value) {
      if($value ==='projekt' && $this->app->Secure->POST[$value]!='' && 0) {
        $uparr[$value] = str_replace("'", '&apos;',$this->app->Secure->POST[$value]);
      }
      else {
        $this->app->Secure->POST[$value] = str_replace("'", '&apos;',$arr[$value]);
        $uparr[$value] = str_replace("'", '&apos;',$arr[$value]);
      }
    }

    $uparr['adresse'] = $adresse;
    $uparr['ust_befreit'] = $this->app->erp->AdresseUSTCheck($adresse);
    $uparr['zahlungsstatusstatus']='offen';

    $this->app->DB->UpdateArr('gutschrift',$id,'id',$uparr,true);
    $uparr=null;

    //liefernantenvorlage
    $arr = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");
    $field = array('zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');

    // falls von Benutzer projekt ueberladen werden soll
    $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    if($projekt_bevorzugt=='1') {
      $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      $arr['projekt'] = $uparr['projekt'];
      $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr['projekt']."' AND id > 0 LIMIT 1");
    }

    $this->app->erp->LoadZahlungsweise($adresse,$arr);

    $this->app->Secure->POST['zahlungsweise'] = strtolower($arr['zahlungsweise']);
    $this->app->Secure->POST['zahlungszieltage'] = strtolower($arr['zahlungszieltage']);
    $this->app->Secure->POST['zahlungszieltageskonto'] = strtolower($arr['zahlungszieltageskonto']);
    $this->app->Secure->POST['zahlungszielskonto'] = strtolower($arr['zahlungszielskonto']);
    $this->app->Secure->POST['versandart'] = strtolower($arr['versandart']);

    if(isset($arr['usereditid'])){
      unset($arr['usereditid']);
    }

    // Enter the correct billing address into the credit note data before it gets saved.
    $arr = $this->app->Container->get('CreditNoteAddressService')->applyBillingAddressToCreditNoteArray($id, $arr);

    $this->app->DB->UpdateArr('gutschrift',$id,'id',$arr,true);
    $this->app->erp->LoadSteuersaetzeWaehrung($id,'gutschrift');
    $this->app->erp->LoadAdresseStandard('gutschrift',$id,$adresse);
  }

  /**
   * @param int $id
   *
   * @return int|mixed
   */
  public function GutschriftSaldo($id)
  {
    if($id <= 0) {
      return 0;
    }

    $rechnungid = $this->app->DB->Select(
      sprintf(
        'SELECT `rechnungid` FROM `gutschrift` WHERE `id`= %d LIMIT 1',
        $id
      )
    );
    $auftragid = $rechnungid <= 0?0:$this->app->DB->Select(
      sprintf(
        'SELECT `auftragid` FROM `rechnung` WHERE `id`=%d LIMIT 1',
        $rechnungid
      )
    );

    $eingangArr = $this->app->DB->SelectArr(
      sprintf(
        "SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%%d.%%m.%%Y') as datum, k.id as kontoauszuege, ke.betrag as betrag 
        FROM `kontoauszuege_zahlungseingang` AS `ke`
        LEFT JOIN `kontoauszuege` AS `k` ON ke.kontoauszuege=k.id 
        LEFT JOIN `konten` AS `ko` ON k.konto=ko.id 
        WHERE (ke.objekt='gutschrift' AND ke.parameter=%d) 
          OR (ke.objekt='auftrag' AND ke.parameter=%d AND ke.parameter>0)
          OR (ke.objekt='rechnung' AND ke.parameter=%d  AND ke.parameter>0)",
        $id, $auftragid, $rechnungid
      )
    );
    $einnahmen = 0;
    if(!empty($eingangArr)) {
      foreach($eingangArr AS $eingangRow) {
        $einnahmen += $eingangRow['betrag'];
      }
    }

    //$gutschriften = $this->app->DB->SelectArr("SELECT belegnr, DATE_FORMAT(datum,'%d.%m.%Y') as datum,soll FROM gutschrift WHERE rechnungid='$id' "); // alt
    $gutschriften = $this->app->DB->SelectArr(
      sprintf(
        "SELECT ro.belegnr, DATE_FORMAT(ro.datum,'%%d.%%m.%%Y') as datum, ro.soll 
        FROM `gutschrift` AS `ro` 
        WHERE ro.`id` = %d ",
        $id
      )
    );

    if(!empty($gutschriften)) {
      foreach($gutschriften as $gutschriftRow) {
        $einnahmen += $gutschriftRow['soll'];
      }
    }

    $ausgangArr = $this->app->DB->SelectArr(
      sprintf(
        "SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%%d.%%m') as datum, ke.betrag as betrag 
        FROM kontoauszuege_zahlungsausgang ke
        LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id 
        LEFT JOIN konten ko ON k.konto=ko.id 
        WHERE (ke.objekt='gutschrift' AND ke.parameter=%d) 
           OR (ke.objekt='rechnung' AND ke.parameter=%d  AND ke.parameter>0)                    
           OR (ke.objekt='auftrag' AND ke.parameter=%d AND ke.parameter>0)",
        $id, $rechnungid, $auftragid
      )
    );
    $ausgaben = 0;
    if(!empty($ausgangArr)){
      foreach($ausgangArr as $ausgangRow) {
        $ausgaben += $ausgangRow['betrg'];
      }
    }

    return $einnahmen - $ausgaben;
  }
}
