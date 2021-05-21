<?php

class Location
{
  /**
   * @var Application
   */
  protected $app;
  protected $server;
  public function __construct($app)
  {
    $this->app = $app;
    $firmendatenServerUrl = (String)$this->app->erp->Firmendaten('server_url');
    if(!empty($firmendatenServerUrl)) {
      $this->server = $this->createServerByUrlAndPort($firmendatenServerUrl, $this->app->erp->Firmendaten('server_port'));
      return;
    }

    $this->server = '';
    $isSecure = false;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
      $isSecure = true;
    }
    elseif ((isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
      && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
      || (isset($_SERVER['HTTP_X_FORWARDED_SSL'])
      && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')) {
      $isSecure = true;
    }
    $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

    if(!empty($_SERVER['SCRIPT_URI']))
    {
      $server = $_SERVER['SCRIPT_URI'];
    } elseif(!empty($_SERVER['SERVER_NAME']))
    {
      if($_SERVER['SERVER_NAME'] === '_' && !empty($_SERVER['HTTP_HOST'])){
        // Sonderfall für Nginx mit Servername "_"
        $server = $REQUEST_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
      }else{
        $server = $REQUEST_PROTOCOL.'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
      }
    }elseif(!empty($_SERVER['REQUEST_URI']) && !empty($_SERVER['SERVER_ADDR']) &&
      !empty($_SERVER['SCRIPT_NAME']) && $_SERVER['SERVER_ADDR']!=='::1' && strpos($_SERVER['SERVER_SOFTWARE'],'nginx')===false
    ){
      $server = (isset($_SERVER['SERVER_ADDR']) &&
        $_SERVER['SERVER_ADDR'] ? $REQUEST_PROTOCOL . '://' . $_SERVER['SERVER_ADDR'] .
          (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 &&
          $_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') : '') .
        $_SERVER['SCRIPT_NAME'];
    }else{
      $server = 'index.php';
      $this->server = $server;
    }
    if($this->server === ''){
      $servera = parse_url($server);
      if(!empty($servera['host']) && !empty($servera['scheme'])
      ){
        $this->server = !empty($servera['scheme'])  ? $servera['scheme'] . '://' : '';
        if(!empty($servera['user']))
        {
          $this->server .= $servera['user'];
          if(!empty($servera['pass']))
          {
            $this->server .= ':'.$servera['pass'];
          }
          $this->server .= '@';
        }
        $this->server .= $servera['host'];
        if(!empty($servera['port'])){
          $this->server .= ':'.$servera['port'];
        }
      }
    }
    if(!empty($servera['path'])){
      $this->server .= $servera['path'];
    }
  }

  /**
   * @param string $url
   * @param bool $intern
   */
  public function execute($url, $intern = true)
  {
    $location = $this->getLocationUrl($url, $intern);
    header('Location: '.$location);
    $this->app->ExitXentral();
  }

  /**
   * @param string $url
   * @param bool $intern
   *
   * @return string
   */
  public function getLocationUrl($url, $intern = true)
  {
    if(stripos($url,'Location:') === 0)
    {
      $url = trim(substr($url,9));
    }
    if($intern)
    {
      $posIndex = strpos($url,'index.php');
      if($posIndex === false)
      {
        $url = 'index.php';
      }elseif($posIndex > 0){
        $url = substr($url, $posIndex);
      }
      $urla = parse_url($url);
      $urla['query'] = $this->prepareQuery($urla['query']);

      $url = $this->server.'?'
        .(!empty($urla['query'])?$urla['query']:'')
        .(!empty($urla['fragment'])?'#'.$urla['fragment']:'');
      return $url;
    }

    return $url;
  }

  /**
   * @param string $query
   *
   * @return string
   */
  protected function prepareQuery($query)
  {
    $querya = explode('&', $query);
    foreach($querya as $k => $v)
    {
      $va = explode('=', $v);
      if($va[0] === 'msg')
      {
        $querya[$k] = $this->transformMsg($va);
      }
    }
    return implode('&', $querya);
  }

  /**
   * @param int $id
   *
   * @return string
   */
  public function getMessage($id)
  {
    $msgKey = 'msg_'.$id;
    if(isset($_COOKIE[$msgKey]))
    {
      $message = $_COOKIE[$msgKey];
      setcookie($msgKey,'',time()-86400);
      return $message;
    }
    return '';
  }

  /**
   * @param array $query
   *
   * @return string
   */
  protected function transformMsg(Array $query)
  {
    if($query[0] === 'msg')
    {
      if(isset($query[1]))
      {
        $id = mt_rand();
        setcookie('msg_'.$id, (String)$query[1], time()+3600);
        return 'msgs='.$id;
      }

      return 'msgs=';
    }
    return '';
  }

  /**
   * @return string
   */
  public function getServer()
  {
    return $this->server;
  }

  /**
   * @param string $server
   * @param string $port
   *
   * @return string
   */
  protected function createServerByUrlAndPort($server, $port) {
    if(empty($server)) {
      return '';
    }
    $parsedUrl = parse_url($server);
    if(!empty($port)) {
      $port = ':'.$port;
    }
    $userpw = '';
    if(!empty($parsedUrl['user'])) {
      if(!empty($parsedUrl['pass'])) {
        $userpw = $parsedUrl['user'].':'.$parsedUrl['pass'].'@';
      } else {
        $userpw = $parsedUrl['user'].'@';
      }
    }
    $path = '';
    if(!empty($parsedUrl['path'])) {
      $path = $parsedUrl['path'];
    }
    if(substr($path,-1) !== '/' && substr($path,-9) !=='index.php') {
      $path .= '/';
    }
    return $parsedUrl['scheme'].'://'.$userpw.$parsedUrl['host'].$port.$path;
  }
}
