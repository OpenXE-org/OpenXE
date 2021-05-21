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
class Firmendaten  {
  /** @var Application */
  var $app;
  /** @var Xentral\Components\Template\Template $tmpl */
  protected $tmpl;
  /** @var array */
  protected $payments;

  const MODULE_NAME = 'Firmendaten';

  public $javascript = [
    './classes/Modules/Company/www/js/companydata.js',
  ];

  public $stylesheet = [
    './classes/Modules/Company/www/css/companydata.css',
  ];

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
      case 'company_document_setting':
        $allowed['firmendaten'] = array('documentsettings');
        $documents = $this->getDocuments();
        $colDocument = '';
        foreach($documents as $documentKey => $document) {
          $colDocument .= sprintf("IF(dci.doctype = '%s', '%s',", $documentKey, $document);
        }
        $colDocument .= 'dci.doctype'. str_repeat(')', count($documents));
        // headings
        $heading = array('', 'Belegart', 'zus&auml;tzliche Sprachen', 'Projekt', 'Aktiv','', 'Men&uuml;');
        $width = array('1%', '10%', '20%', '15%', '5%', '1%', '1%');
        $findcols = array('dcit.id', $colDocument, 'translations', 'project', 'dci.active','color', 'dci.id');
        $searchsql = array('dcit.language_code','pr.abkuerzung');
        $menu = "<table>";
          $menu .= "<tr>";
            $menu .= "<td nowrap>";
              $menu .= '<a href="javascript:;" class="companydocument-edit" data-companydocument-id="%value%">';
                $menu .= "<img alt=\"ändern\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
              $menu .= "</a>&nbsp;";
              $menu .= '<a href="javascript:;" class="companydocument-copy" data-companydocument-id="%value%">';
                $menu .= "<img alt=\"kopieren\" data-id=\"%value%\" class=\"copy\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
              $menu .= "</a>&nbsp;";
              $menu .= '<a href="javascript:;" class="companydocument-delete" data-companydocument-id="%value%">';
                $menu .= "<img alt=\"löschen\" data-id=\"%value%\" class=\"delete\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
              $menu .= "</a>&nbsp;";
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        $trcol = 5;
        // SQL statement
        $sql = "SELECT  dci.id, 
       CONCAT('<input type=\"checkbox\" name=\"selection[]\" value=\"',dci.id,'\" />'),
       $colDocument, 
       
       GROUP_CONCAT(IF(dcit.active = 1,dcit.language_code,CONCAT('<span class=\"grey\">',dcit.language_code,'</span>')) ORDER BY dcit.language_code SEPARATOR ',')
       AS translations
           , pr.abkuerzung as project, IF(dci.active = 1, 'ja','-'),IF(dci.active = 1 AND IFNULL(cp.count_elements,0) > 1,'#F1B19F','')  AS color ,dci.id
        FROM document_customization_infoblock AS dci
        LEFT JOIN document_customization_infoblock_translation AS dcit ON dci.id = dcit.document_customization_infoblock_id
        LEFT JOIN (
            SELECT doctype, project_id, COUNT(id) AS count_elements 
            FROM document_customization_infoblock 
            WHERE active = 1
            GROUP BY doctype, project_id
        ) AS cp ON dci.doctype = cp.doctype AND dci.project_id = cp.project_id
        LEFT JOIN projekt AS pr ON dci.project_id = pr.id";

        // fester filter
        $where = '';
        $groupby = 'GROUP BY dci.id';
        $count = 'SELECT COUNT(id) FROM document_customization_infoblock';
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
   * Firmendaten constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    $this->payments = [
      'eckarte' => 'EC-Karte',
      'ratenzahlung' => 'Ratenzahlung',
      'sofortueberweisung' => 'Sofort&uuml;berweisung',
      'secupay' => 'Secupay',
      'amazon' => 'Amazon',
      'kreditkarte' => 'Kreditkarte',
      'paypal' => 'Paypal',
      'bar' => 'Barzahlung',
      'lastschrift' => 'Lastschrift',
      'vorkasse' => 'Vorkasse',
      'nachnahme' => 'Nachnahme'
    ];
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("edit","FirmendatenEdit");
    $this->app->ActionHandler("briefpapier","FirmendatenBriefpapierDownload");
    $this->app->ActionHandler("logo","FirmendatenLogoDownload");
    $this->app->ActionHandler("nextnumber","FirmendatenNextNumber");
    $this->app->ActionHandler("layout","FirmendatenLayout");
    $this->app->ActionHandler("briefpapiervorlage","FirmendatenBriefpapiervorlage");
    $this->app->ActionHandler("testmail","FirmendatenTestmail");
    $this->app->ActionHandler("documentsettings","documentSettings");

    $this->app->ActionHandlerListen($app);

    $this->app->Tpl->Set('FARBE',"[FARBE5]");
  }

  /**
   * @param int $color
   *
   * @return int
   */
  function ColorOK($color)
  {
    $r = $color % 256;
    $color = (int)floor($color / 256);
    $g = $color % 256;
    $color = (int)floor($color / 256);
    $b = $color % 256;
    return $r+$g+$b;
  }

  /**
   * @param string $bild
   * @param string $name
   *
   * @return string
   */
  function CheckImage($bild, $name)
  {
    $data = explode(',', $bild);
    unset($bild);
    $encodedData = str_replace(' ','+',$data[1]);
    $decodedData = base64_decode($encodedData);
    $filename = $this->app->erp->GetTmp().$name;
    if(!file_put_contents($filename, $decodedData)){
      return '';
    }
    $type = mime_content_type($filename);
    switch($type) {
      case 'image/jpg':
      case 'image/jpeg':

        break;
      case 'image/png':

        break;
      case 'image/gif':

      break;
      default:
        return '';
      break;
    }

    $palette = League\ColorExtractor\Palette::fromFilename($filename);
    if(@is_file($filename)) {
      @unlink($filename);
    }
    if($palette && count($palette) == 1) {
      $color = array_keys($palette);
      return League\ColorExtractor\Color::fromIntToHex(reset($color));
    }

// $palette is an iterator on colors sorted by pixel count
    for($offset = 150; $offset > 0; $offset -= 30) {
      $maxc = 768;
      $creturn = '';
      $i = 0;
      foreach ($palette as $color => $count) {
        $i++;
        if ($i > 100 || ($count < 10 && $i > 20)) {
          break;
        }
        $colorc = $this->ColorOK($color);
        //echo $count.' '. $colorc." ".$color." ".League\ColorExtractor\Color::fromIntToHex($color)."\r\n";
        if ($colorc < 300 && $colorc > 0 + $offset && $colorc < 765 - $offset) {
          //echo $count . ' ' . $colorc . " " . $color . " " . League\ColorExtractor\Color::fromIntToHex($color) . "\r\n";
          return League\ColorExtractor\Color::fromIntToHex($color);
        }
        if ($colorc < $maxc) {
          $maxc = $colorc;
          $creturn = $color;
        }
      }
    }
    return League\ColorExtractor\Color::fromIntToHex($creturn);
  }

  function FirmendatenMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=firmendaten&action=edit','Einstellungen');
    //$this->app->erp->MenuEintrag('index.php?module=firmendaten&action=farbwelten','Farbwelten');
    $this->app->erp->MenuEintrag('index.php?module=firmendaten&action=documentsettings','Belege Infoboxen');
  }

  /**
   * @param string $color
   *
   * @return string
   */
  function FarbeDunkel($color)
  {
    $color = strtolower(trim($color,'#'));
    $r = hexdec(substr($color, 0,2));
    $g = hexdec(substr($color, 2,2));
    $b = hexdec(substr($color, 4,2));
    $r = dechex(floor($r * 0.7));
    $g = dechex(floor($g * 0.7));
    $b = dechex(floor($b * 0.7));
    if(strlen($r) < 2) {
      $r = '0'.$r;
    }
    if(strlen($g) < 2) {
      $g = '0'.$g;
    }
    if(strlen($b) < 2) {
      $b = '0'.$b;
    }
    return '#'.strtoupper($r.$g.$b);
  }

  /**
   * @param string $color
   *
   * @return string
   */
  function CorrectColor($color)
  {
    $color = strtolower(trim($color,'#'));
    $r = hexdec(substr($color, 0,2));
    $g = hexdec(substr($color, 2,2));
    $b = hexdec(substr($color, 4,2));
    $ok =$this->ColorOK($b*65536+$g*256+$r);
    $f = 1;
    if($ok >= 765) {
      return '#DDDDDD';
    }

    if($ok > 600) {
      $f = 0.8;
    }
    $r = dechex(floor($r * $f));
    $g = dechex(floor($g * $f));
    $b = dechex(floor($b * $f));
    if(strlen($r) < 2) {
      $r = '0'.$r;
    }
    if(strlen($g) < 2) {
      $g = '0'.$g;
    }
    if(strlen($b) < 2) {
      $b = '0'.$b;
    }
    return '#'.strtoupper($r.$g.$b);
  }

  public function FirmendatenFarbwelten()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'checkimage') {
      $color = $this->CorrectColor(
        $this->CheckImage($this->app->Secure->GetPOST('bild'),
          $this->app->Secure->GetPOST('name')
        )
      );
      $color2 = $this->FarbeDunkel($color);
      header('Content-Type: application/json');
      echo json_encode(array('color'=>$color,'color2'=>$color2));
      $this->app->ExitXentral();
    }

    if($cmd === 'checkfarbe') {
      $color = $this->app->Secure->GetPOST('farbe');
      $color2 = $this->FarbeDunkel($color);
      header('Content-Type: application/json');
      echo json_encode(array('color'=>$color,'color2'=>$color2));
      $this->app->ExitXentral();
    }

    if($this->app->Secure->GetPOST('speichern')) {
      $bild = $this->app->Secure->GetPOST('bild');
      $dateiname = $this->app->Secure->GetPOST('dateiname');
      $color = $this->app->Secure->GetPOST('firmenfarbehell');
      $firmenhoherformularkontrast = (int)$this->app->Secure->GetPOST('firmenhoherformularkontrast');
      $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$firmenhoherformularkontrast' WHERE name = 'firmenhoherformularkontrast'");
      if($color != '') {
        $colordunkel = $this->FarbeDunkel($color);
        $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$colordunkel' WHERE name = 'firmenfarbedunkel'");
        $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$color' WHERE name = 'firmenfarbehell'");
        $this->app->DB->Update("UPDATE firmendaten SET firmenfarbedunkel = '$colordunkel'");
        $this->app->DB->Update("UPDATE firmendaten SET firmenfarbehell = '$color'");
      }

      if($bild != '') {
        $data = explode(',', $bild);
        unset($bild);
        $encodedData = str_replace(' ','+',$data[1]);
        unset($data);
        $this->app->DB->Update("UPDATE firmendaten SET firmenlogo = '$encodedData'");
        $this->app->DB->Update("UPDATE firmendaten_werte SET wert = 1 WHERE name = 'firmenlogoaktiv'");
        unset($encodedData);
      }

      $this->app->Location->execute('index.php?module=firmendaten&action=farbwelten');
    }

    $this->FirmendatenMenu();

    if(isset($this->app->Conf->WFtestmode) && $this->app->Conf->WFtestmode==true) {
      $logo = './themes/new/images/xentral_logo_testmode.png';
    }
    elseif(is_file('./themes/new/images/logo_cache.png')) {
      $logo = './themes/new/images/logo_cache.png';
    }
    elseif($this->app->erp->Firmendaten('firmenlogoaktiv')=='1'){
      $logo = './index.php?module=welcome&action=logo';
    }
    elseif($this->app->erp->Firmendaten('iconset_dunkel')=='1'){
      $logo = './themes/new/images/xentral_logo_white.png';
    }
    else {
      $logo = './themes/new/images/xentral_logo.png';
    }

    $this->app->Tpl->Add('DATEI',"<span id=\"profilbild\" style=\"padding:0;margin:0;height:50px;width:200px;display:inline-block;position:relative;background-repeat:no-repeat; background-image: url('".$logo."'); \"></span>");
    $this->app->Tpl->Set('FIRMENFARBEHELL', $this->app->erp->Firmendaten('firmenfarbehell'));
    $this->app->Tpl->Set('FIRMENHOHERFORMULARKONSTRAST', $this->app->erp->Firmendaten('firmenhoherformularkontrast')?' checked="checked" ':'');
    $this->app->YUI->ColorPicker('firmenfarbehell');
    $themes = array(
      'Gelb'=>array('image'=>'./images/farbwelten/farbwelt_gelb.png','color'=>'#E0C701','hoherkontrast'=>0)
      ,'Orange'=>array('image'=>'./images/farbwelten/farbwelt_orange.png','color'=>'#D4852E','hoherkontrast'=>0)
      ,'Rot'=>array('image'=>'./images/farbwelten/farbwelt_rot.png','color'=>'#B42927','hoherkontrast'=>0)
      ,'Rosa'=>array('image'=>'./images/farbwelten/farbwelt_rosa.png','color'=>'#CC9999','hoherkontrast'=>0)
      ,'Lila'=>array('image'=>'./images/farbwelten/farbwelt_lila.png','color'=>'#9E7098','hoherkontrast'=>0)
      ,'Blau (taubenblau)'=>array('image'=>'./images/farbwelten/farbwelt_taubenblau.png','color'=>'#608CB5', 'hoherkontrast'=>0)
      ,'Blau (beerenblau)'=>array('image'=>'./images/farbwelten/farbwelt_beerenblau.png','color'=>'#6579A5','hoherkontrast'=>0)
      ,'T&uuml;rkis'=>array('image'=>'./images/farbwelten/farbwelt_tuerkis.png','color'=>'#42B8C4','hoherkontrast'=>0)
      ,'Blau (dunkel)'=>array('image'=>'./images/farbwelten/farbwelt_dunkelblau.png','color'=>'#01367C','hoherkontrast'=>0)
      ,'Gr&uuml;n (hell)'=>array('image'=>'./images/farbwelten/farbwelt_hellgruen.png','color'=>'#7AB31E','hoherkontrast'=>0)
      ,'Gr&uuml;n (dunkel)'=>array('image'=>'./images/farbwelten/farbwelt_dunkelgruen.png','color'=>'#405E10','hoherkontrast'=>0)
      ,'Grau'=>array('image'=>'./images/farbwelten/farbwelt_grau.png','color'=>'#999999','hoherkontrast'=>0)
      ,'Schwarz'=>array('image'=>'./images/farbwelten/farbwelt_schwarz.png','color'=>'#404040','hoherkontrast'=>1)
    );

    $counter = 0;

    $this->app->Tpl->Add('THEMESVORSCHAU',
          '<div class="row">
            <div class="row-height">');

    foreach($themes as $k => $v) {
      if($counter%4 == 0){
        $this->app->Tpl->Add('THEMESVORSCHAU',
          '</div>
          </div>');

        $this->app->Tpl->Add('THEMESVORSCHAU',
          '<div class="row">
            <div class="row-height">');
      }

      $this->app->Tpl->Add('THEMESVORSCHAU',
        '<div class="col-xs-12 col-md-3 col-md-height">
          <div class="inside inside-full-height">
            <fieldset>
              <legend>'.$k.'</legend> 
              <img src="'.$v['image'].'" onclick="$(\'#firmenfarbehell\').val(\''.$v['color'].'\');$(\'#firmenhoherformularkontrast\').prop(\'checked\','.($v['hoherkontrast']?'true':'false').');$(\'#firmenfarbehell\').trigger(\'change\');" />
            </fieldset> 

          </div>
        </div>');     

      $counter += 1;
    }

    $this->app->Tpl->Add('THEMESVORSCHAU',
          '</div>
          </div>');

    $this->app->erp->Headlines('Grundeinstellungen', 'Farbwelten');
    $this->app->Tpl->Parse('PAGE','firmendaten_farbwelten.tpl');
  }

  public function FirmendatenNextNumber()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $nummer = $this->app->Secure->GetGET('nummer');
    $allowedNumbers = [
      'angebot',
      'auftrag',
      'rechnung',
      'lieferschein',
      'retoure',
      'gutschrift',
      'bestellung',
      'arbeitsnachweis',
      'reisekosten',
      'proformarechnung',
      'produktion',
      'anfrage',
      'kundennummer',
      'projektnummer',
      'lieferantennummer',
      'mitarbeiternummer',
      'artikelnummer',
      'preisanfrage',
      'verbindlichkeit',
      'receiptdocument',
    ];
    if(in_array($cmd, $allowedNumbers)) {
      $this->app->erp->FirmendatenSet('next_'.$cmd, $nummer);
    }
    $this->app->Location->execute('index.php?module=firmendaten&action=edit#tabs-6');
  }


  public function FirmendatenTestmail()
  {
    if($this->app->erp->Firmendaten('testmailempfaenger')=='') {
      $msg = $this->app->erp->base64_url_encode(
        '<div class="error">Die Testmail wurde nicht versendet, da das Feld "Testmail Empfänger" keinen Wert hat! Bitte tragen Sie dort z.B. Ihre E-Mailadresse ein.</div>'
      );
    }
    else if(
      $this->app->erp->MailSend(
        $this->app->erp->Firmendaten('email'),
        $this->app->erp->Firmendaten('absendername'),
        $this->app->erp->Firmendaten('testmailempfaenger'),
        'Testmail Empfaenger',
        'Xentral ERP: Testmail',
        'Dies ist eine Testmail',
        '',0,true,'','',
        true
      )
    ) {
      $msg = $this->app->erp->base64_url_encode(
        '<div class="info">Die Testmail wurde erfolgreich versendet. '.$this->app->erp->mail_error.'</div>'
      );
    }
    else {
      $msg = $this->app->erp->base64_url_encode(
        '<div class="error">Die Testmail wurde nicht versendet: '.$this->app->erp->mail_error.'</div>'
      );
    }
    $this->app->Location->execute("index.php?module=firmendaten&action=edit&msg=$msg#tabs-4");
  }

  public function FirmendatenLogoDownload()
  {
    $id = $this->app->DB->Select("SELECT MAX(f.id) FROM firma f INNER JOIN firmendaten fd ON f.id = fd.firma LIMIT 1");
    if(!$id) {
      $id  = $this->app->DB->Select("SELECT MAX(firma) FROM firmendaten LIMIT 1");
    }
    $logo = $this->app->DB->Select("SELECT logo FROM firmendaten WHERE firma='$id'");
    $logo_type = $this->app->erp->Firmendaten('logo_type');
    $endung = str_replace('image/','',$logo_type);

    header('Content-type: '.$logo_type);
    header('Content-Disposition: attachment; filename="logo.'.$endung.'"');
    echo base64_decode($logo);
    $this->app->ExitXentral();
  }


  public function FirmendatenBriefpapierDownload()
  {
    $id = $this->app->DB->Select("SELECT MAX(f.id) FROM firma f INNER JOIN firmendaten fd ON f.id = fd.firma LIMIT 1");
    if(!$id) {
      $id  = $this->app->DB->Select("SELECT MAX(firma) FROM firmendaten LIMIT 1");
    }

    $cmd = $this->app->Secure->GetGET('cmd');
    switch ($cmd) {
      case 'briefpapier2':
        $briefpapier = $this->app->DB->Select("SELECT briefpapier2 FROM firmendaten WHERE firma='$id'");
        $filename = 'breifpapier2.pdf';
        break;

      case 'briefpapier1':
      default:
        $briefpapier = $this->app->DB->Select("SELECT briefpapier FROM firmendaten WHERE firma='$id'");
        $filename = 'breifpapier1.pdf';
        break;
    }

    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo base64_decode($briefpapier);
    $this->app->ExitXentral();
  }

  /**
   * @return array
   */
  protected function getPaymentMethods()
  {
    return [
      'rechnung',
      'vorkasse',
      'nachnahme',
      'lastschrift',
      'bar',
      'paypal',
      'kreditkarte',
      'amazon',
      'amazon_bestellung',
      'secupay',
      'sofortueberweisung',
      'ratenzahlung',
      'eckarte',
    ];
  }

  /**
   * @return array
   */
  protected function getPaymentmethodsToHide()
  {
    return $this->app->DB->SelectFirstCols(
      sprintf(
        "SELECT DISTINCT type 
        FROM zahlungsweisen 
        WHERE aktiv = 1 AND projekt = 0 AND type IN ('%s')",
        implode("', '", $this->getPaymentMethods())
      )
    );
  }

  public function FirmendatenEdit()
  {
    if($this->app->Secure->GetPOST('installnewpayent')) {
      $this->checkPaymentModules(true);
    }
    $this->FirmendatenMenu();
    $this->app->erp->Headlines('Grundeinstellungen','Bearbeiten');
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=firmendaten&action=edit","&Uuml;bersicht");

    $this->app->YUI->CkEditor("signatur","all");
    $this->app->YUI->CkEditor("mailgrussformel","all");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("freitext1inhalt","belege");
      $this->app->YUI->CkEditor("freitext2inhalt","belege");
    }
    
    $this->app->YUI->HideFormular('zeiterfassung_schliessen',array('unchecked'=>'zeiterfassung_schliessen','checked'=>'dummy'));

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("angebot_header","belege");
      $this->app->YUI->CkEditor("angebot_footer","belege");

      $this->app->YUI->CkEditor("auftrag_header","belege");
      $this->app->YUI->CkEditor("auftrag_footer","belege");

      $this->app->YUI->CkEditor("rechnung_header","belege");
      $this->app->YUI->CkEditor("rechnung_footer","belege");
      
      $this->app->YUI->CkEditor("lieferschein_header","belege");
      $this->app->YUI->CkEditor("lieferschein_footer","belege");
   
      $this->app->YUI->CkEditor("gutschrift_header","belege");
      $this->app->YUI->CkEditor("gutschrift_footer","belege");

      $this->app->YUI->CkEditor("bestellung_header","belege");
      $this->app->YUI->CkEditor("bestellung_footer","belege");

      $this->app->YUI->CkEditor("arbeitsnachweis_header","belege");
      $this->app->YUI->CkEditor("arbeitsnachweis_footer","belege");

      $this->app->YUI->CkEditor("provisionsgutschrift_header","belege");
      $this->app->YUI->CkEditor("provisionsgutschrift_footer","belege");

      $this->app->YUI->CkEditor("proformarechnung_header","belege");
      $this->app->YUI->CkEditor("proformarechnung_footer","belege");

      $this->app->YUI->CkEditor("eu_lieferung_vermerk","belege");
      $this->app->YUI->CkEditor("export_lieferung_vermerk","belege");

      $this->app->YUI->CkEditor("footer_reihenfolge_angebot","belege");
      $this->app->YUI->CkEditor("footer_reihenfolge_auftrag","belege");
      $this->app->YUI->CkEditor("footer_reihenfolge_rechnung","belege");
      $this->app->YUI->CkEditor("footer_reihenfolge_gutschrift","belege");
      $this->app->YUI->CkEditor("footer_reihenfolge_lieferschein","belege");
      $this->app->YUI->CkEditor("footer_reihenfolge_bestellung","belege");
      $this->app->YUI->CkEditor("footer_reihenfolge_proformarechnung","belege");
    }

    $this->app->Tpl->Set('MYSQLVERSION',$this->app->DB->Select('SELECT VERSION( ) AS mysql_version'));

    if($this->app->erp->Firmendaten("version")==''){
      $this->app->erp->FirmendatenSet("version", $this->app->erp->RevisionPlain());
    }

    $lizenz = $this->app->erp->Firmendaten("lizenz");
    $schluessel = $this->app->erp->Firmendaten("schluessel");
    if($lizenz=='' || $schluessel=='')
    {
      if(is_file('../wawision.inc.php'))
      {
        $WAWISION = [];
        include_once('../wawision.inc.php');
        $this->app->erp->FirmendatenSet('lizenz',$WAWISION['serial']);
        $this->app->erp->FirmendatenSet('schluessel',$WAWISION['authkey']);
      }
    }

    $id = $this->app->DB->Select("SELECT MAX(f.id) FROM firma f INNER JOIN firmendaten fd ON f.id = fd.firma LIMIT 1");
    if(!$id){
      $id = $this->app->DB->Select('SELECT max(id) FROM firma LIMIT 1');
    }

    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("adresse_vorlage","adresse");
    $this->app->YUI->AutoComplete("angebot_pipewiedervorlage","wiedervorlage_stages");
    $this->app->YUI->AutoComplete("group_sales", "gruppekennziffer");
    $this->app->YUI->AutoComplete("group_employee", "gruppekennziffer");

    $this->app->Tpl->Set('NEXT_ANGEBOT_MAX',$this->app->DB->Select("SELECT MAX(belegnr) FROM angebot WHERE DATE_FORMAT(datum,'%Y')=DATE_FORMAT(NOW(),'%Y')"));
    $this->app->Tpl->Set('NEXT_RECHNUNG_MAX',$this->app->DB->Select("SELECT MAX(belegnr) FROM rechnung WHERE DATE_FORMAT(datum,'%Y')=DATE_FORMAT(NOW(),'%Y')"));
    $this->app->Tpl->Set('NEXT_AUFTRAG_MAX',$this->app->DB->Select("SELECT MAX(belegnr) FROM auftrag WHERE DATE_FORMAT(datum,'%Y')=DATE_FORMAT(NOW(),'%Y')"));
    $this->app->Tpl->Set('NEXT_LIEFERSCHEIN_MAX',$this->app->DB->Select("SELECT MAX(belegnr) FROM lieferschein WHERE DATE_FORMAT(datum,'%Y')=DATE_FORMAT(NOW(),'%Y')"));
    $this->app->Tpl->Set('NEXT_GUTSCHRIFT_MAX',$this->app->DB->Select("SELECT MAX(belegnr) FROM gutschrift WHERE DATE_FORMAT(datum,'%Y')=DATE_FORMAT(NOW(),'%Y')"));
    $this->app->Tpl->Set('NEXT_BESTELLUNG_MAX',$this->app->DB->Select("SELECT MAX(belegnr) FROM bestellung WHERE DATE_FORMAT(datum,'%Y')=DATE_FORMAT(NOW(),'%Y')"));
    $this->app->Tpl->Set('NEXT_ARBEITSNACHWEIS_MAX',$this->app->DB->Select("SELECT MAX(belegnr) FROM arbeitsnachweis WHERE DATE_FORMAT(datum,'%Y')=DATE_FORMAT(NOW(),'%Y')"));

    $this->app->Tpl->Set('NEXT_KUNDENNUMMER_MAX',$this->app->DB->Select("SELECT MAX(kundennummer) FROM adresse"));
    $this->app->Tpl->Set('NEXT_LIEFERANTENNUMMER_MAX',$this->app->DB->Select("SELECT MAX(lieferantennummer) FROM adresse"));
    $this->app->Tpl->Set('NEXT_MITARBEITERNUMMER_MAX',$this->app->DB->Select("SELECT MAX(mitarbeiternummer) FROM adresse"));

    $this->app->Tpl->Set('NEXT_WAREN_MAX',$this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE typ='produkt' OR typ=''"));
    $this->app->Tpl->Set('NEXT_PRODUKTION_MAX',$this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE typ='produktion'"));
    $this->app->Tpl->Set('NEXT_SONSTIGES_MAX',$this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE typ='material' OR typ='fremdleistung' OR typ='gebuehr'"));

    $this->app->erp->checkActiveCronjob('cleaner', 'MESSAGECLEANER', false);

    // Hole Post-Daten
    $data = $this->getPostData();

    $testmail = $this->app->Secure->GetPOST("testmail");
    $submit = $this->app->Secure->GetPOST("submitFirmendaten");
    
    $submitfreifelder = $this->app->Secure->GetPOST("submitFirmendatenFreifelder");

    if($submitfreifelder != '') {
      $doubletes = [];
      $firmendaten_werte = $this->app->DB->SelectArr("SELECT * FROM firmendaten_werte");
      if($firmendaten_werte) {
        foreach($firmendaten_werte as $v) {
          $firmendaten_werte_spalten[$v['name']]['wert'] = $this->app->DB->real_escape_string($v['wert']);
          if(!empty($firmendaten_werte_spalten[$v['name']]['id'])) {
            $doubletes[$v['name']][] =  $firmendaten_werte_spalten[$v['name']]['id'];
          }
          $firmendaten_werte_spalten[$v['name']]['id'] = $this->app->DB->real_escape_string($v['id']);
        }
      }
      for($i = 1; $i <= 20; $i++) {
        $n1 = 'adressefreifeld'.$i.'typ';
        $n2 = 'adressefreifeld'.$i.'spalte';
        $v1 = $this->app->Secure->GetPOST($n1);
        $v2 = $this->app->Secure->GetPOST($n2);
        $this->app->DB->Update("UPDATE firmendaten SET 
          $n1 = '".$v1."', $n2  = '".$v2."'
          WHERE firma='$id' LIMIT 1");

        if(isset($firmendaten_werte_spalten)) {
          if(isset($firmendaten_werte_spalten[$n1]) && $firmendaten_werte_spalten[$n1]['wert'] != $v1) {
            $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v1' WHERE id = '".$firmendaten_werte_spalten[$n1]['id']."' LIMIT 1");
            if(!empty($doubletes[$n1])) {
              $this->app->DB->Delete(
                sprintf(
                  "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                  $firmendaten_werte_spalten[$n1]['id'], $this->app->DB->real_escape_string($n1),
                  implode(', ', $doubletes[$n1])
                )
              );
              unset($doubletes[$n1]);
            }
            unset($firmendaten_werte_spalten[$n1]);
          }
          if(isset($firmendaten_werte_spalten[$n2]) && $firmendaten_werte_spalten[$n2]['wert'] != $v2) {
            $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v2' WHERE id = '".$firmendaten_werte_spalten[$n2]['id']."' LIMIT 1");
            if(!empty($doubletes[$n2])) {
              $this->app->DB->Delete(
                sprintf(
                  "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                  $firmendaten_werte_spalten[$n2]['id'], $this->app->DB->real_escape_string($n2),
                  implode(', ', $doubletes[$n2])
                )
              );
              unset($doubletes[$n2]);
            }
            unset($firmendaten_werte_spalten[$n2]);
          }
        }
      }
      for($i = 1; $i <= 20; $i++) {
        $n1 = 'projektfreifeld'.$i.'typ';
        $n2 = 'projektfreifeld'.$i.'spalte';
        $v1 = $this->app->Secure->GetPOST($n1);
        $v2 = $this->app->Secure->GetPOST($n2);
        $this->app->DB->Update("UPDATE firmendaten SET 
          $n1 = '".$v1."', $n2  = '".$v2."'
          WHERE firma='$id' LIMIT 1");

        if(isset($firmendaten_werte_spalten)) {
          if(isset($firmendaten_werte_spalten[$n1]) && $firmendaten_werte_spalten[$n1]['wert'] != $v1) {
            $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v1' WHERE id = '".$firmendaten_werte_spalten[$n1]['id']."' LIMIT 1");
            unset($firmendaten_werte_spalten[$n1]);
            if(!empty($doubletes[$n1])) {
              $this->app->DB->Delete(
                sprintf(
                  "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                  $firmendaten_werte_spalten[$n1]['id'], $this->app->DB->real_escape_string($n1),
                  implode(', ', $doubletes[$n1])
                )
              );
              unset($doubletes[$n1]);
            }
          }
          if(isset($firmendaten_werte_spalten[$n2]) && $firmendaten_werte_spalten[$n2]['wert'] != $v2) {
            $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v2' WHERE id = '".$firmendaten_werte_spalten[$n2]['id']."' LIMIT 1");
            unset($firmendaten_werte_spalten[$n2]);
            if(!empty($doubletes[$n2])) {
              $this->app->DB->Delete(
                sprintf(
                  "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                  $firmendaten_werte_spalten[$n2]['id'], $this->app->DB->real_escape_string($n2),
                  implode(', ', $doubletes[$n2])
                )
              );
              unset($doubletes[$n2]);
            }
          }
        }
      }
      
      $toupdate = null;
      for($in = 1; $in <= 40; $in++) {
        $toupdate[] = 'freifeld'.$in;
      }
      for($in = 1; $in <= 20; $in++) {
        $toupdate[] = 'projektfreifeld'.$in;
      }
      for($in = 1; $in <= 20; $in++) {
        $toupdate[] = 'adressefreifeld'.$in;
      }

      $sql2a = null;
      foreach($toupdate as $v) {
        $data[$v] = $this->app->Secure->GetPOST($v);
        $sql2a[] = $v ." = '".$data[$v]."' ";
      }
      $sql2 = "UPDATE firmendaten SET ".implode(',',$sql2a)." WHERE firma = '$id' LIMIT 1";
      unset($sql2a);
      $this->app->DB->Update($sql2);
      if($this->app->DB->error()) {
        foreach($toupdate as $v) {
          $data[$v] = $this->app->Secure->GetPOST($v);
          $this->app->DB->Update("UPDATE firmendaten SET ".$v." = '".($data[$v])."'"." WHERE firma = '$id' LIMIT 1");
        }
      }
      $toupdate[] = 'parameterundfreifelder';
      for($in = 1; $in <= 40; $in++) {
        $freifeldarr = array('typ','spalte','sort','an','ab','re','gs','ls','be','pr','pa','pd');
        foreach($freifeldarr as $v) {
          $toupdate[] = 'freifeld'.$in.$v;
        }
      }
      
      for($in = 1; $in <= 20; $in++) {
        $freifeldarr = array('typ','spalte','sort');
        foreach($freifeldarr as $v)
        {
          $toupdate[] = 'adressefreifeld'.$in.$v;
        }
        $toupdate[] = 'adressefreifeld'.$in;
      }
      
      for($in = 1; $in <= 20; $in++) {
        $freifeldarr = array('sort','tabelle','breite');
        foreach($freifeldarr as $v)
        {
          $toupdate[] = 'projektfreifeld'.$in.$v;
        }
      }
      if(isset($firmendaten_werte_spalten))
      {
        foreach($toupdate as $key)
        {
          $data[$key] = $this->app->Secure->GetPOST($key);
          if(isset($firmendaten_werte_spalten[$key]) && $firmendaten_werte_spalten[$key]['wert'] != $data[$key]) {
            $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '".$data[$key]."' WHERE id = '".$firmendaten_werte_spalten[$key]['id']."' LIMIT 1");
            unset($firmendaten_werte_spalten[$key]);
            if(!empty($doubletes[$key])) {
              $this->app->DB->Delete(
                sprintf(
                  "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                  $firmendaten_werte_spalten[$key]['id'], $this->app->DB->real_escape_string($key),
                  implode(', ', $doubletes[$key])
                )
              );
              unset($doubletes[$key]);
            }
          }
        }
      }
      
    }

    if($submit!='')
    {
      $versandmail_zwischenspeichern = $this->app->erp->Firmendaten('versandmail_zwischenspeichern','', true);
      $error = '';
      $logo_error = '';
      $briefpapier_error = '';
      $firmenlogo_error = '';

      // Seite 2
      if(!empty($_FILES['firmenlogo']['tmp_name']) && $_FILES['firmenlogo']['size']>0)
      {
        $firmenlogo_error = $this->app->erp->checkImage($_FILES['firmenlogo'],0,0,0,3);
        if($firmenlogo_error=='')
        {
          if(!($firmenlogo = $this->app->erp->uploadSettigsFile($_FILES['firmenlogo'],'firmenlogo'))){
            $firmenlogo = $this->app->erp->uploadFileIntoDB($_FILES['firmenlogo']);
          }

          $this->app->DB->Update("UPDATE firmendaten SET firmenlogo='{$firmenlogo['file']}' WHERE firma='$id'");
        }
        else{
          $error .= "$firmenlogo_error<br>";
        }
      }

      if(!empty($_FILES['logo']['tmp_name']) && $_FILES['logo']['size']>0) {
        $logo_error = $this->app->erp->checkImage($_FILES['logo']);
        if($logo_error=='') {
          if(!($logo = $this->app->erp->uploadSettigsImage($_FILES['logo'],'logo'))){
            $logo = $this->app->erp->uploadImageIntoDB($_FILES['logo']);
          }
          $data['hintergrund'] = 'logo';
          $data['briefpapier2vorhanden'] = 0;
          $this->app->erp->FirmendatenSet('logo_type',$logo['type']);
          $this->app->erp->FirmendatenSet('hintergrund','logo');
          $this->app->DB->Update("UPDATE firmendaten SET logo='{$logo['image']}' WHERE firma='$id' LIMIT 1");
        }
        else{
          $error .= "$logo_error<br>";
        }
      }
      if ($data['hintergrund'] === 'logo') {
        //pruefe ob logo vorhanden
        $logo = $this->app->erp->getSettingsFile('logo');
        if(strlen($logo)<10){
          $error .= 'Geben Sie bitte ein Logo zum Hochladen an.<br>';
        }
      }

      // Seite 2
      if(!empty($_FILES['briefpapier2']['tmp_name']) && $_FILES['briefpapier2']['size']>0) {
        $briefpapier2_error = $this->app->erp->checkFile($_FILES['briefpapier2'],'application/pdf');
        if($briefpapier2_error=='') {
          if(!($briefpapier2 = $this->app->erp->uploadSettigsFile($_FILES['briefpapier2'],'briefpapier2')))
          {
            $briefpapier2 = $this->app->erp->uploadFileIntoDB($_FILES['briefpapier2']);
          }
          $data['hintergrund'] = 'briefpapier';
          $data['briefpapier2vorhanden'] = 1;
          $this->app->erp->FirmendatenSet('hintergrund','briefpapier');
          $this->app->DB->Update("UPDATE firmendaten SET briefpapier2='{$briefpapier2['file']}' WHERE firma='$id'");
        }
        else{
          $error .= "$briefpapier2_error<br>";
        }
      }

      // Seite 1
      if(!empty($_FILES['briefpapier']['tmp_name']) && $_FILES['briefpapier']['size']>0) {
        $briefpapier_error = $this->app->erp->checkFile($_FILES['briefpapier'],'application/pdf');
        if($briefpapier_error=='') {
          if(!($briefpapier = $this->app->erp->uploadSettigsFile($_FILES['briefpapier'],'briefpapier2'))){
            $briefpapier = $this->app->erp->uploadFileIntoDB($_FILES['briefpapier']);
          }
          $data['hintergrund'] = 'briefpapier';
          $this->app->erp->FirmendatenSet('briefpapier_type',$briefpapier['type']);
          $this->app->erp->FirmendatenSet('hintergrund','briefpapier');
          $this->app->DB->Update("UPDATE firmendaten SET briefpapier='{$briefpapier['file']}' WHERE firma='$id'");
        }
        else{
          $error .= "$briefpapier_error<br>";
        }
      }
      else{
        $briefpapier = $this->app->erp->getSettingsFile('briefpapier');
      }


      if(isset($data['sprachebevorzugen'])){
        $this->savePreferredLanguage($data['sprachebevorzugen']);
      }

      // Schriftarten-Upload
      if(!empty($_FILES['schriftart_upload'])){
        $fontUploadSuccessful = null;
        $fontUploadName = $this->app->Secure->GetPOST('schriftart_upload_bezeichnung');

        $fontUploadSize =
          (int)$_FILES['schriftart_upload']['size']['normal'] +
          (int)$_FILES['schriftart_upload']['size']['kursiv'] +
          (int)$_FILES['schriftart_upload']['size']['fett'] +
          (int)$_FILES['schriftart_upload']['size']['fettkursiv'];

        // Es wurde mindestens eine Datei hochgeladen
        if ($fontUploadSize > 0) {
          try {
            // Wenn keine Exception geworfen wurde, dann ist alles hochgeladen.
            $this->HandleFontUpload($fontUploadName, $_FILES['schriftart_upload']);
            $data['schriftart'] = $fontUploadName;
            $fontUploadSuccessful = true;
          } catch (Exception $e) {
            $error .= $e->getMessage() . '<br>';
            $fontUploadSuccessful = false;
          }
        }
      }

      if($error=='')
      {
        $vorhanden = $this->app->DB->Select("SELECT id FROM firmendaten WHERE firma='$id' LIMIT 1");

        if(!is_numeric($vorhanden)) {
          $this->app->DB->Insert("INSERT INTO firmendaten (firma) VALUES ('$id')");
        }

        // Update Bilder

        // suche max nummern

        //suche projekt ID von abkuerzung

        $data['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='{$data['projekt']}' LIMIT 1");

        // wenn cloud hole data[lizenz] data[schluessel]

        if(!($this->app->Conf->WFcloud==true)) {
          $extendsql = "lizenz='{$data['lizenz']}',
            schluessel='{$data['schluessel']}'";
            
          $this->app->DB->Update("UPDATE firmendaten SET  $extendsql WHERE firma='$id' LIMIT 1");
        }

        
        $firmendaten_werte = $this->app->DB->SelectArr("SELECT * FROM firmendaten_werte");
        if(!empty($firmendaten_werte)) {
          foreach($firmendaten_werte as $v) {
            $firmendaten_werte_spalten[$v['name']]['wert'] = $this->app->DB->real_escape_string($v['wert']);
            $firmendaten_werte_spalten[$v['name']]['id'] = $this->app->DB->real_escape_string($v['id']);
          }
        }
        $toupdate = array('absender','sichtbar','rechnung_gutschrift_ansprechpartner','knickfalz','standardaufloesung','standardversanddrucker','standardetikettendrucker','etikettendrucker_wareneingang','barcode','aufgaben_bondrucker'
          ,'schriftgroesse','betreffszeile','dokumententext','tabellenbeschriftung','tabelleninhalt','zeilenuntertext','freitext','infobox','brieftext','spaltenbreite','seite_von_sichtbar','seite_von_ausrichtung','footersichtbar','briefpapier2vorhanden'
          ,'hintergrund','benutzername','abstand_adresszeileoben','abstand_boxrechtsoben','abstand_boxrechtsoben_lr','abstand_betreffzeileoben','abstand_artikeltabelleoben','abstand_name_beschreibung','artikel_suche_kurztext'
          ,'adresse_freitext1_suche','artikel_freitext1_suche','artikel_suche_variante_von','artikel_artikelnummer_suche'
          ,'standard_datensaetze_datatables','waehrung','branch'
          ,'warnung_doppelte_nummern','wareneingang_zwischenlager','boxausrichtung'
          ,'footer_breite1','footer_breite2','footer_breite3','footer_breite4','steuersatz_normal','steuersatz_ermaessigt','angebot_ohnebriefpapier','auftrag_ohnebriefpapier','rechnung_ohnebriefpapier',
          'lieferschein_ohnebriefpapier','gutschrift_ohnebriefpapier','bestellung_ohnebriefpapier','arbeitsnachweis_ohnebriefpapier','externereinkauf'
          ,'projektnummerimdokument','mailanstellesmtp','herstellernummerimdokument','artikeleinheit','artikeleinheit_standard','auftrag_bezeichnung_bearbeiter','auftrag_bezeichnung_bestellnummer','bezeichnungkundennummer'
          ,'auftrag_bezeichnung_vertrieb','standardmarge','schriftart'
          ,'zahlung_rechnung','zahlung_vorkasse','zahlung_nachnahme','zahlung_bar','zahlung_paypal','zahlung_amazon','zahlung_lastschrift','zahlung_kreditkarte','zahlung_ratenzahlung','zahlung_rechnung_sofort_de','zahlung_rechnung_de'
          ,'zahlung_vorkasse_de','zahlung_bar_de','zahlung_lastschrift_de','zahlung_nachnahme_de','zahlung_paypal_de','zahlung_amazon_de','zahlung_kreditkarte_de','zahlung_ratenzahlung_de','zahlungszieltage','zahlungszieltageskonto'
          ,'zahlungszielskonto','kleinunternehmer','schnellanlegen','bestellvorschlaggroessernull','immernettorechnungen','rechnung_header','rechnung_footer',
          'lieferschein_header','lieferschein_footer','auftrag_header','auftrag_footer','angebot_header','angebot_footer','gutschrift_header','gutschrift_footer','bestellung_header','bestellung_footer',
          'arbeitsnachweis_header','arbeitsnachweis_footer','provisionsgutschrift_header','provisionsgutschrift_footer','proformarechnung_header','proformarechnung_footer','eu_lieferung_vermerk','export_lieferung_vermerk'
          ,'wareneingang_kamera_waage','layout_iconbar','passwort','host','port','mailssl','signatur','email','absendername','bcc1','bcc2'
          ,'firmenfarbe','name','strasse','plz','ort','steuernummer','projekt','steuer_positionen_export','tabsnavigationfarbe','tabsnavigationfarbeschrift'
        );

        if(isset($sql2a)){
          unset($sql2a);
        }
        foreach($toupdate as $v) {
          $sql2a[] = $v ." = '".$data[$v]."' ";
        }
        $sql2 = "UPDATE firmendaten SET ".implode(',',$sql2a)." WHERE firma = '$id' LIMIT 1";
        
        unset($sql2a);
        $this->app->DB->Update($sql2);
        if($this->app->DB->error()) {
          foreach($toupdate as $v) {
            $this->app->DB->Update("UPDATE firmendaten SET ".$v." = '".($data[$v])."'"." WHERE firma = '$id' LIMIT 1");
          }
        }
        
        if(isset($firmendaten_werte_spalten)) {
          foreach($toupdate as $key) {
            if(isset($firmendaten_werte_spalten[$key]) && $firmendaten_werte_spalten[$key]['wert'] != $data[$key]) {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '".$data[$key]."' WHERE id = '".$firmendaten_werte_spalten[$key]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$key]);
              if(!empty($doubletes[$key])) {
                $this->app->DB->Delete(
                  sprintf(
                    "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                    $firmendaten_werte_spalten[$key]['id'], $this->app->DB->real_escape_string($key),
                    implode(', ', $doubletes[$key])
                  )
                );
                unset($doubletes[$key]);
              }
            }
          }
        }
        
        
        
        for($i = 0; $i <= 3; $i++) {
          for($j = 0; $j <= 5; $j++) {
            $toupdate2['footer_'.$i.'_'.$j] = $data['footer'][$i][$j];
          }
        }        
        
        foreach($toupdate2 as $k => $v) {
          $sql2a[] = $k ." = '".$v."' ";
        }
        $sql2 = "UPDATE firmendaten SET ".implode(',',$sql2a)." WHERE firma = '$id' LIMIT 1";
        unset($sql2a);
        $this->app->DB->Update($sql2);
        if($this->app->DB->error()) {
          foreach($toupdate2 as $k => $v) {
            $this->app->DB->Update("UPDATE firmendaten SET ".$k." = '".$v."'"." WHERE firma = '$id' LIMIT 1");
          }
        }
        
        if(isset($firmendaten_werte_spalten)) {
          foreach($toupdate2 as $key => $v) {
            if(isset($firmendaten_werte_spalten[$key]) && $firmendaten_werte_spalten[$key]['wert'] != $toupdate2[$key])
            {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '".$toupdate2[$key]."' WHERE id = '".$firmendaten_werte_spalten[$key]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$key]);
              if(!empty($doubletes[$key])) {
                $this->app->DB->Delete(
                  sprintf(
                    "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                    $firmendaten_werte_spalten[$key]['id'], $this->app->DB->real_escape_string($key),
                    implode(', ', $doubletes[$key])
                  )
                );
                unset($doubletes[$key]);
              }
            }
          }
        }

        $checkDoublettes = [];
        for($i = 1; $i <= 5; $i++) {
          $n1 = 'artikeltabellezusatz'.$i;
          $n2 = 'adressetabellezusatz'.$i;
          $n3 = 'auftragtabellezusatz'.$i;
          $n4 = 'rechnungtabellezusatz'.$i;
          $n5 = 'lieferscheintabellezusatz'.$i;
          $n6 = 'produktiontabellezusatz'.$i;
          $n7 = 'bestellungtabellezusatz'.$i;
          $v1 = $this->app->Secure->GetPOST($n1);
          $v2 = $this->app->Secure->GetPOST($n2);
          $v3 = $this->app->Secure->GetPOST($n3);
          $v4 = $this->app->Secure->GetPOST($n4);
          $v5 = $this->app->Secure->GetPOST($n5);
          $v6 = $this->app->Secure->GetPOST($n6);
          $v7 = $this->app->Secure->GetPOST($n7);
          if(isset($firmendaten_werte_spalten)) {
            if(isset($firmendaten_werte_spalten[$n1]) && $firmendaten_werte_spalten[$n1]['wert'] != $v1) {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v1' WHERE id = '".$firmendaten_werte_spalten[$n1]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$n1]);
              $checkDoublettes[] = $n1;
            }
            if(isset($firmendaten_werte_spalten[$n2]) && $firmendaten_werte_spalten[$n2]['wert'] != $v2)
            {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v2' WHERE id = '".$firmendaten_werte_spalten[$n2]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$n2]);
              $checkDoublettes[] = $n2;
            }
            if(isset($firmendaten_werte_spalten[$n3]) && $firmendaten_werte_spalten[$n3]['wert'] != $v3)
            {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v3' WHERE id = '".$firmendaten_werte_spalten[$n3]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$n3]);
              $checkDoublettes[] = $n3;
            }
            if(isset($firmendaten_werte_spalten[$n4]) && $firmendaten_werte_spalten[$n4]['wert'] != $v4)
            {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v4' WHERE id = '".$firmendaten_werte_spalten[$n4]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$n4]);
              $checkDoublettes[] = $n4;
            }
            if(isset($firmendaten_werte_spalten[$n5]) && $firmendaten_werte_spalten[$n5]['wert'] != $v5)
            {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v5' WHERE id = '".$firmendaten_werte_spalten[$n5]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$n5]);
              $checkDoublettes[] = $n5;
            }
            if(isset($firmendaten_werte_spalten[$n6]) && $firmendaten_werte_spalten[$n6]['wert'] != $v6)
            {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v6' WHERE id = '".$firmendaten_werte_spalten[$n6]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$n6]);
              $checkDoublettes[] = $n6;
            }
            if(isset($firmendaten_werte_spalten[$n7]) && $firmendaten_werte_spalten[$n7]['wert'] != $v7)
            {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '$v7' WHERE id = '".$firmendaten_werte_spalten[$n7]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$n7]);
              $checkDoublettes[] = $n7;
            }
          }
        }

        if(!empty($checkDoublettes) && !empty($doubletes)) {
          foreach($checkDoublettes as $key) {
            if(!empty($doubletes[$key])){
              $this->app->DB->Delete(
                sprintf(
                  "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                  $firmendaten_werte_spalten[$key]['id'], $this->app->DB->real_escape_string($key),
                  implode(', ', $doubletes[$key])
                )
              );
              unset($doubletes[$key]);
            }
          }
        }

        $fields = $this->app->erp->GetFirmaFields();
        
        $fields[] = 'produktionsverhalten';
        $fields[] = 'taxfromdoctypesettings';

        $sqla = null;
        foreach($fields as $key) {
          if(isset($firmendaten_werte_spalten)) {
            if(isset($firmendaten_werte_spalten[$key]) && $firmendaten_werte_spalten[$key]['wert'] != $data[$key])
            {
              $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '".$data[$key]."' WHERE id = '".$firmendaten_werte_spalten[$key]['id']."' LIMIT 1");
              unset($firmendaten_werte_spalten[$key]);
              if(!empty($doubletes[$key])){
                $this->app->DB->Delete(
                  sprintf(
                    "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                    $firmendaten_werte_spalten[$key]['id'], $this->app->DB->real_escape_string($key),
                    implode(', ', $doubletes[$key])
                  )
                );
                unset($doubletes[$key]);
              }
            }
          }
          $sqla[] = " $key='{$data[$key]}' ";
        }
        $sql = "UPDATE firmendaten SET ".implode(', ',$sqla)." WHERE firma='$id' LIMIT 1";
        $this->app->DB->Update($sql);
        if($this->app->DB->error()) {
          foreach($fields as $key) {
            $this->app->DB->Update("UPDATE firmendaten SET $key='{$data[$key]}' WHERE firma='$id' LIMIT 1");
          }
        }
        $fields_checkbox = $this->app->erp->GetFirmaFieldsCheckbox();
        
        if(isset($sqla)){
          unset($sqla);
        }
        foreach($fields_checkbox as $key) {
          $_data[$key] = $data[$key];
          if($_data[$key] === 'checked'){
            $_data[$key] = 1;
          }
          $_data[$key] = (int)$_data[$key];
          $sqla[] = " $key='{$_data[$key]}' ";
          if(isset($firmendaten_werte_spalten) && isset($firmendaten_werte_spalten[$key]) && $firmendaten_werte_spalten[$key]['wert'] != $_data[$key])
          {
            $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '".$_data[$key]."' WHERE id = '".$firmendaten_werte_spalten[$key]['id']."' LIMIT 1");
            if(in_array($key,array('begrenzen_belege','begrenzen_artikeltabelle','begrenzen_adressetabelle')))
            {
              $this->app->erp->SetKonfigurationValue('firmendaten_'.$key.'_gesetzt', 1);
            }
            unset($firmendaten_werte_spalten[$key]);
          }
        }
        $sql = "UPDATE firmendaten SET ".implode(', ',$sqla)." WHERE firma='$id' LIMIT 1";
        $this->app->DB->Update($sql);
        if($this->app->DB->error()) {
          foreach($fields_checkbox as $key) {
            $_data[$key] = $data[$key];
            if($_data[$key] === 'checked'){
              $_data[$key] = 1;
            }
            $_data[$key] = (int)$_data[$key];
            $this->app->DB->Update("UPDATE firmendaten SET $key='{$_data[$key]}' WHERE firma='$id' LIMIT 1");
          }
        }
        
        $firmendaten = $this->app->DB->SelectArr("SELECT * FROM firmendaten WHERE firma='$id' LIMIT 1");
        if($firmendaten && isset($firmendaten_werte_spalten)) {
          foreach($firmendaten[0] as $key => $v)  {
            if($key !== 'id' && isset($data[$key])) {
              if(isset($firmendaten_werte_spalten[$key]) && $firmendaten_werte_spalten[$key]['wert'] != $data[$key]) {
                $this->app->DB->Update("UPDATE firmendaten_werte SET wert = '".$data[$key]."' WHERE id = '".$firmendaten_werte_spalten[$key]['id']."' LIMIT 1");
                unset($firmendaten_werte_spalten[$key]);
                if(!empty($doubletes[$key])){
                  $this->app->DB->Delete(
                    sprintf(
                      "DELETE FROM firmendaten_werte WHERE id <> %d AND name != '%s' AND id IN (%s)",
                      $firmendaten_werte_spalten[$key]['id'], $this->app->DB->real_escape_string($key),
                      implode(', ', $doubletes[$key])
                    )
                  );
                  unset($doubletes[$key]);
                }
              }
            }            
          }
        }
        
        $this->app->DB->Update("UPDATE firma SET name='{$data[name]}', standardprojekt='{$data[projekt]}' WHERE id='$id' LIMIT 1");

        $this->app->Tpl->Set('MESSAGE', "<div class=\"error2\">Ihre Daten wurden erfolgreich gespeichert.</div>");
        $this->FillFormFromDB($id); 
      }
      else {
        // Im Fehlerfall sollen das Formular mit den POST-Daten gefuellt werden
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
        $this->fillForm($data);
      }
      if(!$versandmail_zwischenspeichern  && $this->app->erp->Firmendaten('versandmail_zwischenspeichern','',true) &&
        !$this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'versandmailsundrueckmeldung' AND aktiv = 1")){
        $this->app->DB->Update("UPDATE prozessstarter SET aktiv = 1 WHERE parameter = 'versandmailsundrueckmeldung' LIMIT 1");
      }
    }
    else{
      $this->FillFormFromDB($id);
    }

    if($this->app->erp->Firmendaten('versandmail_zwischenspeichern',"",true)){
      $this->app->erp->checkActiveCronjob('versandmailsundrueckmeldung', 'MESSAGEMAILS', false);
    }

    if (isset($fontUploadSuccessful) && $fontUploadSuccessful === true){
      $this->app->Tpl->Add('MESSAGE',
        sprintf('<div class="info">Die Schriftart &quot;%s&quot; wurde erfolgreich hochgeladen und in den Einstellungen hinterlegt. Bitte Testen Sie die Einstellung durch Generierung eines Belegs.</div>', $fontUploadName)
      );
    }


    $hintergrund=$this->app->erp->Firmendaten("hintergrund");
    $hintergrund_size = 0;
    switch($hintergrund) {
      case "briefpapier":
        $hintergrund_size = strlen($this->app->erp->getSettingsFile('briefpapier'));
        break;
      case "logo":
        $hintergrund_size = strlen($this->app->erp->getSettingsFile('logo'));
      break;
    }

    if($hintergrund_size/1000 > 100){
      $this->app->Tpl->Add('MESSAGE', '<div class="warning">Die Dateigröße von ca. ' . round($hintergrund_size / 1000) . ' KB vom ' . ucfirst($hintergrund) . ' ist zu groß. Das ' . ucfirst($hintergrund) . ' sollte unter 100 KB sein.</div>');
    }

    if($this->app->erp->Firmendaten('lizenz', true)) {
      $this->app->Tpl->Set('UPDATESTARTENBUTTON','&nbsp;<a href="update.php"><input type="button" value="{|Update starten|}" /></a>');
    }

    $getPaymentmethodsToHide = $this->getPaymentmethodsToHide();
    if(!empty($getPaymentmethodsToHide)) {
      $this->app->Tpl->Add(
        'MESSAGEZAHLUNGSWEISEN',
        '<div class="info">Bitte stellen Sie die Zahlungstexte in den 
          <a href="index.php?module=zahlungsweisen&action=list" target="_blank">Zahlungsweisen</a> ein.
        </div>'
      );
      foreach($getPaymentmethodsToHide as $paymentMethod) {
        $this->app->Tpl->Add('TR_ZAHLUNG_'.strtoupper($paymentMethod), ' class="hide" ');
      }
    }
    if(!empty($this->getNotInstalledPayments())) {
      $this->app->Tpl->Add(
        'MESSAGEZAHLUNGSWEISEN',
        '<div class="info">
          Ab der Version 20.1 sind alle Zahlweisen in das Modul Zahlweisen verlagert worden. 
          Sie sehen diese Meldung weil Sie vor der Version 20.1 mit Xentral gestartet hatten. 
          Wenn Sie auf das neue Modul welchseln möchten 
          (bitte nur nachdem Sie es in einem Testsystem geprüft hatten) 
          können Sie das gerne hier mit dem Button durchführen. 
          <div class="clear"></div>
          <input type="submit" 
          value="Auf neues Modul Zahlungsweise unwiderruflich wechseln" 
          id="installnewpayent" name="installnewpayent" />
          <div class="clear"></div>
        </div>'
      );
    }
    $this->app->YUI->AutoComplete('steuersatz_normal','steuersatz',1);
    $this->app->YUI->AutoComplete('steuersatz_ermaessigt','steuersatz',1);
    $this->app->Tpl->Parse('PAGE','firmendaten.tpl');
  }

  /**
   * @param int $id
   */
  function fillFormFromDB($id)
  {
    $vorhanden = $this->app->DB->Select("SELECT id FROM firmendaten WHERE firma='$id' LIMIT 1");

    if(!is_numeric($vorhanden))
    {
      // Falls das Formular zum ersten mal aufgerufen wird
      $this->app->Tpl->Set('SICHTBAR' , "checked");
      $this->app->Tpl->Set('HINTERGRUNDKEIN', "checked");
    }else
    {
      // Lade Formular aus DB
      $data = $this->app->DB->SelectArr("SELECT * FROM firmendaten WHERE firma='$id' LIMIT 1");
      $firmendaten_werte = $this->app->DB->SelectArr("SELECT * FROM firmendaten_werte");
      if($firmendaten_werte)
      {
        foreach($firmendaten_werte as $v) {
          if(!isset($data[0][$v['name']])){
            $data[0][$v['name']] = $v['wert'];
          }
        }
      }

      //Brief Absender
      $this->app->Tpl->Set('ABSENDER' , $data[0]['absender']);    
      $this->app->Tpl->Set('SICHTBAR' , $this->parseCheckbox($data[0]['sichtbar']));
      $this->app->Tpl->Set('RECHNUNG_GUTSCHRIFT_ANSPRECHPARTNER' , $this->parseCheckbox($data[0]['rechnung_gutschrift_ansprechpartner']));
      $this->app->Tpl->Set('KNICKFALZ' , $this->parseCheckbox($data[0]['knickfalz']));
      $this->app->Tpl->Set('STANDARDAUFLOESUNG', $this->parseCheckbox($data[0]['standardaufloesung']));
      //$this->app->Tpl->Set('FIRMENLOGOAKTIV' , $this->parseCheckbox($data[0]['firmenlogoaktiv']));
      $this->app->Tpl->Set('ARTIKELSUCHEKURZTEXT' , $this->parseCheckbox($data[0]['artikel_suche_kurztext']));
      $this->app->Tpl->Set('ARTIKELSUCHEVARIANTEVON' , $this->parseCheckbox($data[0]['artikel_suche_variante_von']));
      
      $this->app->Tpl->Set('ARTIKEL_FREITEXT1_SUCHE' , $this->parseCheckbox($data[0]['artikel_freitext1_suche']));
      $this->app->Tpl->Set('ADRESSE_FREITEXT1_SUCHE' , $this->parseCheckbox($data[0]['adresse_freitext1_suche']));
      $this->app->Tpl->Set('PARAMETERUNDFREIFELDER' , $this->parseCheckbox($data[0]['parameterundfreifelder']));
      $this->app->Tpl->Set('ARTIKEL_ARTIKELNUMMER_SUCHE' , $this->parseCheckbox($data[0]['artikel_artikelnummer_suche']));
      for($in = 1; $in <= 40; $in++){
        $this->app->Tpl->Set('FREIFELD'.$in , $data[0]['freifeld'.$in]);
      }
      for($in = 1; $in <= 20; $in++){
        $this->app->Tpl->Set('PROJEKTFREIFELD'.$in , $data[0]['projektfreifeld'.$in]);
      }

      $this->app->Tpl->Set('STANDARD_DATENSAETZE_DATATABLES' , $data[0]['standard_datensaetze_datatables']);    
      $this->app->Tpl->Set('STEUERSATZNORMAL' , $data[0]['steuersatz_normal']);    
      $this->app->Tpl->Set('STEUERSATZERMAESSIGT' , $data[0]['steuersatz_ermaessigt']);    
      $this->app->Tpl->Set('WAEHRUNG' , $data[0]['waehrung']);    
      $this->app->Tpl->Set('LIZENZ' , $data[0]['lizenz']);    
      $this->app->Tpl->Set('SCHLUESSEL' , $data[0]['schluessel']);    
      $this->app->Tpl->Set('BRANCH' , $data[0]['branch']);    
      $this->app->Tpl->Set('VERSION' , $data[0]['version']);    

      $this->app->Tpl->Set('BOXAUSRICHTUNG' , $data[0]['boxausrichtung']);    
      $this->app->Tpl->Set('FOOTERBREITE1' , $data[0]['footer_breite1']);    
      $this->app->Tpl->Set('FOOTERBREITE2' , $data[0]['footer_breite2']);    
      $this->app->Tpl->Set('FOOTERBREITE3' , $data[0]['footer_breite3']);    
      $this->app->Tpl->Set('FOOTERBREITE4' , $data[0]['footer_breite4']);    
      $this->app->Tpl->Set('WARNUNG_DOPPELTE_NUMMERN' , $this->parseCheckbox($data[0]['warnung_doppelte_nummern']));
      $this->app->Tpl->Set('WARENEINGANG_ZWISCHENLAGER' , $this->parseCheckbox($data[0]['wareneingang_zwischenlager']));

      //Formatierung
      $this->app->Tpl->Set('BARCODE' , $this->parseCheckbox($data[0]['barcode']));    
      $this->app->Tpl->Set('SCHRIFTGROESSE' , ($data[0]['schriftgroesse']));    
      $this->app->Tpl->Set('BETREFFSZEILE' , ($data[0]['betreffszeile']));    
      $this->app->Tpl->Set('DOKUMENTENTEXT' , ($data[0]['dokumententext']));    
      $this->app->Tpl->Set('TABELLENBESCHRIFTUNG' , ($data[0]['tabellenbeschriftung']));    
      $this->app->Tpl->Set('TABELLENINHALT' , ($data[0]['tabelleninhalt']));    
      $this->app->Tpl->Set('ZEILENUNTERTEXT' , ($data[0]['zeilenuntertext']));    
      $this->app->Tpl->Set('FREITEXT' , ($data[0]['freitext']));    
      $this->app->Tpl->Set('BRIEFTEXT' , ($data[0]['brieftext']));    
      $this->app->Tpl->Set('INFOBOX' , ($data[0]['infobox']));    
      $this->app->Tpl->Set('SPALTENBREITE' , ($data[0]['spaltenbreite']));

      $this->app->Tpl->Set('ABSTANDADRESSZEILEOBEN' , ($data[0]['abstand_adresszeileoben']));    
      $this->app->Tpl->Set('ABSTANDBOXRECHTSOBEN' , ($data[0]['abstand_boxrechtsoben']));    
      $this->app->Tpl->Set('ABSTANDBOXRECHTSOBENLR' , ($data[0]['abstand_boxrechtsoben_lr']));    
      $this->app->Tpl->Set('ABSTANDBETREFFZEILEOBEN' , ($data[0]['abstand_betreffzeileoben']));    
      $this->app->Tpl->Set('ABSTANDARTIKELTABELLEOBEN' , ($data[0]['abstand_artikeltabelleoben']));    
      $this->app->Tpl->Set('ABSTANDNAMEBESCHREIBUNG' , ($data[0]['abstand_name_beschreibung']));    
      $this->app->Tpl->Set('ARTIKELEINHEITSTANDARD' , ($data[0]['artikeleinheit_standard']));    
      $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_BEARBEITER' , ($data[0]['auftrag_bezeichnung_bearbeiter']));    
      $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_VERTRIEB' , ($data[0]['auftrag_bezeichnung_vertrieb']));    
      $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_BESTELLNUMMER' , ($data[0]['auftrag_bezeichnung_bestellnummer']));    
      $this->app->Tpl->Set('BEZEICHNUNGKUNDENNUMMER' , ($data[0]['bezeichnungkundennummer']));    

      // Footer
      $this->app->Tpl->Set('FOOTER00' , $data[0]['footer_0_0']);
      $this->app->Tpl->Set('FOOTER01' , $data[0]['footer_0_1']);
      $this->app->Tpl->Set('FOOTER02' , $data[0]['footer_0_2']);
      $this->app->Tpl->Set('FOOTER03' , $data[0]['footer_0_3']);
      $this->app->Tpl->Set('FOOTER04' , $data[0]['footer_0_4']);
      $this->app->Tpl->Set('FOOTER05' , $data[0]['footer_0_5']);
      $this->app->Tpl->Set('FOOTER10' , $data[0]['footer_1_0']);
      $this->app->Tpl->Set('FOOTER11' , $data[0]['footer_1_1']);
      $this->app->Tpl->Set('FOOTER12' , $data[0]['footer_1_2']);
      $this->app->Tpl->Set('FOOTER13' , $data[0]['footer_1_3']);
      $this->app->Tpl->Set('FOOTER14' , $data[0]['footer_1_4']);
      $this->app->Tpl->Set('FOOTER15' , $data[0]['footer_1_5']);
      $this->app->Tpl->Set('FOOTER20' , $data[0]['footer_2_0']);
      $this->app->Tpl->Set('FOOTER21' , $data[0]['footer_2_1']);
      $this->app->Tpl->Set('FOOTER22' , $data[0]['footer_2_2']);
      $this->app->Tpl->Set('FOOTER23' , $data[0]['footer_2_3']);
      $this->app->Tpl->Set('FOOTER24' , $data[0]['footer_2_4']);
      $this->app->Tpl->Set('FOOTER25' , $data[0]['footer_2_5']);
      $this->app->Tpl->Set('FOOTER30' , $data[0]['footer_3_0']);
      $this->app->Tpl->Set('FOOTER31' , $data[0]['footer_3_1']);
      $this->app->Tpl->Set('FOOTER32' , $data[0]['footer_3_2']);
      $this->app->Tpl->Set('FOOTER33' , $data[0]['footer_3_3']);
      $this->app->Tpl->Set('FOOTER34' , $data[0]['footer_3_4']);
      $this->app->Tpl->Set('FOOTER35' , $data[0]['footer_3_5']);

      $this->app->Tpl->Set('RECHNUNG_HEADER' , $data[0]['rechnung_header']);
      $this->app->Tpl->Set('LIEFERSCHEIN_HEADER' , $data[0]['lieferschein_header']);
      $this->app->Tpl->Set('GUTSCHRIFT_HEADER' , $data[0]['gutschrift_header']);
      $this->app->Tpl->Set('ANGEBOT_HEADER' , $data[0]['angebot_header']);
      $this->app->Tpl->Set('AUFTRAG_HEADER' , $data[0]['auftrag_header']);
      $this->app->Tpl->Set('BESTELLUNG_HEADER' , $data[0]['bestellung_header']);
      $this->app->Tpl->Set('ARBEITSNACHWEIS_HEADER' , $data[0]['arbeitsnachweis_header']);
      $this->app->Tpl->Set('PROVISIONSGUTSCHRIFT_HEADER' , $data[0]['provisionsgutschrift_header']);
      $this->app->Tpl->Set('PROFORMARECHNUNG_HEADER' , $data[0]['proformarechnung_header']);

      $this->app->Tpl->Set('RECHNUNG_FOOTER' , $data[0]['rechnung_footer']);
      $this->app->Tpl->Set('LIEFERSCHEIN_FOOTER' , $data[0]['lieferschein_footer']);
      $this->app->Tpl->Set('GUTSCHRIFT_FOOTER' , $data[0]['gutschrift_footer']);
      $this->app->Tpl->Set('ANGEBOT_FOOTER' , $data[0]['angebot_footer']);
      $this->app->Tpl->Set('AUFTRAG_FOOTER' , $data[0]['auftrag_footer']);
      $this->app->Tpl->Set('BESTELLUNG_FOOTER' , $data[0]['bestellung_footer']);
      $this->app->Tpl->Set('ARBEITSNACHWEIS_FOOTER' , $data[0]['arbeitsnachweis_footer']);
      $this->app->Tpl->Set('PROVISIONSGUTSCHRIFT_FOOTER' , $data[0]['provisionsgutschrift_footer']);
      $this->app->Tpl->Set('PROFORMARECHNUNG_FOOTER' , $data[0]['proformarechnung_footer']);
      $this->app->Tpl->Set('EU_LIEFERUNG_VERMERK' , $data[0]['eu_lieferung_vermerk']);
      $this->app->Tpl->Set('EXPORT_LIEFERUNG_VERMERK' , $data[0]['export_lieferung_vermerk']);

      $this->app->Tpl->Set('STANDARDVERSANDDRUCKER' , $this->app->erp->GetSelectDrucker($data[0]['standardversanddrucker']));
      $this->app->Tpl->Set('STANDARDETIKETTENDRUCKER' , $this->app->erp->GetSelectEtikettenDrucker($data[0]['standardetikettendrucker']));
      $this->app->Tpl->Set('ETIKETTENDRUCKERWARENEINGANG' , $this->app->erp->GetSelectEtikettenDrucker($data[0]['etikettendrucker_wareneingang']));
      $this->app->Tpl->Set('AUFGABEN_BONDRUCKER' , $this->app->erp->GetSelectBonDrucker($data[0]['aufgaben_bondrucker']));
      $this->app->Tpl->Set('SPRACHEBEVORZUGEN', $this->languageSelectOptions($this->getPreferredLanguage()));
      $stichwoerter = $this->app->erp->getDateiTypen('artikel');
      foreach ($stichwoerter as $stichwort){
        $selected = '';
        if ($stichwort['wert'] === $data[0]['wareneingangbildtypvorauswahl']){
          $selected = 'selected';
        }
        $this->app->Tpl->Add('WARENEINGANBILDTYPVORAUSWAHL','<option value="'.$stichwort['wert'].'" '.$selected.'>'.$stichwort['beschriftung'].'</option>');
      }

      $scanVerhalten = [
        'nurseriennummern' => 'Nur Seriennummern erfassen',
        'erstseriennummern' => 'Erst Seriennummern dann Bemerkungen erfassen',
        'abwechselnd' => 'Seriennummern und Bemerkungen abwechselnd erfassen'];
      foreach ($scanVerhalten as $verhalten => $bezeichnung){
        $selected = '';
        if ($verhalten === $data[0]['wareneingangscanverhalten']){
          $selected = 'selected';
        }
        $this->app->Tpl->Add('WARENEINGANGSCANVERHALTENAUSWAHL',"<option value=\"$verhalten\" $selected>$bezeichnung</option>");
      }

      $this->app->Tpl->Set('FOOTERSICHTBAR' , $this->parseCheckbox($data[0]['footersichtbar']));
      $this->app->Tpl->Set('BRIEFPAPIER2VORHANDEN' , $this->parseCheckbox($data[0]['briefpapier2vorhanden']));
      $this->app->Tpl->Set('ANGEBOT_OHNEBRIEFPAPIER' , $this->parseCheckbox($data[0]['angebot_ohnebriefpapier']));
      $this->app->Tpl->Set('AUFTRAG_OHNEBRIEFPAPIER' , $this->parseCheckbox($data[0]['auftrag_ohnebriefpapier']));
      $this->app->Tpl->Set('RECHNUNG_OHNEBRIEFPAPIER' , $this->parseCheckbox($data[0]['rechnung_ohnebriefpapier']));
      $this->app->Tpl->Set('LIEFERSCHEIN_OHNEBRIEFPAPIER' , $this->parseCheckbox($data[0]['lieferschein_ohnebriefpapier']));
      $this->app->Tpl->Set('GUTSCHRIFT_OHNEBRIEFPAPIER' , $this->parseCheckbox($data[0]['gutschrift_ohnebriefpapier']));
      $this->app->Tpl->Set('BESTELLUNG_OHNEBRIEFPAPIER' , $this->parseCheckbox($data[0]['bestellung_ohnebriefpapier']));
      $this->app->Tpl->Set('ARBEITSNACHWEIS_OHNEBRIEFPAPIER' , $this->parseCheckbox($data[0]['arbeitsnachweis_ohnebriefpapier']));
      $this->app->Tpl->Set('EXTERNEREINKAUF' , $this->parseCheckbox($data[0]['externereinkauf']));

      $this->app->Tpl->Set('PROJEKTNUMMERIMDOKUMENT' , $this->parseCheckbox($data[0]['projektnummerimdokument']));
      $this->app->Tpl->Set('MAILANSTELLESMTP' , $this->parseCheckbox($data[0]['mailanstellesmtp']));
      $this->app->Tpl->Set('HERSTELLERNUMMERIMDOKUMENT' , $this->parseCheckbox($data[0]['herstellernummerimdokument']));
      $this->app->Tpl->Set('ARTIKELEINHEIT' , $this->parseCheckbox($data[0]['artikeleinheit']));
      $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_BEARBEITER' , $data[0]['auftrag_bezeichnung_bearbeiter']);
      $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_VETRIEB' , $data[0]['auftrag_bezeichnung_vertrieb']);
      $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_BESTELLNUMMER' , $data[0]['auftrag_bezeichnung_bestellnummer']);
      $this->app->Tpl->Set('BEZEICHNUNGKUNDENNUMMER' , $data[0]['bezeichnungkundennummer']);
      $this->app->Tpl->Set('STANDARDMARGE' , $data[0]['standardmarge']);

      $this->app->Tpl->Set('SEITEVONSICHTBAR' , $this->parseCheckbox($data[0]['seite_von_sichtbar']));
      $this->app->Tpl->Set('SEITEVONAUSRICHTUNG' , $data[0]['seite_von_ausrichtung']);
      $this->app->Tpl->Set('SCHRIFTART' , $data[0]['schriftart']);

      $this->app->Tpl->Set('FIRMENFARBEHELL',$data[0]['firmenfarbehell']);
      $this->app->Tpl->Set('FIRMENFARBEDUNKEL',$data[0]['firmenfarbedunkel']);
      $this->app->Tpl->Set('FIRMENFARBEGANZDUNKEL',$data[0]['firmenfarbeganzdunkel']);

      $this->app->Tpl->Set('NAVIGATIONFARBE',$data[0]['navigationfarbe']);
      $this->app->Tpl->Set('NAVIGATIONFARBESCHRIFT',$data[0]['navigationfarbeschrift']);
      $this->app->Tpl->Set('TABSNAVIGATIONFARBE',$data[0]['tabsnavigationfarbe']);
      $this->app->Tpl->Set('TABSNAVIGATIONFARBESCHRIFT',$data[0]['tabsnavigationfarbeschrift']);
      $this->app->Tpl->Set('UNTERNAVIGATIONFARBE',$data[0]['unternavigationfarbe']);
      $this->app->Tpl->Set('UNTERNAVIGATIONFARBESCHRIFT',$data[0]['unternavigationfarbeschrift']);

      $this->app->Tpl->Set('ZAHLUNG_RECHNUNG' , $this->parseCheckbox($data[0]['zahlung_rechnung']));
      $this->app->Tpl->Set('ZAHLUNG_VORKASSE' , $this->parseCheckbox($data[0]['zahlung_vorkasse']));
      $this->app->Tpl->Set('ZAHLUNG_NACHNAHME' , $this->parseCheckbox($data[0]['zahlung_nachnahme']));
      $this->app->Tpl->Set('ZAHLUNG_LASTSCHRIFT' , $this->parseCheckbox($data[0]['zahlung_lastschrift']));
      $this->app->Tpl->Set('ZAHLUNG_BAR' , $this->parseCheckbox($data[0]['zahlung_bar']));
      $this->app->Tpl->Set('ZAHLUNG_KREDITKARTE' , $this->parseCheckbox($data[0]['zahlung_kreditkarte']));
      $this->app->Tpl->Set('ZAHLUNG_PAYPAL' , $this->parseCheckbox($data[0]['zahlung_paypal']));
      $this->app->Tpl->Set('ZAHLUNG_AMAZON' , $this->parseCheckbox($data[0]['zahlung_amazon']));
      $this->app->Tpl->Set('ZAHLUNG_RATENZAHLUNG' , $this->parseCheckbox($data[0]['zahlung_ratenzahlung']));
      $this->app->Tpl->Set('KLEINUNTERNEHMER' , $this->parseCheckbox($data[0]['kleinunternehmer']));
      $this->app->Tpl->Set('SCHNELLANLEGEN' , $this->parseCheckbox($data[0]['schnellanlegen']));
      $this->app->Tpl->Set('BESTELLVORSCHLAGSGROESSERNULL' , $this->parseCheckbox($data[0]['bestellvorschlaggroessernull']));
      $this->app->Tpl->Set('IMMERNETTORECHNUNGEN' , $this->parseCheckbox($data[0]['immernettorechnungen']));

      $this->app->Tpl->Set('ZAHLUNG_RECHNUNG_SOFORT_DE', $data[0]['zahlung_rechnung_sofort_de']);
      $this->app->Tpl->Set('ZAHLUNG_RECHNUNG_DE', $data[0]['zahlung_rechnung_de']);
      $this->app->Tpl->Set('STEUER_POSITIONEN_EXPORT', $data[0]['steuer_positionen_export']);
      
      $fields = $this->app->erp->GetFirmaFields();
      foreach($fields as $key) {
        $this->app->Tpl->Set(strtoupper($key), $data[0][$key]);
        if(in_array($key,array('begrenzenanzahl_belege','begrenzenanzahl_artikeltabelle','begrenzenanzahl_adressetabelle'))) {
          if(empty($data[0][$key])) {
            $this->app->Tpl->Set('VOR'.strtoupper($key),'<!--');
            $this->app->Tpl->Set('NACH'.strtoupper($key),'-->');
          }
        }
      }
      $fields = null;
      for($ki = 1; $ki <= 40; $ki++) {
        $fields[]='freifeld'.$ki.'typ';
        $fields[]='freifeld'.$ki.'spalte';
        $fields[]='freifeld'.$ki.'sort';
      }

      for($ki = 1; $ki <= 20; $ki++) {
        $fields[]='projektfreifeld'.$ki.'typ';
        $fields[]='projektfreifeld'.$ki.'spalte';
        $fields[]='projektfreifeld'.$ki.'sort';
        $fields[]='projektfreifeld'.$ki.'tabelle';
        $fields[]='projektfreifeld'.$ki.'breite';
      }
      
      for($ki = 1; $ki <= 20; $ki++) {
        $fields[]='adressefreifeld'.$ki;
        $fields[]='adressefreifeld'.$ki.'sort';
      }
      
      foreach($fields as $key) {
        $this->app->Tpl->Set(strtoupper($key), $data[0][$key]);
      }

      $tmp = null;
      $belege = array('an','ab','re','gs','ls','be','pr','pa','pd');
      foreach($belege as $value) {
        for($i=1;$i<=40;$i++) {
          $tmp[] = 'freifeld' . $i . $value;
        }
      }
      foreach($tmp as $key) {
        $this->app->Tpl->Set(strtoupper($key), $this->parseCheckbox($data[0][$key]));
      }
      
      $fields_checkbox = $this->app->erp->GetFirmaFieldsCheckbox();
      foreach($fields_checkbox as $key) {
        $this->app->Tpl->Set(strtoupper($key), $this->parseCheckbox($data[0][$key]));
      }
      
      $this->app->Tpl->Set('VERSANDART', $this->app->erp->GetSelectAsso($this->app->erp->GetVersandartAuftrag(),$data[0]['versandart']));    
      $this->app->Tpl->Set('ZAHLUNGSWEISE', $this->app->erp->GetSelectAsso($this->app->erp->GetZahlungsweise(),$data[0]['zahlungsweise']));    
      $this->app->Tpl->Set('ZAHLUNGSWEISELIEFERANT', $this->app->erp->GetSelectAsso($this->app->erp->GetZahlungsweise(),$data[0]['zahlungsweiselieferant']));    
      $this->app->Tpl->Set('WAEHRUNG', $this->app->erp->GetSelectAsso(
        $this->app->erp->GetWaehrung(),(string)$data[0]['waehrung'] === '' ? 'EUR' : $data[0]['waehrung'])
      );


      $this->app->Tpl->Set('ZAHLUNG_VORKASSE_DE', $data[0]['zahlung_vorkasse_de']);
      $this->app->Tpl->Set('ZAHLUNG_NACHNAHME_DE', $data[0]['zahlung_nachnahme_de']);
      $this->app->Tpl->Set('ZAHLUNG_BAR_DE', $data[0]['zahlung_bar_de']);
      $this->app->Tpl->Set('ZAHLUNG_PAYPAL_DE', $data[0]['zahlung_paypal_de']);
      $this->app->Tpl->Set('ZAHLUNG_LASTSCHRIFT_DE', $data[0]['zahlung_lastschrift_de']);
      $this->app->Tpl->Set('ZAHLUNG_KREDITKARTE_DE', $data[0]['zahlung_kreditkarte_de']);
      $this->app->Tpl->Set('ZAHLUNG_AMAZON_DE', $data[0]['zahlung_amazon_de']);
      $this->app->Tpl->Set('ZAHLUNG_RATENZAHLUNG_DE', $data[0]['zahlung_ratenzahlung_de']);

      $this->app->Tpl->Set('ZAHLUNGSZIELTAGE', $data[0]['zahlungszieltage']);
      $this->app->Tpl->Set('ZAHLUNGSZIELTAGESKONTO', $data[0]['zahlungszieltageskonto']);
      $this->app->Tpl->Set('ZAHLUNGSZIELSKONTO', $data[0]['zahlungszielskonto']);


      $this->app->Tpl->Set('WARENEINGANG_KAMERA_WAAGE', $this->parseCheckbox($data[0]['wareneingang_kamera_waage']));
      $this->app->Tpl->Set('LAYOUT_ICONBAR', $this->parseCheckbox($data[0]['layout_iconbar']));

      $this->app->Tpl->Set('NEXT_ANGEBOT' , $data[0]['next_angebot']);
      $this->app->Tpl->Set('NEXT_AUFTRAG' , $data[0]['next_auftrag']);
      $this->app->Tpl->Set('NEXT_LIEFERSCHEIN' , $data[0]['next_lieferschein']);
      $this->app->Tpl->Set('NEXT_RETOURE' , $data[0]['next_retoure']);
      $this->app->Tpl->Set('NEXT_RECHNUNG' , $data[0]['next_rechnung']);
      $this->app->Tpl->Set('NEXT_GUTSCHRIFT' , $data[0]['next_gutschrift']);
      $this->app->Tpl->Set('NEXT_BESTELLUNG' , $data[0]['next_bestellung']);
      $this->app->Tpl->Set('NEXT_ARBEITSNACHWEIS' , $data[0]['next_arbeitsnachweis']);
      $this->app->Tpl->Set('NEXT_KUNDENNUMMER' , $data[0]['next_kundennummer']);
      $this->app->Tpl->Set('NEXT_PROJEKTNUMMER' , $data[0]['next_projektnummer']);
      $this->app->Tpl->Set('NEXT_LIEFERANTENNUMMER' , $data[0]['next_lieferantennummer']);
      $this->app->Tpl->Set('NEXT_MITARBEITERNUMMER' , $data[0]['next_mitarbeiternummer']);
      $this->app->Tpl->Set('NEXT_ARTIKELNUMMER' , $data[0]['next_artikelnummer']);
      $this->app->Tpl->Set('NEXT_WAREN' , $data[0]['next_waren']);
      $this->app->Tpl->Set('NEXT_SONSTIGES' , $data[0]['next_sonstiges']);
      $this->app->Tpl->Set('NEXT_PRODUKTION' , $data[0]['next_produktion']);
      $this->app->Tpl->Set('NEXT_REISEKOSTEN', $data[0]['next_reisekosten']);
      $this->app->Tpl->Set('NEXT_PROFORMARECHNUNG', $data[0]['next_proformarechnung']);
      $this->app->Tpl->Set('NEXT_ANFRAGE' , $data[0]['next_anfrage']);
      $this->app->Tpl->Set('NEXT_PREISANFRAGE' , $data[0]['next_preisanfrage']);
      $this->app->Tpl->Set('NEXT_VERBINDLICHKEIT', $data[0]['next_verbindlichkeit']);
      $this->app->Tpl->Set('NEXT_RECEIPTDOCUMENT', $data[0]['next_receiptdocument']);

      //Briefpapier Hintergrund
      if($data[0]['hintergrund']==='logo') {
        $this->app->Tpl->Set('HINTERGRUNDLOGO', "checked");
        $this->app->Tpl->Set('HINTERGRUNDLOGOTEXT', "<a class=\"button\" href=\"index.php?module=firmendaten&action=logo\">Logo herunterladen</a>");
        $this->app->Tpl->Set('HINTERGRUNDTEXT', "Logo (<a href=\"index.php?module=firmendaten&action=logo\">ansehen</a>)");
      }
      else if($data[0]['hintergrund']==='briefpapier') {
        $this->app->Tpl->Set('HINTERGRUNDBRIEFPAPIER', "checked");
        $this->app->Tpl->Set('HINTERGRUNDBRIEFPAPIERTEXT', "<a class=\"button\" href=\"index.php?module=firmendaten&action=briefpapier&cmd=briefpapier1\">PDF herunterladen</a>");
        $this->app->Tpl->Set('HINTERGRUNDTEXT', "PDF (<a href=\"index.php?module=firmendaten&action=briefpapier\">ansehen</a>)");
      }
      else {
        $this->app->Tpl->Set('HINTERGRUNDKEIN', "checked");
        $this->app->Tpl->Set('HINTERGRUNDTEXT', "Kein");
      }
      if ((int)$data[0]['briefpapier2vorhanden'] === 1) {
        $this->app->Tpl->Set('HINTERGRUNDBRIEFPAPIER2TEXT', "<a class=\"button\" href=\"index.php?module=firmendaten&action=briefpapier&cmd=briefpapier2\">PDF herunterladen</a>");
      }

      //Versand E-Mail
      $this->app->Tpl->Set('BENUTZERNAME' , $data[0]['benutzername']);
      $this->app->Tpl->Set('PASSWORT' , $data[0]['passwort']);
      $this->app->Tpl->Set('HOST' , $data[0]['host']);
      $this->app->Tpl->Set('PORT' , $data[0]['port']);
      if($data[0]['mailssl']=="2")
        $this->app->Tpl->Set('SSL' , "selected");
      else if($data[0]['mailssl']=="1")
        $this->app->Tpl->Set('TLS' , "selected");

      // Signatur
      $this->app->Tpl->Set('SIGNATUR' , base64_decode($data[0]['signatur']));
      $this->app->Tpl->Set('EMAIL' , $data[0]['email']);
      $this->app->Tpl->Set('ABSENDERNAME' , $data[0]['absendername']);
      $this->app->Tpl->Set('BCC1' , $data[0]['bcc1']);
      $this->app->Tpl->Set('BCC2' , $data[0]['bcc2']);
      $this->app->Tpl->Set('FIRMENFARBE' , $data[0]['firmenfarbe']);
      $this->app->Tpl->Set('NAME' , $data[0]['name']);
      $this->app->Tpl->Set('STRASSE' , $data[0]['strasse']);
      $this->app->Tpl->Set('PLZ' , $data[0]['plz']);
      $this->app->Tpl->Set('ORT' , $data[0]['ort']);
      $this->app->Tpl->Set('STEUERNUMMER' , $data[0]['steuernummer']);

      $data[0]['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='{$data[0]['projekt']}' LIMIT 1");
      $this->app->Tpl->Set('PROJEKT' , $data[0]['projekt']);

      $this->app->Tpl->Set('STANDARDVERSANDDRUCKER' , $this->app->erp->GetSelectDrucker($data[0]['standardversanddrucker']));
      $this->app->Tpl->Set('STANDARDETIKETTENDRUCKER' , $this->app->erp->GetSelectEtikettenDrucker($data[0]['standardetikettendrucker']));
      $this->app->Tpl->Set('ETIKETTENDRUCKERWARENEINGANG' , $this->app->erp->GetSelectEtikettenDrucker($data[0]['etikettendrucker_wareneingang']));
      $this->app->Tpl->Set('AUFGABEN_BONDRUCKER' , $this->app->erp->GetSelectBonDrucker($data[0]['aufgaben_bondrucker']));

      if($this->app->Conf->WFcloud==true)
      {
        $this->app->Tpl->Set('LIZENZ',"Cloud Lizenz");
        $this->app->Tpl->Set('SCHLUESSEL',"Cloud Schluessel");
      }
      
      $freifeldtyp = array('einzeilig'=>'einzeilig','mehrzeilig'=>'mehrzeilig','datum'=>'Datum','checkbox' => 'Checkbox','select'=>'Selectfeld');
      $adressefreifeldspalten = array('0'=>'keine','1'=>'1','2'=>'2');
      $projektfreifeldspalten = array('0'=>'keine','1'=>'1');


      for($i = 1; $i <= 40; $i++) {
        $fftyp = "";
        $ffspalte ="";
        foreach($freifeldtyp as $k => $v) {
          $fftyp .= '<option value="' . $k . '"' . ($k == $data[0]['freifeld' . $i . 'typ'] ? ' selected="selected" ' : '') . '>' . $v . '</option>';
        }
        foreach($adressefreifeldspalten as $k => $v) {
          $ffspalte .= '<option value="' . $k . '"' . ($k == $data[0]['freifeld' . $i . 'spalte'] ? ' selected="selected" ' : '') . '>' . $v . '</option>';
        }
        $tr =  '<tr><td width="100">Freifeld '.$i.':</td><td><input type="text" name="freifeld'.$i.'" size="40" value="[FREIFELD'.$i.']" data-lang="artikel_freifeld'.$i.'"></td>
          <td><select name="freifeld'.$i.'typ">'.$fftyp.'</select></td><td><select name="freifeld'.$i.'spalte">'.$ffspalte.'</select></td><td><input type="text" name="freifeld'.$i.'sort" size="3" value="[FREIFELD'.$i.'SORT]"></td>';
        $tr .='<td height="28"><input type="checkbox" value="1" name="freifeld'.$i.'an" [FREIFELD'.$i.'AN]></td><td><input type="checkbox" value="1" name="freifeld'.$i.'ab" [FREIFELD'.$i.'AB]></td><td><input type="checkbox" value="1" name="freifeld'.$i.'re" [FREIFELD'.$i.'RE]></td><td><input type="checkbox" value="1" name="freifeld'.$i.'gs" [FREIFELD'.$i.'GS]></td><td><input type="checkbox" value="1" name="freifeld'.$i.'ls" [FREIFELD'.$i.'LS]></td><td><input type="checkbox" value="1" name="freifeld'.$i.'be" [FREIFELD'.$i.'BE]></td><td><input type="checkbox" value="1" name="freifeld'.$i.'pr" [FREIFELD'.$i.'PR]></td><td><input type="checkbox" value="1" name="freifeld'.$i.'pa" [FREIFELD'.$i.'PA]></td><td><input type="checkbox" value="1" name="freifeld'.$i.'pd" [FREIFELD'.$i.'PD]></td></tr>';

        $this->app->Tpl->Add('ARTIKELFREIFELDER',$tr);
      }
      
      $artikelzusatzfelder = $this->app->erp->getZusatzfelderArtikel();
      $adressezusatzfelder = $this->app->erp->getZusatzfelderAdresse();
      $auftragzusatzfelder = $this->app->erp->getZusatzfelderAuftrag();
      $rechnungzusatzfelder = $this->app->erp->getZusatzfelderRechnung();
      $lieferscheinzusatzfelder = $this->app->erp->getZusatzfelderLieferschein();
      $produktionzusatzfelder = $this->app->erp->getZusatzfelderProduktion();
      $bestellungzusatzfelder = $this->app->erp->getZusatzfelderBestellung();
      
      for($i = 1; $i <= 5; $i++)
      {
        foreach($artikelzusatzfelder as $k => $v)
        {
          $this->app->Tpl->Add('SELARTIKELTABELLEZUSATZ'.$i,'<option value="'.$k.'"'.($k == $data[0]['artikeltabellezusatz'.$i]?' selected="selected" ':'').'>'.$v.'</option>');
        }
        foreach($adressezusatzfelder as $k => $v)
        {
          $this->app->Tpl->Add('SELADRESSETABELLEZUSATZ'.$i,'<option value="'.$k.'"'.($k == $data[0]['adressetabellezusatz'.$i]?' selected="selected" ':'').'>'.$v.'</option>');
        }
        foreach($auftragzusatzfelder as $k => $v)
        {
          $this->app->Tpl->Add('SELAUFTRAGTABELLEZUSATZ'.$i,'<option value="'.$k.'"'.($k == $data[0]['auftragtabellezusatz'.$i]?' selected="selected" ':'').'>'.$v.'</option>');
        }
        foreach($rechnungzusatzfelder as $k => $v)
        {
          $this->app->Tpl->Add('SELRECHNUNGTABELLEZUSATZ'.$i,'<option value="'.$k.'"'.($k == $data[0]['rechnungtabellezusatz'.$i]?' selected="selected" ':'').'>'.$v.'</option>');
        }
        foreach($lieferscheinzusatzfelder as $k => $v)
        {
          $this->app->Tpl->Add('SELLIEFERSCHEINTABELLEZUSATZ'.$i,'<option value="'.$k.'"'.($k == $data[0]['lieferscheintabellezusatz'.$i]?' selected="selected" ':'').'>'.$v.'</option>');
        }
        foreach($produktionzusatzfelder as $k => $v)
        {
          $this->app->Tpl->Add('SELPRODUKTIONTABELLEZUSATZ'.$i,'<option value="'.$k.'"'.($k == $data[0]['produktiontabellezusatz'.$i]?' selected="selected" ':'').'>'.$v.'</option>');
        }
        foreach($bestellungzusatzfelder as $k => $v)
        {
          $this->app->Tpl->Add('SELBESTELLUNGTABELLEZUSATZ'.$i,'<option value="'.$k.'"'.($k == $data[0]['bestellungtabellezusatz'.$i]?' selected="selected" ':'').'>'.$v.'</option>');
        }

      }

      for($i = 1; $i <= 20; $i++) {
        foreach($freifeldtyp as $k => $v) {
          $this->app->Tpl->Add('PROJEKTFREIFELD'.$i.'TYP','<option value="'.$k.'"'.($k == $data[0]['projektfreifeld'.$i.'typ']?' selected="selected" ':'').'>'.$v.'</option>');
        }
        foreach($projektfreifeldspalten as $k => $v) {
          $this->app->Tpl->Add('PROJEKTFREIFELD'.$i.'SPALTE','<option value="'.$k.'"'.($k == $data[0]['projektfreifeld'.$i.'spalte']?' selected="selected" ':'').'>'.$v.'</option>');
        } 
      }
      
      for($i = 1; $i <= 20; $i++) {
        foreach($freifeldtyp as $k => $v) {
          $this->app->Tpl->Add('ADRESSEFREIFELD'.$i.'TYP','<option value="'.$k.'"'.($k == $data[0]['adressefreifeld'.$i.'typ']?' selected="selected" ':'').'>'.$v.'</option>');
        }
        foreach($adressefreifeldspalten as $k => $v) {
          $this->app->Tpl->Add('ADRESSEFREIFELD'.$i.'SPALTE','<option value="'.$k.'"'.($k == $data[0]['adressefreifeld'.$i.'spalte']?' selected="selected" ':'').'>'.$v.'</option>');
        } 
      }
      
      $produktionsverhalten = $data[0]['produktionsverhalten']; 
      $produktionsverhaltenarr['explodieren'] = '{|Unterst&uuml;cklisten aufl&ouml;sen|}';
      $produktionsverhaltenarr['ersteebene'] = '{|Unterst&uuml;cklisten nicht aufl&ouml;sen|}';
      if($produktionsverhalten === 'unterproduktionen' || $this->app->erp->ModulVorhanden('unterproduktionen')) {
        $produktionsverhaltenarr['unterproduktionen'] = '{|Unterproduktionen anlegen|}';
      }
      foreach($produktionsverhaltenarr as $k => $v) {
        $this->app->Tpl->Add('PRODUKTIONSVERHALTEN','<option value="'.$k.'"'.($k == $produktionsverhalten?' selected="selected" ':'').'>'.$v.'</option>');
      }

      if(!empty($data[0]['taxfromdoctypesettings'])) {
        $this->app->Tpl->Set('OPTIONTAXFROMDOCTYPESETTINGS', ' selected="selected" ');
      }
    }
  }

  /**
   * @param array $data
   */
  function fillForm($data)
  {
    //Brief Absender
    $this->app->Tpl->Set('ABSENDER' , $data['absender']);    
    $this->app->Tpl->Set('SICHTBAR' , $this->parseCheckbox($data['sichtbar']));
    $this->app->Tpl->Set('RECHNUNG_GUTSCHRIFT_ANSPRECHPARTNER' , $this->parseCheckbox($data['rechnung_gutschrift_ansprechpartner']));
    $this->app->Tpl->Set('KNICKFALZ' , $this->parseCheckbox($data['knickfalz']));
    $this->app->Tpl->Set('STANDARDAUFLOESUNG' , $this->parseCheckbox($data['standardaufloesung']));
    $this->app->Tpl->Set('ARTIKELSUCHEKURZTEXT' , $this->parseCheckbox($data['artikel_suche_kurztext']));
    $this->app->Tpl->Set('ARTIKELSUCHEVARIANTEVON' , $this->parseCheckbox($data['artikel_suche_variante_von']));
    $this->app->Tpl->Set('ADRESSE_FREITEXT1_SUCHE' , $this->parseCheckbox($data['adresse_freitext1_suche']));
    $this->app->Tpl->Set('ADRESSE_FREITEXT1_SUCHE' , $this->parseCheckbox($data['adresse_freitext1_suche']));
    $this->app->Tpl->Set('PARAMETERUNDFREIFELDER' , $this->parseCheckbox($data['parameterundfreifelder']));
    for($in = 1; $in <= 40; $in++) {
      $this->app->Tpl->Set('FREIFELD'.$in , $data['freifeld'.$in]);
    }
    for($in = 1; $in <= 20; $in++) {
      $this->app->Tpl->Set('PROJEKTFREIFELD'.$in , $data['projektfreifeld'.$in]);
    }
    $this->app->Tpl->Set('STANDARD_DATENSAETZE_DATATABLES' , $data['standard_datensaetze_datatables']);    
    $this->app->Tpl->Set('STEUERSATZNORMAL' , $data['steuersatz_normal']);    
    $this->app->Tpl->Set('STEUERSATZERMAESSIGT' , $data['steuersatz_ermaessigt']);
    $this->app->Tpl->Set('LIZENZ' , $data['lizenz']);    
    $this->app->Tpl->Set('SCHLUESSEL' , $data['schluessel']);    
    $this->app->Tpl->Set('BRANCH' , $data['branch']);    
    $this->app->Tpl->Set('VERSION' , $data['version']);    

    $this->app->Tpl->Set('WARNUNG_DOPPELTE_NUMMERN' , $this->parseCheckbox($data['warnung_doppelte_nummern']));
    $this->app->Tpl->Set('WARENEINGANG_ZWISCHENLAGER' , $this->parseCheckbox($data['wareneingang_zwischenlager']));

    $this->app->Tpl->Set('BOXAUSRICHTUNG' , $data['boxausrichtung']);    
    $this->app->Tpl->Set('FOOTERBREITE1' , $data['footer_breite1']);    
    $this->app->Tpl->Set('FOOTERBREITE2' , $data['footer_breite2']);    
    $this->app->Tpl->Set('FOOTERBREITE3' , $data['footer_breite3']);    
    $this->app->Tpl->Set('FOOTERBREITE4' , $data['footer_breite4']);    

    //Formatierung
    $this->app->Tpl->Set('BARCODE' , $this->parseCheckbox($data['barcode']));    
    $this->app->Tpl->Set('SCHRIFTGROESSE' , ($data['schriftgroesse']));    
    $this->app->Tpl->Set('BETREFFSZEILE' , ($data['betreffszeile']));    
    $this->app->Tpl->Set('DOKUMENTENTEXT' , ($data['dokumententext']));    
    $this->app->Tpl->Set('TABELLENBESCHRIFTUNG' , ($data['tabellenbeschriftung']));    
    $this->app->Tpl->Set('TABELLENINHALT' , ($data['tabelleninhalt']));    
    $this->app->Tpl->Set('ZEILENUNTERTEXT' , ($data['zeilenuntertext']));    
    $this->app->Tpl->Set('FREITEXT' , ($data['freitext']));    
    $this->app->Tpl->Set('BRIFTEXT' , ($data['brieftext']));    
    $this->app->Tpl->Set('INFOBOX' , ($data['infobox']));    
    $this->app->Tpl->Set('SPALTENBREITE' , ($data['spaltenbreite']));    

    $this->app->Tpl->Set('ABSTANDADRESSZEILEOBEN' , ($data['abstand_adresszeileoben']));    
    $this->app->Tpl->Set('ABSTANDBOXRECHTSOBEN' , ($data['abstand_boxrechtsoben']));    
    $this->app->Tpl->Set('ABSTANDBOXRECHTSOBENLR' , ($data['abstand_boxrechtsoben_lr']));    
    $this->app->Tpl->Set('ABSTANDBETREFFZEILEOBEN' , ($data['abstand_betreffzeileoben']));    
    $this->app->Tpl->Set('ABSTANDARTIKELTABELLEOBEN' , ($data['abstand_artikeltabelleoben']));    
    $this->app->Tpl->Set('ABSTANDNAMEBESCHREIBUNG' , ($data['abstand_name_beschreibung']));    
    $this->app->Tpl->Set('ARTIKELEINHEIT_STANDARD' , ($data['artikeleinheit_standard']));    
    $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_BEARBEITER', ($data['auftrag_bezeichnung_bearbeiter']));    
    $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_VERTRIEB', ($data['auftrag_bezeichnung_vertrieb']));    
    $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_BESTELLNUMMER', ($data['auftrag_bezeichnung_bestellnummer']));    
    $this->app->Tpl->Set('BEZEICHNUNGKUNDENNUMMER', ($data['bezeichnungkundennummer']));    

    //Footer
    for($x=0; $x < 4; $x++) {
      for ($y = 0; $y < 6; $y++) {
        $this->app->Tpl->Set('FOOTER'.$x.$y, $data['footer'][$x][$y]);
      }
    }

    $this->app->Tpl->Set('FOOTERSICHTBAR' , $this->parseCheckbox($data['footersichtbar']));    
    $this->app->Tpl->Set('STANDARDAUFLOESUNG' , $this->parseCheckbox($data['standardaufloesung']));    
    $this->app->Tpl->Set('BRIEFPAPIER2VORHANDEN' , $this->parseCheckbox($data['briefpapier2vorhanden']));    
    $this->app->Tpl->Set('SEITEVONSICHTBAR' , $this->parseCheckbox($data['seite_von_sichtbar']));    
    $this->app->Tpl->Set('SEITEVONAUSRICHTUNG' , $data['seite_von_ausrichtung']);    
    $this->app->Tpl->Set('ANGEBOT_OHNEBRIEFPAPIER' , $this->parseCheckbox($data['angebot_ohnebriefpapier']));
    $this->app->Tpl->Set('AUFTRAG_OHNEBRIEFPAPIER' , $this->parseCheckbox($data['auftrag_ohnebriefpapier']));
    $this->app->Tpl->Set('RECHNUNG_OHNEBRIEFPAPIER' , $this->parseCheckbox($data['rechnung_ohnebriefpapier']));
    $this->app->Tpl->Set('LIEFERSCHEIN_OHNEBRIEFPAPIER' , $this->parseCheckbox($data['lieferschein_ohnebriefpapier']));
    $this->app->Tpl->Set('GUTSCHRIFT_OHNEBRIEFPAPIER' , $this->parseCheckbox($data['gutschrift_ohnebriefpapier']));
    $this->app->Tpl->Set('BESTELLUNG_OHNEBRIEFPAPIER' , $this->parseCheckbox($data['bestellung_ohnebriefpapier']));
    $this->app->Tpl->Set('ARBEITSNACHWEIS_OHNEBRIEFPAPIER' , $this->parseCheckbox($data['arbeitsnachweis_ohnebriefpapier']));
    $this->app->Tpl->Set('EXTERNEREINKAUF' , $this->parseCheckbox($data['externereinkauf']));

    $this->app->Tpl->Set('PROJEKTNUMMERIMDOKUMENT' , $this->parseCheckbox($data['projektnummerimdokument']));
    $this->app->Tpl->Set('MAILANSTELLESMTP' , $this->parseCheckbox($data['mailanstellesmtp']));
    $this->app->Tpl->Set('HERSTELLERNUMMERIMDOKUMENT' , $this->parseCheckbox($data['herstellernummerimdokument']));
    $this->app->Tpl->Set('ARTIKELEINHEIT' , $this->parseCheckbox($data['artikeleinheit']));
    $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_BEARBEITER' , $data['auftrag_bezeichnung_bearbeiter']);
    $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_VERTRIEB' , $data['auftrag_bezeichnung_vertrieb']);
    $this->app->Tpl->Set('AUFTRAG_BEZEICHNUNG_BESTELLNUMMER' , $data['auftrag_bezeichnung_bestellnummer']);
    $this->app->Tpl->Set('BEZEICHNUNGNUMMER' , $data['bezeichnungkundennummer']);
    $this->app->Tpl->Set('STANDARDMARGE' , $data['standardmarge']);
    $this->app->Tpl->Set('STANDARDVERSANDDRUCKER' , $this->app->erp->GetSelectDrucker($data['standardversanddrucker']));
    $this->app->Tpl->Set('STANDARDETIKETTENDRUCKER' , $this->app->erp->GetSelectEtikettenDrucker($data['standardetikettendrucker']));
    $this->app->Tpl->Set('ETIKETTENDRUCKERWARENEINGANG' , $this->app->erp->GetSelectEtikettenDrucker($data['etikettendrucker_wareneingang']));
    $this->app->Tpl->Set('AUFGABEN_BONDRUCKER' , $this->app->erp->GetSelectBonDrucker($data['aufgaben_bondrucker']));

    $this->app->Tpl->Set('SCHRIFTART' , $data['schriftart']);

    $this->app->Tpl->Set('FIRMENFARBEHELL',$data['firmenfarbehell']);
    $this->app->Tpl->Set('FIRMENFARBEDUNKEL',$data['firmenfarbedunkel']);
    $this->app->Tpl->Set('FIRMENFARBEGANZDUNKEL',$data['firmenfarbeganzdunkel']);
    $this->app->Tpl->Set('NAVIGATIONFARBE',$data['navigationfarbe']);
    $this->app->Tpl->Set('NAVIGATIONFARBESCHRIFT',$data['navigationfarbeschrift']);
    $this->app->Tpl->Set('TABSNAVIGATIONFARBE',$data['tabsnavigationfarbe']);
    $this->app->Tpl->Set('TABSNAVIGATIONFARBESCHRIFT',$data['tabsnavigationfarbeschrift']);
    $this->app->Tpl->Set('UNTERNAVIGATIONFARBE',$data['unternavigationfarbe']);
    $this->app->Tpl->Set('UNTERNAVIGATIONFARBESCHRIFT',$data['unternavigationfarbeschrift']);

    $this->app->Tpl->Set('ZAHLUNG_RECHNUNG' , $this->parseCheckbox($data['zahlung_rechnung']));
    $this->app->Tpl->Set('ZAHLUNG_VORKASSE' , $this->parseCheckbox($data['zahlung_vorkasse']));
    $this->app->Tpl->Set('ZAHLUNG_NACHNAHME' , $this->parseCheckbox($data['zahlung_nachnahme']));
    $this->app->Tpl->Set('ZAHLUNG_LASTSCHRIFT' , $this->parseCheckbox($data['zahlung_lastschrift']));
    $this->app->Tpl->Set('ZAHLUNG_BAR' , $this->parseCheckbox($data['zahlung_bar']));
    $this->app->Tpl->Set('ZAHLUNG_KREDITKARTE' , $this->parseCheckbox($data['zahlung_kreditkarte']));
    $this->app->Tpl->Set('ZAHLUNG_PAYPAL' , $this->parseCheckbox($data['zahlung_paypal']));
    $this->app->Tpl->Set('ZAHLUNG_AMAZON' , $this->parseCheckbox($data['zahlung_amazon']));
    $this->app->Tpl->Set('ZAHLUNG_RATENZAHLUNG' , $this->parseCheckbox($data['zahlung_ratenzahlung']));
    $this->app->Tpl->Set('KLEINUNTERNEHMER' , $this->parseCheckbox($data['kleinunternehmer']));
    $this->app->Tpl->Set('STEUER_POSITIONEN_EXPORT' , $this->parseCheckbox($data['steuer_positionen_export']));
    
    $this->app->Tpl->Set('SCHNELLANLEGEN' , $this->parseCheckbox($data['schnellanlegen']));
    $this->app->Tpl->Set('BESTELLVORSCHLAGSGROESSERNULL' , $this->parseCheckbox($data['bestellvorschlaggroessernull']));
    $this->app->Tpl->Set('IMMERNETTORECHNUNGEN' , $this->parseCheckbox($data['immernettorechnungen']));

    $this->app->Tpl->Set('ZAHLUNG_RECHNUNG_SOFORT_DE', $data['zahlung_rechnung_sofort_de']);
    $this->app->Tpl->Set('ZAHLUNG_RECHNUNG_DE', $data['zahlung_rechnung_de']);


    $fields = $this->app->erp->GetFirmaFields();
    foreach($fields as $key) {
      $this->app->Tpl->Set(strtoupper($key), $data[$key]);
    }

    $fields_checkbox = $this->app->erp->GetFirmaFieldsCheckbox();
    foreach($fields_checkbox as $key) {
      $this->app->Tpl->Set(strtoupper($key), $this->parseCheckbox($data[$key]));
    }

    $this->app->Tpl->Set('ZAHLUNG_VORKASSE_DE', $data['zahlung_vorkasse_de']);
    $this->app->Tpl->Set('ZAHLUNG_NACHNAHME_DE', $data['zahlung_nachnahme_de']);
    $this->app->Tpl->Set('ZAHLUNG_LASTSCHRIFT_DE', $data['zahlung_lastschrift_de']);
    $this->app->Tpl->Set('ZAHLUNG_AMAZON_DE', $data['zahlung_amazon_de']);
    $this->app->Tpl->Set('ZAHLUNG_BAR_DE', $data['zahlung_bar_de']);
    $this->app->Tpl->Set('ZAHLUNG_PAYPAL_DE', $data['zahlung_paypal_de']);
    $this->app->Tpl->Set('ZAHLUNG_KREDITKARTE_DE', $data['zahlung_kreditkarte_de']);
    $this->app->Tpl->Set('ZAHLUNG_RATENZAHLUNG_DE', $data['zahlung_ratenzahlung_de']);

    $this->app->Tpl->Set('ZAHLUNGSZIELTAGE', $data['zahlungszieltage']);
    $this->app->Tpl->Set('ZAHLUNGSZIELTAGESKONTO', $data['zahlungszieltageskonto']);
    $this->app->Tpl->Set('ZAHLUNGSZIELSKONTO', $data['zahlungszielskonto']);

    $this->app->Tpl->Set('VERSANDART', $this->app->erp->GetSelectAsso($this->app->erp->GetVersandartAuftrag(),$data['versandart']));    
    $this->app->Tpl->Set('ZAHLUNGSWEISE', $this->app->erp->GetSelectAsso($this->app->erp->GetZahlungsweise(),$data['zahlungsweise']));    
    $this->app->Tpl->Set('ZAHLUNGSWEISELIEFERANT', $this->app->erp->GetSelectAsso($this->app->erp->GetZahlungsweise(),$data['zahlungsweiselieferant']));

    $this->app->Tpl->Set('RECHNUNG_HEADER' , $data['rechnung_header']);
    $this->app->Tpl->Set('LIEFERSCHEIN_HEADER' , $data['lieferschein_header']);
    $this->app->Tpl->Set('GUTSCHRIFT_HEADER' , $data['gutschrift_header']);
    $this->app->Tpl->Set('ANGEBOT_HEADER' , $data['angebot_header']);
    $this->app->Tpl->Set('AUFTRAG_HEADER' , $data['auftrag_header']);
    $this->app->Tpl->Set('BESTELLUNG_HEADER' , $data['bestellung_header']);
    $this->app->Tpl->Set('ARBEITSNACHWEIS_HEADER' , $data['arbeitsnachweis_header']);
    $this->app->Tpl->Set('PROVISIONSGUTSCHRIFT_HEADER' , $data['provisionsgutschrift_header']);
    $this->app->Tpl->Set('PROFORMARECHNUNG_HEADER' , $data['proformarechnung_header']);

    $this->app->Tpl->Set('RECHNUNG_FOOTER' , $data['rechnung_footer']);
    $this->app->Tpl->Set('LIEFERSCHEIN_FOOTER' , $data['lieferschein_footer']);
    $this->app->Tpl->Set('GUTSCHRIFT_FOOTER' , $data['gutschrift_footer']);
    $this->app->Tpl->Set('ANGEBOT_FOOTER' , $data['angebot_footer']);
    $this->app->Tpl->Set('AUFTRAG_FOOTER' , $data['auftrag_footer']);
    $this->app->Tpl->Set('BESTELLUNG_FOOTER' , $data['bestellung_footer']);
    $this->app->Tpl->Set('ARBEITSNACHWEIS_FOOTER' , $data['arbeitsnachweis_footer']);
    $this->app->Tpl->Set('PROVISIONSGUTSCHRIFT_FOOTER' , $data['provisionsgutschrift_footer']);
    $this->app->Tpl->Set('PROFORMARECHNUNG_FOOTER' , $data['proformarechnung_footer']);
    $this->app->Tpl->Set('EU_LIEFERUNG_VERMERK' , $data['eu_lieferung_vermerk']);
    $this->app->Tpl->Set('EXPORT_LIEFERUNG_VERMERK' , $data['export_lieferung_vermerk']);

    $this->app->Tpl->Set('WARENEINGANG_KAMERA_WAAGE' , $this->parseCheckbox($data['wareneingang_kamera_waage']));    
    $this->app->Tpl->Set('LAYOUT_ICONBAR' , $this->parseCheckbox($data['layout_iconbar']));    

    $this->app->Tpl->Set('NEXT_ANGEBOT' , ($data['next_angebot']));    
    $this->app->Tpl->Set('NEXT_AUFTRAG' , ($data['next_auftrag']));    
    $this->app->Tpl->Set('NEXT_RECHNUNG' , ($data['next_rechnung']));    
    $this->app->Tpl->Set('NEXT_LIEFERSCHEIN' , ($data['next_lieferschein']));
    $this->app->Tpl->Set('NEXT_RETOURE' , ($data['next_retoure']));
    $this->app->Tpl->Set('NEXT_BESTELLUNG' , ($data['next_bestellung']));    
    $this->app->Tpl->Set('NEXT_ARBEITSNACHWEIS' , ($data['next_arbeitsnachweis']));    
    $this->app->Tpl->Set('NEXT_GUTSCHRIFT' , ($data['next_gutschrift']));    
    $this->app->Tpl->Set('NEXT_KUNDENNUMMER' , ($data['next_kundennummer']));
    $this->app->Tpl->Set('NEXT_PROJEKTNUMMER' , ($data['next_projektnummer']));
    $this->app->Tpl->Set('NEXT_LIEFERANTENNUMMER' , ($data['next_lieferantennummer']));    
    $this->app->Tpl->Set('NEXT_MITARBEITERNUMMER' , ($data['next_mitarbeiternummer']));    
    $this->app->Tpl->Set('NEXT_ARTIKELNUMMER' , ($data['next_artikelnummer']));    
    $this->app->Tpl->Set('NEXT_WAREN' , ($data['next_waren']));    
    $this->app->Tpl->Set('NEXT_SONSTIGES' , ($data['next_sonstiges']));    
    $this->app->Tpl->Set('NEXT_PRODUKTION' , ($data['next_produktion']));    
    $this->app->Tpl->Set('NEXT_REISEKOSTEN' , ($data['next_reisekosten']));    
    $this->app->Tpl->Set('NEXT_PROFORMARECHNUNG' , ($data['next_proformarechnung']));    
    $this->app->Tpl->Set('NEXT_ANFRAGE' , ($data['next_anfrage']));    
    $this->app->Tpl->Set('NEXT_PREISANFRAGE' , ($data['next_preisanfrage']));
    $this->app->Tpl->Set('NEXT_VERBINDLICHKEIT', ($data['next_verbindlichkeit']));
    $this->app->Tpl->Set('NEXT_RECEIPTDOCUMENT', ($data['next_receiptdocument']));

    //Briefpapier Hintergrund
    if($data['hintergrund']==='logo') {
      $this->app->Tpl->Set('HINTERGRUNDLOGO', "checked");
      $this->app->Tpl->Set('HINTERGRUNDTEXT', "Logo");
    }
    else if($data['hintergrund']==='briefpapier'){
      $this->app->Tpl->Set('HINTERGRUNDBRIEFPAPIER', "checked");
      $this->app->Tpl->Set('HINTERGRUNDTEXT', "Briefpapier");
    }
    else{
      $this->app->Tpl->Set('HINTERGRUNDKEIN', "checked");
    }


    //Versand E-Mail
    $this->app->Tpl->Set('BENUTZERNAME' , $data['benutzername']);    
    $this->app->Tpl->Set('PASSWORT' , $data['passwort']);    
    $this->app->Tpl->Set('HOST' , $data['host']);    
    $this->app->Tpl->Set('PORT' , $data['port']);    
    if($data['mailssl']=="2")
      $this->app->Tpl->Set('SSL' ,"selected");
    else if($data['mailssl']=="1")
      $this->app->Tpl->Set('TLS' ,"selected");


    // Signatur
    $this->app->Tpl->Set('SIGNATUR' , base64_decode($data['signatur']));    
    $this->app->Tpl->Set('EMAIL' , $data['email']);    
    $this->app->Tpl->Set('ABSENDERNAME' , $data['absendername']);    
    $this->app->Tpl->Set('BCC1' , $data['bcc1']);    
    $this->app->Tpl->Set('BCC2' , $data['bcc2']);    
    $this->app->Tpl->Set('FIRMENFARBE' , $data['firmenfarbe']);    
    $this->app->Tpl->Set('NAME' , $data['name']);    
    $this->app->Tpl->Set('STRASSE' , $data['strasse']);    
    $this->app->Tpl->Set('PLZ' , $data['plz']);    
    $this->app->Tpl->Set('ORT' , $data['ort']);    
    $this->app->Tpl->Set('STEUERNUMMER' , $data['steuernummer']);
  }

  /**
   * @return array
   */
  function getPostData()
  {
    $data = array();

    // Brief Absender
    $data['taxfromdoctypesettings'] = $this->app->Secure->GetPOST('taxfromdoctypesettings');
    $data['absender'] = $this->app->Secure->GetPOST("absender");
    $data['sichtbar'] = $this->parseCheckbox($this->app->Secure->GetPOST("sichtbar"));
    $data['rechnung_gutschrift_ansprechpartner'] = $this->parseCheckbox($this->app->Secure->GetPOST("rechnung_gutschrift_ansprechpartner"));
    $data['artikel_suche_kurztext'] = $this->parseCheckbox($this->app->Secure->GetPOST("artikel_suche_kurztext"));
    $data['artikel_suche_variante_von'] = $this->parseCheckbox($this->app->Secure->GetPOST("artikel_suche_variante_von"));
    $data['artikel_freitext1_suche'] = $this->parseCheckbox($this->app->Secure->GetPOST("artikel_freitext1_suche"));
    $data['adresse_freitext1_suche'] = $this->parseCheckbox($this->app->Secure->GetPOST("adresse_freitext1_suche"));
    $data['artikel_artikelnummer_suche'] = $this->parseCheckbox($this->app->Secure->GetPOST("artikel_artikelnummer_suche"));
    $data['parameterundfreifelder'] = $this->parseCheckbox($this->app->Secure->GetPOST("parameterundfreifelder"));
    for($in = 1; $in <= 40; $in++) {
      $data['freifeld'.$in] = $this->app->Secure->GetPOST("freifeld".$in);
    }
    for($in = 1; $in <= 20; $in++) {
      $data['projektfreifeld'.$in] = $this->app->Secure->GetPOST("projektfreifeld".$in);
    }
    $data['standard_datensaetze_datatables'] = $this->app->Secure->GetPOST("standard_datensaetze_datatables");
    $data['steuersatz_normal'] = $this->app->Secure->GetPOST("steuersatz_normal");
    $data['steuersatz_ermaessigt'] = $this->app->Secure->GetPOST("steuersatz_ermaessigt");
    $data['waehrung'] = $this->app->Secure->GetPOST("waehrung");
    $data['lizenz'] = $this->app->Secure->GetPOST("lizenz");
    $data['schluessel'] = $this->app->Secure->GetPOST("schluessel");
    $data['branch'] = $this->app->Secure->GetPOST("branch");

    $data['boxausrichtung'] = $this->app->Secure->GetPOST("boxausrichtung");
    $data['footer_breite1'] = $this->app->Secure->GetPOST("footer_breite1");
    $data['footer_breite2'] = $this->app->Secure->GetPOST("footer_breite2");
    $data['footer_breite3'] = $this->app->Secure->GetPOST("footer_breite3");
    $data['footer_breite4'] = $this->app->Secure->GetPOST("footer_breite4");


    // Formatierung
    $data['barcode'] = $this->parseCheckbox($this->app->Secure->GetPOST("barcode"));
    $data['schriftgroesse'] = ($this->app->Secure->GetPOST("schriftgroesse"));
    $data['betreffszeile'] = ($this->app->Secure->GetPOST("betreffszeile"));
    $data['dokumententext'] = ($this->app->Secure->GetPOST("dokumententext"));
    $data['tabellenbeschriftung'] = ($this->app->Secure->GetPOST("tabellenbeschriftung"));
    $data['tabelleninhalt'] = ($this->app->Secure->GetPOST("tabelleninhalt"));
    $data['zeilenuntertext'] = ($this->app->Secure->GetPOST("zeilenuntertext"));
    $data['freitext'] = ($this->app->Secure->GetPOST("freitext"));
    $data['brieftext'] = ($this->app->Secure->GetPOST("brieftext"));
    $data['infobox'] = ($this->app->Secure->GetPOST("infobox"));
    $data['spaltenbreite'] = ($this->app->Secure->GetPOST("spaltenbreite"));

    $data['abstand_adresszeileoben'] = $this->app->Secure->GetPOST("abstand_adresszeileoben");
    $data['abstand_boxrechtsoben'] = $this->app->Secure->GetPOST("abstand_boxrechtsoben");
    $data['abstand_boxrechtsoben_lr'] = $this->app->Secure->GetPOST("abstand_boxrechtsoben_lr");
    $data['abstand_betreffzeileoben'] = $this->app->Secure->GetPOST("abstand_betreffzeileoben");
    $data['abstand_artikeltabelleoben'] = $this->app->Secure->GetPOST("abstand_artikeltabelleoben");
    $data['abstand_name_beschreibung'] = $this->app->Secure->GetPOST("abstand_name_beschreibung");
    $data['artikeleinheit_standard'] = $this->app->Secure->GetPOST("artikeleinheit_standard");
    $data['auftrag_bezeichnung_bearbeiter'] = $this->app->Secure->GetPOST("auftrag_bezeichnung_bearbeiter");
    $data['auftrag_bezeichnung_vertrieb'] = $this->app->Secure->GetPOST("auftrag_bezeichnung_vertrieb");
    $data['auftrag_bezeichnung_bestellnummer'] = $this->app->Secure->GetPOST("auftrag_bezeichnung_bestellnummer");
    $data['bezeichnungkundennummer'] = $this->app->Secure->GetPOST("bezeichnungkundennummer");


    // Footer
    $data['footer'] = $this->app->Secure->GetPOST("footer");
    $data['footersichtbar'] = $this->parseCheckbox($this->app->Secure->GetPOST("footersichtbar"));
    $data['briefpapier2vorhanden'] = $this->parseCheckbox($this->app->Secure->GetPOST("briefpapier2vorhanden"));
    $data['seite_von_sichtbar'] = $this->parseCheckbox($this->app->Secure->GetPOST("seite_von_sichtbar"));
    $data['seite_von_ausrichtung'] = $this->app->Secure->GetPOST("seite_von_ausrichtung");

    $data['angebot_ohnebriefpapier'] = $this->parseCheckbox($this->app->Secure->GetPOST("angebot_ohnebriefpapier"));
    $data['auftrag_ohnebriefpapier'] = $this->parseCheckbox($this->app->Secure->GetPOST("auftrag_ohnebriefpapier"));
    $data['rechnung_ohnebriefpapier'] =  $this->parseCheckbox($this->app->Secure->GetPOST("rechnung_ohnebriefpapier"));
    $data['lieferschein_ohnebriefpapier'] =  $this->parseCheckbox($this->app->Secure->GetPOST("lieferschein_ohnebriefpapier"));
    $data['gutschrift_ohnebriefpapier'] = $this->parseCheckbox($this->app->Secure->GetPOST("gutschrift_ohnebriefpapier"));
    $data['bestellung_ohnebriefpapier'] = $this->parseCheckbox($this->app->Secure->GetPOST("bestellung_ohnebriefpapier"));
    $data['arbeitsnachweis_ohnebriefpapier'] = $this->parseCheckbox($this->app->Secure->GetPOST("arbeitsnachweis_ohnebriefpapier"));
    $data['externereinkauf'] = $this->parseCheckbox($this->app->Secure->GetPOST("externereinkauf"));

    $data['projektnummerimdokument'] = $this->parseCheckbox($this->app->Secure->GetPOST("projektnummerimdokument"));
    $data['mailanstellesmtp'] = $this->parseCheckbox($this->app->Secure->GetPOST("mailanstellesmtp"));
    $data['herstellernummerimdokument'] = $this->parseCheckbox($this->app->Secure->GetPOST("herstellernummerimdokument"));
    $data['artikeleinheit'] = $this->parseCheckbox($this->app->Secure->GetPOST("artikeleinheit"));
    $data['standardmarge'] = $this->app->Secure->GetPOST("standardmarge");
    $data['auftrag_bezeichnung_bearbeiter'] = $this->app->Secure->GetPOST("auftrag_bezeichnung_bearbeiter");
    $data['auftrag_bezeichnung_vertrieb'] = $this->app->Secure->GetPOST("auftrag_bezeichnung_vertrieb");
    $data['bezeichnungkundennummer'] = $this->app->Secure->GetPOST("bezeichnungkundennummer");
    $data['auftrag_bezeichnung_bestellnummer'] = $this->app->Secure->GetPOST("auftrag_bezeichnung_bestellnummer");

    $data['schriftart'] = $this->app->Secure->GetPOST("schriftart");
    $fields_checkbox = $this->app->erp->GetFirmaFieldsCheckbox();
    foreach($fields_checkbox as $key=>$value) {
      $data[$value] = $this->parseCheckbox($this->app->Secure->GetPOST($value));
    }

    $fields = $this->app->erp->GetFirmaFields();
    foreach($fields as $key) {
      $data[$key] = $this->app->Secure->GetPOST($key);
    }

    $data['rechnung_header'] = $this->app->Secure->GetPOST("rechnung_header");
    $data['rechnung_footer'] = $this->app->Secure->GetPOST("rechnung_footer");
    $data['angebot_header'] = $this->app->Secure->GetPOST("angebot_header");
    $data['angebot_footer'] = $this->app->Secure->GetPOST("angebot_footer");
    $data['auftrag_header'] = $this->app->Secure->GetPOST("auftrag_header");
    $data['auftrag_footer'] = $this->app->Secure->GetPOST("auftrag_footer");
    $data['lieferschein_header'] = $this->app->Secure->GetPOST("lieferschein_header");
    $data['lieferschein_footer'] = $this->app->Secure->GetPOST("lieferschein_footer");
    $data['bestellung_header'] = $this->app->Secure->GetPOST("bestellung_header");
    $data['arbeitsnachweis_header'] = $this->app->Secure->GetPOST("arbeitsnachweis_header");
    $data['bestellung_footer'] = $this->app->Secure->GetPOST("bestellung_footer");
    $data['arbeitsnachweis_footer'] = $this->app->Secure->GetPOST("arbeitsnachweis_footer");
    $data['gutschrift_header'] = $this->app->Secure->GetPOST("gutschrift_header");
    $data['gutschrift_footer'] = $this->app->Secure->GetPOST("gutschrift_footer");
    $data['provisionsgutschrift_header'] = $this->app->Secure->GetPOST("provisionsgutschrift_header");
    $data['provisionsgutschrift_footer'] = $this->app->Secure->GetPOST("provisionsgutschrift_footer");
    $data['proformarechnung_header'] = $this->app->Secure->GetPOST("proformarechnung_header");
    $data['proformarechnung_footer'] = $this->app->Secure->GetPOST("proformarechnung_footer");
    $data['eu_lieferung_vermerk'] = $this->app->Secure->GetPOST("eu_lieferung_vermerk");
    $data['export_lieferung_vermerk'] = $this->app->Secure->GetPOST("export_lieferung_vermerk");


    // Briefpapier Hintergrund
    $data['logo'] = $this->app->Secure->GetPOST("logo");
    $data['briefpapier'] = $this->app->Secure->GetPOST("briefpapier");
    $data['hintergrund'] = $this->app->Secure->GetPOST("hintergrund");

    // Versand E-Mail
    $data['benutzername'] = $this->app->Secure->GetPOST("benutzername");
    $data['passwort'] = $this->app->Secure->GetPOST("passwort");
    $data['host'] = $this->app->Secure->GetPOST("host");
    $data['port'] = $this->app->Secure->GetPOST("port");

    $data['mailssl'] = $this->app->Secure->GetPOST("mailssl");

    // Signatur
    $data['signatur'] = base64_encode($this->app->Secure->POST["signatur"]);
    $data['email'] = ($this->app->Secure->POST["email"]);
    $data['absendername'] = ($this->app->Secure->POST["absendername"]);
    $data['bcc1'] = ($this->app->Secure->POST["bcc1"]);
    $data['bcc2'] = ($this->app->Secure->POST["bcc2"]);
    $data['name'] = ($this->app->Secure->POST["name"]);
    $data['firmenfarbe'] = ($this->app->Secure->POST["firmenfarbe"]);
    $data['strasse'] = ($this->app->Secure->POST["strasse"]);
    $data['plz'] = ($this->app->Secure->POST["plz"]);
    $data['ort'] = ($this->app->Secure->POST["ort"]);
    $data['steuernummer'] = ($this->app->Secure->POST["steuernummer"]);
    $data['projekt'] = ($this->app->Secure->POST["projekt"]);

    $data['standardversanddrucker'] = ($this->app->Secure->POST["standardversanddrucker"]);
    $data['standardetikettendrucker'] = ($this->app->Secure->POST["standardetikettendrucker"]);
    $data['etikettendrucker_wareneingang'] = ($this->app->Secure->POST["etikettendrucker_wareneingang"]);
    $data['aufgaben_bondrucker'] = ($this->app->Secure->POST["aufgaben_bondrucker"]);

    $data['wareneingang_kamera_waage'] =$this->parseCheckbox ($this->app->Secure->POST["wareneingang_kamera_waage"]);
    $data['layout_iconbar'] =$this->parseCheckbox ($this->app->Secure->POST["layout_iconbar"]);

    $data['next_angebot'] = ($this->app->Secure->POST["next_angebot"]);
    $data['next_auftrag'] = ($this->app->Secure->POST["next_auftrag"]);
    $data['next_lieferschein'] = ($this->app->Secure->POST["next_lieferschein"]);
    $data['next_retoure'] = ($this->app->Secure->POST["next_retoure"]);
    $data['next_rechnung'] = ($this->app->Secure->POST["next_rechnung"]);
    $data['next_bestellung'] = ($this->app->Secure->POST["next_bestellung"]);
    $data['next_arbeitsnachweis'] = ($this->app->Secure->POST["next_arbeitsnachweis"]);
    $data['next_gutschrift'] = ($this->app->Secure->POST["next_gutschrift"]);
    $data['next_kundennummer'] = ($this->app->Secure->POST["next_kundennummer"]);
    $data['next_lieferantennummer'] = ($this->app->Secure->POST["next_lieferantennummer"]);
    $data['next_mitarbeiternummer'] = ($this->app->Secure->POST["next_mitarbeiternummer"]);
    $data['next_artikelnummer'] = ($this->app->Secure->POST["next_artikelnummer"]);
    $data['next_projektnummer'] = ($this->app->Secure->POST["next_projektnummer"]);
    $data['next_waren'] = ($this->app->Secure->POST["next_waren"]);
    $data['next_sonstiges'] = ($this->app->Secure->POST["next_sonstiges"]);
    $data['next_produktion'] = ($this->app->Secure->POST["next_produktion"]);
    $data['next_reisekosten'] = ($this->app->Secure->POST["next_reisekosten"]);
    $data['next_proformarechnung'] = ($this->app->Secure->POST["next_proformarechnung"]);
    $data['next_anfrage'] = ($this->app->Secure->POST["next_anfrage"]);
    $data['next_preisanfrage'] = ($this->app->Secure->POST["next_preisanfrage"]);
    $data['next_verbindlichkeit'] = ($this->app->Secure->POST["next_verbindlichkeit"]);
    $data['produktionsverhalten'] = $this->app->Secure->POST["produktionsverhalten"];

    $data['sprachebevorzugen'] = ($this->app->Secure->POST["sprachebevorzugen"]);

    return $data;
  }

  function parseCheckBox($checkbox)
  {
    if($checkbox=='0'){
      return '';
    }

    if($checkbox=='1'){
      return 'checked';
    }

    if($checkbox==='on'){
      return 1;
    }

    if($checkbox==''){
      return 0;
    }

    return null;
  }

  function FirmendatenBriefpapiervorlage()
  {
    $this->app->erp->FirmendatenVorlage();
    $this->app->Location->execute('index.php?module=firmendaten&action=edit#tabs-3');
  }
  

  function FirmendatenLayout()
  {
    $cmd = $this->app->Secure->GetGET('cmd');

    switch($cmd) {
      case 'hell':
        $this->app->erp->FirmendatenSet("firmenfarbehell","#42B8C4");
        $this->app->erp->FirmendatenSet("firmenfarbedunkel","#2F9099");
        $this->app->erp->FirmendatenSet("iconset_dunkel",'');
      break;
      case 'dunkel':
        $this->app->erp->FirmendatenSet("firmenfarbehell","#3B3B3B");
        $this->app->erp->FirmendatenSet("firmenfarbedunkel","#fff");
        $this->app->erp->FirmendatenSet("iconset_dunkel",'1');
      break;
      case 'dunkelblau':
        $this->app->erp->FirmendatenSet("firmenfarbehell","#014085");
        $this->app->erp->FirmendatenSet("firmenfarbedunkel","#3A6CAB");
        $this->app->erp->FirmendatenSet("iconset_dunkel",'');
      break;
      case 'grau':
        $this->app->erp->FirmendatenSet("firmenfarbedunkel","#6D6D6F");
        $this->app->erp->FirmendatenSet("firmenfarbehell","#018FA3");
        $this->app->erp->FirmendatenSet("iconset_dunkel",'');
      break;
    }
    $this->app->Location->execute('index.php?module=firmendaten&action=edit#tabs-9');
  }

  /**
   * @param string $fontName
   * @param array  $files
   *
   * @throws RuntimeException
   *
   * @return void
   */
  protected function HandleFontUpload($fontName, $files)
  {
    $uploadDir = dirname(__DIR__) . '/lib/pdf/font/unifont/';

    $this->CheckFontName($fontName);
    $this->CheckFontUploadFiles($files);
    $this->CheckFontUploadDirectory($uploadDir);
    $this->MoveUploadedFontFiles($fontName, $uploadDir, $files['tmp_name']);
  }

  /**
   * @param string $fontName
   *
   * @throws RuntimeException
   *
   * @return void
   */
  protected function CheckFontName($fontName)
  {
    $reservedFontNames = ['Courier','Helvetica','Times'];

    if(empty($fontName)){
      throw new RuntimeException('Sie müssen eine Bezeichnung für die Schriftart angeben.');
    }
    if(preg_match('#[^a-zA-Z0-9_-]#', $fontName) === 1){
      throw new RuntimeException(
        'Die Schriftbezeichnung enthält ungültige Zeichen. Es sind nur Buchstaben, Zahlen, Unterstriche und Minus erlaubt.'
      );
    }
    if (in_array($fontName, $reservedFontNames, true)) {
      throw new RuntimeException(sprintf(
        'Die Schriftbezeichnung "%s" ist reserviert. Folgende Bezeichnungen können nicht gewählt werden: %s',
        $fontName, implode(', ', $reservedFontNames)
      ));
    }
  }

  /**
   * @param array $file
   *
   * @throws RuntimeException
   *
   * @return void
   */
  protected function CheckFontUploadFiles($files)
  {
    $types = ['normal','kursiv','fett','fettkursiv'];

    foreach ($types as $type) {

      // Dateigröße prüfen
      if((int)$files['size'][$type] <= 0){
        throw new RuntimeException(sprintf(
          'Sie müssen alle Schriftschnitte angeben. Für den Typ "%s" wurde keine Datei angegeben.', $type
        ));
      }

      // Upload-Fehler prüfen
      if($files['error'][$type] > 0){
        throw new RuntimeException(sprintf(
          'Eine Schriftdatei konnte nicht verarbeitet werden: [%s] %s',
          strtoupper($type), $this->TranslateUploadErrorCodeToMessage($files['error'][$type])
        ));
      }

      // Mimetype prüfen
      // Gültig für TTF: 'application/x-font-ttf', 'application/x-font-truetype' und 'application/octet-stream'
      $mimeType = $files['type'][$type];
      $mimeCheck = preg_match('#^(application|font).*(ttf|truetype|octet\-stream)#i', $mimeType, $matches);
      if($mimeCheck === 0){
        throw new RuntimeException(sprintf(
          'Es werden nur TTF-Schriftarten unterstüzt. Die Datei für den Schriftschnitt "%s" ist von Typ "%s".',
          $type, $mimeType
        ));
      }
    }
  }

  /**
   * @param int $code
   *
   * @return string
   */
  protected function TranslateUploadErrorCodeToMessage($code)
  {
    switch ((int)$code) {
      case UPLOAD_ERR_INI_SIZE:
        $message = "Der Upload überschreitet die 'upload_max_filesize' Einstellung der in php.ini.";
        break;
      case UPLOAD_ERR_FORM_SIZE:
        $message = "Der Upload überschreitet die MAX_FILE_SIZE Einstellung des HTML-Formulars.";
        break;
      case UPLOAD_ERR_PARTIAL:
        $message = "Die Datei wurde nicht vollständig übertragen.";
        break;
      case UPLOAD_ERR_NO_FILE:
        $message = "Es wurde keine Datei ausgewählt.";
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        $message = "Temporärer Ordner fehlt.";
        break;
      case UPLOAD_ERR_CANT_WRITE:
        $message = "Datei konnte nicht abgespeichert werden.";
        break;
      case UPLOAD_ERR_EXTENSION:
        $message = "Der Upload wurde durch eine PHP-Erweiterung gestoppt.";
        break;
      default:
        $message = "Unbekannter Upload-Fehler.";
        break;
    }

    return $message;
  }

  /**
   * @param string $dir
   *
   * @throws RuntimeException Wenn Verzeichnis nicht existent oder nicht beschreibbar
   *
   * @return void
   */
  protected function CheckFontUploadDirectory($dir)
  {
    $dir = realpath($dir);
    if(!is_dir($dir)){
      throw new RuntimeException(sprintf('Das Upload-Verzeichnis für Schriftarten "%s" existiert nicht.', $dir));
    }
    $tempName = $dir . '/' . uniqid('FONTUPLOADTEST', false);
    $handle = @fopen($tempName, 'wb');
    @fwrite($handle, 'FONTUPLOADTEST');
    if(!is_file($tempName)){
      throw new RuntimeException(sprintf(
        'Upload-Verzeichnis für Schriftarten "%s" ist nicht beschreibbar. Bitte passen Sie die Schreibrechte an.', $dir
      ));
    }
    @fclose($handle);
    @unlink($tempName);
  }


  /**
   * @param string $fontName
   * @param string $uploadDir
   * @param array  $fileTemps
   *
   * @throws RuntimeException
   *
   * @return void
   */
  protected function MoveUploadedFontFiles($fontName, $uploadDir, $fileTemps)
  {
    $uploadDir = realpath($uploadDir);
    $basePath = $uploadDir . '/' . $fontName ;
    $targetPath = '';

    foreach ($fileTemps as $type => $tmpPath) {
      switch ($type) {
        case 'normal': $targetPath = $basePath . '.ttf'; break;
        case 'kursiv': $targetPath = $basePath . 'I.ttf'; break;
        case 'fett': $targetPath = $basePath . 'B.ttf'; break;
        case 'fettkursiv': $targetPath = $basePath . 'BI.ttf'; break;
      }

      if (!is_file($tmpPath)) {
        throw new RuntimeException(sprintf(
          'Schriftart-Upload fehlgeschlagen. Temp-Datei "%s" für Typ "%s" wurde nicht gefunden.', $tmpPath, $type
        ));
      }

      @move_uploaded_file($tmpPath, $targetPath);
      if (!is_file($targetPath)) {
        throw new RuntimeException(sprintf(
          'Schriftart-Upload fehlgeschlagen. Hochgeladene Datei für Typ "%s" konnte nicht ans Ziel "%s" verschoben werden.',
          $targetPath, $type
        ));
      }
    }
  }

  /**
   * Sprache in firmendaten_werte und user->sprachebevorzugen speichern
   * @param $language
   */
  private function savePreferredLanguage($language){

    $lang = $this->app->erp->Firmendaten('preferredLanguage');

    if($lang != $language){
      $this->app->erp->FirmendatenSet('preferredLanguage',$language);
      $this->app->DB->Update("UPDATE `user` SET sprachebevorzugen = '".$language."'");
    }
  }

  /**
   * Bevorzugte Sprache aus firmendaten_werte
   * @return string Sprache
   */
  private function getPreferredLanguage(){

    $lang = $this->app->erp->Firmendaten('preferredLanguage');
    if(!empty($lang)) {
      return $lang;
    }

    $this->app->erp->AddNeuenFirmendatenWert('preferredLanguage','varchar','64','','deutsch','deutsch',0,0);
    return 'deutsch';
  }

  /**
   * Liefert einen String aus HTML-Optionen zurück
   * @param string $select Wert aus der Datenbank
   * @return string
   */
  private function languageSelectOptions($select='deutsch'){

    $out = '';
    $sprachen = $this->getLanguages();

    foreach($sprachen as $sprache) {
      $selected = (($select==$sprache) ? 'selected' : '');
      $out .= "<option value=\"$sprache\" $selected>$sprache</option>";
    }
    return $out;
  }

  /**
   * Liefert einen Array aus Strings zurück. Immer mindestens 'deutsch' enthalten
   * @return array
   */
  private function getLanguages(){

    $sprachen[] = 'deutsch';
    $folder = dirname(dirname(__DIR__ )).'/languages';
    if(is_dir($folder)) {
      $handle = opendir($folder);
      if($handle) {
        while($file = readdir($handle)) {
          if($file[0] !== '.') {
            if(is_dir($folder.'/'.$file) && (file_exists($folder.'/'.$file.'/variablen.php')|| file_exists($folder.'/'.$file.'/variablen_custom.php')))
            {
              if($file === 'german') {
                $file = 'deutsch';
              }
              if(!in_array($file, $sprachen)) {
                $sprachen[] = $file;
              }
            }
          }
        }
        closedir($handle);
      }
    }
    return $sprachen;
  }

  /**
   * @return array
   */
  protected function getDocuments()
  {
    $documents = [
      'offer'           => 'Angebot',
      'order'           => 'Auftrag',
      'invoice'         => 'Rechnung',
      'credit_note'     => 'Gutschrift',
      'delivery_note'   => 'Lieferschein',
      'suppliers_order' => 'Bestellung'
    ];
    if($this->app->erp->ModulVorhanden('retoure')) {
      $documents['return_order'] = 'Retoure';
    }

    return $documents;
  }

  public function Install()
  {
    $this->app->erp->CheckTable('document_customization_infoblock');

    $this->app->erp->CheckColumn('keyword', 'VARCHAR(32)','document_customization_infoblock',"NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn('doctype','VARCHAR(32)', 'document_customization_infoblock',"NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn('fontstyle','VARCHAR(2)', 'document_customization_infoblock',"NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn('alignment','VARCHAR(2)', 'document_customization_infoblock',"NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn('content','TEXT', 'document_customization_infoblock');
    $this->app->erp->CheckColumn('project_id','INT(11)', 'document_customization_infoblock','NOT NULL DEFAULT 0');
    $this->app->erp->CheckColumn('active','TINYINT(1)', 'document_customization_infoblock','NOT NULL DEFAULT 0');

    $this->app->erp->CheckTable('document_customization_infoblock_translation');
    $this->app->erp->CheckColumn('document_customization_infoblock_id','INT(11)','document_customization_infoblock_translation','NOT NULL DEFAULT 0');
    $this->app->erp->CheckColumn('language_code','VARCHAR(2)','document_customization_infoblock_translation',"NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn('content','TEXT', 'document_customization_infoblock_translation');
    $this->app->erp->CheckColumn('active','TINYINT(1)', 'document_customization_infoblock_translation','NOT NULL DEFAULT 0');
    $this->app->erp->CheckColumn('fontstyle','VARCHAR(2)', 'document_customization_infoblock_translation',"NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn('alignment','VARCHAR(2)', 'document_customization_infoblock_translation',"NOT NULL DEFAULT ''");
    $this->app->erp->CheckIndex('document_customization_infoblock_translation','document_customization_infoblock_id');
  }

  /**
   * @param string $payment
   * @param bool   $withInactive
   *
   * @return bool
   */
  public function isPaymentActive($payment, $withInactive = false)
  {
    if($withInactive) {
      return $this->app->DB->Select(
          sprintf(
            "SELECT `id` FROM `zahlungsweisen` WHERE `type` = '%s' LIMIT 1",
            $this->app->DB->real_escape_string($payment)
          )
        ) > 0;
    }

    return $this->app->DB->Select(
      sprintf(
        "SELECT `id` FROM `zahlungsweisen` WHERE `type` = '%s' AND `aktiv` = 1 AND `projekt` = 0 LIMIT 1",
        $this->app->DB->real_escape_string($payment)
      )
    ) > 0;
  }

  /**
   * @return array
   */
  public function getNotInstalledPayments()
  {
    $payments = array_keys($this->payments);
    $installedPayments = $this->app->DB->SelectFirstCols(
      "SELECT DISTINCT `type` FROM `zahlungsweisen` WHERE `type` <> ''"
    );

    return array_diff($payments, $installedPayments);
  }

  /**
   * @param string $payment
   * @param bool   $onlyNew
   *
   * @return int
   */
  public function checkPaymentModule($payment, $onlyNew = false) {
    if(empty($this->payments[$payment])) {
      return 0;
    }
    $description = $this->payments[$payment];
    if($this->isPaymentActive($payment, $onlyNew)) {
      return 0;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `zahlungsweisen` (`type`, `aktiv`, `projekt`, `bezeichnung`, `freitext`) 
                VALUES ('%s', 1, 0, '%s','%s')",
        $this->app->DB->real_escape_string($payment),
        $this->app->DB->real_escape_string($description),
        $this->app->DB->real_escape_string($this->app->erp->Firmendaten('zahlung_'.$payment.'_de'))
      )
    );
    $id = (int)$this->app->DB->GetInsertID();
    if($payment === 'vorkasse') {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `zahlungsweisen` SET `verhalten` = 'vorkasse' WHERE `id` = %d",
          $id
        )
      );
    }
    elseif($payment === 'lastschrift') {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `zahlungsweisen` SET `verhalten` = 'lastschrift' WHERE `id` = %d",
          $id
        )
      );
    }
    $translations = $this->app->DB->SelectArr(
      sprintf(
        "SELECT * FROM `uebersetzung` WHERE `label` = 'zahlung_%s_de'",
        $this->app->DB->real_escape_string($payment)
      )
    );
    if(empty($translations)) {
      return $id;
    }

    foreach($translations as $translation) {
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `uebersetzung` (`label`, `beschriftung`, `sprache`, `original`) 
            VALUES ('%s','%s','%s','%s') ",
          'zahlungsweise_freitext_'.$id,
          $this->app->DB->real_escape_string($translation['beschriftung']),
          $this->app->DB->real_escape_string($translation['sprache']),
          $this->app->DB->real_escape_string($translation['original'])
        )
      );
    }

    return $id;
  }

  /**
   * @param bool $onlyNew
   */
  public function checkPaymentModules($onlyNew = false)
  {
    foreach($this->payments as $payment => $description) {
      $this->checkPaymentModule($payment, $onlyNew);
    }
  }

  public function documentSettings()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $this->FirmendatenMenu();
    $this->Install();
    /** @var \Xentral\Modules\Company\Service\DocumentCustomizationService $service */
    $service = $this->app->Container->get('DocumentCustomizationService');
    $documents = $this->getDocuments();
    if($cmd === 'loadtranslation') {
      $language = $this->app->Secure->GetPOST('language');
      $parent = $this->app->Secure->GetPOST('parent_id');
      $ret = $service->getTranslationByCustomizationInfoBlockId($language, $parent);
      if(empty($ret)) {
        $ret = ['id' => 0, 'active' => 1, 'content' => '', 'fontstyle' => ''];
      }
      header('Content-Type: application/json');
      echo json_encode($ret);
      $this->app->ExitXentral();
    }

    if($cmd === 'savetranslation') {
      $alignment = $this->app->Secure->GetPOST('alignment');
      $language = $this->app->Secure->GetPOST('language');
      $content = $this->app->Secure->GetPOST('content','','',true);
      $active = (int)$this->app->Secure->GetPOST('active');
      $parent = (int)$this->app->Secure->GetPOST('parent_id');
      $fontstyle = $this->app->Secure->GetPOST('fontstyle');
      $ret = ['status' => 1];
      try{
        $service->saveTranslation($parent, $language, $content, $active, $fontstyle, $alignment);
      }
      catch(Exception $e) {
        $ret['status'] = 0;
      }
      header('Content-Type: application/json');
      echo json_encode($ret);
      $this->app->ExitXentral();
    }

    if($cmd === 'savedocument')
    {
      $doctype = $this->app->Secure->GetPOST('doctype');
      $content = $this->app->Secure->GetPOST('content','','',true);
      $active = (int)$this->app->Secure->GetPOST('active');
      $fontStyle = $this->app->Secure->GetPOST('fontstyle');
      $alignment = $this->app->Secure->GetPOST('alignment');
      $project = $this->app->Secure->GetPOST('project');
      $projecId = $this->app->erp->ReplaceProjekt(1, $project, 1);
      $id = $this->app->Secure->GetPOST('id');
      $ret = ['status' => 0];

      $error = '';
      if($doctype === ''){
        $error .= 'Bitte Dokument ausfüllen';
      }
      if($error !== ''){
        $ret['statusText'] = $error;
        echo json_encode($ret);
        $this->app->ExitXentral();
      }

      try {
          if (empty($id)) {
              $service->createBlock('corr', $doctype, $content, $fontStyle, $projecId, $active, $alignment);
          }
          else {
              $service->updateInfoBlock($id, $doctype, $content, $fontStyle, $projecId, $active, $alignment);
          }
          $ret['status'] = 1;
      }
      catch(Exception $e) {

      }
      header('Content-Type: application/json');
      echo json_encode($ret);
      $this->app->ExitXentral();
    }

    if($cmd === 'loaddocument')
    {
      $id = $this->app->Secure->GetPOST('id');
      $ret = $service->getBlock($id);
      $ret['project'] = $this->app->erp->ReplaceProjekt(0, $ret['project_id'], 0);
      if(empty($ret)) {
        $ret = ['id' => 0, 'content' => '', 'doctype' => '', 'project' => '', 'active' => 0, 'alignment' => ''];
      }
      header('Content-Type: application/json');
      echo json_encode($ret);
      $this->app->ExitXentral();
    }

    if($cmd === 'changestatus') {
      $ids = !empty($this->app->Secure->POST['ids'])? $this->app->Secure->POST['ids']:null;
      $action = $this->app->Secure->GetPOST('action');
      $ret = ['status' => 0];
      $activate = $action === 'activate';
      $deactivate = $action === 'deactivate';
      if(!empty($ids) && ($activate || $deactivate)) {
        $ret['status'] = 1;
        foreach($ids as $id) {
          $this->app->DB->Update(
            sprintf(
              'UPDATE document_customization_infoblock 
              SET active = %d WHERE id = %d',
              $activate?1:0, (int)$id
            )
          );
        }
      }
      header('Content-Type: application/json');
      echo json_encode($ret);
      $this->app->ExitXentral();
    }

    if($cmd === 'copyinfoblock') {
      $id = $this->app->Secure->GetPOST('id');
      $ret = ['status' => 0];
      try {
        $service->copy($id);
        $ret['status'] = 1;
      }
      catch (Exception $e) {

      }
      header('Content-Type: application/json');
      echo json_encode($ret);
      $this->app->ExitXentral();
    }

    if($cmd === 'deleteinfoblock') {
      $id = $this->app->Secure->GetPOST('id');
      $ret = ['status' => 0];
      try {
        $service->delete($id);
        $ret['status'] = 1;
      }
      catch(Exception $e) {

      }
      header('Content-Type: application/json');
      echo json_encode($ret);
      $this->app->ExitXentral();
    }

    if($this->app->Secure->GetPOST('save')) {
      foreach($documents as $document => $coumentName) {
        $content = $this->app->Secure->GetPOST($document.'_content','','',true);
        $service->saveBlock(
          'corr',
          $document,
          $content,
          $this->app->Secure->GetPOST($document.'_fontstyle'),
          $this->app->Secure->GetPOST($document.'_active')
        );
      }
    }

    $data = [];
    $fontoptions = [
      ''   => 'normal',
      'f'  => 'fett',
      'i'  => 'kursiv',
      'fi' => 'fett und kursiv'
    ];

    $alignments = [
      ''   => 'Standard',
      'll' => 'L L',
      'lr' => 'L R',
      'cc' => 'C C'
    ];

    $previewArr = [];
    $previewArr[] = [
      'value' => 'ANFRAGENUMMER',
      'label' => 'Anfragenummer',
      'doc_types' => ['offer']
    ];
    $previewArr[] = [
      'value' => 'ANGEBOTSNUMMER',
      'label' => 'Angebotsnummer',
      'doc_types' => ['order', 'offer']
    ];
    $previewArr[] = [
      'value' => 'AUFTRAGSNUMMER',
      'label' => 'Auftragsnummer',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'BEARBEITER',
      'label' => 'Bearbeiter',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'BEARBEITEREMAIL',
      'label' => 'Bearbeiter E-Mail',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'BEARBEITERTELEFON',
      'label' => 'Bearbeiter Telefon',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'BESTELLNUMMER',
      'label' => 'Bestellnummer',
      'doc_types' => ['suppliers_order']
    ];
    $previewArr[] = [
      'value' => 'DATUM',
      'label' => 'Datum',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'suppliers_order', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'EINKAEUFER',
      'label' => 'Einkäufer',
      'doc_types' => ['suppliers_order']
    ];
    $previewArr[] = [
      'value' => 'EMAIL',
      'label' => 'Email aus Beleg',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'GUTSCHRIFTSNUMMER',
      'label' => 'Gutschriftsnummer',
      'doc_types' => ['credit_note']
    ];
    $previewArr[] = [
      'value' => 'IHREBESTELLNUMMER',
      'label' => 'Ihre Bestellnummer',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'suppliers_order', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'KUNDENNUMMER',
      'label' => 'Kundennummer',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'LIEFERANTENNUMMER',
      'label' => 'Lieferantennummer',
      'doc_types' => ['suppliers_order']
    ];
    $previewArr[] = [
      'value' => 'LIEFERSCHEINDATUM',
      'label' => 'Lieferscheindatum',
      'doc_types' => ['invoice', 'credit_note', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'LIEFERSCHEINNUMMER',
      'label' => 'Lieferscheinnummer',
      'doc_types' => ['invoice', 'delivery_note', 'credit_note', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'RECHNUNGSDATUM',
      'label' => 'Rechnungsdatum',
      'doc_types' => ['credit_note']
    ];
    $previewArr[] = [
      'value' => 'RECHNUNGSNUMMER',
      'label' => 'Rechnungsnummer',
      'doc_types' => ['invoice', 'credit_note']
    ];
    $previewArr[] = [
      'value' => 'RETOURENNUMMER',
      'label' => 'Retourennummer',
      'doc_types' => ['return_order']
    ];
    $previewArr[] = [
      'value' => 'PROJEKT',
      'label' => 'Projekt',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'suppliers_order', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'TELEFON',
      'label' => 'Telefon aus Beleg',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'return_order']
    ];
    $previewArr[] = [
      'value' => 'TRACKINGNUMMER',
      'label' => 'Trackingnummer',
      'doc_types' => ['delivery_note']
    ];
    $previewArr[] = [
      'value' => 'KUNDENNUMMER',
      'label' => 'Unsere Kundennummer',
      'doc_types' => ['suppliers_order']
    ];
    $previewArr[] = [
      'value' => 'VERTRIEB',
      'label' => 'Vertrieb',
      'doc_types' => ['invoice', 'order', 'delivery_note', 'credit_note', 'offer', 'return_order']
    ];


    $languages = [];
    $languageArr = $this->app->DB->SelectArr(
      "SELECT * FROM sprachen WHERE aktiv = 1 AND iso <> '' ORDER BY bezeichnung_de"
    );
    if(!empty($languageArr)) {
      foreach($languageArr as $languageRow) {
        $languages[$languageRow['iso']] = $languageRow['bezeichnung_de'];
      }
    }
    $element = [
      'document'     => $document,
      'active'       => !empty($block['active'])?'1':'',
      'fontstyle'   => !empty($block['fontstyle'])?$block['fontstyle']:'',
      'content'     => !empty($block['content'])?$block['content']:'',
      'name'         => ucfirst($coumentName),
      'preview'      => $previewArr
    ];
    /*foreach($documents as $document => $coumentName) {
      $block = $service->getBlock('corr', $document);
      $element = [
        'document'     => $document,
        'active'       => !empty($block['active'])?'1':'',
        'fontstyle'   => !empty($block['fontstyle'])?$block['fontstyle']:'',
        'content'     => !empty($block['content'])?$block['content']:'',
        'name'         => ucfirst($coumentName),
        'preview'      => $previewArr
      ];
      if(in_array($document, ['angebot','auftrag','rechnung','gutschrift','proformarechnung','lieferschein','retoure'])) {
        $element['preview']['KUNDENNUMMER'] = 'Kundennummer';
      }
      if(in_array($document, ['anfrage','bestellung'])) {
        $element['preview']['LIEFERANTENNUMMER'] = 'Lieferantennummer';
      }
      switch($document) {
        case 'offer':
          $element['preview']['ANGEBOTSNUMMER'] = 'Angebotsnummer';
          break;
        case 'order':
          $element['preview']['AUFTRAGSNUMMER'] = 'Auftragsnummer';
          break;
        case 'invoice':
          $element['preview']['RECHNUNGSNUMMER'] = 'Rechnungsnummer';
          break;
        case 'credit_note':
          $element['preview']['GUTSCHRIFTSNUMMER'] = 'Gutschriftsnummer';
          break;
        case 'suppliers_order':
          $element['preview']['BESTELLNUMMER'] = 'Bestellnummer';
          break;
      }
      $data[] = $element;
    }*/

    $this->app->YUI->TableSearch('TAB1', 'company_document_setting','show','','',basename(__FILE__), __CLASS__);
    $datatable = $this->app->Tpl->Parse('', 'emptytab.tpl', true);
    $this->app->Tpl->Set('TAB1', '');
    $this->tmpl = $this->app->Container->get('Template');
    $this->tmpl->setDefaultNamespace('Modules/Company');
    $this->tmpl->assign('fontoptions', $fontoptions);
    $this->tmpl->assign('alignments', $alignments);
    //$this->tmpl->assign('documents', $data);
    $this->tmpl->assign('languages', $languages);
    $this->tmpl->assign('datatable', $datatable);
    $this->tmpl->assign('doctypearr', $documents);
    $this->tmpl->assign('documentarr', $element);
    $table = $this->tmpl->fetch('document_settings.tpl');

    $this->app->YUI->AutoComplete('document_project', 'projektname', 1);


    $this->app->Tpl->Add('TAB1', $table);
    //$this->app->Tpl->Set('TAB1', $ret);
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  /**
   * @return int
   */
  public function getMaxTrackingMailToSend()
  {
    $limit = (int)$this->app->erp->Firmendaten('versandmails_max');
    $isSet = $this->app->erp->GetKonfiguration('firmendaten_versandmails_max_gesetzt');
    if(!$isSet) {
      if(!$limit) {
        $limit = 100;
        $this->app->erp->FirmendatenSet('versandmails_max', $limit);
      }
      $this->app->erp->SetKonfigurationValue('firmendaten_versandmails_max_gesetzt',1);
    }

    return $limit;
  }

  /**
   * @return int
   */
  public function getMaxAutoOrderSend()
  {
    $limit = (int)$this->app->erp->Firmendaten('autoversand_maxauftraege');
    $isSet = $this->app->erp->GetKonfiguration('firmendaten_autoversand_maxauftraege_gesetzt');
    if(!$isSet) {
      if(!$limit) {
        $limit = 100;
        $this->app->erp->FirmendatenSet('autoversand_maxauftraege', $limit);
      }
      $this->app->erp->SetKonfigurationValue('firmendaten_autoversand_maxauftraege_gesetzt',1);
    }

    return $limit;
  }
}
