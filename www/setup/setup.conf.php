<?php
	$config['postinstall'] = true;

	/* ----------------- STEP 1 ----------------- */	
	$setup[1]['configfile'] = "user.inc.php";
	$setup[1]['description'] = 'Um dieses Setup auszuf&uuml;hren muss der Ordner <i>conf</i> Schreibrechte besitzen. Wenn diese passen klicken Sie auf ok (Diese Meldung dann ignorieren).';
	$setup[1]['action'] = "CheckDirRights";
	
	/* ----------------- STEP 2 ----------------- */
	
	$setup[2]['description'] = 'Schritt 2 - Datenbank-Einstellungen';
	$setup[2]['configfile'] = "user.inc.php";

	$setup[2]['fields']['WFdbhost']['text'] = "Host";
	$setup[2]['fields']['WFdbhost']['default'] = "localhost";
	$setup[2]['fields']['WFdbname']['default'] = "Datenbank";
	$setup[2]['fields']['WFdbuser']['default'] = "Benutzername";
	$setup[2]['fields']['WFdbpass']['text'] = "Passwort";
	$setup[2]['fields']['WFdbpass']['type'] = "password";

	$setup[2]['action'] = "CheckDatabase";

	/* ----------------- STEP 3 ----------------- */
	
	$setup[3]['description'] = 'Schritt 3 - Pfad f&uuml;r Dateien';
	$setup[3]['configfile'] = "user.inc.php";

	$setup[3]['fields']['WFuserdata']['text'] = "Userdata-Ordner";
	$setup[3]['fields']['WFuserdata']['default'] = str_replace("www\setup","userdata",str_replace("www/setup","userdata",getcwd()));
  
	$setup[3]['fields']['MainData']['invisible'] = "true";
	$setup[3]['fields']['MainData']['readonly'] = "true";
	$setup[3]['fields']['MainData']['sql'] = "../../database/struktur.sql";

  /*
	$setup[3]['fields']['InitialData']['invisible'] = "true";
	$setup[3]['fields']['InitialData']['readonly'] = "true";
	$setup[3]['fields']['InitialData']['sql'] = "../../database/init.sql";*/
/*
	$setup[3]['fields']['MainData']['invisible'] = "true";
	$setup[3]['fields']['MainData']['readonly'] = "true";
	$setup[3]['fields']['MainData']['sql'] = "../../database/main.sql";

	$setup[3]['fields']['InitialData']['invisible'] = "true";
	$setup[3]['fields']['InitialData']['readonly'] = "true";
	$setup[3]['fields']['InitialData']['sql'] = "../../database/initial.sql";*/
  $setup[3]['action'] = "CreateUserdata";

	/* ----------------- STEP 4 ----------------- */

	$setup[4]['description'] = 'Schritt 4 - Testdaten';
	$setup[4]['configfile'] = "user.inc.php";
  $setup[4]['fields']['BeispielTpl']['text'] = "Testdaten einspielen (mit Beispiel Artikel, Adressen usw.)";
	$setup[4]['fields']['BeispielTpl']['type'] = "checkbox";
	$setup[4]['fields']['BeispielTpl']['value'] = "1";
	$setup[4]['fields']['BeispielTpl']['readonly'] = "true";
	$setup[4]['fields']['BeispielTpl']['sql'] = "../../database/beispiel.sql";
  
/*
	$setup[4]['fields']['MailTpl']['text'] = "E-Mail Templates einspielen";
	$setup[4]['fields']['MailTpl']['type'] = "checkbox";
	$setup[4]['fields']['MailTpl']['value'] = "1";
	$setup[4]['fields']['MailTpl']['readonly'] = "true";
	$setup[4]['fields']['MailTpl']['sql'] = "../../database/emailtemplates.sql";

	$setup[4]['fields']['DhlZones']['text'] = "DHL-Zonen einspielen";
	$setup[4]['fields']['DhlZones']['type'] = "checkbox";
	$setup[4]['fields']['DhlZones']['value'] = "1";
	$setup[4]['fields']['DhlZones']['readonly'] = "true";
	$setup[4]['fields']['DhlZones']['sql'] = "../../database/dhlzones.sql";
	
	$setup[4]['fields']['Testdata']['text'] = "Mustershop-Datens&auml;tze einspielen";
	$setup[4]['fields']['Testdata']['type'] = "checkbox";
	$setup[4]['fields']['Testdata']['value'] = "1";
	$setup[4]['fields']['Testdata']['readonly'] = "true";
	$setup[4]['fields']['Testdata']['sql'] = "../../database/shopdata.sql";

	
  
	$setup[4]['fields']['ArticleData']['text'] = "Beispielartikel einspielen (nur mit Mustershop-Option)";
	$setup[4]['fields']['ArticleData']['type'] = "checkbox";
	$setup[4]['fields']['ArticleData']['value'] = "1";
	$setup[4]['fields']['ArticleData']['readonly'] = "true";
	$setup[4]['fields']['ArticleData']['sql'] = "../../database/testarticles.sql";*/

	/* ------------------------- Functions ------------------------- */


  /**
   * @param string $name
   *
   * @return bool
   */
  function hasExectuableExtension($name) {
    if (!function_exists('exec')) {
      return false;
    }
    if (!is_string($name)) {
      return false;
    }
    exec(sprintf('whereis %s',$name), $out);
    if (empty($out)) {
      return false;
    }
    $result = $out[0];
    $resultExploded = explode(':', $result);
    array_shift($resultExploded);
    return !empty(trim(implode("",$resultExploded)));
  }

  function pruefe($step)
  {
    if($step == 1)
    {
      
      $tmpfile = md5(microtime(true));

      if(!($fh = fopen('../../conf/'.$tmpfile,'w')))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'Der Ordner conf besitzt unzureichende Schreibrechte';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Der Ordner conf besitzt Schreibrechte';      
        $ret[] = $tmp; 
        fclose($fh);
        unlink('../../conf/'.$tmpfile);
      }

      if(!($fh = fopen('../../backup/'.$tmpfile,'w')))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'Der Ordner backup besitzt unzureichende Schreibrechte';
        $ret[] = $tmp;        
      } else {
        fclose($fh);
        unlink('../../backup/'.$tmpfile);
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Der Ordner backup besitzt Schreibrechte';      
        $ret[] = $tmp;
      }

      if(!($fh = fopen('../../userdata/'.$tmpfile,'w')))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'Der Ordner userdata besitzt unzureichende Schreibrechte';
        $ret[] = $tmp;        
      } else {
        fclose($fh);
        $eigenguser = fileowner('../../userdata/'.$tmpfile);
        $eigengroup = filegroup('../../userdata/'.$tmpfile);
        $uploaduser = fileowner(__FILE__);
        $uploadgroup = filegroup(__FILE__);
        
        if($eigenguser !== $uploaduser)
        {
          $tmp['status'] = 'warning';
          $tmp['text'] = 'Die Dateieigent&uuml;mer stimmten nicht mit dem des Webservers &uuml;berein';
          $ret[] = $tmp;          
        } else {
          
          
        }
        unlink('../../userdata/'.$tmpfile);
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Der Ordner userdata besitzt Schreibrechte';      
        $ret[] = $tmp;
      }

      TryCreateCacheDir();
      if(!($fh = fopen('../cache/'.$tmpfile,'w')))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'Der Ordner www/cache besitzt unzureichende Schreibrechte';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Der Ordner www/cache besitzt Schreibrechte';
        $ret[] = $tmp;
        fclose($fh);
        unlink('../cache/'.$tmpfile);
      }

      /*
      $rights = substr(sprintf('%o', fileperms('../../conf')), -3, 1);
      if($rights!='7')
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'Der Ordner conf besitzt unzureichende Schreibrechte';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Der Ordner conf besitzt Schreibrechte';      
        $ret[] = $tmp;      
      }*/
      if(!function_exists('fsockopen'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'fsocket nicht installiert!';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'fsocket verf&uuml;gbar';      
        $ret[] = $tmp;          
      }
      if(!function_exists('mysqli_connect'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'MYSQLi nicht installiert';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'MYSQLi verf&uuml;gbar';      
        $ret[] = $tmp;          
      }
      if(!extension_loaded('mysqlnd'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'Mysqlnd nicht installiert';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'Mysqlnd verf&uuml;gbar';
        $ret[] = $tmp;
      }
      if(!class_exists('\PDO'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'PDO nicht installiert';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'PDO verf&uuml;gbar';
        $ret[] = $tmp;
      }
      if(!function_exists('curl_init'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'cURL nicht installiert';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'cURL verf&uuml;gbar';      
        $ret[] = $tmp;          
      }
      
      if(!function_exists('stream_socket_enable_crypto'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'stream_socket_enable_crypto nicht installiert';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'stream_socket_enable_crypto verf&uuml;gbar';      
        $ret[] = $tmp;          
      }
      
      if(!function_exists('openssl_error_string'))
      {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'OpenSSL nicht installiert (optional)';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'OpenSSL verf&uuml;gbar';      
        $ret[] = $tmp;          
      }

      if(!class_exists('SoapClient'))
      {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'php-soap nicht installiert (optional)';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'php-soap verf&uuml;gbar';      
        $ret[] = $tmp;          
      }
      
      if(!function_exists('imap_open'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'imap nicht installiert';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'imap verf&uuml;gbar';      
        $ret[] = $tmp;          
      } 

      if(!function_exists('simplexml_load_string'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'php-xml nicht installiert';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'php-xml verf&uuml;gbar';      
        $ret[] = $tmp;          
      } 
      
      if(!function_exists('mb_encode_numericentity'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'php-mbstring nicht installiert';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'php-mbstring verf&uuml;gbar';      
        $ret[] = $tmp;          
      } 
      
      
      if(!class_exists('ZipArchive'))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'php-zip nicht installiert';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'php-zip verf&uuml;gbar';      
        $ret[] = $tmp;
      }
      if(!hasExectuableExtension('zip')) {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'zip-extension nicht installiert';
        $ret[] = $tmp;
      }
      else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'zip-extension verf&uuml;gbar';
        $ret[] = $tmp;
      }
      if(!function_exists('imagejpeg'))
      {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'PHP GD-Erweiterung nicht installiert (optional)';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'PHP GD-Erweiterung verf&uuml;gbar';      
        $ret[] = $tmp;          
      }

      if(!function_exists('ldap_connect'))
      {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'PHP LDAP-Erweiterung nicht installiert (optional)';
        $ret[] = $tmp;
      } else {
        $tmp['status'] = 'ok';
        $tmp['text'] = 'PHP LDAP-Erweiterung verf&uuml;gbar';
        $ret[] = $tmp;
      }
      
      if(!file_exists("../../database/struktur.sql"))
      {
        $tmp['status'] = 'error';
        $tmp['text'] = 'Initial Datenbanken nicht vorhanden';
        $ret[] = $tmp;
      }
      if(!file_exists("../../database/beispiel.sql"))
      { 
        $tmp['status'] = 'warning';
        $tmp['text'] = 'Beispiel-Datenbanken nicht vorhanden';
        $ret[] = $tmp;
      }

      $memory_limit = @ini_get('memory_limit');
      $post_max_size = @ini_get('client_max_body_size');
      if($post_max_size == '')$post_max_size = @ini_get('post_max_size');
      if(!$memory_limit)
      {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'Es war nicht m&ouml;glich den verf&uuml;gbaren RAM zu ermitteln. Es werden mindestens 128 MB empfohlen';
        $ret[] = $tmp;
      }else{
        if(strpos($memory_limit, 'M') !== false)
        {
          $memory_limit = str_replace('M','', $memory_limit);
          $memory_limit *= 1024*1024;
        }
        
        $memory_changed = $memory_limit;
        if($memory_limit < 128000000)
        {
          $memory_changed = @ini_set('memory_limit', 512*1024*1024);
          if(strpos($memory_changed, 'M') !== false)
          {
            $memory_changed = str_replace('M','', $memory_changed);
            $memory_changed *= 1024*1024;
          }
        }
        
        if($memory_changed < 128000000)
        {
          $tmp['status'] = 'warning';
          $tmp['text'] = 'Es sind nur '.round($memory_changed/(1024*1024)).' MB RAM verf&uuml;gbar Es werden mindestens 128 MB empfohlen';
          $ret[] = $tmp;
        } else {
          $tmp['status'] = 'ok';
          $tmp['text'] = 'Ausreichend RAM verf&uuml;gbar (>=128MB)';      
          $ret[] = $tmp;          
        }
      }

      $post_max_size = @ini_get('post_max_size');
      if(!$post_max_size)
      {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'Es war nicht m&ouml;glich die verf&uuml;gbare Upload-Kapazit&auml;t zu ermitteln. Es werden mindestens 8 MB empfohlen';
        $ret[] = $tmp;
      }else{
        if(strpos($post_max_size, 'M') !== false)
        {
          $post_max_size = str_replace('M','', $post_max_size);
          $post_max_size *= 1024*1024;
        }elseif(strpos($post_max_size, 'K') !== false)
        {
          $post_max_size = str_replace('K','', $post_max_size);
          $post_max_size *= 1024;
        }

        if($post_max_size < 8*1024*1024)
        {
          $tmp['status'] = 'warning';
          $tmp['text'] = 'Es sind nur '.round($post_max_size/(1024*1024)).' MB Datei-Upload m&ouml;glich. Empfohlen werden mindestens 8 MB';
          $ret[] = $tmp;
        } else {
          $tmp['status'] = 'ok';
          $tmp['text'] = 'Ausreichend Upload-Kapazit&auml;t verf&uuml;gbar (>=8MB)';
          $ret[] = $tmp;
        }
      }

      $max_execution_time = @ini_get('fastcgi_read_timeout');
      if($max_execution_time == '')$max_execution_time = @ini_get('max_execution_time');
      if(!$max_execution_time)
      {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'Es war nicht m&ouml;glich die verf&uuml;gbare Upload-Kapazit&auml;t zu ermitteln. Es werden mindestens 8 MB empfohlen';
        $ret[] = $tmp;
      }else{

        if($max_execution_time < 30)
        {
          $tmp['status'] = 'warning';
          $tmp['text'] = 'Es stehen nur '.($max_execution_time).' Sekunden Scriptlauftzeit zur verf&uuml;gung. Es werden mindestens 30 Sekunden empfohlen';
          $ret[] = $tmp;
        } else {
          $tmp['status'] = 'ok';
          $tmp['text'] = 'Es steht gen&uuml;gend Scriptlauftzeit zur verf&uuml;gung (>= 30 Sekunden).';
          $ret[] = $tmp;
        }
      }

      if(PHP_VERSION < 7.2) {
        $tmp['status'] = 'warning';
        $tmp['text'] = 'Die PHP Version ist unter 7.2. Ab Xentral 20.2 wird mindestens PHP 7.2 ben&ouml;tigt';
        $ret[] = $tmp;
      }

      $fopenUrlEnabled = ini_get('allow_url_fopen');
      $fopenUrlEnabled = strtolower($fopenUrlEnabled);
      if(!in_array($fopenUrlEnabled, ['1', 'true', 'on'])){
        $ret[] = [
          'status' => 'warning',
          'text'   => 'allow_url_fopen ist nicht in der php.ini aktiviert'
        ];
      }

      return $ret;
    }

    return false;
  }
  
  function CreateUserdata()
  {
		foreach($_SESSION['setup'] as $file=>$vars) {
			$out = "<?php\n";
			foreach($vars as $key=>$value)
      {
        if($key == 'WFuserdata')
        {
          if(!is_dir($value))
          {
            return 'Userdata-Ordner nicht vorhanden!';

          }
          if(!is_dir($value.'/dms'))mkdir($value.'/dms',0777);
          if(!is_dir($value.'/tmp'))mkdir($value.'/tmp',0777);
          if(!is_dir($value.'/pdfmirror'))mkdir($value.'/pdfmirror',0777);
          if(!is_dir($value.'/emailbackup'))mkdir($value.'/emailbackup',0777);
        }
      }
    }
    return '';
  }
  
  function stepMessage($step)
  {
    $erg = pruefe($step);
    $ret = '';
    if($erg){
      if($step == 1)
      {
        $ret = '<div>Schritt 1 - Pr&uuml;fe Serverfunktionen:</div><br>';
      }
      foreach($erg as $Message)
      {
        switch ($Message['status']) {
          case 'error': $class = 'error'; break;
          case 'warning': $class = 'warning'; break;
          default: $class = ''; break;
        }
        $ret .= '<div class="box '.$class.'">'.$Message['text']."</div>\r\n";
      }
      return $ret;
    } else {
      return $setup[$step]['description'];
    }
    
  }
  
  function stepFehler($step)
  {
    if($step > 5)$step = 5;
    if($step > 1)
    {
      if(!stepFehler($step - 1))
      {
        return false;
      }
    }
    if($step == 3)
    {
      if(!isset($_SESSION['setup']))return false;
      $db = null;
      foreach($_SESSION['setup'] as $file=>$vars) {
        if($file != 'user.inc.php')
        {
          $fehler = true;
          continue;
        }

        $WFdbhost = '';
        $WFdbname = '';
        $WFdbpass = '';
        $WFdbuser = '';
        $WFuserdata = '';

        foreach($vars as $key=>$value)
        {
          if(in_array($key, array('WFdbhost', 'WFdbname', 'WFdbuser','WFdbpass','WFuserdata')))$$key = $value;
        }
        
        if($WFdbname == '' || $WFdbuser == '')
        {
          return false;
        }
        $db= mysqli_connect($WFdbhost,$WFdbuser,$WFdbpass);
        if(!$db)
        {
          return false;
        }

        if(!mysqli_select_db($db,$WFdbname))
        {
          return false;
        }
      }
      if(!$db)
      {
        return false;
      }
      mysqli_close($db);
      return true;
    }
    if($step == 4)
    {
      if(!file_exists(__DIR__.'/../../conf/user.inc.php'))return false;
      return true;
    }
    if($step == 5)
    {
      if(isset($_SESSION['setup']['../../database/struktur.sql']) )
      {
        return true;
      }
      return false;
    }
    $erg = pruefe($step);
    if($erg)
    {
      foreach($erg as $Message)
      {
        if($Message['status'] == 'error')return false;
        
      }
    }
    return true;
  }
  
 	function CheckDirRights()
	{
		$rights = substr(sprintf('%o', fileperms('../../conf')), -3, 1);
		if($rights!='7'){
		  $tmpfile = md5(mt_rand());
		  if(file_put_contents(dirname(dirname(__DIR__)).'/conf/'.$tmpfile,'test'))
      {
        @unlink(dirname(dirname(__DIR__)).'/conf/'.$tmpfile);
        return '';
      }
      return "Der Ordner conf besitzt unzureichende Schreibrechte";
    }
		return "";
	}

  function TryCreateCacheDir()
  {
    $cacheDir = dirname(__DIR__) . '/cache';
    if (!is_dir($cacheDir)) {
      mkdir($cacheDir,0777);
    }
  }

	function CheckDatabase()
	{
	  global $db;
	  if($_POST['WFdbname'] == '')
    {
      return 'Verbindung zum Server nicht m&ouml;glich - Keine Datenbank angegeben';
    }
		$db = mysqli_connect($_POST['WFdbhost'], $_POST['WFdbuser'], $_POST['WFdbpass'],$_POST['WFdbname']);

		if(!$db) return 'Verbindung zum Server nicht m&ouml;glich - m&ouml;glicherweise ist Host, Benutzername oder Passwort falsch geschrieben'; 
		//if(!mysqli_select_db($db,$_POST['WFdbname'], $db)) return 'Verbindung zur Datenbank nicht m&ouml;glich - m&ouml;glicherweise ist der Datenbankname falsch geschrieben';

		return '';
	}

	function CheckMail()
	{
		$smtp_conn = fsockopen($_POST['WFMailHost'], $_POST['WFMailPort'], $errno, $errstr, 30);
		
		if(empty($smtp_conn)) 
			return "Verbindung zum Server nicht m&ouml;glich<br>$errstr";


		return '';//'Konnte E-Mail nicht finden';
	}

	function CheckOther()
	{
		return '';
	}

	function PostInstall()
	{
		// Copy main.conf.php.tpl to main.conf.php.tpl
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
    {
			copy('..\..\conf\main.conf.php.tpl','..\..\conf\main.conf.php');
      copy('..\..\conf\user_defined.php.tpl','..\..\conf\user_defined.php');
    }
		else
		{	
      copy("../../conf/main.conf.php.tpl","../../conf/main.conf.php");
      copy("../../conf/user_defined.php.tpl","../../conf/user_defined.php");
    }
	}
?>
