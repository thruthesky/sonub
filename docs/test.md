# Test Documentation

## Table of Contents
- [Overview](#overview)
- [Test File Structure](#test-file-structure)
- [Required Bootstrap](#required-bootstrap)
- [Writing Tests](#writing-tests)
- [Running Tests](#running-tests)
- [Test Examples](#test-examples)

## Overview

This document describes the testing guidelines and conventions for the SONUB project. All tests are written in pure PHP without external testing frameworks, following the project's standard workflow.

## Test File Structure

- Test files are stored in the `tests/` directory
- Test file names must end with `.test.php`
- Directory structure should mirror the source code structure
- Each test file should be independently executable

Example:
```
tests/
├── db/
│   └── db.test.php          # Tests for lib/db/db.php
├── db.connection.test.php   # Database connection tests
└── user/
    └── user.test.php        # Tests for user functionality
```

## Required Bootstrap

**IMPORTANT:** All test files must load the bootstrap files in the following order to ensure all dependencies and functions are available:

```php
<?php
// Load bootstrap functions first
require_once __DIR__ . '/../../etc/boot/boot.functions.php';

// Load all necessary includes
require_once etc_folder('includes');
```

This loading sequence ensures:
1. Core utility functions are available (like `etc_folder()`, `is_dev_computer()`, etc.)
2. All required configuration files are loaded
3. Database connections and other dependencies are properly initialized

## Writing Tests

### Basic Test Structure

```php
<?php
// Required bootstrap
require_once __DIR__ . '/../../etc/boot/boot.functions.php';
require_once etc_folder('includes');

echo "=== Test Name ===\n\n";

try {
    // Test 1
    echo "Test 1: Description...\n";
    // Test logic here
    echo "✅ Test 1 passed\n\n";

    // Test 2
    echo "Test 2: Description...\n";
    // Test logic here
    echo "✅ Test 2 passed\n\n";

    echo "=============================\n";
    echo "✅ All tests passed successfully!\n";
    echo "=============================\n";

} catch (Exception $e) {
    echo "\n❌ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}
```

### Assertion Examples

Since we don't use external testing frameworks, use simple assertions:

```php
// Simple assertion
if ($result !== $expected) {
    throw new Exception("Expected $expected but got $result");
}

// Boolean assertion
if (!$condition) {
    throw new Exception("Condition failed: description of what failed");
}

// Array/Object comparison
if ($array1 != $array2) {
    throw new Exception("Arrays do not match");
}
```

## Running Tests

Tests can be run directly using PHP CLI:

```bash
# Run a specific test
php tests/db/db.test.php

# Run database connection test
php tests/db.connection.test.php

# Run all tests in a directory (using shell script)
for test in tests/**/*.test.php; do
    echo "Running $test..."
    php "$test"
done
```

## Test Examples

### Database Connection Test

```php
<?php
// Required bootstrap
require_once __DIR__ . '/../../etc/boot/boot.functions.php';
require_once etc_folder('includes');

echo "=== Database Connection Test ===\n\n";

try {
    $connection = db_connection();

    if ($connection instanceof PDO) {
        echo "✅ Successfully connected to database\n";
    } else {
        throw new Exception("Connection is not a valid PDO instance");
    }

} catch (PDOException $e) {
    echo "\n❌ Database connection error: " . $e->getMessage() . "\n";
    exit(1);
}
```

### Query Builder Test

```php
<?php
// Required bootstrap
require_once __DIR__ . '/../../etc/boot/boot.functions.php';
require_once etc_folder('includes');

echo "=== Query Builder Test ===\n\n";

try {
    // Test INSERT
    $id = db()->insert(['name' => 'Test'])->into('users');
    echo "✅ Insert successful, ID: $id\n";

    // Test SELECT
    $result = db()->select('*')->from('users')->where('id = ?', [$id])->first();
    if ($result['name'] === 'Test') {
        echo "✅ Select successful\n";
    }

    // Test DELETE
    $affected = db()->delete()->from('users')->where('id = ?', [$id])->execute();
    echo "✅ Delete successful, rows affected: $affected\n";

} catch (Exception $e) {
    echo "\n❌ Query builder test failed: " . $e->getMessage() . "\n";
    exit(1);
}
```

### Unit Test Example

```php
<?php
// Required bootstrap
require_once __DIR__ . '/../../etc/boot/boot.functions.php';
require_once etc_folder('includes');

echo "=== User Validation Test ===\n\n";

try {
    // Test email validation
    echo "Test 1: Email validation...\n";

    $validEmail = "user@example.com";
    $invalidEmail = "invalid-email";

    if (filter_var($validEmail, FILTER_VALIDATE_EMAIL)) {
        echo "✅ Valid email accepted\n";
    } else {
        throw new Exception("Valid email rejected");
    }

    if (!filter_var($invalidEmail, FILTER_VALIDATE_EMAIL)) {
        echo "✅ Invalid email rejected\n";
    } else {
        throw new Exception("Invalid email accepted");
    }

    echo "\n✅ All validation tests passed!\n";

} catch (Exception $e) {
    echo "\n❌ Validation test failed: " . $e->getMessage() . "\n";
    exit(1);
}
```

## Best Practices

1. **Always use the bootstrap sequence** - Never skip loading `boot.functions.php` and `includes`
2. **Keep tests independent** - Each test file should be runnable on its own
3. **Use clear output** - Use emojis (✅ ❌) and formatting to make results easy to read
4. **Handle errors gracefully** - Use try-catch blocks and provide helpful error messages
5. **Exit with proper codes** - Use `exit(1)` for failures, normal exit for success
6. **Test both success and failure cases** - Ensure your tests cover edge cases
7. **Clean up after tests** - If your test creates data, clean it up when done