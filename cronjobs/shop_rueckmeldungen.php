<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
if(!class_exists('ApplicationCore') && is_file(dirname(__DIR__).'/xentral_autoloader.php')) {
  include_once dirname(__DIR__).'/xentral_autoloader.php';
}
include_once dirname(__DIR__).'/conf/main.conf.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.mysql.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.secure.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.user.php';
if(file_exists(dirname(__DIR__).'/conf/user_defined.php')) {
  include_once dirname(__DIR__).'/conf/user_defined.php';
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
include_once dirname(__DIR__).'/www/lib/pdf/fpdf_final.php';
include_once dirname(__DIR__).'/www/lib/imap.inc.php';
include_once dirname(__DIR__).'/www/lib/class.erpapi.php';
include_once dirname(__DIR__).'/www/lib/class.remote.php';
include_once dirname(__DIR__).'/www/lib/class.httpclient.php';
$aes = '';
$phpversion = (String)phpversion();
if($phpversion{0} == '7' && (int)$phpversion{2} > 0) {
  $aes = '2';
}
if($aes == 2 && is_file(dirname(__DIR__).'/www/lib/class.aes2.php')) {
  include_once dirname(__DIR__).'/www/lib/class.aes2.php';
}
else{
  include_once dirname(__DIR__) . '/www/lib/class.aes.php';
}
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.phpmailer.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.smtp.php';

$classes = array('briefpapier','lieferschein','auftrag','anfrage','gutschrift','bestellung','rechnung','mahnwesen');
foreach($classes as $class)
{
  if(file_exists(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'_custom.php'))
  {
    include_once dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'_custom.php';
  }
  elseif(file_exists(dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'.php')) {
    include_once dirname(__DIR__).'/www/lib/dokumente/class.'.$class.'.php';
  }
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
include_once dirname(__DIR__).'/phpwf/plugins/class.string.php';
require_once dirname(__DIR__).'/www/plugins/phpmailer/class.smtp.php';
require_once dirname(__DIR__).'/www/plugins/phpmailer/class.phpmailer.php';
if(!class_exists('app_t2'))
{
  class app_t2 extends ApplicationCore {
    var $DB;
    var $erp;
    var $User;
    var $mail;
    var $remote;
    var $Secure;
function GetLandLang($isocode)
{
  $flipped = array_flip($this->GetLaender());
  if(isset($flipped[$isocode]))
    $land = $flipped[$isocode];
  else {
    $land = 'unkown';
  }
  return $land;
}

function GetLaender()
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
  if(empty($app->DB)){
    $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, null, $app->Conf->WFdbport);
  }
  if(empty($app->erp)){
    $erp = new erpAPI($app);
    $app->erp = $erp;
  }
  $app->String         = new WawiString();
  if(empty($app->remote)){
    if(is_file(dirname(__DIR__) . '/www/lib/class.remote_custom.php')){
      require_once dirname(__DIR__) . '/www/lib/class.remote_custom.php';
      $app->remote = new RemoteCustom($app);
    }
    else{
      $app->remote = new Remote($app);
    }
  }
  //$app->remote = new Remote($app);
  $app->Secure = new Secure($app);
  $app->User = new User($app);
  if(!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH',dirname(__DIR__).'/www/lib/pdf/font/');
  }


//ENDE

  const MAXIMUM_NUMBER_OF_TRIES = 10;

  $benutzername = $app->erp->Firmendaten("benutzername");
  $passwort = $app->erp->Firmendaten("passwort");
  $host = $app->erp->Firmendaten("host");
  $port = $app->erp->Firmendaten("port");
  $mailssl = $app->erp->Firmendaten("mailssl");
  $mailanstellesmtp = $app->erp->Firmendaten("mailanstellesmtp");
  $noauth = $app->erp->Firmendaten("noauth");

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


  $shops = $app->DB->SelectFirstCols(
    'SELECT id FROM shopexport WHERE auftragabgleich = 1 AND aktiv = 1 AND demomodus <> 1 AND sendonlywithtracking = 0'
  );
  if(empty($shops)) {
    return;
  }
  $app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND (parameter = 'shop_rueckmeldungen' ) AND aktiv = 1");
  if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND (parameter = 'shop_rueckmeldungen') AND aktiv = 1")) {
    return;
  }
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = 'shop_rueckmeldungen' ) AND aktiv = 1");

  $subwhere = sprintf(' a.shop IN (%s) ', implode(',', $shops));
  $join = '';
  $where = '';
  $app->erp->RunHook('shop_rueckmeldung', 2, $join, $where);
  $sql = "SELECT a.id,apro.zeit, a.shop, l.id as lieferschein, v.id as versandid, l.projekt 
    FROM auftrag AS a 
    LEFT JOIN lieferschein AS l on l.auftragid = a.id 
    LEFT JOIN auftrag_protokoll AS apro ON a.id = apro.auftrag AND apro.grund LIKE 'Auftrag importiert vom Shop'
    LEFT JOIN projekt AS pr ON l.projekt = pr.id
    LEFT JOIN versand AS v ON v.lieferschein = l.id
    $join
    WHERE a.status = 'abgeschlossen' AND $subwhere AND 
    DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 2 WEEK),'%Y-%m-%d') < a.datum AND 
    a.shopextstatus <> 'abgeschlossen' AND a.shop > 0 AND 
    (
      ( v.tracking <> '' AND l.status = 'versendet')  OR 
      isnull(l.id) OR 
      ((pr.kommissionierverfahren = 'lieferschein' OR pr.kommissionierverfahren = 'rechnungsmail') AND  isnull(v.id))
    )
    AND (ISNULL(a.shop_status_update_last_attempt_at) OR a.shop_status_update_last_attempt_at < DATE_SUB(NOW(), INTERVAL 4 HOUR))
    AND a.shop_status_update_attempt <= ".MAXIMUM_NUMBER_OF_TRIES."
    $where
    ORDER BY a.id, l.id DESC, v.id DESC
  ";

  $auftraege = $app->DB->SelectArr($sql);
  if(!empty($auftraege)) {
    $app->DB->Update(
      "UPDATE prozessstarter 
      SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() 
      WHERE (parameter = 'shop_rueckmeldungen' ) AND aktiv = 1"
    );
    $auftragid = null;
    $countRequests = 0;
    foreach($auftraege as $auftrag) {
      if($auftrag['id'] != $auftragid) {
        $countRequests++;
        $auftragid = $auftrag['id'];
        $app->remote->RemoteUpdateAuftrag($auftrag['shop'], $auftrag['id']);
        if($countRequests % 10 === 0) {
          $app->DB->Update(
            "UPDATE prozessstarter 
            SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() 
            WHERE parameter = 'shop_rueckmeldungen' AND aktiv = 1"
          );
        }
      }
    }
    unset($auftraege);
  }
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = 'shop_rueckmeldungen' ) AND aktiv = 1");

  //Zahlungsstatus
  $query = $app->DB->Query(
    "SELECT a.id
    FROM `auftrag` AS `a` 
    LEFT JOIN `rechnung` AS `r1` ON r1.id = a.rechnungid
    LEFT JOIN `rechnung` AS `r2` ON r2.auftragid = a.id
    LEFT JOIN `shopexport_zahlungsstatus` AS `sz` ON a.shopextid = sz.auftrag 
    INNER JOIN `shopexport` AS `s` ON a.shop = s.id 
    WHERE a.vorkasse_ok = 1 AND a.nachnahme_ok = 1 AND a.shop <> 0 AND a.shopextid <> '' AND isnull(sz.id) 
      AND s.aktiv = 1 AND s.demomodus = 0 AND s.auftragabgleich = 1 AND (s.shoptyp = 'intern' OR s.shoptyp = 'custom') 
    AND (r1.zahlungsstatus = 'bezahlt' OR r2.zahlungsstatus = 'bezahlt') AND DATEDIFF(NOW(), a.datum) <= 28
    GROUP BY a.id
    LIMIT 1000"
  );
  if($query) {
    $countRequests = 0;
    while($row = $app->DB->Fetch_Assoc($query)) {
      $app->erp->ImportUpdateZahlungsstatus($row['id']);
      $countRequests++;
      if($countRequests % 10 === 0) {
        $app->DB->Update(
          "UPDATE prozessstarter 
          SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() 
          WHERE parameter = 'shop_rueckmeldungen' AND aktiv = 1"
        );
      }
    }
    $app->DB->free($query);
  }

  $app->DB->Update("UPDATE prozessstarter SET mutex = 0, mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = 'shop_rueckmeldungen' ) AND aktiv = 1");
