<?php

namespace Xentral\Modules\CalDav\SabreDavBackend;

use Sabre\DAV\Auth\Backend\AbstractBasic;
use Xentral\Components\Database\Database;

class WawisionAuthBackend extends AbstractBasic
{
    /**
     * @var Database
     */
    private $db;

    /**
     * WawisionAuthBackend constructor.
     *
     * @param Database $db
     *
     * @return void
     */
    function __construct($db)
    {
        $this->db = $db;
    }

    function validateUserPass($username, $password)
    {
        $count = (int)$this->db->fetchValue(
            'SELECT COUNT(*) FROM konfiguration as k WHERE (k.name="caldav_username" AND k.wert=:user) OR (k.name="caldav_password" AND k.wert=:pass);',
            [
                'user' => $username,
                'pass' => $password,
            ]);

        return $count === 2;
    }

}


