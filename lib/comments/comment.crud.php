<?php


/**
 * Count comments for a specific post
 *
 * @param array $input Input parameters
 *   - post_id (int, required): Post ID
 * @return int Number of comments for the post
 */
function count_comments(?array $input = []): int
{
    $db = pdo();
    $post_id = $input['post_id'] ?? 0;
    $stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
    $stmt->execute([$post_id]);
    return (int)$stmt->fetchColumn();
}

/**
 * Get a single comment with author information
 *
 * @param array $input Input parameters
 *   - comment_id (int, required): Comment ID
 * @return CommentModel|null Comment data with author information, or null if not found
 */
function get_comment(array $input): CommentModel|null
{
    $pdo = pdo();
    $comment_id = isset($input['comment_id']) ? (int)$input['comment_id'] : 0;

    // JOIN comments with users table to get author information
    $sql = "
        SELECT
            c.id,
            c.post_id,
            c.user_id,
            c.parent_id,
            c.content,
            c.files,
            c.comment_count,
            c.depth,
            c.sort,
            c.created_at,
            c.updated_at,
            u.first_name as first_name,
            u.photo_url as photo_url,
            u.firebase_uid as firebase_uid
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$comment_id]);
    $row = $stmt->fetch();

    if ($row) {
        return new CommentModel($row);
    } else {
        return null;
    }
}


/**
 * Get comments for a specific post with author information
 *
 * @param array $input Input parameters
 *   - post_id (int, required): Post ID
 *   - limit (int, optional): Number of comments to fetch (default: 5)
 *   - offset (int, optional): Offset for pagination (default: 0)
 *   - last (int, optional): Number of most recent comments to fetch (overrides limit/offset)
 * @return CommentModel[] Array of comment data with author information
 */
function get_comments(array $input): array
{

    $pdo = pdo();
    $post_id = isset($input['post_id']) ? (int)$input['post_id'] : 0;
    $limit = isset($input['limit']) ? (int)$input['limit'] : 5;
    $offset = isset($input['offset']) ? (int)$input['offset'] : 0;
    $last = isset($input['last']) ? (int)$input['last'] : 0;

    // last 파라미터가 있으면 마지막 N개의 댓글을 가져옴 (서브쿼리 사용)
    if ($last > 0) {
        $sql = "
            SELECT * FROM (
                SELECT
                    c.id,
                    c.post_id,
                    c.user_id,
                    c.parent_id,
                    c.content,
                    c.files,
                    c.comment_count,
                    c.depth,
                    c.sort,
                    c.created_at,
                    c.updated_at,
                    u.first_name as first_name,
                    u.photo_url as photo_url,
                    u.firebase_uid as firebase_uid
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
                ORDER BY c.sort DESC
                LIMIT ?
            ) AS subquery
            ORDER BY sort ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id, $last]);
        $rows = $stmt->fetchAll();
    } else {
        // 일반적인 offset/limit 페이지네이션
        $sql = "
            SELECT
                c.id,
                c.post_id,
                c.user_id,
                c.parent_id,
                c.content,
                c.files,
                c.comment_count,
                c.depth,
                c.sort,
                c.created_at,
                c.updated_at,
                u.first_name as first_name,
                u.photo_url as photo_url,
                u.firebase_uid as firebase_uid
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.sort ASC
            LIMIT ? OFFSET ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id, $limit, $offset]);
        $rows = $stmt->fetchAll();
    }


    return array_map(fn($row) => new CommentModel($row), $rows);
}

/**
 * 부모 댓글의 comment_count 업데이트
 *
 * 특정 댓글의 직접 자식(1단계 하위) 댓글 수를 계산하여
 * comments.comment_count 필드에 저장합니다.
 *
 * @param int $comment_id 업데이트할 댓글 ID
 * @return void
 *
 * @example
 * // 댓글 ID 123의 자식 댓글 수 업데이트
 * update_comment_child_count(123);
 */
function update_comment_child_count(int $comment_id): void
{
    $pdo = pdo();

    // 직접 자식 댓글 수 계산 (parent_id = $comment_id)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE parent_id = ?");
    $stmt->execute([$comment_id]);
    $child_count = (int)$stmt->fetchColumn();

    // comment_count 필드 업데이트
    $stmt = $pdo->prepare("UPDATE comments SET comment_count = ? WHERE id = ?");
    $stmt->execute([$child_count, $comment_id]);
}


/**
 * Create a new comment
 *
 * @param array $input Input parameters
 *   - post_id (int, required): Post ID
 *   - content (string, required): Comment content
 *   - parent_id (int, optional): Parent comment ID (default: 0 for root comment)
 *   - files (array, optional): Attached files
 * @return CommentModel Created comment data with author information
 * @throws Exception If user is not authenticated or input is invalid
 */
function create_comment(array $input): CommentModel
{
    $pdo = pdo();

    $user = login();
    if (!$user) {
        throw new Exception("Authentication required to create a comment.");
    }

    $post_id = isset($input['post_id']) ? (int)$input['post_id'] : 0;
    $parent_id = isset($input['parent_id']) ? (int)$input['parent_id'] : 0;
    $content = isset($input['content']) ? trim($input['content']) : '';
    $files = isset($input['files']) ? $input['files'] : '';
    $now = time(); // Unix timestamp (초 단위)

    if (empty($content)) {
        throw new Exception("Comment content cannot be empty.");
    }

    // 댓글 작성에 필요한 정보 가져오기
    $comment_info = get_comment_info($post_id, $parent_id);
    $depth = $comment_info['depth'];

    // get_comment_sort() 함수로 sort 값 계산
    $sort = get_comment_sort($comment_info['parent_sort'], $depth, $comment_info['no_of_comments']);

    $sql = "INSERT INTO comments (post_id, user_id, parent_id, content, files, depth, sort, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $user->id, $parent_id, $content, $files, $depth, $sort, $now, $now]);

    $comment_id = (int)$pdo->lastInsertId();

    // 게시글의 comment_count 업데이트
    update_post_comment_count($post_id);

    // 부모 댓글이 있으면 부모 댓글의 comment_count 업데이트
    if ($parent_id > 0) {
        update_comment_child_count($parent_id);
    }

    return get_comment(['comment_id' => $comment_id]);
}


/**
 * 댓글 수정
 *
 * 로그인한 사용자가 자신이 작성한 댓글의 내용과 첨부 파일을 수정합니다.
 *
 * @param array $input 입력 파라미터
 *   - comment_id (int, required): 수정할 댓글 ID
 *   - content (string, required): 수정할 댓글 내용
 *   - files (string, optional): 수정할 첨부 파일 (쉼표로 구분된 파일 경로)
 *
 * @return CommentModel 수정된 댓글 데이터 (작성자 정보 포함)
 *
 * @throws ApiException 다음 경우에 에러 발생:
 *   - 로그인하지 않은 경우 (authentication-required)
 *   - comment_id가 유효하지 않은 경우 (invalid-comment-id)
 *   - 댓글 내용이 비어있는 경우 (empty-comment-content)
 *   - 댓글을 찾을 수 없는 경우 (comment-not-found)
 *   - 작성자가 아닌 경우 (permission-denied)
 */
function update_comment(array $input): CommentModel
{
    // PDO 연결 획득
    $pdo = pdo();

    // ========================================================================
    // 1단계: 로그인 확인
    // ========================================================================
    $user = login();
    if (!$user) {
        error('authentication-required', '댓글을 수정하려면 로그인이 필요합니다.');
    }

    // ========================================================================
    // 2단계: 입력값 파싱 및 유효성 검사
    // ========================================================================
    $comment_id = isset($input['comment_id']) ? (int)$input['comment_id'] : 0;
    $content = isset($input['content']) ? trim($input['content']) : '';
    $files = isset($input['files']) ? $input['files'] : '';
    $now = time(); // 현재 시간 (Unix timestamp, 초 단위)

    // comment_id 유효성 검사
    if ($comment_id <= 0) {
        error('invalid-comment-id', '유효하지 않은 댓글 ID입니다.');
    }

    // 댓글 내용 유효성 검사
    if (empty($content)) {
        error('empty-comment-content', '댓글 내용을 입력해주세요.');
    }

    // ========================================================================
    // 3단계: 기존 댓글 존재 여부 확인
    // ========================================================================
    $existing_comment = get_comment(['comment_id' => $comment_id]);
    if (!$existing_comment) {
        error('comment-not-found', '댓글을 찾을 수 없습니다.', ['comment_id' => $comment_id], 404);
    }

    // ========================================================================
    // 4단계: 작성자 권한 확인
    // ========================================================================
    // 댓글 작성자만 수정할 수 있음
    if ($existing_comment->user_id !== $user->id) {
        error('permission-denied', '본인이 작성한 댓글만 수정할 수 있습니다.', [
            'comment_id' => $comment_id,
            'comment_author' => $existing_comment->user_id,
            'current_user' => $user->id
        ], 403);
    }

    // ========================================================================
    // 5단계: 자식 댓글 존재 여부 확인 (자식 댓글이 있으면 수정 불가)
    // ========================================================================
    $sql_check_children = "SELECT COUNT(*) FROM comments WHERE parent_id = ?";
    $stmt_check = $pdo->prepare($sql_check_children);
    $stmt_check->execute([$comment_id]);
    $child_count = (int)$stmt_check->fetchColumn();

    if ($child_count > 0) {
        error('comment-has-children', '하위 댓글이 있는 경우 현재 댓글 수정을 할 수 없습니다.', [
            'comment_id' => $comment_id,
            'child_count' => $child_count
        ], 400);
    }

    // ========================================================================
    // 6단계: 댓글 수정 (PDO Prepared Statement 사용)
    // ========================================================================
    $sql = "UPDATE comments SET content = ?, files = ?, updated_at = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$content, $files, $now, $comment_id]);

    // ========================================================================
    // 6단계: 수정된 댓글 데이터 반환
    // ========================================================================
    return get_comment(['comment_id' => $comment_id]);
}
/**
 * 댓글 삭제
 *
 * 로그인한 사용자가 자신이 작성한 댓글을 삭제합니다.
 * 댓글 삭제 후 해당 게시글의 댓글 수(comment_count)를 자동으로 업데이트합니다.
 *
 * @param array $input 입력 파라미터
 *   - comment_id (int, required): 삭제할 댓글 ID
 *
 * @return bool 삭제 성공 시 true 반환
 *
 * @throws ApiException 다음 경우에 에러 발생:
 *   - 로그인하지 않은 경우 (authentication-required)
 *   - comment_id가 유효하지 않은 경우 (invalid-comment-id)
 *   - 댓글을 찾을 수 없는 경우 (comment-not-found)
 *   - 작성자가 아닌 경우 (permission-denied)
 */
function delete_comment(array $input): bool
{
    // PDO 연결 획득
    $pdo = pdo();

    // ========================================================================
    // 1단계: 로그인 확인
    // ========================================================================
    $user = login();
    if (!$user) {
        error('authentication-required', '댓글을 삭제하려면 로그인이 필요합니다.');
    }

    // ========================================================================
    // 2단계: 입력값 파싱 및 유효성 검사
    // ========================================================================
    $comment_id = isset($input['comment_id']) ? (int)$input['comment_id'] : 0;

    // comment_id 유효성 검사
    if ($comment_id <= 0) {
        error('invalid-comment-id', '유효하지 않은 댓글 ID입니다.');
    }

    // ========================================================================
    // 3단계: 기존 댓글 존재 여부 확인
    // ========================================================================
    // 댓글 삭제 전에 post_id를 미리 저장해두어야 함
    // (삭제 후에는 댓글 데이터를 가져올 수 없으므로)
    $existing_comment = get_comment(['comment_id' => $comment_id]);
    if (!$existing_comment) {
        error('comment-not-found', '댓글을 찾을 수 없습니다.', ['comment_id' => $comment_id], 404);
    }

    // ========================================================================
    // 4단계: 작성자 권한 확인
    // ========================================================================
    // 댓글 작성자만 삭제할 수 있음
    if ($existing_comment->user_id !== $user->id) {
        error('permission-denied', '본인이 작성한 댓글만 삭제할 수 있습니다.', [
            'comment_id' => $comment_id,
            'comment_author' => $existing_comment->user_id,
            'current_user' => $user->id
        ], 403);
    }

    // ========================================================================
    // 5단계: 자식 댓글 존재 여부 확인 (자식 댓글이 있으면 삭제 불가)
    // ========================================================================
    $sql_check_children = "SELECT COUNT(*) FROM comments WHERE parent_id = ?";
    $stmt_check = $pdo->prepare($sql_check_children);
    $stmt_check->execute([$comment_id]);
    $child_count = (int)$stmt_check->fetchColumn();

    if ($child_count > 0) {
        error('comment-has-children', '하위 댓글이 있는 경우 현재 댓글 삭제를 할 수 없습니다.', [
            'comment_id' => $comment_id,
            'child_count' => $child_count
        ], 400);
    }

    // ========================================================================
    // 6단계: 댓글 삭제 (PDO Prepared Statement 사용)
    // ========================================================================
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$comment_id]);

    // ========================================================================
    // 7단계: 게시글의 댓글 수 업데이트
    // ========================================================================
    // 댓글이 삭제되었으므로 해당 게시글의 comment_count를 다시 계산
    update_post_comment_count($existing_comment->post_id);

    // ========================================================================
    // 8단계: 부모 댓글의 comment_count 업데이트
    // ========================================================================
    // 부모 댓글이 있으면 부모 댓글의 comment_count를 다시 계산
    if ($existing_comment->parent_id > 0) {
        update_comment_child_count($existing_comment->parent_id);
    }

    return true;
}
