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
use Xentral\Components\EnvironmentConfig\EnvironmentConfig;
use Xentral\Modules\LearningDashboard\Data\Lesson;
use Xentral\Modules\LearningDashboard\Data\Tab;
use Xentral\Modules\LearningDashboard\Data\Task;
use Xentral\Modules\Wizard\WizardService;

class Learningdashboard
{
  const MODULE_NAME = 'LearningDashboard';

  /** @var Application $app */
  protected $app;

  /** @var array $javascript */
  public $javascript = [
    './classes/Modules/LearningDashboard/www/js/components/learning_dashboard_main_component.js',
    './classes/Modules/LearningDashboard/www/js/components/learning_dashboard_header_component.js',
    './classes/Modules/LearningDashboard/www/js/components/learning_dashboard_tabs_component.js',
    './classes/Modules/LearningDashboard/www/js/components/learning_dashboard_lesson_component.js',
    './classes/Modules/LearningDashboard/www/js/components/learning_dashboard_task_component.js',
    './classes/Modules/LearningDashboard/www/js/components/progress_bar_component.js',
    './classes/Modules/LearningDashboard/www/js/learningdashboard.js',
  ];

  /** @var array $stylesheet */
  public $stylesheet = [
    './www/themes/new/css/xentral_grid.css',
    './classes/Modules/LearningDashboard/www/css/progress_bar.css',
    './classes/Modules/LearningDashboard/www/css/learningdashboard.css',
  ];

  /**
   * Learningdashboard constructor.
   *
   * @param Application $app
   * @param bool $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    if ($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("list", "LearningdashboardList");
    $this->app->ActionHandler("ajax", "LearningdashboardAjax");

    $this->app->ActionHandlerListen($app);
  }

  public function LearningdashboardMenu(): void
  {
    $this->app->erp->MenuEintrag('index.php?module=appstore&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
  }

  public function LearningdashboardList(): void
  {
    $this->LearningdashboardMenu();
    $this->app->erp->Headlines('Learning Dashboard');

    $this->app->Tpl->Parse('PAGE', 'learningdashboard_list.tpl');
  }

  /**
   * @return JsonResponse
   */
  public function LearningdashboardAjax(): JsonResponse
  {
    $userId = $this->app->User->GetID();
    $cmd = $this->app->Secure->GetGET('cmd');

    switch ($cmd) {
      case 'get_content':
        $data = $this->getLearningDashboardContent();
        break;
      default:
        $data = ['success' => false, 'error' => 'Incomplete request'];
        break;
    }

    return new JsonResponse(
      $data,
      $data['success'] === false ? JsonResponse::HTTP_BAD_REQUEST : JsonResponse::HTTP_OK
    );
  }

  /**
   * @return array
   */
  private function getLearningDashboardContent(): array
  {
    $userId = $this->app->User->GetID();

    /** @var WizardService $wizardService */
    $wizardService = $this->app->Container->get('WizardService');

    $recommendationLesson = new Lesson('Für dich empfohlen');
    $recommendationLesson->addTask(new Task($wizardService->getWizard('shopify', $userId)));
    $recommendationLesson->addTask(new Task($wizardService->getWizard('basic_settings', $userId)));
    $recommendationLesson->addTask(new Task($wizardService->getWizard('production', $userId)));
    //$recommendationLesson->addTask(new Task($wizardService->getWizard('fulfiller', $userId))); //steps klaeren

    $initialSetupLesson = new Lesson('Grundeinrichtung');
    //demodaten laden
    $initialSetupLesson->addTask(new Task($wizardService->getWizard('basic_settings', $userId)));
    $initialSetupLesson->addTask(new Task($wizardService->getWizard('parts_list', $userId)));

    /** @var EnvironmentConfig $environmentConfig */
    $environmentConfig = $this->app->Container->get('EnvironmentConfig');
    if($environmentConfig->isSystemFlaggedAsDevelopmentVersion() || $environmentConfig->isSystemFlaggedAsTestVersion()){
      $initialSetupLesson->addTask(new Task($wizardService->getWizard('restore_factory_settings', $userId)));
    }

    $shopConnectionLesson = new Lesson('Shopanbindung');
    $shopConnectionLesson->addTask(new Task($wizardService->getWizard('shopify', $userId)));
    $shopConnectionLesson->addTask(new Task($wizardService->getWizard('amazon', $userId)));
    $shopConnectionLesson->addTask(new Task($wizardService->getWizard('ebay', $userId)));
    $shopConnectionLesson->addTask(new Task($wizardService->getWizard('shopware5', $userId)));
    $shopConnectionLesson->addTask(new Task($wizardService->getWizard('shopware6', $userId)));
    $shopConnectionLesson->addTask(new Task($wizardService->getWizard('additional_shops', $userId)));

    $xentralTourLesson = new Lesson('Tour durch xentral');
    //$xentralTourLesson->addTask(new Task($wizardService->getWizard('fulfiller', $userId))); //steps klaeren
    $xentralTourLesson->addTask(new Task($wizardService->getWizard('batches_bestbefore_serials', $userId)));
    $xentralTourLesson->addTask(new Task($wizardService->getWizard('crm', $userId)));
    $xentralTourLesson->addTask(new Task($wizardService->getWizard('accounting', $userId)));
    $xentralTourLesson->addTask(new Task($wizardService->getWizard('storage', $userId)));
    $xentralTourLesson->addTask(new Task($wizardService->getWizard('return_and_credit', $userId)));

    $tab = new Tab('Tab_one');
    $tab->addLesson($recommendationLesson);
    $tab->addLesson($initialSetupLesson);
    $tab->addLesson($shopConnectionLesson);
    $tab->addLesson($xentralTourLesson);

    return [
      "wording" => [
        "task" => [
          "cta" => [
            "completed" => "Nochmal",
            "incomplete" => "Start"
          ],
            "details" => [
                "info" => "Um diesen Wizard starten zu können benötigst du:",
                "missing" => [
                    "modules" => [
                        "headline" => "Module"
                    ],
                    "permissions" => [
                        "headline" => "Berechtigungen"
                    ]
                ]
            ]
        ],
        "header" => [
          "headline" => "Learning Dashboard",
          "subline" => "xentral Schritt-für-Schritt einrichten",
          "content" => "In unserem Learning Dashboard zeigen wir euch mit unserem Klick-by-Klick Assistenten und vielen Videos wie ihr euch einrichten und direkt mit xentral durchstarten könnt. Welche Funktionen sind für euch interessant? Klick dich einfach durch unsere Kacheln und erfahre was xentral ERP für euer Business tun kann.",
          "progress" => "Fertig"
        ],
        "tabs" => [
          "hideCompleted" => "Abgeschlossene verbergen"
        ]
      ],
      "tabs" => [
        $tab
      ]
    ];
  }

  public function Install()
  {
    $this->app->erp->RegisterHook('welcome_start', 'learningdashboard', 'LearningDashboardStartsite');
  }

  public function LearningDashboardStartsite(Welcome $welcomeObject): void
  {
    if($this->app->User->GetType() !== 'admin') {
      return;
    }
    $this->app->Tpl->Set('LEARNINGDASHBOARDTILE', '');//@todo Add Tile for Sartsite
  }
}
