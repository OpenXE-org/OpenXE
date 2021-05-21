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
 * Class Shopimporter_Magento2_Adapter
 */
class Shopimporter_Magento2_Adapter {
  /**
   * @var Application
   */
  protected $app;

  protected $apiUrl;

  protected $user;
  protected $pass;

  protected $accessToken;

  /**
   * Shopimporter_Magento2_Adapter constructor.
   *
   * @param $app
   * @param $apiUrl
   * @param $user
   * @param $pass
   */
  public function __construct($app, $apiUrl, $user, $pass) {
    $this->app = $app;
    $this->apiUrl = rtrim($apiUrl, '/') . '/';
    $this->user = $user;
    $this->pass = $pass;
  }

  /**
   * @param        $method
   * @param        $endpoint
   * @param string $data
   *
   * @param string $storeview
   * @return array
   */
  public function call($method,$endpoint,$data='',$storeview='')
  {
    if(empty($this->accessToken)){
      $tokenResult = $this->checkConnection();
      if(!$tokenResult['success']){
        return $tokenResult;
      }
    }

    $url = $this->apiUrl;
    if (substr($url, -1) !== '/') {
      $url .= '/';
    }

    if(!empty($storeview) && substr($storeview, -1) !== '/'){
      $storeview .= '/';
    }

    $ch = curl_init();
    $setHeaders = array('Content-Type:application/json','Authorization:Bearer '.$this->accessToken);
    curl_setopt($ch, CURLOPT_URL, $url.'rest/'.$storeview.'V1/'.$endpoint);
    if(!empty($data)){
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $response = [
      'success' => false,
      'message' => '',
      'data' => null
    ];

    $response['data'] = json_decode($result, true);
    if(curl_error($ch))
    {
      $response['message']= curl_error($ch);
    }else{
      $response['success'] = true;
    }
    curl_close($ch);

    return $response;
  }

  /**
   * @return array
   */
  public function checkConnection()
  {
    if(!empty($this->accessToken)){
      return [
        'success' => true,
        'message' => $this->accessToken
        ];
    }

    $data = [
      'username' => $this->user,
      'password' => $this->pass
    ];
    $data_string = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'rest/V1/integration/admin/token');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
      ]
    );
    $resultJson = curl_exec($ch);
    $result = json_decode($resultJson,true);

    $response = [
      'success' => false,
      'message' => 'Keine Rückmeldung von API.'
    ];

    if(!empty($result['message'])){
      $response['message'] = $result['message'];
    }elseif(!empty($result)){
      $this->accessToken = $result;
      $response['message'] = $result;
      $response['success'] = true;

      curl_setopt($ch, CURLOPT_HTTPHEADER, [
          'Content-Type:application/json',
          'Authorization:Bearer '.$this->accessToken
        ]
      );
    }

    return $response;
  }

  /**
   *
   */
  public function resetToken(){
    $this->accessToken = null;
  }
}