<?php

use Sabre\DAV\Auth\Backend\AbstractBasic;

class DocscanAuth extends AbstractBasic
{
    /** @var DB $DB */
    protected $DB;

    /**
     * @param DB $DB
     */
    public function __construct($DB)
    {
        $this->DB = $DB;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function validateUserPass($username, $password)
    {
        $username = $this->DB->real_escape_string($username);
        $password = $this->DB->real_escape_string($password);
        $docscanPassword = $this->DB->Select(
            "SELECT u.docscan_passwort 
            FROM `user` AS u 
            WHERE u.username = '$username' AND u.docscan_aktiv = 1 AND u.activ = 1
            LIMIT 1"
        );
        if (empty($docscanPassword)) {
            return false;
        }

        return $docscanPassword === $password;
    }
}
