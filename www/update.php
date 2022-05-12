<?php
use Xentral\Core\LegacyConfig\ConfigLoader;
@date_default_timezone_set("Europe/Berlin");
@ini_set('default_charset', 'UTF-8');

@ini_set('display_errors', 'off');
@error_reporting(0);
@ini_set("magic_quotes_runtime", 0);
@ignore_user_abort(true);

require_once dirname(__DIR__) . '/xentral_autoloader.php';
if (class_exists(Config::class)){
    $config = new Config();
    $updateHost = $config->updateHost ?: 'update.xentral.biz';
}else{
    $updateHost = 'update.xentral.biz';
}
define('XENTRAL_UPDATE_HOST', $updateHost);

class erpAPI_Update
{
  function __construct($app)
  {
    $this->app=$app;
  }
  
  function Branch()
  {
    return '';
  }

  function Version()
  {
    return '';
  }


  function RevisionPlain()
  {
    return '';
  }


  function Revision()
  {
    return '';
  }
  function Startseite()
  {
    if($this->app->User->GetID()!='')
    {
      $rand = md5(mt_rand());
      header('Location: update.php?rand='.$rand);
      exit;
    }
  }
  
  function calledOnceAfterLogin()
  {
    
    
  }
  
  function Firmendaten($value)
  {
    $id = $this->app->DB->Select("SELECT max(id) FROM firmendaten");
    if($id)
    {
      return $this->app->DB->Select("SELECT $value FROM firmendaten WHERE id = '$id' LIMIT 1");
    }
    return '';
  }

  /**
   * @param $name
   *
   * @return mixed
   */
  public function GetKonfiguration($name) {
    return $this->app->DB->Select("SELECT wert FROM konfiguration WHERE name='$name' LIMIT 1");
  }

  /**
   * @param string $name
   * @param string $value
   */
  public function SetKonfigurationValue($name, $value) {
    $this->app->DB->Delete("DELETE FROM konfiguration WHERE name='$name' LIMIT 1");
    $this->app->DB->Insert("INSERT INTO konfiguration (name,wert,firma,adresse) VALUES ('$name','$value',1,0)");
  }

  /**
   * @param bool $active
   */
  public function setMaintainance($active = true, $mode = 'updatedb') {
    $tags = json_encode('update');
    if(!$active) {
      if($this->GetKonfiguration('update_maintenance') == '0') {
        return;
      }
      $this->SetKonfigurationValue('update_maintenance', 0);
      $this->SetKonfigurationValue('update_maintenance_mode', '');
      $this->app->DB->Delete("DELETE FROM notification_message WHERE tags = '$tags'");
      return;
    }
    if(true) {
      return;//@todo remove in 20.1
    }
    if($this->GetKonfiguration('update_maintenance') == '1') {
      $this->SetKonfigurationValue('update_maintenance_time', time());
      return;
    }

    $this->app->DB->Insert(
      "INSERT INTO notification_message (user_id, type, title, message, tags, options_json, priority, created_at) 
        SELECT u.id, 'warning', 'laufender Updateprozess','Bitte schließen Sie Ihre Aufgaben','$tags','',1,NOW()
        FROM `user` AS u 
        INNER JOIN useronline uo on u.id = uo.user_id AND uo.login = 1"
    );

    $this->SetKonfigurationValue('update_maintenance', 1);
    $this->SetKonfigurationValue('update_maintenance_time', time());
  }
  
  function ClearDataBeforeOutput($text)
  {
    $text = str_replace('form action=""','form action="#"',$text);
    $text = str_replace('NONBLOCKINGZERO','',$text);
    $text = str_replace("&apos;","'",$text);
    return $text;
  }
  
  function convertToHtml($str) {
    if (version_compare(PHP_VERSION, '5.3.4') >= 0) {
      $trans_tbl = array_flip(get_html_translation_table(HTML_ENTITIES, ENT_COMPAT, 'UTF-8'));
    } else {
      $trans_tbl = array_flip(get_html_translation_table(HTML_ENTITIES, ENT_COMPAT));
      if (!empty($trans_tbl)) {
        foreach ($trans_tbl as $key => $entry) {
          $trans_tbl[$key] = utf8_encode($entry);
        }
      }
    }

    // MS Word strangeness..
    // smart single/ double quotes:
    $trans_tbl[chr(39)] = '&apos;';
    $trans_tbl[chr(145)] = '\'';
    $trans_tbl[chr(146)] = '\'';
    //$trans_tbl[chr(147)] = '&quot;';
    $trans_tbl[chr(148)] = '&quot;';
    $trans_tbl[chr(142)] = '&eacute;';
    //&#65279;
    //$trans_tbl[$this->unicode_chr(65279)] = "BENE";
    //$str = str_replace("\xFF\xFE", "BENE", $str);


    return strtr ($str, $trans_tbl);
  }
  
  function superentities( $str ){
    // get rid of existing entities else double-escape

    $str = html_entity_decode(stripslashes($str),ENT_QUOTES| ENT_HTML5,'UTF-8');
    //              $str = str_replace("'","&apos;",$str);
    //                              return $str;
    $ar = preg_split('/(?<!^)(?!$)/u', $str );  // return array of every multi-byte character
    foreach ($ar as $c){
      $o = ord($c);
      if ( (strlen($c) > 1) || /* multi-byte [unicode] */
          ($o <32 || $o > 126) || /* <- control / latin weirdos -> */
          ($o >33 && $o < 35) ||/* quotes + ambersand */
          ($o >35 && $o < 40) ||/* quotes + ambersand */
          ($o >59 && $o < 63) /* html */
         ) {
        // convert to numeric entity
        //$c = @mb_encode_numericentity($c,array (0x0, 0xffff, 0, 0xffff), 'UTF-8');
        $c = $this->convertToHtml($c);
      }
      if(!isset($str2))$str2 = '';
      $str2 .= $c;
    }
    return $str2;
  }
  
}
class UpdateerpooSystem extends UpdateApplication
{
  public $obj;
  public $starttime;
  public $endtime;

  public function __construct($config,$group="")
  {
    parent::__construct($config,$group);
    if(isset($_GET['action']) && $_GET['action'] == 'ajax' && isset($_GET['cmd']) && 'upgradedb' == $_GET['cmd'])
    {
      $className = 'erpAPI';
      //$methodName = 'UpgradeDatabase';
      if(file_exists(__DIR__.'/lib/class.erpapi.php'))
      {
        include_once(__DIR__.'/lib/class.erpapi.php');
      }
      if(file_exists(__DIR__.'/lib/class.erpapi_custom.php'))
      {
        include_once(__DIR__.'/lib/class.erpapi_custom.php');
        $className = 'erpAPICustom';
      }
      //$r = new ReflectionMethod($className, $methodName);
      //$params = $r->getParameters();
      //$anzargs = count($params);
      $this->erp = new $className($this);
    }else{
      $this->erp = new erpAPI_Update($this);
    }
  }
}

  class Md5Dateien
  {
    var $Dateien;
    function __construct($quellverzeichnis)
    {
      $this->getVerzeichnis($quellverzeichnis, '', 0, '');
    }
    
    function getVerzeichnis($quellverzeichnis, $zielverzeichnis, $lvl, $relativ){
      //echo "Verzeichnis: ".$quellverzeichnis." ".$zielverzeichnis.  "\r\n";
      
      $quelllast = $quellverzeichnis;
      if($quellverzeichnis[strlen($quellverzeichnis) - 1] === '/') {
        $quelllast = substr($quellverzeichnis, 0, strlen($quellverzeichnis) - 1);
      }

      $path_parts = pathinfo($quelllast);
      
      $quelllast = $path_parts['basename'];

      if(file_exists($quellverzeichnis)) 
      {
        if(($quelllast !== 'importer' && $quelllast !== 'userdata') || $lvl != 1){
          if ($handle = opendir($quellverzeichnis)) {
            while (false !== ($entry = readdir($handle))) {

              if($entry !== '.' && $entry !== '..' && $entry !== '.git' && $entry !== '.svn' &&  $entry != 'user.inc.php' && $entry != 'user_db_version.php' && $entry != 'pygen')
              {
                if(is_dir($quellverzeichnis.'/'.$entry))
                {
                  if(!($lvl == 1 && $entry === 'vorlagen' && strpos($quellverzeichnis,'www')))
                  {
                    $this->getVerzeichnis($quellverzeichnis.(strrpos($quellverzeichnis,'/')!==strlen($quellverzeichnis)-1?'/':'').$entry,$zielverzeichnis .(strrpos($zielverzeichnis,'/')!==strlen($zielverzeichnis)-1?'/':'').$entry, $lvl + 1,$relativ.'/'.$entry);
                  }
                } else {
                  if(!($lvl == 0 && ($entry === 'INSTALL' || $entry === 'LICENSE_LIST' || $entry == 'LICENSE' || $entry == 'README' || $entry == 'gitlog.txt')))
                  {
                    //$this->getFile($quellverzeichnis.(strrpos($quellverzeichnis,'/')!==strlen($quellverzeichnis)-1?'/':'').$entry,$zielverzeichnis .(strrpos($zielverzeichnis,'/')!==strlen($zielverzeichnis)-1?'/':'').$entry,$relativ.'/'.$entry);
                    if(strtolower(substr($entry,-4)) === '.php') {
                      $this->Dateien[$relativ.'/'.$entry] = md5_file($quellverzeichnis.(strrpos($quellverzeichnis,'/')!==strlen($quellverzeichnis)-1?'/':'').$entry);
                    }
                  }
                }
              }
            }
            @closedir($handle);
          }
        }
      }
      return true;
    }
  }

class UpdateDB{

  var $dbname;
  var $connection;

  function __construct($dbhost,$dbname,$dbuser,$dbpass,&$app="",$dbport=3306)
  {
    $this->app = &$app;
    $this->dbname=$dbname;

    $this->connection = mysqli_connect($dbhost, $dbuser, $dbpass, '', $dbport);
    mysqli_select_db($this->connection,$dbname);

    mysqli_query($this->connection,"SET NAMES 'utf8'");
    mysqli_query($this->connection,"SET SESSION SQL_MODE := ''");
    mysqli_query($this->connection,"SET CHARACTER SET 'utf8'");
    mysqli_query($this->connection,'SET lc_time_names = "de_DE" ');
    
  }

  /**
   * @return string
   */
  public function GetVersion()
  {
    if(empty($this->connection)) {
      return '';
    }
    $version_string = mysqli_get_server_info($this->connection);
    $version_string = substr($version_string,0,3);
    $version_string = str_replace('.','',$version_string);
    if($version_string < 57) {
      $version = $this->Select('SELECT VERSION()');
      if(strripos($version, 'maria') !== false && $version[0] === '1' && str_replace('.','', substr($version,0,4)) >= 102) {
        return '57';
      }
    }
    return $version_string;
  }

  function Close()
  {
    mysqli_close($this->connection);
  }

	function SelectDB($database)
  {
    mysqli_select_db($database);
  }

  function Fetch_Assoc($sql) {
    return mysqli_fetch_assoc($sql);
  }

  function free($query = null){
    // Speicher freimachen
    if(is_null($query))return mysqli_free_result($this->_result);
    return mysqli_free_result($query);
  }

	  function ColumnExists($table, $column)
  {
    if($table=='' || $column=='')
      return false;

		$exists = $this->Select("SELECT COUNT(*)
      FROM information_schema.columns
      WHERE table_schema = '{$this->dbname}' 
      AND table_name = '$table' AND column_name = '$column'");
		return $exists;
  }

  function Select($sql){
    if(mysqli_query($this->connection,$sql)){
      $this->results = mysqli_query($this->connection,$sql);
 			/**
       * Abbrechen query mit SET beginnt
       */
      if (substr(strtolower($sql),0,3) === 'set') {
        return "";
      }
      $count = 0;
      $data = array();
      while( $row = @mysqli_fetch_array($this->results)){
        $data[$count] = $row;
        $count++;
      }
      @mysqli_free_result($this->results);
    } else return false;
    if(is_array($data))
    {
      if(count($data) === 1)  {
        return $data[0][0];
      }
      if(count($data) < 1) {
        $data='';
      }
    } else {
      $data='';
    }
    return $data;
  }

  public function SelectRow($sql)
  {
    if(empty($sql) || empty($this->connection))
    {
      return null;
    }
    $this->results = @mysqli_query($this->connection,$sql);
    if(!$this->results)
    {
      return null;
    }

    $count = 0;
    $data = null;
    if( $row = @mysqli_fetch_array($this->results)){
      unset($ArrData);
      // erstelle datensatz array
      foreach($row as $key=>$value){
        if(!is_numeric($key))
        {
          $ArrData[$key]=$value;
        }
      }
      if(!empty($ArrData)){
        $data = $ArrData;
      }
      $count++;
    }
    @mysqli_free_result($this->results);
    return $data;
  }

  function SelectArr($sql){
    //if(mysqli_query($this->connection,$sql)){
    if(1){
      $this->results = mysqli_query($this->connection,$sql);
      $count = 0;
      $data = array();
      while( $row = @mysqli_fetch_array($this->results)){
				unset($ArrData); 
				// erstelle datensatz array
				foreach($row as $key=>$value){
	  			if(!is_numeric($key)) {
	  			  $ArrData[$key]=$value;
          }
				}
				$data[$count] = $ArrData;
        $count++;
      }
      @mysqli_free_result($this->results);
    }
    return $data;
  }
	
  function Result($sql){ return mysqli_result(mysqli_query($this->connection,$sql), 0);}

  function GetInsertID(){ return mysqli_insert_id($this->connection);}

  function GetArray($sql){
    $i=0;
    $result = mysqli_query($this->connection,$sql);
    while($row = mysqli_fetch_assoc($result)) {
      foreach ($row as $key=>$value){
        $tmp[$i][$key]=$value;
      }
      $i++;
    }
    return $tmp;
  }

  function Insert($sql){ $this->LogSQL($sql,"insert"); return mysqli_query($this->connection,$sql); }
  function InsertWithoutLog($sql){ return mysqli_query($this->connection,$sql); }
  function Update($sql){$this->LogSQL($sql,"update"); return mysqli_query($this->connection,$sql); }
  function UpdateWithoutLog($sql){ return mysqli_query($this->connection,$sql); }
  function Delete($sql){$this->LogSQL($sql,"delete"); return mysqli_query($this->connection,$sql); }

  function LogSQL($sql,$befehl)
  {

  }

  function Count($sql){
    if(mysqli_query($this->connection,$sql)){	
      return mysqli_num_rows(mysqli_query($this->connection,$sql));
    }
    return 0;
  }

  function CheckTableExistence($table){
    $result = mysqli_query($this->connection,"SELECT * FROM $table LIMIT 1");
    if (!$result) {
      return false;
    }
    return true;
  }

 
  function CheckColExistence($table,$col)
  {
    if($this->CheckTableExistence($table)){
      $result = mysqli_query($this->connection,"SHOW COLUMNS FROM $table");
      if (!$result) {
        echo 'Could not run query: ' . mysqli_error();
        exit;
      }
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          if($row['Field']==$col)
            return true;
        }
      }
    }
    return false;
  }



  function GetColArray($table)
  {
    if($this->CheckTableExistence($table)){
      $result = mysqli_query($this->connection,"SHOW COLUMNS FROM $table");
      if (!$result) {
        echo 'Could not run query: ' . mysqli_error();
        exit;
      }
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $ret[]=$row['Field'];
        }
        return $ret;
      }
    }
  }


  function GetColAssocArray($table)
  {
    if($this->CheckTableExistence($table)){
      $result = mysqli_query($this->connection,"SHOW COLUMNS FROM $table");
      if (!$result) {
        echo 'Could not run query: ' . mysqli_error();
        exit;
      }
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $ret[$row['Field']]="";
        }
        return $ret;
      }
    }
  }

  function UpdateArr($tablename,$pk,$pkname,$ArrCols, $escape = false)
  {
    if(count($ArrCols)>0){
      
      $zielspalten = $this->SelectArr("show columns from `$tablename`");
      if($zielspalten)
      {
        foreach($zielspalten as $val)$ziel[$val['Field']] = true;
      }     
      $sql = "UPDATE `$tablename` SET ";
      foreach($ArrCols as $key=>$value)
      {
        if($key!=$pkname && (isset($ziel[$key]) || !$zielspalten))
        {
          $sqla[] = $key." = '".($escape?$this->real_escape_string($value):$value)."' ";
        }
      }
      
      $sql .= implode(', ',$sqla)." WHERE `$pkname`='$pk' LIMIT 1";
      $this->Update($sql);
      
      if(mysqli_error($this->connection))
      {
        foreach($ArrCols as $key=>$value){
          if($key!=$pkname) {
            $this->Query("UPDATE `$tablename` SET `$key`='$value' 
            WHERE `$pkname`='$pk' LIMIT 1");
          }
        }
      }
    }
  }

  function InsertArr($tablename,$pkname,$ArrCols)
  {
    // save primary than update
    $this->Query("INSERT INTO `$tablename` (id) VALUES ('')");
    
    $pk = $this->GetInsertID();
    $this->UpdateArr($tablename,$pk,$pkname,$ArrCols);
  }

  /// get table content with specified cols 
  function SelectTable($tablename,$cols){
   
    $firstcol = true;
    if(count($cols)==0)
      $selection = '*';
    else 
    {
			$selection = '';
      foreach($cols as $value)
      {
        if(!$firstcol)
        $selection .= ','; 
        
        $selection .= $value;

        $firstcol=false;
      }
    }
 
    $sql = "SELECT $selection FROM $tablename";
    return $this->SelectArr($sql);
  }
	


  function Query($query){
    $ret = mysqli_query($this->connection,$query);
    if(mysqli_errno($this->connection) == 1118) {
      mysqli_query($this->connection, 'SET innodb_strict_mode = OFF');
      $ret = mysqli_query($this->connection, $query);
    }
    return $ret;
  }

  function Fetch_Array($sql) {
    return mysqli_fetch_array($sql);
  }


  function MysqlCopyRow($TableName, $IDFieldName, $IDToDuplicate) 
  {
    if ($TableName AND $IDFieldName AND $IDToDuplicate > 0) {
      $sql = "SELECT * FROM $TableName WHERE $IDFieldName = $IDToDuplicate";
      $result = @mysqli_query($this->connection,$sql);
      if ($result) {
        $sql = "INSERT INTO $TableName SET ";
        $row = mysqli_fetch_array($result);
        $RowKeys = array_keys($row);
        $RowValues = array_values($row);
        $cKey = count($RowKeys);
        for ($i=3;$i<$cKey;$i+=2) {
          if ($i!=3) { $sql .= ", "; }
          $sql .= $RowKeys[$i] . " = '" . $RowValues[$i] . "'";
        }
        @mysqli_query($this->connection,$sql);
        return $this->GetInsertID();
      }
    }
  }

  function real_escape_string($value)
  {
    return mysqli_real_escape_string($this->connection, $value);
  }
  
  function affected_rows()
  {
    return mysqli_affected_rows($this->connection);
  }
  
  function error()
  {
    return mysqli_error($this->connection);
  }
}

class UpgradeClient
{
	var $localmd5sums;
  var $erp;
  var $http_code;
  public $errormsg;
	
	function __construct($conf, $app)
	{
    $this->app = $app;
    $this->erp = $app->erp;
		$this->conf = $conf;
	
	}
	

	function Connect()
	{
		// check connection then stop	
	
	}	
	

	function CheckCRT()
	{
	    $updateHost = XENTRAL_UPDATE_HOST;
		$cert = shell_exec("openssl s_client -connect {$updateHost}:443 < /dev/null 2>/dev/null | openssl x509 -in /dev/stdin");
		if($cert==$this->conf['cert']."\n") {
		  return 1;
    }
    echo "wrong\n";
    exit;
	}
  
  function TestModul($modul)
  {
    $parameter['version']=@$this->conf['version'];
    $parameter['module'] = $modul;

		return $this->Request('settestmodul',$parameter);
  }

  function CheckVersionen($funktionen = null, $returnfirst = false)
  {
    $phpversion = PHP_VERSION;
    $ioncube_loader_version = '';
    $this->app->Tpl->Set('PHPVERSION',$phpversion);
    $this->app->Tpl->Set('IONCUBEVERSION','');
    if(!is_dir(dirname(__DIR__).'/download')){
      if(!@mkdir(dirname(__DIR__) . '/download') && !is_dir(dirname(__DIR__) . '/download')){
        $message = 'Im Hauptordner von xentral kann der Ordner &quot;download&quot; Verzeichnis nicht angelegt werden, Pr&uuml;fen Sie die Rechte';
        if($returnfirst) {
          return ['error'=>$message,'version'=>''];
        }
        return $message;
      }
    }
    if(function_exists('ioncube_loader_version'))
    {
      $ioncube_loader_version = (String)ioncube_loader_version();
      $this->app->Tpl->Set('IONCUBEVERSION',$ioncube_loader_version);
    }
    if($funktionen) {
      $parameter['funktionen'] = $funktionen;
    }
    $parameter['version']=@$this->conf['version'];
    $parameter['phpversion'] = $phpversion;
    $parameter['mysqlversion'] = $this->app->DB->GetVersion();
    if(property_exists($this->app, 'multidb')){
      $parameter['multidb'] = !empty($this->app->multidb);
    }
		$result = $this->Request('versionen',$parameter);
    if($result == ''){
      $result = $this->Request('versionen',$parameter);
    }
    if($result == ''){
      $message = 'Der Updateserver scheint nicht erreichbar zu sein Bitte pr&uuml;fen Sie die Netzwerkeinstellungen';
      if($returnfirst) {
        return ['error'=>$message,'version'=>''];
      }
      return $message;
    }
    $ret = '';
    $aktvers = '';
    $masterkey1erlaubt = $ioncube_loader_version?true:false;
    $masterkey2erlaubt = $ioncube_loader_version?true:false;
    $masterkey3erlaubt = $ioncube_loader_version?true:false;
    $masterkey4erlaubt = $ioncube_loader_version?true:false;
    $isPhp72 = (float)substr($phpversion,0,3) >= 7.2;
    $isPhp73 = (float)substr($phpversion,0,3) >= 7.3;
    $php73Warning = false;
    $isMysql57 = $this->app->DB->GetVersion() >= 57;
    $allow201 = $isPhp72 && $isMysql57;
    if(!$allow201) {
      $masterkey4erlaubt = false;
    }
    if(strlen($phpversion) > 2 && $phpversion[0] == '5' && $phpversion[2] < 6) {
      $masterkey2erlaubt = false;
      $masterkey3erlaubt = false;
      $masterkey4erlaubt = false;
    }
    if((int)$phpversion[0] < 7) {
      $masterkey3erlaubt = false;
      $masterkey4erlaubt = false;
    }
    if($phpversion && $phpversion[0] === '8') {
      $masterkey1erlaubt = false;
      $masterkey2erlaubt = false;
    }
    if($phpversion && $phpversion[0] === '7') {
      $masterkey1erlaubt = false;
    }
    if(strlen($phpversion) > 2 && $phpversion[0] === '7' && $phpversion[2] !== '0') {
      $masterkey1erlaubt = false;
      $masterkey2erlaubt = false;
    }
    if(strlen($phpversion) > 2 && $phpversion[0] === '7' && $phpversion[2] === '0') {
      $masterkey1erlaubt = false;
      $masterkey3erlaubt = false;
      $masterkey4erlaubt = false;
    }
    if(strlen($ioncube_loader_version) > 2 && $ioncube_loader_version[0]< 5 && $ioncube_loader_version[1] === '.') {
      $masterkey1erlaubt = false;
      $masterkey2erlaubt = false;
    }
    $return = [];
    if(strpos($result, 'ERROR') === false) {
      $resulta = explode(';',$result);
      
      if($masterkey1erlaubt && $masterkey2erlaubt && $masterkey3erlaubt) //Pruefung der PHP-Version ist fehlgeschlagen => nehme aktuelle Version als Basis
      {
        $versa = explode(':',$resulta[0],2);
        $aktvers = $versa[0];
        $revision = explode('_', $aktvers);
        $revision = $revision[count($revision)-1];
        if(strpos($aktvers, 'masterkey1') !== false) {
          $aktmasterkey = 'masterkey1';
        }
        elseif(strpos($aktvers, 'masterkey2') !== false) {
          $aktmasterkey = 'masterkey2';
        }
        elseif(strpos($aktvers, 'masterkey3') !== false) {
          $aktmasterkey = 'masterkey3';
        }
        elseif(strpos($aktvers, 'masterkey3') !== false) {
          $aktmasterkey = 'masterkey3';
        }
        elseif(strpos($aktvers, 'masterkey4') !== false) {
          $aktmasterkey = 'masterkey4';
        }
        else {
          $aktmasterkey = '';
        }
        if($aktmasterkey === 'masterkey3' && $revision >= 20.2 && !$allow201) {
          $aktvers = '';
        }
        elseif($aktmasterkey === 'masterkey4' && $revision >= 20.3 && !$allow201) {
          $aktvers = '';
        }
        else{
          if($aktmasterkey === 'masterkey1') {
            $masterkey2erlaubt = false;
            $masterkey3erlaubt = false;
            $masterkey4erlaubt = false;
          }
          elseif($aktmasterkey === 'masterkey2') {
            $masterkey1erlaubt = false;
            $masterkey3erlaubt = false;
            $masterkey4erlaubt = false;
          }
          elseif($aktmasterkey === 'masterkey3') {
            $masterkey1erlaubt = false;
            $masterkey2erlaubt = false;
          }
          elseif($aktmasterkey === 'masterkey4') {
            $masterkey1erlaubt = false;
            $masterkey2erlaubt = false;
          }
        }
      }
      
      foreach($resulta as $k => $v) {
        $versa = explode(':',$resulta[$k],2);
        $revision = explode('_', $versa[0]);
        $revision = $revision[count($revision)-1];

        if($returnfirst && empty($return)){
          $return['current_version'] = $versa[0];
        }

        if(!$masterkey1erlaubt && strpos($versa[0], 'masterkey1')!== false) {
          unset($resulta[$k]);
        }
        elseif(!$masterkey2erlaubt && strpos($versa[0], 'masterkey2')!== false) {
          unset($resulta[$k]);
        }
        elseif(!$masterkey3erlaubt && strpos($versa[0], 'masterkey3')!== false) {
          unset($resulta[$k]);
        }
        elseif(!$masterkey4erlaubt && strpos($versa[0], 'masterkey4')!== false) {
          unset($resulta[$k]);
        }
        elseif($revision >= 20.2 && !$allow201 && strpos($versa[0], 'masterkey3')!== false) {
          unset($resulta[$k]);
        }
        elseif($revision >= 20.3 && !$allow201 && strpos($versa[0], 'masterkey4')!== false) {
          unset($resulta[$k]);
        }
        elseif($revision >= 21.1 && !$isPhp73 && strpos($versa[0], 'masterkey4')!== false) {
          $php73Warning = true;
          unset($resulta[$k]);
        }
        elseif($aktvers == '') {
          $aktvers = $versa[0];
          if(strpos($aktvers, 'masterkey1') !== false) {
            $aktmasterkey = 'masterkey1';
          }
          elseif(strpos($aktvers, 'masterkey2') !== false) {
            $aktmasterkey = 'masterkey2';
          }
          elseif(strpos($aktvers, 'masterkey3') !== false) {
            $aktmasterkey = 'masterkey3';
          }
          elseif(strpos($aktvers, 'masterkey3') !== false) {
            $aktmasterkey = 'masterkey3';
          }
          elseif(strpos($aktvers, 'masterkey4') !== false) {
            $aktmasterkey = 'masterkey4';
          }
          else {
            $aktmasterkey = '';
          }
        }
      }
      
      foreach($resulta as $k => $v) {
        $versa = explode(':',$resulta[$k],2);
        if(!$aktvers) {
          $aktvers = $versa[0];
          if(strpos($aktvers, 'masterkey1') !== false) {
            $aktmasterkey = 'masterkey1';
          }
          elseif(strpos($aktvers, 'masterkey2') !== false) {
            $aktmasterkey = 'masterkey2';
          }
          elseif(strpos($aktvers, 'masterkey3') !== false) {
            $aktmasterkey = 'masterkey3';
          }
          elseif(strpos($aktvers, 'masterkey3') !== false) {
            $aktmasterkey = 'masterkey3';
          }
          elseif(strpos($aktvers, 'masterkey4') !== false) {
            $aktmasterkey = 'masterkey4';
          }
          else {
            $aktmasterkey = '';
          }
          if($aktmasterkey === 'masterkey1' && !$masterkey1erlaubt) {
            $aktmasterkey = '';
          }
          if($aktmasterkey === 'masterkey2' && !$masterkey2erlaubt) {
            $aktmasterkey = '';
          }
          if($aktmasterkey === 'masterkey3' && !$masterkey3erlaubt) {
            $aktmasterkey = '';
          }
          if($aktmasterkey === 'masterkey4' && !$masterkey4erlaubt) {
            $aktmasterkey = '';
          }
        }
        if(strpos($versa[0],'masterkey')!== false) {
          if(!$ioncube_loader_version)
          {
            unset($resulta[$k]);
          }else{
            if(strpos($versa[0],'masterkey2')!== false)
            {
              if($phpversion && $phpversion[0] == '5' && $phpversion[2] < 6)
              {
                unset($resulta[$k]);
                if(in_array($aktmasterkey, ['masterkey2','masterkey3','masterkey4'])) {
                  $aktmasterkey = '';
                }
              }else{
                if(in_array($aktmasterkey, ['masterkey3','masterkey4']) && (int)$phpversion[0] < 7) {
                  $aktmasterkey = '';
                }
                if(in_array($aktmasterkey, ['masterkey3','masterkey4']) && ($phpversion[0] == '7' && $phpversion[2] == '0')) {
                  $aktmasterkey = '';
                }
                if($ioncube_loader_version[0]< 5 && $ioncube_loader_version[1] === '.')
                {
                  unset($resulta[$k]);
                  if(in_array($aktmasterkey, ['masterkey2','masterkey3','masterkey4'])) {
                    $aktmasterkey = "";
                  }

                }elseif($phpversion && $phpversion[0] == '7' && (int)$phpversion[2] > 0)
                {
                  unset($resulta[$k]);
                  if($aktmasterkey === 'masterkey2')$aktmasterkey = "";
                }
              }
            }
            elseif(strpos($versa[0],'masterkey1')!== false)
            {
              if($phpversion && (int)$phpversion[0] >= '7')
              {
                unset($resulta[$k]);
                if($aktmasterkey === 'masterkey1')$aktmasterkey = "";
              }
            }
            elseif(strpos($versa[0],'masterkey3')!== false)
            {
              if($phpversion && $phpversion[0] == '5' && $phpversion[2] < 6)
              {
                unset($resulta[$k]);
                if(in_array($aktmasterkey, ['masterkey2','masterkey3','masterkey4'])) {
                  $aktmasterkey = '';
                }
              }else{
                if((int)$phpversion[0] < 7)
                {
                  unset($resulta[$k]);
                  if($aktmasterkey === 'masterkey3')$aktmasterkey = "";
                }
                if($phpversion[0] === '7' && $phpversion[2] === '0')
                {
                  if(in_array($aktmasterkey, ['masterkey3','masterkey4'])) {
                    $aktmasterkey = '';
                  }
                  unset($resulta[$k]);
                }
                if($ioncube_loader_version[0]< 5 && $ioncube_loader_version[1] === '.')
                {
                  unset($resulta[$k]);
                  if(in_array($aktmasterkey, ['masterkey2','masterkey3','masterkey4'])) {
                    $aktmasterkey = '';
                  }
                }
              }
            }
            elseif(strpos($versa[0],'masterkey4') !== false) {
              if(!$masterkey4erlaubt) {
                unset($resulta[$k]);
              }
            }
          }
          if(isset($resulta[$k])) {
            if($masterkey1erlaubt && strpos($versa[0],'masterkey1')!== false && $aktmasterkey == '')
            {
              $aktmasterkey = 'masterkey1';
            }
            elseif($masterkey2erlaubt && strpos($versa[0],'masterkey2')!== false && $aktmasterkey == '') {
              $aktmasterkey = 'masterkey2';
            }
            elseif($masterkey3erlaubt && strpos($versa[0],'masterkey3')!== false && $aktmasterkey == '') {
              $aktmasterkey = 'masterkey3';
            }
            elseif($masterkey4erlaubt && strpos($versa[0],'masterkey4')!== false && $aktmasterkey == '') {
              $aktmasterkey = 'masterkey4';
            }
            $nochioncubes[$versa[0]] = $k;
          }
        }
      }
      
      if(count($resulta) > 1) {
        foreach($resulta as $k => $v) {
          $versa = explode(':',$resulta[$k],2);
          if(strpos($versa[0], 'masterkey') !== false) {
            if(!isset($nochioncubes[$versa[0]])) {
              unset($resulta[$k]);
            }
            else{
              $key1 = str_replace(['masterkey2','masterkey3','masterkey4',],'masterkey1', $versa[0]);
              $key2 = str_replace(['masterkey1','masterkey3','masterkey4',],'masterkey2', $versa[0]);
              $key3 = str_replace(['masterkey1','masterkey2','masterkey4',],'masterkey3', $versa[0]);
              $key4 = str_replace(['masterkey1','masterkey2','masterkey3',],'masterkey4', $versa[0]);
              switch($aktmasterkey) {
                case 'masterkey1':
                  if(isset($nochioncubes[$key1]) && isset($nochioncubes[$key2]))
                  {
                    unset($nochioncubes[$key2]);
                  }
                  if(isset($nochioncubes[$key1]) && isset($nochioncubes[$key3])) {
                    unset($nochioncubes[$key3]);
                  }
                  if(isset($nochioncubes[$key1]) && isset($nochioncubes[$key4])) {
                    unset($nochioncubes[$key4]);
                  }
                  if(isset($nochioncubes[$key2]) && isset($nochioncubes[$key3]))
                  {
                    unset($nochioncubes[$key3]);
                  }
                break;
                case 'masterkey2':
                  if(isset($nochioncubes[$key2]) && isset($nochioncubes[$key1])) {
                    unset($nochioncubes[$key1]);
                  }
                  if(isset($nochioncubes[$key2]) && isset($nochioncubes[$key3])) {
                    unset($nochioncubes[$key3]);
                  }
                  if(isset($nochioncubes[$key2]) && isset($nochioncubes[$key4])) {
                    unset($nochioncubes[$key4]);
                  }
                  if(isset($nochioncubes[$key1]) && isset($nochioncubes[$key3])) {
                    unset($nochioncubes[$key3]);
                  }
                break;              
                case 'masterkey3':
                  if(isset($nochioncubes[$key3]) && isset($nochioncubes[$key1]))
                  {
                    unset($nochioncubes[$key1]);
                  }
                  if(isset($nochioncubes[$key3]) && isset($nochioncubes[$key2]))
                  {
                    unset($nochioncubes[$key2]);
                  }
                  if(isset($nochioncubes[$key1]) && isset($nochioncubes[$key2]))
                  {
                    unset($nochioncubes[$key1]);
                  }
                break;
                case 'masterkey4':
                  if(isset($nochioncubes[$key4]) && isset($nochioncubes[$key1])) {
                    unset($nochioncubes[$key1]);
                  }
                  if(isset($nochioncubes[$key4]) && isset($nochioncubes[$key2])) {
                    unset($nochioncubes[$key2]);
                  }
                  if(isset($nochioncubes[$key4]) && isset($nochioncubes[$key3])) {
                    unset($nochioncubes[$key3]);
                  }
                  if(isset($nochioncubes[$key1]) && isset($nochioncubes[$key2])) {
                    unset($nochioncubes[$key1]);
                  }
                break;
              }
              if(!isset($nochioncubes[$versa[0]])) {
                unset($resulta[$k]);
              }
            }
          }
        }
      }
      
      if(count($resulta) > 1)
      {
        $ret = '<select id="verssel" onchange="versel()">';
        $i = 0;
        $isVersion211Exists = false;
        foreach($resulta as $resu)
        {
          $versa = explode(':',$resu,2);
          if($returnfirst) {
            $return['version'] = $versa[0];
            return $return;
          }
          if($i === 0) {
            $this->app->Tpl->Set('AKTVERSION', $versa[0]);
          }
          $ret .= '<option value="'.$versa[0].'">'.$versa[1].'</option>';
          if($versa[0] === 'ent_masterkey4_21.1') {
            $isVersion211Exists = true;
          }
          $i++;
        }
        $ret .= '</select>';
        $ret .= '<input class="button2" type="button" value="Updaten" id="upgrade" onclick="upgrade()"  />';
        if($isVersion211Exists){
          $ret .= '<div style="padding-top:3rem">
            <b style="color:red;font-size:150%">
            Um beim Versand von Versandbestätigungen (Trackingmails) an Ihre Kunden mehr Flexibilität zu bieten,<br /> 
            kann der Versand sowohl pro Projekt als auch pro Versandart aktiviert werden.<br /> 
            Einstellungen in einer einzelnen Versandart stechen die aus dem Projekt.<br /> 
            Es empfiehlt sich daher, die Einstellungen gemäß der eigenen Anforderungen zu überprüfen.<br /> 
            Für jede Versandart, für die Versandbestätigungen per E-Mail an die Kunden gesendet werden sollen,<br /> 
            ist die Einstellung in der Versandart zu setzen.<br /> 
            Eine genaue Erläuterung über das aktuelle Verhalten findet sich 
            <a target="_blank" style="color:red;" 
            href="https://community.xentral.com/hc/de/articles/360017571259-Logistikprozesse#toc-14"
            >
            hier</a>
           </b>
           </div>';
        }
      }
      elseif(count($resulta) == 1) {
        $resu = reset($resulta);
        //foreach($resulta as $resu)
        //{
          $versa = explode(':',$resu,2);
          if($returnfirst) {
            $return['version'] = $versa[0];
            return $return;
          }
          $this->app->Tpl->Set('AKTVERSION', $versa[0]);
          $ret .= '<input type="button" class="button2" value="'.$versa[1].'" id="upgrade" onclick="upgrade()" />';
        //}
      }else{
        if($ioncube_loader_version !== '' && !$masterkey1erlaubt && !$masterkey2erlaubt && !$masterkey3erlaubt)
        {
          $message = 'Die Ioncubeversion ist zu alt';
          $ret .= $message;
        }else{
          $message = 'Ioncube nicht verf&uuml;gbar';
          $ret .= $message;
        }
        if($returnfirst) {
          return ['error' => $message, 'version' => ''];
        }
      }
      if($php73Warning && count($resulta) > 0) {
        $ret .= '<br />'.'<b style="color:red;font-size:150%">
              Fehler: Ihre PHP-Version '
          . $phpversion
          . ' ist nicht kompatibel mit xentral 21.1 (Es wird mindestens PHP 7.3 benötigt)
              </b>';
      }
    }
    else{
      $this->errormsg = substr($result, 6);
      if($returnfirst) {
        return ['error'=>$this->errormsg,'version'=>''];
      }
      return $result;
    }

    return $ret;
  }
  
  function CheckMd5()
  {
    $parameter['version']=@$this->conf['version'];
    $parameter['withsize'] = 1;

		return $this->Request('md5list',$parameter);
  }
  
  function CopyFile($files, $maxtime = 10)
  {
    $parameter['versionname']=@$this->conf['versionname'];
    $startzeit = microtime(true);
    if(empty($files)) {
      return array('tocopy'=>null);
    }
    foreach($files as $k => $file)  {
      $file = json_decode(json_encode($file),true);
      if(isset($file['typ'])) {
        switch($file['typ']) {
          case 'getfile':
          case 'getfilecustom':
          case 'getfilemodules':
          
          break;
          default:
            $file['typ'] = '';
          break;
        }
      }
      else {
        $file['typ'] = '';
      }
      if(!isset($file['file']) || !isset($file['md5sum']) || !$file['file'] || $file['typ'] === '') {
        unset($files[$k]);
      }
      else{
        $parameter['file']=$file['file'];
        $parameter['md5sum']=$file['md5sum'];
        $ffile = $file['file'];
        $_file = dirname(__DIR__).'/download/'.$ffile;
        $_fileto = dirname(__DIR__).'/'.$ffile;

        $ffa = explode('/',$ffile);
        $_f = '';
        $cffa = count($ffa)-1;
        for($i = 0; $i < $cffa; $i++) {
          $_f .= $ffa[$i];
          if(is_file(dirname(__DIR__).'/'.$_f)) {
            $this->removeEmptyFile(dirname(__DIR__).'/'.$_f);
          }
          if(!is_dir(dirname(__DIR__).'/'.$_f) &&
            !@mkdir(dirname(__DIR__).'/'.$_f) &&
            !is_dir(dirname(__DIR__).'/'.$_f)
          ) {
            continue;
          }
          $_f .= '/';
        }
                
        if(file_exists($_file)) {
          if(substr($file['md5sum'],0,3)=== 'DEL') {
            if($this->CheckVersandZahlungsweise($_file)) {
              @unlink($_file);
            }
          }
          elseif(md5_file($_file)==$file['md5sum']) {
            if(is_dir($_fileto) && is_file($_file)){
              $this->removeEmptyFolder($_fileto);
            }

            if(@copy($_file,$_fileto)) {
              if(md5_file($_fileto)==$file['md5sum']){
                unset($files[$k]);
              }              
            }
          }
        }
        if(substr($file['md5sum'],0,3)=== 'DEL') {
          unset($files[$k]);
        }
      }
      if($maxtime > 0 && microtime(true) - $startzeit > $maxtime) {
        break;
      }
    }
    if(empty($files)) {
      return array('tocopy'=>null);
    }
    foreach($files as $k => $file) {
      $data[] = $file;
    }

    return array('tocopy'=>$data);
  }
  
  function CheckVersandZahlungsweise($datei){
    if(strpos($datei, 'versandart') !== false) {
      $dateia = pathinfo($datei);
      $versandart = $dateia['filename'];
      if(strpos($versandart, 'versandarten_')) {
        $versandart = str_replace('versandarten_', '', $versandart);
      }
      if($this->app->DB->Select(
        "SELECT id 
        FROM versandarten 
        WHERE modul = '".$this->app->DB->real_escape_string($versandart)."' AND ifnull(geloescht,0) = 0 AND aktiv = 1 
        LIMIT 1"
      )) {
        return false;
      }
      return true;
    }
    if(strpos($datei, 'zahlungsweise') !== false) {
      $dateia = pathinfo($datei);
      $zahlungsweise = $dateia['filename'];
      if($this->app->DB->Select(
        "SELECT id 
        FROM `zahlungsweisen` 
        WHERE modul = '".$this->app->DB->real_escape_string($zahlungsweise)."' AND ifnull(geloescht,0) = 0 AND aktiv = 1 
        LIMIT 1"
      )) {
        return false;
      }
      return true;
    }
    if(strpos($datei, 'cronjobs') !== false) {
      $dateia = pathinfo($datei);
      $cronjob = $dateia['filename'];
      if($this->app->DB->Select(
        "SELECT id 
        FROM `prozessstarter` 
        WHERE parameter = '".$this->app->DB->real_escape_string($cronjob)."' AND aktiv = 1 
        LIMIT 1"
      )) {
        return false;
      }
    }
    return true;
  }
  
  function ChangeVersion()
  {
    $parameter['version']=@$this->conf['version'];
    $parameter['versionname']=@$this->conf['versionname'];
    if($parameter['versionname'] && $parameter['versionname'] != $parameter['version']) {
      $changeversion = $this->Request('changeversion',$parameter);
    }
    return $changeversion;    
  }

  function removeEmptyFile($file) {
	  if(is_file($file) && filesize($file) === 0) {
	    @unlink($file);
    }
  }

  function removeEmptyFolder($folder)
  {
    if(empty($folder) || !is_dir($folder)){
      return;
    }
    if(!($handle = opendir($folder))) {
      return;
    }

    while (false !== ($entry = readdir($handle))) {
      if($entry !== '.' && $entry !== '..') {
        closedir($handle);
        return;
      }
    }
    closedir($handle);
    rmdir($folder);
  }
  
  function DownloadFile($files, $maxtime = 15, $echo = false)
  {
    $startzeit = microtime(true);
    $parameter['version']=@$this->conf['version'];
    $parameter['versionname']=@$this->conf['versionname'];
    
    $parameter['version']=@$this->conf['version'];
    $parameter['versionname']=@$this->conf['versionname'];
    if($parameter['versionname'] && $parameter['versionname'] != $parameter['version']) {
      $changeversion = $this->Request('changeversion',$parameter);
    }
    if(empty($files)) {
      return array('todownload'=>null);
    }
    $countFiles = count($files);
    $batches = [];
    $batch = [];
    $keyToBatch = [];
    foreach($files as $k => $file) {
      $file = json_decode(json_encode($file), true);
      if(isset($file['typ'])){
        switch ($file['typ']) {
          case 'getfile':
          case 'getfilecustom':
          case 'getfilemodules':

            break;
          default:
            $file['typ'] = '';
            break;
        }

      }else{
        $file['typ'] = '';
      }
      if(!isset($file['file']) || !isset($file['md5sum']) || !$file['file'] || $file['typ'] === ''){
        $files[$k]['error'] = $file['file'];
        unset($files[$k]);
      }else{
        if(substr($file['md5sum'], 0, 3) === 'DEL'){
          continue;
        }
        $parameter['file'] = $file['file'];
        $parameter['md5sum'] = $file['md5sum'];
        $ffile = $file['file'];
        $_file = dirname(__DIR__) . '/download/' . $ffile;
        $ffa = explode('/', $ffile);
        $_f = '';
        for ($i = 0; $i < count($ffa) - 1; $i++) {
          $_f .= $ffa[$i];
          if(is_file(dirname(__DIR__) . '/download/' . $_f)){
            @unlink(dirname(__DIR__) . '/download/' . $_f);
          }
          if(!is_dir(dirname(__DIR__) . '/download/' . $_f) &&
            !@mkdir(dirname(__DIR__) . '/download/' . $_f) &&
            !is_dir(dirname(__DIR__) . '/download/' . $_f)){
            continue;
          }
          $_f .= '/';
        }
      }
      switch($file['typ']) {
        case 'getfile':
          $batch[] = $k;
          $keyToBatch[$k] = count($batches);
          if(count($batch) >= 10) {
            $batches[] = $batch;
            $batch = [];
          }
          break;
      }
    }
    if(!empty($batch)) {
      $batches[] = $batch;
    }

    foreach($files as $k => $file) {
      $file = json_decode(json_encode($file),true);
      if(isset($file['typ'])) {
        switch($file['typ']) {
          case 'getfile':
          case 'getfilecustom':
          case 'getfilemodules':
          
          break;
          default:
            $file['typ'] = '';
          break;
        }
        
      }
      else {
        $file['typ'] = '';
      }
      if(!isset($file['file']) || !isset($file['md5sum']) || !$file['file'] || $file['typ'] === '') {
        $files[$k]['error'] = $file['file'];
        unset($files[$k]);
      }
      else{
        if(substr($file['md5sum'],0,3) === 'DEL') {
          continue;
        }
        $parameter['file']=$file['file'];
        $parameter['md5sum']=$file['md5sum'];
        $ffile = $file['file'];
        $_file = dirname(__DIR__).'/download/'.$ffile;
        $ffa = explode('/',$ffile);
        $_f = '';
        for($i = 0; $i < count($ffa)-1; $i++) {
          $_f .= $ffa[$i];
          if(is_file(dirname(__DIR__).'/download/'.$_f)) {
            @unlink(dirname(__DIR__).'/download/'.$_f);
          }
          if(!is_dir(dirname(__DIR__).'/download/'.$_f) &&
            !@mkdir(dirname(__DIR__).'/download/'.$_f) &&
            !is_dir(dirname(__DIR__).'/download/'.$_f)) {
            continue;
          }
          $_f .= '/';
        }
        if($echo) {
          echo "\rDownload Files: ".($k < $countFiles?$k+1:$countFiles).' / '.$countFiles."...        ";
        }
        if(isset($keyToBatch[$k]) && isset($batches[$keyToBatch[$k]])) {
          $batch = $batches[$keyToBatch[$k]];
          if(count($batch) > 1) {
            $parameter2 = $parameter;
            $parameter2['parameters'] = [];
            foreach ($batch as $key2) {
              $file2 = $files[$key2];
              $parameter2['parameters'][] = $parameter;
              $parameter2['parameters'][count($parameter2['parameters']) - 1]['file'] = $file2['file'];
              $parameter2['parameters'][count($parameter2['parameters']) - 1]['md5sum'] = $file2['md5sum'];
            }
            $result2 = explode('|', $this->Request('getfiles', $parameter2));
            if(count($result2) === count($batch)) {
              foreach ($batch as $bachKey => $key2) {
                $file2 = $files[$key2];
                if(
                @file_put_contents(dirname(__DIR__).'/download/'.$file2['file'], @base64_decode($result2[$bachKey]))
                ) {
                  if(dirname(__DIR__).'/download/'.$file2['file'] === $file2['md5sum']){
                    unset($files[$key2]);
                  }
                }
              }
            }
            unset($result2);
          }
          unset($batches[$keyToBatch[$k]]);
        }
        if(is_file($_file) && md5_file($_file)==$file['md5sum']) {
          unset($files[$k]);
          continue;
        }
        $result = $this->Request($file['typ'],$parameter);
        $output = @base64_decode($result);
        if(strlen($output) > 0 && is_dir($_file)) {
          $this->removeEmptyFolder($_file);
        }
        if(@file_put_contents($_file, $output)) {
          if(md5_file($_file)==$file['md5sum']) {
            unset($files[$k]);
          }
          else {
            $files[$k]['error'] = 'md5 failed';
          }
        }
        else{
          $files[$k]['error'] = 'file_put_contents ' .$_file. ' failed '.$file['typ'].' ' .json_encode($parameter);
        }
      }
      if($maxtime > 0 && microtime(true) - $startzeit > $maxtime) {
        break;
      }
    }
    if(empty($files)) {
      return array('todownload'=>null);
    }
    foreach($files as $k => $file) {
      if(substr($file['md5sum'],0,3) !== 'DEL'){
        $data[] = $file;
      }
    }
    return array('todownload'=>$data);
  }

  /**
   * @return int[]|string|string[]
   */
  public function downloadZips()
  {
    @clearstatcache();
    if(!function_exists('system')) {
      return ['zip' => 'system not found'];
    }
    $this->app->erp->setMaintainance(true);
    $parameter['version']=@$this->conf['version'];
    $parameter['versionname']=@$this->conf['versionname'];

    if($parameter['versionname'] !== 'ent_masterkey4_20.3') {
      return ['zip' => 'not ent_masterkey4_20.3'];
    }
    $parameter['withsize'] = 1;
    if(!is_dir(dirname(__DIR__).'/download/')) {
      if(!@mkdir(dirname(__DIR__).'/download/') && !is_dir(dirname(__DIR__).'/download/')) {
        $this->app->erp->setMaintainance(false);
        return 'ERROR: Downloadverzeichnis konnte nicht erstellt werden';
      }
    }
    $ret = ['zip' => 0];
    foreach([
              'ent_masterkey4_20.3_4_wo_userdata.zip' => '',
              'ent_masterkey4_20.3_4_vendor.zip' => '/vendor',
              'ent_masterkey4_20.3_4_www.zip' => '/zip',
            ] as $file => $subfolder
    ) {
      $parameter['file'] = $file;
      if(file_put_contents(
        dirname(__DIR__) . '/download/' . $file,
        $this->Request('getversionzip', $parameter)
      )) {
        if(
          !is_dir(dirname(__DIR__).'/download' . $subfolder)
          && !@mkdir(dirname(__DIR__).'/download/' . $subfolder)
          && !is_dir(dirname(__DIR__).'/download/' . $subfolder)
        ) {
          continue;
        }
        system(
          'cd '.dirname(__DIR__).'/download'
          .' && unzip '.$file.' -d '
          .dirname(__DIR__).'/download'.$subfolder
        );
        unlink(dirname(__DIR__).'/download/' . $subfolder);
        $ret['zip']++;
      }
      else {
        $ret['zip_error'][] = 'coudl not save '.$file;
      }
    }

    return $ret;
  }

  /**
   * @param bool $updatefiles
   *
   * @return array|mixed|string
   */
  public function CheckFiles($updatefiles = false)
  {
    @clearstatcache();
    $this->app->erp->setMaintainance(true);
    $parameter['version']=@$this->conf['version'];
    $parameter['versionname']=@$this->conf['versionname'];
    $parameter['withsize'] = 1;
    
    if(!is_dir(dirname(__DIR__).'/download/')) {
      if(!@mkdir(dirname(__DIR__).'/download/') && !is_dir(dirname(__DIR__).'/download/')) {
        $this->app->erp->setMaintainance(false);
        return 'ERROR: Downloadverzeichnis konnte nicht erstellt werden';
      }
    }
    $tmpfile = md5(microtime(true));
    if(!($fh = fopen(dirname(__DIR__).'/download/'.$tmpfile,'w'))) {
      $this->app->erp->setMaintainance(false);
      return 'ERROR: Downloadverzeichnis hat keine Schreibrechte';
    }
    fclose($fh);
    $eigenguser = fileowner(dirname(__DIR__).'/download/'.$tmpfile);
    $eigengroup = filegroup(dirname(__DIR__).'/download/'.$tmpfile);
    @unlink(dirname(__DIR__).'/download/'.$tmpfile);
    $_result = $this->Request('md5list', $parameter);
    $maxRetries = 5;
    while(empty($_result) && $maxRetries > 0) {
      $maxRetries--;
      usleep(2000000);
      $_result = $this->Request('md5list', $parameter);
    }
    if(isset($this->errormsg) && $this->errormsg) {
      $this->app->erp->setMaintainance(false);
      return 'ERROR: '.$this->errormsg;
    }
    if($_result==='ERROR') {
      $this->app->erp->setMaintainance(false);
      return 'ERROR FROM SERVER (Perhaps a wrong license?)';
    }
    $_result2 = '';
    $_result3 = '';
    if(!$updatefiles){
      $_result2 = $this->Request('md5listmodules', $parameter);
      if(empty($_result2) && (!empty($this->http_code) && strpos($this->http_code,'5') === 0)){
        usleep(1000000);
        $_result2 =  $this->Request('md5listmodules', $parameter);
      }
      if($_result2 === 'ERROR'){
        $this->app->erp->setMaintainance(false);
        return "ERROR FROM SERVER (Perhaps a wrong license?)";
      }
      $_result3 = $this->Request('md5listcustom', $parameter);
      if(empty($_result3)){
        usleep(2000000);
        $_result3 =  $this->Request('md5listcustom', $parameter);
      }
      if($_result3 === 'ERROR'){
        $this->app->erp->setMaintainance(false);
        return "ERROR FROM SERVER (Perhaps a wrong license?)";
      }
    }
    $result = '';
    $result2 = '';
    $result3 = '';
    $resulta = explode(';',$_result);
    $resulta2 = explode(';',$_result2);
    $resulta3 = explode(';',$_result3);
    unset($_result, $_result2, $_result3);

    if($resulta3) {
      foreach($resulta3 as $r) {
        if($r)
        {
          $result3.= 'getfilecustom:'.$r.';';
          $ra = explode(':',$r);
          $dats[] = $ra[0];
        }
      }
      unset($resulta3);
    }
    if($resulta2){
      foreach($resulta2 as $r) {
        if($r) {
          $ra = explode(':',$r);
          if(!isset($dats) || !in_array($ra[0], $dats)) {
            $result2.= 'getfilemodules:'.$r.';';
            $dats[] = $ra[0];
          }
        }
      }
      unset($resulta2);
    }
    if($resulta) {
      foreach($resulta as $r) {
        if($r) {
          $ra = explode(':',$r);
          if(!isset($dats) || !in_array($ra[0], $dats)) {
            $result.= 'getfile:'.$r.';';
          }
        }
      }
      unset($resulta);
    }

    $result .= $result2.$result3;
    unset($result2, $result3, $dats);
    
    //$rows = explode(";",$result);
		$rows = explode(';',$result);
    $res['result'] = $result;
		$res['parameter'] = $parameter;
    $downloadind = 0;
    $copyind = 0;
		if(count($rows)>0) {
			foreach($rows as $value) {
				unset($single_row);
				$single_row = explode(':',$value);
        if(!(count($single_row)>=3 && strlen($single_row[0])>4 && strlen($single_row[2])>3)) {
          continue;
        }
        $typ = $single_row[0];
        $file = $single_row[1];
        $file_lokal = dirname(__DIR__).'/'.($file);
        $md5sum = $single_row[2];
        $size = isset($single_row[3])?$single_row[3]:false;

        $parameter['file']=$file;
        $parameter['md5sum']=$md5sum;

        if($file==='./upgradesystemclient.php') {
          continue;
        }
        if(
          (!$updatefiles && ($file==="./www/update.php" ||
              $file==="./www/update.tpl" ||
              $file==="./www/updatelogin.tpl" ||
              $file === './www/jquery-update.js' ||
              $file === './www/jquery-ui-update.js' ||
              $file === 'jquery-ui.min.css'))
          || ($updatefiles && ($file!=="./www/update.php" &&
              $file!=="./www/update.tpl" &&
              $file!=="./www/updatelogin.tpl" &&
              $file !== './www/jquery-update.js' &&
              $file !== './www/jquery-ui-update.js' &&
              $file !== 'jquery-ui.min.css'))

        ){
          continue;
        }

        $bla[] = $file_lokal;
        if(is_file($file_lokal)){
          if(substr($md5sum,0,3) === 'DEL'){
            if($this->CheckVersandZahlungsweise($file_lokal)) {
              @unlink($file_lokal);
            }
            continue;
          }
          if(md5_file($file_lokal)==$md5sum){
            continue;
          }

          $fileowner = fileowner($file_lokal);
          $filegroup = filegroup($file_lokal);
          $perms = fileperms($file_lokal);
          $o = ($perms & 0x0080);
          $g = ($perms & 0x0010);
          $a = ($perms & 0x0002);
          // pruefe ob datei angelegt werden kann, wenn das passt ist eh alles gut
          if(touch(dirname(__DIR__).'/download/chkrights') && file_exists(dirname(__DIR__).'/download/chkrights')) {
            @unlink(dirname(__DIR__).'/download/chkrights');
          }
          else if($eigenguser && $eigengroup){
            if($fileowner != $eigenguser){
              if($filegroup != $eigengroup){
                if(!$a){
                  return array('error'=>'ERROR Fehlende Schreibrechte in '.$file_lokal);
                }
              }
              else{
                if(!$g) {
                  return array('error'=>'ERROR Fehlende Schreibrechte in '.$file_lokal);
                }
              }
            }
            else {
              if(!$o) {
                  return array('error'=>'ERROR Fehlende Schreibrechte in '.$file_lokal);
              }
            }
          }
          $bla[] = array(
            'fileowner'=>$fileowner,
            'filegroup'=>$filegroup,
            'perms'=>$perms,
            'o'=>$o,
            'g'=>$g,
            'a'=>$a,
          );
          if(is_file(dirname(__DIR__).'/download/'.$file)){
            if(md5_file(dirname(__DIR__).'/download/'.$file)!=$md5sum){
              $res['download'][$downloadind] = array('typ'=>$typ,'file'=>$file,'md5sum'=>$md5sum,'size'=>$size);
              $downloadexists[$typ][$file] = $downloadind;
              $downloadind++;
            }
            else{
              $res['copy'][$copyind] = array('typ'=>$typ,'file'=>$file,'md5sum'=>$md5sum,'size'=>$size);
              $copyexists[$typ][$file] = $copyind;
              $copyind++;
            }
          }
          else{
            $res['download'][$downloadind] = array('typ'=>$typ,'file'=>$file,'md5sum'=>$md5sum,'size'=>$size);
            $downloadexists[$typ][$file] = $downloadind;
            $downloadind++;
          }
        }
        else if($file!='') {
          if(substr($md5sum,0,3) === 'DEL') {
            continue;
          }
          if(is_file(dirname(__DIR__).'/download/'.$file)) {
            if(md5_file(dirname(__DIR__).'/download/'.$file)!=$md5sum) {
              $fileowner = fileowner(dirname(__DIR__).'/download/'.ltrim($file,'.'));
              $filegroup = filegroup(dirname(__DIR__).'/download/'.ltrim($file,'.'));
              $perms = fileperms(dirname(__DIR__).'/download/'.ltrim($file,'.'));
              $o = ($perms & 0x0080);
              $g = ($perms & 0x0010);
              $a = ($perms & 0x0002);

              // pruefe ob datei angelegt werden kann, wenn das passt ist eh alles gut
              if(touch(dirname(__DIR__).'/download/chkrights')) {
                unlink(dirname(__DIR__).'/download/chkrights');
              }
              else if($eigenguser && $eigengroup) {
                if($fileowner != $eigenguser) {
                  if($filegroup != $eigengroup) {
                    if(!$a) {
                      return array('error'=>'ERROR Fehlende Schreibrechte im Downloadordner');
                    }
                  }
                  else{
                    if(!$g) {
                      return array('error'=>'ERROR Fehlende Schreibrechte im Downloadordner');
                    }
                  }
                }
                else{
                  if(!$o) {
                    return array('error'=>'ERROR Fehlende Schreibrechte im Downloadordner');
                  }
                }
              }

              $res['download'][$downloadind] = array('typ'=>$typ,'file'=>$file,'md5sum'=>$md5sum,'size'=>$size);
              $downloadexists[$typ][$file] = $downloadind;
              $downloadind++;
            }
            else{
              $res['copy'][$copyind] = array('typ'=>$typ,'file'=>$file,'md5sum'=>$md5sum,'size'=>$size);
              $copyexists[$typ][$file] = $copyind;
              $copyind++;
            }
          }
          else {
            $res['download'][$downloadind] = array('typ'=>$typ,'file'=>$file,'md5sum'=>$md5sum,'size'=>$size);
            $downloadexists[$typ][$file] = $downloadind;
            $downloadind++;
          }
        }
			}
		}
    if(!empty($res['download']) && count($res['download']) > 0) {
      foreach($res['download'] as $key => $val) {
        if(isset($val['md5sum']) && substr($val['md5sum'],0,3) === 'DEL') {
          unset($res['download'][$key]);
        }
      }
    }

    return $this->CheckRights($res, $eigenguser, $eigengroup);
  }

  protected function CheckFileFolder($file, $eigenguser, $eigengroup)
  {
    if(is_file($file)) {
      if($handle = @fopen($file,'a+')) {
        fclose($handle);
        return false;
      }
      $fileowner = fileowner($file);
      if($fileowner !== $eigenguser) {
        if(@chown($file,$eigenguser) && ($handle = @fopen($file,'a+'))) {
          fclose($handle);
          return false;
        }
      }
      $perms = fileperms($file);
      $filegroup = filegroup($file);
      if($fileowner === $eigenguser) {
        if(@chmod($file, $perms | 0600)) {
          return false;
        }
      }
      if($filegroup === $eigengroup) {
        if(@chmod($file, $perms | 0060)) {
          return false;
        }
      }
      if(@chown($file,$perms | 0006)) {
        return false;
      }
      return true;
    }
    if(!is_dir($file)) {
      return false;
    }

    if(is_file($file.'/chkrights')) {
      @unlink($file.'/chkrights');
    }
    if(!is_file($file.'/chkrights') && @touch($file.'/chkrights')){
      if(is_file($file.'/chkrights')){
        @unlink($file . '/chkrights');
        return false;
      }
      return true;
    }
    $fileowner = fileowner($file);
    if($fileowner !== $eigenguser) {
      if(chown($file,$eigenguser) && @touch($file.'/chkrights')) {
        @unlink($file.'/chkrights');
        return false;
      }
    }
    $perms = fileperms($file);
    $filegroup = filegroup($file);
    if($fileowner === $eigenguser) {
      if(@chmod($file, $perms | 0700) && @touch($file.'/chkrights')) {
        @unlink($file.'/chkrights');
        return false;
      }
    }
    if($filegroup === $eigengroup) {
      if(@chmod($file, $perms | 0070) && @touch($file.'/chkrights')) {
        @unlink($file.'/chkrights');
        return false;
      }
    }
    if(@chown($file,$perms | 0007) && @touch($file.'/chkrights')) {
      @unlink($file.'/chkrights');
      return false;
    }
    return true;
  }

  protected function CheckRights($res, $eigenguser, $eigengroup)
  {
    $foldertocheck = [];
    if(!empty($res['download'])) {
      foreach($res['download'] as $k => $v) {
        $file = ltrim(ltrim($v['file'],'.'),'/');
        if($file === '.') {
          continue;
        }
        if($this->CheckFileFolder(dirname(__DIR__).'/'.$file, $eigenguser, $eigengroup)) {
          $res['FileError'][] = dirname(__DIR__).'/'.$file;
        }
        if($this->CheckFileFolder(dirname(__DIR__).'/download/'.$file, $eigenguser, $eigengroup)) {
          $res['FileError'][] = dirname(__DIR__).'/download/'.$file;
        }
        $dfile = dirname($file);
        if($dfile === '.') {
          $folder = dirname(__DIR__);
        }
        else{
          $folder = dirname(__DIR__) . '/' . $dfile;
        }
        $foldertocheck[substr_count($folder,'/')][$folder] = true;
        if($dfile === '.') {
          $folder = dirname(__DIR__). '/download';
        }
        else{
          $folder = dirname(__DIR__) . '/download/' . $dfile;
        }
        $foldertocheck[substr_count($folder,'/')][$folder] = true;
      }
    }
    if(!empty($res['copy'])) {
      foreach($res['copy'] as $k => $v) {
        $file = ltrim(ltrim($v['file'],'.'),'/');
        if($file === '.') {
          continue;
        }
        if($this->CheckFileFolder(dirname(__DIR__).'/'.$file, $eigenguser, $eigengroup)) {
          $res['FileError'][] = dirname(__DIR__).'/'.$file;
        }
        $dfile = dirname($file);
        if($dfile === '.') {
          $folder = dirname(__DIR__);
        }
        else {
          $folder = dirname(__DIR__) . '/' . $dfile;
        }
        $foldertocheck[substr_count($folder,'/')][$folder] = true;
      }
    }
    if(!empty($foldertocheck)) {
      foreach($foldertocheck as $lvl => $folderarr) {
        foreach($folderarr as $k => $v) {
          if($this->CheckFileFolder($k, $eigenguser, $eigengroup)) {
            $res['FolderError'][] = $k;
          }
        }
      }
    }
    return $res;
  }
  
	function CheckUpdate()
	{
    $parameter['version']=@$this->conf['version'];
		$result = $this->Request('md5list',$parameter);
		
		if($result==='ERROR') {
		  echo "Updates: ERROR FROM SERVER (Perhaps a wrong license?)\n";
		  return;
		}

		$rows = explode(";",$result);
		
		if(count($rows)>0)
		{
			foreach($rows as $value)
			{
				unset($single_row);
				$single_row = explode(":",$value);
				
				if(count($single_row)>=2 && strlen($single_row[0])>3 && strlen($single_row[1])>3)
				{
          $file = $single_row[0];
          $md5sum = $single_row[1];
          if(substr($md5sum,0,3) === 'DEL')continue;
          $parameter['file']=$file;
          $parameter['md5sum']=$md5sum;
          
          if($file==='./upgradesystemclient.php')
          {
          
          }	
          else if(is_file($file))
          {
            // pruefe md5sum
            if(md5_file($file)!=$md5sum)
            {
              // wenn update dann UPD_
              echo "update <- $file\n";
              $result = $this->Request("getfile",$parameter);
              $output =  (base64_decode($result));
            //$output = preg_replace('/[^(\x22-\x7F)\x0A]*/','', $output);
              file_put_contents($file."UPD", $output);
              /*
              $fp = fopen($file."UPD","wb+");
              fwrite($fp,base64_decode($result));
              fclose($fp);
              */
              // pruefsuemme neu berechnen wenn passt umbenennen und ins archiv
              echo md5_file($file."UPD");
              echo "-".$md5sum."\n";
              if(md5_file($file."UPD")==$md5sum)
              {
                echo "update ok $file\n";
                rename($file."UPD",$file);
              }
            }
          } else if($file!="") {
            echo "datei <- $file\n";
            // pruefe ob es verzeichnis gibt
            $verzeichnis = dirname($file);
            if(!is_dir($verzeichnis))
            {
              echo "verzeichnis <- $verzeichnis\n";
              mkdir($verzeichnis,0777,true);	
            }
            $result = $this->Request("getfile",$parameter);
            $output =  base64_decode($result);
            //$output = iconv("UTF-8","ISO-8859-1//IGNORE",$output);
            //$output = iconv("ISO-8859-1","UTF-8",$output);
            //$output = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $output);
            file_put_contents($file."NEW", $output);
            /*$fp = fopen($file."NEW","wb+");
            fwrite($fp,base64_decode($result));
            fclose($fp);
            */
            if(md5_file($file."NEW")==$md5sum)
            {
              echo "datei ok $file\n";
              rename($file."NEW",$file);
            }
          }
				}
			}
		}
	}
	
	
	function CheckUpdateModules()
	{
		//$this->dir_rekursiv("./");
		//$parameter['md5sums'] = $this->localmd5sums;
		//shell_exec('find ./ -exec md5sum "{}" \;');
		
		
    $parameter['version']=@$this->conf['version'];
		$result = $this->Request('md5listmodules',$parameter);
		
		if($result==='ERROR') {
		  echo "Modules: ERROR FROM SERVER (Perhaps a wrong license?)\n"; return;
		}

		$rows = explode(";",$result);
		
		if(count($rows)>0)
		{
			foreach($rows as $value)
			{
				unset($single_row);
				$single_row = explode(":",$value);
				
				if(count($single_row)>=2 && strlen($single_row[0])>3 && strlen($single_row[1])>3)
				{
					
          $file = $single_row[0];
          $md5sum = $single_row[1];
          if(substr($md5sum,0,3) === 'DEL') {
            continue;
          }
          $parameter['file']=$file;
          $parameter['md5sum']=$md5sum;

          if($file==="./upgradesystemclient.php")
          {

          }
          else if(is_file($file))
          {
            // pruefe md5sum
            if(md5_file($file)!=$md5sum)
            {
              // wenn update dann UPD_
              echo "update (M) <- $file\n";
              $result = $this->Request("getfilemodules",$parameter);
              $output =  (base64_decode($result));
            //$output = preg_replace('/[^(\x22-\x7F)\x0A]*/','', $output);
              file_put_contents($file."UPD", $output);
              /*
              $fp = fopen($file."UPD","wb+");
              fwrite($fp,base64_decode($result));
              fclose($fp);
              */
              // pruefsuemme neu berechnen wenn passt umbenennen und ins archiv
              echo md5_file($file."UPD");
              echo "-".$md5sum."\n";
              if(md5_file($file."UPD")==$md5sum)
              {
                echo "update (M) ok $file\n";
                rename($file."UPD",$file);
              }
            }
          } else if($file!='') {
            echo "datei (M) <- $file\n";
            // pruefe ob es verzeichnis gibt
            $verzeichnis = dirname($file);
            if(!is_dir($verzeichnis))
            {
              echo "verzeichnis (M) <- $verzeichnis\n";
              mkdir($verzeichnis,0777,true);
            }
            $result = $this->Request("getfilemodules",$parameter);
            $output =  base64_decode($result);
            //$output = iconv("UTF-8","ISO-8859-1//IGNORE",$output);
            //$output = iconv("ISO-8859-1","UTF-8",$output);
            //$output = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $output);
            file_put_contents($file."NEW", $output);
            /*$fp = fopen($file."NEW","wb+");
            fwrite($fp,base64_decode($result));
            fclose($fp);
            */
            if(md5_file($file."NEW")==$md5sum)
            {
              echo "datei (M) ok $file\n";
              rename($file."NEW",$file);
            }
          }
				}
			}
		}
		
	}

	function CheckUpdateCustom()
	{
	  $parameter['version']=@$this->conf['version'];
		$result = $this->Request("md5listcustom",$parameter);
		
		if($result==='ERROR') {
		  echo "Custom: ERROR FROM SERVER (Perhaps a wrong license?)\n"; return;
		}

		$rows = explode(";",$result);
		
		if(count($rows)>0)
		{
			foreach($rows as $value)
			{
				unset($single_row);
				$single_row = explode(":",$value);
				
				if(count($single_row)>=2 && strlen($single_row[0])>3 && strlen($single_row[1])>3)
				{
					
          $file = $single_row[0];
          $md5sum = $single_row[1];

          $parameter['file']=$file;
          $parameter['md5sum']=$md5sum;
          if(substr($md5sum,0,3) === 'DEL') {
            continue;
          }
          if($file==='./upgradesystemclient.php')
          {

          }
          else if(is_file($file))
          {
            // pruefe md5sum
            if(md5_file($file)!=$md5sum)
            {
              // wenn update dann UPD_
              echo "update (C) <- $file\n";
              $result = $this->Request("getfilecustom",$parameter);

              $output =  (base64_decode($result));
            //$output = preg_replace('/[^(\x22-\x7F)\x0A]*/','', $output);
              file_put_contents($file."UPD", $output);
              /*
              $fp = fopen($file."UPD","wb+");
              fwrite($fp,base64_decode($result));
              fclose($fp);
              */
              // pruefsuemme neu berechnen wenn passt umbenennen und ins archiv
              echo md5_file($file."UPD");
              echo "-".$md5sum."\n";
              if(md5_file($file."UPD")==$md5sum)
              {
                echo "update (C) ok $file\n";
                rename($file."UPD",$file);
              }
            }
          } else if($file!="") {
            echo "datei (C) <- $file\n";
            // pruefe ob es verzeichnis gibt
            $verzeichnis = dirname($file);
            if(!is_dir($verzeichnis))
            {
              echo "verzeichnis (C) <- $verzeichnis\n";
              mkdir($verzeichnis,0777,true);
            }
            $result = $this->Request("getfilecustom",$parameter);
            $output =  base64_decode($result);
            //$output = iconv("UTF-8","ISO-8859-1//IGNORE",$output);
            //$output = iconv("ISO-8859-1","UTF-8",$output);
            //$output = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $output);
            file_put_contents($file."NEW", $output);
            /*$fp = fopen($file."NEW","wb+");
            fwrite($fp,base64_decode($result));
            fclose($fp);
            */
            if(md5_file($file."NEW")==$md5sum)
            {
              echo "datei (C) ok $file\n";
              rename($file."NEW",$file);
            }
          }
				}
			}
		}
	}
	

	function DownloadUpdate()
	{
	
	
	}
	
	function CheckDownloadedUpdate()
	{
	
	
	}
	
	function ExecuteUpdate()
	{
	
	}

	
	function Request($command,$parameter)
	{
    $erp = $this->erp;
		
    $auth['serial']=trim($erp->Firmendaten('lizenz'));//$this->conf['serial'];
    $auth['authkey']=trim($erp->Firmendaten('schluessel'));//$this->conf['authkey'];
    if(empty($auth['serial']) || empty($auth['authkey']))
    {
      $this->errormsg = 'Bitte tragen Sie die Lizenzdaten in den <a style="color:red;" target="_blank" href="index.php?module=firmendaten&action=edit#tabs-10">Grundeinstellungen</a> ein.';
      return '';
    }
    if(!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== '') {
      $auth['SERVER_NAME'] = $_SERVER['SERVER_NAME'];
    }
    elseif(!empty($_SERVER['HTTP_HOST'])) {
      $auth['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
    }
    else {
      $auth['SERVER_NAME'] = '';
    }
    $auth = base64_encode(json_encode($auth));

    $parameter = base64_encode(json_encode($parameter));

		$client = new UpdateHttpClient($this->conf['host'],$this->conf['port']);
		$client->post('/upgradesystem.php', [
		    'authjson' => $auth,
        'parameterjson'=>$parameter,
        'command'=>(String)$command ,
        'withdel' => 1
	    ]
    );
		$pageContents = $client->getContent();
    if(!empty($client->errormsg)){
      $this->errormsg = $client->errormsg;
    }
    $this->http_code = (string)$client->getStatus();

		return $pageContents;
	}
	
  function dir_rekursiv($verzeichnis)
  { 
    $handle =  opendir($verzeichnis);

    while ($datei = readdir($handle))
    {   
      if ($datei !== '.' && $datei !== '..')
      {   
        if (is_dir($verzeichnis.$datei)) // Wenn Verzeichniseintrag ein Verzeichnis ist 
        {   
          // Erneuter Funktionsaufruf, um das aktuelle Verzeichnis auszulesen
          $this->dir_rekursiv($verzeichnis.$datei.'/');
        }
        else
        {   
          // Wenn Verzeichnis-Eintrag eine Datei ist, diese ausgeben
          $this->localmd5sums[$verzeichnis.$datei] = md5_file($verzeichnis.$datei);
        }
      }
    }
    closedir($handle);
	}
}


/* Version 0.9, 6th April 2003 - Simon Willison ( http://simon.incutio.com/ )
   Manual: http://scripts.incutio.com/httpclient/
*/

class UpdateHttpClient {
    // Request vars
    var $host;
    var $port;
    var $path;
    var $method;
    var $postdata = '';
    var $cookies = array();
    var $referer;
    var $accept = 'text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*';
    var $accept_encoding = 'gzip';
    var $accept_language = 'en-us';
    var $user_agent = 'Incutio HttpClient v0.9';
    // Options
    var $timeout = 20;
    var $use_gzip = true;
    var $persist_cookies = true;  // If true, received cookies are placed in the $this->cookies array ready for the next request
                                  // Note: This currently ignores the cookie path (and time) completely. Time is not important, 
                                  //       but path could possibly lead to security problems.
    var $persist_referers = true; // For each request, sends path of last request as referer
    var $debug = false;
    var $handle_redirects = true; // Auaomtically redirect if Location or URI header is found
    var $max_redirects = 5;
    var $headers_only = false;    // If true, stops receiving once headers have been read.
    // Basic authorization variables
    var $username;
    var $password;
    // Response vars
    var $status;
    var $headers = array();
    var $content = '';
    var $errormsg;
    // Tracker variables
    var $redirect_count = 0;
    var $cookie_host = '';
    function __construct($host, $port=80) {
        $this->host = $host;
        $this->port = $port;
    }
    function get($path, $data = false) {
        $this->path = $path;
        $this->method = 'GET';
        if ($data) {
            $this->path .= '?'.$this->buildQueryString($data);
        }
        return $this->doRequest();
    }
    function post($path, $data) {
        $this->path = $path;
        $this->method = 'POST';
        $this->postdata = $this->buildQueryString($data);
    	return $this->doRequest();
    }
    function buildQueryString($data) {
        $querystring = '';
        if (is_array($data)) {
            // Change data in to postable data
    		foreach ($data as $key => $val) {
    			if (is_array($val)) {
    				foreach ($val as $val2) {
    					$querystring .= urlencode($key).'='.urlencode($val2).'&';
    				}
    			} else {
    				$querystring .= urlencode($key).'='.urlencode($val).'&';
    			}
    		}
    		$querystring = substr($querystring, 0, -1); // Eliminate unnecessary &
    	} else {
    	    $querystring = $data;
    	}
    	return $querystring;
    }
    function doRequest() {
        // Performs the actual HTTP request, returning true or false depending on outcome

  if(!@fsockopen('ssl://'.$this->host, $this->port, $errno, $errstr, $this->timeout) && $this->port==443)
  {
    $this->port=80;
  }

  if($this->port==443){
    $url = 'ssl://' . $this->host;
  }
  else{
    $url = $this->host;
  }

		if (!$fp = @fsockopen($url, $this->port, $errno, $errstr, $this->timeout)) {
		    // Set error message
            switch($errno) {
				case -3:
					$this->errormsg = 'Socket creation failed (-3)';
          $this->errormsg .= ' '.$errstr;
          $this->debug($this->errormsg);
					break;
				case -4:
					$this->errormsg = 'DNS lookup failure (-4)';
          $this->errormsg .= ' '.$errstr;
          $this->debug($this->errormsg);
          break;
				case -5:
					$this->errormsg = 'Connection refused or timed out (-5)';
          $this->errormsg .= ' '.$errstr;
          $this->debug($this->errormsg);
          break;
				default:
					$this->errormsg = 'Connection failed ('.$errno.')';
			    $this->errormsg .= ' '.$errstr;
			    $this->debug($this->errormsg);
			}
			return false;
        }
        stream_set_timeout($fp, $this->timeout);
        $request = $this->buildRequest();
        $this->debug('Request', $request);
        fwrite($fp, $request);
    	// Reset all the variables that should not persist between requests
    	$this->headers = array();
    	$this->content = '';
    	$this->errormsg = '';
    	// Set a couple of flags
    	$inHeaders = true;
    	$atStart = true;
    	// Now start reading back the response
    	while (!feof($fp)) {
    	    $line = fgets($fp, 4096);
    	    if ($atStart) {
    	        // Deal with first line of returned data
    	        $atStart = false;
    	        if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $line, $m)) {
    	            $this->errormsg = "Status code line invalid: ".htmlentities($line);
    	            $this->debug($this->errormsg);
    	            //return false;
    	        }
    	        $http_version = $m[1]; // not used
    	        $this->status = $m[2];
    	        $status_string = $m[3]; // not used
    	        $this->debug(trim($line));
    	        continue;
    	    }
    	    if ($inHeaders) {
    	        if (trim($line) == '') {
    	            $inHeaders = false;
    	            $this->debug('Received Headers', $this->headers);
    	            if ($this->headers_only) {
    	                break; // Skip the rest of the input
    	            }
    	            continue;
    	        }
    	        if (!preg_match('/([^:]+):\\s*(.*)/', $line, $m)) {
    	            // Skip to the next header
    	            continue;
    	        }
    	        $key = strtolower(trim($m[1]));
    	        $val = trim($m[2]);
    	        // Deal with the possibility of multiple headers of same name
    	        if (isset($this->headers[$key])) {
    	            if (is_array($this->headers[$key])) {
    	                $this->headers[$key][] = $val;
    	            } else {
    	                $this->headers[$key] = array($this->headers[$key], $val);
    	            }
    	        } else {
    	            $this->headers[$key] = $val;
    	        }
    	        continue;
    	    }
    	    // We're not in the headers, so append the line to the contents
    	    $this->content .= $line;
        }
        fclose($fp);
        
        // If data is compressed, uncompress it
        if (isset($this->headers['content-encoding']) && $this->headers['content-encoding'] == 'gzip') {
            $this->debug('Content is gzip encoded, unzipping it');
            $this->content = substr($this->content, 10); // See http://www.php.net/manual/en/function.gzencode.php
            $this->content = gzinflate($this->content);
        }
        // If $persist_cookies, deal with any cookies
        if ($this->persist_cookies && isset($this->headers['set-cookie']) && $this->host == $this->cookie_host) {
            $cookies = $this->headers['set-cookie'];
            if (!is_array($cookies)) {
                $cookies = array($cookies);
            }
            foreach ($cookies as $cookie) {
                if (preg_match('/([^=]+)=([^;]+);/', $cookie, $m)) {
                    $this->cookies[$m[1]] = $m[2];
                }
            }
            // Record domain of cookies for security reasons
            $this->cookie_host = $this->host;
        }
        // If $persist_referers, set the referer ready for the next request
        if ($this->persist_referers) {
            $this->debug('Persisting referer: '.$this->getRequestURL());
            $this->referer = $this->getRequestURL();
        }
        // Finally, if handle_redirects and a redirect is sent, do that
        if ($this->handle_redirects) {
            if (++$this->redirect_count >= $this->max_redirects) {
                $this->errormsg = 'Verbindung konnte nicht aufgebaut werden. Bitte wenden Sie sich an Ihre IT. Eventuell sind SSL-Zertifikate nicht vorhanden bzw. abgelaufen';
                $this->debug($this->errormsg);
                $this->redirect_count = 0;
                return false;
            }
            $location = isset($this->headers['location']) ? $this->headers['location'] : '';
            $uri = isset($this->headers['uri']) ? $this->headers['uri'] : '';
            if ($location || $uri) {
                $url = parse_url($location.$uri);
                // This will FAIL if redirect is to a different site
                return $this->get($url['path']);
            }
        }
        return true;
    }
    function buildRequest() {
        $headers = array();
        $headers[] = "{$this->method} {$this->path} HTTP/1.0"; // Using 1.1 leads to all manner of problems, such as "chunked" encoding
        $headers[] = "Host: {$this->host}";
        $headers[] = "User-Agent: {$this->user_agent}";
        $headers[] = "Accept: {$this->accept}";
        if ($this->use_gzip) {
            $headers[] = "Accept-encoding: {$this->accept_encoding}";
        }
        $headers[] = "Accept-language: {$this->accept_language}";
        if ($this->referer) {
            $headers[] = "Referer: {$this->referer}";
        }
    	// Cookies
    	if ($this->cookies) {
    	    $cookie = 'Cookie: ';
    	    foreach ($this->cookies as $key => $value) {
    	        $cookie .= "$key=$value; ";
    	    }
    	    $headers[] = $cookie;
    	}
    	// Basic authentication
    	if ($this->username && $this->password) {
    	    $headers[] = 'Authorization: BASIC '.base64_encode($this->username.':'.$this->password);
    	}
    	// If this is a POST, set the content type and length
    	if ($this->postdata) {
    	    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    	    $headers[] = 'Content-Length: '.strlen($this->postdata);
    	}
    	$request = implode("\r\n", $headers)."\r\n\r\n".$this->postdata;
    	return $request;
    }
    function getStatus() {
        return $this->status;
    }
    function getContent() {
        return $this->content;
    }
    function getHeaders() {
        return $this->headers;
    }
    function getHeader($header) {
        $header = strtolower($header);
        if (isset($this->headers[$header])) {
            return $this->headers[$header];
        }
        return false;
    }
    function getError() {
        return $this->errormsg;
    }
    function getCookies() {
        return $this->cookies;
    }
    function getRequestURL() {
        $url = 'http://'.$this->host;
        if ($this->port != 80) {
            $url .= ':'.$this->port;
        }            
        $url .= $this->path;
        return $url;
    }
    // Setter methods
    function setUserAgent($string) {
        $this->user_agent = $string;
    }
    function setAuthorization($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    function setCookies($array) {
        $this->cookies = $array;
    }
    // Option setting methods
    function useGzip($boolean) {
        $this->use_gzip = $boolean;
    }
    function setPersistCookies($boolean) {
        $this->persist_cookies = $boolean;
    }
    function setPersistReferers($boolean) {
        $this->persist_referers = $boolean;
    }
    function setHandleRedirects($boolean) {
        $this->handle_redirects = $boolean;
    }
    function setMaxRedirects($num) {
        $this->max_redirects = $num;
    }
    function setHeadersOnly($boolean) {
        $this->headers_only = $boolean;
    }
    function setDebug($boolean) {
        $this->debug = $boolean;
    }
    // "Quick" static methods
    function quickGet($url) {
        $bits = parse_url($url);
        $host = $bits['host'];
        $port = isset($bits['port']) ? $bits['port'] : 80;
        $path = isset($bits['path']) ? $bits['path'] : '/';
        if (isset($bits['query'])) {
            $path .= '?'.$bits['query'];
        }
        $client = new UpdateHttpClient($host, $port);
        if (!$client->get($path)) {
            return false;
        }
        return $client->getContent();
    }
    function quickPost($url, $data) {
        $bits = parse_url($url);
        $host = $bits['host'];
        $port = isset($bits['port']) ? $bits['port'] : 80;
        $path = isset($bits['path']) ? $bits['path'] : '/';
        $client = new UpdateHttpClient($host, $port);
        if (!$client->post($path, $data)) {
            return false;
        }
        return $client->getContent();

    }
    function debug($msg, $object = false) {
        if ($this->debug) {
            print '<div style="border: 1px solid red; padding: 0.5em; margin: 0.5em;"><strong>HttpClient Debug:</strong> '.$msg;
            if ($object) {
                ob_start();
        	    print_r($object);
        	    $content = htmlentities(ob_get_contents());
        	    ob_end_clean();
        	    print '<pre>'.$content.'</pre>';
        	}
        	print '</div>';
        }
    }   
}




class UpdatePage 
{
  var $engine;
  function __construct(&$app)
  {
    $this->app = &$app;
    //$this->engine = &$engine;
  }

  /// load a themeset set
  function LoadTheme($theme)
  {
    //$this->app->Tpl->ReadTemplatesFromPath("themes/$theme/templates/");
    $this->app->Tpl->ReadTemplatesFromPath("themes/$theme/templates/");
  }

  /// show complete page
  function Show()
  {
    return $this->app->Tpl->FinalParse('update.tpl');
  }
}

class UpdateSession {

  // set check to true when user have permissions
  private $check = false;

  public $module;
  public $action;

  // application object
  public  $app;
  public $reason;


  function __construct() 
  {


  }


  function Check($appObj)
  {
    $this->app = $appObj;
    $this->check =  true;

    if(!$this->app->acl->CheckTimeOut()){
      $this->check = false;
      $this->reason = 'PLEASE_LOGIN';
    } else {
      //benutzer ist schon mal erfolgreich angemeldet
      if($this->app->User->GetType()==='admin'){
        $this->check =  true;
      } else {
        $this->reason = 'NO_PERMISSIONS';
        $this->check = false;
      }
    }
  }

  function GetCheck() {
    return $this->check;
  }

  function UserSessionCheck()
  {
    $this->check=false;
    $this->reason='PLEASE_LOGIN';
    //$this->reason="SESSION_TIMEOUT";
    return true;
  }


}


class UpdateWawiString 
{


  function __construct()
  {
  }

  function Convert($value,$input,$output)
  {
    if($input==''){
      return $value;
    }

    $array = $this->FindPercentValues($input);
    $regexp = $this->BuildRegExp($array);

    $elements =
      preg_split($regexp,$value,-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    // input und elements stimmmen ueberein

    $newout = $output;
    $i = 0;
    foreach($array as $key=>$v)
    {
      $newout = str_replace($key,$elements[$i],$newout);
      $i++;
    }
    return $newout;
  }


  function BuildRegExp($array)
  {

    $regexp = '/^';
    foreach($array as $value)
    {
      $value = str_replace('.','\.',$value);
      $value = str_replace('+','\+',$value);
      $value = str_replace('*','\*',$value);
      $value = str_replace('?','\?',$value);
      $regexp .= '(\S+)'.$value;
    }
    $regexp .= '/';

    return $regexp;
  }

  function FindPercentValues($pattern)
  {
    $hash = '';
    $collect = '';
    preg_match_all('/(?:(%[0-9]+)|.)/i', $pattern, $matches);
    $hash = '';
    $collect = '';

    $start = true;
    foreach($matches[1] as $key=>$value)
    {
      if($value==''){
        $collecting = true;
      }
      else
      {
        $collecting = false;
        $oldhash = $hash;
        $hash = $value;
      }

      if(!$collecting)
      {
      	if(!$start){
          $replace[$oldhash] = $collect;
        }
      	$collect='';
      }
      else{
        $collect .= $matches[0][$key];
      }
      $start = false;
    }
    $replace[$hash] = $collect;
    return $replace;
  }

  function encodeText($string)
  {
    $string = str_replace("\\r\\n","#BR#",$string);
    $string = str_replace("\n","#BR#",$string);
    $encoded = htmlspecialchars(stripslashes($string), ENT_QUOTES);
   
    return $encoded;
  }

 function decodeText($_str, $_form=true) 
 {
   if ($_form) {
     $_str      = str_replace("#BR#", "\r\n", $_str);
   }
   else {
     $_str      = str_replace("#BR#", "<br>", $_str);
   }
   return($_str);
 }

	function valid_utf8( $string )
	{
		return !((bool)preg_match('~\xF5\xF6\xF7\xF8\xF9\xFA\xFB\xFC\xFD\xFE\xFF\xC0\xC1~ms',$string));
	}

}
class UpdatephpWFAPI
{
  function __construct(&$app)
  {
    $this->app=&$app;
  }

  function ReBuildPageFrame()
  {
    $this->app->Tpl->ResetParser();
    $this->BuildPageFrame();
  }


  function BuildPageFrame()
  {
    $this->app->Tpl->ReadTemplatesFromPath("phpwf/defaulttemplates/");

    // build template tree
    $this->app->Page->LoadTheme($this->app->WFconf[defaulttheme]);


    // start acutally application instance
    $this->app->Tpl->ReadTemplatesFromPath("pages/content/_gen");
    $this->app->Tpl->ReadTemplatesFromPath("pages/content/");
  }


  function StartRequestedCommand()
  {
    $defaultpage = $this->app->WFconf['defaultpage'];
    $defaultpageaction = $this->app->WFconf['defaultpageaction'];
  
    $module = $this->app->Secure->GetGET('module','alpha'); 
    $action = $this->app->Secure->GetGET('action','alpha'); 
    
    if(!file_exists("pages/".$module.".php"))
      $module = $defaultpage;
   
    if($action=='') {
      $action = $defaultpageaction;
    }
    if(!$this->app->acl->Check($this->app->User->GetType(),$module,$action))
      return;


    // start module
    if(file_exists("pages/".$module.".php"))
    {
      include("pages/".$module.".php");
      //create dynamical an object
      $constr=strtoupper($module[0]).substr($module, 1);
      $myApp = new $constr($this->app);
    } 
    else 
    {
      echo $this->app->WFM->Error("Module <b>$module</b> doesn't exists in pages/");

    }
    $this->app->acl->CheckTimeOut();
  }

  /// mit dem "erstellen Formular" einfach bearbeiten liste + formular anzeigen
  function EasyTableList($tablename,$cols,$parsetarget,$pkname,$delmsg,$delmsgcol)
  {
    // show list

    // create html table
    $table = new HTMLTable("0","100%");
    $table->AddRowAsHeading($cols); 
      
    $all = $this->app->DB->SelectTable($tablename,$cols);

    $table->AddField($all); 

    $action = $this->app->Secure->GetGET("action","alpha");
    $module = $this->app->Secure->GetGET("module","alpha");

    $table->AddCompleteCol(0,
      "<a href=\"index.php?module=$module&action=$action&id=%col%\">bearbeiten</a>");
    
    $table->AddCompleteCol(0,
      "<a href=\"#\" onclick=\"str = confirm('{$delmsg}');
      if(str!='' & str!=null) 
      window.document.location.href='index.php?module=$module&action=$action&id=%col%&formaction=delete';\">
      loeschen</a>",$delmsgcol);
 
    $table->ChangingRowColors('#ffffff','#dddddd');
      
    $this->app->Tpl->Set($parsetarget,$table->Get()); 
  }

  function Message($msg,$parsetarget='MSGBOX')
  {
    $this->app->Tpl->Add('MSGBOXTEXT',$msg);
    $this->app->Tpl->Parse($parsetarget,"messagebox.tpl");
  }
  // emailvorlage aus db senden

  function EmailFromTemplate($template,$to,$values)
  {
    $betreff = $this->app->DB->Select("SELECT betreff 
      FROM emailvorlagen WHERE name='$template' LIMIT 1");

    $nachricht = $this->app->DB->Select("SELECT nachricht 
      FROM emailvorlagen WHERE name='$template' LIMIT 1");

    if(count($values) > 0)
    {
      foreach($values as $key=>$value)
      {
        $nachricht = str_replace("%".$key."%",$value,$nachricht);
        $betreff = str_replace("%".$key."%",$value,$betreff);
      }
    }
    
    $nachricht = str_replace('#BR#',"\n",$nachricht);
    mail($to,$betreff,$nachricht,"From: ActConnect Team <info@actconnect.de>");

  }
}
class UpdateSecure 
{
  var $GET;
  var $POST;


  function __construct(&$app){
    $this->app = &$app;
    // clear global variables, that everybody have to go over secure layer
    $this->GET = $_GET;
    //    $_GET="";
    $this->POST = $_POST;
    //   $_POST="";

    $this->AddRule('notempty','reg','.'); // at least one sign
    $this->AddRule('alpha','reg','[a-zA-Z]');
    $this->AddRule('digit','reg','[0-9]');
    $this->AddRule('space','reg','[ ]');
    $this->AddRule('specialchars','reg','[_-]');
    $this->AddRule('email','reg','^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$');
    $this->AddRule('datum','reg','([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})');

    $this->AddRule('username','glue','alpha+digit');
    $this->AddRule('password','glue','alpha+digit+specialchars');
  }


  function GetGET($name,$rule="",$maxlength="",$sqlcheckoff="")
  {
    return $this->Syntax(isset($this->GET[$name])?$this->GET[$name]:'',$rule,$maxlength,$sqlcheckoff);
  }

  function GetPOST($name,$rule="",$maxlength="",$sqlcheckoff="")
  {
    return $this->Syntax(isset($this->POST[$name])?$this->POST[$name]:'',$rule,$maxlength,$sqlcheckoff);
  }

  function GetPOSTForForms($name,$rule="",$maxlength="",$sqlcheckoff="")
  {
    return $this->SyntaxForForms($this->POST[$name],$rule,$maxlength,$sqlcheckoff);
  }



  function GetPOSTArray()
  {
    if(count($this->POST)>0)
    {
      foreach($this->POST as $key=>$value)
      {
        $key = $this->GetPOST($key,"alpha+digit+specialchars",20);
        $ret[$key]=$this->GetPOST($value);
      }	
    }
    if(isset($ret))return $ret;
  }

  function GetGETArray()
  {
    if(count($this->GET)>0)
    {
      foreach($this->GET as $key=>$value)
      {
        $key = $this->GetGET($key,"alpha+digit+specialchars",20);
        $ret[$key]=$this->GetGET($value);
      }	
    }
    if(isset($ret)) {
      return $ret;
    }
  }

  function stripallslashes($string) {

    while(strstr($string,'\\')) {
      $string = stripslashes($string);
    }
    return $string;
  } 

  function smartstripslashes($str) {
    $cd1 = substr_count($str, "\"");
    $cd2 = substr_count($str, "\\\"");
    $cs1 = substr_count($str, "'");
    $cs2 = substr_count($str, "\\'");
    $tmp = strtr($str, array("\\\"" => "", "\\'" => ""));
    $cb1 = substr_count($tmp, "\\");
    $cb2 = substr_count($tmp, "\\\\");
    if ($cd1 == $cd2 && $cs1 == $cs2 && $cb1 == 2 * $cb2) {
      return strtr($str, array("\\\"" => "\"", "\\'" => "'", "\\\\" => "\\"));
    }
    return $str;
  }

  function SyntaxForForms($value,$rule,$maxlength="",$sqlcheckoff="")
  {
    return $value;//mysqli_real_escape_string($this->app->DB->connection,$value);//mysqli_real_escape_string($value);
  }

  // check actual value with given rule
  function Syntax($value,$rule,$maxlength="",$sqlcheckoff="")
  {
    $value = str_replace("\xef\xbb\xbf","NONBLOCKINGZERO",$value);
    if(is_array($value))
    {
      return $value;
    }

    $value = $this->stripallslashes($value);
    $value = $this->smartstripslashes($value);

    $value = $this->app->erp->superentities($value);		

    if($rule=='' && $sqlcheckoff == '')
    {
      return mysqli_real_escape_string($this->app->DB->connection,$value);//mysqli_real_escape_string($value);
    }
    if($rule=='' && $sqlcheckoff != '')
    {
      return $value;
    }

    // build complete regexp

    // check if rule exists

    if($this->GetRegexp($rule)!=""){
      //$v = '/^['.$this->GetRegexp($rule).']+$/';
      $v = $this->GetRegexp($rule);
      if (preg_match_all('/'.$v.'/i', $value, $teffer) )
      {
        if($sqlcheckoff==""){
          return mysqli_real_escape_string($this->app->DB->connection, $value);//mysqli_real_escape_string($value);
        }
        return $value;
      }
      return '';
    }

    echo "<table border=\"1\" width=\"100%\" bgcolor=\"#FFB6C1\">
        <tr><td>Rule <b>$rule</b> doesn't exists!</td></tr></table>";
    return '';
  }


  function RuleCheck($value,$rule)
  {
    $v = $this->GetRegexp($rule);
    if (preg_match_all('/'.$v.'/i', $value, $teffer) ){
      return true;
    }

    return false;
  }

  function AddRule($name,$type,$rule)
  {
    // type: reg = regular expression
    // type: glue ( already exists rules copy to new e.g. number+digit)
    $this->rules[$name]=array('type'=>$type,'rule'=>$rule);
  }

  // get complete regexp by rule name
  function GetRegexp($rule)
  {
    $rules = explode("+",$rule);
    $ret = '';
    foreach($rules as $key)
    {
      // check if rule is last in glue string
      if($this->rules[$key]['type']==="glue")
      {
        $subrules = explode("+",$this->rules[$key]['rule']);
        if(count($subrules)>0)
        {
          foreach($subrules as $subkey)
          {
            $ret .= $this->GetRegexp($subkey);
          }
        }
      }
      elseif($this->rules[$key]['type']==="reg")
      {
        $ret .= $this->rules[$key]['rule'];
      }
    }
    if($ret=="")
      $ret = "none";
    return $ret;
  }

}
class UpdateAcl 
{
  /** @var UpdateApplication */
  public $app;
  public function __construct($app)
  {
    $this->app = $app;
    if(!empty($_COOKIE['DBSELECTED']))
    {
      $this->app->changeDbConf($_COOKIE['DBSELECTED']);
    }
  }


  function CheckTimeOut()
  {
    $this->session_id = session_id();

    if(isset($_COOKIE['CH42SESSION']) && $_COOKIE['CH42SESSION']!='')
    {
      $this->session_id = $_COOKIE["CH42SESSION"];
      $this->app->DB->Update("UPDATE useronline SET time=NOW(),login=1 WHERE sessionid='".$this->app->DB->real_escape_string($_COOKIE["CH42SESSION"])."' LIMIT 1");
    }

    // check if user is applied 
    // 	$this->app->DB->Delete("DELETE FROM useronline WHERE user_id='".$this->app->User->GetID()."' AND sessionid!='".$this->session_id."'");
    $sessid =  $this->app->DB->Select("SELECT sessionid FROM useronline,user WHERE
          login='1' AND sessionid='".$this->app->DB->real_escape_string($this->session_id)."' AND user.id=useronline.user_id AND user.activ='1' LIMIT 1");

    if($this->session_id == $sessid)
    { 
      // check if time is expired
      $time =  $this->app->DB->Select("SELECT UNIX_TIMESTAMP(time) FROM useronline,user WHERE
            login='1' AND sessionid='".$this->app->DB->real_escape_string($this->session_id)."' AND user.id=useronline.user_id AND user.activ='1' LIMIT 1");

      if((time()-$time) > $this->app->Conf->WFconf['logintimeout'])
      {
        if(!isset($_COOKIE['CH42SESSION']) || $_COOKIE['CH42SESSION']=='')
        {
          //$this->app->WF->ReBuildPageFrame();
          $this->Logout("Ihre Zeit ist abgelaufen, bitte melden Sie sich erneut an.",true);
          return false;
        }
      }
      else {
        // update time
        $this->app->DB->Update("UPDATE useronline,user SET useronline.time=NOW() WHERE
            login='1' AND sessionid='".$this->app->DB->real_escape_string($this->session_id)."' AND user.id=useronline.user_id AND user.activ='1'");

        session_write_close(); // Blockade wegnehmen           

        return true; 
      }
    }

  }

  function Check($usertype,$module='',$action='', $userid='')
  {
    return $usertype==='admin';
  }

  function Login()
  {
    $multidbs = $this->app->getDbs();
    if(count($multidbs) > 1)
    {
      $options = '';
      foreach($multidbs as $k => $v)
      {
        $options .= '<option value="'.$k.'">'.$v.'</options>';
      }
      $this->app->Tpl->Add('MULTIDB','<tr><td>Datenbank: <select name="db">'.$options.'</select><input type="hidden" name="dbselect" value="true"></td></tr>');
    }
    $db = $this->app->Secure->GetPOST('db');
    if(!empty($db))
    {
      if($this->app->changeDbConf($db))
      {
        setcookie('DBSELECTED', $db);
      }
    }
    $username = $this->app->DB->real_escape_string($this->app->Secure->GetPOST("username"));
    $password = $this->app->Secure->GetPOST('password');
    $passwordunescaped = $this->app->Secure->GetPOST('password','','','noescape');
    $stechuhrdevice = $this->app->Secure->GetPOST('stechuhrdevice');

    $token = $this->app->Secure->GetPOST('token');


    if($username=='' && ($password=='' || $token=='') && $stechuhrdevice == ''){
      setcookie('nonavigation',false);
      $this->app->Tpl->Set('LOGINMSG',"Bitte geben Sie Benutzername und Passwort ein.");

      $this->app->Tpl->Parse('PAGE',"updatelogin.tpl");
    }
    else {
      // Benutzer hat Daten angegeben
      $encrypted = $this->app->DB->Select("SELECT password FROM user
          WHERE username='".$username."' AND activ='1' LIMIT 1");

      $encrypted_md5 = $this->app->DB->Select("SELECT passwordmd5 FROM user
          WHERE username='".$username."' AND activ='1' LIMIT 1");

      $fehllogins= $this->app->DB->Select("SELECT fehllogins FROM user
          WHERE username='".$username."' AND activ='1' LIMIT 1");


      $type= $this->app->DB->Select("SELECT type FROM user
          WHERE username='".$username."' AND activ='1' LIMIT 1");

      $externlogin= $this->app->DB->Select("SELECT externlogin FROM user
          WHERE username='".$username."' AND activ='1' LIMIT 1");

      $hwtoken = $this->app->DB->Select("SELECT hwtoken FROM user
          WHERE username='".$username."' AND activ='1' LIMIT 1");
      
      $usesha512 = true;
      $salt = $this->app->DB->Select("SELECT salt FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");
      $passwordsha512 = $this->app->DB->Select("SELECT passwordsha512 FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");
      if($this->app->DB->error())$usesha512 = false;
      $usepasswordhash = true;
      $passwordhash = $this->app->DB->Select("SELECT passwordhash FROM `user` WHERE username='".$username."' AND activ='1' LIMIT 1");
      if($this->app->DB->error())$usepasswordhash = false;
      $stechuhrdevicelogin = false;
      $code = $this->app->Secure->GetPOST('code');
      $devices = $this->app->DB->SelectArr("SELECT * from stechuhrdevice where aktiv = 1 and code = '$code'");
      if($devices)
      {
        $IP = $_SERVER['REMOTE_ADDR'];
        foreach($devices as $device)
        {
          $IP = ip2long($_SERVER['REMOTE_ADDR']);
          $devIP = ip2long($device['IP']);
          $submask = ip2long($device['submask']);
          
          $maskIP = $IP & $submask;
          $dbIP = $devIP & $submask;
          if($maskIP == $dbIP)
          {
            $stechuhrdevicelogin = true;
          }
        }
      }
      if($code && !$stechuhrdevicelogin)
      {
        setcookie('nonavigation',false);
        $this->app->Tpl->Set('RESETSTORAGE','
              var devicecode = localStorage.getItem("devicecode"); 
      if(devicecode)
      {
        localStorage.setItem("devicecode", "");
      }

        ');
      }

      $user_id="";

      $userip = $_SERVER['REMOTE_ADDR'];
      $ip_arr = explode('.',$userip);

      if($ip_arr[0]=="192" || $ip_arr[0]=="10" || $ip_arr[0]=="127")
        $localconnection = 1;
      else 
        $localconnection = 0;


      //HACK intern immer Passwort
      //if($localconnection==1)
      //  $hwtoken=0;
      if($stechuhrdevicelogin && $stechuhrdevice)
      {
        $nr = substr($stechuhrdevice,0,6);
        if(is_numeric($nr) && strlen($stechuhrdevice) > 200)
        {
          $user_id = $this->app->DB->Select("SELECT id FROM user WHERE username = '$nr' and hwtoken = 4 LIMIT 1");
          if($user_id)
          {
            
            $encrypted = $this->app->DB->Select("SELECT password FROM user
                WHERE id='".$user_id."' AND activ='1' LIMIT 1");

            $encrypted_md5 = $this->app->DB->Select("SELECT passwordmd5 FROM user
                WHERE id='".$user_id."' AND activ='1' LIMIT 1");

            $fehllogins= $this->app->DB->Select("SELECT fehllogins FROM user
                WHERE id='".$user_id."' AND activ='1' LIMIT 1");

            //$fehllogins=0;

            $type= $this->app->DB->Select("SELECT type FROM user
                WHERE id='".$user_id."' AND activ='1' LIMIT 1");

            $externlogin= $this->app->DB->Select("SELECT externlogin FROM user
                WHERE id='".$user_id."' AND activ='1' LIMIT 1");

            $hwtoken = $this->app->DB->Select("SELECT hwtoken FROM user
                WHERE id='".$user_id."' AND activ='1' LIMIT 1");
            
            $usesha512 = true;
            $salt = $this->app->DB->Select("SELECT salt FROM user WHERE id='".$user_id."' AND activ='1' LIMIT 1");
            $passwordsha512 = $this->app->DB->Select("SELECT passwordsha512 FROM user WHERE id='".$user_id."' AND activ='1' LIMIT 1");
            if($this->app->DB->error())
            {
              $usesha512 = false;
            }
            $usepasswordhash = true;
            $passwordhash = $this->app->DB->Select("SELECT passwordhash FROM `user` WHERE id='".$user_id."' AND activ='1' LIMIT 1");
            if($this->app->DB->error())$usepasswordhash = false;
            $stechuhruser = $this->app->DB->Select("SELECT stechuhrdevice FROM user WHERE id = '$user_id'");
            {
              if($stechuhrdevice == $stechuhruser)
              {
                setcookie('nonavigation',true);
              } elseif($stechuhruser == "") {
                $this->app->DB->Update("UPDATE user set stechuhrdevice = '$stechuhrdevice' where id = '$user_id' LIMIT 1");
                setcookie('nonavigation',true);
              } else {
                $user_id = "";
                setcookie('nonavigation',false);
              }
            }
          }
        }
      }
      elseif($hwtoken==1) //motp
      {
        setcookie('nonavigation',false);
        $pin = $this->app->DB->Select("SELECT motppin FROM user
            WHERE username='".$username."' AND activ='1' LIMIT 1");

        $secret = $this->app->DB->Select("SELECT motpsecret FROM user
            WHERE username='".$username."' AND activ='1' LIMIT 1");

        if($this->mOTP($pin,$token,$secret) && $fehllogins<8 && (md5($password ) == $encrypted_md5 || md5($passwordunescaped ) == $encrypted_md5))
        {
          $user_id = $this->app->DB->Select("SELECT id FROM user
              WHERE username='".$username."' AND activ='1' LIMIT 1");
        } else { $user_id = ""; }

      } 
      //picosafe login
      else if ($hwtoken==2)
      {
        setcookie('nonavigation',false);
        //include("/var/www/wawision/trunk/phpwf/plugins/class.picosafelogin.php");
        $myPicosafe = new PicosafeLogin();

        $aes = $this->app->DB->Select("SELECT hwkey FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");
        $datablock = $this->app->DB->Select("SELECT hwdatablock FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");
        $counter = $this->app->DB->Select("SELECT hwcounter FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");

        $myPicosafe->SetUserAES($aes);
        $myPicosafe->SetUserDatablock($datablock);
        $myPicosafe->SetUserCounter($counter);		

        if($encrypted_md5!="")
        {
          if ( $myPicosafe->LoginOTP($token) && (md5($password) == $encrypted_md5 || md5($passwordunescaped) == $encrypted_md5)  && $fehllogins<8)
          {
            $user_id = $this->app->DB->Select("SELECT id FROM user
                WHERE username='".$username."' AND activ='1' LIMIT 1");

            // Update counter
            $newcounter = $myPicosafe->GetLastValidCounter();
            $this->app->DB->Update("UPDATE user SET hwcounter='$newcounter' WHERE id='$user_id' LIMIT 1");

          } else {
            //echo $myPicosafe->error_message;
            $user_id = "";
          }
        } else {

          if ( $myPicosafe->LoginOTP($token) && (crypt( $password,  $encrypted ) == $encrypted || crypt( $passwordunescaped,  $encrypted ) == $encrypted)  && $fehllogins<8)
          {
            $user_id = $this->app->DB->Select("SELECT id FROM user
                WHERE username='".$username."' AND activ='1' LIMIT 1");

            // Update counter
            $newcounter = $myPicosafe->GetLastValidCounter();
            $this->app->DB->Update("UPDATE user SET hwcounter='$newcounter' WHERE id='$user_id' LIMIT 1");

          } else {
            //echo $myPicosafe->error_message;
            $user_id = '';
          }
        }
      }
      //wawision otp 
      else if ($hwtoken==3)
      {
        setcookie('nonavigation',false);
        $wawi = new WaWisionOTP();
        $hwkey = $this->app->DB->Select("SELECT hwkey FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");
        $hwcounter = $this->app->DB->Select("SELECT hwcounter FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");
        $hwdatablock = $this->app->DB->Select("SELECT hwdatablock FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");

        //$wawi->SetKey($hwkey);
        //$wawi->SetCounter($hwcounter);

        $serial =$hwdatablock;
        //$key = pack('V*', 0x01,0x02,0x03,0x04);
        $hwkey = trim(str_replace(' ','',$hwkey));
        $hwkey_array = explode(",",$hwkey);  
        $key = pack('V*', $hwkey_array[0], $hwkey_array[1], $hwkey_array[2], $hwkey_array[3]);
        $check = (int)$wawi->wawision_pad_verify($token,$key,$serial);

        // Fix fuer HW
        if($check >= 2147483647) $check = 0;

        if($encrypted_md5!="")
        {
          if ( $check > 0 && (md5($password) == $encrypted_md5 || md5($passwordunescaped) == $encrypted_md5)  && $fehllogins<8 && $check > $hwcounter)
          {
            $user_id = $this->app->DB->Select("SELECT id FROM user
                WHERE username='".$username."' AND activ='1' LIMIT 1");

            // Update counter
            $this->app->DB->Update("UPDATE user SET hwcounter='$check' WHERE id='$user_id' LIMIT 1");
            $this->app->erp->SystemLog("xentral Login OTP Success User: $username Token: $token");

          } else {
            if($check===false)
            {
              $this->app->erp->SystemLog("xentral Login OTP Falscher Key (Unkown Key) User: $username Token: $token");
            } else if ($check < $hwcounter && $check > 0)
            {
              $this->app->erp->SystemLog("xentral Login OTP Counter Fehler (Replay Attacke) User: $username Token: $token");
            }
            //echo $myPicosafe->error_message;
            $user_id = "";
          }
        } else {
/*
          if ( $wawi->LoginOTP($token) && crypt( $password,  $encrypted ) == $encrypted  && $fehllogins<8)
          {
            $user_id = $this->app->DB->Select("SELECT id FROM user
                WHERE username='".$username."' AND activ='1' LIMIT 1");

            // Update counter
            $newcounter = $wawi->GetLastValidCounter();
            $this->app->DB->Update("UPDATE user SET hwcounter='$newcounter' WHERE id='$user_id' LIMIT 1");
          } else {

*/
            //echo $myPicosafe->error_message;
            $user_id = '';
//          }
        }
      }

      else {
        setcookie('nonavigation',false);



        if(isset($passwordhash) && $passwordhash != '' && $usepasswordhash)
        {
          $checkunescaped = password_verify (  $passwordunescaped , $passwordhash );
          if(!$checkunescaped)
          {
            $checkescaped = password_verify (  $password , $passwordhash );
          }else {
            $checkescaped = false;
          }
          if($checkunescaped || $checkescaped)
          {
            $user_id = $this->app->DB->Select("SELECT id FROM `user`
                WHERE username='".$username."' AND activ='1' LIMIT 1");
            if($checkescaped && $user_id)
            {
              $options = array(
                'cost' => 12,
              );
              $passwordhash = @password_hash($passwordunescaped, PASSWORD_BCRYPT, $options);
              $this->app->DB->Update("UPDATE `user` SET passwordhash = '".$this->app->DB->real_escape_string($passwordhash)."',
                password='',passwordmd5='', salt = '', passwordsha512 = '' 
                WHERE id = '".$user_id."' LIMIT 1");
            }
          }else{
            $user_id = '';
          }
        }elseif(!empty($passwordsha512) && $usesha512)
        {
          if(hash('sha512',$passwordunescaped.$salt) === $passwordsha512 && $fehllogins<8)
          {
            $user_id = $this->app->DB->Select("SELECT id FROM user
                WHERE username='".$username."' AND activ='1' LIMIT 1");
          }else{
            $user_id = '';
          }
        }elseif($encrypted_md5!=''){
          if ((md5($password ) == $encrypted_md5 || md5($passwordunescaped) == $encrypted_md5) && $fehllogins<8)
          {
            if(isset($this->app->Conf->WFdbType) && $this->app->Conf->WFdbType=="postgre"){
              $user_id = $this->app->DB->Select("SELECT id FROM \"user\"
                  WHERE username='".$username."' AND activ='1' LIMIT 1");
            } else {
              $user_id = $this->app->DB->Select("SELECT id FROM user
                  WHERE username='".$username."' AND activ='1' LIMIT 1");
            }
            if($user_id && $usesha512)
            {
              $salt = $this->app->DB->Select("SELECT salt FROM user WHERE id = '$user_id' LIMIT 1");
              $sha512 = $this->app->DB->Select("SELECT passwordsha512 FROM user WHERE id = '$user_id' LIMIT 1");
              if(empty($salt) && empty($sha512))
              {
                $salt = hash('sha512',microtime(true));
                $sha512 = hash('sha512',$passwordunescaped.$salt);
                $this->app->DB->Update("UPDATE user SET salt = '$salt', passwordsha512 = '$sha512' WHERE id = '$user_id' LIMIT 1");
              }
            }
          }
          else { $user_id = ""; }
        } else {
          if (((crypt( $password,  $encrypted ) == $encrypted) || (crypt( $passwordunescaped,  $encrypted ) == $encrypted))  && $fehllogins<8)
          {
            if(isset($this->app->Conf->WFdbType) && $this->app->Conf->WFdbType=="postgre"){
              $user_id = $this->app->DB->Select("SELECT id FROM \"user\"
                  WHERE username='".$username."' AND activ='1' LIMIT 1");
            } else {
              $user_id = $this->app->DB->Select("SELECT id FROM user
                  WHERE username='".$username."' AND activ='1' LIMIT 1");

            }
            if($user_id && $usesha512)
            {
              $salt = $this->app->DB->Select("SELECT salt FROM user WHERE id = '$user_id' LIMIT 1");
              $sha512 = $this->app->DB->Select("SELECT passwordsha512 FROM user WHERE id = '$user_id' LIMIT 1");
              if(empty($salt) && empty($sha512))
              {
                $salt = hash('sha512',microtime(true));
                $sha512 = hash('sha512',$passwordunescaped.$salt);
                $this->app->DB->Update("UPDATE user SET salt = '$salt', passwordsha512 = '$sha512' WHERE id = '$user_id' LIMIT 1");
              }
            }
          }
          else {
            $user_id = '';
          }
        }
      }

      //$password = substr($password, 0, 8); //TODO !!! besseres verfahren!!

      //pruefen ob extern login erlaubt ist!!

      // wenn keine externerlogin erlaubt ist und verbindung extern
      if($externlogin==0 && $localconnection==0)
      {
        $this->app->Tpl->Set('LOGINERRORMSG',"Es ist kein externer Login mit diesem Account erlaubt.");  
        $this->app->Tpl->Parse('PAGE','updatelogin.tpl');
      }
      else if(is_numeric($user_id))
      { 

        $this->app->DB->Delete("DELETE FROM useronline WHERE user_id='".$user_id."'");

        if($this->session_id != ''){
          $this->app->DB->Insert("INSERT INTO useronline (user_id, sessionid, ip, login, time)
            VALUES ('" . $user_id . "','" . $this->session_id . "','" . $_SERVER['REMOTE_ADDR'] . "','1',NOW())");
        } else {
          $this->app->Tpl->Set('LOGINERRORMSG','Session ID can not be empty');
          $this->app->Tpl->Parse('PAGE','updatelogin.tpl');
          return;
        }
        $this->app->DB->Select("UPDATE user SET fehllogins=0
            WHERE username='".$username."' LIMIT 1");
        if(method_exists($this->app->User,'createCache')) {
          $this->app->User->createCache();
        }
        header('Location: update.php?rand='.md5(mt_rand()));
        exit;
      }
      else if ($fehllogins>=8)
      {
        $this->app->Tpl->Set('LOGINERRORMSG',"Max. Anzahl an Fehllogins erreicht. Bitte wenden Sie sich an Ihren Administrator.");  
        $this->app->Tpl->Parse('PAGE',"updatelogin.tpl");
      }
      else
      { 

        if(isset($this->app->Conf->WFdbType) && $this->app->Conf->WFdbType=="postgre")
          $this->app->DB->Select("UPDATE \"user\" SET fehllogins=fehllogins+1 WHERE username='".$username."'");
        else
          $this->app->DB->Select("UPDATE user SET fehllogins=fehllogins+1 WHERE username='".$username."' LIMIT 1");

        $this->app->Tpl->Set('LOGINERRORMSG',"Benutzername oder Passwort falsch.");  
        $this->app->Tpl->Parse('PAGE',"updatelogin.tpl");
      }
    }
  }

  function Logout($msg="",$logout=false)
  {
    setcookie('DBSELECTED','');
    if($logout)
      $this->app->Tpl->Parse('PAGE',"sessiontimeout.tpl");

    $username = $this->app->User->GetName();
    $this->app->DB->Delete("DELETE FROM useronline WHERE user_id='".$this->app->User->GetID()."'");
    if(method_exists($this->app->User,'createCache')) {
      $this->app->User->createCache();
    }
    session_destroy();
    session_start();
    session_regenerate_id(true);
    $_SESSION['database']="";


    if(!$logout)
    {
      header("Location: ".$this->app->http."://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['REQUEST_URI']),'/'));
      exit;
    }
    //$this->app->Tpl->Set(LOGINERRORMSG,$msg);  
    //$this->app->Tpl->Parse(PAGE,"updatelogin.tpl");
  }


  function CreateAclDB()
  {

  }

  function mOTP($pin,$otp,$initsecret)
  {

    $maxperiod = 3*60; // in seconds = +/- 3 minutes
    $time=gmdate("U");
    for($i = $time - $maxperiod; $i <= $time + $maxperiod; $i++)
    {
      $md5 = substr(md5(substr($i,0,-1).$initsecret.$pin),0,6);

      if($otp == $md5) {
        return(true);
      }
    }
    return(false);
  }



}
class UpdateUser 
{
  var $cache;
  function __construct(&$app)
  {
    $this->app = &$app;
  }

  function GetID()
  { 
    if(!empty($_COOKIE['CH42SESSION']) && $_COOKIE['CH42SESSION']!='') {
      $tmp = $_COOKIE['CH42SESSION'];
    } else {
      $tmp = session_id();
    }
    if($tmp == '') {
      return 0;
    }
    if(!$this->cache || $this->cache['time'] +10 < microtime(true) || $this->cache['tmp'] != $tmp)
    {
      $this->cache = null;
      $user_id = $this->app->DB->Select("SELECT user_id FROM useronline WHERE sessionid='".$this->app->DB->real_escape_string($tmp)."' AND login ='1'");
      if($user_id)
      {
        $this->cache['user_id'] = $user_id;
        $this->cache['tmp'] = $tmp;
        $this->cache['time'] = microtime(true);
      }
      return $user_id;
    }

    return $this->cache['user_id'];
  }

  function GetType()
  { 
    if($this->GetID()<=0){
      return $this->app->Conf->WFconf['defaultgroup'];
    }

    if(isset($this->cache['type'])) {
      return $this->cache['type'];
    }

    $type = $this->app->DB->Select("SELECT type FROM user WHERE id='".$this->GetID()."'");
    $this->cache['type'] = $type;

    if($type=="")
    {
      $type = $this->app->Conf->WFconf['defaultgroup'];
      $this->cache['type'] = $type;
    }

    return $type;
  }

  function GetParameter($index)
  {
    $id = $this->GetID();

    if($index!="")
    {

      $settings = $this->app->DB->Select("SELECT settings FROM user WHERE id='$id' LIMIT 1");

      $settings = unserialize($settings);

      if(isset($settings[$index]))
        return $settings[$index];
    } 
  } 

  // value koennen beliebige Datentypen aus php sein (serialisiert) 
  function SetParameter($index,$value)
  {
    $id = $this->GetID();

    if($index!="" && isset($value))
    {
      $settings = $this->app->DB->Select("SELECT settings FROM user WHERE id='$id' LIMIT 1");
      $settings = unserialize($settings); 

      $settings[$index] = $value;

      $settings = serialize($settings);
      $this->app->DB->Update("UPDATE user SET settings='$settings' WHERE id='$id' LIMIT 1");
      $this->cache = null;
    }
  }



  function GetUsername()
  {
    if(isset($this->cache['username'])) {
      return $this->cache['username'];
    }
    $username = $this->app->DB->Select("SELECT username FROM user WHERE id='".$this->GetID()."'");
    $this->cache['username'] = $username;
    return $username;
  }

  function GetDescription()
  {
    return $this->GetName();
  }

  function GetMail()
  { 
    return $this->app->DB->Select("SELECT email FROM adresse WHERE id='".$this->GetAdresse()."'");
  }


  function GetName()
  { 
    if(isset($this->cache['name']))return $this->cache['name'];
    $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$this->GetAdresse()."'");
    $this->cache['name'] = $name;
    return $name;
  }

  function GetSprachen()
  {
    $sprachen = $this->app->DB->Select("SELECT sprachen FROM user WHERE id = '".(int)$this->GetId()."' LIMIT 1");
    if($sprachen)
    {
      $sprachena = explode(';',str_replace(',',';',$sprachen));
      foreach($sprachena as $sprache)
      {
        $sprache = trim($sprache);
        if($sprache != '')$ret[] = $sprache;
      }
      if(isset($ret)) {
        return $ret;
      }
    }
    return array('german','english');
  }

  function GetSprache()
  {
    $sprachen = $this->GetSprachen();
    return $sprachen[0];
  }


  function GetAdresse()
  {
    if(isset($this->cache['adresse'])) {
      return $this->cache['adresse'];
    }

    $adresse = $this->app->DB->Select("SELECT adresse FROM user WHERE id='".$this->GetID()."'");
    $this->cache['adresse'] = $adresse;
    return $adresse;
  }

  function GetProjektleiter()
  { 
    $result = $this->app->DB->SelectArr("SELECT parameter FROM adresse_rolle WHERE subjekt='Projektleiter' AND (bis='0000-00-00' OR bis < NOW()) AND adresse='".$this->app->User->GetAdresse()."'");      	

    if(!empty($result)){
      return true;
    }
    return false;
  }



  function DefaultProjekt()
  {
    $adresse = $this->GetAdresse();
    $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='".$adresse."'");
    if($projekt <=0)
      $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");

    return $projekt;
  }

  function GetEmail()
  { 
    $adresse = $this->GetAdresse();
    return $this->app->DB->Select("SELECT email FROM adresse WHERE id='".$adresse."'");
  }


  function GetFirma()
  {
    return 1;
  }


  function GetFirmaName()
  {
    if(isset($this->cache['firmaname']))return $this->cache['firmaname'];
    $name = $this->app->DB->Select("SELECT name FROM firma WHERE id='".$this->GetFirma()."'");
    $this->cache['firmaname'] = $name;
    return $name;
  }


  function GetField($field)
  { 
    return $this->app->DB->Select("SELECT $field FROM user WHERE id='".$this->GetID()."'");
  }


}

class UpdateThemeTemplate {
  var $NAME; //Name des Templates
  var $PATH; //PFAD des Templates
  var $parsed; //Zustand 
  var $ORIGINAL; //Parse - Text Vorlage
  var $VARS; //assoziatives Array mit Variablennamen als Index
  var $Elements;
  var $vararraycreated;
  function __construct($_path, $_file){

    $this->vararraycreated = false;
    $this->PATH=$_path;
    $this->NAME=$_file;
    $this->readFile();
  }
  
  function readFile()
  {
    $_path = $this->PATH;
    $_file = $this->NAME;
    $fp=@fopen($_path.$_file,"r");
    if($fp){
      if(filesize($_path.$_file)>0)
				$contents = fread ($fp, filesize($_path.$_file));
      fclose($fp);
    }else die($_path.$_file.' not found');
    $this->ORIGINAL=isset($contents)?$contents:'';
    //$this->CreateVarArray();
  }

  function CreateVarArray(){
    $this->vararraycreated = true;
    $this->SetVar('','');
    $pattern = '/((\[[A-Z0-9_]+\]))/';
    preg_match_all($pattern,$this->ORIGINAL,$matches, PREG_OFFSET_CAPTURE);

    //TODO Parser umbauen, damit Variablen nicht doppelt genommen werden.
    if(count($matches[0]) > 0)
    {
      $cmatches = count($matches[0]);
      for($i=0;$i<$cmatches;$i++)
      {
        $this->Elements[$i]['before'] = substr($this->ORIGINAL, $i==0?0:($matches[0][$i-1][1] +strlen($matches[0][$i-1][0]) ), $matches[0][$i][1] - ($i==0 ?0 :  ($matches[0][$i-1][1]+strlen($matches[0][$i-1][0])) ) );
        $this->Elements[$i]['el'] = $matches[0][$i][0];
        $this->Elements[$i]['el'] = str_replace('[','',$this->Elements[$i]['el']);
        $this->Elements[$i]['el'] = str_replace(']','',$this->Elements[$i]['el']);
        if($i > 0)$this->Elements[$i-1]['nach'] = $this->Elements[$i]['before'];
      }
      $this->Elements[count($matches[0])-1]['nach'] = substr($this->ORIGINAL, $matches[0][count($matches[0])-1][1]+strlen($matches[0][count($matches[0])-1][0]));
    }
    $cmatches = count($matches[0]);
    for($i=0;$i<$cmatches;$i++)
    {
      $matches[0][$i][0] = str_replace('[','',$matches[0][$i][0]);
      $matches[0][$i][0] = str_replace(']','',$matches[0][$i][0]);
      if(!isset($this->VARS[$matches[0][$i][0]]))
      {
        $this->SetVar($matches[0][$i][0],'');
      }
    }
  }

  function Parsed()
  {
    return 1;
  }

  function AddVar($_var, $_value){ $this->VARS[$_var]=$this->VARS[$_var].$_value; }
  function SetVar($_var, $_value){ $this->VARS[$_var]=$_value; }

}

/*********************** Class PcmsTemplate ****************************/
/// Main Parser for building the html skin (gui) 
class UpdateTemplateParser { 
  var $TEMPLATELIST;
  var $VARARRAY;
  var $VARVARARRAY;

  function __construct(&$app){     
		$this->app = &$app;
   	$this->TEMPLATELIST=null;
    $this->VARVARARRAY = null;
    $this->ReadTemplatesFromPath('');
	}


  function GetVars($tplfile)
  {
    $fp=@fopen($tplfile,"r");
    if($fp){
      $contents = fread ($fp, filesize($tplfile));
      fclose($fp);
    }
    $suchmuster = '/[\[][A-Z_]+[\]]/';
    preg_match_all($suchmuster, $contents, $treffer);
    return $treffer[0];
  }

  function ResetParser()
  {
    unset($this->TEMPLATELIST);
    unset($this->VARARRAY);
  }

  function ReadTemplatesFromPath($_path){
    if(is_file(__DIR__.'/update.tpl'))
    {
      $this->TEMPLATELIST['update.tpl'] = new UpdateThemeTemplate(__DIR__.'/','update.tpl');
    }else die(__DIR__.'/update.tpl nicht gefunden');
    if(is_file(__DIR__.'/updatelogin.tpl'))
    {
      $this->TEMPLATELIST['updatelogin.tpl'] = new UpdateThemeTemplate(__DIR__.'/','updatelogin.tpl');
    }else die(__DIR__.'/updatelogin.tpl nicht gefunden');
  }

  function CreateVarArray(){
    foreach($this->TEMPLATELIST as $template=>$templatename){
      if(count($this->TEMPLATELIST[$template]->VARS) > 0){
        foreach($this->TEMPLATELIST[$template]->VARS as $key=>$value){
          $this->VARARRAY[$key]=$value;
        }
      }
    }
  }

  function ShowVariables(){
    foreach($this->VARARRAY as $key=>$value)
    echo "<b>$key =></b>".htmlspecialchars($value)."<br>";
  }

  function ParseVariables($text){
    foreach($this->VARARRAY as $key=>$value)
    {
      if($key=!"")
        $text = str_replace('['.$key.']',$value,$text);
    }
    // fill empty vars
    return $text;
  }

  function ShowTemplates(){
    foreach ($this->TEMPLATELIST as $key=> $value){
      foreach ($value as $key1=> $text){
        if(!is_array($text))echo "$key ".htmlspecialchars($text)."<br>";
        if(is_array($text))foreach($text as $key2=>$value2) echo $key2." ".$value2;
      }
      echo "<br><br>";
    }
  }

  function Set($_var,$_value, $variable = false){ $this->VARARRAY[$_var]=$_value; if($variable)$this->VARVARARRAY[$_var] = $variable;}

  function Add($_var,$_value, $variable = false){  
    $this->VARARRAY[$_var]=isset($this->VARARRAY[$_var])?$this->VARARRAY[$_var].$_value:$_value;
    if($variable)$this->VARVARARRAY[$_var] = $variable;
  }
  
  function Get($_var){  
    return $this->VARARRAY[$_var]." ";
  }
  
  function Output($_template)
  {
    echo $this->app->erp->ClearDataBeforeOutput($this->Parse("",$_template,1));
  }


  function OutputAsString($_template)
  {
    return $this->app->erp->ClearDataBeforeOutput($this->Parse("",$_template,1));   
	}


  function Parse($_var, $_template,$return=0){

    //$this->AjaxParse();
    //if($_var == 'PAGE')$this->app->erp->ParseMenu();
    $this->ParseVarVars();
    if($_template!=""){
      if(isset($this->TEMPLATELIST[$_template]) && !($this->TEMPLATELIST[$_template]->vararraycreated))
      {
        $this->TEMPLATELIST[$_template]->CreateVarArray();
      }
      
      //alle template variablen aufuellen mit den werten aus VARARRAY 
      if(isset($this->TEMPLATELIST[$_template]) && isset($this->TEMPLATELIST[$_template]->VARS) && count($this->TEMPLATELIST[$_template]->VARS)>0){ 
        foreach ($this->TEMPLATELIST[$_template]->VARS as $key=> $value){
          $this->TEMPLATELIST[$_template]->SetVar($key,isset($this->VARARRAY[$key])?$this->VARARRAY[$key]:'');
        }
      
        //ORIGINAL auffuellen
        $tmptpl = $this->TEMPLATELIST[$_template]->ORIGINAL;
        foreach ($this->TEMPLATELIST[$_template]->VARS as $key=>$value){
          if(!is_numeric($key) && $key!="")
          $tmptpl = str_replace("[".$key."]",$value, $tmptpl);	
        }
      } else $tmptpl = '';
      //aufgefuelltes ORIGINAL in $t_var add($_var,ORIGINAL)
      if($return==1)
        return $tmptpl;
      else
        $this->Add($_var,$tmptpl);
    }
  }

  function AddAndParse($_var, $_value, $_varparse, $_templateparse){
    $this->Set($_var, $_value);
    $this->Parse($_varparse,$_templateparse);
  }
  
  function ParseVarVars()
  {
    $pattern = '/((\[[A-Z0-9_]+\]))/';
    if(!empty($this->VARVARARRAY) && is_array($this->VARVARARRAY))
    {
      foreach($this->VARVARARRAY as $k => $el)
      {
        preg_match_all($pattern,$this->VARARRAY[$k],$matches, PREG_OFFSET_CAPTURE);

        $cmatches = $matches[0]?count($matches[0]):0;
        for($i=0;$i<$cmatches;$i++)
        {
          $matches[0][$i][0] = str_replace('[','',$matches[0][$i][0]);
          $matches[0][$i][0] = str_replace(']','',$matches[0][$i][0]);
          if(isset($this->VARARRAY[$matches[0][$i][0]]))
          {
            $this->VARARRAY[$k] = str_replace('['.$matches[0][$i][0].']',$this->VARARRAY[$matches[0][$i][0]],$this->VARARRAY[$k]);            
          }
        }
        unset($matches);
      }
    }
  }

  function FinalParse($_template){
    
    $this->ParseVarVars();
    if(isset($this->TEMPLATELIST[$_template]) && !($this->TEMPLATELIST[$_template]->vararraycreated))
    {
      $this->TEMPLATELIST[$_template]->CreateVarArray();
    }
		$print = $this->app->Secure->GetGET("print");
		$printcontent = $this->app->Secure->GetGET("printcontent");

		if($printcontent=="") $printcontent="TAB1";
		if($print=="true") {
      $out = str_replace("[PRINT]",$this->VARARRAY[$printcontent],$this->TEMPLATELIST['print.tpl']->ORIGINAL);
      echo $out;
      exit;
		}     

    if($_template!="" && isset($this->TEMPLATELIST[$_template]) && isset($this->TEMPLATELIST[$_template]->VARS)){
      //alle template variablen aufuellen mit den werten aus VARARRAY
      if(count($this->TEMPLATELIST[$_template]->VARS)>0){ 
        foreach ($this->TEMPLATELIST[$_template]->VARS as $key=> $value)
        {
          $this->TEMPLATELIST[$_template]->SetVar($key,(isset($this->VARARRAY[$key])?$this->VARARRAY[$key]:''));
        }
      }
    }
    //ORIGINAL auffuellen
    
    
    $new = false;
    if($new)
    {
      //macht Noch Probleme 
      $tmptpl = '';
      if(!empty($this->TEMPLATELIST[$_template]->Elements))
      {
        
        
        foreach($this->TEMPLATELIST[$_template]->Elements as $k)
        {
          $tmptpl .= $k['before'];
          if(!empty($this->TEMPLATELIST[$_template]->VARS[$k['el']]))
          {
            $tmptpl .= $this->TEMPLATELIST[$_template]->VARS[$k['el']];
          }
        }
        $tmptpl .= $this->TEMPLATELIST[$_template]->Elements[count($this->TEMPLATELIST[$_template]->Elements)-1]['nach'];
      }else $tmptpl = $this->TEMPLATELIST[$_template]->ORIGINAL;
    }else 
    {
      $tmptpl = $this->TEMPLATELIST[$_template]->ORIGINAL;
      if(count($this->TEMPLATELIST[$_template]->VARS)>0){ 
        foreach ($this->TEMPLATELIST[$_template]->VARS as $key=>$value)
        {
          if($key!="")
          $tmptpl = str_replace("[".$key."]",$value, $tmptpl);
        }
      }
      
      if(count($this->VARARRAY)>0)
        foreach($this->VARARRAY as $key=>$value)
        {
          if($key!="")
          $tmptpl = str_replace('['.$key.']',$value,$tmptpl);
        }
    }
    
		$tmptpl = $this->app->erp->ClearDataBeforeOutput($tmptpl);
    return $tmptpl;
  }

  function AjaxParse()
  {

  }


  function KeywordParse()
  {

    foreach($this->TEMPLATELIST as $key=>$value)
    {
      foreach ($this->TEMPLATELIST[$key]->VARS as $var=>$tmp)
      if(strstr($var,"AJAX"))
      {
				echo $var;
      }
    }
  }



} 

class UpdateApplication
{

    var $ActionHandlerList;
    var $ActionHandlerDefault;
    public $Conf;
    protected $multidb;

    public function __construct($config, $group='')
    {
      session_cache_limiter('private');
      @session_start();

      $this->Conf= $config;
      if(file_exists(dirname(__DIR__) .'/conf/multidb.conf.php'))
      {
        $multidb = include dirname(__DIR__) .'/conf/multidb.conf.php';
        if(!empty($multidb))
        {
          $this->Conf->origDB = $this->Conf->WFdbname;
          foreach($multidb as $key => $value)
          {
            if(is_array($value))
            {
              if(is_numeric($key) && !empty($value['dbname']))
              {
                $this->multidb[] = [
                  'dbname'=>$value['dbname'],
                  'dbhost'=>!empty($value['dbhost'])?$value['dbhost']:$this->Conf->WFdbhost,
                  'dbport'=>!empty($value['dbport'])?$value['dbport']:$this->Conf->WFdbport,
                  'dbuser'=>!empty($value['dbuser'])?$value['dbuser']:$this->Conf->WFdbuser,
                  'dbpass'=>!empty($value['dbpass'])?$value['dbpass']:$this->Conf->WFdbpass,
                  'description'=>!empty($value['description'])?$value['description']:$value['dbname'],
                  'cronjob'=>!empty($value['cronjob'])?$value['cronjob']:0
                ];
              }elseif(!is_numeric($key)){
                $this->multidb[] = [
                  'dbname'=>!empty($value['dbname'])?$value['dbname']:$key,
                  'dbhost'=>!empty($value['dbhost'])?$value['dbhost']:$this->Conf->WFdbhost,
                  'dbport'=>!empty($value['dbport'])?$value['dbport']:$this->Conf->WFdbport,
                  'dbuser'=>!empty($value['dbuser'])?$value['dbuser']:$this->Conf->WFdbuser,
                  'dbpass'=>!empty($value['dbpass'])?$value['dbpass']:$this->Conf->WFdbpass,
                  'description'=>!empty($value['description'])?$value['description']:(!empty($value['dbname'])?$value['dbname']:$key),
                  'cronjob'=>!empty($value['cronjob'])?$value['cronjob']:0
                ];
              }
            }else{
              if(is_numeric($key))
              {
                $this->multidb[] = [
                  'dbname'=>$value,
                  'dbhost'=>$this->Conf->WFdbhost,
                  'dbport'=>$this->Conf->WFdbport,
                  'dbuser'=>$this->Conf->WFdbuser,
                  'dbpass'=>$this->Conf->WFdbpass,
                  'description'=>$value,
                  'cronjob'=>0
                ];
              }else{
                $this->multidb[] = [
                  'dbname'=>$key,
                  'dbhost'=>$this->Conf->WFdbhost,
                  'dbport'=>$this->Conf->WFdbport,
                  'dbuser'=>$this->Conf->WFdbuser,
                  'dbpass'=>$this->Conf->WFdbpass,
                  'description'=>$key,
                  'cronjob'=>0
                ];
              }
            }
          }
        }
      }
      if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=="on")
				$this->http = "https";
      else
				$this->http = "http";

    
      $this->Secure         = new UpdateSecure($this);   // empty $_GET, and $_POST so you
      
      // have to need the secure layer always
      $this->Tpl            = new UpdateTemplateParser($this);

      $this->User           = new UpdateUser($this);
      $this->acl            = new UpdateAcl($this);
      $this->WF             = new UpdatephpWFAPI($this);
      $this->String         = new UpdateWawiString();

      $this->BuildNavigation = true;
          
      $this->DB             = new UpdateDB($this->Conf->WFdbhost,$this->Conf->WFdbname,$this->Conf->WFdbuser,$this->Conf->WFdbpass,$this,$this->Conf->WFdbport);
      $this->Tpl->ReadTemplatesFromPath('');
    }


  public function getDbs()
  {
    $ret = [];
    $ret[$this->Conf->WFdbname] = $this->Conf->WFdbname;
    if(!empty($this->multidb))
    {
      foreach($this->multidb as $key => $value)
      {
        if($this->Conf->WFdbname !== $value['dbname']){
          $ret[$value['dbname']] = $value['description'];
        }elseif(!empty($value['description']) && $value['description'] !== $this->Conf->WFdbname){
          $ret[$this->Conf->WFdbname] = $value['description'];
        }
      }
    }
    return $ret;
  }

  public function getCronjobDbs()
  {
    $ret = [];
    if(!empty($this->multidb))
    {
      $nocron = [];
      foreach($this->multidb as $key => $value)
      {
        if($value['cronjob']){
          $ret[] = $value['dbname'];
        }else{
          $nocron[] = $value['dbname'];
        }
      }
      if(empty($ret[$this->Conf->WFdbname]) && empty($nocron[$this->Conf->WFdbname]))
      {
        $ret[] = $this->Conf->WFdbname;
      }
    }else{
      $ret[] = $this->Conf->WFdbname;
    }
    return $ret;
  }

  public function changeDbConf($dbname)
  {
    if(empty($dbname))
    {
      return false;
    }
    if($this->Conf->WFdbname === $dbname)
    {
      return false;
    }
    if(!empty($this->multidb))
    {
      foreach($this->multidb as $value)
      {
        if($value['dbname'] === $dbname)
        {
          $this->Conf->WFdbname = $dbname;
          $this->Conf->WFdbhost = $value['dbhost'];
          $this->Conf->WFdbport = $value['dbport'];
          $this->Conf->WFdbuser = $value['dbuser'];
          $this->Conf->WFdbpass = $value['dbpass'];
          $this->DB = new DB($this->Conf->WFdbhost,$this->Conf->WFdbname,$this->Conf->WFdbuser,$this->Conf->WFdbpass,$this,$this->Conf->WFdbport);
          return true;
        }
      }
    }
    return false;
  }

    function __destruct() {
      $this->DB->Close();
    }

    function ActionHandlerInit(&$caller)
    {
      $this->caller = &$caller;
    }

 
    function ActionHandler($command,$function)
    {
      $this->ActionHandlerList[$command]=$function; 
    }
    
    function DefaultActionHandler($command)
    {
      $this->ActionHandlerDefault=$command;
    }

   
    function ActionHandlerListen(&$app)
    {
      $fkt = '';
      $action = $app->Secure->GetGET("action","alpha");
      if($action!="")
      {
        if(isset($this->ActionHandlerList[$action]))$fkt = $this->ActionHandlerList[$action];
      }
      else
      {
        if(empty($this->ActionHandlerDefault) && isset($this->ActionHandlerList['list']))
        {
          if(empty($action))$app->Secure->GET['action'] = 'list';
          $this->ActionHandlerDefault = 'list';
        }
				if(isset($this->ActionHandlerDefault))$fkt = $this->ActionHandlerList[$this->ActionHandlerDefault];
      }

      // check permissions
      if($fkt)@$this->caller->$fkt();
    }
}

class UpdatePlayer {

  public $DefautTemplates;
  public $DefautTheme;

  /** @var UpdateApplication $app */
  public $app;

  function __construct()
  {
    $this->DefautTemplates='defaulttemplates';
    $this->DefautTheme='default';
  }

  /**
   * @param UpdateSession $sessionObj
   */
  function Run($sessionObj)
  {
    $this->app = $sessionObj->app;
    // play application only when layer 2 said that its ok
    if(!$sessionObj->GetCheck()) {
      if($sessionObj->reason==='PLEASE_LOGIN')
      {
        $action = 'login';
        $this->app->Secure->GET['action']='login';
      } else {
        $action = 'login';
      }
    } else {
      $action = $this->app->Secure->GetGET('action','alpha');
    }
    $this->app->Tpl->Set('YEAR',date('Y'));
    $this->app->Tpl->Set('BENUTZER',$this->app->User->GetName());
    //$this->app->Tpl->Set('REVISION',$this->app->erp->Revision(). " (".$this->app->erp->Branch().")");
    //$this->app->Tpl->Set('REVISIONID',$this->app->erp->RevisionPlain());
    //$this->app->Tpl->Set('BRANCH',$this->app->erp->Branch());

    $this->app->Tpl->Set(
      'LIZENZHINWEIS',' <a href="https://xentral.com/lizenzhinweis" target="_blank">Lizenzhinweis</a>'
    );
    switch($action)
    {
      case 'login':
        $this->app->Tpl->Set('UEBERSCHRIFT',"xentral &middot; Enterprise Warehouse Management");
        $this->app->acl->Login();
        echo $this->app->Tpl->FinalParse('update.tpl');
      break;
      case 'ajax':
        $data = null;
        $WAWISION['host']=XENTRAL_UPDATE_HOST;
        $WAWISION['port']="443";
        $cmd = $this->app->Secure->GetGET('cmd');
        switch($cmd){
          case 'checkforupdate':
            $this->app->erp->setMaintainance(true);
            $myUpd = new UpgradeClient($WAWISION, $this->app);
            $_data = $myUpd->CheckFiles(true);
            if(empty($_data) || (is_string($_data) && strpos($_data, 'ERROR') === 0)) {
              $_data = $myUpd->CheckFiles(true);
            }
            if(is_string($_data) && strpos($_data, 'ERROR') === 0) {
              $this->app->erp->setMaintainance(false);
              $data['error'] = $_data;
            }
            else {
              if(isset($_data['download']) && count($_data['download']) > 0) {
                $files = $_data['download'];
                $myUpd->DownloadFile($files);
                $_data = $myUpd->CheckFiles(true);
                if(isset($_data['download']) && count($_data['download']) > 0) {
                  $files = $_data['download'];
                  $myUpd->DownloadFile($files);
                  $_data = $myUpd->CheckFiles(true);
                }
                elseif(empty($_data['copy'])) {
                  $_data = $myUpd->CheckFiles(true);
                }
                if(!isset($_data['download']) || count($_data['download']) == 0) {
                  $data['reload'] = 1;
                }
              }
              if(isset($_data['copy']) && count($_data['copy']) > 0) {
                $files = $_data['copy'];
                $data3 = $myUpd->CopyFile($files);
                $_data = $myUpd->CheckFiles(true);
                if(!isset($_data['copy']) || count($_data['copy']) == 0) {
                  $data['reload'] = 1;
                  if(function_exists('opcache_invalidate')) {
                    opcache_invalidate(__FILE__);
                    opcache_invalidate(__DIR__ . '/update.tpl');
                  }
                }
              }
            }
          break;
          case 'changeversion':
            $version = $this->app->Secure->GetPOST('version');
            if($version) {
              $WAWISION['versionname'] = $version;
              $myUpd = new UpgradeClient($WAWISION, $this->app);
              $data['version'] = $myUpd->ChangeVersion();
            }
            else {
              $data['error']= 'Fehler: Keine Version';
            }
          break;
          case 'checkfiles':
            $version = $this->app->Secure->GetPOST('version');
            if($version) {
              $WAWISION['versionname'] = $version;
              $myUpd = new UpgradeClient($WAWISION, $this->app);
              $data = $myUpd->CheckFiles();
            }
            else {
              $data['error'] = 'Fehler: Keine Version';
            }
          break;
          case 'checkfileszip':
            $version = $this->app->Secure->GetPOST('version');
            if($version){
              $WAWISION['versionname'] = $version;
              $myUpd = new UpgradeClient($WAWISION, $this->app);
              $_data = $myUpd->CheckFiles();
              $data['zip'] = 0;
              $data['copy'] = isset($_data['copy']) && isset($_data['copy'][0]) ? count($_data['copy']) : 0;
              $data['download'] = isset($_data['download']) && isset($_data['download'][0]) ? count($_data['download']) : 0;
              if($data['download'] > 500) {
                $WAWISION['versionname'] = $version;
                $myUpd = new UpgradeClient($WAWISION, $this->app);
                $zipResonse = $myUpd->downloadZips();
                if(is_array($zipResonse)) {
                  $data = array_merge($data, $zipResonse);
                }
              }
            }
            else {
              $data = 'Fehler: Keine Version';
            }
            break;
          case 'checkfiles2':
            $version = $this->app->Secure->GetPOST('version');
            if($version) {
              $WAWISION['versionname'] = $version;
              $myUpd = new UpgradeClient($WAWISION, $this->app);
              $_data = $myUpd->CheckFiles();
              $data['copy'] = isset($_data['copy']) && isset($_data['copy'][0])?count($_data['copy']):0;
              $data['download'] = isset($_data['download']) && isset($_data['download'][0])?count($_data['download']):0;
              if(!empty($_data['FileError'])) {
                $data['FileError'] = $_data['FileError'];
              }
              if(!empty($_data['FolderError'])) {
                $data['FolderError'] = $_data['FolderError'];
              }
              if(isset($_data['error'])){
                $data['error'] = $_data['error'];
              }
              //$data = 'download '.(isset($data['download']) && isset($data['download'][0])?count($data['download']).' :'.$data['download'][0]['file']:0).' copy '.(isset($data['copy']) && isset($data['copy'][0])?count($data['copy']).' :'.$data['copy'][0]['file']:0);
            }
            else {
              $data = 'Fehler: Keine Version';
            }
          break;
          case 'downloadfiles2':
            $version = $this->app->Secure->GetPOST('version');
            if($version) {
              $WAWISION['versionname'] = $version;
              $myUpd = new UpgradeClient($WAWISION, $this->app);
              $files = false;
              if($version) {
                $data2 = $myUpd->CheckFiles();
                if(isset($data2['download']))$files = $data2['download'];
              }
              if($version && $files) {
                $data3 = $myUpd->DownloadFile($files);
                $data['todownload'] = (isset($data3['todownload']) && is_array($data3['todownload']))?count($data3['todownload']):0;
              }
              else {
                $data['todownload'] = null;
              }
            }
            else{
              $data['error'] = 'Keine Version';
            }
          break;
          case 'downloadfiles':
            $version = $this->app->Secure->GetPOST('version');
            if($version) {
              $WAWISION['versionname'] = $version;
              $myUpd = new UpgradeClient($WAWISION, $this->app);
              $files = json_decode(json_encode($this->app->Secure->GetPOST('files')),true);
              if($version && !$files)
              {
                $data2 = $myUpd->CheckFiles();
                if(isset($data2['todownload']))$files = $data2['todownload'];
              }
              if($version && $files) {
                $data = $myUpd->DownloadFile($files);
              }
              else {
                $data['todownload'] = null;
              }
            }
            else{
              $data['error'] = 'Keine Version';
            }
          break;
          case 'copyfiles':
            $version = $this->app->Secure->GetPOST('version');
            $WAWISION['versionname'] = $version;
            $myUpd = new UpgradeClient($WAWISION, $this->app);

            $files = json_decode(json_encode($this->app->Secure->GetPOST('files')),true);
            if($version && !$files) {
              $data2 = $myUpd->CheckFiles();
              if(isset($data2['todownload'])) {
                $myUpd->DownloadFile($data2['todownload']);
              }
              elseif(isset($data2['tocopy'])) {
                $files = $data2['tocopy'];
              }
            }

            if($version && $files) {
              $data = $myUpd->CopyFile($files);
            }
            elseif(!$version) {
              $data = array('error'=>'Keine Version'); 
            }
            else{
              $data = array('error'=>'Keine Version'); 
            }
          break;
          case 'copyfiles2':
            $version = $this->app->Secure->GetPOST('version');
            $WAWISION['versionname'] = $version;
            $myUpd = new UpgradeClient($WAWISION, $this->app);

            $files = false;
            if($version) {
              $data2 = $myUpd->CheckFiles();
              if(isset($data2['download'])) {
                $myUpd->DownloadFile($data2['download']);
                $data2 = $myUpd->CheckFiles();
              }
              
              if(isset($data2['copy'])) {
                $files = $data2['copy'];
              }
            }
            
            if($version && $files){
              $data3 = $myUpd->CopyFile($files);
              $data['tocopy'] = (isset($data3['tocopy']) && is_array($data3['tocopy']))?count($data3['tocopy']) : 0;
              if($data['tocopy'] === 0 && function_exists('opcache_reset')) {
                echo json_encode($data);
                opcache_reset();
                exit;
              }
            }
            elseif(!$version) {
              $data = array('error'=>'Keine Version'); 
            }
            else{
              $data['tocopy'] = 0;
            }
          break;
          case 'upgradedb':
            $nummer = $this->app->Secure->GetPOST('nummer');
            $tmp = $this->app->Conf->WFuserdata . '/tmp/' . $this->app->Conf->WFdbname.'/';
            if(!empty($tmp)) {
              $oldTmp = dirname($tmp).'/';
              foreach(['cache_services.php','cache_javascript.php','cache_classmap.php'] as $file) {
                // Aktuelle Cache-Dateien (MultiDB) löschen
                if(file_exists($tmp.$file)) {
                  if(function_exists('opcache_invalidate')) {
                    opcache_invalidate($tmp . $file, true);
                  }
                  @unlink($tmp.$file);
                }
                // Cache-Dateien aus Zeiten vor MultiDB löschen
                if(file_exists($oldTmp.$file)) {
                  if(function_exists('opcache_invalidate')) {
                    opcache_invalidate($oldTmp . $file, true);
                  }
                  @unlink($oldTmp.$file);
                }
              }
            }
            $className = 'erpAPI';
            if(class_exists('erpAPICustom')) {
              $className = 'erpAPICustom';
            }
            $this->app = new ApplicationCore();
            $methodName = 'UpgradeDatabase';
            try {
              $r = new ReflectionMethod($className, $methodName);
              $params = $r->getParameters();
              $anzargs = count($params);
            }
            catch(Exception $e) {
              $anzargs = 0;
            }
            $obj = new $className($this->app);
            if($obj) {
              $this->app->erp = $obj;
              if(method_exists($obj,'GetTMP')) {
                $tmp = $obj->GetTMP();
                if(!empty($tmp)) {
                  $oldTmp = dirname($tmp).'/';
                  foreach(['cache_services.php','cache_javascript.php','cache_classmap.php'] as $file) {
                    // Aktuelle Cache-Dateien (MultiDB) löschen
                    if(file_exists($tmp.$file)) {
                      if(function_exists('opcache_invalidate')) {
                        opcache_invalidate($tmp . $file, true);
                      }
                      @unlink($tmp.$file);
                    }
                    // Cache-Dateien aus Zeiten vor MultiDB löschen
                    if(file_exists($oldTmp.$file)) {
                      if(function_exists('opcache_invalidate')) {
                        opcache_invalidate($oldTmp . $file, true);
                      }
                      @unlink($oldTmp.$file);
                    }
                  }
                }
              }
            }
            if($anzargs > 0) {
              ob_start();
              $data['nr'] = $obj->$methodName($nummer);
              ob_end_clean();
            }
            else{
              ob_start();
              $data['nr'] = $obj->$methodName();
              ob_end_clean();
            }
            echo json_encode($data);
            try {
              $multiDbConfs = ConfigLoader::loadAll();
              $dbname = $this->app->Conf->WFdbname;
              //$cronjobDbs = $this->app->getDbs();
              //if(!empty($cronjobDbs)){
              if(!empty($multiDbConfs)){
                //$first = true;
                foreach ($multiDbConfs as $multiDbKey => $multiDbConf) {
                  if($multiDbConf->WFdbname === $dbname) {
                    continue;
                  }

                    $tmp = $this->app->Conf->WFuserdata . '/tmp/' . $multiDbConf->WFdbname.'/';
                    if(!empty($tmp)) {
                      foreach(['cache_services.php','cache_javascript.php','cache_classmap.php'] as $file) {
                        // Aktuelle Cache-Dateien (MultiDB) löschen
                        if(file_exists($tmp.$file)) {
                          if(function_exists('opcache_invalidate')) {
                            opcache_invalidate($tmp . $file, true);
                          }
                          @unlink($tmp.$file);
                        }
                      }
                    }

                  unset($this->app);
                  $this->app = new ApplicationCore($multiDbConf);
                  $this->app->DB = new DB(
                    $multiDbConf->WFdbhost,
                    $multiDbConf->WFdbname,
                    $multiDbConf->WFdbuser,
                    $multiDbConf->WFdbpass,
                    $this->app,
                    $multiDbConf->WFdbport
                  );
                  $obj->app->DB = $this->app->DB;
                //foreach ($cronjobDbs as $cronjobDb => $cronjobValue) {
                  //if($first) {
                  //  $first = false;
                  //  continue;
                  //}
                  //$this->app->changeDbConf($cronjobDb);

                  if($anzargs > 0){
                    ob_start();
                    $obj->app->DatabaseUpgrade->emptyTableCache();
                    $obj->$methodName($nummer);
                    ob_end_clean();
                  }
                  else{
                    ob_start();
                    $obj->app->DatabaseUpgrade->emptyTableCache();
                    $obj->$methodName();
                    ob_end_clean();
                  }
                  if($anzargs > 0 && $nummer < 12) {
                    $this->app->erp->setMaintainance(true);
                  }
                  else {
                    $this->app->erp->setMaintainance(false);
                  }
                  $this->app->erp->SetKonfigurationValue('welcome_changelog_last_save', '');
                  $obj->SetKonfigurationValue('welcome_changelog_last_save', '');
                }
              }

            } catch (Exception $e) {

            }
            if($anzargs > 0 && $nummer < 12) {
              $this->app->erp->setMaintainance(true, 'updatedb');
            }
            else {
              $this->app->erp->setMaintainance(false, 'updatedb');
            }
            $this->app->erp->SetKonfigurationValue('welcome_changelog_last_save', '');
            exit;
          break;
        }
        
        echo json_encode($data);
        exit;
      break;
      default:
        $this->KopiereOrdner(dirname(__DIR__).'/www',dirname(__DIR__).'/www_oss');
        $this->KopiereOrdner(dirname(__DIR__).'/phpwf',dirname(__DIR__).'/phpwf_oss');
        $this->KopiereOrdner(dirname(__DIR__).'/version.php',dirname(__DIR__).'/version_oss.php');
        if(empty($_GET['rand'])) {
          $rand = md5(mt_rand());
          header('Location: update.php?rand='.$rand);
          exit;
        }
        $WAWISION['host']=XENTRAL_UPDATE_HOST;
        $WAWISION['port']='443';
        $myUpd = new UpgradeClient($WAWISION, $this->app);

        $dateien = new Md5Dateien(dirname(__DIR__).'/www/');
        $dateien2 = new Md5Dateien(dirname(__DIR__).'/phpwf/');
        if(isset($dateien2->Dateien)) {
          if($dateien->Dateien && is_array($dateien->Dateien)) {
            $dateien->Dateien = array_merge($dateien->Dateien, $dateien2->Dateien);
          }
          else{
            $dateien = $dateien2;
          }
        }
        $lines = [];
        $request['dateien'] = $dateien->Dateien;
        $funktions_ind = [];
        $funktions = [];
        if(!empty($dateien->Dateien) && is_array($dateien->Dateien)) {
          foreach($dateien->Dateien as $k => $v) {
            if(
              strtolower(substr($k,-4)) !== '.php'
              || strpos($k, '_custom') === false
              || strpos($k,'/vendor/') !== false
            ) {
              continue;
            }

            $datei = __DIR__.'/..'.$k;
            if(!file_exists($datei)) {
              continue;
            }

            $fh = fopen($datei, 'r');
            if(!$fh) {
              continue;
            }

            $f_ind = -1;
            $i = -1;
            while(($line = fgets($fh)) !== false) {
              $i++;
              $lines[$i] = $line;
              if(!empty($funktions_ind) && !empty($funktions_ind[$k])) {
                foreach($funktions_ind[$k] as $k2 => $v2) {
                  if($v2 + 5 >= $i) {
                    $funktions[$k][$k2][] = $line;
                  }
                }
              }
              if(strpos($line, 'function') === false) {
                continue;
              }
              $f_ind++;
              $newBorder = 0;
              for($j = $i - 1; $j >= 0; $j--) {
                if(strpos($lines[$j],'*') !== false) {
                  $newBorder = $i - $j;
                }
                else{
                  break;
                }
              }

              $border = 5;
              if($newBorder > 5) {
                $border = $newBorder;
                if($border > 25) {
                  $border = 25;
                }
              }
              for($j = $i-$border; $j <= $i; $j++)  {
                if($j > -1) {
                  $funktions[$k][$f_ind][] = $lines[$j];
                }
              }
              $funktions_ind[$k][$f_ind] = $i;
            }
            if(isset($lines)) {
              unset($lines);
            }
            fclose($fh);
          }
        }
        $res = $myUpd->CheckVersionen(!empty($funktions)?$funktions:null);
        if(!empty($myUpd->errormsg)) {
          if(is_string($myUpd->errormsg)) {
            if($myUpd->errormsg === 'ERROR') {
              $myUpd->errormsg = 'Fehler: Die Lizenzdaten sind fehlerhaft / Lizenz abgelaufen';
            }
            $res = '<b style="color:red;font-size:150%">Fehler: '.$myUpd->errormsg.'</b>';
          }
          else{
            $res = json_encode($myUpd->errormsg);
          }
        }
        if($res === 'ERROR') {
          $res = '<b style="color:red;font-size:150%">Fehler: Die Lizenzdaten sind fehlerhaft / Lizenz abgelaufen</b>';
        }
        $this->app->Tpl->Add('PAGE',"<br><center>".$res."</center>");
        echo $this->app->Tpl->FinalParse('update.tpl');
      break;
    }
  }

  /**
   * @param string $quelle
   * @param string $ziel
   */
  public function KopiereOrdner($quelle, $ziel){
    if(!file_exists($quelle)) {
      return;
    }
    if(is_dir($quelle)) {
      if(!is_dir($ziel)&& !@mkdir($ziel) && !is_dir($ziel)) {
        return;
      }
      $handle = opendir($quelle);
      if(!$handle) {
        return;
      }
      $entries = [];
      while (false !== ($entry = readdir($handle))) {
        if($entry === '.' || $entry === '..') {
          continue;
        }
        $entries[] = $entry;
      }
      closedir($handle);
      if(empty($entries)) {
        return;
      }
      foreach($entries as $entry) {
        $this->KopiereOrdner(rtrim($quelle,'/').'/'.$entry, rtrim($ziel,'/').'/'.$entry);
      }
      return;
    }
    if(file_exists($ziel)) {
      return;
    }

    @copy($quelle, $ziel);
  }
  
}
if(!empty($intern)){
  if(is_file(dirname(__DIR__).'/conf/main.conf.php')){
    error_reporting(0);
    include_once dirname(__DIR__) . '/conf/main.conf.php';
    $config = new Config();
    $tmp = $config->WFuserdata . '/tmp/' . $config->WFdbname.'/';
    $app = new UpdateerpooSystem($config);
    $player = new UpdatePlayer();
    $player->KopiereOrdner(dirname(__DIR__).'/www',dirname(__DIR__).'/www_oss');
    $player->KopiereOrdner(dirname(__DIR__).'/phpwf',dirname(__DIR__).'/phpwf_oss');
    $player->KopiereOrdner(dirname(__DIR__).'/version.php',dirname(__DIR__).'/version_oss.php');

    $WAWISION['host']= XENTRAL_UPDATE_HOST;
    $WAWISION['port']='443';
    if(!empty($createversion)) {
      $WAWISION['version'] = $createversion;
    }
    $myUpd = new UpgradeClient($WAWISION, $app);

    $dateien = new Md5Dateien(dirname(__DIR__).'/www/');
    $dateien2 = new Md5Dateien(dirname(__DIR__).'/phpwf/');
    if(isset($dateien2->Dateien)) {
      if($dateien->Dateien && is_array($dateien->Dateien)) {
        $dateien->Dateien = array_merge($dateien->Dateien, $dateien2->Dateien);
      }
      else{
        $dateien = $dateien2;
      }
    }
    $request['dateien'] = $dateien->Dateien;
    $funktions_ind = [];
    if(!empty($dateien->Dateien) && is_array($dateien->Dateien)) {
      foreach($dateien->Dateien as $k => $v)  {
        if(!(strtolower(substr($k,-4)) === '.php' &&
          strpos($k, '_custom') !== false)) {
          continue;
        }

        $datei = __DIR__.'/..'.$k;
        if(!file_exists($datei)) {
          continue;
        }

        $fh = fopen($datei, 'r');
        if(!$fh) {
          continue;
        }

        $f_ind = -1;
        $i = -1;
        while(($line = fgets($fh)) !== false) {
          $i++;
          $lines[$i] = $line;
          if(!empty($funktions_ind) && !empty($funktions_ind[$k])) {
            foreach($funktions_ind[$k] as $k2 => $v2) {
              if($v2 + 5 >= $i) {
                $funktions[$k][$k2][] = $line;
              }
            }
          }
          if(strpos($line, 'function') !== false) {
            $f_ind++;
            for($j = $i-5; $j <= $i; $j++) {
              if($j > -1) {
                $funktions[$k][$f_ind][] = $lines[$j];
              }
            }
            $funktions_ind[$k][$f_ind] = $i;
          }
        }
        if(isset($lines)) {
          unset($lines);
        }
        fclose($fh);
      }
    }

    $res = $myUpd->CheckVersionen(null,true);
    if(empty($res) || (is_string($res) && stripos($res,'Error') === 0) || !empty($res['error'])) {
      usleep(1000000);
      $res = $myUpd->CheckVersionen(null,true);
    }
    if(!empty($res['current_version'])) {
      $WAWISION['version'] = $res['current_version'];
      $myUpd = new UpgradeClient($WAWISION, $app);
    }
    elseif(!empty($res['version'])) {
      $WAWISION['version'] = $res['version'];
    }

    $res = $myUpd->CheckVersionen(null,true);
    if(empty($res) || (is_string($res) && stripos($res,'Error') === 0) || !empty($res['error'])) {
      usleep(1000000);
      $res = $myUpd->CheckVersionen(null,true);
    }
    if(!empty($res['current_version'] && !empty($res['version']) && $res['current_version'] !== $res['version'])) {
      if(empty($allowChangeVersion)) {
        echo 'Version '.$res['version'].' ist nicht kompatibel zur eingestellten '.$res['current_version'].": abgebrochen\r\n";
        echo "benutzen Sie\n";
        echo "php upgradesystem changeversion\n";
        echo "um die Version umsustellen\n";
        return;
      }

      $parameter['version']=$res['current_version'];
      $parameter['versionname']=$res['version'];
      if($parameter['versionname'] && $parameter['versionname'] != $parameter['version']) {
        $changeversion = $myUpd->Request('changeversion',$parameter);
        if(empty($changeversion) || (is_string($changeversion) && stripos($changeversion,'Error') === 0)) {
          usleep(1000000);
          $changeversion = $myUpd->Request('changeversion',$parameter);
        }
        if(!empty($changeversion)) {
          $res['version'] = $changeversion;
        }
      }
    }

    $version = '';
    if(!empty($res['version'])) {
      $version = $res['version'];
    }
    elseif(!empty($res['error'])) {
      print_r($res['error']);
      echo "\n";
      return;
    }
    $files = false;
    if($version) {

      $data2 = $myUpd->CheckFiles();
      $maxRetries = 3;
      while((is_string($data2) && stripos($data2,'Error') === 0) || !isset($data2['download'])){
        usleep(1000000);
        $data2 = $myUpd->CheckFiles();
        $maxRetries--;
        if($maxRetries <= 0) {
          break;
        }
      }

      if(isset($data2['download'])){
        echo 'Download Files: ...';
        $myUpd->DownloadFile($data2['download'], 0, true);
        if(!is_file(dirname(__DIR__) . '/key.php') && !is_file(dirname(__DIR__) . '/download/key.php')) {
          $myUpd->DownloadFile($data2['download'], 0, true);
        }
        $data2 = $myUpd->CheckFiles();
        $maxRetries = 3;
        while(is_string($data2) && stripos($data2,'Error') === 0) {
          usleep(1000000);
          $data2 = $myUpd->CheckFiles();
          $maxRetries--;
          if($maxRetries <= 0) {
            break;
          }
        }
        if(!empty($data2['download'])) {
          $myUpd->DownloadFile($data2['download'], 0, true);
          $data2 = $myUpd->CheckFiles();
        }
        echo "done\n";
      }
      if((is_string($data2) && stripos($data2,'Error') === 0) || !isset($data2['copy'])) {
        usleep(1000000);
        $data2 = $myUpd->CheckFiles();
      }
      if(isset($data2['copy'])) {
        $files = $data2['copy'];
      }
    }

    if($version && $files) {
      echo 'Copy Files...';
      $data3 = $myUpd->CopyFile($files, 0);
      $data2 = $myUpd->CheckFiles();
      if(isset($data2['download'])){
        $myUpd->DownloadFile($data2['download'], 0, true);
        $data2 = $myUpd->CheckFiles();
        if(isset($data2['copy'])) {
          $files = $data2['copy'];
          $data3 = $myUpd->CopyFile($files, 0);
        }
      }
      $data['tocopy'] = (isset($data3['tocopy']) && is_array($data3['tocopy']))?count($data3['tocopy']):0;
      echo "done\n";
    }
    elseif(!$version) {
      $data = array('error'=>'Keine Version');
    }
    else{
      $data['tocopy'] = 0;
    }

    if(!empty($tmp)) {
      $tmpOld = dirname($tmp).'/';
      foreach(['cache_services.php','cache_javascript.php','cache_classmap.php'] as $file) {
        if(file_exists($tmp.$file)) {
          if(function_exists('opcache_invalidate')) {
            opcache_invalidate($tmp . $file, true);
          }
          @unlink($tmp.$file);
        }
        if(file_exists($tmpOld.$file)) {
          if(function_exists('opcache_invalidate')) {
            opcache_invalidate($tmpOld . $file, true);
          }
          @unlink($tmpOld.$file);
        }
      }
    }
    if(file_exists(dirname(__DIR__).'/xentral_autoloader.php')){
      $app = new ApplicationCore($config);
    }
    $className = 'erpAPI';
    if(class_exists('erpAPICustom')) {
      $className = 'erpAPICustom';
    }
    $methodName = 'UpgradeDatabase';
    $nummer = 0;
    $r = new ReflectionMethod($className, $methodName);
    $params = $r->getParameters();
    $anzargs = count($params);
    $obj = new $className($app);
    if($obj) {
      $app->erp = $obj;
      if(method_exists($obj,'GetTMP')) {
        $tmp = $obj->GetTMP();
        $tmpOld = dirname($tmp).'/';
        if(!empty($tmp)) {
          foreach(['cache_services.php','cache_javascript.php','cache_classmap.php'] as $file) {
            if(file_exists($tmp.$file)) {
              if(function_exists('opcache_invalidate')) {
                opcache_invalidate($tmp . $file, true);
              }
              @unlink($tmp.$file);
            }
            if(file_exists($tmpOld.$file)) {
              if(function_exists('opcache_invalidate')) {
                opcache_invalidate($tmpOld . $file, true);
              }
              @unlink($tmpOld.$file);
            }
          }
        }
      }
    }
    echo 'Upgrade DB...';
    if($anzargs > 0) {
      ob_start();
      $data['nr'] = $obj->$methodName($nummer);
      ob_end_clean();
    }
    else {
      ob_start();
      $data['nr'] = $obj->$methodName();
      ob_end_clean();
    }
    echo "done\n";
  }
}
elseif(!empty($testapp)) {
  $WAWISION['host']=XENTRAL_UPDATE_HOST;
  $WAWISION['port']='443';
  $myUpd = new UpgradeClient($WAWISION, $this->app);  
  $result = $myUpd->TestModul($testapp);
  if(empty($result) || (is_string($result) && stripos($result,'Error') === 0)) {
    usleep(1000000);
    $result = $myUpd->TestModul($testapp);
  }
}
else{
  if(is_file(dirname(__DIR__).'/conf/main.conf.php')) {
    include_once dirname(__DIR__).'/conf/main.conf.php';
    if(empty($_GET['action'])) {
      header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
      header('Cache-Control: no-store, no-cache, must-revalidate');
      header('Pragma: no-cache');
    }

    if(isset($_GET['action']) && $_GET['action'] === 'ajax' && isset($_GET['cmd']) && 'upgradedb' === $_GET['cmd']){
      $config = new Config();
      $tmp = $config->WFuserdata.'/tmp/';
      foreach(['cache_services.php','cache_javascript.php','cache_classmap.php'] as $file) {
        if(file_exists($tmp . $file)) {
          if(function_exists('opcache_invalidate')) {
            opcache_invalidate($tmp . $file, true);
          }
          @unlink($tmp.$file);
        }
        if(file_exists($tmp . $config->WFdbname . '/' . $file)) {
          if(function_exists('opcache_invalidate')) {
            opcache_invalidate($tmp . $config->WFdbname . '/' . $file, true);
          }
          @unlink($tmp . $config->WFdbname . '/' . $file);
        }
      }

      $config = ConfigLoader::load();
    }
    else {
      $config = new Config();
    }
    $app = new UpdateerpooSystem($config);
    $session = new UpdateSession();
    $session->Check($app);
    $player = new UpdatePlayer();
    $player->Run($session);
  }
}
