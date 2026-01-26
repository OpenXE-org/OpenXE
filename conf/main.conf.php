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
#[AllowDynamicProperties]
class Config
{
    /** @var string  */
    public $updateHost;

    public function __construct()
    {
        $this->updateHost = getenv('XENTRAL_UPDATE_HOST') ?: 'removed.upgrade.host';

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
