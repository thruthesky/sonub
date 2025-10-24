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

    return get_comment(['comment_id' => $comment_id]);
}
