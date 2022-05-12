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

use Xentral\Components\Http\Request;
use Xentral\Components\Logger\Logger;
use Xentral\Modules\CopperSurcharge\CopperSurchargeCalculatorFactory;
use Xentral\Modules\CopperSurcharge\CopperSurchargeService;
use Xentral\Modules\CopperSurcharge\Data\CopperSurchargeData;
use Xentral\Modules\CopperSurcharge\Exception\InvalidDateFormatException;
use \Xentral\Modules\CopperSurcharge\Exception\EmptyResultException;
use Xentral\Modules\CopperSurcharge\Exception\ValidationFailedException;
use Xentral\Modules\CopperSurcharge\Service\CopperSurchargeCalculator;
use Xentral\Modules\SystemConfig\Exception\InvalidArgumentException;
use Xentral\Modules\SystemConfig\Exception\ValueTooLargeException;


class Coppersurcharge
{

  /** @var string MODULE_NAME */
  const MODULE_NAME = 'CopperSurcharge';

  /** @var array $javascript */
  public $javascript = [
    './classes/Modules/CopperSurcharge/www/js/copper_surcharge.js',
  ];

  /** @var Application $app */
  private $app;

  /** @var TemplateParser $template */
  private $template; //legacy template

  /** @var Request $request */
  private $request;

  /**
   * @param Application $app
   * @param bool $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    if($intern){
      return;
    }

    $this->template = $this->app->Tpl;

    $this->request = $this->app->Container->get('Request');
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler('list', 'CopperSurchargeList');

    $this->app->ActionHandlerListen($app);
    $this->app->erp->Headlines('Kupferzuschlag');

    $this->Install();
  }

  public function Install()
  {
    $this->app->erp->RegisterHook('ANABREGSNeuberechnen_1', 'coppersurcharge', 'HandleCopperSurcharge');
    $this->app->erp->RegisterHook('ANABREGSNeuberechnenEnde', 'coppersurcharge', 'UpdateHandledSurcharge');
  }

  public function CopperSurchargeList()
  {
    /** @var CopperSurchargeService $copperSurchargeService */
    $copperSurchargeService = $this->app->Container->get('CopperSurchargeService');

    $isSave = $this->request->getPost('coppersurcharge-save') !== null;

    if($isSave){

      try {
        $copperSurchargeService->setConfigurationData(
          new CopperSurchargeData(
            $this->getArticleIdByNumberNameString(
              $this->app->DB->real_escape_string($this->request->getPost('surcharge-article'))
            ),
            (int)$this->request->getPost('surcharge-position-type'),
            (int)$this->request->getPost('surcharge-document-conversion'),
            (int)$this->request->getPost('surcharge-invoice'),
            (float)str_replace(
              ',',
              '.',
              $this->app->DB->real_escape_string($this->request->getPost('surcharge-delivery-costs'))
            ),
            (string)$this->app->DB->real_escape_string($this->request->getPost('surcharge-copper-base')),
            (float)str_replace(
              ',',
              '.',
              $this->app->DB->real_escape_string($this->request->getPost('surcharge-copper-base-standard'))
            ),
            (int)$this->request->getPost('surcharge-maintenance-type'),
            (string)$this->request->getPost('surcharge-copper-number')
          )
        );
      } catch (InvalidArgumentException $e) {
        $this->template->Add('MESSAGE', '<div class="error">{|Einer der Parameter ist ungültig|}: ' . $e->getMessage() . '</div>');
      } catch (ValueTooLargeException $e) {
        $this->template->Add('MESSAGE', '<div class="error">{|Einer der Werte ist zu groß|}: ' . $e->getMessage() . '</div>');
      } catch (ValidationFailedException $e) {
        $this->template->Add('MESSAGE', '<div class="error">{|Bitte alle Pflichtfelder ausfüllen|}: ' . $e->getMessage() . '</div>');
      }
    }

    $this->getOverview();
  }

  protected function getOverview()
  {
    $this->createMenu();
    $this->app->YUI->AutoComplete('surcharge-article', 'artikelnummer');
    $this->setSavedConfigParameters();
    $this->template->Parse('PAGE', 'coppersurcharge_list.tpl');
  }

  /**
   * @param string $selected
   */
  protected function setCopperNumberOptions(string $selected = ''): void
  {
    $options = $this->getArticleFreeFields($selected);
    if(!empty($options)){
      $this->template->Set('COPPERNUMBEROPTIONS', $options);
    }
  }

  /**
   * @param string $selected
   */
  protected function setSurchargeCopperBase(string $selected = ''): void
  {
    $options = $this->getArticleFreeFields($selected);
    if(!empty($options)){
      $this->template->Set('FREEFIELDOPTIONS', $options);
    }
  }

  /**
   * @param string $selected
   * @return string
   */
  protected function getArticleFreeFields(string $selected = ''): string
  {
    /** @var CopperSurchargeService $service */
    $service = $this->app->Container->get('CopperSurchargeService');

    $options = '';
    $freeFieldColumns = [];
    for ($i = 1; $i <= 40; $i++) {
      $freeFieldColumns[] = 'freifeld' . $i;
    }

    foreach ($freeFieldColumns as $freeFieldColumn) {
      $fieldName = $service->findCompanyData($freeFieldColumn);
      if(!empty($fieldName)){
        $options .= '<option value="' . $freeFieldColumn . '" ' . ($freeFieldColumn == $selected ? 'selected' : '') . '>' . $fieldName . '</option>';
      }
    }

    return $options;
  }

  /**
   * @return void
   */
  protected function createMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=coppersurcharge&action=list', '&Uuml;bersicht');
  }

  /**
   * @param string $docType
   * @param int $docTypeId
   */
  public function HandleCopperSurcharge(string $docType, int $docTypeId)
  {
    /** @var Logger $logger */
    $logger = $this->app->Container->get('Logger');

    /** @var CopperSurchargeService $copperSurchargeService */
    $copperSurchargeService = $this->app->Container->get('CopperSurchargeService');
    $config = $copperSurchargeService->findConfigurationData();

    if(empty($config)){
      return;
    }

    $calculator = $this->getCopperSurchargeCalculator($config);

    if(array_search($docType, ['auftrag', 'rechnung', 'angebot']) === false){
      return;
    }

    $calculator->resetDocument($docType, $docTypeId);

    if($config->getSurchargeMaintenanceType() === CopperSurchargeData::SURCHARGE_MAINTENANCE_TYPE_APP){
      $copperPositions = $calculator->findPositionsForMaintenanceApp($docType, $docTypeId);
      $copperPositionsInPartsList = $calculator->findPositionsForMaintenanceAppInPartsList($docType, $docTypeId);
    }else{
      $copperPositions = $calculator->findPositionsForMaintenanceArticle($docType, $docTypeId);
      $copperPositionsInPartsList = $calculator->findPositionsForMaintenanceArticleInPartsList($docType, $docTypeId);
    }

    if(empty($copperPositions) && empty($copperPositionsInPartsList)){
      $calculator->deleteRemainingCopperSurchargeArticles($docType, $docTypeId);
      return;
    }

    try {
      $calculator->handleCopperSurchargePositions($docType, $docTypeId, $copperPositions, $copperPositionsInPartsList);
    } catch (InvalidDateFormatException | EmptyResultException $e) {
      $logger->error($e->getMessage());
    }
  }

  /**
   * @param int $docTypeId
   * @param string $docType
   */
  public function UpdateHandledSurcharge(int $docTypeId, string $docType)
  {
    /** @var CopperSurchargeService $copperSurchargeService */
    $copperSurchargeService = $this->app->Container->get('CopperSurchargeService');
    $config = $copperSurchargeService->findConfigurationData();

    if(empty($config)){
      return;
    }
    $calculator = $this->getCopperSurchargeCalculator($config);

    $calculator->updateCopperSurchargeArticles($docTypeId, $docType);
  }

  /**
   * @param CopperSurchargeData $configData
   * @return CopperSurchargeCalculator
   */
  protected function getCopperSurchargeCalculator(CopperSurchargeData $configData): CopperSurchargeCalculator
  {
    /** @var CopperSurchargeCalculatorFactory $copperSurchargeCalculatorFactory */
    $copperSurchargeCalculatorFactory = $this->app->Container->get('CopperSurchargeCalculatorFactory');

    return $copperSurchargeCalculatorFactory->createCopperSurchargeCalculator($configData);

  }

  /**
   * @param string $articleNameAndNumber
   * @return int
   */
  protected function getArticleIdByNumberNameString(string $articleNameAndNumber): int
  {
    if(empty($articleNameAndNumber)){
      return 0;
    }
    $articleNumber = explode(' ', $articleNameAndNumber)[0];
    return (int)$this->app->DB->Select(sprintf("SELECT a.id FROM `artikel` AS `a` WHERE a.nummer ='%s'", $articleNumber));
  }

  /**
   * @param int $articleId
   * @return string
   */
  protected function getArticleNumberAndNameString(int $articleId): string
  {
    return
      (string)$this->app->DB->Select(
        sprintf(
          "SELECT CONCAT(a.nummer,' ',a.name_de) FROM `artikel` AS `a` WHERE a.id =%d",
          $articleId
        )
      );
  }

  protected function setSavedConfigParameters(): void
  {
    /** @var CopperSurchargeService $copperSurchargeService */
    $copperSurchargeService = $this->app->Container->get('CopperSurchargeService');
    $configData = $copperSurchargeService->findConfigurationData();

    if(empty($configData)){
      //standard values
      $this->template->Set('DELIVERYCOSTS', 1);
      $this->template->Set('COPPERBASESTANDARD', 150);
      $this->template->SET('MAINTENANCE_APP_CHECK', 'checked');
      $this->setSurchargeCopperBase();
      $this->setCopperNumberOptions();
    }else{
      $this->template->Set('ARTICLEID', $this->getArticleNumberAndNameString($configData->getCopperSurchargeArticleId()));
      $this->template->Set('POSITIONTYPE' . $configData->getSurchargePositionType(), 'selected');
      $this->template->Set('DOCUMENTCONVERSION' . $configData->getSurchargeDocumentConversion(), 'selected');
      $this->template->Set('INVOICE' . $configData->getSurchargeInvoice(), 'selected');
      $this->template->Set('DELIVERYCOSTS', $configData->getSurchargeDeliveryCosts());
      $this->template->Set('COPPERBASESTANDARD', $configData->getSurchargeCopperBaseStandard());
      $this->template->Set('COPPERBASE' . $configData->getSurchargeCopperBase(), 'selected');
      $this->setSurchargeCopperBase($configData->getSurchargeCopperBase());
      $this->setCopperNumberOptions($configData->getCopperNumberOption());
      if($configData->getSurchargeMaintenanceType() === CopperSurchargeData::SURCHARGE_MAINTENANCE_TYPE_ARTICLE){
        $this->template->SET('MAINTENANCE_ARTICLE_CHECK', 'checked');
      }elseif($configData->getSurchargeMaintenanceType() === CopperSurchargeData::SURCHARGE_MAINTENANCE_TYPE_APP){
        $this->template->SET('MAINTENANCE_APP_CHECK', 'checked');
      }
    }
  }
}
