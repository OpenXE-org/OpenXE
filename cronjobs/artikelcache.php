<?php

error_reporting(E_ERROR | E_PARSE);

include_once(dirname(__DIR__).'/conf/main.conf.php');
if(!class_exists('DB')){
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.mysql.php');
}
if(!class_exists('Secure')){
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.secure.php');
}
if(!class_exists('User')){
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.user.php');
}
if(!class_exists('FormHandler')){
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.formhandler.php');
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
  if($phpversion{0} == '7' && (int)$phpversion{2} > 0) $aes = '2';
  if($aes == 2 && is_file(dirname(__DIR__) . '/www/lib/class.aes'.$aes.'.php')){
    include_once(dirname(__DIR__) . '/www/lib/class.aes'.$aes.'.php');
  }else{
    include_once(dirname(__DIR__) . '/www/lib/class.aes.php');
  }
}
if(!class_exists('Printer')){
  include_once(dirname(__DIR__) . '/www/lib/class.printer.php');
}
if(!class_exists('PHPMailer')){
  include_once(dirname(__DIR__) . '/www/plugins/phpmailer/class.phpmailer.php');
}
if(!class_exists('SMTP')){
  include_once(dirname(__DIR__) . '/www/plugins/phpmailer/class.smtp.php');
}
$classes = array('briefpapier','lieferschein','auftrag','anfrage','gutschrift','bestellung','rechnung','mahnwesen');
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
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.string.php');
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

  $app->String         = new WawiString();
  if(class_exists('RemoteCustom'))
  {
    $app->remote = new RemoteCustom($app);
  }else{
    $app->remote = new Remote($app);
  }
  $app->Secure = new Secure($app);
  $app->User = new User($app);
  if(!defined('FPDF_FONTPATH'))
  {
    define('FPDF_FONTPATH',dirname(__DIR__).'/www/lib/pdf/font/');
  }


//ENDE

  $benutzername = $app->erp->Firmendaten("benutzername");
  $passwort = $app->erp->Firmendaten("passwort");
  $host = $app->erp->Firmendaten("host");
  $port = $app->erp->Firmendaten("port");
  $mailssl = $app->erp->Firmendaten("mailssl");
  $mailanstellesmtp = $app->erp->Firmendaten("mailanstellesmtp");

  // mail
  $app->mail = new PHPMailer($app);
  $app->mail->CharSet = "UTF-8";
  $app->mail->PluginDir="plugins/phpmailer/";

  if($mailanstellesmtp=="1"){
    $app->mail->IsMail();
  } else {
    $app->mail->IsSMTP();
    $app->mail->SMTPAuth   = true;                  // enable SMTP authentication
    if($mailssl==1){
      $app->mail->SMTPSecure = "tls";                 // sets the prefix to the servier
    }
    else if ($mailssl==2){
      $app->mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
    }

    $app->mail->Host       = $host;

    $app->mail->Port       = $port;                   // set the SMTP port for the GMAIL server

    $app->mail->Username   = $benutzername;  // GMAIL username
    $app->mail->Password   = $passwort;            // GMAIL password
  }

  $app->FormHandler = new FormHandler($app);

  $app->printer = new Printer($app);

  if(!class_exists('Tpl_t')){
    class Tpl_t
    {
      function Add($a, $b)
      {

      }

      function Set($a, $b)
      {

      }

      function Parse($a, $b)
      {

      }

    }
  }
  

  $app->Tpl = new Tpl_t();

  if(!class_exists('image'))
  {
    include_once(dirname(__DIR__).'/www/lib/class.image.php');
  }
  
  $app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'artikelcache' AND aktiv = 1");
  if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'artikelcache' AND aktiv = 1"))
  {
    return;
  }

  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");

  //Neue Artikel

  /** @var Artikel $obj */
  $obj = $app->erp->LoadModul('artikel');
  $obj->createNewAricleCache();
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");
  if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikelcache'])) {
    return;
  }
  $obj->createAricleCache();
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");
  //Lagerzahlen verkaufbar
  $obj->updateSellableToAricleCache();
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");
  if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikelcache'])) {
    return;
  }
  //Juststuecklisten
  $obj->updateExplodingPartListSellableToAricleCache();
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");
  $obj->updatePartListJitArticleStorageCache();
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");
  $obj->updatePartListSellableToAricleCache();
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");
  //im Sperrlager
  if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikelcache'])) {
    return;
  }

  $obj->updateQuarantineStoreToAricleCache();
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");
  if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikelcache'])) {
    return;
  }
  $obj->updateStorageToAricleCache();
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");
  $obj->updateAttributesToArticleCache();
  if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikelcache'])) {
    return;
  }
  if(method_exists($obj,'updateArticlePicturePreview')) {
    $app->DB->Update(
      "UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1"
    );
    $obj->updateArticlePicturePreview();
  }

  if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikelcache'])) {
    return;
  }
  $app->erp->RunHook('cronjob_artikelcache');

  $app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0 WHERE parameter = 'artikelcache'  AND aktiv = 1");


/*
  $app->DB->Insert("INSERT INTO artikel_cached_fields (artikel,lager_verfuegbar, ek_netto, vk_netto, ek_brutto, vk_brutto, inzulauf,imsperrlager) 
  SELECT a.id,0, 0, 0, 0, 0, 0, 0
  FROM artikel a 
  LEFT JOIN artikel_cached_fields acf ON a.id = acf.artikel WHERE isnull(acf.id)");
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikelcache' AND aktiv = 1");
  //Preise und Bestellungen
  $app->DB->Update("UPDATE artikel_cached_fields acf 
INNER JOIN artikel a ON acf.artikel = a.id
  LEFT JOIN artikel a2 ON a.variante_von = a2.id AND a.variante = 1 AND IFNULL(a2.geloescht,0) = 0 AND a2.nummer <> 'DEL'
    LEFT JOIN (SELECT artikel, min(preis) as minpreis 
    FROM einkaufspreise WHERE (gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate()) AND preis > 0 
    GROUP BY artikel ) ek ON a.id = ek.artikel
  LEFT JOIN (SELECT artikel, min(preis) as minpreis 
  FROM einkaufspreise WHERE (gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate()) AND preis > 0 
  AND (ifnull(waehrung,'EUR') = 'EUR' OR waehrung = '') 
  GROUP BY artikel ) ek2 ON a.id = ek2.artikel
  LEFT JOIN (SELECT artikel, min(preis) as minpreis 
  FROM verkaufspreise WHERE (gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate()) AND art <> 'Gruppe' AND art <> 'gruppe' 
  AND adresse=0 AND preis > 0 
  GROUP BY artikel ) vk ON a.id = vk.artikel
  LEFT JOIN (SELECT artikel, min(preis) as minpreis 
  FROM verkaufspreise WHERE (gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate()) AND art <> 'Gruppe' AND art <> 'gruppe'
  AND adresse=0 AND preis > 0 AND (ifnull(waehrung,'EUR') = 'EUR' OR waehrung = '') 
  GROUP BY artikel ) vk2 ON a.id = vk2.artikel
  LEFT JOIN (SELECT artikel, sum(bp.menge - bp.geliefert) as zulauf FROM 
   bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id
          WHERE b.status!='storniert' AND b.status!='abgeschlossen' AND bp.geliefert<bp.menge GROUP BY bp.artikel) zul ON a.id = zul.artikel
          SET acf.name = a.name_de,
              acf.number = a.nummer,
              acf.ean = a.ean,
              acf.customfield1 = a.freifeld1,
              acf.customfield2 = a.freifeld2,
              acf.is_variant = a.variante,
              acf.variant_from_id = a.variante_von,
              acf.variant_from_name = a2.name_de,
              acf.factory_number = a.herstellernummer,
              acf.manufactor = a.hersteller,
              acf.is_storage_article = a.lagerartikel,
              acf.is_partlist = a.stueckliste,
              acf.locked = a.intern_gesperrt,
              acf.is_shipping = a.porto,
          acf.ek_netto = ifnull( ifnull(ek2.minpreis,ek.minpreis),0), 
          acf.ek_brutto = if(a.umsatzsteuer = 'befreit', 1, if(a.umsatzsteuer = 'ermaessigt', $ermaessigt,$normal))*ifnull( ifnull(ek2.minpreis,ek.minpreis),0),
          acf.vk_netto = ifnull( ifnull(vk2.minpreis,vk.minpreis),0), 
          acf.vk_brutto = if(a.umsatzsteuer = 'befreit', 1, if(a.umsatzsteuer = 'ermaessigt', $ermaessigt,$normal))*ifnull( ifnull(vk2.minpreis,vk.minpreis),0),
          acf.inzulauf = ifnull(zul.zulauf,0)
          ");
*/

