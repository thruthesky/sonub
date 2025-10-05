<?php


/**
 * 데이터베이스 쿼리 빌더 클래스
 *
 * 데이터베이스 쿼리를 구축하고 실행하기 위한 Fluent 인터페이스를 제공합니다.
 *
 * 사용 예제:
 * - 삽입: db()->insert(['display_name' => '재호'])->into('users');
 * - 조회: db()->select('*')->from('users')->where('id > 2')->limit(5)->get();
 * - 업데이트: db()->update(['display_name' => '송'])->table('users')->where("display_name='재호'")->execute();
 * - 삭제: db()->delete()->from('users')->where('id = 5')->execute();
 * - 원시 쿼리: db()->query("SELECT * FROM users WHERE id = ?", [1]);
 */
class Db
{
    protected $connection;

    // 쿼리 빌딩 속성들
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
     * 쿼리 빌더 상태 초기화
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
     * SELECT 쿼리 시작
     *
     * @param string $fields 조회할 필드 (기본값: *)
     * @return $this
     *
     * @example 모든 필드 조회
     * $users = db()->select('*')->from('users')->get();
     *
     * @example 특정 필드만 조회
     * $users = db()->select('id, name, email')->from('users')->get();
     *
     * @example COUNT 쿼리
     * $count = db()->select('COUNT(*) as total')->from('users')->first();
     */
    public function select($fields = '*')
    {
        $this->reset();
        $this->type = 'select';
        $this->fields = $fields;
        return $this;
    }

    /**
     * INSERT 쿼리 시작
     *
     * @param array $data 컬럼명 => 값 쌍의 연관 배열
     * @return $this
     *
     * @example 단일 레코드 삽입
     * $userId = db()->insert([
     *     'display_name' => '홍길동',
     *     'email' => 'hong@example.com',
     *     'created_at' => time()
     * ])->into('users');
     *
     * @example 여러 필드 삽입
     * $postId = db()->insert([
     *     'title' => '게시글 제목',
     *     'content' => '게시글 내용',
     *     'user_id' => 123,
     *     'created_at' => time()
     * ])->into('posts');
     */
    public function insert(array $data)
    {
        $this->reset();
        $this->type = 'insert';
        $this->data = $data;
        return $this;
    }

    /**
     * UPDATE 쿼리 시작
     *
     * @param array $data 컬럼명 => 값 쌍의 연관 배열
     * @return $this
     *
     * @example 특정 사용자 정보 업데이트
     * $affected = db()->update([
     *     'display_name' => '김철수',
     *     'updated_at' => time()
     * ])->table('users')->where('id = ?', [123])->execute();
     *
     * @example 여러 조건으로 업데이트
     * $affected = db()->update([
     *     'status' => 'active',
     *     'verified' => 1
     * ])->table('users')
     *   ->where('email LIKE ?', ['%@example.com'])
     *   ->execute();
     */
    public function update(array $data)
    {
        $this->reset();
        $this->type = 'update';
        $this->data = $data;
        return $this;
    }

    /**
     * DELETE 쿼리 시작
     *
     * @return $this
     *
     * @example 특정 레코드 삭제
     * $deleted = db()->delete()->from('users')->where('id = ?', [123])->execute();
     *
     * @example 조건에 맞는 여러 레코드 삭제
     * $deleted = db()->delete()
     *     ->from('posts')
     *     ->where('created_at < ?', [strtotime('-1 year')])
     *     ->execute();
     *
     * @example 모든 레코드 삭제 (주의!)
     * $deleted = db()->delete()->from('temp_data')->execute();
     */
    public function delete()
    {
        $this->reset();
        $this->type = 'delete';
        return $this;
    }

    /**
     * 쿼리에 사용할 테이블 설정
     *
     * @param string $table 테이블 이름
     * @return $this
     *
     * @example 기본 사용
     * db()->select('*')->table('users')->get();
     *
     * @example UPDATE 쿼리에서 사용
     * db()->update(['status' => 'active'])->table('users')->where('id = 1')->execute();
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }


    /**
     * table('users') 메서드의 별칭 (SELECT, DELETE, UPDATE 쿼리에서 주로 사용)
     */
    public function userTable()
    {
        return $this->table('users');
    }
    /**
     * table('posts') 메서드의 별칭 (SELECT, DELETE, UPDATE 쿼리에서 주로 사용)
     */
    public function postTable()
    {
        return $this->table('posts');
    }


    /**
     * table() 메서드의 별칭 (SELECT, DELETE 쿼리에서 주로 사용)
     *
     * @param string $table 테이블 이름
     * @return $this
     *
     * @example SELECT에서 사용
     * $users = db()->select('*')->from('users')->get();
     *
     * @example DELETE에서 사용
     * $deleted = db()->delete()->from('users')->where('id = ?', [123])->execute();
     *
     * @example WHERE 조건과 함께
     * $users = db()->select('*')->from('users')->where('age > ?', [18])->get();
     */
    public function from($table)
    {
        return $this->table($table);
    }

    public function fromUsers()
    {
        return $this->from('users');
    }
    public function fromPosts()
    {
        return $this->from('posts');
    }

    /**
     * table() 메서드의 별칭 (INSERT 쿼리에서 주로 사용)
     * INSERT 쿼리인 경우 즉시 실행됨
     *
     * @param string $table 테이블 이름
     * @return $this|int 체이닝을 위한 $this 또는 INSERT 쿼리의 경우 삽입된 ID
     *
     * @example 단일 사용자 삽입
     * $userId = db()->insert([
     *     'display_name' => '홍길동',
     *     'email' => 'hong@example.com'
     * ])->into('users');
     * echo "새 사용자 ID: " . $userId;
     *
     * @example 게시글 삽입 후 ID 사용
     * $postId = db()->insert([
     *     'title' => '새 게시글',
     *     'content' => '내용',
     *     'user_id' => 123
     * ])->into('posts');
     *
     * // 삽입된 ID로 추가 작업
     * db()->insert([
     *     'post_id' => $postId,
     *     'tag' => 'news'
     * ])->into('post_tags');
     */
    public function into($table)
    {
        $this->table = $table;

        // INSERT 쿼리인 경우 즉시 실행
        if ($this->type === 'insert') {
            return $this->executeInsert();
        }

        return $this;
    }

    /**
     * into('users') 메서드의 별칭 (INSERT 쿼리에서 주로 사용)
     * INSERT 쿼리인 경우 즉시 실행됨
     *
     * @param string $table 테이블 이름
     * @return $this|int 체이닝을 위한 $this 또는 INSERT 쿼리의 경우 삽입된 ID
     *
     * @example 단일 사용자 삽입
     * $userId = db()->insert([
     *     'display_name' => '홍길동',
     *     'email' => 'hong@example.com'
     * ])->into('users');
     * echo "새 사용자 ID: " . $userId;
     *
     * @example 게시글 삽입 후 ID 사용
     * $postId = db()->insert([
     *     'title' => '새 게시글',
     *     'content' => '내용',
     *     'user_id' => 123
     * ])->into('posts');
     *
     * // 삽입된 ID로 추가 작업
     * db()->insert([
     *     'post_id' => $postId,
     *     'tag' => 'news'
     * ])->into('post_tags');
     */
    public function intoUsers()
    {
        return $this->into('users');
    }

    public function intoPosts()
    {
        return $this->into('posts');
    }

    /**
     * WHERE 조건 추가
     *
     * @param string $condition WHERE 조건 (플레이스홀더 포함 가능)
     * @param array $params 플레이스홀더 매개변수
     * @return $this
     *
     * @example 단순 조건
     * $users = db()->select('*')->from('users')->where('id = 5')->get();
     *
     * @example 플레이스홀더 사용 (SQL 인젝션 방지)
     * $users = db()->select('*')->from('users')->where('id = ?', [123])->get();
     *
     * @example 여러 조건 (AND로 연결)
     * $users = db()->select('*')
     *     ->from('users')
     *     ->where('age > ?', [18])
     *     ->where('status = ?', ['active'])
     *     ->get();
     *
     * @example LIKE 검색
     * $users = db()->select('*')
     *     ->from('users')
     *     ->where('email LIKE ?', ['%@gmail.com'])
     *     ->get();
     *
     * @example IN 조건
     * $users = db()->select('*')
     *     ->from('users')
     *     ->where('id IN (?, ?, ?)', [1, 2, 3])
     *     ->get();
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
     * OR WHERE 조건 추가
     *
     * @param string $condition WHERE 조건
     * @param array $params 플레이스홀더 매개변수
     * @return $this
     *
     * @example OR 조건 사용
     * $users = db()->select('*')
     *     ->from('users')
     *     ->where('role = ?', ['admin'])
     *     ->orWhere('role = ?', ['moderator'])
     *     ->get();
     *
     * @example 복합 조건
     * $posts = db()->select('*')
     *     ->from('posts')
     *     ->where('status = ?', ['published'])
     *     ->where('user_id = ?', [123])
     *     ->orWhere('featured = ?', [1])
     *     ->get();
     *
     * @example 여러 OR 조건
     * $users = db()->select('*')
     *     ->from('users')
     *     ->where('age > ?', [18])
     *     ->orWhere('status = ?', ['verified'])
     *     ->orWhere('premium = ?', [1])
     *     ->get();
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
     * ORDER BY 절 추가
     *
     * @param string $field 정렬할 필드
     * @param string $direction 정렬 방향 (ASC 또는 DESC)
     * @return $this
     *
     * @example 오름차순 정렬
     * $users = db()->select('*')
     *     ->from('users')
     *     ->orderBy('created_at', 'ASC')
     *     ->get();
     *
     * @example 내림차순 정렬
     * $posts = db()->select('*')
     *     ->from('posts')
     *     ->orderBy('created_at', 'DESC')
     *     ->get();
     *
     * @example 여러 필드로 정렬
     * $users = db()->select('*')
     *     ->from('users')
     *     ->orderBy('last_name', 'ASC')
     *     ->orderBy('first_name', 'ASC')
     *     ->get();
     *
     * @example 최신 게시글 10개 조회
     * $recentPosts = db()->select('*')
     *     ->from('posts')
     *     ->orderBy('created_at', 'DESC')
     *     ->limit(10)
     *     ->get();
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
     * LIMIT 절 추가
     *
     * @param int $limit 제한할 레코드 수
     * @param int $offset 페이지네이션을 위한 오프셋
     * @return $this
     *
     * @example 최대 10개 레코드 조회
     * $users = db()->select('*')->from('users')->limit(10)->get();
     *
     * @example 페이지네이션 (1페이지: 0-9)
     * $page1 = db()->select('*')->from('posts')->limit(10, 0)->get();
     *
     * @example 페이지네이션 (2페이지: 10-19)
     * $page2 = db()->select('*')->from('posts')->limit(10, 10)->get();
     *
     * @example 페이지네이션 함수
     * function getPosts($page, $perPage = 10) {
     *     $offset = ($page - 1) * $perPage;
     *     return db()->select('*')
     *         ->from('posts')
     *         ->orderBy('created_at', 'DESC')
     *         ->limit($perPage, $offset)
     *         ->get();
     * }
     *
     * @example 최신 5개 게시글
     * $latestPosts = db()->select('*')
     *     ->from('posts')
     *     ->orderBy('created_at', 'DESC')
     *     ->limit(5)
     *     ->get();
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
     * JOIN 절 추가
     *
     * @param string $table 조인할 테이블
     * @param string $on ON 조건
     * @param string $type 조인 타입 (INNER, LEFT, RIGHT)
     * @return $this
     *
     * @example INNER JOIN
     * $results = db()->select('users.*, profiles.bio')
     *     ->from('users')
     *     ->join('profiles', 'users.id = profiles.user_id', 'INNER')
     *     ->get();
     *
     * @example LEFT JOIN
     * $results = db()->select('posts.*, users.display_name')
     *     ->from('posts')
     *     ->join('users', 'posts.user_id = users.id', 'LEFT')
     *     ->get();
     *
     * @example 여러 JOIN
     * $results = db()->select('posts.*, users.name, categories.title as category')
     *     ->from('posts')
     *     ->join('users', 'posts.user_id = users.id', 'LEFT')
     *     ->join('categories', 'posts.category_id = categories.id', 'LEFT')
     *     ->get();
     *
     * @example JOIN과 WHERE 함께 사용
     * $results = db()->select('posts.*, users.name')
     *     ->from('posts')
     *     ->join('users', 'posts.user_id = users.id', 'INNER')
     *     ->where('posts.status = ?', ['published'])
     *     ->orderBy('posts.created_at', 'DESC')
     *     ->get();
     */
    public function join($table, $on, $type = 'INNER')
    {
        $type = strtoupper($type);
        $this->joinClause .= " $type JOIN $table ON $on";
        return $this;
    }

    /**
     * SELECT 쿼리 실행 및 결과 반환
     *
     * @return array 쿼리 결과 배열
     *
     * @example 모든 사용자 조회
     * $users = db()->select('*')->from('users')->get();
     * foreach ($users as $user) {
     *     echo $user['display_name'];
     * }
     *
     * @example 조건부 조회
     * $activeUsers = db()->select('*')
     *     ->from('users')
     *     ->where('status = ?', ['active'])
     *     ->get();
     *
     * @example 정렬된 결과
     * $sortedUsers = db()->select('id, display_name, email')
     *     ->from('users')
     *     ->orderBy('created_at', 'DESC')
     *     ->get();
     *
     * @example 페이지네이션
     * $page1Users = db()->select('*')
     *     ->from('users')
     *     ->limit(10, 0)
     *     ->get();
     */
    public function get()
    {
        if ($this->type !== 'select') {
            throw new Exception("get()은 SELECT 쿼리에서만 사용할 수 있습니다");
        }

        $sql = $this->buildSelectQuery();
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->whereParams);

        return $stmt->fetchAll();
    }

    /**
     * SELECT 쿼리 실행 및 첫 번째 결과 반환
     *
     * @return array|null 첫 번째 행 또는 null
     *
     * @example 단일 사용자 조회
     * $user = db()->select('*')
     *     ->from('users')
     *     ->where('id = ?', [123])
     *     ->first();
     *
     * if ($user) {
     *     echo $user['display_name'];
     * } else {
     *     echo '사용자를 찾을 수 없습니다';
     * }
     *
     * @example 이메일로 사용자 찾기
     * $user = db()->select('*')
     *     ->from('users')
     *     ->where('email = ?', ['hong@example.com'])
     *     ->first();
     *
     * @example 최신 게시글 1개
     * $latestPost = db()->select('*')
     *     ->from('posts')
     *     ->orderBy('created_at', 'DESC')
     *     ->first();
     *
     * @example 특정 필드만 조회
     * $userEmail = db()->select('email')
     *     ->from('users')
     *     ->where('id = ?', [123])
     *     ->first();
     */
    public function first()
    {
        if ($this->type !== 'select') {
            throw new Exception("first()는 SELECT 쿼리에서만 사용할 수 있습니다");
        }

        $this->limit(1);
        $results = $this->get();

        return !empty($results) ? $results[0] : null;
    }

    /**
     * SELECT 쿼리 실행 및 개수 반환
     *
     * @return int 행 개수
     *
     * @example 전체 사용자 수
     * $totalUsers = db()->select('*')->from('users')->count();
     * echo "총 사용자: " . $totalUsers;
     *
     * @example 조건에 맞는 개수
     * $activeCount = db()->select('*')
     *     ->from('users')
     *     ->where('status = ?', ['active'])
     *     ->count();
     *
     * @example 오늘 등록한 사용자 수
     * $todayCount = db()->select('*')
     *     ->from('users')
     *     ->where('created_at > ?', [strtotime('today')])
     *     ->count();
     *
     * @example 카테고리별 게시글 수
     * $categories = db()->select('*')->from('categories')->get();
     * foreach ($categories as $category) {
     *     $count = db()->select('*')
     *         ->from('posts')
     *         ->where('category_id = ?', [$category['id']])
     *         ->count();
     *     echo "{$category['name']}: {$count}개\n";
     * }
     */
    public function count()
    {
        $this->fields = 'COUNT(*) as count';
        $result = $this->first();

        return $result ? (int)$result['count'] : 0;
    }

    /**
     * INSERT 쿼리 실행 (내부 메서드)
     *
     * @return int 마지막 삽입 ID
     *
     * @example into() 메서드를 통해 자동 호출됨
     * $userId = db()->insert(['name' => '홍길동'])->into('users');
     */
    protected function executeInsert()
    {
        if (!$this->table || empty($this->data)) {
            throw new Exception("INSERT 쿼리에는 테이블과 데이터가 필요합니다");
        }

        $columns = array_keys($this->data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($this->data));

        return $this->connection->lastInsertId();
    }

    /**
     * UPDATE 쿼리 실행
     *
     * @return int 영향받은 행 개수
     *
     * @example 특정 사용자 업데이트
     * $affected = db()->update([
     *     'display_name' => '김철수',
     *     'updated_at' => time()
     * ])->table('users')
     *   ->where('id = ?', [123])
     *   ->execute();
     *
     * echo "{$affected}개 행이 업데이트되었습니다";
     *
     * @example 여러 레코드 업데이트
     * $affected = db()->update(['status' => 'inactive'])
     *     ->table('users')
     *     ->where('last_login < ?', [strtotime('-1 year')])
     *     ->execute();
     *
     * @example 조건 없이 모든 레코드 업데이트 (주의!)
     * $affected = db()->update(['verified' => 0])
     *     ->table('temp_users')
     *     ->execute();
     */
    public function executeUpdate()
    {
        if (!$this->table || empty($this->data)) {
            throw new Exception("UPDATE 쿼리에는 테이블과 데이터가 필요합니다");
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
     * DELETE 쿼리 실행
     *
     * @return int 영향받은 행 개수
     *
     * @example 특정 사용자 삭제
     * $deleted = db()->delete()
     *     ->from('users')
     *     ->where('id = ?', [123])
     *     ->execute();
     *
     * echo "{$deleted}개 행이 삭제되었습니다";
     *
     * @example 오래된 레코드 삭제
     * $deleted = db()->delete()
     *     ->from('logs')
     *     ->where('created_at < ?', [strtotime('-30 days')])
     *     ->execute();
     *
     * @example 조건에 맞는 여러 레코드 삭제
     * $deleted = db()->delete()
     *     ->from('posts')
     *     ->where('status = ?', ['draft'])
     *     ->where('created_at < ?', [strtotime('-1 year')])
     *     ->execute();
     */
    public function executeDelete()
    {
        if (!$this->table) {
            throw new Exception("DELETE 쿼리에는 테이블이 필요합니다");
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
     * 타입에 따라 쿼리 실행
     *
     * @return mixed 쿼리 결과
     *
     * @example SELECT 실행
     * $users = db()->select('*')->from('users')->execute();
     *
     * @example UPDATE 실행
     * $affected = db()->update(['status' => 'active'])
     *     ->table('users')
     *     ->where('id = ?', [123])
     *     ->execute();
     *
     * @example DELETE 실행
     * $deleted = db()->delete()->from('users')->where('id = ?', [123])->execute();
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
                throw new Exception("알 수 없는 쿼리 타입입니다");
        }
    }

    /**
     * SELECT 쿼리 문자열 생성 (내부 메서드)
     *
     * @return string SQL 쿼리
     */
    protected function buildSelectQuery()
    {
        if (!$this->table) {
            throw new Exception("SELECT 쿼리에는 테이블이 필요합니다");
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
     * 원시 SQL 쿼리 실행
     *
     * @param string $sql SQL 쿼리
     * @param array $params 쿼리 매개변수
     * @return mixed 쿼리 결과
     *
     * @example SELECT 쿼리
     * $users = db()->query("SELECT * FROM users WHERE id > ?", [100]);
     * foreach ($users as $user) {
     *     echo $user['display_name'];
     * }
     *
     * @example INSERT 쿼리
     * $userId = db()->query(
     *     "INSERT INTO users (display_name, email) VALUES (?, ?)",
     *     ['홍길동', 'hong@example.com']
     * );
     * echo "새 사용자 ID: " . $userId;
     *
     * @example UPDATE 쿼리
     * $affected = db()->query(
     *     "UPDATE users SET status = ? WHERE id = ?",
     *     ['active', 123]
     * );
     * echo "{$affected}개 행 업데이트됨";
     *
     * @example DELETE 쿼리
     * $deleted = db()->query(
     *     "DELETE FROM users WHERE created_at < ?",
     *     [strtotime('-1 year')]
     * );
     *
     * @example 복잡한 쿼리
     * $results = db()->query("
     *     SELECT u.*, COUNT(p.id) as post_count
     *     FROM users u
     *     LEFT JOIN posts p ON u.id = p.user_id
     *     WHERE u.status = ?
     *     GROUP BY u.id
     *     HAVING post_count > ?
     *     ORDER BY post_count DESC
     * ", ['active', 10]);
     */
    public function query($sql, array $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        // 쿼리 타입 판단
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
     * 데이터베이스 트랜잭션 시작
     *
     * @example 기본 트랜잭션
     * try {
     *     db()->beginTransaction();
     *
     *     $userId = db()->insert(['name' => '홍길동'])->into('users');
     *     db()->insert(['user_id' => $userId, 'bio' => '안녕하세요'])->into('profiles');
     *
     *     db()->commit();
     *     echo '트랜잭션 성공';
     * } catch (Exception $e) {
     *     db()->rollback();
     *     echo '트랜잭션 실패: ' . $e->getMessage();
     * }
     *
     * @example 복잡한 트랜잭션
     * try {
     *     db()->beginTransaction();
     *
     *     // 사용자 생성
     *     $userId = db()->insert([
     *         'display_name' => '김철수',
     *         'email' => 'kim@example.com'
     *     ])->into('users');
     *
     *     // 프로필 생성
     *     db()->insert([
     *         'user_id' => $userId,
     *         'bio' => '자기소개'
     *     ])->into('profiles');
     *
     *     // 초기 설정 생성
     *     db()->insert([
     *         'user_id' => $userId,
     *         'notification' => 1,
     *         'theme' => 'light'
     *     ])->into('user_settings');
     *
     *     db()->commit();
     * } catch (Exception $e) {
     *     db()->rollback();
     *     throw $e;
     * }
     */
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    /**
     * 데이터베이스 트랜잭션 커밋
     *
     * @example 트랜잭션 커밋
     * db()->beginTransaction();
     * // ... 쿼리 실행 ...
     * db()->commit();
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * 데이터베이스 트랜잭션 롤백
     *
     * @example 에러 발생 시 롤백
     * try {
     *     db()->beginTransaction();
     *     // ... 쿼리 실행 ...
     *     db()->commit();
     * } catch (Exception $e) {
     *     db()->rollback();
     *     echo '롤백됨: ' . $e->getMessage();
     * }
     */
    public function rollback()
    {
        $this->connection->rollback();
    }
}

/**
 * 데이터베이스 인스턴스 가져오기 (싱글톤)
 *
 * @return Db 데이터베이스 인스턴스
 *
 * @example 기본 사용
 * $users = db()->select('*')->from('users')->get();
 *
 * @example INSERT
 * $userId = db()->insert(['name' => '홍길동'])->into('users');
 *
 * @example UPDATE
 * $affected = db()->update(['status' => 'active'])
 *     ->table('users')
 *     ->where('id = ?', [123])
 *     ->execute();
 *
 * @example DELETE
 * $deleted = db()->delete()->from('users')->where('id = ?', [123])->execute();
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
 * 데이터베이스 연결 가져오기 (싱글톤)
 *
 * @return PDO 데이터베이스 연결
 *
 * @example PDO 직접 사용
 * $pdo = db_connection();
 * $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
 * $stmt->execute([123]);
 * $user = $stmt->fetch();
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
 * 데이터베이스 연결 종료
 *
 * @example 연결 종료
 * db_close();
 */
function db_close(): void
{
    static $connection = null;
    if ($connection !== null) {
        $connection = null;
    }
}
