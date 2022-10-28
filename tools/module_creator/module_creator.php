<?php

/*
 * Create module for listing and editing based on SQL table
 * Create template files accordingly
 *
 * Copyright (c) 2022 OpenXE project
 *
 * Placeholders:
 * PLACEHOLDER_MODULENAME
 * PLACEHOLDER_MODULECLASSNAME
 * PLACEHOLDER_LIST
 * PLACEHOLDER_EDIT
 * PLACEHOLDER_DELETE
 * PLACEHOLDER_SQL_LIST
 * PLACEHOLDER_DROPNBOX
 * PLACEHOLDER_GET_INPUT
 * PLACEHOLDER_SET_INPUT
 * PLACEHOLDER_COLUMNS
 * PLACEHOLDER_SET_TPL
 */

$host = 'localhost';
$user = 'openxe';
$passwd = 'openxe';
$schema = 'openxe';

if ($argc >= 2) {

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

    if (!str_starts_with($argv[1],'-')) {
      $module_name = $argv[1];
    } else {
      info();
      exit;
    }
    $module_class_name = ucfirst($module_name);
    $php_file_name = $module_name . ".php";
    $php_template_file_name = "module_creator_php_template.txt";
    $template_list_file_name = $module_name . "_list.tpl";
    $template_edit_file_name = $module_name . "_edit.tpl";
    $target_php_folder = "../../www/pages/";
    $target_tpl_folder = "../../www/pages/content/";
    $table_short_name = substr($module_name,0,1);

    if (!$force && file_exists($target_php_folder.$php_file_name)) {
        echo("File exists: ." .$target_php_folder.$php_file_name . "\n");
        echo("Use -f to force overwrite.\n");
        exit;
    }

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

    $query = "SHOW COLUMNS FROM " . $module_name;

    $result = mysqli_query($mysqli, $query);

    if (!$result) {
        echo "Query error: " . mysqli_error($mysqli);
        exit;
    }

    $columns = array();
    $sql_columns = array();
    $edit_form = "";

    /* Iterate through the result set */
    echo "FIELD\t\t\t\tType\t\tNull\tKey\tDefault\tExtra\n";
    while ($row = mysqli_fetch_assoc($result)) {
        foreach ($row as $key => $value) {
            echo($value);

            switch ($key) {
                case 'Field':
                    $colwidth = 32;

                    if ($value != 'id') {
                        $columns[] = $value;
                        $sql_columns[] = $table_short_name.".".$value;
                    }

                    break;
                case 'Type':
                    $colwidth = 16;
                    break;
                default:
                    $colwidth = 8;
                    break;
            }

            for ($filler = strlen($value); $filler < $colwidth; $filler++) {
                echo(" ");
            }
        }

        // Build edit form
        //       <tr><td>{|Bezeichnung|}:*</td><td><input type="text" id="bezeichnung" name="bezeichnung" value="[BEZEICHNUNG]" size="40"></td></tr>

        if ($row['Field'] != 'id') {
            $edit_form = $edit_form . '<tr><td>{|' . ucfirst($row['Field']) . '|}:</td><td><input type="text" name="' . $row['Field'].'" id="'.$row['Field'].'" value="[' . strtoupper($row['Field']) . ']" size="20"></td></tr>' . "\n";
        }

        echo("\n");
    }

// Create php file

    $list_of_columns = implode(', ', $columns);
    $list_of_columns_in_quotes = "'" . implode('\', \'', $columns) . "'";
    $sql_list_of_columns = implode(', ', $sql_columns);
    $sql_list_of_columns_in_quotes = "'" . implode('\', \'', $sql_columns) . "'";

    $get_input = "";
    $set_input = "";

    foreach ($columns as $column) {
        $get_input = $get_input . "\$input['$column'] = \$this->app->Secure->GetPOST('$column');\n\t";
        $set_input = $set_input . "\$this->app->Tpl->Set('" . strtoupper($column) . "', \$input['$column']);\n\t";
    }

    $php_file_contents = file_get_contents($php_template_file_name);

    if (empty($php_file_contents)) {
        echo("Failed to load " . $php_template_file_name . "\n");
        exit;
    }

    $php_file_contents = str_replace('PLACEHOLDER_MODULENAME', $module_name, $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_MODULECLASSNAME', $module_class_name, $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_LIST', $module_name . "_list", $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_EDIT', $module_name . "_edit", $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_DELETE', $module_name . "_delete", $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_DROPNBOX',"'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\\\"checkbox\\\" name=\\\"auswahl[]\\\" value=\\\"',".$table_short_name.".id,'\\\" />') AS `auswahl`", $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_SQL_LIST', "SELECT SQL_CALC_FOUND_ROWS $table_short_name.id, \$dropnbox, $sql_list_of_columns, $table_short_name.id FROM $module_name $table_short_name", $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_SQL_EDIT', "INSERT INTO $module_name ($list_of_columns, id) values ('\".implode('\', \'',\$input).\"', \$id) ON DUPLICATE KEY UPDATE SET ", $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_GET_INPUT', $get_input, $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_SET_INPUT', $set_input, $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_COLUMNS', $list_of_columns_in_quotes, $php_file_contents);
    $php_file_contents = str_replace('PLACEHOLDER_SQL_COLUMNS', $sql_list_of_columns_in_quotes, $php_file_contents);

    $php_file = fopen($target_php_folder . $php_file_name, "w");
    if (empty($php_file)) {
        echo ("Failed to write to " . $target_php_folder . $php_file_name);
    }
    $template_list_file = fopen($target_tpl_folder . $template_list_file_name, "w");
    if (empty($template_list_file)) {
        echo ("Failed to write to " . $target_tpl_folder . $template_list_file_name);
    }
    $template_edit_file = fopen($target_tpl_folder . $template_edit_file_name, "w");
    if (empty($template_edit_file)) {
        echo ("Failed to write to " . $target_tpl_folder . $template_edit_file_name);
    }

    fwrite($php_file, $php_file_contents);
    fclose($php_file);

    $list_template_contents = file_get_contents("module_creator_list.tpl");
    fwrite($template_list_file, $list_template_contents);
    fclose($template_list_file);

    $edit_template_contents = file_get_contents("module_creator_edit.tpl");
    $edit_template_contents = str_replace('PLACEHOLDER_LEGEND', "<!--Legend for this form area goes here>-->".$module_name, $edit_template_contents);
    $edit_template_contents = str_replace('PLACEHOLDER_FIELDS', $edit_form, $edit_template_contents);

    fwrite($template_edit_file, $edit_template_contents);
    fclose($template_edit_file);

    echo("\n\nCreated module files: \n");
    echo ($target_php_folder . $php_file_name . "\n");
    if ($verbose) {
        echo("-----------\n\n");
        echo($php_file_contents);
        echo("-----------\n\n");
    }

    echo ($target_tpl_folder . $template_list_file_name . "\n");
    if ($verbose) {
        echo("-----------\n\n");
        echo($list_template_contents);
        echo("-----------\n\n");
    }

    echo ($target_tpl_folder . $template_edit_file_name . "\n");
    if ($verbose) {
        echo("-----------\n\n");
        echo($edit_template_contents);
        echo("-----------\n\n");
    }

} else {
  info();
  exit;
}

function info() {
    echo("\nOpenXE module creator\n");
    echo("Copyright 2022 (c) OpenXE project\n\n");
    echo("Create a module.php file, a template for listing and a template for editing, based on a SQL table\n");
    echo("\n");
    echo("arg1: SQL table name\n");
    echo("Options\n");
    echo("\t-v: verbose output\n");
    echo("\n");
}



