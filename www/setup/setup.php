<?php
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); 
	ini_set('display_errors', 1);
	$config_file = 'setup.conf.php';
	$output_folder = '../../conf/';

	if(!is_file($config_file) ) { echo 'Config-File is missing'; return; }
	include_once($config_file);

	#########################################################################
	$max_steps = count(array_filter($setup))+1;

	$step = (($_GET['step']!='') ? $_GET['step'] : 1);
	$submit = $_POST['_SUBMIT'];


        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
          $isSecure = true;
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
          $isSecure = true;
        }
        $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

        //$weburl = $REQUEST_PROTOCOL."://".$_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT'].str_replace('setup/setup.php?step=5','',$_SERVER['REQUEST_URI'])."index.php?module=welcome&action=cronjob";
        //$weburllink = $REQUEST_PROTOCOL."://".$_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT'].str_replace('setup/setup.php?step=5','',$_SERVER['REQUEST_URI']);

        if($_SERVER['SCRIPT_URI'] != '')
        {
          $weburl = str_replace(array('setup/setup.php?step=5','setup/setup.php'),array('',''),$_SERVER['SCRIPT_URI'])."index.php?module=welcome&action=cronjob";
  //        $weburllink = str_replace(array('setup/setup.php?step=5','setup/setup.php'),array('',''),$_SERVER['SCRIPT_URI']);
        }elseif($_SERVER['REQUEST_URI'] != '' && $_SERVER['SERVER_ADDR']!='' && $_SERVER['SERVER_ADDR']!='::1' && strpos($_SERVER['SERVER_SOFTWARE'],"nginx")===false)
        {
          $weburl = (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']?$REQUEST_PROTOCOL."://".$_SERVER['SERVER_ADDR'].(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443?':'.$_SERVER['SERVER_PORT']:''):'').str_replace(array('setup/setup.php?step=5','setup/setup.php'),array('',''),$_SERVER['REQUEST_URI'])."index.php?module=welcome&action=cronjob";
 //         $weburllink = (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']?$REQUEST_PROTOCOL."://".$_SERVER['SERVER_ADDR'].(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443?':'.$_SERVER['SERVER_PORT']:''):'').str_replace(array('setup/setup.php?step=5','setup/setup.php'),array('',''),$_SERVER['REQUEST_URI']);
        } elseif($_SERVER['SERVER_NAME']!="") //MAMP auf macos
        {
          $weburl = str_replace(array('setup/setup.php?step=5','setup/setup.php'),array('',''),$REQUEST_PROTOCOL."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUESR_URI'].$_SERVER['SCRIPT_NAME'])."index.php?module=welcome&action=cronjob";
//          $weburllink = str_replace(array('setup/setup.php?step=5','setup/setup.php'),array('',''),$REQUEST_PROTOCOL."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUESR_URI'].$_SERVER['SCRIPT_NAME']);
        } 
        else{
          $weburl = '../index.php?module=welcome&action=cronjob';
          $weburllink = '../';
        }

        $weburllink = '../';

	if($step>=$max_steps){
		//GenerateConfigFiles($output_folder);
		//if($config['postinstall']) PostInstall();
		$webserverUser = DetermineWebserverUser();
		$phpBinary = DeterminePhpBinary();
		$baseDir = DetermineInstallationDir();
		$cronjobStarterPath = $baseDir . '/cronjobs/starter2.php';
		$cronjobLine = htmlspecialchars(sprintf('* * * * * %s %s > /dev/null 2>&1', $phpBinary, $cronjobStarterPath));

		$page = HtmlTemplate(
"<h2>Setup erfolgreich beendet!</h2>
<p>Um den Prozessstarter nutzen zu k&ouml;nnen: Tragen Sie folgendes Script in ihrer crontab ein:</p>
<pre style=\"font-size:9pt\">php {$cronjobStarterPath}</pre>
<p>Am besten eignet sich ein Interval von einer Minute. Wichtig ist dass der Cronjob unter dem Benutzer des Webservers läuft.</p>
<br>
<div id=\"crontab\">
<h3>Anleitung – Crontab einrichten</h3>
<ol>
<li>
<p>Nachfolgendes Kommando ausführen:</p>
<pre class=\"console\">sudo crontab -u {$webserverUser} -e</pre>
</li>
<li>
<p>Anschließend öffnet sich ein Editor. Dort folgende Zeile einfügen und die Datei speichern:</p>
<pre class=\"console\">{$cronjobLine}</pre>
</li>
<li>
<p>Nach dem Verlassen des Editors sollte diese Ausgabe als Bestätigung erscheinen:</p>
<pre class=\"console\">crontab: installing new crontab</pre>
</li>
</ol>
</div>
<br><br>
<center>
<a href=\"$weburllink\">Anmelden mit Benutzer: <i>admin</i> und Passwort: <i>admin</i></a>
</center>
");
	}else{
    $ok = true;
    if($step == 1)
    {
      $setup[$step]['description'] = stepMessage($step);
      $ok = stepFehler($step);
    }else $ok = stepFehler($step);
		$page = GenerateHtml($step, $setup);
		if($ok){$page = str_replace('[BUTTON]', (($step<=$max_steps)?"<input type=\"submit\" name=\"_SUBMIT\" value=\"Weiter\" class=\"btn\">":""), $page);}else{$page = str_replace('[BUTTON]','',$page);}
	}
	if($submit!='') {
		$configfile = $_POST['_CONFIGFILE'];
		$action = $_POST['_ACTION'];
		unset($_POST['_CONFIGFILE']);
		unset($_POST['_ACTION']);
		unset($_POST['_SUBMIT']);

		$error = ((function_exists($action ?? '')) ? $action() : '');
		if($configfile=='')  $error .= "<br>'configfile' for this step is missing";

		if($error=='') {
			// Convert Fields to Session
			foreach($_POST as $key=>$value) 
				$_SESSION['setup'][$configfile][$key] = $value;
      if($step == 3)GenerateConfigFiles($output_folder);
      if($step == 3)CreateUserdata();
      if($step == 3)PostInstall();
			// execute Sql-Files
			$sql_prefix = "sql_";
      
			foreach($_POST as $key=>$value) {
				if(strlen($key)>strlen($sql_prefix) && substr($key,0,strlen($sql_prefix))==$sql_prefix && 
					$_SESSION['setup'][$configfile][substr($key,strlen($sql_prefix), strlen($key)-strlen($sql_prefix))]!=''){
					unset($_SESSION['setup'][$configfile][$key]);
          if($value != '../../database/struktur.sql' && $value != '../../database/beispiel.sql')continue;
          
					if(is_file($value)){
            if(is_file(__DIR__.'/../../conf/user.inc.php'))
            {
						 	$import = file_get_contents($value);

   						$import = preg_replace ("%/\*(.*)\*/%Us", '', $import);
   						$import = preg_replace ("%^--(.*)\n%mU", '', $import);
   						$import = preg_replace ("%^$\n%mU", '', $import);

							$db= mysqli_connect($_SESSION['setup'][$configfile]['WFdbhost'],$_SESSION['setup'][$configfile]['WFdbuser'],$_SESSION['setup'][$configfile]['WFdbpass']);
              if($db)
              {
                if(mysqli_select_db($db,$_SESSION['setup'][$configfile]['WFdbname']))
                {
                                                            mysqli_set_charset($db,"utf8");
                                                            mysqli_query($db,"SET SESSION SQL_MODE :=''");
                  //mysqli_real_escape_string($db,$import); 

		if($value == '../../database/struktur.sql') 
		{
               		// Clear target DB
        	        mysqli_query($db,'SET foreign_key_checks = 0');
	                if ($result = mysqli_query($db,"SHOW TABLES"))
	                {
	                    while($row = $result->fetch_array(MYSQLI_NUM))
	                    {
	                        mysqli_query($db,'DROP TABLE IF EXISTS '.$row[0]);
	                    }
	                }
	                mysqli_query($db,'SET foreign_key_checks = 1');
		}


                  $import = explode (";\n", $import); 

                  foreach ($import as $imp){
                    if ($imp != '' && $imp != ' ' && trim($imp) != ''){
                      mysqli_query($db,$imp);
                    }
                  }

                  if(isset($_POST['BeispielTpl']) && $_POST['BeispielTpl'])
                           mysqli_query($db, "INSERT INTO `user` (`id`, `username`, `password`, `repassword`, `description`, `settings`, `parentuser`, `activ`, `type`, `adresse`, `fehllogins`, `standarddrucker`, `firma`, `logdatei`, `startseite`, `hwtoken`, `hwkey`, `hwcounter`, `motppin`, `motpsecret`, `passwordmd5`, `externlogin`, `projekt_bevorzugen`, `email_bevorzugen`, `projekt`, `rfidtag`, `vorlage`, `kalender_passwort`, `kalender_ausblenden`, `kalender_aktiv`, `gpsstechuhr`, `standardetikett`, `standardfax`, `internebezeichnung`, `hwdatablock`) VALUES
                          (3, 'demomitarbeiter', 'pllIX0pw7JU9c', 0, '', 'a:6:{s:16:\"pos_list_projekt\";s:1:\"0\";s:18:\"pos_list_kassierer\";s:1:\"0\";s:22:\"pos_list_kassierername\";s:1:\"0\";s:18:\"pos_list_lkadresse\";s:1:\"0\";s:18:\"lohnabrechnung_von\";s:0:\"\";s:18:\"lohnabrechnung_bis\";s:0:\"\";}', 0, 1, 'standard', 6, 0, 0, 1, '2015-10-26 16:01:23', '', 0, '', 0, '', '', '2ad71933e4b074c4671425c8e6b48021', 0, 0, 1, 0, '', '', '', 0, 0, 0, 0, 0, NULL, '');
                          ");
                  $_SESSION['setup'][$value] = 1;
                }
                mysqli_close($db);
              }
              /*
						if(exec("mysql --user='{$_SESSION['setup'][$configfile]['WFdbuser']}' --password='{$_SESSION['setup'][$configfile]['WFdbpass']}' --host='{$_SESSION['setup'][$configfile]['WFdbhost']}' --database='{$_SESSION['setup'][$configfile]['WFdbname']}' < '$value'", $sql_out, $sql_status)==2)
							$error = "Konnte '$value' nicht ausf&uuml;hren";
						}else
							$error .= "Konnte '$value' nicht finden";
*/
            }
					}
				}
			}

			// remove Readonly-Fields
			$ro_prefix = "ro_";
			foreach($_POST as $key=>$value) {
				if(strlen($key)>strlen($ro_prefix) && substr($key,0,strlen($ro_prefix))==$ro_prefix){
					unset($_SESSION['setup'][$configfile][substr($key,strlen($ro_prefix), strlen($key)-strlen($ro_prefix))]);
					unset($_SESSION['setup'][$configfile][$key]);
				}
			}

			if($error=='') {
				header('Location: ./setup.php?step='.++$step);
				exit;
			}else
				$page = str_replace('[MESSAGE]', "<div class=\"inputerror\">$error</div>", $page);
		}else
			$page = str_replace('[MESSAGE]', "<div class=\"inputerror\">$error</div>", $page);
	}

	$page = str_replace('[MESSAGE]','', $page);
	echo $page;

  if($step == 5 && stepFehler(5))
  {
		@unlink(__DIR__ . '/setup.conf.php');
		@unlink(__DIR__ . '/setup-cli.php');
		@unlink(__DIR__ . '/setup.css');
		@unlink(__FILE__);
		@rmdir(__DIR__);
  }

	function GenerateConfigFiles($output_folder)
	{
    if(!stepFehler(3)) {
    	return false;
		}
    $fehler = true;
    foreach($_SESSION['setup'] as $file=>$vars) {
    	$fehler = false;
		}
		foreach($_SESSION['setup'] as $file=>$vars) {
      if($file !== 'user.inc.php')
      {
        $fehler = true;
        continue;
      }
      if(!is_dir($output_folder))
      {
        $fehler = true;
        continue;
      }
			$out = "<?php\n";
      
      $WFdbhost = '';
      $WFdbname = '';
      $WFdbpass = '';
      $WFdbuser = '';
      $WFuserdata = '';
      
      foreach($vars as $key=>$value)
      {
        if(in_array($key, array('WFdbhost', 'WFdbname', 'WFdbuser','WFdbpass','WFuserdata'))) {
        	$$key = $value;
				}
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
      
			foreach($vars as $key=>$value)
      {
        if(!in_array($key, array('WFdbhost', 'WFdbname', 'WFdbuser','WFdbpass','WFuserdata'))) {
        	continue;
				}
        if($key === 'WFuserdata' && !is_dir($value))
        {
          $fehler = true;
          continue;
        }
        if(strpos($key, 'WF') !== false){
          if($value==='true' || $value==='false'){
						$out .= '$this->' . $key . '=' . $value . ';' . "\n";
					}
          else{
						$out .= '$this->' . $key . '=\'' . (str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value))) . '\';' . "\n";
					}
        }
      }

      /*$out2 = "<?php\n";
      $out2 .= "  define('USEFPDF2',true);\r\n";
      $out2 .= "?>";
      if(!file_put_contents(dirname(__FILE__).'/../../conf/user_defined.php', $out2))$fehler = true;*/
			if(!file_put_contents($output_folder.$file, $out)) {
				$fehler = true;
			}
		}
		return !$fehler;
	}	

	function GenerateHtml($step, $setup)
	{
		if(!array_key_exists($step, $setup)) { return "<h2>Page doesnt exist</h2>"; }

		$html = "";
		if(array_key_exists('description',$setup[$step])) $html .= "<h2>{$setup[$step]['description']}</h2>";
		if(array_key_exists('configfile',$setup[$step])) $html .= "<input type=\"hidden\" name=\"_CONFIGFILE\" value=\"{$setup[$step]['configfile']}\">";
		if(array_key_exists('action',$setup[$step])) $html .= "<input type=\"hidden\" name=\"_ACTION\" value=\"{$setup[$step]['action']}\">";
		
		$fields = '';
		foreach($setup[$step]['fields'] as $key=>$value)
		{
			$name = $key;
			$text = ((array_key_exists('text',$value)) ? $value['text'] : $value);
			$type = ((array_key_exists('type',$value)) ? $value['type'] : "text");
			$note = ((array_key_exists('note',$value)) ? $value['note'] : "");
			$default = ((array_key_exists('default',$value)) ? $value['default'] : "");
			$options = ((array_key_exists('options',$value)) ? $value['options'] : array());
			$fvalue = ((array_key_exists('value',$value)) ? $value['value'] : "");
			$readonly = ((array_key_exists('readonly',$value)) ? $value['readonly'] : "");
			$sql = ((array_key_exists('sql',$value)) ? $value['sql'] : "");
			$invisible = ((array_key_exists('invisible',$value)) ? $value['invisible'] : "");

			if($readonly!="") $ro = "<input type=\"hidden\" name=\"ro_$name\" value=\"$name\">";
			if($sql!="") $mysql = "<input type=\"hidden\" name=\"sql_$name\" value=\"$sql\">";

			if($invisible=="")
			{
				if($type=='text'){
          $input = "<input type=\"text\" name=\"$name\" value=\"$default\" placeholder=\"$text\">";
        }
				if($type=='password'){
          $input = "<input type=\"password\" name=\"$name\" value=\"$default\" placeholder=\"$text\">";
        }

				if($type=='checkbox'){
          $input = "<label><input type=\"checkbox\" name=\"$name\" value=\"$fvalue\">&nbsp;$text</label>";
        }

				if($type=='select') {
					$opt_out = '';
					foreach($options as $opt_value=>$opt_text){
						$selected = (($default!="" && $default==$opt_value) ? 'selected' : '');
						$opt_out .= "<option value=\"$opt_value\" $selected>$opt_text</option>";
					}
					$input = "<select name=\"$name\">$opt_out</select>";
				}
				$field = "<div class=\"row\"><div>{$input}{$ro}{$mysql}</div><div>$note</div></div>\n";
			}else
				$field = "<input type=\"hidden\" name=\"$name\" value=\"1\">{$ro}{$mysql}";

			$fields .= $field;
		}
		$html .= "\n[MESSAGE]\n$fields\n[BUTTON]";
	
		$page = HtmlTemplate($html, $step);

		return $page;
	}

	function HtmlTemplate($html)
	{
	  global $step;

	  // CSS inline einbinden; ansonsten wird die letzte Seite nicht korrekt angezeigt
		// Auf der letzten Seite werden alle Dateien im setup-Order gelöscht.
	  $cssContent = file_get_contents(__DIR__ . '/setup.css');

	  return
"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html><head>
<style type=\"text/css\">{$cssContent}</style>
<title>OpenXE Installer</title>
</head><body class=\"slide-{$step}\"><div id=\"main\"><div class=\"header\"><h1 class=\"headline\">OpenXE Installer</h1></div><div class=\"content\"><form action=\"\" method=\"POST\">
 {$html}
</form></div>
<!--
<div class=\"footer\"><p>Nutzen Sie unseren <a href=\"https://shop.xentral.biz/sonstige/1-jahr-zugang-updateserver-open-source-version.html?c=164?c=164\" target=\"_blank\">Update-Server</a> um auch mit der Open-Source Version mit Updates versorgt zu sein.</p></div>
--!>
<div class=\"footer\"><p>
OpenXE is free open source software under AGPL-3.0 license, based on <a href=\"https://xentral.com\" target=\"_blank\">Xentral®</a> by Xentral&nbsp;ERP&nbsp;Software&nbsp;GmbH.</p></div>
</div></body></html>";
	}

	/**
	 * Determine under which linux user runs the webserver
	 *
	 * @return string
	 */
    function DetermineWebserverUser()
	{
		$username = @getenv('USERNAME') ?: @getenv('USER');
		if ($username !== false && !empty($username)) {
			return $username;
		}
		$username = $_SERVER['USERNAME'] ?: $_SERVER['USER'];
		if (!empty($username)) {
			return $username;
		}
		$username = trim(@shell_exec('whoami'));
		if (!empty($username)) {
			return $username;
		}

		return get_current_user(); // Eventuell falsch unter IIS
	}

	/**
	 * Determine the full path of the php binary
	 *
	 * @return string
	 */
    function DeterminePhpBinary()
	{
		if (!empty(PHP_BINDIR)) {
			return PHP_BINDIR . '/php';
		}
		$binary = trim(@shell_exec('which php'));
		if (!empty($binary)) {
			return $binary;
		}

		return '/usr/bin/env php';
	}

	/**
	 * Determine the full path of the installation directory; without trailing slash
	 *
	 * @return string
	 */
  	function DetermineInstallationDir()
	{
		$baseDir = null;
		if (!empty(__FILE__)) {
			$offset = strpos(__FILE__, '/www/setup/setup.php');
			if ($offset !== false) {
				$baseDir = substr_replace(__FILE__, '', $offset);
			}
		}
		if (!empty($baseDir)) {
			return $baseDir;
		}

		if (!empty($_SERVER['SCRIPT_FILENAME'])) {
			$offset = strpos($_SERVER['SCRIPT_FILENAME'], '/www/setup/setup.php');
			if ($offset !== false) {
				$baseDir = substr_replace($_SERVER['SCRIPT_FILENAME'], '', $offset);
			}
		}

		return $baseDir;
	}
?>
