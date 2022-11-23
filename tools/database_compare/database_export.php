<?php

/*
 * Helper to compare database structures from files vs. database
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
$tables_file_name_wo_folder = "0-tables.txt";
$tables_file_name = $target_folder."/".$tables_file_name_wo_folder;
$delimiter = ";";
$quote = '"';

$color_red = "\033[31m";
$color_green = "\033[32m";
$color_yellow = "\033[33m";
$color_default = "\033[39m";

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
        echo "Error message: '" . mysqli_connect_error() . "'\n\n";
        exit;
    }

    echo "--------------- Successfully connected! --------------- \n";  
    echo("--------------- Loading from database... ---------------\n");

    // Get tables and views
    $tables = array();
    $sql = "SHOW FULL TABLES"; 
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        echo "Query error: '" . mysqli_error($mysqli)."'";
        exit;
    } 
    $tables = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $table = array();
        $table['name'] = $row['Tables_in_'.$schema];
        $table['type'] = $row['Table_type'];
        $tables[] = $table; // Add table to list of tables
    }

    // Get and add columns of the table
    foreach ($tables as &$table) {    
        $sql = "SHOW FULL COLUMNS FROM ".$table['name'];
        $result = mysqli_query($mysqli, $sql);

        if (!$result) {
            echo "Query error: '" . mysqli_error($mysqli)."'\n\n";
            exit;
        }

        $columns = array();
        while ($column = mysqli_fetch_assoc($result)) {
            $columns[] = $column; // Add column to list of columns
        }     

        $table['columns'] = $columns;     

        if ($verbose) {
                echo("Table '".$table['name']."' of type '".$table['type']."'\n");   
        }
   
    }   
    unset($table);    
    // ------ IMPORT COMPLETE ------

    echo("--------------- Loading from database complete. ---------------\n");


    if ($export) {

        echo("--------------- Export to CSV... ---------------\n");

        // Prepare tables file
        if (!is_dir($target_folder)) {
            mkdir($target_folder);
        }
        if (!$force && file_exists($tables_file_name)) {
            echo("File exists: '" .$tables_file_name . "'!\n");
            echo("Use -f to force overwrite.\n\n");
            exit;
        }

        $tables_file = fopen($tables_file_name, "w");
        if (empty($tables_file)) {
            echo ("Failed to write to '" . $tables_file_name."'!\n\n");
            exit();
        }

        $first_table = true;
        // Now export all colums of the tables
        foreach ($tables as $export_table) {
        
            if ($verbose) {
                echo("Table '".$export_table['name']."' of type '".$export_table['type']."' loaded from database.\n");   
            }
            if ($first_table) {
                $first_table = false;
                fwrite($tables_file,$quote.'name'.$quote.$delimiter.$quote.'type'.$quote."\n");  
            }
            fwrite($tables_file,$quote.$export_table['name'].$quote.$delimiter.$quote.$export_table['type'].$quote."\n");  

            // Prepare export_table file
            $table_file_name = $target_folder."/".$export_table['name'].".txt";
            if (!$force && file_exists($table_file_name)) {
                echo("File exists: '" .$table_file_name . "'!\n");
                echo("Use -f to force overwrite.\n\n");
                exit;
            }
            $table_file = fopen($table_file_name, "w");
            if (empty($table_file)) {
                echo ("Failed to write to '" . $table_file_name."'!\n\n");
                exit();
            }  

            $first_column = true;

            foreach ($export_table['columns'] as $column) {
                if ($first_column) {
                    $first_column = false;
                    fwrite($table_file,implode_with_quote($quote,$delimiter,array_keys($column))."\n");  
                }
                fwrite($table_file,implode_with_quote($quote,$delimiter,array_values($column))."\n");  
            }
            unset($column);

            fclose($table_file);
        }
        unset($export_table);

        echo("Exported ".count($tables)." tables.\n");
        fwrite($tables_file,"\n");  
        fclose($tables_file);
        echo("--------------- Export to CSV complete. ---------------\n");
    }

    if ($compare) {

        // Results here as ['text'] ['diff']
        $compare_differences = array();

        echo("--------------- Loading from CSV... ---------------\n");

        $compare_tables = array();
        $first_table = true;
        $tables_file = fopen($tables_file_name, "r");

        if (!$tables_file) {
            echo("File not found: '" .$tables_file_name . "'\n");
            echo("\n");
            exit;
        }

        while (($csv_line = fgetcsv($tables_file,0,$delimiter,$quote)) !== FALSE) {

            if ($first_table) {
                $first_table = false;
            } else if (count($csv_line) == 2) {
                $new_compare_table = array();
                $new_compare_table['name'] = $csv_line['0'];
                $new_compare_table['type'] = $csv_line['1'];
                $compare_tables[] = $new_compare_table;

                if ($verbose) {
                    echo("Table '".$new_compare_table['name']."' loaded from CSV '$tables_file_name'.\n");
                }

            } else {
                         
            }
        }
        fclose($tables_file);

        // Get columns for each compare_table

        foreach ($compare_tables as &$compare_table) {
    
            $table_file_name = $target_folder."/".$compare_table['name'].".txt";
            if (!file_exists($table_file_name)) {
                echo("File not found: '" .$table_file_name . "'\n");
                echo("\n");
                exit;
            }
            $table_file = fopen($table_file_name, "r");
            if (empty($table_file)) {
                echo ("Failed to open '" . $table_file_name."'\n\n");
                exit();
            }  

            $first_column = true;
            $column_headers = array();
            $columns = array();
            $column = array();
            while (($csv_line = fgetcsv($table_file,0,$delimiter,$quote)) !== FALSE) {

                if ($first_column) {
                    $first_column = false;
                    $column_headers = $csv_line;
                } else {                    
                    for ($cr = 0;$cr < count($csv_line);$cr++) {
                        $column[$column_headers[$cr]] = $csv_line[$cr];
                    }   
                    $columns[] = $column;                                     
                }
            }            

            $compare_table['columns'] = $columns;

            if ($verbose) {
               echo("Colums loaded for '".$compare_table['name']."' from CSV $table_file_name. \n");
            }
        }
        unset($compare_table);

        echo("--------------- Loading from CSV complete. ---------------\n");

        // Do the comparison

        echo("--------------- Comparison... ---------------\n");

        echo("Number of tables: ".count($tables)." in Database, ".count($compare_tables)." in CSV.\n");

        if (count($tables) != count($compare_tables)) {

        }

   
        foreach ($tables as $database_table) {
            
            $found_table = array(); 
            foreach ($compare_tables as $compare_table) {
                if ($database_table['name'] == $compare_table['name']) {
                    $found_table = $compare_table;
                    break;
                }
            }
            unset($compare_table);

            if ($found_table) {

                if ($verbose) {
                    echo("Table '".$database_table['name']."' found in CSV '$tables_file_name'.\n");
                }              
              
                // Check columns
                $compare_table_columns = array_column($found_table['columns'],'Field');

                foreach ($database_table['columns'] as $column) {

                    $column_name_to_find = $column['Field'];
                    $column_key = array_search($column_name_to_find,$compare_table_columns,true);
                    if ($column_key !== false) {
                            
                        if ($verbose) {
                            echo("Column '".$column['Field']."' from table '".$database_table['name']."' in table '".$found_table['name']."' found in CSV.\n");
                        }

                        $column_diff = array_diff($column,$found_table['columns'][$column_key]);
                            
                        if (!empty($column_diff)) {
                            $compare_difference = array();
                            $compare_difference['text'] = $color_red."Difference:".$color_default." Column '".$column['Field']."' from table '".$database_table['name']."' is different from '".$found_table['name']."' in CSV.\n";
                            $compare_difference['diff'] = $column_diff;
                            $compare_differences[] = $compare_difference;
                            echo($compare_difference['text']);
                        }
                    } else {
                        $compare_difference = array();
                        $compare_difference['text'] = $color_red."Difference:".$color_default." Column '".$column['Field']."' from table '".$database_table['name']."' in table '".$found_table['name']."' not found in CSV.\n";
                        $compare_difference['diff'] = $column_diff;
                        $compare_differences[] = $compare_difference;
                        echo($compare_difference['text']);          
                    }
                } 
                unset($column); 
            } else {
                $compare_difference = array();
                $compare_difference['text'] = $color_red."Difference:".$color_default." Table '".$database_table['name']."' not found in CSV '$tables_file_name'.\n";
                $compare_difference['diff'] = $column_diff;
                $compare_differences[] = $compare_difference;
                echo($compare_difference['text']);     
            }
        }
        unset($database_table);

        echo("\nComparison found ".(empty($compare_differences)?0:count($compare_differences))." differences.\n");
        
        echo("--------------- Comparison complete. ---------------\n");

    }

    echo("--------------- Done! ---------------\n");

    echo("\n");

} else {
  info();
  exit;
}

function info() {
    echo("\nOpenXE database extractor\n");
    echo("Copyright 2022 (c) OpenXE project\n\n");
    echo("\n");
    echo("Export database structures in a defined format for database comparison / upgrade\n");
    echo("Options\n");
    echo("\t-v: verbose output\n");
    echo("\t-f: force override of existing files\n");
    echo("\t-e: export database structure to files\n");
    echo("\t-c: compare content of files with database structure\n");
    echo("\n");
}



