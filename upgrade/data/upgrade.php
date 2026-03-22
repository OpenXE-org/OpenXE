<?php

/*
 * Upgrader using git for file upgrade and mustal to update the database definition
 *
 * Copyright (c) 2022 OpenXE project
 *
 */

$upgrade_echo_out_file_name = "";

function upgrade_set_out_file_name(string $filename) {

    GLOBAL $upgrade_echo_out_file_name;

    $upgrade_echo_out_file_name = $filename;
}

function echo_out(string $text) {

    GLOBAL $upgrade_echo_out_file_name;

    if ($upgrade_echo_out_file_name == "") {
        echo($text);
    } else {
        file_put_contents($upgrade_echo_out_file_name,$text, FILE_APPEND);
    }
}

function echo_output(array $output) {
    echo_out(implode("\n",$output)."\n");
}

function abort(string $message = '', $dump = null) {
    echo_out($message."\n");
    if (!empty($dump)) {
        echo_out(print_r($dump, true));
    }
    echo_out("--------------- Aborted! ---------------\n");
    echo_out("--------------- ".date("Y-m-d H:i:s")." ---------------\n");
}

function git(string $command, &$output, bool $show_command, bool $show_output, string $error_text) : int {
    $output = array();
    if ($show_command) {
        echo_out("git ".$command."\n");
    }
    exec("git ".$command,$output,$retval);
    if (!empty($output)) {
        if ($show_output || $retval != 0) {
            echo_output($output);
        }
    }
    if ($retval != 0) {
        echo_out($error_text."\n");
    }
    return($retval);
}

// -------------------------------- START

// Check for correct call method
if (php_sapi_name() == "cli") {

    $directory = getcwd();
    if (basename($directory) != 'upgrade') {
        abort("Must be executed from 'upgrade' directory.");
        return(-1);
    }

    $check_git = false;
    $do_git = false;
    $check_db = false;
    $do_db = false;
    $do_migrate = false;
    $do = false;
    $do_migrate = false;
    $upgrade_branch = '';

    if ($argc > 1) {

        if (in_array('-v', $argv)) {
          $verbose = true;
        } else {
          $verbose = false;
        }

        if (in_array('-e', $argv)) {
          $export_db = true;
        } else {
          $export_db = false;
        }

        if (in_array('-f', $argv)) {
          $force = true;
        } else {
          $force = false;
        }

        if (in_array('-o', $argv)) {
          $origin = true;
        } else {
          $origin = false;
        }

        if (in_array('-connection', $argv)) {
          $connection = true;
        } else {
          $connection = false;
        }

        if (in_array('-s', $argv)) {
          $check_git = true;
        } else {
        }

        if (in_array('-db', $argv)) {
          $check_db = true;
        } else {
        }

        if (in_array('-strict', $argv)) {
          $strict_db = true;
        } else {
          $strict_db = false;
        }

        if (in_array('-drop_keys', $argv)) {
          $drop_keys = true;
        } else {
          $drop_keys = false;
        }

        if (in_array('-do', $argv)) {
            if (!$check_git && !$check_db) {
                $do_git = true;
                $do_db = true;
            }
            if ($check_git) {
                $do_git = true;
            }
            if ($check_db) {
                $do_db = true;
            }
        }

        if (in_array('-m', $argv)) {
            $keypos = array_search('-m', $argv);
            if (isset($argv[$keypos+1])) {
                $upgrade_branch = $argv[$keypos+1];
                $do_migrate = true;
                $check_git = true;
            }
        }

        if ($check_git || $check_db || $do_git || $do_db || $do_migrate) {
            upgrade_main(   directory: $directory,
                            verbose: $verbose,
                            check_git: $check_git,
                            do_git: $do_git,
                            export_db: $export_db,
                            check_db: $check_db,
                            strict_db: $strict_db,
                            do_db: $do_db,
                            force: $force,
                            connection: $connection,
                            origin: $origin,
                            drop_keys: $drop_keys,
                            do_migrate: $do_migrate,
                            upgrade_branch: $upgrade_branch);
        } else {
            info();
        }

    } else {
        info();
    }

}
// -------------------------------- END

function upgrade_main(string $directory,bool $verbose, bool $check_git, bool $do_git, bool $export_db, bool $check_db, bool $strict_db, bool $do_db, bool $force, bool $connection, bool $origin, bool $drop_keys, bool $do_migrate, string $upgrade_branch = null) {

    $mainfolder = dirname($directory);
    $datafolder = $directory."/data";
    $lockfile_name = $datafolder."/.in_progress.flag";
    $remote_file_name = $datafolder."/remote.json";
    $schema_file_name = "db_schema.json";
    $remotes = array();
    $enabled_remotes = array();

    echo_out("--------------- OpenXE upgrade ---------------\n");
    echo_out("--------------- ".date("Y-m-d H:i:s")." ---------------\n");

    if ($check_git || $do_git) {

        if ($origin) {
            $remote_info = array('host' => 'origin','branch' => 'master');
        } else {
            $remote_info_contents = file_get_contents($remote_file_name);
            if (!$remote_info_contents) {
                abort("Unable to load $remote_file_name");
                return(-1);
            }
            $remotes = json_decode($remote_info_contents, true);
            $config = file_get_contents($mainfolder."/conf/user.inc.php");
            preg_match("/WFuserdata='(?<path>.*?)';/", $config, $matches);
            $uhash = file_get_contents($matches['path']."/uhash.txt");
            $ch = curl_init('https://upgrade.openxe-dev.org/remotes.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('uhash'=>$uhash));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            if (!$response) {
                $error_msg = "Fetch additional remotes failed.";
                if (!empty(curl_error($ch))) {
                    $error_msg .= " ".curl_error($ch);
                }
                if (!empty(curl_getinfo($ch)['http_code'])) {
                    $error_msg .= " (HTTP ".curl_getinfo($ch)['http_code'].")";
                }
                echo_out($error_msg."\n");
            } else {
                $additional_remotes = json_decode($response, true);
                if (!empty($additional_remotes['success'])) {
                    $remotes = array_merge($remotes, $additional_remotes['remotes']);
                    echo_out("Fetched additional remotes.\n");
                } else {
                    echo_out("No additional remotes.\n".$additional_remotes['message']."\n");
                }
            }
            $remotes = array_values(array_map("unserialize", array_unique(array_map("serialize", $remotes))));

            echo_out("--------------- Available upgrade branches ---------------\n");

            foreach ($remotes as $remote) {
                if (empty($remote['name'])) {
                    continue;
                }
                if (($remote['active'])) {
                    if (empty($remote_info)) {
                        $remote_info = $remote;
                    }
                    echo_out("-> ");
                } else {
                    echo_out("-  ");
                }

                echo_out($remote['name']);
                if ($remote['enabled']) {
                    if (!$remote['active']) {
                        $enabled_remotes[] = $remote;
                    }
                } else {
                    echo_out(" (disabled)");
                }
                echo_out("\n");
                echo_out("   ".$remote['description']['en']."\n");
            }
        }

        if (empty($remote_info)) {
            abort("No remote upgrade location found.");
            return(-1);
        }

        echo_out("--------------- Checking git ---------------\n");
        $retval = git("log HEAD --", $output,$verbose,false,"");
        // Not a git repository -> Create it and then go ahead
        if ($retval == 128) {
            if (!$do_git) {
                abort("Git not initialized, use -do to initialize.");
                return(-1);
            }

            echo_out("Setting up git...");
            $retval = git("init $mainfolder", $output,$verbose,$verbose,"Error while initializing git!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }
            $retval = git("add $mainfolder", $output,$verbose,$verbose,"Error while initializing git!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }
            $retval = git("fetch ".$remote_info['host']." ".$remote_info['branch'],$output,$verbose,$verbose,"Error while initializing git!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }

            $retval = git("checkout FETCH_HEAD -f --", $output,$verbose,$verbose,"Error while initializing git!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }
        } else if ($retval != 0) {
            abort("Error while executing git!");
            return(-1);
        }

        // Get changed files on system -> Should be empty
        $modified_files = false;
        $output = array();
        $retval = git("ls-files -m $mainfolder", $output,$verbose,false,"Error while checking Git status.");
        if (!empty($output)) {
            $modified_files = true;
            echo_out("There are modified files:\n");
            echo_output($output);
        }

        if ($verbose) {
            echo_out("--------------- Upgrade history ---------------\n");
            $retval = git("log --date=short-local --pretty=\"%cd (%h): %s\" HEAD --not HEAD~5 --",$output,$verbose,$verbose,"Error while showing history!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }
        } else {
            echo_out("--------------- Current version ---------------\n");
            $retval = git("log -1 --date=short-local --pretty=\"%cd (%h): %s\" HEAD --",$output,$verbose,true,"Error while showing history!");
            if ($retval != 0) {
                return(-1);
            }
        }

        if ($do_migrate) {

            echo_out("--------------- Migration ---------------\n");
            if ($modified_files && !$force) {
                abort("Clear modified files or use -f");
                return(-1);
            }
                        
            $retval = git("fetch ".$remote_info['host']." ".$remote_info['branch'],$output,$verbose,$verbose,"Error while fetching files!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }

            echo_out("--------------- Pending upgrades: ---------------\n");
            $retval = git("log --date=short-local --pretty=\"%cd (%h): %s\" FETCH_HEAD --not HEAD",$output,$verbose,true,"Error while fetching files!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }
            if (empty($output)) {
                echo_out("No upgrades pending.\n");
            } else {
                abort("Pending upgrades in current branch exist, upgrade current branch before migration");
                return(-1);
            }

            echo_out("Target upgrade branch: ".$upgrade_branch."\n");
            $remote_info = null;
            foreach ($enabled_remotes as $remote) {
                if ($remote['name'] == $upgrade_branch) {
                    $remote_info = $remote;
                }
            }

            if (empty($remote_info)) {
                abort("Target upgrade branch not found!");
                return(-1);
            }

            echo_out("Checking upgrade branch...\n");
            $retval = git("fetch ".$remote_info['host']." ".$remote_info['branch'],$output,$verbose,$verbose,"Error while fetching upgrade branch!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }

            echo_out("Checking upgrade branch requirements...\n");
            require_once($remote_info['check']);
            $migration_check_result = upgrade_check();
            if ($migration_check_result['result'] != 0) {
                echo_out("\n--------------- Migration check failed ---------------\n");
                abort($migration_check_result['message'], $verbose?$migration_check_result['dump']:false);
                return(-1);
            }
            echo_out("Upgrade branch requirements OK\n");

            echo_out("Starting Migration THIS IS A POINT OF NO RETURN!\n");
            echo_out("Migrating system...\n");
            require_once($remote_info['migration']);
            $migration_result = upgrade_migrate();
            if ($migration_result['result'] != 0) {
                echo_out("--------------- Migration failed ---------------\n");
                abort($migration_check_result['message'], $verbose?$migration_check_result['dump']:false);
                return(-1);
            }
            
            $retval = git("checkout FETCH_HEAD -f --", $output,$verbose,$verbose,"Error while checking out files from git!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }
            
            $do_git = true;
        }

        if ($do_git) {

            if ($modified_files && !$force) {
                abort("Clear modified files or use -f");
                return(-1);
            }

            echo_out("--------------- Pulling files... ---------------\n");

            if ($force) {
                $retval = git("reset --hard",$output,$verbose,$verbose,"Error while resetting modified files!");
                if ($retval != 0) {
                    abort("");
                    return(-1);
                }
            }

            $retval = git("pull ".$remote_info['host']." ".$remote_info['branch'],$output,$verbose,$verbose,"Error while pulling files!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }

            $retval = git("reset --hard",$output,$verbose,$verbose,"Error while applying files!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }

            echo_out("--------------- Files upgrade completed ---------------\n");
            $retval = git("log -1 ",$output,$verbose,$verbose,"Error while checking files!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }
            echo_output($output);

            // Remove files cache
            echo_out("--------------- Cleaning Filescache ---------------\n");
            class UserdataInfo {
                function __construct($dir) {
                    require($dir."/../conf/user.inc.php");
                }
            }

            $udi = new UserdataInfo($directory);

            $cache_files = array('cache_javascript.php','cache_services.php');

            $delete_cache_result = true;

            foreach ($cache_files as $cache_file) {
                $filename = $udi->WFuserdata."/tmp/".$udi->WFdbname."/".$cache_file;
                $delete_cache_file_result = @unlink($filename);
                if (!$delete_cache_file_result) {
                    echo_out("Failed to delete ".$filename."! Please delete manually...\n");
                    $delete_cache_result = false;
                }
            }

            if ($delete_cache_result) {
                echo_out("--------------- Cleaning Filescache completed ---------------\n");
            } else {
                echo_out("--------------- Cleaning Filescache failed! ---------------\n");
            }

        } // $do_git
        else { // Dry run
            echo_out("--------------- Dry run, use -do to upgrade ---------------\n");
            echo_out("--------------- Fetching files... ---------------\n");

            $retval = git("fetch ".$remote_info['host']." ".$remote_info['branch'],$output,$verbose,$verbose,"Error while fetching files!");
            if ($retval != 0) {
                abort("");
                return(-1);
            }

            echo_out("--------------- Pending upgrades: ---------------\n");

            $retval = git("log --date=short-local --pretty=\"%cd (%h): %s\" FETCH_HEAD --not HEAD",$output,$verbose,true,"Error while fetching files!");
            if (empty($output)) {
                echo_out("No upgrades pending.\n");
            }
            if ($retval != 0) {
                abort("");
                return(-1);
            }
        } // Dry run
    } // $check_git

    if ($check_db || $do_db || $export_db) {

        if ($connection) {
            $connection_file_name = $directory."/data/connection.json";
            $connection_file_contents = file_get_contents($connection_file_name);
            if (!$connection_file_contents) {
                abort("Unable to load $connection_file_name");
                return(-1);
            }
            $connection_info = json_decode($connection_file_contents, true);

            $host = $connection_info['host'];
            $user = $connection_info['user'];
            $passwd = $connection_info['passwd'];
            $schema = $connection_info['schema'];

        } else {

            class DatabaseConnectionInfo {
                function __construct($dir) {
                    require($dir."/../conf/user.inc.php");
                }
            }

            $dbci = new DatabaseConnectionInfo($directory);

            $host = $dbci->WFdbhost;
            $user = $dbci->WFdbuser;
            $passwd = $dbci->WFdbpass;
            $schema = $dbci->WFdbname;
        }

        require_once($directory.'/../vendor/mustal/mustal_mysql_upgrade_tool.php');

        echo_out("--------------- Loading from database '$schema@$host'... ---------------\n");
        $db_def = mustal_load_tables_from_db($host, $schema, $user, $passwd, $mustal_replacers);

        if (empty($db_def)) {
            echo_out("Could not load from $schema@$host\n");
            exit;
        }

        if ($export_db) {
            $export_file_name = "exported_db_schema.json";
            if (mustal_save_tables_to_json($db_def, $datafolder, $export_file_name, true) == 0) {
                echo_out("Database exported to $datafolder/$export_file_name\n");
            }
            else {
                echo_out("Could not export database to $datafolder/$export_file_name\n");
            }
        }

        $compare_differences = array();

        echo_out("--------------- Loading from JSON... ---------------\n");
        $compare_def = mustal_load_tables_from_json($datafolder, $schema_file_name);

        if (empty($compare_def)) {
            abort("Could not load from JSON $schema_file_name\n");
            return(-1);
        }
        echo_out("Table count database ".count($db_def['tables'])." vs. JSON ".count($compare_def['tables'])."\n");
        echo_out("--------------- Comparing JSON '".$compare_def['database']."@".$compare_def['host']."' vs. database '$schema@$host' ---------------\n");
        $compare_differences = mustal_compare_table_array($db_def,"in DB",$compare_def,"in JSON",false,true);
        if ($verbose) {
            foreach ($compare_differences as $compare_difference) {
                $comma = "";
                foreach ($compare_difference as $key => $value) {
                    echo_out($comma."$key => [$value]");
                    $comma = ", ";
                }
                echo_out("\n");
            }
        }
        echo_out((empty($compare_differences)?0:count($compare_differences))." differences.\n");

        echo_out("--------------- Comparing database '$schema@$host' vs. JSON '".$compare_def['database']."@".$compare_def['host']."' ---------------\n");

        $compare_differences = mustal_compare_table_array($compare_def,"in JSON",$db_def,"in DB",true,true);
        if ($verbose) {
            foreach ($compare_differences as $compare_difference) {
                $comma = "";
                foreach ($compare_difference as $key => $value) {
                    if (is_array($value)) {
                        $value = implode(',',$value);
                    }
                    echo_out($comma."$key => [$value]");
                    $comma = ", ";
                }
                echo_out("\n");
            }
        }
        echo_out((empty($compare_differences)?0:count($compare_differences))." differences.\n");

        echo_out("--------------- Calculating database upgrade for '$schema@$host'... ---------------\n");

        $upgrade_sql = array();
        $result =  mustal_calculate_db_upgrade($compare_def, $db_def, $upgrade_sql, $mustal_replacers, $strict_db, $drop_keys);

        if (!empty($result)) {
            abort(count($result)." errors.\n");
            if ($verbose) {
                foreach($result as $error) {
                    echo_out("Code: ".$error[0]." '".$error[1]."'\n");
                }
            }
            return(-1);
        }

        if ($verbose) {
            foreach($upgrade_sql as $statement) {
                echo_out($statement."\n");
            }
        }

        echo_out(count($upgrade_sql)." upgrade statements\n");

        if ($do_db) {
            echo_out("--------------- Executing database upgrade for '$schema@$host' database... ---------------\n");
             // First get the contents of the database table structure
            $mysqli = mysqli_connect($host, $user, $passwd, $schema);

            /* Check if the connection succeeded */
            if (!$mysqli) {
                echo ("Failed to connect!\n");
            } else  {

                $counter = 0;
                $error_counter = 0;
                $number_of_statements = count($upgrade_sql);

                foreach ($upgrade_sql as $sql) {
                    $counter++;
                    echo_out("\rUpgrade step $counter of $number_of_statements... ");
                    if ($verbose) {
                        echo_out("\n".$sql."\n");
                    }
                    try {
                        $query_result = mysqli_query($mysqli, $sql);
                        if (!$query_result) {
                            $error = " not ok: ". mysqli_error($mysqli);
                            echo_out($error);
                            echo_out("\n");
                            $error_counter++;
                        } else {
                            echo_out("ok.\r");
                        }
                    }
                    catch (Exception $e) {
                        $error = " not ok: ". mysqli_error($mysqli);
                        echo_out($error);
                        echo_out("\n");
                        $error_counter++;
                    }
                }

                echo_out("\n");
                echo_out("$error_counter errors.\n");
                if ($error_counter > 0) {
//                    echo_out("See 'errors.txt'\n");
                }

                echo_out("--------------- Checking database upgrade for '$schema@$host'... ---------------\n");
                $db_def = mustal_load_tables_from_db($host, $schema, $user, $passwd, $mustal_replacers);

                echo_out("--------------- Comparing database '$schema@$host' vs. JSON '".$compare_def['database']."@".$compare_def['host']."' ---------------\n");
                $compare_differences = mustal_compare_table_array($compare_def,"in JSON",$db_def,"in DB",true,true);
                echo_out((empty($compare_differences)?0:count($compare_differences))." differences.\n");
            }
        } // $do_db
    } // $check_db

/*
    echo_out("--------------- Locking system ---------------\n");
    if (file_exists($lockfile_name)) {
        echo_out("System is already locked.\n");
    } else {
        file_put_contents($lockfile_name," ");
    }

    echo_out("--------------- Unlocking system ---------------\n");
    unlink($lockfile_name);
*/

    echo_out("--------------- Done! ---------------\n");
    echo_out("--------------- ".date("Y-m-d H:i:s")." ---------------\n");
    return(0);
}

function info() {
    echo_out("OpenXE upgrade tool\n");
    echo_out("Copyright 2022 (c) OpenXE project\n");
    echo_out("\n");
    echo_out("Upgrade files and database\n");
    echo_out("Options:\n");
    echo_out("\t-s: check/do system upgrades\n");
    echo_out("\t-db: check/do database upgrades\n");
    echo_out("\t-e: export database schema\n");
    echo_out("\t-do: execute all upgrades\n");
    echo_out("\t-m: migrate to another upgrade branch, next parameter must be upgrade branch name\n");
    echo_out("\t-v: verbose output\n");
    echo_out("\t-f: force override of existing files\n");
    echo_out("\t-o: update from origin instead of remote.json\n");
    echo_out("\t-connection use connection.json in data folder instead of user.inc.php\n");
    echo_out("\t-strict: innodb_strict_mode=ON\n");
    echo_out("\t-clean: (not yet implemented) create the needed SQL to remove items from the database not in the JSON\n");
    echo_out("\n");
}


