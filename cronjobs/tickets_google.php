<?php

use Xentral\Components\Logger\Logger;
use Xentral\Components\MailClient\MailClientFactory;
use Xentral\Modules\GoogleApi\Exception\GoogleApiExceptionInterface;
use Xentral\Modules\SystemMailClient\MailClientConfigProvider;
use Xentral\Modules\SystemMailer\Service\EmailAccountGateway;
use Xentral\Modules\Ticket\Importer\TicketFormatter;
use Xentral\Modules\Ticket\Task\TicketImportHelperFactory;

$DEBUG = 0;

/** @var ApplicationCore $app */

$erp = $app->erp;
$conf = $app->Conf;

/** @var Logger $logger */
$logger = $app->Container->get('Logger');

$mutex = $app->DB->Select(
  "SELECT MAX(`mutex`) FROM `prozessstarter` WHERE (`parameter` = 'tickets_google')"
);
if($mutex){
  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `mutexcounter`=`mutexcounter`+1 
    WHERE `mutex` = 1 AND (`parameter` = 'tickets_google')"
  );
  return;
}
$app->DB->Update(
  "UPDATE `prozessstarter` SET `mutex`='1', `mutexcounter` = 0 WHERE (`parameter` = 'tickets_google')"
);

// get all email Accounts that have the ticket system active
/** @var EmailAccountGateway $accountGateway */
$accountGateway = $app->Container->get('EmailAccountGateway');
$allAccounts = $accountGateway->getAccountsWithTicketActive();
// filter to only process google accounts
$accounts = [];
foreach ($allAccounts as $account) {
  if($account->getImapType() === 5){
    $accounts[] = $account;
  }
}

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

  // create mail client
  try {
    $mailConfig = $configProvider->createImapConfigFromAccount($account);
    $mailClient = $factory->createImapClient($mailConfig);
  } catch (Exception $e) {
    $logger->error('Failed to create email client', ['error' => (string)$e, 'account' => $account]);
    continue;
  }

  // connect mail client
  try {
    $mailClient->connect();
  } catch (GoogleApiExceptionInterface $e) {
    $logger->error(
        'Error during imap connection - access to emails not authorized by Google user {user_name} <{user_email}>',
        [
            'user_email' => $account->getEmailAddress(),
            'user_name' => $account->getSenderName(),
            'error' => (string)$e
        ]
    );
  } catch (Exception $e) {
    $logger->error('Error during imap connection', ['error' => (string)$e, 'account' => $account]);
    continue;
  }

  // connet to INBOX folder
  try {
    $mailClient->selectFolder('INBOX');
  } catch (Exception $e) {
    $logger->error('Failed to select INBOX folder', ['error' => (string)$e, 'account' => $account]);
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

  // search new messages
  try {
    $searchResult = $mailClient->searchMessages($criteria);
  } catch (Exception $e) {
    $logger->error('Error during imap search', ['exception' => $e]);
    continue;
  }
  $logger->debug('unread emails to import: {message_count}', ['message_count' => count($searchResult)]);

  // set mutex if there is more than 5 emails to import
  if (count($searchResult) > 5) {
      $app->DB->Update(
        "UPDATE `prozessstarter` 
        SET `mutex`=1, `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
        WHERE (`parameter` = 'tickets_google')"
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
      WHERE (`parameter` = 'tickets_google')"
      );
  }

  $mailClient->expunge();
  $mailClient->disconnect();

  if (
      method_exists($app->erp, 'canRunCronjob')
      && !$app->erp->canRunCronjob(['supportmails', 'tickets_google'])
  ) {
    return;
  }
  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `mutex`=1, `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
    WHERE (`parameter` = 'tickets_google')"
  );
}

$app->DB->Update(
  "UPDATE `prozessstarter` SET `mutex`=0,`mutexcounter`=0 WHERE (`parameter` = 'tickets_google')"
);
