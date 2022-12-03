<?php

/*
 * Upgrader using git for file upgrade and mustal to update the database definition
 *
 * Copyright (c) 2022 OpenXE project
 *
 */

class DatabaseConnectionInfo {
    function __construct() {
        require('../conf/user.inc.php');
    }
}

$dbci = new DatabaseConnectionInfo;

$host = $dbci->WFdbhost;
$user = $dbci->WFdbuser;
$passwd = $dbci->WFdbpass;
$schema = $dbci->WFdbname;

require('../tools/database_compare/mustal_mysql_upgrade_tool.php');

$lockfile_name = "data/.in_progress.flag";
$remote_file_name = "data/remote.json";
$datafolder = "data";
$schema_file_name = "db_schema.json";

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

function echo_output(array $output) {
    echo("\n".implode("\n",$output)."\n");
}

function abort(string $message) {
    echo($message."\n");
    echo("--------------- Aborted! ---------------\n");
    exit(-1);
}

// -------------------------------- START

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

echo("--------------- OpenXE upgrade ---------------\n");

if (basename(getcwd()) != 'upgrade') {
    abort("Must be executed from 'upgrade' directory.");
}

$remote_info_contents = file_get_contents($remote_file_name);
if (!$remote_info_contents) {
    abort("Unable to load $remote_file_name");
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
    }
    $retval = git("add ../.", $output,$verbose,"Error while initializing git!");   
    if ($retval != 0) {
        abort("");
    }
    $retval = git("fetch ".$remote_info['host']." ".$remote_info['branch'], $output,$verbose,"Error while initializing git!");
    if ($retval != 0) {
        abort("");
    }
    $retval = git("checkout FETCH_HEAD -f", $output,$verbose,"Error while initializing git!");   
    if ($retval != 0) {
        abort("");
    }
}

if ($retval != 0) {
    abort("Error while executing git!");
}

if (!empty($output)) {
    echo("There are modified files:");
    echo_output($output);
    if (!$force) {
        abort("Clear modified files or use -f");
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
    }       
} 

$retval = git("pull ".$remote_info['host']." ".$remote_info['branch'],$output,$verbose,"Error while pulling files!");
if ($retval != 0) {
    echo_output($output);
    abort("");
}

$retval = git("reset --hard",$output,$verbose,"Error while applying files!");
if ($retval != 0) {
    echo_output($output);
    abort("");
}       

echo("--------------- Files upgrade completed ---------------\n");
$retval = git("log -1 ",$output,$verbose,"Error while checking files!");
if ($retval != 0) {
    echo_output($output);
    abort("");
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
    echo ("Could not load from JSON $schema_file_name\n");
    exit;
}
echo("--------------- Comparing database '$schema@$host' vs. JSON '".$compare_def['database']."@".$compare_def['host']."' ---------------\n");
$compare_differences = mustal_compare_table_array($compare_def,"in JSON",$db_def,"in DB",true);
echo((empty($compare_differences)?0:count($compare_differences))." differences.\n");

echo("--------------- Calculating database upgrade for '$schema@$host'... ---------------\n");

$upgrade_sql = array();

$result =  mustal_calculate_db_upgrade($compare_def, $db_def, $upgrade_sql, $mustal_replacers);

if ($result != 0) {
    echo("Error: $result\n");
    exit;
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
echo("--------------- Done! ---------------\n");
exit(0);




