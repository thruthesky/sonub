<?php

/**
 * Entity System Test Suite
 *
 * Pure PHP unit tests for the Entity base class and User/Post entities
 * Run: php tests/db/entity.test.php
 */

// Load the entity classes
require_once __DIR__ . '/../../lib/db/entity.php';
require_once __DIR__ . '/../../lib/db/user.php';
require_once __DIR__ . '/../../lib/db/post.php';

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
 * Assert null
 */
function assert_null($value, $message) {
    return assert_true($value === null, $message);
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
    echo "ENTITY SYSTEM TEST RESULTS\n";
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
        // Create users table if not exists
        db()->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(255),
                last_name VARCHAR(255),
                middle_name VARCHAR(255),
                email VARCHAR(255) UNIQUE,
                password_hash VARCHAR(255),
                status VARCHAR(50) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Create posts table if not exists
        db()->query("
            CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                title VARCHAR(255),
                slug VARCHAR(255) UNIQUE,
                content TEXT,
                status VARCHAR(50) DEFAULT 'draft',
                view_count INT DEFAULT 0,
                published_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");

        // Clean up any existing test data
        db()->query("DELETE FROM posts WHERE email LIKE 'test%@example.com' OR title LIKE 'Test Post%'");
        db()->query("DELETE FROM users WHERE email LIKE 'test%@example.com' OR first_name LIKE 'Test%'");

        return true;
    } catch (Exception $e) {
        echo "Setup failed: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Cleanup test data
 */
function cleanup_test_data() {
    try {
        // Clean up test data (keep tables for future tests)
        db()->query("DELETE FROM posts WHERE title LIKE 'Test Post%'");
        db()->query("DELETE FROM users WHERE email LIKE 'test%@example.com' OR first_name LIKE 'Test%'");
        return true;
    } catch (Exception $e) {
        echo "Cleanup failed: " . $e->getMessage() . "\n";
        return false;
    }
}

// ============================================================================
// TEST CASES
// ============================================================================

echo "Starting Entity System Tests...\n";
echo str_repeat("=", 50) . "\n";

// Setup test database
if (!setup_test_database()) {
    echo "Failed to setup test database. Exiting.\n";
    exit(1);
}

// Test 1: User Entity - Create
echo "\n[Testing User Entity - Create]\n";

$user = User::create([
    'first_name' => 'User',
    'last_name' => 'Test',
    'middle_name' => '1',
    'email' => 'test1@example.com',
    'password' => 'password123'
]);

assert_not_null($user, "User::create should return a User instance");
assert_true($user->getValue('id') > 0, "Created user should have an ID");
assert_equals('User', $user->getValue('first_name'), "User first name should match");
assert_equals('Test', $user->getValue('last_name'), "User last name should match");
assert_equals('1', $user->getValue('middle_name'), "User middle name should match");
assert_equals('test1@example.com', $user->getEmail(), "User email should match");
assert_true($user->verifyPassword('password123'), "Password verification should work");
assert_equals('active', $user->getStatus(), "Default status should be active");
assert_not_null($user->getValue('created_at'), "Created_at should be set");
assert_not_null($user->getValue('updated_at'), "Updated_at should be set");

// Test 2: User Entity - Get by ID
echo "\n[Testing User Entity - Get by ID]\n";

$userId = $user->getValue('id');
$fetchedUser = User::get($userId);

assert_not_null($fetchedUser, "User::get should return a User instance");
assert_equals($userId, $fetchedUser->getValue('id'), "Fetched user ID should match");
assert_equals('User', $fetchedUser->getValue('first_name'), "Fetched user first name should match");

// Test 3: User Entity - Find by Email
echo "\n[Testing User Entity - Find by Email]\n";

$foundUser = User::findByEmail('test1@example.com');
assert_not_null($foundUser, "Should find user by email");
assert_equals($userId, $foundUser->getValue('id'), "Found user ID should match");

$notFound = User::findByEmail('nonexistent@example.com');
assert_null($notFound, "Should return null for non-existent email");

// Test 4: User Entity - Update
echo "\n[Testing User Entity - Update]\n";

$updateResult = $fetchedUser->update(['first_name' => 'Updated']);
assert_true($updateResult, "Update should return true");

$updatedUser = User::get($userId);
assert_equals('Updated', $updatedUser->getValue('first_name'), "First name should be updated");
assert_not_null($updatedUser->getValue('updated_at'), "Updated_at should be updated");

// Test 5: User Entity - Duplicate Email Prevention
echo "\n[Testing User Entity - Duplicate Email Prevention]\n";

$exceptionThrown = false;
try {
    User::create([
        'first_name' => 'User',
        'last_name' => 'Test',
        'middle_name' => '2',
        'email' => 'test1@example.com', // Duplicate email
        'password' => 'password456'
    ]);
} catch (Exception $e) {
    $exceptionThrown = true;
}
assert_true($exceptionThrown, "Should throw exception for duplicate email");

// Test 6: Post Entity - Create
echo "\n[Testing Post Entity - Create]\n";

$post = Post::create([
    'user_id' => $userId,
    'title' => 'Test Post 1',
    'content' => 'This is test content for post 1.'
]);

assert_not_null($post, "Post::create should return a Post instance");
assert_true($post->getValue('id') > 0, "Created post should have an ID");
assert_equals('Test Post 1', $post->getTitle(), "Post title should match");
assert_equals('test-post-1', $post->getSlug(), "Post slug should be generated");
assert_equals('draft', $post->getStatus(), "Default status should be draft");
assert_equals(0, $post->getViewCount(), "Initial view count should be 0");
assert_equals($userId, $post->getUserId(), "Post user_id should match");

// Test 7: Post Entity - Get by ID
echo "\n[Testing Post Entity - Get by ID]\n";

$postId = $post->getValue('id');
$fetchedPost = Post::get($postId);

assert_not_null($fetchedPost, "Post::get should return a Post instance");
assert_equals($postId, $fetchedPost->getValue('id'), "Fetched post ID should match");
assert_equals('Test Post 1', $fetchedPost->getTitle(), "Fetched post title should match");

// Test 8: Post Entity - Find by Slug
echo "\n[Testing Post Entity - Find by Slug]\n";

$foundPost = Post::findBySlug('test-post-1');
assert_not_null($foundPost, "Should find post by slug");
assert_equals($postId, $foundPost->getValue('id'), "Found post ID should match");

// Test 9: Post Entity - Publish
echo "\n[Testing Post Entity - Publish]\n";

$publishResult = $fetchedPost->publish();
assert_true($publishResult, "Publish should return true");

$publishedPost = Post::get($postId);
assert_equals('published', $publishedPost->getStatus(), "Post should be published");
assert_not_null($publishedPost->getValue('published_at'), "Published_at should be set");

// Test 10: Post Entity - Get Author
echo "\n[Testing Post Entity - Get Author]\n";

$author = $publishedPost->getAuthor();
assert_not_null($author, "Should get post author");
assert_equals($userId, $author->getValue('id'), "Author ID should match user ID");
assert_equals('Updated', $author->getValue('first_name'), "Author name should match");

// Test 11: Post Entity - Increment View Count
echo "\n[Testing Post Entity - Increment View Count]\n";

$incrementResult = $publishedPost->incrementViewCount();
assert_true($incrementResult, "Increment view count should return true");

$viewedPost = Post::get($postId);
assert_equals(1, $viewedPost->getViewCount(), "View count should be incremented");

// Test 12: Post Entity - Search
echo "\n[Testing Post Entity - Search]\n";

// Create another published post for search
$post2 = Post::create([
    'user_id' => $userId,
    'title' => 'Test Post 2 with keyword',
    'content' => 'This post contains searchable content.',
    'status' => 'published'
]);

$searchResults = Post::search('keyword');
assert_equals(1, count($searchResults), "Should find 1 post with keyword");
assert_equals('Test Post 2 with keyword', $searchResults[0]->getTitle(), "Search result should match");

// Test 13: Entity - Magic Methods
echo "\n[Testing Entity Magic Methods]\n";

$user3 = User::create([
    'first_name' => 'User',
    'last_name' => 'Test',
    'middle_name' => '3',
    'email' => 'test3@example.com'
]);

// Magic getter
assert_equals('User', $user3->first_name, "Magic getter should work");

// Magic setter
$user3->first_name = 'MagicUpdated';
assert_equals('MagicUpdated', $user3->getValue('first_name'), "Magic setter should work");

// Magic isset
assert_true(isset($user3->email), "Magic isset should work for existing field");
assert_true(!isset($user3->nonexistent), "Magic isset should return false for non-existent field");

// Test 14: Entity - Save Method
echo "\n[Testing Entity Save Method]\n";

// Test update via save
$user4 = User::get($user3->getValue('id'));
$user4->setValue('first_name', 'SaveUpdated');
$saveResult = $user4->save();
assert_true($saveResult, "Save on existing entity should update");

$savedUser = User::get($user3->getValue('id'));
assert_equals('SaveUpdated', $savedUser->getValue('first_name'), "Save should update entity");

// Test 15: User - Get Posts
echo "\n[Testing User Get Posts]\n";

$userPosts = $updatedUser->getPosts();
assert_equals(2, count($userPosts), "User should have 2 posts");

// Test 16: Post - Get Recent
echo "\n[Testing Post Get Recent]\n";

$recentPosts = Post::getRecent(5);
assert_true(count($recentPosts) >= 1, "Should get recent published posts");

// Test 17: Entity - Count
echo "\n[Testing Entity Count]\n";

$userCount = User::count();
assert_true($userCount >= 2, "Should count users correctly");

$activeCount = User::count(['status' => 'active']);
assert_true($activeCount >= 2, "Should count active users correctly");

// Test 18: Entity - Exists
echo "\n[Testing Entity Exists]\n";

assert_true(User::exists($userId), "Should return true for existing user");
assert_true(!User::exists(99999), "Should return false for non-existent user");

// Test 19: Entity - Delete
echo "\n[Testing Entity Delete]\n";

// Create a user to delete
$deleteUser = User::create([
    'first_name' => 'Delete',
    'last_name' => 'Test',
    'email' => 'testdelete@example.com'
]);
$deleteId = $deleteUser->getValue('id');

$deleteResult = $deleteUser->delete();
assert_true($deleteResult, "Delete should return true");

$deletedUser = User::get($deleteId);
assert_null($deletedUser, "Deleted user should not exist");

// Test 20: Entity - Destroy Static Method
echo "\n[Testing Entity Destroy Static Method]\n";

$destroyUser = User::create([
    'first_name' => 'Destroy',
    'last_name' => 'Test',
    'email' => 'testdestroy@example.com'
]);
$destroyId = $destroyUser->getValue('id');

$destroyResult = User::destroy($destroyId);
assert_true($destroyResult, "Destroy should return true");

$destroyedUser = User::get($destroyId);
assert_null($destroyedUser, "Destroyed user should not exist");

// Test 21: Entity - toArray and toJson
echo "\n[Testing Entity toArray and toJson]\n";

$arrayUser = User::create([
    'first_name' => 'Array',
    'last_name' => 'Test',
    'email' => 'testarray@example.com'
]);

$userArray = $arrayUser->toArray();
assert_true(is_array($userArray), "toArray should return array");
assert_array_has_key('id', $userArray, "Array should have id key");
assert_array_has_key('first_name', $userArray, "Array should have first_name key");

$userJson = $arrayUser->toJson();
assert_true(is_string($userJson), "toJson should return string");
$decoded = json_decode($userJson, true);
assert_equals('Array', $decoded['first_name'], "JSON should contain correct data");

// ============================================================================
// CLEANUP AND RESULTS
// ============================================================================

// Cleanup test data
cleanup_test_data();

// Print results
$exitCode = print_results();

// Exit with appropriate code
exit($exitCode);