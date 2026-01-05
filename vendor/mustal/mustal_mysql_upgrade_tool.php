<?php

/*
MUSTAL Mysql Upgrade Schema Tool by Alex Ledis
Helper to compare database structures from JSON files vs. database and upgrade database
Copyright (c) 2022 Alex Ledis
Licensed under AGPL v3

Version 1.0

function mustal_load_tables_from_db(string $host, string $schema, string $user, string $passwd, $replacers) : array
Load structure from db connection to an array.

function mustal_save_tables_to_json(array $db_def, string $path, string $tables_file_name, bool $force) : int
Save structure from array to a JSON file.

function mustal_load_tables_from_json(string $path, string $tables_file_name) : array
Load structure from JSON file into array.

function mustal_compare_table_array(array $nominal, string $nominal_name, array $actual, string $actual_name, bool $check_column_definitions) : array
Compare two database structures
Returns a structured array containing information on all the differences.

function mustal_calculate_db_upgrade(array $compare_def, array $db_def, array &$upgrade_sql, array $replacers, bool $strict, bool $drop_keys) : array
Generate the SQL needed to upgrade the database to match the definition, based on a comparison.

Data structure in Array and JSON
{
    "host": "hostname",
    "database": "schemaname",
    "user": "username",
    "tables": [
        {
            "name": "",
            "type": "",
            "columns": [
                {
                    "Field": "",
                    "Type": "",
                    "Collation": "",
                    "Null": "",
                    "Key": "",
                    "Default": "",
                    "Extra": "",
                    "Privileges": "",
                    "Comment": ""
                }
            ],
            "keys": [
                {
                    "Key_name": "",
                    "columns": [
                        "",
                        ""
                    ]
                }
            ]
        }
    ]
}

*/

// These default values will not be in quotes, converted to lowercase and be replaced by the second entry
$mustal_replacers = [
    ['current_timestamp','current_timestamp()'],
    ['on update current_timestamp','on update current_timestamp()']
];


// Load all db_def from a DB connection into a db_def array
function mustal_load_tables_from_db(string $host, string $schema, string $user, string $passwd, array $replacers) : array {

    $tables = array();
    $views = array();

    // First get the contents of the database table structure
    $mysqli = mysqli_connect($host, $user, $passwd, $schema);

    /* Check if the connection succeeded */
    if (!$mysqli) {
        return(array());
    }

    // Get db_def and views
    $sql = "SHOW TABLE STATUS WHERE engine IS NOT NULL";
    $query_result = mysqli_query($mysqli, $sql);
    if (!$query_result) {
        return(array());
    }
    while ($row = mysqli_fetch_assoc($query_result)) {
        $table = array();
        $table['name'] = $row['Name'];
        $table['collation'] = $row['Collation'];
        $table['type'] = 'BASE TABLE';
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
            // Do some harmonization

            if ($column['Default'] !== NULL) {
                mustal_sql_replace_reserved_functions($column,$replacers);
                $column['Default'] = mustal_mysql_put_text_type_in_quotes($column['Type'],$column['Default']);
            }

            if (empty($column['Collation']) && mustal_is_string_type($column['Type'])) {
                $column['Collation'] = $table['collation'];
            }

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
        $composed_keys = array();
        foreach ($keys as $key) {

            // Check if this key exists already
            $key_pos = array_search($key['Key_name'],array_column($composed_keys,'Key_name'));

            if ($key_pos === false) {
                // New key
                $composed_key = array();
                $composed_key['Key_name'] = $key['Key_name'];
                $composed_key['Index_type'] = $key['Index_type'];
                $composed_key['columns'][] = $key['Column_name'];

                if ($key['Key_name'] != 'PRIMARY') {
                    $composed_key['Non_unique'] = ($key['Non_unique'] == 1)?'':'UNIQUE';
                } else {
                   $composed_key['Non_unique'] = '';
                }

                $composed_keys[] = $composed_key;
            } else {
                // Given key, add column
                $composed_keys[$key_pos]['columns'][] .= $key['Column_name'];
            }
        }
        unset($key);
        $table['keys'] = $composed_keys;
        unset($composed_keys);
    }
    unset($table);

    $sql = "SHOW FULL tables WHERE Table_type = 'VIEW'";
    $query_result = mysqli_query($mysqli, $sql);
    if (!$query_result) {
        return(array());
    }
    while ($row = mysqli_fetch_assoc($query_result)) {
        $view = array();
        $view['name'] = $row['Tables_in_'.$schema];
        $view['type'] = $row['Table_type'];
        $views[] = $view; // Add view to list of views
    }

    foreach ($views as &$view) {
        $sql = "SHOW CREATE VIEW ".$view['name'];

        try {
            $query_result = mysqli_query($mysqli, $sql);
        }
        catch (exception $e) {
            $query_result = false; // VIEW is erroneous
        }
        if (!$query_result) {
            $view['Create'] = '';
            continue;
        }
        $viewdef = mysqli_fetch_assoc($query_result);

        // Remove the security info from view definition
        $view['Create'] = "CREATE ".stristr($viewdef['Create View'],"VIEW");
    }

    $result = array();
    $result['host'] = $host;
    $result['database'] = $schema;
    $result['user'] = $user;
    $result['tables'] = $tables;
    $result['views'] = $views;
    return($result);
}

function mustal_save_tables_to_json(array $db_def, string $path, string $tables_file_name, bool $force) : int {

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
function mustal_load_tables_from_json(string $path, string $tables_file_name) : array {

    $db_def = array();

    $contents = file_get_contents($path."/".$tables_file_name);

    if (!$contents) {
        return(array());
    }

    $db_def = json_decode($contents, true);

    if (!$db_def) {
        return(array());
    }

    return($db_def);
}

// Compare two definitions
// Report based on the first array
// Return Array

function mustal_compare_table_array(array $nominal, string $nominal_name, array $actual, string $actual_name, bool $check_column_definitions, bool $utf8fix) : array {

    $compare_differences = array();

    if($utf8fix) {
        $column_collation_aliases = array(
            ['utf8mb3_general_ci','utf8_general_ci'],
            ['utf8mb3_unicode_ci','utf8_unicode_ci'],
            ['utf8mb3_bin','utf8_bin']
        );
    } else {
        $column_collation_aliases = array();
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

            // Only BASE TABLE supported now
            if ($found_table['type'] != 'BASE TABLE') {
                continue;
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

                            // Apply aliases
                            if (!empty($column_collation_aliases)) {
                                foreach($column_collation_aliases as $column_collation_alias) {
                                    if ($value == $column_collation_alias[0]) {
                                        $value = $column_collation_alias[1];
                                    }
                                    if ($found_column[$key] == $column_collation_alias[0]) {
                                        $found_column[$key] = $column_collation_alias[1];
                                    }
                                }
                            }
                            if ($found_column[$key] != $value) {
                                if ($key != 'Key' || ($key == 'Key' && ($value == 'PRI' || $found_column[$key] == 'PRI'))) { // Keys will be handled separately, except primary
                                    $compare_difference = array();
                                    $compare_difference['type'] = "Column definition";
                                    $compare_difference['table'] = $database_table['name'];
                                    $compare_difference['column'] = $column['Field'];
                                    $compare_difference['property'] = $key;
                                    $compare_difference[$nominal_name] = $value;
                                    $compare_difference[$actual_name] = $found_column[$key];
                                    $compare_differences[] = $compare_difference;
                                }
                            }
                        }
                        unset($value);
                    } // $check_column_definitions
                } else {
                    $compare_difference = array();
                    $compare_difference['type'] = "Column existence";
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
                                $compare_difference = array();
                                $compare_difference['type'] = "Key definition";
                                $compare_difference['table'] = $database_table['name'];
                                $compare_difference['key'] = $sql_index['Key_name'];
                                $compare_difference['property'] = $key;
                                $compare_difference[$nominal_name] = $value;
                                $compare_difference[$actual_name] = $found_sql_index[$key];
                                $compare_differences[] = $compare_difference;
                            }
                        }
                        unset($value);
                    } // $check_sql_index_definitions
                } else {
                    $compare_difference = array();
                    $compare_difference['type'] = "Key existence";
                    $compare_difference['table'] = $database_table['name'];
                    $compare_difference[$nominal_name] = $sql_index['Key_name'];
                    $compare_differences[] = $compare_difference;
                }
            }
            unset($sql_index);


        } else {
            $compare_difference = array();
            $compare_difference['type'] = "Table existence";
            $compare_difference[$nominal_name] = $database_table['name'];
            $compare_differences[] = $compare_difference;
        }
    }
    unset($database_table);

    foreach ($nominal['views'] as $database_view) {
        $found_view = array();
        foreach ($actual['views'] as $compare_view) {
            if ($database_view['name'] == $compare_view['name']) {
                $found_view = $compare_view;
                break;
            }
        }
        unset($compare_view);

        if ($found_view) {

            if (trim($database_view['Create']) != trim($found_view['Create'])) {
                $compare_difference = array();
                $compare_difference['type'] = "View definition";
                $compare_difference[$nominal_name] = $database_view['name'];
                $compare_differences[] = $compare_difference;
            }

        } else {
            $compare_difference = array();
            $compare_difference['type'] = "View existence";
            $compare_difference[$nominal_name] = $database_view['name'];
            $compare_differences[] = $compare_difference;
        }
    }

    return($compare_differences);
}


// Generate SQL to create or modify column
function mustal_column_sql_definition(string $table_name, array $column, array $reserved_words_without_quote) : string {

    $column_is_string_type = mustal_is_string_type($column['Type']);

    foreach($column as $key => &$value) {
        $value = (string) $value;
        $value = mustal_column_sql_create_property_definition($key,$value,$reserved_words_without_quote,$column_is_string_type);
    }

    // Default handling here
    if ($column['Default'] == " DEFAULT ''") {
        $column['Default'] = "";
    }

    if (str_contains($column['Extra'],'auto_increment') && $column['Key'] == '') {
        $primary = " PRIMARY KEY ";
    } else {
        $primary = "";
    }

    $sql =
        $column['Type'].
        $column['Null'].
        $column['Default'].
        $column['Extra'].
        $column['Key'].
        $column['Collation'];

    return($sql);
}

// Generate SQL to modify a single column property
function mustal_column_sql_create_property_definition(string $property, string $property_value, array $reserved_words_without_quote, $column_is_string_type) : string {

    switch ($property) {
        case 'Type':
        break;
        case 'Null':
            if ($property_value == "NO") {
                $property_value = " NOT NULL"; // Idiotic...
            }
            if ($property_value == "YES") {
                $property_value = " NULL"; // Also Idiotic...
            }
        break;
        case 'Default':
            // Check for MYSQL function mustal_call as default

            if (in_array(strtolower($property_value),$reserved_words_without_quote)) {
                $quote = "";
            } else {
                // Remove quotes if there are
                $property_value = trim($property_value,"'");
                $quote = "'";
            }
            $property_value = " DEFAULT $quote".$property_value."$quote";
        break;
        case 'Extra':
            if ($property_value != '')  {
                $property_value = " ".$property_value;
            }
        break;
        case 'Collation':
            if ($property_value != '' && $column_is_string_type)  {
                $property_value = " COLLATE ".$property_value;
            } else {
                $property_value = "";
            }
        break;
        case 'Key':
            if ($property_value == 'PRI') {
                $property_value = " PRIMARY KEY";
            } else {
                $property_value = "";
            }
        break;
        default:
            $property_value = "";
        break;
    }

    return($property_value);
}

// Replaces different variants of the same function mustal_to allow comparison
function mustal_sql_replace_reserved_functions(array &$column, array $replacers) {

    $result = $column['Default'];
    foreach ($replacers as $replace) {
        if (strtolower($column['Default']) == $replace[0]) {
            $result = $replace[1];
        }
    }
    $column['Default'] = $result;

    $result = strtolower($column['Extra']);
    foreach ($replacers as $replace) {
        if ($result == $replace[0]) {
            $result = $replace[1];
        }
    }
    $column['Extra'] = $result;
}

// Is it a text type? -> Use quotes then
function mustal_mysql_put_text_type_in_quotes(string $checktype, string $value) : string {
    $types = array('char','varchar','tinytext','text','mediumtext','longtext');

    foreach($types as $type) {
        if (stripos($checktype, $type) !== false) {
            return("'".$value."'");
        }
    }
    return($value);
}

function mustal_implode_with_quote(string $quote, string $delimiter, array $array_to_implode) : string {
    return($quote.implode($quote.$delimiter.$quote, $array_to_implode).$quote);
}


// Calculate the sql neccessary to update the database
// returns array(code,text)
// Error codes:
// 0 ok
// 1 Upgrade type of table not supported
// 2 Error on table upgrade
// 3 Error on column existence upgrade
// 4 Error on column existence upgrade
// 5 Error on column definition upgrade
// 6 Error on column definition upgrade
// 7 Error on key existence upgrade
// 8 Error on key existence upgrade
// 9 Error on key definition upgrade
// 10 Error on key definition upgrade
// 11 Table type upgrade not supported
// 12 Upgrade type not supported

function mustal_calculate_db_upgrade(array $compare_def, array $db_def, array &$upgrade_sql, array $replacers, bool $strict, bool $drop_keys) : array {

    $result = array();
    $upgrade_sql = array();

    if ($drop_keys) {
        foreach ($db_def['tables'] as $table_id => $table) {
            foreach ($table['keys'] as $key_id => $key) {
                if ($key['Key_name'] != 'PRIMARY') {
                    $upgrade_sql[] = "ALTER TABLE `".$table['name']. "` DROP KEY `".$key['Key_name']."`;";
                    unset($db_def['tables'][$table_id]['keys'][$key_id]);
                }
            }
        }
    }

    $compare_differences = mustal_compare_table_array($compare_def,"in JSON",$db_def,"in DB",true,true);

    foreach ($compare_differences as $compare_difference) {

        $drop_view = false;

        switch ($compare_difference['type']) {
            case 'Table existence':

                // Get table definition from JSON

                $table_name = $compare_difference['in JSON'];

                $table_key = array_search($table_name,array_column($compare_def['tables'],'name'));

                if ($table_key !== false) {
                    $table = $compare_def['tables'][$table_key];

                    switch ($table['type']) {
                        case 'BASE TABLE':

                            // Create table in DB
                            $sql = "";
                            $sql = "CREATE TABLE `".$table['name']."` (";
                            $comma = "";

                            foreach ($table['columns'] as $column) {
                                $sql .= $comma."`".$column['Field']."` ".mustal_column_sql_definition($table_name, $column,array_column($replacers,1));
                                $comma = ", ";
                            }

                            // Add keys
                            $comma = ", ";
                            foreach ($table['keys'] as $key) {
                                if ($key['Key_name'] !== 'PRIMARY') {
                                    $keystring = mustal_key_type(" ".$key['Non_unique']." KEY `".$key['Key_name']."` ",$key['Index_type']);
                                    $sql .= $comma.$keystring."(`".implode("`,`",$key['columns'])."`) ";
                                }
                            }
                            $sql .= ")";
                            $upgrade_sql[] = $sql;
                        break;
                        default:
                            $result[] = array(1,"Upgrade type '".$table['type']."' on table '".$table['name']."' not supported.");
                        break;
                    }
                } else {
                    $result[] = array(2,"Error table_key while creating upgrade for table existence `$table_name`.");
                }

            break;
            case 'Column existence':
                $table_name = $compare_difference['table'];
                $column_name = $compare_difference['in JSON'];
                $table_key = array_search($table_name,array_column($compare_def['tables'],'name'));
                if ($table_key !== false) {
                    $table = $compare_def['tables'][$table_key];
                    $columns = $table['columns'];
                    $column_key = array_search($column_name,array_column($columns,'Field'));
                    if ($column_key !== false) {
                        $column = $table['columns'][$column_key];
                        $sql = "ALTER TABLE `$table_name` ADD COLUMN `".$column_name."` ";
                        $sql .= mustal_column_sql_definition($table_name, $column, array_column($replacers,1));
                        $sql .= ";";
                        $upgrade_sql[] = $sql;
                    }
                    else {
                        $result[] = array(3,"Error column_key while creating column '$column_name' in table '".$table['name']."'.");
                    }
                }
                else {
                    $result[] = array(4,"Error table_key while creating upgrade for column existence '$column_name' in table '$table_name'.");
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

                        $sql = "ALTER TABLE `$table_name` MODIFY COLUMN `".$column_name."` ";
                        $sql .= mustal_column_sql_definition($table_name, $column,array_column($replacers,1));
                        $sql .= ";";
                        $upgrade_sql[] = $sql;
                    }
                    else {
                        $result[] = array(5,"Error column_key while modifying column '$column_name' in table '".$table['name']."'.");
                    }
                }
                else {
                    $result[] = array(6,"Error table_key while modifying column '$column_name' in table '$table_name'.");
                    return(6);
                }
                // Modify Column in DB
            break;
            case 'Key existence':

                $table_name = $compare_difference['table'];
                $key_name = $compare_difference['in JSON'];
                $table_key = array_search($table_name,array_column($compare_def['tables'],'name'));
                if ($table_key !== false) {
                    $table = $compare_def['tables'][$table_key];
                    $keys = $table['keys'];

                    $key_names = array_column($keys,'Key_name');
                    $key_key = array_search($key_name,$key_names);

                    if ($key_key !== false) {
                        $key = $table['keys'][$key_key];
                        if ($key['Key_name'] != 'PRIMARY') {
                            $sql = "ALTER TABLE `$table_name` ADD ".mustal_key_type(" ".$key['Non_unique']." KEY `".$key['Key_name']."` "."(`".implode("`,`",$key['columns'])."`)",$key['Index_type']).";";
                            $upgrade_sql[] = $sql;
                        }
                    }
                    else {
                        $result[] = array(7,"Error key_key while adding key '$key_name' in table '".$table['name']."'.");
                    }
                }
                else {
                    $result[] = array(8,"Error table_key while adding key '$key_name' in table '$table_name'.");
                }
            break;
            case "Key definition":
                $table_name = $compare_difference['table'];
                $key_name = $compare_difference['key'];
                $table_key = array_search($table_name,array_column($compare_def['tables'],'name'));
                if ($table_key !== false) {
                    $table = $compare_def['tables'][$table_key];
                    $keys = $table['keys'];

                    $key_names = array_column($keys,'Key_name');
                    $key_key = array_search($key_name,$key_names);

                    if ($key_key !== false) {
                        $key = $table['keys'][$key_key];
                        $sql = "ALTER TABLE `$table_name` DROP KEY `".$key_name."`;";
                        $upgrade_sql[] = $sql;
                        if ($key['Key_name'] == 'PRIMARY') {
                            $sql = "ALTER TABLE `$table_name` ADD PRIMARY KEY ";
                        }
                        else {
                            $sql = "ALTER TABLE `$table_name` ADD ".mustal_key_type(" ".$key['Non_unique']." KEY `".$key['Key_name']."` ",$key['Index_type']);
                        }
                        $sql .= "(`".implode("`,`",$key['columns'])."`)";
                        $sql .= ";";
                        $upgrade_sql[] = $sql;
                    }
                    else {
                        $result[] = array(9, "Error key_key while changing key '$key_name' in table '".$table['name']."'.");
                    }
                }
                else {
                    $result[] = array(10,"Error table_key while changing key '$key_name' in table '$table_name'.");
                }
            break;
            case 'Table count':
                // Nothing to do
            break;
            case 'Table type':
                $result[] = array(11,"Upgrade type '".$compare_difference['type']."' on table '".$compare_difference['table']."' not supported.");
            break;
            case 'View definition':
                $drop_view = true;
            // intentionally omitted break;
            case 'View existence':
                $view_name = $compare_difference['in JSON'];
                $view_key = array_search($view_name,array_column($compare_def['views'],'name'));

                if ($view_key !== false) {
                    $view = $compare_def['views'][$view_key];

                    switch ($view['type']) {
                        case 'VIEW':

                            if ($drop_view === true) {
                                $sql = "DROP VIEW ".$view['name'];
                                $upgrade_sql[] = $sql;
                            }

                            // Create view in DB
                            $upgrade_sql[] = $view['Create'];
                        break;
                        default:
                            $result[] = array(1,"Upgrade type '".$view['type']."' on view '".$view['name']."' not supported.");
                        break;
                    }
                } else {
                    $result[] = array(2,"Error view_key while creating upgrade for view existence `$view_name`.");
                }
            break;
            default:
                $result[] = array(12,"Upgrade type '".$compare_difference['type']."' not supported.");
            break;
        }
    }

    $upgrade_sql = array_unique($upgrade_sql);

    if (count($upgrade_sql) > 0) {
        array_unshift($upgrade_sql,"SET SQL_MODE='ALLOW_INVALID_DATES';");
        if (!$strict) {
            array_unshift($upgrade_sql,"SET SESSION innodb_strict_mode=OFF;");
        }
    }

    return($result);
}

// Check if given type is a string, relevant for collation
function mustal_is_string_type(string $type) {
    $mustal_string_types = array('varchar','char','text','tinytext','mediumtext','longtext');
    foreach($mustal_string_types as $string_type) {
        if (stripos($type,$string_type) === 0) {
            return(true);
        }
    }
    return(false);
}

// create correct index type syntax
function mustal_key_type(string $key_definition_string, string $key_type) {

    // Key types with using syntax
    $mustal_key_types_using_mapping = [
        'BTREE',
        'HASH'
    ];

    if (in_array($key_type,$mustal_key_types_using_mapping)) {
        return ($key_definition_string." USING ".$key_type);
    } else {
        return ($key_type." ".$key_definition_string);
    }

}

