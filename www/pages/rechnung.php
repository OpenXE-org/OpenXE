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

include_once __DIR__.'/_gen/rechnung.php';
//require_once("Payment/DTA.php"); //PEAR

class Rechnung extends GenRechnung
{
  /**
   * Rechnung constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    /** @var Application app */
    $this->app=$app;
    if($intern)
    {
      return;
    }

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("list","RechnungList");
    $this->app->ActionHandler("create","RechnungCreate");
    $this->app->ActionHandler("positionen","RechnungPositionen");
    $this->app->ActionHandler("uprechnungposition","UpRechnungPosition");
    $this->app->ActionHandler("delrechnungposition","DelRechnungPosition");
    $this->app->ActionHandler("copyrechnungposition","CopyRechnungPosition");
    $this->app->ActionHandler("downrechnungposition","DownRechnungPosition");
    $this->app->ActionHandler("positioneneditpopup","RechnungPositionenEditPopup");
    $this->app->ActionHandler("search","RechnungSuche");
    $this->app->ActionHandler("mahnwesen","RechnungMahnwesen");
    $this->app->ActionHandler("edit","RechnungEdit");
    $this->app->ActionHandler("delete","RechnungDelete");
    $this->app->ActionHandler("gutschrift","RechnungGutschrift");
    $this->app->ActionHandler("copy","RechnungCopy");
    $this->app->ActionHandler("zertifikate","RechnungZertifikate");
    $this->app->ActionHandler("freigabe","RechnungFreigabe");
    $this->app->ActionHandler("abschicken","RechnungAbschicken");
    $this->app->ActionHandler("pdf","RechnungPDF");
    $this->app->ActionHandler("alternativpdf","RechnungAlternativPDF");
    $this->app->ActionHandler("inlinepdf","RechnungInlinePDF");
    $this->app->ActionHandler("lastschrift","RechnungLastschrift");
    $this->app->ActionHandler("protokoll","RechnungProtokoll");
    $this->app->ActionHandler("zahlungseingang","RechnungZahlungseingang");
    $this->app->ActionHandler("minidetail","RechnungMiniDetail");
    $this->app->ActionHandler("editable","RechnungEditable");
    $this->app->ActionHandler("livetabelle","RechnungLiveTabelle");
    $this->app->ActionHandler("schreibschutz","RechnungSchreibschutz");
    $this->app->ActionHandler("undostorno","RechnungUndostorno");
    $this->app->ActionHandler("manuellbezahltmarkiert","RechnungManuellBezahltMarkiert");
    $this->app->ActionHandler("manuellbezahltentfernen","RechnungManuellBezahltEntfernen");
    $this->app->ActionHandler("zahlungsmahnungswesen","RechnungZahlungMahnungswesen");
    $this->app->ActionHandler("deleterabatte","RechnungDeleteRabatte");
    $this->app->ActionHandler("updateverband","RechnungUpdateVerband");
    $this->app->ActionHandler("lastschriftwdh","RechnungLastschriftWdh");
    $this->app->ActionHandler("dateien","RechnungDateien");
    $this->app->ActionHandler("pdffromarchive","RechnungPDFfromArchiv");
    $this->app->ActionHandler("archivierepdf","RechnungArchivierePDF");

    $this->app->ActionHandler("summe","RechnungSumme"); // nur fuer rechte
    $this->app->ActionHandler("einkaufspreise","RechnungEinkaufspreise");
    $this->app->ActionHandler("steuer","RechnungSteuer");
    $this->app->ActionHandler("formeln","RechnungFormeln");

    $this->app->DefaultActionHandler("list");

    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetPOST("adresse");

    if($nummer==''){
      if($id > 0){
        $adresse = $this->app->DB->Select("SELECT a.name FROM rechnung b INNER JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      }else{
        $adresse = '';
      }
    }
    else{
      $adresse = $nummer;
    }
    if($id > 0){
      $nummer = $this->app->DB->Select("SELECT b.belegnr FROM rechnung b WHERE b.id='$id' LIMIT 1");
    }else{
      $nummer = '';
    }
    if($nummer=="" || $nummer==0) $nummer="ohne Nummer";

    $this->app->Tpl->Set('UEBERSCHRIFT',"Rechnung:&nbsp;".$adresse." (".$nummer.")");
    $this->app->Tpl->Set('FARBE',"[FARBE4]");

    $this->app->erp->Headlines('Rechnung');

    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
    $this->app->erp->RegisterHook('supersearch_detail', 'rechnung', 'RechnungSupersearchDetail');
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function RechnungSupersearchDetail($detailQuery, $detailResult)
  {
      if ($detailQuery->getGroupKey() !== 'invoices') {
          return;
      }

      $rechnungId = $detailQuery->getItemIdentifier();
      $sql = sprintf(
          "SELECT r.id, r.belegnr, r.datum, r.soll FROM `rechnung` AS `r` WHERE r.id = '%s' LIMIT 1",
          $this->app->DB->real_escape_string($rechnungId)
      );
      $rechnung = $this->app->DB->SelectRow($sql);
      if (empty($rechnung)) {
          return;
      }
      $datum = date('d.m.Y', strtotime($rechnung['datum']));
      $detailResult->setTitle(sprintf('Rechnung %s', $rechnung['belegnr']));
      $detailResult->addButton('Rechnung Details', sprintf('index.php?module=rechnung&action=edit&id=%s', $rechnung['id']));
      $detailResult->setMiniDetailUrl(sprintf('index.php?module=rechnung&action=minidetail&id=%s', $rechnung['id']));
  }

  /** @var Application app */

  function RechnungFormeln()
  {
    
  }

  /**
   * @param string $typ
   *
   * @return string
   */
  function Custom($typ)
  {
    return '';
  }
  
  function RechnungSteuer()
  {
    
  }
  
  function RechnungEinkaufspreise()
  {
    
  }
  
  function RechnungSumme()
  {
  }



  function RechnungAlternativPDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $abweichendebezeichnung = $this->app->DB->Select("SELECT abweichendebezeichnung FROM rechnung WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$id' LIMIT 1");
    $this->app->DB->Update("UPDATE rechnung SET abweichendebezeichnung=1 WHERE id='$id' LIMIT 1");
    // Rechnungen
    if(class_exists('RechnungPDFCustom'))
    {
      $Brief = new RechnungPDFCustom($this->app,$projekt);
    }else{
      $Brief = new RechnungPDF($this->app,$projekt);
    }
    $Brief->GetRechnung($id);

    if($abweichendebezeichnung!="1")
      $this->app->DB->Update("UPDATE rechnung SET abweichendebezeichnung=0 WHERE id='$id' LIMIT 1");

    $Brief->displayDocument();
    $this->app->ExitXentral();
  }

  
  function RechnungArchivierePDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id = '$id' LIMIT 1");
    $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
    if(class_exists('RechnungPDFCustom'))
    {
      $Brief = new RechnungPDFCustom($this->app,$projekt);
    }else{
      $Brief = new RechnungPDF($this->app,$projekt);
    }
    $Brief->GetRechnung($id);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument(1);    
    unlink($tmpfile);
    $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
    if(class_exists('RechnungPDFCustom'))
    {
      $Brief = new RechnungPDFCustom($this->app,$projekt);
    }else{
      $Brief = new RechnungPDF($this->app,$projekt);
    }
    $Brief->GetRechnung($id);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument(1);
    
    $this->app->DB->Update("UPDATE rechnung SET schreibschutz='1' WHERE id='$id'");
    $this->app->Location->execute('index.php?module=rechnung&action=edit&id='.$id);
  }

  function RechnungUpdateVerband()
  {
    $id=$this->app->Secure->GetGET('id');
    $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Verbandsinformation wurde neu geladen!</div>  ");
    $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$id&msg=$msg");
  }

  function RechnungMahnwesen()
  {


  }


  function RechnungLastschriftWdh()
  {

    $id=$this->app->Secure->GetGET('id');
    $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='offen',dta_datei=0 WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Rechnung kann nochmal eingezogen werden!</div>  ");
    $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$id&msg=$msg");
  }

  function RechnungDateien()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->RechnungMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Dateien)');
    $this->app->YUI->DateiUpload('PAGE','Rechnung',$id);
  }

  function RechnungDeleteRabatte()
  {

    $id=$this->app->Secure->GetGET('id');
    $this->app->DB->Update("UPDATE rechnung SET rabatt='',rabatt1='',rabatt2='',rabatt3='',rabatt4='',rabatt5='',realrabatt='' WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Rabatte wurden entfernt!</div>  ");
    $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$id&msg=$msg");
  }


  /**
   * @param int $invoiceId
   *
   * @return bool
   */
  public function removeManualPayed($invoiceId)
  {
    if(empty($invoiceId) || !$this->app->DB->Select(sprintf('SELECT id FROM rechnung WHERE id = %d', $invoiceId))) {
      return false;
    }
    $this->app->erp->RechnungProtokoll($invoiceId,'Rechnung manuell als bezahlt entfernt');
    $this->app->DB->Update(
      "UPDATE rechnung 
      SET zahlungsstatus='offen',bezahlt_am = NULL, ist='0',
      mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt entfernt am ".date('d.m.Y')."')
      WHERE id='$invoiceId'"
    );

    return true;
  }

  public function RechnungManuellBezahltEntfernen()
  {
    $id = $this->app->Secure->GetGET('id');

    $this->removeManualPayed($id);

    $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$id");
  }

  public function RechnungUndostorno()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->RechnungProtokoll($id,'Rechnung Stornierung rückgängig gemacht');

    $this->app->DB->Update("UPDATE rechnung SET status='freigegeben',zahlungsstatus='offen',schreibschutz=0,bezahlt_am = NULL, ist='0',mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Rechnung Stornierung rückgängig gemacht ".date('d.m.Y')."') WHERE id='$id'");

    $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$id");
  }

  /**
   * @param int $innoiceId
   *
   * @return bool
   */
  public function setManualPayed($invoiceId)
  {
    if(empty($invoiceId) || !$this->app->DB->Select(sprintf('SELECT id FROM rechnung WHERE id = %d', $invoiceId))) {
      return false;
    }
    $this->app->erp->RechnungProtokoll($invoiceId,'Rechnung manuell als bezahlt markiert');

    $this->app->DB->Update(
      "UPDATE rechnung 
      SET zahlungsstatus='bezahlt',bezahlt_am = now(), ist=soll,mahnwesenfestsetzen='1',
      mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt markiert am ".date('d.m.Y')."') 
      WHERE id='$invoiceId'"
    );

    return true;
  }

  public function RechnungManuellBezahltMarkiert()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->setManualPayed($id);
    $this->app->Location->execute('index.php?module=rechnung&action=edit&id='.$id);
  }

  /**
   * @param int $invoiceId
   */
  public function removeWriteProtection($invoiceId) {
    if($invoiceId <= 0) {
      return;
    }
    $this->app->DB->Update(
      sprintf(
        'UPDATE rechnung SET zuarchivieren=1, schreibschutz = 0 WHERE id=%d',
        $invoiceId
      )
    );

    $this->app->erp->RechnungProtokoll($invoiceId,'Schreibschutz entfernt');
  }

  public function RechnungSchreibschutz()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->removeWriteProtection($id);
    $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$id");
  }


  function RechnungCopy()
  {
    $id = $this->app->Secure->GetGET('id');

    $newid = $this->CopyRechnung($id);

    $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$newid");
  }

  /**
   * @param int $invoiceId
   */
  public function addZertificates($invoiceId)
  {
    if($invoiceId <= 0) {
      return;
    }
    $addressId = $this->app->DB->Select(
      sprintf(
        'SELECT adresse FROM rechnung WHERE id = %d',
        $invoiceId
      )
    );
    if($addressId <= 0) {
      return;
    }
    $zertificates = $this->app->DB->SelectArr(
        sprintf(
          "SELECT ds.datei 
          FROM datei_stichwoerter ds 
          INNER JOIN datei_stichwoerter ds2 ON ds.datei = ds2.datei AND ds2.objekt = 'Artikel'
          INNER JOIN rechnung_position ap ON ap.artikel = ds2.parameter AND ap.rechnung = %d
          WHERE ds.objekt = 'Adressen' AND ds.parameter = %d
          GROUP BY ds.datei",
        $invoiceId, $addressId
      )
    );
    if(empty($zertificates)) {
      return;
    }
    foreach($zertificates as $zertificate) {
      $this->app->erp->AddDateiStichwort($zertificate['datei'],'Sonstige','Rechnung',$invoiceId);
    }
  }

  function RechnungZertifikate()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->addZertificates($id);
    $this->app->Location->execute("index.php?module=rechnung&action=dateien&id=$id");
  }

  public function RechnungIconMenu($id, $prefix = '')
  {
    if($id > 0){
      $rechnungarr = $this->app->DB->SelectRow(
        "SELECT status,zahlungsstatus FROM rechnung WHERE id='$id' LIMIT 1"
      );
    }
    $status = '';
    $zahlungsstatus = '';
    if(!empty($rechnungarr)){
      $status = $rechnungarr['status'];//$this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");
      $zahlungsstatus = $rechnungarr['zahlungsstatus'];//$this->app->DB->Select("SELECT zahlungsstatus FROM rechnung WHERE id='$id' LIMIT 1");
    }
    $freigabe ="";
    $storno="";
    $bezahlt="";
    $weiterfuehren="";
    $optionteilstorno = "";

    $checkifgsexists = $this->app->DB->Select("SELECT id FROM gutschrift WHERE rechnungid='$id' LIMIT 1");

    if($status==="angelegt" || $status=="")
    {
      $freigabe = "<option value=\"freigabe\">Rechnung freigeben</option>";
      $storno = "<option value=\"storno\">Rechnung (ENTWURF) löschen</option>";
    } else {
      $weiterfuehren = "<option value=\"gutschrift\">als Gutschrift / ".$this->app->erp->Firmendaten("bezeichnungstornorechnung")."</option>";
    }
    $casehook = '';
    $optionhook = '';
    $this->app->erp->RunHook('rechnungiconmenu_option', 5, $id, $casehook, $optionhook, $status, $prefix);
    
    if($this->app->erp->RechteVorhanden("rechnung","undostorno") && !$checkifgsexists)
      $undostorno = "<option value=\"undostorno\">Rechnung Storno rückgängig</option>";

    if($this->app->erp->RechteVorhanden("rechnung","manuellbezahltmarkiert") && $zahlungsstatus=="offen")
      $bezahlt = "<option value=\"manuellbezahltmarkiert\">manuell als bezahlt markieren</option>";


    if($this->app->erp->RechteVorhanden("rechnung","manuellbezahltentfernen") && $zahlungsstatus=="bezahlt")
      $bezahlt = "<option value=\"manuellbezahltentfernen\">manuell bezahlt entfernen</option>";

    $zertifikatoption = '';
    $zertifikatcase = '';
    
    $optioncustom = $this->Custom('option');
    $casecustom = $this->Custom('case');

    $hookoption = '';
    $hookcase = '';
    $this->app->erp->RunHook('Rechnung_Aktion_option',3, $id, $status, $hookoption);
    $this->app->erp->RunHook('Rechnung_Aktion_case',3, $id, $status, $hookcase);


    //TODO das muss dann später in den Hook
    $RechnungzuVerbindlichkeitOption = "<option value=\"rechnungzuverbindlichkeit\">Rechnung zu Verbindlichkeit</option>";
    $RechnungzuVerbindlichkeitCase = "case 'rechnungzuverbindlichkeit': if(!confirm('Wirklich Verbindlichkeit anlegen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=rechnungzuverbindlichkeit&action=create&id=%value%'; break;";


    if($this->app->erp->RechteVorhanden('zertifikatgenerator','list'))
    {
      $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id = '$id' LIMIT 1");
      if($adresse)
      {
        $zertifikate = $this->app->DB->Select("SELECT ds.datei 
      FROM datei_stichwoerter ds 
      INNER JOIN datei_stichwoerter ds2 ON ds.datei = ds2.datei AND ds2.objekt = 'Artikel'
      INNER JOIN rechnung_position ap ON ap.artikel = ds2.parameter AND ap.rechnung = '$id'
      WHERE ds.objekt = 'Adressen' AND ds.parameter = '$adresse'
      GROUP BY ds.datei LIMIT 1");
        if($zertifikate)
        {
          $zertifikatoption = '<option value="zertifikate">Zertifikate anh&auml;ngen</option>';
          $zertifikatcase = "case 'zertifikate': if(!confirm('Zertifikate wirklich laden?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=rechnung&action=zertifikate&id=%value%'; break; ";
        }
      }
      
    }

    if($this->app->erp->RechteVorhanden('belegeimport', 'belegcsvexport'))
    { 
      $casebelegeimport = "case 'belegeimport':  window.location.href='index.php?module=belegeimport&action=belegcsvexport&cmd=rechnung&id=%value%'; break;";
      $optionbelegeimport = "<option value=\"belegeimport\">Export als CSV</option>";
    }


    
    if($checkifgsexists>0) $extendtext = "HINWEIS: Es existiert bereits eine Gutschrift zu dieser Rechnung! "; else $extendtext="";
    $menu ="
      <script type=\"text/javascript\">
      function onchangerechnung(cmd)
      {
        switch(cmd)
        {
          case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=rechnung&action=delete&id=%value%'; break;
          case 'undostorno': if(!confirm('Wirklich die Stornierung rückgängig machen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=rechnung&action=undostorno&id=%value%'; break;
          case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=rechnung&action=copy&id=%value%'; break;
          case 'gutschrift': if(!confirm('".$extendtext."Wirklich als Gutschrift / ".$this->app->erp->Firmendaten("bezeichnungstornorechnung")." weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%'; break;
          $optionteilstorno
          $RechnungzuVerbindlichkeitCase
          case 'pdf': window.location.href='index.php?module=rechnung&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
          case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;
          case 'manuellbezahltmarkiert': window.location.href='index.php?module=rechnung&action=manuellbezahltmarkiert&id=%value%'; break;
          case 'manuellbezahltentfernen': window.location.href='index.php?module=rechnung&action=manuellbezahltentfernen&id=%value%'; break;
          case 'freigabe': window.location.href='index.php?module=rechnung&action=freigabe&id=%value%'; break;
          $zertifikatcase
          $casebelegeimport
          $casecustom
          $hookcase 
          $casehook
        }

      }
    </script>


      &nbsp;Aktion:&nbsp;<select id=\"aktion$prefix\" onchange=\"onchangerechnung(this.value)\"> 
      <option>bitte w&auml;hlen ...</option>
      <option value=\"copy\">Rechnung kopieren</option>
      $freigabe
      <option value=\"abschicken\">Rechnung abschicken</option>
      $RechnungzuVerbindlichkeitOption
      $storno
      $weiterfuehren
      $undostorno
      $optionbelegeimport
      <option value=\"pdf\">PDF &ouml;ffnen</option>
      $bezahlt
      $zertifikatoption
      $optioncustom
      $optionhook
      $hookoption
      </select>&nbsp;
      ";
      
   $menu .=   "

    <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\" title=\"PDF\"></a>
      <!--  <a href=\"index.php?module=rechnung&action=edit&id=%value%\" title=\"Bearbeiten\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
      <a onclick=\"if(!confirm('Wirklich stornieren?')) return false; else window.location.href='index.php?module=rechnung&action=delete&id=%value%';\" title=\"Stornieren\">
      <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\" title=\"Kopieren\">
      <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich als Gutschrift weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\" title=\"als Gutschrift weiterf&uuml;hren\">
      <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift\"></a>-->";

    //$tracking = $this->AuftragTrackingTabelle($id);

    $menu = str_replace('%value%',$id,$menu);
    return $menu;
  }


  function RechnungLiveTabelle()
  {
    $id = $this->app->Secure->GetGET('id');

    $table = new EasyTable($this->app);

    $table->Query(
      "SELECT ap.bezeichnung as artikel, ap.nummer as Nummer, ap.menge as Menge
      FROM rechnung_position ap, artikel a 
      WHERE ap.rechnung='$id' AND a.id=ap.artikel"
    );
    $artikel = $table->DisplayNew('return','Menge','noAction');
    echo $artikel;
    $this->app->ExitXentral();
  }

  public function RechnungEditable()
  {
    $this->app->YUI->AARLGEditable();
  }

  public function RechnungPDFfromArchiv()
  {
    $id = $this->app->Secure->GetGET('id');
    $archiv = $this->app->DB->Select("SELECT table_id from pdfarchiv where id = '$id' LIMIT 1");
    if($archiv) {
      $projekt = $this->app->DB->Select("SELECT projekt from rechnung where id = '".(int)$archiv."'");
    }
    if(class_exists('RechnungPDFCustom')) {
      if($archiv) {
        $Brief = new RechnungPDFCustom($this->app,$projekt);
      }
    }
    else{
      if($archiv) {
        $Brief = new RechnungPDF($this->app,$projekt);
      }
    }
    if($archiv && $content = $Brief->getArchivByID($id)) {
      header('Content-type: application/pdf');
      header('Content-Disposition: attachment; filename="'.$content['belegnr'].'.pdf"');
      echo $content['file'];
      $this->app->ExitXentral();
    }
    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="Fehler.pdf"');
    $this->app->ExitXentral();
  }

  public function RechnungMiniDetail($parsetarget='',$menu=true)
  {
    $id = $this->app->Secure->GetGET('id');
    
    if(!$this->app->DB->Select("SELECT deckungsbeitragcalc FROM rechnung WHERE  id='$id' LIMIT 1")) {
      $this->app->erp->BerechneDeckungsbeitrag($id,'rechnung');
    }
    
    $auftragArr = $this->app->DB->SelectArr("SELECT * FROM rechnung WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='{$auftragArr[0]['projekt']}' LIMIT 1");
    $kundenname = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");

    $this->app->Tpl->Set('DECKUNGSBEITRAG',0);
    $this->app->Tpl->Set('DBPROZENT',0);    
    $this->app->Tpl->Set('KUNDE',"<a href=\"index.php?module=adresse&action=edit&id=".$auftragArr[0]['adresse']."\">".$kundennummer."</a> ".$kundenname);

    if($this->app->erp->RechteVorhanden('projekt','dashboard')){
      $this->app->Tpl->Set('PROJEKT', "<a href=\"index.php?module=projekt&action=dashboard&id=" . $auftragArr[0]['projekt'] . "\" target=\"_blank\">$projekt</a>");
    }
    else{
      $this->app->Tpl->Set('PROJEKT', $projekt);
    }

    $this->app->Tpl->Set('ZAHLWEISE',$auftragArr[0]['zahlungsweise']);
    $this->app->Tpl->Set('STATUS',($auftragArr[0]['status'] === 'storniert' && $auftragArr[0]['teilstorno'] == 1?'teilstorniert':$auftragArr[0]['status']));
    $this->app->Tpl->Set('IHREBESTELLNUMMER',$auftragArr[0]['ihrebestellnummer']);

    $this->app->Tpl->Set('DEBITORENNUMMER', $auftragArr[0]['kundennummer_buchhaltung']);

    if($auftragArr[0]['mahnwesen']=='') {
      $auftragArr[0]['mahnwesen']='-';
    }
    $this->app->Tpl->Set('MAHNWESEN',$auftragArr[0]['mahnwesen']);
    if($auftragArr[0]['mahnwesen_datum']=='0000-00-00') {
      $auftragArr[0]['mahnwesen_datum']='-';
    }


    if(!empty($auftragArr[0]['kundennummer_buchhaltung'])) {
      $this->app->Tpl->Set('DEBITORENNUMMER', $auftragArr[0]['kundennummer_buchhaltung']);
    }

    $internet = $this->app->DB->Select("SELECT a.internet FROM rechnung r LEFT JOIN auftrag a ON a.id=r.auftragid WHERE r.id='$id' AND r.id > 0 LIMIT 1");
    $this->app->Tpl->Set('INTERNET',$internet);
    

    $this->app->Tpl->Set('MAHNWESENDATUM',$this->app->String->Convert($auftragArr[0]['mahnwesen_datum'],"%1-%2-%3","%3.%2.%1"));

    $ab_datum = $this->app->String->Convert($auftragArr[0]['datum'],"%1-%2-%3","%3.%2.%1");


    if($auftragArr[0]['auftragid']==0) $auftragArr[0]['auftrag']="kein Auftrag";
    $auftragArr[0]['auftrag'] = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='".$auftragArr[0]['auftragid']."' LIMIT 1");

    if($auftragArr[0]['auftragid'] > 0)
    {
      $this->app->Tpl->Set('AUFTRAG',"<a href=\"index.php?module=auftrag&action=edit&id=".$auftragArr[0]['auftragid']."\" target=\"_blank\" title=\"$ab_datum\">".$auftragArr[0]['auftrag']."</a>&nbsp;
          <a href=\"index.php?module=auftrag&action=pdf&id=".$auftragArr[0]['auftragid']."\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Auftrag PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=auftrag&action=edit&id=".$auftragArr[0]['auftragid']."\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Auftrag bearbeiten\" border=\"0\"></a>&nbsp;");
    }else{
      $this->app->Tpl->Set('AUFTRAG', '-');
    }
    $auftraege = $this->app->DB->SelectArr("(SELECT a.belegnr, a.id FROM sammelrechnung_position s 
    INNER JOIN auftrag_position ap on ap.id = s.auftrag_position_id INNER JOIN auftrag a on a.id = ap.auftrag 
    WHERE s.rechnung = '".$id."' GROUP BY a.id ORDER BY a.belegnr)
    union (SELECT 
    a.belegnr, a.id FROM sammelrechnung_position s INNER JOIN lieferschein_position lp ON lp.id = s.lieferschein_position_id
    INNER JOIN auftrag_position ap  on ap.id = lp.auftrag_position_id INNER JOIN 
    auftrag a on a.id = ap.auftrag 
    WHERE s.rechnung = '".$id."' GROUP BY a.id ORDER BY a.belegnr)
    ");
    if($auftraege)
    {
      $this->app->Tpl->Set('AUFTRAG','');
      $first = true;
      foreach($auftraege as $k => $v)
      {
        if(!$first)$this->app->Tpl->Add('AUFTRAG','<br />');
        if($v['id'] > 0)
        {
          if(empty($v['belegnr'])) {
            $v['belegnr'] = 'ENTWURF';
          }
          $this->app->Tpl->Add('AUFTRAG',"<a href=\"index.php?module=auftrag&action=edit&id=".$v['id']."\" target=\"_blank\">".$v['belegnr']."</a>&nbsp;
          <a href=\"index.php?module=auftrag&action=pdf&id=".$v['id']."\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Auftrag PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=auftrag&action=edit&id=".$v['id']."\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Auftrag bearbeiten\" border=\"0\"></a>&nbsp;");
        }
        $first = false;
      }
    }


    $gutschrift = $this->app->DB->SelectArr("SELECT
        CONCAT('<a href=\"index.php?module=gutschrift&action=edit&id=',g.id,'\" target=\"_blank\">',if(g.belegnr='0' OR g.belegnr='','ENTWURF',g.belegnr),'&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=',g.id,'\"><img src=\"./themes/new/images/pdf.svg\" title=\"Gutschrift PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=gutschrift&action=edit&id=',g.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Gutschrift bearbeiten\" border=\"0\"></a>') as gutschrift
        FROM gutschrift g WHERE g.rechnungid='$id'");

    if(!empty($gutschrift))
    {
      $cgutschrift = !empty(count($gutschrift))?:0;
      for($li=0;$li<$cgutschrift;$li++)
      {
        $this->app->Tpl->Add('GUTSCHRIFT',$gutschrift[$li]['gutschrift']);
        if($li<count($gutschrift)){
          $this->app->Tpl->Add('GUTSCHRIFT', "<br>");
        }
      }
    }
    else{
      $this->app->Tpl->Set('GUTSCHRIFT', "-");
    }

    $returnOrders = (array)$this->app->DB->SelectArr(
      sprintf(
        'SELECT ro.id, ro.belegnr, ro.status
        FROM `rechnung` AS `i`
        INNER JOIN `auftrag` AS `o` ON i.auftragid = o.id OR i.id = o.rechnungid
        LEFT JOIN `lieferschein` AS `dn` ON o.id = dn.auftragid
        INNER JOIN `retoure` AS `ro` ON o.id = ro.auftragid OR dn.id = ro.lieferscheinid
        WHERE i.id = %d
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

    $sammelrechnung = false;
    if($this->app->DB->Select("SELECT id FROM sammelrechnung_position LIMIT 1"))$sammelrechnung = true;
    $gruppenrechnung = false;
    if($this->app->DB->Select("SELECT id FROM gruppenrechnung_position LIMIT 1"))$gruppenrechnung = true;
    $rechnungid = false;
    $this->app->DB->Select("SELECT rechnungid FROM lieferschein LIMIT 1");
    if(!$this->app->DB->error())$rechnungid =true;
    
    $lieferscheinsql = "
    SELECT CONCAT('<a href=\"index.php?module=lieferschein&action=edit&id=',l.id,'\" target=\"_blank\">',if(l.status!='angelegt',l.belegnr,'ENTWURF'),'</a>&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=',l.id,'\">
      <img src=\"./themes/new/images/pdf.svg\" title=\"Lieferschein PDF\" border=\"0\"></a>&nbsp;
    <a href=\"index.php?module=lieferschein&action=edit&id=',l.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Lieferschein bearbeiten\" border=\"0\"></a>') as LS
    FROM lieferschein l
    INNER JOIN (
      (SELECT id FROM lieferschein WHERE id = '{$auftragArr[0]['lieferschein']}')
      ";
    if($rechnungid)
    {
      $lieferscheinsql .= "
        UNION ALL 
        (SELECT id FROM lieferschein WHERE rechnungid = '$id')";
    }
    if($sammelrechnung)
    {
      $lieferscheinsql .=   "UNION ALL 
        (SELECT l2.id FROM lieferschein l2 INNER JOIN lieferschein_position lp2 ON lp2.lieferschein = l2.id
      INNER JOIN sammelrechnung_position s ON lp2.id = s.lieferschein_position_id WHERE s.rechnung = '$id' )
      UNION ALL
      
        (SELECT l3.id   FROM lieferschein l3 INNER JOIN lieferschein_position lp3 ON lp3.lieferschein = l3.id
      INNER JOIN auftrag_position ap3 ON ap3.id = lp3.auftrag_position_id
      INNER JOIN sammelrechnung_position s3 ON ap3.id = s3.auftrag_position_id WHERE s3.rechnung = '$id'  )
      ";
    }
    if($gruppenrechnung)
    {
      $lieferscheinsql .= "
        UNION ALL 
        (SELECT l4.id FROM lieferschein l4 INNER JOIN lieferschein_position lp4 ON lp4.lieferschein = l4.id
      INNER JOIN gruppenrechnung_position s4 ON lp4.id = s4.lieferschein_position_id WHERE s4.rechnung = '$id' )
      UNION ALL
      
        (SELECT l5.id   FROM lieferschein l5 INNER JOIN lieferschein_position lp5 ON lp5.lieferschein = l5.id
      INNER JOIN auftrag_position ap5 ON ap5.id = lp5.auftrag_position_id
      INNER JOIN gruppenrechnung_position s5 ON ap5.id = s5.auftrag_position_id WHERE s5.rechnung = '$id'  )

      ";
    }
    $lieferscheinsql .= "
    ) ls ON l.id = ls.id
    LEFT JOIN projekt p ON l.projekt = p.id
    WHERE 1 ".$this->app->erp->ProjektRechte('p.id'). " GROUP BY l.id ";
    
    $lieferschein = $this->app->DB->SelectArr($lieferscheinsql);
    
    if($lieferschein=="") $this->app->Tpl->Set('LIEFERSCHEIN','-');
    else{
      $first = true;
      $this->app->Tpl->Set('LIEFERSCHEIN','');
      foreach($lieferschein as $ls)
      {
        if(!$first)$this->app->Tpl->Add('LIEFERSCHEIN','<br>');
        $this->app->Tpl->Add('LIEFERSCHEIN',$ls['LS']);
        $first = false;
      }
      
    }
    


    if($auftragArr[0]['ust_befreit']==0)
      $this->app->Tpl->Set('STEUER',"Inland");
    else if($auftragArr[0]['ust_befreit']==1)
      $this->app->Tpl->Set('STEUER',"EU-Lieferung");
    else
      $this->app->Tpl->Set('STEUER',"Export");
    $this->app->Tpl->Set('DELIVERYTHRESHOLDVATID',!empty($auftragArr[0]['deliverythresholdvatid'])?$auftragArr[0]['deliverythresholdvatid']:'');

    if($menu)
    {
      $menu = $this->RechnungIconMenu($id);
      $this->app->Tpl->Set('MENU',$menu);
    }
    // ARTIKEL

    $status = $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    $table->Query("SELECT if(CHAR_LENGTH(ap.beschreibung) > 0,CONCAT(ap.bezeichnung,' *'),ap.bezeichnung) as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', ap.nummer,'</a>') as Nummer, ".$this->app->erp->FormatMenge("ap.menge")." as Menge,".$this->app->erp->FormatPreis("ap.preis*(100-ap.rabatt)/100",2)." as Preis
          FROM rechnung_position ap, artikel a WHERE ap.rechnung='$id' AND a.id=ap.artikel ORDER by ap.sort");

    $table->align = array('left','left','right','right');
    $artikel = $table->DisplayNew("return","Preis","noAction","false",0,0,false);

    $this->app->Tpl->Set('ARTIKEL','<div id="artikeltabellelive'.$id.'">'.$artikel.'</div>');

    if($auftragArr[0]['belegnr'] =="0" || $auftragArr[0]['belegnr']=="") $auftragArr[0]['belegnr'] = "ENTWURF";
    $this->app->Tpl->Set('BELEGNR',"<a href=\"index.php?module=rechnung&action=edit&id=".$auftragArr[0]['id']."\">".$auftragArr[0]['belegnr']."</a>");
    $this->app->Tpl->Set('RECHNUNGID',$auftragArr[0]['id']);


    if($auftragArr[0]['status']=="freigegeben")
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"orange");
      $this->app->Tpl->Set('ANGEBOTTEXT',"Das Angebot wurde noch nicht als Auftrag weitergef&uuml;hrt!");
    }
    else if($auftragArr[0]['status']=="versendet")
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"red");
      $this->app->Tpl->Set('ANGEBOTTEXT',"Das Angebot versendet aber noch kein Auftrag vom Kunden erhalten!");
    }
    else if($auftragArr[0]['status']=="beauftragt")
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"green");
      $this->app->Tpl->Set('ANGEBOTTEXT',"Das Angebot wurde beauftragt und abgeschlossen!");
    }
    else if($auftragArr[0]['status']=="angelegt")
    {
      $this->app->Tpl->Set('ANGEBOTFARBE',"grey");
      $this->app->Tpl->Set('ANGEBOTTEXT',"Das Angebot wird bearbeitet und wurde noch nicht freigegeben und abgesendet!");
    }

    
    $this->app->Tpl->Set('ZAHLUNGEN',"<table width=100% border=0 class=auftrag_cell cellpadding=0 cellspacing=0>Erst ab Version Enterprise verf&uuml;gbar</table>");
    if(count($gutschrift) > 0)
      $this->app->Tpl->Add('ZAHLUNGEN',"<div class=\"info\">Zu dieser Rechnung existiert eine Gutschrift!</div>");
    else {

      if($auftragArr[0]['zahlungsstatus']!="bezahlt")
        $this->app->Tpl->Add('ZAHLUNGEN',"<div class=\"warning\">Diese Rechnung ist noch nicht komplett bezahlt!</div>");
      else
      {
        if(!empty($auftragArr[0]['bezahlt_am']) && $auftragArr[0]['bezahlt_am'] != '0000-00-00')
        {
          $this->app->Tpl->Add('ZAHLUNGEN',"<div class=\"success\">Diese Rechnung wurde am ".$this->app->String->Convert($auftragArr[0]['bezahlt_am'],"%1-%2-%3","%3.%2.%1")." bezahlt.</div>");
        }else{
          $this->app->Tpl->Add('ZAHLUNGEN',"<div class=\"success\">Diese Rechnung ist bezahlt.</div>");
        }
      }
    }

    $this->app->Tpl->Set('RECHNUNGADRESSE',$this->Rechnungsadresse($auftragArr[0]['id']));

    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM rechnung_protokoll WHERE rechnung='$id' ORDER by zeit DESC");
    $tmp->DisplayNew('PROTOKOLL',"Protokoll","noAction");


    $query = $this->app->DB->SelectArr("SELECT zeit,bearbeiter,grund FROM rechnung_protokoll WHERE rechnung='$id' ORDER by zeit");
    if($query)
    {
      $zeit = 0;
      foreach($query as $k => $row)
      {
        if(strpos($row['grund'], 'Zahlungserinnerung') === 0 || strpos($row['grund'], 'Mahnung') === 0 )
        {
          if(!$zeit)$zeit = $row['zeit'];
        }
      }
      if($zeit)
      {
        
        $tmp2 = new EasyTable($this->app);
        $tmp2->Query("SELECT concat('<a href=\"index.php?module=mahnwesen&action=mahnpdf&id=',rechnung,'&datum=',DATE_FORMAT(zeit,'%d.%m.%Y'),'&mahnwesen=',LOWER(LEFT(grund,LOCATE(' ',grund))),'\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.svg\" border=\"0\"></a>') as PDF, Date(zeit) as Datum, bearbeiter,grund FROM rechnung_protokoll WHERE rechnung='$id' AND zeit >= '".$zeit."' ORDER by zeit DESC");
        $tmp2->DisplayNew('MAHNPROTOKOLL',"Protokoll","noAction");        
      }
      

    }

    if(class_exists('RechnungPDFCustom'))
    {
      $Brief = new RechnungPDFCustom($this->app,$auftragArr[0]['projekt']);
    }else{
      $Brief = new RechnungPDF($this->app,$auftragArr[0]['projekt']);
    }
    $Dokumentenliste = $Brief->getArchivedFiles($id, 'rechnung');
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
          $tmpr['menu'] = '<a href="index.php?module=rechnung&action=pdffromarchive&id='.$v['id'].'"><img src="themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/pdf.svg" /></a>';
          $tmp3->datasets[] = $tmpr;
        }
      }
      
      $tmp3->DisplayNew('PDFARCHIV','Men&uuml;',"noAction");
    }


    if($parsetarget=='')
    {
      $this->app->Tpl->Output('rechnung_minidetail.tpl');
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Parse($parsetarget,'rechnung_minidetail.tpl');
  }

  /**
   * @param int $id
   *
   * @return string
   */
  public function Rechnungsadresse($id)
  {
    $data = $this->app->DB->SelectArr(
      "SELECT r.*, a.abweichende_rechnungsadresse 
      FROM rechnung AS r 
      INNER JOIN adresse AS a ON r.adresse = a.id 
      WHERE r.id='$id' 
      LIMIT 1"
    );

    foreach($data[0] as $key=>$value)
    {
      if($data[0][$key]!='' && $key!=='abweichendelieferadresse' && $key!=='land' && $key!=='plz'
        && $key!=='lieferland' && $key!=='lieferplz') {
        $data[0][$key] = $data[0][$key].'<br>';
      }
    }

    $rechnungsadresse = $data[0]['name']."".$data[0]['ansprechpartner']."".$data[0]['abteilung']."".$data[0]['unterabteilung'].
      "".$data[0]['strasse']."".$data[0]['adresszusatz']."".$data[0]['land']."-".$data[0]['plz']." ".$data[0]['ort'];

    $isAbweichend = false;
    if($data[0]['abweichende_rechnungsadresse']==1){
      $isAbweichend=true;
    }
    $abweichendString = ($isAbweichend?' (abweichend)':'');

    return "<table width=\"100%\">
      <tr valign=\"top\"><td width=\"50%\"><b>Rechnungsadresse".$abweichendString.":</b><br><br>$rechnungsadresse</td></tr></table>";
  }


  function RechnungLastschrift()
  {
    $this->app->Tpl->Set('UEBERSCHRIFT','Lastschrift&nbsp;/&nbsp;Sammel&uuml;berweisung');
    $erzeugen = $this->app->Secure->GetPOST('erzeugen');
    $lastschrift= $this->app->Secure->GetPOST('lastschrift');
    $kontointern=$this->app->Secure->GetPOST('konto');
    $this->app->erp->Headlines('Lastschriften');

    $this->app->erp->MenuEintrag('index.php?module=rechnung&action=list','Zur Rechnungs&uuml;bersicht');

    if($erzeugen!='')
    {
      //erzeugen
      $rechnung= $this->app->Secure->GetPOST('rechnung');
      $crechnung = !empty($rechnung)?count($rechnung):0;
      for($i=0;$i<$crechnung;$i++)
      {

        //rechnung auf bezahlt markieren + soll auf ist
        $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='abgebucht' WHERE id='{$rechnung[$i]}' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      }
    }



    // offene Rechnungen
    $this->app->Tpl->Set('SUB1TABTEXT',"Offene Rechnungen");
    $table = new EasyTable($this->app);
    $table->Query("SELECT CONCAT('<input type=checkbox name=rechnung[] value=\"',r.id,'\" checked>') as auswahl, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, if(r.belegnr!='',r.belegnr,'ohne Nummer') as beleg, r.name, p.abkuerzung as projekt, r.soll as betrag, r.ist as ist, r.zahlungsweise, a.bank_inhaber, a.bank_institut, a.bank_blz, a.bank_konto, r.id
        FROM rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN zahlungsweisen z ON z.type=r.zahlungsweise LEFT JOIN auftrag a ON a.id=r.auftragid WHERE (r.zahlungsstatus!='bezahlt' AND r.zahlungsstatus!='abgebucht') AND (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung' OR z.verhalten='lastschrift') AND (r.belegnr!='') order by r.datum DESC, r.id DESC");
    $table->DisplayNew('SUB1TAB',"
        <!--<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>-->
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
        ");


    $summe = $this->app->DB->Select("SELECT SUM(r.soll)
        FROM rechnung r LEFT JOIN projekt p ON p.id=r.projekt  WHERE (r.zahlungsstatus!='bezahlt' AND r.zahlungsstatus!='abgebucht')  AND (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzug') AND r.belegnr!='' ");

    if($summe <=0) {
      $summe = '0,00';
    }
    $this->app->Tpl->Set('TAB1',"<center>Gesamt offen: $summe EUR</center>");


    $this->app->YUI->TableSearch('TAB1',"lastschriften");
    $this->app->Tpl->Add('TAB1',"<br><center>
        <input type=\"submit\" name=\"submit\" value=\"Lastschriften an Zahlungstransfer &uuml;bergeben\"></center></form>");

    $this->app->YUI->TableSearch('TAB2','lastschriftenarchiv');

    $this->app->Tpl->Parse('PAGE','rechnung_lastschrift.tpl');
  }


  public function RechnungGutschrift()
  {
    $id = $this->app->Secure->GetGET('id');

    $status = $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");
    if($status==='angelegt')
    {
      $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Die Rechnung ist noch nicht freigegeben und kann daher nicht storniert werden!</div>");
      $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$id&msg=$msg");
    }
    $this->app->erp->RechnungProtokoll($id,'Rechnung als Gutschrift weitergeführt');
    $newid = $this->app->erp->WeiterfuehrenRechnungZuGutschrift($id);

    // pruefe obes schon eine gutschrift fuer diese rechnung gibt
    $anzahlgutschriften = $this->app->DB->Select("SELECT COUNT(id) FROM gutschrift WHERE rechnungid='$id' 
        AND rechnungid!=0 AND rechnungid!=''");

    if($anzahlgutschriften>1){
      $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Achtung es gibt bereits eine oder mehrer Gutschriften f&uuml;r diese Rechnung!</div>");
    }

    $this->app->Location->execute("index.php?module=gutschrift&action=edit&id=$newid&msg=$msg");
  }


  function RechnungFreigabe($id='')
  {
    if($id=='')
    {
      $id = $this->app->Secure->GetGET('id');
      $freigabe= $this->app->Secure->GetGET('freigabe');
      $this->app->Tpl->Set('TABTEXT','Freigabe');
      $this->app->erp->RechnungNeuberechnen($id);
    } else {
      $intern = true;
      $freigabe=$intern;
    }
    $allowedFrm = true;
    $showDefault = true;
    $this->app->erp->CheckVertrieb($id,'rechnung');
    $this->app->erp->CheckBearbeiter($id,'rechnung');
    $doctype = 'rechnung';
    if(empty($intern)){
      $this->app->erp->RunHook('beleg_freigabe', 4, $doctype, $id, $allowedFrm, $showDefault);
    }
    if($allowedFrm && $freigabe==$id)
    {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");

      if($belegnr=='')
      {	
        $this->app->erp->BelegFreigabe('rechnung',$id);
        if($intern) {
          return 1;
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Rechnung wurde freigegeben und kann jetzt versendet werden!</div>");
        $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$id&msg=$msg");
      }
      if($intern) {
        return 0;
      }
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Rechnung wurde bereits freigegeben!</div>");
      $this->app->Location->execute("index.php?module=rechnung&action=edit&id=$id&msg=$msg");
    }

    if($showDefault){
      $name = $this->app->DB->Select("SELECT a.name FROM rechnung b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      $summe = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id' LIMIT 1");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM rechnung_position
        WHERE rechnung='$id' LIMIT 1");

      $this->app->Tpl->Set('TAB1', "<div class=\"info\">Soll die Rechnung an <b>$name</b> im Wert von <b>$summe $waehrung</b> 
        jetzt freigegeben werden? <input type=\"button\" class=\"btnImportantLarge\" value=\"Jetzt freigeben\" onclick=\"window.location.href='index.php?module=rechnung&action=freigabe&id=$id&freigabe=$id'\">
        </div>");
    }

    $this->RechnungMenu();
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }



  function RechnungAbschicken()
  {
    $this->RechnungMenu();
    $this->app->erp->DokumentAbschicken();
  }

  public function RechnungDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    $invoiceArr = $this->app->DB->SelectRow("SELECT belegnr, name FROM rechnung WHERE id='$id' LIMIT 1");
    $belegnr = $invoiceArr['belegnr'];
    $name = $invoiceArr['name'];
    $msg = '';
    if($belegnr=='0' || $belegnr=='') {
      $this->app->erp->DeleteRechnung($id);
      $belegnr='ENTWURF';
      $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Die Rechnung \"$belegnr\" von \"$name\" wurde gelöscht!</div>");
      $this->app->Location->execute('index.php?module=rechnung&action=list&msg='.$msg);
    }

    $this->RechnungGutschrift();
    $this->app->Location->execute('index.php?module=rechnung&action=list&msg='.$msg);
  }

  function RechnungProtokoll()
  {
    $this->RechnungMenu();
    $id = $this->app->Secure->GetGET('id');

    $this->app->Tpl->Set('TABTEXT','Protokoll');
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM rechnung_protokoll WHERE rechnung='$id' ORDER by zeit DESC");
    $tmp->DisplayNew('TAB1','Protokoll','noAction');

    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  function RechnungMahnPDF()
  {
    $id = $this->app->Secure->GetGET('id');
    $invoiceArr = $this->app->DB->SelectRow("SELECT belegnr, mahnwesen, projekt FROM rechnung WHERE id='$id' LIMIT 1");
    $belegnr = $invoiceArr['belegnr'];
    $mahnwesen = $invoiceArr['mahnwesen'];
    $projekt = $invoiceArr['projekt'];
    

    if($belegnr!='' && $belegnr!='0')
    {
      $Brief = new MahnungPDF($this->app,$projekt);
      $Brief->GetRechnung($id,$mahnwesen);
      $Brief->displayDocument(); 
    }
    $this->app->ExitXentral();
  }

  function RechnungInlinePDF()
  {
    $id = $this->app->Secure->GetGET('id');
    $invoiceArr = $this->app->DB->SelectRow("SELECT schreibschutz, projekt FROM rechnung WHERE id='$id' LIMIT 1");
    $schreibschutz = $invoiceArr['schreibschutz'];
    if($schreibschutz!='1'){
      $this->app->erp->RechnungNeuberechnen($id);
    }

    $frame = $this->app->Secure->GetGET('frame');
    $projekt = $invoiceArr['projekt'];

    if($frame=='')
    {
      if(class_exists('RechnungPDFCustom'))
      {
        $Brief = new RechnungPDFCustom($this->app,$projekt);
      }else{
        $Brief = new RechnungPDF($this->app,$projekt);
      }
      $Brief->GetRechnung($id);
      $Brief->inlineDocument($schreibschutz); 
    } else {
      $file = urlencode("../../../../index.php?module=rechnung&action=inlinepdf&id=$id");
      echo "<iframe width=\"100%\" height=\"100%\" style=\"height:calc(100vh - 110px)\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
      $this->app->ExitXentral();
    }
  }

  function RechnungPDF()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->RechnungNeuberechnen($id);
    $doppel = $this->app->Secure->GetGET('doppel');
    $invoiceArr = $this->app->DB->SelectRow("SELECT schreibschutz, projekt, zuarchivieren FROM rechnung WHERE id='$id' LIMIT 1");
    if(!empty($invoiceArr['schreibschutz']) && !empty($invoiceArr['zuarchivieren'])) {
      $this->app->erp->PDFArchivieren('rechnung', $id, true);
    }
    $projekt = $invoiceArr['projekt'];
    $schreibschutz = $invoiceArr['schreibschutz'];
    //    if(is_numeric($belegnr) && $belegnr!=0)
    //  {
    if(class_exists('RechnungPDFCustom'))
    {
      $Brief = new RechnungPDFCustom($this->app,$projekt);
    }else{
      $Brief = new RechnungPDF($this->app,$projekt);
    }
    if($doppel=='1'){
      $Brief->GetRechnung($id, 'doppel');
    }
    else{
      $Brief->GetRechnung($id);
    }
    $Brief->displayDocument($schreibschutz); 

    $this->RechnungList();
  }

  function RechnungSuche()
  {
    $this->app->Tpl->Set('UEBERSCHRIFT','Rechnungen');
    $this->app->erp->Headlines('Rechnungen');

    $this->app->erp->MenuEintrag('index.php?module=rechnung&action=create','Neue Rechnung anlegen');
    $this->app->Tpl->Set('TABTEXT','Rechnungen');

    $name = $this->app->Secure->GetPOST('name');
    $plz = $this->app->Secure->GetPOST('plz');
    $auftrag = $this->app->Secure->GetPOST('auftrag');
    $kundennummer = $this->app->Secure->GetPOST('kundennummer');
    $proforma = '';

    if($name!='' || $plz!='' || $proforma!='' || $kundennummer!='' || $auftrag!='')
    {
      $table = new EasyTable($this->app);
      $this->app->Tpl->Add('ERGEBNISSE',"<h2>Trefferliste:</h2><br>");
      if($name!="")
        $table->Query("SELECT a.name, a.belegnr as rechnung, adr.kundennummer, a.plz, a.ort, a.strasse, a.status, a.id FROM rechnung a 
            LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.name LIKE '%$name%')");
      else if($plz!="")
        $table->Query("SELECT a.name, a.belegnr as rechnung, adr.kundennummer, a.plz, a.ort, a.strasse, a.status, a.id FROM rechnung a 
            LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.plz LIKE '$plz%')");
      else if($kundennummer!="")
        $table->Query("SELECT a.name, a.belegnr as rechnung, adr.kundennummer, a.plz, a.ort, a.strasse, a.status, a.id FROM rechnung a 
            LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (adr.kundennummer='$kundennummer')");
      else if($auftrag!="")
        $table->Query("SELECT a.name, a.belegnr as rechnung , adr.kundennummer,a.plz, a.ort, a.strasse, a.status, a.id FROM rechnung a 
            LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.belegnr='$auftrag')");

      //     $table->DisplayNew('ERGEBNISSE',"<a href=\"index.php?module=rechnung&action=edit&id=%value%\">Lesen</a>");
      $table->DisplayNew('ERGEBNISSE',"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
          <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
          <a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
          <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>


          <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\">
          <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
          ");

    } else {
      $this->app->Tpl->Add('ERGEBNISSE',"<div class=\"info\">Rechnungssuche (bitte entsprechende Suchparameter eingeben)</div>");
    }

    $this->app->Tpl->Parse('INHALT','rechnungssuche.tpl');

    $this->app->Tpl->Set('AKTIV_TAB1','selected');
    $this->app->Tpl->Parse('TAB1','rahmen77.tpl');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  public function RechnungMenu()
  {
    $id = $this->app->Secure->GetGET('id');
    $invoiceArr = $this->app->DB->SelectRow("SELECT belegnr, name,status FROM rechnung WHERE id='$id' LIMIT 1");
    $belegnr = $invoiceArr['belegnr'];
    $name = $invoiceArr['name'];

    if($belegnr=='0' || $belegnr=='') {
      $belegnr ='(Entwurf)';
    }

    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$name Rechnung $belegnr");

    $this->app->erp->RechnungNeuberechnen($id);

    $status = $invoiceArr['status'];

    if ($status==='angelegt') {
      $this->app->erp->MenuEintrag("index.php?module=rechnung&action=freigabe&id=$id",'Freigabe');
    }
    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=edit&id=$id",'Details');

    $anzahldateien = $this->app->erp->AnzahlDateien('Rechnung',$id);
    if($anzahldateien > 0) {
      $anzahldateien = ' ('.$anzahldateien.')';
    } else {
      $anzahldateien='';
    }

    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=dateien&id=$id",'Dateien'.$anzahldateien);


    
    $this->app->erp->MenuEintrag('index.php?module=rechnung&action=list','Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->erp->RunMenuHook('rechnung');
  }

  public function RechnungPositionen()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->RechnungNeuberechnen($id);
    $this->app->YUI->AARLGPositionen(false);
  }

  public function CopyRechnungPosition()
  {
    $this->app->YUI->SortListEvent('copy','rechnung_position','rechnung');
    $this->RechnungPositionen();    
  }

  public function DelRechnungPosition()
  {
    $this->app->YUI->SortListEvent('del','rechnung_position','rechnung');
    $this->RechnungPositionen();
  }

  public function UpRechnungPosition()
  {
    $this->app->YUI->SortListEvent('up','rechnung_position','rechnung');
    $this->RechnungPositionen();
  }

  public function DownRechnungPosition()
  {
    $this->app->YUI->SortListEvent('down','rechnung_position','rechnung');
    $this->RechnungPositionen();
  }


  public function RechnungPositionenEditPopup()
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
          $accordions[$k] = 'rechnung_accordion'.$v;
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
              $ret['accordions'][] = str_replace('rechnung_accordion','',$v['name']);
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
      $this->app->User->SetParameter('rechnung_accordion'.$name, $active);
      echo json_encode(array('success'=>1));
      $this->app->ExitXentral();
    }
    $id = $this->app->Secure->GetGET('id');

    $artikel= $this->app->DB->Select("SELECT artikel FROM angebot_position WHERE id='$id' LIMIT 1");

    // nach page inhalt des dialogs ausgeben
    $filename = 'widgets/widget.rechnung_position_custom.php';
    if(is_file($filename)) {
      include_once $filename;
      $widget = new WidgetRechnung_positionCustom($this->app,'PAGE');
    }
    else {
      $widget = new WidgetRechnung_position($this->app,'PAGE');
    }

    $sid= $this->app->DB->Select("SELECT rechnung FROM rechnung_position WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute('close_refresh',
        "index.php?module=rechnung&action=positionen&id=$sid");
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }



  //		       <li><a href="index.php?module=rechnung&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>

  /**
   * @param int $invoiceId
   * @param int $fileKeywordId
   *
   * @return int
   */
  public function moveFileUp($invoiceId, $fileKeywordId)
  {
    $check = $this->app->DB->SelectRow(
      sprintf(
        'SELECT ds.* 
        FROM datei_stichwoerter ds 
        INNER JOIN datei d on ds.datei = d.id 
        WHERE ds.id = %d and d.geloescht <> 1 
        LIMIT 1',
        $fileKeywordId
      )
    );
    if(empty($check)) {
      return 0;
    }

    $sort = $check['sort']-1;
    if($sort <= 0) {
      return 0;
    }

    $check2 = $this->app->DB->SelectArr(
      "SELECT ds.* FROM datei_stichwoerter ds 
      INNER JOIN datei d on ds.datei = d.id 
      WHERE ds.objekt like 'rechnung' AND ds.sort = %d AND d.geloescht <> 1 
        AND ds.parameter = %d 
        LIMIT 1",
      $sort, $invoiceId
    );
    if(empty($check2)) {
      return 0;
    }

    $this->app->DB->Update(
      sprintf(
        'UPDATE datei_stichwoerter SET sort = sort - 1 WHERE id = %d LIMIT 1',
        $fileKeywordId
      )
    );
    $this->app->DB->Update(
      sprintf(
        'UPDATE datei_stichwoerter SET sort = sort + 1 WHERE id = %d LIMIT 1',
        $check2['id']
      )
    );

    return $check2['id'];
  }

  /**
   * @param int $invoiceId
   * @param int $fileKeywordId
   *
   * @return int
   */
  public function moveFileDown($invoiceId, $fileKeywordId)
  {
    $check = $this->app->DB->SelectRow(
      sprintf(
        'SELECT ds.* 
        FROM datei_stichwoerter ds 
        INNER JOIN datei d on ds.datei = d.id 
        WHERE ds.id = %d and d.geloescht <> 1 
        LIMIT 1',
        $fileKeywordId
      )
    );
    if(empty($check)) {
      return 0;
    }

    $sort = $check['sort']+1;
    if($sort <= 1) {
      return 0;
    }

    $check2 = $this->app->DB->SelectArr(
      "SELECT ds.* FROM datei_stichwoerter ds 
      INNER JOIN datei d on ds.datei = d.id 
      WHERE ds.objekt like 'rechnung' AND ds.sort = %d AND d.geloescht <> 1 
        AND ds.parameter = %d 
        LIMIT 1",
      $sort, $invoiceId
    );
    if(empty($check2)) {
      return 0;
    }

    $this->app->DB->Update(
      sprintf(
        'UPDATE datei_stichwoerter SET sort = sort + 1 WHERE id = %d LIMIT 1',
        $fileKeywordId
      )
    );
    $this->app->DB->Update(
      sprintf(
        'UPDATE datei_stichwoerter SET sort = sort - 1 WHERE id = %d LIMIT 1',
        $check2['id']
      )
    );

    return $check2['id'];
  }

  public function RechnungEdit()
  {
    $id = $this->app->Secure->GetGET('id');
    // zum aendern vom Vertrieb
    $sid = $this->app->Secure->GetGET('sid');
    $cmd = $this->app->Secure->GetGET('cmd');
    if($this->app->Secure->GetPOST('resetextsoll')) {
      $this->app->DB->Update(
        sprintf(
          'UPDATE rechnung SET extsoll = 0 WHERE id = %d',
          $id
        )
      );
      $this->app->erp->RechnungNeuberechnen($id);
    }

    if($cmd === 'dadown')
    {
      $erg['status'] = 0;
      $daid = $this->app->Secure->GetPOST('da_id');
      $from = $this->moveFileDown($id, $daid);
      if($from > 0) {
        $erg['status'] = 1;
        $erg['from'] =$from;
      }
      echo json_encode($erg);
      $this->app->ExitXentral();
    }
    
    if($cmd === 'daup')
    {
      $erg['status'] = 0;
      $daid = $this->app->Secure->GetPOST('da_id');
      $from = $this->moveFileUp($id, $daid);
      if($from > 0) {
        $erg['status'] = 1;
        $erg['from'] =$from;
      }

      echo json_encode($erg);
      $this->app->ExitXentral();
    }
    
    if($this->app->erp->VertriebAendern('rechnung',$id,$cmd,$sid)){
      return;
    }
    if($this->app->erp->InnendienstAendern('rechnung',$id,$cmd,$sid)){
      return;
    }

    if($this->app->erp->DisableModul('rechnung',$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->RechnungMenu();
      return;
    }
    $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
    if($adresse <=0) {
      $this->app->Tpl->Add('JAVASCRIPT','$(document).ready(function() { if(document.getElementById("adresse"))document.getElementById("adresse").focus(); });');
      $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Achtung! Dieses Dokument ist mit keiner Kunden-Nr. verlinkt. Bitte geben Sie die Kundennummer an und klicken Sie &uuml;bernehmen oder Speichern!</div>");
    }
    $this->app->YUI->AARLGPositionen();

    $this->app->erp->DisableVerband();
    $this->app->erp->CheckBearbeiter($id,'rechnung');
    $this->app->erp->CheckBuchhaltung($id,'rechnung');

    $invoiceArr = $this->app->DB->SelectRow(
      sprintf(
        'SELECT zahlungsweise,zahlungszieltage,dta_datei,status,schreibschutz  FROM rechnung WHERE id= %d LIMIT 1',
        (int)$id
      )
    );
    $zahlungsweise= $invoiceArr['zahlungsweise'];
    $zahlungszieltage= $invoiceArr['zahlungszieltage'];
    if($zahlungsweise==='rechnung' && $zahlungszieltage<1)
    {
      $this->app->Tpl->Add('MESSAGE',"<div class=\"info\">Hinweis: F&auml;lligkeit auf \"sofort\", da Zahlungsziel in Tagen auf 0 Tage gesetzt ist!</div>");
    }


    $status= $invoiceArr['status'];
    $schreibschutz= $invoiceArr['schreibschutz'];
    if($status !== 'angelegt' && $status !== 'angelegta' && $status !== 'a')
    {
      $Brief = new Briefpapier($this->app);
      if($Brief->zuArchivieren($id, "rechnung"))
      {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Die Rechnung ist noch nicht archiviert! Bitte versenden oder manuell archivieren. <input type=\"button\" onclick=\"if(!confirm('Soll das Dokument archiviert werden?')) return false;else window.location.href='index.php?module=rechnung&action=archivierepdf&id=$id';\" value=\"Manuell archivieren\" /> <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('rechnung',$id)\"></div>");
      }elseif(!$this->app->DB->Select("SELECT versendet FROM rechnung WHERE id = '$id' LIMIT 1"))
      {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Die Rechnung wurde noch nicht versendet! <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('rechnung',$id)\"></div>");
      }
    }
    $this->app->erp->RechnungNeuberechnen($id); //BENE

    $this->RechnungMiniDetail('MINIDETAIL',false); //BENE
    $this->app->Tpl->Set('ICONMENU',$this->RechnungIconMenu($id));
    $this->app->Tpl->Set('ICONMENU2',$this->RechnungIconMenu($id,2));
    if($id > 0){
      $rechnungarr = $this->app->DB->SelectRow("SELECT * FROM rechnung WHERE id='$id' LIMIT 1");
    }
    $nummer = '';
    $kundennummer = '';
    $adresse = 0;
    $punkte = null;
    $bonuspunkte = null;
    $soll = 0;
    $projekt = 0;
    if(!empty($rechnungarr)){
      $nummer = $rechnungarr['belegnr'];//$this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
      $kundennummer = $rechnungarr['kundennummer'];//$this->app->DB->Select("SELECT kundennummer FROM rechnung WHERE id='$id' LIMIT 1");
      $adresse = $rechnungarr['adresse'];//$this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
      $punkte = $rechnungarr['punkte'];//$this->app->DB->Select("SELECT punkte FROM rechnung WHERE id='$id' LIMIT 1");
      $bonuspunkte = $rechnungarr['bonuspunkte'];//$this->app->DB->Select("SELECT bonuspunkte FROM rechnung WHERE id='$id' LIMIT 1");
      $soll = $rechnungarr['soll'];//$this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id' LIMIT 1");
      $projekt = $rechnungarr['projekt'];
    }

    $this->app->Tpl->Set('PUNKTE',"<input type=\"text\" name=\"punkte\" value=\"$punkte\" size=\"10\" readonly>");
    $this->app->Tpl->Set('BONUSPUNKTE',"<input type=\"text\" name=\"punkte\" value=\"$bonuspunkte\" size=\"10\" readonly>");

    $this->app->Tpl->Set('SOLL',"$soll"."<input type=\"hidden\" id=\"soll_tmp\" value=\"$soll\">");

    if($schreibschutz!='1')// && $this->app->erp->RechteVorhanden("rechnung","schreibschutz"))
    {
      $this->app->erp->AnsprechpartnerButton($adresse);
      $this->app->erp->LieferadresseButton($adresse);
    }

    if($nummer!='') {
      $this->app->Tpl->Set('NUMMER',$nummer);
      if($this->app->erp->RechteVorhanden('adresse','edit')){
        $this->app->Tpl->Set('KUNDE', "&nbsp;&nbsp;&nbsp;Kd-Nr. <a href=\"index.php?module=adresse&action=edit&id=$adresse\" target=\"_blank\">" . $kundennummer . "</a>");
      }
      else{
        $this->app->Tpl->Set('KUNDE', "&nbsp;&nbsp;&nbsp;Kd-Nr. " . $kundennummer);
      }
    }
    $lieferdatum = '';
    $rechnungsdatum = '';
    $lieferscheinid = 0;
    if(!empty($rechnungarr)) {
      $lieferdatum = $rechnungarr['lieferdatum'];//$this->app->DB->Select("SELECT lieferdatum FROM rechnung WHERE id='$id' LIMIT 1");
      $rechnungsdatum = $rechnungarr['datum'];//$this->app->DB->Select("SELECT datum FROM rechnung WHERE id='$id' LIMIT 1");
      $lieferscheinid = $rechnungarr['lieferschein'];//$this->app->DB->Select("SELECT lieferschein FROM rechnung WHERE id='$id' LIMIT 1");
    }
    $lieferscheiniddatum = '';
    if($lieferscheinid > 0){
      $lieferscheiniddatum = $this->app->DB->Select("SELECT datum FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");
    }
    if($lieferdatum=='0000-00-00' && $schreibschutz!='1') {
      if($lieferscheiniddatum!='0000-00-00'){
        $this->app->DB->Update("UPDATE rechnung SET lieferdatum='$lieferscheiniddatum' WHERE id='$id' LIMIT 1");
      }
      else{
        $this->app->DB->Update("UPDATE rechnung SET lieferdatum='$rechnungsdatum' WHERE id='$id' LIMIT 1");
      }
    } 

    if($schreibschutz!='1') {
      $this->app->DB->Update("UPDATE rechnung SET auftrag='' WHERE id='$id' AND auftragid<=0 LIMIT 1");
    }

    $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM rechnung WHERE id='$id' LIMIT 1");
    if($this->app->Secure->GetPOST('zahlungsweise')!='') {
      $zahlungsweise = $this->app->Secure->GetPOST('zahlungsweise');
    }
    $zahlungsweise = strtolower($zahlungsweise);

    $zahlungsweisenmodule = $this->app->DB->SelectArr(
      "SELECT id, modul, verhalten 
        FROM zahlungsweisen 
        WHERE type = '".$this->app->DB->real_escape_string($zahlungsweise)."' AND
          (projekt = '$projekt' OR projekt = 0) 
        ORDER BY projekt = '$projekt' DESC 
        LIMIT 1"
    );

    $this->app->Tpl->Set('RECHNUNG','none');
    $this->app->Tpl->Set('KREDITKARTE','none');
    $this->app->Tpl->Set('VORKASSE','none');
    $this->app->Tpl->Set('PAYPAL','none');
    $this->app->Tpl->Set('EINZUGSERMAECHTIGUNG','none');
    if($zahlungsweise==='rechnung' || isset($zahlungsweisenmodule[0]['verhalten']) && $zahlungsweisenmodule[0]['verhalten']==='rechnung') {
      $this->app->Tpl->Set('RECHNUNG',"");
    }
    if($zahlungsweise==='paypal') {
      $this->app->Tpl->Set('PAYPAL','');
    }
    if($zahlungsweise==='kreditkarte') {
      $this->app->Tpl->Set('KREDITKARTE','');
    }
    if($zahlungsweise==='einzugsermaechtigung' || $zahlungsweise==='lastschrift') {
      $this->app->Tpl->Set('EINZUGSERMAECHTIGUNG','');
    }
    if($zahlungsweise==='vorkasse' || $zahlungsweise==='kreditkarte' || $zahlungsweise==='paypal' || $zahlungsweise==='bar') {
      $this->app->Tpl->Set('VORKASSE','');
    }


    $saldo=$this->app->DB->Select("SELECT ist-skonto_gegeben FROM rechnung WHERE id='$id'");
    $this->app->Tpl->Set('LIVEIST',"$saldo");

    if($schreibschutz=="1" && $this->app->erp->RechteVorhanden('rechnung','schreibschutz'))
    {
      $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">Diese Rechnung ist schreibgesch&uuml;tzt und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml;r diese Rechnung wirklich entfernt werden? Die gespeicherte Rechnung wird &uuml;berschrieben!')) return false;else window.location.href='index.php?module=rechnung&action=schreibschutz&id=$id';\"></div>");
    }

    if($schreibschutz=='1'){
      $this->app->erp->CommonReadonly();
    }

    if($schreibschutz=='1' && $this->app->erp->RechteVorhanden('rechnung','mahnwesen'))
    {
      $this->app->erp->RemoveReadonly('mahnwesen_datum');
      $this->app->erp->RemoveReadonly('mahnwesen_gesperrt');
      $this->app->erp->RemoveReadonly('mahnwesen_internebemerkung');
      $this->app->erp->RemoveReadonly('zahlungsstatus');
      $this->app->erp->RemoveReadonly('mahnwesenfestsetzen');
      $this->app->erp->RemoveReadonly('mahnwesen');
      $this->app->erp->RemoveReadonly('bezahlt_am');
      $this->app->erp->RemoveReadonly('ist');

      if($this->app->erp->Firmendaten('mahnwesenmitkontoabgleich')!='1' || $this->app->DB->Select("SELECT mahnwesenfestsetzen FROM rechnung WHERE id='$id' LIMIT 1")==1)  
        $this->app->erp->RemoveReadonly('ist');

      //$auftrag= $this->app->DB->Select("SELECT auftrag FROM rechnung WHERE id='$id' LIMIT 1");

      $this->app->erp->RemoveReadonly('skonto_gegeben');
      $this->app->erp->RemoveReadonly('internebemerkung');

      $alle_gutschriften = $this->app->DB->SelectArr("SELECT id,belegnr FROM gutschrift WHERE rechnungid='$id' AND rechnungid>0");
      $cgutschriften = !empty($alle_gutschriften)?count($alle_gutschriften):0;
      if($cgutschriften > 1)
      {
        $gutschriften = '';
        for($agi=0;$agi<$cgutschriften;$agi++)
          $gutschriften .= "<a href=\"index.php?module=gutschrift&action=edit&id=".$alle_gutschriften[$agi][id]."\" target=\"_blank\">".$alle_gutschriften[$agi][belegnr]."</a> ";
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">F&uuml;r die angebene Rechnung gibt es schon folgende Gutschriften: $gutschriften</div>");
      }

      $this->app->erp->CommonReadonly();
    }

    $speichern = $this->app->Secure->GetPOST('speichern');
    if($speichern!='' && $this->app->erp->RechteVorhanden('rechnung','mahnwesen'))
    {
      $mahnwesen_datum = $this->app->Secure->GetPOST('mahnwesen_datum');
      $bezahlt_am = $this->app->Secure->GetPOST('bezahlt_am');
      $mahnwesen_gesperrt = $this->app->Secure->GetPOST('mahnwesen_gesperrt');
      $mahnwesen_internebemerkung = $this->app->Secure->GetPOST('mahnwesen_internebemerkung');
      $zahlungsstatus = $this->app->Secure->GetPOST('zahlungsstatus');
      $mahnwesenfestsetzen = $this->app->Secure->GetPOST('mahnwesenfestsetzen');
      $mahnwesen = $this->app->Secure->GetPOST('mahnwesen');
      $internebemerkung = $this->app->Secure->GetPOST('internebemerkung');
      $ist = str_replace(',','.',$this->app->Secure->GetPOST('ist'));
      $skonto_gegeben = str_replace(',','.',$this->app->Secure->GetPOST('skonto_gegeben'));

      if($mahnwesen_gesperrt!='1') {
        $mahnwesen_gesperrt='0';
      }
      if($mahnwesenfestsetzen!='1') {
        $mahnwesenfestsetzen='0';
      }
  
      $mahnwesen_datum = $this->app->String->Convert($mahnwesen_datum,'%1.%2.%3','%3-%2-%1');
      $bezahlt_am = $this->app->String->Convert($bezahlt_am,'%1.%2.%3','%3-%2-%1');

      if($bezahlt_am=='--')$bezahlt_am='0000-00-00';
      $alte_mahnstufe = $this->app->DB->Select("SELECT mahnwesen FROM rechnung WHERE id='$id' LIMIT 1");
      if($alte_mahnstufe!=$mahnwesen) $versendet=0; else $versendet=1;

      if($mahnwesenfestsetzen=='1')
      {
        $this->app->DB->Update("UPDATE rechnung SET mahnwesen_internebemerkung='$mahnwesen_internebemerkung',zahlungsstatus='$zahlungsstatus',versendet_mahnwesen='$versendet',
          mahnwesen_gesperrt='$mahnwesen_gesperrt',mahnwesen_datum='$mahnwesen_datum', mahnwesenfestsetzen='$mahnwesenfestsetzen',internebemerkung='$internebemerkung',
          mahnwesen='$mahnwesen',ist='$ist',skonto_gegeben='$skonto_gegeben',bezahlt_am='$bezahlt_am' WHERE id='$id' LIMIT 1");
      } else {
        $this->app->DB->Update("UPDATE rechnung SET mahnwesen='$mahnwesen', mahnwesenfestsetzen='$mahnwesenfestsetzen', mahnwesen_internebemerkung='$mahnwesen_internebemerkung', mahnwesen_gesperrt='$mahnwesen_gesperrt',mahnwesen_datum='$mahnwesen_datum' WHERE id='$id' LIMIT 1");
      }
    }


    if($status=='')
      $this->app->DB->Update("UPDATE rechnung SET status='angelegt' WHERE id='$id' LIMIT 1");

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
    if($this->app->Secure->GetPOST('adresse')!='')
    {
      $tmp = $this->app->Secure->GetPOST('adresse');
      $kundennummer = $this->app->erp->FirstTillSpace($tmp);

      //$name = substr($tmp,6);
      $filter_projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id = '$id' LIMIT 1");
      //if($filter_projekt)$filter_projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id= '$filter_projekt' and eigenernummernkreis = 1 LIMIT 1");
      $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' AND geloescht=0 ".$this->app->erp->ProjektRechte("projekt", true, 'vertrieb')." ORDER by ".($filter_projekt?" projekt = '$filter_projekt' DESC, ":"")." projekt LIMIT 1");

      $uebernehmen =$this->app->Secure->GetPOST('uebernehmen');
      if($uebernehmen=='1' && $schreibschutz != '1') // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
      {
        $this->LoadRechnungStandardwerte($id,$adresse);
        $this->app->erp->RechnungNeuberechnen($id);
        $this->app->Location->execute('index.php?module=rechnung&action=edit&id='.$id);
      }
    }
    $rechnungarr = null;
    if($id > 0) {
      $rechnungarr = $this->app->DB->SelectRow("SELECT * FROM rechnung WHERE id='$id' LIMIT 1");
    }
    $land = '';
    $ustid = '';
    $ust_befreit = null;
    if(!empty($rechnungarr)) {
      $land = $rechnungarr['land'];//$this->app->DB->Select("SELECT land FROM rechnung WHERE id='$id' LIMIT 1");
      $ustid = $rechnungarr['ustid'];//$this->app->DB->Select("SELECT ustid FROM rechnung WHERE id='$id' LIMIT 1");
      $ust_befreit = $rechnungarr['ust_befreit'];//$this->app->DB->Select("SELECT ust_befreit FROM rechnung WHERE id='$id' LIMIT 1");
    }
    if($ust_befreit) {
      $this->app->Tpl->Set('USTBEFREIT',"<div class=\"info\">EU-Lieferung <br>(bereits gepr&uuml;ft!)</div>");
    }
    else if($land!=='DE' && $ustid!='') {
      $this->app->Tpl->Set('USTBEFREIT',"<div class=\"error\">EU-Lieferung <br>(Fehler bei Pr&uuml;fung!)</div>");
    }


    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $table->Query("SELECT bezeichnung as artikel, nummer as Nummer, menge, vpe as VPE, FORMAT(preis,4) as preis
        FROM rechnung_position
        WHERE rechnung='$id'");
    $table->DisplayNew('POSITIONEN',"Preis","noAction");
    $summe = $this->app->DB->Select("SELECT FORMAT(SUM(menge*preis),2) FROM rechnung_position
        WHERE rechnung='$id'");
    $waehrung = $this->app->DB->Select("SELECT waehrung FROM rechnung_position
        WHERE rechnung='$id' LIMIT 1");

    $summebrutto = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id' LIMIT 1");
    $ust_befreit_check = $this->app->DB->Select("SELECT ust_befreit FROM rechnung WHERE id='$id' LIMIT 1");

    $tmp = 'Kunde zahlt mit UST';
    if($ust_befreit_check==1) {
      $tmp = 'Kunde ist UST befreit';
    }

    if($summe > 0){
      $this->app->Tpl->Add('POSITIONEN', "<br><center>Zu zahlen: <b>$summe (netto) $summebrutto (brutto) $waehrung</b> ($tmp)&nbsp;&nbsp;");
    }

    $status= $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('STATUS',"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");

    $internet = $this->app->DB->Select("SELECT a.internet FROM rechnung r LEFT JOIN auftrag a ON a.id=r.auftragid WHERE r.id='$id' AND r.id > 0 LIMIT 1");
    if($internet!='') {
      $this->app->Tpl->Set('INTERNET',"<tr><td>Internet:</td><td><input type=\"text\" size=\"30\" value=\"".$internet."\" readonly [COMMONREADONLYINPUT]></td></tr>");
    }

    $this->app->Tpl->Set('AKTIV_TAB1',"selected");

    $sollExtSoll = $this->app->DB->SelectRow(
      sprintf(
        "SELECT extsoll, soll 
        FROM rechnung 
        WHERE id = %d AND schreibschutz = 0 AND status = 'versendet' AND extsoll <> 0",
        $id
      )
    );
    if(!empty($sollExtSoll['extsoll']) && $sollExtSoll['extsoll'] == $sollExtSoll['soll']) {
      $sollExtSoll['soll'] = $this->app->DB->Select(
        sprintf(
          'SELECT ROUND(SUM(`umsatz_brutto_gesamt`),2) FROM `rechnung_position` WHERE `rechnung` = %d ',
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
    parent::RechnungEdit();
    if($id > 0 && $this->app->DB->Select(
      sprintf(
        'SELECT id FROM rechnung WHERE schreibschutz =1  AND zuarchivieren = 1 AND id = %d',
        $id
      )
      )
    ) {
      $this->app->erp->PDFArchivieren('rechnung', $id, true);
    }
    $this->app->erp->MessageHandlerStandardForm();

    if($this->app->Secure->GetPOST('weiter')!='')
    {
      $this->app->Location->execute('index.php?module=rechnung&action=positionen&id='.$id);
    }
    $this->RechnungMenu();
  }

  function RechnungCreate()
  {
    $anlegen = $this->app->Secure->GetGET('anlegen');
    if($this->app->erp->Firmendaten('schnellanlegen')=='1' && $anlegen!='1') {
      $this->app->Location->execute('index.php?module=rechnung&action=create&anlegen=1');
    }

    if($anlegen != '') {
      $id = $this->CreateRechnung();
      $this->app->Location->execute('index.php?module=rechnung&action=edit&id='.$id);
    }
    
    $this->app->Tpl->Add('KURZUEBERSCHRIFT','Rechnung');
    $this->app->erp->MenuEintrag('index.php?module=rechnung&action=list','Zur&uuml;ck zur &Uuml;bersicht');
    
    $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">M&ouml;chten Sie eine Rechnung jetzt anlegen? &nbsp;
        <input type=\"button\" onclick=\"window.location.href='index.php?module=rechnung&action=create&anlegen=1'\" value=\"Ja - Rechnung jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set('TAB1',"
        <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><b style=\"font-size: 14pt\">Rechnungen in Bearbeitung</b>
        <br>
        <br>
        Offene Auftr&auml;ge, die durch andere Mitarbeiter in Bearbeitung sind.
        <br>
        </td>
        </tr>  
        </table>
        <br> 
        [AUFTRAGE]");


    $this->app->Tpl->Set('AKTIV_TAB1','selected');
    $this->app->YUI->TableSearch('AUFTRAGE','rechnungeninbearbeitung');
    $this->app->Tpl->Set('TABTEXT','Rechnung anlegen');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  /**
   * @param int $invoiceId
   *
   * @return bool
   */
  public function markInvoiceAsClosed($invoiceId)
  {
    $reArr = $this->app->DB->SelectRow(
      sprintf(
        "SELECT projekt,belegnr,status,usereditid,
        DATE_SUB(NOW(), INTERVAL 30 SECOND) < useredittimestamp AS `open` 
        FROM rechnung WHERE id=%d LIMIT 1",
        $invoiceId
      )
    );
    if($reArr['belegnr'] === '') {
      return false;
    }
    if($reArr['status'] === 'freigegeben') {
      $this->app->erp->RechnungNeuberechnen($invoiceId);
    }
    $projekt = $reArr['projekt'];
    $this->app->erp->RechnungProtokoll($invoiceId,'Rechnung versendet');
    $this->app->erp->closeInvoice($invoiceId);
    $this->app->DB->Update(
      sprintf(
        'UPDATE rechnung SET schreibschutz=1, versendet = 1 WHERE id = %d LIMIT 1',
        $invoiceId
      )
    );
    $this->app->DB->Update(
      sprintf(
        "UPDATE rechnung 
        SET status='versendet' 
        WHERE id = %d AND status!='storniert' 
        LIMIT 1",
        $invoiceId
      )
    );
    $this->app->erp->PDFArchivieren('rechnung', $invoiceId, true);
    if(class_exists('RechnungPDFCustom')) {
      $Brief = new RechnungPDFCustom($this->app,$projekt);
    }
    else {
      $Brief = new RechnungPDF($this->app,$projekt);
    }
    $Brief->GetRechnung($invoiceId);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument();
    @unlink($tmpfile);

    return true;
  }

  public function RechnungList()
  {
    $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='offen' WHERE zahlungsstatus=''");

    if($this->app->Secure->GetPOST('ausfuehren') && $this->app->erp->RechteVorhanden('rechnung', 'edit'))
    {
      $drucker = $this->app->Secure->GetPOST('seldrucker');
      $aktion = $this->app->Secure->GetPOST('sel_aktion');
      $auswahl = $this->app->Secure->GetPOST('auswahl');
      if($drucker > 0) {
        $this->app->erp->BriefpapierHintergrundDisable($drucker);
      }
      if(is_array($auswahl)) {
        foreach($auswahl as $auswahlKey => $auswahlValue) {
          if((int)$auswahlValue > 0) {
            $auswahl[$auswahlKey] = (int)$auswahlValue;
          }
          else {
            unset($auswahl[$auswahlKey]);
          }
        }
        switch($aktion)
        {
          case 'bezahlt':
            $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='bezahlt', bezahlt_am = now(), ist=soll,mahnwesenfestsetzen='1',mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt markiert am ".date('d.m.Y')."')  WHERE id IN (".implode(', ',$auswahl).')');
          break;
          case 'offen':
            $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='offen',bezahlt_am = NULL, ist='0',mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt entfernt am ".date('d.m.Y')."') WHERE id IN (".implode(', ',$auswahl).')');
          break;
          case 'mail':
            $auswahl = $this->app->DB->SelectFirstCols(
              sprintf(
                "SELECT id FROM rechnung WHERE belegnr <> '' AND id IN (%s)",
                implode(', ', $auswahl)
              )
            );
            foreach($auswahl as $v) {
              if(!$v) {
                continue;
              }
              $checkpapier = $this->app->DB->Select(
                "SELECT a.rechnung_papier FROM rechnung AS r 
                LEFT JOIN adresse AS a ON r.adresse=a.id 
                WHERE r.id='$v' 
                LIMIT 1"
              );
              if($checkpapier!=1 &&
                $this->app->DB->Select(
                  "SELECT r.id 
                  FROM rechnung AS r 
                  INNER JOIN adresse AS a ON r.adresse = a.id 
                  WHERE r.id = '$v' AND r.email <> '' OR a.email <> '' 
                  LIMIT 1"
                )
              ) {
                $this->app->erp->PDFArchivieren('rechnung', $v, true);
                $this->app->erp->Rechnungsmail($v);
              }
              else if($checkpapier && $drucker) {
                $this->app->erp->PDFArchivieren('rechnung', $v, true);
                $projekt = $this->app->DB->Select(
                  "SELECT projekt FROM rechnung WHERE id='$v' LIMIT 1"
                );
                if(class_exists('RechnungPDFCustom')) {
                  $Brief = new RechnungPDFCustom($this->app,$projekt);
                }
                else {
                  $Brief = new RechnungPDF($this->app,$projekt);
                }
                $Brief->GetRechnung($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->printer->Drucken($drucker,$tmpfile);
                unlink($tmpfile);
              }
            }
          break;
          case 'versendet':
            $auswahl = $this->app->DB->SelectFirstCols(
              sprintf(
                "SELECT id FROM rechnung WHERE belegnr <> '' AND id IN (%s)",
                implode(', ', $auswahl)
              )
            );
            foreach($auswahl as $v) {
              if($v) {
                $reArr = $this->app->DB->SelectRow(
                  sprintf(
                    "SELECT projekt,belegnr,status,usereditid,
                    DATE_SUB(NOW(), INTERVAL 30 SECOND) < useredittimestamp AS `open` 
                    FROM rechnung WHERE id=%d LIMIT 1",
                    $v
                  )
                );
                if($reArr['belegnr'] === '' || ($reArr['open'] && $reArr['status'] === 'freigegeben')) {
                  continue;
                }
                $this->markInvoiceAsClosed($v);
              }
            }
          break;
          case 'drucken':
            if($drucker) {
              $auswahl = $this->app->DB->SelectFirstCols(
                sprintf(
                  "SELECT id FROM rechnung WHERE belegnr <> '' AND id IN (%s)",
                  implode(', ', $auswahl)
                )
              );
              foreach($auswahl as $v) {
                $reArr = $this->app->DB->SelectRow(
                  sprintf(
                    "SELECT projekt,belegnr,status,usereditid,adresse,
                    DATE_SUB(NOW(), INTERVAL 30 SECOND) < useredittimestamp AS `open` 
                    FROM rechnung WHERE id=%d LIMIT 1",
                    $v
                  )
                );
                if($reArr['belegnr'] === '' || ($reArr['open'] && $reArr['status'] === 'freigegeben')) {
                  continue;
                }
                if($reArr['status'] === 'freigegeben') {
                  $this->app->erp->RechnungNeuberechnen($v);
                }
                $projekt = $reArr['projekt'];//$this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$v' LIMIT 1");
                $this->app->erp->RechnungProtokoll($v,'Rechnung gedruckt');
                $this->app->DB->Update("UPDATE rechnung SET schreibschutz=1, versendet = 1  WHERE id = '$v' LIMIT 1");
                $this->app->DB->Update("UPDATE rechnung SET status='versendet' WHERE id = '$v' AND status!='storniert' LIMIT 1");
                $this->app->erp->PDFArchivieren('rechnung', $v, true);
                if(class_exists('RechnungPDFCustom')) {
                  $Brief = new RechnungPDFCustom($this->app,$projekt);
                }
                else{
                  $Brief = new RechnungPDF($this->app,$projekt);
                }
                $Brief->GetRechnung($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->printer->Drucken($drucker,$tmpfile);
                $doctype = 'rechnung';
                $this->app->erp->RunHook('dokumentsend_ende', 5, $doctype, $v, $projekt, $reArr['adresse'], $aktion);
                @unlink($tmpfile);
              }
            }
          break;
          case 'pdf':
            $tmpfile = [];
            foreach($auswahl as $v) {
              $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id=$v LIMIT 1");
              if(class_exists('RechnungPDFCustom')) {
                $Brief = new RechnungPDFCustom($this->app,$projekt);
              }
              else {
                $Brief = new RechnungPDF($this->app,$projekt);
              }
              $Brief->GetRechnung($v);
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
    
    $this->app->Tpl->Set('UEBERSCHRIFT','Rechnungen');

    $backurl = $this->app->Secure->GetGET('backurl');
    $backurl = $this->app->erp->base64_url_decode($backurl);

    $this->app->erp->MenuEintrag('index.php?module=rechnung&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=rechnung&action=create','Neue Rechnung anlegen');

    if(strlen($backurl)>5){
      $this->app->erp->MenuEintrag("$backurl", 'Zur&uuml;ck');
    }
    //else
    //  $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");

    $zahlungsweisen = $this->app->DB->SelectArr('
      SELECT
        zahlungsweise
      FROM
        rechnung
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
        rechnung
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
        rechnung
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


    $this->app->Tpl->Add('ZAHLUNGSWEISEN',$zahlungsweiseStr);
    $this->app->Tpl->Add('STATUS',$statusStr);
    $this->app->Tpl->Add('VERSANDARTEN',$versandartenStr);
    $this->app->Tpl->Add('LAENDER',$laenderStr);

    $this->app->YUI->DatePicker('datumVon');
    $this->app->YUI->DatePicker('datumBis');
    $this->app->YUI->AutoComplete("projekt", "projektname", 1);
    $this->app->YUI->AutoComplete("kundennummer", "kunde", 1);
    $this->app->YUI->AutoComplete("rechnungsnummer", "rechnung", 1);
    $this->app->YUI->AutoComplete("artikel", "artikelnummer", 1);
    $this->app->Tpl->Parse('TAB1',"rechnung_table_filter.tpl");

    $this->app->Tpl->Set('AKTIV_TAB1',"selected");
    $this->app->Tpl->Set('INHALT','');

    $this->app->YUI->TableSearch('TAB2','rechnungenoffene');
    $this->app->YUI->TableSearch('TAB1','rechnungen');
    $this->app->YUI->TableSearch('TAB3','rechnungeninbearbeitung');

    if($this->app->erp->RechteVorhanden('rechnung', 'manuellbezahltmarkiert')){
      $this->app->Tpl->Set('ALSBEZAHLTMARKIEREN', '<option value="bezahlt">{|als bezahlt markieren|}</option>');
    }

    $this->app->Tpl->Set('SELDRUCKER', $this->app->erp->GetSelectDrucker($this->app->User->GetParameter('rechnung_list_drucker')));
    
    $this->app->Tpl->Parse('PAGE','rechnunguebersicht.tpl');
  }

  /**
   * @param string|int $adresse
   *
   * @return int
   */
  public function CreateRechnung($adresse='')
  {
    $projekt = $this->app->erp->GetCreateProjekt($adresse);

    $belegmax = '';
    $ohnebriefpapier = $this->app->erp->Firmendaten('rechnung_ohnebriefpapier');

    $usereditid = 0;
    if(isset($this->app->User) && $this->app->User && method_exists($this->app->User,'GetID')){
      $usereditid = $this->app->User->GetID();
    }

    if($this->app->erp->StandardZahlungsweise($projekt)==='rechnung')
    {
      $this->app->DB->Insert("INSERT INTO rechnung (id,datum,bearbeiter,firma,belegnr,zahlungsweise,
          zahlungszieltage,
          zahlungszieltageskonto,
          zahlungszielskonto,
          lieferdatum,
          status,projekt,adresse,auftragid,ohne_briefpapier,angelegtam,usereditid)
            VALUES ('',NOW(),'','".$this->app->User->GetFirma()."','$belegmax','".$this->app->erp->StandardZahlungsweise($projekt)."',
              '".$this->app->erp->ZahlungsZielTage($projekt)."',
              '".$this->app->erp->ZahlungsZielTageSkonto($projekt)."',
              '".$this->app->erp->ZahlungsZielSkonto($projekt)."',NOW(),
              'angelegt','$projekt','$adresse',0,'".$ohnebriefpapier."',NOW(),'$usereditid')");
    } else {
      $this->app->DB->Insert("INSERT INTO rechnung (id,datum,bearbeiter,firma,belegnr,zahlungsweise,
          zahlungszieltage,
          zahlungszieltageskonto,
          zahlungszielskonto,
          lieferdatum,
          status,projekt,adresse,auftragid,ohne_briefpapier,angelegtam,usereditid)
            VALUES ('',NOW(),'','".$this->app->User->GetFirma()."','$belegmax','".$this->app->erp->StandardZahlungsweise($projekt)."',
              '0',
              '0',
              '0',NOW(),
              'angelegt','$projekt','$adresse',0,'".$ohnebriefpapier."',NOW(),'$usereditid')");
    }

    $id = $this->app->DB->GetInsertID();
    $this->app->erp->CheckVertrieb($id,'rechnung');
    $this->app->erp->CheckBearbeiter($id,'rechnung');

    $this->app->erp->RechnungProtokoll($id,'Rechnung angelegt');

    $type='rechnung';
    $this->app->erp->ObjektProtokoll($type,$id,$type.'_create',ucfirst($type).' angelegt');
    $deliverythresholdvatid = $this->app->erp->getDeliverythresholdvatid($projekt);
    if($id > 0 && !empty($deliverythresholdvatid)){
      $deliverythresholdvatid = $this->app->DB->real_escape_string($deliverythresholdvatid);
      $this->app->DB->Update("UPDATE rechnung SET deliverythresholdvatid = '$deliverythresholdvatid' WHERE id = $id LIMIT 1");
    }
    $this->app->erp->SchnellFreigabe('rechnung',$id);

    $this->app->erp->LoadSteuersaetzeWaehrung($id,'rechnung',$projekt);
    $this->app->erp->EventAPIAdd('EventRechnungCreate',$id,'rechnung','create');

    return $id;
  }

  /**
   * @param int $id
   *
   * @return int
   */
  public function CopyRechnung($id)
  {
    $this->app->DB->Insert("INSERT INTO rechnung (angelegtam) VALUES (NOW())");
    $newid = $this->app->DB->GetInsertID();
    $arr = $this->app->DB->SelectRow("SELECT NOW() as datum,projekt,bodyzusatz,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,bearbeiter,zahlungszieltage,zahlungszieltageskonto,zahlungsweise,ohne_artikeltext,ohne_briefpapier,'angelegt' as status,
            zahlungszielskonto,ust_befreit,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,gruppe,vertriebid,bearbeiterid,provision,provision_summe,typ,
            firma,sprache,anzeigesteuer,waehrung,kurs,kostenstelle FROM rechnung WHERE id='$id' LIMIT 1");
    $arr['kundennummer'] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '".$arr['adresse']."' LIMIT 1");
    $arr['bundesstaat'] = $this->app->DB->Select("SELECT bundesstaat FROM rechnung WHERE id='$id' LIMIT 1");
    $this->app->DB->UpdateArr('rechnung',$newid,'id',$arr, true);
    
    $pos = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung='$id'");
    $cpos = !empty($pos)?count($pos):0;
    for($i=0;$i<$cpos;$i++){
      $this->app->DB->Insert("INSERT INTO rechnung_position (rechnung) VALUES($newid)");
      $newposid = $this->app->DB->GetInsertID();
      $pos[$i]['rechnung']=$newid;
      $this->app->DB->UpdateArr('rechnung_position',$newposid,'id',$pos[$i], true);
      if($pos[$i]['steuersatz'] === null){
        $this->app->DB->Update("UPDATE rechnung_position SET steuersatz = null WHERE id = '$newposid' LIMIT 1");
      }
    }
    $this->app->erp->CheckFreifelder('rechnung',$newid);
    $this->app->erp->CopyBelegZwischenpositionen('rechnung',$id,'rechnung',$newid);
    $this->app->erp->LoadSteuersaetzeWaehrung($newid,'rechnung');
    $this->app->erp->SchnellFreigabe('rechnung',$newid);
    
    return $newid;
  }

  /**
   * @param int $id
   * @param int $adresse
   */
  public function LoadRechnungStandardwerte($id,$adresse)
  {
    if($id==0 || $id=='' || $adresse=='' || $adresse=='0'){
      return;
    }
    $this->app->erp->StartChangeLog('rechnung', $id);
    // standard adresse von lieferant
    $arr = $this->app->DB->SelectArr("SELECT *,vertrieb as vertriebid,'' as bearbeiter,innendienst as bearbeiterid FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

    if($arr[0]['bearbeiterid'] <=0 ){
      $arr[0]['bearbeiterid'] = $this->app->User->GetAdresse();
    }

    $arr[0]['gruppe'] = $this->app->erp->GetVerband($adresse);

    $rolle_projekt = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE adresse='$adresse' AND subjekt='Kunde' AND objekt='Projekt' AND (bis ='0000-00-00' OR bis <= NOW()) LIMIT 1");

    if($arr[0]['abweichende_rechnungsadresse']=='1')
    {
      $arr = $this->app->DB->SelectArr("SELECT projekt, rechnung_name as name,
              rechnung_abteilung as abteilung,
              rechnung_ansprechpartner as ansprechpartner,
              rechnung_unterabteilung as unterabteilung,
              rechnung_strasse as strasse,
              rechnung_adresszusatz as adresszusatz,
              rechnung_plz as plz,
              rechnung_ort as ort,
              rechnung_land as land,
              rechnung_telefon as telefon,
              rechnung_titel as titel,
              rechnung_email as email,
              rechnung_telefax as telefax,
              rechnung_vorname as vorname,
              rechnung_typ as typ,
              rechnung_bundesstaat as bundesstaat,
              rechnung_gln as gln,
              ustid,
              kundennummer,
              lieferbedingung,
              vertrieb as vertriebid,
              innendienst as bearbeiterid,
              '' as bearbeiter,
              rechnung_anschreiben as anschreiben
              FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

      $arr[0]['gruppe'] = $this->app->erp->GetVerband($adresse);
    }
    $field = array('gln','anschreiben','name','abteilung','typ','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit','gruppe','vertriebid','bearbeiter','ansprechpartner','bearbeiterid','titel','lieferbedingung','bundesstaat');

    if($rolle_projekt > 0)
    {
      $arr[0]['projekt'] = $rolle_projekt;
    }

    foreach($field as $key=>$value)
    {
      if($value=="projekt" && $this->app->Secure->POST[$value]!=""&&0)
      {
        $uparr[$value] = str_replace("'", '&apos;',$this->app->Secure->POST[$value]);
      } else {
        $this->app->Secure->POST[$value] = str_replace("'", '&apos;',$arr[0][$value]);
        $uparr[$value] = str_replace("'", '&apos;',$arr[0][$value]);
      }

      //$this->app->Secure->POST[$value] = $arr[0][$value];
      //$uparr[$value] = $arr[0][$value];
    }

    $uparr['adresse'] = $adresse;
    $uparr['ust_befreit'] = $this->app->erp->AdresseUSTCheck($adresse);
    $uparr['zahlungsstatusstatus']='offen';

    if($this->app->erp->Firmendaten('rechnung_ohnebriefpapier')=='1'){
      $uparr['ohne_briefpapier'] = '1';
    }

    $this->app->DB->UpdateArr('rechnung',$id,'id',$uparr,true);
    $uparr=null;

    //liefernantenvorlage
    $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");

    if($arr[0]['abweichende_rechnungsadresse']=='1')
    {
      $arr = $this->app->DB->SelectArr("SELECT projekt, rechnung_name as name,
              rechnung_abteilung as abteilung,
              rechnung_unterabteilung as unterabteilung,
              rechnung_strasse as strasse,
              rechnung_adresszusatz as adresszusatz,
              rechnung_plz as plz,
              rechnung_ort as ort,
              rechnung_land as land,
              rechnung_telefon as telefon,
              rechnung_telefax as telefax,
              rechnung_vorname as vorname,
              rechnung_typ as typ,
              zahlungsweise,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,versandart,
              rechnung_anschreiben as anschreiben
              FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    }

    $field = array('zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');

    $this->app->erp->LoadZahlungsweise($adresse,$arr);

    // falls von Benutzer projekt ueberladen werden soll
    $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    if($projekt_bevorzugt=="1")
    {
      $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      $arr[0]['projekt'] = $uparr['projekt'];
      $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr[0]['projekt']."' AND id > 0 LIMIT 1");
    }
    if(isset($arr[0]['usereditid'])){
      unset($arr[0]['usereditid']);
    }
    $this->app->Secure->POST['zahlungsweise'] = strtolower($arr[0]['zahlungsweise']);
    $this->app->Secure->POST['zahlungszieltage'] = strtolower($arr[0]['zahlungszieltage']);
    $this->app->Secure->POST['zahlungszieltageskonto'] = strtolower($arr[0]['zahlungszieltageskonto']);
    $this->app->Secure->POST['zahlungszielskonto'] = strtolower($arr[0]['zahlungszielskonto']);
    $this->app->Secure->POST['versandart'] = strtolower($arr[0]['versandart']);
    /*
       foreach($field as $key=>$value)
       {
       $uparr[$value] = $arr[0][$value];
       $this->app->Secure->POST[$value] = $arr[0][$value];
       }
     */
    $this->app->DB->UpdateArr('rechnung',$id,'id',$arr[0],true);
    $this->app->erp->WriteChangeLog();
    $this->app->erp->LoadSteuersaetzeWaehrung($id,'rechnung');
    $this->app->erp->StartChangeLog('rechnung', $id);
    $this->app->erp->LoadAdresseStandard('rechnung',$id,$adresse);
    $this->app->erp->WriteChangeLog();
  }

  /**
   * @param int $id
   */
  public function DeleteRechnung($id)
  {
    if($id <= 0) {
      return;
    }
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
    if($belegnr=='' || $belegnr=='0') {
      $this->app->DB->Delete("DELETE FROM rechnung_position WHERE rechnung='$id'");
      $this->app->DB->Delete("DELETE FROM rechnung_protokoll WHERE rechnung='$id'");
      $this->app->DB->Delete("DELETE FROM rechnung WHERE id='$id' LIMIT 1");
    }
  }

  /**
   * @param int    $rechnung
   * @param int    $verkauf
   * @param float  $menge
   * @param string $datum
   */
  public function AddRechnungPosition($rechnung, $verkauf,$menge,$datum)
  {
    $artikel = 0;
    $preis = 0;
    $projekt = 0;
    $waehrung = 0;
    $vpe = '';
    if($verkauf){
      $verkaufspreisearr = $this->app->SelectRow("SELECT * FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
    }
    if(!empty($verkaufspreisearr)) {
      $artikel = $verkaufspreisearr['artikel'];
      $preis = $verkaufspreisearr['preis'];
      $projekt = $verkaufspreisearr['projekt'];
      $waehrung = $verkaufspreisearr['waehrung'];
      $vpe = $verkaufspreisearr['vpe'];
    }
    if($artikel > 0){
      $artikelarr = $this->app->SelectRow("SELECT * FROM artikel WHERE id='$artikel' LIMIT 1");
    }
    $bezeichnunglieferant = '';
    $umsatzsteuer = '';
    $bestellnummer = '';
    if(!empty($artikelarr)) {
      $bezeichnunglieferant = $artikelarr['name_de'];
      $umsatzsteuer = $artikelarr['umsatzsteuer'];
      $bestellnummer = $artikelarr['nummer'];
    }

    $sort = (int)$this->app->DB->Select("SELECT MAX(sort) FROM rechnung_position WHERE rechnung='$rechnung' LIMIT 1");
    $sort++;
    $this->app->DB->Insert("INSERT INTO rechnung_position (rechnung,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe)
            VALUES ('$rechnung','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe')");
  }

  /**
   * @param int    $rechnung
   * @param int    $artikel
   * @param float  $preis
   * @param float  $menge
   * @param string $bezeichnung
   * @param string $beschreibung
   * @param string $waehrung
   * @param int    $rabatt
   *
   * @return int
   */
  public function AddRechnungPositionManuell($rechnung, $artikel,$preis, $menge,$bezeichnung,$beschreibung='',$waehrung='EUR',$rabatt=0)
  {
    $bezeichnung = $this->app->DB->real_escape_string($bezeichnung);
    $beschreibung = $this->app->DB->real_escape_string($beschreibung);

    $bezeichnunglieferant = $bezeichnung;
    $artArr = $this->app->DB->SelectRow(
      sprintf(
        'SELECT nummer, projekt, umsatzsteuer FROM artikel WHERE id = %d',
        $artikel
      )
    );
    $bestellnummer = !empty($artArr)?$artArr['nummer']: $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
    $projekt = !empty($artArr)?$artArr['projekt']:$this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$artikel' LIMIT 1");

    $bestellnummer = $this->app->DB->real_escape_string($bestellnummer);

    if($waehrung!='') {
      $this->app->DB->Update("UPDATE rechnung SET waehrung='$waehrung' WHERE id='$rechnung' AND waehrung='' LIMIT 1");
    }

    $keinrabatterlaubt=0;
    if($rabatt <> 0) {
      $keinrabatterlaubt=1;
    }

    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
    $umsatzsteuer = !empty($artArr)?$artArr['umsatzsteuer']:$this->app->DB->Select("SELECT umsatzsteuer  FROM artikel WHERE id='$artikel' LIMIT 1");
    $umsatzsteuer = $this->app->DB->real_escape_string($umsatzsteuer);
    $vpe = '';
    //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
    $sort = (int)$this->app->DB->Select("SELECT MAX(sort) FROM rechnung_position WHERE rechnung='$rechnung' LIMIT 1");
    $sort++;
    $this->app->DB->Insert("INSERT INTO rechnung_position (rechnung,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,beschreibung,rabatt,keinrabatterlaubt)
            VALUES ('$rechnung','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe','$beschreibung','$rabatt','$keinrabatterlaubt')");

    return $this->app->DB->GetInsertID();
  }
}
