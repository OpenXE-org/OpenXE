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
include '_gen/drucker.php';

class Drucker extends GenDrucker {
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
    $id = $this->app->Secure->GetGET('id');
    switch ($name){
      case 'druckerlist':
        $allowed['drucker'] = array('list');

        // START EXTRA checkboxen

        // ENDE EXTRA checkboxen


        // headings

        $heading = array('Name', 'Bezeichnung', 'Anbindung', 'Aktiv', 'Men&uuml;');
        $width = array('30%', '30%', '20%', '10%', '10%');
        $findcols = array('d.name', 'd.bezeichnung', 'd.anbindung', "if(d.aktiv,'ja','nein')", 'd.id');
        $searchsql = array('d.name', 'd.bezeichnung', 'd.anbindung', 'd.aktiv');
        $defaultorder = 2; // sortiert nach dem oeffnen nach spalte 2

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=drucker&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" ."<a href=\"index.php?module=drucker&action=testseite&id=%value%\" title=\"Testseite drucken\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=drucker&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS d.id, d.name, d.bezeichnung, 
                                           CONCAT(d.anbindung,if(d.adapterboxseriennummer='','',' SN:'),d.adapterboxseriennummer), if(d.aktiv,'ja','nein') as aktiv, 
                                           d.id FROM drucker d ";
        $where = '';//d.firma='" . $this->app->User->GetFirma() . "'";

        //$groupby=" GROUP by z.adresse_abrechnung ";

        // gesamt anzahl

        $count = 'SELECT COUNT(id) FROM drucker';
        break;
      case 'drucker_spooler':
        $allowed['drucker'] = array('spooler');

        // START EXTRA checkboxen

        // ENDE EXTRA checkboxen


        // headings

        $heading = array('Zeit', 'Dateiname', 'Bearbeiter','Gedruckt','Men&uuml;');
        $width = array('30%', '30%', '20%', '10%','5%');
        $findcols = array("d.zeitstempel", 'd.filename', 'a.name','d.gedruckt','d.id');
        $searchsql = array("DATE_FORMAT(d.zeitstempel,'%d.%m.%Y %H:%i:%s')", 'd.filename', 'a.name');
//        $defaultorder = 2; // sortiert nach dem oeffnen nach spalte 2

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $datecols = array(0);

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=drucker&action=spoolerdownload&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=drucker&action=spoolerdelete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS d.id, DATE_FORMAT(d.zeitstempel,'%d.%m.%Y %H:%i:%s'), if(d.filename!='',d.filename,'Kein Dateiname vorhanden'), a.name,if(d.gedruckt=1,'ja','') as gedruckt,
                                           d.id FROM drucker_spooler d LEFT JOIN user u ON u.id=d.user LEFT JOIN adresse a ON a.id=u.adresse ";
        $where = " d.drucker='$id' ";//d.firma='" . $this->app->User->GetFirma() . "'";

        //$groupby=" GROUP by z.adresse_abrechnung ";

        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM drucker_spooler WHERE drucker='$id'";
        break;
    }

    $erg = [];

    foreach($erlaubtevars as $k => $v)  {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  /**
   * Drucker constructor.
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

    $this->app->ActionHandler("create","DruckerCreate");
    $this->app->ActionHandler("edit","DruckerEdit");
    $this->app->ActionHandler("delete","DruckerDelete");
    $this->app->ActionHandler("spoolerdelete","DruckerSpoolerDelete");
    $this->app->ActionHandler("spoolerdownload","DruckerSpoolerDownload");
    $this->app->ActionHandler("spoolerdownloadall","DruckerSpoolerDownloadAll");
    $this->app->ActionHandler("spoolerdownloadzip","DruckerSpoolerDownloadZip");
    $this->app->ActionHandler("spooler","DruckerSpooler");
    $this->app->ActionHandler("list","DruckerList");
    $this->app->ActionHandler("testseite","DruckerTestseite");
    $this->app->ActionHandler("spoolerleeren", "DruckerSpoolerLeeren");

    $this->app->ActionHandlerListen($app);
  }

  public function DruckerSpoolerDownloadAll()
  {
    $id = $this->app->Secure->GetGET('id');
 
    $data = $this->app->DB->SelectArr(
      "SELECT * 
      FROM drucker_spooler 
      WHERE drucker='$id' AND DATE_FORMAT(zeitstempel,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d')"
    );

    $cdata = !empty($data)?count($data):0;
    for($i=0;$i<$cdata;$i++) {
      $temp = tempnam($this->app->erp->GetTMP(),'');
      file_put_contents($temp,base64_decode($data[$i]['content']));
      $files[] = $temp;
    }
    $raw_data =  $this->app->erp->MergePDF($files);

    $cfiles = !empty($files)?count($files):0;
    for($i=0;$i<$cfiles;$i++) {
      unlink($files[$i]);
    }

    $filename = urlencode('DOWNLOAD_SPOOLER.pdf');

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Type: application/force-download");
    header('Content-Disposition: attachment; filename=' . $filename);
    // header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . strlen($raw_data));
    echo $raw_data;
    $this->app->ExitXentral();
  }

  public function DruckerTestseite()
  {
    $id = $this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd');

    $format = $this->app->DB->Select(sprintf('SELECT format FROM drucker WHERE id=%d', $id));
    switch($format)
    {
      case 'DINA4': $format='A4'; break;
      case 'DINA5': $format='A5'; break;
      case 'DINA6': $format='A6'; break;
      default: $format = 'A4';
    }

    $Brief = new SuperFPDF('P','mm',$format);
    $filename=$this->app->erp->GetTMP().'Testseite_'.$id.'.pdf';
    $Brief->AddPage();
    $Brief->SetTextColor(0);
    $Brief->SetFont(($this->app->erp->Firmendaten('schriftart')?$this->app->erp->Firmendaten('schriftart'):'Arial'),'B',20);
    $Brief->Text(10,10,'XENTRAL TESTSEITE');
    $Brief->SetTextColor(0);
    $Brief->Output($filename,'F');
    $this->app->printer->Drucken($id,$filename);
    unlink($filename);

    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Druckauftrag für Testseite versendet!</div>  ");

    if($cmd==='intern')
    {
      $this->app->Location->execute("index.php?module=drucker&action=edit&id=$id&msg=$msg");
    } else {
      $this->app->Location->execute("index.php?module=drucker&action=list&msg=$msg");
    }
  }


  /**
   * @param string $modul
   * @param int $id
   *
   * @return null|TransferBase
   */
  public function loadPrinterModul($modul, $id)
  {
    $selAnbindung = [
      'cups' => 'Kommandozeilenbefehl',
      'pdf' => 'PDF in Verzeichnis',
      'adapterbox' => 'Adapterbox',
      'email' => 'E-Mail',
      'download' => 'Download',
      'spooler' => 'Xentral Druckerspooler',
    ];
    if(empty($modul) || !empty($selAnbindung[$modul])) {
      return null;
    }

    $id = (int)$id;
    if(empty($modul) ||
      strpos($modul,'..') !== false ||
      (!@is_file(dirname(__DIR__).'/lib/Printer/'.$modul.'.php') && !@is_file(dirname(__DIR__).'/lib/Printer/'.$modul.'/'.$modul.'.php'))
    ) {
      return null;
    }
    $class_name = $modul;
    $class_name_custom = $modul.'Custom';
    if(!class_exists($class_name)) {
      if(@is_file(dirname(__DIR__).'/lib/Printer/'.$modul.'_custom.php')) {
        include_once dirname(__DIR__).'/lib/Printer/'.$modul.'_custom.php';
      }
      elseif(@is_file(dirname(__DIR__).'/lib/Printer/'.$modul.'/'.$modul.'_custom.php')) {
        include_once dirname(__DIR__).'/lib/Printer/'.$modul.'/'.$modul.'_custom.php';
      }
      elseif(is_file(dirname(__DIR__).'/lib/Printer/'.$modul.'/'.$modul.'.php')) {
        include_once dirname(__DIR__).'/lib/Printer/'.$modul.'/'.$modul.'.php';
      }
      elseif(is_file(dirname(__DIR__).'/lib/Printer/'.$modul.'.php')) {
        include_once dirname(__DIR__).'/lib/Printer/'.$modul.'.php';
      }
      else {
        return null;
      }
    }
    if(class_exists($class_name_custom)) {
      $obj = new $class_name_custom($this->app, $id);
      if(method_exists($obj,'getPrinterModul')) {
        $obj->getPrinterModul($this);
      }
      return $obj;
    }
    if(class_exists($class_name)) {
      $obj = new $class_name($this->app, $id);
      if(method_exists($obj,'getPrinterModul')) {
        $obj->getPrinterModul($this);
      }
      return $obj;
    }

    return null;
  }

  /**
   * @param string $value
   * @param bool   $retarr
   *
   * @return string|null
   */
  public function PrinterSelModul($value = '', $retarr = false)
  {
    $array = null;
    $ret = '';
    $pfad = dirname(__DIR__).'/lib/Printer';
    if(is_dir($pfad)) {
      $handle = opendir($pfad);
      if($handle) {
        while (false !== ($file = readdir($handle))) {
          $files[] = $file;
        }
        natcasesort($files);
        foreach($files as $file)  {
          if($file[0] === '.' || substr($file,-8) === '.src.php' || substr($file,-11) === '_custom.php') {
            continue;
          }
          if((substr($file,-4) === '.php'  && is_file($pfad.'/'.$file)) ||
            (is_dir($pfad.'/'.$file) && is_file($pfad.'/'.$file.'/'.$file.'.php'))
          ) {
            $modul = str_replace('.php','',$file);
            $array[$modul] = substr($modul,8);
            if(!class_exists($modul)) {
              include_once $pfad.'/'.$file.'/'.$file.'.php';
            }
            if(class_exists($modul) && method_exists($modul,'getName')) {
              $array[$modul] = $modul::getName();
            }
            $ret .= '<option value="'.$modul.'"'.($value == $modul?' selected="selected" ':'').'>'.$array[$modul].'</option>';
          }
        }
        closedir($handle);
      }
    }
    if($retarr){
      return $array;
    }

    return $ret;
  }

  public function DruckerSpoolerDownloadZip()
  {
    $id = $this->app->Secure->GetGET('id');
 
    $data = $this->app->DB->SelectArr(
      "SELECT * 
      FROM drucker_spooler 
      WHERE drucker='$id' AND DATE_FORMAT(zeitstempel,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d')"
    );


    $dateinamezip = 'DOWNLOAD_SPOOLER_'.date('Y-m-d').'.zip';

    $zip = new ZipArchive;
    $zip->open($dateinamezip, ZipArchive::CREATE);
    $cdata = !empty($data)?count($data):0;
    for($i=0;$i<$cdata;$i++) {
      $zip->addFromString($data[$i]['zeitstempel'].' '.$data[$i]['filename'], base64_decode($data[$i]['content']));
    }
    $zip->close();

    // download
    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename=$dateinamezip");
    header('Content-Length: ' . filesize($dateinamezip));

    readfile($dateinamezip);
    unlink($dateinamezip);
    $this->app->ExitXentral();
  }


  public function DruckerSpoolerDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $drucker = $this->app->DB->Select(sprintf('SELECT drucker FROM drucker_spooler WHERE id=%d', $id));
      $this->app->DB->Delete(sprintf('DELETE FROM drucker_spooler WHERE id=%d', $id));
    }
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Eintrag wurde aus dem Spooler entfernt!</div>  ");
    $this->app->Location->execute("index.php?module=drucker&action=spooler&id=$drucker&msg=$msg");
  }

  public function DruckerSpoolerDownload()
  {
    $id = $this->app->Secure->GetGET('id');
    $data = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM drucker_spooler WHERE id=%d',
        $id
      )
    );

    $raw_data = empty($data)?'': base64_decode($data['content']);
    if(empty($data)) {
      $filename = '';
    }
    elseif($data['filename']!=''){
      $filename = urlencode($data['zeitstempel'] . $data['filename']);
    }
    else{
      $filename = urlencode($data['zeitstempel']);
    }

    $this->app->DB->Update(
      sprintf(
        'UPDATE drucker_spooler SET gedruckt=1 WHERE id=%d',
        $id
      )
    );

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Type: application/force-download");
    header('Content-Disposition: attachment; filename=' . $filename);
    // header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . strlen($raw_data));
    echo $raw_data;
    $this->app->ExitXentral();
  }


  public function DruckerCreate()
  {
    $this->DruckerMenu();
    parent::DruckerCreate();
  }

  public function DruckerList()
  {
    $this->DruckerMenu();
    $this->app->YUI->TableSearch('TAB1','druckerlist', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function DruckerDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    if(is_numeric($id) && $id > 0) {
      $this->app->DB->Delete(
        sprintf(
          'DELETE FROM drucker WHERE id=%d',
          $id
        )
      );
    }
    $this->app->Location->execute('index.php?module=drucker&action=list');
  }

  public function DruckerSpooler()
  {
    $this->DruckerMenu();
    $id = $this->app->Secure->GetGET('id');

    $this->app->Tpl->Set(
      'TAB1',
      "<div class=\"info\">Hinweis: Die Druckeraufträge werden automatisch nach 10 Tagen gelöscht.</div>"
    );

    $this->app->YUI->TableSearch('TAB1','drucker_spooler', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Add('TAB1',"<center>
        <input type=\"button\" onclick=\"window.location.href='index.php?module=drucker&action=spoolerdownloadall&id=$id'\" 
      value=\"Sammel PDF (Heute)\">&nbsp;
        <input type=\"button\" onclick=\"window.location.href='index.php?module=drucker&action=spoolerdownloadzip&id=$id'\" 
      value=\"ZIP-Archiv (Heute)\">&nbsp;
      <input type=\"button\" onclick=\"if(!confirm('Wirklich leeren?')) return false; else window.location.href='index.php?module=drucker&action=spoolerleeren&id=$id'\"; value=\"Spooler leeren\"></center>");
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function DruckerMenu()
  {
    $id = $this->app->Secure->GetGET('id');
    $name = $id <=0?'':(String)$this->app->DB->Select(
      sprintf(
        "SELECT CONCAT(name,' ',bezeichnung) FROM drucker WHERE id=%d LIMIT 1",
        $id
      )
    );
    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',$name);
    $action = $this->app->Secure->GetGET('action');

    if($action==='edit' || $action==='spooler') {
      $this->app->erp->MenuEintrag("index.php?module=drucker&action=edit&id=$id",'Details');
    	$this->app->erp->MenuEintrag("index.php?module=drucker&action=spooler&id=$id",'Spooler');
		}

    if($action==='create'){
      $this->app->erp->MenuEintrag('index.php?module=drucker&action=create', 'Details');
    }

    if($action==='list'){
      $this->app->erp->MenuEintrag('index.php?module=drucker&action=list', '&Uuml;bersicht');
    }
    $this->app->erp->MenuEintrag('index.php?module=drucker&action=create','Neu');
    if($action==='list'){
      $this->app->erp->MenuEintrag('index.php?module=einstellungen&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    else{
      $this->app->erp->MenuEintrag('index.php?module=drucker&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
  }

  public function DruckerEdit()
  {
    $this->DruckerMenu();
    $id = $this->app->Secure->GetGET('id');

    $this->app->Tpl->Set(
      'TESTSEITE',
      "<input type=\"button\" value=\"Testseite drucken\" onclick=\"window.location.href='index.php?module=drucker&action=testseite&cmd=intern&id=$id'\">"
    );

    parent::DruckerEdit();
  }

  public function DruckerSpoolerLeeren()
  {
    $druckerid = $this->app->Secure->GetGET('id');
    $druckerid = $druckerid <= 0?0:(int)$this->app->DB->Select(
      sprintf(
        "SELECT id FROM drucker WHERE id = %d LIMIT 1",
        $druckerid
      )
    );

    if($druckerid <= 0) {
      $this->app->Location->execute('index.php?module=drucker&action=list');
    }

    $this->app->DB->Delete(
      sprintf(
        'DELETE FROM drucker_spooler WHERE drucker = %d',
        $druckerid
      )
    );

    $this->app->Location->execute('index.php?module=drucker&action=spooler&id='.$druckerid);
  }
}
