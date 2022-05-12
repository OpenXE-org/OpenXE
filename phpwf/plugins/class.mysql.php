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
/// Interface for communication with a MySQL Database
class DB{
  /** @var string  */
  public $dbname;
  /** @var false|mysqli  */
  public $connection;
  /** @var bool  */
  public $Log = false;
  /** @var mysqli_result|null|bool */
  public $results;
  /** @var Application|null  */
  public $app;
  //public $pdo; // Removed in 19.2; Use $this->app->Container('Database');
  private $disablehtmlclearing;
  /** @var bool  */
  private $disableclearjs;
  /** @var int  */
  private $maxAllowedPacket = 16777215;
  /** @var string  */
  private $dbuser;
  /** @var string  */
  private $dbpass;
  /** @var string  */
  private $dbhost;
  /** @var int $dbport  */
  private $dbport = 3306;
  /** @var array  */
  protected $columnCache = [];
  /** @var ApplicationCore $app */

  /**
   * DB constructor.
   *
   * @param string           $dbhost
   * @param string           $dbname
   * @param string           $dbuser
   * @param string           $dbpass
   * @param null|Application $app
   * @param int              $dbport
   */
  public function __construct($dbhost,$dbname,$dbuser,$dbpass,$app=null,$dbport=null)
  {
    $this->app = $app;
    $this->dbname=$dbname;
    $this->dbhost = $dbhost;
    $this->dbport = $dbport !== null ? (int)$dbport : 3306;
    $this->dbuser = $dbuser;
    $this->dbpass = $dbpass;
    $this->disablehtmlclearing = false;
    $this->disableclearjs = false;
    $this->columnCache[$dbname] = [];
    $this->connection = mysqli_connect($dbhost, $dbuser, $dbpass, '', $dbport);
    if(empty($this->connection)) {
      return;
    }

    mysqli_select_db($this->connection,$dbname);
    mysqli_query($this->connection, "SET NAMES 'utf8'");
    mysqli_query($this->connection, "SET SESSION SQL_MODE := ''");
    mysqli_query($this->connection, "SET CHARACTER_SET 'utf8'");
    mysqli_query($this->connection, 'SET lc_time_names = "de_DE" ');
    mysqli_query($this->connection, 'SET SQL_BIG_SELECTS=1');
  }

  /**
   * @return DB
   */
  public function getClone()
  {
    return new DB($this->dbhost, $this->dbname, $this->dbuser, $this->dbpass, $this->app, $this->dbport);
  }

  /**
   * @return array
   */
  public function getProcessListIds()
  {
    $processList = $this->SelectArr('SHOW PROCESSLIST');
    $ids = [];
    if(empty($processList)) {
      return $ids;
    }
    foreach($processList as $process) {
      $ids[] = $process['Id'];
    }

    return $ids;
  }

  /**
   * @param bool $value
   */
  public function DisableHTMLClearing($value = true)
  {
    $this->disablehtmlclearing = $value;
  }

  /**
   * @param bool $value
   */
  public function DisableJSClearing($value = true)
  {
    $this->disableclearjs = $value;
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
    return (String)$version_string;
  }

  /**
   * @return array
   */
  public function GetVersionArr()
  {
    if(empty($this->connection)) {
      return ['',0.,''];
    }
    $version = $this->Select('SELECT VERSION()');
    $isMaria = strripos($version, 'maria') !== false;
    if(!$isMaria) {
      $version_string = mysqli_get_server_info($this->connection);
      $version_string = substr($version_string,0,3);
      $version_stringFormat = str_replace('.','',$version_string);
      return ['mysql', $version_string, $version_stringFormat];
    }
    $mariaVersion =  substr($version, 0, 4);
    $version_stringFormat = str_replace('.','', $mariaVersion);
    return ['mariadb',$mariaVersion, $version_stringFormat];
  }

  /**
   * @return bool|null
   */
  public function Close()
  {
    if(empty($this->connection)) {
      return null;
    }

    return @mysqli_close($this->connection);
  }

  /**
   * @param string $database
   *
   * @return bool|null
   */
  public function SelectDB($database)
  {
    if((String)$database === '') {
      return null;
    }
    $this->columnCache[$database] = [];
    return @mysqli_select_db($this->connection, $database);
  }

  /**
   * @param null|mysqli_result $query
   */
  public function free($query = null){
    // Speicher freimachen
    if($query === null && !empty($this->_result)) {
      mysqli_free_result($this->_result);
      return;
    }
    if($query !== null){
      mysqli_free_result($query);
    }
  }

  /**
   * @param string $table
   * @param string $column
   *
   * @return int|bool|null
   */
  public function ColumnExists($table, $column)
  {
    if(empty($table) || empty($column) || empty($this->connection)){
      return false;
    }

    $exists = $this->Select("SELECT COUNT(*)
      FROM information_schema.columns
      WHERE table_schema = '{$this->dbname}' 
      AND table_name = '$table' AND column_name = '$column'");

    return $exists;
  }

  /**
   * @param $sql
   *
   * @return array|mixed|string|null
   */
  public function Select($sql) {
    if(empty($sql) || empty($this->connection) || strlen($sql) > $this->maxAllowedPacket) {
      return null;
    }
    $this->disablehtmlclearing = true;
    $this->disableclearjs = true;//30.07.18 Bruno kurzfritig eingefuegt um Fehler in Beschreibungstexten zu verhindern
    $args = func_num_args();
    if($args === 1) {
      if(class_exists('DevTools')){
        DevTools::Add('','','Select',$sql);
      }
      $this->results = @mysqli_query($this->connection,$sql);
      if($this->results === false) {
        return null;
      }

      /**
       * Abbrechen query mit SET beginnt
       */
      if (stripos($sql,'set') === 0) {
        return '';
      }
      $count = 0;
      $data = null;
      if(!$this->results) {
        return null;
      }
        
      $jsarray = null;
      if((!$this->disablehtmlclearing || !$this->disableclearjs) && !empty($this->app)
        && !empty($this->app->stringcleaner)) {
        $jsarray = $this->app->stringcleaner->CheckSQLHtml($sql);
      }
      while( $row = @mysqli_fetch_array($this->results)) {
        if($jsarray) {
          $j = -1;
          foreach($row as $k => $v)  {
            $j++;
            $pos1 = strpos($v, '[');
            $pos2 = strpos($v, '<');
            if($pos1 === false && $pos2 === false) {
              continue;
            }

            if($pos1 !== false) {
              $v = preg_replace('/\[([A-Z]{1}[\_A-Z0-9]*)\]/','$1', $v);
            }
            $row[$k] = $v;
            if($pos2 !== false && !empty($this->app) && !empty($this->app->stringcleaner)) {
              $dummy = null;
              if(isset($jsarray[$j]) && !$jsarray[$j]) {
                if($this->disablehtmlclearing) {
                  $row[$k] = $this->app->stringcleaner->CleanString($row[$k], 'nojs', $dummy);
                }
                else {
                  $row[$k] = $this->app->stringcleaner->CleanSQLReturn($v, $k);
                }
              }
              elseif(isset($jsarray[$j]) && 1 == $jsarray[$j] && !empty($this->app->stringcleaner)) {
                $row[$k] = $this->app->stringcleaner->CleanString($v, 'nojs', $dummy);
              }
            }

          }
        }
        $data[$count] = $row;
        $count++;
      }
      @mysqli_free_result($this->results);
      if($data === null) {
        return $data;
      }

      if(count($data) === 1){
        $data = $data[0][0];
      }
      elseif(count($data) < 1) {
        $data='';
      }
      return $data;
    }
    $arrArgs = func_get_args();
    $params=null;
    foreach($arrArgs as $key=>$arg) {
      if($key=='0') {
        $sql = preg_replace('/:(\w+)/i', '?', $arg);
        $s = str_repeat('s', count($arrArgs)-1);
        $params[] = &$s;
      }
      else {
        $params[]= &$arrArgs[$key];
      }
    }
    if(empty($params)) {
      return null;
    }
    $stmt = mysqli_prepare($this->connection,$sql);
    if($stmt === false) {
      return null;
    }
    try {
      $bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param');
      $bindParamsMethod->invokeArgs($stmt, $params);
    }
    catch(Exception $e) {
      return null;
    }
    $ret = mysqli_stmt_execute($stmt);
    if($ret === false) {
      return null;
    }

    $resultMetaData = mysqli_stmt_result_metadata($stmt);
    if($resultMetaData === false) {
      return null;
    }
    $stmtRow = array();
    $rowReferences = array();
    $firstcol = null;
    while ($field = mysqli_fetch_field($resultMetaData)) {
      $rowReferences[] = &$stmtRow[$field->name];
      if($firstcol === null) {
        $firstcol = $field->name;
      }
    }
    mysqli_free_result($resultMetaData);
    try {
      $bindResultMethod = new ReflectionMethod('mysqli_stmt', 'bind_result');
      $bindResultMethod->invokeArgs($stmt, $rowReferences);
    }
    catch(Exception $e) {
      return null;
    }
    $count = 0;
    $data = null;
    while(mysqli_stmt_fetch($stmt)) {
      $row = array();
      foreach($stmtRow as $key => $value) {
        $row[$key] = $value;
      }
      $data[] = $row;
    }
    mysqli_stmt_free_result($stmt);
    if($data === null) {
      return $data;
    }
    if(!empty($data) && count($data) === 1) {
      return $data[0][$firstcol];
    }
    if(empty($data)) {
      return $data;
    }
    return $data;
  }

  /**
   * @param string      $sql
   * @param int         $cache
   * @param string      $shortcode
   * @param null|string $lastTime
   *
   * @return array|null
   */
  public function SelectArrCache($sql, $cache = 0, $shortcode = '', &$lastTime = null){
    if(empty($sql) || empty($this->connection) || strlen($sql) > $this->maxAllowedPacket) {
      return null;
    }
    if($cache <= 0) {
      $lastTime = (string)$this->Select('SELECT NOW()');
      return $this->SelectArr($sql);
    }
    $arr = $this->SelectRow(
      "SELECT `id`, `ergebnis`, `zeitstempel`, `zeitstempel` < DATE_SUB(NOW(), INTERVAL $cache SECOND) as abgelaufen 
      FROM `sqlcache` 
      WHERE `abfrage` = '".$this->real_escape_string($sql)."' 
      LIMIT 1"
    );
    $isEmpty = empty($arr);
    if($isEmpty|| $arr['abgelaufen']) {
      $erg = $this->SelectArr($sql);
      $lastTime = (string)$this->Select('SELECT NOW()');
      if(!$isEmpty) {
        $this->Update(
          "UPDATE sqlcache 
          SET zeitstempel = now(), ergebnis = '".base64_encode(json_encode($erg))."',sekunden = '$cache' 
          WHERE id = '".$arr['id']."' 
          LIMIT 1"
        );
      }
      else{
        $this->Insert(
          "INSERT INTO sqlcache (abfrage, ergebnis, zeitstempel,shortcode,sekunden) 
          VALUES ('".$this->real_escape_string($sql)."','".base64_encode(json_encode($erg))."',now(),'$shortcode','$cache')"
        );
        $arr = [];
        $arr['id'] = $this->GetInsertID();
      }
      $this->Delete('DELETE FROM sqlcache WHERE zeitstempel < DATE_SUB(NOW(), INTERVAL sekunden SECOND) ');

      return $erg;
    }
    $ret = json_decode(base64_decode($arr['ergebnis']),true);
    if($ret !== false) {
      $lastTime = $arr['zeitstempel'];
      return $ret;
    }

    $erg = $this->SelectArr($sql);
    $this->Update(
      "UPDATE `sqlcache` 
       SET `zeitstempel` = now(), `ergebnis` = '".base64_encode(json_encode($erg))."', `sekunden` = '$cache' 
       WHERE `id` = '".$arr['id']."' 
       LIMIT 1"
    );
    $lastTime = (string)$this->Select('SELECT NOW()');
    
    return $erg;
  }

  /**
   * @param string $sql
   *
   * @return array|null
   */
  public function SelectRow($sql)
  {
    if(empty($sql) || empty($this->connection) || strlen($sql) > $this->maxAllowedPacket) {
      return null;
    }
    $this->disablehtmlclearing = true;
    $this->disableclearjs = true;//30.07.18 Bruno kurzfritig eingefuegt um Fehler in Beschreibungstexten zu verhindern
    $args = func_num_args();
    if($args === 1) {
      if(class_exists('DevTools')){
        DevTools::Add('','','SelectArr',$sql);
      }
      if(1) {
        $this->results = @mysqli_query($this->connection,$sql);
        if(!$this->results) {
          return null;
        }
        $jsarray = null;
        if((!$this->disablehtmlclearing || !$this->disableclearjs)
          && isset($this->app->stringcleaner)  && $this->app) {
          $jsarray = $this->app->stringcleaner->CheckSQLHtml($sql);
        }
        $count = 0;
        $data = null;
        $nojs = 'nojs';
        if($jsarray) {
          if(strpos($sql,'<input') || strpos($sql,'<select')) {
            $nojs = 'xss_clean';
          }
        }
        if( $row = @mysqli_fetch_array($this->results)){
          if($jsarray) {
            $j = -1;
            foreach($row as $k => $v) {
              if(is_numeric($k)) {
                continue;
              }

              $j++;
              $pos1 = strpos($v, '[');
              $pos2 = strpos($v, '<');
              if($pos1 === false && $pos2 === false) {
                continue;
              }

              if($pos1 !== false) {
                $v = preg_replace('/\[([A-Z]{1}[\_A-Z0-9]*)\]/','$1', $v);
              }
              if($pos2 !== false  && $this->app) {
                $dummy = null;
                if(isset($jsarray[$j]) && !$jsarray[$j]) {
                  if($this->disablehtmlclearing) {
                    $row[$k] = $this->app->stringcleaner->CleanString($v, $nojs, $dummy);
                  }
                  else{
                    $row[$k] = $this->app->stringcleaner->CleanSQLReturn($v, $k, $nojs);
                  }
                }
                elseif(isset($jsarray[$j]) && 1 == $jsarray[$j]) {
                  $row[$k] = $this->app->stringcleaner->CleanString($v, $nojs, $dummy);
                }
                else {
                  $row[$k] = $v;
                }
              }
              else{
                $row[$k] = $v;
              }

            }
          }
          unset($ArrData);
          // erstelle datensatz array
          foreach($row as $key=>$value) {
            if(!is_numeric($key)) {
              $ArrData[$key]=$value;
            }
          }
          if(!empty($ArrData)){
            $data = $ArrData;
          }
          $count++;
        }
        @mysqli_free_result($this->results);
      }
      if(!empty($data)) {
        return $data;
      }
      return null;
    }
    $arrArgs = func_get_args();
    $params=null;
    foreach($arrArgs as $key=>$arg) {
      if($key=='0')
      {
        $sql = preg_replace('/:(\w+)/i', '?', $arg);
        $s = str_repeat('s', count($arrArgs)-1);
        $params[] = &$s;
      }
      else {
        $params[]= &$arrArgs[$key];
      }
    }
    if(empty($params)) {
      return null;
    }
    $stmt = mysqli_prepare($this->connection,$sql);
    if($stmt === false) {
      return null;
    }
    try {
      $bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param');
      $bindParamsMethod->invokeArgs($stmt, $params);
    }
    catch(Exception $e) {
      return null;
    }
    $ret = mysqli_stmt_execute($stmt);
    if($ret === false) {
      return null;
    }

    $resultMetaData = mysqli_stmt_result_metadata($stmt);

    if($resultMetaData === false){
      return null;
    }
    $stmtRow = array();
    $rowReferences = array();
    while ($field = mysqli_fetch_field($resultMetaData)) {
      $rowReferences[] = &$stmtRow[$field->name];
    }
    mysqli_free_result($resultMetaData);
    try {
      $bindResultMethod = new ReflectionMethod('mysqli_stmt', 'bind_result');
      $bindResultMethod->invokeArgs($stmt, $rowReferences);
    }
    catch(Exception $e) {
      return null;
    }
    $count = 0;
    $data = null;
    if(mysqli_stmt_fetch($stmt)) {
      $row = array();
      foreach($stmtRow as $key => $value) {
        $row[$key] = $value;
      }
      $data = $row;
    }
    mysqli_stmt_free_result($stmt);

    return $data;
  }

  /**
   * @param string $sql
   *
   * @return array
   */
  public function SelectFirstCols($sql)
  {
    if(empty($sql)) {
      return [];
    }
    $this->results = @mysqli_query($this->connection,$sql);
    if(!$this->results) {
      return [];
    }
    $ret = [];
    while( $row = @mysqli_fetch_assoc($this->results)){
      $ret[] = reset($row);
    }

    return $ret;
  }

  /**
   * @param string $sql
   *
   * @return array
   */
  public function SelectPairs($sql)
  {
    $this->results = @mysqli_query($this->connection,$sql);
    if(!$this->results) {
      return [];
    }
    $ret = [];
    while( $row = @mysqli_fetch_row($this->results)){
      if(count($row) < 2) {
        mysqli_free_result($this->results);
        return [];
      }
      $ret[$row[0]] = $row[1];
    }
    mysqli_free_result($this->results);
    return $ret;
  }

  /**
   * @param string $sql
   * @param int    $cache
   * @param string $shortcode
   *
   * @return array|null
   */
  public function SelectArr($sql, $cache = 0, $shortcode = '') {
    if(empty($sql) || empty($this->connection) || strlen($sql) > $this->maxAllowedPacket) {
      return null;
    }
    $this->disablehtmlclearing = true;
    $this->disableclearjs = true;//30.07.18 Bruno kurzfritig eingefuegt um Fehler in Beschreibungstexten zu verhindern
    $args = func_num_args();
    if($args === 1) {
      if(class_exists('DevTools')){
        DevTools::Add('','','SelectArr',$sql);
      }
      if(1){
        $this->results = @mysqli_query($this->connection,$sql);
        if(!$this->results) {
          return null;
        }
        $jsarray = null;
        if((!$this->disablehtmlclearing || !$this->disableclearjs) && !empty($this->app)
          && !empty($this->app->stringcleaner)) {
          $jsarray = $this->app->stringcleaner->CheckSQLHtml($sql);
        }
        $count = 0;
        $data = null;
        $nojs = 'nojs';
        if($jsarray) {
          if(strpos($sql,'<input') || strpos($sql,'<select')) {
            $nojs = 'xss_clean';
          }
        }
        while( $row = @mysqli_fetch_array($this->results)){
          if($jsarray) {
            $j = -1;
            foreach($row as $k => $v) {
              if(is_numeric($k)) {
                continue;
              }
              $j++;
              $pos1 = strpos($v, '[');
              $pos2 = strpos($v, '<');
              if($pos1 === false && $pos2 === false) {
                continue;
              }

              $dummy = null;
              if($pos1 !== false) {
                $v = preg_replace('/\[([A-Z]{1}[\_A-Z0-9]*)\]/','$1', $v);
              }
              if($pos2 !== false  && !empty($this->app)) {
                if(isset($jsarray[$j]) && !$jsarray[$j] && !empty($this->app->stringcleaner)) {
                  if($this->disablehtmlclearing) {
                    $row[$k] = $this->app->stringcleaner->CleanString($v, $nojs, $dummy);
                  }
                  else{
                    $row[$k] = $this->app->stringcleaner->CleanSQLReturn($v, $k, $nojs);
                  }
                }
                elseif(isset($jsarray[$j]) && 1 == $jsarray[$j] && !empty($this->app->stringcleaner)) {
                  $row[$k] = $this->app->stringcleaner->CleanString($v, $nojs, $dummy);
                }
                else {
                  $row[$k] = $v;
                }
              }
              else{
                $row[$k] = $v;
              }
            }
          }
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
    $arrArgs = func_get_args();
    $params=null;
    foreach($arrArgs as $key=>$arg){
      if($key=='0') {
        $sql = preg_replace('/:(\w+)/i', '?', $arg);
        $s = str_repeat('s', count($arrArgs)-1);
        $params[] = &$s;
      }
      else {
        $params[]= &$arrArgs[$key];
      }
    }
    if(empty($params)) {
      return null;
    }
    $stmt = mysqli_prepare($this->connection,$sql);
    if($stmt === false) {
      return null;
    }
    try {
      $bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param');
      $bindParamsMethod->invokeArgs($stmt, $params);
    }
    catch(Exception $e) {
      return null;
    }
    $ret = mysqli_stmt_execute($stmt);
    if(empty($ret)) {
      return null;
    }

    $resultMetaData = mysqli_stmt_result_metadata($stmt);
    if(empty($resultMetaData)) {
      return null;
    }

    $stmtRow = array();
    $rowReferences = array();
    while ($field = mysqli_fetch_field($resultMetaData)) {
      $rowReferences[] = &$stmtRow[$field->name];
    }
    mysqli_free_result($resultMetaData);
    try {
      $bindResultMethod = new ReflectionMethod('mysqli_stmt', 'bind_result');
      $bindResultMethod->invokeArgs($stmt, $rowReferences);
    }
    catch(Exception $e) {
      return null;
    }
    //$count = 0;
    $data = null;
    while(mysqli_stmt_fetch($stmt)){
      $row = array();
      foreach($stmtRow as $key => $value){
        $row[$key] = $value;
      }
      $data[] = $row;
    }
    mysqli_stmt_free_result($stmt);

    return $data;
  }

  public function Result($sql){
    if(empty($sql) || empty($this->connection)) {
      return null;
    }
    $query = mysqli_query($this->connection,$sql);
    if(empty($query)) {
      return null;
    }

    return mysqli_result($query, 0);
  }

  /**
   * @return int|string|null
   */
  public function GetInsertID(){
    if(empty($this->connection)) {
      return null;
    }

    return mysqli_insert_id($this->connection);
  }

  /**
   * @param string $sql
   *
   * @return null|array
   */
  public function GetArray($sql){
    $i=0;
    $result = mysqli_query($this->connection,$sql);
    $tmp = null;
    while($row = mysqli_fetch_assoc($result)) {
      foreach ($row as $key=>$value){
        $tmp[$i][$key]=$value;
      }
      $i++;
    }

    return $tmp;
  }

  /**
   * @param string $sql
   *
   * @return bool|mysqli_result|null
   */
  public function Insert($sql){
    if(empty($sql) || empty($this->connection) || strlen($sql) > $this->maxAllowedPacket) {
      return null;
    }
    $args = func_num_args();
    if($args === 1) {
      if(class_exists('DevTools')){
        DevTools::Add('','','Insert',$sql);
      }
      $this->LogSQL($sql,'insert');
      $ret = mysqli_query($this->connection,$sql);
      if(mysqli_errno($this->connection) === 1118) {
        mysqli_query($this->connection, 'SET innodb_strict_mode = OFF;');
        $ret = mysqli_query($this->connection, $sql);
        if(mysqli_errno($this->connection) === 1118 && !empty($this->app) && !empty($this->app->erp)
          && method_exists($this->app->erp, 'setSystemHealth')) {
          $this->app->erp->setSystemHealth('database', 'error','sql_errors', 'Row size to large Error');
          $ret = mysqli_query($this->connection, $sql);
        }
      }
      return $ret;
    }
    $arrArgs = func_get_args();
    $params=null;
    foreach($arrArgs as $key=>$arg) {
      if($key=='0') {
        $sql = preg_replace('/:(\w+)/i', '?', $arg);
        $s = str_repeat('s', count($arrArgs)-1);
        $params[] = &$s;
      }
      else {
        $params[]= &$arrArgs[$key];
      }
    }
    if(empty($params)) {
      return null;
    }
    $stmt = mysqli_prepare($this->connection,$sql);
    if($stmt === false) {
      return null;
    }
    try {
      $bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param');
      $bindParamsMethod->invokeArgs($stmt, $params);
    }
    catch (Exception $e) {
      return null;
    }
    $ret = mysqli_stmt_execute($stmt);
    if($ret) {
      return $ret;
    }
    mysqli_stmt_free_result($stmt);

    return $ret;
  }

  /**
   * @param string $sql
   *
   * @return bool|mysqli_result|null
   */
  public function InsertWithoutLog($sql){
    if(empty($sql) || empty($this->connection) || strlen($sql) > $this->maxAllowedPacket) {
      return null;
    }

    return mysqli_query($this->connection,$sql);
  }

  /**
   * @param string $sql
   *
   * @return bool|mysqli_result|null
   */
  public function Update($sql){
    if(empty($sql) || empty($this->connection) || strlen($sql) > $this->maxAllowedPacket) {
      return null;
    }
    $args = func_num_args();
    if($args===1) {
      if(class_exists('DevTools')) {
        DevTools::Add('','','Update',$sql);
      }
      $this->LogSQL($sql,'update');
      $query = mysqli_query($this->connection,$sql);
      if(mysqli_errno($this->connection) === 1118) {
        mysqli_query($this->connection, 'SET innodb_strict_mode = OFF;');
        $query = mysqli_query($this->connection,$sql);
        if(mysqli_errno($this->connection) === 1118 && !empty($this->app) && !empty($this->app->erp)
          && method_exists($this->app->erp, 'setSystemHealth')) {
          $this->app->erp->setSystemHealth('database', 'error','sql_errors', 'Row size to large Error');

          try {
            /** @var Systemhealth $systemhealth */
            $systemhealth = $this->app->erp->LoadModul('systemhealth');
            if($systemhealth !== null && method_exists($systemhealth, 'createEvent')){
              $systemhealth->createEvent('database', 'sql_errors', 'error', '', 0, 'Row size to large Error');
            }
          }
          catch (Exception $d) {

          }
          $query = mysqli_query($this->connection,$sql);
        }
      }
      if(strpos($sql,'firmendaten') !== false && mysqli_errno($this->connection) === 1118) {
        $this->app->erp->ensureDatabaseUpgradeProperty();
        $this->app->DatabaseUpgrade->ChangeFirmendatenToMyIsam();
        return mysqli_query($this->connection,$sql);
      }
      return $query;
    }
    $arrArgs = func_get_args();
    $params=null;
    foreach($arrArgs as $key=>$arg){
      if($key=='0') {
        $sql = preg_replace('/:(\w+)/i', '?', $arg);
        $s = str_repeat('s', count($arrArgs)-1);
        $params[] = &$s;
      }
      else {
        $params[]= &$arrArgs[$key];
      }
    }
    if(empty($params)) {
      return null;
    }
    $stmt = mysqli_prepare($this->connection,$sql);
    if($stmt === false) {
      return null;
    }
    try {
      $bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param');
      $bindParamsMethod->invokeArgs($stmt, $params);
    }
    catch (Exception $e) {
      return null;
    }
    $ret = mysqli_stmt_execute($stmt);
    if(!$ret) {
      return $ret;
    }
    mysqli_stmt_free_result($stmt);

    return $ret;
  }

  /**
   * @param string $sql
   *
   * @return bool|mysqli_result|null
   */
  public function UpdateWithoutLog($sql){
    if(empty($sql) || empty($this->connection) || strlen($sql) > $this->maxAllowedPacket) {
      return null;
    }

    return mysqli_query($this->connection,$sql);
  }

  /**
   * @param string $sql
   *
   * @return bool|mysqli_result|null
   */
  public function Delete($sql){
    if(empty($sql) || empty($this->connection)) {
      return null;
    }
    if(class_exists('DevTools')) {
      DevTools::Add('','','Delete',$sql);
    }
    $this->LogSQL($sql,'delete');

    return mysqli_query($this->connection,$sql);
  }

  /**
   * @param string $sql
   *
   * @return bool|mysqli_result|null
   */
  public function Drop($sql){
    if(empty($sql) || empty($this->connection) || strlen($sql) > $this->maxAllowedPacket) {
      return null;
    }
    if(class_exists('DevTools')) {
      DevTools::Add('','','Drop',$sql);
    }
    $this->LogSQL($sql,'drop');

    return mysqli_query($this->connection,$sql);
  }

  public function LogSQL($sql,$befehl)
  {
/*
    $name = $this->app->User->GetName();
    $sql = base64_encode($sql);
    $serial = base64_encode(serialize($this->app->Secure));
    mysqli_query($this->connection,"INSERT INTO logdatei (id,name,befehl,statement,app,zeit) 
      VALUES ('','$name','$befehl','$sql','$serial',NOW())");
*/
  }

  /**
   * @param string $sql
   *
   * @return int
   */
  public function Count($sql){
    if(!empty($sql) && !empty($this->connection) &&  strlen($sql) <= $this->maxAllowedPacket
      && mysqli_query($this->connection,$sql)) {
      return mysqli_num_rows(mysqli_query($this->connection,$sql));
    }

    return 0;
  }

  /**
   * @param string $table
   *
   * @return bool
   */
  public function CheckTableExistence($table){
    if(empty($table)) {
      return false;
    }
    $result = mysqli_query($this->connection,"SELECT * FROM $table LIMIT 1");
    if (empty($result)) {
      return false;
    }
    return true;
  }

  /**
   * @param string $table
   * @param string $col
   *
   * @return bool
   */
  public function CheckColExistence($table,$col)
  {
    if(!$this->CheckTableExistence($table)) {
      return false;
    }

    $result = mysqli_query($this->connection,"SHOW COLUMNS FROM $table");
    if (!$result) {
      echo 'Could not run query: ' . mysqli_error();
      exit;
    }
    if (mysqli_num_rows($result) <= 0) {
      return false;
    }

    while ($row = mysqli_fetch_assoc($result)) {
      if($row['Field']==$col) {
        @mysqli_free_result($result);
        return true;
      }
    }
    @mysqli_free_result($result);
    return false;
  }

  /**
   * @return array
   */
  public function GetTables()
  {
    $tables = $this->SelectArr('show tables');
    $result = array();
    if(empty($tables)) {
      return $result;
    }

    foreach ($tables as $tab) {
      foreach ($tab as $key => $tabname) {
        $result[] = $tabname;
      }
    }

    return $result;
  }

  /**
   * @param string $table
   *
   * @return array|null
   */
  public function GetColArray($table)
  {
    if(!$this->CheckTableExistence($table)){
      return null;
    }

    $result = mysqli_query($this->connection,"SHOW COLUMNS FROM $table");
    if (!$result) {
      echo 'Could not run query: ' . mysqli_error();
      exit;
    }
    if (mysqli_num_rows($result) <= 0) {
      return null;
    }

    while ($row = mysqli_fetch_assoc($result)) {
      $ret[]=$row['Field'];
    }
    mysqli_free_result($result);
    if(empty($ret)) {
      return null;
    }

    return $ret;
  }

  /**
   * @param string $table
   *
   * @return null|array
   */
  public function GetColAssocArray($table)
  {
    if(!$this->CheckTableExistence($table)){
      return null;
    }

    $result = mysqli_query($this->connection,"SHOW COLUMNS FROM $table");
    if (!$result) {
      echo 'Could not run query: ' . mysqli_error();
      exit;
    }
    if (mysqli_num_rows($result) <= 0) {
      return null;
    }

    while ($row = mysqli_fetch_assoc($result)) {
      $ret[$row['Field']]='';
    }
    mysqli_free_result($result);
    if(empty($ret)) {
      return null;
    }

    return $ret;
  }

  /**
   * @param string $tablename
   * @param mixed  $pk
   * @param string $pkname
   * @param array  $ArrCols
   * @param bool   $escape
   */
  public function UpdateArr($tablename,$pk,$pkname,$ArrCols, $escape = false)
  {
    if(empty($ArrCols) || empty($tablename) || empty($pk) || empty($this->connection)) {
      return;
    }

    if(empty($pkname)) {
      $pkname = 'id';
    }
    if(!empty($this->columnCache[$this->dbname][$tablename])) {
      $zielspalten = $this->columnCache[$this->dbname][$tablename];
    }
    else{
      $zielspalten = $this->SelectArr("show columns from `$tablename`");
      $this->columnCache[$this->dbname][$tablename] = $zielspalten;
    }
    if(!empty($zielspalten)) {
      foreach($zielspalten as $val) {
        $ziel[$val['Field']] = true;
      }
    }
    $sql = "UPDATE `$tablename` SET ";
    foreach($ArrCols as $key=>$value) {
      if($key!=$pkname && (isset($ziel[$key]) || !$zielspalten)) {
        $sqla[] = $key." = '".($escape?$this->real_escape_string($value):$value)."' ";
      }
    }
    if(!empty($sqla)) {
      $sql .= implode(', ',$sqla)." WHERE `$pkname`='$pk' LIMIT 1";
      $this->Update($sql);
      if(!$this->error()) {
        return;
      }
    }

    foreach($ArrCols as $key=>$value) {
      if($key != $pkname){
        if($escape) {
          $value = $this->real_escape_string($value);
        }
        $this->Query("UPDATE `$tablename` SET `$key`='$value' 
        WHERE `$pkname`='$pk' LIMIT 1");
      }
    }
  }

  /**
   * @param string $tablename
   * @param string $pkname
   * @param array  $ArrCols
   * @param bool   $escape
   *
   * @return int
   */
  public function InsertArr($tablename,$pkname,$ArrCols, $escape = false)
  {
    if(empty($tablename)) {
      return null;
    }
    if(empty($pkname)) {
      $pkname = 'id';
    }
    // save primary than update
    $this->Query('INSERT INTO `'.$tablename.'` ('.$pkname.') VALUES (NULL)');
    $pk = (int)$this->GetInsertID();
    if(empty($pk) || empty($ArrCols)) {
      return $pk;
    }

    $this->UpdateArr($tablename, $pk, $pkname, $ArrCols, $escape);

    return $pk;
  }


  /**
   * get table content with specified cols
   *
   * @param string     $tablename
   * @param null|array $cols
   *
   * @return array|null
   */
  public function SelectTable($tablename,$cols){
    if(empty($tablename)) {
      return null;
    }
    $firstcol = true;
    $selection = '*';
    if(!empty($cols)) {
			$selection = '';
      foreach($cols as $value) {
        if(!$firstcol){
          $selection .= ',';
        }
        $selection .= $value;

        $firstcol=false;
      }
    }
 
    $sql = "SELECT $selection FROM $tablename";

    return $this->SelectArr($sql);
  }


  /**
   * @param string $query
   * @param bool   $async
   *
   * @return bool|mysqli_result|null
   */
  public function Query($query, $async = false) {
    if(stripos($query,'ALTER TABLE') !== false) {
      $this->columnCache[$this->dbname] = [];
    }
    if(empty($query) || empty($this->connection)){
      return null;
    }
    if(class_exists('DevTools')){
      DevTools::Add('','','Query',$query);
    }
    if($async) {
      return mysqli_query($this->connection, $query, MYSQLI_ASYNC);
    }
    $ret = mysqli_query($this->connection,$query);
    if(mysqli_errno($this->connection) === 1118 && stripos($query,'ALTER TABLE') !== false) {
      mysqli_query($this->connection, 'SET innodb_strict_mode = OFF;');
      $ret = mysqli_query($this->connection,$query);
    }

    return $ret;
  }

  /**
   * @deprecated Use Fetch_Assoc OR Fetch_Row insteed (can causes Problem with some PHP/MySQL-Versions)
   * @see DB::Fetch_Assoc()
   * @see DB::Fetch_Row()
   * @param mysqli_result $query SQL-Queryresult
   *
   * @return array|null return Next result as an associative, a numeric array, or both
   */
  public function Fetch_Array($query) {
    if(empty($query)) {
      return null;
    }

    return mysqli_fetch_array($query);
  }

  /**
   * @param mysqli_result $query SQL-Queryresult
   *
   * @return array|null return next result as an associative array
   */
  public function Fetch_Assoc($query) {
    if(empty($query)) {
      return null;
    }

    return mysqli_fetch_assoc($query);
  }

  /**
   * @param mysqli_result $query SQL-Queryresult
   *
   * @return array|null return next result as an enumerated array
   */
  public function Fetch_Row($query) {
    if(empty($query)) {
      return null;
    }

    return mysqli_fetch_row($query);
  }

  /**
   * Get Rowinformation of a Query-Result even if empty
   *
   * @param mysqli_result $query SQL-Queryresult
   *
   * @return array|null Row-Information
   */
  public function Fetch_Fields($query)
  {
    if(empty($query)) {
      return null;
    }
    return $query->fetch_fields();
  }

  /**
   * @param mysqli_result $sql
   * @param int           $offset
   *
   * @return bool|null
   */
  public function Data_Seek($sql,$offset=0)
  {
    if(empty($sql)) {
      return null;
    }
    return mysqli_data_seek($sql,$offset);
  }

  /**
   * @param string $TableName
   * @param string $IDFieldName
   * @param int    $IDToDuplicate
   *
   * @return int|null
   */
  public function MysqlCopyRow($TableName, $IDFieldName, $IDToDuplicate)
  {
    if(empty($TableName) || empty($IDFieldName) || empty($IDToDuplicate)) {
      return null;
    }

    $sql = "SELECT * FROM $TableName WHERE $IDFieldName = $IDToDuplicate";
    $result = @mysqli_query($this->connection,$sql);
    if(empty($result)) {
      return null;
    }

    $sql = "INSERT INTO $TableName SET ";
    $row = mysqli_fetch_array($result);
    $RowKeys = array_keys($row);
    $RowValues = array_values($row);
    $cRowKeys = $RowKeys?count($RowKeys):0;
    for ($i=3;$i<$cRowKeys;$i+=2) {
      if ($i!==3) {
        $sql .= ', ';
      }
      $sql .= $RowKeys[$i] . " = '" . $this->real_escape_string($RowValues[$i]) . "'";
    }
    @mysqli_query($this->connection,$sql);

    $id = $this->GetInsertID();
    if(empty($id)) {
      return null;
    }

    return $id;
  }

  /**
   * @param mixed $value
   *
   * @return string
   */
  public function real_escape_string($value)
  {
    return mysqli_real_escape_string($this->connection, $value);
  }

  /**
   * @return int
   */
  public function affected_rows()
  {
    return mysqli_affected_rows($this->connection);
  }

  /**
   * @return string
   */
  public function error()
  {
    return mysqli_error($this->connection);
  }

  /**
   * @return int
   */
  public function errno()
  {
    return mysqli_errno($this->connection);
  }

  /**
   * @param int $pid
   *
   * @return bool|mysqli_result
   */
  public function kill($pid) {
    if($pid <= 0) {
      return false;
    }
    return mysqli_query($this->connection, 'KILL QUERY '.$pid);
  }

  /**
   * @param bool $echo
   *
   * @return bool
   */
  public function LogIfError($echo = false)
  {
    $error = $this->error();
    if(!$error) {
      return false;
    }
    if(!empty($this->app)) {
      $this->app->erp->LogFile(mysqli_real_escape_string($this->connection, $error));
    }

    if($echo) {
      echo $error;
    }
    return true;
  }
}

