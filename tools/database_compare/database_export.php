<?php

/*
 * Helper to export database structures in a defined format for database comparison / upgrade
 *
 * Copyright (c) 2022 OpenXE project
 *
 */


/*
MariaDB [openxe]> SHOW FULL TABLES;
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
7 rows in set (0.002 sec)

*/

function implode_with_quote(string $quote, string $delimiter, array $array_to_implode) : string {
    return($quote.implode($quote.$delimiter.$quote, $array_to_implode).$quote);
}

$host = 'localhost';
$user = 'openxe';
$passwd = 'openxe';
$schema = 'openxe';

$target_folder = "export";
$tables_file_name_wo_folder = "tables.txt";
$delimiter = ";";
$quote = '"';

echo("\n");

if ($argc >= 1) {

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


/*    if (strpos($argv[1],'-') == 0) {
        $module_name = $argv[1];
    } else {
      info();
      exit;
    }*/

    // First get the contents of the database table structure
    $mysqli = mysqli_connect($host, $user, $passwd, $schema);

    /* Check if the connection succeeded */
    if (!$mysqli) {
        echo "Connection failed\n";
        echo "Error number: " . mysqli_connect_errno() . "\n";
        echo "Error message: " . mysqli_connect_error() . "\n";
        exit;
    }

    echo "Successfully connected!\n";  

    // Prepare files
    mkdir($target_folder);

    $tables_file_name = $target_folder."/".$tables_file_name_wo_folder;

    if (!$force && file_exists($tables_file_name)) {
        echo("File exists: " .$tables_file_name . "\n");
        echo("Use -f to force overwrite.\n");
        exit;
    }

    $tables_file = fopen($tables_file_name, "w");
    if (empty($tables_file)) {
        echo ("Failed to write to " . $tables_file_name."\n");
        exit();
    }

    // Get tables and views

    $tables = array();

    $sql = "SHOW FULL TABLES"; 
    $result = mysqli_query($mysqli, $sql);

    if (!$result) {
        echo "Query error: " . mysqli_error($mysqli);
        exit;
    } 

    $colwidth = 0;

    while ($row = mysqli_fetch_assoc($result)) {

        $table = array();
        $table['name'] = $row['Tables_in_'.$schema];
        $table['type'] = $row['Table_type'];
        $tables[] = $table; // Add table to list of tables

        if (strlen($table['name']) > $colwidth) {
            $colwidth = strlen($table['name']);
        }
    }       

    $pre_text = "Table name";
    echo(" | ".$pre_text);
    for ($filler = strlen($pre_text); $filler < $colwidth; $filler++) {
        echo(" ");
    }
    echo(" | ".'type'."\n");
    echo(" | ");
    for ($filler = 0; $filler < $colwidth; $filler++) {
        echo("-");
    }
    echo("\n");

    fwrite($tables_file, 'tablename'.$delimiter."type\n");

    foreach ($tables as $table) {
        echo(" | ".$table['name']);
        for ($filler = strlen($table['name']); $filler < $colwidth; $filler++) {
            echo(" ");
        }
        echo(" | ".$table['type']." |\n");
        fwrite($tables_file, $table['name'].$delimiter.$table['type']."\n");  
    }        
    fclose($tables_file);
    echo(" | ");
    for ($filler = 0; $filler < $colwidth; $filler++) {
        echo("-");
    }
    echo("\n");

    // Now export all colums of the tables

    $tablecount = 0;

    foreach ($tables as $table) {
        $table_file_name = $target_folder."/".$table['name'].".txt";

        if (!$force && file_exists($table_file_name)) {
            echo("File exists: " .$table_file_name . "\n");
            echo("Use -f to force overwrite.\n");
            exit;
        }

        $table_file = fopen($table_file_name, "w");
        if (empty($table_file)) {
            echo ("Failed to write to " . $table_file_name."\n");
            exit();
        }  

        $sql = "SHOW FULL COLUMNS FROM ".$table['name'];
        $result = mysqli_query($mysqli, $sql);

        if (!$result) {
            echo "Query error: " . mysqli_error($mysqli);
            exit;
        }

        $first = true;
        while ($column = mysqli_fetch_assoc($result)) {

            if ($first) {
                fwrite($table_file,implode_with_quote($quote,$delimiter,array_keys($column))."\n");  
                $first = false;
            }
            fwrite($table_file,implode_with_quote($quote,$delimiter,array_values($column))."\n");  
        }     
        $tablecount++;
    }

    echo("\nExported $tablecount tables.\n");
    echo("\n\nDone!\n");

} else {
  info();
  exit;
}

function info() {
    echo("\nOpenXE database extractor\n");
    echo("Copyright 2022 (c) OpenXE project\n\n");
    echo("\n");
    echo("Export database structures in a defined format for database comparison / upgrade\n");
    echo("arg1: ..\n");
    echo("Options\n");
    echo("\t-v: verbose output\n");
    echo("\t-f: force override of existing files\n");
    echo("\t-c: select columns like this: -c col1,col2,col3,col3\n");
    echo("\n");
}



