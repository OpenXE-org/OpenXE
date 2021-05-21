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
 * Class Shopimporter_Gambio_Adapter
 */
class Shopimporter_Gambio_Adapter {
  const METHODE_GET    = 'GET';
  const METHODE_PUT    = 'PUT';
  const METHODE_POST   = 'POST';
  const METHODE_PATCH   = 'PATCH';
  const METHODE_DELETE = 'DELETE';

  /**
   * @var Application
   */
  protected $app;

  protected $apiUrl;
  protected $cURL;

  protected $protokoll;

  /**
   * Shopimporter_Gambio_Adapter constructor.
   *
   * @param Application $app
   * @param string      $apiUrl
   * @param string      $user
   * @param string      $pass
   */
  public function __construct($app, $apiUrl, $user, $pass) {
    $this->app = $app;
    $this->apiUrl = rtrim($apiUrl, '/') . '/';
    $this->cURL = curl_init();
    curl_setopt($this->cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset=utf-8'));
    curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($this->cURL, CURLOPT_USERPWD, $user.':'.$pass);
  }

  /**
   * @param string $endpoint
   * @param string $method
   * @param array  $data
   * @param string $params
   *
   * @param bool   $asArray
   *
   * @return mixed
   */
  public function call($endpoint, $method = self::METHODE_GET, $data = array(), $params='', $asArray = false) {
    $endpoint = rtrim($endpoint, '?') . '?';
    $url = $this->apiUrl . $endpoint . $params;
    $dataString = json_encode($data);
    curl_setopt($this->cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset=utf-8'));
    curl_setopt($this->cURL, CURLOPT_URL, $url);
    curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $dataString);
    $result   = curl_exec($this->cURL);
    return json_decode($result,$asArray);
  }

  /**
   * @param string $bildpfad
   * @param string $bildtyp
   * @param string $bildname
   *
   * @return mixed
   */
  public function imagecall($bildpfad, $bildtyp = 'image/png', $bildname = 'image') {
    $url = $this->apiUrl.'product_images';
    $data = new CURLFile($bildpfad,$bildtyp,$bildname);
    $post = [
      'filename' => $bildname,
      'upload_product_image' => $data
    ];
    curl_setopt($this->cURL, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data;'));
    curl_setopt($this->cURL, CURLOPT_URL, $url);
    curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, self::METHODE_POST);
    curl_setopt($this->cURL, CURLOPT_POST,1);
    curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($this->cURL);
    return json_decode($result);
  }

  /**
   * @param        $nachricht
   * @param string $dump
   */
  public function GambioLog($nachricht, $dump = '')
  {
    if($this->protokoll){
      $this->app->erp->LogFile($nachricht, print_r($dump, true));
    }
  }

  /**
   * @param $protokoll
   */
  public function setProtokoll($protokoll){
    $this->protokoll = $protokoll;
  }

}
