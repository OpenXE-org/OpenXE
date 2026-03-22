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
        $branch = $this->app->Secure->GetPOST('branch');

      	$this->app->Tpl->Set('DETAILS_ANZEIGEN', $verbose?"checked":"");
      	$this->app->Tpl->Set('DB_DETAILS_ANZEIGEN', $db_verbose?"checked":"");

        include("../upgrade/data/upgrade.php");

        $logfile = "../upgrade/data/upgrade.log";
        upgrade_set_out_file_name($logfile);

        $this->app->Tpl->Set('UPGRADE_VISIBLE', "hidden");
        $this->app->Tpl->Set('UPGRADE_DB_VISIBLE', "hidden");

        $directory = dirname(getcwd())."/upgrade";
        $remote_file_name = $directory."/data/remote.json";

        $remote_info_contents = file_get_contents($remote_file_name);
        if (!$remote_info_contents) {
            $this->app->Tpl->AddMessage('error',"Unable to load $remote_file_name");
        }
        $remotes = json_decode($remote_info_contents, true);

        $branches = array_column($remotes, 'name');

        $table = new EasyTable($this->app);
        $table->headings = array('','Name','Status','Beschreibung');

        foreach ($remotes as $remote) {
            $checked = '';
            $status = '';
            $disabled = '';
            if ($remote['active']) {
                $current_branch = $remote['name'];
                $status = 'aktiv';
                $checked = ' checked';
            }
            if (!$remote['enabled']) {
                $status = 'gesperrt';
                $disabled = ' disabled';
            }
            $select = '<input type="radio" name="branch" value="'.$remote['name'].'"'.$checked.$disabled.'>';
            $row = array($select,$remote['name'],$status,$remote['description']['de']);
            $table->AddRow($row);
        }
        $table->DisplayNew('BRANCHES','Status','noAction');

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
                                drop_keys: false,
                                do_migrate: false
                );
            break;
            case 'do_upgrade':

                if ($branch != $current_branch) {
                    $do_migrate = true;
                } else {
                    $do_migrate = false;
                }

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
                                drop_keys: false,
                                do_migrate: $do_migrate,
                                upgrade_branch: $branch
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
                                drop_keys: false,
                                do_migrate: false
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
                                drop_keys: false,
                                do_migrate: false
                );
            break;
            case 'refresh':
            break;
        }

        // Read results
        $result = file_get_contents($logfile);
        $this->app->Tpl->Set('CURRENTBRANCH', $current_branch);
        $this->app->Tpl->Set('CURRENT', $this->app->erp->Revision());
        $this->app->Tpl->Set('OUTPUT_FROM_CLI',nl2br($result));
        $this->app->Tpl->Parse('PAGE', "upgrade.tpl");
    }
}

