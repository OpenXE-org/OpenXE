<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket\Task;

use Config as LegacyConfig;
use DB as LegacyDataBase;
use erpAPI;
use Throwable;
use Ticket as TicketModule;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Components\MailClient\Client\MailClientInterface;
use Xentral\Components\MailClient\Data\MailMessageInterface;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\Ticket\Importer\TicketFormatter;
use Xentral\Modules\Ticket\Exception\NumberGeneratorException;
use Xentral\Modules\Ticket\Exception\InvalidArgumentException;

/**
 * Utility functions for tickets cronjob for improved testability
 * This is used to create and update tickets from emailbackup, called by the tickets.php cronjob
 * It uses the TicketService to process the tickets
 */
class TicketImportHelper
{
    use LoggerAwareTrait;

    /** @var LegacyDataBase $db */
    private $db;

    /** @var erpAPI $erpApi */
    private $erpApi;

    /** @var LegacyConfig $config */
    private $config;

    /** @var TicketModule $ticketModule */
    private $ticketModule;

    /** @var TicketFormatter $formatter */
    private $formatter;

    /** @var MailClientInterface $mailClient */
    private $mailClient;

    /** @var EmailBackupAccount $mailAccount */
    private $mailAccount;

    /** @var int $projectId */
    private $projectId;

    /**
     * @param LegacyDataBase      $db
     * @param erpAPI              $erpApi
     * @param TicketModule       $ticketModule
     * @param LegacyConfig        $config
     * @param TicketFormatter     $formatter
     * @param MailClientInterface $mailClient
     * @param EmailBackupAccount  $mailAccount
     * @param int                 $projectId
     */
    public function __construct(
        LegacyDataBase $db,
        erpAPI $erpApi,
        TicketModule $ticketModule,
        LegacyConfig $config,
        TicketFormatter $formatter,
        MailClientInterface $mailClient,
        EmailBackupAccount $mailAccount,
        int $projectId
    ) {
        $this->db = $db;
        $this->erpApi = $erpApi;
        $this->ticketModule = $ticketModule;
        $this->formatter = $formatter;
        $this->mailClient = $mailClient;
        $this->mailAccount = $mailAccount;
        $this->projectId = $projectId;
        $this->config = $config;
    }

    /*  Function from AddressWrapper...
        Still using legacy db
    */
    public function tryGetAddressIdByEmailAddress(string $emailAddress): ?int
    {
        $searchByEmail = 'SELECT a.id FROM `adresse` AS `a`
                           WHERE a.email LIKE \''.$emailAddress.'\' AND a.geloescht = 0
                           ORDER BY a.id DESC';

        $id = $this->db->Select($searchByEmail);
        if ($id !== null && $id > 0) {
            return (int) $id;
        }

        $searchByResponsePerson = 'SELECT ap.adresse FROM `ansprechpartner` AS `ap`
                                    WHERE ap.email LIKE \''.$emailAddress.'\'
                                    ORDER BY ap.id DESC';
        $id = $this->db->Select($searchByResponsePerson);
        if ($id !== null && $id > 0) {
            return (int) $id;
        }

        $searchByContactInfo = 'SELECT ak.adresse FROM `adresse_kontakte` AS `ak`
                                 WHERE ak.kontakt LIKE \''.$emailAddress.'\' ORDER BY ak.id DESC';
        $id = $this->db->Select($searchByContactInfo);

        return (int) $id;
    }
    
    /* Some functions taken from TicketService (sorry...) */


    /**
     * @param string $ticketNumber
     *
     * @return void
     */
    public function markTicketMessagesCompleted(string $ticketNumber): void
    {
      $this->setTicketMessagesStatus($ticketNumber, 'abgeschlossen'); // TicketGateway::STATUS_COMPLETED);
    }

    /**
     * @param string $ticketNumber
     * @param string $status
     *
     * @return void
     */
    private function setTicketMessagesStatus(string $ticketNumber, string $status): void
    {
        $sql = "UPDATE `ticket_nachricht` SET `status` = '".$status."' WHERE `ticket` = '".$ticketNumber."';";
        $this->db->Update($sql);
    }

    /**
     * @param string $ticketNumber
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function resetTicketStatus(string $ticketNumber): void
    {
        $this->ensureTicketNumberExists($ticketNumber);
        $sql = "UPDATE `ticket` SET 
                    `status` = 'neu',
                    `zugewiesen` = '0',
                    `inbearbeitung` = '0',
                    `zeit` = now()
                    WHERE `schluessel` LIKE '".$ticketNumber."'";
        $this->db->Update($sql);
    }

    /**
     * @param string $ticketNumber
     *
     * @return void
     */
    public function updateTicketMessagesCount(string $ticketNumber): void
    {
        $this->ensureTicketNumberExists($ticketNumber);
        $count = $this->getMessageCountByTicketNumber($ticketNumber);
        $count++;
        $sql = "UPDATE `ticket` SET `nachrichten_anz` = '".$count."' WHERE schluessel = '".$tickerNumber."';";
        $this->db->Update($sql);
    }

     /**
     * @param string $ticketNumber
     *
     * @return void
     */
    private function ensureTicketNumberExists(string $ticketNumber): void
    {

        if ($ticketNumber == '') {
            throw new InvalidArgumentException(
                sprintf('ticket number empty')
            );
        }

        if (!$this->db->Select('SELECT id FROM ticket WHERE schluessel = '.$ticketNumber)) {
            throw new InvalidArgumentException(
                sprintf('ticket number "%s" does not exist', $ticketNumber)
            );
        }
    }


    /**
    * @throws NumberGeneratorException
    *
    * @return string
    */
    private function generateRandomTicketNumber(): string
    {
        $random = rand(300,700);
        $loopCounter = 0;
        while(true) {
            $candidate = sprintf('%s%04d', date('Ymd'), $random++);

/*            if (!$this->gateway->existsTicketNumber($candidate)) {
                return $candidate;
            }*/

            if (!$this->db->Select('SELECT id FROM ticket WHERE schluessel = '.$candidate)) {
                return($candidate);            
            }

            if ($loopCounter > 9999) {
                throw new NumberGeneratorException('ticket number generation failed');
            }
            $loopCounter++;
        }
    }

    /* Functions from Gateway */    
     /**
     * @param string $ticketNumber
     *
     * @return int
     */
    public function getMessageCountByTicketNumber(string $ticketNumber): int
    {
        $sql = "SELECT COUNT(tm.id) FROM `ticket_nachricht` AS `tm` WHERE tm.ticket = '".$ticketNumber."';";
        $count = $this->db->Select($sql);
        if ($count === null) {
            return 0;
        }

        return (int) $count;
    }

    /**
     * @param string $recipientMail
     * @param string $senderMail
     * @param string $senderName
     * @param string $subject
     *
     * @return array
     */
    public function getTicketRules(
        string $recipientMail,
        string $senderMail,
        string $senderName,
        string $subject
    ): array {

/*
        $sql = "SELECT
            tr.spam AS `is_spam`,
            tr.dsgvo AS `is_gdpr_relevant`,
            tr.prio AS `priority`,
            tr.persoenlich AS `is_private`,
            tr.warteschlange AS `queue_id`
        FROM `ticket_regeln` AS `tr`
        WHERE
          tr.aktiv = 1
          AND (tr.empfaenger_email LIKE :source_email OR empfaenger_email = '')
          AND (
              tr.sender_email LIKE :sender_email
              OR (tr.sender_email LIKE '@%' AND :sender_email LIKE CONCAT('%', tr.sender_email))
              OR tr.sender_email = ''
          )
          AND (tr.name LIKE :sender_name OR tr.name = '')
          AND (tr.betreff LIKE :subject OR tr.betreff = '')";
        $values = [
            'source_email' => $recipientMail,
            'sender_email' => $senderMail,
            'sender_name'  => $senderName,
            'subject'      => $subject,
        ];

        return $this->db->fetchAll($sql, $values);

*/

        // Legacy DB implementation:

        $sql = "SELECT
            tr.id,
            tr.spam AS `is_spam`,
            tr.dsgvo AS `is_gdpr_relevant`,
            tr.prio AS `priority`,
            tr.persoenlich AS `is_private`,
            tr.adresse,
            tr.warteschlange AS `queue_id`
        FROM `ticket_regeln` AS `tr`
        WHERE
          tr.aktiv = 1
          AND ('".$this->db->real_escape_string($recipientMail)."' LIKE tr.empfaenger_email OR tr.empfaenger_email = '')
          AND ('".$this->db->real_escape_string($senderMail)."' LIKE tr.sender_email OR tr.sender_email = '')
          AND ('".$this->db->real_escape_string($senderMail)."' LIKE tr.name OR tr.name = '')
          AND ('".$this->db->real_escape_string($subject)."' LIKE tr.betreff OR tr.betreff = '')";

         $this->logger->debug('ticket rule',['sql' => $sql]);

         $result = $this->db->SelectArr($sql);

        if ($result != null) {
          $this->logger->debug('ticket rules',['count',count($result)]);         
          return ($result);
        } else {
         $this->logger->debug('no ticket rules applicable',['']);         
          return(array());
        }
    }

    /* END Functions from Gateway */    

    public function createTicket(
        int $projectId,
        string $senderName,
        string $senderAddress,
        string $subject,
        int $timestamp,
        string $replyToName,
        string $replyToAddress
    ): string
    {
        $AddressId = $this->tryGetAddressIdByEmailAddress($senderAddress);
 
        $ticketNumber = $this->generateRandomTicketNumber();

        if($projectId < 1) {
            $projectId = $this->mailAccount->getProjectId();
        }
        if ($this->mailAccount->isTicketMarkAsFinishedEnabled()) {
            $status =  'abgeschlossen'; //TicketGateway::STATUS_COMPLETED;
        } else {
            $status = 'neu'; //TicketGateway::STATUS_NEW; 
        }

        $queue_id = $this->mailAccount->getTicketQueueId();
        
        if (!empty($queue_id)) {
          $queue_label = $this->db->Select("SELECT label FROM warteschlangen WHERE label = '".$queue_id."' LIMIT 1");
        }
 
        $insertTicket = "INSERT INTO `ticket` (
                      `schluessel`, `zeit`, `projekt`, `quelle`, `status`, `kunde`,
                      `mailadresse`, `prio`, `betreff`,`warteschlange`,`adresse`
                      ) VALUES (
                        '".$ticketNumber."', 
                        '".date('Y-m-d H:i:s', $timestamp)."',
                        '".$projectId."',
                        '".$this->mailAccount->getEmailAddress()."',
                        '".$status."',
                        '".$this->db->real_escape_string($senderName)."',
                        '".$this->db->real_escape_string($senderAddress)."',
                        '".'3'."',
                        '".$this->db->real_escape_string($subject)."',
                        '".$queue_label."',
                        '".$AddressId."');";
       
        $this->db->Insert($insertTicket);
        $ticketId = $this->db->GetInsertID();

        $this->logger->debug('inserted ticket',['id' => $ticketId,'ticketnr' => $ticketNumber, 'projekt' => $projectId, 'warteschlange' => $this->mailAccount->getTicketQueueId(), 'adresse' => $AddressId]);
                    
        //  todo als rueckgabe ticketnachricht
        return $ticketNumber;
    }
    
  public function addTicketMessage(
        string $ticketNumber,
        int $timestamp,
        string $message,
        string $subject,
        string $senderName,
        string $senderAddress,
        string $status,
        string $replyToName,
        string $replyToAddress
    ): int
    {
//        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO `ticket_nachricht` (
                        `ticket`, `zeit`, `text`, `betreff`, `medium`,
                        `verfasser`, `mail`,`status`, `verfasser_replyto`, `mail_replyto`
                    ) VALUES (
                '".$ticketNumber."',
                '".date('Y-m-d H:i:s', $timestamp)."',
                '".$this->db->real_escape_string($message)."',
                '".$this->db->real_escape_string($subject)."',
                '".'email'."',
                '".$this->db->real_escape_string($senderName)."',
                '".$this->db->real_escape_string($senderAddress)."',
                '".$status."',
                '".$this->db->real_escape_string($replyToName)."',
                '".$this->db->real_escape_string($replyToAddress)."');";

            $this->logger->debug('database insert',['query' => $sql]);
            $this->db->Insert($sql);
            $messageId = $this->db->GetInsertID();

            $this->logger->debug('inserted',['id' => $messageId, 'schluessel' => $ticketNumber]);
            $this->updateTicketMessagesCount($ticketNumber);
            $this->resetTicketStatus($ticketNumber);
  //          $this->db->commit();
            $result = $messageId;
            $this->markTicketMessagesCompleted($ticketNumber);        
        } catch (Throwable $e) {
//            $this->db->rollBack();
            $result = 0;
            $this->logger->error('Failed to insert ticket message into db', ['exception' => $e]);
        }

        return($result);
    }

    public function applyTicketRules(int $ticketMessageId): void
    {
/*        $ticketData = $this->gateway->tryGetTicketDataByByMessage($ticketMessageId); */

        $ticketData = $this->db->SelectArr("SELECT t.id, tn.mail as sender_email, tn.verfasser as sender_name, tn.betreff as subject, t.quelle as source_email FROM ticket t INNER JOIN ticket_nachricht tn ON tn.ticket = t.schluessel WHERE tn.id = '".$ticketMessageId."'")[0];

        if ($ticketData === null) {
            throw new InvalidArgumentException('cannot find ticket by message id');
        }

        $ticketId = $ticketData['id'];
        $senderMail = $ticketData['sender_email'];
        $senderName = $ticketData['sender_name'];
        $subject = $ticketData['subject'];
        $source = $ticketData['source_email'];

        $this->logger->debug('check ticket rules',['tn_id' => $ticketMessageId, 'sender_email' => $senderMail, 'subject' => $subject]);

        //TODO: richtig loggen: $this->app->erp->LogFile("Empfaengermail: $quelle Sendermail: $mailadresse Kunde: $kunde Betreff: $betreff");
        $ruleArray = $this->getTicketRules($source, $senderMail, $senderName, $subject);
        if (empty($ruleArray)) {
            return;
        }

        foreach ($ruleArray as $rule) {

            $this->logger->debug('ticket rule applies',['rule_id' => $rule['id'],'rule' => print_r($rule,true)]);

/*
            $update = $this->db->update();
            $update->table('ticket');
            if ($rule['is_spam'] === 1) {
                $update->set('inbearbeitung', 0);
                $update->set('zugewiesen', 1);
                $this->db->perform(
                    'UPDATE `ticket_nachricht` SET `status` = :status WHERE `ticket` = :ticket_id',
                    ['status' => TicketGateway::STATUS_SPAM, 'ticket_id' => $ticketId]
                );
            }
            $update->set('dsgvo', $rule['is_gdpr_relevant']);
            $update->set('privat', $rule['is_private'] );
            $update->set('prio', $rule['priority'] );
            $update->where('id = :ticket_id');
            $sql = $update->getStatement();
            $this->db->perform($sql, ['ticket_id' => $ticketId]);
*/

            if ($rule['is_spam'] == 1) {
                $status = 'spam';
            } else {
                $status = 'neu';
            }

            $sql = "UPDATE `ticket` SET `dsgvo` = '".$rule['is_gdpr_relevant']."', `privat` = '".$rule['is_private']."', `prio` = '".$rule['priority']."',`adresse` = '".$rule['adresse']."', `warteschlange` = '".$rule['queue_id']."', `status` = '".$status."' WHERE `id` = '".$ticketId."'";

            $this->logger->debug('ticket rule sql',['sql' => $sql]);

            $this->db->Update($sql);
        }
    }


    /* End TicketService */   

    /**
     * @param array $inboxMessageIds
     *
     * @return int amount of imported tickets
     */
    public function importMessages(array $inboxMessageIds): int
    {
        $insertedMailsCount = 0;
        foreach ($inboxMessageIds as $messageNumber) {

            $this->logger->debug("Fetch $messageNumber", ['']);

            try {
                $message = $this->mailClient->fetchMessage((int)$messageNumber);
            } catch (Throwable $e) {
                $this->logger->error('Failed to fetch email from server', ['exception' => $e]);
                continue;
            }
            try {

//                $this->logger->debug('Start import', ['message' => substr(print_r($message,true),1000)]);
                $this->logger->debug('Start import '.$messageNumber, []);

                $result = $this->importMessage($message);               

                if ($result === true) {
                    $insertedMailsCount++;
                    if ($this->mailAccount->isDeleteAfterImportEnabled()) {
                        $this->mailClient->deleteMessage((int)$messageNumber);
                    } else {
                        $this->mailClient->setFlags((int)$messageNumber, ['\\Seen']);
                    }
                } else {
                    $this->logger->error('Error during email import '.$messageNumber, ['message' => substr(print_r($message,true),0,1000)]);
                    continue;
                }
            } catch (Throwable $e) {

                $exception_message = $e->getMessage();

                $this->logger->error('Error during email import '.$messageNumber, ['exc-message' => $exception_message ,'message2' => substr(print_r($message,true),0,1000)]);
                continue;
            }
        }

        $this->logger->debug(
            '{imported_count} of {total} emails imported successfully',
            ['imported_count' => $insertedMailsCount, 'total' => count($inboxMessageIds)]
        );

        return $insertedMailsCount;
    }

    /**
     * @param MailMessageInterface $message
     *
     * @return true on success
     */
    public function importMessage(MailMessageInterface $message): bool
    {
        // extract email data
        $subject = $this->formatter->encodeToUtf8($message->getSubject());
        $from = $this->formatter->encodeToUtf8($message->getSender()->getEmail());
        $fromname = $this->formatter->encodeToUtf8($message->getSender()->getName());
        $ccs = $message->getCcRecipients();
        $cc_recv = [];
        foreach ($ccs as $cc) {
            $cc_recv[] = [
                'email' => $this->formatter->encodeToUtf8($cc->getEmail()),
                'name'  => $this->formatter->encodeToUtf8($cc->getName()),
            ];
        }
        $plainTextBody = $message->getPlainTextBody();
        if ($plainTextBody === null) {
            $plainTextBody = '';
        }
        $htmlBody = $message->getHtmlBody();
        if ($htmlBody === null) {
            $htmlBody = '';
        }        

        if ($plainTextBody == '' && $htmlBody == '') {
            $this->logger->debug('Empty mail',[]);
        }

        $this->logger->debug('Text',['plain' => $plainTextBody, 'html' => $htmlBody, 'simple_content' => $simple_content]);            

        $action = $this->formatter->encodeToUtf8($plainTextBody);
        $action_html = $this->formatter->encodeToUtf8($htmlBody);
        if (strlen($action_html) < strlen($action)) {
            $action_html = nl2br($action);
        }

        $this->logger->debug('Text (converted)',['plain' => $action, 'html' => $action_html]);

        // Import database emailbackup
        try {
            $date = $message->getDate();
        }
        catch (exception $e) {
            $this->logger->debug('Invalid date',['exc-message' => $e->getMessage(),'subject' => $message->getSubject(), $message->getHeader('date')->getValue()]);            
            return(false);
        }

        $timestamp = $date->getTimestamp();
        $frommd5 = md5($from . $subject . $timestamp);
   
        $empfang = $date->format('Y-m-d H:i:s');
        $sql = "SELECT COUNT(id) 
                        FROM `emailbackup_mails` 
                        WHERE `checksum`='$frommd5' 
                          AND `empfang`='$empfang'
                          AND `ticketnachricht` != 0
                          AND `webmail`='" . $this->mailAccount->getId() . "'";

        $this->logger->debug('Importing message '.$from.' '.$fromname);

        $result = $this->db->Select($sql);
        $emailbackup_mails_id = null;

        if ($result == 0) {

//            $this->logger->debug('Importing message',['message' => substr(print_r($message,true),1000)]);
            $this->logger->debug('Importing message attachments',[]);

            try {   
                $attachments = $message->getAttachments();
            }
            catch (Throwable $e) { 
                $this->logger->error('Error while getting attachments',['exception' => $e]);            
                return(false);
            }

            $anhang = count($attachments) > 0 ? 1 : 0;
            $mailacc = $this->mailAccount->getEmailAddress();
            $mailaccid = $this->mailAccount->getId();

            if (!$this->erpApi->isMailAdr($from)) {
                $from = $this->erpApi->filterMailAdr($from);
            }

            //fuege gegenenfalls ein
            $sql = "INSERT INTO `emailbackup_mails` 
                            (
                                `webmail`,
                                `subject`,
                                `sender`,
                                `action`,
                                `action_html`,
                                `empfang`,
                                `anhang`,
                                `checksum`
                            ) VALUES (
                                '$mailaccid',
                                '" . $this->db->real_escape_string($subject) . "',
                                '" . $this->db->real_escape_string($from) . "',
                                '" . $this->db->real_escape_string($action) . "',
                                '" . $this->db->real_escape_string($action_html) . "',
                                '$empfang','$anhang','$frommd5'
                            )";

            $this->db->InsertWithoutLog($sql);
            $emailbackup_mails_id = $this->db->GetInsertID();
        } else {
            $this->logger->debug('Message already imported.',['']);
            return(true);
        }     

        $this->logger->debug('Message emailbackup_mails imported.',['id' => $emailbackup_mails_id]);
        // END database import emailbackup
              
        // Find ticket and add or create new ticket
        $ticketNumber = null;
        $ticketexists = null;
        if (preg_match("/Ticket #[0-9]{12}/i", $subject, $matches)) {
            $ticketNumber = str_replace('Ticket #', '', $matches[0]);

            $this->logger->debug('Check for number',['ticketnummer' => $ticketNumber]);

            $ticketexists = $this->db->Select(
                "SELECT schluessel 
                             FROM ticket 
                             WHERE schluessel LIKE '" . $ticketNumber . "' 
                             AND schluessel!='' LIMIT 1"
            );
        }
        $ticketnachricht = null;

        if (!$ticketexists) {

            $this->logger->debug('New ticket',['']);

            $ticketNumber = $this->createTicket(        
                $this->projectId,               
                $fromname,
                $from,
                $subject,
                $timestamp,
                $fromname,
                $from
            );

        } else {
            $this->logger->debug('Add message to existing ticket',['ticketnummer' => $ticketNumber]);
        }

        // Database import ticket: Add message to new or existing ticket
        $ticketnachricht = $this->addTicketMessage(
            (string) $ticketNumber,
            $timestamp,
            $action_html, //?
            $subject,
            $fromname,
            $from,
            'neu',
            $fromname,
            $from
        );

        // Only for new tickets: apply filter rules        
        if (!$ticketexists) {        
            $this->applyTicketRules($ticketnachricht);
        }


        if ($ticketnachricht > 0 && $emailbackup_mails_id > 0) {
            $this->db->Update(
                "UPDATE `emailbackup_mails`
                        SET ticketnachricht='$ticketnachricht'
                        WHERE id='$emailbackup_mails_id' LIMIT 1"
            );


            // Add all the ccs to the header table
            if (is_array($cc_recv)) {
                foreach ($cc_recv as $mail) {
                    if ($mail['name'] != '') {
                        $cc_value =
                            $this->db->real_escape_string($mail['name'])
                            . ' <'
                            . $this->db->real_escape_string($mail['email'])
                            . ">";
                    } else {
                        $cc_value = $this->db->real_escape_string($mail['email']);
                    }
                    if ($cc_value != '') {
                        $sql = "INSERT INTO ticket_header
                                        (`id`,`ticket_nachricht`,`type`,`value`)
                                        VALUES
                                        ('', '$ticketnachricht', 'cc', '" . $cc_value . "')";
                        $this->db->InsertWithoutLog($sql);
                    }
                }
                $cc_recv = [];
            }

            $this->logger->debug('Add recipients to header',['count' => count($message->getRecipients())]);

            // Add all the recipients to the header table
            if (count($message->getRecipients()) > 0) {

                foreach ($message->getRecipients() as $recipient) {
                    $recipient_address = $this->db->real_escape_string($recipient->getEmail());

                    $this->logger->debug('Add recipient to header',['address' => $recipient_address]);

                    if ($recipient_address != '') {
                        $sql = "INSERT INTO ticket_header
                                        (`id`,`ticket_nachricht`,`type`,`value`)
                                        VALUES
                                        ('', '$ticketnachricht', 'to', '" . $recipient_address . "')";
                        $this->db->InsertWithoutLog($sql);
                    }
                }
            }
        } else {
            $this->logger->error("Message not imported!", ['Time' => $timestamp, 'Subject' => $subject, 'From' => $from]);
            $this->db->Delete("DELETE FROM emailbackup_mails WHERE id = ".$emailbackup_mails_id);
            return(false);
        }
        // END database import ticket
  
        // File management folder with raw text
        $ordner = $this->config->WFuserdata . '/emailbackup/' . $this->config->WFdbname . "/$emailbackup_mails_id";
        if (!is_dir($ordner) && $emailbackup_mails_id > 0) {
            if (!mkdir($ordner, 0777, true) && !is_dir($ordner)) {
                $this->logger->error("Folder \"{folder}\" was not created", ['folder' => $ordner]);
                $this->db->Delete("DELETE FROM emailbackup_mails WHERE id = ".$emailbackup_mails_id);
                return(false);
            }
            $raw_full_email = $message->getRawContent();
            file_put_contents($ordner . '/mail.txt', $raw_full_email);
        }

        // File management attachments
        if ($anhang == 1 && $emailbackup_mails_id > 0) {
            $ordner = $this->config->WFuserdata . '/emailbackup/' . $this->config->WFdbname;
            if (!is_dir($ordner)) {
                if (!mkdir($ordner, 0777, true) && !is_dir($ordner)) {
                    $this->logger->error("Folder \"{folder}\" was not created", ['folder' => $ordner]);
                    $this->db->Delete("DELETE FROM emailbackup_mails WHERE id = ".$emailbackup_mails_id);
                    return(false);
                }
            }
            // Prüfen ob Ordner vorhanden ansonsten anlegen
            $ordner = $this->config->WFuserdata . '/emailbackup/' . $this->config->WFdbname . "/$emailbackup_mails_id";
            if (!is_dir($ordner)) {
                if (!mkdir($ordner, 0777, true) && !is_dir($ordner)) {
                    $this->logger->error("Folder \"{folder}\" was not created", ['folder' => $ordner]);
                    $this->db->Delete("DELETE FROM emailbackup_mails WHERE id = ".$emailbackup_mails_id);
                    return(false);
                }
            }

            $this->logger->debug('Add '.count($attachments).' attachments',['']); 

            foreach ($attachments as $attachment) {

                $dateiname = $attachment->getFileName();
                $dateiname = str_replace(array('\\','/',':','*','?','"','<','>','|'),' ',$dateiname); // Remove problematic characters

                if ($dateiname !== '') {
                    $handle = fopen($ordner . '/' . $dateiname, 'wb');
                    if ($handle) {
                        fwrite($handle, $attachment->getContent());
                        fclose($handle);
                    }
                    //Schreibe Anhänge in Datei-Tabelle
                    $datei = $ordner . '/' . $dateiname;

                    if (stripos(strtoupper($dateiname), '=?UTF-8') !== false) {
                        $dateiname = $this->formatter->encodeToUtf8($dateiname);
                        $dateiname = htmlspecialchars_decode($dateiname);
                    }
                    if (stripos(strtoupper($dateiname), '=?ISO-8859') !== false) {
                        $dateiname = $this->formatter->encodeToUtf8($dateiname);
                        $dateiname = htmlspecialchars_decode($dateiname);
                    }
                    if (stripos(strtoupper($dateiname), 'UTF-8\'\'') === 0) {
                        $dateiname = $this->formatter->encodeToUtf8(urldecode(substr($dateiname,7)));
                        $dateiname = htmlspecialchars_decode($dateiname);
                    }

                    $tmpid = $this->erpApi->CreateDatei(
                        $dateiname,
                        $dateiname,
                        '',
                        '',
                        $datei,
                        'Support Mail',
                        true,
                        $this->config->WFuserdata . '/dms/' . $this->config->WFdbname
                    );

                    $this->logger->debug('Add attachment',['filename' => $dateiname, 'ticketnummer' => $ticketNumber,'id' => $tmpid, 'nachricht' => $ticketnachricht]);

                    $this->erpApi->AddDateiStichwort(
                        $tmpid,
                        'Anhang',
                        'Ticket',
                        $ticketnachricht,
                        true
                    );
                }
            }
        } // END File management  

        // Autoresponder
        if (
            $this->mailAccount->isAutoresponseEnabled()
            && $this->mailAccount->getAutoresponseText() !== ''
            && !(
                 $this->CheckAutoresponderBlacklist($from) &&
                 $this->mailAccount->isAutoresponseLimitEnabled()
            )
        ) {

            $text = $this->mailAccount->getAutoresponseText();
            $betreff = $this->mailAccount->getAutoresponseSubject();

            if (empty($text)) $text = '';
            if (empty($betreff)) $betreff = '';

            $text = str_replace('{TICKET}', $ticketNumber, $text);
            $text = str_replace('{BETREFF}', $subject, $text);
            $betreff = str_replace('{TICKET}', $ticketNumber, $betreff);
            $betreff = str_replace('{BETREFF}', $subject, $betreff);

            if (!$this->erpApi->isHTML($text)) {
                $text = str_replace("\r\n", '<br>', $text);
            }
                       
            $this->SetAutoresponderBlacklist($from);
                                  
            $this->erpApi->MailSend(
                $this->mailAccount->getSenderEmailAddress(),
                $this->mailAccount->getSenderName(),
                $from,
                $from,
                $betreff,
                $text
            );                                   
        }
        return(true);
    } 
    
    // Check if the given address has already been autoresponded to
    // True if blocked
    function CheckAutoresponderBlacklist(string $mailaddress) : bool {
        $blocked = $this->db->Select("SELECT * FROM autoresponder_blacklist WHERE mailaddress = '".$mailaddress."' AND cachetime > DATE_ADD(CURRENT_TIMESTAMP, INTERVAL -24 HOUR)");
        
        $this->logger->debug('Blacklist',['address' => $mailaddress, 'result' => $blocked]);
        
        return(!empty($blocked));
    }
    
    function SetAutoresponderBlacklist(string $mailaddress) {
        $this->db->Insert("INSERT INTO autoresponder_blacklist (mailaddress) VALUES ('".$mailaddress."')");
        $this->db->Delete("DELETE FROM autoresponder_blacklist WHERE cachetime < DATE_ADD(CURRENT_TIMESTAMP, INTERVAL -24 HOUR)");
    }
    
    
}
