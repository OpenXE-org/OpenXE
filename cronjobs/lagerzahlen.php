<?php
if(file_exists(dirname(__DIR__).'/www/lib/class.erpapi_custom.php') && !class_exists('erpAPICustom')) {
  include_once dirname(__DIR__) . '/www/lib/class.erpapi_custom.php';
}

if(empty($app) || !class_exists('ApplicationCore') || !($app instanceof ApplicationCore)) {
  $app = new app_t();
}

if(empty($app->Conf)) {
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB)) {
  $app->DB = new DB($app->Conf->WFdbhost,$app->Conf->WFdbname,$app->Conf->WFdbuser,$app->Conf->WFdbpass,null,$app->Conf->WFdbport);
}
if(empty($app->erp)) {
  if(class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  }
  else {
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}
if(empty($app->remote)) {
  if(is_file(dirname(__DIR__) . '/www/lib/class.remote_custom.php')){
    if(!class_exists('RemoteCustom')) {
      require_once dirname(__DIR__) . '/www/lib/class.remote_custom.php';
    }
    $app->remote = new RemoteCustom($app);
  }
  else {
    $app->remote = new Remote($app);
  }
}
$app->erp->LogFile("Starte Synchronisation");

//$app->DB->Update("UPDATE artikel SET cache_lagerplatzinhaltmenge='999'");

$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");


$benutzername = $app->erp->Firmendaten("benutzername");
$passwort = $app->erp->Firmendaten("passwort");
$host = $app->erp->Firmendaten("host");
$port = $app->erp->Firmendaten("port");
$mailssl = $app->erp->Firmendaten("mailssl");
$mailanstellesmtp = $app->erp->Firmendaten("mailanstellesmtp");
$noauth = $app->erp->Firmendaten("noauth");

$app->mail = new PHPMailer($app);
$app->mail->CharSet = "UTF-8";

  if($mailanstellesmtp=="1"){
    $app->mail->IsMail();
  } else {
    $app->mail->IsSMTP();

    if($noauth=="1") $app->mail->SMTPAuth = false;
    else $app->mail->SMTPAuth   = true;

    if($mailssl==1)
        $app->mail->SMTPSecure = "tls";                 // sets the prefix to the servier
    else if ($mailssl==2)
        $app->mail->SMTPSecure = "ssl";                 // sets the prefix to the servier

    $app->mail->Host       = $host;

    $app->mail->Port       = $port;                   // set the SMTP port for the GMAIL server

    $app->mail->Username   = $benutzername;  // GMAIL username
    $app->mail->Password   = $passwort;            // GMAIL password
  }

  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `mutexcounter` = `mutexcounter` + 1 
    WHERE `mutex` = 1 AND `parameter` = 'lagerzahlen' AND `aktiv` = 1"
  );
  if(!$app->DB->Select(
    "SELECT `id` FROM `prozessstarter` WHERE `mutex` = 0 AND `parameter` = 'lagerzahlen' AND `aktiv` = 1"
  )) {
    return;
  }

  $shops = $app->DB->SelectArr('SELECT * FROM `shopexport` WHERE `aktiv` = 1');
  if(empty($shops)) {
    return;
  }
  $shopByIds = [];
  foreach($shops as $shop) {
    $shopByIds[$shop['id']] = $shop;
  }
  $shopIds = array_keys($shopByIds);
  $shopIdsStr = implode(',', $shopIds);
  $hours = 12;
  $hoursShop = 48;
  $lagerartikel = $app->DB->SelectFirstCols(
      "SELECT a.id
      FROM `artikel` AS `a`
      LEFT JOIN (
        SELECT ao2.artikel, 1 AS `autolagerlampe`,
               MAX(ao2.storage_cache = -999) AS `cache_reseted`,
               MAX(HOUR(TIMEDIFF(NOW(), `last_storage_transfer`))) AS `last_storage_transfer_hours`
        FROM `artikel_onlineshops` AS `ao2`
        INNER JOIN `artikel` AS `art`
            ON ao2.artikel = art.id AND (art.autolagerlampe = 1 OR (ao2.autolagerlampe = 1 AND ao2.ausartikel = 0))
        WHERE ao2.aktiv = 1 AND ao2.shop IN ({$shopIdsStr})
        GROUP BY ao2.artikel
      ) AS `ao` ON a.id = ao.artikel
      WHERE (a.geloescht = 0 OR a.geloescht IS NULL)
      AND (
          a.lagerartikel = 1
          OR (a.stueckliste = 1 AND a.juststueckliste = 1)
      )
      AND (a.autolagerlampe = 1 OR ao.artikel IS NOT NULL)
      AND (a.shop > 0 OR a.shop2 > 0 OR a.shop3 > 0 OR ao.artikel IS NOT NULL)
      AND a.nummer <> 'DEL'
      AND (
          a.cache_lagerplatzinhaltmenge = -999
          OR ao.cache_reseted = 1
          OR a.laststorage_sync < a.laststorage_changed
          OR a.laststorage_sync < DATE_SUB(NOW(), INTERVAL {$hours} HOUR)
          OR (ao.last_storage_transfer_hours IS NULL OR ao.last_storage_transfer_hours > {$hoursShop})
      )
      ORDER BY a.cache_lagerplatzinhaltmenge = -999 DESC,
               ao.cache_reseted DESC,
               a.laststorage_sync"
  );

  if(empty($lagerartikel)) {
    return;
  }

  try {
    $r = new ReflectionMethod($app->erp, 'LagerSync');
    $params = $r->getParameters();
    $anzargs = count($params);
  }
  catch(Exception $e) {
    $anzargs = 2;
  }

  $clagerartikel = $lagerartikel?count($lagerartikel):0;
  $app->erp->LogFile('Artikel Gesamt fuer Synchronisation: '.$clagerartikel);
  foreach($lagerartikel as $ij => $articleId) {
    $app->DB->Update(
      "UPDATE `prozessstarter` 
      SET `mutex` = 1 , `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
      WHERE `parameter` = 'lagerzahlen' AND `aktiv` = 1"
    );
    try {
      if($anzargs > 2){
        $message .= $app->erp->LagerSync($articleId, true, $shopByIds);
      }else{
        $message .= $app->erp->LagerSync($articleId, true);
      }
    }
    catch (Exception $exception) {
      $message .= '<br>' . $exception->getMessage();
    }
    if($message!='') {
      $message .='<br>';
    }
    if($ij % 10 === 0 && method_exists($app->erp, 'canRunCronjob')
      && !$app->erp->canRunCronjob(['lagerzahlen'])) {
      $app->DB->Update(
        "UPDATE `prozessstarter` 
        SET `mutex` = 0 , `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
        WHERE `parameter` = 'lagerzahlen' AND `aktiv` = 1"
      );
      return;
    }
    usleep(10000);
  }
  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `mutex` = 0 , `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
    WHERE `parameter` = 'lagerzahlen' AND `aktiv` = 1"
  );
  if($message !='' && $erp->Firmendaten('systemmailsabschalten') == 0 && $erp->GetFirmaMail()!='') {
    try {
      if($erp->Firmendaten('systemmailsempfaenger') != ''){
        $erp->MailSend(
          $erp->GetFirmaMail(),
          $erp->GetFirmaName(),
          $erp->Firmendaten('systemmailsempfaenger'),
          'Lagerverwaltung',
          'Systemmeldung: Auto Update Lagerlampen',
          $message
        );
      }else{
        if($erp->GetFirmaBCC1() != ''){
          $erp->MailSend(
            $erp->GetFirmaMail(),
            $erp->GetFirmaName(),
            $erp->GetFirmaBCC1(),
            'Lagerverwaltung',
            'Systemmeldung: Auto Update Lagerlampen',
            $message
          );
        }
      }
    }
    catch (Exception $exception) {
      $app->erp->LogFile($app->DB->real_escape_string($exception->getMessage()));
    }
  }

  $app->erp->LogFile('Ende Synchronisation');

