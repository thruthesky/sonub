<?php
// friend-and-feed.functions.php
declare(strict_types=1);

/**
 * 헬퍼 함수: 두 사용자 ID를 정렬하여 [작은 ID, 큰 ID] 배열 반환
 *
 * 무방향 친구 관계에서 (user_id_a, user_id_b)를 항상 작은 ID를 a, 큰 ID를 b로 저장하기 위한 헬퍼 함수
 *
 * @param int $a 첫 번째 사용자 ID
 * @param int $b 두 번째 사용자 ID
 * @return array [작은 ID, 큰 ID]
 *
 * @example
 * [$user_a, $user_b] = friend_pair(10, 5); // [5, 10]
 */
function friend_pair(int $a, int $b): array
{
    return [min($a, $b), max($a, $b)];
}

/**
 * 헬퍼 함수: 현재 시간을 UNIX epoch (초 단위) 정수로 반환
 *
 * @return int 현재 시간의 UNIX timestamp
 *
 * @example
 * $now = now_epoch(); // 1734000000
 */
function now_epoch(): int
{
    return time(); // UNIX epoch (int)
}

// ============================================================================
// FriendshipRepository 함수들 (친구 관계 관리)
// ============================================================================

/**
 * 친구 요청 생성 함수
 *
 * 무방향 친구 관계 모델을 사용하여 친구 요청을 생성합니다.
 * 이미 요청이 존재하면 updated_at만 갱신됩니다 (ON DUPLICATE KEY UPDATE).
 *
 * @param int $me 요청을 보내는 사용자 ID
 * @param int $other 요청을 받는 사용자 ID
 * @return void
 *
 * @example
 * request_friend(5, 10); // 사용자 5가 사용자 10에게 친구 요청
 */
function request_friend(int $me, int $other): void
{
    $pdo = pdo();
    [$a, $b] = friend_pair($me, $other);
    $sql = "INSERT INTO friendships
              (user_id_a, user_id_b, status, requested_by, created_at, updated_at)
            VALUES
              (:a, :b, 'pending', :req, :now, :now)
            ON DUPLICATE KEY UPDATE
              updated_at = VALUES(updated_at)";
    $stmt = $pdo->prepare($sql);
    $now = now_epoch();
    $stmt->execute([':a' => $a, ':b' => $b, ':req' => $me, ':now' => $now]);
}

/**
 * 친구 요청 수락 함수
 *
 * pending 상태의 친구 요청을 accepted로 변경합니다.
 *
 * @param int $me 요청을 수락하는 사용자 ID
 * @param int $other 요청을 보낸 사용자 ID
 * @return bool 수락 성공 여부 (pending 상태가 아니면 false)
 *
 * @example
 * $success = accept_friend(10, 5); // 사용자 10이 사용자 5의 친구 요청 수락
 */
function accept_friend(int $me, int $other): bool
{
    $pdo = pdo();
    [$a, $b] = friend_pair($me, $other);
    $sql = "UPDATE friendships
               SET status='accepted', updated_at=:now
             WHERE user_id_a=:a AND user_id_b=:b AND status='pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':a' => $a, ':b' => $b, ':now' => now_epoch()]);
    return $stmt->rowCount() > 0;
}

/**
 * 친구 관계 삭제 함수
 *
 * accepted 상태의 친구 관계를 데이터베이스에서 삭제합니다.
 *
 * @param int $me 친구 관계를 삭제하는 사용자 ID
 * @param int $other 삭제할 친구의 사용자 ID
 * @return bool 삭제 성공 여부 (친구 관계가 없으면 false)
 *
 * @example
 * $removed = remove_friend(5, 10); // 사용자 5와 10의 친구 관계 삭제
 */
function remove_friend(int $me, int $other): bool
{
    $pdo = pdo();
    [$a, $b] = friend_pair($me, $other);
    $sql = "DELETE FROM friendships
            WHERE user_id_a=:a AND user_id_b=:b AND status='accepted'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':a' => $a, ':b' => $b]);
    return $stmt->rowCount() > 0;
}

/**
 * 사용자의 친구 ID 목록 조회 함수
 *
 * accepted 상태의 친구 관계만 조회하여 친구 ID 배열을 반환합니다.
 * 무방향 1행 모델에서 CASE 문을 사용하여 친구 ID를 추출합니다.
 *
 * @param int $me 친구 목록을 조회할 사용자 ID
 * @return int[] 친구 ID 목록 (친구가 없으면 빈 배열)
 *
 * @example
 * $friends = get_friend_ids(5); // [1, 3, 10, 15]
 */
function get_friend_ids(int $me): array
{
    $pdo = pdo();
    $sql = "SELECT CASE WHEN user_id_a = :me THEN user_id_b ELSE user_id_a END AS friend_id
              FROM friendships
             WHERE (user_id_a = :me OR user_id_b = :me) AND status='accepted'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':me' => $me]);
    return array_map(fn($r) => (int)$r['friend_id'], $stmt->fetchAll());
}

// ============================================================================
// BlockRepository 함수들 (차단 관리)
// ============================================================================

/**
 * 양방향 차단 확인 함수
 *
 * 두 사용자 중 어느 한쪽이라도 상대방을 차단했는지 확인합니다.
 * (A가 B를 차단했거나, B가 A를 차단한 경우 모두 true 반환)
 *
 * @param int $a 첫 번째 사용자 ID
 * @param int $b 두 번째 사용자 ID
 * @return bool 차단 관계가 존재하면 true
 *
 * @example
 * $blocked = is_blocked_either_way(5, 10); // 5가 10을 차단했거나 10이 5를 차단한 경우 true
 */
function is_blocked_either_way(int $a, int $b): bool
{
    $pdo = pdo();
    $sql = "SELECT 1 FROM blocks
             WHERE (blocker_id=:a AND blocked_id=:b)
                OR (blocker_id=:b AND blocked_id=:a)
             LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':a' => $a, ':b' => $b]);
    return (bool)$stmt->fetchColumn();
}

// ============================================================================
// PostRepository 함수들 (게시글 관리)
// ============================================================================
// 주의: create_post() 함수는 lib/post/post.crud.php에 이미 정의되어 있습니다.
// 여기서는 추가 함수들만 정의합니다.

/**
 * 게시글 단일 행 조회 함수 (내부용)
 *
 * 게시글 ID로 posts 테이블의 단일 행을 연관 배열로 반환합니다.
 * PostModel 객체가 아닌 raw array를 반환합니다.
 *
 * @param int $post_id 조회할 게시글 ID
 * @return array|null 게시글 데이터 배열 또는 null
 *
 * @example
 * $row = get_post_row(123);
 * if ($row) {
 *     echo $row['title'];
 * }
 */
function get_post_row(int $post_id): ?array
{
    $pdo = pdo();
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=:id");
    $stmt->execute([':id' => $post_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

// ============================================================================
// FeedRepository 함수들 (피드 캐시 관리)
// ============================================================================

/**
 * Fan-out on Write: 작성자의 친구들에게 피드 캐시 일괄 삽입 함수
 *
 * 게시글 작성 시 작성자의 모든 친구에게 feed_entries를 미리 생성합니다.
 * INSERT IGNORE를 사용하여 중복 삽입을 방지합니다.
 *
 * @param int $author_id 게시글 작성자 ID
 * @param int $post_id 게시글 ID
 * @param int $created_at 게시글 생성 시간 (UNIX timestamp)
 * @return int 실제 삽입된 행 수 (IGNORE로 인해 중복 제외)
 *
 * @example
 * $count = fanout_post_to_friends(5, 100, time());
 * // 사용자 5의 친구들에게 게시글 100을 피드에 전파
 */
function fanout_post_to_friends(int $author_id, int $post_id, int $created_at): int
{
    $pdo = pdo();
    $sql = "INSERT IGNORE INTO feed_entries (receiver_id, post_id, post_author_id, created_at)
            SELECT
              CASE WHEN user_id_a = :author THEN user_id_b ELSE user_id_a END AS receiver_id,
              :post_id,
              :author,
              :created_at
              FROM friendships
             WHERE (user_id_a=:author OR user_id_b=:author)
               AND status='accepted'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':author' => $author_id,
        ':post_id' => $post_id,
        ':created_at' => $created_at
    ]);
    return $stmt->rowCount(); // 실제 삽입된 행 수(IGNORE 영향 있음)
}

/**
 * 피드 캐시에서 사용자의 피드 조회 함수
 *
 * feed_entries 테이블에서 미리 생성된 피드 캐시를 조회합니다.
 * 친구 목록 조인 없이 단일 테이블 스캔으로 고속 조회가 가능합니다.
 *
 * @param int $me 피드를 조회할 사용자 ID
 * @param int $limit 조회할 최대 개수
 * @param int $offset 시작 위치 (페이지네이션용)
 * @return array 피드 항목 배열 (post_id, post_author_id, created_at)
 *
 * @example
 * $feed = get_feed_from_cache(10, 20, 0); // 사용자 10의 피드 20개 조회
 */
function get_feed_from_cache(int $me, int $limit, int $offset = 0): array
{
    $pdo = pdo();
    $sql = "SELECT post_id, post_author_id, created_at
              FROM feed_entries
             WHERE receiver_id = :me
             ORDER BY created_at DESC
             LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':me', $me, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fan-out on Read: 친구 목록 조인으로 피드 조회 함수 (캐시 부족분 보충용)
 *
 * feed_entries 캐시가 충분하지 않을 때 posts 테이블에서 직접 조회합니다.
 * 친구 ID 배열을 받아 IN 절로 필터링하며, 선택적으로 차단 사용자를 제외합니다.
 *
 * @param int $me 피드를 조회할 사용자 ID
 * @param array $friend_ids 친구 ID 배열
 * @param int $limit 조회할 최대 개수
 * @param int $offset 시작 위치
 * @param bool $exclude_blocked 차단 사용자 제외 여부 (기본값: true)
 * @return array 피드 항목 배열 (post_id, post_author_id, created_at)
 *
 * @example
 * $friend_ids = get_friend_ids(10);
 * $feed = get_feed_from_read_join(10, $friend_ids, 20, 0);
 */
function get_feed_from_read_join(
    int $me,
    array $friend_ids,
    int $limit,
    int $offset = 0,
    bool $exclude_blocked = true
): array {
    if (empty($friend_ids)) return [];

    $pdo = pdo();

    // IN 바인딩
    $in = implode(',', array_fill(0, count($friend_ids), '?'));

    $block_clause = $exclude_blocked ? "
        AND NOT EXISTS (
          SELECT 1 FROM blocks b
           WHERE (b.blocker_id = ? AND b.blocked_id = p.user_id)
              OR (b.blocker_id = p.user_id AND b.blocked_id = ?)
        )
    " : "";

    $sql = "SELECT p.id AS post_id, p.user_id AS post_author_id, p.created_at
              FROM posts p
             WHERE p.user_id IN ($in)
               AND p.visibility IN ('public','friends')
             $block_clause
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?";

    $stmt = $pdo->prepare($sql);
    $bind = [...$friend_ids];
    if ($exclude_blocked) {
        $bind[] = $me;
        $bind[] = $me;
    }
    $bind[] = $limit;
    $bind[] = $offset;

    $stmt->execute($bind);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================================
// FeedService 함수들 (피드 비즈니스 로직)
// ============================================================================

/**
 * 게시글 작성 + Fan-out 함수
 *
 * 게시글을 생성하고, visibility가 'private'가 아니면 친구들에게 피드를 전파합니다.
 * 트랜잭션을 사용하여 원자성을 보장합니다.
 *
 * 주의: 이 함수는 lib/post/post.crud.php의 create_post() 함수를 확장한 버전입니다.
 * 기존 create_post()는 피드 전파를 하지 않으므로, 피드 기능이 필요한 경우 이 함수를 사용하세요.
 *
 * @param int $author_id 작성자 ID
 * @param string $category 카테고리
 * @param string $title 게시글 제목
 * @param string $content 게시글 내용
 * @param string $visibility 공개 범위 ('public', 'friends', 'private')
 * @param string $files 첨부 파일 URL (콤마로 구분)
 * @return int 생성된 게시글 ID
 * @throws Throwable 트랜잭션 실패 시 예외
 *
 * @example
 * $post_id = create_post_and_fanout(5, 'story', '제목', '내용', 'friends', '');
 */
function create_post_and_fanout(
    int $author_id,
    string $category,
    string $title,
    string $content,
    string $visibility = 'public',
    string $files = ''
): int {
    $pdo = pdo();
    $pdo->beginTransaction();
    try {
        // lib/post/post.crud.php의 create_post() 로직을 인라인으로 실행
        $now = now_epoch();
        $sql = "INSERT INTO posts (user_id, category, title, content, files, visibility, created_at, updated_at)
                VALUES (:uid, :category, :title, :content, :files, :visibility, :now, :now)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $author_id,
            ':category' => $category,
            ':title' => $title,
            ':content' => $content,
            ':files' => $files,
            ':visibility' => $visibility,
            ':now' => $now
        ]);
        $post_id = (int)$pdo->lastInsertId();

        // visibility가 'private'가 아니면 피드 전파
        if ($visibility !== 'private') {
            $post = get_post_row($post_id);
            $created_at = (int)($post['created_at'] ?? $now);
            fanout_post_to_friends($author_id, $post_id, $created_at);
        }

        $pdo->commit();
        return $post_id;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * 하이브리드 피드 조회 함수
 *
 * 캐시(feed_entries)와 읽기 조인(posts)을 결합하여 사용자의 피드를 조회합니다.
 * 1. 먼저 캐시에서 조회
 * 2. 캐시가 부족하면 posts 테이블에서 직접 조회
 * 3. 두 결과를 병합하고 중복 제거
 * 4. visibility 및 차단 관계를 최종 검증
 *
 * @param int $me 피드를 조회할 사용자 ID
 * @param int $limit 조회할 최대 개수 (기본값: 20)
 * @param int $offset 시작 위치 (기본값: 0)
 * @return array 피드 항목 배열 (post_id, author_id, category, title, content, created_at, visibility)
 *
 * @example
 * $feed = get_hybrid_feed(10, 20, 0); // 사용자 10의 피드 20개 조회
 */
function get_hybrid_feed(int $me, int $limit = 20, int $offset = 0): array
{
    // 1단계: 캐시에서 조회
    $cached = get_feed_from_cache($me, $limit, $offset);
    if (count($cached) >= $limit) {
        return finalize_feed_with_visibility($me, $cached);
    }

    // 2단계: 부족분은 읽기 조인으로 보충
    $friend_ids = get_friend_ids($me);
    $need = $limit - count($cached);
    $joined = get_feed_from_read_join($me, $friend_ids, $need, $offset);

    // 3단계: 병합 + post_id 기준 중복 제거
    $merged = [...$cached, ...$joined];
    $seen = [];
    $unique = [];
    foreach ($merged as $r) {
        $pid = (int)$r['post_id'];
        if (isset($seen[$pid])) continue;
        $seen[$pid] = true;
        $unique[] = $r;
    }

    // 4단계: 최신순(epoch 내림차순) 정렬
    usort($unique, fn($a, $b) => (int)$b['created_at'] <=> (int)$a['created_at']);

    // 5단계: visibility/차단 최종 검증 + 본문 붙이기
    return finalize_feed_with_visibility($me, array_slice($unique, 0, $limit));
}

/**
 * 피드 최종 검증 함수 (내부 헬퍼)
 *
 * visibility 및 차단 관계를 최종 검증하고, 게시글 본문을 붙입니다.
 * - private: 작성자 본인만 조회 가능
 * - friends: 친구에게만 공개
 * - public: 모두에게 공개
 * - 차단된 사용자의 게시글은 제외
 *
 * @param int $me 피드를 조회하는 사용자 ID
 * @param array $items 피드 항목 배열 (post_id, post_author_id, created_at)
 * @return array 최종 검증된 피드 항목 배열
 */
function finalize_feed_with_visibility(int $me, array $items): array
{
    $out = [];
    // 친구 목록 캐시(루프 내 DB 호출 줄이기)
    $friend_ids = get_friend_ids($me);

    foreach ($items as $r) {
        $post = get_post_row((int)$r['post_id']);
        if (!$post) continue;

        $author = (int)$post['user_id'];
        $vis = $post['visibility'];

        // 차단 확인 (양방향)
        if (is_blocked_either_way($me, $author)) {
            continue;
        }

        // visibility 검증
        if ($vis === 'private' && $author !== $me) continue;
        if ($vis === 'friends' && $author !== $me && !in_array($author, $friend_ids, true)) continue;

        $out[] = [
            'post_id'    => (int)$post['id'],
            'author_id'  => $author,
            'category'   => $post['category'],
            'title'      => $post['title'],
            'content'    => $post['content'],
            'created_at' => (int)$post['created_at'],
            'visibility' => $vis,
        ];
    }

    // 안전하게 최신순 보장
    usort($out, fn($a, $b) => $b['created_at'] <=> $a['created_at']);
    return $out;
}
