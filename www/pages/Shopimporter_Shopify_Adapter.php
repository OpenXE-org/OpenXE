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
 * Class Shopimporter_Shopify_Adapter
 */
class Shopimporter_Shopify_Adapter
{
  /**
   * @var Application
   */
  protected $app;
  protected $apiUrl;
  protected $shopId;
  protected $token;
  protected $apiVersion = '2019-10';
  protected static $requestcount = [];

  /**
   * Shopimporter_Shopify_Adapter constructor.
   *
   * @param $app
   * @param $apiUrl
   * @param $shopId
   * @param $token
   */
  public function __construct($app, $apiUrl, $shopId, $token = '') {
    $this->app = $app;
    $this->shopId = $shopId;
    $this->apiUrl = rtrim($apiUrl, '/') . '/';
    $this->token = $token;
  }

  protected function throttling(){
    if(empty(self::$requestcount[$this->shopId]))
    {
      self::$requestcount[$this->shopId] = 1;
    }else{
      self::$requestcount[$this->shopId]++;
    }
    if(self::$requestcount[$this->shopId] >= 30)
    {
      sleep(2);
      self::$requestcount[$this->shopId]-=2;
    }elseif(self::$requestcount[$this->shopId] >= 20)
    {
      sleep(2);
      self::$requestcount[$this->shopId]-=2;
    }
  }

  /**
   * @param        $path
   * @param string $anweisung
   * @param string $data
   * @param bool   $returnFormated
   *
   * @return array
   */
  public function call($path, $anweisung = '', $data = '', $returnFormated = true, $options = null)
  {
    $this->throttling();

    $headers = [];
    $url = $this->apiUrl.'admin/api/'.$this->apiVersion.'/'.$path;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    $header = ['Content-Type: application/json; charset=utf-8'];
    if(!empty($this->token)) {
      $header[] = 'X-Shopify-Access-Token: '.$this->token;
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_VERBOSE, 0);
    if(!empty($anweisung)){
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $anweisung);
    }
    if(!empty($data)){
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, $options));
    }
    curl_setopt($curl, CURLOPT_HEADERFUNCTION,
      function($curl, $header) use (&$headers)
      {
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2){
          // ignore invalid headers
          return $len;
        }
        $headers[strtolower(trim($header[0]))][] = trim($header[1]);
        return $len;
      }
    );

    $response = curl_exec ($curl);
    $httpcode = curl_getinfo($curl);
    $httpcode = (int)$httpcode['http_code'];
    if($httpcode === 429)
    {
      sleep(5);
      self::$requestcount[$this->shopId] = 40;
      return $this->call($path, $anweisung, $data);
    }
    curl_close ($curl);
    if(!empty($this->app)){
      if(!empty($response['errors']) && preg_match_all('/\This action requires merchant approval for ([a-zA-Z\_]+) scope/',$response['errors'], $erg) && !empty($erg[1]))
      {
        $query = sprintf("INSERT INTO `shopexport_log` (`shopid`, `typ`,`parameter1`,`parameter2`,`bearbeiter`,`zeitstempel`)
        VALUES ('%s','fehler','Fehlendes API-Recht: %s','%s','%s',now())",
          $this->shopId,
          $erg[1][0],
          $response['errors'],
          $this->app->erp->GetBearbeiter(true)
        );
        $this->app->DB->Insert($query);
      }
    }

    $links = [];

    if(!empty($headers['link'])){
      foreach ($headers['link'] as $link) {
        $link = str_replace(' ', '', $link);
        $linkData = explode(',', $link);
        foreach ($linkData as $linkwithInfo) {
          $linkParts = explode(';', $linkwithInfo);
          $queryData = explode('?', $linkParts[0]);
          $relationData = explode('=', str_replace('"', '', $linkParts[1]));
          $relation = $relationData[1];
          $links[$relation] = substr($queryData[1], 0, -1);
        }
      }
    }

    $result = [
      'data' => $response,
      'links' => $links
    ];
    if($returnFormated){
      $result['data'] = json_decode($response,true);
    }

    return $result;
  }


}
