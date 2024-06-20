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
        $db_verbose = $this->app->Secure->GetPOST('db_details_anzeigen') === '1';
        $force = $this->app->Secure->GetPOST('erzwingen') === '1';

      	$this->app->Tpl->Set('DETAILS_ANZEIGEN', $verbose?"checked":"");
      	$this->app->Tpl->Set('DB_DETAILS_ANZEIGEN', $db_verbose?"checked":"");

        include("../upgrade/data/upgrade.php");

        $logfile = "../upgrade/data/upgrade.log";
        upgrade_set_out_file_name($logfile);

        $this->app->Tpl->Set('UPGRADE_VISIBLE', "hidden");
        $this->app->Tpl->Set('UPGRADE_DB_VISIBLE', "hidden");

        $directory = dirname(getcwd())."/upgrade";

        switch ($submit) {
            case 'check_upgrade':
                $this->app->Tpl->Set('UPGRADE_VISIBLE', "");
                unlink($logfile);
                upgrade_main(   directory: $directory,
                                verbose: $verbose,
                                check_git: true,
                                do_git: false,
                                export_db: false,
                                check_db: true,
                                strict_db: false,
                                do_db: false,
                                force: $force,
                                connection: false,
                                origin: false,
                                drop_keys: false
                );
            break;
            case 'do_upgrade':
                unlink($logfile);
                upgrade_main(   directory: $directory,
                                verbose: $verbose,
                                check_git: true,
                                do_git: true,
                                export_db: false,
                                check_db: true,
                                strict_db: false,
                                do_db: true,
                                force: $force,
                                connection: false,
                                origin: false,
                                drop_keys: false
                );
            break;    
            case 'check_db':
                $this->app->Tpl->Set('UPGRADE_DB_VISIBLE', "");
                unlink($logfile);
                upgrade_main(   directory: $directory,
                                verbose: $db_verbose,
                                check_git: false,
                                do_git: false,
                                export_db: false,
                                check_db: true,
                                strict_db: false,
                                do_db: false,
                                force: $force,
                                connection: false,
                                origin: false,
                                drop_keys: false
                );
            break;    
            case 'do_db_upgrade':
                $this->app->Tpl->Set('UPGRADE_DB_VISIBLE', "");
                unlink($logfile);
                upgrade_main(   directory: $directory,
                                verbose: $db_verbose,
                                check_git: false,
                                do_git: false,
                                export_db: false,
                                check_db: true,
                                strict_db: false,
                                do_db: true,
                                force: $force,
                                connection: false,
                                origin: false,
                                drop_keys: false
                );
            break;    
            case 'refresh':
            break;
        }

        // Read results
        $result = file_get_contents($logfile);             
        $this->app->Tpl->Set('CURRENT', $this->app->erp->Revision());
        $this->app->Tpl->Set('OUTPUT_FROM_CLI',nl2br($result));
        $this->app->Tpl->Parse('PAGE', "upgrade.tpl");
    }   
    

}

