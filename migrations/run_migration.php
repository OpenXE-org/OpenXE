<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../xentral_autoloader.php';

try {
    // Load the application core
    $app = new ApplicationCore();

    // Get database connection
    $db = $app->DB;

    // Read the migration SQL file
    $sqlFile = __DIR__ . '/office365_oauth_tables.sql';
    $sql = file_get_contents($sqlFile);

    if (!$sql) {
        throw new Exception("Could not read migration file: $sqlFile");
    }

    // Split the SQL into individual statements
    $statements = array_filter(
        array_map(
            'trim',
            preg_split('/;[\s\n]*/', $sql)
        )
    );

    // Execute each statement
    $count = 0;
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->query($statement);
            $count++;
            echo "Executed statement $count\n";
        }
    }

    echo "\n✓ Migration completed successfully! Created/updated 4 tables.\n";
    echo "Tables created:\n";
    echo "  - office365_account\n";
    echo "  - office365_access_token\n";
    echo "  - office365_account_scope\n";
    echo "  - office365_account_property\n";

} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
