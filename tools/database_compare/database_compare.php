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

function implode_with_quote(string $quote, string $delimiter, array $array_to_implode) : string {
    return($quote.implode($quote.$delimiter.$quote, $array_to_implode).$quote);
}

$host = 'localhost';
$user = 'openxe';
$passwd = 'openxe';
$schema = 'openxe';

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

    if (in_array('-clean', $argv)) {
      $clean = true;
    } else {
      $clean = false;
    } 

    echo("--------------- Loading from database '$schema@$host'... ---------------\n");
    $db_def = load_tables_from_db($host, $schema, $user, $passwd);

    if (empty($db_def)) {
        echo ("Could not load from $schema@$host\n");
        exit;
    }

    echo("--------------- Loading from database '$schema@$host' complete. ---------------\n");

    if ($export) {

        echo("--------------- Export to JSON... ---------------\n");
//        $result = save_tables_to_csv($db_def, $target_folder, $tables_file_name_wo_folder, $delimiter, $quote, $keys_postfix, $force);
        $result = save_tables_to_json($db_def, $target_folder, $tables_file_name_wo_folder, $force);

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
        $compare_def = load_tables_from_json($target_folder, $tables_file_name_wo_folder);

        if (empty($compare_def)) {
            echo ("Could not load from JSON $tables_file_name_w_folder\n");
            exit;
        }
        echo("--------------- Loading from JSON complete. ---------------\n");

        // Do the comparison

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
        }

        echo("--------------- Comparing database '$schema@$host' vs. JSON '".$compare_def['database']."@".$compare_def['host']."' ---------------\n");
        $compare_differences = compare_table_array($compare_def,"in JSON",$db_def,"in DB",true);
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
        }

        echo("--------------- Comparison complete. ---------------\n");
    }

    if ($upgrade) {
        // First create all db_def that are missing in the db
        echo("--------------- Calculating database upgrade for '$schema@$host'... ---------------\n");

        $upgrade_sql = array();

        $compare_differences = compare_table_array($compare_def,"in JSON",$db_def,"in DB",true);

        foreach ($compare_differences as $compare_difference) {
            switch ($compare_difference['type']) {
                case 'Table existance':

                    // Get table definition from JSON

                    $table_name = $compare_difference['in JSON']; 

                    $table_key = array_search($table_name,array_column($compare_def,'name'));

                    if ($table_key !== false) {
                        $table = $compare_def[$table_key];

                        switch ($table['type']) {
                            case 'BASE TABLE':

                                // Create table in DB
                                $sql = "";
                                $sql = "CREATE TABLE `".$table['name']."` (";                                   
                                $comma = "";

                                foreach ($table['columns'] as $column) {
                                    $sql .= $comma.column_sql_definition($table_name, $column);                                 
                                    $comma = ",";
                                }
                                   
                            break;
                            default:
                                echo("Upgrade type '".$table['type']."' on table '".$table['name']."' not supported.\n");
                            break;
                        }
                    } else {
                        echo("Error while creating table $table_name.\n");
                    }

                break;
                case 'Column existance':
                    $table_name = $compare_difference['table']; 
                    $column_name = $compare_difference['in JSON']; 
                    $table_key = array_search($table_name,array_column($compare_def['tables'],'name'));
                    if ($table_key !== false) {
                        $table = $compare_def['tables'][$table_key];
                        $columns = $table['columns'];                  
                        $column_key = array_search($column_name,array_column($columns,'Field'));
                        if ($column_key !== false) {
                            $column = $table['columns'][$column_key];
                            $sql = "ALTER TABLE `$table_name` ADD COLUMN "; 
                            $sql .= column_sql_definition($table_name, $column);
                            $sql .= ";";                                                  
                            $upgrade_sql[] = $sql;                       
                        }
                        else {
                            echo("Error column_key while creating column '$column_name' in table '".$table['name']."'\n");
                        }
                    }
                    else {
                        echo("Error table_key while creating column '$column_name' in table '$table_name'.\n");
                    }
                    // Add Column in DB
                break;
                case 'Column definition':
                    $table_name = $compare_difference['table']; 
                    $column_name = $compare_difference['column']; 
                    $table_key = array_search($table_name,array_column($compare_def['tables'],'name'));
                    if ($table_key !== false) {
                        $table = $compare_def['tables'][$table_key];
                        $columns = $table['columns'];   

                        $column_names = array_column($columns,'Field');              
                        $column_key = array_search($column_name,$column_names); 

                        if ($column_key !== false) {
                            $column = $table['columns'][$column_key];

                            $sql = "ALTER TABLE `$table_name` MODIFY COLUMN ";
                            $sql .= column_sql_definition($table_name, $column);
                            $sql .= ";";
                            $upgrade_sql[] = $sql;
                        }
                        else {
                            echo("Error column_key while modifying column '$column_name' in table '".$table['name']."'\n");
                            exit;
                        }
                    }
                    else {
                        echo("Error table_key while modifying column '$column_name' in table '$table_name'.\n");
                    }
                    // Modify Column in DB
                break;
                case 'Table count':
                    // Nothing to do
                break;
                case 'Table type':
                   echo("Upgrade type '".$compare_difference['type']."' on table '".$compare_difference['table']."' not supported.\n");
                break;
                default:
                   echo("Upgrade type '".$compare_difference['type']."' not supported.\n");
                break;
            }
        }

        $upgrade_sql = array_unique($upgrade_sql);

        if ($verbose) {
            foreach($upgrade_sql as $statement) {
                echo($statement."\n");
            }
        }

        echo(count($upgrade_sql)." upgrade statements\n");
        echo("--------------- Database upgrade calculated for '$schema@$host' (show SQL with -v). ---------------\n");
    }

    echo("--------------- Done. ---------------\n");

    echo("\n");

} else {
  info();
  exit;
}

// Load all db_def from a DB connection into a db_def array

function load_tables_from_db(string $host, string $schema, string $user, string $passwd) : array {

    // First get the contents of the database table structure
    $mysqli = mysqli_connect($host, $user, $passwd, $schema);

    /* Check if the connection succeeded */
    if (!$mysqli) {
        return(array());
    }

    // Get db_def and views

    $sql = "SHOW FULL tables"; 
    $query_result = mysqli_query($mysqli, $sql);
    if (!$query_result) {
        return(array());
    } 
    while ($row = mysqli_fetch_assoc($query_result)) {
        $table = array();
        $table['name'] = $row['Tables_in_'.$schema];
        $table['type'] = $row['Table_type'];
        $tables[] = $table; // Add table to list of tables
    }

    // Get and add columns of the table
    foreach ($tables as &$table) {    
        $sql = "SHOW FULL COLUMNS FROM ".$table['name'];
        $query_result = mysqli_query($mysqli, $sql);

        if (!$query_result) {
            return(array());
        }

        $columns = array();
        while ($column = mysqli_fetch_assoc($query_result)) {
            $columns[] = $column; // Add column to list of columns
        }     
        $table['columns'] = $columns;       

        $sql = "SHOW KEYS FROM ".$table['name'];
        $query_result = mysqli_query($mysqli, $sql);
        if (!$query_result) {
            return(array());
        }       
        $keys = array();
        while ($key = mysqli_fetch_assoc($query_result)) {            
            $keys[] = $key; // Add key to list of keys
        }     

        // Compose comparable format for keys

        $refined_keys = array();

        foreach ($keys as &$key) {

            $key_pos = array_search($key['Key_name'],array_column($refined_keys,'Key_name'),true);

            if ($key_pos !== false) {
                $refined_key = &$refined_keys[$key_pos];
                $refined_key['columns'] = $refined_key['columns'].", ".$key['Column_name'];                
            } else {
                $refined_key['Key_name'] = $key['Key_name'];
                $refined_key['Non_unique'] = $key['Non_unique'];
                $refined_key['columns'] = $key['Column_name'];
                $refined_keys[] = $refined_key;
            }
        }
        unset($key);
        $table['keys'] = $refined_keys;       

    }   
    unset($table);    

    $result = array();
    $result['host'] = $host;
    $result['database'] = $schema;
    $result['user'] = $user;
    $result['tables'] = $tables;

    return($result);   
}

function save_tables_to_json(array $db_def, string $path, string $tables_file_name, bool $force) : int {
  
    // Prepare db_def file
    if (!is_dir($path)) {
        mkdir($path);
    }
    if (!$force && file_exists($path."/".$tables_file_name)) {
        return(2);
    }

    $tables_file = fopen($path."/".$tables_file_name, "w");
    if (empty($tables_file)) {
        return(2);
    }

    fwrite($tables_file, json_encode($db_def,JSON_PRETTY_PRINT));

    fclose($tables_file);
    return(0);
}

// Load all db_def from JSON file
function load_tables_from_json(string $path, string $tables_file_name) : array {
    
    $db_def = array();

    $contents = file_get_contents($path."/".$tables_file_name);

    if (!$contents) {
        return(array());
    }

    $db_def = json_decode($contents, true);

    return($db_def);
}

// Compare two definitions
// Report based on the first array
// Return Array
function compare_table_array(array $nominal, string $nominal_name, array $actual, string $actual_name, bool $check_column_definitions) : array {

    $compare_differences = array();

    if (count($nominal['tables']) != count($actual['tables'])) {
        $compare_difference = array();
        $compare_difference['type'] = "Table count";
        $compare_difference[$nominal_name] = count($nominal['tables']);
        $compare_difference[$actual_name] = count($actual['tables']);
        $compare_differences[] = $compare_difference;
    }

    foreach ($nominal['tables'] as $database_table) {
        
        $found_table = array(); 
        foreach ($actual['tables'] as $compare_table) {
            if ($database_table['name'] == $compare_table['name']) {
                $found_table = $compare_table;
                break;
            }
        }
        unset($compare_table);

        if ($found_table) {

            // Check type table vs view

            if ($database_table['type'] != $found_table['type']) {
                $compare_difference = array();
                $compare_difference['type'] = "Table type";
                $compare_difference['table'] = $database_table['name'];
                $compare_difference[$nominal_name] = $database_table['type'];
                $compare_difference[$actual_name] = $found_table['type'];
                $compare_differences[] = $compare_difference;
            }
          
            // Check columns
            $compare_table_columns = array_column($found_table['columns'],'Field');
            foreach ($database_table['columns'] as $column) {

                $column_name_to_find = $column['Field'];
                $column_key = array_search($column_name_to_find,$compare_table_columns,true);
                if ($column_key !== false) {
                        
                    // Compare the properties of the columns
                    if ($check_column_definitions) {
                        $found_column = $found_table['columns'][$column_key];
                        foreach ($column as $key => $value) {                            
                            if ($found_column[$key] != $value) {

//                                if ($key != 'permissions') {                                
                                    $compare_difference = array();
                                    $compare_difference['type'] = "Column definition";
                                    $compare_difference['table'] = $database_table['name'];
                                    $compare_difference['column'] = $column['Field'];
                                    $compare_difference['property'] = $key;
                                    $compare_difference[$nominal_name] = $value;
                                    $compare_difference[$actual_name] = $found_column[$key];
                                    $compare_differences[] = $compare_difference;
//                                }
                            }
                        }
                        unset($value);                          
                    } // $check_column_definitions
                } else {
                    $compare_difference = array();
                    $compare_difference['type'] = "Column existance";
                    $compare_difference['table'] = $database_table['name'];
                    $compare_difference[$nominal_name] = $column['Field'];
                    $compare_differences[] = $compare_difference;
                }
            } 
            unset($column); 


            // Check keys
            $compare_table_sql_indexs = array_column($found_table['keys'],'Key_name');
            foreach ($database_table['keys'] as $sql_index) {

                $sql_index_name_to_find = $sql_index['Key_name'];
                $sql_index_key = array_search($sql_index_name_to_find,$compare_table_sql_indexs,true);
                if ($sql_index_key !== false) {
                        
                    // Compare the properties of the sql_indexs
                    if ($check_column_definitions) {
                        $found_sql_index = $found_table['keys'][$sql_index_key];
                        foreach ($sql_index as $key => $value) {                            
                            if ($found_sql_index[$key] != $value) {

//                                if ($key != 'permissions') {                                
                                    $compare_difference = array();
                                    $compare_difference['type'] = "key definition";
                                    $compare_difference['table'] = $database_table['name'];
                                    $compare_difference['key'] = $sql_index['Key_name'];
                                    $compare_difference['property'] = $key;
                                    $compare_difference[$nominal_name] = $value;
                                    $compare_difference[$actual_name] = $found_sql_index[$key];
                                    $compare_differences[] = $compare_difference;
//                                }
                            }
                        }
                        unset($value);                          
                    } // $check_sql_index_definitions
                } else {
                    $compare_difference = array();
                    $compare_difference['type'] = "key existance";
                    $compare_difference['table'] = $database_table['name'];
                    $compare_difference[$nominal_name] = $sql_index['Key_name'];
                    $compare_differences[] = $compare_difference;
                }
            } 
            unset($sql_index); 


        } else {
            $compare_difference = array();
            $compare_difference['type'] = "Table existance";
            $compare_difference[$nominal_name] = $database_table['name'];
            $compare_differences[] = $compare_difference;
        }
    }
    unset($database_table);

    return($compare_differences);
}


// Generate SQL to create or modify column
function column_sql_definition(string $table_name, array $column) : string {    

    // These default values will not be in quotes
    $mysql_default_values_without_quote = array('current_timestamp()');

    if ($column['Null'] == "NO") {
        $column['Null'] = " NOT NULL"; // Idiotic...
    }
    if ($column['Null'] == "YES") {
        $column['Null'] = " NULL"; // Also Idiotic...
    }

    if ($column['Default'] != '') {
        // Check for MYSQL function call as default                
        if (in_array(strtolower($column['Default']),$mysql_default_values_without_quote)) {
            $quote = "";
        } else {
            // Remove quotes if there are
            $column['Default'] = trim($column['Default'],"'");
            $quote = "'";
        }
        $column['Default'] = " DEFAULT $quote".$column['Default']."$quote"; 
    }

    if ($column['Extra'] != '')  {
        $column['Extra'] = " ".$column['Extra'];
    }


    if ($column['Collation'] != '')  {
        $column['Collation'] = " COLLATE ".$column['Collation'];
    }

    $sql =                             
        "`".$column['Field']."` ".
        $column['Type'].
        $column['Null'].
        $column['Default'].
        $column['Extra'].
        $column['Collation'];

    return($sql);
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
    echo("\t-upgrade: Create the needed SQL to upgrade the database to match the JSON\n");
    echo("\t-clean: (not yet implemented) Create the needed SQL to remove items from the database not in the JSON\n");
    echo("\n");
}

