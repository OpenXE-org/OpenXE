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
use Xentral\Modules\SystemHealth\Gateway\SystemHealthGateway;
use Xentral\Modules\SystemHealth\Service\SystemHealthService;

class Systemhealth {
  /** @var Application $app */
  var $app;
  /** @var SystemHealthService $service */
  protected $service;
  /** @var SystemHealthGateway $service */
  protected $gateway;

  const MODULE_NAME = 'SystemHealth';
  /**
   * Systemlog constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app = $app;
    try {
      if(!empty($this->app->Container)) {
        $this->gateway = $this->app->Container->get('SystemHealthGateway');
        $this->service = $this->app->Container->get('SystemHealthService');
      }
    }
    catch(Exception $e) {

    }
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler('list','SystemhealthList');

    $this->app->DefaultActionHandler('list');

    $this->app->ActionHandlerListen($app);
  }

  public function SystemhealthMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=systemhealth&action=list','&Uuml;bersicht');
  }

  /**
   * @return array
   */
  public function getMemory()
  {
    try {
      return $this->service->getMemoryUsage();
    }
    catch (Exception $e) {
      return [];
    }
  }

  /**
   * @param string $catgoryId
   * @param int    $nr
   * @param string $target
   *
   * @return bool
   */
  public function drawCategory($catgoryId, $nr, $target = 'TAB1') {
    if(empty($this->gateway)) {
      return false;
    }
    $category = $this->gateway->getCategoryById($catgoryId);
    if(empty($category)) {
      return false;
    }
    if($nr % 2 === 0) {
      $this->app->Tpl->Add($target, '<div class="row">
            <div class="row-height">');
    }
    $this->app->Tpl->Set('CATEGORYTITLE', !empty($category['description'])?$category['description']:$category['name']);
    $systemHealthEntries = $this->gateway->getEntriesByCategoryId($catgoryId);
    $notifications = $this->gateway->getItemNoticiationsByUserId($this->app->User->GetID(), $catgoryId);
    $this->app->Tpl->Set('ENTRIES','');
    $icons = [
      '' => '<img class="statusicon" src="./themes/new/images/health-ok@2x.png" alt="unbekannt" />',
      'ok' => '<img class="statusicon" src="./themes/new/images/health-ok@2x.png" alt="OK" />',
      'warning' => '<img class="statusicon" src="./themes/new/images/health-message@2x.png" alt="Warnung" />',
      'error' => '<img class="statusicon" src="./themes/new/images/health-error@2x.png" alt="Fehler" />',
    ];
    foreach($systemHealthEntries as $systemHealthEntry) {
      $this->app->Tpl->Add(
        'ENTRIES',
        '<tr>
          <td>'.$systemHealthEntry['description'].':</td>
          <td>'.$icons[strtolower($systemHealthEntry['status'])].'</td>
          <td>'.$systemHealthEntry['message'].'</td>
          <td><span data-id="'.$systemHealthEntry['id'].'" class="systemhealthnotification '
        .(!empty($notifications[$systemHealthEntry['id']])?'active':'inactive').'">!</span>'.
        (empty($systemHealthEntry['resetable'])?'':
          '<img src="./themes/new/images/back.svg" class="reset" 
          alt="Meldung zur&uuml;cksetzen" title="Meldung zur&uuml;cksetzen" 
          data-id="'.$systemHealthEntry['id'].'" /> '
        ).'</td>
          <td></td>
        </tr>'
      );
    }

    $this->app->Tpl->Parse($target, 'systemhealth_fieldset.tpl');
    if($nr % 2 === 1) {
      $this->app->Tpl->Add($target, '</div></div>');
    }

    return true;
  }

  public function SystemhealthList()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'reset') {
      $id = $this->app->Secure->GetPOST('id');

      try {
        $this->service->resetStatus($id);
        $status = 1;
      }
      catch(Exception $e) {
        $status = 0;
      }
      header('Content-Type: application/json');
      echo json_encode(['status' => $status]);
      $this->app->ExitXentral();
    }

    if($this->app->Secure->GetPOST('reset')) {
      $this->app->DB->Update(
        sprintf(
          "UPDATE systemhealth SET status = '', message = '', last_reset = NOW() WHERE status <> '' OR message <> ''"
        )
      );
      $this->fillEntries();
      $this->app->Location->execute('index.php?module=systemhealth&action=list');
    }

    if($cmd === 'changenotification') {
      $value = $this->app->Secure->GetPOST('value');
      $systemhealthId = $this->app->Secure->GetPOST('id');
      try{
        $status = 1;
        if(empty($value)){
          $this->service->deleteSystemHealthItemNotificationSetting($systemhealthId, $this->app->User->GetID());
        }
        else {
          $this->service->createSystemHealthItemNotificationSetting($systemhealthId, $this->app->User->GetID());
        }
      }
      catch(Exception $e) {
        $status = 0;
      }
      header('Content-Type: application/json');
      echo json_encode(
        [
          'status' => $status,'value' => $value,'id' => $systemhealthId
        ]
      );
      $this->app->ExitXentral();
    }

    $this->SystemhealthMenu();

    $this->app->erp->Headlines('System-Meldungen');

    $this->loadFreeDisc();
    $data = $this->getHealthData();
    if(empty($data)) {
      $this->Install();
    }
    $categories = $this->gateway->getCategories();
    $nr = 0;
    foreach($categories as $category) {
      if($this->drawCategory($category['id'], $nr)) {
        $nr++;
      }
    }
    if($nr % 2 === 0) {
      $this->app->Tpl->Add('TAB1','</div></div>');
    }
    else{
      $this->app->Tpl->Set('CATEGORYTITLE','');
      $this->app->Tpl->Set('ENTRIES','');
      $this->app->Tpl->Parse('TAB1','systemhealth_fieldset.tpl');
    }
    $this->app->erp->checkActiveCronjob('systemhealth');
    $this->app->Tpl->Parse('PAGE', 'systemhealth_list.tpl');
  }

  public function Install()
  {
    $this->app->erp->CheckTable('systemhealth');
    $this->app->erp->CheckColumn('systemhealth_category_id','INT(11)','systemhealth','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('name','VARCHAR(64)','systemhealth',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('description','VARCHAR(64)','systemhealth',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('status','VARCHAR(64)','systemhealth',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('message','VARCHAR(255)','systemhealth',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('created_at','TIMESTAMP','systemhealth','DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckColumn('lastupdate','TIMESTAMP','systemhealth', 'NULL DEFAULT NULL');
    $this->app->erp->CheckColumn('resetable','TINYINT(1)','systemhealth', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('last_reset','TIMESTAMP','systemhealth', 'NULL DEFAULT NULL');
    $this->app->erp->CheckIndex('systemhealth', 'systemhealth_category_id');
    $this->app->erp->CheckIndex('systemhealth', 'name');

    $this->app->erp->CheckTable('systemhealth_category');
    $this->app->erp->CheckColumn('name','VARCHAR(64)','systemhealth_category',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('description','TEXT','systemhealth_category');
    $this->app->erp->CheckColumn('created_at','TIMESTAMP','systemhealth_category');
    $this->app->erp->CheckIndex('systemhealth_category', 'name');

    $this->app->erp->CheckTable('systemhealth_custom_error_lvl');
    $this->app->erp->CheckColumn('systemhealth_id','INT(11)','systemhealth_custom_error_lvl','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('status','VARCHAR(64)','systemhealth_custom_error_lvl',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckIndex('systemhealth_custom_error_lvl', 'systemhealth_id');

    $this->app->erp->CheckTable('systemhealth_notification');
    $this->app->erp->CheckColumn('status','VARCHAR(64)','systemhealth_notification',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('email','VARCHAR(255)','systemhealth_notification',"DEFAULT '' NOT NULL");

    $this->app->erp->CheckTable('systemhealth_notification_item');
    $this->app->erp->CheckColumn('systemhealth_id','INT(11)','systemhealth_notification_item','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('user_id','INT(11)','systemhealth_notification_item','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('status','VARCHAR(64)','systemhealth_notification_item',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('email','VARCHAR(255)','systemhealth_notification_item',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckIndex('systemhealth_notification_item', 'systemhealth_id');

    $this->app->erp->CheckTable('systemhealth_event');
    $this->app->erp->CheckColumn('systemhealth_id','INT(11)','systemhealth_event','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('created_at','TIMESTAMP','systemhealth_event','DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckColumn('doctype','VARCHAR(64)','systemhealth_event',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('doctype_id','INT(11)','systemhealth_event','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('status','VARCHAR(64)','systemhealth_event',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('message','VARCHAR(255)','systemhealth_event',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckIndex('systemhealth_event', 'systemhealth_id');
    $this->app->erp->CheckIndex('systemhealth_event', 'created_at');

    $this->app->erp->CheckProzessstarter('SystemHealth', 'periodisch', '1440', '', 'cronjob', 'systemhealth', 1);
    $this->app->erp->RegisterHook('eproosystem_iconboxes_start','systemhealth','SystemHealthIconBoxedStart');

    $this->checkEntries();
    $this->fillEntries();

    $userDataEntry = $this->app->DB->Select(
      "SELECT COUNT(sh.id) AS `total` FROM `systemhealth` AS `sh`
        WHERE sh.name = 'userdata_writeable'
          AND MD5('Schreibrechte in Userdata') != MD5(sh.description) LIMIT 1"
    );
    if($userDataEntry > 0){
      $this->app->DB->Update(
          "UPDATE `systemhealth` SET `description` = 'Schreibrechte in Userdata' WHERE `name` = 'userdata_writeable'");
    }
  }

  /**
   * @param string $categoryName
   * @param string $systemHealthName
   * @param string $status
   * @param string $doctype
   * @param int    $doctypeId
   * @param string $message
   */
  public function createEvent($categoryName, $systemHealthName, $status, $doctype, $doctypeId, $message = '')
  {
    try {
      $category = $this->gateway->getCategoryByName($categoryName);
      if(empty($category)) {
        return;
      }
      $systemHealth = $this->gateway->getByName($category['id'], $systemHealthName);
      if(empty($systemHealth)) {
        return;
      }
      $this->service->createEvent($systemHealth['id'], $status, $doctype, $doctypeId, $message);
    }
    catch(Exception $e) {

    }
  }

  /**
   * @param int $boxCount
   */
  public function SystemHealthIconBoxedStart(&$boxCount)
  {
    if(empty($this->gateway) || !$this->app->erp->RechteVorhanden('systemhealth', 'list')) {
      return;
    }

    $boxCount++;
    $counter = '';
    $status = $this->gateway->getStatusCount();
    $errorClass = '';
    $isWarning = !empty($status['warning']);
    if(!empty($status['error'])) {
      $errorClass = ' countererror';
      $counter = $status['error'];
      if($isWarning) {
        $counter .= '/'.$status['warning'];
      }
    }
    elseif($isWarning) {
      $counter = $status['warning'];
      $errorClass = ' counterwarning';
    }

    $this->app->Tpl->Add(
      'ICONBOXESHOOK1',
      '<li>
        <a title="{|System|}" href="index.php?module=systemhealth&action=list">					
          <span class="icon-box">
            <span class="counter'.$errorClass.'">'.$counter.'</span>
            <span class="icon">
              <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.33203 9.66641H4.50536C4.60656 9.66626 4.70222 9.62015 4.76536 9.54107L5.64136 8.44641C5.71538 8.35365 5.83312 8.30724 5.95051 8.32454C6.06791 8.34184 6.16726 8.42025 6.21136 8.53041L7.0967 10.7444C7.14266 10.8593 7.24846 10.9393 7.37152 10.9521C7.49458 10.965 7.61464 10.9086 7.68337 10.8057L9.65736 7.84507C9.72514 7.74313 9.84335 7.68654 9.96526 7.69767C10.0872 7.70881 10.1932 7.78587 10.2414 7.89841L11.7347 11.3844C11.7845 11.5007 11.8958 11.5788 12.0222 11.5861C12.1485 11.5933 12.268 11.5283 12.3307 11.4184L13.236 9.83441C13.2954 9.73056 13.4058 9.66645 13.5254 9.66641H16.6654" stroke="var(--header-icon-color, #76899F)" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10.3307 3.16895C10.4228 3.16895 10.4974 3.24356 10.4974 3.33561C10.4974 3.42766 10.4228 3.50228 10.3307 3.50228C10.2387 3.50228 10.1641 3.42766 10.1641 3.33561C10.1641 3.24356 10.2387 3.16895 10.3307 3.16895" stroke="var(--header-icon-color, #76899F)" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12.3307 3.16895C12.4228 3.16895 12.4974 3.24356 12.4974 3.33561C12.4974 3.42766 12.4228 3.50228 12.3307 3.50228C12.2387 3.50228 12.1641 3.42766 12.1641 3.33561C12.1641 3.24356 12.2387 3.16895 12.3307 3.16895" stroke="var(--header-icon-color, #76899F)" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14.3307 3.16895C14.4228 3.16895 14.4974 3.24356 14.4974 3.33561C14.4974 3.42766 14.4228 3.50228 14.3307 3.50228C14.2387 3.50228 14.1641 3.42766 14.1641 3.33561C14.1641 3.24356 14.2387 3.16895 14.3307 3.16895" stroke="var(--header-icon-color, #76899F)" stroke-linecap="round" stroke-linejoin="round"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.33203 2.66895C1.33203 2.11666 1.77975 1.66895 2.33203 1.66895H15.6654C16.2176 1.66895 16.6654 2.11666 16.6654 2.66895V13.3356C16.6654 13.8879 16.2176 14.3356 15.6654 14.3356H2.33203C1.77975 14.3356 1.33203 13.8879 1.33203 13.3356V2.66895Z" stroke="var(--header-icon-color, #76899F)" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1.33203 5.00195H16.6654" stroke="var(--header-icon-color, #76899F)" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>					
          </span>
				</a>
			</li>'
    );
  }

  public function checkEntries()
  {
    if(empty($this->gateway) || empty($this->service)) {
      return;
    }
    $this->app->DB->Update(
      "UPDATE systemhealth SET description = 'DB-Version ab 20.2' WHERE description = 'DB-Version ab 20.1'"
    );
    $categories = $this->getDefaultCategories();
    foreach($this->getValues() as $category => $names) {
      $categoryRow = $this->gateway->getCategoryByName($category);
      if(empty($categoryRow)) {
        try {
          $categoryId = $this->service->createCategory($category, $categories[$category]);
        }
        catch(Exception $e) {
          continue;
        }
      }
      else {
        $categoryId = $categoryRow['id'];
      }
      foreach($names as $name => $description) {
        $resetAble = false;
        if(is_array($description)) {
          $resetAble = !empty($description[1]);
          $description = reset($description);
        }
        $systemHealth = $this->gateway->getByName($categoryId, $name);
        if(empty($systemHealth)) {
          $this->service->create($categoryId, $name, $description, '', $resetAble);
        }
        elseif($resetAble && empty($systemHealth['resetable'])) {
          $this->service->update($systemHealth['id'], $description, $systemHealth['status'], $resetAble);
        }
      }
    }
  }

  /**
   * @param string $categoryKey
   * @param string $categoryTitle
   * @param string $entryKey
   * @param string $entryTitle
   * @param string $status
   * @param string $message
   * @param bool   $resetAble
   */
  public function createEntryWithCategoryIfError(
    $categoryKey, $categoryTitle, $entryKey, $entryTitle, $status, $message = '', $resetAble = false
  )
  {
    if(empty($categoryKey) || empty($entryKey) || empty($status)) {
      return;
    }
    try {
      $category = $this->gateway->getCategoryByName($categoryKey);
      if($status === 'error' || $status === 'warning'){
        if(empty($category)) {
          $this->service->createCategory($categoryKey, $categoryTitle);
        }
        $this->createEntryIfError($categoryKey, $entryKey, $entryTitle, $status, $message, $resetAble);
      }
      else {
        if(empty($category)) {
          return;
        }
        $this->createEntryIfError($categoryKey, $entryKey, $entryTitle, $status, $message, $resetAble);
      }
    }
    catch (Exception $e) {

    }
  }

  /**
   * @param string $categoryKey
   * @param string $entryKey
   * @param string $entryTitle
   * @param string $status
   * @param string $message
   * @param bool   $resetable
   */
  public function createEntryIfError($categoryKey, $entryKey, $entryTitle, $status, $message = '', $resetable = false)
  {
    try {
      if($status === 'error' || $status === 'warning'){
        $category = $this->gateway->getCategoryByName($categoryKey);
        $systemHealth = $this->gateway->getByName($category['id'], $entryKey);
        if(empty($systemHealth)){
          $this->service->create($category['id'], $entryKey, $entryTitle, '', $resetable);
        }
        $this->changeStatus(
          $categoryKey, $entryKey, $status, $message
        );
        return;
      }
      if($status === 'ok'){
        $category = $this->gateway->getCategoryByName($categoryKey);
        $systemHealth = $this->gateway->getByName($category['id'], $entryKey);
        if(!empty($systemHealth)){
          $this->changeStatus(
            $categoryKey, $entryKey, 'ok', ''
          );
        }
      }
    }
    catch(Exception $e) {

    }
  }

  public function fillEntries()
  {
    $phpVersion = substr(PHP_VERSION,0,3);
    if($phpVersion < 7.2) {
      $status = 'warning';
      $phpVersion = 'Ab Xentral 20.2 benötigen Sie mind. PHP 7.2 (aktuell '.$phpVersion
        .'). <a href="https://xentral.com/akademie-faq/systemvoraussetzungen-hd-ich-erhalte-den-hinweis-die-fehlermeldung-ab-xentral-20-1-benotigen-sie-min-php-7-2-aktuell-was-ist-zu-tun" target="_blank">Hilfe</a>';
    }
    else {
      $status = 'ok';
    }
    $this->changeStatus('server', 'php_version', $status, $phpVersion);

    $dbType = '';
    if(method_exists($this->app->DB, 'GetVersionArr')) {
      list($dbType, $dbVersion) = $this->app->DB->GetVersionArr();
      if($dbType === 'mysql') {
        $status = 'warning';
        if($dbVersion >= 5.7) {
          $status = 'ok';
        }
        $mysqlVersion = 'MySQL '.$dbVersion;
      }
      elseif($dbType === 'mariadb') {
        $status = 'warning';
        if($dbVersion >= 10.2) {
          $status = 'ok';
        }
        $mysqlVersion = 'MariaDB '.$dbVersion;
      }
      else {
        $dbType = '';
      }
    }
    if(empty($dbType)) {
      $mysqlVersion = $this->app->DB->GetVersion();
      $mysqlVersion = $mysqlVersion[0] . '.' . $mysqlVersion[1];
      $status = 'warning';
      if($mysqlVersion >= 5.5){
        $status = 'ok';
      }
    }
    //@todo select version() >=  10.2 mariadb + update.php prüfung
    $this->changeStatus('database', 'db_version', $status, $mysqlVersion);
    if($status === 'warning') {
      $mysqlVersion = 'Ab Xentral 20.2 benötigen Sie mind. MySQL 5.7 / MariaDb 10.2 (aktuell '.$mysqlVersion
        .'). <a href="https://xentral.com/akademie-faq/systemvoraussetzungen-hd-ich-erhalte-den-hinweis-die-fehlermeldung-ab-xentral-20-1-benotigen-sie-min-mysql-5-7-aktuell-was-muss-ich-tun" target="_blank">Hilfe</a>';
    }
    $this->changeStatus('database', 'db_version_min', $status, $mysqlVersion);

    $system_cronjob_memory_limit = (string)$this->app->erp->GetKonfiguration('system_cronjob_memory_limit');
    if($system_cronjob_memory_limit === '-1' || $system_cronjob_memory_limit === '0')
    {
      $this->changeStatus('cronjobs','max_memory','ok','OK (unbegrenzt)');
    }
    elseif(!empty($system_cronjob_memory_limit)) {
      if($system_cronjob_memory_limit <= 64 * 1024 * 1024) {
        $this->changeStatus('cronjobs','max_memory','warning','Es werden mindestens 256 MB empfohlen ('
          .round($system_cronjob_memory_limit / 1024 / 1024) .' MB eingestellt)');
      }
      elseif($system_cronjob_memory_limit < 256 * 1024 * 1024) {
        $this->changeStatus('cronjobs','max_memory','warning','Es werden mindestens 256 MB empfohlen ('
          .round($system_cronjob_memory_limit / 1024 / 1024) .' MB eingestellt)');
      }
      else {
        $this->changeStatus('cronjobs','max_memory','ok','OK ('.round($system_cronjob_memory_limit / 1024 / 1024) .' MB)');
      }
    }
    $system_cronjob_max_execution_time = (string)$this->app->erp->GetKonfiguration('system_cronjob_max_execution_time');
    if($system_cronjob_max_execution_time === '0') {
      $this->changeStatus('cronjobs','max_execution_time','ok','unbegrenzt');
    }
    elseif(!empty($system_cronjob_max_execution_time)) {
      if($system_cronjob_max_execution_time < 30) {
        $this->changeStatus('cronjobs','max_execution_time','warning','< 30 Sekunden ('.$system_cronjob_max_execution_time.' Sekunden)');
      }
      elseif($system_cronjob_max_execution_time < 300){
        $this->changeStatus('cronjobs','max_execution_time','warning','< 300 Sekunden ('.$system_cronjob_max_execution_time.' Sekunden)');
      }
      else {
        $this->changeStatus('cronjobs','max_execution_time','ok','OK ('.$system_cronjob_max_execution_time.' Sekunden)');
      }
    }

    $hintergrund=$this->app->erp->Firmendaten('hintergrund');
    $hintergrund_size = 0;
    switch($hintergrund) {
      case 'briefpapier':
        $hintergrund_size = strlen($this->app->erp->getSettingsFile('briefpapier'));
        break;
      case 'logo':
        $hintergrund_size = strlen($this->app->erp->getSettingsFile('logo'));
        break;
    }

    if($hintergrund_size / 1000 > 100){
      $this->changeStatus(
        'settings',
        'letterhead',
        'warning',
        'Die Dateigr&ouml;&szlig;e von ca. '.round($hintergrund_size / 1000).
        ' KB vom ' . ucfirst($hintergrund) . ' ist zu gro&szlig;. Das ' . ucfirst($hintergrund) .
        ' sollte unter 100 KB sein');
    }
    else {
      $this->changeStatus('settings','letterhead','ok','OK');
    }

    $this->loadFreeDisc();
    if((string)1.1 === '1,1') {
      $category = $this->gateway->getCategoryByName('server');
      $name = 'locale_lc_numeric';
      $description = 'Decimaltrennzeichen Einstellung';
      $systemHealth = $this->gateway->getByName($category['id'], $name);
      if(empty($systemHealth)) {
        $this->service->create($category['id'], $name, $description, '', false);
      }
      $this->changeStatus(
        'server',$name,'error','Die lokale Decimal Trennzeicheneinstellung ist fehlerhaft'
      );
    }
    else {
      $category = $this->gateway->getCategoryByName('server');
      $name = 'locale_lc_numeric';
      $systemHealth = $this->gateway->getByName($category['id'], $name);
      if(!empty($systemHealth)) {
        $this->changeStatus(
          'server',$name,'ok',''
        );
      }
    }
    if(!function_exists('imap_open')) {
      $category = $this->gateway->getCategoryByName('server');
      $name = 'imap_plugin';
      $description = 'IMAP Plugin';
      $systemHealth = $this->gateway->getByName($category['id'], $name);
      if(empty($systemHealth)) {
        $this->service->create($category['id'], $name, $description, '', false);
      }
      $this->changeStatus(
        'server',$name,'error','IMAP ist nicht installiert'
      );
    }
    else {
      $category = $this->gateway->getCategoryByName('server');
      $name = 'imap_plugin';
      $systemHealth = $this->gateway->getByName($category['id'], $name);
      if(!empty($systemHealth)) {
        $this->changeStatus(
          'server',$name,'ok',''
        );
      }
    }
    $this->app->erp->RunHook('systemhealth_fill_entries');
  }

  public function loadFreeDisc(): void
  {
    $free = $this->getDiskFree();
    if($free === false) {
      $this->changeStatus('server', 'disk_space', 'warning', 'Der freie Speicherplatz konnte nicht ermittelt werden');
    }
    else{
      $free /= 1024 * 1024;
      $userdataSize = (int)$this->app->erp->GetKonfiguration('userdatasize');
      $dbSize = $this->getDbSize();
      if(is_array($dbSize)) {
        $dbSize = $dbSize['all'];
      }
      $minFree = (int)($dbSize / 1024) + $userdataSize + 512;
      if($minFree < 2048) {
        $minFree = 2048;
      }
      if($free < 512) {
        $this->changeStatus('server', 'disk_space', 'error', 'Es sind nur ' . round($free) . ' MB Speicherplatz frei');
      }
      elseif($free < $minFree) {
        $this->changeStatus('server', 'disk_space', 'warning', 'Es sind nur ' . round($free) . ' MB Speicherplatz frei');
      }
      else {
        $this->changeStatus('server', 'disk_space', 'ok', 'Es sind ' . round($free) . ' MB Speicherplatz frei');
      }
    }
  }

  /**
   * @param string $category
   * @param string $name
   * @param string $status
   * @param string $message
   */
  public function changeStatus($category, $name, $status, $message)
  {
    if(empty($this->gateway) || empty($this->service)) {
      return;
    }
    try {
      $categoryRow = $this->gateway->getCategoryByName($category);
      if(empty($categoryRow)) {
        return;
      }
      $categoryId = $categoryRow['id'];

      $systemHealth = $this->gateway->getByName($categoryId, $name);
      if(empty($systemHealth)) {
        return;
      }

      $this->service->setStatus($systemHealth['id'], $status, $message);
    }
    catch(Exception $e) {

    }
  }

  /**
   * @return array
   */
  public function getDefaultCategories()
  {
    return [
      'masterdata' => 'Stammdaten',
      'bestbeforebatchsn' => 'MHD / Chargen / Seriennummern',
      'server' => 'Server',
      'settings' => 'Einstellungen',
      'cronjobs' => 'Prozessstarter',
      'database' => 'Datenbank',
      'shopexport' => 'Onlineshops',
    ];
  }

  /**
   * @return array
   */
  public function getValues()
  {
    return [
      'masterdata' => [
        'double_articlenumber'  => ['Artikelnummern', true],
        'double_suppliernumber' => ['Lieferantennummern', true],
        'double_customernumber' => ['Kundennummern', true],
        'empty_articlenumber'   => ['Leere Artikelnummern', true],
        'double_invoice'        => ['Rechnungsnummern', true],
        'double_return_order'   => ['Gutschriftsnummern', true],
      ],
      'bestbeforebatchsn' => [
        'sn'                => ['Seriennummern', true],
        'batch'             => ['Chargen-Schiefstand', true],
        'bestbefore'        => ['MHD-Schiefstand', true],
        'expiredbestbefore' => ['abgelaufene MHD', true],
      ],
      'server' => [
        'php_version'        => 'PHP-Version',
        'extension_zip'      => 'Zip Extension',
        'disk_space'         => 'Speicherplatz',
        'max_upload'         => 'Upload-Kapazit&auml;t',
        'max_execution_time' => 'Scriptlauftzeit',
        'userdata_writeable' => 'Schreibrechte in Userdata',
        'ioncube'            => 'Lizenz',
        'tls1-2'             => 'TLS v1.2',
      ],
      'settings' => [
        'company_settings' => 'Firmendaten',
        'letterhead'       => 'Briefpapier',
      ],
      'cronjobs' => [
        'lastrunning'        => 'Aktivit&auml;t',
        'frequently'         => 'H&auml;ufigkeit',
        'max_memory'         => 'Speicher',
        'out_of_memory'      => ['Speicher&uuml;berlauf', true],
        'max_execution_time' => 'Laufzeit',
        'errors'             => ['Fehler', true]
      ],
      'database' => [
        'db_version'     => 'DB-Version',
        'db_version_min' => 'DB-Version ab 20.2',
        'sql_errors'     => ['SQL-Fehler', true],
        'sql_integrity'  => ['Datenintegrit&auml;t', true],
      ],
      'shopexport' => [
        'auth' => ['Authentifizierung', true],
      ],
    ];
  }

  /**
   * @return array
   */
  public function getTableSizes()
  {
    try{
      return $this->service->getTableSizes($this->app->Conf->WFdbname);
    }
    catch(Exception $e) {
      return [];
    }
  }

  /**
   * @return array
   */
  public function getLogTables()
  {
    return [
      'shopexport_log',
      'logfile',
      'protokoll',
      'logfile',
      'cronjob_log',
      'cronjob_starter_running',
      'uebertragungen_monitor',
      'adapterbox_request_log',
      'versandzentrum_log',
    ];
  }

  /**
   * @var bool $splittedLogs
   *
   * @return int|array
   */
  public function getDbSize($splittedLogs = false)
  {
    if($splittedLogs) {
      try {
        $dbSize = $this->service->getTableSizes($this->app->Conf->WFdbname);
        if(empty($dbSize)) {
          return 0;
        }
        $ret = ['all' => 0., 'log' => 0.];
        $logTables = $this->getLogTables();
        foreach($dbSize as $table => $size) {
          $ret['all'] += (float)$size;
          if(in_array($table, $logTables,true)) {
            $ret['log'] += (float)$size;
          }
        }

        return $ret;
      }
      catch(Exception $e) {
        return 0;
      }
    }

    try {
      return $this->service->getDbSize($this->app->Conf->WFdbname);
    }
    catch(Exception $e) {
      return 0;
    }
  }

  /**
   * @return array
   */
  public function getSystemLoad()
  {
    try {
      return $this->service->getSystemLoad();
    }
    catch(Exception $e) {
      return [null, null, null, null, null];
    }
  }

  /**
   * @param float|int $tresholdAct
   * @param float|int $treshold5Min
   *
   * @return bool
   */
  public function isSystemLoadHigh($tresholdAct = 5.0, $treshold5Min = 5.0)
  {
    $systemLoad = $this->getSystemLoad();
    $loadAct = empty($systemLoad['act'])?0:$systemLoad['act'];
    $load5min = empty($systemLoad['5min'])?0:$systemLoad['5min'];

    return $loadAct > $tresholdAct || $load5min > $treshold5Min;
  }

  /**
   * @param string $dir
   *
   * @return false|float
   */
  public function getDiskFree($dir = '')
  {
    try{
      return $this->service->getDiskFree($dir);
    }
    catch(Exception $e) {
      return false;
    }
  }

  /**
   * @param string|array $type
   *
   * @return int|array
   */
  public function getUserdataSpace($type = '')
  {
    $dir = $this->app->Conf->WFuserdata;
    if(is_array($type)) {
      $ret = [];
      $excludes = [];
      $sum = 0;
      foreach($type as $key => $typeString) {
        if(empty(trim($typeString))) {
          unset($type[$key]);
          continue;
        }
        try {
          $ret[$typeString] = $this->service->getUsedSpace($this->app->Conf->WFuserdata.'/'.$typeString, []);
          $sum += $ret[$typeString];
        }
        catch (Exception $e) {
          $ret[$typeString] =  0;
        }
        $excludes[] = './'.$typeString;
      }
      try {
        $ret['sum'] = $sum + $this->service->getUsedSpace($this->app->Conf->WFuserdata, $excludes);
      }
      catch(Exception $e) {
        $ret['sum'] = $sum;
      }

      return $ret;
    }
    $exclude = [];
    switch($type) {
      case 'userdatawithoutdmspdfarchiv':
        $dir = $this->app->Conf->WFuserdata;
        $exclude = ['./pdfarchiv', './dms'];
        break;
      case 'dms':
        $dir = $this->app->Conf->WFuserdata.'/dms';
        break;
      case 'pdfarchiv':
        $dir = $this->app->Conf->WFuserdata.'/pdfarchiv';
        break;
      default:
        if(strpos($type, '.') !== false && is_dir($this->app->Conf->WFuserdata.'/'.$type)) {
          $dir = $this->app->Conf->WFuserdata.'/'.$type;
        }
        break;
    }
    try {
      return $this->service->getUsedSpace($dir, $exclude);
    }
    catch (Exception $e) {
      return 0;
    }
  }

  /**
   * @return int[]
   */
  public function getUserDataSpaces()
  {
    return $this->getUserdataSpace(
      [
        'dms',
        'pdfarchiv',
        'pdfmirror',
        'tmp',
        'wiki',
        'emailbackup',
        'uebertragung',
      ]
    );
  }

  /**
   * @var string $dir
   *
   * @return array
   */
  public function getPartions($dir = '')
  {
    try{
      return $this->service->getPartions($dir);
    }
    catch (Exception $e) {

    }

    return [];
  }

  public function doCronjob()
  {
    $userDataSpaces = $this->getUserDataSpaces();
    foreach($userDataSpaces as $userDataDir => $userDataSpace) {
      if($userDataDir === 'sum') {
        $this->app->erp->SetKonfigurationValue('userdatasize', (int)ceil($userDataSpace / 1024));
      }
      else {
        $this->app->erp->SetKonfigurationValue('userdata'.$userDataDir.'size', (int)ceil($userDataSpace / 1024));
      }
    }
    try{
      $backupSize = (int)$this->service->getUsedSpace(dirname(__DIR__,2).'/backup');
    }
    catch (Exception $e) {
      $backupSize = 0;
    }
    $this->app->erp->SetKonfigurationValue('backupsize', (int)ceil($backupSize / 1024));
    $dbSize = $this->getDbSize(true);
    $dbSizeLog = 0;
    if(is_array($dbSize)) {
      if(!empty($dbSize['log'])) {
        $dbSizeLog = $dbSize['log'];
      }
      $dbSize = $dbSize['all'];
    }

    $this->app->erp->SetKonfigurationValue('databasesize', (int)ceil($dbSize / 1024));
    $this->app->erp->SetKonfigurationValue('databasesizelog', (int)ceil($dbSizeLog / 1024));
    $this->app->erp->SetKonfigurationValue('diskfree', (int)floor($this->getDiskFree() / 1024 / 1024));
    $this->app->erp->SetKonfigurationValue('lastsizeupdate', date('Y-m-d H:i:s'));

    $partitions =  $this->getPartions(dirname(__DIR__, 2));
    if(!empty($partitions)) {
      $this->app->erp->SetKonfigurationValue('partitiontotal', (int)($partitions['total'] / 1024));
      $this->app->erp->SetKonfigurationValue('partitionused', (int)($partitions['used'] / 1024));
      $this->app->erp->SetKonfigurationValue('partitionfree', (int)($partitions['free'] / 1024));
    }

    $this->fillEntries();
    $this->app->erp->RunHook('systemhealth_cronjob');
  }

  /**
   * @return array
   */
  public function getHealthData()
  {
    $ret = [];
    $healthData = $this->app->DB->SelectArr('SELECT * FROM `systemhealth` ORDER BY `id`');
    foreach($healthData as $healthRow) {
      $ret[$healthRow['category']][$healthRow['name']] = $healthRow;
    }

    return $ret;
  }
}
