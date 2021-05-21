<?php
set_time_limit(36000);


error_reporting(E_ERROR | E_WARNING | E_PARSE);
if(!class_exists('Conf')){
  include_once dirname(__DIR__) . '/conf/main.conf.php';
}
if(!class_exists('DB')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.mysql.php';
}
if(!class_exists('Secure')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.secure.php';
}

if(!class_exists('FormHandler')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.formhandler.php';
}

if(!class_exists('User')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.user.php';
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
  include_once dirname(__DIR__) . '/www/lib/imap.inc.php';
}
if(!class_exists('PDF_EPS')){
  include_once dirname(__DIR__) . '/www/lib/pdf/fpdf_final.php';
}
if(!class_exists('SuperFPDF')){
  include_once dirname(__DIR__) . '/www/lib/dokumente/class.superfpdf.php';
}
if(!class_exists('erpAPI')){
  include_once dirname(__DIR__) . '/www/lib/class.erpapi.php';
}
if(file_exists(dirname(__DIR__).'/www/lib/class.erpapi_custom.php') &&
  !class_exists('erpAPICustom')){
  include_once dirname(__DIR__) . '/www/lib/class.erpapi_custom.php';
}
if(!class_exists('Remote')){
  include_once dirname(__DIR__) . '/www/lib/class.remote.php';
}

if(!class_exists('RemoteCustom') &&
  file_exists(dirname(__DIR__).'/www/lib/class.remote_custom.php'))
  include_once dirname(__DIR__).'/www/lib/class.remote_custom.php';
if(!class_exists('HttpClient')){
  include_once dirname(__DIR__) . '/www/lib/class.httpclient.php';
}

if(!class_exists('AES')){
  $aes = '';
  $phpversion = PHP_VERSION;
  if(strpos($phpversion,'7' === '0') && (int)$phpversion{2} > 0) {
    $aes = '2';
  }
  if($aes === '2' && is_file(dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php')){
    include_once dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php';
  }else{
    include_once dirname(__DIR__) . '/www/lib/class.aes.php';
  }
}

if(!class_exists('PHPMailer')){
  include_once dirname(__DIR__) . '/www/plugins/phpmailer/class.phpmailer.php';
}
if(!class_exists('SMTP')){
  include_once dirname(__DIR__) . '/www/plugins/phpmailer/class.smtp.php';
}
if(!class_exists('image'))
{
  include_once dirname(__DIR__).'/www/lib/class.image.php';
}

$classes = array('briefpapier','lieferschein','auftrag','anfrage','gutschrift','bestellung','rechnung','mahnwesen','angebot');
foreach($classes as $class)
{
  if(file_exists(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'_custom.php'))
  {
    include_once dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'_custom.php';
  }elseif(file_exists(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'.php'))
  {
    include_once dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'.php';
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
  include_once dirname(__DIR__) . '/phpwf/plugins/class.string.php';
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

$app = new app_t2();

$conf = new Config();
$app->Conf = $conf;
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass, $app, $conf->WFdbport);
if(class_exists('erpAPICustom'))
{
  $app->erp = new erpAPICustom($app);
}else{
  $app->erp = new erpAPI($app);
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
  define('FPDF_FONTPATH',dirname(__DIR__).'/www/lib/pdf/font/');
}

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter+1 WHERE parameter = 'pdfarchiv_move' AND aktiv = 1 AND mutex = 1");
if($app->DB->Select("SELECT mutex FROM prozessstarter WHERE parameter = 'pdfarchiv_move' AND aktiv = 1 LIMIT 1"))
{
  return;
}
$first_id = (String)$app->erp->GetKonfiguration('pdfarchiv_move_first_id');
if($first_id === '')
{
  $first_id = (int)$app->DB->Select("SELECT IFNULL(MAX(id),0) FROM pdfarchiv");
  $app->erp->SetKonfigurationValue('pdfarchiv_move_first_id',$first_id);
}
if($first_id < 0)
{
  $app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0, aktiv = 0 WHERE parameter = 'pdfarchiv_move' AND aktiv = 1");
  return;
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_move' AND aktiv = 1");
$pdfarchiv = $app->DB->Query("SELECT id FROM pdfarchiv WHERE id <= $first_id ORDER BY id DESC LIMIT 1000");
if(!empty($pdfarchiv)){
  $Brief = new Briefpapier($app);
  $i = 0;
  while ($row = $app->DB->Fetch_Assoc($pdfarchiv)) {
    $i++;
    $Brief->movePDFArchiv($row['id']);
    $app->erp->SetKonfigurationValue('pdfarchiv_move_first_id',$row['id']-1);
    if($row['id'] == 1)
    {
      $app->DB->free($pdfarchiv);
      $app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0,aktiv = 0 WHERE parameter = 'pdfarchiv_move'");
      return;
    }
    $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'pdfarchiv_move' AND aktiv = 1");
  }
  $app->DB->free($pdfarchiv);
  if($i === 0)
  {
    $app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0,aktiv = 0 WHERE parameter = 'pdfarchiv_move'");
    return;
  }
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0 WHERE parameter = 'pdfarchiv_move' AND aktiv = 1");
