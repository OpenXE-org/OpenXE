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

use Xentral\Components\Filesystem\Adapter\FtpConfig;
use Xentral\Components\Filesystem\FilesystemFactory;
use Xentral\Components\Http\JsonResponse;
use Xentral\Modules\AmaInvoice\Service\AmaInvoiceService;

class Amainvoice
{
  /** @var Application $app */
  protected $app;

  /** @var array $error */
  protected $error;

  /** @var AmaInvoiceService $service */
  protected $service;

  /** @var string MODULE_NAME */
  const MODULE_NAME = 'AmaInvoice';

  /** @var array $javascript */
  public $javascript = [
    './classes/Modules/AmaInvoice/www/js/amainvoice.js',
  ];

  /**
   * Amainvoice constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    $this->service = $this->app->Container->get('AmaInvoiceService');
    if($intern){
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler('list', 'AmainvoiceList');

    $this->app->ActionHandlerListen($app);
  }


  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public function TableSearch($app, $name, $erlaubtevars)
  {
    switch($name)
    {
      case 'amainvoice_list':
        $allowed = ['amainvoice' => ['list']];
        $heading = ['','Datum','Typ','Anzahl gesamt','Anzahl importiert','in Warteschlange', ''];
        $width = ['1%','10%','10%','5%','5%','5%','1%'];
        $findcols = ['t.id','t.date','t.type','t.count_all','t.count_imported','count_queue','t.id'];
        $searchsql = ["DATE_FORMAT(t.date,'%d.%m.%Y')",'t.type'];
        $datecols = [1];
        $alignright = [4,5,6];
        $sql = "
        SELECT SQL_CALC_FOUND_ROWS t.date,
           CONCAT('<input class=\"select\" type=\"checkbox\" data-id=\"',t.id,'\" />'),
        DATE_FORMAT(t.date,'%d.%m.%Y'), 
         t.type, t.count_all,t.count_imported, t.count_queue,
        t.id
        FROM (
            SELECT IF(ap.rem_date = '', 'Rechnung', 'Gutschrift') AS `type`,
            IF(ap.rem_date = '', ap.inv_date , ap.rem_date) AS `date`,
            COUNT(ap.id) AS `count_all`,
            SUM(IF(ap.doctype_id > 0,1,0)) AS `count_imported`,
            SUM(IF(ap.create > 0 AND ap.doctype_id = 0,1,0)) AS `count_queue`,
            IF(ap.rem_date = '', CONCAT('inv',ap.inv_date) , CONCAT('rem', ap.rem_date)) AS `id`
            FROM `amazoninvoice_position` AS `ap`
            GROUP BY IF(ap.rem_date = '', 'Rechnung', 'Gutschrift'), IF(ap.rem_date = '', ap.inv_date , ap.rem_date),
                 IF(ap.rem_date = '', CONCAT('inv',ap.inv_date) , CONCAT('rem', ap.rem_date))    
        ) AS `t`
        ";

        break;
    }


    $erg = [];

    foreach($erlaubtevars as $k => $v) {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  /**
   * @return array
   */
  public function getCheckBoxes()
  {
    return ['ftp', 'createorder'];
  }

  /**
   * @return array
   */
  public function getTextFields()
  {
    return ['dir', 'user', 'server', 'port', 'firmkeyid', 'startdate', 'projectfbm', 'projectfba', 'paymentmethod',];
  }

  /**
   * @return array
   */
  public function getPasswordFields()
  {
    return ['pass', 'clientidentifier'];
  }

  /**
   * @return array
   */
  public function getFields()
  {
    return array_merge($this->getCheckBoxes(), $this->getTextFields(), $this->getPasswordFields());
  }

  public function AmainvoiceMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=appstore&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=amainvoice&action=list', 'Details');
  }

  /**
   * @return JsonResponse
   */
  public function HandleAmaInvoiceImportListAjax()
  {
    $list = $this->app->Secure->GetPOST('list');
    $changed = 0;
    if(!empty($list)){
      foreach ($list as $element) {
        $date = new DateTime(substr($element, 3));
        if(strpos($element,'inv') === 0) {
          $this->service->markDbEntriesToImport($date, $date, false);
          $changed++;
        }
        elseif(strpos($element,'rem') === 0) {
          $this->service->markDbEntriesToImport($date, $date, true);
          $changed++;
        }
      }
    }

    return new JsonResponse(['changed' => $changed, 'success' => true]);
  }

  public function AmainvoiceList()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'importlist') {

      return $this->HandleAmaInvoiceImportListAjax();
    }
    $this->AmainvoiceMenu();
    $passwordFields = $this->getPasswordFields();
    if($this->app->Secure->GetPOST('save')){
      $arr = [];
      foreach ($this->getFields() as $field) {
        $value = empty($this->app->Secure->POST[$field]) ? '' : $this->app->Secure->POST[$field];
        if($value === '*****' && in_array($field, $passwordFields)){
          continue;
        }
        if($field === 'startdate'){
          if(strpos($value, '.') !== false){
            $value = $this->app->String->Convert($value, '%1.%2.%3', '%3-%2-%1');
          }
        }
        if(in_array($field, ['projectfbm', 'projectfba',])) {
          $value = $this->app->erp->ReplaceProjekt(1, $value, 1);
        }
        $arr[$field] = $value;
      }

      $this->service->setConfig($arr);
      $this->app->Location->execute('index.php?module=amainvoice&action=list');
    }

    $arr = $this->service->getConfig();
    foreach ($this->getCheckBoxes() as $field) {
      if(!empty($arr[$field])){
        $this->app->Tpl->Set(strtoupper($field), ' checked="checked" ');
      }
    }
    foreach ($this->getTextFields() as $field) {
      $value = !empty($arr[$field]) ? $arr[$field] : '';
      if($field === 'startdate'){
        if(strpos($value, '-') !== false){
          $value = $this->app->String->Convert($value, '%3-%2-%1', '%1.%2.%3');
        }
        $this->app->YUI->DatePicker('startdate');
      }
      if(in_array($field, ['projectfbm','projectfba'])) {
        $value = $this->app->erp->ReplaceProjekt(0, $value, 0);
        $this->app->YUI->AutoComplete($field, 'projektname', 1);
      }
      if($field === 'paymentmethod') {
        $paymentmethods = $this->app->erp->GetZahlungsweise();
        foreach ($paymentmethods as $type => $paymentmethod) {
          $this->app->Tpl->Add(
            'SELPAYMENTMETHOD',
            '<option value="' . $type .'"'
            . ($value === $type ? ' selected="selected"' : '') . '>' . $paymentmethod
            . '</option>'
          );
        }
        continue;
      }
      $this->app->Tpl->Set(strtoupper($field), $value);
    }

    foreach ($passwordFields as $field) {
      if(!empty($arr[$field])){
        $this->app->Tpl->Set(strtoupper($field), '*****');
      }
    }
    /** @var Appstore $appstore */
    $module = 'amainvoice';
    $appstore = $this->app->erp->LoadModul('appstore');
    if($appstore === null || !method_exists($appstore, 'isBeta')) {
      return;
    }
    $this->app->erp->Headlines('Amainvoice');
    if($appstore->isBeta($module)) {
      $this->app->erp->Headlines('Amainvoice', '<span class="beta">BETA</span>');
      $this->app->Tpl->Add(
        'MESSAGE',
        '<div class="info">Dieses Modul ist noch im Beta Stadium.</div>'
      );
    }
    $this->app->erp->checkActiveCronjob('amainvoice', 'MESSAGE');
    $this->app->YUI->TableSearch('TAB2', 'amainvoice_list', 'show', '', '', basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE', 'amainvoice_list.tpl');
  }

  public function Install()
  {
    $this->app->erp->CheckTable('amainvoice_config');
    $this->app->erp->CheckColumn('name', 'varchar(255)', 'amainvoice_config', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('value', 'varchar(255)', 'amainvoice_config', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckIndex('amainvoice_config', 'name', true);

    $this->app->erp->CheckTable('amainvoice_files');
    $this->app->erp->CheckColumn('filename', 'varchar(255)', 'amainvoice_files', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('type', 'varchar(32)', 'amainvoice_files', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('status', 'varchar(32)', 'amainvoice_files', "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('created_at', 'TIMESTAMP', 'amainvoice_files', 'DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckIndex('amainvoice_files', 'filename');

    $this->app->erp->CheckTable('amazoninvoice_position');
    $this->app->erp->CheckColumn('id', 'int(11)', 'amazoninvoice_position', " auto_increment");
    $this->app->erp->CheckColumn('doctype', 'varchar(32)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('doctype_id', 'int(11)', 'amazoninvoice_position', " DEFAULT '0' ");
    $this->app->erp->CheckColumn('position_id', 'int(11)', 'amazoninvoice_position', " DEFAULT '0' ");
    $this->app->erp->CheckColumn('inv_rech_nr', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('inv_date', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('amazonorderid', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shipmentdate', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('buyeremail', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('buyerphonenumber', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('buyername', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('sku', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('productname', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('quantitypurchased', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('quantityshipped', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('currency', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('mwst', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('taxrate', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('brutto_total', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('netto_total', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('tax_total', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('itemprice', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('itemprice_netto', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('itemprice_tax', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shippingprice', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shippingprice_netto', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shippingprice_tax', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('giftwrapprice', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('giftwrapprice_netto', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('giftwrapprice_tax', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('itempromotiondiscount', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('itempromotiondiscount_netto', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('itempromotiondiscount_tax', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shippromotiondiscount', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shippromotiondiscount_netto', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shippromotiondiscount_tax', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('giftwrappromotiondiscount', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('giftwrappromotiondiscount_netto', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('giftwrappromotiondiscount_tax', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shipservicelevel', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('recipientname', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shipaddress1', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shipaddress2', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shipaddress3', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shipcity', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shipstate', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shippostalcode', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shipcountry', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('shipphonenumber', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('billaddress1', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('billaddress2', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('billaddress3', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('billcity', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('billstate', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('billpostalcode', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('billcountry', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('carrier', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('trackingnumber', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('fulfillmentcenterid', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('fulfillmentchannel', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('saleschannel', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('asin', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('conditiontype', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('quantityavailable', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('isbusinessorder', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('uid', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('vatcheck', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('documentlink', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('order_id', 'int(11)', 'amazoninvoice_position', " DEFAULT '0' ");
    $this->app->erp->CheckColumn('rem_gs_nr', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('orderid', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('rem_date', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('returndate', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('buyercompanyname', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('quantity', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('remreturnshipcost', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('remsondererstattung', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('itempromotionid', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('reason', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('rem_gs_nr_real', 'varchar(255)', 'amazoninvoice_position', " DEFAULT '' ");
    $this->app->erp->CheckColumn('create', 'tinyint(1)', 'amazoninvoice_position', ' DEFAULT 0 ');
    $this->app->erp->CheckColumn('create_order', 'tinyint(1)', 'amazoninvoice_position', ' DEFAULT 0 ');
    $this->app->erp->CheckColumn('created_at', 'timestamp', 'amazoninvoice_position', 'DEFAULT CURRENT_TIMESTAMP');
    $this->app->erp->CheckProzessstarter('AmaInvoice', 'periodisch', 60, '', 'cronjob', 'amainvoice', 1);
  }

  /**
   * @param string $host
   * @param string $user
   * @param string $pass
   * @param string $dir
   * @param int    $port
   *
   * @return array
   */
  protected function ftp($host, $user, $pass, $dir, $port = 21)
  {
    /** @var FilesystemFactory $fsFactory */
    $fsFactory = $this->app->Container->get('FilesystemFactory');
    $ftpConfig = new FtpConfig($host, $user, $pass, $dir, $port);
    $ftp = $fsFactory->createFtp($ftpConfig);

    $files = $ftp->listFiles('', false);
    $fileNames = [];
    foreach ($files as $index => $file) {
      $fileNames[] = $file->getFilename();
    }

    return $fileNames;
  }

  /**
   * @param string $dir
   *
   * @return array
   */
  protected function local($dir)
  {
    $fileSystemConfig = ['permissions' => [
      'file' => [
        'public' => 0664,
        'private' => 0664,
      ],
      'dir' => [
        'public' => 0775,
        'private' => 0775,
      ],
    ]];

    /** @var FilesystemFactory $factory */
    $factory = $this->app->Container->get('FilesystemFactory');
    $fileSystem = $factory->createLocal($dir, $fileSystemConfig);

    $files = $fileSystem->listFiles('');
    $fileNames = [];
    foreach ($files as $index => $file) {
      $fileNames[] = $file->getFilename();
    }

    return $fileNames;
  }

}
