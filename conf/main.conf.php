<?php

class Config
{

    public string $updateHost = 'removed.upgrade.host';
    public string $WFdbhost;
    public int $WFdbport;
    public string $WFdbname;
    public string $WFdbuser;
    public string $WFdbpass;
    public string $WFuserdata;
    public array $WFconf;

    public function __construct()
    {
        include('user.inc.php');

        if (empty($this->WFdbport)) {
            $this->WFdbport = 3306;
        }

        // define defaults
        $this->WFconf['defaultpage'] = 'adresse';
        $this->WFconf['defaultpageaction'] = 'list';
        $this->WFconf['defaulttheme'] = 'new';
        $this->WFconf['defaultgroup'] = 'web';

        // allow that cols where dynamically added so structure
        $this->WFconf['autoDBupgrade'] = true;

        // time how long a user can be connected in seconds genau 8 stunden
        $this->WFconf['logintimeout'] = 3600 * 4;
    }
}
