<?php
//include("wawision.inc.php");

// Nur einfache Fehler melden
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR | E_PARSE);

include_once("conf/main.conf.php");
include_once("phpwf/plugins/class.mysql.php");
include_once("www/lib/class.erpapi.php");

class app_t {
    var $DB;
      var $user;
      var $Conf;
}

$app = new app_t();

$DEBUG = 0;

$app->Conf = new Config();
$app->DB = new DB($app->Conf->WFdbhost,$app->Conf->WFdbname,$app->Conf->WFdbuser,$app->Conf->WFdbpass,null,$app->Conf->WFdbport);
$erp = new erpAPI($app);

$WAWISION['host'] = $app->Conf->updateHost ?? 'update.xentral.biz';
$WAWISION['port']="443";

$myUpd = new UpgradeClient($WAWISION);


echo "STARTE UPDATE\n";
echo "Im folgenden stehen die Dateien die geaendert wurden.\n
Erscheinen keine Dateien sind Sie auf der neusten Version.\n";

$myUpd->Connect();
//$myUpd->CheckCRT();
$myUpd->CheckUpdate();
$myUpd->CheckUpdateCustom();
$myUpd->CheckUpdateModules();

echo "ENDE   UPDATE\n";

//echo "STARTE DB UPGRADE\n";
//$erp->UpgradeDatabase();
//echo "ENDE DB UPGRADE\n";

//include("version.php");
//echo "\r\nRevision: $version_revision\r\n";


//$myUpd->Request();



//echo



class UpgradeClient
{
	var $localmd5sums;
	
	function __construct($conf)
	{
		$this->conf = $conf;
	
	}
	

	function Connect()
	{
		// check connection then stop	
	
	}	
	

	function CheckCRT()
	{
		$cert = shell_exec("openssl s_client -connect {$this->conf['host']}:{$this->conf['port']} < /dev/null 2>/dev/null | openssl x509 -in /dev/stdin");
		if($cert==$this->conf['cert']."\n") return 1;
		else {
			echo "wrong\n";
			exit;
		}
	
	}

	function CheckUpdate()
	{
    $parameter['version']=@$this->conf['version'];
		$result = $this->Request("md5list",$parameter);
		
		if($result=="ERROR") { echo "Updates: ERROR FROM SERVER (Perhaps a wrong license?)\n"; return;} 

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
				
				if($file=="./upgradesystemclient.php")
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
					} else {
					//	echo "datei XX $file local: ".md5_file($file."NEW")." remote: ".$md5sum."\n";
					
					}
				} else { }
				}
			}
		
		
		}
		//pruefe, update, lege verzeichnis an, lege datei an, loesche datei????
		
		
		
		// download all files with UPD_ prefix
		
		
		// get md5 liste von server
		
		// pruefe ob alle dateien passen, wenn ja dann alle updaten am schluss
	
		// wenn nein fehler abbrechen und ganzen prozess nochmal starten	
		
		//echo $md5sums;	
		
		
	}
	
	
	function CheckUpdateModules()
	{
    $parameter['version']=@$this->conf['version'];
		$result = $this->Request("md5listmodules",$parameter);
		
		if($result=="ERROR") { echo "Modules: ERROR FROM SERVER (Perhaps a wrong license?)\n"; return;} 

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
				
				if($file=="./upgradesystemclient.php")
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
				} else if($file!="") {
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
					} else {
					//	echo "datei XX $file local: ".md5_file($file."NEW")." remote: ".$md5sum."\n";
					
					}
				} else { }
				}
			}
		
		
		}
		//pruefe, update, lege verzeichnis an, lege datei an, loesche datei????
		
		
		
		// download all files with UPD_ prefix
		
		
		// get md5 liste von server
		
		// pruefe ob alle dateien passen, wenn ja dann alle updaten am schluss
	
		// wenn nein fehler abbrechen und ganzen prozess nochmal starten	
		
		//echo $md5sums;	
		
		
	}

	function CheckUpdateCustom()
	{
	  $parameter['version']=@$this->conf['version'];
		$result = $this->Request("md5listcustom",$parameter);
		
		if($result=="ERROR") { echo "Custom: ERROR FROM SERVER (Perhaps a wrong license?)\n"; return;} 

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
				
				if($file=="./upgradesystemclient.php")
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
					} else {
					//	echo "datei XX $file local: ".md5_file($file."NEW")." remote: ".$md5sum."\n";
					
					}
				} else { }
				}
			}
		
		
		}
		//pruefe, update, lege verzeichnis an, lege datei an, loesche datei????
		
		
		
		// download all files with UPD_ prefix
		
		
		// get md5 liste von server
		
		// pruefe ob alle dateien passen, wenn ja dann alle updaten am schluss
	
		// wenn nein fehler abbrechen und ganzen prozess nochmal starten	
		
		//echo $md5sums;	
		
		
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
		global $erp;
                $auth['serial']=trim($erp->Firmendaten("lizenz"));//$this->conf['serial'];
                $auth['authkey']=trim($erp->Firmendaten("schluessel"));//$this->conf['authkey'];
                        
                $auth = base64_encode(json_encode($auth));
                $parameter = base64_encode(json_encode($parameter));

		$client = new HttpClient($this->conf['host'],$this->conf['port']);
		$client->post('/upgradesystem.php', array( "authjson" => $auth, "parameterjson"=>$parameter,"command"=>"$command" ));
		$pageContents = $client->getContent();
		return $pageContents;
	}
	
  	function dir_rekursiv($verzeichnis)
    	{ 
        	$handle =  opendir($verzeichnis);
        
            	while ($datei = readdir($handle))
                {   
                        if ($datei != "." && $datei != "..")
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

class HttpClient {
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

  if(!fsockopen("ssl://".$this->host, $this->port, $errno, $errstr, $this->timeout) && $this->port==443)
  {
  $this->port=80;
  }

  if($this->port==443)
    $url = "ssl://".$this->host;
  else
    $url = $this->host;

		if (!$fp = @fsockopen($url, $this->port, $errno, $errstr, $this->timeout)) {
		    // Set error message
            switch($errno) {
				case -3:
					$this->errormsg = 'Socket creation failed (-3)';
				case -4:
					$this->errormsg = 'DNS lookup failure (-4)';
				case -5:
					$this->errormsg = 'Connection refused or timed out (-5)';
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
                $this->errormsg = 'Number of redirects exceeded maximum ('.$this->max_redirects.')';
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
        } else {
            return false;
        }
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
        $client = new HttpClient($host, $port);
        if (!$client->get($path)) {
            return false;
        } else {
            return $client->getContent();
        }
    }
    function quickPost($url, $data) {
        $bits = parse_url($url);
        $host = $bits['host'];
        $port = isset($bits['port']) ? $bits['port'] : 80;
        $path = isset($bits['path']) ? $bits['path'] : '/';
        $client = new HttpClient($host, $port);
        if (!$client->post($path, $data)) {
            return false;
        } else {
            return $client->getContent();
        }
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
