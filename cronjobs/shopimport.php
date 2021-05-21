<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
include_once(dirname(__DIR__).'/conf/main.conf.php');
if(!class_exists('DB')){
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.mysql.php');
}
include_once(dirname(__DIR__).'/www/lib/imap.inc.php');
if(!class_exists('erpAPI')){
  include_once(dirname(__DIR__) . '/www/lib/class.erpapi.php');
}
if(file_exists(dirname(__DIR__).'/www/lib/class.erpapi_custom.php') &&
  !class_exists('erpAPICustom')){
  include_once(dirname(__DIR__) . '/www/lib/class.erpapi_custom.php');
}
if(!class_exists('Remote')){
  include_once(dirname(__DIR__) . '/www/lib/class.remote.php');
}
if(!class_exists('RemoteCustom') &&
  file_exists(dirname(__DIR__).'/www/lib/class.remote_custom.php')){
  include_once(dirname(__DIR__) . '/www/lib/class.remote_custom.php');
}
if(!class_exists('HttpClient')){
  include_once(dirname(__DIR__) . '/www/lib/class.httpclient.php');
}
if(!class_exists('AES')){
  $aes = '';
  $phpversion = phpversion();
  if($phpversion{0} == '7' && (int)$phpversion{2} > 0)
  {
    $aes = '2';
  }
  if($aes == 2 && is_file(dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php')){
    include_once(dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php');
  }else{
    include_once(dirname(__DIR__) . '/www/lib/class.aes.php');
  }
}
if(!class_exists('Shopimport')){
  include_once(dirname(__DIR__) . '/www/pages/shopimport.php');
}
if(!class_exists('PHPMailer')){
  include_once(dirname(__DIR__) . '/www/plugins/phpmailer/class.phpmailer.php');
}
if(!class_exists('SMTP')){
  include_once(dirname(__DIR__) . '/www/plugins/phpmailer/class.smtp.php');
}
if(!class_exists('Secure')){
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.secure.php');
}
if(!class_exists('StringCleaner') && file_exists(dirname(__DIR__) . '/phpwf/plugins/class.stringcleaner.php'))
{
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.stringcleaner.php');
}

if(!class_exists('FormHandler')){
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.formhandler.php');
}
if(!class_exists('image')){
  include_once(dirname(__DIR__) . '/www/lib/class.image.php');
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
if(!class_exists('PDF_EPS')){
  include_once(dirname(__DIR__) . '/www/lib/pdf/fpdf_final.php');
}
if(!class_exists('SuperFPDF')){
  include_once(dirname(__DIR__) . '/www/lib/dokumente/class.superfpdf.php');
}
$classes = array('briefpapier','auftrag','angebot','rechnung','gutschrift','lieferschein');
foreach($classes as $class)
{
  if(file_exists(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'_custom.php')
    && !class_exists(ucfirst($class).($class != 'briefpapier'?'PDF':'').'Custom'))
  {
    include_once(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'_custom.php');
  }elseif(file_exists(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'.php')
    && !class_exists(ucfirst($class).($class != 'briefpapier'?'PDF':'')))
  {
    include_once(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'.php');
  }
}

if(!defined('FPDF_FONTPATH'))
{
  define('FPDF_FONTPATH',dirname(__DIR__).'/www/lib/pdf/font/');
}

if(!class_exists('WawiString')){
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.string.php');
}
if(!class_exists('app_t'))
{
  class app_t extends ApplicationCore {
    var $DB;
    var $erp;
    var $user;
    var $remote;

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

if(!class_exists('User'))
{
  class User
  {
    var $app;
    function __construct($app)
    {
      $this->app = $app;
    }
    
    function GetName()
    {
      return 'Cronjob';
    }
    
    function GetID()
    {
      return 0;
    }
    
    function GetFirma()
    {
      return 1;
    }
    
    function GetType()
    {
      return 'admin';
    }
    
    function DefaultProjekt()
    {
      return $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='1' LIMIT 1");
    }
    
    function GetAdresse()
    {
      return 0;
    }
    
    function GetUsername()
    {
      return 'Cronjob';
    }
    
  }
  
}

//ENDE
if(empty($app) || !class_exists('ApplicationCore') || !($app instanceof ApplicationCore)) {
  $app = new app_t();
}
if(empty($app->Conf)) {
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}
if(empty($app->erp)) {
  if(class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  }
  else {
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}
$app->String = new WawiString();
if(empty($app->remote)) {
  if(class_exists('RemoteCustom')) {
    $remote = new RemoteCustom($app);
  }
  else {
    $remote = new Remote($app);
  }

  $app->remote = $remote;
}
$app->Secure = new Secure($app);
$app->User = new User($app);

$app->FormHandler = new FormHandler($app);
$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");
$benutzername = $app->erp->Firmendaten('benutzername');
$passwort = $app->erp->Firmendaten('passwort');
$host = $app->erp->Firmendaten('host');
$port = $app->erp->Firmendaten('port');
$mailssl = $app->erp->Firmendaten('mailssl');
$mailanstellesmtp = $app->erp->Firmendaten('mailanstellesmtp');
$noauth = $app->erp->Firmendaten('noauth');


// mail
$app->mail = new PHPMailer($app);
$app->mail->CharSet = 'UTF-8';
$app->mail->PluginDir='plugins/phpmailer/';

if($mailanstellesmtp=='1'){
  $app->mail->IsMail();
} else {
  $app->mail->IsSMTP();

  if($noauth=='1') {
    $app->mail->SMTPAuth = false;
  }
  else {
    $app->mail->SMTPAuth   = true;
  }

  if($mailssl==1){
    $app->mail->SMTPSecure = 'tls';                 // sets the prefix to the servier
  }
  else if ($mailssl==2){
    $app->mail->SMTPSecure = 'ssl';                 // sets the prefix to the servier
  }

  $app->mail->Host       = $host;

  $app->mail->Port       = $port;                   // set the SMTP port for the GMAIL server

  $app->mail->Username   = $benutzername;  // GMAIL username
  $app->mail->Password   = $passwort;            // GMAIL password
}
$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND (parameter = 'shopimport') AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'shopimport' AND aktiv = 1")) {
  return;
}
usleep(mt_rand(100000,1000000));
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'shopimport' AND aktiv = 1")) {
  return;
}
$app->DB->Update(
  "UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopimport'"
);
$shops = $app->DB->SelectArr("SELECT * FROM shopexport WHERE aktiv = 1 AND cronjobaktiv = 1  AND (demomodus = 0 OR isnull(demomodus)) AND
((url <> '' and passwort <> '' AND token <> '' and not isnull(passwort) and not isnull(url)) OR shoptyp = 'intern' OR shoptyp = 'custom')");
if($shops) {
  foreach($shops as $shop) {
    if(method_exists($app->erp, 'canRunCronjob')
      && !$app->erp->canRunCronjob(['shopimport'])) {
      return;
    }
    $id = $shop['id'];
    $projekt = $shop['projekt'];
    $demomodus = $shop['demomodus'];
    $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopimport'");
    $direktimport = $shop['direktimport'];
    $app->erp->LogFile('Shopimport '.$id);
    if($direktimport)
    {
      $app->erp->LogFile('direktimport '.$id);
      $shopimp = new Shopimport($app, true);
    }

    $pageContents ='';
    try{
      $pageContents = $app->remote->RemoteConnection($id);
    }catch (Exception $ex){
    }

    if($pageContents=="success")
    {
      $holealle = $app->DB->Select("SELECT holealle FROM shopexport WHERE id = '$id' LIMIT 1");
      $nummersyncstatusaendern = $shop['nummersyncstatusaendern'];
      $statusaendern = $shop['auftragabgleich'];
      $zeitraum = $app->DB->SelectArr("SELECT datumvon, datumbis,tmpdatumvon, tmpdatumbis, anzgleichzeitig FROM shopexport WHERE id = '$id' LIMIT 1");
      if($zeitraum)$zeitraum = reset($zeitraum);
      $anzgleichzeitig = 1;
      if(isset($zeitraum['anzgleichzeitig']))$anzgleichzeitig = (int)$zeitraum['anzgleichzeitig'];
      
      if($anzgleichzeitig > 1)
      {
        $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopimport'");
        $result = $app->remote->RemoteGetAuftrag($id);
        $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopimport'");
        if($result && is_array($result) && isset($result[0]))
        {
          $maxtime = false;
          $mintime = false;
          $cresult = count($result);
          for($i = 0; $i < $cresult; $i++)
          {
            $projekt = $shop['projekt'];
            $auftrag = $result[$i]['id'];
            if(isset($result[$i]['warenkorbjson']))
            {
              $isjson = true;
              $tmpwarenkorb = json_decode(base64_decode($result[$i]['warenkorbjson']), true);
            }else{
              $isjson = false;
              $tmpwarenkorb = unserialize(base64_decode($result[$i]['warenkorb']));
            }
            if(!empty($tmpwarenkorb['zeitstempel']))
            {
              $time = strtotime($tmpwarenkorb['zeitstempel']);
              if($time < 0)$time = 0;
              if($maxtime === false)$maxtime = $time;
              if($mintime === false)$mintime = $time;
              if($time > $maxtime)$maxtime = $time;
              if($time < $mintime)$mintime = $time;
            }
            if(!empty($tmpwarenkorb['projekt']) && $app->DB->Select("SELECT id FROM projekt WHERE id = '".(int)$tmpwarenkorb['projekt']."' LIMIT 1"))$projekt = (int)$tmpwarenkorb['projekt'];
            $onlinebestellnummer = $tmpwarenkorb['onlinebestellnummer'];
            $letzteonlinebestellnummer = $tmpwarenkorb['onlinebestellnummer'];
            if(!empty($tmpwarenkorb['useorderid']) || (!is_numeric($onlinebestellnummer) && trim((String)$onlinebestellnummer) !== ''))
            {
              $onlinebestellnummer = $tmpwarenkorb['auftrag'];
            }
            if(isset($tmpwarenkorb['subshop']) && $tmpwarenkorb['subshop'])
            {
              $subshopprojekt = $app->DB->Select("SELECT projekt FROM shopexport_subshop WHERE shop = '".$id."' AND aktiv = 1 AND subshopkennung = '".$app->DB->real_escape_string($tmpwarenkorb['subshop'])."' LIMIT 1");
              if($subshopprojekt)
              {
                $projekt = $subshopprojekt;
              }
            }
            unset($tmpwarenkorb);
            if($holealle && $onlinebestellnummer)
            {
              $neue_nummer = (int)$onlinebestellnummer+1;
              $app->DB->Update("UPDATE shopexport SET ab_nummer = '$neue_nummer' WHERE id = '$id'");
            }
            $sessionid = $result[$i]['sessionid'];
            if($isjson)
            {
              $warenkorb = $result[$i]['warenkorbjson'];
            }else{
              $warenkorb = $result[$i]['warenkorb'];
            }
            $logdatei = $result[$i]['logdatei'];
            if(empty($logdatei))$logdatei = date('Y-m-d H:i:s');
            

            $standardcheck = true;
            $modulename = $app->DB->Select(
              sprintf(
                "SELECT modulename FROM shopexport WHERE id = %d AND modulename <> '' AND (shoptyp = 'intern')",
                $id
              )
            );
            $shopIds = [$id];
            $otherModules= empty($modulename)?null:
              $app->DB->SelectFirstCols(
                sprintf(
                  "SELECT id 
                  FROM shopexport 
                  WHERE modulename = '%s' AND id <> %d",
                  $app->DB->real_escape_string($modulename), $id
                )
              );
            if(!empty($otherModules)) {
              if($app->erp->ModulVorhanden($modulename)) {
                $obj = $app->erp->LoadModul($modulename);
                if($obj){
                  $konfiguration = $obj->EinstellungenStruktur();
                  if($konfiguration && isset($konfiguration['globalerauftragsnummernkreis']) && $konfiguration['globalerauftragsnummernkreis']) {
                    $shopIds = array_merge($shopIds, $otherModules);
                    $standardcheck = false;
                    /*$checkdoppeltimported = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and (shopid = '$id' OR ".implode(" OR ", $shopinternids).") and warenkorb = '".$app->DB->real_escape_string($warenkorb)."' AND trash = 0
                                  AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                  LIMIT 1");*/
                    /*$checkdoppeltimported = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and (shopid = '$id' OR ".implode(" OR ", $shopinternids).") AND trash = 0
                                  AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                  LIMIT 1");
                    $checkdoppelt = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and (shopid = '$id' OR ".implode(" OR ", $shopinternids).")  LIMIT 1");*/
                  }
                }
              }
            }
            $checkdoppelt = $app->DB->Select(
              sprintf(
                "SELECT id 
                FROM shopimport_auftraege 
                WHERE extid = '%s' and shopid IN (%s) 
                LIMIT 1",
                $app->DB->real_escape_string($auftrag), implode(',', $shopIds)
              )
            );
            /*if($standardcheck) {
              $checkdoppelt = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and shopid = '$id' LIMIT 1");
            }*/
            /*if($standardcheck)$checkdoppeltimported = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and shopid = '$id' and warenkorb = '".$app->DB->real_escape_string($warenkorb)."' AND trash = 0
            AND (imported = 0 OR imported = 1)
            LIMIT 1");*/
            //if($standardcheck)
            $insid = null;
            $checkdoppeltimported = $app->DB->Select(
              sprintf(
                "SELECT id 
                FROM shopimport_auftraege 
                WHERE extid = '%s' and shopid IN (%s) AND trash = 0
                LIMIT 1",
                $app->DB->real_escape_string($auftrag), implode(',', $shopIds)
              )
            );
            if(!$checkdoppeltimported)
            {
              $app->DB->Insert("INSERT INTO shopimport_auftraege (id,extid,sessionid,warenkorb,imported,projekt,bearbeiter,logdatei) 
                  VALUES('','".$app->DB->real_escape_string($auftrag)."','".$app->DB->real_escape_string($sessionid)."','".$app->DB->real_escape_string($warenkorb)."','0','$projekt','Cronjob','".$app->DB->real_escape_string($logdatei)."')");
              $insid = $app->DB->GetInsertID();
              if($insid) {
                $app->DB->Update("UPDATE shopimport_auftraege set shopid = '$id' where id = '$insid'");
                if($isjson)$app->DB->Update("UPDATE shopimport_auftraege set jsonencoded = 1 where id = '$insid'");
              }
              $app->DB->Update("UPDATE shopimport_auftraege set bestellnummer = '".$app->DB->real_escape_string($letzteonlinebestellnummer)."' where id = '$insid'");
            }
            unset($letzteonlinebestellnummer);
              //$app->DB->Insert("INSERT INTO shopimport_auftraege (id,extid,sessionid,warenkorb,imported,projekt,bearbeiter,logdatei) 
              //    VALUES('','".$app->DB->real_escape_string($auftrag)."','".$app->DB->real_escape_string($sessionid)."','".$app->DB->real_escape_string($warenkorb)."','0','$projekt','Cronjob','".$app->DB->real_escape_string($logdatei)."')");
              //$insid = $app->DB->GetInsertID();
              //if($insid)$app->DB->Update("UPDATE shopimport_auftraege set shopid = '$id' where id = '$insid'");
              if(!$direktimport)
              {

              }else{
                if(!$checkdoppelt && !$checkdoppeltimported)
                {
                  $warenkorb = $app->DB->Select("SELECT warenkorb FROM shopimport_auftraege WHERE id = '$insid' LIMIT 1");
                  if($app->DB->Select("SELECT id FROM shopimport_auftraege WHERE id = '$insid' AND jsonencoded = 1 LIMIT 1"))
                  {
                    $warenkorb = json_decode(base64_decode($warenkorb),true);
                  }else{
                    $warenkorb = unserialize(base64_decode($warenkorb));
                  }
                  if($warenkorb['name']===""){
                    $warenkorb['name']=$warenkorb['ansprechpartner'];
                    $warenkorb['ansprechpartner'] = '';
                  }
                  if($warenkorb['name'] == "" && $warenkorb['lieferadresse_name']!="")
                  {
                    $warenkorb['name'] = $warenkorb['lieferadresse_name'];
                  }
                  if($warenkorb['lieferadresse_name']===""){
                    $warenkorb['lieferadresse_name']=$warenkorb['lieferadresse_ansprechpartner'];
                    $warenkorb['lieferadresse_ansprechpartner'] = '';
                  }
                  if($shop['utf8codierung']=="1")
                  {
                    $warenkorb = $app->erp->CleanDataBeforImportUTF8($warenkorb, false);
                  } else {
                    $warenkorb = $app->erp->CleanDataBeforImport($warenkorb, false);
                  }
                  foreach($warenkorb as $k => $v) $warenkorb[$k] = $app->erp->fixeUmlaute($v);
                  $kundenurvonprojekt = $app->DB->Select("SELECT kundenurvonprojekt FROM shopexport WHERE id = '$id' LIMIT 1");
                  $adresseprojekt = '';
                  if($kundenurvonprojekt)
                  {
                    $adresseprojekt = $app->DB->Select("SELECT projekt FROM shopexport WHERE id = '$id' LIMIT 1");
                  
                    if(isset($warenkorb['subshop']) && $warenkorb['subshop'])
                    {
                      $subshopprojekt = $app->DB->Select("SELECT projekt FROM shopexport_subshop WHERE shop = '".$id."' AND aktiv = 1 AND subshopkennung = '".$app->DB->real_escape_string($warenkorb['subshop'])."' LIMIT 1");
                      if($subshopprojekt)
                      {
                        $adresseprojekt = $subshopprojekt;
                      }
                    }
                  }

                  $kundenurvonprojekt = $app->DB->Select("SELECT kundenurvonprojekt FROM shopexport WHERE id = '$id' LIMIT 1");
                  if($kundenurvonprojekt)
                  {
                    $adresseprojekt = " AND projekt = '".$adresseprojekt."' ";
                  }else{
                    $adresseprojekt = '';
                  }
                  
                  $checkid = $app->DB->Select("SELECT id FROM adresse WHERE name='".$app->erp->ReadyForPDF($warenkorb['name'])."' AND email='".$app->erp->ReadyForPDF($warenkorb['email'])."' AND abteilung='".$app->erp->ReadyForPDF($warenkorb['abteilung'])."'
                      AND strasse='".$app->erp->ReadyForPDF($warenkorb['strasse'])."' AND plz='".$app->erp->ReadyForPDF($warenkorb['plz'])."' AND ort='".$app->erp->ReadyForPDF($warenkorb['ort'])."' AND geloescht!=1 $adresseprojekt LIMIT 1");
                  if($checkid)
                  {
                    $checkkundennummer = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkid' LIMIT 1");
                    if(!$checkkundennummer)
                    {
                      $app->erp->AddRolleZuAdresse($checkid, "Kunde", "von", "Projekt", $projekt);
                      $checkkundennummer = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkid' LIMIT 1");
                    }
                    $checkid = $checkkundennummer;
                  }
                  if(!$checkid)
                  {
                    if($warenkorb['email']!="amazon_import_bounce@nfxmedia.de")
                    {
                      if($warenkorb['email'] != '')
                      {
                        $checkidemailid = $app->DB->Select("SELECT id FROM adresse WHERE email='".$warenkorb['email']."' and email <> '' AND geloescht!=1 $adresseprojekt LIMIT 1");
                        if($checkidemailid)
                        {
                          $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkidemailid' LIMIT 1");
                          if(!$checkidemail)
                          {
                            $app->erp->AddRolleZuAdresse($checkidemailid, "Kunde", "von", "Projekt", $projekt);
                            $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkidemailid' LIMIT 1");
                          }
                        }
                      }else{
                        $checkidemailid = $app->DB->Select("SELECT id FROM adresse WHERE name='".$app->erp->ReadyForPDF($warenkorb['name'])."'   AND strasse='".$app->erp->ReadyForPDF($warenkorb['strasse'])."' AND plz='".$app->erp->ReadyForPDF($warenkorb['plz'])."' AND ort='".$app->erp->ReadyForPDF($warenkorb['ort'])."' AND geloescht!=1 $adresseprojekt LIMIT 1");
                        if($checkidemailid)
                        {
                          $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkidemailid' LIMIT 1");
                          if(!$checkidemail)
                          {
                            $app->erp->AddRolleZuAdresse($checkidemailid, "Kunde", "von", "Projekt", $projekt);
                            $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkidemailid' LIMIT 1");
                          }
                        }                        
                      }
                    }
                    //if($checkidemail == '')
                    //{
                    //  $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE name LIKE '".$app->erp->ReadyForPDF($warenkorb['name'])."' AND ort LIKE '".$app->erp->ReadyForPDF($warenkorb['ort'])."' $adresseprojekt AND geloescht!=1 LIMIT 1");                  
                    //}
                  }
                  //echo "SELECT kundennummer FROM adresse WHERE email='".$warenkorb[email]."'  LIMIT 1";
                  if(!empty($warenkorb['kundennummer']))$validkundennummer = $app->DB->Select("SELECT kundennummer FROM adresse WHERE kundennummer='".$warenkorb['kundennummer']."' AND geloescht!=1 LIMIT 1");
                  $kundennummer = 0;
                  $import_kundennummer = "";
                  if($warenkorb['kundennummer']!="" && $validkundennummer==$warenkorb['kundennummer'])
                  {
                    $kundennummer = 1;
                    $import_kundennummer = $validkundennummer;
                  }elseif ($checkid!="")
                  {
                    $kundennummer = 1;
                    $import_kundennummer = $checkid;                
                  }
                  elseif ($checkidemail!="")
                  {
                    $kundennummer = 1;
                    $import_kundennummer = $checkidemail;
                  }
                  //echo "Kundennummer: ".$import_kundennummer."\r\n";
                  $unbekanntezahlungsweisen = null;
                  $shopimp->KundeAnlegenUpdate($insid,$auftrag, $warenkorb, $kundennummer, $import_kundennummer, $unbekanntezahlungsweisen);
                  $checkid = '';
                  $checkidemail = '';
                  $validkundennummer = '';
                }
              }

            
            
            if($demomodus!="1")	
            {
              $app->remote->RemoteDeleteAuftrag($id,$auftrag,$onlinebestellnummer);
            }
            elseif($demomodus == "1")
            {
              break;
            }

          }
          if(!$demomodus)
          {
            if(!$maxtime)$maxtime = strtotime(date('Y-m-d H:i:s'));
            $datumvon = strtotime($zeitraum['datumvon']);
            $datumbis = strtotime($zeitraum['datumbis']);
            $tmpdatumvon = strtotime($zeitraum['tmpdatumvon']);
            $tmpdatumbis = strtotime($zeitraum['tmpdatumbis']);
            if($datumvon < 0)$datumvon = 0;
            if($datumbis < 0)$datumbis = 0;
            if($tmpdatumvon < 0)$tmpdatumvon = 0;
            if($tmpdatumbis < 0)$tmpdatumbis = 0;
            $app->DB->Update("UPDATE shopexport SET datumvon = '".date('Y-m-d H:i:s',$maxtime)."', tmpdatumbis = NULL WHERE id = '$id' LIMIT 1");
            /*
            if($datumvon)
            {
              if($datumbis)
              {
                if($tmpdatumvon)
                {
                  if(count($result) < $anzgleichzeitig)
                  {
                    $app->DB->Update("UPDATE shopexport SET datumvon = tmpdatumvon WHERE id = '$id' LIMIT 1");
                    $app->DB->Update("UPDATE shopexport SET tmpdatumvon = NULL, tmpdatumbis = NULL WHERE id = '$id' LIMIT 1");
                  }else{
                    $app->DB->Update("UPDATE shopexport SET tmpdatumbis = '".date('Y-m-d H:i:s',$mintime-1)."' WHERE id = '$id' LIMIT 1");
                  }
                }else{
                  if(count($result) < $anzgleichzeitig)
                  {
                    $app->DB->Update("UPDATE shopexport SET datumvon = datumbis WHERE id = '$id' LIMIT 1");
                  }else{
                    $app->DB->Update("UPDATE shopexport SET tmpdatumbis = '".date('Y-m-d H:i:s',$mintime-1)."' WHERE id = '$id' LIMIT 1");
                    $app->DB->Update("UPDATE shopexport SET tmpdatumvon = '".date('Y-m-d H:i:s',$maxtime)."' WHERE id = '$id' LIMIT 1");
                  }
                }
              }else{
                //von aber kein bis
                if($tmpdatumvon)
                {
                  if(count($result) < $anzgleichzeitig)
                  {
                    if($maxtime)$app->DB->Update("UPDATE shopexport SET datumvon = '".date('Y-m-d H:i:s',$maxtime)."' WHERE id = '$id' LIMIT 1");
                    $app->DB->Update("UPDATE shopexport SET tmpdatumvon = NULL, tmpdatumbis = NULL WHERE id = '$id' LIMIT 1");
                  }else{
                    
                    if($maxtime)$app->DB->Update("UPDATE shopexport SET tmpdatumbis = '".date('Y-m-d H:i:s',$mintime)."' WHERE id = '$id' LIMIT 1");
                  }
                }else{
                  if(count($result) < $anzgleichzeitig)
                  {
                    if($maxtime)$app->DB->Update("UPDATE shopexport SET datumvon = '".date('Y-m-d H:i:s',$maxtime)."' WHERE id = '$id' LIMIT 1");
                    $app->DB->Update("UPDATE shopexport SET tmpdatumvon = NULL, tmpdatumbis = NULL WHERE id = '$id' LIMIT 1");
                  }else{
                    if($maxtime)$app->DB->Update("UPDATE shopexport SET tmpdatumvon = '".date('Y-m-d H:i:s',$maxtime)."' WHERE id = '$id' LIMIT 1");
                    if($maxtime)$app->DB->Update("UPDATE shopexport SET tmpdatumbis = '".date('Y-m-d H:i:s',$mintime)."' WHERE id = '$id' LIMIT 1");
                  }
                } 
              }
            }else{
              if($datumbis)
              {
                //Kein von aber ein bis
                if($tmpdatumvon)
                {
                  if(count($result) < $anzgleichzeitig)
                  {
                    $app->DB->Update("UPDATE shopexport SET datumvon = tmpdatumvon WHERE id = '$id' LIMIT 1");
                    $app->DB->Update("UPDATE shopexport SET tmpdatumvon = NULL, tmpdatumbis = NULL WHERE id = '$id' LIMIT 1");
                  }else{
                    $app->DB->Update("UPDATE shopexport SET tmpdatumbis = '".date('Y-m-d H:i:s',$mintime-1)."' WHERE id = '$id' LIMIT 1");
                  }
                }else{
                  if(count($result) < $anzgleichzeitig)
                  {
                    $app->DB->Update("UPDATE shopexport SET datumvon = datumbis WHERE id = '$id' LIMIT 1");
                  }else{
                    $app->DB->Update("UPDATE shopexport SET tmpdatumbis = '".date('Y-m-d H:i:s',$mintime-1)."' WHERE id = '$id' LIMIT 1");
                    $app->DB->Update("UPDATE shopexport SET tmpdatumvon = '".date('Y-m-d H:i:s',$maxtime)."' WHERE id = '$id' LIMIT 1");
                  }
                }
              }else{
                if($tmpdatumvon)
                {
                  if(count($result) < $anzgleichzeitig)
                  {
                    $app->DB->Update("UPDATE shopexport SET datumvon = tmpdatumvon WHERE id = '$id' LIMIT 1");
                    $app->DB->Update("UPDATE shopexport SET tmpdatumvon = NULL, tmpdatumbis = NULL WHERE id = '$id' LIMIT 1");
                  }else{

                    $app->DB->Update("UPDATE shopexport SET tmpdatumbis = '".date('Y-m-d H:i:s',$mintime-1)."' WHERE id = '$id' LIMIT 1");
                  }
                }else{
                  if(count($result) < $anzgleichzeitig)
                  {
                    if(!$datumvon)$app->DB->Update("UPDATE shopexport SET datumvon = '".date('Y-m-d H:i:s',$maxtime+1)."' WHERE id = '$id' LIMIT 1");
                  }else{
                    $app->DB->Update("UPDATE shopexport SET tmpdatumbis = '".date('Y-m-d H:i:s',$mintime-1)."' WHERE id = '$id' LIMIT 1");
                    $app->DB->Update("UPDATE shopexport SET tmpdatumvon = '".date('Y-m-d H:i:s',$maxtime)."' WHERE id = '$id' LIMIT 1");
                  }
                }
              }
            }*/
          }
        }
        else{
          if(!$demomodus)
          {
            if(is_array($result) && !empty($result['zeitstempel']))
            {
              if($app->DB->Select("SELECT id FROM shopexport WHERE id = '$id' AND datumvon < '".date('Y-m-d H:i:s',strtotime($result['zeitstempel']))."' LIMIT 1"))
              {
                $app->DB->Update("UPDATE shopexport SET datumvon = '".date('Y-m-d H:i:s',strtotime($result['zeitstempel']))."' WHERE id = '$id' AND datumvon < '".date('Y-m-d H:i:s',strtotime($result['zeitstempel']))."' LIMIT 1");
              }else{
                $app->DB->Update("UPDATE shopexport SET datumvon = DATE_ADD(datumvon, INTERVAL 1 SECOND) WHERE id = '$id' LIMIT 1");
              }
              $app->DB->Update("UPDATE shopexport SET tmpdatumbis = NULL WHERE id = '$id' LIMIT 1");
            }else
            {
              $app->DB->Update("UPDATE shopexport SET datumvon = DATE_ADD(datumvon, INTERVAL 1 SECOND) WHERE id = '$id' LIMIT 1");
            }
            if(!$maxtime)$maxtime = strtotime(date('Y-m-d H:i:s'));
            $datumvon = strtotime($zeitraum['datumvon']);
            $datumbis = strtotime($zeitraum['datumbis']);
            $tmpdatumvon = strtotime($zeitraum['tmpdatumvon']);
            $tmpdatumbis = strtotime($zeitraum['tmpdatumbis']);
            if($datumvon < 0)$datumvon = 0;
            if($datumbis < 0)$datumbis = 0;
            if($tmpdatumvon < 0)$tmpdatumvon = 0;
            if($tmpdatumbis < 0)$tmpdatumbis = 0;
            if($tmpdatumbis)$app->DB->Update("UPDATE shopexport SET tmpdatumbis = NULL WHERE id = '$id' LIMIT 1");
          }
        }
      }
      else{
        $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopimport'");
        $gesamtanzahl = $app->remote->RemoteGetAuftraegeAnzahl($id);
        $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopimport'");
        //if($gesamtanzahl > 1)$gesamtanzahl = 1;
        if($gesamtanzahl > 0)
        {
          $app->erp->LogFile("Hole ".$gesamtanzahl." aus Shop ".$id);
          for($i=0;$i<$gesamtanzahl;$i++)
          {
            $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopimport'");
            $result = $app->remote->RemoteGetAuftrag($id);

            if(is_array($result))
            {
              $auftrag = $result[0]['id'];
              unset($tmpwarenkorb);
              if(isset($result[0]['warenkorbjson']))
              {
                $isjson = true;
                $tmpwarenkorb = json_decode(base64_decode($result[0]['warenkorbjson']), true);
              }else{
                $isjson = false;
                $tmpwarenkorb = unserialize(base64_decode($result[0]['warenkorb']));
              }
              if(!empty($tmpwarenkorb['email']))$tmpwarenkorb['email'] = trim($tmpwarenkorb['email']," \t\n\r\0\x0B\xc2\xa0");
              $onlinebestellnummer = $tmpwarenkorb['onlinebestellnummer'];
              if(!empty($tmpwarenkorb['useorderid']) || (!is_numeric($onlinebestellnummer) && trim((String)$onlinebestellnummer) !== ''))
              {
                $onlinebestellnummer = $tmpwarenkorb['auftrag'];
              }
              $projekt = $app->DB->Select("SELECT projekt WHERE shopexport = '$id' LIMIT 1");
              if(!empty($tmpwarenkorb['projekt']) && $app->DB->Select("SELECT id FROM projekt WHERE id = '".(int)$tmpwarenkorb['projekt']."' LIMIT 1"))$projekt = (int)$tmpwarenkorb['projekt'];
              if(isset($tmpwarenkorb['subshop']) && $tmpwarenkorb['subshop'])
              {
                $subshopprojekt = $app->DB->Select("SELECT projekt FROM shopexport_subshop WHERE shop = '".$id."' AND aktiv = 1 AND subshopkennung = '".$app->DB->real_escape_string($tmpwarenkorb['subshop'])."' LIMIT 1");
                if($subshopprojekt)
                {
                  $projekt = $subshopprojekt;
                }
              }
              if($holealle && $onlinebestellnummer)
              {
                $neue_nummer = (int)$onlinebestellnummer+1;
                $app->DB->Update("UPDATE shopexport SET ab_nummer = '$neue_nummer' WHERE id = '$id'");
              }
              $sessionid = $result[0]['sessionid'];
              if($isjson)
              {
                $warenkorb = $result[0]['warenkorbjson'];
              }else{
                $warenkorb = $result[0]['warenkorb'];
              }
              $logdatei = $result[0]['logdatei'];
              if(empty($logdatei))$logdatei = date('Y-m-d H:i:s');

              $standardcheck = true;
              $modulename = $app->DB->Select(
                sprintf(
                  "SELECT modulename FROM shopexport WHERE id = %d AND modulename <> '' AND (shoptyp = 'intern')",
                  $id
                )
              );
              $shopIds = [$id];
              $otherModules= empty($modulename)?null:
                $app->DB->SelectFirstCols(
                  sprintf(
                    "SELECT id 
                  FROM shopexport 
                  WHERE modulename = '%s' AND id <> %d",
                    $app->DB->real_escape_string($modulename), $id
                  )
                );
              if(!empty($otherModules)) {
                if($app->erp->ModulVorhanden($modulename)) {
                  $obj = $app->erp->LoadModul($modulename);
                  if($obj){
                    $konfiguration = $obj->EinstellungenStruktur();
                    if($konfiguration && isset($konfiguration['globalerauftragsnummernkreis']) && $konfiguration['globalerauftragsnummernkreis']) {
                      $shopIds = array_merge($shopIds, $otherModules);
                      $standardcheck = false;
                      /*$checkdoppeltimported = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and (shopid = '$id' OR ".implode(" OR ", $shopinternids).") and warenkorb = '".$app->DB->real_escape_string($warenkorb)."' AND trash = 0
                                    AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                    LIMIT 1");*/
                      /*$checkdoppeltimported = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and (shopid = '$id' OR ".implode(" OR ", $shopinternids).") AND trash = 0
                                    AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                    LIMIT 1");
                      $checkdoppelt = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and (shopid = '$id' OR ".implode(" OR ", $shopinternids).")  LIMIT 1");*/
                    }
                  }
                }
              }

              $checkdoppelt = $app->DB->Select(
                sprintf(
                  "SELECT id 
                FROM shopimport_auftraege 
                WHERE extid = '%s' and shopid IN (%s) 
                LIMIT 1",
                  $app->DB->real_escape_string($auftrag), implode(',', $shopIds)
                )
              );
              /*if($standardcheck) {
                $checkdoppelt = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and shopid = '$id' LIMIT 1");
              }*/
              /*if($standardcheck)$checkdoppeltimported = $app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$app->DB->real_escape_string($auftrag)."' and shopid = '$id' and warenkorb = '".$app->DB->real_escape_string($warenkorb)."' AND trash = 0
              AND (imported = 0 OR imported = 1)
              LIMIT 1");*/
              //if($standardcheck)
              $checkdoppeltimported = $app->DB->Select(
                sprintf(
                  "SELECT id 
                FROM shopimport_auftraege 
                WHERE extid = '%s' and shopid IN (%s) AND trash = 0
                LIMIT 1",
                  $app->DB->real_escape_string($auftrag), implode(',', $shopIds)
                )
              );
              $insid = null;
              if(!$checkdoppeltimported) {
                $app->DB->Insert("INSERT INTO shopimport_auftraege (id,extid,sessionid,warenkorb,imported,projekt,bearbeiter,logdatei) 
                    VALUES('','".$app->DB->real_escape_string($auftrag)."','".$app->DB->real_escape_string($sessionid)."','".$app->DB->real_escape_string($warenkorb)."','0','$projekt','Cronjob','".$app->DB->real_escape_string($logdatei)."')");
                $insid = $app->DB->GetInsertID();
                if($insid && $isjson) {
                  $app->DB->Update("UPDATE shopimport_auftraege set jsonencoded = 1 where id = '$insid'");
                }
                if($insid) {
                  $app->DB->Update("UPDATE shopimport_auftraege set shopid = '$id' where id = '$insid'");
                }
              }
              if(!$direktimport)
              {

              }else{
                if(!$checkdoppelt && !$checkdoppeltimported)
                {
                  $warenkorb = $app->DB->Select("SELECT warenkorb FROM shopimport_auftraege WHERE id = '$insid' LIMIT 1");
                  if($app->DB->Select("SELECT id FROM  shopimport_auftraege WHERE id = '$insid' AND jsonencoded = 1 LIMIT 1"))
                  {
                    $warenkorb = json_decode(base64_decode($warenkorb), true);
                  }else{
                    $warenkorb = unserialize(base64_decode($warenkorb));
                  }
                  if($shop['utf8codierung']=="1")
                  {
                    $warenkorb = $app->erp->CleanDataBeforImportUTF8($warenkorb, false);
                  } else {
                    $warenkorb = $app->erp->CleanDataBeforImport($warenkorb, false);
                  }
                  foreach($warenkorb as $k => $v) $warenkorb[$k] = $app->erp->fixeUmlaute($v);
                  $warenkorb['email'] = trim($warenkorb['email']," \t\n\r\0\x0B\xc2\xa0");
                  $kundenurvonprojekt = $app->DB->Select("SELECT kundenurvonprojekt FROM shopexport WHERE id = '$id' LIMIT 1");
                  $adresseprojekt = '';
                  if($kundenurvonprojekt)$adresseprojekt = $app->DB->Select("SELECT projekt FROM shopexport WHERE id = '$id' LIMIT 1");
                  if($adresseprojekt)$adresseprojekt = " AND projekt = '".$adresseprojekt."' ";
                  
                  $checkid = $app->DB->Select("SELECT id FROM adresse WHERE name='".$app->erp->ReadyForPDF($warenkorb['name'])."' AND email='".$app->erp->ReadyForPDF($warenkorb['email'])."' AND abteilung='".$app->erp->ReadyForPDF($warenkorb['abteilung'])."'
                      AND strasse='".$app->erp->ReadyForPDF($warenkorb['strasse'])."' AND plz='".$app->erp->ReadyForPDF($warenkorb['plz'])."' AND ort='".$app->erp->ReadyForPDF($warenkorb['ort'])."' AND geloescht!=1 $adresseprojekt LIMIT 1");
                  if($checkid)
                  {
                    $checkkundennummer = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkid' LIMIT 1");
                    if(!$checkkundennummer)
                    {
                      $app->erp->AddRolleZuAdresse($checkid, "Kunde", "von", "Projekt", $projekt);
                      $checkkundennummer = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkid' LIMIT 1");
                    }
                    $checkid = $checkkundennummer;
                  }
                  if(!$checkid)
                  {
                    if($warenkorb['email']!="amazon_import_bounce@nfxmedia.de")
                    {
                      $checkidemailid = $app->DB->Select("SELECT id FROM adresse WHERE email='".$warenkorb['email']."' and email <> '' AND geloescht!=1 $adresseprojekt LIMIT 1");
                      if($checkidemailid)
                      {
                        $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkidemailid' LIMIT 1");
                        if(!$checkidemail)
                        {
                          $app->erp->AddRolleZuAdresse($checkidemailid, "Kunde", "von", "Projekt", $projekt);
                          $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkidemailid' LIMIT 1");
                        }
                      }
                    }
                    if($checkidemail == '')
                    {
                      $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE name LIKE '".$app->erp->ReadyForPDF($warenkorb['name'])."' AND ort LIKE '".$app->erp->ReadyForPDF($warenkorb['ort'])."' $adresseprojekt AND geloescht!=1 LIMIT 1");                  
                    }
                  }
                  //echo "SELECT kundennummer FROM adresse WHERE email='".$warenkorb[email]."'  LIMIT 1";
                  if(!empty($warenkorb['kundennummer']))$validkundennummer = $app->DB->Select("SELECT kundennummer FROM adresse WHERE kundennummer='".$warenkorb['kundennummer']."' AND geloescht!=1 LIMIT 1");
                  $kundennummer = 0;
                  $import_kundennummer = "";
                  if($warenkorb['kundennummer']!="" && $validkundennummer==$warenkorb['kundennummer'])
                  {
                    $kundennummer = 1;
                    $import_kundennummer = $validkundennummer;
                  }elseif ($checkid!="")
                  {
                    $kundennummer = 1;
                    $import_kundennummer = $checkid;                
                  }
                  elseif ($checkidemail!="")
                  {
                    $kundennummer = 1;
                    $import_kundennummer = $checkidemail;
                  }
                  //echo "Kundennummer: ".$import_kundennummer."\r\n";
                  $app->erp->LogFile("Importiere Auftrag ".$auftrag);
                  $unbekanntezahlungsweisen = null;
                  $shopimp->KundeAnlegenUpdate($insid,$auftrag, $warenkorb, $kundennummer, $import_kundennummer, $unbekanntezahlungsweisen);
                  $checkid = '';
                  $checkidemail = '';
                  $validkundennummer = '';
                }
              }

              if($demomodus != "1")	
              {
                $app->remote->RemoteDeleteAuftrag($id,$auftrag);
              }
            }
            if(!$app->DB->Select("SELECT aktiv FROM shopexport WHERE id = '$id' LIMIT 1"))$gesamtanzahl = $i + 1;
          }
          $app->erp->LogFile($gesamtanzahl." aus Shop ".$id." geholt");
        }
      }
    }
  }
}
$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0 WHERE parameter = 'shopimport'");
$alteauftraegeohnebestellnummer = $app->DB->Query("SELECT sa.* FROM shopimport_auftraege sa WHERE isnull(bestellnummer) AND sa.trash='0' LIMIT 100");
if(!empty($alteauftraegeohnebestellnummer)){
  $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopimport'");
  while ($row = $app->DB->Fetch_Assoc($alteauftraegeohnebestellnummer)) {
    if($row['warenkorb'] != ''){
      if(isset($row['jsonencoded']) && $row['jsonencoded']){
        $warenkorb = json_decode(base64_decode($row['warenkorb']), true);
      }else{
        $warenkorb = unserialize(base64_decode($row['warenkorb']));
      }
      $app->DB->Update("UPDATE shopimport_auftraege set bestellnummer = '" . (isset($warenkorb['onlinebestellnummer']) ? $warenkorb['onlinebestellnummer'] : '') . "' where id = '" . $row['id'] . "'");
    }else{
      $app->DB->Update("UPDATE shopimport_auftraege set bestellnummer = '' where id = '" . $row['id'] . "'");
    }
  }
  $app->DB->free($alteauftraegeohnebestellnummer);
}
$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0 WHERE parameter = 'shopimport'");

