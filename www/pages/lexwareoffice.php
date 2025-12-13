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

use Xentral\Modules\LexwareOffice\Exception\LexwareOfficeException;
use Xentral\Modules\LexwareOffice\Service\LexwareOfficeApiClient;
use Xentral\Modules\LexwareOffice\Service\LexwareOfficeConfigService;
use Xentral\Modules\LexwareOffice\Service\LexwareOfficeService;

class Lexwareoffice
{
  /** @var Application */
  public $app;

  /** @var LexwareOfficeService|null */
  private $service = null;

  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    if($intern) {
      return;
    }

    $this->ensureSuperSearchIndex();

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler('edit','LexwareOfficeEdit');
    $this->app->DefaultActionHandler('edit');

    $this->app->Tpl->Set('UEBERSCHRIFT','Lexware Office');
    $this->app->Tpl->Set('FARBE','[FARBE5]');

    $this->app->ActionHandlerListen($app);
  }

  public function LexwareOfficeEdit()
  {
    $this->app->erp->MenuEintrag('index.php?module=einstellungen&action=list','Zur&uuml;ck');
    $this->app->erp->MenuEintrag('index.php?module=lexwareoffice&action=edit','Lexware Office');

    $service = $this->getService();
    $message = '';

    if($this->app->Secure->GetPOST('save') !== '') {
      try {
        $apiKey = (string)$this->app->Secure->GetPOST('api_key');
        $service->saveApiKey($apiKey);
        $message = '<div class="success">API-Schl&uuml;ssel wurde gespeichert.</div>';
      } catch (LexwareOfficeException $exception) {
        $message = '<div class="error">'.htmlspecialchars($exception->getMessage()).'</div>';
      }
    }

    if($message !== '') {
      $this->app->Tpl->Set('MESSAGE',$message);
    }

    $apiKeyPlaceholder = $service->hasApiKey() ? '******** (gespeichert)' : '';
    $this->app->Tpl->Set('API_KEY_PLACEHOLDER', $apiKeyPlaceholder);
    $this->app->Tpl->Set('API_KEY_HINT', 'Der API-Schl&uuml;ssel wird verschl&uuml;sselt in der Systemkonfiguration abgelegt.');

    $this->app->Tpl->Parse('PAGE','lexwareoffice_settings.tpl');
  }

  private function getService(): LexwareOfficeService
  {
    if($this->service === null) {
      $this->service = new LexwareOfficeService(
        $this->app->Container->get('Database'),
        new LexwareOfficeConfigService($this->app->Container->get('SystemConfigModule')),
        new LexwareOfficeApiClient(),
        $this->app->Container->get('Logger'),
        $this->app->erp
      );
    }

    return $this->service;
  }

  private function ensureSuperSearchIndex(): void
  {
    if(!$this->app->Container->has('SuperSearchService') || !$this->app->Container->has('SuperSearchIndexer')) {
      return;
    }

    /** @var \Xentral\Modules\SuperSearch\SuperSearchService $service */
    $service = $this->app->Container->get('SuperSearchService');
    /** @var \Xentral\Modules\SuperSearch\SuperSearchIndexer $indexer */
    $indexer = $this->app->Container->get('SuperSearchIndexer');

    $indexName = 'lexwareoffice';
    if(!$service->existsIndex($indexName)) {
      $service->createIndex($indexName, 'Lexware Office', 'lexwareoffice');
    }

    $count = (int)$this->app->DB->Select(
      sprintf(
        "SELECT COUNT(id) FROM supersearch_index_item WHERE index_name = '%s' AND outdated = 0",
        $indexName
      )
    );
    if($count === 0) {
      $indexer->updateIndexFull($indexName);
    }
  }
}
