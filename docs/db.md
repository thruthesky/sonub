# Database Query Builder Documentation

## Table of Contents
- [Overview](#overview)
- [Installation & Configuration](#installation--configuration)
- [Basic Usage](#basic-usage)
  - [INSERT Operations](#insert-operations)
  - [SELECT Operations](#select-operations)
  - [UPDATE Operations](#update-operations)
  - [DELETE Operations](#delete-operations)
  - [Raw Queries](#raw-queries)
- [Advanced Features](#advanced-features)
  - [WHERE Conditions](#where-conditions)
  - [JOIN Operations](#join-operations)
  - [ORDER BY](#order-by)
  - [LIMIT & Pagination](#limit--pagination)
  - [Transactions](#transactions)
- [Entity System](#entity-system)
  - [Entity Base Class](#entity-base-class)
  - [Creating Entity Classes](#creating-entity-classes)
  - [Entity CRUD Operations](#entity-crud-operations)
  - [User Entity](#user-entity)
  - [Post Entity](#post-entity)
- [Method Reference](#method-reference)
- [Examples](#examples)

## Overview

The Sonub Database Query Builder provides a fluent, chainable interface for building and executing database queries. It uses PDO internally and protects against SQL injection through prepared statements.

## Installation & Configuration

The database configuration is located in `/lib/db/db.php`. The connection parameters are:

```php
$host = 'sonub-mariadb';
$db = 'sonub';
$user = 'sonub';
$pass = 'asdf';
$charset = 'utf8mb4';
```

To use the database query builder, simply call the `db()` function:

```php
require_once '/lib/db/db.php';

// The db() function returns a singleton instance
$result = db()->select('*')->from('users')->get();
```

## Basic Usage

### INSERT Operations

Insert a new record and get the inserted ID:

```php
// Simple insert - returns the last insert ID
$id = db()->insert(['display_name' => 'jaeho', 'email' => 'jaeho@example.com'])
    ->into('users');

// Insert with multiple fields
$id = db()->insert([
    'display_name' => 'John Doe',
    'email' => 'john@example.com',
    'created_at' => date('Y-m-d H:i:s')
])->into('users');
```

### SELECT Operations

#### Retrieve Multiple Records

```php
// Select all columns
$users = db()->select('*')->from('users')->get();

// Select specific columns
$users = db()->select('id, display_name, email')->from('users')->get();

// With WHERE condition
$users = db()->select('*')
    ->from('users')
    ->where('id > 2')
    ->limit(5)
    ->get();

// Using prepared statements with placeholders
$users = db()->select('*')
    ->from('users')
    ->where('status = ? AND created_at > ?', ['active', '2024-01-01'])
    ->get();
```

#### Retrieve Single Record

```php
// Get the first matching record
$user = db()->select('*')
    ->from('users')
    ->where('id = ?', [1])
    ->first();

// Returns null if no record found
$user = db()->select('*')
    ->from('users')
    ->where('email = ?', ['nonexistent@example.com'])
    ->first();
```

#### Count Records

```php
// Get the count of records
$count = db()->select()->from('users')->count();

// Count with conditions
$activeCount = db()->select()
    ->from('users')
    ->where('status = ?', ['active'])
    ->count();
```

### UPDATE Operations

```php
// Update records - returns number of affected rows
$affected = db()->update(['display_name' => 'song'])
    ->table('users')
    ->where("display_name = 'jaeho'")
    ->execute();

// Update with multiple fields
$affected = db()->update([
    'display_name' => 'Updated Name',
    'updated_at' => date('Y-m-d H:i:s')
])
->table('users')
->where('id = ?', [5])
->execute();

// Update without WHERE (updates all records - use with caution!)
$affected = db()->update(['status' => 'inactive'])
    ->table('users')
    ->execute();
```

### DELETE Operations

```php
// Delete records - returns number of deleted rows
$deleted = db()->delete()
    ->from('users')
    ->where('id = ?', [5])
    ->execute();

// Delete with multiple conditions
$deleted = db()->delete()
    ->from('users')
    ->where('status = ? AND created_at < ?', ['inactive', '2023-01-01'])
    ->execute();

// Delete all records (use with caution!)
$deleted = db()->delete()->from('temp_table')->execute();
```

### Raw Queries

For complex queries that don't fit the builder pattern:

```php
// SELECT query
$results = db()->query("SELECT * FROM users WHERE id = ?", [1]);

// INSERT query - returns last insert ID
$id = db()->query(
    "INSERT INTO users (display_name, email) VALUES (?, ?)",
    ['John', 'john@example.com']
);

// UPDATE query - returns affected rows
$affected = db()->query(
    "UPDATE users SET display_name = ? WHERE id = ?",
    ['New Name', 5]
);

// DELETE query - returns deleted rows
$deleted = db()->query("DELETE FROM users WHERE id = ?", [10]);

// Complex query with joins
$results = db()->query("
    SELECT u.*, COUNT(p.id) as post_count
    FROM users u
    LEFT JOIN posts p ON u.id = p.user_id
    GROUP BY u.id
    HAVING post_count > ?
", [5]);
```

## Advanced Features

### WHERE Conditions

```php
// Simple WHERE
$users = db()->select('*')
    ->from('users')
    ->where('age > 18')
    ->get();

// Multiple AND conditions
$users = db()->select('*')
    ->from('users')
    ->where('age > ?', [18])
    ->where('status = ?', ['active'])
    ->get();

// OR conditions
$users = db()->select('*')
    ->from('users')
    ->where('role = ?', ['admin'])
    ->orWhere('role = ?', ['moderator'])
    ->get();

// Complex conditions
$users = db()->select('*')
    ->from('users')
    ->where('(age > ? AND status = ?)', [18, 'active'])
    ->orWhere('role = ?', ['admin'])
    ->get();
```

### JOIN Operations

```php
// INNER JOIN
$results = db()->select('users.*, posts.title')
    ->from('users')
    ->join('posts', 'users.id = posts.user_id')
    ->get();

// LEFT JOIN
$results = db()->select('users.*, COUNT(posts.id) as post_count')
    ->from('users')
    ->join('posts', 'users.id = posts.user_id', 'LEFT')
    ->get();

// Multiple JOINs
$results = db()->select('u.display_name, p.title, c.comment')
    ->from('users u')
    ->join('posts p', 'u.id = p.user_id')
    ->join('comments c', 'p.id = c.post_id', 'LEFT')
    ->where('u.status = ?', ['active'])
    ->get();
```

### ORDER BY

```php
// Single column ordering
$users = db()->select('*')
    ->from('users')
    ->orderBy('created_at', 'DESC')
    ->get();

// Multiple column ordering
$users = db()->select('*')
    ->from('users')
    ->orderBy('status', 'ASC')
    ->orderBy('created_at', 'DESC')
    ->get();

// Order with other clauses
$users = db()->select('*')
    ->from('users')
    ->where('status = ?', ['active'])
    ->orderBy('display_name', 'ASC')
    ->limit(10)
    ->get();
```

### LIMIT & Pagination

```php
// Simple limit
$users = db()->select('*')
    ->from('users')
    ->limit(10)
    ->get();

// Limit with offset for pagination
$page = 2;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$users = db()->select('*')
    ->from('users')
    ->limit($perPage, $offset)
    ->get();

// Get total count for pagination
$total = db()->select()->from('users')->count();
$totalPages = ceil($total / $perPage);
```

### Transactions

```php
try {
    db()->beginTransaction();

    // Insert user
    $userId = db()->insert([
        'display_name' => 'John Doe',
        'email' => 'john@example.com'
    ])->into('users');

    // Insert related profile
    db()->insert([
        'user_id' => $userId,
        'bio' => 'Software Developer'
    ])->into('profiles');

    // Update statistics
    db()->update(['user_count' => db()->query("SELECT COUNT(*) FROM users")[0]['COUNT(*)']])
        ->table('stats')
        ->where('id = 1')
        ->execute();

    db()->commit();
    echo "Transaction completed successfully";

} catch (Exception $e) {
    db()->rollback();
    echo "Transaction failed: " . $e->getMessage();
}
```

## Entity System

The Entity System provides an object-oriented layer on top of the database query builder, allowing you to work with database records as objects. Each database table is represented by an Entity class that extends the base Entity class.

### Entity Base Class

The `Entity` class (`/lib/db/entity.php`) provides the foundation for all entity classes:

#### Key Features
- Automatic handling of `id`, `created_at`, and `updated_at` fields
- **IMPORTANT**: All timestamp fields (`created_at`, `updated_at`, etc.) use Unix timestamps (integer values)
- CRUD operations: `get()`, `create()`, `update()`, `delete()`
- Data storage in `$data` array with magic getters/setters
- Find methods for querying records
- Built-in validation and business logic

#### Basic Entity Methods

```php
// Get entity by ID
$entity = EntityClass::get($id);

// Create new entity
$entity = EntityClass::create(['field' => 'value']);

// Update entity
$entity->update(['field' => 'new value']);

// Delete entity
$entity->delete();

// Find entities
$entities = EntityClass::find(['status' => 'active']);
$entity = EntityClass::findFirst(['email' => 'user@example.com']);

// Get all entities
$all = EntityClass::all();

// Count entities
$count = EntityClass::count(['status' => 'active']);

// Check if entity exists
$exists = EntityClass::exists($id);
```

### Creating Entity Classes

To create a new entity class, extend the base `Entity` class and define the table name:

```php
class Product extends Entity {
    protected static $table = 'products';

    // Add custom methods specific to your entity
    public function getPrice() {
        return $this->getValue('price');
    }

    public function isInStock() {
        return $this->getValue('stock_quantity', 0) > 0;
    }
}
```

### Entity CRUD Operations

#### Create
```php
// Create with validation
$user = User::create([
    'firebase_uid' => 'firebase_uid_abc123',
    'phone_number' => '+1234567890',
    'display_name' => 'John Doe'
]);

// Access created entity data
echo $user->getValue('id');           // Auto-generated ID
echo $user->getValue('created_at');   // Unix timestamp (e.g., 1735030800)
```

#### Read
```php
// Get by ID
$user = User::get(1);

// Find methods
$user = User::findByEmail('john@example.com');
$users = User::find(['status' => 'active'], 10); // Limit 10
$first = User::findFirst(['role' => 'admin']);

// Magic getters
echo $user->display_name;  // Same as getValue('display_name')
echo $user->email;
```

#### Update
```php
// Update entity
$user->update(['display_name' => 'Jane Doe']);

// Using magic setters
$user->display_name = 'New Name';
$user->save();

// Bulk update
$user->setData([
    'display_name' => 'Updated Name',
    'email' => 'new@example.com'
]);
$user->save();
```

#### Delete
```php
// Delete instance
$user->delete();

// Static delete by ID
User::destroy($id);
```

### User Entity

The `User` class (`/lib/db/user.php`) extends Entity with user-specific functionality:

```php
// Create user with Firebase Phone Authentication
$user = User::create([
    'firebase_uid' => 'firebase_uid_123',
    'phone_number' => '+1234567890',
    'display_name' => 'John Doe' // Optional, will use masked phone if not provided
]);

// Find users
$user = User::findByFirebaseUid('firebase_uid_123');
$user = User::findByPhoneNumber('+1234567890');
$user = User::findByDisplayName('John Doe');

// Create or get user (useful for Firebase authentication flow)
$user = User::createOrGetByFirebaseUid(
    'firebase_uid_123',
    '+1234567890',
    ['display_name' => 'John Doe'] // Optional additional data
);

// Status management
if ($user->isActive()) {
    $user->deactivate();
}
$user->activate();

// Get user's posts
$posts = $user->getPosts(10); // Limit 10

// Get active/inactive users
$activeUsers = User::getActiveUsers();
$inactiveUsers = User::getInactiveUsers();
```

### Post Entity

The `Post` class (`/lib/db/post.php`) extends Entity with post/article functionality:

```php
// Create post
$post = Post::create([
    'user_id' => 1,
    'title' => 'My First Post',
    'content' => 'Post content here...',
    'status' => 'draft' // Optional, default is 'draft'
]);

// Find posts
$post = Post::findBySlug('my-first-post'); // Auto-generated slug
$posts = Post::getByUser($userId);
$published = Post::getPublished(10); // Limit 10
$drafts = Post::getDrafts();

// Post management
$post->publish();  // Set status to 'published'
$post->unpublish(); // Set status to 'draft'

// View tracking
$post->incrementViewCount();
echo $post->getViewCount();

// Get author
$author = $post->getAuthor(); // Returns User entity

// Content helpers
echo $post->getTitle();
echo $post->getExcerpt(150); // First 150 characters

// Search posts
$results = Post::search('keyword', 20); // Search with limit

// Get recent posts
$recent = Post::getRecent(5); // 5 most recent published posts
```

### Entity Magic Methods

All entities support magic methods for convenient property access:

```php
$user = User::get(1);

// Magic getter
echo $user->display_name;     // Same as getValue('display_name')

// Magic setter
$user->display_name = 'New Name'; // Same as setValue('display_name', 'New Name')

// Magic isset
if (isset($user->email)) {
    echo "Email is set";
}

// Magic unset
unset($user->temporary_field);
```

### Entity Utilities

```php
// Convert to array
$array = $user->toArray();

// Convert to JSON
$json = $user->toJson();

// Refresh from database
$user->refresh();

// Get all data
$data = $user->getData();

// Check existence
if (User::exists($id)) {
    echo "User exists";
}

// Count records
$total = User::count();
$active = User::count(['status' => 'active']);
```

### Testing Entity System

Run the entity system tests:

```bash
php tests/db/entity.test.php
```

The test suite covers:
- Entity CRUD operations
- User entity functionality
- Post entity functionality
- Magic methods
- Relationships between entities
- Validation and error handling

## Method Reference

### Query Building Methods

| Method | Description | Returns |
|--------|-------------|---------|
| `select($fields)` | Start a SELECT query | `$this` |
| `insert($data)` | Start an INSERT query | `$this` |
| `update($data)` | Start an UPDATE query | `$this` |
| `delete()` | Start a DELETE query | `$this` |
| `table($table)` | Set the table name | `$this` |
| `from($table)` | Alias for `table()` | `$this` |
| `into($table)` | Set table and execute INSERT | `int` (insert ID) or `$this` |
| `where($condition, $params)` | Add WHERE condition | `$this` |
| `orWhere($condition, $params)` | Add OR WHERE condition | `$this` |
| `join($table, $on, $type)` | Add JOIN clause | `$this` |
| `orderBy($field, $direction)` | Add ORDER BY | `$this` |
| `limit($limit, $offset)` | Add LIMIT clause | `$this` |

### Execution Methods

| Method | Description | Returns |
|--------|-------------|---------|
| `get()` | Execute SELECT and return all results | `array` |
| `first()` | Execute SELECT and return first result | `array\|null` |
| `count()` | Execute SELECT and return count | `int` |
| `execute()` | Execute the built query | `mixed` |
| `query($sql, $params)` | Execute raw SQL query | `mixed` |

### Transaction Methods

| Method | Description | Returns |
|--------|-------------|---------|
| `beginTransaction()` | Start a transaction | `void` |
| `commit()` | Commit the transaction | `void` |
| `rollback()` | Rollback the transaction | `void` |

## Examples

### User Registration Example

```php
// Check if email exists
$existing = db()->select('id')
    ->from('users')
    ->where('email = ?', [$email])
    ->first();

if ($existing) {
    throw new Exception('Email already registered');
}

// Insert new user
$userId = db()->insert([
    'display_name' => $name,
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'created_at' => date('Y-m-d H:i:s')
])->into('users');

// Create user profile
db()->insert([
    'user_id' => $userId,
    'avatar' => '/default-avatar.png'
])->into('profiles');
```

### Blog Post with Comments Example

```php
// Get post with author and comment count
$post = db()->query("
    SELECT
        p.*,
        u.display_name as author_name,
        COUNT(c.id) as comment_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN comments c ON p.id = c.post_id
    WHERE p.id = ? AND p.status = 'published'
    GROUP BY p.id
", [$postId])[0];

// Get comments for the post
$comments = db()->select('c.*, u.display_name')
    ->from('comments c')
    ->join('users u', 'c.user_id = u.id')
    ->where('c.post_id = ?', [$postId])
    ->orderBy('c.created_at', 'DESC')
    ->limit(10)
    ->get();
```

### Search with Pagination Example

```php
function searchUsers($keyword, $page = 1, $perPage = 20) {
    $offset = ($page - 1) * $perPage;

    // Get total count
    $total = db()->select()
        ->from('users')
        ->where('display_name LIKE ? OR email LIKE ?', ["%$keyword%", "%$keyword%"])
        ->count();

    // Get paginated results
    $users = db()->select('*')
        ->from('users')
        ->where('display_name LIKE ? OR email LIKE ?', ["%$keyword%", "%$keyword%"])
        ->orderBy('display_name', 'ASC')
        ->limit($perPage, $offset)
        ->get();

    return [
        'users' => $users,
        'total' => $total,
        'pages' => ceil($total / $perPage),
        'current_page' => $page
    ];
}
```

## Best Practices

1. **Always use prepared statements** - Use placeholders (?) for user input to prevent SQL injection
2. **Use transactions** for related operations that should succeed or fail together
3. **Limit SELECT fields** - Only select the columns you need instead of using `*`
4. **Add indexes** to columns used in WHERE, ORDER BY, and JOIN clauses
5. **Use `first()` instead of `get()[0]` when expecting a single result
6. **Check return values** - INSERT returns ID, UPDATE/DELETE return affected rows
7. **Close connections** - Call `db_close()` when done with database operations in long-running scripts