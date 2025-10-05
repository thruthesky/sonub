<?php
if (is_dev_computer()) {
    require_once ROOT_DIR . '/etc/config/db.dev.config.php';
} else {
    require_once ROOT_DIR . '/etc/config/db.config.php';
}

/**
 * Database Query Builder Class
 * Provides a fluent interface for building and executing database queries
 *
 * Usage examples:
 * - Insert: db()->insert(['display_name' => 'jaeho'])->into('users');
 * - Select: db()->select('*')->from('users')->where('id > 2')->limit(5)->get();
 * - Update: db()->update(['display_name' => 'song'])->table('users')->where("display_name='jaeho'")->execute();
 * - Delete: db()->delete()->from('users')->where('id = 5')->execute();
 * - Raw Query: db()->query("SELECT * FROM users WHERE id = ?", [1]);
 */
class Db
{
    protected $connection;

    // Query building properties
    protected $type = null; // 'select', 'insert', 'update', 'delete'
    protected $table = null;
    protected $fields = '*';
    protected $data = [];
    protected $whereClause = '';
    protected $whereParams = [];
    protected $orderBy = '';
    protected $limitClause = '';
    protected $joinClause = '';

    public function __construct()
    {
        $this->connection = db_connection();
    }

    /**
     * Reset query builder state
     */
    protected function reset()
    {
        $this->type = null;
        $this->table = null;
        $this->fields = '*';
        $this->data = [];
        $this->whereClause = '';
        $this->whereParams = [];
        $this->orderBy = '';
        $this->limitClause = '';
        $this->joinClause = '';
    }

    /**
     * Start a SELECT query
     * @param string $fields Fields to select (default: *)
     * @return $this
     */
    public function select($fields = '*')
    {
        $this->reset();
        $this->type = 'select';
        $this->fields = $fields;
        return $this;
    }

    /**
     * Start an INSERT query
     * @param array $data Associative array of column => value pairs
     * @return $this
     */
    public function insert(array $data)
    {
        $this->reset();
        $this->type = 'insert';
        $this->data = $data;
        return $this;
    }

    /**
     * Start an UPDATE query
     * @param array $data Associative array of column => value pairs
     * @return $this
     */
    public function update(array $data)
    {
        $this->reset();
        $this->type = 'update';
        $this->data = $data;
        return $this;
    }

    /**
     * Start a DELETE query
     * @return $this
     */
    public function delete()
    {
        $this->reset();
        $this->type = 'delete';
        return $this;
    }

    /**
     * Set the table for the query
     * @param string $table Table name
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Alias for table() method
     * @param string $table Table name
     * @return $this
     */
    public function from($table)
    {
        return $this->table($table);
    }

    /**
     * Alias for table() method (for INSERT queries)
     * @param string $table Table name
     * @return $this|int Returns $this for chaining or insert ID for INSERT queries
     */
    public function into($table)
    {
        $this->table = $table;

        // If this is an INSERT query, execute it immediately
        if ($this->type === 'insert') {
            return $this->executeInsert();
        }

        return $this;
    }

    /**
     * Add WHERE condition
     * @param string $condition WHERE condition (can include placeholders)
     * @param array $params Parameters for placeholders
     * @return $this
     */
    public function where($condition, array $params = [])
    {
        if ($this->whereClause) {
            $this->whereClause .= " AND ($condition)";
        } else {
            $this->whereClause = $condition;
        }

        if (!empty($params)) {
            $this->whereParams = array_merge($this->whereParams, $params);
        }

        return $this;
    }

    /**
     * Add OR WHERE condition
     * @param string $condition WHERE condition
     * @param array $params Parameters for placeholders
     * @return $this
     */
    public function orWhere($condition, array $params = [])
    {
        if ($this->whereClause) {
            $this->whereClause .= " OR ($condition)";
        } else {
            $this->whereClause = $condition;
        }

        if (!empty($params)) {
            $this->whereParams = array_merge($this->whereParams, $params);
        }

        return $this;
    }

    /**
     * Add ORDER BY clause
     * @param string $field Field to order by
     * @param string $direction Direction (ASC or DESC)
     * @return $this
     */
    public function orderBy($field, $direction = 'ASC')
    {
        $direction = strtoupper($direction);
        if ($this->orderBy) {
            $this->orderBy .= ", $field $direction";
        } else {
            $this->orderBy = "$field $direction";
        }
        return $this;
    }

    /**
     * Add LIMIT clause
     * @param int $limit Number of records to limit
     * @param int $offset Offset for pagination
     * @return $this
     */
    public function limit($limit, $offset = 0)
    {
        if ($offset > 0) {
            $this->limitClause = "LIMIT $offset, $limit";
        } else {
            $this->limitClause = "LIMIT $limit";
        }
        return $this;
    }

    /**
     * Add JOIN clause
     * @param string $table Table to join
     * @param string $on ON condition
     * @param string $type Type of join (INNER, LEFT, RIGHT)
     * @return $this
     */
    public function join($table, $on, $type = 'INNER')
    {
        $type = strtoupper($type);
        $this->joinClause .= " $type JOIN $table ON $on";
        return $this;
    }

    /**
     * Execute SELECT query and return results
     * @return array Query results
     */
    public function get()
    {
        if ($this->type !== 'select') {
            throw new Exception("get() can only be used with SELECT queries");
        }

        $sql = $this->buildSelectQuery();
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->whereParams);

        return $stmt->fetchAll();
    }

    /**
     * Execute SELECT query and return first result
     * @return array|null First row or null
     */
    public function first()
    {
        if ($this->type !== 'select') {
            throw new Exception("first() can only be used with SELECT queries");
        }

        $this->limit(1);
        $results = $this->get();

        return !empty($results) ? $results[0] : null;
    }

    /**
     * Execute SELECT query and return count
     * @return int Number of rows
     */
    public function count()
    {
        $this->fields = 'COUNT(*) as count';
        $result = $this->first();

        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Execute INSERT query
     * @return int Last insert ID
     */
    protected function executeInsert()
    {
        if (!$this->table || empty($this->data)) {
            throw new Exception("Table and data are required for INSERT query");
        }

        $columns = array_keys($this->data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($this->data));

        return $this->connection->lastInsertId();
    }

    /**
     * Execute UPDATE query
     * @return int Number of affected rows
     */
    public function executeUpdate()
    {
        if (!$this->table || empty($this->data)) {
            throw new Exception("Table and data are required for UPDATE query");
        }

        $setParts = [];
        $values = [];

        foreach ($this->data as $column => $value) {
            $setParts[] = "$column = ?";
            $values[] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);

        if ($this->whereClause) {
            $sql .= " WHERE {$this->whereClause}";
            $values = array_merge($values, $this->whereParams);
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($values);

        return $stmt->rowCount();
    }

    /**
     * Execute DELETE query
     * @return int Number of affected rows
     */
    public function executeDelete()
    {
        if (!$this->table) {
            throw new Exception("Table is required for DELETE query");
        }

        $sql = "DELETE FROM {$this->table}";

        if ($this->whereClause) {
            $sql .= " WHERE {$this->whereClause}";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->whereParams);

        return $stmt->rowCount();
    }

    /**
     * Execute query based on type
     * @return mixed Query result
     */
    public function execute()
    {
        switch ($this->type) {
            case 'select':
                return $this->get();
            case 'update':
                return $this->executeUpdate();
            case 'delete':
                return $this->executeDelete();
            case 'insert':
                return $this->executeInsert();
            default:
                throw new Exception("Unknown query type");
        }
    }

    /**
     * Build SELECT query string
     * @return string SQL query
     */
    protected function buildSelectQuery()
    {
        if (!$this->table) {
            throw new Exception("Table is required for SELECT query");
        }

        $sql = "SELECT {$this->fields} FROM {$this->table}";

        if ($this->joinClause) {
            $sql .= $this->joinClause;
        }

        if ($this->whereClause) {
            $sql .= " WHERE {$this->whereClause}";
        }

        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }

        if ($this->limitClause) {
            $sql .= " {$this->limitClause}";
        }

        return $sql;
    }

    /**
     * Execute raw SQL query
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return mixed Query result
     */
    public function query($sql, array $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        // Determine query type
        $queryType = strtoupper(substr(trim($sql), 0, 6));

        switch ($queryType) {
            case 'SELECT':
                return $stmt->fetchAll();
            case 'INSERT':
                return $this->connection->lastInsertId();
            case 'UPDATE':
            case 'DELETE':
                return $stmt->rowCount();
            default:
                return true;
        }
    }

    /**
     * Start a database transaction
     */
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    /**
     * Commit a database transaction
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * Rollback a database transaction
     */
    public function rollback()
    {
        $this->connection->rollback();
    }
}

/**
 * Get database instance (singleton)
 * @return Db Database instance
 */
function db(): Db
{
    static $db = null;
    if ($db === null) {
        $db = new Db();
    }
    return $db;
}

/**
 * Get database connection (singleton)
 * @return PDO Database connection
 */
function db_connection(): PDO
{
    static $connection = null;
    if ($connection === null) {
        // 설정 파일에서 정의된 상수 사용
        $host = DB_HOST;
        $db = DB_NAME;
        $user = DB_USER;
        $pass = DB_PASSWORD;
        $charset = 'utf8mb4';
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $connection = new PDO($dsn, $user, $pass, $options);
    }
    return $connection;
}

/**
 * Close database connection
 */
function db_close(): void
{
    static $connection = null;
    if ($connection !== null) {
        $connection = null;
    }
}
