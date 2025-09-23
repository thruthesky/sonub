<?php

/**
 * User Entity Class
 *
 * Represents a user record in the database
 * Uses Firebase Phone Authentication for user authentication
 *
 * Usage:
 * $user = User::get(1);                                    // Get user by ID
 * $user = User::create(['firebase_uid' => 'abc123', ...]);  // Create user with Firebase UID
 * $user->update(['display_name' => 'Jane']);               // Update user
 * $user->delete();                                        // Delete user
 *
 * $users = User::find(['status' => 'active']);            // Find active users
 * $user = User::findByFirebaseUid('firebase_uid_123');    // Find by Firebase UID
 */

require_once __DIR__ . '/entity.php';

class User extends Entity
{
    /**
     * Database table name
     * @var string
     */
    protected static $table = 'users';

    /**
     * Find user by Firebase UID
     * @param string $firebaseUid Firebase Authentication UID
     * @return User|null User instance or null if not found
     */
    public static function findByFirebaseUid($firebaseUid)
    {
        return static::findFirst(['firebase_uid' => $firebaseUid]);
    }

    /**
     * Find user by phone number
     * @param string $phoneNumber Phone number
     * @return User|null User instance or null if not found
     */
    public static function findByPhoneNumber($phoneNumber)
    {
        return static::findFirst(['phone_number' => $phoneNumber]);
    }

    /**
     * Find user by display_name
     * @param string $displayName Display name
     * @return User|null User instance or null if not found
     */
    public static function findByDisplayName($displayName)
    {
        return static::findFirst(['display_name' => $displayName]);
    }

    /**
     * Get user's posts
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of Post entities
     */
    public function getPosts($limit = null, $offset = 0)
    {
        // Assuming Post class exists
        if (class_exists('Post')) {
            return Post::find(['user_id' => $this->getValue('id')], $limit, $offset);
        }
        return [];
    }

    /**
     * Get user's Firebase UID
     * @return string|null Firebase UID
     */
    public function getFirebaseUid()
    {
        return $this->getValue('firebase_uid');
    }

    /**
     * Get user's phone number
     * @return string|null Phone number
     */
    public function getPhoneNumber()
    {
        return $this->getValue('phone_number');
    }

    /**
     * Get user's display name
     * @return string|null Display name
     */
    public function getDisplayName()
    {
        return $this->getValue('display_name');
    }

    /**
     * Get user's status
     * @return string|null User status
     */
    public function getStatus()
    {
        return $this->getValue('status', 'active');
    }

    /**
     * Check if user is active
     * @return bool True if user is active
     */
    public function isActive()
    {
        return $this->getStatus() === 'active';
    }

    /**
     * Activate user
     * @return bool True if successful
     */
    public function activate()
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Deactivate user
     * @return bool True if successful
     */
    public function deactivate()
    {
        return $this->update(['status' => 'inactive']);
    }

    /**
     * Get all active users
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of User entities
     */
    public static function getActiveUsers($limit = null, $offset = 0)
    {
        return static::find(['status' => 'active'], $limit, $offset);
    }

    /**
     * Get all inactive users
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of User entities
     */
    public static function getInactiveUsers($limit = null, $offset = 0)
    {
        return static::find(['status' => 'inactive'], $limit, $offset);
    }

    /**
     * Create user with proper validation
     * @param array $data User data
     * @return static Created user instance
     * @throws Exception If validation fails
     */
    public static function create(array $data)
    {
        // Validate required fields
        if (empty($data['firebase_uid'])) {
            throw new Exception('Firebase UID is required');
        }

        if (empty($data['phone_number'])) {
            throw new Exception('Phone number is required');
        }

        // Check if Firebase UID already exists
        if (static::findByFirebaseUid($data['firebase_uid'])) {
            throw new Exception('Firebase UID already exists');
        }

        // Check if phone number already exists
        if (static::findByPhoneNumber($data['phone_number'])) {
            throw new Exception('Phone number already exists');
        }

        // Set default display name if not provided
        if (empty($data['display_name'])) {
            // Use phone number as default display name (masked for privacy)
            $data['display_name'] = substr($data['phone_number'], 0, 3) . '****' . substr($data['phone_number'], -4);
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
    public function update(array $data)
    {
        // Firebase UID should not be changed
        if (isset($data['firebase_uid'])) {
            throw new Exception('Firebase UID cannot be changed');
        }

        // If phone number is being changed, check uniqueness
        if (!empty($data['phone_number']) && $data['phone_number'] !== $this->getPhoneNumber()) {
            $existing = static::findByPhoneNumber($data['phone_number']);
            if ($existing && $existing->getValue('id') !== $this->getValue('id')) {
                throw new Exception('Phone number already exists');
            }
        }

        // Call parent update method
        return parent::update($data);
    }

    /**
     * Create or get user by Firebase UID
     * @param string $firebaseUid Firebase UID
     * @param string $phoneNumber Phone number from Firebase
     * @param array $additionalData Additional user data
     * @return User User instance
     * @throws Exception If creation fails
     */
    public static function createOrGetByFirebaseUid($firebaseUid, $phoneNumber, array $additionalData = [])
    {
        // Try to find existing user
        $user = static::findByFirebaseUid($firebaseUid);

        if ($user) {
            // Update phone number if changed
            if ($user->getPhoneNumber() !== $phoneNumber) {
                $user->update(['phone_number' => $phoneNumber]);
            }
            return $user;
        }

        // Create new user
        $data = array_merge($additionalData, [
            'firebase_uid' => $firebaseUid,
            'phone_number' => $phoneNumber
        ]);

        return static::create($data);
    }
}