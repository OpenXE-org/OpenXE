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

/**
 * Class Shopimporter_Epages_Adapter
 */
class Shopimporter_Epages_Adapter {

  /** * @var false|resource curl */
  protected $curl;
  /** @var string url */
  protected $url;
  /** @var string token */
  protected $token;

  /**
   * EpagesAdapter constructor.
   *
   * @param string $url
   * @param string $token
   */
  public function __construct($url, $token)
  {
    $this->url = rtrim($url, '/').'/';
    $this->token = $token;
    $this->curl = curl_init();
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Authorization: Bearer '.$token]);
  }

  /**
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = rtrim($url, '/').'/';
  }

  /**
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Authorization: Bearer '.$token]);
  }

  /**
   * @param string $method
   * @param string $endpoint
   * @param array $data
   * @param array $params
   *
   * @return array|string
   */
  public function call($method, $endpoint, $data = [], $params =[])
  {
    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($this->curl, CURLOPT_URL, $this->url.$endpoint.'?'.http_build_query($params));

    $responseRaw = curl_exec($this->curl);
    $response = json_decode($responseRaw ,true);

    if(empty($response)){
      return $responseRaw;
    }

    return $response;
  }

  /**
   * @param string $endpoint
   * @param array $params
   *
   * @return array|string
   */
  public function get($endpoint, $params = [])
  {
    return $this->call('GET', $endpoint, '', $params);
  }

  /**
   * @param string $endpoint
   * @param array $data
   * @param array $params
   *
   * @return array|string
   */
  public function put($endpoint, $data, $params = []){
    return $this->call('PUT', $endpoint, $data, $params);
  }

  /**
   * @param string $endpoint
   * @param array $data
   * @param array $params
   *
   * @return array|string
   */
  public function patch($endpoint, $data, $params = [])
  {
    return $this->call('PATCH', $endpoint, $data, $params);
  }

  /**
   * @param string $endpoint
   * @param array $data
   * @param array $params
   *
   * @return array|string
   */
  public function post($endpoint, $data, $params = [])
  {
    return $this->call('POST', $endpoint, $data, $params);
  }

  /**
   * @param string $endpoint
   * @param array $params
   *
   * @return array|string
   */
  public function delete($endpoint, $params = []){
    return $this->call('DELETE', $endpoint, $params);
  }

  public function postImage($productId, $filename, $multiPartImageData){
    $boundary = 'MIME_boundary';
    $CRLF = "\r\n";
    $data = '--' . $boundary . $CRLF;
    $data .= 'Content-Disposition: form-data; name="image"; filename="'.$filename.'"'.$CRLF;
    $data .= 'Content-Transfer-Encoding: binary' . $CRLF;
    $data .= 'Content-Type: application/octet-stream' . $CRLF . $CRLF;
    $data .= $multiPartImageData;
    $data .= $CRLF;
    $data .= '--' . $boundary . '--' . $CRLF;


    $headers = array (
      'Content-Type: multipart/form-data; boundary='.$boundary,
      'Content-Length: '.strlen($data)
    );

    $connection = curl_init();
    $url = $this->url.'products/'.$productId.'/slideshow?';
    curl_setopt($connection, CURLOPT_URL, $url);
    curl_setopt($connection, CURLOPT_TIMEOUT, 30 );
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
    curl_setopt($connection, CURLOPT_POST, 1);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_FAILONERROR, 0 );
    curl_setopt($connection, CURLOPT_FOLLOWLOCATION, 1 );
//    curl_setopt($connection, CURLOPT_USERAGENT, 'ebatns;xmlstyle;1.0' );
    curl_setopt($connection, CURLOPT_HTTP_VERSION, 1 );
    $response = curl_exec($connection);
    return json_decode($response,true);
  }

}
