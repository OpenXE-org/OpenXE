<?php

/*
 * Helper to compare database structures from files vs. database
 *
 * Copyright (c) 2022 OpenXE project
 *
 */


/*
MariaDB [openxe]> SHOW FULL db_def;
+----------------------------------------------+------------+
| Tables_in_openxe                             | Table_type |
+----------------------------------------------+------------+
| abrechnungsartikel                           | BASE TABLE |
| abrechnungsartikel_gruppe                    | BASE TABLE |
| abschlagsrechnung_rechnung                   | BASE TABLE |
| accordion                                    | BASE TABLE |
| adapterbox                                   | BASE TABLE |
| adapterbox_log                               | BASE TABLE |
| adapterbox_request_log                       | BASE TABLE |
| adresse                                      | BASE TABLE |
| adresse_abosammelrechnungen                  | BASE TABLE |
| adresse_accounts                             | BASE TABLE |
| adresse_filter                               | BASE TABLE |
| adresse_filter_gruppen                       | BASE TABLE |
...


MariaDB [openxe]> SHOW FULL COLUMNS FROM wiki;
+-------------------+--------------+--------------------+------+-----+---------+----------------+---------------------------------+---------+
| Field             | Type         | Collation          | Null | Key | Default | Extra          | Privileges                      | Comment |
+-------------------+--------------+--------------------+------+-----+---------+----------------+---------------------------------+---------+
| id                | int(11)      | NULL               | NO   | PRI | NULL    | auto_increment | select,insert,update,references |         |
| name              | varchar(255) | utf8mb3_general_ci | YES  | MUL | NULL    |                | select,insert,update,references |         |
| content           | longtext     | utf8mb3_general_ci | NO   |     | NULL    |                | select,insert,update,references |         |
| lastcontent       | longtext     | utf8mb3_general_ci | NO   |     | NULL    |                | select,insert,update,references |         |
| wiki_workspace_id | int(11)      | NULL               | NO   |     | 0       |                | select,insert,update,references |         |
| parent_id         | int(11)      | NULL               | NO   |     | 0       |                | select,insert,update,references |         |
| language          | varchar(32)  | utf8mb3_general_ci | NO   |     |         |                | select,insert,update,references |         |
+-------------------+--------------+--------------------+------+-----+---------+----------------+---------------------------------+---------+

MariaDB [openxe]> show keys from wiki;
+-------+------------+----------+--------------+-------------------+-----------+-------------+----------+--------+------+------------+---------+---------------+---------+
| Table | Non_unique | Key_name | Seq_in_index | Column_name       | Collation | Cardinality | Sub_part | Packed | Null | Index_type | Comment | Index_comment | Ignored |
+-------+------------+----------+--------------+-------------------+-----------+-------------+----------+--------+------+------------+---------+---------------+---------+
| wiki  |          0 | PRIMARY  |            1 | id                | A         |         244 |     NULL | NULL   |      | BTREE      |         |               | NO      |
| wiki  |          0 | suche    |            1 | name              | A         |         244 |     NULL | NULL   | YES  | BTREE      |         |               | NO      |
| wiki  |          0 | suche    |            2 | wiki_workspace_id | A         |         244 |     NULL | NULL   |      | BTREE      |         |               | NO      |
| wiki  |          1 | name     |            1 | name              | A         |         244 |     NULL | NULL   | YES  | BTREE      |         |               | NO      |
+-------+------------+----------+--------------+-------------------+-----------+-------------+----------+--------+------+------------+---------+---------------+---------+

*/

require('mustal_mysql_upgrade_tool.php');

$connection_info_file_name = "connection_info.json";
$target_folder = ".";
$tables_file_name_wo_folder = "db_schema.json";
$tables_file_name_w_folder = $target_folder."/".$tables_file_name_wo_folder;
$delimiter = ";";
$quote = '"';

$sql_file_name = "upgrade.sql";

$color_red = "\033[31m";
$color_green = "\033[32m";
$color_yellow = "\033[33m";
$color_default = "\033[39m";

// -------------------------------- START

echo("\n");

if ($argc > 1) {

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

    if (in_array('-e', $argv)) {
      $export = true;
    } else {
      $export = false;
    } 

    if (in_array('-c', $argv)) {
      $compare = true;
    } else {
      $compare = false;
    } 

    if (in_array('-i', $argv)) {
      $onlytables = true;
    } else {
      $onlytables = false;
    } 

    if (in_array('-upgrade', $argv)) {
      $upgrade = true;
    } else {
      $upgrade = false;
    } 

    if (in_array('-do', $argv)) {
      $doupgrade = true;
    } else {
      $doupgrade = false;
    } 

    if (in_array('-clean', $argv)) {
      $clean = true;
    } else {
      $clean = false;
    } 

    if (in_array('-utf8fix', $argv)) {
      $utf8fix = true;
    } else {
      $utf8fix = false;
    } 

    $connection_info_contents = file_get_contents($connection_info_file_name);
    if (!$connection_info_contents) {
        echo("Unable to load $connection_info_file_name\n");
        exit;
    }
    $connection_info = json_decode($connection_info_contents, true);

    $host = $connection_info['host'];
    $user = $connection_info['user'];
    $passwd = $connection_info['passwd'];
    $schema = $connection_info['database'];

    echo("--------------- Loading from database '$schema@$host'... ---------------\n");
    $db_def = mustal_load_tables_from_db($host, $schema, $user, $passwd, $mustal_replacers);

    if (empty($db_def)) {
        echo ("Could not load from $schema@$host\n");
        exit;
    }

    echo("--------------- Loading from database '$schema@$host' complete. ---------------\n");

    if ($export) {

        echo("--------------- Export to JSON... ---------------\n");
//        $result = save_tables_to_csv($db_def, $target_folder, $tables_file_name_wo_folder, $delimiter, $quote, $keys_postfix, $force);
        $result = mustal_save_tables_to_json($db_def, $target_folder, $tables_file_name_wo_folder, $force);

        if ($result != 0) {

            $result_texts = array("ok","key postfix error","table list file error","table file error","key file error");

            echo ("Could not save to JSON (".$result_texts[$result]."). To overwrite, use -f.\n");
            exit;
        }
    
        echo("Exported ".count($db_def['tables'])." tables.\n");
        echo("--------------- Export to JSON ($tables_file_name_w_folder) complete. ---------------\n");
    }

    if ($compare || $upgrade) {

        // Results here as ['text'] ['diff']
        $compare_differences = array();

        echo("--------------- Loading from JSON... ---------------\n");
        $compare_def = mustal_load_tables_from_json($target_folder, $tables_file_name_wo_folder);

        if (empty($compare_def)) {
            echo ("Could not load from JSON $tables_file_name_w_folder\n");
            exit;
        }
        echo("--------------- Loading from JSON complete. ---------------\n");

        // Do the comparison
/*
        echo("--------------- Comparing JSON '".$compare_def['database']."@".$compare_def['host']."' vs. database '$schema@$host' ---------------\n");

        echo(count($compare_def['tables'])." tables in JSON, ".count($db_def['tables'])." tables in database.\n");
        $compare_differences = compare_table_array($db_def,"in DB",$compare_def,"in JSON",false);
        echo("Comparison found ".(empty($compare_differences)?0:count($compare_differences))." differences.\n");
    
        if ($verbose) {
            foreach ($compare_differences as $compare_difference) {
                $comma = "";
                foreach ($compare_difference as $key => $value) {
                    echo($comma."$key => '$value'");
                    $comma = ", ";
                }
                echo("\n");
            }           
        }*/

        echo("--------------- Comparing database '$schema@$host' vs. JSON '".$compare_def['database']."@".$compare_def['host']."' ---------------\n");

        if($utf8fix) {
            $column_collation_aliases = array(
                ['utf8mb3_general_ci','utf8_general_ci']
            );
        } else {
            $column_collation_aliases = array();
        }

        $compare_differences = mustal_compare_table_array($compare_def,"in JSON",$db_def,"in DB",true,$column_collation_aliases);
        echo((empty($compare_differences)?0:count($compare_differences))." differences.\n");

        if ($verbose) {
            foreach ($compare_differences as $compare_difference) {
                $comma = "";
                foreach ($compare_difference as $key => $value) {
                    echo($comma."$key => [$value]");
                    $comma = ", ";
                }
                echo("\n");
            }           
        }
        echo("--------------- Comparison complete. ---------------\n");
    }

    if ($upgrade) {
        // First create all db_def that are missing in the db
        echo("--------------- Calculating database upgrade for '$schema@$host'... ---------------\n");

        $upgrade_sql = array();

        $result =  mustal_calculate_db_upgrade($compare_def, $db_def, $upgrade_sql, $mustal_replacers);

        if ($result != 0) {
            echo("Error: $result\n");
            exit;
        }

        if (!empty($result)) {
            echo(count($result)." errors.\n");
            if ($verbose) {
                foreach($result as $error) {
                    echo("Code: ".$error[0]." '".$error[1]."'.");
                }
            }
            return(-1);
        }

        echo("--------------- Database upgrade for '$schema@$host'... ---------------\n");
        if ($verbose) {
            foreach($upgrade_sql as $statement) {
                echo($statement."\n");
            }
        }

        echo(count($upgrade_sql)." upgrade statements\n");
        echo("--------------- Database upgrade calculated for '$schema@$host' (show SQL with -v). ---------------\n");

        if ($doupgrade) {
            echo("--------------- Executing database upgrade for '$schema@$host' database will be written! ---------------\n");            

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

                    if ($verbose) {
                        echo("(".substr($sql,0,100)."...) ");
                    }

                    $query_result = mysqli_query($mysqli, $sql);
                    if (!$query_result) {
                        $error = " not ok: ". mysqli_error($mysqli)."\n";
                        echo($error);
                        file_put_contents("./errors.txt",$error.$sql."\n",FILE_APPEND);
                        $error_counter++;
                    } else {
                        echo("ok.\r");
                    }

                }

                echo("--------------- Executing database upgrade for '$schema@$host' executed. ---------------\n");
                echo("$error_counter errors.\n");
                echo("--------------- Executing database upgrade for '$schema@$host' done. ---------------\n");

                echo("--------------- Checking database upgrade for '$schema@$host'... ---------------\n");
                $db_def = mustal_load_tables_from_db($host, $schema, $user, $passwd, $mustal_replacers);

                echo("--------------- Comparing database '$schema@$host' vs. JSON '".$compare_def['database']."@".$compare_def['host']."' ---------------\n");
                $compare_differences = mustal_compare_table_array($compare_def,"in JSON",$db_def,"in DB",true);
                echo((empty($compare_differences)?0:count($compare_differences))." differences.\n");

            }
        }


    } // upgrade

    echo("--------------- Done. ---------------\n");

    echo("\n");

} else {
  info();
  exit;
}

function info() {
    echo("OpenXE database compare\n");
    echo("Copyright 2022 (c) OpenXE project\n");
    echo("\n");
    echo("Export database structures in a defined format for database comparison / upgrade\n");
    echo("Options:\n");
    echo("\t-v: verbose output\n");
    echo("\t-f: force override of existing files\n");
    echo("\t-e: export database structure to files\n");
    echo("\t-c: compare content of files with database structure\n");
    echo("\t-i: ignore column definitions\n");
    echo("\t-utf8fix: apply fix for 'utf8' != 'utf8mb3'\n");
    echo("\t-upgrade: Create the needed SQL to upgrade the database to match the JSON\n");
    echo("\t-do: Execute the SQL to upgrade the database to match the JSON (risky!)\n");
    echo("\t-clean: (not yet implemented) Create the needed SQL to remove items from the database not in the JSON\n");
    echo("\n");
}

