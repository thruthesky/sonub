<?php

declare(strict_types=1);

/**
 * 게시글 생성 함수 (Fan-out on Write 자동 적용)
 *
 * 새로운 게시글을 데이터베이스에 생성합니다.
 * MariaDB/MySQL의 PDO prepared statement를 사용하여 SQL 인젝션을 방지합니다.
 * Unix timestamp(초 단위)를 사용하여 시간 정보를 저장합니다.
 *
 * **중요: visibility가 'private'가 아니면 자동으로 친구들에게 피드를 전파합니다.**
 * - public: 모든 친구의 피드에 표시
 * - friends: 친구들의 피드에만 표시
 * - private: 피드 전파 안 함 (본인만 조회 가능)
 *
 * @param array $input 게시글 생성 시 전달되는 입력값
 *                     - category: 카테고리 (필수)
 *                     - title: 게시글 제목 (선택)
 *                     - content: 게시글 내용 (선택)
 *                     - files: 첨부 파일 URL (선택, 콤마로 구분된 여러 URL)
 *                     - visibility: 공개 범위 (선택, 기본값: 'public')
 *                       * 'public': 모두에게 공개 + 친구 피드 전파
 *                       * 'friends': 친구에게만 공개 + 친구 피드 전파
 *                       * 'private': 나만 보기 + 피드 전파 안 함
 *
 * @return PostModel|array 성공 시 생성된 PostModel 객체 반환, 실패 시 에러 배열 반환
 *
 * @example
 * // 필수: 로그인 상태 + category
 * $post = create_post([
 *     'category' => 'discussion',
 *     'title' => '게시글 제목',
 *     'content' => '게시글 내용'
 * ]);
 *
 * // title이나 content 없이도 생성 가능
 * $post = create_post([
 *     'category' => 'discussion'
 * ]);
 *
 * // 파일 첨부 예시 (콤마로 구분)
 * $post = create_post([
 *     'category' => 'discussion',
 *     'title' => '파일 첨부 게시글',
 *     'files' => 'https://abc.com/def/photo.jpg,/var/uploads/345/another-file.zip'
 * ]);
 *
 * // 공개 범위 지정 (친구에게만 공개 + 친구 피드에 전파)
 * $post = create_post([
 *     'category' => 'story',
 *     'title' => '친구들만 보세요',
 *     'content' => '친구들에게만 공개합니다',
 *     'visibility' => 'friends'
 * ]);
 *
 * // 비공개 글 (피드 전파 안 함)
 * $post = create_post([
 *     'category' => 'diary',
 *     'title' => '나만 보는 일기',
 *     'content' => '비밀 내용',
 *     'visibility' => 'private'
 * ]);
 */
function create_post(array $input)
{
    // ========================================================================
    // 1단계: 사용자 로그인 확인 (필수)
    // ========================================================================
    // login() 함수는 로그인된 사용자의 UserModel 객체를 반환하거나 false를 반환합니다.
    // 로그인하지 않은 사용자는 게시글을 생성할 수 없습니다.
    if (login() == false) {
        // 에러 배열 반환: error_code, error_message
        // API를 통해 호출되면 자동으로 JSON으로 변환됩니다.
        error('login-required', tr(['en' => 'Login is required.', 'ko' => '로그인이 필요합니다.', 'ja' => 'ログインが必要です。', 'zh' => '需要登录。']));
    }

    // 로그인된 사용자 정보 가져오기
    $user = login();
    $user_id = $user->id; // UserModel의 id 프로퍼티 사용

    // ========================================================================
    // 2단계: 입력값 정리 및 검증
    // ========================================================================
    // 입력값에서 title, content, category, files, visibility를 추출하고 공백 제거
    // trim()을 사용하여 앞뒤 공백을 제거합니다.
    // (string) 캐스팅으로 타입을 명확히 합니다.
    $title = isset($input['title']) ? trim((string)$input['title']) : '';
    $content = isset($input['content']) ? trim((string)$input['content']) : '';
    $files = isset($input['files']) ? trim((string)$input['files']) : '';
    $visibility = isset($input['visibility']) ? trim((string)$input['visibility']) : 'public';
    if ($visibility === 'public') {
        $category = isset($input['category']) ? trim((string)$input['category']) : '';
    } else {
        $category = $visibility;
    }

    // visibility 값 검증 (public, friends, private만 허용)
    if (!in_array($visibility, ['public', 'friends', 'private'], true)) {
        $visibility = 'public'; // 기본값
    }

    // 필수 입력값 검증: category만 필수
    // title, content, files, visibility는 선택사항 (빈 문자열 허용)
    if ($category === '') {
        // category가 비어있으면 에러 반환
        error('category-required', tr(['en' => 'Category is required.', 'ko' => 'category는 필수 항목입니다.', 'ja' => 'カテゴリは必須項目です。', 'zh' => '类别是必填项。']));
    }

    // ========================================================================
    // 2단계: Unix timestamp 생성
    // ========================================================================
    // time() 함수는 현재 시간을 Unix timestamp(초 단위)로 반환합니다.
    // 예: 1734000000 (2024년 12월 12일 기준)
    // Unix timestamp는 정수형(int)으로 저장되며, 시간대(timezone) 문제가 없습니다.
    // 장점:
    // - 시간대 변환이 쉬움 (UTC 기준)
    // - 정수 비교/정렬이 빠름
    // - 저장 공간 절약 (INT 타입, 4바이트)
    $now = time(); // int 타입 (예: 1734000000)

    try {
        // ====================================================================
        // 3단계: 데이터베이스 연결 (PDO)
        // ====================================================================
        // pdo() 함수는 PDO 인스턴스를 반환합니다.
        // PDO (PHP Data Objects)는 데이터베이스 접근을 위한 표준 인터페이스입니다.
        // 장점:
        // - Prepared statement 지원 (SQL 인젝션 방지)
        // - 여러 데이터베이스 지원 (MySQL, PostgreSQL, SQLite 등)
        // - 예외 처리 지원
        $db = pdo();

        // 권장 설정: PDO 예외 모드 활성화
        // 이렇게 하면 SQL 에러 시 예외(Exception)가 발생하여 try-catch로 잡을 수 있습니다.
        // $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ====================================================================
        // 4단계: Prepared Statement 준비
        // ====================================================================
        // Prepared Statement는 SQL 쿼리를 미리 준비(prepare)하고,
        // 나중에 실제 값을 바인딩(bind)하여 실행하는 방식입니다.
        //
        // 장점:
        // 1. SQL 인젝션 방지:
        //    - 사용자 입력값이 쿼리 구조를 변경할 수 없습니다.
        //    - 예: $input['title'] = "'; DROP TABLE posts; --"
        //    - Prepared statement는 이를 단순 문자열로 처리합니다.
        //
        // 2. 성능 향상:
        //    - 동일한 쿼리를 여러 번 실행할 때 파싱을 한 번만 합니다.
        //    - 데이터베이스가 쿼리 실행 계획을 캐시할 수 있습니다.
        //
        // 3. 가독성 향상:
        //    - 쿼리 구조와 데이터를 분리하여 코드가 명확해집니다.
        //
        // 플레이스홀더 (Placeholder):
        // - :title, :content, :category, :user_id 등은 "명명된 플레이스홀더(named placeholder)"입니다.
        // - 나중에 bindValue()로 실제 값을 연결합니다.
        // - 또 다른 방식: ? (위치 기반 플레이스홀더, positional placeholder)
        $sql = 'INSERT INTO posts (user_id, title, content, category, files, visibility, created_at, updated_at)
                VALUES (:user_id, :title, :content, :category, :files, :visibility, :created_at, :updated_at)';

        // prepare() 메서드:
        // - SQL 쿼리를 데이터베이스에 보내 파싱(구문 분석)합니다.
        // - PDOStatement 객체를 반환합니다.
        // - 이 단계에서는 아직 쿼리가 실행되지 않습니다.
        $stmt = $db->prepare($sql);

        // ====================================================================
        // 5단계: 값 바인딩 (Value Binding)
        // ====================================================================
        // bindValue() 메서드:
        // - 플레이스홀더(:title)와 실제 값($title)을 연결합니다.
        // - 세 번째 인자는 데이터 타입을 명시합니다 (선택사항이지만 권장).
        //
        // 주요 PDO 데이터 타입:
        // - PDO::PARAM_STR: 문자열 (기본값, 가장 많이 사용)
        // - PDO::PARAM_INT: 정수
        // - PDO::PARAM_BOOL: 불리언
        // - PDO::PARAM_NULL: NULL
        //
        // 타입을 명시하는 이유:
        // 1. 타입 안정성: 의도한 타입으로만 데이터가 전달됩니다.
        // 2. 성능 최적화: 데이터베이스가 타입을 미리 알 수 있습니다.
        // 3. 명시적 의도: 코드를 읽는 사람이 의도를 쉽게 파악할 수 있습니다.
        //
        // bindValue() vs bindParam() 차이:
        // - bindValue(): 값 자체를 바인딩 (값 복사, 권장)
        // - bindParam(): 변수 참조를 바인딩 (변수가 변경되면 영향을 받음)
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);

        // files: 콤마로 구분된 파일 URL 문자열
        // 예: 'https://abc.com/def/photo.jpg,/var/uploads/345/another-file.zip'
        // - HTTP/HTTPS로 시작하는 외부 URL
        // - /var/uploads/[n]/... 형식의 로컬 파일 경로
        $stmt->bindValue(':files', $files, PDO::PARAM_STR);

        // visibility: 공개 범위 ('public', 'friends', 'private')
        $stmt->bindValue(':visibility', $visibility, PDO::PARAM_STR);

        // created_at과 updated_at는 Unix timestamp (정수)
        // PDO::PARAM_INT로 타입을 명시하여 정수임을 명확히 합니다.
        $stmt->bindValue(':created_at', $now, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', $now, PDO::PARAM_INT);

        // ====================================================================
        // 6단계: 쿼리 실행 및 결과 반환
        // ====================================================================
        // execute() 메서드:
        // - 준비된 쿼리를 실행합니다.
        // - 성공 시 true, 실패 시 false를 반환합니다.
        // - PDO 예외 모드가 활성화되어 있으면 실패 시 예외를 던집니다.
        if ($stmt->execute()) {
            // lastInsertId() 메서드:
            // - INSERT 쿼리 실행 후 생성된 AUTO_INCREMENT ID를 반환합니다.
            // - MariaDB/MySQL에서만 작동합니다 (데이터베이스마다 다름).
            // - 반환값은 문자열이므로 (int)로 캐스팅합니다.
            $id = (int)$db->lastInsertId();

            // ID가 유효한 경우 (1 이상), 생성된 게시글을 조회하여 반환
            if ($id > 0) {
                // ============================================================
                // Fan-out on Write: 친구들에게 피드 전파
                // ============================================================
                // visibility가 'private'가 아니면 친구들에게 피드를 전파합니다.
                // 이렇게 하면 친구들의 피드에 게시글이 자동으로 표시됩니다.
                if ($visibility !== 'private') {
                    // fanout_post_to_friends() 함수를 사용하여 피드 캐시 생성
                    // lib/friend-and-feed/friend-and-feed.functions.php에 정의되어 있음
                    fanout_post_to_friends($user_id, $id, $now);
                }

                // get_post_by_id() 함수로 방금 생성된 게시글을 조회합니다.
                // 이렇게 하면 데이터베이스에 저장된 실제 값(기본값 등)을 포함한
                // 완전한 PostModel 객체를 얻을 수 있습니다.
                return get_post_by_id($id);
            }
        }

        // 실행 실패 또는 ID가 0인 경우 에러 반환
        error('post-creation-failed', tr(['en' => 'Failed to create post.', 'ko' => '게시글 생성에 실패했습니다.', 'ja' => '投稿の作成に失敗しました。', 'zh' => '创建帖子失败。']));
    } catch (Throwable $e) {
        // ====================================================================
        // 7단계: 예외 처리
        // ====================================================================
        // Throwable:
        // - PHP 7.0+에서 모든 에러와 예외를 잡을 수 있는 최상위 인터페이스
        // - Exception과 Error를 모두 포함합니다.
        //
        // 보안 고려사항:
        // - 운영 환경에서는 내부 에러 메시지를 사용자에게 노출하지 않습니다.
        // - error_log()로 서버 로그에만 기록합니다.
        // - 사용자에게는 일반적인 에러 메시지만 보여줍니다.
        error_log('create_post error: ' . $e->getMessage());

        // 예외 발생 시 에러 배열 반환 (데이터베이스 오류)
        error('database-error', tr(['en' => 'A database error occurred.', 'ko' => '데이터베이스 오류가 발생했습니다.', 'ja' => 'データベースエラーが発生しました。', 'zh' => '发生数据库错误。']));
    }
}

/**
 * ID로 게시글 조회
 *
 * 게시글 ID를 받아 해당 게시글을 데이터베이스에서 조회합니다.
 * Prepared statement를 사용하여 SQL 인젝션을 방지합니다.
 *
 * @param int $id 조회할 게시글 ID
 * @return PostModel|null 게시글이 존재하면 PostModel 객체 반환, 없으면 null 반환
 *
 * @example
 * $post = get_post_by_id(123);
 * if ($post) {
 *     echo $post->title;
 * }
 */
function get_post_by_id(int $id): ?PostModel
{
    // ========================================================================
    // 1단계: 데이터베이스 연결
    // ========================================================================
    // pdo()로 PDO 인스턴스를 가져옵니다.
    $db = pdo();

    // ========================================================================
    // 2단계: Prepared Statement 준비
    // ========================================================================
    // SELECT 쿼리도 Prepared Statement를 사용합니다.
    // 이유:
    // 1. SQL 인젝션 방지 (입력값이 쿼리 구조를 변경할 수 없음)
    // 2. 타입 안정성 (PDO::PARAM_INT로 정수임을 보장)
    // 3. 코드 가독성 향상
    $stmt = $db->prepare('SELECT * FROM posts WHERE id = :id');

    // ========================================================================
    // 3단계: 값 바인딩
    // ========================================================================
    // :id 플레이스홀더에 실제 ID 값을 바인딩합니다.
    // PDO::PARAM_INT로 정수 타입임을 명시합니다.
    //
    // 보안 효과:
    // - 만약 $id가 "1 OR 1=1" 같은 문자열이어도 정수로 변환되어
    //   SQL 인젝션 공격이 불가능합니다.
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    // ========================================================================
    // 4단계: 쿼리 실행 및 결과 처리
    // ========================================================================
    if ($stmt->execute()) {
        // fetch() 메서드:
        // - 쿼리 결과에서 한 행을 가져옵니다.
        // - PDO::FETCH_ASSOC: 연관 배열로 반환 (컬럼명이 키)
        //   예: ['id' => 1, 'title' => '제목', 'content' => '내용', ...]
        //
        // 다른 fetch 모드:
        // - PDO::FETCH_NUM: 숫자 인덱스 배열
        // - PDO::FETCH_OBJ: 객체로 반환
        // - PDO::FETCH_CLASS: 특정 클래스의 인스턴스로 반환
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // 데이터가 존재하면 PostModel 객체 생성 후 반환
        if ($data) {
            // PostModel 생성자에 데이터베이스 행 데이터를 전달합니다.
            // PostModel은 배열 데이터를 받아 프로퍼티로 변환합니다.
            return new PostModel($data);
        }
    }

    // 실행 실패 또는 데이터가 없으면 null 반환
    return null;
}


/**
 * 게시글 목록 조회
 *
 * 다양한 필터 조건에 따라 게시글 목록을 조회합니다.
 * Prepared statement를 사용하여 SQL 인젝션을 방지합니다.
 *
 * @param array $filters 필터 조건 (선택 사항)
 *                       - category: 카테고리 (문자열)
 *                       - user_id: 작성자 ID (정수)
 *                       - created_after: 생성일 이후 (Unix timestamp, 정수)
 *                       - created_before: 생성일 이전 (Unix timestamp, 정수)
 *                       - limit: 최대 결과 수 (정수)
 *
 * @return PostListModel 게시글 목록 배열 (조건에 맞는 게시글이 없으면 빈 배열)
 *
 * @example
 * // 모든 게시글 조회
 * $posts = list_posts();
 *
 * // 특정 카테고리의 게시글 조회
 * $posts = list_posts(['category' => 'discussion']);
 *
 * // 특정 사용자가 작성한 게시글 조회
 * $posts = list_posts(['user_id' => 42]);
 *
 * // 최근 7일 이내에 생성된 게시글 조회
 * $one_week_ago = time() - 7 * 24 * 60 * 60;
 * $posts = list_posts(['created_after' => $one_week_ago]);
 *
 * // 최대 10개의 게시글만 조회
 * $posts = list_posts(['limit' => 10]);
 */
function list_posts(array $filters = []): PostListModel
{
    // ========================================================================
    // 1단계: 데이터베이스 연결
    // ========================================================================
    // pdo() 함수로 PDO 인스턴스를 가져옵니다.
    $pdo = pdo();

    // ========================================================================
    // 2단계: page를 offset으로 변환 (자동 계산)
    // ========================================================================
    // page 파라미터가 있고 offset이 없으면 자동으로 offset 계산
    // 예: page=2, limit=10 → offset=10 (2-1)*10
    if (isset($filters['page']) && !isset($filters['offset'])) {
        $page = (int)$filters['page'];
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 20;

        // page는 1부터 시작하므로 (page - 1) * limit
        if ($page > 1) {
            $filters['offset'] = ($page - 1) * $limit;
        }
    }

    // ========================================================================
    // 3단계: 동적 WHERE 조건 빌드
    // ========================================================================
    // 필터 조건에 따라 WHERE 절과 파라미터 배열을 동적으로 생성합니다.
    $conditions = [];
    $params = [];

    // 카테고리 필터
    if (isset($filters['category'])) {
        $conditions[] = 'category = ?';
        $params[] = $filters['category'];
    }

    // 사용자 ID 필터
    if (isset($filters['user_id'])) {
        $conditions[] = 'user_id = ?';
        $params[] = $filters['user_id'];
    }

    // 생성일 이후 필터 (Unix timestamp)
    if (isset($filters['created_after'])) {
        $conditions[] = 'created_at >= ?';
        $params[] = $filters['created_after'];
    }

    // 생성일 이전 필터 (Unix timestamp)
    if (isset($filters['created_before'])) {
        $conditions[] = 'created_at <= ?';
        $params[] = $filters['created_before'];
    }

    // ========================================================================
    // 4단계: SQL 쿼리 빌드
    // ========================================================================
    // 기본 SELECT 쿼리
    $sql = 'SELECT * FROM posts';

    // WHERE 절 추가 (조건이 있는 경우)
    if (count($conditions) > 0) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    // ORDER BY 절 추가 (최신순 정렬)
    $sql .= ' ORDER BY created_at DESC';

    // LIMIT 절 추가 (제한이 있는 경우)
    if (isset($filters['limit']) && is_int($filters['limit']) && $filters['limit'] > 0) {
        // OFFSET이 있는 경우 LIMIT와 함께 사용
        if (isset($filters['offset']) && is_int($filters['offset']) && $filters['offset'] >= 0) {
            $sql .= ' LIMIT ? OFFSET ?';
            $params[] = $filters['limit'];
            $params[] = $filters['offset'];
        } else {
            $sql .= ' LIMIT ?';
            $params[] = $filters['limit'];
        }
    }

    // ========================================================================
    // 5단계: Prepared Statement 준비 및 실행
    // ========================================================================
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // ====================================================================
        // 6단계: 결과 가져오기 및 PostModel 객체로 변환
        // ====================================================================
        // fetchAll() 메서드:
        // - 쿼리 결과에서 모든 행을 가져옵니다.
        // - PDO::FETCH_ASSOC: 연관 배열로 반환 (컬럼명이 키)
        //   예: ['id' => 1, 'title' => '제목', 'content' => '내용', ...]
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // PostModel 객체 배열로 변환
        $posts = $data ? array_map(fn($item) => new PostModel($item), $data) : [];

        // ====================================================================
        // 7단계: 전체 게시글 개수 조회 (페이지네이션용)
        // ====================================================================
        // count_posts() 함수로 필터 조건에 맞는 전체 게시글 개수를 조회합니다.
        // limit과 offset은 전체 개수 조회에서 제외해야 하므로 필터에서 제거합니다.
        $countFilters = $filters;
        unset($countFilters['limit']);
        unset($countFilters['offset']);
        $total = count_posts($countFilters);

        // ====================================================================
        // 8단계: PostListModel 객체 생성 및 반환
        // ====================================================================
        // 페이지네이션 정보 추출
        $page = isset($filters['page']) ? (int)$filters['page'] : 1;
        $per_page = isset($filters['limit']) ? (int)$filters['limit'] : 20;

        // PostListModel 객체 생성 및 반환
        return new PostListModel($posts, $total, $page, $per_page);
    } catch (PDOException $e) {
        // ====================================================================
        // 9단계: 에러 처리
        // ====================================================================
        // 데이터베이스 에러 발생 시 에러 로그 기록 후 빈 PostListModel 반환
        error_log('게시글 목록 조회 실패: ' . $e->getMessage());
        return new PostListModel([], 0, 1, 20);
    }
}

function search_posts(array $filters): PostListModel
{
    return list_posts($filters);
}

/**
 * 게시글 총 개수 조회 함수
 *
 * 필터 조건에 맞는 게시글의 총 개수를 반환합니다.
 * 페이지네이션의 전체 페이지 수 계산에 사용됩니다.
 *
 * @param array $filters 필터 조건 배열
 *                       - category: 카테고리 필터 (선택)
 *                       - user_id: 사용자 ID 필터 (선택)
 *                       - created_after: 생성일 이후 필터 (Unix timestamp, 선택)
 *                       - created_before: 생성일 이전 필터 (Unix timestamp, 선택)
 *
 * @return int 게시글 총 개수
 *
 * @example
 * // 특정 카테고리의 게시글 개수
 * $count = count_posts(['category' => 'discussion']);
 *
 * // 특정 사용자의 게시글 개수
 * $count = count_posts(['user_id' => 123]);
 */
function count_posts(?array $filters = []): int
{
    // ========================================================================
    // 1단계: 데이터베이스 연결
    // ========================================================================
    $pdo = pdo();

    // ========================================================================
    // 2단계: 동적 WHERE 조건 빌드
    // ========================================================================
    $conditions = [];
    $params = [];

    // 카테고리 필터
    if (isset($filters['category'])) {
        $conditions[] = 'category = ?';
        $params[] = $filters['category'];
    }

    // 사용자 ID 필터
    if (isset($filters['user_id'])) {
        $conditions[] = 'user_id = ?';
        $params[] = $filters['user_id'];
    }

    // 생성일 이후 필터 (Unix timestamp)
    if (isset($filters['created_after'])) {
        $conditions[] = 'created_at >= ?';
        $params[] = $filters['created_after'];
    }

    // 생성일 이전 필터 (Unix timestamp)
    if (isset($filters['created_before'])) {
        $conditions[] = 'created_at <= ?';
        $params[] = $filters['created_before'];
    }

    // ========================================================================
    // 3단계: SQL 쿼리 빌드
    // ========================================================================
    $sql = 'SELECT COUNT(*) FROM posts';

    // WHERE 절 추가 (조건이 있는 경우)
    if (count($conditions) > 0) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    // ========================================================================
    // 4단계: Prepared Statement 준비 및 실행
    // ========================================================================
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // fetchColumn()으로 COUNT(*) 결과 가져오기
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        // 데이터베이스 에러 발생 시 에러 로그 기록 후 0 반환
        error_log('게시글 개수 조회 실패: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Delete a post
 *
 * Deletes a post and all associated data (feed entries, attached files).
 * Only the post owner can delete their own posts.
 *
 * @param array $params Post deletion parameters
 *                      - id|post_id: Post ID to delete (required)
 *
 * @return array Success message
 *
 * @throws ApiException If user is not logged in
 * @throws ApiException If post_id is invalid or missing
 * @throws ApiException If post is not found or user lacks permission
 * @throws ApiException If deletion fails
 *
 * @example
 * // Delete a post
 * $result = delete_post(['id' => 123]);
 * echo $result['message']; // "Post deleted successfully."
 *
 * // Also accepts 'post_id' parameter
 * $result = delete_post(['post_id' => 456]);
 */
function delete_post(array $params)
{
    if (login() == false) {
        error('login-required', tr(['en' => 'Login is required.', 'ko' => '로그인이 필요합니다.', 'ja' => 'ログインが必要です。', 'zh' => '需要登录。']));
    }

    // Extract post_id from params
    $post_id = $params['id'] ?? $params['post_id'] ?? null;

    if (empty($post_id) || !is_numeric($post_id)) {
        error('invalid-id', tr([
            'en' => 'Invalid post ID.',
            'ko' => '잘못된 게시글 ID입니다.',
            'ja' => '無効な投稿IDです。',
            'zh' => '无效的帖子ID。'
        ]));
        return;
    }

    $user = login();
    $user_id = $user->id;


    $post = get_post_by_id($post_id);

    if (!$post) {
        error('post-not-found', tr([
            'en' => 'Post not found or you do not have permission to delete it.',
            'ko' => '게시글을 찾을 수 없거나 삭제 권한이 없습니다.',
            'ja' => '投稿が見つからないか、削除する権限がありません。',
            'zh' => '找不到帖子或您没有删除权限。'
        ]));
    }

    $pdo = pdo();

    try {
        // Delete from in feed entries
        $sql = 'DELETE from feed_entries WHERE post_id = :post_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        // Delete from posts
        $sql = 'DELETE FROM posts WHERE id = :id AND user_id = :user_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            error('delete-failed', tr([
                'en' => 'Failed to delete post.',
                'ko' => '게시글 삭제에 실패했습니다.',
                'ja' => '投稿の削除に失敗しました。',
                'zh' => '删除帖子失败。'
            ]));
        }

        // Delete attached files
        // $post->files is an array of file paths (e.g., ["/var/uploads/123/file.jpg", ...])
        if (!empty($post->files) && is_array($post->files)) {
            foreach ($post->files as $file_url) {
                // Skip empty file paths
                if (empty($file_url)) {
                    continue;
                }

                // Delete the file
                try {
                    file_delete(['url' => $file_url]);
                } catch (Throwable $e) {
                    // Log the error but continue deleting the post
                    // (file might already be deleted or path might be invalid)
                    error_log("Failed to delete file: {$file_url}, Error: " . $e->getMessage());
                }
            }
        }

        return [
            'message' => tr([
                'en' => 'Post deleted successfully.',
                'ko' => '게시글이 삭제되었습니다.',
                'ja' => '投稿が削除されました。',
                'zh' => '帖子已删除。'
            ]),
        ];
    } catch (PDOException $e) {
        error_log('게시글 개수 조회 실패: ' . $e->getMessage());
        return 0;
    }
}
