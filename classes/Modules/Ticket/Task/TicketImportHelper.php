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
            return $id;
        }

        $searchByResponsePerson = 'SELECT ap.adresse FROM `ansprechpartner` AS `ap`
                                    WHERE ap.email LIKE \''.$emailAddress.'\'
                                    ORDER BY ap.id DESC';
        $id = $this->db->Select($searchByResponsePerson);
        if ($id !== null && $id > 0) {
            return $id;
        }

        $searchByContactInfo = 'SELECT ak.adresse FROM `adresse_kontakte` AS `ak`
                                 WHERE ak.kontakt LIKE \''.$emailAddress.'\' ORDER BY ak.id DESC';
        $id = $this->db->Select($searchByContactInfo);

        return $id;
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

            if ($loopCounter > 99) {
                throw new NumberGeneratorException('ticket number generation failed');
            }
            $loopCounter++;
        }
    }

    /* Function from Gateway */    
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
        $assigneeAddressId = $this->mailAccount->getAddressId();
        if ($assigneeAddressId < 1) {
            $assigneeAddressId = $this->tryGetAddressIdByEmailAddress(
                $this->mailAccount->getEmailAddress()
            );
        }
        $ticketNumber = $this->generateRandomTicketNumber();

        if($projectId < 1) {
            $projectId = $this->mailAccount->getProjectId();
        }
        if ($this->mailAccount->isTicketMarkAsFinishedEnabled()) {
            $status =  'abgeschlossen'; //TicketGateway::STATUS_COMPLETED;
        } else {
            $status = 'neu'; //TicketGateway::STATUS_NEW; 
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
                        '".$senderName."',
                        '".$senderAddress."',
                        '".'3'."',
                        '".$subject."',
                        '".$this->mailAccount->getTicketQueueId()."',
                        '".$assigneeAddressId."');";
       
        $this->db->Insert($insertTicket);
        $ticketId = $this->db->GetInsertID();

        $this->logger->debug('inserted ticket',['id' => $ticketId,'ticketnr' => $ticketNumber]);
                    
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
                '".$message."',
                '".$subject."',
                '".'email'."',
                '".$senderName."',
                '".$senderAddress."',
                '".$status."',
                '".$replyToName."',
                '".$replyToAddress."');";

            $this->logger->debug('database insert',['query' => $sql]);
            $this->db->Insert($sql);
            $messageId = $this->db->GetInsertID();

            $this->logger->debug('inserted',['id' => $messageId, 'schluessel' => $ticketNumber]);

            $this->updateTicketMessagesCount($ticketNumber);
            $this->resetTicketStatus($ticketNumber);
  //          $this->db->commit();

            return (int) $messageId;
        } catch (Throwable $e) {
//            $this->db->rollBack();

            $this->logger->error('Failed to insert ticket message into db', ['exception' => $e]);

        }
            $this->markTicketMessagesCompleted($ticketNumber);
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
            try {
                $message = $this->mailClient->fetchMessage((int)$messageNumber);
            } catch (Throwable $e) {
                $this->logger->error('Failed to fetch email from server', ['exception' => $e]);
                continue;
            }
            try {
                $this->importMessage($message);
                $insertedMailsCount++;
                if ($this->mailAccount->isDeleteAfterImportEnabled()) {
                    $this->mailClient->deleteMessage((int)$messageNumber);
                } else {
                    $this->mailClient->setFlags((int)$messageNumber, ['\\Seen']);
                }
            } catch (Throwable $e) {
                $this->logger->error('Error during email import', ['exception' => $e]);
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
     * @return void
     */
    public function importMessage(MailMessageInterface $message): void
    {
        $DEBUG = 0;

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
        $action = $this->formatter->encodeToUtf8($plainTextBody);
        $action_html = $this->formatter->encodeToUtf8($htmlBody);
        if (strlen($action_html) < strlen($action)) {
            $action_html = nl2br($action);
        }

        //check if email exists in database
        $date = $message->getDate();
        if (is_null($date)) { // This should not be happening -> Todo check getDate function
            $this->logger->debug('Null date',['subject' => $message->getSubject()]);            
            $frommd5 = md5($from . $subject);
        } else {
            $timestamp = $date->getTimestamp();
            $frommd5 = md5($from . $subject . $timestamp);
        }
        $empfang = $date->format('Y-m-d H:i:s');
        $sql = "SELECT COUNT(id) 
                        FROM `emailbackup_mails` 
                        WHERE `checksum`='$frommd5' 
                          AND `empfang`='$empfang'
                          AND `webmail`='" . $this->mailAccount->getId() . "'";


        $this->logger->debug('Importing message '.$from.' '.$fromname);

        if ($this->db->Select($sql) == 0) {

            $this->logger->debug('Importing message',['']);

            $attachments = $message->getAttachments();
            $anhang = count($attachments) > 0 ? 1 : 0;
            $mailacc = $this->mailAccount->getEmailAddress();
            if (empty($mailacc) && count($message->getRecipients()) > 0) {
                $mailacc = array_values($message->getRecipients())[0]->getEmail();
            }
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

            $id = null;
            if ($DEBUG) {
                echo $sql;
            } else {
                $this->db->InsertWithoutLog($sql);
                $id = $this->db->GetInsertID();
            }
        }
        
        if ($DEBUG) {
            echo "ticket suchen oder anlegen\n";
        }

        $ticketNumber = null;
        $ticketexists = null;
        if (preg_match("/Ticket #[0-9]{12}/i", $subject, $matches)) {
            $ticketNumber = str_replace('Ticket #', '', $matches[0]);
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

        // Add message to new or existing ticket
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

        if ($ticketnachricht > 0 && $id > 0) {
            $this->db->Update(
                "UPDATE `emailbackup_mails`
                        SET ticketnachricht='$ticketnachricht'
                        WHERE id='$id' LIMIT 1"
            );
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
        }

        // Prüfen ob Ordner vorhanden ansonsten anlegen
        $ordner = $this->config->WFuserdata . '/emailbackup/' . $this->config->WFdbname . "/$id";
        if (!is_dir($ordner) && $id > 0) {
            if (!mkdir($ordner, 0777, true) && !is_dir($ordner)) {
            }
            $raw_full_email = $message->getRawContent();
            file_put_contents($ordner . '/mail.txt', $raw_full_email);
        }

        //speichere anhang als datei
        if ($anhang == 1 && $id > 0) {
            $ordner = $this->config->WFuserdata . '/emailbackup/' . $this->config->WFdbname;
            if (!is_dir($ordner)) {
                if (!mkdir($ordner, 0777, true) && !is_dir($ordner)) {
                    $this->logger->error("Folder \"{folder}\" was not created", ['folder' => $ordner]);
                }
            }
            // Prüfen ob Ordner vorhanden ansonsten anlegen
            $ordner = $this->config->WFuserdata . '/emailbackup/' . $this->config->WFdbname . "/$id";
            if (!is_dir($ordner)) {
                if ($DEBUG) {
                    echo "mkdir $ordner\n";
                } else {
                    if (!mkdir($ordner, 0777, true) && !is_dir($ordner)) {
                        $this->logger->error("Folder \"{folder}\" was not created", ['folder' => $ordner]);
                    }
                }
            }

            $this->logger->debug('Add attachments',['ticketnummer' => $ticketNumber, 'nachricht' => $ticketnachricht, 'count' => count($attachments)]);


            foreach ($attachments as $attachment) {
                if ($attachment->getFileName() !== '') {
                    if ($DEBUG) {
                    } else {
                        $handle = fopen($ordner . '/' . $attachment->getFileName(), 'wb');
                        if ($handle) {
                            fwrite($handle, $attachment->getContent());
                            fclose($handle);
                        }
                    }
                    //Schreibe Anhänge in Datei-Tabelle
                    $datei = $ordner . '/' . $attachment->getFileName();
                    $dateiname = $attachment->getFileName();

                    $this->logger->debug("Attachment", ['filename' => $dateiname]);

                    if (stripos(strtoupper($dateiname), '=?UTF-8') !== false) {
                        $dateiname = $this->formatter->encodeToUtf8($dateiname);
                        $dateiname = htmlspecialchars_decode($dateiname);
                    }
                    if (stripos(strtoupper($dateiname), '=?ISO-8859') !== false) {
                        $dateiname = $this->formatter->encodeToUtf8($dateiname);
                        $dateiname = htmlspecialchars_decode($dateiname);
                    }

                    if ($DEBUG) {
                        echo "CreateDatei($dateiname,{$dateiname},\"\",\"\",\"datei\",\"Support Mail\",true,"
                            . $this->config->WFuserdata . "/dms/" . $this->config->WFdbname . ")\n";
                    } else {
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
                    }

                    if ($DEBUG) {
                        echo "AddDateiStichwort $tmpid,'Anhang','Ticket',$ticketnachricht,true)\n";
                    } else {

                        $this->logger->debug('Add attachment',['ticketnummer' => $ticketNumber,'id' => $tmpid, 'nachricht' => $ticketnachricht]);

                        $this->erpApi->AddDateiStichwort(
                            $tmpid,
                            'Anhang',
                            'Ticket',
                            $ticketnachricht,
                            true
                        );
                    }
                }
            }
        }      

        if (
            $this->mailAccount->isAutoresponseEnabled()
            && $this->mailAccount->getAutoresponseText() !== ''
            && (
                $this->erpApi->AutoresponderBlacklist($from) !== 1
                || $this->mailAccount->isAutoresponseLimitEnabled() === false
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
            $this->erpApi->MailSend(
                $this->mailAccount->getSenderEmailAddress(),
                '',
                $from,
                $name_sender,
                $betreff,
                $text
            );
        }
    } 
}
