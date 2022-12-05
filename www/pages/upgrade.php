<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class upgrade {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "upgrade_overview");        
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }  
 
    function upgrade_overview() {  
    
        $submit = $this->app->Secure->GetPOST('submit');
        $verbose = $this->app->Secure->GetPOST('details_anzeigen') === '1';
        $force = $this->app->Secure->GetPOST('erzwingen') === '1';

        ob_start();
        include("../upgrade/upgrade.php");

        if ($submit == 'do_upgrade') {
            $do_upgrade = true;
            $this->app->Tpl->Set('PENDING_VISIBLE', "hidden");
        } else {
            $do_upgrade = false;
            $this->app->Tpl->Set('PROGRESS_VISIBLE', "hidden");
        }

        upgrade_main("../upgrade",$verbose,$do_upgrade,$force);
        $result = ob_get_contents();
        ob_end_clean();
        $this->app->Tpl->Set('CURRENT', $this->app->erp->Revision());
        $this->app->Tpl->Set('OUTPUT_FROM_CLI',$result);
        $this->app->Tpl->Parse('PAGE', "upgrade.tpl");
    }   
    

}
