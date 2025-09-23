<?php

/**
 * Entity Base Class
 *
 * Base class for all database entities (users, posts, etc.)
 * Each entity represents a single database record with common fields and methods
 *
 * IMPORTANT: All timestamp fields use Unix timestamps (integer values)
 * - created_at: Unix timestamp when the record was created
 * - updated_at: Unix timestamp when the record was last updated
 * - All other datetime fields should also use Unix timestamps for consistency
 *
 * Usage:
 * - Extend this class for each database table
 * - Override $table property with actual table name
 * - Use get(), create(), update(), delete() methods for CRUD operations
 *
 * Example:
 * class User extends Entity {
 *     protected static $table = 'users';
 * }
 *
 * $user = User::get(1);
 * $user->update(['display_name' => 'New Name']);
 */

require_once __DIR__ . '/db.php';

abstract class Entity {
    /**
     * Table name - must be overridden in child classes
     * @var string
     */
    protected static $table = null;

    /**
     * Data storage for entity fields
     * @var array
     */
    protected $data = [];

    /**
     * Whether this entity exists in database
     * @var bool
     */
    protected $exists = false;

    /**
     * Constructor
     * @param array $data Initial data
     * @param bool $exists Whether record exists in database
     */
    public function __construct(array $data = [], bool $exists = false) {
        $this->data = $data;
        $this->exists = $exists;
    }

    /**
     * Get entity by ID
     * @param int $id Entity ID
     * @return static|null Entity instance or null if not found
     */
    public static function get($id) {
        if (!static::$table) {
            throw new Exception('Table name not defined for ' . get_called_class());
        }

        $data = db()->select('*')
            ->from(static::$table)
            ->where('id = ?', [$id])
            ->first();

        if ($data) {
            return new static($data, true);
        }

        return null;
    }

    /**
     * Find entities by conditions
     * @param array $conditions Associative array of field => value conditions
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of entity instances
     */
    public static function find(array $conditions = [], $limit = null, $offset = 0) {
        if (!static::$table) {
            throw new Exception('Table name not defined for ' . get_called_class());
        }

        $query = db()->select('*')->from(static::$table);

        // Build WHERE conditions
        foreach ($conditions as $field => $value) {
            $query->where("$field = ?", [$value]);
        }

        // Add limit if specified
        if ($limit !== null) {
            $query->limit($limit, $offset);
        }

        $results = $query->get();
        $entities = [];

        foreach ($results as $row) {
            $entities[] = new static($row, true);
        }

        return $entities;
    }

    /**
     * Find first entity matching conditions
     * @param array $conditions Associative array of field => value conditions
     * @return static|null First matching entity or null
     */
    public static function findFirst(array $conditions) {
        $results = static::find($conditions, 1);
        return $results ? $results[0] : null;
    }

    /**
     * Get all entities from table
     * @param int|null $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of entity instances
     */
    public static function all($limit = null, $offset = 0) {
        return static::find([], $limit, $offset);
    }

    /**
     * Create new entity in database
     * @param array $data Entity data
     * @return static Created entity instance
     */
    public static function create(array $data) {
        if (!static::$table) {
            throw new Exception('Table name not defined for ' . get_called_class());
        }

        // Add created_at Unix timestamp if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = time();
        }

        // Add updated_at Unix timestamp
        $data['updated_at'] = time();

        // Insert into database
        $id = db()->insert($data)->into(static::$table);

        // Add ID to data
        $data['id'] = $id;

        // Return new entity instance
        return new static($data, true);
    }

    /**
     * Update entity in database
     * @param array $data Data to update
     * @return bool True if successful
     */
    public function update(array $data) {
        if (!$this->exists) {
            throw new Exception('Cannot update non-existent entity');
        }

        if (!isset($this->data['id'])) {
            throw new Exception('Entity ID not set');
        }

        // Add updated_at Unix timestamp
        $data['updated_at'] = time();

        // Update database
        $affected = db()->update($data)
            ->table(static::$table)
            ->where('id = ?', [$this->data['id']])
            ->execute();

        // Update local data
        if ($affected > 0) {
            $this->data = array_merge($this->data, $data);
            return true;
        }

        return false;
    }

    /**
     * Save entity to database (create or update)
     * @return bool True if successful
     */
    public function save() {
        if ($this->exists) {
            // Update existing record
            $updateData = $this->data;
            unset($updateData['id']); // Don't update ID
            unset($updateData['created_at']); // Don't update created_at

            return $this->update($updateData);
        } else {
            // Create new record
            $entity = static::create($this->data);
            $this->data = $entity->data;
            $this->exists = true;
            return true;
        }
    }

    /**
     * Delete entity from database
     * @return bool True if successful
     */
    public function delete() {
        if (!$this->exists) {
            throw new Exception('Cannot delete non-existent entity');
        }

        if (!isset($this->data['id'])) {
            throw new Exception('Entity ID not set');
        }

        $deleted = db()->delete()
            ->from(static::$table)
            ->where('id = ?', [$this->data['id']])
            ->execute();

        if ($deleted > 0) {
            $this->exists = false;
            return true;
        }

        return false;
    }

    /**
     * Delete entity by ID
     * @param int $id Entity ID to delete
     * @return bool True if successful
     */
    public static function destroy($id) {
        $entity = static::get($id);
        if ($entity) {
            return $entity->delete();
        }
        return false;
    }

    /**
     * Count entities matching conditions
     * @param array $conditions Associative array of field => value conditions
     * @return int Number of matching entities
     */
    public static function count(array $conditions = []) {
        if (!static::$table) {
            throw new Exception('Table name not defined for ' . get_called_class());
        }

        $query = db()->select()->from(static::$table);

        // Build WHERE conditions
        foreach ($conditions as $field => $value) {
            $query->where("$field = ?", [$value]);
        }

        return $query->count();
    }

    /**
     * Check if entity exists in database
     * @param int $id Entity ID
     * @return bool True if exists
     */
    public static function exists($id) {
        return static::get($id) !== null;
    }

    /**
     * Reload entity data from database
     * @return bool True if successful
     */
    public function refresh() {
        if (!$this->exists || !isset($this->data['id'])) {
            return false;
        }

        $fresh = static::get($this->data['id']);
        if ($fresh) {
            $this->data = $fresh->data;
            return true;
        }

        return false;
    }

    /**
     * Get entity field value
     * @param string $field Field name
     * @param mixed $default Default value if field not set
     * @return mixed Field value
     */
    public function getValue($field, $default = null) {
        return $this->data[$field] ?? $default;
    }

    /**
     * Set entity field value
     * @param string $field Field name
     * @param mixed $value Field value
     * @return $this
     */
    public function setValue($field, $value) {
        $this->data[$field] = $value;
        return $this;
    }

    /**
     * Get all entity data
     * @return array Entity data
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Set multiple field values
     * @param array $data Associative array of field => value
     * @return $this
     */
    public function setData(array $data) {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Convert entity to array
     * @return array Entity data
     */
    public function toArray() {
        return $this->data;
    }

    /**
     * Convert entity to JSON
     * @return string JSON representation
     */
    public function toJson() {
        return json_encode($this->data);
    }

    /**
     * Magic getter for entity fields
     * @param string $field Field name
     * @return mixed Field value
     */
    public function __get($field) {
        return $this->getValue($field);
    }

    /**
     * Magic setter for entity fields
     * @param string $field Field name
     * @param mixed $value Field value
     */
    public function __set($field, $value) {
        $this->setValue($field, $value);
    }

    /**
     * Check if field is set
     * @param string $field Field name
     * @return bool True if field is set
     */
    public function __isset($field) {
        return isset($this->data[$field]);
    }

    /**
     * Unset field
     * @param string $field Field name
     */
    public function __unset($field) {
        unset($this->data[$field]);
    }
}