<?php

/**
 * @property string WFdbhost
 * @property int WFdbport
 * @property string WFdbname
 * @property string WFdbuser
 * @property string WFdbpass
 * @property string WFuserdata Absolute path to userdata directory
 * @property array WFconf
 */
class Config
{
    /** @var string  */
    public $updateHost = 'update.xentral.biz';

    public function __construct()
    {
        include("user.inc.php");

        if (!isset($this->WFdbport) || empty($this->WFdbport)) {
            $this->WFdbport = 3306;
        }

        // define defaults
        $this->WFconf['defaultpage'] = 'adresse';
        $this->WFconf['defaultpageaction'] = 'list';
        $this->WFconf['defaulttheme'] = 'new';
        //$this->WFconf['defaulttheme'] = 'default_redesign';
        $this->WFconf['defaultgroup'] = 'web';

        // allow that cols where dynamically added so structure
        $this->WFconf['autoDBupgrade'] = true;

        // time how long a user can be connected in seconds genau 8 stunden
        $this->WFconf['logintimeout'] = 3600 * 4;
    }
}
