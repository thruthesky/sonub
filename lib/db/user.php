<?php

/**
 * User Entity Class
 *
 * Represents a user record in the database
 *
 * Usage:
 * $user = User::get(1);                           // Get user by ID
 * $user = User::create(['display_name' => 'John', 'email' => 'john@example.com']);
 * $user->update(['display_name' => 'Jane']);      // Update user
 * $user->delete();                                // Delete user
 *
 * $users = User::find(['status' => 'active']);    // Find active users
 * $user = User::findByEmail('john@example.com');  // Find by email
 */

require_once __DIR__ . '/entity.php';

class User extends Entity {
    /**
     * Database table name
     * @var string
     */
    protected static $table = 'users';

    /**
     * Find user by email
     * @param string $email Email address
     * @return User|null User instance or null if not found
     */
    public static function findByEmail($email) {
        return static::findFirst(['email' => $email]);
    }

    /**
     * Find user by display_name
     * @param string $displayName Display name
     * @return User|null User instance or null if not found
     */
    public static function findByDisplayName($displayName) {
        return static::findFirst(['display_name' => $displayName]);
    }

    /**
     * Verify password
     * @param string $password Plain text password
     * @return bool True if password matches
     */
    public function verifyPassword($password) {
        $hash = $this->getValue('password_hash');
        if (!$hash) {
            return false;
        }
        return password_verify($password, $hash);
    }

    /**
     * Set password (hashes the password)
     * @param string $password Plain text password
     * @return $this
     */
    public function setPassword($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->setValue('password_hash', $hash);
        return $this;
    }

    /**
     * Get user's posts
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of Post entities
     */
    public function getPosts($limit = null, $offset = 0) {
        // Assuming Post class exists
        if (class_exists('Post')) {
            return Post::find(['user_id' => $this->getValue('id')], $limit, $offset);
        }
        return [];
    }

    /**
     * Get user's display name
     * @return string|null Display name
     */
    public function getDisplayName() {
        return $this->getValue('display_name');
    }

    /**
     * Get user's email
     * @return string|null Email address
     */
    public function getEmail() {
        return $this->getValue('email');
    }

    /**
     * Get user's status
     * @return string|null User status
     */
    public function getStatus() {
        return $this->getValue('status', 'active');
    }

    /**
     * Check if user is active
     * @return bool True if user is active
     */
    public function isActive() {
        return $this->getStatus() === 'active';
    }

    /**
     * Activate user
     * @return bool True if successful
     */
    public function activate() {
        return $this->update(['status' => 'active']);
    }

    /**
     * Deactivate user
     * @return bool True if successful
     */
    public function deactivate() {
        return $this->update(['status' => 'inactive']);
    }

    /**
     * Get all active users
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of User entities
     */
    public static function getActiveUsers($limit = null, $offset = 0) {
        return static::find(['status' => 'active'], $limit, $offset);
    }

    /**
     * Get all inactive users
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of User entities
     */
    public static function getInactiveUsers($limit = null, $offset = 0) {
        return static::find(['status' => 'inactive'], $limit, $offset);
    }

    /**
     * Create user with proper validation
     * @param array $data User data
     * @return static Created user instance
     * @throws Exception If validation fails
     */
    public static function create(array $data) {
        // Validate required fields
        if (empty($data['email'])) {
            throw new Exception('Email is required');
        }

        if (empty($data['display_name'])) {
            throw new Exception('Display name is required');
        }

        // Check if email already exists
        if (static::findByEmail($data['email'])) {
            throw new Exception('Email already exists');
        }

        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']); // Don't store plain password
        }

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        // Call parent create method
        return parent::create($data);
    }

    /**
     * Update user with validation
     * @param array $data Data to update
     * @return bool True if successful
     * @throws Exception If validation fails
     */
    public function update(array $data) {
        // If email is being changed, check uniqueness
        if (!empty($data['email']) && $data['email'] !== $this->getEmail()) {
            $existing = static::findByEmail($data['email']);
            if ($existing && $existing->getValue('id') !== $this->getValue('id')) {
                throw new Exception('Email already exists');
            }
        }

        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']); // Don't store plain password
        }

        // Call parent update method
        return parent::update($data);
    }
}