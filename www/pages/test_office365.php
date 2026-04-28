<?php

// Test Office365 Service availability
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Test Office365 Services<br><br>";

try {
    echo "1. Loading application...<br>";
    require_once __DIR__ . '/../../xentral_autoloader.php';

    echo "2. Creating ApplicationCore...<br>";
    $app = new ApplicationCore();

    echo "3. Getting Container...<br>";
    $container = $app->Container;

    echo "4. Testing Office365 Services:<br>";

    try {
        $service = $container->get('Office365CredentialsService');
        echo "   ✓ Office365CredentialsService found<br>";
    } catch (Exception $e) {
        echo "   ✗ Office365CredentialsService NOT found: " . $e->getMessage() . "<br>";
    }

    try {
        $service = $container->get('Office365AccountGateway');
        echo "   ✓ Office365AccountGateway found<br>";
    } catch (Exception $e) {
        echo "   ✗ Office365AccountGateway NOT found: " . $e->getMessage() . "<br>";
    }

    try {
        $service = $container->get('Office365AuthorizationService');
        echo "   ✓ Office365AuthorizationService found<br>";
    } catch (Exception $e) {
        echo "   ✗ Office365AuthorizationService NOT found: " . $e->getMessage() . "<br>";
    }

    echo "<br>5. Testing Database Tables:<br>";
    $tables = [
        'office365_account',
        'office365_access_token',
        'office365_account_scope',
        'office365_account_property'
    ];

    foreach ($tables as $table) {
        $result = $app->DB->SelectArr("SHOW TABLES LIKE '$table'");
        if (!empty($result)) {
            echo "   ✓ Table '$table' exists<br>";
        } else {
            echo "   ✗ Table '$table' NOT found<br>";
        }
    }

    echo "<br>6. Testing Configuration:<br>";
    $result = $app->DB->SelectArr("SELECT varname, wert FROM konfiguration WHERE varname LIKE 'office365_%'");
    foreach ($result as $row) {
        echo "   ✓ Config '{$row['varname']}' = " . substr($row['wert'], 0, 20) . "...<br>";
    }

    echo "<br><strong>All tests completed!</strong>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
