<?php

namespace Xentral\Modules\SubscriptionCycle\Wrapper;

use ApplicationCore;
use AuftragPDF;
use Xentral\Components\Mailer\Data\EmailMessage;
use Xentral\Components\Mailer\Data\EmailRecipient;
use Xentral\Components\Mailer\Data\FileAttachment;
use Xentral\Modules\SubscriptionCycle\Exception\RuntimeException;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\SystemMailer\Service\EmailAccountGateway;
use Xentral\Modules\SystemMailer\SystemMailer;

final class BusinessLetterWrapper
{
    /** @var ApplicationCore $app */
    private $app;

    /** @var SystemMailer $mailer */
    private $mailer;

    /** @var EmailAccountGateway $accountGateway */
    private $accountGateway;

    /**
     * @param ApplicationCore $app
     */
    public function __construct(ApplicationCore $app, SystemMailer $mailer, EmailAccountGateway $accountGateway)
    {
        $this->app = $app;
        $this->mailer = $mailer;
        $this->accountGateway = $accountGateway;
    }

    /**
     * @param array $sendData
     * @param int   $orderId
     */
    public function sendBusinessLetter(array $sendData, int $orderId): void
    {
        $dataSorted = [];
        foreach ($sendData as $data) {
            $autoEmailConfirmation = (bool)$data['auto_email_confirmation'];

            if ($autoEmailConfirmation) {
                $letterSubject = $data['subjekt'];
                $dataSorted[$letterSubject][] = $data;
            }
        }

        if (!empty($dataSorted)) {
            foreach ($dataSorted as $letterSubject => $parts) {
                $language = $parts[0]['sprache'];
                $projectId = (int)$parts[0]['projekt'];
                $isAddPdf = (bool)$parts[0]['add_pdf'];

                if (empty($language)) {
                    $language = 'deutsch';
                }

                $files = [];
                if ($isAddPdf) {
                    $path = $this->getMailPdf($orderId, $projectId);

                    if (!empty($path)) {
                        $attachment = new FileAttachment($path);
                        $files[] = $attachment;
                    }
                }

                $email = $data['abweichendeemailab'];
                if (empty($recipient)) {
                    $email = $data['email'];
                }
                $recipient = new EmailRecipient($email);
                $message = new EmailMessage(
                    $this->getMailSubject($letterSubject, $language, $projectId, $orderId, $parts),
                    $this->getMailContent($letterSubject, $language, $projectId, $orderId, $parts),
                    [$recipient],
                    null,
                    null,
                    $files
                );
                $account = $this->getBackupAccountData();

                $mailresponse = $this->mailer->send($message, $account);

                if (!empty($mailresponse)) {
                    throw new RuntimeException('Mail could not be send. More info in the logger');
                }
            }
        }
    }

    /**
     * @param string $letterSubject
     * @param string $language
     * @param int    $projectId
     * @param int    $orderId
     * @param array  $articles
     *
     * @return string
     */
    private function getMailSubject(
        string $letterSubject,
        string $language,
        int $projectId,
        int $orderId,
        array $articles
    ): string {
        $subject = $this->app->erp->GetGeschaeftsBriefBetreff(
            $letterSubject,
            $language,
            $projectId,
            'auftrag',
            $orderId
        );

        $subject = $this->parseVars($subject, $orderId, $articles);

        return $subject;
    }

    /**
     * @param string $letterSubject
     * @param string $language
     * @param int    $projectId
     * @param int    $orderId
     * @param array  $articles
     *
     * @return string
     */
    private function getMailContent(
        string $letterSubject,
        string $language,
        int $projectId,
        int $orderId,
        array $articles
    ): string {
        $content = $this->app->erp->GetGeschaeftsBriefText(
            $letterSubject,
            $language,
            $projectId,
            'auftrag',
            $orderId
        );

        $content = $this->parseVars($content, $orderId, $articles);

        return $content;
    }

    /**
     * @param string $text
     * @param int    $orderId
     * @param array  $articles
     *
     * @return string
     */
    private function parseVars(string $text, int $orderId, array $articles): string
    {
        $text = (string)$this->app->erp->ParseUserVars('auftrag', $orderId, $text);
        if (empty($text)) {
            return '';
        }

        return $this->parseAutoAboVars($text, $articles);
    }

    /**
     * @param string $text
     * @param array  $articles
     *
     * @return string
     */
    private function parseAutoAboVars(string $text, array $articles): string
    {
        $replace = '';

        foreach ($articles as $article) {
            $lb = '';
            if (!empty($replace)) {
                $lb = PHP_EOL;
            }
            $replace .= $lb . $article['nummer'] . ' - ' . $article['bezeichnung'];
        }

        $text = str_replace('{ABOARTIKEL}', $replace, $text);

        return $text;
    }

    /**
     * @param int $orderId
     * @param int $projectId
     *
     * @return string
     */
    private function getMailPdf(int $orderId, int $projectId): string
    {
        $pdfclass = 'AuftragPDF';
        if (class_exists('RechnungPDFCustom')) {
            $pdfclass = 'AuftragPDFCustom';
        }
        /** @var AuftragPDF $pdfObject */
        $pdfObject = new $pdfclass($this->app, $projectId);
        $pdfObject->GetAuftrag($orderId);
        $orderFile = $pdfObject->displayTMP();
        $pdfObject->ArchiviereDocument();

        return $orderFile;
    }

    /**
     * @return EmailBackupAccount
     */
    private function getBackupAccountData(): EmailBackupAccount
    {
        $senderEmail = (string)$this->app->erp->GetFirmaMail();
        $senderName = (string)$this->app->erp->GetFirmaAbsender();

        $account = $this->accountGateway->tryGetEmailAccountByEmail($senderEmail);

        if (!empty($account)) {
            return $account;
        } else {
            $data['id'] = 0;
            $data['angezeigtername'] = $senderName;
            $data['email'] = $senderEmail;
            $data['internebeschreibung'] = '';
            $data['benutzername'] = $this->app->erp->Firmendaten('benutzername');
            $data['passwort'] = $this->app->erp->Firmendaten('passwort');
            $data['server'] = $this->app->erp->Firmendaten('host');
            $data['imap_sentfolder_aktiv'] = 0;
            $data['imap_sentfolder'] = '';
            $data['imap_port'] = 0;
            $data['imap_type'] = 1;
            $data['geschaeftsbriefvorlage'] = 0;
            $data['autoresponder'] = 0;
            $data['autoresponderbetreff'] = '';
            $data['autorespondertext'] = '';
            $data['autoresponder_blacklist'] = 0;
            $data['projekt'] = 0;
            $data['emailbackup'] = 0;
            $data['loeschtage'] = 0;
            $data['adresse'] = 0;
            $data['firma'] = 1;
            $data['geloescht'] = 0;
            $data['ticket'] = 0;
            $data['ticketloeschen'] = 0;
            $data['ticketabgeschlossen'] = 0;
            $data['ticketqueue'] = 0;
            $data['ticketprojekt'] = 0;
            $data['ticketemaileingehend'] = 0;
            $data['abdatum'] = '0000-00-00';
            $data['smtp_extra'] = 0;
            $data['smtp'] = '';
            $data['smtp_ssl'] = 0;
            $data['smtp_port'] = 0;
            $data['smtp_frommail'] = '';
            $data['smtp_fromname'] = '';
            $data['smtp_authtype'] = '';
            $data['smtp_authparam'] = '';
            $data['smtp_loglevel'] = 0;
            $data['client_alias'] = '';
            $data['eigenesignatur'] = empty($this->app->erp->Firmendaten('signatur')) ? 0 : 1;
            $data['signatur'] = base64_decode($this->app->erp->Firmendaten('signatur'));
            $data['mutex'] = 0;

            return EmailBackupAccount::fromDbState($data);
        }
    }
}
