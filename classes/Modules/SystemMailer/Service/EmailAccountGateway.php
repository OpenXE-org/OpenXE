<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailer\Service;

use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\SystemMailer\Exception\EmailBackupAccountException;

final class EmailAccountGateway
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param int $id
     *
     * @throws EmailBackupAccountException
     *
     * @return EmailBackupAccount
     */
    public function getEmailAccountById(int $id): EmailBackupAccount
    {
        $query = $this->getBaseQuery()->where('id = :id');
        $sql = $query->getStatement();
        $row = $this->db->fetchRow($sql, ['id' => $id]);
        if (empty($row)) {
            throw new EmailBackupAccountException('Account not found.');
        }

        return EmailBackupAccount::fromDbState($row);
    }

    /**
     * @param int $userId
     *
     * @throws EmailBackupAccountException
     *
     * @return EmailBackupAccount
     */
    public function getAccountByUser(int $userId): EmailBackupAccount
    {
        $query = $this->getBaseQuery();
        try {
            $query->join('', 'user AS u', 'u.adresse = e.adresse AND u.adresse != 0');
        } catch (Exception $e) {
            throw new EmailBackupAccountException($e->getMessage(), $e->getCode(), $e);
        }
        $query->where('u.activ = 1')
            ->where('u.id = :user_id');
        $sql = $query->getStatement();
        $values = ['user_id' => $userId];
        $row = $this->db->fetchRow($sql, $values);
        if (empty($row)) {
            throw new EmailBackupAccountException(sprintf('No email account found for user "%s"', $userId));
        }

        return EmailBackupAccount::fromDbState($row);
    }

    /**
     * @param int $addressId
     *
     * @throws EmailBackupAccountException
     *
     * @return EmailBackupAccount
     */
    public function getAccountByAddress(int $addressId): EmailBackupAccount
    {
        $query = $this->getBaseQuery()->where('e.adresse = :address_id');
        $row = $this->db->fetchRow($query, ['address_id' => $addressId]);
        if (empty($row)) {
            throw new EmailBackupAccountException(sprintf('No email account found for address "%s"', $addressId));
        }

        return EmailBackupAccount::fromDbState($row);
    }

    /**
     * @param string $email
     *
     * @throws EmailBackupAccountException
     *
     * @return EmailBackupAccount
     */
    public function getAccountByEmail(string $email): EmailBackupAccount
    {
        $query = $this->getBaseQuery()->where('email LIKE :email')->orWhere('smtp_frommail LIKE :email');
        $sql = $query->getStatement();
        $row = $this->db->fetchRow($sql, ['email' => $email]);
        if (empty($row)) {
            throw new EmailBackupAccountException(sprintf('No email account found for %s', $email));
        }

        return EmailBackupAccount::fromDbState($row);
    }

    /**
     * @deprecated 20.3
     * @deprecated use getAccountByEmail instead
     *
     * @param string $email
     *
     * @return EmailBackupAccount|null
     */
    public function tryGetEmailAccountByEmail(string $email): ?EmailBackupAccount
    {
        $query = $this->getBaseQuery()->where('email LIKE :email')->orWhere('smtp_frommail LIKE :email');
        $sql = $query->getStatement();
        $row = $this->db->fetchRow($sql, ['email' => $email]);
        if (empty($row)) {
            return null;
        }

        return EmailBackupAccount::fromDbState($row);
    }

    /**
     * @return EmailBackupAccount[]
     */
    public function getAccountsWithTicketActive(): array
    {
        $query = $this->getBaseQuery();
        $query->where('e.ticket = 1');
        $query->where('e.geloescht != 1');
        $sql = $query->getStatement();
        $rows = $this->db->fetchAll($sql);
        $accounts = [];
        foreach ($rows as $row) {
            $accounts[] = EmailBackupAccount::fromDbState($row);
        }

        return $accounts;
    }

    /**
     * @return SelectQuery
     */
    private function getBaseQuery(): SelectQuery
    {
        $select = $this->db->select();
        $select->cols(
            [
                'e.id',
                'e.angezeigtername',
                'e.internebeschreibung',
                'e.benutzername',
                'e.passwort',
                'e.server',
                'e.smtp',
                'e.ticket',
                'e.imap_sentfolder_aktiv',
                'e.imap_sentfolder',
                'e.imap_port',
                'e.imap_type',
                'e.autoresponder',
                'e.geschaeftsbriefvorlage',
                'e.autoresponderbetreff',
                'e.autorespondertext',
                'e.projekt',
                'e.emailbackup',
                'e.adresse',
                'e.firma',
                'e.loeschtage',
                'e.geloescht',
                'e.ticketloeschen',
                'e.ticketabgeschlossen',
                'e.ticketqueue',
                'e.ticketprojekt',
                'e.ticketemaileingehend',
                'e.smtp_extra',
                'e.smtp_ssl',
                'e.smtp_port',
                'e.smtp_frommail',
                'e.smtp_fromname',
                'e.autosresponder_blacklist as autoresponder_blacklist',
                'e.eigenesignatur',
                'e.signatur',
                'e.mutex',
                'e.abdatum',
                'e.email',
                'e.smtp_authtype',
                'e.smtp_authparam',
                'e.smtp_loglevel',
                'e.client_alias',
            ]
        );
        $select->from('emailbackup as e');

        return $select;
    }
}
