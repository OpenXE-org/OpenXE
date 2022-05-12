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

use Xentral\Modules\SystemConfig\SystemConfigModule;

class Dataprotection
{
  /** @var Application $app */
  protected $app;

  /** @var SystemConfigModule $systemConfigModule */
  protected $systemConfigModule;

  /**
   * Dataprotection constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    $this->systemConfigModule = $this->app->Container->get('SystemConfigModule');
    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler('list', 'DataProtectionList');
    $this->app->ActionHandler('services', 'DataServices');
    $this->app->DefaultActionHandler('list');
    $this->app->ActionHandlerListen($app);
  }

  public function DataProtectionList()
  {
    $this->app->erp->Headlines('Datenschutz');

    $this->app->erp->MenuEintrag('index.php?module=dataprotection&action=list', 'Datenschutzerklärung');
    $this->app->erp->MenuEintrag('index.php?module=dataprotection&action=services', 'Dienste');
    $this->app->Tpl->Parse('PAGE', 'dataprotection_list.tpl');
  }

  public function DataServices(){
      $this->app->erp->Headlines('Datenschutz');
      if($this->app->Secure->GetPOST('save')) {
          $this->systemConfigModule->setValue(
              'dataprotection',
              'googleanalytics',
              (string)(int)!empty($this->app->Secure->GetPOST('dataprotection_googleanalytics'))
          );
          $this->systemConfigModule->setValue(
              'dataprotection',
              'improvement',
              (string)(int)!empty($this->app->Secure->GetPOST('dataprotection_improvement'))
          );
          $this->systemConfigModule->setValue(
              'dataprotection',
              'hubspot',
              (string)(int)!empty($this->app->Secure->GetPOST('dataprotection_hubspot'))
          );
          $this->systemConfigModule->setValue(
              'dataprotection',
              'zendesk',
              (string)(int)!empty($this->app->Secure->GetPOST('dataprotection_zendesk'))
          );

          $this->app->Location->execute('index.php?module=dataprotection&action=services');
      }
      $isDemo = !empty(erpAPI::Ioncube_Property('testlizenz'))
        && !empty(erpAPI::Ioncube_Property('iscloud'));
      $google = $this->isGoogleAnalyticsActive();
      $improvement = $this->isImprovementProgramActive();
      $hubspot = $this->isHubspotActive();
      $zendesk = $this->isZenDeskActive();
      $this->systemConfigModule->setValue('dataprotection', 'googleanalytics', (string)(int)$google);
      $this->systemConfigModule->setValue('dataprotection', 'improvement', (string)(int)$improvement);
      $this->systemConfigModule->setValue('dataprotection', 'hubspot', (string)(int)$hubspot);
      $this->systemConfigModule->setValue('dataprotection', 'zendesk', (string)(int)$zendesk);
      if($google) {
          $this->app->Tpl->Set('DATAPROTECTION_GOOGLEANALYTICS', ' checked="checked" ');
      }
      if($improvement) {
          $this->app->Tpl->Set('DATAPROTECTION_IMPROVEMENT', ' checked="checked" ');
      }
      if($hubspot) {
          $this->app->Tpl->Set('DATAPROTECTION_HUBSPOT', ' checked="checked" ');
      }
      if($zendesk) {
          $this->app->Tpl->Set('DATAPROTECTION_ZENDESK', ' checked="checked" ');
      }
      if(!$isDemo) {
          $this->app->Tpl->Set('DISABLED_HUBSPOT', ' disabled="disabled" ');
      }

      $this->app->erp->MenuEintrag('index.php?module=dataprotection&action=list', 'Datenschutzerklärung');
      $this->app->erp->MenuEintrag('index.php?module=dataprotection&action=services', 'Dienste');
      $this->app->Tpl->Parse('PAGE', 'dataprotection_services.tpl');
  }

  /**
   * @return bool
   */
  public function isGoogleAnalyticsActive(): bool
  {
    $google = $this->systemConfigModule->tryGetValue('dataprotection', 'googleanalytics');
    if($google === null) {
      $this->systemConfigModule->setValue('dataprotection', 'googleanalytics', '1');

      return true;
    }

    return $google === '1';
  }

  /**
   * @return bool
   */
  public function isZenDeskActive(): bool
  {
    $zendesk = $this->systemConfigModule->tryGetValue('dataprotection', 'zendesk');
    if($zendesk === null) {
      $this->systemConfigModule->setValue('dataprotection', 'zendesk', '1');

      return true;
    }

    return $zendesk === '1';
  }

  /**
   * @return bool
   */
  public function isImprovementProgramActive(): bool
  {
    $improvement = $this->systemConfigModule->tryGetValue('dataprotection', 'improvement');
    if($improvement === null) {
      $this->systemConfigModule->setValue('dataprotection', 'improvement', '1');

      return true;
    }

    return $improvement === '1';
  }

  /**
   * @return bool
   */
  public function isHubspotActive(): bool
  {
    $hubspot = $this->systemConfigModule->tryGetValue('dataprotection', 'hubspot');

    $isDemo = !empty(erpAPI::Ioncube_Property('testlizenz'))
      && !empty(erpAPI::Ioncube_Property('iscloud'));
    if($hubspot === null) {
      return $isDemo;
    }

    return $hubspot === '1';
  }
}
