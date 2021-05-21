<?php

use Xentral\Components\HttpClient\Exception\TransferErrorExceptionInterface;
use Xentral\Components\HttpClient\HttpClient;
use Xentral\Components\HttpClient\RequestOptions;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountNotFoundException;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleApi\Service\GoogleAuthorizationService;

class PrinterGoogleCloudPrint extends PrinterBase
{
  /** @var string */
  private $url = 'https://www.google.com/cloudprint/';

  /** @var string URL_SEARCH */
  private const URL_SEARCH = 'https://www.google.com/cloudprint/search';

  /** @var string URL_PRINT */
  private const URL_PRINT = 'https://www.google.com/cloudprint/submit';

  /** @var string */
  private const URL_JOBS = 'https://www.google.com/cloudprint/jobs';

    /** @var string */
  private const URL_PRINTER = 'https://www.google.com/cloudprint/printer';

  /** @var HttpClient $client */
  private $client;

  /**
   * PrinterGoogleCloudPrint constructor.
   *
   * @param Application $app
   * @param int         $id
   */
  public function __construct($app, $id)
  {
    parent::__construct($app, $id);
    $token = $this->getAuthToken();
    $options = new RequestOptions();
    $options->setHeader('Authorization', sprintf('Bearer %s', $token));
    $this->client = new HttpClient($options);
    $this->app->ModuleScriptCache->IncludeJavascriptFiles(
        'drucker',
        ['./classes/Modules/GoogleCloudPrint/www/js/PrinterGoogleCloudPrint.js']
    );
  }

  /**
   * @return string
   */
  public static function getName() {
    return 'Google Cloud Print';
  }

  /**
   * @return array
   */
  public function getPrinters()
  {
    try {
        $response = $this->client->request('GET', self::URL_SEARCH);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result['printers'];
    } catch (Exception $e) {
        return [];
    }
  }

  /**
   * @return array
   */
  public function SettingsStructure()
  {
    $googlePrinters = [];
    try {
        $googlePrinterArr = $this->getPrinters();
    } catch (Exception $e) {
        return [];
    }
    foreach($googlePrinterArr as $item) {
        $googlePrinters[$item['id']] = sprintf('%s:%s', $item['displayName'], $item['connectionStatus']);
    }

    return [
        'google_printer' => ['bezeichnung' => 'Drucker:','typ' => 'select', 'optionen' => $googlePrinters],
    ];
  }

  /**
   * @param string $document
   * @param int    $count
   *
   * @return bool
   */
  public function printDocument($document, $count = 1)
  {
    if(empty($this->settings['google_printer'])) {
      return false;
    }
    if($count < 1) {
      $count = 1;
    }
    $title = '';
    $contenttype = 'application/pdf';
    if(is_file($document)) {
      $title = basename($document);
      $document = file_get_contents($document);
    }
    $title .= date('YmdHis');
    $titleFirst = $title;
    for($i = 1; $i <= $count; $i++) {
      if($i > 1) {
        $title = $titleFirst.$i;
      }
      $postFields = array(
        'printerid' => $this->settings['google_printer'],
        'title' => $title,
        'contentTransferEncoding' => 'base64',
        'content' => base64_encode($document),
        'contentType' => $contenttype
      );
      try {
          $response = $this->client->request('POST', self::URL_PRINT, [], json_encode($postFields));
          $data = json_decode($response->getBody()->getContents(), true);

          return (isset($data['success']) && $data['success'] === true);
      } catch (TransferErrorExceptionInterface $e) {
          return false;
      }
    }
    return true;
  }

  protected function getAuthToken()
  {
      try {
          /** @var GoogleAccountGateway $gateway */
          $gateway = $this->app->Container->get('GoogleAccountGateway');
          $account = $gateway->getCloudPrintAccount();
          $token = $gateway->getAccessToken($account->getId());
      } catch (GoogleAccountNotFoundException $e) {
          throw new GoogleAccountNotFoundException($e->getMessage(), $e->getCode(), $e);
      } catch (Exception $e) {
          $token = null;
      }
      if ($token === null || $token->getTimeToLive() < 10) {
          /** @var GoogleAuthorizationService $auth */
          $auth = $this->app->Container->get('GoogleAuthorizationService');
          $token = $auth->refreshAccessToken($account);
      }

      return $token->getToken();
  }
}