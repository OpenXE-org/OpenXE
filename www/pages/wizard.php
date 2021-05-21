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

use Xentral\Components\Http\JsonResponse;
use Xentral\Modules\Wizard\Exception\WizardExceptionInterface;
use Xentral\Modules\Wizard\WizardService;

class Wizard
{
  /** @var erpooSystem $app */
  public $app;

  /** @var WizardService $service */
  protected $service;

  /**
   * @param erpooSystem $app
   * @param bool $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    if ($intern) {
      return;
    }

    $this->service = $this->app->Container->get('WizardService');

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler('list', 'WizardList');
    $this->app->ActionHandler('ajax', 'WizardAjax');
    $this->app->ActionHandler('create', 'WizardCreate');
    $this->app->ActionHandlerListen($app);
  }

  /**
   * @return void
   */
  public function Install()
  {
    $this->app->erp->CheckTable('wizard');
    $this->app->erp->CheckColumn('id', 'int(11)', 'wizard', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('user_id', 'INT(11)', 'wizard', "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn('key', 'VARCHAR(32)', 'wizard', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('title', 'VARCHAR(64)', 'wizard', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('skip_link_text', 'VARCHAR(64)', 'wizard', 'DEFAULT NULL');
    $this->app->erp->CheckColumn('params', 'VARCHAR(512)', 'wizard', 'DEFAULT NULL');
    $this->app->erp->CheckColumn('options', 'VARCHAR(512)', 'wizard', 'NULL DEFAULT NULL');
    $this->app->erp->CheckColumn('active', 'TINYINT(1)', 'wizard', "DEFAULT '1' NOT NULL");
    $this->app->erp->CheckColumn('created_at', 'DATETIME', 'wizard', 'DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckIndex('wizard', ['user_id', 'key'], true);

    $this->app->erp->CheckTable('wizard_step');
    $this->app->erp->CheckColumn('id', 'int(11)', 'wizard_step', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('wizard_id', 'INT(11)', 'wizard_step', "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn('key', 'VARCHAR(32)', 'wizard_step', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('link', 'VARCHAR(255)', 'wizard_step', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('title', 'VARCHAR(64)', 'wizard_step', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('caption', 'VARCHAR(255)', 'wizard_step', 'DEFAULT NULL');
    $this->app->erp->CheckColumn('description', 'TEXT', 'wizard_step', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('options', 'VARCHAR(512)', 'wizard_step', 'DEFAULT NULL');
    $this->app->erp->CheckColumn('position', 'TINYINT(3)', 'wizard_step', "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn('checked', 'TINYINT(1)', 'wizard_step', "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn('created_at', 'DATETIME', 'wizard_step', 'DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckIndex('wizard_step', ['wizard_id', 'key'], true);

    $this->app->erp->CheckAlterTable('ALTER TABLE `wizard_step` CHANGE `description` `description` TEXT NOT NULL; ');

    $this->app->erp->RegisterHook('before_final_parse_page', 'wizard', 'checkForActiveWizard');
  }

    public function checkForActiveWizard()
    {
        $this->service = $this->app->Container->get('WizardService');
        $userId = $this->app->User->GetID();
        if($userId === null) {
          return;
        }

    if ($activeWizardKey = $this->service->getActiveWizardKey($userId)) {
      $this->app->Tpl->Set('ACTIVE_WIZARD_KEY', $activeWizardKey);
      $this->app->Tpl->Parse('BODYENDE', 'active_wizard.tpl');
    }
  }

  /**
   * @return void
   */
  protected function WizardMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=wizard&action=create', 'Neuen Wizard anlegen');
    $this->app->erp->MenuEintrag('index.php?module=wizard&action=list', '&Uuml;bersicht');

    $this->app->Tpl->Set('UEBERSCHRIFT', 'Wizard');
    $this->app->erp->Headlines('Wizard');
    $this->app->Tpl->Set('TABTEXT', 'Wizard');
  }

  /**
   * @return void
   */
  public function WizardList()
  {
    $this->WizardMenu();

    $cmd = $this->app->Secure->GetGET('cmd');
    if ($cmd === 'loadexample') {
      $data = $this->GetExampleWizardData();
      $data['settings']['active'] = true;
      try {
        $this->service->replaceWizard($data, $this->app->User->GetID());
      } catch (Exception $e) {

      }
    }

    $this->app->Tpl->Parse('PAGE', 'wizard_list.tpl');
  }

  /**
   * @return void
   */
  public function WizardCreate()
  {
    $this->WizardMenu();

    $jsonOutput = '';
    $selectedUserId = 0;

    $cmd = $this->app->Secure->GetGET('cmd');
    switch ($cmd) {

      // JSON einlesen
      case 'jsoninput':
        $createWizardJsonRaw = $this->app->Secure->GetPOST('createwizard_json');
        $selectedUserId = $this->app->Secure->GetPOST('createwizard_user');
        if (!empty($createWizardJsonRaw)) {
          $createWizardJson = str_replace('\r\n', '', $createWizardJsonRaw);
          $createWizardJson = stripslashes($createWizardJson);
          $data = json_decode($createWizardJson, true);
          if (json_last_error() > 0) {
            $message = sprintf('JSON konnte nicht gelesen werden. Code "%s" - Meldung "%s"', json_last_error(), json_last_error_msg());
            $this->app->Tpl->Set('MESSAGE', '<div class="error">' . htmlspecialchars($message, ENT_QUOTES) . '</div>');
            $jsonOutput = stripslashes(str_replace('\r\n', "\r\n", $createWizardJsonRaw));
            break;
          }

          try {
            // JSON konnte fehlerfrei dekodiert werden
            $overwriteUserId = (int)$selectedUserId > 0 ? $selectedUserId : null;
            $wizardId = $this->service->replaceWizard($data, $overwriteUserId);
            $this->app->Tpl->Set('MESSAGE', '<div class="success">Wizard wurde erfolgreich gespeichert.</div>');

            // JSON aus den gespeicherten Daten neu generieren
            $data = $this->service->generateTemplateFromExistingWizard($wizardId);
            $jsonOutput = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

          } catch (WizardExceptionInterface $e) {
            $message = htmlspecialchars($e->getMessage(), ENT_QUOTES);
            $this->app->Tpl->Set('MESSAGE', '<div class="error">Konnte Wizard nicht erstellen. Fehler: ' . $message . '</div>');

            $jsonOutput = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          }
        }
        break;

      // JSON von einem vorhandenen Wizard laden
      case 'loadwizard':
        if (!empty($this->app->Secure->GetPOST('loadwizard_button'))) {
          $loadWizardId = (int)$this->app->Secure->GetPOST('loadwizard_selected');
          $data = $this->service->generateTemplateFromExistingWizard($loadWizardId);
          $selectedUserId = $data['settings']['user_id'];
          $jsonOutput = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        break;

      case 'loadexample':
        $data = $this->GetExampleWizardData();
        $jsonOutput = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        break;

      case 'deletewizard':
        $deleteWizardId = (int)$this->app->Secure->GetPOST('deletewizard_selected');
        $this->service->deleteWizardById($deleteWizardId);
        break;
    }

    $this->app->Tpl->Set('CREATEWIZARDJSON', $jsonOutput);
    $this->app->Tpl->Set('CREATEWIZARDUSEROPTIONS', $this->GenerateUserSelectOptions($selectedUserId));
    $this->app->Tpl->Set('LOADWIZARDOPTIONS', $this->GenerateWizardSelectOptions());
    $this->app->Tpl->Parse('PAGE', 'wizard_create.tpl');
  }

  /**
   * @return JsonResponse|\Xentral\Components\Http\RedirectResponse
   */
  public function WizardAjax()
  {
    $userId = $this->app->User->GetID();
    $cmd = $this->app->Secure->GetGET('cmd');

    switch ($cmd) {
      case 'save_visit':
        $wizardKey = $this->app->Secure->GetPOST('wizard');
        $stepKey = $this->app->Secure->GetPOST('step');
        $success = $this->service->setStepVisited($wizardKey, $stepKey, $userId);
        $data = ['success' => $success];
        break;

      case 'deactivate_wizard':
        try {
          $wizardKey = $this->app->Secure->GetPOST('wizard');
          $this->service->deactivateWizard($wizardKey, $userId);
          $data = ['success' => true];
        } catch (WizardExceptionInterface $e) {
          $data = ['success' => false, 'error' => $e->getMessage()];
        }
        break;
      case 'cancel_active_wizard':
        try {
          $this->service->cancelActiveWizardForUser($userId);
          $data = ['success' => true];
        } catch (WizardExceptionInterface $e) {
          $data = ['success' => false, 'error' => $e->getMessage()];
        }
        break;
      case 'finish_wizard':
        try {
          $wizardKey = $this->app->Secure->GetGET('key');
          $this->service->finishWizardForUser($wizardKey, $userId);
          $data = ['success' => true];
        } catch (WizardExceptionInterface $e) {
          $data = ['success' => false, 'error' => $e->getMessage()];
        }
        break;
      case 'reset_wizard':
        try {
          $wizardKey = $this->app->Secure->GetGET('key');
          $this->service->resetWizardForUser($wizardKey, $userId);
          $data = ['success' => true];
        } catch (WizardExceptionInterface $e) {
          $data = ['success' => false, 'error' => $e->getMessage()];
        }
        break;
      case 'get_wizards':
        $data = [];
        $wizardKeys = $this->service->getActiveWizardKeys($userId);
        foreach ($wizardKeys as $wizardKey) {
          $wizardData = $this->service->getWizard($wizardKey, $userId);
          $data[] = $wizardData;
        }
        break;
      case 'get_by_key':
        $key = $this->app->Secure->GetGET('key');
        $userId = $this->app->User->getId();
        $data = $this->service->getWizard($key, $userId);
        break;
      case 'set_active_wizard':
        $key = $this->app->Secure->GetGET('key');
        $userId = $this->app->User->getId();
        if($this->service->isWizardCompletedForUser($key, $userId)){
          $this->service->resetWizardForUser($key,$userId);
        }
        $this->service->setActiveWizardKey($key, $userId);
        $this->service->setMinimizedForUser($userId, false);
        $link = $this->service->getFirstWizardLink($key);

        return \Xentral\Components\Http\RedirectResponse::createFromUrl($link);
        break;
      case 'complete_step':
        $key = $this->app->Secure->GetGET('key');
        $completedStep = $this->app->Secure->GetPOST('step');
        $lastVisitedLink = $this->app->Secure->GetPOST('link');
        $userId = $this->app->User->getId();
        $this->service->setStepVisited($key, $completedStep, $userId);
        $this->service->saveLastVisitedLink($key, $lastVisitedLink, $userId);
        $data = ['success' => true];
        break;
      case 'set_minimized':
        $state = $this->app->Secure->GetGET('value');
        $isMinimized = $state === 'true';
        $userId = $this->app->User->getId();
        $isMinimized = $this->service->setMinimizedForUser($userId, $isMinimized);
        $data = ['success' => true, 'is_minimized' => $isMinimized];
        break;
      default:
        $data = ['success' => false, 'error' => 'Incomplete request'];
        break;
    }

    return new JsonResponse(
      $data, $data['success'] === false ? JsonResponse::HTTP_NOT_FOUND : JsonResponse::HTTP_OK
    );
  }

  /**
   * Test-Callback-Methode für die 'check_callback'-Option
   *
   * @return bool
   */
  public function CheckArticlesProvidedCallback()
  {
    $articleCount = $this->app->DB->Select(
      'SELECT COUNT(a.id) FROM `artikel` AS `a` WHERE a.geloescht = 0'
    );

    return (int)$articleCount > 10;
  }

  /**
   * @return array
   */
  protected function GetExampleWizardData()
  {
    $settings = [
      'user_id' => $this->app->User->GetID(),
      'active' => true,
      'key' => 'firstrun',
      'title' => 'Einrichtungsassistent',
      'skip_link_text' => 'Einrichtung überspringen',
      'params' => [
        'shop_id' => 1,
      ]
    ];

    $steps = [
      [
        'key' => 'grundeinstellungen',
        'link' => './index.php?module=firmendaten&action=edit#tabs-1',
        'title' => 'Grundeinstellungen',
        'caption' => 'Meine Firmen-Informationen pflegen ',
        'description' =>
          'Bitte tragen Sie hier Ihren Firmennamen und bla bla ein. ',
        'position' => 1,
      ],
      [
        'key' => 'briefkopf',
        'link' => './index.php?module=firmendaten&action=edit#tabs-2',
        'title' => 'Briefkopf einrichten',
        'caption' => 'Aussehen der Geschäftsbriefe anpassen',
        'position' => 2,
        'options' => [
          'highlight' => [
            'breite_position', 'breite_nummer'
          ]
        ],
      ],
      [
        'key' => 'artikel',
        'link' => './index.php?module=artikel&action=list',
        'title' => 'Artikel pflegen',
        'caption' => 'Beispiel mit Modul-Callback',
        'position' => 3,
        'description' =>
          'Beispiel mit Modul-Callback. Anforderungen:<br>Callback-Methode muss <code>public</code> sein und Rückgabe ' .
          'muss zu <code>bool</code> wandelbar sein.',
        'options' => [
          'check_callback' => [
            'module_name' => 'Wizard',
            'module_action' => 'CheckArticlesProvidedCallback',
            'args' => [
              'Wert für erstes Callback-Argument',
              'Zweites Argument mit ##shop_id## Parameter'
            ],
          ],
        ]
      ],
      [
        'key' => 'adressen',
        'link' => './index.php?module=adresse&action=list',
        'title' => 'Adressen pflegen',
        'caption' => 'Beispiel mit Objekt-Protokoll-Prüfung',
        'position' => 4,
        'options' => [
          'check_protocol' => [
            'object_name' => 'shop',
            'action_name' => 'shop_created',
            'object_id' => '##shop_id##',
          ],
        ]
      ],
    ];

    return [
      'settings' => $settings,
      'steps' => $steps
    ];
  }

  /**
   * @param int $selectedUserId
   *
   * @return string
   */
  protected function GenerateUserSelectOptions($selectedUserId)
  {
    $data = $this->app->DB->SelectArr(
      'SELECT u.id, a.name 
          FROM `user` AS `u` 
          INNER JOIN `adresse` AS `a` ON u.adresse = a.id
          ORDER BY a.name ASC'
    );

    $html = '';
    $selectedUserId = (int)$selectedUserId;
    foreach ($data as $row) {
      $selectedAttr = $selectedUserId === (int)$row['id'] ? ' selected="selected"' : '';
      $html .= sprintf(
        '<option value="%s"%s>%s</option>',
        $row['id'], $selectedAttr, $row['name']
      );
    }

    return $html;
  }

  /**
   * @return string
   */
  protected function GenerateWizardSelectOptions()
  {
    $data = $this->app->DB->SelectArr(
      'SELECT w.id, w.key, w.title, w.active, w.user_id, a.name AS username
          FROM `wizard` AS `w` 
          LEFT JOIN `user` AS `u` ON w.user_id = u.id 
          INNER JOIN `adresse` AS `a` ON u.adresse = a.id
          WHERE 1
          ORDER BY w.user_id ASC, w.created_at ASC'
    );

    $html = '';
    foreach ($data as $row) {
      $html .= sprintf(
        '<option value="%s">%s [%s] - %s (%s)</option>',
        $row['id'], $row['username'], $row['user_id'], $row['title'], (int)$row['active'] === 1 ? 'aktiv' : 'inaktiv'
      );
    }

    return $html;
  }
}
