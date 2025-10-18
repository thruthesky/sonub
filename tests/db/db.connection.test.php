<?php

/**
 * Database Connection Test
 * Simple test to verify database connection works properly
 */

// Load bootstrap functions first
require_once './init.php';

echo "\n";
echo "is_dev_computer(): " . (is_dev_computer() ? 'true' : 'false') . "\n";
echo "=== Database Connection Test ===\n\n";

try {
    // Test 1: Basic connection test
    echo "Test 1: Testing database connection...\n";
    $connection = db_connection();

    if ($connection instanceof PDO) {
        echo "✅ Successfully connected to database\n";
        echo "   - Host: " . DB_HOST . "\n";
        echo "   - Database: " . DB_NAME . "\n";
        echo "   - User: " . DB_USER . "\n\n";
    } else {
        throw new Exception("Connection is not a valid PDO instance");
    }

    // Test 2: Simple query test
    echo "Test 2: Testing simple query...\n";
    $result = $connection->query("SELECT 1 AS `test`");
    $row = $result->fetch();

    if ($row['test'] == 1) {
        echo "✅ Simple query executed successfully\n\n";
    } else {
        throw new Exception("Simple query failed");
    }

    // Test 3: Database server info
    echo "Test 3: Getting server information...\n";
    $version = $connection->getAttribute(PDO::ATTR_SERVER_VERSION);
    $driver = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "✅ Server info retrieved:\n";
    echo "   - Driver: " . $driver . "\n";
    echo "   - Server version: " . $version . "\n\n";

    // Test 4: Test db() helper function
    echo "Test 4: Testing db() helper function...\n";
    $db = db();

    if ($db instanceof Db) {
        echo "✅ db() helper function works correctly\n\n";

        // Test simple select query using query builder
        $testQuery = $db->query("SELECT NOW() AS `current_time`");
        if (!empty($testQuery)) {
            echo "   - Current server time: " . $testQuery[0]['current_time'] . "\n\n";
        }
    } else {
        throw new Exception("db() helper does not return Db instance");
    }

    echo "=============================\n";
    echo "✅ All tests passed successfully!\n";
    echo "=============================\n";
} catch (PDOException $e) {
    echo "\n❌ Database connection error: " . $e->getMessage() . "\n";
    echo "   Please check your database configuration:\n";
    echo "   - Host: " . DB_HOST . "\n";
    echo "   - Database: " . DB_NAME . "\n";
    echo "   - User: " . DB_USER . "\n";
    echo "   - Password: " . DB_PASSWORD . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}
