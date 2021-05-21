<?php
/**
 * Created by PhpStorm.
 * User: schmitt
 * Date: 13.8.18
 * Time: 11:32
 */


class PayoneSftpConnection
{
  /** @var resource  $connection*/
  private $connection;
  /** @var resource $sftp*/
  private $sftp;
  /** @var string */
  private $subdir = '';

  /**
   * UebertragungenSFTPConnection constructor.
   *
   * @param string $host
   * @param int $port
   *
   * @throws Exception
   */
  public function __construct($host, $port=22)
  {
    if(empty($port))
    {
      $port = 22;
    }

    if(!function_exists('ssh2_connect'))
    {
      throw new Exception('SSH2 not installed');
    }
    $this->connection = @ssh2_connect($host, $port);
    if (! $this->connection){
      throw new Exception("Could not connect to $host on port $port.");
    }
  }

  /**
   * @param string $username
   * @param string $password
   *
   * @return bool
   * @throws Exception
   */
  public function login($username, $password)
  {
    if (! @ssh2_auth_password($this->connection, $username, $password)){
      throw new Exception(
        'Could not authenticate with username '.$username  .
        (empty($password)?' and no password': ' and password ***.')
      );
    }
    $this->sftp = @ssh2_sftp($this->connection);
    if (! $this->sftp){
      throw new Exception("Could not initialize SFTP subsystem.");
    }
    return true;
  }

  /**
   * @param string $local_file
   * @param string $remote_file
   *
   * @return bool
   * @throws Exception
   */
  public function uploadFile($local_file, $remote_file)
  {
    $sftp = $this->sftp;
    $stream = @fopen('ssh2.sftp://'.(int)$sftp.$remote_file, 'w');
    if (! $stream){
      throw new Exception("Could not open file: $remote_file");
    }
    $data_to_send = @file_get_contents($local_file);
    if ($data_to_send === false){
      throw new Exception("Could not open local file: $local_file.");
    }
    if (@fwrite($stream, $data_to_send) === false){
      throw new Exception("Could not send data from file: $local_file.");
    }
    @fclose($stream);
    return true;
  }

  /**
   * @param string $remote_file
   *
   * @return array
   * @throws Exception
   */
  public function scanFilesystem($remote_file) {
    $sftp = $this->sftp;
    $dir = 'ssh2.sftp://'.(int)$sftp.$remote_file;
    $tempArray = array();
    $handle = @opendir($dir);
    if(empty($handle))
    {
      throw new Exception("Could not read dir: $remote_file.");
    }
    if(!empty($handle)){
      // List all the files
      while (false !== ($file = readdir($handle))) {
        if(substr("$file", 0, 1) !== '.'){
          if(is_dir($file)){
//                $tempArray[$file] = $this->scanFilesystem("$dir/$file");
          }else{
            $tempArray[] = $file;
          }
        }
      }
      closedir($handle);
    }
    return $tempArray;
  }

  /**
   * @param string $remote_file
   * @param string $local_file
   *
   * @return bool
   * @throws Exception
   */
  public function receiveFile($remote_file, $local_file)
  {
    $sftp = $this->sftp;
    $stream = @fopen('ssh2.sftp://'.(int)$sftp.$remote_file, 'r');
    if (! $stream){
      throw new Exception("Could not open file: $remote_file");
    }
    $contents = '';
    $size = filesize('ssh2.sftp://'.(int)$sftp.$remote_file);
    if($size <= 0 || $size > 8192) {
      $size = 8192;
    }
    while(($content = fread($stream, $size)) !== false) {
      $contents .= $content;
      if($content ==='') {
        break;
      }
    }
    file_put_contents ($local_file, $contents);
    @fclose($stream);
    return true;
  }

  /**
   * @param $remote_file
   *
   * @return bool
   * @throws Exception
   */
  public function deleteFile($remote_file){
    $sftp = $this->sftp;
    if(!@unlink('ssh2.sftp://'.(int)$sftp.$remote_file)) {
      throw new Exception("Could not delete file: $remote_file");
    }
    return true;
  }
}

/**
 * sofortimport for 'payone'
 * import from .csv file at specified location
 *
 * Class payone
 */
class payone
{
  const COL_DIVIDER = ";";
  const DEFAULT_N_DAYS = 5;
  const DEFAULT_FILE = '/tmp/default-payone.csv';

  /** @var Application $app */
  private $app;
  private $requiredHeaders = ['payment', 'currency', 'timestamp', 'txid'];
  private $optionalHeaders = ['reference', 'clearingtype', 'clearingsubtype', 'userid'];

  /**
   * the file handle
   *
   * @var resource
   */
  private $handle = null;

  /**
   * just as fallback, if all fails
   * on delete, the file handle should
   * be closed
   */
  public function __destruct()
  {
    $this->closeFile();
  }

  protected function FTPisDir(&$conn_id, $dir ) {
    $akt = ftp_pwd( $conn_id );
    if ( @ftp_chdir( $conn_id, $dir ) ) {
      @ftp_chdir( $conn_id, $akt );
      return true;
    }

    return false;
  }

  /**
   * @param string $file
   * @param bool   $ssl
   * @param string $url
   * @param int    $port
   * @param string $username
   * @param string $pw
   * @param string $subdir
   *
   * @return bool
   */
  protected function FTPDeleteFile($file, $ssl, $url, $port, $username, $pw, $subdir = '')
  {
    $aktfolder = '';
    if(empty($port)) {
      $port = 21;
    }
    if($ssl) {
      $conn_id = ftp_ssl_connect($url, $port);
    }
    else{
      $conn_id = ftp_connect($url, $port);
    }
    if(!$conn_id) {
      $this->fehler[] = 'FTP-Verbindung fehlgeschlagen zu '.$url.':'.$port;
      return false;
    }
    $login_result = ftp_login($conn_id, $username, $pw);
    if($login_result) {
      if(!empty($subdir) && $subdir !== '/' && $subdir !== '.') {
        $aktfolder = @ftp_pwd($conn_id);
        if(!@ftp_chdir($conn_id, $subdir)) {
          ftp_close($conn_id);
          $this->fehler[] = $subdir.' auf FTP-Server nicht gefunden';
          return false;
        }
      }
      if (ftp_delete($conn_id, $file)) {
        if(!empty($subdir) && $subdir !== '/' && $subdir !== '.' && !empty($aktfolder))
        {
          @ftp_chdir($conn_id, $aktfolder);
        }
        ftp_close($conn_id);
        return true;
      }
    }
    else {
      $this->fehler[] = 'FTP-Login fehlgeschlagen zu '.$url.':'.$port.' mit User '.$username;
    }
    ftp_close($conn_id);
    return false;
  }

  /**
   * @param bool   $ssl
   * @param string $url
   * @param string $port
   * @param string $username
   * @param string $pw
   * @param string $subdir
   * @param string $prefix
   *
   * @return array|bool
   */
  protected function FTPGetFileList($ssl, $url, $port, $username, $pw, $subdir = '', $prefix = '')
  {
    if(empty($port)) {
      $port = 21;
    }
    $port = (int)$port;
    $aktfolder = '';
    if($ssl) {
      $conn_id = ftp_ssl_connect($url, $port);
    }
    else{
      $conn_id = ftp_connect($url, $port);
    }
    if(!$conn_id) {
      $this->fehler[] = 'Verbindung fehlgeschlagen zu '.$url .':'. $port;
      return false;
    }
    $login_result = ftp_login($conn_id, $username, $pw);
    if($login_result) {
      ftp_pasv($conn_id, true);

      if(!empty($subdir) && $subdir !== '/' && $subdir !== '.') {
        $aktfolder = ftp_pwd($conn_id);
        if(!@ftp_chdir($conn_id, $subdir)) {
          $this->fehler[] = $subdir.' nicht gefunden';
          ftp_close($conn_id);
          return false;
        }
      }
      $Liste = ftp_nlist($conn_id, "-dF ".".");
      if(empty($Liste) || count($Liste)<= 1) {
        $Liste = ftp_nlist($conn_id, ".");
      }
      if($Liste !== false) {
        if(!empty($Liste)) {
          foreach($Liste as $k => $v)  {
            if(strpos($v,'./') === 0) {
              $v = substr($v, 2);
              $Liste[$k] = $v;
            }
            if($v === '' || $v === '.' || $v === '..') {
              unset($Liste[$k]);
              continue;
            }
            if($prefix != '') {
              if(stripos($v, $prefix) !== 0) {
                unset($Liste[$k]);
                continue;
              }
              if(substr($v, -1) === '/' || (strpos($v,'.') === false && $this->FTPisDir($conn_id, $v))) {
                unset($Liste[$k]);
                continue;
              }
            }
            if(substr($v,-1) === '/' && $this->FTPisDir($conn_id, $v)) {
              unset($Liste[$k]);
              continue;
            }
          }
        }
        if(!empty($subdir) && $subdir !== '/' && $subdir !== '.') {
          @ftp_chdir($conn_id,$aktfolder);
        }
        ftp_close($conn_id);
        return $Liste;
      }
      $this->fehler[] = "Fehler beim Lesen von $url $subdir";
    }
    else{
      $this->fehler[] = 'FTP Login fehlgeschlagen fehlgeschlagen';
      ftp_close($conn_id);
      return false;
    }
    ftp_close($conn_id);
    return false;
  }

  /**
   * @param string $from
   * @param string $to
   * @param bool   $ssl
   * @param string $url
   * @param string $port
   * @param string $username
   * @param string $pw
   * @param string $subdir
   * @param string $prefix
   * @param bool   $binary
   *
   * @return bool
   */
  protected function GetFile($from, $to, $ssl, $url, $port, $username, $pw, $subdir = '', $prefix = '', $binary = false)
  {
    $aktfolder = '';
    $this->wasdir = false;
    if($from === '' || strpos($from,'.') === 0) {
      $this->fehler[] = 'ungültiger Dateiname '.$from;
      return false;
    }
    if($to === '' || strpos($to,'.') === 0) {
      $this->fehler[] = 'ungültiger Dateiname '.$to;
      return false;
    }
    if(empty($port)) {
      $port = 21;
    }
    $port = (int)$port;
    if($ssl) {
      $conn_id = ftp_ssl_connect($url, $port);
    }
    else{
      $conn_id = ftp_connect($url, $port);
    }
    if(!$conn_id) {
      $this->fehler[] = 'Verbindung fehlgeschlagen zu '.$url.':'.$port;
      return false;
    }
    $login_result = ftp_login($conn_id, $username, $pw);
    if($login_result) {
      ftp_pasv($conn_id, true);
      if(!empty($subdir) && $subdir !== '/' && $subdir !== '.')
      {
        $aktfolder = @ftp_pwd($conn_id);
        if(!@ftp_chdir($conn_id, $subdir))
        {
          $this->fehler[] = $from." nicht gefunden";
        }
      }
      $Liste = ftp_nlist($conn_id, '.');
      if($Liste !== false)
      {
        if(!empty($Liste))
        {
          foreach($Liste as $k => $v)  {
            if($v == $from) {
              if(@ftp_get($conn_id, $to, $from, ($binary?FTP_BINARY:FTP_ASCII)))
              {
                if(file_exists($to)) {
                  if(!empty($subdir) && $subdir !== '/' && $subdir !== '.' && $aktfolder != '') {
                    @ftp_chdir($conn_id,$aktfolder);
                  }
                  ftp_close($conn_id);
                  return true;
                }
                if(@ftp_chdir($conn_id, $from))
                {
                  @ftp_chdir($conn_id,'..');
                  $this->wasdir = true;
                }
                if(!empty($subdir) && $subdir !== '/' && $subdir !== '.' && $aktfolder != '')
                {
                  @ftp_chdir($conn_id,$aktfolder);
                }
                ftp_close($conn_id);
                return false;
              }
              if(@ftp_chdir($conn_id, $from)) {
                @ftp_chdir($conn_id,'..');
                $this->wasdir = true;
              }
              else{
                $this->fehler[] = "Fehler beim Herunterladen von " . $from;
              }
              if(!empty($subdir) && $subdir !== '/' && $subdir !== '.' && $aktfolder != '') {
                @ftp_chdir($conn_id,$aktfolder);
              }
              ftp_close($conn_id);
              return false;
            }
          }
          if(!empty($from) && strpos($from, '..') === false && $from[0] !== '.'
            && @ftp_get($conn_id, $to, $from, ($binary?FTP_BINARY:FTP_ASCII))) {
            if(file_exists($to)) {
              if(!empty($subdir) && $subdir !== '/' && $subdir !== '.' && $aktfolder != '') {
                @ftp_chdir($conn_id,$aktfolder);
              }
              ftp_close($conn_id);
              return true;
            }
            if(@ftp_chdir($conn_id, $from)) {
              @ftp_chdir($conn_id,'..');
              $this->wasdir = true;
            }
            if(!empty($subdir) && $subdir !== '/' && $subdir !== '.' && $aktfolder != '') {
              @ftp_chdir($conn_id,$aktfolder);
            }
            ftp_close($conn_id);
          }
          $this->fehler[] = $from.' nicht gefunden';
        }
      }
      else{
        $this->fehler[] = "Fehler beim Lesen von $url $subdir";
      }
    }
    else{
      $this->fehler[] = 'Login fehlgeschlagen';
    }
    if(!empty($subdir) && $subdir !== '/' && $subdir !== '.' && $aktfolder != '')
    {
      @ftp_chdir($conn_id,$aktfolder);
    }
    ftp_close($conn_id);
    return false;
  }

  /**
   * Import a .csv file from payone
   * all options in $config array are optional.
   * The default number of days is 5 (DEFAULT_N_DAYS)
   * and the default file name is stored in
   * '/tmp/payone.csv' (DEFAULT_FILE)
   *
   * The arguments 'PATH' or 'FILE' set the used file,
   * the keys 'API_DAYS', 'DAYS' or 'TAGE' override the
   * number of days to look into the past
   *
   * @param array       $config
   * @param Application $app
   *
   * @return array|string
   * @throws Exception
   */
  function Import($config, $app)
  {
    $this->app = $app;
    $csv = [];
    $index = [];
    $description = [];
    $config = (array) $config;

    try {
      $filename = $this->getFileName($config);
    }
    catch (Exception $e) {
      $filename = '';
    }
    list($ftphost, $ftpport, $ftpuser, $ftppassword, $ftpdebug, $ftpssl, $ftpsubdir, $sftp) = $this->getFtp($config);

    if($ftphost) {

      if(empty($filename)){
        $filename = date('YmdHis').'.csv';
      }
      //$folder = $this->app->erp->GetTMP(). rtrim($this->app->Conf->WFuserdata,'/').'/payone';
      $folder = $this->app->erp->GetTMP().'payone';
      if(!file_exists($folder)) {
        if(!mkdir($folder) && !is_dir($folder)) {
          $this->app->erp->LogFile($folder.' konnte nicht erstellt werden');
        }
      }
      $folder = $folder.'/'.$this->app->Conf->WFdbname;
      if(!file_exists($folder)) {
        if(!mkdir($folder) && !is_dir($folder)) {
          $this->app->erp->LogFile($folder.' konnte nicht erstellt werden');
        }
      }
      $tofile = $folder.'/'.$filename;


      if($ftphost) {
        if($sftp) {
          try {
            $connection = new PayoneSftpConnection($ftphost, $ftpport);
          }
          catch(Exception $e) {
            $this->fehler[] = $e->getMessage();
            $this->app->erp->LogFile($e->getMessage());
            return '';
          }
          try {
            $connection->login($ftpuser, $ftppassword);
          }
          catch(Exception $e) {
            $this->fehler[] = $e->getMessage();
            $this->app->erp->LogFile($e->getMessage());
            return '';
          }
          $list = $connection->scanFilesystem($ftpsubdir);
        }
        else {
          $list = $this->FTPGetFileList($ftpssl, $ftphost, $ftpport, $ftpuser, $ftppassword, $ftpsubdir);
        }
        if(!empty($list)) {
          foreach($list as $file) {
            $fullFile = $file;
            if(!empty($ftpsubdir)) {
              $fullFile = rtrim($ftpsubdir,'/').'/'.$file;
            }
            if($sftp) {
              try {
                $getFile = $connection->receiveFile($fullFile, $tofile);
              }
              catch (Exception $e) {
                $this->fehler[] = $e->getMessage();
                $this->app->erp->LogFile($e->getMessage());
                continue;
              }
            }
            else {
              $getFile = $this->GetFile($file, $tofile, $ftpssl, $ftphost, $ftpport, $ftpuser, $ftppassword, $ftpsubdir);
            }

            if($getFile) {
              if(!$ftpdebug) {
                if($sftp){
                  try {
                    $slash = '';
                    if(trim($this->subdir) !== '' && substr($this->subdir,-1) !== '/'){
                      $slash = '/';
                    }
                    $connection->deleteFile($this->subdir.$slash.$file);
                  }
                  catch (Exception $e) {
                    $this->fehler[] = $e->getMessage();
                    $this->app->erp->LogFile($e->getMessage());
                  }
                }
                else{
                  $this->FTPDeleteFile($file, $ftpssl, $ftphost, $ftpport, $ftpuser, $ftppassword, $ftpsubdir);
                }
              }
              $filename = $tofile;
              break;
            }
          }
        }
      }
    }
    if(!empty($filename)){
      $this->openFile($filename);
    }
    $completeHeader = [];
    while(!feof($this->handle)) {

      $line = $this->readCSVLineAsArray();

      if (!$line || (is_array($line) && count($line) <= 1 && empty(reset($line)))) {
        continue;
      }

      /*
       * Use the first line as header.
       * Extract some index numbers defined in $requiredHeaders array into $index;
       * Extract some index numbers defined in $descriptionHeaders array into $description;
       */
      if (empty($index)) {
        $index = $this->extractIndexFromHeaderLine($line, $this->requiredHeaders);
        $description = $this->extractIndexFromHeaderLine($line, $this->optionalHeaders);
        $completeHeader = array_flip($line);
        $completeHeader = array_change_key_case($completeHeader, CASE_LOWER);
        try {
          $this->checkMissingArrayValues($index, $this->requiredHeaders);
        }
        catch(Exception $e) {
          if($ftpdebug) {
            $this->app->erp->LogFile(['Error '.$e->getMessage().' in File '.$filename, $line]);
          }
          throw new Exception($e->getMessage().' in File '.$filename, $e->getCode());
        }
        continue;
      }

      try {
        $data = $this->getDataOfInterest($line, $index);
      }
      catch (Exception $e) {
        if($ftpdebug) {
          $this->app->erp->LogFile(['Error '.$e->getMessage().' in File '.$filename, $line]);
        }
        throw new Exception($e->getMessage().' in File '.$filename, $e->getCode());
      }

      try {
        $desc = $this->getDataOfInterest($line, $description);
      }
      catch (Exception $e) {
        if($ftpdebug) {
          $this->app->erp->LogFile(['Error '.$e->getMessage().' in File '.$filename, $line]);
        }
        throw new Exception($e->getMessage().' in File '.$filename, $e->getCode());
      }

      /*
       * The timestamp is in the range, of [today - Number-of-days, today]
       * so, import this line
       */
      $data = array_merge($data, $desc);
      foreach($completeHeader as $col => $indHeader) {
        if(!isset($data[$col]) && !empty($line[$indHeader])) {
          $data[$col] = $line[$indHeader];
        }
      }
      $csv[] = $data;
    }

    $this->closeFile();
    if ($this->getDeleteFlag($config)) {
      /*
       * Note: there are no checks about the given file!
       * At this point the file exists and is readable
       * other checks aren't performed so this method
       * may deletes necessary files!
       */
      @unlink($filename);
    }

    $header = array_unique(array_values(array_merge($index, $description, array_keys($completeHeader))));
    $header = implode(self::COL_DIVIDER, $header);

    $csv = array_map([$this, 'implodeCSVLine'], $csv);

    $csv = array_merge([$header], $csv);
    $csv = implode("\n", $csv);

    return $csv;
  }

  /**
   * copied and modified from 'ImportKontoauszug' in 'class.erpapi'
   * used case "stripe" in switch statement
   *
   * @param string $csv the csv 'file' to import
   * @param int $konto the konto id
   * @param $app
   * @return array($inserted, $duplicate);
   * @throws Exception
   */
  public function ImportKontoauszug($csv, $konto, $app)
  {
    $this->app = $app;
    $inserted = 0;
    $duplicate = 0;

    if (!is_string($csv)) {
      $type = gettype($csv);
      throw new Exception(sprintf(
        'Expected csv as string, got \'%s\'', $type
      ));
    }

    // fix values
    $gebuehr = 0;
    $gegenkonto = "";
    $stamp = time();
    $userName = $this->app->User->GetName();
    $userName = $this->app->DB->real_escape_string($userName);

    $csv = $this->explodeCSVLines($csv);
    if (empty($csv)) {
      return array($inserted, $duplicate);
    }
    $count = count($csv);

    $csv = array_map([$this, 'explodeCSVLine'], $csv);

    $header = $this->extractIndexFromHeaderLine($csv[0], $this->requiredHeaders);
    try {
      $this->checkMissingArrayValues($header, $this->requiredHeaders);
    }
    catch(Exception $e) {
      $this->app->erp->LogFile(['Error '.$e->getMessage(), $header]);
      throw new Exception($e->getMessage().' in '.json_encode($header), $e->getCode());
    }

    $completeHeader = array_flip($csv[0]);
    $completeHeader = array_change_key_case($completeHeader, CASE_LOWER);
    /*
     * skip first row -> 'header' line
     */
    for ($i = 1; $i < $count; $i++) {
      if(count($csv[$i]) <= 1) {
        continue;
      }
      $data = $this->getDataOfInterest($csv[$i], $header);

      /*
       * translate / extract
       */
      $betrag = $data['payment'];
      $vorgang = $data['txid'];
      $buchung = $data['timestamp'];
      $waehrung = $data['currency'];

      foreach(
        [
          'reference',
          'clearingtype',
          'clearingsubtype',
          'userid',
          'customerid',
          'email',
          'company',
          'firstname',
          'lastname',
          'street',
          'zip',
          'city',
          'country'
        ] as $col) {
        if(!empty($data[$col])) {
          $vorgang .= ' '.$data[$col];
        }
        elseif(!empty($completeHeader[$col]) && !empty($csv[$i][$completeHeader[$col]])) {
          $vorgang .= ' '.$csv[$i][$completeHeader[$col]];
        }
      }
      // free some unnecessary memory
      unset($csv[$i], $data);


      $buchung = $this->app->DB->real_escape_string( $buchung);
      $buchung = str_replace('"','', $buchung);
      $buchung = explode(' ', $buchung);
      $buchung = $buchung[0];
      if(is_numeric($buchung) && (String)(int)$buchung === (String)$buchung) {
        $buchung = date('Y-m-d', $buchung);
      }

      $vorgangUtf8 = iconv('UTF-8', 'UTF-8', $vorgang);
      if(md5($vorgangUtf8) !== md5($vorgang)){
        $vorgang = utf8_encode($vorgang);
      }
      $vorgang = $this->app->DB->real_escape_string($vorgang);
      $vorgang = str_replace('"','',$vorgang);

      $betrag = $this->app->DB->real_escape_string( $betrag);

      $waehrung = $this->app->DB->real_escape_string($waehrung);

      // haben vs. soll
      list($haben, $soll) = str_replace(',','.', $betrag) > 0
        ? array($betrag, "")
        : array("", $betrag);

      // hash over some values
      $pruefsumme = md5(serialize(array($buchung, $vorgang, $soll, $haben, $waehrung)));

      $sql = "SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1";
      $check = $app->DB->Select($sql);
      if($check > 0) {
        $duplicate++;
        continue;
      }
      $soll = str_replace(',','.', $soll);
      $haben = str_replace(',','.', $haben);
      $sql = "INSERT INTO kontoauszuege (
          konto,
          buchung,
          vorgang,
          soll,
          haben,
          gebuehr,
          waehrung,
          fertig,
          bearbeiter,
          pruefsumme,
          importgroup,
          originalbuchung,
          originalvorgang,
          originalsoll,
          originalhaben,
          originalgebuehr,
          originalwaehrung,
          gegenkonto
        ) VALUE (
          '$konto',
          '$buchung',
          '$vorgang',
          '$soll',
          '$haben',
          '$gebuehr',
          '$waehrung',
          0,
          '".$userName."',
          '$pruefsumme',
          '$stamp',
          '$buchung',
          '$vorgang',
          '$soll',
          '$haben',
          '$gebuehr',
          '$waehrung',
          '$gegenkonto')";

      $app->DB->Insert($sql);
      $newid = $app->DB->GetInsertID();
      $app->DB->Update("UPDATE kontoauszuege SET sort='$newid' WHERE id='$newid' LIMIT 1");
      $inserted++;
    }

    return array($inserted, $duplicate);
  }

  /**
   * @param string $filename
   *
   * @throws Exception
   */
  private function openFile($filename)
  {
    /*
     * If the open fails, an error of level E_WARNING is generated.
     */
    $handle = @fopen($filename,"r");
    if (!$handle) {
      throw new Exception(sprintf(
        'Die Datei \'%s\' kann nicht geöffnet werden!', $filename
      ));
    }
    $this->handle = $handle;
  }

  /**
   * Close File if Handle open
   */
  private function closeFile()
  {
    if ($this->handle) {
      fclose($this->handle);
      $this->handle = null;
    }
  }

  /**
   * Read one line, explode and trim all values.
   *
   * @return array
   */
  private function readCSVLineAsArray()
  {
    $line = fgets($this->handle);
    $line = trim($line);
    $line = explode(self::COL_DIVIDER, $line);

    $line = array_map('trim', $line);
    $line = array_map([$this, 'trimDoubleQuotes'], $line);

    return $line;
  }

  /**
   * get the number of days as positive integer,
   * they can be set via the config keys
   * 'API_DAYS', 'DAYS' or 'TAGE'
   *
   * @param $config
   * @return int
   */
  private function getFirstDate($config)
  {
    $days = self::DEFAULT_N_DAYS;
    if (array_key_exists('API_DAYS', $config) && is_numeric($config['API_DAYS'])) {
      $days = $config['API_DAYS'];
    } elseif (array_key_exists('DAYS', $config) && is_numeric($config['DAYS'])) {
      $days = $config['DAYS'];
    } elseif (array_key_exists('TAGE', $config) && is_numeric($config['TAGE'])) {
      $days = $config['TAGE'];
    }

    if (!is_numeric($days)) {
      $days = self::DEFAULT_N_DAYS;
    }

    $days = abs($days);

    $time = time();
    if ($days > 0) {
      $time = strtotime("-{$days} days", $time);
    }

    // var_dump($time, date('Y-m-d', $time), $days);

    return $time;
  }

  /**
   * @param array $config
   *
   * @return array
   */
  private function getFtp($config)
  {
    $host = '';
    $user = '';
    $port = '';
    $pw = '';
    $debug = '';
    $subdir = '';
    $ssl = false;
    $sftp = false;
    if(!empty($config['SFTP'])) {
      $sftp = true;
    }
    if(!empty($config['FTP_HOST'])) {
      $host = $config['FTP_HOST'];
    }
    if(!empty($config['FTP_USERNAME'])) {
      $user = $config['FTP_USERNAME'];
    }
    if(!empty($config['FTP_PASSWORD'])) {
      $pw = $config['FTP_PASSWORD'];
    }
    if(!empty($config['FTP_PORT'])) {
      $port = $config['FTP_PORT'];
    }
    if(!empty($config['FTP_DEBUG'])) {
      $debug = $config['FTP_DEBUG'];
    }
    if(!empty($config['FTP_SSL'])) {
      $ssl = $config['FTP_SSL'];
    }
    if(!empty($config['FTP_SUBDIR'])) {
      $subdir = $config['FTP_SUBDIR'];
      $this->subdir = $subdir;
    }
    return [$host, $port, $user, $pw, $debug, $ssl, $subdir, $sftp];
  }

  /**
   * should we delete the given file after processing?
   * default is false
   *
   * @param $config
   * @return bool
   */
  private function getDeleteFlag($config)
  {
    $delete = array_key_exists('API_DELETE', $config)
      ? $config['API_DELETE']
      : false;
    $delete = array_key_exists('DELETE', $config)
      ? $config['DELETE']
      : $delete;
    $delete = array_key_exists('RM', $config)
      ? $config['RM']
      : $delete;

    return (bool)$delete;
  }

  /**
   * grab the file name, throw an exception
   * if not present, invalid or not readable
   *
   * @param $config
   * @return string the file name
   * @throws Exception
   */
  private function getFileName($config)
  {
    /*
     * allow 'PATH' or 'FILE' as key to the .csv file
     * as fallback if that keys aren't present use
     * the value behind 'DEFAULT_FILE'
     */

    $file = self::DEFAULT_FILE;
    if (array_key_exists('PATH', $config)) {
      $file = (string) $config['PATH'];
    }
    if (array_key_exists('FILE', $config)) {
      $file = (string) $config['FILE'];
    }

    if (!$file) {
      throw new Exception('Keine .csv Datei angegeben');
    }

    if (!$this->endsWith($file, '.csv')) {
      throw new Exception('Keine .csv Datei angegeben');
    }
    if (strpos($file, '..')) {
      throw new Exception(sprintf(
        'Der Dateiname \'%s\' ist nicht erlaubt.', $file
      ));
    }
    if (!file_exists($file) || !is_file($file)) {
      throw new Exception(
        sprintf('Die Datei \'%s\' existiert nicht!', $file
      ));
    }
    if (!is_readable($file)) {
      throw new Exception(sprintf(
        'Die Datei \'%s\' kann nicht gelesen werden!', $file
      ));
    }
    return (string) $file;
  }

  /**
   * @param array $line
   *
   * @return string
   */
  private function implodeCSVLine($line)
  {
    return implode(self::COL_DIVIDER, $line);
  }

  /**
   * @param array|string $lines
   *
   * @return array|false
   */
  private function explodeCSVLines($lines)
  {
    if (is_array($lines)) {
      $lines = implode("\n", $lines);
    }

    $lines = preg_split("/(\r\n)+|(\n|\r)+/", $lines);

    return $lines;
  }

  /**
   * @param string $line
   *
   * @return array
   */
  private function explodeCSVLine($line)
  {
    $line = str_replace('"','', $line);
    $line = explode(self::COL_DIVIDER, $line);

    return $line;
  }

  /**
   * @param string $haystack
   * @param string $needle
   *
   * @return bool
   */
  private function endsWith($haystack, $needle)
  {
    $length = strlen($needle);

    return $length === 0 ||
      (substr($haystack, -$length) === $needle);
  }

  /**
   * Should we append the line?
   *
   * compare the date from given line with
   * the first allowed date
   *
   * @see https://stackoverflow.com/a/3847762 for date comparison
   * @param array|string|int|bool $date
   * @param int $firstDate timestamp
   * @return bool
   */
  private function timestampInRangeOfInterest($date, $firstDate)
  {
    if (is_array($date)) {
      $date = $date['timestamp'];
    }
    if(!is_numeric($date) && (String)(int)$date !== (String)$date) {
      $date = strtotime($date);
    }
    if (!$date || $date == -1) {
      /*
       * Returns a timestamp on success, FALSE otherwise.
       * Previous to PHP 5.1.0, this function would return -1 on failure.
       */
      return false;
    }

    return $date >= $firstDate;
  }

  /**
   * Read the header line and return
   * an array with index => name pairs like:
   *
   * array (
   *  2 => 'timestamp',
   *  4 => 'payment',
   *  7 => 'currency',
   * )
   *
   * @param array $line
   * @param array $required the required values
   * @return array
   */
  private function extractIndexFromHeaderLine($line, $required)
  {
    $tmp = array_flip($line);
    $tmp = array_change_key_case($tmp, CASE_LOWER);

    $index = array_flip($required);
    $index = array_intersect_key($tmp, $index);

    $index = array_flip($index);
    ksort($index);

    return $index;
  }

  /**
   * Remove the double quotes from each value
   *
   * @param string $line
   * @return string
   */
  private function trimDoubleQuotes($line)
  {
    return trim($line, '"');
  }

  /**
   * Throw an exception, is one or more required array values
   * are missing.
   *
   * @param array $array
   * @param array $required
   * @throws Exception
   */
  private function checkMissingArrayValues($array, $required)
  {
    foreach ($required as $req) {
      if (!in_array($req, $array)) {
        throw new Exception(sprintf(
          'Could not find column \'%s\' in header line.', $req
        ));
      }
    }
  }

  /**
   * Extract the data of interest.
   *
   * $indexes is required as array with
   * index => name values like:
   *
   * array (
   *  2 => 'timestamp',
   *  4 => 'payment',
   *  7 => 'currency',
   * )
   *
   * returns an array like:
   *
   * array (
   *  'timestamp' => '2019-01-02 14:29:31',
   *  'payment' => '189,90',
   *  'currency' => 'EUR',
   * )
   *
   * @param array $line
   * @param array $indexes
   * @return array
   * @throws Exception
   */
  private function getDataOfInterest($line, $indexes)
  {
    $tmp = array_intersect_key($line, $indexes);
    ksort($tmp);

    /*
     * Check if an required index is missing.
     */
    $missed = array_diff_key($indexes, $line);
    if (!empty($missed)) {
      $cols = count($missed) < 2 ? 'column' : 'columns';
      $missed = implode(', ', $missed);
      throw new Exception(sprintf(
        'Could not find %s \'%s\'', $cols, $missed
      ));
    }

    /*
     * Merge column name with it's value
     */
    $tmp = array_combine($indexes, $tmp);

    return $tmp;
  }
}

