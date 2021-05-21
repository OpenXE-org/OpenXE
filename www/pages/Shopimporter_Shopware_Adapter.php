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
 * Class Shopimporter_Shopware_Adapter
 */
class Shopimporter_Shopware_Adapter {
  protected $apiUrl;
  protected $cURL;

  /**
   * Shopimporter_Shopware_Adapter constructor.
   *
   * @param $apiUrl
   * @param $username
   * @param $apiKey
   * @param $useDigestAuth
   */
  public function __construct($apiUrl, $username, $apiKey, $useDigestAuth) {
    $this->apiUrl = rtrim($apiUrl, '/') . '/';
    //Initializes the cURL instance
    $this->cURL = curl_init();
    curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    if($useDigestAuth){
      curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    }
    curl_setopt($this->cURL, CURLOPT_USERPWD, $username . ':' . $apiKey);
    curl_setopt($this->cURL, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json; charset=utf-8',
    ));
  }

  /**
   * @param        $url
   * @param string $method
   * @param array  $data
   * @param array  $params
   * @param string $direktquery
   *
   * @return mixed|string|void
   */
  public function call($url, $method = 'GET', $data = array(), $params = array(), $direktquery = '') {
    $queryString = '';
    if (!empty($params)) {
      $queryString = http_build_query($params);
    }
    $url = rtrim($url, '?') . '?';
    $url = $this->apiUrl . $url . $queryString . $direktquery;
    $dataString = json_encode($data);

    curl_setopt($this->cURL, CURLOPT_URL, $url);
    curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $dataString);
    curl_setopt($this->cURL, CURLOPT_VERBOSE, 0);
    $result   = curl_exec($this->cURL);

    $httpCode = curl_getinfo($this->cURL, CURLINFO_HTTP_CODE);
    return $this->prepareResponse($result, $httpCode);
  }

  /**
   * @param        $url
   * @param array  $params
   * @param string $direktqery
   *
   * @return mixed|string|void
   */
  public function get($url, $params = array(), $direktqery='') {
    return $this->call($url, 'GET', array(), $params, $direktqery);
  }

  /**
   * @param        $url
   * @param array  $data
   * @param array  $params
   * @param string $direktqery
   *
   * @return mixed|string|void
   */
  public function post($url, $data = array(), $params = array(), $direktqery='') {
    return $this->call($url, 'POST', $data, $params, $direktqery);
  }

  /**
   * @param        $url
   * @param array  $data
   * @param array  $params
   * @param string $direktqery
   *
   * @return mixed|string|void
   */
  public function put($url, $data = array(), $params = array(), $direktqery='') {
    return $this->call($url, 'PUT', $data, $params, $direktqery);
  }

  /**
   * @param        $url
   * @param array  $params
   * @param string $direktqery
   *
   * @return mixed|string|void
   */
  public function delete($url, $params = array(), $direktqery='') {
    return $this->call($url, 'DELETE', array(), $params, $direktqery);
  }

  /**
   * @param $result
   * @param $httpCode
   *
   * @return mixed|string|void
   */
  protected function prepareResponse($result, $httpCode) {

    if (null === $decodedResult = json_decode($result, true)) {
      return;
    }
    if (!isset($decodedResult['success'])) {

      return 'error: invalid response from shopware api';
    }
    if (!$decodedResult['success']) {

      return 'error: no success vom shopware api ('.$decodedResult['message'].')';
    }
    return $decodedResult;
  }
}
