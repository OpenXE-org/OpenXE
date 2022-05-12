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

/**
 * Utility functions for tickets_google cronjob for improved testability
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
     * @param TicketModule        $ticketModule
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
        $name_sender = $this->formatter->encodeToUtf8($message->getSender()->getName());
        $mail_replyto = $this->formatter->encodeToUtf8($message->getReplyToAddress());
        $verfasser_replyto = $this->formatter->encodeToUtf8($message->getSender()->getName());

        //check if email exists in database
        $date = $message->getDate();
        $timestamp = $date->getTimestamp();
        $frommd5 = md5($from . $subject . $timestamp);
        $empfang = $date->format('Y-m-d H:i:s');
        $sql = "SELECT COUNT(id) 
                        FROM `emailbackup_mails` 
                        WHERE `checksum`='$frommd5' 
                          AND `empfang`='$empfang'
                          AND `webmail`='" . $this->mailAccount->getId() . "'";

        if ($this->db->Select($sql) == 0) {
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

            if ($DEBUG) {
                echo "ticket suchen oder anlegen\n";
            }

            $_schluessel = null;
            $schluessel = null;
            $ticketexits = null;
            if (preg_match("/Ticket #[0-9]{12}/i", $subject, $matches)) {
                $schluessel = str_replace('Ticket #', '', $matches[0]);
                $ticketexits = $this->db->Select(
                    "SELECT schluessel 
                                 FROM ticket 
                                 WHERE schluessel LIKE '" . $schluessel . "' 
                                 AND schluessel!='' LIMIT 1"
                );
            }
            $ticketnachricht = null;
            if ($ticketexits) {
                if ($DEBUG) {
                    echo "ticket nummer in betreff gefunden\n";
                }

                $schluessel = str_replace('Ticket #', '', $matches[0]);

                if ($action_html != '') {
                    $sql = "INSERT INTO `ticket_nachricht` 
                                    (
                                     `id`,
                                     `ticket`,
                                     `zeit`,
                                     `text`,
                                     `betreff`,
                                     `medium`,
                                     `verfasser`,
                                     `mail`,
                                     `status`,
                                     `verfasser_replyto`,
                                     `mail_replyto`
                                     ) VALUES (
                                        NULL,
                                        '$schluessel',
                                        FROM_UNIXTIME($timestamp),
                                        '" . $this->db->real_escape_string($action_html) . "',
                                        '" . $this->db->real_escape_string($subject) . "',
                                        'email',
                                        '" . $this->db->real_escape_string($name_sender) . "',
                                        '" . $this->db->real_escape_string($from) . "',
                                        'neu',
                                        '" . $this->db->real_escape_string($verfasser_replyto) . "',
                                        '" . $this->db->real_escape_string($mail_replyto) . "'
                                     );";
                } else {
                    $sql = "INSERT INTO `ticket_nachricht` 
                                    (
                                     `id`,
                                     `ticket`,
                                     `zeit`,
                                     `text`,
                                     `betreff`,
                                     `medium`,
                                     `verfasser`,
                                     `mail`,
                                     `status`,
                                     `verfasser_replyto`,
                                     `mail_replyto`
                                     ) VALUES (
                                       NULL,
                                       '$schluessel',
                                       FROM_UNIXTIME($timestamp),
                                       '" . $this->db->real_escape_string($action) . "',
                                       '" . $this->db->real_escape_string($subject) . "',
                                       'email',
                                       '" . $this->db->real_escape_string($name_sender) . "',
                                       '" . $this->db->real_escape_string($from) . "',
                                       'neu',
                                       '" . $this->db->real_escape_string($verfasser_replyto) . "',
                                       '" . $this->db->real_escape_string($mail_replyto) . "'
                                     );";
                }


                if (!$DEBUG) {
                    $this->db->InsertWithoutLog(
                        "UPDATE ticket_nachricht
                                SET status = 'abgeschlossen'
                                WHERE ticket LIKE '$schluessel'"
                    );
                    $this->db->InsertWithoutLog($sql);
                    $ticketnachricht = $this->db->GetInsertID();
                    $this->db->InsertWithoutLog(
                        "UPDATE ticket
                                SET status='neu', zugewiesen = 0, inbearbeitung=0 
                                WHERE schluessel LIKE '$schluessel'"
                    );
                    $this->db->Update(
                        "UPDATE `ticket` AS `t` 
                                     INNER JOIN (
                                       SELECT COUNT(`id`) AS `co`, `ticket` 
                                       FROM `ticket_nachricht` 
                                       GROUP BY `ticket`
                                     ) AS `tn` ON t.schluessel = tn.ticket 
                                     SET t.nachrichten_anz = tn.co 
                                     WHERE t.schluessel = '$schluessel'"
                    );
                }
            } else {
                if (!$DEBUG) {
                    if ($action_html != '') {
                        $ticketnachricht = $this->ticketModule->CreateTicket(
                            $this->projectId,
                            $mailacc,
                            $name_sender,
                            $from,
                            $subject,
                            $action_html,
                            $timestamp,
                            "email",
                            null,
                            $verfasser_replyto,
                            $mail_replyto
                        ); // ACHTUNG immer Projekt eprooshop
                    } else {
                        $ticketnachricht = $this->ticketModule->CreateTicket(
                            $this->projectId,
                            $mailacc,
                            $name_sender,
                            $from,
                            $subject,
                            $action,
                            $timestamp,
                            "email",
                            null,
                            $verfasser_replyto,
                            $mail_replyto
                        ); // ACHTUNG immer Projekt eprooshop
                    }
                    $schluessel = $this->db->Select(
                        "SELECT `ticket`
                                FROM `ticket_nachricht`
                                WHERE `id`='$ticketnachricht' LIMIT 1"
                    );
                } else {
                    echo "Lege neues Ticket an\n";
                }
            }

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
                        if (stripos($dateiname, '=?utf-8') !== false) {
                            $dateiname = $this->formatter->encodeToUtf8($dateiname);
                            $dateiname = htmlspecialchars_decode($dateiname);
                        }
                        if (stripos($dateiname, '=?iso-8859') !== false) {
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

            $text = $this->mailAccount->getAutoresponseText();
            $betreff = $this->mailAccount->getAutoresponseSubject();
            $text = str_replace('{TICKET}', $schluessel, $text);
            $text = str_replace('{BETREFF}', $subject, $text);
            $betreff = str_replace('{TICKET}', $schluessel, $betreff);
            $betreff = str_replace('{BETREFF}', $subject, $betreff);

            if (
                $this->mailAccount->isAutoresponseEnabled()
                && $this->mailAccount->getAutoresponseText() !== ''
                && (
                    $this->erpApi->AutoresponderBlacklist($from) !== 1
                    || $this->mailAccount->isAutoresponseLimitEnabled() === false
                )
            ) {
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
}
