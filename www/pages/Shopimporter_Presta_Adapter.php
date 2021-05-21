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
 * Class Shopimporter_Presta_Adapter
 */
class Shopimporter_Presta_Adapter {
  /**
   * @var Application
   */
  protected $app;

  protected $apiUrl;
  protected $cURL;

  /** @var string $useKeyAsParameter*/
  protected $useKeyAsParameter;
  /** @var string $apiKey*/
  protected $apiKey;

  protected $protokoll;

    /**
     * Shopimporter_Presta_Adapter constructor.
     *
     * @param Application $app
     * @param string $apiUrl
     * @param string $apiKey
     * @param string $useKeyAsParameter
     */
  public function __construct($app, $apiUrl, $apiKey, $useKeyAsParameter) {

    $this->app = $app;
    $this->apiUrl = rtrim($apiUrl, '/') . '/';
    $this->cURL = curl_init();
    $this->useKeyAsParameter = $useKeyAsParameter;
    $this->apiKey = $apiKey;

    if(!$useKeyAsParameter){
        curl_setopt($this->cURL, CURLOPT_USERPWD, "$apiKey:");
    }
    curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($this->cURL, CURLOPT_TIMEOUT, 60);
    curl_setopt($this->cURL, CURLOPT_HTTPHEADER, array('Expect:'));
  }

  /**
   * @param string $anweisung
   * @param bool $returnstring
   *
   * @return bool|SimpleXMLElement|string|null
   */
  public function call($anweisung, $returnstring = false)
  {
    $ressource = $anweisung['res'];
    $xml = $anweisung['xml'];
    $typ = $anweisung['typ'];
    $filter = $anweisung['filter'];
    if($filter){
      $filter = '?'.$filter;
    }

    $url = $this->apiUrl.$ressource.$filter;

    if($this->useKeyAsParameter){
        if(strpos($url,'?') === false){
            $url .= '?ws_key='.$this->apiKey;
        }else{
            $url .= '&ws_key='.$this->apiKey;
        }
    }
    curl_setopt($this->cURL, CURLOPT_URL, $url);

    if($typ !== 'GET'){
      curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $typ);
      curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $xml);
    }
    $response = curl_exec($this->cURL);
    if(!$returnstring){
      $this->Prestalog($anweisung,$response);
      try {
        $response = simplexml_load_string($response, null, LIBXML_NOCDATA);;
      }catch(Exception $e)
      {
        $response = null;
      }
    }
    return $response;
  }

    /**
     * @param string $imagePath
     * @param int $articleId
     * @return SimpleXMLElement|null
     */
    public function sendImage($imagePath, $articleId){
        $url = $this->apiUrl.'images/products/'.$articleId;

        if($this->useKeyAsParameter){
            $url .= '?ws_key='.$this->apiKey;
        }else{
            curl_setopt($this->cURL, CURLOPT_USERPWD, $this->apiKey.':');
        }

        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, array('image' => curl_file_create($imagePath)));
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->cURL, CURLOPT_TIMEOUT, 60);
        $response = curl_exec($this->cURL);

        try {
            return new SimpleXMLElement($response);
        }catch(Exception $e)
        {
            return null;
        }
    }


  /**
   * @param        $nachricht
   * @param string $dump
   */
  public function Prestalog($nachricht, $dump = '')
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
