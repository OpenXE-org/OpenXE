<?php
if(!class_exists('ApplicationCore') && is_file(dirname(__DIR__).'/xentral_autoloader.php')) {
  include_once dirname(__DIR__).'/xentral_autoloader.php';
}
set_time_limit(36000);


error_reporting(E_ERROR | E_WARNING | E_PARSE);
if(!class_exists('Conf')){
  include_once(dirname(__DIR__) . "/conf/main.conf.php");
}
if(!class_exists('DB')){
  include_once(dirname(__DIR__) . "/phpwf/plugins/class.mysql.php");
}
if(!class_exists('Secure')){
  include_once(dirname(__DIR__) . "/phpwf/plugins/class.secure.php");
}

if(!class_exists('FormHandler')){
  include_once(dirname(__DIR__) . "/phpwf/plugins/class.formhandler.php");
}

if(!class_exists('User')){
  include_once(dirname(__DIR__) . "/phpwf/plugins/class.user.php");
}

if(!class_exists('FPDFWAWISION')){
  if(file_exists(dirname(__DIR__).'/conf/user_defined.php')){
    include_once dirname(__DIR__) . '/conf/user_defined.php';
  }
  if(defined('USEFPDF3') && USEFPDF3 && file_exists(dirname(__DIR__) . '/www/lib/pdf/fpdf_3.php')){
    require_once dirname(__DIR__) . '/www/lib/pdf/fpdf_3.php';
  }else if(defined('USEFPDF2') && USEFPDF2 && file_exists(dirname(__DIR__) . '/www/lib/pdf/fpdf_2.php')){
    require_once dirname(__DIR__) . '/www/lib/pdf/fpdf_2.php';
  }else{
    require_once dirname(__DIR__) . '/www/lib/pdf/fpdf.php';
  }
}
if(!class_exists('IMAP')){
  include_once(dirname(__DIR__) . "/www/lib/imap.inc.php");
}
if(!class_exists('PDF_EPS')){
  include_once(dirname(__DIR__) . "/www/lib/pdf/fpdf_final.php");
}
if(!class_exists('SuperFPDF')){
  include_once(dirname(__DIR__) . "/www/lib/dokumente/class.superfpdf.php");
}
if(!class_exists('erpAPI')){
  include_once(dirname(__DIR__) . "/www/lib/class.erpapi.php");
}
if(file_exists(dirname(__DIR__)."/www/lib/class.erpapi_custom.php") &&
  !class_exists('erpAPICustom')){
  include_once(dirname(__DIR__) . "/www/lib/class.erpapi_custom.php");
}
if(!class_exists('Remote')){
  include_once(dirname(__DIR__) . "/www/lib/class.remote.php");
}

if(!class_exists('RemoteCustom') &&
  file_exists(dirname(__DIR__)."/www/lib/class.remote_custom.php"))
  include_once(dirname(__DIR__)."/www/lib/class.remote_custom.php");
if(!class_exists('HttpClient')){
  include_once(dirname(__DIR__) . "/www/lib/class.httpclient.php");
}

if(!class_exists('AES')){
  $aes = '';
  $phpversion = (String)phpversion();
  if($phpversion{0} == '7' && (int)$phpversion{2} > 0) $aes = '2';
  if($aes == 2 && is_file(dirname(__DIR__) . "/www/lib/class.aes" . $aes . ".php")){
    include_once(dirname(__DIR__) . "/www/lib/class.aes" . $aes . ".php");
  }else
    include_once(dirname(__DIR__) . "/www/lib/class.aes.php");
}

if(!class_exists('PHPMailer')){
  include_once(dirname(__DIR__) . "/www/plugins/phpmailer/class.phpmailer.php");
}
if(!class_exists('SMTP')){
  include_once(dirname(__DIR__) . "/www/plugins/phpmailer/class.smtp.php");
}
if(!class_exists('image'))
{
  include_once (dirname(__DIR__)."/www/lib/class.image.php");
}

$classes = array('briefpapier','lieferschein','auftrag','anfrage','gutschrift','bestellung','rechnung','mahnwesen','angebot');
foreach($classes as $class)
{
  if(file_exists(dirname(__DIR__)."/www/lib/dokumente/class.".$class."_custom.php"))
  {
    include_once(dirname(__DIR__)."/www/lib/dokumente/class.".$class."_custom.php");
  }elseif(file_exists(dirname(__DIR__)."/www/lib/dokumente/class.".$class.".php"))
  {
    include_once(dirname(__DIR__)."/www/lib/dokumente/class.".$class.".php");
  }
}
if(!defined('FPDF_FONTPATH'))
{
  define('FPDF_FONTPATH',dirname(__DIR__).'/www/lib/pdf/font/');
}
/*
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.briefpapier.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.lieferschein.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.auftrag.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.angebot.php");
if(file_exists(dirname(__FILE__)."/../www/lib/dokumente/class.anfrage.php"))include_once(dirname(__FILE__)."/../www/lib/dokumente/class.anfrage.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.gutschrift.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.bestellung.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.rechnung.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.mahnwesen.php");*/
if(!class_exists('WawiString')){
  include_once(dirname(__DIR__) . "/phpwf/plugins/class.string.php");
}
if(!class_exists('app_t2'))
{
  class app_t2 extends ApplicationCore {
    var $DB;
    var $erp;
    var $User;
    var $mail;
    var $remote;
    var $Secure;
    public function GetLandLang($isocode)
    {
      $flipped = array_flip($this->GetLaender());
      if(isset($flipped[$isocode])){
        $land = $flipped[$isocode];
      }
      else
      {
        $land = 'unkown';
      }
      return $land;
    }

    public function GetLaender()
    {
      $laender = array(
        'Afghanistan'  => 'AF',
        '&Auml;gypten'  => 'EG',
        'Albanien'  => 'AL',
        'Algerien'  => 'DZ',
        'Andorra'  => 'AD',
        'Angola'  => 'AO',
        'Anguilla'  => 'AI',
        'Antarktis'  => 'AQ',
        'Antigua und Barbuda'  => 'AG',
        '&Auml;quatorial Guinea'  => 'GQ',
        'Argentinien'  => 'AR',
        'Armenien'  => 'AM',
        'Aruba'  => 'AW',
        'Aserbaidschan'  => 'AZ',
        '&Auml;thiopien'  => 'ET',
        'Australien'  => 'AU',
        'Bahamas'  => 'BS',
        'Bahrain'  => 'BH',
        'Bangladesh'  => 'BD',
        'Barbados'  => 'BB',
        'Belgien'  => 'BE',
        'Belize'  => 'BZ',
        'Benin'  => 'BJ',
        'Bermudas'  => 'BM',
        'Bhutan'  => 'BT',
        'Birma'  => 'MM',
        'Bolivien'  => 'BO',
        'Bosnien-Herzegowina'  => 'BA',
        'Botswana'  => 'BW',
        'Bouvet Inseln'  => 'BV',
        'Brasilien'  => 'BR',
        'Britisch-Indischer Ozean'  => 'IO',
        'Brunei'  => 'BN',
        'Bulgarien'  => 'BG',
        'Burkina Faso'  => 'BF',
        'Burundi'  => 'BI',
        'Chile'  => 'CL',
        'China'  => 'CN',
        'Christmas Island'  => 'CX',
        'Cook Inseln'  => 'CK',
        'Costa Rica'  => 'CR',
        'D&auml;nemark'  => 'DK',
        'Deutschland'  => 'DE',
        'Djibuti'  => 'DJ',
        'Dominika'  => 'DM',
        'Dominikanische Republik'  => 'DO',
        'Ecuador'  => 'EC',
        'El Salvador'  => 'SV',
        'Elfenbeink&uuml;ste'  => 'CI',
        'Eritrea'  => 'ER',
        'Estland'  => 'EE',
        'Falkland Inseln'  => 'FK',
        'F&auml;r&ouml;er Inseln'  => 'FO',
        'Fidschi'  => 'FJ',
        'Finnland'  => 'FI',
        'Frankreich'  => 'FR',
        'Franz&ouml;sisch Guyana'  => 'GF',
        'Franz&ouml;sisch Polynesien'  => 'PF',
        'Franz&ouml;sisches S&uuml;d-Territorium'  => 'TF',
        'Gabun'  => 'GA',
        'Gambia'  => 'GM',
        'Georgien'  => 'GE',
        'Ghana'  => 'GH',
        'Gibraltar'  => 'GI',
        'Grenada'  => 'GD',
        'Griechenland'  => 'GR',
        'Gr&ouml;nland'  => 'GL',
        'Großbritannien'  => 'UK',
        'Großbritannien (UK)'  => 'GB',
        'Guadeloupe'  => 'GP',
        'Guam'  => 'GU',
        'Guatemala'  => 'GT',
        'Guinea'  => 'GN',
        'Guinea Bissau'  => 'GW',
        'Guyana'  => 'GY',
        'Haiti'  => 'HT',
        'Heard und McDonald Islands'  => 'HM',
        'Honduras'  => 'HN',
        'Hong Kong'  => 'HK',
        'Indien'  => 'IN',
        'Indonesien'  => 'ID',
        'Irak'  => 'IQ',
        'Iran'  => 'IR',
        'Irland'  => 'IE',
        'Island'  => 'IS',
        'Israel'  => 'IL',
        'Italien'  => 'IT',
        'Jamaika'  => 'JM',
        'Japan'  => 'JP',
        'Jemen'  => 'YE',
        'Jordanien'  => 'JO',
        'Jugoslawien'  => 'YU',
        'Kaiman Inseln'  => 'KY',
        'Kambodscha'  => 'KH',
        'Kamerun'  => 'CM',
        'Kanada'  => 'CA',
        'Kap Verde'  => 'CV',
        'Kasachstan'  => 'KZ',
        'Kenia'  => 'KE',
        'Kirgisistan'  => 'KG',
        'Kiribati'  => 'KI',
        'Kokosinseln'  => 'CC',
        'Kolumbien'  => 'CO',
        'Komoren'  => 'KM',
        'Kongo'  => 'CG',
        'Kongo, Demokratische Republik'  => 'CD',
        'Kosovo'  => 'KO',
        'Kroatien'  => 'HR',
        'Kuba'  => 'CU',
        'Kuwait'  => 'KW',
        'Laos'  => 'LA',
        'Lesotho'  => 'LS',
        'Lettland'  => 'LV',
        'Libanon'  => 'LB',
        'Liberia'  => 'LR',
        'Libyen'  => 'LY',
        'Liechtenstein'  => 'LI',
        'Litauen'  => 'LT',
        'Luxemburg'  => 'LU',
        'Macao'  => 'MO',
        'Madagaskar'  => 'MG',
        'Malawi'  => 'MW',
        'Malaysia'  => 'MY',
        'Malediven'  => 'MV',
        'Mali'  => 'ML',
        'Malta'  => 'MT',
        'Marianen'  => 'MP',
        'Marokko'  => 'MA',
        'Marshall Inseln'  => 'MH',
        'Martinique'  => 'MQ',
        'Mauretanien'  => 'MR',
        'Mauritius'  => 'MU',
        'Mayotte'  => 'YT',
        'Mazedonien'  => 'MK',
        'Mexiko'  => 'MX',
        'Mikronesien'  => 'FM',
        'Mocambique'  => 'MZ',
        'Moldavien'  => 'MD',
        'Monaco'  => 'MC',
        'Mongolei'  => 'MN',
        'Montenegro'  => 'ME',
        'Montserrat'  => 'MS',
        'Namibia'  => 'NA',
        'Nauru'  => 'NR',
        'Nepal'  => 'NP',
        'Neukaledonien'  => 'NC',
        'Neuseeland'  => 'NZ',
        'Nicaragua'  => 'NI',
        'Niederlande'  => 'NL',
        'Niederl&auml;ndische Antillen'  => 'AN',
        'Niger'  => 'NE',
        'Nigeria'  => 'NG',
        'Niue'  => 'NU',
        'Nord Korea'  => 'KP',
        'Norfolk Inseln'  => 'NF',
        'Norwegen'  => 'NO',
        'Oman'  => 'OM',
        '&Ouml;sterreich'  => 'AT',
        'Pakistan'  => 'PK',
        'Pal&auml;stina'  => 'PS',
        'Palau'  => 'PW',
        'Panama'  => 'PA',
        'Papua Neuguinea'  => 'PG',
        'Paraguay'  => 'PY',
        'Peru'  => 'PE',
        'Philippinen'  => 'PH',
        'Pitcairn'  => 'PN',
        'Polen'  => 'PL',
        'Portugal'  => 'PT',
        'Puerto Rico'  => 'PR',
        'Qatar'  => 'QA',
        'Reunion'  => 'RE',
        'Ruanda'  => 'RW',
        'Rum&auml;nien'  => 'RO',
        'Ru&szlig;land'  => 'RU',
        'Saint Lucia'  => 'LC',
        'Sambia'  => 'ZM',
        'Samoa'  => 'AS',
        'Samoa'  => 'WS',
        'San Marino'  => 'SM',
        'Sao Tome'  => 'ST',
        'Saudi Arabien'  => 'SA',
        'Schweden'  => 'SE',
        'Schweiz'  => 'CH',
        'Senegal'  => 'SN',
        'Serbien'  => 'RS',
        'Seychellen'  => 'SC',
        'Sierra Leone'  => 'SL',
        'Singapur'  => 'SG',
        'Slowakei -slowakische Republik-'  => 'SK',
        'Slowenien'  => 'SI',
        'Solomon Inseln'  => 'SB',
        'Somalia'  => 'SO',
        'South Georgia, South Sandwich Isl.'  => 'GS',
        'Spanien'  => 'ES',
        'Sri Lanka'  => 'LK',
        'St. Helena'  => 'SH',
        'St. Kitts Nevis Anguilla'  => 'KN',
        'St. Pierre und Miquelon'  => 'PM',
        'St. Vincent'  => 'VC',
        'S&uuml;d Korea'  => 'KR',
        'S&uuml;dafrika'  => 'ZA',
        'Sudan'  => 'SD',
        'Surinam'  => 'SR',
        'Svalbard und Jan Mayen Islands'  => 'SJ',
        'Swasiland'  => 'SZ',
        'Syrien'  => 'SY',
        'Tadschikistan'  => 'TJ',
        'Taiwan'  => 'TW',
        'Tansania'  => 'TZ',
        'Thailand'  => 'TH',
        'Timor'  => 'TP',
        'Togo'  => 'TG',
        'Tokelau'  => 'TK',
        'Tonga'  => 'TO',
        'Trinidad Tobago'  => 'TT',
        'Tschad'  => 'TD',
        'Tschechische Republik'  => 'CZ',
        'Tunesien'  => 'TN',
        'T&uuml;rkei'  => 'TR',
        'Turkmenistan'  => 'TM',
        'Turks und Kaikos Inseln'  => 'TC',
        'Tuvalu'  => 'TV',
        'Uganda'  => 'UG',
        'Ukraine'  => 'UA',
        'Ungarn'  => 'HU',
        'Uruguay'  => 'UY',
        'Usbekistan'  => 'UZ',
        'Vanuatu'  => 'VU',
        'Vatikan'  => 'VA',
        'Venezuela'  => 'VE',
        'Vereinigte Arabische Emirate'  => 'AE',
        'Vereinigte Staaten von Amerika'  => 'US',
        'Vietnam'  => 'VN',
        'Virgin Island (Brit.)'  => 'VG',
        'Virgin Island (USA)'  => 'VI',
        'Wallis et Futuna'  => 'WF',
        'Wei&szlig;ru&szlig;land'  => 'BY',
        'Westsahara'  => 'EH',
        'Zentralafrikanische Republik'  => 'CF',
        'Zimbabwe'  => 'ZW',
        'Zypern'  => 'CY'
      );
      return $laender;
    }
  }
}
//ENDE


if(empty($app) || !class_exists('ApplicationCore') || !($app instanceof ApplicationCore)) {
  $app = new app_t2();
}
if(empty($app->Conf)){
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)){
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, null, $app->Conf->WFdbport);
}
if(empty($app->erp)){
  if(class_exists('erpAPICustom')){
    $erp = new erpAPICustom($app);
  }
  else{
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}
  if(class_exists('RemoteCustom'))
  {
    $app->remote = new RemoteCustom($app);
  }elseif(class_exists('Remote'))
  {
    $app->remote = new Remote($app);
  }

  $app->String  = new WawiString();

  $app->Secure = new Secure($app);
  $app->User = new User($app);
  if(class_exists('FormHandler')){
    $app->FormHandler = new FormHandler($app);
  }
  if(class_exists('TemplateParser'))
  {
    $app->TemplateParser = new TemplateParser($app);
  }
  if(!defined('FPDF_FONTPATH'))
  {
    define('FPDF_FONTPATH',dirname(__DIR__)."/www/lib/pdf/font/");
  }


$pdf = new pdfarchiv_app($app);
$pdf->run();
class pdfarchiv_app
{
  /** @var ApplicationCore $app */
  var $app;

  /** @var string $folder */
  var $folder;

  /**
   * pdfarchiv_app constructor.
   *
   * @param ApplicationCore $app
   */
  public function __construct($app)
  {
    $this->app = $app;
    $this->folder = $this->app->Conf->WFuserdata."/pdfarchiv/".$this->app->Conf->WFdbname;
  }

  /**
   * @return bool
   */
  public function run()
  {
    if(!empty($this->app->Conf->WFuserdata))
    {
      if(!file_exists($this->app->Conf->WFuserdata.'/pdfarchiv'))
      {
        if(!@mkdir($this->app->Conf->WFuserdata.'/pdfarchiv') && !is_dir($this->app->Conf->WFuserdata.'/pdfarchiv'))
        {
          echo $this->app->Conf->WFuserdata."/pdfarchiv"."konnte nicht erstellt werden\r\n";
        }
      }
      if(!file_exists($this->folder))
      {
        if(!@mkdir($this->folder) && !is_dir($this->folder))
        {
          echo $this->folder."konnte nicht erstellt werden\r\n";
        }
      }
    }
    if(!file_exists($this->folder))
    {
      return false;
    }
    $run = true;
    $this->app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter+1 WHERE parameter = 'pdfarchiv_app' AND aktiv = 1 AND mutex = 1");
    if($this->app->DB->Select("SELECT mutex FROM prozessstarter WHERE parameter = 'pdfarchiv_app' AND aktiv = 1 LIMIT 1"))
    {
      return false;
    }
    while($run) {
      $this->app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");
      $run = $this->run_next();
      if(method_exists($this->app->erp, 'canRunCronjob') && !$this->app->erp->canRunCronjob(['pdfarchiv_app'])) {
        return true;
      }
    }
    $this->app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0 WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");

    return true;
  }

  /**
   * @return bool
   */
  protected function run_next()
  {
    $job = $this->app->DB->SelectRow("SELECT * FROM pdfarchiv_jobs WHERE status = 'angelegt' and aktiv = 1 and abbrechen <> 1 ORDER by geaendert_am LIMIT 1");
    if(empty($job))
    {
      return false;
    }
    if($job)
    {
      $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'gestartet' WHERE id = '".$job['id']."' LIMIT 1");
      $monatvon = (int)$job['monat_von'];
      $monatbis = (int)$job['monat_bis'];
      $jahrvon = (int)$job['jahr_von'];
      $jahrbis = (int)$job['jahr_bis'];
      if($jahrbis < $jahrvon)
      {
        $jahrbis = $jahrvon;
        $monatbis = $monatvon;
      }
      if($jahrbis == $jahrvon && $monatbis < $monatvon)
      {
        $monatbis = $monatvon;
      }
      $monatvon = ($monatvon < 10?'0':'').$monatvon;
      $monatbis = ($monatbis < 10?'0':'').$monatbis;
      $pdfneu = $job['pdfneu'];
      $generiere_nur_neue = $job['generiere_nur_neue'];
      $tabs = explode(',',$job['tabellen']);
      foreach($tabs as $k => $v)
      {
        $tabelle = strtolower(trim($v));
        if(!empty($tabelle))
        {
          $tabellen[] = $tabelle;
        }
      }

      if(empty($tabellen))
      {
        $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'Fehler', kommentar = 'Keine Tabellen angegeben' WHERE id = '".$job['id']."' LIMIT 1");
        return true;
      }

      $this->app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0 WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");
      if($pdfneu)
      {
        echo "pdfneu\r\n";
        $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'Archiviere...' WHERE id = '".$job['id']."' LIMIT 1");
        $centries = 0;
        foreach($tabellen as $table)
        {
          $centries++;
          if($centries % 10 === 0 && function_exists('gc_enabled') && function_exists('gc_collect_cycles') && gc_enabled())
          {
            $cgc = gc_collect_cycles();
            if($cgc > 0)
            {
              $this->app->erp->LogFile($cgc.' cycles collected');
            }
          }
          $check = $this->app->DB->Query("SELECT t.id,t.projekt,t.schreibschutz FROM $table t LEFT JOIN pdfarchiv p ON p.table_id = t.id AND p.table_name = '$table' AND CHAR_LENGTH(p.belegnummer) > 2 AND p.belegnummer <> 'SAB' WHERE t.belegnr <> '' AND t.status <> 'angelegt' AND t.status <> 'angelegta' AND t.status <> 'a'  
          AND year(t.datum) >= '".$jahrvon."' AND year(t.datum) <= '".$jahrbis."' AND month(t.datum) >= '".$monatvon."' AND month(t.datum) <= '".$monatbis."'
          GROUP BY t.id,t.projekt");
          while($row = $this->app->DB->Fetch_Array($check))
          {
            echo "$table ".$row['id']."\r\n";

            if(empty($row['schreibschutz']))
            {
              $this->app->DB->Update("UPDATE $table SET schreibschutz = 1 WHERE id = '".$row['id']."' LIMIT 1");
              $protkollfunktion = ucfirst($table.'Protokoll');
              if(method_exists($this->app->erp,$protkollfunktion))
              {
                $this->app->erp->$protkollfunktion($row['id'], 'PDF-Archiv-App: Schreibschutz gesetzt');
              }
            }

            $this->app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");
            if($this->app->DB->Select("SELECT abbrechen FROM pdfarchiv_jobs WHERE id = '".$job['id']."'"))
            {
              $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'abgebrochen' WHERE id = '".$job['id']."' LIMIT 1");
              return true;
            }
                    
            if($table === 'rechnung')
            {
              $mahnwesen = $this->app->DB->Select("SELECT mahnwesen FROM rechnung WHERE id='".$row['id']."' LIMIT 1");
              if($mahnwesen && class_exists('MahnwesenPDF'))
              {
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('MahnwesenCustomPDF'))
                {
                  $Brief = new MahnwesenCustomPDF($this->app, $row['projekt']);
                }else{
                  $Brief = new MahnwesenPDF($this->app, $row['projekt']);
                }
                $Brief->GetRechnung($row['id'], $mahnwesen);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                unlink($tmpfile);
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('MahnwesenCustomPDF'))
                {
                  $Brief = new MahnwesenCustomPDF($this->app, $row['projekt']);
                }else{
                  $Brief = new MahnwesenPDF($this->app, $row['projekt']);
                }
                $Brief->GetRechnung($row['id'], $mahnwesen);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                unlink($tmpfile);
              }
            }
            
            $name = ucfirst($table).'PDFCustom';
            if(!class_exists($name))
            {
              $name = ucfirst($table).'PDF';
            }
            $nameget = 'Get'.ucfirst($table);
            $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
            $Brief = new $name($this->app, $row['projekt']);
            $Brief->$nameget($row['id']);
            $tmpfile = $Brief->displayTMP();
            $Brief->ArchiviereDocument();
            unlink($tmpfile);
            $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;

            if(class_exists($name)){
              $anzargs = 2;
              if(method_exists($name,'__construct'))
              {
                $r = new ReflectionMethod($name, '__construct');
                $params = $r->getParameters();
                $anzargs = count($params);
              }
              if($anzargs < 2)
              {
                $Brief = new $name($this->app);
              }else{
                $Brief = new $name($this->app, $row['projekt']);
              }
              $Brief->$nameget($row['id']);
              $tmpfile = $Brief->displayTMP();
              $Brief->ArchiviereDocument();
              unlink($tmpfile);
            }
          }
          if($check)
          {
            $this->app->DB->free($check);
          }
        }
      }elseif($generiere_nur_neue)
      {
        $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'Archiviere...' WHERE id = '".$job['id']."' LIMIT 1");
        foreach($tabellen as $table)
        {
          $check = $this->app->DB->Query("SELECT t.* FROM $table t LEFT JOIN pdfarchiv p ON p.table_id = t.id AND p.table_name = '$table' AND if(t.schreibschutz = 1, p.schreibschutz = 1,1) AND CHAR_LENGTH(p.belegnummer) > 2 AND p.belegnummer <> 'SAB' WHERE isnull(p.id) AND t.belegnr <> '' AND t.status <> 'angelegt' AND t.status <> 'angelegta' AND t.status <> 'a' 
          AND year(t.datum) >= '".$jahrvon."' AND year(t.datum) <= '".$jahrbis."' AND month(t.datum) >= '".$monatvon."' AND month(t.datum) <= '".$monatbis."'
          ");
          $centries = 0;
          while($row = $this->app->DB->Fetch_Array($check))
          {
            echo "$table ".$row['id']."\r\n";
            $centries++;
            if($centries % 10 === 0 && function_exists('gc_enabled') && function_exists('gc_collect_cycles') && gc_enabled())
            {
              $cgc = gc_collect_cycles();
              if($cgc > 0)
              {
                $this->app->erp->LogFile($cgc.' cycles collected');
              }
            }
            if(empty($row['schreibschutz']))
            {
              $this->app->DB->Update("UPDATE $table SET schreibschutz = 1 WHERE id = '".$row['id']."' LIMIT 1");
              $protkollfunktion = ucfirst($table.'Protokoll');
              if(method_exists($this->app->erp,$protkollfunktion))
              {
                $this->app->erp->$protkollfunktion($row['id'], 'PDF-Archiv-App: Schreibschutz gesetzt');
              }
            }

            $this->app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");
            if($this->app->DB->Select("SELECT abbrechen FROM pdfarchiv_jobs WHERE id = '".$job['id']."'"))
            {
              $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'abgebrochen' WHERE id = '".$job['id']."' LIMIT 1");
              return true;
            }
            $name = ucfirst($table).'PDFCustom';
            if(!class_exists($name))
            {
              $name = ucfirst($table).'PDF';
            }
            $nameget = 'Get'.ucfirst($table);
            $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
            $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
            $Brief = new $name($this->app, $row['projekt']);

            $Brief->$nameget($row['id']);
            $tmpfile = $Brief->displayTMP();
            $Brief->ArchiviereDocument();
            unlink($tmpfile);
            $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
            $Brief = new $name($this->app, $row['projekt']);

            $Brief->$nameget($row['id']);
            $tmpfile = $Brief->displayTMP();
            $Brief->ArchiviereDocument();
            unlink($tmpfile);
          }
        }
      }
      $this->app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");
      if($this->app->DB->Select("SELECT abbrechen FROM pdfarchiv_jobs WHERE id = '".$job['id']."'"))
      {
        $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'abgebrochen' WHERE id = '".$job['id']."' LIMIT 1");
        return true;
      }
      $allepdf = $job['allepdf'];
      $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'Speichere...' WHERE id = '".$job['id']."' LIMIT 1");
      
      $pfad = $this->folder.'/'.$monatvon.$jahrvon.'-'.$monatbis.$jahrbis.'-'.$job['id'];
      $pfad_rel = $monatvon.$jahrvon.'-'.$monatbis.$jahrbis.'-'.$job['id'];
      $i = 0;
      while(file_exists($pfad))
      {
        $i++;
        $pfad = $pfad = $this->folder.'/'.$monatvon.$jahrvon.'-'.$monatbis.$jahrbis.'_'.$i.'_'.$job['id'];
        $pfad_rel = $monatvon.$jahrvon.'-'.$monatbis.$jahrbis.'_'.$i.'_'.$job['id'];
      }
      $zip = false;
      $gz = false;
      if($job['format'] === 'zip')
      {
        $zip = true;
      }
      if($job['format'] === 'gz')
      {
        $gz = true;
      }
      
      if($zip)
      {
        if(!class_exists('ZipArchive'))
        {
          $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'Fehler', kommentar = 'Die Klasse ZipArchiv ist nicht installiert' WHERE id = '".$job['id']."' LIMIT 1");
          return false;
        }
        $ziparchiv = new ZipArchive;
        if($ziparchiv->open($pfad.'.zip', ZipArchive::CREATE) !== true)
        {
          $this->app->erp->LogFile($pfad.'.zip '.(file_exists($pfad.'.zip')?'ex':'ex nicht'));
          
          $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'Fehler', kommentar = 'Es konnte kein Zip-Archiv erstellt werden' WHERE id = '".$job['id']."' LIMIT 1");
          return false;
        }
      }elseif($gz){
        if(!mkdir($pfad) && !is_dir($pfad)){
          echo "$pfad konnte nicht erstellt werden\r\n";
        }
        if(!function_exists('system'))
        {
          $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'Fehler', kommentar = 'Die Funktion system() ist deaktivert: Es ist nicht moeglich gz-Archive zu erzeugen' WHERE id = '".$job['id']."' LIMIT 1");
          return false;
        }
      }
      foreach($tabellen as $table)
      {
        if(!$gz && !$zip)
        {
          continue;
        }
        if($allepdf)
        {
          echo "allepdf\r\n";
          $sql = "SELECT t.*, p.dateiname, p.belegnummer as pbelegnr FROM $table t LEFT JOIN pdfarchiv p ON p.table_id = t.id AND p.table_name = '$table' AND CHAR_LENGTH(p.belegnummer) > 2 AND p.belegnummer <> 'SAB' WHERE p.dateiname <> '' AND not isnull(p.dateiname) AND  t.belegnr <> ''  AND t.status <> 'angelegt' AND t.status <> 'angelegta' AND t.status <> 'a' 
          AND( year(t.datum) >= '".$jahrvon."' AND year(t.datum) <= '".$jahrbis."' AND month(t.datum) >= '".$monatvon."' AND month(t.datum) <= '".$monatbis."') ORDER BY p.table_id, p.id DESC
          ";
          $check = $this->app->DB->Query($sql);
          //echo $sql."\r\n";
          echo mysqli_error($this->app->DB->connection);
          $centries = 0;
          while($row = $this->app->DB->Fetch_Array($check))
          {
            $centries++;
            if($centries % 10 === 0 && function_exists('gc_enabled') && function_exists('gc_collect_cycles') && gc_enabled())
            {
              $cgc = gc_collect_cycles();
              if($cgc > 0)
              {
                $this->app->erp->LogFile($cgc.' cycles collected');
              }
            }
            echo $row['id'];
            $this->app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");
            $dateipfad = Briefpapier::getPDFfolder($this->folder.'/'.$table,$row['id'],$row['dateiname']);
            //if(file_exists($this->folder.'/'.$table.'/'.$row['dateiname']))
            if(file_exists($dateipfad))
            {
              
              echo " ".$row['dateiname'];
              $belegnr = $row['pbelegnr'];
              if(!$belegnr)$belegnr = $this->app->DB->Select("SELECT belegnr FROM $table WHERE id = '".$table['table_id']."' LIMIT 1");
              if(!$belegnr)$belegnr = $table;
              
              $ziel = $pfad.'/'.$table.'/'.$belegnr.'.pdf';
              $zielzip  = $table.'/'.$belegnr.'.pdf';
              $i = 0;
              while(file_exists($ziel))
              {
                $i++;
                $ziel = $pfad.'/'.$table.'/'.$belegnr.'_'.$i.'.pdf';
                $zielzip  = $table.'/'.$belegnr.'_'.$i.'.pdf';
              }
              if($zip)
              {
                //echo "addfile: ".$ziparchiv->addFile($this->folder.'/'.$table.'/'.$row['dateiname'], $zielzip);
                echo "addfile: ".$ziparchiv->addFile($dateipfad, $zielzip);
                echo "\r\n";
                echo "numfiles: " . $ziparchiv->numFiles . "\n";
                echo "status:" . $ziparchiv->status . "\n";
                echo "status:" . $ziparchiv->getStatusString()."\r\n";
              }else{
                if(!file_exists($pfad.'/'.$table))
                {
                  if(!mkdir($pfad.'/'.$table) && !is_dir($pfad.'/'.$table))
                  {
                    echo 'Es konnte der Pfad '.$pfad.'/'.$table.' nicht erstellt werden'."\r\n";
                  }
                }
                //copy($this->folder.'/'.$table.'/'.$row['dateiname'], $ziel);
                copy($dateipfad, $ziel);
              }
            }
            echo "\r\n";
          }
        }else{
          echo "nur neueste\r\n";
          $check = $this->app->DB->Query("SELECT t.*,p.table_id, p.dateiname, p.belegnummer as pbelegnr FROM $table t LEFT JOIN pdfarchiv p ON p.table_id = t.id AND p.table_name = '$table' AND CHAR_LENGTH(p.belegnummer) > 2 AND p.belegnummer <> 'SAB' WHERE p.dateiname <> '' AND t.belegnr <> '' AND t.status <> 'angelegt' AND t.status <> 'angelegta' AND t.status <> 'a' 
          AND( year(t.datum) >= '".$jahrvon."' AND year(t.datum) <= '".$jahrbis."' AND month(t.datum) >= '".$monatvon."' AND month(t.datum) <= '".$monatbis."') ORDER BY p.table_id, p.id DESC
          ");
          $lasttable_id = false;
          $centries = 0;
          while($row = $this->app->DB->Fetch_Array($check))
          {
            $centries++;
            if($centries % 10 === 0 && function_exists('gc_enabled') && function_exists('gc_collect_cycles') && gc_enabled())
            {
              $cgc = gc_collect_cycles();
              if($cgc > 0)
              {
                $this->app->erp->LogFile($cgc.' cycles collected');
              }
            }
            echo $row['id'];
            $this->app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");
            if($lasttable_id != $row['table_id'])
            {
              $ok = false;
              $lasttable_id = $row['table_id'];
            }
            if(!$ok)
            {

              $dateipfad = Briefpapier::getPDFfolder($this->folder.'/'.$table,$row['id'],$row['dateiname']);

              //if(file_exists($this->folder.'/'.$table.'/'.$row['dateiname']))
              if(file_exists($dateipfad))
              {
                echo " ".$row['dateiname'];
                $ok = true;
                $belegnr = $row['pbelegnr'];
                if(!$belegnr)$belegnr = $this->app->DB->Select("SELECT belegnr FROM $table WHERE id = '".$table['table_id']."' LIMIT 1");
                if(!$belegnr)$belegnr = $table;
                
                $ziel = $pfad.'/'.$table.'/'.$belegnr.'.pdf';
                $zielzip  = $table.'/'.$belegnr.'.pdf';
                $i = 0;
                while(file_exists($ziel))
                {
                  $i++;
                  $zielzip  = $table.'/'.$belegnr.'_'.$i.'.pdf';
                  $ziel = $pfad.'/'.$table.'/'.$belegnr.'_'.$i.'.pdf';
                }
                if($zip)
                {
                  //$ziparchiv->addFile($this->folder.'/'.$table.'/'.$row['dateiname'], $zielzip);
                  $ziparchiv->addFile($dateipfad, $zielzip);
                  echo "numfiles: " . $ziparchiv->numFiles . "\n";
                  echo "status:" . $ziparchiv->status . "\n";
                  echo "status:" . $ziparchiv->getStatusString()."\r\n";
                }else{
                  if(!file_exists($pfad.'/'.$table))
                  {
                    if(!mkdir($pfad.'/'.$table) && !is_dir($pfad.'/'.$table))
                    {
                      echo $pfad.'/'.$table." konnte nicht erstellt werden\r\n";
                    }
                  }
                  //copy($this->folder.'/'.$table.'/'.$row['dateiname'], $ziel);
                  copy($dateipfad, $ziel);
                }
              }
            }
            echo "\r\n";
          }
        }
      }
      $this->app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");
      
      if($zip)
      {
        if(TRUE !== $ziparchiv->close())
        {
          $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'Fehler', kommentar = 'Fehler beim Speichern des Zip-Archives' WHERE id = '".$job['id']."' LIMIT 1");
          return false;
        }
        $this->app->erp->LogFile($pfad.'.zip '.(file_exists($pfad.'.zip')?'ex':'ex nicht'));
        $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'abgeschlossen',kommentar = '', datei = '".$pfad_rel.".zip' WHERE id = '".$job['id']."' LIMIT 1");
      }elseif($gz){
        system("cd ".$this->folder." && tar cfz ".$pfad.".tar.gz $pfad_rel $1>/dev/null");
        $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'abgeschlossen',kommentar = '', datei = '".$pfad_rel.".tar.gz' WHERE id = '".$job['id']."' LIMIT 1");
      }else{
        $this->app->DB->Update("UPDATE pdfarchiv_jobs SET status = 'abgeschlossen',kommentar = '', datei = '' WHERE id = '".$job['id']."' LIMIT 1");
      }
      
      $this->app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_app' AND aktiv = 1");
      //system("rm -R $pfad");
    }
    return true;
  }
}


/*
echo "Suche nach nicht archivierten Dokumenten..\r\n";

$dokumente = array('auftrag','angebot','gutschrift','rechnung','bestellung','lieferschein','anfrage');
foreach($dokumente as $table)
{
  
  
  echo $table."\r\n";
  $check = $app->DB->Query("SELECT t.* FROM $table t LEFT JOIN pdfarchiv p ON p.table_id = t.id AND p.table_name = '$table' WHERE isnull(p.id) AND belegnr <> '' AND status <> 'storniert' AND status <> 'angelegt' AND status <> 'angelegta' AND status <> 'a' AND status <> ''");
  while($row = $app->DB->Fetch_Array($check))
  {
    echo $row['id']."\r\n";
    $name = ucfirst($table).'PDF';
    $nameget = 'Get'.ucfirst($table);
    $Brief = new $name($app, $row['projekt']);
   

    $Brief->$nameget($row['id']);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument();
   
  }
  
}
*/
