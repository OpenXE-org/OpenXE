<?php

/*
 * Upgrader using git for file upgrade and mustal to update the database definition
 *
 * Copyright (c) 2022 OpenXE project
 *
 */

function echo_output(array $output) {
    echo(implode("\n",$output)."\n");
}

function abort(string $message) {
    echo($message."\n");
    echo("--------------- Aborted! ---------------\n");
}

class DatabaseConnectionInfo {
    function __construct() {
        require_once('../conf/user.inc.php');
    }
}

function git(string $command, &$output, bool $verbose, string $error_text) : int {
    $output = array();
    if ($verbose) {
        echo("git ".$command."\n");
    }
    exec("git ".$command,$output,$retval);
    if (!empty($output)) {
        if ($verbose || $retval != 0) {
            echo_output($output);
        }
    }
    return($retval);
}

// -------------------------------- START
// Check for correct call method
$directory = "";
if (php_sapi_name() == "cli") {
    $cli = true;
    $directory = ".";
} else if (basename(getcwd()) != 'upgrade') {
    // Started from "www"
    $directory = "../upgrade";
} else {
    abort("Must be executed from 'upgrade' directory.");
    return(-1);
}

if ($cli) {
    if (in_array('-v', $argv)) {
      $verbose = true;
    } else {
      $verbose = false;
    } 

    if (in_array('-f', $argv)) {
      $force = true;
    } else {
      $force = false;
    } 

    if (in_array('-do', $argv)) {
      $do_upgrade = true;
    } else {
      $do_upgrade = false;
    } 
    upgrade_main($directory, $verbose, $do_upgrade, $force);
} 
// -------------------------------- END

function upgrade_main(string $directory,bool $verbose, bool $do_upgrade, bool $force) {  

    $dbci = new DatabaseConnectionInfo;

    $host = $dbci->WFdbhost;
    $user = $dbci->WFdbuser;
    $passwd = $dbci->WFdbpass;
    $schema = $dbci->WFdbname;

    require_once($directory.'/../tools/database_compare/mustal_mysql_upgrade_tool.php');

    $datafolder = $directory."/data";
    $lockfile_name = $datafolder."/.in_progress.flag";
    $remote_file_name = $datafolder."/remote.json";
    $schema_file_name = "db_schema.json";

    echo("--------------- OpenXE upgrade ---------------\n");

    require_once($directory.'/../cronjobs/githash.php');

    $remote_info_contents = file_get_contents($remote_file_name);
    if (!$remote_info_contents) {
        abort("Unable to load $remote_file_name");
        return(-1);
    } 
    $remote_info = json_decode($remote_info_contents, true);

    // Get changed files on system -> Should be empty
    $output = array();
    $retval = git("ls-files -m ..", $output,$verbose,"Git not initialized.");

    // Not a git repository -> Create it and then go ahead
    if ($retval == 128) { 
        echo("Setting up git...");
        $retval = git("init ..", $output,$verbose,"Error while initializing git!");
        if ($retval != 0) {
            abort("");
            return(-1);
        }
        $retval = git("add ../.", $output,$verbose,"Error while initializing git!");   
        if ($retval != 0) {
            abort("");
            return(-1);
        }
        $retval = git("fetch ".$remote_info['host']." ".$remote_info['branch'], $output,$verbose,"Error while initializing git!");
        if ($retval != 0) {
            abort("");
            return(-1);
        }

        $retval = git("checkout FETCH_HEAD -f", $output,$verbose,"Error while initializing git!");   
        if ($retval != 0) {
            abort("");
            return(-1);
        }
    }

    if ($retval != 0) {
        abort("Error while executing git!");
        return(-1);
    }


    if ($verbose) {
        echo("--------------- Update history ---------------\n");
        $retval = git("log --date=short-local --pretty=\"%cd (%h): %s\" HEAD --not HEAD~4",$output,$verbose,"Error while showing history!");
        if ($retval != 0) {
            abort("");
            return(-1);
        }
    }

    if ($do_upgrade) { 
        if (!empty($output)) {
            echo("There are modified files:");
            echo_output($output);
            if (!$force) {
                abort("Clear modified files or use -f");
                return(-1);
            }
        }

        echo("--------------- Locking system ---------------\n");
        if (file_exists($lockfile_name)) {
            echo("System is already locked.\n");
        } else {
            file_put_contents($lockfile_name," ");
        }

        echo("--------------- Pulling files... ---------------\n");

        if ($force) {
            $retval = git("reset --hard",$output,$verbose,"Error while resetting modified files!");
            if ($retval != 0) {
                echo_output($output);
                abort("");
                return(-1);
            }       
        } 

        $retval = git("pull ".$remote_info['host']." ".$remote_info['branch'],$output,$verbose,"Error while pulling files!");
        if ($retval != 0) {
            echo_output($output);
            abort("");
            return(-1);
        }

        $retval = git("reset --hard",$output,$verbose,"Error while applying files!");
        if ($retval != 0) {
            echo_output($output);
            abort("");
            return(-1);
        }       

        echo("--------------- Files upgrade completed ---------------\n");
        $retval = git("log -1 ",$output,$verbose,"Error while checking files!");
        if ($retval != 0) {
            echo_output($output);
            abort("");
            return(-1);
        }
        echo_output($output);

        echo("--------------- Loading from database '$schema@$host'... ---------------\n");
        $db_def = mustal_load_tables_from_db($host, $schema, $user, $passwd, $mustal_replacers);

        if (empty($db_def)) {
            echo ("Could not load from $schema@$host\n");
            exit;
        }
        $compare_differences = array();

        echo("--------------- Loading from JSON... ---------------\n");
        $compare_def = mustal_load_tables_from_json($datafolder, $schema_file_name);

        if (empty($compare_def)) {
            abort("Could not load from JSON $schema_file_name\n");
            return(-1);
        }
        echo("--------------- Comparing database '$schema@$host' vs. JSON '".$compare_def['database']."@".$compare_def['host']."' ---------------\n");
        $compare_differences = mustal_compare_table_array($compare_def,"in JSON",$db_def,"in DB",true);
        echo((empty($compare_differences)?0:count($compare_differences))." differences.\n");

        echo("--------------- Calculating database upgrade for '$schema@$host'... ---------------\n");

        $upgrade_sql = array();

        $result =  mustal_calculate_db_upgrade($compare_def, $db_def, $upgrade_sql, $mustal_replacers);

        if ($result != 0) {
            abort("Error: $result\n");
            return(-1);
        }

        echo(count($upgrade_sql)." upgrade statements\n");

        echo("--------------- Executing database upgrade for '$schema@$host' database... ---------------\n");            

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
                echo("\rUpgrade step $counter of $number_of_statements... ");

                $query_result = mysqli_query($mysqli, $sql);
                if (!$query_result) {        
                    $error = " not ok: ". mysqli_error($mysqli);            
                    echo($error);
                    echo("\n");
                    file_put_contents("./errors.txt",date()." ".$error.$sql."\n",FILE_APPEND);
                    $error_counter++;
                } else {
                    echo("ok.\r");
                }

            }

            echo("\n");
            echo("$error_counter errors.\n");
            if ($error_counter > 0) {
                echo("See 'errors.txt'\n");
            }

            echo("--------------- Checking database upgrade for '$schema@$host'... ---------------\n");
            $db_def = mustal_load_tables_from_db($host, $schema, $user, $passwd, $mustal_replacers);

            echo("--------------- Comparing database '$schema@$host' vs. JSON '".$compare_def['database']."@".$compare_def['host']."' ---------------\n");
            $compare_differences = mustal_compare_table_array($compare_def,"in JSON",$db_def,"in DB",true);
            echo((empty($compare_differences)?0:count($compare_differences))." differences.\n");

        }

        echo("--------------- Unlocking system ---------------\n");
        unlink($lockfile_name);
    } 
    else { // Dry run
        echo("--------------- Dry run, use -do to upgrade ---------------\n");
        echo("--------------- Fetching files... ---------------\n");

        $retval = git("fetch ".$remote_info['host']." ".$remote_info['branch'],$output,$verbose,"Error while fetching files!");
        if ($retval != 0) {
            echo_output($output);
            abort("");
        }

        echo("--------------- Pending upgrades: ---------------\n");

        $retval = git("log --date=short-local --pretty=\"%cd (%h): %s\" FETCH_HEAD --not HEAD",$output,$verbose,"Error while fetching files!");
        if (!$verbose) {
            echo_output($output);
        }
        if (empty($output)) {
            echo("No upgrades pending.\n");
        }
        if ($retval != 0) {
            abort("");
        }
    }

    echo("--------------- Done! ---------------\n");
    return(0);
}


