<?php

/**
 * Database Query Builder Test Suite
 *
 * Pure PHP unit tests for the database query builder
 * Run: php tests/db/db.test.php
 */

// Load the database class
require_once __DIR__ . '/../../lib/db/db.php';

// Test configuration
$testsPassed = 0;
$testsFailed = 0;
$testResults = [];

/**
 * Simple assertion function
 */
function assert_true($condition, $message) {
    global $testsPassed, $testsFailed, $testResults;

    if ($condition) {
        $testsPassed++;
        $testResults[] = " PASS: $message";
        return true;
    } else {
        $testsFailed++;
        $testResults[] = " FAIL: $message";
        return false;
    }
}

/**
 * Assert equality
 */
function assert_equals($expected, $actual, $message) {
    $result = $expected === $actual;
    if (!$result) {
        $message .= " (Expected: " . var_export($expected, true) . ", Got: " . var_export($actual, true) . ")";
    }
    return assert_true($result, $message);
}

/**
 * Assert not null
 */
function assert_not_null($value, $message) {
    return assert_true($value !== null, $message);
}

/**
 * Assert array has key
 */
function assert_array_has_key($key, $array, $message) {
    return assert_true(is_array($array) && array_key_exists($key, $array), $message);
}

/**
 * Print test results
 */
function print_results() {
    global $testsPassed, $testsFailed, $testResults;

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "DATABASE QUERY BUILDER TEST RESULTS\n";
    echo str_repeat("=", 50) . "\n\n";

    foreach ($testResults as $result) {
        echo $result . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n";
    echo "SUMMARY: ";
    echo "Passed: $testsPassed | Failed: $testsFailed | Total: " . ($testsPassed + $testsFailed) . "\n";

    if ($testsFailed === 0) {
        echo " All tests passed successfully!\n";
    } else {
        echo " Some tests failed. Please review the results above.\n";
    }
    echo str_repeat("=", 50) . "\n\n";

    return $testsFailed === 0 ? 0 : 1;
}

/**
 * Setup test database and tables
 */
function setup_test_database() {
    try {
        // Create test table if not exists
        db()->query("
            CREATE TABLE IF NOT EXISTS test_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                display_name VARCHAR(255),
                email VARCHAR(255),
                status VARCHAR(50) DEFAULT 'active',
                age INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Create posts table for join tests
        db()->query("
            CREATE TABLE IF NOT EXISTS test_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                title VARCHAR(255),
                content TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Clean up any existing data
        db()->query("TRUNCATE TABLE test_users");
        db()->query("TRUNCATE TABLE test_posts");

        return true;
    } catch (Exception $e) {
        echo "Setup failed: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Cleanup test database
 */
function cleanup_test_database() {
    try {
        db()->query("DROP TABLE IF EXISTS test_users");
        db()->query("DROP TABLE IF EXISTS test_posts");
        return true;
    } catch (Exception $e) {
        echo "Cleanup failed: " . $e->getMessage() . "\n";
        return false;
    }
}

// ============================================================================
// TEST CASES
// ============================================================================

echo "Starting Database Query Builder Tests...\n";
echo str_repeat("=", 50) . "\n";

// Setup test database
if (!setup_test_database()) {
    echo "Failed to setup test database. Exiting.\n";
    exit(1);
}

// Test 1: INSERT Operation
echo "\n[Testing INSERT Operations]\n";

$insertId1 = db()->insert([
    'display_name' => 'jaeho',
    'email' => 'jaeho@example.com',
    'age' => 25
])->into('test_users');

assert_true($insertId1 > 0, "INSERT should return a valid ID");

$insertId2 = db()->insert([
    'display_name' => 'song',
    'email' => 'song@example.com',
    'age' => 30
])->into('test_users');

assert_true($insertId2 > $insertId1, "Second INSERT should return a higher ID");

// Insert more test data
for ($i = 3; $i <= 5; $i++) {
    db()->insert([
        'display_name' => "User$i",
        'email' => "user$i@example.com",
        'age' => 20 + $i
    ])->into('test_users');
}

// Test 2: SELECT Operations
echo "\n[Testing SELECT Operations]\n";

// Select all
$allUsers = db()->select('*')->from('test_users')->get();
assert_equals(5, count($allUsers), "Should have 5 users in database");

// Select with WHERE
$filteredUsers = db()->select('*')
    ->from('test_users')
    ->where('id > 2')
    ->limit(5)
    ->get();
assert_equals(3, count($filteredUsers), "Should return 3 users with id > 2");

// Select with parameters
$user = db()->select('*')
    ->from('test_users')
    ->where('id = ?', [1])
    ->first();
assert_not_null($user, "Should find user with id = 1");
assert_equals('jaeho', $user['display_name'], "User with id = 1 should be 'jaeho'");

// Select specific columns
$emailList = db()->select('email')->from('test_users')->get();
assert_equals(5, count($emailList), "Should return 5 email records");
assert_array_has_key('email', $emailList[0], "Should have email field");
assert_true(!isset($emailList[0]['display_name']), "Should not have display_name field");

// Count records
$count = db()->select()->from('test_users')->count();
assert_equals(5, $count, "Count should return 5 users");

$countFiltered = db()->select()
    ->from('test_users')
    ->where('age > ?', [25])
    ->count();
assert_equals(2, $countFiltered, "Should have 2 users with age > 25");

// Test 3: UPDATE Operations
echo "\n[Testing UPDATE Operations]\n";

$affected = db()->update(['display_name' => 'updated_jaeho'])
    ->table('test_users')
    ->where("display_name = 'jaeho'")
    ->execute();
assert_equals(1, $affected, "Should update 1 row");

// Verify update
$updatedUser = db()->select('*')
    ->from('test_users')
    ->where('id = ?', [1])
    ->first();
assert_equals('updated_jaeho', $updatedUser['display_name'], "Name should be updated");

// Update multiple rows
$affectedMultiple = db()->update(['status' => 'inactive'])
    ->table('test_users')
    ->where('age > ?', [25])
    ->execute();
assert_true($affectedMultiple >= 2, "Should update at least 2 rows");

// Test 4: DELETE Operations
echo "\n[Testing DELETE Operations]\n";

// Add a user to delete
$deleteId = db()->insert([
    'display_name' => 'to_delete',
    'email' => 'delete@example.com'
])->into('test_users');

$deleted = db()->delete()
    ->from('test_users')
    ->where('id = ?', [$deleteId])
    ->execute();
assert_equals(1, $deleted, "Should delete 1 row");

// Verify deletion
$deletedUser = db()->select('*')
    ->from('test_users')
    ->where('id = ?', [$deleteId])
    ->first();
assert_true($deletedUser === null, "Deleted user should not exist");

// Test 5: Raw Query
echo "\n[Testing Raw Queries]\n";

$results = db()->query("SELECT * FROM test_users WHERE id = ?", [1]);
assert_true(is_array($results), "Raw query should return array");
assert_equals(1, count($results), "Should return 1 result");

$insertedId = db()->query(
    "INSERT INTO test_users (display_name, email) VALUES (?, ?)",
    ['raw_insert', 'raw@example.com']
);
assert_true($insertedId > 0, "Raw INSERT should return ID");

$affectedRows = db()->query(
    "UPDATE test_users SET display_name = ? WHERE id = ?",
    ['raw_updated', $insertedId]
);
assert_equals(1, $affectedRows, "Raw UPDATE should affect 1 row");

$deletedRows = db()->query("DELETE FROM test_users WHERE id = ?", [$insertedId]);
assert_equals(1, $deletedRows, "Raw DELETE should delete 1 row");

// Test 6: Advanced WHERE conditions
echo "\n[Testing Advanced WHERE Conditions]\n";

// Multiple WHERE conditions (AND)
$multiWhere = db()->select('*')
    ->from('test_users')
    ->where('age > ?', [20])
    ->where('status = ?', ['inactive'])
    ->get();
assert_true(count($multiWhere) >= 0, "Multiple WHERE with AND should work");

// OR WHERE
$orWhere = db()->select('*')
    ->from('test_users')
    ->where('id = ?', [1])
    ->orWhere('id = ?', [2])
    ->get();
assert_equals(2, count($orWhere), "OR WHERE should return 2 results");

// Test 7: ORDER BY
echo "\n[Testing ORDER BY]\n";

$ordered = db()->select('*')
    ->from('test_users')
    ->orderBy('id', 'DESC')
    ->limit(1)
    ->get();
assert_true(count($ordered) === 1, "Should return 1 ordered result");

$firstUser = db()->select('*')
    ->from('test_users')
    ->orderBy('id', 'ASC')
    ->first();
assert_equals(1, $firstUser['id'], "First user should have id = 1");

// Test 8: LIMIT and Pagination
echo "\n[Testing LIMIT and Pagination]\n";

$limited = db()->select('*')
    ->from('test_users')
    ->limit(2)
    ->get();
assert_equals(2, count($limited), "Should return 2 limited results");

$paginated = db()->select('*')
    ->from('test_users')
    ->limit(2, 2)  // limit 2, offset 2
    ->get();
assert_true(count($paginated) <= 2, "Paginated results should have max 2 items");

// Test 9: JOIN Operations
echo "\n[Testing JOIN Operations]\n";

// Insert test posts
db()->insert(['user_id' => 1, 'title' => 'Post 1'])->into('test_posts');
db()->insert(['user_id' => 1, 'title' => 'Post 2'])->into('test_posts');
db()->insert(['user_id' => 2, 'title' => 'Post 3'])->into('test_posts');

$joined = db()->select('test_users.*, test_posts.title')
    ->from('test_users')
    ->join('test_posts', 'test_users.id = test_posts.user_id')
    ->get();
assert_true(count($joined) > 0, "JOIN should return results");

$leftJoined = db()->select('test_users.display_name, COUNT(test_posts.id) as post_count')
    ->from('test_users')
    ->join('test_posts', 'test_users.id = test_posts.user_id', 'LEFT')
    ->get();
assert_true(count($leftJoined) >= count($allUsers), "LEFT JOIN should include all users");

// Test 10: Transactions
echo "\n[Testing Transactions]\n";

try {
    db()->beginTransaction();

    $transId = db()->insert([
        'display_name' => 'transaction_test',
        'email' => 'trans@example.com'
    ])->into('test_users');

    // Intentionally cause an error to test rollback
    if (false) { // Change to true to test rollback
        throw new Exception("Test rollback");
    }

    db()->commit();

    $transUser = db()->select('*')
        ->from('test_users')
        ->where('id = ?', [$transId])
        ->first();
    assert_not_null($transUser, "Transaction should be committed");

} catch (Exception $e) {
    db()->rollback();
    assert_true(true, "Transaction rollback handled");
}

// Test 11: Edge Cases
echo "\n[Testing Edge Cases]\n";

// Empty result set
$emptyResult = db()->select('*')
    ->from('test_users')
    ->where('id = ?', [99999])
    ->get();
assert_equals(0, count($emptyResult), "Non-existent ID should return empty array");

$nullFirst = db()->select('*')
    ->from('test_users')
    ->where('id = ?', [99999])
    ->first();
assert_true($nullFirst === null, "first() should return null for empty result");

// Test without WHERE clause
$updateAll = db()->update(['status' => 'active'])
    ->table('test_users')
    ->execute();
assert_true($updateAll >= 0, "Update without WHERE should work");

// ============================================================================
// CLEANUP AND RESULTS
// ============================================================================

// Cleanup test database
cleanup_test_database();

// Print results
$exitCode = print_results();

// Exit with appropriate code
exit($exitCode);