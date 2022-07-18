<?php

/*
* Fetch all mails for accounts with ticket function
* Create tickets or sort mails to existing tickets
*/

use Xentral\Components\Logger\Logger;
use Xentral\Components\MailClient\MailClientFactory;
use Xentral\Modules\SystemMailClient\MailClientConfigProvider;
use Xentral\Modules\SystemMailer\Service\EmailAccountGateway;
use Xentral\Modules\Ticket\Importer\TicketFormatter;
use Xentral\Modules\Ticket\Task\TicketImportHelperFactory;

$DEBUG = 0;


$debugfile = "/var/www/html/Xenomporio/debug.txt";

function file_append($filename,$text) {
  $oldtext = file_get_contents($filename);
  file_put_contents($filename,$oldtext.$text);
}

file_put_contents($debugfile,"0");

/** @var ApplicationCore $app */

$erp = $app->erp;
$conf = $app->Conf;

/** @var Logger $logger */
$logger = $app->Container->get('Logger');

$cronjobname = 'tickets';
/*
$mutex = $app->DB->Select(
  "SELECT MAX(`mutex`) FROM `prozessstarter` WHERE (`parameter` = '".$cronjobname."')"
);
if($mutex){
  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `mutexcounter`=`mutexcounter`+1 
    WHERE `mutex` = 1 AND (`parameter` = '".$cronjobname."')"
  );

  file_append($debugfile,"MUTEX");

  return;
}
$app->DB->Update(
  "UPDATE `prozessstarter` SET `mutex`='1', `mutexcounter` = 0 WHERE (`parameter` = '".$cronjobname."')"
);
*/

// get all email Accounts that have the ticket system active
/** @var EmailAccountGateway $accountGateway */
$accountGateway = $app->Container->get('EmailAccountGateway');
$accounts = $accountGateway->getAccountsWithTicketActive();

  file_append($debugfile,"Accs:".count($accounts).";");


// only load services if there is at least one account to import (performance)
$ticketModule = null;
$factory = null;
$configProvider = null;
$formatHelper = null;
$importHelperFactory = null;
if(!empty($accounts)){
  /** @var Ticket $ticketModule */
  $ticketModule = $app->erp->LoadModul('ticket');
  /** @var MailClientFactory $factory */
  $factory = $app->Container->get('MailClientFactory');
  /** @var MailClientConfigProvider $configProvider */
  $configProvider = $app->Container->get('MailClientConfigProvider');
  /** @var TicketFormatter $formatHelper */
  $formatHelper = $app->Container->get('TicketFormatter');
  /** @var TicketImportHelperFactory $importHelperFactory */
  $importHelperFactory = $app->Container->get('TicketImportHelperFactory');
}

$totalEmailsImportCount = 0;
foreach ($accounts as $account) {
  $logger->debug(
      'Start imap ticket import for {email}',
      ['email' => $account->getEmailAddress(), 'account' => $account]
  );

  file_append($debugfile,"Account ".$account->getemailAddress());

  // create mail client
  try {
    $mailConfig = $configProvider->createImapConfigFromAccount($account);
    $mailClient = $factory->createImapClient($mailConfig);
  } catch (Exception $e) {
    $logger->error('Failed to create email client', ['error' => (string)$e, 'account' => $account]);

  file_append($debugfile,"Failed 1");

    continue;
  }

  file_append($debugfile,"Connect to ".."SSL: ".$configProvider->isSslEnabled()." auth ".getAuthType()."\n");

  // connect mail client
  try {
    try {
      $mailClient->connect();

      file_append($debugfile,"Meh");

    } catch (Exception $e) {
      $logger->error('Error during imap connection', ['error' => (string)$e, 'account' => $account]);

      file_append($debugfile,"Error ".(string)$e);
   
      continue;
    } 
  }

  file_append($debugfile,"2");

  // connet to INBOX folder
  try {
    $mailClient->selectFolder('INBOX');
  } catch (Exception $e) {
    $logger->error('Failed to select INBOX folder', ['error' => (string)$e, 'account' => $account]);


  file_append($debugfile,"Failed 2");


    continue;
  }

  $projectId = $account->getProjectId() > 0 ? $account->getProjectId() : 1;
  $delete_msg = 0;
  $daysold = $account->getBackupDeleteAfterDays();

  // determine search criteria for new messages
  $datet = '2012-12-24';
  if ($account->getImportStartDateAsString() !== '0000-00-00') {
    $datesince = date('d-M-Y', strtotime($account->getImportStartDateAsString()));
    $criteria = 'UNSEEN SINCE ' . $datesince;
  } else {
    $criteria = 'UNSEEN';
  }

  file_append($debugfile,"3");

  // search new messages
  try {
    $searchResult = $mailClient->searchMessages($criteria);
  } catch (Exception $e) {
    $logger->error('Error during imap search', ['exception' => $e]);

  file_append($debugfile,"Failed 3");


    continue;
  }
  $logger->debug('unread emails to import: {message_count}', ['message_count' => count($searchResult)]);

  // set mutex if there is more than 5 emails to import
  if (count($searchResult) > 5) {
      $app->DB->Update(
        "UPDATE `prozessstarter` 
        SET `mutex`=1, `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
        WHERE (`parameter` = '".$cronjobname."')"
      );
  }
  $importer = $importHelperFactory->create($mailClient, $account, $projectId);
  $insertedMailsCount = $importer->importMessages($searchResult);
  $totalEmailsImportCount += $insertedMailsCount;

  // set mutex if the total amount of imported emails is more than 10
  if ($totalEmailsImportCount > 10) {
      $app->DB->Update(
          "UPDATE `prozessstarter` 
      SET `mutex`=1, `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
      WHERE (`parameter` = '".$cronjobname."')"
      );
  }

  $mailClient->expunge();
  $mailClient->disconnect();

  if (
      method_exists($app->erp, 'canRunCronjob')
      && !$app->erp->canRunCronjob(['supportmails', 'tickets'])
  ) {

    $logger->error('Tickets error');

  file_append($debugfile,"Failed 5");


    return;
  }
  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `mutex`=1, `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
    WHERE (`parameter` = '".$cronjobname."')"
  );
}

$app->DB->Update(
  "UPDATE `prozessstarter` SET `mutex`=0,`mutexcounter`=0 WHERE (`parameter` = '".$cronjobname."')"
);

file_append($debugfile,"END");
