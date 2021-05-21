<?php
/*
include_once(dirname(__FILE__)."/../conf/main.conf.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include_once(dirname(__FILE__)."/../www/lib/imap.inc.php");
include_once(dirname(__FILE__)."/../www/lib/class.erpapi.php");
if(file_exists(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php"))include_once(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");
include_once(dirname(__FILE__)."/../www/lib/class.remote.php");
include_once(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include_once(dirname(__FILE__)."/../www/lib/class.aes.php");
include_once(dirname(__DIR__)."/phpwf/plugins/class.secure.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");
include_once(dirname(__DIR__)."/www/pages/shopimport.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.stringcleaner.php");
*/
if(!class_exists('Conf')){
  include_once dirname(__DIR__) . '/conf/main.conf.php';
}
if(!class_exists('DB')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.mysql.php';
}
if(!class_exists('IMAP')){
  include_once dirname(__DIR__) . '/www/lib/imap.inc.php';
}

if(!class_exists('erpAPI'))
{
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
  file_exists(dirname(__DIR__).'/www/lib/class.remote_custom.php')){
  include_once dirname(__DIR__) . '/www/lib/class.remote_custom.php';
}
if(!class_exists('HttpClient')){
  include_once dirname(__DIR__) . '/www/lib/class.httpclient.php';
}
if(!class_exists('AES')){
  $aes = '';
  $phpversion = PHP_VERSION;
  if(strpos($phpversion,'7') === 0 && (int)$phpversion{2} > 0)
  {
    $aes = '2';
  }
  if($aes === '2' && is_file(dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php')){
    include_once dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php';
  }else{
    include_once dirname(__DIR__) . '/www/lib/class.aes.php';
  }
}
if(!class_exists('Shopimport')){
  include_once dirname(__DIR__) . '/www/pages/shopimport.php';
}
if(!class_exists('ShopimportCustom') &&
  file_exists(dirname(__DIR__) . '/www/pages/shopimport_custom.php'))
{
  include_once dirname(__DIR__) . '/www/pages/shopimport_custom.php';
}
if(!class_exists('PHPMailer')){
  include_once dirname(__DIR__) . '/www/plugins/phpmailer/class.phpmailer.php';
}
if(!class_exists('SMTP')){
  include_once dirname(__DIR__) . '/www/plugins/phpmailer/class.smtp.php';
}
if(!class_exists('Secure')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.secure.php';
}
if(!class_exists('StringCleaner') && file_exists(dirname(__DIR__) . '/phpwf/plugins/class.stringcleaner.php'))
{
  include_once dirname(__DIR__) . '/phpwf/plugins/class.stringcleaner.php';
}
if(!class_exists('FormHandler')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.formhandler.php';
}
if(!class_exists('image')){
  include_once dirname(__DIR__) . '/www/lib/class.image.php';
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
  include_once dirname(__DIR__) . '/www/lib/pdf/fpdf_final.php';
}
if(!class_exists('SuperFPDF')){
  include_once dirname(__DIR__) . '/www/lib/dokumente/class.superfpdf.php';
}
$classes = array('briefpapier','auftrag','angebot','rechnung','gutschrift','lieferschein');
foreach($classes as $class)
{
  if(file_exists(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'_custom.php')
    && !class_exists(ucfirst($class).($class != 'briefpapier'?'PDF':'').'Custom'))
  {
    include_once dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'_custom.php';
  }elseif(file_exists(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'.php')
    && !class_exists(ucfirst($class).($class != 'briefpapier'?'PDF':'')))
  {
    include_once dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'.php';
  }
}
if(!class_exists('WawiString')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.string.php';
}

if(!defined('FPDF_FONTPATH'))
{
  define('FPDF_FONTPATH',dirname(__DIR__).'/www/lib/pdf/font/');
}

if(!class_exists('app_t'))
{
  class app_t {
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
    public function __construct($app)
    {
      $this->app = $app;
    }

    public function GetName()
    {
      return 'Cronjob';
    }

    public function GetID()
    {
      return 0;
    }

    public function GetFirma()
    {
      return 1;
    }

    public function GetType()
    {
      return 'admin';
    }

    public function DefaultProjekt()
    {
      return $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='1' LIMIT 1");
    }

    public function GetAdresse()
    {
      return 0;
    }

    public function GetUsername()
    {
      return 'Cronjob';
    }
    public function GetParameter()
    {
      return 0;
    }
  }
}
//ENDE

if(!isset($app)) {
  $app = new app_t();
}
if(empty($app->Conf)){
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB)){
  $app->DB = new DB($conf->WFdbhost, $conf->WFdbname, $conf->WFdbuser, $conf->WFdbpass, $app, $conf->WFdbport);
}
if(class_exists('erpAPICustom'))
{
  $erp = new erpAPICustom($app);
}else{
  $erp = new erpAPI($app);
}
$app->erp = $erp;
$app->String = new WawiString();
if(class_exists('RemoteCustom'))
{
  $remote = new RemoteCustom($app);
}else{
  $remote = new Remote($app);
}

$app->remote = $remote;
$app->Secure = new Secure($app);
$app->User = new User($app);

$app->FormHandler = new FormHandler($app);
/*if(false){
  $auftraege = $app->DB->Query("SELECT a.id as auftrag, s.* FROM auftrag a INNER JOIN shopimport_auftraege s ON a.shop =s.shopid AND a.internet = s.extid WHERE a.datum = '2018-10-29'");
  if($auftraege){
    while ($row = $app->DB->Fetch_Assoc($auftraege)) {
      $auftragcheck = $row['auftrag'];
      if(!empty($row['jsonencoded']))
      {
        $warenkorb = json_decode(base64_decode($row['warenkorb']), true);
      }else{
        $warenkorb = unserialize(base64_decode($row['warenkorb']));
      }
      if(!empty($warenkorb['bestelldatum'])){
        $app->DB->Update("UPDATE auftrag SET datum = '" . $app->DB->real_escape_string($warenkorb['bestelldatum']) . "' 
                    WHERE id = '$auftragcheck' LIMIT 1");
        $rechnungid = $app->DB->Select("SELECT id FROM rechnung WHERE auftragid = '$auftragcheck' AND status != 'storniert' LIMIT 1");
        if($rechnungid){
          $app->DB->Update("UPDATE rechnung SET datum = '" . $app->DB->real_escape_string($warenkorb['bestelldatum']) . "' 
                    WHERE id = '$rechnungid' AND datum > '" . $app->DB->real_escape_string($warenkorb['bestelldatum']) . "' LIMIT 1");
        }
      }
    }
    $app->DB->free($auftraege);
  }
}*/
$app->DB->Update(
  "UPDATE prozessstarter 
  SET mutexcounter = mutexcounter + 1 
  WHERE mutex = 1 AND (parameter = 'shopimport_auftragarchiv') AND aktiv = 1"
);
if(
  !$app->DB->Select(
    "SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'shopimport_auftragarchiv' AND aktiv = 1"
  )
){
  return;
}
usleep(mt_rand(100000,1000000));
if(
!$app->DB->Select(
  "SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'shopimport_auftragarchiv' AND aktiv = 1"
)
){
  return;
}

$app->DB->Update(
  "UPDATE prozessstarter 
        SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 
        WHERE parameter = 'shopimport_auftragarchiv'"
);

$break = false;
$throttledIds = [0];
$cronjobTmpIds = $app->DB->SelectFirstCols(
  "SELECT `id` FROM `prozessstarter` WHERE `aktiv`  = 1 AND `parameter` = 'shopimport_auftragarchiv'"
);
$autoThrottle = false;
if(count($cronjobTmpIds) !== 1) {
  $cronjobTmpId = (int)reset($throttledIds);
  $periode = $app->DB->SelectRow(sprintf(
    "SELECT `periode` FROM `prozessstarter` WHERE `id` = %d AND `art` = 'periodisch'",
    $cronjobTmpId
  ));
  if(!empty($periode)) {
    $autoThrottle = true;
    if($periode['periode'] > 15) {
      $app->DB->Update(sprintf("UPDATE `prozessstarter` SET `periode` = 15 WHERE `id` = %d", $cronjobTmpId));
    }
  }
}
else {
  $cronjobTmpId = null;
}

while(!$break) {
  $break = true;
  $archivauftraege = $app->DB->SelectArr(
    sprintf(
      "SELECT sa.*, 
        UNIX_TIMESTAMP(sa.datumvon) as datumvon_timestamp,
        UNIX_TIMESTAMP(sa.datumbis) as datumbis_timestamp, se.modulename
      FROM `shopexport_archiv` AS `sa`
      INNER JOIN `shopexport` AS `se` ON sa.shop = se.id AND (se.geloescht IS NULL OR se.geloescht = 0) AND se.aktiv = 1
      WHERE sa.status = 'aktiv' AND  sa.id NOT IN (%s) ",
      implode(',', $throttledIds)
    )
  );
  if(empty($archivauftraege)) {
    break;
  }
  $carchivauftraege = !empty($archivauftraege) ? count($archivauftraege) : 0;
  if($carchivauftraege > 0) {
    if($autoThrottle && $carchivauftraege === 1 && $archivauftraege[0]['modulename'] === 'shopimporter_amazon') {
      $app->DB->Update(
        sprintf(
          "UPDATE `prozessstarter` 
          SET `periode` = 30 
          WHERE `id` = %d AND `art` = 'periodisch' AND `aktiv` = 1 AND `parameter` = 'shopimport_auftragarchiv'",
          $cronjobTmpId
        )
      );
    }
    for ($i = 0; $i < $carchivauftraege; $i++) {
      if($autoThrottle) {
        $throttledIds[] = $archivauftraege[$i]['id'];
      }
      $donotimport = $archivauftraege[$i]['donotimport'];
      $app->DB->Update(
        "UPDATE `prozessstarter` 
        SET `letzteausfuerhung`=NOW(), `mutex` = 1,`mutexcounter`=0 
        WHERE `parameter` = 'shopimport_auftragarchiv'"
      );
      $shopdaten = $app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `shopexport` WHERE `id` = %d',
          $archivauftraege[$i]['shop']
        )
      );
      $shopid = $shopdaten['id'];
      try {
        $pageContents = $app->remote->RemoteConnection($shopid);
      }
      catch (Exception $e) {
        $app->erp->LogFile(['RemoteConnection Error'=>$e->getMessage()]);
        $pageContents = 'failed';
      }
      $app->erp->LogFile(
        $app->DB->real_escape_string(
          'shopimport_auftragarchiv Shop: '.$shopid.' Auth: '.
          print_r($pageContents,true).' arr: '.print_r($archivauftraege[$i],true)
        )
      );
      if($pageContents === 'success') {
        //Kommunikation steht
        $anzahlauftraege = (int)$archivauftraege[$i]['anzahl'];
        if($anzahlauftraege == 0){

          if($archivauftraege[$i]['nummervon']) {
            $data = [
              'ab_nummer' => $archivauftraege[$i]['nummervon'],
              'bis_nummer' => $archivauftraege[$i]['nummerbis'],
            ];
          }
          if($archivauftraege[$i]['type'] !== 'zeitraum') {
            try {
              $anzahlauftraege = (int)$app->remote->RemoteGetAuftraegeAnzahl($shopid, $data);
              $app->DB->Update(
                "UPDATE `prozessstarter` 
                SET `letzteausfuerhung`=NOW(), `mutex` = 1,`mutexcounter`=0 
                WHERE `parameter` = 'shopimport_auftragarchiv'"
              );
            }
            catch (Exception $e) {
              $app->erp->LogFile(['RemoteGetAuftraegeAnzahl Error'=>$e->getMessage()]);
              $anzahlauftraege = 0;
            }
            $app->erp->LogFile('shopimport_auftragarchiv Shop: '.$shopid.' gefundene Auftraege: '.$anzahlauftraege);
            if($anzahlauftraege > 0) {
              $app->DB->Update(
                sprintf(
                  "UPDATE `shopexport_archiv` SET `anzahl` = %d WHERE `id` = %d",
                  $anzahlauftraege,
                  $archivauftraege[$i]['id']
                )
              );
            }
            else {
              $app->DB->Update(
                sprintf(
                  "UPDATE `shopexport_archiv` SET `status` = 'abgeschlossen' WHERE `id` = %d",
                  $archivauftraege[$i]['id']
                )
              );
              continue;
            }
          }
          else{
            $anzahlauftraege = 1;
          }
        }
        if($archivauftraege[$i]['type'] === 'zeitraum') {
          //Datumsbereich hinzufügen
          $data = array();
          $data['datumvon'] = $archivauftraege[$i]['datumvon'];
          $data['datumbis'] = $archivauftraege[$i]['datumbis'];
          //if(strtotime($zeitraum[0]['tmpdatumvon']) > 0)$data['datumvon'] = $zeitraum[0]['tmpdatumbis'];
          if(strtotime($data['datumvon']) <= 0) {
            $data['datumvon'] = '2000-01-01 00:00:00';
          }
          if(strtotime($data['datumbis']) <= 0) {
            $data['datumbis'] = date('Y-m-d H:i:s');
          }
          if(strtotime($data['datumvon']) >= strtotime($data['datumbis'])) {
            $app->DB->Update("UPDATE `shopexport_archiv` SET `status` = 'abgeschlossen' WHERE `id` = " . $archivauftraege[$i]['id']);
            continue;
          }
          $data['anzgleichzeitig'] = 50;
          $data['holeallestati'] = 1;
          $data['archive'] = 1;
          $app->erp->LogFile($app->DB->real_escape_string('shopimport_auftragarchiv Shop: '.$shopid.' Zeitraum: '.print_r($data,true)));
        }
        $shopimp = new Shopimport($app, true);
        $erfolgreich = 0;
        $letzteabgeholtenummer = $archivauftraege[$i]['letzteabgeholtenummer'];
        for ($nummerinkrement = 0; $nummerinkrement < $anzahlauftraege; $nummerinkrement++) {
          $app->DB->Update(
            "UPDATE `prozessstarter` 
            SET `letzteausfuerhung` = NOW(), `mutex` = 1, `mutexcounter`=0 
            WHERE `parameter` = 'shopimport_auftragarchiv'"
          );
          if($archivauftraege[$i]['type'] !== 'zeitraum'){
            if($letzteabgeholtenummer != 0){
              $nummervon = $letzteabgeholtenummer;
            }else{
              $nummervon = (int)$archivauftraege[$i]['nummervon'] + $nummerinkrement-1;
            }
            $data = array('ab_nummer' => $nummervon+1, 'bis_nummer' => $archivauftraege[$i]['nummerbis']);
          }
          $data['stornierte_abholen'] = !isset($archivauftraege[$i]['stornierte_abholen'])?null:$archivauftraege[$i]['stornierte_abholen'];
          try {
            $result = $app->remote->RemoteGetAuftrag($shopid, $data);
            $app->DB->Update(
              "UPDATE `prozessstarter` 
              SET `letzteausfuerhung` = NOW(), `mutex` = 1, `mutexcounter` = 0 
              WHERE `parameter` = 'shopimport_auftragarchiv'"
            );
          }
          catch(Exception $e) {
            $app->erp->LogFile(['RemoteGetAuftrag Error'=>$e->getMessage()]);
            $result = '';
          }
          if(!is_array($result)) {
            if(is_string($result) && stripos($result, 'Throttled') === 0) {
              $throttledIds[] = $archivauftraege[$i]['id'];
              $app->erp->ProzessstarterStatus('Id '.$shopid.' Throttled', $cronjobTmpId);
              continue;
            }
            $app->erp->ProzessstarterStatus('Id '.$shopid.' keine (weiteren) Auftraege gefunden', $cronjobTmpId);
            $app->erp->LogFile('shopimport_auftragarchiv Shop: '.$shopid.' keine (weiteren) Auftraege gefunden');
            $app->DB->Update("UPDATE `shopexport_archiv` SET `status` = 'abgeschlossen' WHERE `id` = " . $archivauftraege[$i]['id']);
            break;
          }
          if(!isset($result[0]) && $archivauftraege[$i]['type'] === 'zeitraum'){
            if(is_array($result) && !empty($result['zeitstempel'])){
              $app->erp->ProzessstarterStatus('Id '.$shopid.' '. $result['zeitstempel'].' empty arr', $cronjobTmpId);
              if($app->DB->Select(
                "SELECT `id` 
                FROM `shopexport_archiv` 
                WHERE `id` = '" . $archivauftraege[$i]['id'] . "' 
                  AND `datumvon` < '" . date('Y-m-d H:i:s', strtotime($result['zeitstempel'])) . "' 
                  LIMIT 1")){
                $app->DB->Update(
                  "UPDATE `shopexport_archiv` 
                  SET `datumvon` = '" . date('Y-m-d H:i:s', strtotime($result['zeitstempel'])) . "' 
                  WHERE `id` = '" . $archivauftraege[$i]['id'] . "' 
                  AND `datumvon` < '" . date('Y-m-d H:i:s', strtotime($result['zeitstempel'])) . "' 
                  LIMIT 1"
                );
              }
              else{
                $app->DB->Update(
                  "UPDATE `shopexport_archiv` 
                  SET `datumvon` = DATE_ADD(`datumvon`, INTERVAL 1 SECOND) 
                  WHERE `id` = '" . $archivauftraege[$i]['id'] . "' 
                  LIMIT 1"
                );
              }
            }
            else{
              $app->erp->ProzessstarterStatus('Id '.$shopid.' no array', $cronjobTmpId);
              $app->DB->Update(
                "UPDATE `shopexport_archiv` 
                SET `datumvon` = DATE_ADD(`datumvon`, INTERVAL 1 SECOND) 
                WHERE `id` = '" . $archivauftraege[$i]['id'] . "' 
                LIMIT 1"
              );
            }
          }
          $maxtime = false;
          $mintime = false;
          $cw = count($result);
          if($archivauftraege[$i]['type'] === 'zeitraum') {
            $app->erp->LogFile('shopimport_auftragarchiv Shop: '.$shopid.' gefundene Auftraege: '.$cw);
            $app->erp->ProzessstarterStatus('Id '.$shopid.' anz '.$cw, $cronjobTmpId);
          }
          for ($ii = 0; $ii < $cw; $ii++) {
            $app->DB->Update(
              "UPDATE `prozessstarter` SET `letzteausfuerhung` = NOW(), `mutex` = 1,`mutexcounter`=0 
              WHERE `parameter` = 'shopimport_auftragarchiv'"
            );
            if(isset($result[$ii])){

              $sessionid = $result[$ii]['sessionid'];
              if(!empty($result[$ii]['warenkorbjson'])) {
                $isjson = true;
                $warenkorb = json_decode(base64_decode($result[$ii]['warenkorbjson']), true);
              }
              else {
                $isjson = false;
                $warenkorb = unserialize(base64_decode($result[$ii]['warenkorb']));
              }

              if(!empty($warenkorb['zeitstempel'])) {
                if(!empty($warenkorb['zeitstempel_raw']))
                {
                  $time = strtotime($warenkorb['zeitstempel_raw']);
                }else{
                  $time = strtotime($warenkorb['zeitstempel']);
                }
                if($time < 0) {
                  $time = 0;
                }
                if($maxtime === false) {
                  $maxtime = $time;
                }
                if($mintime === false) {
                  $mintime = $time;
                }
                if($time > $maxtime) {
                  $maxtime = $time;
                }
                if($time < $mintime) {
                  $mintime = $time;
                }
                if($time > strtotime($data['datumbis'])) {
                  continue;
                }
              }
              $kundenurvonprojekt = $app->DB->Select(
                "SELECT `kundenurvonprojekt` FROM `shopexport` WHERE `id` = '$shopid' LIMIT 1"
              );
              $adresseprojekt = '';
              if($kundenurvonprojekt) {
                $adresseprojekt = $app->DB->Select("SELECT `projekt` FROM `shopexport` WHERE `id` = '$shopid' LIMIT 1");

                if(isset($warenkorb['subshop']) && $warenkorb['subshop']) {
                  $subshopprojekt = $app->DB->Select(
                    "SELECT `projekt` 
                    FROM `shopexport_subshop` 
                    WHERE `shop` = '".$shopid."' AND `aktiv` = 1 AND `subshopkennung` = '".
                    $app->DB->real_escape_string($warenkorb['subshop'])."'
                    LIMIT 1"
                  );
                  if($subshopprojekt) {
                    $adresseprojekt = $subshopprojekt;
                  }
                }
              }

              if($adresseprojekt) {
                $adresseprojekt = " AND projekt = '".$adresseprojekt."' ";
              }

              $onlinebestellnummer = $warenkorb['onlinebestellnummer'];
              $letzteabgeholtenummer = $onlinebestellnummer;
              if(isset($warenkorb['onlinebestellnummerueberschreiben'])){
                $letzteabgeholtenummer = $warenkorb['onlinebestellnummerueberschreiben'];
              }
              $auftrag = $warenkorb['auftrag'];
              $auftragcheck = $app->DB->Select(
                sprintf(
                  "SELECT `id` 
                  FROM `auftrag` 
                  WHERE `internet` = '%s' AND `internet`<>'' AND `status` <> 'storniert' 
                  LIMIT 1",
                  $onlinebestellnummer
                )
              );
              //Wenn Auftrag bereits vorhanden, dann überspringen
              if(!$auftragcheck) {
                $app->DB->Insert(
                  "INSERT INTO `shopimport_auftraege` 
                    (`extid`,`sessionid`,`warenkorb`,`imported`,`projekt`,`bearbeiter`,`logdatei`) 
                  VALUES('" . $app->DB->real_escape_string($auftrag) . "','" .
                  $app->DB->real_escape_string($sessionid) . "','" .
                  $app->DB->real_escape_string($isjson?$result[$ii]['warenkorbjson']:$result[$ii]['warenkorb']) .
                  "','0','$projekt','Cronjob',NOW())");
                $insid = $app->DB->GetInsertID();
                if($insid) {
                  $app->DB->Update("UPDATE `shopimport_auftraege` set `shopid` = '$shopid' WHERE `id` = '$insid'");
                  if($isjson) {
                    $app->DB->Update("UPDATE `shopimport_auftraege` set `jsonencoded` = 1 WHERE `id` = '$insid'");
                  }
                }
                if(empty($warenkorb['name']) && !empty($warenkorb['ansprechpartner'])){
                  $warenkorb['name']=$warenkorb['ansprechpartner'];
                  $warenkorb['ansprechpartner'] = '';
                }
                if(empty($warenkorb['name']) && !empty($warenkorb['lieferadresse_name']))
                {
                  $warenkorb['name'] = $warenkorb['lieferadresse_name'];
                }
                if(empty($warenkorb['lieferadresse_name']) && !empty($warenkorb['lieferadresse_ansprechpartner'])){
                  $warenkorb['lieferadresse_name']=$warenkorb['lieferadresse_ansprechpartner'];
                  $warenkorb['lieferadresse_ansprechpartner'] = '';
                }
                /*
                if($shopdaten['utf8codierung']=="1")
                {
                  $warenkorb = $app->erp->CleanDataBeforImportUTF8($warenkorb, false);
                } else {
                  $warenkorb = $app->erp->CleanDataBeforImport($warenkorb, false);
                }*/
                foreach($warenkorb as $k => $v) {
                  $warenkorb[$k] = $app->erp->fixeUmlaute($v);
                }
                $projekt = $app->DB->Select(
                  "SELECT `projekt` FROM `shopexport` WHERE `id` = '$shopid' LIMIT 1"
                );
                $subshopprojekt = $app->DB->Select(
                  "SELECT `projekt` 
                  FROM `shopexport_subshop` 
                  WHERE `shop` = '" . $shopid . "' AND `aktiv` = 1 AND `subshopkennung` = '" . $app->DB->real_escape_string($warenkorb['subshop']) . "' 
                  LIMIT 1"
                );
                $projekt = $subshopprojekt;
                $checkkundennummer = '';
                $checkidemail = '';
                $checkid = $app->DB->Select(
                  sprintf(
                  "SELECT `id` 
                    FROM `adresse` 
                    WHERE IFNULL(`name`,'')='%s' AND IFNULL(`email`,'')='%s' AND IFNULL(abteilung,'')='%s' AND IFNULL(strasse,'')='%s' AND IFNULL(plz,'')='%s' AND IFNULL(ort,'')='%s' AND geloescht!=1 
                    LIMIT 1",
                    $app->erp->ReadyForPDF($warenkorb['name']),
                    $app->erp->ReadyForPDF($warenkorb['email']),
                    $app->erp->ReadyForPDF($warenkorb['abteilung']),
                    $app->erp->ReadyForPDF($warenkorb['strasse']),
                    $app->erp->ReadyForPDF($warenkorb['plz']),
                    $app->erp->ReadyForPDF($warenkorb['ort'])
                  )
                );

                //CleanString

                if(!$checkid){
                  $checkid = $app->DB->Select(
                    sprintf(
                      "SELECT id 
                    FROM adresse 
                    WHERE IFNULL(name,'')='%s' AND IFNULL(email,'')='%s' AND IFNULL(abteilung,'')='%s' AND IFNULL(strasse,'')='%s' AND IFNULL(plz,'')='%s' AND IFNULL(ort,'')='%s' AND geloescht!=1 
                    LIMIT 1",
                      $app->DB->real_escape_string($warenkorb['name']),
                      $app->DB->real_escape_string($warenkorb['email']),
                      $app->DB->real_escape_string($warenkorb['abteilung']),
                      $app->DB->real_escape_string($warenkorb['strasse']),
                      $app->DB->real_escape_string($warenkorb['plz']),
                      $app->DB->real_escape_string($warenkorb['ort'])
                    )
                  );
                }

                if(!$checkid && $app->erp->CleanStringUTF8($warenkorb['name'], false) != ''){
                  $checkid = $app->DB->Select(
                    sprintf(
                      "SELECT id 
                    FROM adresse 
                    WHERE IFNULL(name,'')='%s' AND IFNULL(name,'') <> '' AND IFNULL(email,'')='%s' AND IFNULL(abteilung,'')='%s' AND IFNULL(strasse,'')='%s' AND IFNULL(plz,'')='%s' AND IFNULL(ort,'')='%s' AND geloescht!=1 
                    LIMIT 1",
                      $app->erp->CleanStringUTF8($warenkorb['name'], false),
                      $app->erp->CleanStringUTF8($warenkorb['email'], false),
                      $app->erp->CleanStringUTF8($warenkorb['abteilung'], false),
                      $app->erp->CleanStringUTF8($warenkorb['strasse'], false),
                      $app->erp->CleanStringUTF8($warenkorb['plz'], false),
                      $app->erp->CleanStringUTF8($warenkorb['ort'], false)
                    )
                  );
                }

                if(!$checkid && $app->erp->CleanString($warenkorb['name'], false) != ''){
                  $checkid = $app->DB->Select(
                    sprintf(
                      "SELECT id 
                    FROM adresse 
                    WHERE IFNULL(name,'')='%s' AND IFNULL(name,'') <> '' AND IFNULL(email,'')='%s' AND IFNULL(abteilung,'')='%s' AND IFNULL(strasse,'')='%s' AND IFNULL(plz,'')='%s' AND IFNULL(ort,'')='%s' AND geloescht!=1 
                    LIMIT 1",
                      $app->erp->CleanString($warenkorb['name'], false),
                      $app->erp->CleanString($warenkorb['email'], false),
                      $app->erp->CleanString($warenkorb['abteilung'], false),
                      $app->erp->CleanString($warenkorb['strasse'], false),
                      $app->erp->CleanString($warenkorb['plz'], false),
                      $app->erp->CleanString($warenkorb['ort'], false)
                    )
                  );
                }
                if(!$donotimport) {
                  if($checkid){
                    $checkkundennummer = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkid' LIMIT 1");
                    if(!$checkkundennummer){
                      $app->erp->AddRolleZuAdresse($checkid, 'Kunde', 'von', 'Projekt', $projekt);
                      $checkkundennummer = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkid' LIMIT 1");
                    }
                    $checkid = $checkkundennummer;
                  }

                  if(!$checkid){
                    if($warenkorb['email'] !== 'amazon_import_bounce@nfxmedia.de'){
                      if($warenkorb['email'] != ''){

                        $checkidemailid = $app->DB->Select(
                          sprintf(
                            "SELECT id 
                            FROM adresse 
                            WHERE email='%s' and email <> '' AND geloescht!=1 $adresseprojekt 
                            LIMIT 1",
                            $app->DB->real_escape_string($warenkorb['email'])
                          )
                        );

                        if(!$checkidemailid){
                          $checkidemailid = $app->DB->Select(
                            sprintf(
                              "SELECT id 
                              FROM adresse 
                              WHERE email='%s' and email <> '' AND geloescht!=1 $adresseprojekt 
                              LIMIT 1",
                              $app->erp->ReadyForPDF($warenkorb['email'])
                            )
                          );
                        }

                        if($checkidemailid){
                          $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkidemailid' LIMIT 1");
                          if(!$checkidemail){
                            $app->erp->AddRolleZuAdresse($checkidemailid, 'Kunde', 'von', 'Projekt', $projekt);
                            $checkidemail = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$checkidemailid' LIMIT 1");
                          }
                        }
                      }else{

                        $checkidemailid = $app->DB->Select(
                          sprintf(
                            "SELECT id 
                            FROM adresse 
                            WHERE IFNULL(name,'')='%s' AND IFNULL(strasse,'')='%s' AND IFNULL(plz,'')='%s' AND IFNULL(ort,'')='%s' AND geloescht!=1 
                            LIMIT 1",
                            $app->erp->ReadyForPDF($warenkorb['name']),
                            $app->erp->ReadyForPDF($warenkorb['strasse']),
                            $app->erp->ReadyForPDF($warenkorb['plz']),
                            $app->erp->ReadyForPDF($warenkorb['ort'])
                          )
                        );
                        if(!$checkidemailid){
                          $checkidemail = $app->DB->Select(
                            sprintf(
                              "SELECT id 
                              FROM adresse 
                              WHERE IFNULL(name,'')='%s' AND IFNULL(strasse,'')='%s' AND IFNULL(plz,'')='%s' AND IFNULL(ort,'')='%s' AND geloescht!=1 
                              LIMIT 1",
                              $app->DB->real_escape_string($warenkorb['name']),
                              $app->DB->real_escape_string($warenkorb['strasse']),
                              $app->DB->real_escape_string($warenkorb['plz']),
                              $app->DB->real_escape_string($warenkorb['ort'])
                            )
                          );
                        }

                        if(!$checkidemailid){
                          $checkidemailid = $app->DB->Select("SELECT id FROM adresse WHERE name='" . $app->erp->ReadyForPDF($warenkorb['name']) . "'   AND strasse='" . $app->erp->ReadyForPDF($warenkorb['strasse']) . "' AND plz='" . $app->erp->ReadyForPDF($warenkorb['plz']) . "' AND ort='" . $app->erp->ReadyForPDF($warenkorb['ort']) . "' AND geloescht!=1 $adresseprojekt LIMIT 1");
                        }
                        if(!$checkidemailid){
                          $checkidemailid = $app->DB->Select("SELECT id FROM adresse WHERE name='" . $app->DB->real_escape_string($warenkorb['name']) . "'   AND strasse='" . $app->DB->real_escape_string($warenkorb['strasse']) . "' AND plz='" . $app->DB->real_escape_string($warenkorb['plz']) . "' AND ort='" . $app->DB->real_escape_string($warenkorb['ort']) . "' AND geloescht!=1 $adresseprojekt LIMIT 1");
                        }

                        if($checkidemailid){
                          $checkidemail = $app->DB->Select("SELECT `kundennummer` FROM `adresse` WHERE `id` = '$checkidemailid' LIMIT 1");
                          if(!$checkidemail){
                            $app->erp->AddRolleZuAdresse($checkidemailid, 'Kunde', 'von', 'Projekt', $projekt);
                            $checkidemail = $app->DB->Select("SELECT `kundennummer` FROM `adresse` WHERE `id` = '$checkidemailid' LIMIT 1");
                          }
                        }
                      }
                    }
                  }
                  $validkundennummer = '';
                  if(!empty($warenkorb['kundennummer'])){
                    $validkundennummer = $app->DB->Select("SELECT `kundennummer` FROM `adresse` WHERE `kundennummer`='" . trim($warenkorb['kundennummer']) . "' AND geloescht!=1 LIMIT 1");
                  }
                  $kundennummer = 0;
                  $import_kundennummer = '';
                  if(!empty($warenkorb['kundennummer']) && $validkundennummer == $warenkorb['kundennummer']){
                    $kundennummer = 1;
                    $import_kundennummer = $validkundennummer;
                  }
                  elseif($checkid != ''){
                    $kundennummer = 1;
                    $import_kundennummer = $checkid;
                  }
                  elseif($checkidemail != ''){
                    $kundennummer = 1;
                    $import_kundennummer = $checkidemail;
                  }

                  if($shopdaten['utf8codierung'] == '1') {
                    $warenkorb = $app->erp->CleanDataBeforImportUTF8($warenkorb, false);
                  }
                  else{
                    $warenkorb = $app->erp->CleanDataBeforImport($warenkorb, false);
                  }
                  $unbekanntezahlungsweisen = null;
                  $shopimp->KundeAnlegenUpdate($insid, $auftrag, $warenkorb, $kundennummer, $import_kundennummer, $unbekanntezahlungsweisen);
                  $checkid = '';
                  $checkidemail = '';
                }
                $app->DB->Update(
                  "UPDATE `shopexport_archiv` 
                  SET `erfolgreich` = `erfolgreich` + 1, `letzteabgeholtenummer` = '$letzteabgeholtenummer'
                  WHERE `id` = " . $archivauftraege[$i]['id']
                );
                $auftragstmp = $app->DB->SelectArr(
                  "SELECT `id` FROM `auftrag` WHERE `internet` = '$onlinebestellnummer' AND `internet` <> ''"
                );
                if(!empty($auftragstmp) && count($auftragstmp) == 1) {
                  $auftragid = $auftragstmp[0]['id'];
                  if($archivauftraege[$i]['abschliessen'] && !$donotimport) {
                    $app->erp->BelegFreigabe('auftrag', $auftragid);
                    if(!empty($warenkorb['bestelldatum']) && strtotime($warenkorb['bestelldatum']) > 1 &&
                      strtotime($warenkorb['bestelldatum']) < strtotime(date('Y-m-d'))){
                      $app->DB->Update(
                        sprintf(
                          "UPDATE `auftrag` 
                          SET `datum` = '%s' 
                          WHERE `id` = %d 
                          LIMIT 1",
                          $app->DB->real_escape_string($warenkorb['bestelldatum']), $auftragid
                        )
                      );
                    }
                    $app->DB->Update(
                      "UPDATE `auftrag` SET `status`='abgeschlossen', `schreibschutz`='1' WHERE `id`='$auftragid' LIMIT 1"
                    );
                  }
                  $rechnungid = '';
                  if($archivauftraege[$i]['rechnung_erzeugen'] && !$donotimport){
                    $rechnungid = $app->DB->Select("SELECT `id` FROM `rechnung` WHERE `auftragid` = '$auftragid' AND `status` != 'storniert' LIMIT 1");
                    if(!$rechnungid){
                      $rechnungid = $app->erp->WeiterfuehrenAuftragZuRechnung($auftragid);
                      if(!empty($warenkorb['bestelldatum']) && strtotime($warenkorb['bestelldatum']) > 1 &&
                        strtotime($warenkorb['bestelldatum']) < strtotime(date('Y-m-d'))){
                        $app->DB->Update("UPDATE auftrag SET datum = '" . $app->DB->real_escape_string($warenkorb['bestelldatum']) . "' 
                        WHERE id = '$auftragid' LIMIT 1");
                      }
                      $datum = $app->DB->Select("SELECT `datum` FROM `auftrag` WHERE `id` = '$auftragid' LIMIT 1");
                      if(!empty($datum))
                      {
                        $app->DB->Update("UPDATE `rechnung` SET `datum` = '$datum' WHERE `id` = '$rechnungid' LIMIT 1");
                      }
                      $app->erp->BelegFreigabe('rechnung', $rechnungid);
                      $app->DB->Update("UPDATE `rechnung` SET `status`='versendet', `schreibschutz`='1' WHERE `id`='$rechnungid' LIMIT 1");
                      if(!empty($datum))
                      {
                        $app->DB->Update("UPDATE `rechnung` SET `datum` = '$datum' WHERE `id` = '$rechnungid' LIMIT 1");
                      }
                    }
                  }
                  if($archivauftraege[$i]['rechnung_bezahlt'] && $rechnungid != '') {
                    $app->DB->Update("UPDATE `rechnung` SET `ist`=`soll`, `zahlungsstatus`='bezahlt', `mahnwesenfestsetzen`=1 WHERE `id`='$rechnungid' ");
                  }
                }
              }
              elseif(!empty($warenkorb['bestelldatum']) && strtotime($warenkorb['bestelldatum']) > 1 &&
                strtotime($warenkorb['bestelldatum']) < strtotime(date('Y-m-d'))) {
                $auftragid = $auftragcheck;
                if(!$donotimport) {
                  $app->DB->Update("UPDATE `auftrag` SET `datum` = '" . $app->DB->real_escape_string($warenkorb['bestelldatum']) . "' 
                    WHERE id = '$auftragcheck' LIMIT 1");
                  $rechnungid = $app->DB->Select("SELECT `id` FROM `rechnung` WHERE `auftragid` = '$auftragid' AND status != 'storniert' LIMIT 1");
                  if($rechnungid){
                    $app->DB->Update("UPDATE `rechnung` SET `datum` = '" . $app->DB->real_escape_string($warenkorb['bestelldatum']) . "' 
                    WHERE `id` = '$rechnungid' AND `datum` > '" . $app->DB->real_escape_string($warenkorb['bestelldatum']) . "' LIMIT 1");
                  }
                }
              }
            }
          }

          if($archivauftraege[$i]['type'] === 'zeitraum') {
            if(!$maxtime) {
              $app->erp->ProzessstarterStatus('Id '.$shopid.' no maxTime', $cronjobTmpId);
              $maxtime = strtotime(date('Y-m-d H:i:s'));
            }
            else {
              $app->erp->ProzessstarterStatus('Id '.$shopid.' '.$maxtime.' maxTime', $cronjobTmpId);
            }
            if(!$mintime) {
              $app->erp->ProzessstarterStatus('Id '.$shopid.' no minTime', $cronjobTmpId);
            }
            else {
              $app->erp->ProzessstarterStatus('Id '.$shopid.' '.$mintime.' minTime', $cronjobTmpId);
            }
            $datumvon = strtotime($archivauftraege[$i]['datumvon']);
            $datumbis = strtotime($archivauftraege[$i]['datumbis']);
            if($datumvon < 0) {
              $datumvon = 0;
            }
            if($datumbis < 0) {
              $datumbis = 0;
            }

            $app->DB->Update(
              sprintf(
                'UPDATE `shopexport_archiv` 
                SET `datumvon` = FROM_UNIXTIME(%d) 
                WHERE `id` = %d LIMIT 1',
                $maxtime ,$archivauftraege[$i]['id']
              )
            );
            if($cw < 1 || strtotime(date('Y-m-d H:i:s', $maxtime)) <= $datumvon){
              $app->DB->Update(
                sprintf(
                  "UPDATE `shopexport_archiv` SET `status` = 'abgeschlossen' WHERE `id` = %d",
                  $archivauftraege[$i]['id']
                )
              );
            }
            else{
              $break = false;
            }
          }
        }
      }
      else{
        $app->erp->LogFile(
          $app->DB->real_escape_string(
            'shopimport_auftragarchiv Shop: '.$shopid.' Auth: '.print_r($pageContents,true)
          )
        );
      }
      if($archivauftraege[$i]['type'] !== 'zeitraum') {
        $app->DB->Update(
          sprintf(
            "UPDATE `shopexport_archiv` SET `status` ='abgeschlossen' WHERE `id` = %d",
            $archivauftraege[$i]['id']
          )
        );
      }
    }
  }
}
$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `letzteausfuerhung`=NOW(), `mutex` = 0,`mutexcounter`=0 
  WHERE `parameter` = 'shopimport_auftragarchiv'"
);

//Da der Cronjob wohl nie wieder gebraucht wird abschalten wenn es keine weiteren Shops mehr gibt
$weitereauftraegevorhanden = $app->DB->Select("SELECT `id` from `shopexport_archiv` WHERE `status` = 'aktiv' LIMIT 1");
if(!$weitereauftraegevorhanden) {
  $app->DB->Update("UPDATE `prozessstarter` SET `aktiv` = 0 WHERE `parameter` = 'shopimport_auftragarchiv'");
}
