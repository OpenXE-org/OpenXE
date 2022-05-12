<?php
// This function returns the digest string
function getDigest() {
  $digest="";
  // mod_php
  if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
    $digest = $_SERVER['PHP_AUTH_DIGEST'];
    // most other servers
  } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {

    if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'digest')===0)
      $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
  }
  elseif (isset($_SERVER['REMOTE_USER'])) {

    if (strpos(strtolower($_SERVER['REMOTE_USER']),'digest')===0)
      $digest = substr($_SERVER['REMOTE_USER'], 7);
  }


  return $digest;

}

// This function forces a login prompt
function requireLogin($realm,$nonce) {
  header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . $nonce . '",opaque="' . md5($realm) . '"');
  header('HTTP/2.0 401 Unauthorized');
  echo 'Auth canceled';
  die();
}

// This function extracts the separate values from the digest string
function digestParse($digest) {
  // protect against missing data
  $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
  $data = array();

  preg_match_all('@(\w+)=(?:(?:")([^"]+)"|([^\s,$]+))@', $digest, $matches, PREG_SET_ORDER);

  foreach ($matches as $m) {
    $data[$m[1]] = $m[2] ? $m[2] : $m[3];
    unset($needed_parts[$m[1]]);
  }

  return $needed_parts ? false : $data;
}

function emu_getallheaders() {
  foreach ($_SERVER as $name => $value)
  {
    if (substr($name, 0, 5) == 'HTTP_')
    {
      $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
      $headers[$name] = $value;
    } else if ($name == "CONTENT_TYPE") {
      $headers["Content-Type"] = $value;
    } else if ($name == "CONTENT_LENGTH") {
      $headers["Content-Length"] = $value;
    }
  }
  return $headers;
}


class Router {
  var $route;
  var $apiobj;
  var $request;

  function __construct($apiobj)
  {
    $this->apiobj=$apiobj;
  }


  function getRequest()
  {
    $request = stristr($_SERVER['REQUEST_URI'],'api');

    // map url to rest objects
    list($api,$object,$objectid) =  array_pad(explode('/',$request),3,'');

    //get all headers
    $headers = emu_getallheaders();

    //get method
    $method = $_SERVER['REQUEST_METHOD'];

    //get content type
    $contenttype =  $headers["Content-Type"];

    //get body
    $body = file_get_contents('php://input');

    $this->request['Content-Type']=$contenttype;
    $this->request['Method']=trim(strtoupper($method));
    $this->request['Object']=$object;
    $this->request['ObjectID']=$objectid;
    $this->request['Body']=$body;
  }

  function doRequest()
  {
    $this->getRequest();

    $notfound=true;

    foreach($this->route[$this->request['Method']] as $request)
    {
      // set ony to false if there ist an objectidregex and if this not pass!  
      $foundobjectid = true;

      if(isset($request['objectidregex']) && $request['objectidregex']!="")
      {
        if(!preg_match('/'.$request['objectidregex'].'/', $this->request['ObjectID'], $matches)) {
          $foundobjectid = false;
        }
      }

      if($request['urlressource']==$this->request['Object'] && $foundobjectid)
      {
        //echo $request['apimethod'];

        if( method_exists ( $this->apiobj , $request['apimethod']))
        {

          if (stripos($this->request['Content-Type'], 'json')!== false) $this->apiobj->usejson = true;

          if($this->request['ObjectID'] > 0)
            $this->apiobj->{$request['apimethod']}(false,$this->request['ObjectID']);
          else
            $this->apiobj->{$request['apimethod']}();
          exit;
        }
        else {
          echo "API Method: ".$request['apimethod']." doesn't exists";
        }
      }
    }

    if($notfound)
    {
      echo "Can't route request:\r\n";
      print_r($this->request);
      echo "route list:\r\n";
      print_r($request);
    }
  }


  function RouteRequest($http_method,$urlressource,$apimethod)
  {
    $http_method = trim(strtoupper($http_method));
    if($http_method=="GET" || $http_method=="POST" || $http_method=="PUT" || $http_method=="DELETE")
    {
      $objectidregex="";
      if(stripos($urlressource)!==false)
      {
        $tmp = explode('/',$urlressource);
        $urlressource = $tmp[0];
        $objectidregex = $tmp[1];
      } 
      $this->route[$http_method][] = array('urlressource' => $urlressource, 'apimethod'=>$apimethod,'objectidregex'=>$objectidregex);
    }
  }



}
