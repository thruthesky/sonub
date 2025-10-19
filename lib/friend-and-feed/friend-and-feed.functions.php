<?php
// friend-and-feed.functions.php
declare(strict_types=1);

/**
 * 헬퍼 함수: 두 사용자 ID를 정렬하여 [작은 ID, 큰 ID] 배열 반환
 *
 * 무방향 친구 관계에서 (user_id_a, user_id_b)를 항상 작은 ID를 a, 큰 ID를 b로 저장하기 위한 헬퍼 함수
 * (내부 전용 - API로 호출되지 않음)
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
 * (내부 전용 - API로 호출되지 않음)
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
// FriendshipRepository 함수들 (친구 관계 관리) - API 호출 가능
// ============================================================================

/**
 * 친구 요청 생성 함수 (API 호출 가능)
 *
 * 무방향 친구 관계 모델을 사용하여 친구 요청을 생성합니다.
 * 이미 요청이 존재하면 updated_at만 갱신됩니다 (ON DUPLICATE KEY UPDATE).
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 요청을 보내는 사용자 ID
 *   - int $input['other'] 요청을 받는 사용자 ID
 * @return array 성공 메시지 및 성공 여부
 *   - string message: 성공 메시지
 *   - bool success: 항상 true
 * @throws ApiException 에러 발생 시
 *
 * @example
 * // PHP에서 호출
 * $result = request_friend(['me' => 5, 'other' => 10]);
 * // ['message' => '친구 요청을 보냈습니다', 'success' => true]
 *
 * // JavaScript에서 API 호출
 * const result = await func('request_friend', { me: 5, other: 10, auth: true });
 * console.log(result.message);  // '친구 요청을 보냈습니다'
 */
function request_friend(array $input): array
{
    // 파라미터 추출 및 검증
    $me = (int)($input['me'] ?? 0);
    $other = (int)($input['other'] ?? 0);

    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    if ($other <= 0) {
        error('invalid-other', '유효하지 않은 대상 사용자 ID입니다');
    }

    if ($me === $other) {
        error('same-user', '자기 자신에게 친구 요청을 보낼 수 없습니다');
    }

    // 비즈니스 로직 수행
    $pdo = pdo();
    [$a, $b] = friend_pair($me, $other);
    $now = now_epoch();
    $sql = "INSERT INTO friendships
              (user_id_a, user_id_b, status, requested_by, created_at, updated_at)
            VALUES
              (:a, :b, 'pending', :req, :created, :updated)
            ON DUPLICATE KEY UPDATE
              updated_at = VALUES(updated_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':a' => $a,
        ':b' => $b,
        ':req' => $me,
        ':created' => $now,
        ':updated' => $now
    ]);

    return ['message' => '친구 요청을 보냈습니다', 'success' => true];
}

/**
 * 친구 요청 수락 함수 (API 호출 가능)
 *
 * pending 상태의 친구 요청을 accepted로 변경합니다.
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 요청을 수락하는 사용자 ID
 *   - int $input['other'] 요청을 보낸 사용자 ID
 * @return array 성공 여부 및 메시지
 * @throws ApiException 에러 발생 시
 *
 * @example
 * // PHP에서 호출
 * $result = accept_friend(['me' => 10, 'other' => 5]);
 *
 * // JavaScript에서 API 호출
 * const result = await func('accept_friend', { me: 10, other: 5, auth: true });
 */
function accept_friend(array $input): array
{
    // 파라미터 추출 및 검증
    $me = (int)($input['me'] ?? 0);
    $other = (int)($input['other'] ?? 0);

    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    if ($other <= 0) {
        error('invalid-other', '유효하지 않은 대상 사용자 ID입니다');
    }

    // 비즈니스 로직 수행
    $pdo = pdo();
    [$a, $b] = friend_pair($me, $other);
    $sql = "UPDATE friendships
               SET status='accepted', updated_at=:now
             WHERE user_id_a=:a AND user_id_b=:b AND status='pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':a' => $a, ':b' => $b, ':now' => now_epoch()]);

    $success = $stmt->rowCount() > 0;

    if (!$success) {
        error('no-pending-request', '승인할 친구 요청이 없습니다');
    }

    return ['message' => '친구 요청을 수락했습니다', 'success' => true];
}

/**
 * 친구 관계 삭제 함수 (API 호출 가능)
 *
 * accepted 상태의 친구 관계를 데이터베이스에서 삭제합니다.
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 친구 관계를 삭제하는 사용자 ID
 *   - int $input['other'] 삭제할 친구의 사용자 ID
 * @return array 성공 여부 및 메시지
 * @throws ApiException 에러 발생 시
 *
 * @example
 * // PHP에서 호출
 * $result = remove_friend(['me' => 5, 'other' => 10]);
 *
 * // JavaScript에서 API 호출
 * const result = await func('remove_friend', { me: 5, other: 10, auth: true });
 */
function remove_friend(array $input): array
{
    // 파라미터 추출 및 검증
    $me = (int)($input['me'] ?? 0);
    $other = (int)($input['other'] ?? 0);

    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    if ($other <= 0) {
        error('invalid-other', '유효하지 않은 대상 사용자 ID입니다');
    }

    // 비즈니스 로직 수행
    $pdo = pdo();
    [$a, $b] = friend_pair($me, $other);
    $sql = "DELETE FROM friendships
            WHERE user_id_a=:a AND user_id_b=:b AND status='accepted'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':a' => $a, ':b' => $b]);

    $success = $stmt->rowCount() > 0;

    if (!$success) {
        error('no-friendship', '삭제할 친구 관계가 없습니다');
    }

    return ['message' => '친구 관계를 삭제했습니다', 'success' => true];
}

/**
 * 사용자의 친구 ID 목록 조회 함수 (API 호출 가능)
 *
 * accepted 상태의 친구 관계만 조회하여 친구 ID 배열을 반환합니다.
 * 무방향 1행 모델에서 CASE 문을 사용하여 친구 ID를 추출합니다.
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 친구 목록을 조회할 사용자 ID
 * @return array 친구 ID 배열 (직접 배열 형태로 반환, 친구가 없으면 빈 배열)
 * @throws ApiException 에러 발생 시
 *
 * @example
 * // PHP에서 호출
 * $friend_ids = get_friend_ids(['me' => 5]);
 * // [1, 3, 10, 15]
 *
 * // JavaScript에서 API 호출
 * const friend_ids = await func('get_friend_ids', { me: 5 });
 * console.log(friend_ids);  // [1, 3, 10, 15]
 */
function get_friend_ids(array $input): array
{
    // 파라미터 추출 및 검증
    $me = (int)($input['me'] ?? 0);

    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    // 비즈니스 로직 수행
    $pdo = pdo();
    $sql = "SELECT CASE WHEN user_id_a = :me1 THEN user_id_b ELSE user_id_a END AS friend_id
              FROM friendships
             WHERE (user_id_a = :me2 OR user_id_b = :me3) AND status='accepted'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':me1' => $me, ':me2' => $me, ':me3' => $me]);
    $friend_ids = array_map(fn($r) => (int)$r['friend_id'], $stmt->fetchAll());

    return $friend_ids;
}

/**
 * 내가 보낸 친구 요청 수 카운트 함수 (API 호출 가능)
 *
 * pending 상태의 친구 요청 중에서 내가 요청을 보낸 건수를 카운트합니다.
 * 무방향 1행 모델에서 requested_by 필드를 사용하여 내가 요청을 보낸 경우만 카운트합니다.
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 요청 수를 조회할 사용자 ID
 * @return int 보낸 친구 요청 수
 * @throws ApiException 에러 발생 시
 *
 * @example
 * // PHP에서 호출
 * $count = count_friend_requests_sent(['me' => 5]);
 * // 3 (내가 보낸 친구 요청이 3건)
 *
 * // JavaScript에서 API 호출
 * const count = await func('count_friend_requests_sent', { me: 5 });
 * console.log(count);  // 3
 */
function count_friend_requests_sent(array $input): int
{
    // 파라미터 추출 및 검증
    $me = (int)($input['me'] ?? 0);

    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    // 비즈니스 로직 수행
    // pending 상태이면서 내가 요청을 보낸 경우 (requested_by = me)
    $pdo = pdo();
    $sql = "SELECT COUNT(*) as count
              FROM friendships
             WHERE (user_id_a = :me1 OR user_id_b = :me2)
               AND status = 'pending'
               AND requested_by = :me3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':me1' => $me, ':me2' => $me, ':me3' => $me]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int)($result['count'] ?? 0);
}

/**
 * 내가 보낸 친구 요청 목록 조회 함수 (API 호출 가능)
 *
 * pending 상태의 친구 요청 중 내가 요청을 보낸 항목을 상세 정보와 함께 반환합니다.
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 요청 목록을 조회할 사용자 ID
 *   - int $input['limit'] 조회할 최대 건수 (기본값 20, 최대 100)
 *   - int $input['offset'] 시작 위치 (기본값 0)
 *   - string $input['order_by'] 정렬 기준 (updated_at, created_at, display_name)
 *   - string $input['order'] 정렬 순서 (ASC, DESC)
 * @return array 보낸 친구 요청 목록
 * @throws ApiException 에러 발생 시
 */
function get_friend_requests_sent(array $input): array
{
    $me = (int)($input['me'] ?? 0);
    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    $limit = (int)($input['limit'] ?? 20);
    if ($limit <= 0 || $limit > 100) {
        $limit = 20;
    }

    $offset = (int)($input['offset'] ?? 0);
    if ($offset < 0) {
        $offset = 0;
    }

    $orderBy = $input['order_by'] ?? 'updated_at';
    $order = strtoupper($input['order'] ?? 'DESC');

    $orderByWhitelist = [
        'updated_at' => 'f.updated_at',
        'created_at' => 'f.created_at',
        'display_name' => 'u.display_name',
    ];
    $orderBySql = $orderByWhitelist[$orderBy] ?? $orderByWhitelist['updated_at'];
    $orderSql = $order === 'ASC' ? 'ASC' : 'DESC';

    $pdo = pdo();
    $sql = "SELECT
                CASE WHEN f.user_id_a = :me_case THEN f.user_id_b ELSE f.user_id_a END AS receiver_id,
                u.display_name,
                u.photo_url,
                f.requested_by,
                f.created_at,
                f.updated_at
            FROM friendships f
            INNER JOIN users u ON u.id = CASE WHEN f.user_id_a = :me_join THEN f.user_id_b ELSE f.user_id_a END
            WHERE (f.user_id_a = :me_where_a OR f.user_id_b = :me_where_b)
              AND f.status = 'pending'
              AND f.requested_by = :me_requester
            ORDER BY {$orderBySql} {$orderSql}
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':me_case', $me, PDO::PARAM_INT);
    $stmt->bindValue(':me_join', $me, PDO::PARAM_INT);
    $stmt->bindValue(':me_where_a', $me, PDO::PARAM_INT);
    $stmt->bindValue(':me_where_b', $me, PDO::PARAM_INT);
    $stmt->bindValue(':me_requester', $me, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(static function (array $row): array {
        return [
            'user_id' => (int)$row['receiver_id'],
            'display_name' => $row['display_name'] ?? '',
            'photo_url' => $row['photo_url'] ?? '',
            'requested_by' => (int)$row['requested_by'],
            'created_at' => (int)$row['created_at'],
            'updated_at' => (int)$row['updated_at'],
        ];
    }, $rows);
}

/**
 * 내가 받은 친구 요청 수 카운트 함수 (API 호출 가능)
 *
 * pending 상태의 친구 요청 중에서 상대방이 나에게 요청을 보낸 건수를 카운트합니다.
 * 무방향 1행 모델에서 requested_by 필드를 사용하여 상대방이 요청을 보낸 경우만 카운트합니다.
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 요청 수를 조회할 사용자 ID
 * @return int 받은 친구 요청 수
 * @throws ApiException 에러 발생 시
 *
 * @example
 * // PHP에서 호출
 * $count = count_friend_requests_received(['me' => 5]);
 * // 2 (내가 받은 친구 요청이 2건)
 *
 * // JavaScript에서 API 호출
 * const count = await func('count_friend_requests_received', { me: 5 });
 * console.log(count);  // 2
 */
function count_friend_requests_received(array $input): int
{
    // 파라미터 추출 및 검증
    $me = (int)($input['me'] ?? 0);

    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    // 비즈니스 로직 수행
    // pending 상태이면서 상대방이 요청을 보낸 경우 (requested_by != me)
    $pdo = pdo();
    $sql = "SELECT COUNT(*) as count
              FROM friendships
             WHERE (user_id_a = :me1 OR user_id_b = :me2)
               AND status = 'pending'
               AND requested_by != :me3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':me1' => $me, ':me2' => $me, ':me3' => $me]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int)($result['count'] ?? 0);
}

/**
 * 받은 친구 요청 목록 조회 함수 (API 호출 가능)
 *
 * pending 상태의 친구 요청 중 상대방이 보낸 요청을 상세 정보와 함께 반환합니다.
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 요청 목록을 조회할 사용자 ID
 *   - int $input['limit'] 조회할 최대 건수 (기본값 20, 최대 100)
 *   - int $input['offset'] 시작 위치 (기본값 0)
 *   - string $input['order_by'] 정렬 기준 (updated_at, created_at, display_name)
 *   - string $input['order'] 정렬 순서 (ASC, DESC)
 * @return array 받은 친구 요청 목록
 * @throws ApiException 에러 발생 시
 */
function get_friend_requests_received(array $input): array
{
    $me = (int)($input['me'] ?? 0);
    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    $limit = (int)($input['limit'] ?? 20);
    if ($limit <= 0 || $limit > 100) {
        $limit = 20;
    }

    $offset = (int)($input['offset'] ?? 0);
    if ($offset < 0) {
        $offset = 0;
    }

    $orderBy = $input['order_by'] ?? 'updated_at';
    $order = strtoupper($input['order'] ?? 'DESC');

    $orderByWhitelist = [
        'updated_at' => 'f.updated_at',
        'created_at' => 'f.created_at',
        'display_name' => 'u.display_name',
    ];
    $orderBySql = $orderByWhitelist[$orderBy] ?? $orderByWhitelist['updated_at'];
    $orderSql = $order === 'ASC' ? 'ASC' : 'DESC';

    $pdo = pdo();
    $sql = "SELECT
                CASE WHEN f.user_id_a = :me_case THEN f.user_id_b ELSE f.user_id_a END AS requester_id,
                u.display_name,
                u.photo_url,
                f.requested_by,
                f.created_at,
                f.updated_at
            FROM friendships f
            INNER JOIN users u ON u.id = CASE WHEN f.user_id_a = :me_join THEN f.user_id_b ELSE f.user_id_a END
            WHERE (f.user_id_a = :me_where_a OR f.user_id_b = :me_where_b)
              AND f.status = 'pending'
              AND f.requested_by != :me_requester
            ORDER BY {$orderBySql} {$orderSql}
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':me_case', $me, PDO::PARAM_INT);
    $stmt->bindValue(':me_join', $me, PDO::PARAM_INT);
    $stmt->bindValue(':me_where_a', $me, PDO::PARAM_INT);
    $stmt->bindValue(':me_where_b', $me, PDO::PARAM_INT);
    $stmt->bindValue(':me_requester', $me, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(static function (array $row): array {
        return [
            'user_id' => (int)$row['requester_id'],
            'display_name' => $row['display_name'] ?? '',
            'photo_url' => $row['photo_url'] ?? '',
            'requested_by' => (int)$row['requested_by'],
            'created_at' => (int)$row['created_at'],
            'updated_at' => (int)$row['updated_at'],
        ];
    }, $rows);
}

/**
 * 사용자의 친구 상세 정보 조회 함수 (API 호출 가능)
 *
 * accepted 상태의 친구 관계를 조회하여 친구들의 상세 정보(users 테이블)를 반환합니다.
 * 최대 개수 제한, 정렬 순서 등을 옵션으로 지정할 수 있습니다.
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 친구 목록을 조회할 사용자 ID
 *   - int $input['limit'] 조회할 최대 친구 수 (기본값: 10, 최대값: 100)
 *   - int $input['offset'] 시작 위치 (기본값: 0, 페이지네이션용)
 *   - string $input['order_by'] 정렬 기준 (기본값: 'id', 옵션: 'id', 'display_name', 'created_at')
 *   - string $input['order'] 정렬 순서 (기본값: 'DESC', 옵션: 'ASC', 'DESC')
 * @return array 친구 정보 배열 (직접 배열 형태로 반환, 친구가 없으면 빈 배열)
 * @throws ApiException 에러 발생 시
 *
 * @example
 * // PHP에서 호출 - 기본 (최대 10명, ID 내림차순)
 * $friends = get_friends(['me' => 5]);
 * // [['id' => 10, 'display_name' => '홍길동', ...], ...]
 *
 * // 최대 5명만 조회
 * $friends = get_friends(['me' => 5, 'limit' => 5]);
 *
 * // 이름 오름차순 정렬
 * $friends = get_friends(['me' => 5, 'order_by' => 'display_name', 'order' => 'ASC']);
 *
 * // 페이지네이션 (6번째부터 5명)
 * $friends = get_friends(['me' => 5, 'limit' => 5, 'offset' => 5]);
 *
 * // JavaScript에서 API 호출
 * const friends = await func('get_friends', { me: 5, limit: 5 });
 * console.log(friends); // 친구 배열
 */
function get_friends(array $input): array
{
    // 파라미터 추출 및 검증
    $me = (int)($input['me'] ?? 0);
    $limit = (int)($input['limit'] ?? 10);
    $offset = (int)($input['offset'] ?? 0);
    $order_by = $input['order_by'] ?? 'id';
    $order = strtoupper($input['order'] ?? 'DESC');

    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    // limit 범위 제한 (1-100)
    if ($limit <= 0 || $limit > 100) {
        $limit = 10;
    }

    // offset 음수 방지
    if ($offset < 0) {
        $offset = 0;
    }

    // order_by 화이트리스트 검증 (SQL 인젝션 방지)
    $allowed_order_by = ['id', 'display_name', 'created_at'];
    if (!in_array($order_by, $allowed_order_by, true)) {
        $order_by = 'id';
    }

    // order 검증
    if (!in_array($order, ['ASC', 'DESC'], true)) {
        $order = 'DESC';
    }

    // 1단계: 친구 ID 목록 가져오기
    $friend_ids = get_friend_ids(['me' => $me]);

    // 친구가 없으면 빈 배열 반환
    if (empty($friend_ids)) {
        return [];
    }

    // 2단계: 친구 상세 정보 조회 (users 테이블)
    $pdo = pdo();
    $placeholders = implode(',', array_fill(0, count($friend_ids), '?'));

    // 동적 ORDER BY 절 (화이트리스트 검증 완료)
    $sql = "SELECT * FROM users
            WHERE id IN ($placeholders)
            ORDER BY {$order_by} {$order}
            LIMIT ? OFFSET ?";

    $stmt = $pdo->prepare($sql);

    // 바인딩: friend_ids + limit + offset
    $bind_values = [...$friend_ids, $limit, $offset];
    $stmt->execute($bind_values);

    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $friends;
}

// ============================================================================
// BlockRepository 함수들 (차단 관리)
// ============================================================================

/**
 * 양방향 차단 확인 함수 (내부 전용)
 *
 * 두 사용자 중 어느 한쪽이라도 상대방을 차단했는지 확인합니다.
 * (A가 B를 차단했거나, B가 A를 차단한 경우 모두 true 반환)
 * (내부 전용 - API로 호출되지 않음)
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
             WHERE (blocker_id=:ab1 AND blocked_id=:ab2)
                OR (blocker_id=:ba1 AND blocked_id=:ba2)
             LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ab1' => $a,
        ':ab2' => $b,
        ':ba1' => $b,
        ':ba2' => $a,
    ]);
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
 * (내부 전용 - API로 호출되지 않음)
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
// FeedRepository 함수들 (피드 캐시 관리) - 일부 API 호출 가능
// ============================================================================

/**
 * Fan-out on Write: 작성자의 친구들에게 피드 캐시 일괄 삽입 함수
 *
 * 게시글 작성 시 작성자의 모든 친구에게 feed_entries를 미리 생성합니다.
 * INSERT IGNORE를 사용하여 중복 삽입을 방지합니다.
 * (내부 전용 - create_post()에서 자동 호출)
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
                            CASE WHEN user_id_a = :author_case THEN user_id_b ELSE user_id_a END AS receiver_id,
                            :post_id,
                            :author_value,
                            :created_at
                            FROM friendships
                         WHERE (user_id_a = :author_where_a OR user_id_b = :author_where_b)
                             AND status='accepted'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':author_case' => $author_id,
        ':post_id' => $post_id,
        ':author_value' => $author_id,
        ':created_at' => $created_at,
        ':author_where_a' => $author_id,
        ':author_where_b' => $author_id,
    ]);
    return $stmt->rowCount(); // 실제 삽입된 행 수(IGNORE 영향 있음)
}

/**
 * 피드 캐시에서 사용자의 피드 조회 함수
 *
 * feed_entries 테이블에서 미리 생성된 피드 캐시를 조회합니다.
 * 친구 목록 조인 없이 단일 테이블 스캔으로 고속 조회가 가능합니다.
 * (내부 전용 - get_hybrid_feed()에서 사용)
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
 * (내부 전용 - get_hybrid_feed()에서 사용)
 *
 * @param int $me 피드를 조회할 사용자 ID
 * @param array $friend_ids 친구 ID 배열
 * @param int $limit 조회할 최대 개수
 * @param int $offset 시작 위치
 * @param bool $exclude_blocked 차단 사용자 제외 여부 (기본값: true)
 * @return array 피드 항목 배열 (post_id, post_author_id, created_at)
 *
 * @example
 * $friend_ids = get_friend_ids(['me' => 10])['friend_ids'];
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
// FeedService 함수들 (피드 비즈니스 로직) - API 호출 가능
// ============================================================================
// 주의: create_post_and_fanout() 함수는 삭제되었습니다.
// 이제 lib/post/post.crud.php의 create_post() 함수에서 자동으로 피드 전파를 수행합니다.

/**
 * 하이브리드 피드 조회 함수 (API 호출 가능)
 *
 * 캐시(feed_entries)와 읽기 조인(posts)을 결합하여 사용자의 피드를 조회합니다.
 * 1. 먼저 캐시에서 조회
 * 2. 캐시가 부족하면 posts 테이블에서 직접 조회
 * 3. 두 결과를 병합하고 중복 제거
 * 4. visibility 및 차단 관계를 최종 검증
 *
 * @param array $input 입력 파라미터
 *   - int $input['me'] 피드를 조회할 사용자 ID
 *   - int $input['limit'] 조회할 최대 개수 (기본값: 20)
 *   - int $input['offset'] 시작 위치 (기본값: 0)
 * @return array 피드 항목 배열 (직접 배열 형태로 반환)
 *   - 각 항목: ['post_id', 'author_id', 'category', 'title', 'content', 'created_at', 'visibility']
 * @throws ApiException 에러 발생 시
 *
 * @example
 * // PHP에서 호출
 * $feed = get_hybrid_feed(['me' => 10, 'limit' => 20, 'offset' => 0]);
 * // [['post_id' => 1, 'title' => '...'], ['post_id' => 2, 'title' => '...'], ...]
 *
 * // JavaScript에서 API 호출
 * const feed = await func('get_hybrid_feed', { me: 10, limit: 20, offset: 0 });
 * console.log(feed);  // 피드 배열
 */
function get_hybrid_feed(array $input): array
{
    // 파라미터 추출 및 검증
    $me = (int)($input['me'] ?? 0);
    $limit = (int)($input['limit'] ?? 20);
    $offset = (int)($input['offset'] ?? 0);

    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    if ($limit <= 0 || $limit > 100) {
        $limit = 20; // 기본값 및 최대값 제한
    }

    if ($offset < 0) {
        $offset = 0;
    }

    // 1단계: 캐시에서 조회
    $cached = get_feed_from_cache($me, $limit, $offset);
    if (count($cached) >= $limit) {
        return finalize_feed_with_visibility($me, $cached);
    }

    // 2단계: 부족분은 읽기 조인으로 보충
    $friend_ids = get_friend_ids(['me' => $me]);
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
 * (내부 전용 - get_hybrid_feed()에서 사용)
 *
 * @param int $me 피드를 조회하는 사용자 ID
 * @param array $items 피드 항목 배열 (post_id, post_author_id, created_at)
 * @return array 최종 검증된 피드 항목 배열
 */
function finalize_feed_with_visibility(int $me, array $items): array
{
    $out = [];
    // 친구 목록 캐시(루프 내 DB 호출 줄이기)
    $friend_ids = get_friend_ids(['me' => $me]);

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
