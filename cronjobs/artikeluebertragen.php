<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");



class app_t {
  var $DB;
  var $erp;
  var $user;
  var $remote;
}
*/
//ENDE
if(file_exists(dirname(__DIR__).'/www/lib/class.erpapi_custom.php') && !class_exists('erpAPICustom')){
  include_once dirname(__DIR__) . '/www/lib/class.erpapi_custom.php';
}
if(empty($app->Conf)) {
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, null, $app->Conf->WFdbport);
}
if(!isset($app->erp) || !$app->erp) {
  if (class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  } else {
    $erp = new erpAPI($app);
  }
//$remote = new Remote($app);
  $app->erp = $erp;
//$app->remote= $remote;
}
if(empty($app->remote)) {
  if(is_file(dirname(__DIR__) . '/www/lib/class.remote_custom.php')) {
    if(!class_exists('RemoteCustom')){
      require_once dirname(__DIR__) . '/www/lib/class.remote_custom.php';
    }
    $app->remote = new RemoteCustom($app);
  }
  else {
    $app->remote = new Remote($app);
  }
}

$app->erp->LogFile('Cronjob Artikeluebertragung Start');

$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `mutexcounter` = `mutexcounter` + 1 
  WHERE `mutex` = 1 AND (`parameter` = 'artikeluebertragen') AND `aktiv` = 1"
);

if($app->DB->Select("SELECT `mutex` FROM `prozessstarter` WHERE (`parameter` = 'artikeluebertragen') LIMIT 1") == 1){
  return;
}

$articles = $app->DB->SelectArr(
  'SELECT `id`,`shop`,`artikel` FROM `shopexport_artikeluebertragen_check` ORDER BY `id` LIMIT 10'
);
if(!empty($articles)) {
  /** @var Shopexport $objShopexport */
  $objShopexport = $app->loadModule('shopexport');
  if($objShopexport !== null && method_exists($objShopexport, 'addChangedArticles')) {
    $objShopexport->addChangedArticles();
  }
}

$anzChecked = [];
$anzChanged = [];
$lastids = [];
while(!empty($articles)) {
  foreach($articles as $article) {
    if(empty($anzChanged[$article['shop']])) {
      $anzChanged[$article['shop']] = 0;
    }
    if(empty($anzChecked[$article['shop']])) {
      $anzChecked[$article['shop']] = 0;
    }
    if(!isset($lastids[$article['shop']])) {
      $lastids[$article['shop']] = (int)$app->erp->GetKonfiguration(
        'shopexport_artikeluebertragen_check_lastid_' . $article['shop']
      );
    }
    $changed = $objShopexport->hasArticleHashChanged($article['artikel'], $article['shop']);
    if($changed['changed']) {
      $app->DB->Insert(
        sprintf(
          'INSERT INTO `shopexport_artikeluebertragen` (`artikel`, `shop`, `check_nr`) VALUES (%d, %d, %d)',
          $article['artikel'], $article['shop'], $lastids[$article['shop']]
        )
      );
      $anzChanged[$article['shop']]++;
    }
    $anzChecked[$article['shop']]++;
    $app->DB->Delete(
      sprintf(
        'DELETE FROM `shopexport_artikeluebertragen_check` WHERE `id` = %d',
        $article['id']
      )
    );
    $app->DB->Update(
      sprintf(
        "UPDATE `shopexport` SET `autosendarticle_last` = NOW() WHERE `id` = %d",
        $article['shop']
      )
    );
  }
  $app->erp->SetKonfigurationValue(
    'shopexport_artikeluebertragen_check_changed_'.$article['shop'],
    $anzChanged[$article['shop']]
  );
  $app->erp->SetKonfigurationValue(
    'shopexport_artikeluebertragen_check_checked_'.$article['shop'],
    $anzChecked[$article['shop']]
  );
  if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikeluebertragen'])) {
    return;
  }
  $articles = $app->DB->SelectArr(
    'SELECT `id`,`shop`,`artikel` FROM `shopexport_artikeluebertragen_check` ORDER BY `id` LIMIT 10'
  );
  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `letzteausfuerhung`=NOW(), `mutex` = 1,`mutexcounter`=0 
    WHERE `parameter` = 'artikeluebertragen'"
  );
}

$check = $app->DB->Select('SELECT COUNT(`id`) FROM `shopexport_artikeluebertragen`');
$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `letzteausfuerhung`=NOW(), `mutex` = 1,`mutexcounter`=0 
  WHERE `parameter` = 'artikeluebertragen'"
);
while($check > 0) {
  $shopartikel = $app->DB->Query(
    "SELECT `id`,`shop`,`artikel`,`check_nr` FROM `shopexport_artikeluebertragen` ORDER BY `id` LIMIT 10"
  );
  if(!empty($shopartikel)) {
    $anz = 0;
    while ($row = $app->DB->Fetch_Assoc($shopartikel)) {
      if(!isset($lastids[$row['shop']])) {
        $lastids[$row['shop']] = (int)$app->erp->GetKonfiguration('shopexport_artikeluebertragen_check_lastid_' . $row['shop']);
      }

      $anz++;
      try {
        $app->remote->RemoteSendArticleList($row['shop'], array($row['artikel']));
        $app->erp->LagerSync($row['artikel'], true);
      }
      catch (Execption $exception) {
        $app->erp->LogFile($app->DB->real_escape_string($exception->getMessage()));
      }
      $app->DB->Delete(
        sprintf(
          'DELETE FROM `shopexport_artikeluebertragen` WHERE `id`= %d LIMIT 1',
          $row['id']
        )
      );

      if(!empty($row['check_nr']) && $row['check_nr'] == $lastids[$row['shop']]) {
        $transfered = 1+(int)$app->erp->GetKonfiguration('shopexport_artikeluebertragen_check_transfered_'.$row['shop']);
        $app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_transfered_'.$row['shop'], $transfered);
      }

      $app->DB->Update(
        "UPDATE `prozessstarter` 
        SET `letzteausfuerhung`=NOW(), `mutex` = 1,`mutexcounter`=0 
        WHERE `parameter` = 'artikeluebertragen'"
      );
    }

    $app->DB->free($shopartikel);
  }

  if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikeluebertragen'])) {
    return;
  }

  //sleep(20); // das performance nicht total spinnt
  $check = $app->DB->Select('SELECT COUNT(`id`) FROM `shopexport_artikeluebertragen`');
}
$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `letzteausfuerhung`= NOW(), `mutex` = 0,`mutexcounter`=0 
  WHERE `parameter` = 'artikeluebertragen'"
);


