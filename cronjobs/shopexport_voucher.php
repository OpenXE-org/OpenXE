<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
if(file_exists(dirname(__DIR__).'/xentral_autoloader.php'))
{
  include_once dirname(__DIR__).'/xentral_autoloader.php';
}
@date_default_timezone_set('Europe/Berlin');

include_once dirname(__DIR__).'/conf/main.conf.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.mysql.php';

include_once dirname(__DIR__).'/phpwf/plugins/class.secure.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.user.php';
include_once dirname(__DIR__).'/www/lib/imap.inc.php';
include_once dirname(__DIR__).'/www/lib/class.erpapi.php';

if(is_file(dirname(__DIR__).'/www/lib/class.erpapi_custom.php')){
  include_once dirname(__DIR__) . '/www/lib/class.erpapi_custom.php';
}
include_once dirname(__DIR__).'/www/lib/class.httpclient.php';
$aes = '';
$phpversion = PHP_VERSION;
if(strpos($phpversion,'7') === 0 && (int)$phpversion{2} > 0)
{
  $aes = '2';
}
if($aes === '2' && is_file(dirname(__DIR__).'/www/lib/class.aes'.$aes.'.php'))
{
  include_once dirname(__DIR__).'/www/lib/class.aes'.$aes.'.php';
}elseif(is_file(dirname(__DIR__) . '/www/lib/class.aes.php')){
  include_once dirname(__DIR__) . '/www/lib/class.aes.php';
}
include_once dirname(__DIR__).'/www/lib/class.remote.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.phpmailer.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.smtp.php';
if(!class_exists('app_t')){
  class app_t extends ApplicationCore
  {
    public $DB;
    public $user;
    public $mail;
    public $erp;
    public $remote;
  }
}
if(empty($app) || !class_exists('ApplicationCore') || !($app instanceof ApplicationCore)){
  $app = new app_t();
}
if(empty($app->Conf)){
  $app->Conf = new Config();
}

$DEBUG = 0;

if(empty($app->DB)){
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, null, $app->Conf->WFdbport);
}
if(class_exists('erpAPICustom'))
{
  $erp = new erpAPICustom($app);
}else{
  $erp = new erpAPI($app);
}
$app->erp = $erp;
$app->String = new WawiString();
if(class_exists('RemoteCustom'))
{
  $remote = new RemoteCustom($app);
}else{
  $remote = new Remote($app);
}

$app->remote = $remote;

$app->FormHandler = new FormHandler($app);

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND (parameter = 'shopexport_voucher') AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'shopexport_voucher' AND aktiv = 1")){
  return;
}

$shops = $app->DB->SelectArr('SELECT id FROM shopexport WHERE gutscheineuebertragen=1');

foreach ($shops as $shop){
  $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopexport_voucher'");

  //Get Vouchers
  try {
    $vouchers = $app->remote->RemoteCommand($shop['id'],'getvouchers');
  }catch(Exception $exception)
  {
    $app->erp->LogFile($app->DB->real_escape_string($exception->getMessage()));
  }
  if($vouchers['success']){
    foreach ($vouchers['data'] as $voucherInShop){
      $sql = sprintf("SELECT id FROM voucher WHERE voucher_code='%s' LIMIT 1", $voucherInShop['code']);
      $voucherInXentral = $app->DB->SelectRow($sql);

      if(empty($voucherInXentral)){
        $sql = sprintf("INSERT INTO voucher (voucher_code, voucher_original_value, voucher_residual_value, valid_from, 
                     voucher_date,valid_to)
          VALUES ('%s','%s','%s','%s','%s','%s')",
          $voucherInShop['code'],$voucherInShop['value'],$voucherInShop['value'],$voucherInShop['valid_from'],
          $voucherInShop['valid_from'],'0000-00-00');
        $app->DB->Insert($sql);
      }
      $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopexport_voucher'");
    }
  }

  //Send Vouchers
  $sql = "SELECT svc.id AS cacheid, v.* FROM voucher AS v
    LEFT JOIN shopexport_voucher_cache AS svc ON v.id = svc.voucher_id
    WHERE (v.valid_to > NOW() OR v.valid_to='0000-00-00') 
      AND (v.valid_from < NOW() OR v.valid_from='0000-00-00') 
      AND (ISNULL(svc.id) OR svc.value<>v.voucher_residual_value)";
  $vouchersToSend = $app->DB->SelectArr($sql);
  foreach ($vouchersToSend as $voucherToSend){
    try {
      $response = $app->remote->RemoteCommand($shop['id'],'sendvoucher',$voucherToSend);
      if($response['success']){
        if(empty($voucherToSend['cacheid'])){
          $sql = sprintf("INSERT INTO shopexport_voucher_cache (voucher_id, value) VALUES ('%s','%s')",
            $voucherToSend['id'],$voucherToSend['voucher_residual_value']);
          $app->DB->Insert($sql);
        }else{
          $sql = sprintf("UPDATE shopexport_voucher_cache SET value ='%s' WHERE id='%s'",
            $voucherToSend['voucher_residual_value'],$voucherToSend['cacheid']);
          $app->DB->Update($sql);
        }
      }else{
        $app->erp->LogFile('voucher was not send to shop. Shopid: '.$shop['id'].' - voucher Code: '.$voucherToSend['voucher_code'],print_r($response['message'],true));
      }
    }catch(Exception $exception)
    {
      $app->erp->LogFile($app->DB->real_escape_string($exception->getMessage()));
    }
    $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopexport_voucher'");
  }

  $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopexport_voucher'");
}

$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0 WHERE parameter = 'shopexport_voucher'");
