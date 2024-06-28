<?php

use Xentral\Components\Logger\Logger;

/** @var Logger $logger */
$logger = $app->Container->get('Logger');

if (file_exists(dirname(__DIR__) . '/www/lib/class.erpapi_custom.php') && !class_exists('erpAPICustom')) {
    include_once dirname(__DIR__) . '/www/lib/class.erpapi_custom.php';
}
if (empty($app->Conf)) {
    $conf = new Config();
    $app->Conf = $conf;
}
if (empty($app->DB) || empty($app->DB->connection)) {
    $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, null, $app->Conf->WFdbport);
}
if (!isset($app->erp) || !$app->erp) {
    if (class_exists('erpAPICustom')) {
        $erp = new erpAPICustom($app);
    } else {
        $erp = new erpAPI($app);
    }
    $app->erp = $erp;
}
if (empty($app->remote)) {
    if (is_file(dirname(__DIR__) . '/www/lib/class.remote_custom.php')) {
        if (!class_exists('RemoteCustom')) {
            require_once dirname(__DIR__) . '/www/lib/class.remote_custom.php';
        }
        $app->remote = new RemoteCustom($app);
    } else {
        $app->remote = new Remote($app);
    }
}

$logger->debug(
    'Start'
);

$app->DB->Update(
        "UPDATE `prozessstarter` 
  SET `mutexcounter` = `mutexcounter` + 1 
  WHERE `mutex` = 1 AND (`parameter` = 'artikeluebertragen') AND `aktiv` = 1"
);

if ($app->DB->Select("SELECT `mutex` FROM `prozessstarter` WHERE (`parameter` = 'artikeluebertragen') LIMIT 1") == 1) {
    $logger->debug(
        'Läuft bereits'
    );
    return;
}

$articles = $app->DB->SelectArr(
        'SELECT `id`,`shop`,`artikel` FROM `shopexport_artikeluebertragen_check` ORDER BY `id`'
);
if (!empty($articles)) {
    /** @var Shopexport $objShopexport */
    $objShopexport = $app->loadModule('shopexport');
    if ($objShopexport !== null && method_exists($objShopexport, 'addChangedArticles')) {
        $objShopexport->addChangedArticles();
    }
}

$logger->debug(
    'Prepare',
    [
        'articles' => $articles
    ]
);

$anzChecked = [];
$anzChanged = [];
$lastids = [];
while (!empty($articles)) {
    foreach ($articles as $article) {
        if (empty($anzChanged[$article['shop']])) {
            $anzChanged[$article['shop']] = 0;
        }
        if (empty($anzChecked[$article['shop']])) {
            $anzChecked[$article['shop']] = 0;
        }
        if (!isset($lastids[$article['shop']])) {
            $lastids[$article['shop']] = (int) $app->erp->GetKonfiguration(
                            'shopexport_artikeluebertragen_check_lastid_' . $article['shop']
            );
        }
        $changed = $objShopexport->hasArticleHashChanged($article['artikel'], $article['shop']);
        if ($changed['changed']) {
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
            'shopexport_artikeluebertragen_check_changed_' . $article['shop'],
            $anzChanged[$article['shop']]
    );
    $app->erp->SetKonfigurationValue(
            'shopexport_artikeluebertragen_check_checked_' . $article['shop'],
            $anzChecked[$article['shop']]
    );
    if (method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikeluebertragen'])) {
        $logger->debug(
            '!canRunCronjob'
        );
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

/*
while ($check > 0) {
    $shopartikel = $app->DB->Query(
            "SELECT `id`,`shop`,`artikel`,`check_nr` FROM `shopexport_artikeluebertragen` ORDER BY `id` LIMIT 10"
    );
    if (!empty($shopartikel)) {
        $anz = 0;
        while ($row = $app->DB->Fetch_Assoc($shopartikel)) {
            if (!isset($lastids[$row['shop']])) {
                $lastids[$row['shop']] = (int) $app->erp->GetKonfiguration('shopexport_artikeluebertragen_check_lastid_' . $row['shop']);
            }

            $anz++;
            try {
                $app->remote->RemoteSendArticleList($row['shop'], array($row['artikel']));
                $app->erp->LagerSync($row['artikel'], true);
            } catch (Execption $exception) {
                $app->erp->LogFile($app->DB->real_escape_string($exception->getMessage()));
            }
            $app->DB->Delete(
                    sprintf(
                            'DELETE FROM `shopexport_artikeluebertragen` WHERE `id`= %d LIMIT 1',
                            $row['id']
                    )
            );

            if (!empty($row['check_nr']) && $row['check_nr'] == $lastids[$row['shop']]) {
                $transfered = 1 + (int) $app->erp->GetKonfiguration('shopexport_artikeluebertragen_check_transfered_' . $row['shop']);
                $app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_transfered_' . $row['shop'], $transfered);
            }

            $app->DB->Update(
                    "UPDATE `prozessstarter` 
        SET `letzteausfuerhung`=NOW(), `mutex` = 1,`mutexcounter`=0 
        WHERE `parameter` = 'artikeluebertragen'"
            );
        }

        $app->DB->free($shopartikel);
    }

    if (method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['artikeluebertragen'])) {
        return;
    }

    $check = $app->DB->Select('SELECT COUNT(`id`) FROM `shopexport_artikeluebertragen`');
}*/


$sql = "SELECT DISTINCT `shop`, shopexport.`bezeichnung` FROM `shopexport_artikeluebertragen` INNER JOIN shopexport ON shopexport.id = `shop`";
$shops_to_transmit = $app->DB->SelectArr($sql);

//$app->erp->LogFile('Cronjob artikeluebertragen '.(!empty($shops_to_transmit)?count($shops_to_transmit):0)." shops", print_r($shops_to_transmit, true));
$logger->debug(
  '{count} Shops',
  [
    'count' => (!empty($shops_to_transmit)?count($shops_to_transmit):0),
    'shops_to_transmit' => $shops_to_transmit
  ]
);

foreach ($shops_to_transmit as $shop_to_transmit) {
    $sql = "SELECT `artikel` FROM `shopexport_artikeluebertragen` WHERE `shop` = '".$shop_to_transmit['shop']."'";
    $articles_to_transmit = $app->DB->SelectArr($sql);
    
    $logger->debug(
        '{bezeichnung} (Shop {shop_to_transmit}) {count} Artikel',
        [
            'shop_to_transmit' => $shop_to_transmit['shop'],
            'bezeichnung' => $shop_to_transmit['bezeichnung'],
            'count' => (!empty($articles_to_transmit)?count($articles_to_transmit):0),
            'articles_to_transmit' => $articles_to_transmit
        ]
    );
    
    if (!empty($articles_to_transmit)) {   
    
        $article_ids_to_transmit = array_column($articles_to_transmit, 'artikel');
    
        try {
            $result = $app->remote->RemoteSendArticleList($shop_to_transmit['shop'], $article_ids_to_transmit); // Expected result is array $articles_to_transmit, field status contains transmission status
        } catch (Execption $exception) {
              $logger->error(
                    'Fehler {bezeichnung} (Shop {shop_to_transmit}) {count} Artikel',
                    [
                        'shop_to_transmit' => $shop_to_transmit['shop'],
                        'bezeichnung' => $shop_to_transmit['bezeichnung'],
                        'count' => (!empty($articles_to_transmit)?count($articles_to_transmit):0),
                        'exception' => $exception
                    ]
                );    
        }

        $logger->debug(
            'Ende {bezeichnung} (Shop {shop_to_transmit}) {count} Artikel',
            [
                'shop_to_transmit' => $shop_to_transmit['shop'],
                'bezeichnung' => $shop_to_transmit['bezeichnung'],
                'count' => (!empty($articles_to_transmit)?count($articles_to_transmit):0),
                'result' => $result
            ]
        );    

        // See description of return format in function class.remote.php -> RemoteSendArticleList()
        foreach ($result['articlelist'] as $article) {
            $app->DB->Delete(
                sprintf(
                    'DELETE FROM `shopexport_artikeluebertragen` WHERE `artikel`= %d AND `shop` = %d',
                    $article['artikel'],
                    $shop_to_transmit['shop']
                )
            );
            $app->erp->LagerSync($article['artikel'], true); 
        }                       
    } else {

    }   
}

$logger->debug(
    'Ende'
);

$app->DB->Update(
        "UPDATE `prozessstarter` 
  SET `letzteausfuerhung`= NOW(), `mutex` = 0,`mutexcounter`=0 
  WHERE `parameter` = 'artikeluebertragen'"
);
