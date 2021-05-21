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
 * Class Shopimporter_Ebay_Adapter
 */
class Shopimporter_Ebay_Adapter
{
  /**
   * @var Application
   */
  protected $app;

  protected $devID;
  protected $appID;
  protected $certID;
  protected $siteID;
  protected $baseUrl = 'https://api.ebay.com/ws/api.dll';
  protected $sandboxUrl = 'https://api.sandbox.ebay.com/ws/api.dll';
  protected $bulkExchangeUrl = 'https://webservices.ebay.com/BulkDataExchangeService';
  protected $sandboxBulkExchangeUrl = 'https://webservices.sandbox.ebay.com/BulkDataExchangeService';
  protected $fileTransferUrl = 'https://storage.ebay.com/FileTransferService';
  protected $sandboxFileTransferUrl = 'https://storage.sandbox.ebay.com/FileTransferService';
  protected $userToken = '';
  const APIVERSION = '1037';

  public const PROTOCOL_DISABLED = 0;
  public const PROTOCOL_ACTIVATED_SIMPLE = 1;
  public const PROTOCOL_ACTIVATED_EXTENDED = 2;

  protected $protokoll;

  /** @var string|bool $lastResponse */
  protected $lastResponse = false;

  /** @var array $countCalls */
  protected $countCalls = [];


  /**
   * Shopimporter_Ebay_Adapter constructor.
   *
   * @param      $app
   * @param      $devID
   * @param      $appID
   * @param      $certID
   * @param      $siteID
   * @param bool $sandbox
   * @param string $userToken
   */
  public function __construct($app, $devID,$appID,$certID,$siteID, $sandbox=false, $userToken = '')
  {
    $this->app=$app;
    $this->devID=$devID;
    $this->appID=$appID;
    $this->certID=$certID;
    $this->siteID=$siteID;
    $this->userToken = $userToken;
    if($sandbox){
      $this->baseUrl = $this->sandboxUrl;
      $this->bulkExchangeUrl = $this->sandboxBulkExchangeUrl;
      $this->fileTransferUrl = $this->sandboxFileTransferUrl;
    }
  }

  /**
   * @param      $apicall
   * @param      $requestbody
   * @param bool $giveerrormessage
   *
   * @return bool|SimpleXMLElement|string|null
   */
  public function call($apicall, $requestbody, $giveerrormessage = false)
  {
    $this->eBayLog($apicall,print_r($requestbody,true));
    $fehler = array();
    $requestHeader = array('X-EBAY-API-COMPATIBILITY-LEVEL: '.self::APIVERSION,
      'X-EBAY-API-DEV-NAME:' . $this->devID,
      'X-EBAY-API-APP-NAME:' . $this->appID,
      'X-EBAY-API-CERT-NAME:' . $this->certID,
      'X-EBAY-API-CALL-NAME:' . $apicall,
      'X-EBAY-API-SITEID:'. $this->siteID
    );
    $requestbody = '<?xml version="1.0" encoding="utf-8"?>'.$requestbody;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
    // Verhindere dass curl die Zertifizierung der SSL Verbindung überprüft
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestbody);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6000);

    $versuche = 0;
    do {
      $error = false;
      $responseXml = curl_exec($ch);
      $this->lastResponse = $responseXml;
      if(!isset($this->countCalls[$apicall])) {
        $this->countCalls[$apicall] = 1;
      }
      else {
        $this->countCalls[$apicall]++;
      }
      if($responseXml === false){
        $error = 'Keine Rueckmeldung der API.';
      }
      else if((int)$this->protokoll === self::PROTOCOL_ACTIVATED_EXTENDED){
        $this->eBayLog($apicall . ' Request Header', print_r($requestHeader, true));
        $this->eBayLog($apicall . ' Request', $requestbody);
        $this->eBayLog($apicall . ' Response', $responseXml);
      }
      if(!($responseXml == null)){
        try {
          $response = new SimpleXMLElement($responseXml);
        }catch(Exception $e)
        {
          $response = null;
        }
        if($response == null)
        {
          if(!empty($responseXml))
          {
            $error = 'XML konnte nich geparst werden.';
          }else{
            $error = 'Antwort ist leer.';
          }

        }elseif($apicall==='getOrders'){
          //Wenn Aufträge abgeholt werden aber fehlerhafte vorhanden sind wird in ImportGetAuftrag weitergeschleift und aussortiert
          if((bool)$response->HasMoreOrders){
            $error ='';
            $response->Ack = 'Success';
          }
        }elseif($apicall==='GetUser'){
          if((string)$response->User->UserID != ''){
            $error = '';
            $response->Ack = 'Success';
          }
        }else{
          foreach ($response->Errors as $key => $value) {
            if((string)$value->LongMessage != ''){
              $error = (string)$value->LongMessage;
            }else{
              $error = (string)$value->Error->ShortMessage;
            }
          }
        }

        if((string)$response->Ack !== 'Success' && (string)$response->Ack !== 'Warning')
        {
          if(empty($response)){
            $error = 'Möglicherweise liegt das Problem bei eBay.';
          }else{
            $errorobjekt = $response->Errors;
            if(is_array($errorobjekt)){
              $errorobjekt = $errorobjekt[0];
            }
            $error = (string)$errorobjekt->LongMessage;
            switch ((int)$errorobjekt->ErrorCode){
              case 0:
                $error = 'Ein unbekannter Fehler ist aufgetreten';
                break;
              case 932;
                $error = (string)$errorobjekt->LongMessage;
                $this->app->erp->InternesEvent($this->app->User->GetID(),'Das Authentifizierungstoken des eBay Importers ist abgelaufen','warning',1);
                break;
              case 518:
              case 21919188:
                $versuche = 10;
                $this->eBayLog('Verkaufslimit erreicht', print_r($error,true));
                $this->app->erp->InternesEvent($this->app->User->GetID(),'Das Verkaufslimit für den eBay Importer wurde erreicht','warning',1);
                break;
              default:
                $versuche = 10;
                $error = (string)$errorobjekt->LongMessage;
                break;
            }
          }
        }else{
          if($giveerrormessage){
            $response = 'success';
          }
          break;
        }
      }
      $versuche ++;
    } while($versuche < 2 && $error);
    curl_close($ch);

    if(!empty($error)){
      $this->eBayLog($apicall." failed (Versuche: $versuche)", print_r($requestHeader,true));
      $this->eBayLog($apicall.' failed response', print_r($response,true));
      $this->eBayLog($apicall.' failed request', print_r($requestbody,true));
      if(isset($response->Errors) && $response->Errors){
        foreach ($response->Errors as $key => $value) {
          if((string)$value->ErrorCode === '21919067'){
            $this->fremdnummertmp = (string)$value->ErrorParameters[1]->Value;
          }
        }
      }
      if($giveerrormessage){
        return $error;
      }
      if($apicall === 'GetItem') {
        return $response;
      }

      return false;
    }

    $this->eBayLog($apicall." success (Versuche: $versuche)", print_r($response,true));

    return $response;
  }

  /**
   * @return mixed
   */
  public function getLastResponse()
  {
    return $this->lastResponse;
  }

  /**
   * @return array
   */
  public function getCountCalls(): array
  {
    return $this->countCalls;
  }

  public function resetCountCalls(): void
  {
    $this->countCalls = [];
  }


  /**
   * @param $apicall
   * @param $requestbody
   *
   * @return array
   */
  public function propperCall($apicall, $requestbody)
  {
    $response = [
      'success' => false,
      'data' => '',
      'message' => 'no Message'
    ];
    $this->eBayLog($apicall,print_r($requestbody,true));

    $requestHeader = array('X-EBAY-API-COMPATIBILITY-LEVEL: '.self::APIVERSION,
      'X-EBAY-API-DEV-NAME:' . $this->devID,
      'X-EBAY-API-APP-NAME:' . $this->appID,
      'X-EBAY-API-CERT-NAME:' . $this->certID,
      'X-EBAY-API-CALL-NAME:' . $apicall,
      'X-EBAY-API-SITEID:'. $this->siteID
    );
    $requestbody = '<?xml version="1.0" encoding="utf-8"?>'.$requestbody;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
    // Verhindere dass curl die Zertifizierung der SSL Verbindung überprüft
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestbody);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6000);

    $versuche = 0;
    do {
      $response['data'] = '';
      $ebayResponse = curl_exec($ch);
      $this->lastResponse = $ebayResponse;
      if(!isset($this->countCalls[$apicall])) {
        $this->countCalls[$apicall] = 1;
      }
      else {
        $this->countCalls[$apicall]++;
      }
      if(!empty($ebayResponse)){
        $xml = null;
        try {
          $xml = new SimpleXMLElement($ebayResponse);
          $response['data'] = $xml;
        }catch(Exception $e)
        {
          $response['message'] = 'Response could not be parsed.';
        }

        if($apicall==='getOrders'){
          //Wenn Aufträge abgeholt werden aber fehlerhafte vorhanden sind wird in ImportGetAuftrag weitergeschleift und aussortiert
          if((bool)$xml->HasMoreOrders){
            $response['success'] = true;
            $xml->Ack = 'Success';
          }
        }elseif($apicall==='GetUser'){
          if((string)$xml->User->UserID != ''){
            $xml->Ack = 'Success';
            $response['success'] = true;
          }
        }elseif((string)$xml->Ack !== 'Failure'){
          $error = false;
          foreach ($xml->Errors as $key => $value) {
            if($value->SeverityCode === 'Error'){
              $error = true;
              if((string)$value->LongMessage != ''){
                $response['message'] = (string)$value->LongMessage;
              }else{
                $response['message'] = (string)$value->Error->ShortMessage;
              }
            }
          }
          if(!$error){
            $response['success'] = true;
          }
        }

        if((string)$xml->Ack !== 'Success' && (string)$xml->Ack !== 'Warning')
        {
          foreach ($xml->Errors as $errorobjekt){
            if((string)$errorobjekt->SeverityCode === 'Error'){
              $response['message'] = (string)$errorobjekt->LongMessage;
              switch ((int)$errorobjekt->ErrorCode){
                case 0:
                  $response['message'] = 'Ein unbekannter Fehler ist aufgetreten.';
                  break;
                case 932;
                  $response['message'] = 'Das Authentifizierungstoken des eBay Importers ist abgelaufen.';
                  $this->app->erp->InternesEvent($this->app->User->GetID(),'Das Authentifizierungstoken des eBay Importers ist abgelaufen','warning',1);
                  break;
                case 518:
                case 21919144:
                  $versuche = 10;
                  $this->eBayLog('Call Limit wurde erreicht', print_r($error,true));
                  $this->app->erp->InternesEvent($this->app->User->GetID(),'Das Call Limit für Ihren eBay Account wurde erreicht','warning',1);
                  break;
                case 21919188:
                  $versuche = 10;
                  $response['message'] = 'Das Verkaufslimit für Ihren eBay Account wurde erreicht.';
                  $this->eBayLog('Verkaufslimit erreicht', print_r($error,true));
                  $this->app->erp->InternesEvent($this->app->User->GetID(),'Das Verkaufslimit für Ihren eBay Account wurde erreicht','warning',1);
                  break;
              }
              break;
            }
          }
        }else{
          break;
        }
      }else{
        $response['message'] = 'Keine Rueckmeldung der API.';
      }
      $versuche ++;
    } while($versuche < 2 && $error);
    curl_close($ch);

    if(!$response['success']){
      $this->eBayLog($apicall." failed (Versuche: $versuche)", print_r($requestHeader,true));
      $this->eBayLog($apicall.' failed response', print_r($response['data'],true));
      $this->eBayLog($apicall.' failed request', print_r($requestbody,true));
      $this->eBayLog($apicall." success (Versuche: $versuche)", print_r($xml,true));
    }
    return $response;
  }


  /**
   * @param        $nachricht
   * @param string $dump
   */
  protected function eBayLog($nachricht, $dump = ''){
    if($this->protokoll !== self::PROTOCOL_DISABLED){
      $this->app->erp->LogFile($nachricht, $this->app->DB->real_escape_string($dump));
    }
  }

  /**
   * @param $protokoll
   */
  public function setProtokoll($protokoll){
    $this->protokoll = (int)$protokoll;
  }

  /**
   * @return string
   */
  public function getBulkExchangeApiUrl(){
    return $this->bulkExchangeUrl;
  }

  /**
   * @return string
   */
  public function getFileTransferApiUrl(){
    return $this->fileTransferUrl;
  }
}
