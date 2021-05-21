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

use Xentral\Core\LegacyConfig\ConfigLoader;

/**
 * @property User $User
 * @property TemplateParser $Tpl
 * @property Config $Conf
 * @property YUI $YUI
 * @property WawiString $String
 * @property FormHandler $FormHandler
 * @property Table $Table
 * @property phpWFAPI $WF
 * @property WFMonitor $WFM
 * @property Secure $Secure
 * @property PHPMailer $mail
 * @property Help $help
 * @property StringCleaner $stringcleaner
 * @property Acl $acl
 * @property ModuleScriptCache $ModuleScriptCache
 * @property ObjectAPI $ObjAPI
 * @property erpAPI $erp
 * @property Remote $remote
 * @property Printer $printer
 * @property Location $Location
 * @property DatabaseUpgrade $DatabaseUpgrade
 * @property DB $DB
 */
class ApplicationCore
{
  protected $getter;
  protected $gettercustom;
  protected $props;
  protected $loadedModules;

  /** @var \Xentral\Core\DependencyInjection\ServiceContainer $Container */
  public $Container;

  public function __construct($config = null,$group='')
  {
    if($config === null)
    {
      $this->Conf = ConfigLoader::load();
    }else{
      $this->Conf = $config;
    }

    $registry = include dirname(__DIR__) . '/classes/bootstrap.php';
    $registry->add('LegacyApplication', $this);
    $this->Container = $registry->get('ServiceContainer');

    $this->getter = array(
      'User'=>array('class'=>'User','app'=>true),
      'Tpl'=>array('class'=>'TemplateParser','app'=>true),
      'Conf'=>array('class'=>'Config'),
      'YUI'=>array('class'=>'YUI','app'=>true),
      'String'=>array('class'=>'WawiString'),
      'FormHandler'=>array('class'=>'FormHandler','app'=>true),
      'Table'=>array('class'=>'Table','app'=>true),
      'WF'=>array('class'=>'phpWFAPI','app'=>true),
      'WFM'=>array('class'=>'WFMonitor','app'=>true),
      'Secure'=>array('class'=>'Secure','app'=>true),
      'mail'=>array('class'=>'PHPMailer','app'=>true),
      'help'=>array('class'=>'Help','app'=>true),
      'stringcleaner'=>array('class'=>'StringCleaner','app'=>true),
      'acl'=>array('class'=>'Acl','app'=>true),
      'ModuleScriptCache'=>array('class'=>'ModuleScriptCache'),
      'ObjAPI'=>array('class'=>'ObjectAPI','app'=>true),
      'FormSecure'=>array('class'=>'FormSecure','app'=>true),
      'Location'=>array('class'=>'Location','app'=>true),
      'DatabaseUpgrade'=>array('class'=>'DatabaseUpgrade','app'=>true),
      'PageBuilder' => ['class' => 'PageBuilder','app'=>true],
    );
    $this->gettercustom = array(
      'erp'=>array(
        'class'=>'erpAPI',
        'class_custom'=>'erpAPICustom',
        'file'=>dirname(__DIR__).'/www/lib/class.erpapi.php',
        'file_custom'=>dirname(__DIR__).'/www/lib/class.erpapi_custom.php'
      ),
      'remote'=>array(
        'class'=>'Remote',
        'class_custom'=>'RemoteCustom',
        'file'=>dirname(__DIR__).'/www/lib/class.remote.php',
        'file_custom'=>dirname(__DIR__).'/www/lib/class.remote_custom.php'
      ),
      'printer'=>array(
        'class'=>'Printer',
        'class_custom'=>'PrinterCustom',
        'file'=>dirname(__DIR__).'/www/lib/class.printer.php',
        'file_custom'=>dirname(__DIR__).'/www/lib/class.printer_custom.php'
      ),
    );
  }

  /**
   * @return array
   */
  public function getDbs()
  {
    return ConfigLoader::loadAllDescriptions();
  }

  /**
   * @param int $status
   */
  public function ExitXentral($status = 0)
  {
    if(class_exists('DevTools')){
      DevTools::exitcustom($this, $status);
    }

    $dbConnected = !empty($this->DB) && !empty($this->DB->connection);
    $cronjobToClose = defined('CRONJOBUID') && defined('FROMSTARTER2');
    if($dbConnected && $cronjobToClose && method_exists($this->erp,'closeAndLogCronjob')) {
      $this->erp->closeAndLogCronjob(CRONJOBUID, FROMSTARTER2);
    }

    if($dbConnected){
      $this->DB->Close();
    }

    exit($status);
  }

  /**
   * @param string $value
   *
   * @return mixed
   */
  public function __get($value)
  {
    if(isset($this->getter[$value]))
    {
      $class = $this->getter[$value]['class'];
      if($this->getter[$value]['app']){
        $this->$value = new $class($this);
        return $this->$value;
      }
      $this->$value = new $class();
      return $this->$value;
    }
    if(isset($this->gettercustom[$value]))
    {
      if(is_file($this->gettercustom[$value]['file_custom']))
      {
        $class = $this->gettercustom[$value]['class_custom'];
      }else{
        $class = $this->gettercustom[$value]['class'];
      }
      $this->$value = new $class($this);
      return $this->$value;
    }
    if($value === 'DB')
    {
      $this->DB = new DB($this->Conf->WFdbhost,$this->Conf->WFdbname,$this->Conf->WFdbuser,$this->Conf->WFdbpass,$this,$this->Conf->WFdbport);
      return $this->DB;
    }
  }

  /**
   * @param string $value
   *
   * @return bool
   */
  public function __isset($value)
  {
    if (isset($this->$value)){
      return true;
    }

    if($value === 'DB'){
      return class_exists('DB', true);
    }

    if(isset($this->getter[$value])){
      $className = $this->getter[$value]['class'];
      if(class_exists($className, true)){
        return true;
      }
    }

    if(isset($this->gettercustom[$value])){
      $className = $this->gettercustom[$value]['class'];
      $classNameCustom = $this->gettercustom[$value]['class_custom'];
      if(class_exists($classNameCustom, true) || class_exists($className, true)){
        return true;
      }
    }

    return false;
  }

  /**
   * @param string $name
   * @param mixed $value
   */
  public function __set($name, $value)
  {
    $this->$name = $value;
  }

  /**
   * @param string $class
   * @param bool   $ownInstance
   *
   * @return mixed|null
   */
  public function loadModule($class, $ownInstance = true) {
    $class = ucfirst($class);
    $classcustom = $class.'Custom';
    if(!$ownInstance && !empty($this->loadedModules[$classcustom])) {
      return $this->loadedModules[$classcustom];
    }
    if(!$ownInstance && !empty($this->loadedModules[$class])) {
      return $this->loadedModules[$class];
    }
    $phpname = strtolower($class);
    if(!class_exists($class) && @is_file(dirname(__DIR__) . '/www/pages/' . $phpname . '.php')){
      include_once dirname(__DIR__) . '/www/pages/' . $phpname . '.php';
    }
    if(!class_exists($classcustom) && @is_file(dirname(__DIR__) . '/www/pages/' . $phpname . '_custom.php') && class_exists($class)) {
      include_once dirname(__DIR__) . '/www/pages/' . $phpname . '_custom.php';
    }

    if(class_exists($classcustom)) {
      if(method_exists($classcustom, '__construct')) {
        try {
          $r = new ReflectionMethod($classcustom, '__construct');
          $params = $r->getParameters();
        } catch( Exception $e) {
          return null;
        }
        $anzargs = count($params);
        if($anzargs > 1) {
          $obj = new $classcustom($this, true);
        }
      }else{
        $obj = new $classcustom($this, true);
      }
    }

    if(!empty($obj)) {
      if($ownInstance) {
        return $obj;
      }
      $this->loadedModules[$classcustom] = $obj;
      return $this->loadedModules[$classcustom];
    }
    if(!class_exists($class)) {
      return null;
    }

    if(method_exists($class, '__construct'))
    {
      try {
        $r = new ReflectionMethod($class, '__construct');
        $params = $r->getParameters();
      } catch (Exception $e) {
        return null;
      }
      $anzargs = count($params);
      if($anzargs > 1) {
        $obj = new $class($this, true);
      }
    }else{
      $obj = new $class($this, true);
    }
    if(empty($obj)) {
      return null;
    }
    if($ownInstance) {
      return $obj;
    }
    $this->loadedModules[$class] = $obj;
    return $this->loadedModules[$class];
  }

  /**
   * @return array|null
   * @throws ReflectionException
   */
  function getAppList()
  {
    /** @var Appstore $obj */
    $obj = $this->loadModule('appstore');
    if(!empty($obj) && method_exists($obj,'getAppList'))
    {
      return $obj->getAppList();
    }

    return null;
  }

  public function IsWindows()
  {
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
  }

  public function getTmpFolder()
  {
    $userdata = $this->Conf->WFuserdata;

    if ($this->IsWindows()) {
      $tmp = $userdata."\\tmp\\";
    } else {
      $tmp = $userdata."/tmp/";
    }

    $tmp = str_replace('//','/',$tmp);

    if(!is_dir($tmp) && !mkdir($tmp) && !is_dir($tmp)) {
      return $tmp;
    }

    return $tmp;
  }

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
